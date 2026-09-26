<?php

namespace Doco\models;

use Yii;

class Sbar extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sbar_t';
    }

    public function rules()
    {
        return [
            [['dokter_tujuan_id', 'tgl_sbar', 'situasi', 'asesmen', 'rekomendasi', 'pendaftaran_id'], 'required'],
            [['dokter_tujuan_id', 'created_by', 'last_modified_by', 'modified_count', 'deleted_by', 'pasienadmisi_id', 'pendaftaran_id'], 'integer'],
            [['sistol', 'diastol', 'nadi', 'respirasi', 'spo2', 'suhu', 'tinggi_badan', 'berat_badan', 'lingkar_kepala'], 'number'],
            [['is_ttv', 'is_verifikasi', 'is_active', 'is_deleted'], 'boolean'],
            [['created_date', 'last_modified_date', 'deleted_date', 'tgl_sbar'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'sbar_id' => Yii::t('app', 'SBAR ID'),
            'dokter_tujuan_id' => Yii::t('app', 'Dokter Tujuan'),
            'tgl_sbar' => Yii::t('app', 'Tanggal & Waktu'),
            'situasi' => Yii::t('app', 'Situation'),
            'asesmen' => Yii::t('app', 'Assesment'),
            'rekomendasi' => Yii::t('app', 'Recommendation'),
            'is_active' => Yii::t('app', 'Is Active'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'created_date' => Yii::t('app', 'Created Date'),
            'created_by' => Yii::t('app', 'Created By'),
            'last_modified_date' => Yii::t('app', 'Last Modified Date'),
            'last_modified_by' => Yii::t('app', 'Last Modified By'),
            'modified_count' => Yii::t('app', 'Modified Count'),
            'deleted_date' => Yii::t('app', 'Deleted Date'),
            'deleted_by' => Yii::t('app', 'Deleted By'),
        ];
    }
}
