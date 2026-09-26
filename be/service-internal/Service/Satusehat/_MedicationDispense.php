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

class MedicationDispense extends \Integrasi\Service\Satusehat\Encounter {

    const RESOURCE_TYPE = 'MedicationDispense';

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
                    $resData = (new SatusehatService)->createMedicationDispense($payload, false);

                    if($resData) {
                        $this->setLogs($this->pendaftaranId, $state, $payload, $resData);
                    }
                }

                return json_encode([
                    'service' => 'Satusehat-MedicationDispense',
                    'state' => $state,
                    'timestamp' => date('Y-m-d H:i:s')
                ]);
            } else if(in_array($return_from, ['reseptur_controller', 'reseptur_process'])) {
                if ( $this->pendaftaranId ) {
                    $payload = $this->build();
                    $resData = (new SatusehatService)->createMedicationDispense($payload, false);

                    if($resData) {
                        $this->setLogs($this->pendaftaranId, $state, $payload, $resData);
                    }
                }

                return json_encode([
                    'service' => 'Satusehat-MedicationDispense',
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
            "category" => [
                "coding" => [
                    [
                        "system" => "http://terminology.hl7.org/CodeSystem/medicationdispense-category",
                        "code" => "outpatient",
                        "display" => "Outpatient"
                    ]
                ]
            ],
            "medicationReference" => [
                "reference" => "Medication/e96f7df8-9386-4cf2-99ca-cab9771f6eb3",
                "display" => $returnMedicine['obat_display']
            ],
            "subject" => [
                "reference" => "Patient/100000030009",
                "display" => "Budi Santoso"
            ],
            "context" => [
                "reference" => $returnMedicine['encounter_reference']
            ],
            "performer" => [
                [
	                "actor" => [
	                    "reference" => "Practitioner/N10000003",
	                    "display" => "John Miller"
	                ]
	            ]
            ],
            "location" => [
                "reference" => "Location/52e135eb-1956-4871-ba13-e833e662484d",
                "display" => "Apotek RSUD Jati Asih"
            ],
            "authorizingPrescription" => [
                [
                    "reference" => "MedicationRequest/957a0dbc-6821-4675-9a1f-9c024907a609"
                ]
            ],
            "quantity" => [
                "system" => "http://terminology.hl7.org/CodeSystem/v3-orderableDrugForm",
                "code" => "TAB",
                "value" => 120
            ],
            "daysSupply" => [
                "value" => 30,
                "unit" => "Day",
                "system" => "http://unitsofmeasure.org",
                "code" => "d"
            ],
            "whenPrepared" => "2022-01-15T10:20:00Z",
            "whenHandedOver" => "2022-01-15T16:20:00Z",
            "dosageInstruction" => [
                [
                    "sequence" => 1,
                    "text" => "Diminum 4 tablet sekali dalam sehari",
                    "timing" => [
                        "repeat" => [
                            "frequency" => 1,
                            "period" => 1,
                            "periodUnit" => "d"
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
            ]
        ];

        return $res;
    }
}