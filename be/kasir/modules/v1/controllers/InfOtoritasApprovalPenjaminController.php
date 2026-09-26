<?php

namespace app\modules\v1\controllers;

use app\modules\v1\models\ApprovalDiskonT;
use app\modules\v1\models\InfoListingApprovalView;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\PenjaminDiskonView;
use app\modules\v1\models\PenjaminView;
use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use Doco\components\DocoRestActiveFilter;
use yii\helpers\ArrayHelper;

class InfOtoritasApprovalPenjaminController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoListingApprovalView';

    protected $_title = "Informasi List Approval Penjamin";


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

    public function actionInitIndex()
    {
        try {
            $statusApprove = Lookup::find()
                ->select(['lookup_id', 'lookup_name'])
                ->where(['lookup_type' => 'status_approve'])->asArray()->all();

            $mappingStatus = ArrayHelper::map($statusApprove, 'lookup_id', 'lookup_name');

            $response = [
                'status_approve' => $mappingStatus
            ];

            return [
                'data' => $response,
                'message' => "Get data successfully !"
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
    /**
     * Retrieves a list of information for approval of penjamin.
     *
     * This function creates a new instance of the InfoListingApprovalView model and uses it to query the database.
     * The query is filtered using the advancedFilter method of the DocoRestActiveFilter class.
     * The results are returned as an ActiveDataProvider object with pagination disabled.
     *
     * @return ActiveDataProvider The data provider containing the list of information for approval of penjamin.
     */
    public function actionIndex()
    {
        $model = new InfoListingApprovalView;
        $query = $model::find();
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['tgl_pembayaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pembayaran']);
                if (count($explode) == 2) {
                    $date_start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $date_end = date('Y-m-d 23:59:00', strtotime($explode[1]));

                    $query->andWhere(['between', 'tgl_pembayaran', $date_start, $date_end]);
                }
                unset($_GET['advanced-filter']['tgl_pembayaran']);
            } else {
                $query->andWhere(['between', 'tgl_pembayaran', $start, $end]);
            }

            if (isset($_GET['advanced-filter']['status_approve'])) {
                $statusApprove = ArrayHelper::getValue($_GET['advanced-filter'], 'status_approve');
                $statusApprove = preg_replace('/\s+/', '', $statusApprove);
                if ($statusApprove != "" || $statusApprove != null && $statusApprove != "Semua") {
                    $query->andWhere(['status_approve' => $statusApprove]);
                }
                unset($_GET['advanced-filter']['status_approve']);
            }
        } else {
            $query->andWhere(['between', 'tgl_pembayaran', $start, $end]);
        }
        $query->orderBy(['tgl_pembayaran' => SORT_DESC]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query
        ]);
    }

    /**
     * Creates an approval for a penjamin.
     *
     * @return array The response data containing the status, title, and attributes of the approval.
     * @throws \Exception If an error occurs during the process.
     */
    public function actionCreateApprovalPenjamin()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            $payload = $request->post();
            $payload['pegawai_kasir_id'] = Yii::$app->jwt->user->pegawai_id;
            $payload['tanggal_pembayaran'] = date('Y-m-d H:i:s');
            $masterPenjamin = $this->actionGetLimitPenjamin($payload['penjamin_id_main']);
            $payload['limit_master_penjamin'] = $masterPenjamin;

            $pendaftaranId = (int) $request->post("pendaftaran_id");
            $totalTagihan = (float) $request->post("total_tagihan");
            $limitPenjamin = (int) $request->post("limit_penjamin");
            $jumlahTagihan = $request->post("total_tagihan");
            $jumlahDiskon = $request->post("total_diskon");
            /**
             * Handle diskon apabil ada dibelakang koma 1,00
             */
            $totalDiskon = (float) ($jumlahDiskon / $jumlahTagihan) * 100;
            $totalDiskon = number_format($totalDiskon, 2);

            $model = new ApprovalDiskonT();
            $model->pendaftaran_id = $pendaftaranId;
            $model->diskon = (float) $totalDiskon;
            $model->limit_diskon = $limitPenjamin;
            $model->jumlah_limit_diskon = $totalTagihan / 100 * $limitPenjamin;
            $model->jumlah_diskon = $request->post("total_diskon");
            $model->additional_data = json_encode($payload);

            if (!$model->validate()) {
                \Yii::$app->response->statusCode = 400;
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                    'data' => $model->errors
                ]);
            }

            if (!$model->save()) {
                \Yii::$app->response->statusCode = 500;
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                    'data' => $model->errors
                ]);
            }

            Yii::$app->db->createCommand("
                    UPDATE pendaftaran_t SET is_close_bill = true
                    WHERE pendaftaran_id = {$pendaftaranId}
                ")->execute();

            $transaction->commit();
            Yii::$app->response->statusCode = 200;
            return [
                'status' => 200,
                'title' => 'Proses Berhasil',
                'data' => $model->attributes
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    /**
     * Function for payment with approval.
     * 
     * @author Maulana Muhammad Rizky
     * @return array
     */
    public function actionApprovalPembayaran()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $request = Yii::$app->request;
        try {
            $approvalId = (int) $request->post("approval_id");
            $findTrx = ApprovalDiskonT::find()->where(['approvaldiskon_id' => $approvalId])->one();

            if (empty($findTrx)) {
                \Yii::$app->response->statusCode = 404;
                $transaction->rollBack();
                return [
                    'status' => 404,
                    'title' => 'Proses Approval Gagal !',
                    'message' => "Data Tidak ditemukan !"
                ];
            }

            if ($findTrx->status_approve == DocoConstants::STATUS_APPROVED) {
                \Yii::$app->response->statusCode = 422;
                $transaction->rollBack();
                return [
                    'status' => 422,
                    'title' => 'Proses Approval Gagal !',
                    'message' => "Data Sudah di Approve !"
                ];
            }
            $payloadPembayaran = json_decode($findTrx->additional_data, true);
            $payloadPembayaran['api_approval'] = true;

            Yii::$app->request->setBodyParams($payloadPembayaran);
            $result = Yii::$app->runAction('v1/tagihan-pasien/save');

            $statusCode = isset($result['metadata']['status']) ? $result['metadata']['status'] : null;
            if ($statusCode != 200) {
                Yii::error($result);
                \Yii::$app->response->statusCode = 422;
                if (isset($result['response']['text'])) {
                    $message = $result['response']['text'];
                } else {
                    $message = "Proses Gagal / Tindakan Sudah dibayarkan !";
                }

                $transaction->rollBack();
                return [
                    'status' => 422,
                    'title' => 'Proses Approval Gagal !',
                    'message' => isset($result['response']['message']) ? $result['response']['message'] : $message
                ];
            }

            if (!empty($result)) {
                $model = ApprovalDiskonT::find()->where(['approvaldiskon_id' => $approvalId])->one();
                $model->status_approve = DocoConstants::STATUS_APPROVED;
                $model->pembayaran_id = $result['response']['pembayaran_id'];
                $model->pegawai_approve_id = 1;
                $model->tgl_approve = date("Y-m-d H:i:s");
                $model->save();

                Yii::$app->db->createCommand("
                    UPDATE pendaftaran_t SET is_close_bill = false
                    WHERE pendaftaran_id = {$findTrx->pendaftaran_id}
                ")->execute();
            }

            $transaction->commit();
            Yii::$app->response->statusCode = 200;
            return [
                'status' => 200,
                'title' => 'Proses Approval Berhasil !',
                'data' => $result
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionRejectPembayaran()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $request = Yii::$app->request;
        try {
            $approvalId = (int) $request->post("approval_id");
            $findTrx = ApprovalDiskonT::find()->where(['approvaldiskon_id' => $approvalId])->one();
            $pegawai_id = Yii::$app->jwt->user->pegawai_id;

            if (empty($findTrx)) {
                \Yii::$app->response->statusCode = 404;
                $transaction->rollBack();
                return [
                    'status' => 404,
                    'title' => 'Proses Approval Gagal !',
                    'message' => "Data Tidak ditemukan !"
                ];
            }

            Yii::$app->db->createCommand("
                    UPDATE pendaftaran_t SET is_close_bill = false
                    WHERE pendaftaran_id = {$findTrx->pendaftaran_id}
                ")->execute();

            $findTrx->status_approve = DocoConstants::STATUS_REJECT;
            $findTrx->pegawai_approve_id = $pegawai_id;
            $findTrx->tgl_approve = date("Y-m-d H:i:s");
            $findTrx->save();

            $transaction->commit();
            return [
                'status' => 200,
                'title' => 'Proses Reject Berhasil !',
                'data' => $findTrx
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionGetDetailData()
    {
        try {
            $request = Yii::$app->request;
            $approvalId = (int) $request->get("approval_id");

            Yii::error($request->get());
            $findTrx = InfoListingApprovalView::find()->where(['approvaldiskon_id' => $approvalId])->one();
            if (empty($findTrx)) {
                \Yii::$app->response->statusCode = 404;
                return [
                    'status' => 404,
                    'title' => 'Data Tidak ditemukan !',
                    'message' => "Data Tidak ditemukan !"
                ];
            }

            $additional = json_decode($findTrx->additional_data, true);
            $penjaminId = ArrayHelper::getValue($additional, "penjamin_id_main");

            $findPenjamin = PenjaminView::find()->select([
                'penjamin_nama'
            ])->where(['penjamin_id' => $penjaminId])->one();

            return [
                'status' => 200,
                'data' => $findTrx,
                'penjamin' => ! empty($findPenjamin) ? $findPenjamin : [],
                'message' => "Berhasil mengambil data !"
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionGetLimitPenjamin($penjaminId)
    {
        $penjaminUmum = [];
        $penjaminAsuransi = [];
        $penjamin = PenjaminDiskonView::find()
            ->select([
                'penjamindiskon_id',
                'penjamin_id',
                'carabayar_id',
                'carabayar_nama',
                'penjamin_kode',
                'penjamin_nama',
                'diskon_otomatis',
            ])
            ->where(['penjamin_id' => [$penjaminId, DocoConstants::NEW_PENJAMIN_UMUM]])
            ->andWhere(['is_deleted' => false])
            ->andWhere(['is_active' => true])
            ->asArray()
            ->all();

        if (!empty($penjamin)) {
            foreach ($penjamin as $value) {
                if ($value['penjamin_id'] == DocoConstants::NEW_PENJAMIN_UMUM) {
                    $penjaminUmum = $value;
                } else {
                    $penjaminAsuransi = $value;
                }
            }
        }

        return [
            'umum' => $penjaminUmum,
            'asuransi' => $penjaminAsuransi
        ];
    }

    public function actionExportExcel()
    {
        try {
            $result = [];
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            $query = InfoListingApprovalView::find();
            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['tgl_pembayaran'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tgl_pembayaran']);
                    if (count($explode) == 2) {
                        $date_start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $date_end = date('Y-m-d 23:59:00', strtotime($explode[1]));

                        $query->andWhere(['between', 'tgl_pembayaran', $date_start, $date_end]);
                    }
                    unset($_GET['advanced-filter']['tgl_pembayaran']);
                }

                if (isset($_GET['advanced-filter']['status_approve'])) {
                    $statusApprove = ArrayHelper::getValue($_GET['advanced-filter'], 'status_approve');
                    $statusApprove = preg_replace('/\s+/', '', $statusApprove);
                    if ($statusApprove != "" || $statusApprove != null) {
                        if ($statusApprove != "-Semua-") {
                            $query->andWhere(['status_approve' => $statusApprove]);
                        }
                    }
                    unset($_GET['advanced-filter']['status_approve']);
                }

                if (isset($_GET['advanced-filter']['nama_pasien'])) {
                    $namaPasien = ArrayHelper::getValue($_GET['advanced-filter'], 'nama_pasien');
                    if ($namaPasien != "") {
                        $query->andWhere(['ILIKE', 'nama_pasien', $namaPasien]);
                        unset($_GET['advanced-filter']['nama_pasien']);
                    }
                }

                if (isset($_GET['advanced-filter']['pegawai_kasir'])) {
                    $pegawaiKasir = ArrayHelper::getValue($_GET['advanced-filter'], 'pegawai_kasir');
                    if ($pegawaiKasir != "") {
                        $query->andWhere(['ILIKE', 'pegawai_kasir', $pegawaiKasir]);
                        unset($_GET['advanced-filter']['pegawai_kasir']);
                    }
                }

                if (isset($_GET['advanced-filter']['no_pendaftaran'])) {
                    $noPendaftaran = ArrayHelper::getValue($_GET['advanced-filter'], 'no_pendaftaran');
                    if ($noPendaftaran != "") {
                        $query->andWhere(['no_pendaftaran' => $noPendaftaran]);
                        unset($_GET['advanced-filter']['no_pendaftaran']);
                    }
                }
            } else {
                $query->andWhere(['between', 'tgl_pembayaran', $start, $end]);
            }

            $query->orderby(['tgl_pembayaran' => SORT_ASC]);
            $no = 0;

            foreach ($query->asArray()->all() as $value) {
                $no++;
                $data['tgl_pembayaran'] = $value['tgl_pembayaran'];
                $data['nama_pasien'] = $value['nama_pasien'];
                $data['no_pendaftaran'] = $value['no_pendaftaran'];
                $data['pegawai_kasir'] = $value['pegawai_kasir'];
                $data['limit_diskon'] = $value['limit_diskon'];
                $data['diskon'] = $value['diskon'];
                $data['jumlah_diskon'] = $value['jumlah_diskon'];
                $data['status'] = $value['status'];
                $data['pegawai_approve_nama'] = $value['pegawai_approve_nama'];
                $data['tgl_approve'] = $value['tgl_approve'];
                $result[] = $data;
            }

            $header = [];
            $filePath = DocoHelpers::exportExcel('Laporan Approval Penjamin', $result, $header, [], [], [], true);

            $filePath->save('php://output');
            die;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
}
