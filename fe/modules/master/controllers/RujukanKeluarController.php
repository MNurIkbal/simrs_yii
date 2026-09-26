<?php
// Author : Naufal Ziyad L

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\RujukanKeluarForm;
use GuzzleHttp\Exception\RequestException;

class RujukanKeluarController extends DocoController
{
    protected $allowAction = [ '*' ];
    protected $_title = "Master :: Rujukan Keluar";
    protected $_module = 'master/rujukan-keluar/';
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

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actionIndex()
    {
        $status = $this->_status; $options = $this->_options;
        return $this->render('index', get_defined_vars());
    }

    public function actionGetDataRujukanKeluar()
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
            $response = $this->_restMaster->get('rujukan-keluar/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['rujukankeluar_id']);
                $value['primary'] = $primaryKey;
                unset($value['rujukankeluar_id']);

               /* $value['aksi']  = Html::button(
                    "<i class='fa fa-eye'></i>", [
                        'style' => 'margin-right:5px',
                        'class' => 'btn btn-info btn-xs data-view',
                        'action' => Url::home().$this->_module.'view?id='.$primaryKey,
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_rujukankeluar',
                        'data-popup' => "tooltip",
                        'data-placement' => 'bottom',
                        'data-original-title' => Yii::t('fe', 'Lihat')
                    ]
                );
                $value['aksi'] .= Html::button(
                    "<i class='fa fa-pencil'></i>", [
                        'style' => 'margin-right:5px ',
                        'class' => 'btn btn-primary btn-xs data-update-rujukan-keluar',
                        'action' => Url::home().$this->_module.'update?id='.$primaryKey,
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_rujukankeluar',
                        'data-popup' => "tooltip",
                        'data-placement' => 'bottom',
                        'data-original-title' => Yii::t('fe', 'Ubah'),
                    ]
                );
                $value['aksi'] .= Html::a(
                    "<i class='fa fa-trash'></i>",'#', [
                        'id' => 'hapus_rujukankeluar',
                        'style' => 'margin-right:5px',
                        'class' => 'btn btn-danger btn-xs delete data-delete-rujukan-keluar',
                        'data-popup' => "tooltip",
                        'data-placement' => 'bottom',
                        'data-original-title' => Yii::t('fe', 'Hapus'),
                        'action' => Url::home().$this->_module.'delete?id='.$primaryKey
                    ]
                );*/
                $value['is_active'] = DocoHelpers::switchStatus($value['is_active'], $primaryKey, 'change-status-rujukan-keluar');
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
        $model = new RujukanKeluarForm;
        $id = DocoHelpers::decrypt($id);

        $response = $this->_restMaster->get('rujukan-keluar/view?id='.$id);
        $body = json_decode($response->getBody(), TRUE);
        $attributes = $body['response'];
        $model->attributes = $attributes;
        return $this->renderPartial('view', get_defined_vars());
    }

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $title = 'Tambah Data';
        $model = new RujukanKeluarForm;
        $model->is_active = true;
        $status = $this->_status;
        $options = $this->_options;
        $formName = substr(strrchr(get_class($model), "\\"), 1);

        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('rujukan-keluar/create', [
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
            $response = $this->_restMaster->get('asal-rujukan');
            $body = json_decode($response->getBody(), TRUE);
            $asalrujukan = $body['response']['data'];

            return $this->renderPartial('form', get_defined_vars());
        }
    }

    public function actionUpdate($id = null)
    {
        $request = Yii::$app->request;
        $title = 'Ubah Data';
        $model = new RujukanKeluarForm;
        $status = $this->_status; 
        $options = $this->_options;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);

        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->put('rujukan-keluar/update?id='.$id, [
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
            $response = $this->_restMaster->get('asal-rujukan');
            $body = json_decode($response->getBody(), TRUE);
            $asalrujukan = $body['response']['data'];

            $response = $this->_restMaster->get('rujukan-keluar/view?id='.$id);
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
            $response = $this->_restMaster->delete('rujukan-keluar/delete?id='.$id);
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

    public function actionChangeStatusRujukanKeluar($id, $status)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restMaster->put('rujukan-keluar/update?id='.$id, [
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

    // Export pdf
    public function actionExportPdf()
    {
        // Get request
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        // Download path
        $path = Yii::getAlias("@download") . "/keluar.pdf";

        // Try catch
        try {
            // Response
            $response = $this->_restMaster->get('rujukan-keluar/export-pdf?'.http_build_query($yiiRestfulParams), ['save_to' => $path]);

            // Download pdf
            return DocoHelpers::downloadPdf($response, $path, 'rujukan-keluar');
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

        // Get request
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/rujukan-keluar.xlsx";

        // Try catch
        try {
            // Response
            $response = $this->_restMaster->get('rujukan-keluar/export-excel', [
                "query" => $yiiRestfulParams,
                "save_to" => $path
            ]);

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
