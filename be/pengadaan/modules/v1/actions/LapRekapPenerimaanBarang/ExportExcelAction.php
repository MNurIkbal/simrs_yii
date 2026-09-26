<?php

/**
 * @author : Bambang Hermawan (bambang.hermawan@sirs.com)
 * Powered by Sirs
 */

namespace app\modules\v1\actions\LapRekapPenerimaanBarang;

use Yii;
use yii\base\Action;
use GuzzleHttp\Exception\RequestException;
use Doco\components\DocoHelpers;
use app\modules\v1\models\LapRekapPenerimaanBarangView;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use app\modules\v1\models\Supplier;

class ExportExcelAction extends Action {
    public function run() {
        try {
            $title = 'Laporan Rekap Penerimaan Barang';
            $request = Yii::$app->request;

            $model = new LapRekapPenerimaanBarangView;
            $query = $model::find();

            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            
            if (isset($_GET['advanced-filter'])) {
                $advancedFilter = $_GET['advanced-filter'];

                // Tanggal Penerimaan
                if (isset($advancedFilter['tgl_penerimaan_awal']) && isset($advancedFilter['tgl_penerimaan_akhir'])) {
                    $start = date('Y-m-d H:i:s', strtotime($advancedFilter['tgl_penerimaan_awal']. ' 00:00:00'));
                    $end   = date('Y-m-d H:i:s', strtotime($advancedFilter['tgl_penerimaan_akhir'] . ' 23:59:59'));

                    unset($_GET['advanced-filter']['tgl_penerimaan_awal']);
                    unset($_GET['advanced-filter']['tgl_penerimaan_akhir']);
                } else {
                    $advancedFilter['tgl_penerimaan'] = date('d-M-Y', strtotime($start)) . ' - ' . date('d-M-Y', strtotime($end));
                }

                // Nama Supplier
                if (isset($advancedFilter['supplier_id']) && $advancedFilter['supplier_id'] != 0 ) {
                    $query->andWhere(['in', 'supplier_id', array_filter(explode(',', $advancedFilter['supplier_id']))]);
                    $supplier = Supplier::find()
                        ->select(['supplier_nama'])
                        ->andWhere(['in', 'supplier_id', array_filter(explode(',', $advancedFilter['supplier_id']))])
                        ->all();

                    foreach($supplier as $suppliers) {
                        $new_arr[] = $suppliers->supplier_nama;
                    }

                    $res_arr = implode(',', $new_arr);
                    $advancedFilter['supplier_nama'] = $res_arr;
                    
                    unset($_GET['advanced-filter']['supplier_id']);
                } else {
                    $advancedFilter['supplier_nama'] = '-';
                }

                // No PO
                if (isset($advancedFilter['nomor_po'])) {
                    $advancedFilter['nomor_po'] = $advancedFilter['nomor_po'];
                } else {
                    $advancedFilter['nomor_po'] = '-';
                }

                // No Penerimaan
                if (isset($advancedFilter['no_penerimaan'])) {
                    $advancedFilter['no_penerimaan'] = $advancedFilter['no_penerimaan'];
                } else {
                    $advancedFilter['no_penerimaan'] = '-';
                }
            } else {
                $advancedFilter['tgl_penerimaan'] = date('d-M-Y', strtotime($start)) . ' - ' . date('d-M-Y', strtotime($end));
                $advancedFilter['supplier_nama']  = '-';
                $advancedFilter['nomor_po']       = '-';
                $advancedFilter['no_penerimaan']  = '-';
            }

            $query->andWhere(['between', 'tgl_penerimaan', $start, $end]);
            $query->orderBy(['tgl_penerimaan' => SORT_DESC]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            if (isset($advancedFilter)) {
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