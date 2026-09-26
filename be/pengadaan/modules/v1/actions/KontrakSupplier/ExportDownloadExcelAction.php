<?php

namespace app\modules\v1\actions\KontrakSupplier;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;
use app\modules\v1\models\KontrakSupplierTmpForm;
use app\modules\v1\models\Payterm;

class ExportDownloadExcelAction extends Action
{
    const DATERANGE = 'daterange';
    const NUMBER = 'number';
    const STRING_TYPE = 'string';
    const DATE_FORMAT = 'd M Y';

    public function run()
    {
        $result = $header = $footer = $custHeader = [];
        $header = $this->header();
        $custHeader = $this->custHeader();
        $model = new KontrakSupplierTmpForm;
        $result = $this->mappingDataExcel($model);
        $filePath = DocoHelpers::exportExcel('', $result, $header,  array(
            "skipIncrement" => true,
            "skipHeader" => true,
            'customHeader' => $custHeader,
            'customFormatCode' => $this->customFormatCode()
        ),$footer,[], true);
        $filePath->save('php://output');
        die;
    }

    public function mappingDataExcel($data) {
        $cols = $data->columnNames();
        $result = [];
        foreach ($cols as $key => $value) {
            if ($key == 0) {
                $newValue = [];
                foreach ($cols as $col) {
                    $name = $col['name'];
                    $type = ArrayHelper::getValue($col, 'type', '');
                    $rowData = ArrayHelper::getValue($value, $name, '');
                    if($type == self::DATERANGE && $rowData != '') {
                        $rowData = date(self::DATE_FORMAT, strtotime($rowData));
                    }
                    if($type == self::NUMBER) {
                        $rowData = number_format($rowData, 2);
                    }
                    $newValue[\Yii::t('app', $col['label'])] = $rowData;
                }
                $result[$key] = $newValue;
            }
        }
        return $result;
    }

    /**
     * Contoh input column
     */
    private function header()
    {
        $stringPayterm = implode(", ", array_column($this->listPayterm(), 'payterm_nama'));
        return [
            'Kode Supplier' => ('Harus sesuai dari Master Supplier'),
            'PPN' => ('Harus sesuai dari Master pajak, contoh = (11 / 0)'),
            'Payterm' => ('Harus Sesuai dari master Payterm contoh = ('.$stringPayterm.')'),
            'Kode Obat' => ('Harus Sesuai dari master obat alkes'),
            'Nama Obat' => ('Harus sesuai dari master obat alkes'),
            'Kolom tidak boleh kosong' => ('(No Kontrak Supplier, Tgl Berlaku, Kode Supplier, Supplier,	Payterm, PPN, Contact Person, Kode Obat, Nama Obat, Harga, Qty Min)')
        ];
    }

    /**
     * custom header column template
     */
    private function custHeader()
    {
        return [
            [
                [
                    'label'=>'No',
                    'rowspan'=>1,
                ],
                [
                    'label'=>'No Kontrak Supplier',
                    'rowspan'=>1,
                ],
                [
                    'label'=>'Tgl Berlaku (DD/MM/YYYY)',
                    'rowspan'=>1,
                ],
                [
                    'label'=>'Kode Supplier',
                    'rowspan'=>1,
                ],
                [
                    'label'=>'Supplier',
                    'rowspan'=>1,
                ],
                [
                    'label'=>'Payterm',
                    'rowspan'=>1,
                ],
                [
                    'label'=>'PPN (%)',
                    'rowspan'=>1,
                ],
                [
                    'label'=>'Contact Person',
                    'rowspan'=>1,
                ],
                [
                    'label'=>'Kode Obat',
                    'rowspan'=>1,
                ],
                [
                    'label'=>'Nama Obat (Master Item)',
                    'rowspan'=>1,
                ],
                [
                    'label'=>'Harga Order',
                    'rowspan'=>1,
                ],
                [
                    'label'=>'Pengurang',
                    'rowspan'=>1,
                ],
                [
                    'label'=>'Qty Min',
                    'rowspan'=>1,
                ],
                [
                    'label'=>'Total Harga',
                    'rowspan'=>1,
                ],
            ]
        ];
    }

    private function listPayterm()
    {
        return Payterm::find()->select(['payterm_id', 'payterm_kode', 'payterm_nama', 'jumlah_hari'])
            ->where(['is_active' => true, 'is_deleted' => false])
            ->asArray()->all();
    }

    private function customFormatCode()
    {
        return [
            ['selectColumn' => 'B', 'formatCode' => 'general'],
            ['selectColumn' => 'C', 'formatCode' => 'general'],
            ['selectColumn' => 'D', 'formatCode' => 'general'],
            ['selectColumn' => 'E', 'formatCode' => 'general'],
            ['selectColumn' => 'F', 'formatCode' => 'general'],
            ['selectColumn' => 'G', 'formatCode' => 'general'],
            ['selectColumn' => 'H', 'formatCode' => 'general'],
            ['selectColumn' => 'I', 'formatCode' => 'general'],
            ['selectColumn' => 'J', 'formatCode' => 'general'],
            ['selectColumn' => 'K', 'formatCode' => 'general'],
            ['selectColumn' => 'L', 'formatCode' => 'general'],
            ['selectColumn' => 'M', 'formatCode' => 'general'],
            ['selectColumn' => 'N', 'formatCode' => 'general'],
        ];
    }

}