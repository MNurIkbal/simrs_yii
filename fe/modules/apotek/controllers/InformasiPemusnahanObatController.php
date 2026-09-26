<?php

/**
 * @author Randy Vianda Putra
 * @todo Informasi Pemusnahan Obat Alkes
 * @copyright 8 Juni 2018 aweutist
 */

namespace Doco\apotek\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use Doco\apotek\models\InformasiForm;

class InformasiPemusnahanObatController extends DocoController
{
    protected $_title = "Informasi Pemusnahan Obat Alkes";
    protected $_module = '/apotek/informasi-pemusnahan-obat-alkes/';
    protected $_restApotek;
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek;
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $instalasi = Yii::$app->docoVars->workspace('instalasi_name');
        $model = new InformasiForm;

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['ruangan_id'] = Yii::$app->docoVars->workspace('ruangan_id');
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restApotek->get('inf-pemusnahan-obat/index?' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pemusnahanobat_id']);
                unset($value['pemusnahanobat_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['tglpemusnahan'] = date('d-M-Y', strtotime($value['tglpemusnahan']));
                $value['total_harganetto'] = DocoHelpers::formatNumber($value['total_harganetto']);
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

    public function actionGetNoPemusnahan()
    {
        if (isset($_GET['q']['term']) && !empty($_GET['q']['term'])) {
            $response = $this->_restApotek->request('POST', 'inf-pemusnahan-obat/data-no-pemusnahan',[
                'form_params' => ['term'=>$_GET['q']['term']],
            ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = [
                    'id' => $value['nopemusnahan'],
                    'text' => $value['nopemusnahan']
                ];
            }
            $total = count($body['response']);
            $return = ['result' => $data, 'total_count' => $total, 'incomplete_results' => false];

            return DocoHelpers::response($return);
        }
    }

    public function actionView($id)
    {
        $title = $this->_title;
        $instalasi = Yii::$app->docoVars->workspace('instalasi_name');
        $pemusnahanobat_id = DocoHelpers::decrypt($id);
        $response = $this->_restApotek->get('inf-pemusnahan-obat/data-detail', ['query' => ['id' => $pemusnahanobat_id]]);
        $body = json_decode($response->getBody(), true);
        $data = $body['response'];

        return $this->render('detail',get_defined_vars());
    }

    public function actionGetDataPemusnahan()
    {
        $id = DocoHelpers::decrypt($_GET['id']);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['pemusnahanobat_id'] = $id;
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restApotek->get('inf-pemusnahan-obat/data-pemusnahan?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $data_pemusnahan = $body['response']['data'];
            $no = $request->get('start',1);
            $totalharga = 0;
            foreach ($data_pemusnahan as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['obatalkes_id']);
                unset($value['obatalkes_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                // $total = $value['jumlah'] * ($value['harganetto']);
                $value['tglkadaluarsa'] = date('d-M-Y', strtotime($value['tglkadaluarsa']));
                $value['jumlah_harganetto'] = DocoHelpers::rupiahDisplay($value['jumlah_harganetto']);
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
            $result['error'] = 'x-'.$e->getMessage();
            return $result;
        }
    }

    public function actionPrintPemusnahan($id, $nopemusnahan)
    {
        $id = DocoHelpers::decrypt($id);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/cetak-pemusnahan-".$nopemusnahan.".pdf";
        try {
            $response = $this->_restApotek->get('inf-pemusnahan-obat/print-pemusnahan',[
                'save_to' => $path,
                'query' => [
                    'id' => $id,
                    'nopemusnahan' => $nopemusnahan
                ],
            ]);
            $body = json_decode($response->getBody(), true);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        try{
            $response = $this->_restApotek->post('inf-pemusnahan-obat/delete-pemusnahan', ['form_params' => ['id' => $id]]);
            $body = json_decode($response->getBody(), true);

            return DocoHelpers::response($body['response']);
        } catch(\Exception $e){
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch(RequestException $e){
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }
}

