<?php
namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;

class ExportPemeriksaanLab extends \Integrasi\Contracts\DocoImplement
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
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengekstrak data Pemeriksaan Laboratorium',
                'progress' => 80
            ]),
        ]);

        for ($x = 0; $x < $totalPerPage; $x++) {
            $data = $cacheFiles->get($this->unique_str .'-'. $x);
            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    $row[] = $value;
                }
            }
            $cacheFiles->delete($this->unique_str .'-'. $x);
        }

        $start   = date('Y-m-d');
        $end     = date('Y-m-d');
        $db = Yii::$app->db;
        $kelompokpemeriksaanlab_nama = $jenispemeriksaanlab_nama = $pemeriksaanlab_nama = $kelaspelayanan = $nama_pegawai = '-';
        if(isset($filter['advanced-filter'])) {
            $advancedFilter = $filter['advanced-filter'];
            if(isset($advancedFilter['tglmasukpenunjang'])) {
                $explode = explode(" - ", $advancedFilter['tglmasukpenunjang']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }
                unset($advancedFilter['tglmasukpenunjang']);
            }
            if(isset($advancedFilter['kelompokpemeriksaanlab_id'])) {
                $kelompokpemeriksaanlab_id = $advancedFilter['kelompokpemeriksaanlab_id'];
                $data = $db->createCommand("
                    SELECT nama_kelompok
                    FROM kelompokpemeriksaanlab_m
                    WHERE kelompokpemeriksaanlab_id = {$kelompokpemeriksaanlab_id}
                ")->queryOne();
                $kelompokpemeriksaanlab_nama = $data['nama_kelompok'];
            }
            if(isset($advancedFilter['jenispemeriksaanlab_id'])) {
                $jenispemeriksaanlab_id = $advancedFilter['jenispemeriksaanlab_id'];
                $data = $db->createCommand("
                    SELECT jenispemeriksaanlab_nama
                    FROM jenispemeriksaanlab_m
                    WHERE jenispemeriksaanlab_id = {$jenispemeriksaanlab_id}
                ")->queryOne();
                $jenispemeriksaanlab_nama = $data['jenispemeriksaanlab_nama'];
            }
            if(isset($advancedFilter['daftartindakan_id'])) {
                $daftartindakan_id = $advancedFilter['daftartindakan_id'];
                $data = $db->createCommand("
                    SELECT daftartindakan_nama
                    FROM daftartindakan_m
                    WHERE daftartindakan_id = {$daftartindakan_id}
                ")->queryOne();
                $pemeriksaanlab_nama = $data['daftartindakan_nama'];
            }
            if(isset($advancedFilter['pegawai_id'])) {
                $pegawai_id = $advancedFilter['pegawai_id'];
                $data = $db->createCommand("
                    SELECT nama_pegawai
                    FROM pegawai_m
                    WHERE pegawai_id = {$pegawai_id}
                ")->queryOne();
                $nama_pegawai = $data['nama_pegawai'];
            }
            if(isset($advancedFilter['kelaspelayanan_id'])) {
                $kelaspelayanan_id = $advancedFilter['kelaspelayanan_id'];
                $data = $db->createCommand("
                    SELECT kelaspelayanan_nama
                    FROM kelaspelayanan_m
                    WHERE kelaspelayanan_id = {$kelaspelayanan_id}
                ")->queryOne();
                $kelaspelayanan = $data['kelaspelayanan_nama'];
            }
        }

        $header = [
            'Tanggal Masuk' => date('d M Y', strtotime($start)).' Sampai Dengan '.date('d M Y', strtotime($end)),
            'No Pendaftaran' => isset($filter['advanced-filter']['no_pendaftaran']) ? $filter['advanced-filter']['no_pendaftaran'] : '-',
            'No Rekam Medik' => isset($filter['advanced-filter']['no_rekam_medik']) ? $filter['advanced-filter']['no_rekam_medik'] : '-',
            'Nama Pasien' => isset($filter['advanced-filter']['nama_pasien']) ? $filter['advanced-filter']['nama_pasien'] : '-',
            'Nama Dokter' => $nama_pegawai,
            'Kelas Pelayanan' => $kelaspelayanan,
            'Kelompok Pemeriksaan' => $kelompokpemeriksaanlab_nama,
            'Jenis Pemeriksaan' => $jenispemeriksaanlab_nama,
            'Nama Pemeriksaan' => $pemeriksaanlab_nama,
        ];
        $custHeader = $this->custHeader($this->hide_price_column);
        $path = 'uploads/'. $this->unique_str .'.xlsx';
        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengimport data ke dalam excel',
                'progress' => 85
            ]),
        ]);
        $filePath = DocoHelpers::exportExcel('Laporan Pemeriksaan Laboratorium', $row, $header,[
            "skipIncrement" => true,
            'customHeader' => $custHeader,
        ], $footer, [], true);
        $filePath->save($path);
        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Proses import excel berhasil',
                'progress' => 90
            ]),
        ]);
        return json_encode([
            'service' => 'Sirs-ExportPemeriksaanLab',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function custHeader($isHidePrice = false)
    {
        $headers = [
            [
                'label'=>'No',
                'rowspan'=>2,
            ],
            [
                'label'=>'Tanggal Masuk',
                'rowspan'=>2,
            ],
            [
                'label'=>'No Pendaftaran',
                'rowspan'=>2,
            ],
            [
                'label'=>'No Rekam Medik',
                'rowspan'=>2,
            ],
            [
                'label'=>'Nama Pasien',
                'rowspan'=>2,
            ],
            [
                'label'=>'Nama Dokter',
                'rowspan'=>2,
            ],
            [
                'label'=>'Dokter DPJP',
                'rowspan'=>2,
            ],
            [
                'label'=>'Kelas Pelayanan',
                'rowspan'=>2,
            ],
            [
                'label'=>'Kelompok Pemeriksaan',
                'rowspan'=>2,
            ],
            [
                'label'=>'Jenis Pemeriksaan',
                'rowspan'=>2,
            ],
            [
                'label'=>'Nama Pemeriksaan',
                'rowspan'=>2,
            ],
        ];

        if ($isHidePrice) {
            $headers[] = [
                'label'=>'Qty',
                'rowspan'=>2,
            ];
        } else {
            array_push($headers,
                [
                    'label'=>'Harga Satuan',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Qty',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Cito',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Total',
                    'rowspan'=>2,
                ]
            );
        }
        return [$headers];
    }
}
