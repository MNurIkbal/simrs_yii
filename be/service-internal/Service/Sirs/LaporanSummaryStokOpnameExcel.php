<?php 

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\LaporanSumStokOpnameView;
use Integrasi\Service\Sirs\Models\KonfigFarmasi;
use Integrasi\Components\DocoRestActiveFilter;
use Integrasi\Components\DocoConstants;


class LaporanSummaryStokOpnameExcel extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $data = $this->loadData()->asArray()->all();
        $cacheFiles = Yii::$app->cacheFiles;
        $row = $tmpCache = [];
        $no = 1;
        $prefix = 0;
        $new_arr = $result = [];
        $noData = 1;
        $totalSystemStockQty = $totalPhysicalStockQty = $totalVarianceQty = $totalOpeningCost = $totalEndingCost = $totalSelisihCost = 0;

        $implementasi = KonfigFarmasi::find()->select(['is_tgl_implementasi_sesuai_verif'])->asArray()->one();

        foreach($data as $key => $item) {
            $totalSystemStockQty += $item['system_stock_qty'];
            $totalPhysicalStockQty += $item['physical_stock_qty'];
            $totalVarianceQty += $item['variance_qty'];
            $totalOpeningCost += $item['opening_total_batch_cost'];
            $totalEndingCost += $item['ending_total_batch_cost'];
            $totalSelisihCost += $item['selisih_batch_cost'];

            $item['no'] = $noData++;
            $store = $item['store'];
            $new_arr[$store][0] = [
                "no" => "",
                "store"=> 'Summary ' . $store,
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
                "system_stock_qty"=> (isset($new_arr[$item['store']][0]['system_stock_qty']) ? $new_arr[$item['store']][0]['system_stock_qty'] : 0) + $item['system_stock_qty'],
                "physical_stock_qty"=> (isset($new_arr[$item['store']][0]['physical_stock_qty']) ? $new_arr[$item['store']][0]['physical_stock_qty'] : 0) + $item['physical_stock_qty'],
                "variance_qty"=> (isset($new_arr[$item['store']][0]['variance_qty']) ? $new_arr[$item['store']][0]['variance_qty'] : 0) + $item['variance_qty'],
                "opening_total_batch_cost"=> isset($new_arr[$item['store']][0]['opening_total_batch_cost']) ? $new_arr[$item['store']][0]['opening_total_batch_cost'] : 0 + $item['opening_total_batch_cost'],
                "ending_total_batch_cost"=> isset($new_arr[$item['store']][0]['ending_total_batch_cost']) ? $new_arr[$item['store']][0]['ending_total_batch_cost'] : + $item['ending_total_batch_cost'],
                "selisih_batch_cost"=> isset($new_arr[$item['store']][0]['selisih_batch_cost']) ? $new_arr[$item['store']][0]['selisih_batch_cost'] : 0 + $item['selisih_batch_cost'],
                "tglformulir"=> "",
                "noformulir"=> "",
                "tgl_implementasi"=>"",
            ];
            
            $new_arr[$store][] = $item;

            $grandTotal = [
                "no" => "",
                "store"=> 'GRAND TOTAL ',
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
                "system_stock_qty"=> $totalSystemStockQty,
                "physical_stock_qty"=> $totalPhysicalStockQty,
                "variance_qty"=> $totalVarianceQty,
                "opening_total_batch_cost"=> $totalOpeningCost,
                "ending_total_batch_cost"=> $totalEndingCost,
                "selisih_batch_cost"=> $totalSelisihCost,
                "tglformulir"=> "",
                "noformulir"=> "",
                "tgl_implementasi"=>"",
            ];
            
            if($key + 1 == count($data)) {
                array_push($new_arr[$store], $grandTotal);
            }
        }
        
        if(!empty($new_arr)) {
            $result = call_user_func_array('array_merge', $new_arr);
        }
        
        foreach ($result as $res) 
        {
            $no1=0;
            $weighted_average = ($res['weighted_average'] == 0 || $res['weighted_average'] == "") ? "" : $res['weighted_average'];
            $tmp[1]  = $res['no'];
            $tmp[2]  = $res['store'];
            $tmp[3]  = $res['noformulir'];
            $tmp[4]  = !empty($res['tgl_so']) ? date("j M Y H:i:s", strtotime($res['tgl_so'])) : '';
            $tmp[5]  = !empty($res['tgl_validasi']) ? date("j M Y H:i:s", strtotime($res['tgl_validasi'])) : '';
            if(!$implementasi['is_tgl_implementasi_sesuai_verif']) {
                $tmp[6]  = !empty($res['tgl_implementasi']) ? date("j M Y H:i:s", strtotime($res['tgl_so'])) : '';;
                $no1=1;
            }
            $tmp[6+$no1]  = $res['validasi_oleh'];
            $tmp[7+$no1]  = $res['jenis_obat'];
            $tmp[8+$no1]  = $res['kode_obat'];
            $tmp[9+$no1]  = $res['nama_obat'];
            $tmp[10+$no1]  = $res['satuan_kecil'];
            $tmp[11+$no1]  = $res['konversi'];
            $tmp[12+$no1]  = $weighted_average;
            $tmp[13+$no1]  = $res['system_stock_qty'];
            $tmp[14+$no1]  = $res['physical_stock_qty'];
            $tmp[15+$no1]  = $res['variance_qty'];
            $tmp[16+$no1]  = $res['opening_total_batch_cost'];
            $tmp[17+$no1]  = $res['ending_total_batch_cost'];
            $tmp[18+$no1]  = $res['selisih_batch_cost'];
            $row[] = $tmp;
            $tmpCache[] = $tmp;
            if (($no%50) == 0) {
                Yii::$app->redis->executeCommand('PUBLISH', [
                    'channel' => 'export-excel:'.$this->unique_str,
                    'message' => json_encode(['unique_process' => $this->unique_str]),
                ]);
                $cacheFiles->set($this->unique_str .'-'. $prefix, $tmpCache);
                $prefix++;
                $tmpCache = [];
            }
            $no++;
        }
        $cacheFiles->set($this->unique_str .'-'. $prefix, $tmpCache);
        return json_encode([
            'service' => 'Sirs-LaporanSummaryStokOpnameExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    private function loadData()
    {
        $request = $this->filter;

        $model = new LaporanSumStokOpnameView;
        $query = $model::find();

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        
        if (isset($request['advanced-filter'])) {
            $advancedFilter = $request['advanced-filter'];
            if(isset($advancedFilter['tglformulir'])) {
                $explode = explode(" - ", $advancedFilter['tglformulir']);
                $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($request['advanced-filter']['tglformulir']);
            }
        }
        
        $query->andWhere(['between', 'tglformulir', $start, $end]);
        $query->orderBy(['store' => SORT_ASC, 'nama_obat' => SORT_ASC, 'tglformulir' => SORT_DESC]);

        return DocoRestActiveFilter::advancedFilter($model, $query, $request);
    }
}
