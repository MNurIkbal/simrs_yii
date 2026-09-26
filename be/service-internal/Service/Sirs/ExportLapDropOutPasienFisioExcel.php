<?php

namespace Integrasi\Service\Sirs;

use Yii;
use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoHelpers;
use Integrasi\Contracts\DocoImplement;

class ExportLapDropOutPasienFisioExcel extends DocoImplement
{
    public function execute()
    {
        $db = Yii::$app->db;
        ini_set('memory_limit', '-1');
        $totalPerPage = $this->totalPerPage;
        $cacheFiles = Yii::$app->cacheFiles;
        $filter = $this->filter;
        $row = $footer = $fvalue = [];
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Sedang mengekstrak data Riwayat Kunjungan Pasien Laboratorium',
                'progress' => 80
            ]),
        ]);

        for ($x = 0; $x < $totalPerPage; $x++) {
            $data = $cacheFiles->get($this->unique_str . '-' . $x);
            if (!empty($data)) {
                foreach ($data as $value) {
                    if (isset($filter['advanced-filter'])) {
                        foreach ($value as $fk => $fv) {
                            // if ($fk == 9 && !empty($filter['advanced-filter']['carabayar_id'])) {
                            //     $fvalue['carabayar_id'] = $fv;
                            // } else if ($fk == 10 && !empty($filter['advanced-filter']['penjamin_id'])) {
                            //     $fvalue['penjamin_id'] = $fv;
                            // } else if ($fk == 8 && !empty($filter['advanced-filter']['pegawai_id'])) {
                            //     $fvalue['pegawai_id'] = $fv;
                            // }
                            // else if( $fk == 11 && !empty($filter['advanced-filter']['is_status_bayar']) ) {
                            //     $fvalue['is_status_bayar'] = $fv;
                            // }
                        }
                    }
                    $row[] = $value;
                }
            }
            $cacheFiles->delete($this->unique_str . '-' . $x);
        }

        $start = date('Y-m-d');
        $end = date('Y-m-d');
        $nama_pasien = '-';
        $status_program = '-';

        if (isset($filter['advanced-filter'])) {
            if (isset($filter['advanced-filter']['tgl_rujukan'])) {
                $tglRujukan = $filter['advanced-filter']['tgl_rujukan'];
                $tglRujukanRange = DocoHelpers::parsingRangeDate($tglRujukan);
                $start = $tglRujukanRange['startDate'];
                $end = $tglRujukanRange['endDate'];
                unset($filter['advanced-filter']['tgl_rujukan']);
            }

            if (isset($filter['advanced-filter']['nama_pasien'])) {
                $nama_pasien = $filter['advanced-filter']['nama_pasien'];
                unset($filter['advanced-filter']['nama_pasien']);
            }

            if (isset($filter['advanced-filter']['status_program_fisio_id'])) {
                $status_program_fisio_id = strtolower($filter['advanced-filter']['status_program_fisio_id']);
                if ($status_program_fisio_id != 'semua') {
                    $status_program_fisio_id = ArrayHelper::getValue($filter, 'advanced-filter.status_program_fisio_id');
                    $data = $db->createCommand("
                        SELECT lookup_name
                        FROM lookup_m
                        WHERE lookup_id = {$status_program_fisio_id}
                    ")->queryOne();
                    $status_program = $data['lookup_name'];
                    unset($filter['advanced-filter']['status_program_fisio_id']);
                }
                unset($_GET['advanced-filter']['status_program_fisio_id']);
            }
        }

        $header = [
            'Tanggal Rujukan' => $start . ' Sampai Dengan ' . $end,
            'Nama Pasien / No Rekam Medik' => $nama_pasien,
            'Status Program' => $status_program,
        ];

        $custHeader = $this->custHeader();
        $custFormatCode = $this->custFormatCode();
        $path = 'uploads/' . $this->unique_str . '.xlsx';

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Sedang mengimport data ke dalam excel',
                'progress' => 85
            ]),
        ]);

        $filePath = DocoHelpers::exportExcel('Laporan Drop Out Pasien Fisioterapi', $row, $header, [
            "skipIncrement" => true,
            'skipHeader' => true,
            'customHeader' => $custHeader,
            'customFormatCode' => $custFormatCode
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
            'service' => 'Sirs-ExportLapDropOutPasienFisioExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s')
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
                    'label' => 'Tanggal Permintaan',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Data Pasien',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Program Terapi',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Frekuensi',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Realisasi',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Sisa Terapi',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Tidak Hadir',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Status',
                    'rowspan' => 2,
                ],
            ]
        ];
    }

    private function custFormatCode()
    {
        return [
            [
                'selectColumn' => 'A',
                'formatCode'   => 'general',
            ],
            [
                'selectColumn' => 'B',
                'formatCode'   => 'date',
            ],
            [
                'selectColumn' => 'C',
                'formatCode'   => 'general',
                'alignment'    => [
                    'wrapText' => true
                ]
            ],
            [
                'selectColumn' => 'D',
                'formatCode'   => 'general',
            ],
            [
                'selectColumn' => 'E',
                'formatCode'   => 'general'
            ],
            [
                'selectColumn' => 'F',
                'formatCode'   => 'general'
            ],
            [
                'selectColumn' => 'G',
                'formatCode'   => 'general'
            ],
            [
                'selectColumn' => 'H',
                'formatCode'   => 'general',
            ],
            [
                'selectColumn' => 'I',
                'formatCode'   => 'general',
            ]
        ];
    }
}
