<?php 

namespace Integrasi\Service\Sirs;

use Yii;

use Integrasi\Service\Sirs\Models\LaporanPurchaseOrderOutstandingView;
use Integrasi\Components\DocoRestActiveFilter;

class LaporanPurchaseOrderExcel extends \Integrasi\Contracts\DocoImplement
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
            $tmp[2]  = isset($value['tgl_pr']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['tgl_pr']))) : '-';
            $tmp[3]  = isset($value['tgl_approve']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['tgl_approve']))) : '-';
            $tmp[4]  = !empty($value['no_pr']) ? $value['no_pr'] : '-';
            $tmp[5]  = !empty($value['no_po']) ? $value['no_po'] : '-';
            $tmp[6]  = isset($value['tgl_po_dibuat']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['tgl_po_dibuat']))) : '-';
            $tmp[7]  = isset($value['tgl_validasi']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['tgl_validasi']))) : '-';
            $tmp[8]  = !empty($value['is_cito']) ? $value['is_cito'] : '-';
            $tmp[9]  = !empty($value['is_admin']) ? $value['is_admin'] : '-';
            $tmp[10]  = !empty($value['is_consigment']) ? $value['is_consigment'] : '-';
            $tmp[11]  = !empty($value['manufaktur_nama']) ? $value['manufaktur_nama'] : '-';
            $tmp[12]  = !empty($value['supplier_kode']) ? $value['supplier_kode'] : '-';
            $tmp[13]  = !empty($value['supplier_nama']) ? $value['supplier_nama'] : '-';
            $tmp[14]  = !empty($value['jenisobatalkes_nama']) ? $value['jenisobatalkes_nama'] : '-';
            $tmp[15]  = !empty($value['kode_item']) ? $value['kode_item'] : '-';
            $tmp[16]  = !empty($value['nama_item']) ? $value['nama_item'] : '-';
            $tmp[17]  = !empty($value['qty_po']) ? $value['qty_po'] : '-';
            $tmp[18]  = !empty($value['po_balance']) ? $value['po_balance'] : '-';
            $tmp[19]  = !empty($value['satuan_besar']) ? $value['satuan_besar'] : '-';
            $tmp[20]  = !empty($value['uom']) ? $value['uom'] : '-';
            $tmp[21]  = !empty($value['harga']) ? $value['harga'] : '-';
            $tmp[22]  = !empty($value['discount']) ? $value['discount'] : '-';
            $tmp[23]  = !empty($value['ppn_persen']) ? $value['ppn_persen'] : '-';
            $tmp[24]  = !empty($value['sub_total']) ? $value['sub_total'] : '-';
            $tmp[25]  = !empty($value['total']) ? $value['total'] : '-';
            $tmp[26]  = !empty($value['catatan1']) ? $value['catatan1'] : '-';
            $tmp[27]  = !empty($value['catatan2']) ? $value['catatan2'] : '-';

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
            'service' => 'Sirs-LaporanPurchaseOrderExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    public function getDataExcel()
    {
        $request = $this->filter;
        $model = new LaporanPurchaseOrderOutstandingView;
        $dateFilter = 'tgl_po_dibuat';
        $query = $model::find();
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        if (isset($request['advanced-filter'])) {
            $advancedFilter = $request['advanced-filter'];
            if(!empty($advancedFilter['tgl_po_dibuat'])) {
                $explode = explode(" - ", $advancedFilter['tgl_po_dibuat']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
            }
            if (isset($_GET['advanced-filter']['is_cito'])) {
                $cito = (int) $_GET['advanced-filter']['is_cito'];
                $query->andWhere(['is_cyto' => $cito]);
            }
            if (isset($_GET['advanced-filter']['is_admin'])) {
                $admin = (int) $_GET['advanced-filter']['is_admin'];
                $query->andWhere(['is_admin' => $admin]);
            }
        }
        $query->andWhere(['between', $dateFilter, $start, $end]);

        return DocoRestActiveFilter::advancedFilter($model,$query,$request);
    }
}