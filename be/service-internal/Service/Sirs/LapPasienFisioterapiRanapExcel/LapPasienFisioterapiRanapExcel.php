<?php

namespace Integrasi\Service\Sirs\LapPasienFisioterapiRanapExcel;

use Yii;
use yii\helpers\ArrayHelper;
use Integrasi\Contracts\DocoImplement;

class LapPasienFisioterapiRanapExcel extends DocoImplement
{
    public function execute()
    {
        $data = LoadDataLapPasienFisioterapiRanapExcel::queryLoadData($this->filter)->asArray()->all();
        $cacheFiles = Yii::$app->cacheFiles;
        $tmpCache = [];
        $no = 1;
        $prefix = 0;

        foreach ($data as $value) {
            $tglPendaftaran = ArrayHelper::getValue($value, 'tgl_pendaftaran', '-');
            if ($tglPendaftaran) $tglPendaftaran = date('Y-m-d H:i:s', strtotime($tglPendaftaran));
            $namaPasien = ArrayHelper::getValue($value, 'nama_pasien', '-');
            $noRekamMedik = ArrayHelper::getValue($value, 'no_rekam_medik', '-');
            $dataPasien = "$namaPasien /\n$noRekamMedik";
            $dokterPerujukNama = ArrayHelper::getValue($value, 'dokterperujuk_nama', '-');
            $dokterDpjpNama = ArrayHelper::getValue($value, 'dokterdpjp_nama', '-');
            $ruanganAsalNama = ArrayHelper::getValue($value, 'ruangan_nama', '-');
            $program = ArrayHelper::getValue($value, 'jenispemeriksaanfisio_nama', '-');
            $frekuensi = ArrayHelper::getValue($value, 'frekuensi', '-');
            $realisasi = ArrayHelper::getValue($value, 'realisasi', '-');
            $sisa = ArrayHelper::getValue($value, 'sisa', '-');
            $ketidakHadiran = ArrayHelper::getValue($value, 'jumlah_ketidakhadiran', '-');
            $statusProgram = ArrayHelper::getValue($value, 'status_program_fisio_nama', '-');
            $tglRealisasi = ArrayHelper::getValue($value, 'tgl_realisasi', '-');
            if ($tglRealisasi) $tglRealisasi = date('Y-m-d H:i:s', strtotime($tglRealisasi));
            $terapis = ArrayHelper::getValue($value, 'terapis_nama', '-');
            $tglPenjadwalanAwal = ArrayHelper::getValue($value, 'tgl_penjadwalan_awal');
            if ($tglPenjadwalanAwal) $tglPenjadwalanAwal = date('Y-m-d H:i:s', strtotime($tglPenjadwalanAwal));
            $tglPenjadwalanAkhir = ArrayHelper::getValue($value, 'tgl_penjadwalan_akhir');
            if ($tglPenjadwalanAkhir) $tglPenjadwalanAkhir = date('Y-m-d H:i:s', strtotime($tglPenjadwalanAkhir));
            $penjadwalan = "$tglPenjadwalanAwal - $tglPenjadwalanAkhir";
            $tmp[1] = $no;
            $tmp[2] = $tglPendaftaran;
            $tmp[3] = $dataPasien;
            $tmp[4] = $dokterPerujukNama;
            $tmp[5] = $dokterDpjpNama;
            $tmp[6] = $ruanganAsalNama;
            $tmp[7] = $program;
            $tmp[8] = $frekuensi;
            $tmp[9] = $realisasi;
            $tmp[10] = $sisa;
            $tmp[11] = $ketidakHadiran;
            $tmp[12] = $statusProgram;
            $tmp[13] = $penjadwalan;
            $tmp[14] = $tglRealisasi;
            $tmp[15] = $terapis;
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
            'service' => 'Sirs-LapPasienFisioterapiRanapExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }
}
