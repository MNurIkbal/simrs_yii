<?php

namespace Integrasi\Service\Sirs;

use Yii;
use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoRestActiveFilter;
use Integrasi\Service\Sirs\Models\LaporanPemakaianObatRuanganView;


class LaporanPemakaianBmhpRuangan extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $data = $this->loadData()->asArray()->all();
        $cacheFiles = Yii::$app->cacheFiles;
        $row = $tmpCache = [];
        $no = 1;
        $prefix = 0;
        foreach ($data as $value) {
            $tmp[1] = $no;
            $tmp[2] = ArrayHelper::getValue($value, 'ruangan_nama');
            $tmp[3] = isset($value['tgl_transaksi']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['tgl_transaksi']))) : null;
            $tmp[4] = ArrayHelper::getValue($value, 'no_transaksi');
            $tmp[5] = ArrayHelper::getValue($value, 'jenisobatalkes_nama');
            $tmp[6] = ArrayHelper::getValue($value, 'kode_obat');
            $tmp[7] = ArrayHelper::getValue($value, 'nama_obat');
            $tmp[8] = ArrayHelper::getValue($value, 'qty_input', 0);
            $tmp[9] = ArrayHelper::getValue($value, 'satuan_besar');
            $tmp[10] = ArrayHelper::getValue($value, 'harga_netto_konversi');
            $tmp[11] = ArrayHelper::getValue($value, 'total_harga');
            $tmp[12] = ArrayHelper::getValue($value, 'user');
            $tmp[13] = ArrayHelper::getValue($value, 'catatan');

            $tmpCache[] = $tmp;
            if (($no % ($this->totalPerPage)) == 0) {
                Yii::$app->redis->executeCommand('PUBLISH', [
                    'channel' => 'export-excel:' . $this->unique_str,
                    'message' => json_encode(['unique_process' => $this->unique_str]),
                ]);
                $cacheFiles->set($this->unique_str . '-' . $prefix, $tmpCache);
                $prefix++;
                $tmpCache = [];
            }
            $no++;
        }
        $cacheFiles->set($this->unique_str . '-' . $prefix, $tmpCache);
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:' . $this->unique_str,
            'message' => json_encode(['unique_process' => $this->unique_str]),
        ]);
        $tmpCache = [];

        return json_encode([
            'service' => 'Sirs-LaporanPemakaianBmhpRuangan',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    private function loadData()
    {
        $request = $this->filter;
        $model = new LaporanPemakaianObatRuanganView;
        $query = $model::find(true);
        $model->daterangeFilter($query, $request, 'tgl_transaksi');

        return DocoRestActiveFilter::advancedFilter($model, $query, $request);
    }
}
