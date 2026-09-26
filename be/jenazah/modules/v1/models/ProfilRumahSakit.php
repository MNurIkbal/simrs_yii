<?php

namespace app\modules\v1\models;

use Yii;

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
class ProfilRumahSakit extends \yii\db\ActiveRecord
{
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
            [['kodejenisrs_profilrs', 'jenisrs_profilrs', 'nokode_rumahsakit', 'nama_rumahsakit'], 'required'],
            [['alamatlokasi_rumahsakit', 'motto', 'visi', 'additional_data'], 'string'],
            [['tgl_suratizin', 'tglakreditasi', 'tglregistrasi', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['tahunprofilrs', 'masaberlakutahun_suratizin', 'tahun_diresmikan'], 'string', 'max' => 4],
            [['kodejenisrs_profilrs', 'no_faksimili', 'no_telp_profilrs'], 'string', 'max' => 15],
            [['jenisrs_profilrs', 'namakepemilikanrs', 'statuskepemilikanrs', 'nama_rumahsakit', 'negara', 'notelphumas', 'luastanah', 'luasbangunan'], 'string', 'max' => 100],
            [['statusrsswasta', 'pentahapanakreditasrs', 'statusakreditasrs', 'namadirektur_rumahsakit', 'sifat_suratizin', 'website', 'email', 'ppkpelayanan'], 'string', 'max' => 50],
            [['nokode_rumahsakit'], 'string', 'max' => 10],
            [['kelas_rumahsakit', 'khususuntukswasta'], 'string', 'max' => 1],
            [['nomor_suratizin'], 'string', 'max' => 20],
            [['oleh_suratizin'], 'string', 'max' => 30],
            [['logo_rumahsakit', 'path_logorumahsakit', 'akreditasirs'], 'string', 'max' => 200],
            [['npwp'], 'string', 'max' => 25],
            [['kabupaten_id'], 'exist', 'skipOnError' => true, 'targetClass' => Kabupaten::className(), 'targetAttribute' => ['kabupaten_id' => 'kabupaten_id']],
            [['kecamatan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Kecamatan::className(), 'targetAttribute' => ['kecamatan_id' => 'kecamatan_id']],
            [['kelurahan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Kelurahan::className(), 'targetAttribute' => ['kelurahan_id' => 'kelurahan_id']],
            [['propinsi_id'], 'exist', 'skipOnError' => true, 'targetClass' => Propinsi::className(), 'targetAttribute' => ['propinsi_id' => 'propinsi_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'profilrs_id' => 'Profil RS',
            'kabupaten_id' => 'Kabupaten',
            'kecamatan_id' => 'Kecamatan',
            'propinsi_id' => 'Propinsi',
            'kelurahan_id' => 'Kelurahan',
            'tahunprofilrs' => 'Tahun profil RS',
            'kodejenisrs_profilrs' => 'Kode jenis profil',
            'jenisrs_profilrs' => 'Jenis profil',
            'statusrsswasta' => 'Status RS swasta',
            'namakepemilikanrs' => 'Nama kepemilikan RS',
            'kodestatuskepemilikanrs' => 'Kodestatuskepemilikanrs',
            'statuskepemilikanrs' => 'Status Kepemilikan RS',
            'pentahapanakreditasrs' => 'Pentahapan akreditas RS',
            'statusakreditasrs' => 'Status akreditas',
            'nokode_rumahsakit' => 'Nomor kode rumah sakit',
            'nama_rumahsakit' => 'Nama rumah sakit',
            'kelas_rumahsakit' => 'Kelas rumah sakit',
            'namadirektur_rumahsakit' => 'Nama direktur rumah sakit',
            'alamatlokasi_rumahsakit' => 'Alamat lokasi rumah sakit',
            'nomor_suratizin' => 'Nomor surat izin',
            'tgl_suratizin' => 'Tanggal surat izin',
            'oleh_suratizin' => 'Oleh surat izin',
            'sifat_suratizin' => 'Sifat surat izin',
            'masaberlakutahun_suratizin' => 'Masa berlaku tahun surat izin',
            'motto' => 'Motto',
            'visi' => 'Visi',
            'no_faksimili' => 'No faksimili',
            'logo_rumahsakit' => 'Logo rumah sakit',
            'path_logorumahsakit' => 'Path logo rumah sakit',
            'npwp' => 'Npwp',
            'tahun_diresmikan' => 'Tahun diresmikan',
            'khususuntukswasta' => 'Khusus untuk swasta',
            'website' => 'Website',
            'email' => 'Email',
            'no_telp_profilrs' => 'No telp profil RS',
            'negara' => 'Negara',
            'tglakreditasi' => 'Tanggal akreditasi',
            'akreditasirs' => 'Akreditasi RS',
            'tglregistrasi' => 'Tanggal registrasi',
            'notelphumas' => 'Nomor telp humas',
            'luastanah' => 'Luas tanah',
            'luasbangunan' => 'Luas bangunan',
            'ppkpelayanan' => 'Ppk pelayanan',
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getAntrianTs()
    // {
        // return $this->hasMany(Antrian::className(), ['layarantrian_id' => 'profilrs_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getInstalasiMs()
    // {
        // return $this->hasMany(Instalasi::className(), ['profilers_id' => 'profilrs_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getMisirsMs()
    // {
        // return $this->hasMany(Misirs::className(), ['profilrs_id' => 'profilrs_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getPegawaiMs()
    // {
        // return $this->hasMany(Pegawai::className(), ['profilrs_id' => 'profilrs_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getPengeluaranumumTs()
    // {
        // return $this->hasMany(Pengeluaranumum::className(), ['profilrs_id' => 'profilrs_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getProfilpictureMs()
    // {
        // return $this->hasMany(Profilpicture::className(), ['profilrs_id' => 'profilrs_id']);
    // }
    
    public function getPropinsi()
    {
        return $this->hasOne(Propinsi::className(), ['propinsi_id' => 'propinsi_id']);
    }
    
    public function getKabupaten()
    {
        return $this->hasOne(Kabupaten::className(), ['kabupaten_id' => 'kabupaten_id']);
    }
    
    public function getKecamatan()
    {
        return $this->hasOne(Kecamatan::className(), ['kecamatan_id' => 'kecamatan_id']);
    }
    
    public function getKelurahan()
    {
        return $this->hasOne(Kelurahan::className(), ['kelurahan_id' => 'kelurahan_id']);
    }
    
    public function extraFields()
    {
        return [
            'propinsi_m' => function($item){
                return $item->propinsi;
            },
            'kabupaten_m' => function($item){
                return $item->kabupaten;
            },
            'kecamatan_m' => function($item){
                return $item->kecamatan;
            },
            'kelurahan_m' => function($item){
                return $item->kelurahan;
            }
        ];
    }
}
