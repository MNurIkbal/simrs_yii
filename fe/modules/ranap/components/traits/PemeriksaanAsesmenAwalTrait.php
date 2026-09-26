<?php

namespace app\modules\ranap\components\traits;

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

use app\modules\ranap\models\AsesmenAwalForm;
use app\modules\ranap\models\SkriningGiziForm;
use app\components\Services\AksesFormService;
use app\modules\ranap\models\AsesmenKeperawatanForm;
use app\modules\ranap\models\AsesmenKeperawatanResikoJatuh;

use app\modules\ranap\models\SydneyForm;
use app\modules\ranap\models\HumptyDumptyForm;
use app\modules\ranap\models\MorseForm;

trait PemeriksaanAsesmenAwalTrait
{
    /**
     * This function will return new form of asesmen keperawatan
     *
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionAsesmenKeperawatan()
    {
        $pendaftaran_id = DocoHelpers::decrypt($this->_pendaftaran_id);
        $data = $this->guzzleExec($this->_restRanap, [
            'url' => 'pemeriksaan-rawat-inap/get-bundle-data-asesmen-keperawatan',
            'payload' => [
                'query' => [
                    'pendaftaran_id' => $pendaftaran_id,
                    'pasien_id' => $this->_pasien_id,
                    'pasienadmisi_id' => $this->_pasienadmisi_id
                ]
            ]
        ]);

        $dataBmi = $this->guzzleExec($this->_restRanap, [
            'url' => 'allow/data-bmi',
        ]);
        $jeniskelamin = $this->_jeniskelamin;
        $data_bmi     = empty($dataBmi['data-bmi']) ? [] : $dataBmi['data-bmi'];
        $data_bmi     = json_encode($data_bmi);

        $model = new AsesmenKeperawatanForm;
        $model->attributes = $data['asesmenkeperawatan'];
        if (!empty($model->diagnosa_keperawatan)) {
            $diagnosaKeperawatan = [];
            foreach (json_decode($model->diagnosa_keperawatan, true) as $index => $diagnosa) {
                if (empty($diagnosa['id']) && empty($diagnosa['kode'])) {
                    $id = $diagnosa['text'];
                } else {
                    $id = $diagnosa['id'] . '_' . $diagnosa['kode'] . ' - ' . $diagnosa['text'];
                }
                $diagnosaKeperawatan[$id] = (!empty($diagnosa['kode']) ? $diagnosa['kode'] . ' - ' : '') . $diagnosa['text'];
            }
            $model->diagnosa_keperawatan = $diagnosaKeperawatan;
        }
        $data['asesmenkeperawatan']['pendaftaran_id'] = Yii::$app->request->get('id');
        $modelResiko = new AsesmenKeperawatanResikoJatuh;
        $arrayConfig = $this->getConfig('asesmen_keperawatan_rd');
        if (isset($data['asesmenkeperawatan']['keluhan_utama'])) {
            $model->keluhan = $data['asesmenkeperawatan']['keluhan_utama'];
        }
        if (isset($data['asesmenkeperawatan']['r_penyakitsekarang'])) {
            $model->r_penyakitsaatini = $data['asesmenkeperawatan']['r_penyakitsekarang'];
        }
        if (isset($data['asesmenkeperawatan']['r_penyakitdahulu'])) {
            $r_penyakitdahulu = is_array($data['asesmenkeperawatan']['r_penyakitdahulu']) ?
                end($data['asesmenkeperawatan']['r_penyakitdahulu']) : $data['asesmenkeperawatan']['r_penyakitdahulu'];
            if (!empty($r_penyakitdahulu)) {
                $model->r_penyakitdahulu = empty($r_penyakitdahulu['penyakit']) ? null : $r_penyakitdahulu['penyakit'];
                $model->r_penyakitdahulu = empty($r_penyakitdahulu['terapi']) ? null : $r_penyakitdahulu['terapi'];
            }
        }
        if (isset($data['asesmenkeperawatan']['r_alergiobat']) || isset($data['asesmenkeperawatan']['alergi_lainnya'])) {
            $model->is_alergi = ((isset($data['asesmenkeperawatan']['r_alergiobat']) && !is_null($data['asesmenkeperawatan']['r_alergiobat'])) || (isset($data['asesmenkeperawatan']['alergi_lainnya']) && !is_null($data['asesmenkeperawatan']['alergi_lainnya']))) ? true : false;
            if (isset($data['asesmenkeperawatan']['r_alergiobat'])) {
                $model->is_alergiobat = !is_null($data['asesmenkeperawatan']['r_alergiobat']) ? true : false;
            }
            if (isset($data['asesmenkeperawatan']['alergi_lainnya'])) {
                $model->is_alergilainnya = !is_null($data['asesmenkeperawatan']['alergi_lainnya']) ? true : false;
            }
        }

        $tidak_ada_kelainan = 'tidak_ada_kelainan';
        if (!isset($data['asesmenkeperawatan']['survey_kepala'])) {
            $model->survey_kepala = $tidak_ada_kelainan;
        }
        if (!isset($data['asesmenkeperawatan']['survey_mata'])) {
            $model->survey_mata = $tidak_ada_kelainan;
        }
        if (!isset($data['asesmenkeperawatan']['survey_mulut'])) {
            $model->survey_mulut = $tidak_ada_kelainan;
        }
        if (!isset($data['asesmenkeperawatan']['survey_telinga'])) {
            $model->survey_telinga = $tidak_ada_kelainan;
        }
        if (!isset($data['asesmenkeperawatan']['survey_leher'])) {
            $model->survey_leher = $tidak_ada_kelainan;
        }
        if (!isset($data['asesmenkeperawatan']['survey_extremitas'])) {
            $model->survey_extremitas = $tidak_ada_kelainan;
        }
        if (!isset($data['asesmenkeperawatan']['survey_dada'])) {
            $model->survey_dada = $tidak_ada_kelainan;
        }
        if (!isset($data['asesmenkeperawatan']['survey_abdomen'])) {
            $model->survey_abdomen = $tidak_ada_kelainan;
        }

        if (!isset($data['asesmenkeperawatan']['survey_pelvis'])) {
            $model->survey_pelvis = $tidak_ada_kelainan;
        }
        if (!isset($data['asesmenkeperawatan']['survey_medulla_spinalis'])) {
            $model->survey_medulla_spinalis = $tidak_ada_kelainan;
        }
        if (!isset($data['asesmenkeperawatan']['survey_kolumna_vertebralis'])) {
            $model->survey_kolumna_vertebralis = $tidak_ada_kelainan;
        }

        $model->tgl_datang = date('d/m/Y H:i:s', strtotime($model->tgl_datang));
        $model->tgl_keluar = !empty($data["asesmenkeperawatan"]['tgl_keluar']) ? date('d/m/Y H:i:s', strtotime($data["asesmenkeperawatan"]['tgl_keluar'])) : date('d/m/Y H:i:s');

        // Disabled input validation
        $disabled = (!empty($this->_data_pasien['pasienpulang_id']) || $this->_data_pasien['is_stopakomodasi'] == true) ? true : false;
        $isStopAkomodasi = $this->_data_pasien['is_stopakomodasi'] ? 1 : 0;

        $status_disabled = $this->getStatusPeriksa($pendaftaran_id);
        $cekAkses = (new AksesFormService)->execute($this->_pasien_id, DocoConstants::FORM_ASESMEN_AWAL_KEP);
        $enable_edit = !empty($data['enable_pulang']) ? $data['enable_pulang'] : false;
        if ($cekAkses == true || $enable_edit) {
            $isStopAkomodasi = 0;
            $status_disabled = false;
            $disabled = false;
        }

        // Nurse validation
        $is_perawat = 0;
        if ($this->_user_identity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN) {
            $is_perawat = 1;
        }

        // Status Btn Verifikasi Gizi
        $disableGiziBtn = 1;
        if ($this->_user_identity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_GIZI && !$model->is_verifikasigizi && (!empty($model->nutrisi_1a) || !empty($model->nutrisi_1b) || !empty($model->nutrisi_2) || !empty($model->strongkids_kurus) || !empty($model->strongkids_turunbb) || !empty($model->strongkids_kondisikhusus) || !empty($model->strongkids_keadaan_beresiko))) {
            $disableGiziBtn = 0;
        }

        return $this->renderAjax('asesmen-keperawatan/__form', compact('model', 'arrayConfig', 'status_disabled', 'modelResiko', 'data', 'pendaftaran_id', 'data_bmi', 'jeniskelamin', 'disabled', 'isStopAkomodasi', 'is_perawat', 'disableGiziBtn'));
    }

    /**
     * This function will save data from view
     *
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionSaveAsesmenKeperawatan()
    {
        $model = new AsesmenKeperawatanForm;
        $request = Yii::$app->request->post();
        $session = Yii::$app->session;
        $cache = Yii::$app->cache;
        $request['tgl_datang'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request['tgl_datang'])));
        $request['tgl_keluar'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request['tgl_keluar'])));
        $model->attributes = $request;

        // Mapping asesmen allo or auto
        if ($model->allo_or_auto == '0') {
            $model->asesmen_auto = 1;
        } else if ($model->allo_or_auto == '1') {
            $model->asesmen_allo = 1;
        } else {
            $model->asesmen_auto = null;
            $model->asesmen_auto = null;
        }
        $model->allo_or_auto = null;

        if (!$model->validate()) {
            return $this->responseJson(422, 'Silakan cek kembali inputan.', $this->mapErrorForm($model->errors, 'AsesmenKeperawatanForm'));
        } else {
            $model->tinggi_badan = str_replace(',', '.', $model->tinggi_badan);
            $model->berat_badan = str_replace(',', '.', $model->berat_badan);
            $arrayPayload = [];
            if ($model->diagnosa_keperawatan) {
                $arrDiagnosa = [];
                foreach ($model->diagnosa_keperawatan as $index => $diagnosa) {
                    $explodeDiagnosa = explode(' - ', $diagnosa);
                    $diagnosaId = null;
                    $diagnosaKode = null;
                    $diagnosaText = $explodeDiagnosa[0];
                    if (count($explodeDiagnosa) > 1) {
                        $explodeDiagnosaIdText = explode('_', $explodeDiagnosa[0]);
                        $diagnosaId = isset($explodeDiagnosaIdText[0]) ? $explodeDiagnosaIdText[0] : '';
                        $diagnosaKode = isset($explodeDiagnosaIdText[1]) ? $explodeDiagnosaIdText[1] : '';
                        $diagnosaText = $explodeDiagnosa[1];
                    }
                    $arrDiagnosa[] = [
                        'id' => $diagnosaId,
                        'kode' => $diagnosaKode,
                        'text' => $diagnosaText
                    ];
                }
                $model->diagnosa_keperawatan = json_encode($arrDiagnosa);
            }
            foreach ($model->attributes as $fieldName => $valueField) {
                $arrayPayload[$fieldName] = is_null($valueField) ? '' : $valueField;
            }

            $result = $this->guzzleExec($this->_restRanap, [
                'url' => 'pemeriksaan-rawat-inap/save-asesmen',
                'method' => 'POST',
                'payload' => [
                    'form_params' => [
                        'formdata' => array_merge($arrayPayload, [
                            'pendaftaran_id' => $this->helper->decrypt($model->pendaftaran_id),
                            'pasienadmisi_id' => $this->_pasienadmisi_id,
                            'resiko_jatuh' => Yii::$app->request->post('resiko_jatuh')
                        ])
                    ]
                ]
            ]);

            // Integrate Skrining Gizi
            $pendaftaran_id = $this->helper->decrypt($request['pendaftaran_id']);
            $pasienadmisi_id = $this->_pasienadmisi_id;
            $postSkrining['pendaftaran_id'] = $pendaftaran_id;
            $postSkrining['pasienadmisi_id'] = $pasienadmisi_id;
            $postSkrining['SkriningGiziForm']['skor'] = $request['skor_gizi'];
            $submitSkrining = $this->guzzleExec($this->_restRanap, [
                'url' => 'pemeriksaan-rawat-inap/simpan-skrining-gizi',
                'method' => 'POST',
                'payload' => [
                    'form_params' => $postSkrining
                ]
            ]);

            // Reset Cache Data for Alergi
            
            $cache = Yii::$app->cache;
            $cacheData = $cache->get('pasien-pendaftaran-id-' . $request['pendaftaran_id']);
            $cacheData['alergi'] = (bool) $request['is_alergi'];
            $cacheData['skor'] = $request['skor_gizi'];
            $cache->set('pasien-pendaftaran-id-' . $request['pendaftaran_id'], $cacheData, 3600);

            $cache->delete('data-riwayat-pasien-' . $this->_data_pasien['pasien_id']); // reset data riwayat pasien ketika save data

            return $this->helper->response($result, 200);
        }
    }

    public function actionCetakAskepRd($id)
    {
        $path = Yii::getAlias("@download") . "/asesmen-keperawatan-igd-" . uniqid() . ".pdf";
        $pendaftaran_id = DocoHelpers::decrypt($id);
        $arrayConfig = $this->getConfig('asesmen_keperawatan_rd');

        $request = $this->_restRanap->post('pemeriksaan-rawat-inap/cetak-askep?' . http_build_query([
            'pendaftaran_id' => $pendaftaran_id,
            'pasienadmisi_id' => $this->_pasienadmisi_id,
        ]), [
            'form_params' => [
                'array_config' => $arrayConfig
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::previewPdf($path, null, true);
    }

    public function actionFormMorse($pendaftaran_id)
    {
        $modelMorse   = new MorseForm;
        $tanggal      = date('d-m-Y');
        $jam          = date('H:i');
        $totalTanggal = 1;
        $morseData = $this->guzzleExec($this->_restRanap, [
            'url' => 'pemeriksaan-rawat-inap/get-morse',
            'payload' => [
                'query' => compact('pendaftaran_id')
            ]
        ]);
        $morseData = empty($morseData['data']) ? [] : $morseData['data'];
        $historyData = $this->guzzleExec($this->_restRanap, [
            'url' => 'pemeriksaan-rawat-inap/get-history-morse',
            'payload' => [
                'query' => compact('pendaftaran_id')
            ]
        ]);

        foreach ($historyData['group'] as $key => $value) {
            $totalTanggal += $value['count'];
            $historyData['group'][$key]['tanggal'] = date('d-m-Y', strtotime($value['tanggal']));
        }

        foreach ($historyData['history'] as $key => $value) {
            $historyData['history'][$key]['jam'] = date('H:i', strtotime($value['jam']));
        }

        return $this->renderAjax('asesmen-keperawatan/modal/__morse', compact('modelMorse', 'pendaftaran_id', 'morseData', 'historyData', 'totalTanggal', 'tanggal', 'jam'));
    }

    /**
     * List dropdown of resiko jatuh
     *
     * @param Integer $page
     * @return JSON
     * @author Aris Munandar
     **/
    public function actionResikoJatuhList()
    {
        return $this->helper->guzzleExec($this->_restRanap, [
            'url' => 'pemeriksaan-rawat-inap/resiko-jatuh-list',
            'returnResponse' => true,
            'payload' => [
                'query' => Yii::$app->request->get('payload', [])
            ],
        ]);
    }

