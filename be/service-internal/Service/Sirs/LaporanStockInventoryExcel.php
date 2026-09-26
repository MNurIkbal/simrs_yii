<?php 

namespace Integrasi\Service\Sirs;

use Yii;

use Integrasi\Service\Sirs\Models\LaporanStockInventoryFn;
use Integrasi\Components\DocoRestActiveFilter;

class LaporanStockInventoryExcel extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $data = $this->getDataExcel()->asArray()->all();
        // $data = $this->data;
        $cacheFiles = Yii::$app->cacheFiles;
        $row = $tmpCache = [];
        $no = 1;
        $prefix = 0;
        foreach ($data as $value) 
        {
            $tmp[1]  = $no;
            $tmp[2]  = !empty($value['ruangan_nama']) ? $value['ruangan_nama'] : '-';
            $tmp[3]  = !empty($value['obatalkes_kode']) ? $value['obatalkes_kode'] : '-';
            $tmp[4]  = !empty($value['obatalkes_nama']) ? $value['obatalkes_nama'] : '-';
            $tmp[5]  = !empty($value['jenis_obat']) ? $value['jenis_obat'] : '-';
            $tmp[6]  = !empty($value['is_generik']) ? $value['is_generik'] : '-';
            $tmp[7]  = !empty($value['is_oral']) ? $value['is_oral'] : '-';
            $tmp[8]  = !empty($value['satuan_kecil']) ? $value['satuan_kecil'] : '-';
            $tmp[9]  = !empty($value['satuan_besar']) ? $value['satuan_besar'] : '-';
            $tmp[10]  = !empty($value['nilai_konv']) ? $value['nilai_konv'] : '-';
            $tmp[11]  = !empty($value['qty_satuankecil']) ? $value['qty_satuankecil'] : '-';
            $tmp[12]  = !empty($value['qty_satuanbesar']) ? $value['qty_satuanbesar'] : '-';
            $tmp[13]  = !empty($value['wa_satuan_kecil']) ? $value['wa_satuan_kecil'] : '-';
            $tmp[14]  = !empty($value['wa_satuan_besar']) ? $value['wa_satuan_besar'] : '-';
            $tmp[15]  = !empty($value['total_satuankecil']) ? $value['total_satuankecil'] : '-';
            $tmp[16]  = !empty($value['total_satuanbesar']) ? $value['total_satuanbesar'] : '-';
            $tmp[17]  = !empty($value['baseprice_kecil']) ? $value['baseprice_kecil'] : '-';
            $tmp[18]  = !empty($value['baseprice_besar']) ? $value['baseprice_besar'] : '-';
            $tmp[19]  = !empty($value['total_satuankecil_netto']) ? $value['total_satuankecil_netto'] : '-';
            $tmp[20]  = !empty($value['total_satuanbesar_netto']) ? $value['total_satuanbesar_netto'] : '-';

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
            'service' => 'Sirs-LaporanStockInventoryExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    public function getDataExcel()
    {
        $request = $this->filter;

        $date = date('Y-m-d');
        $model = new LaporanStockInventoryFn;
        $query = LaporanStockInventoryFn::getData($date);

        if (isset($request['advanced-filter'])) {
            if(isset($request['advanced-filter']['tanggal_inventory'])){
                $date = date('Y-m-d', strtotime($request['advanced-filter']['tanggal_inventory']));
                $query = $model::getData($date);
            }
        }

        return DocoRestActiveFilter::advancedFilter($model,$query,$request);
    }
}