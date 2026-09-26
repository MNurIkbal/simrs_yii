<?php 

namespace Integrasi\Service\Sirs;

use Yii;

use Integrasi\Service\Sirs\Models\LaporanPemakaianBarangView;
use Integrasi\Components\DocoRestActiveFilter;

class LaporanPemakaianBarang extends \Integrasi\Contracts\DocoImplement
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
            $tmp[2]  = !empty($value['ruangan_nama']) ? $value['ruangan_nama'] : '';
            $tmp[3]  = isset($value['tgl_transaksi']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['tgl_transaksi']))) : null;
            $tmp[4]  = !empty($value['no_transaksi']) ? $value['no_transaksi'] : '';
            $tmp[5]  = !empty($value['kelompokbarang_nama']) ? $value['kelompokbarang_nama'] : '';
            $tmp[6]  = !empty($value['barang_kode']) ? $value['barang_kode'] : '';
            $tmp[7]  = !empty($value['barang_nama']) ? $value['barang_nama'] : '';
            $tmp[8]  = !empty($value['qty']) ? $value['qty'] : '';
            $tmp[9]  = !empty($value['satuan_kecil']) ? $value['satuan_kecil'] : '';
            $tmp[10]  = !empty($value['harga_netto']) ? $value['harga_netto'] : '';
            $tmp[11] = !empty($value['total_harga']) ? $value['total_harga'] : '';
            $tmp[12] = !empty($value['user']) ? $value['user'] : '';
            $tmp[13] = !empty($value['catatan']) ? $value['catatan'] : '';

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
            'service' => 'Sirs-LaporanPemakaianBarang',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    public function getDataExcel()
    {
        $request = $this->filter;

        $model = new LaporanPemakaianBarangView;
        $query = $model::find();

        $start = date('Y-m-d');
        $end = date('Y-m-d');

        if (isset($request['advanced-filter'])) {
            $advancedFilter = $request['advanced-filter'];
            if (!empty($advancedFilter['tgl_transaksi'])) {
                $explode = explode(" - ", $advancedFilter['tgl_transaksi']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }
                unset($advancedFilter['tgl_transaksi']);
            }

            if (!empty($advancedFilter['ruangan_nama'])) {
                $query->andWhere(['ruangan_nama' => $advancedFilter['ruangan_nama']]);
            }

            if (!empty($advancedFilter['barang_nama'])) {
                $query->andWhere(['barang_nama' => $advancedFilter['barang_nama']]);
            }

            if (!empty($advancedFilter['barang_kode'])) {
                $query->andWhere(['barang_kode' => $advancedFilter['barang_kode']]);
            }

            if (!empty($advancedFilter['kelompokbarang_nama'])) {
                $query->andWhere(['kelompokbarang_nama' => $advancedFilter['kelompokbarang_nama']]);
            }
        }

        $query->andWhere(['between', 'tgl_transaksi', $start, $end]);
        return DocoRestActiveFilter::advancedFilter($model, $query, $request);
    }
}