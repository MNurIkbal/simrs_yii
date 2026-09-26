<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\penatajasa\models;

use Yii;

class PencarianPasienForm extends \yii\base\Model
{
    public $no_rekam_medik;
    public $no_pendaftaran;

    public function rules()
    {
        return [
            [ ['no_rekam_medik','no_pendaftaran'], 'safe'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'no_rekam_medik' => Yii::t('fe', 'No Rekam Medik'),
            'no_pendaftaran' => Yii::t('fe', 'No Pendaftaran')
        ];
    }
}