<?php

namespace app\components\rabbitmq\integrasi;

use app\modules\v1\models\SyKunjunganPasien;
use SirsCore\businessLogic\AsuransiDataSource;
use Doco\components\DocoConstants;
use Doco\models\DiagnosaView;
use Doco\models\IntegrasiTindakanObatAsuransi;
use Doco\models\LogAsuransiTransaksi;
use Doco\models\Pendaftaran;
use Doco\models\ResumeMedisRi;
use Doco\rabbitmq\task\IntegrasiTask;
use Yii;
use yii\helpers\ArrayHelper;

class IntegrasiAsuransiTask extends IntegrasiTask
{
    /**
     * Main Execute
     * 
     * @author Maulana Muhammad Rizky (maulana.rizky@sirs.co.id)
     * 
     * 3 Desember 2024
     */
    public function prosesSync()
    {
        $pendaftaranId = $this->pendaftaranId;

        $dataAsuransi = Pendaftaran::find()
            ->select([
                'pendaftaran_t.pendaftaran_id',
                'asuransi_t.asuransi_id',
                'asuransi_t.penjamin_id',
                'asuransi_t.no_klaim',
                'asuransi_t.provider_id',
                'log_asuransitransaksi_t.is_batal',
                'asuransi_t.is_cob'
            ])
            ->join('JOIN', 'asuransi_t', 'asuransi_t.asuransi_id = pendaftaran_t.asuransi_id')
            ->join('LEFT JOIN', 'log_asuransitransaksi_t', 'log_asuransitransaksi_t.asuransi_id = asuransi_t.asuransi_id')
            ->where(['pendaftaran_id' => $pendaftaranId])
            ->asArray()
            ->one();

        if (! isset($dataAsuransi['asuransi_id']) || empty($dataAsuransi['asuransi_id'])) {
            Yii::error(json_encode([
                'status' => 404,
                'message' => 'Data Asuransi Tidak ditemukan !'
            ]));

            return false;
        }

        if (isset($dataAsuransi['is_batal']) && $dataAsuransi['is_batal'] == true) {
            Yii::error(json_encode([
                'status' => 404,
                'message' => 'Data Asuransi Sudah dibatalkan !'
            ]));

            return false;
        }

        $logData = LogAsuransiTransaksi::find()
            ->select([
                'kode_diagnosa'
            ])
            ->where(['asuransi_id' => $dataAsuransi['asuransi_id']])
            ->one();

        if (isset($logData->kode_diagnosa)) {
            $diagnosa = json_decode($logData->kode_diagnosa, true);
        } else {
            $diagnosa = $this->getDiagnosa($pendaftaranId, $dataAsuransi['is_cob']);
        }

        if (! isset($diagnosa['kode']) || $diagnosa['kode'] == "") {
            Yii::error(json_encode([
                'status' => 404,
                'message' => 'Data Diagnosa Tidak ditemukan !',
                'diagnosa' => $diagnosa
            ]));

            return false;
        }

        $insuranceClient = Yii::$app->assuransiClient->setProvider($dataAsuransi['penjamin_id']);

        /**
         * Penambahan konfigurasi alias untuk prefix code.
         */
        $getProvider = Yii::$app->assuransiClient->getProvider();
        $config = is_object($getProvider->config) ? json_decode($getProvider->config->url_pendaftaran, true) : [];

        if (! isset($config['config'])) {
            Yii::error(json_encode([
                'status' => 404,
                'message' => 'Config Asuransi URL Pendaftaran Tidak ditemukan / Belum Lengkap !'
            ]));

            return false;
        }

        $prefixCodeActive = filter_var($config['config']['is_alias'], FILTER_VALIDATE_BOOLEAN);
        $prefixActive = $prefixCodeActive ? $config['config']['alias'] : null;

        /**
         * Collecting Item Request.
         */
        $collectionRequest = (new AsuransiDataSource($pendaftaranId))->collectionItemRequest($prefixActive);

        $payloadItemRequest = ArrayHelper::getValue($collectionRequest, 'payloadItemRequest');
        $deleteItemRequest = ArrayHelper::getValue($collectionRequest, 'deleteItemRequest');

        $dataPayload = [
            'no_claim' => $dataAsuransi['no_klaim'],
            'icd' => ArrayHelper::getValue($diagnosa, 'kode'),
            'items_data' => $payloadItemRequest
        ];

        $response = $insuranceClient->ItemRequest($dataPayload);

        $transaksiKirim = LogAsuransiTransaksi::find()->where([
            'asuransi_id' => $dataAsuransi['asuransi_id'],
        ])->one();

        if (empty($transaksiKirim)) {
            $transaksiKirim = new LogAsuransiTransaksi();
        }

        /**
         * Delete item request.
         */
        $this->deleteItemRequest($dataAsuransi, $deleteItemRequest);

        /**
         * Create logging item request.
         */
        $this->createLogItemRequest($dataAsuransi, $payloadItemRequest);

        if (isset($response['data']) && count($response['data']) > 0) {
            $transaksiKirim->is_resend = true;
        }

        $transaksiKirim->asuransi_id = $dataAsuransi['asuransi_id'];
        $transaksiKirim->pegawairesend_id = 1;
        $transaksiKirim->tgl_resend = date('Y-m-d H:i:s');
        $transaksiKirim->kode_diagnosa = json_encode([
            'kode' => ArrayHelper::getValue($diagnosa, 'kode'),
            'text' => ArrayHelper::getValue($diagnosa, 'text')
        ]);
        $transaksiKirim->save();

        Yii::error(json_encode([
            'service' => 'Sirs-SyncIntegrasiAsuransiData',
            'payload' => $this,
            'response' => $response,
            'timestamp' => date('Y-m-d H:i:s'),
        ]));
    }

