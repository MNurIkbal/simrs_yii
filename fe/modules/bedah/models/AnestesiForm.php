<?php

namespace app\modules\bedah\models;

class AnestesiForm extends \yii\base\Model
{
    public $anestesi_id;
    public $pasienmasukpenunjang_id;
    public $anestesi_result;
    public $anestesi_regional;
    public $anestesi_regional_other;
    public $type_needle_size;
    public $lenght_catheter_isertion;
    public $anestesi_general;
    public $patient_position;
    public $preinduction;
    public $induction;
    public $maintenance;
    public $recovery;
    public $additional_data;

	public function rules()
	{
        return [
            [[
                'anestesi_id',
                'pasienmasukpenunjang_id',
                'anestesi_result', 
                'anestesi_regional',
                'anestesi_regional_other',
                'type_needle_size',
                'lenght_catheter_isertion',
                'anestesi_general', 
                'patient_position', 
                'preinduction', 
                'induction', 
                'maintenance', 
                'recovery', 
                'additional_data', 
            ], 'safe'],
        ];
	}

    public function attributeLabels()
    {
        return [
            'pasienmasukpenunjang_id' => \Yii::t('fe', 'ID Pasien Penunjang'),
            'anestesi_result' => \Yii::t('fe', 'Result'),
            'anestesi_regional' => \Yii::t('fe', 'Regional'),
            'anestesi_regional_other' => \Yii::t('fe', 'Other Regional'),
            'type_needle_size' => \Yii::t('fe', 'Type of Needle/Size'),
			'lenght_catheter_isertion' => \Yii::t('fe', 'Length of Catheter Isertion'),
            'anestesi_general' => \Yii::t('fe', 'General'),
            'patient_position' => \Yii::t('fe', 'Patient Position'),
            'preinduction' => \Yii::t('fe', 'Preinduction'),
            'induction' => \Yii::t('fe', 'Induction'),
            'maintenance' => \Yii::t('fe', 'Maintenance'),
            'recovery' => \Yii::t('fe', 'Recovery'),
        ];
    }
}