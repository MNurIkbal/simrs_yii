<?php 

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\LaporanPemeriksaanLabView;
use Integrasi\Components\DocoRestActiveFilter;
use Integrasi\Components\DocoConstants;

class LaporanPemeriksaanLabExcel extends \Integrasi\Contracts\DocoImplement
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
            $tmpCache[] = $this->populateEachData($no, $value, $this->hide_price_column);
            if (($no%100) == 0)
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
            'service' => 'Sirs-LaporanPemeriksaanLabExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    private function loadData()
    {
        $request = $this->filter;
        $model = new LaporanPemeriksaanLabView;
        $query = $model::find();
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        if (isset($request['advanced-filter'])) {
            if (isset($request['advanced-filter']['tglmasukpenunjang'])) {
                $explode = explode(" - ", $request['advanced-filter']['tglmasukpenunjang']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                    $startString = date('d F Y', strtotime($explode[0]));
                    $endString = date('d F Y', strtotime($explode[1]));
                }
                unset($request['advanced-filter']['tglmasukpenunjang']);
            }   
        }
        $query->andWhere(['between', 'tglmasukpenunjang', $start, $end]);
        return DocoRestActiveFilter::advancedFilter($model, $query, $request);
    }

    protected function populateEachData($number, $valData, $isHidePrice = false)
    {
        $tarif_satuan = isset($valData['tarif_satuan']) ? $valData['tarif_satuan'] : 0;
        $tarifcyto_tindakan = isset($valData['tarifcyto_tindakan']) ? $valData['tarifcyto_tindakan'] : 0;
        $qty_tindakan = isset($valData['qty_tindakan']) ? $valData['qty_tindakan'] : 1;
        $total = ($tarif_satuan + $tarifcyto_tindakan) * $qty_tindakan;

        $temp[1]  = $number;
        $temp[2]  = !empty($valData['tglmasukpenunjang']) ? date('d-M-Y', strtotime($valData['tglmasukpenunjang'])) : '';
        $temp[3]  = !empty($valData['no_pendaftaran']) ? $valData['no_pendaftaran'] : '';
        $temp[4]  = !empty($valData['no_rekam_medik']) ? $valData['no_rekam_medik'] : '';
        $temp[5]  = !empty($valData['nama_pasien']) ? $valData['nama_pasien'] : '';
        $temp[6]  = !empty($valData['dokter']) ? $valData['dokter'] : '';
        $temp[7]  = !empty($valData['dokter_dpjp_nama']) ? $valData['dokter_dpjp_nama'] : '';
        $temp[8]  = !empty($valData['kelaspelayanan_nama']) ? $valData['kelaspelayanan_nama'] : '';
        $temp[9]  = !empty($valData['nama_kelompok']) ? $valData['nama_kelompok'] : '';
        $temp[10] = !empty($valData['jenispemeriksaanlab_nama']) ? $valData['jenispemeriksaanlab_nama'] : '';
        $temp[11] = !empty($valData['daftartindakan_nama']) ? $valData['daftartindakan_nama'] : '';
        if ($isHidePrice) {
            $temp[12] = !empty($valData['qty_tindakan']) ? $valData['qty_tindakan'] : '';
        } else {
            $temp[12] = number_format($tarif_satuan, 2, ',', '.');
            $temp[13] = !empty($valData['qty_tindakan']) ? $valData['qty_tindakan'] : '';
            $temp[14] = number_format($tarifcyto_tindakan, 2, ',', '.');
            $temp[15] = number_format($total, 2, ',', '.');
        }

        return $temp;
    }
}