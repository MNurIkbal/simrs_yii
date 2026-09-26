<?php

namespace Doco\Libraries\Asuransi\Client\Apln;

/**
 * @author: [Maulana Muhammad Rizky]
 * A product of PT. Sirs
 * Powered by Sirs
 */

use Doco\Libraries\Asuransi\AsuransiResponseClient;
use Doco\Libraries\Asuransi\Client\Apln\Payload\AuthPayload;
use Doco\Libraries\Asuransi\Client\Apln\Payload\CekEligiblePayload;
use Doco\Libraries\Asuransi\Client\Apln\Payload\CetakStrukPendaftaranPayload;
use Doco\Libraries\Asuransi\Client\Apln\Payload\CetakSuratJaminanPayload;
use Doco\Libraries\Asuransi\Client\Apln\Payload\DaftarKunjunganPayload;
use Doco\Libraries\Asuransi\Client\Apln\Payload\DischargingPayload;
use Doco\Libraries\Asuransi\Client\Apln\Payload\PembatalanPayload;
use Doco\Libraries\Asuransi\Client\Apln\Payload\PendaftaranPayload;
use Doco\Libraries\Asuransi\Client\Apln\Payload\ReferensiBenefitPayload;
use Doco\Libraries\Asuransi\Client\Asuransi;
use Doco\Libraries\Asuransi\Collection;
use Doco\Libraries\Asuransi\TaskInter\InsuranceInterface;
use GuzzleHttp\Client;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * Integrate Insurance Core Logic.
 *
 * @author Maulana Muhammad Rizky
 */
class AplnLogic extends Asuransi implements InsuranceInterface
{
    /**
     * Authenticate with the API and retrieve a token.
     * 
     * This function checks the cache for an existing token, and if not found, 
     * it attempts to login to the API using the provided credentials.
     * 
     * @throws \Exception if an error occurs during the authentication process
     * @return array containing the authentication result and token data
     */
    public function Authentication($isCheckConnection = false)
    {
        $loginPayload = json_decode($this->config->auth, true);

        try {
            $client =  new Client([
                'base_uri' => $this->config->base_url,
                'headers' => [
                    "Content-Type" => "application/json"
                ]
            ]);

            $transfromPayload = (new AuthPayload($loginPayload))->toArray();

            $response = $client->request('POST', $loginPayload['path'], [
                'headers' => [
                    'header-token' => isset($loginPayload['header']['token']) ? $loginPayload['header']['token'] : '',
                    'request-date' => date('Ymd'),
                ],
                'form_params' => $transfromPayload
            ]);

            $data = $response->getBody()->getContents();
            $data = json_decode($data, true);

            if (! empty($data)) {

                $this->setLogs([
                    'payload' => $transfromPayload,
                    'response' => $data,
                    'rawresponse' => $data,
                    'state' => 'Login',
                    'path' => $loginPayload['path'],
                    'status' => $response->getStatusCode(),
                ]);

                if ($data['errornumber'] === 999) {
                    \Yii::$app->response->statusCode = 401;
                    return [
                        'code' => 401,
                        'message' => $data['messagestring'],
                        'data' => null,
                    ];
                }

                $tokenResponse = [
                    'userid' => $data['userid'],
                    'usertoken' => $data['usertoken'],
                    'header-token' => $data['header-token'],
                    'kodeprovider' => $data['kodeprovider'],
                ];

                if (! $isCheckConnection) {
                    Yii::$app->cache->set($this->config->provider_code, $tokenResponse, isset($this->config->session_expired) ? $this->config->session_expired : 5);
                }

                return [
                    'code' => 200,
                    'message' => 'Login success !',
                    'data' => $data,
                ];
            }

            $this->setLogs([
                'response' => 'Login failed !',
                'payload' => $transfromPayload,
                'rawresponse' => $data,
                'state' => 'Login',
                'path' => $loginPayload['path']
            ]);

            \Yii::$app->response->statusCode = 401;
            return [
                'code' => 401,
                'message' => 'Login failed !'
            ];
        } catch (\Exception $th) {
            \Yii::$app->response->statusCode = 500;
            return [
                'code' => 500,
                'message' => $th->getMessage()
            ];
        }
    }

    /**
     * Retrieves referensi data by sending a POST request to the '/api/V2/h2h' endpoint.
     *
     * @return array Referensi data in JSON format
     */
    public function GetReferensi()
    {
        return true;
    }

