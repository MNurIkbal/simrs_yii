<?php

/**
 * @Author: Johndoe
 * @Date:   2018-03-22 10:00:00
 * @Description: 
 */

namespace Doco\rajal\controllers;

use Yii; 
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\modules\rajal\models\InformasiForm;
use app\modules\rajal\models\AnamnesaForm;
use app\modules\rajal\models\BuatJanjiPoliForm;
use app\modules\rajal\models\PendaftaranForm;

class LapStokBarangController extends DocoController
{
    protected $_title = "Laporan Stok Barang";
    protected $_module = '/rajal';
    protected $_controller = '/rajal/lap-stok-barang';
    protected $_restGudang;
    protected $_restRajal;
    protected $_ruangan_id;
    protected $_nama_ruangan;

    public function init()
    {
        parent::init();
        $this->_restGudang = Yii::$app->docoRest->gudang;
        $this->_ruangan_id = Yii::$app->docoVars->workspace('ruangan_id') ? Yii::$app->docoVars->workspace('ruangan_id') : 1;
        $this->_nama_ruangan = Yii::$app->docoVars->workspace('ruangan_name') ? Yii::$app->docoVars->workspace('ruangan_name') : '';


        $this->_restRajal = Yii::$app->docoRest->rajal;
    }

    public function actionIndex()
    {
        
        $title = $this->_nama_ruangan .' :: '. $this->_title;
        $api = $this->_restGudang->get('lap-stok-barang/generate-api');
        $api = json_decode($api->getBody(), True);
        
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        $ruangan_id = $this->_ruangan_id;
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
            $response = $this->_restGudang->get('lap-stok-barang/index?ruangan_id=' . $ruangan_id . '&' . http_build_query($yiiRestfulParams), ['form_params' => []]);

            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            // return DocoHelpers::response($body['response']);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['primary'] = $value['periodestok_id'];
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

    public function actionExportExcel()
    {
        $ruangan_id = $this->_ruangan_id;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $result = [];
        $url = "";
        try {
            $response = $this->_restGudang->get('lap-stok-barang/export-excel?ruangan_id=' . $ruangan_id . '&' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            return $this->downloadFile($body["response"]);
        } catch (RequestException $e) {
            return $e;
        } catch (\Exception $e) {
            return $e;
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
        header('Content-Disposition: attachment; filename="'.$file.'".xlsx');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        ob_clean();
        flush();
        readfile($file);
        exit;
    }


    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/lap-stok-barang.pdf";
        try {
            $response = $this->_restRajal->get('lap-stok-barang/cetak-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true); 

            return DocoHelpers::downloadPdf($response,$path);
        } catch (RequestException $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

}