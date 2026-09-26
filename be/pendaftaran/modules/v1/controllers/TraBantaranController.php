<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveEncryptController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoConstansId;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\PasienAdmisi;
use app\modules\v1\models\Pasien;
use app\modules\v1\models\RujukanBantaran;
use app\modules\v1\models\DokumenRujukanBantaran;
use yii\helpers\ArrayHelper;
use Doco\Services\ApiBPJSLZString;
use Doco\Notifications\PembantaranNotification;

class TraBantaranController extends DocoActiveEncryptController
{
    public $modelClass = 'app\modules\v1\models\Pendaftaran';
    protected $keyEncrypt;

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["view"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "GET", 'PUT'];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['update']);
        return $actions;
    }

    public function init() {
        $this->keyEncrypt = DocoConstansId::actionGetAdditional('key_enkripsi');
    }

    public function actionGeneratePayloadTest() {
        $postData = file_get_contents('php://input');

        if(!empty($postData)) {
            $payload = json_decode($postData, true);
        } else {
            $payload = [
                'pengajuan_id' => 38,
                'uptasal_id' => 3,
                'uptasal_nama' => 'Lapas Kelas I Sukamiskin',
                'no_rekam_medik' => 'MC0023492',
                'nama_pasien' => 'Budi Santoso',
                'no_identitas_pasien' => '3283081108110012',
                'tempat_lahir' => 'Jakarta',
                'tgl_lahir' => '1990-10-10',
                'no_telp' => '08111222333',
                'pasien_lama' => false,
                'jenis_kelamin' => 'L',
                'no_rujukanbantaran' => 'PB-20251117-0037',
                'tgl_kunjungan' => '2025-11-18',
                'keterangan_rujukan' => 'Lorem Ipsum',
                'instalasi_id' => 1,
                'link_dokumen_pendukung' => []
            ];
        }

        $encrypt = (new ApiBPJSLZString())->encryptWithCompress($this->keyEncrypt, $payload);

        return ['raw' => $payload, 'encrypt' => $encrypt];
    }
    
    public function actionGetDataPasien() {
        $request = Yii::$app->request;
        $nik = $request->get('nik');

        try {
            if(!empty($nik)) {
                $data = $this->getDataPasienByNik($nik);
            } else {
                $data = [];
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }

        return $data;
    }

    public function actionCreateRujukan() {
        $postData = file_get_contents('php://input');

        try {
            if(!empty($postData)) {
                $decryptData = (new ApiBPJSLZString())->decryptWithDecompress($this->keyEncrypt, $postData);

                if(!empty($decryptData)) {
                    $connection = Yii::$app->db;
                    $transaction = $connection->beginTransaction();

                    $link_dokumen_pendukung = !empty($decryptData['link_dokumen_pendukung']) ? $decryptData['link_dokumen_pendukung'] : [];
                    unset($decryptData['link_dokumen_pendukung']);

                    $nik = !empty($decryptData['no_identitas_pasien']) ? $decryptData['no_identitas_pasien'] : null;

                    $pasien = !empty($nik) ? $this->getDataPasienByNik($nik) : [];

                    if(!empty($decryptData['jenis_kelamin']) && strtoupper($decryptData['jenis_kelamin']) == 'P') {
                        $jenisKelamin = DocoConstants::VAR_PR;
                    } else {
                        $jenisKelamin =  DocoConstants::VAR_LK;
                    }
                    unset($decryptData['jenis_kelamin']);
                    
                    $noTelp = isset($decryptData['no_telepon']) ? $decryptData['no_telepon'] : '-';
                    unset($decryptData['no_telepon']);

                    $noTahanan = isset($decryptData['no_tahanan']) ? $decryptData['no_tahanan'] : '-';
                    unset($decryptData['no_tahanan']);

                    $keteranganRujukan = isset($decryptData['keterangan_rujukan']) ? $decryptData['keterangan_rujukan'] : '-';
                    unset($decryptData['keterangan_rujukan']);

                    $rujukanBantaran = new RujukanBantaran;
                    $rujukanBantaran->attributes = $decryptData;
                    $rujukanBantaran->jenis_identitas = DocoConstants::IDENTITAS_KTP;
                    $rujukanBantaran->no_rekam_medik = !empty($pasien) ? $pasien['no_rekam_medik'] : null;
                    $rujukanBantaran->pasien_id = !empty($pasien) ? $pasien['pasien_id'] : null;
                    $rujukanBantaran->jenis_kelamin = $jenisKelamin;
                    $rujukanBantaran->created_date = date('Y-m-d H:i:s');
                    $rujukanBantaran->is_active = true;
                    $rujukanBantaran->is_deleted = false;
                    $rujukanBantaran->pasien_lama = !empty($pasien) ? true : false;
                    $rujukanBantaran->no_telp = $noTelp;
                    $rujukanBantaran->no_telepon = $noTelp;
                    $rujukanBantaran->no_tahanan = $noTahanan;
                    $rujukanBantaran->keterangan_rujukan = $keteranganRujukan;
                    $rujukanBantaran->status_verifikasi_bantaran = DocoConstants::BELUM_VERIFIKASI_BANTARAN;

                    if($rujukanBantaran->validate()) {
                        $saveData = $rujukanBantaran->save();
                        if(!$saveData) {
                            \Yii::$app->response->statusCode = 500;
                            return [
                                'message' => 'Terjadi kesalahan saat menyimpan data rujukan'
                            ];
                        }

                        if(!empty($link_dokumen_pendukung)) {
                            for($i = 0; $i < count($link_dokumen_pendukung); $i++) {
                                $dokumenBantaran = new DokumenRujukanBantaran;
                                $dokumenBantaran->rujukanbantaran_id = $rujukanBantaran->rujukanbantaran_id;
                                $dokumenBantaran->nama_dokumen = $link_dokumen_pendukung[$i]['nama_dokumen'];
                                $dokumenBantaran->url_dokumen = $link_dokumen_pendukung[$i]['url_dokumen'];
                                $dokumenBantaran->created_date = date('Y-m-d H:i:s');
                                $dokumenBantaran->is_active = true;
                                $dokumenBantaran->is_deleted = false;

                                $saveDokumen = $dokumenBantaran->save();
                                
                                if(!$saveDokumen) {
                                    $transaction->rollBack();
                                    \Yii::$app->response->statusCode = 500;
                                    return [
                                        'message' => 'Terjadi kesalahan saat menyimpan dokumen'
                                    ];
                                }
                            }
                        }

                        $transaction->commit();
                        PembantaranNotification::newRujukan($rujukanBantaran, $link_dokumen_pendukung);
                    } else {
                        $transaction->rollBack();
                        \Yii::$app->response->statusCode = 422;
                        return [
                            'status' => 422,
                            'data' => $rujukanBantaran->errors
                        ];
                    }
                } else {
                    \Yii::$app->response->statusCode = 500;
                    return [
                        'message' => 'Unable to decrypt or parameter is unavailable'
                    ];
                }
            } else {
                return [];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }

        return [
            'message' => 'Data berhasil disimpan !'
        ];
    }

    protected function getDataPasienByNik($nik) {
        $kondisiIdentitas[] = [
            'and',
            ['jenisidentitas' => DocoConstants::IDENTITAS_KTP],
            ['no_identitas_pasien' => $nik]
        ];

        $arrayIdentitas =   ['jenisidentitas' => (string) DocoConstants::IDENTITAS_KTP, 'no_identitas_pasien' => $nik];
        $identitasJson = json_encode($arrayIdentitas);

        $kondisiIdentitas[] = ['ILIKE', 'additional_pasien', $identitasJson];

        $pasien = Pasien::find();
        $pasien->orWhere( array_merge(['or'], $kondisiIdentitas) );
        
        return $pasien->asArray()->one();
    }
}