    public function CekSisaLimitPeserta()
    {
        return false;
    }

    /**
     * Checks if a peserta is eligible by sending a POST request to the '/api/V2/h2h' endpoint.
     *
     * @return array Eligibility data in JSON format
     */
    public function CekEligiblePeserta($data = [])
    {
        $client = $this->setUrl();
        $authPayload = $this->mappingResponseAuth();
        $transfromPayload = (new CekEligiblePayload($data))->toArray();
        $urlPayload = json_decode($this->config->url_cek_eligibilitas, true);

        $formParams = array_merge($transfromPayload, $authPayload);
        $response = $client->request('POST', $urlPayload['path'], [
            'form_params' => $formParams
        ]);

        $data = $response->getBody()->getContents();
        $data = json_decode($data, true);

        $transResponse = AsuransiResponseClient::responseEligible($data);
        $responsePayload = [
            'status' => isset($data['Status']['errornumber']) ? $data['Status']['errornumber'] : [],
            'data' => $transResponse,
            'message' => isset($data['Status']['messagestring']) ? $data['Status']['messagestring'] : '',
        ];

        $this->setLogs([
            'response' => $responsePayload,
            'payload' => $formParams,
            'rawresponse' => $data,
            'state' => 'Eligible',
            'path' => $urlPayload['path'],
            'status' => $responsePayload['status'],
        ]);

        $response = AsuransiResponseClient::wrappingResponse($responsePayload);
        $collection = new Collection($response);

        return $collection->all();
    }

    public function CetakStrukPendaftaran($data = [])
    {
        $client = $this->setUrl();
        $authPayload = $this->mappingResponseAuth();
        $transfromPayload = (new CetakStrukPendaftaranPayload($data))->toArray();

        $formParams = array_merge($transfromPayload, $authPayload);

        $path = $this->directory . '/uploads/struk-pendaftaran.pdf';

        $response = $client->request('POST', '/bridging-service/api/ProviderOnline/StrukPendaftaran', [
            'form_params' => $formParams,
            'save_to' => $path
        ]);

        $data = $response->getBody()->getContents();
        $data = json_decode($data, true);

        $responsePayload = [
            'path' => $path,
            'message' => 'File download success !',
        ];

        $this->setLogs([
            'payload' => $formParams,
            'response' => $responsePayload,
            'rawresponse' => $data,
            'state' => 'Cetak Surat Jaminan',
            'path' => '/bridging-service/api/ProviderOnline/StrukPendaftaran',
            'status' => $response->getStatusCode(),
        ]);

        $collection = new Collection($responsePayload);
        return $collection->all();
    }

    /**
     * Downloads and returns the pengesahan document for a claim.
     *
     * @return array The pengesahan document data in JSON format.
     */
    public function CetakStrukPengesahan()
    {
        return false;
    }

    public function CetakSuratJaminan($data = [])
    {
        $client = $this->setUrl();
        $authPayload = $this->mappingResponseAuth();
        $transfromPayload = (new CetakSuratJaminanPayload($data))->toArray();

        $formParams = array_merge($transfromPayload, $authPayload);

        $path = $this->directory . '/uploads/surat-jaminan.pdf';

        $response = $client->request('POST', '/bridging-service/api/ProviderOnline/FormSuratJaminan', [
            'form_params' => $formParams,
            'save_to' => $path
        ]);

        $data = $response->getBody()->getContents();
        $data = json_decode($data, true);

        $responsePayload = [
            'path' => $path,
            'message' => 'File download success !',
        ];

        $this->setLogs([
            'payload' => $formParams,
            'response' => $responsePayload,
            'rawresponse' => $data,
            'state' => 'Cetak Surat Jaminan',
            'path' => '/bridging-service/api/ProviderOnline/FormSuratJaminan',
            'status' => $response->getStatusCode(),
        ]);

        $collection = new Collection($responsePayload);
        return $collection->all();
    }

    public function CreateTagihan()
    {
        return false;
    }

