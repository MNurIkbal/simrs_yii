<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\InfoPurchaseOrder;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;
use Doco\components\DocoConstants;
use app\modules\v1\models\LaporanPurchaseOrderOutstandingView;
use app\modules\v1\models\LaporanPurchaseOrderOutstandingBarangView;
use Doco\components\DocoRestActiveFilter;

class LaporanPOOutstandingAction extends Action {
    public function run() {
        try {
            $request = Yii::$app->request;
            $advanced_filter = $request->get('advanced-filter');
            $type = $request->get('type');

            if($type == DocoConstants::JENIS_OBAT) {
                $model = new LaporanPurchaseOrderOutstandingView;
                $dateKey = 'tgl_po_dibuat';
            } else {
                // non-medis
                $model = new LaporanPurchaseOrderOutstandingBarangView;
                $dateKey = 'tanggal_po';
            }
            
            $query = $model::find();

            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            if (isset($advanced_filter)) {
                if (isset($advanced_filter['tgl_po_dibuat'])) {
                    $tanggal = explode(' - ', $advanced_filter['tgl_po_dibuat']);
                    $start = date('Y-m-d H:i:s', strtotime($tanggal[0] . ' 00:00:00'));
                    $end = date('Y-m-d H:i:s', strtotime($tanggal[1] . ' 23:59:59'));
                    unset($advanced_filter['tgl_po_dibuat']);
                } else {
                    $tanggal = explode(' - ', $advanced_filter['tanggal_po']);
                    $start = date('Y-m-d H:i:s', strtotime($tanggal[0] . ' 00:00:00'));
                    $end = date('Y-m-d H:i:s', strtotime($tanggal[1] . ' 23:59:59'));
                    unset($advanced_filter['tanggal_po']);
                }
            }

            $query->andWhere(['between', $dateKey, $start, $end]);
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
