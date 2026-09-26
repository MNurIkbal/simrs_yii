<?php
namespace Integrasi\Service\Sirs;

use Yii;
use yii\helpers\ArrayHelper;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\Supplier;

class ExportPurchaseOrderObatBarang extends \Integrasi\Contracts\DocoImplement
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
        $cito = $admin = $consigment = $no_po = $item_name = $status_po = $supplier_name = '-';
        if (isset($filter['advanced-filter'])) {
            if (isset($filter['advanced-filter']['tanggal_po'])) {
                $explode = explode(" - ", $filter['advanced-filter']['tanggal_po']);
                if (count($explode) == 2) {
                    $start = date('d M Y', strtotime($explode[0]));
                    $end = date('d M Y', strtotime($explode[1]));
                }
                unset($filter['advanced-filter']['tanggal_po']);
            }
            
            if(isset($filter['advanced-filter']['no_po'])) {
                $no_po = $filter['advanced-filter']['no_po'];
            }

            if(isset($filter['advanced-filter']['item_name'])) {
                $item_name = $filter['advanced-filter']['item_name'];
            }

            if(isset($filter['advanced-filter']['status_po'])) {
                $status_po = $filter['advanced-filter']['status_po'];
            }

            if(isset($filter['advanced-filter']['supplier_id'])){
                $supplier_id = $filter['advanced-filter']['supplier_id'];
                $data = Supplier::find()->where(['supplier_id' => $supplier_id])->select(['supplier_nama'])->one();
                $supplier_name = $data['supplier_nama'];
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

        if (isset($filter['advanced-filter'])) {
          if($filter['advanced-filter']['type'] == 'OBAT') {
            $header = array(
                'Tanggal PO' => $start . ' s/d ' . $end,
                'No. Po' => $no_po,
                'Item Name' => $item_name,
                'Status Po' => $status_po,
                'Supplier Name' => $supplier_name,
                'Cito' => $cito,
                'Admin' => $admin,
                'Consigment' => $consigment
            );
          }
          if($filter['advanced-filter']['type'] == 'BARANG') {
            $header = array(
                'Tanggal PO' => $start . ' s/d ' . $end,
                'No. Po' => $no_po,
                'Item Name' => $item_name,
                'Status Po' => $status_po,
                'Supplier Name' => $supplier_name,
                'Cito' => $cito,
                'Admin' => $admin
            );
          }
        }

        $custHeader = $this->custHeader();
         if($filter['advanced-filter']['type'] == 'OBAT'){
            $custHeader[0] = array_merge(array_slice($custHeader[0], 0,8),array(array('label' => 'Consignment','rowspan' => 2)), array_slice($custHeader[0], 8));
        }

        $path = 'uploads/'. $this->unique_str .'.xlsx';

        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengimport data ke dalam excel',
                'progress' => 85
            ]),
        ]);
        
        if($filter['advanced-filter']['type'] == 'OBAT'){
            $options = [
                "skipIncrement" => true,
                'customHeader' => $custHeader,
                "customFormatCode" =>  [
                    ['selectColumn' => 'C', 'formatCode' => 'datetime'],
                        ['selectColumn' => 'E', 'formatCode' => 'datetime'],
                        ['selectColumn' => 'F', 'formatCode' => 'datetime'],
                        ['selectColumn' => 'Q', 'formatCode' => 'number'],
                        ['selectColumn' => 'V', 'formatCode' => 'number'],
                        ['selectColumn' => 'X', 'formatCode' => 'number'],
                        ['selectColumn' => 'Z', 'formatCode' => 'number'],
                        ['selectColumn' => 'AA', 'formatCode' => 'number'],
                        ['selectColumn' => 'AB', 'formatCode' => 'textwrap'],
                        ['selectColumn' => 'AC', 'formatCode' => 'textwrap'],
                        ['selectColumn' => 'AD', 'formatCode' => 'textwrap'],
                        ['selectColumn' => 'AF', 'formatCode' => 'datetime'],
                        ['selectColumn' => 'AH', 'formatCode' => 'datetime'],
                ]
            ];
        }
        if($filter['advanced-filter']['type'] == 'BARANG'){
            $options = [
                "skipIncrement" => true,
                'customHeader' => $custHeader,
                "customFormatCode" =>  [
                    ['selectColumn' => 'C', 'formatCode' => 'datetime'],
                    ['selectColumn' => 'E', 'formatCode' => 'datetime'],
                    ['selectColumn' => 'F', 'formatCode' => 'datetime'],
                    ['selectColumn' => 'P', 'formatCode' => 'number'],
                    ['selectColumn' => 'U', 'formatCode' => 'number'],
                    ['selectColumn' => 'W', 'formatCode' => 'number'],
                    ['selectColumn' => 'Y', 'formatCode' => 'number'],
                    ['selectColumn' => 'Z', 'formatCode' => 'number'],
                    ['selectColumn' => 'AA', 'formatCode' => 'textwrap'],
                    ['selectColumn' => 'AB', 'formatCode' => 'textwrap'],
                    ['selectColumn' => 'AC', 'formatCode' => 'textwrap'],
                    ['selectColumn' => 'AE', 'formatCode' => 'datetime'],
                    ['selectColumn' => 'AG', 'formatCode' => 'datetime'],
                ]
            ];
        }
        
        $filePath = DocoHelpers::exportExcel('LAPORAN PURCHASE ORDER', $row, $header, $options, $footer, [], true);
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
            'service' => 'Sirs-ExportPurchaseOrderObatBarang',
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
                    'label'=>'PR No',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'PR Approval Date',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'PO No',
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
                    'label'=>'Supplier Name',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Manufacturer',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Item Code',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Item Name',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Qty',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Qty GRN',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Qty Outstanding',
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
                    'label'=>'Discount %',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Discount(Rp.)',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Tax %',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Gross Amount',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Nett Amount',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'PO Remarks',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Catatan Internal',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Catatan Eksternal',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Status',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Reject Date',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Reject Remarks',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Reception Date',
                    'rowspan'=>2,
                ],
            ]
        ];
    }
}
