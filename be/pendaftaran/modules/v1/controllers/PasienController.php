<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use app\modules\v1\models\Pasien;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\PenanggungJawab;
use app\modules\v1\models\KeluargaPasien;
use app\modules\v1\models\SyncPasien;
use app\modules\v1\models\SyncPasienView;
use app\modules\v1\models\PasienUbahData;
use app\modules\v1\models\SyPendaftaranView;
use app\modules\v1\models\SyPasienView;
use app\modules\v1\models\SyKeluargaPasienView;
use app\modules\v1\models\SyPenanggungJawabView;
use Doco\Services\Vendors\PendaftaranService;

class PasienController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Pasien';
    static protected $_url = 'on/sinkronisasi/sync';

    public $messageBroker = [
        'update' => [
            'services' => [
                'Odoo' => [
                    'Pasien' => [
                        'query_params' => ['id'],
                    ]
                ],
                'Mhg' => [
                    'UpdatePatient' => [
                        'result' => true,
                        'successProcess'=>true,
                        'state' => 'update',
                    ],
                ],
                'Roche' => [
                    'Patient' => [
                        'result' => true,
                        'successProcess'=>true,
                    ]
                ],
            ]
        ]
    ];

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "GET"];
        $verbs["create"] = ["POST", "GET"];
        $verbs["delete"] = ["DELETE"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['update']);
        unset($actions['create']);
        unset($actions['view']);
        unset($actions['delete']);
        $newActions = [
            'get-data-pasien-sync' => 'app\modules\v1\actions\Pasien\GetDataPasienSyncAction',
        ];
        $actions = array_merge($actions, $newActions);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $result = $this->getData()
                            ->limit($request->post('length',10))
                            ->offset($request->post('start',0));

            if ($indexing = $request->post('no_rekam_medik')) {
                $result->andFilterWhere(['ILIKE', 'no_rekam_medik', $indexing]);
            }

            if ($order = $request->post('orderby')) {
                $dir = (int) $request->post('dir');
                $result->orderby([$order => $dir]);
            }

            return [
                'data' => $result->asArray()->all(),
                'count' => $result->count()
            ];
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
    }

    public function actionUmur($param, $umur_only = false)
    {
        $diff = date_diff(date_create(date('Y-m-d', strtotime($param))), date_create(date('Y-m-d')));
        if (!$umur_only){
            return "umur ".$diff->y." tahun ".$diff->m." bulan ".$diff->d." hari";
        }else{
            return $diff->y;
        }
    }

    public function actionCreate($no_rekam_medik = null)
    {
        return Yii::$app->docoPlugin->execute('pasien_create');
    }

    public function actionUpdate($id)
    {
        return Yii::$app->docoPlugin->execute('update_pasien');
    }

    /*
        Trigger Menyimpan Data Sinkronisasi pasien di DB tujuan
    *   return: boolean
    */
    private function sinkronPasien($attribut_pasien)
    {
        try{
            $restSync = Yii::$app->docoRest->sinkronisasi;
            $request = $restSync->post('sync/pasien', [
                'json'=>$attribut_pasien
            ]);

            $response = json_decode($request->getBody(),true);
            return $response['response'];
        }catch(\Exception $e){
            return [false, $e->getMessage()];
        } catch(\Exception $e){
            return [false, $e->getMessage()];
        }
    }

    /*
        Trigger Menyimpan Data Temporari Sinkronisasi Kunjungan yang gagal
    */
    private function saveTempPasien($attribut_pasien, $pasien_id)
    {
        try{
            $temp_data = json_encode($attribut_pasien);
            $model = new SyncPasien;
            $model->pasien_id = $pasien_id;
            $model->additional_sync = $temp_data;
            $model->is_sync = false;

            if ($model->save()) {
                return true;
            }
            return false;
        }catch(\Exception $e){
            return false;
        }
    }

    public function actionView($id) 
    {
        $model = $kunjungan = $pj = $keluarga_pasien = [];
        try {
            $model = Pasien::find()
            ->where(['pasien_id'=>$id])
            ->one();

            if (!empty($model)) {
                $kunjungan = Pendaftaran::find()
                ->where(['pasien_id' => $id])
                ->orderBy(['tgl_pendaftaran' => SORT_DESC])
                ->one();

                if(!empty($kunjungan) && !is_null($kunjungan->penanggungjawab_id)) {
                    $pj = PenanggungJawab::find()
                    ->where(['penanggungjawab_id' => $kunjungan->penanggungjawab_id])
                    ->one();
                } 

                $keluarga_pasien = KeluargaPasien::find()
                ->where(['pasien_id' => $id])
                ->one();

                return [
                    'pasien' => $model,
                    'kunjungan' => $kunjungan,
                    'pj' => $pj,
                    'keluarga_pasien' => $keluarga_pasien
                ];
            }
            throw new \Exception("Data Tidak Di Temukan");
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
    }

    private function setPj($modelPasien)
    {
        $request = Yii::$app->request;
        $dataPost = $request->post();
        $pendaftaranId = isset($dataPost['pendaftaran_id_last']) ? $dataPost['pendaftaran_id_last'] : null;
        $pjId = isset($dataPost['pj_id']) ?$dataPost['pj_id'] : null;
        $isDelete = isset($dataPost['is_deleted_pj']) ? $dataPost['is_deleted_pj']: null;

        if($pendaftaranId && $pjId != '0' && $isDelete == '0') {
            /** Case Edit */
            $pjModel = self::getPenanggungJawab()
            ->where(['penanggungjawab_id' => $pjId])
            ->one();

            $pjModel->pengantar = $dataPost['pj_pengantar'];
            $pjModel->penanggungjawab_nama = $dataPost['pj_nama'];
            $pjModel->penanggungjawab_jeniskelamin = $dataPost['pj_jk'];
            $pjModel->jenisidentitas = $dataPost['pj_jenis_identitas'];
            $pjModel->no_identitas = $dataPost['pj_no_identitas'];
            $pjModel->hubungankeluarga = $dataPost['pj_hubungan'];
            $pjModel->penanggungjawab_tempatlahir = $dataPost['pj_tempat_lahir'];
            $pjModel->penanggungjawab_tgllahir = $dataPost['pj_tanggal_lahir'];
            $pjModel->penanggungjawab_alamat = $dataPost['pj_alamat'];
            $pjModel->penanggungjawab_notelp = $dataPost['pj_no_telepon'];
            if(!$pjModel->save()) {
                return false;
            }
        } else if($pendaftaranId && $pjId == '0') {
            /** Case Insert */
            $pjModel = new PenanggungJawab;
            $pjModel->pengantar = $dataPost['pj_pengantar'];
            $pjModel->penanggungjawab_nama = $dataPost['pj_nama'];
            $pjModel->penanggungjawab_jeniskelamin = $dataPost['pj_jk'];
            $pjModel->jenisidentitas = $dataPost['pj_jenis_identitas'];
            $pjModel->no_identitas = $dataPost['pj_no_identitas'];
            $pjModel->hubungankeluarga = $dataPost['pj_hubungan'];
            $pjModel->penanggungjawab_tempatlahir = $dataPost['pj_tempat_lahir'];
            $pjModel->penanggungjawab_tgllahir = $dataPost['pj_tanggal_lahir'];
            $pjModel->penanggungjawab_alamat = $dataPost['pj_alamat'];
            $pjModel->penanggungjawab_notelp = $dataPost['pj_no_telepon'];
            $pjModel->pasien_id = $modelPasien->pasien_id;
            if($pjModel->save()) {
                $kunjungan = self::getPendaftaran()
                ->where(['pendaftaran_id' =>$pendaftaranId, 
                        'pasien_id' => $modelPasien->pasien_id])
                ->one();

                if($kunjungan) {
                    $pjId = $kunjungan->penanggungjawab_id;
                    $kunjungan->penanggungjawab_id = $pjModel->penanggungjawab_id;
                    if(!$kunjungan->save()){
                        return false;
                    }
                    if($isDelete == '1') {
                        $pjOldModel = self::getPenanggungJawab()
                        ->where(['penanggungjawab_id' => $pjId])
                        ->one();
                        if(!$pjOldModel->delete()){
                            return false;
                        }
                    }
                } else {
                    throw new \Exception("Data Tidak Di Temukan");
                }
            } 
        } else if ($pendaftaranId && empty($pjId) && $isDelete == '1') {
            $kunjungan = self::getPendaftaran()
            ->where(['pendaftaran_id' =>$pendaftaranId, 
                    'pasien_id' => $modelPasien->pasien_id])
            ->one();

            if($kunjungan) {
                $pjId = $kunjungan->penanggungjawab_id;
                $kunjungan->penanggungjawab_id = null;
                if($kunjungan->save()){
                    if(!empty($pjId)) {
                        $pjModel = self::getPenanggungJawab()
                        ->where(['penanggungjawab_id' => $pjId])
                        ->one();
                        if(!$pjModel->delete()){
                            return false;
                        }
                    }
                }
            } else {
                throw new \Exception("Data Tidak Di Temukan");
            }
        }

        return true;
    }

    private function setKp($modelPasien)
    {
        $request = Yii::$app->request;
        $dataPost = $request->post();
        $keluargapasien_id = isset($dataPost['kp_id']) ?$dataPost['kp_id'] : null;
        $isDelete = isset($dataPost['is_deleted_kp']) ? $dataPost['is_deleted_kp']: null;

        if($isDelete == '0') {
            /** Case Edit */
            $kpModel = KeluargaPasien::find()
            ->where(['pasien_id' => $modelPasien->pasien_id])
            ->one();
            
            if (empty($kpModel)) {
                $kpModel = new KeluargaPasien;
            }

            $kpModel->keluarga_nama = $dataPost['keluarga_nama'];
            $kpModel->keluarga_jk = $dataPost['keluarga_jk'];
            $kpModel->keluarga_hubungan = $dataPost['keluarga_hubungan'];
            $kpModel->keluarga_no_telepon = $dataPost['keluarga_no_telepon'];
            $kpModel->keluarga_alamat = $dataPost['keluarga_alamat'];
            $kpModel->keluarga_namadepan = $dataPost['keluarga_namadepan'];
            $kpModel->keluarga_pekerjaan_id = $dataPost['keluarga_pekerjaan_id'];
            $kpModel->keluarga_propinsi_id = $dataPost['keluarga_propinsi_id'];
            $kpModel->keluarga_kabupaten_id = isset($dataPost['keluarga_kabupaten_id'])?$dataPost['keluarga_kabupaten_id']:null;
            $kpModel->keluarga_kecamatan_id = isset($dataPost['keluarga_kecamatan_id'])?$dataPost['keluarga_kecamatan_id']:null;
            $kpModel->keluarga_kelurahan_id = isset($dataPost['keluarga_kelurahan_id'])?$dataPost['keluarga_kelurahan_id']:null;
            $kpModel->keluarga_rt = $dataPost['keluarga_rt'];
            $kpModel->keluarga_rw = $dataPost['keluarga_rw'];
            $kpModel->pasien_id = $modelPasien->pasien_id;
            if(!$kpModel->save()) {
                return false;
            }
        }  else if (empty($keluargapasien_id) && $isDelete == '1') {
            $kpModel = KeluargaPasien::find()
                ->where(['pasien_id' => $modelPasien->pasien_id])
                ->one();

            if($kpModel) {
                if(!$kpModel->delete()){
                    return false;
                }
            }
        }

        return true;
    }

    private static function getPenanggungJawab()
    {
        return PenanggungJawab::find();
    }

    private static function getPendaftaran()
    {
        return Pendaftaran::find();
    }

    public function actionDelete($id)
    {
        try {
            $model = Pasien::find()
            ->where(['pasien_id'=>$id])
            ->one();

            if($model && $model->delete()) {
                $responseMessage = [
                    'message' => Yii::t('app', 'Data pasien berhasil di hapus'),
                ];
                return $responseMessage;
            }
            throw new \Exception("Data Tidak Di Temukan");
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
    }

    public function actionCekRetensi()
    {
        $no_rekam_medik = Yii::$app->request->get('no_rekam_medik');
        $restSerconn = Yii::$app->serconn->guzzle('blocking');
        $headers = Yii::$app->request->headers;
        $params['authorization'] = $headers['authorization'];
        $params['x-owner'] = $headers['x-owner'];
        $params['route'] = 'app/cek-retensi';
        $params['data'] = [
            'no_rekam_medik' => $no_rekam_medik
        ];
        
        try {
            $request = $restSerconn->post(self::$_url, [
                'body' => json_encode($params)
            ]);
            $response = json_decode($request->getBody(), true);
            $result = isset($response['Results'][0]['data']) ? $response['Results'][0]['data'] : null;

            return [
                'result' => $result
            ];
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
    }

    private function syncDataUpdate($id)
    {
        $result = $keluarga = $pasien = $penanggungJawab = $pdftrn = [];
        $pdftrn = SyPendaftaranView::find()
        ->where(['pasien_id' => $id])
        ->orderBy(['tgl_pendaftaran' => SORT_DESC])
        ->asArray()
        ->one();

        $pasien = SyPasienView::find()
        ->where(['pasien_id' => $id])
        ->asArray()
        ->one();

        if (isset($pasien['additional_pasien']) && !empty($pasien['additional_pasien'])) {
            $additionalPasien = json_decode($pasien['additional_pasien']);
            if (!empty($additionalPasien)) {
                foreach ($additionalPasien as $key => $value) {
                    if (isset($value->jenisidentitas) && $value->jenisidentitas == DocoConstants::CONS_ID_KTP) {
                        $ktp = $value->no_identitas_pasien;
                    }
                }
                if(isset($ktp)) {
                    $pasien['nik'] = $ktp;
                }
            }
        }

        if(!empty($pdftrn)) {
            $add = json_decode($pdftrn['additional_data'], true);

            // if(isset($add['keluargapasien']) && !empty($add['keluargapasien'])) {
                $keluarga = SyKeluargaPasienView::find()
                ->where(['pasien_id' => $pdftrn['pasien_id']])
                ->orderBy(['keluargapasien_id' => SORT_DESC])
                ->asArray()
                ->one();
            // }

            if(isset($add['penanggung_jawab']) && !empty($add['penanggung_jawab'])) {
                $penanggungJawab = SyPenanggungJawabView::find()
                ->where(['pasien_id' => $pdftrn['pasien_id']])
                ->orderBy(['penanggungjawab_id' => SORT_DESC])
                ->asArray()
                ->one();
            }

            if(isset($pdftrn['umur']) && !empty($pdftrn['umur'])) {
                $umurExp = explode(" ",$pdftrn['umur']);
                $pdftrn['umur_hari'] = $umurExp[4];
                $pdftrn['umur_bulan'] = $umurExp[2];
                $pdftrn['umur_tahun'] = $umurExp[0];
            }
        }

        $result = [
            'pendaftaran' => $pdftrn,
            'pasien' => $pasien,
            'keluarga' => $keluarga,
            'penanggungJawab' => $penanggungJawab,
        ];

        return $result;
    }
}