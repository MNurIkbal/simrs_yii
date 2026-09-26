<?php
namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;

class ExportLapRekapHarianKinerjaExcel extends \Integrasi\Contracts\DocoImplement
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
                'messageProcess' => 'Sedang mengekstrak data Pasien',
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
            if(isset($filter['advanced-filter']['tgl_sensus'])) {
                $explode = explode(" - ", $filter['advanced-filter']['tgl_sensus']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($filter['advanced-filter']['tgl_sensus']);
            }
        }

        $header = [
            'Tanggal' => date('d-m-Y', strtotime($start))." - ".  date('d-m-Y', strtotime($end)),
        ];

        $custHeader = $this->custHeader();
        $customFormatCode = $this->customFormatCode();
        $path = 'uploads/'. $this->unique_str .'.xlsx';

        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengimport data ke dalam excel',
                'progress' => 85
            ]),
        ]);
        
        $filePath = DocoHelpers::exportExcel('Laporan Rekapitulasi harian Perbandingan Data Kinerja Profesional', $row, $header,[
            "skipIncrement" => true,
            'customHeader' => $custHeader,
            'customFormatCode' => $customFormatCode
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
            'service' => 'Sirs-ExportLapRekapHarianKinerjaExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function custHeader() 
    {
        $firstRow = [
            [
                'label' => 'No',
                'rowspan' => 3
            ],
            [
                'label' => 'RAWAT INAP (RUANGAN)',
                'rowspan' => 3
            ],
            [
                'label' => 'JUMLAH TEMPAT TIDUR',
                'colspan' => 2
            ],
            [
                'label' => 'JUMLAH PASIEN SEBELUM',
                'rowspan' => 3
            ],
            [
                'label' => 'PASIEN MASUK RAWAT',
                'colspan' => 3
            ],
            [
                'label' => 'PASIEN KELUAR',
                'colspan' => 6
            ],
            [
                'label' => 'HP (Pasien Sisa)',
                'rowspan' => 3
            ],
            [
                'label' => 'LOS Lama Rawat (Hari)',
                'rowspan' => 3
            ],
            [
                'label' => 'ALOS (Hari)',
                'rowspan' => 3
            ],
            [
                'label' => 'BOR (%)',
                'rowspan' => 3
            ],
            [
                'label' => 'TOI (Hari)',
                'rowspan' => 3
            ],
            [
                'label' => 'BTO (KALI)',
                'rowspan' => 3
            ],
            [
                'label' => 'NDR (%)',
                'rowspan' => 3
            ],
            [
                'label' => 'GDR (%)',
                'rowspan' => 3
            ],
        ];
        $secondRow = [
            [
                'label' => 'KAPASITAS',
                'startfrom' => 3,
                'rowspan' => 2
            ],
            [
                'label' => 'TERSEDIA',
                'rowspan' => 2
            ],
            [
                'label' => 'MASUK',
                'startfrom' => 2, // last loop index awal ditambah target pos
                'rowspan' => 2
            ],
            [
                'label' => 'PINDAHAN',
                'rowspan' => 2
            ],
            [
                'label' => 'JUMLAH',
                'rowspan' => 2
            ],
            [
                'label' => 'HIDUP',
                'rowspan' => 2
            ],
            [
                'label' => 'DIPINDAHKAN',
                'rowspan' => 2
            ],
            [
                'label' => 'RUJUK RS LAIN',
                'rowspan' => 2
            ],
            [
                'label' => 'MENINGGAL',
                'colspan' => 2
            ],
            [
                'label' => 'JUMLAH',
                'rowspan' => 2
            ],
        ];
        $thirdRow = [
            [
                'label' => '< 48 JAM',
                'startfrom' => 12,
            ],
            [
                'label' => '> 48 JAM',
            ],
        ];

        return [
            $firstRow,
            $secondRow,
            $thirdRow
        ];
    }

    private function customFormatCode() 
    {
        return [
            // [
            //     'startRow' => 'P8',
            //     'endRow' => 'P25'
            // ],
            // [
            //     'startRow' => 'Q8',
            //     'endRow' => 'Q25'
            // ],
            // [
            //     'startRow' => 'R8',
            //     'endRow' => 'R25'
            // ],
            // [
            //     'startRow' => 'S8',
            //     'endRow' => 'S25'
            // ],
            [
                'startRow' => 'T8',
                'endRow' => 'T25'
            ],
            [
                'startRow' => 'U8',
                'endRow' => 'U25',
                'formatCode' => 'number3'
            ],
            [
                'startRow' => 'V8',
                'endRow' => 'V25',
                'formatCode' => 'number3'
            ],
        ];
    }
}
