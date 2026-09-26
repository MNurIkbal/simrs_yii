<?php

namespace Doco\rm\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;

class LaporanSensusPasienRanapController extends DocoController
{
    protected $_restRm;
    protected $_module = '/rm/laporan-sensus-pasien-ranap/';
    public $_title;
    
    public function init()
    {
        parent::init();
        $this->_restRm = Yii::$app->docoRest->rm;
        $this->_title = 'Laporan Sensus Pasien Ranap';
    }
    
    public function actionIndex()
    {
        try {
            $title = $this->_title;
            $tempBulan = DocoHelpers::daftarBulan();
            $bulan = [];
            $tahun = [];

            for ($x = date('Y'); $x >= (date('Y') - 9) ; $x--) {
                $tahun[$x] = $x;
            }

            foreach ($tempBulan as $key => $value) {
                if ($key < 10) {
                    $bulan['0'.$key] = $value;
                } else {
                    $bulan[$key] = $value;
                }
            }

            $request = $this->_restRm->get('laporan-sensus-pasien-ranap/get-data-options');
            $options = json_decode($request->getBody(), true)['response'];
            $options['bulan'] = $bulan;
            $options['tahun'] = $tahun;
        } catch (RequestException $e) {
            $options = [
                'ruangan' => [],
                'kelas' => [],
                'bulan' => [],
                'tahun' => []
            ];
        }

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $no = $request->get('start',1);
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try {
            $restRm = $this->_restRm->get('laporan-sensus-pasien-ranap/index?'.http_build_query($yiiRestfulParams), [
                'form_params' => []
            ]);
            $body = json_decode($restRm->getBody(), true);
            $data = $body['response'];

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

    public function actionExportExcel()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/Laporan Bulanan Sensus Pasien Ranap.xlsx";

            $restRm = $this->_restRm->get('laporan-sensus-pasien-ranap/export-excel?'.http_build_query($yiiRestfulParams), [
                'save_to' => $path,
            ]);

            return DocoHelpers::downloadFile($path, true);
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    public function actionExportPdf()
    {
        try {
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/History Master Tempat Tidur.pdf";

            $restRm = $this->_restRm->get('laporan-sensus-pasien-ranap/export-pdf?'.http_build_query($yiiRestfulParams), [
                'save_to' => $path,
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    public function actionGetJumlahBed()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $ruangan_id = Yii::$app->request->get('ruangan_id');
        $kelaspelayanan_id = Yii::$app->request->get('kelaspelayanan_id');

        try {
            $restRm = $this->_restRm->get('laporan-sensus-pasien-ranap/get-jumlah-bed?ruangan_id='.$ruangan_id.'&kelaspelayanan_id='.$kelaspelayanan_id, [
                'form_params' => []
            ]);
            $body = json_decode($restRm->getBody(), true);

            return $body['response']['jumlah_bed'];
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
}