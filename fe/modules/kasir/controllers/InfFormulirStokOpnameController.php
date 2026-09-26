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

class InfFormulirStokOpnameController extends DocoController
{
    protected $_title = "Informasi Formulir Stok Opname";
    protected $_module = 'kasir/inf-formulir-stok-opname/';
    protected $_restKasir; protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restKasir = Yii::$app->docoRest->kasir; $this->_restMaster = Yii::$app->docoRest->master;
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
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
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
            $response = $this->_restKasir->get('inf-formulir-stok-opname/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['formulirstokopname_id']);
                unset($value['formulirstokopname_id']);

                $value['detail'] = Html::button('<i class="fa fa-list" aria-hidden="true"></i>', [
                    'class' => 'btn btn-primary btn-xs data-detail',
                    'action' => Url::home().$this->_module.'detail?parent_id='.$primaryKey,
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_backdrop_full',
                    'data-popup' => "tooltip", 'data-placement' => 'bottom',
                    'title' => \Yii::t('fe', 'Pembayaran'),
                ]);

                $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                    'class' => 'btn btn-success btn-xs data-check',
                    'data-value' => $value['noformulir'],
                    'title' => \Yii::t('fe', 'Klik'),
                ]);
                
                $value['tglformulir'] = date("j M Y", strtotime($value['tglformulir']));

                $value['rowNum'] = $no; $value['primary'] = $primaryKey;
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

    public function actionGetDataDetail($parent_id)
    {
        $parent_id = DocoHelpers::decrypt($parent_id);
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
            $response = $this->_restKasir->get('inf-formulir-stok-opname/detail?parent_id='.($parent_id).'&'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['obatalkes_m']['tglkadaluarsa'] = date("j M Y", strtotime($value['obatalkes_m']['tglkadaluarsa']));
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

    public function actionDetail($parent_id)
    {
        return $this->renderPartial('detail', get_defined_vars());
    }
}
