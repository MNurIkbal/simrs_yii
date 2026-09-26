<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-03-05 10:26:16
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2019-04-01 19:52:34
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

class InformasiPenjualanResepBebasController extends DocoController
{
    protected $_title = "Informasi Penjualan Resep Bebas";
    protected $_module = '/apotek/informasi-penjualan-resep-bebas';
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
        $title = 'Detail Informasi Penjualan Resep Bebas';
        $penjualanresep_id = DocoHelpers::decrypt($id);
        try{
            $response = $this->_restApotek->get('inf-penjualan-resep-bebas/data-detail', ['query' => ['id'=>$penjualanresep_id]]);
            $body = json_decode($response->getBody(), true);
            $data = $body['response'];
            $iter = $data['iter'];
            $disableButton = false;
            if ($iter < 1) {
                $disableButton = true;
            }

            return $this->render('detail',get_defined_vars());

        } catch(\Exception $e){

        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        try{
            $response = $this->_restApotek->post('inf-penjualan-resep-bebas/delete-resep', ['form_params'=>['id'=>$id]]);
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
            $response = $this->_restApotek->get('inf-penjualan-resep-bebas/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['penjualanresep_id']);
                unset($value['penjualanresep_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['nama_pembeli'] = !empty( $value['nama_pembeli'] ) ? $value['nama_pembeli'] : '' ;
                $value['tglpenjualan'] = date('d-M-Y', strtotime($value['tglpenjualan']));
                $value['totaltagihan'] = number_format($value['totalhargajual']+$value['biayaadministrasi']+$value['totaltarifservice']+$value['jasadokterresep']+$value['pembulatanharga'], 0, ',','.');
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
            $response = $this->_restApotek->request('POST', 'inf-penjualan-resep-bebas/data-resep',[
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
            $response = $this->_restApotek->get('inf-penjualan-resep-bebas/data-obat?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            \Yii::$app->cache->set('dataobat_'.$id, $body['response']['data']);
            $data_obat = $body['response']['data'];
            $no = $request->get('start',1);
            $totalharga = 0;
            foreach ($data_obat as $key => $value) {
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
                $value['hargasatuan_oa'] = DocoHelpers::formatNumber($value['hargasatuan_oa']);

                $totalharga += $total;
                $data[$key] = $value;
            }
            $biaya_admin = $value["biayaadministrasi"];
            $jasa_racik = $value["totaltarifservice"];
            $pembulatan = $value["pembulatanharga"];
            $total_tagihan = array_sum([$biaya_admin,$jasa_racik,$pembulatan,$totalharga]);

            $result["biaya_admin"] = DocoHelpers::formatNumber($biaya_admin);
            $result["jasa_racik"] = DocoHelpers::formatNumber($jasa_racik);
            $result["pembulatan"] = DocoHelpers::formatNumber($pembulatan);
            $result['totalobat'] = DocoHelpers::formatNumber($totalharga);
            $result["total_tagihan"] = DocoHelpers::formatNumber(floor($total_tagihan));
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = 'x-'.$e->getMessage();
            return $result;
        }
    }

    /**
     * @Author: Rizqi Fitrianto
     * @Date:   2018-03-13
     * @Add by:   Rizqi Fitrianto
     * @todo: action for copy resep, params needed
     * penjualanresep_id, noresep, nopendaftaran
     */
    public function actionCopyResep($id,$noresep,$pendaftaranid)
    {
        try {
            $id = DocoHelpers::decrypt($id);
            $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
            $dataObat = \Yii::$app->cache->get('dataobat_'.$id);
            $postData = ['penjualanresep_id'=>$id,'pendaftaran_id'=>$pendaftaranid,'noresep'=>$noresep, 'dataObat'=>$dataObat];
            $response = $this->_restApotek->request('POST', 'inf-penjualan-resep-bebas/copy-resep', [
                'form_params' => $postData
            ]);
            $body = json_decode($response->getBody(), true);
            /*if($body['metadata']['status'] == 422){
                $result = ['response'=>[
                            'return' => 'disableButton()',
                        ]
                    ];
            }else{
                $result = $body;
            }*/
            return DocoHelpers::response($body);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionPrintResep($id, $noresep)
    {
        $id = DocoHelpers::decrypt($id);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/cetak-resep-".$noresep.".pdf";
        try {
            $response = $this->_restApotek->get('inf-penjualan-resep-bebas/print-resep',[
                'save_to' => $path,
                'query' => [
                        'id'=>$id,
                        'noresep'=>$noresep
                    ],
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
