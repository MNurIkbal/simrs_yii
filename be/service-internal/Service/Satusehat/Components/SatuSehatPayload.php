<?php

namespace Integrasi\Service\Satusehat\Components;

use Yii;
use yii\helpers\ArrayHelper;
use Integrasi\Service\Satusehat\Models\Satusehat;

class SatuSehatPayload {

    public function generatePayload($types = [], $additional = [])
    {
        $pendaftaranId = ArrayHelper::getValue($additional, 'pendaftaran_id', null);
        if ( !$types ) {
            Yii::error('!types');
            return [];
        }
        $payload = [];
        foreach( $types as $payloadType) {
            switch ( $payloadType ) {
                case 'GENERAL-VITAL-SIGN':
                    $payload = array_merge($payload, [
                        "resourceType" =>"Observation",
                        "status" => "final",
                        "category" => [
                            [
                                "coding" => [
                                    [
                                        "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                        "code" => "vital-signs",
                                        "display" =>"Vital Signs"
                                    ]
                                ]
                            ]
                        ],
                        "subject" => [
                            "reference" => "Patient/100000030009"
                        ],
                        "encounter" => [
                            "reference" => $this->getReferences($pendaftaranId),
                            "display" => "Pemeriksaan Fisik Nadi Budi Santoso di hari Selasa, 14 Juni 2022"
                        ],
                        "effectiveDateTime" => "2022-07-14"
                    ]);
                    break;
                case 'PULSE-VITAL-SIGN':
                    $payload = array_merge($payload, [
                        "code" => [
                            "coding" => [
                                [
                                    "system" => "http://loinc.org",
                                    "code" => "8867-4",
                                    "display" => "Heart rate"
                                ]
                            ]
                        ],
                        "valueQuantity" => [
                            "value" => 80,
                            "unit" => "beats/minute",
                            "system" => "http://unitsofmeasure.org",
                            "code" => "/min"
                        ]
                    ]);
                    break;
                case 'BREATHING-VITAL-SIGN':
                    $payload = array_merge($payload, [
                        "code" => [
                            "coding" => [
                                [
                                    "system" => "http://loinc.org",
                                    "code" => "9279-1",
                                    "display" => "Respiratory rate"
                                ]
                            ]
                        ],
                        "valueQuantity" => [
                            "value" => 22,
                            "unit" => "breaths/minute",
                            "system" => "http://unitsofmeasure.org",
                            "code" => "/min"
                        ]
                    ]);
                    break;
                case 'SISTOL-VITAL-SIGN':
                    $payload = array_merge($payload, [
                        "code" => [
                            "coding" => [
                                [
                                    "system" => "http://loinc.org",
                                    "code" => "8480-6",
                                    "display" => "Systolic blood pressure"
                                ]
                            ]
                        ],
                        "valueQuantity" => [
                            "value" => 22,
                            "unit" => "mm[Hg]",
                            "system" => "http://unitsofmeasure.org",
                            "code" => "mm[Hg]"
                        ],
                        "interpretation" => [
                            [
                                "coding" => [
                                    [
                                        "system" => "http://terminology.hl7.org/CodeSystem/v3-ObservationInterpretation",
                                        "code" => "HU",
                                        "display" => "significantly high"
                                    ]
                                ],
                                "text" =>"Di atas nilai referensi"
                            ]
                        ]
                    ]);
                    break;
                case 'DIASTOLE-VITAL-SIGN':
                    $payload = array_merge($payload, [
                        "code" => [
                            "coding" => [
                                [
                                    "system" => "http://loinc.org",
                                    "code" => "8462-4",
                                    "display" => "Diastolic blood pressure"
                                ]
                            ]
                        ],
                        "valueQuantity" => [
                            "value" => 22,
                            "unit" => "mm[Hg]",
                            "system" => "http://unitsofmeasure.org",
                            "code" => "mm[Hg]"
                        ],
                        "bodySite" => [
                            "coding" => [
                                [
                                    "system" => "http://snomed.info/sct",
                                    "code" => "368209003",
                                    "display" => "Right arm"
                                ]
                            ]
                        ],
                        "interpretation" => [
                            [
                                "coding" => [
                                    [
                                        "system" => "http://terminology.hl7.org/CodeSystem/v3-ObservationInterpretation",
                                        "code" => "L",
                                        "display" => "low"
                                    ]
                                ],
                                "text" => "Di bawah nilai referensi"
                            ]
                        ]
                    ]);
                    break;
                case 'TEMP-VITAL-SIGN':
                    $payload = array_merge($payload, [
                        "code" => [
                            "coding" => [
                                [
                                    "system" => "http://loinc.org",
                                    "code" => "8310-5",
                                    "display" => "Body temperature"
                                ]
                            ]
                        ],
                        "valueQuantity" => [
                            "value" => 22,
                            "unit" => "C",
                            "system" => "http://unitsofmeasure.org",
                            "code" => "Cel"
                        ],
                        "interpretation" => [
                            [
                                "coding" => [
                                    [
                                        "system" => "http://terminology.hl7.org/CodeSystem/v3-ObservationInterpretation",
                                        "code" => "H",
                                        "display" => "High"
                                    ]
                                ],
                                "text" => "Di atas nilai referensi"
                            ]
                        ]
                    ]);
                    break;
                case 'COMPOSITION-GENERAL-PAYLOAD':
                    $payload = array_merge($payload,
                        [
                            "resourceType" => "Composition",
                            "identifier" => [
                                "system" => "http://sys-ids.kemkes.go.id/composition/10000004",
                                "value" => "P20240001"
                            ],
                            "status" => "final",
                            "type" => [ 
                                "coding" => [
                                    [
                                        "system" => "http://loinc.org",
                                        "code" => "18842-5",
                                        "display" => "Discharge summary"
                                    ]
                                ]
                            ],
                            "category" => [
                                [
                                    "coding" => [
                                        [
                                            "system" => "http://loinc.org",
                                            "code" => "LP173421-1",
                                            "display" => "Report"
                                        ]
                                    ]
                                ]
                            ],
                            "subject" => [
                                "reference" => "Patient/100000030009",
                                "display" => "Budi Santoso"
                            ],
                            "encounter" => [
                                "reference" => $this->getReferences($pendaftaranId),
                                "display" => "Kunjungan Budi Santoso di hari Selasa, 14 Juni 2022"
                            ],
                            "date" => "2022-06-14",
                            "author" => [
                                [
                                    "reference" => "Practitioner/N10000001",
                                    "display" => "Dokter Bronsig"
                                ]
                            ],
                            "title" => "Resume Medis Rawat Jalan",
                            "custodian" => [
                                "reference" => "Organization/10000004"
                            ],
                            "section" => [
                                [
                                    "code" => [
                                        "coding" => [
                                            [
                                                "system" => "http://loinc.org",
                                                "code" => "42344-2",
                                                "display" => "Discharge diet (narrative)"
                                            ]
                                        ]
                                    ],
                                    "text" => [
                                        "status" => "additional",
                                        "div" => "Rekomendasi diet rendah lemak, rendah kalori"
                                    ]
                                ]
                            ]
                        ]
                    );
                    break;
                default:
                    Yii::error('!default');
                    continue;
                    break;
            }
        }
        return $payload;
    }

    public function getReferences( $pendaftaranId = '', $type = 'encounter')
    {
        if ( empty($pendaftaranId) ) {
            return null;
        }
        $references = 'Encounter/2823ed1d-3e3e-434e-9a5b-9c579d192787';
        $getEncounter = (new Satusehat)->getEncounter()->andWhere([
            'pendaftaran_id' => $pendaftaranId
        ])->asArray()->one();
        if ( !empty($getEncounter) ) {
            $references = 'Encounter/'.$getEncounter['satusehat_id'];
        }
        return $references;
    }
}