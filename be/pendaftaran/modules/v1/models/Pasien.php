<?php

namespace app\modules\v1\models;

use Yii;
use app\modules\v1\models\GolonganUmur;
use Doco\components\DocoConstants;
use yii\web\UnprocessableEntityHttpException;

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
 * @property string $nama_panggilan
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
 * @property string $is_mergerm
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
    protected $xssProtected = [
        'no_identitas_pasien',
        'nama_pasien',
        'nama_panggilan',
        'tempat_lahir',
        'nama_ibu',
        'nama_ayah',
        'alamat_pasien',
        'no_telepon_pasien',
        'no_mobile_pasien',
        'alamatemail',
        'catatanpenting_pasien'
    ];
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
            [
                [
                    'tgl_rekam_medik', 'nama_pasien', 
                    'jeniskelamin', 'tanggal_lahir', 'golonganumur_id', 'alamat_pasien',
                    'jeniskelamin', 'tanggal_lahir',
                    'alamat_pasien', 'statusperkawinan', 'golongandarah', 
                    'tempat_lahir'
                ], 
                'required', 'except' => 'fix-error-update'
            ],
            [
                [
                    'nama_pasien', 
                    'jeniskelamin', 'tanggal_lahir'
                ], 
                'required', "on" => "fix-error-update",
            ],
            [['no_rekam_medik', 'tgl_rekam_medik', 'tanggal_lahir', 'tgl_meninggal', 'created_date', 'last_modified_date', 'deleted_date', 'created_date', 'is_deleted', 'is_active','photopasien','is_aps', 'warga_negara', 'nama_ibu', 'agama', 'nama_panggilan', 'namadepan','nopeserta_bpjs', 'alamatemail', 'catatanpenting_pasien', 'bahasa_sehari', 'alamatdepan'], 'safe'],
            [['golonganumur_id', 'rt', 'rw', 'propinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id', 'pendidikan_id', 'pekerjaan_id', 'suku_id', 'anakke', 'jumlah_bersaudara', 'dokrekammedis_id', 'pegawai_id', 'loginpemakai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'profilrs_id'], 'default', 'value' => null],
            [['golonganumur_id', 'rt', 'rw', 'propinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id', 'pendidikan_id', 'pekerjaan_id', 'suku_id', 'anakke', 'jumlah_bersaudara', 'dokrekammedis_id', 'pegawai_id', 'loginpemakai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'profilrs_id'], 'integer'],
            [['alamat_pasien', 'additional_data', 'alamat_sekarang', 'additional_pasien', 'is_mergerm'], 'string'],
            [['garis_latitude', 'garis_longitude'], 'number'],
            [['is_deleted', 'is_active','is_aps'], 'boolean'],
            [['no_rekam_medik'], 'string', 'max' => 10],
            ['statusrekammedis','default','value'=>336],
            [['jenisidentitas', /*'namadepan',*/ 'jeniskelamin', 'agama', 'rhesus', 'no_mobile_pasien'], 'string', 'max' => 20],
            [['no_identitas_pasien'/*, 'nama_panggilan'*/], 'string', 'max' => 30],
            [['nama_pasien', 'nama_ibu', 'nama_ayah'], 'string', 'max' => 50],
            [['tempat_lahir', 'warga_negara'], 'string', 'max' => 25],
            [['no_telepon_pasien'], 'string', 'max' => 50],
            [['photopasien'], 'string', 'max' => 200],
            [['alamatemail'], 'string', 'max' => 100],
            [['no_rekam_medik'], 'unique'],
            [['nama_pasien', 'tanggal_lahir'], 'uniquePasienValidator'],
            [['statusrekammedis'], 'default', 'value'=>DocoConstants::STAT_RM_AKTIF],
        ];
    }


    public function uniquePasienValidator($attribute, $params)
    {
        if(empty($this->pasien_id)){
            $model = Pasien::find()
                ->andWhere([
                    'LOWER(nama_pasien)'=>strtolower($this->nama_pasien),
                    'tanggal_lahir'=>$this->tanggal_lahir,
                ])->one();

            if ($model) {
                $this->addError($attribute, 'Pasien sudah terdaftar. No Rekam Medik : ' . $model->no_rekam_medik);
            }
        } else {
            $tmpArray = json_decode($this->additional_pasien, true);
            if (!is_array($tmpArray) || empty($tmpArray)) {
                return;
            }

            $arrIdentitas = [];
            foreach ($tmpArray as $item) {
                if (isset($item['jenisidentitas'], $item['no_identitas_pasien'])) {
                    $arrIdentitas[] = [
                        'jenisidentitas' => $item['jenisidentitas'],
                        'no_identitas_pasien' => $item['no_identitas_pasien'],
                    ];
                }
            }
            if (empty($arrIdentitas)) {
                return;
            }

            $query = Pasien::find()
                ->andWhere(['<>', 'pasien_id', $this->pasien_id])
                ->andWhere(['is_deleted' => false])
                ->andWhere(['is_active' => true]);

            $orConditions = ['or'];

            foreach ($arrIdentitas as $identitas) {
                $orConditions[] = ['and',
                    ['jenisidentitas' => $identitas['jenisidentitas']],
                    ['no_identitas_pasien' => $identitas['no_identitas_pasien']],
                ];
            }

            foreach ($arrIdentitas as $identitas) {
                $snippet = sprintf('{"jenisidentitas":"%s","no_identitas_pasien":"%s"}', $identitas['jenisidentitas'], $identitas['no_identitas_pasien']);
                $orConditions[] = ['ilike', 'additional_pasien', $snippet];
            }

            $query->andWhere($orConditions);
            $model = $query->one();

            if ($model) {
                $this->addError($attribute, 'Pasien sudah terdaftar. No Rekam Medik : ' . $model->no_rekam_medik);
            }
        }
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
            'nama_panggilan' => 'Nama Panggilan',
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

    public function getGolonganUmurPasien()
    {
        $diffHari = self::convertToHari($this->tanggal_lahir);
        // return $diffHari;
        $golonganUmur = GolonganUmur::find(true);
        $golonganUmur->andWhere(['<=', 'golonganumur_minimal', $diffHari]);
        $golonganUmur->andWhere(['>=', 'golonganumur_maksimal', $diffHari]);
        $golonganUmur->orderBy('golonganumur_minimal ASC');
        $result = $golonganUmur->one();
        return $result;
    }

    public static function convertToHari($tanggal, $tanggal2=null)
    {
        $tanggal2 = $tanggal2 ? : date('Y-m-d');
        $ts1 = date_create($tanggal);
        $ts2 = date_create($tanggal2);

        $diff = date_diff($ts1,$ts2);
        return $diff->format("%a");
    }

    public function beforeSave($insert = null) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $any_ktp = false;
        $any_bpjs = false;
        $additional_pasien = $this->additional_pasien;
        if(!is_array($this->additional_pasien)) {
            $additional_pasien = json_decode($additional_pasien, true) ? : [];
        }
        $no_identitas_pasien = $this->no_identitas_pasien;
        $jenisidentitas = $this->jenisidentitas;
        $nopeserta_bpjs = $this->nopeserta_bpjs;
        foreach ($additional_pasien as $data) {
            if(isset($data['jenisidentitas']) && isset($data['no_identitas_pasien'])) {
                if($data['jenisidentitas'] == DocoConstants::IDENTITAS_KTP) {
                    $no_identitas_pasien = $data['no_identitas_pasien'];
                    $jenisidentitas = DocoConstants::IDENTITAS_KTP;
                    $any_ktp = true;
                } else if($data['jenisidentitas'] == DocoConstants::IDENTITAS_BPJS) {
                    $nopeserta_bpjs = $data['no_identitas_pasien'];
                    $any_bpjs = true;
                }
            }
        }

        if(!$any_ktp && !empty($no_identitas_pasien) && !empty($jenisidentitas)) {
            $additional_pasien[] = [
                'jenisidentitas' => $jenisidentitas,
                'no_identitas_pasien' => $no_identitas_pasien,
            ];
        }

        if(!$any_bpjs && !empty($nopeserta_bpjs)) {
            $additional_pasien[] = [
                'jenisidentitas' => DocoConstants::IDENTITAS_BPJS,
                'no_identitas_pasien' => $nopeserta_bpjs,
            ];
        }
        $this->additional_pasien = json_encode($additional_pasien);
        $this->no_identitas_pasien = $no_identitas_pasien;
        $this->jenisidentitas = $jenisidentitas;
        $this->nopeserta_bpjs = $nopeserta_bpjs;
        return true;
    }

    public function getPasienBpjs($nobpjs)
    {
        return Yii::$app->db->createCommand('
        SELECT 
            c.nama_pasien,
            c.no_rekam_medik
        FROM bpjs_t a
            JOIN pendaftaran_t b ON a.pendaftaran_id = b.pendaftaran_id
            JOIN pasien_m c ON b.pasien_id = c.pasien_id 
        WHERE a.nokartuasuransi = :nobpjs and c.is_deleted = false and c.is_active = true')
        ->bindParam(':nobpjs', $nobpjs)
        ->queryOne();
    }

    public function getPasienByNoIdentitas($no_identitas_pasien)
    {
        return self::find()
            ->andWhere(['no_identitas_pasien' => $no_identitas_pasien])
            ->andWhere(['is_deleted' => false, 'is_active' => true])
            ->one();
    }
}
