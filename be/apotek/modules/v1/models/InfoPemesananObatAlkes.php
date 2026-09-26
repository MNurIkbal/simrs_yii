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
class InfoPemesananObatAlkes extends \Doco\components\DocoActiveRecord
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
            [['nopemesanan', 'status_pengiriman'], 'string'],
            [['ruangan_tujuan', 'instalasi_tujuan', 'ruangan_pemesan', 'instalasi_pemesan'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pesanobatalkes_id' => 'Pesanobatalkes ID',
            'tglpemesanan' => 'Tglpemesanan',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_tujuan' => 'Ruangan Tujuan',
            'instalasi_id' => 'Instalasi ID',
            'instalasi_tujuan' => 'Instalasi Tujuan',
            'nopemesanan' => 'Nopemesanan',
            'ruanganpemesan_id' => 'Ruanganpemesan ID',
            'ruangan_pemesan_id' => 'Ruangan Pemesan ID',
            'ruangan_pemesan' => 'Ruangan Pemesan',
            'instalasi_pemesan_id' => 'Instalasi Pemesan ID',
            'instalasi_pemesan' => 'Instalasi Pemesan',
            'status_pengiriman' => 'Status Pengiriman',
        ];
    }

    public function getRuangan()
    {
        return $this->hasOne(Ruangan::className(), ['ruangan_id' => 'ruangan_id']);
    }

    public static function primaryKey()
    {
        return ['pesanobatalkes_id', 'pesanobatalkes_id'];
    }
}
