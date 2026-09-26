<?php

namespace app\components\Traits;

use yii\base\DynamicModel;
use Yii;
use app\components\DocoHelpers;
use yii\helpers\Url;
/**
 * Trait helper
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
     * Macro of response
     *
     * @param Integer $httpCode Http Server code
     * @param String $message Message of response
     * @param Array $payloadResponse Payload of response will be assign to object data
     * @param Integer $customStatusCodeMeta
     * @return Array/Object
     * @author Tsani Nashrullah <tsani@docotel.com>
     **/
    public function responseJson($httpCode, $message, $payloadResponse = [], $customCodeMeta = null)
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
                'message' => $message,
                'title' => isset($option['title']) ? $option['title'] : null,
                'code' => !empty($customCodeMeta) && is_int((int) $customCodeMeta) && !is_array($customCodeMeta) ? $customCodeMeta : $httpCode
            ],
            'data' => $payloadResponse
        ];
    }

    /**
     * Logging exception
     *
     * @param Class/Exception $e
     * @author Tsani Nashrullah
     **/
    public function logError($e)
    {
        Yii::error([
            'Message' => $e->getMessage(),
            'File' => $e->getFile(),
            'Line' => $e->getLine(),
        ]);
    }

    /**
     * Guzzle Helper
     *
     * @param Class/Guzzle $guzzleClass
     * @param Array $optionGuzzle => two keys => url, method
     * @param Array $payloadData payload of request, two keys => form_params, query
     * @return Array
     * @author Tsani Nashrullah <tsani@docotel.com>
     **/
    public function guzzleExec($guzzleClass, $optionGuzzle, $payloadData = [], $withStatusCode = false)
    {
        return (new DocoHelpers)->guzzleExec($guzzleClass, $optionGuzzle, $payloadData, $withStatusCode);
    }

    /**
     * Mapping error message for form frontend response
     *
     * @param Array $errors
     * @return Array
     **/
    public static function mapErrorForm($errors, $modelName = '', $formId = null, $customAttribute = [])
    {
        $result = [];
        foreach ($errors as $keyError => $value) {
            if ( isset($customAttribute[$keyError]['is_multiple']) && $customAttribute[$keyError]['is_multiple'] ) {
                $result[$modelName . '[' . $keyError . '][]'] = $value;
            } else {
                $result[$modelName . '[' . $keyError . ']'] = $value;
            }
        }
        return !empty($formId) ? [
            'formId' => $formId,
            'errors' => $result
        ] : $result;
    }

    /**
     * @method Jump ruangan without use set-method
     * @param array $data
     *  
     * @return boolean
     * @author : Erlangga (librantara.erlangga@sirs.com)
     */
    public static function jumpRuanganWithoutSetMethod($data = [])
    {
        $rest_dcms = Yii::$app->docoRest->dcms;
        $session = Yii::$app->session;
        $loaded_ws = $session->get('active_workspace');
        $is_unset = $unset_all = false;
        
        $dtp = [
            'home_url' => Url::home(),
            'is_unset' => ($is_unset ? 1 : 0),
            'unset_all' => ($unset_all ? 1 : 0),
            'module_id' => $loaded_ws['modul_id'],
            'instalasi_id' => ($is_unset ? 0 : (isset($data['instalasiID']) ? $data['instalasiID'] : 0)),
            'instalasi_index' => ($is_unset ? 0 : (isset($data['instalasiIndex']) ? $data['instalasiIndex'] : 0)),
            'room_id' => ($is_unset ? 0 : (isset($data['roomID']) ? $data['roomID'] : 0)),
            'room_index' => ($is_unset ? 0 : (isset($data['roomIndex']) ? $data['roomIndex'] : 0))
        ];

        try {
            $rest = self::guzzleExec($rest_dcms, [
                'method' => 'POST',
                'url' => 'access-data/set-active-workspace',
                'payload' => [
                    'form_params' => $dtp,
                ],
            ]);
            $active_workspace = $rest['active_workspace'];
            $rModuleId = $rest['module_id'];
            $active_workspace['url'] = (Url::home().$active_workspace['url']);
            $session->set('moduleID',$rModuleId);
            $session->set('active_workspace', $active_workspace);
            return true;
        } catch (\Exception $e) {
            return 'error '.$e->getMessage();
        }
    }
}
