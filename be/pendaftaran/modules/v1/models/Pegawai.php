<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pegawai_m".
 *
 * @property int $pegawai_id
 * @property string $nomorindukpegawai
 * @property string $gelardepan
 * @property string $nama_pegawai
 * @property string $gelarbelakang
 * @property string $jeniskelamin
 * @property string $tempatlahir_pegawai
 * @property string $tgl_lahirpegawai
 * @property string $agama
 * @property string $alamat_pegawai
 * @property int $propinsi_id
 * @property int $kabupaten_id
 * @property int $kecamatan_id
 * @property int $kelurahan_id
 * @property int $suku_id
 * @property string $notelp_pegawai
 * @property string $nomobile_pegawai
 * @property string $alamatemail
 * @property int $pangkat_id
 * @property int $esselon_id
 * @property int $jabatan_id
 * @property string $kelompokjabatan
 * @property int $golonganpegawai_id
 * @property int $jenisjabatan_id
 * @property int $jenjangjabatan_id
 * @property int $pendidikan_id
 * @property int $pendkualifikasi_id
 * @property int $kelompokpegawai_id
 * @property string $kategoripegawai
 * @property int $profilrs_id
 * @property int $pengangkatantphl_id
 * @property string $jenisidentitas
 * @property string $noidentitas
 * @property string $no_kartupegawainegerisipil
 * @property string $no_taspen
 * @property string $no_askes
 * @property string $nama_keluarga
 * @property string $statusperkawinan
 * @property string $warganegara_pegawai
 * @property int $statuskepemilikanrumah_id
 * @property string $golongandarah
 * @property string $rhesus
 * @property double $tinggibadan
 * @property double $beratbadan
 * @property string $warnakulit
 * @property string $nip_lama
 * @property int $loginpemakai_id
 * @property string $photopegawai
 * @property string $nofingerprint
 * @property string $jeniswaktukerja
 * @property string $suratizinpraktek
 * @property string $no_rekening
 * @property string $bank_no_rekening
 * @property string $npwp
 * @property string $tglditerima
 * @property string $tglberhenti
 * @property double $gajipokok
 * @property string $deskripsi
 * @property double $garis_latitude
 * @property double $garis_longitude
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
 * @property AlokasianggaranT[] $alokasianggaranTs
 * @property AlokasianggaranT[] $alokasianggaranTs0
 * @property AnamnesaT[] $anamnesaTs
 * @property AnamnesaT[] $anamnesaTs0
 * @property AnamnesaT[] $anamnesaTs1
 * @property AnamnesadietT[] $anamnesadietTs
 * @property AnamnesadietT[] $anamnesadietTs0
 * @property ApproverenanggpenT[] $approverenanggpenTs
 * @property ApproverenanggpenT[] $approverenanggpenTs0
 * @property ApproverencanaanggaranT[] $approverencanaanggaranTs
 * @property ApproverencanaanggaranT[] $approverencanaanggaranTs0
 * @property ApprrencanggaranT[] $apprrencanggaranTs
 * @property ApprrencanggaranT[] $apprrencanggaranTs0
 * @property AsuhankeperawatanT[] $asuhankeperawatanTs
 * @property BuatjanjipoliT[] $buatjanjipoliTs
 * @property ClosingkasirT[] $closingkasirTs
 * @property DebetnotaT[] $debetnotaTs
 * @property DebetnotaT[] $debetnotaTs0
 * @property DekontaminasiT[] $dekontaminasiTs
 * @property DekontaminasiT[] $dekontaminasiTs0
 * @property DietpasienT[] $dietpasienTs
 * @property DietpasienT[] $dietpasienTs0
 * @property FakturpembelianT[] $fakturpembelianTs
 * @property FakturpembelianT[] $fakturpembelianTs0
 * @property HasilpemeriksaanmcuT[] $hasilpemeriksaanmcuTs
 * @property HasilpemeriksaanrmT[] $hasilpemeriksaanrmTs
 * @property HearingtestT[] $hearingtestTs
 * @property InventarisasibarangT[] $inventarisasibarangTs
 * @property InventarisasibarangT[] $inventarisasibarangTs0
 * @property InventarisasibarangT[] $inventarisasibarangTs1
 * @property InvoicedisposisiT[] $invoicedisposisiTs
 * @property InvoicedisposisiT[] $invoicedisposisiTs0
 * @property InvoicekeluarT[] $invoicekeluarTs
 * @property InvoicekeluarT[] $invoicekeluarTs0
 * @property InvoicekeluarT[] $invoicekeluarTs1
 * @property InvoicemasukT[] $invoicemasukTs
 * @property InvoicemasukT[] $invoicemasukTs0
 * @property InvoicemasukdetailT[] $invoicemasukdetailTs
 * @property InvoicemasukdetsupplierT[] $invoicemasukdetsupplierTs
 * @property InvoicetagihanT[] $invoicetagihanTs
 * @property InvoicetagihanT[] $invoicetagihanTs0
 * @property JadwaldokterM[] $jadwaldokterMs
 * @property JadwalkunjunganrmT[] $jadwalkunjunganrmTs
 * @property KenaikangajiT[] $kenaikangajiTs
 * @property KenaikangajiT[] $kenaikangajiTs0
 * @property KenaikanpangkatT[] $kenaikanpangkatTs
 * @property KenaikanpangkatT[] $kenaikanpangkatTs0
 * @property KesimpulanmcuT[] $kesimpulanmcuTs
 * @property KesimpulanpenilaianT[] $kesimpulanpenilaianTs
 * @property KesimpulanpenilaiandetailT[] $kesimpulanpenilaiandetailTs
 * @property KesimpulanpenilaiandetailT[] $kesimpulanpenilaiandetailTs0
 * @property MasukkamarT[] $masukkamarTs
 * @property MutasibarangT[] $mutasibarangTs
 * @property MutasibarangT[] $mutasibarangTs0
 * @property NofingeralatM[] $nofingeralatMs
 * @property ObatalkesproduksiM[] $obatalkesproduksiMs
 * @property OrganigramM[] $organigramMs
 * @property OtorisasikeuanganT[] $otorisasikeuanganTs
 * @property OtorisasipimpinanT[] $otorisasipimpinanTs
 * @property PasienadmisiT[] $pasienadmisiTs
 * @property PasienanastesiT[] $pasienanastesiTs
 * @property PasienanastesiT[] $pasienanastesiTs0
 * @property PasienkirimkeunitlainT[] $pasienkirimkeunitlainTs
 * @property PasienkirimkeunitlainT[] $pasienkirimkeunitlainTs0
 * @property PasienmasukpenunjangT[] $pasienmasukpenunjangTs
 * @property Esselon $esselon
 * @property JabatanM $jabatan
 * @property JenisJabatan $jenisjabatan
 * @property JenjangJabatan $jenjangjabatan
 * @property Kabupaten $kabupaten
 * @property Kecamatan $kecamatan
 * @property KelompokPegawai $kelompokpegawai
 * @property Kelurahan $kelurahan
 * @property LoginPemakai $loginpemakai
 * @property Pangkat $pangkat
 * @property Pendidikan $pendidikan
 * @property PendidikanKualifikasi $pendkualifikasi
 * @property PengangkatantphlT $pengangkatantphl
 * @property ProfilRumahSakit $profilrs
 * @property Propinsi $propinsi
 * @property StatusKepemilikanRumah $statuskepemilikanrumah
 * @property Suku $suku
 * @property PegawairuanganMp[] $pegawairuanganMps
 * @property RuanganM[] $ruangans
 * @property PemakaianambulansT[] $pemakaianambulansTs
 * @property PemakaianambulansT[] $pemakaianambulansTs0
 * @property PemakaianambulansT[] $pemakaianambulansTs1
 * @property PemakaianambulansT[] $pemakaianambulansTs2
 * @property PembebasantarifT[] $pembebasantarifTs
 * @property PendaftaranT[] $pendaftaranTs
 * @property PenerimaanbarangT[] $penerimaanbarangTs
 * @property PenerimaanbarangT[] $penerimaanbarangTs0
 * @property PenerimaanbarangT[] $penerimaanbarangTs1
 * @property PenerimaansterilisasiT[] $penerimaansterilisasiTs
 * @property PenerimaansterilisasiT[] $penerimaansterilisasiTs0
 * @property PengajuansterlilisasiT[] $pengajuansterlilisasiTs
 * @property PengajuansterlilisasiT[] $pengajuansterlilisasiTs0
 * @property PengangkatantphlT[] $pengangkatantphlTs
 * @property PengeluaranumumT[] $pengeluaranumumTs
 * @property PengirimanrmT[] $pengirimanrmTs
 * @property PengirimanrmT[] $pengirimanrmTs0
 * @property PenjualanresepT[] $penjualanresepTs
 * @property PenjualanresepT[] $penjualanresepTs0
 * @property PermintaanpembelianT[] $permintaanpembelianTs
 * @property PermintaanpembelianT[] $permintaanpembelianTs0
 * @property PermintaanpembelianT[] $permintaanpembelianTs1
 * @property PermintaanpenawaranT[] $permintaanpenawaranTs
 * @property PermintaanpenawaranT[] $permintaanpenawaranTs0
 * @property PermintaanpenawaranT[] $permintaanpenawaranTs1
 * @property PersalinanT[] $persalinanTs
 * @property PersalinanT[] $persalinanTs0
 * @property PersalinanT[] $persalinanTs1
 * @property PersalinanT[] $persalinanTs2
 * @property PersalinanT[] $persalinanTs3
 * @property PesanbarangT[] $pesanbarangTs
 * @property PesanbarangT[] $pesanbarangTs0
 * @property PindahkamarT[] $pindahkamarTs
 * @property RealisasianggpenerimaanT[] $realisasianggpenerimaanTs
 * @property RealisasianggpenerimaanT[] $realisasianggpenerimaanTs0
 * @property ReferensihasilradM[] $referensihasilradMs
 * @property RenanggpenerimaanT[] $renanggpenerimaanTs
 * @property RenanggpenerimaanT[] $renanggpenerimaanTs0
 * @property RencanakebfarmasiT[] $rencanakebfarmasiTs
 * @property RencanakebfarmasiT[] $rencanakebfarmasiTs0
 * @property RencanakebfarmasiT[] $rencanakebfarmasiTs1
 * @property RencanatindakanT[] $rencanatindakanTs
 * @property RencanggaranpengT[] $rencanggaranpengTs
 * @property RencanggaranpengT[] $rencanggaranpengTs0
 * @property ReturpenerimaanT[] $returpenerimaanTs
 * @property ReturpenerimaanT[] $returpenerimaanTs0
 * @property ReturresepT[] $returresepTs
 * @property ReturresepT[] $returresepTs0
 * @property RevisirencanaanggaranT[] $revisirencanaanggaranTs
 * @property RevisirencanggpengT[] $revisirencanggpengTs
 * @property RuanganpegawaiMp[] $ruanganpegawaiMps
 * @property RuanganM[] $ruangans0
 * @property SetorbankT[] $setorbankTs
 * @property SusunankeluargaM[] $susunankeluargaMs
 * @property TandabuktibayarT[] $tandabuktibayarTs
 * @property TandabuktibayarT[] $tandabuktibayarTs0
 * @property TandabuktibayarT[] $tandabuktibayarTs1
 * @property TandabuktibayarT[] $tandabuktibayarTs2
 * @property TandabuktikeluarT[] $tandabuktikeluarTs
 * @property TandabuktikeluarT[] $tandabuktikeluarTs0
 * @property TandabuktikeluarT[] $tandabuktikeluarTs1
 * @property TandabuktikeluarT[] $tandabuktikeluarTs2
 * @property TerimapersediaanT[] $terimapersediaanTs
 * @property TerimapersediaanT[] $terimapersediaanTs0
 * @property VerifikasitagihanT[] $verifikasitagihanTs
 * @property VerifikasitagihanT[] $verifikasitagihanTs0
 * @property VerifrenctindakanT[] $verifrenctindakanTs
 * @property VerifrenctindakanT[] $verifrenctindakanTs0
 */
