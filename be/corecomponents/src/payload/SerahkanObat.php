<?php

namespace Doco\payload;

use Yii;
use Doco\components\DocoBaseModel;

class SerahkanObat extends DocoBaseModel {
    public $nomor;
    public $ruangan_id;
    public $ruanganasal_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'nomor',
                'ruangan_id'
            ], 'required'],
            [[
                'nomor', 
                'ruangan_id', 
                'ruanganasal_id'
            ],'safe'],
            [[ 
                'ruangan_id', 
                'ruanganasal_id'
            ], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'nomor' => 'Nomor Resep',
            'ruangan_id' => 'ID Ruangan',
            'ruanganasal_id' => 'ID Ruangan Asal',
            'pegawai_menyerahkan_id' => 'Pegawai Menyerahkan'
        ];
    }
}
