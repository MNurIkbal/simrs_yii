<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\fisioterapi\actions\PemeriksaanRanap;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;
use app\modules\fisioterapi\models\SoapForm;

class IsCreateSoapAction extends BaseCurrentAction
{
    public function run()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $id = $request->get('id');
        $status = (new DocoHelpers)->guzzleExec(Yii::$app->docoRest->fisioterapi, [
            'method' => 'GET',
            'url'    => 'soap-ranap/status-by-pendaftaran'
        ], [
            'query' => [
                'id' => DocoHelpers::decrypt($id)
            ]
        ]);
        $allowed = $status['is_pulang'] == TRUE ? 0 : ($status['is_create_soap'] == TRUE ? 0 : 1);
        return ['allowed' => $allowed];
    }
}
