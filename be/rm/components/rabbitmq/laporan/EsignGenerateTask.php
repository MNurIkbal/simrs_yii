<?php

namespace app\components\rabbitmq\laporan;

use app\modules\v1\models\DokumenSign;

use Doco\components\DocoHelpers;
use Doco\components\DocoConstansId;
use Doco\components\DocoConstants;

use Doco\Notifications\RmNotification;

use Doco\rabbitmq\task\BaseTask;
use Doco\rabbitmq\RabbitBgProcess;

use Doco\Services\Esign\TilakaService;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

use Yii;

class EsignGenerateTask extends BaseTask
{
    // private $rmRest;
    // function __construct() {
    //     $this->rmRest = Yii::$app->docoRest->rm;
    // }


    public function processFlow($parameters)
    {
        $val = $parameters['data'];
        $doc = $parameters['doc'];
        $doc_name = $parameters['doc_name'];

        $keys = [
            'type' => trim($doc['additional_data']['type']),
            'transaksi_id' => $val[$doc_name.'_id'],
            'pendaftaran_id' => $val['pendaftaran_id'],
            'konfig_dokumen_id' => $doc['konfig_dokumen_id'],
        ];
        $hash = hash('sha256', json_encode($keys));
        if(Yii::$app->cache->exists("esign-generated-".$hash)) {
            Yii::error([
                'message' => 'kena cache',
                "value" => $hash,
            ]);
            return;
        }
        
        $existDoc = DokumenSign::find()->where([
            'type' => trim($doc['additional_data']['type']),
            'transaksi_id' => $val[$doc_name.'_id'],
            'pendaftaran_id' => $val['pendaftaran_id'],
            'konfig_dokumen_id' => $doc['konfig_dokumen_id'],
            'is_deleted' => false,
        ])->one();

        Yii::$app->cache->set("esign-generated-".$hash, 1, 30 * 60); // 30 menit untuk generate data yang sama
        $this->generateProcess($parameters, $existDoc);
    }
    

