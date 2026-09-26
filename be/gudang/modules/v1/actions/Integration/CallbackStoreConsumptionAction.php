<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\Integration;

use Yii;
use yii\base\Action;
use app\modules\v1\models\StockScrapView;

class CallbackStoreConsumptionAction extends Action {

    public function run() {
        $request = Yii::$app->request;
        $results = $request->post('data') ?: [];
        $uid = $request->post('uid');
        if (!empty($uid) && isset($results[0])) {
            foreach ($results as $data) {
                $isError = isset($data['is_error']) ? $data['is_error'] : false;
                $response = isset($data['response']) ? $data['response'] : null;
                $payload = isset($data['payload']) ? $data['payload'] : [];
                if (isset($payload['id']) && isset($payload['tipe_rekap'])) {
                    switch ($payload['tipe_rekap']) {
                        case StockScrapView::BMHP :
                            $query = Yii::$app->db->createCommand("
                                UPDATE pemakaianobatdetail_r 
                                    SET is_sent = :is_sent, id_sync_sercon = :id_sync_sercon , sync_respon = :sync_respon
                                WHERE id = :id
                            ")
                            ->bindValue(':is_sent', !$isError)
                            ->bindValue(':id_sync_sercon', $uid)
                            ->bindValue(':id', $payload['id'])
                            ->bindValue(':sync_respon', json_encode([
                                    'uid' => $uid,
                                    'result' => $response,
                                    'payload' => $payload
                                ]))
                            ->execute();
                            $this->controller->logWarning(['message'=>'Stock Scrap BMHP update','payload'=>$payload]);
                            // return [
                            //     'messages' => 'data berhasil di update'
                            // ];
                            break;

                        case StockScrapView::ADJK :
                            $query = Yii::$app->db->createCommand("
                                UPDATE adjusmenobatkeluar_r 
                                    SET is_sent = :is_sent, id_sync_sercon = :id_sync_sercon , sync_respon = :sync_respon
                                WHERE id = :id
                            ")
                            ->bindValue(':is_sent', !$isError)
                            ->bindValue(':id_sync_sercon', $uid)
                            ->bindValue(':id', $payload['id'])
                            ->bindValue(':sync_respon', json_encode([
                                    'uid' => $uid,
                                    'result' => $response,
                                    'payload' => $payload
                                ]))
                            ->execute();
                            $this->controller->logWarning(['message'=>'Stock Scrap ADJK update','payload'=>$payload]);
                            // return [
                            //     'messages' => 'data berhasil di update'
                            // ];
                            break;

                        case StockScrapView::ADJM :
                            $query = Yii::$app->db->createCommand("
                                UPDATE adjusmenobatmasuk_r 
                                    SET is_sent = :is_sent, id_sync_sercon = :id_sync_sercon , sync_respon = :sync_respon
                                WHERE id = :id
                            ")
                            ->bindValue(':is_sent', !$isError)
                            ->bindValue(':id_sync_sercon', $uid)
                            ->bindValue(':id', $payload['id'])
                            ->bindValue(':sync_respon', json_encode([
                                    'uid' => $uid,
                                    'result' => $response,
                                    'payload' => $payload
                                ]))
                            ->execute();
                            $this->controller->logWarning(['message'=>'Stock Scrap ADJM update','payload'=>$payload]);
                            // return [
                            //     'messages' => 'data berhasil di update'
                            // ];
                            break;

                        case StockScrapView::MUSNAH :
                            $query = Yii::$app->db->createCommand("
                                UPDATE pemusnahanobatdetail_r 
                                    SET is_sent = :is_sent, id_sync_sercon = :id_sync_sercon , sync_respon = :sync_respon
                                WHERE id = :id
                            ")
                            ->bindValue(':is_sent', !$isError)
                            ->bindValue(':id_sync_sercon', $uid)
                            ->bindValue(':id', $payload['id'])
                            ->bindValue(':sync_respon', json_encode([
                                    'uid' => $uid,
                                    'result' => $response,
                                    'payload' => $payload
                                ]))
                            ->execute();
                            $this->controller->logWarning(['message'=>'Stock Scrap Musnah update','payload'=>$payload]);
                            // return [
                            //     'messages' => 'data berhasil di update'
                            // ];
                            break;

                        case StockScrapView::SO :
                            $query = Yii::$app->db->createCommand("
                                UPDATE stokopnamedetail_r 
                                    SET is_sent = :is_sent, id_sync_sercon = :id_sync_sercon , sync_respon = :sync_respon
                                WHERE id = :id
                            ")
                            ->bindValue(':is_sent', !$isError)
                            ->bindValue(':id_sync_sercon', $uid)
                            ->bindValue(':id', $payload['id'])
                            ->bindValue(':sync_respon', json_encode([
                                    'uid' => $uid,
                                    'result' => $response,
                                    'payload' => $payload
                                ]))
                            ->execute();
                            $this->controller->logWarning(['message'=>'Stock Scrap Opname update','payload'=>$payload]);
                            // return [
                            //     'messages' => 'data berhasil di update'
                            // ];
                            break;

                        case StockScrapView::BMHP_BARANG :
                            $query = Yii::$app->db->createCommand("
                                UPDATE pemakaianbarangdetail_r
                                    SET is_sent = :is_sent, id_sync_sercon = :id_sync_sercon , sync_respon = :sync_respon
                                WHERE id = :id
                            ")
                            ->bindValue(':is_sent', !$isError)
                            ->bindValue(':id_sync_sercon', $uid)
                            ->bindValue(':id', $payload['id'])
                            ->bindValue(':sync_respon', json_encode([
                                    'uid' => $uid,
                                    'result' => $response,
                                    'payload' => $payload
                                ]))
                            ->execute();
                            $this->controller->logWarning(['message'=>'Stock Scrap BMHP Barang update','payload'=>$payload]);
                            // return [
                            //     'messages' => 'data berhasil di update'
                            // ];
                            break;

                        case StockScrapView::ADJK_BARANG :
                            $query = Yii::$app->db->createCommand("
                                UPDATE adjusmenbarangkeluar_r
                                    SET is_sent = :is_sent, id_sync_sercon = :id_sync_sercon , sync_respon = :sync_respon
                                WHERE id = :id
                            ")
                            ->bindValue(':is_sent', !$isError)
                            ->bindValue(':id_sync_sercon', $uid)
                            ->bindValue(':id', $payload['id'])
                            ->bindValue(':sync_respon', json_encode([
                                    'uid' => $uid,
                                    'result' => $response,
                                    'payload' => $payload
                                ]))
                            ->execute();
                            $this->controller->logWarning(['message'=>'Stock Scrap ADJK Barang update','payload'=>$payload]);
                            // return [
                            //     'messages' => 'data berhasil di update'
                            // ];
                            break;

                        case StockScrapView::ADJM_BARANG :
                            $query = Yii::$app->db->createCommand("
                                UPDATE adjusmenbarangmasuk_r
                                    SET is_sent = :is_sent, id_sync_sercon = :id_sync_sercon , sync_respon = :sync_respon
                                WHERE id = :id
                            ")
                            ->bindValue(':is_sent', !$isError)
                            ->bindValue(':id_sync_sercon', $uid)
                            ->bindValue(':id', $payload['id'])
                            ->bindValue(':sync_respon', json_encode([
                                    'uid' => $uid,
                                    'result' => $response,
                                    'payload' => $payload
                                ]))
                            ->execute();
                            $this->controller->logWarning(['message'=>'Stock Scrap ADJM Barang update','payload'=>$payload]);
                            // return [
                            //     'messages' => 'data berhasil di update'
                            // ];
                            break;

                        case StockScrapView::MUSNAH_BARANG :
                            $query = Yii::$app->db->createCommand("
                                UPDATE pemusnahanbarangdetail_r
                                    SET is_sent = :is_sent, id_sync_sercon = :id_sync_sercon , sync_respon = :sync_respon
                                WHERE id = :id
                            ")
                            ->bindValue(':is_sent', !$isError)
                            ->bindValue(':id_sync_sercon', $uid)
                            ->bindValue(':id', $payload['id'])
                            ->bindValue(':sync_respon', json_encode([
                                    'uid' => $uid,
                                    'result' => $response,
                                    'payload' => $payload
                                ]))
                            ->execute();
                            $this->controller->logWarning(['message'=>'Stock Scrap Musnah Barang update','payload'=>$payload]);
                            // return [
                            //     'messages' => 'data berhasil di update'
                            // ];
                            break;

                        case StockScrapView::SO_BARANG :
                            $query = Yii::$app->db->createCommand("
                                UPDATE stokopnamebarangdetail_r 
                                    SET is_sent = :is_sent, id_sync_sercon = :id_sync_sercon , sync_respon = :sync_respon
                                WHERE id = :id
                            ")
                            ->bindValue(':is_sent', !$isError)
                            ->bindValue(':id_sync_sercon', $uid)
                            ->bindValue(':id', $payload['id'])
                            ->bindValue(':sync_respon', json_encode([
                                    'uid' => $uid,
                                    'result' => $response,
                                    'payload' => $payload
                                ]))
                            ->execute();
                            $this->controller->logWarning(['message'=>'Stock Scrap Opname Barang update','payload'=>$payload]);
                            // return [
                            //     'messages' => 'data berhasil di update'
                            // ];
                            break;

                        default:
                            // do nothing
                            break;
                    }
                }
            }
        }
    }
}