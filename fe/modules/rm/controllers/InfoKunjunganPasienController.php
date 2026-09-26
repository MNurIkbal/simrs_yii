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

use app\modules\rm\models\FormKunjunganPasien;
use app\components\Services\DiagnosaService;


class InfoKunjunganPasienController extends DocoController
{
    protected $_title;
    protected $_restRm;
    protected $_module = '/rm/info-kunjungan-pasien/';
    public $diagnosaService;

    public function __construct($id, $module, $config = [], DiagnosaService $diagnosaService)
    {
        $this->diagnosaService = $diagnosaService;
        parent::__construct($id, $module, $config);
    }

    public function init()
    {
        parent::init();
        $this->_title = Yii::t('fe', 'Pengkodean Diagnosa');
        $this->_restRm = Yii::$app->docoRest->rm;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        try {
            $response = $this->_restRm->get('info-kunjungan-pasien/get-request');
            $response = json_decode($response->getBody(),true);
            $response = $response['response'];
            $cara_bayar = isset($response['cara_bayar']) ? $response['cara_bayar'] : [];
            $instalasi = isset($response['instalasi']) ? $response['instalasi'] : [];
            $ruangan = isset($response['ruangan']) ? $response['ruangan'] : [];
            $penjamin = isset($response['penjamin']) ? $response['penjamin'] : [];
            $status_verivikasi = isset($response['status_verivikasi']) ? $response['status_verivikasi'] : [];
            $jenis_kelamin = isset($response['jenis_kelamin']) ? $response['jenis_kelamin'] : [];

            $session_id = Yii::$app->docoVars->user("id");
            $cache_diagnosa = Yii::$app->cache->get("cache_diagnosa_" . $session_id);
            if (!$cache_diagnosa) {
                $response = $this->_restRm->get('info-kunjungan-pasien/get-list-diagnosa');
                $response = json_decode($response->getBody(), true);
                $diagnosa = $response['response']['diagnosa'];
                Yii::$app->cache->set("cache_diagnosa_" . $session_id, $diagnosa);
            }
        } catch (RequestException $e) {
            var_dump($e->getMessage());die();
            $cara_bayar = $instalasi = $ruangan = $penjamin = [];
            $status_verivikasi = [];
        }
        return $this->render('index',get_defined_vars());
    }

