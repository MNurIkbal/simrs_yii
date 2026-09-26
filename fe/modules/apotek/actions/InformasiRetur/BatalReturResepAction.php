<?php

/**
 * @author : Asri
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\InformasiRetur;

use Yii;
use yii\base\Action;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use Doco\apotek\models\InformasiForm;

class BatalReturResepAction extends Action
{
    public function run()
    {   
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $retur_decrypt = []; 

        $request = Yii::$app->request;
        $post = $request->post();

        // decrypt id retur resep
        $returresep_id = ArrayHelper::getValue($post, 'returresep_id');
        foreach($returresep_id as $value) {
            $retur_decrypt[] = DocoHelpers::decrypt($value);
        }

        return $this->controller->guzzleExec(Yii::$app->docoRest->apotek,[
            'url' => 'inf-retur/batal-retur',
            'method' => 'POST',
            'payload' => [
                'query' => [
                    'returresep_id' => $retur_decrypt,
                ]
            ],
            'returnResponse' => true
        ]);
    }
}
