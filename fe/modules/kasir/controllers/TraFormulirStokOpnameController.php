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

class TraFormulirStokOpnameController extends DocoController
{
    protected $_title = "Transaksi Formulir Stok Opname";
    protected $_module = 'kasir/tra-formulir-stok-opname/';
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
        $response = $this->_restMaster->get('instalasi?advanced-filter[is_active]=1');
        $body = json_decode($response->getBody(), TRUE);
        $instalasi = $body['response']['data'];
        
        $response = $this->_restMaster->get('ruangan?advanced-filter[is_active]=1');
        $body = json_decode($response->getBody(), TRUE);
        $ruangan = $body['response']['data'];
    
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
            $response = $this->_restKasir->get('inf-stok-obat-alkes/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['periodestok_id']);
                unset($value['periodestok_id']);

                $value['detail'] = Html::button('<i class="fa fa-list" aria-hidden="true"></i>', [
                    'class' => 'btn btn-primary btn-xs data-detail',
                    'action' => Url::home().$this->_module.'view?id='.$primaryKey,
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_backdrop',
                    'data-popup' => "tooltip", 'data-placement' => 'bottom',
                    'title' => \Yii::t('fe', 'Rincian Tagihan'),
                ]);

                $value['payment'] = Html::button('<i class="fa fa-shopping-cart" aria-hidden="true"></i>', [
                    'class' => 'btn btn-primary btn-xs data-payment',
                    'action' => Url::home().$this->_module.'view?id='.$primaryKey,
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_backdrop',
                    'data-popup' => "tooltip", 'data-placement' => 'bottom',
                    'title' => \Yii::t('fe', 'Pembayaran'),
                ]);

                $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                    'class' => 'btn btn-success btn-xs data-check',
                    'data-value' => $value['obatalkes_namalain'],
                    'title' => \Yii::t('fe', 'Klik'),
                ]);

                $value['tglperiodestok_awal'] = date("j M Y", strtotime($value['tglperiodestok_awal']));
                $value['tglperiodestok_akhir'] = date("j M Y", strtotime($value['tglperiodestok_akhir']));
                $value['form_stok_fisik'] = '<input name="" class="input-sm form-control" />';
                $value['form_kondisi'] = '<input name="" class="input-sm form-control" />';
                $value['form_kadaluarsa'] = '<input name="" class="input-sm form-control daterange-single" />';

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
}
