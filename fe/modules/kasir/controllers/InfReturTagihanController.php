<?php
/**
* Author : Budi
* Last Modified : 15 Feb 2019
*   by Anggoro (tri.anggoro@docotel.com)
*   bracnh : feature/kasir-retur-tagihan-pasien
**/

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

class InfReturTagihanController extends DocoController
{
	protected $_title = "Informasi Retur Tagihan Pasien";
    protected $_module = '/kasir/inf-retur-tagihan/';
    protected $_restKasir;
    protected $_restMaster;

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
        $title = $this->_title;
        $module = $this->_module;
    	$response = $this->_restMaster->get('cara-bayar?advanced-filter[is_active]=1');
        $body = json_decode($response->getBody(), TRUE);
        $cara_bayar = $body['response']['data'];

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
            $response = $this->_restKasir->get('inf-retur-tagihan/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);

            foreach ($body['response']['data'] as $key => $value) {
                $primaryKey = DocoHelpers::encrypt($value['returbayarpelayanan_id']);
                unset($value['returbayarpelayanan_id']);

                $value['primary'] = $primaryKey;
                $value['tgl_returpelayanan'] = date("d-M-Y", strtotime($value['tgl_returpelayanan']));
                $value['total_biayaretur'] = "Rp. ".number_format($value['total_biayaretur'], 0, ",", ".");
                $data[] = $value;
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

    public function actionPrintKwitansi($id)
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/kasir-kwitansi-retur-tagihan-pasien.pdf";
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restKasir->get('inf-retur-tagihan/print-kwitansi', [
                "query" => [
                    "id" => $id
                ],
                'save_to' => $path
            ]);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e);
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e);
        }
    }

    public function actionPrintBkk($id)
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/kasir-bkk-retur-tagihan-pasien.pdf";
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restKasir->get('inf-retur-tagihan/print-bkk', [
                "query" => [
                    "id" => $id
                ],
                'save_to' => $path
            ]);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e);
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e);
        }
    }

    public function actionHapus($id)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restKasir->get("inf-retur-tagihan/hapus", [
                "query" => ["id" => $id]
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response($body);
        } catch (Exception $e) {
            return DocoHelpers::response(['text' => $e->getMessage()]);
        }
    }
}