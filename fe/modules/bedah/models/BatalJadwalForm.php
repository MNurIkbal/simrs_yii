<?php

namespace app\modules\bedah\models;

class BatalJadwalForm extends \yii\base\Model
{
    public $catatan;

    public function rules()
    {
        return [
            [['catatan'], 'required'],
            [['catatan'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'catatan' => \Yii::t('fe', 'Catatan'),
        ];
    }
}
