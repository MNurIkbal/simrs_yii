<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "modul_k".
 *
 * @property integer $modul_id
 * @property string $modul_nama
 * @property string $modul_namalainnya
 * @property string $modul_fungsi
 * @property string $url_modul
 * @property string $icon_modul
 * @property string $modul_key
 * @property integer $modul_urutan
 * @property string $modul_kategori
 * @property string $imagemodul
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
 * @property AksespenggunaK[] $aksespenggunaKs
 * @property MenumodulK[] $menumodulKs
 * @property SmsgatewayM[] $smsgatewayMs
 */
class Modul extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'modul_k';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['modul_nama', 'modul_key'], 'required'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['modul_fungsi', 'imagemodul', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date','modul_id'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['modul_nama', 'modul_namalainnya', 'url_modul', 'modul_key'], 'string', 'max' => 50],
            [['icon_modul', 'modul_kategori'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'modul_id' => 'Modul ID',
            'modul_nama' => 'Modul Nama',
            'modul_namalainnya' => 'Modul Namalainnya',
            'modul_fungsi' => 'Modul Fungsi',
            'url_modul' => 'Url Modul',
            'icon_modul' => 'Icon Modul',
            'modul_key' => 'Modul Key',
            'modul_urutan' => 'Modul Urutan',
            'modul_kategori' => 'Modul Kategori',
            'imagemodul' => 'Imagemodul',
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
    public function getAksespenggunaKs()
    {
        return $this->hasMany(AksespenggunaK::className(), ['modul_id' => 'modul_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenumodulKs()
    {
        return $this->hasMany(MenumodulK::className(), ['modul_id' => 'modul_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSmsgatewayMs()
    {
        return $this->hasMany(SmsgatewayM::className(), ['modul_id' => 'modul_id']);
    }
}
