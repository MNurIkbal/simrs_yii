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
use yii\helpers\ArrayHelper;

class InfClosingKasirController extends DocoController
{
    protected $_title = "Informasi Closing Kasir";
    protected $_module = 'kasir/inf-closing-kasir/';
    protected $_nama_ruangan;
    protected $_restKasir; protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_nama_ruangan = Yii::$app->docoVars->workspace('ruangan_name') ? Yii::$app->docoVars->workspace('ruangan_name') : '';
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
        // $response = $this->_restMaster->get('instalasi?advanced-filter[is_active]=1');
        // $body = json_decode($response->getBody(), TRUE);
        // $instalasi = $body['response']['data'];
        $request = $this->_restKasir->get('inf-closing-kasir/list-request');
        $request = json_decode($request->getBody(), True);
        $request = $request['response'];
        
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
            $response = $this->_restKasir->get('inf-closing-kasir/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);
            // var_dump(DocoHelpers::response($body));die;
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['closingkasir_id']);
                unset($value['closingkasir_id']);

                $value['detail'] = Html::button('<i class="fa fa-list" aria-hidden="true"></i>', [
                    'class' => 'btn btn-primary btn-xs data-detail',
                    'action' => Url::home().$this->_module.'view?id='.$primaryKey,
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_backdrop',
                    'data-popup' => "tooltip", 'data-placement' => 'bottom',
                    'title' => \Yii::t('fe', 'Detail'),
                ]);

                $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                    'class' => 'btn btn-success btn-xs data-check',
                    'data-value' => $value['no_struksetor'],
                    'title' => \Yii::t('fe', 'Klik'),
                ]);

                $value['tgl_closingkasir'] = date("j M Y", strtotime($value['tgl_closingkasir']));

                $value['rowNum'] = $no; 
                $value['primary'] = $primaryKey;
                $value['total_setoran'] = DocoHelpers::formatNumber($value['total_setoran']);
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

    public function actionDetail($id)
    {
        try {
            $title = $this->_nama_ruangan;
            $request = Yii::$app->request;
            $get = $request->get();
            $id = DocoHelpers::decrypt($id);
            $response = $this->_restKasir->request('GET', 'inf-closing-kasir/header', [
                            'query' => ['id' => $id ]
                        ]);
            $body = json_decode($response->getBody(), True);
            $response_header = $body['response'];
            return $this->render('detail', get_defined_vars());
        } catch (RequestException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function actionGetDataHeader($id)
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
            $response = $this->_restKasir->get('inf-closing-kasir/rincian-closing?id='.$id.'&'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response'] as $key => $value) {
                $no++;

                $value['tgl_pembayaran'] = ArrayHelper::getValue($value,'tgl_pembayaran','');
                $value['total_tagihan'] = ArrayHelper::getValue($value,'total_tagihan');
                $value['total_tunai'] = ArrayHelper::getValue($value,'total_tunai');
                $value['total_non_tunai'] = ArrayHelper::getValue($value,'total_non_tunai');
                $value['total_dijamin'] = ArrayHelper::getValue($value,'total_dijamin');
                $value['no_pendaftaran'] = ArrayHelper::getValue($value,'no_pendaftaran','');
                $value['nama_pasien'] = ArrayHelper::getValue($value,'nama_pasien','');
                $value['carabayar_nama'] = ArrayHelper::getValue($value,'carabayar_nama','');
                $value['penjamin_nama'] = ArrayHelper::getValue($value,'penjamin_nama','');
                $value['no_rekam_medik'] = ArrayHelper::getValue($value,'no_rekam_medik','');

                $value['rowNum'] = $no;
                $value['tgl_pembayaran'] = date('d-M-Y H:i:s',strtotime($value['tgl_pembayaran']));
                $value['total_tagihan'] = DocoHelpers::formatNumber(ceil($value['total_tagihan']));
                $value['total_tunai'] = DocoHelpers::formatNumber(ceil($value['total_tunai']));
                $value['total_non_tunai'] = DocoHelpers::formatNumber(ceil($value['total_nontunai']));
                $value['total_dijamin'] = DocoHelpers::formatNumber(ceil($value['total_dijamin']));
                // $value['total_tagihan'] = DocoHelpers::formatNumber($value['total_tagihan']);
                // $value['total_tunai'] = DocoHelpers::formatNumber($value['total_tunai']);
                // $value['total_non_tunai'] = DocoHelpers::formatNumber($value['total_nontunai']);
                // $value['total_dijamin'] = DocoHelpers::formatNumber($value['total_dijamin']);
                $value['no_pendaftaran'] = $value['no_pendaftaran'].'<br/>'.$value['nama_pasien'].'<br/>'.$value['no_rekam_medik'];
                $value['carabayar_nama'] = $value['carabayar_nama'].'<br/>'.$value['penjamin_nama'];
                $value['metode_pembayaran_nama'] = !empty($value['metode_pembayaran_nama']) ? $value['metode_pembayaran_nama'] : '-';
                // $value['total_terbayar'] = DocoHelpers::formatNumber($value['total_terbayar']);
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = count($body['response']);
            $result['recordsFiltered'] = count($body['response']);
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
            $response = $this->_restKasir->get('inf-closing-kasir/detail?id='.$id.'&'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['nilaiuang'] = DocoHelpers::formatNumber($value['nilaiuang']);
                $value['jumlahuang'] = DocoHelpers::formatNumber($value['jumlahuang']);
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = count($body['response']);
            $result['recordsFiltered'] = count($body['response']);
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionExportPdf($id)
    {
        $path = Yii::getAlias("@download") . "/closing_kasir.pdf";

        $response = $this->_restKasir->get('inf-closing-kasir/cetak',[
            'query' => [
                'id' => $id,
                'nama_ruangan' => $this->_nama_ruangan
            ],
            'save_to' => $path
        ]);

        $body = json_decode($response->getBody(),true);
        return DocoHelpers::previewPdf($path);
    }

    public function actionExportExcel($id)
    {
        $url = "inf-closing-kasir/export-excel";
        $path = Yii::getAlias("@download") . "/detail-closing-kasir.xlsx";
        try {
            $response = $this->_restKasir->get($url,[
                'query' => [
                    'id' => $id,
                    'nama_ruangan' => $this->_nama_ruangan
                ],
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), True);
            return DocoHelpers::downloadFile($path,true);
        } catch (RequestException $e) {
            throw new \yii\web\NotFoundHttpException();
            return $e;
        } catch (\Exception $e) {
            throw new \yii\web\NotFoundHttpException();
            return $e;
        }
    }
}
