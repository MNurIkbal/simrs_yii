<?php 

namespace Integrasi\Service\Sirs;

use Yii;

use yii\helpers\ArrayHelper;
use Integrasi\Service\Sirs\Models\LaporanAllPOView;
use Integrasi\Components\DocoRestActiveFilter;

class LaporanPurchaseOrderObatBarangExcel extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $data = $this->getDataExcel()->asArray()->all();
        
        $request = $this->filter;
        $type = $request['advanced-filter']['type'];
        $cacheFiles = Yii::$app->cacheFiles;
        $row = $tmpCache = [];
        $no = 1;
        $prefix = 0;
        foreach ($data as $value) 
        {
            $no1=0;
            $tmp[1]  = $no;
            $tmp[2]  = !empty($value['no_pr']) ? $value['no_pr'] : '-';
            $tmp[3]  = isset($value['tanggal_verifikasi_pr']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['tanggal_verifikasi_pr']))) : '-';
            $tmp[4]  = !empty($value['no_po']) ? $value['no_po'] : '-';
            $tmp[5]  = isset($value['tanggal_po']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['tanggal_po']))) : '-';
            $tmp[6]  = isset($value['tanggal_verifikasi_po']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['tanggal_verifikasi_po']))) : '-';
            $tmp[7]  = !empty($value['is_cito']) ? $value['is_cito'] : '-';
            $tmp[8]  = !empty($value['is_admin']) ? $value['is_admin'] : '-';
            if($type=='OBAT') {
                $tmp[9]  = !empty($value['is_consigment']) ? $value['is_consigment'] : '-';
                $no1=1;
            }
            $tmp[9+$no1]  = !empty($value['supplier_code']) ? $value['supplier_code'] : '-';
            $tmp[10+$no1]  = !empty($value['supplier_name']) ? $value['supplier_name'] : '-';
            $tmp[11+$no1]  = !empty($value['manufacturer']) ? $value['manufacturer'] : '-';
            $tmp[12+$no1]  = !empty($value['item_code']) ? $value['item_code'] : '-';
            $tmp[13+$no1]  = !empty($value['item_name']) ? $value['item_name'] : '-';
            $tmp[14+$no1]  = !empty($value['qty_po']) ? $value['qty_po'] : '-';
            $tmp[15+$no1]  = !empty($value['po_balance']) ? $value['po_balance'] : '0';
            $tmp[16+$no1]  = !empty($value['qty_outstanding']) ? $value['qty_outstanding'] : '-';
            $tmp[17+$no1]  = !empty($value['uom']) ? $value['uom'] : '-';
            $tmp[18+$no1]  = !empty($value['from_uom']) ? $value['from_uom'] : '-';
            $tmp[19+$no1]  = !empty($value['factor']) ? $value['factor'] : '-';
            $tmp[20+$no1]  = !empty($value['to_uom']) ? $value['to_uom'] : '-';
            $tmp[21+$no1]  = !empty($value['price']) ? $value['price'] : '0';
            $tmp[22+$no1]  = !empty($value['deduction_percent']) ? $value['deduction_percent'] : '0';
            $tmp[23+$no1]  = !empty($value['deduction_rupiah']) ? $value['deduction_rupiah'] : '0';
            $tmp[24+$no1]  = !empty($value['addition_percent']) ? $value['addition_percent'] : '0';
            $tmp[25+$no1]  = !empty($value['gross_amount']) ? $value['gross_amount'] : '0';
            $tmp[26+$no1]  = !empty($value['nett_amount']) ? $value['nett_amount'] : '0';
            $tmp[27+$no1]  = !empty($value['remarks']) ? $value['remarks'] : '-';
            $tmp[28+$no1]  = !empty($value['catatan_1']) ? $value['catatan_1'] : '-';
            $tmp[29+$no1]  = !empty($value['catatan_2']) ? $value['catatan_2'] : '-';
            $tmp[30+$no1]  = !empty($value['status_po']) ? $value['status_po'] : '-';
            $tmp[31+$no1]  = isset($value['reject_date']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['reject_date']))) : '-';
            $tmp[32+$no1]  = !empty($value['reject_remarks']) ? $value['reject_remarks'] : '-';
            $tmp[33+$no1]  = isset($value['tanggal_penerimaan']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['tanggal_penerimaan']))) : '-';

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
            'service' => 'Sirs-LaporanPurchaseOrderObatBarangExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    public function getDataExcel()
    {
        $request = $this->filter;
        $model = new LaporanAllPOView;
        $dateFilter = 'tanggal_po';
        $query = $model::find();
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        if (isset($request['advanced-filter'])) {
            $advancedFilter = $request['advanced-filter'];
            $tipe = $advancedFilter['type'];
            if(!empty($advancedFilter['tanggal_po'])) {
                $explode = explode(" - ", $advancedFilter['tanggal_po']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
            }
            $query->andWhere(['type'=>$tipe]);
        }
        $query->andWhere(['between', $dateFilter, $start, $end]);

        return DocoRestActiveFilter::advancedFilter($model, $query, $request);
    }
}
