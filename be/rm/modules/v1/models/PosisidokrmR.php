<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "posisidokrm_r".
 *
 * @property int $posisidokrm_id
 * @property int $dokrekammedis_id
 * @property int $ruanganakhir_id
 * @property bool $is_pesan
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
class PosisidokrmR  extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'posisidokrm_r';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dokrekammedis_id', 'ruanganakhir_id'], 'required'],
            [['dokrekammedis_id', 'ruanganakhir_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['dokrekammedis_id', 'ruanganakhir_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_pesan', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'posisidokrm_id' => 'Posisidokrm ID',
            'dokrekammedis_id' => 'Dokrekammedis ID',
            'ruanganakhir_id' => 'Ruanganakhir ID',
            'is_pesan' => 'Is Pesan',
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
