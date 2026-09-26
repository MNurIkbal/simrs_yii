<?php

namespace app\modules\v1\models;

use Yii;

class BeforeLeaving extends \Doco\components\DocoActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'anestesikondisipasien_t';
    }

	public function rules()
	{
        return [
            [['anestesikondisipasien_id', 'pasienmasukpenunjang_id', 'pendaftaran_id', 'observation_taken', 'consciousness', 'respiration', 'tv','hemodinamic_bp', 'hemodinamic_hr', 'spontaneous', 'fio', 'spo2', 'skin_color', 'skin_temperature'], 'safe'],
        ];
	}

    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => "Pendaftaran ID",
            'observation_taken' => "Observation Taken",
			'arrival_time' => \Yii::t('fe', 'Observation Taken at'),
			'consciousness' => \Yii::t('fe', 'Consciousness'),
			'respiration' => \Yii::t('fe', 'Respiration'),
            'tv' => \Yii::t('fe', 'TV'),
			'hemodinamic_bp' => \Yii::t('fe', 'BP'),
			'hemodinamic_hr' => \Yii::t('fe', 'HR'),
			'spontaneous' => \Yii::t('fe', 'Spontaneous'),
			'fio' => \Yii::t('fe', 'Fio'),
			'spo2' => \Yii::t('fe', 'SpO2'),
			'skin_color' => \Yii::t('fe', 'Skin Color'),
			'skin_temperature' => \Yii::t('fe', 'Skin Temperature'),
        ];
    }
}