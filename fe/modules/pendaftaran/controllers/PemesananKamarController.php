<?php
// Author : Naufal Ziyad L

namespace Doco\pendaftaran\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\rm\models\PasienForm;
use app\modules\pendaftaran\models\TraPemesananKamarForm;
use app\modules\pendaftaran\models\InfPemesananKamarForm;
use app\modules\pendaftaran\models\PendaftaranForm;
use app\modules\master\models\PenanggungjawabForm;
use app\modules\master\models\RuanganForm;
use app\modules\pendaftaran\models\RujukanForm;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class PemesananKamarController extends DocoController
{
    protected $_title = "Pendaftaran :: Pemesanan Kamar";
    protected $_module = 'pendaftaran/pemesanan-kamar/';
    protected $_restMaster;
    protected $_restPendaftaran;
    const SINGKATAN_RI = 'RI';

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

    public function actionIndex()
    {
        try {
            $title = Yii::t('fe', 'Pemesanan Kamar');

            $modelPemesananKamar = new TraPemesananKamarForm();

            // Set inisial value
            $modelPemesananKamar->tgl_transaksibooking = date('Y-m-d h:i:s', strtotime('NOW'));
            $modelPemesananKamar->tgl_rawatinap = date('Y-m-d h:i:s', strtotime('NOW'));

            $ddlJenisKasusPenyakit = [];
            $ddlKelasPelayanan = [];
            $ddlListPerkawinan = [];
            $dllJenisKelamin = [];
            $allReq = $this->_restPendaftaran->get('pemesanan-kamar/get-bundle-data');
            $body = json_decode($allReq->getBody(),TRUE);
            $ddlJenisKasusPenyakit = $body['response']['list-penyakit'];
            $ddlKelasPelayanan = $body['response']['list-kelas'];
            $dllJenisKelamin = $body['response']['list-jenis-kelamin'];
            $ddlIdentitas = $body['response']['list-jenis-identitas'];
            $ddlPekerjaan = $body['response']['list-pekerjaan'];
            $ddlAgama = $body['response']['list-agama'];


            return $this->render('index', get_defined_vars());
        }catch(RequestException $e){
            return DocoHelpers::responseTemplate(500,$e->getMessage());
        }
    }

    public function actionInformasi()
    {
        $title = Yii::t('fe', 'Informasi Pemesanan Kamar');
        $data = $this->getData();
        $data_ruangan = !empty($data['data_ruangan']) 
            ? ArrayHelper::map($data['data_ruangan'], 'ruangan_id', 'ruangan_nama')
            : [];
        $data_status = !empty($data['data_status']) 
            ? ArrayHelper::map($data['data_status'], 'lookup_id', 'status_booking_kamar')
            : [];

        $module = $this->_module;
        return $this->render('_informasi_pemesanan_kamar', get_defined_vars());

    }

    public function actionGetDataInformasi()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        if (isset($yiiRestfulParams['advanced-filter']['tgl_bookingkamar'])) {
            $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_bookingkamar']);
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_akhir = $tgl_pendaftaran_range[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_bookingkamar']);
        }

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restPendaftaran->get('inf-pemesanan-kamar/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['bookingkamar_id']);
                // unset($value['bookingkamar_id']);
                $value['primary'] = $primaryKey;
                $value['no_kamar'] = $value['kamarruangan_nokamar'] . ' - ' . $value['no_tempattidur'];
                $value['nama_pasien'] = !empty($value['nama_pasien']) ? $value['nama_pasien'] : $value['namapasien_additional'];
                $value['no_telepon_pasien'] = $value['notlp_additional'];
                $value['toggle'] = "";
                $value['rowNum'] = $no;
                if (!empty($value['tgl_expired'])) {
                    $expire = strtotime($value['tgl_expired']);
                    $today = strtotime("today midnight");
                    if ($today > $expire AND $value['statusbooking'] == 371) {
                        $value['statusbooking'] = 369;
                        $value['status_booking'] = 'Ditolak';
                    }
                }
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

    function dateDifference($date_1 , $date_2 , $differenceFormat = '%h')
    {
        $datetime1 = date_create($date_1);
        $datetime2 = date_create($date_2);
        $interval = date_diff($datetime1, $datetime2);

        return $interval->format($differenceFormat);
        
    }

    public function actionView($id)
    {
        $request = Yii::$app->request;
        $title = Yii::t('fe', 'Lihat Data');
        $model = new PendaftaranForm();
        $modelPemesananKamar = new TraPemesananKamarForm();
        $modelPasien = new PasienForm();
        $modelPenanggungjawab = new PenanggungjawabForm();
        $modelRujukan = new RujukanForm();
        $modelRuangan = new RuanganForm();
        $id = DocoHelpers::decrypt($id);

        $response = $this->_restPendaftaran->get('inf-pemesanan-kamar/index?id='.$id);
        $body = json_decode($response->getBody(), TRUE);
        $attributes = $body['response'];
        $model->attributes = $attributes;
        return $this->renderPartial('index', get_defined_vars());
    }

    public function actionSetujui($id)
    {
        $booking_id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $post = $request->post();
        $pegawai_id = Yii::$app->docoVars->user('pegawai_id');
        Yii::$app->cache->set('data_booking' . $id . $pegawai_id, $post, 3600);
        $codeHttp = 200;
        return DocoHelpers::responseTemplate(
            $codeHttp, 
            "OK", 
            []
        );
    }

    public function actionDitolak($id)
    {
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $response = $this->_restPendaftaran->get('tra-pemesanan-kamar/ditolak?id=' . $id, [
                'form_params' => $post
            ]);

            $data = [
                'title' => \Yii::t('fe', 'Proses berhasil')." !",
                'text' => \Yii::t('fe', "Status berhasil diubah.")
            ];
            return DocoHelpers::responseTemplate(
                $response->getStatusCode(), 
                "OK", 
                [],
                $data
            );
        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    
    public function actionBatal($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restPendaftaran->put('tra-pemesanan-kamar/update?id='.$id, [
                'form_params' => ["status_booking" => 368]
            ]);

            $data = [
                'title' => \Yii::t('fe', 'Proses berhasil')." !",
                'text' => \Yii::t('fe', "Status berhasil diubah.")
            ];
            return DocoHelpers::responseTemplate(
                $response->getStatusCode(), 
                "OK", 
                [],
                $data
            );
        } catch (RequestException $e) {
            $data = [
                'title' => \Yii::t('fe', 'Proses gagal ')." !",
                'text' => \Yii::t('fe', "Status tidak berhasil dubah.")
            ];
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(), 
                json_decode($e->getResponse()->getBody()->getContents())->message,  
                [],
                $data
            );
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }


    public function actionCreate()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        try{
            if(!isset($post)){
                throw new Exception("Method Tidak Diijinkan", 500);
            }
            if(!isset($post['TraPemesananKamarForm'])){
                throw new Exception("Data Form Tidak Ditemukan", 500);
            }
            $dataform = $request->post('TraPemesananKamarForm');
            $simpanRequest = $this->_restPendaftaran->post('pemesanan-kamar/simpan-pesan-kamar',['form_params'=>$dataform]);
            $body = json_decode($simpanRequest->getBody(), true);
            $res_status = $response['metadata']['status'];

            if ($res_status == 200) {
                $data_dashboard['reload'] = 1;
                $mode = Yii::$app->params->mode;
                Yii::$app->redis->executeCommand('PUBLISH', [
                    'channel' => 'display-dashboard-kamar-'.$mode,
                    'message' => json_encode(['data' => $data_dashboard])
                ]);
            }
            return DocoHelpers::response($body,false,true);
        } catch(RequestException $e){
            // $body = json_decode($e->getResponse()->getBody(), true);
                return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
            // return DocoHelpers::responseTemplate(500,$e->getResponse()->getBody());
        } catch(Exception $e){
                return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
            // return DocoHelpers::responseTemplate(500,$e->getMessage());
        }
    }

    public function actionUpdate($id = null)
    {
        // Get request
        $request = Yii::$app->request;
        $post = $request->post();

        // Declare title
        $title = Yii::t('fe', 'Ubah Pemesanan Kamar');

        // Declare model
        $modelPemesananKamar = new TraPemesananKamarForm();
        $pasien = [];

        // Declare some variables
        $ddlJenisKasusPenyakit = [];
        $ddlKelasPelayanan = [];

        // Assign booking kamar id
        $bookingkamar_id = $id;

        // Check id
        if ($id != null) {
            // Decrypt id
            $id = DocoHelpers::decrypt($id);
        }

        // Try catch
        try {
            // Get all request
            $allReq = $this->_restPendaftaran->get('inf-pemesanan-kamar/get-bundle-data?id='.$id);
            $body = json_decode($allReq->getBody(), true);
            // Check model pemesanan kamar
            if (!empty($body['response']['pemesanan-kamar'])) {
                // Load model
                $modelPemesananKamar->load($body['response']['pemesanan-kamar'], '');

                // Change some value
                $modelPemesananKamar->tgl_transaksibooking = $body['response']['pemesanan-kamar']['tgl_transaksi'];
                $modelPemesananKamar->tgl_rawatinap = $body['response']['pemesanan-kamar']['tgl_pesan'];
                $modelPemesananKamar->bookingkamar_no = $body['response']['pemesanan-kamar']['no_pemesanan'];
                $modelPemesananKamar->ruangan_nama = $body['response']['pemesanan-kamar']['ruangan_nama'];
                $modelPemesananKamar->nokamar = $body['response']['pemesanan-kamar']['kamarruangan_nokamar'] . ' - ' . $body['response']['pemesanan-kamar']['no_tempattidur'];
                $modelPemesananKamar->kamarruangan_jenis = $body['response']['pemesanan-kamar']['kamarruangan_jenis'];
                $modelPemesananKamar->jenis_kelamin_booking = !empty($body['response']['pemesanan-kamar']['jeniskelamin'])
                    ? $body['response']['pemesanan-kamar']['jeniskelamin']
                    : $body['response']['pemesanan-kamar']['jk'];
            }

            // Check model pasien
            if (!empty($body['response']['pasien'])) {
                // Load model
                $pasien = $body['response']['pasien'];

                /* CALCULATE AGE */
                $date = new \DateTime($pasien['tanggal_lahir']);
                $now = new \DateTime();
                $interval = $now->diff($date);
                $age = $interval->y;

                // Assign umur
                $pasien['umur'] = $age;

                // Assign to model
                $modelPemesananKamar->no_identitas_pasien = $body['response']['pasien']['no_identitas_pasien'];
                $modelPemesananKamar->no_rekam_medik = $body['response']['pasien']['no_rekam_medik'];
                $modelPemesananKamar->nama_pasien = $body['response']['pasien']['nama_pasien'];
                $modelPemesananKamar->tempat_lahir = $body['response']['pasien']['tempat_lahir'];
                $modelPemesananKamar->tanggal_lahir = $body['response']['pasien']['tanggal_lahir'];
                $modelPemesananKamar->umur = $age;
                $modelPemesananKamar->jeniskelamin = $body['response']['pasien']['jeniskelamin'];
                $modelPemesananKamar->statusperkawinan = $body['response']['pasien']['statusperkawinan'];
                $modelPemesananKamar->nama_ibu = $body['response']['pasien']['nama_ibu'];
                $modelPemesananKamar->jenisidentitas = $body['response']['pasien']['jenisidentitas'];
                $modelPemesananKamar->nama_bin = $body['response']['pasien']['nama_bin'];
                $modelPemesananKamar->alamat_pasien = $body['response']['pasien']['alamat_pasien'];
                $modelPemesananKamar->pekerjaan_id = $body['response']['pasien']['pekerjaan_id'];
                $modelPemesananKamar->agama = $body['response']['pasien']['agama'];
            } else {
                $additional_data = $body['response']['pemesanan-kamar']['additional_data'];
                $data = json_decode($additional_data);

                $modelPemesananKamar->no_identitas_pasien = $data->no_identitas_pasien;
                $modelPemesananKamar->no_rekam_medik = $data->no_rekam_medik;
                $modelPemesananKamar->nama_pasien = $data->nama_pasien;
                $modelPemesananKamar->tempat_lahir = $data->tempat_lahir;
                $modelPemesananKamar->tanggal_lahir = $data->tanggal_lahir;
                $modelPemesananKamar->umur = $data->umur;
                $modelPemesananKamar->jeniskelamin = $data->jeniskelamin;
                $modelPemesananKamar->jenisidentitas = $data->jenisidentitas;
                $modelPemesananKamar->nama_bin = $data->nama_bin;
                $modelPemesananKamar->alamat_pasien = $data->alamat_pasien;
                $modelPemesananKamar->pekerjaan_id = $data->pekerjaan_id;
                $modelPemesananKamar->agama = $data->agama;
                $modelPemesananKamar->no_telepon_pasien = $data->no_telepon_pasien;
            }

            // Get for dropdown
            $ddlJenisKasusPenyakit = $body['response']['list-penyakit'];
            $ddlKelasPelayanan = $body['response']['list-kelas'];
            $ddlListPerkawinan = $body['response']['list-status-perkawinan'];
            $dllJenisKelamin = $body['response']['list-jenis-kelamin'];
            $ddlIdentitas = $body['response']['list-jenis-identitas'];
            $ddlPekerjaan = $body['response']['list-pekerjaan'];
            $ddlAgama = $body['response']['list-agama'];

            // Check data form
            if (isset($post['TraPemesananKamarForm'])) {
                // Dataform
                $dataform = $request->post('TraPemesananKamarForm');

                // Ubah request
                $ubahRequest = $this->_restPendaftaran->put('pemesanan-kamar/update?id='.$id, ['form_params' => $dataform]);
                $body = json_decode($ubahRequest->getBody(), true);

                // Return
                return json_encode($body['response']);
            }
            else {
                // Return
                return $this->render('index', get_defined_vars());
            }
        } catch(RequestException $e) {
            // Return
            return DocoHelpers::responseJsonString($e->getResponse()->getBody(), null);
        } catch(Exception $e) {
            // Return
            return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
        }
    }

    public function actionDelete($id)
    {
         return $this->render('index', get_defined_vars());
       
    }

    public function actionExport($id)
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionPrint($id)
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionExportAll()
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionPrintAll()
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionGetPasienRekamMedik()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){        

            $response = $this->_restPendaftaran->request('POST', 'allow/view-info-pasien',[
                            'form_params'=>['keyword'=>$_GET['q']['term']],
                        ]);            
            $body = json_decode($response->getBody(), true);

            $data = [];                                
            foreach ($body['response'] as $key => $value) {                
                $data[] = ['id'=>$value['no_rekam_medik'],'text'=>$value['nama_pasien']];
            }          
            $total = count($body['response']);      
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];                
            return DocoHelpers::response($return);
        }
    }

    public function actionGetListRuangan() {
        $request = Yii::$app->request;
        $post = $request->post();
        $depdrop_parents = $post['depdrop_parents'];

        $kasuspenyakit = empty($depdrop_parents[0]) ? null : $depdrop_parents[0];


        $depdrop_params = $post['depdrop_params'];
        $bookingkamar_id = empty($depdrop_params[0]) ? null : DocoHelpers::decrypt($depdrop_params[0]);

        $selected = '';
        $ddlRuangan = [];
        if($kasuspenyakit){
            try{
                $RuanganRequest = $this->_restPendaftaran->get('pemesanan-kamar/get-list-ruangan',['query'=>[
                    'jeniskasuspenyakit_id' => $kasuspenyakit,
                    'bookingkamar_id' => $bookingkamar_id
                ]]);
                $body = json_decode($RuanganRequest->getBody(),TRUE);
                $ddlRuangan = $body['response']['list-ruangan'];
                $selected = $body['response']['selectedData'];
            } catch(RequestException $e){
                $ddlRuangan = [];
            }
        }

        $out = [];
        foreach($ddlRuangan as $key => $value) {
            $out[] = [
                        'id' => $key,
                        'name' => $value
                    ];
        }

        return DocoHelpers::response(['output'=>$out, 'selected'=>$selected]);
    }

    public function actionGetInfoPasien()
    {
        $response = $this->_restPendaftaran->request('POST', 'pemesanan-kamar/get-pasien-by-rm',[
                        'form_params'=>['no_rekam_medik'=>$_GET['no_rm']],
                    ]);

        $body = json_decode($response->getBody(), true);
        $data = $body['response'];               
        $data['umur_pasien'] = DocoHelpers::getUmur($data['tanggal_lahir'],true);
        return DocoHelpers::response($data);
    }

    // DEPRECATED, moved to endpointController
    // public function actionPilihTempatTidur()
    // {
    //     $request = Yii::$app->request;
    //     $title = Yii::t('fe', 'Pilih Tempat Tidur');
    //     $ruangan_id = $request->get('ruangan_id');
    //     $jenis_id = $request->get('jenis_id');
    //     $kelas_id = $request->get('kelas_id');
    //     return $this->renderAjax('_pemilihan_tempat_tidur', get_defined_vars());
    // }

    // public function actionGetDataKamar()
    // {
    //      Yii::$app->response->format = Response::FORMAT_JSON;
    //     $request = Yii::$app->request;   
    //     $jenis_id = $request->get('jenis_id');
    //     $kelas_id = $request->get('kelas_id');
    //     $ruangan_id = $request->get('ruangan_id');
    //     $draw = $request->get('draw',1);
    //     $data = [];
    //     try {                                           
    //         $response = $this->_restPendaftaran->get('pemesanan-kamar/get-data-kamar',['form_params'=>[],
    //             'query' => ['jeniskasuspenyakit_id'=>$jenis_id,'kelaspelayanan_id'=>$kelas_id,'ruangan_id'=>$ruangan_id]
    //         ]);
    //         $body = json_decode($response->getBody(), true);            
    //         $no = $request->get('start',1);                  
    //         // foreach ($body['response']['data'] as $key => $value) {
    //         //     $no++;
    //             // $primaryKey = DocoHelpers::encrypt($value['mutasiobatruangan_id']);
    //             // $value['primary'] = $primaryKey;
    //             // unset($value['mutasiobatruangan_id']);                
                
    //         //     $value['rowNum'] = $no;
    //         //     $data[$key] = $value;
    //         // }
    //         $result['data'] = $this->listRuangan($body['response']['list-ruangan'],$body['response']['data']);
    //         // $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
    //         // $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

    //         $result['recordsTotal'] = '';
    //         $result['recordsFiltered'] = '';

    //         return $result;

    //     } catch (RequestException $e) {
    //         $result['error'] = $e->getMessage();
    //         return $result;
    //     } catch (\Exception $e) {
    //         $result['error'] = $e->getMessage();
    //         return $result;
    //     }
    // }

    // public function listRuangan($index,$data)
    // {
    //     function listKamar($id,$data){
    //         $buttonList = [];
    //         foreach ($data as $key => $value) {
    //             if($value['ruangan_id'] == $id){
    //                 $buttonList[] = "<input style='margin-bottom:8px;background-color:{$value['kode_warna']};' data-dismiss='modal' aria-hidden='true' data-kamartempattidur_id='".$value['kamartempattidur_id']."' data-kamarruangan_id='".$value['kamarruangan_id']."' data-bookingkamar_no='".$value['no_tempattidur']."'  type='button' class='pilih-kamar btn btn-danger-custom btn-xs' onClick='pilihKamar(this)' value='" . $value['kamarruangan_nokamar'] . "'>";
    //             }
    //         }
    //         if(count($buttonList) == 0) {
    //             $buttonList[] = "<p>--Tempat Tidur Tidak Tersedia--</p>";
    //         }
    //         return implode(' ', $buttonList);
    //     }

    //     $data_kamar = [];
    //     $no = 1;
    //     foreach ($index as $key => $value) {
    //         $value['rowNum'] = $no;
    //         $value['datakamar'] = listKamar($value['ruangan_id'],$data);
    //         $data_kamar[$key] = $value;
    //     }
    //     $no++;
    //     return $data_kamar;
    // }
    public function actionCetakPemesanan($id)
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/bukti-pemesanan-kamar.pdf";
        try {
            $id = $request->get('id',null);
            $id = DocoHelpers::decrypt($id);
            $post = ['id'=>$id];
            $response = $this->_restPendaftaran->get('tra-pemesanan-kamar/cetak-pemesanan', [
                'query' => $post,
                'save_to' => $path
            ]);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {  
            echo "<pre>";
            print_r($e); die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (Exception $e) { 
            var_dump($e); die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    private function getData()
    {
        try {
            $response = $this->_restPendaftaran->request('GET', 'inf-pemesanan-kamar/generate-api');
            $body = json_decode($response->getBody(),TRUE);

            $return = [
                'data_ruangan' => $body['response']['data-ruangan'],
                'data_status' => $body['response']['data-status'],
            ];

            return $return;
        } catch (RequestException $e) {
            // dump($e);exit;
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionCheckKunjungan($no_rekam_medik)
    {
        try {
            $response = $this->_restPendaftaran->request('GET', 'inf-pemesanan-kamar/check-kunjungan?no_rekam_medik=' . $no_rekam_medik);
            $body = json_decode($response->getBody(),TRUE);

            return DocoHelpers::response($body);
        } catch (RequestException $e) {
            // dump($e);exit;
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
