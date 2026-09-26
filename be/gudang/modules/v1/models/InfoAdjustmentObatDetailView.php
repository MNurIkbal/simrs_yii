<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for view "infoadjusmenobatdetail_v".
 *
 * @property int $jenis
 * @property int $adjusmenobat_id
 * @property string $no_adjusmen
 * @property string $detail_id
 * @property string $obatalkes_id
 * @property string $obatalkes_nama
 * @property string $qty_input
 * @property string $qty_konversi
 * @property string $tgl_kadaluarsa
 * @property string $harga_netto
 * @property string $no_batch
 * @property string $keterangan
 * @property string $alasan
 */
class InfoAdjustmentObatDetailView extends \Doco\components\DocoActiveRecord {
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infoadjusmenobatdetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jenis'             => 'Jenis Adjustment',
            'adjusmenobat_id'   => 'ID Adjustment Obat',
            'no_adjusmen'       => 'No. Adjustment',
            'detail_id'         => 'ID Detail',
            'obatalkes_id'      => 'ID Obat Alkes',
            'obatalkes_nama'    => 'Nama Obat Alkes',
            'obatalkes_kode'    => 'Kode Obat Alkes',
            'qty_input'         => 'Qty Input',
            'qty_konversi'      => 'Qty Konversi',
            'tgl_kadaluarsa'    => 'Tanggal Kadaluarsa',
            'harga_netto'       => 'Harga Netto',
            'no_batch'          => 'No Batch',
            'keterangan'        => 'Keterangan',
            'alasan'            => 'Alasan'
        ];
    }
}
