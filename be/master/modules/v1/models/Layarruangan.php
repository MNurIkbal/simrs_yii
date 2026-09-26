<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "layarruangan_m".
 *
 * @property integer $ruangan_id
 * @property integer $layarantrian_id
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
 *
 * @property LayarantrianM $layarantrian
 * @property RuanganM $ruangan
 */
class Layarruangan extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'layarruangan_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ruangan_id', 'layarantrian_id'], 'required'],
            [['ruangan_id', 'layarantrian_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['layarantrian_id'], 'exist', 'skipOnError' => true, 'targetClass' => LayarantrianM::className(), 'targetAttribute' => ['layarantrian_id' => 'layarantrian_id']],
            [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => RuanganM::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ruangan_id' => 'Ruangan ID',
            'layarantrian_id' => 'Layarantrian ID',
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
    public function getLayarantrian()
    {
        return $this->hasOne(LayarantrianM::className(), ['layarantrian_id' => 'layarantrian_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuangan()
    {
        return $this->hasOne(RuanganM::className(), ['ruangan_id' => 'ruangan_id']);
    }
}
