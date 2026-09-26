<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Jenis Pemeriksaan Radiologi
 * @copyright 03 Juli 2018 aweutist
 */

namespace Doco\master\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\JenisPemeriksaanRadForm;
use GuzzleHttp\Exception\RequestException;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;

class JenisPemeriksaanRadController extends DocoController
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
        // Declare some variables
        $data = $this->getDataApi();
        // Declare some variables
        $kelompokPemeriksaanRad = $jenisPemeriksaanrad = $options = [];

        if (!empty($data['jenis_pemeriksaan'])) {
            $jenisPemeriksaanRad = ArrayHelper::map($data['jenis_pemeriksaan'], 'jenispemeriksaanrad_id', 'jenispemeriksaanrad_nama');
        }
        if (!empty($data['kelompok_pemeriksaan'])) {
            $kelompokPemeriksaanRad = ArrayHelper::map($data['kelompok_pemeriksaan'], 'kelompokpemeriksaanrad_id', 'nama_kelompok');
        }

        // Return index
        return $this->render('index', get_defined_vars());
    }

    // Action create
    public function actionCreate()
    {
        $instalasi = Yii::$app->docoVars->workspace("instalasi_id");
        $kelompokPemeriksaanRad = [];

        // Get request
        $request = Yii::$app->request;
        $model = new JenisPemeriksaanRadForm;
        // $model->scenario = 'insert';
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
                    $response = $this->_restMaster->post('jenis-pemeriksaan-rad/create', [
                        'form_params' => $model->attributes
                    ]);
                    // Yii::$app->cache->delete("data-jenis-pemeriksaan-rad-{$instalasi}");

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
            $response = $this->_restMaster->get('kelompok-pemeriksaan-rad/index');
            $response = json_decode($response->getBody(), TRUE);

            // Check data
            if (!empty($response['response']['data'])) {
                // Assign into array
                $kelompokPemeriksaanRad = ArrayHelper::map($response['response']['data'], 'kelompokpemeriksaanrad_id', 'nama_kelompok');
            }
            // Return form
            return $this->renderPartial('form', get_defined_vars());
        }
    }

    // Action update
    public function actionUpdate($id = null)
    {
        $instalasi = Yii::$app->docoVars->workspace("instalasi_id");
        // Declare some variables
        $kelompokPemeriksaanRad = [];

        // Get response
        $response = $this->_restMaster->get('kelompok-pemeriksaan-rad/index');
        $response = json_decode($response->getBody(), TRUE);

        // Check data
        if (!empty($response['response']['data'])) {
            // Assign into array
            $kelompokPemeriksaanRad = ArrayHelper::map($response['response']['data'], 'kelompokpemeriksaanrad_id', 'nama_kelompok');
        }

        // Get request
        $request = Yii::$app->request;
        $model = new JenisPemeriksaanRadForm;
        // $model->scenario = 'update';
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
                    $response = $this->_restMaster->put('jenis-pemeriksaan-rad/update?id='.$id, [
                        'form_params' => $model->attributes
                    ]);
                    // Yii::$app->cache->delete("data-jenis-pemeriksaan-rad-{$instalasi}");

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
            $response = $this->_restMaster->get('jenis-pemeriksaan-rad/view?id='.$id);
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
        $instalasi = Yii::$app->docoVars->workspace("instalasi_id");
        // Decrypt id
        $id = DocoHelpers::decrypt($id);
        
        // Try catch
        try {
            // Get response
            $response = $this->_restMaster->delete('jenis-pemeriksaan-rad/delete?id='.$id);
            // Yii::$app->cache->delete("data-jenis-pemeriksaan-rad-{$instalasi}");
            $response = json_decode($response->getBody(),true);
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];

            return DocoHelpers::response($response);
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
        $instalasi = Yii::$app->docoVars->workspace("instalasi_id");
        // Format json
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Get request & convert to restful params
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = $cache = [];

        // Initiate data
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        // Try catch
        try {
            // Get response
            $response = $this->_restMaster->get('jenis-pemeriksaan-rad/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);

            // Start number
            $no = $request->get('start', 1);

            // Check body response
            if (!empty($body['response']['data'])) {
                // Loop
                foreach ($body['response']['data'] as $key => $value) {
                    // Manage data for datatables
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['jenispemeriksaanrad_id']);
                    $value['primary'] = $primaryKey;
                    $cache[] = [
                        'jenispemeriksaanrad_id' => $value['jenispemeriksaanrad_id'],
                        'jenispemeriksaanrad_kode' => $value['jenispemeriksaanrad_kode'],
                        'jenispemeriksaanrad_nama' => $value['jenispemeriksaanrad_nama'],
                        'kelompokpemeriksaanrad_id' => $value['kelompokpemeriksaanrad_id'],
                    ];
                    unset($value['jenispemeriksaanrad_id']);
                    $value['row'] = $no;
                    $value['empty'] = '';
                    $value['kelompok_nama'] = !empty($value['kelompokpemeriksaanrad_m']['nama_kelompok'])
                        ? $value['kelompokpemeriksaanrad_m']['nama_kelompok']
                        : '';
                    $data[$key] = $value;
                }
                // $data_jenis_pemeriksaan_rad = Yii::$app->cache->get("data-jenis-pemeriksaan-rad-{$instalasi}");
                // if ($data_jenis_pemeriksaan_rad === false) {
                //     Yii::$app->cache->set("data-jenis-pemeriksaan-rad-{$instalasi}", $cache);
                // }

                // Assign result
                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

                // Return result
                return $result;
            }
            else {
                // Assign result
                $result['data'] = $data;
                $result['recordsTotal'] = 0;
                $result['recordsFiltered'] = 0;

                // Return result
                return $result;
            }
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
        $path = Yii::getAlias("@download") . "/jenis_pemeriksaan_lab.pdf";

        // Try catch
        try {
            // Response
            $response = $this->_restMaster->get('jenis-pemeriksaan-rad/export-pdf?'.http_build_query($yiiRestfulParams), ['save_to' => $path]);

            // Download pdf
            return DocoHelpers::downloadPdf($response, $path, 'jenis-pemeriksaan-rad');
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
            $response = $this->_restMaster->get('jenis-pemeriksaan-rad/export-excel?'.http_build_query($yiiRestfulParams));
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

    /**
     * @todo get all data api from backend
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    private function getDataApi()
    {
        try {
            $response = $this->_restMaster->get('pemeriksaan-rad/generate-api');
            $body = json_decode($response->getBody(), true);

            $result = [
                'jenis_pemeriksaan' => $body['response']['jenis-pemeriksaan'],
                'kelompok_pemeriksaan' => $body['response']['kelompok-pemeriksaan'],
            ];

            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return$result;
        }
    }


    public function actionGetKelompokPemeriksaanRad()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $params = '';
        if ($request->post()) {
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
            $params = '?id='.$parent_label;
        }

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restMaster->get('kelompok-pemeriksaan-rad/get-kelompok-pemeriksaan-rad' . $params);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value)
                $result['output'][] = [
                    'id' => $value['kelompokpemeriksaanrad_id'],
                    'name' => $value['nama_kelompok']
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

    public function actionGetJenisPemeriksaanRad()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restMaster->get('jenis-pemeriksaan-rad/get-jenis-pemeriksaan-rad?id='.$parent_label);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value)
                $result['output'][] = [
                    'id' => $value['jenispemeriksaanrad_id'],
                    'name' => $value['jenispemeriksaanrad_nama']
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