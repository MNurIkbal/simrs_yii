<?php 
/**
 * @author : Budi
 * @description : Controller RM Pemesanan Obat Alkes
 * @date : 15 Januari 2018 
 */

namespace Doco\rm\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class PemesananObatController extends DocoController
{
	protected $_title;
    protected $_restRm;
    protected $_module = '/rm/pemesanan-obat/';

    public function init()
    {
        parent::init();

        $this->_title = Yii::t('fe', 'Update Pemesanan Obat Alkes');
        $this->_restRm = Yii::$app->docoRest->rm;
    }

    public function actionIndex()
    {
    	$title = $this->_title;
        $url_search_popup = $this->_module.'popup';
        $instalasi = [];
        $ruangan = [];
        
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
            // $response = $this->_restMaster->get('cara-bayar/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            // $body = json_decode($response->getBody(), True);
            $body = [];
            $no = $request->get('start',1);
            // foreach ($body as $key => $value) {
                $no++;
                // $primaryKey = DocoHelpers::encrypt($value['carabayar_id']);
                // unset($value['carabayar_id']);

                $value['nama_obat'] = 'Paracetamol';
	        	$value['jumlah_pemesanan'] = '100';
	        	$value['aksi'] = Html::a(
                    '<i class="fa fa-trash" aria-hidden="true"></i>', '#', [
                        'class' => 'btn btn-danger btn-xs data-delete',
                        'action' => Url::home().$this->_module.'delete?id=1',
                        'data-popup' => "tooltip",
                        'title' => \Yii::t('fe', 'Hapus'),
                    ]
                );
                $value['rowNum'] = 1;
                // $data[$key] = $value;
                $data[] = $value;
            // }

            $result['data'] = $data;
            // $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            // $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            $result['recordsTotal'] = 1;
            $result['recordsFiltered'] = 1;
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionPopup()
    {
    	$title = Yii::t('fe', 'Obat Alkes');
        return $this->renderPartial('popup_obat', get_defined_vars());
    }

    public function actionGetDataObat()
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
            // $response = $this->_restMaster->get('cara-bayar/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            // $body = json_decode($response->getBody(), True);
            $body = [];
            $no = $request->get('start',1);
            // foreach ($body as $key => $value) {
                $no++;
                // $primaryKey = DocoHelpers::encrypt($value['carabayar_id']);
                // unset($value['carabayar_id']);

                $value['nama_obat'] = 'Paracetamol';
	        	$value['jenis_obat'] = 'Generik';
	        	$value['satuan_kecil'] = 'Tablet';
	        	$value['aksi'] = Html::a('<i class="fa fa-lg fa-check-square-o"></i>', ['#'], [
                    'class' => 'btn btn-success btn-xs select-obat',
                    'data-obat' => 1,
                    'data-tooltip' => 'tooltip',
                    'title' => Yii::t('fe', 'Pilih'),
                ]);

                $value['rowNum'] = 1;
                // $data[$key] = $value;
                $data[] = $value;
            // }

            $result['data'] = $data;
            // $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            // $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            $result['recordsTotal'] = 1;
            $result['recordsFiltered'] = 1;
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