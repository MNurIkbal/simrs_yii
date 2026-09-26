<?php
// Author : Naufal Ziyad L

namespace Doco\pendaftaran\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoConstants;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\pendaftaran\models\InfReservasiPoliklinikForm;
use app\modules\pendaftaran\models\TraReservasiPoliklinikForm;
use app\modules\pendaftaran\models\ReservasiPoliklinikForm;
use app\modules\rm\models\PasienForm;
use app\modules\master\models\RuanganForm;
use app\modules\rm\models\PegawaiForm;
use app\modules\pendaftaran\models\PasienForm as PasienFormPendaftaran;
use GuzzleHttp\Exception\RequestException;


use app\modules\pendaftaran\components\traits\PendaftaranTrait;

class ReservasiPoliklinikController extends DocoController
{
    use PendaftaranTrait;

    protected $_title = "Pendaftaran :: Reservasi Poliklinik";
    protected $_module = 'pendaftaran/reservasi-poliklinik/';
    protected $_restMaster;
    protected $_restPendaftaran;
    protected $allowAction = ['*'];

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

    public function actionCreate() //Sebelumnya actionIndex
    {
        $title = $this->_title;
        $request = Yii::$app->request;
        $model = new TraReservasiPoliklinikForm;
        $modelPasien = new PasienForm;
        $modelRuangan = new RuanganForm();
        $modelPegawai = new PegawaiForm();
        $status = $this->_status; $options = $this->_options;

        $carabayarRequest = $this->_restMaster->get('cara-bayar/list-cara-bayar');
        $body = json_decode($carabayarRequest->getBody(),TRUE);
        $carabayar = $body['response'];

        $responsePegawai = $this->_restMaster->get('pegawai/allow-list-dokter-rajal');
        $body = json_decode($responsePegawai->getBody(), TRUE);
        $pegawai = $body['response'];

        $JenisIDRequest = $this->_restMaster->get('lookup/list-lookup-by-type?param=jenis_identitas');
        $body = json_decode($JenisIDRequest->getBody(),TRUE);
        $ddlJenisID = $body['response'];

        $NamaDepanRequest = $this->_restMaster->get('lookup/list-lookup-by-type?param=nama_depan');
        $body = json_decode($NamaDepanRequest->getBody(),TRUE);
        $ddlNamaDepan = $body['response'];

        $JenisKelaminRequest = $this->_restMaster->get('lookup/list-lookup-by-type?param=jenis_kelamin');
        $body = json_decode($JenisKelaminRequest->getBody(),TRUE);
        $ddlJenisKelamin = $body['response'];

        $RuanganRequest = $this->_restMaster->get('ruangan/list-ruangan');
        $body = json_decode($RuanganRequest->getBody(),TRUE);
        $ddlRuangan = $body['response'];

        $penjaminRequest = $this->_restMaster->get('penjamin/list-penjamin?carabayar_id='.$model->carabayar_id);
        $body = json_decode($penjaminRequest->getBody(),TRUE);
        $penjaminList = $body['response'];

        if($request->post()){
            $post = $request->post();
            $model->load($post);
            $response = $this->_restPendaftaran->request('POST', 'tra-reservasi-poliklinik/create',[
                                'form_params'=>$model->attributes,
                            ]);
            $response = json_decode($response->getBody(), true);
            return DocoHelpers::response($response, false, true);
        }
            return $this->render('index', get_defined_vars());
        }