    public function actionSaveMorse()
    {
        $payload                   = Yii::$app->request->post();
        $pendaftaran_id            = Yii::$app->request->get('pendaftaran_id', null);
        $payload['pendaftaran_id'] = $pendaftaran_id;
        return $this->guzzleExec($this->_restRanap, [
            'url' => 'pemeriksaan-rawat-inap/save-morse',
            'returnResponse' => true,
            'method' => 'POST',
            'payload' => [
                'form_params' => [
                    'formdata' => $payload
                ]
            ]
        ]);
    }

    public function actionFormSydney($pendaftaran_id)
    {
        $modelSydney = new SydneyForm;
        $sydneyData = $this->guzzleExec($this->_restRanap, [
            'url' => 'pemeriksaan-rawat-inap/get-sydney',
            'payload' => [
                'query' => compact('pendaftaran_id')
            ]
        ]);
        $sydneyData = empty($sydneyData['data']) ? [] : $sydneyData['data'];
        $configVal = $this->getConfig('sydney');
        return $this->renderAjax('asesmen-keperawatan/modal/__sydney', compact('modelSydney', 'configVal', 'pendaftaran_id', 'sydneyData'));
    }

    public function actionSaveSydney()
    {
        $payload                   = Yii::$app->request->post();
        $pendaftaran_id            = Yii::$app->request->get('pendaftaran_id', null);
        $payload['pendaftaran_id'] = $pendaftaran_id;
        return $this->guzzleExec($this->_restRanap, [
            'url' => 'pemeriksaan-rawat-inap/save-sydney',
            'returnResponse' => true,
            'method' => 'POST',
            'payload' => [
                'form_params' => [
                    'formdata' => $payload
                ]
            ]
        ]);
    }

