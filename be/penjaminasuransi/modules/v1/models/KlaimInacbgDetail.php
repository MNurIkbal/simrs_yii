<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "klaiminacbgdetail_t".
 *
 * @property int $klaiminacbgdetail_id
 * @property int $klaiminacbg_id
 * @property int $diagnosa_id
 * @property string $kode_diagnosa
 * @property string $nama_diagnosa
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
class KlaimInacbgDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'klaiminacbgdetail_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'klaiminacbg_id'], 'required'],
            [[ 'klaiminacbg_id', 'diagnosa_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [[ 'klaiminacbg_id', 'diagnosa_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date', 'icd_versi'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kode_diagnosa', 'nama_diagnosa'], 'string', 'max' => 255],
            [['klaiminacbgdetail_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'klaiminacbgdetail_id' => 'Klaiminacbgdetail ID',
            'klaiminacbg_id' => 'Klaiminacbg ID',
            'diagnosa_id' => 'Diagnosa ID',
            'kode_diagnosa' => 'Kode Diagnosa',
            'nama_diagnosa' => 'Nama Diagnosa',
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
