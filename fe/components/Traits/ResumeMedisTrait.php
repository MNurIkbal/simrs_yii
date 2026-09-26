<?php

namespace app\components\Traits;

use Yii;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use app\components\Pelayanan\PelayananHelpers;
use app\models\ResumeMedisForm;
use yii\web\Response;
use Mpdf\Mpdf;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

trait ResumeMedisTrait
{
    protected $baseUrl = '';

    public function actionResumeMedis()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id', null);
        $model = new ResumeMedisForm;
        $userIdentity = Yii::$app->session->get('user_identity');
        $datapemeriksaan = [];

        if($post = Yii::$app->request->post('ResumeMedisForm') ) {
            $model->load($post);
            $model->attributes = $post;
            $model->diag_penyerta = json_decode($model->diag_penyerta, true);
            $model->diag_utama = json_decode($model->diag_utama, true);
            $model->diag_awal = json_decode($model->diag_awal, true);
            $nama_alergi = isset($post['nama_alergi']) ? $post['nama_alergi'] : null;
            $is_alergi = isset($post['is_alergi']) ? $post['is_alergi'] : 0;

            $additionalData = [
                'instruksi_tanggal' => !empty($post['instruksi_tanggal']) ? $post['instruksi_tanggal'] : null,
                'instruksi_kontrol' => isset($post['instruksi_kontrol']) ? $post['instruksi_kontrol'] : null,
                'is_igd' => isset($post['is_igd']) ? $post['is_igd'] : false,
                'kontak_darurat' => isset($post['kontak_darurat']) ? $post['kontak_darurat'] : null,
                'edukasi_rencana' => isset($post['edukasi_rencana']) ? $post['edukasi_rencana'] : null,
                'kesadaran' => isset($post['kesadaran']) ? $post['kesadaran'] : null,
                'keadaan_umum' => isset($post['keadaan_umum']) ? $post['keadaan_umum'] : null,
                'frekuensi_nafas' => isset($post['frekuensi_nafas']) ? $post['frekuensi_nafas'] : null,
                'cara_keluar' => isset($post['cara_keluar']) ? $post['cara_keluar'] : 1,
                'is_alergi' => $is_alergi,
                'nama_alergi' => $nama_alergi,
                'instruksi_tindakanbmhp' => isset($post['instruksi_tindakanbmhp']) ? $post['instruksi_tindakanbmhp'] : null,
                'dokter_pengirim' => isset($post['dokter_pengirim']) ? $post['dokter_pengirim'] : null,

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
            return $this->helper->guzzleExec($this->restGeneral, [
                'url' => $this->baseUrl . '/save-resume-medis',
                'method' => 'POST',
                'payload' => [
                    'query' => [
                        'pendaftaran_id' => PelayananHelpers::decryptId($pendaftaran_id)
                    ],
                    'form_params' => $model->attributes
                ],
                'returnResponse' => true
            ]);
        }
        $data = $this->guzzleExec($this->restGeneral, [
            'url' => $this->baseUrl . '/get-data-resume-medis',
            'method' => 'get',
            'payload' => [
                'query' => [
                    'pendaftaran_id' => $this->helper->decrypt($pendaftaran_id)
                ]
            ]
        ]);
        
        $patient_record = ArrayHelper::getValue($data, 'patient', []);
        $tindakan_bedah = ArrayHelper::getValue($data, 'tindakan_bedah', []);
        $asesmen_keperawatan = ArrayHelper::getValue($data, 'asesmen_keperawatan', []);
        $asesmen_medis = ArrayHelper::getValue($data, 'asesmen_medis', []);
        $konsul = ArrayHelper::getValue($data, 'konsul', []);
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
            $model->tgl_masuk = !empty(ArrayHelper::getValue($resume_medis, 'tgl_masuk')) ? date('d-M-Y', strtotime(ArrayHelper::getValue($resume_medis, 'tgl_masuk'))) : NULL;
            $model->tgl_keluar = !empty(ArrayHelper::getValue($resume_medis, 'tgl_keluar')) ? date('d-M-Y', strtotime(ArrayHelper::getValue($resume_medis, 'tgl_keluar'))) : NULL;
            $model->frekuensi_nafas = ArrayHelper::getValue($resume_medis, 'rr', '');
            $model->order_laboratorium = ArrayHelper::getValue($resume_medis, 'order_laboratorium.text', '');
            $model->order_radiologi = ArrayHelper::getValue($resume_medis, 'order_radiologi.text', '');
            $model->prosedur_list = $tindakan;
            list($model->diag_awal, $model->diag_awal_text) = array_values($this->getDiagnosaSoapValue($model->diag_awal)); // return use array values because list only assign from numerical array
            list($model->diag_utama, $model->diag_utama_text) = array_values($this->getDiagnosaSoapValue($model->diag_utama)); // return use array values because list only assign from numerical array
            list($model->diag_penyerta, $model->diag_penyerta_json) = array_values($this->getDiagnosaSoapMultipleValue($model->diag_penyerta, TRUE)); // return use array values because list only assign from numerical array
        } else { 
            $latest_reseptur_dpjp = ArrayHelper::getValue($data, 'latest_reseptur_dpjp', []); 
            
            // load suggest
            $resume_medis_suggest = [
                'pendaftaran_id' => PelayananHelpers::decryptId($pendaftaran_id),
                'tgl_masuk' => !empty(ArrayHelper::getValue($patient_record, 'tgl_pendaftaran')) ? date('d-M-Y', strtotime(ArrayHelper::getValue($patient_record, 'tgl_pendaftaran'))) : date('d-M-Y'),
                'tgl_keluar' => !empty(ArrayHelper::getValue($patient_record, 'tgl_pasien_pulang')) ? date('d-M-Y', strtotime(ArrayHelper::getValue($patient_record, 'tgl_pasien_pulang'))) : NULL,
                'berat_badan' => ArrayHelper::getValue($asesmen_medis, 'berat_badan', ''),
                'tinggi_badan' => ArrayHelper::getValue($asesmen_medis, 'tinggi_badan', ''),
                'nadi' => ArrayHelper::getValue($asesmen_medis, 'nadi', ''),
                'frekuensi_nafas' => ArrayHelper::getValue($asesmen_medis, 'rr', ''),
                'td' => ArrayHelper::getValue($asesmen_medis, 'td', ''),
                'suhu' => ArrayHelper::getValue($asesmen_medis, 'suhu', ''),
                'keluhan_utama' => ArrayHelper::getValue($data, 'cppt.latest_subjective', ''),
                'pemeriksaan_fisik' => ArrayHelper::getValue($data, 'cppt.latest_objective', ''),
                'instruksi_tindakanbmhp' => $this->getTindakanAsText($tindakan_bedah),
                'is_alergi' => isset($asesmen_keperawatan['is_alergi']) && $asesmen_keperawatan['is_alergi'] !== NULL ?  (int) $asesmen_keperawatan['is_alergi'] : NULL,
                'nama_alergi' => $this->getAlergiValue($asesmen_keperawatan),
                'riwayat_penyakit_dahulu' => ArrayHelper::getValue($asesmen_medis, 'riwayat_penyakit_dahulu', ''),
                'cara_keluar' => ArrayHelper::getValue($patient_record, 'carakeluar_id'),
                'prosedur_list' => $tindakan,
                'konsultasi' => $this->getKonsulListAsText($konsul),
                'diag_penyerta_json' =>  $this->getDiagnosaSoapMultipleValue(ArrayHelper::getValue($data, 'cppt.all_diagnosa_penyerta', [])),
                'obat_rs' => $this->getObatRsValueAsText(ArrayHelper::getValue($data, 'all_reseptur', [])),
                'obat_dibawa_pulang' => $latest_reseptur_dpjp,
                'obat_dibawa_pulang_text' => ResumeMedisTrait::getListTakeHomeMedichine(ArrayHelper::index($latest_reseptur_dpjp, NULL, 'rke')), // reindexing array by rke, jika kosong = non racikan selain itu kebaca racikan
                'instruksi' => ArrayHelper::getValue($patient_record, 'dokter_dpjp', ''),
            ];
            
            $model->attributes = $resume_medis_suggest;
            
            list($model->diag_awal, $model->diag_awal_text) = array_values($this->getDiagnosaSoapValue(ArrayHelper::getValue($data, 'cppt.oldest_diagnosa', []))); // return use array values because list only assign from numerical array
            list($model->diag_utama_text, $model->diag_utama) = array_values($this->getDiagnosaSoapValue(ArrayHelper::getValue($data, 'cppt.latest_diagnosa', []))); // return use array values because list only assign from numerical array
        }
        
