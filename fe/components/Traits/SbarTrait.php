<?php

namespace app\components\Traits;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use app\components\Traits\Pelayanan\SbarForm;
use GuzzleHttp\Exception\RequestException;

trait SbarTrait
{
    public function actionSbar()
    {
        $title = 'SBAR';
        $this->setServicePath();
        $url = $this->urlFrontend;
        $modul = $this->modul;
        $request = Yii::$app->request;
        $pendaftaranId = $request->get('id');
        $model = new SbarForm;
        $instalasiId = Yii::$app->docoVars->workspace("instalasi_id");
        $userIdentity = Yii::$app->session->get('user_identity');
        $createdBy = ArrayHelper::getValue($userIdentity, 'loginpemakai_id');
        return $this->renderAjax('//sbar/index', compact('title','pendaftaranId', 'url', 'modul', 'model', 'instalasiId', 'createdBy'));
    }

    public function actionInputSbar()
    {
        $this->setServicePath();
        $request = Yii::$app->request;
        $pendaftaranId = $request->get('pendaftaran_id');
        if(!is_numeric($pendaftaranId)) {
            $pendaftaranId = DocoHelpers::decrypt($pendaftaranId);
        }
        $sbarId = $request->get('sbar_id');
        $title = $sbarId ? 'Update SBAR' : 'Input SBAR';
        $model = new SbarForm;
        $url = $this->urlFrontend;
        $modul = $this->modul;
        $response = [];
        $instalasiId = Yii::$app->docoVars->workspace("instalasi_id");
        if(empty(Yii::$app->request->post())) {
            $response = $this->getData($sbarId, $pendaftaranId, $instalasiId);
        }
        
        $sbarData = ArrayHelper::getValue($response, 'data', []);
        $dokterNama = ArrayHelper::getValue($sbarData, 'dokter_tujuan');
        $tglPendaftaran = ArrayHelper::getValue($response, 'tgl_pendaftaran');
        $tglPulang = ArrayHelper::getValue($response, 'tglpasienpulang');
        $isEditable = false;
        $model->tgl_sbar = $sbarData ? date('d/m/Y H:i', strtotime($model->tgl_sbar)) : date('d/m/Y H:i', strtotime(date('Y-m-d H:i:s')));
        $model->tgl_pendaftaran = date(SbarForm::DATETIME_FORMAT, strtotime($tglPendaftaran));
        $model->tglpasienpulang = $tglPulang ? date(SbarForm::DATETIME_FORMAT, strtotime($tglPulang)) : '';

        if (!empty($sbarData)) {
            $model->setAttributes($sbarData, false);
            $createdBy = ArrayHelper::getValue($sbarData, 'created_by');
            $userIdentity = Yii::$app->session->get('user_identity');
            if ($createdBy == ArrayHelper::getValue($userIdentity, 'loginpemakai_id')) {
                $isEditable = true;
            }
        }
        
        if(Yii::$app->request->post()) {
            $data = Yii::$app->request->post();
            $modelName = substr(strrchr(get_class($model), "\\"), 1);
            $formData = ArrayHelper::getValue($data, $modelName, []);
            $sbarIdFromPost = ArrayHelper::getValue($formData, 'sbar_id');
            if($sbarIdFromPost && $sbarIdFromPost !== '') {
                $responseCheck = $this->getData($sbarIdFromPost, $pendaftaranId, $instalasiId);
                $sbarDataCheck = ArrayHelper::getValue($responseCheck, 'data', []);
                $existingTglSbar = ArrayHelper::getValue($sbarDataCheck, 'tgl_sbar');
                if(empty($sbarDataCheck)) {
                    return DocoHelpers::response([
                        'title' => 'Proses Gagal!',
                        'message' => 'Data SBAR telah dihapus.'
                    ], 422);
                }
                $data['SbarForm']['tgl_sbar'] = $existingTglSbar;
            }
            $actionUrl = '/save-sbar';
            return $this->processDataSbar($this->urlBackend.$actionUrl, $model, $data);
        }

        return $this->renderAjax(
            '//sbar/form',
            compact(
                'title',
                'pendaftaranId',
                'model',
                'url',
                'modul',
                'sbarId',
                'sbarData',
                'tglPendaftaran',
                'tglPulang',
                'isEditable',
                'instalasiId',
                'dokterNama'
            )
        );
    }

    private function processDataSbar($actionUrl, $model, $data)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $modelName = substr(strrchr(get_class($model), "\\"), 1);
        $formData = ArrayHelper::getValue($data, $modelName, []);
        $tanggalSbar = ArrayHelper::getValue($formData, 'tgl_sbar');
        $formData['tgl_sbar'] = $tanggalSbar ?
            date(SbarForm::DATETIME_FORMAT, strtotime(str_replace('/', '-', $tanggalSbar))) : '';

        $model->attributes = $formData;
        if(!$model->validate()) {
            $response = $model->errors;
            return DocoHelpers::response($response, 422, $modelName);
        }

        $sbarId = ArrayHelper::getValue($formData, 'sbar_id');
        if($sbarId) {
            $model->sbar_id = $sbarId;
            $userIdentity = Yii::$app->session->get('user_identity');
            $pegawaiId = ArrayHelper::getValue($userIdentity, 'loginpemakai_id');
            $model->loginpemakai_id = $pegawaiId;
        }

