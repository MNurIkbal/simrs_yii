<?php

namespace Integrasi\Service\Sirs;

use Yii;
use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoHelpers;
use Integrasi\Contracts\DocoImplement;

class LapPemeriksaanFisioRajalExcel extends DocoImplement
{
    public function execute()
    {
        $data = LoadDataLapPemeriksaanFisioRajal::queryLoadData($this->filter)
            ->orderBy(['tgl_pendaftaran' => SORT_DESC])
            ->asArray()
            ->all();
        $cacheFiles = Yii::$app->cacheFiles;
        $tmpCache = [];
        $no = 1;
        $prefix = 0;
        foreach ($data as $value) {
            $nama_pasien = ArrayHelper::getValue($value, 'nama_pasien');
            $jeniskelamin_id = ArrayHelper::getValue($value, 'jeniskelamin_id');
            $jeniskelamin_kode = DocoHelpers::genderCode($jeniskelamin_id);
            $no_rekam_medik = ArrayHelper::getValue($value, 'no_rekam_medik');
            $tgl_pendaftaran = ArrayHelper::getValue($value, 'tgl_pendaftaran');
            $carabayar_nama = ArrayHelper::getValue($value, 'carabayar_nama');
            $penjamin_nama = ArrayHelper::getValue($value, 'penjamin_nama');
            $instalasi_nama = ArrayHelper::getValue($value, 'instalasi_nama');
            $ruangan_nama = ArrayHelper::getValue($value, 'ruangan_nama');
            $jenispemeriksaan_nama = ArrayHelper::getValue($value, 'jenispemeriksaanfisio_nama');
            $daftartindakan_nama = ArrayHelper::getValue($value, 'daftartindakan_nama');
            $dokterterapis_nama = ArrayHelper::getValue($value, 'terapis_nama');
            $dokterdpjp_nama = ArrayHelper::getValue($value, 'dokterdpjp_nama');
            $instalasi_ruangan = "$instalasi_nama / \n$ruangan_nama";
            $carabayar_penjamin = "$carabayar_nama /\n$penjamin_nama";
            $data_pasien = "$nama_pasien\n($jeniskelamin_kode)\n$no_rekam_medik";
            $tmp[1] = $no;
            $tmp[2] = $tgl_pendaftaran;
            $tmp[3] = $data_pasien;
            $tmp[4] = $carabayar_penjamin;
            $tmp[5] = $instalasi_ruangan;
            $tmp[6] = $dokterterapis_nama;
            $tmp[7] = $dokterdpjp_nama;
            $tmp[8] = $jenispemeriksaan_nama;
            $tmp[9] = $daftartindakan_nama;
            $tmp[10] = 1;
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
            'service' => 'Sirs-LapPemeriksaanFisioRajalExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }
}
