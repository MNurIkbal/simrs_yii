<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Display Antrian
 * @copyright 11 April 2018 aweutist
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoConstansId;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\DisplayAntrian;
use app\modules\v1\models\DisplayAntrianModel;
use app\modules\v1\models\LayarAntrian;
use app\modules\v1\models\LayarAntrianDetail;
use app\modules\v1\models\LoketJenisAntrianMp;
use app\modules\v1\models\JenisAntrianDetail;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\models\LayarAntrianDokterView;
use app\modules\v1\cache\Cache;
use app\modules\v1\models\InfoPasienOperasiView;
use app\modules\v1\models\RencanaOperasiView;
use yii\helpers\ArrayHelper;

class DisplayAntrianController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Lookup';
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

    // Index
    public function actionIndex() {
        // Try catch
        try {
            // Get request and expand the get
            $request = Yii::$app->request;
            $_GET['expand'] = $request->get('expand', 'lookup_m');

            // Define model
            $model = new LayarAntrian();

            // Query
            $query = $model::find(true)->joinWith(['lookup' => function($query) {
                // Select
                $query->select(['lookup_m.lookup_name', 'lookup_m.lookup_id']);
            }])->where([LayarAntrian::tableName().'.is_deleted' => false]);
            // ->andWhere([LayarAntrian::tableName().'.is_active' => true]);


            // Doco active filter
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            // Return data
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }


    public function actionView()
    {
        $model = new LayarAntrian;
        $query = $this->getData();
        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['layarantrian_jenis'])) {
                $query->andWhere(['=','lookup_m.lookup_name', $_GET['advanced-filter']['layarantrian_jenis']]);
            }
            if (isset($_GET['advanced-filter']['layarantrian_nama'])) {
                $query->andWhere(['ILIKE','layarantrian_m.layarantrian_nama', $_GET['advanced-filter']['layarantrian_nama']]);
            }
            if (isset($_GET['advanced-filter']['layarantrian_latarbelakang'])) {
                $query->andWhere(['ILIKE','layarantrian_m.layarantrian_latarbelakang', $_GET['advanced-filter']['layarantrian_latarbelakang']]);
            }
            if (isset($_GET['advanced-filter']['is_active'])) {
                $query->andWhere(['=','layarantrian_m.is_active', $_GET['advanced-filter']['is_active']]);
            }
        }
        $query->andWhere(['=','layarantrian_m.is_deleted', 'false']);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }


    // public function actionView($id)
    // {
    //     return $this->getData($id)->one();
    //     // return $this->getData($id)->asArray()->one();
    // }

    private function getData($id = null)
    {
        $returnData = (new \yii\db\Query())
                        ->select([
                                'layarantrian_m.layarantrian_id',
                                'layarantrian_m.jenisantrian_id',
                                'layarantrian_m.layarantrian_nama',
                                'lookup_m.lookup_name as layarantrian_jenis',
                                'layarantrian_m.layarantrian_latarbelakang',
                                'layarantrian_m.is_active'])->from('layarantrian_m layarantrian_m')
                        ->join('JOIN', 'lookup_m lookup_m','lookup_m.lookup_id = layarantrian_m.jenisantrian_id')
                        ->orderBy([ 'layarantrian_m.jenisantrian_id' => SORT_ASC ]);

        if ($id) {
            $returnData->where(['layarantrian_m.layarantrian_id' => $id]);
        }

        return $returnData;
    }

    public function actionListDisplayAntrian() {
        $items = ArrayHelper::map(DisplayAntrian::find()->where(['is_active' => true])->all(), 'layarantrian_id', 'layarantrian_judul');

        return $items;
    }

    public function actionListTypeScreen() {
        $items = ArrayHelper::map(Lookup::find()->where(['lookup_type' => 'jenis_antrian'])->all(), 'lookup_id', 'lookup_name');

        return $items;
    }

    public function actionListFunctionScreen() {
        $items = ArrayHelper::map(Lookup::find()->where(['lookup_type' => 'fungsi_antrian'])->all(), 'lookup_id', 'lookup_name');

        return $items;
    }

    protected function getJenisAntrian()
    {
        $model = new Lookup;
        $query = $model::find();
        $query->andWhere(['lookup_type' => 'jenis_antrian']);

        return $query->all();
    }

    protected function getRuanganPegawai()
    {
        $data = $model->getRuanganMapping();

        return $data;
    }

    public function actionAjax()
    {
        $data_antrian = $this->getJenisAntrian();
        $data_konfig = $this->getKonfigAntrian();

        return [
            'data-antrian' => $data_antrian,
            'data-konfig' => $data_konfig
        ];
    }

    public function actionGetRuangan()
    {
        $model = new DisplayAntrian;
        $request = Yii::$app->request;
        $post = $request->post();
        $data = [];
        if (!empty($post['list_id'])) {
            $list_id = implode(', ', $post['list_id']);
            $data = $model->getRuangan($list_id);
        }
        return $data;
    }


    public function actionSave()
    {
        $model = new LayarAntrian;
        $request = Yii::$app->request;
        $post = $request->post();
        $jenisantrian_id = empty($post['data_post']['DisplayAntrianForm']['jenisantrian_id']) ? null : $post['data_post']['DisplayAntrianForm']['jenisantrian_id'];
        
        $modelJenisAntrianDetail = null;
        if((new DocoConstansId)->actionGetId('konfig_antrian_using_jenisantriandetail') == 1){
            $modelJenisAntrianDetail = JenisAntrianDetail::find()->where(['jenisantrian_id' => $jenisantrian_id])->one();
        }
        $connection = Yii::$app->db;
        // return $post;
        $transaction = $connection->beginTransaction();
        try {
            $model->jenisantrian_id = $post['data']['jenisantrian_id'];
            $model->layarantrian_nama = $post['data']['layarantrian_nama'];
            $model->is_active = (int) $post['data']['is_active'];
            // $model->layarantrian_latarbelakang = $post['data']['layarantrian_latarbelakang'];

            if ($model->validate() && $model->save()) {
                $idParent = $model->layarantrian_id;
                $arr = [];
                if (!empty($post['data_post']['DisplayAntrianForm']['pegawai_id'])) {
                    foreach ($post['data_post']['DisplayAntrianForm']['pegawai_id'] as $value) {
                        $data = explode('-', $value);
                        $arr[] = [
                            'layarantrian_id' => $idParent,
                            'pegawai_id' => $data[0],
                            'ruangan_id' => $data[1]
                        ];
                    }
                }

                if (!empty($post['data_post']['DisplayAntrianForm']['loket_id'])) {
                    $jumlahLoket = count($post['data_post']['DisplayAntrianForm']['loket_id']);
                    $dataKonfig = $this->getKonfigAntrian();

                    if ($model->jenisantrian_id == DocoConstants::VAR_JA_PD) {
                        if ($dataKonfig['is_banyakloket']) {
                            if ($jumlahLoket > 8) {
                                $transaction->rollBack();
                                return [
                                    'status' => 200,
                                    'message' => Yii::t('app', 'Loket tidak boleh lebih dari 8.')
                                ];
                            }
                        } else {
                            if ($jumlahLoket > 4) {
                                $transaction->rollBack();
                                return [
                                    'status' => 200,
                                    'message' => Yii::t('app', 'Loket tidak boleh lebih dari 4.')
                                ];
                            }
                        }
                    }

                    foreach ($post['data_post']['DisplayAntrianForm']['loket_id'] as $value) {
                        $arr[] = [
                            'layarantrian_id' => $idParent,
                            'loket_id' => $value,
                        ];

                        if((new DocoConstansId)->actionGetId('konfig_antrian_using_jenisantriandetail') == 1){
                            if (!empty($modelJenisAntrianDetail['jenisantriandetail_id'])) {
                                $modelLoketJenisAntrianMp = new LoketJenisAntrianMp;
                                $findModelLoketJenisAntrianMp = $modelLoketJenisAntrianMp::find()->where(['loket_id'=> $value,'jenisantriandetail_id'=>$modelJenisAntrianDetail['jenisantriandetail_id']])->one();
                                if (!$findModelLoketJenisAntrianMp) {
                                    $modelLoketJenisAntrianMp->loket_id = $value;
                                    $modelLoketJenisAntrianMp->jenisantriandetail_id = $modelJenisAntrianDetail['jenisantriandetail_id'];
                                    $modelLoketJenisAntrianMp->save();
                                }
                            }
                        }
                    }
                }
                LayarAntrianDetail::batchInsert($arr);
                $transaction->commit();
                return ['message' => 'Data Berhasil di simpan'];
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

    }


    protected $_title = 'Display Antrian';
    public function actionExportExcel()
    {
        try {
            $data = array();
            $header = array();

            $model = new LayarAntrian;
            $query = $model::find()->where(['is_deleted' => false]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query)->all();

            if (!empty($query)) {
                $counter = 0;

                foreach ($query as $index => $value) {
                    // Assign data
                    $data[$counter]['jenis_layar_antrian'] = $value->lookup->lookup_name;
                    $data[$counter]['nama_layar_antrian'] = $value->layarantrian_nama;
                    $data[$counter]['status'] = $value->is_active == true ? Yii::t('app', 'Aktif') : Yii::t('app', 'Tidak Aktif');

                    $counter++;
                }
            }
            $filePath = DocoHelpers::exportExcel($this->_title, $data, $header, [],[],[],true);

            $filePath->save('php://output');
            die;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    /**
    * @controller actionCetakPdf
    * @attribute #layarantrian# => table
    **/

    public function actionCetakPdf()
    {
        $request = Yii::$app->request;
        $query = $this->getData();
        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['layarantrian_jenis'])) {
                $query->andWhere(['=','lookup_m.lookup_name', $_GET['advanced-filter']['layarantrian_jenis']]);
            }
            if (isset($_GET['advanced-filter']['layarantrian_nama'])) {
                $query->andWhere(['ILIKE','layarantrian_m.layarantrian_nama', $_GET['advanced-filter']['layarantrian_nama']]);
            }
            if (isset($_GET['advanced-filter']['layarantrian_latarbelakang'])) {
                $query->andWhere(['ILIKE','layarantrian_m.layarantrian_latarbelakang', $_GET['advanced-filter']['layarantrian_latarbelakang']]);
            }
            if (isset($_GET['advanced-filter']['is_active'])) {
                $query->andWhere(['=','layarantrian_m.is_active', $_GET['advanced-filter']['is_active']]);
            }
        }
        $query->andWhere(['=','layarantrian_m.is_deleted', 'false']);
        $data = $query->all();
            $header=[];
        // $filter = [
        //     'Ruangan nama' => $ruangan ? $ruangan->ruangan_nama : '-',
        //     'Status' => isset($_GET['advanced-filter']['is_active']) ? ($_GET['advanced-filter']['is_active'] == 0) ? 'Aktif' : 'Tidak Aktif' : '-' ,
        // ];
        $print = new DocoPrint();
        $print->attributes = [
            '#display-antrian#' => $this->renderPartial('index', [
                'filter'=> $header,
                'detail' => $data,
                'title' => 'Layar Antrian'
            ]),
        ];
        $print->Output();
    }

    public function actionViewData($id)
    {
        $model = new LayarAntrian;
        $modelDisplay = new DisplayAntrian;
        $dataRuangan = $modelDisplay->getRuanganByLayar($id);
        $query = $model->find();
        if ($id) {
            $query->andWhere(['layarantrian_m.layarantrian_id' => $id]);
        }
        return [
            'data-edit' => $query->one(),
            'data-ruangan' => $dataRuangan
        ];
    }

    public function actionViewDataLayarPoli($id)
    {
        $model = new LayarAntrian;
        $modelDisplay = new LayarAntrianDokterView;
        $dataRuangan = $modelDisplay->find()
        ->where(['layarantrian_id' => $id, 'hari_id' => DocoHelpers::getIdHariIni()])
        ->asArray()
        ->all();
        $query = $model->find();
        if ($id) {
            $query->andWhere(['layarantrian_m.layarantrian_id' => $id]);
        }
        $konfig_layar = Cache::getKonfigAntrian();
        $modelKonfigSystem = KonfigSystem::find()->limit(1)->one();
        return [
            'data-edit' => $query->one(),
            'data-ruangan' => $dataRuangan,
            'data-jadwal' => ArrayHelper::getColumn($dataRuangan, 'jadwaldokter_id'),
            'konfig_jenisantriandetail' => (new DocoConstansId)->actionGetId('konfig_antrian_using_jenisantriandetail'),
            'konfig-layar' => $konfig_layar,
            'konfig-system' => $modelKonfigSystem
        ];
    }

    public function actionUpdate($id)
    {
        try {
            $now = date('Y-m-d H:i:s');
            $request = Yii::$app->request;
            $model = LayarAntrian::findOne($id);
            $post = $request->post();
            $modelJenisAntrianDetail = null;
            if((new DocoConstansId)->actionGetId('konfig_antrian_using_jenisantriandetail') == 1){
                $modelJenisAntrianDetail = JenisAntrianDetail::find()->where(['jenisantrian_id' => $jenisantrian_id])->one();
            }

            if ($model && !empty($model)) {
                $model->jenisantrian_id = $post['data']['jenisantrian_id'];
                $model->layarantrian_nama = $post['data']['layarantrian_nama'];
                $model->is_active = (int) $post['data']['is_active'];

                if ($model->save()) {
                    $connection = Yii::$app->db;
                    $sql = "UPDATE layarantriandetail_m
                        SET
                            is_deleted = true,
                            deleted_date = '{$now}'
                        WHERE layarantrian_id = {$id}
                    ";
                    $connection->createCommand($sql)->execute();
                    $arr = [];
                    // if (!empty($post['pegawai_id'])) {
                    //     foreach ($post['pegawai_id'] as $value) {
                    //         $data = explode('-', $value);
                    //         $arr[] = [
                    //             'layarantrian_id' => $id,
                    //             'pegawai_id' => $data[0],
                    //             'ruangan_id' => $data[1]
                    //         ];
                    //     }
                    // }

                    // if (!empty($post['loket_id'])) {
                    //     foreach ($post['loket_id'] as $value) {
                    //         $arr[] = [
                    //             'layarantrian_id' => $id,
                    //             'loket_id' => $value,
                    //         ];
                    //     }
                    // }

                    if (!empty($post['data_post']['DisplayAntrianForm']['pegawai_id'])) {
                        foreach ($post['data_post']['DisplayAntrianForm']['pegawai_id'] as $value) {
                            $data = explode('-', $value);
                            $arr[] = [
                                'layarantrian_id' => $id,
                                'pegawai_id' => $data[0],
                                'ruangan_id' => $data[1]
                            ];
                        }
                    }

                    if (!empty($post['data_post']['DisplayAntrianForm']['loket_id'])) {
                        $jumlahLoket = count($post['data_post']['DisplayAntrianForm']['loket_id']);
                        $dataKonfig = $this->getKonfigAntrian();

                        if ($model->jenisantrian_id == DocoConstants::VAR_JA_PD) {
                            if ($dataKonfig['is_banyakloket']) {
                                if ($jumlahLoket > 8) {
                                    return [
                                        'status' => 200,
                                        'message' => Yii::t('app', 'Loket tidak boleh lebih dari 8.')
                                    ];
                                }
                            } else {
                                if ($jumlahLoket > 4) {
                                    return [
                                        'status' => 200,
                                        'message' => Yii::t('app', 'Loket tidak boleh lebih dari 4.')
                                    ];
                                }
                            }
                        }

                        foreach ($post['data_post']['DisplayAntrianForm']['loket_id'] as $value) {
                            $arr[] = [
                                'layarantrian_id' => $id,
                                'loket_id' => $value,
                            ];

                            if((new DocoConstansId)->actionGetId('konfig_antrian_using_jenisantriandetail') == 1){
                                if (!empty($modelJenisAntrianDetail['jenisantriandetail_id'])) {
                                    $modelLoketJenisAntrianMp = new LoketJenisAntrianMp;
                                    $findModelLoketJenisAntrianMp = $modelLoketJenisAntrianMp::find()->where(['loket_id'=> $value,'jenisantriandetail_id'=>$modelJenisAntrianDetail['jenisantriandetail_id']])->one();
                                    if (!$findModelLoketJenisAntrianMp) {
                                        $modelLoketJenisAntrianMp->loket_id = $value;
                                        $modelLoketJenisAntrianMp->jenisantriandetail_id = $modelJenisAntrianDetail['jenisantriandetail_id'];
                                        $modelLoketJenisAntrianMp->save();
                                    }
                                }
                            }
                        }
                    }
                    LayarAntrianDetail::batchInsert($arr);
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'LayarAntrian');
                    return [
                        'data' => $errors,
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

    public function actionDelete($id)
    {
        try {
            $now = date('Y-m-d H:i:s');
            $result = (new LayarAntrian)->delete($id);
            $connection = Yii::$app->db;
            $sql = "UPDATE layarantriandetail_m
                SET
                    is_deleted = true,
                    deleted_date = '{$now}'
                WHERE layarantrian_id = {$id}
            ";
            $connection->createCommand($sql)->execute();
            return $result;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetLoket()
    {
        $model = new DisplayAntrian;
        $request = Yii::$app->request;
        $post = $request->post();
        $data = [];
        if (isset($post['jenisantrian_id'])) {
            $data = $model->getLoket($post['jenisantrian_id']);
        }
        return $data;
    }

    public function actionGetSelectedLoket($id)
    {
        $modelDisplay = new DisplayAntrian;
        $dataLoket = $modelDisplay->getLoketByLayar($id);

        return $dataLoket;
    }

    /**
     * @todo Fungsi untuk mendapatkan konfig antrian
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    protected function getKonfigAntrian()
    {
        $model = KonfigSystem::find()->limit(1)->one();

        return $model;
    }

    /**
     * @todo Fungsi untuk mengambil data jadwal operasi
     */
    public function actionDataLayarJadwalOperasi()
    {
        $tmpData = [];
        $konfig_layar = Cache::getKonfigAntrian();
        $startOperasi = date('Y-m-d 00:00:00');
        $endOperasi = date('Y-m-d 23:59:00');
        $infoPasienOperasi = InfoPasienOperasiView::find(true)
        ->orderBy('no_masukpenunjang', 'ASC')
        ->asArray()
        ->select([
            'no_masukpenunjang',
            'no_rekam_medik',
            'nama_pasien',
            'no_peserta',
            'tgl_operasi',
            'ruangan_nama',
            'dok_perujuk',
            'pemeriksaan',
            'status'
        ])
        ->where(['between','tgl_operasi', $startOperasi, $endOperasi])
        ->all();

        foreach ($infoPasienOperasi as $key => $value) {
            $inArray = json_decode($value['pemeriksaan'], true);
            $tmpData[] = [
                'no_masukpenunjang' => $value['no_masukpenunjang'],
                'no_rekam_medik' => $value['no_rekam_medik'],
                'nama_pasien' => $value['nama_pasien'],
                'no_peserta' => $value['no_peserta'],
                'tgl_operasi' => $value['tgl_operasi'],
                'ruangan_nama' => $value['ruangan_nama'],
                'dok_perujuk' => $value['dok_perujuk'],
                'pemeriksaan' => isset($inArray[0]) ? $inArray[0] : null,
                'status' => $value[ 'status']
            ];
        }

        return [
            'dataJadwal' => $tmpData,
            'konfig-layar' => $konfig_layar
        ];
    }
}
