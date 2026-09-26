<?php
namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\Instalasi;
use Integrasi\Service\Sirs\Models\Ruangan;

class ExportPurchaseRequisitionOutStanding extends \Integrasi\Contracts\DocoImplement
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
                'messageProcess' => 'Sedang mengekstrak data Purchase Requisition Outstanding',
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
            if (isset($filter['advanced-filter']['tgl_pr'])) {
                $explode = explode(" - ", $filter['advanced-filter']['tgl_pr']);
                if (count($explode) == 2) {
                    $start = date('d M Y', strtotime($explode[0]));
                    $end = date('d M Y', strtotime($explode[1]));
                }
                unset($filter['advanced-filter']['tgl_pr']);
            }

            if(isset($filter['advanced-filter']['is_cyto'])) {
                $cito = $filter['advanced-filter']['is_cyto'];
            }

            if(isset($filter['advanced-filter']['is_admin'])) {
                $admin = $filter['advanced-filter']['is_admin'];
            }

            if(isset($filter['advanced-filter']['is_consigment'])) {
                $consigment = $filter['advanced-filter']['is_consigment'];
            }
        }

        $header = array(
            'Tanggal PR' => $start . ' s/d ' . $end,
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
        
        $filePath = DocoHelpers::exportExcel('LAPORAN PURCHASE REQUISITION OUTSTANDING', $row, $header,[
            "skipIncrement" => true,
            'customHeader' => $custHeader,
            "customFormatCode" => [
                ['selectColumn' => 'C', 'formatCode' => 'datetime'],
                ['selectColumn' => 'D', 'formatCode' => 'datetime'],
                ['selectColumn' => 'K', 'formatCode' => 'number'],
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
            'service' => 'Sirs-ExportPurchaseRequisitionOutStanding',
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
                    'label'=>'Tanggal PR',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Tanggal Approve',
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
                    'label'=>'Kode Obat',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Nama Obat',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Jenis Obat',
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
                    'label'=>'UoM',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Satuan PR',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Manufaktur',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Status Obat',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Catatan',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Alasan Batal',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Dibuat Oleh',
                    'rowspan'=>2,
                ],
            ]
        ];
    }
}