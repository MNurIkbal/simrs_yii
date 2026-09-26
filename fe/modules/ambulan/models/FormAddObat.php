<?php

namespace app\modules\ambulan\models;

use Yii;

class FormAddObat extends \yii\base\Model
{
    
     public $obatalkes_id;
     public $qty;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['obatalkes_id','qty'],'safe'],
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
            'obatalkes_id' => 'Tindakan Pelayanan',
            'qty' => 'Qty'
        ];
    }
}
