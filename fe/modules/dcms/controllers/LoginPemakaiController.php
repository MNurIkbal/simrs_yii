<?php
// Author : Ramdhan Nurrachman

namespace Doco\dcms\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\dcms\models\LoginpemakaiForm;
use GuzzleHttp\Exception\RequestException;

class LoginPemakaiController extends DocoController
{
    protected $_title = "Login Pemakai";
    protected $_module = '/dcms/login-pemakai/';
    protected $_restDcms;

    public function init()
    {
        parent::init();
        $this->_restDcms = Yii::$app->docoRest->dcms;
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
        $title = $this->_title;
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
            $response = $this->_restDcms->get('login-pemakai/index?'.http_build_query($yiiRestfulParams), 
                    [
                        'form_params' => []
                    ]
            );
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['loginpemakai_id']);
                $value['primary'] = $primaryKey;
                unset($value['loginpemakai_id']);

                /*$value['aksi'] = Html::a(
                        '<i class="fa fa-pencil" aria-hidden="true"></i>',
                            Url::to([$this->_module. 'update','id' => $primaryKey])
                            , [
                            'class' => 'btn btn-dark-turquise btn-xs data-update',
                            'style' => 'margin-right:5px',
                            'data-popup' => "tooltip",
                            'data-original-title' => \Yii::t('fe', 'Ubah'),
                        ]
                    );
                $value['aksi'] .= Html::a(
                    '<i class="fa fa-trash" aria-hidden="true"></i>', '#', [
                        'class' => 'btn btn-danger btn-xs data-delete',
                        'action' => Url::to([$this->_module .'delete','id' => $primaryKey]),
                        'data-popup' => "tooltip",
                        'title' => \Yii::t('fe', 'Hapus'),
                    ]
                );*/

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

    public function actionView($id)
    {
        $request = Yii::$app->request;
        $title = 'Lihat Data';
        $model = new LoginpemakaiForm;
        $id = DocoHelpers::decrypt($id);

        $response = $this->_restDcms->get('loginpemakai/view?id='.$id);
        $body = json_decode($response->getBody(), TRUE);
        $attributes = $body['response'];
        $model->attributes = $attributes;
        return $this->renderPartial('view', get_defined_vars());
    }

    public function actionCreate()
    {
        try {
            $request = Yii::$app->request;
            $title = 'Tambah Data';
            $model = new LoginpemakaiForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $id = null;
            $ruangan = [];
            if ($request->post()) {
                $model->load($request->post());
                $model->instalasi = $request->post('instalasi',[]);
                if ($model->validate()) {
                    $model->setPassword($model->katakunci_pemakai);
                    $response = $this->_restDcms->post('login-pemakai/create', [
                        'form_params' => $model->attributes
                    ]);
                    $response = json_decode($response->getBody(),true);
                } else {
                    $response = $model->errors;
                }
                return DocoHelpers::response($response,422,$formName);
            } else {
                try {
                    $response = $this->_restDcms->get('login-pemakai/get-data', [
                        'form_params' => []
                    ]);
                    $response = json_decode($response->getBody(),true);
                    $dataInstalasi = $response['response'];
                } catch (RequestException $e) {
                    $dataInstalasi = [];
                } 
                return $this->render('form', get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['error' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['error' => $e->getMessage()],500);
        }
    }

    public function actionUpdate($id = null)
    {
        try {
            $request = Yii::$app->request;
            $title = 'Ubah Data';
            $model = new LoginpemakaiForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $id = DocoHelpers::decrypt($id);

            if ($request->post()) {
                $model->load($request->post());
                $model->instalasi = $request->post('instalasi',[]);
                if ($model->validate()) {
                    if ($model->katakunci_pemakai != '') {
                        $model->setPassword($model->katakunci_pemakai);
                    }
                    
                    $response = $this->_restDcms->post('login-pemakai/update', [
                        'query' => [
                            'id' => $id
                        ],
                        'form_params' => $model->attributes
                    ]);
                    $response = json_decode($response->getBody(),true);
                } else {
                    $response = $model->errors;
                }
                return DocoHelpers::response($response,422,$formName);
            } else {
                try {
                    $response = $this->_restDcms->get('login-pemakai/get-data', [
                        'query' => [
                            'id' => $id
                        ]
                    ]);
                    $response = json_decode($response->getBody(),true);
                    $model->attributes = $response['response']['data_login'];
                    $model->instalasi = isset($response['response']['data_login']['ruangPemakai']) 
                                            ? $response['response']['data_login']['ruangPemakai']
                                            : [];
                    $ruangan = [];
                    foreach ($model->instalasi as $value) {
                        $ruangan[] = $value['ruangan_id'];
                    }
                    $dataInstalasi = $response['response']['data_instalasi'];
                } catch (RequestException $e) {
                    $dataInstalasi = [];
                } 
                return $this->render('form', get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['error' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['error' => $e->getMessage()],500);
        }
    }

    public function actionPegawai()
    {
        $title = "List Pegawai";
        return $this->renderPartial('pegawai',get_defined_vars());
    }

    public function actionGetPegawai()
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
            $response = $this->_restDcms->get('login-pemakai/get-pegawai?'.http_build_query($yiiRestfulParams), 
                    [
                        'form_params' => []
                    ]
            );
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);

            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pegawai_id']);

                $value['aksi'] = Html::a(
                        '<i class="fa fa-check-square-o" aria-hidden="true"></i>',
                            Url::to([$this->_module. 'update','id' => $primaryKey])
                            , [
                            'class' => 'btn btn-success btn-xs select-pegawai',
                            'style' => 'margin-right:5px;padding-left:9px!important;',
                            'data-id' => $value['pegawai_id'],
                            'data-label' => $value['nomorindukpegawai'] . ' - ' . $value['nama_pegawai'],
                            'data-tooltip' => "tooltip",
                            'title' => \Yii::t('fe', 'Pilih'),
                        ]
                    );
                $value['primary'] = $primaryKey;
                unset($value['pegawai_id']);
                $value['rowNum'] = $no;
                $data[$key] = $value;
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

    public function actionAutocompletePegawai()
    {
        try { 
            $request = Yii::$app->request;
            $response = $this->_restDcms->get('login-pemakai/autocomplete-pegawai', [
                'query' => [
                    'q' => $request->get('q')
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $data = isset($response['response']['data']) ? $response['response']['data'] : [];
        } catch (RequestException $e) {
            $data = ['errors' => $e->getMessage()];
        }
        return DocoHelpers::response($data);
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restDcms->delete('login-pemakai/delete?id='.$id);
            $response = json_decode($response->getBody(),true);
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response($e->getMessage(),500);
        } catch (\Exception $e) {
            return DocoHelpers::response($e->getMessage(),500);
        }
    }

    public function actionExport($id)
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionPrint($id)
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionExportAll()
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionPrintAll()
    {
        return $this->render('index', get_defined_vars());
    }
}
