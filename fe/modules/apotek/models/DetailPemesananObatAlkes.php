<?php

namespace app\modules\apotek\models;

use Yii;

/**
 * This is the model class for table "detailpemesananobatalkes_v".
 *
 * @property int $pesanobatalkes_id
 * @property string $tglpemesanan
 * @property int $ruangan_id
 * @property string $ruangan_tujuan
 * @property int $instalasi_id
 * @property string $instalasi_tujuan
 * @property string $nopemesanan
 * @property int $ruanganpemesan_id
 * @property int $ruangan_pemesan_id
 * @property string $ruangan_pemesan
 * @property int $instalasi_pemesan_id
 * @property string $instalasi_pemesan
 * @property int $obatalkes_id
 * @property string $obatalkes_namalain
 * @property double $jumlah_pesan
 * @property string $tglmintadikirim
 */
class DetailPemesananObatAlkes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'detailpemesananobatalkes_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pesanobatalkes_id', 'ruangan_id', 'instalasi_id', 'ruanganpemesan_id', 'ruangan_pemesan_id', 'instalasi_pemesan_id', 'obatalkes_id'], 'default', 'value' => null],
            [['pesanobatalkes_id', 'ruangan_id', 'instalasi_id', 'ruanganpemesan_id', 'ruangan_pemesan_id', 'instalasi_pemesan_id', 'obatalkes_id'], 'integer'],
            [['tglpemesanan', 'tglmintadikirim'], 'safe'],
            [['nopemesanan', 'obatalkes_namalain'], 'string'],
            [['jumlah_pesan'], 'number'],
            [['ruangan_tujuan', 'instalasi_tujuan', 'ruangan_pemesan', 'instalasi_pemesan'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pesanobatalkes_id' => Yii::t('fe','Pesanobatalkes ID'),
            'tglpemesanan' => Yii::t('fe','Tglpemesanan'),
            'ruangan_id' => Yii::t('fe','Ruangan ID'),
            'ruangan_tujuan' => Yii::t('fe','Ruangan Tujuan'),
            'instalasi_id' => Yii::t('fe','Instalasi ID'),
            'instalasi_tujuan' => Yii::t('fe','Instalasi Tujuan'),
            'nopemesanan' => Yii::t('fe','Nopemesanan'),
            'ruanganpemesan_id' => Yii::t('fe','Ruanganpemesan ID'),
            'ruangan_pemesan_id' => Yii::t('fe','Ruangan Pemesan ID'),
            'ruangan_pemesan' => Yii::t('fe','Ruangan Pemesan'),
            'instalasi_pemesan_id' => Yii::t('fe','Instalasi Pemesan ID'),
            'instalasi_pemesan' => Yii::t('fe','Instalasi Pemesan'),
            'obatalkes_id' => Yii::t('fe','Obatalkes ID'),
            'obatalkes_namalain' => Yii::t('fe','Obatalkes Namalain'),
            'jumlah_pesan' => Yii::t('fe','Jumlah Pesan'),
            'tglmintadikirim' => Yii::t('fe','Tglmintadikirim'),
        ];
    }
}
