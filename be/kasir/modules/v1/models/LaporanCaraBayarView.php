<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporancarabayar_v".
 *
 * @property integer $pembayaranpelayanan_id
 * @property string $tgl_pembayaran
 * @property string $no_pembayaran
 * @property integer $pendaftaran_id
 * @property string $no_pendaftaran
 * @property integer $pasien_id
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property integer $carabayar_id
 * @property string $carabayar_nama
 * @property integer $penjamin_id
 * @property string $penjamin_nama
 */
class LaporanCaraBayarView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'laporancarabayar_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pembayaranpelayanan_id', 'pendaftaran_id', 'pasien_id', 'carabayar_id', 'penjamin_id'], 'integer'],
            [['tgl_pembayaran'], 'safe'],
            [['no_pembayaran', 'nama_pasien', 'carabayar_nama', 'penjamin_nama'], 'string', 'max' => 50],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['no_rekam_medik'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pembayaranpelayanan_id' => Yii::t('app', 'Pembayaran pelayanan'),
            'tgl_pembayaran' => Yii::t('app', 'Tanggal pembayaran'),
            'no_pembayaran' => Yii::t('app', 'No pembayaran'),
            'pendaftaran_id' => Yii::t('app', 'Pendaftaran'),
            'no_pendaftaran' => Yii::t('app', 'No pendaftaran'),
            'pasien_id' => Yii::t('app', 'Pasien'),
            'no_rekam_medik' => Yii::t('app', 'No rekam medik'),
            'nama_pasien' => Yii::t('app', 'Nama pasien'),
            'carabayar_id' => Yii::t('app', 'Cara bayar'),
            'carabayar_nama' => Yii::t('app', 'Nama cara bayar'),
            'penjamin_id' => Yii::t('app', 'Penjamin'),
            'penjamin_nama' => Yii::t('app', 'Nama penjamin'),
        ];
    }
}
