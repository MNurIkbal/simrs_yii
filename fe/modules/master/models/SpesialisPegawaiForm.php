<?php

/**
 * @Author: Fajar Supriadi
 * @Date:   2021-12-31 
 * @Last Modified by:   Fajar Supriadi
 * @Last Modified time: 2021-12-31
 */

namespace app\modules\master\models;

use Yii;

class SpesialisPegawaiForm extends \yii\base\Model
{
    public $pegawai_id;
    public $spesialis_id;
    public $nama_pegawai;

    public function rules()
    {
         return [
            [[
                'pegawai_id',
            ], 'required','message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')],
            [[
                
                'spesialis_id', 
                'nama_pegawai',
            ], 'safe'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'pegawai_id' => \Yii::t('fe', 'Dokter'),
            'nama_pegawai' => \Yii::t('fe', 'Nama Dokter'),
            'spesialis_id' => \Yii::t('fe', 'Spesialis'),
        ];
    }


}
