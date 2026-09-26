<?php
namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;

class ExportPasienRadiologi extends \Integrasi\Contracts\DocoImplement
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
                'messageProcess' => 'Sedang mengekstrak data Pasien Radiologi',
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
            if(isset($filter['advanced-filter']['tglmasukpenunjang'])) {
                $explode = explode(" - ", $filter['advanced-filter']['tglmasukpenunjang']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }
                unset($filter['advanced-filter']['tglmasukpenunjang']);
            }
        }

        $header = [
            'Tanggal Pendaftaran' => $start.' Sampai Dengan '.$end,
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
        
        $filePath = DocoHelpers::exportExcel('Laporan Pasien Radiologi', $row, $header,[
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
            'service' => 'Sirs-ExportPasienRadiologi',
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
                            'label'=>'Tanggal Pendaftaran',
                            'rowspan'=>2,
                        ],
                        [
                            'label' => 'Tanggal Periksa',
                            'rowspan' => 2,
                        ],
                        [
                            'label' => 'Status Cito',
                            'rowspan' => 2,
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
                            'label'=>'Tanggal Lahir',
                            'rowspan'=>2,
                        ],
                        [
                            'label' => 'Dokter Perujuk',
                            'rowspan' => 2,
                        ],
                        [
                            'label'=>'Dokter',
                            'rowspan'=>2,
                        ],
                        [
                            'label'=>'Cara Bayar',
                            'rowspan'=>2,
                        ],
                        [
                            'label' => 'Penjamin',
                            'rowspan' => 2,
                        ],
                        [
                            'label'=>'No Radiologi',
                            'rowspan'=>2,
                        ],
                        [
                            'label'=>'Asal Rujukan',
                            'rowspan'=>2,
                        ],
                        [
                            'label'=>'Ruangan Asal',
                            'rowspan'=>2,
                        ],
                    ]
                ];
    }
}
