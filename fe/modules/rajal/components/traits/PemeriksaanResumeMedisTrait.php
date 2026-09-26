<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2019-01-17 16:10:30
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-01-22 11:46:00
 */

namespace app\modules\rajal\components\traits;

use Yii;
use yii\filters\AccessControl;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;
use yii\base\Exception;

use function GuzzleHttp\json_encode;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\Traits\ResumeMedisTrait;
use app\components\Pelayanan\PelayananHelpers;

use app\modules\rajal\models\ResumeMedisForm;
trait PemeriksaanResumeMedisTrait
{
    public function actionResumeMedis()
    {
       
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id', null);
        $pasien_id = $request->get('pasien_id', null);
        return $this->actionResumeMedisOdc();
        // if($this->_id_ruangan == $this->_ruangan_odc){
        //     return $this->actionResumeMedisOdc();
        // }
        // $datapemeriksaan = [];
        // try { 
        //     $response = $this->_restRajal->get('resume-medis/', [
        //         'query' => [
        //             'pendaftaran_id' => DocoHelpers::decrypt($pendaftaran_id),
        //             'pasien_id' => DocoHelpers::decrypt($pasien_id),
        //         ]
        //     ]);
        //     $body = json_decode($response->getBody(), true);
        //     $datapemeriksaan = isset($body['response']['pemeriksaanfisik']) ? $body['response']['pemeriksaanfisik'] : [];
        // } catch (\Exception $e) {
        //     $datapemeriksaan = [];
        // } catch (\RequestException $e){
        //     $datapemeriksaan = [];
        // }

        // return $this->renderAjax('resume-medis/_resume', get_defined_vars());
    }


    public function actionResumeMedisOdc()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id', null);
        $model = new ResumeMedisForm;
        $userIdentity = Yii::$app->session->get('user_identity');
        $datapemeriksaan = [];

        if($post = Yii::$app->request->post('ResumeMedisForm') ) {
            $model->attributes = $post;
            $model->diag_penyerta = json_decode($model->diag_penyerta, true);
            $model->diag_utama = json_decode($model->diag_utama, true);
            $model->diag_awal = json_decode($model->diag_awal, true);
            $nama_alergi = isset($post['nama_alergi']) ? $post['nama_alergi'] : null;
            $is_alergi = isset($post['is_alergi']) ? $post['is_alergi'] : 0;
            $instruksi_tanggal = !empty($post['instruksi_tanggal']) ? date('Y-m-d', strtotime($post['instruksi_tanggal'])) : null;
            
            $additionalData = [
                'instruksi_tanggal' => $post['instruksi_tanggal'],
                'instruksi_kontrol' => isset($post['instruksi_kontrol']) ? $post['instruksi_kontrol'] : null,
                'kontak_darurat' => isset($post['kontak_darurat']) ? $post['kontak_darurat'] : null,
                'edukasi_rencana' => isset($post['edukasi_rencana']) ? $post['edukasi_rencana'] : null,
                'kesadaran' => isset($post['kesadaran']) ? $post['kesadaran'] : null,
                'keadaan_umum' => isset($post['keadaan_umum']) ? $post['keadaan_umum'] : null,
                'frekuensi_nafas' => isset($post['frekuensi_nafas']) ? $post['frekuensi_nafas'] : null,
                'cara_keluar' => isset($post['cara_keluar']) ? $post['cara_keluar'] : 1,
                'is_alergi' => $is_alergi,
                'nama_alergi' => $nama_alergi,
                'instruksi_tindakanbmhp' => isset($post['instruksi_tindakanbmhp']) ? $post['instruksi_tindakanbmhp'] : null,

            ];
            $model->additional_data = json_encode($additionalData);
            $decodeList = [
                // 'order_laboratorium',
                // 'order_radiologi',
                'konsul',
                'obat',
                // 'obat_dibawa_pulang',
                'tindakan'
            ];
            foreach($decodeList as $item){
                $model->$item = $this->dataFormatter( json_decode( $model->$item, true) );
            }
            return $this->helper->guzzleExec($this->_restRajal, [
                'url' => 'resume-medis/save-resume-odc',
                'method' => 'POST',
                'payload' => [
                    'query' => [
                        'pendaftaran_id' => PelayananHelpers::decryptId($pendaftaran_id),
                    ],
                    'form_params' => $model->attributes
                ],
                'returnResponse' => true
            ]);
        }
        $data = $this->guzzleExec($this->_restRajal, [
            'url' => 'resume-medis/get-data-resume-medis',
            'method' => 'get',
            'payload' => [
                'query' => [
                    'pendaftaran_id' => $this->helper->decrypt($pendaftaran_id)
                ]
            ]
        ]);

