<?php

namespace Integrasi\Service\Satusehat;

use Yii;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoConstants;
use Integrasi\Components\Services\SatusehatService;
use Integrasi\Service\Satusehat\Components\SatuSehatPayload;
use Integrasi\Service\Satusehat\Models\Satusehat;
use Integrasi\Service\Satusehat\Models\InfoResepDetailView;
use Integrasi\Service\Satusehat\Models\InfoResepView;

class MedicationRequest extends \Integrasi\Service\Satusehat\Encounter {

    const RESOURCE_TYPE = 'MedicationRequest';

    public $resepturId;
    public $pendaftaranId;
    public $logType;

    public function execute()
    {
        $return_rule = ['reseptur_controller', 'reseptur_process', 'serahkan_obat_process'];
        $return_from = isset($this->attributes['result']['data']['from']) ? $this->attributes['result']['data']['from'] : NULL;
        $state = $this->state;
        $this->resepturId = $this->attributes['result']['data']['reseptur_id'];
        $this->pendaftaranId = $this->attributes['result']['data']['pendaftaran_id'];
        $this->logType = self::RESOURCE_TYPE;

        $instalasi_reseptur = InfoResepView::find(true)->where(['reseptur_id' => $this->resepturId, 'pendaftaran_id' => $this->pendaftaranId])->one()->instalasi_reseptur_id;

        if(in_array($return_from, $return_rule)) {
            if($return_from == 'serahkan_obat_process' && $instalasi_reseptur == DocoConstants::INST_ID_RJ) {
                if ( $this->pendaftaranId ) {
                    $payload = $this->build();
                    $resData = (new SatusehatService)->createMedicationRequest($payload, false);

                    if($resData) {
                        $this->setLogs($this->pendaftaranId, $state, $payload, $resData);
                    }
                }

                return json_encode([
                    'service' => 'Satusehat-MedicationRequest',
                    'state' => $state,
                    'timestamp' => date('Y-m-d H:i:s')
                ]);
            } else if(in_array($return_from, ['reseptur_controller', 'reseptur_process'])) {
                if ( $this->pendaftaranId ) {
                    $payload = $this->build();
                    $resData = (new SatusehatService)->createMedicationRequest($payload, false);

                    if($resData) {
                        $this->setLogs($this->pendaftaranId, $state, $payload, $resData);
                    }
                }

                return json_encode([
                    'service' => 'Satusehat-MedicationRequest',
                    'state' => $state,
                    'timestamp' => date('Y-m-d H:i:s')
                ]);
            } else {
                return json_encode(['message' => 'Unable process, outside rule']);
            }
        } else {
            return json_encode(['message' => 'Unable process, outside rule']);
        }
    }

    protected function generateMedicine($reseptur_id) {
    	$infoResep = InfoResepView::find(true)->where(['reseptur_id' => $reseptur_id])->one();
    	$encounterReference = (new SatuSehatPayload)->getReferences($this->pendaftaranId);
    	$resepDetail = InfoResepDetailView::find(true)->where(['reseptur_id' => $reseptur_id, 'jenis' => 'reseptur'])->asArray()->all();

    	if(isset($infoResep->diagnosa_id)){
    	    $diagnosa = $infoResep->diagnosa_nama;
    	} else if(isset($infoResep->diagnosa_text)) {
    	    $diagnosa = $infoResep->diagnosa_text;
    	} else {
    	    $diagnosa = "-";
    	}

    	$ingredient = array();
    	$nama_obat = array();
        foreach ($resepDetail as $key => $value) {
            $ingredient[] = [
                "itemCodeableConcept" => [
                    "coding" => [
                        [
                            "system" => "http://sys-ids.kemkes.go.id/kfa",
                            "code" => "91000330",
                            "display" => $value['obatalkes_nama']
                        ]
                    ]
                ],
                "isActive" => true,
                "strength" => [
                    "numerator" => [
                        "value" => 150,
                        "system" => "http://unitsofmeasure.org",
                        "code" => "mg"
                    ],
                    "denominator" => [
                        "value" => (int) $value['qty_transaksi'],
                        "system" => "http://terminology.hl7.org/CodeSystem/v3-orderableDrugForm",
                        "code" => $value['satuan_input']
                    ]
                ]
            ];

            $nama_obat[] = $value['obatalkes_nama'];
        }

	    $defaultNamaObat = "Obat Anti Tuberculosis / Rifampicin 150 mg / Isoniazid 75 mg / Pyrazinamide 400 mg / Ethambutol 275 mg Kaplet Salut Selaput (KIMIA FARMA)";
		$medicine['obat_display'] = (count($nama_obat) > 0) ? 'Obat '.implode(' / ', $nama_obat) : $defaultNamaObat;
    	$medicine['ingredient'] = $ingredient;
    	$medicine['pendaftaran_id'] = $this->pendaftaranId;
    	$medicine['encounter_reference'] = $encounterReference;
    	$medicine['diagnosa'] = $diagnosa;

    	return $medicine;
    }

