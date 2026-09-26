<?php

/**
 * @author : Ardi Pratama (ardi.pratama@sirs.co.id)
 * Powered by Sirs
 */

namespace Extensions\pengadaan;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use app\components\DocoHelpers;
use app\modules\v1\models\LaporanAllPOView;
use Doco\components\DocoRestActiveFilter;

class GetdataLaporanPurchaseOrderMhbg extends \Doco\components\DocoBaseProcessExtension
{
	protected function processFlow() {
        try {
            $request = Yii::$app->request;
            $model = new LaporanAllPOView;
            $query = $model::find();
            $tanggal_po = ArrayHelper::getValue($request->get(),'advanced-filter.tanggal_po',null);
            $tipe_po = ArrayHelper::getValue($request->get(),'advanced-filter.type',null);
            $tipe = $request->get('tipe',$tipe_po);
            $tipe = strtoupper($tipe);
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:59');
            if(!is_null($tanggal_po)){
            	$explode = explode(" - ", $tanggal_po);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tanggal_po']);
            }
            $query->andWhere(['between', 'tanggal_po', $start, $end]);
            $query->andWhere(['type'=>$tipe]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            return new ActiveDataProvider([
                'query' => $query,
            ]);
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