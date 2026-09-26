<?php

/**
 * @author : Bambang Hermawan (bambang.hermawan@sirs.com)
 * Powered by Sirs
 */

namespace app\modules\v1\actions\LapRekapPenerimaanObat;

use Yii;
use yii\base\Action;
use GuzzleHttp\Exception\RequestException;
use Doco\components\DocoHelpers;
use app\modules\v1\models\LaporanRekapPenerimaanObatView;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use app\modules\v1\models\Supplier;
use app\modules\v1\models\Payterm;

class ExportExcelAction extends Action {
    public function run() {
        try {
            $title = 'Laporan Rekap Penerimaan Obat';
            $request = Yii::$app->request;

            $model = new LaporanRekapPenerimaanObatView;
            $query = $model::find();

            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            if (isset($_GET['advanced-filter'])) {
                $advancedFilter = $_GET['advanced-filter'];

                if (isset($advancedFilter['tgl_penerimaan_awal']) && isset($advancedFilter['tgl_penerimaan_akhir'])) {
                    $start = date('Y-m-d H:i:s', strtotime($advancedFilter['tgl_penerimaan_awal']. ' 00:00:00'));
                    $end   = date('Y-m-d H:i:s', strtotime($advancedFilter['tgl_penerimaan_akhir'] . ' 23:59:59'));
                    unset($_GET['advanced-filter']['tgl_penerimaan_awal']);
                    unset($_GET['advanced-filter']['tgl_penerimaan_akhir']);
                } else {
                    $advancedFilter['tgl_penerimaan'] = date('d-m-Y', strtotime($start)) . ' - ' . date('d-m-Y', strtotime($end));
                }

                if (isset($advancedFilter['supplier_id']) && $advancedFilter['supplier_id'] != 0 ) {
                    $query->andWhere(['in', 'supplier_id', array_filter(explode(',', $advancedFilter['supplier_id']))]);
                    $supplier = Supplier::find()
                        ->select(['supplier_nama'])
                        ->andWhere(['in', 'supplier_id', array_filter(explode(',', $advancedFilter['supplier_id']))])
                        ->all();
                    foreach($supplier as $suppliers)
                    {
                        $new_arr[] = $suppliers->supplier_nama;
                    }
                    $res_arr = implode(',',$new_arr);
                    $advancedFilter['supplier_nama'] = $res_arr;
                    unset($_GET['advanced-filter']['supplier_id']);
                } else {
                    $advancedFilter['supplier_nama'] = '-';
                }

                if (isset($advancedFilter['payterm_id']) && $advancedFilter['payterm_id'] != 0 ) {
                    $query->andWhere(['payterm_id' => $advancedFilter['payterm_id']]);
                    $payment = Payterm::find()
                        ->select(['payterm_nama'])
                        ->where(['payterm_id' => $advancedFilter['payterm_id']])
                        ->all();
                    foreach ($payment as $payments) {
                        $advancedFilter['payterm_nama'] = $payments->payterm_nama;
                    }
                    unset($_GET['advanced-filter']['payterm_id']);
                } else {
                    $advancedFilter['payterm_nama'] = '-';
                }

                if(!isset($advancedFilter['nomor_po'])){
                    $advancedFilter['nomor_po'] = '-';
                }

                if(!isset($advancedFilter['no_penerimaan'])){
                    $advancedFilter['no_penerimaan'] = '-';
                }
                
            } else {
                $advancedFilter['tgl_penerimaan'] = date('d-m-Y', strtotime($start)) . ' - ' . date('d-m-Y', strtotime($end));
                $advancedFilter['supplier_nama'] = '-';
                $advancedFilter['nomor_po'] = '-';
                $advancedFilter['no_penerimaan'] = '-';
                $advancedFilter['payterm_nama'] = '-';
            }
            

            $query->andWhere(['between', 'tgl_penerimaan', $start, $end]);
            $query->orderBy(['tgl_penerimaan' => SORT_DESC]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            if(isset($advancedFilter)) {
                $header = $model->setHeaderExcel($advancedFilter);
            } else {
                $header = [];
            }

            $options = [
                "titleStyle" => [
                    "fontSize" => 15,
                    "alignment" => "left",
                    "fontWeight" => 600,
                ],
                "customFormatCode" => [
                    [
                        'selectColumn' => 'D',
                        'formatCode' => 'date'
                    ],
                    [
                        'selectColumn' => 'H',
                        'formatCode' => 'date'
                    ],
                    [
                        'selectColumn' => 'I',
                        'formatCode' => 'date'
                    ],
                    [
                        'selectColumn' => 'M',
                        'formatCode' => 'number'
                    ]
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
