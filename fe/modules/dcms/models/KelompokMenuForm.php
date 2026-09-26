<?php

namespace Doco\dcms\models;

use Yii;

class KelompokMenuForm extends \yii\base\Model
{

    public $kelmenu_id;
    public $kelmenu_nama;
    public $kelmenu_key;
    public $kelmenu_url;
    public $kelmenu_icon;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kelmenu_nama'], 'required'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['kelmenu_id','created_date', 'last_modified_date', 'deleted_date'], 'safe'],
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
            'kelmenu_id' => Yii::t('fe','Kelmenu ID'),
            'kelmenu_nama' => Yii::t('fe','Nama'),
            'kelmenu_key' => Yii::t('fe','Kelmenu Key'),
            'kelmenu_url' => Yii::t('fe','Kelmenu Url'),
            'kelmenu_icon' => Yii::t('fe','Kelmenu Icon'),
            'additional_data' => Yii::t('fe','Additional Data'),
            'created_date' => Yii::t('fe','Created Date'),
            'created_by' => Yii::t('fe','Created By'),
            'modified_count' => Yii::t('fe','Modified Count'),
            'last_modified_date' => Yii::t('fe','Last Modified Date'),
            'last_modified_by' => Yii::t('fe','Last Modified By'),
            'is_deleted' => Yii::t('fe','Is Deleted'),
            'is_active' => Yii::t('fe','Is Active'),
            'deleted_date' => Yii::t('fe','Deleted Date'),
            'deleted_by' => Yii::t('fe','Deleted By'),
        ];
    }
}