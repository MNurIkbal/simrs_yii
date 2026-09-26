<?php 

namespace Doco\dcms\controllers;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use Doco\dcms\models\JenisKertasForm;

class JenisKertasController extends DocoController
{

    protected $_title = "Jenis Kertas";
    protected $_module = 'dcms/jenis-kertas';
    protected $_restDcms;

    public function init()
    {
        parent::init();
        $this->_restDcms = Yii::$app->docoRest->dcms;
    }

    public function actionIndex()
    {
        $title = "Jenis Kertas";
        $model = new JenisKertasForm;
        return $this->render('index',get_defined_vars());
    }

    public function actionGetData()
    {
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
            $response = $this->_restDcms->get('jenis-kertas?'.http_build_query($yiiRestfulParams), 
                    [
                        'form_params' => []
                    ]
            );
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['kertas_id']);
                $value['primary'] = $primaryKey;
                unset($value['kertas_id']);

                $value['rowNum'] = $no;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionCreate()
    {
        $title = "Tambah Jenis Kertas";
        $model = new JenisKertasForm;
        $request = Yii::$app->request;
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restDcms->request('POST', 'jenis-kertas/create',[
                                        'form_params' => $model->attributes
                                ]);
                    $body = json_decode($response->getBody(),TRUE);
                    $result = $body;
                } catch (RequestException $e) {
                    $result = $e->getMessage();
                } catch (\Exception $e) {
                    $result = $e->getMessage();
                }
                return DocoHelpers::response($result,false,'JenisKertasForm');
            } else {
                $errors = DocoHelpers::parseError($model->errors,'JenisKertasForm');
                return DocoHelpers::response([
                        'response' => [
                            'data' => $errors
                        ]
                    ],422);
            }
        } else {
            return $this->render('form',get_defined_vars());
        }
    }

    public function actionUpdate($id)
    {
        try {
            $title = 'Update Jenis Kertas';
            $model = new JenisKertasForm;
            $status = $this->_status;
            $request = Yii::$app->request;
            $id = DocoHelpers::decrypt($id);
            if ($request->post()) {
                $model->load($request->post());
                if ($model->validate()) {
                    $response = $this->_restDcms->request('PUT', 'jenis-kertas/update',[
                                        'query' => ['id' => $id],
                                        'form_params' => $model->attributes
                                ]);
                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response,false,'JenisKertasForm');
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'JenisKertasForm');
                    return DocoHelpers::response([
                            'response' => [
                                'data' => $errors
                            ]
                        ],422);
                }
            } else {
                $response = $this->_restDcms->request('GET', 'jenis-kertas/view',[
                                    'query' => ['id' => $id]
                            ]);
                $response = json_decode($response->getBody(),true);
                if (isset($response['response'])) {
                    $model->attributes = $response['response'];
                    return $this->render('form',get_defined_vars());
                } else {
                    return $this->actionCreate();
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
            $response = $this->_restDcms->delete('jenis-kertas/delete?id='.$id);
            $response = json_decode($response->getBody(),true);
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }
}