    public function actionFormDumpty($pendaftaran_id)
    {
        $modelDumpty  = new HumptyDumptyForm;
        $tanggal      = date('d-m-Y');
        $jam          = date('H:i');
        $totalTanggal = 1;
        $dumptyData = $this->guzzleExec($this->_restRanap, [
            'url' => 'pemeriksaan-rawat-inap/get-dumpty',
            'payload' => [
                'query' => compact('pendaftaran_id')
            ]
        ]);
        $dumptyData = empty($dumptyData['data']) ? [] : $dumptyData['data'];
        $historyData = $this->guzzleExec($this->_restRanap, [
            'url' => 'pemeriksaan-rawat-inap/get-history-dumpty',
            'payload' => [
                'query' => compact('pendaftaran_id')
            ]
        ]);

        foreach ($historyData['group'] as $key => $value) {
            $totalTanggal += $value['count'];
            $historyData['group'][$key]['tanggal'] = date('d-m-Y', strtotime($value['tanggal']));
        }

        foreach ($historyData['history'] as $key => $value) {
            $historyData['history'][$key]['jam'] = date('H:i', strtotime($value['jam']));
        }

        return $this->renderAjax('asesmen-keperawatan/modal/__dumpty', compact('modelDumpty', 'pendaftaran_id', 'dumptyData', 'historyData', 'totalTanggal', 'tanggal', 'jam'));
    }

