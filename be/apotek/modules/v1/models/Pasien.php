<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pasien_m".
 *
 * @property int $pasien_id
 * @property string $no_rekam_medik
 * @property string $tgl_rekam_medik
 * @property string $jenisidentitas
 * @property string $no_identitas_pasien
 * @property string $namadepan
 * @property string $nama_pasien
 * @property string $nama_bin
 * @property string $jeniskelamin
 * @property string $tempat_lahir
 * @property string $tanggal_lahir
 * @property int $kelompokumur_id
 * @property string $alamat_pasien
 * @property int $rt
 * @property int $rw
 * @property int $propinsi_id
 * @property int $kabupaten_id
 * @property int $kecamatan_id
 * @property int $kelurahan_id
 * @property int $pendidikan_id
 * @property int $pekerjaan_id
 * @property int $suku_id
 * @property string $statusperkawinan
 * @property string $agama
 * @property string $golongandarah
 * @property string $rhesus
 * @property int $anakke
 * @property int $jumlah_bersaudara
 * @property string $no_telepon_pasien
 * @property string $no_mobile_pasien
 * @property string $warga_negara
 * @property string $photopasien
 * @property string $alamatemail
 * @property string $nama_ibu
 * @property string $nama_ayah
 * @property int $dokrekammedis_id
 * @property string $tgl_meninggal
 * @property int $pegawai_id
 * @property int $loginpemakai_id
 * @property double $garis_latitude
 * @property double $garis_longitude
 * @property string $statusrekammedis
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
 * @property int $profilrs_id
 *
 * @property AmbiljenazahT[] $ambiljenazahTs
 * @property AnamnesaT[] $anamnesaTs
 * @property AnamnesadietT[] $anamnesadietTs
 * @property AsuhankeperawatanT[] $asuhankeperawatanTs
 * @property AsuransipasienM[] $asuransipasienMs
 * @property BayaruangmukaT[] $bayaruangmukaTs
 * @property BookingkamarT[] $bookingkamarTs
 * @property BuatjanjipoliT[] $buatjanjipoliTs
 * @property DietpasienT[] $dietpasienTs
 * @property DokrekammedisM $dokrekammedisM
 * @property HasilmcuT[] $hasilmcuTs
 * @property HasilpemeriksaanlabT[] $hasilpemeriksaanlabTs
 * @property HasilpemeriksaanradT[] $hasilpemeriksaanradTs
 * @property HasilpemeriksaanrmT[] $hasilpemeriksaanrmTs
 * @property HearingtestT[] $hearingtestTs
 * @property InvoicemasukdetailT[] $invoicemasukdetailTs
 * @property JadwalkunjunganrmT[] $jadwalkunjunganrmTs
 * @property JantungkoronerT[] $jantungkoronerTs
 * @property KesimpulanmcuT[] $kesimpulanmcuTs
 * @property DokrekammedisM $dokrekammedis
 * @property KabupatenM $kabupaten
 * @property KecamatanM $kecamatan
 * @property KelompokumurM $kelompokumur
 * @property KelurahanM $kelurahan
 * @property PekerjaanM $pekerjaan
 * @property PendidikanM $pendidikan
 * @property PropinsiM $propinsi
 * @property SukuM $suku
 * @property PasienadmisiT[] $pasienadmisiTs
 * @property PasienanastesiT[] $pasienanastesiTs
 * @property PasienbatalperiksaT[] $pasienbatalperiksaTs
 * @property PasienkirimkeunitlainT[] $pasienkirimkeunitlainTs
 * @property PasienpulangT[] $pasienpulangTs
 * @property PemakaianambulansT[] $pemakaianambulansTs
 * @property PembayaranpelayananT[] $pembayaranpelayananTs
 * @property PembklaimdetailT[] $pembklaimdetailTs
 * @property PendaftaranT[] $pendaftaranTs
 * @property PengirimanrmT[] $pengirimanrmTs
 * @property PenjualanresepT[] $penjualanresepTs
 * @property PersalinanT[] $persalinanTs
 * @property PesanambulansT[] $pesanambulansTs
 * @property PindahkamarT[] $pindahkamarTs
 * @property RencanaoperasiT[] $rencanaoperasiTs
 * @property RencanatindakanT[] $rencanatindakanTs
 * @property ReturresepT[] $returresepTs
 * @property TindakanpelayananT[] $tindakanpelayananTs
 */
