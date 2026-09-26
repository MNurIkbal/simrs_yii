<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-23 11:59:19
 * @Last Modified by:   Iqbal@docotel.com
 * @Last Modified time: 2019-02-21 14:13:25
 */
namespace Doco\master\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\KelompokPemeriksaanLabForm;
use GuzzleHttp\Exception\RequestException;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;

class KelompokPemeriksaanLabController extends DocoController
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

    public function actionIndex()
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionCreate()
    {
        $instalasi = Yii::$app->docoVars->workspace("instalasi_id");
        $request = Yii::$app->request;
        $model = new KelompokPemeriksaanLabForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    // Get response
                    $response = $this->_restMaster->post('kelompok-pemeriksaan-lab/create', [
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

    public function actionUpdate($id = null)
    {
        $instalasi = Yii::$app->docoVars->workspace("instalasi_id");
        $request = Yii::$app->request;
        $model = new KelompokPemeriksaanLabForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);

        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('kelompok-pemeriksaan-lab/update?id='.$id, [
                        'form_params' => $model->attributes
                    ]);

                    $body = json_decode($response->getBody(), True);
                    return DocoHelpers::response($body);
                } catch (RequestException $e) {
                    return DocoHelpers::response(['message' => $e->getMessage()],500);
                } catch (\Exception $e) {
                    return DocoHelpers::response(['message' => $e->getMessage()],500);
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            $response = $this->_restMaster->get('kelompok-pemeriksaan-lab/view?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'];
            $model->attributes = $attributes;

            return $this->renderPartial('form', get_defined_vars());
        }
    }

    public function actionDelete($id)
    {
        $instalasi = Yii::$app->docoVars->workspace("instalasi_id");
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restMaster->delete('kelompok-pemeriksaan-lab/delete',[
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
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = $cache = [];

        try {
            $response = $this->_restMaster->get('kelompok-pemeriksaan-lab/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['kelompokpemeriksaanlab_id']);
                $value['primary'] = $primaryKey;
                $cache[] = [
                    'kelompokpemeriksaanlab_id' => $value['kelompokpemeriksaanlab_id'],
                    'kode_kelompok' => $value['kode_kelompok'],
                    'nama_kelompok' => $value['nama_kelompok'],
                ];
                unset($value['kelompokpemeriksaanlab_id']);
                $value['rowNum'] = $no;
                $data[$key] = $value;
            }
            // $data_kel_pemeriksaan_lab = Yii::$app->cache->get("data-kel-pemeriksaan-lab-{$instalasi}");
            // if ($data_kel_pemeriksaan_lab === false) {
            //     Yii::$app->cache->set("data-kel-pemeriksaan-lab-{$instalasi}", $cache);
            // }

            $return = [
                'data' => $data,
                'draw' => $request->get('draw'),
                'recordsTotal' => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount']
            ];

            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
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
        $path = Yii::getAlias("@download") . "/kelompok_pemeriksaan_lab.pdf";

        // Try catch
        try {
            // Response
            $response = $this->_restMaster->get('kelompok-pemeriksaan-lab/export-pdf?'.http_build_query($yiiRestfulParams), ['save_to' => $path]);

            // Download pdf
            return DocoHelpers::downloadPdf($response, $path, 'kelompok-pemeriksaan-lab');
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
        $url = 'kelompok-pemeriksaan-lab/export-excel?'.http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/Master Kelompok Pemeriksaan Lab.xlsx";
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
}
?>