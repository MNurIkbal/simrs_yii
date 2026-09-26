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

class Medication extends \Integrasi\Service\Satusehat\Encounter {

    const RESOURCE_TYPE = 'Medication';

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
                    $resData = (new SatusehatService)->createMedication($payload, false);

                    if($resData) {
                        $this->setLogs($this->pendaftaranId, $state, $payload, $resData);
                    }
                }

                return json_encode([
                    'service' => 'Satusehat-Medication',
                    'state' => $state,
                    'timestamp' => date('Y-m-d H:i:s')
                ]);
            } else if(in_array($return_from, ['reseptur_controller', 'reseptur_process'])) {
                if ( $this->pendaftaranId ) {
                    $payload = $this->build();
                    $resData = (new SatusehatService)->createMedication($payload, false);

                    if($resData) {
                        $this->setLogs($this->pendaftaranId, $state, $payload, $resData);
                    }
                }

                return json_encode([
                    'service' => 'Satusehat-Medication',
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
    	$resepDetail = InfoResepDetailView::find(true)->where(['reseptur_id' => $reseptur_id, 'jenis' => 'reseptur'])->asArray()->all();

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

    	return $medicine;
    }

    protected function build()
    {
    	$returnMedicine = $this->generateMedicine($this->resepturId);

        $res = [
            "resourceType" => self::RESOURCE_TYPE,
            "meta" => [
            	"profile" => [
            		"https://fhir.kemkes.go.id/r4/StructureDefinition/".self::RESOURCE_TYPE
            	]
            ],
            "identifier" => [
                [
                	"system" => "http://sys-ids.kemkes.go.id/medication/10000004",
                	"use" => "official",
                	"value" => "123456789"
                ]
            ],
            "code" => [
                "coding" => [
                    [
                        "system" => "http://sys-ids.kemkes.go.id/kfa",
                        "code" => "93001019",
                        "display" => $returnMedicine['obat_display']
                    ]
                ]
            ],
            "status" => "active",
            "manufacturer" => [
                "reference" => 'Organization/900001'
            ],
            "form" => [
                "coding" => [
                    [
                        "system" => "https://terminology.kemkes.go.id/CodeSystem/medication-form",
                        "code" => "BS023",
                        "display" => "Kaplet Salut Selaput"
                    ]
                ]
            ],
            "ingredient" => $returnMedicine['ingredient'],
            "extension" => [
                [
                    "url" => "https://fhir.kemkes.go.id/r4/StructureDefinition/MedicationType",
                    "valueCodeableConcept" => [
                        "coding" => [
                            [
                                "system" => "https://terminology.kemkes.go.id/CodeSystem/medication-type",
                                "code" => "NC",
                                "display" => "Non-compound"
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $res;
    }
}