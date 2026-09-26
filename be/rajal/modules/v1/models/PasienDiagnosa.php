<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2019-01-25 15:13:25
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-01-25 15:13:34
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pasiendiagnosa_t".
 *
 * @property int $pasiendiagnosa_id
 * @property int $pasien_id
 * @property string $diagnosa_pasien
 * @property int $ruangan_id
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
class PasienDiagnosa extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pasiendiagnosa_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pasien_id', 'ruangan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pasien_id', 'ruangan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['diagnosa_pasien', 'additional_data'], 'string'],
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
            'pasiendiagnosa_id' => 'Pasiendiagnosa ID',
            'pasien_id' => 'Pasien ID',
            'diagnosa_pasien' => 'Diagnosa Pasien',
            'ruangan_id' => 'Ruangan ID',
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