<?php

/**
 * @Author: afil
 * @Date:   2018-01-15 18:00:44
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2018-11-22 10:46:07
 * @Description: 
 */
namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "anamnesa_t".
 *
 * @property int $anamesa_id
 * @property int $pendaftaran_id
 * @property int $pasien_id
 * @property int $triase_id
 * @property int $pasienadmisi_id
 * @property int $pegawaidokter_id
 * @property int $pegawaiperawat_id
 * @property int $pegawaitriase_id
 * @property string $tgl_anamnesis
 * @property string $keluhan_utama
 * @property string $keluhan_tambahan
 * @property string $riwayat_penyakitterdahulu
 * @property string $riwayat_penyakitkeluarga
 * @property string $lama_sakit
 * @property string $pengobatan_ygsudahdilakukan
 * @property string $riwayat_alergiobat
 * @property string $riwayat_kelahiran
 * @property string $riwayat_makanan
 * @property string $riwayat_imunisasi
 * @property string $keterangan_anamesa
 * @property string $riwayat_perjalananpasien
 * @property bool $status_merokok
 * @property int $jmlrokok_btgperhari
 * @property string $riwayat_imunisasiblm
 * @property string $riwayat_obatygsering
 * @property string $keb_olahraga
 * @property string $keb_jnsolahraga
 * @property int $keb_frekuensi_kaliminggu
 * @property string $keb_konsumsialkohol
 * @property string $keb_minumkopi
 * @property string $riwayat_kecelakaan
 * @property string $riwayat_operasi
 * @property string $keb_konsumsidrug
 * @property string $additional_data
 * @property string $created_date
 * @property string $lokasi_nyeri
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 * @property int $skala_nyeri
 *
 * @property PasienM $pasien
 * @property PasienadmisiT $pasienadmisi
 * @property PegawaiM $pegawaidokter
 * @property PegawaiM $pegawaiperawat
 * @property PegawaiM $pegawaitriase
 * @property PendaftaranT $pendaftaran
 * @property TriaseM $triase
 */
