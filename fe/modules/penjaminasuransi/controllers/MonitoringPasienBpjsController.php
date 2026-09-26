<?php 

namespace Doco\penjaminasuransi\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DHtml;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use app\modules\penjaminasuransi\models\MonitorSetDiagnosaForm;

class MonitoringPasienBpjsController extends DocoController
{
    protected $_title;
    protected $_restMaster;
    protected $_restPenjaminAsuransi;
    protected $_module = '/penjamin-asuransi/monitoring-pasien-bpjs/';

    public function init()
    {
        parent::init();
        $this->_title = Yii::t('fe', 'Monitoring Pasien Rawat Inap BPJS');
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restPenjaminAsuransi = Yii::$app->docoRest->penjaminasuransi;
    }

    public function actionIndex()
    {
        $payload = [
            DocoConstants::STATUS_PERIKSA_DIPERIKSA,
            DocoConstants::STATUS_PERIKSA_PULANG,
            DocoConstants::STATUS_PERIKSA_BTL_PERIKSA,
            DocoConstants::STATUS_PERIKSA_BLM_PERIKSA
        ];
        $response = $this->guzzleExec($this->_restPenjaminAsuransi, [
            'url' => "monitoring-pasien-bpjs/get-lookup",
            'payload' => [
                'query' => $payload,
            ]
        ]);
        foreach ($response as $key => $value) {
                $data[] = [
                    'id' => $value['lookup_name'],
                    'text' => $value['lookup_name']
                ];
            }
        $listStatusPeriksa = $data;
        $title = DHtml::getTitleMenu();
		$title = !empty($title) ? $title : $this->_title;
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $payload = DocoDatatableHelper::advancedFilterParam();
        $response = $this->guzzleExec($this->_restPenjaminAsuransi, [
            'url' => "monitoring-pasien-bpjs/index",
            'payload' => [
                'query' => $payload,
            ]
        ]);
        foreach ($response['data'] as $key => $value) {
            $pendaftaranId = ArrayHelper::getValue($value, 'pendaftaran_id');
            $pasienAdmisiId = ArrayHelper::getValue($value, 'pasienadmisi_id');
            $setDiagnosaTindakan = ArrayHelper::getValue($value, 'set_diagnosatindakan');
            $setDiagnosaPenyerta = ArrayHelper::getValue($value, 'set_diagnosapenyerta');
            $tarifInacbg = ArrayHelper::getValue($value, 'tarif_inacbg', 0);
            $tagihanRs = ArrayHelper::getValue($value, 'tagihan_rs', 0);
            $persentase = ($tarifInacbg == 0) ? 0 : ceil(($tagihanRs/$tarifInacbg) * 100);

            $listDiagnosaTindakan = $listDiagnosaPenyerta = [];
            $diagnosaTindakanNama = '';
            $diagnosaPenyertaNama = '';
            
            if(!empty($setDiagnosaTindakan)) {
                $listDiagnosaTindakan = json_decode($setDiagnosaTindakan, true);
                if(!empty($listDiagnosaTindakan)) {
                    $diagnosaTindakanNama = '<ul>';
                    foreach ($listDiagnosaTindakan as $k => $v) {
                        $kode = ArrayHelper::getValue($v, 'kode');
                        $text = ArrayHelper::getValue($v, 'text');
                        if(!empty($kode) && !empty($text)) {
                            $diagnosaTindakanNama .= '<li>'.$kode.' - '.$text.'</li>';
                        }
                    }
                    $diagnosaTindakanNama .= '</ul>';
                }
            }
            
            if(!empty($setDiagnosaPenyerta)) {
                $listDiagnosaPenyerta = json_decode($setDiagnosaPenyerta, true);
                if(!empty($listDiagnosaPenyerta)) {
                    $diagnosaPenyertaNama = '<ul>';
                    foreach ($listDiagnosaPenyerta as $k => $v) {
                        $kode = ArrayHelper::getValue($v, 'kode');
                        $text = ArrayHelper::getValue($v, 'text');
                        if(!empty($kode) && !empty($text)) {
                            $diagnosaPenyertaNama .= '<li>'.$kode.' - '.$text.'</li>';
                        }
                    }
                    $diagnosaPenyertaNama .= '</ul>';
                }
            }

            $response['data'][$key]['set_diagnosatindakan'] = $diagnosaTindakanNama;
            $response['data'][$key]['set_diagnosapenyerta'] = $diagnosaPenyertaNama;
            $response['data'][$key]['persentase'] = $persentase;
            $response['data'][$key]['primary'] = DocoHelpers::encrypt($pendaftaranId.'-'.$pasienAdmisiId);
        }
        $response['recordsTotal'] = $response['_meta']['totalCount'];
        $response['recordsFiltered'] = $response['_meta']['totalCount'];
        return $response;
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
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
        $url = 'monitoring-pasien-bpjs/export-excel?'.http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/monitoring-pasien-bpjs.xlsx";
        try {
            $response = $this->_restPenjaminAsuransi->get($url,[
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        try {
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
            $path = Yii::getAlias("@download") . "/monitoring-pasien-bpjs.pdf";
            $response = $this->_restPenjaminAsuransi->get('monitoring-pasien-bpjs/export-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionGetKamar($assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $params = '';
        if ($request->post()) {
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
            $params = '?ruangan_id='.$parent_label;
        }

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        // dump($request->post());die;
        try {
            $response = $this->_restPenjaminAsuransi->get('allow/get-kamar-new'. $params);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value)
                $result['output'][] = [
                    'id' => $value['kamarruangan_id'],
                    'name' => $value['kamarruangan_nokamar']
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

    public function actionSetDiagnosa($id)
    {
        $model = new MonitorSetDiagnosaForm;
        $id = DocoHelpers::decrypt($id);
        list($pendaftaran_id, $pasienadmisi_id) = explode('-', $id);
        $dataMonitoring = $this->getDataMonitoring($id);
        if(Yii::$app->request->post()) {
            $post = Yii::$app->request->post();
            $diagUtamaId = null;
            $diagUtamaKode = null;
            $model->attributes = $post;
            $model->pendaftaran_id = $pendaftaran_id;
            $model->pasienadmisi_id = $pasienadmisi_id;
            $modelForm = ArrayHelper::getValue($post, 'MonitorSetDiagnosaForm', []);
            $diagUtamaId = ArrayHelper::getValue($modelForm, 'diag_utama_id');
            $diagPenyerta = ArrayHelper::getValue($modelForm, 'diag_penyerta');
            $diagTindakan = ArrayHelper::getValue($modelForm, 'diag_tindakan');
            $hakKelas = ArrayHelper::getValue($modelForm, 'hak_kelas');

            if(!empty($diagUtamaId)) {
                $diagutama = $diagUtamaId;
                if(strpos($diagutama, '_') !== false) {
                    $diagId = explode("_", $diagutama);
                }
                else {
                    $diagId = explode(" - ", $diagutama);
                }

                $diagUtamaId = ArrayHelper::getValue($diagId, 0);
                $diag_utama_kode = ArrayHelper::getValue($diagId, 1);
                $text = ArrayHelper::getValue($diagId, 1);
                $expText = explode(" - ", $text);
                $diagUtamaKode = ArrayHelper::getValue($expText, 0);
            }
            
            $model->diag_utama_id = $diagUtamaId;
            $model->diag_utama_kode = $diagUtamaKode;
            $model->pasienadmisi_id = $pasienadmisi_id;

            $list = [];
            $listTindakan = [];
            if(!empty($diagPenyerta)) {
                foreach ($diagPenyerta as $key => $value) {
                    $diagId = explode("_", $value);
                    $text = ArrayHelper::getValue($diagId, 1);
                    $expText = explode(" - ", $text);
                    $list[] = [
                        'id' => ArrayHelper::getValue($diagId, 0),
                        'kode' => ArrayHelper::getValue($expText, 0),
                        'text' => ArrayHelper::getValue($expText, 1)
                    ];
                }
            }
            if(!empty($diagTindakan)) {
                foreach ($diagTindakan as $key => $value) {
                    $diagId = explode("_", $value);
                    $text = ArrayHelper::getValue($diagId, 1);
                    $expText = explode(" - ", $text);
                    $listTindakan[] = [
                        'id' => ArrayHelper::getValue($diagId, 0),
                        'kode' => ArrayHelper::getValue($expText, 0),
                        'text' => ArrayHelper::getValue($expText, 1)
                    ];
                }
            }
            $model->no_rekam_medik = ArrayHelper::getValue($dataMonitoring, 'no_rekam_medik');
            $model->nama_pasien = ArrayHelper::getValue($dataMonitoring, 'nama_pasien');
            $model->jeniskelamin = ArrayHelper::getValue($dataMonitoring, 'jeniskelamin');
            $model->tanggal_lahir = ArrayHelper::getValue($dataMonitoring, 'tanggal_lahir');
            $model->diag_penyerta = json_encode($list);
            $model->diag_tindakan = json_encode($listTindakan);
            $model->tgl_masuk = ArrayHelper::getValue($dataMonitoring, 'tgl_pendaftaran');
            $model->tgl_keluar = ArrayHelper::getValue($dataMonitoring, 'tglpasienpulang');
            $model->dokter_dpjp = ArrayHelper::getValue($dataMonitoring, 'dokter_dpjp');
            $model->hak_kelas = $hakKelas;
            $model->kelaspelayanan_id = ArrayHelper::getValue($dataMonitoring, 'kelaspelayanan_id');
            $model->kelaspelayanan_nama = ArrayHelper::getValue($dataMonitoring, 'kelaspelayanan_nama');
            $model->no_sep = ArrayHelper::getValue($dataMonitoring, 'nosep');
            $model->no_kartu = ArrayHelper::getValue($dataMonitoring, 'nokartuasuransi');

            if($model->validate()) {
                try {
                    $response = $this->_restPenjaminAsuransi->post('monitoring-pasien-bpjs/save',[
                        'form_params' => $model->attributes
                    ]);
                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response,false,'MonitorSetDiagnosaForm');
                } catch (RequestException $e) {
                    return DocoHelpers::response([
                        'messages' => $e->getMessage()
                    ], 500);
                }
            } else {
                return DocoHelpers::response($model->errors, 422, 'MonitorSetDiagnosaForm');
            }
        }
        else {
            $title = Yii::t('fe', 'Set Diagnosa');
            $dataDiagnosa = $this->getDataExistDiagnosa($dataMonitoring);
            $callbackDiagUtama = ArrayHelper::getValue($dataDiagnosa, 'callbackDiagUtama', []);
            $callbackDiagPenyerta = ArrayHelper::getValue($dataDiagnosa, 'callbackDiagPenyerta', []);
            $callbackDiagTindakan = ArrayHelper::getValue($dataDiagnosa, 'callbackDiagTindakan', []);
            $keteranganDokter = ArrayHelper::getValue($dataDiagnosa, 'keteranganDokter');
            $keteranganNaikKelas = ArrayHelper::getValue($dataDiagnosa, 'keteranganNaikKelas');
            $fontColor = ArrayHelper::getValue($dataDiagnosa, 'fontColor');

            $model->diag_utama_id = ArrayHelper::getValue($dataDiagnosa, 'keyDiagUtama');
            $model->diag_penyerta = ArrayHelper::getValue($dataDiagnosa, 'keyDiagPenyerta');
            $model->diag_tindakan = ArrayHelper::getValue($dataDiagnosa, 'keyDiagTindakan');
            $model->hak_kelas = ArrayHelper::getValue($dataMonitoring, 'hak_kelas');
            $model->kelaspelayanan_id = ArrayHelper::getValue($dataMonitoring, 'kelaspelayanan_id');
            $model->kelaspelayanan_nama = ArrayHelper::getValue($dataMonitoring, 'kelaspelayanan_nama');
        }

        return $this->renderAjax('_formDiagnosa', get_defined_vars());
    }

    private function getDataMonitoring($id)
    {
        try {
            list($pendaftaran_id, $pasienadmisi_id) = explode('-', $id);
            $requests = $this->_restPenjaminAsuransi->get('allow/get-data-monitoring?pendaftaran_id='.$pendaftaran_id.'&pasienadmisi_id='.$pasienadmisi_id);
            $response = json_decode($requests->getBody(), true);
            $result = ['response' => $response['response']];
            return $result['response'];
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
        } catch (\Exception $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
        }
    }

    public function actionGetDataDiagnosa()
    {
        $request = Yii::$app->request;
        $q = $request->get('q');
        $type = $request->get('type');
        $data = [];
        
        try {
            $response = $this->_restPenjaminAsuransi->get('allow/get-data-diagnosa',[
                'query' => [
                    'q' => $q,
                    'type' => $type,
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $response = $response['response'];
            foreach ($response as $value) {
                $data[] = [
                    'id' => $value['diagnosa_id'].'_'.$value['diagnosa_kode'].' - '.$value['diagnosa_nama'],
                    'text' => $value['diagnosa_kode'].' - '.$value['diagnosa_nama']
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

    private function getListDiagnosa($value)
    {
        $diagnosaUtamaNama = $diagnosaTindakanNama = $diagnosaPenyertaNama = '';
        if(!empty($value['diagnosa_utama'])) {
            $diagnosa_utama = json_decode($value['diagnosa_utama'], true);
            $diagnosaUtamaNama = $diagnosa_utama['text'];
        }

        if(!empty($value['diagnosa_tindakan'])) {
            $listDiagnosaTindakan = json_decode($value['diagnosa_tindakan'], true);
            if(!empty($listDiagnosaTindakan)) {
                foreach ($listDiagnosaTindakan as $k => $v) {
                    $diagnosaTindakanNama = $v['text'];
                }
            }
        }

        if(!empty($value['diagnosa_penyerta'])) {
            $listDiagnosaPenyerta = json_decode($value['diagnosa_penyerta'], true);
            if(!empty($listDiagnosaPenyerta)) {
                foreach ($listDiagnosaPenyerta as $k => $v) {
                    $diagnosaPenyertaNama = $v['text'];
                }
            }
        }

        return [
            'diagnosa_utama' => $diagnosaUtamaNama,
            'diagnosa_tindakan' => $diagnosaTindakanNama,
            'diagnosa_penyerta' => $diagnosaPenyertaNama,
        ];
    }

    public function actionMonitor($id)
    {
        $id = DocoHelpers::decrypt($id);
        list($pendaftaran_id, $pasienadmisi_id) = explode('-', $id);
        $title = Yii::t('fe', 'Monitoring Pasien Rawat Inap BPJS');
        $response = $this->_restPenjaminAsuransi->get('monitoring-pasien-bpjs/get-kelompok-diagnosa?pendaftaran_id='.$pendaftaran_id.'&pasienadmisi_id='.$pasienadmisi_id);
        $body = json_decode($response->getBody(), True);
        $result = ArrayHelper::getValue($body, 'response', []);
        $model = ArrayHelper::getValue($result, 'model', []);
        $dataPasien = ArrayHelper::getValue($result, 'dataPasien', []);
        $diagnosaUtama = ArrayHelper::getValue($dataPasien, 'diagnosa_utama');
        $setDiagnosaUtama = ArrayHelper::getValue($dataPasien, 'set_diagnosautama');
        $diagnosaUtamaNama = '';
        if (!empty($diagnosaUtama)) {
            $diagnosaUtama = json_decode($diagnosaUtama, true);
            if(array_key_exists(0, $diagnosaUtama)) {
                foreach ($diagnosaUtama as $key => $value) {
                    $diagnosaUtamaNama = ArrayHelper::getValue($value, 'text');
                }
            } else {
                $diagnosaUtamaNama = ArrayHelper::getValue($diagnosaUtama, 'text');
            }
        }
        
        if(empty($diagnosaUtamaNama)) {
            $diagnosaUtamaNama = $setDiagnosaUtama;
        }
        return $this->render('_monitoring', get_defined_vars());
    }

    public function actionUpdatePersen()
    {
        try {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $get = $request->get();
            $response = $this->_restPenjaminAsuransi->post('monitoring-pasien-bpjs/update-persen', [
                'form_params' => [
                    'data' => $get,
                ],
            ]);
            $body = json_decode($response->getBody(), True);
            $result = $body['response'];
            $result = ['response' => $result];
            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
        } catch (\Exception $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
        }
    }

    public function actionDetailTagihan($id, $monitorbpjs_id)
    {
        
        $title = Yii::t('fe', 'Rincian Tagihan');
        list($pendaftaran_id, $pasienadmisi_id) = explode('-', $id);
        $header = $this->getDetail($pendaftaran_id, $pasienadmisi_id, $monitorbpjs_id);
        $groupHeader = [];
        foreach ($header as $key => $value) {
            $groupHeader[$value['groupinacbg_nama']][] = $value;
        }
        return $this->renderAjax('_detailTagihan', get_defined_vars());
    }

    public function actionGetDataTindakan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $id = $request->get('id');
        $jenis = $request->get('jenis');
        $monitorbpjs_id = $request->get('monitorbpjs_id');
        list($pendaftaran_id, $pasienadmisi_id) = explode('-', $id);
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restPenjaminAsuransi->get('monitoring-pasien-bpjs/detail-tagihan?pendaftaran_id='.$pendaftaran_id.'&pasienadmisi_id='.$pasienadmisi_id.'&jenis='.$jenis.'&monitorbpjs_id='.$monitorbpjs_id.'&'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['tgl_tindakan'] = date('d M Y', strtotime($value['tgl_tindakan']));
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

    private function getDiagnosaByKode($diagnosaUtamaKode)
    {
        try {
            $request = Yii::$app->request;
            $get = $request->get();
            $url = 'monitoring-pasien-bpjs/get-diagnosa-by-kode';
            $response = $this->_restPenjaminAsuransi->get($url.'?diagnosaUtamaKode='.$diagnosaUtamaKode);
            $body = json_decode($response->getBody(), True);
            $result = ['response'=>$body['response']];
            return $result['response'];
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
        } catch (\Exception $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
        }
    }

    private function getDetail($pendaftaran_id, $pasienadmisi_id, $monitorbpjs_id)
    {
        try {
            $request = Yii::$app->request;
            $get = $request->get();
            $response = $this->_restPenjaminAsuransi->get('monitoring-pasien-bpjs/detail-tagihan?pendaftaran_id='.$pendaftaran_id.'&pasienadmisi_id='.$pasienadmisi_id.'&monitorbpjs_id='.$monitorbpjs_id);
            $body = json_decode($response->getBody(), True);
            return $body['response'];
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
        } catch (\Exception $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
        }
    }

    private function getDataExistDiagnosa($dataMonitoring)
    {
        $keyDiagPenyerta = [];
        $keyDiagTindakan = [];
        $keyDiagUtama = [];
        $callbackDiagPenyerta = [];
        $callbackDiagTindakan = [];
        $callbackDiagUtama = [];

        $setDiagnosaUtama = ArrayHelper::getValue($dataMonitoring, 'set_diagnosautama');
        $setDiagnosaPenyerta = ArrayHelper::getValue($dataMonitoring, 'set_diagnosapenyerta');
        $setDiagnosaTindakan = ArrayHelper::getValue($dataMonitoring, 'set_diagnosatindakan');
        $diagnosaPenyerta = ArrayHelper::getValue($dataMonitoring, 'diagnosa_penyerta');
        $diagnosaTindakan = ArrayHelper::getValue($dataMonitoring, 'diagnosa_tindakan');
        $diagnosaUtama = ArrayHelper::getValue($dataMonitoring, 'diagnosa_utama');
        if(!empty($diagnosaPenyerta)) {
            $diagnosaPenyerta = json_decode($diagnosaPenyerta, true);
        }
        if(!empty($diagnosaTindakan)) {
            $diagnosaTindakan = json_decode($diagnosaTindakan, true);
        }
        if(!empty($diagnosaUtama)) {
            $diagnosaUtama = json_decode($diagnosaUtama, true);
        }

        $statusMonitorId = ArrayHelper::getValue($dataMonitoring, 'status_monitor_id');
        $cekDiagnosa = ArrayHelper::getValue($dataMonitoring, 'cek_diagnosa');
        $keteranganNaikKelas = ArrayHelper::getValue($dataMonitoring, 'keterangan_kelas');
        $isDokter = false;
        if($statusMonitorId == 0 && $diagnosaUtama != "") {
            $isDokter = true;
        }
        elseif($cekDiagnosa == "BEDA") {
            $isDokter = true;
        }

        $diagnosaNama = '';
        if(!empty($diagnosaUtama)) {
            if(array_key_exists(0, $diagnosaUtama)) {
                foreach ($diagnosaUtama as $key => $value) {
                    $diagnosaNama = ArrayHelper::getValue($value, 'text');
                }
            }
            else {
                $diagnosaNama = ArrayHelper::getValue($diagnosaUtama, 'text');
            }

            $keteranganDokter = ($isDokter == true) ? 'Diagnosa diinput oleh Dokter' : 'Diagnosa diinput oleh Casemix';
            $keteranganDokter = $keteranganDokter. ' - '.$diagnosaNama;
        }
        else {
            $keteranganDokter = 'Dokter belum menginput diagnosa';
        }
        
        $fontColor = '';
        if($keteranganNaikKelas == 'TURUN KELAS') {
            $fontColor = '#DD972C';
        }
        elseif($keteranganNaikKelas == 'NAIK KELAS') {
            $fontColor = '#B94747';
        }
        else {
            $fontColor = '#34bfa3';
        }

        if(!empty($setDiagnosaUtama)) {
            $callbackDiagUtama = [];
            list($diagnosaKode, $diagnosaNama) = explode(" - ", $setDiagnosaUtama);
            $diagnosaId = $this->getDiagnosaByKode($diagnosaKode);
            $diagnosaId = ArrayHelper::getValue($diagnosaId, 'diagnosa_id');
            if(!empty($diagnosaId) && !empty($diagnosaKode) && !empty($diagnosaNama)) {
                $keyDiagUtama = $diagnosaId.' - '.$diagnosaKode.' - '.$diagnosaNama;
                $callbackDiagUtama = [$keyDiagUtama => $diagnosaKode.' - '.$diagnosaNama];
            }
        }
        else {
            if(!empty($diagnosaUtama)) {
                $callbackDiagUtama = [];
                $diagnosaUtamaText = ArrayHelper::getValue($diagnosaUtama, 'text');
                $diagnosaId = ArrayHelper::getValue($diagnosaUtama, 'id');
                if(!empty($diagnosaUtamaText)) {
                    $explode = explode(" - ", $diagnosaUtamaText);
                    $diagnosaKode = ArrayHelper::getValue($explode, 0);
                    $diagnosaNama = ArrayHelper::getValue($explode, 1);
                }
                if(!empty($diagnosaId) && !empty($diagnosaKode) && !empty($diagnosaNama)) {
                    $keyDiagUtama = $diagnosaId.' - '.$diagnosaKode.' - '.$diagnosaNama;
                    $callbackDiagUtama = [$keyDiagUtama => $diagnosaKode.' - '.$diagnosaNama];
                }
            }
        }

        if(!empty($setDiagnosaPenyerta)) {
            $setDiagnosaPenyerta = json_decode($setDiagnosaPenyerta, true);
            foreach ($setDiagnosaPenyerta as $key => $value) {
                $valueId = ArrayHelper::getValue($value, 'id');
                $valueKode = ArrayHelper::getValue($value, 'kode');
                $valueText = ArrayHelper::getValue($value, 'text');
                $valDiagId = $valDiagText = '';
                if(!empty($valueId) && !empty($valueKode) && !empty($valueText)) {
                    $valDiagId = $valueId.' - '.$valueKode.' - '.$valueText;
                    $valDiagText = $valueKode.' - '.$valueText;
                }
                $keyDiagPenyerta[] = $valDiagId;
                $callbackDiagPenyerta[] = [
                    'id' => $valDiagId, 
                    'text' => $valDiagText 
                ];
            }
        }
        else {
            if(!empty($diagnosaPenyerta)) {
                foreach ($diagnosaPenyerta as $key => $value) {
                    $valueId = ArrayHelper::getValue($value, 'id');
                    $valueKode = ArrayHelper::getValue($value, 'kode');
                    $valueText = ArrayHelper::getValue($value, 'text');
                    $valDiagId = $valDiagText = '';
                    if(!empty($valueText)) {
                        $explode = explode(" - ", $valueText);
                        $valueKode = ArrayHelper::getValue($explode, 0);
                        $valueText = ArrayHelper::getValue($explode, 1);
                    }

                    if(!empty($valueId) && !empty($valueKode) && !empty($valueText)) {
                        $valDiagId = $valueId.' - '.$valueKode.' - '.$valueText;
                        $valDiagText = $valueKode.' - '.$valueText;
                    }
                    
                    $keyDiagPenyerta[] = $valDiagId;
                    $callbackDiagPenyerta[] = [
                        'id' => $valDiagId, 
                        'text' => $valDiagText 
                    ];
                }
            }
        }
        
        if(!empty($setDiagnosaTindakan)) {
            $setDiagnosaTindakan = json_decode($setDiagnosaTindakan, true);
            foreach ($setDiagnosaTindakan as $key => $value) {
                $valueId = ArrayHelper::getValue($value, 'id');
                $valueKode = ArrayHelper::getValue($value, 'kode');
                $valueText = ArrayHelper::getValue($value, 'text');
                $valTindakanId = $valTindakanText = '';
                if(!empty($valueId) && !empty($valueKode) && !empty($valueText)) {
                    $valTindakanId = $valueId.' - '.$valueKode.' - '.$valueText;
                    $valTindakanText = $valueKode.' - '.$valueText;
                }
                $keyDiagTindakan[] = $valTindakanId;
                $callbackDiagTindakan[] = [
                    'id' => $valTindakanId, 
                    'text' => $valTindakanText 
                ];
            }
        }
        else {
            if(!empty($diagnosaTindakan)) {
                foreach ($diagnosaTindakan as $key => $value) {
                    $valueId = ArrayHelper::getValue($value, 'id');
                    $valueKode = ArrayHelper::getValue($value, 'kode');
                    $valueText = ArrayHelper::getValue($value, 'text');
                    $valTindakanId = $valTindakanText = '';
                    if(!empty($valueId) && !empty($valueKode) && !empty($valueText)) {
                        $valTindakanId = $valueId.' - '.$valueKode.' - '.$valueText;
                        $valTindakanText = $valueKode.' - '.$valueText;
                    }
                    $keyDiagTindakan[] = $valTindakanId;
                    $callbackDiagTindakan[] = [
                        'id' => $valTindakanId, 
                        'text' => $valTindakanText 
                    ];
                }
            }
        }

        return [
            'keyDiagUtama' => $keyDiagUtama,
            'keyDiagPenyerta' => $keyDiagPenyerta,
            'keyDiagTindakan' => $keyDiagTindakan,
            'callbackDiagUtama' => $callbackDiagUtama,
            'callbackDiagPenyerta' => $callbackDiagPenyerta,
            'callbackDiagTindakan' => $callbackDiagTindakan,
            'keteranganDokter' => $keteranganDokter,
            'keteranganNaikKelas' => $keteranganNaikKelas,
            'fontColor' => $fontColor
        ];
    }

    public function actionFilters() 
    {
        $request = Yii::$app->request;
        $term = $request->get('term', null);
        $type = $request->get('type', null);
        $page = $request->get('page', 1);
        $additionalPayload = $request->get('additionalPayload', []);
        $response = $this->guzzleExec($this->_restPenjaminAsuransi, [
            'url' => 'monitoring-pasien-bpjs/filters',
            'payload' => [
                'query' => [
                    'term' => $term,
                    'type' => $type,
                    'page' => $page,
                    'additionalPayload' => $additionalPayload
                ]
            ],
        ]);
        return $this->responseJson(200, 'Data berhasil diambil!', $response);
    }

    public function actionShowPopup($type)
    {
        $title = ($type == 1) ? 'Cetak PDF Monitoring BPJS' : 'Unduh Excel Monitoring BPJS';
        $request = Yii::$app->request;
        $advancedFilter = $request->get('advancedFilter', []);
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advancedFilter'] = $advancedFilter;
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modal', get_defined_vars());
    }

    public function actionProcessSync($randString, $type)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);
        $session['randString'] = $randString;
        $session['type'] = $type;
        return $this->guzzleExec($this->_restPenjaminAsuransi, [
            'url' => "monitoring-pasien-bpjs/export-file",
            'payload' => [
            'query' => $session
            ],
        ]);
    }

    public function actionDownloadFile()
    {
        $request = Yii::$app->request;
        $type = $request->get('type', null);
        $filename = $request->get('fileName', null);
        $ext = ($type == 1) ? '.pdf' : '.xlsx';
        $fileDownloads = ($type == 1) ? $filename.$ext : 'Monitoring BPJS'.$ext;
        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restPenjaminAsuransi->get('monitoring-pasien-bpjs/download-file', [
            'query' => [
                'filename' => $filename,
                'type' => $type,
            ],
            'save_to' => $path,
        ]);
        $response = json_decode($response->getBody(), true);
        if($type == 1) {
            return DocoHelpers::previewPdf($path);
        }
        else {
            return DocoHelpers::downloadFile($path,true);
        }
    }
}
