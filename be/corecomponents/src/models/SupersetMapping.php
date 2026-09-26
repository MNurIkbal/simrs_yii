<?php

namespace Doco\models;

class SupersetMapping extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'superset_mapping_m';
    }

    public function rules()
    {
        return [
            [['superset_mapping_id'], 'integer'],
            [['mapping_key', 'mapping_identity'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'superset_mapping_id' => 'Superset Mapping ID',
            'mapping_key' => 'Mapping Key',
            'mapping_identity' => 'Mapping Identity',
        ];
    }
}
