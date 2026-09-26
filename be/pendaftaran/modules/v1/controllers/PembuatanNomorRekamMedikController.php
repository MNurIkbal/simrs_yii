<?php

namespace app\modules\v1\controllers;

use Yii;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Kabupaten;
use app\modules\v1\models\Kecamatan;
use app\modules\v1\models\Kelurahan;
use app\modules\v1\models\PembuatanNomorRekamMedik;
use app\modules\v1\models\Pasien;
use app\modules\v1\models\Pekerjaan;
use app\modules\v1\models\Pendidikan;
use app\modules\v1\models\Propinsi;
use app\modules\v1\models\Suku;
use app\modules\v1\models\PasienV;
use app\modules\v1\models\KonfigSystem;
use Doco\components\DocoActiveController;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\PendaftaranOnline;

class PembuatanNomorRekamMedikController extends DocoActiveController
{
    public $messageBroker = [
        'save' => [
            'services' => [
                'Mhg' => [
                    'CreateMasterPatient' => [
                        'result' => true,
                        'successProcess'=>true,
                    ]
                ],
                'Satusehat' => [
                    'SyncPasienSatusehat' => [
                        'result' => true,
                        'successProcess' => true,
                        'state' => 'new-admission'
                    ]
                ],
            ]
        ],
        
    ];

    /**
     * @todo Variable yang mendefinisikan model class
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public $modelClass = 'app\modules\v1\payload\PembuatanNomorRekamMedik ';

    /**
     * @todo Fungsi custom verbs
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function verbs()
    {
        $verbs = parent::verbs();

        return $verbs;
    }

    /**
     * @todo Fungsi custom actions
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actions()
    {
        $actions = parent::actions();

        return $actions;
    }

    /**
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @todo Fungsi untuk mendapatkan data options
     * @return array
     */
    public function actionGetDataOptions()
    {
        $jenisIdentitas = Lookup::find()->where([
            'lookup_type' => 'jenis_identitas'
        ])->all();
        $namaDepan = Lookup::find()->where([
            'lookup_type' => 'nama_depan'
        ])->all();
        $jenisKelamin = Lookup::find()->where([
            'lookup_type' => 'jenis_kelamin'
        ])->all();
        $statusPerkawinan = Lookup::find()->where([
            'lookup_type' => 'status_perkawinan'
        ])->all();
        $golonganDarah = Lookup::find()->where([
            'lookup_type' => 'golongan_darah'
        ])->all();
        $propinsi = Propinsi::find()->where([
            'is_active' => true,
            'is_deleted' => false
        ])->all();
        $pendidikan = Pendidikan::find()->where([
            'is_active' => true,
            'is_deleted' => false
        ])->all();
        $pekerjaan = Pekerjaan::find()->where([
            'is_active' => true,
            'is_deleted' => false
        ])->all();
        $suku = Suku::find()->where([
            'is_active' => true,
            'is_deleted' => false
        ])->all();
        $wargaNegara = Lookup::find()->where([
            'lookup_type' => 'warga_negara'
        ])->all();
        $agama = Lookup::find()->where([
            'lookup_type' => 'agama'
        ])->all();

        return [
            'jenisIdentitas' => Arrayhelper::map($jenisIdentitas, 'lookup_id', 'lookup_name'),
            'namaDepan' => Arrayhelper::map($namaDepan, 'lookup_id', 'lookup_name'),
            'jenisKelamin' => Arrayhelper::map($jenisKelamin, 'lookup_id', 'lookup_name'),
            'statusPerkawinan' => Arrayhelper::map($statusPerkawinan, 'lookup_id', 'lookup_name'),
            'golonganDarah' => Arrayhelper::map($golonganDarah, 'lookup_id', 'lookup_name'),
            'propinsi' => Arrayhelper::map($propinsi, 'propinsi_id', 'propinsi_nama'),
            'pendidikan' => Arrayhelper::map($pendidikan, 'pendidikan_id', 'pendidikan_nama'),
            'pekerjaan' => Arrayhelper::map($pekerjaan, 'pekerjaan_id', 'pekerjaan_nama'),
            'suku' => Arrayhelper::map($suku, 'suku_id', 'suku_nama'),
            'wargaNegara' => Arrayhelper::map($wargaNegara, 'lookup_id', 'lookup_name'),
            'agama' => Arrayhelper::map($agama, 'lookup_id', 'lookup_name'),
        ];
    }

