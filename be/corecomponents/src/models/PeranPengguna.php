<?php

namespace Doco\models;

use Yii;


class PeranPengguna extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'peranpengguna_k';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['peranpenggunanama', 'peranpenggunanamalain','modul_id'], 'required'],
            [['created_date', 'last_modified_date', 'deleted_date','peranpengguna_id','peranpengguna_aktif',
            'additional_data','created_by','modified_count','last_modified_by','is_deleted','is_active',
            'deleted_by','peranpengguna_menu','peranpengguna_akses','modul_id', 'is_exception'], 'safe']
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModul()
    {
        return $this->hasOne(\Doco\models\Modul::className(), ['modul_id' => 'modul_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModulInstalasi()
    {
        return $this->hasMany(\Doco\models\ModulInstalasi::className(), ['modul_id' => 'modul_id']);
    }
}