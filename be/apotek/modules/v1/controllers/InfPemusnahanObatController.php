<?php

/**
 * @author Randy Vianda Putra
 * @todo Informasi Pemusnahan Obat Alkes
 * @copyright 10 January 2018 aweutist
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;

use app\modules\v1\models\InfoPemusnahanObatView;
use app\modules\v1\models\InfoPemusnahanObatDetailView;
use app\modules\v1\models\PemusnahanObat;
use app\modules\v1\models\PemusnahanObatDetail;

class InfPemusnahanObatController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPemusnahanObatView';

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
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        $model = new InfoPemusnahanObatView;
        $query = $model::find();

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['tglpemusnahan'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglpemusnahan']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglpemusnahan']); // Unset Advanced Filter  date range
                $between = true;
            }
        }
        $query->andWhere(['between', 'tglpemusnahan', $start, $end]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionDataNoPemusnahan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = $this->dataPemusnahan();
        $result->select(['nopemusnahan','nopemusnahan']);
        if(!empty($post['term'])){
            $term = $post['term'];
            $result->where(['ILIKE','LOWER(nopemusnahan)',$term]);
        }
        return $result->asArray()->all();
    }

    public function dataPemusnahan()
    {
        $data = InfoPemusnahanObatView::find();
        return $data;
    }

    public function actionDataPemusnahan()
    {
        $model = new InfoPemusnahanObatDetailView;
        $query = $model::find(true);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionDataDetail($id)
    {
        $query =  InfoPemusnahanObatDetailView::find()->where(['pemusnahanobat_id' => $id]);

        return $query->asArray()->one();
    }

    /**
    * @controller actionPrintPemusnahan
    * @attribute #data_pemusnahan# => print
    **/

    public function actionPrintPemusnahan()
    {
        $id = Yii::$app->request->get('id');
        $nopemusnahan = Yii::$app->request->get('nopemusnahan');
        $query =  InfoPemusnahanObatDetailView::find()->where(['pemusnahanobat_id' => $id]);
        $data_header = $query->asArray()->one();
        $model = new InfoPemusnahanObatDetailView;
        $query_pemusnahan = $model::find(true);
        $query_pemusnahan->where(['pemusnahanobat_id' => $id]);
        $data_pemusnahan = $query_pemusnahan->asArray()->all();

        $print = new DocoPrint();
        $print->attributes = [
            '#data_pemusnahan#' => $this->renderPartial('index', [
                'data' => $data_header,
                'data_pemusnahan' => $data_pemusnahan,
            ]),
        ];
        $print->Output();
    }

    public function actionDeletePemusnahan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $id = $post['id'];
            (new PemusnahanObat)->delete($id);
            (new PemusnahanObatDetail)->delete(['pemusnahanobat_id' => $id]);
            return [
                'message' => 'Data berhasil di hapus'
            ];
        } catch (Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\yii\db\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
        
    }
}