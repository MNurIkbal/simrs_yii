<?php
namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "restriction_list_obat_mp".
 *
 * @property int $restriction_list_obat_id
 * @property int $restriction_obat_id
 * @property int $obatalkes_id
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 */
class RestrictionListObat extends \Doco\components\DocoActiveRecord
{
    public static function tableName()
    {
        return 'restriction_list_obat_mp';
    }

    public function rules()
    {
        return [
            [['restriction_obat_id', 'obatalkes_id'], 'required'],
            [['restriction_obat_id', 'obatalkes_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'restriction_list_obat_id' => 'ID',
            'restriction_obat_id' => 'Restriction Obat ID',
            'obatalkes_id' => 'Obatalkes ID',
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