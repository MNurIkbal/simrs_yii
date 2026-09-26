<?php

namespace Doco\Traits;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoMessages;
use Doco\models\Lookup;
use Doco\models\Pendaftaran;
use Doco\models\Sbar;
use Doco\models\VitalSign;
use Doco\models\SbarView;
use Doco\models\PegawaiView;
use Doco\components\DocoRestActiveFilter;
use yii\data\ActiveDataProvider;
use Doco\exceptions\ValidationException;
use Doco\models\PasienPulang;
use Doco\models\PasienAdmisi;

trait SbarTrait
{
    public function actionGetSbarById()
    {
        $request = Yii::$app->request;
        try {
            $sbarId = $request->get('sbar_id');
            $pendaftaranId = $request->get('pendaftaran_id');
            $instalasiId = $request->get('instalasi_id');
            $sbarData = [];
            if($sbarId) {
                $sbarData = SbarView::find()->where(['sbar_id' => $sbarId])->asArray()->one();
            }
            
            $pendaftaran = Pendaftaran::findOne($pendaftaranId);
            $tglPendaftaran = ArrayHelper::getValue($pendaftaran, 'tgl_pendaftaran');
            if($instalasiId == DocoConstants::INST_ID_RI && (!empty($pendaftaran->pasienpulang_id) && !empty($pendaftaran->pasienadmisi_id))){
                $pasienadmisi = PasienAdmisi::findOne($pendaftaran->pasienadmisi_id);
                $tglPendaftaran = ArrayHelper::getValue($pasienadmisi, 'tgl_admisi');
            }else{
                $tglPendaftaran = ArrayHelper::getValue($pendaftaran, 'tgl_pendaftaran');
                $pasienPulang = PasienPulang::findOne($pendaftaran->pasienpulang_id);
            }
            if(!empty($pendaftaran->pasienadmisi_id)){
                if($instalasiId == DocoConstants::INST_ID_RD){
                    $pasienPulang = PasienPulang::findOne($pendaftaran->pasienpulang_id);
                }else{
                    $pasienadmisi = PasienAdmisi::findOne($pendaftaran->pasienadmisi_id);
                    $pasienPulang = PasienPulang::findOne($pasienadmisi->pasienpulang_id);
                }
            }else{
                $tglPendaftaran = ArrayHelper::getValue($pendaftaran, 'tgl_pendaftaran');
                $pasienPulang = PasienPulang::findOne($pendaftaran->pasienpulang_id);
            }
            
            $tglPulang = ArrayHelper::getValue($pasienPulang, 'tglpasienpulang');

            return [
                'data' => $sbarData,
                'tgl_pendaftaran' => $tglPendaftaran,
                'tglpasienpulang' => $tglPulang
            ];
        } catch (\Throwable $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionSaveSbar()
    {
        $request = Yii::$app->request;
        $data = $request->post();
        $sbarId = ArrayHelper::getValue($data, 'sbar_id');
        $model = new Sbar();
        $message = '';
        if ($sbarId) {
            $message = $this->validateExistingSbar($data, $sbarId, $model);
            if ($message) {
                return $this->returnErrorMessage($message);
            }
        }

        $model->load($data, '');
        $model->is_verifikasi = false;

        if ($model->validate()) {
            $model->save();
            if ($model->is_ttv) {
                $data['is_ttv'] = $model->is_ttv;
                $this->handleTtvIntegration($data, $model);
            }

            return [
                'message' => 'Data berhasil di Simpan.',
                'payload' => $model
            ];
        }

        Yii::$app->response->statusCode = 422;
        return [
            'message' => 'Data gagal di Simpan.',
            'errors' => $model->getErrors()
        ];
    }

    private function integrateTtv($data, $vitalsignId)
    {
        $result = null;
        $pendaftaranId = ArrayHelper::getValue($data, 'pendaftaran_id');
        $pendaftaran = Pendaftaran::findOne($pendaftaranId);
        $pasienId = ArrayHelper::getValue($pendaftaran, 'pasien_id');
        
        $vitalSignFields = ['sistol', 'diastol', 'nadi', 'suhu', 'spo2', 'respirasi', 'tinggi_badan', 'berat_badan'];
        $hasVitalSignData = false;

        foreach ($vitalSignFields as $field) {
            if (!empty($data[$field])) {
                $hasVitalSignData = true;
                break;
            }
        }

        if ($hasVitalSignData) {
            $vitalSign = $vitalsignId ? VitalSign::findOne($vitalsignId) : new VitalSign();
            $vitalSign->pasien_id       = $pasienId;
            $vitalSign->pendaftaran_id  = $pendaftaranId;
            $vitalSign->tanggal_ttv     = date('Y-m-d H:i:s');
            $vitalSign->sumberttv_id    = DocoConstants::SUMBER_TTV_SBAR;
            $vitalSign->sumberttv       = $this->getSumberTtv(DocoConstants::SUMBER_TTV_SBAR);
            $vitalSign->is_active       = true;
            $vitalSign->is_deleted      = false;

            $map = [
                'sistol' => 'sistol',
                'diastol' => 'diastol',
                'nadi' => 'nadi',
                'suhu' => 'suhu',
                'spo2' => 'spo2',
                'respirasi' => 'respirasi',
                'tinggi_badan' => 'tinggi_badan',
                'berat_badan' => 'berat_badan'
            ];

            foreach ($map as $inputKey => $modelAttr) {
                if (!empty($data[$inputKey])) {
                    $vitalSign->$modelAttr = $data[$inputKey];
                }
            }

            if (!$vitalSign->validate()) {
                Yii::$app->response->statusCode = 422;
                $result = [
                    'message' => 'Data gagal di Simpan.',
                    'errors' => $vitalSign->getErrors()
                ];
            } elseif ($vitalSign->save()) {
                $result = $vitalSign->vitalsign_id;
            }
        }

        return $result;
    }

    private function getSumberTtv($id)
    {
        $data = Lookup::findOne($id);
        return ArrayHelper::getValue($data, 'lookup_name');
    }

    public function actionGetDataSbar()
    {
        try {
            $request = Yii::$app->request;
            $pendaftaranId = $request->get('pendaftaran_id', null);
            $model = new SbarView;
            $query = $model::find()->where(['pendaftaran_id' => $pendaftaranId]);
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:59');
            if(isset($_GET['advanced-filter']['tgl_sbar'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_sbar']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_sbar']);
            }

            $query->andWhere(['between', 'tgl_sbar', $start, $end]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
            return new ActiveDataProvider([
				 'query' => $query,
                 'pagination' => false
			]);
        } catch (\yii\db\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionVerifikasi()
    {
        $request = Yii::$app->request;
        $data = $request->post();
        $sbarId = ArrayHelper::getValue($data, 'sbar_id');
        $pegawaiId = ArrayHelper::getValue($data, 'pegawai_verifikasi_id');
        
        if($sbarId) {
            $message = '';
            $model = Sbar::findOne($sbarId);
            $dokterTujuanId = ArrayHelper::getValue($model, 'dokter_tujuan_id');
            if(!$model || empty($model)) {
                $message = DocoMessages::MESSAGE_SBAR_DELETED;
            }
            $isVerifikasi = ArrayHelper::getValue($model, 'is_verifikasi', false);
            if(!empty($model) && $isVerifikasi) {
                $message = DocoMessages::MESSAGE_SBAR_VERIFIKASI;
            }

            if(!empty($model) && $dokterTujuanId != $pegawaiId) {
                $message = 'Data SBAR hanya dapat di Verifikasi oleh dokter tujuan.';
            }

            if($message) {
                $helpers = new DocoHelpers;
                return $helpers->callBack(DocoMessages::KEY_ERR_CUSTOM, [
                    'text' => $message
                ]);
            }

            $model->is_verifikasi = true;
            $model->pegawai_verifikasi_id = $pegawaiId;
            $model->tanggal_verifikasi = date('Y-m-d H:i:s');
            $model->save();
            return [
                'message' => 'Data berhasil di Verifikasi.',
                'payload' => $model
            ];
        }
    }

    public function actionFiltersSbar()
    {
        $request = Yii::$app->request;
        $payload = $request->get('payload', []);
        $type = ArrayHelper::getValue($payload, 'type');
        $instalasiId = ArrayHelper::getValue($payload, 'instalasi_id');
        $page = ArrayHelper::getValue($payload, 'page', 1);
        $limit = ArrayHelper::getValue($payload, 'limit', DocoConstants::LIMIT_INFINITY_SCROLL);
        $term = ArrayHelper::getValue($payload, 'term');

        if (empty($type)) {
            return [];
        }

        $result = $this->buildSbarQuery($type, $instalasiId, $term);

        if (!$result) {
            return [];
        }

        return $result->limit($limit + 1)
            ->offset(($page - 1) * $limit)
            ->asArray()
            ->all();
    }

    private function buildSbarQuery($type, $instalasiId, $term)
    {
        $query = PegawaiView::find()
            ->select(['pegawai_id as id', 'nama_pegawai as text'])
            ->distinct()
            ->orderBy(['nama_pegawai' => SORT_ASC]);

        switch ($type) {
            case 'dokter_tujuan':
                $query->where([
                    'kelompokpegawai_id' => [DocoConstants::KELOMPOK_PEGAWAI_DOKTER],
                    'is_active' => true,
                    'instalasi_id' => $instalasiId,
                ]);
                break;

            case 'pegawai_input':
                $query->where([
                    'kelompokpegawai_id' => [
                        DocoConstants::KELOMPOK_PEGAWAI_DOKTER,
                        DocoConstants::KELOMPOK_PEGAWAI_PERAWAT,
                    ],
                    'is_active' => true,
                ]);
                break;

            default:
                return null;
        }

        if (!empty($term)) {
            $query->andWhere(['like', 'LOWER(nama_pegawai)', strtolower($term)]);
        }

        return $query;
    }

    public function actionDeleteSbar()
    {
        $request = Yii::$app->request;
        $sbarId = $request->post('sbar_id');
        $model = Sbar::findOne($sbarId);
        $isVerifikasi = ArrayHelper::getValue($model, 'is_verifikasi', false);
        $message = '';
        if(!$model) {
            $message = DocoMessages::MESSAGE_SBAR_DELETED;
        }
        if($isVerifikasi) {
            $message = DocoMessages::MESSAGE_SBAR_VERIFIKASI;
        }

        if($message) {
            $helpers = new DocoHelpers;
            return $helpers->callBack(DocoMessages::KEY_ERR_CUSTOM, [
                'text' => $message
            ]);
        }

        try {
            if($model) {
                $currentVitalsignId = $model->vitalsign_id;
                $model->delete();
                if($currentVitalsignId) {
                    $this->deleteRelatedTtvSbar($currentVitalsignId);
                }
            }
            Yii::$app->response->statusCode = 200;
            return [
                'status' => 200,
                'message' => 'Data SBAR berhasil dihapus.'
            ];
        } catch (\Throwable $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'message' => 'Gagal menghapus data SBAR: ' . $th->getMessage()
            ];
        }
    }

    private function deleteRelatedTtvSbar($vitalsignId)
    {
        try {
        if($vitalsignId){
            $vitalSign = VitalSign::findOne($vitalsignId);
            if ($vitalSign) {
                $vitalSign->delete();
            }
        }
        } catch (\Throwable $th) {
            Yii::error('Error deleting related TTV: ' . $th->getMessage());
        }
    }

    public function actionGetDataTtv()
    {
        try {
            $request = Yii::$app->request;
            $pendaftaranId = $request->get('pendaftaran_id', null);
            $model = new VitalSign;
            $query = $model::find()
                ->where(['pendaftaran_id' => $pendaftaranId])
                ->andWhere(['is_deleted' => false]);

            $query->orderBy(['created_date' => SORT_DESC]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
            return new ActiveDataProvider([
				 'query' => $query,
			]);
        } catch (\yii\db\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    private function validateExistingSbar($data, $sbarId, &$model)
    {
        $loginPemakaiId = ArrayHelper::getValue($data, 'loginpemakai_id');
        $model = Sbar::findOne($sbarId);
        $text = '';
        if (!$model) {
            $text = DocoMessages::MESSAGE_SBAR_DELETED;
        }

        if(!empty($model)){
            if (ArrayHelper::getValue($model, 'is_verifikasi', false)) {
                $text = DocoMessages::MESSAGE_SBAR_VERIFIKASI;
            }
            if ($loginPemakaiId != $model->created_by) {
                $text = 'Data SBAR hanya dapat di edit oleh user penginput SBAR.';
            }
            
            $model->sbar_id = $sbarId;
        }
        return $text;
    }

    private function returnErrorMessage($message)
    {
        $helpers = new DocoHelpers;
        return $helpers->callBack(DocoMessages::KEY_ERR_CUSTOM, ['text' => $message]);
    }

    private function handleTtvIntegration($data, $model)
    {
        if (!ArrayHelper::getValue($data, 'is_ttv', false)) {
            return;
        }

        $vitalsignId = $this->integrateTtv($data, $model->vitalsign_id);
        if (empty($model->vitalsign_id) && $vitalsignId) {
            $model->vitalsign_id = $vitalsignId;
            $model->save();
        }
    }

    public function actionCheckStatus()
    {
        $request = Yii::$app->request;
        $sbarId = $request->get('sbar_id');
        $result = [];
        if(!empty($sbarId)) {
            $result = Sbar::find()->where(['sbar_id' => $sbarId])->asArray()->one();
        }
        return $result;
    }
}