<?php

namespace app\modules\bedah\models;

class PostOperativeAnestesiForm extends \yii\base\Model
{

	public $anestesipostopr_id;
	public $pasienmasukpenunjang_id;
	public $arrival_time;
	public $consciousness;
	public $respiration;
	public $tv;
	public $bp;
	public $hr;
	public $spontaneous;
	public $fio;
	public $spo2;
	public $skin_color;
	public $skin_temperature;
	public $arousal_time;
	public $doctor_ins;
	public $aldretescores = [];
	public $drugsupports = [];

	public function rules()
	{
        return [
            [['anestesipostopr_id', 'pasienmasukpenunjang_id', 'arrival_time', 'consciousness', 'respiration', 'tv', 'bp', 'hr', 'spontaneous', 'fio', 'spo2', 'skin_color', 'skin_temperature', 'arousal_time', 'doctor_ins', 'aldretescores', 'drugsupports'], 'safe'],
        ];
	}

    public function attributeLabels()
    {
        return [
			'arrival_time' => \Yii::t('fe', 'Time of Patient Arrival'),
			'consciousness' => \Yii::t('fe', 'Consciousness'),
			'respiration' => \Yii::t('fe', 'Respiration'),
			'tv' => \Yii::t('fe', 'TV'),
			'bp' => \Yii::t('fe', 'BP'),
			'hr' => \Yii::t('fe', 'HR'),
			'spontaneous' => \Yii::t('fe', 'Spontaneous'),
			'fio' => \Yii::t('fe', 'Fio'),
			'spo2' => \Yii::t('fe', 'SpO2'),
			'skin_color' => \Yii::t('fe', 'Skin Color'),
			'skin_temperature' => \Yii::t('fe', 'Skin Temperature'),
			'arousal_time' => \Yii::t('fe', 'Time of Arousal'),
			'doctor_ins' => \Yii::t('fe', 'Doctor Instruction'),
			'aldretescores' => \Yii::t('fe', 'Aldrete Score'),
			'drugsupports' => \Yii::t('fe', 'Drug Support'),
        ];
    }
}