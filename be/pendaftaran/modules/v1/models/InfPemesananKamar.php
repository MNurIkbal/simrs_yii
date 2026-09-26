<?php

namespace app\modules\v1\models;

use Yii;



/**
 * This is the model class for table "infopemesanankamar_v".
 *
 * @property int $bookingkamar_id
 * @property string $tgl_transaksi
 * @property string $tgl_pesan
 * @property string $no_pemesanan
 * @property int $pasien_id
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property string $no_telepon_pasien
 * @property string $no_mobile_pasien
 * @property int $kamarruangan_id
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property string $kamarruangan_nokamar
 * @property int $kelaspelayanan_id
 * @property string $kelaspelayanan_nama
 * @property string $status_booking
 * @property int $pendaftaran_id
 */

class InfPemesananKamar extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infopemesanankamar_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['bookingkamar_id', 'pasien_id', 'kamarruangan_id', 'ruangan_id', 'kelaspelayanan_id', 'pendaftaran_id'], 'default', 'value' => null],
            [['bookingkamar_id', 'pasien_id', 'kamarruangan_id', 'ruangan_id', 'kelaspelayanan_id', 'pendaftaran_id'], 'integer'],
            [['tgl_transaksi', 'tgl_pesan'], 'safe'],
            [['no_pemesanan', 'no_mobile_pasien'], 'string', 'max' => 20],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['nama_pasien', 'ruangan_nama', 'kelaspelayanan_nama'], 'string', 'max' => 50],
            [['no_telepon_pasien'], 'string', 'max' => 15],
            [['kamarruangan_nokamar'], 'string', 'max' => 25],
            [['status_booking'], 'string', 'max' => 200],
            [['kamarruangan_id'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'bookingkamar_id' => 'Bookingkamar ID',
            'tgl_transaksi' => 'Tgl Transaksi',
            'tgl_pesan' => 'Tgl Pesan',
            'no_pemesanan' => 'No Pemesanan',
            'pasien_id' => 'Pasien ID',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'no_telepon_pasien' => 'No Telepon Pasien',
            'no_mobile_pasien' => 'No Mobile Pasien',
            'kamarruangan_id' => 'Kamarruangan ID',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'kamarruangan_nokamar' => 'Kamarruangan Nokamar',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'status_booking' => 'Status Booking',
            'pendaftaran_id' => 'Pendaftaran ID',
        ];
    }
}
