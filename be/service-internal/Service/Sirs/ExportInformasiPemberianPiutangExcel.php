<?php

namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoConstants;

class ExportInformasiPemberianPiutangExcel extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        ini_set('memory_limit', '-1');
        $totalPerPage = $this->totalPerPage;
        $cacheFiles = Yii::$app->cacheFiles;
        $filter = $this->filter;
        $row = $footer = [];
        $no = 1;
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Sedang mengekstrak data Informasi Pemberian Piutang',
                'progress' => 80
            ]),
        ]);

        for ($x = 0; $x < $totalPerPage; $x++) {
            $data = $cacheFiles->get($this->unique_str . '-' . $x);
            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    $row[] = $value;
                }
            }
            $cacheFiles->delete($this->unique_str . '-' . $x);
        }

        $start = date('Y-m-d 00:00:00', strtotime('-1 months'));
        $end = date('Y-m-d 23:59:00');
        $outStart = date('Y-m-d 00:00:00', strtotime('-1 months'));
        $outEnd = date('Y-m-d 23:59:00');
        $db = Yii::$app->db;
        $status_piutang_nama = $nama_pegawai = '-';
        if (isset($filter['advanced-filter'])) {
            $advancedFilter = $filter['advanced-filter'];
            if (isset($advancedFilter['tgl_pemberianpiutang'])) {
                $explode = explode(" - ", $advancedFilter['tgl_pemberianpiutang']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($advancedFilter['tgl_pemberianpiutang']);
            }
            if (isset($advancedFilter['tglpasienpulang'])) {
                $explode = explode(" - ", $advancedFilter['tglpasienpulang']);
                if (count($explode) == 2) {
                    $outStart = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $outEnd = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($advancedFilter['tglpasienpulang']);
            }
            if (isset($advancedFilter['pegawaidibebankan_id'])) {
                $pegawai_id = $advancedFilter['pegawaidibebankan_id'];
                $data = $db->createCommand("
                    SELECT nama_pegawai, nomorindukpegawai
                    FROM pegawai_m
                    WHERE pegawai_id = {$pegawai_id}
                ")->queryOne();
                $nama_pegawai = $data['nomorindukpegawai'] . '-' . $data['nama_pegawai'];
            }
            if (isset($advancedFilter['status_piutang'])) {
                $status_piutang_nama = ($advancedFilter['status_piutang'] == DocoConstants::LUNAS) ? "Lunas" : "BELUM LUNAS";
            }
        }

        $header = [
            'Periode Tanggal Pemberian Piutang' => date('d M Y', strtotime($start)) . ' Sampai Dengan ' . date('d M Y', strtotime($end)),
            'Periode Tgl Masuk - Tgl Pulang' => date('d M Y', strtotime($outStart)) . ' Sampai Dengan ' . date('d M Y', strtotime($outEnd)),
            'Nama Pasien' => isset($filter['advanced-filter']['nama_pasien']) ? $filter['advanced-filter']['nama_pasien'] : '-',
            'No Rekam Medik' => isset($filter['advanced-filter']['no_rekam_medik']) ? $filter['advanced-filter']['no_rekam_medik'] : '-',
            'Nama Karyawan' => $nama_pegawai,
            'No Pendaftaran' => isset($filter['advanced-filter']['no_pendaftaran']) ? $filter['advanced-filter']['no_pendaftaran'] : '-',
            'Status' => $status_piutang_nama,
        ];
        $custHeader = $this->custHeader();
        $path = 'uploads/' . $this->unique_str . '.xlsx';
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Sedang mengimport data ke dalam excel',
                'progress' => 85
            ]),
        ]);
        $filePath = DocoHelpers::exportExcel('Informasi Pemberian Piutang', $row, $header, [
            "skipIncrement" => true,
            'customHeader' => $custHeader,
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
            'service' => 'Sirs-ExportInformasiPemberianPiutangExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function custHeader()
    {
        return [
            [
                [
                    'label' => 'No',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Tanggal Pemberian Piutang',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Nama Pasien',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Nama Karyawan',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'No Rekam Medis',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'No Pendaftaran',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Tanggal Masuk',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Tanggal Pulang',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Piutang (Rp.)',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Sudah Dibayar (Rp.)',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Sisa Piutang (Rp.)',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Status',
                    'rowspan' => 2,
                ]
            ]
        ];
    }
}
