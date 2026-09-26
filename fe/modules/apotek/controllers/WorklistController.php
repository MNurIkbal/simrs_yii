<?php

/**
 * @author : Anggoro (tri.anggoro@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\controllers;

use Yii;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoHelpers;

class WorklistController extends DocoController {
	protected $allowAction = ['*'];

    public function actions() {
        return [
        	'index'         => 'Doco\apotek\actions\worklist\IndexAction',
            'get-list-rj'   => 'Doco\apotek\actions\worklist\GetListRJAction',
            'get-list-ri'   => 'Doco\apotek\actions\worklist\GetListRIAction',
            'get-list-rd'   => 'Doco\apotek\actions\worklist\GetListRDAction',
            'get-list-penunjang' => 'Doco\apotek\actions\worklist\GetListPenunjangAction',
            'get-detail'    => 'Doco\apotek\actions\worklist\GetDetailAction',
            'update-status' => 'Doco\apotek\actions\worklist\UpdateStatusAction',
            'print-etiket' => 'Doco\apotek\actions\worklist\PrintEtiketAction',
            'print-etiket-new' => 'Doco\apotek\actions\worklist\PrintEtiketNewAction',
        ];
    }

    public function getDataWorklist($instalasi_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $no_resep = $request->get('no_resep', null);
        $tanggal = $request->get('tanggal', null);

        try {
            $response = Yii::$app->docoRest->apotek->get('worklist', [
                'query' => [
                    'instalasi' => $instalasi_id,
                    'no_resep' => $no_resep,
                    'tanggal' => $tanggal,
                ],
            ]);
            $body = json_decode($response->getBody(), true);

            foreach ($body['response'] as $key => $value) {
                $body['response'][$key]['enc_noresep'] = @DocoHelpers::encrypt($key);
                $body['response'][$key]['enc_id'] = @DocoHelpers::encrypt($value['reseptur_id']);
            }

            return $body;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
