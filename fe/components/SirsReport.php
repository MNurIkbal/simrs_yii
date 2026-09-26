<?php

namespace app\components;

use Yii;
use yii\base\Component;
use GuzzleHttp\Client;

class SirsReport extends Component
{
    const REPORT_HTML = 'HTML';
    const REPORT_JSON = 'JSON';
    const REPORTID_CODE = 'CODE';
    const REPORTID_URL = 'URL';
    const RENDERMODE_FULL = 'FULL';
    const RENDERMODE_PDF = 'PDF';

    private $_ini_file;
    private $_redirect;
    private $_modules;
    private $_controller;
    private $_action;
    private $_query_param;

    private function camelToDashes($string){
        $strArr=[];
        preg_match_all('/((?:^|[A-Z])[a-z1-9]+)/',$string,$matches);
        if(count($matches)>0 && count($matches[0])>0){

            for ($i=0; $i < count($matches[0]); $i++) {
                $strArr[] = strtolower($matches[0][$i]);
            }
        }
        return implode('-',$strArr);
    }

    public function __construct()
    {
        $initIniFile = @parse_ini_file(Yii::getAlias("@app").'/config/env/.env', true);
        $this->_ini_file = @$initIniFile['report'];
    }

    public function init()
    {
        $this->_redirect = false;
        $this->_modules = '';
        $this->_controller = '';
        $this->_action = '';
        $this->_query_param = '';
    }

    public function __get($property)
    {
      $ini = $this->_ini_file;
      if (property_exists($this, $property)) {
        return $this->$property;
      } else {
        return isset($ini[$property]) ? $ini[$property] : null;
      }
    }

    public function embedViewer($link)
    {
        if (strpos($link, '?') == false) {
            $link = $link.'?';
        }
        $src = $this->address.'/reports/viewer/'.$link.'&ds_access='.@$this->schema;
        $html = "
        <iframe sandbox='allow-same-origin allow-scripts allow-presentation allow-downloads allow-modals allow-popups' src=$src style='border:none; height: 864px; width: 100%;'></iframe>
        ";

        return $html;
    }

    public function embedDesigner($code)
    {
        $src = $this->address.'/reports/designer/'.$code.'?ds_access='.@$this->schema;
        $html = "
        <iframe sandbox='allow-same-origin allow-scripts allow-presentation allow-downloads allow-modals allow-popups' src=$src style='border:none; height: 864px; width: 100%;'></iframe>
        ";

        return $html;
    }

