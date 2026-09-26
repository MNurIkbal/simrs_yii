<?php 

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoRestActiveFilter;
use Integrasi\Components\DocoConstants;

class LapRekapHarianKinerjaProfresional extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        // $data = $this->loadData()->asArray()->all();        
        $data = $this->data;
        $cacheFiles = Yii::$app->cacheFiles;
        $row = $tmpCache = [];
        $no = 1;
        $prefix = 0;
        $no     = 1;
        $jumlah = 'jumlah';
        foreach ($data as $value) {
            $tmp[1] = $no;
            $tmp[2] = $value['kelaspelayanan_nama'];
            $tmp[3] = $value['jumlah_bed'];
            $tmp[4] = $value['jumlah_bed'];
            $tmp[5] = $value['pasien_awal'];
            $tmp[6] = $value['pasien_masuk'];
            $tmp[7] = $value['pindah_ke'];
            $tmp[8] = $value['jumlah_pasien_masuk'];
            $tmp[9] = $value['keluar_hidup'];
            $tmp[10] = $value['dipindahkan_dari'];
            $tmp[11] = $value['rujuk_rs_lain'];
            $tmp[12] = $value['meninggal_kurang_48'];
            $tmp[13] = $value['meninggal_lebih_48'];
            $tmp[14] = $value['jumlah_pasien_keluar'];
            $tmp[15] = $value['hp'];
            $tmp[16] = $value['los'];
            $tmp[17] = $value['alos'];
            $tmp[18] = ceil($value['bor_today']);
            $tmp[19] = ceil($value['toi']);
            $tmp[20] = ceil($value['bto']);
            $tmp[21] = ceil($value['ndr']);
            $tmp[22] = ceil($value['gdr']);
            

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
            'service' => 'Sirs-LapRekapHarianKinerjaProfresional',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    private function loadData()
    {
        $request = $this->filter;

        $model   = new LaporanDokterRujukanV;
        $query   = $model::find();
        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');
        if(isset($request['advanced-filter'])) {
            if(isset($request['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $request['advanced-filter']['tgl_pendaftaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($request['advanced-filter']['tgl_pendaftaran']);
            }

            if(isset($request['advanced-filter']['dok_rujukan_nama'])) {
                $dok_rujukan_id = $request['advanced-filter']['dok_rujukan_nama'];

                unset($request['advanced-filter']['dok_rujukan_nama']);
            }

            if(isset($request['advanced-filter']['spes_rujukan_nama'])) {
                $spes_rujukan_id = $request['advanced-filter']['spes_rujukan_nama'];

                unset($request['advanced-filter']['spes_rujukan_nama']);
            }

            if(isset($request['advanced-filter']['jenis_referal_nama'])) {
                $jenis_referal_id = $request['advanced-filter']['jenis_referal_nama'];

                unset($request['advanced-filter']['jenis_referal_nama']);
            }

        }

        

        if (!empty($dok_rujukan_id)){
            $query->andWhere(['dok_rujukan_id' => $dok_rujukan_id]);
        }
        if (!empty($spes_rujukan_id)){
            $query->andWhere(['=', 'spes_rujukan_id', $spes_rujukan_id]);
        }
        if (!empty($jenis_referal_id)){
            $query->andWhere(['jenis_referal_id' => $jenis_referal_id]);
        }

        $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        
        return DocoRestActiveFilter::advancedFilter($model, $query, $request);
    }
}