    public function actionSaveDumpty()
    {
        $payload                   = Yii::$app->request->post();
        $pendaftaran_id            = Yii::$app->request->get('pendaftaran_id', null);
        $payload['pendaftaran_id'] = $pendaftaran_id;
        return $this->guzzleExec($this->_restRanap, [
            'url' => 'pemeriksaan-rawat-inap/save-dumpty',
            'returnResponse' => true,
            'method' => 'POST',
            'payload' => [
                'form_params' => [
                    'formdata' => $payload
                ]
            ]
        ]);
    }

    public function actionAsesmenawal()
    {
        $pendaftaran_id = $this->_pendaftaran_id;
        $decryptPendaftaranId = DocoHelpers::decrypt($this->_pendaftaran_id);
        $modelAsesmen = new AsesmenAwalForm;
        $modelSkrining = new SkriningGiziForm;
        $request = Yii::$app->request;
        $disabled = (!empty($this->_data_pasien['pasienpulang_id']) || $this->_data_pasien['is_stopakomodasi'] == true) ? true : false;
        if ($request->post('submit-sementara')) {
            $post_sementara = $request->post();
            $post_sementara['pendaftaran_id'] = $decryptPendaftaranId;
            $post_sementara['pasien_id'] = $this->_pasien_id;
            $post_sementara['pasienadmisi_id'] = $this->_pasienadmisi_id;
            $post_sementara['AsesmenAwalForm']['waktu_tiba'] = $post_sementara['AsesmenAwalForm']['waktu_tiba'] == '' ? null : date('Y-m-d H:i:s', strtotime($post_sementara['AsesmenAwalForm']['waktu_tiba']));
            $post_sementara['AsesmenAwalForm']['tgl_asesmen'] = $post_sementara['AsesmenAwalForm']['tgl_asesmen'] == '' ? null : date('Y-m-d H:i:s', strtotime($post_sementara['AsesmenAwalForm']['tgl_asesmen']));
            $post_sementara['AsesmenAwalForm']['hpht'] = $post_sementara['AsesmenAwalForm']['hpht'] == '' ? null : date('Y-m-d H:i:s', strtotime($post_sementara['AsesmenAwalForm']['hpht']));
            $post_sementara['AsesmenAwalForm']['tgl_dirawat'] = $post_sementara['AsesmenAwalForm']['tgl_dirawat'] == '' ? null : date('Y-m-d H:i:s', strtotime($post_sementara['AsesmenAwalForm']['tgl_dirawat']));
            $post_sementara['AsesmenAwalForm']['tgl_tindakan'] = $post_sementara['AsesmenAwalForm']['tgl_tindakan'] == '' ? null : date('Y-m-d H:i:s', strtotime($post_sementara['AsesmenAwalForm']['tgl_tindakan']));
            // $post_sementara['AsesmenAwalForm']['diagnosa_masuk'] = $post_sementara['diagnosa_masuk'];
            $resSubmit = $this->_restRanap->post('pemeriksaan-rawat-inap/simpan-sementara', [
                'form_params' => $post_sementara
            ]);
            $body = json_decode($resSubmit->getBody(), true);
            return DocoHelpers::response($body, false, true);
        }

        $response = $this->_restRanap->get('pemeriksaan-rawat-inap/get-bundle-data-asesmen-nyeri', ['query' => ['pendaftaran_id' => $decryptPendaftaranId, 'pasien_id' => $this->_pasien_id, 'pasienadmisi_id' => $this->_pasienadmisi_id]]);
        $response = json_decode($response->getBody(), true);
        $list_asesmen_diambildari = $response['response']['list_asesmen_diambildari'];
        $list_masuk_dengan = $response['response']['list_masuk_dengan'];
        $list_ya_tidak = [0 => 'Tidak', 1 => 'Ya'];
        $list_ada_tidak = [0 => 'Tidak Ada', 1 => 'Ada'];
        $list_penurunan_bb = DocoConstants::PENURUNAN_BB;
        $rawdata_gizi_bbturun = $response['response']['list_skgizi_bbturun'];
        $list_gizi_bbturun = ArrayHelper::map($rawdata_gizi_bbturun, 'lookupkeperawatan_id', 'lookup_value');
        $skor_gizi_bbturun = json_encode(ArrayHelper::map($rawdata_gizi_bbturun, 'lookupkeperawatan_id', 'lookup_name'));
        $rawdata_gizi_porsi = $response['response']['list_skgizi_porsi'];
        $list_gizi_porsi = ArrayHelper::map($rawdata_gizi_porsi, 'lookupkeperawatan_id', 'lookup_name');
        $skor_gizi_porsi = json_encode(ArrayHelper::map($rawdata_gizi_porsi, 'lookupkeperawatan_id', 'lookup_value'));
        $rawdata_gizi_sakitberat = $response['response']['list_skgizi_sakitberat'];
        $list_gizi_sakitberat = ArrayHelper::map($rawdata_gizi_sakitberat, 'lookupkeperawatan_id', 'lookup_name');
        $skor_gizi_sakitberat = json_encode(ArrayHelper::map($rawdata_gizi_sakitberat, 'lookupkeperawatan_id', 'lookup_value'));
        $list_ketergantungan = $response['response']['list_ketergantungan'];
        $list_r_penyakit_kel = $response['response']['list_r_penyakit_kel'];
        $list_jenis_operasi = $response['response']['list_jenis_operasi'];
        $list_diagnosa_masuk = [];
        $list_haid = [1 => 'Teratur', 0 => 'Tidak Teratur'];
        $tanggal_mulai = isset($this->_data_pasien['tgl_admisi']) ? date('d-m-Y H:i:s', strtotime($this->_data_pasien['tgl_admisi'])) : date('d-m-Y H:i:s');

        $is_update_asesmen = false;
        if (isset($response['response']['data_asesmen_awal'])) {
            $dataasesmenawal = $response['response']['data_asesmen_awal'];
            $modelAsesmen->attributes = $dataasesmenawal;
            $modelAsesmen->ketergantungan = isset($modelAsesmen->ketergantungan) ? explode(',', $modelAsesmen->ketergantungan) : null;
            $modelAsesmen->r_penyakit_kel = isset($modelAsesmen->r_penyakit_kel) ? explode(',', $modelAsesmen->r_penyakit_kel) : null;
        }
        $datainit_asmenriwayat = isset($response['response']['data_asmen_riwayat']) ? $response['response']['data_asmen_riwayat'] : [];

        $is_perawat = 1;
        if ($this->_user_identity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN) {
            $is_perawat = 1;
        }
        $hide = 'show()';
        // $status_disabled = $this->getStatusPeriksa($decryptPendaftaranId);
        $status_disabled = 'false';
        if ($status_disabled == true) {
            $hide = 'hide()';
        } else {
            $status_disabled = 'false';
        }
        if (isset($response['response']['data_skrining'])) {
            $data_response_skrining = $response['response']['data_skrining'];
            $modelSkrining->attributes = $data_response_skrining;
            if (isset($data_response_skrining['is_active']) && $data_response_skrining['is_active'] == TRUE) {
                $is_update_asesmen = true;
                $status_disabled = TRUE;
            }
        }

        $init_diagnosa = '';
        if (isset($response['response']['diagnosa']) && $response['response']['diagnosa'] != null) {
            $diagnosa = $response['response']['diagnosa'];
            $init_diagnosa = $diagnosa['diagnosa_nama'];
        }
        $isStopAkomodasi = $this->_data_pasien['is_stopakomodasi'] ? 1 : 0;
        $cekAkses = (new AksesFormService)->execute($this->_data_pasien['pasien_id'], DocoConstants::FORM_ASESMEN_AWAL_KEP);
        if ($cekAkses == true) {
            $isStopAkomodasi = 0;
            $status_disabled = 'false';
            $disabled = false;
        }

        return $this->renderAjax('asesmen-awal-keperawatan/__asesmenawal', [
            'pendaftaran_id' => $pendaftaran_id,
            'modelAsesmen' => $modelAsesmen,
            'list_asesmen_diambildari' => $list_asesmen_diambildari,
            'list_masuk_dengan' => $list_masuk_dengan,
            'list_ada_tidak' => $list_ada_tidak,
            'list_ya_tidak' => $list_ya_tidak,
            'list_haid' => $list_haid,
            'list_ketergantungan' => $list_ketergantungan,
            'list_r_penyakit_kel' => $list_r_penyakit_kel,
            'datainit_asmenriwayat' => $datainit_asmenriwayat,
            'data_pasien' => $this->_data_pasien,
            'list_jenis_operasi' => $list_jenis_operasi,
            'tanggal_mulai' => $tanggal_mulai,
            'list_diagnosa_masuk' => $list_diagnosa_masuk,
            'list_gizi_bbturun' => $list_gizi_bbturun,
            'list_penurunan_bb' => $list_penurunan_bb,
            'list_gizi_porsi' => $list_gizi_porsi,
            'list_gizi_sakitberat' => $list_gizi_sakitberat,
            'skor_gizi_bbturun' => $skor_gizi_bbturun,
            'skor_gizi_porsi' => $skor_gizi_porsi,
            'skor_gizi_sakitberat' => $skor_gizi_sakitberat,
            'is_perawat' => $is_perawat,
            'is_update_asesmen' => $is_update_asesmen,
            'status_disabled' => $status_disabled,
            'hide' => $hide,
            'init_diagnosa' => $init_diagnosa,
            'modelSkrining' => $modelSkrining,
            'disabled' => $disabled,
            'isStopAkomodasi' => $isStopAkomodasi
        ]);
    }

