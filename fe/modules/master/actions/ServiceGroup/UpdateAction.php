<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\master\actions\ServiceGroup;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use app\modules\master\models\ServiceGroupForm;
use GuzzleHttp\Exception\RequestException;

class UpdateAction extends Action {
    public function run($id = null) {
        try {
            $model = new ServiceGroupForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $title = Yii::t('app', 'Ubah Service Group');

            $encryptedId = $id;
            $id = DocoHelpers::decrypt($id);

            $request = Yii::$app->docoRest->master->get('service-group/get-by-id?id='.$id);
            $response = json_decode($request->getBody(), true);
            $attributes = $response['response'];
            $model->attributes = $attributes;

            if (Yii::$app->request->post()) {
                $model->load(Yii::$app->request->post());

                if ($model->validate()) {
                    $request = Yii::$app->docoRest->master->post('service-group/update-data?id='.$id, [
                        'form_params' => $model->attributes
                    ]);
                    $response = json_decode($request->getBody(), true);

                    return DocoHelpers::response($response);
                } else {
                    $errors = DocoHelpers::parseError($model->errors, $formName);
                    return DocoHelpers::responseTemplate(422, 'Error', $errors);
                }
            } else {
                return $this->controller->renderPartial('form', get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }
}