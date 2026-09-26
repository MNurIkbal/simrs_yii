<?php

namespace app\modules\v1\models;

use Yii;

class KonfigRak extends \Doco\components\DocoActiveRecord {
    /**
     * {@inheritdoc}
     */

    public static function tableName() {
        return 'konfigrak_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['obatalkes_id'], 'required'],
            [['additional_data', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['obatalkes_id', 'ruangan_id', 'rakobat_id', 'min_stok', 'max_stok', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'obatalkes_id' => 'Obatalkes ID',
            'ruangan_id' => 'Ruangan ID',
            'rakobat_id' => 'Rak Obat ID',
            'min_stok' => 'Minimum Stok',
            'max_stok' => 'Maksimum Stok',
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
