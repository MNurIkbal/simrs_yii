<?php

namespace Integrasi\Service\Roche\Object;

class PatientObject extends \yii\base\Model
{
    public $patient_id;

    public $patient_first_name;

    public $patient_last_name;

    public $mother_maiden_name;

    public $date_of_birth;

    public $gender;

    public $pasien_id;

    public function rules()
    {
        return [
            [['patient_id', 'pasien_id'],'number'],
            [[
                'patient_first_name',
                'patient_last_name',
                'mother_maiden_name',
                'date_of_birth',
                'gender',
            ], 'safe']
        ];
    }

    public function setPatientName($value)
    {
        $fullName = explode(" ",$value, 2);
        $this->patient_first_name = isset($fullName[0]) ? $fullName[0] : null;
        $this->patient_last_name = isset($fullName[1]) ? $fullName[1] : null;
    }

}