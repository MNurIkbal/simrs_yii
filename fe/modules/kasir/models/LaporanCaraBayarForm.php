<?php

namespace app\modules\kasir\models;

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
class LaporanCaraBayarForm extends \yii\base\Model
{
    
    public $pembayaranpelayanan_id;
    public $tgl_pembayaran;
    public $no_pembayaran;
    public $pendaftaran_id;
    public $no_pendaftaran;
    public $pasien_id;
    public $no_rekam_medik;
    public $nama_pasien;
    public $carabayar_id;
    public $carabayar_nama;
    public $penjamin_id;
    public $penjamin_nama;

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
            'pembayaranpelayanan_id' => Yii::t('fe', 'Pembayaran pelayanan'),
            'tgl_pembayaran' => Yii::t('fe', 'Tanggal pembayaran'),
            'no_pembayaran' => Yii::t('fe', 'No pembayaran'),
            'pendaftaran_id' => Yii::t('fe', 'Pendaftaran'),
            'no_pendaftaran' => Yii::t('fe', 'No pendaftaran'),
            'pasien_id' => Yii::t('fe', 'Pasien'),
            'no_rekam_medik' => Yii::t('fe', 'No rekam medik'),
            'nama_pasien' => Yii::t('fe', 'Nama pasien'),
            'carabayar_id' => Yii::t('fe', 'Cara bayar'),
            'carabayar_nama' => Yii::t('fe', 'Nama cara bayar'),
            'penjamin_id' => Yii::t('fe', 'Penjamin'),
            'penjamin_nama' => Yii::t('fe', 'Nama penjamin'),
        ];
    }
}
