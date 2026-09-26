<?php

namespace Doco\models;

use Yii;

class KonfigPelayanan extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'konfigpelayanan_k';
    }

    public function getAdditionalConditions($payload)
    {
        $getRecord = self::find()->select([
            'additional_condition'
        ])->where([
            'nama_fitur' => $payload['nama_fitur'],
            'instalasi_id' => $payload['instalasi_id']
        ])->asArray()->one();

        return is_array($getRecord['additional_condition']) ? $getRecord['additional_condition'] : json_decode($getRecord['additional_condition'], true);
    }

}
