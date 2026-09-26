<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "shift_m".
 *
 * @property int $shift_id
 * @property string $shift_nama
 * @property string $shift_namalainnya
 * @property string $shift_jamawal
 * @property string $shift_jamakhir
 * @property string $shift_kode
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
class Shift extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shift_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shift_nama', 'shift_jamawal', 'shift_jamakhir'], 'required'],
            [['shift_jamawal', 'shift_jamakhir', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['additional_data'], 'string'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['shift_nama', 'shift_namalainnya'], 'string', 'max' => 50],
            [['shift_kode'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'shift_id' => Yii::t('app', 'Shift'),
            'shift_nama' => Yii::t('app', 'Nama shift'),
            'shift_namalainnya' => Yii::t('app', 'Nama lainnya'),
            'shift_jamawal' => Yii::t('app', 'Jam awal'),
            'shift_jamakhir' => Yii::t('app', 'Jam akhir'),
            'shift_kode' => Yii::t('app', 'Kode shift'),
            'additional_data' => Yii::t('app', 'Additional Data'),
            'created_date' => Yii::t('app', 'Created Date'),
            'created_by' => Yii::t('app', 'Created By'),
            'modified_count' => Yii::t('app', 'Modified Count'),
            'last_modified_date' => Yii::t('app', 'Last Modified Date'),
            'last_modified_by' => Yii::t('app', 'Last Modified By'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'is_active' => Yii::t('app', 'Is Active'),
            'deleted_date' => Yii::t('app', 'Deleted Date'),
            'deleted_by' => Yii::t('app', 'Deleted By'),
        ];
    }

    public static function getCurrentShiftId()
    {
        $currentHour = date("H:i:s");
        $row = self::find()
            ->andWhere("'{$currentHour}' >= shift_jamawal")
            ->andWhere("'{$currentHour}' <= shift_jamakhir")
            ->one();
        return $row? $row->shift_id : null;
    }
}
