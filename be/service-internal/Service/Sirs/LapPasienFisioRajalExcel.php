<?php

namespace Integrasi\Service\Sirs;

use Yii;
use yii\helpers\ArrayHelper;
use Integrasi\Contracts\DocoImplement;

class LapPasienFisioRajalExcel extends DocoImplement
{
    public function execute()
    {
        $data = LoadDataLapPasienFisioRajal::queryLoadData($this->filter)->asArray()->all();
        $cacheFiles = Yii::$app->cacheFiles;
        $tmpCache = [];
        $no = 1;
        $prefix = 0;
        foreach ($data as $value) {
            $tglPendaftaran = ArrayHelper::getValue($value, 'tgl_pendaftaran');
            $noRekamMedik = ArrayHelper::getValue($value, 'no_rekam_medik');
            $namaPasien = ArrayHelper::getValue($value, 'nama_pasien');
            $jenisKelamin = ArrayHelper::getValue($value, 'jeniskelamin_kode');
            $dataPasien = "$namaPasien\n($jenisKelamin)\n$noRekamMedik";
            $dokterPerujuk = ArrayHelper::getValue($value, 'dokterperujuk_nama');
            $ruanganAsal = ArrayHelper::getValue($value, 'ruangan_nama');
            $program = ArrayHelper::getValue($value, 'jenispemeriksaanfisio_nama');
            $frekuensi = ArrayHelper::getValue($value, 'frekuensi');
            $realisasi = ArrayHelper::getValue($value, 'realisasi');
            $sisaTerapi = ArrayHelper::getValue($value, 'sisa');
            $tidakHadir = ArrayHelper::getValue($value, 'jumlah_ketidakhadiran');
            $statusProgram = ArrayHelper::getValue($value, 'status_program_fisio_nama');
            $tglPenjadwalanAwal = ArrayHelper::getValue($value, 'tgl_penjadwalan_awal');
            $tglPenjadwalanAkhir = ArrayHelper::getValue($value, 'tgl_penjadwalan_akhir');
            $tglPenjadwalan = $tglPenjadwalanAwal . " - " . $tglPenjadwalanAkhir;
            $tglRealisasi = ArrayHelper::getValue($value, 'tgl_realisasi');
            $terapisNama = ArrayHelper::getValue($value, 'terapis_nama');

            $tmp[1] = $no;
            $tmp[2] = $tglPendaftaran;
            $tmp[3] = $dataPasien;
            $tmp[4] = $dokterPerujuk;
            $tmp[5] = $ruanganAsal;
            $tmp[6] = $program;
            $tmp[7] = $frekuensi;
            $tmp[8] = $realisasi;
            $tmp[9] = $sisaTerapi;
            $tmp[10] = $tidakHadir;
            $tmp[11] = $statusProgram;
            $tmp[12] = $tglPenjadwalan;
            $tmp[13] = $tglRealisasi;
            $tmp[14] = $terapisNama;
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
            'service' => 'Sirs-LapPasienFisioRajalExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }
}
