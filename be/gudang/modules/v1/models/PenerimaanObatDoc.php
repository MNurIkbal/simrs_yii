<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "penerimaanobatdoc_t".
 *
 * @property int $penerimaanobatdoc_id
 * @property int $penerimaanobat_id
 * @property string $upload_berkas
 * @property string $catatan_berkas
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
class PenerimaanObatDoc extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'penerimaanobatdoc_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['penerimaanobat_id'], 'required'],
            [['penerimaanobat_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['penerimaanobat_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['upload_berkas', 'catatan_berkas', 'additional_data'], 'string'],
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
            'penerimaanobatdoc_id' => 'Penerimaanobatdoc ID',
            'penerimaanobat_id' => 'Penerimaanobat ID',
            'upload_berkas' => 'Upload Berkas',
            'catatan_berkas' => 'Catatan Berkas',
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
