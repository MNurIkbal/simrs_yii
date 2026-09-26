<?php

namespace app\modules\v1\models;

use Yii;

class GeneratedPO extends \Doco\components\DocoActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'generatedpo_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tipe','podetail_id','prdetail_id','status'], 'required'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['is_deleted'], 'default','value'=>false],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
        ];
    }
}
