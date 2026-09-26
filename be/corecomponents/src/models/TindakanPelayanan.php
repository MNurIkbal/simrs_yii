<?php

/**
 * @Author: afil
 * @Date:   2018-01-16 15:58:00
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-06-28 16:44:43
 * @Description: 
 */
namespace Doco\models;

use Yii;

/**
 * This is the model class for table "tindakanpelayanan_t".
 *
 * @property int $tindakanpelayanan_id
 * @property int $detailhasilpemeriksaanlab_id
 * @property int $shift_id
 * @property int $kelaspelayanan_id
 * @property int $kelastanggungan_id
 * @property int $pasien_id
 * @property int $rencanaoperasi_id
 * @property int $instalasi_id
 * @property int $daftartindakan_id
 * @property int $alatmedis_id
 * @property int $tipepaket_id
 * @property int $tindakansudahbayar_id
 * @property int $carabayar_id
 * @property int $pendaftaran_id
 * @property int $hasilpemeriksaanrad_id
 * @property int $jeniskasuspenyakit_id
 * @property int $hasilpemeriksaanrm_id
 * @property int $ruangan_id
 * @property int $konsulpoli_id
 * @property int $pasienmasukpenunjang_id
 * @property int $hasilpemeriksaanpa_id
 * @property int $penjamin_id
 * @property int $pasienadmisi_id
 * @property int $verifikasitagihan_id
 * @property int $jurnalrekening_id
 * @property int $instruksitindakan_id
 * @property string $tgl_tindakan
 * @property double $tarif_rsakomodasi
 * @property double $tarif_medis
 * @property double $tarif_paramedis
 * @property double $tarif_bhp
 * @property double $tarif_satuan
 * @property double $tarif_tindakan
 * @property double $tarifcyto_tindakan
 * @property string $satuan_tindakan
 * @property int $qty_tindakan
 * @property bool $cyto_tindakan
 * @property string $dokterpenanggungjawab_id
 * @property string $dokterpelaksana_id
 * @property string $dokteranastesi_id
 * @property string $dokterdelegasi_id
 * @property string $bidan1_id
 * @property string $bidan2_id
 * @property string $perawat1_id
 * @property int $perawat2_id
 * @property double $discount_tindakan
 * @property double $pembebasan_tindakan
 * @property double $subsidiasuransi_tindakan
 * @property double $subsidipemerintah_tindakan
 * @property double $subsisidirumahsakit_tindakan
 * @property double $uangditerima_tindakan
 * @property string $keterangantindakan
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
 * @property double $pembulatan
 *
 * @property DetailhasilpemeriksaanlabT[] $detailhasilpemeriksaanlabTs
 * @property HasilpemeriksaanradT[] $hasilpemeriksaanradTs
 * @property HasilpemeriksaanrmT[] $hasilpemeriksaanrmTs
 * @property PermintaanmcuT[] $permintaanmcuTs
 * @property RencanaoperasiT[] $rencanaoperasiTs
 * @property AlatmedisM $alatmedis
 * @property CarabayarM $carabayar
 * @property DaftartindakanM $daftartindakan
 * @property DetailhasilpemeriksaanlabT $detailhasilpemeriksaanlab
 * @property HasilpemeriksaanpaT $hasilpemeriksaanpa
 * @property HasilpemeriksaanradT $hasilpemeriksaanrad
 * @property HasilpemeriksaanrmT $hasilpemeriksaanrm
 * @property InstalasiM $instalasi
 * @property JeniskasuspenyakitM $jeniskasuspenyakit
 * @property JurnalrekeningT $jurnalrekening
 * @property KelaspelayananM $kelaspelayanan
 * @property KelaspelayananM $kelastanggungan
 * @property PasienM $pasien
 * @property PasienadmisiT $pasienadmisi
 * @property PasienmasukpenunjangT $pasienmasukpenunjang
 * @property PendaftaranT $pendaftaran
 * @property PenjaminM $penjamin
 * @property RencanaoperasiT $rencanaoperasi
 * @property RuanganM $ruangan
 * @property VerifikasitagihanT $verifikasitagihan
 */
