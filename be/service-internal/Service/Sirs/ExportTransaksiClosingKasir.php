<?php
namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;

class ExportTransaksiClosingKasir extends \Integrasi\Contracts\DocoImplement
{
	public function execute()
    {
        ini_set('memory_limit', '-1');
        $totalPerPage = $this->totalPerPage; 
        $cacheFiles = Yii::$app->cacheFiles;
        $filter = $this->filter;

        $row = [];
        $no = 1;
        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengekstrak data Transaksi Closing Kasir',
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
        $headerExcel = [   
            "Nama Pegawai" =>  $this->nama_pemakai,
        ];
        
        $filePath = DocoHelpers::exportExcel('Laporan Transaksi Closing Kasir', $row, $headerExcel,[
            "skipIncrement" => true,
            'customHeader' => $custHeader,
        ], [], [], true);
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
            'service' => 'Sirs-ExportTransaksiClosingKasir',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'filePath' => $filePath
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
                    'label'=>'Tanggal Pembayaran',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'No Pembayaran',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'No Pendaftaran',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Nama Pasien / No RM',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Transaksi',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Keterangan',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Penjamin',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Cara Bayar Non Tunai',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Total Tagihan (Rp.)',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Tunai (Rp.)',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Non-Tunai (Rp.',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Dijamin (Rp.)',
                    'rowspan'=>2,
                ],
            ]
        ];
    }
}
