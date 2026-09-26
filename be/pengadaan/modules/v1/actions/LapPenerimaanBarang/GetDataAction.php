<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace app\modules\v1\actions\LapPenerimaanBarang;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;
use app\modules\v1\models\LaporanPenerimaanBarangView;
use Doco\components\DocoRestActiveFilter;

class GetDataAction extends Action {
    public function run() {
        try {
            $request = Yii::$app->request;

            $model = new LaporanPenerimaanBarangView;
            $query = $model->find();

            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            if (isset($_GET['advanced-filter'])) {
                $advancedFilter = $_GET['advanced-filter'];

                if (isset($advancedFilter['tgl_penerimaan_awal']) &&
                    isset($advancedFilter['tgl_penerimaan_akhir'])) {
                    $start = $advancedFilter['tgl_penerimaan_awal'];
                    $end = $advancedFilter['tgl_penerimaan_akhir'];
                }
            }

            $query->andWhere(['between', 'tgl_penerimaan', $start, $end]);
            $query->orderBy(['tgl_penerimaan' => SORT_ASC, 'no_penerimaan' => SORT_ASC, 'barang_nama' => SORT_ASC]); 
            
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }
}
