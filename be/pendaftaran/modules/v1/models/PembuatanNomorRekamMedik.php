<?php

namespace app\modules\v1\models;

use Yii;
use app\modules\v1\models\GolonganUmur;
use Doco\components\DocoConstants;

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
class PembuatanNomorRekamMedik extends \Doco\components\DocoActiveRecord
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
        'alamatemail'
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
                    'nama_pasien', 'jeniskelamin', 'tanggal_lahir'
                ], 
                'required', 'on' => 'pendaftaran-rajal'
            ],
            [
                [
                    'nopeserta_bpjs',
                    'no_kartu_keluarga',
                    'nama_pasien',
                    // 'tempat_lahir', 
                    'tanggal_lahir',
                    'alamat_pasien', 
                    'jeniskelamin', 
                    'propinsi_id',
                    'kabupaten_id',
                    'kecamatan_id',
                    'kelurahan_id',
                    'rt',
                    'rw',
                    // 'no_telepon_pasien',
                    // 'no_identitas_pasien',
                ], 
                'required', 'on' => 'pasien-jkn',
                'message'=>'{attribute} Belum Diisi'
            ],
            [
                [
                    'nopeserta_bpjs',
                    // 'tempat_lahir', 
                    'tanggal_lahir',
                    // 'no_telepon_pasien',
                    // 'no_identitas_pasien',
                ], 
                'required', 'on' => 'update-pasien-jkn',
                'message'=>'{attribute} Belum Diisi'
            ],
            [['no_rekam_medik', 'tgl_rekam_medik', 'tanggal_lahir', 'tgl_meninggal', 'created_date', 'last_modified_date', 'deleted_date', 'created_date', 'is_deleted', 'is_active','photopasien','is_aps', 'warga_negara', 'nama_ibu', 'agama', 'nama_panggilan', 'namadepan','nopeserta_bpjs', 'alamatemail', 'catatanpenting_pasien','no_kartu_keluarga'], 'safe'],
            [['golonganumur_id', 'rt', 'rw', 'propinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id', 'pendidikan_id', 'pekerjaan_id', 'suku_id', 'anakke', 'jumlah_bersaudara', 'dokrekammedis_id', 'pegawai_id', 'loginpemakai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'profilrs_id'], 'default', 'value' => null],
            [['golonganumur_id', 'rt', 'rw', 'propinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id', 'pendidikan_id', 'pekerjaan_id', 'suku_id', 'anakke', 'jumlah_bersaudara', 'dokrekammedis_id', 'pegawai_id', 'loginpemakai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'profilrs_id'], 'integer'],
            [['alamat_pasien', 'additional_data', 'alamat_sekarang', 'additional_pasien'], 'string'],
            [['garis_latitude', 'garis_longitude'], 'number'],
            [['is_deleted', 'is_active','is_aps'], 'boolean'],
            [['no_rekam_medik'], 'string', 'max' => 10],
            ['statusrekammedis','default','value'=>336],
            [['jenisidentitas', /*'namadepan',*/ 'jeniskelamin', 'agama', 'rhesus', 'no_mobile_pasien', 'statusperkawinan', 'golongandarah'], 'string', 'max' => 20],
            [['no_identitas_pasien'/*, 'nama_panggilan'*/], 'string', 'max' => 30],
            [['nama_pasien', 'nama_ibu', 'nama_ayah'], 'string', 'max' => 50],
            [['tempat_lahir', 'warga_negara'], 'string', 'max' => 25],
            [['no_telepon_pasien'], 'string', 'max' => 15],
            [['photopasien'], 'string', 'max' => 200],
            [['alamatemail'], 'string', 'max' => 100],
            [['no_rekam_medik'], 'unique'],
            [['nama_pasien'], 'uniquePasienValidator'],
            [['statusrekammedis'], 'default', 'value'=>DocoConstants::STAT_RM_AKTIF],
        ];
    }


    public function uniquePasienValidator($attribute, $params)
    {
        $nama_pasien = $this->nama_pasien;
        $tanggal_lahir = $this->tanggal_lahir;
        $pasien_id = $this->pasien_id;
        $nopeserta_bpjs = $this->nopeserta_bpjs;
        if (empty($pasien_id)) {
            // if ($this->scenario == 'pasien-jkn') {
                $tmpArray = json_decode($this->additional_pasien,true);
                $buildJson = null;
                if (is_array($tmpArray) && isset($tmpArray[0])) {
                    $buildJson = json_encode($tmpArray[0]);
                }


                if (!empty($this->nopeserta_bpjs)) {
                    $model = Pasien::find()
                        ->andWhere(['and',
                           // ['tanggal_lahir' => $this->tanggal_lahir],
                           // ['jeniskelamin' => $this->jeniskelamin],
                           ['nopeserta_bpjs' => $this->nopeserta_bpjs],
                       ])
                       ->orWhere(['and',
                           // ['tanggal_lahir' => $this->tanggal_lahir],
                           // ['jeniskelamin' => $this->jeniskelamin],
                           ['ilike', 'additional_pasien', $buildJson],
                       ])
                       ->orWhere(['and',
                           // ['tanggal_lahir' => $this->tanggal_lahir],
                           // ['jeniskelamin' => $this->jeniskelamin],
                           ['no_identitas_pasien'=> $this->no_identitas_pasien],
                           ['jenisidentitas'=> $this->jenisidentitas],
                       ])->one();
                } else {
                    $model = Pasien::find()
                       ->andWhere(['and',
                           // ['tanggal_lahir' => $this->tanggal_lahir],
                           // ['jeniskelamin' => $this->jeniskelamin],
                           ['ilike', 'additional_pasien', $buildJson],
                       ])
                       ->orWhere(['and',
                           // ['tanggal_lahir' => $this->tanggal_lahir],
                           // ['jeniskelamin' => $this->jeniskelamin],
                           ['no_identitas_pasien'=> $this->no_identitas_pasien],
                           ['jenisidentitas'=> $this->jenisidentitas],
                       ])->one();
                }

                // $model = Pasien::find()
                //     ->andWhere(['and',
                //        ['tanggal_lahir' => $this->tanggal_lahir],
                //        ['jeniskxelamin' => $this->jeniskelamin],
                //        ['nopeserta_bpjs' => $this->nopeserta_bpjs],
                //    ])
                //    ->orWhere(['and',
                //        ['tanggal_lahir' => $this->tanggal_lahir],
                //        ['jeniskelamin' => $this->jeniskelamin],
                //        ['ilike', 'additional_pasien', $buildJson],
                //    ])->one();
            // } else {
                // $model = Pasien::find()
                // ->andWhere([
                //     'LOWER(nama_pasien)'=>strtolower($nama_pasien),
                //     'tanggal_lahir'=>$tanggal_lahir,
                // ])->one();
            // }

            if ($model) {
                $error = $this->addError($attribute, 'Pasien sudah terdaftar. No Rekam Medik : ' . $model->no_rekam_medik);
                Yii::$app->response->statusCode = 422;
                return [
                    'data' => $error,
                    'status' => 422
                ];
            }
            return true;
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        switch( $this->getScenario() )
        {
            case 'pasien-jkn':
                return [
                    'pasien_id' => 'Pasien ID',
                    'no_rekam_medik' => 'No Rekam Medik',
                    'tgl_rekam_medik' => 'Tgl Rekam Medik',
                    'jenisidentitas' => 'Jenis Identitas',
                    'no_identitas_pasien' => 'No Identitas Pasien',
                    'namadepan' => 'Namadepan',
                    'nama_pasien' => 'Nama Pasien',
                    'nama_panggilan' => 'Nama Panggilan',
                    'jeniskelamin' => 'Jenis Kelamin',
                    'tempat_lahir' => 'Tempat Lahir',
                    'tanggal_lahir' => 'Tanggal Lahir',
                    'golonganumur_id' => 'Golonganumur ID',
                    'alamat_pasien' => 'Alamat Pasien',
                    'rt' => 'RT',
                    'rw' => 'RW',
                    'propinsi_id' => 'Kode Propinsi',
                    'kabupaten_id' => 'Kode Dati 2',
                    'kecamatan_id' => 'Kode Kecamatan',
                    'kelurahan_id' => 'Kode Kelurahan',
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
                    'nopeserta_bpjs' => 'Nomor Kartu',
                    'no_kartu_keluarga' => 'Nomor KK',
                ];
                break;
            default:
                return [
                    'pasien_id' => 'Pasien ID',
                    'no_rekam_medik' => 'No Rekam Medik',
                    'tgl_rekam_medik' => 'Tgl Rekam Medik',
                    'jenisidentitas' => 'Jenis Identitas',
                    'no_identitas_pasien' => 'No Identitas Pasien',
                    'namadepan' => 'Namadepan',
                    'nama_pasien' => 'Nama Pasien',
                    'nama_panggilan' => 'Nama Bin',
                    'jeniskelamin' => 'Jenis Kelamin',
                    'tempat_lahir' => 'Tempat Lahir',
                    'tanggal_lahir' => 'Tanggal Lahir',
                    'golonganumur_id' => 'Golonganumur ID',
                    'alamat_pasien' => 'Alamat Pasien',
                    'rt' => 'RT',
                    'rw' => 'RW',
                    'propinsi_id' => 'Propinsi',
                    'kabupaten_id' => 'Kabupaten',
                    'kecamatan_id' => 'Kecamatan',
                    'kelurahan_id' => 'Kelurahan',
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
                    'nopeserta_bpjs' => 'Nomor Kartu',
                    'no_kartu_keluarga' => 'Nomor KK',
                ];
                break;
        }
    }

    public function getGolonganUmurPasien()
    {
        $diffHari = self::convertToHari($this->tanggal_lahir);
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
}