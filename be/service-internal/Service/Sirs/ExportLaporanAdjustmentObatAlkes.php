<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Integrasi\Service\Sirs;

use Yii;
use Integrasi\Components\DocoHelpers;

class ExportLaporanAdjustmentObatAlkes extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        ini_set('memory_limit', '-1');
        $totalPerPage = $this->totalPerPage;
        $cacheFiles = Yii::$app->cacheFiles;
        $filter = $this->filter;
        $row = $footer = [];
        $title = 'Laporan Adjustment Obat Alkes';
        
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Sedang mengekstrak data ' . $title,
                'progress' => 80
            ]),
        ]);

        for ($x = 0; $x < $totalPerPage; $x++) {
            $data = $cacheFiles->get($this->unique_str . '-' . $x);
            if (!empty($data)) {
                foreach ($data as $key => $obj) {
                    $row[] = $obj;
                }
            }
            $cacheFiles->delete($this->unique_str . '-' . $x);
        }

        $start = date('d M Y');
        $end = date('d M Y');
        $ruangan = $jenis_adjustment = $jenis_obatalkes = "";
        if (isset($filter['advanced-filter'])) {
            if (isset($filter['advanced-filter']['tgl_adjusmen'])) {
                $explode = explode(" - ", $filter['advanced-filter']['tgl_adjusmen']);
                if (count($explode) == 2) {
                    $start = date('d M Y', strtotime($explode[0]));
                    $end = date('d M Y', strtotime($explode[1]));
                }
                unset($filter['advanced-filter']['tgl_adjusmen']);
            }

            if (isset($filter['advanced-filter']['ruangan_nama'])) {
                $ruangan = $filter['advanced-filter']['ruangan_nama'];
                unset($filter['advanced-filter']['ruangan_nama']);
            }

            if (isset($filter['advanced-filter']['jenis_adjusmen_nama'])) {
                $jenis_adjustment = $filter['advanced-filter']['jenis_adjusmen_nama'];
                unset($filter['advanced-filter']['jenis_adjusmen_nama']);
            }

            if (isset($filter['advanced-filter']['jenisobatalkes_nama'])) {
                $jenis_obatalkes = $filter['advanced-filter']['jenisobatalkes_nama'];
                unset($filter['advanced-filter']['jenisobatalkes_nama']);
            }
        }

        $header = [
            'Tanggal Adjustment' => $start . ' s/d ' . $end,
            'Ruangan' => $ruangan,
            'Jenis Adjustment' => $jenis_adjustment,
            'Jenis Obat Alkes' => $jenis_obatalkes
        ];

        $custHeader = $this->custHeader();
        $options = [
            "skipIncrement" => true,
            "customFormatCode" => [
                [
                    'selectColumn' => 'D',
                    'formatCode' => 'date'
                ]
            ],
            "customHeader" => $custHeader,
        ];

        $path = 'uploads/' . $this->unique_str . '.xlsx';
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Sedang mengimport data ke dalam excel',
                'progress' => 85
            ]),
        ]);

        $filePath = DocoHelpers::exportExcel($title, $row, $header, $options, $footer, [], true);
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
            'service' => 'Sirs-ExportLaporanAdjustmentObatAlkes',
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
                    'label' => 'Ruangan',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'No. Transaksi',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Tanggal Adjustment',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Jenis Adjustment',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Jenis Obat Alkes',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Kode Obat Alkes',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Nama Obat Alkes',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Qty',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Satuan',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Qty Konversi',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Satuan Terkecil',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Nama Pegawai',
                    'rowspan' => 2,
                ],
            ]
        ];
    }
}
