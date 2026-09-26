<?php

namespace app\modules\bedah\models;

class IntraOperativeAnestesiForm extends \yii\base\Model
{

	public $anestesiintraopr_id;
	public $pasienmasukpenunjang_id;
	public $start_induction;
	public $end_induction;
	public $start_surgery;
	public $end_surgery;
	public $patient_exit;
	public $length_anesthesia;
	public $length_surgery;

	public $vital_sign = [];
	public $vital_sign_individual;
	public $other_monitoring = [];
	public $other_monitoring_individual;

	public function rules()
	{
        return [
            [['anestesiintraopr_id', 'pasienmasukpenunjang_id', 'start_induction', 'end_induction', 'start_surgery', 'end_surgery', 'patient_exit', 'length_anesthesia', 'length_surgery', 'vital_sign', 'other_monitoring', 'vital_sign_individual', 'other_monitoring_individual'], 'safe'],
        ];
	}

    public function attributeLabels()
    {
        return [
			'start_induction' => \Yii::t('fe', 'Start in Induction'),
			'end_induction' => \Yii::t('fe', 'End in Induction'),
			'start_surgery' => \Yii::t('fe', 'Start of Surgery'),
			'end_surgery' => \Yii::t('fe', 'End of Surgery'),
			'patient_exit' => \Yii::t('fe', 'Patient Exit at'),
			'length_anesthesia' => \Yii::t('fe', 'Length of Anesthesia'),
			'length_surgery' => \Yii::t('fe', 'Length of Surgery'),
			'vital_sign' => \Yii::t('fe', 'Vital Sign'),
			'other_monitoring' => \Yii::t('fe', 'Other Monitoring'),
			'vital_sign_individual' => \Yii::t('fe', 'Vital Sign'),
			'other_monitoring_individual' => \Yii::t('fe', 'Other Monitoring'),
        ];
    }

    public function displayLengthOfInduction()
    {
    	return $this->length_anesthesia > 0 ? $this->formatCalculatedLengthDiff($this->length_anesthesia) : 0;
    }

    public function displayLengthOfSurgery()
    {
    	return $this->length_surgery > 0 ? $this->formatCalculatedLengthDiff($this->length_surgery) : 0;
    }

    private function formatCalculatedLengthDiff($length)
    {
    	if ($length < 60) {
    		return $length . ($length > 1 ? ' Minutes' : ' Minute');
    	}

    	$hour = floor($length / 60);
    	$minute = $length - ($hour * 60);

    	$result = $hour . ($hour > 1 ? ' Hours' : ' Hour');
    	if ($minute > 0) {
    		$result .= ' ' . $minute . ($minute > 1 ? ' Minutes' : 'Minute');
    	}

    	return $result;
    }
}