        return $this->renderAjax('//resume-medis/index', compact(
                'patient_record', 'pendaftaran_id', 'status_disabled','tindakan', 'konsul', 'model',
                'is_perawat', 'tgl_masuk', 'list_cara_keluar', 'disabled', 'tindakan'
            ));
    }

    /**
     * @todo Method untuk mendapatkan data diagnosa berdasarkan versi tabular list
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
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

            $request = $this->restGeneral->get($this->baseUrl . '/get-new-diagnosa',['query'=>['q'=>$q,'type'=>$type,'page'=>$page,'offset'=>$offset,'limit'=>$limit,'is_perawat'=>$is_perawat]]);
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

    public function actionCetakResume()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/cetak-resume-medis.pdf";
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        $pasien_id = $request->get('pasien_id', null);
        $margin_top = $request->get('margin_top', 0);
        \app\components\EsignHelpers::previewEsign([
            'type' => 'Resume Rawat Darurat',
            'pendaftaran_id' => DocoHelpers::decrypt($pendaftaran_id),
        ]);
        try {
            if(Yii::$app->report->enabled){
                $url = 'igd/pemeriksaan-igd/cetak-resume-medis-igd?pendaftaran_id='.DocoHelpers::decrypt($pendaftaran_id).'&pasien_id='.$pasien_id;
                return Yii::$app->report->exec($url,[
                    'manualRender'=>function() use($path,$pendaftaran_id,$pasien_id){
                        $response = $this->restGeneral->get($this->baseUrl . '/cetak-resume-medis-igd',[
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

            $response = $this->restGeneral->get($this->baseUrl . '/cetak-resume-medis-igd',[
                'save_to' => $path,
                'query' => [
                    'pendaftaran_id'=> DocoHelpers::decrypt($pendaftaran_id),
                    'pasien_id'=> DocoHelpers::decrypt($pasien_id)
                ],
            ]);

            $result = json_decode($response->getBody(), true);
            $body = $result['response']['body'];

            $mpdf = new Mpdf([
                'format' => [165,250],
                'tempDir' => Yii::getAlias("@download"),
                'autoPageBreak' => false
            ]);
            $mpdf->AddPageByArray([
                'margin-left' => 8,
                'margin-right' => 8,
                'margin-top' => 35 + $margin_top,
                'margin-bottom' => 35,
            ]);

            $mpdf->WriteHTML($body);
            $mpdf->Output();

            // return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            var_dump($e->getMessage()); die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage()); die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
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

    /**
     * This function will return datatable result of penunjang
     *
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionResumeResult($pendaftaran_id, $type)
    {
        $urlEndpoint = $this->baseUrl . '/resume-result';

        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!empty($urlEndpoint)) {
            $payloadDatatable = DocoDatatableHelper::convertToRestfulParams(Yii::$app->request->get());
            $response = $this->guzzleExec($this->restGeneral, [
                'url' => $urlEndpoint,
                'payload' => [
                    'query' => [
                        'pendaftaran_id' => $this->helper->decrypt($pendaftaran_id),
                        'type' => $type,
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

    public function getListTakeHomeMedichine($arrMedicine){
        $textObatDibawaPulang = '';
        $number = 0;
        $numberKey = 0;
        foreach ($arrMedicine as $key => $value) {
            if (!empty($value) && is_array($value)) {
                if ($key == "") {
                    $textObatDibawaPulang .= 'NON RACIKAN'."<br />";
                    foreach ($value as $k => $v) {
                        $number ++;
                        $qty =  !empty($v['qty_reseptur']) ? $v['qty_reseptur'] : '';
                        $satuan =  !empty($v['satuan_kecil']) ? $v['satuan_kecil'] : '';
                        $signa_nama = !empty($v['signa_nama']) ? $v['signa_nama'] : '';
                        $catatan =  !empty($v['etiket']) ? $v['etiket'] : '';
                        $obat_alkes = !empty($v['obatalkes_nama']) ? $v['obatalkes_nama'] : '';
                        $textObatDibawaPulang .= $number.'. '.$obat_alkes . ' ' . $qty . ' ' . $satuan. ' ' . $signa_nama . ' ' . $catatan."<br />";
                    }
                    $textObatDibawaPulang .= "<br />";
                }else{
                    $numberKey++;
                    $textObatDibawaPulang .= 'RACIKAN'.$key."<br />";
                    $textObatDibawaPulang .= 'R-'.$key."<br />";
                    $numberNR = 0;
                    foreach ($value as $k => $v) {
                        $number ++;
                        $numberNR++;
                        $qty =  !empty($v['qty_reseptur']) ? $v['qty_reseptur'] : '';
                        $satuan =  !empty($v['satuan_kecil']) ? $v['satuan_kecil'] : '';
                        $signa_nama = !empty($v['signa_nama']) ? $v['signa_nama'] : '';
                        $catatan =  !empty($v['etiket']) ? $v['etiket'] : '';
                        $obat_alkes = !empty($v['obatalkes_nama']) ? $v['obatalkes_nama'] : '';
                        $textObatDibawaPulang .= $numberNR.'. '.$obat_alkes . ' ' . $qty . ' ' . $satuan. ' ' . $signa_nama . ' ' . $catatan."<br />";
                    }
                    $textObatDibawaPulang = str_replace('RACIKAN1', "RACIKAN", $textObatDibawaPulang);
                    $textObatDibawaPulang = str_replace("RACIKAN".$numberKey, "", $textObatDibawaPulang);
                }
            }
        }
        return str_replace("<br />", "\n", $textObatDibawaPulang);
    }
    
    private function getAlergiValue($data = [])
    {
        $alergi_obat = !empty(ArrayHelper::getValue($data, 'alergi_obat', NULL)) ? explode(',', ArrayHelper::getValue($data, 'alergi_obat', NULL)) : [];
        $alergi_lainnya = !empty(ArrayHelper::getValue($data, 'alergi_lainnya', NULL)) ? explode(',', ArrayHelper::getValue($data, 'alergi_lainnya', NULL)) : [];
        return implode(', ', array_merge($alergi_obat, $alergi_lainnya));
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
        $data_diagnosa = is_array($data) ? $data : json_decode($data, true); 
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
                $item = is_array($item) ? $item : json_decode($item, TRUE);
                $item = is_array($item) ? $this->recursiveArray($item) : $item;
                if ((isset($item['text']) && $item['text'] == '-') || !is_array($item) ) continue;
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
    
    private function getObatRsValueAsText($data = [])
    {
        $obat_rs_text = '';
        $lastArr = end($data);
        $lastObat = ArrayHelper::getValue($lastArr, 'obatalkespasien_id');
        foreach ($data as $key => $value) {
            $key++;
            $obatAlkesPasienId = ArrayHelper::getValue($value, 'obatalkespasien_id');
            $namaObat = ArrayHelper::getValue($value, 'obatalkes_nama');
            $qtyObat = ArrayHelper::getValue($value, 'qty_transaksi');
            $signaObat = ArrayHelper::getValue($value, 'signa_nama');
            $str = ($lastObat == $obatAlkesPasienId) ? "\r" : "\r\n";
            $obat_rs_text .= $key.'. '.$namaObat.' '.$qtyObat.' '.$signaObat.$str;
        }
        
        return $obat_rs_text;
    }
    
}
