<?php 

namespace Doco\dcms\controllers;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use Doco\dcms\models\AksesPenggunaForm;
use yii\web\Response;

class AksesPenggunaController extends DocoController
{
    
    protected $_title = "Akses Pengguna";
    protected $_module = 'akses-pengguna/';
    protected $_restDcms;

    public function init()
    {
        parent::init();
        $this->_restDcms = Yii::$app->docoRest->dcms;
    }

    public function actionIndex()
    {
        // Init
        $status = $this->_status; 
        $options = $this->_options;
        
        return $this->render('index', get_defined_vars());
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
        $no = $request->get('start',1);

        try {
            $response = $this->_restDcms->get('akses-pengguna?'.http_build_query($yiiRestfulParams), 
                                [
                                    'form_params' => []
                                ]);
            $body = json_decode($response->getBody(), True);
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
                    );*/

                $value['rowNum'] = $no;
                $value['akses_pengguna'] = '';
                if (!isset($value['aksesPengguna'])) continue;
                foreach ($value['aksesPengguna'] as $val) {
                    $value['akses_pengguna'] .= '<span class="label border-left-primary label-striped"
                        style="margin-right:5px;">'. 
                        $val['peranpenggunanama']
                     .'</span>';
                }

                $data[] = $value;
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

    public function actionDokumenTercetak()
    {

        $date = date("Y-m-d");
        $path = Yii::getAlias("@download") . "/dokumen-tercetak.pdf";

        try {
            $response = $this->_restDcms->post('akses-pengguna/dokumen-tercetak', [
                'query' => [
                    'id' => null
                ],
                'form_params' => [],
                'save_to' => $path
            ]);

            return DocoHelpers::downloadPdf($response,$path);
        } catch (RequestException $e) {
            var_dump($e->getMessage());
            die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionUpdate($id = null)
    {
        if (!$id) throw new \yii\web\HttpException(505,Yii::t("fe","Data tidak di temukan"));
        try {
            $request = Yii::$app->request;
            $title = Yii::t('fe', 'Ubah Data');
            $model = new AksesPenggunaForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $id = DocoHelpers::decrypt($id);
            if ($request->post()) {
                $model->load($request->post());
                $model->akses_pemakai = $request->post('akses',[]);
                if ($model->validate()) {
                    $response = $this->_restDcms->post('akses-pengguna/update', [
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
                    $response = $this->_restDcms->get('akses-pengguna/update', [
                        'query' => [
                            'id' => $id
                        ],
                        'form_params' => []
                    ]);
                    $response = json_decode($response->getBody(),true);
                    $dataModul = $response['response']['modul'];
                    $aksesPengguna = $response['response']['akses_pengguna'];
                    $model->attributes = $response['response']['data'];
                } catch (RequestException $e) {
                    $aksesPengguna = [];
                } 
                return $this->render('form', get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['error' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['error' => $e->getMessage()],500);
        }
    }

    public function actionDelete()
    {

    }

    public function actionUser()
    {
        $title = Yii::t('fe', 'Daftar Pengguna');
        return $this->renderPartial('user',get_defined_vars());
    }

    public function actionGetUser()
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
        try {
            $response = $this->_restDcms->get('akses-pengguna/get-user?'.http_build_query($yiiRestfulParams), 
                    [
                        'form_params' => []
                    ]
            );
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);

            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['loginpemakai_id']);

               /* $value['aksi'] = Html::a(
                        '<i class="fa fa-check-square-o" aria-hidden="true"></i>',
                            Url::to([$this->_module. 'update','id' => $primaryKey])
                            , [
                            'class' => 'btn btn-success btn-xs select-pegawai',
                            'style' => 'margin-right:5px',
                            'data-id' => $value['loginpemakai_id'],
                            'data-label' =>$value['nama_pemakai'],
                            'data-tooltip' => "tooltip",
                            'title' => \Yii::t('fe', 'Pilih'),
                        ]
                    );*/
                $value['primary'] = $primaryKey;
                unset($value['loginpemakai_id']);
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

    public function actionAutocompleteUser()
    {
        try { 
            $request = Yii::$app->request;
            $response = $this->_restDcms->get('akses-pengguna/autocomplete-user', [
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

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $path = Yii::getAlias("@download") . "/akses-pengguna.xlsx";
            $response = $this->_restDcms->get('akses-pengguna/export-excel',[
                'query' => $yiiRestfulParams,
                'save_to' => $path
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/akses-pengguna.pdf";
        try {
            $response = $this->_restDcms->get('akses-pengguna/cetak-pdf?' . http_build_query($yiiRestfulParams),[
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