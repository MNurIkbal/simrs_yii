<?php

namespace Doco\models;

use Yii;

class Ews extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ews_t';
    }

    public function rules()
    {
        return [
            [['tanggal_ews', 'jenis_ews', 'pegawai_id', 'pendaftaran_id'], 'required'],
            [['pegawai_id', 'pasienadmisi_id', 'created_by', 'last_modified_by', 'modified_count', 'deleted_by', 'pendaftaran_id'], 'integer'],
            [['is_active', 'is_deleted'], 'boolean'],
            [['created_date', 'last_modified_date', 'deleted_date', 'tanggal_ews', 'jenis_ews', 'pegawai_id', 'pendaftaran_id',
            'pasienadmisi_id', 'additional_data'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'ews_id' => Yii::t('app', 'Ews ID'),
            'is_active' => Yii::t('app', 'Is Active'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'created_date' => Yii::t('app', 'Created Date'),
            'created_by' => Yii::t('app', 'Created By'),
            'last_modified_date' => Yii::t('app', 'Last Modified Date'),
            'last_modified_by' => Yii::t('app', 'Last Modified By'),
            'modified_count' => Yii::t('app', 'Modified Count'),
            'deleted_date' => Yii::t('app', 'Deleted Date'),
            'deleted_by' => Yii::t('app', 'Deleted By'),
            'tanggal_ews' => Yii::t('app', 'Tanggal EWS'),
        ];
    }
}
