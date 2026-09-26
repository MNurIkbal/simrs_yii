<?php

namespace app\modules\v1\controllers;

use Yii;
use app\modules\v1\models\PasienPulangMeninggalView;
use Doco\components\DocoAccessRule;
use Doco\components\DocoConstansId;
use yii\data\ActiveDataProvider;
use Doco\components\DocoConstants;
use Doco\components\DocoJwtHttpBearerAuth;
use Doco\components\DocoRestActiveFilter;
use Doco\models\LookupTransaksi;
use Doco\models\Pendaftaran;

class PasienMeninggalController extends \Doco\components\DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\Pasien';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        $verbs["create"] = ["POST"];
        return $verbs;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => DocoJwtHttpBearerAuth::className(),
            'except' => ['cron-hide-pasien'],
        ];

        $behaviors['access'] = [
            'class' => DocoAccessRule::className(),
            'except' => ['cron-hide-pasien'],
        ];

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

    public function actionIndex()
    {
        try {
            $konfigCron = LookupTransaksi::find()
                ->select([
                    'kode_id',
                    'kode_transaksi',
                ])
                ->where([
                    'kode_transaksi' => DocoConstants::CRON_HIDE_PASIEN_MENINGGAL
                ])->one();

            return [
                'status' => 200,
                'data' => [
                    'konfig_cron' => $konfigCron
                ]
            ];
        } catch (\Exception $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionGetDataPasienMeninggal()
    {
        try {
            $model = new PasienPulangMeninggalView();
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:59');
            $statusMeninggal = (new DocoConstansId)->actionGetId('meninggal');

            $query = $model::find(true);

            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                    if (count($explode) == 2) {
                        $date_start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $date_end = date('Y-m-d 23:59:00', strtotime($explode[1]));

                        $query->andWhere(['between', 'tgl_pendaftaran', $date_start, $date_end]);
                    }
                    unset($_GET['advanced-filter']['tgl_pendaftaran']);
                } else {
                    $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
                }

                if (isset($_GET['advanced-filter']['tglpasienpulang'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tglpasienpulang']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                        $query->andWhere(['between', 'tglpasienpulang', $start, $end]);
                    }
                    unset($_GET['advanced-filter']['tglpasienpulang']);
                }

                if (isset($_GET['advanced-filter']['status_aktif'])) {
                    $status_aktif = $_GET['advanced-filter']['status_aktif'];
                    if ($status_aktif == '1') {
                        $query->andWhere(['is_deleted' => false]);
                    }

                    if ($status_aktif == '0') {
                        $query->andWhere(['is_deleted' => true]);
                    }

                    unset($_GET['advanced-filter']['status_aktif']);
                }

                if (isset($_GET['advanced-filter']['no_rekam_medik'])) {
                    $no_rekam_medik = $_GET['advanced-filter']['no_rekam_medik'];
                    if ($no_rekam_medik != '') {
                        $query->andWhere(['no_rekam_medik' => $no_rekam_medik]);
                        unset($_GET['advanced-filter']['no_rekam_medik']);
                    }
                }

                if (isset($_GET['advanced-filter']['nama_pasien'])) {
                    $nama_pasien = $_GET['advanced-filter']['nama_pasien'];
                    if ($nama_pasien != '') {
                        $query->andWhere(['LIKE', 'nama_pasien', $nama_pasien]);
                        unset($_GET['advanced-filter']['nama_pasien']);
                    }
                }
            } else {
                $query->andWhere(['between', 'tglpasienpulang', $start, $end]);
            }

            $query->andWhere([
                'carakeluar_id' => $statusMeninggal
            ]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
            return new ActiveDataProvider([
                'query' => $query
            ]);
        } catch (\Exception $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionActiveCron()
    {
        try {
            $request = Yii::$app->request;
            $status = $request->get('status');

            $konfigCron = LookupTransaksi::find()
                ->select([
                    'kode_id',
                    'kode_transaksi',
                ])
                ->where([
                    'kode_transaksi' => DocoConstants::CRON_HIDE_PASIEN_MENINGGAL
                ])->one();

            $konfigCron->kode_id = $status;
            $konfigCron->save();

            return [
                'status' => 200,
                'message' => 'Status Cron Berhasil Diubah !'
            ];
        } catch (\Exception $th) {
            Yii::error($th);
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'message' => $th->getMessage()
            ];
        }
    }


    public function actionActivePasien()
    {
        try {
            $request = Yii::$app->request;
            $pendaftaranId = $request->get('pendaftaran_id');
            $status = $request->get('status');
            $status = $status == '1' ? false : true;

            $cekPendaftaran = Pendaftaran::find(true)->where([
                'pendaftaran_id' => $pendaftaranId
            ])->one();

            if (empty($cekPendaftaran)) {
                Yii::$app->response->statusCode = 404;
                return [
                    'status' => 404,
                    'message' => 'Pendaftaran tidak ditemukan !'
                ];
            }

            $cekPendaftaran->is_deleted = $status;
            $cekPendaftaran->save();

            Yii::$app->response->statusCode = 200;
            return [
                'status' => 200,
                'message' => 'Status Pasien Berhasil Diubah !'
            ];
        } catch (\Exception $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionCronHidePasien()
    {
        try {
            $dateNow = date('Y-m-d H:i:s');
            $dateBefore = date('Y-m-d 00:00:00', strtotime('-1 day'));

            $konfigCron = LookupTransaksi::find()
                ->select([
                    'kode_id',
                    'kode_transaksi',
                ])
                ->where([
                    'kode_transaksi' => DocoConstants::CRON_HIDE_PASIEN_MENINGGAL
                ])->one();

            if (empty($konfigCron)) {
                Yii::$app->response->statusCode = 404;
                return [
                    'status' => 404,
                    'message' => 'Konfigurasi Cron Tidak Ditemukan !'
                ];
            }

            if ($konfigCron->kode_id == '0') {
                Yii::$app->response->statusCode = 404;
                return [
                    'status' => 404,
                    'message' => 'Konfigurasi Cron Tidak Aktif !'
                ];
            }
            $statusMeninggal = (new DocoConstansId)->actionGetId('meninggal');
            $getPasien = PasienPulangMeninggalView::find(true)
                ->andWhere([
                    'carakeluar_id' => $statusMeninggal,
                    'is_deleted' => false
                ])
                ->andWhere(['between', 'tglpasienpulang', $dateBefore, $dateNow])
                ->asArray()
                ->all();
            
            if (empty($getPasien)) {
                $payloadResponse = [
                    'status' => 404,
                    'message' => 'Pendaftaran tidak ditemukan !',
                    'dateBefore' => $dateBefore,
                    'dateNow' => $dateNow
                ];
                Yii::error(json_encode($payloadResponse));

                Yii::$app->response->statusCode = 404;
                return $payloadResponse;
            }

            if (! empty($getPasien)) {
                $pendaftaranIds = [];
                foreach ($getPasien as $key => $value) {
                    $pendaftaranIds[] = $value['pendaftaran_id'];
                }

                Pendaftaran::updateAll(['is_deleted' => true], ['pendaftaran_id' => $pendaftaranIds]);
            }

            Yii::error(json_encode([
                'message' => 'Success',
                'data' => $getPasien
            ]));

            Yii::$app->response->statusCode = 200;
            return [
                'status' => 200,
                'message' => 'Success',
                'data' => $getPasien,
            ];
        } catch (\Exception $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'message' => $th->getMessage()
            ];
        }
    }
}