        if(!is_numeric($model->pendaftaran_id)) {
            $model->pendaftaran_id = DocoHelpers::decrypt($model->pendaftaran_id);
        }
        
        $response = $this->serviceRest->post($actionUrl,[
            'form_params' => $model->attributes
        ]);
        $response = json_decode($response->getBody(),true);
        return DocoHelpers::response($response);
    }

    public function actionGetDataSbar()
    {
        $this->setServicePath();
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $pendaftaranId = $request->get('pendaftaran_id');
        if(!is_numeric($pendaftaranId)) {
            $pendaftaranId = DocoHelpers::decrypt($pendaftaranId);
        }
        
        $payload = DocoDatatableHelper::advancedFilterParam();
        $payload['pendaftaran_id'] = $pendaftaranId;
        $response = $this->guzzleExec($this->serviceRest, [
            'url' => $this->urlBackend."/get-data-sbar",
            'payload' => [
                'query' => $payload,
            ]
        ]);
        foreach ($response['data'] as $key => $value) {
            $response['data'][$key]['primary'] = DocoHelpers::encrypt($value['sbar_id']);
        }
        $response['recordsTotal'] = count($response['data']);
        $response['recordsFiltered'] = count($response['data']);
        return $response;
    }

    public function actionVerifikasi()
    {
        $this->setServicePath();
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        if(Yii::$app->request->post()) {
            $sbarId = $request->post('sbar_id');
            $userIdentity = Yii::$app->session->get('user_identity');
            $pegawaiId = ArrayHelper::getValue($userIdentity, 'loginpemakai_id');
            $response = $this->serviceRest->post($this->urlBackend. '/verifikasi',[
                'form_params' => [
                    'sbar_id' => $sbarId,
                    'pegawai_verifikasi_id' => $pegawaiId
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            return DocoHelpers::response($response);
        }
    }

    public function actionCopyTtv()
    {
        $this->setServicePath();
        $request = Yii::$app->request;
        $pendaftaranId = $request->get('pendaftaran_id');
        $pendaftaranId = DocoHelpers::encrypt($pendaftaranId);
        $url = $this->urlFrontend;
        $modul = $this->modul;
        return $this->renderAjax('//sbar/copy_ttv', compact('pendaftaranId', 'url', 'modul'));
    }

    private function getData($sbarId, $pendaftaranId, $instalasiId)
    {
        if(!is_numeric($pendaftaranId)) {
            $pendaftaranId = DocoHelpers::decrypt($pendaftaranId);
        }

        $this->setServicePath();
        return $this->guzzleExec($this->serviceRest, [
            'method' => 'GET',
            'url' => $this->urlBackend. '/get-sbar-by-id',
            'payload' => [
                'query' => [
                    'sbar_id' => $sbarId,
                    'pendaftaran_id' => $pendaftaranId,
                    'instalasi_id' => $instalasiId
                ]
            ]
        ]);
    }

    public function actionFiltersSbar()
	{
		$this->setServicePath();
        return $this->guzzleExec($this->serviceRest, [
			'url' => $this->urlBackend. '/filters-sbar',
			'payload' => [
				'query' => Yii::$app->request->get()
			],
			'returnResponse' => true
	  	]);
	}

    public function actionDeleteSbar()
    {
        $this->setServicePath();
        $request = Yii::$app->request;
        $sbarId = $request->post('sbar_id');
        $response = $this->serviceRest->post($this->urlBackend. '/delete-sbar',[
            'form_params' => [
                'sbar_id' => $sbarId
            ]
        ]);
        $response = json_decode($response->getBody(),true);
        return DocoHelpers::response($response);
    }

    public function actionGetDataTtv()
    {
        $this->setServicePath();
        Yii::$app->response->format = Response::FORMAT_JSON;
        $userId = Yii::$app->user->id;
        $request = Yii::$app->request;
        $pendaftaranId = $request->get('pendaftaran_id');
        $pendaftaranId = DocoHelpers::decrypt($pendaftaranId);
        $payload = DocoDatatableHelper::advancedFilterParam();
        $payload['pendaftaran_id'] = $pendaftaranId;
        $response = $this->guzzleExec($this->serviceRest, [
            'url' => $this->urlBackend. "/get-data-ttv",
            'payload' => [
                'query' => $payload,
            ]
        ]);
        foreach ($response['data'] as $key => $value) {
            $response['data'][$key]['primary'] = DocoHelpers::encrypt($value['vitalsign_id']);
            $response['data'][$key]['hasAccess'] = $userId === $value['created_by'];
        }
        $response['recordsTotal'] = $response['_meta']['totalCount'];
        $response['recordsFiltered'] = $response['_meta']['totalCount'];
        return $response;
    }

    public function actionCheckStatus()
    {
        $this->setServicePath();
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $sbarId = $request->get('id');
        return $this->guzzleExec($this->serviceRest, [
            'method' => 'GET',
            'url' => $this->urlBackend. '/check-status',
            'payload' => [
                'query' => [
                    'sbar_id' => $sbarId,
                ]
            ]
        ]);
    }

    public function actionCetakSbar()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/cetak-sbar.pdf";
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        try {
            if(Yii::$app->report->enabled){
                return Yii::$app->report->exec('sbar?id='.DocoHelpers::decrypt($pendaftaran_id));
            }
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}
