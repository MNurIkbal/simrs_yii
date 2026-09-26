<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Subrak;
use app\modules\v1\models\LokasiRakRekamMedik;

class SubRakRekamMedikController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Subrak';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'lokasirak_m');

        $model = new Subrak;
        $query = $model::find()
            ->where(['lokasirak_m.is_deleted' => 'f'])
            ->joinWith(['lokasirak' => function($query){
                $query->from('lokasirak_m');
            }]);

        $advancedFilters = $request->get('advanced-filter', []);
        // if(isset($advancedFilters['lokasirak_m.lokasirak_nama'])){
        //     $query->andWhere(['ILIKE','lokasirak_m.lokasirak_nama',$advancedFilters['lokasirak_m.lokasirak_nama']]);
        // }

        if(isset($advancedFilters['subrak_namalainnya'])){
            $query->andWhere(['ILIKE','subrak_namalainnya',$advancedFilters['subrak_namalainnya']]);
        }
        if(isset($advancedFilters['lokasirak_m.lokasirak_nama']) && $advancedFilters['lokasirak_m.lokasirak_nama'] != -1){
            $query->andWhere(['lokasirak_m.lokasirak_id' => $advancedFilters['lokasirak_m.lokasirak_nama']]);
            unset($_GET['advanced-filter']['lokasirak_m.lokasirak_nama']);
        }

        if (isset($advancedFilters['subrak_nama'])) {
            $query->andWhere(['subrak_nama'=>$advancedFilters['subrak_nama']]);
            unset($_GET['advanced-filter']['subrak_nama']);
        }
        // $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionView($id)
    {
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'lokasirak_m');

        $model = new Subrak;
        $query = Subrak::findOne($id);
        return $query;
    }

    public function actionListSubrak($lokasi)
    {
        $data = Subrak::find()->where(['is_active' => 't', 'is_deleted' => 'f', 'lokasirak_id' => $lokasi ]);

        $items = ArrayHelper::map($data->all(), 'subrak_id', 'subrak_nama');

        return $items;
    }

    public function actionListSubrakByName($lokasi)
    {
        $data = Subrak::find()
        ->joinWith(['lokasirak' => function($query){
                $query->from('lokasirak_m');
            }])
        ->where(['subrak_m.is_active' => 't', 'subrak_m.is_deleted' => 'f'])
        ->andWhere(['lokasirak_m.lokasirak_id' => $lokasi]);


        if(isset($advancedFilters['lokasirak_m.lokasirak_nama'])){
            $query->andWhere(['lokasirak_m.lokasirak_id' => $advancedFilters['lokasirak_m.lokasirak_nama']]);
            unset($_GET['advanced-filter']['lokasirak_m.lokasirak_nama']);
        }

        $items = ArrayHelper::map($data->all(), 'subrak_id', 'subrak_nama');

        return $items;
    }

    public function actionListSubrakAll()
    {
        $data = Subrak::find()
        ->select([
            'lokasirak_m.lokasirak_id',
            'subrak_m.subrak_id',
            'subrak_m.subrak_nama'
        ])
        ->innerJoin('lokasirak_m', 'subrak_m.lokasirak_id = lokasirak_m.lokasirak_id')
        ->where(['subrak_m.is_active' => 't', 'subrak_m.is_deleted' => 'f', 'lokasirak_m.is_active' => 't', 'lokasirak_m.is_deleted' => 'f']);

        $items = ArrayHelper::index($data->asArray()->all(), null, 'lokasirak_id');

        return $items;
    }

    public function actionListRak()
    {
        $request = Yii::$app->request;

        $model = new LokasiRakRekamMedik;
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
    public function actionGetDataNoSubRak($lokasi = '')
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $term = strtoupper($post['term']);
        $filter = "";
        if(!empty($lokasi)){
            $filter = " and lokasirak_id = '".$lokasi."' ";
        }

        $sql = "select subrak_id, subrak_nama from subrak_m where subrak_nama LIKE '%{$term}%'
            and is_deleted='false' ".$filter."
            group by subrak_id, subrak_nama
            order by subrak_id asc limit 50
        ";
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }

}