class Pasien extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    
    public static function tableName()
    {
        return 'pasien_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_rekam_medik', 'tgl_rekam_medik', 'nama_pasien', 'jeniskelamin', 'tanggal_lahir', 'kelompokumur_id', 'alamat_pasien'], 'required'],
            [['penanggungjawab_nama', 'penanggungjawab_alamat', 'no_identitas', 'penanggungjawab_notelp', 'hubungankeluarga', 'tgl_rekam_medik', 'tanggal_lahir', 'tgl_meninggal', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['kelompokumur_id', 'rt', 'rw', 'propinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id', 'pendidikan_id', 'pekerjaan_id', 'suku_id', 'anakke', 'jumlah_bersaudara', 'dokrekammedis_id', 'pegawai_id', 'loginpemakai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'profilrs_id'], 'default', 'value' => null],
            [['kelompokumur_id', 'rt', 'rw', 'propinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id', 'pendidikan_id', 'pekerjaan_id', 'suku_id', 'anakke', 'jumlah_bersaudara', 'dokrekammedis_id', 'pegawai_id', 'loginpemakai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'profilrs_id'], 'integer'],
            [['alamat_pasien', 'additional_data'], 'string'],
            [['garis_latitude', 'garis_longitude'], 'number'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_rekam_medik', 'statusrekammedis'], 'string', 'max' => 10],
            [['jenisidentitas', 'namadepan', 'jeniskelamin', 'statusperkawinan', 'agama', 'rhesus', 'no_mobile_pasien'], 'string', 'max' => 20],
            [['no_identitas_pasien', 'nama_bin'], 'string', 'max' => 30],
            [['nama_pasien', 'nama_ibu', 'nama_ayah'], 'string', 'max' => 50],
            [['tempat_lahir', 'warga_negara'], 'string', 'max' => 25],
            [['golongandarah'], 'string', 'max' => 2],
            [['no_telepon_pasien'], 'string', 'max' => 15],
            [['photopasien'], 'string', 'max' => 200],
            [['alamatemail'], 'string', 'max' => 100],
            // [['dokrekammedis_id'], 'exist', 'skipOnError' => true, 'targetClass' => DokrekammedisM::className(), 'targetAttribute' => ['dokrekammedis_id' => 'dokrekammedis_id']],
            // [['kabupaten_id'], 'exist', 'skipOnError' => true, 'targetClass' => KabupatenM::className(), 'targetAttribute' => ['kabupaten_id' => 'kabupaten_id']],
            // [['kecamatan_id'], 'exist', 'skipOnError' => true, 'targetClass' => KecamatanM::className(), 'targetAttribute' => ['kecamatan_id' => 'kecamatan_id']],
            // [['kelompokumur_id'], 'exist', 'skipOnError' => true, 'targetClass' => KelompokumurM::className(), 'targetAttribute' => ['kelompokumur_id' => 'kelompokumur_id']],
            // [['kelurahan_id'], 'exist', 'skipOnError' => true, 'targetClass' => KelurahanM::className(), 'targetAttribute' => ['kelurahan_id' => 'kelurahan_id']],
            // [['pekerjaan_id'], 'exist', 'skipOnError' => true, 'targetClass' => PekerjaanM::className(), 'targetAttribute' => ['pekerjaan_id' => 'pekerjaan_id']],
            // [['pendidikan_id'], 'exist', 'skipOnError' => true, 'targetClass' => PendidikanM::className(), 'targetAttribute' => ['pendidikan_id' => 'pendidikan_id']],
            // [['propinsi_id'], 'exist', 'skipOnError' => true, 'targetClass' => PropinsiM::className(), 'targetAttribute' => ['propinsi_id' => 'propinsi_id']],
            // [['suku_id'], 'exist', 'skipOnError' => true, 'targetClass' => SukuM::className(), 'targetAttribute' => ['suku_id' => 'suku_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pasien_id' => 'Pasien ID',
            'no_rekam_medik' => 'No Rekam Medik',
            'tgl_rekam_medik' => 'Tgl Rekam Medik',
            'jenisidentitas' => 'Jenisidentitas',
            'no_identitas_pasien' => 'No Identitas Pasien',
            'namadepan' => 'Namadepan',
            'nama_pasien' => 'Nama Pasien',
            'nama_bin' => 'Nama Bin',
            'jeniskelamin' => 'Jeniskelamin',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'kelompokumur_id' => 'Kelompokumur ID',
            'alamat_pasien' => 'Alamat Pasien',
            'alamat_sekarang' => 'Alamat Sekarang',
            'rt' => 'Rt',
            'rw' => 'Rw',
            'propinsi_id' => 'Propinsi ID',
            'kabupaten_id' => 'Kabupaten ID',
            'kecamatan_id' => 'Kecamatan ID',
            'kelurahan_id' => 'Kelurahan ID',
            'pendidikan_id' => 'Pendidikan ID',
            'pekerjaan_id' => 'Pekerjaan ID',
            'suku_id' => 'Suku ID',
            'statusperkawinan' => 'Statusperkawinan',
            'agama' => 'Agama',
            'golongandarah' => 'Golongandarah',
            'rhesus' => 'Rhesus',
            'anakke' => 'Anakke',
            'jumlah_bersaudara' => 'Jumlah Bersaudara',
            'no_telepon_pasien' => 'No Telp',
            'no_mobile_pasien' => 'No Mobile Pasien',
            'warga_negara' => 'Warga Negara',
            'photopasien' => 'Photopasien',
            'alamatemail' => 'Alamatemail',
            'nama_ibu' => 'Nama Ibu',
            'nama_ayah' => 'Nama Ayah',
            'dokrekammedis_id' => 'Dokrekammedis ID',
            'tgl_meninggal' => 'Tgl Meninggal',
            'pegawai_id' => 'Pegawai ID',
            'loginpemakai_id' => 'Loginpemakai ID',
            'garis_latitude' => 'Garis Latitude',
            'garis_longitude' => 'Garis Longitude',
            'statusrekammedis' => 'Statusrekammedis',
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
            'profilrs_id' => 'Profilrs ID',
            'penanggungjawab_nama' => 'Penanggung Jawab'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAmbiljenazahTs()
    {
        return $this->hasMany(AmbiljenazahT::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnamnesaTs()
    {
        return $this->hasMany(AnamnesaT::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnamnesadietTs()
    {
        return $this->hasMany(AnamnesadietT::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAsuhankeperawatanTs()
    {
        return $this->hasMany(AsuhankeperawatanT::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAsuransipasienMs()
    {
        return $this->hasMany(AsuransipasienM::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBayaruangmukaTs()
    {
        return $this->hasMany(BayaruangmukaT::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBookingkamarTs()
    {
        return $this->hasMany(BookingkamarT::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuatjanjipoliTs()
    {
        return $this->hasMany(BuatjanjipoliT::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDietpasienTs()
    {
        return $this->hasMany(DietpasienT::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDokrekammedisM()
    {
        return $this->hasOne(DokrekammedisM::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHasilmcuTs()
    {
        return $this->hasMany(HasilmcuT::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHasilpemeriksaanlabTs()
    {
        return $this->hasMany(HasilpemeriksaanlabT::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHasilpemeriksaanradTs()
    {
        return $this->hasMany(HasilpemeriksaanradT::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHasilpemeriksaanrmTs()
    {
        return $this->hasMany(HasilpemeriksaanrmT::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHearingtestTs()
    {
        return $this->hasMany(HearingtestT::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoicemasukdetailTs()
    {
        return $this->hasMany(InvoicemasukdetailT::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJadwalkunjunganrmTs()
    {
        return $this->hasMany(JadwalkunjunganrmT::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJantungkoronerTs()
    {
        return $this->hasMany(JantungkoronerT::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKesimpulanmcuTs()
    {
        return $this->hasMany(KesimpulanmcuT::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDokrekammedis()
    {
        return $this->hasOne(DokrekammedisM::className(), ['dokrekammedis_id' => 'dokrekammedis_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKabupaten()
    {
        return $this->hasOne(KabupatenM::className(), ['kabupaten_id' => 'kabupaten_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKecamatan()
    {
        return $this->hasOne(KecamatanM::className(), ['kecamatan_id' => 'kecamatan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKelompokumur()
    {
        return $this->hasOne(KelompokumurM::className(), ['kelompokumur_id' => 'kelompokumur_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKelurahan()
    {
        return $this->hasOne(KelurahanM::className(), ['kelurahan_id' => 'kelurahan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPekerjaan()
    {
        return $this->hasOne(PekerjaanM::className(), ['pekerjaan_id' => 'pekerjaan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendidikan()
    {
        return $this->hasOne(PendidikanM::className(), ['pendidikan_id' => 'pendidikan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPropinsi()
    {
        return $this->hasOne(PropinsiM::className(), ['propinsi_id' => 'propinsi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSuku()
    {
        return $this->hasOne(SukuM::className(), ['suku_id' => 'suku_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienadmisiTs()
    {
        return $this->hasMany(PasienadmisiT::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienanastesiTs()
    {
        return $this->hasMany(PasienanastesiT::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienbatalperiksaTs()
    {
        return $this->hasMany(PasienbatalperiksaT::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienkirimkeunitlainTs()
    {
        return $this->hasMany(PasienkirimkeunitlainT::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienpulangTs()
    {
        return $this->hasMany(PasienpulangT::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPemakaianambulansTs()
    {
        return $this->hasMany(PemakaianambulansT::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPembayaranpelayananTs()
    {
        return $this->hasMany(PembayaranpelayananT::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPembklaimdetailTs()
    {
        return $this->hasMany(PembklaimdetailT::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendaftaranTs()
    {
        return $this->hasMany(PendaftaranT::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPengirimanrmTs()
    {
        return $this->hasMany(PengirimanrmT::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenjualanresepTs()
    {
        return $this->hasMany(PenjualanresepT::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPersalinanTs()
    {
        return $this->hasMany(PersalinanT::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPesanambulansTs()
    {
        return $this->hasMany(PesanambulansT::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPindahkamarTs()
    {
        return $this->hasMany(PindahkamarT::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRencanaoperasiTs()
    {
        return $this->hasMany(RencanaoperasiT::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRencanatindakanTs()
    {
        return $this->hasMany(RencanatindakanT::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReturresepTs()
    {
        return $this->hasMany(ReturresepT::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTindakanpelayananTs()
    {
        return $this->hasMany(TindakanpelayananT::className(), ['pasien_id' => 'pasien_id']);
    }

    public function getInfoPasien($where)
    {
        $sql = 'SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.pegawai_id
                    ELSE pasienadmisi_t.pegawai_id
                END AS dokter_id,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN dok_rj.nama_pegawai
                    ELSE dok_ri.nama_pegawai
                END AS dpjp_nama,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.carabayar_id
                    ELSE pasienadmisi_t.carabayar_id
                END AS carabayar_id,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.penjamin_id
                    ELSE pasienadmisi_t.penjamin_id
                END AS penjamin_id,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.kelaspelayanan_id
                    ELSE COALESCE(pasienadmisi_t.kelas_ditagihkan_id, pasienadmisi_t.kelaspelayanan_id)
                END AS kelaspelayanan_id,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN kelaspelayanan_rj.kelaspelayanan_nama
                    ELSE COALESCE(kelaspelayanan_ri_ditagihkan.kelaspelayanan_nama, kelaspelayanan_ri.kelaspelayanan_nama)
                END AS kelaspelayanan_nama,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.ruangan_id
                    ELSE pasienadmisi_t.ruangan_id
                END AS ruangan_id,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.instalasi_id
                    ELSE ruang_ri.instalasi_id
                END AS instalasi_id,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            CONCAT(nama_depan.lookup_value,\' \',pasien_m.nama_pasien) AS nama,
            pasien_m.tanggal_lahir,
            pasien_m.no_telepon_pasien,
            pasien_m.alamat_pasien,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN instalasi_rj.instalasi_nama
                    ELSE instalasi_ri.instalasi_nama
                END AS instalasi_nama,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN ruang_rj.ruangan_nama
                    ELSE ruang_ri.ruangan_nama
                END AS ruangan_nama,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN carabayar_rj.carabayar_nama
                    ELSE carabayar_ri.carabayar_nama
                END AS carabayar_nama,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN penjamin_rj.penjamin_nama
                    ELSE penjamin_ri.penjamin_nama
                END AS penjamin_nama,
                CASE
                    WHEN pendaftaran_t.instalasi_id = 2 THEN true
                    ELSE false
                END AS is_rd,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.instalasi_id = 1 THEN pemeriksaanfisik_t.bb
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.instalasi_id = 2 THEN asesmenmedisrd_t.bb::double precision
                    WHEN pendaftaran_t.pasienadmisi_id IS NOT NULL THEN asesmenmedis_t.bb::double precision
                    ELSE NULL::double precision
                END AS berat_badan,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.instalasi_id = 1 THEN pemeriksaanfisik_t.tb
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.instalasi_id = 2 THEN asesmenmedisrd_t.tb::double precision
                    WHEN pendaftaran_t.pasienadmisi_id IS NOT NULL THEN asesmenmedis_t.tb::double precision
                    ELSE NULL::double precision
                END AS tinggi_badan,
            pendaftaran_t.is_stopakomodasi,
            pendaftaran_t.status_periksa,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.status_bayar,
            bpjs.nosep,
                CASE
                    WHEN pasienadmisi_t.is_pasientitipan = true THEN pasienadmisi_t.kelas_ditagihkan_id
                    ELSE NULL::integer
                END AS kelas_titipan
        FROM pendaftaran_t
            LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.pasienpulang_id,
                    a.pegawai_id,
                    a.kelaspelayanan_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.ruangan_id,
                    a.is_pasientitipan,
                    a.kelas_ditagihkan_id,
                    a.status_ranap,
                    a.bpjs_id
                FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            LEFT JOIN ( SELECT b.pegawai_id,
                    b.nama_pegawai
                FROM pegawai_m b) dok_rj ON pendaftaran_t.pegawai_id = dok_rj.pegawai_id
            LEFT JOIN ( SELECT c.pegawai_id,
                    c.nama_pegawai
                FROM pegawai_m c) dok_ri ON pasienadmisi_t.pegawai_id = dok_ri.pegawai_id
            LEFT JOIN ( SELECT d.ruangan_id,
                    d.instalasi_id,
                    d.ruangan_nama
                FROM ruangan_m d) ruang_rj ON pendaftaran_t.ruangan_id = ruang_rj.ruangan_id
            LEFT JOIN ( SELECT d.ruangan_id,
                    d.instalasi_id,
                    d.ruangan_nama
                FROM ruangan_m d) ruang_ri ON pasienadmisi_t.ruangan_id = ruang_ri.ruangan_id
            JOIN ( SELECT e.pasien_id,
                    e.nama_pasien,
                    e.no_rekam_medik,
                    e.tanggal_lahir,
                    e.no_telepon_pasien,
                    e.alamat_pasien,
                    e.namadepan
                FROM pasien_m e) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
            LEFT JOIN ( SELECT f.instalasi_id,
                    f.instalasi_nama
                FROM instalasi_m f) instalasi_rj ON pendaftaran_t.instalasi_id = instalasi_rj.instalasi_id
            LEFT JOIN ( SELECT g.instalasi_id,
                    g.instalasi_nama
                FROM instalasi_m g) instalasi_ri ON ruang_ri.instalasi_id = instalasi_ri.instalasi_id
            LEFT JOIN ( SELECT h.carabayar_id,
                    h.carabayar_nama
                FROM carabayar_m h) carabayar_rj ON pendaftaran_t.carabayar_id = carabayar_rj.carabayar_id
            LEFT JOIN ( SELECT i.carabayar_id,
                    i.carabayar_nama
                FROM carabayar_m i) carabayar_ri ON pasienadmisi_t.carabayar_id = carabayar_ri.carabayar_id
            LEFT JOIN ( SELECT j.penjamin_id,
                    j.penjamin_nama
                FROM penjamin_m j) penjamin_rj ON pendaftaran_t.penjamin_id = penjamin_rj.penjamin_id
            LEFT JOIN ( SELECT k.penjamin_id,
                    k.penjamin_nama
                FROM penjamin_m k) penjamin_ri ON pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id
            LEFT JOIN ( SELECT k.kelaspelayanan_id,
                    k.kelaspelayanan_nama
                FROM kelaspelayanan_m k) kelaspelayanan_ri ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_ri.kelaspelayanan_id
            LEFT JOIN ( SELECT k.kelaspelayanan_id,
                    k.kelaspelayanan_nama
                FROM kelaspelayanan_m k) kelaspelayanan_ri_ditagihkan ON pasienadmisi_t.kelas_ditagihkan_id = kelaspelayanan_ri_ditagihkan.kelaspelayanan_id
            LEFT JOIN ( SELECT k.kelaspelayanan_id,
                    k.kelaspelayanan_nama
                FROM kelaspelayanan_m k) kelaspelayanan_rj ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_rj.kelaspelayanan_id
            LEFT JOIN ( SELECT a.bpjs_id,
                    a.nosep
                    FROM bpjs_t a
                WHERE a.is_deleted IS FALSE) bpjs on COALESCE(pasienadmisi_t.bpjs_id, pendaftaran_t.bpjs_id) = bpjs.bpjs_id
            LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_value
                        FROM lookup_m a) nama_depan ON pasien_m.namadepan::int = nama_depan.lookup_id
            LEFT JOIN ( SELECT DISTINCT ON (pemeriksaanfisik_t.pendaftaran_id) pemeriksaanfisik_t.pendaftaran_id,
                    pemeriksaanfisik_t.tinggibadan_cm AS tb,
                    pemeriksaanfisik_t.beratbadan_kg AS bb
                FROM pemeriksaanfisik_t
                WHERE pemeriksaanfisik_t.is_deleted = false) pemeriksaanfisik_t ON pendaftaran_t.pendaftaran_id = pemeriksaanfisik_t.pendaftaran_id
           LEFT JOIN ( SELECT DISTINCT ON (ass_medisrd.pendaftaran_id) ass_medisrd.pendaftaran_id,
                    REPLACE(ass_medisrd.tinggi_badan, \',\', \'.\')::double precision AS tb,
                    REPLACE(ass_medisrd.berat_badan, \',\', \'.\')::double precision AS bb
                   FROM asesmenmedisrd_t ass_medisrd
                  WHERE ass_medisrd.is_deleted = false) asesmenmedisrd_t ON pendaftaran_t.pendaftaran_id = asesmenmedisrd_t.pendaftaran_id
            LEFT JOIN ( SELECT DISTINCT ON (ass_medis.pendaftaran_id) ass_medis.pendaftaran_id,
                    ass_medis.tinggi_badan AS tb,
                    ass_medis.berat_badan AS bb
                   FROM asesmenmedis_t ass_medis
                  WHERE ass_medis.is_deleted = false) asesmenmedis_t ON pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id';
        if (!empty($where)) {
            $sql.= ' WHERE pendaftaran_t.is_deleted = false AND '.$where;
        } else {
            $sql.= ' WHERE pendaftaran_t.is_deleted = false';
        }
        $sql .= ' ORDER BY pendaftaran_t.created_date DESC';

         return Yii::$app->db->createCommand($sql)
                ->queryOne();
    }
}
