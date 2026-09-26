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
 * @property int $jabatan_id
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
* @property string $golongandarah
 * @property string $rhesus
 * @property double $tinggibadan
 * @property double $beratbadan
 * @property string $warna_kulit
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
 * @property string $dokter_id
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
 * @property FakturpenerimaanobatT[] $fakturpenerimaanobatTs
 * @property FakturpenerimaanobatT[] $fakturpenerimaanobatTs0
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
 * @property EsselonM $esselon
 * @property JabatanM $jabatan
 * @property JenisjabatanM $jenisjabatan
 * @property JenjangjabatanM $jenjangjabatan
 * @property KabupatenM $kabupaten
 * @property KecamatanM $kecamatan
 * @property KelompokpegawaiM $kelompokpegawai
 * @property KelurahanM $kelurahan
 * @property LoginpemakaiK $loginpemakai
 * @property PangkatM $pangkat
 * @property PendidikanM $pendidikan
 * @property PendidikankualifikasiM $pendkualifikasi
 * @property PengangkatantphlT $pengangkatantphl
 * @property ProfilrumahsakitM $profilrs
 * @property PropinsiM $propinsi
 * @property StatuskepemilikanrumahM $statuskepemilikanrumah
 * @property SukuM $suku
 * @property PemakaianambulansT[] $pemakaianambulansTs
 * @property PemakaianambulansT[] $pemakaianambulansTs0
 * @property PemakaianambulansT[] $pemakaianambulansTs1
 * @property PemakaianambulansT[] $pemakaianambulansTs2
 * @property PembelianobatT[] $pembelianobatTs
 * @property PembelianobatT[] $pembelianobatTs0
 * @property PembelianobatT[] $pembelianobatTs1
 * @property PenawaranobatT[] $penawaranobatTs
 * @property PenawaranobatT[] $penawaranobatTs0
 * @property PenawaranobatT[] $penawaranobatTs1
 * @property PendaftaranT[] $pendaftaranTs
 * @property PenerimaanobatT[] $penerimaanobatTs
 * @property PenerimaanobatT[] $penerimaanobatTs0
 * @property PenerimaanobatT[] $penerimaanobatTs1
 * @property PenerimaansterilisasiT[] $penerimaansterilisasiTs
 * @property PenerimaansterilisasiT[] $penerimaansterilisasiTs0
 * @property PengajuansterlilisasiT[] $pengajuansterlilisasiTs
 * @property PengajuansterlilisasiT[] $pengajuansterlilisasiTs0
 * @property PengangkatantphlT[] $pengangkatantphlTs
 * @property PengeluaranumumT[] $pengeluaranumumTs
 * @property PenjualanresepT[] $penjualanresepTs
 * @property PenjualanresepT[] $penjualanresepTs0
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
 * @property RencanakebutuhanobatT[] $rencanakebutuhanobatTs
 * @property RencanakebutuhanobatT[] $rencanakebutuhanobatTs0
 * @property RencanakebutuhanobatT[] $rencanakebutuhanobatTs1
 * @property RencanatindakanT[] $rencanatindakanTs
 * @property RencanggaranpengT[] $rencanggaranpengTs
 * @property RencanggaranpengT[] $rencanggaranpengTs0
 * @property ReturpenerimaanbarangT[] $returpenerimaanbarangTs
 * @property ReturpenerimaanbarangT[] $returpenerimaanbarangTs0
 * @property ReturpenerimaanobatT[] $returpenerimaanobatTs
 * @property ReturpenerimaanobatT[] $returpenerimaanobatTs0
 * @property ReturresepT[] $returresepTs
 * @property ReturresepT[] $returresepTs0
 * @property RevisirencanaanggaranT[] $revisirencanaanggaranTs
 * @property RevisirencanggpengT[] $revisirencanggpengTs
 * @property RuanganpegawaiMp[] $ruanganpegawaiMps
 * @property RuanganM[] $ruangans
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
    const KELOMPOK_DOKTER = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pegawai_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tgl_lahirpegawai', 'tglditerima', 'tglberhenti', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['alamat_pegawai', 'warna_kulit', 'additional_data'], 'string'],
            [['propinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id', 'suku_id', 'pangkat_id', 'jabatan_id', 'golonganpegawai_id', 'jenisjabatan_id', 'jenjangjabatan_id', 'pendidikan_id', 'pendkualifikasi_id', 'kelompokpegawai_id', 'profilrs_id','loginpemakai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['propinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id', 'suku_id', 'pangkat_id', 'jabatan_id', 'golonganpegawai_id', 'jenisjabatan_id', 'jenjangjabatan_id', 'pendidikan_id', 'pendkualifikasi_id', 'kelompokpegawai_id', 'profilrs_id','loginpemakai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tinggibadan', 'beratbadan'], 'number'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['nomorindukpegawai', 'tempatlahir_pegawai'], 'string', 'max' => 30],
            [['gelardepan'], 'string', 'max' => 10],
            [['nama_pegawai', 'notelp_pegawai', 'nomobile_pegawai', 'npwp'], 'string', 'max' => 150],
            [['gelarbelakang'], 'string', 'max' => 32],
            [['jeniskelamin', 'agama', 'jenisidentitas'], 'string', 'max' => 20],
            [['alamatemail', 'noidentitas', 'suratizinpraktek', 'no_rekening', 'dokter_id'], 'string', 'max' => 100],
            [['warganegara_pegawai'], 'string', 'max' => 25],
            [['photopegawai'], 'string', 'max' => 200],
            [['nomorindukpegawai'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
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
            'jabatan_id' => 'Jabatan ID',
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
            'golongandarah' => 'Golongandarah',
            'rhesus' => 'Rhesus',
            'tinggibadan' => 'Tinggibadan',
            'beratbadan' => 'Beratbadan',
            'warna_kulit' => 'warna_kulit',
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

}
