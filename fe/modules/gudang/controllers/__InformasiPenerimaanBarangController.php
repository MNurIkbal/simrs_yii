<?php

namespace Doco\gudang\controllers;
/**
* @author yaya
* @since 22 March 2018
*/

use Yii;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;

class InformasiPenerimaanBarangController extends DocoController
{
    public $_title = "Informasi Penerimaan Barang";
    protected $_module = '/gudang/informasi-penerimaan-barang';
    protected $_restGudang;

    public function init()
    {
        parent::init();
        $this->_restGudang = Yii::$app->docoRest->gudang;
        $this->_title = Yii::t("fe", $this->_title);
    }

    public function actionIndex()
    {
        $instalasi = $ruangan = [];
        try {
            $response = $this->_restGudang->get('informasi-penerimaan-barang/get-fillter',[]);
            $response = json_decode($response->getBody(),true);
            $instalasi = $response['response']['instalasi'];
            $ruangan = $response['response']['ruangan'];
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
        } catch (\Exception $e) {
            Yii::info($e->getMessage());
        }

        return $this->render('index',get_defined_vars());
    }

    public function actionGetData()
    {
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
            $response = $this->_restGudang->get('informasi-penerimaan-barang/index', 
                    [
                        'query' => http_build_query($yiiRestfulParams)
                    ]
            );
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['terimamutasibarang_id']);
                unset($value['terimamutasibarang_id']);
                $value['tglterima'] = date("j M Y", strtotime($value['tglterima']));

                $value['rowNum'] = $no; 
                $value['primary'] = $primaryKey;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionPreview($id)
    {
        $data = (object) [];
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restGudang->get('informasi-penerimaan-barang/get-detail',
                [
                    'query' => [
                        'id' => $id
                    ]
                ]
            );
            $response = json_decode($response->getBody(),true);
            $data = (object) $response['response']['data'];
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
        } catch (\Exception $e) {
            Yii::info($e->getMessage());
        }

        return $this->render('preview',get_defined_vars());
    }

    public function actionGetDataPemesanan($id)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $filter['id'] = $id;
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restGudang->get('informasi-penerimaan-barang/get-data-detail', 
                    [
                        'query' => http_build_query($filter)
                    ]
            );
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no; 
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionExportPdf($id)
    {
        $date = date("Y-m-d");
        $path = Yii::getAlias("@download") . "/gudang-penerimaan-barang.pdf";
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restGudang->post('informasi-penerimaan-barang/cetak-penerimaan-mutasi', [
                'query' => [
                    'id' => $id
                ],
                'form_params' => [],
                'save_to' => $path
            ]);

            return DocoHelpers::downloadPdf($response,$path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restGudang->delete('informasi-penerimaan-barang/delete', [
                'query' => [
                    'id' => $id
                ]
            ]);
        return DocoHelpers::response([
            'message' => "data berhasil di delete"
        ]);
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'message' => $e->getMessage()
            ],422);
        } catch (\Exception $e) {
            return DocoHelpers::response([
                'message' => $e->getMessage()
            ],422);
        }
    }
}