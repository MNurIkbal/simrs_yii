<?php

namespace Doco\penjaminasuransi\controllers;

use app\components\DocoConstants;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\penjaminasuransi\models\SyncPasienForm;
use Doco\penjaminasuransi\models\FormKoreksi;
use Doco\penjaminasuransi\models\FormKunjunganPasien;
use Doco\penjaminasuransi\models\KlaimInacbgForm;
use GuzzleHttp\Exception\RequestException;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class InformasiPasienRajalBpjsController extends DocoController
{

    protected $_title = "Informasi pasien rawat jalan bpjs";
    protected $_module = 'penjaminasuransi/informasi-pasien-rajal-bpjs/';
    protected $_restPenjamin;

    public function init()
    {
        parent::init();
        $this->_restPenjamin = Yii::$app->docoRest->penjaminasuransi;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        $newActions = [
            'index' => 'Doco\penjaminasuransi\actions\InformasiPasienRajalBpjs\IndexAction',
            'get-data' => 'Doco\penjaminasuransi\actions\InformasiPasienRajalBpjs\GetDataAction',
        ];
        $actions = array_merge($actions, $newActions);
        return $actions;
    }

    public function actionProses($id,$no_pendaftaran)
    {
        $request = Yii::$app->request;
        $title = 'Klaim Rajal';
        $id_dec = DocoHelpers::decrypt($id);
        $model = new FormKoreksi;
        $diagnosa = $this->getDetail($id_dec,$no_pendaftaran);
        $status_kunjungan = ArrayHelper::getValue($diagnosa['data'], 'status_kunjungan');
        $info = ArrayHelper::getValue($diagnosa, 'data', []);
        return $this->render('proses', get_defined_vars());
    }

    public function getDetail($id,$no_pendaftaran)
    {
        $state = true;
        $data = $detail = $mapping = $hasil_diagnosa = [];
        $final = false;
        try {
            $response = $this->_restPenjamin->get('inf-pasien-rajal-bpjs/detail', [
                'query' => [
                    'id' => $id,
                    'no_pendaftaran' => $no_pendaftaran,
                ],
            ]);
            $response = json_decode($response->getBody(), true);
            $response = ArrayHelper::getValue($response, 'response');;
            $data = ArrayHelper::getValue($response, 'header');
            $data_detail = ArrayHelper::getValue($response, 'detail');
            $mapping = ArrayHelper::getValue($response, 'mapping');
            $hasil_diagnosa = ArrayHelper::getValue($response, 'hasil_diagnosa');
            $statusKunjungan = ArrayHelper::getValue($data, 'status_kunjungan');
            $state = ($statusKunjungan == '' || $statusKunjungan == '0') ? true : false;
            $kunjunganId = ArrayHelper::getValue($response, 'kunjungan_id');
            $detail = [];
            $penyerta = 0;
            $count_detail = count($data_detail);
            $detail = [
                'Utama' => isset($hasil_diagnosa['diagnosa_utama'][0]) ? $hasil_diagnosa['diagnosa_utama'][0] : [[]],
                'Tambahan' => $hasil_diagnosa['diagnosa_tambahan'] ? $hasil_diagnosa['diagnosa_tambahan'] : [[]],
                'Tindakan/Operasi' => $hasil_diagnosa['diagnosa_opertindakan'] ? $hasil_diagnosa['diagnosa_opertindakan'] : [[]],
                'Sebab Luar' => $hasil_diagnosa['diagnosa_luar'] ? $hasil_diagnosa['diagnosa_luar'] : [[]],
                'Morfologi' => $hasil_diagnosa['diagnosa_morfologi'] ? $hasil_diagnosa['diagnosa_morfologi'] : [[]],
            ];

            $response = [
                'state' => $state,
                'data' => $data,
                'data_detail' => $data_detail,
                'detail' => $detail,
                'count_detail' => $count_detail,
                'mapping' => $mapping,
                'hasil_diagnosa' => $hasil_diagnosa,
                'penyerta' => $penyerta,
                'fl_detail' => ArrayHelper::getValue($response, 'fl_detail'),
                'kunjungan_id' => $kunjunganId
            ];
            return $response;
        } catch (RequestException $e) {
            $result = ['response' => ['title' => "Terjadi Kesalahan", 'text' => 'Edit Data Klaim Gagal']];
            return DocoHelpers::response($result, 422);
        }
    }

    public function actionGetRandomString()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return DocoHelpers::generateRandomString();
    }

    public function actionSinkron($randString)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);
        $session['randString'] = $randString;
        return $this->guzzleExec($this->_restPenjamin, [
            'url' => "sinkronisasi/sinkron",
            'payload' => [
                'query' => $session
            ],
        ]);
    }

    public function actionSingleSync()
    {
        $title = 'Sinkron Pasien';
        $model = new SyncPasienForm;
        $instalasi = [
            DocoConstants::SINGKATAN_RJ => DocoConstants::TITLE_RJ, 
            DocoConstants::SINGKATAN_RD => DocoConstants::TITLE_RD, 
            DocoConstants::SINGKATAN_RI => DocoConstants::TITLE_RI
        ];

        return $this->renderPartial('sync', get_defined_vars());
    }

    public function actionSingleSyncSave($randString,$instalasi,$no)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);
        $session['randString'] = $randString;
        $session['instalasi'] = $instalasi;
        $session['no_pendaftaran'] = $no;
        return $this->guzzleExec($this->_restPenjamin, [
            'url' => "single-sync/single-sinkron",
            'payload' => [
                'query' => $session
            ],
        ]);
    }

    public function actionGetIcd()
    {
        $request = Yii::$app->request;
        $type_icd = $request->get('type_icd');
        $term = $request->get('term');
        $not_in = $request->get('not_in');
        try {
            $cache_diagnosa = Yii::$app->cache->get("cache_diagnosa");
            if (!$cache_diagnosa) {
                $response = $this->_restPenjamin->get('inf-pasien-rajal-bpjs/get-list-diagnosa');
                $response = json_decode($response->getBody(), true);
                $diagnosa = $response['response']['diagnosa'];
                Yii::$app->cache->set("cache_diagnosa", $diagnosa);
                $cache_diagnosa = $diagnosa;
            }
            $term = '/' . strtoupper($term) . '/';
            $find_data = array_filter($cache_diagnosa, function ($a) use ($term) {
                $a = str_replace(".", "", $a);
                $diagnosaNama = strtoupper(ArrayHelper::getValue($a, 'diagnosa_nama'));
                $a = [
                    'diagnosa_id' => ArrayHelper::getValue($a, 'diagnosa_id'),
                    'diagnosa_kode' => ArrayHelper::getValue($a, 'diagnosa_kode'),
                    'diagnosa_nama' => $diagnosaNama,
                    'tabularlist_versi' => ArrayHelper::getValue($a, 'tabularlist_versi'),
                ];
                $term = str_replace(".", "", $term);
                return preg_grep($term, $a);
            });
            $data = [];
            if ($find_data) {
                if (!$not_in) {
                    $not_in = [];
                }
                foreach ($find_data as $value) {
                    if (strtolower(trim($value['tabularlist_versi'])) == strtolower(trim($type_icd))) {
                        if (!in_array($value['diagnosa_id'], $not_in)) {
                            $data[] = [
                                'id' => $value['diagnosa_id'],
                                'text' => $value['diagnosa_kode'] . ' - ' . $value['diagnosa_nama'],
                            ];
                        }
                    }
                }
            }
        } catch (RequestException $e) {
            $data = [];
        }

        return DocoHelpers::response([
            'result' => $data,
        ]);
    }

    public function actionSimpanKoreksi($id)
    {
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $formkoreksi = $request->post('FormKoreksi');
        $disable_edit = ($request->post('disable_edit') == 'true') ? true : false;
        if ($disable_edit) {
            $result = [
                'id' => DocoHelpers::encrypt($id),
                'updated' => DocoHelpers::encrypt(1),
            ];
            return DocoHelpers::responseTemplate(200, 'Koreksi Berhasil ditambahakan', $result);
        }

        if (!isset($formkoreksi['is_inacbg'])) {
            $msg = ['response' => ['text' => 'Belum ada diagnosa yang dipilih', 'title' => 'Terjadi Kesalahan']];
            return DocoHelpers::response($msg, 422);
        }
        if (!isset($formkoreksi['is_icdprimer'])) {
            $msg = ['response' => ['text' => 'Belum ada Icd Primary yang dipilih', 'title' => 'Terjadi Kesalahan']];
            return DocoHelpers::response($msg, 422);
        }
        
        try {
            $editKoreksi = $request->post('edit_koreksi');
            $model = new FormKunjunganPasien;
            $model->koreksi_diagnosa = $request->post('koreksi_diagnosa');
            $model->dokter_nama = $request->post('dokter_nama');
            $model->kunjungan_id = $request->post('kunjungan_id');
            $model->data_koreksi = $request->post('data_koreksi');
            $model->total_data = $request->post('total_data');
            $model->is_inacbg = ArrayHelper::getValue($formkoreksi, 'is_inacbg', false);
            $model->is_icdprimer = ArrayHelper::getValue($formkoreksi, 'is_icdprimer', false);
            $model->dokterdpjp_id = $request->post('dokterdpjp_id');
            if ($model->validate()) {
                $response = $this->_restPenjamin->post('inf-pasien-rajal-bpjs/save', [
                    'form_params' => $model->attributes,
                    'query' => [
                        'id' => $id,
                        'edit_koreksi' => $editKoreksi
                    ],
                ]);
                $response = json_decode($response->getBody(), true);
                if ($response['metadata']['status'] == 200) {
                    $result = [
                        'id' => DocoHelpers::encrypt($id),
                        'updated' => DocoHelpers::encrypt(1),
                    ];
                    return DocoHelpers::responseTemplate(200, 'Koreksi Berhasil ditambahakan', $result);
                } else {
                    return DocoHelpers::responseTemplate(422, 'Koreksi Gagal ditambahkan');
                }
            } else {
                return DocoHelpers::responseTemplate(422, $model->errors);
            }
        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(422, 'Terjadi Kesalahan');
        }
    }

    public function actionEklaim($id)
    {
        $request = Yii::$app->request;
        $title = 'E-Klaim INACBGS';
        $info = [];
        $opsi = [];
        $model = new KlaimInacbgForm;
        $id_dec = $id;
        $id = DocoHelpers::decrypt($id);
        $state = false;
        $isAjukan = false;
        $detailDiagnosa = json_encode([]);
        $diagnosa10 = array();
        $diagnosa9 = array();
        $update_dec = null;
        if ($request->post()) {
            $post = $request->post();
            $klaimInacbgForm = ArrayHelper::getValue($post, 'KlaimInacbgForm', []);
            $jenisKelasRawat = ArrayHelper::getValue($klaimInacbgForm, 'jenis_kelasrawat');
            $model->attributes = $klaimInacbgForm;
            $model->rujukanrs = isset($klaimInacbgForm['rujukanrs']) ? $klaimInacbgForm['rujukanrs'] : null;
            $model->berat_lahir = preg_replace('/[^A-Za-z0-9\-]/', '', ArrayHelper::getValue($klaimInacbgForm, 'berat_lahir'));
            if($model->instalasi_kode == DocoConstants::SINGKATAN_RI){
                $model->instalasi_id = DocoConstants::INSTALASI_ID_RI;
                $model->tgl_keluar = date('Y-m-d H:i', strtotime($model->tgl_keluar));
            }else{
                $model->instalasi_id = DocoConstants::INSTALASI_ID_RJ;
                $model->tgl_keluar = date('Y-m-d H:i', strtotime($model->tgl_masuk));
            }
            $model->tgl_masuk = date('Y-m-d H:i', strtotime($model->tgl_masuk));
            $model->jenis_kelasrawat = ($jenisKelasRawat == 1) ? '1' : '3';
            $diagnosaTambahan = ArrayHelper::getValue($post, 'KlaimInacbgDetail', []);
            $diagnosaDeleted = ArrayHelper::getValue($post, 'DiagnosaDeleted', []);
            $isPrimer = ArrayHelper::getValue($post, 'primer', []);
            $response = [];
            try {
                if ($model->total_tarifrs == 0 || $model->total_tarifrs < 15000) {
                    $errText = "Tarif RS Masih Belum Valid.";
                    $result = ['response' => ['text' => $errText]];
                    return DocoHelpers::response($result, 422);
                } else if ($model->nama_dokter == '' && $model->dokter_id == 0) {
                    $errText = "Dokter DPJP Tidak Boleh Kosong.";
                    $result = ['response' => ['text' => $errText]];
                    return DocoHelpers::response($result, 422);
                } else {
                    if ($model->validate()) {
                        if (count($diagnosaTambahan)) {
                            $diagnosaDetail = [];
                            $procedureDetail = [];
                            foreach ($diagnosaTambahan as $key => $value) {
                                $diagnosaType = ArrayHelper::getValue($value, 'diagnosa_type');
                                $kodeDiagnosa = ArrayHelper::getValue($value, 'kode_diagnosa');
                                $diagnosaTambahan[$key]['icd_versi'] = $diagnosaType;
                                if ($diagnosaType == 9) {
                                    $procedureDetail[] = $kodeDiagnosa;
                                } else {
                                    $diagnosaDetail[] = $kodeDiagnosa;
                                }
                            }
                            $diagnosaPrimer = explode('#', $model->diagnosa_primer);
                            $diagnosaPrimer = array_merge($diagnosaPrimer, $diagnosaDetail);
                            if (!empty($model->diagnosa_sekunder)) {
                                $diagnosaSekunder = explode('#', $model->diagnosa_sekunder);
                                $diagnosaSekunder = array_merge($diagnosaSekunder, $procedureDetail);
                            } else {
                                $diagnosaSekunder = $procedureDetail;
                            }

                            $model->diagnosa_primer = implode('#', $diagnosaPrimer);
                            $model->diagnosa_sekunder = implode('#', $diagnosaSekunder);
                        }
                        try {
                            $klaimPenjamin = [
                                DocoConstants::PAYOR_ID_COVID,
                                DocoConstants::PAYOR_ID_KIPI,
                                DocoConstants::PAYOR_ID_JAMPERSAL
                            ];
                            if(in_array($model->klaim_penjamin, $klaimPenjamin)) {
                                
                                $response = $this->_restPenjamin->post('inf-pasien-rajal-bpjs-covid/proses-eklaim', ['form_params' => ['data' => $model->attributes, 'detail' => $diagnosaTambahan, 'deleted' => $diagnosaDeleted, 'primer' => $isPrimer]]);
                                $body = json_decode($response->getBody(), true);
                                if ($body['metadata']['status'] != 200) {
                                    $result = ['response' => ['text' =>  isset($body['response']) ? $body['response']['text'] : $body, 'title' => isset($body['response']) ? $body['response']['title'] : $body]];
                                    return DocoHelpers::response($result, 422);
                                }
                                return DocoHelpers::response($body['response']);
                            } else {
                                $response = $this->_restPenjamin->post('inf-pasien-rajal-bpjs/proses-eklaim', ['form_params' => ['data' => $model->attributes, 'detail' => $diagnosaTambahan, 'deleted' => $diagnosaDeleted, 'primer' => $isPrimer]]);
                                $body = json_decode($response->getBody(), true);
                                return DocoHelpers::response($body['response']);
                            }
                        } catch (RequestException $e) {
                            return $e->getMessage();
                        } catch (\Exception $e) {
                            return $e->getMessage();
                        }
                        return true;
                    }
                    return $response;
                }
            } catch (\Exception $e) {
                var_dump($e->getMessage());
                die();
            } catch (RequestException $e) {
                var_dump($e->getMessage());
                die();
            }
        }
        else {
            $response = $this->_restPenjamin->get('inf-pasien-rajal-bpjs/get-data', ['query' => ['id' => $id]]);
            $body = json_decode($response->getBody(), true);
            $response = ArrayHelper::getValue($body, 'response', []);
            $info = ArrayHelper::getValue($response, 'info', []);
            $checkLastKlaim = ArrayHelper::getValue($response, 'klaim', []);
            $opsi = ArrayHelper::getValue($response, 'opsi', []);            
            $detail = ArrayHelper::getValue($response, 'detailtarif', []);
            $diagnosa = ArrayHelper::getValue($response, 'diagnosa', []);
            $inacbgData = ArrayHelper::getValue($response, 'inacbg', []);
            $detailInacbg = ArrayHelper::getValue($inacbgData, 'detailinacbg', []);
            $detailDiagnosa = json_encode($detailInacbg);


            $statusVerifikasi = ArrayHelper::getValue($info, 'status_kunjungan');
            $umur = ArrayHelper::getValue($info, 'umur');
            $tmpTarif = 0;
            foreach ($detail as $k => $v) {
                $tmpTarif += $v;
            }
            if(!empty($statusVerifikasi)) {
                $update_dec = DocoHelpers::decrypt($statusVerifikasi);
            }

            if(!empty($umur)) {
                $expUmur = explode(" Tahun", $umur);
                $expUmur = ArrayHelper::getValue($expUmur, 0) . ' Tahun';
            }
            
            $instalasiNama = DocoConstants::TITLE_RJ;
            $jenisKelasRawat = ArrayHelper::getValue($info, 'jenis_kelasrawat');
            $jeniskelasNama = ($jenisKelasRawat == 0) ? 'Reguler' : 'Eksekutif';
            $jenisRawat = $instalasiNama . ' Kelas ' . $jeniskelasNama;
            $kodeTarif = ArrayHelper::getValue($checkLastKlaim, 'tarif', DocoConstants::DEFAULT_KODE_INACBG);
            $defaultJenisTarif = ArrayHelper::getValue($opsi, 'tarifrs');
            $jenistarif = ArrayHelper::map($defaultJenisTarif, 'lookup_kode', 'lookup_name');
            $infoTxt = "INACBG @ " . date('d M Y H:i') . ' - ' . DocoConstants::DEFAULT_KELAS_INACBG . ' - TARIF : ';
            $info['los'] = 1;
            $info['adl_subacute'] = '';
            $info['adl_cronic'] = '';
            
            $inacbgsDiagnosa = [];
            $inacbgsProcedure = [];
            $dupliDiagnosa = [];
            
            foreach ($diagnosa as $key => $value) {
                $kelompokDiagnosaId = ArrayHelper::getValue($value, 'kelompokdiagnosa_id');
                $diagnosaId = ArrayHelper::getValue($value, 'diagnosa_id');
                $diagnosaKode = ArrayHelper::getValue($value, 'diagnosa_kode');
                $diagnosaNama = ArrayHelper::getValue($value, 'diagnosa_nama');
                $isIcdPrimer = ArrayHelper::getValue($value, 'is_icdprimer', false);

                if(!empty($kelompokDiagnosaId)) {
                    if ($kelompokDiagnosaId == DocoConstants::MAP_DIAGNOSA_OPERTINDAKAN) {
                        if (!in_array($diagnosaKode, $inacbgsProcedure)) {
                            $inacbgsProcedure[] = $diagnosaKode;
                        }
                    }
                    else {
                        if (!in_array($diagnosaKode, $inacbgsDiagnosa)) {
                            if ($isIcdPrimer) {
                                array_unshift($inacbgsDiagnosa, $diagnosaKode);
                            } else {
                                $inacbgsDiagnosa[] = $diagnosaKode;
                            }
                        }
                   
                    }

                    if($kelompokDiagnosaId == DocoConstants::DIAGNOSA_UTAMA) {
                        if(! in_array($diagnosaKode, $dupliDiagnosa)) {
                            $dupliDiagnosa[] = $diagnosaKode; 
                            array_push($diagnosa10, array(
                                "diagnosa_id"   => $diagnosaId,
                                "diagnosa_nama" => $diagnosaNama,
                                "diagnosa_kode" => $diagnosaKode,
                                "is_icdprimer"  => $isIcdPrimer
                            ));
                        }
                    }else{
                        if(! in_array($diagnosaKode, $dupliDiagnosa)) {
                            $dupliDiagnosa[] = $diagnosaKode; 
                            array_push($diagnosa9, array(
                                "diagnosa_id"   => $diagnosaId,
                                "diagnosa_nama" => $diagnosaNama,
                                "diagnosa_kode" => $diagnosaKode,
                            ));
                        }
                    }
                }
            }

            $dokterKode = ArrayHelper::getValue($info, 'dokter_kode');
            $dokterNama = ArrayHelper::getValue($info, 'dokter_nama');
            $instalasiNama = ArrayHelper::getValue($info, 'instalasi_nama');
            $klaimInacbgId = ArrayHelper::getValue($info, 'klaiminacbg_id');
            $nosep = ArrayHelper::getValue($info, 'nosep');
            $noKartu = ArrayHelper::getValue($info, 'no_kartu');

            $model->attributes = $info;
            $model->dokter_kode = $dokterKode;
            $model->nama_pasien = ArrayHelper::getValue($info, 'nama_pasien');
            $model->berat_lahir = !empty($checkLastKlaim['berat_lahir']) ? $checkLastKlaim['berat_lahir'] : '';
            $model->nama_dokter = !empty($checkLastKlaim['nama_dokter']) ? $checkLastKlaim['nama_dokter'] : $dokterNama;
            $model->tgl_masuk = ArrayHelper::getValue($info, 'tgl_pendaftaran');
            $model->tgl_keluar = ArrayHelper::getValue($info, 'tgl_pulang');
            $model->no_rekam_medik = ArrayHelper::getValue($info, 'no_rekammedik');
            $model->no_sep = !empty($checkLastKlaim['no_sep']) ? $checkLastKlaim['no_sep'] : $nosep;
            $model->instalasi_kode = ArrayHelper::getValue($info, 'instalasi_kode');
            $model->total_tarifrs = isset($tmpTarif) ? (float) $tmpTarif : 0;
            $model->pasien_id = ArrayHelper::getValue($info, 'pasien_id');
            $model->kelas_bpjs = ArrayHelper::getValue($info, 'kelas_bpjs');
            $model->jenis_kelasrawat = ArrayHelper::getValue($info, 'jenis_kelasrawat');
            $model->carapulang_id = !empty($checkLastKlaim['carapulang_id']) ? $checkLastKlaim['carapulang_id'] : ArrayHelper::getValue($info, 'carakeluar_value');
            $model->tarif = $kodeTarif;
            $model->no_kartu = $noKartu;
            $model->diagnosa_primer = implode('#', $inacbgsDiagnosa);
            $model->diagnosa_sekunder = implode('#', $inacbgsProcedure);
            $model->tgl_lahir = ArrayHelper::getValue($info, 'tgl_lahir');
            $model->no_pendaftaran = ArrayHelper::getValue($info, 'no_pendaftaran');
            $dokterDpjp[] = !empty($checkLastKlaim['nama_dokter']) ? $checkLastKlaim['nama_dokter'] : $dokterNama;

            /* start group tarif */
            $model->prosedur_bedah = $this->setAttr($checkLastKlaim, $detail, 'prosedur_bedah');
            $model->prosedur_nonbedah = $this->setAttr($checkLastKlaim, $detail, 'prosedur_nonbedah');
            $model->konsultasi = $this->setAttr($checkLastKlaim, $detail, 'konsultasi');

            $model->tenaga_ahli = $this->setAttr($checkLastKlaim, $detail, 'tenaga_ahli');
            $model->keperawatan = $this->setAttr($checkLastKlaim, $detail, 'keperawatan');
            $model->penunjang = $this->setAttr($checkLastKlaim, $detail, 'penunjang');

            $model->radiologi = $this->setAttr($checkLastKlaim, $detail, 'radiologi');
            $model->laboratorium = $this->setAttr($checkLastKlaim, $detail, 'laboratorium');
            $model->pelayanan_darah = $this->setAttr($checkLastKlaim, $detail, 'pelayanan_darah');
            $model->rehabilitasi = $this->setAttr($checkLastKlaim, $detail, 'rehabilitasi');
            $model->kamar_akomodasi = $this->setAttr($checkLastKlaim, $detail, 'kamar_akomodasi');

            $model->rawat_intensif = $this->setAttr($checkLastKlaim, $detail, 'rawat_intensif');
            $model->obat = $this->setAttr($checkLastKlaim, $detail, 'obat');
            $model->alkes = $this->setAttr($checkLastKlaim, $detail, 'alkes');
            $model->bmhp = $this->setAttr($checkLastKlaim, $detail, 'bmhp');
            $model->sewa_alat = $this->setAttr($checkLastKlaim, $detail, 'sewa_alat');
            $model->obat_kemoterapi = $this->setAttr($checkLastKlaim, $detail, 'obat_kemoterapi');
            $model->obat_kronis = $this->setAttr($checkLastKlaim, $detail, 'obat_kronis');


            if (isset($detail['prosedur_bedah'])) {
                $model->prosedur_bedah = !empty($lastProsedurBedah) ? DocoHelpers::formatNumber($lastProsedurBedah) : DocoHelpers::formatNumber(ArrayHelper::getValue($detail, 'prosedur_bedah', 0));
            } else {
                $model->prosedur_bedah = !empty($lastProsedurBedah) ? DocoHelpers::formatNumber($lastProsedurBedah) : 0;
            }

            if (isset($detail['prosedur_non_bedah'])) {
                $model->prosedur_nonbedah = !empty($lastProsedurNonBedah) ? DocoHelpers::formatNumber($lastProsedurNonBedah) : DocoHelpers::formatNumber(ArrayHelper::getValue($detail, 'prosedur_non_bedah', 0));
            } else {
                $model->prosedur_nonbedah = !empty($lastProsedurNonBedah) ? DocoHelpers::formatNumber($lastProsedurNonBedah) : 0;
            }

            if (isset($detail['konsultasi'])) {
                $model->konsultasi = !empty($lastKonsultasi) ? DocoHelpers::formatNumber($lastKonsultasi) : DocoHelpers::formatNumber(ArrayHelper::getValue($detail, 'konsultasi', 0));
            } else {
                $model->konsultasi = !empty($lastKonsultasi) ? DocoHelpers::formatNumber($lastKonsultasi) : 0;
            }

            if (isset($detail['tenaga_ahli'])) {
                $model->tenaga_ahli = !empty($checkLastKlaim['tenaga_ahli']) ? DocoHelpers::formatNumber($checkLastKlaim['tenaga_ahli']) : DocoHelpers::formatNumber($detail['tenaga_ahli']);
            } else {
                $model->tenaga_ahli = !empty($checkLastKlaim['tenaga_ahli']) ? DocoHelpers::formatNumber($checkLastKlaim['tenaga_ahli']) : 0;
            }
            if (isset($detail['keperawatan'])) {
                $model->keperawatan = !empty($checkLastKlaim['keperawatan']) ? DocoHelpers::formatNumber($checkLastKlaim['keperawatan']) : DocoHelpers::formatNumber($detail['keperawatan']);
            } else {
                $model->keperawatan = !empty($checkLastKlaim['keperawatan']) ? DocoHelpers::formatNumber($checkLastKlaim['keperawatan']) : 0;
            }
            if (isset($detail['penunjang'])) {
                $model->penunjang = !empty($checkLastKlaim['penunjang']) ? DocoHelpers::formatNumber($checkLastKlaim['penunjang']) : DocoHelpers::formatNumber($detail['penunjang']);
            } else {
                $model->penunjang = !empty($checkLastKlaim['penunjang']) ? DocoHelpers::formatNumber($checkLastKlaim['penunjang']) : 0;
            }

            if (isset($detail['radiologi'])) {
                $model->radiologi = !empty($checkLastKlaim['radiologi']) ? DocoHelpers::formatNumber($checkLastKlaim['radiologi']) : DocoHelpers::formatNumber($detail['radiologi']);
            } else {
                $model->radiologi = !empty($checkLastKlaim['radiologi']) ? DocoHelpers::formatNumber($checkLastKlaim['radiologi']) : 0;
            }
            if (isset($detail['laboratorium'])) {
                $model->laboratorium = !empty($checkLastKlaim['laboratorium']) ? DocoHelpers::formatNumber($checkLastKlaim['laboratorium']) : DocoHelpers::formatNumber($detail['laboratorium']);
            } else {
                $model->laboratorium = !empty($checkLastKlaim['laboratorium']) ? DocoHelpers::formatNumber($checkLastKlaim['laboratorium']) : 0;
            }
            if (isset($detail['pelayanan_darah'])) {
                $model->pelayanan_darah = !empty($checkLastKlaim['pelayanan_darah']) ? DocoHelpers::formatNumber($checkLastKlaim['pelayanan_darah']) : DocoHelpers::formatNumber($detail['pelayanan_darah']);
            } else {
                $model->pelayanan_darah = !empty($checkLastKlaim['pelayanan_darah']) ? DocoHelpers::formatNumber($checkLastKlaim['pelayanan_darah']) : 0;
            }
            if (isset($detail['rehabilitasi'])) {
                $model->rehabilitasi = !empty($checkLastKlaim['rehabilitasi']) ? DocoHelpers::formatNumber($checkLastKlaim['rehabilitasi']) : DocoHelpers::formatNumber($detail['rehabilitasi']);
            } else {
                $model->rehabilitasi = !empty($checkLastKlaim['rehabilitasi']) ? DocoHelpers::formatNumber($checkLastKlaim['rehabilitasi']) : 0;
            }
            if (isset($detail['kamar_akomodasi'])) {
                $model->kamar_akomodasi = !empty($checkLastKlaim['kamar_akomodasi']) ? DocoHelpers::formatNumber($checkLastKlaim['kamar_akomodasi']) : DocoHelpers::formatNumber($detail['kamar_akomodasi']);
            } else {
                $model->kamar_akomodasi = !empty($checkLastKlaim['kamar_akomodasi']) ? DocoHelpers::formatNumber($checkLastKlaim['kamar_akomodasi']) : 0;
            }
            if (isset($detail['rawat_intensif'])) {
                $model->rawat_intensif = !empty($checkLastKlaim['rawat_intensif']) ? DocoHelpers::formatNumber($checkLastKlaim['rawat_intensif']) : DocoHelpers::formatNumber($detail['rawat_intensif']);
            } else {
                $model->rawat_intensif = !empty($checkLastKlaim['rawat_intensif']) ? DocoHelpers::formatNumber($checkLastKlaim['rawat_intensif']) : 0;
            }
            if (isset($detail['obat'])) {
                $model->obat = !empty($checkLastKlaim['obat']) ? DocoHelpers::formatNumber($checkLastKlaim['obat']) : DocoHelpers::formatNumber($detail['obat']);
            } else {
                $model->obat = !empty($checkLastKlaim['obat']) ? DocoHelpers::formatNumber($checkLastKlaim['obat']) : 0;
            }
            if (isset($detail['alkes'])) {
                $model->alkes = !empty($checkLastKlaim['alkes']) ? DocoHelpers::formatNumber($checkLastKlaim['alkes']) : DocoHelpers::formatNumber($detail['alkes']);
            } else {
                $model->alkes = !empty($checkLastKlaim['alkes']) ? DocoHelpers::formatNumber($checkLastKlaim['alkes']) : 0;
            }
            if (isset($detail['bmhp'])) {
                $model->bmhp = !empty($checkLastKlaim['bmhp']) ? DocoHelpers::formatNumber($checkLastKlaim['bmhp']) : DocoHelpers::formatNumber($detail['bmhp']);
            } else {
                $model->bmhp = !empty($checkLastKlaim['bmhp']) ? DocoHelpers::formatNumber($checkLastKlaim['bmhp']) : 0;
            }
            if (isset($detail['sewa_alat'])) {
                $model->sewa_alat = !empty($checkLastKlaim['sewa_alat']) ? DocoHelpers::formatNumber($checkLastKlaim['sewa_alat']) : DocoHelpers::formatNumber($detail['sewa_alat']);
            } else {
                $model->sewa_alat = !empty($checkLastKlaim['sewa_alat']) ? DocoHelpers::formatNumber($checkLastKlaim['sewa_alat']) : 0;
            }
            if (isset($detail['obat_kemoterapi'])) {
                $model->obat_kemoterapi = !empty($checkLastKlaim['obat_kemoterapi']) ? DocoHelpers::formatNumber($checkLastKlaim['obat_kemoterapi']) : DocoHelpers::formatNumber($detail['obat_kemoterapi']);
            } else {
                $model->obat_kemoterapi = !empty($checkLastKlaim['obat_kemoterapi']) ? DocoHelpers::formatNumber($checkLastKlaim['obat_kemoterapi']) : 0;
            }
            if (isset($detail['obat_kronis'])) {
                $model->obat_kronis = !empty($checkLastKlaim['obat_kronis']) ? DocoHelpers::formatNumber($checkLastKlaim['obat_kronis']) : DocoHelpers::formatNumber($detail['obat_kronis']);
            } else {
                $model->obat_kronis = !empty($checkLastKlaim['obat_kronis']) ? DocoHelpers::formatNumber($checkLastKlaim['obat_kronis']) : 0;
            }
            /* end group tarif*/

            $model->los = 1;
            $pengajuanKlaimDetailId = ArrayHelper::getValue($info, 'pengajuanklaimdetail_id');
            $tarifPoliEksekutif = ArrayHelper::getValue($info, 'tarif_polieksekutif', 0);
            $model->tarif_poli_eks = DocoHelpers::formatNumber($tarifPoliEksekutif);
            $isAjukan = !empty($pengajuanKlaimDetailId) ? true : false;
            $idEnc = DocoHelpers::encrypt(ArrayHelper::getValue($info, 'kunjungan_id'));
            $stateEnc = DocoHelpers::encrypt($statusVerifikasi);

            // penambhan case covid
            $listJaminan = DocoConstants::LIST_JAMINAN;
            $listStatusCovid = DocoConstants::LIST_STATUS_COVID;
            $listIdentitas = DocoConstants::LIST_IDENTITAS_PASIEN;
            $totalEpisode = ArrayHelper::getValue($checkLastKlaim, 'total_episode', 0);

            $model->total_episode = DocoHelpers::formatNumber($totalEpisode);
            $model->is_komplikasi = ArrayHelper::getValue($checkLastKlaim, 'is_komplikasi', false);
            $model->status_covid = ArrayHelper::getValue($checkLastKlaim, 'status_covid');
            $model->is_pemulasaranjenazah = ArrayHelper::getValue($checkLastKlaim, 'is_pemulasaranjenazah', false);
            $model->is_kantongjenazah = ArrayHelper::getValue($checkLastKlaim, 'is_kantongjenazah', false);
            $model->is_petijenazah = ArrayHelper::getValue($checkLastKlaim, 'is_petijenazah', false);
            $model->is_plastikerat = ArrayHelper::getValue($checkLastKlaim, 'is_plastikerat', false);
            $model->is_desinfektanmobil = ArrayHelper::getValue($checkLastKlaim, 'is_desinfektanmobil', false);
            $model->is_desinfektanjenazah = ArrayHelper::getValue($checkLastKlaim, 'is_desinfektanjenazah', false);
            $model->is_transport = ArrayHelper::getValue($checkLastKlaim, 'is_transport', false);
            $opsiKomplikasi = ['0' => 'Tidak Ada', '1' => 'Ada'];
            $payorCovid = DocoConstants::PAYOR_ID_COVID;
            $payorJkn = DocoConstants::PAYOR_ID_JKN;
            $payorKipi = DocoConstants::PAYOR_ID_KIPI;
            $payorBayi = DocoConstants::PAYOR_ID_BAYI_BARU_LAHIR;
            $payorPerpanjanganRawat = DocoConstants::PAYOR_ID_PERPANJANGAN_MASA_RAWAT;
            $payorCoinsidense = DocoConstants::PAYOR_ID_COINSIDENSE;
            $payorJampersal = DocoConstants::PAYOR_ID_JAMPERSAL;
            
            $opsiPenjamin = [];
            foreach ($opsi['inacbg_penjamin'] as $key => $value) {
                if(in_array($value['lookup_value'],
                    [
                        DocoConstants::PAYOR_ID_COVID,
                        DocoConstants::PAYOR_ID_JKN,
                        DocoConstants::PAYOR_ID_KIPI,
                        DocoConstants::PAYOR_ID_JAMPERSAL
                    ]
                )) {

                    $opsiPenjamin[] = $value;
                }
            }
            
            // $dupliDiagnosa = [];
            // foreach ($diagnosa as $key => $value) {
            //     if(isset($value['kelompokdiagnosa_id'])){
            //         if ($value['kelompokdiagnosa_id'] != DocoConstants::DIAGNOSA_PENYERTA) {
            //             if(! in_array($value['diagnosa_kode'], $dupliDiagnosa)) {
            //                 $dupliDiagnosa[] = $value['diagnosa_kode']; 
            //                 array_push($diagnosa10, array(
            //                     "diagnosa_id"   => ArrayHelper::getValue($value, 'diagnosa_id'),
            //                     "diagnosa_nama" => ArrayHelper::getValue($value, 'diagnosa_nama'),
            //                     "diagnosa_kode" => ArrayHelper::getValue($value, 'diagnosa_kode'),
            //                     "is_icdprimer"  => ArrayHelper::getValue($value, 'is_icdprimer')
            //                 ));
            //             }
            //         }else{
            //             if(! in_array($value['diagnosa_kode'], $dupliDiagnosa)) {
            //                 $dupliDiagnosa[] = $value['diagnosa_kode']; 
            //                 array_push($diagnosa9, array(
            //                     "diagnosa_id"   => ArrayHelper::getValue($value, 'diagnosa_id'),
            //                     "diagnosa_nama" => ArrayHelper::getValue($value, 'diagnosa_nama'),
            //                     "diagnosa_kode" => ArrayHelper::getValue($value, 'diagnosa_kode'),
            //                 ));
            //             }
            //         }
            //     }
            // }
        }
        
        return $this->render('eklaim', get_defined_vars());
    }

    public function actionGetKlaim()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $nosep = ArrayHelper::getValue($post, 'no_sep');
        $pendaftaran_id = ArrayHelper::getValue($post, 'pendaftaran_id');
        $getklaim = [
            'metadata' => [
                'method' => 'get_claim_data',
            ],
            'data' => [
                'nomor_sep' => $nosep,
            ],
        ];
        $klaim = DocoHelpers::restInacbgs($getklaim);
        $body = json_decode($klaim, true);
        if(!empty($body)) {
            $response = ArrayHelper::getValue($body, 'response', []);
            $responseData = ArrayHelper::getValue($response, 'data', []);
            $metadata = ArrayHelper::getValue($body, 'metadata', []);
            $code = ArrayHelper::getValue($metadata, 'code');
            $message = ArrayHelper::getValue($metadata, 'message');
            $error_no = ArrayHelper::getValue($metadata, 'error_no');
            if (!empty($code) && $code == 200) {
                $get_klaim_group = $this->getKlaimGroup($pendaftaran_id);
                $body['response']['data']['db_special_cmg'] = $get_klaim_group;
                $grouper = [];
                if (isset($responseData['klaim_status_cd']) && $responseData['klaim_status_cd'] == 'normal') {
                    $options = $this->getGrouper($nosep);
                    if ($options['metadata']['code'] == 200) {
                        $grouper = isset($options['special_cmg_option']) ? $options['special_cmg_option'] : [];
                    }
                }
                $body['response']['special_cmg_option'] = $grouper;
            }
        }

        return json_encode($body);
    }

    public function actionGetDiagnosa($type)
    {
        if (isset($_GET['q']['term']) && !empty($_GET['q']['term'])) {
            if ($type == 10) {
                $tab = 'ICD X';
            } else {
                $tab = 'ICD IX';
            }
            $response = $this->_restPenjamin->request('POST', 'allow/get-diagnosa', [
                'form_params' => ['diagnosa_nama' => $_GET['q']['term'], 'tabularlist_versi' => $tab],
            ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            $rawData = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = ['id' => $value['diagnosa_id'], 'text' => $value['diagnosa_kode'] . ' - ' . $value['diagnosa_nama']];
                $rawData[$value['diagnosa_id']] = $value;
            }
            $total = count($body['response']);
            $return = ['result' => $data, 'rawData' => $rawData, 'total_count' => $total, 'incomplete_results' => false];
            return DocoHelpers::response($return);
        }
    }

    public function actionGetDokter()
    {
        $data = [];
        try {
            $request = Yii::$app->request;
            $search = $request->get('q')['term'];
            $data = [];

            $response = $this->_restPenjamin->get('allow/get-list-dokter');
            $response = json_decode($response->getBody(), true);

            $session_id = Yii::$app->docoVars->user("id");
            $cache_dokter = Yii::$app->cache->get("cache_dokter_" . $session_id);

            if (!$cache_dokter) {
                $response = $this->_restPenjamin->get('allow/get-list-dokter');
                $response = json_decode($response->getBody(), true);
                $dokter = $response['response'];
                Yii::$app->cache->set("cache_dokter" . $session_id, $dokter);
                $cache_dokter = $dokter;
            }
            $data = $this->filter_array($cache_dokter, 'text', $search);
        } catch (RequestException $e) {
            $data = [];
        }
        $total = count($data);
        $return = ['result' => $data, 'total_count' => $total, 'incomplete_results' => false];
        return DocoHelpers::response([
            'result' => $return,
        ]);
    }

    public function actionGrouper()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $nosep = $post['nosep'];
        $code = $post['kodeDiagnosa'];
        $stage = $post['stage'];

        $getklaim = [
            'metadata' => [
                'method' => 'grouper',
                'stage' => 1,
            ],
            'data' => [
                'nomor_sep' => $nosep,
            ],
        ];

        if ($stage = 2) {
            $getklaim['metadata']['stage'] = $stage;
            $getklaim['data']['special_cmg'] = implode('#', $post['kodeDiagnosa']);
        }

        $klaim = DocoHelpers::restInacbgs($getklaim);
        $body = json_decode($klaim, true);
        return json_encode($body);
    }

    public function actionHapusKlaim()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $nosep = ArrayHelper::getValue($post, 'nosep');
        $penjamin = ArrayHelper::getValue($post, 'penjamin');
        $kunjunganId = ArrayHelper::getValue($post, 'pendaftaranid');
        $noPendaftaran = ArrayHelper::getValue($post, 'no_pendaftaran');
        try {
            $url = !empty($penjamin) && $penjamin == DocoConstants::PAYOR_ID_COVID ? 'inf-pasien-rajal-bpjs-covid/hapus-klaim' : 'inf-pasien-rajal-bpjs/hapus-klaim';
            $response = $this->_restPenjamin->post($url, [
                'form_params' =>
                [
                    'kunjungan_id' => $kunjunganId,
                    'no_sep' => $nosep,
                    'no_pendaftaran' => $noPendaftaran,
                ],
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response($body['response']);
        } catch (RequestException $e) {
            $result = ['response' => ['title' => "Terjadi Kesalahan", 'text' => 'Hapus Data Klaim Gagal']];
            return DocoHelpers::response($result, 422);
        }
        $result = ['response' => ['title' => "Proses Berhasil", 'text' => 'Hapus Data Klaim Berhasil']];
        return DocoHelpers::response($result);
    }

    public function actionAddDiagnosaTambahan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $response = $this->_restPenjamin->post('inf-pasien-rajal-bpjs/add-diagnosa-tambahan', [
                'form_params' =>
                [
                    'pendaftaran_id' => $post['id'],
                    'diagnosa_id' => $post['diagnosa_id'],
                    'diagnosa_nama' => $post['nama_diagnosa'],
                    'diagnosa_kode' => $post['kode_diagnosa'],
                    'type' => $post['type_diagnosa'],
                    'dokterdpjp_id' => $post['dokterdpjp_id']
                ],
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response($body);
        } catch (RequestException $e) {
            $result = ['response' => ['title' => "Terjadi Kesalahan"]];
            return DocoHelpers::response($result, 422);
        }
    }

    public function actionFinalKlaim()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $nosep = ArrayHelper::getValue($post, 'nosep');
        $model = new KlaimInacbgForm;
        $model->attributes = ArrayHelper::getValue($post, 'KlaimInacbgForm');
        $dataDetail = ArrayHelper::getValue($post, 'KlaimInacbgDetail', []);
        $dataGrouper = ArrayHelper::getValue($post, 'Grouper', []);
        $dataAdditional = ArrayHelper::getValue($post, 'Add', []);
        $response = $this->_restPenjamin->post('inf-pasien-rajal-bpjs-covid/final-klaim', ['form_params' => ['data' => $model->attributes, 'detail' => $dataDetail, 'grouper' => $dataGrouper, 'nosep' => $nosep, 'additional' => $dataAdditional]]);
        $body = json_decode($response->getBody(), true);
        $result = ['response' => ['title' => $body['response']['title'], 'text' => $body['response']['text']]];
        $status = 200;
        if ($body['metadata']['status'] !== 200) {
            $status = 422;
        }
        return DocoHelpers::response($result, $status);
    }

    public function actionEditUlangKlaim()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $nosep = $post['nosep'];
        try {
            $response = $this->_restPenjamin->post('inf-pasien-rajal-bpjs-covid/update-klaim', [
                'form_params' =>
                [
                    'pendaftaran_id' => $post['pendaftaranid'],
                    'nomor_sep' => $nosep,
                ],
            ]);

            $body = json_decode($response->getBody(), true);
            return json_encode($body);
            return DocoHelpers::response($body['response']);
        } catch (RequestException $e) {
            $result = ['response' => ['title' => "Terjadi Kesalahan", 'text' => 'Edit Data Klaim Gagal']];
            return DocoHelpers::response($result, 422);
        }
    }

    public function actionCetakKlaim($sep, $type = null)
    {
        $request = Yii::$app->request;
        $filename = 'filename.pdf';
        try {
            $cetakklaim = [
                'metadata' => [
                    'method' => 'claim_print',
                ],
                'data' => [
                    'nomor_sep' => $sep,
                ],
            ];
            $cetakklaim = json_decode(DocoHelpers::restInacbgs($cetakklaim), true);
            $data = ArrayHelper::getValue($cetakklaim, 'data');
            $decoded = base64_decode($data);
            if ($type == 'covid') {
                $filename = $sep . '.pdf';
            } else {
                $exp = explode('v', strtolower($sep));
                $filename = $exp[1] . '.pdf';
            }
            $file = '../uploads/' . $filename;

            file_put_contents($file, $decoded);
            return DocoHelpers::previewPdf($file);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    public function actionKirimKlaimOnline()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $response = $this->_restPenjamin->post('inf-pasien-rajal-bpjs/kirim-klaim-online', [
                'form_params' =>
                [
                    'pendaftaran_id' => ArrayHelper::getValue($post, 'pendaftaranid'),
                    'nomor_sep' => ArrayHelper::getValue($post, 'nosep'),
                ],
            ]);
            $body = json_decode($response->getBody(), true);
            $status = $body['metadata']['status'];
            return DocoHelpers::response($body, $status);
        } catch (RequestException $e) {
            $result = ['response' => ['title' => "Terjadi Kesalahan", 'text' => 'Edit Data Klaim Gagal']];
            return DocoHelpers::response($result, 422);
        }
    }

    public function actionSetPrimer()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $response = $this->_restPenjamin->post('inf-pasien-rajal-bpjs/set-primer', [
                'form_params' =>
                [
                    'id' => $post['id'],
                    'diagnosa' => $post['diagnosa'],
                ],
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response($body);
        } catch (RequestException $e) {
            $result = ['response' => ['title' => "Terjadi Kesalahan"]];
            return DocoHelpers::response($result, 422);
        }
    }

    public function actionUpdate()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $kunjungan_id = $post['data']['kunjunganId'];
        $no_sep = $post['data']['noSep'];
        $no_kartu = $post['data']['noKartu'];
        $kelas_kode = $post['data']['kelasKode'];

        try {
            $response = $this->_restPenjamin->post('inf-pasien-rajal-bpjs/update-data', [
                'form_params' => [
                    'kunjungan_id' => $kunjungan_id,
                    'no_sep' => $no_sep,
                    'no_kartu' => $no_kartu,
                    'kelas_kode' => $kelas_kode,
                ],
            ]);
            $body = json_decode($response->getBody(), true);
            return json_encode($body);
            return DocoHelpers::response($body['response']);
        } catch (RequestException $e) {
            $result = ['response' => ['title' => "Terjadi Kesalahan", 'text' => 'Edit Data Klaim Gagal']];
            return DocoHelpers::response($result, 422);
        }
    }

    public function actionGenerateNoCovid()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $payorId = DocoConstants::PAYOR_ID_COVID;
        try {
            if ($payorId) {
                $generateNoPengajuan = [
                    'metadata' => [
                        'method' => 'generate_claim_number',
                    ],
                    'data' => [
                        'payor_id' => $payorId,
                    ],
                ];
                $getNoPengajuan = DocoHelpers::restInacbgs($generateNoPengajuan);
                $body = json_decode($getNoPengajuan, true);
                if ($body['metadata']['code'] == 200) {
                    $response = $this->_restPenjamin->post('inf-pasien-rajal-bpjs-covid/update-data', [
                        'form_params' =>
                        [
                            'kunjunganId' =>  $post['kunjunganId'],
                            'nosep' => $post['nosep'],
                            'jenisIdentitas' => $post['jenisIdentitas'],
                            'noIdentitas' => $post['noIdentitas'],
                            'noPengajuan'=> $body['response']['claim_number']
                        ],
                    ]);
                    $body = json_decode($response->getBody(), true);
                    if ($body['metadata']['status'] != 200) {
                        $result = ['response' => ['text' =>  isset($body['response']) ? $body['response']['text'] : $body, 'title' => isset($body['response']) ? $body['response']['title'] : $body]];
                        return DocoHelpers::response($result, 422);
                    }
                    return DocoHelpers::response($body);
                }
            }
        } catch (RequestException $e) {
            return DocoHelpers::response('401', $e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::response('401', $e->getMessage());
        }
    }

    public function actionFilters()
    {
        $request = Yii::$app->request;
        $term = $request->get('term', null);
        $type = $request->get('type', null);
        $case = $request->get('case', null);
        $page = $request->get('page', 1);
        $additionalPayload = $request->get('additionalPayload', []);
        $response = $this->guzzleExec($this->_restPenjamin, [
            'url' => 'inf-pasien-rajal-bpjs/filters',
            'payload' => [
                'query' => [
                    'term' => $term,
                    'type' => $type,
                    'page' => $page,
                    'case' => $case,
                    'additionalPayload' => $additionalPayload
                ]
            ],
        ]);
        return $this->responseJson(200, 'Data berhasil diambil!', $response);
    }

    public function getKlaimGroup($id)
    {
        $db_special_cmg = '';
        try {
            $response = $this->_restPenjamin->get('inf-pasien-rajal-bpjs/get-klaim-group', [
                'query' => [
                    'id' => $id,
                ],
            ]);
            $response = json_decode($response->getBody(), true);
            $response = $response['response'];
            if ($response) {
                if ($response['spesial_procedure'] !== 0 && $response['spesial_procedure']) {
                    $db_special_cmg = $response['spesial_procedure'];
                } else if ($response['spesial_prosthesis'] !== 0 && $response['spesial_prosthesis']) {
                    $db_special_cmg = $response['spesial_prosthesis'];
                } else if ($response['spesial_investigation'] !== 0 && $response['spesial_investigation']) {
                    $db_special_cmg = $response['spesial_investigation'];
                } else if ($response['spesial_drug'] !== 0 && $response['spesial_drug']) {
                    $db_special_cmg = $response['spesial_drug'];
                }
            }
            return $db_special_cmg;
        } catch (RequestException $e) {
            $result = ['response' => ['title' => "Terjadi Kesalahan", 'text' => 'Edit Data Klaim Gagal']];
            return DocoHelpers::response($result, 422);
        }
    }

    public function getGrouper($nosep)
    {
        $getklaim = [
            'metadata' => [
                'method' => 'grouper',
                'stage' => 1,
            ],
            'data' => [
                'nomor_sep' => $nosep,
            ],
        ];

        $klaim = DocoHelpers::restInacbgs($getklaim);
        $body = json_decode($klaim, true);
        return $body;
    }

    public function actionGetBerkas() 
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $nosep = ArrayHelper::getValue($get, 'nosep');
        $response = $this->_restPenjamin->get('inf-pasien-rajal-bpjs/get-berkas',['query' => ['nosep' => $nosep]]);
        $body = json_decode($response->getBody(), true);
        $res = ArrayHelper::getValue($body, 'response', []);
        $data = ArrayHelper::getValue($res, 'data', []);
        $berkas= [];
        if ($data) {
            foreach ($data as $key => $value) {
                if (!isset($value['file_class'])) {
                    $berkas[$value['file_class']] = [];
                }

                $message ='';
                if ($value['upload_dc_bpjs_response']['metaData']['code'] == '401') {
                    $message = 'Gagal upload ke server BPJS. 401 Rumah Sakit Tidak Terdaftar';
                }
                $item = [
                    'file_id' => $value['file_id'],
                    'file_name' => $value['file_name'],
                    'file_size' => $value['file_size'],
                    'message' => $message,
                ];
                $berkas[$value['file_class']][] = $item;
            }
        }
        return json_encode($berkas);
    }

    public function actionUploadBerkas() 
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $fileName = 'file';
        try {
            if (isset($_FILES[$fileName])) {
                $file = \yii\web\UploadedFile::getInstanceByName($fileName);
                $tmpfile_contents = file_get_contents( $file->tempName );
                $file->saveAs(Yii::getAlias("@download").'/'.$file->name);
                $base64 = base64_encode($tmpfile_contents);
                $response = $this->_restPenjamin->post('inf-pasien-rajal-bpjs/upload-berkas', ['form_params' => 
                    [
                        'data' => $base64, 
                        'label' => $post['label'],
                        'nosep'   => $post['nosep'],
                        'filename' => $file->name
                    ]
                ]);
                $body = json_decode($response->getBody(), true);
                $res = $body['response'];
                $message = '';
                if ($res['metadata']['code'] !== 200) {
                    $code = isset($res['response']['upload_dc_bpjs_response']['metaData']['code']) ? $res['response']['upload_dc_bpjs_response']['metaData']['code'] : '';
                    $mes = isset($res['response']['upload_dc_bpjs_response']['metaData']['message']) ?  $res['response']['upload_dc_bpjs_response']['metaData']['message'] : '';
                    $message = $res['metadata']['message'] .' '. $code . ' ' . $mes;
                }
               
                $res = [
                    'code'    => $res['metadata']['code'],
                    'id'      => isset($res['response']['file_id']) ? $res['response']['file_id'] : '',
                    'message' => $message
                ];
                return json_encode($res);
            }
        }catch (RequestException $e) {
            $result = ['response' => ['title' => $e->getMessage()]];
            return DocoHelpers::response($result, 422);
        }
    }

    public function actionRemoveBerkas() 
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $file_id = ArrayHelper::getValue($get, 'file_id');
        if(isset($file_id)) {

            $response = $this->_restPenjamin->post('inf-pasien-rajal-bpjs/delete-berkas', ['form_params' => 
                [
                    'nosep'   => $get['nosep'],
                    'file_id' => $get['file_id']
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            $res = $body['response'];
            return json_encode($res);
        }
        
        return DocoHelpers::response([], 200);
    }

    private function setAttr($checkLastKlaim, $detail, $key)
    {
        $model = new KlaimInacbgForm;
        $lastProsedur = ArrayHelper::getValue($checkLastKlaim, $key);
        if (isset($detail[$key])) {
            $model->$key = !empty($lastProsedur) ? DocoHelpers::formatNumber($lastProsedur) : DocoHelpers::formatNumber(ArrayHelper::getValue($detail, $key, 0));
        } else {
            $model->$key = !empty($lastProsedur) ? DocoHelpers::formatNumber($lastProsedur) : 0;
        }

        return $model->$key;
    }

    public function actionEditKoreksi()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $no_pendaftaran = $request->get('no_pendaftaran');
        $title = 'Edit Koreksi';
        $id_dec = DocoHelpers::decrypt($id);
        $model = new FormKoreksi;
        $diagnosa = $this->getDetail($id_dec,$no_pendaftaran);
        $status_kunjungan = ArrayHelper::getValue($diagnosa['data'], 'status_kunjungan');
        $info = ArrayHelper::getValue($diagnosa, 'data', []);
        return $this->renderAjax('_modal_edit_koreksi', get_defined_vars());
    }

    public function actionValidasiSitb()
    {
        $request = Yii::$app->request;
        $nosep = $request->post('nosep');
        $nomer_sitb = $request->post('nomer_sitb');

        $response = $this->_restPenjamin->get('inf-pasien-rajal-bpjs/validasi-sitb', [
            'query' => [
                'nosep' => $nosep,
                'nomer_sitb' => $nomer_sitb
            ],
        ]);

        $body = json_decode($response->getBody(), true);
        $statusCode = ArrayHelper::getValue($body, "code");
        $response = $body['response'];
        if($statusCode == 400) {
            return DocoHelpers::response($response, $statusCode);
        }

        return DocoHelpers::response($response, $statusCode);
    }

    public function actionPreviewFile()
    {
        $request = Yii::$app->request;
        $pathName =  Yii::getAlias("@download") .'/'. $request->get('filename');
        return DocoHelpers::previewPdf($pathName);
    }

    public function actionUpdateNoklaim()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $id = ArrayHelper::getValue($get, ['kunjungan_id']);

        try {
            $response = $this->_restPenjamin->post('inf-pasien-rajal-bpjs/update-noklaim', 
            ['query' => 
                [
                    'kunjungan_id'   => $id
                ]
            ]);

            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response([
                'status' => 200,
                $body 
            ]);
        }catch (RequestException $e) {
            $result = ['response' => ['title' => $e->getMessage()]];
            return DocoHelpers::response($result, 422);
        }
    }
}
