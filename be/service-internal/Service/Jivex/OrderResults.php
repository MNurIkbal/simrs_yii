<?php

namespace Integrasi\Service\Jivex;

use Yii;
use Integrasi\Components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

use yii\db\Query;

class OrderResults extends \Integrasi\Contracts\DocoImplement
{

    protected $keyConfig = 'serconn_ris';

    public function execute()
    {
        $params = Yii::$app->params['iniFile'];
        $baseConfig = isset($params[$this->keyConfig]) ? $params[$this->keyConfig] : [];
        $hasilPemeriksaan = $this->hasilpemeriksaanrad_id;
        $connection = Yii::$app->db;
        $queryPenunjang = $connection->createCommand("
            SELECT 
                * 
            FROM infopasienradiologi_v
            WHERE hasilpemeriksaanrad_id = :hasilpemeriksaanrad_id 
        ")->bindValue(':hasilpemeriksaanrad_id', $hasilPemeriksaan)->queryOne();

        $noRekamMedik = isset($queryPenunjang['no_rekam_medik']) ? $queryPenunjang['no_rekam_medik'] : null;

        $imageLink = isset($baseConfig['path_images']) 
                        ? $baseConfig['path_images'] . "&patientsID=" . $noRekamMedik : null;

        if (!empty($queryPenunjang)) {
            $no_masukpenunjang = isset($queryPenunjang['no_masukpenunjang']) ? $queryPenunjang['no_masukpenunjang'] : null;
            $tindakanpelayanan_id = isset($queryPenunjang['tindakanpelayanan_id']) ? $queryPenunjang['tindakanpelayanan_id'] : null;
            $data = [
                'data' => [
                    'Data' => [
                        'log' => null,
                        'message_time' => date('Y-m-d H:i:s'),
                        'control_id' => null,
                        'patient_id' => !empty($queryPenunjang['pasien_id']) ? $queryPenunjang['pasien_id'] : null,
                        'case_no' => !empty($queryPenunjang['no_pendaftaran']) ? $queryPenunjang['no_pendaftaran'] : null,
                        'order_no' => $no_masukpenunjang. "-" .$tindakanpelayanan_id,
                        'filler_order' => null,
                        'priority' => null,
                        'procedure_id' => !empty($queryPenunjang['daftartindakan_id']) ? $queryPenunjang['daftartindakan_id'] : null,
                        'procedure_code' => '-',
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
                        'service' => 'Jivex-OrderResults',
                        'payload' => $data,
                        'result' => $request,
                        'timestamp' => date('Y-m-d H:i:s'),
                    ]);
                }
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                if($e->hasResponse()) {
                    Yii::error($e);
                    $response = $e->getResponse();
                    return json_encode([
                        'service' => 'Jivex-OrderResults',
                        'payload' => $data,
                        'result' => $response->getBody(),
                        'timestamp' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

        }

    }
}