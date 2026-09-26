<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use app\modules\v1\models\InfoAdjustmentObatView;
use app\modules\v1\models\InfoAdjustmentObatDetailView;

class InformasiAdjustmentObatAlkesController extends DocoActiveController {
    public $modelClass = 'app\modules\v1\models\InfoAdjustmentObatView';

    public function verbs() {
        $verbs = parent::verbs();

        return $verbs;
    }
    public function actions() {
        $actions = parent::actions();
        unset($actions['index']);

        return $actions;
    }

    public function actionIndex() {
        $model = new InfoAdjustmentObatView;
        $query = $model::find();
        $ruangan_id = $_GET['advanced-filter']['ruangan_id'];
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_adjusmen'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_adjusmen']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d H:i:s', strtotime($explode[0]));
                    $end = date('Y-m-d H:i:s', strtotime($explode[1]."23:59:59"));
                }
                unset($_GET['advanced-filter']['tgl_adjusmen']); // Unset Advanced Filter  date range
                $between = true;
            }
        }

        $query->where(['ruangan_adjusmen_id' => $ruangan_id]);
        $query->andWhere(['between', 'tgl_adjusmen', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query
        ]);
    }

    public function actionSearchNoAdjustment($ruangan_id) {
        $request = Yii::$app->request;
        $term = $request->get('term', '');
        $ruangan_id = $request->get('ruangan_id', '');
        if (empty($term)) {
            return ['data' => []];
        }
        $data = InfoAdjustmentObatView::find()
            ->where(['ruangan_adjusmen_id' => $ruangan_id])
            ->andWhere(['like', 'LOWER(no_adjusmen)', strtolower($term)]);
        return ['data' => $data->all()];
    }

    public function actionDataAdjustment() {
        $request = Yii::$app->request;
        $model = new InfoAdjustmentObatDetailView;
        $query = $model::find(true);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        if (!empty($request->get('advanced-filter')['get_data'])) {
            if($request->get('advanced-filter')['get_data'] == true){
                return $query->asArray()->all();
            }
        }

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionInfoAdjustmentDetail() {
        try{
            $get = Yii::$app->request->get();
            $data = InfoAdjustmentObatView::find()->where(['no_adjusmen'=>$get['no_adjustment']]);
            $info_adjustment = $data->one();
            if(is_null($info_adjustment)){
                throw new \Exception("Data Adjustment Tidak Ada", 1);
            }
            return ['data'=>$info_adjustment];
        }catch(\Exception $e){
            return ['data'=>[]];
        }
    }
}