<?php

/**
 * @author : ilham
 * Powered by Sirs
 */

namespace Extensions\gizi;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use app\components\DocoHelpers;
use app\modules\v1\models\LaporanPermintaanMakanMhbg;
use Doco\components\DocoRestActiveFilter;


class GetDataPermintaanMakanMhg extends \Doco\components\DocoBaseProcessExtension

{
	protected function processFlow() {
            try {
                $request = Yii::$app->request;
                $model = new LaporanPermintaanMakanMhbg;
                $query = $model::find();
                $params = [];
                $start = '2010-02-22';
                $end = date('Y-m-d 23:59:59');

                if(isset($_GET['advanced-filter']['tgl_admisi']) && $_GET['advanced-filter']['tgl_admisi'] != '') {
                        $end = date('Y-m-d 23:59:59', strtotime($_GET['advanced-filter']['tgl_admisi']));
                        unset($_GET['advanced-filter']['tgl_admisi']);
                }

                $query->andWhere(['between', 'tgl_admisi', $start, $end]);
                $query->andWhere([
                    'is_stopakomodasi' => false
                ]);
                $query = DocoRestActiveFilter::advancedFilter($model, $query);
                $query->orderBy(['tgl_admisi' => SORT_DESC]);

                $data = new ActiveDataProvider([
                        'query' => $query,
                    ]);
                return [
                    'data' => $data->getModels(),
                    '_meta' => [
                        'totalCount' => $data->getTotalCount()
                    ],

                ];
            } catch (\yii\db\Exception $e) {
                return [
                    'status' => 500,
                    'message' => $e->getMessage()
                ];
            } catch (\Exception $e) {
                return [
                    'status' => 500,
                    'message' => $e->getMessage()
                ];
            }
    }



}