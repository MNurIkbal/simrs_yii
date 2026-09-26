<?php

namespace Doco\master\controllers;

use Yii;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class UnitPelaksanaTeknisController extends DocoController
{
    protected $_title = "Unit Pelaksana Teknis";
    protected $_module = 'master/unit-pelaksana-teknis/';
    protected $_restMaster;
    protected $allowAction = ['*'];

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access'], $behaviors['verbs']);
        return $behaviors;
    }

    public function actionIndex()
    {
        $status = $this->_status;
        $options = $this->_options;
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);

        $result = [
            'data' => [],
            'draw' => $draw,
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
        ];

        try {
            $response = $this->_restMaster->get('unit-pelaksana-teknis/index?' . http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), true);

            $no = $request->get('start', 0);
            $data = [];
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['upt_id']);
                $value['rowNum'] = $no;
                $value['primary'] = $primaryKey;
                unset($value['upt_id']);
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }

        return $result;
    }
}
