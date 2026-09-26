<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-22 10:57:21
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-22 11:48:50
 */

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
use app\modules\kasir\models\ReturTagihanForm;
use app\components\DocoSelect2Trait;

class ReturTagihanController extends DocoController
{
    use DocoSelect2Trait;
    
	protected $_title = 'Retur tagihan pasien';
    protected $_module = 'kasir/retur-tagihan/';
    protected $_restKasir;
    protected $_restMaster;

	public function init()
    {
        parent::init();
        $this->_restKasir = Yii::$app->docoRest->kasir;
        $this->_restMaster = Yii::$app->docoRest->master;
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
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $konfig = [
            "is_pembulatankeatas" => true,
            "satuanpembulatan" => 500
        ];
        try {
            $result =  $this->_restKasir->get("allow/get-konfig-system");
            $body = json_decode($result->getBody(), 1);
            $konfig = $body["response"];
        } catch (Exception $e) {
            //
        }
        $model = new ReturTagihanForm;
        return $this->render('index', get_defined_vars());
    }

    public function actionSearchKwitansi()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $term = $request->get('term');

            $result = $this->_restKasir->get('inf-retur-tagihan/search-kwitansi',[
                'query' => [
                    'term' => $term
                ]
            ]);
            $result = json_decode($result->getBody(),true);

            $data = $result["response"];
            $transaksi = [];
            foreach ($data as $key => $value) {
                $id = $value['tandabuktibayar_id'];
                $response[] = [
                    'id' => $id,
                    'text' => $value['no_pembayaran'],
                ];
                $transaksi[$id] = $value;
                $transaksi[$id]["tglbuktibayar"] = date("d-M-Y", strtotime($value["tglbuktibayar"]));
            }
        } catch (RequestException $e) {
            $transaksi = [];
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }
        return DocoHelpers::response([
            'result' => $response,
            'transaksi' => $transaksi
        ]);
    }

    public function actionSave()
    {
        $request = Yii::$app->request;
        $model = new ReturTagihanForm;
        $model->load($request->post());

        try {
            if ($model->validate()) {
                $response = $this->_restKasir->post('inf-retur-tagihan/retur-tagihan-pasien',[
                    "form_params" => $model->attributes
                ]);

                $body = json_decode($response->getBody(), true);
                return DocoHelpers::response($body,200);
            }else{
                return DocoHelpers::response($model->errors,422, "ReturTagihanForm");
            }
        } catch (Exception $e) {
            return DocoHelpers::response([
                "message" => $e->getMessage()
            ],false,'ReturTagihanForm');
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

}

