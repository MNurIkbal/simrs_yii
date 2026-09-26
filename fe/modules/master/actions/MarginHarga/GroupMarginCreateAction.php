<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\master\actions\MarginHarga;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\modules\master\models\GroupMarginForm;

class GroupMarginCreateAction extends Action {
    protected $_title = "Margin Harga";

    public function run() {
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Tambah').' '.\Yii::t('fe', $this->_title);
        $model = new GroupMarginForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = null;
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                $postDetailMargin = $request->post('detail_margin');
                $post = [
                    'header' => $model->attributes,
                    'detail' => $postDetailMargin,
                ];
                try {
                    $response = Yii::$app->docoRest->master->post('margin-harga/group-margin-create', [
                        'form_params' => $post
                    ]);
                    $body = json_decode($response->getBody(), True);
                    return DocoHelpers::response($body);
                } catch (RequestException $e) {
                   return DocoHelpers::response(['message' => $e->getMessage()], 500);
                } catch (\Exception $e) {
                    return DocoHelpers::response(['message' => $e->getMessage()], 500);
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            $model->is_discount = 0;
            return $this->controller->renderAjax('form_group_margin', get_defined_vars());
        }
    }
}