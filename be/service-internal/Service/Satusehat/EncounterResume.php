<?php 

namespace Integrasi\Service\Satusehat;

use Yii;
use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Satusehat\Models\Satusehat;
use Integrasi\Components\Services\SatusehatService;

class EncounterResume extends \Integrasi\Service\Satusehat\Encounter {

    public function execute()
    {
        $state = $this->state;
        $this->logType = 'Encounter-Outpatient-MedicalResume';
        $pendaftaranId = ArrayHelper::getValue($this->result, 'data.id', null); 
        $medicalResumeId = ArrayHelper::getValue($this->result, 'data.resumemedisri_id', null); 

        if($pendaftaranId && $medicalResumeId) {
            $payload = $this->build();
            $res = (new SatusehatService)->createEncounterOutpatientResume($payload, false);
            $this->setLogs($pendaftaranId, $state, $payload, $res, $medicalResumeId);
        }

        return json_encode([
            'service' => 'Satusehat-EncounterResume',
            'state' => $state,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

}