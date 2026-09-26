<?php
namespace Integrasi\Service\Sirs\Models;

use Yii;

class LaporanStockMutasiFn extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName()
    {
        return "laporanmutasistok_fn";
    }

    public static function attributSchema()
    {
        return [
            'integer' => [
              'obatalkes_id',
              'ruangan_id',
            ],
            'varchar' => [
                'ruangan_nama',
                'obatalkes_nama',
                'obatalkes_kode',
                'jenisobatalkes_nama',
                'manufaktur_nama',
                'uom',
            ],
            'float8'  => [
                'hna',
                'qty_total_awal',
                'total_nilai_awal',
                'qtystok_in',
                'qtystok_out',
                'total_qty_diterima',
                'total_nilai_diterima',
                'total_qty_keluar',
                'total_nilai_keluar',
                'total_qty_akhir',
                'total_nilai_akhir',
                'turn_over'
            ]
        ];
    }

    public static function getData($start,$end)
    {   
        $start_date = !empty($start) ? date('Y-m-d',strtotime($start)) : date('Y-m-d');
        $end_date = !empty($end) ? date('Y-m-d',strtotime($end)) : date('Y-m-d');

        return (new LaporanStockMutasiFn(['extParam'=>[$start_date,$end_date]]))->find();
    }

    public function attributeLabels()
    {
        return 
        [
            'obatalkes_id' => 'Obat Alkes ID',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Nama Ruangan',
            'obatalkes_nama' => 'Nama Obat Alkes',
            'obatalkes_kode' => 'Kode Obat Alkes',
            'jenisobatalkes_nama' => 'Jenis Obat',
            'manufaktur_nama' => 'Nama Manufaktur',
            'UOM' => 'UOM',
            'HNA' => 'HNA',
            'qty_total_awal' => 'Qty Total Awal',
            'total_nilai_awal' => 'Total Nilai Awal',
            'qtystok_in' => 'Qty Stok In',
            'qtystok_out' => 'Qty Stok Out',
            'total_qty_diterima' => 'Total Qty Diterima',
            'total_nilai_diterima' => 'Total Nilai Diterima',
            'total_qty_keluar' => 'Total Qty Keluar',
            'total_nilai_keluar' => 'Total Nilai Keluar',
            'total_qty_akhir' => 'Total Qty Akhir',
            'total_nilai_akhir' => 'Total Nilai Akhir',
            'turn_over' => 'Turn Over',
        ];
            
    }

    public function excelColumns()
    {
        return [
            ['name' => 'obatalkes_id'],
            ['name' => 'ruangan_id'],
            ['name' => 'ruangan_nama'],
            ['name' => 'obatalkes_nama'],
            ['name' => 'obatalkes_kode'],
            ['name' => 'jenisobatalkes_nama'],
            ['name' => 'manufaktur_nama'],
            ['name' => 'UOM'],
            ['name' => 'HNA', 'type' => 'number'],
            ['name' => 'qty_total_awal', 'type' => 'number'],
            ['name' => 'total_nilai_awal', 'type' => 'number'],
            ['name' => 'qtystok_in', 'type' => 'number'],
            ['name' => 'qtystok_out', 'type' => 'number'],
            ['name' => 'total_qty_diterima', 'type' => 'number'],
            ['name' => 'total_nilai_diterima', 'type' => 'number'],
            ['name' => 'total_qty_keluar', 'type' => 'number'],
            ['name' => 'total_nilai_keluar', 'type' => 'number'],
            ['name' => 'total_qty_akhir', 'type' => 'number'],
            ['name' => 'total_nilai_akhir', 'type' => 'number'],
            ['name' => 'turn_over', 'type' => 'number'],
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
