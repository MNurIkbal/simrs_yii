<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopemesananbarang_v".
 *
 * @property int $pesanbarang_id
 * @property string $tgl_pesanbarang
 * @property int $instalasipemesan_id
 * @property string $instalasi_pemesan
 * @property int $ruanganpemesan_id
 * @property string $ruangan_pemesan
 * @property int $instalasi_id
 * @property string $instalasi_tujuan
 * @property int $ruangan_id
 * @property string $ruangan_tujuan
 * @property string $no_pemesanan
 * @property string $status_pesan
 */
class InfoPemesananBarangView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infopemesananbarang_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pesanbarang_id', 'instalasipemesan_id', 'ruanganpemesan_id', 'instalasi_id', 'ruangan_id'], 'default', 'value' => null],
            [['pesanbarang_id', 'instalasipemesan_id', 'ruanganpemesan_id', 'instalasi_id', 'ruangan_id'], 'integer'],
            [['tgl_pesanbarang'], 'safe'],
            [['instalasi_pemesan', 'ruangan_pemesan', 'instalasi_tujuan', 'ruangan_tujuan', 'no_pemesanan'], 'string', 'max' => 50],
            [['status_pesan'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pesanbarang_id' => 'Pesanbarang ID',
            'tgl_pesanbarang' => 'Tgl Pesanbarang',
            'instalasipemesan_id' => 'Instalasipemesan ID',
            'instalasi_pemesan' => 'Instalasi Pemesan',
            'ruanganpemesan_id' => 'Ruanganpemesan ID',
            'ruangan_pemesan' => 'Ruangan Pemesan',
            'instalasi_id' => 'Instalasi ID',
            'instalasi_tujuan' => 'Instalasi Tujuan',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_tujuan' => 'Ruangan Tujuan',
            'no_pemesanan' => 'No Pemesanan',
            'status_pesan' => 'Status Pesan',
        ];
    }
}
