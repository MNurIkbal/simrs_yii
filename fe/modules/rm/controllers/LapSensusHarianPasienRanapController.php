<?php

namespace Doco\rm\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class LapSensusHarianPasienRanapController extends DocoController
{
    protected $_restRm;
    protected $_title;
    protected $_module = '/rm/lap-sensus-harian-pasien-ranap/';
    const TGL_ADMISI = 'tgl_admisi';
    const TGL_PENDAFTARAN = 'tgl_pendaftaran';
    const TGL_PASIEN_PLG = 'tgl_pasienplg';
    const GROUP_KONDISIKELUAR_MORE_48 = [5];

	public function init()
    {
        parent::init();
        $this->_title = Yii::t('fe', 'Laporan Sensus Harian Rawat Inap');
        $this->_restRm = Yii::$app->docoRest->rm;
    }

    public function actionIndex()
    {
        $api = [];
        $title = $this->_title;
        $api = $this->_restRm->get('lap-sensus-harian-pasien-ranap/generate-api');
        $api = json_decode($api->getBody(), True);
        $api = $api['response'];
        return $this->render('index', get_defined_vars());
    }

    public function actionGetDataMasuk()
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
            $response = $this->_restRm->get('lap-sensus-harian-pasien-ranap/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $body = $body['response'];
            $no = 0;
            foreach($body['data'] as $key => $val) {
                $no++;
                $val['rowNum'] = $no;
                if(!empty($val['diagnosa_nama'])){
                    $diagnosa = json_decode($val['diagnosa_nama'], true);
                    $val['diagnosa_nama'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                }
                $data[] = $val;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDataKeluar()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        
        $draw = $request->get('draw', 1);
        if($draw == 1) {
            $yiiRestfulParams['advanced-filter'] = [];
        }
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restRm->get('lap-sensus-harian-pasien-ranap/get-data-pasien-keluar?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $body = $body['response'];
            $no = 0;
            foreach($body['data'] as $key => $val) {
                $no++;
                $val['rowNum'] = $no;
                if(!empty($val['diagnosa_nama'])){
                    $diagnosa = json_decode($val['diagnosa_nama'], true);
                    $val['diagnosa_nama'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                }
                $val['jam_rawat'] = $this->generateJamRawat($val['tgl_masukkamar_1'], $val['tgl_keluarkamar'], $val['lama_rawat'], true);
                $data[] = $val;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDataPindahanDari()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        
        $draw = $request->get('draw', 1);
        if($draw == 1) {
            $yiiRestfulParams['advanced-filter'] = [];
        }
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restRm->get('lap-sensus-harian-pasien-ranap/get-data-pasien-pindahan-dari?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $body = $body['response'];
            $no = 0;
            foreach($body['data'] as $key => $val) {
                $no++;
                $val['rowNum'] = $no;
                if(!empty($val['diagnosa_nama'])){
                    $diagnosa = json_decode($val['diagnosa_nama'], true);
                    $val['diagnosa_nama'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                }
                $data[] = $val;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDataPindahanKe()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        
        $draw = $request->get('draw', 1);
        if($draw == 1) {
            $yiiRestfulParams['advanced-filter'] = [];
        }
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restRm->get('lap-sensus-harian-pasien-ranap/get-data-pasien-pindahan-ke?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $body = $body['response'];
            $no = 0;
            foreach($body['data'] as $key => $val) {
                $no++;
                $val['rowNum'] = $no;
                if(!empty($val['diagnosa_nama'])){
                    $diagnosa = json_decode($val['diagnosa_nama'], true);
                    $val['diagnosa_nama'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                }
                // $tglMasukKamar = explode(" ",$val['tgl_masukkamar_1']);
                // if($tglMasukKamar[1] == '00:00:00') {
                //     $tglMasukKamar[1] = $val['jam_masukkamar'];
                //     $val['tgl_masukkamar_1'] = implode($tglMasukKamar);
                // }
                if(empty($val['lama_rawat'])) {
                    $lamaRawat =  $this->generateJamRawat($val['tgl_masukkamar_1']);
                    $val['lama_rawat'] = $lamaRawat;
                    if($lamaRawat == 0){
                        $val['jam_rawat'] = $this->generateJamRawat($val['tgl_masukkamar_1'], null, null, true);
                        $val['lama_rawat'] = 1;
                    } else {
                        $val['jam_rawat'] = $this->generateJamRawat($val['tgl_masukkamar_1'], null, $lamaRawat, true);
                    }
                } else {
                    $val['jam_rawat'] = $this->generateJamRawat($val['tgl_masukkamar_1'], $val['tgl_keluarkamar'], $val['lama_rawat'], true);
                }
                $data[] = $val;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDataMeninggal()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        
        $draw = $request->get('draw', 1);
        if($draw == 1) {
            $yiiRestfulParams['advanced-filter'] = [];
        }
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restRm->get('lap-sensus-harian-pasien-ranap/get-data-pasien-meninggal?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $body = $body['response'];
            // dump($body);die;
            $no = 0;
            foreach($body['data'] as $key => $val) {
                $no++;
                $val['rowNum'] = $no;
                if(!empty($val['diagnosa_nama'])){
                    $diagnosa = json_decode($val['diagnosa_nama'], true);
                    $val['diagnosa_nama'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                }
                $val['lama_rawat_leb48'] = 0;
                $val['lama_rawat_kur48'] = 0;
                if($val['lama_rawat'] <= 2) {
                    $val['lama_rawat_kur48'] = 1;
                } else {
                    $val['lama_rawat_leb48'] = 1;
                }
                $data[] = $val;
            }
            // dump($data);die;
            $result['data'] = $data;
            $result['recordsTotal'] = $body['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDataKeluarRujuk()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        
        $draw = $request->get('draw', 1);
        if($draw == 1) {
            $yiiRestfulParams['advanced-filter'] = [];
        }
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restRm->get('lap-sensus-harian-pasien-ranap/get-data-pasien-keluar-rujuk?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $body = $body['response'];
            $no = 0;
            foreach($body['data'] as $key => $val) {
                $no++;
                $val['rowNum'] = $no;
                if(!empty($val['diagnosa_nama'])){
                    $diagnosa = json_decode($val['diagnosa_nama'], true);
                    $val['diagnosa_nama'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                }
                $val['jam_rawat'] = $this->generateJamRawat($val['tgl_masukkamar_1'], $val['tgl_keluarkamar'], $val['lama_rawat'], true);
                $data[] = $val;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDataRekapitulasi()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        
        $draw = $request->get('draw', 1);
        if($draw == 1) {
            $yiiRestfulParams['advanced-filter'] = [];
        }
        $data = [];
        $result = [];
        $tmp = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restRm->get('lap-sensus-harian-pasien-ranap/get-data-rekapitulasi?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $body = $body['response'];
            $no = 0;
            foreach($body as $key => $val) {
                $val['rowNum'] = $val['no'];
                $val['tgl_admisi'] = '';
                $val['kelaspelayanan_id'] = '';
                $val['ruangan_id'] = '';
                $val['status_ranap_id'] = '';
                $data[] = $val;
            }
            ArrayHelper::multisort($data, ['order'], [SORT_ASC]);
            $result['data'] = $data;
            $result['recordsTotal'] = count($data);
            $result['recordsFiltered'] = count($data);
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if (isset($yiiRestfulParams['advanced-filter'][self::TGL_ADMISI])) {
            $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter'][self::TGL_ADMISI]);
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_akhir = $tgl_pendaftaran_range[1];
            $tgl_awal_format = date('Y-m-d', strtotime($tgl_awal));
            $tgl_akhir_format = date('Y-m-d', strtotime($tgl_akhir));
            $yiiRestfulParams['advanced-filter']['tgl_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter'][self::TGL_ADMISI]);
        }
        $yiiRestfulParams['advanced-filter']['pegawai'] = Yii::$app->session->get('user_identity')['nama_pegawai'];
        $path = Yii::getAlias("@download") . "/lap-sensus-harian-pasien-rawat-inap.pdf";
        try {
            $response = $this->_restRm->get('lap-sensus-harian-pasien-ranap/export-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            // dump($body);die;
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            return $e->getMessage();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            return $e->getMessage();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionShowPopupExcel()
    {
        $title = 'Download Excel Laporan Sensus Harian Rawat Inap';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = [
            'tgl_pendaftaran' => $request->get('tgl_pendaftaran'),
            'kelas' => $request->get('kelas'),
            'ruangan' => $request->get('ruangan'),
            'statusperiksa' => $request->get('statusperiksa'),
        ];
        $yiiRestfulParams['randString'] = $randString;
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modalExcel', get_defined_vars());
    }

    public function actionProcessSyncExcel($randString)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($this->_restRm, [
            'url' => "lap-sensus-harian-pasien-ranap/sync-export-excel-rabbitmq",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'LAP_SENSUS_HARIAN_PASIEN_RAWAT_INAP.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restRm->get('lap-sensus-harian-pasien-ranap/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        return $yiiRestfulParams;
        if (isset($yiiRestfulParams['advanced-filter'][self::TGL_ADMISI])) {
            $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter'][self::TGL_ADMISI]);
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_akhir = $tgl_pendaftaran_range[1];
            $tgl_awal_format = date('Y-m-d', strtotime($tgl_awal));
            $tgl_akhir_format = date('Y-m-d', strtotime($tgl_akhir));
            $yiiRestfulParams['advanced-filter']['tgl_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter'][self::TGL_ADMISI]);
        }
        try {
            $path = Yii::getAlias("@download") . "/lap-sensus-harian-pasien-rawat-inap.xlsx";
            $response = $this->_restRm->get('lap-sensus-harian-pasien-ranap/export-excel',[
                'query' => $yiiRestfulParams,
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            // dump($body);die;
            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    private function generateJamRawat($start , $end = null, $lamaRawat = null, $toHours = false)
    {
        $dateOne = new \DateTime($start);

        if($end != null) {
            $dateTwo = new \DateTime($end);
        } else {
            $dateTwo = new \DateTime();
        }
        $tmpResult = ($dateTwo->diff($dateOne));

        if($lamaRawat != null) {
            $result = ($tmpResult->d * 24) + $tmpResult->h;
        } else {
            if($toHours){
                $result =$tmpResult->h;
            } else {
                $result =$tmpResult->days;
            }
        }

        return $result;
    }

    public function actionGetDataSedangRanap()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        $tgl_akhir_rawat = date('Y-m-d H:i:s');
        if (isset($yiiRestfulParams['advanced-filter']['tgl_admisi'])) {
            $tgl_admisi = date('Y-m-d');
            $explode = explode(" - ", $yiiRestfulParams['advanced-filter']['tgl_admisi']);
            if(count($explode) == 2) {
                $tgl_admisi = date('Y-m-d', strtotime($explode[0]));
            }

            $tgl_akhir_rawat = $tgl_admisi >= date('Y-m-d') ? date('Y-m-d H:i:s') : date('Y-m-d 23:59:59', strtotime($tgl_admisi));
        }
        
        $draw = $request->get('draw', 1);
        if($draw == 1) {
            $yiiRestfulParams['advanced-filter'] = [];
        }
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        // dump($yiiRestfulParams);die();
        try {
            $response = $this->_restRm->get('lap-sensus-harian-pasien-ranap/get-data-pasien-sedang-ranap?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            // dump($body);die();
            $body = $body['response'];
            
            $no = 0;
            foreach($body['data'] as $key => $val) {
                $no++;
                $val['rowNum'] = $no;
                if(!empty($val['diagnosa_nama'])){
                    $diagnosa = json_decode($val['diagnosa_nama'], true);
                    $val['diagnosa_nama'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                }
                $waktu = DocoHelpers::getDiffDateTime($val['tgl_masukkamar_1'], $tgl_akhir_rawat);
                $val['jam_rawat'] = $waktu['jam'];
                $val['lama_rawat'] = $waktu['hari'];
                $data[] = $val;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['_meta']['totalCount'];
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
