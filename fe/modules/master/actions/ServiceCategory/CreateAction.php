<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\master\actions\ServiceCategory;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use app\modules\master\models\ServiceCategoryForm;
use GuzzleHttp\Exception\RequestException;

class CreateAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $model = new ServiceCategoryForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);

        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                $response = Yii::$app->docoRest->master->post('service-category/save', [
                    'form_params' => $model->attributes
                ]);
                $result = json_decode($response->getBody(),true);

                return DocoHelpers::response($result);
            } else {
                $errors = DocoHelpers::parseError($model->errors, 'ServiceCategoryForm');
                return DocoHelpers::response([
                    'response' => [
                        'data' => $errors
                    ]
                ], 422);
            }
        } else {
            return $this->controller->renderPartial('form', get_defined_vars());
        }
    }
}