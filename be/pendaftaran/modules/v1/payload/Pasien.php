<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\payload;

use Yii;
use app\modules\v1\models\Pasien as PasienModel;


class Pasien extends \Doco\components\DocoBaseModel
{
    public $kelahiranbayi_id;
    public $pendaftaran_id;
    /** Attribute Pasien */
    public $nama_pasien;
    public $jeniskelamin;
    public $jenisidentitas;
    public $tanggal_lahir;
    public $alamat_pasien;
    public $statusperkawinan;
    public $golongandarah;
    public $tempat_lahir;
    public $umur;
    public $propinsi_id;
    public $kabupaten_id;
    public $kecamatan_id;
    public $pendidikan_id;
    public $pekerjaan_id;
    public $tgl_rekam_medik;
    public $namadepan;
    public $no_identitas_pasien;
    public $nama_ibu;
    public $nama_ayah;
    public $anakke;
    public $jumlah_bersaudara;
    public $rt;
    public $rw;
    public $kelurahan_id;
    public $no_telepon_pasien;
    public $no_mobile_pasien;
    public $alamatemail;
    public $warga_negara;
    public $suku_id;
    public $agama;
    public $no_rekam_medik;
    public $nopeserta_bpjs;
    public $golonganumur_id;
    public $dokrekammedis_id;
    public $pegawai_id;
    public $loginpemakai_id;
    public $photopasien;
    public $pasien_id;
    public $catatanpenting_pasien;
    public $statusrekammedis;
    public $is_aps;
    public $nama_panggilan;
    
    public $tgl_meninggal;
    public $created_date;
    public $last_modified_date;
    public $deleted_date;
    public $additional_identitas;
    public $bahasa_sehari;
    public $alamatdepan;