    public function actionUpdate($id)
    {
        // Init
        $id = DocoHelpers::decrypt($id);
        $status = $this->_status; $options = $this->_options;
        $request = Yii::$app->request;
        $model = new TraReservasiPoliklinikForm;
        $modelPasien = new PasienForm;
        $modelInformasi = new InfReservasiPoliklinikForm;
        $modelRuangan = new RuanganForm();
        $modelPegawai = new PegawaiForm();
        //$model->scenario = 'update';
        $title = \Yii::t('fe', 'Ubah').' '.\Yii::t('fe', $this->_title);

        $formName = substr(strrchr(get_class($model), "\\"), 1);

        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restPendaftaran->put('tra-reservasi-poliklinik/update?id='.$id, [
                        'form_params' => $model->attributes,
                    ]);
                    return DocoHelpers::responseJsonString($response->getBody(), $formName);
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            $response = $this->_restPendaftaran->get('tra-reservasi-poliklinik/view?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'];
            $model->attributes = $attributes;

            $carabayarRequest = $this->_restMaster->get('cara-bayar/list-cara-bayar');
            $body = json_decode($carabayarRequest->getBody(),TRUE);
            $carabayar = $body['response'];

            $responsePegawai = $this->_restMaster->get('pegawai/allow-list-dokter-rajal');
            $body = json_decode($responsePegawai->getBody(), TRUE);
            $pegawai = $body['response'];

            $JenisIDRequest = $this->_restMaster->get('lookup/list-lookup-by-type?param=jenis_identitas');
            $body = json_decode($JenisIDRequest->getBody(),TRUE);
            $ddlJenisID = $body['response'];

            $NamaDepanRequest = $this->_restMaster->get('lookup/list-lookup-by-type?param=nama_depan');
            $body = json_decode($NamaDepanRequest->getBody(),TRUE);
            $ddlNamaDepan = $body['response'];

            $JenisKelaminRequest = $this->_restMaster->get('lookup/list-lookup-by-type?param=jenis_kelamin');
            $body = json_decode($JenisKelaminRequest->getBody(),TRUE);
            $ddlJenisKelamin = $body['response'];

            $RuanganRequest = $this->_restMaster->get('ruangan/list-ruangan');
            $body = json_decode($RuanganRequest->getBody(),TRUE);
            $ddlRuangan = $body['response'];

            $penjaminRequest = $this->_restMaster->get('penjamin/list-penjamin?carabayar_id='.$model->carabayar_id);
            $body = json_decode($penjaminRequest->getBody(),TRUE);
            $penjaminList = $body['response'];

            return $this->render('form_update', get_defined_vars());
        }
    }

    public function actionGetDataInformasi()
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
            $response = $this->_restPendaftaran->get('inf-reservasi-poliklinik/index', [
                'query' => $yiiRestfulParams
            ]);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pendaftaranol_id']);
                unset($value['pendaftaranol_id']);
                $setNamaPasien = empty($value['nama_pasien']) ? $value['nama_pasien_ol'] : $value['nama_pasien'];
                $setNoRm = empty($value['no_rekam_medik']) ? "-" : $value['no_rekam_medik'];
                $value['antrian'] = $value['no_antrian'];
                if(!empty($value['antrian_dokter'])) {
                    $value['antrian'] = $value['antrian_dokter'];
                }
                $value['info_pasien'] = $value['no_pendaftaranol'] . '<br>' . $setNoRm . ' <br/> ' . $value['nama_depan'] . ' ' . $setNamaPasien . ' <br/> ' . $value['jk'];
                $value['aksi']  = '<table class="no-border"><tr>';
                $value['aksi'] .='<td>&nbsp;'. Html::a(
                    '<i class="fa fa-pencil" aria-hidden="true"></i>',
                    Url::home().$this->_module.'update?id='.$primaryKey,
                    [
                        'class' => 'btn btn-dark-turquise btn-xs',
                        'data-popup' => "tooltip",
                        'data-placement' => 'bottom',
                        'title' => Yii::t('fe', 'Ubah'),
                    ]
                ).'&nbsp;</td>';
                $value['aksi'] .= '<td>&nbsp;'.Html::a(
                    '<i class="fa fa-check-square-o" aria-hidden="true"></i>','#',
                    [
                        'class' => 'btn btn-success btn-xs data-setuju',
                        'data-placement' => 'bottom',
                        'action' => Url::home().$this->_module.'setujui?id='.$primaryKey,
                        'data-popup' => "tooltip",
                        'title' => \Yii::t('fe', 'Setujui'),
                    ]
                ).'&nbsp;</td>';
                $value['aksi'] .= '<td>&nbsp;'.Html::a(
                    '<i class="fa fa-times-circle-o" aria-hidden="true"></i>','#',
                    [
                        'class' => 'btn btn-danger  btn-xs data-ditolak',
                        'action' => Url::home().$this->_module.'ditolak?id='.$primaryKey,
                        'data-popup' => "tooltip",
                        'title' => \Yii::t('fe', 'Tolak'),
                    ]
                ).'&nbsp;</td>';
                $value['aksi'] .= '<td>&nbsp;'.Html::a(
                    '<i class="fa fa-times" aria-hidden="true"></i>','#',
                    [
                        'class' => 'btn btn-indian-red btn-xs data-batal',
                        'action' =>  Url::home().$this->_module.'batal?id='.$primaryKey,
                        'data-popup' => "tooltip",
                        'title' => \Yii::t('fe', 'Batal'),
                    ]
                ).'&nbsp;</td>';
                $value['aksi'] .= '</tr></table>';
                $value['toggle'] = "";
                $value['jampelayanan'] = date('H:i', strtotime($value['jam_mulai'])) .' - '. date('H:i', strtotime($value['jam_tutup']));
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['poli_dokter'] = $value['ruangan_nama'].' - <br>'.$value['nama_pegawai'].'<br>'.ArrayHelper::getValue($value,'hari_poli').'<br>'.ArrayHelper::getValue($value,'jam_mulai').' - '.ArrayHelper::getValue($value,'jam_tutup');
                $value['carabayar_penjamin'] = $value['carabayar_nama'];
                $value['tgl_kunjungan_formated'] = date('Y-m-d', strtotime($value['tgl_kunjungan']));
                $value['tgl_pendaftaran'] = DocoHelpers::convDateTime($value['tgl_pendaftaran']);
                $value['tgl_kunjungan'] = DocoHelpers::convertDate($value['tgl_kunjungan']) . '<br>' . $value['jam_kunjungan'];
                $value['jam_kunjungan'] = $value['jam_kunjungan'];
                $value['tanggal_lahir'] = date('d M Y', strtotime($value['tanggal_lahir']));
                $value['no_telepon_pasien'] = empty($value['no_telepon_pasien']) ? $value['no_telepon_pasien_ol'] : $value['no_telepon_pasien'];
                $value['no_pendaftaran'] = $value['no_pendaftaranol'];
                $value['is_checkin_origin'] = $value['is_checkin'];
                $value['is_checkin'] = $value['is_checkin'] ? 'Sudah Check-in' : 'Belum Check-in';
                $value['logged_user'] = Yii::$app->user->identity->nama;
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

    public function actionView($id)
    {


    }

    public function actionInformasi()
    {
        $session = Yii::$app->session;
        $title = 'Informasi Reservasi Poliklinik';
        $model = new InfReservasiPoliklinikForm;
        $status = $this->_status; $options = $this->_options;
        $reportEngine = Yii::$app->report->enabled;
        $userLogin = Yii::$app->docoVars->user('id_pegawai');
        $keteranganCetakTracer = [
            [
                'id' => 1,
                'kode_warna' => '#FFFFFF',
                'text' => 'Belum Cetak Tracer'
            ],
            [
                'id' => 2,
                'kode_warna' => '#05BBBE',
                'text' => 'Sudah Cetak Tracer'
            ]
        ];

        try{
            $activeWorkspace = $session->get('active_workspace');
            if(is_null($activeWorkspace['loket'])){
                return $this->redirect(['/pendaftaran/daftar/pilih-loket?jenisantrian_id='.DocoConstants::JA_PDN.'&redirect=pendaftaran/reservasi-poliklinik/informasi']);
            }

            $responseRuangan = $this->_restPendaftaran->get('inf-reservasi-poliklinik/bundle-informasi',
                [
                    'query' => ['is_executive' => Yii::$app->request->get('is_executive') == 'true'],
                ]
            );

            $body = json_decode($responseRuangan->getBody(), TRUE);
            $result = $body['response'];
        } catch(RequestException $e){
            $result = [];
        } catch(\Exception $e){
            $result = [];
        }
        return $this->render('_informasi_reservasi_poliklinik', compact('title', 'model', 'model_pasien', 'status', 'reportEngine', 'keteranganCetakTracer', 'result', 'userLogin'));
    }

    public function actionSetujui($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restPendaftaran->put('tra-reservasi-poliklinik/update?id='.$id, [
                'form_params' => ["status_janjipoli" => 356]
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

    public function actionDitolak($id)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restPendaftaran->put('tra-reservasi-poliklinik/update?id='.$id, [
                'form_params' => ["status_daftar_ol" => 566]
            ]);

            $payload = [
                "kodebooking" => $request->post('noBoking'),
                "keterangan" => "Kunjungan anda belum bisa dilanjutkan. Anda bisa menghubungi CS Rumah Sakit untuk konfirmasi kapan bisa berkunjung kembali. Mohon maaf untuk ketidaknyamanan ini. Terimakasih atas pengertiannya."
            ];
            $batal = $this->guzzleExec($this->_restPendaftaran, [
                'url' => "api/batal-antrol",
                'payload' => [
                    'form_params' => $payload
                ],
            ]);

            $logJkn = [
                "pendaftaran_id" => $id,
                "taskId" => 99,
                "data" => $payload,
                "response" => $batal
            ];
            $loginJknR = $this->setLogJknR($logJkn);

            $data = [
                'title' => \Yii::t('fe', 'Proses berhasil')." !",
                'text' => \Yii::t('fe', "Reservasi Berhasil Ditolak.")
            ];
            return DocoHelpers::responseTemplate(
                $response->getStatusCode(),
                "OK",
                [],
                $data
            );
        } catch (RequestException $e) {
            $response = json_decode($e->getResponse()->getBody()->getContents(), true);
            $data = [
                'title' => \Yii::t('fe', 'Proses gagal ')." !",
                'message' => $e->getResponse()->getStatusCode() < 500 ? ArrayHelper::getValue($response, 'response.message'): \Yii::t('fe', "Status tidak berhasil dubah.")
            ];
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(),
                $e->getMessage(),
                [],
                $data
            );
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionBatal($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restPendaftaran->put('tra-reservasi-poliklinik/update?id='.$id, [
                'form_params' => ["status_janjipoli" => 354]
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

    //action buat handle data no rekam medik
    public function actionGetRekamMedik()
    {
        try{
            if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
                $response = $this->_restPendaftaran->request('POST', 'tra-reservasi-poliklinik/get-data-rekam-medik',[
                                'form_params'=>['term'=>$_GET['q']['term']],
                            ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {

                    $data[] = ['id'=>$value['no_rekam_medik'],'text'=>$value['no_rekam_medik']];

                }
                $total = count($body['response']);
                $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
                return DocoHelpers::response($return);
            }
        }catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionGetUmur()
    {
        try{
            if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
                $response = $this->_restPendaftaran->request('POST', 'tra-reservasi-poliklinik/umur',[
                                'form_params'=>['term'=>$_GET['q']['term']],
                            ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {

                    $data[] = ['id'=>$value['no_rekam_medik'],'text'=>$value['tanggal_lahir']];

                }
                $total = count($body['response']);
                $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
                return DocoHelpers::response($return);
            }
        }catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    //action buat ambil data pasien ketika select2 rekam medik berubah
    public function actionGetData($id){
        try {
            $request = Yii::$app->request;
            $id = $_GET['id'];
            $response = $this->_restPendaftaran->request('GET', 'tra-reservasi-poliklinik/get-data',['query' => ['id' => $id ]]);
            $body = json_decode($response->getBody(), true);

            return DocoHelpers::response($body['response']);

        } catch (\Exception $e) {

        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionListPenjamin() {
        $request = Yii::$app->request;
        $post = $request->post();
        $carabayar_id = $post['depdrop_parents'][0];

        $penjaminRequest = $this->_restPendaftaran->get('allow/list-penjamin?carabayar_id='.$carabayar_id);
        $body = json_decode($penjaminRequest->getBody(),TRUE);
        $responses = $body['response'];

        $out = [];
        foreach($responses as $key => $response) {
            $out[] = [
                'id' => $key,
                'name' => $response
            ];
        }

        echo json_encode(['output'=>$out, 'selected'=>'']);
        return;
    }

    public function actionDelete($id)
    {

    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $path = Yii::getAlias("@download") . "/informasi-reservasi-poli.xlsx";
            $response = $this->_restPendaftaran->get('inf-reservasi-poliklinik/export-excel',[
                'query' => $yiiRestfulParams,
                'save_to' => $path
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

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $post = $request->post();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        $path = Yii::getAlias("@download") . "/informasi-reservasi-poli.pdf";
        try {
            $response = $this->_restPendaftaran->get('inf-reservasi-poliklinik/export-pdf',[
                'query' => $yiiRestfulParams,
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            var_dump($e->getMessage()); die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage()); die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
    public function actionGetPenjamin($assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $parent_label = '';
        if ($request->post()) {
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
        }

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restPendaftaran->get('allow/list-penjamin', [
                'query' => [
                    'carabayar_id' => $parent_label
                ]
            ]);
            $body = json_decode($response->getBody(), True);
            return $body['response'];
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
    public function actionGetCarabayar($assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];
        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restApotek->get('allow/list-carabayar', [
                'query' => [
                    'carabayar_id' => $parent_label
                ]
            ]);
            $body = json_decode($response->getBody(), True);
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    /**
     * @todo Fungsi untuk menampilkan form tambah dan melakukan proses simpan reservasi poli
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionIndex() //sebelumnya actionCreate
    {
        try {
            $model = new ReservasiPoliklinikForm;
            $modelPasien = new PasienFormPendaftaran();
            $modelPasien->scenario = "pendaftaran-rajal";
            $namaForm = substr(strrchr(get_class($model), "\\"), 1);
            $post = Yii::$app->request->post();
            $userIdentity = Yii::$app->session->get('user_identity');
            $dataForm = $this->getDataApi();

            if (!empty($post)) {
                if ($post['konfig_antrian'] == DocoConstants::VAR_ID_KUOTA_ANTRIAN_POLIKLINIK) {
                    $post['ReservasiPoliklinikForm']['jadwaldokter_id'] = null;
                } else {
                    $post['ReservasiPoliklinikForm']['jadwalbukapoli_id'] = null;
                    if(isset($post['is_nomor_urut'])) {
                        if($post['is_nomor_urut'] == 'true' || $post['is_nomor_urut'] == true) {
                            $model->scenario = 'scenario_nomor_urut_manual';
                        }
                    }
                }
                $post['ReservasiPoliklinikForm']['user_id'] = $userIdentity['id'];

                if ($post['PasienForm']['type'] == 0) {
                    if ($post['ReservasiPoliklinikForm']['pasien_id'] == '') {
                        return DocoHelpers::responseTemplate(
                            422,
                            'Error',
                            [],
                            [
                                'title' => Yii::t('fe', 'Peringatan').'!',
                                'text' => Yii::t('fe', 'Pasien belum dipilih.')
                            ]
                        );
                    }
                } else {
                    if (!$modelPasien->load($post['PasienForm'], '')) {
                        $error = $modelPasien->getErrors();
                        return DocoHelpers::response($error, 422, 'PasienForm');
                    }

                    if (!$modelPasien->validate()) {
                        $error = $modelPasien->getErrors();
                        return DocoHelpers::response($error, 422, 'PasienForm');
                    }
                }

                if (!$model->load($post['ReservasiPoliklinikForm'], '')) {
                    $error = $model->getErrors();
                    return DocoHelpers::response($error, 422, $namaForm);
                }

                if (!$model->validate()) {
                    $error = $model->getErrors();
                    return DocoHelpers::response($error, 422, $namaForm);
                }

                $dataPasienBaru = [
                    'jenisidentitas' => $modelPasien->jenisidentitas,
                    'no_identitas_pasien' => $modelPasien->no_identitas_pasien,
                    'namadepan' => $modelPasien->namadepan,
                    'nama_pasien' => $modelPasien->nama_pasien,
                    'tempat_lahir' => $modelPasien->tempat_lahir,
                    'tanggal_lahir' => $modelPasien->tanggal_lahir,
                    'jeniskelamin' => $modelPasien->jeniskelamin,
                    'no_telepon_pasien' => $modelPasien->no_telepon_pasien,
                    'alamat_pasien' => $modelPasien->alamat_pasien,
                    'tipe_pasien' => (int)$post['PasienForm']['type']
                ];

                $dataSubmit = array_merge($model->attributes, $dataPasienBaru);

                $restPendaftaran = $this->_restPendaftaran->post('pendaftaran-online/daftar-online', [
                    'form_params' => $dataSubmit
                ]);
                $response = json_decode($restPendaftaran->getBody(), true);
                return DocoHelpers::response($response);
            } else {
                $restPendaftaran = $this->_restPendaftaran->post('reservasi-poliklinik/get-bundle-data');
                $response = json_decode($restPendaftaran->getBody(), true);
                $bundleData = $response['response'];
                $caraBayar = $bundleData['caraBayar'];
                $konfigSystem = $bundleData['konfigSystem'];
                $kuotaAntrian = $konfigSystem['kuota_antrian'];
                $kuotaDokter = true;
                $reservasiAwal = 0;
                $reservasiAkhir = 0;

                $carabayarOptions = [];
                foreach ($caraBayar as $key => $value) {
                    $carabayarOptions[$value['carabayar_id']] = ['data-id' => $value['groupcarabayar_id']];
                }

                if (isset($konfigSystem['reservasi_awal']) && $konfigSystem['reservasi_awal'] != '' && $konfigSystem['reservasi_awal']  > 0) {
                    $reservasiAwal = $konfigSystem['reservasi_awal'];
                }

                if (isset($konfigSystem['reservasi_akhir']) && $konfigSystem['reservasi_akhir'] != '' && $konfigSystem['reservasi_akhir']  > 0) {
                    $reservasiAkhir = $konfigSystem['reservasi_akhir'];
                }

                if (isset($konfigSystem['kuota_antrian']) && $konfigSystem['kuota_antrian'] == DocoConstants::VAR_ID_KUOTA_ANTRIAN_POLIKLINIK) {
                    $kuotaDokter = false;
                }

                if(isset($konfigSystem['is_nourut']) && $konfigSystem['is_nourut'] == true) {
                    $is_nourut = true;
                    $model->scenario = 'scenario_nomor_urut_manual';
                }
                return $this->render('form', get_defined_vars());
            }
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    /**
     * @todo Fungsi untuk mendapatkan data pasien
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetPasien()
    {
        try {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $param = $request->get('q', null);
            $id = $request->get('id', null);
            $results = [];

            if ($param) {
                $restPendaftaran = $this->_restPendaftaran->get('reservasi-poliklinik/get-pasien', [
                    'query' => [
                        'q' => $param
                    ]
                ]);
                $response = json_decode($restPendaftaran->getBody(), true);
                $status = $response['metadata']['status'];

                if ($status == 200) {
                    $listPasien = $response['response'];

                    foreach ($listPasien as $key => $value) {
                        $noRekamMedik = $value['no_rekam_medik'] != '' ? $value['no_rekam_medik'] : '-';
                        $namaPasien = $value['nama_pasien'] != '' ? $value['nama_pasien'] : '-';
                        $noBpjs = isset($value['nopeserta_bpjs'])? $value['nopeserta_bpjs'] : '-';
                        $no_telepon_pasien = isset($value['no_telepon_pasien']) && $value['no_telepon_pasien'] != '-'? $value['no_telepon_pasien'] : $value['no_mobile_pasien'];
                        $noTelp = $no_telepon_pasien != null && $no_telepon_pasien != ''? $no_telepon_pasien : '-';
                        $text = $noRekamMedik.' / '.$namaPasien.' / '.$noBpjs.' / '.$noTelp;
                        $results[] = [
                            'id' => $value['pasien_id'],
                            'text' => $text,
                        ];
                    }
                }
            }

            if ($id) {
                $restPendaftaran = $this->_restPendaftaran->get('reservasi-poliklinik/get-pasien', [
                    'query' => [
                        'id' => $id
                    ]
                ]);
                $response = json_decode($restPendaftaran->getBody(), true);
                $results = $response['response'];
                foreach ($results as $key => $value) {
                    $results[$key] = ($value != '' ? $value : '-');
                }
            }

            return ['results' => $results];
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    // /**
    //  * @todo Fungsi untuk mendapatkan data jadwal buka poli
    //  * @author Sigit Arif Munandar <sigit@docotel.com>
    //  */
    // public function actionGetRuangan()
    // {
    //     try {
    //         $request = Yii::$app->request;
    //         $post = $request->post();
    //         $tglPendaftaran = !empty($post['depdrop_parents'][0]) ? $post['depdrop_parents'][0] : null;
    //         $out = [];

    //         if ($tglPendaftaran) {
    //             $tglPendaftaran = DocoHelpers::convDateTime($tglPendaftaran.' 23:59:59');
    //             $hariId = date('N', strtotime($tglPendaftaran)) + 74;
    //             $restPendaftaran = $this->_restPendaftaran->get('pendaftaran-online/get-ruangan', [
    //                 'query' => ['hari_id' => $hariId]
    //             ]);
    //             $body = json_decode($restPendaftaran->getBody(), true);
    //             $response = $body['response'];
    //             if (!empty($response)) {
    //                 foreach ($response as $key => $value) {
    //                     $out[] = [
    //                         'id' => $value['ruangan_id'],
    //                         'name' => $value['ruangan_nama'],
    //                     ];
    //                 }
    //             }
    //         }

    //         echo json_encode([
    //             'output' => $out,
    //             'selected' => ''
    //         ]);
    //         return;
    //     } catch (\Exception $e) {
    //         throw new \yii\web\HttpException(500, $e->getMessage());
    //     } catch (RequestException $e) {
    //         throw new \yii\web\HttpException(500, $e->getMessage());
    //     }
    // }

    /**
     * @todo Fungsi untuk mendapatkan data jadwal buka poli
     * @author Aris Munandar <aris.m@docotel.com>
     */
    public function actionGetRuangan()
    {
        try {
            $request        = Yii::$app->request;
            $post           = $request->post();
            $tglPendaftaran = !empty($post['depdrop_parents'][0]) ? $post['depdrop_parents'][0] : null;
            $out            = [];

            if ($tglPendaftaran) {
                $tglPendaftaran  = DocoHelpers::convDateTime($tglPendaftaran.' 23:59:59');
                $hariId          = date('N', strtotime($tglPendaftaran)) + 74;
                $restPendaftaran = $this->_restPendaftaran->get('pendaftaran-online/get-ruangan', [
                    'query' => [
                        'hari_id' => $hariId,
                        'tanggal' => $tglPendaftaran
                    ]
                ]);
                $body = json_decode($restPendaftaran->getBody(), true);
                if($body['metadata']['status'] > 200) {
                    return DocoHelpers::response($body);
                } else {
                    $response = $body['response'];
                    if (!empty($response)) {
                        foreach ($response as $key => $value) {
                            $out[] = [
                                'id'   => $value['ruangan_id'],
                                'name' => $value['ruangan_nama'],
                            ];
                        }
                    }
                }
            }

            return json_encode([
                'output'   => $out,
                'selected' => ''
            ]);
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    // /**
    //  * @todo Fungsi untuk mendapatkan data jadwal dokter
    //  * @author Sigit Arif Munandar <sigit@docotel.com>
    //  */
    // public function actionGetDokter()
    // {
    //     try {
    //         $request = Yii::$app->request;
    //         $post = $request->post();
    //         $tglPendaftaran = !empty($post['depdrop_parents'][0]) ? $post['depdrop_parents'][0] : null;
    //         $ruanganId = !empty($post['depdrop_parents'][1]) ? $post['depdrop_parents'][1] : null;
    //         $hariId = null;
    //         $params = '';
    //         $out = [];

    //         if ($tglPendaftaran) {
    //             $tglPendaftaran = DocoHelpers::convDateTime($tglPendaftaran.' 23:59:59');
    //             $hariId = date('N', strtotime($tglPendaftaran)) + 74;
    //         }

    //         if ($hariId) {
    //             $params = $params.'hari_id='.$hariId.'&';
    //         }

    //         if ($ruanganId) {
    //             $params = $params.'ruangan_id='.$ruanganId;
    //         }

    //         if ($params != '') {
    //             if ($tglPendaftaran) {
    //                 $tglPendaftaran = DocoHelpers::convDateTime($tglPendaftaran.' 23:59:59');
    //                 $hariId = date('N', strtotime($tglPendaftaran)) + 74;
    //             }

    //             $restPendaftaran = $this->_restPendaftaran->get('pendaftaran-online/get-dokter', [
    //                 'query' => [
    //                     'hari_id' => $hariId,
    //                     'ruangan_id' => $ruanganId
    //                 ]
    //             ]);
    //             $body = json_decode($restPendaftaran->getBody(), true);
    //             $response = $body['response'];

    //             if (!empty($response)) {
    //                 foreach ($response as $key => $value) {
    //                     $out[] = [
    //                         'id' => $value['pegawai_id'],
    //                         'name' => $value['nama_pegawai'],
    //                     ];
    //                 }
    //             }
    //         }

    //         echo json_encode([
    //             'output' => $out,
    //             'selected' => ''
    //         ]);
    //         return;
    //     } catch (\Exception $e) {
    //         throw new \yii\web\HttpException(500, $e->getMessage());
    //     } catch (RequestException $e) {
    //         throw new \yii\web\HttpException(500, $e->getMessage());
    //     }
    // }

    /**
     * @todo Fungsi untuk mendapatkan data jadwal dokter
     * @author Aris Munandar <aris.m@docotel.com>
     */
    public function actionGetDokter($is_from_reservasi = false, $dokter_id=null, $booked_jadwaldokter_id = null)
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $tglPendaftaran = !empty($post['depdrop_parents'][0]) ? $post['depdrop_parents'][0] : null;
            $ruanganId = !empty($post['depdrop_parents'][1]) ? $post['depdrop_parents'][1] : null;
            $jenisReservasi = (int) $request->get('jenis_reservasi');
            $hariId = null;
            $params = '';
            $result =[];
            $out = [];
            if ($tglPendaftaran) {
                $tglPendaftaran = ($is_from_reservasi) ? date('Y-m-d H:i:s',strtotime($tglPendaftaran))  : DocoHelpers::convDateTime($tglPendaftaran.' 23:59:59');
                $hariId = date('N', strtotime($tglPendaftaran)) + 74;
            }

            if ($hariId) {
                $params = $params.'hari_id='.$hariId.'&';
            }


            if ($ruanganId) {
                $params = $params.'ruangan_id='.$ruanganId;
            }

            $exceptValue = ["null","","Tidak ada data"];
            if (!in_array($hariId, $exceptValue) && !in_array($ruanganId, $exceptValue)) {

                $restPendaftaran = $this->_restPendaftaran->get('pendaftaran-online/get-dokter', [
                    'query' => [
                        'hari_id' => $hariId,
                        'ruangan_id' => $ruanganId,
                        'tanggal' => $tglPendaftaran
                    ]
                ]);
                $body = json_decode($restPendaftaran->getBody(), true);
                if($body['metadata']['status'] > 200) {
                    return DocoHelpers::response($body);
                } else {
                    $response = $body['response'];
                    if (!empty($response)) {
                        foreach ($response as $key => $value) {
                            $option_value = [
                                'id' => $value['pegawai_id'],
                                'name' => $value['nama_pegawai'],
                            ]; 

                            $jam_mulai = date('H:i', strtotime(ArrayHelper::getValue($value, 'waktu_mulai')));
                            $jam_selesai = date('H:i', strtotime(ArrayHelper::getValue($value, 'waktu_selesai')));
                            $jam_praktek = ' (' . $jam_mulai . ' - ' . $jam_selesai . ')';

                            if ($request->get('with_kuota')) {
                                if ($jenisReservasi == DocoConstants::JNS_RESERVASI_MQARE) {
                                    $option_value['options'] = ['disabled' => true];    
                                }
                                if ($request->get('booked_jadwaldokter_id') == $value['jadwaldokter_id']) {
                                    $option_value['options'] = ['disabled' => false];
                                    $option_value['name'] = $value['nama_pegawai']. ' - ' . $jam_praktek . ' - ' .ArrayHelper::getValue($value, 'kuota'). ' Kuota (BOOKED)';
                                } else if ((ArrayHelper::getValue($value, 'kuota', 0) < 1 || ArrayHelper::getValue($value, 'kuota', 0) == null)) {
                                    $option_value['name'] = $value['nama_pegawai']. ' - ' . $jam_praktek . ' - 0 Kuota ';
                                    $option_value['options'] = ['disabled' => true];
                                } else {
                                    $option_value['name'] = $value['nama_pegawai']. ' - ' . $jam_praktek . ' - ' .ArrayHelper::getValue($value, 'kuota'). ' Kuota ';
                                }
                                $option_value['options']['data-jadwaldokter_id'] = ArrayHelper::getValue($value, 'jadwaldokter_id');
                            }

                            $result['output'][] = $option_value;
                            if(!is_null($dokter_id)){
                                $result['selected'] = $dokter_id;
                            };
                            if(!is_null($booked_jadwaldokter_id)){
                                $result['selected'] = $booked_jadwaldokter_id;
                            };
                        }
                    }else{
                        $result = [
                            'output' => [],
                            'selected' => ""
                        ];
                    }
                }
            }

            // echo json_encode([
            //     'output' => $out,
            //     'selected' => $selected,
            // ]);

            return json_encode($result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return json_encode($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return json_encode($result);
        }
    }

    // /**
    //  * @todo Fungsi untuk mendapatkan data jam kunjungan
    //  * @author Sigit Arif Munandar <sigit@docotel.com>
    //  */
    // public function actionGetJamKunjungan()
    // {
    //     try {
    //         $request = Yii::$app->request;
    //         $post = $request->post();
    //         $tglPendaftaran = !empty($post['depdrop_parents'][0]) ? $post['depdrop_parents'][0] : null;
    //         $ruanganId = !empty($post['depdrop_parents'][1]) ? $post['depdrop_parents'][1] : null;
    //         $pegawaiId = !empty($post['depdrop_parents'][2]) ? $post['depdrop_parents'][2] : null;
    //         $kuotaAntrian = !empty($post['depdrop_parents'][3]) ? $post['depdrop_parents'][3] : null;
    //         $hariId = null;
    //         $params = '';
    //         $out = [];

    //         if ($tglPendaftaran) {
    //             $tglPendaftaran = DocoHelpers::convDateTime($tglPendaftaran.' 23:59:59');
    //             $hariId = date('N', strtotime($tglPendaftaran)) + 74;
    //         }

    //         if ($hariId) {
    //             $params = $params.'hari_id='.$hariId.'&';
    //         }

    //         if ($ruanganId) {
    //             $params = $params.'ruangan_id='.$ruanganId.'&';
    //         }

    //         if ($pegawaiId) {
    //             $params = $params.'pegawai_id='.$pegawaiId;
    //         }

    //         if ($params != '') {
    //             $response = array();

    //             if ($kuotaAntrian == DocoConstants::VAR_ID_KUOTA_ANTRIAN_POLIKLINIK) {
    //                 $restPendaftaran = $this->_restPendaftaran->get('pendaftaran-online/get-jam-kunjungan-poli?'.$params);
    //                 $body = json_decode($restPendaftaran->getBody(), true);
    //                 $response = $body['response'];
    //             } else {
    //                 $restPendaftaran = $this->_restPendaftaran->get('pendaftaran-online/get-jam-kunjungan?'.$params);
    //                 $body = json_decode($restPendaftaran->getBody(), true);
    //                 $response = $body['response'];
    //             }

    //             if (!empty($response)) {
    //                 foreach ($response as $key => $value) {
    //                     $out[] = [
    //                         'id' => $value['waktu'],
    //                         'name' => $value['waktu'].' | '.$value['kuota_tersedia_online'],
    //                         'options' => [
    //                             'disabled' => $value['kuota_tersedia_online'] == 0 ? true : false,
    //                             'data-jadwal' => $kuotaAntrian == DocoConstants::VAR_ID_KUOTA_ANTRIAN_POLIKLINIK ? $value['jadwalbukapoli_id'] : $value['jadwaldokter_id']
    //                         ],
    //                     ];
    //                 }
    //             }
    //         }

    //         echo json_encode([
    //             'output' => $out,
    //             'selected' => ''
    //         ]);
    //         return;
    //     } catch (\Exception $e) {
    //         throw new \yii\web\HttpException(500, $e->getMessage());
    //     } catch (RequestException $e) {
    //         throw new \yii\web\HttpException(500, $e->getMessage());
    //     }
    // }

    /**
     * @todo Fungsi untuk mendapatkan data jam kunjungan
     * @author Aris Munandar <aris.m@docotel.com>
     */
    public function actionGetJamKunjungan()
    {
        try {
            $request        = Yii::$app->request;
            $post           = $request->post();
            $tglPendaftaran = !empty($post['depdrop_parents'][0]) ? $post['depdrop_parents'][0] : null;
            $ruanganId      = !empty($post['depdrop_parents'][1]) ? $post['depdrop_parents'][1] : null;
            $pegawaiId      = !empty($post['depdrop_parents'][2]) ? $post['depdrop_parents'][2] : null;
            $kuotaAntrian   = !empty($post['depdrop_parents'][3]) ? $post['depdrop_parents'][3] : null;

            $hariId         = null;
            $params         = '';
            $out            = [];

            if ($tglPendaftaran) {
                $tglPendaftaran = DocoHelpers::convDateTime($tglPendaftaran.' 23:59:59');
                $date           = date('Y-m-d', strtotime($tglPendaftaran));
                $hariId         = date('N', strtotime($tglPendaftaran)) + 74;

                $params         = $params.'tanggal='.$date.'&';
            }

            if ($hariId) {
                $params = $params.'hari_id='.$hariId.'&';
            }

            if ($ruanganId) {
                $params = $params.'ruangan_id='.$ruanganId.'&';
            }

            if ($pegawaiId) {
                $params = $params.'pegawai_id='.$pegawaiId;
            }

            $exceptValue = ["null","","Tidak ada data"];
            if (!in_array($hariId, $exceptValue) && !in_array($ruanganId, $exceptValue) && !in_array($pegawaiId, $exceptValue)) {

                $response = array();

                if ($kuotaAntrian == DocoConstants::VAR_ID_KUOTA_ANTRIAN_POLIKLINIK) {
                    $restPendaftaran = $this->_restPendaftaran->get('pendaftaran-online/get-jam-kunjungan-poli?'.$params);
                    $body            = json_decode($restPendaftaran->getBody(), true);
                    $response        = $body['response'];
                } else {
                    $restPendaftaran = $this->_restPendaftaran->get('pendaftaran-online/get-jam-kunjungan?'.$params);
                    $body            = json_decode($restPendaftaran->getBody(), true);
                    $response        = $body['response'];
                }
                //return DocoHelpers::response($response);

                    $tanggal          = date('Y-m-d', strtotime($tglPendaftaran));
                    $tanggal_saat_ini = date('Y-m-d', strtotime('NOW'));
                    $jam_saat_ini     = date('H:i:s', strtotime('NOW'));

                    if (!empty($response)) {
                        foreach ($response as $key => $value) {
                            if(!isset($value['kuota_tersedia_online'])){
                                $kuota = 0;
                            }
                            else{
                                $kuota = $value['kuota_tersedia_online'];
                            }

                            $status_disabled_sameday = false;
                            if($tanggal == $tanggal_saat_ini && date('H:i:s', strtotime($value['jam_tutup'])) <= $jam_saat_ini){
                                $status_disabled_sameday = true;
                            }


                            $out[] = [
                                'id' => $value['waktu'],
                                'name' => $value['waktu'].' | '.$kuota,
                                 'options' => [
                                    'disabled' => $value['kuota_tersedia_online'] == 0 ? true : $status_disabled_sameday,
                                    'data-jadwal' => $pegawaiId ? $value['jadwaldokter_id'] : $value['jadwalbukapoli_id']
                                ],
                            ];
                        }
                    }

            }
            return DocoHelpers::response([
                'output'   => $out,
                'selected' => ''
            ]);
        } catch (\Exception $e) {
            $out = [];
            echo json_encode([
                'output' => $out,
                'selected' => ''
            ]);
            return;
        } catch (RequestException $e) {
            $out = [];
            echo json_encode([
                'output' => $out,
                'selected' => ''
            ]);
            return;
        }
    }

    /**
     * @todo Fungsi untuk mendapatkan data penjamin untuk depdrop
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetPenjaminDepdrop()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $caraBayarId = !empty($post['depdrop_parents'][0]) ? $post['depdrop_parents'][0] : null;
            $out = [];

            if ($caraBayarId) {
                $restPendaftaran = $this->_restPendaftaran->get('pendaftaran-online/get-penjamin', [
                    'query' => [
                        'carabayar_id' => $caraBayarId,
                        'is_online' => false
                    ]
                ]);
                $body = json_decode($restPendaftaran->getBody(), true);
                $response = $body['response'];

                if (!empty($response)) {
                    foreach ($response as $key => $value) {
                        $out[] = [
                            'id' => $value['penjamin_id'],
                            'name' => $value['penjamin_nama'],
                        ];
                    }
                }
            }

            echo json_encode([
                'output' => $out,
                'selected' => ''
            ]);
            return;
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    /**
     * @todo Fungsi untuk melakukan cetak karcis
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionCetakKarcis()
    {
        try {
            $request = Yii::$app->request;
            $id = $request->get('id', null);
            $path = Yii::getAlias("@download") . "/Cetak Karcis.pdf";
            $params = ['id' => $id];

            $restPendaftaran = $this->_restPendaftaran->get('pendaftaran-online/cetak-karcis', [
                'query' => $params,
                'save_to' => $path
            ]);

            return DocoHelpers::previewPdf($path);
            //return DocoHelpers::downloadFile($path, true);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    /* bg-proc */
    public function actionShowPopup()
    {
        $request = Yii::$app->request;
        $tipe = $request->get('tipe', 1);
        $title = ($tipe == 1) ? 'Unduh Excel ' : 'Cetak PDF ';
        $title .= ' Reservasi Poliklinik';
        $randString = DocoHelpers::generateRandomString();
        $payload = DocoDatatableHelper::convertToRestfulParams($request->get());

        Yii::$app->session->setFlash($randString, $payload);
        return $this->renderAjax('_modal', get_defined_vars());
    }

    public function actionProcessSync($randString, $tipe, $columns)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);
        $session['randString'] = $randString;
        $session['tipe'] = $tipe;
        $session['columns'] = $columns;

        return $this->guzzleExec($this->_restPendaftaran, [
            'url' => "reservasi-poliklinik/unduh-file",
            'payload' => [
                'query' => $session
            ],
        ]);
    }

    public function actionDownloadFile()
    {
        $request = Yii::$app->request;
        $date = date('dmY');
        $filename = $request->get('fileName', null);
        $tipe = $request->get('tipe', 1);
        $ext = ($tipe == 1) ? '.xlsx' : '.pdf';
        $fileDownloads = 'Laporan Reservasi Poliklinik '.$date.$ext;
        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restPendaftaran->get('reservasi-poliklinik/download-file', [
            'query' => [
                'filename' => $filename,
                'tipe' => $tipe
            ],
            'save_to' => $path,
        ]);
        return DocoHelpers::downloadFile($path,true);
    }

    public function setLogJknR($logJkn){
        $log = $this->guzzleExec($this->_restPendaftaran,[
            'url' => 'api/login-jkn',
            'method' => 'post',
            'payload' => [
                'query' => $logJkn
            ],
        ]);
      
        return $log;
    }

    public function actionShowAutoRegisterModal()
    {
        $request = Yii::$app->request;
        $title = ' Auto Register Kunjungan';
        $randString = DocoHelpers::generateRandomString();

        $payload = json_encode($request->post('reservationItems', []));
        $patientList = $request->post('patientList', []);

        return $this->renderAjax('_auto_register_modal', compact('title', 'randString', 'patientList', 'payload'));
    }

    public function actionAutoRegisterProcess()
    {
        $request = Yii::$app->request;
        $randString = $request->post('randString', null);
        $payload = $request->post('reservationItems', []);

        if ($payload) {
            $payload = json_decode($payload, true);
        }

        $this->guzzleExec($this->_restPendaftaran, [
            'url' => 'inf-reservasi-poliklinik/auto-register',
            'method' => 'POST',
            'payload' => [
                'form_params' => [
                    'processKey' => $randString,
                    'reservationItems' => $payload,
                ],
            ],
            'returnResponse' => true,
        ]);

        return DocoHelpers::response([
            'msg' => 'Success set Reservation Queue',
            'data' => compact('randString')
        ], 200, true);
    }
}
