<?php

/**
 * @Author: sunarko
 * @Date:   2018-06-05 13:42:42
 * @Description:
 */

namespace Doco\ranap\controllers;

use Yii;
use yii\filters\AccessControl;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;
use yii\base\Exception;

use function GuzzleHttp\json_encode;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\Pelayanan\PelayananHelpers;

use app\modules\ranap\models\InfoPasienRanapForm;
use app\modules\ranap\models\InfoPasienRanap;
use app\modules\ranap\models\PendaftaranForm;
use app\modules\ranap\models\PasienBatalPeriksaForm;
use app\modules\ranap\models\PasienPulangForm;
use app\modules\ranap\models\PasienDirujukKeluarForm;
use app\modules\ranap\models\PindahKamarForm;
use app\modules\ranap\models\PelayananJenazahForm;
use app\modules\ranap\models\RujukanPulangForm;
use app\modules\ranap\models\BatalStopAkomodasiForm;

use app\components\Traits\RujukanPasienTrait;

class InfPasienRanapController extends DocoController
{
    use RujukanPasienTrait;

    protected $_title = "Informasi pasien rawat inap";
    protected $_controller = '/ranap/inf-pasien-ranap';
    protected $_restRanap;
    protected $_restJenazah;
    protected $_restPendaftaran;

    protected $_data_pasien;
    protected $_id_ruangan;
    protected $_instalasi_id;
    protected $_pegawai_id;

