<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Kamar
 * @copyright 21 Mei 2018 aweutist
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
// model
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\KamarRuangan;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\JenisKasusPenyakit;
use app\modules\v1\models\KasusPenyakitRuangan;
use app\modules\v1\models\KlasifikasiKamarV;
use app\modules\v1\models\KlasifikasiKamar;
use app\modules\v1\models\MasterKamarRuanganView;
use yii\helpers\ArrayHelper;
use app\modules\v1\payload\PayloadForm;
use Doco\Repositories\LookUpTransaksiRepositories;
use Doco\models\bpjs\BpjsAplicare;
use app\modules\v1\models\KamarTempatTidur;
use Doco\components\DocoMessages;

class KamarController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\MasterKamarRuanganView';
    public $konfig_farmasi;

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        $verbs["list-display-antrian"] = ["POST", "GET"];
        $verbs["list-type-screen"] = ["POST", "GET"];
        $verbs["list-function-screen"] = ["POST", "GET"];
        $verbs["get-kamar-ranap"] = ["GET"];
        $verbs["get-kamar-ranap-dep"] = ["GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }
    /**
    *
    * @todo Fungsi get data ruangan
    * @return array, activeQueryRecords
    *
    */
    private function getRuangan()
    {
         $sql = "SELECT ruangan_id, ruangan_nama
            FROM ruangan_m 
            WHERE is_deleted = false AND is_active = true
            ORDER BY ruangan_nama ASC
        ";
        $result = Ruangan::findBySql($sql);

        return $result;
    }

    /**
    *
    * @todo Fungsi get data jenis kamar
    * @return array, activeQueryRecords
    *
    */
    private function getJenisKamar()
    {
        $sql = "SELECT lookup_id, lookup_name, lookup_name as jenis_kamar FROM lookup_m WHERE lookup_type = 'jenis_kamar' 
            AND is_deleted = false AND is_active = true
        ";
        $result = Lookup::findBySql($sql);

        return $result;
    }

    /**
    *
    * @todo Fungsi get kelas pelayanan by ruangan
    * @return array
    *
    */
    public function actionGetKelasPelayananAndJenisPenyakitRuangan()
    {
        $request = Yii::$app->request;
        $ruangan_id = $request->get('ruangan_id');
        $payload = new PayloadForm;
        $payload->ruangan_id = $ruangan_id;
        if (!$payload->validate()) {
            return [
                'status' => 422,
                'data' => $payload->errors
            ];
        }
        $getKelasPelayanan = $this->getKelasPelayanan($ruangan_id);
        $getJenisKasusPenyakitRuangan = $this->getJenisKasusPenyakitRuangan($ruangan_id);
        $result = ['kelas_pelayanan' => $getKelasPelayanan,
                    'jenis_penyakit' => $getJenisKasusPenyakitRuangan,

                   ];

        return $result;
    }

    public function getJenisKasusPenyakitRuangan($ruangan_id) {
        $data = KasusPenyakitRuangan::find();
        $data->select(['kasuspenyakitruangan_mp.ruangan_id',
                        'jeniskasuspenyakit_m.jeniskasuspenyakit_id',
                        'jeniskasuspenyakit_m.jeniskasuspenyakit_nama'
                        ]);
        if($ruangan_id){
            $data->where(['kasuspenyakitruangan_mp.ruangan_id' => $ruangan_id]);
        }
        $data->leftJoin('jeniskasuspenyakit_m', 'jeniskasuspenyakit_m.jeniskasuspenyakit_id = kasuspenyakitruangan_mp.jeniskasuspenyakit_id'); 
        $data->andWhere(['kasuspenyakitruangan_mp.is_active' => true,'kasuspenyakitruangan_mp.is_deleted'=>false])
            ->orderBy(['jeniskasuspenyakit_m.jeniskasuspenyakit_nama' => SORT_ASC]);

        $result = $data->asArray()->all();

        return $result;
    }

    public function getKelasPelayanan($ruangan_id)
    {
        if (empty($ruangan_id)) return [];
        $request = Yii::$app->request;
        $db = Yii::$app->db;
        $sql = "SELECT kr.kelaspelayanan_id, kr.ruangan_id, kp.kelaspelayanan_nama
            FROM kelasruangan_mp kr
            JOIN kelaspelayanan_m kp ON kp.kelaspelayanan_id = kr.kelaspelayanan_id
            WHERE kr.is_deleted = false AND kr.is_active = true AND kr.ruangan_id = {$ruangan_id}
            AND kp.is_deleted = false AND kp.is_active = true 
            ORDER By kelaspelayanan_nama ASC
        ";
        
        $result = $db->createCommand($sql)->queryAll();
        return $result;
    }

    /**
    *
    * @todo Fungsi get kelas pelayanan default
    * @return array
    *
    */
    public function getPelayanan($id='')
    {
        if ($id) {
            $sql = "SELECT a.kelaspelayanan_id, a.kelaspelayanan_nama
                FROM kelaspelayanan_m a
                inner join kelasruangan_mp b on b.kelaspelayanan_id = a.kelaspelayanan_id
                WHERE a.is_deleted = false AND a.is_active = true and b.ruangan_id = {$id} 
                ORDER BY a.kelaspelayanan_nama ASC ";

        }else{
            $sql = "SELECT kelaspelayanan_id, kelaspelayanan_nama
                FROM kelaspelayanan_m 
                WHERE is_deleted = false AND is_active = true
                ORDER BY kelaspelayanan_nama ASC ";
        }
        $result = Ruangan::findBySql($sql);

        return $result;
    }



    public function actionGenerateApi($id='')
    {
        try {
            $request = Yii::$app->request;

            // Get ruangan
            $modelRuangan = $this->getRuangan();
            $dataRuangan = $modelRuangan->asArray()->all();

            $modelJenisKamar = $this->getJenisKamar();
            $dataJenisKamar = $modelJenisKamar->asArray()->all();

            $modelJenisKasusPenyakit = $this->getKasusPenyakitRuangan();

            if ($id) {
                $modelPelayanan = $this->getPelayanan($id);
                $dataPelayanan = $modelPelayanan->asArray()->all();
            }else{
                $modelPelayanan = $this->getPelayanan();
                $dataPelayanan = $modelPelayanan->asArray()->all();
            }

            $modelKlasifikasi = KlasifikasiKamar::find()->select([
                'klasifikasikamar_id',
                'klasifikasikamar_nama'
            ])
            ->asArray()
            ->all();


            return [
                'data-ruangan' => $dataRuangan,
                'data-jenis-kamar' => $dataJenisKamar,
                'data-pelayanan' => $dataPelayanan,
                'data-kasus-penyakit' => $modelJenisKasusPenyakit,
                'data-klasifikasi' => $modelKlasifikasi,
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

    private function getKasusPenyakitRuangan(){
        $model = new JenisKasusPenyakit;
        $query = $model->find()
                ->where(['is_active' => true, 'is_deleted' => false])
                ->orderBy(['jeniskasuspenyakit_nama'=> SORT_ASC ]);
        $result = $query->asArray()->all();

        return $result;
    }

    public function actionIndex() {
        try {
            $request = Yii::$app->request;

            // Define model
            $model = new MasterKamarRuanganView;
            $query = $model::find();

            // Doco active filter
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            // Return data
            return new ActiveDataProvider([
                'query' => $query,
            ]);
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

    /**
    *
    * @todo Fungsi get kasus penyakit by ruangan
    * @return array
    *
    */
    public function actionGetKasusPenyakitRuangan()
    {
        $request = Yii::$app->request;
        $db = Yii::$app->db;
        $ruangan_id = $request->get('ruangan_id');
        // Query
        $sql = "SELECT kr.jeniskasuspenyakit_id, kr.ruangan_id, kp.jeniskasuspenyakit_nama
            FROM kasuspenyakitruangan_mp kr
            JOIN jeniskasuspenyakit_m kp ON kp.jeniskasuspenyakit_id = kr.jeniskasuspenyakit_id
            WHERE kr.is_deleted = false AND kr.is_active = true AND kr.ruangan_id = {$ruangan_id}
            AND kp.is_deleted = false AND kp.is_active = true 
        ";
        
        // Result
        $result = $db->createCommand($sql)->queryAll();

        // Return
        return $result;
    }

    /**
    *
    * @todo Fungsi autocomplete nama kamar
    * @return array
    *
    */

    public function dataKamar()
    {
        $data = MasterKamarRuanganView::find();
        return $data;
    }

    public function actionDataKamar()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = $this->dataKamar();
        $result->select(['kamarruangan_nokamar', 'kamarruangan_nokamar']);
        if (!empty($post['term'])) {
            $term = $post['term'];
            $result->where(['ILIKE','LOWER(kamarruangan_nokamar)', $term]);
        }
        $result->groupBy('kamarruangan_nokamar');
        $result->limit(50);

        return $result->asArray()->all();
    }

    public function actionSave()
    {
        try {
            $request = Yii::$app->request;
            $model = new KamarRuangan;
            $post = $request->post();            
            if(empty($post)){
                $result = [
                            'status' => 422,
                            'title' => 'Proses Gagal!',
                            'text' => 'Data Tidak Di Temukan'
                        ];                        
                return $result;
            }else{
                $model->attributes = $post;
                if (!$model->validate()) {
                    return [
                        'data' => $model->errors,
                        'status' => 422
                    ];
                }else{
                    if (!$model->save()){
                        return [
                                'data' =>  $model->errors,
                                'status' => 422
                            ];
                    } 
                    else{
                        $result = [
                                'status' => 200,
                                'title' => 'Proses Berhasil',
                                'text' => 'Data Berhasil Tersimpan'
                            ];                        
                        return $result;
                    }
                }
            }            
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
    
    public function actionDelete($id)
    {
        try {
            $klasfikasi = KlasifikasiKamarV::find()
            ->select([
                'kamarruangan_id'
            ])
            ->where(['kamarruangan_id' => $id, 'is_deleted' => false])->asArray()->one();
            if(! empty($klasfikasi)) {
                return [
                    'title' => 'Proses Gagal !',
                    'message' => 'Klasifikasi Sudah Dimappingkan dengan kamar',
                    'res_status' => 422
                ];
            }
            return (new KamarRuangan)->delete($id);
        } catch (\yii\db\Exception $e) {
            return $this->responseJson(400, DocoMessages::ERR_MESSAGE);
        } catch (\Exception $e) {
            return $this->responseJson(400, $e->getMessage());
        }
    }

    /**
    * @controller actionCetakPdf
    * @attribute #kamar# => table
    * @attribute #nama_rs# => nama rumah sakit
    * @attribute #tanggal# => Tanggal sekarang
    **/
    public function actionCetakPdf()
    {
        $request = Yii::$app->request;
        $nama_rs = '';
        $model = new MasterKamarRuanganView;
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->all();

        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['nama_rs'])) {
                $nama_rs = $_GET['advanced-filter']['nama_rs'];
            }
        }

        $print = new DocoPrint();
        $print->attributes = [
            '#kamar#' => $this->renderPartial('index', [
                'detail' => $data
            ]),
            '#nama_rs#' => $nama_rs,
            '#tanggal#' => date('d-m-Y'),
        ];
        $print->Output();
    }

    public function actionViewData($id)
    {
        $model = new MasterKamarRuanganView;
        $query = $model->find();
        if ($id) {
            $query->andWhere(['kamarruangan_id' => $id]);
        }

        $data = $query->asArray()->one();
        if(! empty($data)) {
            $klasfikasi = KlasifikasiKamarV::find()->where(['kamarruangan_id' => $id, 'is_deleted' => false])->one();
            $data['klasifikasi'] = ! empty($klasfikasi) ? true : false;
        }
        return $data;
    }

    public function actionUpdate($id)
    {
        try {
            $now = date('Y-m-d H:i:s');
            $request = Yii::$app->request;
            $model = KamarRuangan::findOne($id);
            $post = $request->post();
            if ($model && !empty($model)) {
                $model->attributes = $post;
                if ($model->validate() && $model->save()) {
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                     return [
                        'data' => $model->errors,
                        'status' => 422
                    ];
                }
            }
            throw new Exception("Data Tidak Di Temukan");
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

    protected $_title = 'MASTER KAMAR';
    public function actionExportExcel()
    {
        try {
            $data = array();
            $header = array();
            $footer = array();

            $model = new MasterKamarRuanganView;
            $query = $model::find();
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query = $query->all();

            $nama_rs = '';
            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['nama_rs'])) {
                    $nama_rs = $_GET['advanced-filter']['nama_rs'];
                }
            }

            if (!empty($query)) {
                foreach ($query as $index => $value) {
                    $data[$index]['Ruangan'] = $value->ruangan_nama;
                    $data[$index]['Kelas'] = $value->kelaspelayanan_nama;
                    $data[$index]['Jenis Kasus Penyakit'] = $value->jeniskasuspenyakit_nama;
                    $data[$index]['Nama Kamar'] = $value->kamarruangan_nokamar;
                    $data[$index]['Jenis Kamar'] = $value->jenis_kamar;
                    $data[$index]['Status'] = ($value->is_active) ? 'Aktif' : 'Tidak Aktif' ;
                }
            }

            $footer = [
                'title' => [
                    0 => '',
                    1 => '',
                ],
                'data' => [
                    'Kelas' => 'Tanggal Unduh : ' . date('d M Y'),
                ]
            ];

            $filePath = DocoHelpers::exportExcel($this->_title . " " . strtoupper($nama_rs), $data, $header, [],$footer,[],true);
            $filePath->save('php://output');
            die;
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

    public function actionDataNamaRuangan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        
        $term = strtoupper($post['term']);
        
        $sql = "select ruangan_id, ruangan_nama from ruangan_m where UPPER( ruangan_nama ) LIKE '%{$term}%' and is_deleted = false
            group by ruangan_id, ruangan_nama
            order by ruangan_id, ruangan_nama asc limit 50
        "; 
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }

    public function actionCekDataKamar($kamarruangan_nokamar)
    {
        $term = strtoupper($kamarruangan_nokamar);
        $sql = "select a.kamarruangan_id, b.status_isi
        from kamarruangan_m a
        inner join kamartempattidur_m b on a.kamarruangan_id=b.kamarruangan_id
        where UPPER( a.kamarruangan_nokamar ) = '{$term}' and b.status_isi = true and b.is_deleted = false
        "; 
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        if ($data) {
            return "ada";
        }else{
            return "kosong";
        }
    }

    public function actionUpdateDashboard($id,$is_dashboard)
    {
        try {
            $now = date('Y-m-d H:i:s');
            $request = Yii::$app->request;
            $model = KamarRuangan::findOne($id);

            if ($model && !empty($model)) {
                $model->is_dashboard = $is_dashboard;
                if ($model->validate(false) && $model->save(false)) {
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    return [
                        'data' => $model->errors,
                        'status' => 422
                    ];
                }
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

    public function actionIndexKlasifikasiKamar()
    {
        try {
            $getRequest = Yii::$app->request->get();
            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['klasifikasikamar_nama'])) {
                    $_GET['advanced-filter']['klasifikasikamar_id'] = $_GET['advanced-filter']['klasifikasikamar_nama'];
                    unset($_GET['advanced-filter']['klasifikasikamar_nama']);
                }
            }
            $model = new KlasifikasiKamarV;
            $query = $model::find()->select([
                'klasifikasikamar_id',
                'klasifikasikamar_nama',
                'kamarruangan_id',
                'kamarruangan_nokamar',
                'ruangan_id',
                'ruangan_nama',
                'kelaspelayanan_nama',
                'is_active',
                'namakelas_aplicare',
                'namatt_rsonline'
            ])
            ->where(['is_deleted' => false])
            ->asArray();

            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionApiKlasifikasiKamar()
    {
        $request = Yii::$app->request;
        $modelKlasifikasi = KlasifikasiKamar::find()->select([
                'klasifikasikamar_id',
                'klasifikasikamar_nama'
            ])->where(['is_active' => true])
            ->asArray()
            ->all();

        $modelKamar = new KamarRuangan;
        $listKamar = $modelKamar::find()
            ->select([
                'kamarruangan_id',
                'kamarruangan_nokamar'
            ])
            ->asArray()
            ->all();

        if($request->get('type') == 'update') {
            $dataKamar = $modelKamar::find()
            ->select([
                'kamarruangan_id',
                'klasifikasikamar_id'
            ])
            ->where([
                'kamarruangan_id' => $request->get('id')
            ])->one();
        }
        return [
            'klasifikasi' => ArrayHelper::map($modelKlasifikasi, 'klasifikasikamar_id', 'klasifikasikamar_nama'),
            'listKamar' => ArrayHelper::map($listKamar, 'kamarruangan_id', 'kamarruangan_nokamar'),
            'dataKamar' => isset($dataKamar) ? $dataKamar : []
        ];
    }

    public function actionDeleteKlasifikasiKamar($id)
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try{
            // validasi jika sudah ter mapping dengan master tempat tidur
            $cekKamar = KamarTempatTidur::find()->select(['kamarruangan_id'])->where(['kamarruangan_id' => $id])->all();
            if (!empty($cekKamar)) {
                return $this->responseJson(422, 'Data Sudah Dimappingkan dengan master tempat tidur');
            }

            // integrasi bpjs Aplicare
            $integrasi = (new BpjsAplicare)->deleteAplicare($id);

            $model = KamarRuangan::find()->where(['kamarruangan_id' => $id])->one();
            $model->klasifikasikamar_id = null;
            $model->save();

            $transaction->commit();
            return $this->responseJson(200, 'Data berhasil disimpan',[]);
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(422, DocoMessages::ERR_MESSAGE);
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(422, DocoMessages::ERR_MESSAGE);
        }
    }

    public function actionUpsertKlasifikasiKamar()
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $type = $request->get('type');

        try{
            /** ketika update delete integrasinya */
            $idKamar = $request->post('kamarruangan_nokamar');
            if ($type == DocoConstants::TYPE_UPDATE_APLICARE) {
                $integrasi = (new BpjsAplicare)->deleteAplicare($idKamar);
            };
            $model = KamarRuangan::find()->where(['kamarruangan_id' => $idKamar])->one();
            $model->klasifikasikamar_id = $request->post('klasifikasikamar_id');
            if(!$model->save()){
                throw new \Exception("Gagal Simpan", 1);
            }

            $transaction->commit();

            /** integrasi bpjs aplicare create */
            $integrasi = (new BpjsAplicare)->createOrUpdateAplicare($idKamar,  DocoConstants::TYPE_CREATE_APLICARE);
            return $this->responseJson(200, 'Data berhasil disimpan',[]);
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(422, DocoMessages::ERR_MESSAGE);
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(422, DocoMessages::ERR_MESSAGE);
        }
    }

    /**
    * @controller actionExportPdfKlasifikasiKamar
    * @attribute #table# => table
    **/
    public function actionExportPdfKlasifikasiKamar()
    {
        $model = new KlasifikasiKamarV;
        $query = $model::find()
            ->select([
                'klasifikasikamar_id',
                'klasifikasikamar_nama',
                'kamarruangan_id',
                'kamarruangan_nokamar',
                'ruangan_id',
                'ruangan_nama',
                'kelaspelayanan_nama',
                'is_active',
            ])
            ->where(['is_deleted' => false]);

        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['klasifikasikamar_nama'])) {
                $_GET['advanced-filter']['klasifikasikamar_id'] = $_GET['advanced-filter']['klasifikasikamar_nama'];
                unset($_GET['advanced-filter']['klasifikasikamar_nama']);
            }
        }
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->all();

        $print = new DocoPrint();
        $print->attributes = [
            '#table#' => $this->renderPartial('_export_klasifikasi_kamar', [
                'data' => $data
            ]),
        ];
        $print->Output();
    }

    public function actionExportExcelKlasifikasiKamar()
    {
        try {
            $data = array();
            $header = array();
            $footer = array();
            $title = 'Klasifikasi Kamar';
            $model = new KlasifikasiKamarV;
            $query = $model::find()
                ->select([
                    'klasifikasikamar_id',
                    'klasifikasikamar_nama',
                    'kamarruangan_id',
                    'kamarruangan_nokamar',
                    'ruangan_id',
                    'ruangan_nama',
                    'kelaspelayanan_nama',
                    'is_active',
                ])
                ->where(['is_deleted' => false]);
    
            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['klasifikasikamar_nama'])) {
                    $_GET['advanced-filter']['klasifikasikamar_id'] = $_GET['advanced-filter']['klasifikasikamar_nama'];
                    unset($_GET['advanced-filter']['klasifikasikamar_nama']);
                    // $modelKlasifikasi = KlasifikasiKamar::find()->select([
                    //     'klasifikasikamar_id',
                    //     'klasifikasikamar_nama'
                    // ])
                    // ->where([
                    //     'klasifikasikamar_id' => $_GET['advanced-filter']['klasifikasikamar_id'] 
                    // ])
                    // ->one();
                    // $namaKlasifikasi = $modelKlasifikasi->klasifikasikamar_nama;
                }
            }
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query = $query->all();

            if (!empty($query)) {
                foreach ($query as $index => $value) {
                    $data[$index]['Kamar'] = $value->kamarruangan_nokamar;
                    $data[$index]['Ruangan'] = $value->ruangan_nama;
                    $data[$index]['Kelas Pelayanan'] = $value->ruangan_nama;
                    $data[$index]['Klasifkasi'] = $value->klasifikasikamar_nama;
                }
            }

            // $header = [
            //     'klasifikasi' => isset($namaKlasifikasi) ? $namaKlasifikasi : '',
            // ];

            $filePath = DocoHelpers::exportExcel($title, $data, $header, [],[],[],true);
            $filePath->save('php://output');
            die;
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

    /**
     * @author Chacha Nurholis (chacha@sirs.co.id)
     * @method getkamarRanap
     * @return Object
     */
    public function actionGetKamarRanap() 
    {
        $request = Yii::$app->request;
        $term = $request->get('term', null);
        $page = $request->get('page', 1);
        $limit = DocoConstants::LIMIT_INFINITY_SCROLL;
        $instalasiRanapId = (new LookUpTransaksiRepositories)->getInstalasiIdRi();
        $query = new \yii\db\Query();
        $kamarRuanganQuery = $query->from('kamarruangan_m')
            ->innerJoin('ruangan_m', 'kamarruangan_m.ruangan_id = ruangan_m.ruangan_id')
            ->andWhere(['instalasi_id' => $instalasiRanapId]);
        if (!empty($term)) {
            $kamarRuanganQuery->andWhere(['ILIKE', 'LOWER(kamarruangan_nokamar)', strtolower($term)]);
        }
        return $kamarRuanganQuery->limit($limit)->offset(($page - 1) * $limit)->all();
    }

    /**
     * @author Andri Amirul (andri.amirul@sirs.co.id)
     * @method getRuanganRanapDep (Mengambil Data Kamar Yang Dependency Dengan Ruangan Tanpa Infinity Scroll)
     * @param Integer $ruangan_id
     * @return Array
     */
    public function actionGetKamarRanapDep($ruangan_id = null)
    {
        $model = KamarRuangan::find(true);
        if($ruangan_id){
            $model->andWhere(['ruangan_id'=>$ruangan_id]);
        }
        return $model->asArray()->all();
    }
}