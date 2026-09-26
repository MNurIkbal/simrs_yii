<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-02-21 11:47:03
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2019-04-15 14:54:09
 */

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use Doco\master\models\PegawaiRuanganForm;
use app\components\DocoSelect2Trait;

class PegawaiRuanganController extends DocoController
{
	use DocoSelect2Trait;
    protected $_title = "Pegawai ruangan";
    protected $_module = '/master/pegawai-ruangan';
    protected $_restMaster;
    protected $_workspace;
    protected $_ruangan_id;
    protected $_ruangan_nama;
    protected $_request;
    protected $_session;
    protected $_instalasi;
    protected $_backUrl;
    protected $_uid;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_instalasi = Yii::$app->docoVars->workspace("instalasi_id");
        $this->_backUrl = 'pegawai-ruangan/';
        $this->_uid = Yii::$app->docoVars->user('uid');
    }

    public function getDataDetail($id)
    {
        $id = explode("-", $id);
        $pegawai_id = $id[0];
        $ruangan_id = $id[1];

        $response = $this->_restMaster->get('pegawai-ruangan/get-detail-pegawai-ruangan',
        [ 'query' => [
            'pegawai_id' => $pegawai_id,
            'ruangan_id' => $ruangan_id
        ]]);
    }

    public function actionIndex()
    {
        $ruangan = $kelompok_pegawai = [];
        try {
            $title = $this->_title;
            $status_arr = ['1'=> Yii::t('fe','Aktif'), '0'=>Yii::t('fe','Tidak aktif')];
            $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");

            $get_data = $this->_restMaster->get("pegawai-ruangan/get-index-data",[
                "query" => ["user_id" => $this->_uid, "instalasi_id" => $instalasi_id]
            ]);

            $body = json_decode($get_data->getBody(), true);

            $ruangan = $body["response"]["ruangan"];
            $kelompok_pegawai = $body["response"]["kelompok_pegawai"];

            return $this->render('index', get_defined_vars());
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e){
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }

    public function actionDetail($id)
    {
        $title = "Detail pegawai ruangan";
        $request = Yii::$app->request;
        $rawId = $id;
        $id = explode("-", $id);
        $pegawai_id = $id[0];
        $ruangan_id = $id[1];

        $model = new PegawaiRuanganForm;

        $response = $this->_restMaster->get('pegawai-ruangan/get-detail-pegawai-ruangan',
            [ 'query' => [
                'pegawai_id' => $pegawai_id,
                'ruangan_id' => $ruangan_id
            ]]);
        $body = json_decode($response->getBody(), true);
        $instalasi = $body["response"]["instalasi"];
        $kelompokPegawai = $body["response"]["kelompok_pegawai"];
        $pegawai = $body['response']["pegawai_ruangan"];
        $model->attributes = $body['response']["pegawai_ruangan"];
        $model->pegawai_id = $body['response']["pegawai_ruangan"]['pegawai_id'];
        $data_pegawai = isset($body['response']["pegawai_ruangan"]) ? $body['response']["pegawai_ruangan"] : null;

        $pegawai_id = ($data_pegawai) ? $data_pegawai['pegawai_id'] : null;
        $nama_pegawai =  ($data_pegawai) ? $data_pegawai['nomorindukpegawai'].' - '.$data_pegawai['nama_pegawai'] : null;
        if($request->post()){
            $post = $request->post();
            $post['user_id'] = Yii::$app->docoVars->user("id");
            $response = $this->_restMaster->request('POST', 'pegawai-ruangan/update', ['form_params'=>$post]);
            $body = json_decode($response->getBody(), true);
            $return = ['response' => $body['response']];
            return DocoHelpers::response($return);
        }
		return $this->render('detail', get_defined_vars());
    }

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $title = "Tambah Pegawai Ruangan";
        $instalasi = [];
        try {
            $instalasi = $this->_restMaster->get('pegawai-ruangan/get-list-instalasi',[
                'query'=> ['user_id'=> $this->_uid ],
                'form_params'=>[]]
            );
            $instalasi = json_decode($instalasi->getBody(), true);
            $instalasi = ArrayHelper::map($instalasi['response']['instalasi'],'instalasi_id','instalasi_nama');
            $model = new PegawaiRuanganForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);

            if($request->post()){
                $post = $request->post();
                $post['user_id'] = Yii::$app->docoVars->user("id");
                $model->attributes = $post['PegawaiRuanganForm'];
                if ($model->validate()) {
                    try {
                        $response = $this->_restMaster->post('pegawai-ruangan/create', [
                                        'form_params'=>$post
                                    ]);
                        $body = json_decode($response->getBody(), true);
                        $return = ['response'=>$body['response']];
                        return DocoHelpers::response($return);
                    } catch (RequestException $e) {
                        return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                    } catch (\Exception $e) {
                        return DocoHelpers::responseTemplate(500, $e->getMessage());
                    }
                } else {
                    $errors = DocoHelpers::parseError($model->errors, $formName);
                    return DocoHelpers::responseTemplate(422, 'Error', $errors);
                }
            }
        } catch (Exception $e) {
            return DocoHelpers::response($e->getMessage(), 422);
        }

        return $this->render('form', get_defined_vars());
    }

    public function actionListRuanganByInstalasi()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $instalasi_id = isset($post['depdrop_parents'][0]) ? $post['depdrop_parents'][0] : null;

        $RuanganRequest = $this->_restMaster->get('pegawai-ruangan/get-list-ruangan',[
            'query' => [
                'user_id' => $this->_uid,
                'instalasi_id' => $instalasi_id
            ],'form_params' => []
        ]);
        $ruangan = json_decode($RuanganRequest->getBody(), true);
        $ruangan = $ruangan['response']['data'];
        $ddlRuangan = ArrayHelper::map($ruangan, 'ruangan_id','ruangan_nama');

        $out = [];
        foreach($ddlRuangan as $key => $value) {
            $out[] = [
                        'id' => $key,
                        'name' => $value
                    ];
        }

        $selected = null;
        if (count($out) == 1) {
            $selected = $out[0]['id'];
        }
        return DocoHelpers::response(['output'=>$out, 'selected'=>$selected]);
    }

    public function actionGetDataPegawaiRuangan()
    {
    	Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        unset($yiiRestfulParams["order"]);
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restMaster->get('pegawai-ruangan/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            $responData = $body['response']['data'];
            foreach ($responData as $key => $value) {
                $no++;
                $primaryKey = $value['pegawai_id']."-".$value['ruangan_id'];
                $value['primary'] = $primaryKey;
                $value['is_active'] = ($value['is_active']) ? Yii::t('fe','Aktif') : Yii::t('fe','Tidak aktif');
                unset($value['obatalkes_id']);

                $value['rowNum'] = $no;
                $value['pegawai_is_active'] = ($value['pegawai_is_active'] == true) ? Yii::t('fe','Aktif') : Yii::t('fe','Tidak aktif');
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e){
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e) {
            return $e->getMessage();
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return $e->getMessage();
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionGetRuangan()
    {
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $id = $parents[0];
                $data = [];
                $ruangan = $this->_restMaster->get('ruangan?advanced-filter[is_active]=1&advanced-filter[instalasi_id]='.$id,['form_params'=>[]]);
                $ruangan = json_decode($ruangan->getBody(), true);
                $ruangan = $ruangan['response']['data'];
                //$ruangan = ArrayHelper::map($ruangan, 'ruangan_id','ruangan_nama');
                $no = 0;
                foreach ($ruangan as $value) {
                    $data[$no] = ['id'=>$value['ruangan_id'],'name'=>$value['ruangan_nama']];
                    $no++;
                }
                $result = ['output'=>$data, 'selected'=>''];
                return DocoHelpers::response($result);
            }
        }
    }

    //get data pegawai buat depdrop
    public function actionGetPegawai()
    {
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $id = $parents[0];
                $data = [];
                $ruangan = $this->_restMaster->get('pegawai/get-pegawai?kelompokpegawai_id='.$id,['form_params'=>[]]);
                $ruangan = json_decode($ruangan->getBody(), true);
                $ruangan = $ruangan['response'];
                //$ruangan = ArrayHelper::map($ruangan, 'ruangan_id','ruangan_nama');
                $no = 0;
                foreach ($ruangan as $value) {
                    $data[$no] = ['id'=>$value['pegawai_id'],'name'=>$value['nomorindukpegawai'].' - '.$value['nama_pegawai']];
                    $no++;
                }
                $result = ['output'=>$data, 'selected'=>''];
                return DocoHelpers::response($result);
            }
        }else{
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
                $response = $this->_restMaster->get('pegawai/index-view?sel_wrap=.filter-form'.http_build_query($yiiRestfulParams));
                $body = json_decode($response->getBody(), True);

                $no = $request->get('start',1);
                foreach ($body['response']['data'] as $key => $value) {
                    $no++;
                    $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                        'class' => 'btn btn-success btn-xs data-check',
                        'data-sel_wrap' =>'.filter-form',
                        'data-key' => $value['pegawai_id'],
                        'data-label' => $value['nama_pegawai'],
                        'title' => \Yii::t('fe', 'Klik'),
                    ]);
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
    }

    public function actionGetKelompokpegawai()
    {
        if(isset($_GET['q']) && !empty($_GET['q'])){
            $response = $this->_restMaster->request('POST', 'pegawai-ruangan/data-kelompokpegawai',[
                            'form_params'=>['term'=>$_GET['q']],
                        ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = ['id'=>$value['kelompokpegawai_id'],'text'=>$value['kelompokpegawai_nama']];
            }
            // Return
            return Json::encode(['results' => $data]);
        }
    }

    //get data pegawai buat data di modal
    public function actionGetDataPegawai()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){

            $response = $this->_restMaster->request('POST', 'pegawai-ruangan/data-pegawai',[
                            'form_params'=>['term'=>$_GET['q']['term']],
                        ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = ['id'=>$value['nama_pegawai'],'text'=>$value['nomorindukpegawai'] . ' - ' . $value['nama_pegawai']];
            }
            $total = count($body['response']);
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
            return DocoHelpers::response($return);
        }
    }

    public function actionModal(){
        $type = $_GET['tipe'];
        if($type == 'pegawai-nama'){
            $view = 'modal-pegawai';
        }else{
            $view = '';
        }
        return $this->renderAjax($view, get_defined_vars());
    }

    public function actionDelete($id)
    {
        try {
            $response = $this->_restMaster->request('DELETE', 'pegawai-ruangan/delete',[
                            'query' => ['id' => $id ]
                        ]);
            $response = json_decode($response->getBody(),true);
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        unset($yiiRestfulParams["order"]);
        try {
            $path = Yii::getAlias("@download") . "/pegawai-ruangan.xlsx";
            $response = $this->_restMaster->get('pegawai-ruangan/export-excel?' .http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            $url = $body['response'];

            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e){
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        unset($yiiRestfulParams["order"]);
        $path = Yii::getAlias("@download") . "/pegawai-ruangan.pdf";
        try {
            $response = $this->_restMaster->get('pegawai-ruangan/export-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    /*
    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/pegawai-ruangan-".Yii::$app->docoVars->workspace("ruangan_name").".pdf";
        try {
            $response = $this->_restMaster->get($this->_backUrl.'export-pdf?instalasi_id='.$this->_instalasi.'&'.http_build_query($yiiRestfulParams),[
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
    }*/

    private function getRuanganNama($id)
    {
        try {
            $response = $this->_restMaster->request('GET', 'pegawai-ruangan/get-ruangan-nama?id='.$id);
            $result = json_decode($response->getBody(), true);

            return $result;

        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
       } catch (RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
       }
    }
}