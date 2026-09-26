<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pegawai_m".
 *
 * @property int $pegawai_id -
 * @property string $nomorindukpegawai -
 * @property string $gelardepan lookup_m.lookup_type='gelar_depan'
 * @property string $nama_pegawai -
 * @property string $gelarbelakang gelarbelakang_m
 * @property string $jeniskelamin lookup_m.lookup_type='jenis_kelamin'
 * @property string $tempatlahir_pegawai -
 * @property string $tgl_lahirpegawai -
 * @property string $agama lookup_m.lookup_type='agama'
 * @property string $alamat_pegawai -
 * @property int $propinsi_id propinsi_m
 * @property int $kabupaten_id kabupaten_m
 * @property int $kecamatan_id kecamatan_m
 * @property int $kelurahan_id kelurahan_m
 * @property int $suku_id suku_m
 * @property string $notelp_pegawai -
 * @property string $nomobile_pegawai -
 * @property string $alamatemail -
 * @property int $pangkat_id pangkat_m
 * @property int $jabatan_id jabatan_m
 * @property string $warna_kulit lookup_m.lookup_type='warna_kulit'
 * @property int $golonganpegawai_id golonganpegawai_m
 * @property int $jenisjabatan_id jenisjabatan_m
 * @property int $jenjangjabatan_id jenjangjabatan_m
 * @property int $pendidikan_id pendidikan_m
 * @property int $pendkualifikasi_id pendidikankualifikasi_m
 * @property int $kelompokpegawai_id kelompokpegawai_m
 * @property int $profilrs_id
 * @property int $pengangkatantphl_id
 * @property string $jenisidentitas lookup_m.lookup_type='jenis_identitas'
 * @property string $noidentitas
 * @property string $status_pegawai lookup_m.lookup_type='kategori_pegawai'
 * @property string $warganegara_pegawai lookup_m.lookup_type='warga_negara'
 * @property int $bank_id bank_m
 * @property double $tinggibadan -
 * @property double $beratbadan -
 * @property string $kemampuan_bahasa -
 * @property int $loginpemakai_id loginpemakai_k
 * @property string $photopegawai -
 * @property string $photopegawai_blob -
 * @property string $no_fingerprint
 * @property string $suratizinpraktek
 * @property string $no_rekening -
 * @property string $npwp -
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
 * @property int $golongan_darah lookup_type='golongan_darah'
 * @property int $status_kawin lookup_type='status_perkawinan'
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
 * @property PasienkirimkeunitlainT[] $pasienkirimkeunitlainTs
 * @property PasienkirimkeunitlainT[] $pasienkirimkeunitlainTs0
 * @property PasienmasukpenunjangT[] $pasienmasukpenunjangTs
 * @property JabatanM $jabatan
 * @property JenisjabatanM $jenisjabatan
 * @property JenjangjabatanM $jenjangjabatan
 * @property KelompokpegawaiM $kelompokpegawai
 * @property LoginpemakaiK $loginpemakai
 * @property PangkatM $pangkat
 * @property PendidikanM $pendidikan
 * @property PendidikankualifikasiM $pendkualifikasi
 * @property PengangkatantphlT $pengangkatantphl
 * @property ProfilrumahsakitM $profilrs
 * @property PropinsiM $propinsi
 * @property StatuskepemilikanrumahM $bank
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
 * @property RenanggpenerimaanT[] $renanggpenerimaanTs
 * @property RenanggpenerimaanT[] $renanggpenerimaanTs0
 * @property RencanakebutuhanobatT[] $rencanakebutuhanobatTs
 * @property RencanakebutuhanobatT[] $rencanakebutuhanobatTs0
 * @property RencanakebutuhanobatT[] $rencanakebutuhanobatTs1
 * @property RencanggaranpengT[] $rencanggaranpengTs
 * @property RencanggaranpengT[] $rencanggaranpengTs0
 * @property ReturpenerimaanbarangT[] $returpenerimaanbarangTs
 * @property ReturpenerimaanbarangT[] $returpenerimaanbarangTs0
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
 * @property TandabuktikeluarT[] $tandabuktikeluarTs
 * @property TandabuktikeluarT[] $tandabuktikeluarTs0
 * @property TandabuktikeluarT[] $tandabuktikeluarTs1
 * @property TandabuktikeluarT[] $tandabuktikeluarTs2
 * @property VerifikasitagihanT[] $verifikasitagihanTs
 * @property VerifikasitagihanT[] $verifikasitagihanTs0
 */
