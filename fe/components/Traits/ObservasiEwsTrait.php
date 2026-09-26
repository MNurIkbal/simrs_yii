<?php

namespace app\components\Traits;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use app\components\Traits\Pelayanan\ObservasiEwsForm;
use GuzzleHttp\Exception\RequestException;

trait ObservasiEwsTrait
{
    public function actionObservasiEws()
    {
        $title = 'Observasi EWS';
        $this->setServicePath();
        $url = $this->urlFrontend;
        $modul = $this->modul;
        $request = Yii::$app->request;
        $pendaftaranId = $request->get('id');
        $model = new ObservasiEwsForm;

        $response = $this->guzzleExec($this->serviceRest, [
            'method' => 'GET',
            'url' => $this->urlBackend. '/get-index-data-ews',
            'payload' => [
                'query' => [
                    'pendaftaran_id' => !is_numeric($pendaftaranId) ? DocoHelpers::decrypt($pendaftaranId) : $pendaftaranId
                ]
            ]
        ]);
        $additionalData = ArrayHelper::getValue($response, 'ews.additional_data');
        $additionalData = json_decode($additionalData, true);
        $latestJenisEws = ArrayHelper::getValue($additionalData, 'jenis_ews');
        $latestJenisEwsNama = ArrayHelper::getValue($additionalData, 'jenis_ews_nama');

        return $this->renderAjax('//observasi-ews/index', compact('title','pendaftaranId', 'url', 'modul', 'model', 'latestJenisEws', 'latestJenisEwsNama'));
    }

    public function actionInputEws()
    {
        $this->setServicePath();
        $request = Yii::$app->request;
        $pendaftaranId = $request->get('pendaftaran_id');
        if(!is_numeric($pendaftaranId)) {
            $pendaftaranId = DocoHelpers::decrypt($pendaftaranId);
        }
        $jenisEwsId = $request->get('jenisews_id');
        $jenisEws = $request->get('jenisews');
        $ewsId = $request->get('ews_id');
        $title = $ewsId ? 'Update EWS' : 'Input EWS';
        
        $model = new ObservasiEwsForm;
        $url = $this->urlFrontend;
        $modul = $this->modul;
        $ewsData = [];
        $userIdentity = Yii::$app->session->get('user_identity');
        
        // Hanya eksekusi jika ada data POST
        if(empty(Yii::$app->request->post())) {
            $response = $this->guzzleExec($this->serviceRest, [
                'method' => 'GET',
                'url' => $this->urlBackend. '/get-ews-by-id',
                'payload' => [
                    'query' => [
                        'ews_id' => $ewsId,
                        'pendaftaran_id' => $pendaftaranId
                    ]
                ]
            ]);
        } else {
            $response = [];
        }
        $ewsData = ArrayHelper::getValue($response, 'data', []);
        $tglPendaftaran = ArrayHelper::getValue($response, 'tgl_pendaftaran');
        $lastTtv = ArrayHelper::getValue($response, 'lastTtv');
        $isLastTtv = ArrayHelper::getValue($response, 'isLastTtv');
        $scoringRules = null;
        if($isLastTtv){
            $scoringRules = $this->guzzleExec($this->serviceRest, [
                'method' => 'GET',
                'url' => $this->urlBackend . '/get-all-skor-rules',
                'payload' => ['query' => ['jenis_ews' => $jenisEwsId]]
            ]);
            
        }
        $isEditable = false;
        if (!empty($ewsData)) {
            $model->setAttributes($ewsData, false);
            $model->tanggal_ews = date('d/m/Y H:i', strtotime($model->tanggal_ews));
            $createdBy = ArrayHelper::getValue($ewsData, 'created_by');
            if ($createdBy == ArrayHelper::getValue($userIdentity, 'loginpemakai_id')) {
                $isEditable = true;
            }
        } else {
            $model->tanggal_ews = date('d/m/Y H:i');

            // get last data from ttv
            $model->sistolik = ArrayHelper::getValue($lastTtv, 'sistolik');
            $model->diastolik = ArrayHelper::getValue($lastTtv, 'diastolik');
            $model->nadi = ArrayHelper::getValue($lastTtv, 'nadi');
            $model->spo2 = ArrayHelper::getValue($lastTtv, 'spo2');
            $model->suhu = ArrayHelper::getValue($lastTtv, 'suhu');
            $model->nafas = ArrayHelper::getValue($lastTtv, 'respirasi');
        }
        
        $model->jenis_ews = $jenisEwsId;
        $model->jenis_ews_nama = ucfirst($jenisEws);
        $model->pegawai_id = ArrayHelper::getValue($userIdentity, 'id_pegawai');
        $model->pegawai_nama = ArrayHelper::getValue($userIdentity, 'nama_pegawai');
        $partial = $this->mapingFormEws($jenisEwsId);
        if(Yii::$app->request->post()) {
            $data = Yii::$app->request->post();
            $ewsIdFromPost = ArrayHelper::getValue($data, 'ews_id');
            
            if ($ewsIdFromPost && $ewsIdFromPost !== '') {
                $actionUrl = $this->urlBackend. '/update-ews';
                return $this->processDataEws('update', $actionUrl, $model, $data);
            } else {
                $actionUrl = $this->urlBackend. '/save-ews';
                return $this->processDataEws('save', $actionUrl, $model, $data);
            }
        }

        return $this->renderAjax(
            '//observasi-ews/form',
            compact(
                'title',
                'pendaftaranId',
                'model',
                'url',
                'modul',
                'ewsId',
                'ewsData',
                'partial',
                'tglPendaftaran',
                'isEditable',
                'isLastTtv',
                'scoringRules'
            )
        );
    }


