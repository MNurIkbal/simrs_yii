<?php
// Author : Ramdhan Nurrachman

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\CaraBayarForm;
use GuzzleHttp\Exception\RequestException;

class CaraBayarController extends DocoController
{
    protected $_title = "Cara Bayar";
    protected $_module = 'master/cara-bayar/';
    protected $_restMaster;
    protected $allowAction = ['*'];

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
        // Init
        $status = $this->_status; $options = $this->_options;
        unset($options['confirm'][""]);
        unset($options['status'][""]);

        $MetodeBayarRequest = $this->_restMaster->get('lookup/list-lookup-by-type?param=cara_pembayaran');
        $body = json_decode($MetodeBayarRequest->getBody(),TRUE);
        $metode_bayar = $body['response'];

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
            $response = $this->_restMaster->get('cara-bayar/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['carabayar_id']);
                $value['primary'] = $primaryKey;
                unset($value['carabayar_id']);
        
                /*$value['aksi']  = '<table class="no-border"><tr>';
                $value['aksi'] .= '<td>&nbsp;'.Html::button(
                    '<i class="fa fa-eye" aria-hidden="true"></i>', [
                        'class' => 'btn btn-info btn-xs data-view',
                        'action' => Url::home().$this->_module.'view?id='.$primaryKey,
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_backdrop',
                        'data-popup' => "tooltip",
                        'title' => \Yii::t('fe', 'Lihat'),
                    ]
                ).'&nbsp;</td>';
                $value['aksi'] .= '<td>&nbsp;'.Html::a(
                    '<i class="fa fa-file-excel-o" aria-hidden="true"></i>', '#', [
                        'class' => 'btn btn-green btn-xs data-export',
                        'action' => Url::home().$this->_module.'export?id='.$primaryKey,
                        'data-popup' => "tooltip",
                        'title' => \Yii::t('fe', 'Ekspor'),
                    ]
                ).'&nbsp;</td>';
                $value['aksi'] .= '<td>&nbsp;'.Html::a(
                    '<i class="fa fa-file-pdf-o" aria-hidden="true"></i>', '#', [
                        'class' => 'btn btn-crimson btn-xs data-print',
                        'action' => Url::home().$this->_module.'print?id='.$primaryKey,
                        'data-popup' => "tooltip",
                        'title' => \Yii::t('fe', 'Cetak'),
                    ]
                ).'&nbsp;</td>';
                $value['aksi'] .= '<td>&nbsp;'.Html::button(
                    '<i class="fa fa-pencil" aria-hidden="true"></i>', [
                        'class' => 'btn btn-dark-turquise btn-xs data-update',
                        'action' => Url::home().$this->_module.'update?id='.$primaryKey,
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_backdrop',
                        'data-popup' => "tooltip",
                        'data-original-title' => \Yii::t('fe', 'Ubah'),
                    ]
                ).'&nbsp;</td>';
                $value['aksi'] .= '<td>&nbsp;'.Html::a(
                    '<i class="fa fa-trash" aria-hidden="true"></i>', '#', [
                        'class' => 'btn btn-danger btn-xs data-delete',
                        'action' => Url::home().$this->_module.'delete?id='.$primaryKey,
                        'data-popup' => "tooltip",
                        'title' => \Yii::t('fe', 'Hapus'),
                    ]
                ).'&nbsp;</td>';
                $value['aksi'] .= '</tr></table>';*/

                $value['is_subsidiasuransi'] = DocoHelpers::labelYesNo($value['is_subsidiasuransi']);
                $value['is_subsidipemerintah'] = DocoHelpers::labelYesNo($value['is_subsidipemerintah']);
                $value['is_subsidirs'] = DocoHelpers::labelYesNo($value['is_subsidirs']);
                $value['is_active'] = DocoHelpers::isActive($value['is_active']);
                $value['is_online'] = DocoHelpers::switchStatus($value['is_online'], $primaryKey,'change-status','Ya','Tidak');
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
        $title = \Yii::t('fe', 'Lihat').' '.\Yii::t('fe', $this->_title);
        $model = new CaraBayarForm;
        $id = DocoHelpers::decrypt($id);

        $response = $this->_restMaster->get('cara-bayar/view?id='.$id);
        $body = json_decode($response->getBody(), TRUE);
        $attributes = $body['response'];
        $model->attributes = $attributes;
        return $this->renderPartial('view', get_defined_vars());
    }

