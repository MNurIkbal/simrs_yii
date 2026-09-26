<?php

namespace app\modules\kasir\models;

use Yii;

/**
 * This is the model class for table "inforeturtagihanpasien_v".
 *
 * @property int $returbayarpelayanan_id
 * @property string $tgl_returpelayanan
 * @property int $pendaftaran_id
 * @property string $no_pendaftaran
 * @property int $pasien_id
 * @property string $nama_pasien
 * @property string $no_rekam_medik
 * @property int $penjamin_id
 * @property string $penjamin_nama
 * @property int $carabayar_id
 * @property string $carabayar_nama
 * @property double $total_biayaretur
 * @property int $tandabuktikeluar_id
 */
class InfoReturTagihanPasienForm extends \yii\base\Model
{
    
    public $returbayarpelayanan_id;
    public $tgl_returpelayanan;
    public $pendaftaran_id;
    public $no_pendaftaran;
    public $pasien_id;
    public $nama_pasien;
    public $no_rekam_medik;
    public $penjamin_id;
    public $penjamin_nama;
    public $carabayar_id;
    public $carabayar_nama;
    public $total_biayaretur;
    public $tandabuktikeluar_id;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'inforeturtagihanpasien_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['returbayarpelayanan_id', 'pendaftaran_id', 'pasien_id', 'penjamin_id', 'carabayar_id', 'tandabuktikeluar_id'], 'default', 'value' => null],
            [['returbayarpelayanan_id', 'pendaftaran_id', 'pasien_id', 'penjamin_id', 'carabayar_id', 'tandabuktikeluar_id'], 'integer'],
            [['tgl_returpelayanan'], 'safe'],
            [['total_biayaretur'], 'number'],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['nama_pasien', 'penjamin_nama', 'carabayar_nama'], 'string', 'max' => 50],
            [['no_rekam_medik'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'returbayarpelayanan_id' => Yii::t('fe', 'Returbayar pelayanan'),
            'tgl_returpelayanan' => Yii::t('fe', 'Tanggal retur pelayanan'),
            'pendaftaran_id' => Yii::t('fe', 'Pendaftaran'),
            'no_pendaftaran' => Yii::t('fe', 'No pendaftaran'),
            'pasien_id' => Yii::t('fe', 'Pasien'),
            'nama_pasien' => Yii::t('fe', 'Nama pasien'),
            'no_rekam_medik' => Yii::t('fe', 'No rekam medik'),
            'penjamin_id' => Yii::t('fe', 'Penjamin'),
            'penjamin_nama' => Yii::t('fe', 'Nama penjamin'),
            'carabayar_id' => Yii::t('fe', 'Cara bayar'),
            'carabayar_nama' => Yii::t('fe', 'Nama cara bayar'),
            'total_biayaretur' => Yii::t('fe', 'Total biaya retur'),
            'tandabuktikeluar_id' => Yii::t('fe', 'Tanda bukti keluar'),
        ];
    }
}
