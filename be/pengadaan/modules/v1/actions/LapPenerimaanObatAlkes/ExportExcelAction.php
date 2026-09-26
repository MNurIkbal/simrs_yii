<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace app\modules\v1\actions\LapPenerimaanObatAlkes;

use yii\base\Action;
use Doco\components\DocoHelpers;
use app\modules\v1\models\LaporanPenerimaanObatAlkesView;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Supplier;

class ExportExcelAction extends Action {
    public function run() {
        try {
            $title = 'Laporan Penerimaan Obat Alkes';
            $model = new LaporanPenerimaanObatAlkesView;
            $query = $model::find();

            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            if (isset($_GET['advanced-filter'])) {
                $advancedFilter = $_GET['advanced-filter'];

                if (isset($advancedFilter['tgl_penerimaan_awal']) &&
                    isset($advancedFilter['tgl_penerimaan_akhir'])) {
                    $start = $advancedFilter['tgl_penerimaan_awal'];
                    $end = $advancedFilter['tgl_penerimaan_akhir'];
                }

                if (isset($advancedFilter['supplier_id'])) {
                    $supplier = Supplier::find()
                        ->select(['supplier_nama'])
                        ->where(['supplier_id' => $advancedFilter['supplier_id']])
                        ->one();

                    $advancedFilter['supplier_nama'] = $supplier->supplier_nama;
                }
            } else {
                $advancedFilter['tgl_penerimaan'] = date('d-m-Y', strtotime($start)) . ' - ' . date('d-m-Y', strtotime($end));
            }

            $query->andWhere(['between', 'tgl_penerimaan', $start, $end]);
            $query->orderBy(['tgl_penerimaan' => SORT_DESC, 'no_penerimaan' => SORT_ASC]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            if(isset($advancedFilter)) {
                $header = $model->setHeaderExcel($advancedFilter);
            } else {
                $header = [];
            }

            $options = [
                "skipIncrement" => true,
                "customFormatCode" => [
                    [
                        'selectColumn' => 'D',
                        'formatCode' => 'datetime'
                    ],
                    [
                        'selectColumn' => 'H',
                        'formatCode' => 'datetime'
                    ],
                    [
                        'selectColumn' => 'I',
                        'formatCode' => 'datetime'
                    ],
                    [
                        'selectColumn' => 'AC',
                        'formatCode' => 'datetime'
                    ],
                    [
                        'selectColumn' => 'AF',
                        'formatCode' => 'shortdate'
                    ],
                    ['selectColumn' => 'W','formatCode' => 'number'],
                    ['selectColumn' => 'X','formatCode' => 'number'],
                    ['selectColumn' => 'Y','formatCode' => 'number'],
                    ['selectColumn' => 'Z','formatCode' => 'number'],
                    ['selectColumn' => 'AA','formatCode' => 'number']
                ],
            ];

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
