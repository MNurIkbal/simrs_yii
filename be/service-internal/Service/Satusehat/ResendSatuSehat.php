<?php

namespace Integrasi\Service\Satusehat;

use Yii;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoConstants;
use Integrasi\Components\Services\SatusehatService;
use Integrasi\Service\Satusehat\Models\Satusehat;
use Integrasi\Service\Satusehat\Models\SatusehatIntegrasiView;
use Integrasi\Service\Satusehat\Models\SatusehatPasien;
use Integrasi\Service\Satusehat\Models\SatusehatPegawai;
use Integrasi\Service\Satusehat\Models\SatusehatInstalasi;
use Integrasi\Service\Satusehat\Models\SatusehatRuangan;
use yii\helpers\ArrayHelper;

class ResendSatuSehat extends \Integrasi\Contracts\DocoImplement
{
    public $list_type_update = [];

    /** resend satu sehat by type */
    public function execute()
    {
        $list_id_satusehat_int = isset($this->attributes['result']['list_id_satusehat_int']) ? $this->attributes['result']['list_id_satusehat_int'] : null;
        if ($list_id_satusehat_int) {
            $dataSatuSehat = SatusehatIntegrasiView::find()->where(['IN', 'id', $list_id_satusehat_int])->asArray()->all();
            $res_satusehat = [];
            foreach($dataSatuSehat as $key => $value) {
                $integrationId = ArrayHelper::getValue($value, 'id');
                $payload = json_decode(ArrayHelper::getValue($value, 'payload'), true);
                $state = ArrayHelper::getValue($value, 'state');
                $type = ArrayHelper::getValue($value, 'type');

                switch ($type) {
                    case DocoConstants::SATU_SEHAT_TYPE_PRACTITIONER;
                        $res_satusehat = (new SatusehatService)->createPractitioner($payload, true);
                        break;
                    case DocoConstants::SATU_SEHAT_TYPE_ORGANIZATION;
                        $res_satusehat = $state == DocoConstants::SATU_SEHAT_STATE_CREATE ? (new SatusehatService)->createOrganization($payload, true) : (new SatusehatService)->updateOrganization($payload, true);
                        break;
                    case DocoConstants::SATU_SEHAT_TYPE_LOCATION;
                        $res_satusehat = (new SatusehatService)->createLocation($payload, true);
                        break;    
                    case DocoConstants::SATU_SEHAT_TYPE_LOCATIONUPDATE;
                        $res_satusehat = (new SatusehatService)->updateLocation($payload, true);
                        break;
                    case DocoConstants::SATU_SEHAT_TYPE_PATIENT;
                        $res_satusehat = (new SatusehatService)->createPatient($payload, true);
                        break;            
                    case DocoConstants::SATU_SEHAT_TYPE_ENCOUNTER;
                        $res_satusehat = (new SatusehatService)->createEncounter($payload, true);
                        break;
                    case DocoConstants::SATU_SEHAT_TYPE_ENCOUNTERUPDATEINPROGRES;
                        $res_satusehat = (new SatusehatService)->updateEncounter($payload);
                        break;
                    case DocoConstants::SATU_SEHAT_TYPE_CONDITION;
                        $res_satusehat = (new SatusehatService)->createCondition($payload, true);
                        break;
                    case DocoConstants::SATU_SEHAT_TYPE_CONDITIONSECONDARY;
                        $res_satusehat = (new SatusehatService)->createCondition($payload, true);
                        break;
                    case DocoConstants::SATU_SEHAT_TYPE_ENCOUNTERFINISH;
                        $res_satusehat = (new SatusehatService)->updateEncounter($payload, true);
                        break;
                    case DocoConstants::SATU_SEHAT_TYPE_OBSERVATION;
                        $res_satusehat = (new SatusehatService)->createObservation($payload, true);
                        break;
                    case DocoConstants::SATU_SEHAT_TYPE_PROCEDURE;
                        $res_satusehat = (new SatusehatService)->createProcedure($payload, true);
                        break;
                    case DocoConstants::SATU_SEHAT_TYPE_COMPOSITION_DIET;
                        $res_satusehat = (new SatusehatService)->createComposition($payload, true);
                        break;
                    default:
                        $res_satusehat = [];
                        break;
                }
                
                $this->updateLogSatuSehat($integrationId, $res_satusehat);
                /** buat response json untuk masing2 res berdasarkan typenya */
                $this->list_type_update[$type] = [
                    'res_satusehat' => $res_satusehat,
                    'payload' => $payload
                ];
            }
        }

        return json_encode([
            'service' => 'Satusehat-ResendSatuSehat',
            'timestamp' => date('Y-m-d H:i:s'),
            'attribute' => $this->attributes,
            'list_id_satusehat_int' => $list_id_satusehat_int,
            'list_type_update' => $this->list_type_update,
        ]);
    }

