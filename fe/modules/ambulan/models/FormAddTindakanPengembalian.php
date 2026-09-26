<?php

namespace app\modules\ambulan\models;

use Yii;

class FormAddTindakanPengembalian extends \yii\base\Model
{
    
     public $daftartindakan_id;
     public $tarif;
     public $qty;
     public $cyto;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['daftartindakan_id','tarif','qty','cyto'],'safe'],
            [['daftartindakan_id','qty'],'required'],
            [['qty'], 'number','min' => 1]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'daftartindakan_id' => 'Tindakan',
            'tarif'             => 'Tarif',
            'qty'               => 'Qty',
            'cyto'              => 'Cyto'
        ];
    }
}
