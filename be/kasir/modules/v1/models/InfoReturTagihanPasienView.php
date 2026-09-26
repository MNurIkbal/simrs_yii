<?php

namespace app\modules\v1\models;

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
class InfoReturTagihanPasienView extends \Doco\components\DocoActiveRecord
{
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
            'returbayarpelayanan_id' => Yii::t('app', 'Returbayar pelayanan'),
            'tgl_returpelayanan' => Yii::t('app', 'Tanggal retur pelayanan'),
            'pendaftaran_id' => Yii::t('app', 'Pendaftaran'),
            'no_pendaftaran' => Yii::t('app', 'No pendaftaran'),
            'pasien_id' => Yii::t('app', 'Pasien'),
            'nama_pasien' => Yii::t('app', 'Nama pasien'),
            'no_rekam_medik' => Yii::t('app', 'No rekam medik'),
            'penjamin_id' => Yii::t('app', 'Penjamin'),
            'penjamin_nama' => Yii::t('app', 'Nama penjamin'),
            'carabayar_id' => Yii::t('app', 'Cara bayar'),
            'carabayar_nama' => Yii::t('app', 'Nama cara bayar'),
            'total_biayaretur' => Yii::t('app', 'Total biaya retur'),
            'tandabuktikeluar_id' => Yii::t('app', 'Tanda bukti keluar'),
        ];
    }
}
