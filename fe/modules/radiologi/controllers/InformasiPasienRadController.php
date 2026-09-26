<?php

/**
 * @author Randy Vianda Putra
 * @todo Informasi Pasien Radiologi
 * @copyright 26 Juli 2018 aweutist
 */

namespace Doco\radiologi\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DHtml;
use Doco\radiologi\models\BatalPeriksaPenunjangForm;
use yii\helpers\ArrayHelper;
use app\assets\CalenderAssets;

class InformasiPasienRadController extends DocoController
{
    protected $_title = "Informasi Pasien Radiologi";
    protected $_module = '/radiologi/informasi-pasien-rad';
    protected $_restRad;
    protected $allowAction = [
        'get-asal-rujukan2',
        'get-asal-rujukan3',
    ];
    protected $_asal_rujukan = [
        'ORDER' => 'Order',
        'RUJUKAN RS' => 'Rujukan RS',
        'APS'=>'APS'
    ];

    public function init()
    {
        parent::init();
        $this->_restRad = Yii::$app->docoRest->radiologi;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $cara_bayar = $penjamin = [];
        $asalRujukan = $this->_asal_rujukan;
        $instalasi_id = Yii::$app->docoVars->workspace('instalasi_id');
        try {
            $data = $this->getDataApi($instalasi_id);
            $status = !empty($data['data_status'])
                ? ArrayHelper::map($data['data_status'], 'lookup_id', 'lookup_name')
                : [];
            $statusExpertise = !empty($data['data_status_expertise'])
                ? ArrayHelper::map($data['data_status_expertise'], 'lookup_id', 'lookup_name')
                : [];
            $cara_bayar = !empty($data['data_carabayar']) ? $data['data_carabayar'] : [];
            $dataAsalRujukan = !empty($data['asalRujukan']) ? $data['asalRujukan'] : [];

            $asalrujukan = ArrayHelper::map($dataAsalRujukan, 'asalrujukan_nama', 'asalrujukan_nama');
            $arrAsalRujukan = array_merge($asalRujukan,$asalrujukan);
        } catch (RequestException $e) {
            $cara_bayar = $penjamin = $dataAsalRujukan = [];
        }

        $btnCetakPemeriksaan = Yii::$app->docoPlugin->execute($this, 'popup_logo');
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        try {
            $request = Yii::$app->request;
            $type = $request->get('type', null);
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $yiiRestfulParams['advanced-filter']['type'] = $type;
            // echo json_encode(http_build_query($yiiRestfulParams)); exit;
            $response = $this->_restRad->request('get', 'inf-pasien-rad/index?' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $row = $cache = [];
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            $updateDoctor = DHtml::cekHakAkses('update-doctor');
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $diffTglExpertise = '-';
                $primaryKey = DocoHelpers::encrypt($value['pasienmasukpenunjang_id']);
                $daftartindakan_id = DocoHelpers::encrypt($value['daftartindakan_id']);
                $tindakanpelayanan_id = DocoHelpers::encrypt($value['tindakanpelayanan_id']);
                $penunjang_id = DocoHelpers::encrypt($value['pasienmasukpenunjang_id']);
                $isHasil = !empty($value['is_hasil']) ? $value['is_hasil'] : null;
                $tglVerif = !empty($value['tgl_verifikasi']) ? $value['tgl_verifikasi'] : null;
                $value['primary'] = $primaryKey;
                $value['id'] = $daftartindakan_id;
                $value['tindakan_id'] = $tindakanpelayanan_id;
                $value['penunjang_id'] = $penunjang_id;
                $value['tindakanpelayanan_id'] = DocoHelpers::encrypt($value['tindakanpelayanan_id']);
                $value['daftartindakan_id'] = DocoHelpers::encrypt($value['daftartindakan_id']);
                unset($value['kamarruangan_id']);
                $value['rowNum'] = $no;
                $value['status_periksa_id'] = $value['status_periksa'];
                $value['status_periksa'] = empty($value['status_periksa']) ? '-' : DocoConstants::$status_lab[$value['status_periksa']];
                $value['status_expertise'] = null;
                $value['no_rekam_medik'] = $value['no_rekam_medik'];
                $value['tglmasukpenunjang'] = $value['tglperiksa'] = DocoHelpers::convertDate($value['tglmasukpenunjang'], 'd-M-Y H:i');
                $value['status_cito'] = $value['cyto_tindakan'] == true ? 'CITO' : 'NON CITO';
                $tgl_lahir = !empty($value['tanggal_lahir']) ? DocoHelpers::convertDate($value['tanggal_lahir'], 'd-M-Y') : "-";
                $value['tanggal_lahir'] = $tgl_lahir;
                $value['no_antrian'] = $this->getAksiListPasien($value);
                $value['is_update_doctor'] = $updateDoctor;
                $color = 'white';
                $colorExprt = 'white';
                $font = 'black';
                $fontExprt = 'black';
                if ($value['status_periksa'] == 'Batal' || $value['status_batal']) {
                    $value['status_periksa'] = 'Batal';
                    $color = '#d64541';
                    $font = 'white';
                } else if (!empty($isHasil)) {
                    if (!empty($tglVerif)) {
                        $value['status_periksa'] = 'Selesai';
                        $color = '#2bcc6e';
                        $font = 'white';
                    } else {
                        $value['status_periksa'] = 'Periksa';
                        $color = '#05bbbe';
                        $font = 'white';
                    }

                    $value['status_expertise'] = isset($value['tgl_hasilrad']) ? "Sudah Expertise" : "Belum Expertise";
                    $colorExprt = isset($value['tgl_hasilrad']) ? "white" : "#6189e5";
                    $fontExprt = isset($value['tgl_hasilrad']) ? "black" : "white";
                } else {
                    $value['status_periksa'] = 'Belum Diperiksa';
                }
                $value['status_periksa_btn'] = '<span class="badge" style="background: ' . $color . '; color: ' . $font . '">' . $value['status_periksa'] . '</span>';
                $value['status_periksa_btn'] = $value['status_periksa_btn'] . '<br/><br/>' . '<span class="badge" style="background: ' . $colorExprt . '; color: ' . $fontExprt . '">' . $value['status_expertise'] . '</span>';
                if(!$value['status_batal']){
                    if ($value['status_bayar']) {
                        $value['status_bayar_btn'] = '<span class="badge" style="background: #2bcc6e; color: white">Sudah Bayar</span>';
                    } else {
                        $value['status_bayar_btn'] = '<span class="badge" style="background: #FFFFFF; color: black">Belum Bayar</span>';
                    }
                }else{
                    $value['status_bayar_btn'] = '<span class="badge" style="background: #EE1002; color: white">Batal Bayar</span>';
                }
                $value['jk'] = isset($value['jenis_kelamin_kode']) ? $value['jenis_kelamin_kode'] : "";
                $value['waktu_ambil_foto'] = $value['tgl_ambilfoto'] == null ? '-' : DocoHelpers::convertDate($value['tgl_ambilfoto'], 'd-M-Y H:i:s');
                $value['waktu_expertise'] = $value['tgl_hasilrad'] == null ? '-' : DocoHelpers::convertDate($value['tgl_hasilrad'], 'd-M-Y H:i:s');
                if (!empty($value['tgl_hasilrad'])) {
                    $diffTglExpertise = DocoHelpers::getLamaTunggu($value['tgl_ambilfoto'], $value['tgl_hasilrad']);
                }
                $value['selisih'] = $diffTglExpertise;
                $value['detail_diagnosa'] = Html::button("<i class='fa fa-plus-square-o'></i>", [
                    'class' => 'btn btn-sm btn-success', 'data-source'=>"/radiologi/inf-pasien-rujukan-rad/detail-diagnosa?pasienkirimkeunitlain_id=".DocoHelpers::encrypt($value['pasienkirimkeunitlain_id']),'onclick'=> 'docoHelper.detail(this)']);

                $row[$key] = $value;
            }
            $return = [
                'data' => $row,
                'draw' => $request->get('draw'),
                'recordsTotal' => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount']
            ];

            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    private function getAksiListPasien($data)
    {
        $pendaftaran_id = DocoHelpers::encrypt($data['pendaftaran_id']);

        $return_data = '';
        if (!empty($data['no_antrian'])) {
            $return_data .= Html::button(
                '<i class="fa fa-volume-up"></i> ' . $data['no_antrian'],
                [
                    'class' => 'btn btn-turquoise btn-md antrian',
                    'data-tooltip' => "tooltip",
                    'data-original-title' => Yii::t('fe', 'Panggil antrian'),
                    'data-id' => $data['pendaftaran_id'],
                    'data-antrian' => $data['no_antrian'],
                ]
            );
        }
        $return_data .= '&nbsp;&nbsp;';

        return $return_data;
    }

    /**
     * @author Randy Vianda Putra
     * @todo Panggil Antrian
     * @copyright 7 May 2018 aweutist
     */
    public function actionPanggilAntrian()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $post = Yii::$app->request->post();

        try {
            $no_antrian = $post['no_antrian'];
            $teks_panggil = DocoHelpers::convertAntrian($no_antrian);
            $response['teks_panggil'] = $teks_panggil;
            return DocoHelpers::response($response, 200);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionGetAsalRujukan2($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $get = $request->get();

        $result = [];
        $result['results'] = [];
        try {
            if ($get['asal_1'] == "order") {
                $response = $this->_restRad->get('inf-pasien-rad/list-instalasi');
                $body = json_decode($response->getBody(), true);
                foreach ($body['response'] as $k => $value) {
                    $result['results'][] = [
                        'id' => $k,
                        'text' => $value
                    ];
                }
                return $result;
            }
            if ($get['asal_1'] == "rujukanRS") {
                $response = $this->_restRad->get('inf-pasien-rad/list-asal-rujukan');
                $body = json_decode($response->getBody(), true);
                foreach ($body['response'] as $k => $value) {
                    $result['results'][] = [
                        'id' => $k,
                        'text' => $value
                    ];
                }
                return $result;
            }
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }


    public function actionGetAsalRujukan3($q = "", $q2 = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $get = $request->get();

        $result = [];
        $result['results'] = [];
        try {
            if ($get['asal_1'] == "order") {
                $response = $this->_restRad->get('inf-pasien-rad/list-ruangan?instalasi_id=' . $get['asal_2']);
                $body = json_decode($response->getBody(), true);
                foreach ($body['response'] as $k => $value) {
                    $result['results'][] = [
                        'id' => $k,
                        'text' => $value
                    ];
                }
                return $result;
            }
            if ($get['asal_1'] == "rujukanRS") {
                $response = $this->_restRad->get('inf-pasien-rad/list-asal-rujukan-dari?asalrujukan_id=' . $get['asal_2']);
                $body = json_decode($response->getBody(), true);
                foreach ($body['response'] as $k => $value) {
                    $result['results'][] = [
                        'id' => $k,
                        'text' => $value
                    ];
                }
                return $result;
            }
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionFormBatal($id)
    {
        $id = DocoHelpers::decrypt($id);
        $model = new BatalPeriksaPenunjangForm;
        $detail = $pemeriksaan = $data = [];
        try {
            $response = $this->_restRad->get('inf-pasien-rad/detail-periksa?pasienmasukpenunjang_id=' . $id);
            $body = json_decode($response->getBody(), true);
            $detail = $body['response']['labDetail'];
        } catch (RequestException $e) {
            $cara_bayar = $penjamin = [];
        }

        return $this->render('form_batal', get_defined_vars());
    }

    public function actionBatalOrder()
    {
        $request = Yii::$app->request;
        $model = new BatalPeriksaPenunjangForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);

        if ($request->post()) {

            $model->load($request->post());

            if ($model->validate()) {

                $formData = $request->post(); // tampung formdata

                $form_params['pasienmasukpenunjang_id'] = DocoHelpers::decrypt($formData['pasienmasukpenunjang_id']);
                $form_params['peg_menyetujui_id'] = $formData['BatalPeriksaPenunjangForm']['peg_menyetujui_id'];
                $form_params['tgl_batalperiksa'] = $formData['BatalPeriksaPenunjangForm']['tgl_batalperiksa'];
                $form_params['alasan'] = $formData['BatalPeriksaPenunjangForm']['alasan'];

                try {
                    $response = $this->_restRad->post('inf-pasien-rad/proses-batal', [
                        'form_params' => $form_params
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
            return $this->renderPartial('form', get_defined_vars());
        }
    }

    public function actionGetDataPemeriksaan($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['id'] = $id;
        $draw = $request->get('draw', 1);
        $data = [];
        try {
            $response = $this->_restRad->get('inf-pasien-rad/get-pemeriksaan-view', [
                'form_params' => [],
                'query' => $yiiRestfulParams
            ]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);

            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['tindakanpelayanan_id']);
                $value['primary'] = $primaryKey;
                unset($value['tindakanpelayanan_id']);
                $value['rowNum'] = $no;
                $data[$key] = $value;
            }

            $return = [
                'data' => $data,
                'draw' => $request->get('draw'),
                'recordsTotal' => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount']
            ];

            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();

            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();

            return $result;
        }
    }


    public function actionPrintRincian($id)
    {
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/rincian_tagihan_rad.pdf";

        try {
            $response = $this->_restRad->get('inf-pasien-rad/print-rincian?id=' . $id, ['save_to' => $path]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path,$response);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionGetDokter($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];
        try {
            $response = $this->_restRad->get('inf-pasien-rad/get-dokter?advanced-filter[nama_pegawai]=' . $q);
            $body = json_decode($response->getBody(), true);
            $temp_dokter = array();
            foreach ($body['response']['data'] as $value) {
                if (!array_key_exists($value['pegawai_id'], $temp_dokter)) {
                    $result['results'][] = [
                        'id' => $value['nama_pegawai'],
                        'text' => $value['nama_pegawai']
                    ];
                    $temp_dokter[$value['pegawai_id']] = $value['nama_pegawai'];
                }
            }
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetPegawaiRuangan($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];
        $ruanganid = Yii::$app->docoVars->workspace("ruangan_id");
        try {
            $response = $this->_restRad->get('allow/data-pegawai?advanced-filter[nama_pegawai]=' . $q . '&advanced-filter[ruangan_id]=' . $ruanganid);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response']['data'] as $value)
                $result['results'][] = [
                    'id' => $value['pegawai_id'],
                    'text' => $value['nama_pegawai']
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

    private function getDataApi($instalasi_id)
    {
        try {
            $response = $this->_restRad->request('GET', 'inf-pasien-rad/generate-api?instalasi_id=' . $instalasi_id);
            $body = json_decode($response->getBody(), TRUE);

            $return = [
                'data_carabayar' => $body['response']['cara_bayar'],
                'data_status' => $body['response']['status_radiologi'],
                'asalRujukan' => $body['response']['asalRujukan'],
                'rujukanDari' => $body['response']['rujukanDari'],
                'data_status_expertise' => $body['response']['status_expertise'],
            ];
            return $return;
        } catch (RequestException $e) {
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * This function will return doctor radiologi
     *
     * @param Array $payload
     * @return JSON
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionDoctors()
    {
        return $this->guzzleExec($this->_restRad, [
            'url' => 'allow/get-dokter-rad',
            'method' => 'get',
            'payload' => [
                'query' => array_merge(Yii::$app->request->get('payload', []), [
                    'type' => 'selectScroll'
                ])
            ],
            'returnResponse' => true
        ]);
    }

    /**
     * This API update doctor
     *
     * @param String $daftartindakan_id
     * @param String $no_masukpenunjang
     * @param String $pegawai_id
     * @return JSON
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionUpdateDoctor()
    {
        $payload = $this->validatePayload([
            'payloadKey' => [
                'daftartindakan_id' => 'required',
                'no_masukpenunjang' => 'required',
                'pegawai_id' => 'required',
            ]
        ]);
        if (isset($payload['errors'])) {
            return $this->responseJson(422, 'Silakan cek kembali input', ['errors' => $payload['errors']]);
        } else {
            if (isset($payload['daftartindakan_id'])) {
                $payload['daftartindakan_id'] = DocoHelpers::decrypt($payload['daftartindakan_id']);
            }
            return $this->guzzleExec($this->_restRad, [
                'url' => 'inf-pasien-rad/update-doctor',
                'method' => 'POST',
                'returnResponse' => true,
                'payload' => [
                    'form_params' => $payload
                ]
            ]);
        }
    }

    /**
     * Endpoint to get instalasi and ruangan
     *
     * @param String var
     * @return JSON
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionDropdown()
    {
        $data =  $this->guzzleExec($this->_restRad, [
            'url' => 'allow/dropdown',
            'payload' => [
                'query' => Yii::$app->request->get('payload')
            ],
            'returnResponse' => true
        ]);
        $result = [];
        unset($data['data'][0]);
        foreach ($data['data'] as $key => $value) {
            $result['data'][] =[
                'id' => $value['text'],
                'text' => $value['text'],
            ];
        }
        return $result;
    }

    public function actionBatalPeriksa()
    {
        $request = Yii::$app->request;
        $payload = $request->post();
        return $this->guzzleExec($this->_restRad, [
            'url' => 'inf-pasien-rad/batal-periksa',
            'method' => 'POST',
            'returnResponse' => true,
            'payload' => [
                'form_params' => $payload
            ]
        ]);
    }

    public function actionShowPopup($id,$tindakanpelayanan_id,$daftartindakan_id)
    {
        $title = 'Cetak Pemeriksaan';
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($request->get('id', null));
        $tindakanpelayanan_id = DocoHelpers::decrypt($request->get('tindakanpelayanan_id', null));
        $daftartindakan_id = DocoHelpers::decrypt($request->get('daftartindakan_id', null));
        $jenis_cetakan = [1 => 'Tanpa Logo', 2 => 'Dengan Logo'];
        $model = new \yii\base\DynamicModel(['jenis_cetakan', 'pasienmasukpenunjang_id']);
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $model->addRule(['jenis_cetakan', 'pasienmasukpenunjang_id'], 'integer')->addRule(['jenis_cetakan'], 'required');
        $model->pasienmasukpenunjang_id = DocoHelpers::decrypt($request->get('id', null));
        $model->jenis_cetakan = 1;
        if($model->load($request->post())) {
            $model->pasienmasukpenunjang_id = DocoHelpers::decrypt($model->pasienmasukpenunjang_id);
            return DocoHelpers::response(['data' => [
                'daftartindakan_id' =>$request->get('daftartindakan_id', null),
                'tindakanpelayanan_id' => $request->get('tindakanpelayanan_id', null),
                'pasienmasukpenunjang_id' => $request->get('id', null),
                'jenis_cetakan' => DocoHelpers::encrypt($model->jenis_cetakan),
            ]]);
        }
        else {
            return $this->renderAjax('partial/_popup_logo', get_defined_vars());
        }
    }
}
