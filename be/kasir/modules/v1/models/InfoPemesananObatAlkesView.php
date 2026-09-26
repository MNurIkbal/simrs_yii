<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopemesananobatalkes_v".
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
 * @property string $status_pengiriman
 */
class InfoPemesananObatAlkesView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infopemesananobatalkes_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pesanobatalkes_id', 'ruangan_id', 'instalasi_id', 'ruanganpemesan_id', 'ruangan_pemesan_id', 'instalasi_pemesan_id'], 'default', 'value' => null],
            [['pesanobatalkes_id', 'ruangan_id', 'instalasi_id', 'ruanganpemesan_id', 'ruangan_pemesan_id', 'instalasi_pemesan_id'], 'integer'],
            [['tglpemesanan'], 'safe'],
            [['nopemesanan'], 'string'],
            [['ruangan_tujuan', 'instalasi_tujuan', 'ruangan_pemesan', 'instalasi_pemesan'], 'string', 'max' => 50],
            [['status_pengiriman'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pesanobatalkes_id' => Yii::t('app', 'Pesanobatalkes ID'),
            'tglpemesanan' => Yii::t('app', 'Tglpemesanan'),
            'ruangan_id' => Yii::t('app', 'Ruangan ID'),
            'ruangan_tujuan' => Yii::t('app', 'Ruangan Tujuan'),
            'instalasi_id' => Yii::t('app', 'Instalasi ID'),
            'instalasi_tujuan' => Yii::t('app', 'Instalasi Tujuan'),
            'nopemesanan' => Yii::t('app', 'Nopemesanan'),
            'ruanganpemesan_id' => Yii::t('app', 'Ruanganpemesan ID'),
            'ruangan_pemesan_id' => Yii::t('app', 'Ruangan Pemesan ID'),
            'ruangan_pemesan' => Yii::t('app', 'Ruangan Pemesan'),
            'instalasi_pemesan_id' => Yii::t('app', 'Instalasi Pemesan ID'),
            'instalasi_pemesan' => Yii::t('app', 'Instalasi Pemesan'),
            'status_pengiriman' => Yii::t('app', 'Status pengiriman'),
        ];
    }
}
