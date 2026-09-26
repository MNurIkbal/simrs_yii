<?php
// Author : Rizal Faidin

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\SpesialisForm;
use app\modules\master\models\SpesialisPegawaiForm;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class SpesialisController extends DocoController
{
    protected $_title = 'Spesialis';
    protected $_titleSpesialis = 'Spesialis';
    protected $_titleSpesialisPegawai = 'Dokter Spesialis';
    protected $_module = '/master/spesialis/';
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
        $subtitle_spesialis = $this->_titleSpesialis;
        $subtitle_pegawai = $this->_titleSpesialisPegawai;
        
        return $this->render('index', get_defined_vars());
    }

    public function actionPageSpesialis() 
    {
        return $this->renderPartial('_spesialis', get_defined_vars());
    }

    public function actionPageSpesialisPegawai() 
    {
        return $this->renderPartial('_spesialisPegawai', get_defined_vars());
    }

    public function actionGetDataSpesialis()
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

            $response = $this->_restMaster->get('spesialis', [
                'form_params' => [],
                'query' => $yiiRestfulParams
            ]);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['spesialis_id']);
                $value['primary'] = $primaryKey;
                unset($value['spesialis_id']);

                $value['rowNum'] = $no;
                $value['is_active'] = DocoHelpers::isActive($value['is_active']);
                
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionCreateSpesialis()
    {
        $model = new SpesialisForm;
        $title = \Yii::t('fe', 'Tambah').' '.\Yii::t('fe', $this->_titleSpesialis);
        $request = Yii::$app->request;

        if ($request->post()) {
            $post = $request->post();
            $model->load($post);

            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('spesialis/create', [
                        'form_params' => $model->attributes
                    ]);
                    $response = json_decode($response->getBody(),true);

                    return DocoHelpers::response($response, false, 'SpesialisForm');
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), 'SpesialisForm');
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                $response = $model->errors;
                return DocoHelpers::response($response, 422, 'SpesialisForm');
            }
        } else {
            return $this->renderPartial('form_spesialis', get_defined_vars());
        }
    }

    public function actionUpdateSpesialis($id)
    {
        try {
            $title = Yii::t('fe', 'Ubah') . ' ' . Yii::t('fe', $this->_titleSpesialis);
            $model = new SpesialisForm;
            $request = Yii::$app->request;
            $id = DocoHelpers::decrypt($id);

            if ($request->post()) {
                $model->load($request->post());
                if ($model->validate()) {
                    $response = $this->_restMaster->post('spesialis/update', [
                        'query' => ['id' => $id],
                        'form_params' => $model->attributes
                    ]);
                    $response = json_decode($response->getBody(),true);

                    return DocoHelpers::response($response, false, 'SpesialisForm');
                } else {
                    $response = $model->errors;
                    return DocoHelpers::response($response, 422, 'SpesialisForm');
                }
            } else {
                $spesialisRequest = $this->_restMaster->get('spesialis/get-spesialis-by-id',[
                    'query' => [
                        'id' => $id
                    ]
                ]);
                $body = json_decode($spesialisRequest->getBody(),TRUE);

                if (isset($body['response'])) {
                    $spesialis = $body['response'];
                    $model->attributes = $spesialis;
                    $model->is_active = $model->is_active == true ? 1 : 0;

                    return $this->renderPartial('form_spesialis', get_defined_vars());
                } else {
                    return $this->actionCreateSpesialis();
                }
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restMaster->delete('spesialis/delete?id='.$id);
            $response = json_decode($response->getBody(),true);
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];

            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(), 
                json_decode($e->getResponse()->getBody()->getContents())->message, [
            ]);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionGetDataSpesialisPegawai()
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
            $response = $this->_restMaster->get('spesialis/get-spesialis-pegawai?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['primary'] = DocoHelpers::encrypt($value['pegawai_id']);
                $value['rowNum'] = $no;

                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionUpdateSpesialisPegawai($id)
    {
        try {
            $title = Yii::t('fe', 'Ubah') . ' ' . Yii::t('fe', $this->_titleSpesialisPegawai);
            $request = Yii::$app->request;
            $model = new SpesialisPegawaiForm;
            $id = DocoHelpers::decrypt($id);
            
            if ($request->post()) {
                $post = $request->post();
                $model->load($request->post());

                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'spesialis/update-spesialis-pegawai',[
                        'query' => ['id' => $id],
                        'form_params' => $model->attributes
                    ]);
                    $response = json_decode($response->getBody(),true);

                    return DocoHelpers::response($response, false, 'SpesialisPegawaiForm');
                } else {
                    $response = $model->errors;
                    return DocoHelpers::response($response, 422, 'SpesialisPegawaiForm');
                }
            } else {
                $result = $this->_restMaster->get('spesialis/pack-spesialis-pegawai',[
                    'query' => [
                        'id' => $id
                    ]
                ]);
                $body = json_decode($result->getBody(),TRUE);

                $listSpesialis = ArrayHelper::map($body['response']['list_spesialis'], 'spesialis_id', 'spesialis_nama');
                if (isset($body['response']['pegawai'])) {
                    $spesialisPegawai = $body['response']['pegawai'];
                    $model->attributes = $spesialisPegawai;

                    return $this->renderPartial('form_spesialisPegawai', get_defined_vars());
                } else {
                    $message = 'Data tidak ditemukan';
                    return DocoHelpers::response(['message' => $message]);
                }
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }
}
