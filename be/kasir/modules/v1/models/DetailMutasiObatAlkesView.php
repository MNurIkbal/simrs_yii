<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "detailmutasiobatalkes_v".
 *
 * @property int $mutasiobatdetail_id
 * @property int $mutasiobatruangan_id
 * @property string $nomutasioa
 * @property string $tglmutasioa
 * @property int $instalasi_tujuan_id
 * @property string $instalasi_nama
 * @property int $ruangan_tujuan_id
 * @property string $ruangan_nama
 * @property int $instalasi_asal_id
 * @property string $instalasi_asal
 * @property int $ruangan_asal_id
 * @property string $ruangan_asal
 * @property double $jumlah_mutasi
 * @property int $obatalkes_id
 * @property string $obatalkes_namalain
 * @property int $satuankecil_id
 * @property string $satuankecil_nama
 * @property double $harga_netto
 * @property double $harga_jualsatuan
 */
class DetailMutasiObatAlkesView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'detailmutasiobatalkes_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mutasiobatdetail_id', 'mutasiobatruangan_id', 'instalasi_tujuan_id', 'ruangan_tujuan_id', 'instalasi_asal_id', 'ruangan_asal_id', 'obatalkes_id', 'satuankecil_id'], 'default', 'value' => null],
            [['mutasiobatdetail_id', 'mutasiobatruangan_id', 'instalasi_tujuan_id', 'ruangan_tujuan_id', 'instalasi_asal_id', 'ruangan_asal_id', 'obatalkes_id', 'satuankecil_id'], 'integer'],
            [['nomutasioa', 'obatalkes_namalain', 'satuankecil_nama'], 'string'],
            [['tglmutasioa'], 'safe'],
            [['jumlah_mutasi', 'harga_netto', 'harga_jualsatuan'], 'number'],
            [['instalasi_nama', 'ruangan_nama', 'instalasi_asal', 'ruangan_asal'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mutasiobatdetail_id' => Yii::t('app', 'Mutasi obat detail'),
            'mutasiobatruangan_id' => Yii::t('app', 'Mutasi obat ruangan'),
            'nomutasioa' => Yii::t('app', 'No mutasi oa'),
            'tglmutasioa' => Yii::t('app', 'Tanggal mutasi oa'),
            'instalasi_tujuan_id' => Yii::t('app', 'Instalasi tujuan'),
            'instalasi_nama' => Yii::t('app', 'Nama instalasi'),
            'ruangan_tujuan_id' => Yii::t('app', 'Ruangan tujuan'),
            'ruangan_nama' => Yii::t('app', 'Nama ruangan'),
            'instalasi_asal_id' => Yii::t('app', 'Instalasi asal'),
            'instalasi_asal' => Yii::t('app', 'Instalasi asal'),
            'ruangan_asal_id' => Yii::t('app', 'Ruangan asal'),
            'ruangan_asal' => Yii::t('app', 'Ruangan asal'),
            'jumlah_mutasi' => Yii::t('app', 'Jumlah mutasi'),
            'obatalkes_id' => Yii::t('app', 'Obat alkes'),
            'obatalkes_namalain' => Yii::t('app', 'Nama lain obat alkes'),
            'satuankecil_id' => Yii::t('app', 'Satuan kecil'),
            'satuankecil_nama' => Yii::t('app', 'Nama satuan kecil'),
            'harga_netto' => Yii::t('app', 'Harga netto'),
            'harga_jualsatuan' => Yii::t('app', 'Harga jual satuan'),
        ];
    }
}
