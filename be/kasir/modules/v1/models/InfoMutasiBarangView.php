<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infomutasibarang_v".
 *
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
 * @property int $status_mutasi
 * @property string $statusmutasi
 */
class InfoMutasiBarangView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infomutasibarang_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mutasibarang_id', 'instalasi_tujuan_id', 'ruangan_tujuan_id', 'instalasi_asal_id', 'ruangan_asal_id', 'status_mutasi'], 'default', 'value' => null],
            [['mutasibarang_id', 'instalasi_tujuan_id', 'ruangan_tujuan_id', 'instalasi_asal_id', 'ruangan_asal_id', 'status_mutasi'], 'integer'],
            [['tgl_mutasibarang'], 'safe'],
            [['nomutasi_barang', 'instalasi_nama', 'ruangan_nama', 'instalasi_asal', 'ruangan_asal'], 'string', 'max' => 50],
            [['statusmutasi'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
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
            'status_mutasi' => Yii::t('app', 'Status mutasi'),
            'statusmutasi' => Yii::t('app', 'Status mutasi'),
        ];
    }
}
