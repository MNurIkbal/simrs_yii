<?php

namespace Doco\models;

use Yii;
use Doco\models\GolonganUmur;
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
        'nama_bin',
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
            [['no_rekam_medik', 'tgl_rekam_medik', 'tanggal_lahir', 'tgl_meninggal', 'created_date', 'last_modified_date', 'deleted_date', 'created_date', 'is_deleted', 'is_active','photopasien','is_aps', 'warga_negara', 'nama_ibu', 'agama', 'nama_bin', 'namadepan','nopeserta_bpjs', 'alamatemail', 'catatanpenting_pasien', 'bahasa_sehari', 'alamatdepan', 'no_rm_old'], 'safe'],
            [['golonganumur_id', 'rt', 'rw', 'propinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id', 'pendidikan_id', 'pekerjaan_id', 'suku_id', 'anakke', 'jumlah_bersaudara', 'dokrekammedis_id', 'pegawai_id', 'loginpemakai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'profilrs_id', 'additional_pasien'], 'default', 'value' => null],
            [['golonganumur_id', 'rt', 'rw', 'propinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id', 'pendidikan_id', 'pekerjaan_id', 'suku_id', 'anakke', 'jumlah_bersaudara', 'dokrekammedis_id', 'pegawai_id', 'loginpemakai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'profilrs_id'], 'integer'],
            [['alamat_pasien', 'additional_data', 'alamat_sekarang', 'additional_pasien', 'is_mergerm'], 'string'],
            [['garis_latitude', 'garis_longitude'], 'number'],
            [['is_deleted', 'is_active','is_aps'], 'boolean'],
            [['no_rekam_medik'], 'string', 'max' => 10],
            ['statusrekammedis','default','value'=>336],
            [['jenisidentitas', /*'namadepan',*/ 'jeniskelamin', 'agama', 'rhesus', 'no_mobile_pasien'], 'string', 'max' => 20],
            [['no_identitas_pasien'/*, 'nama_bin'*/], 'string', 'max' => 30],
            [['nama_pasien', 'nama_ibu', 'nama_ayah'], 'string', 'max' => 50],
            [['tempat_lahir', 'warga_negara'], 'string', 'max' => 25],
            [['no_telepon_pasien'], 'string', 'max' => 15],
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

    //------------------------- Repository sementara ---------------------------/
    public function getInfoPasienByPendaftaranId($pendaftaran_id)
    {
        if (!empty($pendaftaran_id)) {
            $where = ' pendaftaran_t.pendaftaran_id = '.$pendaftaran_id;
            return self::getInfoPasien($where);
        }
        return [];
    }

    private function getInfoPasien($where)
    {
        $sql = 'SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.pegawai_id
                    ELSE pasienadmisi_t.pegawai_id
                END AS dokter_id,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN dok_rj.nama_pegawai
                    ELSE dok_ri.nama_pegawai
                END AS dpjp_nama,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.carabayar_id
                    ELSE pasienadmisi_t.carabayar_id
                END AS carabayar_id,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.penjamin_id
                    ELSE pasienadmisi_t.penjamin_id
                END AS penjamin_id,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.kelaspelayanan_id
                    ELSE COALESCE(pasienadmisi_t.kelas_ditagihkan_id, pasienadmisi_t.kelaspelayanan_id)
                END AS kelaspelayanan_id,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN kelaspelayanan_rj.kelaspelayanan_nama
                    ELSE COALESCE(kelaspelayanan_ri_ditagihkan.kelaspelayanan_nama, kelaspelayanan_ri.kelaspelayanan_nama)
                END AS kelaspelayanan_nama,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.ruangan_id
                    ELSE pasienadmisi_t.ruangan_id
                END AS ruangan_id,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.instalasi_id
                    ELSE ruang_ri.instalasi_id
                END AS instalasi_id,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            CONCAT(nama_depan.lookup_value,\' \',pasien_m.nama_pasien) AS nama,
            pasien_m.tanggal_lahir,
            pasien_m.no_telepon_pasien,
            pasien_m.alamat_pasien,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN instalasi_rj.instalasi_nama
                    ELSE instalasi_ri.instalasi_nama
                END AS instalasi_nama,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN ruang_rj.ruangan_nama
                    ELSE ruang_ri.ruangan_nama
                END AS ruangan_nama,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN carabayar_rj.carabayar_nama
                    ELSE carabayar_ri.carabayar_nama
                END AS carabayar_nama,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN penjamin_rj.penjamin_nama
                    ELSE penjamin_ri.penjamin_nama
                END AS penjamin_nama,
                CASE
                    WHEN pendaftaran_t.instalasi_id = 2 THEN true
                    ELSE false
                END AS is_rd,
            pendaftaran_t.is_stopakomodasi,
            pendaftaran_t.status_periksa,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.status_bayar,
            bpjs.nosep,
                CASE
                    WHEN pasienadmisi_t.is_pasientitipan = true THEN pasienadmisi_t.kelas_ditagihkan_id
                    ELSE NULL::integer
                END AS kelas_titipan
        FROM pendaftaran_t
            LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.pasienpulang_id,
                    a.pegawai_id,
                    a.kelaspelayanan_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.ruangan_id,
                    a.is_pasientitipan,
                    a.kelas_ditagihkan_id,
                    a.status_ranap,
                    a.bpjs_id
                FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            LEFT JOIN ( SELECT b.pegawai_id,
                    b.nama_pegawai
                FROM pegawai_m b) dok_rj ON pendaftaran_t.pegawai_id = dok_rj.pegawai_id
            LEFT JOIN ( SELECT c.pegawai_id,
                    c.nama_pegawai
                FROM pegawai_m c) dok_ri ON pasienadmisi_t.pegawai_id = dok_ri.pegawai_id
            LEFT JOIN ( SELECT d.ruangan_id,
                    d.instalasi_id,
                    d.ruangan_nama
                FROM ruangan_m d) ruang_rj ON pendaftaran_t.ruangan_id = ruang_rj.ruangan_id
            LEFT JOIN ( SELECT d.ruangan_id,
                    d.instalasi_id,
                    d.ruangan_nama
                FROM ruangan_m d) ruang_ri ON pasienadmisi_t.ruangan_id = ruang_ri.ruangan_id
            JOIN ( SELECT e.pasien_id,
                    e.nama_pasien,
                    e.no_rekam_medik,
                    e.tanggal_lahir,
                    e.no_telepon_pasien,
                    e.alamat_pasien,
                    e.namadepan
                FROM pasien_m e) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
            LEFT JOIN ( SELECT f.instalasi_id,
                    f.instalasi_nama
                FROM instalasi_m f) instalasi_rj ON pendaftaran_t.instalasi_id = instalasi_rj.instalasi_id
            LEFT JOIN ( SELECT g.instalasi_id,
                    g.instalasi_nama
                FROM instalasi_m g) instalasi_ri ON ruang_ri.instalasi_id = instalasi_ri.instalasi_id
            LEFT JOIN ( SELECT h.carabayar_id,
                    h.carabayar_nama
                FROM carabayar_m h) carabayar_rj ON pendaftaran_t.carabayar_id = carabayar_rj.carabayar_id
            LEFT JOIN ( SELECT i.carabayar_id,
                    i.carabayar_nama
                FROM carabayar_m i) carabayar_ri ON pasienadmisi_t.carabayar_id = carabayar_ri.carabayar_id
            LEFT JOIN ( SELECT j.penjamin_id,
                    j.penjamin_nama
                FROM penjamin_m j) penjamin_rj ON pendaftaran_t.penjamin_id = penjamin_rj.penjamin_id
            LEFT JOIN ( SELECT k.penjamin_id,
                    k.penjamin_nama
                FROM penjamin_m k) penjamin_ri ON pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id
            LEFT JOIN ( SELECT k.kelaspelayanan_id,
                    k.kelaspelayanan_nama
                FROM kelaspelayanan_m k) kelaspelayanan_ri ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_ri.kelaspelayanan_id
            LEFT JOIN ( SELECT k.kelaspelayanan_id,
                    k.kelaspelayanan_nama
                FROM kelaspelayanan_m k) kelaspelayanan_ri_ditagihkan ON pasienadmisi_t.kelas_ditagihkan_id = kelaspelayanan_ri_ditagihkan.kelaspelayanan_id
            LEFT JOIN ( SELECT k.kelaspelayanan_id,
                    k.kelaspelayanan_nama
                FROM kelaspelayanan_m k) kelaspelayanan_rj ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_rj.kelaspelayanan_id
            LEFT JOIN ( SELECT a.bpjs_id,
                    a.nosep
                    FROM bpjs_t a
                WHERE a.is_deleted IS FALSE) bpjs on COALESCE(pasienadmisi_t.bpjs_id, pendaftaran_t.bpjs_id) = bpjs.bpjs_id
            LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_value
                        FROM lookup_m a) nama_depan ON pasien_m.namadepan::int = nama_depan.lookup_id';
        if (!empty($where)) {
            $sql.= ' WHERE pendaftaran_t.is_deleted = false AND '.$where;
        } else {
            $sql.= ' WHERE pendaftaran_t.is_deleted = false';
        }
        $sql .= ' ORDER BY pendaftaran_t.created_date DESC';

         return Yii::$app->db->createCommand($sql)
                ->queryOne();
    }
}