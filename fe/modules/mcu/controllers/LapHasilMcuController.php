<?php

namespace Doco\mcu\controllers;

use Yii; 
use yii\web\Response;
use app\components\DocoController; 
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers; 
use GuzzleHttp\Exception\RequestException;

class LapHasilMcuController extends DocoController
{
    protected $allowAction = ['*'];
    protected $_title = "Laporan Hasil MCU Corporate";
    protected $_module = '/mcu/lap-hasil-mcu/';
    protected $_restMcu;
    
    public function init()
    {
        parent::init();
        $this->_restMcu = Yii::$app->docoRest->mcu;
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
        $title = $this->_title; 
        $InstansiRequest = $this->_restMcu->get('lap-hasil-mcu/list-penjamin');
        $body = json_decode($InstansiRequest->getBody(),TRUE);
        $instansi = $body['response'];

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restMcu->get('lap-hasil-mcu/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            $responData = $body['response']['data'];
            foreach ($responData as $key => $value) {
                $no++; 
                $no_exportexcel = $value['no_exportexcel'];
                $action = $this->_module."modal?no_exportexcel=".$no_exportexcel;
                $value['tgl_pendaftaran'] = date('d-M-Y', strtotime($value['tgl_pendaftaran']));
                $value['instansi'] = $value['instansi'];
                $value['jumlah_peserta'] = $value['jumlah_peserta'];
                $value['aksi'] ='<button type="button" class="btn btn-info btn-labeled btn-xs" data-toggle="modal" data-target="#modal_backdrop" data-width="75%" action='.$action.'><b><i class="fa fa-file-pdf-o"></i></b> Cetak Report </button>';
                $value['rowNum'] = $no;
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    } 
    
    public function actionModal()
    {
        $request = Yii::$app->request;
        $no_exportexcel = $request->get('no_exportexcel', null);
        $title = 'Cetak General Report MCU';
        $token = $this->getToken();
        $response = $this->guzzleExec($this->_restMcu, [
            'url' => "lap-hasil-mcu/cetak-corporate",
            'payload' => [
                'query' => [
                    'no_exportexcel' => $no_exportexcel,
                ]
            ],
        ]);
        return $this->renderAjax('_modal', get_defined_vars());
    }

    public function actionDownloadFile()
    {
        $request = Yii::$app->request;
        $no_order = $request->get('no_order', null);
        if(!empty($no_order)) {
            $no_order = DocoHelpers::decrypt($no_order);
        }
        $fileDownloads = 'Hasil Pemeriksaan MCU '.$no_order.'.zip';
        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restMcu->get('lap-hasil-mcu/download-file',
        [
            'query' => [
                'no_order' => $no_order,
            ],
            'save_to' => $path,
        ]);
        $response = json_decode($response->getBody(), true);
        return DocoHelpers::downloadFile($path,true);
    }

    private function getToken()
    {
        return $this->guzzleExec($this->_restMcu, [
            'url' => 'lap-hasil-mcu/get-token'
        ]);
    }
}