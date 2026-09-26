<?php

namespace app\modules\rajal\models;

use Yii;
use app\components\DocoConstants;

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
 * @property string $penerima_pasien
 * @property int $lama_rawat
 * @property string $satuan_lamarawat
 * @property bool $is_meninggal
 * @property string $keterangan_keluar
 * @property int $hari_perawatan
 * @property string $satuan_hariperawatan
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
 * @property string $nosep
 *
 * @property string $infeksi
 * @property int $kamarruangan_jenis
 */
class PasienPulangForm extends  \yii\base\Model
{

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
  public $tgl_meninggal;
  public $persetujuanpelayanan;
  public $tgl_pendaftaran;
  public $additional_data;
  public $created_date;
  public $created_by;
  public $modified_count;
  public $last_modified_date;
  public $last_modified_by;
  public $is_deleted;
  public $is_active;
  public $deleted_date;
  public $deleted_by;
  public $tgl_konsulpoli;
  public $tempattidurtujuan_id;
  public $catatan_lain;
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
  public $infeksi;
  public $is_prb;
  public $ruangan_id;
  public $kamarruangan_jenis;
  public $dokterspesialis_id;
  public $catatan_tindakan;
  public $no_surat_kematian;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pasienpulang_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pasien_id', 'carakeluar_id', 'tglpasienpulang'], 'required'],
            [['pasien_id', 'pasienbatalpulang_id', 'pendaftaran_id', 'pasienadmisi_id', 'carakeluar_id', 'kondisikeluar_id', 'ruanganakhir_id', 'lama_rawat', 'hari_perawatan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'kamarruangan_jenis'], 'default', 'value' => null],
            [['pasien_id', 'pasienbatalpulang_id', 'pendaftaran_id', 'pasienadmisi_id', 'carakeluar_id', 'kondisikeluar_id', 'ruanganakhir_id', 'lama_rawat', 'hari_perawatan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'kamarruangan_jenis'], 'integer'],
            [['tglpasienpulang', 'created_date', 'last_modified_date', 'deleted_date', 'tgl_meninggal', 'is_meninggal', 'persetujuanpelayanan', 'tgl_pendaftaran', 'tempattidurtujuan_id', 'catatan_lain','status_jenazah','tgl_kremasi','nama_pemeriksa_jenazah','kualifikasi_pemeriksa','waktu_pemeriksaan_jenazah','dasar_diagnosis','kelompok_kematian','tempat_kematian','penyebab_langsung','penyebab_antara','penyebab_dasar','kondisi_lain','penyebab_utama_bayi','penyebab_utama_ibu','penyebab_lain_bayi','penyebab_lain_ibu','pihak_menerima','hubungan_penerima','infeksi','dokterspesialis_id', 'catatan_tindakan'], 'safe'],
            [['is_meninggal', 'is_deleted', 'is_active', 'is_prb'], 'boolean'],
            [['keterangan_keluar', 'additional_data'], 'string'],
            // ['tglpasienpulang', 'validateDate'],
            [['penerima_pasien'], 'string', 'max' => 100],
            [['tempat_kematian','kualifikasi_pemeriksa','waktu_pemeriksaan_jenazah','tgl_meninggal','no_surat_kematian'], 'required', 'when' => function($model, $attribute) {
                    return $model->carakeluar_id == DocoConstants::CARA_KELUAR_MENINGGAL;
                }, 'message' => '{attribute} Tidak Boleh Kosong!'
            ],
            [['satuan_lamarawat', 'satuan_hariperawatan'], 'string', 'max' => 50],
        ];
    }
    public function checkMeninggal()
    {
      if($this->is_meninggal == 1){
        if(empty($this->tgl_meninggal)){
          $this->addError('tgl_meninggal', 'Tanggal Meninggal Tidak Boleh Kosong');
        }
      }
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pasienpulang_id' => 'Pasienpulang ID',
            'pasien_id' => 'Pasien ID',
            'pasienbatalpulang_id' => 'Pasienbatalpulang ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'carakeluar_id' => 'Cara Pulang',
            'kondisikeluar_id' => 'Kondisikeluar ID',
            'tglpasienpulang' => 'Tanggal Pulang',
            'ruanganakhir_id' => 'Ruanganakhir ID',
            'penerima_pasien' => 'Penerima Pasien',
            'lama_rawat' => 'Lama Rawat',
            'satuan_lamarawat' => 'Satuan Lamarawat',
            'is_meninggal' => 'Is Meninggal',
            'keterangan_keluar' => 'Keterangan Keluar',
            'hari_perawatan' => 'Hari Perawatan',
            'satuan_hariperawatan' => 'Satuan Hariperawatan',
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
            'persetujuanpelayanan' => 'Persetujuan Pelayanan Jenazah',
            'tgl_meninggal' => 'Tanggal Meninggal',
            'tempattidurtujuan_id' => 'Pemilihan Kamar',
            'catatan_lain' => 'Catatan Lain',
            'infeksi' => 'infeksi',
            'is_prb' => 'PRB',
            'ruangan_id' => 'Jenis Ruangan',
            'kamarruangan_jenis' => 'Jenis Kamar',
            'dokterspesialis_id' => 'Dokter Tujuan',
            'catatan_tindakan' => 'Pemeriksaan/Pertolongan yang sudah/harus diberikan',
            'no_surat_kematian' => 'Nomor Surat Kematian',
        ];
    }

    public function validateDate()
    {
        if (strtotime($this->tgl_pendaftaran) > strtotime($this->tglpasienpulang)) {
            $this->addError('tglpasienpulang','Tanggal pulang tidak boleh kecil dari tanggal pendaftaran');
        }

        if (!is_null($this->tgl_konsulpoli)) {
          if (strtotime($this->tgl_konsulpoli) > strtotime($this->tglpasienpulang)) {
              $this->addError('tglpasienpulang','Tanggal pulang tidak boleh kecil dari tanggal konsul');
          }
        }
    }



}
