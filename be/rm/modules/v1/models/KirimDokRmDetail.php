<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kirimdokrmdetail_t".
 *
 * @property int $kirimdokrmdetail_id
 * @property int $kirimdokrm_id
 * @property int $pesandokrmdetail_id
 * @property int $dokrekammedis_id
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
class KirimDokRmDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kirimdokrmdetail_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kirimdokrm_id', 'pesandokrmdetail_id', 'dokrekammedis_id'], 'required'],
            [['kirimdokrm_id', 'pesandokrmdetail_id', 'dokrekammedis_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['kirimdokrm_id', 'pesandokrmdetail_id', 'dokrekammedis_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kirimdokrmdetail_id' => 'Kirimdokrmdetail ID',
            'kirimdokrm_id' => 'Kirimdokrm ID',
            'pesandokrmdetail_id' => 'Pesandokrmdetail ID',
            'dokrekammedis_id' => 'Dokrekammedis ID',
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
