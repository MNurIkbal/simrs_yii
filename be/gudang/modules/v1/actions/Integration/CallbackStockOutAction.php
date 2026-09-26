<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\Integration;

use Yii;
use yii\base\Action;
use app\modules\v1\models\StockOutView;
use app\modules\v1\models\IntPendaftaranBmhpR;
use app\modules\v1\models\IntPenjualanResepR;

class CallbackStockOutAction extends Action {
    public function run() {
    	$request = Yii::$app->request;
        $data = $request->post('data',null);

        try{
            if(is_null($data)){
                throw new \Exception("Data Kosong", 1);
            }

            if(isset($data['is_empty']) && $data['is_empty'] == true){
                return [
                    'message' => 'Tidak Ada Yang Diproses'
                ];
            }

            $isError = (!empty($data['is_error']) && isset($data['is_error'])); 
            $uidSercon = $request->post('uid');
            $body = isset($data['body']) ? $data['body'] :[];
            $payload = isset($data['payload']) ? $data['payload'] :[];            
            $idRekap = isset($body['id']) ? $body['id'] : null;
            $tipeRekap = isset($body['tipe_rekap']) ? $body['tipe_rekap'] : null;
            if (!empty($idRekap)) {                
                if ($tipeRekap == StockOutView::BMHP) {
                    $modelBmhp = IntPendaftaranBmhpR::find()->where(['id'=>$idRekap])->one();
                    if(!$isError){
                        $modelBmhp->is_sent = true;
                    }
                    $modelBmhp->sync_respon = json_encode([
                        'uid'=>$uidSercon,
                        'result'=> isset($data['response']) ? $data['response'] : null,
                        'payload'=> $payload
                    ]);
                    if($modelBmhp->save()){
                        return [
                            'messages' => 'data berhasil di update'
                        ];
                    }
                    $this->controller->logWarning(['message'=>'BMHP Stockout Save Failed','payload'=>$payload]);
                }else if ($tipeRekap == StockOutView::RESEP || $tipeRekap == StockOutView::RACIKAN_BATAL) {
                    $modelResep = IntPenjualanResepR::find()->where(['id'=>$idRekap])->one();
                    if(!$isError){
                        $modelResep->is_sent = true;
                    }
                    $modelResep->sync_respon = json_encode([
                        'uid'=>$uidSercon,
                        'result'=> isset($data['response']) ? $data['response'] : null,
                        'payload'=> $payload
                    ]);
                    if($modelResep->save()){
                        return [
                            'messages' => 'data berhasil di update'
                        ];
                    }
                    $this->controller->logWarning(['message'=>'Resep Stockout Save Failed','payload'=>$payload]);
                }
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