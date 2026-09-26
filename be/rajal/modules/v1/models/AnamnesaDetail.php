<?php
namespace app\modules\v1\models;

use Yii;

class AnamnesaDetail extends \Doco\components\DocoActiveRecord {

    public static function tableName(){
        return 'anamnesadetail_t';
    }

    public function rules()
    {
        return [
            [
                [
                    'anamnesa_id',
                    'diagnosa_keperawatan',
                    'tujuan_terukur',
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