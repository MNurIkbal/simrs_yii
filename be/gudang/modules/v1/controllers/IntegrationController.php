<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\StockOutView;
use app\modules\v1\models\StockOutDetailView;
use app\modules\v1\models\IntPendaftaranBmhpR;
use app\modules\v1\models\IntPenjualanResepR;
use app\modules\v1\models\StockReturnView;
use app\modules\v1\models\StockReturnDetailView;
use app\modules\v1\models\StockScrapView;

class IntegrationController extends DocoActiveController
{
    public $modelClass = '';

    public function actions()
    {
        $actions['stock-out']                       = 'app\modules\v1\actions\Integration\StockOutAction';
        $actions['callback-stock-out']              = 'app\modules\v1\actions\Integration\CallbackStockOutAction';
        $actions['store-consumption']               = 'app\modules\v1\actions\Integration\StoreConsumptionAction';
        $actions['callback-store-consumption']      = 'app\modules\v1\actions\Integration\CallbackStoreConsumptionAction';
        $actions['stock-return']                    = 'app\modules\v1\actions\Integration\StockReturnAction';
        $actions['stock-return-detail']             = 'app\modules\v1\actions\Integration\StockReturnDetailAction';
        $actions['callback-stock-return']           = 'app\modules\v1\actions\Integration\CallbackStockReturnAction';
        $actions['callback-stock-return-detail']    = 'app\modules\v1\actions\Integration\CallbackStockReturnDetailAction';
        $actions['resync'] = 'app\modules\v1\actions\Integration\ResyncAction';
        $actions['get-data-transaksi'] = 'app\modules\v1\actions\Integration\GetDataTransaksiAction';
        return $actions;
    }

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs['stock-out'] = ["GET"];
        $verbs['stock-out-detail'] = ["GET"];
        return $verbs;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        unset($behaviors['access']);
        return $behaviors;
    }

    public function actionGetData($model)
    {
        try {
            $request = Yii::$app->request;

            switch ($model) {
                case 'stockout':
                    $view = new StockOutView;
                    break;
                case 'stockoutdetail':
                    $view = new StockOutDetailView;
                    break;
                case 'stockreturn':
                    $view = new StockReturnView;
                    break;
                case 'stockreturndetail':
                    $view = new StockReturnDetailView;
                    break;
                case 'storeconsumption':
                    $view = new StockScrapView;
                    break;
                
                default:
                    throw new \Exception("Undefined Model", 1);
                    break;
            }
            $query = $view::find(true);

            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['tanggal_transaksi'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tanggal_transaksi']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tanggal_transaksi']);
                }
                if (isset($_GET['advanced-filter']['tipe_rekap'])) {
                    $query->andWhere(['tipe_rekap' => trim($_GET['advanced-filter']['tipe_rekap'])]);
                    unset($_GET['advanced-filter']['tipe_rekap']);
                }
                if (isset($_GET['advanced-filter']['status_proses'])) {
                    $query->andWhere(['status_proses' => trim($_GET['advanced-filter']['status_proses'])]);
                    unset($_GET['advanced-filter']['status_proses']);
                }
            }

            $query->andWhere(['between', 'tanggal_transaksi', $start, $end]);

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

    public function actionStockOutDetail()
    {
        $uid = Yii::$app->request->get('uid',null);
        if(is_null($uid)){
            throw new \Exception("UID tidak boleh kosong", 1);
        }
    	$stockOutDetail = StockOutDetailView::find()->andWhere([
            'is_sending' => false
        ])
        ->orderBy([
            'id' => SORT_ASC
        ])
        ->one();

        if (empty($stockOutDetail)) {
            return [
                'status' => 422,
                'messages' => 'Tidak ada data yang di proses'
            ];
        }

        $data = $stockOutDetail->attributes;

        $query = Yii::$app->db->createCommand("
            UPDATE int_obatalkespasien_r SET is_sending = true,id_sync_sercon = '{$uid}' WHERE id = {$stockOutDetail->id}
        ")->execute();

        return $data;

    }

    public function actionCallbackStockOutDetail()
    {
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
            $syncRespon = json_encode([
                            'uid' => $uidSercon,
                            'result'=> isset($data['response']) ? $data['response'] : null,
                            'payload'=> $payload
                        ]);

            if (!empty($idRekap)) {
                $query = Yii::$app->db->createCommand("
                    UPDATE int_obatalkespasien_r
                        SET is_sent = :is_sent, id_sync_sercon = :id_sync_sercon , sync_respon = :sync_respon
                    WHERE id = :id
                ")
                ->bindValue(':is_sent', !$isError)
                ->bindValue(':id_sync_sercon', $uidSercon)
                ->bindValue(':id', $idRekap)
                ->bindValue(':sync_respon', $syncRespon)
                ->execute();
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
