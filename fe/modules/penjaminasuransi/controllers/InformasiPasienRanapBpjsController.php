<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-09-10 09:21:03
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2019-02-19 14:34:15
 */

namespace Doco\penjaminasuransi\controllers;

use app\components\DHtml;
use app\components\DocoConstants;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\penjaminasuransi\models\SyncPasienForm;
use DateTime;
use Doco\penjaminasuransi\models\FormKoreksi;
use Doco\penjaminasuransi\models\FormKunjunganPasien;
use Doco\penjaminasuransi\models\KlaimInacbgFormNew;
use Doco\penjaminasuransi\models\KlaimInacbgRanapForm;
use GuzzleHttp\Exception\RequestException;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use Doco\api\controllers\CacheDiagnosaController;
use app\components\Services\DiagnosaService;

class InformasiPasienRanapBpjsController extends DocoController
{

    protected $_title = "E-Klaim INACBGS";
    protected $_module = 'penjaminasuransi/informasi-pasien-ranap-bpjs/';
    protected $_restPenjamin;
    public $diagnosaService;
    protected $allowAction = ['cetak-klaim', 'upload-dokumen-eklaim'];


    public function __construct($id, $module, $config = [], DiagnosaService $diagnosaService)
    {
        $this->diagnosaService = $diagnosaService;
        parent::__construct($id, $module, $config);
    }

    public function init()
    {
        parent::init();
        $this->_restPenjamin = Yii::$app->docoRest->penjaminasuransi;
    }

    public function actionIndex()
    {
        $title = DHtml::getTitleMenu();
        $status = [];
        $penjamin = [];
        $ruangan = [];
        $session_id = Yii::$app->docoVars->user("id");
        $cache_diagnosa = Yii::$app->cache->get("cache_diagnosa_" . $session_id);
        $hasAccess = DocoHelpers::checkButtonAccess('/penjamin-asuransi/informasi-pasien-ranap-bpjs', 'unduh-dokumen');

        try {
            $response = $this->_restPenjamin->get('inf-pasien-ranap-bpjs/init-index');
            $body = json_decode($response->getBody(), true);
            $penjamin = ArrayHelper::map($body['response']['penjamin'], 'penjamin_id', 'penjamin_nama');
            $ruangan =  $body['response']['ruangan'];
            $status = ArrayHelper::map($body['response']['status_verif'], 'lookup_id', 'lookup_name');
            $instalasi = ArrayHelper::map($body['response']['instalasi'], 'instalasi_singkatan', 'instalasi_nama');
            $validasiUnduh = ArrayHelper::getValue($body['response'], 'validasi_unduh');
            $validasiCutoff = ArrayHelper::getValue($body['response'], 'validasi_cutoff');

            // useless, backend not send diagnosa data
            // print_r($body['response']['diagnosa']);
            // if (!$cache_diagnosa) {
            //     $diagnosa = $body['response']['diagnosa'];
            //     Yii::$app->cache->set("cache_diagnosa_" . $session_id, $diagnosa);
            // }
        } catch (\Exception $e) {
            $ruangan = [];
            $status = [];
            $penjamin = [];
        } catch (RequestException $e) {
            $ruangan = [];
            $status = [];
            $penjamin = [];
        }
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData($checkAll=null)
    {
        try {
            $status_kunjungan = '';
            $badge_class = '';

            $request = Yii::$app->request;

            if (!is_null($checkAll)) {
                $filter['advanced-filter'] = $request->get();
                $response = $this->_restPenjamin->get('inf-pasien-ranap-bpjs/index?' . http_build_query($filter), ['form_params' => []]);
                $body = json_decode($response->getBody(), true);
                $kunjungan_id = [];
                foreach ($body['response']['data'] as $key => $value) {
                    array_push($kunjungan_id, $value['kunjungan_id']);
                }
        
                $return = [
                    'data' => $kunjungan_id,
                ];
                return DocoHelpers::response($return);
            }

            $filter =  DocoDatatableHelper::advancedFilterParam();
            $response = $this->_restPenjamin->get('inf-pasien-ranap-bpjs/index?' . http_build_query($filter), ['form_params' => []]);
            $row = [];
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);

            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['kunjungan_id']);
                $value['primary'] = $primaryKey;

                $value['rowNum'] = $no;
                $nama_pasien = $value['nama_pasien'] ? $value['nama_pasien'] : '-';
                $no_pendaftaran = $value['no_pendaftaran'] ? $value['no_pendaftaran'] : '-';
                $no_rekammedik = $value['no_rekammedik'] ? $value['no_rekammedik'] : '-';
                $no_pembayaran = $value['no_pembayaran'] ? $value['no_pembayaran'] : '-';
                $tgl_pasienpulang = !empty($value['tgl_pulang']) ? date('d-M-Y', strtotime($value['tgl_pulang'])) : '-' ;

                $value['pasien'] = $nama_pasien . ' (' . $value['jenis_kelamin'] . ')' . ' <br> No. Registrasi : ' . $value['no_pendaftaran'] . '<br> No RM : ' . $no_rekammedik . '<br> No. Pembayaran : ' . $no_pembayaran;
                $carabayar = $value['carabayar_nama'] ? $value['carabayar_nama'] : '-';
                $penjamin = $value['penjamin_nama'] ? $value['penjamin_nama'] : '-';
                $value['carabayar_penjamin'] = $carabayar . '<br>' . $penjamin;
                $value['dokter_dpjp'] = $value['dokter_nama'] ? $value['dokter_nama'] : '-';
                $value['state'] = DocoHelpers::encrypt($value['status_kunjungan']);
                $value['pendaftaran_id_encrypted'] = DocoHelpers::encrypt($value['pendaftaran_id']);
                $value['tarif_rs'] = 'Rp. '.DocoHelpers::formatNumber($value['tarif_rs'], 0);
                $value['plafon'] = 'Rp. '.DocoHelpers::formatNumber($value['plafon'], 0);
                // $value['admisi'] = DocoHelpers::encrypt($value['pasienadmisi_id']);

                switch ($value['status_kunjungan']) {
                    case DocoConstants::STATUS_VERIFIKASI_BPJS_BLM:
                        $status_kunjungan = 'Belum Koreksi';
                        break;
                    case DocoConstants::STATUS_VERIFIKASI_BPJS_SDH:
                        $status_kunjungan = 'Sudah Koreksi';
                        break;
                    case DocoConstants::STATUS_VERIFIKASI_BPJS_PRS:
                        $status_kunjungan = 'Proses Klaim';
                        break;
                    case DocoConstants::STATUS_VERIFIKASI_BPJS_FNL:
                        $status_kunjungan = 'Final Klaim';
                        break;
                    default:
                        $status_kunjungan = ' - ';
                        break;
                }
                $value['instalasi_ruangan'] = $value['instalasi_nama'] . ' <br> Ruangan : ' . $value['ruangan_nama'];
                $value['status'] = $status_kunjungan;
                $value['tgl_pendaftaran'] = date('d-M-Y', strtotime($value['tgl_pendaftaran']));
                $value['tglpasienpulang'] = date('d-M-Y', strtotime($value['tgl_pulang']));
                $value['tgl_daftar_keluar'] = 'Tgl Masuk : ' . date('d-M-Y', strtotime($value['tgl_pendaftaran'])) . '<br> Tgl Keluar : ' . $tgl_pasienpulang;
                $value['instalasi'] = '';
                $row[$key] = $value;
            }

            $return = [
                'data' => $row,
                'draw' => $request->get('draw'),
                'recordsTotal' => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount'],
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

    public function actionProses($id, $admisi)
    {
        $request = Yii::$app->request;
        $title = 'Klaim Penjamin';
        $aksi = 'Sudah Koreksi';
        $id_enc = $id;
        $admisi_enc = $admisi;
        $id = DocoHelpers::decrypt($id);
        $admisi = DocoHelpers::decrypt($admisi);
        $state_dec = DocoHelpers::decrypt($_GET['state']);
        $info = [];
        $model = new FormKoreksi;
        if ($request->post()) {
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
                $model = new FormKunjunganPasien();
                $model->koreksi_diagnosa = $request->post('koreksi_diagnosa');
                $model->koreksi_diagnosa_ina = $request->post('koreksi_diagnosa_ina');
                $model->dokter_nama = $request->post('dokter_nama');
                $model->kunjungan_id = $request->post('kunjungan_id');
                $model->data_koreksi = $request->post('data_koreksi');
                $model->total_data = $request->post('total_data');
                $model->is_inacbg = ArrayHelper::getValue($formkoreksi, 'is_inacbg', false);
                $model->is_icdprimer = ArrayHelper::getValue($formkoreksi, 'is_icdprimer', false);
                $model->is_icdprimer_ina = ArrayHelper::getValue($formkoreksi, 'is_icdprimer_ina');
                $model->dokterdpjp_id = $request->post('dokterdpjp_id');
                if ($model->validate()) {
                    $response = $this->_restPenjamin->post('inf-pasien-ranap-bpjs/save', [
                        'form_params' => $model->attributes,
                        'query' => [
                            'id' => $id,
                            'edit_koreksi' => $editKoreksi
                        ],
                    ]);
                    $response = json_decode($response->getBody(), true);
                    $logActivity = $this->setLogActivity($aksi, $id, $response);
                    if ($response['metadata']['status'] == 200) {
                        $result = [
                            'id' => DocoHelpers::encrypt($id),
                            'updated' => DocoHelpers::encrypt(1),
                        ];
                        return DocoHelpers::responseTemplate(200, 'Koreksi Berhasil ditambahkan', $result);
                    } else {
                        if ($response['metadata']['status'] == 400) {
                            return DocoHelpers::responseTemplate(422, $response['response']['message']);
                        } else {
                            return DocoHelpers::responseTemplate(422, 'Koreksi Gagal ditambahkan');
                        }
                    }
                } else {
                    return DocoHelpers::responseTemplate(422, $model->errors);
                }
            } catch (RequestException $e) {
                return DocoHelpers::responseTemplate(422, $e->getMessage());
            }
        }
        $diagnosa = $this->getDetail($id, null);
        try {
            $response = $this->_restPenjamin->get('inf-pasien-ranap-bpjs/view', ['query' => ['id' => $id, 'admisi' => $admisi]]);
            $body = json_decode($response->getBody(), TRUE);
            $info = $body['response'];
        } catch (Exception $e) {
            $info = [];
        }
        if (isset($diagnosa['detail']['Morfologi'])) unset($diagnosa['detail']['Morfologi']);
        if (isset($diagnosa['detail']['Sebab Luar'])) unset($diagnosa['detail']['Sebab Luar']);
        $labelINACBS = DocoConstants::$labelDiagnosaINACBSEklaim;
        return $this->render('proses', get_defined_vars());
    }

