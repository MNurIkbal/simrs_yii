<?php

namespace app\modules\v1\models;

class PostOperativeAnestesi extends \Doco\components\DocoActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'anestesipostopr_t';
    }

	public function rules()
	{
        return [
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['anestesipostopr_id', 'arrival_time', 'consciousness', 'respiration', 'tv', 'bp', 'hr', 'spontaneous', 'fio', 'spo2', 'skin_color', 'skin_temperature', 'arousal_time', 'doctor_ins', 'pasienmasukpenunjang_id'], 'safe'],
            [['pasienmasukpenunjang_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
        ];
	}

    public function attributeLabels()
    {
        return [
			'arrival_time' => \Yii::t('bedah', 'Time of Patient Arrival'),
			'consciousness' => \Yii::t('bedah', 'Consciousness'),
			'respiration' => \Yii::t('bedah', 'Respiration'),
			'tv' => \Yii::t('bedah', 'TV'),
			'bp' => \Yii::t('bedah', 'BP'),
			'hr' => \Yii::t('bedah', 'HR'),
			'spontaneous' => \Yii::t('bedah', 'Spontaneous'),
			'fio' => \Yii::t('bedah', 'Fio'),
			'spo2' => \Yii::t('bedah', 'SpO2'),
			'skin_color' => \Yii::t('bedah', 'Skin Color'),
			'skin_temperature' => \Yii::t('bedah', 'Skin Temperature'),
			'arousal_time' => \Yii::t('bedah', 'Time of Arousal'),
			'doctor_ins' => \Yii::t('bedah', 'Doctor Instruction'),
        ];
    }
}