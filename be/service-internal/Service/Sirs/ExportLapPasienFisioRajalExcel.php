<?php

namespace Integrasi\Service\Sirs;

use Yii;
use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoHelpers;
use Integrasi\Contracts\DocoImplement;

class ExportLapPasienFisioRajalExcel extends DocoImplement
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
                'messageProcess' => 'Sedang mengekstrak data laporan pasien fisioterapi rawat jalan',
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

        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d');
        $dataPasien = '-';
        $dokterPerujuk = '-';
        $ruanganAsal = '-';
        $program = '-';
        $statusProgram = '-';
        $terapis = '-';

        if (isset($filter['advanced-filter'])) {
            // Tanggal Pendaftaran
            if (isset($filter['advanced-filter']['tgl_pendaftaran'])) {
                $tglPendaftaran = ArrayHelper::getValue($filter, 'advanced-filter.tgl_pendaftaran');
                $tglPendaftaranRange = DocoHelpers::parsingRangeDate($tglPendaftaran);
                $startDate = $tglPendaftaranRange['startDate'];
                $endDate = $tglPendaftaranRange['endDate'];
                unset($filter['advanced-filter']['tgl_pendaftaran']);
            }
            // Data Pasien
            if (isset($filter['advanced-filter']['nama_pasien'])) {
                $dataPasien = $filter['advanced-filter']['nama_pasien'];
                unset($filter['advanced-filter']['nama_pasien']);
            }
            // Dokter Perujuk
            if (isset($filter['advanced-filter']['dokterperujuk_id'])) {
                $dokterperujuk_id = ArrayHelper::getValue($filter, 'advanced-filter.dokterperujuk_id');
                $dokterperujuk_id = strtolower($dokterperujuk_id);
                if ($dokterperujuk_id != 'semua') {
                    $data = $db->createCommand("
                        SELECT nama_pegawai
                        FROM pegawai_m
                        WHERE pegawai_id = {$dokterperujuk_id}
                    ")->queryOne();
                    $dokterPerujuk = $data['nama_pegawai'];
                }
                unset($filter['advanced-filter']['dokterperujuk_id']);
            }
            // Ruangan Asal
            if (isset($filter['advanced-filter']['ruangan_id'])) {
                $ruangan_id = ArrayHelper::getValue($filter, 'advanced-filter.ruangan_id');
                $ruangan_id = strtolower($ruangan_id);
                if ($ruangan_id != 'semua') {
                    $data = $db->createCommand("
                        SELECT ruangan_nama
                        FROM ruangan_m
                        WHERE ruangan_id = {$ruangan_id}
                    ")->queryOne();
                    $ruanganAsal = $data['ruangan_nama'];
                }
                unset($filter['advanced-filter']['ruangan_id']);
            }
            // Program
            if (isset($filter['advanced-filter']['jenispemeriksaanfisio_id'])) {
                $jenispemeriksaanfisio_id = ArrayHelper::getValue($filter, 'advanced-filter.jenispemeriksaanfisio_id');
                $jenispemeriksaanfisio_id = strtolower($jenispemeriksaanfisio_id);
                if ($jenispemeriksaanfisio_id != 'semua') {
                    $data = $db->createCommand("
                        SELECT jenispemeriksaanfisio_nama
                        FROM jenispemeriksaanfisio_m
                        WHERE jenispemeriksaanfisio_id = {$jenispemeriksaanfisio_id}
                    ")->queryOne();
                    $program = $data['jenispemeriksaanfisio_nama'];
                }
                unset($filter['advanced-filter']['jenispemeriksaanfisio_id']);
            }
            // Status Program
            if (isset($filter['advanced-filter']['status_program_fisio_id'])) {
                $status_program_fisio_id = ArrayHelper::getValue($filter, 'advanced-filter.status_program_fisio_id');
                $status_program_fisio_id = strtolower($status_program_fisio_id);
                if ($status_program_fisio_id != 'semua') {
                    $data = $db->createCommand("
                        SELECT lookup_name
                        FROM lookup_m
                        WHERE lookup_id = {$status_program_fisio_id}
                    ")->queryOne();
                    $statusProgram = $data['lookup_name'];
                }
                unset($filter['advanced-filter']['status_program_fisio_id']);
            }
            if (isset($filter['advanced-filter']['terapis_id'])) {
                $terapis_id = strtolower($filter['advanced-filter']['terapis_id']);
                if ($terapis_id != 'semua') {
                    $terapis_id = ArrayHelper::getValue($filter, 'advanced-filter.terapis_id');
                    $data = $db->createCommand("
                        SELECT nama_pegawai
                        FROM pegawai_m
                        WHERE pegawai_id = {$terapis_id}
                    ")->queryOne();
                    $terapis = $data['nama_pegawai'];
                    unset($filter['advanced-filter']['terapis_id']);
                }
                unset($_GET['advanced-filter']['terapis_id']);
            }
        }
        $header = [
            'Tanggal Permintaan' => $startDate . ' Sampai Dengan ' . $endDate,
            'Data Pasien' => $dataPasien,
            'Dokter Perujuk' => $dokterPerujuk,
            'Ruangan Asal' => $ruanganAsal,
            'Program' => $program,
            'Status' => $statusProgram,
            'Terapis' => $terapis
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
        $filePath = DocoHelpers::exportExcel('Laporan Pasien Fisioterapi Rawat Jalan', $row, $header, [
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
            'service' => 'Sirs-ExportLapPasienFisioRajalExcel',
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
                    'rowspan' => 2
                ],
                [
                    'label' => 'Tanggal Permintaan',
                    'rowspan' => 2
                ],
                [
                    'label' => 'Data Pasien',
                    'rowspan' => 2
                ],
                [
                    'label' => 'Dokter Perujuk',
                    'rowspan' => 2
                ],
                [
                    'label' => 'Ruangan Asal',
                    'rowspan' => 2
                ],
                [
                    'label' => 'Program',
                    'rowspan' => 2
                ],
                [
                    'label' => 'Frekuensi',
                    'rowspan' => 2
                ],
                [
                    'label' => 'Realisasi',
                    'rowspan' => 2
                ],
                [
                    'label' => 'Sisa Terapi',
                    'rowspan' => 2
                ],
                [
                    'label' => 'Tidak Hadir',
                    'rowspan' => 2
                ],
                [
                    'label' => 'Status',
                    'rowspan' => 2
                ],
                [
                    'label' => 'Penjadwalan',
                    'rowspan' => 2
                ],
                [
                    'label' => 'Realisasi',
                    'rowspan' => 2
                ],
                [
                    'label' => 'Terapis',
                    'rowspan' => 2
                ]
            ]
        ];
    }

    private function custFormatCode()
    {
        return [
            [
                'selectColumn' => 'A',
                'formatCode' => 'general'
            ],
            [
                'selectColumn' => 'B',
                'formatCode' => 'date'
            ],
            [
                'selectColumn' => 'C',
                'formatCode' => 'general',
                'alignment' => [
                    'wrapText' => true
                ]
            ],
            [
                'selectColumn' => 'D',
                'formatCode' => 'general'
            ],
            [
                'selectColumn' => 'E',
                'formatCode' => 'general'
            ],
            [
                'selectColumn' => 'F',
                'formatCode' => 'general'
            ],
            [
                'selectColumn' => 'G',
                'formatCode' => 'general'
            ],
            [
                'selectColumn' => 'H',
                'formatCode' => 'general'
            ],
            [
                'selectColumn' => 'I',
                'formatCode' => 'general'
            ],
            [
                'selectColumn' => 'J',
                'formatCode' => 'general'
            ],
            [
                'selectColumn' => 'K',
                'formatCode' => 'general'
            ],
            [
                'selectColumn' => 'L',
                'formatCode' => 'general'
            ],
            [
                'selectColumn' => 'M',
                'formatCode' => 'date'
            ],
            [
                'selectColumn' => 'N',
                'formatCode' => 'general'
            ]
        ];
    }
}