    public function init()
    {
        parent::init();
        $this->_restRanap = Yii::$app->docoRest->ranap;
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restJenazah = Yii::$app->docoRest->jenazah;
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;
        $docoVars = Yii::$app->docoVars;
        $this->_id_ruangan = $docoVars->workspace('ruangan_id') ? $docoVars->workspace('ruangan_id') : 1;
        $this->_instalasi_id = $docoVars->workspace('instalasi_id') ? $docoVars->workspace('instalasi_id') : 1;
        $this->_pegawai_id = $docoVars->user('id_pegawai') ? $docoVars->user('id_pegawai') : 1;

        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
        $session = Yii::$app->session;
        $cache = Yii::$app->cache;
        $userIdentity = Yii::$app->session->get('user_identity');
        $data_pasien = [];
        $request = Yii::$app->request;
        $pendId = $request->get('id', null);
        $pendaftaran_id = DocoHelpers::decrypt($pendId);
        try {
            $cacheData = $cache->get('pasien-pendaftaran-id-' . $pendId);
            if (!$cacheData && !empty($pendId)) {
                $response = $this->_restRanap->get(
                    'pemeriksaan-rawat-inap/get-pasien',
                    [
                        'query' => [
                            'id' => $pendaftaran_id,
                            'cppt' => true
                        ]
                    ]
                );
                $response = json_decode($response->getBody(), true);
                $data_pasien = $response["response"]["data"];

                // cppt
                $data_pasien['cppt'] = !empty($response['response']['cppt']) ? $response['response']['cppt'] : [];

                // askep riwayat_penyakit_keluarga
                if (isset($data_pasien['r_penyakitkeluarga']) && $data_pasien['r_penyakitkeluarga'] != '') {
                    $r_penyakitkeluarga = json_decode($data_pasien['r_penyakitkeluarga']);

                    if (!empty($r_penyakitkeluarga)) {
                        foreach ($r_penyakitkeluarga as $key => $value) {
                            $r_penyakitkeluarga[$key] = $value->text;
                        }
                    }

                    $r_penyakitkeluarga = implode(', ', $r_penyakitkeluarga);
                    $data_pasien['askep']['riwayat_penyakit_keluarga'] = $r_penyakitkeluarga;
                } else {
                    $r_penyakitkeluarga = '-';
                    $data_pasien['askep']['riwayat_penyakit_keluarga'] = '-';
                }

                // askep status_merokok
                if (isset($data_pasien['is_merokok']) && $data_pasien['is_merokok'] == true) {
                    $merokok = Yii::t('fe', 'Ya, ') . $data_pasien['jml_rokok'] . Yii::t('fe', ' batang rokok perhari');
                    $data_pasien['askep']['status_merokok'] = $merokok;
                } else {
                    $merokok = Yii::t('fe', 'Tidak');
                    $data_pasien['askep']['status_merokok'] = '';
                }

                // askep status ekonomi (blank for now)
                $data_pasien['askep']['status_ekonomi'] = '-';

                $data_pasien['titipan'] = false;
                $data_pasien['kelas_ditagihkan'] = '';

                if (isset($data_pasien['pindahkamar_id']) && $data_pasien['pindahkamar_id']) {
                    if ($data_pasien['is_stoppasientitipan'] == false) {
                        if ($data_pasien['is_pasientitipan_pk']) {
                            $data_pasien['titipan'] = true;
                            $data_pasien['kelas_ditagihkan'] = strtoupper($data_pasien['kelas_ditagihkan_nama']);
                        }
                    }
                } else {
                    if ($data_pasien['is_stoptitipan'] == false) {
                        if ($data_pasien['is_pasientitipan']) {
                            $data_pasien['titipan'] = true;
                            $data_pasien['kelas_ditagihkan'] = strtoupper($data_pasien['kelas_ditagihkan_nama']);
                        }
                    }
                }

                $cache->set('pasien-pendaftaran-id-' . $pendId, $data_pasien, 3600);
            } else {
                $data_pasien = isset($cacheData) ? $cacheData : [];
            }
        } catch (RequestException $e) {
            $data_pasien = [];
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t("fe", "Tidak Ada Data Pasien"));
        } catch (Exception $e) {
            $data_pasien = [];
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t("fe", "Tidak Ada Data Pasien"));
        }
        $this->_data_pasien = !empty($data_pasien) ? $data_pasien : [];
        $this->_restGeneralRanap = $this->_restRanap;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    /*=========================================
    =            list pasien ranap            =
    =========================================*/

    public function actionIndex()
    {
        try {
            $response = $this->_restRanap->get('allow/get-api');
            $resResponseBody = json_decode($response->getBody(), True)['response']['master'];
            $body = json_decode($response->getBody(), TRUE);

            $getcarabayar = isset($resResponseBody['carabayar']) ? $resResponseBody['carabayar'] : [];
            $listcarabayar = ArrayHelper::map($getcarabayar, 'carabayar_id', 'carabayar_nama');

            $getpenjamin = isset($resResponseBody['penjamin']) ? $resResponseBody['penjamin'] : [];
            $listpenjamin = ArrayHelper::map($getpenjamin, 'penjamin_id', 'penjamin_nama');

            $getruangan = isset($resResponseBody['ruangan']) ? $resResponseBody['ruangan'] : [];
            $listruangan = ArrayHelper::map($getruangan, 'ruangan_id', 'ruangan_nama');

            $getkelaspelayanan = isset($resResponseBody['kelaspelayanan']) ? $resResponseBody['kelaspelayanan'] : [];
            $listkelaspelayanan = ArrayHelper::map($getkelaspelayanan, 'kelaspelayanan_id', 'kelaspelayanan_nama');

            $resResponselist_dokter = json_decode($response->getBody(), True)['response']['list_dokter'];
            $getlist_dokter = isset($resResponselist_dokter) ? $resResponselist_dokter : [];

            $resResponsejeniskasuspenyakit = json_decode($response->getBody(), True)['response']['jenis_kasus_penyakit'];
            $getjeniskasuspenyakit = isset($resResponsejeniskasuspenyakit) ? $resResponsejeniskasuspenyakit : [];

            $data_kelaspelayanan = isset($resMaster['kelaspelayanan']) && is_array($resMaster['kelaspelayanan']) ? $resMaster['kelaspelayanan'] : [];
            $resLookup = $body['response']['lookup'];

            $lookup_kelas_bpjs = isset($resLookup['kelas_bpjs']) && is_array($resLookup['kelas_bpjs']) ? $resLookup['kelas_bpjs'] : [];
            $_isPeriksa = DocoConstants::STAT_PERIKSA;
            $title = Yii::t('fe', 'Informasi Pasien Rawat Inap');
            $all_ruangan = false;
            $statusPasien = [
                1 => 'Pasien Konsul',
                2 => 'Pasien Non Konsul',
                3 => 'Pasien Titipan',
                4 => 'Pasien Stop Akomodasi'
            ];

            foreach ($getcarabayar as $key => $value) {
                $legend_cara_bayar[$key]['carabayar_nama'] = $value['carabayar_nama'];
                $legend_cara_bayar[$key]['carabayar_kode_warna'] = $value['carabayar_kode_warna'];
            }

            return $this->render('index', get_defined_vars());
        } catch (RequestException $e) {
            $this->logError($e);
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionListPasienSaya()
    {
        try {
            $response = $this->_restRanap->get('allow/get-api');
            $resResponseBody = json_decode($response->getBody(), True)['response']['master'];
            $body = json_decode($response->getBody(), TRUE);

            $getcarabayar = isset($resResponseBody['carabayar']) ? $resResponseBody['carabayar'] : [];
            $listcarabayar = ArrayHelper::map($getcarabayar, 'carabayar_id', 'carabayar_nama');

            $getpenjamin = isset($resResponseBody['penjamin']) ? $resResponseBody['penjamin'] : [];
            $listpenjamin = ArrayHelper::map($getpenjamin, 'penjamin_id', 'penjamin_nama');

            $getruangan = isset($resResponseBody['ruangan']) ? $resResponseBody['ruangan'] : [];
            $listruangan = ArrayHelper::map($getruangan, 'ruangan_id', 'ruangan_nama');

            $getkelaspelayanan = isset($resResponseBody['kelaspelayanan']) ? $resResponseBody['kelaspelayanan'] : [];
            $listkelaspelayanan = ArrayHelper::map($getkelaspelayanan, 'kelaspelayanan_id', 'kelaspelayanan_nama');

            $resResponselist_dokter = json_decode($response->getBody(), True)['response']['list_dokter'];
            $getlist_dokter = isset($resResponselist_dokter) ? $resResponselist_dokter : [];

            $resResponsejeniskasuspenyakit = json_decode($response->getBody(), True)['response']['jenis_kasus_penyakit'];
            $getjeniskasuspenyakit = isset($resResponsejeniskasuspenyakit) ? $resResponsejeniskasuspenyakit : [];
            $data_ruangan = isset($resMaster['ruangan']) && is_array($resMaster['ruangan']) ? $resMaster['ruangan'] : [];
            $data_kelaspelayanan = isset($resMaster['kelaspelayanan']) && is_array($resMaster['kelaspelayanan']) ? $resMaster['kelaspelayanan'] : [];

            $resLookup = $body['response']['lookup'];
            $lookup_kelas_bpjs = isset($resLookup['kelas_bpjs']) && is_array($resLookup['kelas_bpjs']) ? $resLookup['kelas_bpjs'] : [];
            $_isPeriksa = DocoConstants::STAT_PERIKSA;
            $title = Yii::t('fe', 'Informasi Pasien Rawat Inap') . ' - ' . Yii::t('fe', 'Pasien saya');
            $all_ruangan = true;
            $statusPasien = [
                1 => 'Pasien Konsul',
                2 => 'Pasien Non Konsul',
                3 => 'Pasien Titipan',
                4 => 'Pasien Stop Akomodasi'
            ];

            foreach ($getcarabayar as $key => $value) {
                $legend_cara_bayar[$key]['carabayar_nama'] = $value['carabayar_nama'];
                $legend_cara_bayar[$key]['carabayar_kode_warna'] = $value['carabayar_kode_warna'];
            }

            return $this->render('index', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        $all_ruangan = $request->get('all_ruangan');
        $idR = null;
        if (!$all_ruangan) {
            $idR = Yii::$app->docoVars->workspace("ruangan_id");
        }

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        $counter = 0;

        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
        $kelompokpegawai_id = Yii::$app->docoVars->user('kelompokpegawai_id');

        try {
            $response = $this->_restRanap->get('inf-pasien-ranap/index?idruangan=' . $idR . '&' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start', 1);
            foreach ($body['response']["data"] as $key => $value) {
                $no++;
                $primary = json_encode($value['pendaftaran_id']);
                $value['primary'] = DocoHelpers::encrypt($primary);
                $isTitip = false;
                $statusTitip = '-';
                $status_kamar = '-';
                if ($value['carabayar_id'] == 6) {
                    $status_kamar = 'Sesuai Kelas';
                    if ($value['klsrawat'] != null) {
                        if ($value['is_aps'] == true) {
                            if ($value['bpjs_kelas'] != null) {
                                if ($value['kelaspelayanan_id'] < $value['klsrawat']) {
                                    $status_kamar = 'APS / Naik kelas';
                                } elseif ($value['kelaspelayanan_id'] > $value['klsrawat']) {
                                    $status_kamar = 'APS / Turun kelas';
                                }
                            } else {
                                $status_kamar = 'APS / Naik kelas';
                            }
                        } elseif ($value['is_pasientitipan'] == true) {
                            if ($value['bpjs_kelas'] != null) {
                                if ($value['kelaspelayanan_id'] < $value['klsrawat']) {
                                    $status_kamar = 'Titipan / Naik kelas';
                                } elseif ($value['kelaspelayanan_id'] > $value['klsrawat']) {
                                    $status_kamar = 'Titipan / Turun kelas';
                                }
                            } else {
                                $status_kamar = 'Titipan / Naik kelas';
                            }
                        }
                    }
                } else  if (!empty($value['is_pasientitipan_pk'])) {
                    if ($value['is_pasientitipan_pk'] == true && $value['is_stoppasientitipan'] == false) {
                        $isTitip = true;
                        $statusTitip = $value['kelas_ditagihkan_nama'];
                        // $value['kelas_pelayanan'] = $value['kelas_ditagihkan_nama'];
                    }
                } else if (empty($value['is_pasientitipan_pk'])) {
                    if ($value['is_pasientitipan'] == true && $value['is_stoppasientitipan'] == false) {
                        $isTitip = true;
                        $statusTitip = $value['kelas_ditagihkan_nama'];
                        // $value['kelas_pelayanan'] = $value['kelas_ditagihkan_nama'];
                    }
                }

                $value['status_kamar'] = $status_kamar;
                $value['rowNum'] = $no;
                $data[$key] = $value;
                $data[$counter]['gab_noRmPdft'] = strtoupper($value['nama_pasien']) . ' (' . substr($value['jenis_kelamin'], 0, 1) . ')<br>No. Registrasi : ' . $value['no_pendaftaran'] . '<br>No. RM : ' . $value['no_rekam_medik'];
                $data[$counter]['noRuangannya'] = $value['ruangan_nama'] . " <br> " . $value['kamarruangan_nokamar'] . " - " . $value['no_tempattidur'];
                $data[$counter]['hakKelas'] = $value['hak_kelas'] . " / " . $value['kelas_pelayanan'] . " / " . $statusTitip;
                $data[$counter]['carBay'] = 'Cara Bayar : ' . $value['carabayar_nama'] . '<br>Penjamin : ' . $value['penjamin_nama'];
                $data[$counter]['tgl_admisi_format'] = date('d-m-Y H:i:s', strtotime($value['tgl_admisi']));
                $data[$counter]['tgl_pindahkamar_format'] = $value['tgl_pindahkamar'] ? date('d-m-Y H:i:s', strtotime($value['tgl_pindahkamar'])) : '';
                $data[$counter]['rencana_pulang_format'] = $value['rencana_pulang'] ? date('d-m-Y H:i:s', strtotime($value['rencana_pulang'])) : '';
                $date1 = date_create(date('Y-m-d', strtotime($value['tgl_admisi'])));
                $date2 = date_create(date('Y-m-d'));
                $diff = date_diff($date1, $date2);
                $data[$counter]['is_titip'] = $isTitip;
                $data[$counter]['hariRawat'] = $diff->format('%a') + 1;
                $data[$counter]['is_konsul'] = false;
                // $data[$counter]['is_konsul'] =
                //     $value['dokter_admisi_id'] != $pegawai_id &&
                //     $kelompokpegawai_id == DocoConstants::KELOMPOK_MEDIS &&
                //     $idR == null
                //     ? true
                //     : false;
                if( ($value['jenis_konsul'] == 434 || $value['jenis_konsul'] == 435) && $value['status_konsul'] == 437 ) {
                    $data[$counter]['is_konsul'] = true;
                }

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

    public function actionGetNoPendaftaran()
    {
        try {
            if (isset($_GET['q']['term']) && !empty($_GET['q']['term'])) {
                $tgl_pendaftaran = '';
                $idR = Yii::$app->docoVars->workspace("ruangan_id");
                if (isset($_GET['z'])) {
                    $tgl_pendaftaran = $_GET['z'];
                }
                $response = $this->_restRanap->request('POST', 'inf-pasien-ranap/data-pendaftaran', [
                    'form_params' => ['term' => $_GET['q']['term'], 'date' => $tgl_pendaftaran, 'idR' => $idR],
                ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id' => $value['no_pendaftaran'], 'text' => $value['no_pendaftaran']];
                }
                $total = count($body['response']);
                $return = ['result' => $data, 'total_count' => $total, 'incomplete_results' => false];
                return DocoHelpers::response($return);
            }
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionGetNoRekamMedik()
    {
        try {
            if (isset($_GET['q']['term']) && !empty($_GET['q']['term'])) {
                $tgl_pendaftaran = '';
                $idR = Yii::$app->docoVars->workspace("ruangan_id");
                if (isset($_GET['z'])) {
                    $tgl_pendaftaran = $_GET['z'];
                }
                $response = $this->_restRanap->request('POST', 'inf-pasien-ranap/data-rekam-medik', [
                    'form_params' => ['term' => $_GET['q']['term'], 'date' => $tgl_pendaftaran, 'idR' => $idR],
                ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id' => $value['no_rekam_medik'], 'text' => $value['no_rekam_medik']];
                }
                $total = count($body['response']);
                $return = ['result' => $data, 'total_count' => $total, 'incomplete_results' => false];
                return DocoHelpers::response($return);
            }
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionGetPasien($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];

        try {
            $response = $this->_restRanap->get('inf-pasien-ranap/data-nama-pasien?advanced-filter[nama_pasien]=' . $q);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value)
                $result['results'][] = [
                    'id' => $value['nama_pasien'],
                    'text' => $value['nama_pasien']
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

    public function actionGetDokter($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];

        try {
            $response = $this->_restRanap->get('inf-pasien-ranap/data-nama-dokter?advanced-filter[dokter_admisi]=' . $q);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value)
                $result['results'][] = [
                    'id' => $value['dokter_admisi'],
                    'text' => $value['dokter_admisi']
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

    public function actionGetCarabayar($assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $params = '';
        if ($request->post()) {
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
            $params = '?advanced-filter[penjamin_m.penjamin_id]=' . $parent_label;
        }

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restRanap->get('cara-bayar' . $params);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value)
                $result['output'][] = [
                    'id' => $assign_id ? $value['carabayar_id'] : $value['carabayar_nama'],
                    'name' => $value['carabayar_nama']
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

    public function actionGetPenjamin($assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restRanap->get('penjamin?advanced-filter[carabayar_m.carabayar_id]=' . $parent_label);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value)
                $result['output'][] = [
                    'id' => $assign_id ? $value['penjamin_id'] : $value['penjamin_nama'],
                    'name' => $value['penjamin_nama']
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

    public function actionGetKasusPenyakit($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];

        try {
            $response = $this->_restRanap->get('inf-pasien-ranap/data-kasus-penyakit?advanced-filter[jeniskasuspenyakit_nama]=' . $q);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value)
                $result['results'][] = [
                    'id' => $value['jeniskasuspenyakit_nama'],
                    'text' => $value['jeniskasuspenyakit_nama']
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

    public function actionGetHakKelas($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];

        try {
            $response = $this->_restRanap->get('inf-pasien-ranap/data-hak-kelas?advanced-filter[hak_kelas]=' . $q);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value)
                $result['results'][] = [
                    'id' => $value['hak_kelas'],
                    'text' => $value['hak_kelas']
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

    public function actionGetKelasSaatIni($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];

        try {
            $response = $this->_restRanap->get('inf-pasien-ranap/data-kelas-saat-ini?advanced-filter[kelas_pelayanan]=' . $q);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value)
                $result['results'][] = [
                    'id' => $value['kelas_pelayanan'],
                    'text' => $value['kelas_pelayanan']
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

    public function actionGetNamaRuangan($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];

        try {
            $response = $this->_restRanap->get('inf-pasien-ranap/data-nama-ruangan?advanced-filter[ruangan_nama]=' . $q);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value)
                $result['results'][] = [
                    'id' => $value['ruangan_nama'],
                    'text' => $value['ruangan_nama']
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

    public function actionExportPdf($jenis)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $all_ruangan = $request->get('all_ruangan');
        $idR = null;
        if (!$all_ruangan) {
            $idR = Yii::$app->docoVars->workspace("ruangan_id");
        }
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if (isset($yiiRestfulParams['advanced-filter']['tgl_admisi'])) {
            $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_admisi']);
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_akhir = $tgl_pendaftaran_range[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $yiiRestfulParams['advanced-filter']['tgl_admisi_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_admisi_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_admisi']);
        }
        $path = Yii::getAlias("@download") . "/informasi-pasien-ranap.pdf";
        try {
            $response = $this->_restRanap->get('inf-pasien-ranap/export-pdf?jenis=' . $jenis . '&ruangan=' . Yii::$app->docoVars->workspace("ruangan_id") . '&idruangan=' . $idR . '&' . http_build_query($yiiRestfulParams), [
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

    public function actionExportRincianTagihanPdf($pendaftaran_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        if (!is_numeric($pendaftaran_id)) {
            $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        }
        $path = Yii::getAlias("@download") . "/rincian-tagihan-pasien-ranap.pdf";
        $response = $this->_restRanap->get('inf-pasien-ranap/exp-rincian-tagihan-ranap?pendaftaran_id=' . $pendaftaran_id, [
            'save_to' => $path,
        ]);
        $body = json_decode($response->getBody(), true);
        return DocoHelpers::previewPdf($path);
    }

    public function actionExportExcel($jenis)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $all_ruangan = $request->get('all_ruangan');
        $idR = null;
        if (!$all_ruangan) {
            $idR = Yii::$app->docoVars->workspace("ruangan_id");
        }
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if (isset($yiiRestfulParams['advanced-filter']['tgl_admisi'])) {
            $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_admisi']);
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_akhir = $tgl_pendaftaran_range[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $yiiRestfulParams['advanced-filter']['tgl_admisi_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_admisi_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_admisi']);
        }
        try {
            $path = Yii::getAlias("@download") . "/informasi-pasien-ranap.xlsx";
            $query = [
                'jenis' => $jenis,
                'ruangan' => Yii::$app->docoVars->workspace("ruangan_id"),
                'idruangan' => $idR
            ];
            $query = array_merge($query, $yiiRestfulParams);

            $response = $this->_restRanap->get('inf-pasien-ranap/export-excel', [
                'query' => $query,
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

    public function actionAksiBatal($id)
    {
        try {
            $title = 'Batal Rawat';
            $model = new InfoPasienRanapForm;
            $modelBatal = new PasienBatalPeriksaForm;
            $request = Yii::$app->request;
            $pendaftaran_id = json_decode(DocoHelpers::decrypt($id));
            if ($request->post()) {
                $pasienadmisi_id = $request->post('pasienadmisi_id');
                $model->attributes = $request->post('InfoPasienRanapForm');
                $modelBatal->attributes = $request->post('PasienBatalPeriksaForm');
                $modelBatal->pendaftaran_id = $pendaftaran_id;
                $modelBatal->pasienadmisi_id = $pasienadmisi_id;
                $modelBatal->tgl_batal = $model->tgl_admisi;

                if ($modelBatal->validate()) {
                    $response = $this->_restRanap->request('POST', 'inf-pasien-ranap/batal-periksa', [
                        'query' => ['id' => $pasienadmisi_id],
                        'form_params' => $modelBatal->attributes
                    ]);
                    $response = json_decode($response->getBody(), true);
                    return DocoHelpers::response($response, false, true);
                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'PasienBatalPeriksaForm');
                    return DocoHelpers::response([
                        'response' => [
                            'data' => $errors
                        ]
                    ], 422);
                }
            } else {
                $result = $this->find($pendaftaran_id);
                if (isset($result['response'])) {
                    $model->attributes = $result['response'];
                    $model->tgl_admisi = date('d M Y', strtotime($model->tgl_admisi));
                    return $this->renderAjax('form', get_defined_vars());
                }
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    public function dataInstruksiTindakan($pendaftaran_id = "")
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $response = $this->_restRanap->get(
                'inf-pasien-ranap/data-instruksi-tindakan',
                [
                    'query' => ['pendaftaran_id' => $pendaftaran_id]
                ]
            );
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function find($id = "")
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $response = $this->_restRanap->request(
                'GET',
                'inf-pasien-ranap/view-data',
                [
                    'query' => ['id' => $id]
                ]
            );
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function actionGetDataKondisiKeluar($carakeluar_id)
    {
        try {
            $data = [];
            if (isset($carakeluar_id)) {
                $response = $this->_restRanap->get('inf-pasien-ranap/data-kondisi-keluar?carakeluar_id=' . $carakeluar_id);
                $body = json_decode($response->getBody(), True);
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id' => $value['kondisikeluar_id'], 'text' => $value['kondisikeluar_nama']];
                }
                $total = count($body['response']);
                $return = ['result' => $data];
                return DocoHelpers::response($return);
            }
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionPindahKamar($id)
    {
        try {
            $title = Yii::t('fe', 'Pindah kamar');
            $pendaftaran_id = DocoHelpers::decrypt($id);

            $req = $this->_restRanap->get('allow/get-api-pindah-kamar?id=' . $pendaftaran_id);
            $response = json_decode($req->getBody(), true);
            $response = $response['response'];

            $data_pasien = $response['data_pasien'];
            $data_master = $response['data_master'];

            $masterWarnaTempatTidur = Yii::$app->cache->getOrSet("warna-tempat-tidur", function () {
                $masterWarnaTempatTidur = $this->_restPendaftaran->get(
                    'allow-antrian/get-warna-tempat-tidur',
                    [
                        'query' => [],
                    ]
                );
                return json_decode($masterWarnaTempatTidur->getBody(), true)['response']['warna_tempat_tidur'];
            });

            $model = new PindahKamarForm;
            $model->pendaftaran_id = $data_pasien['pendaftaran_id'];
            $model->pasienadmisi_id = $data_pasien['pasienadmisi_id'];

            return $this->render('pindah_kamar', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    public function actionSavePindahKamar()
    {
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $model = new PindahKamarForm;
        $model->load($request->post());
        $pendaftaran_id = DocoHelpers::encrypt($model->pendaftaran_id);
        $model->tgl_pindahkamar = date('Y-m-d H:i:s'); // unused, override in backend after

        if ($model->validate()) {
            $req = $this->_restRanap->post('inf-pasien-ranap/proses-pindah-kamar', [
                'form_params' => $model->attributes
            ]);
            $response = json_decode($req->getBody(), true);
            $response = $response['response'];
            $removeSession = $this->removeSession($model->pendaftaran_id);
            return DocoHelpers::response($response);
        } else {
            $errors = DocoHelpers::parseError($model->errors, 'PindahKamarForm');
            return DocoHelpers::response([
                'response' => [
                    'data' => $errors
                ]
            ], 422);
        }
    }

    public function actionPindahKamarOld($id)
    {
        try {
            $title = 'Pindah Kamar';
            $model = new InfoPasienRanapForm;
            $modelValid = new InfoPasienRanap;
            $request = Yii::$app->request;
            $pendaftaran_id = json_decode(DocoHelpers::decrypt($id));
            if ($request->post()) {
                $model->load($request->post());
                $modelValid->load($request->post());
                $modelValid->tgl_pindahkamar = $model->tgl_pindahkamar;
                $modelValid->ruangan_id = $model->ruangan_id;
                if ($modelValid->ruangan_nama) {
                    $model->ruangan_nama = $modelValid->ruangan_nama;
                }
                // print_r($modelValid); die;
                if ($modelValid->validate()) {
                    $dataPost = $request->post('InfoPasienRanapForm');
                    $model->tgl_pindahkamar = date('Y-m-d H:i:s', strtotime($model->tgl_pindahkamar));
                    $model->tgl_admisi = date('Y-m-d H:i:s', strtotime($model->tgl_admisi));
                    $model->tanggal_lahir = date('Y-m-d', strtotime($model->tanggal_lahir));

                    $response = $this->_restRanap->request('POST', 'inf-pasien-ranap/proses-pindah-kamar', [
                        'form_params' => $model->attributes
                    ]);
                    $response = json_decode($response->getBody(), true);
                    return DocoHelpers::response($response, false, true);
                } else {
                    $errors = $modelValid->getErrors();
                    return DocoHelpers::response([
                        'response' => [
                            'data' => $errors
                        ]
                    ], 422, 'InfoPasienRanap');
                }
            } else {
                $result = $this->find($pendaftaran_id);
                // Get all request
                $allReq = $this->_restRanap->get('inf-pasien-ranap/get-bundle-data?id=' . $pendaftaran_id);
                $bodydata = json_decode($allReq->getBody(), true);
                $ddlRuangan = $bodydata['response']['list-ruangan'];

                if (isset($result['response'])) {
                    $model->attributes = $result['response'];
                    $model->tgl_admisi = date('d F Y H:i', strtotime($model->tgl_admisi));
                    $model->tanggal_lahir = date('d F Y', strtotime($model->tanggal_lahir));
                    $model->rencana_pulang = $model->ruangan_nama . " - " . $model->kamarruangan_nokamar . " - " . $model->no_tempattidur;
                    $model->tgl_pindahkamar = date('d-m-Y H:i');
                    return $this->render('pindah', get_defined_vars());
                }
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    private function prosesPulangPasien($data)
    {
        $response = $this->_restRanap->request('POST', 'inf-pasien-ranap/proses-pulang-pasien', [
            'form_params' => $data
        ]);
        $response = json_decode($response->getBody(), true);
        return DocoHelpers::response($response, false, true);
    }

    public function actionPasienPulang($id)
    {
        $cache = Yii::$app->cache;
        try {
            $title = 'Pasien Pulang';
            $model = new InfoPasienRanapForm;
            $data_pasien = $this->_data_pasien;
            $no_identitas_pasien = ArrayHelper::getValue($data_pasien, 'icare_identifier');
            $modelValid = new InfoPasienRanap;
            $modelPulang = new PasienPulangForm;
            $mPelayananJenazah = new PelayananJenazahForm;
            // $modelrujukan = new PasienDirujukKeluarForm;
            $rujukanPulangForm = new RujukanPulangForm;
            $request = Yii::$app->request;
            $pendaftaran_id = json_decode(DocoHelpers::decrypt($id));
            $formName = substr(strrchr(get_class($modelPulang), "\\"), 1);

            if ($request->post()) {
                $reqPost = $request->post();
                $reqPost['PasienPulangForm']['user'] = Yii::$app->docoVars->user("nama");
                $modelPulang->load($reqPost);

                if (isset($reqPost['PasienPulangForm']['is_meninggal']) && $reqPost['PasienPulangForm']['is_meninggal'] == 1) {
                    if (empty($reqPost['PasienPulangForm']['tgl_meninggal'])) {
                        $modelPulang->addError('tgl_meninggal', 'Tanggal Belum Diisi');
                        $errors = DocoHelpers::parseError($modelPulang->errors, 'PasienPulangForm');
                        return DocoHelpers::response([
                            'response' => [
                                'data' => $errors
                            ]
                        ], 422);
                    }
                    if (empty($reqPost['PasienPulangForm']['no_surat_kematian'])) {
                        $modelPulang->addError('no_surat_kematian', 'Nomor surat kematian Belum Diisi');
                        $errors = DocoHelpers::parseError($modelPulang->errors, 'PasienPulangForm');
                        return DocoHelpers::response([
                            'response' => [
                                'data' => $errors
                            ]
                        ], 422);
                    }
                }

                /* added condition while pelayanan jenazah is active */
                if (isset($reqPost['PelayananJenazahForm'])) {
                    $mPelayananJenazah->load($reqPost);
                    if (!$mPelayananJenazah->validate()) {
                        $errors = DocoHelpers::parseError($mPelayananJenazah->errors, 'PelayananJenazahForm');
                        return DocoHelpers::response([
                            'response' => [
                                'data' => $errors
                            ]
                        ], 422);
                    }
                    $listOrderJenazah = $listAlatJenazah = [];
                    if ($cache->get($id . '-tindakan')) {
                        $listOrderJenazah['tindakan'] = $cache->get($id . '-tindakan');
                    }
                    if ($cache->get($id . '-obat')) {
                        $listOrderJenazah['obat'] = $cache->get($id . '-obat');
                    }
                    if ($cache->get($id . '-linen')) {
                        $listAlatJenazah['linen'] = $cache->get($id . '-linen');
                    }
                    if ($cache->get($id . '-alat')) {
                        $listAlatJenazah['alat'] = $cache->get($id . '-alat');
                    }
                    $reqPost['list_jenazah_order'] = $listOrderJenazah;
                    $reqPost['list_jenazah_alat'] = $listAlatJenazah;
                }

                if (isset($reqPost['RujukanPulangForm'])) {
                    $rujukanPulangForm->load($reqPost);
                    $jam = !empty($reqPost['jam_rujukan']) ? $reqPost['jam_rujukan'] : null;
                    $rujukanPulangForm->tanggal_rujukan = !empty($rujukanPulangForm->tanggal_rujukan) ? date('Y-m-d H:i:s', strtotime("$rujukanPulangForm->tanggal_rujukan $jam")) : null;
                    if (!$rujukanPulangForm->validate()) {
                        $errors = DocoHelpers::parseError($rujukanPulangForm->errors, 'RujukanPulangForm');
                        return DocoHelpers::response([
                            'response' => [
                                'data' => $errors
                            ]
                        ], 422);
                    }
                }
                if ($modelPulang->pasiendirujukkeluar_id == 1) {
                    $modelPulang->scenario = PasienPulangForm::SCENARIO_PASIENDIRUJUK;
                    if (!$modelPulang->validate()) {
                        $errors = DocoHelpers::parseError($modelPulang->errors, 'PasienPulangForm');
                        return DocoHelpers::response([
                            'response' => [
                                'data' => $errors
                            ]
                        ], 422);
                    } else {
                        $response = $this->_restRanap->request('POST', 'inf-pasien-ranap/proses-pulang-pasien', [
                            'form_params' => $reqPost
                        ]);
                        $response = json_decode($response->getBody(), true);
                        return $this->redirect(['ranap/inf-pasien-ranap']);
                    }
                } else {
                    $modelPulang->scenario = PasienPulangForm::SCENARIO_PASIENPULANG;
                    if (!$modelPulang->validate()) {
                        $errors = DocoHelpers::parseError($modelPulang->errors, 'PasienPulangForm');
                        return DocoHelpers::response([
                            'response' => [
                                'data' => $errors
                            ]
                        ], 422);
                    } else {
                        $response = $this->_restRanap->request('POST', 'inf-pasien-ranap/proses-pulang-pasien', [
                            'form_params' => $reqPost
                        ]);
                        $response = json_decode($response->getBody(), true);
                        if ($response['metadata']['status'] == 200) {
                            $this->actionResetCacheJenazah($id, 'all');
                        }
                        return DocoHelpers::response($response, false, true);
                    }
                }
            } else {
                $result = $this->find($pendaftaran_id);
                $dataInstruksiTindakan = $this->dataInstruksiTindakan($pendaftaran_id);
                $getdataInstruksiTindakan = $dataInstruksiTindakan['response'];

                if (!empty($getdataInstruksiTindakan)) {
                    foreach ($getdataInstruksiTindakan as $key => $value) {
                        if ($value['tindakan_deleted'] == true) {
                            unset($getdataInstruksiTindakan[$key]);
                        }

                        /*if ($value['tindakan_deleted'] == true) {
                            unset($getdataInstruksiTindakan[$key]);
                            $statusInstruksiTindakan = false;
                        }else{
                            $statusInstruksiTindakan = true;
                        }*/

                        /*if (($value['grouping_tipe'] == 'PENUNJANG') && ($value['instruksi_deleted'] == true)) {
                            $statusInstruksiTindakan = false;
                        }else{
                            $statusInstruksiTindakan = true;
                        }

                        if (($value['grouping_tipe'] == 'TINDAKANBMHP') && ($value['tindakan_deleted'] == true)) {
                            $statusInstruksiTindakan = false;
                        }else{
                            $statusInstruksiTindakan = true;
                        }*/
                    }
                }

                $statusInstruksiTindakan = !empty($getdataInstruksiTindakan) ? true : false;
                $messageInstruksiTindakan = ' - ' . Yii::t('fe', 'Masih ada instruksi yang belum diimplementasikan');

                $allReq = $this->_restRanap->get('inf-pasien-ranap/get-bundle-data?id=' . $pendaftaran_id);
                $bodydata = json_decode($allReq->getBody(), true);

                $ddlRuangan = $bodydata['response']['list-ruangan'];
                $cara_keluar = $bodydata['response']['list-carakeluar'];
                $kondisi_keluar = $bodydata['response']['list-kondisikeluar'];
                $pegawai = $bodydata['response']['list-pegawai'];
                $rujukan_keluar = $bodydata['response']['list-rujukan'];
                $jeniskelamin = $bodydata['response']['list-jeniskelamin'];
                $hubkeluarga = $bodydata['response']['list-hubungankeluarga'];
                $ruangan_asal = $ddlRuangan;
                if (isset($result['response'])) {
                    $model->attributes = $result['response'];
                    $modelPulang->pasien_id = $result['response']['pasien_id'];
                    $model->tgl_pendaftaran = $result['response']['tgl_pendaftaran'];
                    $tgl_admisi_x = $model->tgl_admisi;
                    $tglpasienpulang_x = date('m/d/Y');
                    $model->tgl_admisi = date('d-m-Y H:i:s', strtotime($model->tgl_admisi));
                    $model->tgl_pendaftaran = date('d-m-Y H:i:s', strtotime($model->tgl_pendaftaran));
                    $model->tanggal_lahir = date('d-m-Y', strtotime($model->tanggal_lahir));
                    $model->rencana_pulang = $model->ruangan_nama . " - " . $model->kamarruangan_nokamar . " - " . $model->no_tempattidur;
                    $modelPulang->tglpasienpulang = date('d-m-Y H:i:s');

                    $startDateAdmisi = new \DateTime(date('Y-m-d', strtotime($model->tgl_admisi)));
                    $endDatePulang = new \DateTime(date('Y-m-d'));
                    $diffDate = $startDateAdmisi->diff($endDatePulang);

                    $daysLamaRawat = ($diffDate->days == 0) ?  1 : $diffDate->days + 1;

                    $modelPulang->ruanganasal_id = $result['response']['ruangan_id'];

                    return $this->render('pulang', get_defined_vars());
                }
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    public function actionPilihTempatTidur()
    {
        $request = Yii::$app->request;
        $title = Yii::t('fe', 'Pilih Tempat Tidur');
        $ruangan_id = $request->get('ruangan_id');
        $jenis_id = $request->get('jenis_id');
        $kelas_id = $request->get('kelas_id');

        $masterWarnaTempatTidur = $this->_restRanap->get(
            'allow/get-warna-tempat-tidur',
            [
                'query' => []
            ]
        );
        $body = json_decode($masterWarnaTempatTidur->getBody(), TRUE);
        $getWarnaTempatTidur = $body['response'];

        return $this->renderAjax('_pemilihan_tempat_tidur', get_defined_vars());
    }

    public function actionCekKamarFleksibel($kamarruangan_id)
    {
        try {
            $response = $this->_restRanap->request('GET', 'inf-pasien-ranap/cek-kamar-fleksibel?kamarruangan_id=' . $kamarruangan_id);
            $body = json_decode($response->getBody(), TRUE);

            return DocoHelpers::response($body);
        } catch (RequestException $e) {
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionGetDataKamar()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $jenis_id = $request->get('jenis_id');
        $kelas_id = $request->get('kelas_id');
        $ruangan_id = $request->get('ruangan_id');
        $draw = $request->get('draw', 1);
        $data = [];
        try {
            $response = $this->_restRanap->get('inf-pasien-ranap/get-data-kamar', [
                'form_params' => [],
                'query' => ['jeniskasuspenyakit_id' => $jenis_id, 'kelaspelayanan_id' => $kelas_id, 'ruangan_id' => $ruangan_id]
            ]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            $result['data'] = $this->listRuangan($body['response']['list-ruangan'], $body['response']['data']);
            $result['recordsTotal'] = '';
            $result['recordsFiltered'] = '';
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function listRuangan($index, $data)
    {
        function listKamar($id, $no_kamar, $data)
        {
            $buttonList = [];
            $i = 1;
            foreach ($data as $key => $value) {
                if ($value['ruangan_id'] == $id && $value['kamarruangan_nokamar'] == $no_kamar) {
                    $attributes = [
                        'style' => "margin-bottom:8px;background-color:{$value['kode_warna']}",
                        'data-kettempattidur_id' => $value['kettempattidur_id'],
                        'data-kamartempattidur_id' => $value['kamartempattidur_id'],
                        'data-kamarruangan_jenis' => $value['kamarruangan_jenis'],
                        'data-kamarruangan_id' => $value['kamarruangan_id'],
                        'data-no_tempattidur' => $value['no_tempattidur'],
                        'data-kamarruangan_nokamar' => $value['kamarruangan_nokamar'],
                        'class' => 'pilih-kamar btn btn-danger-custom btn-xs',
                        'onClick' => 'pilihKamar(this)'
                    ];
                    if (($value['kettempattidur_id'] == DocoConstants::ISI_PRMPN) or ($value['kettempattidur_id'] == DocoConstants::ISI_LAKI) or ($value['kettempattidur_id'] == DocoConstants::DIPESAN)) {
                        $attributes['disabled'] = 'disabled';
                    }

                    $buttonLabel = $value['no_tempattidur'];
                    $buttonList[$value['no_tempattidur'] . $value['kamartempattidur_id']] = Html::buttonInput($buttonLabel, $attributes);
                }
                $i++;
            }
            if (count($buttonList) == 0) {
                $buttonList[] = "<p>--Tempat Tidur Tidak Tersedia--</p>";
            } else {
                ksort($buttonList);
            }

            return implode(' ', $buttonList);
        }

        $data_kamar = [];
        $no = 1;
        foreach ($index as $key => $value) {
            $value['rowNum'] = $no;
            $value['datakamar'] = listKamar($value['ruangan_id'], $value['kamarruangan_nokamar'], $data);
            $data_kamar[$key] = $value;
            $no++;
        }
        return $data_kamar;
    }

    public function actionPeriksa($id)
    {
        try {
            $status_ranap = Yii::$app->request->get('status_ranap', null);
            if ($status_ranap != 441) {
                $response = $this->_restRanap->request(
                    'GET',
                    'inf-pasien-ranap/update-status-periksa',
                    [
                        'query' => ['id' => $id]
                    ]
                );
            }
            return $this->redirect(['pemeriksaan-rawat-inap/periksa?id=' . $id]);
        } catch (RequestException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function actionCariTindakanJenazah()
    {
        $request = Yii::$app->request;
        $page = $request->get('page');
        $response = [];
        $limit = 5;
        $offset = ($page - 1) * 5;
        try {
            $result = $this->_restRanap->get('inf-pasien-ranap/cari-tindakan-jenazah', [
                'query' => [
                    'ruangan_id' => DocoConstants::VAR_RUANGAN_JNZ,
                    'pendaftaran_id' => DocoHelpers::decrypt($request->get('id')),
                    'term' => $request->get('term', null),
                    'page' => $page,
                    'offset' => $offset,
                    'limit' => $limit
                ]
            ]);

            $result = json_decode($result->getBody(), true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['daftartindakan_id'],
                    'text' => $value['daftartindakan_nama'],
                    'tarif_satuan' => $value['harga_tariftindakan'],
                    'persen_cyto' => $value['persencyto_tindakan'],
                    'list_komponen' => $value['list_komponen']
                ];
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }

        return DocoHelpers::response([
            'result' => $response,
            'pagination' => ['more' => !empty($data) ? true : false]
        ]);
    }

    public function actionCariObatJenazah()
    {
        $request = Yii::$app->request;
        $page = $request->get('page');
        $response = [];
        $limit = 5;
        $offset = ($page - 1) * 5;
        $dataObat = [];
        try {
            $result = $this->_restRanap->get('inf-pasien-ranap/cari-obat-jenazah', [
                'query' => [
                    'ruangan_id' => DocoConstants::VAR_RUANGAN_JNZ,
                    'term' => $request->get('term', null),
                    'page' => $page,
                    'offset' => $offset,
                    'limit' => $limit
                ]
            ]);

            $result = json_decode($result->getBody(), true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['obatalkes_id'],
                    'text' => $value['obatalkes_nama'],
                    'harga' => $value['hargajual'],
                    'qty_tersedia' => $value['qty_tersedia'],
                    'harganetto' => $value['harganetto'],
                    'persendiscount' => $value['persendiscount'],
                    'persenppn' => $value['persenppn'],
                    'persenmargin' => $value['persenmargin'],
                    'jmldiscount' => $value['jmldiscount'],
                    'jmlmargin' => $value['jmlmargin'],
                    'jmlppn' => $value['jmlppn'],
                ];
                $dataObat[$value['obatalkes_id']] = $value;
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }

        return DocoHelpers::response([
            'result' => $response,
            'pagination' => ['more' => !empty($data) ? true : false],
            'dataObat' => $dataObat
        ]);
    }

    public function actionCariLinenJenazah()
    {
        $request = Yii::$app->request;
        $page = $request->get('page');
        $response = [];
        $limit = 5;
        $offset = ($page - 1) * 5;
        try {
            $result = $this->_restRanap->get('inf-pasien-ranap/cari-linen-jenazah', [
                'query' => [
                    'term' => $request->get('term', null),
                    'page' => $page,
                    'offset' => $offset,
                    'limit' => $limit
                ]
            ]);

            $result = json_decode($result->getBody(), true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['barang_id'],
                    'text' => $value['barang_nama'],
                ];
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }

        return DocoHelpers::response([
            'result' => $response,
            'pagination' => ['more' => !empty($data) ? true : false]
        ]);
    }

    public function actionCariAlatJenazah()
    {
        $request = Yii::$app->request;
        $page = $request->get('page');
        $response = [];
        $limit = 5;
        $offset = ($page - 1) * 5;
        try {
            $result = $this->_restRanap->get('inf-pasien-ranap/cari-alat-jenazah', [
                'query' => [
                    'term' => $request->get('term', null),
                    'page' => $page,
                    'offset' => $offset,
                    'limit' => $limit
                ]
            ]);

            $result = json_decode($result->getBody(), true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['obatalkes_id'],
                    'text' => $value['obatalkes_nama'],
                ];
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }

        return DocoHelpers::response([
            'result' => $response,
            'pagination' => ['more' => !empty($data) ? true : false]
        ]);
    }

    public function actionSaveCacheJenazah($id, $type)
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $cache = Yii::$app->cache;
        try {
            $listdata = [];
            $cacheName = $id . '-' . $type;
            $getCache = $cache->get($cacheName);
            if ($getCache) {
                $listdata = $getCache;
            }
            $post['is_deleted'] = false;
            $keychange = null;
            foreach ($listdata as $key => $value) {
                if ($type == 'tindakan') {
                    if ($value['daftartindakan_id'] == $post['daftartindakan_id']) {
                        $keychange = $key;
                    }
                }
                if ($type == 'obat') {
                    if ($value['obatalkes_id'] == $post['obatalkes_id']) {
                        $keychange = $key;
                    }
                }
                if ($type == 'linen' && ($value['barang_id'] == $post['barang_id'])) {
                    $keychange = $key;
                }
                if ($type == 'alat' && ($value['obatalkes_id'] == $post['obatalkes_id'])) {
                    $keychange = $key;
                }
            }
            if ($keychange === null) {
                $listdata[] = $post;
            } else {
                if ($type == 'tindakan') {
                    $listdata[$key]['qty_tindakan'] += $post['qty_tindakan'];
                    $listdata[$key]['tarif_tindakan'] = $listdata[$key]['qty_tindakan'] * $post['tarif_satuan'];
                }
                if ($type == 'obat') {
                    $listdata[$key]['qty'] += $post['qty'];
                    if ($listdata[$key]['qty'] > $listdata[$key]['qty_tersedia']) {
                        return DocoHelpers::responseTemplate(
                            422,
                            'Error',
                            [],
                            [
                                'title' => Yii::t('fe', 'Terjadi Kesalahan'),
                                'text' => 'Qty yang dipesan tidak boleh melebihi stok tersedia',
                                'message' => 'Qty yang dipesan tidak boleh melebihi stok tersedia',
                            ]
                        );
                    }
                    $listdata[$key]['obat_harga'] = $listdata[$key]['qty'] * $post['hargajual'];
                }
                if ($type == 'linen' || $type == 'alat') {
                    $listdata[$key]['qty'] += $post['qty'];
                }
            }
            $cache->set($cacheName, $listdata);
            return DocoHelpers::response(['title' => 'Berhasil', 'message' => 'Data Berhasil Disimpan']);
        } catch (Exception $e) {
            return DocoHelpers::response($e->getMessage(), 500);
        }
    }
    public function actionResetCacheJenazah($id, $type)
    {
        $cache = Yii::$app->cache;
        $cacheName = $id . '-' . $type;
        try {
            if ($type == 'all') {
                $cache->set($id . '-linen', []);
                $cache->set($id . '-obat', []);
                $cache->set($id . '-tindakan', []);
                $cache->set($id . '-alat', []);
            } else {
                $cache->set($cacheName, []);
            }
            return true;
        } catch (Exception $e) {
            return DocoHelpers::response($e->getMessage(), 500);
        }
    }

    public function actionGetCacheJenazah($cachetype, $id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $cache = Yii::$app->cache;
        try {
            $listdata = [];
            $cacheName = $id . '-' . $cachetype;
            $getCache = $cache->get($cacheName);
            if ($getCache) {
                $listdata = $getCache;
            }
            if (!$listdata) {
                $return = [
                    'data' => [],
                    'draw' => $request->post('draw'),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0
                ];
                return DocoHelpers::response($return);
            }
            $data_tables = [];
            $no = 1;
            foreach ($listdata as $key => $value) {
                if (!$value['is_deleted']) {
                    $value['rownum'] = $no;
                    $value['aksi'] = Html::button(
                        '<i class="fa fa-times"></i>',
                        [
                            'class' => 'btn btn-danger btn-xs delete-data',
                            'action' => 'delete-cache-jenazah?id=' . $id . '&key=' . $key . '&type=' . $cachetype,
                            'data-confirm-message' => Yii::t('fe', 'confirm_batal'),
                        ]
                    );

                    array_push($data_tables, $value);
                    $no++;
                }
            }

            $return = [
                'data' => $data_tables,
                'draw' => $request->post('draw'),
                'recordsTotal' => count($data_tables),
                'recordsFiltered' => count($data_tables)
            ];
            return DocoHelpers::response($return);
        } catch (Exception $e) {
            $return = [
                'data' => [],
                'draw' => $request->post('draw'),
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ];
            return DocoHelpers::response($return);
        } catch (\RequestException $e) {
            $return = [
                'data' => [],
                'draw' => $request->post('draw'),
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ];
            return DocoHelpers::response($return);
        }
    }

    public function actionSetSatuanObatJenazah()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $obat_id = $parents[0];
                $reqSatuanObat = $this->_restRanap->get('inf-pasien-ranap/get-satuan-obat-jenazah', [
                    'query' => [
                        'obat_id' => $obat_id
                    ]
                ]);
                $result = json_decode($reqSatuanObat->getBody(), true);
                $data = isset($result['response']['data_obat']) ? $result['response']['data_obat'] : [];
                if (isset($data['satuankecil_id']) && isset($data['satuan_kecil'])) {
                    $output = [['id' => $data['satuankecil_id'], 'name' => $data['satuan_kecil']]];
                    $selected = $data['satuankecil_id'];
                    return ['output' => $output, 'selected' => $selected];
                }
            }
        }
        return ['output' => '', 'selected' => ''];
    }

    public function actionDeleteCacheJenazah()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $id = $get['id'];
        $type = $get['type'];
        $key = $get['key'];
        $cache = Yii::$app->cache;
        $cacheName = $id . '-' . $type;
        try {
            $getCache = $cache->get($cacheName);
            if (isset($getCache[$key])) {
                $listdata = $getCache;
                unset($listdata[$key]);
                $cache->set($cacheName, $listdata);
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function actionCetakPersetujuanJenazah($id)
    {
        $path = Yii::getAlias("@download") . "/persetujuan-jenazah.pdf";
        $pendaftaran_id = DocoHelpers::decrypt($id);
        // Try catch
        try {
            // Request
            $request = $this->_restJenazah->get('informasi-pasien-meninggal/print-belum-diterima?pendaftaran_id=' . $pendaftaran_id, [
                'save_to' => $path,
            ]);

            // Download pdf
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            var_dump(json_decode($e->getResponse()->getBody()));
            exit;
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionStopAkomodasi($pendaftaran_id)
    {
        $cache = Yii::$app->cache;
        $encryptedId = DocoHelpers::encrypt($pendaftaran_id);

        $cacheData = $cache->get('pasien-pendaftaran-id-' . $encryptedId);
        if (!empty($cacheData)) {
            $cache->delete('pasien-pendaftaran-id-' . $encryptedId);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = $this->_restRanap->get('inf-pasien-ranap/stop-akomodasi?pendaftaran_id=' . $pendaftaran_id);

        $body = json_decode($response->getBody(), true);
        // dump($body);die;
        return DocoHelpers::response($body);
    }

    public function actionBatalStopAkomodasi($pendaftaran_id)
    {
        $cache = Yii::$app->cache;
        $encryptedId = DocoHelpers::encrypt($pendaftaran_id);
        $request = Yii::$app->request;
        $title = 'Batal Stop Akomodasi';
        $model = new BatalStopAkomodasiForm;
        $listStatusBatal = [['no' => 0, 'text' => 'Edit Pemeriksaan'] , ['no' => 1, 'text' => 'Melanjutkan Perawatan']];
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $username = Yii::$app->docoVars->user('nama');
        $cacheData = $cache->get('pasien-pendaftaran-id-' . $encryptedId);
        if (!$cacheData) {
            $cache->delete('pasien-pendaftaran-id-' . $encryptedId);
        }

        if($request->post()){
            $model->load($request->post());
            if ($model->validate()) {
                $post = $request->post('BatalStopAkomodasiForm', []);
                $post['pendaftaran_id'] = isset($post['pendaftaran_id']) ? PelayananHelpers::decryptId($post['pendaftaran_id']) : 1;
                $response = $this->_restRanap->post('inf-pasien-ranap/batal-stop-akomodasi', [
                    'form_params' => $post
                ]);
                $body = json_decode($response->getBody(), true);
                return DocoHelpers::response($body, false, $formName);
            } else {
                $errors = $model->errors;
                return DocoHelpers::response($errors, 422, $formName);
            }

        } else {
            return $this->renderAjax('_batal_stop_akomodasi', get_defined_vars());
        }
    }

    public function actionFormBatalStopAkomodasi()
    {

    }

    public function actionExportDetailRincian($pendaftaran_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        if (!is_numeric($pendaftaran_id)) {
            $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        }
        $path = Yii::getAlias("@download") . "/detail-rincian-tagihan.pdf";
        $response = $this->_restRanap->get('inf-pasien-ranap/export-detail-rincian?pendaftaran_id=' . $pendaftaran_id, [
            'save_to' => $path,
        ]);
        $body = json_decode($response->getBody(), true);
        return DocoHelpers::previewPdf($path);
    }

    /**
     * @todo Fungsi untuk stop pasien titipan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionStopPasienTitipan($pendaftaran_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = $this->_restRanap->get(
            'inf-pasien-ranap/stop-pasien-titipan?pendaftaran_id=' . $pendaftaran_id
        );
        $body = json_decode($response->getBody(), true);
        $removeSession = $this->removeSession($pendaftaran_id);
        return DocoHelpers::response($body);
    }

    private function removeSession($id)
    {
        $session = Yii::$app->session;
        $cache = Yii::$app->cache;
        $pendId = DocoHelpers::encrypt($id);

        $cacheData = $cache->get('pasien-pendaftaran-id-' . $pendId);
        if ($cacheData) {
            $cache->delete('pasien-pendaftaran-id-' . $pendId);
        }

        if ($session['ranap-list-data-allow-' . $pendId]) {
            $session->remove('ranap-list-data-allow-' . $pendId);
        }

        if ($session['tindakan']) {
            $session->remove('tindakan');
        }

        if ($session['paket']) {
            $session->remove('paket');
        }

        return true;
    }

    public function actionExportSuratKeteranganKelahiran($pendaftaran_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        if (!is_numeric($pendaftaran_id)) {
            $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        }
        $path = Yii::getAlias("@download") . "/surat-keterangan-kelahiran.pdf";
        $response = $this->_restRanap->get('inf-pasien-ranap/export-surat-keterangan-kelahiran?pendaftaran_id=' . $pendaftaran_id, [
            'save_to' => $path,
        ]);
        $body = json_decode($response->getBody(), true);
        return DocoHelpers::previewPdf($path);
    }

    public function actionExportSuratR2bbl($pendaftaran_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        if (!is_numeric($pendaftaran_id)) {
            $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        }
        $path = Yii::getAlias("@download") . "/surat-r2bbl.pdf";
        $response = $this->_restRanap->get('inf-pasien-ranap/export-surat-r2bbl?pendaftaran_id=' . $pendaftaran_id, [
            'save_to' => $path,
        ]);
        $body = json_decode($response->getBody(), true);
        return DocoHelpers::previewPdf($path);
    }

    public function actionModalKelasTitipan()
    {
        return $this->renderAjax('_modal_kelastitipan_tagihan');
    }

    public function actionShowPopupExcel()
    {
        $title = 'Informasi Pasien Rawat Inap';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['jenis'] = $request->get('jenis', 'ranap');
        $allRuangan = $request->get('allruangan', '');
        if ($allRuangan != 1) {
            $yiiRestfulParams['ruangan'] = Yii::$app->docoVars->workspace("ruangan_id");
        }
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modal_excel', get_defined_vars());
    }

    public function actionProcessSyncExcel($randString)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);
        $session['randString'] = $randString;
        return $this->guzzleExec($this->_restRanap, [
            'url' => "inf-pasien-ranap/export-excel-bgprocess",
            'payload' => [
                'query' => $session
            ],
        ]);
    }

    public function actionDownloadExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('fileName', null);
        $fileDownloads = 'informasi-pasien-rawat-inap.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restRanap->get('inf-pasien-ranap/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }

    public function actionRiwayatVisitDokter($pendaftaran_id)
    {
        $request = Yii::$app->request;
        $pendaftaranId = DocoHelpers::decrypt($pendaftaran_id);
        $title = 'Riwayat Visit Dokter';
        $getPatientHeader = $this->guzzleExec($this->_restRanap, [
            'url' => 'inf-pasien-ranap/get-history-visit-header',
            'method' => 'get',
            'payload' => [
                'query' => [
                    'pendaftaran_id' => $pendaftaranId,
                ],
            ]
        ]);
        $data = [
            'pasien' => isset($getPatientHeader['data']) ? $getPatientHeader['data'] : [],
        ];
        return $this->renderAjax('riwayat_visit_dokter', compact('pendaftaranId', 'title', 'data'));
    }

    public function actionGetRiwayatVisit($pendaftaran_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $payload = DocoDatatableHelper::advancedFilterParam();
        $pendaftaranId = DocoHelpers::decrypt($pendaftaran_id);
        $result = $this->guzzleExec($this->_restRanap, [
            'url' => 'inf-pasien-ranap/get-history-visit',
            'method' => 'get',
            'payload' => [
                'query' => array_merge($payload, ['pendaftaran_id' => $pendaftaranId])
            ],
        ]);

        return $result;
    }
}
