<?php

/**
 * @author : Asri Nurul M
 * Powered by Sirs
 */

namespace Doco\apotek\actions\ProduksiObat;

use Yii;
use yii\base\Action;
use yii\base\View;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use Doco\apotek\models\ApprovalProduksiObatForm;

class SaveProduksiAction extends Action {
    public function run($id) {
        try {
            $model = new ApprovalProduksiObatForm;
            $request = Yii::$app->request;
            $id = DocoHelpers::decrypt($id);
            $post = $request->post();
            $model->attributes = $post;
            $model->produksiobatalkes_id = DocoHelpers::decrypt($request->get('id'));

            if ($model->validate()) {
                $response = $this->controller->guzzleExec(Yii::$app->docoRest->apotek,[
                    'url' => 'inf-produksi-obat/save-produksi',
                    'method' => 'POST',
                    'payload' => [
                        'form_params' => [
                            'ruangan_id' => Yii::$app->docoVars->workspace('ruangan_id'),
                            'data' => DocoHelpers::decrypt($request->get('id')),
                            'post' => $post,
                        ]
                    ]
                ]);
                
                if(isset($response['httpStatusCode']) == 422) {
                    return DocoHelpers::response($response, 422);
                }else{
                    return DocoHelpers::response($response, 200, true);
                }
            } else {
                $response = $model->errors;
                return DocoHelpers::response($response,422,'ApprovalProduksiObatForm');
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }
}
