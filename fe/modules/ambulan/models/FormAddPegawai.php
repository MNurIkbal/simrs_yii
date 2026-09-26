<?php

namespace app\modules\ambulan\models;

use Yii;

class FormAddPegawai extends \yii\base\Model
{
    
     public $pegawai_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pegawai_id'],'safe'],
            [['pegawai_id'],'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pegawai_id' => 'Petugas',
            'qty' => 'Qty'
        ];
    }
}
