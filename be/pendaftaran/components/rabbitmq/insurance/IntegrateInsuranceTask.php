<?php

namespace app\components\rabbitmq\insurance;

use Yii;
use app\modules\v1\models\AsuransiT;
use app\modules\v1\models\InfoPendaftaranOlView;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\Penjamin;
use Doco\models\LogAsuransiTransaksi;
use Doco\models\pendaftaran\PendaftaranOnline;
use Doco\rabbitmq\task\IntegrasiTask;
use GuzzleHttp\Client;
use yii\helpers\ArrayHelper;

class IntegrateInsuranceTask extends IntegrasiTask
{
    public function prosesSync()
    {
        try {
            $params = Yii::$app->params;
            $urlBackend = isset($params['url_backend']) ? $params['url_backend'] : 'http://web:8858/';
            $pendaftaranId = ArrayHelper::getValue($this->data_pendaftaran, 'pendaftaran_id', null);
            $guzzle = new Client([
                'base_uri' => $urlBackend . 'penjaminasuransi/v1/',
                'verify' => false,
                'headers' => [
                    'user-agent' => 'cli',
                ]
            ]);

            /**
             * Check if pendaftaran online is exist.
             */
            $payloadPendaftaranOnline = $this->getPayloadPendaftaranOnline();
            $nokartu = isset($payloadPendaftaranOnline) ? $payloadPendaftaranOnline->no_asuransi : ArrayHelper::getValue($this->data_peserta, 'nokartu', null);
            $benefitCode = isset($payloadPendaftaranOnline) ? $payloadPendaftaranOnline->benefit_code : $this->kodebenefit;            
            $transactionId = isset($payloadPendaftaranOnline) ? $payloadPendaftaranOnline->transaction_id : null;

            $payload = [
                "tanggalmasuk" => date("Y-m-d"),
                "nokartu" => $nokartu,
                "kodebenefit" => $benefitCode,
                "asalrujukan" => "",
                "cobbpjs" => "0",
                "nomorsep" => "",
                "keterangan" => ArrayHelper::getValue($this->data_pendaftaran, 'no_pendaftaran', null),
                "notransaksiprovider" => ArrayHelper::getValue($this->data_pendaftaran, 'no_pendaftaran', null),
                "inacbgscode" => "0",
                "inacbgsamount" => "0",
                "transaction_id" => $transactionId
            ];

            $payloadRequest = [
                'no_kartu' => $payload['nokartu'],
                'penjamin_id' => $this->penjamin_id,
            ];

            $penjaminData = Penjamin::find()
                ->select([
                    'penjamin_m.penjamin_id',
                    'penjamin_m.penjamin_nama',
                    'penjamin_m.konfigasuransi_id',
                    'konfigasuransi_k.provider_id'
                ])
                ->where([
                    'penjamin_m.penjamin_id' => $this->penjamin_id,
                    'penjamin_m.is_deleted' => false,
                    'penjamin_m.is_active' => true,
                    'konfigasuransi_k.is_deleted' => false,
                    'konfigasuransi_k.is_active' => true
                ])
                ->join('INNER JOIN', 'konfigasuransi_k', 'konfigasuransi_k.konfigasuransi_id = penjamin_m.konfigasuransi_id')
                ->asArray()->one();
                
            if(empty($penjaminData)) {
                return Yii::error(json_encode([
                    'status' => 500,
                    'message' => "Asuransi Tidak Tersedia / Terintegrasi !",
                    'data_pendaftaran' => $this->data_pendaftaran
                ]));
            }

            $result = $guzzle->post('inf-integrasi-asuransi/pendaftaran', [
                'query' => $payloadRequest,
                'form_params' => $payload
            ]);

            $result = json_decode($result->getBody(), true);

            /**
             * Validate if error when integration with vendor
             */
            if (isset($result['response']['response']['status']) && $result['response']['response']['status'] !== 0) {
                return Yii::error(json_encode([
                    'status' => 500,
                    'message' => "Gagal melakukan proses integrasi",
                    'data' => $result,
                    'data_pendaftaran' => $this->data_pendaftaran
                ]));
            }

            $rawResponse = ArrayHelper::getValue($result, 'response.response');
            $newModel = new AsuransiT();
            $newModel->provider_id = $penjaminData['provider_id'];
            $newModel->penjamin_id = $this->penjamin_id;
            $newModel->no_klaim = ArrayHelper::getValue($result, 'response.response.data.dataPeserta.noklaim');
            $newModel->no_kartu = $nokartu;
            $newModel->no_polis = ArrayHelper::getValue($result, 'response.response.data.dataPeserta.nopolis');
            $newModel->additional_data = json_encode($rawResponse);
            $newModel->additional_pendaftaran = json_encode($this->data_pendaftaran);
            $newModel->benefit_id = $benefitCode;
            $newModel->save();

            $pendaftaran = Pendaftaran::findOne($pendaftaranId);
            $pendaftaran->asuransi_id = $newModel->asuransi_id;
            $pendaftaran->save();

            $transaksiKirim = new LogAsuransiTransaksi();
            $transaksiKirim->asuransi_id = $newModel->asuransi_id;
            $transaksiKirim->save();
            
            return Yii::error(json_encode([
                'status' => 200,
                'raw' => $result,
                'data' => $newModel->attributes,
                'data_pendaftaran' => $this->data_pendaftaran
            ]));
        } catch (\Exception $th) {
            return Yii::error(json_encode([
                'status' => 500,
                'message' => $th->getMessage(),
            ]));
        }
    }

    protected function getPayloadPendaftaranOnline()
    {
        return InfoPendaftaranOlView::find()->where([
            'pendaftaranol_id' => $this->pendaftaranol_id
        ])->one();
    }
}
