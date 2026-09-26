<?php


namespace Doco\gudang\controllers;

use Yii;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class LaporanRekapPenerimaanBarangController extends DocoController
{
    public $_title = "Laporan Rekap Penerimaan Barang";
    public $_module = '/gudang/laporan-rekap-penerimaan-barang/';

	public function init() {
        parent::init();
    }

	public function actionIndex()
	{
		$title = $this->_title;
		$module = $this->_module;
		return $this->render('index',get_defined_vars());
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
        	$response = Yii::$app->docoRest->gudang->get('lap-rekap-penerimaan-barang', [
                'query' => $yiiRestfulParams
            ]);
            $body = json_decode($response->getBody(), true);
            
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['total'] = DocoHelpers::formatNumber($value['total']);
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