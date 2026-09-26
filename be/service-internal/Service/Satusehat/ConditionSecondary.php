<?php

namespace Integrasi\Service\Satusehat;

use Yii;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\Services\SatusehatService;
use Integrasi\Service\Satusehat\Models\Satusehat;
use Integrasi\Service\Sirs\Cache\Cache;

class ConditionSecondary extends \Integrasi\Service\Satusehat\Condition
{
    const RESOURCE_TYPE = 'Condition';
    protected $logType = 'ConditionSecondary';
    public $pendaftaranId;
    public $organizationId;
    public $dataAttr;

    public function execute()
    {
        $this->setAttrSatusehat();
        $state = $this->state;
        $this->dataAttr = $this->attributes['result']['data'];
        $dataAttr = $this->dataAttr;
        $pendaftaranId = $this->dataAttr['result']['pendaftaran_id'];
        $this->konsulpoliId = (int)$this->dataAttr['konsulpoli_id'];
        if($pendaftaranId) {
            $this->masterPayloadUpdate($pendaftaranId);

            $payload = $this->buildSecondary();
            $res = (new SatusehatService)->createCondition($payload, true);
            $this->setLogs($pendaftaranId, $state, $payload, $res);
        }
        
        return json_encode([
            'service' => 'Satusehat-ConditionSecondary',
            'state' => $state,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    protected function buildSecondary() {
    	$dataBuild = $this->dataAttr;
    	$payload = NULL;

    	if(!empty($dataBuild['a_diag_penyerta'])){
	    	$payload = $this->build();
	    	unset($payload['code']['coding']);

	    	$diagnosaPenyerta = $dataBuild['a_diag_penyerta'];


	    	for ($i = 0; $i < count($diagnosaPenyerta); $i++) { 
    	    	$codeCoding = !empty($diagnosaPenyerta[$i]['kode']) ? $diagnosaPenyerta[$i]['kode'] : '';
    	    	$displayCoding = !empty($diagnosaPenyerta[$i]['nama']) ? $diagnosaPenyerta[$i]['nama'] : '';

		    	$payload['code']['coding'][] = [
		    		"system" => "http://hl7.org/fhir/sid/icd-10",
		    		"code" => trim($codeCoding),
		    		"display" => $displayCoding,
		    	]; 
	    	}
    	};

    	return $payload;
    }
}