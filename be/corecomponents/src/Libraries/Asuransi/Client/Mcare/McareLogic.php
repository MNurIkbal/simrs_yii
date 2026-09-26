<?php

namespace Doco\Libraries\Asuransi\Client\Mcare;

/**
 * @author: [Maulana Muhammad Rizky]
 * A product of PT. Sirs
 * Powered by Sirs
 */

use Doco\Libraries\Asuransi\AsuransiResponseClient;
use Doco\Libraries\Asuransi\Client\Apln\Payload\ReferensiPayload;
use Doco\Libraries\Asuransi\Client\Asuransi;
use Doco\Libraries\Asuransi\Client\Mcare\Payload\CekEligiblePayload;
use Doco\Libraries\Asuransi\Client\Mcare\Payload\CetakStrukPendaftaranPayload;
use Doco\Libraries\Asuransi\Client\Mcare\Payload\CetakStrukPengesahanPayload;
use Doco\Libraries\Asuransi\Client\Mcare\Payload\CetakSuratJaminanPayload;
use Doco\Libraries\Asuransi\Client\Mcare\Payload\DaftarKunjunganPayload;
use Doco\Libraries\Asuransi\Client\Mcare\Payload\DeleteItemRequestPayload;
use Doco\Libraries\Asuransi\Client\Mcare\Payload\DischargingPayload;
use Doco\Libraries\Asuransi\Client\Mcare\Payload\ItemRequestPayload;
use Doco\Libraries\Asuransi\Client\Mcare\Payload\ListItemPayload;
use Doco\Libraries\Asuransi\Client\Mcare\Payload\PembatalanPayload;
use Doco\Libraries\Asuransi\Client\Mcare\Payload\PendaftaranPayload;
use Doco\Libraries\Asuransi\Client\Mcare\Payload\ReferensiBenefitPayload;
use Doco\Libraries\Asuransi\Client\Mcare\Payload\ReferensiPesertaPayload;
use Doco\Libraries\Asuransi\Client\Mcare\Payload\SisaLimitPayload;
use Doco\Libraries\Asuransi\Collection;
use Doco\Libraries\Asuransi\TaskInter\InsuranceInterface;
use Exception;
use GuzzleHttp\Client;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Integrate Insurance Core Logic.
 *
 * @author Maulana Muhammad Rizky
 */
class McareLogic extends Asuransi implements InsuranceInterface
{
    protected $propertyResponse;

    public function __construct()
    {
        parent::__construct();
        $this->propertyResponse = \Yii::$app->response->hasProperty('statusCode');
    }

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

            if (!$isCheckConnection) {

                $cacheToken = Yii::$app->cache->get($this->config->provider_code);

                if (! empty($cacheToken)) {
                    return [
                        'code' => 200,
                        'message' => 'Login success !',
                        'data' => $cacheToken
                    ];
                }
            }

            $client =  new Client([
                'base_uri' => $this->config->base_url,
                'headers' => [
                    "Content-Type" => "application/json"
                ]
            ]);

            $transfromPayload = (new ReferensiPayload($loginPayload))->toArray();

            $response = $client->request('POST', $loginPayload['path'], [
                'form_params' => $transfromPayload
            ]);

            $data = $response->getBody()->getContents();
            $data = json_decode($data, true);

            if (! empty($data)) {
                if (!$isCheckConnection) {
                    Yii::$app->cache->set($this->config->provider_code, $data['api_token'], isset($this->config->session_expired) ? $this->config->session_expired : 10);
                    Yii::$app->cache->set('kodeprovider-' . $this->config->provider_code, $data['kodeprovider'], isset($this->config->session_expired) ? $this->config->session_expired : 10);
                }

                $this->setLogs([
                    'response' => $data,
                    'payload' => $transfromPayload,
                    'rawresponse' => $data,
                    'state' => 'Login',
                    'path' => $loginPayload['path'],
                    'status' => $response->getStatusCode(),
                ]);
                if ($this->propertyResponse) {
                    \Yii::$app->response->statusCode = 200;
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
                'path' => $loginPayload['path'],
                'status' => $response->getStatusCode(),
            ]);

