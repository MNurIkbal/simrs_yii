<?php

namespace app\modules\pendaftaran\models;

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
 * @property string $status_booking lookup_type='status_bookingkamar'
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
 * @property int $kamartempattidur_id
 * @property int $jeniskasuspenyakit_id
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
class TraPemesananKamarForm extends \yii\db\ActiveRecord
{
    public $nokamar;
    public $no_rekam_medik;
    public $no_identitas_pasien;
    public $nama_pasien;
    public $tanggal_lahir;
    public $tempat_lahir;
    public $umur;
    public $jeniskelamin;
    public $statusperkawinan;
    public $nama_ibu;
    public $ruangan_nama;
    public $jenisidentitas;
    public $nama_bin;
    public $pekerjaan_id;
    public $agama;
    public $no_telepon_pasien;
    public $alamat_pasien;
    public $tgl_rawatinap;
    public $kamarruangan_jenis;
    public $jenis_kelamin_booking;

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
            [['ruangan_id', 'kamarruangan_id', 'kelaspelayanan_id', 'tgl_transaksibooking', 'tgl_bookingkamar', 'status_booking', 'kamartempattidur_id','nama_pasien','jeniskasuspenyakit_id','tempat_lahir', 'tanggal_lahir', 'umur' ,'jeniskelamin','alamat_pasien','no_telepon_pasien','nokamar','tgl_rawatinap','nama_pemesan'], 'required'],
            [['ruangan_id', 'pasien_id', 'pasienadmisi_id', 'pendaftaran_id', 'kamarruangan_id', 'kelaspelayanan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'kamartempattidur_id', 'jeniskasuspenyakit_id'], 'default', 'value' => null],
            [['pasien_id', 'pasienadmisi_id', 'pendaftaran_id', 'kamarruangan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'kamartempattidur_id'], 'integer'],
            [['tgl_transaksibooking', 'tgl_bookingkamar', 'tgl_akhirkonfirmasi', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['keterangan_booking', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['bookingkamar_no'], 'string', 'max' => 20],
            [['status_booking'], 'string', 'max' => 10],
            [['status_konfirmasi'], 'string', 'max' => 50],
            [['bookingkamar_no'], 'unique'],
            // [['kamarruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Kamarruangan::className(), 'targetAttribute' => ['kamarruangan_id' => 'kamarruangan_id']],
            // [['kelaspelayanan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Kelaspelayanan::className(), 'targetAttribute' => ['kelaspelayanan_id' => 'kelaspelayanan_id']],
            // [['pasien_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pasien::className(), 'targetAttribute' => ['pasien_id' => 'pasien_id']],
            // [['pasienadmisi_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pasienadmisi::className(), 'targetAttribute' => ['pasienadmisi_id' => 'pasienadmisi_id']],
            // [['pendaftaran_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pendaftaran::className(), 'targetAttribute' => ['pendaftaran_id' => 'pendaftaran_id']],
            // [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ruangan::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'bookingkamar_id' => 'Bookingkamar ID',
            'ruangan_id' => 'Ruangan',
            'pasien_id' => 'Pasien',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'kamarruangan_id' => Yii::t('fe','Kamar Ruangan'),
            'kelaspelayanan_id' => Yii::t('fe','Kelas Pelayanan'),
            'bookingkamar_no' => Yii::t('fe','No Pemesanan'),
            'tgl_transaksibooking' => Yii::t('fe','Tanggal Pemesanan'),
            'tgl_bookingkamar' => 'Tgl Booking Kamar',
            'status_booking' => 'Status Booking',
            'keterangan_booking' => Yii::t('fe','Keterangan Pendaftaran'),
            'status_konfirmasi' => 'Status Konfirmasi',
            'tgl_akhirkonfirmasi' => 'Tanggal Akhir Konfirmasi',
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
            'kamartempattidur_id' => 'No Kamar',
            'jeniskasuspenyakit_id' => Yii::t('fe','Jenis Kasus Penyakit'),
            'pekerjaan_id' => Yii::t('fe','Pekerjaan'),
            'jeniskelamin' => Yii::t('fe','Jenis Kelamin'),
            'nokamar' => Yii::t('fe','No. Tempat Tidur'),
            'no_telepon_pasien' => Yii::t('fe','No Tlp/Hp'),
            'tgl_rawatinap' => Yii::t('fe','Tanggal Rawat Inap'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKamarruangan()
    {
        return $this->hasOne(Kamarruangan::className(), ['kamarruangan_id' => 'kamarruangan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKelaspelayanan()
    {
        return $this->hasOne(Kelaspelayanan::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasien()
    {
        return $this->hasOne(Pasien::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienadmisi()
    {
        return $this->hasOne(Pasienadmisi::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendaftaran()
    {
        return $this->hasOne(Pendaftaran::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuangan()
    {
        return $this->hasOne(Ruangan::className(), ['ruangan_id' => 'ruangan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMasukkamarTs()
    {
        return $this->hasMany(Masukkamar::className(), ['bookingkamar_id' => 'bookingkamar_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienadmisiTs()
    {
        return $this->hasMany(Pasienadmisi::className(), ['bookingkamar_id' => 'bookingkamar_id']);
    }
}
