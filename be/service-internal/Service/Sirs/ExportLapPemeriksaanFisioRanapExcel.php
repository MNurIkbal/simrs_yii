<?php

namespace Integrasi\Service\Sirs;

use Yii;
use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoHelpers;
use Integrasi\Contracts\DocoImplement;

class ExportLapPemeriksaanFisioRanapExcel extends DocoImplement
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
                'messageProcess' => 'Sedang mengekstrak data laporan pemeriksaan pasien fisioterapi rawat inap',
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
        $carabayar_nama = '-';
        $penjamin_nama = '-';
        $instalasi_nama = '-';
        $ruangan_nama = '-';
        $dokterdpjp_nama = '-';
        $jenis_pemeriksaan_nama = '-';
        $daftar_tindakan_nama = '-';
        if (isset($filter['advanced-filter'])) {
            // tgl_pendaftaran
            if (isset($filter['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $filter['advanced-filter']['tgl_pendaftaran']);
                if (count($explode) == 2) {
                    $start = date('d M Y', strtotime($explode[0]));
                    $end = date('d M Y', strtotime($explode[1]));
                }
                unset($filter['advanced-filter']['tgl_pendaftaran']);
            }
            // nama_pasien
            if (isset($filter['advanced-filter']['nama_pasien'])) {
                $nama_pasien = $filter['advanced-filter']['nama_pasien'];
                unset($filter['advanced-filter']['nama_pasien']);
            }
            // carabayar_id
            if (isset($filter['advanced-filter']['carabayar_id'])) {
                $carabayar_id = ArrayHelper::getValue($filter, 'advanced-filter.carabayar_id');
                $carabayar_id = strtolower($carabayar_id);
                if ($carabayar_id != 'semua') {
                    $data = $db->createCommand("
                        SELECT carabayar_nama
                        FROM carabayar_m
                        WHERE carabayar_id = {$carabayar_id}
                    ")->queryOne();
                    $carabayar_nama = $data['carabayar_nama'];
                }
                unset($filter['advanced-filter']['carabayar_id']);
            }
            // penjamin_id
            if (isset($filter['advanced-filter']['penjamin_id'])) {
                $penjamin_id = ArrayHelper::getValue($filter, 'advanced-filter.penjamin_id');
                $penjamin_id = strtolower($penjamin_id);
                if ($penjamin_id != 'semua') {
                    $data = $db->createCommand("
                        SELECT penjamin_nama
                        FROM penjamin_m
                        WHERE penjamin_id = {$penjamin_id}
                    ")->queryOne();
                    $penjamin_nama = $data['penjamin_nama'];
                }
                unset($filter['advanced-filter']['penjamin_id']);
            }
            // instalasi_id
            if (isset($filter['advanced-filter']['instalasi_id'])) {
                $instalasi_id = ArrayHelper::getValue($filter, 'advanced-filter.instalasi_id');
                $instalasi_id = strtolower($instalasi_id);
                if ($instalasi_id != 'semua') {
                    $data = $db->createCommand("
                        SELECT instalasi_nama
                        FROM instalasi_m
                        WHERE instalasi_id = {$instalasi_id}
                    ")->queryOne();
                    $instalasi_nama = $data['instalasi_nama'];
                }
                unset($filter['advanced-filter']['instalasi_id']);
            }
            // ruangan_id
            if (isset($filter['advanced-filter']['ruangan_id'])) {
                $ruangan_id = ArrayHelper::getValue($filter, 'advanced-filter.ruangan_id');
                $ruangan_id = strtolower($ruangan_id);
                if ($ruangan_id != 'semua') {
                    $data = $db->createCommand("
                        SELECT ruangan_nama
                        FROM ruangan_m
                        WHERE ruangan_id = {$ruangan_id}
                    ")->queryOne();
                    $ruangan_nama = $data['ruangan_nama'];
                }
                unset($filter['advanced-filter']['ruangan_id']);
            }
            // terapis_id
            if (isset($filter['advanced-filter']['terapis_id'])) {
                $terapis_id = ArrayHelper::getValue($filter, 'advanced-filter.terapis_id');
                $terapis_id = strtolower($terapis_id);
                if ($terapis_id != 'semua') {
                    $data = $db->createCommand("
                        SELECT nama_pegawai
                        FROM pegawai_m
                        WHERE pegawai_id = {$terapis_id}
                    ")->queryOne();
                    $dokterdpjp_nama = $data['nama_pegawai'];
                }
                unset($filter['advanced-filter']['terapis_id']);
            }
            // jenispemeriksaanfisio_id
            if (isset($filter['advanced-filter']['jenispemeriksaanfisio_id'])) {
                $jenispemeriksaanfisio_id = strtolower($filter['advanced-filter']['jenispemeriksaanfisio_id']);
                if ($jenispemeriksaanfisio_id != 'semua') {
                    $jenispemeriksaanfisio_id = ArrayHelper::getValue($filter, 'advanced-filter.jenispemeriksaanfisio_id');
                    $data = $db->createCommand("
                        SELECT jenispemeriksaanfisio_nama
                        FROM jenispemeriksaanfisio_m
                        WHERE jenispemeriksaanfisio_id = {$jenispemeriksaanfisio_id}
                    ")->queryOne();
                    $jenis_pemeriksaan_nama = $data['jenispemeriksaanfisio_nama'];
                    unset($filter['advanced-filter']['jenispemeriksaanfisio_id']);
                }
                unset($filter['advanced-filter']['jenispemeriksaanfisio_id']);
            }
            // daftartindakan_id
            if (isset($filter['advanced-filter']['daftartindakan_id'])) {
                $daftartindakan_id = strtolower($filter['advanced-filter']['daftartindakan_id']);
                if ($daftartindakan_id != 'semua') {
                    $daftartindakan_id = ArrayHelper::getValue($filter, 'advanced-filter.daftartindakan_id');
                    $data = $db->createCommand("
                        SELECT daftartindakan_nama
                        FROM daftartindakan_m
                        WHERE daftartindakan_id = {$daftartindakan_id}
                    ")->queryOne();
                    $daftar_tindakan_nama = $data['daftartindakan_nama'];
                    unset($filter['advanced-filter']['daftartindakan_id']);
                }
                unset($filter['advanced-filter']['daftartindakan_id']);
            }
        }
        $header = [
            'Tanggal Permintaan' => $start . ' Sampai Dengan ' . $end,
            'Data Pasien' => $nama_pasien,
            'Cara Bayar' => $carabayar_nama,
            'Penjamin' => $penjamin_nama,
            'Dokter Terapis' => $dokterdpjp_nama,
            'Instalasi' => $instalasi_nama,
            'Ruangan' => $ruangan_nama,
            'Jenis Pemeriksaan' => $jenis_pemeriksaan_nama,
            'Nama Pemeriksaan' => $daftar_tindakan_nama
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
        $filePath = DocoHelpers::exportExcel('Laporan Pemeriksaan Fisioterapi Rawat Inap', $row, $header, [
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
            'service' => 'Sirs-ExportLapPemeriksaanFisioRanapExcel',
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
                    'label' => 'Tanggal Pendaftaran',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Data Pasien',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Cara Bayar / Penjamin',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Instalasi / Ruangan',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Dokter Terapis',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Dokter DPJP',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Jenis Pemeriksaan',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Nama Pemeriksaan',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Jumlah',
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
                'formatCode'   => 'general'
            ],
            [
                'selectColumn' => 'J',
                'formatCode'   => 'general'
            ]
        ];
    }
}
