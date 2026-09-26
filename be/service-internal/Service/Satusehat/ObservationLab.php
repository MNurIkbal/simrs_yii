<?php

namespace Integrasi\Service\Satusehat;

use yii\helpers\ArrayHelper;
use Integrasi\Components\Services\SatusehatService;
use Integrasi\Service\Satusehat\Components\SatuSehatPayload;
use Integrasi\Service\Satusehat\Models\Satusehat;

class ObservationLab extends \Integrasi\Service\Satusehat\Encounter
{
    public $dateTimeCreate;
    public $satusehatIDServiceRequest;
    public $satusehatIDSpeciment;

    public function execute()
    {
        $state = $this->state;
        $this->logType = 'ObservationLab';
        $this->setAttrSatusehat();
        $this->pendaftaranId = ArrayHelper::getValue($this->result, 'pendaftaran_id', null);

        $pasienkirimkeunitlainId = ArrayHelper::getValue($this->result, 'pasienkirimkeunitlain_id', null);

        $modelServiceRequest = (new Satusehat)->getServiceRequest()->andWhere([
            'pendaftaran_id' => $this->pendaftaranId, 
            'additional_id' => $pasienkirimkeunitlainId
        ])->asArray()->one();

        $modelSpecimen = (new Satusehat)->getSpecimen()->andWhere([
            'pendaftaran_id' => $this->pendaftaranId, 
            'additional_id' => $pasienkirimkeunitlainId
        ])->orderBy([
            'created_date' => SORT_DESC
        ])->asArray()->one();

        $this->dateTimeCreate = ArrayHelper::getValue($this->result, 'tanggal', null);
        $this->satusehatIDServiceRequest = ArrayHelper::getValue($modelServiceRequest, 'satusehat_id');
        $this->satusehatIDSpeciment = ArrayHelper::getValue($modelSpecimen, 'satusehat_id');

        if ($this->pendaftaranId) {
            $payload = $this->build();
            $res = (new SatusehatService)->createObservation($payload, false);
            $this->setLogs($this->pendaftaranId, $state, $payload, $res, $pasienkirimkeunitlainId);
        }

        return json_encode([
            'service' => 'Satusehat-ObservationLab',
            'state' => $state,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    protected function build()
    {
        $res = [
            "basedOn" => [
                [
                    "reference" => "ServiceRequest/".$this->satusehatIDServiceRequest
                ]
            ],
            "category" => [
                [
                    "coding" => [
                        [
                            "code" => "laboratory",
                            "display" => "Laboratory",
                            "system" => "http://terminology.hl7.org/CodeSystem/observation-category"
                        ]
                    ]
                ]
            ],
            "code" => [
                "coding" => [
                    [
                        "code" => "11477-7",
                        "display" => "Microscopic observation [Identifier] in Sputum by Acid fast stain",
                        "system" => "http://loinc.org"
                    ]
                ]
            ],
            "effectiveDateTime" => date_format(date_create($this->dateTimeCreate), 'Y-m-d'),
            "encounter" => [
                "reference" => (new SatuSehatPayload)->getReferences($this->pendaftaranId)
            ],
            "id" => "f1fc52be-8000-4be0-90f4-58128501e893",
            "identifier" => [
                [
                    "system" => "http://sys-ids.kemkes.go.id/observation/".$this->organizationId,
                    "value" => "O111111"
                ]
            ],
            "issued" => date_format(date_create($this->dateTimeCreate), DATE_ATOM),
            "meta" => [
                "lastUpdated" => "2022-11-04T03:22:28.043814+00:00",
                "profile" => [
                    "https://fhir.kemkes.go.id/r4/StructureDefinition/Observation|4.0.1"
                ],
                "versionId" => "MTY2NzUzMjE0ODA0MzgxNDAwMA"
            ],
            "performer" => [
                [
                    "reference" => "Practitioner/N10000001"
                ],
                [
                    "reference" => "Organization/".$this->organizationId
                ]
            ],
            "referenceRange" => [
                [
                    "text" => "Negative"
                ]
            ],
            "resourceType" => "Observation",
            "specimen" => [
                "reference" => "Specimen/".$this->satusehatIDSpeciment
            ],
            "status" => "final",
            "subject" => [
                "reference" => "Patient/100000030009"
            ],
            "valueCodeableConcept" => [
                "coding" => [
                    [
                        "code" => "260347006",
                        "display" => "+",
                        "system" => "http://snomed.info/sct"
                    ]
                ]
            ]
        ];

        return $res;
    }
}
