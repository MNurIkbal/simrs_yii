<?php 

namespace Integrasi\Service\Sirs;

use Yii;

use Integrasi\Service\Sirs\Models\KetersediaanObatView;
use Integrasi\Components\DocoRestActiveFilter;

class informasiStokObatAlkes extends \Integrasi\Contracts\DocoImplement
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
            $tmp[2]  = !empty($value['ruangan_nama']) ? $value['ruangan_nama'] : '-';
            $tmp[3]  = !empty($value['obatalkes_nama']) ? $value['obatalkes_nama'] : '-';
            $tmp[4]  = !empty($value['obatalkes_kode']) ? $value['obatalkes_kode'] : '-';
            $tmp[5]  = !empty($value['ven_name']) ? $value['ven_name'] : '-';
            $tmp[6]  = !empty($value['min_stok']) ? $value['min_stok'] : '0';
            $tmp[7]  = !empty($value['max_stok']) ? $value['max_stok'] : '0';
            $tmp[8]  = !empty($value['qty_dipesan']) ? $value['qty_dipesan'] : '0';
            $tmp[9]  = !empty($value['qty_tersedia']) ? $value['qty_tersedia'] : '0';
            $tmp[10]  = !empty($value['qty_stok']) ? $value['qty_stok'] : '0';

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
            'service' => 'Sirs-informasiStokObatAlkes',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    public function getDataExcel()
    {
        $request = $this->filter;
        $model = new KetersediaanObatView;
        $query = $model::find(true);

        if(isset($request['advanced-filter'])) {
            if(isset($request['advanced-filter']['periodestok_nama'])) {
                $date = $request['advanced-filter']['periodestok_nama'];
                unset($request['advanced-filter']['periodestok_nama']); // Unset Advanced Filter  date range
                $between = true;
            }

            if(isset($request['advanced-filter']['ruangan_id'])) {
                $ruanganId = $request['advanced-filter']['ruangan_id'];
                $listRuangan = explode(",", $ruanganId);
                $query->andWhere(['IN', 'ruangan_id', $listRuangan]);
                unset($request['advanced-filter']['ruangan_id']);
            }

            if(isset($request['advanced-filter']['obatalkes_kode'])) {
                $filterKodeObatalkes = $request['advanced-filter']['obatalkes_kode'];
                $query->andWhere(['ILIKE', 'obatalkes_kode', trim($filterKodeObatalkes)]);
            }
        }
    
        return DocoRestActiveFilter::advancedFilter($model,$query,$request);
    }
}