<?php 

namespace Integrasi\Service\Sirs;

use Yii;

use Integrasi\Service\Sirs\Models\LaporanStockInventoryBarangFn;
use Integrasi\Components\DocoRestActiveFilter;

class LaporanStockInventoryBarangExcel extends \Integrasi\Contracts\DocoImplement
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
            $tmp[2]  = !empty($value['ruangan_nama']) ? $value['ruangan_nama'] : '-';
            $tmp[3]  = !empty($value['barang_kode']) ? $value['barang_kode'] : '-';
            $tmp[4]  = !empty($value['barang_nama']) ? $value['barang_nama'] : '-';
            $tmp[5]  = !empty($value['kelompok_barang']) ? $value['kelompok_barang'] : '-';
            $tmp[6]  = !empty($value['subkelompok_nama']) ? $value['subkelompok_nama'] : '-';
            $tmp[7]  = !empty($value['satuan_kecil']) ? $value['satuan_kecil'] : '-';
            $tmp[8]  = !empty($value['satuan_besar']) ? $value['satuan_besar'] : '-';
            $tmp[9]  = !empty($value['nilai_konv']) ? $value['nilai_konv'] : '-';
            $tmp[10]  = !empty($value['qty_satuankecil']) ? $value['qty_satuankecil'] : '-';
            $tmp[11]  = !empty($value['qty_satuanbesar']) ? $value['qty_satuanbesar'] : '-';
            $tmp[12]  = !empty($value['baseprice_kecil']) ? $value['baseprice_kecil'] : '-';
            $tmp[13]  = !empty($value['baseprice_besar']) ? $value['baseprice_besar'] : '-';
            $tmp[14]  = !empty($value['total_satuankecil']) ? $value['total_satuankecil'] : '-';
            $tmp[15]  = !empty($value['total_satuanbesar']) ? $value['total_satuanbesar'] : '-';

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
            'service' => 'Sirs-LaporanStockInventoryBarangExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    public function getDataExcel()
    {
        $request = $this->filter;

        $date = date('Y-m-d');
        $model = new LaporanStockInventoryBarangFn;
        $query = LaporanStockInventoryBarangFn::getData($date);

        if (isset($request['advanced-filter'])) {
            if(isset($request['advanced-filter']['tanggal_inventory'])){
                $date = date('Y-m-d', strtotime($request['advanced-filter']['tanggal_inventory']));
                $query = $model::getData($date);
            }
        }

        return DocoRestActiveFilter::advancedFilter($model,$query,$request);
    }
}