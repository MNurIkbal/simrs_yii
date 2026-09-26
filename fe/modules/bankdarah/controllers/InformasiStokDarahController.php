<?php

/**
 * @Author: Tri Anggoro
 * @Date:   2019-02-19
 * @Last Branch:   feature/bankdarah-kartu-stok
 */
namespace Doco\bankdarah\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DHtml;

class InformasiStokDarahController extends DocoController
{
	public $_title = "Informasi Stok Darah";
    protected $_restBankDarah;
    protected $_restMaster;
    protected $_module = '/bankdarah/informasi-stok-darah/';

    public function init()
    {
        parent::init();
        $this->_restBankDarah = Yii::$app->docoRest->bankdarah;
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $lookup = [];

        try {
            $response = $this->_restBankDarah->get('allow/get-golongan-jenis-darah');
            $lookup = json_decode($response->getBody(), true)["response"];
        } catch (Exception $e) {
            $lookup = [];
        }

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw',1);
        $data = [];
        try {
            $response = $this->_restBankDarah->get('informasi-stok-darah/', [
                'query' => $filter
            ]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            $data = [];
            foreach ($body['response']['data'] as $key => $value)
            {
                $no++;
                $value['rowNum'] = $no;

                $primaryKey = DocoHelpers::encrypt($value['stokdarahr_id']);
                $value['primary'] = $primaryKey;
                $value['tgl_kadaluarsa'] = date("d-M-Y", strtotime($value["tgl_kadaluarsa"]));
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionDetail($id)
    {
        $module = $this->_module;
        $title = $this->_title;
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);

        return $this->render("detail", get_defined_vars());
    }

    public function actionGetDetail($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $draw = $request->get('draw',1);
        $query = ["id" => $id];
        $data = [];
        try {
            $response = $this->_restBankDarah->get('informasi-stok-darah/detail', [
                'query' => $query
            ]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            $data = [];
            foreach ($body['response']['data'] as $key => $value)
            {
                $no++;
                $value['rowNum'] = $no;

                $primaryKey = DocoHelpers::encrypt($value['stokdarah_id']);
                $value['primary'] = $primaryKey;
                $value['tgl_kadaluarsa'] = date("d-M-Y", strtotime($value["tgl_kadaluarsa"]));
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionExportExcel($id)
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/laporan-stok-darah.xlsx";
        $id = DocoHelpers::decrypt($id);
        $query = ["id" => $id];
        try {
            $response = $this->_restBankDarah->get('informasi-stok-darah/export-excel',[
                'query' => $query,
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e){
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExcel()
    {
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw',1);

        $path = Yii::getAlias("@download") . "/laporan-stok-darah.xlsx";

        try {
            $response = $this->_restBankDarah->get('informasi-stok-darah/excel',[
                'query' => $query,
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e){
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

}