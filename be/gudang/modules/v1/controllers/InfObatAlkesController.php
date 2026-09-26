<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use app\modules\v1\models\InfoObatAlkesView;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\JenisObatAlkes;
use app\modules\v1\models\ObatAlkes;
use Doco\components\DocoHelpers;

class InfObatAlkesController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoObatAlkesView';
    protected $_title = "Informasi Obat Alkes";

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new InfoObatAlkesView;
            $query = $model::find();

            if(isset($_GET['advanced-filter'])) {
                if(!empty($_GET['advanced-filter']['jenisobatalkes_nama'])){
                    $_GET['advanced-filter']['jenisobatalkes_id'] = $_GET['advanced-filter']['jenisobatalkes_nama'];
                    unset($_GET['advanced-filter']['jenisobatalkes_nama']);
                }
                // return $_GET['advanced-filter'];
                if(!empty($_GET['advanced-filter']['ven_nama'])){
                    $ven = $_GET['advanced-filter']['ven_nama'];
                    $query->andWhere(['ven' => $ven]);
                    unset($_GET['advanced-filter']['ven_nama']);
                }
                if(!empty($_GET['advanced-filter']['supplier_nama'])){
                    $supplier_id = $_GET['advanced-filter']['supplier_nama'];
                    $query->andWhere(['supplier_id' => $supplier_id]);
                    unset($_GET['advanced-filter']['supplier_nama']);
                }
                if(!empty($_GET['advanced-filter']['obatalkes_nama'])){
                    $obatalkes_id = $_GET['advanced-filter']['obatalkes_nama'];
                    $query->andWhere(['obatalkes_id' => $obatalkes_id]);
                    unset($_GET['advanced-filter']['obatalkes_nama']);
                }
            }
            
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
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

    public function actionGenerateApi()
    {
        // jenis obat
        $modelJenisObat = new JenisObatAlkes;
        $queryJenisObat = $modelJenisObat::find();

        $queryJenisObat = DocoRestActiveFilter::advancedFilter($modelJenisObat, $queryJenisObat);
        $queryJenisObat = new ActiveDataProvider([
            'query' => $queryJenisObat,
        ]);

        // ven
        $modelVen = new Lookup;
        $queryVen = $modelVen::find()->where(['lookup_type' => 'ven', 'is_deleted' => false, 'is_active' => true]);

        $queryVen = DocoRestActiveFilter::advancedFilter($modelVen, $queryVen);
        $queryVen = new ActiveDataProvider([
            'query' => $queryVen,
        ]);

        return [
            'jenis_obat' => $queryJenisObat->getModels(),
            'ven' => $queryVen->getModels(),
        ];
    }

    public function actionDelete($id)
    {
        try {
            $result = (new ObatAlkes)->delete($id);
            if($result) {
                $result->delete();
            }
            return $result;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}