    /**
     * update log untuk = 
     * Satusehat,  SatusehatPegawai, SatusehatInstalasi, SatusehatRuangan, SatusehatPasien
     * $integrationId = satusehat_integration_id
     * $res_satusehat = response dari satu sehat sercon
     */
    public function updateLogSatuSehat($integrationId, $res_satusehat)
    {
        $uidSercon = ArrayHelper::getValue($res_satusehat, 'uid');
	    $result_sercon = isset($res_satusehat['response']) ? (is_array($res_satusehat['response']) ? $res_satusehat['response'] : json_decode($res_satusehat['response'], true)) : [];
	    /** get satusehat_id dari response satu sehat sercon  */
        $satusehat_id = isset($result_sercon['entry'][0]['resource']['id']) ? $result_sercon['entry'][0]['resource']['id'] : null;
        if (empty($satusehat_id)) {
            $satusehat_id = isset($result_sercon['id']) ? $result_sercon['id'] : NULL;
        }

	    $error = !empty($res_satusehat['error']) ? $res_satusehat['error'] : NULL;
	    if(!empty($error)) {
	        $sync_response = $error;
	    } else { 
	        $sync_response = isset($res_satusehat['response']) ? (is_array($res_satusehat['response']) ? json_encode($res_satusehat['response']) : $res_satusehat['response']) : json_encode($res_satusehat);
	    }
        
        /** update satusehat_integrasi_t */
        $satuSehat = Satusehat::find()->where(['id' => $integrationId])->one();
        if ($satusehat_id) {
            $satuSehat->satusehat_id = $satusehat_id;
            $satuSehat->is_sent = true;
        }
        $satuSehat->id_sync_sercon = $uidSercon;
        $satuSehat->sync_response = $sync_response;
        $satuSehat->tgl_resend = date('Y-m-d H:i:s');
        $satuSehat->save();

        $satusehatPegawai = SatusehatPegawai::find()->where(['satusehat_integration_id' => $integrationId])->one();
        if ($satusehat_id && $satusehatPegawai) {
            $satusehatPegawai->satusehat_pegawai_id = $satusehat_id;
            $satusehatPegawai->save();
        }

        $instalasiSatusehat = SatusehatInstalasi::find()->where(['satusehat_integration_id' => $integrationId])->one();
        if ($satusehat_id && $instalasiSatusehat) {
            $instalasiSatusehat->satusehat_instalasi_id = $satusehat_id;
            $instalasiSatusehat->save();
        }

        $ruanganSatusehat = SatusehatRuangan::find()->where(['satusehat_integration_id' => $integrationId])->one();
        if ($satusehat_id && $ruanganSatusehat) {
            $ruanganSatusehat->satusehat_ruangan_id = $satusehat_id;
            $ruanganSatusehat->save();
        }

        $pasienSatusehat = SatusehatPasien::find()->where(['satusehat_integration_id' => $integrationId])->one();
        if ($satusehat_id && $pasienSatusehat) {
            $pasienSatusehat->satusehat_pasien_id = $satusehat_id;
            $pasienSatusehat->save();
        }
    }
}