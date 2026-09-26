<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\Integration;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use app\modules\v1\models\IntReturnOrder;

class CallbackReturnOrderAction extends Action
{
    public function run()
    {
     	$request = Yii::$app->request;

     	$isError = $request->post('is_error');
     	$data = $request->post('data');
     	$payload = isset($data['payload']) ? $data['payload'] : [];
        if (!$isError && isset($data['is_error'])) {
            $isError = $data['is_error'];
        }
     	$isSent = $isError ? false : true;

     	if(isset($data['is_empty']) && $data['is_empty'] == true){
            return [
                'message' => 'Tidak Ada Yang Diproses'
            ];
        }

     	$uidSercon = $request->post('uid');
     	$idRekap = isset($payload['id']) ? $payload['id'] : null;
        $tipeRekap = isset($payload['tipe_rekap']) ? $payload['tipe_rekap'] : null;

     	if(!empty($idRekap)) {
     		$isSent = $isError ? false : true;

            if ($tipeRekap === 'RPOS' || $tipeRekap === 'RPOM') {
         		$query = Yii::$app->db->createCommand("
         			UPDATE returpenerimaanobat_r
         				SET is_sent = :is_sent, id_sync_sercon = :id_sync_sercon, sync_respon = :sync_respon
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

         		$this->controller->logWarning(['message'=>'Return update', 'payload'=>$payload]);

         		return [
         			'messages' => 'data berhasil di update'
         		];
            }else if($tipeRekap === 'RPBM' || $tipeRekap == 'RPBS'){
                $query = Yii::$app->db->createCommand("
                    UPDATE returpenerimaanbarang_r
                        SET is_sent = :is_sent, id_sync_sercon = :id_sync_sercon, sync_respon = :sync_respon
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

                $this->controller->logWarning(['message'=>'Return update', 'payload'=>$payload]);

                return [
                    'messages' => 'data berhasil di update'
                ];
            }
     	}

     	throw new \Exception("Error Processing Request");
    }
}
