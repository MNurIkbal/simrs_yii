<?php

namespace Doco\Services\Esign;

use app\modules\v1\models\DokumenSign;

use Doco\components\DocoConstansId;
use Doco\components\DocoConstants;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use Yii;

class TilakaService implements IEsignService
{
    const STATUS_VALIDATION = "VALIDATION";
    const STATUS_ACTIVATION = "ACTIVATION";
    const STATUS_ACTIVE = "ACTIVE";
    const STATUS_INACTIVE = "INACTIVE";
    const STATUS_REJECTED = "REJECTED";
    const STATUS_EXPIRED = "EXPIRED";
    const STATUS_REGISTRATION = "REGISTRATION";
    const STATUS_REENROLL = "REENROLL";
    const STATUS_OTHER = "OTHER";
    /**
     * This function is used to retrieve a token.
     * If there is no token in the cache system, it will return a new token.
     * Otherwise, it will return the active token currently in the cache.
     * The token's expiration is determined by subtracting 30 seconds from the original expiry time.
     *
     * @return string token
     * @throws Exception If an error occurs during integration
     */

    private static function refreshToken() {
        if(Yii::$app->cache->exists('esign-token')) {
            return Yii::$app->cache->get('esign-token');
        }

        $configEsign = (new DocoConstansId)->actionGetAdditional('konfig_esign');
        $configEsign = json_decode($configEsign,true);

        $client = new Client();
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];
        $options = [
            'form_params' => [
                'client_id' => $configEsign['credential']['client_id'],
                'grant_type' => 'client_credentials',
                'client_secret' => $configEsign['credential']['client_secret'],
            ]
        ];
        $request = new Request('POST', $configEsign['tilaka_base_url'].'auth', $headers);
        try {
            $response = $client->send($request, $options);
            if($response->getStatusCode() == 200) {
                $resBody = json_decode($response->getBody(), true);
                Yii::$app->cache->set('esign-token', $resBody['access_token'], $resBody['expires_in'] - 30);
                return $resBody['access_token'];
            } else {
                throw new \Exception("Get Token Esign Error");
            }
        } catch (ClientException $e) {
            throw new \Exception("Token Error");
        }
    }

    /**
     * @return string uuid from tilaka system
     * @throws Exception If an error occurs during integration
     */
    public static function generateUUID($params = []) {
        $token = static::refreshToken();
        $client = new Client();
        $headers = [
            'Authorization' => 'Bearer ' . $token
        ];

        $configEsign = (new DocoConstansId)->actionGetAdditional('konfig_esign');
        $configEsign = json_decode($configEsign,true);

        $request = new Request('POST', $configEsign['tilaka_base_url'].'generateUUID?'.http_build_query($params), $headers);
        try {
            $response = $client->send($request);
            if($response->getStatusCode() == 200) {
                $resBody = json_decode($response->getBody(), true);
                return reset($resBody['data']);
            } else {
                throw new \Exception("Generate UUID Error");
            }
        } catch (ClientException $e) {
            throw new \Exception("Generate UUID Error");
        }
    }

    public static function generateOwnUUID() {
        // Generate 16 bytes (128 bits) of random data
        $data = random_bytes(16);

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);

        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 36 character UUID
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public static function generateTnC($body, $configEsign) {
        $tnc_body = [];
        if(isset($configEsign['tnc'])) {
            $consent_timestamp = date('Y-m-d H:i:s');
            $hash_consent = hash_hmac('sha256', 
                $configEsign['credential']['client_id'] .
                $body['consent_text'] .
                $configEsign['tnc']['version'] .
                $consent_timestamp,
                $configEsign['credential']['client_secret']
            );

            $tnc_body = [
                'is_approved' => $configEsign['tnc']['is_approved'],
                'consent_text' => $body['consent_text'],
                'version' => $configEsign['tnc']['version'],
                'hash_consent' => $hash_consent,
                'consent_timestamp' => $consent_timestamp,
            ];
        }

        return $tnc_body;
    }

    public static function register($options) {
        $token = static::refreshToken();
        $client = new Client();
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ];

        $configEsign = (new DocoConstansId)->actionGetAdditional('konfig_esign');
        $configEsign = json_decode($configEsign,true);

        $paramsUUID = [];
        if(isset($configEsign['cert_type']) && $configEsign['cert_type'] == 'corp') {
            $paramsUUID['name'] = $options['name'];
            $paramsUUID['email'] = $options['email'];
        }
        $registration_id = static::generateUUID($paramsUUID);
        $tnc_body = static::generateTnC($options, $configEsign);

        $body = array_merge([
            'registration_id' => $registration_id,
            'date_expire' => date('Y-m-d H:i', strtotime(isset($configEsign['register']['expire_time']) ?
                $configEsign['register']['expire_time'] : '+3 days')),
        ], $options, $tnc_body);
        $request = new Request('POST', $configEsign['tilaka_base_url'] . 'registerForKycCheck', $headers, json_encode($body));
        try {
            $response = $client->send($request);
            if($response->getStatusCode() == 200) {
                $resBody = json_decode($response->getBody(), true);
                if($resBody['success']) {
                    return $body;
                } else {
                    throw new \Exception($resBody['message']);
                }
            } else {
                throw new \Exception("Register Error");
            }
        } catch (ClientException $e) {
            throw new \Exception("Register Error");
        }
    }

    public static function reenroll($options) {
        $token = static::refreshToken();
        $client = new Client();
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ];

        $configEsign = (new DocoConstansId)->actionGetAdditional('konfig_esign');
        $configEsign = json_decode($configEsign,true);
        
        $paramsUUID = [
            'request_type' => 're_enroll',
            'user_identifier'=> $options['tilaka_name'],
        ];
        unset($options['tilaka_name']);

        $registration_id = static::generateUUID($paramsUUID);
        $tnc_body = static::generateTnC($options, $configEsign);

        $body = array_merge([
            'registration_id' => $registration_id,
        ], $options, $tnc_body);

        $request = new Request('POST', $configEsign['tilaka_base_url'] . 'registerForKycCheck', $headers, json_encode($body));
        try {
            $response = $client->send($request);
            if($response->getStatusCode() == 200) {
                $resBody = json_decode($response->getBody(), true);
                if($resBody['success']) {
                    return $body;
                } else {
                    throw new \Exception($resBody['message']);
                }
            } else {
                throw new \Exception("Register Error");
            }
        } catch (ClientException $e) {
            throw new \Exception("Register Error");
        }
    }

    public static function revoke($options) {
        $token = static::refreshToken();
        $client = new Client();
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ];

        $configEsign = (new DocoConstansId)->actionGetAdditional('konfig_esign');
        $configEsign = json_decode($configEsign,true);

        $request = new Request('POST', $configEsign['tilaka_base_url'] . 'requestRevokeCertificate', $headers, json_encode($options));
        try {
            $response = $client->send($request);
            if($response->getStatusCode() == 200) {
                $resBody = json_decode($response->getBody(), true);
                if($resBody['success']) {
                    return $resBody['data'];
                } else {
                    throw new \Exception($resBody['message']);
                }
            } else {
                throw new \Exception("Revoke Error");
            }
        } catch (ClientException $e) {
            throw new \Exception("Revoke Error");
        }
    }

    public static function generateRegistrationWebview($registration_id, $redirect = null) {
        $configEsign = (new DocoConstansId)->actionGetAdditional('konfig_esign');
        $configEsign = json_decode($configEsign,true);

        return $configEsign['tilaka_base_url'] . 'personal-webview/guide?' . http_build_query([
            'request_id' => $registration_id,
            'redirect_url' => $redirect,
        ]);
    }

    public static function generateReEnrollWebview($registration_id, $redirect = null) {
        $configEsign = (new DocoConstansId)->actionGetAdditional('konfig_esign');
        $configEsign = json_decode($configEsign,true);

        return $configEsign['tilaka_base_url'] . 'personal-webview/kyc/re-enroll?' . http_build_query([
            'issue_id' => $registration_id,
            'redirect_url' => $redirect,
        ]);
    }

    public static function generateChangeMFAWebview($tilaka_name, $redirect = null) {
        $configEsign = (new DocoConstansId)->actionGetAdditional('konfig_esign');
        $configEsign = json_decode($configEsign,true);

        return $configEsign['tilaka_base_url'] . 'personal-webview/login?' . http_build_query([
            "setting" => 2,
            "tilaka_name" => $tilaka_name,
            "channel_id" => $configEsign['credential']['client_id'],
            "redirect_url" => $redirect,
        ]);
    }

    public static function generateActivationWebview($registration_id, $redirect = null) {
        $configEsign = (new DocoConstansId)->actionGetAdditional('konfig_esign');
        $configEsign = json_decode($configEsign,true);

        return $configEsign['tilaka_base_url'] . 'personal-webview/link-account?' . http_build_query([
            'setting' => 1,
            'channel_id' => $configEsign['credential']['client_id'],
            'request_id' => $registration_id,
            'redirect_url' => $redirect,
        ]);
    }

    public static function generateActivationReenrollWebview($issue_id, $redirect = null) {
        $configEsign = (new DocoConstansId)->actionGetAdditional('konfig_esign');
        $configEsign = json_decode($configEsign,true);

        return $configEsign['tilaka_base_url'] . 'personal-webview/link-account?' . http_build_query([
            'setting' => 1,
            'channel_id' => $configEsign['credential']['client_id'],
            'issue_id' => $issue_id,
            'redirect_url' => $redirect,
        ]);
    }

    public static function registrationResult($register_id) {
        $token = static::refreshToken();
        $client = new Client();
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ];

        $configEsign = (new DocoConstansId)->actionGetAdditional('konfig_esign');
        $configEsign = json_decode($configEsign,true);

        $body = [
            'register_id' => $register_id,
        ];

        $request = new Request('POST', $configEsign['tilaka_base_url'] . 'userregstatus', $headers, json_encode($body));
        try {
            $response = $client->send($request);
            if($response->getStatusCode() == 200) {
                $resBody = json_decode($response->getBody(), true);
                if($resBody['success']) {
                    return $resBody['data'];
                } else {
                    throw new \Exception($resBody['message']);
                }
            } else {
                throw new \Exception("Register Result Error");
            }
        } catch (ClientException $e) {
            throw new \Exception("Register Result Error");
        }
    }

    public static function certificateStatus($user_identifier) {
        $token = static::refreshToken();
        $client = new Client();
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ];

        $configEsign = (new DocoConstansId)->actionGetAdditional('konfig_esign');
        $configEsign = json_decode($configEsign,true);

        $body = [
            'user_identifier' => $user_identifier,
        ];

        $request = new Request('POST', $configEsign['tilaka_base_url'] . 'checkcertstatus', $headers, json_encode($body));
        try {
            $response = $client->send($request);
            if($response->getStatusCode() == 200) {
                $resBody = json_decode($response->getBody(), true);
                if($resBody['success']) {
                    return $resBody;
                } else {
                    throw new \Exception($resBody['message']);
                }
            } else {
                throw new \Exception("Cert Status Error");
            }
        } catch (ClientException $e) {
            throw new \Exception("Cert Status Error");
        }
    }

    /**
     * @param string filename
     * @param file contentFile
     * @return string new filename in minIO
     * @throws Exception If an error occurs during integration
     */
    public static function uploadFile($filename, $contentFile) {
        $token = static::refreshToken();
        $client = new Client();
        $headers = [
            'Authorization' => 'Bearer ' . $token
        ];
        $options = [
          'multipart' => [
            [
                'name' => 'file',
                'contents' => $contentFile,
                'filename' => $filename,
            ]
        ]];

        $configEsign = (new DocoConstansId)->actionGetAdditional('konfig_esign');
        $configEsign = json_decode($configEsign,true);

        $request = new Request('POST', $configEsign['lite_base_url'] . 'upload', $headers);
        $response = $client->send($request, $options);
        try {
            if($response->getStatusCode() == 200) {
                $resBody = json_decode($response->getBody(), true);
                return $resBody['filename'];
            } else {
                throw new \Exception("Upload File Error");
            }
        } catch (ClientException $e) {
            throw new \Exception("Upload File Error");
        }
    }

    /**
     * @param array options
     * @return array uuid and array of authantication's urls
     * @throws Exception If an error occurs during integration
     */
    public static function requestSign($options, $redirect = null) {
        $token = static::refreshToken();
        $client = new Client();
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ];

        $body = [
            "request_id" => null,
            "send_email" => true,
            "signatures" => [],
            "list_pdf" => [],
        ];
        $body = array_merge($body, $options);
        $body['request_id'] = $uuid = static::generateOwnUUID() . '-' . time();

        $configEsign = (new DocoConstansId)->actionGetAdditional('konfig_esign');
        $configEsign = json_decode($configEsign,true);

        $request = new Request('POST', $configEsign['lite_base_url'] . 'requestsign', $headers, json_encode($body));
        $response = $client->send($request);
        try {
            if($response->getStatusCode() == 200) {
                $resBody = json_decode($response->getBody(), true);
                if(!empty($redirect)) {
                    foreach ($resBody['auth_urls'] as $key => $value) {
                        $resBody['auth_urls'][$key]['url'] .= "&" . http_build_query(['redirect_url' => $redirect]);
                    }
                }
                return [
                    'uuid' => $uuid,
                    'auth_urls' => $resBody['auth_urls'],
                ];
            } else {
                throw new \Exception("Request Sign Error");
            }
        } catch (ClientException $e) {
            throw new \Exception("Request Sign Error");
        }
    }

    /**
     * @param string request_id
     * @param string user_identifier tilaka id
     * @return bool status execute sign
     * @throws Exception If an error occurs during integration
     */
    public static function executeSign($request_id, $user_identifier) {
        $token = static::refreshToken();
        $client = new Client();
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ];

        $body = [
            "request_id" => $request_id,
            "user_identifier" => $user_identifier,
        ];

        $configEsign = (new DocoConstansId)->actionGetAdditional('konfig_esign');
        $configEsign = json_decode($configEsign,true);

        $request = new Request('POST', $configEsign['lite_base_url'] . 'executesign', $headers, json_encode($body));
        try {
            $response = $client->send($request);
            if($response->getStatusCode() == 200) {
                $resBody = json_decode($response->getBody(), true);
                return $resBody['success'];
            } else {
                return false;
            }
        } catch (ClientException $e) {
            return false;
        }
    }

    /**
     * @param string request_id
     * @return array list of pdf signed
     * @throws Exception If an error occurs during integration
     */
    public static function checkSign($request_id) {
        $token = static::refreshToken();
        $client = new Client();
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ];

        $body = [
            "request_id" => $request_id,
        ];

        $configEsign = (new DocoConstansId)->actionGetAdditional('konfig_esign');
        $configEsign = json_decode($configEsign,true);

        $request = new Request('POST', $configEsign['lite_base_url'] . 'checksignstatus', $headers, json_encode($body));
        $response = $client->send($request);
        try {
            if($response->getStatusCode() == 200) {
                $resBody = json_decode($response->getBody(), true);
                return $resBody;
            } else {
                throw new \Exception("Check Sign Error");
            }
        } catch (ClientException $e) {
            throw new \Exception("Check Sign Error");
        }
    }

    public static function saveDoc($filename, $stream)
    {
        try {
            $newFileName = static::uploadFile($filename, $stream);
            return [
                'path' => '/',
                'newFilename' => $newFileName,
            ];
        } catch (\Exception $e) {
            Yii::error($e);
        }
    }

    public static function signing($listDokumenSign, $signer)
    {
        $signatures = [
            [
                "user_identifier" => $signer['useresign_id'],
                "signature_image" => 'data:' . $signer['tanda_tangan']['mime_type'] . ';base64,' . base64_encode($signer['tanda_tangan']['content']),
                "sequence" => 1
            ]
        ];

        $list_pdf = [];
        $list_id = [];
        $updatedData = [];
        foreach ($listDokumenSign as $dokumenSign) {
            $additional_data = $dokumenSign['additional_data'];
            if(empty($additional_data)) {
                if(!empty($dokumenSign['additional_konfig'])) {
                    $additional_konfig = json_decode($dokumenSign['additional_konfig'], true);
                    $additional_konfig = $additional_konfig['default'];
                    $imTtd = imagecreatefromstring($signer['tanda_tangan']['content']);
                    $width = imagesx($imTtd);
                    $height = imagesy($imTtd);
                    if(($additional_konfig['max-height'] == 0 ? $additional_konfig['max-width'] : ($additional_konfig['max-width'] / $additional_konfig['max-height'])) > ($width / $height)) {
                        $width = $additional_konfig['max-height'] * $width / $height;
                        $height = $additional_konfig['max-height'];
                    } else {
                        $height = $additional_konfig['max-width'] * $height / $width;
                        $width = $additional_konfig['max-width'];
                    }
                    $additional_data = [
                        "width" => $width,
                        "height" => $height,
                        "coordinate_x" => $additional_konfig['x'] - ($additional_konfig['pivot'][0] * $width / 2),
                        "coordinate_y" => $additional_konfig['y'] - ($additional_konfig['pivot'][1] * $height / 2),
                        "page_number" => $additional_konfig['page_number'] == 'last' ? static::getPageCount($dokumenSign['filename']) : $additional_konfig['page_number'],
                    ];
                } else {
                    // default
                    $additional_data = [
                        "width" => 1,
                        "height" => 1,
                        "coordinate_x" => 0,
                        "coordinate_y" => 0,
                        "page_number" => 1,
                    ];
                }
            } else if(!is_array($additional_data)) {
                $additional_data = json_decode($additional_data, true);
            }

            $list_id[] = $dokumenSign['dokumen_sign_id'];
            $list_pdf[] = $pdf = [
                "filename" => $dokumenSign['filename'],
                "signatures" => [
                    [
                        "user_identifier" => $signer['useresign_id'],
                        "width" => $additional_data["width"],
                        "height" => $additional_data["height"],
                        "coordinate_x" => $additional_data["coordinate_x"],
                        "coordinate_y" => $additional_data["coordinate_y"],
                        "page_number" => $additional_data["page_number"],
                    ]
                ]
            ];
            $updatedData[] = [
                'user_esign_id' => $signer['useresign_id'],
                'dokumen_sign_id' => $dokumenSign['dokumen_sign_id'],
                'pdf_payload_sended' => $pdf,
                'additional_data' => $additional_data,
            ];
        }
        $options = [
            "signatures" => $signatures,
            "list_pdf" => $list_pdf,
        ];
        $response = TilakaService::requestSign($options);
        $getDefaultData = DokumenSign::getDefaultData();
        foreach ($updatedData as $data) {
            $additional_data = $data['additional_data'];
            $additional_data['pdf_payload_sended'] = $data['pdf_payload_sended'];
            DokumenSign::updateAll([
                'sign_provider_id' => $response['uuid'],
                'doc_status' => DocoConstants::ESIGN_STAT_SIGNING_TK,
                'additional_data' => json_encode($additional_data),
                'last_modified_date' => $getDefaultData->date,
                'last_modified_by' => $getDefaultData->by,
            ], [
                'doc_status' => DocoConstants::ESIGN_STAT_GENERATED,
                'dokumen_sign_id' => $data['dokumen_sign_id'],
            ]);
        }
        return $response['auth_urls'];
    }

    private static function getPageCount($filename){
        $url = Yii::$app->minio->getPresignedUrl($filename, '+10 minutes');
        $temp_filename = "./uploads/tmp_" . $filename;
        $data = file_get_contents($url);
        file_put_contents($temp_filename, $data);
        $pdf = new \Mpdf\Mpdf([
            'tempDir' => dirname(__DIR__) . '/../temp/',
        ]);
        $pageNum = $pdf->SetSourceFile($temp_filename);

        unlink($temp_filename);
        return $pageNum;
    }

    public static function getCurrentStatus($additional_esign_data) {
        $expired_date = isset($additional_esign_data['registration_data']['date_expire']) ?
            strtotime($additional_esign_data['registration_data']['date_expire']) : 0;
        $reg_status = isset($additional_esign_data['registration_result']['status']) ?
            $additional_esign_data['registration_result']['status'] : null;
        $manual_reg_status = isset($additional_esign_data['registration_result']['manual_registration_status']) ?
            $additional_esign_data['registration_result']['manual_registration_status'] : null;
        $cert_status = isset($additional_esign_data['cert_status']['status']) ? $additional_esign_data['cert_status']['status'] : -1;
        $last_cert = isset($additional_esign_data['cert_status']['data']) ? $additional_esign_data['cert_status']['data'][0]  : [];

        switch ($cert_status) {
            case 0 :
                if(isset($additional_esign_data['registration_data']['done_reenroll']) && !empty($last_cert)) {
                    if($additional_esign_data['registration_data']['done_reenroll']) {
                        return TilakaService::STATUS_VALIDATION;
                    } else {
                        return TilakaService::STATUS_REENROLL;
                    }
                }
                return TilakaService::STATUS_INACTIVE;
            case 1 :
                if(isset($additional_esign_data['registration_data']['done_reenroll']) && !empty($last_cert)) {
                    if(!$additional_esign_data['registration_data']['done_reenroll']) {
                        return TilakaService::STATUS_REENROLL;
                    }
                }
                return TilakaService::STATUS_VALIDATION;
            case 2 :
                return TilakaService::STATUS_ACTIVATION;
            case 3 :
                if(isset($last_cert['expiry_date']) && strtotime($last_cert['expiry_date']) < time()) {
                    return TilakaService::STATUS_INACTIVE;
                }
                return TilakaService::STATUS_ACTIVE;
            case 4 :
                if(isset($additional_esign_data['registration_data']['done_reenroll']) && !empty($last_cert)) {
                    if(!$additional_esign_data['registration_data']['done_reenroll']) {
                        return TilakaService::STATUS_REENROLL;
                    }
                }
                return TilakaService::STATUS_REJECTED;
        }
        if($reg_status == 'S' || $manual_reg_status == 'S') {
            return TilakaService::STATUS_VALIDATION;
        } else if(in_array($reg_status, ['B', 'D']) || in_array($manual_reg_status, ['P', 'I']) || $reg_status == null) {
            if(time() > $expired_date) {
                return TilakaService::STATUS_EXPIRED;
            } else {
                return TilakaService::STATUS_REGISTRATION;
            }
        } else if($manual_reg_status == 'E') {
            return TilakaService::STATUS_EXPIRED;
        } else if($manual_reg_status == 'V') {
            return TilakaService::STATUS_VALIDATION;
        } else {
            return TilakaService::STATUS_OTHER;
        }
    }
}
