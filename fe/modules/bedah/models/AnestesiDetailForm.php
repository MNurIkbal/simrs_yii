<?php

namespace app\modules\bedah\models;

class AnestesiDetailForm extends \yii\base\Model
{
    public $anestesidetail_id;
    public $anestesi_id;
    public $obatalkes_id;
    public $dose;
    public $time_delivery;
    public $additional_data;

	public function rules()
	{
        return [
            [[
                'anestesidetail_id',
                'anestesi_id',
                'obatalkes_id',
                'dose', 
                'time_delivery',
                'additional_data', 
            ], 'safe'],
        ];
	}

    public function attributeLabels()
    {
        return [
            'anestesidetail_id' => \Yii::t('fe', 'ID Anestesi Detail'),
            'anestesi_id' => \Yii::t('fe', 'ID Anestesi'),
            'obatalkes_id' => \Yii::t('fe', 'Drug'),
            'dose' => \Yii::t('fe', 'Dose'),
            'time_delivery' => \Yii::t('fe', 'Time Delivery'),
        ];
    }
}