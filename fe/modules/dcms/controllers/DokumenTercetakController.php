<?php

namespace Doco\dcms\controllers;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use Doco\dcms\models\DokumenTercetakForm;

class DokumenTercetakController extends DocoController
{
    public $_title = "Dokumen Tercetak";
    protected $_module = 'dcms/dokumen-tercetak';
    protected $_restDcms;

    public function init()
    {
        parent::init();
        $this->_restDcms = Yii::$app->docoRest->dcms;
    }

    public function actionIndex()
    {
        $is_new_report = Yii::$app->report->version == \app\components\SirsReport::REPORT_JSON ? 1 : 0;
        return $this->render('index',get_defined_vars());
    }

    public function actionGetData()
    {
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        $is_new_report = Yii::$app->report->version == \app\components\SirsReport::REPORT_JSON ? 1 : 0;

        try {
            $response = $this->_restDcms->get('dokumen-tercetak/index',
                    [
                        'query' => http_build_query($yiiRestfulParams)
                    ]
            );
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);

            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['docmapping_id']);
                $value['primary'] = $primaryKey;
                unset($value['docmapping_id']);

                $value['rowNum'] = $no;
                $lisrService = explode("-", $value['doc_key']);
                $value['service'] = isset($lisrService[0]) ? $lisrService[0] : '';
                $value['menu'] = isset($lisrService[1]) ? $lisrService[1] : '';
                $value['dokumen'] = isset($lisrService[2]) ? $lisrService[2] : '';
                $clearAction = str_replace("action", "", $value['dokumen']);
                $string = preg_replace("/(?<=\\w)(?=[A-Z])/"," $1", $clearAction);
                $value['dokumen'] = $string;
                $data[$key] = $value;
                $data[$key]['header_nama'] = isset($data[$key]['header']) ? $data[$key]['header']['nama_header'] : '-';
                $data[$key]['footer_nama'] = isset($data[$key]['footer']) ? $data[$key]['footer']['nama_footer'] : '-';
                $data[$key]['kertas_nama'] = isset($data[$key]['kertas']) ? $data[$key]['kertas']['kertas_nama'] : '-';
                if($is_new_report){
                    $data[$key]['viewer'] = 
                        '<a class="btn btn-info btn-labeled btn-xs data-back" target="_blank" href='.Url::toRoute(['/reports/viewer/'.$value['kode_doc']]).'>
                            <b><i class="fa fa-external-link"></i></b>Viewer
                        </a>';
                    $data[$key]['designer'] = 
                    '<a class="btn btn-info btn-labeled btn-xs data-back" target="_blank" href='.Url::toRoute(['/reports/designer','kode'=>$value['kode_doc']]).'>
                        <b><i class="fa fa-external-link"></i></b>Designer
                    </a>';
                }
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionCreate()
    {
        $model = new DokumenTercetakForm;
        $listApi = $this->getEnv();
        $request = Yii::$app->request;
        $docHeader = $docFooter = $jenisKertas = [];
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $model->doc_key = $model->menu_id . '-' . $model->sub_menu_id;
                    $response = $this->_restDcms->post('dokumen-tercetak/create',[
                        'form_params' => $model->attributes
                    ]);
                    $body = json_decode($response->getBody(),true);
                    $result = $body;
                } catch (RequestException $e) {
                    $result = $e->getMessage();
                } catch (\Exception $e) {
                    $result = $e->getMessage();
                }
                return DocoHelpers::response($result,false,'DokumenTercetakForm');
            } else {
                $response = $model->errors;
            }
            return DocoHelpers::response($response,422,$formName);
        } else {
            $response = $this->_restDcms->get('dokumen-tercetak/create',[
                'query' => []
            ]);
            $response = json_decode($response->getBody(),true);
            $response = $response['response'];
            $docHeader = $response['doc_header'];
            $docFooter = $response['doc_footer'];
            $jenisKertas = $response['jenis_kertas'];
        }
        return $this->render('form',get_defined_vars());
    }

    /**
    * @return array
    **/
    private function getEnv()
    {
        $baseApp = Yii::getAlias('@app');
        $ini = @parse_ini_file($baseApp .'/config/env/.env', true);
        $listApi = [];
        foreach ($ini['api'] as $key => $value) {
            $listApi[$key] = strtoupper($key);
        }
        return $listApi;
    }

    public function actionUpdate($id)
    {
        $model = new DokumenTercetakForm;
        $listApi = $this->getEnv();
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $docHeader = $docFooter = $jenisKertas = [];
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $model->doc_key = $model->menu_id . '-' . $model->sub_menu_id;
                    $response = $this->_restDcms->post('dokumen-tercetak/update',[
                        'query' => [
                            'id' => $id
                        ],
                        'form_params' => $model->attributes
                    ]);
                    $body = json_decode($response->getBody(),true);
                    $result = $body;
                } catch (RequestException $e) {
                    $result = $e->getMessage();
                } catch (\Exception $e) {
                    $result = $e->getMessage();
                }
                return DocoHelpers::response($result,false,'DokumenTercetakForm');
            } else {
                $response = $model->errors;
            }
            return DocoHelpers::response($response,422,$formName);
        } else {
            $response = $this->_restDcms->get('dokumen-tercetak/update',[
                'query' => [
                    'id' => $id
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $response = $response['response'];
            $docHeader = $response['doc_header'];
            $docFooter = $response['doc_footer'];
            $jenisKertas = $response['jenis_kertas'];
            $model->attributes = $response['data'];
            list($service,$controller,$method) = explode("-", $response['data']['doc_key']);
            $model->modul_id = $service;
            $model->menu_id = $service.'-'.$controller;
            $model->sub_menu_id = $method;
        }
        return $this->render('form',get_defined_vars());
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restDcms->delete('dokumen-tercetak/delete?id='.$id);
            $response = json_decode($response->getBody(),true);
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionGetController()
    {
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $id = isset($parents[0]) ? $parents[0] : false;
                if (!empty($id)) {
                    try {
                        $response = Yii::$app->docoRest->{$id}->request('GET','allow/get-attributes-dok');
                        $body = json_decode($response->getBody(),true);
                        Yii::$app->session->set($id,$body['response']['data_collect']);
                        return DocoHelpers::response($body['response']);
                    } catch (RequestException $e) {
                        return DocoHelpers::response(['message' => $e->getMessage()],500);
                    }
                }
            }
        }
        return DocoHelpers::response([]);
    }

    public function actionGetDokumen()
    {
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $id = isset($parents[0]) ? $parents[0] : false;
                if (!empty($id)) {
                    try {
                        list($service,$controller) = explode("-", $id);
                        $listAttrbute = Yii::$app->session->get($service);
                        $selected = isset($listAttrbute[$id]) ? $listAttrbute[$id] : [];
                        $output = [];
                        foreach ($selected as $key => $value) {
                            // remove action
                            $clearAction = str_replace("action", "", $key);
                            $string = preg_replace("/(?<=\\w)(?=[A-Z])/"," $1", $clearAction);
                            $output[] = [
                                'id' => $key,
                                'name' => $string
                            ];
                        }
                        Yii::$app->session->set('informasi-kontent',$selected);
                        Yii::$app->session->set('dt-attributes',[]);
                        $response = [
                            'output' => $output,
                            'selected' => ''
                        ];
                        return DocoHelpers::response($response);
                    } catch (\Exception $e) {
                        return DocoHelpers::response(['message' => $e->getMessage()],500);
                    }
                }
            }
        }
        return DocoHelpers::response([]);
    }

    public function actionSetSession()
    {
        $informasi = Yii::$app->session->get("informasi-kontent");
        $request = Yii::$app->request;
        if ($request->post()) {
            $id = $request->post('id');
            if (isset($informasi[$id])) {
                Yii::$app->session->set('dt-attributes',$informasi[$id]);
            }
        }
        return DocoHelpers::response([
            'message' => "success"
        ]);
    }

    public function actionGetDataAttributes()
    {
        $request = Yii::$app->request;
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;

        try {
            $dataAttribute = Yii::$app->session->get('dt-attributes');
            if ($dataAttribute) {
                $no = $request->get('start',1);
                foreach ($dataAttribute as $key => $value) {
                    $no++;
                    $explode = explode("=>", $value);
                    $data[] = [
                        'rowNum' => $no,
                        'attribute' => isset($explode[0]) ? trim($explode[0]) : '',
                        'fungsi' => isset($explode[1]) ? trim($explode[1]) : ''
                    ];
                }
                $count = count($dataAttribute);
                $result['data'] = $data;
                $result['recordsTotal'] = $count;
                $result['recordsFiltered'] = $count;
            }
            Yii::$app->session->set('dt-attributes',[]);
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }
}