<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pendaftaran_t".
 *
 * @property int $pendaftaran_id
 * @property string $no_pendaftaran
 * @property string $tgl_pendaftaran
 * @property int $pasienpulang_id
 * @property int $pasienbatalperiksa_id
 * @property int $penanggungjawab_id
 * @property int $penjamin_id
 * @property int $shift_id
 * @property int $pasien_id
 * @property int $persalinan_id
 * @property int $pegawai_id
 * @property int $instalasi_id
 * @property int $caramasuk_id
 * @property int $pengirimanrm_id
 * @property int $peminjamanrm_id
 * @property int $jeniskasuspenyakit_id
 * @property int $pembayaranpelayanan_id
 * @property int $kelaspelayanan_id
 * @property int $carabayar_id
 * @property int $pasienadmisi_id
 * @property int $kelompokumur_id
 * @property int $golonganumur_id
 * @property int $rujukan_id
 * @property int $antrian_id
 * @property int $karcis_id
 * @property int $ruangan_id
 * @property string $no_urutantri
 * @property string $transportasi
 * @property string $keadaan_masuk
 * @property string $status_periksa
 * @property string $status_pasien
 * @property string $kunjungan
 * @property bool $alih_status
 * @property bool $by_phone
 * @property bool $kunjungan_rumah
 * @property string $status_masuk
 * @property string $umur
 * @property string $tgl_selesaiperiksa
 * @property string $keterangan_pendaftaran
 * @property bool $nopendaftaran_aktif
 * @property string $status_konfirmasi
 * @property string $tgl_konfirmasi
 * @property string $tgl_renkontrol
 * @property bool $status_farmasi
 * @property bool $panggil_antrian
 * @property int $asuransipasien_id
 * @property string $tgl_akandilayani
 * @property string $statusdok_rekammedik
 * @property int $bpjs_id
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
 * @property AmbiljenazahT[] $ambiljenazahTs
 * @property AnamnesaT[] $anamnesaTs
 * @property AnamnesadietT[] $anamnesadietTs
 * @property AntrianT[] $antrianTs
 * @property AsuhankeperawatanT[] $asuhankeperawatanTs
 * @property BayaruangmukaT[] $bayaruangmukaTs
 * @property BookingkamarT[] $bookingkamarTs
 * @property BuatjanjipoliT[] $buatjanjipoliTs
 * @property DietpasienT[] $dietpasienTs
 * @property HasilmcuT[] $hasilmcuTs
 * @property HasilpemeriksaanlabT[] $hasilpemeriksaanlabTs
 * @property HasilpemeriksaanmcuT[] $hasilpemeriksaanmcuTs
 * @property HasilpemeriksaanradT[] $hasilpemeriksaanradTs
 * @property HasilpemeriksaanrmT[] $hasilpemeriksaanrmTs
 * @property HearingtestT[] $hearingtestTs
 * @property JadwalkunjunganrmT[] $jadwalkunjunganrmTs
 * @property JantungkoronerT[] $jantungkoronerTs
 * @property KesimpulanmcuT[] $kesimpulanmcuTs
 * @property PasienadmisiT[] $pasienadmisiTs
 * @property PasienanastesiT[] $pasienanastesiTs
 * @property PasienbatalperiksaT[] $pasienbatalperiksaTs
 * @property PasienkirimkeunitlainT[] $pasienkirimkeunitlainTs
 * @property PasienmasukpenunjangT[] $pasienmasukpenunjangTs
 * @property PasienpulangT[] $pasienpulangTs
 * @property PemakaianambulansT[] $pemakaianambulansTs
 * @property PemakaianuangmukaT[] $pemakaianuangmukaTs
 * @property PembayaranpelayananT[] $pembayaranpelayananTs
 * @property PembklaimdetailT[] $pembklaimdetailTs
 * @property AntrianT $antrian
 * @property CarabayarM $carabayar
 * @property CaramasukM $caramasuk
 * @property GolonganumurM $golonganumur
 * @property InstalasiM $instalasi
 * @property JeniskasuspenyakitM $jeniskasuspenyakit
 * @property KarcisM $karcis
 * @property KelaspelayananM $kelaspelayanan
 * @property KelompokumurM $kelompokumur
 * @property PasienM $pasien
 * @property PasienadmisiT $pasienadmisi
 * @property PasienbatalperiksaT $pasienbatalperiksa
 * @property PasienpulangT $pasienpulang
 * @property PegawaiM $pegawai
 * @property PembayaranpelayananT $pembayaranpelayanan
 * @property PeminjamanrmT $peminjamanrm
 * @property PenanggungjawabM $penanggungjawab
 * @property PengirimanrmT $pengirimanrm
 * @property PenjaminM $penjamin
 * @property PersalinanT $persalinan
 * @property RuanganM $ruangan
 * @property RujukanT $rujukan
 * @property ShiftM $shift
 * @property PengirimanrmT[] $pengirimanrmTs
 * @property PenjualanresepT[] $penjualanresepTs
 * @property PermintaanmcuT[] $permintaanmcuTs
 * @property PersalinanT[] $persalinanTs
 * @property PesanambulansT[] $pesanambulansTs
 * @property PindahkamarT[] $pindahkamarTs
 * @property RencanatindakanT[] $rencanatindakanTs
 * @property ReturresepT[] $returresepTs
 * @property TindakanpelayananT[] $tindakanpelayananTs
 */
