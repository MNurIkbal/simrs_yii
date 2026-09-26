<?php

/**
 * @Author: Naufal Ziyad L
 * @Date:   2018-01-31 15:53
 */

namespace app\modules\v1\models;

use Yii;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers; 
use SirsCore\models\LogActivityR;   
use app\modules\v1\models\Pasien;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "pendaftaran_t".
 *
 * @property int $pendaftaran_id
 * @property string $no_pendaftaran
 * @property string $tgl_pendaftaran
 * @property int $pasienpulang_id
 * @property int $pasienbatalperiksa_id
 * @property int $penanggungjawab_id
 * @property int $penjamin_id
 * @property int $shift_id
 * @property int $pasien_id
 * @property int $persalinan_id
 * @property int $pegawai_id
 * @property int $instalasi_id
 * @property int $caramasuk_id
 * @property int $pengirimanrm_id
 * @property int $peminjamanrm_id
 * @property int $jeniskasuspenyakit_id
 * @property int $pembayaranpelayanan_id
 * @property int $kelaspelayanan_id
 * @property int $carabayar_id
 * @property int $pasienadmisi_id
 * @property int $kelompokumur_id
 * @property int $golonganumur_id
 * @property int $rujukan_id
 * @property int $antrian_id
 * @property int $karcis_id
 * @property int $ruangan_id
 * @property string $no_urutantri
 * @property string $transportasi
 * @property string $keadaan_masuk
 * @property string $status_periksa
 * @property string $status_pasien
 * @property string $kunjungan
 * @property bool $alih_status
 * @property bool $by_phone
 * @property bool $kunjungan_rumah
 * @property string $status_masuk
 * @property string $umur
 * @property string $tgl_selesaiperiksa
 * @property string $keterangan_pendaftaran
 * @property bool $nopendaftaran_aktif
 * @property string $status_konfirmasi
 * @property string $tgl_konfirmasi
 * @property string $tgl_renkontrol
 * @property bool $status_farmasi
 * @property bool $panggil_antrian
 * @property int $asuransipasien_id
 * @property string $tgl_akandilayani
 * @property string $statusdok_rekammedik
 * @property int $bpjs_id
 * @property int $sep_id
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
 * @property int $asuransi_id
 *
 * @property AmbiljenazahT[] $ambiljenazahTs
 * @property AnamnesaT[] $anamnesaTs
 * @property AnamnesadietT[] $anamnesadietTs
 * @property AntrianT[] $antrianTs
 * @property AsuhankeperawatanT[] $asuhankeperawatanTs
 * @property BayaruangmukaT[] $bayaruangmukaTs
 * @property BookingkamarT[] $bookingkamarTs
 * @property BuatjanjipoliT[] $buatjanjipoliTs
 * @property DietpasienT[] $dietpasienTs
 * @property HasilmcuT[] $hasilmcuTs
 * @property HasilpemeriksaanlabT[] $hasilpemeriksaanlabTs
 * @property HasilpemeriksaanmcuT[] $hasilpemeriksaanmcuTs
 * @property HasilpemeriksaanradT[] $hasilpemeriksaanradTs
 * @property HasilpemeriksaanrmT[] $hasilpemeriksaanrmTs
 * @property HearingtestT[] $hearingtestTs
 * @property JadwalkunjunganrmT[] $jadwalkunjunganrmTs
 * @property JantungkoronerT[] $jantungkoronerTs
 * @property KesimpulanmcuT[] $kesimpulanmcuTs
 * @property PasienadmisiT[] $pasienadmisiTs
 * @property PasienanastesiT[] $pasienanastesiTs
 * @property PasienbatalperiksaT[] $pasienbatalperiksaTs
 * @property PasienkirimkeunitlainT[] $pasienkirimkeunitlainTs
 * @property PasienmasukpenunjangT[] $pasienmasukpenunjangTs
 * @property PasienpulangT[] $pasienpulangTs
 * @property PemakaianambulansT[] $pemakaianambulansTs
 * @property PemakaianuangmukaT[] $pemakaianuangmukaTs
 * @property PembayaranpelayananT[] $pembayaranpelayananTs
 * @property PembklaimdetailT[] $pembklaimdetailTs
 * @property AntrianT $antrian
 * @property CarabayarM $carabayar
 * @property CaramasukM $caramasuk
 * @property GolonganumurM $golonganumur
 * @property InstalasiM $instalasi
 * @property JeniskasuspenyakitM $jeniskasuspenyakit
 * @property KarcisM $karcis
 * @property KelaspelayananM $kelaspelayanan
 * @property KelompokumurM $kelompokumur
 * @property PasienM $pasien
 * @property PasienadmisiT $pasienadmisi
 * @property PasienbatalperiksaT $pasienbatalperiksa
 * @property PasienpulangT $pasienpulang
 * @property PegawaiM $pegawai
 * @property PembayaranpelayananT $pembayaranpelayanan
 * @property PeminjamanrmT $peminjamanrm
 * @property PenanggungjawabM $penanggungjawab
 * @property PengirimanrmT $pengirimanrm
 * @property PenjaminM $penjamin
 * @property PersalinanT $persalinan
 * @property RuanganM $ruangan
 * @property RujukanT $rujukan
 * @property ShiftM $shift
 * @property PengirimanrmT[] $pengirimanrmTs
 * @property PenjualanresepT[] $penjualanresepTs
 * @property PermintaanmcuT[] $permintaanmcuTs
 * @property PersalinanT[] $persalinanTs
 * @property PesanambulansT[] $pesanambulansTs
 * @property PindahkamarT[] $pindahkamarTs
 * @property RencanatindakanT[] $rencanatindakanTs
 * @property ReturresepT[] $returresepTs
 * @property TindakanpelayananT[] $tindakanpelayananTs
 * @property int $prev_pendaftaran_id
 * @property bool $is_indolab
 * @property string $additional_indolab
 */

