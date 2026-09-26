<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoAccessRule;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoJwtHttpBearerAuth;
use Doco\components\DocoSyp;
use app\modules\v1\models\Cron;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\SyKunjungan;
use app\modules\v1\models\SyKunjunganDetail;
use app\modules\v1\models\SyKunjunganPasien;
use app\modules\v1\models\SyKunjunganTagihan;
use app\modules\v1\models\SyKunjunganPotonganTagihan;
use app\modules\v1\models\SyKunjunganAdjusmentHeader;
use app\modules\v1\models\SyKunjunganAdjusmentDetail;
use Doco\components\DocoConstansId;
use Doco\components\DocoHelpers;
use Doco\rabbitmq\RabbitBgProcess;
use yii\helpers\ArrayHelper;
use Doco\Services\InternalService;

/**
 * @function : Set Limit exec
 */
ini_set('max_execution_time', '0');
ini_set("memory_limit", "-1");
class SingleSyncController extends DocoActiveController
{
    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => DocoJwtHttpBearerAuth::className(),
            'except' => ['single-sinkron', 'integrasi-eklaim', 'integrasi-hapus-eklaim', 'update-tgl-pulang'],
        ];

        $behaviors['access'] = [
            'class' => DocoAccessRule::className(),
            'except' => ['single-sinkron', 'integrasi-eklaim', 'integrasi-hapus-eklaim', 'update-tgl-pulang'],
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        return $actions;
    }

    /**
     * @todo Function sync perpasien
     * @return message
     * @author Erlangga <erlangga@docotel.com>
     */
    public function actionSingleSinkron($command = false)
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        $randString = isset($get['randString']) ? $get['randString'] : null;
        $noPendaftaran = ArrayHelper::getValue($get, 'no_pendaftaran');
        $no_pembayaran = ArrayHelper::getValue($get, 'no_pembayaran');
        $instalasi = ArrayHelper::getValue($get, 'instalasi');
        $isTrigger = ArrayHelper::getValue($get, 'is_trigger');
        $progressBar = ArrayHelper::getValue($get, 'progressBar'); // Progressbar ini untuk penanda sinkron global
        $isTriggerBayar = true;
        $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
        $countData = 1000;
        $totalPerPage = 1000;

        if (empty($isTrigger) || ! empty($progressBar)) {
            $isTrigger = false;
            $isTriggerBayar = false;
        }

        if (!empty($no_pembayaran) && $isTriggerBayar) {
            $no_referensi = $no_pembayaran;
        } else {
            $no_referensi = $noPendaftaran;
        }

        if(! empty($progressBar)) {
            $no_referensi = $no_pembayaran;
        }

        (new RabbitBgProcess())->send([
            'no_referensi' => $no_referensi,
            'instalasi_kode' => $instalasi,
            'user_id' => !empty($jwt->loginpemakai_id) ? $jwt->loginpemakai_id : null,
            'token' => $auth,
            'xOwner' => $xOwner,
            'unique_str' => $randString,
            'is_trigger' => $isTrigger,
            'is_nobayar' => $isTriggerBayar,
            'tgl_pendaftaran' => null,
            'jam_pendaftaran' => null,
            'is_api' => false,
            'progress_bar' => $progressBar 
        ], 'sinkron_eklaim');

        return [
            'totalPerPage' => $totalPerPage,
            'randString' => $randString,
            'countData' => $countData,
        ];
    }

    /**
     * Function ini hanya untuk integrasi dari pembayaran
     * 
     * @author Maulana Muhammad Rizky
     * 30 Agustus 2023
     */
    public function actionIntegrasiEklaim()
    {
        try {
            $request = Yii::$app->request;
            $get = $request->get();
            $pendfataran_id = ArrayHelper::getValue($get, 'pendaftaran_id');
            $pembayaran_id = ArrayHelper::getValue($get, 'pembayaran_id');
            $instalasi = ArrayHelper::getValue($get, 'instalasi');


            (new RabbitBgProcess())->send([
                'type_sinkron' => "sinkron",
                'pendaftaran_id' => $pendfataran_id,
                'pembayaran_id' => $pembayaran_id,
                'instalasi' => $instalasi
            ], 'trigger_sinkron_eklaim', 'sync_data');

            return [
                'status' => 200,
                'message' => "Integrasi berhasil !"
            ];
        } catch (\Throwable $th) {
            return [
                'status' => 500,
                'message' => $th->getMessage()
            ];
        }
    }

    /**
     * Function ini hanya untuk integrasi dari pembayaran
     * 
     * @author Maulana Muhammad Rizky
     * 30 Agustus 2023
     */
    public function actionIntegrasiHapusEklaim()
    {
        try {
            $request = Yii::$app->request;
            $get = $request->get();
    
            if(isset($get['pendaftaran_id'])) {
    
                (new RabbitBgProcess())->send([
                    'type_sinkron' => "hapus",
                    'pendaftaran_id' => $get['pendaftaran_id'],
                    'pembayaran_id' => $get['pembayaran_id'],
                ], 'trigger_sinkron_eklaim', 'sync_data');
            }
    
            return [
                'status' => 200,
                'message' => "Sinkronisasi Berhasil"
            ];
        } catch (\Throwable $th) {
            return [
                'status' => 500,
                'message' => $th->getMessage()
            ];
        }
    }

    /**
     * Function ini untuk update tanggal pulang sync eklaim
     * 
     * @author Sulthan Zaidan Fauzi
     * 4 Juni 2024
     */
    public function actionUpdateTglPulang() {
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            $request = Yii::$app->request;
            $post = $request->post();

            if(isset($post['no_pendaftaran']) && isset($post['instalasi_kode'])) {
                $no_pendaftaran = $post['no_pendaftaran'];
                $instalasi_kode = $post['instalasi_kode'];
                $instalasi_kodeText = implode(',', $post['instalasi_kode']);
                
                $getKunjungan = SyKunjungan::find()
                                ->select(['no_pendaftaran', 'instalasi_singkatan', 'tgl_pulang', 'jam_pulang'])
                                ->andWhere(['no_pendaftaran' => $no_pendaftaran])
                                ->andWhere(['instalasi_singkatan' => $instalasi_kode])
                                ->asArray()->one();

                if(isset($getKunjungan['tgl_pulang']) && isset($getKunjungan['jam_pulang'])) {
                    $tgl_pulang = $getKunjungan['tgl_pulang'] . ' ' . $getKunjungan['jam_pulang'];

                    $updSinkron = SyKunjunganPasien::updateAll([
                            'tgl_pulang' => $tgl_pulang
                        ], 
                        ['AND', 
                            ['no_pendaftaran' => $no_pendaftaran], 
                            ['instalasi_kode' => $instalasi_kode] 
                        ]
                    );

                    if($updSinkron) {
                        $transaction->commit();
                        return [
                            'status' => 200,
                            'message' => "Update Tanggal Pulang, Registrasi ".$no_pendaftaran." - ".$instalasi_kodeText." Berhasil"
                        ];
                    } else {
                        $transaction->rollback();
                        \Yii::$app->response->statusCode = 422;
                        return [
                            'status' => 422,
                            'message' => "Terjadi kesalahan saat update ".$no_pendaftaran."."
                        ];
                    }
                }

                $transaction->rollback();
                \Yii::$app->response->statusCode = 422;
                return [
                    'status' => 422,
                    'message' => "Kunjungan Registrasi ".$post['no_pendaftaran']." - ".$instalasi_kodeText." Tidak ditemukan atau belum dipulangkan."
                ];
            }
            
            $transaction->rollback();
            \Yii::$app->response->statusCode = 422;
            return [
                'status' => 422,
                'message' => "Pendaftaran tidak ditemukan."
            ];
        } catch(\Throwable $th) {
            $transaction->rollback();
            \Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'message' => $th->getMessage()
            ];
        }
    }
}
