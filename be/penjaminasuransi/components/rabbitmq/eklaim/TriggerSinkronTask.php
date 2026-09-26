<?php

namespace app\components\rabbitmq\eklaim;

use app\modules\v1\models\Cron;
use app\modules\v1\models\SyKunjungan;
use app\modules\v1\models\SyKunjunganPasien;
use app\modules\v1\models\SyDiagnosaView;
use app\modules\v1\models\SyKoreksiDiagnosa;
use app\modules\v1\models\SyKunjunganDetail;
use app\modules\v1\models\SyKunjunganTagihan;
use app\modules\v1\models\SyKunjunganTagihanView;
use Doco\components\DocoConstants;
use Doco\components\DocoConstansId;
use Doco\rabbitmq\task\IntegrasiTask;
use Yii;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Client;

class TriggerSinkronTask extends IntegrasiTask
{
    /**
     * Main Execute 
     * 
     * @author Maulana Muhammad Rizky (maulana.rizky@sirs.co.id)
     * 
     * 30 Agustus 2023
     */
    public function prosesSync()
    {
        $type = $this->type_sinkron;

        if (strtolower($type) == "sinkron") {
            $result = self::triggerSinkronisasi();
        }

        if (strtolower($type) == 'hapus') {
            $result = self::triggerHapusSinkronisasi();
        }

        if (strtolower($type) == 'global') {
            $result = self::triggerSinkronisasiAll();
        }

        Yii::error(json_encode([
            'service' => 'Sirs-TriggerSinkronisasi',
            'payload' => $this,
            'response' => $result,
            'timestamp' => date('Y-m-d H:i:s'),
        ]));
    }

    /**
     * Function untuk melakukan sinkronisasi ketika pasien melakukan pembayaran 
     * Function ini tidak menggunakan pendaftaran_id ataupun no_pendaftaran
     * 
     * Perubahan menjadi satu nomer pembayaran pada eklaim 
     * @return Json
     */
    protected function triggerSinkronisasi()
    {
        $result = null;
        $resultValue = self::searchPembayaran($this->pembayaran_id);
        $no_pembayaran = ArrayHelper::getValue($resultValue, 'no_pembayaran');
        if (!empty($resultValue)) {
            $response = $this->triggerSyncData($this->instalasi, $no_pembayaran);
            $result   = isset($response['response']['list']) ? $response['response']['list'] : [];
        }

        return $result;
    }

    /**
     * Function untuk melakukan sinkronisasi ketika pasien melakukan pembayaran 
     * Function ini tidak menggunakan pendaftaran_id ataupun no_pendaftaran
     * 
     * Perubahan menjadi satu nomer pembayaran pada eklaim 
     * @return Json
     */
    protected function triggerSinkronisasiAll()
    {
        $result = null;
        $resultValue = self::searchAllPendafataran();
        $countData = count($resultValue);
        if($countData == 0) {
            sleep(2);
            self::publishMessage(100, 'Sinkronisasi Berhasil !');
        } else {
            if (!empty($resultValue)) {
                foreach ($resultValue as $key => $value) {
                    $no_pendaftaran = ArrayHelper::getValue($value, 'no_pendaftaran');
                    $instalasi_id = ArrayHelper::getValue($value, 'instalasi_id');
                    $no = $key + 1;
                    $progressBar = ceil($no / $countData * 100);
                    $response = $this->triggerSyncData($instalasi_id, $no_pendaftaran, $progressBar);
                    $result   = isset($response['response']['list']) ? $response['response']['list'] : [];
                }
            }
        }


        return $result;
    }

    /**
     * Function untuk menghapus ketika kondisi batal bayar apabila sudah terintegrasi
     * @property pendaftaran_id string
     * @property no_pendaftaran string
     * @return Json
     */
    protected function triggerHapusSinkronisasi()
    {
        $resultValue = self::searchPembayaran($this->pembayaran_id);
        $no_pembayaran = ArrayHelper::getValue($resultValue, 'no_pembayaran');
        $resultData = null;

        if (!empty($resultValue)) {
            $prevData = SyKunjunganPasien::find()
                ->where(
                    [
                        'no_pembayaran' => $no_pembayaran,
                        'status_kunjungan' => [DocoConstants::STATUS_BELUM_KOREKSI, DocoConstants::STATUS_SUDAH_KOREKSI]
                    ]
                )->one();

            try {
                $jumlahPembayaran = 0;
                // Kondisi apabila data tidak kosong
                if (!empty($prevData)) {
                    $kunjungan_id = $prevData->kunjungan_id;
                    $jumlahPembayaran = self::countJumlahPembayaran($kunjungan_id);
                    if ($jumlahPembayaran > 1) {
                        $resultData = self::deleteTransactionPembayaran($kunjungan_id);
                    } else {
                        // Kondisi ketika jumlah pembayaran hanya satu kali
                        $resultData = self::deleteTransactionPembayaran($kunjungan_id);
                        $prevData->is_deleted = true;
                        $prevData->save();
                    }
                } else {
                    $resultData = "DATA TIDAK DITEMUKAN!";
                }

                return [
                    'status' => 200,
                    'message' => "Data berhasil dihapus!",
                    "processingData" => $resultData,
                    "prevData" => $prevData,
                    "jumlahPembayaran" => $jumlahPembayaran
                ];
            } catch (\Throwable $th) {
                return [
                    'status' => 500,
                    'message' => $th->getMessage()
                ];
            }
        }
        return $this->pembayaran_id;
    }

