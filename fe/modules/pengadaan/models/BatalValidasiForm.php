<?php

namespace app\modules\pengadaan\models;

use Yii;

class BatalValidasiForm extends \yii\base\Model
{

    public $catatan;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['catatan'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            
            'catatan' => 'Catatan',
            
        ];
    }
}
