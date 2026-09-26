<?php

namespace Integrasi\Service\Sirs\Rm\LapKunjunganPenunjangExcel;

use Exception;
use Yii;
use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoHelpers;
use Integrasi\Contracts\DocoImplement;

class ExportLapKunjunganPenunjangExcel extends DocoImplement
{
    private $totalJenisPemeriksaan;
    private $totalNamaPemeriksaan;
    private $totalJumlah;

    public function execute()
    {
        $this->totalJenisPemeriksaan = ArrayHelper::getValue($this->footerData, 'jumlah_jeniskegiatan', 0);
        $this->totalNamaPemeriksaan = ArrayHelper::getValue($this->footerData, 'jumlah_tindakan', 0);
        $this->totalJumlah = ArrayHelper::getValue($this->footerData, 'jumlah_hasil', 0);

        ini_set('memory_limit', '-1');
        $totalPerPage = $this->totalPerPage;
        $cacheFiles = Yii::$app->cacheFiles;
        $filter = $this->filter;
        $row = $footer = $fvalue = [];
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Sedang mengekstrak data',
                'progress' => 80
            ]),
        ]);

        for ($x = 0; $x < $totalPerPage; $x++) {
            $data = $cacheFiles->get($this->unique_str . '-' . $x);
            if (!empty($data)) {
                foreach ($data as $value) {
                    if (isset($filter['advanced-filter'])) {
                        foreach ($value as $fk => $fv) {
                        }
                    }
                    $row[] = $value;
                }
            }
            $cacheFiles->delete($this->unique_str . '-' . $x);
        }
        $header = $this->getHeaderNameByRequest();
        $custHeader = $this->custHeader();
        $custNamedSkippedHeader = $this->custNamedSkippedHeader();
        $footer = $this->cusFooter();
        $path = 'uploads/' . $this->unique_str . '.xlsx';

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Sedang mengimport data ke dalam excel',
                'progress' => 85
            ]),
        ]);

        $filePath = DocoHelpers::exportExcel('Laporan Kunjungan Penunjang', $row, $header, [
            "skipIncrement" => true,
            "skipHeader" => true,
            "customHeader" => $custHeader,
            "nameHeaderSkipped" => $custNamedSkippedHeader,
        ], $footer, [], true);

        $filePath->save($path);

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Proses import excel berhasil',
                'progress' => 90
            ]),
        ]);

        return json_encode([
            'service' => 'Sirs-ExportLapKunjunganPenunjangExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    private function getHeaderNameByRequest()
    {
        $db = Yii::$app->db;
        $filter = $this->filter;
        $advancedFilter = ArrayHelper::getValue($filter, 'advanced-filter');
        $startTglMasukPenunjang = date('Y-m-d');
        $endTglMasukPenunjang = date('Y-m-d');
        $tglMasukPenunjang = ArrayHelper::getValue($advancedFilter, 'tglmasukpenunjang');
        $noPendaftaran = ArrayHelper::getValue($advancedFilter, 'no_pendaftaran');
        $noRm = ArrayHelper::getValue($advancedFilter, 'no_rekam_medik');
        $namaPasien = ArrayHelper::getValue($advancedFilter, 'nama_pasien');
        $unit = ArrayHelper::getValue($advancedFilter, 'unit');
        $namaPemeriksaan = ArrayHelper::getValue($advancedFilter, 'daftartindakan_nama');
        $jenisPemeriksaan = ArrayHelper::getValue($advancedFilter, 'jeniskegiatantindakan_nama');
        $instalasiId = ArrayHelper::getValue($advancedFilter, 'instalasi_nama');
        $caraBayarId = ArrayHelper::getValue($advancedFilter, 'carabayar_id');
        $penjaminId = ArrayHelper::getValue($advancedFilter, 'penjamin_nama');

        // Header Default
        $headerNoPendaftaran = $noPendaftaran ? $noPendaftaran : '-';
        $headerCaraBayar = '-';
        $headerPenjamin = '-';
        $headerNoRm = $noRm ? $noRm : '-';
        $headerNamaPasien = $namaPasien ? $namaPasien : '-';
        $headerInstalasi = '-';
        $headerUnit = $unit ? $unit : '-';
        $headerJenisPemeriksaan = $jenisPemeriksaan ? $jenisPemeriksaan : '-';
        $headerNamaPemeriksaan = $namaPemeriksaan ? $namaPemeriksaan : '-';

        $tglMasukPenunjangRange = DocoHelpers::parsingRangeDate($tglMasukPenunjang);
        $startTglMasukPenunjang = date('j-M-Y', strtotime($tglMasukPenunjangRange['startDate']));
        $endTglMasukPenunjang = date('j-M-Y', strtotime($tglMasukPenunjangRange['endDate']));

        if ($instalasiId) {
            $data = $db->createCommand("SELECT instalasi_nama FROM instalasi_m WHERE instalasi_id = {$instalasiId}")->queryOne();
            $headerInstalasi = $data['instalasi_nama'];
        }
        if ($caraBayarId) {
            $data = $db->createCommand("SELECT carabayar_nama FROM carabayar_m WHERE carabayar_id = {$caraBayarId}")->queryOne();
            $headerCaraBayar = $data['carabayar_nama'];
        }
        if ($penjaminId) {
            $data = $db->createCommand("SELECT penjamin_nama FROM penjamin_m WHERE penjamin_id = {$penjaminId}")->queryOne();
            $headerPenjamin = $data['penjamin_nama'];
        }

        $header = [
            'Tanggal Masuk' => $startTglMasukPenunjang . ' Sampai Dengan ' . $endTglMasukPenunjang,
            'No. Pendaftaran' => $headerNoPendaftaran,
            'Cara Bayar' => $headerCaraBayar,
            'Penjamin' => $headerPenjamin,
            'No. Rekam Medik' => $headerNoRm,
            'Nama Pasien' => $headerNamaPasien,
            'Instalasi' => $headerInstalasi,
            'Unit' => $headerUnit,
            'Jenis Pemeriksaan' => $headerJenisPemeriksaan,
            'Nama Pemeriksaan' => $headerNamaPemeriksaan,
            'Total Jenis Pemeriksaan' => $this->totalJenisPemeriksaan,
            'Total Nama Pemeriksaan' => $this->totalNamaPemeriksaan,
            'Total Jumlah' =>  $this->totalJumlah,
        ];
        return $header;
    }

    private function custHeader()
    {
        $staticHeader = [
            [
                ['label' => 'No', 'rowspan' => 2],
                ['label' => 'Tanggal Masuk', 'rowspan' => 2],
                ['label' => 'No Pendaftaran', 'rowspan' => 2],
                ['label' => 'No Rekam Medik', 'rowspan' => 2],
                ['label' => 'Nama Pasien', 'rowspan' => 2],
                ['label' => 'Unit', 'rowspan' => 2],
                ['label' => 'Instalasi', 'rowspan' => 2],
                ['label' => 'Cara Bayar / Penjamin', 'rowspan' => 2],
                ['label' => 'Jenis Pemeriksaan', 'rowspan' => 2],
                ['label' => 'Nama Pemeriksaan', 'rowspan' => 2],
                ['label' => 'Jumlah', 'rowspan' => 2],
            ],
        ];
        return $staticHeader;
    }

    private function custNamedSkippedHeader()
    {
        $custNamedSkippedHeader = [
            'No',
            'Tanggal Masuk',
            'No Pendaftaran',
            'No Rekam Medik',
            'Nama Pasien',
            'Unit',
            'Instalasi',
            'Cara Bayar / Penjamin',
            'Jenis Pemeriksaan',
            'Nama Pemeriksaan',
            'Jumlah',
        ];
        return $custNamedSkippedHeader;
    }

    private function cusFooter()
    {
        $footer = [
            'title' => ['TOTAL', 7],
            'data' => [
                'Jenis Pemeriksaan' => "Total Jenis Pemeriksaan : $this->totalJenisPemeriksaan",
                'Nama Pemeriksaan' => "Total Nama Pemeriksaan : $this->totalNamaPemeriksaan",
                'Jumlah' => "Total Jumlah : $this->totalJumlah",
            ]
        ];
        return $footer;
    }
}
