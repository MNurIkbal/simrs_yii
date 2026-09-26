<?php 

namespace Integrasi\Service\Sirs;

use Yii;

use Integrasi\Service\Sirs\Models\InfoStokBarang;
use Integrasi\Components\DocoRestActiveFilter;

class InformasiStokBarangExcel extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $data = $this->loadData();
        $cacheFiles = Yii::$app->cacheFiles;
        $row = $tmpCache = [];
        $no = 1;
        $prefix = 0;
        foreach ($data as $value)
        {
            $tmp[1]  = $no;
            $tmp[2]  = !empty($value['ruangan_nama']) ? $value['ruangan_nama'] : '';
            $tmp[3]  = !empty($value['barang_nama']) ? $value['barang_nama'] : '';
            $tmp[4]  = !empty($value['barang_kode']) ? $value['barang_kode'] : '';
            $tmp[5]  = !empty($value['kelompokbarang_nama']) ? $value['kelompokbarang_nama'] : '';
            $tmp[6]  = !empty($value['qty_dipesan']) ? $value['qty_dipesan'] : '';
            $tmp[7]  = !empty($value['qty_tersedia']) ? $value['qty_tersedia'] : '';
            $tmp[8]  = !empty($value['qty_stok']) ? $value['qty_stok'] : '';

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
            'service' => 'Sirs-InformasiStokBarangExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str,
        ]);
    }

    private function loadData()
    {
        $request = $this->filter;
        $model = new InfoStokBarang;
        $query = $model::find(true);

        $between = false;
        $start = $end = date('Y-m-d');

        if(isset($request['advanced-filter']['periodestok_nama'])) {
            $explode = explode(" - ", $request['advanced-filter']['periodestok_nama']);
            if(count($explode) == 2) {
                $start = date('Y-m-d', strtotime($explode[0]));
                $end = date('Y-m-d', strtotime($explode[1]));
            }
            unset($request['advanced-filter']['periodestok_nama']); // Unset Advanced Filter  date range
            $between = true;
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query, $request);
        return $query->asArray()->all();
    }
}