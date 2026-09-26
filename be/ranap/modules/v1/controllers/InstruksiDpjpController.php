<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoMessages;

use app\modules\v1\payload\InstruksiDpjp;

use app\modules\v1\models\Cppt;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\PindahKamar;
use app\modules\v1\models\InfoPasienRiView;

use GuzzleHttp\Exception\RequestException;

class InstruksiDpjpController extends DocoActiveController
{
    public $modelClass = '';

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
        $model = new InstruksiDpjp;
        $model->attributes = $request->post();
        if ($model->validate()) {
            $qPendaftaran = InfoPasienRiView::find()->select([
                'penjamin_id',
                'pasienadmisi_id',
                'kelaspelayanan_id',
                'ruangan_id',
                'pasien_id',
                'no_pendaftaran',
                'is_pasientitipan',
                'kelas_ditagihkan_id',
                'is_stoppasientitipan'
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
            $kelasPelayananId = $qPendaftaran['kelaspelayanan_id'];
            
            /** Cek Pasien titip */
            $pasienTitipan = [];
            $pasienTitipan = PindahKamar::getKelasTitipan($model->pendaftaran_id);

            if(!empty($pasienTitipan)) {
                if($pasienTitipan[0]['is_pasientitipan'] == true){
                    if($pasienTitipan[0]['is_stoptitipan'] != true){
                        $kelasPelayananId = !empty($pasienTitipan[0]['kelas_ditagihkan_id']) ? $pasienTitipan[0]['kelas_ditagihkan_id'] :$kelasPelayananId; 
                    } else {
                        $kelasPelayananId = $pasienTitipan[0]['kelaspelayanan_id']; 
                    }
                }else{
                    $kelasPelayananId = $pasienTitipan[0]['kelaspelayanan_id']; 
                }
            } else if($qPendaftaran['is_pasientitipan'] == true) {
                if($qPendaftaran['is_stoppasientitipan'] != true){
                    $kelasPelayananId = $qPendaftaran['kelas_ditagihkan_id'];
                }else{
                    $kelasPelayananId = $qPendaftaran['kelaspelayanan_id'];
                }
            }

            try {
                $mCppt = new Cppt;
                $mCppt->pendaftaran_id = $model->pendaftaran_id;
                $mCppt->pasienadmisi_id = $qPendaftaran['pasienadmisi_id'];
                $mCppt->pasien_id = $qPendaftaran['pasien_id'];
                $mCppt->ruangan_id = $model->ruangan_id;
                $mCppt->tgl_cppt = date('Y-m-d H:i:s');
                $mCppt->pegawai_id = Yii::$app->jwt->user->pegawai_id;
                $mCppt->instruksi = nl2br($model->instruksi);
                $mCppt->pemberi_instruksi_id = $model->pemberi_instruksi_id;
                $mCppt->kamarruangan_id = $model->kamarruangan_id;
                $mCppt->kamartempattidur_id = $model->kamartempattidur_id;
                $mCppt->kamar_tempattidur  = $model->kamar_tempattidur;

                if ($mCppt->save()) {
                    if($model->fee_konsul != ''){
                        $restKasir = Yii::$app->docoRest->kasir;
                        $request = $restKasir->post('api/billing', [
                            'form_params' => [
                                'kelaspelayanan_id' => $kelasPelayananId,
                                'penjamin_id' => $qPendaftaran['penjamin_id'],
                                'pendaftaran_id' => $model->pendaftaran_id,
                                'no_pendaftaran' => $qPendaftaran['no_pendaftaran'],
                                'ruangan_id' => $model->ruangan_id,
                                'instalasi_id' => $qRuangan['instalasi_id'],
                                'tgl_transaksi' => date('Y-m-d H:i:s'),
                                'detail_tindakan' => [
                                    [
                                        'dokter_id' => $model->pemberi_instruksi_id,
                                        'perawat_id' => '',
                                        'tipepaket_id' => '',
                                        'is_cyto' => false,
                                        'qty' => 1,
                                        'daftartindakan_id' => $model->fee_konsul,
                                    ]
                                ],
                            ]
                        ]);
                    }

                    $transaction->commit();
                    return DocoHelpers::callback(DocoMessages::KEY_SUC_SYSTEM);
                } 

                return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                    'data' => $mCppt->errors
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