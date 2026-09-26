<?php

/**
 * @Author  : Sunarko
 * @Date    : 2018-08-09 15:01:28
 * @Last Modified by    :  
 * @Last Modified time  :  
 * @Description : membuat laporan daftar pasien rawat jalan
 */

namespace Doco\rajal\controllers;

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

use app\modules\rajal\models\LapDaftarPasienView;


class LapDaftarPasienController extends DocoController
{
    protected $_title = "Laporan Daftar Pasien Rawat Jalan";
    protected $_restRajal;

    public function init()
    {
        parent::init();
        $this->_restRajal = Yii::$app->docoRest->rajal;

    }

    public function actionIndex()
    {
        $list_data = $this->getListDataPasien();
        $data_carabayar = $list_data["data_carabayar"];
        $data_penjamin = $list_data["data_penjamin"];
        $data_pegawai = $list_data["data_pegawai"];
        $data_statusperiksa = $list_data["data_statusperiksa"];
        $data_ruangan = $list_data["data_ruangan"];
        $data_jenis_kelamin = $list_data["data_jenis_kelamin"];
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $request = Yii::$app->request;
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        
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
            $response = $this->_restRajal->get('laporan-daftar-pasien/index?ruangan_id=' . $ruangan_id . '&' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primary = json_encode($value['pendaftaran_id']);
                $value['primary'] = DocoHelpers::encrypt($primary);
                $data[$key] = $value;
                $data[$counter]['tgl_pendaftaran'] = date('d F Y H:i:s', strtotime($value['tgl_pendaftaran']));
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

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if (isset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran'])) {
            $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_akhir = $tgl_pendaftaran_range[1];

            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
        }
        $path = Yii::getAlias("@download") . "/laporan-daftar-pasien.pdf";
        try {
            $response = $this->_restRajal->get('laporan-daftar-pasien/export-pdf?ruangan_id='.Yii::$app->docoVars->workspace("ruangan_id").'&ruangan_nama='.Yii::$app->docoVars->workspace("ruangan_name").'&'.http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if (isset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran'])) {
            $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_akhir = $tgl_pendaftaran_range[1];

            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));

            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
        }
        try {
            $path = Yii::getAlias("@download") . "/laporan-daftar-pasien.xlsx";
            $query = [
                'ruangan_id' => Yii::$app->docoVars->workspace("ruangan_id")
            ];
            $query = array_merge($query,$yiiRestfulParams);
            $response = $this->_restRajal->get('laporan-daftar-pasien/export-excel',[
                'query' => $query,
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

    /**
     *
     * private function
     *
     */
    private function getListDataPasien()
    {
        try {
            $request = Yii::$app->request;
            $response = $this->_restRajal->get('laporan-daftar-pasien/get-list-data?id_ruangan='.Yii::$app->docoVars->workspace('ruangan_id'));
            $body = json_decode($response->getBody(),TRUE);
            
            $data_statusperiksa = empty($body['response']['data-statusperiksa']) ? [] : $body['response']['data-statusperiksa'];
            $data_pegawai = empty($body['response']['data-pegawai']) ? [] : $body['response']['data-pegawai'];
            $data_carabayar = empty($body['response']['data-carabayar']) ? [] : $body['response']['data-carabayar'];
            $data_penjamin = empty($body['response']['data-penjamin']) ? [] : $body['response']['data-penjamin'];
            $data_ruangan = empty($body['response']['data-ruangan']) ? [] : $body['response']['data-ruangan'];
            $data_jenis_kelamin = empty($body['response']['data-jenis-kelamin']) ? [] : $body['response']['data-jenis-kelamin'];
           
            $result = [
                'data_statusperiksa' => $data_statusperiksa,
                'data_carabayar' => $data_carabayar,
                'data_pegawai' => $data_pegawai,
                'data_penjamin' => $data_penjamin,
                'data_ruangan' => $data_ruangan,
                'data_jenis_kelamin' => $data_jenis_kelamin,
            ];

            return $result;
        } catch (RequestException $e) {
            return [
                'data_statusperiksa' => [],
                'data_carabayar' => [],
                'data_pegawai' => [],
                'data_penjamin' => [],
                'data_ruangan' => [],
                'data_jenis_kelamin' => [],
            ];
        } catch (\Exception $e) {
            return [
                'data_statusperiksa' => [],
                'data_carabayar' => [],
                'data_pegawai' => [],
                'data_penjamin' => [],
                'data_ruangan' => [],
                'data_jenis_kelamin' => [],
            ];
        }
    }

}
