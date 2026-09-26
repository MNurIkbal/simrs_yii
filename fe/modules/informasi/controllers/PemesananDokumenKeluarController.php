<?php
// Author : Ardi Pratama

namespace Doco\informasi\controllers;

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

class PemesananDokumenKeluarController extends DocoController
{
    protected $_title = "Informasi :: Pemesanan Dokumen Rekam Medik";
    protected $_module = 'informasi/pemesanan-dokumen-keluar/';
    protected $_restInformasi;

    public function init()
    {
        parent::init();
        $this->_restInformasi = Yii::$app->docoRest->informasi;
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
        $listRequest = ['data_instalasi'=>'actionListInstalasi', 'data_statuspesan'=>'actionListStatusPesan'];
        $request = $this->_restInformasi->post('allow/loop-aksi', ['form_params'=>['param'=>$listRequest]]);
        $body = json_decode($request->getBody(), true);
        $list_instalasi = ArrayHelper::map($body['response']['data_instalasi']['data'],'instalasi_id','instalasi_nama');
        $list_status = ArrayHelper::map($body['response']['data_statuspesan'],'statuspesan_nama','statuspesan_nama');

        return $this->render('index',get_defined_vars());
    }

    public function actionGetData()
    {
        try{
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $yiiRestfulParams['advanced-filter']['ruanganpemesan_id'] = Yii::$app->docoVars->workspace("ruangan_id");
            $draw = $request->get('draw', 1);
            $data = [];

            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;
            $result['recordsTotal'] = 0;
            $response = $this->_restInformasi->get('pemesanan-dokumen-keluar/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pesandokrm_id']);
                unset($value['pesandokrm_id']);

                $value['primary'] = $primaryKey;
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

    public function actionDpdListRuangan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $instalasi_id = $post['depdrop_parents'][0];
        if ($instalasi_id) {
            $ruanganRequest = $this->_restInformasi->get('allow/list-ruangan?instalasi_id='.$instalasi_id);
            $body = json_decode($ruanganRequest->getBody(),TRUE);
            $responses = $body['response']['data'];
            $responses = ArrayHelper::map($responses, 'ruangan_id', 'ruangan_nama');
        } else {
            $responses = [];
        }

        $out = [];
        foreach($responses as $key => $response) {
            $out[] = [
                'id' => $key,
                'name' => $response
            ];
        }

        echo json_encode(['output'=>$out, 'selected'=>'']);
        return;
    }

    public function actionGetDataNopesan()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $response = $this->_restInformasi->request('POST', 'pemesanan-dokumen-keluar/data-nopesan',[
                            'form_params'=>['term'=>$_GET['q']['term']],
                        ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                //$id = DocoHelpers::encrypt($value['mutasiobatruangan_id']);
                $data[] = [
                    'id'=>$value['no_pesandokrm'],
                    'text'=>$value['no_pesandokrm'],
                    'instalasi'=>$value['instalasi_tujuan_id'],
                    'ruangan'=>$value['ruangantujuan_id'],
                ];
            }
            $total = count($body['response']);
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
            return DocoHelpers::response($return);
        }
    }

    public function actionDetail($id)
    {
        try{
            $decryptId = DocoHelpers::decrypt($id);
            $res = $this->_restInformasi->get('pemesanan-dokumen-keluar/info-pemesanan',['query'=>['id'=>$decryptId]]);
            $res = json_decode($res->getBody(),TRUE);
            $info_pemesanan = $res['response']['data'];

            return $this->render('detail',get_defined_vars());
        } catch (\Exception $e){
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        } catch (RequestException $e){
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionGetDataDetail($id)
    {
        try{
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $decryptId = DocoHelpers::decrypt($id);
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $draw = $request->get('draw', 1);
            $data = [];

            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;
            $result['recordsTotal'] = 0;
            $response = $this->_restInformasi
                            ->get('pemesanan-dokumen-keluar/detail?'.http_build_query($yiiRestfulParams), 
                                [
                                    'query' => ['id'=>$decryptId],
                                    'form_params' => []
                                ]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pesandokrmdetail_id']);
                unset($value['pesandokrmdetail_id']);

                $value['primary'] = $primaryKey;
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

    public function actionPrintDetail($id)
    {
        $request = Yii::$app->request;
        $decryptId = DocoHelpers::decrypt($id);
        $path = Yii::getAlias("@download") . "/informasi-pemesanan-dokumen-keluar.pdf";
        try {
            $response = $this->_restInformasi
                        ->get('pemesanan-dokumen-keluar/print-detail-pdf?id='.$decryptId,
                            [
                                'save_to' => $path
                            ]
                        );
            return DocoHelpers::downloadPdf($response,$path);
        } catch (RequestException $e) { 
            // var_dump('expression');exit;
        // var_dump(json_decode($e->getResponse()->getBody()));exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            // var_dump($e);exit;
            // var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionDelete($id){
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restInformasi->get('pemesanan-dokumen-keluar/delete-pesanan', ['query'=>['id'=>$id]]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response($body['response']);
        } catch (RequestException $e) { 
            // var_dump('expression');exit;
        // var_dump(json_decode($e->getResponse()->getBody()));exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            // var_dump($e);exit;
            // var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}