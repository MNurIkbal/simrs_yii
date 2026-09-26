<?php 

namespace Integrasi\Service\Sirs;

use Yii;

use Integrasi\Service\Sirs\Models\LaporanPurchaseOrderOutstandingBarangView;
use Integrasi\Components\DocoRestActiveFilter;

class LaporanPurchaseOrderBarangExcel extends \Integrasi\Contracts\DocoImplement
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
            $tmp[2]  = !empty($value['no_pr']) ? $value['no_pr'] : '-';
            $tmp[3]  = isset($value['tanggal_verifikasi_pr']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['tanggal_verifikasi_pr']))) : '-';
            $tmp[4]  = !empty($value['no_po']) ? $value['no_po'] : '-';
            $tmp[5]  = isset($value['tanggal_po']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['tanggal_po']))) : '-';
            $tmp[6]  = isset($value['tanggal_verifikasi_po']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['tanggal_verifikasi_po']))) : '-';
            $tmp[7]  = !empty($value['is_cito']) ? $value['is_cito'] : '-';
            $tmp[8]  = !empty($value['is_admin']) ? $value['is_admin'] : '-';
            $tmp[9]  = !empty($value['supplier_kode']) ? $value['supplier_kode'] : '-';
            $tmp[10]  = !empty($value['supplier_nama']) ? $value['supplier_nama'] : '-';
            $tmp[11]  = !empty($value['manufacturer']) ? $value['manufacturer'] : '-';
            $tmp[12]  = !empty($value['item_code']) ? $value['item_code'] : '-';
            $tmp[13]  = !empty($value['item_name']) ? $value['item_name'] : '-';
            $tmp[14]  = !empty($value['qty_po']) ? $value['qty_po'] : '-';
            $tmp[15]  = !empty($value['uom']) ? $value['uom'] : '-';
            $tmp[16]  = !empty($value['from_uom']) ? $value['from_uom'] : '-';
            $tmp[17]  = !empty($value['factor']) ? $value['factor'] : '-';
            $tmp[18]  = !empty($value['to_uom']) ? $value['to_uom'] : '-';
            $tmp[19]  = !empty($value['price']) ? $value['price'] : '-';
            $tmp[20]  = !empty($value['deduction_percent']) ? $value['deduction_percent'] : '-';
            $tmp[21]  = !empty($value['addition_percent']) ? $value['addition_percent'] : '-';
            $tmp[22]  = !empty($value['gross_amount']) ? $value['gross_amount'] : '-';
            $tmp[23]  = !empty($value['nett_amount']) ? $value['nett_amount'] : '-';
            $tmp[24]  = !empty($value['remarks']) ? $value['remarks'] : '-';

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
            'service' => 'Sirs-LaporanPurchaseOrderBarangExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    public function getDataExcel()
    {
        $request = $this->filter;
        $model = new LaporanPurchaseOrderOutstandingBarangView;
        $dateFilter = 'tanggal_po';
        $query = $model::find();
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        if (isset($request['advanced-filter'])) {
            $advancedFilter = $request['advanced-filter'];
            if(!empty($advancedFilter['tanggal_po'])) {
                $explode = explode(" - ", $advancedFilter['tanggal_po']);
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