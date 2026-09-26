<?php
namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\CaraBayar;
use Integrasi\Service\Sirs\Models\Lookup;

class ExportExcelLaporanTotalRekapPenjualanFarmasi extends \Integrasi\Contracts\DocoImplement
{
	public function execute()
    {
        ini_set('memory_limit', '-1');
        $totalPerPage = $this->totalPerPage; 
        $cacheFiles = Yii::$app->cacheFiles;
        $filter = $this->filter;
        $row = $footer = [];
        $no = 1; $jenis_obat = "-";
        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengekstrak data Penjualan Resep',
                'progress' => 80
            ]),
        ]);

        for ($x = 0; $x < $totalPerPage; $x++) {
            $data = $cacheFiles->get($this->unique_str .'-'. $x);
            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    $row[] = $value;
                    if(isset($filter['advanced-filter']) && isset($filter['advanced-filter']['jenisobatalkes_nama'])) {
                        $jenis_obat = $value[3];
                    }        
                }
            }
            $cacheFiles->delete($this->unique_str .'-'. $x);
        }

        $start   = date('d F Y');
        $end     = date('d F Y');

        if(isset($filter['advanced-filter'])) {
            if(isset($filter['advanced-filter']['tgl'])) {
                $explode = explode(" - ", $filter['advanced-filter']['tgl']);
                if(count($explode) == 2) {
                    $start = date('d F Y', strtotime($explode[0]));
                    $end = date('d F Y', strtotime($explode[1]));
                }
            }
        }

        $header = [
            'Tanggal Penjualan' => $start.' Sampai Dengan '.$end,
            'Jenis Obat' => $jenis_obat
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
        $filePath = DocoHelpers::exportExcel('Laporan Total Rekapitulasi Penjualan Farmasi', $row, $header,[
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
            'service' => 'Sirs-ExportExcelLaporanTotalRekapPenjualanFarmasi',
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
                    'label'=>'Kode Obat',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Jenis Obat',
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
                    'label'=>'Satuan Kecil',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Diskon (Rp.)',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Total Harga (Rp.)',
                    'rowspan'=>2,
                ],
            ]
        ];
    }
}