    public function actionCreate()
    {
        // Init
        $status = $this->_status; $options = $this->_options;
        unset($options['confirm'][""]);
        unset($options['status'][""]);

        $MetodeBayarRequest = $this->_restMaster->get('lookup/list-lookup-by-type?param=metode_bayar');
        $body = json_decode($MetodeBayarRequest->getBody(),TRUE);
        $metode_bayar = $body['response'];
        
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Tambah').' '.\Yii::t('fe', $this->_title);
        $model = new CaraBayarForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                $response = $this->_restMaster->post('cara-bayar/create-cara-bayar', [
                    'form_params' => $model->attributes
                ]);

                $body = json_decode($response->getBody(),true);
                return DocoHelpers::response($body);
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::response([
                    'response' => [
                        'data' => $errors
                    ]
                ],422);
            }
        } else {
            return $this->renderPartial('form', get_defined_vars());
        }
    }

    public function actionUpdate($id = null)
    {
        // Init
        $status = $this->_status; $options = $this->_options;

        $MetodeBayarRequest = $this->_restMaster->get('lookup/list-lookup-by-type?param=metode_bayar');
        $body = json_decode($MetodeBayarRequest->getBody(),TRUE);
        $metode_bayar = $body['response'];

        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Ubah').' '.\Yii::t('fe', $this->_title);
        $model = new CaraBayarForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);
        
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('cara-bayar/update-cara-bayar?id='.$id, [
                        'form_params' => $model->attributes
                    ]);

                    $body = json_decode($response->getBody(), TRUE);
                    return DocoHelpers::response($body);
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            $response = $this->_restMaster->get('cara-bayar/view?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'];
            $model->attributes = $attributes;
            $model->is_subsidiasuransi = ($model->is_subsidiasuransi == false) ? 0 : 1;
            $model->is_active = ($model->is_active == false) ? 0 : 1;
            $model->is_subsidirs = ($model->is_subsidirs == false) ? 0 : 1;
            $model->is_subsidipemerintah = ($model->is_subsidipemerintah == false) ? 0 : 1;
            return $this->renderPartial('form', get_defined_vars());
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restMaster->delete('cara-bayar/delete?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            return DocoHelpers::response($body);
            // return DocoHelpers::responseTemplate(
            //     $response->getStatusCode(), 
            //     "OK", [
            // ]);
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
            // return DocoHelpers::responseTemplate(
            //     $e->getResponse()->getStatusCode(), 
            //     json_decode($e->getResponse()->getBody()->getContents())->message, [
            // ]);
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

    public function actionChangeStatus($id, $status)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restMaster->put('cara-bayar/update-online?id='.$id.'&is_online='.$status);

            $data = [
                'title' => \Yii::t('fe', 'Proses berhasil')." !",
                'text' => \Yii::t('fe', "Tampil di mobile berhasil diubah."),
                'response' => $response
            ];
            return DocoHelpers::responseTemplate(
                $response->getStatusCode(), 
                "OK", 
                [],
                $data
            );
        } catch (RequestException $e) {
            $data = [
                'title' => \Yii::t('fe', 'Proses gagal ')." !",
                'text' => \Yii::t('fe', "Tampil di mobile tidak berhasil dubah.")
            ];
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(), 
                json_decode($e->getResponse()->getBody()->getContents())->message,  
                [],
                $data
            );
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    // Export pdf
    public function actionExportPdf()
    {
        // Get request
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $url = 'cara-bayar/export-pdf?'.http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/cara_bayar.pdf";

        // Try catch
        try {
            $response = $this->_restMaster->get($url, [
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), TRUE);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    // Export excel
    public function actionExportExcel()
    {
        // Convert to json format
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $url = 'cara-bayar/export-excel?'.http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/Master Cara Bayar.xlsx";

        // Try catch
        try {
            // Response
            $response = $this->_restMaster->get($url, [
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            $url = $body['response'];

            // Return download file
        return DocoHelpers::downloadFile($path, true);
        } catch (\Exception $e) {
            // Get message
            $result['error'] = $e->getMessage();

            // Return
            return $result;
        } catch (RequestException $e) {
            // Get message
            $result['error'] = $e->getMessage();
            
            // Return
            return $result;
        }
    }

    public function actionGetDataSelect2()
    {
        return $this->guzzleExec($this->_restMaster, [
            'url' => 'cara-bayar/get-data-select2',
            'payload' => [
                'query' => Yii::$app->request->get()
            ],
            'returnResponse' => true
        ]);
    }
}
