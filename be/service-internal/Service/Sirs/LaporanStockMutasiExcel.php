<?php 

namespace Integrasi\Service\Sirs;

use Yii;

use Integrasi\Service\Sirs\Models\LaporanStockMutasiFn;
use Integrasi\Components\DocoRestActiveFilter;

class LaporanStockMutasiExcel extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $data = $this->getDataExcel()->asArray()->all();
        
        $cacheFiles = Yii::$app->cacheFiles;
        $row = $tmpCache = [];
        $no = 1;
        $prefix = 0;
        foreach ($data as $value) 
        {
            $tmp[1]  = $no;
            $tmp[2]  = !empty($value['obatalkes_kode']) ? $value['obatalkes_kode'] : '';
            $tmp[3]  = !empty($value['obatalkes_nama']) ? $value['obatalkes_nama'] : '';
            $tmp[4]  = !empty($value['jenisobatalkes_nama']) ? $value['jenisobatalkes_nama'] : '';
            $tmp[5]  = !empty($value['manufaktur_nama']) ? $value['manufaktur_nama'] : '';
            $tmp[6]  = !empty($value['ruangan_nama']) ? $value['ruangan_nama'] : '';
            $tmp[7]  = !empty($value['uom']) ? $value['uom'] : '';
            $tmp[8]  = !empty($value['hna']) ? $value['hna'] : '';
            $tmp[9]  = !empty($value['qty_total_awal']) ? $value['qty_total_awal'] : '';
            $tmp[10] = !empty($value['total_nilai_awal']) ? $value['total_nilai_awal'] : '';
            $tmp[11] = !empty($value['total_qty_diterima']) ? $value['total_qty_diterima'] : '';
            $tmp[12] = !empty($value['total_nilai_diterima']) ? $value['total_nilai_diterima'] : '';
            $tmp[13] = !empty($value['total_qty_keluar']) ? $value['total_qty_keluar'] : '';
            $tmp[14] = !empty($value['total_nilai_keluar']) ? $value['total_nilai_keluar'] : '';
            $tmp[15] = !empty($value['total_qty_akhir']) ? $value['total_qty_akhir'] : '';
            $tmp[16] = !empty($value['total_nilai_akhir']) ? $value['total_nilai_akhir'] : '';
            $tmp[17] = !empty($value['turn_over']) ? $value['turn_over'] : '';

            $tmpCache[] = $tmp;
            if (($no%50) == 0) 
            {
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
            'service' => 'Sirs-LaporanStockMutasiExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    public function getDataExcel()
    {
        $request = $this->filter;

        $start = date('Y-m-d');
        $end = date('Y-m-d');
        $model = new LaporanStockMutasiFn;
        $query = LaporanStockMutasiFn::getData($start,$end);

        if (isset($request['advanced-filter'])) {
            $advancedFilter = $request['advanced-filter'];
            if(isset($request['advanced-filter']['tanggal_inventory'])){
                $explode = explode(" - ", $advancedFilter['tanggal_inventory']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }
                $query = $model::getData($start,$end);
            }
        }

        return DocoRestActiveFilter::advancedFilter($model,$query,$request);
    }
}