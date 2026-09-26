<?php
namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Service\Sirs\Models\PegawaiMasterView;
use Integrasi\Service\Sirs\Models\CaraBayar;
use Integrasi\Service\Sirs\Models\Penjamin;
use Integrasi\Service\Sirs\Models\Ruangan;
use Integrasi\Components\DocoHelpers;

class ExportInformasiStokBarang extends \Integrasi\Contracts\DocoImplement
{
	public function execute()
    {
        ini_set('memory_limit', '-1');
        $totalPerPage = $this->totalPerPage; 
        $cacheFiles = Yii::$app->cacheFiles;
        $filter = $this->filter;
        $db = Yii::$app->db;
        $row = $footer = [];
        $no = 1;
        $barang_nama = $barang_kode = $kelompok_barang = $ruangan = "";
        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengekstrak data Informasi Stok Barang',
                'progress' => 80
            ]),
        ]);
        
        for ($x = 0; $x < $totalPerPage; $x++) {
            $data = $cacheFiles->get($this->unique_str .'-'. $x);
            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    $row[] = $value;
                    if (isset($filter['advanced-filter']['ruangan_id']) && !empty($filter['advanced-filter']['ruangan_id'])) {
                        $ruangan = $value[2];
                    }
                }
            }
            $cacheFiles->delete($this->unique_str .'-'. $x);
        }
        
        if(isset($filter['advanced-filter'])) {
            $advancedFilter = $filter['advanced-filter'];
            if (isset($advancedFilter['barang_nama']) && !empty($advancedFilter['barang_nama'])) {
                $barang_nama = $advancedFilter['barang_nama'];
            }

            if (isset($advancedFilter['barang_kode']) && !empty($advancedFilter['barang_kode'])) {
                $barang_kode = $advancedFilter['barang_kode'];
            }

            if (isset($advancedFilter['kelompokbarang_nama']) && !empty($advancedFilter['kelompokbarang_nama'])) {
                $kelompok_barang = $advancedFilter['kelompokbarang_nama'];
            }

        }

        $header = array(
            Yii::t("app", "Ruangan") => $ruangan,
            Yii::t("app", "Nama Barang") => strtoupper($barang_nama),
            Yii::t("app", "Kode Barang") => strtoupper($barang_kode),
            Yii::t("app", "Kelompok Barang") => strtoupper($kelompok_barang)
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
        
        $filePath = DocoHelpers::exportExcel('Informasi Stok Barang', $row, $header,[
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
            'service' => 'Sirs-ExportInformasiStokBarang',
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
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Nama Ruangan',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Nama Barang',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Kode Barang',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Kelompok Barang',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Qty Dipesan',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Qty Tersedia',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Stok Total',
                        'rowspan' => 2
                    ]
                ]
            ];
    }
}
