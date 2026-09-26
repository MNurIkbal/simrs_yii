<?php
// Author : JohnDoe

namespace Doco\rajal\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\InfPenerimaanForm;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class InfPenerimaanController extends DocoController
{
	protected $_title = 'Informasi Penerimaan Obat Alkes';
    protected $_module = '/rajal/inf-penerimaan/';
    protected $_workspace;
    protected $_ruangan_id;
    protected $_restRajal;

    public function init()
    {
        parent::init();
        $this->_workspace = Yii::$app->session->get('active_workspace');
        $this->_ruangan_id = $this->_workspace['ruangan_id'];
        $this->_restRajal = Yii::$app->docoRest->rajal;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actionObatAlkes()
    {
        $title = $this->_title;
        $api = $this->_restRajal->get('inf-penerimaan/generate-api');
        $api = json_decode($api->getBody(), True);
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        $ruangan_id = $this->_ruangan_id;
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
            $response = $this->_restRajal->get('inf-penerimaan/obat-alkes?ruangan_id=' . $ruangan_id . '&' . http_build_query($yiiRestfulParams), ['form_params' => []]);

            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);

            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['primary'] = $value['terimamutasiobat_id'];
                $value['tglterima'] = DocoHelpers::display_label($value['tglterima'], true,
                    date('d M Y', strtotime($value['tglterima'])));

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

    public function actionGetDataDetail($id)
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
            $response = $this->_restRajal->get('inf-penerimaan/detail?id=' . $id . '&' . http_build_query($yiiRestfulParams), ['form_params' => []]);

            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['primary'] = $value['terimamutasiobat_id'];
                $value['rowNum'] = $no;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['count'];
            $result['recordsFiltered'] = $body['response']['count'];
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
        $title = 'Detail Penerimaan Obat Alkes';
        try {
            $request = Yii::$app->request;
            $get = $request->get();
            $response = $this->_restRajal->request('GET', 'inf-penerimaan/view', [
                            'query' => ['id' => $id ]
                        ]);
            $body = json_decode($response->getBody(),true);
            $response = $body['response'];
            return $this->render('view', get_defined_vars());
        } catch (RequestException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function actionDelete($id)
    {
        try {
            $response = $this->_restRajal->request('DELETE', 'inf-penerimaan/delete',[
                            'query' => ['id' => $id ]
                        ]);
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
