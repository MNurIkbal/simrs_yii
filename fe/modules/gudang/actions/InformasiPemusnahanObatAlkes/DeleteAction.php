<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\gudang\actions\InformasiPemusnahanObatAlkes;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class DeleteAction extends Action {
    public function run($id) {
        $id = DocoHelpers::decrypt($id);
        try{
            $response = Yii::$app->docoRest->gudang->post('inf-pemusnahan-obat/delete-pemusnahan', ['form_params' => ['id' => $id]]);
            $body = json_decode($response->getBody(), true);

            return DocoHelpers::response($body['response']);
        } catch(\Exception $e){
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch(RequestException $e){
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }
}