        $patient_record = ArrayHelper::getValue($data, 'patient', []);
        $asesmen_keperawatan = ArrayHelper::getValue($data, 'asesmen_keperawatan', []);
        $asesmen_medis = ArrayHelper::getValue($data, 'asesmen_medis', []);
        $latest_reseptur_dpjp = ArrayHelper::getValue($data, 'latest_reseptur_dpjp', []); 
        $konsul = ArrayHelper::getValue($data, 'konsul', []);
        $tindakan_bedah = ArrayHelper::getValue($data, 'tindakan_bedah', []);
        $tindakan = $this->getTindakanValue(ArrayHelper::getValue($data, 'tindakan', []));
        $list_cara_keluar = ArrayHelper::map(ArrayHelper::getValue($data, 'list_cara_keluar', []), 'carakeluar_id', 'carakeluar_namalain');

        $is_perawat = PelayananHelpers::isNurse() ? 1 : 0;
        $disabled = $is_perawat ? 'disabled' : '';
        
        $status_disabled = in_array(ArrayHelper::getValue($patient_record, 'status_periksa', ''), [DocoConstants::STATUS_PULANG, DocoConstants::STATUS_RUJUK_RAWAT_INAP]);
        $enable_edit = !empty($data['enable_pulang']) ? $data['enable_pulang'] : false;
        $dokter_dpjp_id = isset($patient_record['dokter_dpjp_id']) ? $patient_record['dokter_dpjp_id'] : '';
        if($status_disabled){
            if (
                ( $enable_edit && $dokter_dpjp_id === Yii::$app->docoVars->user('id_pegawai')) 
                || in_array('SPV Rekam Medik', $userIdentity['roles'])
                ){
                $status_disabled = false;
            }
        }else{
            if($dokter_dpjp_id != Yii::$app->docoVars->user('id_pegawai') && !in_array('SPV Rekam Medik', $userIdentity['roles'])){
                $status_disabled = true;
            }
        }

