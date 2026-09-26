<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rincianclosing_t".
 *
 * @property int $rincianclosing_id
 * @property int $closingkasir_id
 * @property int $nourutrincian
 * @property double $nilaiuang
 * @property int $banyakuang
 * @property double $jumlahuang
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
class RincianClosing extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rincianclosing_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['closingkasir_id', 'nilaiuang', 'banyakuang', 'jumlahuang'], 'required'],
            [['closingkasir_id', 'nourutrincian', 'banyakuang', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['closingkasir_id', 'nourutrincian', 'banyakuang', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['nilaiuang', 'jumlahuang'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rincianclosing_id' => Yii::t('app', 'Rincianclosing ID'),
            'closingkasir_id' => Yii::t('app', 'Closingkasir ID'),
            'nourutrincian' => Yii::t('app', 'Nourutrincian'),
            'nilaiuang' => Yii::t('app', 'Nilaiuang'),
            'banyakuang' => Yii::t('app', 'Banyakuang'),
            'jumlahuang' => Yii::t('app', 'Jumlahuang'),
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
}
