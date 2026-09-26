<?php

namespace app\modules\pendaftaran\models;
use app\components\DocoBaseModel;

use Yii;

class PencarianIdentitasForm extends DocoBaseModel
{
    public $jenis_pencarian;
    public $nik;
    public $no_kartu;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_kartu' => Yii::t('fe', 'No. Kartu'),
            'nik' => Yii::t('fe', 'NIK'),
        ];
    }
}
