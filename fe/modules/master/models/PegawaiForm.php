<?php

namespace Doco\master\models;

use Yii;

/**
 * This is the model class for table "pegawai_m".
 *
 * @property int $pegawai_id
 * @property string $nomorindukpegawai
 * @property string $gelardepan
 * @property string $nama_pegawai
 * @property string $gelarbelakang
 * @property string $jeniskelamin
 * @property string $tempatlahir_pegawai
 * @property string $tgl_lahirpegawai
 * @property string $agama
 * @property string $alamat_pegawai
 * @property int $propinsi_id
 * @property int $kabupaten_id
 * @property int $kecamatan_id
 * @property int $kelurahan_id
 * @property int $suku_id
 * @property string $notelp_pegawai
 * @property string $nomobile_pegawai
 * @property string $alamatemail
 * @property int $pangkat_id
 * @property int $esselon_id
 * @property int $jabatan_id
 * @property string $kelompokjabatan
 * @property int $golonganpegawai_id
 * @property int $jenisjabatan_id
 * @property int $jenjangjabatan_id
 * @property int $pendidikan_id
 * @property int $pendkualifikasi_id
 * @property int $kelompokpegawai_id
 * @property string $kategoripegawai
 * @property int $profilrs_id
 * @property int $pengangkatantphl_id
 * @property string $jenisidentitas
 * @property string $noidentitas
 * @property string $no_kartupegawainegerisipil
 * @property string $no_taspen
 * @property string $no_askes
 * @property string $nama_keluarga
 * @property string $status_kawin
 * @property string $warganegara_pegawai
 * @property int $statuskepemilikanrumah_id
 * @property string $golongan_darah
 * @property string $rhesus
 * @property double $tinggibadan
 * @property double $beratbadan
 * @property string $warnakulit
 * @property string $nip_lama
 * @property int $loginpemakai_id
 * @property string $photopegawai
 * @property string $nofingerprint
 * @property string $jeniswaktukerja
 * @property string $suratizinpraktek
 * @property string $no_rekening
 * @property string $bank_no_rekening
 * @property string $npwp
 * @property string $tglditerima
 * @property string $tglberhenti
 * @property double $gajipokok
 * @property string $deskripsi
 * @property double $garis_latitude
 * @property double $garis_longitude
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
 */
