<?php

namespace app\modules\master\components\actions;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\ViewAction;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\base\Exception;

class UpdateModalAction extends ViewAction
{
    public $serviceName = null;
    public $serviceUpdateAction = null;
    public $serviceViewAction = null;
    public $requestUpdateMethod = 'PUT';
    public $requestViewMethod = 'GET';
    public $modelForm;
    public $viewForm;
    public $titleForm = null;
    public $module;
    public $additional_data;

    public function run($id = null)
    {
        $request = Yii::$app->request;
        $title = (!is_null($this->titleForm)) ? $this->titleForm : Yii::t('fe', 'Ubah Data');
        $model = $this->modelForm;
        $viewForm = $this->viewForm;
        $status = $this->controller->_getStatus();
        $options = $this->controller->_getOptions();
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);
        $uriUpdate = $this->serviceUpdateAction;
        $uriView = $this->serviceViewAction;
        $additional_data = $this->additional_data;
        $jenisKomponen = $this->controller->_getJenisKomponen();
        if ($request->post()) {
            $model->load($request->post());
            // if ($model->validate()) {
                try {
                    $response = $this->serviceName->request($this->requestUpdateMethod,$uriUpdate,[
                        'query' => ['id' => $id],
                        'form_params' => $model->attributes
                    ]);
                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response,true);
                } catch (RequestException $e) {
                    return DocoHelpers::response(['message' => $e->getMessage()],500);
                } catch (\Exception $e) {
                    return DocoHelpers::response(['message' => $e->getMessage()],500);
                }
            /*} else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }*/
        } else {           
            try{     
                $response = $this->serviceName->request($this->requestViewMethod,$uriView,[
                        'query' => ['id' => $id]]);
                $body = json_decode($response->getBody(), TRUE);
                $attributes = $body['response'];
                $model->attributes = $attributes;
                if ($uriView == 'komponen-tarif/view') {
                    switch ($attributes) {
                        case $attributes['is_dokter']:
                            $valueKomponen = 'is_dokter';
                            break;

                        case $attributes['is_perawat']:
                            $valueKomponen = 'is_perawat';
                            break;

                        case $attributes['is_fisioterapis']:
                            $valueKomponen = 'is_fisioterapis';
                            break;

                        case $attributes['is_dietisien']:
                            $valueKomponen = 'is_dietisien';
                            break;

                        case $attributes['is_radiografer']:
                            $valueKomponen = 'is_radiografer';
                            break;

                        // default:
                            // break;
                    }
                    $model->jenis_komponen = isset($valueKomponen) ? $valueKomponen : null;
                    if (isset($model->persen_delegasi)) {
                        $model->persen_delegasi = str_replace('.', ',', $model->persen_delegasi );
                    }
                    

                }
            } catch(RequestException $e){
                return DocoHelpers::responseTemplate(500, $e->getMessage());
            }
            return $this->controller->renderAjax($this->viewForm,get_defined_vars());
        }
    }
}