class Pegawai extends \yii\db\ActiveRecord
{

    public $namaGelarDepan;
    public $namaGelarBelakang;
    /**
     * @inheritdoc
     */
    const KELOMPOK_DOKTER = 1;
    public static function tableName()
    {
        return 'pegawai_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nama_pegawai', 'jeniskelamin', 'agama'], 'required'],
            [['tgl_lahirpegawai', 'tglditerima', 'tglberhenti', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['alamat_pegawai', 'warnakulit', 'deskripsi', 'additional_data'], 'string'],
            [['propinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id', 'suku_id', 'pangkat_id', 'esselon_id', 'jabatan_id', 'golonganpegawai_id', 'jenisjabatan_id', 'jenjangjabatan_id', 'pendidikan_id', 'pendkualifikasi_id', 'kelompokpegawai_id', 'profilrs_id', 'pengangkatantphl_id', 'statuskepemilikanrumah_id', 'loginpemakai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['propinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id', 'suku_id', 'pangkat_id', 'esselon_id', 'jabatan_id', 'golonganpegawai_id', 'jenisjabatan_id', 'jenjangjabatan_id', 'pendidikan_id', 'pendkualifikasi_id', 'kelompokpegawai_id', 'profilrs_id', 'pengangkatantphl_id', 'statuskepemilikanrumah_id', 'loginpemakai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tinggibadan', 'beratbadan', 'gajipokok', 'garis_latitude', 'garis_longitude'], 'number'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['nomorindukpegawai', 'tempatlahir_pegawai', 'kelompokjabatan', 'no_kartupegawainegerisipil', 'no_taspen', 'no_askes'], 'string', 'max' => 30],
            [['gelardepan'], 'string', 'max' => 10],
            [['nama_pegawai', 'notelp_pegawai', 'nomobile_pegawai', 'nama_keluarga', 'npwp'], 'string', 'max' => 50],
            [['gelarbelakang'], 'string', 'max' => 32],
            [['jeniskelamin', 'agama', 'jenisidentitas', 'statusperkawinan', 'rhesus', 'nofingerprint', 'jeniswaktukerja'], 'string', 'max' => 20],
            [['alamatemail', 'noidentitas', 'nip_lama', 'suratizinpraktek', 'no_rekening', 'bank_no_rekening'], 'string', 'max' => 100],
            [['kategoripegawai'], 'string', 'max' => 128],
            [['warganegara_pegawai'], 'string', 'max' => 25],
            [['golongandarah'], 'string', 'max' => 2],
            [['photopegawai'], 'string', 'max' => 200],
            [['esselon_id'], 'exist', 'skipOnError' => true, 'targetClass' => Esselon::className(), 'targetAttribute' => ['esselon_id' => 'esselon_id']],
            [['jabatan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Jabatan::className(), 'targetAttribute' => ['jabatan_id' => 'jabatan_id']],
            [['jenisjabatan_id'], 'exist', 'skipOnError' => true, 'targetClass' => JenisJabatan::className(), 'targetAttribute' => ['jenisjabatan_id' => 'jenisjabatan_id']],
            [['jenjangjabatan_id'], 'exist', 'skipOnError' => true, 'targetClass' => JenjangJabatan::className(), 'targetAttribute' => ['jenjangjabatan_id' => 'jenjangjabatan_id']],
            [['kabupaten_id'], 'exist', 'skipOnError' => true, 'targetClass' => Kabupaten::className(), 'targetAttribute' => ['kabupaten_id' => 'kabupaten_id']],
            [['kecamatan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Kecamatan::className(), 'targetAttribute' => ['kecamatan_id' => 'kecamatan_id']],
            [['kelompokpegawai_id'], 'exist', 'skipOnError' => true, 'targetClass' => KelompokPegawai::className(), 'targetAttribute' => ['kelompokpegawai_id' => 'kelompokpegawai_id']],
            [['kelurahan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Kelurahan::className(), 'targetAttribute' => ['kelurahan_id' => 'kelurahan_id']],
            [['loginpemakai_id'], 'exist', 'skipOnError' => true, 'targetClass' => LoginPemakai::className(), 'targetAttribute' => ['loginpemakai_id' => 'loginpemakai_id']],
            [['pangkat_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pangkat::className(), 'targetAttribute' => ['pangkat_id' => 'pangkat_id']],
            [['pendidikan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pendidikan::className(), 'targetAttribute' => ['pendidikan_id' => 'pendidikan_id']],
            [['pendkualifikasi_id'], 'exist', 'skipOnError' => true, 'targetClass' => PendidikanKualifikasi::className(), 'targetAttribute' => ['pendkualifikasi_id' => 'pendkualifikasi_id']],
            [['pengangkatantphl_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pengangkatantphl::className(), 'targetAttribute' => ['pengangkatantphl_id' => 'pengangkatantphl_id']],
            [['profilrs_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProfilRumahSakit::className(), 'targetAttribute' => ['profilrs_id' => 'profilrs_id']],
            [['propinsi_id'], 'exist', 'skipOnError' => true, 'targetClass' => Propinsi::className(), 'targetAttribute' => ['propinsi_id' => 'propinsi_id']],
            [['statuskepemilikanrumah_id'], 'exist', 'skipOnError' => true, 'targetClass' => StatusKepemilikanRumah::className(), 'targetAttribute' => ['statuskepemilikanrumah_id' => 'statuskepemilikanrumah_id']],
            [['suku_id'], 'exist', 'skipOnError' => true, 'targetClass' => Suku::className(), 'targetAttribute' => ['suku_id' => 'suku_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pegawai_id' => 'Pegawai ID',
            'nomorindukpegawai' => 'Nomorindukpegawai',
            'gelardepan' => 'Gelardepan',
            'nama_pegawai' => 'Nama Pegawai',
            'gelarbelakang' => 'Gelarbelakang',
            'jeniskelamin' => 'Jeniskelamin',
            'tempatlahir_pegawai' => 'Tempatlahir Pegawai',
            'tgl_lahirpegawai' => 'Tgl Lahirpegawai',
            'agama' => 'Agama',
            'alamat_pegawai' => 'Alamat Pegawai',
            'propinsi_id' => 'Propinsi ID',
            'kabupaten_id' => 'Kabupaten ID',
            'kecamatan_id' => 'Kecamatan ID',
            'kelurahan_id' => 'Kelurahan ID',
            'suku_id' => 'Suku ID',
            'notelp_pegawai' => 'Notelp Pegawai',
            'nomobile_pegawai' => 'Nomobile Pegawai',
            'alamatemail' => 'Alamatemail',
            'pangkat_id' => 'Pangkat ID',
            'esselon_id' => 'Esselon ID',
            'jabatan_id' => 'Jabatan ID',
            'kelompokjabatan' => 'Kelompokjabatan',
            'golonganpegawai_id' => 'Golonganpegawai ID',
            'jenisjabatan_id' => 'Jenisjabatan ID',
            'jenjangjabatan_id' => 'Jenjangjabatan ID',
            'pendidikan_id' => 'Pendidikan ID',
            'pendkualifikasi_id' => 'Pendkualifikasi ID',
            'kelompokpegawai_id' => 'Kelompokpegawai ID',
            'kategoripegawai' => 'Kategoripegawai',
            'profilrs_id' => 'Profilrs ID',
            'pengangkatantphl_id' => 'Pengangkatantphl ID',
            'jenisidentitas' => 'Jenisidentitas',
            'noidentitas' => 'Noidentitas',
            'no_kartupegawainegerisipil' => 'No Kartupegawainegerisipil',
            'no_taspen' => 'No Taspen',
            'no_askes' => 'No Askes',
            'nama_keluarga' => 'Nama Keluarga',
            'statusperkawinan' => 'Statusperkawinan',
            'warganegara_pegawai' => 'Warganegara Pegawai',
            'statuskepemilikanrumah_id' => 'Statuskepemilikanrumah ID',
            'golongandarah' => 'Golongandarah',
            'rhesus' => 'Rhesus',
            'tinggibadan' => 'Tinggibadan',
            'beratbadan' => 'Beratbadan',
            'warnakulit' => 'Warnakulit',
            'nip_lama' => 'Nip Lama',
            'loginpemakai_id' => 'Loginpemakai ID',
            'photopegawai' => 'Photopegawai',
            'nofingerprint' => 'Nofingerprint',
            'jeniswaktukerja' => 'Jeniswaktukerja',
            'suratizinpraktek' => 'Suratizinpraktek',
            'no_rekening' => 'No Rekening',
            'bank_no_rekening' => 'Bank No Rekening',
            'npwp' => 'Npwp',
            'tglditerima' => 'Tglditerima',
            'tglberhenti' => 'Tglberhenti',
            'gajipokok' => 'Gajipokok',
            'deskripsi' => 'Deskripsi',
            'garis_latitude' => 'Garis Latitude',
            'garis_longitude' => 'Garis Longitude',
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
    public function getAlokasianggaranTs()
    {
        return $this->hasMany(AlokasianggaranT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlokasianggaranTs0()
    {
        return $this->hasMany(AlokasianggaranT::className(), ['pegawaimenyetujui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnamnesaTs()
    {
        return $this->hasMany(AnamnesaT::className(), ['pegawaidokter_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnamnesaTs0()
    {
        return $this->hasMany(AnamnesaT::className(), ['pegawaiperawat_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnamnesaTs1()
    {
        return $this->hasMany(AnamnesaT::className(), ['pegawaitriase_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnamnesadietTs()
    {
        return $this->hasMany(AnamnesadietT::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnamnesadietTs0()
    {
        return $this->hasMany(AnamnesadietT::className(), ['ahligizi_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApproverenanggpenTs()
    {
        return $this->hasMany(ApproverenanggpenT::className(), ['pegawaimenyetujui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApproverenanggpenTs0()
    {
        return $this->hasMany(ApproverenanggpenT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApproverencanaanggaranTs()
    {
        return $this->hasMany(ApproverencanaanggaranT::className(), ['pegawaimenyetujui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApproverencanaanggaranTs0()
    {
        return $this->hasMany(ApproverencanaanggaranT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApprrencanggaranTs()
    {
        return $this->hasMany(ApprrencanggaranT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApprrencanggaranTs0()
    {
        return $this->hasMany(ApprrencanggaranT::className(), ['pegawaimenyetujui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAsuhankeperawatanTs()
    {
        return $this->hasMany(AsuhankeperawatanT::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuatjanjipoliTs()
    {
        return $this->hasMany(BuatjanjipoliT::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClosingkasirTs()
    {
        return $this->hasMany(ClosingkasirT::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDebetnotaTs()
    {
        return $this->hasMany(DebetnotaT::className(), ['debetnotauntuk_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDebetnotaTs0()
    {
        return $this->hasMany(DebetnotaT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDekontaminasiTs()
    {
        return $this->hasMany(DekontaminasiT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDekontaminasiTs0()
    {
        return $this->hasMany(DekontaminasiT::className(), ['pegawaipetugas_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDietpasienTs()
    {
        return $this->hasMany(DietpasienT::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDietpasienTs0()
    {
        return $this->hasMany(DietpasienT::className(), ['pegawaiahligizi_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFakturpembelianTs()
    {
        return $this->hasMany(FakturpembelianT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFakturpembelianTs0()
    {
        return $this->hasMany(FakturpembelianT::className(), ['pegawaimenyetujui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHasilpemeriksaanmcuTs()
    {
        return $this->hasMany(HasilpemeriksaanmcuT::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHasilpemeriksaanrmTs()
    {
        return $this->hasMany(HasilpemeriksaanrmT::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHearingtestTs()
    {
        return $this->hasMany(HearingtestT::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInventarisasibarangTs()
    {
        return $this->hasMany(InventarisasibarangT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInventarisasibarangTs0()
    {
        return $this->hasMany(InventarisasibarangT::className(), ['petugas1_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInventarisasibarangTs1()
    {
        return $this->hasMany(InventarisasibarangT::className(), ['petugas2_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoicedisposisiTs()
    {
        return $this->hasMany(InvoicedisposisiT::className(), ['pegawaipengirim_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoicedisposisiTs0()
    {
        return $this->hasMany(InvoicedisposisiT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoicekeluarTs()
    {
        return $this->hasMany(InvoicekeluarT::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoicekeluarTs0()
    {
        return $this->hasMany(InvoicekeluarT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoicekeluarTs1()
    {
        return $this->hasMany(InvoicekeluarT::className(), ['pegawaimenyetujui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoicemasukTs()
    {
        return $this->hasMany(InvoicemasukT::className(), ['pegawaimenyetujui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoicemasukTs0()
    {
        return $this->hasMany(InvoicemasukT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoicemasukdetailTs()
    {
        return $this->hasMany(InvoicemasukdetailT::className(), ['pasienpegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoicemasukdetsupplierTs()
    {
        return $this->hasMany(InvoicemasukdetsupplierT::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoicetagihanTs()
    {
        return $this->hasMany(InvoicetagihanT::className(), ['pegawaiverifikasi_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoicetagihanTs0()
    {
        return $this->hasMany(InvoicetagihanT::className(), ['pegawaimenyetujui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJadwaldokterMs()
    {
        return $this->hasMany(JadwaldokterM::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJadwalkunjunganrmTs()
    {
        return $this->hasMany(JadwalkunjunganrmT::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKenaikangajiTs()
    {
        return $this->hasMany(KenaikangajiT::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKenaikangajiTs0()
    {
        return $this->hasMany(KenaikangajiT::className(), ['pegawaipimpinan_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKenaikanpangkatTs()
    {
        return $this->hasMany(KenaikanpangkatT::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKenaikanpangkatTs0()
    {
        return $this->hasMany(KenaikanpangkatT::className(), ['pegawaipimpinan_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKesimpulanmcuTs()
    {
        return $this->hasMany(KesimpulanmcuT::className(), ['pegawaipemeriksa_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKesimpulanpenilaianTs()
    {
        return $this->hasMany(KesimpulanpenilaianT::className(), ['pegawai_pemberisaran' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKesimpulanpenilaiandetailTs()
    {
        return $this->hasMany(KesimpulanpenilaiandetailT::className(), ['penilaianpegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKesimpulanpenilaiandetailTs0()
    {
        return $this->hasMany(KesimpulanpenilaiandetailT::className(), ['pegawaipenilai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMasukkamarTs()
    {
        return $this->hasMany(MasukkamarT::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMutasibarangTs()
    {
        return $this->hasMany(MutasibarangT::className(), ['pegawaipengirim_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMutasibarangTs0()
    {
        return $this->hasMany(MutasibarangT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNofingeralatMs()
    {
        return $this->hasMany(NofingeralatM::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getObatalkesproduksiMs()
    {
        return $this->hasMany(ObatalkesproduksiM::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganigramMs()
    {
        return $this->hasMany(OrganigramM::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOtorisasikeuanganTs()
    {
        return $this->hasMany(OtorisasikeuanganT::className(), ['otorisasioleh_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOtorisasipimpinanTs()
    {
        return $this->hasMany(OtorisasipimpinanT::className(), ['otorisasioleh_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienadmisiTs()
    {
        return $this->hasMany(PasienadmisiT::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienanastesiTs()
    {
        return $this->hasMany(PasienanastesiT::className(), ['dokteranastesi_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienanastesiTs0()
    {
        return $this->hasMany(PasienanastesiT::className(), ['perawatanastesi_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienkirimkeunitlainTs()
    {
        return $this->hasMany(PasienkirimkeunitlainT::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienkirimkeunitlainTs0()
    {
        return $this->hasMany(PasienkirimkeunitlainT::className(), ['ahligizipegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienmasukpenunjangTs()
    {
        return $this->hasMany(PasienmasukpenunjangT::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEsselon()
    {
        return $this->hasOne(Esselon::className(), ['esselon_id' => 'esselon_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJabatan()
    {
        return $this->hasOne(Jabatan::className(), ['jabatan_id' => 'jabatan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJenisjabatan()
    {
        return $this->hasOne(JenisJabatan::className(), ['jenisjabatan_id' => 'jenisjabatan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJenjangjabatan()
    {
        return $this->hasOne(JenjangJabatan::className(), ['jenjangjabatan_id' => 'jenjangjabatan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKabupaten()
    {
        return $this->hasOne(Kabupaten::className(), ['kabupaten_id' => 'kabupaten_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKecamatan()
    {
        return $this->hasOne(Kecamatan::className(), ['kecamatan_id' => 'kecamatan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKelompokpegawai()
    {
        return $this->hasOne(KelompokPegawai::className(), ['kelompokpegawai_id' => 'kelompokpegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKelurahan()
    {
        return $this->hasOne(Kelurahan::className(), ['kelurahan_id' => 'kelurahan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLoginpemakai()
    {
        return $this->hasOne(Loginpemakai::className(), ['loginpemakai_id' => 'loginpemakai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPangkat()
    {
        return $this->hasOne(Pangkat::className(), ['pangkat_id' => 'pangkat_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendidikan()
    {
        return $this->hasOne(Pendidikan::className(), ['pendidikan_id' => 'pendidikan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendkualifikasi()
    {
        return $this->hasOne(PendidikanKualifikasi::className(), ['pendkualifikasi_id' => 'pendkualifikasi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPengangkatantphl()
    {
        return $this->hasOne(Pengangkatantphl::className(), ['pengangkatantphl_id' => 'pengangkatantphl_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfilrs()
    {
        return $this->hasOne(ProfilRumahSakit::className(), ['profilrs_id' => 'profilrs_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPropinsi()
    {
        return $this->hasOne(Propinsi::className(), ['propinsi_id' => 'propinsi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatuskepemilikanrumah()
    {
        return $this->hasOne(StatusKepemilikanRumah::className(), ['statuskepemilikanrumah_id' => 'statuskepemilikanrumah_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSuku()
    {
        return $this->hasOne(Suku::className(), ['suku_id' => 'suku_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPegawairuanganMps()
    {
        return $this->hasMany(PegawairuanganMp::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuangans()
    {
        return $this->hasMany(RuanganM::className(), ['ruangan_id' => 'ruangan_id'])->viaTable('pegawairuangan_mp', ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPemakaianambulansTs()
    {
        return $this->hasMany(PemakaianambulansT::className(), ['supir_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPemakaianambulansTs0()
    {
        return $this->hasMany(PemakaianambulansT::className(), ['pelaksana_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPemakaianambulansTs1()
    {
        return $this->hasMany(PemakaianambulansT::className(), ['perawat1_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPemakaianambulansTs2()
    {
        return $this->hasMany(PemakaianambulansT::className(), ['perawat2_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPembebasantarifTs()
    {
        return $this->hasMany(PembebasantarifT::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendaftaranTs()
    {
        return $this->hasMany(PendaftaranT::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenerimaanbarangTs()
    {
        return $this->hasMany(PenerimaanbarangT::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenerimaanbarangTs0()
    {
        return $this->hasMany(PenerimaanbarangT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenerimaanbarangTs1()
    {
        return $this->hasMany(PenerimaanbarangT::className(), ['pegawaimenyetujui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenerimaansterilisasiTs()
    {
        return $this->hasMany(PenerimaansterilisasiT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenerimaansterilisasiTs0()
    {
        return $this->hasMany(PenerimaansterilisasiT::className(), ['pegawaipenerima_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPengajuansterlilisasiTs()
    {
        return $this->hasMany(PengajuansterlilisasiT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPengajuansterlilisasiTs0()
    {
        return $this->hasMany(PengajuansterlilisasiT::className(), ['pegawaipengajuan_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPengangkatantphlTs()
    {
        return $this->hasMany(PengangkatantphlT::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPengeluaranumumTs()
    {
        return $this->hasMany(PengeluaranumumT::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPengirimanrmTs()
    {
        return $this->hasMany(PengirimanrmT::className(), ['petugaspengirim_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPengirimanrmTs0()
    {
        return $this->hasMany(PengirimanrmT::className(), ['petugaspenerima_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenjualanresepTs()
    {
        return $this->hasMany(PenjualanresepT::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenjualanresepTs0()
    {
        return $this->hasMany(PenjualanresepT::className(), ['penjpasienpegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermintaanpembelianTs()
    {
        return $this->hasMany(PermintaanpembelianT::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermintaanpembelianTs0()
    {
        return $this->hasMany(PermintaanpembelianT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermintaanpembelianTs1()
    {
        return $this->hasMany(PermintaanpembelianT::className(), ['pegawaimenyetujui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermintaanpenawaranTs()
    {
        return $this->hasMany(PermintaanpenawaranT::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermintaanpenawaranTs0()
    {
        return $this->hasMany(PermintaanpenawaranT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermintaanpenawaranTs1()
    {
        return $this->hasMany(PermintaanpenawaranT::className(), ['pegawaimenyetujui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPersalinanTs()
    {
        return $this->hasMany(PersalinanT::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPersalinanTs0()
    {
        return $this->hasMany(PersalinanT::className(), ['bidan1_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPersalinanTs1()
    {
        return $this->hasMany(PersalinanT::className(), ['bidan2_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPersalinanTs2()
    {
        return $this->hasMany(PersalinanT::className(), ['perawat1_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPersalinanTs3()
    {
        return $this->hasMany(PersalinanT::className(), ['perawat2_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPesanbarangTs()
    {
        return $this->hasMany(PesanbarangT::className(), ['pegawaipemesan_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPesanbarangTs0()
    {
        return $this->hasMany(PesanbarangT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPindahkamarTs()
    {
        return $this->hasMany(PindahkamarT::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRealisasianggpenerimaanTs()
    {
        return $this->hasMany(RealisasianggpenerimaanT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRealisasianggpenerimaanTs0()
    {
        return $this->hasMany(RealisasianggpenerimaanT::className(), ['pegawaimenyetujui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReferensihasilradMs()
    {
        return $this->hasMany(ReferensihasilradM::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRenanggpenerimaanTs()
    {
        return $this->hasMany(RenanggpenerimaanT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRenanggpenerimaanTs0()
    {
        return $this->hasMany(RenanggpenerimaanT::className(), ['pegawaimenyetujui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRencanakebfarmasiTs()
    {
        return $this->hasMany(RencanakebfarmasiT::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRencanakebfarmasiTs0()
    {
        return $this->hasMany(RencanakebfarmasiT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRencanakebfarmasiTs1()
    {
        return $this->hasMany(RencanakebfarmasiT::className(), ['pegawaimenyetujui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRencanatindakanTs()
    {
        return $this->hasMany(RencanatindakanT::className(), ['pegawaiperencana_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRencanggaranpengTs()
    {
        return $this->hasMany(RencanggaranpengT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRencanggaranpengTs0()
    {
        return $this->hasMany(RencanggaranpengT::className(), ['pegawaimenyetujui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReturpenerimaanTs()
    {
        return $this->hasMany(ReturpenerimaanT::className(), ['pegawairetur_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReturpenerimaanTs0()
    {
        return $this->hasMany(ReturpenerimaanT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReturresepTs()
    {
        return $this->hasMany(ReturresepT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReturresepTs0()
    {
        return $this->hasMany(ReturresepT::className(), ['pegawairetur_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRevisirencanaanggaranTs()
    {
        return $this->hasMany(RevisirencanaanggaranT::className(), ['ygmerevisi_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRevisirencanggpengTs()
    {
        return $this->hasMany(RevisirencanggpengT::className(), ['ygmerevisi_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuanganpegawaiMps()
    {
        return $this->hasMany(RuanganpegawaiMp::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuangans0()
    {
        return $this->hasMany(RuanganM::className(), ['ruangan_id' => 'ruangan_id'])->viaTable('ruanganpegawai_mp', ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSetorbankTs()
    {
        return $this->hasMany(SetorbankT::className(), ['pegawaimenyetor_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSusunankeluargaMs()
    {
        return $this->hasMany(SusunankeluargaM::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTandabuktibayarTs()
    {
        return $this->hasMany(TandabuktibayarT::className(), ['pegawai1_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTandabuktibayarTs0()
    {
        return $this->hasMany(TandabuktibayarT::className(), ['pegawai2_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTandabuktibayarTs1()
    {
        return $this->hasMany(TandabuktibayarT::className(), ['pegawai3_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTandabuktibayarTs2()
    {
        return $this->hasMany(TandabuktibayarT::className(), ['pegawai4_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTandabuktikeluarTs()
    {
        return $this->hasMany(TandabuktikeluarT::className(), ['pegawai1_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTandabuktikeluarTs0()
    {
        return $this->hasMany(TandabuktikeluarT::className(), ['pegawai2_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTandabuktikeluarTs1()
    {
        return $this->hasMany(TandabuktikeluarT::className(), ['pegawai3_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTandabuktikeluarTs2()
    {
        return $this->hasMany(TandabuktikeluarT::className(), ['pegawai4_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTerimapersediaanTs()
    {
        return $this->hasMany(TerimapersediaanT::className(), ['pegawaimenyetujui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTerimapersediaanTs0()
    {
        return $this->hasMany(TerimapersediaanT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVerifikasitagihanTs()
    {
        return $this->hasMany(VerifikasitagihanT::className(), ['verifikasioleh_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVerifikasitagihanTs0()
    {
        return $this->hasMany(VerifikasitagihanT::className(), ['mengetahuioleh_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVerifrenctindakanTs()
    {
        return $this->hasMany(VerifrenctindakanT::className(), ['petugasverifikasi_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVerifrenctindakanTs0()
    {
        return $this->hasMany(VerifrenctindakanT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    }


    public function attributes()
    {
        $list = parent::attributes();
        $list[] = 'namaGelarDepan';
        return $list;
    }

    public function afterFind()
    {
        parent::afterFind();
        // $this->namaGelarDepan = $this->getnamaGelarDepan();
        // $this->namaGelarBelakang = $this->getnamaGelarBelakang();
    }

    public function getnamaGelarDepan()
    {
        $listGelar = explode(' ', $this->gelardepan);
        $listNamaGelar = [];
        foreach ($listGelar as $gelar) {
            $gelar = trim($gelar);

            $lookup = Lookup::findOne($gelar);
            $listNamaGelar[] = $lookup->lookup_name;
        }

        return implode(' ', $listNamaGelar);
    }

    public function getnamaGelarBelakang()
    {
        $listGelar = explode(' ', $this->gelarbelakang);
        $listNamaGelar = [];
        foreach ($listGelar as $gelar) {
            $gelar = trim($gelar);

            $lookup = Lookup::findOne($gelar);
            $listNamaGelar[] = $lookup->lookup_name;
        }

        return implode(' ', $listNamaGelar);
    }

    public static function signatureEmployee($column, $value)
    {
        $urlFe = Yii::$app->urlManagerFrontend->createUrl('');
        $record = self::find()->select(['pegawai_id', 'tanda_tangan'])->andWhere([$column => $value])->asArray()->one();
        $signaturePath = !empty($record) ? $record['tanda_tangan'] : null;
        return !empty($signaturePath) ? '<img style="max-width: 85px; max-height: 85px;" src="'.$urlFe.'uploads/signature/' . $signaturePath . '">' : '<br><br>';
    }
}
