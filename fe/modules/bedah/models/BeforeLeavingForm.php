<?php

namespace app\modules\bedah\models;

class BeforeLeavingForm extends \yii\base\Model
{

	public $anestesikondisipasien_id;
	public $pasienmasukpenunjang_id;
	public $observation_taken;
	public $consciousness;
	public $respiration;
	public $tv;
	public $hemodinamic_bp;
	public $hemodinamic_hr;
	public $spontaneous;
	public $fio;
	public $spo2;
	public $skin_color;
	public $skin_temperature;
	public $drugsupports = [];

	public function rules()
	{
        return [
            [['anestesikondisipasien_id', 'pasienmasukpenunjang_id', 'observation_taken', 'consciousness', 'respiration', 'tv', 'hemodinamic_bp', 'hemodinamic_hr', 'spontaneous', 'fio', 'spo2', 'skin_color', 'skin_temperature', 'drugsupports'], 'safe'],
        ];
	}

    public function attributeLabels()
    {
        return [
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
			'drugsupports' => \Yii::t('fe', 'Drug Support'),
        ];
    }
}