    public function actionGetData()
    {
        try {
            $request = Yii::$app->request;
            $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
            $response = $this->_restRm->request('get', 'info-kunjungan-pasien/index', [
                'query' => $filter
            ]);
            $row = [];
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pendaftaran_id']);
                $value['pasienadmisi_id'] = $value['pasienadmisi_id'] ? $value['pasienadmisi_id'] : 0;
                $value['primary'] = $primaryKey;
                $value['admisi'] = DocoHelpers::encrypt($value['pasienadmisi_id']);
                $value['rowNum'] = $no;
                $value['tgl_pendaftaran'] = date('d/m/Y', strtotime($value['tgl_pendaftaran']));
                $value['info_pasien'] = $value['no_rekam_medik'] .'<br>'. $value['nama_pasien'] .'<br>'. $value['jenis_kelamin'];
                $value['info_penjamin'] = $value['carabayar_nama'] .' / '. $value['penjamin_nama'];
                $value['info_instalasi'] = $value['instalasi_nama'] .' / '. $value['ruangan_nama'];
                $value['diagnosa_dokter'] = $this->generateDiagnosa($value, true, false);
                $value['diagnosa_coding'] = $this->generateDiagnosa($value, false);
                $value['tglpasienpulang'] = !empty($value['tglpasienpulang']) ?  date("d/m/Y", strtotime($value['tglpasienpulang'])) :  "-";
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

    public function actionGetInstalasi($assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $params = '';
        if ($request->post()) {
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
            $params = '?id='.$parent_label;
        }

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restRm->get('allow/get-instalasi-pasien'. $params);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value)
                $result['output'][] = [
                    'id' => $value['instalasi_id'],
                    'name' => $value['instalasi_nama']
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

    public function actionGetRuangan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];
        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restRm->get('allow/get-ruangan-pasien?id='.$parent_label);
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

    public function actionGetPenjamin($assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $params = '';
        if ($request->post()) {
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
            $params = '?id='.$parent_label;
        }

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restRm->get('allow/get-penjamin'. $params);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value)
                $result['output'][] = [
                    'id' => $value['penjamin_id'],
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
            $response = $this->_restRm->get('allow/get-carabayar?id='.$parent_label);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value)
                $result['output'][] = [
                    'id' => $value['carabayar_id'],
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

    public function actionGetIcd()
    {
        return $this->diagnosaService->getCacheDiagnosa($this->_restRm, 'info-kunjungan-pasien/get-list-diagnosa');
        /** Replace pengambilan data cache diagnosa untuk meminimalisir attribute cache yang tidak sinkron */
        // $request = Yii::$app->request;
        // $type_icd = $request->get('type_icd');
        // $term = $request->get('term');
        // $not_in = $request->get('not_in');

        // try {
        //     $session_id = Yii::$app->docoVars->user("id");
        //     $cache_diagnosa = Yii::$app->cache->get("cache_diagnosa");
        //     if (!$cache_diagnosa) {
        //         $response = $this->_restRm->get('info-kunjungan-pasien/get-list-diagnosa');
        //         $response = json_decode($response->getBody(), true);
        //         $diagnosa = $response['response']['diagnosa'];
        //         Yii::$app->cache->set("cache_diagnosa", $diagnosa);
        //         $cache_diagnosa = $diagnosa;
        //     }
        //     $term = '/' . strtolower($term) . '/';
        //     $find_data = array_filter($cache_diagnosa, function ($a) use ($term) {
        //         $a = str_replace(".", "", $a);
        //         $term = str_replace(".", "", $term);
        //         return preg_grep($term, $a);
        //     });

        //     $data = [];
        //     if ($find_data) {
        //         if (!$not_in) {
        //             $not_in = [];
        //         }
        //         foreach ($find_data as $value) {
        //             if ( strtolower(trim($value['tabularlist_versi'])) == strtolower(trim($type_icd)) ) {
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
        //     'result' => $data
        // ]);
    }

    public function actionGetDokter()
    {
        $request = Yii::$app->request;
        $term = $request->get('term');
        try {
            $response = $this->_restRm->get('allow/get-dokter',[
                'query' => [
                    'term' => $term,
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $response = $response['response'];
            $data = [];
            foreach ($response as $value) {
                $data[] = [
                    'id' => $value['pegawai_id'],
                    'text' => $value['nama_pegawai'],
                ];
            }
        } catch (RequestException $e) {
            $data = [
                'messages' => $e->getMessage()
            ];
        }

        return DocoHelpers::response([
            'result' => $data
        ]);
    }

    public function actionGetJenisPenyakit()
    {
        $request = Yii::$app->request;
        $term = $request->get('term');
        try {
            $response = $this->_restRm->get('allow/get-jenis-penyakit',[
                'query' => [
                    'term' => $term,
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $response = $response['response'];
            $data = [];
            foreach ($response as $value) {
                $data[] = [
                    'id' => $value['jeniskasuspenyakit_id'],
                    'text' => $value['jeniskasuspenyakit_nama'],
                ];
            }
        } catch (RequestException $e) {
            $data = [
                'messages' => $e->getMessage()
            ];
        }

        return DocoHelpers::response([
            'result' => $data
        ]);
    }

    public function actionSimpanKoreksi($id,$admisi)
    {
        $request = Yii::$app->request;
        $model = new FormKunjunganPasien;
        $model->koreksi_diagnosa = $request->post('koreksi_diagnosa');
        $model->dokter_dpjp_id = $request->post('dokter_dpjp_id');
        $model->pasien_id = $request->post('pasien_id');
        $model->data_koreksi = $request->post('data_koreksi');
        $model->total_data = $request->post('total_data');
        $id = DocoHelpers::decrypt($id);
        $admisi = DocoHelpers::decrypt($admisi);
        if ($model->validate()) {
            $response = $this->_restRm->post('info-kunjungan-pasien/save',[
                'form_params' => $model->attributes,
                'query' => [
                    'id' => $id,
                    'admisi' => $admisi
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            return DocoHelpers::response($response,false,'');
        } else {
            return DocoHelpers::response([
                'response' => [
                    'data' => $model->errors
                ]
            ],422);
        }
    }

    public function actionKoreksiDiagnosa($id,$admisi,$is_koreksi = false)
    {
        $title = $this->_title;
        $id_dec = DocoHelpers::decrypt($id);
        $admisi_dec = DocoHelpers::decrypt($admisi);
        $state = true;
        try {
            $response = $this->_restRm->get('info-kunjungan-pasien/detail',[
                'query' => [
                    'id' => $id_dec,
                    'admisi' => $admisi_dec
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $response = $response['response'];
            // dump($response);die;
            $data = $response['header'];
            $dataDetail = $response['detail'];
            $mapping = $response['mapping'];
            $hasil_diagnosa = $response['hasil_diagnosa'];
            $state = ($data['status_pengajuanklaim'] == '' || $data['status_pengajuanklaim'] == '555') ? true : false;
            $final = $data['status_verifikasi'] == 551 ? true : false;
            $detail = [];
            $penyerta = 0;
            $countDetail = count($dataDetail);
            if ($countDetail > 0) {
                $penyerta = $dataDetail['diagnosa_penyerta'];
                $detail = [
                    'diagnosa_masuk' => (empty($response['hasil_diagnosa']) ? $dataDetail['diagnosa_masuk'] : isset($response['fl_detail']['diagnosa_masuk'])) ? $response['fl_detail']['diagnosa_masuk'] : $dataDetail['diagnosa_masuk'],
                    'diagnosa_utama' => (empty($response['hasil_diagnosa']) ? $dataDetail['diagnosa_utama'] : isset($response['fl_detail']['diagnosa_utama'])) ?  $response['fl_detail']['diagnosa_utama'] : $dataDetail['diagnosa_utama'],
                    'diagnosa_tambahan' => (empty($response['hasil_diagnosa']) ? $penyerta : isset($response['fl_detail']['diagnosa_penyerta'])) ? $response['fl_detail']['diagnosa_penyerta'] : $penyerta,
                    'tindakan' => (empty($response['hasil_diagnosa']) ? $dataDetail['diagnosa_terapi'] : isset($response['fl_detail']['diagnosa_terapi'])) ? $response['fl_detail']['diagnosa_terapi'] : $dataDetail['diagnosa_terapi'],
                ];
            }
            $diagnosa = [
                'data' => $data,
                'data_detail' => $dataDetail, // sblm koreksi
                'detail' => $detail,
                'count_detail' => $countDetail,
                'mapping' => $mapping,
                'hasil_diagnosa' => $hasil_diagnosa,
                'penyerta' => $penyerta,
                'fl_detail' => isset($response['fl_detail']) ? $response['fl_detail'] : [],
            ];
            // dump($detail);die;
        } catch (RequestException $e) {
            $data = $detail = $mapping = $hasil_diagnosa = [];
            $final = false;
        }
        return $this->render('form', get_defined_vars());
    }

    private function generateDiagnosa($value, $isAwal = false, $skip = true)
    {
        $diagMasuk = $diagUtama = $diagTambahan = $diagPenyerta = '';

        if($isAwal == true) {
            $diagMasuk = isset($value['diagnosadokter_masuk']) ? $value['diagnosadokter_masuk'] : '';
            $diagUtama = isset($value['diagnosadokter_utama']) ? $value['diagnosadokter_utama'] : '';
            $diagTambahan = isset($value['diagnosadokter_penyerta']) ? $value['diagnosadokter_penyerta'] : '';
            $diagPenyerta = isset($value['diagnosadokter_terapi']) ? $value['diagnosadokter_terapi'] : '';
        } else {
            if(isset($value['diagnosa_coding']) && !empty($value['diagnosa_coding'])) {
                $diagCoding = json_decode($value['diagnosa_coding'], true);
                foreach($diagCoding as $key => $value) {
                    foreach($value as $k => $v) {
                        foreach($v as $x => $y) {
                            $cleanDiag[] = $y;
                        }
                    }
                }
                $tmpStr = $tmpStrUtama = $tmpStrMsk = $tmpStrPenyerta = $tmpStrTerapi = '';
                if(isset($cleanDiag)) {
                    foreach($cleanDiag as $k => $v) {
                        switch ($v['type']) {
                            case DocoConstants::DIAGNOSA_UTAMA:
                                $tmpStrUtama = "<br>".(isset($v['text']) ? $v['text'] : ''). "</br>";
                                break;
                            case DocoConstants::DIAGNOSA_MASUK:
                                $tmpStrMsk .= "<br>".(isset($v['text']) ? $v['text'] : ''). "</br>";
                                break;
                            case DocoConstants::DIAGNOSA_PENYERTA:
                                $tmpStrPenyerta .= "<br>".(isset($v['text']) ? $v['text'] : ''). "</br>";
                                break;
                            case DocoConstants::DIAGNOSA_TERAPI:
                                $tmpStrTerapi .= "<br>".(isset($v['text']) ? $v['text'] : ''). "</br>";
                                break;
                            default:
                                $tmpStr = '';
                                break;
                        }
                    }
                }
                $diagUtama = $tmpStrUtama;
                $diagMasuk = $tmpStrMsk;
                $diagTambahan = $tmpStrPenyerta;
                $diagPenyerta = $tmpStrTerapi;
            }
        }

        if(!$skip) {
            if(!empty($diagUtama) && $diagUtama != '') {
                $tmp = json_decode($diagUtama, true);
                $tmpStr = '';
                // foreach($tmp as $k => $v) {
                //     $tmpStr .= isset($v['text']) ? $v['text'] : ''."\r\n";
                // }
                $diagUtama = "<br>".(isset($tmp['text']) ? $tmp['text'] : ''). "</br>";
            }

            if(!empty($diagMasuk) && $diagMasuk != '') {
                if(DocoHelpers::isJson($diagMasuk) != true) {
                    $diagMasuk = explode(',', $diagMasuk);
                    $tmp['text'] = isset($diagMasuk[2]) ? $diagMasuk[2] . ' - '. $diagMasuk[1] : $diagMasuk[1];
                } else {
                    $tmp = json_decode($diagMasuk, true);
                }
                $tmpStr = '';
                // foreach($tmp as $k => $v) {
                //     $tmpStr .= isset($v['text']) ? $v['text'] : ''."\r\n";
                // }
                $diagMasuk = "<br>".(isset($tmp['text']) ? $tmp['text'] : ''). "</br>";
            }
    
            if(!empty($diagTambahan) && $diagTambahan != '') {
                $tmp = json_decode($diagTambahan, true);
                $tmpStr = '';
                foreach($tmp as $k => $v) {
                    $tmpStr .= "<br>".(isset($v['text']) ? $v['text'] : ''). "</br>";
                }
                $diagTambahan = $tmpStr;
            }

            if(!empty($diagPenyerta) && $diagPenyerta != '') {
                $tmp = json_decode($diagPenyerta, true);
                $tmpStr = '';
                foreach($tmp as $k => $v) {
                    $tmpStr .= "<br>".(isset($v['text']) ? $v['text'] : ''). "</br>";
                }
                $diagPenyerta = $tmpStr;
            }
        }

        return "<b>Masuk</b>" . "<br>" . wordwrap($diagMasuk,100,"<br>") . "<br>" . "<b>Utama</b>" . "<br>" . wordwrap($diagUtama,100,"<br>") . "<br>" .
        "<b>Tambahan</b>" . "<br>" . wordwrap($diagTambahan,100,"<br>") . "<br>" .
        "<b>Tindakan</b>" . "<br>" . wordwrap($diagPenyerta,100,"<br>");
    }

    public function actionListPenjamin() {
        $request = Yii::$app->request;
        $post = $request->post();
        $carabayar_id = $post['depdrop_parents'][0];

        $penjaminRequest = $this->_restRm->get('allow/list-penjamin?carabayar_id='.$carabayar_id);
        $body = json_decode($penjaminRequest->getBody(),TRUE);
        $responses = $body['response'];

        $out = [];
        foreach($responses as $key => $response) {
            $out[] = [
                'id' => $key,
                'name' => $response
            ];
        }

        return json_encode(['output'=>$out, 'selected'=>'']);
    }
}