    public function actionSimpanKoreksi($id)
    {
        $request = Yii::$app->request;
        $aksi = 'Edit Koreksi';
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
            $model = new FormKunjunganPasien();
            $model->koreksi_diagnosa = $request->post('koreksi_diagnosa');
            $model->dokter_nama = $request->post('dokter_nama');
            $model->kunjungan_id = $request->post('kunjungan_id');
            $model->data_koreksi = $request->post('data_koreksi');
            $model->total_data = $request->post('total_data');
            $model->is_inacbg = ArrayHelper::getValue($formkoreksi, 'is_inacbg', false);
            $model->is_icdprimer = ArrayHelper::getValue($formkoreksi, 'is_icdprimer', false);
            $model->dokterdpjp_id = $request->post('dokterdpjp_id');
            if ($model->validate()) {
                $response = $this->_restPenjamin->post('inf-pasien-ranap-bpjs/save', [
                    'form_params' => $model->attributes,
                    'query' => [
                        'id' => $id,
                        'edit_koreksi' => $editKoreksi
                    ],
                ]);
                $response = json_decode($response->getBody(), true);
                $logActivity = $this->setLogActivity($aksi, $id, $response);
                if ($response['metadata']['status'] == 200) {
                    $result = [
                        'id' => DocoHelpers::encrypt($id),
                        'updated' => DocoHelpers::encrypt(1),
                    ];
                    return DocoHelpers::responseTemplate(200, 'Koreksi Berhasil ditambahkan', $result);
                } else {
                    return DocoHelpers::responseTemplate(422, 'Koreksi Gagal ditambahkan');
                }
            } else {
                return DocoHelpers::responseTemplate(422, $model->errors);
            }
        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(422, $e->getMessage());
        }
    }

    // WIP
    public function actionProsess($id, $admisi)
    {
        $request = Yii::$app->request;
        $title = 'Klaim Ranap';
        $id_dec = DocoHelpers::decrypt($id);
        $admisi_dec = DocoHelpers::decrypt($admisi);
        $info = [];
        $model = new FormKoreksi;
        $diagnosa = $this->getDetail($id_dec, $admisi_dec);
        $session_id = Yii::$app->docoVars->user("id");

        try {
            $response = $this->_restPenjamin->get('inf-pasien-ranap-bpjs/view', ['query' => ['id' => $id_dec]]);
            $body = json_decode($response->getBody(), true);
            $info = $body['response'];
        } catch (\Exception $e) {
            $info = [];
        }

        return $this->render('proses', get_defined_vars());
    }

    public function getDetail($id, $admisi)
    {
        $state = true;
        $data = $detail = $mapping = $hasil_diagnosa = [];
        $final = false;
        try {
            $response = $this->_restPenjamin->get('inf-pasien-ranap-bpjs/detail', [
                'query' => [
                    'id' => $id,
                    'admisi' => $admisi,
                ],
            ]);
            $response = json_decode($response->getBody(), true);
            $response = $response['response'];
            $data = $response['header']['info'];
            $data_detail = $response['detail'];
            $mapping = $response['mapping'];
            $hasil_diagnosa = $response['hasil_diagnosa'];
            $state = ($data['status_kunjungan'] == '' || $data['status_kunjungan'] == '0') ? true : false;
            $detail = [];
            $penyerta = 0;
            $count_detail = count($data_detail);
            $unuGrouper = [];
            $inaGrouper = [];
            foreach ($hasil_diagnosa as $key => $value) {
                foreach ($value as $childValue) {
                    if (isset($childValue['is_inagrouper'])) {
                        if ($childValue['is_inagrouper'] == true) {
                            $inaGrouper[$key][] = $childValue;
                        } else {
                            $unuGrouper[$key][] = $childValue;
                        }
                    } else {
                        $unuGrouper[$key][] = $childValue;
                    }
                }
            }

            $detail = [
                'Utama' => isset($unuGrouper['diagnosa_utama'][0]) ? $unuGrouper['diagnosa_utama'][0] : [[]],
                'Tambahan' => isset($unuGrouper['diagnosa_tambahan']) ? $unuGrouper['diagnosa_tambahan'] : [[]],
                'Tindakan/Operasi' => isset($unuGrouper['diagnosa_opertindakan']) ? $unuGrouper['diagnosa_opertindakan'] : [[]],
            ];

            $detailIna = [
                'Utama' => isset($inaGrouper['diagnosa_utama_ina'][0]) ? $inaGrouper['diagnosa_utama_ina'][0] : [[]],
                'Tambahan' => isset($inaGrouper['diagnosa_tambahan_ina']) ? $inaGrouper['diagnosa_tambahan_ina'] : [[]],
                'Tindakan/Operasi' => isset($inaGrouper['diagnosa_opertindakan_ina']) ? $inaGrouper['diagnosa_opertindakan_ina'] : [[]]
            ];
            $response = [
                'state' => $state,
                'data' => $data,
                'data_detail' => $data_detail,
                'detail' => $detail,
                'detail_ina' => $detailIna,
                'count_detail' => $count_detail,
                'mapping' => $mapping,
                'hasil_diagnosa' => $hasil_diagnosa,
                'penyerta' => $penyerta,
                'fl_detail' => $response['fl_detail'],
                'kunjungan_id' => $id,
                'hidden_inagrouper' => isset($data['instalasi_kode']) && $data['instalasi_kode'] != 'RI' ? true : false
            ];
            return $response;
        } catch (RequestException $e) {
            $result = ['response' => ['title' => "Terjadi Kesalahan", 'text' => 'Data Koreksi Tidak di Temukan']];
            return DocoHelpers::response($result, 500);
        }
    }

    public function actionGetIcd()
    {
        return $this->diagnosaService->getCacheDiagnosa($this->_restPenjamin, 'inf-pasien-ranap-bpjs/get-list-diagnosa');
        /** Replace pengambilan data cache diagnosa untuk meminimalisir attribute cache yang tidak sinkron */
        // $request = Yii::$app->request;
        // $type_icd = $request->get('type_icd');
        // $term = $request->get('term');
        // $not_in = $request->get('not_in');
        // try {
        //     $cache_diagnosa = Yii::$app->cache->get("cache_diagnosa");
        //     if (!$cache_diagnosa) {
        //         $response = $this->_restPenjamin->get('inf-pasien-ranap-bpjs/get-list-diagnosa');
        //         $response = json_decode($response->getBody(), true);
        //         $diagnosa = $response['response']['diagnosa'];
        //         Yii::$app->cache->set("cache_diagnosa", $diagnosa);
        //         $cache_diagnosa = $diagnosa;
        //     }
        //     $term = '/' . strtoupper($term) . '/';
        //     $find_data = array_filter($cache_diagnosa, function ($a) use ($term) {
        //         $a = str_replace(".", "", $a);
        //         $diagnosaNama = strtoupper(ArrayHelper::getValue($a, 'diagnosa_nama'));
        //         $a = [
        //             'diagnosa_id' => ArrayHelper::getValue($a, 'diagnosa_id'),
        //             'diagnosa_kode' => ArrayHelper::getValue($a, 'diagnosa_kode'),
        //             'diagnosa_nama' => $diagnosaNama,
        //             'tabularlist_versi' => ArrayHelper::getValue($a, 'tabularlist_versi'),
        //         ];
        //         $term = str_replace(".", "", $term);
        //         return preg_grep($term, $a);
        //     });
        //     $data = [];
        //     if ($find_data) {
        //         if (!$not_in) {
        //             $not_in = [];
        //         }
        //         foreach ($find_data as $value) {
        //             if (strtolower(trim($value['tabularlist_versi'])) == strtolower(trim($type_icd))) {
        //                 if (!in_array($value['diagnosa_id'], $not_in)) {
        //                     $data[] = [
        //                         'id' => $value['diagnosa_id'],
        //                         'text' => $value['diagnosa_kode'] . ' - ' . $value['diagnosa_nama'],
        //                     ];
        //                 }
        //             }
        //         }
        //     }
        // } catch (RequestException $e) {
        //     $data = [];
        // }

        // return DocoHelpers::response([
        //     'result' => $data,
        // ]);
    }

    public function actionGetDokter()
    {
        if (isset($_GET['q']['term']) && !empty($_GET['q']['term'])) {
            $response = $this->_restPenjamin->request('POST', 'allow/get-dokter', [
                'form_params' => ['nama_pegawai' => $_GET['q']['term']],
            ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = ['id' => $value['pegawai_id'], 'text' => $value['nama_pegawai']];
            }
            $total = count($body['response']);
            $return = ['result' => $data, 'total_count' => $total, 'incomplete_results' => false];
            return DocoHelpers::response($return);
        }
    }

    public function actionGetNewDokter($q = '', $all_text = 0, $id_with_text = 0, $is_perawat = 0,  $page = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $limit = 10;
            $offset = ($page-1)*10;
            $result = [];
            $result['results'] = [];

            $request = $this->_restPenjamin->request('POST', 'allow/get-new-dokter', [
                'query' => compact('q', 'type', 'is_perawat', 'limit', 'offset', 'page')
            ]);
            $response = json_decode($request->getBody(), true);

            $list = $response['response'];
            if ($all_text == 1) {
                foreach ($response['response'] as $value) {
                    $result['results'][] = [
                        'id' => $value['nama_pegawai'],
                        'text' => $value['nama_pegawai'],
                    ];
                }
            } else {
                if ($id_with_text == 1) {
                    foreach ($response['response'] as $value) {
                        $result['results'][] = [
                            'id' => $value['nama_pegawai'],
                            'text' => $value['nama_pegawai'],
                        ];
                    }
                } else {
                    foreach ($response['response'] as $value) {
                        $result['results'][] = [
                            'id' => $value['nama_pegawai'],
                            'text' => $value['nama_pegawai'],
                        ];
                    }
                }
            }

            $result['pagination'] = [ 'more' => !empty($list)?true:false ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    private function getKonfigDokterMultiple(){
        try {
            $request = $this->_restPenjamin->request('POST', 'allow/get-konfig-dokter-multiple', []);
            $response = json_decode($request->getBody(), true);
            $result = ArrayHelper::getValue($response, 'response');
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetKamar()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restPenjamin->get('allow/get-kamar', ['query' => ['ruangan_id' => $parent_label]]);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response'] as $value) {
                $result['output'][] = [
                    'id' => $value['kamarruangan_id'],
                    'name' => $value['kamarruangan_nokamar'],
                ];
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

    public function actionGetKoreksi($pendaftaran_id, $pasienadmisi_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restPenjamin->get('inf-pasien-ranap-bpjs/get-koreksi?id=' . $pendaftaran_id . '&admisi=' . $pasienadmisi_id);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            foreach ($body['response'] as $key => $value) {
                $no++;
                $checked = '';
                $disabled = 'disabled=\"disabled\"';
                $disCheck = '';
                $selected = '';
                if ($value['is_inacbg'] == true) {
                    $checked = 'checked="checked"';
                    $disabled = '';
                }
                if ($value['is_icdprimer'] == true) {
                    $selected = "checked='checked'";
                }
                if ($_GET['state'] == 551) {
                    if ($disabled == '') {
                        $disabled = 'disabled=\"disabled\"';
                    }
                    $disCheck = 'disabled="disabled"';
                }
                $value['rowNum'] = $no;
                $koreksi = '';
                if ($value['kelompokdiagnosa_id'] == DocoConstants::DIAGNOSA_MASUK) {
                    if ($value['diag_asal_masuk'] == "0 - " || empty($value['diag_asal_masuk'])) {
                        $koreksi = '-';
                    } else {
                        $koreksi = $value['diag_asal_masuk'];
                    }
                } else if ($value['kelompokdiagnosa_id'] == DocoConstants::DIAGNOSA_UTAMA) {
                    if ($value['diag_asal_utama'] == "0 - " || empty($value['diag_asal_utama'])) {
                        $koreksi = '-';
                    } else {
                        $koreksi = $value['diag_asal_utama'];
                    }
                } else if ($value['kelompokdiagnosa_id'] == DocoConstants::DIAGNOSA_TERAPI) {
                    if ($value['diag_asal_penyerta'] == "0 - " || empty($value['diag_asal_penyerta'])) {
                        $koreksi = '-';
                    } else {
                        $koreksi = $value['diag_asal_penyerta'];
                    }
                } else if ($value['kelompokdiagnosa_id'] == DocoConstants::DIAGNOSA_TERAPI) {
                    if ($value['diag_asal_terapi'] == "0 - " || empty($value['diag_asal_terapi'])) {
                        $koreksi = '-';
                    } else {
                        $koreksi = $value['diag_asal_terapi'];
                    }
                }
                $value['icd_primary'] = '';
                if ($value['kelompokdiagnosa_id'] != DocoConstants::DIAGNOSA_TERAPI) {
                    $value['icd_primary'] = "<input type='radio' " . $selected . " class='radio-icdprimer' " . $disabled . " name='FormKoreksi[is_icdprimer]' value='" . $value['koreksidiagnosa_id'] . "'>";
                }
                $value['koreksi_diagnosa'] = $koreksi . ' <input name="FormKoreksi[koreksidiagnosa_id][' . ($no - 1) . ']" type="hidden" value="' . $value['koreksidiagnosa_id'] . '">';
                $value['inacbgs'] = "<input type='checkbox' " . $checked . " data-key='" . $value['koreksidiagnosa_id'] . "' class='check-inacbg' name='FormKoreksi[is_inacbg][" . $value['koreksidiagnosa_id'] . "]' " . $disCheck . ">";
                $value['diagnosa_label'] = $value['diagnosa_kode'] . ' - ' . $value['diagnosa_nama'];
                $data[$key] = $value;
            }

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

    public function actionEklaim($id, $admisi)
    {
        $request = Yii::$app->request;
        $title = 'E-Klaim INACBGS';
        $aksi = 'Proses Eklaim';
        $info = [];
        $opsi = [];
        $model = new KlaimInacbgRanapForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id_dec = DocoHelpers::decrypt($id);
        $admisi_dec = DocoHelpers::decrypt($admisi);
        // $update_dec = DocoHelpers::decrypt($_GET['updated']);

        $state = false;
        $isAjukan = false;
        $isChek = false;

        //Detail
        $detailDiagnosa = json_encode([]);

        //Konfig Dokter Multiple
        $isDokterMultiple = $this->getKonfigDokterMultiple();
        
        if ($request->post()) {
            $post = $request->post();
            $model->attributes = $post['KlaimInacbgRanapForm'];
            $model->berat_lahir = isset($post['KlaimInacbgRanapForm']['berat_lahir']) ? preg_replace('/[^A-Za-z0-9\-]/', '', $post['KlaimInacbgRanapForm']['berat_lahir']) : null;
            $model->instalasi_id = DocoConstants::INSTALASI_ID_RI;
            $model->tgl_masuk = date('Y-m-d H:i', strtotime($model->tgl_masuk));
            $model->tgl_keluar = date('Y-m-d H:i', strtotime($model->tgl_keluar));
            $diagnosaTambahan = isset($post['KlaimInacbgDetail']) ? $post['KlaimInacbgDetail'] : [];
            $diagnosaDeleted = isset($post['DiagnosaDeleted']) ? $post['DiagnosaDeleted'] : [];
            $isPrimer = isset($post['primer']) ? $post['primer'] : [];
            
            try {
                $kelasEksekutif = ArrayHelper::getValue($post['KlaimInacbgRanapForm'], 'kelas_eksekutif');
                $model->lama_rawatintensif = ArrayHelper::getValue($post['KlaimInacbgRanapForm'], 'lama_rawatintensif');
                $model->is_rawatintensif = isset($post['KlaimInacbgRanapForm']['is_rawatintensif']) ? $post['KlaimInacbgRanapForm']['is_rawatintensif'] : 0;
                if ($model->is_naikkelas == true) {
                    $model->naik_kelas = 'kelas_' . $post['KlaimInacbgRanapForm']['naik_kelas'];
                } else {
                    $model->naik_kelas = 'kelas_' . $post['KlaimInacbgRanapForm']['jenis_kelasrawat'];
                }

                if ($kelasEksekutif == true) {
                    $model->jenis_kelasrawat = '1';
                }

                $model->rujukanrs = ArrayHelper::getValue($post['KlaimInacbgRanapForm'], 'rujukanrs', 'gp');    
                if ($isDokterMultiple == 'true') {
                    $dokter_additional = isset($post['KlaimInacbgRanapForm']['dokter_additional']) ? $post['KlaimInacbgRanapForm']['dokter_additional'] : [];
                    $resultAdditional = implode('/', $dokter_additional);
                    $model->dokter_additional = $dokter_additional;
                    $model->nama_dokter = $resultAdditional;
                }
                if ($model->validate()) {
                    if (count($diagnosaTambahan)) {
                        $diagnosaDetail = [];
                        $procedureDetail = [];
                        foreach ($diagnosaTambahan as $key => $value) {
                            $diagnosaTambahan[$key]['icd_versi'] = $value['diagnosa_type'];
                            if ($value['diagnosa_type'] == 9) {
                                $procedureDetail[] = $value['kode_diagnosa'];
                            } else {
                                $diagnosaDetail[] = $value['kode_diagnosa'];
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

                        $arrayPenjamin = [
                            DocoConstants::PAYOR_ID_COVID,
                            DocoConstants::PAYOR_ID_BAYI_BARU_LAHIR,
                            DocoConstants::PAYOR_ID_COINSIDENSE,
                            DocoConstants::PAYOR_ID_KIPI,
                            DocoConstants::PAYOR_ID_PERPANJANGAN_MASA_RAWAT,
                            DocoConstants::PAYOR_ID_JAMPERSAL
                        ];
                        $dataGrouper = isset($post['Grouper']) ? $post['Grouper'] : [];
                        if (in_array($model->klaim_penjamin, $arrayPenjamin)) {
                            $response = $this->_restPenjamin->post('inf-pasien-ranap-bpjs-covid/proses-eklaim', ['form_params' => ['data' => $model->attributes, 'detail' => $diagnosaTambahan, 'grouper' => $dataGrouper, 'deleted' => $diagnosaDeleted, 'primer' => $isPrimer]]);
                            $body = json_decode($response->getBody(), true);
                            $logActivity = $this->setLogActivity($aksi, $id_dec, $body);
                            if ($body['metadata']['status'] != 200) {
                                $result = ['response' => ['text' =>  isset($body['response']) ? $body['response']['text'] : $body, 'title' => isset($body['response']) ? $body['response']['title'] : $body]];
                                return DocoHelpers::response($result, 422);
                            }
                            return DocoHelpers::response($body['response']);
                        } else {
                            $response = $this->_restPenjamin->post('inf-pasien-ranap-bpjs/proses-eklaim', ['form_params' => ['data' => $model->attributes, 'detail' => $diagnosaTambahan, 'grouper' => $dataGrouper, 'deleted' => $diagnosaDeleted, 'primer' => $isPrimer]]);
                            $body = json_decode($response->getBody(), true);
                            $logActivity = $this->setLogActivity($aksi, $id_dec, $body);
                            if ($body['metadata']['status'] != 200) {
                                $result = ['response' => ['title' => isset($body['response']) ? $body['response'] : $body]];
                                return DocoHelpers::response($result, $body['metadata']['status']);
                            }
                            return DocoHelpers::response($body['response']);
                        }
                    } catch (RequestException $e) {
                        return DocoHelpers::response($e->getMessage());
                    } catch (\Exception $e) {
                        return DocoHelpers::response($e->getMessage());
                    }
                    return true;
                } else {
                    $response = $model->errors;
                    return DocoHelpers::response($response, 422, $formName);
                }
                return $response;
            } catch (\Exception $e) {
                var_dump($e->getMessage());
                die();
            } catch (RequestException $e) {
                var_dump($e->getMessage());
                die();
            }
        }

        try {
            $response = $this->_restPenjamin->get('inf-pasien-ranap-bpjs/get-data', ['query' => ['id' => $id_dec, 'admisi' => $admisi_dec]]);
            $body = json_decode($response->getBody(), true);
            $info = $body['response']['info'];
            $opsi = $body['response']['opsi'];
            $checkLastKlaim = $body['response']['klaim'];
            $klaimgroup = $body['response']['klaimgroup'];
            $detail = $body['response']['detailtarif'];
            $diagnosa = $body['response']['diagnosa'];
            $inacbgData = $body['response']['inacbg'];
            $historyKlaim = ArrayHelper::getValue($body,'response.historyKlaim');
            
            $unuGrouper = [];
            $inaGrouper = [];

            $is_prosesklaim = !empty($checkLastKlaim) ? true : false;

            foreach ($diagnosa as $key => $value) {
                if ($value['is_inagrouper'] == true) {
                    $inaGrouper[] = $value;
                } else {
                    $unuGrouper[] = $value;
                }
            }

            $tmpTarif = 0;
            foreach ($detail as $k => $v) {
                $tmpTarif += $v;
            }
            $tarifRanap = isset($tmpTarif) ? (float)$tmpTarif : 0;
            $tambahanBiaya = isset($klaimgroup['tambahan_biaya']) ? $klaimgroup['tambahan_biaya'] : 0;
            $persen_tambahan = $klaimgroup ? $klaimgroup['persen_tambahan'] : 0;
            $naikKelas = $this->getValueNaikKelas($checkLastKlaim);
            $instalasiNama = DocoConstants::TITLE_RI;
            $instalasiRajal = DocoConstants::TITLE_RJ;
            $instalasiIgd = DocoConstants::TITLE_RD;
            $jenisRawat = $instalasiNama . ' ' . $info['kelas_nama'] . ' (' . $info['lama_rawat'] . ' Hari)';
            $defaultJenisTarif = $opsi['tarifrs'];
            $listJenisTarif = DocoConstants::LIST_TARIF_INACBG;
            $kodeJenisTarif = '';
            $namaJenisTarif = '';
            foreach ($defaultJenisTarif as $value) {
                $kodeJenisTarif = $value['lookup_value'];
            }
            foreach ($listJenisTarif as $key => $val) {
                if ($key == $kodeJenisTarif) {
                    $namaJenisTarif = $val;
                }
            }

            if ($isDokterMultiple == 'true') {
                $dokterKlaim = !empty($checkLastKlaim['nama_dokter']) ? $checkLastKlaim['nama_dokter'] : $info['dokter_nama'];
                $stringDokterKlaim = explode('/', $dokterKlaim);
                $resultDokterKlaim = [];

                foreach($stringDokterKlaim as $key => $name){
                    $resultDokterKlaim[] = [
                        'text' => $name
                    ];
                }

                $additionalData = [];
                $dokterAdditional = [];
                if (isset($info['additional_data'])) {
                    $additionalData = json_decode($info['additional_data'], true);
                    if (isset($additionalData['dokter_additional'])) {
                        $dokterAdditional = $additionalData['dokter_additional'];
                    }
                }

                $dokterDpjp = [];
                $valDpjp = $dokterDpjp = [];
                $arrDokter = !empty($resultDokterKlaim) ? $resultDokterKlaim : $dokterAdditional;
                foreach ($arrDokter as $value) {
                    if (isset($value['id']) && isset($value['text'])) {
                        $valDpjp[] = $value['text'];
                        $dokterDpjp[] = [$value['text'] => $value['text']];
                    } else if (isset($value['text'])) {
                        $valDpjp[] = $value['text'];
                        $dokterDpjp[] = [$value['text'] => $value['text']];
                    }
                }
                $model->dokter_additional = $valDpjp;
            } else {
                $dokterDpjp[] = !empty($checkLastKlaim['nama_dokter']) ? $checkLastKlaim['nama_dokter'] : $info['dokter_nama'];
            }

            
            $disabledProses = ($inacbgData['header']['status_klaim'] == true) ? "disabled=disabled" : "";
            $jenistarif = [
                $kodeJenisTarif => $namaJenisTarif
            ];
            $infoTxt = "INACBG @ " . date('d M Y H:i') . ' - ' . DocoConstants::DEFAULT_KELAS_INACBG . ' - TARIF : ';
            $infoInaGrouperTxt = "INACBG @ " . date('d M Y H:i') . ' - ';
            $expUmur = explode(" Tahun", $info['umur']);
            $update_dec = DocoHelpers::decrypt($info['status_kunjungan']);
            $status_kunjungan = $info['status_kunjungan'];
            $expUmur = $expUmur[0] . ' Tahun';
            $info['los'] = $info['lama_rawat'];
            $detailDiagnosa = isset($inacbgData['detailinacbg']) ? json_encode($inacbgData['detailinacbg']) : json_encode([]);
            $inacbgsDiagnosa = [];
            $inacbgsProcedure = [];
            $inacbgsDiagnosaIna = [];
            $inacbgsProcedureIna = [];
            $IdrgDiagnosa = [];
            $IdrgProcedure = [];

            if ($info['kelas_kode'] == 1) {
                $dataKelasRawat = ['3' => 'Reguler', '1' => 'Eksekutif'];
            } else {
                $dataKelasRawat = ['1' => 'Kelas 1', '2' => 'Kelas 2', '3' => 'Kelas 3'];
            }
            foreach ($diagnosa as $key => $value) {
                // if ($value['kelompokdiagnosa_id'] == DocoConstants::MAP_DIAGNOSA_OPERTINDAKAN || $value['is_icdprimer'] == true) {
                //     if (!in_array($value['diagnosa_kode'], $inacbgsProcedure) || !in_array($value['diagnosa_kode'], $inacbgsProcedureIna)) {
                //         if ($value['is_icdprimer']) {
                //             if ($value['is_inagrouper']) {
                //                 array_unshift($inacbgsProcedureIna, $value['diagnosa_kode']);
                //             } else {
                //                 array_unshift($inacbgsProcedure, $value['diagnosa_kode']);
                //             }
                //         } else {
                //             if ($value['is_inagrouper']) {
                //                 $inacbgsProcedureIna[] = $value['diagnosa_kode'];
                //             } else {
                //                 $inacbgsProcedure[] = $value['diagnosa_kode'];
                //             }
                //         }
                //     }
                // } else {
                //     if (!in_array($value['diagnosa_kode'], $inacbgsDiagnosa) || !in_array($value['diagnosa_kode'], $inacbgsDiagnosaIna)) {
                //         if ($value['is_inagrouper']) {
                //             $inacbgsDiagnosaIna[] = $value['diagnosa_kode'];
                //         } else {
                //             $inacbgsDiagnosa[] = $value['diagnosa_kode'];
                //         }
                //     }
                // }

                /**
                 * Set Idrg Diagnosa
                 */
                if($value['kelompokdiagnosa_id'] == DocoConstants::VAR_KELOMPOK_DIAGNOSA_TERAPI) {
                    if ($value['is_idrg'] && !empty($value['is_idrg'])) {
                        $IdrgProcedure[] = [
                            'diagnosa_id' => $value['diagnosa_id'],
                            'diagnosa_kode' => $value['diagnosa_kode'],
                            'diagnosa_nama' => $value['diagnosa_nama'],
                            'is_icdprimer' => $value['is_icdprimer'],
                            'multiplicity' => isset($value['multiplicity']) ? $value['multiplicity'] : 1,
                            'is_idrg' => $value['is_idrg']
                        ];
                    } else {
                        $inacbgsProcedure[] = [
                            'diagnosa_id' => $value['diagnosa_id'],
                            'diagnosa_kode' => $value['diagnosa_kode'],
                            'diagnosa_nama' => $value['diagnosa_nama'],
                            'is_icdprimer' => $value['is_icdprimer'],
                            'multiplicity' => isset($value['multiplicity']) ? $value['multiplicity'] : 1,
                            'is_inacbg' => $value['is_inacbg'],
                        ];
                    }
                } else {
                    if ($value['is_idrg'] && !empty($value['is_idrg'])) {
                        $IdrgDiagnosa[] = [
                            'diagnosa_id' => $value['diagnosa_id'],
                            'diagnosa_kode' => $value['diagnosa_kode'],
                            'diagnosa_nama' => $value['diagnosa_nama'],
                            'is_icdprimer' => $value['is_icdprimer'],
                            'is_idrg' => $value['is_idrg'],
                            'accpdx' => isset($value['accpdx']) ? $value['accpdx'] : null,
                            'asterik' => isset($value['asterik']) ? $value['asterik'] : null
                        ];
                    } else {
                        $inacbgsDiagnosa[] = [
                            'diagnosa_id' => $value['diagnosa_id'],
                            'diagnosa_kode' => $value['diagnosa_kode'],
                            'diagnosa_nama' => $value['diagnosa_nama'],
                            'is_icdprimer' => $value['is_icdprimer'],
                            'is_inacbg' => $value['is_inacbg'],
                            'accpdx' => isset($value['accpdx']) ? $value['accpdx'] : null,
                            'asterik' => isset($value['asterik']) ? $value['asterik'] : null
                        ];
                    }
                }
            }

            $nosep = ArrayHelper::getValue($info, 'nosep');
            $noKartu = ArrayHelper::getValue($info, 'no_asuransi');
            $model->attributes = $info;
            $model->dokterdpjp_id = $info['dokter_kode'];
            $model->nama_pasien = !empty($checkLastKlaim['nama_pasien']) ? $checkLastKlaim['nama_pasien'] : $info['nama_pasien'];
            $model->nama_dokter = !empty($checkLastKlaim['nama_dokter']) ? $checkLastKlaim['nama_dokter'] : $info['dokter_nama'];
            $model->tgl_masuk = !empty($checkLastKlaim['tgl_masuk']) ? $checkLastKlaim['tgl_masuk'] : $info['tgl_pendaftaran'];
            $model->tgl_keluar = !empty($checkLastKlaim['tgl_keluar']) ? $checkLastKlaim['tgl_keluar'] : $info['tgl_pulang'];
            $model->intubasi = !empty($checkLastKlaim['ventilator_start']) ? $checkLastKlaim['ventilator_start'] : date('Y-m-d H:i:s');
            $model->ekstubasi = !empty($checkLastKlaim['ventilator_stop']) ? $checkLastKlaim['ventilator_stop'] : date('Y-m-d H:i:s');
            $model->no_rekam_medik = !empty($checkLastKlaim['no_rekammedik']) ? $checkLastKlaim['no_rekammedik'] : $info['no_rekammedik'];
            $model->no_sep = !empty($checkLastKlaim['no_sep']) ? $checkLastKlaim['no_sep'] : $info['nosep'];
            $model->total_tarifrs =  !empty($checkLastKlaim['total_tarifrs']) ? $checkLastKlaim['total_tarifrs'] : 
                (!empty($historyKlaim['total_tarifrs']) ? $historyKlaim['total_tarifrs'] : 
                    $tarifRanap);
            $model->carapulang_id =  !empty($checkLastKlaim['carapulang_id']) ? $checkLastKlaim['carapulang_id'] : $info['carakeluar_kode'];
            $model->klaim_penjamin =  !empty($checkLastKlaim['klaim_penjamin']) ? $checkLastKlaim['klaim_penjamin'] : null;

            $model->lama_rawatintensif = isset($info['lama_kelasintensif']) ? $info['lama_kelasintensif'] : 0;
            $model->pasienadmisi_id = isset($info['pasienadmisi_id']) ? $info['pasienadmisi_id'] : null;

            $model->is_naikkelas = !empty($checkLastKlaim['is_naikkelas']) ? $checkLastKlaim['is_naikkelas'] : false;
            if (isset($info['kelas_bpjs'])) {
                $isChek = $this->cekKelas($info['urutankelas'], $info['kelas_bpjs']);
                $model->is_naikkelas = $this->cekKelas($info['urutankelas'], $info['kelas_bpjs']);
            }
            $model->ventilator = isset($info['ventilator']) ? $info['ventilator'] : 0;
            $model->naik_kelas = $naikKelas;
            $model->is_rawatintensif = isset($info['is_rawatintensif']) ? $info['is_rawatintensif'] : false;
            $model->instalasi_id = $info['kelas_kode'];
            $model->pasien_id = $info['pasien_id'];
            $model->jenis_kelasrawat = !empty($checkLastKlaim['kelas_id']) ? $checkLastKlaim['kelas_id'] : $info['kelas_nama'];
            $model->tarif = !empty($checkLastKlaim['tarif']) ? $checkLastKlaim['tarif'] : DocoConstants::DEFAULT_KODE_INACBG;
            $model->no_kartu = isset($info['nokartuasuransi']) ? $info['nokartuasuransi'] : $noKartu;
            // $model->diagnosa_primer = implode('#', $inacbgsProcedure);
            // $model->diagnosa_sekunder = implode('#', $inacbgsDiagnosa);
            // $model->diagnosa_primer_ina = implode('#', $inacbgsProcedureIna);
            // $model->diagnosa_sekunder_ina = implode('#', $inacbgsDiagnosaIna);
            $model->tgl_lahir = $info['tgl_lahir'];
            $model->pasien_tb = !empty($checkLastKlaim['is_pasientb']) ? $checkLastKlaim['is_pasientb'] : false;
            $number_pasientb = !empty($checkLastKlaim['number_pasientb']) ? $checkLastKlaim['number_pasientb'] : null;
            $model->dializer = !empty($checkLastKlaim['dializer']) ? $checkLastKlaim['dializer'] : 0;
            $model->transfusi_darah = !empty($checkLastKlaim['transfusi_darah']) ? $checkLastKlaim['transfusi_darah'] : 0;
            /* start group tarif */
            
            $model->prosedur_nonbedah = !empty($checkLastKlaim['prosedur_nonbedah']) ? DocoHelpers::formatNumber($checkLastKlaim['prosedur_nonbedah']) : 
                (!empty($historyKlaim['prosedur_nonbedah']) ? DocoHelpers::formatNumber($historyKlaim['prosedur_nonbedah']) : 
                    (isset($detail['prosedur_non_bedah']) ? DocoHelpers::formatNumber($detail['prosedur_non_bedah']) : 0));
            $model->prosedur_bedah = $this->getSuggestTarif($checkLastKlaim,$historyKlaim,$detail,'prosedur_bedah');
            $model->konsultasi = $this->getSuggestTarif($checkLastKlaim,$historyKlaim,$detail,'konsultasi');
            $model->tenaga_ahli = $this->getSuggestTarif($checkLastKlaim,$historyKlaim,$detail,'tenaga_ahli');
            $model->keperawatan = $this->getSuggestTarif($checkLastKlaim,$historyKlaim,$detail,'keperawatan');
            $model->penunjang = $this->getSuggestTarif($checkLastKlaim,$historyKlaim,$detail,'penunjang');
            $model->radiologi = $this->getSuggestTarif($checkLastKlaim,$historyKlaim,$detail,'radiologi');
            $model->laboratorium = $this->getSuggestTarif($checkLastKlaim,$historyKlaim,$detail,'laboratorium');
            $model->pelayanan_darah = $this->getSuggestTarif($checkLastKlaim,$historyKlaim,$detail,'pelayanan_darah');
            $model->rehabilitasi = $this->getSuggestTarif($checkLastKlaim,$historyKlaim,$detail,'rehabilitasi');
            $model->kamar_akomodasi = $this->getSuggestTarif($checkLastKlaim,$historyKlaim,$detail,'kamar_akomodasi');
            $model->rawat_intensif = $this->getSuggestTarif($checkLastKlaim,$historyKlaim,$detail,'rawat_intensif');
            $model->obat = $this->getSuggestTarif($checkLastKlaim,$historyKlaim,$detail,'obat');
            $model->alkes = $this->getSuggestTarif($checkLastKlaim,$historyKlaim,$detail,'alkes');
            $model->bmhp = $this->getSuggestTarif($checkLastKlaim,$historyKlaim,$detail,'bmhp');
            $model->sewa_alat = $this->getSuggestTarif($checkLastKlaim,$historyKlaim,$detail,'sewa_alat');
            $model->obat_kemoterapi = $this->getSuggestTarif($checkLastKlaim,$historyKlaim,$detail,'obat_kemoterapi');
            $model->obat_kronis = $this->getSuggestTarif($checkLastKlaim,$historyKlaim,$detail,'obat_kronis');
            /*
            if (isset($detail['prosedur_bedah'])) {
                $model->prosedur_bedah = !empty($checkLastKlaim['prosedur_bedah']) ? DocoHelpers::formatNumber($checkLastKlaim['prosedur_bedah']) : DocoHelpers::formatNumber($detail['prosedur_bedah']);
            } else {
                $model->prosedur_bedah = !empty($checkLastKlaim['prosedur_bedah']) ? DocoHelpers::formatNumber($checkLastKlaim['prosedur_bedah']) : 0;
            }
            if (isset($detail['prosedur_non_bedah'])) {
                $model->prosedur_nonbedah = !empty($checkLastKlaim['prosedur_nonbedah']) ? DocoHelpers::formatNumber($checkLastKlaim['prosedur_nonbedah']) : DocoHelpers::formatNumber($detail['prosedur_non_bedah']);
            } else {
                $model->prosedur_nonbedah = !empty($checkLastKlaim['prosedur_nonbedah']) ? DocoHelpers::formatNumber($checkLastKlaim['prosedur_nonbedah']) : 0;
            }
            if (isset($detail['konsultasi'])) {
                $model->konsultasi = !empty($checkLastKlaim['konsultasi']) ? DocoHelpers::formatNumber($checkLastKlaim['konsultasi']) :  DocoHelpers::formatNumber($detail['konsultasi']);
            } else {
                $model->konsultasi = !empty($checkLastKlaim['konsultasi']) ? DocoHelpers::formatNumber($checkLastKlaim['konsultasi']) : 0;
            }
            if (isset($detail['tenaga_ahli'])) {
                $model->tenaga_ahli = !empty($checkLastKlaim['tenaga_ahli']) ? DocoHelpers::formatNumber($checkLastKlaim['tenaga_ahli']) : DocoHelpers::formatNumber($detail['tenaga_ahli']);
            } else {
                $model->tenaga_ahli = !empty($checkLastKlaim['tenaga_ahli']) ? DocoHelpers::formatNumber($checkLastKlaim['tenaga_ahli']) : 0;
            }
            if (isset($detail['keperawatan'])) {
                $model->keperawatan =  !empty($checkLastKlaim['keperawatan']) ? DocoHelpers::formatNumber($checkLastKlaim['keperawatan']) : DocoHelpers::formatNumber($detail['keperawatan']);
            } else {
                $model->keperawatan = !empty($checkLastKlaim['keperawatan']) ? DocoHelpers::formatNumber($checkLastKlaim['keperawatan']) : 0;
            }
            if (isset($detail['penunjang'])) {
                $model->penunjang = !empty($checkLastKlaim['penunjang']) ? DocoHelpers::formatNumber($checkLastKlaim['penunjang']) : DocoHelpers::formatNumber($detail['penunjang']);
            } else {
                $model->penunjang =  !empty($checkLastKlaim['penunjang']) ? DocoHelpers::formatNumber($checkLastKlaim['penunjang']) : 0;
            }
            if (isset($detail['radiologi'])) {
                $model->radiologi = !empty($checkLastKlaim['radiologi']) ? DocoHelpers::formatNumber($checkLastKlaim['radiologi']) : DocoHelpers::formatNumber($detail['radiologi']);
            } else {
                $model->radiologi = !empty($checkLastKlaim['radiologi']) ? DocoHelpers::formatNumber($checkLastKlaim['radiologi']) : 0;
            }
            if (isset($detail['laboratorium'])) {
                $model->laboratorium =  !empty($checkLastKlaim['laboratorium']) ? DocoHelpers::formatNumber($checkLastKlaim['laboratorium']) :  DocoHelpers::formatNumber($detail['laboratorium']);
            } else {
                $model->laboratorium =  !empty($checkLastKlaim['laboratorium']) ? DocoHelpers::formatNumber($checkLastKlaim['laboratorium']) : 0;
            }
            if (isset($detail['pelayanan_darah'])) {
                $model->pelayanan_darah = !empty($checkLastKlaim['pelayanan_darah']) ? DocoHelpers::formatNumber($checkLastKlaim['pelayanan_darah']) :   DocoHelpers::formatNumber($detail['pelayanan_darah']);
            } else {
                $model->pelayanan_darah = !empty($checkLastKlaim['pelayanan_darah']) ? DocoHelpers::formatNumber($checkLastKlaim['pelayanan_darah']) : 0;
            }
            if (isset($detail['rehabilitasi'])) {
                $model->rehabilitasi = !empty($checkLastKlaim['rehabilitasi']) ? DocoHelpers::formatNumber($checkLastKlaim['rehabilitasi']) : DocoHelpers::formatNumber($detail['rehabilitasi']);
            } else {
                $model->rehabilitasi = !empty($checkLastKlaim['rehabilitasi']) ? DocoHelpers::formatNumber($checkLastKlaim['rehabilitasi']) : 0;
            }
            if (isset($detail['kamar_akomodasi'])) {
                $model->kamar_akomodasi =  !empty($checkLastKlaim['kamar_akomodasi']) ? DocoHelpers::formatNumber($checkLastKlaim['kamar_akomodasi']) :  DocoHelpers::formatNumber($detail['kamar_akomodasi']);
            } else {
                $model->kamar_akomodasi =  !empty($checkLastKlaim['kamar_akomodasi']) ? DocoHelpers::formatNumber($checkLastKlaim['kamar_akomodasi']) : 0;
            }
            if (isset($detail['rawat_intensif'])) {
                $model->rawat_intensif =  !empty($checkLastKlaim['rawat_intensif']) ? DocoHelpers::formatNumber($checkLastKlaim['rawat_intensif']) : DocoHelpers::formatNumber($detail['rawat_intensif']);
            } else {
                $model->rawat_intensif =  !empty($checkLastKlaim['rawat_intensif']) ? DocoHelpers::formatNumber($checkLastKlaim['rawat_intensif']) : 0;
            }
            if (isset($detail['obat'])) {
                $model->obat = !empty($checkLastKlaim['obat']) ? DocoHelpers::formatNumber($checkLastKlaim['obat']) : DocoHelpers::formatNumber($detail['obat']);
            } else {
                $model->obat = !empty($checkLastKlaim['obat']) ? DocoHelpers::formatNumber($checkLastKlaim['obat']) : 0;
            }
            if (isset($detail['alkes'])) {
                $model->alkes =  !empty($checkLastKlaim['alkes']) ? DocoHelpers::formatNumber($checkLastKlaim['alkes']) : DocoHelpers::formatNumber($detail['alkes']);
            } else {
                $model->alkes =  !empty($checkLastKlaim['alkes']) ? DocoHelpers::formatNumber($checkLastKlaim['alkes']) : 0;
            }
            if (isset($detail['bmhp'])) {
                $model->bmhp =  !empty($checkLastKlaim['bmhp']) ? DocoHelpers::formatNumber($checkLastKlaim['bmhp']) : DocoHelpers::formatNumber($detail['bmhp']);
            } else {
                $model->bmhp =  !empty($checkLastKlaim['bmhp']) ? DocoHelpers::formatNumber($checkLastKlaim['bmhp']) : 0;
            }
            if (isset($detail['sewa_alat'])) {
                $model->sewa_alat = !empty($checkLastKlaim['sewa_alat']) ? DocoHelpers::formatNumber($checkLastKlaim['sewa_alat']) : DocoHelpers::formatNumber($detail['sewa_alat']);
            } else {
                $model->sewa_alat = !empty($checkLastKlaim['sewa_alat']) ? DocoHelpers::formatNumber($checkLastKlaim['sewa_alat']) : 0;
            }
            if (isset($detail['obat_kemoterapi'])) {
                $model->obat_kemoterapi =  !empty($checkLastKlaim['obat_kemoterapi']) ? DocoHelpers::formatNumber($checkLastKlaim['obat_kemoterapi']) : DocoHelpers::formatNumber($detail['obat_kemoterapi']);
            } else {
                $model->obat_kemoterapi =  !empty($checkLastKlaim['obat_kemoterapi']) ? DocoHelpers::formatNumber($checkLastKlaim['obat_kemoterapi']) : 0;
            }
            if (isset($detail['obat_kronis'])) {
                $model->obat_kronis =  !empty($checkLastKlaim['obat_kronis']) ? DocoHelpers::formatNumber($checkLastKlaim['obat_kronis']) : DocoHelpers::formatNumber($detail['obat_kronis']);
            } else {
                $model->obat_kronis =  !empty($checkLastKlaim['obat_kronis']) ? DocoHelpers::formatNumber($checkLastKlaim['obat_kronis']) : 0;
            }
            */

            $totalTagihanRs = DocoHelpers::formatNumber($this->getTotalTagihanRs($detail, $checkLastKlaim));
            $payorCovid = DocoConstants::PAYOR_ID_COVID;
            $payorJkn = DocoConstants::PAYOR_ID_JKN;
            $payorKipi = DocoConstants::PAYOR_ID_KIPI;
            $payorBayi = DocoConstants::PAYOR_ID_BAYI_BARU_LAHIR;
            $payorPerpanjanganRawat = DocoConstants::PAYOR_ID_PERPANJANGAN_MASA_RAWAT;
            $payorCoinsidense = DocoConstants::PAYOR_ID_COINSIDENSE;
            $payorJampersal = DocoConstants::PAYOR_ID_JAMPERSAL;
            $hakKelas = (isset($info['kelas_bpjs'])) ? $info['kelas_bpjs'] : '';
            $peserta_hakkelas = (isset($info['peserta_hakkelas'])) ? $info['peserta_hakkelas'] : '';
            if (!empty($checkLastKlaim)) {
                $hakKelas = $checkLastKlaim['kelas_id'];
            }

            /* end group tarif*/
            $model->los = $info['lama_rawat'];
            if ($info['lama_rawat'] >= 42) {
                $info['adl_subacute'] = "12";
            }
            if ($info['lama_rawat'] >= 103) {
                $info['adl_cronic'] = "12";
            }

            $model->adl_subacute = !empty($checkLastKlaim['adl_subacute']) ? $checkLastKlaim['adl_subacute'] : 0;
            $model->adl_cronic = !empty($checkLastKlaim['adl_cronic']) ? $checkLastKlaim['adl_cronic'] : 0;

            $model->sistole = !empty($checkLastKlaim['sistole']) ? $checkLastKlaim['sistole'] : $info['sistole'];
            $model->diastole = !empty($checkLastKlaim['diastole']) ? $checkLastKlaim['diastole'] : $info['diastole'];

            $model->lama_rawatkelas = ($model->is_naikkelas) ? $checkLastKlaim['lama_naikkelas'] : 0;
            if ($info['status_kunjungan'] == 3) {
                $state = true;
            }
            $isAjukan = (isset($info['pengajuanklaimdetail_id']) && !empty($info['pengajuanklaimdetail_id'])) ? true : false;
            $isChek = !empty($checkLastKlaim['is_naikkelas']) ? $checkLastKlaim['is_naikkelas'] : false;
            $isVentilator = !empty($checkLastKlaim['ventilator']) ? $checkLastKlaim['ventilator'] : 0;
            $jenisKelasRawatAwal = $model->jenis_kelasrawat;

            // Suggest untuk kondisi awal
            $jenisPerawatan = null;
            $caramasuk = ArrayHelper::getValue($info, 'caramasuk');
            if (!empty($caramasuk)) {
                $model->rujukanrs = $caramasuk;
            }

            if ($info['instalasi_kode'] == DocoConstants::SINGKATAN_RI) {
                $jenisPerawatan = 1;
                if (empty($caramasuk)) {
                    $model->rujukanrs = DocoConstants::CARAMASUK_RI;
                }
            }
            if ($info['instalasi_kode'] == DocoConstants::SINGKATAN_RJ) {
                $jenisPerawatan = 2;
                if (empty($caramasuk)) {
                    $model->rujukanrs = DocoConstants::CARAMASUK_RJ;
                }
            }
            if ($info['instalasi_kode'] == DocoConstants::SINGKATAN_RD_NEW || $info['instalasi_kode'] == DocoConstants::SINGKATAN_RD) {
                $jenisPerawatan = 2;
                if (empty($caramasuk)) {
                    $model->rujukanrs = DocoConstants::CARAMASUK_RD;
                }
            }

            $model->berat_lahir = isset($checkLastKlaim['berat_lahir']) ? DocoHelpers::formatNumber($checkLastKlaim['berat_lahir']) : 0;
            $idEnc = DocoHelpers::encrypt($info['kunjungan_id']);
            $stateEnc = DocoHelpers::encrypt($info['status_kunjungan']);
            
            $spesialProcedure = isset($checkLastKlaim['sp_procedure_kode']) ? $checkLastKlaim['sp_procedure_kode'] : null;
            $spesialProsthesis = isset($checkLastKlaim['sp_prosthesis_kode']) ? $checkLastKlaim['sp_prosthesis_kode'] : null;
            $spesialInvestigate = isset($checkLastKlaim['sp_investigation_kode']) ? $checkLastKlaim['sp_investigation_kode'] : null;
            $spesialDrug = isset($checkLastKlaim['sp_drug_kode']) ? $checkLastKlaim['sp_drug_kode'] : null;
            $statusInacbg = isset($info['status_inacbg']) ? $info['status_inacbg'] : null;
            
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            die();
            $info = [];
            $diagnosa = [];
        } catch (RequestException $e) {
            var_dump($e->getMessage());
            die();
            $info = [];
            $diagnosa = [];
        }
        return $this->render('eklaim-new-idrg', get_defined_vars());
    }

    private function getSuggestTarif($klaimAktif,$historyKlaim,$grouptarif,$key)
    {
        return !empty($klaimAktif[$key]) ? DocoHelpers::formatNumber($klaimAktif[$key]) : 
            (!empty($historyKlaim[$key]) ? DocoHelpers::formatNumber($historyKlaim[$key]) : 
                (isset($grouptarif[$key]) ? DocoHelpers::formatNumber($grouptarif[$key]) : 0));
    }

    public function actionResetGrouping($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($this->_restPenjamin, [
            'url' => "inf-pasien-ranap-bpjs/reset-grouping",
            'payload' => [
                'query' => [
                    'id' => $id
                ]
            ],
        ]);
    }

    public function actionGetKlaim()
    {
        $request = Yii::$app->request;
        $nosep = $request->post('no_sep');
        $kunjunganId = $request->post('kunjungan_id');

        $response = $this->guzzleExec($this->_restPenjamin, [
            'url' => "inf-pasien-ranap-bpjs/get-claim-data",
            'payload' => [
                'query' => [
                    'nosep' => $nosep,
                    'kunjungan_id' => !is_numeric($kunjunganId) ? DocoHelpers::decrypt($kunjunganId) : $kunjunganId
                ]
            ],
        ]);

        $data = isset($response['data']) ? $response['data'] : [];
        return json_encode($data);
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
                'stage' => 2,
                'grouper' => 'inacbg',
            ],
            'data' => [
                'nomor_sep' => $nosep,
                'special_cmg' => $code
            ],
        ];

        if ($stage == 2) {
            $spesialCmg = [];
            foreach ($post['kodeDiagnosa'] as $value) {
                if(! empty($value) && $value !== "") {
                    $spesialCmg[] = $value;
                }
            }

            $getklaim['metadata']['stage'] = $stage;
            $getklaim['data']['special_cmg'] = implode('#', $spesialCmg);
        }

        $klaim = DocoHelpers::restInacbgs($getklaim);
        $body = json_decode($klaim, true);
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
    public function actionHapusKlaim()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $nosep = $post['nosep'];
        $aksi = 'Hapus Eklaim';
        $id = DocoHelpers::decrypt($post['kunjungan_id']);
        try {
            $response = $this->_restPenjamin->post('inf-pasien-ranap-bpjs/hapus-klaim', [
                'form_params' =>
                [
                    'pendaftaran_id' => $post['pendaftaranid'],
                    'no_sep' => $nosep,
                    'admisi' => isset($post['admisi']) ? $post['admisi'] : null,
                ],
            ]);
            $body = json_decode($response->getBody(), true);
            $logActivity = $this->setLogActivity($aksi, $id, $body);
            if ($body['metadata']['status'] != 200) {
                $result = ['response' => ['title' => isset($body['response']) ? $body['response'] : $body]];
                return DocoHelpers::response($result, $body['metadata']['status']);
            }
            return DocoHelpers::response($body['response']);
        } catch (\RequestException $e) {
            $result = ['response' => ['title' => "Terjadi Kesalahan", 'text' => 'Hapus Data Klaim Gagal']];
            $result = json_encode($e->getMessage());
            return DocoHelpers::response($result, 422);
        }
        $result = ['response' => ['title' => "Proses Berhasil", 'text' => 'Hapus Data Klaim Berhasil']];
        return DocoHelpers::response($result);
    }

    public function actionFinalKlaim()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $nosep = $post['nosep'];
        $aksi = 'Final Klaim';
        try {
            $model = new KlaimInacbgRanapForm;
            $model->attributes = $post['KlaimInacbgRanapForm'];
            $model->tgl_masuk = date('Y-m-d H:i', strtotime($model->tgl_masuk));
            $model->tgl_keluar = date('Y-m-d H:i', strtotime($model->tgl_keluar));
            $kelasEksekutif = ArrayHelper::getValue($post['KlaimInacbgRanapForm'], 'kelas_eksekutif');
            $model->lama_rawatintensif = ArrayHelper::getValue($post['KlaimInacbgRanapForm'], 'lama_rawatintensif');
            $model->jenis = ArrayHelper::getValue($post, 'jenis');
            $model->jenis_kelasrawat = ArrayHelper::getValue($post, 'kelas_rawat');
            $model->is_rawatintensif = isset($post['KlaimInacbgRanapForm']['is_rawatintensif']) ? $post['KlaimInacbgRanapForm']['is_rawatintensif'] : 0;
            if ($model->is_naikkelas == true) {
                $model->naik_kelas = 'kelas_' . ArrayHelper::getValue($post, 'naik_kelas');
            } else {
                $model->naik_kelas = 'kelas_' . ArrayHelper::getValue($post, 'kelas_rawat');
            }

            if ($kelasEksekutif == true) {
                $model->jenis_kelasrawat = '1';
            }
            $dataDetail = isset($post['KlaimInacbgDetail']) ? $post['KlaimInacbgDetail'] : [];
            $dataGrouper = isset($post['Grouper']) ? $post['Grouper'] : [];
            $codeGrouper = isset($post['Add']) ? $post['Add'] : [];
            $response = $this->_restPenjamin->post('inf-pasien-ranap-bpjs/final-klaim', ['form_params' => ['data' => $model->attributes, 'detail' => $dataDetail, 'grouper' => $dataGrouper, 'nosep' => $nosep, 'additional' => $codeGrouper]]);
            $body = json_decode($response->getBody(), true);
            $kunjungan_id = ArrayHelper::getValue($post['KlaimInacbgRanapForm'], 'kunjungan_id');
            $logActivity = $this->setLogActivity($aksi, $kunjungan_id, $body);
            if (isset($body['response']['metadata'])) {
                $title = "Proses Berhasil";
                $message = "Final Klaim Berhasil";
                $statusCode = 200;
                if ($body['response']['metadata']['code'] == 400) {
                    $title = "Oops";
                    $message = $body['response']['metadata']['message'];
                    $statusCode = 422;
                }

                $result = ['response' => ['title' => $title, 'text' => $message]];
                return DocoHelpers::response($result, $statusCode);
            }

            if (isset($body['response']['status']) != 200) {
                $result = ['response' => ['title' => isset($body['response']) ? $body['response'] : $body]];
                return DocoHelpers::response($result, $body['metadata']['status']);
            }
        } catch (Exception $e) {
            $result = ['response' => ['title' => "Terjadi Kesalahan", 'text' => 'Final Klaim Gagal']];
            return DocoHelpers::response($result, 422);
        }
    }
    public function actionCetakKlaim($sep)
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $cetakklaim = [
            'metadata' => [
                'method' => 'claim_print',
            ],
            'data' => [
                'nomor_sep' => $sep,
            ],
        ];
        $cetakklaim = json_decode(DocoHelpers::restInacbgs($cetakklaim), true);
        $decoded = base64_decode($cetakklaim['data']);
        $file = '../uploads/INACBGS - ' . $sep . '.pdf';
        file_put_contents($file, $decoded);
        return DocoHelpers::previewPdf($file);
    }

    public function actionEditUlangKlaim()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $nosep = $post['nosep'];
        $admisi = isset($post['admisi']) ?  $post['admisi'] : null;
        $aksi = 'Edit Eklaim';
        $kunjungan_id = DocoHelpers::decrypt($post['kunjungan_id']);
        try {
            $response = $this->_restPenjamin->post('inf-pasien-ranap-bpjs/update-klaim', [
                'form_params' =>
                [
                    'pendaftaran_id' => $post['pendaftaranid'],
                    'admisi' => $admisi,
                    'nomor_sep' => $nosep,
                ],
            ]);
            $body = json_decode($response->getBody(), true);
            $logActivity = $this->setLogActivity($aksi, $kunjungan_id, $body);
            if ($body['metadata']['status'] != 200) {
                $result = ['response' => ['title' => isset($body['response']) ? $body['response'] : $body]];
                return DocoHelpers::response($result, $body['metadata']['status']);
            }
            return DocoHelpers::response($body['response']);
        } catch (\RequestException $e) {
            $result = ['response' => ['title' => "Terjadi Kesalahan", 'text' => 'Edit Data Klaim Gagal']];
            return DocoHelpers::response($result, 422);
        }
    }

    private function getTotalTagihanRs($detail)
    {
        $prosedur_bedah = isset($detail['prosedur_bedah']) ? $detail['prosedur_bedah'] : 0;
        $prosedur_nonbedah = isset($detail['prosedur_non_bedah']) ? $detail['prosedur_non_bedah'] : 0;
        $konsultasi = isset($detail['konsultasi']) ? $detail['konsultasi'] : 0;
        $tenaga_ahli = isset($detail['tenaga_ahli']) ? $detail['tenaga_ahli'] : 0;
        $keperawatan = isset($detail['keperawatan']) ? $detail['keperawatan'] : 0;
        $penunjang = isset($detail['penunjang']) ? $detail['penunjang'] : 0;
        $radiologi = isset($detail['radiologi']) ? $detail['radiologi'] : 0;
        $laboratorium = isset($detail['laboratorium']) ? $detail['laboratorium'] : 0;
        $pelayanan_darah = isset($detail['pelayanan_darah']) ? $detail['pelayanan_darah'] : 0;
        $rehabilitasi = isset($detail['rehabilitasi']) ? $detail['rehabilitasi'] : 0;
        $kamar_akomodasi = isset($detail['kamar_akomodasi']) ? $detail['kamar_akomodasi'] : 0;
        $rawat_intensif = isset($detail['rawat_intensif']) ? $detail['rawat_intensif'] : 0;
        $obat = isset($detail['obat']) ? $detail['obat'] : 0;
        $alkes = isset($detail['alkes']) ? $detail['alkes'] : 0;
        $bmhp = isset($detail['bmhp']) ? $detail['bmhp'] : 0;
        $sewa_alat = isset($detail['sewa_alat']) ? $detail['sewa_alat'] : 0;
        $obat_kemoterapi = isset($detail['obat_kemoterapi']) ? $detail['obat_kemoterapi'] : 0;
        $obat_kronis = isset($detail['obat_kronis']) ? $detail['obat_kronis'] : 0;

        $totalTagihanRs = ($prosedur_bedah + $prosedur_nonbedah + $konsultasi + $tenaga_ahli + $keperawatan + $penunjang + $radiologi + $laboratorium + $pelayanan_darah + $rehabilitasi + $kamar_akomodasi + $rawat_intensif + $obat + $alkes + $bmhp + $sewa_alat + $obat_kemoterapi + $obat_kronis);

        return $totalTagihanRs;
    }

    private function getValueNaikKelas($info)
    {
        $naikKelasKlaim = isset($info['naik_kelas_klaim']) ? $info['naik_kelas_klaim'] : $info['naik_kelas'];

        if (stripos(strtolower($naikKelasKlaim), 'kelas_') !== false) {
            if ($naikKelasKlaim == 'kelas_5' || $naikKelasKlaim == 5) {
                $naikKelas = 5;
            } elseif ($naikKelasKlaim == 'kelas_4' || $naikKelasKlaim == 4) {
                $naikKelas = 4;
            } elseif ($naikKelasKlaim == 'kelas_3' || $naikKelasKlaim == 3) {
                $naikKelas = 3;
            } elseif ($naikKelasKlaim == 'kelas_2' || $naikKelasKlaim == 2) {
                $naikKelas = 2;
            } else {
                $naikKelas = 1;
            }
        } else {
            if ($naikKelasKlaim == 'vip') {
                $naikKelas = 4;
            } elseif ($naikKelasKlaim == 'vvip') {
                $naikKelas = 5;
            } else {
                $naikKelas = $naikKelasKlaim;
            }
        }
        return $naikKelas;
    }

    private function getIsNaikKelas($jenis_kelasrawat, $naik_kelas)
    {
        $isNaikKelas = false;
        if ($jenis_kelasrawat == 1) {
            if ($naik_kelas == 2) {
                $isNaikKelas = false;
            } else if ($naik_kelas == 3) {
                $isNaikKelas = false;
            } else if ($naik_kelas == 4) {
                $isNaikKelas = true;
            } else if ($naik_kelas == 5) {
                $isNaikKelas = true;
            }
        } elseif ($jenis_kelasrawat == 2) {
            if ($naik_kelas == 1) {
                $isNaikKelas = true;
            } else if ($naik_kelas == 3) {
                $isNaikKelas = false;
            } else if ($naik_kelas == 4) {
                $isNaikKelas = true;
            } else if ($naik_kelas == 5) {
                $isNaikKelas = true;
            }
        } elseif ($jenis_kelasrawat == 3) {
            $isNaikKelas = true;
        }

        return $isNaikKelas;
    }

    public function actionKirimKlaimOnline()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $nosep = $post['nosep'];
        $admisi = isset($post['admisi']) ? $post['admisi'] : null;
        try {
            $response = $this->_restPenjamin->post('inf-pasien-ranap-bpjs/kirim-klaim-online', [
                'form_params' =>
                [
                    'pendaftaran_id' => $post['pendaftaranid'],
                    'admisi' => $admisi,
                    'nomor_sep' => $nosep,
                ],
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response($body['response']);
        } catch (\RequestException $e) {
            $result = ['response' => ['title' => "Terjadi Kesalahan", 'text' => 'Edit Data Klaim Gagal']];
            return DocoHelpers::response($result, 422);
        }
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filters = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $path = Yii::getAlias("@download") . "/informasi-pasien-ranap-bpjs.xlsx";
            $response = $this->_restPenjamin->get('inf-pasien-ranap-bpjs/export-excel', [
                'query' => $filters,
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path, true);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        try {
            $filters = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/informasi-pasien-ranap-bpjs.pdf";
            $response = $this->_restPenjamin->get('inf-pasien-ranap-bpjs/export-pdf', [
                'save_to' => $path,
                'query' => $filters,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }

    private function cekKelas($jenis_kelasrawat, $hak_kelas)
    {
        $status = false;
        if ($jenis_kelasrawat != $hak_kelas) {
            $status = true;
        }
        return $status;
    }

    public function actionAddDiagnosaTambahan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $response = $this->_restPenjamin->post('inf-pasien-ranap-bpjs/add-diagnosa-tambahan', [
                'form_params' =>
                [
                    'pendaftaran_id' => $post['id'],
                    'diagnosa_id' => $post['diagnosa_id'],
                    'diagnosa_nama' => $post['nama_diagnosa'],
                    'diagnosa_kode' => $post['kode_diagnosa'],
                    'type' => $post['type_diagnosa'],
                ],
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response($body);
        } catch (RequestException $e) {
            $result = ['response' => ['title' => "Terjadi Kesalahan"]];
            return DocoHelpers::response($result, 422);
        }
    }

    public function actionSingleSync()
    {
        $title = 'Sinkron Pasien';
        $model = new SyncPasienForm;
        $response = $this->_restPenjamin->get('allow/get-instalasi-eklaim');
        $body = json_decode($response->getBody(), true);
        $res = ArrayHelper::getValue($body, 'response', []);
        $data = ArrayHelper::getValue($res, 'data', []);

        if (isset($res['data'])) {
            $instalasi = ArrayHelper::map($res['data'], 'lookup_value', 'lookup_name');
        } else {
            $instalasi = [];
        }

        return $this->renderPartial('sync', get_defined_vars());
    }


    public function actionSingleSyncSave($randString, $instalasi, $no)
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

    public function actionSyncAll()
    {
        $title = 'Sinkron Semua Pasien';
        $model = new SyncPasienForm;
        $instalasi = [
            DocoConstants::SINGKATAN_RJ => DocoConstants::TITLE_RJ,
            DocoConstants::SINGKATAN_RD => DocoConstants::TITLE_RD,
            DocoConstants::SINGKATAN_RI => DocoConstants::TITLE_RI
        ];

        $randString = DocoHelpers::generateRandomString();
        Yii::$app->session->setFlash('rand-sinkron-semua', $randString);
        return $this->renderPartial('sync-all', get_defined_vars());
    }


    public function actionSyncAllSave($randString)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $randString = $request->get('randString');
        return $this->guzzleExec($this->_restPenjamin, [
            'url' => "sinkronisasi/trigger-sinkron-global",
            'payload' => [
                'query' => [
                    'randString' => $randString
                ]
            ],
        ]);
    }

    /**
     * @function : Generate nomor pengajuan klaim covid
     */
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
                    $response = $this->_restPenjamin->post('inf-pasien-ranap-bpjs-covid/update-data', [
                        'form_params' =>
                        [
                            'kunjunganId' => DocoHelpers::decrypt($post['kunjunganId']),
                            'nosep' => $post['nosep'],
                            'jenisIdentitas' => $post['jenisIdentitas'],
                            'noIdentitas' => $post['noIdentitas'],
                            'noPengajuan' => $body['response']['claim_number'],
                        ],
                    ]);
                    $body = json_decode($response->getBody(), true);
                    if ($body['metadata']['status'] != 200) {
                        $result = ['response' => ['text' => isset($body['response']) ? $body['response']['text'] : $body, 'title' => isset($body['response']) ? $body['response']['title'] : $body]];
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

    public function actionGetBerkas()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $nosep = ArrayHelper::getValue($get, 'nosep');
        $response = $this->_restPenjamin->get('inf-pasien-ranap-bpjs/get-berkas', ['query' => ['nosep' => $nosep]]);
        $body = json_decode($response->getBody(), true);
        $res = ArrayHelper::getValue($body, 'response', []);
        $data = ArrayHelper::getValue($res, 'data', []);
        $berkas = [];
        if ($data) {
            foreach ($data as $key => $value) {
                if (!isset($value['file_class'])) {
                    $berkas[$value['file_class']] = [];
                }

                $message = '';
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
                $tmpfile_contents = file_get_contents($file->tempName);
                $file->saveAs(Yii::getAlias("@download") . '/' . $file->name);
                $base64 = base64_encode($tmpfile_contents);
                $response = $this->_restPenjamin->post('inf-pasien-ranap-bpjs/upload-berkas', [
                    'form_params' =>
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
                    $message = $res['metadata']['message'] . ' ' . $code . ' ' . $mes;
                }

                $res = [
                    'code'    => $res['metadata']['code'],
                    'id'      => isset($res['response']['file_id']) ? $res['response']['file_id'] : '',
                    'message' => $message
                ];
                return json_encode($res);
            }
        } catch (RequestException $e) {
            $result = ['response' => ['title' => $e->getMessage()]];
            return DocoHelpers::response($result, 422);
        }
    }

    public function actionRemoveBerkas()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $file_id = ArrayHelper::getValue($get, 'file_id');
        if (isset($file_id)) {
            $response = $this->_restPenjamin->post('inf-pasien-rajal-bpjs/delete-berkas', [
                'form_params' =>
                [
                    'nosep'   => $get['nosep'],
                    'file_id' => $file_id
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            $res = $body['response'];
            return json_encode($res);
        }

        return DocoHelpers::response([], 200);
    }

    public function actionPreviewFile()
    {
        $request = Yii::$app->request;
        $pathName =  Yii::getAlias("@download") . '/' . $request->get('filename');
        return DocoHelpers::previewPdf($pathName);
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

    public function actionUpdateNoklaim()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $id = ArrayHelper::getValue($get, ['kunjungan_id']);
        $is_jaminan = ArrayHelper::getValue($get, ['is_jaminan']);
        $id_dec = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restPenjamin->post(
                'inf-pasien-ranap-bpjs/update-noklaim',
                [
                    'query' =>
                    [
                        'kunjungan_id'   => $id_dec,
                        'is_jaminan' => $is_jaminan
                    ]
                ]
            );

            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response([
                'status' => 200,
                $body
            ]);
        } catch (RequestException $e) {
            $result = ['response' => ['title' => $e->getMessage()]];
            return DocoHelpers::response($result, 422);
        }
    }

    public function actionEditKoreksi()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $no_pendaftaran = $request->get('no_pendaftaran');
        $title = 'Edit Koreksi';
        $model = new FormKoreksi;
        $diagnosa = $this->getDetail($id, null);
        if (isset($diagnosa['detail']['Morfologi'])) unset($diagnosa['detail']['Morfologi']);
        if (isset($diagnosa['detail']['Sebab Luar'])) unset($diagnosa['detail']['Sebab Luar']);
        $labelINACBS = DocoConstants::$labelDiagnosaINACBSEklaim;
        $status_kunjungan = ArrayHelper::getValue($diagnosa['data'], 'status_kunjungan');
        $stateEnc = DocoHelpers::encrypt($status_kunjungan);
        $idEnc = DocoHelpers::encrypt($id);
        $info = ArrayHelper::getValue($diagnosa, 'data', []);
        return $this->renderAjax('_modal_edit_koreksi', get_defined_vars());
    }

    public function actionValidasiSitb()
    {
        $request = Yii::$app->request;
        $nosep = $request->post('nosep');
        $nomer_sitb = $request->post('nomer_sitb');
        $no_rekammedik = $request->post('no_rekammedik');
        $nomer_peserta = $request->post('nomer_peserta');
        $nama_pasien = $request->post('nama_pasien');
        $tanggal_lahir = $request->post('tanggal_lahir');
        $jenis_kelamin = $request->post('jenis_kelamin');
        $kunjungan_id = $request->post('kunjungan_id');

        $response = $this->_restPenjamin->get('inf-pasien-ranap-bpjs/validasi-sitb', [
            'query' => [
                'nosep' => $nosep,
                'nomer_sitb' => $nomer_sitb,
                'no_rekammedik' => $no_rekammedik,
                'nomer_peserta' => $nomer_peserta,
                'nama_pasien' => $nama_pasien,
                'tanggal_lahir' => $tanggal_lahir,
                'jenis_kelamin' => $jenis_kelamin,
                'kunjungan_id' => $kunjungan_id
            ],
        ]);

        $body = json_decode($response->getBody(), true);
        $statusCode = ArrayHelper::getValue($body, "code");
        $response = $body['response'];
        if ($statusCode == 400) {
            return DocoHelpers::response($response, $statusCode);
        }

        return DocoHelpers::response($response, $statusCode);
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
            'url' => 'inf-pasien-ranap-bpjs/filters',
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

    public function actionSetPrimer()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $response = $this->_restPenjamin->post('inf-pasien-ranap-bpjs/set-primer', [
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

    public function actionValidasiDataSitb()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $validation = ArrayHelper::getValue($get, 'validation.data', []);
        return $this->renderAjax('_modal_pasientb', compact('validation'));
    }

    public function actionBatalValidasiSitb()
    {
        $request = Yii::$app->request;
        $nosep = $request->post('nosep');
        $kunjungan_id = $request->post('kunjungan_id');


        $response = $this->_restPenjamin->get('inf-pasien-ranap-bpjs/batal-validasi-sitb', [
            'query' => [
                'nosep' => $nosep,
                'kunjungan_id' => $kunjungan_id
            ],
        ]);

        $body = json_decode($response->getBody(), true);
        $statusCode = ArrayHelper::getValue($body, "code");
        $response = $body['response'];
        if ($statusCode == 400) {
            return DocoHelpers::response($response, $statusCode);
        }

        return DocoHelpers::response($response, $statusCode);
    }

    public function actionKonfirmasiSitb()
    {
        $request = Yii::$app->request;
        $nosep = $request->post('nosep');
        $nomer_sitb = $request->post('nomer_sitb');
        $kunjungan_id = $request->post('kunjungan_id');

        $response = $this->_restPenjamin->get('inf-pasien-ranap-bpjs/konfirmasi-sitb', [
            'query' => [
                'nosep' => $nosep,
                'nomer_sitb' => $nomer_sitb,
                'kunjungan_id' => $kunjungan_id
            ],
        ]);

        $body = json_decode($response->getBody(), true);
        $statusCode = ArrayHelper::getValue($body, "code");
        $response = $body['response'];
        if ($statusCode == 400) {
            return DocoHelpers::response($response, $statusCode);
        }

        return DocoHelpers::response($response, $statusCode);
    }

    public function actionUnduhDokumen()
    {
        $request = Yii::$app->request;
        $kunjungan_id = $request->post('data');

        $response = $this->_restPenjamin->post('inf-pasien-ranap-bpjs/unduh-dokumen', [
            'form_params' => [
                'kunjungan_id' => $kunjungan_id
            ],
        ]);

        $body = json_decode($response->getBody(), true);
        $statusCode = ArrayHelper::getValue($body, "code");
        $response = $body['response'];
        if ($statusCode == 400) {
            return DocoHelpers::response($response, $statusCode);
        }

        return DocoHelpers::response($response, $statusCode);
    }

    public function actionUploadDokumenEklaim()
    {
        @ini_set('max_execution_time', '300');
        $request = Yii::$app->request;
        $params = @parse_ini_file('../config/env/.env', true);
        $pendaftaran_id = $request->get('pendaftaran_id');
        $bulkArray = $request->get("remote_url");
        try {
            $response = $this->_restPenjamin->post('inf-pasien-ranap-bpjs/get-data-dokumen-upload', [
                'form_params' => [
                    'pendaftaran_id' => $pendaftaran_id
                ],
            ]);

            $body = json_decode($response->getBody(), true);
            $dokumen = isset($body['response']['data']) ? $body['response']['data'] : [];
            $connectionFtp = $this->actionFtpSendFile();
            $payload_log = [];
            if($connectionFtp) {
                if (! empty($dokumen)) {
                    foreach ($dokumen as $key => $value) {
                        foreach ($bulkArray as $b => $valBulk) {
                            if ($value['pendaftaran_id'] == $valBulk['pendaftaran_id']) {
                                $dir = dirname(dirname(dirname(dirname(__DIR__))));
                                $pathDokumen = $dir . '/frontend/' . $value['path'] . '/' . $value['filename'];
                                $sourcePath = isset($params['konfigftp']) ? $params['konfigftp']['path_doc_upload'] . $value['path'] : null;
                                $remote_file = isset($params['konfigftp']) ? $params['konfigftp']['path'] : null;
                                $remote_file = $valBulk['remote_file'];
                                $dirExists = ftp_nlist($connectionFtp, $remote_file);
                                $log_dokumen = [
                                    'dokumen_id' => $value['dokumenupload_id'],
                                    'pendaftaran_id' => $pendaftaran_id[0],
                                    'status' => null
                                ];
                                if ($dirExists == false) {
                                    @ftp_mkdir($connectionFtp, $remote_file);
                                }
                                if (ftp_nlist($connectionFtp, $sourcePath . $value['filename'])) {
                                    ftp_pasv($connectionFtp, true);
                                    $local_dir = $dir . '/frontend/uploads/'.$value['filename'];
                                    ftp_get($connectionFtp, $local_dir, $sourcePath . $value['filename'], FTP_BINARY);
                                    $upload = ftp_put($connectionFtp, $remote_file . '/' . $value['filename'], $local_dir, FTP_BINARY);
                                    if ($upload) {
                                        $log_dokumen['status'] = true;
                                        $payload_log[] = $log_dokumen;
                                        unlink($local_dir);
                                        continue;
                                    }
                                } else {
                                    if (file_exists($pathDokumen)) {
                                        $filename = pathinfo($pathDokumen, PATHINFO_BASENAME);
                                        if(ftp_put($connectionFtp, $remote_file . '/' . $filename, $pathDokumen, FTP_BINARY)){
                                            $log_dokumen['status'] = true;
                                            $payload_log[] = $log_dokumen;
                                            continue;
                                        }
                                    }
                                }
                                $log_dokumen['status'] = false;
                                $payload_log[] = $log_dokumen;
                                
                                
                            }
                        }
                    };
                    ftp_close($connectionFtp);
                    $send_data = [
                        'log' => $payload_log,
                        'type' => 'upload_dokumen'
                    ];
                    return DocoHelpers::response([
                        'status' => 200,
                        'data' => [
                            'pendaftaran' => $pendaftaran_id,
                            'dokumen' => $dokumen,
                            'send_data' => $send_data
                        ],
                        'message' => "Upload document success"
                    ]);
                } else {
                    return DocoHelpers::response([
                        'status' => 200,
                        'message' => "Dokumen tidak ada di upload"
                    ]);
                }
            }
        } catch (\Exception $th) {
            return DocoHelpers::response([
                'status' => 500,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    public function actionImportKoding()
    {
        $request = Yii::$app->request;
        $kunjungan_id = $request->post('kunjunganId');
        $nosep = $request->post('nosep');
        try {
            $response = $this->_restPenjamin->post('inf-pasien-ranap-bpjs/import-koding', [
                'form_params' =>
                [
                    'kunjungan_id' => $kunjungan_id,
                    'nosep' => $nosep
                ],
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response($body);
        } catch (RequestException $e) {
            $result = ['response' => ['title' => "Terjadi Kesalahan", 'message' => $e->getMessage()]];
            return DocoHelpers::response($result, 422);
        }
    }

    public function actionLoadDiagnosa($id)
    {
        try {
            $diagnosa = $this->getDetail($id, null);
            $labelINACBS = DocoConstants::$labelDiagnosaINACBSEklaim;
            return $this->renderAjax('./partial/_koding', compact('diagnosa', 'labelINACBS'));
        } catch (\Exception $th) {
            return DocoHelpers::response([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function actionFtpSendFile()
    {
        $params = @parse_ini_file('../config/env/.env', true);
        $host = isset($params['konfigftp']) ? $params['konfigftp']['host'] : null;
        $user = isset($params['konfigftp']) ? $params['konfigftp']['username'] : null;
        $password = isset($params['konfigftp']) ? $params['konfigftp']['password'] : null;
        $ftpConn = ftp_connect($host);
        $login = ftp_login($ftpConn, $user, $password);
        ftp_pasv($ftpConn, true);

        return $ftpConn;
    }

    public function actionLogActivity($id)
    {
        $title = 'Log Activity';
        $transaksi_id = DocoHelpers::encrypt($id);
        return $this->renderAjax('_modal_log_activity', get_defined_vars());
    }

    public function actionGetLogActivity($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $filter['advanced-filter']['transaksi_id'] = DocoHelpers::decrypt($id);
        $filter['advanced-filter']['tipe'] = 'EKLAIM';

        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->guzzleExec($this->_restPenjamin, [
                'url' => 'inf-pasien-ranap-bpjs/get-log-activity',
                'method' => 'GET',
                'payload' => [
                    'query' => $filter
                ],
            ]);
            $no = $request->get('start', 1);
            foreach ($response['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $primaryKey = DocoHelpers::encrypt($value['transaksi_id']);
                unset($value['transaksi_id']);
                $value['primary'] = $primaryKey;
                $value['tgl'] = date('d M Y H:i:s', strtotime($value['tgl']));
                $value['status'] = $value['additional_detail'] == 'Gagal' ? '<span class="badge badge-danger">Gagal</span>' : '<span class="badge badge-success">Berhasil</span>';
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $response['_meta']['totalCount'];
            $result['recordsFiltered'] = $response['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function setLogActivity($aksi, $kunjungan_id, $body)
    {
        $status = $body['metadata']['status'];
        $response = json_encode($body['response']);

        $log = $this->guzzleExec($this->_restPenjamin, [
            'url' => 'inf-pasien-ranap-bpjs/save-log-activity',
            'method' => 'post',
            'payload' => [
                'query' => [
                    'aksi'          => $aksi,
                    'kunjungan_id'  => $kunjungan_id,
                    'hasil'         => $status == 200 ? 'Berhasil' : 'Gagal',
                    'response'      => $response
                ]
            ],
        ]);

        return $log;
    }

    public function actionShowPopupExcel()
    {
        $title = 'Informasi Pasien Ranap BPJS';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams = DocoDatatableHelper::advancedFilterParam();

        // $yiiRestfulParams['advanced-filter']['tgl_pulang'] = $request->get('tgl_pulang', null);
        // $yiiRestfulParams['advanced-filter']['instalasi_id'] = $request->get('instalasi_id', null);
        // $yiiRestfulParams['advanced-filter']['carabayar_nama_text'] = $request->get('carabayar_nama',null);
        // $yiiRestfulParams['advanced-filter']['carabayar_id'] = $request->get('carabayar_id',null);
        // $yiiRestfulParams['advanced-filter']['penjamin_text'] = urldecode($request->get('penjamin', null));

        Yii::$app->session->set($randString, $yiiRestfulParams);
        return $this->renderAjax('partial/_modal_excel', get_defined_vars());
    }

    /**
     * show popup excel
     * PCP-49
     * 
     * @author faidzindev@gmail.com
     **/
    // public function actionShowPopupExcel()
    // {
    //     print_r('test');
    // }


    /**
     * process sync excel
     * PCP-49
     *
     * @author faidzindev@gmail.com
     **/
    public function actionProcessSyncExcel($randString)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->get($randString);
        $session['randString'] = $randString;
        //remove session after stored to variable;
        Yii::$app->session->remove($randString);
        return $this->guzzleExec($this->_restPenjamin, [
            'url' => "inf-pasien-ranap-bpjs/export-excel-bg-process",
            'payload' => [
                'query' => $session
            ],
        ]);
    }

    public function actionDownloadExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('fileName', null);
        $fileDownloads = 'Informasi Klaim - ' . date("dmY") . '.xlsx';

        $path = Yii::getAlias("@download") . '/' . $fileDownloads;
        $response = $this->_restPenjamin->get('inf-pasien-ranap-bpjs/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path, true);
    }

    public function actionCekDokumen(){
        $request = Yii::$app->request;
        $id = $request->get('id');

        $listDokumen = $this->guzzleExec($this->_restPenjamin,[
            'url' => 'inf-pasien-ranap-bpjs/get-list-dokumen',
            'method' => 'GET',
            'payload' => [
                'query' => [
                    'id' => $id
                    ]
            ],
        ]);
        $listDokumen = ArrayHelper::map($listDokumen['data'],'nama_dokumen','nama_dokumen');

        return $this->renderAjax('__modal_cek_dokumen', get_defined_vars());
    }

    

    public function actionGetCekUnduhDokumen($id){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $filter['advanced-filter']['pendaftaranId'] = $id;

        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->guzzleExec($this->_restPenjamin,[
                'url' => 'inf-pasien-ranap-bpjs/get-cek-dokumen',
                'method' => 'GET',
                'payload' => [
                    'query' => $filter
                ],
            ]);
            $no = $request->get('start', 1);
            foreach ($response['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['last_unduh'] = $value['latest_unduh'];
                $value['nama_dokumen'] = $value['nama_dokumen'];
                $value['status'] = $value['status'] == false ? '<span class="badge badge-danger">Gagal</span>' : '<span class="badge badge-success">Berhasil</span>';
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $response['_meta']['totalCount'];
            $result['recordsFiltered'] = $response['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionAddDiagnosaIdrg()
    {
        try {
            $request = Yii::$app->request;
            $diagnosaIdrg = json_decode($request->post('dataDiagnosa'), true);
            $procedureIdrg = json_decode($request->post('dataProcedure'), true);
            $kunjunganId = $request->post('kunjunganId');

            foreach ($diagnosaIdrg as $key => $value) {
                $diagnosaIdrg[$key]['kunjungan_id'] = $kunjunganId;
                $diagnosaIdrg[$key]['is_diagnosa_idrg'] = true;
            }

            foreach ($procedureIdrg as $key => $value) {
                $procedureIdrg[$key]['kunjungan_id'] = $kunjunganId;
                $procedureIdrg[$key]['is_diagnosa_idrg'] = false;
            }

            $diagnosa = array_merge($diagnosaIdrg, $procedureIdrg);

            if (empty($diagnosa)) {
                $result = [
                    'response' => [
                        'title' => 'Proses Gagal',
                        'message' => 'Silahkan pilih data diagnosa & procedure !',
                    ]
                ];
                return DocoHelpers::response($result, 422);
            }

            $response = $this->guzzleExec($this->_restPenjamin,[
                'url' => 'inf-pasien-ranap-bpjs/koreksi-diagnosa',
                'method' => 'POST',
                'payload' => [
                    'form_params' => [
                        'kunjungan_id' => $kunjunganId,
                        'idrg' => $diagnosa
                    ]
                ],
                'returnResponse' => false
            ]);

            if (isset($response['code']) && $response['code'] != 200) {
                $result = [
                    'response' => [
                        'title' => 'Proses Gagal',
                        'message' => isset($response['message']) ? $response['message'] : 'Gagal melakukan grouping IDRG',
                        'response' => $response
                    ]
                ];
                return DocoHelpers::response($result, 422);
            }

            return DocoHelpers::response([
                'response' => [
                    'title' => 'Proses Berhasil',
                    'text' => isset($response['message']) ? $response['message'] : 'Berhasil melakukan grouping IDRG',
                    'response' => $response
                ]
            ], 200);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionFinalIdrg()
    {
        try {
            $request = Yii::$app->request;
            $kunjunganId = $request->post('kunjunganId');
            
            $response = $this->guzzleExec($this->_restPenjamin,[
                'url' => 'inf-pasien-ranap-bpjs/final-idrg',
                'method' => 'GET',
                'payload' => [
                    'query' => [
                        'kunjungan_id' => $kunjunganId,
                    ]
                ],
            ]);

            if (isset($response['code']) && $response['code'] != 200) {
                $result = [
                    'response' => [
                        'title' => 'Proses Gagal',
                        'message' => isset($response['message']) ? $response['message'] : 'Gagal melakukan final IDRG',
                    ]
                ];
                return DocoHelpers::response($result, 422);
            }

            $result = [
                'response' => [
                    'title' => 'Proses Berhasil',
                    'text' => isset($response['message']) ? $response['message'] : 'Berhasil melakukan final IDRG',
                ]
            ];

            return DocoHelpers::response($result, 200);
        } catch (\Throwable $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionEditIdrg()
    {
        try {
            $request = Yii::$app->request;
            $kunjunganId = $request->post('kunjunganId');
            
            $response = $this->guzzleExec($this->_restPenjamin,[
                'url' => 'inf-pasien-ranap-bpjs/edit-idrg',
                'method' => 'GET',
                'payload' => [
                    'query' => [
                        'kunjungan_id' => $kunjunganId,
                    ]
                ],
                'returnResponse' => false
            ]);

            if (isset($response['code']) && $response['code'] != 200) {
                return DocoHelpers::response([
                    'response' => [
                        'title' => 'Proses Gagal',
                        'message' => isset($response['message']) ? $response['message'] : 'Gagal melakukan edit ulang IDRG',
                    ]
                ], 422);
            }

            return DocoHelpers::response([
                'response' => [
                    'title' => 'Proses Berhasil',
                    'text' => isset($response['message']) ? $response['message'] : 'Berhasil melakukan edit ulang IDRG',
                ]
            ]);
        } catch (\Throwable $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }


    public function actionAddDiagnosaInacbgs()
    {
        try {
            $request = Yii::$app->request;
            $kunjunganId = $request->post('kunjunganId');
            $diagnosaInacbgs = json_decode($request->post('dataDiagnosa'), true);
            $procedureInacbgs = json_decode($request->post('dataProcedure'), true);

            if (empty($diagnosaInacbgs)) {
                return DocoHelpers::response([
                    'response' => [
                        'title' => 'Proses Gagal',
                        'message' => 'Data diagnosa tidak boleh kosong !',
                    ]
                ], 422);
            }

            foreach ($diagnosaInacbgs as $key => $value) {
                $diagnosaInacbgs[$key]['kunjungan_id'] = $kunjunganId;
                $diagnosaInacbgs[$key]['is_diagnosa'] = true;
            }

            if (empty($procedureInacbgs)) {   
                $procedureInacbgs = [];
            }

            foreach ($procedureInacbgs as $key => $value) {
                $procedureInacbgs[$key]['kunjungan_id'] = $kunjunganId;
                $procedureInacbgs[$key]['is_diagnosa'] = false;
            }

            $diagnosa = array_merge($diagnosaInacbgs, $procedureInacbgs);

            $response = $this->guzzleExec($this->_restPenjamin,[
                'url' => 'inf-pasien-ranap-bpjs/add-diagnosa-inacbgs',
                'method' => 'GET',
                'payload' => [
                    'form_params' => [
                        'kunjungan_id' => $kunjunganId,
                        'diagnosa' => $diagnosa,
                    ]
                ],
                'returnResponse' => false
            ]);

            if (isset($response['code']) && $response['code'] != 200) {
                return DocoHelpers::response([
                    'response' => [
                        'title' => 'Proses Gagal',
                        'message' => isset($response['message']) ? $response['message'] : 'Gagal melakukan grouping INACBGS',
                    ]
                ], 422);
            }

            return DocoHelpers::response([
                'response' => [
                    'title' => 'Proses Berhasil',
                    'text' => isset($response['message']) ? $response['message'] : 'Berhasil melakukan grouping INACBGS',
                    'response_diagnosa' => isset($response['response_diagnosa']) ? $response['response_diagnosa']['data'] : null,
                    'response_procedure' => isset($response['response_procedure']) ? $response['response_procedure']['data'] : null
                ]
            ]);
        } catch (\Throwable $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionFinalInacbgs()
    {
        try {
            $request = Yii::$app->request;
            $kunjunganId = $request->post('kunjunganId');

            $response = $this->guzzleExec($this->_restPenjamin, [
                'url' => 'inf-pasien-ranap-bpjs/final-inacbgs',
                'method' => 'GET',
                'payload' => [
                    'query' => [
                        'kunjungan_id' => $kunjunganId,
                    ]
                ],
            ]);

            if (isset($response['code']) && $response['code'] != 200) {
                $result = [
                    'response' => [
                        'title' => 'Proses Gagal',
                        'message' => isset($response['message']) ? $response['message'] : 'Gagal melakukan final INACBGS',
                    ]
                ];
                return DocoHelpers::response($result, 422);
            }

            $result = [
                'response' => [
                    'title' => 'Proses Berhasil',
                    'text' => isset($response['message']) ? $response['message'] : 'Berhasil melakukan final INACBGS',
                ]
            ];

            return DocoHelpers::response($result, 200);
        } catch (\Throwable $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionEditInacbgs()
    {
        try {
            $request = Yii::$app->request;
            $kunjunganId = $request->post('kunjunganId');

            $response = $this->guzzleExec($this->_restPenjamin, [
                'url' => 'inf-pasien-ranap-bpjs/edit-inacbgs',
                'method' => 'GET',
                'payload' => [
                    'query' => [
                        'kunjungan_id' => $kunjunganId,
                    ]
                ],
            ]);

            if (isset($response['code']) && $response['code'] != 200) {
                $result = [
                    'response' => [
                        'title' => 'Proses Gagal',
                        'message' => isset($response['message']) ? $response['message'] : 'Gagal melakukan edit ulang INACBGS',
                    ]
                ];
                return DocoHelpers::response($result, 422);
            }

            $result = [
                'response' => [
                    'title' => 'Proses Berhasil',
                    'text' => isset($response['message']) ? $response['message'] : 'Berhasil melakukan edit ulang INACBGS',
                ]
            ];

            return DocoHelpers::response($result, 200);
        } catch (\Throwable $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionKoreksiTransfusiDarah()
    {
        try {
            $request = Yii::$app->request;
            $kunjunganId = $request->post('kunjungan_id');

            $response = $this->guzzleExec($this->_restPenjamin, [
                'url' => 'inf-pasien-ranap-bpjs/koreksi-transfusi-darah',
                'method' => 'GET',
                'payload' => [
                    'query' => [
                        'kunjungan_id' => !is_numeric($kunjunganId) ? DocoHelpers::decrypt($kunjunganId) : $kunjunganId,
                        'kantong_darah' => $request->post('kantong_darah'),
                        'dializer' => $request->post('dializer'),
                    ]
                ],
            ]);

            if (isset($response['code']) && $response['code'] != 200) {
                $result = [
                    'response' => [
                        'title' => 'Proses Gagal',
                        'message' => isset($response['message']) ? $response['message'] : 'Gagal melakukan penambahan Kantong Darah !',
                    ]
                ];
                return DocoHelpers::response($result, 422);
            }

            $result = [
                'response' => [
                    'title' => 'Proses Berhasil',
                    'text' => isset($response['message']) ? $response['message'] : 'Berhasil melakukan penambahan Kantong Darah !',
                ]
            ];

            return DocoHelpers::response($result, 200);
        } catch (\Throwable $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionValidasiDiagnosaInacbgs()
    {
        $request = Yii::$app->request;
        $kunjungan_id = $request->post('kunjunganId');
        $nosep = $request->post('nosep');
        try {
            $response = $this->_restPenjamin->post('inf-pasien-ranap-bpjs/validasi-diagnosa', [
                'form_params' =>
                [
                    'kunjungan_id' => $kunjungan_id,
                    'nosep' => $nosep
                ],
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response($body);
        } catch (RequestException $e) {
            $result = ['response' => ['title' => "Terjadi Kesalahan", 'message' => $e->getMessage()]];
            return DocoHelpers::response($result, 422);
        }
    }
}