            if ($this->propertyResponse) {
                \Yii::$app->response->statusCode = 401;
            }
            return [
                'code' => 401,
                'message' => 'Login failed !'
            ];
        } catch (\Exception $th) {
            if ($this->propertyResponse) {
                \Yii::$app->response->statusCode = 500;
            }
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
    public function GetReferensi($payload = [])
    {
        return true;
    }

    /**
     * Checks if a peserta is eligible by sending a POST request to the '/api/V2/h2h' endpoint.
     *
     * @return array Eligibility data in JSON format
     */
    public function CekEligiblePeserta($data = [])
    {
        $client = $this->setUrl();
        $formParams = (new CekEligiblePayload($data))->toArray();

        $urlPayload = json_decode($this->config->url_cek_eligibilitas, true);

        $response = $client->request('POST', $urlPayload['path'], [
            'form_params' => $formParams
        ]);

        $data = $response->getBody()->getContents();
        $data = json_decode($data, true);

        $transResponse = AsuransiResponseClient::responseEligible($data);
        $responsePayload = [
            'data' => $transResponse,
            'message' => isset($data['Status']['messagestring']) ? $data['Status']['messagestring'] : '',
            'status' => isset($data['Status']['errornumber']) ? $data['Status']['errornumber'] : '',
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

    /**
     * Retrieves the benefit reference for a peserta by sending a POST request to the '/api/V2/h2h' endpoint.
     *
     * @return array Benefit reference data in JSON format
     */
    public function ReferensiBenefitPeserta($data = [])
    {
        $client = $this->setUrl();
        $formParams = (new ReferensiBenefitPayload($data))->toArray();

        $urlPayload = json_decode($this->config->url_referensi_benefit, true);

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
            'status' => isset($data['Status']['errornumber']) ? $data['Status']['errornumber'] : '',
        ];

        $this->setLogs([
            'payload' => $formParams,
            'response' => $responsePayload,
            'rawresponse' => $data,
            'state' => 'Referensi Benefit',
            'path' => $urlPayload['path'],
            'status' => $responsePayload['status'],
        ]);

        $responsePayload = AsuransiResponseClient::wrappingResponse($responsePayload);
        $collection = new Collection($responsePayload);

        return $collection->all();
    }

    /**
     * Retrieves the reference sub-benefit for a member.
     *
     * @return array The sub-benefit data in JSON format.
     */
    public function ReferensiSubBenefitPeserta()
    {
        $client = $this->setUrl();

        $response = $client->request('POST', '/api/V2/h2h', [
            'form_params' => [
                'service' => 3,
                'nokartu' => '09008000000001',
                'kodebenefit' => '00010'
            ]
        ]);

        $data = $response->getBody()->getContents();
        $data = json_decode($data, true);

        return $data;
    }

    /**
     * Retrieves the reference limit for a member.
     *
     * @return array data JSON format.
     */
    public function CekSisaLimitPeserta()
    {
        $client = $this->setUrl();
        $response = $client->request('POST', '/api/V2/h2h', [
            'form_params' => [
                'service' => 4,
                'nokartu' => '09008000000001',
                'kodebenefit' => '00010'
            ]
        ]);

        $data = $response->getBody()->getContents();
        $data = json_decode($data, true);

        return $data;
    }

    /**
     * Handles the registration process by sending a POST request to the '/api/V2/h2h' endpoint.
     *
     * @return array Registration data in JSON format
     */
    public function Pendaftaran($data = [])
    {
        $client = $this->setUrl();
        $formParams = (new PendaftaranPayload($data))->toArray();
        $urlPayload = json_decode($this->config->url_pendaftaran, true);

        try {
            $response = $client->request('POST', $urlPayload['path'], [
                'form_params' => $formParams,
            ]);

            $data = $response->getBody()->getContents();
            $data = json_decode($data, true);

            $transformResponse = AsuransiResponseClient::responsePendaftaran(ArrayHelper::getValue($data, 'Data.0'), ArrayHelper::getValue($data, 'LimitSubBenefit'));

            $responsePayload = [
                'data' => isset($transformResponse) ? $transformResponse : [],
                'message' => isset($data['Status']['messagestring']) ? $data['Status']['messagestring'] : null,
                'status' => isset($data['Status']['errornumber']) ? $data['Status']['errornumber'] : '',
            ];

            $this->setLogs([
                'payload' => $formParams,
                'response' => $responsePayload,
                'rawresponse' => $data,
                'state' => 'Pendaftaran',
                'path' => $urlPayload['path'],
                'status' => $responsePayload['status'],
            ]);

            $responsePayload = AsuransiResponseClient::wrappingResponse($responsePayload);
            $collection = new Collection($responsePayload);

            return $collection->all();
        } catch (\Exception $th) {
            $this->setLogs([
                'payload' => $formParams,
                'response' => null,
                'rawresponse' => $th->getMessage(),
                'state' => 'Pendaftaran',
                'path' => $urlPayload['path'],
                'status' => $th->getCode(),
            ]);

            throw new Exception($th->getMessage());
        }
    }

    /**
     * Sends a POST request to the '/api/V2/h2h' endpoint to discharge a claim.
     *
     * @return array The response data in JSON format.
     */
    public function Discharging($data = [])
    {
        $client = $this->setUrl();
        $formParams = (new DischargingPayload($data))->toArray();
        $urlPayload = json_decode($this->config->url_pengesahan, true);

        $response = $client->request('POST', $urlPayload['path'], [
            'form_params' => $formParams
        ]);

        $data = $response->getBody()->getContents();
        $data = json_decode($data, true);

        $data = isset($data['data']) ? $data['data'] : $data;
        $status = isset($data['status']) ? $data['status'] : '';
        $message = isset($data['message']) ? $data['message'] : null;

        $responsePayload = [
            'data' => $data,
            'message' => $message,
            'status' => $status,
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

    /**
     * Handles the cancellation process by sending a POST request to the '/api/V2/h2h' endpoint.
     *
     * @return array Cancellation data in JSON format
     */
    public function Pembatalan($data = [])
    {
        $client = $this->setUrl();
        $formParams = (new PembatalanPayload($data))->toArray();

        $response = $client->request('POST', '/api/V2/h2h', [
            'form_params' => $formParams
        ]);

        $data = $response->getBody()->getContents();
        $data = json_decode($data, true);

        $responsePayload = [
            'data' => isset($data['data']) ? $data['data'] : [],
            'message' => isset($data['message']) ? $data['message'] : null,
            'status' => isset($data['code']) ? $data['code'] : null,
        ];

        $this->setLogs([
            'payload' => $formParams,
            'response' => $responsePayload,
            'rawresponse' => $data,
            'state' => 'Pembatalan',
            'path' => '/api/V2/h2h',
            'status' => $responsePayload['status'],
        ]);

        $response = AsuransiResponseClient::wrappingResponse($responsePayload);
        $collection = new Collection($response);

        return $collection->all();
    }

    /**
     * Uploads a document for a claim by sending a POST request to the '/api/v1/konektor' endpoint.
     *
     * @return array The response data in JSON format.
     */
    public function UploadDokumenKlaim()
    {
        $client = $this->setUrl();
        $response = $client->request('POST', '/api/v1/konektor', [
            'form_params' => [
                "service" => 6,
                "noklaim" => "23011200000007",
                "file" => []
            ]
        ]);

        $data = $response->getBody()->getContents();
        $data = json_decode($data, true);

        return $data;
    }

    /**
     * Generates and returns the struk pendaftaran document for a claim.
     *
     * @param array $data The data required to generate the struk pendaftaran document.
     * @return array The struk pendaftaran document data in JSON format.
     */
    public function CetakStrukPendaftaran($data = [])
    {
        $client = $this->setUrl();
        $formParams = (new CetakStrukPendaftaranPayload($data))->toArray();

        $path = $this->directory . '/uploads/struk-pendaftaran.pdf';

        $response = $client->request('POST', '/api/V2/h2h', [
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
            'state' => 'Cetak Struk Pendaftaran',
            'path' => '/api/V2/h2h',
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
    public function CetakStrukPengesahan($data = [])
    {

        $client = $this->setUrl();
        $formParams = (new CetakStrukPengesahanPayload($data))->toArray();

        $path = $this->directory . '/uploads/struk-pengesahan.pdf';

        $response = $client->request('POST', '/api/download/pengesahaan', [
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
            'state' => 'Cetak Struk Pengesahan',
            'path' => '/api/download/pengesahaan',
            'status' => $response->getStatusCode(),
        ]);

        $collection = new Collection($responsePayload);

        return $collection->all();
    }

    /**
     * Prints a letter of guarantee by sending a POST request to the '/api/V2/h2h' endpoint.
     *
     * @return array The response data in JSON format
     */
    public function CetakSuratJaminan($data = [])
    {
        $client = $this->setUrl();
        $formParams = (new CetakSuratJaminanPayload($data))->toArray();

        $path = $this->directory . '/uploads/surat-jaminan.pdf';

        $response = $client->request('POST', '/api/V2/h2h', [
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
            'path' => '/api/V2/h2h',
            'status' => $response->getStatusCode(),
        ]);

        $collection = new Collection($responsePayload);

        return $collection->all();
    }

    /**
     * Monitors the billing by sending a POST request to the '/api/v1/konektor' endpoint.
     *
     * @return array The response data in JSON format
     */
    public function MonitoringTagihan()
    {
        $client = $this->setUrl();
        $response = $client->request('POST', '/api/v1/konektor', [
            'form_params' => [
                "service" => 19,
                "nomorinvoice" => "invoice-1"
            ]
        ]);

        $data = $response->getBody()->getContents();
        $data = json_decode($data, true);

        return $data;
    }

    public function KlaimPending()
    {
        return false;
    }

    /**
     * Registers a visit by sending a POST request to the '/api/V2/h2h' endpoint.
     *
     * @param none
     * @throws GuzzleHttp\Exception\RequestException if the request fails
     * @return array The response data in JSON format
     */
    public function DaftarKunjungan($data = [])
    {
        $client = $this->setUrl();
        $formParams = (new DaftarKunjunganPayload($data))->toArray();

        $response = $client->request('POST', '/api/V2/h2h', [
            'form_params' => $formParams
        ]);

        $data = $response->getBody()->getContents();
        $data = json_decode($data, true);

        $responsePayload = [
            'data' => isset($data['Data']) ? $data['Data'] : [],
            'message' => isset($data['Status']['messagestring']) ? $data['Status']['messagestring'] : null,
            'status' => isset($data['Status']['errornumber']) ? $data['Status']['errornumber'] : null,
        ];

        $this->setLogs([
            'payload' => $formParams,
            'response' => $responsePayload,
            'rawresponse' => $data,
            'state' => 'Daftar Kunjungan',
            'path' => '/api/V2/h2h',
            'status' => $response->getStatusCode(),
        ]);

        $response = AsuransiResponseClient::wrappingResponse($responsePayload);
        $collection = new Collection($response);

        return $collection->all();
    }

    /**
     * Creates a new billing by sending a POST request to the '/api/v1/konektor' endpoint.
     *
     * @param none
     * @throws GuzzleHttp\Exception\RequestException if the request fails
     * @return array The response data in JSON format
     */
    public function CreateTagihan()
    {
        $client = $this->setUrl();
        $response = $client->request('POST', '/api/v1/konektor', [
            'form_params' => [
                "service" => "15",
                "nomorbatch" => "", //dikosongkan bila ingin membuat tagihan baru
                "nomorinvoice" => "invoice-1",
                "tanggalinvoice" => "2023-12-28",
                "noklaim" => "23110600000153"
            ]
        ]);

        $data = $response->getBody()->getContents();
        $data = json_decode($data, true);

        return $data;
    }

    public function SisaLimit($data = [])
    {
        $client = $this->setUrl();
        $formParams = (new SisaLimitPayload($data))->toArray();

        $response = $client->request('POST', '/api/V2/h2h', [
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
            'state' => 'Sisa Limit',
            'path' => '/api/V2/h2h',
            'status' => $responsePayload['status'],
        ]);

        $response = AsuransiResponseClient::wrappingResponse($responsePayload);
        $collection = new Collection($response);

        return $collection->all();
    }

    public function ItemRequest($data = [])
    {
        $client = $this->setUrl();
        $providerCode = \Yii::$app->cache->get('kodeprovider-' . $this->config->provider_code);
        $data['provider_code'] = $providerCode;
        $formParams = (new ItemRequestPayload($data))->toArray();
        
        try {
            $response = $client->request('POST', '/api/V2/request-item', [
                'form_params' => $formParams
            ]);

            $data = $response->getBody()->getContents();
            $data = json_decode($data, true);

            $responsePayload = [
                'data' => isset($data['data']) ? $data['data'] : [],
                'message' => isset($data['message']) ? $data['message'] : '',
                'status' => isset($data['status']) ? $data['status'] : '',
                'kode_item' => isset($data['kode_item']) ? $data['kode_item'] : '',
            ];

            $this->setLogs([
                'payload' => $formParams,
                'response' => $responsePayload,
                'rawresponse' => $data,
                'state' => 'Item Request',
                'path' => '/api/V2/request-item',
                'status' => $responsePayload['status'],
            ]);

            $response = AsuransiResponseClient::wrappingResponse($responsePayload);
            $collection = new Collection($response);

            return $collection->all();
        } catch (\Exception $th) {
            $this->setLogs([
                'payload' => $formParams,
                'response' => $th->getMessage(),
                'rawresponse' => $th->getMessage(),
                'state' => 'Item Request',
                'path' => '/api/V2/request-item',
                'status' => $th->getCode(),
            ]);

            throw new Exception($th->getMessage());
        }
    }

    public function ListItemRequest($data = [])
    {
        $client = $this->setUrl();
        $formParams = (new ListItemPayload($data))->toArray();

        $response = $client->request('POST', '/api/V2/list-item', [
            'form_params' => $formParams
        ]);

        $data = $response->getBody()->getContents();
        $data = json_decode($data, true);

        $responsePayload = [
            'data' => isset($data['data']) ? $data['data'] : [],
            'message' => isset($data['Status']['messagestring']) ? $data['Status']['messagestring'] : '',
            'status' => isset($data['status']) ? $data['status'] : '',
        ];

        $this->setLogs([
            'payload' => $formParams,
            'response' => $responsePayload,
            'rawresponse' => $data,
            'state' => 'List Item Request',
            'path' => '/api/V2/list-item',
            'status' => $responsePayload['status'],
        ]);

        $response = AsuransiResponseClient::wrappingResponse($responsePayload);
        $collection = new Collection($response);

        return $collection->all();
    }

    public function DeleteItemRequest($data = [])
    {
        $client = $this->setUrl();
        $formParams = (new DeleteItemRequestPayload($data))->toArray();

        $response = $client->request('POST', '/api/V2/delete-item', [
            'form_params' => $formParams
        ]);

        $data = $response->getBody()->getContents();
        $data = json_decode($data, true);

        $responsePayload = [
            'data' => isset($data['data']) ? $data['data'] : [],
            'message' => isset($data['message']) ? $data['message'] : '',
            'status' => isset($data['status']) ? $data['status'] : '',
        ];

        $this->setLogs([
            'payload' => $formParams,
            'response' => $responsePayload,
            'rawresponse' => $data,
            'state' => 'Delete Item Request',
            'path' => '/api/V2/delete-item',
            'status' => $responsePayload['status'],
        ]);

        $response = AsuransiResponseClient::wrappingResponse($responsePayload);
        $collection = new Collection($response);

        return $collection->all();
    }

    public function UnbatchKlaim()
    {
        return false;
    }

    public function ReferensiKepesertaan($data = [])
    {
        $client = $this->setUrl();
        $formParams = (new ReferensiPesertaPayload($data))->toArray();

        $response = $client->request('POST', '/api/referensi-peserta', [
            'form_params' => $formParams
        ]);

        $data = $response->getBody()->getContents();
        $data = json_decode($data, true);

        $responsePayload = [
            'data' => isset($data['data']) ? $data['data'] : [],
            'message' => isset($data['message']) ? $data['message'] : '',
            'status' => isset($data['code']) ? $data['code'] : '',
        ];

        $this->setLogs([
            'response' => $responsePayload,
            'payload' => $formParams,
            'rawresponse' => $data,
            'state' => 'Referensi Peserta',
            'path' => '/api/referensi-peserta',
            'status' => $responsePayload['status'],
        ]);

        $response = AsuransiResponseClient::wrappingResponse($responsePayload);
        $collection = new Collection($response);

        return $collection->all();
    }


    public function setUrl()
    {
        $bearer = $this->setBearerToken();

        return new Client([
            'base_uri' => $this->config->base_url,
            'headers' => [
                "Content-Type" => "application/json",
                "Authorization" => $bearer
            ]
        ]);
    }

    public function setBearerToken()
    {
        $token = Yii::$app->cache->get($this->config->provider_code);
        if (empty($token)) {
            $token = $this->Authentication();
            $token = isset($token['data']['api_token']) ? $token['data']['api_token'] : null;
        }
        return 'Bearer ' . $token;
    }
}
