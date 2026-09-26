<?php 

/**
 * @author Randy Vianda Putra
 * @todo Informasi Stok Opname Barang
 * @copyright 17 April 2018 aweutist
 */

namespace Doco\gudang\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;

class InformasiStokOpnameController extends DocoController
{

    protected $_title = "Stok Opname Barang";
    protected $_module = '/gudang/informasi-kartu-stok';
    protected $_restGudang;

    public function init()
    {
        parent::init();
        $this->_restGudang = Yii::$app->docoRest->gudang;
    }

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs['index'] = ["GET"];
        $verbs['get-data'] = ["GET"];
        $verbs['detail-so'] = ["GET"];
        $verbs['verifikasi'] = ["GET","PUT"];
        return $verbs;
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
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['ruangan_id'] = $ruangan_id;
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restGudang->get('inf-stok-opname/index?' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;                
                $primaryKey = DocoHelpers::encrypt($value['stokopnamebarang_id']);
                unset($value['stokopnamebarang_id']);
                $value['tglstokopname'] = date('d F Y', strtotime($value['tglstokopname']));
                $value['rowNum'] = $no;
                $value['primary'] = $primaryKey;
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

    public function actionGetNoFormulir()
    {
    	if (isset($_GET['q']['term']) && !empty($_GET['q']['term'])) {
            $response = $this->_restGudang->request('POST', 'inf-stok-opname/autocomplete-no-formulir',[
                'form_params' => ['term' => $_GET['q']['term']],
            ]);            
            $body = json_decode($response->getBody(), true);   
            $data = [];                            
            foreach ($body['response'] as $key => $value) {
                $data[] = ['id' => $value['noformulir'], 'text' => $value['noformulir']];
            }          
            $total = count($body['response']);      
            $return = ['result' => $data, 'total_count' => $total, 'incomplete_results' => false];                
            return DocoHelpers::response($return);
        }
    }

    public function actionView($id)
    {
        $so_id = DocoHelpers::decrypt($id);
        $yiiRestfulParams['advanced-filter']['stokopnamebarang_id'] = $so_id;
        $response = $this->_restGudang->get('inf-stok-opname/get-info-so-detail?' . http_build_query($yiiRestfulParams), ['form_params' => []]);
        $body = json_decode($response->getBody(), true);
        $responseHeaderSo = $this->_restGudang->get('inf-stok-opname/get-info-so?id=' . $so_id);
        $bodyHeaderSo = json_decode($responseHeaderSo->getBody(), true);
        $data_header = $bodyHeaderSo['response'];
        $data_detail = $body['response']['data'];
        // echo "<pre>";
        // var_dump($data_detail);die();

        return $this->render('detail', get_defined_vars());
    } 

    // public function actionPrint($id, $no)
    public function actionPrint($id)
    {
        $id = DocoHelpers::decrypt($id);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        
        // $path = Yii::getAlias("@download") . "/cetak-detail-stok-opname-".$no.".pdf";
        $path = Yii::getAlias("@download") . "/cetak-detail-stok-opname.pdf";
        try {
            $response = $this->_restGudang->get('inf-stok-opname/print-detail',[
                'save_to' => $path,
                'query' => [
                        'id'=>$id,
                    ],
            ]);
            $body = json_decode($response->getBody(), true);      
            return DocoHelpers::previewPdf($path);      
            // return DocoHelpers::downloadPdf($response,$path);
        } catch (RequestException $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionViewModal($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restGudang->get('inf-stok-opname/get-detail',
                [
                    'query' => [
                        'id' => $id
                    ]
                ]
            );
            $response = json_decode($response->getBody(),true);
            $data = $response['response']['data'];
            return DocoHelpers::response($data);
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
        } catch (\Exception $e) {
            Yii::info($e->getMessage());
        }

        return $this->render('preview',get_defined_vars());
    }

    public function actionGetDataDetailModal()
    {
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $stokopnamebarang_id = $request->get('primary');
        $yiiRestfulParams['stokopnamebarang_id'] = DocoHelpers::decrypt($stokopnamebarang_id);
        
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restGudang->get('inf-stok-opname/get-data-detail', 
                    [
                        'query' => $yiiRestfulParams
                    ]
            );
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            $cnt = count($body['response']);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['stokopnamebarangdetail_id']);
                unset($value['stokopnamebarangdetail_id']);
                $value['rowNum'] = $no; 
                $value['primary'] = $primaryKey;
                $value['selisih'] = $value['volume_sistem'] - $value['volume_fisik'];
                // $value['qty_besar'] = $value['qty_besar'].' '.$value['satuan_besar'];
                // $value['qty_kecil'] = $value['qty_kecil'].' '.$value['satuan_kecil'];
                // $value['tgl_kadaluarsa'] = !empty($value['tgl_kadaluarsa']) ? date("j M Y", strtotime($value['tgl_kadaluarsa'])) : '';

                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $cnt;
            $result['recordsFiltered'] = $cnt;
            // $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            // $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }
}