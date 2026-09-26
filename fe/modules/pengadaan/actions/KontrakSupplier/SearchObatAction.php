<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\KontrakSupplier;

use Yii;
use yii\base\Action;
use yii\filters\AccessControl;
use yii\web\Response;
use app\components\DocoHelpers;
use app\modules\pengadaan\models\PurchaseRequisitionForm;
use GuzzleHttp\Exception\RequestException;

class SearchObatAction extends Action {
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $nameOnly = $request->get('name_only', null);
        $codeOnly = $request->get('code_only', null);
        $term = $request->get('term', null);
        try {
            $req = Yii::$app->docoRest->pengadaan->get('kontrak-supplier/get-master-obat',[
                'query' => [
                    'term' => $term,
                    'name_only' => $nameOnly,
                    'code_only' => $codeOnly,
                ]
            ]);
            $response = json_decode($req->getBody(), true);

            $list = [];
            foreach ($response['response'] as $data_obat) {
                $text = $data_obat['kod']." - ".$data_obat['nma'];
                if($nameOnly) $text = $data_obat['nma'];
                if($codeOnly) $text = $data_obat['kod'];
                $item = [
                    "id" => $data_obat['id'],
                    "kode_obat" => $data_obat['kod'],
                    "nama_obat" => $data_obat['nma'],
                    "text" => $text,
                    "satuan" => $data_obat['satuan']
                ];
                $list[] = $item;
            }

            return ["results" => $list];
        } catch (\Exception $e) {
            return $e->getMessage();
            return [];
        }
    }
}