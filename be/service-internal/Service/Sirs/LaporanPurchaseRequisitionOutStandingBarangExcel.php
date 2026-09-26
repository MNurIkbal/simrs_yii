<?php 

namespace Integrasi\Service\Sirs;

use Yii;

use Integrasi\Service\Sirs\Models\LapPurchaseRequisitionOutstandingBarangView;
use Integrasi\Components\DocoRestActiveFilter;

class LaporanPurchaseRequisitionOutStandingBarangExcel extends \Integrasi\Contracts\DocoImplement
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
            $tmp[3]  = isset($value['create_date']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['create_date']))) : '-';
            $tmp[4]  = isset($value['tgl_approve']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['tgl_approve']))) : '-';
            $tmp[5]  = !empty($value['is_cyto']) ? $value['is_cyto'] : '-';
            $tmp[6]  = !empty($value['is_admin']) ? $value['is_admin'] : '-';
            $tmp[7]  = !empty($value['item_code']) ? $value['item_code'] : '-';
            $tmp[8]  = !empty($value['item_name']) ? $value['item_name'] : '-';
            $tmp[9]  = !empty($value['category']) ? $value['category'] : '-';
            $tmp[10]  = !empty($value['qty']) ? $value['qty'] : '-';
            $tmp[11]  = !empty($value['uom']) ? $value['uom'] : '-';
            $tmp[12]  = !empty($value['from_uom']) ? $value['from_uom'] : '-';
            $tmp[13]  = !empty($value['factor']) ? $value['factor'] : '-';
            $tmp[14]  = !empty($value['to_uom']) ? $value['to_uom'] : '-';
            $tmp[15]  = !empty($value['remarks']) ? $value['remarks'] : '-';

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
            'service' => 'Sirs-LaporanPurchaseRequisitionOutStandingBarangExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    public function getDataExcel()
    {
        $request = $this->filter;
        $model = new LapPurchaseRequisitionOutstandingBarangView;
        $dateFilter = 'create_date';
        $query = $model::find();
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        if (isset($request['advanced-filter'])) {
            $advancedFilter = $request['advanced-filter'];
            if(!empty($advancedFilter['create_date'])) {
                $explode = explode(" - ", $advancedFilter['create_date']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
            }
            if (isset($_GET['advanced-filter']['is_cyto'])) {
                $cito = (int) $_GET['advanced-filter']['is_cyto'];
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