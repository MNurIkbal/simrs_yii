<?php 

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\LaporanRekapPenjualanFarmasiFn;
use Integrasi\Service\Sirs\Models\Pegawai;
use Integrasi\Components\DocoRestActiveFilter;
use Integrasi\Components\DocoConstants;
use yii\helpers\ArrayHelper;

class LaporanTotalRekapPenjualanFarmasi extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $data = $this->loadData()->asArray()->all();
        $cacheFiles = Yii::$app->cacheFiles;
        $row = $tmpCache = [];
        $no = 1;
        $prefix = 0;
        foreach ($data as $value) 
        {
            $tmp[1]  = $no;
            $tmp[2]  = !empty($value['kode_obat']) ? $value['kode_obat'] : '';
            $tmp[3]  = !empty($value['jenisobatalkes_nama']) ? $value['jenisobatalkes_nama'] : '';
            $tmp[4]  = !empty($value['nama_obat']) ? $value['nama_obat'] : '';
            $tmp[5]  = !empty($value['qty']) ? $value['qty'] : '';
            $tmp[6]  = !empty($value['satuan_kecil']) ? $value['satuan_kecil'] : '';
            $tmp[7]  = !empty($value['diskon']) ? number_format($value['diskon'],2,',','.') : 0;
            $tmp[8]  = !empty($value['total']) ? number_format($value['total'],2,',','.') : 0;

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
            'service' => 'Sirs-LaporanTotalRekapPenjualanFarmasi',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    private function loadData()
    {
        $request = $this->filter;
        $advanced_filter = !empty($request['advanced-filter']) ? $request['advanced-filter'] : null;
        $start = date('Y-m-d 00:00:00');
        $end   = date('Y-m-d 23:59:59');

        if(isset($advanced_filter) && isset($advanced_filter['tgl'])) {
            $explode = explode(" - ", $advanced_filter['tgl']);
            if(count($explode) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
            }
            unset($request['advanced-filter']['tgl']);
        }

        $model = new LaporanRekapPenjualanFarmasiFn;
        $query = $model::getData($start, $end);

        if(isset($advanced_filter['jenisobatalkes_nama'])){
            $jenisobatalkes_id = $advanced_filter['jenisobatalkes_nama'];
            $query->andWhere(['jenisobatalkes_id' => $jenisobatalkes_id]);
            unset($request['advanced-filter']['jenisobatalkes_nama']);
        }
        return DocoRestActiveFilter::advancedFilter($model, $query, $request);
    }
}
