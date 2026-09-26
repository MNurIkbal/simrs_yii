<?php 
// Author : Budi

namespace Doco\rm\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use kartik\mpdf\Pdf;

class LapSepuluhBesarPenyakitController extends DocoController
{
	protected $_title;
    protected $_restRm;
    protected $_module = '/rm/lap-sepuluh-besar-penyakit/';
    protected $_controllerService = 'lap-sepuluh-besar-penyakit/';

    public function init()
    {
        parent::init();

        $this->_title = Yii::t('fe', 'Laporan 10 Besar Penyakit');
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

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restRm->get($this->_controllerService.'index?'.http_build_query($yiiRestfulParams), [
            	'form_params' => []
            ]);

            $body = json_decode($response->getBody(), True);
            
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;

                $value['jumlah'] = 1;
                $value['rowNum'] = $no;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    private function downloadFile($filename)
    {
        $file = basename($filename);

        $fp = fopen($file, 'w');
         
        $ch = curl_init($filename);
        curl_setopt($ch, CURLOPT_FILE, $fp);
         
        $data = curl_exec($ch);
         
        curl_close($ch);
        fclose($fp);
         
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$file.'".xls');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        ob_clean();
        flush();
        readfile($file);
        exit;

    }
    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $request = Yii::$app->request;
        
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        $response = $this->_restRm->get($this->_controllerService.'index?'.http_build_query($yiiRestfulParams), [
                'form_params' => []]);

        $body = json_decode($response->getBody(), True);

        $api = $this->_restRm->post($this->_controllerService.'export-excel?', [
            'form_params' => [
                'body' => json_encode($body['response']['data']) 
            ]
        ]);

        $response = json_decode($api->getBody(), True);
        
        $this->downloadFile($response['response']);
    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $request = Yii::$app->request;
        
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        $response = $this->_restRm->get($this->_controllerService.'index?'.http_build_query($yiiRestfulParams), [
                'form_params' => []]);

        $body = json_decode($response->getBody(), True);

        $content = $this->renderPartial('_pdf', get_defined_vars());

        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE, 
            'format' => Pdf::FORMAT_A4, 
            'orientation' => Pdf::ORIENT_PORTRAIT, 
            'destination' => Pdf::DEST_BROWSER, 
            'content' => $content,  
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '.kv-heading-1{font-size:18px}', 
            'options' => ['title' => 'Krajee Report Title'],
            'methods' => [ 
                'SetHeader' => ['Laporan 10 Besar Penyakit'], 
                'SetFooter' => ['{PAGENO}'],
            ]
        ]);

        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/pdf');

        return $pdf->render();
    }
}