<?php

namespace Doco\models;

use Yii;

class KelompokMenuGroup extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kelompokmenugroup_k';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kelompokmenu_id','data','kelompokmenu_id'], 'safe'],
        ];
    }

    public static function primaryKey()
    {
        return ['kelompokmenu_id'];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKelompokMenu()
    {
        return $this->hasOne(\Doco\models\KelompokMenu::className(), ['kelmenu_id' => 'kelompokmenu_id']);
    }
}