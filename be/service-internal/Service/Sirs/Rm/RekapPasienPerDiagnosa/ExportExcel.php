<?php

namespace Integrasi\Service\Sirs\Rm\RekapPasienPerDiagnosa;

use Yii;
use Integrasi\Components\DocoHelpers;

class ExportExcel extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $totalPerPage = $this->totalPerPage;
        $cacheFiles = Yii::$app->cacheFiles;
        $filter = $this->filter;

        $row = $footer = [];
        $no = 1;
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
                foreach ($data as $key => $value) {
                    $row[] = $value;
                }
            }
            $cacheFiles->delete($this->unique_str . '-' . $x);
        }

        $start   = date('Y-m-d');
        $end     = date('Y-m-d');

        if (isset($filter['advanced-filter'])) {
            if (isset($filter['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $filter['advanced-filter']['tgl_pendaftaran']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }
                unset($filter['advanced-filter']['tgl_pendaftaran']);
            }
        }

        $header = [
            'Tanggal Pendaftaran'     => $start . ' Sampai Dengan ' . $end,
        ];

        switch ($this->type) {
            case 'detail':
                $custHeader = [
                    [
                        [
                            'label' => 'No',
                            'rowspan' => 2,
                        ],
                        [
                            'label' => 'Nama Pasien',
                            'rowspan' => 2,
                        ],
                        [
                            'label' => 'No Rekam Medik',
                            'rowspan' => 2,
                        ],
                        [
                            'label' => 'No Registrasi',
                            'rowspan' => 2,
                        ],
                        [
                            'label' => 'Tanggal Registrasi',
                            'rowspan' => 2,
                        ],
                        [
                            'label' => 'Nama Dokter',
                            'rowspan' => 2,
                        ],
                        [
                            'label' => 'Instalasi',
                            'rowspan' => 2,
                        ],
                        [
                            'label' => 'Ruangan',
                            'rowspan' => 2,
                        ],
                        [
                            'label' => 'Diagnosa Utama',
                            'rowspan' => 2,
                        ],
                        [
                            'label' => 'Diagnosa Sekunder 1',
                            'rowspan' => 2,
                        ],
                        [
                            'label' => 'Diagnosa Sekunder 2',
                            'rowspan' => 2,
                        ],
                        [
                            'label' => 'Diagnosa Sekunder 3',
                            'rowspan' => 2,
                        ],
                    ]
                ];
                break;

            default:
                $custHeader = [
                    [
                        [
                            'label' => 'No',
                            'rowspan' => 2,
                        ],
                        [
                            'label' => 'Kode Diagnosa Utama',
                            'rowspan' => 2,
                        ],
                        [
                            'label' => 'Nama Diagnosa Utama',
                            'rowspan' => 2,
                        ],
                        [
                            'label' => 'Jumlah Pasien',
                            'rowspan' => 2,
                        ],
                    ]
                ];
                break;
        }

        $path = 'uploads/' . $this->unique_str . '.xlsx';

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Sedang mengimport data ke dalam excel',
                'progress' => 85
            ]),
        ]);
        $filePath = DocoHelpers::exportExcel('Laporan Rekap Pasien per Diagnosa', $row, $header, [
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
            'service' => 'Sirs-RekapPasienPerDiagnosa-Excel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }
}
