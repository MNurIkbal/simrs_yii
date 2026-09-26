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
class Pegawai extends \Doco\components\DocoActiveRecord
{
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

    public function getKelompokPegawai()
    {
        return $this->hasOne(KelompokPegawai::className(), ['kelompokpegawai_id' => 'kelompokpegawai_id']);
    }
}
