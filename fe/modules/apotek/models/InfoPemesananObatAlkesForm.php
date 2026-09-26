<?php

namespace app\modules\apotek\models;

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
class InfoPemesananObatAlkesForm extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */

    public $pesanobatalkes_id;
    public $tglpemesanan;
    public $ruangan_id;
    public $ruangan_tujuan;
    public $instalasi_id;
    public $instalasi_tujuan;
    public $nopemesanan;
    public $ruanganpemesan_id;
    public $ruangan_pemesan_id;
    public $ruangan_pemesan;
    public $instalasi_pemesan_id;
    public $instalasi_pemesan;
    public $status_pengiriman;

    public $tanggal_dikirim;
    public $pegawai_id;

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
            'status_pengiriman' => Yii::t('fe','Status Pengiriman'),
        ];
    }
}
