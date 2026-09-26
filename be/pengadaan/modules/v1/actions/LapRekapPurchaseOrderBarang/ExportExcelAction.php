<?php

/**
 * @author : Bambang Hermawan (bambang.hermawan@sirs.co.id)
 * Powered by Sirs
 */

namespace app\modules\v1\actions\LapRekapPurchaseOrderBarang;

use Yii;
use yii\base\Action;
use GuzzleHttp\Exception\RequestException;
use Doco\components\DocoHelpers;
use app\modules\v1\models\LapRekapPurchaseOrderBarangView;
use app\modules\v1\models\Supplier;
use Doco\components\DocoRestActiveFilter;

class ExportExcelAction extends Action {
    public function run()
    {
        try {
            $title = 'Laporan Rekap Purchase Order Barang';
            $request = Yii::$app->request;

            $model = new LapRekapPurchaseOrderBarangView;
            $query = $model::find();

            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            if (isset($_GET['advanced-filter'])) {
                $advancedFilter = $_GET['advanced-filter'];

                if (isset($advancedFilter['tgl_po_awal']) && isset($advancedFilter['tgl_po_akhir'])) {
                    $start = date('Y-m-d H:i:s', strtotime($advancedFilter['tgl_po_awal']. ' 00:00:00'));
                    $end   = date('Y-m-d H:i:s', strtotime($advancedFilter['tgl_po_akhir'] . ' 23:59:59'));
                    unset($_GET['advanced-filter']['tgl_po_awal']);
                    unset($_GET['advanced-filter']['tgl_po_akhir']);
                } else {
                    $advancedFilter['tgl_po'] = date('d-m-Y', strtotime($start)) . ' - ' . date('d-m-Y', strtotime($end)); 
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
                
                if(!isset($advancedFilter['no_po'])){
                    $advancedFilter['no_po'] = '-';
                }
                
            } else {
                $advancedFilter['tgl_po'] = date('d-m-Y', strtotime($start)) . ' - ' . date('d-m-Y', strtotime($end));
                $advancedFilter['supplier_nama'] = '-';
                $advancedFilter['no_po'] = '-';
            }
            
            $query->andWhere(['between', 'tgl_po', $start, $end]);
            $query->orderBy(['tgl_po' => SORT_DESC]);

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
                        'selectColumn' => 'E',
                        'formatCode' => 'date',
                    ],
                    [
                        'selectColumn' => 'F',
                        'formatCode' => 'date',
                    ],
                    [
                        'selectColumn' => 'J',
                        'formatCode' => 'number',
                    ],
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