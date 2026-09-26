<?php

namespace Doco\bedah\models;
use Yii;

class LaporanEndoskopiForm extends \yii\base\Model
{

    public $pendaftaran_id;
    public $pasienmasukpenunjang_id;
    public $simptoms;
    public $pre_diagnosis;
    public $pre_diagnosis_sekunder;
    public $indications_examinations;
    public $instrument;
    public $pre_medications;
    public $procedure_performed;
    public $findings;
    public $sampling;
    public $endoscopic_diagnosis;
    public $endoscopic_diagnosis_sekunder;
    public $recommendations;
    public $additional_data;
    public $additional_photo;

    public function rules()
    {
        return [
            // [['pendaftaran_id', 'pasienmasukpenunjang_id'], 'required'],
            [['simptoms', 'pre_diagnosis', 'pre_diagnosis_sekunder', 'indications_examinations', 'instrument', 'pre_medications', 'procedure_performed', 'findings', 'sampling', 'endoscopic_diagnosis', 'endoscopic_diagnosis_sekunder', 'recommendations', 'additional_data'], 'safe'],
            [['additional_photo'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, PNG, JPG, JPEG'],
        ];
    }

 

    public function attributeLabels(){
        return [
            'simptoms' => \Yii::t('fe', 'Simptoms'),
            'pre_diagnosis' => \Yii::t('fe', 'Pre-Endoscopic Diagnosis'),
            'pre_diagnosis_sekunder' => \Yii::t('fe', 'Pre-Endoscopic Diagnosis Sekunder'),
            'indications_examinations ' => \Yii::t('fe', 'Indications For Examinations'),
            'instrument ' => \Yii::t('fe', 'Instrument(s) Used'),
            'pre_medications' => \Yii::t('fe', 'Pre-Endoscopic Medications'),
            'procedure_performed' => \Yii::t('fe', 'Procedure Performed'),
            'findings' => \Yii::t('fe', 'Findings'),
            'sampling' => \Yii::t('fe', 'Sampling'),
            'endoscopic_diagnosis' => \Yii::t('fe', 'Endoscopic Diagnosis'),
            'endoscopic_diagnosis_sekunder' => \Yii::t('fe', 'Endoscopic Diagnosis Sekunder'),
            'recommendations' => \Yii::t('fe', 'Recommendations'),
            'additional_photo' => \Yii::t('fe', 'Import Photo'),
        ];
    }

    
}