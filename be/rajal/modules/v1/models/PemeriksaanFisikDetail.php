<?php

namespace app\modules\v1\models;

use Yii;

class PemeriksaanFisikDetail extends \Doco\components\DocoActiveRecord
{

    public static function tableName()
    {
        return 'pemeriksaanfisikdetail_t';
    }

    public function rules()
    {
        return [
            [
                [
                    'pemeriksaanfisik_id',
                    'masalah_diagnosa_medis',
                    'rencana_laksana_medis',
                    'additional_data',
                    'is_deleted',
                    'is_active',
                    'created_date',
                    'created_by',
                ],
                'safe'
            ]
        ];
    }
}