    protected $xssProtected = [
        'nama_pasien', 
        'jeniskelamin', 
        'jenisidentitas', 
        'alamat_pasien', 
        'statusperkawinan', 
        'golongandarah', 
        'umur',
        'tempat_lahir', 
        'tgl_rekam_medik', 
        'tanggal_lahir', 
        'tgl_meninggal',
        'created_date', 
        'last_modified_date', 
        'deleted_date',
        'nama_ibu', 
        'namadepan', 
        'agama',
        'warga_negara',
        'nopeserta_bpjs',
        'no_identitas_pasien',
        'nama_ayah',
        'anakke',
        'jumlah_bersaudara',
        'rt',
        'rw',
        'no_telepon_pasien',
        'no_mobile_pasien',
        'alamatemail',
        'no_rekam_medik',
        'statusrekammedis',
        'nama_panggilan',
        'additional_identitas',
    ];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'nama_pasien',
                    'jeniskelamin', 
                    // 'jenisidentitas', 
                    'tanggal_lahir',
                    // 'alamat_pasien', 
                    // 'statusperkawinan', 
                    // 'golongandarah',
                    // 'tempat_lahir', 
                    // 'propinsi_id', 
                    // 'kabupaten_id', 
                    // 'kecamatan_id'
                ],
                'required', "on" => 'default'
            ],
            [
                [
                    'nama_pasien',
                    // 'tempat_lahir', 
                    'tanggal_lahir',
                    // 'alamat_pasien', 
                    'jeniskelamin', 
                    // 'no_telepon_pasien',
                    // 'no_identitas_pasien',
                ], 
                'required', 'on' => 'pendaftaran-rajal',
            ],
            [
                [
                    'nama_pasien',
                    // 'tempat_lahir', 
                    'tanggal_lahir',
                    // 'alamat_pasien', 
                    'jeniskelamin', 
                    // 'no_telepon_pasien',
                    // 'no_identitas_pasien',
                ], 
                'required', 'on' => 'pendaftaran-ranap',
            ],
            [
                [
                    'nama_pasien',
                    'jeniskelamin', 
                ], 
                'required', 'on' => 'pendaftaran-igd',
            ],
            [
                [], 
                'safe', 'on' => 'pendaftaran-pasien-lama',
            ],
            [
                [
                    'nama_pasien',
                    'jeniskelamin', 
                    'no_identitas_pasien', 
                ], 
                'required', 'on' => 'pendaftaran-mcu-multiple',
            ],
            [
                [
                    'nama_pasien', 
                    'jeniskelamin', 
                    'jenisidentitas', 
                    'alamat_pasien', 
                    'statusperkawinan', 
                    'golongandarah', 
                    'umur', 
                    'propinsi_id', 
                    'kabupaten_id', 
                    'kecamatan_id', 
                    'pendidikan_id', 
                    'tempat_lahir', 
                    'tgl_rekam_medik', 
                    'tanggal_lahir', 
                    'tgl_meninggal',
                    'created_date', 
                    'last_modified_date', 
                    'deleted_date',
                    'nama_ibu', 
                    'namadepan', 
                    'pekerjaan_id', 
                    'suku_id',
                    'agama',
                    'warga_negara',
                    'nopeserta_bpjs',
                    'no_identitas_pasien',
                    'nama_ayah',
                    'anakke',
                    'jumlah_bersaudara',
                    'rt',
                    'rw',
                    'kelurahan_id',
                    'no_telepon_pasien',
                    'no_mobile_pasien',
                    'alamatemail',
                    'no_rekam_medik',
                    'golonganumur_id',
                    'dokrekammedis_id',
                    'pegawai_id',
                    'catatanpenting_pasien',
                    'loginpemakai_id',
                    'statusrekammedis',
                    'is_aps',
                    'nama_panggilan',
                    'pasien_id',
                    'additional_identitas',
                    'bahasa_sehari',
                    'alamatdepan',
                ], 'safe'],
            [[
                'golonganumur_id', 
                'rt',
                'rw', 
                'propinsi_id', 
                'kabupaten_id', 
                'kecamatan_id', 
                'kelurahan_id', 
                'golongandarah', 
                'suku_id', 
                'anakke', 
                'jumlah_bersaudara', 
                'dokrekammedis_id', 
                'pegawai_id', 
                'loginpemakai_id',
                'pendidikan_id',
                'pekerjaan_id',
                'agama',
            ], 'default', 'value' => null, "on" => ['default', 'pendaftaran-rajal', 'pendaftaran-igd', 'pendaftaran-mcu-multiple']],
            [[
                'golonganumur_id', 
                'rt', 
                'rw', 
                'propinsi_id', 
                'kabupaten_id', 
                'kecamatan_id', 
                'kelurahan_id', 
                'pendidikan_id', 
                'pekerjaan_id', 
                'suku_id', 
                'anakke', 
                'jumlah_bersaudara', 
                'dokrekammedis_id', 
                'pegawai_id', 
                'loginpemakai_id'], 'integer'],
            [[
                'alamat_pasien',
            ], 'string'],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [[
                'jenisidentitas', 
                'namadepan', 
                'jeniskelamin', 
                'statusperkawinan', 
                'agama', 
                'no_mobile_pasien'
            ], 'string', 'max' => 20],
            [['no_identitas_pasien'], 'string', 'max' => 30],
            [['nama_pasien', 'nama_ibu', 'nama_ayah'], 'string', 'max' => 50],
            [['tempat_lahir', 'warga_negara'], 'string', 'max' => 25],
            [['no_telepon_pasien'], 'string', 'max' => 15],
            [['photopasien'], 'string', 'max' => 200],
            [['alamatemail'], 'string', 'max' => 100],
            [['golongandarah'], 'integer'],
            [['alamatemail'], 'email'],
            [['nama_pasien'], 'uniquePasienValidator'],

        ];
    }

    public function uniquePasienValidator($attribute, $params)
    {
        if(empty($this->pasien_id)){
            $model = PasienModel::find()
                ->andWhere([
                    'LOWER(nama_pasien)'=>strtolower($this->nama_pasien),
                    'tanggal_lahir'=>$this->tanggal_lahir,
                    'nama_ibu'=>$this->nama_ibu
                ])->one();

            if ($model) {
                $this->addError('nama_pasien', 'Pasien sudah terdaftar. No Rekam Medik : ' . $model->no_rekam_medik);
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
            'no_rekam_medik' => 'No rekam medik',
            'tgl_rekam_medik' => 'Tgl rekam medik',
            'jenisidentitas' => 'Jenis identitas',
            'no_identitas_pasien' => 'No identitas pasien',
            'namadepan' => 'Nama depan',
            'nama_pasien' => 'Nama pasien',
            'nama_panggilan' => 'Nama panggilan',
            'jeniskelamin' => 'Jenis kelamin',
            'tempat_lahir' => 'Tempat lahir',
            'tanggal_lahir' => 'Tanggal lahir',
            'golonganumur_id' => 'Golongan umur',
            'alamat_pasien' => 'Alamat pasien',
            'rt' => 'RT',
            'rw' => 'RW',
            'propinsi_id' => 'Propinsi',
            'kabupaten_id' => 'Kabupaten',
            'kecamatan_id' => 'Kecamatan',
            'kelurahan_id' => 'Kelurahan',
            'pendidikan_id' => 'Pendidikan',
            'pekerjaan_id' => 'Pekerjaan',
            'suku_id' => 'Suku',
            'statusperkawinan' => 'Status perkawinan',
            'agama' => 'Agama',
            'golongandarah' => 'Golongan darah',
            'rhesus' => 'Rhesus',
            'anakke' => 'Anak ke',
            'jumlah_bersaudara' => 'Jumlah bersaudara',
            'no_telepon_pasien' => 'No telepon pasien',
            'no_mobile_pasien' => 'No mobile pasien',
            'warga_negara' => 'Warga negara',
            'photopasien' => 'Photo pasien',
            'alamatemail' => 'Alamat email',
            'nama_ibu' => 'Nama ibu',
            'nama_ayah' => 'Nama ayah',
            'dokrekammedis_id' => 'Dok rekam medis',
            'tgl_meninggal' => 'Tgl meninggal',
            'pegawai_id' => 'Pegawai',
            'loginpemakai_id' => 'Login pemakai',
            'garis_latitude' => 'Garis latitude',
            'garis_longitude' => 'Garis longitude',
            'statusrekammedis' => 'Status rekam medis',
            'additional_data' => 'Additional data',
            'created_date' => 'Created date',
            'created_by' => 'Created by',
            'modified_count' => 'Modified count',
            'last_modified_date' => 'Last modified date',
            'last_modified_by' => 'Last modified by',
            'is_deleted' => 'Is deleted',
            'is_active' => 'Is active',
            'deleted_date' => 'Deleted date',
            'deleted_by' => 'Deleted by',
            'profilrs_id' => 'Profil rs',
            'alamat_sekarang' => 'Alamat sekarang',
            'additional_identitas' => 'Additional Identitas',
            'bahasa_sehari' => 'Bahasa Sehari-hari',
            'alamatdepan' => 'Alamat Depan',
        ];
    }
}
