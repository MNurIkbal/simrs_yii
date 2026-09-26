<?php
// Author : Ardi Pratama
// edited : rizal

namespace Doco\pendaftaran\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class KunjunganRawatJalanBrimobController extends DocoController
{
    protected $_title = "Laporan Kunjungan Pasien";
    protected $_module = 'pendaftaran/kunjungan-rawat-jalan/';
    protected $_restMaster;
    protected $_restPendaftaran;
    const SINGKATAN_RJ = 'RJ';

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actionLaporan()
    {
        // Init
        // $status = $this->_status; $options = $this->_options;
        $title = $this->_title;

        $settingKolomRequest = $this->_restPendaftaran->get('lap-kunjungan-rawat-jalan-brimob/data-kolom');
        $body = json_decode($settingKolomRequest->getBody(), true);
        $dataKolomList = $body['response']['filter_kebutuhan'];

        
        // $propinsiList = ArrayHelper::map($propinsiList, 'propinsi_id', 'propinsi_nama');

        $golonganUmurRequest = $this->_restPendaftaran->get('lap-kunjungan-rawat-jalan-brimob/golongan-umur');
        $body = json_decode($golonganUmurRequest->getBody(), true);
        $golonganUmurList = $body['response'];
        $golonganUmurList = ArrayHelper::map($golonganUmurList, 'golonganumur_id', 'golonganumur_nama');

        $propinsiRequest = $this->_restPendaftaran->get('lap-kunjungan-rawat-jalan-brimob/propinsi');
        $body = json_decode($propinsiRequest->getBody(), true);
        $propinsiList = $body['response'];
        $propinsiList = ArrayHelper::map($propinsiList, 'propinsi_id', 'propinsi_nama');

        $instalasiRequest = $this->_restPendaftaran->get('lap-kunjungan-rawat-jalan-brimob/instalasi');
        $body = json_decode($instalasiRequest->getBody(), true);
        $instalasiList = $body['response'];
        $instalasiList = ArrayHelper::map($instalasiList, 'instalasi_id', 'instalasi_nama');

        $carabayarRequest = $this->_restMaster->get('cara-bayar/list-cara-bayar');
        $body = json_decode($carabayarRequest->getBody(),TRUE);
        $carabayarList = $body['response'];
        
        /*$ruanganRequest = $this->_restMaster->get('ruangan/list-ruangan?singkatan=' . self::SINGKATAN_RJ);
        $body = json_decode($ruanganRequest->getBody(),TRUE);*/
        // echo $ruanganRequest->getBody();
        $ruanganList = $body['response'];
        $jenisPencarianList = $this->listPencarian();
        $listKolom = $this->listKolom();
        $jenisPencarian = $this->jenisPencarian();

        return $this->render('laporan_2', get_defined_vars());
    }

    public function listPencarian() 
    {
        return [
            1 => Yii::t('fe', 'Umur'),
            2 => Yii::t('fe', 'Jenis kelamin'),
            3 => Yii::t('fe', 'Status kunjungan'),
            4 => Yii::t('fe', 'Agama'),
            5 => Yii::t('fe', 'Pekerjaan'),
            6 => Yii::t('fe', 'Status pekerjaan'),
            7 => Yii::t('fe', 'Status perkawinan'),
            8 => Yii::t('fe', 'Alamat'),
            9 => Yii::t('fe', 'Kabupaten/kota'),
            10 => Yii::t('fe', 'Cara masuk'),
            11 => Yii::t('fe', 'Rujukan'),
            12 => Yii::t('fe', 'Jenis kasus penyakit'),
            13 => Yii::t('fe', 'Keterangan pulang'),
            14 => Yii::t('fe', 'Dokter pemeriksa'),
            15 => Yii::t('fe', 'Kelas pelayanan'),
        ];
    }

    public function jenisPencarian()
    {
        return [
            1 => Yii::t('fe', 'Umur'),
            2 => Yii::t('fe', 'Wilayah'),
        ];
    }

    public function listKolom(){
        return [
            1 => Yii::t('fe', 'Tgl Pendaftaran'),
            2 => Yii::t('fe', 'No Pendaftaran'),
            3 => Yii::t('fe', 'No Rekam Medik'),
            4 => Yii::t('fe', 'Nama Pasien'),
            5 => Yii::t('fe', 'Jenis Kelamin'),
            6 => Yii::t('fe', 'Tanggal Lahir'),
            7 => Yii::t('fe', 'Umur'),
            8 => Yii::t('fe', 'Golongan Umur'),
            9 => Yii::t('fe', 'Agama'),
            10 => Yii::t('fe', 'Status Perkawinan'),
            11 => Yii::t('fe', 'Pekerjaan'),
            12 => Yii::t('fe', 'Propinsi'),
            // 11 => Yii::t('fe', 'Alamat'),
            13 => Yii::t('fe', 'Kota / Kabupaten'),
            14 => Yii::t('fe', 'Kunjungan'),
            15 => Yii::t('fe', 'Jenis Kasus Penyakit'),
            16 => Yii::t('fe','Cara Masuk'),
            17 => Yii::t('fe', 'Cara Bayar'),
            18 => Yii::t('fe', 'Penjamin'),
            19 => Yii::t('fe', 'Rujukan'),
            20 => Yii::t('fe', 'Ruangan'),
            21 => Yii::t('fe', 'Dokter'),
            22 => Yii::t('fe', 'Kelas Pelayanan'),
            23 => Yii::t('fe', 'Status Pulang'),
        ];
    }

