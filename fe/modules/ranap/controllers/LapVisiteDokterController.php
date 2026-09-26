<?php


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
use app\modules\ranap\models\LaporanVisiteDokterView;


class LapVisiteDokterController extends DocoController
{
    protected $_page;
    protected $_restRanap;
	public function init()
    {
        parent::init();
        $this->_restRanap = Yii::$app->docoRest->ranap;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $modelV = new LaporanVisiteDokterView;
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $instalasi_nama = Yii::$app->docoVars->workspace("instalasi_name");
        $params = [
            'controller'=>'traVisitDokter',
            'ruangan_id'=>$ruangan_id,
            'instalasi_nama'=>$instalasi_nama,
        ];
        
        $response = $this->_restRanap->get('allow/get-api?' . http_build_query($params));
        $body = json_decode($response->getBody(), TRUE);
        $resMaster = $body['response']['master'];
        $datajenis_visite = $body['response']['master']['jenisvisite'];
        $datadokter_visite = $body['response']['master']['doktervisite'];
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
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
            $response = $this->_restRanap->get('lap-visite-dokter/index?ruangan_id=' . $ruangan_id . '&' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);

            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primary = json_encode($value['pendaftaran_id']);
                $value['primary'] = DocoHelpers::encrypt($primary);
                $data[$key] = $value;
                $data[$counter]['carabayar_penjamin'] = $value['Cara Bayar'] . ' / ' . $value['Penjamin'];
                $data[$counter]['ruangan_kamar'] = $value['Ruangan'] . ' - ' . $value['Kamar']. ' - ' . $value['Bed'];
                $data[$counter]['pendaftaran'] = $value['No. Pendaftaran'];
                $data[$counter]['r_medik'] = $value['No. Rekam Medik'];
                $data[$counter]['pasien'] = $value['Nama Pasien'];
                $data[$counter]['jk'] = $value['Jenis Kelamin'];
                $data[$counter]['kp'] = $value['Kasus Penyakit'];
                $data[$counter]['dpj'] = $value['Dokter Penanggung Jawab'];
                $data[$counter]['jv'] = $value['Jenis Visite'];
                $data[$counter]['dokv'] = $value['Dokter Visite'];
                $data[$counter]['Tanggal Visite'] = date('d F Y H:i:s', strtotime($value['Tanggal Visite']));
                $data[$counter]['Tanggal Admisi'] = date('d F Y H:i:s', strtotime($value['Tanggal Admisi']));
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
        if (isset($yiiRestfulParams['advanced-filter']['Tanggal Visite'])) {
            $tgl_visite_range = explode(' - ', $yiiRestfulParams['advanced-filter']['Tanggal Visite']);
            $tgl_awal = $tgl_visite_range[0];
            $tgl_akhir = $tgl_visite_range[1];

            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            
            $yiiRestfulParams['advanced-filter']['tgl_visite_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_visite_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['Tanggal Visite']);
        }
        $path = Yii::getAlias("@download") . "/lap-visite-dokter.pdf";
        try {
            $response = $this->_restRanap->get('lap-visite-dokter/export-pdf?ruangan_id='.Yii::$app->docoVars->workspace("ruangan_id").'&'.http_build_query($yiiRestfulParams),[
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
        if (isset($yiiRestfulParams['advanced-filter']['Tanggal Visite'])) {
            $tgl_visite_range = explode(' - ', $yiiRestfulParams['advanced-filter']['Tanggal Visite']);
            $tgl_awal = $tgl_visite_range[0];
            $tgl_akhir = $tgl_visite_range[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $yiiRestfulParams['advanced-filter']['tgl_visite_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_visite_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['Tanggal Visite']);
        }
        try {
            $path = Yii::getAlias("@download") . "/laporan-visite-dokter.xlsx";
            $query = [
                'ruangan_id' => Yii::$app->docoVars->workspace("ruangan_id"),
            ];
            $query = array_merge($query,$yiiRestfulParams);

            $response = $this->_restRanap->get('lap-visite-dokter/export-excel',[
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

}