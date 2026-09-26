<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ruangan_m".
 *
 * @property int $ruangan_id
 * @property int $instalasi_id
 * @property string $ruangan_nama
 * @property string $ruangan_namalainnya
 * @property string $ruangan_jenispelayanan
 * @property string $ruangan_singkatan
 * @property string $ruangan_fasilitas
 * @property string $ruangan_lokasi
 * @property string $ruangan_image
 * @property int $ruangan_urutan
 * @property string $ruangan_filesuara
 * @property int $estimasipelayanan
 * @property string $image_mobile
 * @property int $warnadokrm_id
 * @property string $kode_ruanganpoli
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
 * @property int $unitkerja_id
 *
 * @property AmbiljenazahT[] $ambiljenazahTs
 * @property AmbiljenazahT[] $ambiljenazahTs0
 * @property AnamnesadietT[] $anamnesadietTs
 * @property AntrianT[] $antrianTs
 * @property AntrianfarmasiT[] $antrianfarmasiTs
 * @property AsuhankeperawatanT[] $asuhankeperawatanTs
 * @property BatalbayarsupplierT[] $batalbayarsupplierTs
 * @property BatalkeluarumumT[] $batalkeluarumumTs
 * @property BatalpakaiambulansT[] $batalpakaiambulansTs
 * @property BayaruangmukaT[] $bayaruangmukaTs
 * @property BesaranjasadetailT[] $besaranjasadetailTs
 * @property BookingkamarT[] $bookingkamarTs
 * @property BuatjanjipoliT[] $buatjanjipoliTs
 * @property BukubesarT[] $bukubesarTs
 * @property DekontaminasidetailT[] $dekontaminasidetailTs
 * @property DietpasienT[] $dietpasienTs
 * @property FakturpembelianT[] $fakturpembelianTs
 * @property FormasishiftM[] $formasishiftMs
 * @property HasilmcuT[] $hasilmcuTs
 * @property HasilpemeriksaanmcuT[] $hasilpemeriksaanmcuTs
 * @property HasilpemeriksaanrmT[] $hasilpemeriksaanrmTs
 * @property HearingtestT[] $hearingtestTs
 * @property InventarisasibarangT[] $inventarisasibarangTs
 * @property InventarisasiruanganT[] $inventarisasiruanganTs
 * @property InvoicedisposisiT[] $invoicedisposisiTs
 * @property InvoicedisposisiT[] $invoicedisposisiTs0
 * @property InvoicemasukT[] $invoicemasukTs
 * @property InvoicetagihanT[] $invoicetagihanTs
 * @property JadwalbukapoliM[] $jadwalbukapoliMs
 * @property JadwaldokterM[] $jadwaldokterMs
 * @property KamarruanganM[] $kamarruanganMs
 * @property KarcisM[] $karcisMs
 * @property KasuspenyakitruanganMp[] $kasuspenyakitruanganMps
 * @property JeniskasuspenyakitM[] $jeniskasuspenyakits
 * @property KelahiranbayiT[] $kelahiranbayiTs
 * @property KelasruanganMp[] $kelasruanganMps
 * @property KelaspelayananM[] $kelaspelayanans
 * @property KesimpulanmcuT[] $kesimpulanmcuTs
 * @property KomponenjasaM[] $komponenjasaMs
 * @property LayarruanganM[] $layarruanganMs
 * @property LayarantrianM[] $layarantrians
 * @property LinenM[] $linenMs
 * @property LokasiasetM[] $lokasiasetMs
 * @property MasukkamarT[] $masukkamarTs
 * @property PaketpelayananM[] $paketpelayananMs
 * @property PasienadmisiT[] $pasienadmisiTs
 * @property PasienkirimkeunitlainT[] $pasienkirimkeunitlainTs
 * @property PasienmasukpenunjangT[] $pasienmasukpenunjangTs
 * @property PasienmasukpenunjangT[] $pasienmasukpenunjangTs0
 * @property PasienpulangT[] $pasienpulangTs
 * @property PegawairuanganMp[] $pegawairuanganMps
 * @property PegawaiM[] $pegawais
 * @property PelayananrekeningM[] $pelayananrekeningMs
 * @property PemakaianambulansT[] $pemakaianambulansTs
 * @property PembatalanuangmukaT[] $pembatalanuangmukaTs
 * @property PembayaranpelayananT[] $pembayaranpelayananTs
 * @property PembayaranpelayananT[] $pembayaranpelayananTs0
 * @property PendaftaranT[] $pendaftaranTs
 * @property PenerimaanbarangT[] $penerimaanbarangTs
 * @property PenerimaansterilisasiT[] $penerimaansterilisasiTs
 * @property PenerimaanumumT[] $penerimaanumumTs
 * @property PengajuansterlilisasiT[] $pengajuansterlilisasiTs
 * @property PengirimanrmT[] $pengirimanrmTs
 * @property PengirimanrmT[] $pengirimanrmTs0
 * @property PengirimanrmT[] $pengirimanrmTs1
 * @property PenjualanresepT[] $penjualanresepTs
 * @property PenjualanresepT[] $penjualanresepTs0
 * @property PermintaanmcuT[] $permintaanmcuTs
 * @property PermintaanpembelianT[] $permintaanpembelianTs
 * @property PersalinanT[] $persalinanTs
 * @property PesanambulansT[] $pesanambulansTs
 * @property PesanbarangT[] $pesanbarangTs
 * @property PindahkamarT[] $pindahkamarTs
 * @property RekeninguangmukaM[] $rekeninguangmukaMs
 * @property RenanggpenerimaanT[] $renanggpenerimaanTs
 * @property RencanakebfarmasiT[] $rencanakebfarmasiTs
 * @property RencanatindakanT[] $rencanatindakanTs
 * @property ReturbayarpelayananT[] $returbayarpelayananTs
 * @property ReturpembelianT[] $returpembelianTs
 * @property ReturpenerimaanumumT[] $returpenerimaanumumTs
 * @property ReturresepT[] $returresepTs
 * @property InstalasiM $instalasi
 * @property UnitkerjaM $unitkerja
 * @property RuanganpegawaiMp[] $ruanganpegawaiMps
 * @property PegawaiM[] $pegawais0
 * @property RuanganpemakaiK[] $ruanganpemakaiKs
 * @property LoginpemakaiK[] $loginpemakais
 * @property TandabuktibayarT[] $tandabuktibayarTs
 * @property TandabuktikeluarT[] $tandabuktikeluarTs
 * @property TerimapersediaanT[] $terimapersediaanTs
 * @property TindakanpelayananT[] $tindakanpelayananTs
 * @property TindakanruanganMp[] $tindakanruanganMps
 * @property DaftartindakanM[] $daftartindakans
 * @property TindakansudahbayarT[] $tindakansudahbayarTs
 * @property UnitkerjaruanganMp[] $unitkerjaruanganMps
 * @property UnitkerjaM[] $unitkerjas
 */
