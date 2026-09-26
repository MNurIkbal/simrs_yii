<?php

namespace app\modules\ambulan\models;

use Yii;

class FormAddObatPengembalian extends \yii\base\Model
{
    
     public $obatalkes_id;
     public $tarif;
     public $qty;
     public $cyto;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['obatalkes_id','tarif','qty','cyto'],'safe'],
            [['obatalkes_id','qty'],'required'],
            [['qty'], 'number','min' => 1]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'obatalkes_id' => 'Obat Alkes',
            'tarif'        => 'Tarif',
            'qty'          => 'Qty',
            'cyto'         => 'Cyto'
        ];
    }
}
