<?php
namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;

class ExportLaporanPtmExcel extends \Integrasi\Contracts\DocoImplement
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
                'messageProcess' => 'Sedang mengekstrak data Laporan PTM',
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
            if(isset($filter['advanced-filter']['tgl_registrasi'])) {
                $explode = explode(" - ", $filter['advanced-filter']['tgl_registrasi']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }
                unset($filter['advanced-filter']['tgl_registrasi']);
            }
        }

        $header = [
            'Periode' => date('d F Y', strtotime($start)) . ' - '.date('d F Y', strtotime($end)),
            // 'Pilihan Registrasi' => $instalasi_nama,
            // 'Searching' => $diagnosa_nama
        ];

        $custHeader = $this->custHeader();
        $path = 'uploads/'. $this->unique_str .'.xlsx';

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
            'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengimport data ke dalam excel',
                'progress' => 85
            ]),
        ]);
        
        $filePath = DocoHelpers::exportExcel('Laporan PTM', $row, $header,[
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
            'service' => 'Sirs-ExportlLaporanPtmExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function custHeader() 
    {
        return [
            [
                [
                    'label'=>'No',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'No KTP',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'No BPJS',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Nama Pasien',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'No Rekam Medik',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Tanggal Lahir',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'No HP',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Email',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Alamat',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Tanggal Registrasi',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'No Registrasi',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Diagnosa',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'SOAP (O)',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Umur',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Jumlah Kunjungan',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Nama Dokter',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Golongan Darah',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Pemeriksaan EKG',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Nama Keluarga',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Tanggal Pulang',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Keadaan Sekarang',
                    'rowspan'=>2,
                ],
            ]
        ];
    }
}
