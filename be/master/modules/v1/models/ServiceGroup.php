<?php

namespace app\modules\v1\models;

use Yii;

class ServiceGroup extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'servicegroup_m';
    }

    public function rules()
    {
        return [
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['servicegroup_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['servicegroup_nama'], 'string', 'max' => 255]
        ];
    }
}
