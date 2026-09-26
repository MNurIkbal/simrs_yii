<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace app\modules\v1\actions\LapPenerimaanBarang;

use yii\base\Action;
use Doco\components\DocoHelpers;
use app\modules\v1\models\LaporanPenerimaanBarangView;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Supplier;
use app\modules\v1\models\Payterm;

class ExportExcelAction extends Action {
    public function run() {
        try {
            $title = 'Laporan Penerimaan Barang';
            $model = new LaporanPenerimaanBarangView;
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

                if (isset($advancedFilter['payterm_id'])) {
                    $payterm = Payterm::find()
                        ->select(['payterm_nama'])
                        ->where(['payterm_id' => $advancedFilter['payterm_id']])
                        ->one();

                    $advancedFilter['payterm_nama'] = $payterm->payterm_nama;
                }
            } else {
                $advancedFilter['tgl_penerimaan'] = date('d-m-Y', strtotime($start)) . ' - ' . date('d-m-Y', strtotime($end));
            }

            $query->andWhere(['between', 'tgl_penerimaan', $start, $end]);
            $query->orderBy(['tgl_penerimaan' => SORT_ASC, 'no_penerimaan' => SORT_ASC, 'barang_nama' => SORT_ASC]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            if(isset($advancedFilter)) {
                $header = $model->setHeaderExcel($advancedFilter);
            } else {
                $header = [];
            }

            $options = [
                "titleStyle" => [
                    "fontSize" => 11,
                    "alignment" => "left"
                ],
                "customFormatCode" => [
                    [
                        'selectColumn' => 'E',
                        'formatCode' => 'datetime'
                    ],
                    [
                        'selectColumn' => 'I',
                        'formatCode' => 'datetime'
                    ],
                    [
                        'selectColumn' => 'J',
                        'formatCode' => 'datetime'
                    ],
                    [
                        'selectColumn' => 'Z',
                        'formatCode' => 'datetime'
                    ],
                    [
                        'selectColumn' => 'AD',
                        'formatCode' => 'datetime'
                    ],
                    [
                        'selectColumn' => 'AG',
                        'formatCode' => 'shortdate'
                    ],
                    [
                        'selectColumn' => 'AF',
                        'formatCode' => 'date'
                    ],
                    ['selectColumn' => 'V'],
                    ['selectColumn' => 'W'],
                    ['selectColumn' => 'X'],
                    ['selectColumn' => 'Y'],
                    ['selectColumn' => 'Z'],
                    ['selectColumn' => 'AA'],
                    ['selectColumn' => 'AB']
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
