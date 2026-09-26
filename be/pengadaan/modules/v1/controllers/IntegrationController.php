<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\IntPurchaseOrder;
use app\modules\v1\models\IntPurchaseOrderLine;
use app\modules\v1\models\IntReturnOrder;
use app\modules\v1\models\IntReturnOrderLine;

class IntegrationController extends DocoActiveController
{
    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs['purchase-order'] = ["GET"];
        $verbs['purchase-order-line'] = ["GET"];
        $verbs['return-order'] = ["GET"];
        $verbs['return-order-line'] = ["GET"];
        return $verbs;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        unset($behaviors['access']);
        return $behaviors;
    }

    public function actions()
    {
        return [
            'return-order' => 'app\modules\v1\actions\Integration\ReturnOrderAction',
            'return-order-line' => 'app\modules\v1\actions\Integration\ReturnOrderLineAction',
            'callback-return-order' => 'app\modules\v1\actions\Integration\CallbackReturnOrderAction',
            'callback-return-order-line' => 'app\modules\v1\actions\Integration\CallbackReturnOrderLineAction',
            'resync' => 'app\modules\v1\actions\Integration\ResyncAction',
            'get-data-transaksi' => 'app\modules\v1\actions\Integration\GetDataTransaksiAction'
        ];
    }

    public function actionGetData($model)
    {
        try {
            $request = Yii::$app->request;
            $advancedFilter = $request->get('advanced-filter',[]);

            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');
            switch ($model) {
                case 'purchaseorder':
                    $view = new IntPurchaseOrder;
                    $filterDate = ArrayHelper::getValue($advancedFilter,'date_order','');
                    if(!empty($filterDate)){
                        $explode = explode(" - ", $filterDate);
                        if(count($explode) ==2){
                            $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                            $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                        }
                        unset($_GET['advanced-filter']['date_order']);
                    }

                    $query = $view::find(true);
                    $query->andWhere(['between', 'date_order', $start, $end]);
                    break;
                case 'purchaseorderline':
                    $view = new IntPurchaseOrderLine;
                    $filterDate = ArrayHelper::getValue($advancedFilter,'date_planned','');
                    if(!empty($filterDate)){
                        $explode = explode(" - ", $filterDate);
                        if(count($explode) ==2){
                            $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                            $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                        }
                        unset($_GET['advanced-filter']['date_planned']);
                    }

                    $query = $view::find(true);
                    $query->andWhere(['between', 'date_planned', $start, $end]);
                    break;
                case 'returnpurchaseorder':
                    $view = new IntReturnOrder;
                    $filterDate = ArrayHelper::getValue($advancedFilter,'date_order','');
                    if(!empty($filterDate)){
                        $explode = explode(" - ", $filterDate);
                        if(count($explode) ==2){
                            $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                            $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                        }
                        unset($_GET['advanced-filter']['date_order']);
                    }

                    $query = $view::find(true);
                    $query->andWhere(['between', 'date_order', $start, $end]);
                    break;
                case 'returnpurchaseorderline':
                    $view = new IntReturnOrderLine;
                    $filterDate = ArrayHelper::getValue($advancedFilter,'date_planned','');
                    if(!empty($filterDate)){
                        $explode = explode(" - ", $filterDate);
                        if(count($explode) ==2){
                            $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                            $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                        }
                        unset($_GET['advanced-filter']['date_planned']);
                    }

                    $query = $view::find(true);
                    $query->andWhere(['between', 'date_planned', $start, $end]);
                    break;
                default:
                    throw new \Exception("Undefined Model", 1);
                    break;
            }

            $query = DocoRestActiveFilter::advancedFilter($view, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionPurchaseOrder()
    {
        $uid = Yii::$app->request->get('uid',null);
        if(is_null($uid)){
            throw new \Exception("UID tidak boleh kosong", 1);
        }
    	$po = IntPurchaseOrder::find()->andWhere([
            'is_sending' => false
        ])->orderBy([
            'date_order' => SORT_ASC
        ])->one();

        if (empty($po)) {
            return [
                'status' => 422,
                'messages' => 'Tidak ada data yang di proses'
            ];
        }

        $data = $po->attributes;

        if ($po->tipe_rekap == IntPurchaseOrder::POS || $po->tipe_rekap == IntPurchaseOrder::PBS) {
            $query = Yii::$app->db->createCommand("
                UPDATE penerimaansupp_r SET is_sending = true,id_sync_sercon = '{$uid}' WHERE id = {$po->id}
            ")->execute();
        } else if ($po->tipe_rekap == IntPurchaseOrder::POM) {
            $query = Yii::$app->db->createCommand("
                UPDATE penerimaanobat_r SET is_sending = true,id_sync_sercon = '{$uid}' WHERE id = {$po->id}
            ")->execute();
        } else if ($po->tipe_rekap == IntPurchaseOrder::PBM) {
            $query = Yii::$app->db->createCommand("
                UPDATE penerimaanbarang_r SET is_sending = true,id_sync_sercon = '{$uid}' WHERE id = {$po->id}
            ")->execute();
        }

        return $data;
    }

    public function actionPurchaseOrderLine()
    {
        $uid = Yii::$app->request->get('uid',null);
        if(is_null($uid)){
            throw new \Exception("UID tidak boleh kosong", 1);
        }
    	$poline = IntPurchaseOrderLine::find()->andWhere([
            'is_sending' => false
        ])->orderBy([
            'date_planned' => SORT_ASC
        ])->one();

        if (empty($poline)) {
            return [
                'status' => 422,
                'messages' => 'Tidak ada data yang di proses'
            ];
        }

        $data = $poline->attributes;

        if ($poline->tipe_rekap == IntPurchaseOrderLine::POS || $poline->tipe_rekap == IntPurchaseOrderLine::PBS) {
            $query = Yii::$app->db->createCommand("
                UPDATE penerimaansuppdetail_r SET is_sending = true,id_sync_sercon = '{$uid}' WHERE id = {$poline->id}
            ")->execute();
        } else if ($poline->tipe_rekap == IntPurchaseOrderLine::POM) {
            $query = Yii::$app->db->createCommand("
                UPDATE penerimaanobatdetail_r SET is_sending = true,id_sync_sercon = '{$uid}' WHERE id = {$poline->id}
            ")->execute();
        } else if ($poline->tipe_rekap == IntPurchaseOrderLine::PBM) {
            $query = Yii::$app->db->createCommand("
                UPDATE penerimaanbarangdetail_r SET is_sending = true,id_sync_sercon = '{$uid}' WHERE id = {$poline->id}
            ")->execute();
        }

        return $data;

    }

    public function actionCallbackPurchaseOrder()
    {
    	$request = Yii::$app->request;

        if(isset($data['is_empty']) && $data['is_empty'] == true){
            return [
                'message' => 'Tidak Ada Yang Diproses'
            ];
        }

        $data = $request->post('data');
        $payload = isset($data['payload']) ? $data['payload'] :[];
        $isError = isset($data['is_error']) ? $data['is_error'] :false;
        $isSent = $isError ? false : true;
        
        /*
        if (!$isSent) {
            $payload = $request->post('error_payload');
        }
        */
        
        $uidSercon = $request->post('uid');
        $idRekap = isset($payload['id']) ? $payload['id'] : null;
        $tipeRekap = isset($payload['tipe_rekap']) ? $payload['tipe_rekap'] : null;
        if (!empty($idRekap)) {
            $isSent = true;
            if ($isError) {
                $isSent = false;
            }
            
            if ($tipeRekap === IntPurchaseOrder::POS || $tipeRekap === IntPurchaseOrder::PBS) {
                $query = Yii::$app->db->createCommand("
                    UPDATE penerimaansupp_r 
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
                $this->logWarning(['message'=>'POS update','payload'=>$payload]);
                return [
                    'messages' => 'data berhasil di update'
                ];
            }else if ($tipeRekap == IntPurchaseOrder::POM) {
                $query = Yii::$app->db->createCommand("
                    UPDATE penerimaanobat_r 
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
                $this->logWarning(['message'=>'POM update','payload'=>$payload]);
                return [
                    'messages' => 'data berhasil di update'
                ];
            }else if ($tipeRekap == IntPurchaseOrder::PBM) {
                $query = Yii::$app->db->createCommand("
                    UPDATE penerimaanbarang_r 
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
                $this->logWarning(['message'=>'PBM update','payload'=>$payload]);
                return [
                    'messages' => 'data berhasil di update'
                ];
            }
        }
        throw new \Exception("Error Processing Request");
    }

    public function actionCallbackPurchaseOrderLine()
    {
    	$request = Yii::$app->request;

        if(isset($data['is_empty']) && $data['is_empty'] == true){
            return [
                'message' => 'Tidak Ada Yang Diproses'
            ];
        }
        
        $isError = $request->post('is_error');
        $data = $request->post('data');
        $payload = isset($data['payload']) ? $data['payload'] :[];
        if (!$isError && isset($data['is_error'])) {
            $isError = $data['is_error'];
        }
        $isSent = $isError ? false : true;
        
        $uidSercon = $request->post('uid');
        $idRekap = isset($payload['id']) ? $payload['id'] : null;
        $tipeRekap = isset($payload['tipe_rekap']) ? $payload['tipe_rekap'] : null;
        if (!empty($idRekap)) {
            $isSent = true;
            if ($isError) {
                $isSent = false;
            }
            
            if ($tipeRekap === IntPurchaseOrder::POS || $tipeRekap === IntPurchaseOrder::PBS) {
                $query = Yii::$app->db->createCommand("
                    UPDATE penerimaansuppdetail_r 
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
                return [
                    'messages' => 'data berhasil di update'
                ];
            }else if ($tipeRekap == IntPurchaseOrder::POM) {
                $query = Yii::$app->db->createCommand("
                    UPDATE penerimaanobatdetail_r 
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
                return [
                    'messages' => 'data berhasil di update'
                ];
            }else if ($tipeRekap == IntPurchaseOrder::PBM) {
                $query = Yii::$app->db->createCommand("
                    UPDATE penerimaanbarangdetail_r 
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
                return [
                    'messages' => 'data berhasil di update'
                ];
            }
        }
        throw new \Exception("Error Processing Request");
    }
}
