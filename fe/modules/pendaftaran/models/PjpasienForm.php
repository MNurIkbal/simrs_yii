<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-04-12 14:29:48
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-04-12 15:14:34
 */

namespace app\modules\pendaftaran\models;

use Yii;

class PjpasienForm extends \yii\base\Model
{
    public $pj_pengantar;
    public $pj_nama;
    public $pj_jk;
    public $pj_jenis_identitas;
    public $pj_no_identitas;
    public $pj_hubungan;
    public $pj_tempat_lahir;
    public $pj_tanggal_lahir;
    public $pj_umur;
    public $pj_alamat;
    public $pj_no_telepon;
    public $pj_namadepan;
    public $pj_propinsi_id;
    public $pj_kabupaten_id;
    public $pj_kecamatan_id;
    public $pj_kelurahan_id;
    public $pj_pekerjaan_id;
    public $pj_rt;
    public $pj_rw;
    public $pj_kode_pos;
    public $pj_pt;

    public function rules()
    {
         return [
            [[
                'pj_pengantar',
                'pj_nama',
                'pj_jk'
            ], 'required', 'on' => 'default', 'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')],
            [[
                'pj_pengantar',
                'pj_nama',
                'pj_jk'
            ], 'safe','on' => 'pendaftaran-igd'],
            [[
                'pj_pengantar',
                'pj_nama',
                'pj_jk',
                'pj_jenis_identitas',
                'pj_no_identitas',
                'pj_hubungan',
                'pj_tempat_lahir',
                'pj_tanggal_lahir',
                'pj_umur',
                'pj_alamat',
                'pj_no_telepon',
                'pj_namadepan',
                'pj_propinsi_id',
                'pj_kabupaten_id',
                'pj_kecamatan_id',
                'pj_kelurahan_id',
                'pj_pekerjaan_id',
                'pj_rt',
                'pj_rw',
                'pj_kode_pos',
                'pj_pt'
            ], 'safe'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'pj_pengantar' => \Yii::t('fe', 'Pengantar'),
            'pj_nama' => \Yii::t('fe', 'Nama'),
            'pj_jk' => \Yii::t('fe', 'Jenis kelamin'),
            'pj_jenis_identitas' => \Yii::t('fe', 'Jenis identitas'),
            'pj_no_identitas' => \Yii::t('fe', 'No identitas'),
            'pj_hubungan' => \Yii::t('fe', 'Hubungan'),
            'pj_tempat_lahir' => \Yii::t('fe', 'Tempat lahir'),
            'pj_tanggal_lahir'=> \Yii::t('fe', 'Tanggal lahir'),
            'pj_umur'=> \Yii::t('fe', 'Umur'),
            'pj_alamat'=> \Yii::t('fe', 'Alamat'),
            'pj_no_telepon'=> \Yii::t('fe', 'No telepon'),
            'is_pj'=> \Yii::t('fe', 'Penangung Jawab'),
            'pj_namadepan' => \Yii::t('fe', 'Nama Depan'),
            'pj_propinsi_id' => \Yii::t('fe', 'Propinsi'),
            'pj_kabupaten_id' => \Yii::t('fe', 'Kabupaten/Kota'),
            'pj_kecamatan_id'=> \Yii::t('fe', 'Kecamatan'),
            'pj_kelurahan_id'=> \Yii::t('fe', 'Kelurahan'),
            'pj_pekerjaan_id'=> \Yii::t('fe', 'Pekerjaan'),
            'pj_rt'=> \Yii::t('fe', 'RT'),
            'pj_rw'=> \Yii::t('fe', 'RW'),
            'pj_kode_pos'=> \Yii::t('fe', 'Kode Pos'),
            'pj_pt'=> \Yii::t('fe', 'PT'),
        ];
    }


}
