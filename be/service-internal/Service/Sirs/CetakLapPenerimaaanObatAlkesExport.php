<?php
namespace Integrasi\Service\Sirs;

use Yii;
use Integrasi\Components\DocoHelpers;
use yii\helpers\ArrayHelper;
use Integrasi\Service\Sirs\Models\Payterm;
use Integrasi\Service\Sirs\Models\Supplier;
use Integrasi\Service\Sirs\Models\LaporanPenerimaanObatAlkesView;

class CetakLapPenerimaaanObatAlkesExport extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        ini_set('memory_limit', '-1');
        $totalPerPage = $this->totalPerPage; 
        $cache = Yii::$app->cache;
        $row = [];
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

        $path = 'uploads/'. $this->unique_str .'.xlsx';
        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengimport data',
                'progress' => 85
            ]),
        ]);

        $model = new LaporanPenerimaanObatAlkesView;
        $header = $this->setHeader();

        $filePath = DocoHelpers::exportExcel('Laporan Penerimaan Obat Alkes', $row, $header, [
            "skipIncrement" => true,
            'customHeader' => $this->custHeader(),
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
            'service' => 'Sirs-CetakLapPenerimaaanObatAlkesExport',
            'timestamp' => date('Y-m-d H:i:s'),
            'file' => $path
        ]);
    }
    
    protected function customFormatCode()
    {
        return [
            ['selectColumn' => 'B', 'formatCode' => 'datetime'],
            ['selectColumn' => 'C', 'formatCode' => 'datetime'],
            ['selectColumn' => 'H', 'formatCode' => 'datetime'],
            ['selectColumn' => 'L', 'formatCode' => 'datetime'],
            ['selectColumn' => 'M', 'formatCode' => 'datetime'],
            ['selectColumn' => 'R', 'formatCode' => 'number2'],
            ['selectColumn' => 'T', 'formatCode' => 'number2'],
            ['selectColumn' => 'U', 'formatCode' => 'number2'],
            ['selectColumn' => 'V', 'formatCode' => 'number2'],
            ['selectColumn' => 'X', 'formatCode' => 'number2'],
            ['selectColumn' => 'Z', 'formatCode' => 'number2'],
            ['selectColumn' => 'AA', 'formatCode' => 'number2'],
            ['selectColumn' => 'AC', 'formatCode' => 'number2'],
            ['selectColumn' => 'AB', 'formatCode' => 'number2'],
            ['selectColumn' => 'AD', 'formatCode' => 'number2'],
            ['selectColumn' => 'AE', 'formatCode' => 'number2'],
            ['selectColumn' => 'AF', 'formatCode' => 'number2'],
            ['selectColumn' => 'AG', 'formatCode' => 'number2'],
            ['selectColumn' => 'AK', 'formatCode' => 'general'],
            ['selectColumn' => 'AL', 'formatCode' => 'datetime'],
        ];
    }

    protected function custHeader() 
    {
        return [
            [
                [
                    'label'=>'No',
                    'rowspan' => 2
                ],
                [
                    'label' => "Tanggal PR", 
                    'rowspan' => 2
                ],
                [
                    'label' => "Tanggal Approve PR", 
                    'rowspan' => 2
                ],
                [
                    'label' => "Kode Supplier",
                    'rowspan' => 2
                ],
                [
                    'label' => "Nama Supplier",
                    'rowspan' => 2
                ],
                [
                    'label' => "Nama Manufaktur",
                    'rowspan' => 2
                ],
                [
                    'label' => "Payment Term",
                    'rowspan' => 2
                ],
                [
                    'label' => "Tanggal Penerimaan",
                    'rowspan' => 2
                ],
                [
                    'label' => "Nomor Penerimaan",
                    'rowspan' => 2
                ],
                [
                    'label' => "Diterima Oleh",
                    'rowspan' => 2
                ],
                [
                    'label' => "Status Penerimaan",
                    'rowspan' => 2
                ],
                [
                    'label' => "Tanggal PO",
                    'rowspan' => 2
                ],
                [
                    'label' => "Tanggal Validasi PO",
                    'rowspan' => 2
                ],
                [
                    'label' => "Nomor PO",
                    'rowspan' => 2
                ],
                [
                    'label' => "Kode Obat Alkes",
                    'rowspan' => 2
                ],
                [
                    'label' => "Nama Obat Alkes",
                    'rowspan' => 2
                ],
                [
                    'label' => "Jenis Obat Alkes",
                    'rowspan' => 2
                ],
                [
                    'label' => "Qty PO", 
                    'rowspan' => 2
                ],
                [
                    'label' => "Satuan Besar PO", 
                    'rowspan' => 2
                ],
                [
                    'label' => "Qty Penerimaan", 
                    'rowspan' => 2
                ],
                [
                    'label' => "Qty Return", 
                    'rowspan' => 2
                ],
                [
                    'label' => "Qty Diterima", 
                    'rowspan' => 2
                ],
                [
                    'label' => "Satuan Besar Terima", 
                    'rowspan' => 2
                ],
                [
                    'label' => "PO Balance",
                    'rowspan' => 2
                ],
                [
                    'label' => "Satuan Besar PO Balance", 
                    'rowspan' => 2
                ],
                [
                    'label' => "Nilai Konversi", 
                    'rowspan' => 2
                ],
                [
                    'label' => "Qty Konversi", 
                    'rowspan' => 2
                ],
                [
                    'label' => "Satuan Kecil", 
                    'rowspan' => 2
                ],
                [
                    'label' => "Harga Netto (Rp.)", 
                    'rowspan' => 2
                ],
                [
                    'label' => "Harga (Rp.)", 
                    'rowspan' => 2
                ],
                [
                    'label' => "Discount (%)", 
                    'rowspan' => 2
                ],
                [
                    'label' => "PPN (%)", 
                    'rowspan' => 2
                ],
                [
                    'label' => "Subtotal (Rp.)", 
                    'rowspan' => 2
                ],
                [
                    'label' => "Total (Rp.)", 
                    'rowspan' => 2
                ],
                [
                    'label' => "Catatan PO", 
                    'rowspan' => 2
                ],
                [
                    'label' => "Nomor PR", 
                    'rowspan' => 2
                ],
                [
                    'label' => "Nomor Batch", 
                    'rowspan' => 2
                ],
                [
                    'label' => "Tanggal Kadaluarsa", 
                    'rowspan' => 2
                ],
                [
                    'label' => "No. Surat Jalan", 
                    'rowspan' => 2
                ],
                [
                    'label' => "No. Faktur", 
                    'rowspan' => 2
                ],
            ]
        ];
    }

    protected function setUpHeader()
    {
        $filter = $this->filter;
        $advFilter = ArrayHelper::getValue($filter, 'advanced-filter', []);
        $reportHeader = [];
        foreach($advFilter as $key => $value) {
            if (!empty($value)) {
                $reportHeader[$key] = $value;
            }
        }
        return $reportHeader;
    }

    protected function setHeader()
    {
        $request = $this->filter;

        $tglPenerimaan = $paytermNama = $nomerPo = $penerimaan = $obatAlkes = '';
        $suppliers = [];
        if (isset($request['advanced-filter'])) {
            $advancedFilter = $request['advanced-filter'];
            if (!empty($advancedFilter['tgl_penerimaan'])) {
                $tglPenerimaan = $advancedFilter['tgl_penerimaan'];
            }
            if(!empty($advancedFilter['supplier_id'])) {
                $supplierIds = $advancedFilter['supplier_id'];
                $supplier = Supplier::find()
                ->select(['supplier_nama'])
                ->where(['IN', 'supplier_id', $supplierIds])
                ->asArray()->all();
                $suppliers = ArrayHelper::getColumn($supplier, 'supplier_nama');
            }
            if(!empty($advancedFilter['payterm_id'])) {
                $payterm_id = $advancedFilter['payterm_id'];
                $payterm = Payterm::find()
                ->select(['payterm_nama'])
                ->where(['payterm_id' => $payterm_id])
                ->one();
                $paytermNama = ArrayHelper::getValue($payterm, 'payterm_nama');
            }
            if(!empty($advancedFilter['nomor_po'])) {
                $nomerPo = $advancedFilter['nomor_po'];
            }
            if(!empty($advancedFilter['obatalkes_nama'])) {
                $obatAlkes = $advancedFilter['obatalkes_nama'];
            }
            if(!empty($advancedFilter['no_penerimaan'])) {
                $penerimaan = $advancedFilter['no_penerimaan'];
            }
        }

        $header = array(
            'Tanggal Penerimaan' => $tglPenerimaan,
            'Nama Supplier' => implode(" , ", $suppliers),
            'No.Penerimaan' => $penerimaan,
            'Payment Term' => $paytermNama,
            'No.PO' => $nomerPo,
            'Nama Obat Alkes' => $obatAlkes,
        );

        return $header;
    }
}