    /**
     * Delete item request.
     *
     * @param array $dataAsuransi
     * @param array $payloadItemRequest
     * @return void
     */
    protected function deleteItemRequest($dataAsuransi, $deleteItemRequest)
    {
        if (! empty($deleteItemRequest)) {
            $deleteItemCode = [];
            $insuranceClient = Yii::$app->assuransiClient->setProvider($dataAsuransi['penjamin_id']);
            foreach ($deleteItemRequest as $value) {
                $deleteItemCode[] = $value['item_code'];
                $dataPayload = [
                    'no_claim' => $dataAsuransi['no_klaim'],
                    'item_code' => $value['item_code']
                ];

                $insuranceClient->DeleteItemRequest($dataPayload);
            }

            if (!empty($deleteItemCode)) {
                IntegrasiTindakanObatAsuransi::deleteAll([
                    'asuransi_id' => $dataAsuransi['asuransi_id'],
                    'pendaftaran_id' => $dataAsuransi['pendaftaran_id'],
                    'item_code' => $deleteItemCode
                ]);
            }
        }
    }


    /**
     * Create log item request for integrasi asuransi based on $payloadItemRequest.
     *
     * @param array $dataAsuransi
     * @param array $payloadItemRequest
     */
    protected function createLogItemRequest($dataAsuransi, $payloadItemRequest)
    {
        $insuranceClient = Yii::$app->assuransiClient->setProvider($dataAsuransi['penjamin_id']);
        $payloadLogItem = [];

        if (!empty($payloadItemRequest)) {
            $data = [
                'no_claim' => $dataAsuransi['no_klaim'],
            ];

            $listTerkirim = $insuranceClient->ListItemRequest($data);
            $listTerkirim = ArrayHelper::getValue($listTerkirim, 'data');

            $tindakanExist = [];
            $conditionDelete = [];

            if (!empty($listTerkirim)) {
                foreach ($listTerkirim as $dataVendor) {
                    foreach ($payloadItemRequest as $requestItem) {
                        $dataPayload = [
                            'pendaftaran_id' => $dataAsuransi['pendaftaran_id'],
                            'asuransi_id' => $dataAsuransi['asuransi_id'],
                            'pelayanan_id' => $requestItem['pelayanan_id'],
                            'item_code' => $requestItem['item_code'],
                            'is_sending' => false,
                            'type' => $requestItem['category'],
                            'qty' => $requestItem['qty'],
                            'subtotal' => $requestItem['price'],
                        ];

                        if ($dataVendor['item_code'] == $requestItem['item_code']) {
                            $dataPayload['is_sending'] = true;
                            $payloadLogItem[] = $dataPayload;
                            $tindakanExist[] = $requestItem['pelayanan_id'];
                        }
                    }
                }
            }

            foreach ($payloadItemRequest as $requestItem) {
                if (! in_array($requestItem['item_code'], $conditionDelete)) {
                    $conditionDelete[] = $requestItem['item_code'];
                }

                if (!in_array($requestItem['pelayanan_id'], $tindakanExist)) {
                    $dataPayload = [
                        'pendaftaran_id' => $dataAsuransi['pendaftaran_id'],
                        'asuransi_id' => $dataAsuransi['asuransi_id'],
                        'pelayanan_id' => $requestItem['pelayanan_id'],
                        'item_code' => $requestItem['item_code'],
                        'is_sending' => false,
                        'type' => $requestItem['category'],
                        'qty' => $requestItem['qty'],
                        'subtotal' => $requestItem['price'],
                    ];
                    $payloadLogItem[] = $dataPayload;
                }
            }
        }

        if (!empty($payloadLogItem)) {
            IntegrasiTindakanObatAsuransi::deleteAll([
                'asuransi_id' => $dataAsuransi['asuransi_id'],
                'pendaftaran_id' => $dataAsuransi['pendaftaran_id'],
                'item_code' => $conditionDelete
            ]);

            IntegrasiTindakanObatAsuransi::batchInsert($payloadLogItem);
        }
    }


