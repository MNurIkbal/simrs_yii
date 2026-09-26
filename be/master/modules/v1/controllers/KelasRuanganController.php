<?php

/**
 * @Author: ijal
 * @Date:   2018-01-15 09:55:06
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-05-24 15:48:50
 * @Description: controller untuk master Kelas Pelayanan
 */
namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Kelasruangan;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\KelasPelayanan;
use Doco\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use Doco\components\DocoPrint;

class KelasRuanganController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Kelasruangan';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["create"] = ["POST", "GET"];
        $verbs["view"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "GET"];
        $verbs["delete"] = ["DELETE"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['view']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }


    /**
    * @author Ayip
    * @since 2018-01-15 10:27:04
    * @return array list data jenis kasus penyakit
    */
    public function actionIndex() {
        try 
        {
            $request = Yii::$app->request;

            $get = $request->get();
            $page = (isset($get['page'])) ? $get['page']: 1;
            $perPage = (isset($get['per-page'])) ? $get['per-page']: 10;

            $offset = ($page - 1) * $perPage;

            $result = $this->getData()
                ->limit($request->post('length',$perPage))
                ->offset($request->post('start',$offset));

            if(isset($get['advanced-filter'])) {
                $advancedFilters = $get['advanced-filter'];
                if(isset($advancedFilters['ruangan_nama'])) {
                    $result->andFilterWhere(['t.ruangan_id' => $advancedFilters['ruangan_nama']]);
                }

                if(isset($advancedFilters['kelaspelayanan_nama'])) {
                    $result->andFilterWhere(['t.kelaspelayanan_id' => $advancedFilters['kelaspelayanan_nama']]);
                }
            }
            
            // if ($nama = $request->post('kelaspelayanan_nama')) {
            //     $result->andFilterWhere(['ILIKE','t3.kelaspelayanan_nama',$nama]);
            // }
            // if ($namaLainnya = $request->post('kelaspelayanan_namalainnya')) {
            //     $result->andFilterWhere(['ILIKE','t3.kelaspelayanan_namalainnya',$namaLainnya]);
            // }

            $status = $request->post('status');
            $is_active = $status === '0' ? false : true;
            $result->andWhere(['t3.is_active' => $is_active]);

            if ($order = $request->post('orderby')) {
                $dir = (int) $request->post('dir');
                $result->orderby([$order => $dir]);
            }

            return [
                'filter'=>$request->post(),
                'data' => $result->all(),
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

    /**
    * @author Arief
    * @since 2018-02-05 10:42:12
    * @param integer id
    * @return list activerecord kelas pelayanan
    * @desc 
    */
    private function getData($id = null)
    {

        $returnData = (new \yii\db\Query())
                        ->select([
                                't.ruangan_id',
                                't.kelaspelayanan_id',
                                't.is_active',
                                't.is_deleted',
                                't2.ruangan_nama',
                                't3.kelaspelayanan_nama'
                        ])->from('kelasruangan_mp t')
                        ->join('LEFT JOIN', 'ruangan_m t2','t2.ruangan_id = t.ruangan_id')
                        ->join('LEFT JOIN', 'kelaspelayanan_m t3','t3.kelaspelayanan_id = t.kelaspelayanan_id')
                        ->where(['t.is_deleted' => false])
                        ->orderBy([ 't.kelaspelayanan_id' => SORT_ASC ]);
        if($id)
        {
            $returnData->where(['t.layarantrian_id' => $id]);
        }

        return $returnData;
    }

    /**
    * @author Rizal
    * @since 2018-01-15 11:17:44 
    * @param 
    * @return response result of create
    * @desc 
    */
    public function actionCreate() {
        try {
            $request = Yii::$app->request;
            $model = new Kelasruangan;
            if ($request->post()) {
                $post = $request->post();

                $data = [];
                $data['rejected'] = [];
                $data['accepted'] = [];

                $i = 0;
                foreach($post['KelasRuanganForm']['list_ruangan_id'] as $as)
                {
                    $kelaspelayanan_id = $post['KelasRuanganForm']['kelaspelayanan_id'];
                    $ruangan_id = $as;

                    // CHECK IS EXISTING
                    $existing = Kelasruangan::find(true)
                        ->select([
                            'kelasruangan_mp.is_active',
                            'kelasruangan_mp.is_deleted'
                        ])
                        ->andWhere(['kelasruangan_mp.kelaspelayanan_id' => $kelaspelayanan_id])
                        ->andWhere(['kelasruangan_mp.ruangan_id' => $ruangan_id])
                        ->one();

                    if($existing) {
                        if($existing->is_active == true && $existing->is_deleted == false)
                        {
                            $data['rejected'][] = array(
                                'kelaspelayanan_id' => $kelaspelayanan_id,
                                'ruangan_id' => $ruangan_id
                            );
                        }
                        else
                        {
                            $model->attributes = array(
                                    'kelaspelayanan_id' => $kelaspelayanan_id,
                                    'ruangan_id' => $ruangan_id,
                                    'is_active' => 't',
                                    'is_deleted' => 'f'

                                );

                            if ($model->update()) {
                                $data['accepted'][] = array(
                                        'kelaspelayanan_id' => $kelaspelayanan_id,
                                        'ruangan_id' => $ruangan_id
                                    );
                            } else {
                                $data['rejected'][] = array(
                                    'kelaspelayanan_id' => $kelaspelayanan_id,
                                    'ruangan_id' => $ruangan_id
                                );
                            }
                        }
                        
                    }
                    else {

                        $model->attributes = array(
                                    'kelaspelayanan_id' => $kelaspelayanan_id,
                                    'ruangan_id' => $ruangan_id,
                                );

                        if ($model->save()) {
                            $data['accepted'][] = array(
                                    'kelaspelayanan_id' => $kelaspelayanan_id,
                                    'ruangan_id' => $ruangan_id
                                );
                        } else {
                            $data['rejected'][] = array(
                                'kelaspelayanan_id' => $kelaspelayanan_id,
                                'ruangan_id' => $ruangan_id
                            );
                        }

                        
                    }
                
                    $i++;
                }

                if($i > 0) {
                    $accept = (count($data['accepted']) > 0) ?  " (".count($data['accepted']).') Data Telah Disimpan. ':'';
                    $reject = (count($data['rejected']) > 0) ? " (".count($data['rejected']).') Data Telah Ditolak. ':'';

                    return ['message' => $accept.$reject];
                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'KelasRuanganForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
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
    }

    public function actionView($id)
    {
        return $this->getData($id)->asArray()->one();
    }

    public function actionEdit($kelaspelayanan_id, $ruangan_id)
    {
        return $this->getNewData($kelaspelayanan_id, $ruangan_id)->one();
    }

    private function getNewData($kelaspelayanan_id, $ruangan_id)
    {
        $returnData = (new \yii\db\Query())
                        ->select([
                                't.ruangan_id',
                                't.kelaspelayanan_id',
                                't.is_active',
                                't.is_deleted',
                                't2.ruangan_nama',
                                't3.kelaspelayanan_nama'
                        ])->from('kelasruangan_mp t')
                        ->join('LEFT JOIN', 'ruangan_m t2','t2.ruangan_id = t.ruangan_id')
                        ->join('LEFT JOIN', 'kelaspelayanan_m t3','t3.kelaspelayanan_id = t.kelaspelayanan_id')
                        ->where(['t.kelaspelayanan_id' => $kelaspelayanan_id, 't.ruangan_id' => $ruangan_id])
                        ->orderBy([ 't.kelaspelayanan_id' => SORT_ASC ]);

        return $returnData;
    }

    public function actionUpdate($id)
    {
        try {
            $request = Yii::$app->request;
            $model = KelasPelayanan::findOne($id);
            if ($request->post() && !empty($model)) {
                $model->attributes = $request->post();
                if ($model->save()) {
                    return [
                        'message' => 'Data berhasil diubah.',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'KelasPelayananForm');
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

    public function actionUpdateRuangan($kelaspelayanan_id, $ruangan_id)
    {
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $postKelasRuangan = $post['KelasRuanganForm'];
            $latest = $post['latest'];
            $user_id = $post['user_id'];
            if ($post) {
                $delete = new Kelasruangan;
                $model = $delete->delete(['kelaspelayanan_id' => $kelaspelayanan_id, 'ruangan_id' => $latest]);
                if($model) {
                    foreach ($postKelasRuangan['list_ruangan_id'] as $key => $value) {
                        $model = new Kelasruangan;
                        $model->kelaspelayanan_id = $postKelasRuangan['kelaspelayanan_id'];
                        $model->ruangan_id = $value;
                        $model->is_active = true;
                        $model->save();
                    }

                    $transaction->commit();
                    return [
                        'message' => 'Data berhasil diubah.',
                    ];
                }
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
    }

    public function actionCreateRuangan()
    {
        try {
            $request = Yii::$app->request;
            $saved = false;
            $errors = [];

            if ($request->post()) {
                $post = $request->post();
                $list_ruangan_id = $post['KelasRuanganForm']['list_ruangan_id'];
                $modelKelasRuangan = new Kelasruangan;
                $modelKelasRuangan->attributes = $post['KelasRuanganForm'];
                $modelKelasRuangan->ruangan_id = $post['KelasRuanganForm']['list_ruangan_id'];

                $arrInsert = [];
                if($modelKelasRuangan->validate()) {
                    $saved = true;
                    foreach ($modelKelasRuangan->ruangan_id as $ruangan_id) {
                        $arrInsert[] = [
                            'ruangan_id' => $ruangan_id,
                            'kelaspelayanan_id' => $modelKelasRuangan->kelaspelayanan_id
                        ];
                    }

                    Kelasruangan::batchInsert($arrInsert, false);
                }
                else {
                    $saved = false;
                    $errors = DocoHelpers::parseError(
                        $modelKelasRuangan->errors, 
                        'KelasRuanganForm'
                    );
                    return [
                        'data' => $errors,
                        'status' => 422,
                    ];
                }

                if ($saved) {
                    // $transaction->commit();
                    return ['message' => 'Data berhasil disimpan.'];
                } else {
                    // $transaction->rollback();
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
    * @author Rizal
    * @since 2018-01-15 11:58:55
    * @param integer id
    * @return 
    * @desc action to soft delete row
    */
    public function actionDelete($id)
    {
        try {
            $model = new KelasPelayanan;
            $result = $model->delete($id);
            return $result;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionDeleteRuangan($kelaspelayanan_id, $ruangan_id)
    {
        try {
            $result = new Kelasruangan;
            $result = $result->delete(['kelaspelayanan_id' => $kelaspelayanan_id, 'ruangan_id' => $ruangan_id]);
            return $result;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
    * @author Ayip
    * @since 2018-02-26 10:42:12
    * @param integer id
    * @return list activerecord jenis kelas
    * @desc untuk ddl jenis kelas
    */
    public function actionListJenisKelas() {
        $data = JenisKelas::find()->where(['is_active' => 't', 'is_deleted' => 'f']);

        $items = ArrayHelper::map($data->all(), 'jeniskelas_id', 'jeniskelas_nama');

        return $items;
    }

    /**
    * @controller actionCetakKelasRuangan
    * @attribute #tabel_kelas_ruangan# => Untuk Menampilkan Tabel kelas Ruangan
    **/

    public function actionCetakKelasRuangan()
    {
        $query = $this->dataProvider();
        if(isset($_GET['advanced-filter'])){            
            $filter = $_GET['advanced-filter'];
            if(isset($filter['ruangan_nama'])) {
                $query->andFilterWhere(['kelasruangan_mp.ruangan_id' => $filter['ruangan_nama']]);
            }

            if(isset($filter['kelaspelayanan_nama'])) {
                $query->andFilterWhere(['kelasruangan_mp.kelaspelayanan_id' => $filter['kelaspelayanan_nama']]);
            }
        }

        // Creating an array as per the need for the table
        $data = $query->asArray()->all();
        $array = [];
        foreach ($data as $value) {
            $array[] = $value;
        }

        $filter = [
            'Kelas Pelayanan'=> isset($_GET['advanced-filter']['kelaspelayanan_nama']) ? 
                $_GET['advanced-filter']['kelaspelayanan_nama'] : '-',
            'Nama Ruangan'=> isset($_GET['advanced-filter']['ruangan_nama']) ? $_GET['advanced-filter']['ruangan_nama'] : '-',
        ];
        $print = new DocoPrint();    
        $print->attributes = [
            '#tabel_kelas_ruangan#' => $this->renderPartial('index',[
                'filter'=> $filter,
                'detail' => $array,
            ]),            
        ];
        $print->Output();
    }

    private function dataProvider()
    {
        $model = new Kelasruangan;
        $query = $model::find()->select([
            'kelasruangan_mp.kelaspelayanan_id',
            'kelasruangan_mp.ruangan_id',
            'kelaspelayanan_nama',
            'ruangan_nama',
        ])->joinWith([
            'kelasPelayanan' => function ($query) {
            $query->select([
                'kelaspelayanan_m.kelaspelayanan_nama',
                'kelaspelayanan_m.kelaspelayanan_id'
            ]);
        }])->joinWith([
            'ruangan' => function ($query) {
            $query->select([
                'ruangan_m.ruangan_nama',
                'ruangan_m.ruangan_id'
            ]);
        }]);
        
        return $query;
    }

    protected $_title = 'Master Kelas Ruangan';
    public function actionExportExcelRuangan()
    {
        try {
            $query = $this->dataProvider();
            if(isset($_GET['advanced-filter'])){            
                $filter = $_GET['advanced-filter'];                                                 
                if(isset($filter['ruangan_nama'])) {
                    $query->andFilterWhere(['kelasruangan_mp.ruangan_id' => $filter['ruangan_nama']]);
                }

                if(isset($filter['kelaspelayanan_nama'])) {
                    $query->andFilterWhere(['kelasruangan_mp.kelaspelayanan_id' => $filter['kelaspelayanan_nama']]);
                }           
            }

            if(isset($_GET['order'])){
                $order = ' order by '.$_GET['order'];               
            }

            $data = $query->asArray()->all();
            // Declare temp
            $tempData = array();

            // Check data
            if (!empty($data)) {
                // Loop
                $counter = 0;
                foreach ($data as $value) {
                    // Assign temp
                    $tempData[$counter]['ruangan_nama'] = $value['ruangan_nama'];
                    $tempData[$counter]['kelaspelayanan_nama'] = $value['kelaspelayanan_nama'];

                    $counter++;
                }
            }
            
            $header = array();
            $filePath = DocoHelpers::exportExcel($this->_title, $tempData, $header, array(
                "uploadPath" => "./uploads",
            ));

            return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionListKelasPelayanan($q = null)
    {
        $query = KelasPelayanan::find()->where(['ILIKE', 'kelaspelayanan_nama', $q])->orderBy('kelaspelayanan_nama')->all();
        $out = [];
        foreach ($query as $d) {
            
            $out[] = ['id' => $d['kelaspelayanan_id'], 'text' => $d['kelaspelayanan_nama']];
        }
        
        return $out;
    }
}