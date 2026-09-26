<?php
namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\Instalasi;
use Integrasi\Service\Sirs\Models\Ruangan;

class ExportPurchaseOrderBarang extends \Integrasi\Contracts\DocoImplement
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
                'messageProcess' => 'Sedang mengekstrak data Purchase Order Outstanding Non-Medis',
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
        $cito = $admin = '';
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
        }

        $header = array(
            'Tanggal PO' => $start . ' s/d ' . $end,
            'Cito' => $cito,
            'Admin' => $admin

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
        
        $filePath = DocoHelpers::exportExcel('LAPORAN PURCHASE ORDER OUTSTANDING NON-MEDIS ', $row, $header,[
            "skipIncrement" => true,
            'customHeader' => $custHeader,
            "customFormatCode" => [
                ['selectColumn' => 'C', 'formatCode' => 'datetime'],
                ['selectColumn' => 'E', 'formatCode' => 'datetime'],
                ['selectColumn' => 'F', 'formatCode' => 'datetime'],
                ['selectColumn' => 'N', 'formatCode' => 'number'],
                ['selectColumn' => 'Q', 'formatCode' => 'number'],
                ['selectColumn' => 'S', 'formatCode' => 'number'],
                ['selectColumn' => 'T', 'formatCode' => 'number'],
                ['selectColumn' => 'U', 'formatCode' => 'number'],
                ['selectColumn' => 'V', 'formatCode' => 'number'],
                ['selectColumn' => 'W', 'formatCode' => 'number'],
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
            'service' => 'Sirs-ExportPurchaseOrderBarang',
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
                    'label'=>'Nomor PR',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Tanggal Approve',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Nomor PO',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'PO Create Date',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'PO Approval Date',
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
                    'label'=>'Kode Supplier',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Supplier',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Manufaktur',
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
                    'label'=>'Qty',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'UoM',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'From UOM',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Factor',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'To UOM',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Price',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Ded %',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Add %',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Gross Amount',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Nett Amount Incl PPn',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'PO Remarks',
                    'rowspan'=>2,
                ],
            ]
        ];
    }
}