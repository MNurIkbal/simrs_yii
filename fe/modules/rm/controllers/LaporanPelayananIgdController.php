<?php 

namespace Doco\rm\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use kartik\mpdf\Pdf;

class LaporanPelayananIgdController extends DocoController
{
	protected $_title;
    protected $_restRm;
    protected $_module = '/rm/laporan-pelayanan-igd/';

    public function init()
    {
        parent::init();

        $this->_title = Yii::t('fe', 'Laporan Pelayanan Rawat Darurat');
        $this->_restRm = Yii::$app->docoRest->rm;
    }

    public function actionIndex()
    {
    	$title = $this->_title;

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
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restRm->get('lap-pelayanan-igd/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            // dump($body);die;
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                if($value['jenis_pelayanan'] == 'TOTAL') {
                    $value['jenis_pelayanan'] = '<b>'.$value['jenis_pelayanan'].'</b>';
                }
                
                $data[$key] = $value;
            }

            $result['data'] = $data;
            //$result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            //$result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $path = Yii::getAlias("@download") . "/lap-pelayanan-igd.xlsx";
            $response = $this->_restRm->get('lap-pelayanan-igd/export-excel',[
                'query' => $yiiRestfulParams,
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path, true);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/lap-pelayanan-igd.pdf";
        try {
            $response = $this->_restRm->get('lap-pelayanan-igd/export-pdf?',[
                'query' => $yiiRestfulParams,
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            // dump($body);die;
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

}