        // check resume medis is exist (saved) (doest load suggestion)
        if (!empty(ArrayHelper::getValue($data, 'resume_medis', []))) {
            $additional_data = isset($data['resume_medis']['additional_data']) && $data['resume_medis']['additional_data'] != NULL ? json_decode($data['resume_medis']['additional_data'], true) : NULL;
            $additional_data = $additional_data != NULL ? $additional_data : [];
            $resume_medis = ArrayHelper::merge(ArrayHelper::getValue($data, 'resume_medis', []), $additional_data);
            $model->attributes = $resume_medis;
            
            //custom mutator
            $model->tgl_masuk = !empty(ArrayHelper::getValue($resume_medis, 'tgl_masuk')) ? date('d-M-Y', strtotime(ArrayHelper::getValue($resume_medis, 'tgl_masuk'))) : date('d-M-Y');
            $model->tgl_keluar = !empty(ArrayHelper::getValue($resume_medis, 'tgl_keluar')) ? date('d-M-Y', strtotime(ArrayHelper::getValue($resume_medis, 'tgl_keluar'))) : NULL;
            $model->frekuensi_nafas = ArrayHelper::getValue($resume_medis, 'rr', '');
            $model->order_laboratorium = ArrayHelper::getValue($resume_medis, 'order_laboratorium.text', '');
            $model->order_radiologi = ArrayHelper::getValue($resume_medis, 'order_radiologi.text', '');
            $model->prosedur_list = $tindakan;
            list($model->diag_awal, $model->diag_awal_text) = array_values($this->getDiagnosaSoapValue($model->diag_awal)); // return use array values because list only assign from numerical array
            list($model->diag_utama, $model->diag_utama_text) = array_values($this->getDiagnosaSoapValue($model->diag_utama)); // return use array values because list only assign from numerical array
            list($model->diag_penyerta, $model->diag_penyerta_json) = array_values($this->getDiagnosaSoapMultipleValue($model->diag_penyerta, TRUE)); // return use array values because list only assign from numerical array
        } else { 
            // load suggest
            $resume_medis_suggest = [
                'pendaftaran_id' => PelayananHelpers::decryptId($pendaftaran_id),
                'tgl_masuk' => !empty(ArrayHelper::getValue($patient_record, 'tgl_pendaftaran')) ? date('d-M-Y', strtotime(ArrayHelper::getValue($patient_record, 'tgl_pendaftaran'))) : date('d-M-Y'),
                'berat_badan' => ArrayHelper::getValue($asesmen_medis, 'berat_badan', ''),
                'tinggi_badan' => ArrayHelper::getValue($asesmen_medis, 'tinggi_badan', ''),
                'nadi' => ArrayHelper::getValue($asesmen_medis, 'nadi', ''),
                'frekuensi_nafas' => ArrayHelper::getValue($asesmen_medis, 'rr', ''),
                'td' => ArrayHelper::getValue($asesmen_medis, 'td', ''),
                'suhu' => ArrayHelper::getValue($asesmen_medis, 'suhu', ''),
                'keluhan_utama' => ArrayHelper::getValue($data, 'cppt.latest_subjective', ''),
                'pemeriksaan_fisik' => ArrayHelper::getValue($data, 'cppt.latest_objective', ''),
                'instruksi_tindakanbmhp' => $this->getTindakanAsText($tindakan_bedah),
                'is_alergi' => isset($asesmen_keperawatan['alergi']) && $asesmen_keperawatan['alergi'] !== NULL ?  (int) $asesmen_keperawatan['alergi'] : NULL,
                'nama_alergi' => $this->getAlergiValue($asesmen_keperawatan),
                'cara_keluar' => ArrayHelper::getValue($patient_record, 'carakeluar_id', ''),
                'prosedur' => ArrayHelper::getValue($data, 'prosedur', ''),
                'konsultasi' => $this->getKonsulListAsText($konsul),
                'diag_penyerta_json' =>  $this->getDiagnosaSoapMultipleValue(ArrayHelper::getValue($data, 'cppt.all_diagnosa_penyerta', [])),
                'obat_dibawa_pulang' => $latest_reseptur_dpjp,
                'obat_dibawa_pulang_text' => ResumeMedisTrait::getListTakeHomeMedichine(ArrayHelper::index($latest_reseptur_dpjp, NULL, 'rke')), // reindexing array by rke, jika kosong = non racikan selain itu kebaca racikan
                'instruksi' => ArrayHelper::getValue($patient_record, 'dokter_dpjp', ''),
            ];
            $model->attributes = $resume_medis_suggest;
            
            list($model->diag_awal, $model->diag_awal_text) = array_values($this->getDiagnosaSoapValue(ArrayHelper::getValue($data, 'cppt.oldest_diagnosa', []))); // return use array values because list only assign from numerical array
            list($model->diag_utama_text, $model->diag_utama) = array_values($this->getDiagnosaSoapValue(ArrayHelper::getValue($data, 'cppt.latest_diagnosa', []))); // return use array values because list only assign from numerical array
        }
        