    public function actionSimpanKolom(){

        $request = Yii::$app->request;
        $formName = '';
        if ($request->post()) {
            $formData = $request->post(); // tampung formdata

            $form_params = array(
                'filter_kebutuhan' => $formData['filter_kebutuhan']
            );

            try {
                $response = $this->_restPendaftaran->post('lap-kunjungan-rawat-jalan-brimob/setting-kolom', [
                    'form_params' => $formData
                ]);
                $r = json_decode($response->getBody(), true);
                    // Return
                return DocoHelpers::response($r, false, $formName);
            } catch (RequestException $e) {
                    // Return
                return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
            } catch (\Exception $e) {
                    // Return
                return DocoHelpers::responseTemplate(500, $e->getMessage());
            }
        }

    }

    public function actionListKabupaten()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $propinsi_id = empty($post['depdrop_parents'][0]) ? null : $post['depdrop_parents'][0];

        $penjaminRequest = $this->_restPendaftaran->get('lap-kunjungan-rawat-jalan-brimob/kabupaten?propinsi_id=' . $propinsi_id);
        $body = json_decode($penjaminRequest->getBody(), true);
        $responses = $body['response'];

        $out = [];
        foreach ($responses as $key => $response) {
            $out[] = [
                'id' => $response['kabupaten_id'],
                'name' => $response['kabupaten_nama']
            ];
        }

        echo json_encode(['output' => $out, 'selected' => '']);
        return;
    }

    public function actionListRuangan(){

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

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        if (isset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran'])) {
            $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_akhir = $tgl_pendaftaran_range[1];
            $tgl_awal_format = DocoHelpers::convertIndoToEnglish($tgl_awal, false, true);
            $tgl_akhir_format = DocoHelpers::convertIndoToEnglish($tgl_akhir, false, true);
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
        }
        
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restPendaftaran->get('lap-kunjungan-rawat-jalan-brimob/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            $data = $body['response']['data'];
            foreach ($data as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pendaftaran_id']);
                unset($value['pendaftaran_id']);

                $value['rowNum'] = $no;
                if($value['carakeluar_nama'] != '' && $value['kondisikeluar_nama']){
                    $statusPulang = $value['carakeluar_nama'] . ' / ' . $value['kondisikeluar_nama'];
                }elseif(($value['carakeluar_nama'] != '') || ($value['kondisikeluar_nama'] != '')){
                    if($value['carakeluar_nama'] != ''){
                        $statusPulang = $value['carakeluar_nama'];
                    }else{
                        $statusPulang = $value['kondisikeluar_nama'];
                    }
                }
                else{
                    $statusPulang = '';
                } 
                $value['status_pulang'] = $statusPulang;  
                $value['tgl_pendaftaran'] = date('d-F-Y H:i:s',strtotime($value['tgl_pendaftaran']));
                $value['primaryKey'] = $primaryKey;
                $value['toggle'] = '';
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

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $post = $request->post();
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
        $path = Yii::getAlias("@download") . "/lap-kunjungan-rawat-jalan.pdf";
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        try {
            $response = $this->_restPendaftaran->get('lap-kunjungan-rawat-jalan-brimob/export-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            // return $e->getMessage();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            // return $e->getMessage();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $requestGet = $request->get();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $toggle = [];
        if(array_key_exists(26, $requestGet['columns']) && !empty($requestGet['columns'][26]['search']['value'])){
            $togle_tmp = explode(",",$requestGet['columns'][26]['search']['value']);
            $toggle = json_encode($togle_tmp);
        }
        if (isset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran'])) {
            $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_akhir = $tgl_pendaftaran_range[1];
            $tgl_awal_format = DocoHelpers::convertIndoToEnglish($tgl_awal, false, true);
            $tgl_akhir_format = DocoHelpers::convertIndoToEnglish($tgl_akhir, false, true);
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
            $yiiRestfulParams['advanced-filter']['toggle'] = $toggle;
            unset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
        }
        try {
            $path = Yii::getAlias("@download") . "/lap-kunjungan-rawat-jalan-brimob.xlsx";
            $response = $this->_restPendaftaran->get('lap-kunjungan-rawat-jalan-brimob/export-excel',[
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
