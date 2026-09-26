<?php
// Author : Ramdhan Nurrachman
// Last Edited : Johndoe

namespace Doco\kasir\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\modules\kasir\models\MutasiBarangForm;

class InfPemesananBarangController extends DocoController
{
    protected $_title = "Informasi Pemesanan Barang";
    protected $_module = '/kasir/inf-pemesanan-barang/';
    protected $_restKasir; 
    protected $_restMaster;
    protected $_ruangan_id;
    protected $_nama_ruangan;
    protected $_backUrl = 'inf-pemesanan-barang/';

    public function init()
    {
        parent::init();
        $this->_restKasir = Yii::$app->docoRest->kasir; $this->_restMaster = Yii::$app->docoRest->master;
        $this->_ruangan_id = Yii::$app->docoVars->workspace('ruangan_id') ? Yii::$app->docoVars->workspace('ruangan_id') : 1;
        $this->_nama_ruangan = Yii::$app->docoVars->workspace('ruangan_name') ? Yii::$app->docoVars->workspace('ruangan_name') : '';
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
        $title = $this->_nama_ruangan .' :: '. $this->_title;
        $api = $this->_restKasir->get('inf-pemesanan-barang/generate-api');
        $api = json_decode($api->getBody(), True);
    
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
            $response = $this->_restKasir->get('inf-pemesanan-barang/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['primary'] = $value['pesanbarang_id'];
                $value['tgl_pesanbarang'] = date("j M Y", strtotime($value['tgl_pesanbarang']));

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

    public function actionCancel($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restKasir->get('inf-pemesanan-barang/cancel?id='.$id);
            $response = json_decode($response->getBody(),true);
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionDetail($id)
    {
        $title = 'Pemesanan Barang Ruangan '.$this->_nama_ruangan;
        $request = Yii::$app->request;
        $get = $request->get();
        $response = $this->_restKasir->request('GET', 'inf-pemesanan-barang/view', [
                        'query' => ['id' => $id ]
                    ]);
        $body = json_decode($response->getBody(),true);
        $response = $body['response']; 
        
        return $this->render('view', get_defined_vars());
        try {

        } catch (RequestException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function actionGetDataDetail($id)
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
            $response = $this->_restKasir->get('inf-pemesanan-barang/detail?id=' . $id . '&' . http_build_query($yiiRestfulParams), ['form_params' => []]);

            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['primary'] = $value['pesanbarang_id'];
                $value['qty_konversi'] = $this->actionGetKonversi($value['satuanbesar_id'], $value['satuankecil_id'], 
                    $value['qty_pesan']);
                $value['rowNum'] = $no;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['count'];
            $result['recordsFiltered'] = $body['response']['count'];
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
        $response = $this->_restKasir->get('inf-pemesanan-barang/get-konversi?satuanbesar_id=' . $satuan_besar . '&satuankecil_id=' . $satuan_kecil . '&qty_pesan=' . $qty_pesan, ['form_params' => []]);

        $body = json_decode($response->getBody(), True);

        return $body['response'];
    }

    public function actionGetRuangan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restKasir->get('inf-pemesanan-barang/get-ruangan?instalasi_id='.$parent_label);
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


    public function actionGetNopemesanan()
    {
        if(isset($_GET['q']) && !empty($_GET['q'])){
            $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
            $response = $this->_restKasir->request('POST', $this->_backUrl.'get-nopemesanan',[
                            'form_params'=> [
                                'term' => $_GET['q'],
                                'ruangan_id' => $ruangan_id
                            ],
                        ]);            
            $body = json_decode($response->getBody(), true);   
            $data = [];                            
            foreach ($body['response'] as $key => $value) {
                $data[] = ['id'=>$value['no_pemesanan'],'text'=>$value['no_pemesanan']];
            }          
            $total = count($body['response']);      
            $return = ['results'=>$data,'total_count'=>$total,'incomplete_results'=>false];                
            return DocoHelpers::response($return);
        }
        
    }


    public function actionMutasi($id)
    {
        try {
            $title = $this->_title ;
            $url_search_popup = $this->_module.'popup';
            $model = new MutasiBarangForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
             
            $pemesanan = $this->_restKasir->request('GET', 'inf-pemesanan-barang/view', [
                            'query' => ['id' => $id ]
                        ]);
            $body = json_decode($pemesanan->getBody(), True);
            $response = $body['response']; 
            $request = Yii::$app->request;
            if ($request->post()) {
                $post = $request->post('MutasiBarangForm');
                $model->attributes = $post;
                if ($model->validate()) {
                    $response = $this->_restKasir->post('inf-pemesanan-barang/create', [
                        'form_params' => $model,
                    ]);

                    $response = json_decode($response->getBody(),true);
                    // return DocoHelpers::response($response);
                    return DocoHelpers::response($response,false,$formName);
                } else {
                    return DocoHelpers::response($model->errors,422,$formName);
                }
            } else {
                return $this->render('mutasi',get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionPopup()
    {
        $title = Yii::t('fe', 'Pegawai');
        $ruangan_id = $this->_ruangan_id;
        return $this->renderPartial('search_pegawai', get_defined_vars());
    }

    public function actionGetDataPegawai()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $post = $request->post();
        $response = $this->_restKasir->request('POST', 'pegawai/index',[
            'form_params' => $post
        ]);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',0);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pegawai_id']);
                $value['aksi'] = Html::a('<i class="fa fa-lg fa-check-square-o"></i>', ['#'], [
                    'class' => 'btn btn-success btn-xs select-pegawai',
                    'data-pegawai' => $value['pegawai_id'],
                    'data-tooltip' => 'tooltip',
                    'title' => Yii::t('fe', 'Pilih'),
                ]);

                $value['rowNum'] = $no;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['draw'] = $request->post('draw');
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
}
