<?php

/**
 * @Author: Naufal Ziyad L
 * @Date:   2018-01-29 11:13
*/


namespace app\modules\v1\models;

use Yii;


/**
 * This is the model class for table "bookingkamar_t".
 *
 * @property int $bookingkamar_id
 * @property int $ruangan_id
 * @property int $pasien_id
 * @property int $pasienadmisi_id
 * @property int $pendaftaran_id
 * @property int $kamarruangan_id
 * @property int $kelaspelayanan_id
 * @property string $bookingkamar_no
 * @property string $tgl_transaksibooking
 * @property string $tgl_bookingkamar
 * @property string $status_booking
 * @property string $keterangan_booking
 * @property string $status_konfirmasi
 * @property string $tgl_akhirkonfirmasi
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 *
 * @property KamarruanganM $kamarruangan
 * @property KelaspelayananM $kelaspelayanan
 * @property PasienM $pasien
 * @property PasienadmisiT $pasienadmisi
 * @property PendaftaranT $pendaftaran
 * @property RuanganM $ruangan
 * @property MasukkamarT[] $masukkamarTs
 * @property PasienadmisiT[] $pasienadmisiTs
 */


class TraPemesananKamar extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
          return 'bookingkamar_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
           [['ruangan_id', 'bookingkamar_no', 'tgl_transaksibooking', 'tgl_bookingkamar', 'status_booking'], 'required'],
            [['ruangan_id', 'pasien_id', 'pasienadmisi_id', 'pendaftaran_id', 'kamarruangan_id', 'kelaspelayanan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['ruangan_id', 'pasien_id', 'pasienadmisi_id', 'pendaftaran_id', 'kamarruangan_id', 'kelaspelayanan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_transaksibooking', 'tgl_bookingkamar', 'tgl_akhirkonfirmasi', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['keterangan_booking', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['bookingkamar_no'], 'string', 'max' => 20],
            [['status_booking'], 'string', 'max' => 10],
            [['status_konfirmasi'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'bookingkamar_id' => 'Bookingkamar ID',
            'ruangan_id' => 'Ruangan ID',
            'pasien_id' => 'Pasien ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'kamarruangan_id' => 'Kamarruangan ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'bookingkamar_no' => 'Bookingkamar No',
            'tgl_transaksibooking' => 'Tgl Transaksibooking',
            'tgl_bookingkamar' => 'Tgl Bookingkamar',
            'status_booking' => 'Status Booking',
            'keterangan_booking' => 'Keterangan Booking',
            'status_konfirmasi' => 'Status Konfirmasi',
            'tgl_akhirkonfirmasi' => 'Tgl Akhirkonfirmasi',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
        ];
    }
}
