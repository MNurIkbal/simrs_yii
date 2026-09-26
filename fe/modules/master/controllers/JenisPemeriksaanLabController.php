<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-24 11:33:43
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-04-24 15:11:32
 */

namespace Doco\master\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\JenisPemeriksaanLabForm;
use GuzzleHttp\Exception\RequestException;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;

class JenisPemeriksaanLabController extends DocoController
{
    protected $_restMaster;

    public function init()
    {
        parent::init();

        $this->_restMaster = Yii::$app->docoRest->master;
    }

    // Behaviors
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        unset($behaviors['access']);
        unset($behaviors['verbs']);

        return $behaviors;
    }

    // Action index
    public function actionIndex()
    {
        $kelompokPemeriksaanLab = $this->getKelompokPemeriksaanLab();
        return $this->render('index', get_defined_vars());
    }

    private function getKelompokPemeriksaanLab()
    {
        try {
            $response = $this->_restMaster->get('allow/get-kelompok-pemeriksaan');
            $getBody = json_decode($response->getBody(), TRUE);
            $result = $getBody['response']['kelompok_pemeriksaan_lab'];
            return $result;
        } catch (Exception $e) {
            return [];
        }
    }

  
    public function actionCreate()
    {
        $instalasi = Yii::$app->docoVars->workspace("instalasi_id");
        $kelompokPemeriksaanLab = $this->getKelompokPemeriksaanLab();

        $request = Yii::$app->request;
        $model = new JenisPemeriksaanLabForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);

        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('jenis-pemeriksaan-lab/create', [
                        'form_params' => $model->attributes
                    ]);
                    $body = json_decode($response->getBody(), TRUE);
                    return DocoHelpers::response($body);

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
            return $this->renderPartial('form', get_defined_vars());
        }
    }

    // Action update
    public function actionUpdate($id = null)
    {
        $instalasi = Yii::$app->docoVars->workspace("instalasi_id");
        // Declare some variables
        $kelompokPemeriksaanLab = [];

        // Get response
        $response = $this->_restMaster->get('kelompok-pemeriksaan-lab/index');
        $response = json_decode($response->getBody(), TRUE);

        // Check data
        if (!empty($response['response']['data'])) {
            // Assign into array
            $kelompokPemeriksaanLab = ArrayHelper::map($response['response']['data'], 'kelompokpemeriksaanlab_id', 'nama_kelompok');
        }

        // Get request
        $request = Yii::$app->request;
        $model = new JenisPemeriksaanLabForm;
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
                    $response = $this->_restMaster->put('jenis-pemeriksaan-lab/update?id='.$id, [
                        'form_params' => $model->attributes
                    ]);
                    // Yii::$app->cache->delete("data-jenis-pemeriksaan-lab-{$instalasi}");

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
            $response = $this->_restMaster->get('jenis-pemeriksaan-lab/view?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'];
            $model->attributes = $attributes;

            // Return
            return $this->renderPartial('form', get_defined_vars());
        }
    }

    public function actionDelete()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restMaster->delete('jenis-pemeriksaan-lab/delete',[
                            'query' => [
                                'id' => $id 
                            ]
                        ]);
            $getBody = json_decode($response->getBody(),true);
            return DocoHelpers::response($getBody);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
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
            $response = $this->_restMaster->get('jenis-pemeriksaan-lab/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);

            // Start number
            $no = $request->get('start', 1);

            // Check body response
            if (!empty($body['response']['data'])) {
                // Loop
                foreach ($body['response']['data'] as $key => $value) {
                    // Manage data for datatables
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['jenispemeriksaanlab_id']);
                    $value['primary'] = $primaryKey;
                    $cache[] = [
                        'jenispemeriksaanlab_id' => $value['jenispemeriksaanlab_id'],
                        'jenispemeriksaanlab_kode' => $value['jenispemeriksaanlab_kode'],
                        'jenispemeriksaanlab_nama' => $value['jenispemeriksaanlab_nama'],
                        'kelompokpemeriksaanlab_id' => $value['kelompokpemeriksaanlab_id'],
                    ];
                    unset($value['jenispemeriksaanlab_id']);
                    $value['row'] = $no;
                    $value['empty'] = '';
                    $value['kelompok_nama'] = !empty($value['kelompokpemeriksaanlab_m']['nama_kelompok'])
                        ? $value['kelompokpemeriksaanlab_m']['nama_kelompok']
                        : '';
                    $data[$key] = $value;
                }
                // $data_jenis_pemeriksaan_lab = Yii::$app->cache->get("data-jenis-pemeriksaan-lab-{$instalasi}");
                // if ($data_jenis_pemeriksaan_lab === false) {
                //     Yii::$app->cache->set("data-jenis-pemeriksaan-lab-{$instalasi}", $cache);
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
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
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
            $response = $this->_restMaster->get('jenis-pemeriksaan-lab/export-pdf?'.http_build_query($yiiRestfulParams), ['save_to' => $path]);

            // Download pdf
            return DocoHelpers::downloadPdf($response, $path, 'jenis-pemeriksaan-lab');
        } catch (RequestException $e) {
            // Exception
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            // Exception
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $url = 'jenis-pemeriksaan-lab/export-excel?'.http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/Master Jenis Pemeriksaan Lab.xlsx";
        try {
            $response = $this->_restMaster->get($url, [
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);

            return DocoHelpers::downloadFile($path, true);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    /**
     * @todo get all data api from backend
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    private function getDataApi()
    {
        try {
            $response = $this->_restMaster->get('pemeriksaan-lab/generate-api');
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

    public function actionGetKelompokPemeriksaanLab()
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
            $response = $this->_restMaster->get('kelompok-pemeriksaan-lab/get-kelompok-pemeriksaan-lab' . $params);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value)
                $result['output'][] = [
                    'id' => $value['kelompokpemeriksaanlab_id'],
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

    public function actionGetJenisPemeriksaanLab()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restMaster->get('jenis-pemeriksaan-lab/get-jenis-pemeriksaan-lab?id='.$parent_label);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value)
                $result['output'][] = [
                    'id' => $value['jenispemeriksaanlab_id'],
                    'name' => $value['jenispemeriksaanlab_nama']
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