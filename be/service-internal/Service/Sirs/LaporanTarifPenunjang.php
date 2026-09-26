<?php 

namespace Integrasi\Service\Sirs;

use Yii;

use Integrasi\Service\Sirs\Models\InfoTarifPenunjangView;
use Integrasi\Service\Sirs\Models\Ruangan;
use Integrasi\Components\DocoRestActiveFilter;

class LaporanTarifPenunjang extends \Integrasi\Contracts\DocoImplement
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
            $tmp[3]  = !empty($value['penjamin_nama']) ? $value['penjamin_nama'] : '-';
            $tmp[4]  = !empty($value['nama_kelompok']) ? $value['nama_kelompok'] : '-';
            $tmp[5]  = !empty($value['jenispemeriksaanlab_nama']) ? $value['jenispemeriksaanlab_nama'] : '-';
            $tmp[6]  = !empty($value['pemeriksaanlab_nama']) ? $value['pemeriksaanlab_nama'] : '-';
            $tmp[7]  = !empty($value['kelaspelayanan_nama']) ? $value['kelaspelayanan_nama'] : '-';
            $tmp[8]  = !empty($value['harga_tariftindakan']) ? $value['harga_tariftindakan'] : '0';
            $tmp[9]  = !empty($value['persencyto_tindakan']) ? $value['persencyto_tindakan'] : '0';
            $tmp[10]  = !empty($value['persendiskon_tindakan']) ? $value['persendiskon_tindakan'] : '0';

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
            'service' => 'Sirs-LaporanTarifPenunjang',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    public function getDataExcel()
    {
        $request = $this->filter;

        $flashData = $request['flash'];
        $instalasiId = $request['instalasi_id'];

        $date = date('Y-m-d');
        $model = new InfoTarifPenunjangView;
        $query = $model::find()->where(['komponentarif_id' => 6]);

        if( !empty($instalasiId) ) {
        	$query->andWhere(['instalasi_id' => $instalasiId]);
        }

        if(isset($flashData['advanced-filter'])) {
            $advancedFilter = $flashData['advanced-filter'];
            if(isset($advancedFilter['ruangan_nama'])) {
            	$ruangan_id = $advancedFilter['ruangan_nama'];
            	$query->andWhere(['ruangan_id' => $ruangan_id]);
                unset($flashData['advanced-filter']['ruangan_nama']);
            }
            if(isset($advancedFilter['penjamin_nama'])) {
                $penjamin_id = $advancedFilter['penjamin_nama'];
                $query->andWhere(['penjamin_id' => $penjamin_id]);
                unset($flashData['advanced-filter']['penjamin_nama']);
            }
            if(isset($advancedFilter['nama_kelompok'])) {
                $kelompok_id = $advancedFilter['nama_kelompok'];
                $query->andWhere(['kelompokpemeriksaanlab_id' => $kelompok_id]);
                unset($flashData['advanced-filter']['nama_kelompok']);
            }
            if(isset($advancedFilter['jenispemeriksaanlab_nama'])) {
                $jenis_id = $advancedFilter['jenispemeriksaanlab_nama'];
                $query->andWhere(['jenispemeriksaanlab_id' => $jenis_id]);
                unset($flashData['advanced-filter']['jenispemeriksaanlab_nama']);
            }
            if(isset($advancedFilter['kelaspelayanan_nama'])) {
                $kelaspelayanan_id = $advancedFilter['kelaspelayanan_nama'];
                $query->andWhere(['kelaspelayanan_id' => $kelaspelayanan_id]);
                unset($flashData['advanced-filter']['kelaspelayanan_nama']);
            }
        }

        if(isset($flashData['order'])) {
            $order = $flashData['order'];
            $query->orderBy($order);
        }
    
        return DocoRestActiveFilter::advancedFilter($model, $query, $flashData);
    }
}