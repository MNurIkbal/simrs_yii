<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\InfoPurchaseOrder;

use Yii;
use yii\base\Action;
use Doco\components\DocoSpout;
use Doco\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;
use GuzzleHttp\Exception\RequestException;
use app\modules\v1\models\LaporanAnalisaPurchaseOrderView;
use yii\helpers\ArrayHelper;

class ExportExcelAnalisaPOAction extends Action {
    private function dateFilterWithDefault(&$query, &$advanced_filter, $tgl) {
        $filter_tgl = ArrayHelper::getValue($advanced_filter, $tgl);
        $advanced_filter[$tgl] = $filter_tgl;
        $filter_tgl = DocoHelpers::parsingRangeDate($filter_tgl);
        $start = $filter_tgl['startDate'];
        $end = $filter_tgl['endDate'];
        $query->andWhere(['between', $tgl, $start, $end]);
    }

    public function run() {
        try {
            $title = 'Laporan Analisa Purchase Order';
            $request = Yii::$app->request;
            $advanced_filter = $request->get('advanced-filter');
            $model = new LaporanAnalisaPurchaseOrderView;
            $query = $model::find();
            $dateKey = ['tgl_po'];
            if(count($advanced_filter) > 0) {
                $this->controller->dateFilter($query, $request, $dateKey);
            }
            if (isset($advanced_filter['status_po_kondisi']) && !empty($advanced_filter['status_po_kondisi'])){
                $query->andWhere(['ILIKE', 'status_po_kondisi', $advanced_filter['status_po_kondisi']]);
            }
            $options = [
                    "titleStyle" => [
                    "fontSize" => 11,
                    "alignment" => "left"
                ],
                "customFormatCode" => [
                    [
                        'selectColumn' => 'B',
                        'formatCode' => 'general'
                    ],
                    [
                        'selectColumn' => 'C',
                        'formatCode' => 'general'
                    ],
                    [
                        'selectColumn' => 'D',
                        'formatCode' => 'general'
                    ],
                    [
                        'selectColumn' => 'E',
                        'formatCode' => 'general'
                    ],
                    [
                        'selectColumn' => 'F',
                        'formatCode' => 'general'
                    ],
                    [
                        'selectColumn' => 'I',
                        'formatCode' => 'general'
                    ],
                    [
                        'selectColumn' => 'J',
                        'formatCode' => 'general'
                    ],
                    [
                        'selectColumn' => 'K',
                        'formatCode' => 'general'
                    ],
                    [
                        'selectColumn' => 'O',
                        'formatCode' => 'general'
                    ],
                    [
                        'selectColumn' => 'U',
                        'formatCode' => 'general'
                    ],
                    [
                        'selectColumn' => 'W',
                        'formatCode' => 'general'
                    ],
                    [
                        'selectColumn' => 'X',
                        'formatCode' => 'general'
                    ],
                    [
                        'selectColumn' => 'Y',
                        'formatCode' => 'general'
                    ],
                    [
                        'selectColumn' => 'AB',
                        'formatCode' => 'general'
                    ],
                    [
                        'selectColumn' => 'AD',
                        'formatCode' => 'general'
                    ],

                    [
                        'selectColumn' => 'G',
                        'formatCode' => 'datetime'
                    ],
                    [
                        'selectColumn' => 'L',
                        'formatCode' => 'datetime'
                    ],
                    [
                        'selectColumn' => 'M',
                        'formatCode' => 'datetime'
                    ],
                    [
                        'selectColumn' => 'V',
                        'formatCode' => 'datetime'
                    ],
                    [
                        'selectColumn' => 'Z',
                        'formatCode' => 'datetime'
                    ],
                    ['selectColumn' => 'P'],
                    ['selectColumn' => 'S'],
                    ['selectColumn' => 'T']
                ],
            ];
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $advancedFilterHeaderNullable = [
                'tgl_pr' => ArrayHelper::getValue($advanced_filter, 'tgl_pr', '-'),
                'tgl_po' => ArrayHelper::getValue($advanced_filter, 'tgl_po', '-'),
                'nama_obat' => ArrayHelper::getValue($advanced_filter, 'nama_obat', '-'),
                'no_po' => ArrayHelper::getValue($advanced_filter, 'no_po', '-'),
            ];
            $header = !is_null($advanced_filter) ? $model->setHeaderExcel($advancedFilterHeaderNullable) : [];
            $result = $model->mappingDataExcel($query);
            $filePath = DocoHelpers::exportExcel($title, $result, $header, $options, [], [], true);
            $filePath->save('php://output');
            die;
        } catch (\Yii\db\Exception $e) {
            $this->controller->logError($e);
            return $e->getMessage();
        } catch (\Exception $e){
            $this->controller->logError($e);
            return $e->getMessage();
        }
    }
}
