<?php

/**
 * @Author: ijal
 * @Date:   2018-01-15 09:55:06
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-05-21 09:25:51
 * @Description: controller untuk master Kelas Pelayanan
 */
namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\JenisKelas;
use app\modules\v1\models\KelasPelayanan;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use yii\helpers\ArrayHelper;


class KelasPelayananController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\KelasPelayanan';

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
    * @author Rizal
    * @since 2018-01-15 10:27:04
    * @return array list data jenis kasus penyakit
    */
    public function actionIndex() {
        try {
            $request = Yii::$app->request;
            $query = $this->dataProvider();
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

    /**
    * @author Rizal
    * @since 2018-01-15 10:42:12
    * @param integer id
    * @return list activerecord kelas pelayanan
    * @desc 
    */
    private function getData($id = null)
    {
        $kelasPelayanan = KelasPelayanan::find()
        ->select([
            'kelaspelayanan_m.kelaspelayanan_id',
            'kelaspelayanan_m.jeniskelas_id',
            'kelaspelayanan_m.kelaspelayanan_nama',
            'kelaspelayanan_m.kelaspelayanan_namalainnya',
            'kelaspelayanan_m.persentasirujin',
            'kelaspelayanan_m.urutankelas',
            'kelaspelayanan_m.is_active',
            'jeniskelas_nama',
        ]) 
        ->joinWith([
            'jenisKelas' => function ($query) {
            $query->select([
                'jeniskelas_m.jeniskelas_nama',
                'jeniskelas_m.jeniskelas_id'
            ]);
        }]);
        if ($id) {
            $kelasPelayanan->where(['kelaspelayanan_m.kelaspelayanan_id' => $id]);
        }

        return $kelasPelayanan;
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
            $model = new KelasPelayanan;
            if ($request->post()) {
                $post = $request->post();
                $list = $request->post('list_kelas_pelayanan');
                $batchInsert = KelasPelayanan::batchInsert($list);
                if(!isset($batchInsert['status'])) {
                    return ['message' => 'Data berhasil disimpan.'];
                } else {
                    return [
                        'data' => $batchInsert['messages'],
                        'status' => 422
                    ];
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

    public function actionView($id)
    {
        return $this->getData($id)->asArray()->one();
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
                    return [
                        'data' => $model->errors,
                        'status' => 422
                    ];
                }
            }
            throw new Exception("Data Tidak Di Temukan");
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
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

    /**
    * @author Ayip
    * @since 2018-02-26 10:42:12
    * @param integer id
    * @return list activerecord jenis kelas
    * @desc untuk ddl jenis kelas
    */
    public function actionListJenisKelas($id = null) 
    {
        $data = JenisKelas::find()->where(['is_active' => 't']);
        $items = ArrayHelper::map($data->all(), 'jeniskelas_id', 'jeniskelas_nama');
        $kelas_pelayanan = $this->getKelasPelayanan();

        $ruangan = Ruangan::find()->where(['is_active' => 't']);
        $ruanganItems = ArrayHelper::map($ruangan->all(), 'ruangan_id', 'ruangan_nama');
        $kelasPelayananItems = ArrayHelper::map($kelas_pelayanan->all(), 'kelaspelayanan_id','kelaspelayanan_nama');
        $listData = [
            'jenis_kelas' => $items,
            'ruangan' => $ruanganItems,
            'kelas_pelayanan' => $kelasPelayananItems,
            'data_pelayanan' => []
        ];
        
        if ($id) {
            $query = KelasPelayanan::find()->where([
                'kelaspelayanan_id' => $id
            ])->one();
            $listData['data_pelayanan'] = $query;
        }
        return $listData;
    }

    private function dataProvider()
    {
        $model = new KelasPelayanan;
        $query = $model::find()->select([
            'kelaspelayanan_m.kelaspelayanan_id',
            'kelaspelayanan_m.jeniskelas_id',
            'kelaspelayanan_m.kelaspelayanan_nama',
            'kelaspelayanan_m.kelaspelayanan_namalainnya',
            'kelaspelayanan_m.persentasirujin',
            'kelaspelayanan_m.urutankelas',
            'kelaspelayanan_m.is_active',
            'jeniskelas_nama',
        ])->joinWith([
            'jenisKelas' => function ($query) {
            $query->select([
                'jeniskelas_m.jeniskelas_nama',
                'jeniskelas_m.jeniskelas_id'
            ]);
        }])->asArray();

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['jeniskelas_m.jeniskelas_nama'])){
                $jeniskelas = $_GET['advanced-filter']['jeniskelas_m.jeniskelas_nama'];
                $query->andWhere(['kelaspelayanan_m.jeniskelas_id' => $jeniskelas]);
               unset($_GET['advanced-filter']['jeniskelas_m.jeniskelas_nama']);
           
            }
        }
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
       
        return $query;
    }

    public function actionExportExcel()
    {
        $query = KelasPelayanan::find()->select([
            'kelaspelayanan_m.kelaspelayanan_id',
            'kelaspelayanan_m.jeniskelas_id',
            'kelaspelayanan_m.kelaspelayanan_nama',
            'kelaspelayanan_m.kelaspelayanan_namalainnya',
            'kelaspelayanan_m.is_active',
            'jeniskelas_m.jeniskelas_nama',
        ])->joinWith(["jenisKelas"])->asArray()->all();
        $result = [];
        foreach ($query as $value) {
            $value['is_active'] = $value['is_active'] ? "Aktif" : "Tidak Aktif";
            if (isset($value['jenisKelas'])) {
                unset($value['jenisKelas']);
            }
            $result[] = $value;
        }

        $_title = "Kelas Pelayanan";

        $filePath = DocoHelpers::exportExcel($_title, $result, [], [
            "uploadPath" => "./uploads",
        ]);
        
        return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));
    }

    /**
    * @controller actionCetakKelasPelayanan
    * @attribute #tabel_kelas_pelayanan# => Untuk Menampilkan Tabel kelas Pelayanan
    **/

    public function actionCetakKelasPelayanan()
    {
        $query = $this->dataProvider();
        $print = new DocoPrint;
        $print->attributes = [
            '#tabel_kelas_pelayanan#' => $this->renderPartial('index',[
                'detail' => $query->all()
            ])
        ];
        $print->Output();
    }

    public function actionListRuangan() {
        $data = Ruangan::find()->where(['is_active' => 't', 'is_deleted' => 'f']);
        $items = ArrayHelper::map($data->all(), 'ruangan_id', 'ruangan_nama');

        return $items;
    }

    /**
     * @author: arief saputra
     * @description: list jenis kegiatan tindakan
    **/

    public function actionListKelasPelayanan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = $this->getKelasPelayanan();
        $result->select(['kelaspelayanan_id','kelaspelayanan_nama']);
        if(!empty($post['term'])){
            $term = $post['term'];
            $result->where(['like', 'LOWER(kelaspelayanan_nama)', $term]);
        }
        return $result->all();
    }

    public function getKelasPelayanan()
    {
        $data = KelasPelayanan::find();
        return $data;

    }

}