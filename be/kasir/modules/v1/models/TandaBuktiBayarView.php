<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "tandabuktibayar_v".
 *
 * @property int tandabuktibayar_id
 * @property string nobuktibayar
 * @property string no_pendaftaran
 * @property string no_rekam_medik
 * @property string nama_pasien
 * @property string carabayar_nama
 * @property string penjamin_nama
 * @property string kelaspelayanan_nama
 * @property string tglbuktibayar
 * @property double jmlpembayaran

 */
class TandaBuktiBayarView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tandabuktibayar_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        // return [
        //     [['returbayarpelayanan_id', 'pendaftaran_id', 'pasien_id', 'penjamin_id', 'carabayar_id', 'tandabuktikeluar_id'], 'default', 'value' => null],
        //     [['returbayarpelayanan_id', 'pendaftaran_id', 'pasien_id', 'penjamin_id', 'carabayar_id', 'tandabuktikeluar_id'], 'integer'],
        //     [['tgl_returpelayanan'], 'safe'],
        //     [['total_biayaretur'], 'number'],
        //     [['no_pendaftaran'], 'string', 'max' => 20],
        //     [['nama_pasien', 'penjamin_nama', 'carabayar_nama'], 'string', 'max' => 50],
        //     [['no_rekam_medik'], 'string', 'max' => 10],
        // ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tandabuktibayar_id' => Yii::t('app', 'ID Tanda Bukti Bayar'),
            'nobuktibayar' => Yii::t('app', 'No. Bukti Bayar'),
            'no_pendaftaran' => Yii::t('app', 'No. Pendaftaran'),
            'no_rekam_medik' => Yii::t('app', 'No. Rekam Medik'),
            'nama_pasien' => Yii::t('app', 'Nama Pasien'),
            'carabayar_nama' => Yii::t('app', 'Cara Bayar'),
            'penjamin_nama' => Yii::t('app', 'Nama Penjamin'),
            'kelaspelayanan_nama' => Yii::t('app', 'Nama Kelas Pelayanan'),
            'tglbuktibayar' => Yii::t('app', 'Tanggal Pembayaran'),
            'jmlpembayaran' => Yii::t('app', 'Jumlah Pembayaran'),
            'total_biayaretur' => Yii::t('app', 'Total Retur'),
        ];
    }
}
