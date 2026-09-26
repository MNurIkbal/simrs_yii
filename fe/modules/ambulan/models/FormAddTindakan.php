<?php

namespace app\modules\ambulan\models;

use Yii;

class FormAddTindakan extends \yii\base\Model
{
    
     public $daftartindakan_id;
     public $qty;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['daftartindakan_id','qty'],'safe'],
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
            'daftartindakan_id' => 'Tindakan Pelayanan',
            'qty' => 'Qty'
        ];
    }
}