    public function DaftarKunjungan($data = [])
    {
        $client = $this->setUrl();
        $authPayload = $this->mappingResponseAuth();
        $transfromPayload = (new DaftarKunjunganPayload($data))->toArray();

        $formParams = array_merge($transfromPayload, $authPayload);
        $response = $client->request('POST', '/bridging-service/api/ProviderOnline/DaftarKunjungan', [
            'form_params' => $formParams
        ]);

        $data = $response->getBody()->getContents();
        $data = json_decode($data, true);

        $responsePayload = [
            'data' => isset($data['Data']) ? $data['Data'] : [],
            'message' => isset($data['Status']['messagestring']) ? $data['Status']['messagestring'] : '',
            'status' => isset($data['Status']['errornumber']) ? $data['Status']['errornumber'] : '',
        ];

        $this->setLogs([
            'payload' => $formParams,
            'response' => $responsePayload,
            'rawresponse' => $data,
            'state' => 'Daftar Kunjungan',
            'path' => '/bridging-service/api/ProviderOnline/DaftarKunjungan',
            'status' => $responsePayload['status'],
        ]);


        $response = AsuransiResponseClient::wrappingResponse($responsePayload);
        $collection = new Collection($response);

        return $collection->all();
    }

    /**
     * Sends a POST request to the '/api/V2/h2h' endpoint to discharge a claim.
     *
     * @return array The response data in JSON format.
     */
    public function Discharging($data = [])
    {
        $client = $this->setUrl();
        $authPayload = $this->mappingResponseAuth();
        $transfromPayload = (new DischargingPayload($data))->toArray();
        $urlPayload = json_decode($this->config->url_pengesahan, true);

        $formParams = array_merge($transfromPayload, $authPayload);
        $response = $client->request('POST', $urlPayload['path'], [
            'form_params' => $formParams
        ]);

        $data = $response->getBody()->getContents();
        $data = json_decode($data, true);

        $responsePayload = [
            'data' => isset($data['Data']) ? $data['Data'] : [],
            'biaya' => isset($data['Biaya']) ? $data['Biaya'] : [],
            'message' => isset($data['Status']['messagestring']) ? $data['Status']['messagestring'] : '',
            'status' => isset($data['Status']['errornumber']) ? $data['Status']['errornumber'] : '',
        ];

        $this->setLogs([
            'payload' => $formParams,
            'response' => $responsePayload,
            'rawresponse' => $data,
            'state' => 'Pengesahan',
            'path' => $urlPayload['path'],
            'status' => $responsePayload['status'],
        ]);

        $response = AsuransiResponseClient::wrappingResponse($responsePayload);
        $collection = new Collection($response);

        return $collection->all();
    }

    public function KlaimPending()
    {
        return false;
    }

    public function MonitoringTagihan()
    {
        return false;
    }

    /**
     * Handles the registration process by sending a POST request to the '/api/V2/h2h' endpoint.
     *
     * @return array Registration data in JSON format
     */
    public function Pendaftaran($payload = [])
    {
        $client = $this->setUrl();
        $authPayload = $this->mappingResponseAuth();
        $transfromPayload = (new PendaftaranPayload($payload))->toArray();
        $formParams = array_merge($transfromPayload, $authPayload);

        $urlPayload = json_decode($this->config->url_pendaftaran, true);

        $response = $client->request('POST', $urlPayload['path'], [
            'form_params' => $formParams
        ]);

        $data = $response->getBody()->getContents();
        $response = json_decode($data, true);

        $transformResponse = AsuransiResponseClient::responsePendaftaran(ArrayHelper::getValue($response, 'Data.0'), ArrayHelper::getValue($response, 'LimitSubBenefit'));

        $responsePayload = [
            'data' => isset($transformResponse) ? $transformResponse : [],
            'message' => isset($response['Status']['messagestring']) ? $response['Status']['messagestring'] : '',
            'status' => isset($response['Status']['errornumber']) ? $response['Status']['errornumber'] : [],
        ];

        $this->setLogs([
            'payload' => $formParams,
            'response' => $responsePayload,
            'rawresponse' => $response,
            'state' => 'Pendaftaran',
            'path' => $urlPayload['path'],
            'status' => $responsePayload['status'],
        ]);

        $response = AsuransiResponseClient::wrappingResponse($responsePayload);
        $collection = new Collection($response);

        return $collection->all();
    }

