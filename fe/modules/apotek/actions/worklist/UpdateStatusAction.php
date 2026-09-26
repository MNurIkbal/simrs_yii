<?php

/**
 * @author : Anggoro (tri.anggoro@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\worklist;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoConstants;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class UpdateStatusAction extends Action {
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        if($request->post('status_worklist') == DocoConstants::WORKLIST_SIAP_DISERAHKAN) {
            // action serahkan obat
            $response = $this->hitApiSerahkan($request);
        } else {
            // action update status
            $response = $this->hitApiUpdate($request);
        }

        return $response;
    }

    private function hitApiUpdate($request)
    {
        $form_params = [
            'identifier' => $request->post('identifier'),
            'status_worklist' => $request->post('status_worklist'),
            'pegawai_id' => $request->post('pegawai_id', null)
        ];

        if($request->post('multi_status', false)) {
            $form_params += ['multi_status' => $request->post('multi_status')];
        }

        return $this->controller->guzzleExec(Yii::$app->docoRest->apotek, [
            'url' => 'worklist/update-status',
            'method' => 'post',
            'payload' => [
                'form_params' => $form_params
            ],
            'returnResponse' => true
        ]);
    }

    private function hitApiSerahkan($request)
    {
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        return $this->controller->guzzleExec(Yii::$app->docoRest->apotek, [
            'url' => 'inf-reseptur/serahkan-obat',
            'method' => 'post',
            'payload' => [
                'form_params' => [
                    'nomor' => $request->post('identifier'),
                    'ruangan_id' => $ruangan_id,
                    'instalasiasal_id' => $request->post('instalasiasal_id'. null),
                    'pegawai_menyerahkan_id' => $request->post('pegawai_id', null)
                ]
            ],
            'returnResponse' => true
        ]);
    }
}