    private function mapingFormEws($jenisEwsId)
    {
        switch ($jenisEwsId) {
            case DocoConstants::JENIS_EWS_DEWASA:
                $form = '_dewasa';
                break;

            case DocoConstants::JENIS_EWS_ANAK:
                $form = '_anak';
                break;

            case DocoConstants::JENIS_EWS_KEBIDANAN:
                $form = '_kebidanan';
                break;
            
            default:
                $form = '_ibu_hamil';
                break;
        }

        return $form;
    }

    public function actionGetSkor()
    {
        $this->setServicePath();
        $request = Yii::$app->request;
        return $this->guzzleExec($this->serviceRest, [
            'url' => $this->urlBackend. '/get-skor',
            'payload' => [
                'query' => [
                    'jenis' => $request->get('jenis'),
                    'parameter' => $request->get('parameter'),
                    'value' => $request->get('value')
                ]
            ],
            'returnResponse' => true
        ]);
    }

    public function actionFilters()
	{
		$this->setServicePath();
        return $this->guzzleExec($this->serviceRest, [
			'url' => $this->urlBackend. '/filters-ews',
			'payload' => [
				'query' => Yii::$app->request->get()
			],
			'returnResponse' => true
	  	]);
	}

    public function actionBukuPanduanEws()
    {
        $path = Yii::getAlias("@download") . "/cetak-buku-panduan-ews.pdf";
        try {
            if (Yii::$app->report->enabled) {
                return Yii::$app->report->exec('buku-panduan-ews?');
            }
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    private function processDataEws($type, $actionUrl, $model, $data)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $modelName = substr(strrchr(get_class($model), "\\"), 1);
        $formData = ArrayHelper::getValue($data, $modelName, []);
        $tanggalEws = ArrayHelper::getValue($formData, 'tanggal_ews');
        $formData['tanggal_ews'] = $tanggalEws ?
            date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $tanggalEws))) : '';

        $model->attributes = $formData;
        if(!$model->validate()) {
            $response = $model->errors;
            return DocoHelpers::response($response, 422, $modelName);
        }

        $method = ($type === 'update') ? 'put' : 'post';
        
        $backendData = $model->attributes;
        if ($type === 'update') {
            $backendData['ews_id'] = ArrayHelper::getValue($data, 'ews_id');
        }
        
        $payload = [];
        if ($method === 'put') {
            $payload['json'] = $backendData;
        } else {
            $payload['form_params'] = $backendData;
        }
        
        $response = $this->guzzleExec($this->serviceRest, [
            'url' => $actionUrl,
            'method' => $method,
            'payload' => $payload
        ]);
        
        $httpStatusCode = ArrayHelper::getValue($response, 'httpStatusCode');
        if ($httpStatusCode && $httpStatusCode !== 200) {
            if (is_array($response)) {
                return DocoHelpers::response($response, $httpStatusCode);
            }
        }
        
        return DocoHelpers::response($response);
    }
    
    public function actionGetDataObservasiEws()
    {
        $this->setServicePath();
        try {
            $request = Yii::$app->request;
            $limit = $request->post('limit', 20);
            $jenisEws = $request->post('jenisEwsId');
            $pendaftaranId = $request->post('pendaftaran_id');
            $date = $request->post('date');
            if(!is_numeric($pendaftaranId)) {
                $pendaftaranId = DocoHelpers::decrypt($pendaftaranId);
            }

            $response = $this->guzzleExec($this->serviceRest, [
                'url' => $this->urlBackend. '/get-data-table-ews',
                'payload' => [
                    'query' => [
                        'date' => $date,
                        'limit' => $limit,
                        'jenis_ews' => $jenisEws,
                        'pendaftaran_id' => $pendaftaranId
                    ]
                ]
            ]);


            $totalData = ArrayHelper::getValue($response, 'totalData');
            $header = ArrayHelper::getValue($response, 'mappingHeader');
            $headerIds = ArrayHelper::getValue($response, 'mappingHeaderIds');
            $body = ArrayHelper::getValue($response, 'mappingData');
            $scoreFooter = ArrayHelper::getValue($response, 'mappingScore');
            $lastEws = ArrayHelper::getValue($response, 'lastEws');

            $data = $this->renderAjax('//observasi-ews/partial/table-ews', compact('limit', 'totalData', 'header', 'headerIds', 'body', 'scoreFooter'));
            return DocoHelpers::response([
                'data' => $data,
                'totalRecords' => count($header),
                'limit' => $limit,
                'totalData' => $totalData,
                'lastEws' => $lastEws
            ]);
        } catch (\Throwable $th) {
            return DocoHelpers::response($th->getMessage(), 500);
        }
    }

    public function actionDeleteEws()
        {
        $this->setServicePath();
        $request = Yii::$app->request;
        $ewsId = $request->post('ews_id');
        $pendaftaranId = $request->post('pendaftaran_id');
        
        if (empty($ewsId)) {
            Yii::$app->response->statusCode = 400;
            return DocoHelpers::response('EWS ID tidak ditemukan.', 400);
        }

        $response = $this->guzzleExec($this->serviceRest, [
            'url' => $this->urlBackend. '/delete-ews',
            'method' => 'POST',
            'payload' => [
                'form_params' => [
                    'ews_id' => $ewsId,
                    'pendaftaran_id' => $pendaftaranId
                ]
            ]
        ]);

        return DocoHelpers::response($response);
    }

    public function actionRecalculateEws()
    {
        $this->setServicePath();
        try {
            $request = Yii::$app->request;
            $pendaftaranId = $request->get('pendaftaran_id');
            $jenisEws = $request->get('jenis_ews');
            $newJenisEws = $request->get('new_jenis_ews');

            $response = $this->guzzleExec($this->serviceRest, [
                'url' => $this->urlBackend. '/recalculate-score',
                'method' => 'GET',
                'payload' => [
                    'query' => [
                        'pendaftaran_id' => is_numeric($pendaftaranId) ? $pendaftaranId : DocoHelpers::decrypt($pendaftaranId),
                        'jenis_ews' => $jenisEws,
                        'new_jenis_ews' => $newJenisEws
                    ]
                ],
            ]);

            return DocoHelpers::response($response);
        } catch (\Throwable $th) {
            return DocoHelpers::response($th->getMessage(), 500);
        }
    }
}
