<?php

namespace Integrasi\Service\Roche;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;

use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoConstants;
use Integrasi\Service\Roche\Models\BridgingOrderLabRocheView;
use Integrasi\Service\Roche\Models\IntegrasiPasienRoche;
use Integrasi\Components\Services\RocheService;
use Integrasi\Service\Roche\Object\PatientObject;

class Patient extends \Integrasi\Contracts\DocoImplement
{

    private $_patientId;

    /**
     * return @void
     */
    private function setAttribute()
    {
        $this->_patientId = $this->result['pasien_id'];
    }

    public function execute()
    {
        $this->setAttribute();
        $payloadData = $this->getData();

        $model = new PatientObject;
        $model->attributes = $payloadData;
        $patientName = ArrayHelper::getValue($payloadData, 'patient_name');
        $model->setPatientName($patientName);

        if (!empty($model->attributes)) {
            // post approved data to roche
            $response = (new RocheService)->updatePatient($model->attributes, function ($data) {
                return $data;
            }, 'PATCH');

            $rekapRoche = new IntegrasiPasienRoche;
            $result = ArrayHelper::getValue($response, 'Results.0.data.Data');
            $prosesId = ArrayHelper::getValue($result, 'StatusId');
            $rekapRoche->pasien_id = $model->pasien_id;
            $rekapRoche->payload = json_encode($model->attributes);
            $rekapRoche->id_sync_sercon = $prosesId;
            $rekapRoche->sync_respon = json_encode($response);
            $rekapRoche->is_sending = true;
            $rekapRoche->is_sent = ArrayHelper::getValue($result, 'IsResponded', false);;

            if (!$rekapRoche->save()) {
                return json_encode([
                    'service' => 'Roche-Patient',
                    'payload' => $model->attributes,
                    'error' => $rekapRoche->errors,
                    'timestamp' => date('Y-m-d H:i:s'),
                ]);
            }

        }

        return json_encode([
            'service' => 'Roche-Patient',
            'payload' => $payloadData,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function getData()
    {
        return BridgingOrderLabRocheView::find()
                ->select([
                    'patient_id', // No rekam medik
                    'patient_name',
                    'date_of_birth',
                    'gender',
                    'mother_maiden_name',
                    'pasien_id', // Pasien Id
                ])->andWhere([
                    'pasien_id' => $this->_patientId
                ])->asArray()->one();
    }
}