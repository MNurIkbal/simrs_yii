<?php

namespace Doco\models;

use Yii;

/**
 * This is the model class for table "kamartempattidur_m".
 *
 * @property int $kamartempattidur_id
 * @property int $kamarruangan_id
 * @property string $no_tempattidur
 * @property bool $status_isi
 * @property int $kettempattidur_id
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
class KamarTempatTidur extends \Doco\components\DocoActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kamartempattidur_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kamarruangan_id', 'no_tempattidur'], 'required'],
            [['no_tempattidur','kamarruangan_id'], 'checkUnique'],
            [['kamarruangan_id', 'kettempattidur_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['kamarruangan_id', 'kettempattidur_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['status_isi', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['no_tempattidur'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kamartempattidur_id' => 'Kamartempattidur ID',
            'kamarruangan_id' => 'Kamarruangan ID',
            'no_tempattidur' => 'No Tempattidur',
            'status_isi' => 'Status Isi',
            'kettempattidur_id' => 'Kettempattidur ID',
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
