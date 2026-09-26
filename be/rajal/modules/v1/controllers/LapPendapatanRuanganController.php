<?php
// Author: Ardi Pratama

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanPendapatanRuanganView;
use yii\db\Query;

class LapPendapatanRuanganController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanPendapatanRuanganView';

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
    * @return array, activeQueryRecords data pendapatan ruangan
    *
    */

    public function actionIndex()
    {
        try{
            $request = Yii::$app->request;
            
            $model = new LaporanPendapatanRuanganView;
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

    public function actionView($id)
    {
        return $this->getData($id)->asArray()->one();
    }

    private function getData($id = null)
    {
        $model = LaporanPendapatanRuanganView::find();
        if ($id) {
            $model->where(['pendaftaran_id' => $id]);
        }

        return $model;
    }

}