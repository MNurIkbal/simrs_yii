<?php 

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\LaporanPenjualanObatalkesView;
use Integrasi\Service\Sirs\Models\Pegawai;
use Integrasi\Components\DocoRestActiveFilter;
use Integrasi\Components\DocoConstants;
use yii\helpers\ArrayHelper;

class LaporanPenjualanResep extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $data = $this->loadData()->asArray()->all();
        $cacheFiles = Yii::$app->cacheFiles;
        $row = $tmpCache = [];
        $no = 1;
        $prefix = 0;
        $modelPegawai = new Pegawai;
        $queryPegawai = $modelPegawai::find()->all();
        $pegawai = ArrayHelper::map($queryPegawai, 'pegawai_id', 'nama_pegawai');
        foreach ($data as $value)  {
            $tmp[1]  = $no;
            $tmp[2]  = !empty($value['tgltransaksi']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['tgltransaksi']))) : '';
            $tmp[3]  = !empty($value['no_pendaftaran']) ? $value['no_pendaftaran'] : '';
            $tmp[4]  = !empty($value['jenis_resep']) ? $value['jenis_resep'] : '';
            $tmp[5]  = !empty($value['ruangan_nama']) ? $value['ruangan_nama'] : '';
            $tmp[6]  = !empty($value['noresep']) ? $value['noresep'] : '';
            $tmp[7]  = !empty($value['nama_dokter']) ? $value['nama_dokter'] : '';
            $tmp[8]  = !empty($value['no_rekammedik']) ? $value['no_rekammedik'] : '';
            $tmp[9]  = !empty($value['nama_pasien']) ? $value['nama_pasien'] : '-';
            $tmp[10]  = !empty($value['tanggal_lahir']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['tanggal_lahir']))) : '';
            $tmp[11]  = !empty($value['rke']) ? $value['rke'] : '';
            $tmp[12]  = !empty($value['kode_obat']) ? $value['kode_obat'] : '';
            $tmp[13]  = !empty($value['nama_obat']) ? $value['nama_obat'] : '';
            $tmp[14]  = !empty($value['jenisobatalkes_nama']) ? $value['jenisobatalkes_nama'] : '';
            $tmp[15] = !empty($value['jumlah_obat']) ? $value['jumlah_obat'] : 0;
            $tmp[16] = !empty($value['satuan']) ? $value['satuan'] : '';
            $tmp[17] = !empty($value['totaltagihan']) ? $value['totaltagihan'] : 0;
            $tmp[18] = !empty($value['status_reseptur_nama']) ? $value['status_reseptur_nama'] : '';
            $tmp[19] = !empty($value['carabayar_nama']) ? $value['carabayar_nama'] : '';
            $tmp[20] = !empty($value['penjamin_nama']) ? $value['penjamin_nama'] : '';
            $tmp[21] = $value['is_formularium'] ? 'Ya' : 'Tidak';
            $tmp[22] = $value['is_psycothropica'] ? 'Ya' : 'Tidak';
            $tmp[23] = $value['is_narcotic'] ? 'Ya' : 'Tidak';
            $tmp[24] = !empty($value['supplier']) ? $value['supplier'] : '';
            $tmp[25] = !empty($value['principle']) ? $value['principle'] : '';
            $tmp[26] = !empty($value['user']) ? $value['user'] : '';

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
            'service' => 'Sirs-LaporanPenjualanResep',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    private function loadData()
    {
        $request = $this->filter;
        $model = new LaporanPenjualanObatalkesView;
        $query = $model::find();
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        if(isset($request['advanced-filter'])) {
            if(isset($request['advanced-filter']['tgltransaksi'])) {
                $explode = explode(" - ", $request['advanced-filter']['tgltransaksi']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($request['advanced-filter']['tgltransaksi']);
            }

            if(isset($request['advanced-filter']['carabayar_nama'])){
                $carabayar_id = $request['advanced-filter']['carabayar_nama'];
                $query->andWhere(['carabayar_id' => $carabayar_id]);
                unset($request['advanced-filter']['carabayar_nama']);
            }

            if(isset($request['advanced-filter']['penjamin_nama'])){
                $penjamin_nama = $request['advanced-filter']['penjamin_nama'];
                $query->andWhere(['ILIKE', 'penjamin_nama', $penjamin_nama]);
                unset($request['advanced-filter']['penjamin_nama']);
            }

            if(isset($request['advanced-filter']['jenispenjualan'])){
                $jenispenjualan = $request['advanced-filter']['jenispenjualan'];
                $query->andWhere(['jenispenjualan' => $jenispenjualan]);
            }

            if(isset($request['advanced-filter']['noresep'])){
                $noresep = $request['advanced-filter']['noresep'];
                $query->andWhere(['ILIKE', 'noresep', $noresep]);
            }

            if(isset($request['advanced-filter']['no_rekammedik'])){
                $no_rekammedik = $request['advanced-filter']['no_rekammedik'];
                $query->andWhere(['ILIKE', 'no_rekammedik', $no_rekammedik]);
            }

            if(isset($request['advanced-filter']['nama_pasien'])){
                $nama_pasien_filter = $request['advanced-filter']['nama_pasien'];
                $query->andWhere(['ILIKE', 'nama_pasien', $nama_pasien_filter]);
            }
            if(isset($request['advanced-filter']['jenisobatalkes_nama'])){
                $jenisobatalkes_nama_filter = $request['advanced-filter']['jenisobatalkes_nama'];
                $query->andWhere(['ILIKE', 'jenisobatalkes_nama', $jenisobatalkes_nama_filter]);
            }

            if(isset($request['advanced-filter']['tanggal_lahir'])){
                $tanggal_lahir = $request['advanced-filter']['tanggal_lahir'];
                $tanggalMew = date('Y-m-d', strtotime($tanggal_lahir));
                $query->andWhere(['tanggal_lahir' => $tanggalMew]);
            }
        }
        
        $query->andWhere(['between', 'tgltransaksi', $start, $end]);
        $query->orderBy($request['order']);

        return DocoRestActiveFilter::advancedFilter($model, $query, $request);
    }
}