class Pegawai extends \Doco\components\DocoActiveRecord
{
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
            [
                [
                    'pegawai_id', 
                    'nama_pegawai',
                    'jeniskelamin',
                ], 
                'required', "on" => "update-spesialis",
            ],
            [['nama_pegawai', 'jeniskelamin'], 'required','message'=>'{attribute} Tidak boleh kosong'],
            // [['nomorindukpegawai', 'nama_pegawai', 'jeniskelamin', 'agama', 'status_kawin', 'golongan_darah', 'tgl_lahirpegawai', 'tempatlahir_pegawai', 'warganegara_pegawai', 'suku_id', 'alamat_pegawai', 'propinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id', 'pendidikan_id', 'pendkualifikasi_id'], 'required','message'=>'{attribute} Tidak boleh kosong'],
            [['noidentitas', 'nomorindukpegawai', 'tgl_lahirpegawai', 'created_date', 'last_modified_date', 'deleted_date', 'spesialis_id', 'is_publishweb', 'kode_dokter_bpjs', 'nama_dokter_bpjs','tanda_tangan'], 'safe'],
            [['alamat_pegawai', 'additional_data'], 'string'],
            [['propinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id', 'suku_id', 'pangkat_id', 'jabatan_id', 'golonganpegawai_id', 'jenisjabatan_id', 'jenjangjabatan_id', 'pendidikan_id', 'pendkualifikasi_id', 'kelompokpegawai_id', 'profilrs_id', 'pengangkatantphl_id', 'bank_id', 'loginpemakai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'golongan_darah', 'status_kawin'], 'default', 'value' => null],
            [['propinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id', 'suku_id', 'pangkat_id', 'jabatan_id', 'golonganpegawai_id', 'jenisjabatan_id', 'jenjangjabatan_id', 'pendidikan_id', 'pendkualifikasi_id', 'kelompokpegawai_id', 'profilrs_id', 'pengangkatantphl_id', 'bank_id', 'loginpemakai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'golongan_darah', 'status_kawin', 'kemampuan_bahasa'], 'integer'],
            [['tinggibadan', 'beratbadan'], 'number'],
            [['is_deleted', 'is_active','is_online'], 'boolean'],
            [['nomorindukpegawai', 'tempatlahir_pegawai', 'warna_kulit'], 'string', 'max' => 30],
            [['gelardepan'], 'string', 'max' => 10],
            [['nama_pegawai'], 'string', 'max' => 255],
            [['notelp_pegawai', 'nomobile_pegawai', 'dokter_id'], 'string', 'max' => 50],
            [['gelarbelakang'], 'string', 'max' => 32],
            [['jeniskelamin', 'agama', 'jenisidentitas', 'status_pegawai', 'no_fingerprint'], 'string', 'max' => 20],
            [['alamatemail', 'noidentitas', 'suratizinpraktek'], 'string', 'max' => 100],
            [['warganegara_pegawai'], 'string', 'max' => 25],
            [['nama_pegawai', 'no_rekening', 'npwp'], 'string', 'max' => 255],
            [['photopegawai'], 'string', 'max' => 200],
            // [['nomorindukpegawai'], 'unique', 'message' => 'NIP sudah terdaftar'],
            [['nomorindukpegawai'], 'checkUnique'],
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
            'warna_kulit' => 'Warna Kulit',
            'golonganpegawai_id' => 'Golonganpegawai ID',
            'jenisjabatan_id' => 'Jenisjabatan ID',
            'jenjangjabatan_id' => 'Jenjangjabatan ID',
            'pendidikan_id' => 'Pendidikan ID',
            'pendkualifikasi_id' => 'Pendkualifikasi ID',
            'kelompokpegawai_id' => 'Kelompokpegawai ID',
            'profilrs_id' => 'Profilrs ID',
            'pengangkatantphl_id' => 'Pengangkatantphl ID',
            'jenisidentitas' => 'Jenisidentitas',
            'noidentitas' => 'NIK',
            'status_pegawai' => 'Status Pegawai',
            'warganegara_pegawai' => 'Warganegara Pegawai',
            'bank_id' => 'Bank ID',
            'tinggibadan' => 'Tinggibadan',
            'beratbadan' => 'Beratbadan',
            'kemampuan_bahasa' => 'Kemampuan Bahasa',
            'loginpemakai_id' => 'Loginpemakai ID',
            'photopegawai' => 'Photopegawai',
            'no_fingerprint' => 'No Fingerprint',
            'suratizinpraktek' => 'Suratizinpraktek',
            'no_rekening' => 'No Rekening',
            'npwp' => 'Npwp',
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
            'golongan_darah' => 'Golongan Darah',
            'status_kawin' => 'Status Kawin',
            'is_online' => 'Pegawai Online',
            'photopegawai_blob' => 'Photo Pegawai Blob',
        ];
    }

    public function checkNoInduk()
    {
        $model = self::find()
            ->select(['nomorindukpegawai'])
            ->where(['LOWER (nomorindukpegawai)' => strtolower($this->nomorindukpegawai), 'is_deleted' => false])->one();


        if($this->isNewRecord) {
            $this->addError("nomorindukpegawai", "NIP Sudah Dipakai");
            return false;
        }

        // if ( !empty($model) && ($model->pegawai_id != $this->pegawai_id)) {
        //     $this->addError("nomorindukpegawai", "NIP Sudah Dipakai");
        //     return false;
        // }
        return true;
    }

    public function checkUnique()
    {
        $nomorindukpegawai = $this->nomorindukpegawai;
        if($nomorindukpegawai != '-') {
            $model = self::find()->where([
                'TRIM(LOWER (nomorindukpegawai))' => strtolower($nomorindukpegawai), 
                'is_deleted' => false,
            ])->one();
    
            if (!empty($model) && $model->pegawai_id !== $this->pegawai_id) {
                $this->addError('nomorindukpegawai', 'NIP Sudah Dipakai.');
                return false;
            }
            else {
                return true;
            }
        }
    }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getAlokasianggaranTs()
    // {
    //     return $this->hasMany(AlokasianggaranT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getAlokasianggaranTs0()
    // {
    //     return $this->hasMany(AlokasianggaranT::className(), ['pegawaimenyetujui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getAnamnesaTs()
    // {
    //     return $this->hasMany(AnamnesaT::className(), ['pegawaidokter_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getAnamnesaTs0()
    // {
    //     return $this->hasMany(AnamnesaT::className(), ['pegawaiperawat_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getAnamnesaTs1()
    // {
    //     return $this->hasMany(AnamnesaT::className(), ['pegawaitriase_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getAnamnesadietTs()
    // {
    //     return $this->hasMany(AnamnesadietT::className(), ['pegawai_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getAnamnesadietTs0()
    // {
    //     return $this->hasMany(AnamnesadietT::className(), ['ahligizi_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getApproverenanggpenTs()
    // {
    //     return $this->hasMany(ApproverenanggpenT::className(), ['pegawaimenyetujui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getApproverenanggpenTs0()
    // {
    //     return $this->hasMany(ApproverenanggpenT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getApproverencanaanggaranTs()
    // {
    //     return $this->hasMany(ApproverencanaanggaranT::className(), ['pegawaimenyetujui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getApproverencanaanggaranTs0()
    // {
    //     return $this->hasMany(ApproverencanaanggaranT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getApprrencanggaranTs()
    // {
    //     return $this->hasMany(ApprrencanggaranT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getApprrencanggaranTs0()
    // {
    //     return $this->hasMany(ApprrencanggaranT::className(), ['pegawaimenyetujui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getAsuhankeperawatanTs()
    // {
    //     return $this->hasMany(AsuhankeperawatanT::className(), ['pegawai_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getBuatjanjipoliTs()
    // {
    //     return $this->hasMany(BuatjanjipoliT::className(), ['pegawai_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getClosingkasirTs()
    // {
    //     return $this->hasMany(ClosingkasirT::className(), ['pegawai_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getDebetnotaTs()
    // {
    //     return $this->hasMany(DebetnotaT::className(), ['debetnotauntuk_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getDebetnotaTs0()
    // {
    //     return $this->hasMany(DebetnotaT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getDekontaminasiTs()
    // {
    //     return $this->hasMany(DekontaminasiT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getDekontaminasiTs0()
    // {
    //     return $this->hasMany(DekontaminasiT::className(), ['pegawaipetugas_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getDietpasienTs()
    // {
    //     return $this->hasMany(DietpasienT::className(), ['pegawai_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getDietpasienTs0()
    // {
    //     return $this->hasMany(DietpasienT::className(), ['pegawaiahligizi_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getFakturpenerimaanobatTs()
    // {
    //     return $this->hasMany(FakturpenerimaanobatT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getFakturpenerimaanobatTs0()
    // {
    //     return $this->hasMany(FakturpenerimaanobatT::className(), ['pegawaimenyetujui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getHasilpemeriksaanmcuTs()
    // {
    //     return $this->hasMany(HasilpemeriksaanmcuT::className(), ['pegawai_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getHearingtestTs()
    // {
    //     return $this->hasMany(HearingtestT::className(), ['pegawai_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getInventarisasibarangTs()
    // {
    //     return $this->hasMany(InventarisasibarangT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getInventarisasibarangTs0()
    // {
    //     return $this->hasMany(InventarisasibarangT::className(), ['petugas1_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getInventarisasibarangTs1()
    // {
    //     return $this->hasMany(InventarisasibarangT::className(), ['petugas2_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getInvoicedisposisiTs()
    // {
    //     return $this->hasMany(InvoicedisposisiT::className(), ['pegawaipengirim_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getInvoicedisposisiTs0()
    // {
    //     return $this->hasMany(InvoicedisposisiT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getInvoicekeluarTs()
    // {
    //     return $this->hasMany(InvoicekeluarT::className(), ['pegawai_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getInvoicekeluarTs0()
    // {
    //     return $this->hasMany(InvoicekeluarT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getInvoicekeluarTs1()
    // {
    //     return $this->hasMany(InvoicekeluarT::className(), ['pegawaimenyetujui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getInvoicemasukTs()
    // {
    //     return $this->hasMany(InvoicemasukT::className(), ['pegawaimenyetujui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getInvoicemasukTs0()
    // {
    //     return $this->hasMany(InvoicemasukT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getInvoicemasukdetailTs()
    // {
    //     return $this->hasMany(InvoicemasukdetailT::className(), ['pasienpegawai_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getInvoicemasukdetsupplierTs()
    // {
    //     return $this->hasMany(InvoicemasukdetsupplierT::className(), ['pegawai_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getInvoicetagihanTs()
    // {
    //     return $this->hasMany(InvoicetagihanT::className(), ['pegawaiverifikasi_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getInvoicetagihanTs0()
    // {
    //     return $this->hasMany(InvoicetagihanT::className(), ['pegawaimenyetujui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getJadwaldokterMs()
    // {
    //     return $this->hasMany(JadwaldokterM::className(), ['pegawai_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getJadwalkunjunganrmTs()
    // {
    //     return $this->hasMany(JadwalkunjunganrmT::className(), ['pegawai_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getKesimpulanmcuTs()
    // {
    //     return $this->hasMany(KesimpulanmcuT::className(), ['pegawaipemeriksa_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getKesimpulanpenilaianTs()
    // {
    //     return $this->hasMany(KesimpulanpenilaianT::className(), ['pegawai_pemberisaran' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getKesimpulanpenilaiandetailTs()
    // {
    //     return $this->hasMany(KesimpulanpenilaiandetailT::className(), ['penilaianpegawai_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getKesimpulanpenilaiandetailTs0()
    // {
    //     return $this->hasMany(KesimpulanpenilaiandetailT::className(), ['pegawaipenilai_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getMasukkamarTs()
    // {
    //     return $this->hasMany(MasukkamarT::className(), ['pegawai_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getMutasibarangTs()
    // {
    //     return $this->hasMany(MutasibarangT::className(), ['pegawaipengirim_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getMutasibarangTs0()
    // {
    //     return $this->hasMany(MutasibarangT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getNofingeralatMs()
    // {
    //     return $this->hasMany(NofingeralatM::className(), ['pegawai_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getObatalkesproduksiMs()
    // {
    //     return $this->hasMany(ObatalkesproduksiM::className(), ['pegawai_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getOrganigramMs()
    // {
    //     return $this->hasMany(OrganigramM::className(), ['pegawai_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getOtorisasikeuanganTs()
    // {
    //     return $this->hasMany(OtorisasikeuanganT::className(), ['otorisasioleh_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getOtorisasipimpinanTs()
    // {
    //     return $this->hasMany(OtorisasipimpinanT::className(), ['otorisasioleh_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPasienadmisiTs()
    // {
    //     return $this->hasMany(PasienadmisiT::className(), ['pegawai_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPasienkirimkeunitlainTs()
    // {
    //     return $this->hasMany(PasienkirimkeunitlainT::className(), ['pegawai_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPasienkirimkeunitlainTs0()
    // {
    //     return $this->hasMany(PasienkirimkeunitlainT::className(), ['antrian_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPasienmasukpenunjangTs()
    // {
    //     return $this->hasMany(PasienmasukpenunjangT::className(), ['pegawai_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getJabatan()
    // {
    //     return $this->hasOne(JabatanM::className(), ['jabatan_id' => 'jabatan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getJenisjabatan()
    // {
    //     return $this->hasOne(JenisjabatanM::className(), ['jenisjabatan_id' => 'jenisjabatan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getJenjangjabatan()
    // {
    //     return $this->hasOne(JenjangjabatanM::className(), ['jenjangjabatan_id' => 'jenjangjabatan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getKelompokpegawai()
    // {
    //     return $this->hasOne(KelompokpegawaiM::className(), ['kelompokpegawai_id' => 'kelompokpegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getLoginpemakai()
    // {
    //     return $this->hasOne(LoginpemakaiK::className(), ['loginpemakai_id' => 'loginpemakai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPangkat()
    // {
    //     return $this->hasOne(PangkatM::className(), ['pangkat_id' => 'pangkat_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPendidikan()
    // {
    //     return $this->hasOne(PendidikanM::className(), ['pendidikan_id' => 'pendidikan_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPendkualifikasi()
    // {
    //     return $this->hasOne(PendidikankualifikasiM::className(), ['pendkualifikasi_id' => 'pendkualifikasi_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPengangkatantphl()
    // {
    //     return $this->hasOne(PengangkatantphlT::className(), ['pengangkatantphl_id' => 'pengangkatantphl_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getProfilrs()
    // {
    //     return $this->hasOne(ProfilrumahsakitM::className(), ['profilrs_id' => 'profilrs_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPropinsi()
    // {
    //     return $this->hasOne(PropinsiM::className(), ['propinsi_id' => 'propinsi_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getBank()
    // {
    //     return $this->hasOne(StatuskepemilikanrumahM::className(), ['statuskepemilikanrumah_id' => 'bank_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getSuku()
    // {
    //     return $this->hasOne(SukuM::className(), ['suku_id' => 'suku_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPemakaianambulansTs()
    // {
    //     return $this->hasMany(PemakaianambulansT::className(), ['supir_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPemakaianambulansTs0()
    // {
    //     return $this->hasMany(PemakaianambulansT::className(), ['pelaksana_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPemakaianambulansTs1()
    // {
    //     return $this->hasMany(PemakaianambulansT::className(), ['perawat1_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPemakaianambulansTs2()
    // {
    //     return $this->hasMany(PemakaianambulansT::className(), ['perawat2_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPembelianobatTs()
    // {
    //     return $this->hasMany(PembelianobatT::className(), ['pegawai_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPembelianobatTs0()
    // {
    //     return $this->hasMany(PembelianobatT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPembelianobatTs1()
    // {
    //     return $this->hasMany(PembelianobatT::className(), ['pegawaimenyetujui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPenawaranobatTs()
    // {
    //     return $this->hasMany(PenawaranobatT::className(), ['pegawai_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPenawaranobatTs0()
    // {
    //     return $this->hasMany(PenawaranobatT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPenawaranobatTs1()
    // {
    //     return $this->hasMany(PenawaranobatT::className(), ['pegawaimenyetujui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPendaftaranTs()
    // {
    //     return $this->hasMany(PendaftaranT::className(), ['pegawai_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPenerimaanobatTs()
    // {
    //     return $this->hasMany(PenerimaanobatT::className(), ['pegawai_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPenerimaanobatTs0()
    // {
    //     return $this->hasMany(PenerimaanobatT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPenerimaanobatTs1()
    // {
    //     return $this->hasMany(PenerimaanobatT::className(), ['pegawaimenyetujui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPenerimaansterilisasiTs()
    // {
    //     return $this->hasMany(PenerimaansterilisasiT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPenerimaansterilisasiTs0()
    // {
    //     return $this->hasMany(PenerimaansterilisasiT::className(), ['pegawaipenerima_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPengajuansterlilisasiTs()
    // {
    //     return $this->hasMany(PengajuansterlilisasiT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPengajuansterlilisasiTs0()
    // {
    //     return $this->hasMany(PengajuansterlilisasiT::className(), ['pegawaipengajuan_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPengangkatantphlTs()
    // {
    //     return $this->hasMany(PengangkatantphlT::className(), ['pegawai_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPengeluaranumumTs()
    // {
    //     return $this->hasMany(PengeluaranumumT::className(), ['pegawai_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPersalinanTs()
    // {
    //     return $this->hasMany(PersalinanT::className(), ['pegawai_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPersalinanTs0()
    // {
    //     return $this->hasMany(PersalinanT::className(), ['bidan1_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPersalinanTs1()
    // {
    //     return $this->hasMany(PersalinanT::className(), ['bidan2_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPersalinanTs2()
    // {
    //     return $this->hasMany(PersalinanT::className(), ['perawat1_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPersalinanTs3()
    // {
    //     return $this->hasMany(PersalinanT::className(), ['perawat2_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPesanbarangTs()
    // {
    //     return $this->hasMany(PesanbarangT::className(), ['pegawaipemesan_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPesanbarangTs0()
    // {
    //     return $this->hasMany(PesanbarangT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPindahkamarTs()
    // {
    //     return $this->hasMany(PindahkamarT::className(), ['pegawai_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getRealisasianggpenerimaanTs()
    // {
    //     return $this->hasMany(RealisasianggpenerimaanT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getRealisasianggpenerimaanTs0()
    // {
    //     return $this->hasMany(RealisasianggpenerimaanT::className(), ['pegawaimenyetujui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getRenanggpenerimaanTs()
    // {
    //     return $this->hasMany(RenanggpenerimaanT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getRenanggpenerimaanTs0()
    // {
    //     return $this->hasMany(RenanggpenerimaanT::className(), ['pegawaimenyetujui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getRencanakebutuhanobatTs()
    // {
    //     return $this->hasMany(RencanakebutuhanobatT::className(), ['pegawai_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getRencanakebutuhanobatTs0()
    // {
    //     return $this->hasMany(RencanakebutuhanobatT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getRencanakebutuhanobatTs1()
    // {
    //     return $this->hasMany(RencanakebutuhanobatT::className(), ['pegawaimenyetujui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getRencanggaranpengTs()
    // {
    //     return $this->hasMany(RencanggaranpengT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getRencanggaranpengTs0()
    // {
    //     return $this->hasMany(RencanggaranpengT::className(), ['pegawaimenyetujui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getReturpenerimaanbarangTs()
    // {
    //     return $this->hasMany(ReturpenerimaanbarangT::className(), ['pegawairetur_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getReturpenerimaanbarangTs0()
    // {
    //     return $this->hasMany(ReturpenerimaanbarangT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getReturresepTs()
    // {
    //     return $this->hasMany(ReturresepT::className(), ['pegawaimengetahui_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getReturresepTs0()
    // {
    //     return $this->hasMany(ReturresepT::className(), ['pegawairetur_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getRevisirencanaanggaranTs()
    // {
    //     return $this->hasMany(RevisirencanaanggaranT::className(), ['ygmerevisi_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getRevisirencanggpengTs()
    // {
    //     return $this->hasMany(RevisirencanggpengT::className(), ['ygmerevisi_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getRuanganpegawaiMps()
    // {
    //     return $this->hasMany(RuanganpegawaiMp::className(), ['pegawai_id' => 'pegawai_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuangan()
    {
        return $this->hasMany(Ruangan::className(), ['ruangan_id' => 'ruangan_id'])->viaTable('ruanganpegawai_mp', ['pegawai_id' => 'pegawai_id']);
    }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getSetorbankTs()
    // {
    //     return $this->hasMany(SetorbankT::className(), ['pegawaimenyetor_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getSusunankeluargaMs()
    // {
    //     return $this->hasMany(SusunankeluargaM::className(), ['pegawai_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getTandabuktibayarTs()
    // {
    //     return $this->hasMany(TandabuktibayarT::className(), ['pegawai1_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getTandabuktibayarTs0()
    // {
    //     return $this->hasMany(TandabuktibayarT::className(), ['pegawai2_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getTandabuktikeluarTs()
    // {
    //     return $this->hasMany(TandabuktikeluarT::className(), ['pegawai1_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getTandabuktikeluarTs0()
    // {
    //     return $this->hasMany(TandabuktikeluarT::className(), ['pegawai2_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getTandabuktikeluarTs1()
    // {
    //     return $this->hasMany(TandabuktikeluarT::className(), ['pegawai3_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getTandabuktikeluarTs2()
    // {
    //     return $this->hasMany(TandabuktikeluarT::className(), ['pegawai4_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getVerifikasitagihanTs()
    // {
    //     return $this->hasMany(VerifikasitagihanT::className(), ['verifikasioleh_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getVerifikasitagihanTs0()
    // {
    //     return $this->hasMany(VerifikasitagihanT::className(), ['mengetahuioleh_id' => 'pegawai_id']);
    // }
}