    public function actionGetDiagnosisMasuk()
    {
        if (isset($_GET['q']['term']) && !empty($_GET['q']['term'])) {

            $response = $this->_restRanap->request('POST', 'pemeriksaan-rawat-inap/view-diagnosis-masuk', [
                'form_params' => ['keyword' => $_GET['q']['term']],
            ]);
            $body = json_decode($response->getBody(), true);

            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = ['id' => $value['diagnosa_id'], 'text' => $value['diagnosa_nama']];
            }
            $total = count($body['response']);
            $return = ['result' => $data, 'total_count' => $total, 'incomplete_results' => false];
            return DocoHelpers::response($return);
        }
    }

    public function actionPeriksaFisik()
    {
        $pendaftaran_id = $this->_pendaftaran_id;
        $modelAsesmen = new AsesmenAwalForm;


        $response = $this->_restRanap->get('pemeriksaan-rawat-inap/get-bundle-data-asesmen-nyeri');
        $response = json_decode($response->getBody(), true);
        $list_asesmen_diambildari = $response['response']['list_asesmen_diambildari'];
        $list_masuk_dengan = $response['response']['list_masuk_dengan'];
        $list_ya_tidak = [0 => 'Tidak', 1 => 'Ya'];
        $list_ada_tidak = [0 => 'Tidak Ada', 1 => 'Ada'];
        $list_haid = [0 => 'Teratur', 1 => 'Tidak Teratur'];
        $list_ketergantungan = $response['response']['list_ketergantungan'];
        $list_r_penyakit_kel = $response['response']['list_r_penyakit_kel'];


        return $this->renderAjax('asesmen-awal-keperawatan/__periksafisik', [
            'pendaftaran_id' => $pendaftaran_id,
            'modelAsesmen' => $modelAsesmen,
            'list_asesmen_diambildari' => $list_asesmen_diambildari,
            'list_masuk_dengan' => $list_masuk_dengan,
            'list_ada_tidak' => $list_ada_tidak,
            'list_ya_tidak' => $list_ya_tidak,
            'list_haid' => $list_haid,
            'list_ketergantungan' => $list_ketergantungan,
            'list_r_penyakit_kel' => $list_r_penyakit_kel
        ]);
    }

    public function actionCetakPdfAsesmenAwal()
    {

        $params = Yii::$app->request;
        $pendaftaran_id = DocoHelpers::decrypt($params->get('id', 'MA'));
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $pasienadmisi_id = $this->_pasienadmisi_id;
        $path = Yii::getAlias("@download") . "/instruksi-implementasi.pdf";
        $nama_user = Yii::$app->session->get('user_identity')['nama'];
        try {
            $response = $this->_restRanap->get('pemeriksaan-rawat-inap/cetak-pdf-asesmen-awal', [
                'query' => [
                    'pendaftaran_id' => $pendaftaran_id,
                    'ruangan_id' => $ruangan_id,
                    'pasienadmisi_id' => $pasienadmisi_id,
                    'nama_user' => $nama_user
                ],
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            var_dump($e->getMessage());
            die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionSimpanSkriningGizi()
    {
        try {
            $request = Yii::$app->request;
            $post_sementara = $request->post();
            $decryptPendaftaranId = DocoHelpers::decrypt($this->_pendaftaran_id);
            $post_sementara['pendaftaran_id'] = $decryptPendaftaranId;
            $post_sementara['pasien_id'] = $this->_pasien_id;
            $post_sementara['pasienadmisi_id'] = $this->_pasienadmisi_id;

            $skring_gizi_data = $request->post('SkriningGiziForm');
            $modelSkrining = new SkriningGiziForm;
            $modelSkrining->attributes = $skring_gizi_data;
            if (!$modelSkrining->validate()) {
                $errors = DocoHelpers::parseError($model->errors, 'SkriningGiziForm');
                return DocoHelpers::response([
                    'response' => [
                        'data' => $errors
                    ]
                ], 422);
            }
            $resSubmit = $this->_restRanap->post('pemeriksaan-rawat-inap/simpan-skrining-gizi', [
                'form_params' => $post_sementara
            ]);
            $body = json_decode($resSubmit->getBody(), true);
            return DocoHelpers::response($body, false, true);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(400, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionSimpanAwalKeperawatan()
    {
        try {
            $request = Yii::$app->request;
            $encryptedPendaftaranId = $request->get('id', 'MQ');
            $pendaftaran_id = DocoHelpers::decrypt($encryptedPendaftaranId);
            $pasien_id = $this->_pasien_id;
            $pasienadmisi_id = $this->_pasienadmisi_id;
            $cache = Yii::$app->cache;

            $asesmen_keperawatan_data = $request->post('AsesmenAwalForm', []);
            $post_asesmen = [];
            $post_asesmen['pendaftaran_id'] = $pendaftaran_id;
            $post_asesmen['pasien_id'] = $this->_pasien_id;
            $post_asesmen['pasienadmisi_id'] = $this->_pasienadmisi_id;
            $asesmen_keperawatan_data['waktu_tiba'] = $asesmen_keperawatan_data['waktu_tiba'] == '' ? null : date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $asesmen_keperawatan_data['waktu_tiba'])));
            $asesmen_keperawatan_data['tgl_asesmen'] = $asesmen_keperawatan_data['tgl_asesmen'] == '' ? null : date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $asesmen_keperawatan_data['tgl_asesmen'])));
            $asesmen_keperawatan_data['hpht'] = $asesmen_keperawatan_data['hpht'] == '' ? null : date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $asesmen_keperawatan_data['hpht'])));
            $asesmen_keperawatan_data['tgl_dirawat'] = $asesmen_keperawatan_data['tgl_dirawat'] == '' ? null : date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $asesmen_keperawatan_data['tgl_dirawat'])));
            $asesmen_keperawatan_data['tgl_tindakan'] = $asesmen_keperawatan_data['tgl_tindakan'] == '' ? null : date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $asesmen_keperawatan_data['tgl_tindakan'])));

            $modelAsesmen = new AsesmenAwalForm;
            $modelAsesmen->attributes = $asesmen_keperawatan_data;

            if (!$modelAsesmen->validate()) {
                $errors = DocoHelpers::parseError($modelAsesmen->errors, 'AsesmenAwalForm');
                return DocoHelpers::response([
                    'response' => [
                        'data' => $errors
                    ]
                ], 422);
            }
            $post_asesmen['AsesmenAwalForm'] = $asesmen_keperawatan_data;
            $submitAsesmen = $this->_restRanap->post('pemeriksaan-rawat-inap/simpan-sementara', [
                'form_params' => $post_asesmen
            ]);
            $responseAsesmen = json_decode($submitAsesmen->getBody(), true);

            $skring_gizi_data = $request->post('SkriningGiziForm', []);
            Yii::error(compact('skring_gizi_data'));
            // $post_skrining = $skring_gizi_data;
            $post_skrining['pendaftaran_id'] = $pendaftaran_id;
            $post_skrining['pasien_id'] = $pasien_id;
            $post_skrining['pasienadmisi_id'] = $pasienadmisi_id;
            $modelSkrining = new SkriningGiziForm;
            $modelSkrining->attributes = $skring_gizi_data;
            Yii::error(['model' => $modelSkrining->attributes]);
            if (!$modelSkrining->validate()) {
                $errors = DocoHelpers::parseError($modelSkrining->errors, 'SkriningGiziForm');
                return DocoHelpers::response([
                    'response' => [
                        'data' => $errors
                    ]
                ], 422);
            }
            $post_skrining['SkriningGiziForm'] = $skring_gizi_data;
            $submitSkrining = $this->_restRanap->post('pemeriksaan-rawat-inap/simpan-skrining-gizi', [
                'form_params' => $post_skrining
            ]);
            $responseSkrining = json_decode($submitSkrining->getBody(), true);

            $cache = Yii::$app->cache;
            $cacheData = $cache->get('pasien-pendaftaran-id-' . $encryptedPendaftaranId);
            $cacheData['alergi'] = (bool) $asesmen_keperawatan_data['r_alergi'];
            $cache->set('pasien-pendaftaran-id-' . $encryptedPendaftaranId, $cacheData, 3600);

            $response = ['metadata' => ['status' => 200], 'response' => ['message' => 'Berhasil', 'text' => 'Berhasil']];
            return DocoHelpers::response($response, false, true);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(400, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400, 'Terjadi Kesalahan pada server.');
        }
    }
}