    /**
     * Retrieves the benefit reference for a peserta by sending a POST request to the '/api/V2/h2h' endpoint.
     *
     * @return array Benefit reference data in JSON format
     */
    public function ReferensiBenefitPeserta($data = [])
    {
        $client = $this->setUrl();
        $authPayload = $this->mappingResponseAuth();
        $transfromPayload = (new ReferensiBenefitPayload($data))->toArray();
        $urlPayload = json_decode($this->config->url_referensi_benefit, true);

        $formParams = array_merge($transfromPayload, $authPayload);
        $response = $client->request('POST', $urlPayload['path'], [
            'form_params' => $formParams
        ]);

        $data = $response->getBody()->getContents();
        $data = json_decode($data, true);

        $dataBenefit = [];

        /**
         * Transform.
         */
        if (isset($data['Data'])) {
            foreach ($data['Data'] as $value) {
                $dataBenefit[] = AsuransiResponseClient::responseBenefit($value);
            }
        }

        $responsePayload = [
            'data' => isset($dataBenefit) ? $dataBenefit : [],
            'message' => isset($data['Status']['messagestring']) ? $data['Status']['messagestring'] : '',
            'status' => isset($data['Status']['errornumber']) ? $data['Status']['errornumber'] : [],
        ];

        $this->setLogs([
            'payload' => $formParams,
            'response' => $responsePayload,
            'rawresponse' => $data,
            'state' => 'Referensi Benefit',
            'path' => $urlPayload['path'],
            'status' => $responsePayload['status'],
        ]);

        $response = AsuransiResponseClient::wrappingResponse($responsePayload);
        $collection = new Collection($response);

        return $collection->all();
    }

    /**
     * Retrieves the reference sub-benefit for a member.
     *
     * @return array The sub-benefit data in JSON format.
     */
    public function ReferensiSubBenefitPeserta()
    {
        return false;
    }

    /**
     * Handles the cancellation process by sending a POST request to the '/api/V2/h2h' endpoint.
     *
     * @return array Cancellation data in JSON format
     */
    public function Pembatalan($data = [])
    {
        $client = $this->setUrl();
        $authPayload = $this->mappingResponseAuth();
        $transfromPayload = (new PembatalanPayload($data))->toArray();

        $formParams = array_merge($transfromPayload, $authPayload);
        $response = $client->request('POST', '/bridging-service/api/ProviderOnline/Pembatalan', [
            'form_params' => $formParams
        ]);

        $data = $response->getBody()->getContents();
        $data = json_decode($data, true);

        $responsePayload = [
            'data' => isset($data['Data']) ? $data['Data'] : [],
            'message' => isset($data['Status']['messagestring']) ? $data['Status']['messagestring'] : '',
            'status' => isset($data['Status']['errornumber']) ? $data['Status']['errornumber'] : [],
        ];

        $this->setLogs([
            'payload' => $formParams,
            'response' => $responsePayload,
            'rawresponse' => $data,
            'state' => 'Pembatalan',
            'path' => '/bridging-service/api/ProviderOnline/Pembatalan',
            'status' => $responsePayload['status'],
        ]);

        $response = AsuransiResponseClient::wrappingResponse($responsePayload);
        $collection = new Collection($response);

        return $collection->all();
    }

    public function ItemRequest()
    {
        return false;
    }

    public function ListItemRequest()
    {
        return false;
    }
    
    public function DeleteItemRequest()
    {
        return false;
    }    

    public function UploadDokumenKlaim()
    {
        return false;
    }

    public function UnbatchKlaim()
    {
        return false;
    }

    public function setUrl()
    {
        $this->setBearerToken();
        $header = $this->getResponseToken();

        return new Client([
            'base_uri' => $this->config->base_url,
            'headers' => [
                "Content-Type" => "application/json",
                'header-token' => isset($header['header-token']) ? $header['header-token'] : '',
                'request-date' => date('ymd'),
            ]
        ]);
    }

    public function setBearerToken()
    {
        $this->Authentication();
    }

    public function getResponseToken()
    {
        $response = Yii::$app->cache->get($this->config->provider_code);
        if (empty($response)) {
            return [];
        }
        return $response;
    }

    public function mappingResponseAuth()
    {
        $responseToken = $this->getResponseToken();
        return [
            "kodeprovider" => isset($responseToken['kodeprovider']) ? $responseToken['kodeprovider'] : '',
            "p_user_no" => isset($responseToken['userid']) ? $responseToken['userid'] : '',
            "p_token" => isset($responseToken['usertoken']) ? $responseToken['usertoken'] : '',
            'usertoken' => isset($responseToken['usertoken']) ? $responseToken['usertoken'] : '',
            'userid' => isset($responseToken['userid']) ? $responseToken['userid'] : '',
        ];
    }
}
