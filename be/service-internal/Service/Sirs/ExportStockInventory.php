<?php
namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;

class ExportStockInventory extends \Integrasi\Contracts\DocoImplement
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
                'messageProcess' => 'Sedang mengekstrak data Stock Inventory',
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

        $ruangan_nama = $jenis = '';
        if (isset($filter['advanced-filter'])) {
            if(isset($filter['advanced-filter']['tanggal_inventory'])){
                $date = date('Y-m-d', strtotime($filter['advanced-filter']['tanggal_inventory']));
            }
            if(isset($filter['advanced-filter']['ruangan_nama'])){
                $ruangan_nama = $filter['advanced-filter']['ruangan_nama'];
            }

            if(isset($filter['advanced-filter']['jenis_obat'])){
                $jenis = $filter['advanced-filter']['jenis_obat'];
            }
        }

        $header = array(
            'Tanggal Inventory' => (@$date),
            'Nama Ruangan' => $ruangan_nama,
            'Jenis Obat Alkes' => $jenis

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
        
        $filePath = DocoHelpers::exportExcel('LAPORAN STOCK INVENTORY', $row, $header,[
            "skipIncrement" => true,
            'customHeader' => $custHeader,
            "customFormatCode" => [
                ['selectColumn' => 'J', 'formatCode' => 'number'],
                ['selectColumn' => 'K', 'formatCode' => 'number'],
                ['selectColumn' => 'L', 'formatCode' => 'number'],
                ['selectColumn' => 'M', 'formatCode' => 'number'],
                ['selectColumn' => 'N', 'formatCode' => 'number'],
                ['selectColumn' => 'O', 'formatCode' => 'number'],
                ['selectColumn' => 'P', 'formatCode' => 'number'],
                ['selectColumn' => 'Q', 'formatCode' => 'number'],
                ['selectColumn' => 'R', 'formatCode' => 'number'],
                ['selectColumn' => 'S', 'formatCode' => 'number'],
                ['selectColumn' => 'T', 'formatCode' => 'number']
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
            'service' => 'Sirs-ExportStockInventory',
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
                    'label'=>'Kode Obat',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Nama Obat Alkes',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Jenis Obat Alkes',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Generik',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Oral',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Satuan Kecil',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Satuan Besar',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Nilai Konversi',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Stok Satuan Kecil',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Stok Satuan Besar',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Weighted Average Satuan Kecil',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Weighted Average Satuan Besar',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Total (Rp.) Weighted Average Satuan Kecil',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Total (Rp.) Weighted Average Satuan Besar',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Base Price Satuan Kecil',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Base Price Satuan Besar',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Total (Rp.) Base Price Satuan Kecil',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Total (Rp.) Base Price Satuan Besar',
                    'rowspan'=>2,
                ],
            ]
        ];
    }
}
