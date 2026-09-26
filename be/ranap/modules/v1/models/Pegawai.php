<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pegawai_m".
 *
 * @property integer $pegawai_id
 * @property string $nomorindukpegawai
 * @property string $gelardepan
 * @property string $nama_pegawai
 * @property string $gelarbelakang
 * @property string $jeniskelamin
 * @property string $tempatlahir_pegawai
 * @property string $tgl_lahirpegawai
 * @property string $agama
 * @property string $alamat_pegawai
 * @property integer $propinsi_id
 * @property integer $kabupaten_id
 * @property integer $kecamatan_id
 * @property integer $kelurahan_id
 * @property integer $suku_id
 * @property string $notelp_pegawai
 * @property string $nomobile_pegawai
 * @property string $alamatemail
 * @property integer $pangkat_id
 * @property integer $esselon_id
 * @property integer $jabatan_id
 * @property string $kelompokjabatan
 * @property integer $golonganpegawai_id
 * @property integer $jenisjabatan_id
 * @property integer $jenjangjabatan_id
 * @property integer $pendidikan_id
 * @property integer $pendkualifikasi_id
 * @property integer $kelompokpegawai_id
 * @property string $kategoripegawai
 * @property integer $profilrs_id
 * @property integer $pengangkatantphl_id
 * @property string $jenisidentitas
 * @property string $noidentitas
 * @property string $no_kartupegawainegerisipil
 * @property string $no_taspen
 * @property string $no_askes
 * @property string $nama_keluarga
 * @property string $statusperkawinan
 * @property string $warganegara_pegawai
 * @property integer $statuskepemilikanrumah_id
 * @property string $golongandarah
 * @property string $rhesus
 * @property double $tinggibadan
 * @property double $beratbadan
 * @property string $warnakulit
 * @property string $nip_lama
 * @property integer $loginpemakai_id
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
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 */
class Pegawai extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */

    const KELOMPOK_DOKTER = 1;

    public static function tableName()
    {
        return 'pegawai_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nama_pegawai', 'jeniskelamin', 'agama'], 'required'],
            [['tgl_lahirpegawai', 'tglditerima', 'tglberhenti', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['alamat_pegawai', 'warnakulit', 'deskripsi', 'additional_data'], 'string'],
            [['propinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id', 'suku_id', 'pangkat_id', 'esselon_id', 'jabatan_id', 'golonganpegawai_id', 'jenisjabatan_id', 'jenjangjabatan_id', 'pendidikan_id', 'pendkualifikasi_id', 'kelompokpegawai_id', 'profilrs_id', 'pengangkatantphl_id', 'statuskepemilikanrumah_id', 'loginpemakai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tinggibadan', 'beratbadan', 'gajipokok', 'garis_latitude', 'garis_longitude'], 'number'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['nomorindukpegawai', 'tempatlahir_pegawai', 'kelompokjabatan', 'no_kartupegawainegerisipil', 'no_taspen', 'no_askes'], 'string', 'max' => 30],
            [['gelardepan'], 'string', 'max' => 10],
            [['nama_pegawai', 'notelp_pegawai', 'nomobile_pegawai', 'nama_keluarga', 'npwp'], 'string', 'max' => 50],
            [['gelarbelakang'], 'string', 'max' => 32],
            [['jeniskelamin', 'agama', 'jenisidentitas', 'statusperkawinan', 'rhesus', 'nofingerprint', 'jeniswaktukerja'], 'string', 'max' => 20],
            [['alamatemail', 'noidentitas', 'nip_lama', 'suratizinpraktek', 'no_rekening', 'bank_no_rekening'], 'string', 'max' => 100],
            [['kategoripegawai'], 'string', 'max' => 128],
            [['warganegara_pegawai'], 'string', 'max' => 25],
            [['golongandarah'], 'string', 'max' => 2],
            [['photopegawai'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pegawai_id' => 'Pegawai ID',
            'nomorindukpegawai' => 'Nomorindukpegawai',
            'gelardepan' => 'Gelardepan',
            'nama_pegawai' => 'Nama Pegawai',
            'gelarbelakang' => 'Gelarbelakang',
            'jeniskelamin' => 'Jeniskelamin',
            'tempatlahir_pegawai' => 'Tempatlahir Pegawai',
            'tgl_lahirpegawai' => 'Tgl Lahirpegawai',
            'agama' => 'Agama',
            'alamat_pegawai' => 'Alamat Pegawai',
            'propinsi_id' => 'Propinsi ID',
            'kabupaten_id' => 'Kabupaten ID',
            'kecamatan_id' => 'Kecamatan ID',
            'kelurahan_id' => 'Kelurahan ID',
            'suku_id' => 'Suku ID',
            'notelp_pegawai' => 'Notelp Pegawai',
            'nomobile_pegawai' => 'Nomobile Pegawai',
            'alamatemail' => 'Alamatemail',
            'pangkat_id' => 'Pangkat ID',
            'esselon_id' => 'Esselon ID',
            'jabatan_id' => 'Jabatan ID',
            'kelompokjabatan' => 'Kelompokjabatan',
            'golonganpegawai_id' => 'Golonganpegawai ID',
            'jenisjabatan_id' => 'Jenisjabatan ID',
            'jenjangjabatan_id' => 'Jenjangjabatan ID',
            'pendidikan_id' => 'Pendidikan ID',
            'pendkualifikasi_id' => 'Pendkualifikasi ID',
            'kelompokpegawai_id' => 'Kelompokpegawai ID',
            'kategoripegawai' => 'Kategoripegawai',
            'profilrs_id' => 'Profilrs ID',
            'pengangkatantphl_id' => 'Pengangkatantphl ID',
            'jenisidentitas' => 'Jenisidentitas',
            'noidentitas' => 'Noidentitas',
            'no_kartupegawainegerisipil' => 'No Kartupegawainegerisipil',
            'no_taspen' => 'No Taspen',
            'no_askes' => 'No Askes',
            'nama_keluarga' => 'Nama Keluarga',
            'statusperkawinan' => 'Statusperkawinan',
            'warganegara_pegawai' => 'Warganegara Pegawai',
            'statuskepemilikanrumah_id' => 'Statuskepemilikanrumah ID',
            'golongandarah' => 'Golongandarah',
            'rhesus' => 'Rhesus',
            'tinggibadan' => 'Tinggibadan',
            'beratbadan' => 'Beratbadan',
            'warnakulit' => 'Warnakulit',
            'nip_lama' => 'Nip Lama',
            'loginpemakai_id' => 'Loginpemakai ID',
            'photopegawai' => 'Photopegawai',
            'nofingerprint' => 'Nofingerprint',
            'jeniswaktukerja' => 'Jeniswaktukerja',
            'suratizinpraktek' => 'Suratizinpraktek',
            'no_rekening' => 'No Rekening',
            'bank_no_rekening' => 'Bank No Rekening',
            'npwp' => 'Npwp',
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
        ];
    }

    public function listDokter()
    {
        $query = self::find()->where([
            'kelompokpegawai_id' => self::KELOMPOK_DOKTER, 
            'is_active' => 1
        ])
        ->all();

        return $query;
    }

    public function getLoginPemakai()
    {
        return $this->hasOne(\Doco\models\Loginpemakai::className(), ['pegawai_id' => 'pegawai_id']);
    }

    public function getKelompokPegawai()
    {
        return $this->hasOne(\Doco\models\KelompokPegawai::className(), ['kelompokpegawai_id' => 'kelompokpegawai_id']);
    }
}
