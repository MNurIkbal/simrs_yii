<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "profilrumahsakit_v".
 *
 * @property string $nokode_rumahsakit
 * @property string $tglregistrasi
 * @property string $nama_rumahsakit
 * @property string $jenis_rs
 * @property string $kelas_rs
 * @property string $nama_penyelenggara
 * @property string $kode_pos
 * @property string $no_telp_profilrs
 * @property string $no_faksimili
 * @property string $email
 * @property string $notelphumas
 * @property string $website
 * @property string $luastanah
 * @property string $luasbangunan
 * @property string $nomor_suratizin
 * @property string $tgl_suratizin
 * @property string $oleh_suratizin
 * @property string $sifat_suratizin
 * @property string $masaberlaku_dari
 * @property string $masaberlaku_sampai
 * @property string $statuskepemilikanrs
 * @property string $pentahapanakreditasrs
 * @property string $statusakreditasrs
 * @property string $tglakreditasi
 */
class ProfilRumahSakitV extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'profilrumahsakit_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tglregistrasi', 'tgl_suratizin', 'masaberlaku_dari', 'masaberlaku_sampai', 'tglakreditasi'], 'safe'],
            [['nokode_rumahsakit'], 'string', 'max' => 10],
            [['nama_rumahsakit', 'nama_penyelenggara', 'kode_pos', 'notelphumas', 'luastanah', 'luasbangunan', 'statuskepemilikanrs'], 'string', 'max' => 100],
            [['jenis_rs', 'kelas_rs'], 'string', 'max' => 200],
            [['no_telp_profilrs', 'no_faksimili'], 'string', 'max' => 15],
            [['email', 'website', 'sifat_suratizin', 'pentahapanakreditasrs', 'statusakreditasrs'], 'string', 'max' => 50],
            [['nomor_suratizin'], 'string', 'max' => 20],
            [['oleh_suratizin'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'nokode_rumahsakit' => 'Nokode Rumahsakit',
            'tglregistrasi' => 'Tglregistrasi',
            'nama_rumahsakit' => 'Nama Rumahsakit',
            'jenis_rs' => 'Jenis Rs',
            'kelas_rs' => 'Kelas Rs',
            'nama_penyelenggara' => 'Nama Penyelenggara',
            'kode_pos' => 'Kode Pos',
            'no_telp_profilrs' => 'No Telp Profilrs',
            'no_faksimili' => 'No Faksimili',
            'email' => 'Email',
            'notelphumas' => 'Notelphumas',
            'website' => 'Website',
            'luastanah' => 'Luastanah',
            'luasbangunan' => 'Luasbangunan',
            'nomor_suratizin' => 'Nomor Suratizin',
            'tgl_suratizin' => 'Tgl Suratizin',
            'oleh_suratizin' => 'Oleh Suratizin',
            'sifat_suratizin' => 'Sifat Suratizin',
            'masaberlaku_dari' => 'Masaberlaku Dari',
            'masaberlaku_sampai' => 'Masaberlaku Sampai',
            'statuskepemilikanrs' => 'Statuskepemilikanrs',
            'pentahapanakreditasrs' => 'Pentahapanakreditasrs',
            'statusakreditasrs' => 'Statusakreditasrs',
            'tglakreditasi' => 'Tglakreditasi',
        ];
    }
}
