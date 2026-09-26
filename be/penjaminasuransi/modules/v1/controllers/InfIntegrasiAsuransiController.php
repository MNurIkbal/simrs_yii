<?php

namespace app\modules\v1\controllers;

/**
 * @author: [Maulana Muhammad Rizky]
 * A product of PT. Sirs
 * Powered by Sirs
 */

use Doco\components\DocoAccessRule;
use Yii;
use Doco\components\DocoActiveController;
use Doco\components\DocoJwtHttpBearerAuth;

class InfIntegrasiAsuransiController extends DocoActiveController
{
    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["pengesahan"] = ["POST"];
        return $verbs;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => DocoJwtHttpBearerAuth::className(),
            'except' => ['bg-pendaftaran', 'pendaftaran'],
        ];

        $behaviors['access'] = [
            'class' => DocoAccessRule::className(),
            'except' => ['bg-pendaftaran', 'pendaftaran'],
        ];

        return $behaviors;
    }

    public function actionAuthentication()
    {
        try {
            $request = Yii::$app->request;
            $penjaminId = $request->get('penjamin_id');
            $insuranceClient = Yii::$app->assuransiClient->setProvider($penjaminId);
            $response = $insuranceClient->Authentication();

            return [
                'response' => $response
            ];
        } catch (\Exception $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionGetReferensi()
    {
        $insuranceClient = Yii::$app->assuransiClient->setProvider(0);
        $response = $insuranceClient->GetReferensi();

        return [
            'response' => $response
        ];
    }

    public function actionGetDaftarKunjungan()
    {
        try {
            $request = Yii::$app->request;
            $penjaminId = $request->get('penjamin_id');
            $insuranceClient = Yii::$app->assuransiClient->setProvider($penjaminId);

            $data = [
                'bulanlayanan' => $request->get('bulanlayanan'),
                'kodeperusahaan' => $request->get('kodeperusahaan'),
                'kodeklien' => $request->get('kodeklien'),
            ];

            $response = $insuranceClient->DaftarKunjungan($data);

            Yii::$app->response->statusCode = 200;
            return [
                'response' => $response
            ];
            echo json_encode($verbs);
            die;
        } catch (\Throwable $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionGetReferensiBenefit()
    {
        try {
            $request = Yii::$app->request;
            $noKartu = $request->get('no_kartu');
            $penjaminId = $request->get('penjamin_id');
            $insuranceClient = Yii::$app->assuransiClient->setProvider($penjaminId);

            $data = [
                'no_kartu' => $noKartu
            ];

            $response = $insuranceClient->ReferensiBenefitPeserta($data);

            return [
                'response' => $response
            ];
        } catch (\Throwable $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionCekEligiblePeserta()
    {
        try {
            $request = Yii::$app->request;
            $noKartu = $request->get('no_kartu');
            $penjaminId = $request->get('penjamin_id');
            $insuranceClient = Yii::$app->assuransiClient->setProvider($penjaminId);
            $data = [
                'no_kartu' => $noKartu
            ];

            $response = $insuranceClient->CekEligiblePeserta($data);

            return [
                'response' => $response
            ];
        } catch (\Throwable $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionCetakStrukPendaftaran()
    {
        try {
            $request = Yii::$app->request;
            $noKartu = $request->get('no_klaim');
            $penjaminId = $request->get('penjamin_id');
            $insuranceClient = Yii::$app->assuransiClient->setProvider($penjaminId);
            $data = [
                'no_klaim' => $noKartu
            ];

            $response = $insuranceClient->CetakStrukPendaftaran($data);

            // Membuka file PDF di browser
            $fileName = isset($response['path']) ? $response['path'] : '';
            if (file_exists($fileName)) {
                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="' . $fileName . '"');
                header('Content-Transfer-Encoding: binary');
                header('Accept-Ranges: bytes');

                readfile($fileName);
            }

            return [
                'status' => 200,
                'message' => 'File not exits or corrupt !',
            ];
        } catch (\Throwable $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionCetakSuratJaminan()
    {
        try {
            $request = Yii::$app->request;
            $noSurat = $request->get('nosuratjaminan');
            $penjaminId = $request->get('penjamin_id');
            $insuranceClient = Yii::$app->assuransiClient->setProvider($penjaminId);
            $data = [
                'nosuratjaminan' => $noSurat
            ];

            $response = $insuranceClient->CetakSuratJaminan($data);

            // Membuka file PDF di browser
            $fileName = isset($response['path']) ? $response['path'] : '';
            if (file_exists($fileName)) {
                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="' . $fileName . '"');
                header('Content-Transfer-Encoding: binary');
                header('Accept-Ranges: bytes');

                readfile($fileName);
            }

            return [
                'status' => 200,
                'message' => 'File not exits or corrupt !',
            ];
        } catch (\Throwable $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionCetakStrukPengesahan()
    {
        try {
            $request = Yii::$app->request;
            $no_klaim = $request->get('no_klaim');
            $penjaminId = $request->get('penjamin_id');
            $insuranceClient = Yii::$app->assuransiClient->setProvider($penjaminId);
            $data = [
                'no_klaim' => $no_klaim
            ];

            $response = $insuranceClient->CetakStrukPengesahan($data);

            // Membuka file PDF di browser
            $fileName = isset($response['path']) ? $response['path'] : '';
            if (file_exists($fileName)) {
                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="' . $fileName . '"');
                header('Content-Transfer-Encoding: binary');
                header('Accept-Ranges: bytes');

                readfile($fileName);
            }

            return [
                'status' => 200,
                'message' => 'File not exits or corrupt !',
            ];
        } catch (\Throwable $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionPengesahan()
    {
        try {
            $request = Yii::$app->request;
            $data = $request->post();
            $penjaminId = $request->get('penjamin_id');
            $insuranceClient = Yii::$app->assuransiClient->setProvider($penjaminId);

            $response = $insuranceClient->Discharging($data);

            Yii::$app->response->statusCode = 200;
            return [
                'response' => $response
            ];
        } catch (\Throwable $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionPendaftaran()
    {
        try {
            $request = Yii::$app->request;
            $penjaminId = $request->get('penjamin_id');
            $insuranceClient = Yii::$app->assuransiClient->setProvider($penjaminId);

            $payloadRequest = [
                "transaction_id" => $request->post('transaction_id'),
                "tanggalmasuk" => $request->post('tanggalmasuk'),
                "nokartu" => $request->post('nokartu'),
                "kodebenefit" => $request->post('kodebenefit'),
                "statusrujukan" => $request->post('statusrujukan'),
                "asalrujukan" => $request->post('asalrujukan'),
                "cobbpjs" => $request->post('cobbpjs'),
                "nomorsep" => $request->post('nomorsep'),
                "keterangan" => $request->post('keterangan'),
                "notransaksiprovider" => $request->post('notransaksiprovider'),
                "inacbgscode" => $request->post('inacbgscode'),
                "inacbgsamount" => $request->post('inacbgsamount')
            ];

            $response = $insuranceClient->Pendaftaran($payloadRequest);

            Yii::$app->response->statusCode = 200;
            return [
                'response' => $response
            ];
        } catch (\Exception $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionPembatalan()
    {
        try {
            $request = Yii::$app->request;
            $penjaminId = $request->get('penjamin_id');
            $insuranceClient = Yii::$app->assuransiClient->setProvider($penjaminId);
            $data = [
                'noklaim' => $request->post('noklaim'),
                'keterangan' => $request->post('keterangan')
            ];

            $response = $insuranceClient->Pembatalan($data);

            Yii::$app->response->statusCode = 200;
            return [
                'response' => $response
            ];
        } catch (\Throwable $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionCekKoneksi()
    {
        try {
            $request = Yii::$app->request;
            $providerId = ucfirst(strtolower($request->get('provider')));
            $baseUrl = $request->get('base_url');
            $auth = $request->get('auth');

            if (
                ! empty($providerId) &&
                ! empty($baseUrl) &&
                ! empty($auth)
            ) {
                $providerData = [
                    'provider' => $providerId,
                    'base_url' => $baseUrl,
                    'auth' => $auth
                ];

                $insuranceClient = Yii::$app->assuransiClient->checkConnection($providerData);

                if ($insuranceClient === false) {
                    Yii::$app->response->statusCode = 500;
                    return [
                        'status' => 500,
                        'message' => 'Connection Failed Or Provider Not Found !'
                    ];
                }

                $response = $insuranceClient->Authentication(true);

                $responseCode = isset($response['code']) ? $response['code'] : 500;
                Yii::$app->response->statusCode = $responseCode;
                if (isset($response['code']) && $response['code'] == 200) {
                    return [
                        'title' => 'Connection Successful !',
                        'status' => $responseCode,
                        'responseAuth' => $response,
                        'message' => 'Connection Successful !'
                    ];
                }

                return [
                    'title' => 'Connection Failed !',
                    'status' => $responseCode,
                    'responseAuth' => $response,
                    'message' => 'Connection Failed & Please Check Your Parameters !'
                ];
            }

            Yii::$app->response->statusCode = 401;
            return [
                'status' => 401,
                'title' => 'Connection Failed !',
                'responseAuth' => null,
                'message' => 'Connection Failed & Please Check Your Parameters !'
            ];
        } catch (\Exception $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'title' => 'Connection Failed !',
                'message' => 'Connection Failed & Please Check Your Parameters !'
            ];
        }
    }

    public function actionGetPenjaminTerintegrasi()
    {
        try {
            $result = Yii::$app->db->createCommand("
                SELECT 
                    penjamin_m.penjamin_id,
                    penjamin_m.penjamin_nama,
                    kk.konfigasuransi_id,
                    kk.provider_id 
                FROM penjamin_m 
                JOIN konfigasuransi_k kk ON kk.konfigasuransi_id = penjamin_m.konfigasuransi_id 
                WHERE penjamin_m.konfigasuransi_id IS NOT NULL
                AND kk.is_deleted IS FALSE 
                AND kk.is_active IS TRUE
            ")->queryAll();

            Yii::$app->response->statusCode = 200;
            return [
                'status' => 200,
                'message' => 'Get Penjamin Success !',
                'data' => $result
            ];
        } catch (\Exception $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'message' => 'Connection Failed & Please Check Your Parameters !'
            ];
        }
    }
}
