<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "bookingkamar_t".
 *
 * @property integer $bookingkamar_id
 * @property integer $ruangan_id
 * @property integer $pasien_id
 * @property integer $pasienadmisi_id
 * @property integer $pendaftaran_id
 * @property integer $kamarruangan_id
 * @property integer $kelaspelayanan_id
 * @property string $bookingkamar_no
 * @property string $tgl_transaksibooking
 * @property string $tgl_bookingkamar
 * @property string $status_booking
 * @property string $keterangan_booking
 * @property string $status_konfirmasi
 * @property string $tgl_akhirkonfirmasi
 * @property string $additional_data
 * @property string $created_date
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 * @property integer $kamartempattidur_id
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
class Bookingkamar extends \Doco\components\DocoActiveRecord
{
    protected $xssProtected = [
        'no_identitas_pasien',
        'nama_pasien',
        'nama_bin',
        'tempat_lahir',

        'keterangan_booking',
        'nama_pemesan',
        'alamat_pasien',
        'no_telepon_pasien'
    ];

    public $no_identitas_pasien;
    public $nama_pasien;
    public $nama_bin;
    public $tempat_lahir;
    public $alamat_pasien;
    public $no_telepon_pasien;
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
            [['ruangan_id', 'kamarruangan_id', 'kelaspelayanan_id', 'tgl_transaksibooking', 'tgl_bookingkamar', 'status_booking', 'kamartempattidur_id','jeniskasuspenyakit_id'], 'required'],
            [['ruangan_id', 'pasien_id', 'pasienadmisi_id', 'pendaftaran_id', 'kamarruangan_id', 'kelaspelayanan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'kamartempattidur_id'], 'integer'],
            [['tgl_transaksibooking','jeniskasuspenyakit_id','nama_pemesan', 'tgl_bookingkamar', 'tgl_akhirkonfirmasi', 'created_date', 'last_modified_date', 'deleted_date', 'tgl_expired', 'alamat_pasien', 'no_telepon_pasien','no_identitas_pasien','nama_pasien','nama_bin','tempat_lahir'], 'safe'],
            [['keterangan_booking', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['bookingkamar_no'], 'string', 'max' => 20],
            [['status_booking'], 'string', 'max' => 10],
            [['status_konfirmasi'], 'string', 'max' => 50],
            [['bookingkamar_no'], 'unique'],
            // [['kamarruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => KamarruanganM::className(), 'targetAttribute' => ['kamarruangan_id' => 'kamarruangan_id']],
            // [['kelaspelayanan_id'], 'exist', 'skipOnError' => true, 'targetClass' => KelaspelayananM::className(), 'targetAttribute' => ['kelaspelayanan_id' => 'kelaspelayanan_id']],
            // [['pasien_id'], 'exist', 'skipOnError' => true, 'targetClass' => PasienM::className(), 'targetAttribute' => ['pasien_id' => 'pasien_id']],
            // [['pasienadmisi_id'], 'exist', 'skipOnError' => true, 'targetClass' => PasienadmisiT::className(), 'targetAttribute' => ['pasienadmisi_id' => 'pasienadmisi_id']],
            // [['pendaftaran_id'], 'exist', 'skipOnError' => true, 'targetClass' => PendaftaranT::className(), 'targetAttribute' => ['pendaftaran_id' => 'pendaftaran_id']],
            // [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => RuanganM::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],
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
            'kamartempattidur_id' => 'Kamartempattidur ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKamarruangan()
    {
        return $this->hasOne(KamarruanganM::className(), ['kamarruangan_id' => 'kamarruangan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKelaspelayanan()
    {
        return $this->hasOne(KelaspelayananM::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasien()
    {
        return $this->hasOne(PasienM::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienadmisi()
    {
        return $this->hasOne(PasienadmisiT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendaftaran()
    {
        return $this->hasOne(PendaftaranT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuangan()
    {
        return $this->hasOne(RuanganM::className(), ['ruangan_id' => 'ruangan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMasukkamarTs()
    {
        return $this->hasMany(MasukkamarT::className(), ['bookingkamar_id' => 'bookingkamar_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienadmisiTs()
    {
        return $this->hasMany(PasienadmisiT::className(), ['bookingkamar_id' => 'bookingkamar_id']);
    }
}
