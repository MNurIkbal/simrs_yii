<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoMessages;

use app\modules\v1\payload\VerbalOrder;

use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\SoapRj;
use app\modules\v1\models\Ruangan;

use GuzzleHttp\Exception\RequestException;
use Doco\Services\KasirService;

class VerbalOrderController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\AsesmenMedisRD';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs['create'] = ["POST"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        return $actions;
    }

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new VerbalOrder;
        $model->attributes = $request->post();


        
        if ($model->validate()) {
            $qPendaftaran = Pendaftaran::find()->select([
                'penjamin_id',
                'kelaspelayanan_id',
                'ruangan_id',
                'pasien_id',
                'no_pendaftaran',
            ])->andWhere([
                'pendaftaran_id' => $model->pendaftaran_id
            ])->asArray()->one();

            if (empty($qPendaftaran)) {
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                    'text' => 'Pendaftaran tidak valid'
                ]);
            }

            $qRuangan = Ruangan::find()->select([
                'instalasi_id'
            ])->andWhere([
                'ruangan_id' => $model->ruangan_id
            ])->asArray()->one();

            if (empty($qRuangan)) {
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                    'text' => 'Ruangan tidak valid'
                ]);
            }

            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            try {
                $mSoapRj = new SoapRj;
                $mSoapRj->pendaftaran_id = $model->pendaftaran_id;
                $mSoapRj->pasien_id = $qPendaftaran['pasien_id'];
                $mSoapRj->ruangan_id = $model->ruangan_id;
                $mSoapRj->tgl_soaprj = date('Y-m-d H:i:s');
                $mSoapRj->pegawai_id = Yii::$app->jwt->user->pegawai_id;
                $mSoapRj->instruksi = nl2br($model->instruksi);
                $mSoapRj->pemberi_instruksi_id = $model->pemberi_instruksi_id;
                if ($mSoapRj->validate() && $mSoapRj->save()) {
                     $detailIntegration[] = [
                        'dokter_id'         => $model->pemberi_instruksi_id,
                        'perawat_id'        => '',
                        'tipepaket_id'      => '',
                        'is_cyto'           => false,
                        'qty'               => 1,
                        'daftartindakan_id' => $model->fee_konsul,
                     ];
                    $integrateKasir = (new KasirService)->tagihanAmbulan([
                        'kelaspelayanan_id' => $qPendaftaran['kelaspelayanan_id'],
                        'penjamin_id'       => $qPendaftaran['penjamin_id'],
                        'pendaftaran_id'    => $model->pendaftaran_id,
                        'no_pendaftaran'    => $qPendaftaran['no_pendaftaran'],
                        'ruangan_id'        => $model->ruangan_id,
                        'instalasi_id'      => $qRuangan['instalasi_id'],
                        'tgl_transaksi'     => date('Y-m-d H:i:s'),
                    ], $detailIntegration);

                    if(isset($integrateKasir['meta']['result'])) {
                        $result = $integrateKasir['meta']['result'];
                        if($result == 'failed') {
                            $message = isset($integrateKasir['message']) ? $integrateKasir['message'] : 'Terjadi kesalahan saat integerasi dengan kasir';
                            return $this->responseJson(400, $message, [], [
                                'title' => "Proses tidak dapat dilanjutkan!"
                            ]);
                        }
                    }

                    $transaction->commit();
                    return DocoHelpers::callback(DocoMessages::KEY_SUC_SYSTEM);
                } 

                return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                    'data' => $mSoapRj->errors
                ]);
            } catch (RequestException $e) {
                $transaction->rollback();
                $response = $e->getResponse();
                $body = json_decode($response->getBody(),true);
                $message = isset($body['response']['message']) ? $body['response']['message'] : null;
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                    'text' => $message
                ]);
            } catch (\yii\db\Exception $e) {
                $transaction->rollback();
                \Yii::$app->response->statusCode = 500;
                return [
                    'message' => $e->getMessage()
                ];
            } catch (\Exception $e) {
                $transaction->rollback();
                \Yii::$app->response->statusCode = 500;
                return [
                    'message' => $e->getMessage()
                ];
            }
        }

        return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
            'data' => $model->errors
        ]);
    }
}