<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "detailmutasibarang_v".
 *
 * @property int $mutasibarangdetail_id
 * @property int $mutasibarang_id
 * @property string $nomutasi_barang
 * @property string $tgl_mutasibarang
 * @property int $instalasi_tujuan_id
 * @property string $instalasi_nama
 * @property int $ruangan_tujuan_id
 * @property string $ruangan_nama
 * @property int $instalasi_asal_id
 * @property string $instalasi_asal
 * @property int $ruangan_asal_id
 * @property string $ruangan_asal
 * @property double $qty_mutasi
 * @property int $barang_id
 * @property string $barang_nama
 * @property string $satuanbrg
 * @property string $lookup_value
 */
class DetailMutasiBarangView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'detailmutasibarang_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mutasibarangdetail_id', 'mutasibarang_id', 'instalasi_tujuan_id', 'ruangan_tujuan_id', 'instalasi_asal_id', 'ruangan_asal_id', 'barang_id'], 'default', 'value' => null],
            [['mutasibarangdetail_id', 'mutasibarang_id', 'instalasi_tujuan_id', 'ruangan_tujuan_id', 'instalasi_asal_id', 'ruangan_asal_id', 'barang_id'], 'integer'],
            [['tgl_mutasibarang'], 'safe'],
            [['qty_mutasi'], 'number'],
            [['nomutasi_barang', 'instalasi_nama', 'ruangan_nama', 'instalasi_asal', 'ruangan_asal', 'satuanbrg'], 'string', 'max' => 50],
            [['barang_nama'], 'string', 'max' => 100],
            [['lookup_value'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mutasibarangdetail_id' => Yii::t('app', 'Mutasi barang detail'),
            'mutasibarang_id' => Yii::t('app', 'Mutasi barang'),
            'nomutasi_barang' => Yii::t('app', 'No mutasi barang'),
            'tgl_mutasibarang' => Yii::t('app', 'Tanggal mutasi barang'),
            'instalasi_tujuan_id' => Yii::t('app', 'Instalasi tujuan'),
            'instalasi_nama' => Yii::t('app', 'Nama instalasi'),
            'ruangan_tujuan_id' => Yii::t('app', 'Ruangan tujuan'),
            'ruangan_nama' => Yii::t('app', 'Nama ruangan'),
            'instalasi_asal_id' => Yii::t('app', 'Instalasi asal'),
            'instalasi_asal' => Yii::t('app', 'Instalasi asal'),
            'ruangan_asal_id' => Yii::t('app', 'Ruangan asal'),
            'ruangan_asal' => Yii::t('app', 'Ruangan asal'),
            'qty_mutasi' => Yii::t('app', 'Qty mutasi'),
            'barang_id' => Yii::t('app', 'Barang'),
            'barang_nama' => Yii::t('app', 'Barang nama'),
            'satuanbrg' => Yii::t('app', 'Satuan barang'),
            'lookup_value' => Yii::t('app', 'Lookup value'),
        ];
    }
}
