<?php

/**
 * @author : Anggoro (tri.anggoro@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\PurchaseRequisition;

use Yii;
use yii\base\Action;

class SaveAction extends Action {
    public function run() {
        // Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
        $type = $request->post('type', null);
        $catatan = $request->post('catatan', null);
        $cyto = $request->post('is_cyto', null);
        $is_admin = $request->post('is_admin', null);
        $detailItem = $request->post('detail', []);
        $is_consignment = $request->post('is_consignment', null);

        foreach ($detailItem as $key => $item) {
            $detailItem[$key]['qty'] = str_replace(".", "", $item['qty']);
            unset($detailItem[$key]['reorder']);
            unset($detailItem[$key]['move_category']);
        }

        $to_post = [
            "pegawaiID" => $pegawai_id,
            "ruanganID" => $ruangan_id,
            "type" => $type,
            "reference" => $catatan,
            "cyto" => $cyto,
            "is_admin" => $is_admin,
            "detailItem" => $detailItem,
            "is_consignment" => $is_consignment,
            "is_admin" => $request->post('is_admin', false)
        ];
        return $this->controller->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => 'purchase-requisition/create',
            'method' => 'POST',
            'payload' => [
                'form_params' => ['pr' => $to_post]
            ],
            'returnResponse' => true
        ]);
    }
}
