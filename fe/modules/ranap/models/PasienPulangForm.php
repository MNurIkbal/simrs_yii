<?php

namespace app\modules\ranap\models;

use Yii;

/**
 * This is the model class for table "pasienpulang_t".
 *
 * @property int $pasienpulang_id
 * @property int $pasien_id
 * @property int $pasienbatalpulang_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $carakeluar_id
 * @property int $kondisikeluar_id
 * @property string $tglpasienpulang
 * @property int $ruanganakhir_id
 * @property int $pasiendirujukkeluar_id
 * @property string $penerima_pasien
 * @property int $lama_rawat
 * @property string $satuan_lamarawat
 * @property bool $is_meninggal
 * @property bool $is_rencanakontrol
 * @property string $keterangan_keluar
 * @property int $hari_perawatan
 * @property string $satuan_hariperawatan
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $tgl_meninggal
 * @property string $tgl_rencanakontrol
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property string $deleted_date
 * @property int $deleted_by
 * @property string $status_jenazah
 * @property string $tgl_kremasi
 * @property string $nama_pemeriksa_jenazah
 * @property string $kualifikasi_pemeriksa
 * @property string $waktu_pemeriksaan_jenazah
 * @property string $dasar_diagnosis
 * @property string $kelompok_kematian
 * @property string $tempat_kematian
 * @property string $penyebab_langsung
 * @property string $penyebab_antara
 * @property string $penyebab_dasar
 * @property string $kondisi_lain
 * @property string $penyebab_utama_bayi
 * @property string $penyebab_utama_ibu
 * @property string $penyebab_lain_bayi
 * @property string $penyebab_lain_ibu
 * @property string $pihak_menerima
 * @property string $hubungan_penerima
 * @property string $no_surat_kematian
 * @property string $nama_spesialis
 * @property string $dokterdpjp_kode
 * @property string $dokterdpjp_nama
 * @property string $kode_poli
 * @property string $no_kartu
 *
 */
class PasienPulangForm extends \yii\base\Model
{
     // Public variable
    public $pasienpulang_id;
    public $pasien_id;
    public $pasienbatalpulang_id;
    public $pendaftaran_id;
    public $pasienadmisi_id;
    public $carakeluar_id;
    public $kondisikeluar_id;
    public $tglpasienpulang;
    public $ruanganakhir_id;
    public $penerima_pasien;
    public $lama_rawat;
    public $satuan_lamarawat;
    public $is_meninggal;
    public $keterangan_keluar;
    public $hari_perawatan;
    public $satuan_hariperawatan;
    public $is_deleted;
    public $is_active;
    public $tgl_meninggal;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $deleted_date;
    public $deleted_by;
    public $is_rencanakontrol;
    public $tgl_rencanakontrol;
    public $pasiendirujukkeluar_id;

    public $rujukankeluar_id;
    public $pegawai_id;
    public $nosuratrujukan;
    public $tgldirujuk;
    public $ruanganasal_id;
    public $catatandokterperujuk;
    public $alasandirujuk;
    public $hasilpemeriksaan_ruj;
    public $diagnosasementara_ruj;
    public $pengobatan_ruj;
    public $lainlain_ruj;
    public $is_pelayanan_jenazah;
    public $status_jenazah;
    public $tgl_kremasi;
    public $nama_pemeriksa_jenazah;
    public $kualifikasi_pemeriksa;
    public $waktu_pemeriksaan_jenazah;
    public $dasar_diagnosis;
    public $kelompok_kematian;
    public $tempat_kematian;
    public $penyebab_langsung;
    public $penyebab_antara;
    public $penyebab_dasar;
    public $kondisi_lain;
    public $penyebab_utama_bayi;
    public $penyebab_utama_ibu;
    public $penyebab_lain_bayi;
    public $penyebab_lain_ibu;
    public $pihak_menerima;
    public $hubungan_penerima;
    public $no_surat_kematian;
    public $nama_spesialis;
    public $dokterdpjp_kode;
    public $dokterdpjp_nama;
    public $kode_poli;
    public $no_kartu;

