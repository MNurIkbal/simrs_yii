<?php

namespace Integrasi\Service\Satusehat;

use Yii;
use yii\helpers\ArrayHelper;
use Integrasi\Service\Satusehat\Models\Satusehat;
use Integrasi\Components\Services\SatusehatService;
use Integrasi\Components\models\PasienKirimUnitlain;
use Integrasi\Components\models\PasienMasukPenunjangT;
use Integrasi\Components\models\HasilPemeriksaanLab;

use Integrasi\Components\DocoConstants;
use Integrasi\Service\Satusehat\Components\SatuSehatPayload;
use DateTime;

class DiagnosticReport extends \Integrasi\Service\Satusehat\Encounter
{
    public $serviceRequestId;
    public $specimenId;
    public $observationLabId;
    public $dateTimeResultLab;
    public $pasienkirimkeunitlainId;

    public function execute()
    {
        $this->setAttrSatusehat();
        $this->logType = 'DiagnosticReport';
        $pasienMasukPenunjangId = ArrayHelper::getValue($this->attributes, 'id', null);

        if($pasienMasukPenunjangId) {
            $modelMasukPenunjang = PasienMasukPenunjangT::find()
            ->select([
                'pendaftaran_id',
                'pasienkirimkeunitlain_id'
            ])
            ->where([
                'pasienmasukpenunjang_id' => $pasienMasukPenunjangId,
                'status_periksa' => DocoConstants::ST_SELESAI //handle validasi expertise belum terisi
            ])
            ->asArray()->one();

            $this->pendaftaranId = ArrayHelper::getValue($modelMasukPenunjang, 'pendaftaran_id');
            $this->pasienkirimkeunitlainId = ArrayHelper::getValue($modelMasukPenunjang, 'pasienkirimkeunitlain_id');
        }

        if($this->pendaftaranId && $this->pasienkirimkeunitlainId) {
            $modelServiceRequest = (new Satusehat)->getServiceRequest()->andWhere([
                'pendaftaran_id' => $this->pendaftaranId, 
                'additional_id' => $this->pasienkirimkeunitlainId
            ])->asArray()->one();
    
            $modelSpecimen = (new Satusehat)->getSpecimen()->andWhere([
                'pendaftaran_id' => $this->pendaftaranId, 
                'additional_id' => $this->pasienkirimkeunitlainId
            ])->orderBy([
                'created_date' => SORT_DESC

            ])->asArray()->one();

            $modelObservationLab = (new Satusehat)->getObservationLab()->andWhere([
                'pendaftaran_id' => $this->pendaftaranId, 
                'additional_id' => $this->pasienkirimkeunitlainId
            ])->orderBy([
                'created_date' => SORT_DESC
            ])->asArray()->one();

            $modelHasilLab = HasilPemeriksaanLab::find()
            ->select([
                'tgl_hasilpemeriksaanlab'
            ])->where([
                'pasienmasukpenunjang_id' => $pasienMasukPenunjangId,
                'pendaftaran_id' => $this->pendaftaranId
            ])->asArray()->one();

            $hasilLab = ArrayHelper::getValue($modelHasilLab, 'tgl_hasilpemeriksaanlab');
            $this->serviceRequestId = ArrayHelper::getValue($modelServiceRequest, 'satusehat_id');
            $this->specimenId = ArrayHelper::getValue($modelSpecimen, 'satusehat_id');
            $this->observationLabId = ArrayHelper::getValue($modelObservationLab, 'satusehat_id');
            $this->dateTimeResultLab = (new DateTime($hasilLab))->format(DateTime::ATOM);

            $payload = $this->build();
            $res = (new SatusehatService)->create('/diagnosticreport',$payload, false);
            $this->setLogs($this->pendaftaranId, $this->state, $payload, $res, $this->pasienkirimkeunitlainId);
        }

        return json_encode([
            'service' => 'Satusehat-DiagnosticReport',
            'state' => $this->state,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    protected function build()
    {
        $res = [
                "resourceType" => $this->logType,
                "identifier" => [
                    [
                        "system" => "http://sys-ids.kemkes.go.id/diagnostic/" . $this->organizationId . "/lab",
                        "use" => "official",
                        "value" => "5234342",
                    ],
                ],
                "status" => "final",
                "category" => [
                    [
                        "coding" => [
                            [
                                "system" => "http://terminology.hl7.org/CodeSystem/v2-0074",
                                "code" => "MB",
                                "display" => "Microbiology",
                            ],
                        ],
                    ],
                ],
                "code" => [
                    "coding" => [
                        [
                            "system" => "http://loinc.org",
                            "code" => "11477-7",
                            "display" =>
                            "Microscopic observation [Identifier] in Sputum by Acid fast stain",
                        ],
                    ],
                ],
                "subject" => ["reference" => "Patient/100000030009"],
                "encounter" => [
                    "reference" => (new SatuSehatPayload)->getReferences($this->pendaftaranId),
                ],
                "effectiveDateTime" => $this->dateTimeResultLab,
                "issued" => $this->dateTimeResultLab,
                "performer" => [
                    ["reference" => "Practitioner/N10000001"],
                    ["reference" => "Organization/". $this->organizationId],
                ],
                "result" => [
                    ["reference" => "Observation/". $this->observationLabId],
                ],
                "specimen" => [
                    ["reference" => "Specimen/". $this->specimenId],
                ],
                "basedOn" => [
                    ["reference" => "ServiceRequest/". $this->serviceRequestId],
                ],
                "conclusionCode" => [
                    [
                        "coding" => [
                            [
                                "system" => "http://snomed.info/sct",
                                "code" => "260347006",
                                "display" => "+",
                            ],
                        ],
                    ],
                ],
            ];

        return $res;
    }
}
