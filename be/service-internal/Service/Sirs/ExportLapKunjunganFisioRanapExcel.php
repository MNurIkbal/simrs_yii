<?php

namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Query;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoHelpers;

class ExportLapKunjunganFisioRanapExcel extends \Integrasi\Contracts\DocoImplement
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
        $start   = date('Y-m-d');
        $end     = date('Y-m-d');
        $startBirthDate = null;
        $endBirthDate = null;
        $nama_pasien = '-';
        $carabayar_nama = '-';
        $penjamin_nama = '-';
        $instalasi_nama = '-';
        $ruangan_nama = '-';
        $dokterdpjp_nama = '-';
        $status_periksa_nama = '-';
        if (isset($filter['advanced-filter'])) {
            if (isset($filter['advanced-filter']['tgl_permintaan'])) {
                $explode = explode(" - ", $filter['advanced-filter']['tgl_permintaan']);
                if (count($explode) == 2) {
                    $start = date('d M Y', strtotime($explode[0]));
                    $end = date('d M Y', strtotime($explode[1]));
                }
                unset($filter['advanced-filter']['tgl_permintaan']);
            }
            if (isset($filter['advanced-filter']['tanggal_lahir'])) {
                $explode = explode(" - ", $filter['advanced-filter']['tanggal_lahir']);
                if (count($explode) == 2) {
                    $startBirthDate = date('d M Y', strtotime($explode[0]));
                    $endBirthDate = date('d M Y', strtotime($explode[1]));
                }
                unset($filter['advanced-filter']['tanggal_lahir']);
            }
            if (isset($filter['advanced-filter']['nama_pasien'])) {
                $nama_pasien = $filter['advanced-filter']['nama_pasien'];
                unset($filter['advanced-filter']['nama_pasien']);
            }
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
            if (isset($filter['advanced-filter']['dokterdpjp_id'])) {
                $dokterdpjp_id = ArrayHelper::getValue($filter, 'advanced-filter.dokterdpjp_id');
                $dokterdpjp_id = strtolower($dokterdpjp_id);
                if ($dokterdpjp_id != 'semua') {
                    $data = $db->createCommand("
                        SELECT nama_pegawai
                        FROM pegawai_m
                        WHERE pegawai_id = {$dokterdpjp_id}
                    ")->queryOne();
                    $dokterdpjp_nama = $data['nama_pegawai'];
                }
                unset($filter['advanced-filter']['dokterdpjp_id']);
            }
            if (isset($filter['advanced-filter']['status_periksa_id'])) {
                $status_periksa_id = strtolower($filter['advanced-filter']['status_periksa_id']);
                if ($status_periksa_id != 'semua') {
                    $status_periksa_id = ArrayHelper::getValue($filter, 'advanced-filter.status_periksa_id');
                    $data = $db->createCommand("
                        SELECT lookup_name
                        FROM lookup_m
                        WHERE lookup_id = {$status_periksa_id}
                    ")->queryOne();
                    $status_periksa_nama = $data['lookup_name'];
                    unset($filter['advanced-filter']['status_periksa_id']);
                }
                unset($_GET['advanced-filter']['status_periksa_id']);
            }
        }
        $birthDate = '-';
        if ($startBirthDate & $endBirthDate) $birthDate = "$startBirthDate Sampai Dengan $endBirthDate";
        $header = [
            'Tanggal Pemeriksaan' => $start . ' Sampai Dengan ' . $end,
            'Tanggal Lahir' => $birthDate,
            'Data Pasien' => $nama_pasien,
            'Cara bayar' => $carabayar_nama,
            'Penjamin' => $penjamin_nama,
            'Instalasi' => $instalasi_nama,
            'Ruangan' => $ruangan_nama,
            'Dokter' => $dokterdpjp_nama,
            'Status Periksa' => $status_periksa_nama,
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
        $filePath = DocoHelpers::exportExcel('Laporan Kunjungan Fisio Rawat Inap', $row, $header, [
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
            'service' => 'Sirs-ExportLapKunjunganFisioRanapExcel',
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
                    'label' => 'Tanggal Lahir',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Alamat',
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
                    'label' => 'Dokter',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Status Periksa',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'No. Telepon Pasien',
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
                'formatCode'   => 'general',
                'alignment'    => [
                    'wrapText' => true
                ]
            ],
            [
                'selectColumn' => 'F',
                'formatCode'   => 'general',
                'alignment'    => [
                    'wrapText' => true
                ]
            ],
            [
                'selectColumn' => 'G',
                'formatCode'   => 'general',
                'alignment'    => [
                    'wrapText' => true
                ]
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
        ];
    }
}
