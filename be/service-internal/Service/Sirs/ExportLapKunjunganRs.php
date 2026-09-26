<?php

namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use GuzzleHttp\Client;

class ExportLapKunjunganRs extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        ini_set('memory_limit', '-1');
        $client = $this->setUrlRm();
        $totalPerPage = $this->totalPerPage; 
        $cacheFiles = Yii::$app->cacheFiles;
        $filter = $this->filter;

        $row = $footer = [];
        $no = 1;
        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengekstrak data kunjungan',
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

        if(isset($filter['advanced-filter'])) {
            if(isset($filter['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $filter['advanced-filter']['tgl_pendaftaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }
                unset($filter['advanced-filter']['tgl_pendaftaran']);
            }
        }

        $header = [
            'Tanggal Pendaftaran'     => $start.' Sampai Dengan '.$end,
            // 'No Rekam Medik'          => $no_rekam_medik,
            // 'Nama Pasien'             => $nama_pasien,
            // 'Jenis Kelamin'           => $jenis_kelamin,
            // 'Cara Bayar'              => $carabayar_nama,
            // 'Penjamin'                => $penjamin_nama,
            // 'Jenis Kasus Penyakit'    => $jeniskasuspenyakit_nama,
            // 'Instalasi'               => $instalasi_nama,
            // 'Ruangan'                 => $ruangan_nama,
            // 'Dokter Penanggung Jawab' => $nama_pegawai,
        ];

        $custHeader = [
            [
                [
                    'label'=>'No',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Tanggal Pendaftaran',
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
                    'label'=>'Jenis Kelamin',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Nama Pasien',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Tanggal Lahir',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Umur',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Alamat',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'No Identitas',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Cara bayar',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Penjamin',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Kelas Pelayanan',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Jenis Kasus Penyakit',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Instalasi',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Ruangan',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Dokter DPJP',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Diagnosa Utama',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Diagnosa Penyerta',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Status Periksa',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'No Telepon Pasien',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Kondisi Pulang',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Cara Pulang',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Status Kunjungan',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Asal Rujukan',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Rujukan Dari',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Nama Perujuk',
                    'rowspan'=>2,
                ],
            ]
        ];

        $path = 'uploads/'. $this->unique_str .'.xlsx';

        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengimport data ke dalam excel',
                'progress' => 85
            ]),
        ]);
        $filePath = DocoHelpers::exportExcel('Laporan Kunjungan Pasien Rumah Sakit', $row, $header,[
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
            'service' => 'Sirs-ExportLapKunjunganRs',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

   private function setUrlRm()
    {
        $header = [
            'Authorization' => $this->token,
            'user-agent' => 'cli',
            'X-Owner' => $this->owner,
        ];

        $client =  new Client([
            'base_uri' => "http://localhost:8858/rm/v1/",
            'headers' => $header
        ]);

        return $client;
    }


}