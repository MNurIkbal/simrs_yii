<?php

namespace Doco\mcu\controllers;

use Yii;
use yii\web\Response;
use app\components\DHtml;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;

class InfMcuCollectiveController extends DocoController
{
    protected $_title = "Informasi Medical Checkup Collective";
    protected $_module = '/mcu/inf-mcu-collective/';
    protected $_restMcu;
    protected $_restKasir;

    public function init()
    {
        parent::init();
        $this->_restMcu = Yii::$app->docoRest->mcu;
        $this->_restKasir = Yii::$app->docoRest->kasir;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actionIndex()
    {
        $title = !empty(DHtml::getTitleMenu()) ? DHtml::getTitleMenu() : $this->_title;
        $data = $this->getDataDetail();
        $status_mcu = isset($data['status_mcu']) ? $data['status_mcu'] : [];
        $status_open = isset($data['status_open']) ? $data['status_open'] : null;
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $payload = DocoDatatableHelper::advancedFilterParam();
        $response = $this->guzzleExec($this->_restMcu, [
            'url' => 'inf-mcu-collective/index',
            'method' => 'get',
            'payload' => [
                'query' => $payload,
            ]
        ]);
        foreach ($response['data'] as $key => $value) {
            $response['data'][$key]['primary'] = DocoHelpers::encrypt($value['no_order']);
        }
        $response['recordsTotal'] = $response['_meta']['totalCount'];
        $response['recordsFiltered'] = $response['_meta']['totalCount'];
        return $response;
    }

	public function actionFilters($type = null, $id = null)
    {
        $response = $this->guzzleExec($this->_restMcu, [
            'url' => 'inf-mcu-collective/filters',
            'payload' => [
                'query' => [
                    'types' => $type,
                    'term' => Yii::$app->request->get('term'),
                    'additionalPayload' => Yii::$app->request->get('additionalPayload', []),
                    'page' => Yii::$app->request->get('page', 1),
                    'id' => $id,
                ]
            ],
        ]);
        if (!empty($response)) {
            if(!empty($type)) {
                if(isset($response[$type])) {
                    $response = $this->responseJson(200, 'Data berhasil diambil!', $response[$type]);
                    return $response;
                }
            }
        }
        return DocoHelpers::response($response);
    }

	public function actionDetail()
	{
		$request = Yii::$app->request;
		$title = 'Pendaftaran Kolektif';
		$id = $request->get('id', null);
        if(!empty($id)) {
            $id = DocoHelpers::decrypt($id);
        }
		$data = $this->getDataDetail($id);
        $header = isset($data['header']) ? $data['header'] : [];
        $status_reservasi = isset($data['status_reservasi']) ? $data['status_reservasi'] : [];
        $token = isset($data['token']) ? $data['token'] : null;
		return $this->render('detail', get_defined_vars());
	}

	private function getDataDetail($id = null)
	{
		return $this->guzzleExec($this->_restMcu, [
			'url' => 'inf-mcu-collective/get-data-detail',
			'payload' => [
				'query' => [
					'no_order' => $id,
				]
			]
		]);
	}

	public function actionListPasien()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
		$request = Yii::$app->request;
		$no_order = $request->get('no_order', null);
        $payload = DocoDatatableHelper::advancedFilterParam();
		$payload['no_order'] = $no_order;
        $response = $this->guzzleExec($this->_restMcu, [
            'url' => 'inf-mcu-collective/list-pasien',
            'payload' => [
                'query' => $payload,
            ]
        ]);
        foreach ($response['data'] as $key => $value) {
            $response['data'][$key]['primary'] = DocoHelpers::encrypt($value['reservasimcu_id']);
        }
        $response['recordsTotal'] = $response['_meta']['totalCount'];
        $response['recordsFiltered'] = $response['_meta']['totalCount'];
        return $response;
    }

    public function actionProsesInvoice()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $no_order = $request->get('no_order', null);
        $type = $request->get('type', null);
        $userIdentity = Yii::$app->session->get('user_identity');
        if($post = $request->post()) {
            $postData = $post['data'];
            if($type == 3) {
                $endPoint = 'lap-hasil-mcu/cetak-corporate';
                $params = [
                    'no_exportexcel' => $no_order,
                ];
            }
            else {
                $endPoint = 'inf-mcu-collective/proses-invoice';
                $params = [
                    'no_order' => $no_order,
                    'type' => $type,
                    'data_pegawai' => $userIdentity,
                ];
            }
            return $this->guzzleExec($this->_restMcu, [
                'url' => $endPoint,
                'method' => 'POST',
                'payload' => [
                    'query' => $params,
                    'form_params' => $postData,
                ]
            ]);
        }
    }

    public function actionDownloadFile()
    {
        $request = Yii::$app->request;
        $no_order = $request->get('no_order', null);
        if(!empty($no_order)) {
            $no_order = DocoHelpers::decrypt($no_order);
        }
        $fileDownloads = 'INVOICE '.$no_order.'.zip';
        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restKasir->get('tagihan-pasien/download-invoice-mcu',
        [
            'query' => [
                'no_order' => $no_order,
            ],
            'save_to' => $path,
        ]);
        $response = json_decode($response->getBody(), true);
        return DocoHelpers::downloadFile($path,true);
    }
}