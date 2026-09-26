<?php

namespace Integrasi\Service\Sirs;

use Yii;
use Integrasi\Service\Sirs\Models\LaporanPenerimaanObatAlkesView;
use Integrasi\Components\DocoRestActiveFilter;
use yii\helpers\ArrayHelper;

class LapPenerimaaanObatAlkesExport extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $data = $this->loadData()->asArray()->all();
        $cache = Yii::$app->cache;
        $row = $tmpCache = [];
        $no = 1;
        $prefix = 0;
        foreach ($data as $value) {
            $tmp[1] = $no;
            $tmp[2] = !empty($value['tgl_pr']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date("d M Y H:i:s", strtotime($value['tgl_pr']))) : '-';
            $tmp[3] = !empty($value['tgl_approve']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date("d M Y H:i:s", strtotime($value['tgl_approve']))) : '-';
            $tmp[4] = ArrayHelper::getValue($value, 'supplier_kode', '-');
            $tmp[5] = ArrayHelper::getValue($value, 'supplier_nama', '-');
            $tmp[6] = ArrayHelper::getValue($value, 'nama_manufaktur', '-');
            $tmp[7] = ArrayHelper::getValue($value, 'payterm_nama', '-');
            $tmp[8] = !empty($value['tgl_penerimaan']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date("d M Y H:i:s", strtotime($value['tgl_penerimaan']))) : '-';
            $tmp[9] = ArrayHelper::getValue($value, 'no_penerimaan', '-');
            $tmp[10] = ArrayHelper::getValue($value, 'diterima_oleh', '-');
            $tmp[11] = ArrayHelper::getValue($value, 'status_penerimaan', '-');
            $tmp[12] = !empty($value['tgl_po']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date("d M Y H:i:s", strtotime($value['tgl_po']))) : '-';
            $tmp[13] = !empty($value['tgl_validasi_po']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date("d M Y H:i:s", strtotime($value['tgl_validasi_po']))) : '-';
            $tmp[14] = ArrayHelper::getValue($value, 'nomor_po', '-');
            $tmp[15] = ArrayHelper::getValue($value, 'kode_item', '-');
            $tmp[16] = ArrayHelper::getValue($value, 'obatalkes_nama', '-');
            $tmp[17] = ArrayHelper::getValue($value, 'jenisobatalkes_nama', '-');
            $tmp[18] = ArrayHelper::getValue($value, 'qty_po', '-');
            $tmp[19] = ArrayHelper::getValue($value, 'satuan_po', '-');
            $tmp[20] = ArrayHelper::getValue($value, 'qty_penerimaan', '-');
            $tmp[21] = ArrayHelper::getValue($value, 'qty_retur', '-');
            $tmp[22] = ArrayHelper::getValue($value, 'qty_diterima', '-');
            $tmp[23] = ArrayHelper::getValue($value, 'satuan_terima', '-');
            $tmp[24] = ArrayHelper::getValue($value, 'po_balance', '-');
            $tmp[25] = ArrayHelper::getValue($value, 'satuan_balance', '-');
            $tmp[26] = ArrayHelper::getValue($value, 'nilai_konversi', '-');
            $tmp[27] = ArrayHelper::getValue($value, 'qty_konversi', '-');
            $tmp[28] = ArrayHelper::getValue($value, 'satuan_kecil', '-');
            $tmp[29] = ArrayHelper::getValue($value, 'harganetto', '-');
            $tmp[30] = ArrayHelper::getValue($value, 'harga', '-');
            $tmp[31] = ArrayHelper::getValue($value, 'discount', '-');
            $tmp[32] = ArrayHelper::getValue($value, 'ppn_persen', '-');
            $tmp[33] = ArrayHelper::getValue($value, 'sub_total', '-');
            $tmp[34] = ArrayHelper::getValue($value, 'total', '-');
            $tmp[35] = ArrayHelper::getValue($value, 'catatan_po', '-');
            $tmp[36] = ArrayHelper::getValue($value, 'no_pr', '-');
            $tmp[37] = ArrayHelper::getValue($value, 'no_batch', '-');
            $tmp[38] = !empty($value['tgl_kadaluarsa']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date("d M Y H:i:s", strtotime($value['tgl_kadaluarsa']))) : '-';
            $tmp[39] = ArrayHelper::getValue($value, 'no_suratjalan', '-');
            $tmp[40] = ArrayHelper::getValue($value, 'no_faktur', '-');
            $tmpCache[] = $tmp;
            if (($no%50) == 0)
            {
                Yii::$app->redis->executeCommand('PUBLISH', [
                   'channel' => 'export-excel:'.$this->unique_str,
                   'message' => json_encode(['unique_process' => $this->unique_str]),
                ]);
                $cache->set($this->unique_str .'-'. $prefix, $tmpCache);
                $prefix++;
                $tmpCache = [];
            }
            $no++;
        }
        $cache->set($this->unique_str .'-'. $prefix, $tmpCache);        
        return json_encode([
            'service' => 'Sirs-LapPenerimaaanObatAlkesExport',
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str,
            'data' => count($tmpCache)
        ]);
    }

    private function loadData()
    {
        $request = $this->filter;
        $model = new LaporanPenerimaanObatAlkesView;
        $query = $model::find();
        
        $startDate = date('Y-m-d 00:00:00');
        $endDate = date('Y-m-d 23:59:59');

        if(!empty($request['advanced-filter'])){
            $advancedFilter = $request['advanced-filter'];
            if (isset($advancedFilter['tgl_penerimaan'])) {
                $tgl_filter = explode(' - ', $advancedFilter['tgl_penerimaan']);

                $startDate = date('Y-m-d H:i:s', strtotime($tgl_filter[0]));
                $endDate   = date('Y-m-d H:i:s', strtotime($tgl_filter[1] . ' 23:59:59'));

                unset($request['advanced-filter']['tgl_penerimaan']);
            }

            if(!empty($advancedFilter['supplier_id'])) {
                $supplierIds = $advancedFilter['supplier_id'];
                $query->andWhere(['IN', 'supplier_id', $supplierIds]);
                unset($request['advanced-filter']['supplier_id']);
            }
        }
        $query->andWhere(['between', 'tgl_penerimaan', $startDate, $endDate]);
        $query->orderBy(['tgl_penerimaan' => SORT_DESC]);

        return DocoRestActiveFilter::advancedFilter($model, $query, $request);
    }
}