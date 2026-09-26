<?php
namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use yii\helpers\ArrayHelper;

class ExportPemakaianBmhp extends \Integrasi\Contracts\DocoImplement
{
	public function execute()
    {
        ini_set('memory_limit', '-1');
        $pageCount = $this->pageCount; 
        $cacheFiles = Yii::$app->cacheFiles;
        $filter = $this->filter;
        $row = $footer = [];
        $no = 1;
        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengekstrak data Pemakaian Bmhp Ruangan',
                'progress' => 80
            ]),
        ]);

        for ($x = 0; $x < $pageCount; $x++) {
            $data = $cacheFiles->get($this->unique_str .'-'. $x);
            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    $row[] = $value;
                }
            }
            $cacheFiles->delete($this->unique_str .'-'. $x);
        }

        $header = $this->header($filter);
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
        $filePath = DocoHelpers::exportExcel('Laporan Pemakaian Bmhp Ruangan', $row, $header,[
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
            'service' => 'Sirs-ExportPemakaianBmhp',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function header($filter)
    {
        $filter = ArrayHelper::getValue($filter, 'advanced-filter', []);
        $start = $end = date('d M Y');
        $kode_obat = $nama_obat = $ruangan_nama = $jenisobatalkes = '-';
        if($filter) {
            if (!empty(ArrayHelper::getValue($filter, 'tgl_transaksi'))) {
                $explode = explode(" - ", ArrayHelper::getValue($filter, 'tgl_transaksi'));
                if(count($explode) == 2) {
                    $start = date('d M Y', strtotime($explode[0]));
                    $end = date('d M Y', strtotime($explode[1]));
                }
            }
            $kode_obat = ArrayHelper::getValue($filter, 'kode_obat');
            $nama_obat = ArrayHelper::getValue($filter, 'nama_obat');
            
            $ruangan_nama = ArrayHelper::getValue($filter, 'ruangan_nama');
            $jenisobatalkes = ArrayHelper::getValue($filter, 'jenisobatalkes_nama');
        }
        return [
            'Tanggal Penjualan' => $start.' s/d '.$end,
            'Nama Ruangan' => $ruangan_nama,
            'Jenis Obat Alkes' => $jenisobatalkes,
            'Kode Obat' => $kode_obat,
            'Nama Obat' => $nama_obat,
        ];
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
                    'label'=>'Tanggal Transaksi',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'No Transaksi',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Jenis Obat Alkes',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Kode Obat',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Nama Obat',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Qty',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Satuan',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Harga Satuan (Rp)',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Total Harga (Rp)',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'User',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Catatan',
                    'rowspan'=>2,
                ],
            ]
        ];
    }

    private function customFormatCode()
    {
        return [
            ['selectColumn' => 'B', 'formatCode' => 'general'],
            ['selectColumn' => 'C', 'formatCode' => 'datetime'],
            ['selectColumn' => 'D', 'formatCode' => 'general'],
            ['selectColumn' => 'E', 'formatCode' => 'general'],
            ['selectColumn' => 'F', 'formatCode' => 'general'],
            ['selectColumn' => 'G', 'formatCode' => 'general'],
            ['selectColumn' => 'H', 'formatCode' => 'general'],
            ['selectColumn' => 'I', 'formatCode' => 'general'],
            ['selectColumn' => 'J', 'formatCode' => 'number'],
            ['selectColumn' => 'K', 'formatCode' => 'number'],
            ['selectColumn' => 'L', 'formatCode' => 'general'],
            ['selectColumn' => 'M', 'formatCode' => 'general'],
        ];
    }
}
