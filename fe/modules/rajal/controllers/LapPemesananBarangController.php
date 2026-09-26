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

class LapPemesananBarangController extends DocoController
{
    protected $_title = "Laporan Pemesanan Barang";
    protected $_module = '/rajal';
    protected $_controller = '/rajal/lap-pemesanan-barang';
    protected $_restGudang;
    protected $_restMaster;
    protected $_ruangan_id;
    protected $_nama_ruangan;

    public function init()
    {
        parent::init();
        $this->_restGudang = Yii::$app->docoRest->gudang;
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_ruangan_id = Yii::$app->docoVars->workspace('ruangan_id') ? Yii::$app->docoVars->workspace('ruangan_id') : 1;
        $this->_nama_ruangan = Yii::$app->docoVars->workspace('ruangan_name') ? Yii::$app->docoVars->workspace('ruangan_name') : '';
    }

    public function actionIndex()
    {
        
        $title = $this->_nama_ruangan .' :: '. $this->_title;
        $api = $this->_restGudang->get('lap-pemesanan-barang/generate-api');
        $api = json_decode($api->getBody(), True);
        
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        if(!isset($yiiRestfulParams['advanced-filter']['ruanganpemesan_id'])){
            $yiiRestfulParams['advanced-filter']['ruanganpemesan_id'] = Yii::$app->docoVars->workspace("ruangan_id");
        }  
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restGudang->get('lap-pemesanan-barang/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            // return DocoHelpers::response($body['response']);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pesanbarang_id']);
                unset($value['pesanbarang_id']);

                $value['qty_konversi'] = $this->actionGetKonversi($value['satuanbesar_id'], $value['satuankecil_id'], 
                    $value['qty_pesan']);
                
                $value['tgl_pesanbarang'] = date("j M Y", strtotime($value['tgl_pesanbarang']));
                $value['rowNum'] = $no; $value['primary'] = $primaryKey;
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

    public function actionExportExcel()
    {
        $ruangan_id = $this->_ruangan_id;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $result = [];
        $url = "";
        try {
            $response = $this->_restGudang->get('lap-pemesanan-barang/export-excel?ruanganpemesan_id=' . $ruangan_id . '&' . http_build_query($yiiRestfulParams), ['form_params' => []]);
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

    public function actionGetRuangan($assign_id="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restGudang->get('lap-pemesanan-barang/get-ruangan?instalasi_id='.$parent_label);
            $body = json_decode($response->getBody(), True);

            foreach ($body['response'] as $value) 
                $result['output'][] = [
                    'id' => $value['ruangan_id'], 
                    'name' => $value['ruangan_nama']
                ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetKonversi($satuan_besar, $satuan_kecil, $qty_pesan)
    {
        $response = $this->_restGudang->get('lap-pemesanan-barang/get-konversi?satuanbesar_id=' . $satuan_besar . '&satuankecil_id=' . $satuan_kecil . '&qty_pesan=' . $qty_pesan, ['form_params' => []]);

        $body = json_decode($response->getBody(), True);

        return $body['response'];
    }

}