    const SCENARIO_PASIENPULANG = 'pasienPulang';
    const SCENARIO_PASIENDIRUJUK = 'pasienDirujuk';
    const SCENARIO_PASIENMENINGGAL = 'pasienMeninggal';

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['carakeluar_id', 'kondisikeluar_id'], 'required','on' => self::SCENARIO_PASIENPULANG],
            [['carakeluar_id', 'kondisikeluar_id','rujukankeluar_id', 'pegawai_id', 'nosuratrujukan', 'tgldirujuk'], 'required','on' => self::SCENARIO_PASIENDIRUJUK],
            [['carakeluar_id','kondisikeluar_id', 'tgl_rencanakontrol', 'nama_spesialis', 'dokterdpjp_nama'],'required'],
            // ['is_rencanakontrol', 'required', 'when' => function($model) {
            //     return empty($model->tgl_rencanakontrol);
            // },'on'=>[self::SCENARIO_PASIENPULANG,self::SCENARIO_PASIENDIRUJUK],'requiredValue' => 1,'message'=>'Tanggal Rencana Kontrol Harus Diisi'],
            [['is_rencanakontrol'], 'cekTgl'],
            // ['pasiendirujukkeluar_id', 'required', 'when' => function($model) {
            //     return $model->carakeluar_id == '2';
            // },'on'=>[self::SCENARIO_PASIENPULANG,self::SCENARIO_PASIENDIRUJUK],'requiredValue' => 1,'message'=>'Rujuk Keluar Harus Diisi'],
            [['is_meninggal','tgl_meninggal', 'no_surat_kematian'], 'required', 'when' => function ($model) {
                return $model->carakeluar_id == '4';
            }, 'whenClient' => "function (attribute, value) {
                return $('#pasienpulangform-carakeluar_id').val() == '4';
            }"],
            [['pasien_id', 'pasienbatalpulang_id', 'pendaftaran_id', 'pasienadmisi_id', 'carakeluar_id', 'kondisikeluar_id', 'ruanganakhir_id', 'lama_rawat', 'hari_perawatan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by',
                'ruanganasal_id'], 'default', 'value' => null],
            [['pasien_id','pasiendirujukkeluar_id', 'pasienbatalpulang_id', 'pendaftaran_id', 'pasienadmisi_id', 'carakeluar_id', 'kondisikeluar_id', 'ruanganakhir_id', 'lama_rawat', 'hari_perawatan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by',
                'ruanganasal_id'], 'integer'],
            [['tglpasienpulang', 'tgl_meninggal','tgl_rencanakontrol', 'created_date', 'last_modified_date', 'deleted_date','status_jenazah','tgl_kremasi','nama_pemeriksa_jenazah','kualifikasi_pemeriksa','waktu_pemeriksaan_jenazah','dasar_diagnosis','kelompok_kematian','tempat_kematian','penyebab_langsung','penyebab_antara','penyebab_dasar','kondisi_lain','penyebab_utama_bayi','penyebab_utama_ibu','penyebab_lain_bayi','penyebab_lain_ibu','pihak_menerima','hubungan_penerima','no_surat_kematian'], 'safe'],
            [['is_meninggal', 'is_rencanakontrol','is_deleted', 'is_active'], 'boolean'],
            [['keterangan_keluar', 'additional_data',
                'catatandokterperujuk', 'alasandirujuk', 'hasilpemeriksaan_ruj', 'diagnosasementara_ruj', 'pengobatan_ruj', 'lainlain_ruj'], 'string'],
            [['penerima_pasien'], 'string', 'max' => 100],
            [['satuan_lamarawat', 'satuan_hariperawatan'], 'string', 'max' => 50],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_PASIENPULANG] = ['carakeluar_id','kondisikeluar_id','is_rencanakontrol','pasiendirujukkeluar_id','is_meninggal'];
        $scenarios[self::SCENARIO_PASIENDIRUJUK] = ['carakeluar_id', 'kondisikeluar_id','rujukankeluar_id', 'pegawai_id', 'nosuratrujukan', 'tgldirujuk','is_rencanakontrol','pasiendirujukkeluar_id','is_meninggal'];
        return $scenarios;
    }
    public function cekTgl($attributes, $params){
        if($this->is_rencanakontrol && empty($this->tgl_rencanakontrol)){
            $this->addError('tgl_rencanakontrol', 'Tanggal Rencana Kontrol Tidak Boleh Kosong');
        }

        if($this->is_rencanakontrol && empty($this->tgl_rencanakontrol)){
            $this->addError('nama_spesialis', 'Nama Spesialis Tidak Boleh Kosong');
        }

        if($this->is_rencanakontrol && empty($this->tgl_rencanakontrol)){
            $this->addError('dokterdpjp_nama', 'Dokter Spesialis Tidak Boleh Kosong');
        }
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'rujukankeluar_id' => 'Rujukan Keluar',
            'pegawai_id' => 'Dokter',
            'nosuratrujukan' => 'No. Surat Rujukan',
            'tgldirujuk' => 'Tanggal Dirujuk',
            'ruanganasal_id' => 'Ruangan Asal',
            'catatandokterperujuk' => 'Catatan Dokter Perujuk',
            'alasandirujuk' => 'Alasan Dirujuk',
            'hasilpemeriksaan_ruj' => 'Hasil Pemeriksaan Rujukan',
            'diagnosasementara_ruj' => 'Diagnosa Sementara Rujukan',
            'pengobatan_ruj' => 'Pengobatan Rujukan',
            'lainlain_ruj' => 'Lain-lain',

            'pasienpulang_id' => 'Pasienpulang ID',
            'pasien_id' => 'Pasien ID',
            'pasienbatalpulang_id' => 'Pasienbatalpulang ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'carakeluar_id' => 'Cara Keluar',
            'pasiendirujukkeluar_id' => 'Rujukan Keluar',
            'kondisikeluar_id' => 'Kondisi Pulang',
            'tglpasienpulang' => 'Tanggal Keluar Kamar',
            'ruanganakhir_id' => 'Ruanganakhir ID',
            'penerima_pasien' => 'Penerima Pasien',
            'lama_rawat' => 'Lama Dirawat',
            'satuan_lamarawat' => 'Satuan Lamarawat',
            'is_meninggal' => 'Tanggal Meninggal',
            'is_rencanakontrol' => 'Tanggal Rencana Kontrol',
            'keterangan_keluar' => 'Keterangan Pulang',
            'hari_perawatan' => 'Hari Perawatan',
            'satuan_hariperawatan' => 'Satuan Hariperawatan',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'tgl_meninggal' => 'Tanggal Meninggal',
            'tgl_rencanakontrol' => 'Tanggal Rencana Kontrol',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
            'is_pelayanan_jenazah' => 'Pelayanan Jenazah',
            'no_surat_kematian' => 'Nomor surat kematian',
            'nama_spesialis' => Yii::t('fe', 'Spesialis/Sub Spesialis'),
            'dokterdpjp_kode' => 'Kode Dokter DPJP',
            'dokterdpjp_nama' => 'Nama Dokter DPJP',
            'kode_poli' => 'Kode Poli',
            'no_kartu' => 'No Kartu',
        ];
    }
}
