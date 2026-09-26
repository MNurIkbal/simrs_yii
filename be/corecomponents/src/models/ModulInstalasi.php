<?php

namespace Doco\models;

use Yii;

class ModulInstalasi extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'modulinstalasi_mp';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['modul_id', 'instalasi_id'], 'required'],
            [['additional_data','created_date','created_by',
            'created_date', 'last_modified_date', 'deleted_date',
            'modified_count', 'modul_id', 'last_modified_by',
            'is_deleted','is_active','deleted_by'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'modul_id' => 'Modul',
            'instalasi_id' => 'Instalasi',
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
    public function getInstalasi()
    {
        return $this->hasOne(\Doco\models\Instalasi::className(), ['instalasi_id' => 'instalasi_id']);
    }
}
