<?php 

namespace Integrasi\Service\Sirs;

use Yii;

use Integrasi\Service\Sirs\Models\InfoResepView;
use Integrasi\Service\Sirs\Models\Ruangan;
use Integrasi\Components\DocoRestActiveFilter;

class ProsesSerahkanObatReseptur extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $data = $this->getDataReseptur()->asArray()->all();
        
        $cacheFiles = Yii::$app->cacheFiles;
        $row = $tmpCache = [];
        $no = 1;
        $prefix = 0;
        foreach ($data as $value) 
        {
            $tmp[1]  = $no;
            $tmp[2]  = !empty($value['reseptur_id']) ? $value['reseptur_id'] : '-';
            $tmp[3]  = !empty($value['resep_id']) ? $value['resep_id'] : '-';
            $tmp[4]  = !empty($value['nama_pasien']) ? $value['nama_pasien'] : '-';
            $tmp[5]  = !empty($value['ruangan_id']) ? $value['ruangan_id'] : '-';
            $tmp[6]  = !empty($value['instalasi_reseptur_id']) ? $value['instalasi_reseptur_id'] : NULL;

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
            'service' => 'Sirs-ProsesSerahkanObatReseptur',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    public function getDataReseptur()
    {
        $request = $this->filter;

        $nomorResep = $request[0]['nomor'];

        $arr_noresep = explode(',', $nomorResep);

        $date = date('Y-m-d');
        $model = new InfoResepView;
        $query = $model::find()
            ->where(['no_resep' => $arr_noresep])
            ->orWhere(['no_reseptur' => $arr_noresep]);
    
        return DocoRestActiveFilter::advancedFilter($model, $query, $request);
    }
}