<?php

namespace Integrasi\Service\Sirs\Rm\LapKunjunganPenunjangExcel;

use Yii;
use yii\helpers\ArrayHelper;
use Integrasi\Contracts\DocoImplement;
use Exception;


class LapKunjunganPenunjangExcel extends DocoImplement
{
    private $pointer = 0;
    private $no = 1;
    private $tmpCache = [];
    private $batchMaxNumber = 100;

    public function execute()
    {
        $data = LoadDataLapKunjunganPenunjangExcel::queryLoadData($this->filter)->asArray()->all();
        $cacheFiles = Yii::$app->cacheFiles;

        foreach ($data as $value) {
            $tglMasukPenunjang = ArrayHelper::getValue($value, 'tglmasukpenunjang', '-');
            if ($tglMasukPenunjang) $tglMasukPenunjang = date('Y-m-d H:i:s', strtotime($tglMasukPenunjang));
            $noPendaftaran = ArrayHelper::getValue($value, 'no_pendaftaran', '-');
            $noRekamMedik = ArrayHelper::getValue($value, 'no_rekam_medik', '-');
            $namaPasien = ArrayHelper::getValue($value, 'nama_pasien', '-');
            $unit = ArrayHelper::getValue($value, 'unit', '-');
            $instalasiNama = ArrayHelper::getValue($value, 'instalasi_nama', '-');
            $ruanganNama = ArrayHelper::getValue($value, 'ruangan_nama', '-');
            $caraBayarNama = ArrayHelper::getValue($value, 'carabayar_nama', '-');
            $penjaminNama = ArrayHelper::getValue($value, 'penjamin_nama', '-');
            $caraBayarPenjamin = "$caraBayarNama /\n$penjaminNama";
            $jenisPemeriksaanNama = ArrayHelper::getValue($value, 'jeniskegiatantindakan_nama', '-');
            $daftarTindakanNama = ArrayHelper::getValue($value, 'daftartindakan_nama', '-');
            $jumlah = ArrayHelper::getValue($value, 'jumlah_tindakan', '-');

            $tmp[1] = $this->no;
            $tmp[2] = $tglMasukPenunjang;
            $tmp[3] = $noPendaftaran;
            $tmp[4] = $noRekamMedik;
            $tmp[5] = $namaPasien;
            $tmp[6] = $unit;
            $tmp[7] = $instalasiNama;
            $tmp[8] = $caraBayarPenjamin;
            $tmp[9] = $jenisPemeriksaanNama;
            $tmp[10] = $daftarTindakanNama;
            $tmp[11] = $jumlah;
            $this->tmpCache[] = $tmp;

            if (($this->no % $this->batchMaxNumber) == 0) {
                Yii::$app->redis->executeCommand('PUBLISH', [
                    'channel' => 'export-excel:' . $this->unique_str,
                    'message' => json_encode(['unique_process' => $this->unique_str]),
                ]);
                $cacheFiles->set($this->unique_str . '-' . $this->pointer, $this->tmpCache);
                $this->pointer++;
                $this->tmpCache = [];
            }
            $this->no++;
        }

        if (!empty($this->tmpCache)) {
            $cacheFiles = Yii::$app->cacheFiles;
            $cacheFiles->set($this->unique_str . '-' . $this->pointer, $this->tmpCache);
        }

        return json_encode([
            'service' => 'Sirs-LapKunjunganPenunjangExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }
}
