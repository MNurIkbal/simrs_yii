<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "kelasruangan_mp".
 *
 * @property int $ruangan_id
 * @property int $kelaspelayanan_id
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
class KelasRuanganForm extends \yii\db\ActiveRecord
{
    public $list_ruangan_id;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kelasruangan_mp';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['list_ruangan_id', 'kelaspelayanan_id'], 'required'],
            [['ruangan_id', 'kelaspelayanan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['ruangan_id', 'kelaspelayanan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['ruangan_id', 'kelaspelayanan_id'], 'unique', 'targetAttribute' => ['ruangan_id', 'kelaspelayanan_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ruangan_id' => Yii::t('fe','Ruangan ID'),
            'kelaspelayanan_id' => Yii::t('fe','Kelaspelayanan ID'),
            'additional_data' => Yii::t('fe','Additional Data'),
            'created_date' => Yii::t('fe','Created Date'),
            'created_by' => Yii::t('fe','Created By'),
            'modified_count' => Yii::t('fe','Modified Count'),
            'last_modified_date' => Yii::t('fe','Last Modified Date'),
            'last_modified_by' => Yii::t('fe','Last Modified By'),
            'is_deleted' => Yii::t('fe','Is Deleted'),
            'is_active' => Yii::t('fe','Is Active'),
            'deleted_date' => Yii::t('fe','Deleted Date'),
            'deleted_by' => Yii::t('fe','Deleted By'),
        ];
    }
}