class Pendaftaran extends \Doco\components\DocoActiveRecord
{
    public $kelompokumur_id;
    // penanggungjawab
    // public $pj_pengantar;
    // public $pj_nama;
    // public $pj_jk;
    // public $pj_jenis_identitas;
    // public $pj_no_identitas;
    // public $pj_hubungan;
    // public $pj_tempat_lahir;
    // public $pj_tanggal_lahir;
    // public $pj_umur;
    // public $pj_alamat;
    // public $pj_no_telepon;
    // public $pendaftaranol_id;

    protected $xssProtected = [
        'keterangan_pendaftaran',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pendaftaran_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tgl_pendaftaran', 'status_periksa', 'status_pasien', 'kunjungan', 'status_masuk'], 'required'],
            [['pegawai_id'], 'required', 'on' => 'penunjang', 'message' => 'Pegawai ID tidak boleh kosong'],
            [['pegawai_id'], 'safe', 'on' => 'penunjang_styp', 'message' => 'Pegawai ID tidak boleh kosong'],
            [['pasien_id'], 'checkPasien'], // deprecated soon
            [['asuransipasien_id', 'penanggungbiaya_id', 'tgl_pendaftaran', 'tgl_selesaiperiksa', 'tgl_konfirmasi', 'tgl_renkontrol', 'tgl_akandilayani', 'created_date','pegawai_id', 'last_modified_date', 'deleted_date','is_aps','bpjs_id', 'pj_nama', 'pj_no_identitas', 'pj_alamat', 'pj_no_telepon', 'pj_tempat_lahir', 'keterangan_pendaftaran', 'namadepan', 'nama_pasien', 'rt', 'rw', 'kode_pos', 'alamat_pasien', 'no_telepon_pasien', 'pt', 'is_bsl', 'limit_tagihan', 'no_exportexcel', 'dokterpengirim_id', 'styrujukaninstalasi_id', 'is_multipayer', 'petugas_id', 'petugas_tgl_pembuat', 'waktu_kunjungan' , 'dokterpengganti_id', 'prev_pendaftaran_id', 'diagnosa', 'is_indolab', 'referal_pegawai_id', 'referal_luar'], 'safe'],
            [['pasienpulang_id', 'pasienbatalperiksa_id', 'penanggungjawab_id', 'penjamin_id', 'shift_id', 'pasien_id', 'persalinan_id', 'pegawai_id', 'instalasi_id', 'caramasuk_id', 'jeniskasuspenyakit_id', 'pembayaranpelayanan_id', 'kelaspelayanan_id', 'carabayar_id', 'pasienadmisi_id', 'kelompokumur_id', 'golonganumur_id', 'rujukan_id', 'antrian_id', 'karcis_id', 'ruangan_id', 'asuransipasien_id', 'penanggungbiaya_id', 'bpjs_id', 'propinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id', 'pekerjaan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pasienpulang_id', 'pasienbatalperiksa_id', 'penanggungjawab_id', 'penjamin_id', 'shift_id', 'pasien_id', 'persalinan_id', 'pegawai_id', 'instalasi_id', 'caramasuk_id', 'jeniskasuspenyakit_id', 'pembayaranpelayanan_id', 'kelaspelayanan_id', 'carabayar_id', 'pasienadmisi_id', 'kelompokumur_id', 'golonganumur_id', 'rujukan_id', 'antrian_id', 'karcis_id', 'ruangan_id', 'asuransipasien_id', 'penanggungbiaya_id', 'bpjs_id', 'propinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id', 'pekerjaan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'prev_pendaftaran_id'], 'integer'],
            [['alih_status', 'by_phone', 'kunjungan_rumah', 'nopendaftaran_aktif', 'status_farmasi', 'panggil_antrian', 'is_deleted', 'is_active', 'is_skd', 'is_bsl', 'is_multipayer', 'is_indolab'], 'boolean'],
            [['keterangan_pendaftaran', 'additional_data', 'namadepan', 'nama_pasien', 'rt', 'rw', 'kode_pos', 'alamat_pasien', 'no_telepon_pasien', 'pt', 'additional_indolab'], 'string'],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['no_urutantri'], 'string', 'max' => 6],
            [['transportasi', 'keadaan_masuk', /*'status_periksa',*/ 'status_pasien', 'kunjungan',
                /*'status_masuk',*/ 'status_konfirmasi'/*, 'statusdok_rekammedik'*/], 'string', 'max' => 50],
            [['umur'], 'string', 'max' => 30],
            [['no_pendaftaran'], 'unique'],
            [['no_pendaftaran','kelompokumur_id', 'asuransi_id'], 'safe'],
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        if(!$insert) {
            $this->attachBehavior('typecast',\yii\behaviors\AttributeTypecastBehavior::class);
            $this->typecastAttributes();

            $before = [];
            $after = [];

            $fieldsToTrack = [
                'carabayar_id',
                'keterangan_pendaftaran',
                'pegawai_id',
                'penjamin_id',
                'referal_luar',
                'referal_pegawai_id',
                'ruangan_id',
                'tgl_pendaftaran',
                'jeniskasuspenyakit_id',
            ];

            foreach ($changedAttributes as $fieldName => $valueBefore) {
                if(in_array($fieldName, $fieldsToTrack)) {
                    $valueAfter = ArrayHelper::getValue($this->attributes,$fieldName);
                    if($valueBefore !== $valueAfter) {
                        if($fieldName == 'referal_pegawai_id'){
                            if(intval($valueBefore) !== intval($valueAfter)) {
                                $before['referal_pegawai_nama'] = !empty($valueBefore) ? Pegawai::findOne($valueBefore)->nama_pegawai : null;
                                $after['referal_pegawai_nama'] = !empty($valueAfter) ? Pegawai::findOne($valueAfter)->nama_pegawai : null;
                                $before[$fieldName] = (int) $valueBefore;
                                $after[$fieldName] = (int) $valueAfter;
                            }
                        }elseif ($fieldName == 'carabayar_id') {
                            $before['carabayar_nama'] = CaraBayar::findOne($valueBefore)->carabayar_nama;
                            $after['carabayar_nama'] = CaraBayar::findOne($valueAfter)->carabayar_nama;
                        }elseif ($fieldName == 'pegawai_id') {
                            $before['nama_pegawai'] = Pegawai::findOne($valueBefore)->nama_pegawai;
                            $after['nama_pegawai'] = Pegawai::findOne($valueAfter)->nama_pegawai;
                        }elseif ($fieldName == 'penjamin_id') {
                            $before['penjamin_nama'] = Penjamin::findOne($valueBefore)->penjamin_nama;
                            $after['penjamin_nama'] = Penjamin::findOne($valueAfter)->penjamin_nama;
                        }elseif ($fieldName == 'ruangan_id') {
                            $before['ruangan_nama'] = Ruangan::findOne($valueBefore)->ruangan_nama;
                            $after['ruangan_nama'] = Ruangan::findOne($valueAfter)->ruangan_nama;
                        }elseif ($fieldName == 'jeniskasuspenyakit_id') {
                            $before['jeniskasuspenyakit_nama'] = JenisKasusPenyakit::findOne($valueBefore)->jeniskasuspenyakit_nama;
                            $after['jeniskasuspenyakit_nama'] = JenisKasusPenyakit::findOne($valueAfter)->jeniskasuspenyakit_nama;
                        }else{
                            $before[$fieldName] = $valueBefore;
                            $after[$fieldName] = $valueAfter;
                        }
                    }
                }
            }

            if(count($before) > 0 || count($after) >0) {
                $changedSummary = ['before' => $before, 'after' => $after];

                $model = new LogActivityR();
                $model->attributes = [
                    'tgl' => date('Y-m-d H:i:s'),
                    'aksi' => DocoConstants::LA_AKSI_EDIT,
                    'tipe' => 'PENDAFTARAN_RAJAL',
                    'transaksi_id' => $this->pendaftaran_id,
                    'alasan' => $this->keterangan_pendaftaran,
                    'additional_detail' => $changedSummary,
                ];
                $model->save(false);
            }

        }
        return parent::afterSave($insert, $changedAttributes);
    }

    public static function getValueReferal($id)
    {
        return static::find(['pendaftaran_id' => $id])->select(['referal_pegawai_id','referal_luar'])->where(['pendaftaran_id'=>$id])->one();
    }

    // rules
    public function checkPasien($attribute, $params)
    {
        return true;
        $request = Yii::$app->request;
        $pasien_id = $this->pasien_id;
        $query = Yii::$app->db->createCommand("
                    SELECT pendaftaran_id
                    FROM pendaftaran_t
                    WHERE pasien_id = {$pasien_id}
                    AND pasienpulang_id IS NULL
                    AND is_aps = FALSE
                    AND pasienbatalperiksa_id IS NULL
                    AND status_periksa::integer IN (1,2,339,430)
                    AND date_trunc('day', tgl_pendaftaran) = date_trunc('day', now())
                ")->queryOne();
        if (!empty($query)) {
            if ($this->pendaftaran_id != $query['pendaftaran_id']) {
                throw new \yii\db\Exception('Pasien sudah ada di hari yang sama');
                // $this->addError('pasien_id',Yii::t('app','Pasien sudah ada di hari yang sama'));
            }
        }
    }

    public function getPendaftaranByNomorPendaftaran($no_pendaftaran)
    {
        $query = Yii::$app->db->createCommand("
                    SELECT
                        pendaftaran_id
                    FROM pendaftaran_t
                    WHERE no_pendaftaran = '{$no_pendaftaran}'
                ")->queryOne();
        
        if (!empty($query)) {
            return $query;
        }

        return null;
    }

    public function getPendaftaranByNoRekamMedik($no_rekam_medik, $isRanap = false)
    {
        $pasien = Yii::$app->db->createCommand("
                    SELECT
                        pasien_id
                    FROM pasien_m
                    WHERE no_rekam_medik = '{$no_rekam_medik}'
                ")->queryOne();

        $rawQueryPasien = "
            SELECT
                pendaftaran_id
            FROM pendaftaran_t
            WHERE pasien_id = {$pasien['pasien_id']}
        ";

        if($isRanap) {
            $rawQueryPasien = "
                WITH
                    pendaftaran AS (
                        SELECT
                            pendaftaran_id,
                            pasienadmisi_id
                        FROM pendaftaran_t
                        WHERE pasien_id = {$pasien['pasien_id']}
                    )
                SELECT
                    pendaftaran.pendaftaran_id
                FROM pendaftaran
                JOIN pasienadmisi_t ON pendaftaran.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                WHERE pasienadmisi_t.pasienpulang_id IS NULL
                AND pasienadmisi_t.is_active = TRUE
                AND pasienadmisi_t.is_deleted = FALSE
            ";
        }

        $query = Yii::$app->db->createCommand($rawQueryPasien)->queryOne();
        
        if (!empty($query)) {
            return $query;
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'buatjanjipoli_id' => 'Buatjanjipoli ID',
            'tgl_buatjanji' => 'Tgl Buatjanji',
            'antrian_id' => 'Antrian ID',
            'no_antrian' => 'No Antrian',
            'pegawai_id' => 'Pegawai ID',
            'nama_pegawai' => 'Nama Pegawai',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'pasien_id' => 'Pasien ID',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'hari' => 'Hari',
            'tgl_jadwal' => 'Tgl Jadwal',
            'pendaftaran_id' => 'Pendaftaran ID',
            'no_pendaftaran' => 'No Pendaftaran',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'pasienpulang_id' => 'Pasienpulang ID',
            'pasienbatalperiksa_id' => 'Pasienbatalperiksa ID',
            'penanggungjawab_id' => 'Penanggungjawab ID',
            'penjamin_id' => 'Penjamin ID',
            'shift_id' => 'Shift ID',
            'pasien_id' => 'Pasien ID',
            'persalinan_id' => 'Persalinan ID',
            'pegawai_id' => 'Pegawai ID',
            'instalasi_id' => 'Instalasi ID',
            'caramasuk_id' => 'Caramasuk ID',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'pembayaranpelayanan_id' => 'Pembayaranpelayanan ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'carabayar_id' => 'Carabayar ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'kelompokumur_id' => 'Kelompokumur ID',
            'golonganumur_id' => 'Golonganumur ID',
            'rujukan_id' => 'Rujukan ID',
            'antrian_id' => 'Antrian ID',
            'karcis_id' => 'Karcis ID',
            'ruangan_id' => 'Ruangan ID',
            'no_urutantri' => 'No Urutantri',
            'transportasi' => 'Transportasi',
            'keadaan_masuk' => 'Keadaan Masuk',
            'status_periksa' => 'Status Periksa',
            'status_pasien' => 'Status Pasien',
            'kunjungan' => 'Kunjungan',
            'alih_status' => 'Alih Status',
            'by_phone' => 'By Phone',
            'kunjungan_rumah' => 'Kunjungan Rumah',
            'status_masuk' => 'Status Masuk',
            'umur' => 'Umur',
            'tgl_selesaiperiksa' => 'Tgl Selesaiperiksa',
            'keterangan_pendaftaran' => 'Keterangan Pendaftaran',
            'nopendaftaran_aktif' => 'Nopendaftaran Aktif',
            'status_konfirmasi' => 'Status Konfirmasi',
            'tgl_konfirmasi' => 'Tgl Konfirmasi',
            'tgl_renkontrol' => 'Tgl Renkontrol',
            'status_farmasi' => 'Status Farmasi',
            'panggil_antrian' => 'Panggil Antrian',
            'asuransipasien_id' => 'Asuransipasien ID',
            'tgl_akandilayani' => 'Tgl Akandilayani',
            'statusdok_rekammedik' => 'Statusdok Rekammedik',
            'bpjs_id' => 'Bpjs ID',
            'namadepan'=> 'Nama Depan',
            'nama_pasien'=> 'Nama Pasien',
            'propinsi_id'=> 'Propinsi',
            'kabupaten_id'=> 'Kabupaten',
            'kecamatan_id'=> 'Kecamatan',
            'kelurahan_id'=> 'Kelurahan',
            'rt'=> 'RT',
            'rw'=> 'RW',
            'kode_pos'=> 'Kode Pos',
            'alamat_pasien'=> 'Alamat Pasien',
            'no_telepon_pasien'=> 'No Telepon Pasien',
            'pekerjaan_id'=> 'pekerjaan_id',
            'pt'=> 'PT',
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
            'prev_pendaftaran_id' => 'Pendaftaran Sebelumnya',
            'diagnosa' => 'Diagnosa',
            'is_bsl' => 'Is BSL',
            'is_indolab' => 'Is Indolab',
            'additional_indolab' => 'Additional Indolab',
        ];
    }

    public function getLastSequencePendaftaran($jadwalDokterId, $date)
    {
        $getSlot ="SELECT
                        b.slot_sequence 
                    FROM
                        jadwaldokter_m
                        A JOIN slotjadwaldokter_m b ON b.jadwaldokter_id = A.jadwaldokter_id
                        JOIN pegawai_m C ON C.pegawai_id = A.pegawai_id 
                    WHERE
                        C.is_active = TRUE 
                        AND C.is_deleted = FALSE 
                        AND b.jadwaldokter_id = :jadwalDokterId
                        AND b.slot_sequence NOT IN (
                        SELECT
                            b.slot_sequence 
                        FROM
                            pendaftaranol_t a 
                            JOIN antrian_t b ON b.antrian_id = a.antrian_id 
                        WHERE
                            a.tgl_pendaftaranol::DATE = :date 
                            AND b.jadwaldokter_id = :jadwalDokterId
                            AND b.slot_sequence IS NOT NULL 
                        UNION ALL
                            SELECT
                                b.slot_sequence 
                            FROM
                                pendaftaran_t a 
                                JOIN antrian_t b ON b.antrian_id = a.antrian_id 
                            WHERE
                                a.tgl_pendaftaran::DATE = :date 
                                AND b.jadwaldokter_id = :jadwalDokterId
                                AND b.slot_sequence IS NOT NULL 
                        ) 
                    GROUP BY
                        b.slot_sequence 
                    ORDER BY
                        b.slot_sequence
                        ";
        
        return Yii::$app->db->createCommand($getSlot)
            ->bindValue(':jadwalDokterId', $jadwalDokterId)
            ->bindValue(':date', $date)
            ->queryOne();
    }

    public function getBpjs()
    {
        return $this->hasOne(Bpjs::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    public function getKunjunganPasien($no_rekam_medik = '', $ruangan_id = '', $status_periksa_not = [])
    {
        if (empty($no_rekam_medik)) {
            return [];
        }
        return self::getKunjunganPasienRaw($no_rekam_medik, $ruangan_id, $status_periksa_not)->asArray()->all();
    }

    public function getKunjunganPasienOne($no_rekam_medik = '', $ruangan_id = '', $status_periksa_not = [])
    {
        if (empty($no_rekam_medik)) {
            return [];
        }
        return self::getKunjunganPasienRaw($no_rekam_medik, $ruangan_id, $status_periksa_not)->asArray()->one();
    }

    public function validasiKunjunganPasien($no_rekam_medik)
    {
        if (empty($no_rekam_medik)) {
            return null;
        }

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        $subquery = self::getKunjunganPasienRaw($no_rekam_medik, '', [], true)->select([
            'pendaftaran_t.pendaftaran_id',
            'pm.pasien_id',
            'pm.no_rekam_medik',
            'pm.nama_pasien',
            'pendaftaran_t.status_periksa',
            'pt2.status_ranap',
            new \yii\db\Expression('
                CASE
                    WHEN pt2.pasienadmisi_id IS NULL THEN
                        rm.ruangan_nama
                    ELSE
                        rm_admisi.ruangan_nama
                END AS ruangan_nama
            '),
            new \yii\db\Expression('
                CASE
                    WHEN pt2.pasienadmisi_id IS NULL THEN pasienpulang.tglpasienpulang
                    WHEN pt2.pasienadmisi_id IS NOT NULL AND pasienpulangri.tglpasienpulang IS NULL THEN pendaftaran_t.tgl_stopakomodasi
                    ELSE pasienpulangri.tglpasienpulang
                END AS tglpasienpulang
            ')
        ]);

        if(is_null($subquery)) {
            return null;
        }

        // creating this subquery because of the need to filter tglpasienpulang based on case conditions
        $query = (new \yii\db\Query())
            ->from(['sub' => $subquery])
            ->andWhere(['or', 
                ['and', ['between', 'sub.tglpasienpulang', $start, $end], ['or', ['in', 'sub.status_periksa', [4, 433]], ['sub.status_ranap' => 487]]],
                ['not in', 'sub.status_periksa', [4, 433]] 
            ]);
     
        return $query->one();
    }

    private function getKunjunganPasienRaw($no_rekam_medik, $ruangan_id = '', $status_periksa_not = [], $validasi_pendaftaran = false)
    {
        $query = Pendaftaran::find()->select([
            'pendaftaran_t.pendaftaran_id',
            'pendaftaran_t.no_pendaftaran',
            new \yii\db\Expression('
                CASE
                    WHEN pt2.pasienadmisi_id IS NULL THEN
                        im.instalasi_id
                    ELSE
                        im_admisi.instalasi_id
                END AS ins_id
            '),
            new \yii\db\Expression('
                CASE
                    WHEN pt2.pasienadmisi_id IS NULL THEN
                        rm.ruangan_id
                    ELSE
                        rm_admisi.ruangan_id
                END AS rua_id
            '),
            new \yii\db\Expression('
                CASE
                    WHEN pt2.pasienadmisi_id IS NULL THEN
                        rm.ruangan_nama
                    ELSE
                        rm_admisi.ruangan_nama
                END AS rua_nama
            '),
            new \yii\db\Expression('
                CASE
                    WHEN pt2.pasienadmisi_id IS NULL THEN
                        im.instalasi_nama
                    ELSE
                        im_admisi.instalasi_nama
                END AS ins_nama
            '),
            'pendaftaran_t.pasienpulang_id',
            'pt2.pasienpulang_id AS pasienpulangri_id',
            'pendaftaran_t.status_periksa',
            'pt2.status_ranap',
            new \yii\db\Expression('
                CASE
                    WHEN pt2.pasienadmisi_id IS NULL THEN pasienpulang.tglpasienpulang
                    WHEN pt2.pasienadmisi_id IS NOT NULL AND pasienpulangri.tglpasienpulang IS NULL THEN pendaftaran_t.tgl_stopakomodasi
                    ELSE pasienpulangri.tglpasienpulang
                END AS tglpasienpulang
            ')
        ])
        ->leftJoin('pasien_m pm', 'pm.pasien_id = pendaftaran_t.pasien_id')
        ->leftJoin('pasienadmisi_t pt2', 'pt2.pasienadmisi_id = pendaftaran_t.pasienadmisi_id')
        ->leftJoin('pasienpulang_t pasienpulang', 'pasienpulang.pasienpulang_id = pendaftaran_t.pasienpulang_id')
        ->leftJoin('pasienpulang_t pasienpulangri', 'pasienpulangri.pasienpulang_id = pt2.pasienpulang_id')
        ->leftJoin('ruangan_m rm', 'rm.ruangan_id = pendaftaran_t.ruangan_id')
        ->leftJoin('ruangan_m rm_admisi', 'rm_admisi.ruangan_id = pt2.ruangan_id')
        ->leftJoin('instalasi_m im', 'im.instalasi_id = rm.instalasi_id')
        ->leftJoin('instalasi_m im_admisi', 'im_admisi.instalasi_id = rm_admisi.instalasi_id');

        $query = $query->andWhere(['=', 'pm.no_rekam_medik', $no_rekam_medik]);

        if ($validasi_pendaftaran) {
            $query->andWhere(['date(pendaftaran_t.tgl_pendaftaran)' => date('Y-m-d')]);
            $query->andWhere(['pendaftaran_t.pasienbatalperiksa_id' => null]);
            $query->orderBy(['pendaftaran_t.tgl_pendaftaran' => SORT_DESC]);
        } else {
            if (!empty($status_periksa_not)) {
            $query = $query->andWhere(['not in', 'pendaftaran_t.status_periksa', $status_periksa_not]);
            } else {
                $query = $query->andWhere(['not in', 'pendaftaran_t.status_periksa', [4, 402, 411, 433]]); // pulang, btl periksa, btl konsul, rujuk ranap
            }

            if (!empty($ruangan_id)) {
                $query = $query->andWhere(['or', ['rm.ruangan_id' => $ruangan_id], ['rm_admisi.ruangan_id' => $ruangan_id]]);
            }
        }

        return $query;

    }
}
