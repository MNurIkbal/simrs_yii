<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kelompokmenu_k".
 *
 * @property integer $kelmenu_id
 * @property string $kelmenu_nama
 * @property string $kelmenu_key
 * @property string $kelmenu_url
 * @property string $kelmenu_icon
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
 * @property MenumodulK[] $menumodulKs
 */
class KelompokMenu extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kelompokmenu_k';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kelmenu_nama'], 'required'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date','kelmenu_id'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kelmenu_nama'], 'string', 'max' => 100],
            [['kelmenu_key'], 'string', 'max' => 50],
            [['kelmenu_url'], 'string', 'max' => 200],
            [['kelmenu_icon'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kelmenu_id' => 'Kelmenu ID',
            'kelmenu_nama' => 'Kelmenu Nama',
            'kelmenu_key' => 'Kelmenu Key',
            'kelmenu_url' => 'Kelmenu Url',
            'kelmenu_icon' => 'Kelmenu Icon',
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
    public function getMenuModul()
    {
        return $this->hasMany(MenuModul::className(), ['kelmenu_id' => 'kelmenu_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(\Doco\models\KelompokMenuGroup::className(), ['kelompokmenu_id' => 'kelmenu_id']);
    }
}
