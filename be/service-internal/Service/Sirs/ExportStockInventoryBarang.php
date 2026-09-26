<?php
namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\Instalasi;
use Integrasi\Service\Sirs\Models\Ruangan;

class ExportStockInventoryBarang extends \Integrasi\Contracts\DocoImplement
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
                'messageProcess' => 'Sedang mengekstrak data Stock Inventory Barang',
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

        $db = Yii::$app->db;
        $ruangan_nama = $jenis = $instalasi = '';
        if (isset($filter['advanced-filter'])) {
            if(isset($filter['advanced-filter']['tanggal_inventory'])){
                $date = date('Y-m-d', strtotime($filter['advanced-filter']['tanggal_inventory']));
            }
            if(isset($filter['advanced-filter']['instalasi_id'])){
                $instalasi_id = $filter['advanced-filter']['instalasi_id'];
                $data = Instalasi::find()->where(['instalasi_id' => $instalasi_id])->select(['instalasi_nama'])->one();
                $instalasi = $data['instalasi_nama'];
            }
            if(isset($filter['advanced-filter']['ruanganid'])){
                $ruangan_id = $filter['advanced-filter']['ruanganid'];
                $data = Ruangan::find()->where(['ruangan_id' => $ruangan_id])->select(['ruangan_nama'])->one();
                $ruangan_nama = $data['ruangan_nama'];
            }

            if(isset($filter['advanced-filter']['kelompok_barang'])){
                $jenis = $filter['advanced-filter']['kelompok_barang'];
            }
        }

        $header = array(
            'Tanggal Inventory' => (@$date),
            'Instalasi' => $instalasi,
            'Nama Ruangan' => $ruangan_nama,
            'Kelompok Barang' => $jenis

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
        
        $filePath = DocoHelpers::exportExcel('LAPORAN STOCK INVENTORY BARANG', $row, $header,[
            "skipIncrement" => true,
            'customHeader' => $custHeader,
            "customFormatCode" => [
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
            'service' => 'Sirs-ExportStockInventoryBarang',
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
                    'label'=>'Kode Barang',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Nama Barang',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Kelompok Barang',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Sub Kelompok',
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
                    'label'=>'Harga Netto Satuan Kecil',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Harga Netto Satuan Besar',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Total(Rp.) Satuan Kecil',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Total(Rp.) Satuan Besar',
                    'rowspan'=>2,
                ],
            ]
        ];
    }
}