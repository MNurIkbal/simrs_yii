<?php

namespace Integrasi\Service\Sirs;

use Yii;
use yii\helpers\ArrayHelper;
use Integrasi\Contracts\DocoImplement;

class LapDropOutPasienFisioExcel extends DocoImplement
{
    public function execute()
    {
        $data = LoadDataLapDropOutPasienFisio::queryLoadData($this->filter)->asArray()->all();
        $cacheFiles = Yii::$app->cacheFiles;
        $tmpCache = [];
        $no = 1;
        $prefix = 0;

        foreach ($data as $value) {
            $tglPermintaan = ArrayHelper::getValue($value, 'tgl_rujukan', '-');
            $namaPasien = ArrayHelper::getValue($value, 'nama_pasien', '-');
            $jenisKelamin = ArrayHelper::getValue($value, 'jenis_kelamin', '-');
            $tglLahir = ArrayHelper::getValue($value, 'tanggal_lahir', '-');
            $noRekamMedik = ArrayHelper::getValue($value, 'no_rekam_medik', '-');
            $daftarTindakan = ArrayHelper::getValue($value, 'daftartindakan_nama', '-');
            $frekuensi = ArrayHelper::getValue($value, 'frekuensi', '-');
            $realisasi = ArrayHelper::getValue($value, 'realisasi', '-');
            $sisa = ArrayHelper::getValue($value, 'sisa', '-');
            $jumlahTidakHadir = ArrayHelper::getValue($value, 'jumlah_ketidakhadiran', '-');
            $statusProgram = ArrayHelper::getValue($value, 'status_program_fisio_nama', '-');
            $data_pasien = "$namaPasien ($jenisKelamin)\n$tglLahir\n$noRekamMedik";

            $tmp[1] = $no;
            $tmp[2] = $tglPermintaan;
            $tmp[3] = $data_pasien;
            $tmp[4] = $daftarTindakan;
            $tmp[5] = $frekuensi;
            $tmp[6] = $realisasi;
            $tmp[7] = $sisa;
            $tmp[8] = $jumlahTidakHadir;
            $tmp[9] = $statusProgram;
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
            'service' => 'Sirs-LapDropOutPasienFisioExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }
}
