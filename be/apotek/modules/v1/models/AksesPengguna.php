<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "aksespengguna_k".
 *
 * @property integer $aksespengguna_id
 * @property integer $peranpengguna_id
 * @property integer $modul_id
 * @property integer $loginpemakai_id
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
 * @property LoginpemakaiK $loginpemakai
 * @property ModulK $modul
 * @property PeranpenggunaK $peranpengguna
 */
class AksesPengguna extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'aksespengguna_k';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['aksespengguna_id', 'peranpengguna_id', 'modul_id', 'loginpemakai_id'], 'required'],
            [['aksespengguna_id', 'peranpengguna_id', 'modul_id', 'loginpemakai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['loginpemakai_id'], 'exist', 'skipOnError' => true, 'targetClass' => LoginpemakaiK::className(), 'targetAttribute' => ['loginpemakai_id' => 'loginpemakai_id']],
            [['modul_id'], 'exist', 'skipOnError' => true, 'targetClass' => ModulK::className(), 'targetAttribute' => ['modul_id' => 'modul_id']],
            [['peranpengguna_id'], 'exist', 'skipOnError' => true, 'targetClass' => PeranpenggunaK::className(), 'targetAttribute' => ['peranpengguna_id' => 'peranpengguna_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'aksespengguna_id' => 'Aksespengguna ID',
            'peranpengguna_id' => 'Peranpengguna ID',
            'modul_id' => 'Modul ID',
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
        return $this->hasOne(LoginpemakaiK::className(), ['loginpemakai_id' => 'loginpemakai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModul()
    {
        return $this->hasOne(ModulK::className(), ['modul_id' => 'modul_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPeranpengguna()
    {
        return $this->hasOne(PeranpenggunaK::className(), ['peranpengguna_id' => 'peranpengguna_id']);
    }
}
