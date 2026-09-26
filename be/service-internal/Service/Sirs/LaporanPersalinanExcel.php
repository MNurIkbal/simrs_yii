<?php

namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use GuzzleHttp\Client;
use Integrasi\Service\Sirs\Models\LaporanKunjunganPasienRsDiagnosa;
use Integrasi\Service\Sirs\Models\Lookup;
use Integrasi\Components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Integrasi\Service\Sirs\Models\LaporanpersalinanV;

class LaporanPersalinanExcel extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        // $dataObject = $this->getObjectData();
        $dataObject = $this->getObjectData()->asArray()->all();
        $cacheFiles = Yii::$app->cacheFiles;
        $row = $tmpCache = [];
        $no = 1;
        $prefix = 0;
        foreach ($dataObject as $value) {

            $tmp[1] = $no;
            $tmp[2] = $value['nama_bayi'];
            $tmp[3] = date('d/m/Y H:i', strtotime($value['tgl_lahir_bayi']));
            $tmp[4] = $value['no_rekam_medik_bayi'];
            $tmp[5] = $value['berat_badan'];
            $tmp[6] = $value['nama_ibu'];
            $tmp[7] = $value['no_rekam_medik_ibu'];
            $tmp[8] = $value['dokter_dpjp_nama'];
            $tmp[9] = $value['jenis_persalinan_nama'];

            $row[] = $tmp;
            $tmpCache[] = $tmp;
            if (($no%50) == 0) {
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
            'service' => 'Sirs-LaporanPersalinanExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str,
            'data' => $tmpCache
        ]);
    }

    private function getObjectData()
    {
        $request = $this->filter;
        
        $model   = new LaporanpersalinanV;
        $query   = LaporanpersalinanV::find();
        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');

        if (array_key_exists('advanced-filter', $request)) {
            if (array_key_exists('tgl_lahir_bayi', $request['advanced-filter'])) {
                $explode = explode(' - ', $request['advanced-filter']['tgl_lahir_bayi']);
                
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }

                unset($_GET['advanced-filter']['tgl_lahir_bayi']);
            }

        }

        $query->andWhere(['between', 'tgl_lahir_bayi', $start, $end]);
        return DocoRestActiveFilter::advancedFilter($model, $query, $request);

        // $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        // $dataProvider = new ActiveDataProvider(['query' => $query, 'pagination' => false]);
        // $tmpData = $dataProvider->getModels();
        // return $tmpData;
    }
}