<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace app\modules\v1\actions\LapPurchaseRequisitionOutstanding;

use Yii;
use yii\base\Action;
use GuzzleHttp\Exception\RequestException;
use Doco\components\DocoHelpers;
use app\modules\v1\models\LapPurchaseRequisitionOutstandingView;
use app\modules\v1\models\LapPurchaseRequisitionOutstandingBarangView;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;

class ExportExcelAction extends Action {
    public function run() {
        try {
            $title = 'Laporan Purchase Requisition Outstanding';
            $request = Yii::$app->request;
            $type = $request->get('type');

            if($type == DocoConstants::JENIS_OBAT) {
                $model = new LapPurchaseRequisitionOutstandingView;
                $dateFilter = 'tgl_pr';
                $options = [
                        "titleStyle" => [
                        "fontSize" => 11,
                        "alignment" => "left"
                    ],
                    "subTitle" => "Medical PR Outstanding Report",
                    "customFormatCode" => [
                        [
                            'selectColumn' => 'C',
                            'formatCode' => 'datetime'
                        ]
                    ],
                ];
            } else {
                $model = new LapPurchaseRequisitionOutstandingBarangView;
                $dateFilter = 'create_date';

                $options = [
                        "titleStyle" => [
                        "fontSize" => 11,
                        "alignment" => "left"
                    ],
                    "subTitle" => "Non-Medical PR Outstanding Report",
                    "customFormatCode" => [
                        [
                            'selectColumn' => 'C',
                            'formatCode' => 'datetime'
                        ],
                        [
                            'selectColumn' => 'D',
                            'formatCode' => 'datetime'
                        ]
                    ],
                ];
            }

            $query = $model::find();

            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            if (isset($_GET['advanced-filter'])) {
                $advancedFilter = $_GET['advanced-filter'];

                if (isset($advancedFilter['tgl_pr_awal']) &&
                    isset($advancedFilter['tgl_pr_akhir'])) {
                    $start = $advancedFilter['tgl_pr_awal'];
                    $end = $advancedFilter['tgl_pr_akhir'];
                }
            } else {
                $advancedFilter[$dateFilter] = date('d-m-Y', strtotime($start)) . ' - ' . date('d-m-Y', strtotime($end));
            }

            $query->andWhere(['between', $dateFilter, $start, $end]);
            $query->orderBy([$dateFilter => SORT_DESC, 'no_pr' => SORT_ASC]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            if(isset($advancedFilter)) {
                $header = $model->setHeaderExcel($advancedFilter);
            } else {
                $header = [];
            }

            $result = $model->mappingDataExcel($query);
            $filePath = DocoHelpers::exportExcel($title, $result, $header, $options, [], [], true);
            $filePath->save('php://output');
            die;
        } catch (\Yii\db\Exception $e) {
            return $e->getMessage();
        } catch (\Exception $e){
            return $e->getMessage();
        }
    }
}
