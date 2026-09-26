<?php
namespace app\modules\v1\models;

use Yii;

class LaporanStockInventoryFn extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName()
    {
        return "laporanstockinventory_fn";
    }

    public static function attributSchema()
    {
        return [
            'varchar' => [
                'ruangan_nama',
                'obatalkes_kode',
                'obatalkes_nama',
                'satuan_besar',
                'satuan_kecil',
                'jenis_obat',
                'is_generik',
                'is_oral',
            ],
            'float8'  => [
                'harga_konversi',
                'wa_satuan_kecil',
                'wa_satuan_besar',
                'harga_netto',
                'harga',
                'total_harga',
                'nilai_konv',
                'baseprice_kecil',
                'baseprice_besar',
                'total_satuankecil',
                'total_satuanbesar',
                'qty_satuankecil',
                'qty_satuanbesar',
                'total_satuankecil_netto',
                'total_satuanbesar_netto'
            ]
        ];
    }

    public static function getData($date = null)
    {
        $date = !empty($date) ? date('Y-m-d',strtotime($date)) : date('Y-m-d');
        return (new LaporanStockInventoryFn(['extParam'=>[$date]]))->find();
    }

    public function attributeLabels()
    {
        return 
        [
            'ruangan_nama' => 'Nama Ruangan',
            'obatalkes_kode' => 'Kode Obat Alkes',
            'obatalkes_nama' => 'Nama Obat Alkes',
            'satuan_besar' => 'Satuan Besar',
            'satuan_kecil' => 'Satuan Kecil',
            'jenis_obat' => 'Jenis Obat Alkes',
            'is_generik' => 'Generik',
            'is_oral' => 'Oral',
            'stok' => 'Stok',
            'stok_fisik' => 'Stok Fisik',
            'qty_satuankecil' => 'Stok Satuan Kecil',
            'qty_satuanbesar' => 'Stok Satuan Besar',
            'harga_konversi' => 'Harga Konversi',
            'harga_netto' => 'Harga Netto',
            'harga' => 'Harga',
            'total_harga' => 'Total Harga',
            'nilai_konv' => 'Nilai Konversi',
            'baseprice_kecil' => 'Base Price Satuan Kecil',
            'baseprice_besar' => 'Base Price Satuan Besar',
            'total_satuankecil' => 'Total (Rp.) Weighted Average Satuan Kecil',
            'total_satuanbesar' => 'Total (Rp.) Weighted Average Satuan Besar',
            'total_satuankecil_netto' => 'Total (Rp.) Base Price Satuan Kecil',
            'total_satuanbesar_netto' => 'Total (Rp.) Base Price Satuan Besar',
        ];
            
    }

    public function excelColumns()
    {
        return [
            ['name' => 'ruangan_nama'],
            ['name' => 'obatalkes_kode'],
            ['name' => 'obatalkes_nama'],
            ['name' => 'jenis_obat'],
            ['name' => 'is_generik'],
            ['name' => 'is_oral'],
            ['name' => 'satuan_kecil'],
            ['name' => 'satuan_besar'],
            ['name' => 'nilai_konv', 'type' => 'number'],
            ['name' => 'qty_satuankecil', 'type' => 'number'],
            ['name' => 'qty_satuanbesar', 'type' => 'number'],
            ['name' => 'baseprice_kecil', 'type' => 'number'],
            ['name' => 'baseprice_besar', 'type' => 'number'],
            ['name' => 'total_satuankecil', 'type' => 'number'],
            ['name' => 'total_satuanbesar', 'type' => 'number'],
            ['name' => 'total_satuankecil_netto', 'type' => 'number'],
            ['name' => 'total_satuanbesar_netto', 'type' => 'number'],
        ];
    }

    public function toExcel($data)
    {   
        $cols = $this->excelColumns();
        $labels = $this->attributeLabels();
        $result = [];

        foreach($data->asArray()->all() as $key => $value)
        {
            $newValue = [];

            foreach($cols as $col)
            {
                $name = $col['name'];
                $type = isset($col['type']) ? $col['type'] : '-';
                $rowData = isset($value[$name]) ? $value[$name] : '-';
                
                if($type == 'date' && $rowData != '-') {
                    $rowData = date('d M Y', strtotime($rowData));
                } elseif ($type == 'number' && $rowData != '-') {
                    $rowData = number_format($rowData, 2);
                }
            

                $newValue[\Yii::t('app', $labels[$name])] = $rowData;
            }
            $result[$key] = $newValue;
        }
        return $result;
    }
}
