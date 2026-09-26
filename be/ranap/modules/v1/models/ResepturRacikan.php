<?php

namespace app\modules\v1\models;

use Yii;
use Doco\components\DocoActiveRecord;

class ResepturRacikan extends DocoActiveRecord {
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'resepturracikan_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['resepturracikan_id', 'reseptur_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['racikan', 'rke', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['additional_data', 'racikan'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_racikan'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'resepturracikan_id' => 'Reseptur Racikan ID',
            'reseptur_id' => 'Reseptur ID',
            'rke' => 'R Ke',
            'no_racikan' => 'No Racikan',
            'racikan' => 'Racikan',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
        ];
    }
}
