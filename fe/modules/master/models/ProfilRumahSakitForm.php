<?php

namespace app\modules\master\models;

use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "profilrumahsakit_m".
 *
 * @property int $profilrs_id
 * @property int $kabupaten_id
 * @property int $kecamatan_id
 * @property int $propinsi_id
 * @property int $kelurahan_id
 * @property string $tahunprofilrs
 * @property string $kodejenisrs_profilrs
 * @property string $jenisrs_profilrs
 * @property string $statusrsswasta
 * @property string $namakepemilikanrs
 * @property int $kodestatuskepemilikanrs
 * @property string $statuskepemilikanrs
 * @property string $pentahapanakreditasrs
 * @property string $statusakreditasrs
 * @property string $nokode_rumahsakit
 * @property string $nama_rumahsakit
 * @property string $kelas_rumahsakit
 * @property string $namadirektur_rumahsakit
 * @property string $alamatlokasi_rumahsakit
 * @property string $nomor_suratizin
 * @property string $tgl_suratizin
 * @property string $oleh_suratizin
 * @property string $sifat_suratizin
 * @property string $masaberlakutahun_suratizin
 * @property string $motto
 * @property string $visi
 * @property string $no_faksimili
 * @property string $logo_rumahsakit
 * @property string $path_logorumahsakit
 * @property string $npwp
 * @property string $tahun_diresmikan
 * @property string $khususuntukswasta
 * @property string $website
 * @property string $email
 * @property string $no_telp_profilrs
 * @property string $negara
 * @property string $tglakreditasi
 * @property string $akreditasirs
 * @property string $tglregistrasi
 * @property string $notelphumas
 * @property string $luastanah
 * @property string $luasbangunan
 * @property string $ppkpelayanan
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
 *
 * @property AntrianT[] $antrianTs
 * @property InstalasiM[] $instalasiMs
 * @property MisirsM[] $misirsMs
 * @property PegawaiM[] $pegawaiMs
 * @property PengeluaranumumT[] $pengeluaranumumTs
 * @property ProfilpictureM[] $profilpictureMs
 * @property KabupatenM $kabupaten
 * @property KecamatanM $kecamatan
 * @property KelurahanM $kelurahan
 * @property PropinsiM $propinsi
 */
class ProfilRumahSakitForm extends \yii\base\Model
{
    
