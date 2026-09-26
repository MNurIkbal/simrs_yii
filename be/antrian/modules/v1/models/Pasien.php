<?php

namespace app\modules\v1\models;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\models\bpjs\Bpjs;

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
 * @property int $golonganumur_id
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
 * @property string $alamat_sekarang
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
 * @property GolonganumurM $golonganumur
 * @property KabupatenM $kabupaten
 * @property KecamatanM $kecamatan
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
            [['no_rekam_medik', 'tgl_rekam_medik', 'namadepan', 'nama_pasien', 'tanggal_lahir', 'alamat_pasien', 'golongandarah', 'nama_ibu', 'statusrekammedis'], 'required'],
            [['tgl_rekam_medik', 'tanggal_lahir', 'tgl_meninggal', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['golonganumur_id', 'rt', 'rw', 'propinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id', 'pendidikan_id', 'pekerjaan_id', 'suku_id', 'anakke', 'jumlah_bersaudara', 'dokrekammedis_id', 'pegawai_id', 'loginpemakai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'profilrs_id'], 'default', 'value' => null],
            [['golonganumur_id', 'rt', 'rw', 'propinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id', 'pendidikan_id', 'pekerjaan_id', 'suku_id', 'anakke', 'jumlah_bersaudara', 'dokrekammedis_id', 'pegawai_id', 'loginpemakai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'profilrs_id'], 'integer'],
            [['alamat_pasien', 'additional_data', 'alamat_sekarang'], 'string'],
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
            [['no_rekam_medik'], 'unique'],
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
            'golonganumur_id' => 'Golonganumur ID',
            'alamat_pasien' => 'Alamat Pasien',
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
            'no_telepon_pasien' => 'No Telepon Pasien',
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
            'alamat_sekarang' => 'Alamat Sekarang',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAmbiljenazahTs()
    {
        return $this->hasMany(Ambiljenazah::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnamnesaTs()
    {
        return $this->hasMany(Anamnesa::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnamnesadietTs()
    {
        return $this->hasMany(Anamnesadiet::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAsuhankeperawatanTs()
    {
        return $this->hasMany(Asuhankeperawatan::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAsuransipasienMs()
    {
        return $this->hasMany(Asuransipasien::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBayaruangmukaTs()
    {
        return $this->hasMany(Bayaruangmuka::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBookingkamarTs()
    {
        return $this->hasMany(Bookingkamar::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuatjanjipoliTs()
    {
        return $this->hasMany(Buatjanjipoli::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDietpasienTs()
    {
        return $this->hasMany(Dietpasien::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDokrekammedisM()
    {
        return $this->hasOne(Dokrekammedis::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHasilmcuTs()
    {
        return $this->hasMany(Hasilmcu::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHasilpemeriksaanlabTs()
    {
        return $this->hasMany(Hasilpemeriksaanlab::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHasilpemeriksaanradTs()
    {
        return $this->hasMany(Hasilpemeriksaanrad::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHasilpemeriksaanrmTs()
    {
        return $this->hasMany(Hasilpemeriksaanrm::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHearingtestTs()
    {
        return $this->hasMany(Hearingtest::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoicemasukdetailTs()
    {
        return $this->hasMany(Invoicemasukdetail::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJadwalkunjunganrmTs()
    {
        return $this->hasMany(Jadwalkunjunganrm::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJantungkoronerTs()
    {
        return $this->hasMany(Jantungkoroner::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKesimpulanmcuTs()
    {
        return $this->hasMany(Kesimpulanmcu::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDokrekammedis()
    {
        return $this->hasOne(Dokrekammedis::className(), ['dokrekammedis_id' => 'dokrekammedis_id']);
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
    public function getGolonganumur()
    {
        return $this->hasOne(Golonganumur::className(), ['golonganumur_id' => 'golonganumur_id']);
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
    public function getPekerjaan()
    {
        return $this->hasOne(Pekerjaan::className(), ['pekerjaan_id' => 'pekerjaan_id']);
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
    public function getPropinsi()
    {
        return $this->hasOne(Propinsi::className(), ['propinsi_id' => 'propinsi_id']);
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
    public function getPasienadmisiTs()
    {
        return $this->hasMany(Pasienadmisi::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienanastesiTs()
    {
        return $this->hasMany(Pasienanastesi::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienbatalperiksaTs()
    {
        return $this->hasMany(Pasienbatalperiksa::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienkirimkeunitlainTs()
    {
        return $this->hasMany(Pasienkirimkeunitlain::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienpulangTs()
    {
        return $this->hasMany(Pasienpulang::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPemakaianambulansTs()
    {
        return $this->hasMany(Pemakaianambulans::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPembayaranpelayananTs()
    {
        return $this->hasMany(Pembayaranpelayanan::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPembklaimdetailTs()
    {
        return $this->hasMany(Pembklaimdetail::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendaftaranTs()
    {
        return $this->hasMany(Pendaftaran::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPengirimanrmTs()
    {
        return $this->hasMany(Pengirimanrm::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenjualanresepTs()
    {
        return $this->hasMany(Penjualanresep::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPersalinanTs()
    {
        return $this->hasMany(Persalinan::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPesanambulansTs()
    {
        return $this->hasMany(Pesanambulans::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPindahkamarTs()
    {
        return $this->hasMany(Pindahkamar::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRencanaoperasiTs()
    {
        return $this->hasMany(Rencanaoperasi::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRencanatindakanTs()
    {
        return $this->hasMany(Rencanatindakan::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReturresepTs()
    {
        return $this->hasMany(Returresep::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTindakanpelayananTs()
    {
        return $this->hasMany(Tindakanpelayanan::className(), ['pasien_id' => 'pasien_id']);
    }

    public function getPenanggungJawab()
    {
        return $this->hasOne(PenanggungJawab::className(), ['pasien_id' => 'pasien_id']);
    }

    public static function getHistoryPoli($pasien_id)
    {
        if(empty($pasien_id) || $pasien_id=='undefined'){
            return [];
        }
        $pasien = self::findOne($pasien_id);
        $listPoli = $data_poli_bpjs = [];
        if(isset($pasien->nopeserta_bpjs)){

            $stime0 = microtime(true);
            $ws_rujukan_faskes = (new Bpjs)->cariRujukanPeserta($pasien->nopeserta_bpjs,true,false);
            $waktuCariRujukanBpjs = microtime(true) - $stime0;

            $stime1 = microtime(true);
            $ws_rujukan_rs = (new Bpjs)->cariRujukanPeserta($pasien->nopeserta_bpjs,true,true);
            $waktuCariPeserta = microtime(true) - $stime1;
            
            $bulan = date('m');
            $tahun = date('Y');

            $stime2 = microtime(true);
            $ws_rencana_kontrol = (new Bpjs)->rencanaKontrolBerdasarkanNoKartu($bulan, $tahun, $pasien->nopeserta_bpjs, 2);
            $waktuCariNoka = microtime(true) - $stime2;

            $list_rencana_kontrol = ArrayHelper::getValue($ws_rencana_kontrol,'response.list');
            
            if(!empty($ws_rujukan_faskes)){
                $response_rujukan_faskes = ArrayHelper::getValue($ws_rujukan_faskes,'response.rujukan');
                if(is_array($response_rujukan_faskes)){
                    foreach ($response_rujukan_faskes as $_res) {
                        $_kdpoli = null;
                        $_kdpoli = ArrayHelper::getValue($_res,'poliRujukan.kode');
                        if(isset($_kdpoli) && !empty($_kdpoli)){
                            $listPoli[] = $_kdpoli;
                        }
                    }
                }
            }
            if(!empty($ws_rujukan_rs)){
                $response_rujukan_rs = ArrayHelper::getValue($ws_rujukan_rs,'response.rujukan');
                if(is_array($response_rujukan_rs)){
                    foreach ($response_rujukan_rs as $_res) {
                        $_kdpoli = null;
                        $_kdpoli = ArrayHelper::getValue($_res,'poliRujukan.kode');
                        if(isset($_kdpoli) && !empty($_kdpoli)){
                            $listPoli[] = $_kdpoli;
                        }
                    }
                }
            }
            // $kode_rujukan_faskes = ArrayHelper::getValue($ws_rujukan_faskes,'response.rujukan.poliRujukan.kode');
            // $kode_rujukan_rs = ArrayHelper::getValue($ws_rujukan_rs,'response.rujukan.poliRujukan.kode');

            
            // if(isset($kode_rujukan_faskes) && !empty($kode_rujukan_faskes)){
            //     $listPoli[] = $kode_rujukan_faskes;
            // }
            // if(isset($kode_rujukan_rs) && !empty($kode_rujukan_rs)){
            //     $listPoli[] = $kode_rujukan_rs;
            // }
            if(isset($list_rencana_kontrol) && count($list_rencana_kontrol)>0){
                $list_poli_kontrol = ArrayHelper::getColumn($list_rencana_kontrol,'poliTujuan');
                if(is_array($list_poli_kontrol) && count($list_poli_kontrol)>0){
                    $listPoli = array_merge($listPoli,$list_poli_kontrol);
                }
            }
            if(count($listPoli)>0){
                $data_poli_bpjs = Ruangan::find()->select(['ruangan_id','kode_ruangan_bpjs','ruangan_nama'])->where(['kode_ruangan_bpjs'=>$listPoli])->asArray()->all();
            }
        }

        $stime3 = microtime(true);
        $query = "
            SELECT 
            DISTINCT ON (ruangan_m.kode_ruangan_bpjs)
            ruangan_pasien.pasien_id,
            ruangan_pasien.ruangan_id,
            ruangan_pasien.pegawai_id,
            ruangan_pasien.no_identitas_pasien,
            ruangan_pasien.nopeserta_bpjs,
            ruangan_m.ruangan_nama,
            ruangan_m.kode_ruangan_bpjs 
            FROM (
                SELECT DISTINCT ON (konsul.pasien_id,konsul.ruangan_id) 
                konsul.pasien_id,konsul.ruangan_id,konsul.pegawai_id ,pasien_m.no_identitas_pasien,pasien_m.nopeserta_bpjs  
                FROM konsulpoli_t konsul 
                JOIN pasien_m on pasien_m.pasien_id = konsul.pasien_id and pasien_m.is_deleted is false
                WHERE pasien_m.pasien_id = :pasien_id
                UNION
                SELECT DISTINCT ON (pendaftaran.pasien_id,pendaftaran.ruangan_id) 
                pendaftaran.pasien_id,pendaftaran.ruangan_id,pendaftaran.pegawai_id,pasien_m.no_identitas_pasien,pasien_m.nopeserta_bpjs   
                FROM pendaftaran_t pendaftaran   
                JOIN pasien_m on pasien_m.pasien_id = pendaftaran.pasien_id and pasien_m.is_deleted is false
                WHERE pendaftaran.instalasi_id =1 AND pasien_m.pasien_id = :pasien_id
            ) ruangan_pasien
            LEFT JOIN ruangan_m on ruangan_m.ruangan_id = ruangan_pasien.ruangan_id
            WHERE ruangan_m.kode_ruangan_bpjs is not null
        ";
        if(isset($data_poli_bpjs) && count($data_poli_bpjs)>0){
            $ruangan_ids = ArrayHelper::getColumn($data_poli_bpjs,'ruangan_id');
            if(count($ruangan_ids)>0){
                $query .= "AND ruangan_pasien.ruangan_id NOT IN (".implode(",",$ruangan_ids).")";
            }
        }
        $data_history = Yii::$app->db->createCommand($query)->bindValue(':pasien_id',$pasien_id)->queryAll();
        $waktuQueryDataHistory = microtime(true) - $stime3;

        $traceLog = [
            'waktuCariRujukanBpjs' => number_format($waktuCariRujukanBpjs,3),
            'waktuCariPeserta' => number_format($waktuCariPeserta,3),
            'waktuCariNoka' => number_format($waktuCariNoka,3),
            'waktuQueryDataHistory' => number_format($waktuQueryDataHistory,3)
        ];

        $data = array_merge($data_history,$data_poli_bpjs);
        $data['trace_log'] = $traceLog;
        return $data;
    }
}
