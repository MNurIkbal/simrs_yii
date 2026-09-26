<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporanmutasiobat_v".
 *
 * @property string $tgl_pemesanan
 * @property string $tgl_pengiriman
 * @property string $tgl_penerimaan
 * @property string $no_pemesanan
 * @property string $no_pengiriman
 * @property string $pegawai_pemesan
 * @property string $pegawai_pengirim
 * @property string $pegawai_penerima
 * @property string $ruangan_pengirim
 * @property string $ruangan_penerima
 * @property string $kode_obat
 * @property string $nama_obat
 * @property float $qty_pesan
 * @property float $qty_kirim
 * @property float $qty_terima
 * @property float $harga_satuan
 * @property float $harga_total
 * @property string $uom_input
 * @property string $uom_konversi
 * @property string $catatan_pemesan
 * @property string $catatan_pengirim
 * @property string $catatan_penerima
 */
class LaporanMutasiObatView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'laporanmutasiobat_v';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
        	'tgl_pemesanan' => 'Tanggal Pemesanan',
            'no_pemesanan' => 'Nomor Pemesanan',
            'pegawai_pemesan' => 'User Pemesanan',
            'kode_obat' => 'Kode Obat',
            'nama_obat' => 'Nama Obat',
            'qty_input_pesan' => 'Qty Pesan',
            'uom_input' => 'UoM Pesan',
            'qty_input_terima' => 'Qty Terima',
            'uom_input_terima' => 'UoM Terima',
            'tgl_pengiriman' => 'Tanggal Pengiriman',
            'no_pengiriman' => 'Nomor Pengiriman',
            'ruangan_pengirim' => 'Ruangan Pengirim',
            'pegawai_pengirim' => 'User Pengirim',
            'tgl_penerimaan' => 'Tanggal Penerimaan',
            'no_penerimaan' => 'Nomor Penerimaan',
            'ruangan_penerima' => 'Ruangan Penerima',
            'pegawai_penerima' => 'User Penerima',
            'catatan_penerima' => 'Catatan Penerima',
            'harga_satuan' => 'Harga Satuan',
            'harga_netto_konversi' => 'Harga Satuan',
            'harga_total' => 'Total Harga (Rp)',
        ];
    }

    public function excelColumns()
    {
    	return [
            ['name' => 'tgl_pemesanan', 'type' => 'date'],
            ['name' => 'no_pemesanan'],
            ['name' => 'pegawai_pemesan'],
            ['name' => 'kode_obat'],
            ['name' => 'nama_obat'],
            ['name' => 'qty_input_pesan'],
            ['name' => 'uom_input'],
            ['name' => 'qty_input_terima'],
            ['name' => 'uom_input_terima'],
            ['name' => 'tgl_pengiriman', 'type' => 'date'],
            ['name' => 'no_pengiriman'],
            ['name' => 'ruangan_pengirim'],
            ['name' => 'pegawai_pengirim'],
            ['name' => 'tgl_penerimaan', 'type' => 'date'],
            ['name' => 'no_penerimaan'],
            ['name' => 'ruangan_penerima'],
            ['name' => 'pegawai_penerima'],
            ['name' => 'catatan_penerima'],
            ['name' => 'harga_netto_konversi', 'type' => 'number'],
            ['name' => 'harga_total', 'type' => 'number']
        ];
    }

    public function toExcel($data)
    {
    	$cols = $this->excelColumns();
    	$labels = $this->attributeLabels();
    	$result = [];

        foreach ($data->asArray()->all() as $key => $value) {
            $newValue = [];

            foreach ($cols as $col) {
                $name = $col['name'];
                $type = isset($col['type']) ? $col['type'] : '-';

                $rowData = isset($value[$name]) ? $value[$name] : '-';
                
                if($type == 'date' && $rowData != '-') {
                    $rowData = date('d/m/Y', strtotime($rowData));
                } elseif($type == 'number' && $rowData != '-') {
                    $rowData = number_format($rowData, 2);
                }

                $newValue[\Yii::t('app', $labels[$name])] = $rowData;
            }

            $result[$key] = $newValue;
        }

        return $result;
    }
}