class TindakanPelayanan extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tindakanpelayanan_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['shift_id', 'kelaspelayanan_id', 'kelastanggungan_id', 'pasien_id', 'rencanaoperasi_id', 'instalasi_id', 'daftartindakan_id', 'alatmedis_id', 'tipepaket_id', 'tindakansudahbayar_id', 'carabayar_id', 'pendaftaran_id', 'hasilpemeriksaanrad_id', 'jeniskasuspenyakit_id', 'hasilpemeriksaanrm_id', 'ruangan_id', 'konsulpoli_id', 'pasienmasukpenunjang_id', 'penjamin_id', 'pasienadmisi_id', 'verifikasitagihan_id', 'jurnalrekening_id', 'instruksitindakan_id', 'qty_tindakan', 'dokterpenanggungjawab_id', 'dokterpelaksana_id', 'dokteranastesi_id', 'dokterdelegasi_id', 'bidan1_id', 'bidan2_id', 'perawat1_id', 'perawat2_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['shift_id', 'kelaspelayanan_id', 'kelastanggungan_id', 'pasien_id', 'rencanaoperasi_id', 'instalasi_id', 'daftartindakan_id', 'alatmedis_id', 'tipepaket_id', 'tindakansudahbayar_id', 'carabayar_id', 'pendaftaran_id', 'hasilpemeriksaanrad_id', 'jeniskasuspenyakit_id', 'hasilpemeriksaanrm_id', 'ruangan_id', 'konsulpoli_id', 'pasienmasukpenunjang_id', 'penjamin_id', 'pasienadmisi_id', 'verifikasitagihan_id', 'jurnalrekening_id', 'instruksitindakan_id', 'qty_tindakan', 'dokterpenanggungjawab_id', 'dokterpelaksana_id', 'dokteranastesi_id', 'dokterdelegasi_id', 'bidan1_id', 'bidan2_id', 'perawat1_id', 'perawat2_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['kelaspelayanan_id', 'pasien_id', 'instalasi_id', 'carabayar_id', 'pendaftaran_id', 'jeniskasuspenyakit_id', 'ruangan_id', 'penjamin_id', 'tgl_tindakan'], 'required'],
            [['kamarruangan_id', 'kamartempattidur_id', 'tgl_tindakan', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['tarif_rsakomodasi', 'tarif_medis', 'tarif_paramedis', 'tarif_bhp', 'tarif_satuan', 'tarif_tindakan', 'tarifcyto_tindakan', 'discount_tindakan', 'pembebasan_tindakan', 'subsidiasuransi_tindakan', 'subsidipemerintah_tindakan', 'subsisidirumahsakit_tindakan', 'uangditerima_tindakan', 'pembulatan'], 'number'],
            [['cyto_tindakan', 'is_deleted', 'is_active'], 'boolean'],
            [['keterangantindakan', 'additional_data'], 'string'],
            [['satuan_tindakan'], 'string', 'max' => 10],
            // [['alatmedis_id'], 'exist', 'skipOnError' => true, 'targetClass' => AlatmedisM::className(), 'targetAttribute' => ['alatmedis_id' => 'alatmedis_id']],
            // [['carabayar_id'], 'exist', 'skipOnError' => true, 'targetClass' => CarabayarM::className(), 'targetAttribute' => ['carabayar_id' => 'carabayar_id']],
            // [['daftartindakan_id'], 'exist', 'skipOnError' => true, 'targetClass' => DaftartindakanM::className(), 'targetAttribute' => ['daftartindakan_id' => 'daftartindakan_id']],
            // [['detailhasilpemeriksaanlab_id'], 'exist', 'skipOnError' => true, 'targetClass' => DetailhasilpemeriksaanlabT::className(), 'targetAttribute' => ['detailhasilpemeriksaanlab_id' => 'detailhasilpemeriksaanlab_id']],
            // [['hasilpemeriksaanpa_id'], 'exist', 'skipOnError' => true, 'targetClass' => HasilpemeriksaanpaT::className(), 'targetAttribute' => ['hasilpemeriksaanpa_id' => 'hasilpemeriksaanpa_id']],
            // [['hasilpemeriksaanrad_id'], 'exist', 'skipOnError' => true, 'targetClass' => HasilpemeriksaanradT::className(), 'targetAttribute' => ['hasilpemeriksaanrad_id' => 'hasilpemeriksaanrad_id']],
            // [['hasilpemeriksaanrm_id'], 'exist', 'skipOnError' => true, 'targetClass' => HasilpemeriksaanrmT::className(), 'targetAttribute' => ['hasilpemeriksaanrm_id' => 'hasilpemeriksaanrehabmedik_id']],
            // [['instalasi_id'], 'exist', 'skipOnError' => true, 'targetClass' => InstalasiM::className(), 'targetAttribute' => ['instalasi_id' => 'instalasi_id']],
            // [['jeniskasuspenyakit_id'], 'exist', 'skipOnError' => true, 'targetClass' => JeniskasuspenyakitM::className(), 'targetAttribute' => ['jeniskasuspenyakit_id' => 'jeniskasuspenyakit_id']],
            // [['jurnalrekening_id'], 'exist', 'skipOnError' => true, 'targetClass' => JurnalrekeningT::className(), 'targetAttribute' => ['jurnalrekening_id' => 'jurnalrekening_id']],
            // [['kelaspelayanan_id'], 'exist', 'skipOnError' => true, 'targetClass' => KelaspelayananM::className(), 'targetAttribute' => ['kelaspelayanan_id' => 'kelaspelayanan_id']],
            // [['kelastanggungan_id'], 'exist', 'skipOnError' => true, 'targetClass' => KelaspelayananM::className(), 'targetAttribute' => ['kelastanggungan_id' => 'kelaspelayanan_id']],
            // [['pasien_id'], 'exist', 'skipOnError' => true, 'targetClass' => PasienM::className(), 'targetAttribute' => ['pasien_id' => 'pasien_id']],
            // [['pasienadmisi_id'], 'exist', 'skipOnError' => true, 'targetClass' => PasienadmisiT::className(), 'targetAttribute' => ['pasienadmisi_id' => 'pasienadmisi_id']],
            // [['pasienmasukpenunjang_id'], 'exist', 'skipOnError' => true, 'targetClass' => PasienmasukpenunjangT::className(), 'targetAttribute' => ['pasienmasukpenunjang_id' => 'pasienmasukpenunjang_id']],
            // [['pendaftaran_id'], 'exist', 'skipOnError' => true, 'targetClass' => PendaftaranT::className(), 'targetAttribute' => ['pendaftaran_id' => 'pendaftaran_id']],
            // [['penjamin_id'], 'exist', 'skipOnError' => true, 'targetClass' => PenjaminM::className(), 'targetAttribute' => ['penjamin_id' => 'penjamin_id']],
            // [['rencanaoperasi_id'], 'exist', 'skipOnError' => true, 'targetClass' => RencanaoperasiT::className(), 'targetAttribute' => ['rencanaoperasi_id' => 'rencanaoperasi_id']],
            // [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => RuanganM::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],
            // [['verifikasitagihan_id'], 'exist', 'skipOnError' => true, 'targetClass' => VerifikasitagihanT::className(), 'targetAttribute' => ['verifikasitagihan_id' => 'verifikasitagihan_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tindakanpelayanan_id' => 'Tindakanpelayanan ID',
            'detailhasilpemeriksaanlab_id' => 'Detailhasilpemeriksaanlab ID',
            'shift_id' => 'Shift ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'kelastanggungan_id' => 'Kelastanggungan ID',
            'pasien_id' => 'Pasien ID',
            'rencanaoperasi_id' => 'Rencanaoperasi ID',
            'instalasi_id' => 'Instalasi ID',
            'daftartindakan_id' => 'Daftartindakan ID',
            'alatmedis_id' => 'Alatmedis ID',
            'tipepaket_id' => 'Tipepaket ID',
            'tindakansudahbayar_id' => 'Tindakansudahbayar ID',
            'carabayar_id' => 'Carabayar ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'hasilpemeriksaanrad_id' => 'Hasilpemeriksaanrad ID',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'hasilpemeriksaanrm_id' => 'Hasilpemeriksaanrm ID',
            'ruangan_id' => 'Ruangan ID',
            'konsulpoli_id' => 'Konsulpoli ID',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'hasilpemeriksaanpa_id' => 'Hasilpemeriksaanpa ID',
            'penjamin_id' => 'Penjamin ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'verifikasitagihan_id' => 'Verifikasitagihan ID',
            'jurnalrekening_id' => 'Jurnalrekening ID',
            'instruksitindakan_id' => 'Instruksitindakan ID',
            'tgl_tindakan' => 'Tgl Tindakan',
            'tarif_rsakomodasi' => 'Tarif Rsakomodasi',
            'tarif_medis' => 'Tarif Medis',
            'tarif_paramedis' => 'Tarif Paramedis',
            'tarif_bhp' => 'Tarif Bhp',
            'tarif_satuan' => 'Tarif Satuan',
            'tarif_tindakan' => 'Tarif Tindakan',
            'tarifcyto_tindakan' => 'Tarifcyto Tindakan',
            'satuan_tindakan' => 'Satuan Tindakan',
            'qty_tindakan' => 'Qty Tindakan',
            'cyto_tindakan' => 'Cyto Tindakan',
            'dokterpenanggungjawab_id' => 'Dokterpenanggungjawab ID',
            'dokterpelaksana_id' => 'Dokterpelaksana ID',
            'dokteranastesi_id' => 'Dokteranastesi ID',
            'dokterdelegasi_id' => 'Dokterdelegasi ID',
            'bidan1_id' => 'Bidan1 ID',
            'bidan2_id' => 'Bidan2 ID',
            'perawat1_id' => 'Perawat1 ID',
            'perawat2_id' => 'Perawat2 ID',
            'discount_tindakan' => 'Discount Tindakan',
            'pembebasan_tindakan' => 'Pembebasan Tindakan',
            'subsidiasuransi_tindakan' => 'Subsidiasuransi Tindakan',
            'subsidipemerintah_tindakan' => 'Subsidipemerintah Tindakan',
            'subsisidirumahsakit_tindakan' => 'Subsisidirumahsakit Tindakan',
            'uangditerima_tindakan' => 'Uangditerima Tindakan',
            'keterangantindakan' => 'Keterangantindakan',
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
            'pembulatan' => 'Pembulatan',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetailhasilpemeriksaanlabTs()
    {
        return $this->hasMany(DetailhasilpemeriksaanlabT::className(), ['tindakanpelayanan_id' => 'tindakanpelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHasilpemeriksaanradTs()
    {
        return $this->hasMany(HasilpemeriksaanradT::className(), ['tindakanpelayanan_id' => 'tindakanpelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHasilpemeriksaanrmTs()
    {
        return $this->hasMany(HasilpemeriksaanrmT::className(), ['tindakanpelayanan_id' => 'tindakanpelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermintaanmcuTs()
    {
        return $this->hasMany(PermintaanmcuT::className(), ['tindakanpelayanan_id' => 'tindakanpelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRencanaoperasiTs()
    {
        return $this->hasMany(RencanaoperasiT::className(), ['tindakanpelayanan_id' => 'tindakanpelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlatmedis()
    {
        return $this->hasOne(AlatmedisM::className(), ['alatmedis_id' => 'alatmedis_id']);
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
    public function getDaftartindakan()
    {
        return $this->hasOne(DaftartindakanM::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetailhasilpemeriksaanlab()
    {
        return $this->hasOne(DetailhasilpemeriksaanlabT::className(), ['detailhasilpemeriksaanlab_id' => 'detailhasilpemeriksaanlab_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHasilpemeriksaanpa()
    {
        return $this->hasOne(HasilpemeriksaanpaT::className(), ['hasilpemeriksaanpa_id' => 'hasilpemeriksaanpa_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHasilpemeriksaanrad()
    {
        return $this->hasOne(HasilpemeriksaanradT::className(), ['hasilpemeriksaanrad_id' => 'hasilpemeriksaanrad_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHasilpemeriksaanrm()
    {
        return $this->hasOne(HasilpemeriksaanrmT::className(), ['hasilpemeriksaanrehabmedik_id' => 'hasilpemeriksaanrm_id']);
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
    public function getJurnalrekening()
    {
        return $this->hasOne(JurnalrekeningT::className(), ['jurnalrekening_id' => 'jurnalrekening_id']);
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
    public function getKelastanggungan()
    {
        return $this->hasOne(KelaspelayananM::className(), ['kelaspelayanan_id' => 'kelastanggungan_id']);
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
    public function getPasienmasukpenunjang()
    {
        return $this->hasOne(PasienmasukpenunjangT::className(), ['pasienmasukpenunjang_id' => 'pasienmasukpenunjang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendaftaran()
    {
        return $this->hasOne(PendaftaranT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
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
    public function getRencanaoperasi()
    {
        return $this->hasOne(RencanaoperasiT::className(), ['rencanaoperasi_id' => 'rencanaoperasi_id']);
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
    public function getVerifikasitagihan()
    {
        return $this->hasOne(VerifikasitagihanT::className(), ['verifikasitagihan_id' => 'verifikasitagihan_id']);
    }
}
?>
