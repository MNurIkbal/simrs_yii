<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "spesialis_m".
 *
 * @property integer $spesialis_id
 * @property string $spesialis_kode
 * @property string $spesialis_nama
 * @property string $additional_data
 * @property string $created_date
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 * @property string $spesialis_namalainnya
 */
class Spesialis extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'spesialis_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_date', 'last_modified_date', 'deleted_date'], 'default', 'value' => null],
            //[['spesialis_id'], 'integer'],
            [['spesialis_nama'], 'required'],
            [['spesialis_kode', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date', 'spesialis_namalainnya'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'spesialis_id' => Yii::t('app', 'Spesialis'),
            'spesialis_kode' => Yii::t('app', 'Kode spesialis'),
            'spesialis_nama' => Yii::t('app', 'Nama spesialis'),
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
            'spesialis_namalainnya' => Yii::t('app', 'Nama Spesialis Lainnya'),
        ];
    }
}
