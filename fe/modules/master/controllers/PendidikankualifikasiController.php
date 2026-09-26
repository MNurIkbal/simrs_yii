<?php
// Author : Ramdhan Nurrachman
// Modify : Naufal Ziyad L

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\PendidikankualifikasiForm;
use GuzzleHttp\Exception\RequestException;

class PendidikanKualifikasiController extends DocoController
{
    protected $_title = "Master :: Pendidikankualifikasi";
    protected $_module = 'master/pendidikan-kualifikasi/';
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
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
        $status = $this->_status;
        return $this->renderPartial('index', get_defined_vars());
    }

    public function actionGetDataPendKualifikasi()
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
            $response = $this->_restMaster->get('pendidikankualifikasi/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pendkualifikasi_id']);
                unset($value['pendkualifikasi_id']);
                 $value['aksi']  = Html::button(
                    "<i class='fa fa-eye'></i>", [
                        'style' => 'margin-right:5px',
                        'class' => 'btn btn-info btn-xs data-view',
                        'action' => Url::home().$this->_module.'view?id='.$primaryKey,
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_pend_kualifikasi',
                        'data-popup' => "tooltip",
                        'data-placement' => 'bottom',
                        'data-original-title' => Yii::t('fe', 'Lihat')
                    ]
                );
                $value['aksi'] .= Html::button( 
                    "<i class='fa fa-pencil'></i>", [
                        'style' => 'margin-right:5px ',
                        'class' => 'btn btn-primary btn-xs data-update-pendidikan-kualifikasi',
                        'action' => Url::home().$this->_module.'update?id='.$primaryKey,
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_pend_kualifikasi',
                        'data-popup' => "tooltip",
                        'data-placement' => 'bottom',
                        'data-original-title' => Yii::t('fe', 'Ubah'),
                    ]
                );
                $value['aksi'] .= Html::a(
                    "<i class='fa fa-trash'></i>",'#', [
                        'id' => 'hapus_pendidikan_kualifikasi',
                        'style' => 'margin-right:5px',
                        'class' => 'btn btn-danger btn-xs delete data-delete-pendidikan-kualifikasi',
                        'data-popup' => "tooltip",
                        'data-placement' => 'bottom',
                        'data-original-title' => Yii::t('fe', 'Hapus'),
                        'action' => Url::home().$this->_module.'delete?id='.$primaryKey
                    ]
                );
                $value['is_active'] = DocoHelpers::isActive($value['is_active']);
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

    public function actionView($id)
    {
        $request = Yii::$app->request;
        $title = 'Lihat Data';
        $model = new PendidikankualifikasiForm;
        $id = DocoHelpers::decrypt($id);

        $response = $this->_restMaster->get('pendidikankualifikasi/view?id='.$id);
        $body = json_decode($response->getBody(), TRUE);
        $attributes = $body['response'];
        $model->attributes = $attributes;
        return $this->renderPartial('view', get_defined_vars());
    }

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $title = 'Tambah Data';
        $model = new PendidikankualifikasiForm;
        $status = $this->_status;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('pendidikankualifikasi/create', [
                        'form_params' => $model->attributes
                    ]);

                    return DocoHelpers::responseJsonString($response->getBody(), $formName);
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            $response = $this->_restMaster->get('kelompok-pegawai');
            $body = json_decode($response->getBody(), TRUE);
            $kelompokpegawai = $body['response']['data'];
            
            $response = $this->_restMaster->get('pendidikan');
            $body = json_decode($response->getBody(), TRUE);
            $pendidikan = $body['response']['data'];
            
            return $this->renderPartial('form', get_defined_vars());
        }
    }

    public function actionUpdate($id = null)
    {
        $request = Yii::$app->request;
        $title = 'Ubah Data';
        $model = new PendidikankualifikasiForm;
        $status = $this->_status;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);
        
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->put('pendidikankualifikasi/update?id='.$id, [
                        'form_params' => $model->attributes
                    ]);

                    return DocoHelpers::responseJsonString($response->getBody(), $formName);
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            
            $response = $this->_restMaster->get('kelompok-pegawai');
            $body = json_decode($response->getBody(), TRUE);
            $kelompokpegawai = $body['response']['data'];
            
            $response = $this->_restMaster->get('pendidikan');
            $body = json_decode($response->getBody(), TRUE);
            $pendidikan = $body['response']['data'];
            
            $response = $this->_restMaster->get('pendidikankualifikasi/view?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'];
            $model->attributes = $attributes;
            return $this->renderPartial('form', get_defined_vars());
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restMaster->delete('pendidikankualifikasi/delete?id='.$id);
            return DocoHelpers::responseTemplate(
                $response->getStatusCode(), 
                "OK", [
            ]);
        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(), 
                json_decode($e->getResponse()->getBody()->getContents())->message, [
            ]);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
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
