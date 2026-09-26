<?php
// Author : Ramdhan Nurrachman

namespace Doco\kasir\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class ModalSearchController extends DocoController
{
    protected $_title = "Modal Search";
    protected $_module = 'master/modal-search/';
    protected $_restGudang;
    protected $_restMaster;
    
    public function beforeAction($action)
    {
        return true;
    }

    public function init()
    {
        parent::init();
        $this->_restGudang = Yii::$app->docoRest->gudang;
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actionGetDataSupplier($sel_wrap = ".filter-form", $assign_id = "")
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
            $response = $this->_restMaster->get('supplier/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                    'class' => 'btn btn-success btn-xs data-check',
                    'data-sel_wrap' => $sel_wrap,
                    'data-key' => $assign_id? $value['mutasibarang_id'] : $value['nomutasi_barang'],
                    'data-label' => $value['nomutasi_barang'],
                    'title' => \Yii::t('fe', 'Klik'),
                ]);

                $value['tgl_mutasibarang'] = date("j M Y", strtotime($value['tgl_mutasibarang']));
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

    public function actionModalSupplier($sel_wrap = ".filter-form", $assign_id = "")
    {
        return $this->renderPartial('modal-supplier', get_defined_vars());
    }
}
