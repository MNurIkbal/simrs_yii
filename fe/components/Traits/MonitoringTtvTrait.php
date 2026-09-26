<?php

namespace app\components\Traits;

use Yii;
use yii\base\Exception;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use app\components\Traits\Pelayanan\MonitoringTtvForm;

trait MonitoringTtvTrait
{
    public function actionMonitoringTtv()
    {
        $title = 'Monitoring Tanda-Tanda Vital';
        $this->setServicePath();
        $url = $this->urlFrontend;
        $modul = $this->modul;
        $request = Yii::$app->request;
        $pendaftaranId = $request->get('id');
        $pendaftaranIdDecrypt = DocoHelpers::decrypt($pendaftaranId);
        $lov = $this->getFilter($pendaftaranIdDecrypt);
        $sumber = ArrayHelper::getValue($lov, 'sumberTtv', []);
        return $this->renderAjax('//monitoring-ttv/index', compact('title','pendaftaranId', 'url', 'modul', 'pendaftaranIdDecrypt', 'sumber'));
    }

    public function actionGetDataMonitoringTtv()
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
            'url' => "monitoring-ttv/get-data",
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

    public function actionInputTtv()
    {
        $this->setServicePath();
        $title = 'Input TTV Pasien';
        $actionButton = "Simpan";
        $request = Yii::$app->request;
        $pendaftaranId = $request->get('pendaftaran_id');
        $model = new MonitoringTtvForm;
        $url = $this->urlFrontend;
        $modul = $this->modul;
        $lov = $this->getFilter(DocoHelpers::decrypt($pendaftaranId));
        $kesadaran = ArrayHelper::getValue($lov, 'tingkatKesadaran', []);
        $jenis = ArrayHelper::getValue($lov, 'jenisTtv', []);
        $sumber = ArrayHelper::getValue($lov, 'sumberTtv', []);
        $model->tanggal_ttv = date('d/m/Y H:i');
        $tglPendaftaran = ArrayHelper::getValue($lov, 'tgl_pendaftaran');
        $sumberTtvId = DocoConstants::SUMBER_TTV_MONITORING_ID;
        if(Yii::$app->request->post()) {
            $data = Yii::$app->request->post();
            $actionUrl = 'monitoring-ttv/insert';
            return $this->processData('save', $actionUrl, $model, $lov, $data);
        }

        return $this->renderAjax(
            '//monitoring-ttv/form', 
            compact(
                'title', 
                'pendaftaranId', 
                'model', 
                'url', 
                'modul', 
                'kesadaran', 
                'jenis', 
                'sumber',
                'tglPendaftaran',
                'sumberTtvId'
            )
        );
    }

    public function actionEditTtv()
    {
        if (Yii::$app->request->post()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }
        $title = "Edit TTV Pasien";
        $actionButton = "Update";
        $this->setServicePath();
        $request = Yii::$app->request;
        $vitalsignId = $request->get('vitalsign_id');
        $pendaftaranId = $request->get('pendaftaran_id');
        $sumberTtvId = $request->get('sumberttv_id');
        $model = new MonitoringTtvForm;
        $url = $this->urlFrontend;
        $modul = $this->modul;
        $vitalSignData = $this->fetchDetail($vitalsignId, DocoHelpers::decrypt($pendaftaranId));
        $lov = $this->getFilter(DocoHelpers::decrypt($pendaftaranId));
        $tglPendaftaran = ArrayHelper::getValue($lov, 'tgl_pendaftaran');
        $kesadaran = ArrayHelper::getValue($lov, 'tingkatKesadaran', []);
        $jenis = ArrayHelper::getValue($lov, 'jenisTtv', []);
        $sumber = ArrayHelper::getValue($lov, 'sumberTtv', []);
        if(Yii::$app->request->post()) {
            $data = Yii::$app->request->post();
            $actionUrl = 'monitoring-ttv/update';

            return $this->processData('update', $actionUrl, $model, $lov, $data);
        }

        return $this->renderAjax(
            '//monitoring-ttv/form', 
            compact(
                'title', 
                'pendaftaranId', 
                'model', 
                'url', 
                'modul', 
                'kesadaran', 
                'jenis', 
                'sumber', 
                'vitalSignData',
                'tglPendaftaran',
                'sumberTtvId'
            )
        );

    }

    public function actionRemoveTtv()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->setServicePath();
        
        $request = Yii::$app->request;
        $vitalsignId = $request->post('vitalsign_id');

        if ($vitalsignId === null) {
            Yii::$app->response->statusCode = 400;
            return [
                'message' => 'Missing required parameters',
                'payload' => [
                    'vitalsign_id' => $vitalsignId
                ]
            ];
        }

        try {
            $response = $this->guzzleExec($this->serviceRest, [
                'url' => 'monitoring-ttv/delete',
                'method' => 'delete', // Changed to DELETE method
                'payload' => [
                    'form_params' => [
                        'vitalsign_id' => $vitalsignId
                    ]
                ]
            ]);

            return [
                'message' => 'TTV data removed successfully'
            ];
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

    private function processData($type, $actionUrl, $model, $lov, $data)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $modelName = substr(strrchr(get_class($model), "\\"), 1);
        $formData = ArrayHelper::getValue($data, $modelName, []);
        $dt = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', '27-08-2025 09:50:42')));
        $formData['pendaftaran_id'] = DocoHelpers::decrypt($formData['pendaftaran_id']);
        $formData['sumberttv_id'] = ArrayHelper::getValue($formData, 'sumberttv_id');
        $formData['sumberttv'] = ArrayHelper::getValue($lov['sumberTtv'], $formData['sumberttv_id'], '');
        $formData['tanggal_ttv'] = !empty($formData['tanggal_ttv']) ? date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $formData['tanggal_ttv']))) : null;
        $formData['jenisttv'] = ArrayHelper::getValue($lov['jenisTtv'], $formData['jenisttv_id'], '');
        $formData['tingkatkesadaran'] = ArrayHelper::getValue($lov['tingkatKesadaran'], $formData['tingkatkesadaran_id'], '');
        
        $model->attributes = $formData;
        if(!$model->validate()) {
            $response = $model->errors;
            return DocoHelpers::response($response, 422, $modelName);
        }

        $method = $type === 'update' ? 'put' : 'post';
        $response = $this->guzzleExec($this->serviceRest, [
            'url' => $actionUrl,
            'method' => $method,
            'payload' => [
                'form_params' => $model->attributes,
            ]
        ]);

        return DocoHelpers::response($response);
    }

    private function fetchDetail($vitalsignId, $pendaftaranId)
    {
        $response = $this->guzzleExec($this->serviceRest, [
            'url' => 'monitoring-ttv/get-detail',
            'method' => 'get',
            'payload' => [
                'query' => [
                    'vitalsign_id' => $vitalsignId,
                    'pendaftaran_id' => $pendaftaranId
                ]
            ]
        ]);

        return $response;
    }

    private function getFilter($pendaftaranId) 
    {
        $response = $this->guzzleExec($this->serviceRest, [
            'url' => 'monitoring-ttv/get-filter',
            'method' => 'get',
            'payload' => [
                'query' => [
                    'pendaftaran_id' => $pendaftaranId
                ]
            ]
        ]);

        return $response;
    }

    public function actionCetakMonitoringTtv()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/cetak-resume-medis.pdf";
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        try {
            if(Yii::$app->report->enabled){
                return Yii::$app->report->exec('monitoring-ttv?id='.DocoHelpers::decrypt($pendaftaran_id));
            }
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}
