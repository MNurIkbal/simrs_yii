<?php

namespace app\modules\pendaftaran\models;


use Yii;

class AksesForm extends \yii\base\Model
{
    public $aksesform_id;
    public $akses;
    public $pasien_id;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['akses','aksesform_id','pasien_id'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'akses' => Yii::t('fe', 'Edit'),
        ];
    }
}
