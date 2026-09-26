<?php
namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;

class ExportStockMutasi extends \Integrasi\Contracts\DocoImplement
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
                'messageProcess' => 'Sedang mengekstrak data Stock Mutasi',
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

        $start = date('d M Y');
        $end = date('d M Y');
        $ruangan = $jenis_adjustment = $jenis_obatalkes = "";
        if (isset($filter['advanced-filter'])) {
            if (isset($filter['advanced-filter']['tanggal_inventory'])) {
                $explode = explode(" - ", $filter['advanced-filter']['tanggal_inventory']);
                if (count($explode) == 2) {
                    $start = date('d M Y', strtotime($explode[0]));
                    $end = date('d M Y', strtotime($explode[1]));
                }
                unset($filter['advanced-filter']['tanggal_inventory']);
            }

            if (isset($filter['advanced-filter']['ruangan_nama'])) {
                $ruangan = $filter['advanced-filter']['ruangan_nama'];
                unset($filter['advanced-filter']['ruangan_nama']);
            }

            if (isset($filter['advanced-filter']['obatalkes_nama'])) {
                $jenis_obatalkes = $filter['advanced-filter']['obatalkes_nama'];
                unset($filter['advanced-filter']['obatalkes_nama']);
            }
        }

        $header = [
            'Tanggal' => $start . ' s/d ' . $end,
            'Ruangan' => $ruangan,
            'Jenis Obat Alkes' => $jenis_obatalkes
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
        
        $filePath = DocoHelpers::exportExcel('LAPORAN SUMMARY MUTASI STOCK', $row, $header,[
            "skipIncrement" => true,
            'customHeader' => $custHeader,
            "customFormatCode" => [
                ['selectColumn' => 'H', 'formatCode' => 'number'],
                ['selectColumn' => 'J', 'formatCode' => 'number'],
                ['selectColumn' => 'K', 'formatCode' => 'number'],
                ['selectColumn' => 'L', 'formatCode' => 'number'],
                ['selectColumn' => 'M', 'formatCode' => 'number'],
                ['selectColumn' => 'N', 'formatCode' => 'number'],
                ['selectColumn' => 'O', 'formatCode' => 'number'],
                ['selectColumn' => 'P', 'formatCode' => 'number']
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
            'service' => 'Sirs-ExportStockMutasi',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
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
                    'label' => 'Kode Obat',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Nama Obat Alkes',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Jenis Obat Alkes',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Manufaktur',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Ruangan',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'UoM',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'HNA',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Total Qty Awal',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Total Value Awal',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Total Qty Received',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Total Value Received',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Total Qty Usage',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Total Value Usage',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Total Qty Akhir',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Total Value Akhir',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Turn Over',
                    'rowspan' => 2,
                ],
            ]
        ];
    }
}