    public function generateProcess($parameters, $existDoc = null)
    {   
        $paramsIni = Yii::$app->params['iniFile']; // ????
        $configEsign = (new DocoConstansId)->actionGetAdditional('konfig_esign',true);
    	$val = $parameters['data'];
        $doc = $parameters['doc'];
    	$doc_name = $parameters['doc_name'];
        $value = null;

        if ($doc['type'] == 'document') {
            $paramsArray = [];
            $valParam = null;
            $skipDokumen = false;
            $params = json_decode($doc['params'], true);
            foreach ($params as $attr => $param) {
                if (is_null($param['fixed_value'])) {
                    if (!empty($val[$param['column']])) {
                        // Kondisi ada.
                        $valParam = $param['encrypt'] ? DocoHelpers::encrypt($val[$param['column']]) : (
                            isset($param['is_integer']) == true ? intval($val[$param['column']]) : $val[$param['column']]);
                    } else {
                        Yii::error([
                            empty($val[$param['column']]),
                            $param['column'],
                        ]);
                        $skipDokumen = true;
                        break;
                    }
                 } else {
                    $valParam = $param['fixed_value'];
                 }
                 $paramsArray[$attr] = $valParam;
            }

            $paramsArray = array_merge($paramsArray, [
                'skip_signed' => true,
            ]);

            if ($skipDokumen) {
                Yii::error([
                    'message' => 'skipped Doc',
                    'val' => $val,
                    'doc' => $doc,
                ]);
                return;
            };
            if (!$doc['is_reportdesigner']) {
                $url_doc = $doc['base_url'] . $doc['doc_url'] . http_build_query($paramsArray);
            } else {
                $url_doc = isset($paramsIni['report']) ? $paramsIni['report']['api_url'] : $doc['base_url'];
            }

            $validationData = json_decode($doc['validation'], true);

            if (!empty($validationData) && is_array($validationData)) {
                $data = [
                    'raw_url' => $doc['base_url'] . $doc['doc_url'],
                    'url' => $url_doc,
                    'is_report' => $doc['is_reportdesigner'],
                    'is_bgprocess' => $doc['is_bgprocess'],
                    'nama_dokumen' => trim($doc['additional_data']['type']),
                    'kode_dokumen' => $doc['kode_report'],
                    'query_param' => $paramsArray,
                    // 'path' => $pathUri
                ];
                $valid = true;
                foreach ($validationData as $key => $dataValidasi) {
                    switch ($key) {
                        case 'instalasi_id':
                            if (in_array($val['instalasi_id'], $dataValidasi)) {
                                if ($val['instalasi_id'] == DocoConstants::VAR_I_RANAP && empty($val['pasienadmisi_id'])) {
                                    $valid = false;
                                }
                            } else {
                                $valid = false;
                            }
                            break;
                        case 'admisi_igd':
                            /**
                            * Handle kondisi CETAKAN RD RUJUK RANAP.
                            */
                            // if ($dataValidasi && $val['instalasi_id'] == DocoConstants::VAR_I_RD && empty($val['pasienadmisi_id'])) {
                            //     $valid = false;
                            // }
                            break;
                        default:
                            break;
                    }

                    if(!$valid) {
                        break;
                    }
                }
                if($valid) {
                    $value = $data;
                }
            } else {
                $value = [
                    'raw_url' => $doc['base_url'] . $doc['doc_url'],
                    'url' => $url_doc,
                    'is_report' => $doc['is_reportdesigner'],
                    'is_bgprocess' => $doc['is_bgprocess'],
                    'nama_dokumen' => trim($doc['additional_data']['type']),
                    'kode_dokumen' => $doc['kode_report'],
                    'query_param' => $paramsArray,
                    // 'path' => $pathUri
                ];
            }

            if(empty($value)) {
                Yii::error([
                    'message' => 'value empty',
                    'val' => $val,
                    'doc' => $doc,
                ]);
                return;
            }

            $docStream = null;
            if ($value['is_report']) {
                $query_parameter = [
                    'api-key' => isset($paramsIni['report']) ? $paramsIni['report']['api_key'] : null,
                    'ds_access' => isset($paramsIni['report']) ? $paramsIni['report']['schema'] : null,
                    'ds_kode' => $value['kode_dokumen']
                ];
                $mergeParameter = array_merge($query_parameter, $value['query_param']);
                $client = new Client([
                    'verify' => false,
                    'base_uri' => $value['url']
                ]);

                $file = $client->get('api/preview-pdf', [
                    'query' => $mergeParameter,
                    'http_errors' => false
                ]);
                $docStream = $file->getBody();

                Yii::error(json_encode([
                    'query' => $mergeParameter,
                    "value" => $value,
                ]));
            } else {
                $guzzle = new Client([
                    'cookies' => true,
                    'verify' => false
                ]);

                if ($value['is_bgprocess']) {
                    $headers = [
                        'Authorization' => $bearerToken,
                        'X-Owner' => 'YmRnLXNpbXJzLWRvY28tZGV2ZWxvcG1lbnQ'
                    ];
                    $value['query_param']['nama_dokumen'] = $value['nama_dokumen'];
                    $value['query_param']['is_ftp'] = true;
                    $file = $guzzle->get($value['raw_url'], [
                        'http_errors' => false,
                        'headers' => $headers,
                        'query' => $value['query_param']
                    ]);
                    $docStream = $file->getBody();
                    Yii::error([
                        'url' => $value['raw_url'],
                        'options' => [
                            'http_errors' => false,
                            'headers' => $headers,
                            'query' => $value['query_param']
                        ]
                    ]);
                } else {
                    $cookieJar = $this->getCookieJar($doc['base_url']);
                    $headers = [
                        'Cache-Control' => 'no-cache',
                        'Content-Type' => 'Content-Disposition: attachment; filename="dokumen"',
                        'Content-Transfer-Encoding' => 'binary'
                    ];
                    $file = $guzzle->get($value['url'], [
                        'cookies' => $cookieJar,
                        'http_errors' => false,
                        'headers' => $headers
                    ]);
                    $docStream = $file->getBody();
                }
            }

            $class = $configEsign['provider'];
            $saveDoc = $class::saveDoc($val['pendaftaran_id'] . '_' . strtolower(str_replace(' ', '_', trim($doc['additional_data']['type']))) . '_' . $val[ $doc_name . '_id'] . '.pdf', $docStream);
            $data = [
                'type' => trim($doc['additional_data']['type']),
                'transaksi_id' => $val[$doc_name.'_id'],
                'pegawai_id' => $val[$doc_name . '_pegawai_id'],
                'pendaftaran_id' => $val['pendaftaran_id'],
                'path' => $saveDoc['path'],
                'filename' => $saveDoc['newFilename'],
                'konfig_dokumen_id' => $doc['konfig_dokumen_id'],
                'doc_status' => DocoConstants::ESIGN_STAT_GENERATED,
                'created_date' => date('Y-m-d H:i:s', time()),
            ];

            if(empty($existDoc)) {
                $model = new DokumenSign();
            } else {
                if($existDoc->doc_status == DocoConstants::ESIGN_STAT_GENERATED) {
                    $model = $existDoc;
                } else {
                    $model = new DokumenSign();

                    $existDoc->is_deleted = true;
                    $existDoc->save();
                }
            }
            $model->setAttributes($data);
            $model->save();

            RmNotification::documentSignNotification($model);
        }
    }

