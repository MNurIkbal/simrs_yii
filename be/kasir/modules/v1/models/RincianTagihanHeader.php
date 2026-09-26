<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rinciantagihansudahbayarheader_v".
 *
 * @property int $pendaftaran_id
 * @property string $no_pendaftaran
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property string $jeniskasuspenyakit_nama
 * @property string $dok_pendaftaran
 * @property string $dok_ranap
 * @property string $r_pendaftaran
 * @property string $r_ranap
 * @property string $carabayar_nama
 * @property string $penjamin_nama
 * @property double $total_tagihan
 * @property double $total_uang_muka
 * @property double $total_sudah_dibayarkan
 * @property double $total_sisatagihan
 * @property string $status_bayar
 * @property string $tgl_pendaftaran
 * @property int $pembayaranpelayanan_id
 * @property string $kelaspelayanan_nama
 * @property int $tandabuktibayar_id
 * @property double $pembulatan
 * @property double $biaya_administrasi
 * @property string $tgl_pembayaran
 */
class RincianTagihanHeader extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rinciantagihansudahbayarheader_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pembayaranpelayanan_id', 'tandabuktibayar_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'pembayaranpelayanan_id', 'tandabuktibayar_id'], 'integer'],
            [['total_tagihan', 'total_uang_muka', 'total_sudah_dibayarkan', 'total_sisatagihan', 'pembulatan', 'biaya_administrasi'], 'number'],
            [['tgl_pendaftaran', 'tgl_pembayaran'], 'safe'],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['nama_pasien', 'dok_pendaftaran', 'dok_ranap', 'r_pendaftaran', 'r_ranap', 'carabayar_nama', 'penjamin_nama', 'kelaspelayanan_nama'], 'string', 'max' => 50],
            [['jeniskasuspenyakit_nama'], 'string', 'max' => 100],
            [['status_bayar'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'no_pendaftaran' => 'No Pendaftaran',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'jeniskasuspenyakit_nama' => 'Jeniskasuspenyakit Nama',
            'dok_pendaftaran' => 'Dok Pendaftaran',
            'dok_ranap' => 'Dok Ranap',
            'r_pendaftaran' => 'R Pendaftaran',
            'r_ranap' => 'R Ranap',
            'carabayar_nama' => 'Carabayar Nama',
            'penjamin_nama' => 'Penjamin Nama',
            'total_tagihan' => 'Total Tagihan',
            'total_uang_muka' => 'Total Uang Muka',
            'total_sudah_dibayarkan' => 'Total Sudah Dibayarkan',
            'total_sisatagihan' => 'Total Sisatagihan',
            'status_bayar' => 'Status Bayar',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'pembayaranpelayanan_id' => 'Pembayaranpelayanan ID',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'tandabuktibayar_id' => 'Tandabuktibayar ID',
            'pembulatan' => 'Pembulatan',
            'biaya_administrasi' => 'Biaya Administrasi',
            'tgl_pembayaran' => 'Tgl Pembayaran',
        ];
    }
}
