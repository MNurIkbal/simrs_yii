<?php

namespace Doco\Traits;

use yii\data\ActiveDataProvider;
use yii\base\DynamicModel;
use app\modules\v1\models\Lookup;
use Doco\components\DocoHelpers;
use Yii;


/**
 * Trait of all helper controller
 */
trait ControllerHelperTrait
{
    /**
     * global function to validate
     * 
     * @param Array $payload
     * @param Array $rules
     * @return Array
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function validatePayload($payload, $rules = [])
    {
        if (isset($payload['payloadKey']) && !empty($payload['payloadKey'])) {
            $originPayload = [];
            $allRules = [];
            foreach ($payload['payloadKey'] as $keyPayload => $rulesPayload) {
                $originPayload[$keyPayload] = Yii::$app->request->post($keyPayload, null);
                $rulesArrayPayload = explode("|", $rulesPayload);
                foreach ($rulesArrayPayload as $rulesArray) {
                    $allRules[$rulesArray][] = $keyPayload;
                }
            }
            $ruleDefaultOption = [
                'required' => ['message' => '{attribute} tidak boleh kosong.'],
                'date' => ['format' => 'php:Y-m-d']
            ];
            if (isset($payload['rules'])) {
                $ruleDefaultOption = array_merge($ruleDefaultOption, $payload['rules']);
            }
            $validator = new DynamicModel($originPayload);
            foreach ($allRules as $rule => $column) {
                $validator->addRule($column, $rule, (isset($ruleDefaultOption[$rule]) ? $ruleDefaultOption[$rule] : []));
            }
            $validator->validate();

            if ($validator->hasErrors()) {
                return [
                    'errors' => $this->mapErrorFormToString($validator->errors, [
                        'arrayReturn' => true
                    ]),
                ];
            } else {
                return array_merge(Yii::$app->request->post(), $originPayload);
            }
        } else {
            $validator = new DynamicModel($payload);
            foreach ($rules as $rule) {
                $validator = $validator->addRule($rule['column'], $rule['type'], @$rule['option']);
            }
            $validator->validate();
            return $validator;
        }
    }

    /**
     * This function get config
     *
     * @param String $directory
     * @return Array/String
     * @author Tsani Nashrullah
     **/
    public function getConfig($directory)
    {
        $arrayDir = explode('.', $directory);
        $fileName = $arrayDir[0];
        unset($arrayDir[0]);
        $configDirectory = Yii::$app->basePath . '/../config/files/' . $fileName . '.php';
        $result = null;
        if (file_exists($configDirectory)) {
            array_values($arrayDir);
            $stringKeyArray = implode('.', $arrayDir);
            $arrayFile = include $configDirectory;
            if (empty($stringKeyArray)) {
                $result = $arrayFile;
            } else {
                $result = $this->helper->getKeyByString($stringKeyArray, $arrayFile);
            }
        }
        return $result;
    }


    /**
     * Macro of response
     *
     * @param Integer $httpCode Http Server code
     * @param String $message Message of response
     * @param Array $payloadResponse Payload of response will be assign to object data
     * @param Integer $customStatusCodeMeta
     * @return Array/Object
     * @author Tsani Nashrullah <tsani@docotel.com>
     **/
    public function responseJson($httpCode, $message = '', $payloadResponse = [], $customCodeMeta = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        \Yii::$app->response->statusCode = $httpCode;
        $option = [];
        if (is_array($customCodeMeta)) {
            $option = $customCodeMeta;
        }
        return [
            'meta' => [
                'result' => $httpCode < 300 && $httpCode >= 200 ? 'success' : 'failed',
                'title' => isset($option['title']) ? $option['title'] : null,
                'code' => !empty($customCodeMeta) && is_int((int) $customCodeMeta) && !is_array($customCodeMeta) ? $customCodeMeta : $httpCode
            ],
            'message' => $httpCode == 500 ? 'Terjadi kesalahan' : $message,
            'data' => $payloadResponse
        ];
    }

    /**
     * Mapping array error validation message to one string
     *
     * @param Array $arrayMessage
     * @param Boolean $firstRow
     * @return String
     * @author Tsani Nashrullah <tsani@docotel.com>
     **/
    public function mapErrorFormToString($arrayMessage, $firstRow=true)
    {
        $message = '';
        if (!$firstRow || isset($firstRow['arrayReturn'])) {
            if (isset($firstRow['arrayReturn'])) {
                $message = [];
            }
            foreach ($arrayMessage as $value) {
                if (isset($firstRow['arrayReturn'])) {
                    $message = array_merge($message, $value);
                } else {
                    $message .= $value[0] . "\n";
                }
            }
        } else {
            $keysOfArray = array_keys($arrayMessage);
            $message = $arrayMessage[$keysOfArray[0]][0];
        }
        return $message;
    }

    /**
     * This function to dump data
     *
     * @param Array/Object/String $payload
     * @author Tsani Nashrullah
     **/
    public function dd($payload)
    {
        echo '<pre>' . var_export($payload, true) . '</pre>';
        die();
    }

    /**
     * Log error
     *
     * @param Class $e | Exception Class
     * @author Tsani Nashrullah
     **/
    public function logError($e)
    {
        $request =  Yii::$app->request;
        $method = 'GET';
        if ($request->isPost) {
            $method = 'POST';
        } else if ($request->isPut) {
            $method = 'PUT';
        }
        Yii::error(
            'Message : ' . $e->getMessage() . '--||--Line : ' . $e->getLine() . '--||--File : ' . $e->getFile() . '--||--API URL : ' . Yii::$app->request->getPathInfo() . '--||--Method : ' . $method . '--||--Payload : ' . json_encode(Yii::$app->request->post()),
            'server-error'
        );
    }

    /**
     * Log error
     *
     * @param Array $array['message']
     * @param Array $array['payload']
     * @author Tsani Nashrullah
     **/
    public function logWarning( $array )
    {
        Yii::warning(
            'Message : ' . $array['message'] . '--||--Line : - --||--File : - --||--API URL : ' . Yii::$app->request->getPathInfo() . '--||--Method : - --||--Payload : ' . json_encode($array['payload']),
            'server-error'
        );
    }

    /**
     * This function will return result lookup by type in array
     * 
     * @param Array $lookupTypes
     * @return Array
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function lookupIn($lookupTypes)
    {
        $records = Lookup::find()
            ->select([
                'lookup_id as id',
                'lookup_name as name',
                'lookup_type as type'
            ])
            ->andWhere(['in', 'lookup_type', $lookupTypes])
            ->andWhere([
                'is_active' => true
            ])
            ->orderBy(['lookup_type' => SORT_ASC])
            ->asArray()
            ->all();
        $results = [];
        foreach ($records as $record) {
            $results[$record['type']][] = $record;
        }
        return $results;
    }

    /**
     * return all key params as print attributes with prefix # at start and end of key
     * 
     * @param Array $params
     * @return Array
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function paramsPrint($params)
    {
        $result = [];
        foreach ($params as $keyParam => $param) {
            $result['#' . $keyParam . '#'] = $param;
        }
        return $result;
    }

    public function activeDataProvider($query, $additionalData = null){
        $provider = new ActiveDataProvider([
            'query' => $query,
        ]);
        return [
            'data' => $provider->getModels(),
            '_meta' => [
                'totalCount' => $provider->getTotalCount(),
                'pageCount' => $provider->getPagination()->getPageCount(),
                'currentPage' => $provider->getPagination()->getPage() ? $provider->getPagination()->getPage() : 1,
                'perPage' => $provider->getPagination()->getPageSize(),
            ],
            'additional_data' => $additionalData
        ];
    }
}
