<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "sysdia_m".
 *
 * @property int $sysdia_id
 * @property int $golonganumur_id
 * @property double $systolic_min
 * @property double $systolic_max
 * @property double $diastolic_min
 * @property double $diastolic_max
 * @property string $sysdia_range
 * @property string $sysdia_nama
 * @property string $sysdia_deskirpsi
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
 *
 * @property GolonganumurM $golonganumur
 */
class Sysdia extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sysdia_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['golonganumur_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['golonganumur_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['systolic_min', 'systolic_max', 'diastolic_min', 'diastolic_max', 'sysdia_range', 'sysdia_nama'], 'required'],
            [['systolic_min', 'systolic_max', 'diastolic_min', 'diastolic_max'], 'number'],
            [['sysdia_deskirpsi', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['sysdia_range', 'sysdia_nama'], 'string', 'max' => 100],
            [['golonganumur_id'], 'exist', 'skipOnError' => true, 'targetClass' => GolonganumurM::className(), 'targetAttribute' => ['golonganumur_id' => 'golonganumur_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'sysdia_id' => 'Sysdia ID',
            'golonganumur_id' => 'Golonganumur ID',
            'systolic_min' => 'Systolic Min',
            'systolic_max' => 'Systolic Max',
            'diastolic_min' => 'Diastolic Min',
            'diastolic_max' => 'Diastolic Max',
            'sysdia_range' => 'Sysdia Range',
            'sysdia_nama' => 'Sysdia Nama',
            'sysdia_deskirpsi' => 'Sysdia Deskirpsi',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGolonganumur()
    {
        return $this->hasOne(GolonganumurM::className(), ['golonganumur_id' => 'golonganumur_id']);
    }
}
