<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Pemeriksaan Radiologi
 * @copyright 03 Juli 2018 aweutist
 */

namespace Doco\master\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\PemeriksaanRadForm;
use GuzzleHttp\Exception\RequestException;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;

class PemeriksaanRadController extends DocoController
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
        $daftarTindakan = [];
        $jenisPemeriksaanRad = [];
        $kelompokPemeriksaanRad = [];
        $pemeriksaanRad = [];

        // Get response
        $data = $this->getDataApi();

        if (!empty($data['daftar_tindakan'])) {
            $daftarTindakan = ArrayHelper::map($data['daftar_tindakan'], 'daftartindakan_id', 'daftartindakan_nama');
        }
        if (!empty($data['jenis_pemeriksaan'])) {
            $jenisPemeriksaanRad = ArrayHelper::map($data['jenis_pemeriksaan'], 'jenispemeriksaanrad_id', 'jenispemeriksaanrad_nama');
        }
        if (!empty($data['kelompok_pemeriksaan'])) {
            $kelompokPemeriksaanRad = ArrayHelper::map($data['kelompok_pemeriksaan'], 'kelompokpemeriksaanrad_id', 'nama_kelompok');
        }
        if (!empty($data['pemeriksaan_rad'])) {
            $pemeriksaanRad = ArrayHelper::map($data['pemeriksaan_rad'], 'pemeriksaanrad_nama', 'pemeriksaanrad_nama');
        }

        // Return index
        return $this->render('index', get_defined_vars());
    }

    // Action create
    public function actionCreate()
    {
        $instalasi = Yii::$app->docoVars->workspace("instalasi_id");
        // Declare some variables
        $daftarTindakan = [];
        $jenisPemeriksaanRad = [];
        $kelompokPemeriksaanRad = [];

        // Get response
        $data = $this->getDataApi();

        // Check data
        if (!empty($data['daftar_tindakan'])) {
            // Assign into array
            $daftarTindakan = ArrayHelper::map($data['daftar_tindakan'], 'daftartindakan_id', 'daftartindakan_nama');
        }
        // Check data
        if (!empty($data['jenis_pemeriksaan'])) {
            // Assign into array
            $jenisPemeriksaanRad = ArrayHelper::map($data['jenis_pemeriksaan'], 'jenispemeriksaanrad_id', 'jenispemeriksaanrad_nama');
        }
        // Check data
        if (!empty($data['kelompok_pemeriksaan'])) {
            // Assign into array
            $kelompokPemeriksaanRad = ArrayHelper::map($data['kelompok_pemeriksaan'], 'kelompokpemeriksaanrad_id', 'nama_kelompok');
        }

        // Get request
        $request = Yii::$app->request;
        $model = new PemeriksaanRadForm;
        // $model->scenario = 'insert';
        $formName = substr(strrchr(get_class($model), "\\"), 1);

        // Check post
        if ($request->post()) {
            // Load
            $model->load($request->post());

            // Validate model
            if ($model->validate()) {
                // Get last data
                // NOTE : takut suatu saat butuh
                // $response = $this->_restMaster->get('pemeriksaan-rad/get-last-pemeriksaan');
                // $response = json_decode($response->getBody(), true);

                // Check response
                // if (!empty($response['response'])) {
                //     // Get kode
                //     $lastKode = $response['response']['pemeriksaanrad_kode'];
                //     $explodedString = explode('LAB', $lastKode);
                //     $number = intval($explodedString[1]);
                //     $number++;

                //     // Assign new kode
                //     $model->pemeriksaanrad_kode = 'LAB0'.$number;
                // }
                // else {
                //     // Assign new kode
                //     $model->pemeriksaanrad_kode = 'LAB01';
                // }

                // Try catch
                try {
                    // Get response
                    $response = $this->_restMaster->post('pemeriksaan-rad/create', [
                        'form_params' => $model->attributes
                    ]);
                    // Yii::$app->cache->delete("data-pemeriksaan-rad-{$instalasi}");

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
        $instalasi = Yii::$app->docoVars->workspace("instalasi_id");
        // Declare some variables
        $daftarTindakan = [];
        $jenisPemeriksaanRad = [];
        $kelompokPemeriksaanRad = [];

        // Get response
        $data = $this->getDataApi();
        // Check data
        if (!empty($data['daftar_tindakan'])) {
            // Assign into array
            $daftarTindakan = ArrayHelper::map($data['daftar_tindakan'], 'daftartindakan_id', 'daftartindakan_nama');
        }
        // Check data
        if (!empty($data['jenis_pemeriksaan'])) {
            // Assign into array
            $jenisPemeriksaanRad = ArrayHelper::map($data['jenis_pemeriksaan'], 'jenispemeriksaanrad_id', 'jenispemeriksaanrad_nama');
        }

        // Get request
        $request = Yii::$app->request;
        $model = new PemeriksaanRadForm;
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
                    $response = $this->_restMaster->put('pemeriksaan-rad/update?id='.$id, [
                        'form_params' => $model->attributes
                    ]);
                    // Yii::$app->cache->delete("data-pemeriksaan-rad-{$instalasi}");

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
            $response = $this->_restMaster->get('pemeriksaan-rad/view?id='.$id);
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
            $response = $this->_restMaster->delete('pemeriksaan-rad/delete?id='.$id);
            // Yii::$app->cache->delete("data-pemeriksaan-rad-{$instalasi}");
            // Return
            return DocoHelpers::responseTemplate($response->getStatusCode(), "OK", []);
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
        $draw = $request->get('draw', 1);
        $data = $cache = [];
        $instalasi = Yii::$app->docoVars->workspace("instalasi_id");
        // Initiate data
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        // Try catch
        try {
            // Get response
            $response = $this->_restMaster->get('pemeriksaan-rad/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);

            // Start number
            $no = $request->get('start', 1);

            // Check body response
            if (!empty($body['response']['data'])) {
                // Loop
                foreach ($body['response']['data'] as $key => $value) {
                    // Manage data for datatables
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['pemeriksaanradiologi_id']);
                    $value['primary'] = $primaryKey;
                    $cache[] = [
                        'pemeriksaanradiologi_id' => $value['pemeriksaanradiologi_id'],
                        'pemeriksaanrad_kode' => $value['pemeriksaanrad_kode'],
                        'pemeriksaanrad_nama' => $value['pemeriksaanrad_nama'],
                        'jenispemeriksaanrad_id' => $value['jenispemeriksaanrad_id'],
                        'kelompokpemeriksaanrad_id' => $value['kelompokpemeriksaanrad_id'],
                        'daftartindakan_id' => $value['daftartindakan_id'],
                    ];
                    unset($value['pemeriksaanradiologi_id']);
                    $value['row'] = $no;
                    $value['empty'] = '';
                    $value['kelompok_nama'] = !empty($value['kelompokpemeriksaanrad_m']['nama_kelompok'])
                        ? $value['kelompokpemeriksaanrad_m']['nama_kelompok']
                        : '';
                    $value['kelompokpemeriksaanrad_id'] = !empty($value['kelompokpemeriksaanrad_m']['kelompokpemeriksaanrad_id'])
                        ? $value['kelompokpemeriksaanrad_m']['kelompokpemeriksaanrad_id']
                        : '';
                    $value['jenispemeriksaanrad_nama'] = !empty($value['jenispemeriksaanrad_m']['jenispemeriksaanrad_nama'])
                        ? $value['jenispemeriksaanrad_m']['jenispemeriksaanrad_nama']
                        : '';
                    $value['daftartindakan_nama'] = !empty($value['daftartindakan_m']['daftartindakan_nama'])
                        ? $value['daftartindakan_m']['daftartindakan_nama']
                        : '';
                    $data[$key] = $value;
                }
                // $data_pemeriksaan_rad = Yii::$app->cache->get("data-pemeriksaan-rad-{$instalasi}");
                // if ($data_pemeriksaan_rad === false) {
                //     Yii::$app->cache->set("data-pemeriksaan-rad-{$instalasi}", $cache);
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

    // Action get data
    public function actionGetKelompokByJenisId()
    {
        // Get request
        $id = Yii::$app->request->get('id');

        // Try catch
        try {
            // Get response
            $response = $this->_restMaster->get('jenis-pemeriksaan-rad/get-kelompok-by-jenis-id?id='.$id, ['form_params' => []]);
            $body = json_decode($response->getBody(), true);

            // Echo
            return isset($body['response']) ? json_encode($body['response']) : '';
        } catch (RequestException $e) {
            // Error result
            $result['error'] = $e->getMessage();

            // Return result
            echo $result;
        } catch (\Exception $e) {
            // Error result
            $result['error'] = $e->getMessage();

            // Return result
            echo $result;
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
        $path = Yii::getAlias("@download") . "/pemeriksaan_lab.pdf";

        // Try catch
        try {
            // Response
            $response = $this->_restMaster->get('pemeriksaan-rad/export-pdf?'.http_build_query($yiiRestfulParams), ['save_to' => $path]);

            // Download pdf
            return DocoHelpers::downloadPdf($response, $path, 'pemeriksaan-rad');
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
            $response = $this->_restMaster->get('pemeriksaan-rad/export-excel?'.http_build_query($yiiRestfulParams));
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
                'daftar_tindakan' => $body['response']['daftar-tindakan'],
                'jenis_pemeriksaan' => $body['response']['jenis-pemeriksaan'],
                'kelompok_pemeriksaan' => $body['response']['kelompok-pemeriksaan'],
                'pemeriksaan_rad' => $body['response']['pemeriksaan-rad'],
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
}
?>