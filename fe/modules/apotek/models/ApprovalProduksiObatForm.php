<?php

/**
* @author Asri Nurul M
**/

namespace Doco\apotek\models;

use Yii;

class ApprovalProduksiObatForm extends \yii\base\Model
{
    public $pegawai_id;
    public $tgl_produksi;
    public $tgl_kadaluarsa;
    public $batch_number;
    public $produksiobatalkes_id;

    public function rules()
    {
        return [
            [[
                'pegawai_id',
                'tgl_kadaluarsa',
                'produksiobatalkes_id'
            ], 'required'],
            [['pegawai_id','tgl_produksi','tgl_kadaluarsa','batch_number','produksiobatalkes_id'],'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pegawai_id' => Yii::t('fe','Approved By')
        ];
    }
}