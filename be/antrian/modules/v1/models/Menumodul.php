<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "menumodul_k".
 *
 * @property integer $menu_id
 * @property integer $kelmenu_id
 * @property integer $modul_id
 * @property string $menu_nama
 * @property string $menu_namalainnya
 * @property string $menu_key
 * @property string $menu_url
 * @property string $menu_fungsi
 * @property integer $menu_urutan
 * @property string $menu_icon
 * @property boolean $menu_shortcut
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
 * @property KelompokmenuK $kelmenu
 * @property ModulK $modul
 * @property TugaspenggunaK[] $tugaspenggunaKs
 * @property TugaspenggunaK[] $tugaspenggunaKs0
 */
class Menumodul extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'menumodul_k';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['menu_id', 'menu_nama', 'menu_key'], 'required'],
            [['menu_id', 'kelmenu_id', 'modul_id', 'menu_urutan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['menu_shortcut', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['menu_nama', 'menu_namalainnya', 'menu_key', 'menu_url', 'menu_fungsi'], 'string', 'max' => 100],
            [['menu_icon'], 'string', 'max' => 500],
            [['kelmenu_id'], 'exist', 'skipOnError' => true, 'targetClass' => Kelompokmenu::className(), 'targetAttribute' => ['kelmenu_id' => 'kelmenu_id']],
            [['modul_id'], 'exist', 'skipOnError' => true, 'targetClass' => Modul::className(), 'targetAttribute' => ['modul_id' => 'modul_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'menu_id' => 'Menu ID',
            'kelmenu_id' => 'Kelmenu ID',
            'modul_id' => 'Modul ID',
            'menu_nama' => 'Menu Nama',
            'menu_namalainnya' => 'Menu Namalainnya',
            'menu_key' => 'Menu Key',
            'menu_url' => 'Menu Url',
            'menu_fungsi' => 'Menu Fungsi',
            'menu_urutan' => 'Menu Urutan',
            'menu_icon' => 'Menu Icon',
            'menu_shortcut' => 'Menu Shortcut',
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
    public function getKelmenu()
    {
        return $this->hasOne(KelompokmenuK::className(), ['kelmenu_id' => 'kelmenu_id']);
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
    public function getTugaspenggunaKs()
    {
        return $this->hasMany(TugaspenggunaK::className(), ['menu_id' => 'menu_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTugaspenggunaKs0()
    {
        return $this->hasMany(TugaspenggunaK::className(), ['menu_id' => 'menu_id']);
    }

    public function extraFields()
    {
        return ['kelmenu'];
    }
}
