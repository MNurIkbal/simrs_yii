<?php 

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\PemeriksaanPasienRadiologiView;
use Integrasi\Service\Sirs\Models\LaporanPasienRadiologiView;
use Integrasi\Components\DocoRestActiveFilter;
use Integrasi\Components\DocoConstants;

class LaporanPasienRadiologiExcel extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $data = $this->loadData()->asArray()->all();
        // $data = $this->data;
        $cacheFiles = Yii::$app->cacheFiles;
        $row = $tmpCache = [];
        $no = 1;
        $prefix = 0;
        foreach ($data as $value) 
        {
            $diffTglExpertise = "";
            
            if (!empty($value['tgl_hasilrad'])) {
                $diffTglExpertise = DocoHelpers::getLamaTunggu($value['tgl_ambilfoto'], $value['tgl_hasilrad']);
            }

            $tmp[1]  = $no;
            $tmp[2]  = !empty($value['tglmasukpenunjang']) ? DocoHelpers::convertDate($value['tglmasukpenunjang'], 'd-M-Y H:i') : '';
            $tmp[3]  = !empty($value['tglmasukpenunjang']) ? DocoHelpers::convertDate($value['tglmasukpenunjang'], 'd-M-Y H:i') : '';
            $tmp[4]  = $value['is_cyto'] == true ? 'CITO' : 'NON CITO';
            $tmp[5]  = !empty($value['no_pendaftaran']) ? $value['no_pendaftaran'] : '';
            $tmp[6]  = !empty($value['no_rekam_medik']) ? $value['no_rekam_medik'] : '';
            $tmp[7]  = !empty($value['nama_pasien']) ? $value['nama_pasien'] : '';
            $tmp[8]  = !empty($value['tanggal_lahir']) ? DocoHelpers::convertDate($value['tanggal_lahir'], 'd-M-Y') : '';
            $tmp[9]  = !empty($value['nama_pegawai']) ? $value['nama_pegawai'] : '';
            $tmp[10] = !empty($value['dokter_penunjang']) ? $value['dokter_penunjang'] : '';
            $tmp[11] = !empty($value['carabayar_nama']) ? $value['carabayar_nama'] : '';
            $tmp[12] = !empty($value['penjamin_nama']) ? $value['penjamin_nama'] : '';
            $tmp[13] = !empty($value['no_rekam_medik']) ? $value['no_rekam_medik'] : '';
            $tmp[14] = !empty($value['asalrujukan_nama']) ? $value['asalrujukan_nama'] : '';
            $tmp[15] = !empty($value['ruanganasal_nama']) ? $value['ruanganasal_nama'] : '';

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
            'service' => 'Sirs-LaporanPasienRadiologiExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    private function loadData()
    {
        $request = $this->filter;

        $model = new LaporanPasienRadiologiView;
        $query = $model::find();

        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        $startLahir = '';
        $endLahir = '';

        if(isset($request['advanced-filter'])) {
            $advancedFilter = $request['advanced-filter'];
            if(!empty($advancedFilter['tglmasukpenunjang'])) {
                $explode = explode(" - ", $advancedFilter['tglmasukpenunjang']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($advancedFilter['tglmasukpenunjang']); // Unset Advanced Filter  date range
                $between = true;
            }
            if(!empty($advancedFilter['tanggal_lahir'])) {
                $explode = explode(" - ", $advancedFilter['tanggal_lahir']);
                if (count($explode) == 2) {
                    $startLahir = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $endLahir = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($advancedFilter['tanggal_lahir']); // Unset Advanced Filter  date range
                $between = true;
            }
            if(!empty($advancedFilter['no_pendaftaran'])) {
                $query->andWhere(['no_pendaftaran' => $advancedFilter['no_pendaftaran']]);
            }
            if(!empty($advancedFilter['no_rekam_medik'])) {
                $query->andWhere(['no_rekam_medik' => $advancedFilter['no_rekam_medik']]);
            }
        }

        $query->andWhere(['between', 'tglmasukpenunjang', $start, $end]);
        if(!empty($startLahir) && !empty($endLahir) && $between){
            $query->andWhere(['between', 'tanggal_lahir', $startLahir, $endLahir]);
        }

        $query->andWhere(['status_batal' => false ]);
        $query->andWhere(['not',['status_periksa' => (string) DocoConstants::BTL_PERIKSA_LAB]]);
        $query->noInStatusPeriksa(DocoConstants::BTL_APPROVE);
        $query->noInStatusPeriksa(null);
        $query->andWhere(['tindakanpelayananasal_id' => null]);

        return DocoRestActiveFilter::advancedFilter($model, $query, $request);
    }
}
