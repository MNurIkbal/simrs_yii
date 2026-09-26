<?php

namespace Doco\pendaftaran\controllers;

use Yii;
use app\components\DocoConstants;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\base\Exception;
use yii\web\Response;
use app\components\Services\Contracts\BpjsInterface;
use app\components\Services\Contracts\RencanaKontrolInterface;
use app\modules\pendaftaran\models\RencanaKontrolForm;
use app\modules\pendaftaran\models\RencanaKontrolHapusForm;
use Doco\models\bpjs\Bpjs;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

class RencanaKontrolInapController extends DocoController
{
    protected $_title = 'Rencana Kontrol/Rencana Inap';
    protected $_restPendaftaran;
    protected $_module = 'rencana-kontrol-inap/';
    protected $bpjsService;
    protected $rkService;
    protected $title_hapus = 'HAPUS RENCANA KONTROL INAP';

    protected $allowAction = ['print-rencana'];


    public function __construct($id, $module, $config = [], BpjsInterface $bpjsService, RencanaKontrolInterface $rkService)
    {
        $this->rkService = $rkService;
        $this->bpjsService = $bpjsService;
        parent::__construct($id, $module, $config);

    }

    public function init()
    {
        parent::init();
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
            $title = $this->_title;
            $dataPack = $this->helper->guzzleExec($this->_restPendaftaran, [
                'url' => 'rencana-kontrol-inap/get-pack',
                'payload' => []
            ]);
            $jenisRencanaList = ArrayHelper::map($dataPack['jenis_rencana_list'], 'lookup_id','lookup_name');

            return $this->render('index', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    public function actionAllDokterList()
    {
        $dokterList = $this->helper->guzzleExec($this->_restPendaftaran, [
            'url' => 'rencana-kontrol-inap/all-dokter-list',
            'returnResponse' => true,   
            'payload' => [
                'query' => Yii::$app->request->get('payload', [])
            ],
        ]);

        $payload = Yii::$app->request->get('payload', []);
        if ($payload['page'] == 1 && !empty($dokterList['data'])) {
            $data = $dokterList['data'];
            $allData = [
                'id' => '',
                'text' => \Yii::t('fe', '-- Pilih --')
            ];
            array_unshift($data, $allData);
            $dokterList['data'] = $data;
        }
        Yii::error($dokterList);
        return $dokterList;
    }

    /**
     * @todo Action untuk mendapatkan list data rencana kontrol / inap
     * @author Fajar Supriadi <fajar.supriadi@sirs.co.id>
     */
    public function actionGetData()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $draw = $request->get('draw', 1);
            $no = $request->get('start', 1);
            $data = [];

            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;

            if (isset($yiiRestfulParams['advanced-filter']['tgl_rencanakontrol'])) {
                $tgl_kunjungan_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_rencanakontrol']);
                $tgl_awal = $tgl_kunjungan_range[0];
                $tgl_akhir = $tgl_kunjungan_range[1];
                $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
                $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
                $yiiRestfulParams['advanced-filter']['tgl_rencanakontrol_awal'] = $tgl_awal_format;
                $yiiRestfulParams['advanced-filter']['tgl_rencanakontrol_akhir'] = $tgl_akhir_format;
                unset($yiiRestfulParams['advanced-filter']['tgl_rencanakontrol']);
            }
            if (isset($yiiRestfulParams['advanced-filter']['nosuratkontrol'])) {
                $noSurat = $yiiRestfulParams['advanced-filter']['nosuratkontrol'];
                $yiiRestfulParams['advanced-filter']['nosuratkontrolspri'] = $noSurat;
                unset($yiiRestfulParams['advanced-filter']['nosuratkontrol']);
            }

            $restPendaftaran = $this->_restPendaftaran->get('rencana-kontrol-inap/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($restPendaftaran->getBody(), true);
            $data = $body['response']['data'];

            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['rencanakontrol_id']);
                    $value['primary'] = $primaryKey;
                    $value['no'] = $no;
                    $value['tgl_rencanakontrol'] = DocoHelpers::convDateTime($value['tgl_rencanakontrol'], false, false);
                    $data[$key] = $value;
                }

                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

                return $result;
            } else {
                $result['data'] = $data;
                $result['recordsTotal'] = 0;
                $result['recordsFiltered'] = 0;

                return $result;
            }
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }


    /**
     * @todo Action Create Rencacan Kontrol dan Rawat inap
     * @author Ikhwanu arriyadh <ikhwanu.arriyadh@sirs.co.id>
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $post = Yii::$app->request->post();
        try {
            $title = 'Create '.$this->_title;
            $data = new RencanaKontrolForm;
            if ($request->post()){
                $data->load($post);
                $post['user'] = Yii::$app->docoVars->user("nama");
                if ($data->validate()) {
                    $data_sirs = $this->_restPendaftaran->post('rencana-kontrol-inap/create',
                    ['form_params' => $post]);
                    $response = json_decode($data_sirs->getBody(), true);
                    if ($response['response']['result']['metaData']['code'] > 200 || $response['response']['result']['metaData']['code'] != '200')
                    {
                        return DocoHelpers::responseTemplate(
                            500,
                            'Error',
                            [],
                            [
                                'title' => Yii::t('fe', 'Peringatan!'),
                                'text' => $response['response']['result']['metaData']['message'],
                                'message' => $response['response']['result']['metaData']['message'],
                            ]
                        );
                    }
                    $response['id'] = DocoHelpers::encrypt($response['response']['model']['rencanakontrol_id']);
                    return DocoHelpers::response($response);
                } else{
                    $response = $data->errors;
                    return DocoHelpers::response($response, 422, 'RencanaKontrolForm');
                }
            }
            return $this->render('create', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    public function actionGetDataKontrol()
    {

        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $data = $request->get();
            if ($data['jenis_rencana'] == '1') {

                $nosep = ArrayHelper::getValue($data, 'no_sep', null);

                $params = [
                    'nosep' => $nosep,
                    'jenis_rencana' => $data['jenis_rencana'],
                ];
                $sep = $this->rkService->cariRencanaKontrolSep($nosep);

                $metadata = ArrayHelper::getValue($sep['metaData'], 'code', null);

                if ($metadata != 200 || $metadata != '200') {
                    return DocoHelpers::responseTemplate(
                        400,
                        'Error',
                        [],
                        [
                            'title' => Yii::t('fe', 'Peringatan!'),
                            'text' => $sep['metaData']['message'],
                            'message' => $sep['metaData']['message'],
                        ]
                    );
                }
                $peserta =  $this->bpjsService->cariPeserta($sep['response']['peserta']['noKartu']);
                $rujukan =  $this->bpjsService->cariRujukan($sep['response']['peserta']['noKartu']);


                $requests = $this->_restPendaftaran->get('allow-bpjs/cek-sep-pasien',[
                    'query' => [
                        'nosep' => $nosep
                    ]
                ]);
                $response = json_decode($requests->getBody(), true);
                if (!empty($response['response'])){
                    $sep['response']['dpjp']['kdDPJP'] = $response['response']['kode_dpjp_melayani'];
                    $sep['response']['dpjp']['nmDPJP'] = $response['response']['nama_dpjp_melayani'];
                } else {
                    $request_data_vclaim = $this->_restPendaftaran->post('allow-bpjs/referensi-cari-sep', [
                        'form_params' => ['nosep' => $nosep],
                    ]);
                    $response_vclaim = json_decode($request_data_vclaim->getBody(), true);
                    if(!empty($response_vclaim['response']['response'])){
                        $sep['response']['dpjp']['kdDPJP'] = $response_vclaim['response']['response']['dpjp']['kdDPJP'];
                        $sep['response']['dpjp']['nmDPJP'] = $response_vclaim['response']['response']['dpjp']['nmDPJP'];
                    }
                }

                if (!empty($sep['response']['tglSep'])){
                    $sep['response']['tglSep'] = date('d M Y', strtotime($sep['response']['tglSep']));
                }
            } else {
                $no_kartu = ArrayHelper::getValue($data, 'no_kartu', null);
                $params = [
                    'no_kartu' => $no_kartu,
                    'jenis_rencana' => $data['jenis_rencana'],
                ];
                $peserta =  $this->bpjsService->cariPeserta($no_kartu);
                $rujukan =  $this->bpjsService->cariRujukan($no_kartu);

                $metadata = ArrayHelper::getValue($peserta['metaData'], 'code', null);

                if ($metadata != 200 || $metadata != '200') {
                    return DocoHelpers::responseTemplate(
                        400,
                        'Error',
                        [],
                        [
                            'title' => Yii::t('fe', 'Peringatan!'),
                            'text' => $peserta['metaData']['message'],
                            'message' => $peserta['metaData']['message'],
                        ]
                    );
                }
                $sep =[];
            }


            $data_sirs = $this->_restPendaftaran->post('rencana-kontrol-inap/get-data-peserta',
                ['form_params' => $params]);
            $response = json_decode($data_sirs->getBody(), true);

            $data_sirs = ['sirs' => $response['response'], 'status' => $response['metadata']['status']];


            $data = [
                'sep' => $sep,
                'peserta' => $peserta,
                'rujukan' => $rujukan,
                'Sirs' => $data_sirs
            ];
            return $data;
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    /**
     * @todo Untuk update rencana kontrol/inap
     * @author Fajar Supriadi <fajar.supriadi@sirs.co.id>
     */
    public function actionUpdate($id, $vclaim = false, $noKartu = null)
    {
        try {
            $request = Yii::$app->request;
            $rencanaKontrol = $peserta = $pasien = [];
            $title = 'Ubah '.$this->_title;
            $model = new RencanaKontrolForm;
            $rencanakontrol_id = ($vclaim) ? $id : DocoHelpers::decrypt($id);


            if ($request->post()){
                $model->load($request->post());
                $model->scenario = 'update';

                if ($model->validate()) {
                    $response = $this->_restPendaftaran->post('rencana-kontrol-inap/update-rencana-kontrol?rencanakontrol_id='.$rencanakontrol_id, [
                        'form_params' => $model->attributes
                    ]);
                    $response = json_decode($response->getBody(), true);
                    return DocoHelpers::response($response);
                }

                return DocoHelpers::response($model->errors,422,'RencanaKontrolForm');
            } else {

                if ($vclaim) {
                    $noKartu = $noKartu;
                    $noSuratKontrol = $rencanakontrol_id;

                    // $result = $this->helper->guzzleExec($this->_restPendaftaran, [
                    //     'url' => 'rencana-kontrol-inap/get-data-pasien-peserta',
                    //     'payload' => [
                    //         'query' => [
                    //             'no_kartu' => $noKartu
                    //         ]
                    //     ]
                    // ]);

                    $pesertaRes = $this->bpjsService->cariPeserta($noKartu);
                    if ($pesertaRes && $pesertaRes['metaData']['code'] == 200) {
                        $peserta = $pesertaRes['response']['peserta'];
                        $pasien = [
                            "no_rekam_medik" => null,
                            "nama_pasien" => $peserta['nama']
                        ];
                    }
                    $rencanaKontrolRes = $this->bpjsService->cariSuratKontrol($noSuratKontrol);
                    if ($rencanaKontrolRes && $rencanaKontrolRes['metaData']['code'] == 200) {
                        $rencanaKontrol = $rencanaKontrolRes['response'];
                    }

                    $model->tgl_rencanakontrol = date('d-m-Y', strtotime(empty($rencanaKontrol['tglRencanaKontrol']) ? null : $rencanaKontrol['tglRencanaKontrol']));
                    $model->nosuratkontrol = empty($rencanaKontrol['noSuratKontrol']) ? null : $rencanaKontrol['noSuratKontrol'];
                    $model->nama = empty($peserta['nama']) ? null : $peserta['nama'];
                    $model->no_kartu = empty($peserta['noKartu']) ? null : $peserta['noKartu'];
                    $model->no_sep = empty($rencanaKontrol['sep']) ? null : $rencanaKontrol['sep']['noSep'];
                    $model->jenis_pelayanan = empty($rencanaKontrol['sep']) ? null : $rencanaKontrol['sep']['jnsPelayanan'];
                    $model->jenis_rencana = empty($rencanaKontrol['namaJnsKontrol'])
                                            ? null : (($rencanaKontrol['namaJnsKontrol'] == "Kontrol") ? DocoConstants::STS_RECANA_KONTROL: DocoConstants::STS_RENCAN_INAP);

                } else {
                    $result = $this->helper->guzzleExec($this->_restPendaftaran, [
                        'url' => 'rencana-kontrol-inap/get-data-update',
                        'payload' => [
                            'query' => [
                                'rencanakontrol_id' => $rencanakontrol_id
                            ]
                        ]
                    ]);

                    $model->attributes = $result['data_kontrol'];
                    $model->tgl_rencanakontrol = date('d-m-Y', strtotime($model->tgl_rencanakontrol));
                    $model->jenis_pelayanan_nama = $model->jenis_pelayanan;

                    $pasien = $result['data_pasien'];

                    $noKartu = $model->no_kartu;
                    $noSuratKontrol = $model->jenis_rencana == DocoConstants::STS_RECANA_KONTROL ? $model->nosuratkontrol : $model->no_spri;

                    $pesertaRes = $this->bpjsService->cariPeserta($noKartu);
                    if ($pesertaRes && $pesertaRes['metaData']['code'] == 200) {
                        $peserta = $pesertaRes['response']['peserta'];
                    }
                    $rencanaKontrolRes = $this->bpjsService->cariSuratKontrol($noSuratKontrol);
                    if ($rencanaKontrolRes && $rencanaKontrolRes['metaData']['code'] == 200) {
                        $rencanaKontrol = $rencanaKontrolRes['response'];
                    }
                }



            }

            return $this->render('update', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    public function actionModalPencarianSpesialis()
    {
        try {
            $model = new RencanaKontrolForm;
            return $this->renderAjax('_modal-pencarian-spesialis', compact('model'));
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    public function actionGetDataSpesialis()
    {
        $result = $data = $spesialis = [];
        $request = Yii::$app->request;
        $jenis_kontrol = $request->get('jenis_kontrol', null);
        $no_kartu = $request->get('no_kartu', null);
        $tgl = $request->get('tgl', null);
        $tgl = date('Y-m-d', strtotime($tgl));

        try {
            $result['recordsTotal'] = 0;
            $result['recordsFiltered'] = 0;

            $spesialisRes = $this->bpjsService->spesialisRencanaKontrol($jenis_kontrol, $no_kartu, $tgl);
            if ($spesialisRes && $spesialisRes['metaData']['code'] == 200) {
                $spesialis = $spesialisRes['response']['list'];
            }

            $no = 0;
            foreach ($spesialis as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['detail'] = Html::button("<i class='fa fa-plus-square-o'></i>", [
                    'class' => 'btn btn-sm btn-success', 'data-source'=> "/pendaftaran/rencana-kontrol-inap/detail-spesialis?kode_poli=".$value['kodePoli']."&nama_spesialis=".$value['namaPoli'],'onclick'=> 'docoHelper.detail(this)']);

                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = count($spesialis);
            $result['recordsFiltered'] = count($spesialis);

            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionDetailSpesialis()
    {
        $title = Yii::t('fe', 'Data Dokter');
        $request = Yii::$app->request;
        $kode_poli = $request->get('kode_poli', null);
        $nama_spesialis = $request->get('nama_spesialis', null);

        return $this->renderAjax('partial/_detail-spesialis', get_defined_vars());
    }

    public function actionGetDokterDpjp()
    {
        $result = $data = $dokter = [];
        $request = Yii::$app->request;
        $jenis_kontrol = $request->get('jenis_kontrol', null);
        $kode_poli = $request->get('kode_poli', null);
        $tgl = $request->get('tgl', null);
        $tgl = date('Y-m-d', strtotime($tgl));
        $nama_spesialis = $request->get('nama_spesialis', null);

        try {
            $result['recordsTotal'] = 0;
            $result['recordsFiltered'] = 0;

            $dokterRes = $this->bpjsService->dokterRencanaKontrol($jenis_kontrol, $kode_poli, $tgl);
            if ($dokterRes && $dokterRes['metaData']['code'] == 200) {
                $dokter = $dokterRes['response']['list'];
            }

            $no = 0;
            foreach ($dokter as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['aksi'] = Html::button("Pilih", [
                    'style' => "margin-bottom:8px;",
                    'class' => 'btn-simpan-dpjp btn btn-info btn-labeled btn-xs',
                    'data-kode_poli' => $kode_poli,
                    'data-nama_spesialis' => $nama_spesialis,
                    'data-dokterdpjp_kode' => $value['kodeDokter'],
                    'data-dokterdpjp_nama' => $value['namaDokter'],
                    'onClick' => 'pilihDpjp(this)',
                ]);

                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = count($dokter);
            $result['recordsFiltered'] = count($dokter);

            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionPrintRencana()
    {
        $session = Yii::$app->session;
        $idRuanganWorkspace = $session->get('active_workspace')['ruangan_id'];
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-rencana.pdf";

        try {
            $noSuratKontrol = $request->get('no_surat_kontrol', null);
            $isVclaim = $request->get('is_vclaim', null);
            if(!empty($isVclaim)){
                $path = Yii::getAlias("@download") . "/print-rencana-vclaim.pdf";
                $vclaimRes = $this->bpjsService->cariSuratKontrol($noSuratKontrol);
                if ($vclaimRes && $vclaimRes['metaData']['code'] == 200) {
                    $vclaimData = $vclaimRes['response'];
                    $response = $this->_restPendaftaran->get('rencana-kontrol-inap/print-rencana-vclaim', [
                        'query' => $vclaimData,
                        'save_to' => $path
                    ]);
                    $body = json_decode($response->getBody(), true);
                } else {
                    return DocoHelpers::response($vclaimRes, 422);
                }
            }else{
                $rencanakontrol_id = $request->get('rencanakontrol_id', null);
                $nosep = $request->get('nosep', null);

                if (!is_numeric($rencanakontrol_id)) {
                    $rencanakontrol_id = DocoHelpers::decrypt($rencanakontrol_id);
                }

                $filterQuery = [
                    'rencanakontrol_id' => $rencanakontrol_id,
                    'jenis_rencana' => $request->get('jenis_rencana', null)
                ];

                $filterQuery['ruangan_id'] = $idRuanganWorkspace;

                $response = $this->_restPendaftaran->get('rencana-kontrol-inap/print-rencana', [
                    'query' => $filterQuery,
                    'save_to' => $path
                ]);

                $body = json_decode($response->getBody(), true);
            }

            return DocoHelpers::previewPdf($path, $response);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionGetNoSuratKontrol()
    {
        $request = Yii::$app->request;
        $noSuratKontrol = $request->get('no_surat_kontrol', null);
        try
        {
            $vclaimRes = $this->bpjsService->cariSuratKontrol($noSuratKontrol);
            if ($vclaimRes && $vclaimRes['metaData']['code'] == 200) {
                $response = [
                    'can_print' => true,
                    'message' => ''
                ];
            } else {
                $response = [
                    'can_print' => false,
                    'message' => $vclaimRes['metaData']['message']
                ];
            }
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionGetInfoPeserta()
    {
        $request = Yii::$app->request;
        $rencanakontrol_id = $request->get('rencanakontrol_id', null);
        try
        {
            $requests = $this->_restPendaftaran->get('rencana-kontrol-inap/get-info-peserta',[
                'query' => [
                    'rencanakontrol_id' => $rencanakontrol_id,
                ]
            ]);
            $response = json_decode($requests->getBody(), true);
            return json_encode($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionConfirmHapus()
    {
        $request = Yii::$app->request;
        $rencanakontrol_id = $request->get( 'rencanakontrol_id',null);
        $noSuratKontrol = $request->get('noSuratKontrol',null);
        $username = Yii::$app->docoVars->user('nama');
        $hapusform = new  RencanaKontrolHapusForm;
        $title = $this->title_hapus;
        $formAction = '/pendaftaran/rencana-kontrol-inap/confirm-hapus';
        try{

            if ($request->post()) {
                $hapusform->load($request->post());
                $hapusform->scenario = ArrayHelper::getValue($request->post(), 'RencanaKontrolHapusForm.is_from_vclaim', false) ? 'hapus-dari-vclaim' : 'default';
                if ($hapusform->validate()) {
                    $response = $this->_restPendaftaran->post('rencana-kontrol-inap/hapus', [
                        'form_params' => $hapusform->attributes
                    ]);
                    $response = json_decode($response->getBody(), true);
                    return DocoHelpers::response($response);
                }
                return DocoHelpers::response($hapusform->errors,422,'RencanaKontrolHapusForm');
            } else {
                return $this->renderAjax('partial/_modal_batal', get_defined_vars());
            }
        } catch (\Exception $e){
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        } catch (RequestException $e){
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionInfDataVclaim()
    {
        try {
            $title = 'Rencana Kontrol/Inap dari Vclaim';

            return $this->render('informasi-vclaim', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    public function actionGetDataVclaim()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $filterParam = $request->get('advancedFilter');
            $data = $vclaimData = $result = [];

            $filterList = [
                'jnsKontrol' => DocoConstants::FILTER_NUMBER,
                'noSepAsalKontrol' => DocoConstants::FILTER_CHAR,
                'noKartu' => DocoConstants::FILTER_NUMBER,
                'noSuratKontrol' => DocoConstants::FILTER_CHAR,
                'nama' => DocoConstants::FILTER_CHAR,
            ];
            $filter = ArrayHelper::getValue($filterParam, 'filter', DocoConstants::FILTER_BY_TGL_RENCANA);
            $tglAwal = date('Y-m-d');
            $tglAkhir = date('Y-m-d');
            if (isset($filterParam['tglRencanaKontrol'])) {
                if ($filterParam['tglRencanaKontrol']) {
                    $tglRencanaKontrol = explode(' - ', $filterParam['tglRencanaKontrol']);
                    $tgl_awal = $tglRencanaKontrol[0];
                    $tgl_akhir = $tglRencanaKontrol[1];
                    $tglAwal = date('Y-m-d', strtotime($tgl_awal));
                    $tglAkhir = date('Y-m-d', strtotime($tgl_akhir));
                }
            }

            $vclaimRes = $this->bpjsService->cariListSuratKontrol($tglAwal, $tglAkhir, $filter);
            if ($vclaimRes && $vclaimRes['metaData']['code'] == 200) {
                $vclaimData = $vclaimRes['response']['list'];
            } else {
                return DocoHelpers::response($vclaimRes, 422);
            }

            if(!empty($filterParam)){
                $no = 0;
                foreach ($vclaimData as $key => $value) {
                    $isFiltered = $this->filterDataVclaim($filterList, $value, $filterParam);
                    if ($isFiltered) {
                        $no++;
                        $value['no'] = $no;
                        $value['filter'] = $filter;
                        $value['primary'] = $value['noSuratKontrol'];
                        $value['vclaim'] = true;

                        $data[] = $value;
                    }
                }
            }

            $result['data'] = $data;
            $result['recordsTotal'] = count($vclaimData);
            $result['recordsFiltered'] = count($data);

            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionHapusDataVclaim()
    {
        $request = Yii::$app->request;
        $rencanakontrol_id = $request->get('rencanakontrol_id',null);
        $noSuratKontrol = $request->get('noSuratKontrol',null);
        $username = Yii::$app->docoVars->user('nama');
        $hapusform = new  RencanaKontrolHapusForm;
        $is_from_vclaim = true;
        $title = $this->title_hapus;
        $formAction = '/pendaftaran/rencana-kontrol-inap/hapus-data-vclaim';

        if ($request->post()) {
            $hapusform->load($request->post());
            if ($hapusform->validate()) {
                $response = $this->helper->guzzleExec($this->_restPendaftaran, [
                    'url' => 'rencana-kontrol-inap/hapus-data-vclaim',
                    'method' => 'post',
                    'payload' => [
                        'form_params' => $hapusform->attributes
                    ]
                ]);

                $statusCode = ArrayHelper::getValue($response, 'httpStatusCode', 200);
                return $this->helper->response([
                    'response' => $response
                ], $statusCode);
            }
            return $this->helper->response($hapusform->errors,422,'RencanaKontrolHapusForm');
        } else {
            return $this->renderAjax('partial/_modal_batal', get_defined_vars());
        }
    }

    private function filterDataVclaim($filterList, $data, $filterParam) {
        $result = true;

        foreach ($filterParam as $key => $value) {
            if (isset($filterList[$key]) && $value) {
                if ($filterList[$key] == DocoConstants::FILTER_NUMBER) {
                    if ($data[$key] != $value) {
                        $result = false;
                        break;
                    }
                } else if ($filterList[$key] == DocoConstants::FILTER_CHAR) {
                    if (strpos(strtolower($data[$key]), strtolower($value)) === false) {
                        $result = false;
                        break;
                    }
                }
            }
        }
        return $result;
    }
}
