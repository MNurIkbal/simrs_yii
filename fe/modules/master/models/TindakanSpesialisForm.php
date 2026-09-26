<?php

namespace app\modules\master\models;

class TindakanSpesialisForm extends \yii\base\Model
{
    public $spesialis_id;
    public $daftartindakan_id;
    public $is_active;

    public function rules()
    {
        return [
            [['spesialis_id', 'daftartindakan_id'], 'integer'],
            [['is_active'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'spesialis_id' => \Yii::t('fe', 'Spesialis'),
            'daftartindakan_id' => \Yii::t('fe', 'Nama Tindakan'),
            'is_active' => \Yii::t('fe', 'Aktif'),
        ];
    }
}
