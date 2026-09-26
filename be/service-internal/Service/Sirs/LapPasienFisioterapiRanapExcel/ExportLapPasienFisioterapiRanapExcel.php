<?php

namespace Integrasi\Service\Sirs\LapPasienFisioterapiRanapExcel;

use Yii;
use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoHelpers;
use Integrasi\Contracts\DocoImplement;

class ExportLapPasienFisioterapiRanapExcel extends DocoImplement
{
    private function getHeaderNameByRequest()
    {
        $db = Yii::$app->db;
        $filter = $this->filter;
        $advancedFilter = ArrayHelper::getValue($filter, 'advanced-filter');
        $startTglPendaftaran = date('Y-m-d');
        $endTglPendaftaran = date('Y-m-d');
        $tglPendaftaran = ArrayHelper::getValue($advancedFilter, 'tgl_pendaftaran');
        $dataPasien = ArrayHelper::getValue($advancedFilter, 'nama_pasien');
        $dokterPerujukId = ArrayHelper::getValue($advancedFilter, 'dokterperujuk_id');
        $dokterDpjpId = ArrayHelper::getValue($advancedFilter, 'dokterdpjp_id');
        $statusProgramFisioId = ArrayHelper::getValue($advancedFilter, 'status_program_fisio_id');
        // Header Default
        $headerDataPasien = '-';
        $dokterPerujukNama = '-';
        $dokterDpjpNama = '-';
        $statusProgramFisioNama = '-';
        if ($advancedFilter) {
            if ($tglPendaftaran) {
                $tglPendaftaranRange = DocoHelpers::parsingRangeDate($tglPendaftaran);
                $startTglPendaftaran = date('j-M-Y', strtotime($tglPendaftaranRange['startDate']));
                $endTglPendaftaran = date('j-M-Y', strtotime($tglPendaftaranRange['endDate']));
            }
            if ($dataPasien) {
                $headerDataPasien = $dataPasien;
            }
            if ($dokterPerujukId) {
                $data = $db->createCommand("SELECT nama_pegawai FROM pegawai_m WHERE pegawai_id = {$dokterPerujukId}")->queryOne();
                $dokterPerujukNama = $data['nama_pegawai'];
            }
            if ($dokterDpjpId) {
                $data = $db->createCommand("SELECT nama_pegawai FROM pegawai_m WHERE pegawai_id = {$dokterDpjpId}")->queryOne();
                $dokterDpjpNama = $data['nama_pegawai'];
            }
            if ($statusProgramFisioId) {
                $data = $db->createCommand("SELECT lookup_name FROM lookup_m WHERE lookup_id = {$statusProgramFisioId}")->queryOne();
                $statusProgramFisioNama = $data['lookup_name'];
            }
        }
        $header = [
            'Tanggal Permintaan' => $startTglPendaftaran . ' Sampai Dengan ' . $endTglPendaftaran,
            'Data Pasien' => $headerDataPasien,
            'Dokter Perujuk' => $dokterPerujukNama,
            'Dokter DPJP' => $dokterDpjpNama,
            'Status Program' => $statusProgramFisioNama,
        ];
        return $header;
    }

    public function execute()
    {
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

        $filePath = DocoHelpers::exportExcel('Laporan Pasien Fisioterapi Ranap', $row, $header, [
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
            'service' => 'Sirs-ExportLapPasienFisioterapiRanapExcel',
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
                    'label' => 'Dokter Perujuk',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Dokter DPJP.',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Ruangan Asal',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Program',
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
                [
                    'label' => 'Tanggal Penjadwalan',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Tanggal Realisasi',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Terapis',
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
            ],
            [
                'selectColumn' => 'J',
                'formatCode'   => 'general',
            ],
            [
                'selectColumn' => 'K',
                'formatCode'   => 'general',
            ],
            [
                'selectColumn' => 'L',
                'formatCode'   => 'general',
            ],
            [
                'selectColumn' => 'M',
                'formatCode'   => 'general',
            ],
            [
                'selectColumn' => 'N',
                'formatCode'   => 'general',
            ],
            [
                'selectColumn' => 'O',
                'formatCode'   => 'general',
            ],
        ];
    }
}
