<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace app\modules\v1\actions\LaporanSummaryStokOpname;

use Yii;
use yii\base\Action;
use GuzzleHttp\Exception\RequestException;
use Doco\components\DocoHelpers;
use app\modules\v1\models\LaporanSumStokOpnameView;
use Doco\components\DocoRestActiveFilter;
use yii\helpers\ArrayHelper;

class ExportExcelAction extends Action {
    public function run() {
        try {
            $title = 'Laporan Summary SO';
            $request = Yii::$app->request;

            $model = new LaporanSumStokOpnameView;
            $query = $model::find();

            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            if (isset($_GET['advanced-filter'])) {
                $advancedFilter = $_GET['advanced-filter'];

                if (isset($advancedFilter['tglformulir_awal']) &&
                    isset($advancedFilter['tglformulir_akhir'])) {
                    $start = $advancedFilter['tglformulir_awal'];
                    $end = $advancedFilter['tglformulir_akhir'];
                }
            }

            $query->andWhere(['between', 'tglformulir', $start, $end]);
            $query->orderBy(['tglformulir' => SORT_DESC, 'no_so' => SORT_ASC, 'store' => SORT_ASC]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            $new_arr = $result = [];
            foreach($query->asArray()->all() as $key => $item) {
                if(!isset($new_arr[$item['store']][0])) {
                    $new_arr[$item['store']][0] = [
                        "store"=> $item['store'],
                        "no_so"=> "",
                        "tgl_so"=> "",
                        "tgl_validasi"=> "",
                        "validasi_oleh"=> "",
                        "jenis_obat"=> "",
                        "kode_obat"=> "",
                        "nama_obat"=> "",
                        "satuan_kecil"=> "",
                        "konversi"=> "",
                        "weighted_average"=> 0,
                        "system_stock_qty"=> 0,
                        "physical_stock_qty"=> 0,
                        "variance_qty"=> 0,
                        "opening_total_batch_cost"=> 0,
                        "ending_total_batch_cost"=> 0,
                        "selisih_batch_cost"=> 0,
                        "tglformulir"=> "",
                        "noformulir"=> "",
                    ];
                }
                
                $new_arr[$item['store']][] = $item;

                $new_arr[$item['store']][0] = [
                    "store"=> $item['store'],
                    "no_so"=> "",
                    "tgl_so"=> "",
                    "tgl_validasi"=> "",
                    "validasi_oleh"=> "",
                    "jenis_obat"=> "",
                    "kode_obat"=> "",
                    "nama_obat"=> "",
                    "satuan_kecil"=> "",
                    "konversi"=> "",
                    "weighted_average"=> 0,
                    "system_stock_qty"=> $new_arr[$item['store']][0]['system_stock_qty'] + $item['system_stock_qty'],
                    "physical_stock_qty"=> $new_arr[$item['store']][0]['physical_stock_qty'] + $item['physical_stock_qty'],
                    "variance_qty"=> $new_arr[$item['store']][0]['variance_qty'] + $item['variance_qty'],
                    "opening_total_batch_cost"=> $new_arr[$item['store']][0]['opening_total_batch_cost'] + $item['opening_total_batch_cost'],
                    "ending_total_batch_cost"=> $new_arr[$item['store']][0]['ending_total_batch_cost'] + $item['ending_total_batch_cost'],
                    "selisih_batch_cost"=> $new_arr[$item['store']][0]['selisih_batch_cost'] + $item['selisih_batch_cost'],
                    "tglformulir"=> "",
                    "noformulir"=> "",
                ];
            }

            if(!empty($new_arr)) {
                $result = call_user_func_array('array_merge', $new_arr);
            }

            if(isset($advancedFilter)) {
                $header = $model->setHeaderExcel($advancedFilter);
            } else {
                $header = [];
            }

            $options = [
                "skipIncrement" => true,
                "customFormatCode" => [
                    [
                        'selectColumn' => 'C',
                        'formatCode' => 'date'
                    ],
                    [
                        'selectColumn' => 'D',
                        'formatCode' => 'date'
                    ],
                    ['selectColumn' => 'J'],
                    ['selectColumn' => 'K'],
                    ['selectColumn' => 'L'],
                    ['selectColumn' => 'M'],
                    ['selectColumn' => 'N'],
                    ['selectColumn' => 'O'],
                    ['selectColumn' => 'P'],
                    ['selectColumn' => 'Q'],
                    ['selectColumn' => 'R']
                ],
            ];

            $mappingData = $model->mappingDataExcel($result);

            $filePath = DocoHelpers::exportExcel($title, $mappingData, $header, $options, [], [], true);
            $filePath->save('php://output');
            die;
        } catch (\Yii\db\Exception $e) {
            return $e->getMessage();
        } catch (\Exception $e){
            return $e->getMessage();
        }
    }
}
