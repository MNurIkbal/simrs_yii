<?php 

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\LaporanCaraPulangV;
use Integrasi\Components\DocoRestActiveFilter;
use Integrasi\Components\DocoConstants;

class LaporanCaraPulangPasienExcel extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $data = $this->loadData()->asArray()->all();
        // $data = $this->data;
        $cacheFiles = Yii::$app->cacheFiles;
        $row = $tmpCache = [];
        $no = 1;
        $prefix = 0;
        $no     = 1;

        foreach ($data as $value) {
            $tmp[1]  = $no;
            $tmp[2]  = $value['no_registrasi'];
            $tmp[3]  = $value['tgl_pendaftaran'];
            $tmp[4]  = $value['tgl_pulang'];
            $tmp[5]  = $value['no_rekam_medik'];
            $tmp[6]  = $value['nama_pasien'];
            $tmp[7]  = $value['instalasi_nama'];
            $tmp[8]  = $value['ruangan_nama'];
            $tmp[9]  = $value['cara_pulang'];
            $tmp[10] = $value['rumahsakit_rujukan'] ?? '-';
            $tmp[11] = $value['kondisi_pulang'] ?? '-';
            $tmp[12] = $value['cara_bayar'];

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
            'service' => 'Sirs-LaporanCaraPulangPasienExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    private function loadData()
    {
        $request = $this->filter;

        $model   = new LaporanCaraPulangV;
        $query   = $model::find();
        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');

        if(isset($request['advanced-filter'])) {
            if(isset($request['advanced-filter']['tgl_pulang'])) {
                $explode = explode(" - ", $request['advanced-filter']['tgl_pulang']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($request['advanced-filter']['tgl_pulang']);
            }

            if (array_key_exists('instalasi_id', $request['advanced-filter'])) {
                $query->andWhere(['instalasi_id' => $request['advanced-filter']['instalasi_id']]);

                unset($request['advanced-filter']['instalasi_id']);
            }

            if (array_key_exists('ruangan_id', $request['advanced-filter'])) {
                $query->andWhere(['ruangan_id' => $request['advanced-filter']['ruangan_id']]);

                unset($request['advanced-filter']['ruangan_id']);
            }

            if (array_key_exists('carapulang_id', $request['advanced-filter'])) {
                $query->andWhere(['carapulang_id' => $request['advanced-filter']['carapulang_id']]);

                unset($request['advanced-filter']['carapulang_id']);
            }

            if (array_key_exists('kondisipulang_id', $request['advanced-filter'])) {
                $query->andWhere(['kondisipulang_id' => $request['advanced-filter']['kondisipulang_id']]);

                unset($request['advanced-filter']['kondisipulang_id']);
            }

            if (array_key_exists('no_rekam_medik', $request['advanced-filter'])) {
                $query->andWhere(['ilike', 'no_rekam_medik', $request['advanced-filter']['no_rekam_medik']]);

                unset($request['advanced-filter']['no_rekam_medik']);
            }

            if (array_key_exists('no_registrasi', $request['advanced-filter'])) {
                $query->andWhere(['ilike', 'no_registrasi', $request['advanced-filter']['no_registrasi']]);

                unset($request['advanced-filter']['no_registrasi']);
            }

            if (array_key_exists('nama_pasien', $request['advanced-filter'])) {
                $query->andWhere(['ilike', 'nama_pasien', $request['advanced-filter']['nama_pasien']]);

                unset($request['advanced-filter']['nama_pasien']);
            }

            if (array_key_exists('rumahsakit_rujukan', $request['advanced-filter'])) {
                $query->andWhere(['ilike', 'rumahsakit_rujukan', $request['advanced-filter']['rumahsakit_rujukan']]);
    
                unset($request['advanced-filter']['rumahsakit_rujukan']);
            }

            if (array_key_exists('carabayar_id', $request['advanced-filter'])) {
                $query->andWhere(['carabayar_id' => $request['advanced-filter']['carabayar_id']]);

                unset($request['advanced-filter']['carabayar_id']);
            }
        }

        $query->andWhere(['between', 'tgl_pulang', $start, $end]);

        return DocoRestActiveFilter::advancedFilter($model, $query, $request);
    }
}
