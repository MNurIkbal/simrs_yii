<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\base\DynamicModel;
use app\modules\v1\models\HasilPemeriksaanRadView;
use app\modules\v1\models\HasilPemeriksaanRad;
use app\modules\v1\models\PasienMasukPenunjangT;
use app\modules\v1\models\TindakanPelayananT;
use Doco\components\DocoActiveController;
use Doco\Services\InternalService;
use app\modules\v1\models\HasilBridgingRadiologiT;
use Doco\components\DocoConstants;
use yii\helpers\ArrayHelper;

class IntegrasiController extends DocoActiveController
{
    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["create"] = ["POST"];
        $verbs["delete"] = ["DELETE"];
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
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }

    /**
     * API for save bridging from RIS GE
     * 
     * @param String var
     * @return JSON
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionSaveBridging()
    {
        $payload = [
            'message' => 'Payload Accepted',
            'payload' => Yii::$app->request->post(),
            'time' => date('Y-m-d H:i:s')
        ];

        Yii::error(
            'Message : Payload Accepted --||--Line : 55 --||--File : IntegrasiController.php --||--API URL : ' . Yii::$app->request->getPathInfo() . '--||--Method : POST --||--Payload : ' . json_encode($payload),
            'server-error'
        );

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            $data = (object) $request->post('data', []);
            $data = $data->Data;
            $orderNo = ArrayHelper::getValue($data, 'order_no');
            $model = new HasilBridgingRadiologiT;
            if(!empty($orderNo)) {
                $cekExistData = HasilBridgingRadiologiT::find()->where(['order_no' => $orderNo])->one();
                $model = $cekExistData ? $cekExistData : new HasilBridgingRadiologiT;
                if($cekExistData) {
                    $model->image_link = ArrayHelper::getValue($data, 'image_link');
                }
                else {
                    $model->attributes = $data;
                    $explodeOrder = explode("-", $model->order_no);
                    if (!isset($explodeOrder[1])) {
                        $getNoRadiologi = $connection->createCommand("
                            SELECT 
                                a.no_masukpenunjang 
                            FROM pasienmasukpenunjang_t a
                            JOIN tindakanpelayanan_t b ON a.pasienmasukpenunjang_id = b.pasienmasukpenunjang_id
                            WHERE b.tindakanpelayanan_id = :trans_id 
                        ")->bindValue(':trans_id', $model->order_no)->queryOne();
    
                        $noPenunjang = isset($getNoRadiologi['no_masukpenunjang']) ? $getNoRadiologi['no_masukpenunjang'] : null;
                        $model->order_no = $noPenunjang . "-" . $model->order_no;
                    }
                    $decodeVal = !empty($model->obv_value_text) ? base64_decode($model->obv_value_text) : null;
                    $model->obv_value_html = str_replace("\n", "<br>", $decodeVal);
    
                    $pattern = "/(?<=Kesan :).*/i";
                    preg_match($pattern, $model->obv_value_html, $kesan);
                    $kesan = isset($kesan[0]) ? $kesan[0] : '';
    
                    $obvValue = $model->obv_value_html;
    
                    $desc = preg_replace("/(kesan :).*/i","", $model->obv_value_html);
                    preg_match("/(?<=-).*/", $model->order_no, $pelayananId);
                    $pelayananId = isset($pelayananId[0]) ? $pelayananId[0] : null;
                    $this->setHasilRad($pelayananId, $desc, $kesan);
    
                    $model->log_id = $request->post('logid', null);
                }

                if ($model->save()) {
                    $transaction->commit();
                    return $this->responseJson(200, 'Data Berhasil Disimpan');
                }
            }
            
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(500, 'Terjadi Kesalahan pada Server');
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(500, 'Terjadi Kesalahan pada Server');
        }
    }


    private function setHasilRad($pelId, $desc, $kesan)
    {
        $modelOrder = Yii::$app->db->createCommand("
            SELECT 
                tindakanpelayanan_t.pendaftaran_id,
                tindakanpelayanan_t.pasienadmisi_id,
                tindakanpelayanan_t.dokterpenanggungjawab_id,
                tindakanpelayanan_t.pasienmasukpenunjang_id,
                pemeriksaanrad_m.pemeriksaanradiologi_id,
                tindakanpelayanan_t.daftartindakan_id
            FROM tindakanpelayanan_t
            LEFT JOIN pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
            WHERE tindakanpelayanan_id = {$pelId}
        ")->queryOne();
        if (!empty($modelOrder)) {
            $mHasil = HasilPemeriksaanRad::find()
                        ->andWhere([
                            'tindakanpelayanan_id' => $pelId
                        ])->one();
            $model = !empty($mHasil) ? $mHasil : new HasilPemeriksaanRad;
            $model->pemeriksaanrad_id = isset($modelOrder['pemeriksaanradiologi_id']) ? $modelOrder['pemeriksaanradiologi_id'] : null;
            $model->penanggungjawab_id = isset($modelOrder['dokterpenanggungjawab_id']) ? $modelOrder['dokterpenanggungjawab_id'] : null;
            $model->pasienmasukpenunjang_id = isset($modelOrder['pasienmasukpenunjang_id']) ? $modelOrder['pasienmasukpenunjang_id'] : null;
            $model->pasienadmisi_id = isset($modelOrder['pasienadmisi_id']) ? $modelOrder['pasienadmisi_id'] : null;
            $model->pendaftaran_id = isset($modelOrder['pendaftaran_id']) ? $modelOrder['pendaftaran_id'] : null;
            $model->tindakanpelayanan_id = $pelId;
            if (!empty($desc)) {
                $model->kesan = '<br>' . $desc;
            }

            if (!empty($kesan)) {
                $model->kesimpulan = '<br>' . $kesan;
            }

            $model->is_ambilfoto = true;
            $model->tgl_ambilfoto = date('Y-m-d H:i:s');
            $model->no_hasilrad = $pelId;
            $model->tgl_hasilrad = date('Y-m-d H:i:s');
            $model->daftartindakan_id = isset($modelOrder['daftartindakan_id']) ? $modelOrder['daftartindakan_id'] : null;
            $model->save();
        }

        if (isset($modelOrder['pasienmasukpenunjang_id'])) {
            $modelPenunjang = PasienMasukPenunjangT::findOne($modelOrder['pasienmasukpenunjang_id']);
            if ($modelPenunjang->status_periksa == DocoConstants::ST_P_PEN_BLM_PRKS) {
                $modelPenunjang->status_periksa = DocoConstants::ST_PERIKSA;
            }
            $modelPenunjang->save();
        }

        return true;
    }

    /**
     * API for handle status order update from RIS GE
     * 
     * @param String var
     * @return JSON
     * @author : Rizqi Fitrianto (rizqi@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionUpdateOrder()
    {
        $cache = Yii::$app->cache;
        /* dibuat log buat mastiin kalo payload berhasil keterima sama api ini */
        $payload = [
            'message' => 'Payload Accepted',
            'payload' => Yii::$app->request->post(),
            'time' => date('Y-m-d H:i:s')
        ];
        Yii::error(
            'Message : Payload Accepted --||--Line : 55 --||--File : IntegrasiController.php --||--API URL : ' . Yii::$app->request->getPathInfo() . '--||--Method : POST --||--Payload : ' . json_encode($payload),
            'server-error'
        );
        $payloadData = (object) Yii::$app->request->post('data', []);
        $order_no = $payloadData->Data['order_no']; /* kombinasi antara no_order dan tindakanpelayanan_id */
        $order_status = $payloadData->Data['order_status']; /* berisi lookup_code, lookup_type = 'status_ris' */
        $procedure_code = $payloadData->Data['procedure_code']; /* kode tindakan */
        // $getCacheOrder = $cache->get($order_no);
        $getCacheOrder = false;
        if (!empty($getCacheOrder)) return $this->responseJson(400, 'Silahkan Coba lagi');
        $cache->set($order_no, true, 120);
        /* validasi buat handle kalo salah satu dari 3 parameter diatas ada yg kosong */
        $apiValidator = DynamicModel::validateData(compact('order_no', 'order_status','procedure_code'), [
            [['order_no', 'order_status', 'procedure_code'], 'required', 'message' => 'Tidak Boleh Kosong!']
        ]);
        if( $apiValidator->hasErrors() ){
            return $this->responseJson(400, 'Silahkan Cek inputan', $apiValidator->getErrors());
        }

        /* mecahin order_no buat dapet no_order dan tindakanpelayanan_id */
        $explodeOrder = explode('-', $order_no);
        $no_order = $explodeOrder[0];
        $tindakanpelayanan_id = $explodeOrder[1];

        $tindakanPelayanan = TindakanPelayananT::findOne($tindakanpelayanan_id);
        $daftartindakanId = ($tindakanPelayanan) ? $tindakanPelayanan['daftartindakan_id'] : null;
        
        /* cari dulu data di hasilpemeriksaanrad_v berdasarkan tindakanpelayanan_id yg didapat dari order_no */
        $getHasilPemeriksaanRad = HasilPemeriksaanRadView::find()->select([
            'hasilpemeriksaanrad_id',
            'tindakanpelayanan_id',
            'pasienmasukpenunjang_id',
        ])->where(['tindakanpelayanan_id' => $tindakanpelayanan_id, 'daftartindakan_id' => $daftartindakanId])->asArray()->one();
        if( empty($getHasilPemeriksaanRad['hasilpemeriksaanrad_id']) ){
            /* kondisi kalo misal ketika update order ini belom ada record di hasilpemeriksaanrad_t */
            /* start ambil data buat ngisi hasilpemeriksaanrad_t data */
            $getDataPasien = Yii::$app->db->createCommand('select 
                        pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                        pasienmasukpenunjang_t.pendaftaran_id,
                        pasienmasukpenunjang_t.pasienadmisi_id,
                        tindakanpelayanan_t.tindakanpelayanan_id,
                        tindakanpelayanan_t.daftartindakan_id,
                        pemeriksaanrad_m.pemeriksaanradiologi_id as pemeriksaanrad_id,
                        (SELECT lookup_id FROM lookup_m WHERE lookup_type = :lookuptype AND lookup_kode = :lookup_code) AS status_pemeriksaan
                        from pasienmasukpenunjang_t
                        RIGHT JOIN tindakanpelayanan_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
                        RIGHT JOIN pemeriksaanrad_m ON pemeriksaanrad_m.daftartindakan_id = tindakanpelayanan_t.daftartindakan_id
                        WHERE tindakanpelayanan_t.tindakanpelayanan_id = :tindakanpelayanan_id AND pasienmasukpenunjang_t.pasienmasukpenunjang_id = :pasienmasukpenunjang_id')
                        ->bindValue(':tindakanpelayanan_id', $getHasilPemeriksaanRad['tindakanpelayanan_id'])
                        ->bindValue(':pasienmasukpenunjang_id', $getHasilPemeriksaanRad['pasienmasukpenunjang_id'])
                        ->bindValue(':lookuptype', 'status_ris')
                        ->bindValue(':lookup_code', $order_status)
                        ->queryOne();
            /* end */
            $hasilPemeriksaan = new HasilPemeriksaanRad;
            $hasilPemeriksaan->attributes = $getDataPasien;
            if( !$hasilPemeriksaan->save() ){
                return $this->responseJson(500, 'Terjadi Kesalahan pada Server');
            }
        } else {
            /* kondisi kalo misal ketika update order ini udah ada record di hasilpemeriksaanrad_t
            *  update hasilpemeriksaanrad_t sesuai sama hasilpemeriksaanrad_id dari view yg atas
            */
            $getLookupId = Yii::$app->db->createCommand('SELECT lookup_id FROM lookup_m WHERE lookup_type = :lookuptype AND lookup_kode = :lookup_code')
                        ->bindValue(':lookuptype', 'status_ris')
                        ->bindValue(':lookup_code',  $order_status)
                        ->queryOne();
            $updateHasilPemeriksaan = HasilPemeriksaanRad::updateAll(['status_pemeriksaan' => $getLookupId['lookup_id']], "hasilpemeriksaanrad_id = {$getHasilPemeriksaanRad['hasilpemeriksaanrad_id']}");
        }
        (new InternalService)->sendTo([
            'Ris' => [
                'ValidateRequest' => [
                    'tindakanpelayanan_id' => $tindakanpelayanan_id
                ]
            ]
        ]);
        return $this->responseJson(200, 'Data Berhasil Didapatkan', $getHasilPemeriksaanRad);
    }
}