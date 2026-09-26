<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-23 11:59:19
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-08-08 09:49:23
 */
namespace Doco\master\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\KegiatanOperasiForm;
use GuzzleHttp\Exception\RequestException;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use yii\helpers\ArrayHelper;


class KegiatanOperasiController extends DocoController
{
    // Protected variables
    protected $_restMaster;

    // Init
    public function init()
    {
        // Init parent
        parent::init();

        // Rest master
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    // Behaviors
    public function behaviors()
    {
        // Behaviors parent
        $behaviors = parent::behaviors();

        // Unset behaviors
        unset($behaviors['access']);
        unset($behaviors['verbs']);

        // Return behaviors
        return $behaviors;
    }

    // Action index
    public function actionIndex()
    {
        // Return index
        $data = $this->getDataApi();
        $kegiatanOperasi = $options = [];

        if (!empty($data['kegiatan_operasi'])) {
            $kegiatanOperasi = ArrayHelper::map($data['kegiatan_operasi'], 'kegiatanoperasi_nama', 'kegiatanoperasi_nama');
        }
        return $this->render('index', get_defined_vars());
    }

    // Action create
    public function actionCreate()
    {
        // Get request
        $request = Yii::$app->request;
        $model = new KegiatanOperasiForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);

        // Check post
        if ($request->post()) {
            // Load
            $model->load($request->post());

            // Validate model
            if ($model->validate()) {
                // Try catch
                try {
                    // Get response
                    $response = $this->_restMaster->post('kegiatan-operasi/create', [
                        'form_params' => $model->attributes
                    ]);

                    // Return
                    return DocoHelpers::responseJsonString($response->getBody(), $formName);
                } catch (RequestException $e) {
                    // Return
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    // Return
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                // Errors
                $errors = DocoHelpers::parseError($model->errors, $formName);

                // Return
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            // Return form
            return $this->renderPartial('form', get_defined_vars());
        }
    }

    // Action update
    public function actionUpdate($id = null)
    {
        // Get request
        $request = Yii::$app->request;
        $model = new KegiatanOperasiForm;

        $formName = substr(strrchr(get_class($model), "\\"), 1);

        // Decrypt id
        $id = DocoHelpers::decrypt($id);

        

        // Check post
        if ($request->post()) {
            // Load
            $model->load($request->post());

            // Validate model
            if ($model->validate()) {
                // Try catch
                try {
                    // Get response
                    $response = $this->_restMaster->put('kegiatan-operasi/update?id='.$id, [
                        'form_params' => $model->attributes
                    ]);

                    // Return
                    return DocoHelpers::responseJsonString($response->getBody(), $formName);
                } catch (RequestException $e) {
                    // Return
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    // Return
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                // Errors
                $errors = DocoHelpers::parseError($model->errors, $formName);

                // Return
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            // Get response
            $response = $this->_restMaster->get('kegiatan-operasi/view?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'];
            $model->attributes = $attributes;

            // Return
            return $this->renderPartial('form', get_defined_vars());
        }
    }

    // Action delete
    public function actionDelete($id)
    {
        // Decrypt id
        $id = DocoHelpers::decrypt($id);

        // Try catch
        try {
            // Get response
            $response = $this->_restMaster->delete('kegiatan-operasi/deleted?id='.$id);
            $response = json_decode($response->getBody(), true);
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];
            // Return
            return DocoHelpers::response($response);
            // return DocoHelpers::responseTemplate($response->getStatusCode(), "OK", []);
        } catch (RequestException $e) {
            // Return
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            // Return
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    // Action get data
    public function actionGetData()
    {
        // Format json
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Get request & convert to restful params
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        // echo '<pre>';
        // print_r($yiiRestfulParams);
        // echo '</pre>';
        // exit;
        $draw = $request->get('draw', 1);
        $data = [];

        // Try catch
        try {
            // Get response
            $response = $this->_restMaster->get('kegiatan-operasi/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);

            // Start number
            $no = $request->get('start', 1);

            // Loop
            foreach ($body['response']['data'] as $key => $value) {
                // Manage data for datatables
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['kegiatanoperasi_id']);
                $value['primary'] = $primaryKey;
                unset($value['kegiatanoperasi_id']);
                $value['rowNum'] = $no;
                $data[$key] = $value;
            }

            $return = [
                'data' => $data,
                'draw' => $request->get('draw'),
                'recordsTotal' => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount']
            ];

            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            // Error result
            $result['error'] = $e->getMessage();

            // Return result
            return $result;
        } catch (\Exception $e) {
            // Error result
            $result['error'] = $e->getMessage();

            // Return result
            return $result;
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
        $path = Yii::getAlias("@download") . "/kelompok_pemeriksaan_rad.pdf";

        // Try catch
        try {
            // Response
            $response = $this->_restMaster->get('kegiatan-operasi/export-pdf?' . http_build_query($yiiRestfulParams), ['save_to' => $path]);

            // Download pdf
            return DocoHelpers::downloadPdf($response, $path, 'kelompok-pemeriksaan-rad');
        } catch (RequestException $e) {
            // Exception
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            // Exception
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

        // Try catch
        try {
            // Response
            $response = $this->_restMaster->get('kegiatan-operasi/export-excel?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), true);
            $url = $body['response'];

            // Return download file
        return DocoHelpers::downloadFile($url);
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

    private function getDataApi()
    {
        try {
            $response = $this->_restMaster->get('kegiatan-operasi/index');
            $body = json_decode($response->getBody(), true);

            $result = [
                'kegiatan_operasi' => $body['response']['data'],
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

    public function actionGetKegiatanOperasi($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];

        try {
            $response = $this->_restMaster->get('kegiatan-operasi/index?advanced-filter[kegiatanoperasi_nama]=' . $q);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response']['data'] as $value)
                $result['results'][] = [
                'id' => $value['kegiatanoperasi_nama'],
                'text' => $value['kegiatanoperasi_nama']
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
}
?>