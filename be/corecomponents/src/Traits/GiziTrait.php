<?php

namespace Doco\Traits;

use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;

use function GuzzleHttp\json_encode;
use GuzzleHttp\Exception\RequestException;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoMessages;
use app\components\Traits\Form\DietPasienForm;
use Doco\models\PermintaanMakan;
use Doco\models\Pegawai;
use Doco\components\DocoRestActiveFilter;
use Doco\components\PelayananHelpers;
use Doco\Notifications\GiziNotification;

trait GiziTrait
{

    /**
     * @var String $resumeModel
     * @author ilham.pramono@sirs.co.id
     */
    public $resumeModel;

    /**
     * @var String $infoPasienRiModel
     * @author ilham.pramono@sirs.co.id
     */
    public $infoPasienRiModel;

    public function actionSaveDietPasien()
    {
        // try {

            $connection  = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            $request     = Yii::$app->request;
            $posts      = $request->post();
            $modelMakan = new PermintaanMakan;
            $modelMakan->pendaftaran_id      = $posts['pendaftaran_id'];

            $modelMakan->pasienadmisi_id     = $posts['pasienadmisi_id'];
            $modelMakan->catatan_diet        = $posts['catatan_diet'];
            $modelMakan->peg_pemesan_id      = $posts['peg_pemesan_id'];
            $modelMakan->tgl_permintaanmakan = date('Y-m-d H:i:s');
            $modelMakan->status              = 1;
            $modelMakan->ruangan_asal        = !empty($posts['ruangan_asal']) ? PelayananHelpers::decryptId($posts['ruangan_asal']) : null;

            $modelMakan->save();
            if ($posts['instalasi_id'] == DocoConstants::VAR_I_RANAP) {
                $infoPasienRi = $this->infoPasienRiModel->find()->where(['pendaftaran_id' => $posts['pendaftaran_id']])->one();
                $pasienadmisi_id = $infoPasienRi->pasienadmisi_id;
            }
            GiziNotification::catatanDietNotification($modelMakan);
            $transaction->commit();

            return [
                'message' => 'Proses Berhasil!',
                'text' => 'Permintaan Makan Berhasil Dibuat',
                'data' => [
                    'pendaftaran_id' => $modelMakan->pendaftaran_id
                ]
            ];
        // } catch (\yii\db\Exception $e) {
        //     $transaction->rollBack();
        //     \Yii::$app->response->statusCode = 500;
        //     return [
        //         'message' => $e->getMessage()
        //     ];
        // } catch (\Exception $e) {
        //     $transaction->rollBack();
        //     \Yii::$app->response->statusCode = 500;
        //     return [
        //         'message' => $e->getMessage()
        //     ];
        // }
    }

    public function actionGetDataDietPasien()
    {
        $limit = Yii::$app->request->get('per-page', 10);
        $page = Yii::$app->request->get('page', 2);
        $id = Yii::$app->request->get('id');
        $order = Yii::$app->request->get('order');
        $model = new PermintaanMakan;

        $data = PermintaanMakan::find()->select([
            'permintaanmakan_t.tgl_permintaanmakan',
            'permintaanmakan_t.catatan_diet',
            'permintaanmakan_t.peg_pemesan_id',
            'pegawai_m.nama_pegawai'
        ])->where([
            'pendaftaran_id' => $id
        ])->join('LEFT JOIN', 'pegawai_m', 'pegawai_m.pegawai_id=permintaanmakan_t.peg_pemesan_id')->orderBy([
            'permintaanmakan_t.tgl_permintaanmakan' => SORT_DESC
        ]);
        $data = DocoRestActiveFilter::advancedFilter($model, $data);
        $totalRecord = $data->count();
        $data = $data->offset(($page - 1) * $limit)->limit($limit)->asArray()->all();
        return [
            'data' => $data,
            'totalRecord' => $totalRecord,
            'recordsFiltered' => $totalRecord,
        ];
    }

    public function actionVerifikasiSkriningGizi()
    {
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->post('pendaftaran_id');
            $model = $this->askepModel->find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
            $model->is_verifikasigizi = true;
            $model->pegawaiverifikasigizi_id = Yii::$app->jwt->user->pegawai_id;
            $model->tgl_verifikasigizi = date('Y-m-d H:i:s');
            if ($model->save(false)) {
                return ['message' => 'Sukses'];
            } else {
                \Yii::$app->response->statusCode = 500;
                return [
                    'message' => 'Terjadi kesalahan sistem'
                ];
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}
