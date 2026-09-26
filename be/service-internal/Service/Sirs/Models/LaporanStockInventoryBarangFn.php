<?php
namespace Integrasi\Service\Sirs\Models;

use Yii;

class LaporanStockInventoryBarangFn extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName()
    {
        return "laporanstockinventorybarang_fn";
    }

    public static function attributSchema()
    {
        return [
            'integer' => [
                'ruanganid',
                'instalasi_id',
            ],
            'date' => [
                'tanggal_inventory'
            ],
            'varchar' => [
                'ruangan_nama',
                'barang_kode',
                'barang_nama',
                'kelompok_barang',
                'satuan_besar',
                'satuan_kecil',
                'instalasi_nama',
                'subkelompok_nama'
            ],
            'float8'  => [
                'harga_konversi',
                'harga_netto',
                'harga',
                'stok',
                'stok_fisik',
                'total_harga',
                'nilai_konv',
                'baseprice_kecil',
                'baseprice_besar',
                'total_satuankecil',
                'total_satuanbesar',
                'qty_satuankecil',
                'qty_satuanbesar'
            ]
        ];
    }

    public static function getData($date = null)
    {
        $date = !empty($date) ? date('Y-m-d',strtotime($date)) : date('Y-m-d');
        return (new LaporanStockInventoryBarangFn(['extParam'=>[$date]]))->find();
    }

    public function attributeLabels()
    {
        return 
        [
            'tanggal_inventory' => 'Tanggal Inventory',
            'instalasi_nama' => 'Instalasi',
            'ruangan_nama' => 'Nama Ruangan',
            'barang_kode' => 'Kode Barang',
            'barang_nama' => 'Nama Barang',
            'kelompok_barang' => 'Kelompok Barang',
            'subkelompok_nama' => 'Sub Kelompok',
            'satuan_kecil' => 'Satuan Kecil',
            'satuan_besar' => 'Satuan Besar',
            'nilai_konv' => 'Nilai Konversi',
            'qty_satuankecil' => 'Stok Satuan Kecil',
            'qty_satuanbesar' => 'Stok Satuan Besar',
            'baseprice_kecil' => 'Base Price Satuan Kecil',
            'baseprice_besar' => 'Base Price Satuan Besar',
            'stok_fisik' => 'Stok Fisik',
            'stok' => 'Stok',
            'harga_konversi' => 'Harga Konversi',
            'harga_netto' => 'Harga Netto',
            'harga' => 'Harga',
            'total_satuankecil' => 'Total (Rp.) Satuan Kecil',
            'total_satuanbesar' => 'Total (Rp.) Satuan Besar',
            'total_harga' => 'Total Harga',
            'ruanganid' => 'Id ruangan',

        ];     
    }

    public function excelColumns()
    {
        return [
            ['name' => 'instalasi_nama'],
            ['name' => 'ruangan_nama'],
            ['name' => 'barang_kode'],
            ['name' => 'barang_nama'],
            ['name' => 'kelompok_barang'],
            ['name' => 'subkelompok_nama'],
            ['name' => 'stok_fisik'],
            ['name' => 'stok'],
            ['name' => 'satuan_kecil'],
            ['name' => 'satuan_besar'],
            ['name' => 'nilai_konv'],
            ['name' => 'qty_satuankecil'],
            ['name' => 'qty_satuanbesar'],
            ['name' => 'baseprice_kecil'],
            ['name' => 'baseprice_besar'],
            ['name' => 'total_satuankecil'],
            ['name' => 'total_satuanbesar'],
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
