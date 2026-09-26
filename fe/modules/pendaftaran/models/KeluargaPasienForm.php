<?php

/**
 * @Author: Fajar Supriadi
 * @Date:   2020-01-21 10:23:47
 * @Last Modified by:   Fajar Supriadi
 * @Last Modified time: 2020-01-21 10:23:47
 */

namespace app\modules\pendaftaran\models;

use Yii;

class KeluargaPasienForm extends \yii\base\Model
{
    public $keluarga_nama;
    public $keluarga_jk;
    public $keluarga_hubungan;
    public $keluarga_alamat;
    public $keluarga_no_telepon;
    public $keluarga_namadepan;
    public $keluarga_propinsi_id;
    public $keluarga_kabupaten_id;
    public $keluarga_kecamatan_id;
    public $keluarga_kelurahan_id;
    public $keluarga_pekerjaan_id;
    public $keluarga_rt;
    public $keluarga_rw;
    public $keluargapasien_id;
    public $alamatdepan;

    public function rules()
    {
         return [
            [[
                'keluarga_nama',
                'keluarga_namadepan', 
                'keluarga_jk',
            ], 'required','message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')],
            [[
                'keluarga_jk',
                'keluarga_hubungan',
                'keluarga_alamat',
                'keluarga_no_telepon',
                'keluarga_namadepan',
                'keluarga_propinsi_id',
                'keluarga_kabupaten_id',
                'keluarga_kecamatan_id',
                'keluarga_kelurahan_id',
                'keluarga_pekerjaan_id',
                'keluarga_rt',
                'keluarga_rw',
                'keluargapasien_id',
                'alamatdepan',
            ], 'safe'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'keluarga_nama' => \Yii::t('fe', 'Nama'),
            'keluarga_jk' => \Yii::t('fe', 'Jenis kelamin'),
            'keluarga_hubungan' => \Yii::t('fe', 'Hubungan'),
            'keluarga_alamat'=> \Yii::t('fe', 'Alamat'),
            'keluarga_no_telepon'=> \Yii::t('fe', 'No telepon'),
            'is_pj'=> \Yii::t('fe', 'Penangung Jawab'),
            'keluarga_namadepan' => \Yii::t('fe', 'Nama Depan'),
            'keluarga_propinsi_id' => \Yii::t('fe', 'Propinsi'),
            'keluarga_kabupaten_id' => \Yii::t('fe', 'Kabupaten/Kota'),
            'keluarga_kecamatan_id'=> \Yii::t('fe', 'Kecamatan'),
            'keluarga_kelurahan_id'=> \Yii::t('fe', 'Kelurahan'),
            'keluarga_pekerjaan_id'=> \Yii::t('fe', 'Pekerjaan'),
            'keluarga_rt'=> \Yii::t('fe', 'RT'),
            'keluarga_rw'=> \Yii::t('fe', 'RW'),
            'alamatdepan'=> \Yii::t('fe', 'Sebutan Jalan'),
        ];
    }


}