class Ruangan extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ruangan_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['instalasi_id', 'ruangan_urutan', 'estimasipelayanan', 'warnadokrm_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'unitkerja_id'], 'default', 'value' => null],
            [['instalasi_id', 'ruangan_urutan', 'estimasipelayanan', 'warnadokrm_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'unitkerja_id'], 'integer'],
            [['ruangan_nama', 'unitkerja_id'], 'required'],
            [['ruangan_fasilitas', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['ruangan_nama', 'ruangan_namalainnya', 'ruangan_jenispelayanan', 'ruangan_lokasi'], 'string', 'max' => 50],
            [['ruangan_singkatan', 'kode_ruanganpoli'], 'string', 'max' => 3],
            [['ruangan_image'], 'string', 'max' => 100],
            [['ruangan_filesuara', 'image_mobile'], 'string', 'max' => 500],
            [['instalasi_id'], 'exist', 'skipOnError' => true, 'targetClass' => Instalasi::className(), 'targetAttribute' => ['instalasi_id' => 'instalasi_id']],
            // [['unitkerja_id'], 'exist', 'skipOnError' => true, 'targetClass' => UnitkerjaM::className(), 'targetAttribute' => ['unitkerja_id' => 'unitkerja_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ruangan_id' => 'Ruangan ID',
            'instalasi_id' => 'Instalasi ID',
            'ruangan_nama' => 'Ruangan Nama',
            'ruangan_namalainnya' => 'Ruangan Namalainnya',
            'ruangan_jenispelayanan' => 'Ruangan Jenispelayanan',
            'ruangan_singkatan' => 'Ruangan Singkatan',
            'ruangan_fasilitas' => 'Ruangan Fasilitas',
            'ruangan_lokasi' => 'Ruangan Lokasi',
            'ruangan_image' => 'Ruangan Image',
            'ruangan_urutan' => 'Ruangan Urutan',
            'ruangan_filesuara' => 'Ruangan Filesuara',
            'estimasipelayanan' => 'Estimasipelayanan',
            'image_mobile' => 'Image Mobile',
            'warnadokrm_id' => 'Warnadokrm ID',
            'kode_ruanganpoli' => 'Kode Ruanganpoli',
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
            'unitkerja_id' => 'Unitkerja ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getAmbiljenazahTs()
    // {
    //     return $this->hasMany(AmbiljenazahT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getAmbiljenazahTs0()
    // {
    //     return $this->hasMany(AmbiljenazahT::className(), ['ruanganmeninggal_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getAnamnesadietTs()
    // {
    //     return $this->hasMany(AnamnesadietT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getAntrianTs()
    // {
    //     return $this->hasMany(AntrianT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getAntrianfarmasiTs()
    // {
    //     return $this->hasMany(AntrianfarmasiT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getAsuhankeperawatanTs()
    // {
    //     return $this->hasMany(AsuhankeperawatanT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getBatalbayarsupplierTs()
    // {
    //     return $this->hasMany(BatalbayarsupplierT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getBatalkeluarumumTs()
    // {
    //     return $this->hasMany(BatalkeluarumumT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getBatalpakaiambulansTs()
    // {
    //     return $this->hasMany(BatalpakaiambulansT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getBayaruangmukaTs()
    // {
    //     return $this->hasMany(BayaruangmukaT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getBesaranjasadetailTs()
    // {
    //     return $this->hasMany(BesaranjasadetailT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getBookingkamarTs()
    // {
    //     return $this->hasMany(BookingkamarT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getBuatjanjipoliTs()
    // {
    //     return $this->hasMany(BuatjanjipoliT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getBukubesarTs()
    // {
    //     return $this->hasMany(BukubesarT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getDekontaminasidetailTs()
    // {
    //     return $this->hasMany(DekontaminasidetailT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getDietpasienTs()
    // {
    //     return $this->hasMany(DietpasienT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getFakturpembelianTs()
    // {
    //     return $this->hasMany(FakturpembelianT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getFormasishiftMs()
    // {
    //     return $this->hasMany(FormasishiftM::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getHasilmcuTs()
    // {
    //     return $this->hasMany(HasilmcuT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getHasilpemeriksaanmcuTs()
    // {
    //     return $this->hasMany(HasilpemeriksaanmcuT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getHasilpemeriksaanrmTs()
    // {
    //     return $this->hasMany(HasilpemeriksaanrmT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getHearingtestTs()
    // {
    //     return $this->hasMany(HearingtestT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getInventarisasibarangTs()
    // {
    //     return $this->hasMany(InventarisasibarangT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getInventarisasiruanganTs()
    // {
    //     return $this->hasMany(InventarisasiruanganT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getInvoicedisposisiTs()
    // {
    //     return $this->hasMany(InvoicedisposisiT::className(), ['ruangantujuan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getInvoicedisposisiTs0()
    // {
    //     return $this->hasMany(InvoicedisposisiT::className(), ['ruanganasal_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getInvoicemasukTs()
    // {
    //     return $this->hasMany(InvoicemasukT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getInvoicetagihanTs()
    // {
    //     return $this->hasMany(InvoicetagihanT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getJadwalbukapoliMs()
    // {
    //     return $this->hasMany(JadwalbukapoliM::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getJadwaldokterMs()
    // {
    //     return $this->hasMany(JadwaldokterM::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getKamarruanganMs()
    // {
    //     return $this->hasMany(KamarruanganM::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getKarcisMs()
    // {
    //     return $this->hasMany(KarcisM::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getKasuspenyakitruanganMps()
    // {
    //     return $this->hasMany(KasuspenyakitruanganMp::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getJeniskasuspenyakits()
    // {
    //     return $this->hasMany(JeniskasuspenyakitM::className(), ['jeniskasuspenyakit_id' => 'jeniskasuspenyakit_id'])->viaTable('kasuspenyakitruangan_mp', ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getKelahiranbayiTs()
    // {
    //     return $this->hasMany(KelahiranbayiT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getKelasruanganMps()
    // {
    //     return $this->hasMany(KelasruanganMp::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getKelaspelayanans()
    // {
    //     return $this->hasMany(KelaspelayananM::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id'])->viaTable('kelasruangan_mp', ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getKesimpulanmcuTs()
    // {
    //     return $this->hasMany(KesimpulanmcuT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getKomponenjasaMs()
    // {
    //     return $this->hasMany(KomponenjasaM::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getLayarruanganMs()
    // {
    //     return $this->hasMany(LayarruanganM::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getLayarantrians()
    // {
    //     return $this->hasMany(LayarantrianM::className(), ['layarantrian_id' => 'layarantrian_id'])->viaTable('layarruangan_m', ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getLinenMs()
    // {
    //     return $this->hasMany(LinenM::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getLokasiasetMs()
    // {
    //     return $this->hasMany(LokasiasetM::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getMasukkamarTs()
    // {
    //     return $this->hasMany(MasukkamarT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPaketpelayananMs()
    // {
    //     return $this->hasMany(PaketpelayananM::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPasienadmisiTs()
    // {
    //     return $this->hasMany(PasienadmisiT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPasienkirimkeunitlainTs()
    // {
    //     return $this->hasMany(PasienkirimkeunitlainT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPasienmasukpenunjangTs()
    // {
    //     return $this->hasMany(PasienmasukpenunjangT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPasienmasukpenunjangTs0()
    // {
    //     return $this->hasMany(PasienmasukpenunjangT::className(), ['ruanganasal_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPasienpulangTs()
    // {
    //     return $this->hasMany(PasienpulangT::className(), ['ruanganakhir_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPegawairuanganMps()
    // {
    //     return $this->hasMany(PegawairuanganMp::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPegawais()
    // {
    //     return $this->hasMany(PegawaiM::className(), ['pegawai_id' => 'pegawai_id'])->viaTable('pegawairuangan_mp', ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPelayananrekeningMs()
    // {
    //     return $this->hasMany(PelayananrekeningM::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPemakaianambulansTs()
    // {
    //     return $this->hasMany(PemakaianambulansT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPembatalanuangmukaTs()
    // {
    //     return $this->hasMany(PembatalanuangmukaT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPembayaranpelayananTs()
    // {
    //     return $this->hasMany(PembayaranpelayananT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPembayaranpelayananTs0()
    // {
    //     return $this->hasMany(PembayaranpelayananT::className(), ['ruangan_pelakhir_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPendaftaranTs()
    // {
    //     return $this->hasMany(PendaftaranT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPenerimaanbarangTs()
    // {
    //     return $this->hasMany(PenerimaanbarangT::className(), ['gudangpenerima_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPenerimaansterilisasiTs()
    // {
    //     return $this->hasMany(PenerimaansterilisasiT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPenerimaanumumTs()
    // {
    //     return $this->hasMany(PenerimaanumumT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPengajuansterlilisasiTs()
    // {
    //     return $this->hasMany(PengajuansterlilisasiT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPengirimanrmTs()
    // {
    //     return $this->hasMany(PengirimanrmT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPengirimanrmTs0()
    // {
    //     return $this->hasMany(PengirimanrmT::className(), ['ruanganpengirim_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPengirimanrmTs1()
    // {
    //     return $this->hasMany(PengirimanrmT::className(), ['ruanganpenerima_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPenjualanresepTs()
    // {
    //     return $this->hasMany(PenjualanresepT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPenjualanresepTs0()
    // {
    //     return $this->hasMany(PenjualanresepT::className(), ['penjpasienruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPermintaanmcuTs()
    // {
    //     return $this->hasMany(PermintaanmcuT::className(), ['ruangantujuan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPermintaanpembelianTs()
    // {
    //     return $this->hasMany(PermintaanpembelianT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPersalinanTs()
    // {
    //     return $this->hasMany(PersalinanT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPesanambulansTs()
    // {
    //     return $this->hasMany(PesanambulansT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPesanbarangTs()
    // {
    //     return $this->hasMany(PesanbarangT::className(), ['ruanganpemesan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPindahkamarTs()
    // {
    //     return $this->hasMany(PindahkamarT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getRekeninguangmukaMs()
    // {
    //     return $this->hasMany(RekeninguangmukaM::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getRenanggpenerimaanTs()
    // {
    //     return $this->hasMany(RenanggpenerimaanT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getRencanakebfarmasiTs()
    // {
    //     return $this->hasMany(RencanakebfarmasiT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getRencanatindakanTs()
    // {
    //     return $this->hasMany(RencanatindakanT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getReturbayarpelayananTs()
    // {
    //     return $this->hasMany(ReturbayarpelayananT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getReturpembelianTs()
    // {
    //     return $this->hasMany(ReturpembelianT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getReturpenerimaanumumTs()
    // {
    //     return $this->hasMany(ReturpenerimaanumumT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getReturresepTs()
    // {
    //     return $this->hasMany(ReturresepT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */

    public function getInstalasi()
    {
        return $this->hasOne(Instalasi::className(), ['instalasi_id' => 'instalasi_id']);
    }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getUnitkerja()
    // {
    //     return $this->hasOne(UnitkerjaM::className(), ['unitkerja_id' => 'unitkerja_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getRuanganpegawaiMps()
    // {
    //     return $this->hasMany(RuanganpegawaiMp::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPegawais0()
    // {
    //     return $this->hasMany(PegawaiM::className(), ['pegawai_id' => 'pegawai_id'])->viaTable('ruanganpegawai_mp', ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getRuanganpemakaiKs()
    // {
    //     return $this->hasMany(RuanganpemakaiK::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getLoginpemakais()
    // {
    //     return $this->hasMany(LoginpemakaiK::className(), ['loginpemakai_id' => 'loginpemakai_id'])->viaTable('ruanganpemakai_k', ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getTandabuktibayarTs()
    // {
    //     return $this->hasMany(TandabuktibayarT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getTandabuktikeluarTs()
    // {
    //     return $this->hasMany(TandabuktikeluarT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getTerimapersediaanTs()
    // {
    //     return $this->hasMany(TerimapersediaanT::className(), ['ruanganpenerima_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getTindakanpelayananTs()
    // {
    //     return $this->hasMany(TindakanpelayananT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getTindakanruanganMps()
    // {
    //     return $this->hasMany(TindakanruanganMp::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getDaftartindakans()
    // {
    //     return $this->hasMany(DaftartindakanM::className(), ['daftartindakan_id' => 'daftartindakan_id'])->viaTable('tindakanruangan_mp', ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getTindakansudahbayarTs()
    // {
    //     return $this->hasMany(TindakansudahbayarT::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getUnitkerjaruanganMps()
    // {
    //     return $this->hasMany(UnitkerjaruanganMp::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getUnitkerjas()
    // {
    //     return $this->hasMany(UnitkerjaM::className(), ['unitkerja_id' => 'unitkerja_id'])->viaTable('unitkerjaruangan_mp', ['ruangan_id' => 'ruangan_id']);
    // }

    public function listRuangan($penunjang = NULL , $instalasi = NULL)
    {
        $query = self::find()
        ->joinWith(['instalasi' => function($q) use($penunjang) {
            $q->where(['is_penunjang' => $penunjang]);
        }])
        ->where([
            self::tableName().'.is_active' => 't',
        ]);

        if($penunjang == 'f') {
            $query->andWhere([
                'IN', self::tableName().'.instalasi_id', $instalasi
            ]);
        }

        return $query->all();
    }
}
