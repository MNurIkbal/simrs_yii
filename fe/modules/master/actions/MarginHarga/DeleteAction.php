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

class DeleteAction extends Action {
    public function run($id) {
        $id = DocoHelpers::decrypt($id);
        try {
            $request = Yii::$app->docoRest->master->delete('margin-harga/delete?id='.$id);
            $request = json_decode($request->getBody(),true);
            return DocoHelpers::response($request);
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }
}