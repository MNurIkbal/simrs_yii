<?php

namespace Integrasi\Service\InaBroker;

use Doco\models\HasilBridgingRad;
use Yii;
use yii\helpers\ArrayHelper;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoConstants;
use Integrasi\Components\DocoConstansId;
use Integrasi\Components\Services\RadiologiService;
use Integrasi\Service\Sirs\Models\TindakanPelayanan;
use Integrasi\Service\Sirs\Models\Pendaftaran;
use Integrasi\Service\Sirs\Models\DaftarTindakan;
use Integrasi\Service\Sirs\Cache\Cache;

class CreateHasilBridging extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        usleep(10000);
        $primaryId = $this->tindakan_id;
        $pendaftaran = Pendaftaran::find()->select(['no_pendaftaran','pasien_id'])->where(['pendaftaran_id'=> $this->pendaftaran_id])->one();
        $tindakan = DaftarTindakan::find()->select(['daftartindakan_kode'])->where(['daftartindakan_id' => $this->daftartindakan_id])->one();
        $lastImageLink = HasilBridgingRad::find()->select(['image_link'])
        // ->where(['like', 'order_no', '-'.$primaryId])
        ->where("order_no = concat('".$this->no_masukpenunjang."','-',".$this->tindakan_id.")")
        ->asArray()->one();
        $payload = [
            'accession_number' => $primaryId,               
        ];
        
        /**
         * Validasi apabila image link sudah ada.
         * Agar tidak tergenerate 2 kali karena jadi issue.
         */
        // if (! empty($lastImageLink)) {
        //     $cekImage = ArrayHelper::getValue($lastImageLink, 'image_link');
        //     if (! empty($cekImage)) {
        //         return json_encode([
        //             'service' => 'InaBroker-CreateHasilBridging',
        //             'response' => $lastImageLink,
        //             'message' => "Image link sudah tersedia",
        //             'timestamp' => date('Y-m-d H:i:s'),
        //         ]);
        //     }
        // }

        $response = (new RadiologiService)->openViewerRadiologi($payload, function ($data) {
                    return $data;
        }, 'POST');
        $imageLink = isset($response['Results'][0]['data']['data']['url']) ? $response['Results'][0]['data']['data']['url'] : null;
        
        $data = [
            'data' => [
                'Data' => [
                    'log' => null,
                    'message_time' => date('Y-m-d H:i:s'),
                    'control_id' => null,
                    'patient_id' => $pendaftaran->pasien_id,
                    'case_no' => $pendaftaran->no_pendaftaran,
                    'order_no' => $this->no_masukpenunjang. "-" .$this->tindakan_id,
                    'filler_order' => null,
                    'priority' => null,
                    'procedure_id' => $this->daftartindakan_id,
                    'procedure_code' => $tindakan->daftartindakan_kode,
                    'procedure_name' => null,
                    'observation_time' => date('Y-m-d H:i:s'),
                    'clinical_info' => null,
                    'result_status' => false,
                    'principal_itpr' => null,
                    'assistant_itpr' => null,
                    'transcriptionist' => null,
                    'value_type' => null,
                    'obv_id' => null,
                    'obv_value' => null,
                    'obv_value_text' => null,
                    'obv_status' => false,
                    'abnormal_flags' => null,
                    'image_link' => $imageLink,
                ]
            ]
        ];

        try {
            if(!empty($imageLink)){
                $request = Yii::$app->docoRest->radiologi->post('integrasi/save-bridging',['form_params'=>$data]);
                $request = json_decode($request->getBody(),true);
                return json_encode([
                    'service' => 'InaBroker-CreateHasilBridging',
                    'payload' => $data,
                    'result' => $request,
                    'timestamp' => date('Y-m-d H:i:s'),
                ]);
            }else{
                return json_encode([
                    'service' => 'InaBroker-CreateHasilBridging',
                    'response' => @$response,
                    'timestamp' => date('Y-m-d H:i:s'),
                ]);
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            if($e->hasResponse()) {
                Yii::error($e);
                $response = $e->getResponse();
                return json_encode([
                    'service' => 'InaBroker-CreateHasilBridging',
                    'payload' => $data,
                    'result' => $response->getBody(),
                    'timestamp' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }
}