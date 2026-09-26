<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\Integration;

use Yii;
use yii\base\Action;
use app\modules\v1\models\StockReturnView;

class CallbackStockReturnDetailAction extends Action {
    public function run() {
        $request = Yii::$app->request;

        $isError = $request->post('is_error',null);
        $data = $request->post('data',null);
        try{
            if(is_null($data)){
                throw new \Exception("Payload Kosong", 1);
            }
            if(isset($data['is_empty']) && $data['is_empty'] == true){
                return [
                    'message' => 'Tidak Ada Yang Diproses'
                ];
            }
            $payload = isset($data['payload']) ? $data['payload'] :[];
            $isSent = $isError ? false : true;
            if (!$isSent) {
                $payload = $request->post('error_payload');
            }

            $uidSercon = $request->post('uid');
            $idRekap = isset($payload['id']) ? $payload['id'] : null;
            $tipeRekap = isset($payload['tipe_rekap']) ? $payload['tipe_rekap'] : null;

            $this->controller->logWarning([
                'message' => 'log payload',
                'payload' => $payload
            ]);
            if (!empty($idRekap)) {
                $isSent = true;
                if ($isError) {
                    $isSent = false;
                }

                if($tipeRekap == StockReturnView::RETUR) {
                    $query = Yii::$app->db->createCommand("
                        UPDATE returresepdetail_r
                            SET is_sent = :is_sent, id_sync_sercon = :id_sync_sercon , sync_respon = :sync_respon
                        WHERE id = :id
                    ")
                    ->bindValue(':is_sent', $isSent)
                    ->bindValue(':id_sync_sercon', $uidSercon)
                    ->bindValue(':id', $idRekap)
                    ->bindValue(':sync_respon', json_encode([
                            'uid' => $uidSercon,
                            'result' => isset($data['response']) ? $data['response'] : null,
                            'payload' => $payload
                        ]))
                    ->execute();
                    $this->controller->logWarning([
                        'message' => 'Stock Return Detail RETUR update',
                        'payload' => $stockReturn->attributes
                    ]);
                } else if($tipeRekap == StockReturnView::BATAL) {
                    $query = Yii::$app->db->createCommand("
                        UPDATE pembatalanresepdetail_r
                            SET is_sent = :is_sent, id_sync_sercon = :id_sync_sercon , sync_respon = :sync_respon
                        WHERE id = :id
                    ")
                    ->bindValue(':is_sent', $isSent)
                    ->bindValue(':id_sync_sercon', $uidSercon)
                    ->bindValue(':id', $idRekap)
                    ->bindValue(':sync_respon', json_encode([
                            'uid' => $uidSercon,
                            'result' => isset($data['response']) ? $data['response'] : null,
                            'payload' => $payload
                        ]))
                    ->execute();
                    $this->controller->logWarning([
                        'message' => 'Stock Return Detail BATAL update',
                        'payload' => $stockReturn->attributes
                    ]);
                }

                return [
                    'messages' => 'data berhasil di update'
                ];
            }
        } catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
        throw new \Exception("Error Processing Request");
    }
}