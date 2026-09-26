<?php
namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\Instalasi;
use Integrasi\Service\Sirs\Models\Ruangan;

class ExportPurchaseOrder extends \Integrasi\Contracts\DocoImplement
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
                'messageProcess' => 'Sedang mengekstrak data Purchase Order',
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
        $db = Yii::$app->db;
        $cito = $admin = $consigment = '';
        if (isset($filter['advanced-filter'])) {
            if (isset($filter['advanced-filter']['tgl_po_dibuat'])) {
                $explode = explode(" - ", $filter['advanced-filter']['tgl_po_dibuat']);
                if (count($explode) == 2) {
                    $start = date('d M Y', strtotime($explode[0]));
                    $end = date('d M Y', strtotime($explode[1]));
                }
                unset($filter['advanced-filter']['tgl_po_dibuat']);
            }

            if(isset($filter['advanced-filter']['is_cito'])) {
                $cito = $filter['advanced-filter']['is_cito'];
            }

            if(isset($filter['advanced-filter']['is_admin'])) {
                $admin = $filter['advanced-filter']['is_admin'];
            }

            if(isset($filter['advanced-filter']['is_consigment'])) {
                $consigment = $filter['advanced-filter']['is_consigment'];
            }
        }

        $header = array(
            'Tanggal PO' => $start . ' s/d ' . $end,
            'Cito' => $cito,
            'Admin' => $admin,
            'Consignment' => $consigment

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
        
        $filePath = DocoHelpers::exportExcel('LAPORAN PURCHASE ORDER OUTSTANDING', $row, $header,[
            "skipIncrement" => true,
            'customHeader' => $custHeader,
            "customFormatCode" => [
                ['selectColumn' => 'B', 'formatCode' => 'datetime'],
                ['selectColumn' => 'C', 'formatCode' => 'datetime'],
                ['selectColumn' => 'F', 'formatCode' => 'datetime'],
                ['selectColumn' => 'G', 'formatCode' => 'datetime'],
                ['selectColumn' => 'Q', 'formatCode' => 'number'],
                ['selectColumn' => 'U', 'formatCode' => 'number'],
                ['selectColumn' => 'V', 'formatCode' => 'number'],
                ['selectColumn' => 'W', 'formatCode' => 'number'],
                ['selectColumn' => 'X', 'formatCode' => 'number'],
                ['selectColumn' => 'Y', 'formatCode' => 'number'],
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
            'service' => 'Sirs-ExportPurchaseOrder',
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
                    'label'=>'Tanggal PR',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Tanggal Approve',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Nomor PR',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Nomor PO',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Tanggal PO',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Tanggal Validasi PO',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Cito',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Admin',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Consignment',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Manufaktur',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Kode Supplier',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Supplier',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Jenis Obat',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Kode Item',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Nama Item',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Qty PO',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'PO Balance',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Satuan',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'UoM',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Harga Netto (Rp.)',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Diskon (%)',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'PPn (%)',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Sub Total (Rp.)',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Total setelah PPn (Rp.)',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Catatan 1',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Catatan 2',
                    'rowspan'=>2,
                ],
            ]
        ];
    }
}