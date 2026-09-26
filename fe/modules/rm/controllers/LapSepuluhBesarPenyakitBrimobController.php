<?php
// Author : Ramdhan Nurrachman

namespace Doco\rm\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use yii\helpers\ArrayHelper;

class LapSepuluhBesarPenyakitBrimobController extends DocoController
{
    protected $_title = "Laporan 10 Besar Penyakit";
    protected $_module = 'rm/lap-sepuluh-besar-penyakit/';
    protected $_restRm; protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restRm = Yii::$app->docoRest->rm; $this->_restMaster = Yii::$app->docoRest->master;
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
        $laporan_penyakit = $this->laporanPenyakit();
        $cek_start = 0;
        $custKolom=[];
        foreach ($laporan_penyakit['header'] as $key => $v) {
           $custKolom[$v['kelompok']]['data'][] = [
                'kode' => $key,
                'klasifikasipasien_nama' => $v['klasifikasipasien_nama']
            ];
            $custKolom[$v['kelompok']]['colspan'] = count($custKolom[$v['kelompok']]['data']);
        }
        $instalasiRequest = $this->_restRm->get('lap-sepuluh-besar-penyakit/instalasi');
        $body = json_decode($instalasiRequest->getBody(), true);
        $instalasiList = $body['response'];
        $instalasiList = ArrayHelper::map($instalasiList, 'instalasi_id', 'instalasi_nama');
    
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

        try {
            // echo 'lap-sepuluh-besar-penyakit/get-data?' . http_build_query($yiiRestfulParams);
            // die();
            $response = $this->_restRm->get('lap-sepuluh-besar-penyakit/get-data?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            $no_kunci = -1;
            $laporan_penyakit = $body['response'];
            foreach ($laporan_penyakit['list'] as $key => $value) {
                $no++;
                $no_kunci++;
                $value['rowNum'] = $no;
                $value['no_dtd'] = $value['no_dtd'];
                $value['kode_diagnosa'] = $key;
                $value['nama_diagnosa'] = $value['nama_diagnosa'];
                $value['bulan'] = null;
                $value['tahun'] = null;

                $total_row = 0;
                $total_satuan = 0;
                foreach ($laporan_penyakit['header'] as $k => $v){
                    $detail = $laporan_penyakit['detail'];
                    $total_satuan = isset($detail[$k][$key]['total']) ? $detail[$k][$key]['total'] : 0;
                    if ($total_satuan > 0) {
                        $total_row = $total_row + $total_satuan;
                    }
                    $value['data_'.$k] = $total_satuan;
                    $value['instalasi'] = isset($detail[$k][$key]['instalasi']) ? $detail[$k][$key]['instalasi'] : "" ;
                    $value['ruangan_id'] = isset($detail[$k][$key]['ruangan_id']) ? $detail[$k][$key]['ruangan_id'] : "" ;
                }
                $value['jumlah'] = $total_row;
                $data[$no_kunci] = $value;   
            }

            $result['data'] = $data;
            $result['recordsTotal'] = isset($body['response']['_meta']['totalCount']) ? $body['response']['_meta']['totalCount'] : 10;
            $result['recordsFiltered'] = isset($body['response']['_meta']['totalCount']) ? $body['response']['_meta']['totalCount'] : 10;
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionListRuangan()
    {

        $request = Yii::$app->request;
        $post = $request->post();
        $instalasi_id = empty($post['depdrop_parents'][0]) ? null : $post['depdrop_parents'][0];

        $ruanganRequest = $this->_restMaster->get('ruangan/list-ruangan?instalasi_id=' . $instalasi_id);
        $body = json_decode($ruanganRequest->getBody(), true);
        $responses = $body['response'];

        $out = [];
        foreach ($responses as $key => $response) {
            $out[] = [
                'id' => $key,
                'name' => $response
            ];
        }

        echo json_encode(['output' => $out, 'selected' => '']);
        return;
    }

    public function actionGetLaporan()
    {
        $request = Yii::$app->request;
        return $this->laporanPenyakit();
    }


    protected function laporanPenyakit()
    {
        $request = Yii::$app->request;
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');
        $tgl_pendaftaran = $request->get('tgl_pendaftaran');
        $nama_diagnosa = $request->get('nama_diagnosa');
        $instalasi = $request->get('instalasi');
        $ruangan_id = $request->get('ruangan_id');

        $response = [];
        try {
            $response = $this->_restRm->get('lap-sepuluh-besar-penyakit/get-data', [
                'query' => [
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'tgl_pendaftaran' => $tgl_pendaftaran,
                    'nama_diagnosa' => $nama_diagnosa,
                    'instalasi' => $instalasi,
                    'ruangan_id' => $ruangan_id
                ]
            ]);
            $response = json_decode($response->getBody(), true);
            $response = $response['response'];
            $header = isset($response['header']) ? $response['header'] : [];
            $list = isset($response['list']) ? $response['list'] : [];
            $detail = isset($response['detail']) ? $response['detail'] : [];
        } catch (RequestException $e) {
            $header = $list = $detail = [];
        }

        return $response;
    }

    // Beware Section Data Selection
    public function actionExportExcel()
    {        
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        $result = [];
        $url = "";
        
        try {
            $path = Yii::getAlias("@download") . "/lap-sepuluh-besar-penyakit.xlsx";

            $response = $this->_restRm->get('lap-sepuluh-besar-penyakit/export-excel',[
                'query' => $yiiRestfulParams,
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), True);
            return DocoHelpers::downloadFile($path,true);       
        } catch (RequestException $e) {
            throw new \yii\web\NotFoundHttpException();
            return $e;
        } catch (\Exception $e) {
            throw new \yii\web\NotFoundHttpException();
            return $e;
        }
    }
    public function actionGetRuangan($assign_id="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restRm->get('allow/get-ruangan?advanced-filter[instalasi_id]='.$parent_label);
            $body = json_decode($response->getBody(), True);            
            foreach ($body['response'] as $value) 
                $result['output'][] = [
                    'id' => $value['ruangan_id'], 
                    'name' => $value['ruangan_nama']
                ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDiagnosa($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];
        try {
            $response = $this->_restRm->get('lap-sepuluh-besar-penyakit/nama-diagnosa?advanced-filter[diagnosa_nama]=' . $q);
            $body = json_decode($response->getBody(), true);
            $temp_dokter = array(); // array dokter temp
            foreach ($body['response']['data'] as $value)

                $result['results'][] = [
                    'id' => $value['diagnosa_kode'],
                    'text' => $value['diagnosa_nama']
                ];
 
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