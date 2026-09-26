<?php
namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoRestActiveFilter;
use Integrasi\Service\Sirs\Models\LaporanAnalisaPoNonMedisView;
use yii\helpers\ArrayHelper;

class ExportAnalisaPONonMedis extends \Integrasi\Contracts\DocoImplement
{
	public function execute()
    {
        ini_set('memory_limit', '-1');
        $totalPerPage = $this->totalPerPage; 
        $cache = Yii::$app->cache;
        $filter = $this->filter;
        $row = [];
        $title = 'Laporan PO Analisa Non Medis';
        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengekstrak data',
                'progress' => 80
            ]),
        ]);

        for ($x = 0; $x < $totalPerPage; $x++) {
            $data = $cache->get($this->unique_str .'-'. $x);
            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    $row[] = $value;
                }
            }
            $cache->delete($this->unique_str .'-'. $x);
        }

        $advanced_filter = $filter['advanced-filter'];
        $startTglPR =  $endTglPR =  $startTglPO =  $endTglPO = $namaBarang = $noPo = '-';
        $poCito = $poAdmin = '-';
        if(isset($advanced_filter)) {
            if(isset($advanced_filter['tgl_pr'])) {
                $explode = explode(" - ", $advanced_filter['tgl_pr']);
                if(count($explode) == 2) {
                    $startTglPR = date('d-M-Y', strtotime($explode[0]));
                    $endTglPR = date('d-M-Y', strtotime($explode[1]));
                }
                unset($advanced_filter['tgl_pr']);
            }
            if(isset($advanced_filter['tgl_po'])) {
                $explode = explode(" - ", $advanced_filter['tgl_po']);
                if(count($explode) == 2) {
                    $startTglPO = date('d-M-Y', strtotime($explode[0]));
                    $endTglPO = date('d-M-Y', strtotime($explode[1]));
                }
                unset($advanced_filter['tgl_po']);
            }
            if(isset($advanced_filter['nama_barang'])) {
                $namaBarang = $advanced_filter['nama_barang'];
            }
            if(isset($advanced_filter['no_po'])) {
                $noPo = $advanced_filter['no_po'];
            }
            if(isset($advanced_filter['po_cito'])) {
                $poCito = $advanced_filter['po_cito'];
            }
            if(isset($advanced_filter['po_admin'])) {
                $poAdmin = $advanced_filter['po_admin'];
            }
        }
        $header = [
            'Tanggal Buat PR' => ($startTglPR && $endTglPR != '-') ? $startTglPR.' Sampai Dengan '.$endTglPR : '',
            'Tanggal Buat PO' => ($startTglPO && $endTglPO != '-') ? $startTglPO.' Sampai Dengan '.$endTglPO : '',
            'Nama Barang' => $namaBarang,
            'No Po' => $noPo,
            'Cito' => $poCito,
            'Admin' => $poAdmin,
        ];       

        $path = 'uploads/'. $this->unique_str .'.xlsx';
        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengimport data Laporan Analisa PO Non Medis',
                'progress' => 85
            ]),
        ]);

        $custHeader = $this->custHeader();
        $filePath = DocoHelpers::exportExcel($title, $row, $header, [
            "skipIncrement" => true,
            'customHeader' => $custHeader,
            "customFormatCode" => $this->customFormatCode(),
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
            'service' => 'Sirs-ExportAnalisaPONonMedis',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function customFormatCode()
    {
        return [
            ['selectColumn' => 'B', 'formatCode' => 'general'],
            ['selectColumn' => 'C', 'formatCode' => 'general'],
            ['selectColumn' => 'D', 'formatCode' => 'general'],
            ['selectColumn' => 'E', 'formatCode' => 'datetime'],
            ['selectColumn' => 'F', 'formatCode' => 'datetime'],
            ['selectColumn' => 'G', 'formatCode' => 'number2'],
            ['selectColumn' => 'H', 'formatCode' => 'general'],
            ['selectColumn' => 'I', 'formatCode' => 'general'],
            ['selectColumn' => 'J', 'formatCode' => 'general'],
            ['selectColumn' => 'K', 'formatCode' => 'general'],
            ['selectColumn' => 'L', 'formatCode' => 'general'],
            ['selectColumn' => 'M', 'formatCode' => 'datetime'],
            ['selectColumn' => 'N', 'formatCode' => 'datetime'],
            ['selectColumn' => 'O', 'formatCode' => 'datetime'],
            ['selectColumn' => 'P', 'formatCode' => 'general'],
            ['selectColumn' => 'Q', 'formatCode' => 'number2'],
            ['selectColumn' => 'R', 'formatCode' => 'general'],
            ['selectColumn' => 'S', 'formatCode' => 'number2'],
            ['selectColumn' => 'T', 'formatCode' => 'number2'],
            ['selectColumn' => 'U', 'formatCode' => 'general'],
            ['selectColumn' => 'V', 'formatCode' => 'number2'],
            ['selectColumn' => 'W', 'formatCode' => 'number2'],
            ['selectColumn' => 'X', 'formatCode' => 'general'],
            ['selectColumn' => 'Y', 'formatCode' => 'datetime'],
            ['selectColumn' => 'Z', 'formatCode' => 'number2'],
            ['selectColumn' => 'AA', 'formatCode' => 'number2'],
            ['selectColumn' => 'AB', 'formatCode' => 'number2'],
            ['selectColumn' => 'AC', 'formatCode' => 'number2'],
            ['selectColumn' => 'AD', 'formatCode' => 'general'],
            ['selectColumn' => 'AE', 'formatCode' => 'datetime'],
        ];
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
                        'label'=>'Kode Barang',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Nama Barang',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'No. PR',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Tanggal Buat PR',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Tanggal Approve PR',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Qty PR',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'UoM PR',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Catatan PR',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'No. PO',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Cito',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Admin',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Tanggal Buat PO',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Tanggal Validasi PO',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Tanggal Batal PO',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Catatan Batal PO',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Qty PO',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'UoM PO',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Harga (Rp.)',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Diskon (%)',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'PPn (%)',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Subtotal (Rp.)',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Total (Rp.)',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'No. Penerimaan',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Tanggal Penerimaan',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Qty Penerimaan',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'UoM Penerimaan',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Sisa Penerimaan (PO Ballance)',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'UoM Sisa Penerimaan',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'No. Faktur Penerimaan',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Tanggal Verifikasi Penerimaan',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'PR diapprove ke PO',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'PO dibuat ke Validasi PO',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'PO dibuat ke Tanggal Penerimaan',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'PR diapprove ke Tanggal Penerimaan',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'PO divalidasi ke Tanggal Penerimaan',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Kode Supplier',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Nama Supplier',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Catatan PO',
                        'rowspan' => 2
                    ]
                ]
            ];
    }
}
