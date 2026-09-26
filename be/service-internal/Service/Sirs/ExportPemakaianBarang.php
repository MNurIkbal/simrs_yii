<?php
namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;

class ExportPemakaianBarang extends \Integrasi\Contracts\DocoImplement
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
                'messageProcess' => 'Sedang mengekstrak data Pemakaian Barang',
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
        $ruangan = $nama_barang = $kode_barang = $kelompok ="";
        if (isset($filter['advanced-filter'])) {
            if (isset($filter['advanced-filter']['tgl_transaksi'])) {
                $explode = explode(" - ", $filter['advanced-filter']['tgl_transaksi']);
                if (count($explode) == 2) {
                    $start = date('d M Y', strtotime($explode[0]));
                    $end = date('d M Y', strtotime($explode[1]));
                }
                unset($filter['advanced-filter']['tgl_transaksi']);
            }

            if (isset($filter['advanced-filter']['ruangan_nama'])) {
                $ruangan = $filter['advanced-filter']['ruangan_nama'];
                unset($filter['advanced-filter']['ruangan_nama']);
            }

            if (isset($filter['advanced-filter']['barang_nama'])) {
                $nama_barang = $filter['advanced-filter']['barang_nama'];
                unset($filter['advanced-filter']['barang_nama']);
            }

            if (isset($filter['advanced-filter']['barang_kode'])) {
                $kode_barang = $filter['advanced-filter']['barang_kode'];
                unset($filter['advanced-filter']['barang_kode']);
            }
            
            if (isset($filter['advanced-filter']['kelompokbarang_nama'])) {
                $kelompok = $filter['advanced-filter']['kelompokbarang_nama'];
                unset($filter['advanced-filter']['kelompokbarang_nama']);
            }
        }

        $header = [
            'Tanggal Transaksi' => $start . ' s/d ' . $end,
            'Ruangan' => $ruangan,
            'Nama Barang' => $nama_barang,
            'Kode Barang' => $kode_barang,
            'Kelompok Barang' => $kelompok
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
        
        $filePath = DocoHelpers::exportExcel('LAPORAN PEMAKAIAN BARANG', $row, $header,[
            "skipIncrement" => true,
            'customHeader' => $custHeader,
            "customFormatCode" => [
                ['selectColumn' => 'C', 'formatCode' => 'datetime'],
                ['selectColumn' => 'G', 'formatCode' => 'number'],
                ['selectColumn' => 'I', 'formatCode' => 'number'],
                ['selectColumn' => 'J', 'formatCode' => 'number'],
                ['selectColumn' => 'K', 'formatCode' => 'number']
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
            'service' => 'Sirs-ExportPemakaianBarang',
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
                    'label' => 'Nama Ruangan',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Tanggal Transaksi',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'No Transaksi',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Kelompok Barang',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Kode Barang',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Nama Barang',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Qty',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Satuan',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Harga Satuan (Rp)',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Total Harga (Rp)',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'User',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Catatan',
                    'rowspan' => 2,
                ],
            ]
        ];
    }
}