class Pendaftaran extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pendaftaran_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_pendaftaran', 'tgl_pendaftaran', 'status_periksa', 'status_pasien', 'kunjungan', 'status_masuk'], 'required'],
            [['tgl_pendaftaran', 'tgl_selesaiperiksa', 'tgl_konfirmasi', 'tgl_renkontrol', 'tgl_akandilayani', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['pasienpulang_id', 'pasienbatalperiksa_id', 'penanggungjawab_id', 'penjamin_id', 'shift_id', 'pasien_id', 'persalinan_id', 'pegawai_id', 'instalasi_id', 'caramasuk_id', 'jeniskasuspenyakit_id', 'pembayaranpelayanan_id', 'kelaspelayanan_id', 'carabayar_id', 'pasienadmisi_id', 'golonganumur_id', 'rujukan_id', 'antrian_id', 'karcis_id', 'ruangan_id', 'asuransipasien_id', 'bpjs_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pasienpulang_id', 'pasienbatalperiksa_id', 'penanggungjawab_id', 'penjamin_id', 'shift_id', 'pasien_id', 'persalinan_id', 'pegawai_id', 'instalasi_id', 'caramasuk_id', 'jeniskasuspenyakit_id', 'pembayaranpelayanan_id', 'kelaspelayanan_id', 'carabayar_id', 'pasienadmisi_id', 'golonganumur_id', 'rujukan_id', 'antrian_id', 'karcis_id', 'ruangan_id', 'asuransipasien_id', 'bpjs_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['alih_status', 'by_phone', 'kunjungan_rumah', 'nopendaftaran_aktif', 'status_farmasi', 'panggil_antrian', 'is_deleted', 'is_active'], 'boolean'],
            [['keterangan_pendaftaran', 'additional_data'], 'string'],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['no_urutantri'], 'string', 'max' => 6],
            [['transportasi', 'keadaan_masuk', 'status_periksa', 'status_pasien', 'kunjungan', 'status_masuk', 'status_konfirmasi', 'statusdok_rekammedik'], 'string', 'max' => 50],
            [['umur'], 'string', 'max' => 30],
            [['no_pendaftaran'], 'unique'],
            // [['antrian_id'], 'exist', 'skipOnError' => true, 'targetClass' => AntrianT::className(), 'targetAttribute' => ['antrian_id' => 'antrian_id']],
            // [['carabayar_id'], 'exist', 'skipOnError' => true, 'targetClass' => CarabayarM::className(), 'targetAttribute' => ['carabayar_id' => 'carabayar_id']],
            // [['caramasuk_id'], 'exist', 'skipOnError' => true, 'targetClass' => CaramasukM::className(), 'targetAttribute' => ['caramasuk_id' => 'caramasuk_id']],
            // [['golonganumur_id'], 'exist', 'skipOnError' => true, 'targetClass' => GolonganumurM::className(), 'targetAttribute' => ['golonganumur_id' => 'golonganumur_id']],
            // [['instalasi_id'], 'exist', 'skipOnError' => true, 'targetClass' => InstalasiM::className(), 'targetAttribute' => ['instalasi_id' => 'instalasi_id']],
            // [['jeniskasuspenyakit_id'], 'exist', 'skipOnError' => true, 'targetClass' => JeniskasuspenyakitM::className(), 'targetAttribute' => ['jeniskasuspenyakit_id' => 'jeniskasuspenyakit_id']],
            // [['karcis_id'], 'exist', 'skipOnError' => true, 'targetClass' => KarcisM::className(), 'targetAttribute' => ['karcis_id' => 'karcis_id']],
            // [['kelaspelayanan_id'], 'exist', 'skipOnError' => true, 'targetClass' => KelaspelayananM::className(), 'targetAttribute' => ['kelaspelayanan_id' => 'kelaspelayanan_id']],
            // [['kelompokumur_id'], 'exist', 'skipOnError' => true, 'targetClass' => KelompokumurM::className(), 'targetAttribute' => ['kelompokumur_id' => 'kelompokumur_id']],
            // [['pasien_id'], 'exist', 'skipOnError' => true, 'targetClass' => PasienM::className(), 'targetAttribute' => ['pasien_id' => 'pasien_id']],
            // [['pasienadmisi_id'], 'exist', 'skipOnError' => true, 'targetClass' => PasienadmisiT::className(), 'targetAttribute' => ['pasienadmisi_id' => 'pasienadmisi_id']],
            // [['pasienbatalperiksa_id'], 'exist', 'skipOnError' => true, 'targetClass' => PasienbatalperiksaT::className(), 'targetAttribute' => ['pasienbatalperiksa_id' => 'pasienbatalperiksa_id']],
            // [['pasienpulang_id'], 'exist', 'skipOnError' => true, 'targetClass' => PasienpulangT::className(), 'targetAttribute' => ['pasienpulang_id' => 'pasienpulang_id']],
            // [['pegawai_id'], 'exist', 'skipOnError' => true, 'targetClass' => PegawaiM::className(), 'targetAttribute' => ['pegawai_id' => 'pegawai_id']],
            // [['pembayaranpelayanan_id'], 'exist', 'skipOnError' => true, 'targetClass' => PembayaranpelayananT::className(), 'targetAttribute' => ['pembayaranpelayanan_id' => 'pembayaranpelayanan_id']],
            // [['peminjamanrm_id'], 'exist', 'skipOnError' => true, 'targetClass' => PeminjamanrmT::className(), 'targetAttribute' => ['peminjamanrm_id' => 'peminjamanrm_id']],
            // [['penanggungjawab_id'], 'exist', 'skipOnError' => true, 'targetClass' => PenanggungjawabM::className(), 'targetAttribute' => ['penanggungjawab_id' => 'penanggungjawab_id']],
            // [['pengirimanrm_id'], 'exist', 'skipOnError' => true, 'targetClass' => PengirimanrmT::className(), 'targetAttribute' => ['pengirimanrm_id' => 'pengirimanrm_id']],
            // [['penjamin_id'], 'exist', 'skipOnError' => true, 'targetClass' => PenjaminM::className(), 'targetAttribute' => ['penjamin_id' => 'penjamin_id']],
            // [['persalinan_id'], 'exist', 'skipOnError' => true, 'targetClass' => PersalinanT::className(), 'targetAttribute' => ['persalinan_id' => 'persalinan_id']],
            // [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => RuanganM::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],
            // [['rujukan_id'], 'exist', 'skipOnError' => true, 'targetClass' => RujukanT::className(), 'targetAttribute' => ['rujukan_id' => 'rujukan_id']],
            // [['shift_id'], 'exist', 'skipOnError' => true, 'targetClass' => ShiftM::className(), 'targetAttribute' => ['shift_id' => 'shift_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'no_pendaftaran' => 'No Pendaftaran',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'pasienpulang_id' => 'Pasienpulang ID',
            'pasienbatalperiksa_id' => 'Pasienbatalperiksa ID',
            'penanggungjawab_id' => 'Penanggungjawab ID',
            'penjamin_id' => 'Penjamin ID',
            'shift_id' => 'Shift ID',
            'pasien_id' => 'Pasien ID',
            'persalinan_id' => 'Persalinan ID',
            'pegawai_id' => 'Pegawai ID',
            'instalasi_id' => 'Instalasi ID',
            'caramasuk_id' => 'Caramasuk ID',
            'pengirimanrm_id' => 'Pengirimanrm ID',
            'peminjamanrm_id' => 'Peminjamanrm ID',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'pembayaranpelayanan_id' => 'Pembayaranpelayanan ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'carabayar_id' => 'Carabayar ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'kelompokumur_id' => 'Kelompokumur ID',
            'golonganumur_id' => 'Golonganumur ID',
            'rujukan_id' => 'Rujukan ID',
            'antrian_id' => 'Antrian ID',
            'karcis_id' => 'Karcis ID',
            'ruangan_id' => 'Ruangan ID',
            'no_urutantri' => 'No Urutantri',
            'transportasi' => 'Transportasi',
            'keadaan_masuk' => 'Keadaan Masuk',
            'status_periksa' => 'Status Periksa',
            'status_pasien' => 'Status Pasien',
            'kunjungan' => 'Kunjungan',
            'alih_status' => 'Alih Status',
            'by_phone' => 'By Phone',
            'kunjungan_rumah' => 'Kunjungan Rumah',
            'status_masuk' => 'Status Masuk',
            'umur' => 'Umur',
            'tgl_selesaiperiksa' => 'Tgl Selesaiperiksa',
            'keterangan_pendaftaran' => 'Keterangan Pendaftaran',
            'nopendaftaran_aktif' => 'Nopendaftaran Aktif',
            'status_konfirmasi' => 'Status Konfirmasi',
            'tgl_konfirmasi' => 'Tgl Konfirmasi',
            'tgl_renkontrol' => 'Tgl Renkontrol',
            'status_farmasi' => 'Status Farmasi',
            'panggil_antrian' => 'Panggil Antrian',
            'asuransipasien_id' => 'Asuransipasien ID',
            'tgl_akandilayani' => 'Tgl Akandilayani',
            'statusdok_rekammedik' => 'Statusdok Rekammedik',
            'bpjs_id' => 'Bpjs ID',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAmbiljenazahTs()
    {
        return $this->hasMany(AmbiljenazahT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnamnesaTs()
    {
        return $this->hasMany(AnamnesaT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnamnesadietTs()
    {
        return $this->hasMany(AnamnesadietT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAntrianTs()
    {
        return $this->hasMany(AntrianT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAsuhankeperawatanTs()
    {
        return $this->hasMany(AsuhankeperawatanT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBayaruangmukaTs()
    {
        return $this->hasMany(BayaruangmukaT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBookingkamarTs()
    {
        return $this->hasMany(BookingkamarT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuatjanjipoliTs()
    {
        return $this->hasMany(BuatjanjipoliT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDietpasienTs()
    {
        return $this->hasMany(DietpasienT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHasilmcuTs()
    {
        return $this->hasMany(HasilmcuT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHasilpemeriksaanlabTs()
    {
        return $this->hasMany(HasilpemeriksaanlabT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHasilpemeriksaanmcuTs()
    {
        return $this->hasMany(HasilpemeriksaanmcuT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHasilpemeriksaanradTs()
    {
        return $this->hasMany(HasilpemeriksaanradT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHasilpemeriksaanrmTs()
    {
        return $this->hasMany(HasilpemeriksaanrmT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHearingtestTs()
    {
        return $this->hasMany(HearingtestT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJadwalkunjunganrmTs()
    {
        return $this->hasMany(JadwalkunjunganrmT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJantungkoronerTs()
    {
        return $this->hasMany(JantungkoronerT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKesimpulanmcuTs()
    {
        return $this->hasMany(KesimpulanmcuT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienadmisiTs()
    {
        return $this->hasMany(PasienadmisiT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienanastesiTs()
    {
        return $this->hasMany(PasienanastesiT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienbatalperiksaTs()
    {
        return $this->hasMany(PasienbatalperiksaT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienkirimkeunitlainTs()
    {
        return $this->hasMany(PasienkirimkeunitlainT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienmasukpenunjangTs()
    {
        return $this->hasMany(PasienmasukpenunjangT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienpulangTs()
    {
        return $this->hasMany(PasienpulangT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPemakaianambulansTs()
    {
        return $this->hasMany(PemakaianambulansT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPemakaianuangmukaTs()
    {
        return $this->hasMany(PemakaianuangmukaT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPembayaranpelayananTs()
    {
        return $this->hasMany(PembayaranpelayananT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPembklaimdetailTs()
    {
        return $this->hasMany(PembklaimdetailT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAntrian()
    {
        return $this->hasOne(AntrianT::className(), ['antrian_id' => 'antrian_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCarabayar()
    {
        return $this->hasOne(CarabayarM::className(), ['carabayar_id' => 'carabayar_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaramasuk()
    {
        return $this->hasOne(CaramasukM::className(), ['caramasuk_id' => 'caramasuk_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGolonganumur()
    {
        return $this->hasOne(GolonganumurM::className(), ['golonganumur_id' => 'golonganumur_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstalasi()
    {
        return $this->hasOne(InstalasiM::className(), ['instalasi_id' => 'instalasi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJeniskasuspenyakit()
    {
        return $this->hasOne(JeniskasuspenyakitM::className(), ['jeniskasuspenyakit_id' => 'jeniskasuspenyakit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKarcis()
    {
        return $this->hasOne(KarcisM::className(), ['karcis_id' => 'karcis_id']);
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
    public function getKelompokumur()
    {
        return $this->hasOne(KelompokumurM::className(), ['kelompokumur_id' => 'kelompokumur_id']);
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
    public function getPasienbatalperiksa()
    {
        return $this->hasOne(PasienbatalperiksaT::className(), ['pasienbatalperiksa_id' => 'pasienbatalperiksa_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienpulang()
    {
        return $this->hasOne(PasienpulangT::className(), ['pasienpulang_id' => 'pasienpulang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPegawai()
    {
        return $this->hasOne(PegawaiM::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPembayaranpelayanan()
    {
        return $this->hasOne(PembayaranpelayananT::className(), ['pembayaranpelayanan_id' => 'pembayaranpelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPeminjamanrm()
    {
        return $this->hasOne(PeminjamanrmT::className(), ['peminjamanrm_id' => 'peminjamanrm_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenanggungjawab()
    {
        return $this->hasOne(PenanggungjawabM::className(), ['penanggungjawab_id' => 'penanggungjawab_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPengirimanrm()
    {
        return $this->hasOne(PengirimanrmT::className(), ['pengirimanrm_id' => 'pengirimanrm_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenjamin()
    {
        return $this->hasOne(PenjaminM::className(), ['penjamin_id' => 'penjamin_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPersalinan()
    {
        return $this->hasOne(PersalinanT::className(), ['persalinan_id' => 'persalinan_id']);
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
    public function getRujukan()
    {
        return $this->hasOne(RujukanT::className(), ['rujukan_id' => 'rujukan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShift()
    {
        return $this->hasOne(ShiftM::className(), ['shift_id' => 'shift_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPengirimanrmTs()
    {
        return $this->hasMany(PengirimanrmT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenjualanresepTs()
    {
        return $this->hasMany(PenjualanresepT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermintaanmcuTs()
    {
        return $this->hasMany(PermintaanmcuT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPersalinanTs()
    {
        return $this->hasMany(PersalinanT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPesanambulansTs()
    {
        return $this->hasMany(PesanambulansT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPindahkamarTs()
    {
        return $this->hasMany(PindahkamarT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRencanatindakanTs()
    {
        return $this->hasMany(RencanatindakanT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReturresepTs()
    {
        return $this->hasMany(ReturresepT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTindakanpelayananTs()
    {
        return $this->hasMany(TindakanpelayananT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }
}
