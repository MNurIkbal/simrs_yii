<?php

namespace Integrasi\Service\Satusehat;

use yii\helpers\ArrayHelper;
use Integrasi\Components\Services\SatusehatService;
use Integrasi\Service\Satusehat\Models\Satusehat;

class Specimen extends \Integrasi\Service\Satusehat\Encounter
{
    public $dateTimeCreate;
    public $satusehatID;

    public function execute()
    {
        $state = $this->state;
        $this->logType = 'Specimen';
        $this->setAttrSatusehat();
        $this->pendaftaranId = ArrayHelper::getValue($this->result, 'pendaftaran_id', null);

        $pasienkirimkeunitlainId = ArrayHelper::getValue($this->result, 'pasienkirimkeunitlain_id', null);
        $tglAmbilsample = ArrayHelper::getValue($this->result, 'tgl_ambilsample', null);
        $jamAmbilsample = ArrayHelper::getValue($this->result, 'jam_ambilsample', null);

        $model = (new Satusehat)->getServiceRequest()->andWhere([
            'pendaftaran_id' => $this->pendaftaranId, 
            'additional_id' => $pasienkirimkeunitlainId
        ])->asArray()->one();

        $this->dateTimeCreate = date_format(date_create(date_format(date_create($tglAmbilsample.' '.$jamAmbilsample), "Y/m/d H:i:s")), DATE_ATOM);
        $this->satusehatID = ArrayHelper::getValue($model, 'satusehat_id');

        if ($this->pendaftaranId && $pasienkirimkeunitlainId) {
            $payload = $this->build();
            $res = (new SatusehatService)->create('/specimen', $payload, false);
            $this->setLogs($this->pendaftaranId, $state, $payload, $res, $pasienkirimkeunitlainId);
        }

        return json_encode([
            'service' => 'Satusehat-Specimen',
            'state' => $state,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    protected function build()
    {
        $res = [
            "collection" => [
                "collectedDateTime" =>  $this->dateTimeCreate,
                "method" => [
                    "coding" => [
                        [
                            "code" => "386089008",
                            "display" => "Collection of coughed sputum",
                            "system" => "https://snomed.info/sct"
                        ]
                    ]
                 ]
           ],
            "id" => "df3aaf6c-fe59-4e0b-8ca4-f05e0cfe40d0",
            "identifier" => [
                [
                    "assigner" => [
                        "reference" => "Organization/".$this->organizationId
                    ],
                    "system" => "http://sys-ids.kemkes.go.id/specimen/".$this->organizationId,
                    "value" => "00001"
               ]
            ],
            "meta" => [
                "lastUpdated" => "2022-11-01T04:02:57.048330+00:00",
                "versionId" => "MTY2NzI3NTM3NzA0ODMzMDAwMA"
           ],
            "receivedTime" =>  $this->dateTimeCreate,
            "request" => [
                [
                    "reference" => "ServiceRequest/".$this->satusehatID
                ]
            ],
            "resourceType" => "Specimen",
            "status" => "available",
            "subject" => [
                "display" => "Budi Santoso",
                "reference" => "Patient/100000030009"
           ],
            "type" => [
                "coding" => [
                    [
                        "code" => "45710003",
                        "display" => "Sputum",
                        "system" => "http://snomed.info/sct"
                    ]
                ]
           ]
        ];

        return $res;
    }
}
