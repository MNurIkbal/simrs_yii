<?php

namespace app\modules\ranap\models;

use Yii;

class KelahiranBayiForm extends \yii\base\Model
{
    public $kelahiranbayi_id;
    public $pendaftaran_id;
    public $pasienadmisi_id;
    public $bayi_urut;
    public $berat_badan;
    public $tinggi_badan;
    public $jenis_kelamin;
    public $penilaian;
    public $kondisi_bayi;
    public $is_asi;
    public $keterangan_asi;
    public $masalah_lain;
    public $hasil;
    public $is_normal;
    public $normal_tindakan;
    public $asfiksia;
    public $asfiksia_tindakan;
    public $cacat_kondisi;
    public $hipotermi_keterangan;
    public $keterangan_asi_ya;
    public $keterangan_asi_tidak;
    public $asfiksia_tindakan_lainnya;
    public $warna_kulit;
    public $tgl_lahir;
    public $pegawai_id;
    public $kamartempattidur_id;
    public $lingkar_kepala;
    public $golongan_darah;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['berat_badan','tinggi_badan','jenis_kelamin','penilaian','kondisi_bayi','is_asi', 'tgl_lahir'], 'required'],
            [[ 'pasienadmisi_id', 'bayi_urut', 'jenis_kelamin', 'penilaian'], 'default', 'value' => null],
            [['pasienadmisi_id', 'bayi_urut', 'jenis_kelamin', 'penilaian'], 'integer'],
            // [['berat_badan', 'tinggi_badan'], 'number'],
            [['kondisi_bayi', 'masalah_lain', 'hasil'], 'string'],
            // [['is_asi'], 'boolean'],
            [['jenis_kelamin', 'warna_kulit', 'pegawai_id', 'kamartempattidur_id', 'lingkar_kepala', 'golongan_darah'], 'safe'],
            [['keterangan_asi'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kelahiranbayi_id' => 'Kelahiranbayi ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'bayi_urut' => 'Bayi Urut',
            'berat_badan' => 'Berat Badan',
            'tinggi_badan' => 'Tinggi Badan',
            'jenis_kelamin' => 'Jenis Kelamin',
            'penilaian' => 'Penilaian',
            'kondisi_bayi' => 'Kondisi Bayi',
            'is_asi' => 'Pemberian Asi',
            'keterangan_asi' => 'Keterangan Asi',
            'masalah_lain' => 'Masalah Lain',
            'hasil' => 'Hasil',
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
            'normal_tindakan' => 'Tindakan',
            'warna_kulit' => 'Warna Kulit',
            'asfiksia_tindakan' => 'Tindakan',
            'tgl_lahir' => 'Tanggal Lahir',
            'lingkar_kepala' => 'Lingkar Kepala',
            'golongan_darah' => 'Golongan Darah',
            'pegawai_id' => 'Dokter',
            'kamartempattidur_id' => 'Kamar Ruangan Bayi',
        ];
    }
}