class PegawaiForm extends \yii\base\Model
{
    public $pegawai_id;
    public $nomorindukpegawai;
    public $satusehat_pegawai_id;
    public $gelardepan;
    public $nama_pegawai;
    public $gelarbelakang;
    public $jeniskelamin;
    public $tempatlahir_pegawai;
    public $tgl_lahirpegawai;
    public $agama;
    public $alamat_pegawai;
    public $propinsi_id;
    public $kabupaten_id;
    public $kecamatan_id;
    public $kelurahan_id;
    public $suku_id;
    public $notelp_pegawai;
    public $nomobile_pegawai;
    public $alamatemail;
    public $pangkat_id;
    public $esselon_id;
    public $jabatan_id;
    public $kelompokjabatan;
    public $golonganpegawai_id;
    public $jenisjabatan_id;
    public $jenjangjabatan_id;
    public $pendidikan_id;
    public $pendkualifikasi_id;
    public $kelompokpegawai_id;
    public $kategoripegawai;
    public $profilrs_id;
    public $pengangkatantphl_id;
    public $jenisidentitas;
    public $noidentitas;
    public $no_kartupegawainegerisipil;
    public $no_taspen;
    public $no_askes;
    public $nama_keluarga;
    public $status_kawin;
    public $kemampuan_bahasa;
    public $warganegara_pegawai;
    public $statuskepemilikanrumah_id;
    public $golongan_darah;
    public $rhesus;
    public $tinggibadan;
    public $beratbadan;
    public $warna_kulit;
    public $bank_id;
    public $nip_lama;
    public $loginpemakai_id;
    public $photopegawai;
    public $nofingerprint;
    public $jeniswaktukerja;
    public $suratizinpraktek;
    public $no_rekening;
    public $bank_no_rekening;
    public $npwp;
    public $status_pegawai;
    public $tglditerima;
    public $tglberhenti;
    public $gajipokok;
    public $deskripsi;
    public $garis_latitude;
    public $garis_longitude;
    public $aktif;
    public $is_online;
    public $photopegawai_blob;
    public $kode_dokter_bpjs;
    public $nama_dokter_bpjs;
    public $tanda_tangan;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['noidentitas', 'nomorindukpegawai', 'nama_pegawai', 'jeniskelamin', 'agama', 'status_kawin', 'golongan_darah', 'tgl_lahirpegawai', 'tempatlahir_pegawai', 'warganegara_pegawai', 'suku_id', 'alamat_pegawai', 'propinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id', 'pendidikan_id', 'pendkualifikasi_id', 'kelompokpegawai_id', 'aktif'], 'required','message'=>'{attribute} Tidak boleh kosong'],
            [['tgl_lahirpegawai', 'tglditerima', 'tglberhenti', 'pegawai_id', 'kode_dokter_bpjs', 'nama_dokter_bpjs',], 'safe'],
            [['alamat_pegawai', 'warna_kulit', 'deskripsi'], 'string'],
            [['satusehat_pegawai_id', 'propinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id', 'suku_id', 'pangkat_id', 'esselon_id', 'jabatan_id', 'golonganpegawai_id', 'jenisjabatan_id', 'jenjangjabatan_id', 'pendidikan_id', 'pendkualifikasi_id', 'profilrs_id', 'pengangkatantphl_id', 'statuskepemilikanrumah_id', 'loginpemakai_id'], 'default', 'value' => null],
            [['propinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id', 'suku_id', 'pangkat_id', 'esselon_id', 'jabatan_id', 'golonganpegawai_id', 'jenisjabatan_id', 'jenjangjabatan_id', 'pendidikan_id', 'pendkualifikasi_id', 'kelompokpegawai_id', 'profilrs_id', 'pengangkatantphl_id', 'statuskepemilikanrumah_id', 'loginpemakai_id', 'kemampuan_bahasa', 'status_pegawai'], 'integer'],
            [['tinggibadan', 'beratbadan', 'gajipokok', 'garis_latitude', 'garis_longitude'], 'number'],
            [['is_online'], 'boolean'],
            [['nomorindukpegawai', 'tempatlahir_pegawai', 'kelompokjabatan', 'no_kartupegawainegerisipil', 'no_taspen', 'no_askes'], 'string', 'max' => 30],
            [['gelardepan'], 'string', 'max' => 10],
            [['nama_pegawai'], 'string', 'max' => 255],
            [['notelp_pegawai', 'nomobile_pegawai', 'nama_keluarga', 'npwp'], 'string', 'max' => 50],
            [['gelarbelakang'], 'string', 'max' => 32],
            [['jeniskelamin', 'agama', 'jenisidentitas', 'status_kawin', 'rhesus', 'nofingerprint', 'jeniswaktukerja'], 'string', 'max' => 20],
            [['alamatemail', 'noidentitas', 'nip_lama', 'suratizinpraktek', 'no_rekening', 'bank_no_rekening'], 'string', 'max' => 100],
            [['kategoripegawai'], 'string', 'max' => 128],
            [['warganegara_pegawai'], 'string', 'max' => 25],
            [['golongan_darah'], 'string', 'max' => 32],
            [['photopegawai'], 'string', 'max' => 200],
            [['nomorindukpegawai'], 'checkSpaces'],
            [['noidentitas'], 'checkSpacesNik'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pegawai_id' => 'Pegawai ID',
            'nomorindukpegawai' => 'NIP',
            'gelardepan' => 'Gelardepan',
            'nama_pegawai' => 'Nama pegawai',
            'gelarbelakang' => 'Gelarbelakang',
            'jeniskelamin' => 'Jenis kelamin',
            'tempatlahir_pegawai' => 'Tempat lahir',
            'tgl_lahirpegawai' => 'Tanggal lahir',
            'agama' => 'Agama',
            'alamat_pegawai' => 'Alamat Pegawai',
            'propinsi_id' => 'Propinsi',
            'kabupaten_id' => 'Kabupaten',
            'kecamatan_id' => 'Kecamatan',
            'kelurahan_id' => 'Kelurahan',
            'suku_id' => 'Suku',
            'notelp_pegawai' => 'No telp pegawai',
            'nomobile_pegawai' => 'No mobile pegawai',
            'alamatemail' => 'Alamatemail',
            'pangkat_id' => 'Pangkat ID',
            'esselon_id' => 'Esselon ID',
            'jabatan_id' => 'Jabatan ID',
            'kelompokjabatan' => 'Kelompokjabatan',
            'golonganpegawai_id' => 'Golonganpegawai ID',
            'jenisjabatan_id' => 'Jenisjabatan ID',
            'jenjangjabatan_id' => 'Jenjangjabatan ID',
            'pendidikan_id' => 'Pendidikan',
            'pendkualifikasi_id' => 'Kualifikasi pendidikan',
            'kelompokpegawai_id' => 'Kelompokpegawai ID',
            'kategoripegawai' => 'Kategoripegawai',
            'profilrs_id' => 'Profilrs ID',
            'pengangkatantphl_id' => 'Pengangkatantphl ID',
            'jenisidentitas' => 'Jenisidentitas',
            'noidentitas' => 'NIK',
            'no_kartupegawainegerisipil' => 'No Kartupegawainegerisipil',
            'no_taspen' => 'No Taspen',
            'no_askes' => 'No Askes',
            'nama_keluarga' => 'Nama Keluarga',
            'status_kawin' => 'Status perkawinan',
            'kemampuan_bahasa' => 'Kemampuan Bahasa',
            'warganegara_pegawai' => 'Warga negara',
            'statuskepemilikanrumah_id' => 'Statuskepemilikanrumah ID',
            'golongan_darah' => 'Golongan darah',
            'rhesus' => 'Rhesus',
            'tinggibadan' => 'Tinggibadan',
            'beratbadan' => 'Beratbadan',
            'warna_kulit' => 'Warnakulit',
            'nip_lama' => 'Nip Lama',
            'loginpemakai_id' => 'Loginpemakai ID',
            'photopegawai' => 'Photopegawai',
            'nofingerprint' => 'Nofingerprint',
            'jeniswaktukerja' => 'Jeniswaktukerja',
            'suratizinpraktek' => 'Suratizinpraktek',
            'no_rekening' => 'No Rekening',
            'bank_no_rekening' => 'Bank No Rekening',
            'npwp' => 'Npwp',
            'bank_id'=>'Nama Bank',
            'status_pegawai'=>'Status Pegawai',
            'tglditerima' => 'Tglditerima',
            'tglberhenti' => 'Tglberhenti',
            'gajipokok' => 'Gajipokok',
            'deskripsi' => 'Deskripsi',
            'garis_latitude' => 'Garis Latitude',
            'garis_longitude' => 'Garis Longitude',
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
            'aktif' => 'Status Pegawai',
            'is_online' => 'Pegawai Online',
            'tanda_tangan' => 'Tanda Tangan Pegawai',
        ];
    }

    public function checkSpaces() {
        $nomorindukpegawai = $this->nomorindukpegawai;
        if (strpos(substr($nomorindukpegawai, 0, 1), ' ') !== FALSE) {
            $this->addError('nomorindukpegawai', 'NIP mengandung spasi di awal kata');
            return false;
        }

        return true;
    }

    public function checkSpacesNik() {
        $nomorindukpegawai = $this->nomorindukpegawai;
        if (strpos(substr($nomorindukpegawai, 0, 1), ' ') !== FALSE) {
            $this->addError('noidentitas', 'NIK mengandung spasi di awal kata');
            return false;
        }

        return true;
    }
}
