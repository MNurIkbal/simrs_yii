<?php

namespace app\modules\antrian\models;


/**
 * @Author: Naufal
 * @Date:   2018-02-20 16:04:34
 */


use Yii;

class PasienForm extends \app\components\DocoBaseModel
{
    public $nama_pasien;
    public $jeniskelamin;
    public $jenisidentitas;
    public $tanggal_lahir;
    public $tmp_tanggal_lahir;
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
    public $bahasa_sehari;
    public $no_rekam_medik;
    public $nopeserta_bpjs;
    public $golonganumur_id;
    public $dokrekammedis_id;
    public $pegawai_id;
    public $loginpemakai_id;
    public $photopasien;
    public $pasien_id;
    public $statusrekammedis;
    public $is_aps;
    public $nama_panggilan;
    public $tgl_meninggal;
    public $additional_data;
    public $additional_identitas;
    public $catatanpenting_pasien;
    public $username;
    public $password;
    public $reason;
    public $alamatdepan;
    public $is_pj;

    protected $xssProtected = [
        'no_identitas_pasien',
        'nama_pasien',
        'nama_panggilan',
        'tempat_lahir',
        'umur',
        'nama_ibu',
        'nama_ayah',
        'anakke',
        'jumlah_bersaudara',
        'alamat_pasien',
        'rt',
        'rw',
        'catatanpenting_pasien'
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
                    //'jenisidentitas',
                    'tanggal_lahir',
                    // 'alamat_pasien',
                    // 'statusperkawinan',
                    // 'golongandarah',
                    // 'tempat_lahir',
                    // 'umur',
                    // 'propinsi_id',
                    // 'kabupaten_id',
                    // 'kecamatan_id',
                    // 'no_telepon_pasien',
                    // 'no_identitas_pasien',
                    // 'pendidikan_id',
                    // 'pekerjaan_id',
                     // 'no_identitas_pasien',
                ],
                'required', 'on' => 'default'
            ],
            [
                [
                    'nama_pasien',
                    // 'tempat_lahir',
                    'tanggal_lahir',
                    // 'alamat_pasien',
                    'jeniskelamin',
                    'no_telepon_pasien',
                    // 'jenisidentitas',
                    // 'no_identitas_pasien',
                ],
                'required', 'on' => 'pendaftaran-rajal',
            ],
            // [
            //     [
            //         'nama_pasien',
            //         // 'tempat_lahir',
            //         'tanggal_lahir',
            //         // 'alamat_pasien',
            //         'jeniskelamin',
            //         // 'no_telepon_pasien',
            //         // 'jenisidentitas',
            //         // 'no_identitas_pasien',
            //     ],
            //     'required', 'on' => 'pendaftaran-ranap',
            // ],
            [
                [],
                'safe', 'on' => 'pendaftaran-pasien-lama',
            ],
            [
                [
                    // 'jenisidentitas',
                    // 'no_identitas_pasien',
                    'nama_pasien',
                    'tanggal_lahir',
                    'jeniskelamin'
                ],
                'required', 'on' => 'pendaftaran-mcu',
            ],
            [
                [
                    'nama_pasien',
                    'jeniskelamin',
                    'tanggal_lahir',
                    'alamat_pasien',
                    'statusperkawinan',
                    // 'golongandarah',
                    'tempat_lahir',
                    'umur',
                    'propinsi_id',
                    'kabupaten_id',
                    'kecamatan_id',
                ],
                'required', 'on' => 'bbl',
            ],
            [
                [
                    // 'jenisidentitas',
                    // 'no_identitas_pasien',
                    'nama_pasien',
                    'jeniskelamin',
                    'tanggal_lahir',
                ],
                'required', 'on' => 'pendaftaran-igd',
            ],
            [
                [
                    // 'jenisidentitas',
                    // 'no_identitas_pasien',
                    'nama_pasien',
                    'tanggal_lahir',
                    'jeniskelamin',
                    'username',
                    'password',
                    'reason'
                ],
                'required', 'on' => 'edit-pasien',
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
                    'tmp_tanggal_lahir',
                    'tgl_meninggal',
                    'created_date',
                    'last_modified_date',
                    'deleted_date',
                    'nama_ibu',
                    'namadepan',
                    'pekerjaan_id',
                    'suku_id',
                    'agama',
                    'bahasa_sehari',
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
                    'loginpemakai_id',
                    'statusrekammedis',
                    'is_aps',
                    'nama_panggilan',
                    'additional_data',
                    'additional_identitas',
                    'catatanpenting_pasien',
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
                'suku_id',
                'anakke',
                'jumlah_bersaudara',
                'dokrekammedis_id',
                'pegawai_id',
                'loginpemakai_id'
            ], 'default', 'value' => null],
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
                'additional_data',
            ], 'string'],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [[
                // 'jenisidentitas',
                'namadepan',
                'jeniskelamin',
                'statusperkawinan',
                'agama',
                'no_mobile_pasien',
                'no_telepon_pasien'
            ], 'string', 'max' => 20],
            // [['no_identitas_pasien'], 'string', 'max' => 16],
            // [['no_identitas_pasien'], 'each', 'rule' => ['string', 'max' => 16]],
            [['nama_pasien', 'nama_ibu', 'nama_ayah'], 'string', 'max' => 50],
            [['tempat_lahir', 'warga_negara'], 'string', 'max' => 25],
            // [['no_telepon_pasien'], 'string', 'max' => 15],
            [['photopasien'], 'string', 'max' => 200],
            [['alamatemail'], 'string', 'max' => 100],
            [['alamatemail'], 'email'],
        ];
    }

    public function validateNoIdentitasPaseien($attribute, $params)
    {
        # code...
        if(is_array($this->no_identitas_pasien) == true) {
            foreach ($this->no_identitas_pasien as $value) {
                # code...
                if (!preg_match("/^[0-9][0-9]*$/", $value)) {
                    # code...
                    $this->addError('no_identitas_pasien', 'NIK tidak berupa angka.');
                }
                if (strlen($value) > 16) {
                    # code...
                    $this->addError('no_identitas_pasien', 'NIK tidak boleh lebih dari 16.');
                }
            }
        }
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pasien_id' => Yii::t('fe', 'Pasien ID'),
            'no_rekam_medik' => Yii::t('fe', 'No rekam medik'),
            'tgl_rekam_medik' => Yii::t('fe', 'Tgl rekam medik'),
            'jenisidentitas' => Yii::t('fe', 'Jenis identitas'),
            'no_identitas_pasien' => Yii::t('fe', 'No identitas pasien'),
            'namadepan' => Yii::t('fe', 'Nama depan'),
            'nama_pasien' => Yii::t('fe', 'Nama pasien'),
            'nama_panggilan' => Yii::t('fe', 'Nama panggilan'),
            'jeniskelamin' => Yii::t('fe', 'Jenis kelamin'),
            'tempat_lahir' => Yii::t('fe', 'Tempat lahir'),
            'tanggal_lahir' => Yii::t('fe', 'Tanggal lahir'),
            'golonganumur_id' => Yii::t('fe', 'Golongan umur'),
            'alamat_pasien' => Yii::t('fe', 'Alamat pasien'),
            'rt' => Yii::t('fe', 'RT'),
            'rw' => Yii::t('fe', 'RW'),
            'propinsi_id' => Yii::t('fe', 'Propinsi'),
            'kabupaten_id' => Yii::t('fe', 'Kabupaten'),
            'kecamatan_id' => Yii::t('fe', 'Kecamatan'),
            'kelurahan_id' => Yii::t('fe', 'Kelurahan'),
            'pendidikan_id' => Yii::t('fe', 'Pendidikan'),
            'pekerjaan_id' => Yii::t('fe', 'Pekerjaan'),
            'suku_id' => Yii::t('fe', 'Suku'),
            'statusperkawinan' => Yii::t('fe', 'Status perkawinan'),
            'agama' => Yii::t('fe', 'Agama'),
            'bahasa_sehari' => Yii::t('fe', 'Bahasa Sehari-hari'),
            'golongandarah' => Yii::t('fe', 'Golongan darah'),
            'rhesus' => Yii::t('fe', 'Rhesus'),
            'anakke' => Yii::t('fe', 'Anak ke'),
            'jumlah_bersaudara' => Yii::t('fe', 'Jumlah bersaudara'),
            'no_telepon_pasien' => Yii::t('fe', 'No telepon pasien'),
            'no_mobile_pasien' => Yii::t('fe', 'No mobile pasien'),
            'warga_negara' => Yii::t('fe', 'Warga negara'),
            'photopasien' => Yii::t('fe', 'Photo pasien'),
            'alamatemail' => Yii::t('fe', 'Alamat email'),
            'nama_ibu' => Yii::t('fe', 'Nama ibu'),
            'nama_ayah' => Yii::t('fe', 'Nama ayah'),
            'dokrekammedis_id' => Yii::t('fe', 'Dok rekam medis'),
            'tgl_meninggal' => Yii::t('fe', 'Tgl meninggal'),
            'pegawai_id' => Yii::t('fe', 'Pegawai'),
            'loginpemakai_id' => Yii::t('fe', 'Login pemakai'),
            'garis_latitude' => Yii::t('fe', 'Garis latitude'),
            'garis_longitude' => Yii::t('fe', 'Garis longitude'),
            'statusrekammedis' => Yii::t('fe', 'Status rekam medis'),
            'additional_data' => Yii::t('fe', 'Additional data'),
            'created_date' => Yii::t('fe', 'Created date'),
            'created_by' => Yii::t('fe', 'Created by'),
            'modified_count' => Yii::t('fe', 'Modified count'),
            'last_modified_date' => Yii::t('fe', 'Last modified date'),
            'last_modified_by' => Yii::t('fe', 'Last modified by'),
            'is_deleted' => Yii::t('fe', 'Is deleted'),
            'is_active' => Yii::t('fe', 'Is active'),
            'deleted_date' => Yii::t('fe', 'Deleted date'),
            'deleted_by' => Yii::t('fe', 'Deleted by'),
            'profilrs_id' => Yii::t('fe', 'Profil rs'),
            'alamat_sekarang' => Yii::t('fe', 'Alamat sekarang'),
            'catatanpenting_pasien' => Yii::t('fe', 'Catatan Penting Pasien'),
            'reason' => Yii::t('fe', 'Alasan'),
            'alamatdepan' => Yii::t('fe', 'Sebutan Jalan'),
        ];
    }
}
