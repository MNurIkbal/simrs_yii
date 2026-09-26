<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporanpasiensudahbayar_v".
 *
 * @property int $pendaftaran_id
 * @property int $pembayaranpelayanan_id
 * @property string $tgl_pembayaran
 * @property string $no_pembayaran
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property string $no_pendaftaran
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property int $carabayar_id
 * @property string $carabayar_nama
 * @property int $penjamin_id
 * @property string $penjamin_nama
 * @property string $status_bayar
 * @property int $instalasi_id
 * @property string $instalasi_nama
 * @property int $closingkasir_id
 * @property double $total_tagihan
 * @property double $subsidi_asuransi
 * @property double $total_uang_muka
 * @property double $total_sudah_dibayarkan
 * @property double $total_sisa_tagihan
 * @property double $total_pembayaran
 */
class LaporanPasienSudahBayarView extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'laporanpasiensudahbayar_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pembayaranpelayanan_id', 'ruangan_id', 'carabayar_id', 'penjamin_id', 'instalasi_id', 'closingkasir_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'pembayaranpelayanan_id', 'ruangan_id', 'carabayar_id', 'penjamin_id', 'instalasi_id', 'closingkasir_id'], 'integer'],
            [['tgl_pembayaran'], 'safe'],
            [['total_tagihan', 'total_uang_muka', 'total_sudah_dibayarkan', 'total_pembayaran'], 'number'],
            [['no_pembayaran', 'ruangan_nama', 'nama_pasien', 'carabayar_nama', 'penjamin_nama', 'instalasi_nama'], 'string', 'max' => 50],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['no_rekam_medik'], 'string', 'max' => 10],
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
            'pembayaranpelayanan_id' => 'Pembayaranpelayanan ID',
            'tgl_pembayaran' => 'Tgl Pembayaran',
            'no_pembayaran' => 'No Pembayaran',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'no_pendaftaran' => 'No Pendaftaran',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'carabayar_id' => 'Carabayar ID',
            'carabayar_nama' => 'Carabayar Nama',
            'penjamin_id' => 'Penjamin ID',
            'penjamin_nama' => 'Penjamin Nama',
            'status_bayar' => 'Status Bayar',
            'instalasi_id' => 'Instalasi ID',
            'instalasi_nama' => 'Instalasi Nama',
            'closingkasir_id' => 'Closingkasir ID',
            'total_tagihan' => 'Total Tagihan',
            'total_uang_muka' => 'Total Uang Muka',
            'total_sudah_dibayarkan' => 'Total Sudah Dibayarkan',
            'total_pembayaran' => 'Total Pembayaran'
        ];
    }
}
