<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-03-06 15:54:22
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-11-12 13:32:45
 */

namespace Doco\apotek\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;

class InformasiPenjualanResepKaryawanController extends DocoController
{
    protected $_title = "Informasi Penjualan Resep Karyawan";
    protected $_module = '/apotek/informasi-penjualan-resep-karyawan';
    protected $_restApotek;

    public function init()
    {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek;
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        $title  = $this->_title;
        $carabayar = \Yii::$app->cache->get('carabayar');
        if(!$carabayar){
            $response = $this->_restMaster->get('cara-bayar/index?advanced-filter[is_active]=1');
            $body = json_decode($response->getBody(), True);
            $carabayar_data = ArrayHelper::map($body['response']['data'],'carabayar_nama','carabayar_nama');
            \Yii::$app->cache->set('carabayar', $carabayar_data, 60);
            $carabayar = $carabayar_data;
        }

        return $this->render('index', get_defined_vars());
    }
    public function actionView($id)
    {
        $title = 'Detail Informasi Penjualan Resep Karyawan';
        $penjualanresep_id = DocoHelpers::decrypt($id);
        $response = $this->_restApotek->get('inf-penjualan-resep-karyawan/data-detail', ['query' => ['id'=>$penjualanresep_id]]);
        $body = json_decode($response->getBody(), true);
        $data = $body['response'];
        $iter = $data['iter'];
        $disableButton = false;
        if ($iter < 1) {
            $disableButton = true;
        }

        return $this->render('detail',get_defined_vars());
    }
    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        try{
            $response = $this->_restApotek->post('inf-penjualan-resep-karyawan/delete-resep', ['form_params'=>['id'=>$id]]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response($body['response']);
        } catch(\Exception $e){
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch(RequestException $e){
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }

    }
    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['ruangan_id'] = Yii::$app->docoVars->workspace("ruangan_id");

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restApotek->get('inf-penjualan-resep-karyawan/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['penjualanresep_id']);
                unset($value['penjualanresep_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['tglpenjualan'] = date('d-M-Y', strtotime($value['tglpenjualan']));
                $value['totaltagihan'] = DocoHelpers::formatNumber($value['totalhargajual']+$value['biayaadministrasi']+$value['totaltarifservice']+$value['jasadokterresep']+$value['pembulatanharga']);
                unset($value['totalhargajual']);
                unset($value['biayaadministrasi']);
                unset($value['jasadokterresep']);
                unset($value['pembulatanharga']);
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
    public function actionGetPenjamin()
    {       
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restMaster->get('penjamin?advanced-filter[carabayar_m.carabayar_nama]='.$parent_label);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value) 
                $result['output'][] = [
                    'id' => $value['penjamin_nama'], 
                    'name' => $value['penjamin_nama']
                ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return$result;
        }
    }
    public function actionGetNoresep()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $response = $this->_restApotek->request('POST', 'inf-penjualan-resep-karyawan/data-resep',[
                            'form_params'=>['term'=>$_GET['q']['term']],
                        ]);
            $body = json_decode($response->getBody(), true);   
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = ['id'=>$value['noresep'],'text'=>$value['noresep']];
            }
            $total = count($body['response']);
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
            return DocoHelpers::response($return);
        }
    }
    public function actionGetKaryawan()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $response = $this->_restApotek->request('POST', 'inf-penjualan-resep-karyawan/data-karyawan',[
                            'form_params'=>['term'=>$_GET['q']['term']],
                        ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = ['id'=>$value['nama_karyawan'],'text'=>$value['nama_karyawan']];
            }
            $total = count($body['response']);
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
            return DocoHelpers::response($return);
        }
    }
    public function actionGetDataObat()
    {
        $id = DocoHelpers::decrypt($_GET['id']);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['penjualanresep_id'] = $id;
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restApotek->get('inf-penjualan-resep-karyawan/data-obat?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            \Yii::$app->cache->set('dataobat_'.$id, $body['response']['data']);
            $cache = $body['response']['data'];
            $no = $request->get('start',1);
            $totalharga = 0;
            foreach ($cache as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['obatalkes_id']);
                unset($value['obatalkes_id']);
                $jenisRacikan = ($value['racikan_id'] == 1) ? Yii::t('fe', 'Racikan') : Yii::t('fe', 'Non Racikan');
                $value['jenis_racikan'] = $jenisRacikan;
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['signa_oa'] = !empty($value['signa_oa']) ? $value['signa_oa'] : '-';
                $value['rke'] = !empty($value['rke']) ? $value['rke'] : '-';
                $total = $value['qty_oa']*($value['hargasatuan_oa']);
                $value['totaltagihan'] = DocoHelpers::formatNumber($total);
                $value['hargajual_oa'] = DocoHelpers::formatNumber($value['hargasatuan_oa']);
                $totalharga += $total;
                $data[$key] = $value;
            }
            $result['totalobat'] = DocoHelpers::formatNumber($totalharga);
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
    public function actionCopyResep($id,$noresep,$pendaftaranid)
    {
        try {
            $id = DocoHelpers::decrypt($id);
            $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
            $dataObat = \Yii::$app->cache->get('dataobat_'.$id);
            $postData = ['penjualanresep_id'=>$id,'pendaftaran_id'=>$pendaftaranid,'noresep'=>$noresep, 'dataObat'=>$dataObat];
            $response = $this->_restApotek->request('POST', 'inf-penjualan-resep-karyawan/copy-resep', [
                'form_params' => $postData
            ]);
            $body = json_decode($response->getBody(), true);
            /*if($body['response'] == false){
                $result = ['response'=>[
                            'return' => 'disableButton()',
                        ]
                    ];
            }else{
                $result = $body['response'];
            }*/
            return DocoHelpers::response($body);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }
    public function actionPrintDetail($id,$noresep){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $path = Yii::getAlias("@download") . "/rincian-tagihan-penjualan-resep-".$noresep.".pdf";
        try {
            $response = $this->_restApotek->get('inf-penjualan-resep-karyawan/print-detail',[
                'save_to' => $path,
                'query'=>[
                    'id'=>$id
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

}