    private function getDiagnosa($pendaftaranId, $isCob = false)
    {
        $icdX = [];
        if (! $isCob) {
            $resumeMedis = ResumeMedisRi::find()->select([
                'diag_utama',
                'diag_awal'
            ])->where([
                'pendaftaran_id' => $pendaftaranId
            ])->one();

            $diganosaUtama = ArrayHelper::getValue($resumeMedis, 'diag_utama');
            $icdX = null;

            if (! is_array($diganosaUtama)) {
                $diganosaUtama = json_decode($diganosaUtama, true);
            }

            if (isset($diganosaUtama['text']) && isset($diganosaUtama['kode']) && $diganosaUtama['text'] != '' && $diganosaUtama['kode'] != '') {
                $icdX = [
                    'kode' => ArrayHelper::getValue($diganosaUtama, 'kode'),
                    'text' => ArrayHelper::getValue($diganosaUtama, 'text')
                ];
            } else {
                $icdX = [
                    'kode' => ArrayHelper::getValue($diganosaUtama, 'kode'),
                    'text' => ArrayHelper::getValue($diganosaUtama, 'text')
                ];
            }
        } else {
            $dataAsuransi = Pendaftaran::find()
                ->select([
                    'pendaftaran_t.no_pendaftaran',
                ])
                ->join('JOIN', 'asuransi_t', 'asuransi_t.asuransi_id = pendaftaran_t.asuransi_id')
                ->join('LEFT JOIN', 'log_asuransitransaksi_t', 'log_asuransitransaksi_t.asuransi_id = asuransi_t.asuransi_id')
                ->where(['pendaftaran_id' => $pendaftaranId])
                ->asArray()
                ->one();

            $noPendaftaran = ArrayHelper::getValue($dataAsuransi, 'no_pendaftaran');

            $dataKunjungan = SyKunjunganPasien::find()
                ->select([
                    'sy_koreksidiagnosa.diagnosa_id'
                ])
                ->JOIN("JOIN", "sy_koreksidiagnosa", 'sy_kunjungan.kunjungan_id = sy_koreksidiagnosa.kunjungan_id AND sy_koreksidiagnosa.is_icdprimer IS TRUE')
                ->where([
                    'sy_kunjungan.no_pendaftaran' => $noPendaftaran,
                    'sy_kunjungan.status_kunjungan' => DocoConstants::STATUS_FINAL_KLAIM
                ])->asArray()->one();

            $diagnosaId = ArrayHelper::getValue($dataKunjungan, 'diagnosa_id');
            $dataDiagnosa = DiagnosaView::find()->where(['diagnosa_id' => $diagnosaId])->one();

            $icdX = [
                'kode' => ArrayHelper::getValue($dataDiagnosa, 'diagnosa_kode'),
                'text' => ArrayHelper::getValue($dataDiagnosa, 'diagnosa_kode') . ' - ' . ArrayHelper::getValue($dataDiagnosa, 'diagnosa_nama')
            ];
        }

        return $icdX;
    }
}
