<?php 

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\LaporanRekapJasaDokterView;
use Integrasi\Service\Sirs\Models\PegawaiMasterView;
use Integrasi\Service\Sirs\Models\CaraBayar;
use Integrasi\Service\Sirs\Models\Penjamin;
use Integrasi\Service\Sirs\Models\Ruangan;
use Integrasi\Components\DocoRestActiveFilter;
use Integrasi\Components\DocoConstants;

class LaporanRekapJasaDokterExcel extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $data = $this->loadData();
        $cacheFiles = Yii::$app->cacheFiles;
        $row = $tmpCache = [];
        $no = 1;
        $prefix = 0;
        foreach ($data as $value)
        {
            $flag_jasdok = !empty($value['tgl_flag']) ? "Lunas" : "Belum Lunas";

            $tmp[1]  = $no;
            $tmp[2]  = !empty($value['tgl_tindakan']) ? date('d M Y', strtotime($value['tgl_tindakan'])) : '';
            $tmp[3]  = !empty($value['tgl_pasienpulang']) ? date('d M Y', strtotime($value['tgl_pasienpulang'])) : '';
            $tmp[4]  = !empty($value['tgl_flag']) ? date('d M Y', strtotime($value['tgl_flag'])) : '';
            $tmp[5]  = !empty($value['nama_pegawai']) ? $value['nama_pegawai'] : '';
            $tmp[6]  = !empty($value['status_bayar']) ? $value['status_bayar'] : '';
            $tmp[7]  = $flag_jasdok;
            $tmp[8]  = !empty($value['no_pendaftaran']) ? $value['no_pendaftaran'] : '';
            $tmp[9]  = !empty($value['no_rekam_medik']) ? $value['no_rekam_medik'] : '';
            $tmp[10]  = !empty($value['nama_pasien']) ? $value['nama_pasien'] : '';
            $tmp[11]  = !empty($value['ruangan_nama']) ? $value['ruangan_nama'] : '';
            $tmp[12]  = !empty($value['daftartindakan_nama']) ? $value['daftartindakan_nama'] : '';
            $tmp[13]  = !empty($value['komponentarif_nama']) ? $value['komponentarif_nama'] : '';
            $tmp[14]  = !empty($value['tarif_tindakan']) ? $value['tarif_tindakan'] : '';
            $tmp[15]  = !empty($value['tarif_tindakankomp']) ? $value['tarif_tindakankomp'] : '';
            $tmp[16]  = !empty($value['bruto']) ? $value['bruto'] : '';
            $tmp[17]  = !empty($value['dpp']) ? $value['dpp'] : '';
            $tmp[18]  = !empty($value['kondisi']) ? $value['kondisi'] : '-';
            $tmp[19]  = !empty($value['jenis_transaksi']) ? $value['jenis_transaksi'] : '';
            $tmp[20]  = !empty($value['carabayar_nama']) ? $value['carabayar_nama'] : '';
            $tmp[21]  = !empty($value['penjamin_nama']) ? $value['penjamin_nama'] : '';
            $tmp[22]  = !empty($value['kelaspelayanan_nama']) ? $value['kelaspelayanan_nama'] : '';
            $tmp[23]  = !empty($value['pelayanan']) ? $value['pelayanan'] : '';

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
            'service' => 'Sirs-LaporanRekapJasaDokterExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    private function loadData()
    {
        $request = $this->filter;
        $model = new LaporanRekapJasaDokterView;
        $query = $model::find(true);
        $start = date('Y-m-d 00:00:00');
        $end   = date('Y-m-d 23:59:00');
        $startPulang   = date('Y-m-d 00:00:00');
        $endPulang     = date('Y-m-d 23:59:00');
        if (isset($request['advanced-filter'])) {
            $advancedFilter = $request['advanced-filter'];
            if (isset($advancedFilter['tgl_tindakan']) && !empty($advancedFilter['tgl_tindakan'])) {
                $explode = explode(" - ", $advancedFilter['tgl_tindakan']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($request['advanced-filter']['tgl_tindakan']);
            }
            if (isset($advancedFilter['tgl_pasienpulang']) && !empty($advancedFilter['tgl_pasienpulang'])) {
                $explode = explode(" - ", $advancedFilter['tgl_pasienpulang']);
                if(count($explode) == 2) {
                    $startPulang = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $endPulang = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                $query->andWhere(['between', 'tgl_pasienpulang', $startPulang, $endPulang]);
                unset($request['advanced-filter']['tgl_pasienpulang']);
            }

            if(isset($advancedFilter['tgl_flag']) && !empty($advancedFilter['tgl_flag'])) {
                $startFlag = date('Y-m-d 00:00:00');
                $endFlag = date('Y-m-d 23:59:59');
        
                $explode = explode(" - ", $advancedFilter['tgl_flag']);
                if(count($explode) == 2) {
                    $startFlag = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $endFlag = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                $query->andWhere(['between', 'pembayaranjasadokter_t.tgl_flag', $startFlag, $endFlag]);
                unset($request['advanced-filter']['tgl_flag']);
            }

            if (isset($advancedFilter['status_bayar_id']) && !empty($advancedFilter['status_bayar_id'])) {
                $status_bayar_id = $advancedFilter['status_bayar_id'];
                $query->andWhere(['status_bayar_id' => $status_bayar_id]);
                unset($request['advanced-filter']['status_bayar_id']);
            }

            if (isset($advancedFilter['ruangan_id']) && !empty($advancedFilter['ruangan_id'])) {
                $ruangan_id = $advancedFilter['ruangan_id'];
                $query->andWhere(['ruangan_id' => $ruangan_id]);
                unset($request['advanced-filter']['ruangan_id']);
            }

            if (isset($advancedFilter['carabayar_id']) && !empty($advancedFilter['carabayar_id'])) {
                $carabayar_id = $advancedFilter['carabayar_id'];
                $query->andWhere(['carabayar_id' => $carabayar_id]);
                unset($request['advanced-filter']['carabayar_id']);
            }

            if (isset($advancedFilter['penjamin_id']) && !empty($advancedFilter['penjamin_id'])) {
                $penjamin_id = $advancedFilter['penjamin_id'];
                $query->andWhere(['penjamin_id' => $penjamin_id]);
                unset($request['advanced-filter']['penjamin_id']);
            }

            if (isset($advancedFilter['kelaspelayanan_id']) && !empty($advancedFilter['kelaspelayanan_id'])) {
                $kelaspelayanan_id = $advancedFilter['kelaspelayanan_id'];
                $query->andWhere(['kelaspelayanan_id' => $kelaspelayanan_id]);
                unset($request['advanced-filter']['kelaspelayanan_id']);
            }
            
            if (isset($advancedFilter['pelayanan']) && !empty($advancedFilter['pelayanan'])) {
                $pelayanan = $advancedFilter['pelayanan'];
                $query->andWhere(['pelayanan' => $pelayanan]);
                unset($request['advanced-filter']['pelayanan']);
            }

            if (isset($advancedFilter['dokterpenanggungjawab_id']) && !empty($advancedFilter['dokterpenanggungjawab_id'])) {
                $dokterpenanggungjawab_id = $advancedFilter['dokterpenanggungjawab_id'];
                $query->andWhere(['dokterpenanggungjawab_id' => $dokterpenanggungjawab_id]);
                unset($request['advanced-filter']['dokterpenanggungjawab_id']);
            }
            if(isset($advancedFilter['flag_jasdok']) && !empty($advancedFilter['flag_jasdok'])) {
                if($advancedFilter['flag_jasdok'] == 1) {
                    $query->andWhere(['not', ['pembayaranjasadokter_t.tgl_flag' => null]]);
                } else {
                    $query->andWhere(['pembayaranjasadokter_t.tgl_flag' => null]);
                }
                unset($request['advanced-filter']['flag_jasdok']);
            }
        }
        
        $query->andWhere(['between', 'tgl_tindakan', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query, $request);
        return $query->asArray()->all();
    }
}