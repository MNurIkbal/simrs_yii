<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Integrasi\Service\Sirs;

use Yii;
use Integrasi\Service\Sirs\Models\LaporanAdjustmentObatAlkesView;
use Integrasi\Components\DocoRestActiveFilter;

class LaporanAdjustmentObatAlkes extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $data = $this->loadData()->asArray()->all();
        $cacheFiles = Yii::$app->cacheFiles;
        $tmpCache = [];
        $no = 1;
        $prefix = 0;
        foreach ($data as $value) {
            $tmp[1]  = $no;
            $tmp[2]  = !empty($value['ruangan_nama']) ? $value['ruangan_nama'] : '';
            $tmp[3]  = !empty($value['no_adjusmen']) ? $value['no_adjusmen'] : '';
            $tmp[4] = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d-m-Y', strtotime($value['tgl_adjusmen'])));
            $tmp[5]  = !empty($value['jenis_adjusmen_nama']) ? $value['jenis_adjusmen_nama'] : '';
            $tmp[6]  = !empty($value['jenisobatalkes_nama']) ? $value['jenisobatalkes_nama'] : '';
            $tmp[7]  = !empty($value['obatalkes_kode']) ? $value['obatalkes_kode'] : '';
            $tmp[8]  = !empty($value['obatalkes_nama']) ? $value['obatalkes_nama'] : '';
            $tmp[9]  = !empty($value['qty_input']) ? $value['qty_input'] : '';
            $tmp[10] = !empty($value['satuan_besar']) ? $value['satuan_besar'] : '';
            $tmp[11]  = !empty($value['qty_konversi']) ? $value['qty_konversi'] : '';
            $tmp[12] = !empty($value['satuan_kecil']) ? $value['satuan_kecil'] : '';
            $tmp[13] = !empty($value['pegawai_adjusmen']) ? $value['pegawai_adjusmen'] : '';

            $tmpCache[] = $tmp;
            if (($no % 50) == 0) {
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

        return json_encode([
            'service' => 'Sirs-LaporanAdjustmentObatAlkes',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    private function loadData()
    {
        $request = $this->filter;

        $model = new LaporanAdjustmentObatAlkesView;
        $query = $model::find();

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if (isset($request['advanced-filter'])) {
            $advancedFilter = $request['advanced-filter'];
            if (!empty($advancedFilter['tgl_adjusmen'])) {
                $explode = explode(" - ", $advancedFilter['tgl_adjusmen']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($advancedFilter['tgl_adjusmen']);
            }

            if (!empty($advancedFilter['ruangan_nama'])) {
                $query->andWhere(['ruangan_nama' => $advancedFilter['ruangan_nama']]);
            }

            if (!empty($advancedFilter['jenis_adjusmen_nama'])) {
                $query->andWhere(['jenis_adjusmen_nama' => $advancedFilter['jenis_adjusmen_nama']]);
            }

            if (!empty($advancedFilter['jenisobatalkes_nama'])) {
                $query->andWhere(['jenisobatalkes_nama' => $advancedFilter['jenisobatalkes_nama']]);
            }
        }

        $query->andWhere(['between', 'tgl_adjusmen', $start, $end]);
        return DocoRestActiveFilter::advancedFilter($model, $query, $request);
    }
}
