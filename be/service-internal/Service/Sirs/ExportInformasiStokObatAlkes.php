<?php
namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoRestActiveFilter;
use Doco\Repositories\KonfigRepositories;

class ExportInformasiStokObatAlkes extends \Integrasi\Contracts\DocoImplement
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
                'messageProcess' => 'Sedang mengekstrak data Informasi Stok Obat Alkes',
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
        
        $header = array(
            'Tanggal Unduh' => date('d-M-Y H:i:s'),
        );

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
        
        $filePath = DocoHelpers::exportExcel('Informasi Stok dan Ketersediaan Obat Alkes', $row, $header,[
            "skipIncrement" => true,
            'customHeader' => $custHeader,
            "customFormatCode" => [
                ['selectColumn' => 'F', 'formatCode' => 'number'],
                ['selectColumn' => 'G', 'formatCode' => 'number'],
                ['selectColumn' => 'H', 'formatCode' => 'number'],
                ['selectColumn' => 'I', 'formatCode' => 'number'],
                ['selectColumn' => 'J', 'formatCode' => 'number']
            ],
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
            'service' => 'Sirs-ExportInformasiStokObatAlkes',
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
                    'label'=>'Nama Ruangan',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Nama Obat Alkes',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Kode Obat',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Ven',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Stok Minimal',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Stok Maksimal',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Stok Dipesan',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Stok Tersedia',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Stok Total',
                    'rowspan'=>2,
                ],
            ]
        ];
    }
}