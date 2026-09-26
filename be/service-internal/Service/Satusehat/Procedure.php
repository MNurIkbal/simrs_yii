<?php

namespace Integrasi\Service\Satusehat;

use Yii;
use yii\helpers\ArrayHelper;
use Integrasi\Components\Services\SatusehatService;
use Integrasi\Service\Satusehat\Components\SatuSehatPayload;

class Procedure extends \Integrasi\Service\Satusehat\Encounter
{
    public function execute()
    {
        $state = $this->state;
        $this->logType = 'Procedure';
        $this->pendaftaranId = ArrayHelper::getValue($this->result, 'pendaftaran_id', null);
        $instruksiId = ArrayHelper::getValue($this->result, 'instruksi.instruksi_id', null); 

        if ($this->pendaftaranId && $instruksiId) {
            $payload = $this->build();
            $res = (new SatusehatService)->createProcedure($payload, false);
            $this->setLogs($this->pendaftaranId, $state, $payload, $res, $instruksiId);
        }

        return json_encode([
            'service' => 'Satusehat-Procedure',
            'state' => $state,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    protected function build()
    {
        $res = [
            "resourceType" => $this->logType,
            "status" => "completed",
            "category" => [
                "coding" => [
                    [
                        "system" => "http://snomed.info/sct",
                        "code" => "103693007",
                        "display" => "Diagnostic procedure"
                    ]
                ],
                "text" => "Diagnostic procedure"
            ],
            "code" => [
                "coding" => [
                    [
                        "system" => "http://hl7.org/fhir/sid/icd-9-cm",
                        "code" => "87.44",
                        "display" => "Routine chest x-ray, so described"
                    ]
                ]
            ],
            "subject" => [
                "reference" => "Patient/P00030004",
                "display" => "Budi Santoso"
            ],
            "encounter" => [
                "reference" => (new SatuSehatPayload)->getReferences($this->pendaftaranId),
                "display" => "Tindakan Rontgen Dada Budi Santoso pada Selasa tanggal 14 Juni 2022"
            ],
            "performedPeriod" => [
                "start" => "2022-06-14T13:31:00+01:00",
                "end" => "2022-06-14T14:27:00+01:00"
            ],
            "performer" => [
                [
                    "actor" => [
                        "reference" => "Practitioner/N10000001",
                        "display" => "Dokter Bronsig"
                    ]
                ]
            ],
            "reasonCode" => [
                [
                    "coding" => [
                        [
                            "system" => "http://hl7.org/fhir/sid/icd-10",
                            "code" => "A15.0",
                            "display" => "Tuberculosis of lung, confirmed by sputum microscopy with or without culture"
                        ]
                    ]
                ]
            ],
            "bodySite" => [
                [
                    "coding" => [
                        [
                            "system" => "http://snomed.info/sct",
                            "code" => "302551006",
                            "display" => "Entire Thorax"
                        ]
                    ]
                ]
            ],
            "note" => [
                [
                    "text" => "Rontgen thorax melihat perluasan infiltrat dan kavitas."
                ]
            ]
        ];

        return $res;
    }
}