    public function exec($reportId,$options = [])
    {
        $manualRender = isset($options['manualRender']) ? $options['manualRender'] : null;
        $reportConfig = null;
        $docMapping = null;
        $reportCode = null;
        $validateReport = $this->validateId($reportId);
        if($validateReport == self::REPORTID_URL && $this->version == self::REPORT_HTML){
            return $this->renderPdf();
        }else if($validateReport == self::REPORTID_CODE){
            $parse = parse_url($reportId);
            $request = Yii::$app->docoRest->dcms->get('report/config',[
                'query'=>[
                    'kode'=>@$parse['path']
                ]
            ]);
            $body = json_decode($request->getBody());
            $reportCode = isset($body->response->reportCode) ? $body->response->reportCode : null;
            $reportConfig = isset($body->response->reportConfig) ? $body->response->reportConfig : null;
            $docMapping = isset($body->response->docMapping) ? $body->response->docMapping : null;

            if(isset($docMapping->doc_key) && $this->version == self::REPORT_HTML){
                if(isset($options['manualRender'])){
                    return call_user_func($manualRender);
                }
                $this->renderDocMapping($docMapping->doc_key);
            }
        }else if ($validateReport == self::REPORTID_URL){
            $request = Yii::$app->docoRest->dcms->get('report/config-url',[
                'query'=>[
                    'modules' => $this->_modules,
                    'controller' => $this->_controller,
                    'action' => $this->_action
                ]
            ]);

            $body = json_decode($request->getBody());
            $reportCode = isset($body->response->reportCode) ? $body->response->reportCode : null;
            $reportConfig = isset($body->response->reportConfig) ? $body->response->reportConfig : null;
            $docMapping = isset($body->response->docMapping) ? $body->response->docMapping : null;
        }

        if(isset($docMapping->doc_key) && isset($reportConfig->docmapping_enabled) && $reportConfig->docmapping_enabled == true){
            if(isset($options['manualRender'])){
                return call_user_func($manualRender);
            }
            $this->renderDocMapping($docMapping->doc_key);
        }else if(isset($reportConfig->render_mode) && strtolower($reportConfig->render_mode) == strtolower(self::RENDERMODE_PDF)){
            $request_get = Yii::$app->request->get();
            $build_query = [
                'api-key' => $this->api_key,
                'ds_kode' => $reportCode,
                'ds_access' => $this->schema
            ];
            if($this->_query_param != '' && $validateReport == self::REPORTID_URL){
                if(substr($this->_query_param,0,1) == '?'){
                    $request_get = [];
                    $que_param = substr($this->_query_param,1); //remove ?
                    parse_str($que_param,$request_get);
                }
            }

            if(isset($options['queryParameter']) && is_array($options['queryParameter'])){
                $query_parameter = array_merge($options['queryParameter'],$build_query);
            }else{
                $query_parameter = array_merge($request_get,$build_query);
            }

            $client = new Client([
                'base_uri' => $this->api_url
            ]);
            $filename = $reportCode.'.pdf';
            $response = $client->get('api/preview-pdf',[
                'query' => $query_parameter,
                'save_to'=> Yii::getAlias("@download") . '/'.$filename,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf(Yii::getAlias("@download") . '/'.$filename,null,true);
        }else{
            if(
                isset($options['useQueryParameterInViewer']) 
                && $options['useQueryParameterInViewer'] == true 
                && isset($options['queryParameter']) 
                && is_array($options['queryParameter'])
            ) {
                $added_param = '';
                if((empty($this->_query_param) || $this->_query_param === '')){
                    $added_param = '?'.http_build_query($options['queryParameter']);
                }
                return Yii::$app->controller->redirect(['/reports/viewer/'.$reportId.$added_param]);
            }
            return Yii::$app->controller->redirect(['/reports/viewer/'.$reportId]);
        }
    }

    private function renderDocMapping($doc_key)
    {
        $arrDoc = explode('-',$doc_key);
        if(is_array($arrDoc) && count($arrDoc) == 3){
            $this->_modules = $arrDoc[0];
            $camelController = preg_replace('/(Controller)$/','',$arrDoc[1]);
            $camelAction = preg_replace('/^(action)/','',$arrDoc[2]);

            $this->_controller = $this->camelToDashes($camelController);
            $this->_action = $this->camelToDashes($camelAction);
            $this->_query_param = '?'.http_build_query(Yii::$app->request->get());
            $this->renderPdf();
        }else{
            throw new \Exception("Failed Url Document", 1);

        }
    }

    private function validateId($reportId)
    {
        $re = '/(([\w\-\_]+)\/([\w\-\_]+)\/([\w\-\_]+))(\?(.*))?/';

        preg_match_all($re, $reportId, $matches, PREG_SET_ORDER, 0);
        if(count($matches)>0){
            if(count($matches[0])>0){
                if(isset($matches[0][2])){
                    $this->_modules = $matches[0][2];
                }
                if(isset($matches[0][3])){
                    $this->_controller = $matches[0][3];
                }
                if(isset($matches[0][4])){
                    $this->_action = $matches[0][4];
                }
                if(isset($matches[0][5])){
                    $this->_query_param = $matches[0][5];
                }
            }
            return self::REPORTID_URL;
        }else{
            return self::REPORTID_CODE;
        }
    }

    public function renderPdf()
    {
        if($this->_modules != '' && $this->_controller != '' && $this->_action != ''){
            $filename = $this->_controller.'.pdf';
            $response = Yii::$app->docoRest->{$this->_modules}->get($this->_controller.'/'.$this->_action.$this->_query_param,
            [
                'save_to' => Yii::getAlias("@download") . '/'.$filename,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf(Yii::getAlias("@download") . '/'.$filename);
        }
    }

    public function isAvailable($reportId)
    {
        if ($this->enabled) {
            $response = Yii::$app->docoRest->dcms->get('report/config',[
                'query'=>[
                    'kode'=> $reportId,
                ]
            ]);

            $body = json_decode($response->getBody(), true);

            if ($body['response']['reportCode'] != null) {
                return true;
            }
        }
        return false;
    }
}