        return $this->renderAjax('resume-medis/odc/_resume', compact(
                'patient_record', 'pendaftaran_id', 'status_disabled','tindakan', 'konsul',
                'model', 'is_perawat', 'tgl_masuk', 'list_cara_keluar', 'disabled', 'tindakan'
            ));
    
    }

    public function actionGetNewDiagnosa($q = '', $type = 'diagnosa_masuk', $all_text = 0, $id_with_text = 0, $is_perawat = 0, $page = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $limit = 10;
            $offset = ($page-1)*10;
            $result = [];
            $result['results'] = [];

            if ($type == 'diagnosa_masuk') {
                $type = DocoConstants::VAR_KELOMPOK_DIAGNOSA_MASUK;
            } else if ($type == 'diagnosa_utama') {
                $type = DocoConstants::VAR_KELOMPOK_DIAGNOSA_UTAMA;
            } else if ($type == 'diagnosa_penyerta') {
                $type = DocoConstants::VAR_KELOMPOK_DIAGNOSA_PENYERTA;
            } else if ($type == 'diagnosa_operasi') {
                $type = DocoConstants::VAR_KELOMPOK_DIAGNOSA_OPERASI;
            } else if ($type == 'diagnosa_keluarga') {
                $type = DocoConstants::VAR_KELOMPOK_DIAGNOSA_KELUARGA;
            } else if ($type == 'diagnosa_terapi') {
                $type = DocoConstants::VAR_KELOMPOK_DIAGNOSA_TERAPI;
            } else {
                $type = DocoConstants::VAR_KELOMPOK_DIAGNOSA_AWALAN;
            }

            $request = $this->_restRajal->get('allow/get-new-diagnosa',['query'=>['q'=>$q,'type'=>$type,'page'=>$page,'offset'=>$offset,'limit'=>$limit,'is_perawat'=>$is_perawat]]);
            $response = json_decode($request->getBody(), true);

            $list = $response['response'];
            if ($all_text == 1) {
                foreach ($response['response'] as $value) {
                    $result['results'][] = [
                        'id' => $value['diagnosa_kode'] . ' - ' . $value['diagnosa_nama'],
                        'text' => $value['diagnosa_kode'] . ' - ' . $value['diagnosa_nama'],
                    ];
                }
            } else {
                if ($id_with_text == 1) {
                    foreach ($response['response'] as $value) {
                        $result['results'][] = [
                            'id' => $value['diagnosa_id'] . '_' . $value['diagnosa_kode'] . ' - ' . $value['diagnosa_nama'],
                            'text' => $value['diagnosa_kode'] . ' - ' . $value['diagnosa_nama'],
                        ];
                    }
                } else {
                    foreach ($response['response'] as $value) {
                        $result['results'][] = [
                            'id' => $value['diagnosa_id'],
                            'text' => $value['diagnosa_kode'] . ' - ' . $value['diagnosa_nama'],
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

    public function actionGetDataResumeObat()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $id = $request->get('pendaftaran_id');
        $pasien_id = $request->get('pasien_id');
        $id_encrypt = DocoHelpers::encrypt($id);
        $yiiRestfulParams['pendaftaran_id'] = DocoHelpers::decrypt($id);
        $yiiRestfulParams['pasien_id'] = DocoHelpers::decrypt($pasien_id);
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        try {
            $no = $request->get('start',1);
            $response = $this->_restRajal->get('resume-medis/get-reseptur', [
                    'query' => $yiiRestfulParams
                ]);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $key => $value) {

                $no++;
                $value['rowNum'] = $no;
                $value['qty_reseptur'] = DocoHelpers::formatNumber($value['qty_reseptur']);
                $data[] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

            return $result;
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\RequestException $e){
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }
    public function actionGetDataResumeTerapi()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $id = $request->get('pendaftaran_id');
        $pasien_id = $request->get('pasien_id');
        $type = $request->get('type', 'BMHP');
        $id_encrypt = DocoHelpers::encrypt($id);
        $yiiRestfulParams['pendaftaran_id'] = DocoHelpers::decrypt($id);
        $yiiRestfulParams['pasien_id'] = DocoHelpers::decrypt($pasien_id);
        $yiiRestfulParams['type'] = $type;

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        try {
            $no = $request->get('start',1);
            $response = $this->_restRajal->get('resume-medis/get-terapi', [
                    'query' => $yiiRestfulParams
                ]);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $key => $value) {
                $detailtindakantxt = '';
                if($type != 'BMHP'){
                    if(!empty($value['daftartindakan_namapaket'])){
                        $dataexplode = explode(',', $value['daftartindakan_namapaket']);
                        if(isset($dataexplode[0]) ){
                            $detailtindakantxt .= "<ul class='dash'>";
                            foreach ($dataexplode as $val) {
                                $detailtindakantxt .= "<li>".$val."</li>";
                            }
                            $detailtindakantxt .= "</ul>";
                        }
                    }
                }
                $no++;
                $value['rowNum'] = $no;
                $value['qty'] = DocoHelpers::formatNumber($value['qty']);
                $value['tindakan'] = !empty($value['tindakan']) ? '-' : $value['tindakan'];
                $value['tindakan_obat'] = $value['tindakan_obat'].' '.$detailtindakantxt;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

            return $result;
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\RequestException $e){
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }
    public function actionGetDataResumeLab()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $id = $request->get('pendaftaran_id');
        $pasien_id = $request->get('pasien_id');
        $id_encrypt = DocoHelpers::encrypt($id);
        $yiiRestfulParams['pendaftaran_id'] = DocoHelpers::decrypt($id);
        $yiiRestfulParams['pasien_id'] = DocoHelpers::decrypt($pasien_id);

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        try {
            $no = $request->get('start',1);
            $response = $this->_restRajal->get('resume-medis/get-detail-lab', [
                    'query' => $yiiRestfulParams
                ]);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $key => $value) {

                $no++;
                $value['rowNum'] = $no;
                $value['tgl_tindakan'] = date('d/m/Y', strtotime($value['tgl_tindakan'])); //DocoHelpers::convDateTime($value['tgl_tindakan']);
                $value['jenis'] = str_replace('_', ' ', $value['jenis']);
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

            return $result;
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\RequestException $e){
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }
    public function actionGetDataResumeRad()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $id = $request->get('pendaftaran_id');
        $pasien_id = $request->get('pasien_id');
        $id_encrypt = DocoHelpers::encrypt($id);
        $yiiRestfulParams['pendaftaran_id'] = DocoHelpers::decrypt($id);
        $yiiRestfulParams['pasien_id'] = DocoHelpers::decrypt($pasien_id);

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        try {
            $no = $request->get('start',1);
            $response = $this->_restRajal->get('resume-medis/get-detail-rad', [
                    'query' => $yiiRestfulParams
                ]);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $key => $value) {

                $no++;
                $value['rowNum'] = $no;
                $value['tgl_tindakan'] = date('d/m/Y', strtotime($value['tgl_tindakan'])); //DocoHelpers::convDateTime($value['tgl_tindakan']);
                $value['jenis'] = str_replace('_', ' ', $value['jenis']);
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

            return $result;
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\RequestException $e){
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }
    public function actionGetDataResumeBedah()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $id = $request->get('pendaftaran_id');
        $pasien_id = $request->get('pasien_id');
        $id_encrypt = DocoHelpers::encrypt($id);
        $yiiRestfulParams['pendaftaran_id'] = DocoHelpers::decrypt($id);
        $yiiRestfulParams['pasien_id'] = DocoHelpers::decrypt($pasien_id);

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        try {
            $no = $request->get('start',1);
            $response = $this->_restRajal->get('resume-medis/get-detail-bedah', [
                    'query' => $yiiRestfulParams
                ]);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $key => $value) {

                $no++;
                $value['rowNum'] = $no;
                $value['tgl_tindakan'] = DocoHelpers::convDateTime($value['tgl_tindakan']);
                $value['jenis'] = str_replace('_', ' ', $value['jenis']);
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

            return $result;
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\RequestException $e){
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }
    public function actionGetDataResumeRehab()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $id = $request->get('pendaftaran_id');
        $pasien_id = $request->get('pasien_id');
        $id_encrypt = DocoHelpers::encrypt($id);
        $yiiRestfulParams['pendaftaran_id'] = DocoHelpers::decrypt($id);
        $yiiRestfulParams['pasien_id'] = DocoHelpers::decrypt($pasien_id);

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        return $result;
        try {
            $no = $request->get('start',1);
            $response = $this->_restRajal->get('resume-medis/get-detail-bedah', [
                    'query' => $yiiRestfulParams
                ]);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $key => $value) {

                $no++;
                $value['rowNum'] = $no;
                $value['tgl_tindakan'] = date('d/m/Y', strtotime($value['tgl_tindakan']));
                $value['jenis'] = str_replace('_', ' ', $value['jenis']);
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

            return $result;
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\RequestException $e){
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }
    public function actionCetakResume()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/cetak-resume-medis.pdf";
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        $pasien_id = $request->get('pasien_id', null);
        \app\components\EsignHelpers::previewEsign([
            'type' => 'Resume Medis Rawat Jalan',
            'pendaftaran_id' => DocoHelpers::decrypt($pendaftaran_id),
        ]);
        try {
            if(Yii::$app->report->enabled){
                $url = 'rajal/resume-medis/cetak-resume-odc?pendaftaran_id='.DocoHelpers::decrypt($pendaftaran_id);
                return Yii::$app->report->exec($url,['manualRender'=>function() use($path,$pendaftaran_id,$pasien_id){
                        $response = $this->_restRajal->get('resume-medis/cetak-resume-odc',[
                            'save_to' => $path,
                            'query' => [
                                    'pendaftaran_id'=> DocoHelpers::decrypt($pendaftaran_id),
                                    'pasien_id'=> DocoHelpers::decrypt($pasien_id)
                                ],
                        ]);
            
                        return DocoHelpers::previewPdf($path);
                    }
                ]);
            }
            $response = $this->_restRajal->get('resume-medis/cetak-resume-odc',[
                'save_to' => $path,
                'query' => [
                        'pendaftaran_id'=> DocoHelpers::decrypt($pendaftaran_id),
                        'pasien_id'=> DocoHelpers::decrypt($pasien_id)
                    ],
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            // var_dump($e->getMessage()); die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            // var_dump($e->getMessage()); die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    /**
     * This function will retrieve lab which has result
     * 
     * @param String $pendaftaran_id
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionResumeLabResult($pendaftaran_id)
    {
        return $this->datatablePenunjang($pendaftaran_id, 'lab', \Yii::$app->request->get('load'));
    }

    /**
     * This function will retrieve radiologi
     * 
     * @param String $pendaftaran_id
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionResumeRadResult($pendaftaran_id)
    {
        return $this->datatablePenunjang($pendaftaran_id, 'rad');
    }

    /**
     * This function will return datatable result of penunjang
     * 
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    private function datatablePenunjang($pendaftaran_id, $type, $sub = '')
    {
        $urlEndpoint = null;
        switch ($type) {
            case 'rad':
                $urlEndpoint = 'resume-medis/rad-result';
                break;
            case 'lab':
                $urlEndpoint = 'resume-medis/lab-result';
                break;
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!empty($urlEndpoint)) {
            $payloadDatatable = DocoDatatableHelper::convertToRestfulParams(Yii::$app->request->get());
            $response = $this->guzzleExec($this->_restRajal, [
                'url' => $urlEndpoint,
                'payload' => [
                    'query' => [
                        'load' => $sub,
                        'pendaftaran_id' => $this->helper->decrypt($pendaftaran_id),
                        'paginationOption' => [
                            'page' => $payloadDatatable['page'],
                            'limit' => $payloadDatatable['per-page']
                        ]
                    ]
                ]
            ]);
            $totalRecord = isset($response['total']) ? $response['total'] : count($response['data']);
            return [
                'data' => $response['data'],
                'draw' => Yii::$app->request->get('draw'),
                'recordsTotal' => $totalRecord,
                'recordsFiltered' => $totalRecord
            ];
        } else {
            return [
                'data' => [],
                'draw' => Yii::$app->request->get('draw'),
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ];
        }
    }

    private function dataFormatter($fieldData)
    {
        $result = [];
        if (!empty($fieldData) && is_array($fieldData)) {
            foreach($fieldData as $key => $item){
                $name = $item['name'];
                $splitName = explode('[', $name);
                $fieldKey = str_replace(']', '', $splitName[1]);
                $fieldName = str_replace(']', '', $splitName[2]);
                if($fieldName == 'signa') {
                    $value = json_encode([
                        'text' => $value
                    ]);
                } else {
                    $value = $item['value'];
                }
                if ( !isset($result[$fieldKey]) ){
                    $result[$fieldKey] = [
                        $fieldName => $value
                    ];
                } else {
                    $result[$fieldKey] = array_merge($result[$fieldKey], [
                        $fieldName => $value
                    ]);
                }
            }
        }
        return $result;
    }

    public function actionCetakResumeOdc()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/cetak-resume-medis-odc.pdf";
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        $pasien_id = $request->get('pasien_id', null);
        try {
            $response = $this->_restRajal->get('resume-medis/cetak-resume-odc',[
                'save_to' => $path,
                'query' => [
                    'pendaftaran_id'=> $this->helper->decrypt($pendaftaran_id),
                    'pasien_id'=>$this->helper->decrypt($pasien_id)
                ],
            ]);
            return $this->helper->previewPdf($path);
        } catch (RequestException $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    private function recursiveArray( $arr )
    {
        $isArray = false;
        foreach ($arr as $v) {
            if (is_array($v)) $isArray = true;
        }

        if ( $isArray ) {
            return $arr[0];
        }
        return $arr;
    }
    
    private function getAlergiValue($data = [])
    {
        $alergi_obat = !empty(ArrayHelper::getValue($data, 'alergi_obat', NULL)) ? explode(',', ArrayHelper::getValue($data, 'alergi_obat', NULL)) : [];
        $alergi_makanan = !empty(ArrayHelper::getValue($data, 'alergi_makanan', NULL)) ? explode(',', ArrayHelper::getValue($data, 'alergi_makanan', NULL)) : [];
        $alergi_lainnya = !empty(ArrayHelper::getValue($data, 'alergi_lainnya', NULL)) ? explode(',', ArrayHelper::getValue($data, 'alergi_lainnya', NULL)) : [];
        return implode(', ', array_merge($alergi_obat, $alergi_makanan, $alergi_lainnya));
    }
    
    private function getTindakanValue(Array $data = [])
    {
        if (empty($data)) {
            return ' - ';
        }
        
        $prosedurTT = '';
        $prosedurTT .= '<table class="table table-bordered table-hover" id="table-tindakan-bmhp">';
        $prosedurTT .= '<tr>';
        $prosedurTT .= '<td style="text-align: center;"><strong>Tindakan</strong></td>';
        $prosedurTT .= '<td><input type="checkbox" class="checkbox-result checkbox-tindakan-all" value=""><strong>&nbsp;&nbsp;All</strong></td>';
        $prosedurTT .= '<tr>';
        for ($i=0; $i < count($data); $i++) { 
            if( $data[$i]['tipe'] == 'TINDAKAN'){
                $prosedurTT .= '<tr>';
                $prosedurTT .= '<td><b>Tindakan</b> '.$data[$i]['tindakan_paket_obat'].' Jumlah ' . $data[$i]['qty']  .'</td>';
                $prosedurTT .= '<td>'.'<input type="checkbox" class="checkbox-result checkbox-tindakan" value="<b>Tindakan </b>'.$data[$i]['tindakan_paket_obat'].' Jumlah '.$data[$i]['qty']  .'">'.'</td>';
                $prosedurTT .= '<tr>';
            }
            if( $data[$i]['tipe'] == 'BMHP'){
                $prosedurTT .= '<tr>';
                $prosedurTT .= '<td><b>Obat</b> '.$data[$i]['tindakan_paket_obat'].' Jumlah ' . $data[$i]['qty']  .'</td>';
                $prosedurTT .= '<td>'.'<input type="checkbox" class="checkbox-result checkbox-tindakan" value="<b>Obat </b>'.$data[$i]['tindakan_paket_obat'].' Jumlah '.$data[$i]['qty']  .'">'.'</td>';
                $prosedurTT .= '<tr>';
            }
        }
        $prosedurTT .= '</table>';
        
        return $prosedurTT;
    }
    
    
    private function getTindakanAsText(Array $tindakan = [])
    {
        $tindakan_text = '';
        $last_arr = end($tindakan);
        $last_tindakan = ArrayHelper::getValue($last_arr, 'daftartindakan_nama', NULL);
        foreach ($tindakan as $key => $value) {
            $tindakan = ArrayHelper::getValue($value, 'daftartindakan_nama');
            $str = ($last_tindakan == $tindakan) ? "\r" : "\r\n";
            $tindakan_text .= ($key + 1).'. '.$tindakan.$str;
        }
        return $tindakan_text;
    }
    
    private function getKonsulListAsText($consule_record)
    {
        $konsul_list = '';
        for ($i=0; $i < count($consule_record); $i++) { 
            $konsul_list .= date('d-M-Y H:i:s', strtotime($consule_record[$i]['tgl_selesaikonsul'])). ' - ' . $consule_record[$i]['dok_mengkonsul']  . ' - '. $consule_record[$i]['catatan_dokter_konsul']. ' - '.$consule_record[$i]['jawaban_konsul'] ."\n";
        }
        return $konsul_list;
    }
    
    private function getDiagnosaSoapValue($data) // can array or string (decoded to array)
    {
        $data_diagnosa = is_array($data) ?  $data : json_decode($data, true); 
        if ( !empty($data_diagnosa) && isset($data_diagnosa['text']) && $data_diagnosa['text'] != '-' ) {
            $diag_id = isset($data_diagnosa['id']) && !empty($data_diagnosa['id']) ? $data_diagnosa['id'] : $data_diagnosa['text'];
            $text = $data_diagnosa['text'];
            $value = ($diag_id == $data_diagnosa['text']) ? $data_diagnosa['text'] : $diag_id.'_'.$data_diagnosa['text'];
            
            return compact('value', 'text');
        }
        
        return ['value' => '', 'text' => ''];
    }
    
    private function getDiagnosaSoapMultipleValue($data = [], $is_select_2 = false)
    {
        $diagnosa_penyerta = [];
        $data = ($data != null || !empty($data)) ? $data : [];
        $data_diagnosa = is_array($data) ? $data : json_decode($data, true);
      
        if ( isset($data_diagnosa['text']) && $data_diagnosa['text'] != '-' ){
            $data_diagnosa = is_array($data_diagnosa) ? $data_diagnosa : json_encode($data_diagnosa, true) ;
            $diagnosa_penyerta[] = [
                'id' => $data_diagnosa['text'].'_'.$data_diagnosa['text'],
                'kode' => '',
                'text' => $data_diagnosa['text'],
            ];
        } else {
            foreach($data_diagnosa as $index => $item){
                $item = $this->recursiveArray( is_array($item) ? $item : json_decode($item, true) );
                if (isset($item['text']) && $item['text'] == '-' ) continue;
                $id = isset($item['id']) && !empty($item['id']) ? $item['id'] : '';
                $nama = isset($item['nama']) && !empty($item['nama']) ? $item['nama'] : '';
                $kode = isset($item['kode']) && !empty($item['kode']) ? $item['kode'] : '';
                $text = isset($item['text']) && !empty($item['text']) ? $item['text'] : '';
                $ids = !empty($id) ? $id : (!empty($text) ? $text : !empty($text) ? $text : '');
                $diagnosa_penyerta[$ids] = [
                    'id' => $ids,
                    'kode' => $kode,
                    'text' => (isset($item['kode']) && !empty($item['kode']) ? $item['kode'].' - ' : '').(isset($item['nama']) && !empty($item['nama']) ? $item['nama'] : $item['text'])
                ];
            }
        }
        
        return $is_select_2 
            ? ['value' => null, 'json' => $diagnosa_penyerta]
            : $diagnosa_penyerta;
    }

}