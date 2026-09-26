<?php

namespace Integrasi\Service\Sirs;

use Integrasi\Service\Sirs\Models\LaporanKunjunganPasienFisioterapiRi;
use Yii;
use GuzzleHttp\Client;

use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\PemeriksaanPasienRadiologiView;
use Integrasi\Components\DocoRestActiveFilter;
use Integrasi\Components\DocoConstants;
use yii\helpers\ArrayHelper;

class LapKunjunganFisioRajalExcel extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $data = LoadDataLapKunjunganFisioRajal::queryLoadData($this->filter)->asArray()->all();
        $cacheFiles = Yii::$app->cacheFiles;
        $tmpCache = [];
        $no = 1;
        $prefix = 0;
        foreach ($data as $value) {
            $nama_pasien = ArrayHelper::getValue($value, 'nama_pasien');
            $jeniskelamin_nama = ArrayHelper::getValue($value, 'jeniskelamin_nama');
            $jeniskelamin_kode = ArrayHelper::getValue($value, 'jeniskelamin_kode');
            $no_pendaftaran = ArrayHelper::getValue($value, 'no_pendaftaran');
            $no_rekam_medik = ArrayHelper::getValue($value, 'no_rekam_medik');
            $tgl_pendaftaran = ArrayHelper::getValue($value, 'tgl_pendaftaran');
            $tanggal_lahir = ArrayHelper::getValue($value, 'tanggal_lahir');
            $carabayar_nama = ArrayHelper::getValue($value, 'carabayar_nama');
            $penjamin_nama = ArrayHelper::getValue($value, 'penjamin_nama');
            $carabayar_penjamin = "$carabayar_nama /\n$penjamin_nama";
            $instalasi_nama = ArrayHelper::getValue($value, 'instalasi_nama');
            $ruangan_nama = ArrayHelper::getValue($value, 'ruangan_nama');
            $instalasi_ruangan = "$instalasi_nama / \n$ruangan_nama";
            $dokter = ArrayHelper::getValue($value, 'dokterdpjp_nama');
            $status_periksa_nama = ArrayHelper::getValue($value, 'status_periksa_nama');
            $no_telepon_pasien = ArrayHelper::getValue($value, 'no_telepon_pasien');
            if ($tanggal_lahir) $tanggal_lahir = date('d-M-Y', strtotime($value['tanggal_lahir'])) ?? '-';
            $data_pasien = "$nama_pasien\n($jeniskelamin_kode)\n$no_rekam_medik";
            $alamat = ArrayHelper::getValue($value, 'alamat_pasien', '-') ?? '-';
            $alamatImploded = $this->newLinerWord($alamat);
            $tmp[1] = $no;
            $tmp[2] = $tgl_pendaftaran;
            $tmp[3] = $data_pasien;
            $tmp[4] = $tanggal_lahir;
            $tmp[5] = $alamatImploded;
            $tmp[6] = $carabayar_penjamin;
            $tmp[7] = $instalasi_ruangan;
            $tmp[8] = $dokter;
            $tmp[9] = $status_periksa_nama;
            $tmp[10]  = $no_telepon_pasien;
            $tmpCache[] = $tmp;
            if (($no % 50) == 0) {
                Yii::$app->redis->executeCommand('PUBLISH', [
                    'channel' => 'export-excel:' . $this->unique_str,
                    'message' => json_encode(['unique_process' => $this->unique_str]),
                ]);
                $cacheFiles->set($this->unique_str . '-' . $prefix, $tmpCache);
                $prefix++;
                $tmpCache = [];
            }
            $no++;
        }

        $cacheFiles->set($this->unique_str . '-' . $prefix, $tmpCache);

        return json_encode([
            'service' => 'Sirs-LapKunjunganFisioRajalExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    private function newLinerWord($word, $threshold = 5)
    {
        $wordExploded = explode(" ", $word);
        $counter = 1;
        $wordImploded = "";
        foreach ($wordExploded as $key => $value) {
            if (($counter % $threshold) == 0) {
                $wordImploded = "$wordImploded\n$value";
                $counter = 0;
            } else {
                $wordImploded = "$wordImploded $value";
            }
            $counter++;
        }
        return $wordImploded;
    }
}