    public $profilrs_id;
    public $kabupaten_id;
    public $kecamatan_id;
    public $propinsi_id;
    public $kelurahan_id;
    public $tahunprofilrs;
    public $kodejenisrs_profilrs;
    public $kode_pos;
    public $jenisrs_profilrs;
    public $jenis_rumahsakit;
    public $statusrsswasta;
    public $namakepemilikanrs;
    public $kodestatuskepemilikanrs;
    public $statuskepemilikanrs;
    public $status_penyelenggara;
    public $pentahapanakreditasrs;
    public $statusakreditasrs;
    public $nokode_rumahsakit;
    public $nama_rumahsakit;
    public $kelas_rumahsakit;
    public $namadirektur_rumahsakit;
    public $nama_penyelenggara;
    public $alamatlokasi_rumahsakit;
    public $nomor_suratizin;
    public $tgl_suratizin;
    public $oleh_suratizin;
    public $sifat_suratizin;
    public $masaberlakutahun_suratizin;
    public $masaberlaku_dari;
    public $masaberlaku_sampai;
    public $motto;
    public $visi;
    public $misi;
    public $no_faksimili;
    public $logo_rumahsakit;
    public $path_logorumahsakit;
    public $npwp;
    public $tahun_diresmikan;
    public $khususuntukswasta;
    public $website;
    public $email;
    public $no_telp_profilrs;
    public $negara;
    public $tglakreditasi;
    public $akreditasirs;
    public $tglregistrasi;
    public $notelphumas;
    public $luastanah;
    public $luasbangunan;
    public $ppkpelayanan;
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
    public $gambar_login;
    public $background_login;
    public $logo_header;
    public $latitude;
    public $longtitude;
    public $path_background_login;
    public $path_gambar_login;
    public $path_logo_header;
    public $warna_header;
    public $font_header;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'profilrumahsakit_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kabupaten_id', 'kecamatan_id', 'propinsi_id', 'kelurahan_id', 'kodestatuskepemilikanrs', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['kabupaten_id', 'kecamatan_id', 'propinsi_id', 'kelurahan_id', 'kodestatuskepemilikanrs', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [[ 'nokode_rumahsakit', 'nama_rumahsakit', 'propinsi_id', 'kecamatan_id', 'kelurahan_id', 'kabupaten_id'], 'required'],
            [['alamatlokasi_rumahsakit', 'nama_penyelenggara', 'visi', 'additional_data'], 'string'],
            [['tgl_suratizin', 'tglakreditasi', 'jenis_rumahsakit','kode_pos','misi','masaberlaku_dari','masaberlaku_sampai','status_penyelenggara', 'tglregistrasi', 'created_date', 'last_modified_date', 'deleted_date', 'latitude', 'longtitude','warna_header','font_header','path_logo_header','path_gambar_login','path_background_login'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['tahunprofilrs', 'masaberlakutahun_suratizin', 'tahun_diresmikan'], 'string', 'max' => 4],
            [['kodejenisrs_profilrs', 'no_faksimili', 'no_telp_profilrs'], 'string', 'max' => 15],
            [['jenisrs_profilrs', 'namakepemilikanrs', 'statuskepemilikanrs', 'nama_rumahsakit', 'negara', 'notelphumas', 'luastanah', 'luasbangunan'], 'string', 'max' => 100],
            [['statusrsswasta', 'pentahapanakreditasrs', 'statusakreditasrs', 'namadirektur_rumahsakit', 'sifat_suratizin', 'website', 'email', 'ppkpelayanan'], 'string', 'max' => 50],
            [['nokode_rumahsakit'], 'string', 'max' => 10],
            [['kelas_rumahsakit', 'khususuntukswasta'], 'string', 'max' => 5],
            [['nomor_suratizin'], 'string', 'max' => 20],
            [['oleh_suratizin'], 'string', 'max' => 30],
            [[ 'path_logorumahsakit', 'akreditasirs'], 'string', 'max' => 200],
            [['npwp'], 'string', 'max' => 25],
            [['logo_rumahsakit','gambar_login','background_login','logo_header'], 'file', 'extensions' => 'jpg, jpeg, png, svg','message'=>'File yang bisa di upload hanyalah gambar'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'profilrs_id' => \Yii::t('fe', 'Profil RS'),
            'kabupaten_id' => \Yii::t('fe', 'Kabupaten'),
            'kecamatan_id' => \Yii::t('fe', 'Kecamatan'),
            'propinsi_id' => \Yii::t('fe', 'Propinsi'),
            'kelurahan_id' => \Yii::t('fe', 'Kelurahan'),
            'tahunprofilrs' => \Yii::t('fe', 'Tahun profil RS'),
            'kodejenisrs_profilrs' => \Yii::t('fe', 'Kode jenis profil'),
            'kode_pos' => \Yii::t('fe', 'Kode pos'),
            'jenisrs_profilrs' => \Yii::t('fe', 'Jenis profil'),
            'jenis_rumahsakit' => \Yii::t('fe', 'Jenis rumah sakit'),
            'statusrsswasta' => \Yii::t('fe', 'Status RS swasta'),
            'namakepemilikanrs' => \Yii::t('fe', 'Nama kepemilikan RS'),
            'kodestatuskepemilikanrs' => \Yii::t('fe', 'Kodestatuskepemilikanrs'),
            'statuskepemilikanrs' => \Yii::t('fe', 'Status Kepemilikan RS'),
            'status_penyelenggara' => \Yii::t('fe', 'Status Penyelenggara'),
            'pentahapanakreditasrs' => \Yii::t('fe', 'Pentahapan akreditas RS'),
            'statusakreditasrs' => \Yii::t('fe', 'Status akreditas'),
            'nokode_rumahsakit' => \Yii::t('fe', 'Kode rumah sakit'),
            'nama_rumahsakit' => \Yii::t('fe', 'Nama rumah sakit'),
            'kelas_rumahsakit' => \Yii::t('fe', 'Kelas rumah sakit'),
            'namadirektur_rumahsakit' => \Yii::t('fe', 'Nama direktur rumah sakit'),
            'nama_penyelenggara' => \Yii::t('fe', 'Nama penyelenggara'),
            'alamatlokasi_rumahsakit' => \Yii::t('fe', 'Alamat rumah sakit'),
            'nomor_suratizin' => \Yii::t('fe', 'Nomor surat izin'),
            'tgl_suratizin' => \Yii::t('fe', 'Tanggal'),
            'oleh_suratizin' => \Yii::t('fe', 'Oleh'),
            'sifat_suratizin' => \Yii::t('fe', 'Sifat'),
            'masaberlakutahun_suratizin' => \Yii::t('fe', 'Masa berlaku tahun surat izin'),
            'masaberlaku_dari' => \Yii::t('fe', 'Masa berlaku dari'),
            'masaberlaku_sampai' => \Yii::t('fe', 'Masa berlaku sampai'),
            'motto' => \Yii::t('fe', 'Motto'),
            'visi' => \Yii::t('fe', 'Visi rumah sakit'),
            'misi' => \Yii::t('fe', 'Misi rumah sakit'),
            'no_faksimili' => \Yii::t('fe', 'No fax'),
            'logo_rumahsakit' => \Yii::t('fe', 'Logo rumah sakit'),
            'npwp' => \Yii::t('fe', 'Npwp'),
            'tahun_diresmikan' => \Yii::t('fe', 'Tahun diresmikan'),
            'khususuntukswasta' => \Yii::t('fe', 'Khusus untuk swasta'),
            'website' => \Yii::t('fe', 'Website'),
            'email' => \Yii::t('fe', 'Alamat e-mail'),
            'no_telp_profilrs' => \Yii::t('fe', 'No telphone'),
            'negara' => \Yii::t('fe', 'Negara'),
            'tglakreditasi' => \Yii::t('fe', 'Tanggal akreditasi'),
            'akreditasirs' => \Yii::t('fe', 'Akreditasi RS'),
            'tglregistrasi' => \Yii::t('fe', 'Tanggal registrasi'),
            'notelphumas' => \Yii::t('fe', 'Nomor telp humas'),
            'luastanah' => \Yii::t('fe', 'Luas tanah (m2)'),
            'luasbangunan' => \Yii::t('fe', 'Luas bangunan (m2)'),
            'ppkpelayanan' => \Yii::t('fe', 'Ppk pelayanan'),
            'additional_data' => \Yii::t('fe', 'Additional Data'),
            'created_date' => \Yii::t('fe', 'Created Date'),
            'created_by' => \Yii::t('fe', 'Created By'),
            'modified_count' => \Yii::t('fe', 'Modified Count'),
            'last_modified_date' => \Yii::t('fe', 'Last Modified Date'),
            'last_modified_by' => \Yii::t('fe', 'Last Modified By'),
            'is_deleted' => \Yii::t('fe', 'Is Deleted'),
            'is_active' => \Yii::t('fe', 'Is Active'),
            'deleted_date' => \Yii::t('fe', 'Deleted Date'),
            'deleted_by' => \Yii::t('fe', 'Deleted By'),
            'latitude' => 'Latitude',
            'longtitude' => \Yii::t('fe', 'Longtitude'),
            'warna_header' => \Yii::t('fe', 'Warna Header'),
            'path_logorumahsakit' => \Yii::t('fe', 'Path Logo Rumah Sakit'),
            'path_gambar_login' => \Yii::t('fe', 'Path Gambar Login'),
            'path_background_login' => \Yii::t('fe', 'Path Background Login'),
            'path_logo_header' => \Yii::t('fe', 'Path Logo Header'),
        ];
    }

    public function uploadGambar()
    {
        if ($this->validate()) {
            $root_path = \Yii::getAlias('@webroot');
            $path = $root_path . '/media/img/profil-rs/';
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }
            // $path = \Yii::getAlias('@webroot') . '/media/img/pasien/';
            if($this->logo_rumahsakit instanceof UploadedFile){
                $img_path = $root_path.$this->path_logorumahsakit;
                if (!is_dir($img_path)) {
                    mkdir($img_path, 0777, true);
                }
                $this->logo_rumahsakit->saveAs($img_path . $this->logo_rumahsakit->baseName . '.' . $this->logo_rumahsakit->extension);
                $this->logo_rumahsakit = $this->logo_rumahsakit->name;
            }
            if($this->gambar_login instanceof UploadedFile){
                $img_path = $root_path.$this->path_gambar_login;
                if (!is_dir($img_path)) {
                    mkdir($img_path, 0777, true);
                }
                $this->gambar_login->saveAs($img_path . $this->gambar_login->baseName . '.' . $this->gambar_login->extension);
                $this->gambar_login = $this->gambar_login->name;
            }
            if($this->background_login instanceof UploadedFile){
                $img_path = $root_path.$this->path_background_login;
                if (!is_dir($img_path)) {
                    mkdir($img_path, 0777, true);
                }
                $this->background_login->saveAs($img_path . $this->background_login->baseName . '.' . $this->background_login->extension);
                $this->background_login = $this->background_login->name;
            }
            if($this->logo_header instanceof UploadedFile){
                $img_path = $root_path.$this->path_logo_header;
                if (!is_dir($img_path)) {
                    mkdir($img_path, 0777, true);
                }
                $this->logo_header->saveAs($img_path . $this->logo_header->baseName . '.' . $this->logo_header->extension);
                $this->logo_header = $this->logo_header->name;
            }
            return true;
        } else {
            return false;
        }
    }
}
