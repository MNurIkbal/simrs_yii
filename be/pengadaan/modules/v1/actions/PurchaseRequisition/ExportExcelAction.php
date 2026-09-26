<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\PurchaseRequisition;

use Yii;
use yii\base\Action;
use GuzzleHttp\Exception\RequestException;
use Doco\components\DocoHelpers;
use app\modules\v1\models\LaporanPurchaseRequisitionView;
use Doco\components\DocoRestActiveFilter;

class ExportExcelAction extends Action {
    public function run() {
        try {
            $title = 'Laporan Purchase Requisition';
            $request = Yii::$app->request;
            $advanced_filter = $request->get('advanced-filter');
            $model = new LaporanPurchaseRequisitionView;
            $query = $model::find();
            if(count($advanced_filter) > 0) {
                $this->controller->dateFilter($query, $request);
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            if(!is_null($advanced_filter)) {
                $header = $model->setHeaderExcel($advanced_filter);
            } else {
                $header = [];
            }

            $result = $model->mappingDataExcel($query);
            $filePath = DocoHelpers::exportExcel($title, $result, $header, [], [], [], true);
            $filePath->save('php://output');
            die;
        } catch (\Yii\db\Exception $e) {
            return $e->getMessage();
        } catch (\Exception $e){
            return $e->getMessage();
        }
    }
}