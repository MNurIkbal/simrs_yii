<?php

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\SatuanKonversiBarangForm;
use GuzzleHttp\Exception\RequestException;

class SatuanKonversiBarangController extends DocoController
{
    protected $_title = "Satuan Konversi Barang";
    protected $_module = '/master/satuan-konversi-barang/';
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
        $title = $this->_title;
        $satuan = $this->getSatuan();
        
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = $cache = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restMaster->get('satuan-konversi-barang/list-barang', 
                [
                    'query' => $yiiRestfulParams
                ]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            if (!empty($body['response']['data'])) {
                foreach ($body['response']['data'] as $key => $value) {
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['barang_id']);
                    $value['primary'] = $primaryKey;
                    unset($value['barang_id']);

                    $value['rowNum'] = $no;
                    $data[$key] = $value;
                }

                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
                return $result;
            } else {
                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
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

    public function actionCreate($id)
    {
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $model = new SatuanKonversiBarangForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $title = 'Tambah Satuan Konversi Barang';
        
        $dataBarang = $this->getBarang($id);
        
        return $this->render('form', get_defined_vars());
    }

    public function actionCreateSatuan($id)
    {
        $request = Yii::$app->request;
        $model = new SatuanKonversiBarangForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $title = 'Tambah Satuan Konversi Barang';
        $model->is_active = 1;
        
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('satuan-konversi-barang/create', [
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
        }
        
        $satuan = $this->getSatuan();
        $dataBarang = $this->getBarang($id);
        
        return $this->renderPartial('form_satuan', get_defined_vars());
    }

    public function actionUpdate($id)
    {
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $model = new SatuanKonversiForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        try {
            if ($request->post()) {
                $model->load($request->post());
                if ($model->validate()) {
                    $response = $this->_restMaster->put('satuan-konversi/update?id='.$id, [
                        'form_params' => $model->attributes
                    ]);
                    return DocoHelpers::responseJsonString($response->getBody(), $formName);
                }else{
                    $errors = DocoHelpers::parseError($model->errors, $formName);
                    return DocoHelpers::responseTemplate(422, 'Error', $errors);
                }
            } else {
                $response = $this->_restMaster->get('satuan-konversi/view', [
                    'query' => [
                        'id' => $id
                    ]
                ]);
                $response = json_decode($response->getBody(),true);
                $response = $response['response'];
                $model->attributes = $response;
                $model->obatalkes_id = $response['obatalkes_id'];
                $model->obatalkes_nama = $response['obatalkes_nama'];
                $satuan = $this->getSatuan();
            }
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
        
        return $this->renderPartial('form', get_defined_vars());
    }

    public function actionDelete($id)
    {
        return DocoHelpers::response([
            'response' => [
                'title' => 'Proses Gagal !',
                'text' => 'Data sudah di gunakan'
            ]
        ],422);
    }

    public function actionSearchBarang()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restMaster->get('allow/get-data-barang', [
                            'query' => [
                                'term' => $request->get('term'),
                            ]
                        ]);
            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];

            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['barang_id'],
                    'text' => $value['barang_nama'],
                    'satuankecil_id' => $value['satuankecil_id'],
                ];
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }
        
        return DocoHelpers::response([
            'result' => $response
        ]);
    }

    private function getSatuan()
    {
        $response = $this->_restMaster->get('allow/list-satuan');
        $body = json_decode($response->getBody(), true);
        $body = $body['response'];

        $satuan = (isset($body) && count($body) > 0) ? 
        ArrayHelper::map($body, 'satuanunit_id', 'satuanunit_nama') : [];

        return $satuan;
    }

    private function getBarang($id)
    {
        $response = $this->_restMaster->get('satuan-konversi-barang/list-barang?advanced-filter[barang_id]='.$id);
        $body = json_decode($response->getBody(), true);
        $body = $body['response'];
        
        return $body['data'][0];
    }

    private function getSatuanUnit($id)
    {
        $response = $this->_restMaster->get('satuan-barang?advanced-filter[satuanunit_id]='.$id);
        $body = json_decode($response->getBody(), true);
        $body = $body['response'];
        
        return $body['data'][0];
    }

    public function actionGetKonversi()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['barang_id'] = $request->get('barang_id');
        $draw = $request->get('draw', 1);
        $data = $cache = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restMaster->get('satuan-konversi-barang/list-konversi', 
                [
                    'query' => $yiiRestfulParams
                ]);

            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            
            if (!empty($body['response']['data'])) {
                foreach ($body['response']['data'] as $key => $value) {
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['satuankonversi_id']);
                    $value['primary'] = $primaryKey;
                    unset($value['satuankonversi_id']);
                    $value['is_active'] = DocoHelpers::switchStatus($value['is_active'], $primaryKey);

                    $value['rowNum'] = $no;
                    $data[$key] = $value;
                }

                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
                return $result;
            } else {
                $result['data'] = $data;
                $result['recordsTotal'] = 0;
                $result['recordsFiltered'] = 0;
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

    public function actionDeleteListItem($obatalkes_id = null, $satuanbesar_id = null)
    {
        $cacheKonversi = Yii::$app->cache->get("konversi");
        if ($cacheKonversi !== false) {
            if (isset($cacheKonversi[$obatalkes_id][$satuanbesar_id])) {
                unset($cacheKonversi[$obatalkes_id][$satuanbesar_id]);
                Yii::$app->cache->set("konversi",$cacheKonversi);
            }
        }
        $response['response'] = [
            'title' => 'Proses Berhasil !',
            'text' => 'Data berhasil dihapus'
        ];
        return DocoHelpers::response($response);
    }

    public function actionChangeStatus($id, $status)
    {
        $id = DocoHelpers::decrypt($id);
        
        // try {
            $response = $this->_restMaster->put('satuan-konversi-barang/update?id='.$id, [
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
        // } catch (RequestException $e) {
        //     $data = [
        //         'title' => \Yii::t('fe', 'Proses gagal ')." !",
        //         'text' => \Yii::t('fe', "Status tidak berhasil dubah.")
        //     ];
        //     return DocoHelpers::responseTemplate(
        //         $e->getResponse()->getStatusCode(), 
        //         json_decode($e->getResponse()->getBody()->getContents())->message,  
        //         [],
        //         $data
        //     );
        // } catch (\Exception $e) {
        //     return DocoHelpers::responseTemplate(500, $e->getMessage());
        // }
    }
}