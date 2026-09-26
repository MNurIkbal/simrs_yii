<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "hasilpemeriksaanraddetail_t".
 *
 * @property int $hasilpemeriksaanraddetail_id
 * @property int $hasilpemeriksaanrad_id
 * @property int $petugasrad_id
 * @property string $upload_file
 * @property string $kondisi_file
 * @property string $catatan
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
class HasilPemeriksaanRadDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hasilpemeriksaanraddetail_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hasilpemeriksaanrad_id', 'petugasrad_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['hasilpemeriksaanrad_id', 'petugasrad_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['upload_file', 'catatan', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kondisi_file'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'hasilpemeriksaanraddetail_id' => 'Hasilpemeriksaanraddetail ID',
            'hasilpemeriksaanrad_id' => 'Hasilpemeriksaanrad ID',
            'petugasrad_id' => 'Petugasrad ID',
            'upload_file' => 'Upload File',
            'kondisi_file' => 'Kondisi File',
            'catatan' => 'Catatan',
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
