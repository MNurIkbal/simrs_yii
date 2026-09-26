<?php

/**
 * @Author: Fajar Supriadi
 * @Date:   2021-05-03 19:37
 */

namespace app\modules\v1\controllers;

use Yii;
use app\modules\v1\components\BpjsController;

use app\modules\v1\models\Pasien;
use app\modules\v1\models\AsalRujukan;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\SyncEditsantoyusupR;

use app\modules\v1\cache\Cache;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoJwtHttpBearerAuth;
use Doco\components\DocoAccessRule;
use Doco\components\DocoMessages;
use yii\base\DynamicModel;
use yii\helpers\ArrayHelper;

class PendaftaranReservasiController extends BpjsController
{
    public $modelClass = 'app\modules\v1\models\Pendaftaran';
    static protected $_url = 'on/sinkronisasi/sync';
    static protected $_restSerconn;
    static protected $_headers;
    const INS_RJ = 1;
    const INS_RD = 2;
    const INS_RI = 3;
    const CARABAYAR_UMUM = 43;
    const CARABAYAR_ASURANSI = 39;
    const CARABAYAR_INSTANSI = 44;
    const CARABAYAR_RK_KARYAWAN = 41;
    const CARABAYAR_PERSONIL = 45;

    public function init()
    {
        self::$_restSerconn = Yii::$app->serconn->guzzle();
        self::$_headers = Yii::$app->request->headers;
    }

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => DocoJwtHttpBearerAuth::className(),
            'except' => ['save-pendaftaran'],
        ];

        $behaviors['access'] = [
            'class' => DocoAccessRule::className(),
            'except' => ['save-pendaftaran'],
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionSavePendaftaran()
    {
        $request = Yii::$app->request;
        
        $restSerconn = Yii::$app->serconn->guzzle();
        $headers = Yii::$app->request->headers;
        $start = date('d-m-Y');
        $end = date('d-m-Y');
        $post['route'] = 'app/get-pendaftaran-reservasi/'.$start.'/'.$end;
        try {
            $restSerconn->post('on/sinkronisasi/pendaftaranreservasi', [
                'body' => json_encode($post),
                'timeout' => 1, // Response timeout
            ]);
            return [
                'message' => 'Proses Sync Pendaftaran Berhasil',
            ];
        } catch (\Exception $e) {
            return [
                'message' => 'Proses Sync Pendaftaran Berhasil',
            ];
        }
    }

    public function actionSyncPendaftaran()
    {
        $request = Yii::$app->request;
        $post = Yii::$app->request->post();
        $idLog = $this->setLog(false,$post);
        $dataPendaftaran = $pjPasien = $asuransi = $penanggungbiaya = $tarif = $rujukan = [];

        $no_pendaftaran = $post['REGISTRASI.NOREG'];
        $tgl_pendaftaran = $post['REGISTRASI.TGLREG'] . ' ' . $post['REGISTRASI.JAMREG'];
        $umur = $post['REGISTRASI.UMURTH'] . ' Tahun ' . $post['REGISTRASI.UMURBLN'] . ' Bulan ' . $post['REGISTRASI.UMURHARI'] . ' Hari';
        $statusPasien = DocoConstants::VAR_PAS_L; //Self Master - Pasien Lama
        $statusPeriksa = DocoConstants::VAR_SP_AP; //Self Master - Antrian Poli
        $keterangan_pendaftaran = $post['REGISTRASI.KET'];

        /** Reservasi Only RJ */
        // if (strpos($no_pendaftaran, 'RJ') !== false) {
        //     $instalasi_id = self::INS_RJ;
        //     $keadaan_masuk = '159'; //Self Master - Tidak Gawat - Tidak Darurat
        // } else if (strpos($no_pendaftaran, 'RD') !== false) {
        //     $instalasi_id = self::INS_RD;
        //     $keadaan_masuk = 160; //Self Master - Gawat - Darurat
        // } else {
        //     $instalasi_id = self::INS_RI;
        //     $keadaan_masuk = 159; //Self Master - Tidak Gawat - Tidak Darurat
        // }
        $instalasi_id = self::INS_RJ;
        $keadaan_masuk = '159'; //Self Master - Tidak Gawat - Tidak Darurat

        $no_rekam_medik = $post['REGISTRASI.NORM'];
        $pasien_data = Pasien::find()->select(['pasien_id', 'golonganumur_id'])
            ->where(['no_rekam_medik' => $no_rekam_medik])
            ->one();

        if(count($pasien_data) < 1) {
            return [
                'errors' => json_encode('Pasien tidak ditemukan'),
                'uid' => $idLog
            ];
        }

        $asalrujukan = AsalRujukan::find()->select(['asalrujukan_id'])
            ->where(['asalrujukan_kode' => $post['REGISTRASI.KDMSK']])
            ->one();
        $ruangan = Ruangan::find()->select(['ruangan_id'])
            ->where(['additional_data' => $post['REGISTRASI.KDBAGIAN']])
            ->andWhere(['instalasi_id' => $instalasi_id])
            ->one();
        $kelaspelayanan = KelasPelayanan::find()->select(['kelaspelayanan_id'])
            ->where(['additional_data' => $post['REGISTRASI.KDKELAS']])
            ->one();
        $pegawai = Pegawai::find()->select(['pegawai_id'])
            ->where(['additional_data' => $post['REG_RJ.KDDR']])
            ->one();
        $carabayar = CaraBayar::find()->select(['carabayar_id'])
            ->where(['additional_data' => $post['INFOPNG.KLPPNG']])
            ->one();
        $carabayar_id = $carabayar->carabayar_id;
        $penjamin = Penjamin::find()->select(['penjamin_id'])
            ->where(['penjamin_kode' => $post['INFOPNG.KODEPNG']])
            ->one();

        $payload = [
            'asalrujukan' => $asalrujukan->asalrujukan_id,
            'ruangan' => $ruangan->ruangan_id,
            'kelaspelayanan' => $kelaspelayanan->kelaspelayanan_id,
            'pegawai' => $pegawai->pegawai_id,
            'carabayar' => $carabayar->carabayar_id,
            'penjamin' =>$penjamin->penjamin_id,
        ];

        $validator = DynamicModel::validateData($payload, [
            [['asalrujukan', 'ruangan', 'kelaspelayanan', 'pegawai', 'carabayar', 'penjamin'], 'required']
        ]);

        if($validator->hasErrors()) {
            return [
                'errors' => json_encode($validator->errors),
                'uid' => $idLog
            ];
        }
        
        switch($carabayar_id) {
            case self::CARABAYAR_UMUM:
                $pjPasien = [
                    'penanggungjawab_nama' => $post['INFOPNG.NMUMUM'],
                    'penanggungjawab_alamat' => $post['INFOPNG.ALMTUMM'],
                    'pj_rt' => $post['INFOPNG.RTUMM'],
                    'pj_rw' => $post['INFOPNG.RWUMM'],
                    'penanggungjawab_notelp' => $post['INFOPNG.TLPUMM'],
                ];
                break;
            case self::CARABAYAR_ASURANSI:
                $asuransi = [
                    'penjamin_id' => $penjamin->penjamin_id,
                    'carabayar_id' => $carabayar_id,
                    'nokartuasuransi' => $post['NOASURANSI'],
                    'namapemilikasuransi' => $post['NAMAPOLIS'],
                    'masaberlakukartu' => date('Y-m-d', strtotime($post['TGLAKHIRASURANSI']))
                ];
                break;
            case self::CARABAYAR_INSTANSI:
                $penanggungbiaya = [
                    'carabayar_id' => $carabayar_id,
                    'noindukkaryawan' => $post['NIKPEG'],
                    'penanggungbiaya_nama' => $post['NMPEG'],
                ];
                break;
            case self::CARABAYAR_RK_KARYAWAN:
                $penanggungbiaya = [
                    'carabayar_id' => $carabayar_id,
                    'noindukkaryawan' => $post['INFOPNG.NIKKARYAWAN'],
                    'penanggungbiaya_nama' => $post['INFOPNG.NMKARYAWAN'],
                ];
                break;
            default:
                $penanggungbiaya = [
                    'carabayar_id' => $carabayar_id,
                    'noindukkaryawan' => $post['INFOPNG.NIKPERSONIL'],
                    'penanggungbiaya_nama' => $post['INFOPNG.NMPERSONIL'],
                ];
                break;
        }

        $dataAntrian = [
            'pasien_id' => $pasien_data->pasien_id,
            'ruangan_id' => $ruangan->ruangan_id,
            'carabayar_id' => $carabayar->carabayar_id,
            'pendaftaran_id' => null, // Di Set Di Trigger
            'tgl_antrian' => date('Y-m-d H:i:s', strtotime($tgl_pendaftaran)),
            'no_antrian' => null,
            'penjamin_id' => $penjamin->penjamin_id,
            'pegawai_id' => $pegawai->pegawai_id,
            'status_pasien' => $statusPasien,
            'jenisantrian_id' => DocoConstants::VAR_JA_P,
            'is_active' => false,
        ];

        $additionals = [
            'tarif' => $tarif,
            'rujukan' => $rujukan,
            'antrian' => $dataAntrian,
            'penanggung_jawab' => $pjPasien,
            'asuransi' => $asuransi,
            'penanggungbiaya' => $penanggungbiaya,
        ];

        $dataPendaftaran = [
            'no_pendaftaran' => $no_pendaftaran,
            'tgl_pendaftaran' => date('Y-m-d H:i:s', strtotime($tgl_pendaftaran)),
            'caramasuk_id' => $asalrujukan->asalrujukan_id,
            'penjamin_id' => $penjamin->penjamin_id,
            'pasien_id' => $pasien_data->pasien_id,
            'pegawai_id' => $pegawai->pegawai_id,
            'instalasi_id' => $instalasi_id,
            'jeniskasuspenyakit_id' => 23, //Self Master - Umum
            'kelaspelayanan_id' => $kelaspelayanan->kelaspelayanan_id,
            'carabayar_id' => $carabayar->carabayar_id,
            'golonganumur_id' => $pasien_data->golonganumur_id,
            'umur' => $umur,
            'rujukan_id' => null,
            'kunjungan' => DocoConstants::VAR_K_L, //Self Master - Kunjungan Lama
            'ruangan_id' => $ruangan->ruangan_id,
            'transportasi' => null,
            'keadaan_masuk' => $keadaan_masuk,
            'status_periksa' => $statusPeriksa,
            'status_pasien' => $statusPasien,
            'status_masuk' => DocoConstants::VAR_SM_NR, //Self Master - Non Rujukan
            'keterangan_pendaftaran' => $keterangan_pendaftaran,
            'is_karcis' => true,
            'additional_data' => json_encode($additionals),
            'is_aps' => false,
            'limit_tagihan' => 0,
            'uid' => $idLog
        ];

        return $dataPendaftaran;
    }

    /* 
    * Pendaftaran Reservasi dengan Sercon non-block
    * @return true
    */
    public function actionSave()
    {
        $post = Yii::$app->request->post();
        $no_pendaftaran = [];

        if($post) {
            try {                
                foreach($post as $key => $value) {
                    $pendaftaran = new Pendaftaran;
                    $pendaftaran->attributes = $value;
                    if ($pendaftaran->validate() && $pendaftaran->save(false)) {
                        $idPendaftaran = $pendaftaran->pendaftaran_id;
                        $dataUpdate = [
                            'id_update' => $value['uid'],
                            'pendaftaran_id' => $idPendaftaran,
                            'pasien_id' => $pendaftaran->pasien_id
                        ];
                    } else {
                        $dataUpdate = [
                            'id_update' => $value['uid'],
                            'errors' => isset($value['errors']) ? $value['errors'] : json_encode($pendaftaran->errors)
                        ];
                    }
                    $this->setLog(true, null , null , $dataUpdate);
                }
                
                return true;
            } catch (\yii\db\Exception $e) {
                Yii::$app->response->statusCode = 500;
                // $transaction->rollBack();
                Yii::error($e->getMessage());
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM,[
                    'text' => $e->getMessage(),
                ]);
            } catch (\Exception $e) {
                Yii::$app->response->statusCode = 500;
                // $transaction->rollBack();
                Yii::error($e->getMessage());
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM,[
                    'text' => $e->getMessage(),
                ]);
            }
        }
    }

    private function setLog($isUpdate = false, $additionalData = null, $uid = null, $dataUpdate = [])
    {
        if($isUpdate) {
            $modelSync = SyncEditsantoyusupR::find()
            ->where([
                'synceditsantoyusup_id' => $dataUpdate['id_update']
            ])
            ->one();
            if($modelSync) {
                $isError = ArrayHelper::getValue($dataUpdate, 'errors');
                if(is_null($isError)) {
                    $modelSync->pendaftaran_id = $dataUpdate['pendaftaran_id'];
                    $modelSync->pasien_id = $dataUpdate['pasien_id'];
                    $modelSync->is_sync = true;
                    $modelSync->additional_data = DocoMessages::KEY_SUC_SYSTEM;
                    $modelSync->last_modified_date = date("Y-m-d H:i:s");
                } else {
                    $modelSync->additional_data = $isError;
                }
                $modelSync->save(false);
            }
        } else {
            $modelSync = new SyncEditsantoyusupR;
            $modelSync->pendaftaran_id = null;
            $modelSync->pasien_id = null;
            $modelSync->additional_data = "on sync";
            $modelSync->created_date = date("Y-m-d H:i:s");
            $modelSync->count_sync = 1;
            $modelSync->is_sync = false;
            $modelSync->state = "sync reservasi";
            $modelSync->payload = json_encode($additionalData);
            $modelSync->uid = json_encode($uid);
            $modelSync->save(false);
    
            return $modelSync->synceditsantoyusup_id;
        }
    }
}