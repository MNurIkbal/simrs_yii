<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Integrasi\Service\Sirs;

use Yii;
use Integrasi\Components\DocoHelpers;

class ExportLaporanHasilSOBarang extends \Integrasi\Contracts\DocoImplement {
    public function execute() {
        ini_set('memory_limit', '-1');
        $totalPerPage = $this->totalPerPage;
        $cacheFiles = Yii::$app->cacheFiles;
        $filter = $this->filter;
        $row = $footer = [];
        $title = 'Laporan Hasil Stok Opname Barang';
        
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
        $instalasi_ruangan = $no_form_so = '';
        if (isset($filter['advanced-filter'])) {
            if (isset($filter['advanced-filter']['tgl_form_so'])) {
                $explode = explode(" - ", $filter['advanced-filter']['tgl_form_so']);
                if (count($explode) == 2) {
                    $start = date('d M Y', strtotime($explode[0]));
                    $end = date('d M Y', strtotime($explode[1]));
                }
                unset($filter['advanced-filter']['tgl_form_so']);
            }

            if (isset($filter['advanced-filter']['instalasi_ruangan'])) {
                $instalasi_ruangan = $filter['advanced-filter']['instalasi_ruangan'];
                unset($filter['advanced-filter']['instalasi_ruangan']);
            }

            if (isset($filter['advanced-filter']['no_form_so'])) {
                $no_form_so = $filter['advanced-filter']['no_form_so'];
                unset($filter['advanced-filter']['no_form_so']);
            }
        }

        $header = [
            'Tanggal Form SO' => $start . ' s/d ' . $end,
            'Instalasi - Ruangan' => $instalasi_ruangan,
            'No. Form SO' => $no_form_so
        ];

        $custHeader = $this->custHeader();
        $options = [
            "skipIncrement" => true,
            "customFormatCode" => [
                [
                    'selectColumn' => 'B',
                    'formatCode' => 'datetime'
                ],
                [
                    'selectColumn' => 'C',
                    'formatCode' => 'datetime'
                ],
                [
                    'selectColumn' => 'D',
                    'formatCode' => 'datetime'
                ],
                [
                    'selectColumn' => 'M',
                    'formatCode' => 'number'
                ],
                [
                    'selectColumn' => 'N',
                    'formatCode' => 'number'
                ],
                [
                    'selectColumn' => 'O',
                    'formatCode' => 'number'
                ],
                [
                    'selectColumn' => 'P',
                    'formatCode' => 'number'
                ],
                [
                    'selectColumn' => 'Q',
                    'formatCode' => 'number'
                ],
                [
                    'selectColumn' => 'R',
                    'formatCode' => 'number'
                ],
                [
                    'selectColumn' => 'S',
                    'formatCode' => 'number'
                ],
                [
                    'selectColumn' => 'T',
                    'formatCode' => 'number'
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
            'service' => 'Sirs-ExportLaporanHasilSOBarang',
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
                    'label' => 'Tanggal Form SO',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Tanggal Validasi SO',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Tanggal Implementasi SO',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Divalidasi Oleh',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'No. Form SO',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Instalasi - Ruangan',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Kelompok Barang',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Sub Kelompok',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Kode Barang',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Nama Barang',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Satuan Kecil',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Harga Netto (Rp.)',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Stok Sistem',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Stok Fisik',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Selisih',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Stok Akhir',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Selisih Akhir',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Total Harga Netto (Rp.)',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Total Selisih (Rp.)',
                    'rowspan' => 2,
                ],
            ]
        ];
    }
}
