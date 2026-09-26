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
            'pesanbarang_id' => Yii::t('app', 'Pesan barang'),
            'tgl_pesanbarang' => Yii::t('app', 'Tanggal pesan barang'),
            'instalasipemesan_id' => Yii::t('app', 'Instalasi pemesan'),
            'instalasi_pemesan' => Yii::t('app', 'Instalasi pemesan'),
            'ruanganpemesan_id' => Yii::t('app', 'Ruangan pemesan'),
            'ruangan_pemesan' => Yii::t('app', 'Ruangan pemesan'),
            'instalasi_id' => Yii::t('app', 'Instalasi'),
            'instalasi_tujuan' => Yii::t('app', 'Instalasi Tujuan'),
            'ruangan_id' => Yii::t('app', 'Ruangan'),
            'ruangan_tujuan' => Yii::t('app', 'Ruangan Tujuan'),
            'no_pemesanan' => Yii::t('app', 'No pemesanan'),
            'status_pesan' => Yii::t('app', 'Status pesan'),
            'tgl_mintadikirim' => 'Tanggal Minta Dikirim',
            'keterangan_pesan' => 'Keterangan Pesan',
        ];
    }
}
