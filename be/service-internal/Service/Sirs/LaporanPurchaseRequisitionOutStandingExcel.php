<?php 

namespace Integrasi\Service\Sirs;

use Yii;

use Integrasi\Service\Sirs\Models\LapPurchaseRequisitionOutstandingView;
use Integrasi\Components\DocoRestActiveFilter;

class LaporanPurchaseRequisitionOutStandingExcel extends \Integrasi\Contracts\DocoImplement
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
            $tmp[3]  = isset($value['tgl_pr']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['tgl_pr']))) : '-';
            $tmp[4]  = isset($value['tgl_approve']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['tgl_approve']))) : '-';
            $tmp[5]  = !empty($value['is_cyto']) ? $value['is_cyto'] : '-';
            $tmp[6]  = !empty($value['is_admin']) ? $value['is_admin'] : '-';
            $tmp[7]  = !empty($value['is_consigment']) ? $value['is_consigment'] : '-';
            $tmp[8]  = !empty($value['kode_obat']) ? $value['kode_obat'] : '-';
            $tmp[9]  = !empty($value['obatalkes_nama']) ? $value['obatalkes_nama'] : '-';
            $tmp[10]  = !empty($value['jenis_obat']) ? $value['jenis_obat'] : '-';
            $tmp[11]  = !empty($value['qty_input']) ? $value['qty_input'] : '-';
            $tmp[12]  = !empty($value['satuan']) ? $value['satuan'] : '-';
            $tmp[13]  = !empty($value['uom']) ? $value['uom'] : '-';
            $tmp[14]  = !empty($value['status_pr']) ? $value['status_pr'] : '-';
            $tmp[15]  = !empty($value['manufaktur_nama']) ? $value['manufaktur_nama'] : '-';
            $tmp[16]  = !empty($value['status_obat']) ? $value['status_obat'] : '-';
            $tmp[17]  = !empty($value['catatan_pr']) ? $value['catatan_pr'] : '-';
            $tmp[18]  = !empty($value['alasan']) ? $value['alasan'] : '-';
            $tmp[19]  = !empty($value['pegawai']) ? $value['pegawai'] : '-';

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
            'service' => 'Sirs-LaporanPurchaseRequisitionOutStandingExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    public function getDataExcel()
    {
        $request = $this->filter;
        $model = new LapPurchaseRequisitionOutstandingView;
        $dateFilter = 'tgl_pr';
        $query = $model::find();
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        if (isset($request['advanced-filter'])) {
            $advancedFilter = $request['advanced-filter'];
            if(!empty($advancedFilter['tgl_pr'])) {
                $explode = explode(" - ", $advancedFilter['tgl_pr']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
            }
        }
        $query->andWhere(['between', $dateFilter, $start, $end]);

        return DocoRestActiveFilter::advancedFilter($model,$query,$request);
    }
}