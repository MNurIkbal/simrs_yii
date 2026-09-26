<?php
// Author: Ardi Pratama

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\PembebasanTarif;
use app\modules\v1\models\InfoPembebasanTarifView;
use yii\db\Query;

class LapPembebasanTarifController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\PembebasanTarif';

    public function verbs()
    {
        $verbs = parent::verbs();

        // additional/ override verbs


        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();

        // unset default action
        unset($actions['index']);
        unset($actions['view']);
        

        return $actions;
    }

    /**
    *
    * @see Fungsi override action index
    * @return array, activeQueryRecords data pembebasan tarif
    *
    */

    public function actionIndex()
    {
        try{
            $request = Yii::$app->request;
            
            $model = new InfoPembebasanTarifView;
            $query = $model::find();
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $advancedFilters = $request->get('advanced-filter', []);
            if (isset($advancedFilters['tgl_pendaftaran_awal']) 
                    && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
                $tgl_awal = $advancedFilters['tgl_pendaftaran_awal'];
                $tgl_akhir = $advancedFilters['tgl_pendaftaran_akhir'];
                $query->andWhere(['between', 'tgl_pendaftaran', $tgl_awal, $tgl_akhir]);
            }
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
    * @see Fungsi override action view
    * @return array, activeQueryRecords data pembebasan tarif
    *
    */
    public function actionView()
    {
        try {
            $request = Yii::$app->request;

            $model = new BuatJanjiPoli;
            $query = $this->getData($request->get('ruangan_id'), $request->get('id', null));
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            return [
                'data' => $query->asArray()->one(),
                'count' => $query->count()
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

}