class Anamnesa extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'anamnesa_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasien_id', 'pegawaidokter_id'], 'required'],
            [['pendaftaran_id', 'pasien_id', 'triase_id', 'pasienadmisi_id', 'pegawaidokter_id', 'pegawaiperawat_id', 'pegawaitriase_id', 'jmlrokok_btgperhari', 'keb_frekuensi_kaliminggu', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasien_id', 'triase_id', 'pasienadmisi_id', 'pegawaidokter_id', 'pegawaiperawat_id', 'pegawaitriase_id', 'jmlrokok_btgperhari', 'keb_frekuensi_kaliminggu', 'skala_nyeri','created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_anamnesis', 'created_date', 'last_modified_date', 'deleted_date','is_nyeri','is_resikojatuh'], 'safe'],
            [['keluhan_utama', 'keluhan_tambahan', 'keterangan_anamesa', 'riwayat_perjalananpasien', 'riwayat_kecelakaan', 'riwayat_operasi', 'additional_data'], 'string'],
            [['status_merokok', 'is_deleted', 'is_active'], 'boolean'],
            [['riwayat_penyakitterdahulu', 'riwayat_penyakitkeluarga', 'keb_jnsolahraga'], 'string', 'max' => 200],
            [['lama_sakit'], 'string', 'max' => 20],
            [['pengobatan_ygsudahdilakukan', 'riwayat_alergiobat', 'riwayat_kelahiran', 'riwayat_makanan','lokasi_nyeri'], 'string', 'max' => 100],
            [['riwayat_imunisasi', 'riwayat_imunisasiblm', 'riwayat_obatygsering'], 'string', 'max' => 500],
            [['keb_olahraga', 'keb_konsumsialkohol', 'keb_minumkopi', 'keb_konsumsidrug'], 'string', 'max' => 5],
            // [['pasien_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pasien::className(), 'targetAttribute' => ['pasien_id' => 'pasien_id']],
            // [['pasienadmisi_id'], 'exist', 'skipOnError' => true, 'targetClass' => PasienadmisiT::className(), 'targetAttribute' => ['pasienadmisi_id' => 'pasienadmisi_id']],
            [['pegawaidokter_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pegawai::className(), 'targetAttribute' => ['pegawaidokter_id' => 'pegawai_id']],
            [['pegawaiperawat_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pegawai::className(), 'targetAttribute' => ['pegawaiperawat_id' => 'pegawai_id']],
            [['pegawaitriase_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pegawai::className(), 'targetAttribute' => ['pegawaitriase_id' => 'pegawai_id']],
            [['pendaftaran_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pendaftaran::className(), 'targetAttribute' => ['pendaftaran_id' => 'pendaftaran_id']],
            // [['triase_id'], 'exist', 'skipOnError' => true, 'targetClass' => TriaseM::className(), 'targetAttribute' => ['triase_id' => 'triase_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'anamesa_id' => 'Anamesa ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasien_id' => 'Pasien ID',
            'triase_id' => 'Triase ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'pegawaidokter_id' => 'Pegawaidokter ID',
            'pegawaiperawat_id' => 'Pegawaiperawat ID',
            'pegawaitriase_id' => 'Pegawaitriase ID',
            'tgl_anamnesis' => 'Tgl Anamnesis',
            'keluhan_utama' => 'Keluhan Utama',
            'keluhan_tambahan' => 'Keluhan Tambahan',
            'riwayat_penyakitterdahulu' => 'Riwayat Penyakitterdahulu',
            'riwayat_penyakitkeluarga' => 'Riwayat Penyakitkeluarga',
            'lama_sakit' => 'Lama Sakit',
            'pengobatan_ygsudahdilakukan' => 'Pengobatan Ygsudahdilakukan',
            'riwayat_alergiobat' => 'Riwayat Alergiobat',
            'riwayat_kelahiran' => 'Riwayat Kelahiran',
            'riwayat_makanan' => 'Riwayat Makanan',
            'riwayat_imunisasi' => 'Riwayat Imunisasi',
            'keterangan_anamesa' => 'Keterangan Anamesa',
            'riwayat_perjalananpasien' => 'Riwayat Perjalananpasien',
            'status_merokok' => 'Status Merokok',
            'jmlrokok_btgperhari' => 'Jmlrokok Btgperhari',
            'riwayat_imunisasiblm' => 'Riwayat Imunisasiblm',
            'riwayat_obatygsering' => 'Riwayat Obatygsering',
            'keb_olahraga' => 'Keb Olahraga',
            'keb_jnsolahraga' => 'Keb Jnsolahraga',
            'keb_frekuensi_kaliminggu' => 'Keb Frekuensi Kaliminggu',
            'keb_konsumsialkohol' => 'Keb Konsumsialkohol',
            'keb_minumkopi' => 'Keb Minumkopi',
            'riwayat_kecelakaan' => 'Riwayat Kecelakaan',
            'riwayat_operasi' => 'Riwayat Operasi',
            'keb_konsumsidrug' => 'Keb Konsumsidrug',
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
            'skala_nyeri' => 'Skala Nyeri',
            'lokasi_nyeri' => 'Lokasi',
        ];
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
        return $this->hasOne(PasienadmisiT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPegawaidokter()
    {
        return $this->hasOne(Pegawai::className(), ['pegawai_id' => 'pegawaidokter_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPegawaiperawat()
    {
        return $this->hasOne(Pegawai::className(), ['pegawai_id' => 'pegawaiperawat_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPegawaitriase()
    {
        return $this->hasOne(Pegawai::className(), ['pegawai_id' => 'pegawaitriase_id']);
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
    public function getTriase()
    {
        return $this->hasOne(TriaseM::className(), ['triase_id' => 'triase_id']);
    }
}
