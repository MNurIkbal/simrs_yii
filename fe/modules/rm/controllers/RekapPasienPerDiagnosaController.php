<?php

namespace Doco\rm\controllers;
use Yii;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DHtml;

class RekapPasienPerDiagnosaController extends DocoController
{
    protected $_title;
    protected $_restRm;
    protected $_module;

    public function init()
    {
        parent::init();
        $this->_restRm = Yii::$app->docoRest->rm;
        $this->_module = 'rekap-pasien-per-diagnosa';
        $this->_title = 'Rekap Pasien per Diagnosa';
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
        $title = DHtml::getTitleMenu();
		$title = !empty($title) ? $title : $this->_title;
        $_rekapTitle = 'Rekap';
        $_detailTitle = 'Detail';
        $filters = $this->guzzleExec($this->_restRm, [
            'url' => $this->_module.'/index',
            'payload' => [
                'query' =>[]
            ]
        ]);
        $instalasi = ArrayHelper::getValue($filters,'instalasi');
        $ruangan = ArrayHelper::getValue($filters,'ruangan');
        return $this->render('index',compact('_rekapTitle', '_detailTitle', 'instalasi', 'ruangan', 'title'));
    }

    public function actionGetDataRekap()
    {   
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        $draw = $request->get('draw', 1);
        if($draw == 1) {
            $yiiRestfulParams['advanced-filter'] = [];
        }
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->guzzleExec($this->_restRm, [
                'url' => $this->_module.'/get-data-rekap',
                'payload' => [
                    'query' => $yiiRestfulParams
                ]
            ]);
            $no = 0;
            foreach($response['data'] as $key => $val) {
                $no++;
                $val['rowNum'] = $no;
                $data[] = $val;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $response['_meta']['totalCount'];
            $result['recordsFiltered'] = $response['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDataDetail()
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
        $result['recordsTotal'] = 0;
        try {
            $response = $this->guzzleExec($this->_restRm, [
                'url' => $this->_module.'/get-data-detail',
                'payload' => [
                    'query' => $yiiRestfulParams
                ]
            ]);
            $no = 0;
            foreach($response['data'] as $key => $val) {
                $no++;
                $val['rowNum'] = $no;
                $data[] = $val;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $response['_meta']['totalCount'];
            $result['recordsFiltered'] = $response['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionShowPopupExcel()
    {
        $title = 'Cetak Laporan Rekap Pasien per Diagnosa';
        $request = Yii::$app->request;
        $type = $request->get('type', 'rekap');
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['randString'] = $randString;
        $yiiRestfulParams['type'] = $type;
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modalExcel', compact('title', 'randString'));
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $randString = $request->get('randString');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($this->_restRm, [
            'url' => $this->_module."/export-excel",
            'payload' => [
                'query' => Yii::$app->session->getFlash($randString)
            ],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Laporan Rekap Pasien per Diagnosa.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restRm->get($this->_module.'/download-file-excel', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }
}