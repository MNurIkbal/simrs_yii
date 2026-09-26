<?php

namespace Doco\models;

use Yii;

/**
 * This is the model class for table "ruanganpemakai_k".
 *
 * @property int $ruangan_id
 * @property int $loginpemakai_id
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
 * @property LoginpemakaiK $loginpemakai
 * @property RuanganM $ruangan
 */
class RuanganPemakai extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ruanganpemakai_k';
    }

    public static function primaryKey()
    {
       return array('ruangan_id', 'loginpemakai_id');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ruangan_id', 'loginpemakai_id'], 'required'],
            [['ruangan_id', 'loginpemakai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['ruangan_id', 'loginpemakai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
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
            'ruangan_id' => 'Ruangan ID',
            'loginpemakai_id' => 'Loginpemakai ID',
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
    public function getLoginpemakai()
    {
        return $this->hasOne(\Doco\models\Loginpemakai::className(), ['loginpemakai_id' => 'loginpemakai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuangan()
    {
        return $this->hasOne(\Doco\models\Ruangan::className(), ['ruangan_id' => 'ruangan_id']);
    }
}