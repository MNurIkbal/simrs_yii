<?php
namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;

class ExportCaraPulangPasien extends \Integrasi\Contracts\DocoImplement
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
            if(isset($filter['advanced-filter']['tgl_pulang'])) {
                $explode = explode(" - ", $filter['advanced-filter']['tgl_pulang']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($filter['advanced-filter']['tgl_pulang']);
            }
        }

        $header = [
            'Tanggal Pulang' => $start.' Sampai Dengan '.$end,
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
        
        $filePath = DocoHelpers::exportExcel('Laporan Cara Pulang Pasien', $row, $header,[
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
            'service' => 'Sirs-ExportCaraPulangPasien',
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
                    'label'=>'No Registrasi',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Tanggal Pendaftaran',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Tanggal Pulang',
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
                    'label'=>'Instalasi',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Ruangan',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Cara Pulang',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Rumah Sakit Dirujuk',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Kondisi Pulang',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Cara Bayar',
                    'rowspan'=>2,
                ],
            ]            
        ];
    }
}
