<?php
//Author Ardi Pratama
namespace app\modules\master\components\actions;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\ViewAction;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\base\Exception;

class CreateModalAction extends ViewAction
{
	public $serviceName = null;
	public $serviceCreateAction = null;
    public $modelForm;
    public $viewForm;
    public $titleForm;
    public $additional_data;

	public function run()
	{
        try {
            $title = isset($this->titleForm) ? $this->titleForm : Yii::t('fe', 'Tambah');
            $model = $this->modelForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $status = $this->controller->_getStatus();
            $options = $this->controller->_getOptions();
            $jenisKomponen = $this->controller->_getJenisKomponen();
            // $model->is_active = 1;
            $request = Yii::$app->request;
            $additional_data = $this->additional_data;
            if ($request->post()) {
                $model->load($request->post());
                if ($model->validate()) {
                    try {
                        $response = $this->serviceName->request('POST', $this->serviceCreateAction,[
                                        'form_params' => $model->attributes
                                ]);
                        $response = json_decode($response->getBody(),true);
                        return DocoHelpers::response($response, true);
                    } catch (RequestException $e) {
                        return DocoHelpers::response(['message' => $e->getMessage()],500);
                    } catch (\Exception $e) {
                        return DocoHelpers::response(['message' => $e->getMessage()],500);
                    }

                    // $response = $this->serviceName->request('POST', $this->serviceCreateAction,[
                    //                     'form_params' => $model->attributes
                    //             ]);
                } else {
                    $errors = DocoHelpers::parseError($model->errors, $formName);
                    return DocoHelpers::responseTemplate(422, 'Error', $errors);
                    // $errors = DocoHelpers::parseError($model->errors,$model);
                    // return DocoHelpers::response([
                    //         'response' => [
                    //             'data' => $errors
                    //         ]
                    //     ],422);
                }
            } else {
                return $this->controller->renderAjax($this->viewForm,get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
	}
}