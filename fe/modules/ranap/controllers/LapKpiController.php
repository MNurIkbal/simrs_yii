<?php

/**
 * @Author  : M.ilhamsyah.P
 * @Date    : 2020-08-10 13:23:53
 * @Last Modified by    :  
 * @Last Modified time  :  
 * @Description : membuat laporan KPI
 */

namespace Doco\ranap\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;



class LapKpiController extends DocoController
{
    protected $_title = "Laporan KPI";
    protected $_restRanap;

    public function init()
    {
        parent::init();
        $this->_restRanap = Yii::$app->docoRest->ranap;
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
        $result['recordsFiltered'] = 0;
        $counter=0;

        try {
            $response = $this->_restRanap->get('laporan-kpi/index?='. http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $data[$key] = $value;
                $data[$counter]['tgl_rencanapulang'] = !empty($value['tgl_rencanapulang']) ? $value['tgl_rencanapulang'] : ' - ';
                $data[$counter]['nomor_tagihan'] = !empty($value['nomor_tagihan']) ? $value['nomor_tagihan'] : ' - ';
                $data[$counter]['rowNum'] = $no;
                $counter++;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if (isset($yiiRestfulParams['advanced-filter']['tgl_masukkamar'])) {
            $tgl_masuk_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_masukkamar']);
            $tgl_awal = $tgl_masuk_range[0];
            $tgl_akhir = $tgl_masuk_range[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $yiiRestfulParams['advanced-filter']['tgl_masuk_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_masuk_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_masukkamar']);
        }
        try {
            $path = Yii::getAlias("@download") . "/laporan-KPI.xlsx";

            $response = $this->_restRanap->get('laporan-kpi/export-excel',[
                'query' => $yiiRestfulParams,
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }



}