    protected function build()
    {
    	$returnMedicine = $this->generateMedicine($this->resepturId);

        $res = [
            "resourceType" => self::RESOURCE_TYPE,
            "identifier" => [
            	[
	            	"system" => "http://sys-ids.kemkes.go.id/prescription/10000004",
	            	"use" => "official",
	            	"value" => "123456788"
            	],
            	[
	            	"system" => "http://sys-ids.kemkes.go.id/prescription-item/10000004",
	            	"use" => "official",
	            	"value" => "123456788-1"
            	],
            ],
            "status" => "completed",
            "intent" => "order",
            "category" => [
                [
                    "coding" => [
                        [
                            "system" => "http://terminology.hl7.org/CodeSystem/medicationrequest-category",
                            "code" => "outpatient",
                            "display" => "Outpatient"
                        ]
                    ]
                ]
            ],
            "priority" => "routine",
            "medicationReference" => [
                "reference" => "Medication/e96f7df8-9386-4cf2-99ca-cab9771f6eb3",
                "display" => $returnMedicine['obat_display']
            ],
            "subject" => [
                "reference" => "Patient/100000030009",
                "display" => "Budi Santoso"
            ],
            "encounter" => [
                "reference" => $returnMedicine['encounter_reference']
            ],
            "authoredOn" => "2022-08-04",
            "requester" => [
                "reference" => "Practitioner/N10000001",
                "display" => "Dokter Bronsig"
            ],
            "reasonCode" => [
                [
                    "coding" => [
                        [
                            "system" => "http://hl7.org/fhir/sid/icd-10",
                            "code" => "A15.0",
                            "display" => $returnMedicine['diagnosa']
                        ]
                    ]
                ]
            ],
            "courseOfTherapyType" => [
                "coding" => [
                    [
                    	"system" => "http://terminology.hl7.org/CodeSystem/medicationrequest-course-of-therapy",
                        "code" => "continuous",
                        "display" => "Continuing long term therapy"
                    ]
                ]
            ],
            "dosageInstruction" => [
                [
                	"sequence" => 1,
                    "text" => "4 tablet per hari",
                    "additionalInstruction" => [
                        [
                            "text" => "Diminum setiap hari"
                        ]
                    ],
                    "patientInstruction" => "4 tablet perhari, diminum setiap hari tanpa jeda sampai prose pengobatan berakhir",
                    "timing" => [
                        "repeat" => 
                            [
                            	"frequency" => 1,
                                "period" => 1,
                                "periodUnit" => "d"
                            ]
                                            
                    ],
                    "route" =>
                        [
                        	"coding" => [
                                [
                                    "system" => "http://www.whocc.no/atc",
                                    "code" => "O",
                                    "display" => "Oral"
                                ]
                            ]
                        ],
                    "doseAndRate" => [
                        [
                            "type" => [
                                "coding" => [
                                    [
                                        "system" => "http://terminology.hl7.org/CodeSystem/dose-rate-type",
                                        "code" => "ordered",
                                        "display" => "Ordered"
                                    ]
                                ]
                            ],
                            "doseQuantity" => [
                                "value" => 4,
                                "unit" => "TAB",
                                "system" => "http://terminology.hl7.org/CodeSystem/v3-orderableDrugForm",
                                "code" => "TAB"
                            ]
                        ]
                    ]
                ]
            ],
            "dispenseRequest" => [
                "dispenseInterval" => [
                    "value" => 1,
                    "unit" => "days",
                    "system" => "http://unitsofmeasure.org",
                    "code" => "d"
                ],
                "validityPeriod" => [
                    "start" => "2022-01-01",
                    "end" => "2022-01-30"
                ],
                "numberOfRepeatsAllowed" => 0,
                "quantity" => [
                    "value" => 120,
                    "unit" => "TAB",
                    "system" => "http://terminology.hl7.org/CodeSystem/v3-orderableDrugForm",
                    "code" => "TAB"
                ],
                "expectedSupplyDuration" => [
                    "value" => 30,
                    "unit" => "days",
                    "system" => "http://unitsofmeasure.org",
                    "code" => "d"
                ],
                "performer" => [
                    "reference" => "Organization/10000004"
                ]
            ]
        ];

        return $res;
    }
}