    /**
     * Function untuk mendapatkan nomer pendaftaran.
     */
    protected function searchPendafataran($pendaftaran_id)
    {
        $pendaftaran_id = intval($pendaftaran_id);

        return Yii::$app->db->createCommand("
            SELECT no_pendaftaran, pasienadmisi_id
                FROM pendaftaran_t
            WHERE pendaftaran_id = {$pendaftaran_id}
        ")->queryOne();
    }

    /**
     * Function untuk mencari nomer pembayaran
     * 
     * @param $pembayaran_id
     * 
     * @return array
     */
    protected function searchPembayaran($pembayaran_id)
    {
        $pembayaran_id = intval($pembayaran_id);

        return Yii::$app->db->createCommand("
            SELECT no_pembayaran, pendaftaran_id
                FROM pembayaran_t
            WHERE pembayaran_id = {$pembayaran_id}
        ")->queryOne();
    }


    /**
     * Function untuk mendapatkan nomer pendaftaran.
     * 
     * @return array
     */
    protected function searchAllPendafataran()
    {
        $limit = (new DocoConstansId)->actionGetId('limit_cron_global_eklaim');
        if(is_null($limit)){
            $limit = 50;
        }
        return Yii::$app->db->createCommand("
        SELECT
            no_pendaftaran,
            instalasi_id
        FROM
            sy_kunjungan_v
        WHERE
            no_pendaftaran NOT IN(
                SELECT
                    no_pendaftaran
                FROM
                    sy_kunjungan
                WHERE
                    is_deleted = false
                    AND is_active = true
            )
            ORDER BY tgl_pulang DESC
            LIMIT :limit
        ")
        ->bindValue(':limit',$limit)
        ->queryAll();
    }

    /**
     * Function untuk menentukan jumlah pembayaran di sy_kunjungantagihan
     * 
     * @param $kunjungan_id string
     * 
     * @return $jumlahNoPembayaran int
     */
    protected function countJumlahPembayaran($kunjungan_id)
    {
        $jumlahNoPembayaran = SyKunjunganTagihan::find()
            ->select(['no_buktitrans'])
            ->where(['kunjungan_id' => $kunjungan_id, 'is_deleted' => false])
            ->groupBy(['no_buktitrans'])
            ->count();

        return $jumlahNoPembayaran;
    }

    /** 
     * Function untuk mengehapus data transaksi sy_kunjungantagihan
     * 
     * @return string|bool
     */
    protected function deleteTransactionPembayaran($kunjungan_id)
    {
        $noPembayaran = self::searchPembayaran($this->pembayaran_id);
        if (isset($noPembayaran)) {
            $noPembayaran = ArrayHelper::getValue($noPembayaran, 'no_pembayaran');
            return SyKunjunganTagihan::updateAll([
                'is_deleted' => true
            ], ['no_buktitrans' => $noPembayaran, 'kunjungan_id' => $kunjungan_id]);
        }

        return "DATA PEMBAYARAN TIDAK DITEMUKAN!";
    }

    /**
     * Function untuk trigger data kunjungan dari KASIR PEMBAYARAN
     * 
     * @return JSON 
     */
    public function triggerSyncData($instalasi, $no_pembayaran, $progressBar = null)
    {
        $params = Yii::$app->params;
        $urlBackend = isset($params['url_backend']) ? $params['url_backend'] : 'http://web:8858/';
        $guzzle = new Client([
            'base_uri' => $urlBackend . 'penjaminasuransi/v1/',
            'verify' => false,
            'headers' => [
                'user-agent' => 'cli',
            ]
        ]);

        $tgl_pendaftaran = '2023-04-26';
        $jam_pendaftaran = "00:00:00";

        $result = $guzzle->post('single-sync/single-sinkron', [
            'query' => [
                'tgl_pendaftaran' => $tgl_pendaftaran,
                'jam_pendaftaran' => $jam_pendaftaran,
                'instalasi' => $instalasi,
                'no_pembayaran' => $no_pembayaran,
                'is_trigger' => true,
                'randString' => $this->unique_str,
                'progressBar' => $progressBar
            ]
        ]);
        $result = json_decode($result->getBody(), true);
        Yii::error(json_encode($result));
    }

    protected function publishMessage($progressBar, $message, $state = 'finish')
    {
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'sync-eklaim:' . $this->unique_str,
            'message' => json_encode([
                'status' => $state,
                'messageProcess' => $message,
                'hide' => true,
                'progress' => $progressBar,
            ]),
        ]);
    }
}
