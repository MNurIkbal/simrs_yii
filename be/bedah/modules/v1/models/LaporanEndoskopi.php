<?php

namespace app\modules\v1\models;

use Yii;

class LaporanEndoskopi extends \Doco\components\DocoActiveRecord
{
    protected $xssProtected = [
        'simptoms',
        'indications_examinations',
        'instrument',
        'pre_medications',
        'findings',
        'sampling',
        'recommendations',
     ];

    /**
    * @inheritdoc
    */
    public static function tableName()
    {
        return 'laporanendoskopi_r';
    }

    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasienmasukpenunjang_id'], 'required'],
            [['simptoms', 'pre_diagnosis', 'pre_diagnosis_sekunder', 'indications_examinations', 'pre_medications', 'instrument', 'procedure_performed', 'findings', 'sampling', 'endoscopic_diagnosis', 'endoscopic_diagnosis_sekunder', 'recommendations', 'additional_data', 'additional_photo'], 'safe']
        ];
    }

    public function attributeLabels(){
        return [
            'simptoms' => \Yii::t('fe', 'Simptoms'),
            'pre_diagnosis' => \Yii::t('fe', 'Pre-Endoscopic Diagnosis'),
            'pre_diagnosis_sekunder' => \Yii::t('fe', 'Pre-Endoscopic Diagnosis Sekunder'),
            'indications_examinations ' => \Yii::t('fe', 'Indications Examinations '),
            'instrument ' => \Yii::t('fe', 'Instrument'),
            'pre_medications' => \Yii::t('fe', 'Pre Medications'),
            'procedure_performed' => \Yii::t('fe', 'Procedure Performed'),
            'findings' => \Yii::t('fe', 'Findings'),
            'sampling' => \Yii::t('fe', 'Sampling'),
            'endoscopic_diagnosis' => \Yii::t('fe', 'Endoscopic Diagnosis'),
            'endoscopic_diagnosis_sekunder' => \Yii::t('fe', 'Endoscopic Diagnosis Sekunder'),
            'recommendations' => \Yii::t('fe', 'Recommendations'),
        ];
    }
}