    /**
     * Auth login
     *
     * @author Maulana
     */
    private function AuthLogin()
    {
        $params = Yii::$app->params['iniFile'];
        $baseConfig = isset($params['authusereklaim']) ? $params['authusereklaim'] : [];
        $urlBackend = isset($params['rabbitMq']['url_backend']) ? $params['rabbitMq']['url_backend'] : 'http://web:8858/';
        $docoRest = new Client([
            'base_uri' => $urlBackend . 'dcms/v1/',
            'verify' => false,
            'headers' => [
                'user-agent' => 'cli',
            ]
        ]);

        if (!empty($baseConfig)) {
            $response = $docoRest->post('auth/get-token', [
                'form_params' => [
                    'username' => $baseConfig['username'],
                    'password' => $baseConfig['password']
                ]
            ]);
            $response = json_decode($response->getBody(), true);
            $token = isset($response['response']['access_token']) ? $response['response']['access_token'] : null;

            $bearer = 'Bearer ' . $token;
            Yii::error(json_encode([
                'token_user' => $bearer
            ]));
            return $bearer;
        }
        return null;
    }
 
    private function getCookieJar($url_frontend) {
        $params = Yii::$app->params['iniFile'];
        $guzzle = new Client([
            'cookies' => true,
            'verify' => false,
            'headers' => [
                'Cache-Control' => 'no-cache',
                'Content-Type' => 'application/x-www-form-urlencoded'
            ]
        ]);
        $jar = new CookieJar();
        $test = $guzzle->post(trim($url_frontend,'/') . '/site/login', [
            'form_params' => [
                'LoginForm[username]' => isset($params['authusereklaim']) ? $params['authusereklaim']['username'] : null,
                'LoginForm[password]' => isset($params['authusereklaim']) ? $params['authusereklaim']['password'] : null,
            ],
            'cookies' => $jar
        ]);

        $_csrf = $jar->getCookieByName("_csrf")->toArray();
        $sirs = $jar->getCookieByName("development-sirs")->toArray();
        $cookieJar = CookieJar::fromArray([
            '_csrf' => $_csrf['Value'],
            'development-sirs' => $sirs['Value']
        ], $sirs['Domain']);
        return $cookieJar;
    }
}