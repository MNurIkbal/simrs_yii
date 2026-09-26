<?php

namespace Integrasi\Service\Satusehat;

use Yii;
use yii\helpers\ArrayHelper;
use Integrasi\Components\Services\SatusehatService;
use Integrasi\Components\models\PasienKirimUnitlain;
use Integrasi\Service\Satusehat\Components\SatuSehatPayload;
use DateTime;

class ServiceRequest extends \Integrasi\Service\Satusehat\Encounter
{
    public $createReqTime;

    public function execute()
    {
        $this->setAttrSatusehat();
        $state = $this->state;
        $this->logType = 'ServiceRequest';
        $this->createReqTime = (new DateTime())->format(DateTime::ATOM);
        $pasienkirimunitlainId = ArrayHelper::getValue($this->result, 'pasienkirimkeunitlain_id', null);

        if($pasienkirimunitlainId) {
            $model = PasienKirimUnitlain::find()
            ->select([
                'pendaftaran_id'
            ])
            ->where([
                'pasienkirimkeunitlain_id' => $pasienkirimunitlainId
            ])
            ->asArray()->one();
            $this->pendaftaranId = ArrayHelper::getValue($model, 'pendaftaran_id', null);
        }

        if ($this->pendaftaranId && $pasienkirimunitlainId) {
            $payload = $this->build();
            $res = (new SatusehatService)->createServiceRequest($payload, false);
            $this->setLogs($this->pendaftaranId, $state, $payload, $res, $pasienkirimunitlainId);
        }

        return json_encode([
            'service' => 'Satusehat-ServiceRequest',
            'state' => $state,
            'timestamp' => date('Y-m-d H:i:s'),
            'result' => $this->result
        ]);
    }

    protected function build()
    {
        $res = [
            "resourceType" => $this->logType,
            "identifier" => [
                [
                    "system" => "http://sys-ids.kemkes.go.id/servicerequest/" . $this->organizationId,
                    "value" => "00001",
                ],
            ],
            "status" => "active",
            "intent" => "original-order",
            "priority" => "routine",
            "code" => [
                "coding" => [
                    [
                        "system" => "http://loinc.org",
                        "code" => "11477-7",
                        "display" =>
                        "Microscopic observation [Identifier] in Sputum by Acid fast stain",
                    ],
                ],
                "text" => "Pemeriksaan Sputum BTA",
            ],
            "subject" => ["reference" => "Patient/100000030009"],
            "encounter" => [
                "reference" => (new SatuSehatPayload)->getReferences($this->pendaftaranId),
                "display" =>
                "Permintaan BTA Sputum Budi Santoso di hari Selasa, 14 Juni 2022 pukul 09:30 WIB",
            ],
            "occurrenceDateTime" => $this->createReqTime,
            "requester" => [
                "reference" => "Practitioner/N10000001",
                "display" => "Dokter Bronsig",
            ],
            "performer" => [
                ["reference" => "Practitioner/N10000005", "display" => "Fatma"],
            ],
            "reasonCode" => [["text" => "Periksa jika ada kemungkinan Tuberculosis"]],
        ];


        return $res;
    }
}
