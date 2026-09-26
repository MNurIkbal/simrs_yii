<?php

namespace Integrasi\Service\Mhg;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\Services\MhgService;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoConstants;
use Integrasi\Components\DocoConstansId;
use Integrasi\Service\Mhg\Cache\Cache;
use Integrasi\Service\Mhg\Models\PendaftaranOlT;
use Integrasi\Service\Mhg\Models\PendaftaranT;
use Integrasi\Service\Mhg\Models\PendaftaranOl;
use Integrasi\Service\Mhg\Models\InfoPasienSudahBayarV;
use Integrasi\Service\Sirs\Models\PasienV;

class UpdateAppointment extends \Integrasi\Contracts\DocoImplement
{
    const STATUS_REGISTERED = "REGISTERED";
    const STATUS_DISCHARGED = "DISCHARGED";
    const STATUS_CANCELED   = "CANCELED";

    public function execute()
    {
        $payload = [];
        $result = [];
        $pendaftaran_id = isset($this->result['id']) ? DocoHelpers::decrypt($this->result['id']) : null;
        $pendaftaranol_id = isset($this->result['pendaftaranol_id']) ? $this->result['pendaftaranol_id'] : null;
        $pendaftaran_id = isset($this->result['pendaftaran_id']) ? $this->result['pendaftaran_id'] : $pendaftaran_id;
        $status_appoinment = '';
        $no_pembayaran = '';
        $total_tagihan = '';
        $no_pendaftaran = '';
        $norm = '';
        
        $state = "update";
        if (!empty($pendaftaran_id))
        {
            $pembayaran = InfoPasienSudahBayarV::find()
                ->andWhere([
                    'pendaftaran_id' => $pendaftaran_id
                ])
                ->one(); 

            if (!empty($pembayaran)){
                $status_appoinment = self::STATUS_DISCHARGED;
                $no_pembayaran = $pembayaran['no_pembayaran'];
                $total_tagihan = $pembayaran['total_tagihan'];
            } else {
                $status_appoinment = self::STATUS_REGISTERED;
            }
            
            if (!empty($pendaftaran_id)){
                $data = PendaftaranOlT::find()
                ->andWhere([
                    'pendaftaran_id' => $pendaftaran_id
                ])
                ->one();

                if (empty($data)){
                    $payload = [
                        'pendaftaran_id' => $pendaftaran_id
                    ];
                    $result = ['pendaftaran tersebut bukan pasien appoinment'];
                    return $this->setResponse($payload, $result);
                }
            } else {
                $data = PendaftaranOlT::find()
                ->andWhere([
                    'pendaftaranol_id' => $pendaftaranol_id
                ])
                ->one();
            }

            if (!empty($data->pendaftaran_id)){
                $pendaftaran = PendaftaranT::find()
                                ->andWhere([
                                    'pendaftaran_id' => $data->pendaftaran_id
                                ])
                                ->one();
                $no_pendaftaran = $pendaftaran->no_pendaftaran;
                if($pendaftaran->pasien_id) {
                    $norm = $this->getPatientMr($pendaftaran->pasien_id)->no_rekam_medik;
                }
                $payload = [
                    'pendaftaranol_id' => $data->pendaftaranol_id,
                    'status_daftar_ol' => $status_appoinment,
                    'bill_number' => $no_pembayaran,
                    'bill_amount' => $total_tagihan,
                    'no_pendaftaran' => $no_pendaftaran,   
                    'mrid' => $norm,                 
                ];
                $result = (new MhgService)->appointmentUpdate($payload);
            }
            $this->setLogs($result, $state, $payload);
        } else {
            $data = PendaftaranOlT::find()
            ->andWhere([
                'pendaftaranol_id' => $pendaftaranol_id
            ])
            ->one();
            $status_appoinment = self::STATUS_CANCELED;
            if($data->pasien_id) {
                $norm = $this->getPatientMr($data->pasien_id)->no_rekam_medik;
            }

            $payload = [
                'pendaftaranol_id' => $data->pendaftaranol_id,
                'status_daftar_ol' => $status_appoinment,
                'bill_number' => $no_pembayaran,
                'bill_amount' => $total_tagihan,
                'no_pendaftaran' => $no_pendaftaran,
                'mrid' => $norm,
            ];
            $result = (new MhgService)->appointmentUpdate($payload);
            $this->setLogs($result, $state, $payload);
        }
        
        return $this->setResponse($payload, $result);
        
    }

    public function setResponse($payload, $result)
    {
        return json_encode([
            'service' => 'Mhg-UpdateAppointment',
            'payload' => $payload,
            'timestamp' => date('Y-m-d H:i:s'),
            'attributes' => $this->attributes,
            'result' => $result
        ]);
    }

    public function setLogs($result, $state, $payload)
    {
        $response = isset($result['response']) ? $result['response'] : [];
        $userIdentity = $this->user_identity;
        $uidSercon = isset($result['uid']) ? $result['uid'] : null;

        // $logData[] = [
        //     'pendaftaranol_id' => $payload['pendaftaranol_id'],
        //     'state' => $state,
        //     'created_date' => date('Y-m-d H:i:s'),
        //     'created_by' => isset($userIdentity['uid']) ? $userIdentity['uid'] : null,
        //     'payload' => isset($response['payload']) ? json_encode($response['payload']) : null,
        //     'sync_respon' => isset($response['response']) ? json_encode($response['response']) : null,
        // ];

        $model = new PendaftaranOl;
        $model->pendaftaranol_id = $payload['pendaftaranol_id'];
        $model->state = $state;
        $model->created_date = date('Y-m-d H:i:s');
        $model->created_by = isset($userIdentity['uid']) ? $userIdentity['uid'] : null;
        $model->payload = isset($payload) ? json_encode($payload) : null;
        $model->sync_respon = isset($response) ? json_encode($response) : null;
        $model->id_sync_sercon = $uidSercon;
        $model->save();

        // $this->saveLogs($logData);
    }

    public function saveLogs($data)
    {
        return PendaftaranOl::batchInsert($data);
    }

    private function getPatientMr($pasienId)
    {
        $pasien = PasienV::find()
        ->select(['no_rekam_medik'])
        ->where([
            'pasien_id' => $pasienId
        ])
        ->one();

        return $pasien;
    }
}