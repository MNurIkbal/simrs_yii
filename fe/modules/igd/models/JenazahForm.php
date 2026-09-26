<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2019-01-22 14:34:48
 * @Last Modified by:   Sigit
 * @Last Modified time: 2019-01-29 12:08:20
 */

namespace app\modules\igd\models;

use Yii;

class JenazahForm extends \yii\base\Model
{
    public $kondisi;
    public $hubungan_keluarga;
    public $nama_pj;
    public $jeniskelamin_id;
    public $umur;
    public $no_kontak;
    public $alamat;
    public $pegawai;
    public $list_order;
    public $list_linen;

    const SCENARIO_REQ = 'req_jenazah_form';

    public function rules()
    {
        return [
            [
                [
                    'kondisi', 'hubungan_keluarga', 'nama_pj', 'jeniskelamin_id', 'umur', 'no_kontak', 
                    'alamat', 'pegawai', 'list_order','list_linen'
                ], 
                'safe'
            ],
            [
                [
                    'hubungan_keluarga', 'nama_pj', 'jeniskelamin_id', 'umur', 'no_kontak'
                ], 
                'required',
                'on' => self::SCENARIO_REQ
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'kondisi' => \Yii::t('fe', 'Kondisi'),
            'hubungan_keluarga' => \Yii::t('fe', 'Hubungan Keluarga'),
            'nama_pj' => \Yii::t('fe', 'Nama Penanggung Jawab'),
            'jeniskelamin_id' => \Yii::t('fe', 'Jenis Kelamin'),
            'umur' => \Yii::t('fe', 'Umur'),
            'no_kontak' => \Yii::t('fe', 'Nomor Kontak'),
            'alamat' => \Yii::t('fe', 'Alamat'),
            'pegawai' => \Yii::t('fe', 'Pegawai'),
            'list_order' => \Yii::t('fe', 'List Order'),
            'list_linen' => \Yii::t('fe', 'List Linen'),
        ];
    }

}