<?php
// Author : Naufal Ziyad L
// since 2018-01-10 11:09:00


namespace Doco\master\controllers;

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
use app\modules\master\models\AsalRujukanForm;

class AsalRujukanController extends DocoController
{
    protected $allowAction = [ '*' ];
    protected $_title = "Asal Rujukan";
    protected $_module = 'master/asal-rujukan/';
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_status = [
            '1' => Yii::t('fe', 'Aktif'),
            '0' => Yii::t('fe', 'Tidak aktif'),
        ];
    }

    public function actionIndex()
    {
        $status = $this->_status; 
        $options = $this->_options;
        return $this->render('index',get_defined_vars());
    }

    public function actionGetDataAsalRujukan()
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
            $response = $this->_restMaster->get('asal-rujukan/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['asalrujukan_id']);
                $value['primary'] = $primaryKey;
                unset($value['asalrujukan_id']);
                // $value['aksi']  = Html::button(
                //     "<i class='fa fa-eye'></i>", [
                //         'style' => 'margin-right:5px',
                //         'class' => 'btn btn-info btn-xs data-view',
                //         'action' => Url::home().$this->_module.'view?id='.$primaryKey,
                //         'data-toggle' => 'modal',
                //         'data-target' => '#modal_asalrujukan',
                //         'data-popup' => "tooltip",
                //         'data-placement' => 'bottom',
                //         'data-original-title' => Yii::t('fe', 'Lihat')
                //     ]
                // );
                // $value['aksi'] .= Html::button(
                //     "<i class='fa fa-pencil'></i>", [
                //         'style' => 'margin-right:5px ',
                //         'class' => 'btn btn-primary btn-xs data-update-asalrujukan',
                //         'action' => Url::home().$this->_module.'update?id='.$primaryKey,
                //         'data-toggle' => 'modal',
                //         'data-target' => '#modal_asalrujukan',
                //         'data-popup' => "tooltip",
                //         'data-placement' => 'bottom',
                //         'data-original-title' => Yii::t('fe', 'Ubah'),
                //     ]
                // );
                // $value['aksi'] .= Html::a(
                //     "<i class='fa fa-trash'></i>",'#', [
                //         'id' => 'hapus_asalrujukan',
                //         'style' => 'margin-right:5px',
                //         'class' => 'btn btn-danger btn-xs delete data-delete-asalrujukan',
                //         'data-popup' => "tooltip",
                //         'data-placement' => 'bottom',
                //         'data-original-title' => Yii::t('fe', 'Hapus'),
                //         'action' => Url::home().$this->_module.'delete?id='.$primaryKey
                //     ]
                // );
                $value['is_active'] = DocoHelpers::switchStatus($value['is_active'], $primaryKey);
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
        $title = Yii::t('fe', 'Lihat Data');
        $model = new AsalRujukanForm;
        $id = DocoHelpers::decrypt($id);

        $response = $this->_restMaster->get('asal-rujukan/view?id='.$id);
        $body = json_decode($response->getBody(), TRUE);
        $attributes = $body['response'];
        $model->attributes = $attributes;
        return $this->renderPartial('view', get_defined_vars());
    }

    public function actionCreate()
    {
        try {
            $title = Yii::t('fe', 'Tambah Asal Rujukan');
            $model = new AsalRujukanForm;
            $status = $this->_status; 
            $options = $this->_options;
            $request = Yii::$app->request;
            if ($request->post()) {
                $model->load($request->post());
                if ($model->validate()) {
                  //check data if duplicate
                  $asal_rujukan = $model->asalrujukan_nama;
                  $get_chek = $this->_restMaster->get('asal-rujukan/check-asal-rujukan?nama='. $asal_rujukan);
                  $get_chekbody = json_decode($get_chek->getBody(), TRUE);
                  $get_chekattributes = $get_chekbody['response'];

                    if (count($get_chekattributes)==0) {
                      $response = $this->_restMaster->request('POST', 'asal-rujukan/create',[
                                          'form_params' => $model->attributes
                                  ]);
                      $response = json_decode($response->getBody(),true);
                      return DocoHelpers::response($response,false,true);
                    }else{
                      return DocoHelpers::response([
                          'response' => [
                              'message' => 'Data Sudah tersedia'
                          ]
                        ],500);
                    }

                } else {
                    $errors = DocoHelpers::parseError($model->errors,'AsalRujukanForm');
                    return DocoHelpers::response([
                            'response' => [
                                'data' => $errors
                            ]
                        ],422);
                }
            } else {
                $model->is_active = 1;
                return $this->renderPartial('form',get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionUpdate($id = null)
    {
        $request = Yii::$app->request;
        $title = Yii::t('fe', 'Ubah Data');
        $model = new AsalRujukanForm;
        $status = $this->_status; 
        $options = $this->_options;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);

        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                  $asal_rujukan = $model->asalrujukan_nama;
                  //check data if duplicate
                  $get_chek = $this->_restMaster->get('asal-rujukan/check-asal-rujukan?nama='.$asal_rujukan.'&id='.$id);
                  $get_chekbody = json_decode($get_chek->getBody(), TRUE);
                  $get_chekattributes = $get_chekbody['response'];

                    if (count($get_chekattributes)==0) {
                      $response = $this->_restMaster->put('asal-rujukan/update?id='.$id, [
                          'form_params' => $model->attributes
                      ]);
                      return DocoHelpers::responseJsonString($response->getBody(), $formName);
                    }else{
                      return DocoHelpers::response([
                          'response' => [
                              'message' => 'Data Sudah tersedia'
                          ]
                        ],500);
                    }
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
            $response = $this->_restMaster->get('asal-rujukan/view?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'];
            $attributes['is_active'] = (empty($attributes['is_active'])) ?  '0' : '1';
            $model->attributes = $attributes;
            return $this->renderPartial('form', get_defined_vars());
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restMaster->delete('asal-rujukan/delete?id='.$id);
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

    public function actionChangeStatus($id, $status)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restMaster->put('asal-rujukan/update?id='.$id, [
                'form_params' => ["is_active" => $status]
            ]);

            $data = [
                'title' => \Yii::t('fe', 'Proses berhasil')." !",
                'text' => \Yii::t('fe', "Status berhasil diubah.")
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
                'text' => \Yii::t('fe', "Status tidak berhasil dubah.")
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

        // Download path
        $path = Yii::getAlias("@download") . "/asal_rujukan.pdf";

        // Try catch
        try {
            // Response
            $response = $this->_restMaster->get('asal-rujukan/export-pdf?'.http_build_query($yiiRestfulParams), ['save_to' => $path]);

            // Download pdf
            return DocoHelpers::downloadPdf($response, $path, 'asal-rujukan');
        } catch (RequestException $e) {
            echo '<pre>';
            print_r($e->getMessage());
            exit();
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
        $path = Yii::getAlias("@download") . "/Master Asal Rujukan.xlsx";

        // Try catch
        try {
            // Response
            $response = $this->_restMaster->get('asal-rujukan/export-excel?'.http_build_query($yiiRestfulParams), [
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
}
