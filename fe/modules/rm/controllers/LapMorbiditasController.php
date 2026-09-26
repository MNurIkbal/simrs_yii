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

class LapMorbiditasController extends DocoController
{
	protected $_title;
    protected $_restRm;
    protected $_module = '/rm/lap-morbiditas/';
    protected $_controllerService = 'lap-morbiditas/';

    public function init()
    {
        parent::init();

        $this->_title = Yii::t('fe', 'Laporan Morbiditas');
        $this->_restRm = Yii::$app->docoRest->rm;
    }

    public function actionIndex()
    {
    	$title = $this->_title;
        $api = $this->_restRm->get($this->_controllerService.'generate-api');
        $api = json_decode($api->getBody(), True);
        $module = $this->_module;
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $limit = !empty($yiiRestfulParams["per-page"]) ? $yiiRestfulParams["per-page"] : 10;
        

        $draw = $request->get('draw', 1);
        $no = $request->get('start', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restRm->get($this->_controllerService.'data-laporan?'.http_build_query($yiiRestfulParams), [
                'form_params' => []]);
            $body = json_decode($response->getBody(), true);

            $result['data'] = $body['response'];
            $result['recordsTotal'] = $limit;
            $result['recordsFiltered'] = $limit;
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
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") .'/'.$this->_title.'.xlsx';

            $restRm = $this->_restRm->get($this->_controllerService.'export-excel?'.http_build_query($yiiRestfulParams), [
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path, true);
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    public function actionListPenjamin() {
        $request = Yii::$app->request;
        $post = $request->post();
        $carabayar_id = $post['depdrop_parents'][0];

        $penjaminRequest = $this->_restRm->get('allow/list-penjamin?carabayar_id='.$carabayar_id);
        $body = json_decode($penjaminRequest->getBody(),TRUE);
        $responses = $body['response'];

        $out = [];
        foreach($responses as $key => $response) {
            $out[] = [
                'id' => $key,
                'name' => $response
            ];
        }

        return json_encode(['output'=>$out, 'selected'=>'']);
    }
}