    /**
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @todo Fungsi untuk menyimpan transaksi pembuatan nomor rekam medik
     * @return array
     */
    public function actionSave()
    {
        try {
            $request = Yii::$app->request;
            $model = new PembuatanNomorRekamMedik;
            $model->scenario = 'pendaftaran-rajal';
            $identitas = array();
            $isJkn = false;

            if ($request->post()) {
                $post = $request->post();
                $scenario = ArrayHelper::getValue($post, 'scenario');
                if ($scenario) {
                    $model->scenario = $scenario;
                    unset($post['scenario']);
                }

                $model->attributes = $post;
                $model->tanggal_lahir = date('Y-m-d',strtotime($model->tanggal_lahir));
                $model->tgl_rekam_medik = date('Y-m-d');
                $model->is_aps = ($model->is_aps == 1) ? true : false;
                $golonganumurpasien = $model->getGolonganUmurPasien();
                $model->golonganumur_id = $golonganumurpasien->golonganumur_id;
                $model->jenisidentitas = null;
                $model->no_identitas_pasien = null;

                $postNoIdentitas = isset($post['no_identitas_pasien']) ? $post['no_identitas_pasien'] : null;

                if (!empty($postNoIdentitas)) {
                    if(is_array($postNoIdentitas))
                    {
                        $idIdentity = null;
                        foreach ($postNoIdentitas as $key => $value) {
                            $idIdentity = isset($post['jenisidentitas'][$key]) ? $post['jenisidentitas'][$key] : null;
                            if ($idIdentity == DocoConstants::IDENTITAS_KTP) {
                                $model->jenisidentitas = (string) DocoConstants::IDENTITAS_KTP;
                                $model->no_identitas_pasien = $value;
                            }

                            $identitas[] = [
                                'jenisidentitas' => $idIdentity,
                                'no_identitas_pasien' => $value
                            ];
                        }

                        if (empty($model->jenisidentitas) && empty($model->no_identitas_pasien)) {
                            $model->jenisidentitas = $idIdentity;
                            $model->no_identitas_pasien = !empty($value) ? $value : null;
                        }

                    } else {
                        if (isset($post['jenisidentitas'])) {
                            $model->jenisidentitas = (string) $post['jenisidentitas'];
                            $model->no_identitas_pasien = $postNoIdentitas;
                        }
                        $identitas[] = [
                            'jenisidentitas' => $post['jenisidentitas'],
                            'no_identitas_pasien' => $postNoIdentitas
                        ];
                    }
                    
                }

                if (!empty($identitas)) {
                    $model->additional_pasien = json_encode($identitas);
                }

                // Validation for mobile jkn
                if ($model->scenario == 'pasien-jkn') {
                    $konfigSystem = KonfigSystem::find()->select(['is_allow_create_pasien_jkn'])
                    ->asArray()->one();
                    $isJkn = true;
                    $nik = null;

                    if (!empty($post['nopeserta_bpjs']) && !empty($post['tanggal_lahir']) && !empty($post['nama_pasien'])){

                        $tmpArray = json_decode($model->additional_pasien,true);
                        $buildJson = null;
                        if (is_array($tmpArray) && isset($tmpArray[0])) {
                            $buildJson = json_encode($tmpArray[0]);
                        }

                        $cekPasien = Pasien::find()
                           ->andWhere(['and',
                               // ['tanggal_lahir' => $model->tanggal_lahir],
                               // ['jeniskelamin' => $model->jeniskelamin],
                               ['ilike', 'additional_pasien', $buildJson],
                           ])
                           ->orWhere(['and',
                               // ['tanggal_lahir' => $model->tanggal_lahir],
                               // ['jeniskelamin' => $model->jeniskelamin],
                               ['no_identitas_pasien'=> $model->no_identitas_pasien],
                               ['jenisidentitas'=> $model->jenisidentitas],
                           ])
                           ->asArray()->one();

                        if (!empty($cekPasien)) {
                            $cekPasienByNokartu = Pasien::find()
                               ->andWhere(['and',
                                   ['nopeserta_bpjs' => $post['nopeserta_bpjs']],
                               ])->asArray()->all();

                            if (!empty($cekPasienByNokartu)) {
                                foreach ($cekPasienByNokartu as $value) {
                                    $getNik = null;
                                    $getIdentityPatient = isset($value['additional_pasien']) 
                                            ? json_decode($value['additional_pasien'],true) : [];
                                    if (is_array($getIdentityPatient)) {
                                        foreach ($getIdentityPatient as $dataValue) {
                                            $idIdentity = isset($dataValue['jenisidentitas']) ? $dataValue['jenisidentitas'] : null;
                                            if ($idIdentity == DocoConstants::IDENTITAS_KTP) {
                                                $getNik = isset($dataValue['no_identitas_pasien']) ? $dataValue['no_identitas_pasien'] : null;
                                                break;
                                            }
                                        }
                                    }

                                    if ($cekPasien['pasien_id'] != $value['pasien_id']) {
                                        return [
                                            'message' => Yii::t('app', 'Data Berhasil di simpan'),
                                            'pasien_id' => isset($value['no_rekam_medik']) ? $value['no_rekam_medik'] : null,
                                            'pas_id' => isset($value['pasien_id']) ? $value['pasien_id'] : null
                                        ];
                                    }else{
                                        return [
                                            'norm' => isset($value['no_rekam_medik']) ? $value['no_rekam_medik'] : null,
                                            'pasien_id' => isset($value['no_rekam_medik']) ? $value['no_rekam_medik'] : null,
                                            'pas_id' => isset($value['pasien_id']) ? $value['pasien_id'] : null,
                                            'message' => 'Data Peserta Sudah Pernah Dientrikan',
                                            'is_new' => false
                                        ];
                                    }
                                }
                            } else {
                                $updatePasien = PembuatanNomorRekamMedik::find()->where(['pasien_id' => $cekPasien['pasien_id']])->one();
                                $updatePasien->scenario = 'update-pasien-jkn';
                                $updatePasien->nopeserta_bpjs = $post['nopeserta_bpjs'];
                                $updatePasien->save();
                            }


                            return [
                                'message' => Yii::t('app', 'Data Berhasil di simpan'),
                                'pasien_id' => isset($cekPasien['no_rekam_medik']) ? $cekPasien['no_rekam_medik'] : null,
                                'pas_id' => isset($cekPasien['pasien_id']) ? $cekPasien['pasien_id'] : null
                            ];
                        }

                        // $cekPasien = Pasien::find()->where([
                        //     'nopeserta_bpjs' => $post['nopeserta_bpjs'],
                        //     'tanggal_lahir' => $post['tanggal_lahir'],
                        //     'nama_pasien' => $post['nama_pasien']
                        // ])->asArray()->one();

                    }

                    if (!$konfigSystem['is_allow_create_pasien_jkn']) {
                        return [
                            'status' => 201,
                            'title' => 'Proses Gagal!',
                            'text' => 'Pasien tidak ditemukan, harap melakukan pendaftaran di rumah sakit'
                        ];
                    }

                    if (empty($post['no_identitas_pasien'])) {
                        return [
                            'status' => 201,
                            'title' => 'Proses Gagal!',
                            'text' => 'NIK Belum Diisi'
                        ];
                    } else {
                        $nik = $post['no_identitas_pasien'];
                    }

                    if (empty($post['nopeserta_bpjs'])) {
                        return [
                            'status' => 201,
                            'title' => 'Proses Gagal!',
                            'text' => 'Nomor Kartu Belum Diisi'
                        ];
                    }
                    if (empty($post['tanggal_lahir'])) {
                        return [
                            'status' => 201,
                            'title' => 'Proses Gagal!',
                            'text' => 'Tanggal Lahir Belum Diisi'
                        ];
                    }

                    $validateNik = $this->validateNumeric($post['no_identitas_pasien'], 16);
                    if ($validateNik == false) {
                        return [
                            'status' => 201,
                            'title' => 'Proses Gagal!',
                            'text' => 'Format NIK Tidak Sesuai'
                        ];
                    }
                    $validateKk = $this->validateNumeric($post['nopeserta_bpjs'], 13);
                    if ($validateKk == false) {
                        return [
                            'status' => 201,
                            'title' => 'Proses Gagal!',
                            'text' => 'Format Nomor Kartu Tidak Sesuai'
                        ];
                    }

                    if ($model->tanggal_lahir > date('Y-m-d')) {
                        return [
                            'status' => 201,
                            'title' => 'Proses Gagal!',
                            'text' => 'Format Tanggal Lahir Tidak Sesuai'
                        ];
                    }
                }

                if ($model->validate() && $model->save()) {
                    $pasien_id = $model->getPrimaryKey();
                    $mrPasien = $model->pasien_id;
                    $pasien = self::getPasien()
                    ->where(['pasien_id' => $mrPasien])
                    ->one();

                    
                    if($pasien) {
                        $mrPasien = $pasien->no_rekam_medik;

                        // Update data reservasi
                        // if ($isJkn) {
                        //     $this->updatePendaftaranol($pasien, $nik);
                        // }
                    }
                    $responseMessage = [
                        'message' => Yii::t('app', 'Data Berhasil di simpan'),
                        'pasien_id' => $mrPasien,
                        'pas_id' => $pasien_id,
                        'data_pasien' => [
                            'pasien' => [
                                'pasien_id' => $pasien_id
                            ]
                        ]
                    ];
                    Yii::error([
                        'responseMessage' => $responseMessage,
                        'post' => $post
                    ]);
                    return $responseMessage;
                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'PasienForm');
                    Yii::error($errors);
                    // Response message error for mobile jkn
                    if ($model->scenario == 'pasien-jkn') {
                        foreach ($model->errors as $key => $value) {
                            return [
                                'status' => 201,
                                'title' => 'Proses Gagal!',
                                'text' => $value[0]
                            ];
                        }
                    } else {
                        foreach ($model->errors as $key => $value) {
                            Yii::$app->response->statusCode = 422;
                            return [
                                'status' => 422,
                                'title' => 'Proses Gagal!',
                                'text' => $value[0],
                                'message' => $value[0],
                            ];
                        }
                    }
                }
            }
        } catch (\yii\db\Exception $e) {
            Yii::error($e->getMessage());

            return [
                'response' => [
                    'message' => 'Proses Pembuatan Nomor Rekam Medik Gagal.',
                    'status' => 500
                ],
            ];
        } catch (\Exception $e) {
            Yii::error($e->getMessage());
            Yii::error($e->getLine());

            return [
                'response' => [
                    'message' => 'Proses Pembuatan Nomor Rekam Medik Gagal.',
                    'status' => 500
                ],
            ];
        }
    }

    private static function getPasien()
    {
        return PasienV::find();
    }

    public function actionGetDataWilayah(){
        $request = Yii::$app->request;
        // Retrieving Post Value
        $kodeProvinsi = $request->post('kode_propinsi');
        $kodeKabupaten = $request->post('kode_kabupaten');
        $kodeKecamatan = $request->post('kode_kecamatan');
        $kodeKelurahan = $request->post('kode_kelurahan');

        $kodeProvinsiBpjs = $request->post('kode_propinsi_bpjs');
        $kodeKabupatenBpjs = $request->post('kode_kabupaten_bpjs');
        $kodeKecamatanBpjs = $request->post('kode_kecamatan_bpjs');
        $kodeKelurahanBpjs = $request->post('kode_kelurahan_bpjs');

        $namaProvinsiBpjs = $request->post('nama_propinsi_bpjs');
        $namaKabupatenBpjs = $request->post('nama_kabupaten_bpjs');
        $namaKecamatanBpjs = $request->post('nama_kecamatan_bpjs');
        $namaKelurahanBpjs = $request->post('nama_kelurahan_bpjs');

        // Trim and Lowering Case Name
        $namaProvinsiBpjs = strtolower($namaProvinsiBpjs);
        $namaProvinsiBpjs = trim($namaProvinsiBpjs);
        $namaKabupatenBpjs = trim($namaKabupatenBpjs);
        $namaKabupatenBpjs = strtolower($namaKabupatenBpjs);
        $namaKecamatanBpjs = trim($namaKecamatanBpjs);
        $namaKecamatanBpjs = strtolower($namaKecamatanBpjs);
        $namaKelurahanBpjs = trim($namaKelurahanBpjs);
        $namaKelurahanBpjs = strtolower($namaKelurahanBpjs);

        $propinsi = Propinsi::find()
        ->where([
            'is_active' => true,
            'is_deleted' => false,
        ])
        ->andWhere([
            'or', 
            ['=','LOWER(propinsi_nama)', $namaProvinsiBpjs],
            ['=','kode_propinsi', $kodeProvinsi],
            ['=','kode_propinsi_bpjs', $kodeProvinsiBpjs]
        ])
        ->asArray()
        ->one();

        $kabupaten = Kabupaten::find()->where([
            'is_active' => true,
            'is_deleted' => false,
        ])
        ->andWhere([
            'or', 
            ['=','LOWER(kabupaten_nama)', $namaKabupatenBpjs],
            ['=','kode_kabupaten', $kodeKabupaten],
            ['=','kode_kabupaten_bpjs', $kodeKabupatenBpjs]
        ])
        ->asArray()
        ->one();

        $kecamatan = Kecamatan::find()->where([
            'is_active' => true,
            'is_deleted' => false,
        ])
        ->andWhere([
            'or', 
            ['=','LOWER(kecamatan_nama)', $namaKecamatanBpjs],
            ['=','kode_kecamatan', $kodeKecamatan],
            ['=','kode_kecamatan_bpjs', $kodeKecamatanBpjs]
        ])
        ->asArray()
        ->one();

        $kelurahan = Kelurahan::find()->where([
            'is_active' => true,
            'is_deleted' => false,
        ])
        ->andWhere([
            'or', 
            ['=','LOWER(kelurahan_nama)', $namaKelurahanBpjs],
            ['=','kode_kelurahan', $kodeKelurahan],
            ['=','kode_kelurahan_bpjs', $kodeKelurahanBpjs]
        ])
        ->asArray()
        ->one();

        return [
            'propinsi' => $propinsi,
            'kabupaten' => $kabupaten,
            'kecamatan' => $kecamatan,
            'kelurahan' => $kelurahan,
        ];
    }

    private function validateNumeric($string, $length = null) {
        if (!is_numeric($string)) {
            return false;
        }

        if ($length) {
            if (strlen($string) > $length) {
                return false;
            }
        }

        return true;
    }

    private function updatePendaftaranol($pasien, $nik = null) {
        $pendaftaranol = PendaftaranOnline::find()->where([
            'no_bpjs' => $pasien->nopeserta_bpjs,
            'no_identitas_pasien' => $nik,
        ])
        ->orderBy(['pendaftaranol_id' => SORT_DESC])
        ->one();

        if (!empty($pendaftaranol)) {
            $additional_data = json_decode($pendaftaranol->additional_data, true);
            $additional_data['pasien_baru'] = true;

            $pendaftaranol->pasien_id = $pasien->pasien_id;
            $pendaftaranol->no_rekam_medik = $pasien->no_rekam_medik;
            $pendaftaranol->status_pasien = DocoConstants::VAR_PAS_L;
            $pendaftaranol->additional_data = json_encode($additional_data);

            $pendaftaranol->save(false);
        }
    }
}