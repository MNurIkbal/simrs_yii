<?php

/**
 * @Author: Sigit
 * @Date:   2018-07-03 14:26:01
 */

// Namespace
namespace app\modules\ranap\components\traits;

// Using Yii
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;

// Using Guzzles
use function GuzzleHttp\json_encode;
use GuzzleHttp\Exception\RequestException;

// Using components
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DHtml;
use app\components\Pelayanan\PelayananHelpers;

// Using model
use app\modules\ranap\models\CpptForm;
use app\modules\ranap\models\VerbalOrderRanapForm;
use app\modules\ranap\models\CpptFormVerbalOrder;
use app\modules\ranap\models\InstruksiForm;
use app\modules\ranap\models\InstruksiTindakanForm;
use app\modules\ranap\models\InstruksiTindakanBmhpForm;
use app\modules\ranap\models\ObatAlkesPasienForm;
use app\modules\ranap\models\ResepturForm;
use app\modules\ranap\models\ResepturDetailForm;
use app\modules\ranap\models\ResepturNrDetailForm;
use app\modules\ranap\models\TindakankomponenForm;
use app\modules\ranap\models\TindakanPelayananForm;
use app\modules\ranap\models\InstruksiPenunjangForm;
use app\modules\ranap\models\JadwalOperasiForm;
use app\modules\ranap\models\InstruksiDpjpForm;

// Trait
trait PemeriksaanCpptTrait
{
    private function checkProgramFisio($pasienId)
    {
        $helpers = new DocoHelpers();
        $response = $helpers->guzzleExec(Yii::$app->docoRest->fisioterapi, [
            'method' => 'GET',
            'url' => 'program-fisioterapi/check-sisa-program-ranap',
            'payload' => [
                'query' => ['pasien_id' => $pasienId]
            ],
        ]);
        $countSisaAllProgram = ArrayHelper::getValue($response, 'data.count_sisa_all_program', 0);
        $isMustConfirm = false;
        if ($countSisaAllProgram > 0) {
            $isMustConfirm = true;
        }
        return $isMustConfirm;
    }

    // Cppt
    public function actionCppt()
    {
        // Try catch
        try {
            // Get params
            $params = Yii::$app->request;
            $userIdentity = Yii::$app->session->get('user_identity');
            $classKelompokpegawai = 'hidden';
            $is_perawat = false;
            if (isset($userIdentity['kelompokpegawai_id']) && $userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN) {
                $classKelompokpegawai = '';
                $is_perawat = true;
            }
            $isEditTindakan = false;
            $isEditBmhp = false;
            $id_jenisinstruksi = 0;
            $data_pasien = $this->_data_pasien;

            // Deklarasi model
            $model = new CpptForm;
            $modelInstruksi = new InstruksiForm;
            $modelInstruksiTindakan = new InstruksiTindakanForm;
            $modelTindakanPelayanan = new TindakanPelayananForm;
            $modelTindakanKomponen = new TindakankomponenForm;
            $modelBmhp = new ObatAlkesPasienForm;
            $modelReseptur = new ResepturForm;
            $modelResepturDetailRacikan = new ResepturDetailForm;
            $modelResepturDetailNonRacikan = new ResepturNrDetailForm;
            $modelVerbalOrder = new VerbalOrderRanapForm;
            $disabled = (!empty($this->_data_pasien['pasienpulang_id']) || $this->_data_pasien['is_stopakomodasi'] == true) ? true : false;

            $listRuangan[$this->_data_pasien['ruangan_id'] . '@#' . @$this->_data_pasien['kamarruangan_id'] . '@#' . @$this->_data_pasien['kamartempattidur_id'] . '@#' . $this->_data_pasien['kamarruangan_nokamar'] . ' | ' . $this->_data_pasien['no_tempattidur']] = $this->_data_pasien['ruangan_nama'] . ' | ' . $this->_data_pasien['kamarruangan_nokamar'] . ' | ' . $this->_data_pasien['no_tempattidur'];

            // Get list data
            $listData = $this->getListDataCppt($this->_pasien_id, $this->_pegawai_id, $this->_id_ruangan, $this->_data_pasien['pendaftaran_id'], $this->_pasienadmisi_id);
            $tempListRuangan = ArrayHelper::map($listData['listRuangan'], 'ruangan_id', 'ruangan_nama');
            $listDiagnosa = ArrayHelper::map($listData['listDiagnosa'], 'diagnosa_id', 'diagnosa_nama');
            $listPemberiInstruksi = ArrayHelper::map($listData['listPemberiInstruksi'], 'pegawai_id', 'nama_pegawai');
            $listDataSigna = ArrayHelper::map($listData['listDataSigna'], 'signa_id', 'signa_nama');
            $listDataApotek = ArrayHelper::map($listData['listDataApotek'], 'ruangan_id', 'ruangan_nama');
            $listDataTerapi = ArrayHelper::map($listData['listDataTerapi'], 'lookup_id', 'lookup_name');
            $pegawai_id = $this->_pegawai_id;
            $isExistTindakan = isset($listData['isExistTindakan']) ? $listData['isExistTindakan'] : 0;

            $autofill_diagnose = [];
            // Set default value ke model
            if (!is_null(@$listData['cpptInactive'])) {
                $model->scenario = CpptForm::SUBMIT_AUTO;
                $model->attributes = $listData['cpptInactive'];
                $model->a_diag_utama = null;
                $model->a_diag_penyerta = null;
                $autofill_diagnose = [
                    'primary' => ArrayHelper::getValue($listData, 'cpptInactive.a_diag_utama'),
                    'secondary' => ArrayHelper::getValue($listData, 'cpptInactive.a_diag_penyerta'),
                ];
            }
            $model->pendaftaran_id = $this->_data_pasien['pendaftaran_id'];
            $model->pasienadmisi_id = $this->_data_pasien['pasienadmisi_id'];
            $model->pasien_id = $this->_data_pasien['pasien_id'];
            $model->ruangan_id = $this->_data_pasien['ruangan_id'] . '@#' . @$this->_data_pasien['kamarruangan_id'] . '@#' . @$this->_data_pasien['kamartempattidur_id'] . '@#' . $this->_data_pasien['kamarruangan_nokamar'] . ' | ' . $this->_data_pasien['no_tempattidur'];
            $model->pegawai_id = $this->_pegawai_id;
            $model->tgl_cppt = date('Y-m-d H:i:s');

            // Set model verbal order
            $modelVerbalOrder->pendaftaran_id = $this->_data_pasien['pendaftaran_id'];
            $modelVerbalOrder->pasienadmisi_id = $this->_data_pasien['pasienadmisi_id'];
            $modelVerbalOrder->pasien_id = $this->_data_pasien['pasien_id'];
            $modelVerbalOrder->ruangan_id = $this->_data_pasien['ruangan_id'];
            $modelVerbalOrder->pegawai_id = $this->_pegawai_id;
            $modelVerbalOrder->tgl_cppt = date('Y-m-d H:i:s');

            // Set model reseptur
            $modelReseptur->pasienadmisi_id = $this->_data_pasien['pasienadmisi_id'];
            $modelReseptur->berat_badan = $this->_data_pasien['berat_badan'];
            $modelReseptur->tinggi_badan = $this->_data_pasien['tinggi_badan'];
            $modelReseptur->luas_tubuh = $this->_data_pasien['luas_permukaantubuh'];
            $modelReseptur->pegawai_id = $this->_data_pasien['dokter_admisi_id'];
            $modelReseptur->tglreseptur = date('d F, Y', strtotime("NOW"));

            // Set default value ke model instruksi
            $modelInstruksi->tgl_instruksi = date('Y-m-d H:i:s', strtotime('NOW'));

            // Assign temp list ruangan ke list ruangan
            // Cek temp list ruangan
            if (!empty($listData['listRuangan'])) {
                // Looping
                foreach ($listData['listRuangan'] as $value) {
                    // Set temp list ruangan
                    $listRuangan[$value['ruangan_id'] . '@#' . $value['kamarruangan_id'] . '@#' . $value['kamartempattidur_id'] . '@#' . $value['kamarruangan_nokamar'] . ' | ' . $value['no_tempattidur']] = $value['ruangan_nama'] . ' | ' . $value['kamarruangan_nokamar'] . ' | ' . $value['no_tempattidur'];
                }
            }

            $hide = 'show()';
            $status_disabled = $this->getStatusPeriksaRanap();
            if ($status_disabled == true) {
                $hide = 'hide()';
            }
            if (isset($listData['suggestion'])) {
                $model->subject = $listData['suggestion']['subject'] != '' ? $listData['suggestion']['subject'] : $model->subject;
                $model->object = $listData['suggestion']['object'] != '' ? $listData['suggestion']['object'] : $model->object;
                $model->planning = $listData['suggestion']['planning'] != '' ? $listData['suggestion']['planning'] : $model->planning;
            }
            $lastCppt = DocoHelpers::encrypt($listData['lastCppt']);

            $response = $this->_restRanap->get('allow/get-data-to-cppt', [
                'query' => [
                    'kode_lookup' => DocoConstants::TIME_RESET_SUGGEST_SOAP,
                ],
            ]);
            $response = json_decode($response->getBody(), true);
            $response = ArrayHelper::getValue($response, 'response', []);
            
            $pelayananConfigButton = ArrayHelper::getValue($response, 'pelayananConfigButton', []);
            $pelayananConfigButton = ArrayHelper::getValue($pelayananConfigButton, 'data', []);
            $timeResetSuggestSoap = ArrayHelper::getValue($response, 'timeResetSuggestSoap');
            $konfigSystem = ArrayHelper::getValue($response, 'konfigSystem', []);
            
            $isPerawat = Yii::$app->docoVars->user('kelompokpegawai_id') == DocoConstants::KELOMPOK_KEPERAWATAN;

            if (!empty($pelayananConfigButton)) {
                foreach ($pelayananConfigButton as $key => $pelayanan) {
                    $dataPelayanan = [];
                    $dataPelayanan['name'] = $pelayanan['nama_fitur'];
                    $dataPelayanan['title'] = $pelayanan['title'];
                    $pelayanan['additional_data'] = json_decode($pelayanan['additional_data'], true);
                    $additional = empty($pelayanan['additional_data']) ? [] : $pelayanan['additional_data'];
                    $dataPelayanan = array_merge($dataPelayanan, $additional);
                    if ($isPerawat && !$pelayanan['is_perawat']) {
                        $dataPelayanan['disabled'] = true;
                    } else if (!$isPerawat && !$pelayanan['is_dokter']) {
                        $dataPelayanan['disabled'] = true;
                    }
                    if ($this->_data_pasien['is_stopakomodasi']) {
                        $dataPelayanan['disabled'] = true;
                    }

                    $pelayanan['additional_condition'] = json_decode($pelayanan['additional_condition'], true);
                    if (isset($pelayanan['additional_condition']['akses']) && is_array($pelayanan['additional_condition']['akses'])) {
                        $hasAkses = false;
                        foreach ($pelayanan['additional_condition']['akses'] as $module => $akses) {
                            if (DHtml::hasAkses($module, $akses)) {
                                $hasAkses = true;
                            }
                        }
                        if (!$hasAkses) {
                            $dataPelayanan['disabled'] = true;
                        }
                    }

                    if (isset($pelayanan['additional_condition']['data_pasien']) && is_array($pelayanan['additional_condition']['data_pasien'])) {
                        foreach ($pelayanan['additional_condition']['data_pasien'] as $attribute => $value) {
                            if(is_array($value['values'])) {
                                foreach ($value['values'] as $val) {
                                    if (
                                        $data_pasien[$attribute] == $val 
                                        && ((isset($value['is_stopakomodasi']) && $value['is_stopakomodasi'] == $data_pasien['is_stopakomodasi']) || !isset($value['is_stopakomodasi']))
                                    ) {
                                        $dataPelayanan[$value['attr']] = $value['attr_value'];
                                    }
                                }
                            } else {
                                if(
                                    $data_pasien[$attribute] == $value['values']
                                    && ((isset($value['is_stopakomodasi']) && $value['is_stopakomodasi'] == $data_pasien['is_stopakomodasi']) || !isset($value['is_stopakomodasi']))
                                ) {
                                    $dataPelayanan[$value['attr']] = $value['attr_value'];
                                }
                            }
                        }
                    }

                    $pelayananConfigButton[$key] = $dataPelayanan;
                }
            }

            $time_reset = !empty($timeResetSuggestSoap) ? $timeResetSuggestSoap : 1440;
            $pasien_encrypt_id = DocoHelpers::encrypt($data_pasien['pasien_id']);
            $total_belum_baca_rad = ArrayHelper::getValue($listData, 'total_belum_baca', 0);

            $pasienId = ArrayHelper::getValue($this->_data_pasien, 'pasien_id');
            $hasFisioterapiModule  = Yii::$app->docoRest->fisioterapi->getConfig('base_uri')->getPath();
            $isMustCheckProgramFisio = false;
            if (!empty($hasFisioterapiModule)) {
                $isMustCheckProgramFisio = $this->checkProgramFisio($pasienId);
            }
            
            $config_soap = ArrayHelper::getValue($konfigSystem, 'hide_instruksi_soap');

            return $this->renderAjax('cppt/index', [
                'is_perawat' => $is_perawat,
                'id_ruangan' => $this->_id_ruangan,
                'id_instalasi' => $this->_instalasi_id,
                'pendaftaran_id' => $this->_pendaftaran_id,
                'model' => $model,
                'modelInstruksi' => $modelInstruksi,
                'modelInstruksiTindakan' => $modelInstruksiTindakan,
                'modelBmhp' => $modelBmhp,
                'modelTindakanKomponen' => $modelTindakanKomponen,
                'modelTindakanPelayanan' => $modelTindakanPelayanan,
                'modelReseptur' => $modelReseptur,
                'modelResepturDetailRacikan' => $modelResepturDetailRacikan,
                'modelResepturDetailNonRacikan' => $modelResepturDetailNonRacikan,
                'listRuangan' => $listRuangan,
                'listDiagnosa' => $listDiagnosa,
                'listPemberiInstruksi' => $listPemberiInstruksi,
                'pegawai_id' => $pegawai_id,
                'listDataSigna' => $listDataSigna,
                'listDataApotek' => $listDataApotek,
                'listDataTerapi' => $listDataTerapi,
                'listPemberiInstruksi' => $listPemberiInstruksi,
                'data_pasien' => $this->_data_pasien,
                'modelBmhp' => $modelBmhp,
                'modelTindakanKomponen' => $modelTindakanKomponen,
                'modelTindakanPelayanan' => $modelTindakanPelayanan,
                'modelVerbalOrder' => $modelVerbalOrder,
                'count_riwayat' => $isExistTindakan,
                'data_tindakanbmhp' => [],
                'classKelompokpegawai' => $classKelompokpegawai,
                'isEditTindakan' => $isEditTindakan,
                'isEditBmhp' => $isEditBmhp,
                'id_jenisinstruksi' => $id_jenisinstruksi,
                'status_disabled' => $status_disabled,
                'hide' => $hide,
                'disabled' => $disabled,
                'lastCppt' => $lastCppt,
                'tgl_pendaftaran' => $this->_data_pasien['tgl_pendaftaran'],
                'cpptInactive' => @$listData['cpptInactive'],
                'autofill_diagnose' => $autofill_diagnose,
                'isStopAkomodasi' => $this->_data_pasien['is_stopakomodasi'] ? 1 : 0,
                'pelayananConfigButton' => $pelayananConfigButton,
                'time_reset' => $time_reset,
                'pasien_encrypt_id' => $pasien_encrypt_id,
                'total_belum_baca_rad' => $total_belum_baca_rad,
                'is_must_check_program_fisio' => $isMustCheckProgramFisio,
                'config_soap' => $config_soap
            ]);
        } catch (RequestException $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (\Exception $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    public function actionGetDataResepturSession()
    {
        $session = Yii::$app->session;
        $request = Yii::$app->request;

        $pendaftaran_id_encrypt = $request->get('id');
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id_encrypt);
        $cppt_id = $request->get('cppt_id');
        $ruangan_id = $request->get('ruangan_id');

        try {
            if (isset($session['pemeriksaan_reseptur'][$pendaftaran_id_encrypt . $cppt_id])) {
                $temp_session = $session['pemeriksaan_reseptur'][$pendaftaran_id_encrypt . $cppt_id];

                $data_tables = $this->generateTableReseptur($pendaftaran_id_encrypt, $temp_session, $cppt_id, $ruangan_id);

                return DocoHelpers::response($data_tables);
            } else {
                return DocoHelpers::dataTabelsException('Data kosong');
            }
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    // Batal reseptur
    public function actionBatalSessionReseptur()
    {
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $errors = 'Terdapat kesalahan';

        try {
            $sess_id = $request->get('sess_key');
            $obatalkes_id = null;

            $pendaftaran_id_encrypt = $request->get('pendaftaran_id');
            $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id_encrypt);
            $cppt_id = $request->get('cppt_id');

            if (isset($session['pemeriksaan_reseptur'])) {
                $session_reseptur = $session['pemeriksaan_reseptur'];
                if (isset($session_reseptur[$pendaftaran_id_encrypt . $cppt_id])) {
                    foreach ($session_reseptur[$pendaftaran_id_encrypt . $cppt_id] as $key => $value) {
                        if ($value['session_key'] == $sess_id) {
                            $reseptur = $session_reseptur[$pendaftaran_id_encrypt . $cppt_id][$key];
                            unset($session_reseptur[$pendaftaran_id_encrypt . $cppt_id][$key]);
                        }
                    }
                    $session->set('pemeriksaan_reseptur', $session_reseptur);
                }
            } else {
                return DocoHelpers::responseTemplate(500, 'Error', $errors);
            }

            if (isset($session['obat_dihapus'])) {
                $session_obat_dihapus = $session['obat_dihapus'];
            } else {
                $session_obat_dihapus = [];
            }

            $session_obat_dihapus[$reseptur['jenis_racikan'] . $reseptur['obatalkes_id']] = $reseptur;
            $session->set('obat_dihapus', $session_obat_dihapus);

            return DocoHelpers::responseTemplate(
                200,
                Yii::t('fe', 'message_batal'),
                $reseptur,
                ['title' => Yii::t('fe', 'message_berhasil'), 'text' => Yii::t('fe', 'message_batal')]
            );
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    // Reset reseptur session
    public function actionResetResepturSession($id, $cppt_id)
    {
        // Try
        try {
            // Reset
            $test = $this->resetResepturSession($id, $cppt_id);

            // Return
            return DocoHelpers::response(['message' => 'Sukses']);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    // Reseptur
    public function actionReseptur()
    {
        // Try
        // try {
        // Reqeust
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id', null);

        // Model
        $modelResepturDetailRacikan = new ResepturDetailForm;
        $modelResepturDetailNonRacikan = new ResepturNrDetailForm;
        $modelResepturDetailRacikan->scenario = ResepturDetailForm::SCENARIO_SESSION;
        $modelResepturDetailNonRacikan->scenario = ResepturDetailForm::SCENARIO_SESSION;

        // Cek post
        if ($post = $request->post()) {
            // Data
            $data = [];

            // Cek form
            if (isset($post['ResepturNrDetailForm'])) {
                // Set model
                $modelResepturDetailNonRacikan->attributes = $post['ResepturNrDetailForm'];

                // Validasi
                if (!$modelResepturDetailNonRacikan->validate()) {
                    // Form name
                    $formName = substr(strrchr(get_class($modelResepturDetailNonRacikan), "\\"), 1);

                    // Error response
                    $response = $modelResepturDetailNonRacikan->errors;

                    // Return error
                    return DocoHelpers::response($response, 422, $formName);
                }

                // Set data
                $data = $post['ResepturNrDetailForm'];
            }

            // Cek form
            if (isset($post['ResepturDetailForm'])) {
                // Set model
                $modelResepturDetailRacikan->attributes = $post['ResepturDetailForm'];

                // Validasi
                if (!$modelResepturDetailRacikan->validate()) {
                    // Form name
                    $formName = substr(strrchr(get_class($modelResepturDetailRacikan), "\\"), 1);

                    // Error response
                    $response = $modelResepturDetailRacikan->errors;

                    // Return error
                    return DocoHelpers::response($response, 422, $formName);
                }

                // Set data
                $data = $post['ResepturDetailForm'];
            }

            // Add session
            $response = $this->addSessionReseptur($pendaftaran_id, $data);

            // Cek status
            if ($response['status'] != 200) {
                $message = $response['message'];

                if ($response['status'] == 100) {
                    return DocoHelpers::responseTemplate(
                        422,
                        'Error',
                        [],
                        [
                            'title' => Yii::t('fe', 'Peringatan!'),
                            'text' => $message,
                            'message' => $message,
                        ]
                    );
                } else {
                    return DocoHelpers::responseTemplate(
                        500,
                        'Error',
                        [],
                        [
                            'title' => Yii::t('fe', 'Peringatan!'),
                            'text' => $message,
                            'message' => $message,
                        ]
                    );
                }
            } else {
                // Success message
                return DocoHelpers::response(['response' => ['message' => 'Data berhasil disimpan']]);
            }
        }
        // } catch(\Exception $e) {
        //     var_dump($e->getMessage()); die();
        //     return DocoHelpers::responseTemplate(
        //         500,
        //         'Error',
        //         [],
        //         [
        //             'title' => Yii::t('fe', 'Peringatan'),
        //             'message' => Yii::t('fe', 'Terjadi kesalahan').'!',
        //         ]
        //     );
        // }
    }

    // Save session reseptur
    public function actionSaveSessionReseptur()
    {
        $session = Yii::$app->session;
        $cache = Yii::$app->cache;
        $request = Yii::$app->request;
        $errors = 'Terdapat kesalahan';

        try {
            $post = $request->post();
            $pendaftaran_id_encrypt = $request->get('id');
            $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id_encrypt);
            $cacheData = $cache->get('pasien-pendaftaran-id-' . $pendaftaran_id_encrypt);
            $data_pasien = isset($cacheData) ? $cacheData : [];
            $cppt_id = $request->get('cppt_id');
            $update = $request->get('update');

            // Form
            $resepturForm = new ResepturForm;
            $instruksiForm = $post['InstruksiForm'] ? $post['InstruksiForm'] : [];

            if (isset($post['ResepturForm']['diagnosa_id']) && $post['ResepturForm']['diagnosa_id'] != '') {
                $valDiag = [];
                $splitVal = explode('_', $post['ResepturForm']['diagnosa_id']);
                $valDiag['id'] = $splitVal[0];
                $post['ResepturForm']['diagnosa_id'] = $valDiag['id'];
            }

            $resepturForm->attributes = $post['ResepturForm'] ? $post['ResepturForm'] : [];

            // Set value ke instruksi form
            $instruksiForm['tgl_instruksi'] = date('Y-m-d H:i:s', strtotime('NOW'));
            $instruksiForm['ruangan'] = $post['ruangan'];
            $instruksiForm['admisi_id'] = $data_pasien['pasienadmisi_id'];
            $instruksiForm['pasien_id'] = $data_pasien['pasien_id'];
            $instruksiForm['pendaftaran_id'] = $data_pasien['pendaftaran_id'];
            $instruksiForm['pegawai_id'] = !empty($post['pegawai_id']) ? (int) $post['pegawai_id'] : (int) Yii::$app->session->get('user_identity')['id_pegawai'];
            // Jika perawat
            $userIdentity = Yii::$app->session->get('user_identity');
            if (isset($userIdentity['kelompokpegawai_id']) && $userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN) {
                $instruksiForm['pegawai_id'] = $userIdentity['id_pegawai'];
            }

            // Set value ke reseptur form
            $resepturForm['pasien_id'] = $data_pasien['pasien_id'];
            $resepturForm['pendaftaran_id'] = DocoHelpers::decrypt($pendaftaran_id_encrypt);
            $resepturForm['tglreseptur'] = date('Y-m-d', strtotime($resepturForm['tglreseptur']));
            $resepturForm['ruanganreseptur_id'] = $this->_id_ruangan;
            $resepturForm['berat_badan'] = str_replace(',', '.', $resepturForm['berat_badan']);
            $resepturForm['tinggi_badan'] = str_replace(',', '.', $resepturForm['tinggi_badan']);
            $resepturForm['luas_tubuh'] = str_replace(',', '.', $resepturForm['luas_tubuh']);

            if ($resepturForm['ruangan_id'] == '') {
                $resepturForm['ruangan_id'] = $post['ruangan_id'];
            }

            // Validasi
            if (!$resepturForm->validate()) {
                $formName = substr(strrchr(get_class($resepturForm), "\\"), 1);
                $response = $resepturForm->errors;
                return DocoHelpers::response($response, 422, $formName);
            }

            if (isset($session['pemeriksaan_reseptur'])) {
                $session_reseptur = $session['pemeriksaan_reseptur'];
            }

            if (isset($session_reseptur[$pendaftaran_id_encrypt . $cppt_id])) {
                $data_reseptur = $session_reseptur[$pendaftaran_id_encrypt . $cppt_id];
            } else {
                $message = Yii::t('fe', 'Data belum ditambahkan!');
                return DocoHelpers::responseTemplate(
                    500,
                    'Error',
                    [],
                    [
                        'title' => Yii::t('fe', 'Peringatan'),
                        'text' => $message,
                        'message' => $message,
                    ]
                );
            }

            // Change data from table form
            foreach ($data_reseptur as $key => $value) {
                if (isset($post['signa_edit_' . $value['session_key']])) {
                    $data_reseptur[$key]['signa_reseptur'] = $post['signa_edit_' . $value['session_key']];
                }

                if (isset($post['qty_edit_' . $value['session_key']])) {
                    $data_reseptur[$key]['qty_reseptur'] = $post['qty_edit_' . $value['session_key']];
                }
            }

            // data insert reseptur_t
            $data_insert_reseptur = $resepturForm;
            $data_insert_reseptur['ruangan_id'] = $resepturForm['ruangan_id'];
            $data_insert_reseptur['pasien_id'] = $data_pasien['pasien_id'];
            $data_insert_reseptur['pendaftaran_id'] = DocoHelpers::decrypt($pendaftaran_id_encrypt);
            $data_insert_reseptur['tglreseptur'] = date('Y-m-d H:i:s', strtotime('NOW'));
            $data_insert_reseptur['ruanganreseptur_id'] = $this->_id_ruangan;

            // data insert resepturdetail_t
            $check_isracikan = false;
            $data_insert_resepturdetails = [];
            foreach ($data_reseptur as $key => $value) {

                $valAdditionalData = [];
                $valAdditionalData['satuaninput_id'] = $value['satuankecil_id'];
                $valAdditionalData['satuan_input'] = $value['satuankecil_nama'];
                $valAdditionalData['satuankonversi_id'] = $value['satuanbesar_id'];
                $valAdditionalData['satuan_konversi'] = $value['satuankecil'];
                $valAdditionalData['harga_konversi'] = $value['hargasatuan_reseptur'];
                $valAdditionalData['nilai_konversi'] = $value['nilai_konversi'];
                $valAdditionalData['jml_konversi'] = $value['qty_reseptur'];
                $valAdditionalData['etiket'] = $value['etiket'];
                $valAdditionalData['satuan_penyimpanan'] = $value['satuan_penyimpanan'];

                $data_insert_resepturdetail = [];
                $data_insert_resepturdetail['resepturdetail_id'] = $value['resepturdetail_id'];
                $data_insert_resepturdetail['obatalkes_id'] = $value['obatalkes_id'];
                $data_insert_resepturdetail['racikan_id'] = $value['jenis_racikan'];
                $data_insert_resepturdetail['satuankecil_id'] = $value['satuankecil_id'];
                $data_insert_resepturdetail['reseptur_id'] = 'null';
                $data_insert_resepturdetail['r'] = !empty($value['rke']) ? 'r' : 'null';
                $data_insert_resepturdetail['rke'] = !empty($value['rke']) ? $value['rke'] : 'null';
                $data_insert_resepturdetail['qty_reseptur'] = (float) str_replace(',', '.', $value['jml_konversi']);
                $data_insert_resepturdetail['etiket'] = $value['etiket'];
                $data_insert_resepturdetail['qty_konversi'] = (float) str_replace(',', '.', $value['qty_konversi']);
                $data_insert_resepturdetail['additional_data'] = json_encode($valAdditionalData);
                $data_insert_resepturdetail['hargasatuan_reseptur'] = DocoHelpers::convertToAngka($value['hargasatuan_reseptur']);
                $data_insert_resepturdetail['iter'] = $resepturForm['iter'];
                $data_insert_resepturdetail['signa'] = @$value['signa_reseptur'];

                // check is racikan / non racikan
                if ($check_isracikan === false && $value['jenis_racikan'] == ResepturDetailForm::VC_RC) {
                    $check_isracikan = true;
                }

                array_push($data_insert_resepturdetails, $data_insert_resepturdetail);
            }

            if (!empty($data_insert_resepturdetails)) {
                foreach ($data_insert_resepturdetails as $key => $value) {
                    if ($value['qty_reseptur'] == 0 || $value['qty_reseptur'] == '') {
                        return DocoHelpers::responseTemplate(
                            422,
                            'Error',
                            [],
                            [
                                'title' => Yii::t('fe', 'Peringatan'),
                                'text' => Yii::t('fe', 'Qty reseptur tidak boleh 0 atau kosong!'),
                                'message' => Yii::t('fe', 'Terjadi kesalahan') . '!',
                            ]
                        );
                    }
                }
            }

            // define is racikan / non racikan
            $data_insert_reseptur['racikan_id'] = ($check_isracikan === true) ? ResepturDetailForm::VC_RC : ResepturDetailForm::VC_NRC;

            $data_send = [
                'data_instruksi' => $instruksiForm,
                'data_reseptur' => $data_insert_reseptur,
                'data_resepturdetail' => $data_insert_resepturdetails,
            ];
            $instruksiForm['is_puasa'] = isset($post['is_puasa']) ? $post['is_puasa'] : 0;

            if ($update == 'false') {
                $response = $this->_restRanap->post('cppt/create-reseptur', [
                    'form_params' => $data_send
                ]);
                $response = json_decode($response->getBody(), true);
            } else {
                $response = $this->_restRanap->post('cppt/update-reseptur', [
                    'form_params' => $data_send
                ]);
                $response = json_decode($response->getBody(), true);
            }
            if ($response['metadata']['status'] == 200) {
                // unset session
                $this->resetResepturSession($pendaftaran_id_encrypt, $cppt_id);
            }
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            var_dump($e->getMessage());
            die();
            // $response = json_decode($e->getBody(), true);
            return DocoHelpers::responseTemplate(
                500,
                'Error',
                [],
                [
                    'title' => Yii::t('fe', 'Peringatan'),
                    'text' => 'Terdapat kesalahan',
                    'message' => $e->getMessage(),
                    // 'message' => Yii::t('fe','Terjadi kesalahan').'!',
                ]
            );
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            die();
            $response = json_decode($e->getMessage(), true);
            return DocoHelpers::responseTemplate(
                500,
                'Error',
                [],
                [
                    'title' => Yii::t('fe', 'Peringatan'),
                    'text' => 'Terdapat kesalahan',
                    'message' => $e->getMessage(),
                ]
            );
        }
    }

    // Create soap
    public function actionCreateSoap()
    {
        // Try
        try {
            $auto = Yii::$app->request->get('auto', null);
            if (!empty($auto)) {
                return $this->soapCreateOrUpdate(null, true);
            } else {
                return $this->soapCreateOrUpdate();
            }
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, Yii::t("fe", "Terdapat kesalahan"));
        } catch (RequestException $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(500, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    // Create soap
    public function actionCreateVerbalOrder()
    {
        // Try catch()
        try {
            // Get params
            $params = Yii::$app->request;
            $post = $params->post('VerbalOrderRanapForm');
            $model = new VerbalOrderRanapForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $model->attributes = $post;
            // Explode ruangan_id ke 4 field
            $explodeRuangan_id = explode('@#', $post['ruangan_id']);
            if (count($explodeRuangan_id) > 1) {
                $post['ruangan_id']         = (int)$explodeRuangan_id[0];
                $post['kamarruangan_id']    = (int)$explodeRuangan_id[1];
                $post['kamartempattidur_id'] = (int)$explodeRuangan_id[2];
                $post['kamar_tempattidur']  = $explodeRuangan_id[3];
                $post['tgl_cppt']   = date('Y-m-d H:i:s');
            }
            if ($model->validate()) {
                // Send to backend
                $request = $this->_restRanap->post('cppt/create-verbal-order', [
                    'form_params' => $post
                ]);
                $response = json_decode($request->getBody(), true);
                // Return
                return json_encode($response);
            } else {
                $response = $model->errors;
                return DocoHelpers::response($response, 422, $formName);
            }
        } catch (\Exception $e) {
            // Exception
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (RequestException $e) {
            // Exception
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    // Get data
    public function actionGetDataCppt()
    {
        // Try catch
        try {
            // Inisiasi
            Yii::$app->response->format = Response::FORMAT_JSON;
            $params = Yii::$app->request;
            $userIdentity = Yii::$app->session->get('user_identity');
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($params->get());
            $draw = $params->get('draw', 1);
            $data = $dataPagt = $newData = $data_cppt = [];
            $pendaftaran_id = DocoHelpers::decrypt($params->get('id'));
            $ruangan_id = $params->get('ruangan_id', null);
            $pegawai_id = $params->get('pegawai_id', null);
            $kelompokpegawai_id = $params->get('kelompokpegawai_id', null);
            $tgl_cppt = $params->get('tanggal_cppt', null);
            $length = $params->get('length', null);
            $start = $params->get('start', null);
            $order = 0;

            // Inisiasi result
            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;
            $result['recordsFiltered'] = 0;

            // Menghilangkan q
            if (isset($yiiRestfulParams['q'])) {
                // Unset q
                unset($yiiRestfulParams['q']);
            }

            // Menghilangkan filters
            if (isset($yiiRestfulParams['filters'])) {
                // Unset filters
                unset($yiiRestfulParams['filters']);
            }

            if(isset($yiiRestfulParams['order']) && $yiiRestfulParams['order'] == 'tgl_soaprj DESC'){
                $order = 1;
            }
            // Get request
            $response = $this->_restRanap->get('cppt/index?pendaftaran_id='.$pendaftaran_id.'&'. '&ruangan_id=' . $ruangan_id . '&pegawai_id=' . $pegawai_id . '&' . http_build_query($yiiRestfulParams) .'&tgl_cppt='.$tgl_cppt."&filter_kelompokpegawai_id={$kelompokpegawai_id}" ."&length={$length}" . "&start={$start}", ['form_params' => []]);
            $response = json_decode($response->getBody(), true);
            // Inisiasi nomor
            $no = $params->get('start', 1);

            $hide = 'show()';
            $status_disabled = $this->getStatusPeriksaRanap();
            if ($status_disabled == true) {
                $hide = 'hide()';
            }


            // Loop untuk membuat array dari response
            if (!empty($response['response']['data'])) {
                $html = '';
                foreach ($response['response']["data"] as $key => $value) {
                    // Assign data
                    $no++;
                    $value['no'] = $no;
                    $data[$key] = $value;
                    $data[$key]['user_identity'] = $userIdentity;
                    if ($data[$key]['is_gizi']) {
                        $tgl_cppt = $value['tgl_cppt'];
                        $data[$key]['primary'] = DocoHelpers::encrypt($value['pagt_id']);
                        $data[$key]['another_format_tgl_pagt'] = date('Y-m-d', strtotime($value['origin_tgl_cppt'])) . ' ' . date('H:i', strtotime($value['origin_tgl_cppt']));
                        $data[$key]['profesi'] = $value['kelompokpegawai_nama'] . '<br>' . $value['pegawai_nama'];
                        $data[$key]['ruangan'] = $tgl_cppt . '<hr/>' . $data[$key]['profesi'];
                        $data[$key]['penatalaksanaan'] = isset($value['hasil_asesmen']) ? $value['hasil_asesmen'] : '';
                        $data[$key]['is_verifikasi'] = $value['is_verifikasi'];
                        $data[$key]['tgl_cppt'] = $tgl_cppt;
                        $data[$key]['subject'] = '';
                        $data[$key]['object'] = '';
                        $data[$key]['a_diag_utama'] = '';
                        $data[$key]['a_diag_penyerta'] = '';
                        $data[$key]['planning'] = '';
                        $data[$key]['catatan_dokter'] = '';
                        $data[$key]['catatan_perawat'] = '';
                        $data[$key]['instruksi_soap'] = '';
                        $data[$key]['instruksi'] = '';
                        $data[$key]['is_deleted'] = false;
                        $data[$key]['origin_tgl_cppt'] = date('Y-m-d', strtotime($value['origin_tgl_cppt'])) . ' ' . date('H:i', strtotime($value['origin_tgl_cppt']));;
                        $data[$key]['is_cppt'] = false;
                        $data[$key]['pegawai_verifikasi_nama'] = $value['pegawai_verifikasi_nama'];
                        $data[$key]['tgl_verifikasi'] = !empty($valueGizi['tgl_verifikasi']) ? date('d/m/Y / H:i:s', strtotime($value['tgl_verifikasi'])) : '-';
                        if ($data[$key]['is_verifikasi']) {
                            $html .= '<span>' . $data[$key]['pegawai_verifikasi_nama'] . ' ' . $data[$key]['tgl_verifikasi'] . '</span>';
                            $html .= '<br>';
                        } else {
                            $pegawai_id = $this->_pegawai_id;
                            $dpjp = $value['dokteradmisi_id'];
                            if ($pegawai_id == $dpjp && $this->_user_identity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS) {
                                $html .= Html::button('<b><i class="fa fa-check-square"></i></b>' . Yii::t('fe', 'Verifikasi DPJP'), [
                                    'class' => 'btn btn-info btn-labeled btn-xs btn-verifikasi-dpjp',
                                    'style' => 'margin-top:10px',
                                    'data-id' => $value['pagt_id'],
                                    'data-cpptid' => DocoHelpers::encrypt($value['pagt_id']),
                                    'data-ispagt' => 1,
                                    'data-confirm-message' => Yii::t('fe', 'Apakah anda yakin akan memverifikasi PAGT tersebut?'),
                                ]);
                            }
                        }
                        $data[$key]['verifikasi'] = $html;
                    } else {
                        $data[$key]['primary'] = DocoHelpers::encrypt($value['origin_cppt_id']);
                        $data[$key]['tgl_cppt'] = date('d/m/Y / H:i:s', strtotime($value['tgl_cppt']));
                        $data[$key]['another_format_tgl_cppt'] = date('Y-m-d', strtotime($value['tgl_cppt'])) . ' ' . date('H:i', strtotime($value['tgl_cppt']));
                        $data[$key]['profesi'] = $value['kelompokpegawai_nama'] . '<br>' . $value['nama_pegawai'];
                        $data[$key]['ruangan'] = $value['ruangan_nama'] . ' ' . $value['no_tempattidur'] . ' ' . $value['kamarruangan_nokamar'] . '<hr/>' . $data[$key]['tgl_cppt'] . '<hr/>' . $data[$key]['profesi'];
                        $data[$key]['tgl_soaprj'] = $value['tgl_cppt'];
                        $data[$key]['penatalaksanaan'] = $this->getPenatalaksanaan($value);
                        $data[$key]['verifikasi'] = $this->getVerifikasi($value, [], $status_disabled);
                        $data[$key]['is_verifikasi'] = $value['is_verifikasi'];
                        $data[$key]['subject'] = str_replace('<br />'," ", $value['subject']);
                        $data[$key]['object'] = str_replace('<br />'," ", $value['object']);
                        $data[$key]['a_diag_utama'] = $value['a_diag_utama'];
                        $data[$key]['a_diag_penyerta'] = $value['a_diag_penyerta'];
                        $data[$key]['planning'] = str_replace('<br />'," ", $value['planning']);
                        $data[$key]['catatan_dokter'] = str_replace('<br />'," ", $value['catatan_dokter']);
                        $data[$key]['catatan_perawat'] = str_replace('<br />'," ", $value['catatan_perawat']);
                        $data[$key]['instruksi'] = str_replace('<br />'," ", $value['instruksi']);
                        $data[$key]['instruksi_soap'] = '<div style="white-space: pre-line">' . $value['instruksi'] . '</div>';
                        $data[$key]['is_deleted'] = $value['is_deleted'];
                        $data[$key]['origin_tgl_cppt'] = $value['tgl_cppt'];
                        $data[$key]['origin_cppt_id'] = $this->helper->encrypt($value['origin_cppt_id']);
                        $data[$key]['is_cppt'] = true;
                        $data[$key]['is_dokter'] = $value['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS ? true : false;
                        $data[$key]['is_icd_x'] = isset($value['is_icd_x']) ? $value['is_icd_x'] : true;
                    }
                }
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $response['response']['totalCount'];
            $result['recordsFiltered'] = $response['response']['totalCount'];
            $result['load_more'] = $response['response']['load_more'];
            return $result;
        } catch (RequestException $e) {
            $this->logError($e);
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            $this->logError($e);
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    private function getInstruksiDpjp($data_instruksi, $isDeleted)
    {
        $groupInstruksi = [];
        $html = '<table><tr><td>';
        foreach ($data_instruksi as $d_instruksi) {
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['instruksi_id'] = $d_instruksi['instruksi_id'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['cppt_id'] = $d_instruksi['cppt_id'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['tipe_instruksi'] = $d_instruksi['tipe_instruksi'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['catatan_instruksi'] = $d_instruksi['catatan_instruksi'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['cpptpegawai_id'] = $d_instruksi['cpptpegawai_id'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['is_verifikasi_dpjp'] = $d_instruksi['is_verifikasi_dpjp'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['instruksi_deleted'] = $d_instruksi['instruksi_deleted'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['ruangan_pertindakan'] = $d_instruksi['ruangan_pertindakan'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['list_tindakan'][] = [
                'nama_tindakan' => $d_instruksi['tindakaninstruksi_nama'],
                'qty_tindakan' => $d_instruksi['qty'],
                'tgl_tindakan' => $d_instruksi['tgl_instruksi'],
                // 'tgl_tindakan' => $d_instruksi['tanggal_input'],
                'ket_cyto' => $d_instruksi['ket_cyto'],
                'ket_racik' => $d_instruksi['ket_racik'],
                'instruksi_deleted' => $d_instruksi['instruksi_deleted'],
                'tindakan_deleted' => $d_instruksi['tindakan_deleted'],
                'daftar_paket' => $d_instruksi['daftar_paket'],
                'tipe_instruksi' => $d_instruksi['tipe_instruksi'],
                'daftar_paket' => $d_instruksi['daftar_paket'],
                'instruksi' => $d_instruksi['instruksi'],
                'is_telah_implementasi' => $d_instruksi['is_telah_implementasi'],
                'status_implementasi' => $d_instruksi['status_implementasi']
            ];
        }
        foreach ($groupInstruksi as $id_ins => $groupTipe) {
            foreach ($groupTipe as $nama_tipe => $data_ins) {
                if ($nama_tipe == 'TINDAKANBMHP') {
                    $label_nama_tipe = 'Tindakan';
                } else if ($nama_tipe == 'RESEPTUR') {
                    $label_nama_tipe = 'Obat';
                } else if ($nama_tipe == 'PENUNJANG') {
                    $label_nama_tipe = 'Penunjang';
                } else {
                    $label_nama_tipe = 'Tindakan';
                }
                if ($data_ins['instruksi_deleted'] == true) {
                    $html .= '<tr class="strikeout"><td><table>';
                } else {
                    $html .= '<tr><td><table>';
                }
                $html .= '<tr><td>' . @$label_nama_tipe . '</td>';

                $array_status_implemented = [];
                $array_status_penunjang_batal = [];
                foreach ($data_ins['list_tindakan'] as $ins_tindakan) {
                    if ($ins_tindakan['tindakan_deleted'] != true) {
                        $array_status_implemented[] = $ins_tindakan['is_telah_implementasi'];
                        if ($nama_tipe == 'PENUNJANG') {
                            $array_status_penunjang_batal[] = $ins_tindakan['status_implementasi'];
                        }
                    }
                }
                $instruksi_implemented = false;
                if (count($array_status_implemented) > 0) {
                    if (count(array_unique($array_status_implemented)) === 1) {
                        if (current($array_status_implemented) == true) {
                            $instruksi_implemented = true;
                        }
                    }
                }
                $is_penunjang_batal = false;
                $is_penunjang_ditolak = false;
                if (count($array_status_penunjang_batal) > 0) {
                    if (in_array('472', $array_status_penunjang_batal)) {
                        $is_penunjang_batal = true;
                    }
                    if (in_array('541', $array_status_penunjang_batal)) {
                        $is_penunjang_ditolak = true;
                    }
                }

                if ($data_ins['instruksi_deleted'] != true && $this->_pegawai_id == $data_ins['cpptpegawai_id'] && $data_ins['is_verifikasi_dpjp'] != true && $instruksi_implemented != true) {
                    if ($nama_tipe == 'PENUNJANG' && $is_penunjang_batal == true) {
                        $html .= '<td><span class="label label-danger">Dibatalkan</span></td>';
                    } elseif ($nama_tipe == 'PENUNJANG' && $is_penunjang_ditolak == true) {
                        $html .= '<td><span class="label label-danger">Ditolak</span></td>';
                    } else {
                        $html .= '<td>' . (!$isDeleted ? Html::button('<b><i class="fa fa-pencil"></i></b>', [
                            'class' => 'btn btn-info btn-ubah-terapi btn-link',
                            'data-cpptid' => DocoHelpers::encrypt($data_ins['cppt_id']),
                            'data-instruksiid' => DocoHelpers::encrypt($data_ins['instruksi_id']),
                            'data-jnsinstruksi' => @$nama_tipe
                        ]) : '') . '</td>';
                        $html .= '<td>' . (!$isDeleted ? Html::button('<b><i class="fa fa-trash"></i></b>', [
                            'class' => 'btn btn-info btn-hapus-terapi btn-link',
                            'data-cpptid' => DocoHelpers::encrypt($data_ins['cppt_id']),
                            'data-instruksiid' => DocoHelpers::encrypt($data_ins['instruksi_id']),
                            'data-tipeinstruksi' => @$nama_tipe,
                            'action' => '/ranap/pemeriksaan-rawat-inap/hapus-terapi?id=' . $this->_pendaftaran_id . '&cppt_id=' . DocoHelpers::encrypt($data_ins['cppt_id']) . '&instruksi_id=' . DocoHelpers::encrypt($data_ins['instruksi_id']) . '&tipeinstruksi=' . @$nama_tipe
                        ]) : '') . '</td>';
                    }
                } else {
                    if ($nama_tipe == 'PENUNJANG' && $is_penunjang_batal == true) {
                        $html .= '<td><span class="label label-danger">Dibatalkan</span></td>';
                    } elseif ($nama_tipe == 'PENUNJANG' && $is_penunjang_ditolak == true) {
                        $html .= '<td><span class="label label-danger">Ditolak</span></td>';
                    }
                }
                $html .= '</tr>';
                if ($nama_tipe == 'PENUNJANG') {
                    if (substr($data_ins['tipe_instruksi'], 0, 3) == 'LAB') {
                        $label_instalasi = 'Laboratorium';
                    } else if (substr($data_ins['tipe_instruksi'], 0, 3) == 'RAD') {
                        $label_instalasi = 'Radiologi';
                    } else if (substr($data_ins['tipe_instruksi'], 0, 3) == 'BED') {
                        $label_instalasi = 'Bedah Sentral';
                    } else {
                        $label_instalasi = $data_ins['tipe_instruksi'];
                    }
                    $html .= '<tr><td>';
                    $html .= @$label_instalasi . ' - ';
                    $html .= @$data_ins['ruangan_pertindakan'];
                    $html .= '<td></tr>';
                }
                $html .= '<tr><td>' . @$data_ins['catatan_instruksi'] . '</td></tr>';
                $html .= '<tr><td><table>';
                $groupTglTindakan = [];
                foreach ($data_ins['list_tindakan'] as $ins_tindakan) {
                    $groupTglTindakan[$ins_tindakan['tipe_instruksi']][$ins_tindakan['tgl_tindakan']][] = $ins_tindakan;
                }
                foreach ($groupTglTindakan as $tipe_instruksi => $group_tgl) {
                    $html .= '<tr>';
                    if ($tipe_instruksi == 'LAB_TINDAKAN') {
                        $label_tipe_instruksi = 'Tindakan Laboratorium';
                    } else if ($tipe_instruksi == 'RAD_TINDAKAN') {
                        $label_tipe_instruksi = 'Tindakan Radiologi';
                    } else if ($tipe_instruksi == 'LAB_PAKET') {
                        $label_tipe_instruksi = 'Paket Laboratorium';
                    } else if ($tipe_instruksi == 'RAD_PAKET') {
                        $label_tipe_instruksi = 'Paket Radiologi';
                    } else if ($tipe_instruksi == 'BED_TINDAKAN') {
                        $label_tipe_instruksi = 'Tindakan Bedah Sentral';
                    } else {
                        $label_tipe_instruksi = $tipe_instruksi;
                    }
                    $html .= '<td>&nbsp;</td><td>&nbsp;</td><td>' . @$label_tipe_instruksi . '</td></tr>';
                    foreach ($group_tgl as $tgl_ins => $ins_tgltindakan) {
                        $hitungTgl = 0;
                        $hitungInsTindakan = count($ins_tgltindakan);
                        foreach ($ins_tgltindakan as $row_tgltindakan) {
                            $html .= '<tr>';
                            if ($hitungTgl == 0) {
                                if ($hitungInsTindakan == 1 && $row_tgltindakan['tindakan_deleted'] == true) {
                                    $html .= '<td rowspan="' . @$hitungInsTindakan . '"><strike>' . @$row_tgltindakan['tgl_tindakan'] . '</strike></td>';
                                } else {
                                    $html .= '<td rowspan="' . @$hitungInsTindakan . '">' . @$row_tgltindakan['tgl_tindakan'] . '</td>';
                                }
                            }
                            $html .= '<td>&nbsp;</td>';
                            if ($row_tgltindakan['tindakan_deleted'] == true || $row_tgltindakan['instruksi_deleted'] == true) {
                                $html .= '<td><strike>';
                                $html .= @$row_tgltindakan['instruksi'];
                                if (substr($tipe_instruksi, -3) == 'KET') {
                                    $dftr_paket = json_decode($row_tgltindakan['daftar_paket'], true);
                                    if (count($dftr_paket) > 1) {
                                        $html .= '<ul>';
                                        foreach ($dftr_paket as $paket) {
                                            $html .= '<li>' . $paket . '</li>';
                                        }
                                        $html .= '</ul>';
                                    }
                                }
                                $html .= '</strike></td>';
                            } else {
                                $html .= '<td>';
                                $html .= @$row_tgltindakan['instruksi'];
                                if (substr($tipe_instruksi, -3) == 'KET') {
                                    $dftr_paket = json_decode($row_tgltindakan['daftar_paket'], true);
                                    if (count($dftr_paket) > 1) {
                                        $html .= '<ul>';
                                        foreach ($dftr_paket as $paket) {
                                            $html .= '<li>' . $paket . '</li>';
                                        }
                                        $html .= '</ul>';
                                    }
                                }
                                $html .= '</td>';
                            }
                            $html .= '</tr>';
                            $hitungTgl++;
                        }
                    }
                    $html .= '<tr><td>&nbsp;</td></tr>';
                }
                $html .= '</table></td></tr>';
                $html .= '</table>';
            }
        }
        $html .= '</td></tr></table>';
        return $html;
    }

    // Get cppt
    public function actionGetCpptAsesmenMedis()
    {
        // Param
        $pendaftaran_id = DocoHelpers::decrypt(Yii::$app->request->post('pendaftaran_id'));
        $cppt_id = Yii::$app->request->post('cppt_id');

        // Try
        try {
            // Get request
            $request = $this->_restRanap->get('cppt/get-cppt-asesmen-medis?pendaftaran_id=' . $pendaftaran_id . '&cppt_id=' . $cppt_id);
            $response = json_decode($request->getBody(), true);

            // Return
            return json_encode($response['response']);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    /**
     *
     * get data obatalkes
     *
     */
    public function actionListObatAlkesDepo()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        // $ruangan_id = $post['depdrop_parents'][0];
        $ruangan_id = $request->get('ruangan_id', 3);
        $penjaminId = $request->get('penjaminId', null);
        $kelaspelayananId = $request->get('kelaspelayananId', null);
        $page = $request->get('page', 1);
        $keyword = $request->get('q', null);
        $limit = 11;

        $group_jenis = null;
        $userIdentity = Yii::$app->session->get('user_identity');
        if ($userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN) {
            $group_jenis = DocoConstants::GOUP_ALKES;
        }
        try {
            // $request = $this->_restRanap->get('cppt/list-obat-alkes-fn?ruangan_id='.$ruangan_id);
            // $body = json_decode($request->getBody(),TRUE);
            $ddl =  $this->getListStokObatAlkes($ruangan_id, $penjaminId, $kelaspelayananId, $page, $keyword, $group_jenis);

            $out = [];
            foreach ($ddl as $key => $value) {
                $status = ($value['qty_tersedia'] > 0) ? false : true;
                $out[] = [
                    'id' => $value['obatalkes_id'],
                    // 'name' => $value['obatalkes_namalain'] ." - ". $value['qty_tersedia'],
                    'text' => $value['obatalkes_namalain'],
                    'qty_av' => $value['qty_tersedia'],
                    'disabled' => $status,
                    'options' => [
                        'disabled' => $status,
                        'qty_tersedia' => $value['qty_tersedia'],
                        'data-hargajual' => $value['hargaygdipakai'],
                        'data-satuankecil_nama' => $value['satuankecil_nama'],
                        'data-satuankecil_id' => $value['satuankecil_id'],
                        'hargajual' => $value['hargaygdipakai'],
                        'satuankecil_nama' => $value['satuankecil_nama'],
                        'satuankecil_id' => $value['satuankecil_id'],
                    ],
                ];
            }
            $data = $out;
            $removed = count($data) === $limit ? array_pop($data) : false;

            // return DocoHelpers::response(['output'=>$out, 'selected'=>'']);
            return DocoHelpers::response([
                'result' => $data,
                'total_count' => count($out),
                'incomplete_results' => false,
                'pagination' => ['more' => count($out) === $limit ? true : false]
            ]);
        } catch (\Exception $e) {
            echo json_encode(['result' => [], 'selected' => '']);
            return;
        }
    }

    // get obat alkes by pagination
    // public function actionListObatAlkesDepo()
    // {
    //     $request = Yii::$app->request;
    //     $get = $request->get();
    //     $ruangan_id = isset($get['ruangan_id']) ? $get['ruangan_id'] : null;
    //     $keyword = isset($get['q']['term']) ? $get['q']['term'] : '';
    //     $page = isset($get['page']) ? $get['page'] : 1;
    //     $return = [];
    //     $data = [];

    //     try {
    //         $request = $this->_restRanap->get('cppt/list-obat-alkes-fn?ruangan_id=' .$ruangan_id.'&keyword='. $keyword.'&page='. $page);
    //         $body = json_decode($request->getBody(),TRUE);
    //         if(!empty($body['response']['data'])) {
    //             $data = $body['response']['data'];
    //         }

    //         $return = [
    //             'data_stok' => $data,
    //             'payload' => $get
    //         ];

    //         return DocoHelpers::response($return);
    //     } catch (\Exception $e) {
    //         echo json_encode(['data_stok'=>[], 'payload'=>$get]);
    //         return;
    //     }
    // }

    public function actionGetDataAjax()
    {
        $id = Yii::$app->docoVars->workspace('instalasi_id');
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $request = Yii::$app->request;
        // $keyword = $request->get('q');
        $payload = $request->get();

        $return = [];
        try {
            $response = $this->_restApotek->get('cppt/get-list-stok-obat', [
                'query' => [
                    'instalasi_id' => $id,
                    'ruangan_id'   => $ruangan_id,
                    'keyword'      => isset($payload['q']) ? $payload['q'] : '',
                    'page'         => $payload['page'],
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            $return = [
                'data_stok' => $body['response']['data'],
                'payload' => $payload
            ];
            return DocoHelpers::response($return);
        } catch (\Exception $e) {
            $this->logError($e);
            // return json_encode([
            //     'message' => $e->getMessage(),
            //     'file' => $e->getFile(),
            //     'line' => $e->getLine(),
            // ]);
            return DocoHelpers::response($return);
        }
    }

    public function actionListSatuanBesar()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $obatalkes_id = $post['depdrop_parents'][0];
        // var_dump($obatalkes_id); die();
        try {
            $request = $this->_restRanap->get('cppt/list-satuan-besar?obatalkes_id=' . $obatalkes_id);
            $body = json_decode($request->getBody(), TRUE);
            $response = $body['response'];
            $out = [];
            $val_satuan = $response['0']['satuankecil_id'];
            foreach ($response as $key => $value) {
                $out[] = [
                    'id' => $value['satuanbesar_id'],
                    'satuanbesar_id' => $value['satuanbesar_id'],
                    'satuan_besar' => $value['satuan_besar'],
                    'name' => $value['satuan_besar'],
                    'nilai_konversi' => $value['nilai_konversi'],
                ];
            }
            return json_encode(['output' => $out, 'selected' => $val_satuan]);
        } catch (\Exception $e) {
            return json_encode(['output' => [], 'selected' => '']);;
        }
    }

    public function actionGetNilaiKonversi()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $get = $request->get();
        $satuanbesar_id = $get['satuanbesar_id'];
        $obatalkes_id = $get['obatalkes_id'];
        try {
            $request = $this->_restRanap->get('cppt/get-konversi?obatalkes_id=' . $obatalkes_id . '&satuanbesar_id=' . $satuanbesar_id);
            $body = json_decode($request->getBody(), TRUE);
            $response = $body['response'];
            return $response;
        } catch (\Exception $e) {
            return [];
        }
    }

    // Hapus terapi
    public function actionHapusTerapi()
    {
        $request = Yii::$app->request;
        $cppt_id = $request->get('cppt_id', 0);
        $instruksi_id = $request->get('instruksi_id', 0);
        $tipeinstruksi = $request->get('tipeinstruksi', 0);
        $codeHttp = 422;

        try {
            // Request
            $request = $this->_restRanap->delete('cppt/hapus-terapi', [
                'query' => [
                    'cppt_id' => $cppt_id,
                    'instruksi_id' => $instruksi_id,
                    'tipeinstruksi' => $tipeinstruksi
                ]
            ]);
            $response = json_decode($request->getBody(), true);
            if ($response['metadata']['status'] == 200) {
                $response['response']['text'] = 'Berhasil Dihapus';
                return DocoHelpers::response($response);
            } else {
                $codeHttp = $response['metadata']['status'];
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['response']['text'] = 'Terjadi kesalah pada sistem';
            $response['response']['message'] = $e->getMessage();
        } catch (\Exception $e) {
            // Exception
            Yii::info($e->getMessage());
            $response['response']['text'] = 'Terjadi kesalah pada sistem';
            $response['response']['message'] = $e->getMessage();
        }
        return DocoHelpers::response($response, $codeHttp);
    }

    // Cetak pdf
    public function actionCetakReseptur($id, $instruksi_id)
    {
        // Path
        $path = Yii::getAlias("@download") . "/terapi-reseptur.pdf";

        // Try catch
        try {
            // Request
            $request = $this->_restRanap->get('cppt/cetak-reseptur?pendaftaran_id=' . $id . '&pasienadmisi_id=' . $this->_data_pasien['pasienadmisi_id'] . '&pegawai_id=' . $this->_user_identity['id_pegawai'] . '&ruangan_id=' . $this->_id_ruangan . '&instruksi_id=' . $instruksi_id, [
                'save_to' => $path,
            ]);

            // Download pdf
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

    // Get list data
    private function getListData($pasienId, $pegawaiId, $ruanganId, $pendaftaranId, $pasienadmisiId = null)
    {
        // Try catch
        try {
            // Get request
            $request = $this->_restRanap->get('cppt/get-list-data', [
                'form_params' => [],
                'query' => [
                    'pasien_id' => $pasienId,
                    'pegawai_id' => $pegawaiId,
                    'ruangan_id' => $ruanganId,
                    'pendaftaran_id' => $pendaftaranId,
                    'pasienadmisi_id' => $pasienadmisiId
                ]
            ]);
            $response = json_decode($request->getBody(), true);
            // Return response
            $bodyResponse = $response['response'];
            return $bodyResponse;
        } catch (\Exception $e) {
            // Exception
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (RequestException $e) {
            // Exception
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    // Get penatalaksanaan
    private function getPenatalaksanaan($data)
    {
        // Check if related cppt has ordered lab/rad/reseptur
        $hasLab = !empty($data['is_lab']) && $data['is_lab'] ? '* Pasien dilakukan pemeriksaan laboratorium <br/>' : '';
        $hasRad = !empty($data['is_rad']) && $data['is_rad'] ? '* Pasien dilakukan pemeriksaan radiologi <br/>' : '';
        $hasResep = !empty($data['is_reseptur']) && $data['is_reseptur'] ? '* Pasien diberikan resep <br/>' : '';
        $hasKonsul = !empty($data['is_konsul']) && $data['is_konsul'] ? '* Pasien dikonsulkan <br/>' : '';

        // Deklarasi html
        $html = '<div class="wrapper"><table border="0" cellpadding="0" cellspacing="0" style="width: 100%; border-collapse: collapse;">';

        // Cek subjek
        if (isset($data['subject']) && $data['subject'] != '') {
            // normalize string
            $data['subject'] = str_replace('<br />'," ", $data['subject']);
            // Set html
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td><b>Subjektif :</b><br><div style="white-space: pre-line">' . $data['subject'] . '</div></td>';
            $html .= '</tr>';
        }
        // Cek objek
        if (isset($data['object']) && $data['object'] != '') {
            // normalize string
            $data['object'] = str_replace('<br />'," ", $data['object']);
            // Set html
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td><b>Objektif :</b><br/><div style="white-space: pre-line">' . $data['object'] . '</div></td>';
            $html .= '</tr>';
        }

        // Cek subject
        if (($data['subject'] != '') && ($data['object'] != '') && ($data['planning'] != '')) {
            // Cek asesmen
            if (isset($data['a_diag_utama']) && $data['a_diag_utama'] != '') {
                $diag_utama = json_decode($data['a_diag_utama'], TRUE);
                // $diag_utama = $data['a_diag_utama'];
                // Set html
                $html .= '<tr style="line-height:130%;">';

                if($data['is_icd_x']){
                    $html .= '<td><b>Asesmen Diagnosa Utama :</b><br/>' . @$diag_utama['text'] . '</td>';
                }
                else{
                    $html .= '<td><b>Asesmen Diagnosa Utama:</b><br/><div style="white-space: pre-line">'.$diag_utama['text'].'</div></td>';
                }
                $html .= '</tr>';
            }

            // Cek asesmen
            if (isset($data['a_diag_penyerta']) && $data['a_diag_penyerta'] != '') {
                // Encode
                $diagnosaPenyerta = json_decode($data['a_diag_penyerta'], TRUE);
                if (is_string($diagnosaPenyerta)) {
                    $diagnosaPenyerta = json_decode($diagnosaPenyerta, TRUE);
                }

                // Cek diagnosa
                if ($diagnosaPenyerta != '') {
                    // Inisialisasi counter
                    $counter = 0;

                    // Loop
                    $html .= '<tr style="line-height:130%;">';
                    $html .= '<td> <b>' . Yii::t('fe', 'Diagnosa Penyerta') . '</b><br/>';
                    foreach ($diagnosaPenyerta as $valueDiagnosaPenyerta) {
                        // Cek counter
                        $html .= '&nbsp;&nbsp;&nbsp;- ' . @$valueDiagnosaPenyerta['text'] . '<br/>';

                        // Plus the counter
                        $counter++;
                    }
                    $html .= '<td/></tr>';
                } else {
                    // Set strip
                    $html .= '<td>-</td>';
                }

                // Close tag
                $html .= '</tr>';
            }
        } else {
            // Set html
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td style="display: inline-block; word-break: break-word; white-space: initial;">' . $data['instruksi'] . '<br>' . $data['pegawai_instruksi'] . '</td>';
            $html .= '</tr>';
        }

        // Cek penanda order penunjang
        if (!empty($hasLab) || !empty($hasRad) || !empty($hasResep) || isset($data['planning'])) {
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td><b>Planning:</b> <br/>';

            // Cek planning
            if (isset($data['planning']) && $data['planning'] != '') {
                // normalize string
                $data['planning'] = str_replace('<br />'," ", $data['planning']);
                // Set html
                $html .= '<div style="white-space: pre-line">' . $data['planning'] . '</div>';
            }

            $html .= '<br/>' . $hasLab . $hasRad . $hasResep . $hasKonsul;
            $html .= '</td></tr>';
        }

        // Cek catatan dokter
        if (isset($data['catatan_dokter']) && $data['catatan_dokter'] != null) {
            // normalize string
            $data['catatan_dokter'] = str_replace('<br />'," ", $data['catatan_dokter']);
            // Set html
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td style="vertical-align: top;"><b>' . Yii::t('fe', 'Catatan') . '</b><br/><div style="white-space: pre-line">' . $data['catatan_dokter'] . '</div></td>';
            $html .= '</tr>';
        }

        // Cek catatan perawat
        if (isset($data['catatan_perawat']) && $data['catatan_perawat'] != null) {
            // normalize string
            $data['catatan_perawat'] = str_replace('<br />'," ", $data['catatan_perawat']);
            // Set html
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td style="vertical-align: top;"><b>' . Yii::t('fe', 'Catatan') . '</b><br/><div style="white-space: pre-line">' . $data['catatan_perawat'] . '</div></td>';
            $html .= '</tr>';
        }

        // Cek instruksi pulang
        if (isset($data['is_instruksi_pulang']) && $data['is_instruksi_pulang'] == true) {
            // Set html
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td>' . Yii::t('fe', 'Instruksi Pulang') . '<br/>' . Yii::t('fe', 'Ya') . '</td>';
            $html .= '</tr>';
        }

        // Set end tag html
        $html .= '</table></div>';

        // Return
        return $html;
    }

    // Get dpjp
    private function getDpjp($data, $dataDetail)
    {
        if (!empty($dataDetail)) {
            $tempDataDetail = [];

            foreach ($dataDetail as $value) {
                $tempDataDetail[$value['instruksi_id']]['data'][] = $value;
                $tempDataDetail[$value['instruksi_id']]['is_deleted_instruksi'] = $value['is_deleted_instruksi'];
            }
        }

        $html = '<table border="0" cellpadding="0" cellspacing="0">';

        if (!empty($tempDataDetail)) {
            $firstFlag = true;
            $secondFlag = true;

            foreach ($tempDataDetail as $index => $content) {
                if (!empty($content['data'])) {
                    foreach ($content['data'] as $key => $value) {
                        if ($content['is_deleted_instruksi'] == true) {
                            $html .= '<tr class="strikeout">';
                            if ($firstFlag == true) {
                                if ($value['last_modified_date'] != '') {
                                    $html .= '<td>' . date('d-m-Y / h:i:s', strtotime($value['tgl_instruksi'])) . '&nbsp&nbsp&nbsp<br>' . date('d-m-Y / h:i:s', strtotime($value['last_modified_date'])) . '</td>';
                                } else {
                                    $html .= '<td>' . date('d-m-Y / h:i:s', strtotime($value['tgl_instruksi'])) . '&nbsp&nbsp&nbsp</td>';
                                }

                                $html .= '<td>' . $value['jns_instruksi'] . '</td>';
                                $firstFlag = false;
                            } else {
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                            }
                            $html .= '<td>:</td>';
                            if ($value['jns_instruksi'] == 'PAKET') {
                                $paket = '-';
                                $temp = [];

                                if (!empty($value['tipepaket_id'])) {
                                    foreach ($value['tipepaket_id'] as $newKey => $newValue) {
                                        $temp[] = $newValue['daftartindakan_nama'];
                                    }

                                    if (!empty($temp)) {
                                        $paket = implode(", ", $temp);
                                    }
                                }
                                $html .= '<td>' . $value['nama_instruksi'] . ': ' . $paket . '</td>';
                            } else {
                                $html .= '<td>' . $value['nama_instruksi'] . '</td>';
                            }
                            $html .= '<td></td>';
                            $html .= '<td></td>';
                            $html .= '</tr>';
                        } else {
                            $html .= '<tr>';
                            if ($firstFlag == true) {
                                if ($value['last_modified_date'] != '') {
                                    $html .= '<td>' . date('d-m-Y / h:i:s', strtotime($value['tgl_instruksi'])) . '&nbsp&nbsp&nbsp<br>' . date('d-m-Y / h:i:s', strtotime($value['last_modified_date'])) . '</td>';
                                } else {
                                    $html .= '<td>' . date('d-m-Y / h:i:s', strtotime($value['tgl_instruksi'])) . '&nbsp&nbsp&nbsp</td>';
                                }

                                $html .= '<td>' . $value['jns_instruksi'] . '</td>';
                                $firstFlag = false;
                            } else {
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                            }
                            $html .= '<td>:</td>';
                            if ($value['jns_instruksi'] == 'PAKET') {
                                $paket = '-';
                                $temp = [];

                                if (!empty($value['tipepaket_id'])) {
                                    foreach ($value['tipepaket_id'] as $newKey => $newValue) {
                                        $temp[] = $newValue['daftartindakan_nama'];
                                    }

                                    if (!empty($temp)) {
                                        $paket = implode(", ", $temp);
                                    }
                                }
                                $html .= $value['is_deleted'] == true ? '<td><strike>' . $value['nama_instruksi'] . ' : ' . $paket . '</strike></td>' : '<td>' . $value['nama_instruksi'] . ' : ' . $paket . '</td>';
                            } else {
                                $html .= '<td>' . $value['nama_instruksi'] . '</td>';
                            }
                            if ($secondFlag == true) {
                                if ($value['status'] == '454' || $value['status'] == '470' || $value['status'] == '346') {
                                    $html .= '<td>' . Html::button('<b><i class="fa fa-pencil"></i></b>', [
                                        'class' => 'btn btn-info btn-ubah-terapi btn-link',
                                        'data-cpptid' => DocoHelpers::encrypt($value['cppt_id']),
                                        'data-instruksiid' => DocoHelpers::encrypt($value['instruksi_id']),
                                        'data-jnsinstruksi' => $value['jns_instruksi']
                                    ]) . '</td>';
                                    $html .= '<td>' . Html::button('<b><i class="fa fa-trash"></i></b>', [
                                        'class' => 'btn btn-info btn-hapus-terapi btn-link',
                                        'data-cpptid' => DocoHelpers::encrypt($value['cppt_id']),
                                        'data-instruksiid' => DocoHelpers::encrypt($value['instruksi_id']),
                                        'data-jnsinstruksi' => $value['jns_instruksi'],
                                        'action' => '/ranap/pemeriksaan-rawat-inap/hapus-terapi?id=' . $this->_pendaftaran_id . '&cppt_id=' . DocoHelpers::encrypt($value['cppt_id']) . '&instruksi_id=' . DocoHelpers::encrypt($value['instruksi_id']) . '&jns_instruksi=' . $value['jns_instruksi']
                                    ]) . '</td>';
                                } else {
                                    $html .= '<td></td>';
                                    $html .= '<td></td>';
                                }
                                $secondFlag = false;
                            } else {
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                            }
                            $html .= '</tr>';
                        }
                    }

                    $firstFlag = true;
                    $secondFlag = true;
                }
            }
        }

        $html .= '</table>';

        return $html;
    }

    // Get tanggal dpjp
    private function getTanggalDpjp($dataDetail)
    {
        // Deklarasi html
        $html = '<table border="0" cellpadding="0" cellspacing="0">';

        // Cek data detail
        if (!empty($dataDetail)) {
            // Inisiasi instruksi
            $instruksi = [];
            $tempInstruksi = '';

            // Loop untuk membuat data per row
            foreach ($dataDetail as $key => $value) {
                // Cek temp instruksi
                if ($tempInstruksi != $value['instruksi_id']) {
                    // Cek deleted
                    if ($value['is_deleted'] == true) {
                        // Set html
                        $html .= '<tr>';
                        $html .= '<td><strike>' . date('d-m-Y / h:i:s', strtotime($value['tgl_instruksi'])) . '</strike></td>';
                        $html .= '</tr>';
                    } else {
                        // Set html
                        $html .= '<tr>';
                        $html .= '<td>' . date('d-m-Y / h:i:s', strtotime($value['tgl_instruksi'])) . '</td>';
                        $html .= '</tr>';
                    }
                } else {
                    // Set html
                    $html .= '<tr>';
                    $html .= '<td><br></td>';
                    $html .= '</tr>';
                }

                // Assign temp instruksi
                $tempInstruksi = $value['instruksi_id'];
            }
        }

        // Set end tag html
        $html .= '</table>';

        // Return
        return $html;
    }

    private function getVerifikasi($data, $data_instruksi, $status_disabled)
    {
        $pegawai_id = $this->_pegawai_id;
        $dpjp = $data['dokteradmisi_id'];
        $pemberi_instruksi = $data['pemberi_instruksi_id'];
        $html = '';
        if (!$data['is_deleted']) {
            // cek verifikasi verbal order
            if ($data['instruksi']) {
                if ($data['is_verifikasi_verbal']) {
                    $html .= '<span>' . $data['pegawai_verifikasi_verbal'] . ' ' . date('d/m/Y H:i:s', strtotime($data['tgl_verif_verbal'])) . '</span>';
                } else {
                    if ($pegawai_id == $pemberi_instruksi && $this->_user_identity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS) {
                        $html .= Html::button('<b><i class="fa fa-check-square"></i></b>' . Yii::t('fe', 'Verifikasi verbal order'), [
                            'class' => 'btn btn-info btn-labeled btn-xs btn-verifikasi-verbal',
                            'style' => 'margin-top:10px',
                            'data-id' => $data['cppt_id'],
                            'disabled' => $status_disabled,
                            'data-cpptid' => DocoHelpers::encrypt($data['cppt_id']),
                            'data-confirm-message' => Yii::t('fe', 'Apakah anda yakin akan memverifikasi verbal order tersebut?'),
                        ]);
                    }
                }
                $html .= '<br>';
            }

            // cek verifikasi dpjp
            if ($data['is_verifikasi']) {
                $html .= '<span>' . $data['pegawai_verifikasi'] . ' ' . date('d/m/Y H:i:s', strtotime($data['tgl_verifikasi'])) . '</span>';
                $html .= '<br>';
            } else {
                if ($pegawai_id == $dpjp && $this->_user_identity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS && empty($pemberi_instruksi)) {
                    $html .= Html::button('<b><i class="fa fa-check-square"></i></b>' . Yii::t('fe', 'Verifikasi DPJP'), [
                        'class' => 'btn btn-info btn-labeled btn-xs btn-verifikasi-dpjp',
                        'style' => 'margin-top:10px',
                        'data-id' => $data['origin_cppt_id'],
                        'data-xx' => $status_disabled,
                        'disabled' => $status_disabled,
                        'data-cpptid' => DocoHelpers::encrypt($data['origin_cppt_id']),
                        'data-confirm-message' => Yii::t('fe', 'Apakah anda yakin akan memverifikasi CPPT tersebut?'),
                    ]);
                    $html .= '<br>';
                }
            }

            // cek verifikasi untuk button tambah
            // jika telah verifikasi dpjp button tambah terapi tidak muncul
            // Button terapi dipindah keluar (Aktifkan buat memunculkan kembali)
            // if($data['is_verifikasi'] != true && $data['pegawai_verifikasi'] == null){
            //     $userIdentity = $this->_user_identity;
            //     $html .= Html::button('<b><i class="fa fa-plus"></i></b>'.Yii::t('fe', 'Tambah Terapi'), [
            //         'class' => 'btn btn-info btn-labeled btn-xs btn-tambah-terapi',
            //         'style' => 'margin-top:10px',
            //         'data-id' => $data['cppt_id'],
            //         'data-xx' => $status_disabled,
            //         'data-cpptid' => DocoHelpers::encrypt($data['cppt_id']),
            //         'disabled' => (($this->_pegawai_id == $data['pegawai_id']) && ($status_disabled != 'true') && !empty($data['pasienadmisi_id'])) ? false : true,
            //     ]);
            //     $html .= '<br>';
            // }
            // End Button terapi dipindah keluar (Aktifkan buat memunculkan kembali)
            if ($pegawai_id == $data['pegawai_id'] && empty($pemberi_instruksi)) {
                $html .= Html::button('<b><i class="fa fa-pencil"></i></b>' . Yii::t('fe', 'Edit SOAP'), [
                    'class' => $data['tipe'] != 'RI' ? 'hidden' : 'btn btn-info btn-labeled btn-xs btn-edit-soap',
                    'data-cpptid' => DocoHelpers::encrypt($data['origin_cppt_id']),
                    'data-is_icd_x' => isset($data['is_icd_x']) ? $data['is_icd_x'] : true,
                    'disabled' => $data['tipe'] != 'RI' || $data['is_verifikasi'] ? true : false
                ]);
                $html .= Html::button('<b><i class="fa fa-copy"></i></b>' . Yii::t('fe', 'Copy'), [
                    'class' => $data['tipe'] != 'RI' ? 'hidden' : 'btn btn-info btn-labeled btn-xs btn-copy-soap',
                    'data-cpptid' => DocoHelpers::encrypt($data['origin_cppt_id']),
                    'data-is_icd_x' => isset($data['is_icd_x']) ? $data['is_icd_x'] : true,
                    'disabled' => $data['tipe'] != 'RI' || $data['is_verifikasi'] ? true : false
                ]);
                $html .= Html::button('<b><i class="fa fa-times"></i></b>' . Yii::t('fe', 'Batal Edit SOAP'), [
                    'class' => 'btn btn-danger btn-labeled btn-xs btn-cancel-edit-soap hidden'
                ]);
            }

            if($pegawai_id == $data['pegawai_id'] && !$data['is_verifikasi']) {
                $html .= Html::button('<b><i class="fa fa-trash"></i></b>' . Yii::t('fe', 'Hapus SOAP'), [
                    'class' => $data['tipe'] != 'RI' && $data['tipe'] != 'RI-SOAPFISIO' ? 'hidden' : 'btn btn-info btn-labeled btn-xs btn-delete-cppt',
                    'data-cpptid' => DocoHelpers::encrypt($data['origin_cppt_id']),
                    'data-tipe' => $data['tipe']
                ]);
            }

            $html .= '<br>';
        } else {
            $tgl_edit = isset($data['created_date']) ? date('d/m/Y / H:i:s', strtotime($data['created_date'])) : '';
            $html .= '<p> Data sudah di ubah oleh <br>' . $data['pegawai_update_nama'] . ' - <br>' . $tgl_edit . '</p>';
        }

        return $html;
    }

    public function actionCpptVerifikasiDpjp()
    {
        $request = Yii::$app->request;
        $cppt_id = DocoHelpers::decrypt($request->get('cppt_id'));
        $ispagt = $request->get('ispagt', false);
        try {
            $response = $this->_restRanap->post('cppt/verifikasi', [
                'form_params' => ['id' => $cppt_id, 'jenis' => 'dpjp', 'ispagt' => $ispagt]
            ]);
            $response = json_decode($response->getBody(), true);
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            // Exception
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (\Exception $e) {
            // Exception
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    public function actionCpptVerifikasiVerbalOrder()
    {
        $request = Yii::$app->request;
        $cppt_id = DocoHelpers::decrypt($request->get('id'));

        try {
            $response = $this->_restRanap->post('cppt/verifikasi', [
                'form_params' => ['id' => $cppt_id, 'jenis' => 'verbal']
            ]);
            $response = json_decode($response->getBody(), true);
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            // Exception
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (\Exception $e) {
            // Exception
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    // Get aksi
    private function getAksi($data)
    {
        // Initiate html
        $html = '';

        // Cek verifikasi
        if ($data['is_verifikasi'] == false) {
            // Cek pegawai
            // if ($this->_pegawai_id == $data['pegawai_id']) {
            //     // Set html
            //     $html .= Html::button('<b><i class="fa fa-plus"></i></b>'.Yii::t('fe', 'Tambah Terapi'), [
            //      'class' => 'btn btn-info btn-labeled btn-xs btn-tambah-terapi',
            //      'data-id' => $data['cppt_id'],
            //      'data-cpptid' => DocoHelpers::encrypt($data['cppt_id'])
            //     ]);
            // }
            // else {
            //     // Set html
            //     $html .= Html::button('<b><i class="fa fa-plus"></i></b>'.Yii::t('fe', 'Tambah Terapi'), [
            //      'class' => 'btn btn-info btn-labeled btn-xs btn-tambah-terapi',
            //      'data-id' => $data['cppt_id'],
            //      'data-cpptid' => DocoHelpers::encrypt($data['cppt_id']),
            //      'disabled' => 'disabled'
            //     ]);
            // }
            // Button terapi dipindah keluar (Aktifkan buat memunculkan kembali)
            // $html .= Html::button('<b><i class="fa fa-plus"></i></b>'.Yii::t('fe', 'Tambah Terapi'), [
            //     'class' => 'btn btn-info btn-labeled btn-xs btn-tambah-terapi',
            //     'data-id' => $data['cppt_id'],
            //     'data-cpptid' => DocoHelpers::encrypt($data['cppt_id']),
            //     'disabled' => $this->_pegawai_id == $data['pegawai_id'] ? false : true,
            // ]);
            // End Button terapi dipindah keluar (Aktifkan buat memunculkan kembali)
        } else {
            // Set html
            // $html .= Html::tag('span', $data['pegawai_verifikasi'].' '.date('d-m-Y H:i:s', strtotime($data['tgl_verifikasi'])), []);
        }

        // Return
        return $html;
    }

    // Add session reseptur
    private function addSessionReseptur($pendaftaran_id, $data = null)
    {
        $session = Yii::$app->session;

        $pendaftaran_id_encrypt = $pendaftaran_id;
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id_encrypt);
        $cppt_id = isset($data['cppt_id']) ? $data['cppt_id'] : '';

        try {
            // get session
            $session_key = 1;
            if (isset($session['pemeriksaan_reseptur'])) {
                $session_reseptur = $session['pemeriksaan_reseptur'];
            }

            if (isset($session_reseptur[$pendaftaran_id_encrypt . $cppt_id])) {
                foreach ($session_reseptur[$pendaftaran_id_encrypt . $cppt_id] as $key => $value) {
                    $session_key = $value['session_key'];
                }

                $session_key++;
            }

            $count_data_insert = 0;
            $data_reseptur = [];
            $data_reseptur['jenis_racikan'] = !empty($data['rke']) ? ResepturDetailForm::VC_RC : ResepturDetailForm::VC_NRC;
            $data_reseptur['nama_racikan'] = !empty($data['rke']) ? 'Obat Racikan' : 'Non racikan';
            $data_reseptur['rke'] = @$data['rke'];
            $data_reseptur['signa_reseptur'] = $data['signa_reseptur'];
            if (is_array($data['obatalkes_id'])) {
                foreach ($data['obatalkes_id'] as $key => $value) {
                    $data_reseptur['obatalkes_id'][] = $value;

                    $count_data_insert++;
                }
            } else {
                $data_reseptur['obatalkes_id'] = $data['obatalkes_id'];
            }
            if (is_array($data['qty_reseptur'])) {
                foreach ($data['qty_reseptur'] as $key => $value) {
                    $data_reseptur['qty_reseptur'][] = $value;
                }
            } else {
                $data_reseptur['qty_reseptur'] = $data['qty_reseptur'];
            }
            if (is_array($data['qty_konversi'])) {
                foreach ($data['qty_konversi'] as $key => $value) {
                    $data_reseptur['qty_konversi'][] = $value;
                }
            } else {
                $data_reseptur['qty_konversi'] = $data['qty_konversi'];
            }
            if (is_array($data['satuankecil_id'])) {
                foreach ($data['satuankecil_id'] as $key => $value) {
                    $data_reseptur['satuankecil_id'][] = $value;
                }
            } else {
                $data_reseptur['satuankecil_id'] = $data['satuankecil_id'];
            }
            if (is_array($data['nilai_konversi'])) {
                foreach ($data['nilai_konversi'] as $key => $value) {
                    $data_reseptur['nilai_konversi'][] = $value;
                }
            } else {
                $data_reseptur['nilai_konversi'] = $data['nilai_konversi'];
            }
            if (is_array($data['satuankecil'])) {
                foreach ($data['satuankecil'] as $key => $value) {
                    $data_reseptur['satuankecil'][] = $value;
                }
            } else {
                $data_reseptur['satuankecil'] = $data['satuankecil'];
            }
            if (is_array($data['jml_konversi'])) {
                foreach ($data['jml_konversi'] as $key => $value) {
                    $data_reseptur['jml_konversi'][] = $value;
                }
            } else {
                $data_reseptur['jml_konversi'] = $data['jml_konversi'];
            }
            if (is_array($data['satuanbesar_id'])) {
                foreach ($data['satuanbesar_id'] as $key => $value) {
                    $data_reseptur['satuanbesar_id'][] = $value;
                }
            } else {
                $data_reseptur['satuanbesar_id'] = $data['satuanbesar_id'];
            }
            if (is_array($data['etiket'])) {
                foreach ($data['etiket'] as $key => $value) {
                    $data_reseptur['etiket'][] = $value;
                }
            } else {
                $data_reseptur['etiket'] = $data['etiket'];
            }
            if (is_array($data['satuan_penyimpanan'])) {
                foreach ($data['satuan_penyimpanan'] as $key => $value) {
                    $data_reseptur['satuan_penyimpanan'][] = $value;
                }
            } else {
                $data_reseptur['satuan_penyimpanan'] = $data['satuan_penyimpanan'];
            }
            if (is_array($data['satuankecil_nama'])) {
                foreach ($data['satuankecil_nama'] as $key => $value) {
                    $data_reseptur['satuankecil_nama'][] = $value;
                }
            } else {
                $data_reseptur['satuankecil_nama'] = $data['satuankecil_nama'];
            }
            if (is_array($data['hargasatuan_reseptur'])) {
                foreach ($data['hargasatuan_reseptur'] as $key => $value) {
                    $data_reseptur['hargasatuan_reseptur'][] = $value;
                }
            } else {
                $data_reseptur['hargasatuan_reseptur'] = $data['hargasatuan_reseptur'];
            }

            if ($count_data_insert != 0) {
                for ($i = 0; $i < $count_data_insert; $i++) {
                    if (!empty($session_reseptur[$pendaftaran_id_encrypt . $cppt_id])) {
                        foreach ($session_reseptur[$pendaftaran_id_encrypt . $cppt_id] as $key => $value) {
                            // if (($value['obatalkes_id'] == $data_reseptur['obatalkes_id'][$i]) && ($value['jenis_racikan'] == $data_reseptur['jenis_racikan'])) {
                            // if (($value['rke'] == $data_reseptur['rke']) && ($value['obatalkes_id'] == $data_reseptur['obatalkes_id'][$i]) && ($value['satuankecil_nama'] == $data_reseptur['satuankecil_nama'][$i]) && ($value['jenis_racikan'] == $data_reseptur['jenis_racikan'])) {
                            if (($value['rke'] == $data_reseptur['rke']) && ($value['obatalkes_id'] == $data_reseptur['obatalkes_id'][$i]) && ($value['jenis_racikan'] == $data_reseptur['jenis_racikan'])) {
                                // $message = Yii::t('fe', 'Tidak bisa menambahkan obat yang sama, silahkan ubah qty pada tabel dibawah!');
                                $message = Yii::t('fe', 'Obat sudah diinputkan!');
                                return [
                                    'status' => 100,
                                    'message' => $message,
                                ];
                            }
                        }
                    }
                    $session_reseptur[$pendaftaran_id_encrypt . $cppt_id][] = [
                        'session_key' => $session_key,
                        'resepturdetail_id' => '',
                        'jenis_racikan' => $data_reseptur['jenis_racikan'],
                        'nama_racikan' => $data_reseptur['nama_racikan'],
                        'rke' => $data_reseptur['rke'],
                        'signa_reseptur' => $data_reseptur['signa_reseptur'],
                        'obatalkes_id' => $data_reseptur['obatalkes_id'][$i],
                        'qty_reseptur' => $data_reseptur['qty_reseptur'][$i],
                        'qty_konversi' => $data_reseptur['qty_konversi'][$i],
                        'satuankecil_id' => $data_reseptur['satuankecil_id'][$i],
                        'satuankecil_nama' => $data_reseptur['satuankecil_nama'][$i],
                        'hargasatuan_reseptur' => $data_reseptur['hargasatuan_reseptur'][$i],
                        'nilai_konversi' => $data_reseptur['nilai_konversi'][$i],
                        'satuankecil' => $data_reseptur['satuankecil'][$i],
                        'jml_konversi' => $data_reseptur['jml_konversi'][$i],
                        'satuanbesar_id' => $data_reseptur['satuanbesar_id'][$i],
                        'etiket' => $data_reseptur['etiket'],
                        'satuan_penyimpanan' => $data_reseptur['satuan_penyimpanan'][$i],
                    ];
                    $session_key++;
                }
                $session->set('pemeriksaan_reseptur', $session_reseptur);
            } else {
                if (!empty($session_reseptur[$pendaftaran_id_encrypt . $cppt_id])) {
                    foreach ($session_reseptur[$pendaftaran_id_encrypt . $cppt_id] as $key => $value) {
                        // if (($value['obatalkes_id'] == $data_reseptur['obatalkes_id']) && ($value['jenis_racikan'] == $data_reseptur['jenis_racikan'])) {
                        // if (($value['rke'] == $data_reseptur['rke']) && ($value['obatalkes_id'] == $data_reseptur['obatalkes_id']) && ($value['satuankecil_nama'] == $data_reseptur['satuankecil_nama']) && ($value['jenis_racikan'] == $data_reseptur['jenis_racikan'])) {
                        if (($value['rke'] == $data_reseptur['rke']) && ($value['obatalkes_id'] == $data_reseptur['obatalkes_id']) && ($value['jenis_racikan'] == $data_reseptur['jenis_racikan'])) {
                            // $message = Yii::t('fe', 'Tidak bisa menambahkan obat yang sama, silahkan ubah qty pada tabel dibawah!');
                            $message = Yii::t('fe', 'Obat sudah diinputkan!');
                            return [
                                'status' => 100,
                                'message' => $message,
                            ];
                        }
                    }
                }

                $session_reseptur[$pendaftaran_id_encrypt . $cppt_id][] = [
                    'session_key' => $session_key,
                    'resepturdetail_id' => '',
                    'jenis_racikan' => $data_reseptur['jenis_racikan'],
                    'nama_racikan' => $data_reseptur['nama_racikan'],
                    'rke' => $data_reseptur['rke'],
                    'signa_reseptur' => $data_reseptur['signa_reseptur'],
                    'obatalkes_id' => $data_reseptur['obatalkes_id'],
                    'qty_reseptur' => $data_reseptur['qty_reseptur'],
                    'qty_konversi' => $data_reseptur['qty_konversi'],
                    'satuankecil_id' => $data_reseptur['satuankecil_id'],
                    'satuankecil_nama' => $data_reseptur['satuankecil_nama'],
                    'hargasatuan_reseptur' => $data_reseptur['hargasatuan_reseptur'],
                    'nilai_konversi' => $data_reseptur['nilai_konversi'],
                    'satuankecil' => $data_reseptur['satuankecil'],
                    'jml_konversi' => $data_reseptur['jml_konversi'],
                    'satuanbesar_id' => $data_reseptur['satuanbesar_id'],
                    'etiket' => $data_reseptur['etiket'],
                    'satuan_penyimpanan' => $data_reseptur['satuan_penyimpanan'],
                ];
                $session->set('pemeriksaan_reseptur', $session_reseptur);
            }

            return [
                'status' => 200,
                'message' => Yii::t('fe', 'OK'),
                'data' => $session['pemeriksaan_reseptur'],
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage(),
            ];
        }
    }

    // Reset session reseptur
    private function resetResepturSession($id, $cppt_id)
    {
        // Try
        try {
            $session = Yii::$app->session;

            if ($id) {
                if (isset($session['pemeriksaan_reseptur'])) {
                    $temp_session = $session['pemeriksaan_reseptur'];
                    if (isset($temp_session[$id . $cppt_id])) {
                        unset($temp_session[$id . $cppt_id]);
                    }
                    $session->set('pemeriksaan_reseptur', $temp_session);
                }
            }

            if (isset($session['pemeriksaan_reseptur_db'])) {
                $session->remove('pemeriksaan_reseptur_db');
            }

            return true;
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    /* Get sisa stok obat */
    public function actionGetSisaStokObat($id, $cppt_id, $obatalkes_id)
    {
        try {
            $session = Yii::$app->session;
            $encrypted_pendaftaran_id = $id;
            $pendaftaran_id = DocoHelpers::decrypt($encrypted_pendaftaran_id);
            $qty_obat = 0;
            $flag = true;

            if ($obatalkes_id != '') {
                if (isset($session['pemeriksaan_reseptur'])) {
                    $session_reseptur = $session['pemeriksaan_reseptur'];

                    if (isset($session_reseptur[$encrypted_pendaftaran_id . $cppt_id])) {
                        foreach ($session_reseptur[$encrypted_pendaftaran_id . $cppt_id] as $key => $value) {
                            if ($value['obatalkes_id'] == $obatalkes_id) {
                                if ($flag) {
                                    if (isset($value['resepturdetail_id']) && $value['resepturdetail_id'] != '') {
                                        $qty_obat = 0;
                                    } else {
                                        $qty_obat = $value['qty_reseptur'];
                                    }

                                    $flag = false;
                                } else {
                                    if (isset($value['resepturdetail_id']) && $value['resepturdetail_id'] != '') {
                                        $qty_obat = $qty_obat + 0;
                                    } else {
                                        $qty_obat = $qty_obat + $value['qty_reseptur'];
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return $qty_obat;
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    /* Set qty reseptur */
    public function actionSetQtyReseptur($id, $cppt_id, $obatalkes_id, $jenis_racikan, $qty)
    {
        try {
            $session = Yii::$app->session;
            $encrypted_pendaftaran_id = $id;
            $pendaftaran_id = DocoHelpers::decrypt($encrypted_pendaftaran_id);
            $qty_obat = 0;
            $flag = true;

            if ($obatalkes_id != '') {
                if (isset($session['pemeriksaan_reseptur'])) {
                    $session_reseptur = $session['pemeriksaan_reseptur'];

                    if (isset($session_reseptur[$encrypted_pendaftaran_id . $cppt_id])) {
                        foreach ($session_reseptur[$encrypted_pendaftaran_id . $cppt_id] as $key => $value) {
                            if ($value['obatalkes_id'] == $obatalkes_id && $value['jenis_racikan'] == $jenis_racikan) {
                                $session_reseptur[$encrypted_pendaftaran_id . $cppt_id][$key]['last_qty_reseptur'] = $session_reseptur[$encrypted_pendaftaran_id . $cppt_id][$key]['qty_reseptur'];
                                $session_reseptur[$encrypted_pendaftaran_id . $cppt_id][$key]['qty_reseptur'] = floatval($qty);

                                $session->set('pemeriksaan_reseptur', $session_reseptur);
                            }
                        }
                    }
                }
            }

            return $qty_obat;
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    // Generate tabel reseptur
    private function generateTableReseptur($pendaftaran_id_encrypt, $datas = [], $cppt_id, $ruangan_id)
    {
        $request = Yii::$app->request;
        $session = Yii::$app->session;

        foreach ($datas as $eachData) {
            $medIds[] = $eachData['obatalkes_id'];
        }
        $list_data = $this->getDataSignaDanObatalkes($medIds);
        $list_data_signa = ArrayHelper::map($list_data['listDataSigna'], 'signa_id', 'signa_nama');
        $list_data_obatalkes = ArrayHelper::map($list_data['listDataObatalkesByInstalasi'], 'obatalkes_id', 'obatalkes_namalain');
        $list_data_obatalkes_qty = [];
        $data_tables = [];
        $stok_tersedia = [];
        $no = 1;

        if (!empty($list_data['listDataObatalkesByInstalasi'])) {
            foreach ($list_data['listDataObatalkesByInstalasi'] as $key => $value) {
                if ($ruangan_id == $value['ruangan_id']) {
                    $list_data_obatalkes_qty[$value['obatalkes_id']] = $value['qty_tersedia'];
                }
            }
        }

        if (isset($session['obat_dihapus'])) {
            $session_obat_dihapus = $session['obat_dihapus'];
        } else {
            $session_obat_dihapus = [];
        }

        if (!empty($session_obat_dihapus)) {
            foreach ($session_obat_dihapus as $key => $value) {
                if (isset($temp_session_obat_dihapus[$value['obatalkes_id']])) {
                    $temp_session_obat_dihapus[$value['obatalkes_id']] = $temp_session_obat_dihapus[$value['obatalkes_id']] + $value['qty_reseptur'];
                } else {
                    $temp_session_obat_dihapus[$value['obatalkes_id']] = $value['qty_reseptur'];
                }
            }
        }

        if ($datas) {
            foreach ($datas as $data) {
                $data_table = [];
                $primaryKey = $data['session_key'];

                if (isset($list_data_obatalkes_qty[$data['obatalkes_id']])) {
                    if (isset($data['resepturdetail_id']) && $data['resepturdetail_id'] != '') {
                        if (isset($data['last_qty_reseptur']) && $data['last_qty_reseptur'] != '') {
                            if (isset($temp_session_obat_dihapus[$data['obatalkes_id']])) {
                                $stok_tersedia[$data['obatalkes_id']] = floatval($list_data_obatalkes_qty[$data['obatalkes_id']]) + $data['last_qty_reseptur'] - floatval($data['qty_reseptur']) + $temp_session_obat_dihapus[$data['obatalkes_id']];
                                $list_data_obatalkes_qty[$data['obatalkes_id']] = $stok_tersedia[$data['obatalkes_id']];
                            } else {
                                $stok_tersedia[$data['obatalkes_id']] = floatval($list_data_obatalkes_qty[$data['obatalkes_id']]) + $data['last_qty_reseptur'] - floatval($data['qty_reseptur']);
                                $list_data_obatalkes_qty[$data['obatalkes_id']] = $stok_tersedia[$data['obatalkes_id']];
                            }
                        } else {
                            if (isset($temp_session_obat_dihapus[$data['obatalkes_id']])) {
                                $stok_tersedia[$data['obatalkes_id']] = floatval($list_data_obatalkes_qty[$data['obatalkes_id']]) + $temp_session_obat_dihapus[$data['obatalkes_id']];
                                $list_data_obatalkes_qty[$data['obatalkes_id']] = $stok_tersedia[$data['obatalkes_id']];
                            } else {
                                $stok_tersedia[$data['obatalkes_id']] = floatval($list_data_obatalkes_qty[$data['obatalkes_id']]);
                                $list_data_obatalkes_qty[$data['obatalkes_id']] = $stok_tersedia[$data['obatalkes_id']];
                            }
                        }
                    } else {
                        $stok_tersedia[$data['obatalkes_id']] = floatval($list_data_obatalkes_qty[$data['obatalkes_id']]) - floatval($data['qty_reseptur']);
                        $list_data_obatalkes_qty[$data['obatalkes_id']] = $stok_tersedia[$data['obatalkes_id']];
                    }
                } else {
                    $stok_tersedia[$data['obatalkes_id']] = 0;
                }

                $data_table['no'] = $no;
                $data_table['session_key'] = $data['session_key'];
                $data_table['jenis_racikan'] = $data['jenis_racikan'];
                $data_table['nama_racikan'] = $data['nama_racikan'];
                $data_table['rke'] = !empty($data['rke']) ? $data['rke'] : '-';
                $data_table['signa_reseptur'] = $data['signa_reseptur'];
                $data_table['obatalkes_id'] = $data['obatalkes_id'];
                $data_table['obatalkes_nama'] = isset($list_data_obatalkes[$data['obatalkes_id']]) ? $list_data_obatalkes[$data['obatalkes_id']] : '-';
                $data_table['qty_reseptur'] = isset($data['jml_konversi']) ? $data['jml_konversi'] : 0;
                $data_table['qty_konv'] = $data['qty_reseptur'] . ' ' . @$data['satuan_penyimpanan'];
                $data_table['satuan_penyimpanan'] = @$data['satuan_penyimpanan'];
                $data_table['jml_konversi'] = $data['qty_reseptur'];
                $data_table['satuankecil_id'] = $data['satuankecil_id'];
                $data_table['satuankecil_nama'] = $data['satuankecil_nama'];
                $data_table['nilai_konversi'] = @$data['nilai_konversi'];
                $data_table['hargasatuan_reseptur'] = '<div class="harga_satuan">' . $data['hargasatuan_reseptur'] . '</div>';
                $data_table['jumlah_harga'] = '<div class="jumlah_harga">' . number_format(@$data['jml_konversi'] *  DocoHelpers::convertToAngka($data['hargasatuan_reseptur']), 2, ',', '.') . '</div>';
                $data_table['etiket'] = @$data['etiket'];
                $data_table['resepturdetail_id'] = $data['resepturdetail_id'];

                $data_table['signa_edit'] = Html::textInput('signa_edit_' . $primaryKey, $data['signa_reseptur'], ['class' => 'form-control input-xs']);

                $data_table['qty_edit'] = Html::textInput('qty_edit_' . $primaryKey, @$data['jml_konversi'], ['class' => 'form-control input-xs doco-decimal-wcomma qty', 'readonly' => 'readonly']);

                $data_table['aksi'] = Html::button(
                    '<i class="fa fa-times"></i>',
                    [
                        'class' => 'btn btn-indian-red btn-sm delete-reseptur',
                        'action' => '/ranap/pemeriksaan-rawat-inap/batal-session-reseptur?id=' . $pendaftaran_id_encrypt . '&sess_key=' . $primaryKey . '&pendaftaran_id=' . $pendaftaran_id_encrypt . '&cppt_id=' . $cppt_id,
                        'data-confirm-message' => Yii::t('fe', 'confirm_batal'),
                        'onclick' => 'batalSessionReseptur(this)'
                    ]
                );

                array_push($data_tables, $data_table);
                $no++;
            }

            if (!empty($data_tables)) {
                for ($i = 0; $i < count($data_tables); $i++) {
                    $data_tables[$i]['stok_tersedia'] = isset($stok_tersedia[$data_tables[$i]['obatalkes_id']]) ? $stok_tersedia[$data_tables[$i]['obatalkes_id']] : 0;
                    if (@$data['nilai_konversi'] !== null || @$data['nilai_konversi'] > 0) {
                        $data_tables[$i]['stok_konversi'] = number_format(($data_tables[$i]['stok_tersedia'] / @$data['nilai_konversi']), 2);
                    }
                }
            }
        }

        $return = [
            'data' => $data_tables,
            'draw' => $request->get('draw', null),
            'recordsTotal' => count($data_tables),
            'recordsFiltered' => count($data_tables)
        ];

        return $return;
    }

    /* Get list data signa dan obatalkes */
    private function getDataSignaDanObatalkes($medIds = [])
    {
        try {
            $request = $this->_restRanap->get('cppt/get-data-signa-dan-obatalkes', ['form_params' => compact('medIds')]);
            $response = json_decode($request->getBody(), true);

            // Return response
            return $response['response'];
        } catch (\Exception $e) {
            // Exception
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (RequestException $e) {
            // Exception
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    /*@author: Ardi Pratama*/
    /*used by: Terapi Tindakan CPPT Ranap*/
    public function actionCpptCreateTerapiTindakan()
    {
        $docoVars = Yii::$app->docoVars;
        $response['response'] = [
            'text' => 'Terjadi Kesalahan',
            'title' => 'Proses Gagal !'
        ];
        $codeHttp = 422;
        try {
            $request = Yii::$app->request;
            if (!empty($request->post())) {
                // Assign
                $post = $request->post();
                $post['pegawai_ruangan'] = $docoVars->user('id_pegawai') ? $docoVars->user('id_pegawai') : 1;
                // Send to backend

                $response = $this->_restRanap->post('pemeriksaan-rawat-inap/cppt-create-tindakan', [
                    'form_params' => $post
                ]);
                $response = json_decode($response->getBody(), true);
                // dump($response);die;
                // Assign new response
                $newResponse['status'] = $response['metadata']['status'];
                // $newResponse['pendaftaran_id'] =
                $codeHttp = 200;
                $response['response'] = [
                    'text' => 'Data Berhasil Disimpan',
                    'title' => 'Proses berhasil !'
                ];
                // Return
                // return json_encode($newResponse);
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['response']['text'] = 'Terjadi kesalah pada sistem';
            $response['response']['message'] = $e->getMessage();
        } catch (\Exception $e) {
            // Exception
            Yii::info($e->getMessage());
            $response['response']['text'] = 'Terjadi kesalah pada sistem';
            $response['response']['message'] = $e->getMessage();
        }
        return DocoHelpers::response($response, $codeHttp);
    }

    public function actionCpptGetTindakanDetail(
        $id = null,
        $detail_id = null,
        $penjamin_id = null,
        $kelaspelayanan_id = null,
        $tipe = null
    ) {

        \Yii::$app->response->statusCode = 200;
        // Response format
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        // Check id
        if ($detail_id == null) {
            \Yii::$app->response->statusCode = 500;
            // Return
            return [
                'status' => 500,
                'message' => 'Tindakan tidak ditemukan',
            ];
        }
        // Check tipe
        if ($tipe == 'tindakan') {
            // Get response
            // $url = 'pemeriksaan-rawat-inap/cppt-get-tindakan-ruangan?ruanganId='.$this->_id_ruangan.'&kelasPelayananId='.$kelaspelayanan_id.'&id='.$detail_id.'&penjaminId='.$penjamin_id;
            // $response_tindakan = $this->_restRanap->get($url);
            $response_tindakan = $this->_restRanap->get('pemeriksaan-rawat-inap/cppt-get-tindakan-ruangan?ruanganId=' . $this->_id_ruangan . '&kelasPelayananId=' . $kelaspelayanan_id . '&id=' . $detail_id . '&penjaminId=' . $penjamin_id);
        } else {
            // Get response
            // $response_tindakan = $this->_restRanap->get('pemeriksaan-rawat-inap/cppt-get-paket-ruangan?ruanganId='.$this->_id_ruangan.'&kelasPelayananId='.$kelaspelayanan_id.'&id='.$detail_id.'&penjaminId='.$penjamin_id);
            $response_tindakan = $this->_restRanap->get('pemeriksaan-rawat-inap/cppt-get-paket-ruangan?ruanganId=' . $this->_id_ruangan . '&kelasPelayananId=' . $kelaspelayanan_id . '&id=' . $detail_id . '&penjaminId=' . $penjamin_id);
        }

        // Body
        $body = json_decode($response_tindakan->getBody(), true);
        $data = $body['response'];

        // Return
        return [
            'status' => 200,
            'data' => $data,
        ];
    }

    public function actionCpptGetObatAlkesByJenis()
    {
        $request = Yii::$app->request;
        // $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $instalasi_id = Yii::$app->docoVars->workspace('instalasi_id');
        Yii::$app->response->format = Response::FORMAT_JSON;
        $ruangan_id = $request->get('ruangan_id', 3);
        $group_jenis = $request->get('group_jenis');
        $penjaminId = $request->get('penjamin');
        $kelaspelayananId = $request->get('kelaspelayananId');
        $page = $request->get('page', 1);
        $keyword = $request->get('q', null);
        $limit = 11;

        $userIdentity = Yii::$app->session->get('user_identity');
        if ($userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN) {
            $group_jenis = DocoConstants::GOUP_ALKES;
        }

        $params = [
            'instalasi_id' => $instalasi_id,
            'ruangan_id' => $ruangan_id,
            'penjamin_id' => $penjaminId,
            'group_jenisobat' => $group_jenis,
            'kelaspelayanan_id' =>  $kelaspelayananId,
            'page' => $page,
            'keyword' => $keyword
        ];

        try {
            $response = $this->_restApotek->get('allow/get-list-stok-apotek', [
                'query' => $params,
            ]);

            $body = json_decode($response->getBody(), true);
            $response = isset($body['response']['data']) ? $body['response']['data'] : [];
            $dataObat = [
                'obat' => [],
                'alkes' => []
            ];
            foreach ($response as $key => $value) {
                $groupObat = isset($value['group_jenisobat']) ? $value['group_jenisobat'] : null;
                $status = ($value['qty_tersedia'] > 0) ? false : true;
                if ($groupObat === DocoConstants::GOUP_ALKES) {
                    $dataObat['alkes'][] = [
                        'id' => $value['obatalkes_id'],
                        'text' => $value['obatalkes_namalain'],
                        'qty_av' => $value['qty_tersedia'],
                        'disabled' => $status,
                        'value' => $value['obatalkes_id'],
                        'data_stok' => $value['qty_tersedia'],
                        'data_harga' => $value['hargaygdipakai'],
                        'qty_tersedia' => $value['qty_tersedia'],
                        'obatalkes_id' => $value['obatalkes_id'],
                        'options' => [
                            'disabled' => $status,
                            'data-hargajual' => $value['hargaygdipakai'],
                            'data-satuankecil_nama' => $value['satuankecil_nama'],
                            'data-satuankecil_id' => $value['satuankecil_id'],
                            'hargajual' => $value['hargaygdipakai'],
                            'satuankecil_nama' => $value['satuankecil_nama'],
                            'satuankecil_id' => $value['satuankecil_id'],
                        ],
                    ];
                } else {
                    $dataObat['obat'][] = [
                        'id' => $value['obatalkes_id'],
                        'text' => $value['obatalkes_namalain'],
                        'qty_av' => $value['qty_tersedia'],
                        'disabled' => $status,
                        'value' => $value['obatalkes_id'],
                        'data_stok' => $value['qty_tersedia'],
                        'data_harga' => $value['hargaygdipakai'],
                        'qty_tersedia' => $value['qty_tersedia'],
                        'obatalkes_id' => $value['obatalkes_id'],
                        'options' => [
                            'disabled' => $status,
                            'data-hargajual' => $value['hargaygdipakai'],
                            'data-satuankecil_nama' => $value['satuankecil_nama'],
                            'data-satuankecil_id' => $value['satuankecil_id'],
                            'hargajual' => $value['hargaygdipakai'],
                            'satuankecil_nama' => $value['satuankecil_nama'],
                            'satuankecil_id' => $value['satuankecil_id'],
                        ],
                    ];
                }
            }

            /** Handle double data on next page */
            $data = [];
            if (!empty($dataObat['obat'])) {
                $data['obat'] = $dataObat['obat'];
                $removed = (count($data['obat']) === $limit) ? array_pop($data['obat']) : false;
            } else {
                $data['alkes'] = $dataObat['alkes'];
                $removed = (count($data['alkes']) === $limit) ? array_pop($data['alkes']) : false;
            }

            return DocoHelpers::response([
                'response' => $data,
                'total_count' => (!empty($dataObat['obat'])) ? count($dataObat['obat']) : count($dataObat['alkes']),
                'incomplete_results' => false,
                'pagination' => ['more' => ((!empty($dataObat['obat'])) ? count($dataObat['obat'])  : $dataObat['alkes']) === $limit ? true : false]
            ]);

            // return DocoHelpers::response($body, false, true);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    // Get tindakan dan paket session
    public function actionCpptGetTindakanPaketSession()
    {
        $this->getRanapListDataAllow();
        // Session
        $session = Yii::$app->session;

        // Assign data
        $data['tindakan'] = $this->_list_data['data_tindakanruangan'];
        $data['paket'] = $this->_list_data['data_paket'];

        // Return
        return json_encode([
            'response' => $data
        ]);
    }

    public function actionCpptTransaksiInstruksi()
    {
        try {
            $this->getRanapListDataAllow();
            $pendaftaran_id = DocoHelpers::decrypt($this->_pendaftaran_id);
            $params = Yii::$app->request;
            $encryptedPendaftaranId = $params->get('id', 'MA');
            $cppt_id = DocoHelpers::decrypt($params->get('cppt_id', 'MA'));
            $instruksi_id = DocoHelpers::decrypt($params->get('instruksi_id', 'MA'));
            $jns_instruksi = $params->get('jns_instruksi', '');
            $sourceTab =  $params->get('from', 'cppt');
            $isUbah =  $params->get('is_ubah', "0");
            $isEditTindakan = false;
            $isEditBmhp = false;
            $isEditReseptur = false;
            $id_jenisinstruksi = 0;
            $iter = null;
            $initObatAlkes = [];
            if ($isUbah == "1" && $jns_instruksi != '') {
                switch ($jns_instruksi) {
                    case 'TINDAKANBMHP':
                        $isEditTindakan = true;
                        $id_jenisinstruksi = 457;
                        break;
                    case 'BMHP':
                        $isEditBmhp = true;
                        $id_jenisinstruksi = 457;
                        break;
                    case 'RESEPTUR':
                        $isEditReseptur = true;
                        $id_jenisinstruksi = 458;
                        break;
                    case 'PENUNJANG':
                        $id_jenisinstruksi = 459;
                        break;
                }
            }

            $userIdentity = Yii::$app->session->get('user_identity');
            $kelompokpegawai_id = $userIdentity['kelompokpegawai_id'];
            $classKelompokpegawai = 'hidden';
            if (isset($userIdentity['kelompokpegawai_id']) && $userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN) {
                $classKelompokpegawai = '';
            }

            // Deklarasi model
            $model = new CpptForm;
            $modelInstruksi = new InstruksiForm;
            $modelInstruksiTindakan = new InstruksiTindakanForm;
            $modelInstruksiBmhp = new InstruksiTindakanBmhpForm;
            $modelTindakanPelayanan = new TindakanPelayananForm;
            $modelTindakanKomponen = new TindakankomponenForm;
            $modelBmhp = new ObatAlkesPasienForm;
            $modelReseptur = new ResepturForm;
            $modelResepturDetailRacikan = new ResepturDetailForm;
            $modelResepturDetailNonRacikan = new ResepturNrDetailForm;
            $modelResepturDetailRacikan->scenario = ResepturDetailForm::SCENARIO_SESSION;
            $modelResepturDetailNonRacikan->scenario = ResepturNrDetailForm::SCENARIO_SESSION;
            $modelVerbalOrder = new VerbalOrderRanapForm;
            $modelPenunjang = new InstruksiPenunjangForm;

            $modelInstruksi->cppt_id = $cppt_id;
            $modelInstruksiTindakan->instruksi_id = $instruksi_id;
            $modelPenunjang->tgl_kirimpasien = date('d F Y');

            if (isset($userIdentity['kelompokpegawai_id'])) {
                if ($userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS) {
                    $modelInstruksiTindakan->dokterdpjp_id = $userIdentity['id_pegawai'];
                } else if ($userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN) {
                    $modelInstruksiTindakan->dokterdpjp_id = $this->_data_pasien['dokter_admisi_id'];
                    $modelInstruksiTindakan->perawat1_id = $userIdentity['id_pegawai'];
                    $modelInstruksiBmhp->perawat1_id = $userIdentity['id_pegawai'];
                }
            }

            $listRuangan[$this->_data_pasien['ruangan_id'] . '@#' . @$this->_data_pasien['kamarruangan_id'] . '@#' . @$this->_data_pasien['kamartempattidur_id'] . '@#' . $this->_data_pasien['kamarruangan_nokamar'] . ' | ' . $this->_data_pasien['no_tempattidur']] = $this->_data_pasien['ruangan_nama'] . ' | ' . $this->_data_pasien['kamarruangan_nokamar'] . ' | ' . $this->_data_pasien['no_tempattidur'];

            // Get list data
            $listData = $this->getListData($this->_pasien_id, ($userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN ? $this->_data_pasien['dokter_admisi_id'] : $this->_pegawai_id), $this->_id_ruangan, $this->_data_pasien['pendaftaran_id'], $this->_pasienadmisi_id);

            $tempListRuangan = ArrayHelper::map($listData['listRuangan'], 'ruangan_id', 'ruangan_nama');
            $listDiagnosa = ArrayHelper::map($listData['listDiagnosa'], 'diagnosa_id', 'diagnosa_nama');
            $listPemberiInstruksi = ArrayHelper::map($listData['listPemberiInstruksi'], 'pegawai_id', 'nama_pegawai');
            $listDataSigna = ArrayHelper::map($listData['listDataSigna'], 'signa_id', 'signa_nama');
            $listDataApotek = ArrayHelper::map($listData['listDataApotek'], 'ruangan_id', 'ruangan_nama');
            $listDataTerapi = ArrayHelper::map($listData['listDataTerapi'], 'lookup_id', 'lookup_name');
            $listInstalasiPenunjang = ArrayHelper::map($listData['listInstalasiPenunjang'], 'instalasi_id', 'instalasi_nama');
            $pegawai = $listData['pegawai'];
            $listJenisPemakaian = ArrayHelper::map($listData['listJenisPemakaian'], 'lookup_id', 'lookup_name');

            // Set default value ke model
            $model->pendaftaran_id = $this->_data_pasien['pendaftaran_id'];
            $model->pasienadmisi_id = $this->_data_pasien['pasienadmisi_id'];
            $model->pasien_id = $this->_data_pasien['pasien_id'];
            $model->ruangan_id = $this->_data_pasien['ruangan_id'] . '@#' . @$this->_data_pasien['kamarruangan_id'] . '@#' . @$this->_data_pasien['kamartempattidur_id'] . '@#' . $this->_data_pasien['kamarruangan_nokamar'] . ' | ' . $this->_data_pasien['no_tempattidur'];
            $model->pegawai_id = $pegawai['pegawai_id'];
            $model->tgl_cppt = date('Y-m-d H:i:s');

            // Set model verbal order
            $modelVerbalOrder->pendaftaran_id = $this->_data_pasien['pendaftaran_id'];
            $modelVerbalOrder->pasienadmisi_id = $this->_data_pasien['pasienadmisi_id'];
            $modelVerbalOrder->pasien_id = $this->_data_pasien['pasien_id'];
            $modelVerbalOrder->ruangan_id = $this->_data_pasien['ruangan_id'];
            $modelVerbalOrder->pegawai_id = $pegawai['pegawai_id'];
            $modelVerbalOrder->tgl_cppt = date('Y-m-d H:i:s');

            // Set model reseptur

            $modelReseptur->pasienadmisi_id = $this->_data_pasien['pasienadmisi_id'];
            $modelReseptur->berat_badan = $this->_data_pasien['berat_badan'];
            $modelReseptur->tinggi_badan = $this->_data_pasien['tinggi_badan'];
            $modelReseptur->luas_tubuh = $this->_data_pasien['luas_permukaantubuh'];
            $modelReseptur->pegawai_id = $this->_data_pasien['dokter_admisi_id'];
            $modelReseptur->tglreseptur = date('d F, Y', strtotime("NOW"));

            // Set default value ke model instruksi
            $modelInstruksi->tgl_instruksi = date('Y-m-d H:i:s', strtotime('NOW'));

            // Assign temp list ruangan ke list ruangan
            // Cek temp list ruangan
            if (!empty($listData['listRuangan'])) {
                // Looping
                foreach ($listData['listRuangan'] as $value) {
                    // Set temp list ruangan
                    $listRuangan[$value['ruangan_id'] . '@#' . $value['kamarruangan_id'] . '@#' . $value['kamartempattidur_id'] . '@#' . $value['kamarruangan_nokamar'] . ' | ' . $value['no_tempattidur']] = $value['ruangan_nama'] . ' | ' . $value['kamarruangan_nokamar'] . ' | ' . $value['no_tempattidur'];
                }
            }

            //format key cache " tindakanruangan-{ruangan_id}-{kelaspelayanan_id}-{penjamin_id} "
            // $tindakanruangan = Yii::$app->cache->get("tindakanruangan-".$this->_id_ruangan."-".$this->_kelaspelayanan_id."-".$this->_data_pasien['penjamin_id']);
            // $paketruangan = Yii::$app->cache->get("paketruangan-".$this->_id_ruangan."-".$this->_kelaspelayanan_id."-".$this->_data_pasien['penjamin_id']);
            // if($tindakanruangan == false || $paketruangan == false){

            // }
            // Add to session
            $session = Yii::$app->session;
            // Check session tindakan
            if (!isset($session['tindakan'])) {
                // Set session
                $session->set('tindakan', $this->_list_data['data_tindakanruangan']);
            }

            // Check session paket
            if (!isset($session['paket'])) {
                // Set session
                $session->set('paket', $this->_list_data['data_paket']);
            }

            if (isset($session['pemeriksaan_reseptur'])) {
                $temp_session = $session['pemeriksaan_reseptur'];
                if (isset($temp_session[$this->_pendaftaran_id . $cppt_id])) {
                    unset($temp_session[$this->_pendaftaran_id . $cppt_id]);
                }
                $session->set('pemeriksaan_reseptur', $temp_session);
            }
            // $request = $this->_restRanap->get('cppt/bundle-data-transaksi',[
            //  'query' => [
            //      'pendaftaran_id' => $pendaftaran_id,
            //      'pasienadmisi_id' => $this->_pasienadmisi_id,
            //      'cppt_id' => $cppt_id,
            //      'instruksi_id' => $instruksi_id
            //  ]
            // ]);
            $listJenisInstruksi = [];

            // Cek jenis instruksi
            if (!empty($this->_list_data['data_jenisinstruksi'])) {
                // Loop
                $listJenisInstruksi = $this->_list_data['data_jenisinstruksi'];
            } else {
                $listJenisInstruksi = $listData['listJenisInstruksi'];
            }

            // Get request
            $requestCpptAsesmenMedis = $this->_restRanap->get('cppt/get-cppt-asesmen-medis?pendaftaran_id=' . $pendaftaran_id . '&cppt_id=' . $cppt_id);
            $responseCpptAsesmenMedis = json_decode($requestCpptAsesmenMedis->getBody(), true);
            $responseCpptAsesmenMedis = $responseCpptAsesmenMedis['response'];
            // dump($responseCpptAsesmenMedis);die;
            $terapiobat_init = [];
            if (isset($responseCpptAsesmenMedis['reseptur'][0]) && count($responseCpptAsesmenMedis['reseptur'][0]) > 0) {
                $terapiobat_init['berat_badan'] = $responseCpptAsesmenMedis['reseptur'][0]['berat_badan'];
                $terapiobat_init['tinggi_badan'] = $responseCpptAsesmenMedis['reseptur'][0]['tinggi_badan'];
                $terapiobat_init['luas_tubuh'] = $responseCpptAsesmenMedis['reseptur'][0]['luas_tubuh'];
                if ($responseCpptAsesmenMedis['reseptur'][0]['is_hamil'] == true) {
                    $terapiobat_init['is_hamil'] = 1;
                } else {
                    $terapiobat_init['is_hamil'] = 0;
                }
            } else if (isset($responseCpptAsesmenMedis['asesmenMedis'])) {
                $terapiobat_init['berat_badan'] = $responseCpptAsesmenMedis['asesmenMedis']['berat_badan'];
                $terapiobat_init['tinggi_badan'] = $responseCpptAsesmenMedis['asesmenMedis']['tinggi_badan'];
                $terapiobat_init['luas_tubuh'] = $responseCpptAsesmenMedis['asesmenMedis']['luas_permukaantubuh'];

                if ($responseCpptAsesmenMedis['asesmenMedis']['is_hamil'] == true) {
                    $terapiobat_init['is_hamil'] = 1;
                } else {
                    $terapiobat_init['is_hamil'] = 0;
                }
            }

            // Cek response sppt
            if (isset($responseCpptAsesmenMedis['reseptur'][0]) && count($responseCpptAsesmenMedis['reseptur'][0]) > 0) {
                // if (isset($responseCpptAsesmenMedis['reseptur'][0]['diagnosa_id'])) {
                //     $terapiobat_init['diagnosa_id'] = $responseCpptAsesmenMedis['reseptur'][0]['diagnosa_id'];
                //     $terapiobat_init['diagnosa_nama'] = $responseCpptAsesmenMedis['reseptur'][0]['diagnosa_nama'];
                // }
                // else if (isset($responseCpptAsesmenMedis['reseptur'][0]['diagnosa_text'])) {
                //     $terapiobat_init['diagnosa_id'] = '';
                //     $terapiobat_init['diagnosa_nama'] = $responseCpptAsesmenMedis['reseptur'][0]['diagnosa_text'];
                // }
                // else{
                $arr_diag_utama = isset($responseCpptAsesmenMedis['cppt'][0]['a_diag_utama']) ? $responseCpptAsesmenMedis['cppt'][0]['a_diag_utama'] : '';
                if (isset($arr_diag_utama['id'])) {
                    $terapiobat_init['diagnosa_id'] = $arr_diag_utama['id'];
                } else {
                    $terapiobat_init['diagnosa_id'] = '';
                }
                $terapiobat_init['diagnosa_nama'] = isset($arr_diag_utama['text']) ? $arr_diag_utama['text'] : $responseCpptAsesmenMedis['reseptur'][0]['diagnosa_text'];
                // }

                $terapiobat_init['nama_dokter_cppt'] = $responseCpptAsesmenMedis['reseptur'][0]['nama_pegawai'];
                $terapiobat_init['pegawai_id'] = $responseCpptAsesmenMedis['reseptur'][0]['pegawai_id'];
            } else if (isset($responseCpptAsesmenMedis['cppt'][0]) && count($responseCpptAsesmenMedis['cppt'][0]) > 0) {
                if (isset($responseCpptAsesmenMedis['cppt'][0]['a_diag_utama'])) {

                    // $arr_diag_utama = json_decode($responseCpptAsesmenMedis['cppt'][0]['a_diag_utama'],TRUE);
                    $arr_diag_utama = $responseCpptAsesmenMedis['cppt'][0]['a_diag_utama'];

                    if (isset($arr_diag_utama['id'])) {
                        $terapiobat_init['diagnosa_id'] = $arr_diag_utama['id'];
                    } else {
                        $terapiobat_init['diagnosa_id'] = '';
                    }
                    $terapiobat_init['diagnosa_nama'] = $arr_diag_utama['text'];
                }
                $terapiobat_init['nama_dokter_cppt'] = $responseCpptAsesmenMedis['cppt'][0]['nama_pegawai'];
                $terapiobat_init['pegawai_id'] = $responseCpptAsesmenMedis['cppt'][0]['pegawai_id'];
            }

            // else {
            //  $terapiobat_init['diagnosa_id'] = $responseCpptAsesmenMedis['asesmenMedis']['diagnosa_id'];
            // }

            $modelReseptur->berat_badan = isset($terapiobat_init['berat_badan']) ? str_replace(".", ",", $terapiobat_init['berat_badan']) : null;
            $modelReseptur->tinggi_badan = isset($terapiobat_init['tinggi_badan']) ? str_replace(".", ",", $terapiobat_init['tinggi_badan']) : null;
            $modelReseptur->luas_tubuh = isset($terapiobat_init['luas_tubuh']) ? str_replace(".", ",", $terapiobat_init['luas_tubuh']) : null;
            $modelReseptur->diagnosa_id = isset($terapiobat_init['diagnosa_id']) ? $terapiobat_init['diagnosa_id'] : null;
            $modelReseptur->pegawai_id = isset($terapiobat_init['pegawai_id']) ? $terapiobat_init['pegawai_id'] : null;
            $modelReseptur->is_hamil = isset($terapiobat_init['is_hamil']) ? $terapiobat_init['is_hamil'] : null;
            $modelReseptur->diagnosa_id = isset($terapiobat_init['diagnosa_id']) ? $terapiobat_init['diagnosa_id'] : null;
            $diagnosa_nama = isset($terapiobat_init['diagnosa_nama']) ? $terapiobat_init['diagnosa_nama'] : null;

            $modelResepturDetailRacikan->cppt_id = $cppt_id;
            $modelResepturDetailNonRacikan->cppt_id = $cppt_id;

            $dataTerapiTindakan = [];
            $dataTerapiPenunjang = [];
            $dataTerapiReseptur = ($isEditReseptur == true) ? $this->getTerapiReseptur($this->_data_pasien['pendaftaran_id'], $jns_instruksi, $instruksi_id, $cppt_id) : [];
            // dump($dataTerapiReseptur);die;
            $data_tindakanbmhp = [];
            $data_penunjang = [];
            if ($isUbah == '1') {

                if ($jns_instruksi == 'TINDAKANBMHP') {
                    $dataTerapiTindakan = $this->cpptDataTerapiTindakan($this->_data_pasien['pendaftaran_id'], $jns_instruksi, $instruksi_id, $cppt_id);
                    if (isset($dataTerapiTindakan['riwayat_instruksi'])) {
                        $data_tindakanbmhp = $dataTerapiTindakan['riwayat_instruksi'];
                    }
                    if (isset($dataTerapiTindakan['data_instruksi']['instruksi_id'])) {
                        $modelInstruksi->instruksi_id = $dataTerapiTindakan['data_instruksi']['instruksi_id'];
                    }
                    if (isset($dataTerapiTindakan['data_instruksi']['catatan_instruksi'])) {
                        $modelInstruksi->catatan_instruksi = $dataTerapiTindakan['data_instruksi']['catatan_instruksi'];
                    }
                }

                if ($jns_instruksi == 'PENUNJANG') {
                    $dataTerapiPenunjang = $this->cpptDataTerapiPenunjang($this->_data_pasien['pendaftaran_id'], $jns_instruksi, $instruksi_id, $cppt_id);
                    if (isset($dataTerapiPenunjang['riwayat_instruksi'])) {
                        $data_penunjang = $dataTerapiPenunjang['riwayat_instruksi'];
                    }
                    if (isset($dataTerapiPenunjang['data_instruksi'])) {
                        $modelInstruksi->instruksi_id = $dataTerapiPenunjang['data_instruksi']['instruksi_id'];
                        $modelInstruksi->catatan_instruksi = $dataTerapiPenunjang['data_instruksi']['catatan_instruksi'];
                        $modelPenunjang->tgl_kirimpasien = date('d F Y', strtotime($dataTerapiPenunjang['data_instruksi']['tgl_instruksi']));
                    }
                }
            }

            $this->resetResepturSession(DocoHelpers::encrypt($this->_data_pasien['pendaftaran_id']), $cppt_id);
            $session->remove('obat_dihapus');

            if ($isEditReseptur == true) {
                $session = Yii::$app->session;
                $session_reseptur = [];
                $session_key = 1;
                $count_data_insert = 0;

                $pendaftaran_id_encrypted = DocoHelpers::encrypt($this->_data_pasien['pendaftaran_id']);
                $pendaftaran_id = $this->_data_pasien['pendaftaran_id'];
                $data_instruksi = $dataTerapiReseptur['data_instruksi'];
                $data_reseptur = $dataTerapiReseptur['data_reseptur'];
                $data_resepturdetail = $dataTerapiReseptur['data_resepturdetail'];
                $data_obatalkes = $dataTerapiReseptur['data_obatalkes'];

                if (!empty($data_instruksi)) {
                    $modelInstruksi->instruksi_id = $data_instruksi['instruksi_id'];
                    $modelInstruksi->catatan_instruksi = $data_instruksi['catatan_instruksi'];
                }

                if (!empty($data_obatalkes)) {
                    $initObatAlkes = $data_obatalkes;
                }

                if (!empty($data_reseptur)) {
                    $modelReseptur->reseptur_id = $data_reseptur['reseptur_id'];
                    $modelReseptur->pasienadmisi_id = $data_reseptur['pasienadmisi_id'];
                    $modelReseptur->ruangan_id = $data_reseptur['ruangan_id'];
                    $modelReseptur->pasien_id = $data_reseptur['pasien_id'];
                    $modelReseptur->pegawai_id = $data_reseptur['pegawai_id'];
                    $modelReseptur->pendaftaran_id = $data_reseptur['pendaftaran_id'];
                    $modelReseptur->penjualanresep_id = $data_reseptur['penjualanresep_id'];
                    $modelReseptur->tglreseptur = date('d F, Y', strtotime($data_reseptur['tglreseptur']));
                    $modelReseptur->noresep = $data_reseptur['noresep'];
                    $modelReseptur->ruanganreseptur_id = $data_reseptur['ruanganreseptur_id'];
                    $modelReseptur->status_reseptur = $data_reseptur['status_reseptur_id'];
                    $modelReseptur->antrian_id = $data_reseptur['antrian_id'];
                    $modelReseptur->berat_badan = str_replace(".", ',', $data_reseptur['berat_badan']);
                    $modelReseptur->tinggi_badan = str_replace(".", ',', $data_reseptur['tinggi_badan']);
                    $modelReseptur->luas_tubuh = str_replace(".", ',', $data_reseptur['luas_tubuh']);
                    $modelReseptur->diagnosa_id = $data_reseptur['diagnosa_id'];
                    $modelReseptur->is_hamil = $data_reseptur['is_hamil'] == true ? 1 : 0;
                    $modelReseptur->instruksi_id = $data_reseptur['instruksi_id'];
                }

                // unutk menampilkan data reseptur obat yang sudah dipesan saat edit reseptur dari cppt
                if (!empty($data_resepturdetail)) {
                    foreach ($data_resepturdetail as $value) {
                        $session_reseptur[$pendaftaran_id_encrypted . $cppt_id][] = [
                            'session_key' => $session_key,
                            'resepturdetail_id' => $value['resepturdetail_id'],
                            'jenis_racikan' => !empty($value['rke']) ? ResepturDetailForm::VC_RC : ResepturDetailForm::VC_NRC,
                            'nama_racikan' => $value['racikan_nama'],
                            'rke' => $value['rke'],
                            'signa_reseptur' => $value['signa_id'],
                            'obatalkes_id' => $value['obatalkes_id'],
                            'qty_reseptur' => $value['qty_konversi'],
                            'satuankecil_id' => $value['satuankecil_id'],
                            'satuankecil_nama' => $value['satuan_input'],
                            'satuan_penyimpanan' => $value['satuan_kecil'],
                            'nilai_konversi' => $value['nilai_konversi'],
                            'jml_konversi' => $value['qty_reseptur'],
                            'etiket' => $value['etiket'],
                            'satuanbesar_id' => $value['satuaninput_id'],
                            'satuankecil' => $value['satuan_konversi'],
                            'qty_konversi' => $value['qty_konversi'],
                            'hargasatuan_reseptur' => number_format($value['hargajual_satuan'], 2, ',', '.'),
                        ];

                        $modelReseptur->iter = $value['iter'];
                        $session_key++;
                    }
                }

                $session->set('pemeriksaan_reseptur', $session_reseptur);
            }

            //** untuk terapi lgs tanpa cppt */
            if ($cppt_id == 0) {
                $terapiobat_init['nama_dokter_cppt'] = Yii::$app->session->get('user_identity')['nama_pegawai'];
                $modelReseptur->pegawai_id = Yii::$app->session->get('user_identity')['id_pegawai'];
            }
            $active_workspace = Yii::$app->session->get('active_workspace');
            $ruangan_nama = $active_workspace['ruangan_name'];

            /** Cek Pasien titipan  dan Pindah Kamar*/
            if ($responseCpptAsesmenMedis['isPindahKamar'] && !empty($responseCpptAsesmenMedis['dataPindahKamar'])) {
                if ($responseCpptAsesmenMedis['dataPindahKamar'][0]['is_pasientitipan'] == true) {
                    if ($responseCpptAsesmenMedis['dataPindahKamar'][0]['is_stoptitipan'] != true) {
                        $this->_data_pasien['kelaspelayanan_id'] = !empty($responseCpptAsesmenMedis['dataPindahKamar'][0]['kelas_ditagihkan_id']) ? $responseCpptAsesmenMedis['dataPindahKamar'][0]['kelas_ditagihkan_id'] : $this->_data_pasien['kelaspelayanan_id'];
                    } else {
                        $this->_data_pasien['kelaspelayanan_id'] = $responseCpptAsesmenMedis['dataPindahKamar'][0]['kelaspelayanan_id'];
                    }
                } else {
                    $this->_data_pasien['kelaspelayanan_id'] = $responseCpptAsesmenMedis['dataPindahKamar'][0]['kelaspelayanan_id'];
                }
            } else if ($responseCpptAsesmenMedis['asesmenMedis']['is_pasientitipan'] == true) {
                if ($responseCpptAsesmenMedis['asesmenMedis']['is_stoppasientitipan'] != true) {
                    $this->_data_pasien['kelaspelayanan_id'] = isset($responseCpptAsesmenMedis['asesmenMedis']['kelas_ditagihkan_id']) && !empty($responseCpptAsesmenMedis['asesmenMedis']['kelas_ditagihkan_id']) ? $responseCpptAsesmenMedis['asesmenMedis']['kelas_ditagihkan_id'] : $this->_data_pasien['kelaspelayanan_id'];
                } else {
                    $this->_data_pasien['kelaspelayanan_id'] = $responseCpptAsesmenMedis['asesmenMedis']['kelaspelayanan_id'];
                }
            }
            // this API will return default depo
            $defaultDepo = $this->guzzleExec($this->_restRanap, [
                'url' => 'allow/default-depo',
            ]);

            return $this->renderAjax('cppt/transaksi_terapi', [
                'sourceTab' => $sourceTab,
                'isUbah' => $isUbah,
                'cppt_id' => $cppt_id,
                'instruksi_id' => $instruksi_id,
                'id_ruangan' => $this->_id_ruangan,
                'id_instalasi' => $this->_instalasi_id,
                'pendaftaran_id' => $this->_pendaftaran_id,
                'model' => $model,
                'modelInstruksi' => $modelInstruksi,
                'modelInstruksiTindakan' => $modelInstruksiTindakan,
                'modelInstruksiBmhp' => $modelInstruksiBmhp,
                'modelBmhp' => $modelBmhp,
                'modelTindakanKomponen' => $modelTindakanKomponen,
                'modelTindakanPelayanan' => $modelTindakanPelayanan,
                'modelReseptur' => $modelReseptur,
                'modelResepturDetailRacikan' => $modelResepturDetailRacikan,
                'modelResepturDetailNonRacikan' => $modelResepturDetailNonRacikan,
                'listRuangan' => $listRuangan,
                'listDiagnosa' => $listDiagnosa,
                'listPemberiInstruksi' => $listPemberiInstruksi,
                'pegawai' => $pegawai,
                'listDataSigna' => $listDataSigna,
                'listDataApotek' => $listDataApotek,
                'listDataTerapi' => $listDataTerapi,
                'listPemberiInstruksi' => $listPemberiInstruksi,
                'data_pasien' => $this->_data_pasien,
                'modelBmhp' => $modelBmhp,
                'modelTindakanKomponen' => $modelTindakanKomponen,
                'modelTindakanPelayanan' => $modelTindakanPelayanan,
                'modelVerbalOrder' => $modelVerbalOrder,
                'data_tindakanruangan' => $this->_list_data['data_tindakanruangan'],
                'data_paket' => $this->_list_data['data_paket'],
                'data_dokter' => $this->_list_data['data_dokter'],
                'data_perawat' => $this->_list_data['data_perawat'],
                'data_obatalkes' => $this->_list_data['data_obatalkes'],
                'data_satuantindakan' => $this->_list_data['data_satuantindakan'],
                'konfig' => $this->_list_data['konfig_farmasi'],
                'data_jenisinstruksi' => $listJenisInstruksi,
                'count_riwayat' => $this->_list_data['count_riwayat'],
                'data_tindakanbmhp' => $data_tindakanbmhp,
                'data_penunjang' => $data_penunjang,
                'classKelompokpegawai' => $classKelompokpegawai,
                'isEditTindakan' => $isEditTindakan,
                'isEditBmhp' => $isEditBmhp,
                'isEditReseptur' => $isEditReseptur,
                'id_jenisinstruksi' => $id_jenisinstruksi,
                'terapiobat_init' => $terapiobat_init,
                'encryptedPendaftaranId' => $encryptedPendaftaranId,
                'jns_instruksi' => $jns_instruksi,
                'modelPenunjang' => $modelPenunjang,
                'listInstalasiPenunjang' => $listInstalasiPenunjang,
                'initObatAlkes' => $initObatAlkes,
                'diagnosa_nama' => $diagnosa_nama,
                'listJenisPemakaian' => $listJenisPemakaian,
                'ruangan_nama' => $ruangan_nama,
                'defaultDepo' => isset($defaultDepo['depoId']) ? $defaultDepo['depoId'] : null,
                'kelompokpegawai_id' => $kelompokpegawai_id
            ]);
        } catch (RequestException $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (\Exception $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    public function cpptDataTerapiTindakan($id, $jns_instruksi, $instruksi_id, $cppt_id)
    {
        try {
            $response = $this->_restRanap->get('cppt/get-list-data-instruksi', [
                'query' => [
                    'id' => $id,
                    'instruksi_id' => $instruksi_id,
                    'cppt_id' => $cppt_id
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            $riwayat_instruksi = $body['response']['riwayat_instruksi'];
            $data_instruksi = $body['response']['data_instruksi'];
            return [
                'riwayat_instruksi' => $riwayat_instruksi,
                'data_instruksi' => $data_instruksi
            ];
        } catch (RequestException $e) {
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    // get data terapi penunjang
    public function cpptDataTerapiPenunjang($id, $jns_instruksi, $instruksi_id, $cppt_id)
    {
        try {
            $response = $this->_restRanap->get('cppt/get-list-data-penunjang', [
                'query' => [
                    'id' => $id,
                    'instruksi_id' => $instruksi_id,
                    'cppt_id' => $cppt_id
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            $riwayat_instruksi = $body['response']['riwayat_instruksi'];
            $data_instruksi = $body['response']['data_instruksi'];
            return [
                'riwayat_instruksi' => $riwayat_instruksi,
                'data_instruksi' => $data_instruksi
            ];
        } catch (RequestException $e) {
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /* Fungsi untuk mendapatkan terapi reseptur */
    private function getTerapiReseptur($id, $jns_instruksi, $instruksi_id, $cppt_id)
    {
        try {
            $request = $this->_restRanap->get('cppt/get-terapi-reseptur', [
                'query' => [
                    'id' => $id,
                    'instruksi_id' => $instruksi_id,
                    'cppt_id' => $cppt_id
                ]
            ]);
            $response = json_decode($request->getBody(), true);
            $response = $response['response'];

            return [
                'data_instruksi' => $response['data_instruksi'],
                'data_reseptur' => $response['data_reseptur'],
                'data_resepturdetail' => $response['data_resepturdetail'],
                'data_obatalkes' => $response['data_obatalkes'],
            ];
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, Yii::t("fe", "Terdapat kesalahan"));
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, Yii::t("fe", "Terdapat kesalahan"));
        }
    }


    /*
    author: Rizal Faidin CLONE FROM PENDAFTARAN
    usage: modal tambah pemeriksaan penunjang
    date: 26-07-2018
    */
    public function actionModalPemeriksaanPenunjang()
    {
        $result = [];
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            $listPemeriksaanPure = [];
            if ($get['instalasi_id'] == DocoConstants::INSTALASI_ID_LAB || $get['instalasi_id'] == DocoConstants::INSTALASI_ID_RAD || $get['instalasi_id'] == DocoConstants::INSTALASI_ID_BEDAH || $get['instalasi_id'] == DocoConstants::INSTALASI_FISIOTERAPI) {
                $params = [
                    'ruangan_id'        => isset($get['ruangan_id']) ? $get['ruangan_id'] : '',
                    'penjamin_id'       => isset($get['penjamin_id']) ? $get['penjamin_id'] : '',
                    'kelaspelayanan_id' => isset($get['kelaspelayanan_id']) ? $get['kelaspelayanan_id'] : '',
                    'instalasi_id'      => isset($get['instalasi_id']) ? $get['instalasi_id'] : '',
                ];
                $url = 'allow/get-tarif-tindakan-ri';
                $response = $this->_restRanap->get($url, ['query' => $params]);
                $body = json_decode($response->getBody(), true);
                $body = isset($body['response']) ? $body['response'] : [];
                $groupingtindakan_penunjang = isset($body['groupingtindakan_penunjang']) ? $body['groupingtindakan_penunjang'] : false;
                foreach ($body['data'] as $key => $value) {
                    if ($get['instalasi_id'] == DocoConstants::INSTALASI_ID_BEDAH) {
                        if ($groupingtindakan_penunjang) {
                            $result[$value['daftartindakan_nama']][$value['nama_kelompok']][] = $value;
                            continue;
                        }
                    }
                    $result[$value['jenispemeriksaanlab_nama']][$value['nama_kelompok']][] = $value;
                    $listPemeriksaanPure[] = $value;
                }
            }

            $title = Yii::t('fe', 'Tambah pemeriksaan');
            $instalasiId = ArrayHelper::getValue($get, 'instalasi_id');
            if ($instalasiId == DocoConstants::INSTALASI_FISIOTERAPI) {
                return $this->renderAjax('/modal-order-penunjang/fisioterapi/__modal_order_penunjang.php', get_defined_vars());
            }
            return $this->renderAjax('//cppt/penunjang/__modal_order_penunjang', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionModalPemeriksaanPenunjangFisio()
    {
        $result = [];
        $request = Yii::$app->request;
        $get = $request->get();
        $model = new InstruksiPenunjangForm;
        try {
            $listPemeriksaanPure = [];
            $params = [
                'ruangan_id' => ArrayHelper::getValue($get, 'ruangan_id'),
                'penjamin_id' => ArrayHelper::getValue($get, 'penjamin_id'),
                'kelaspelayanan_id' => ArrayHelper::getValue($get, 'kelaspelayanan_id'),
                'instalasi_id' => ArrayHelper::getValue($get, 'instalasi_id'),
                'spesialis_id' => ArrayHelper::getValue($get, 'spesialis_id'),
            ];
            $url = 'allow/get-tarif-tindakan-ri';
            $response = $this->_restRanap->get($url, ['query' => $params]);
            $body = json_decode($response->getBody(), true);
            $body = isset($body['response']) ? $body['response'] : [];
            foreach ($body['data'] as $key => $value) {
                $result[$value['jenispemeriksaanlab_nama']][] = $value;
                $listPemeriksaanPure[] = $value;
            }
            $days = [
                'Mon' => 'Senin',
                'Tue' => 'Selasa',
                'Wed' => 'Rabu',
                'Thu' => 'Kamis',
                'Fri' => 'Jumat',
                'Sat' => 'Sabtu',
                'Sun' => 'Minggu',
            ];
            $title = 'Tambah pemeriksaan';
            $model->has_jadwal = 0;
            $userIdentity = Yii::$app->session->get('user_identity');
            $pegawaiId = ArrayHelper::getValue($userIdentity, 'loginpemakai_id');
            $instalasiId = ArrayHelper::getValue($get, 'instalasi_id');
            return $this->renderAjax('/modal-order-penunjang/fisioterapi/__modal_order_penunjang.php', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionSimpanTerapiPenunjang()
    {
        try {
            $request = Yii::$app->request;
            $orders = $request->post('periksalab');
            $orders = json_decode($orders, true);
            $ruanganasal_id = $request->post('ruanganperiksa_id') != null ? PelayananHelpers::decryptId($request->post('ruanganperiksa_id')) : null;

            $model = new InstruksiPenunjangForm;
            $model->attributes = $request->post('InstruksiPenunjangForm');
            $model->tgl_kirimpasien = str_replace('/', '-', $model->tgl_kirimpasien);

            $jadwal_operasi = [];
            if ($model->instalasi_id == DocoConstants::INSTALASI_ID_BEDAH) {
                $jadwal_operasi = json_decode($request->post('jadwal_operasi'), true);
            }

            if ($model->validate()) {
                $temps = [];
                foreach ($orders as $key => $order) {
                    $temps[$key]['is_paketfisio'] = ArrayHelper::getValue($order, 'is_paketfisio');
                    $temps[$key]['parentdaftartindakan_id'] = ArrayHelper::getValue($order, 'parentdaftartindakan_id');
                    $temps[$key]['tariftindakan_id'] = $order['tariftindakan_id'];
                    $temps[$key]['daftartindakan_id'] = $order['daftartindakan_id'];
                    $temps[$key]['is_cyto'] = $order['is_cyto'] == 'true' ? true : false;
                    $temps[$key]['is_paket'] = isset($order['tipepaket_id']) ? true : false;
                    $temps[$key]['golongan_id'] = isset($order['kelompokpemeriksaanlab_id'])
                        ? $order['kelompokpemeriksaanlab_id']
                        : null;
                    $temps[$key]['kegiatan_id'] = isset($order['jenispemeriksaanlab_id'])
                        ? $order['jenispemeriksaanlab_id']
                        : null;
                    $temps[$key]['catatan'] = isset($order['catatan'])
                        ? $order['catatan']
                        : null;
                }

                $post = [
                    'pendaftaran_id' => $model->pendaftaran_id,
                    'pasienadmisi_id' => $model->pasienadmisi_id,
                    'instalasi_id' => $model->instalasi_id,
                    'ruangan_id' => $model->ruangan_id,
                    'pegawai_id' => $model->pegawai_id,
                    'cppt_id' => $this->helper->decrypt($model->cppt_id),
                    'catatan' => $model->catatan,
                    'catatan_dokterpengirim' => $model->catatan_dokterpengirim,
                    'tgl_kirimpasien' => $model->instalasi_id == DocoConstants::INSTALASI_ID_BEDAH ? date('Y-m-d', strtotime($jadwal_operasi['tgl_kirimpasien'])) . ' ' . $jadwal_operasi['jam_mulai'] : date('Y-m-d', strtotime($model->tgl_kirimpasien)) . ' ' . date('H:i:s'),
                    'list_order' => $temps,
                    'jadwal_operasi' => $jadwal_operasi,
                    'pasien_id' => $this->_pasien_id,
                    'ruangan' => $model->ruangan,
                    'is_puasa' => $model->is_puasa,
                    'is_dokter' => isset($userIdentity['kelompokpegawai_id']) && $userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS ? true : false,
                    'pemakaian_implant' => $model->pemakaian_implant,
                    'sewa_vendor' => $model->sewa_vendor,
                    'sewa_alat_rs' => $model->sewa_alat_rs,
                    'jenis_operasi_cito' => $model->jenis_operasi_cito,
                    'jenis_operasi_elektif' => $model->jenis_operasi_elektif,
                    'jenis_operasi_odc' => $model->jenis_operasi_odc,
                    'is_rujukan' => $model->is_rujukan,
                    'diagnosis' => $model->diagnosis,
                    'frekuensi_terapi' => $model->frekuensi_terapi,
                    'schedule_details' => $model->schedule_details,
                    'diagnosa_utama' => isset($model->diagnosa_utama) ? json_decode($model->diagnosa_utama, true) : [],
                    'diagnosa_penyerta' => isset($model->diagnosa_penyerta) ? json_decode($model->diagnosa_penyerta, true) : [],
                    'ruangan_asal' => $ruanganasal_id != null ? $ruanganasal_id : ArrayHelper::getValue(Yii::$app->session->get('active_workspace'), 'ruangan_id')
                ];
                $response = $this->_restRanap->post('cppt/create-terapi-penunjang', [
                    'form_params' => $post
                ]);
                $response = json_decode($response->getBody(), true);
                // echo '<pre>';
                // print_r($response);
                // exit();
            } else {
                $formName = substr(strrchr(get_class($model), "\\"), 1);
                $response = $model->errors;
                return DocoHelpers::response($response, 422, $formName);
            }
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    /* Cetak pdf penunjang */
    public function actionCetakPenunjang($id = null, $pasienadmisi_id = null, $instruksi_id = null)
    {
        $path = Yii::getAlias("@download") . "/terapi-penunjang.pdf";

        // try {
        $request = $this->_restRanap->get('cppt/cetak-penunjang', [
            'query' => [
                'pendaftaran_id' => $id,
                'pasienadmisi_id' => $pasienadmisi_id,
                'instruksi_id' => $instruksi_id,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::previewPdf($path);
        // } catch (RequestException $e) {
        //     throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        // } catch (\Exception $e) {
        //     throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        // }
    }

        /* Cetak pdf penunjang */
        public function actionCetakPenunjangFisio($id = NULL, $pasienadmisi_id = NULL, $pasienkirimkeunitlain_id = NULL)
        {
            $path = Yii::getAlias("@download") . "/terapi-penunjang.pdf";
    
            try {
            $request = $this->_restRanap->get('cppt/cetak-penunjang-fisio', [
                'query' => [
                    'pendaftaran_id' => $id,
                    'pasienadmisi_id' => NULL,
                    'pasienkirimkeunitlain_id' => $pasienkirimkeunitlain_id,
                ],
                'save_to' => $path,
            ]);
    
            return DocoHelpers::previewPdf($path);
            } catch (RequestException $e) {
                throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
            } catch (\Exception $e) {
                throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
            }
        }

    public function actionShowPopupPdf()
    {
        $title = 'Cetak PDF CPPT';
        $randString = DocoHelpers::generateRandomString();
        $params = Yii::$app->request;
        $pendaftaran_id = DocoHelpers::decrypt($params->get('id'));
        $ruangan_id = $params->get('ruangan_id', null);
        $pegawai_id = $params->get('pegawai_id', null);
        $kelompokpegawai_id = $params->get('kelompokpegawai_id', null);
        $tgl_cppt = $params->get('tanggal_cppt', null);
        $nama_usercetak = Yii::$app->session->get('user_identity')['nama'];
        $id_usercetak = Yii::$app->session->get('user_identity')['id_pegawai'];
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($params->get());
        $process_path = '/ranap/pemeriksaan-rawat-inap/process-sync-pdf';
        $download_pdf_path = '/ranap/pemeriksaan-rawat-inap/download-pdf';
        $filter = [
            'pendaftaran_id' => $pendaftaran_id,
            'filter_ruangan_id' => $ruangan_id,
            'filter_pegawai_id' => $pegawai_id,
            'filter_kelompokpegawai_id' => $kelompokpegawai_id,
            'filter_tgl_cppt' => $tgl_cppt,
            'pasienadmisi_id' => $this->_data_pasien['pasienadmisi_id'],
            'ruangan_id' => $this->_id_ruangan,
            'pegawai_id' => $this->_pegawai_id,
            'kelompokpegawai_id' => $this->_user_identity['kelompokpegawai_id'],
            'nama_usercetak' => $nama_usercetak,
            'id_usercetak' => $id_usercetak,
            'order' => $yiiRestfulParams['order']
        ];
        Yii::$app->session->setFlash($randString, $filter);
        return $this->renderAjax('//cppt/_modal_progress', compact('title', 'randString', 'process_path', 'download_pdf_path'));
    }

    public function actionProcessSyncPdf($randString)
    {

        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);
        $session['randString'] = $randString;
        return $this->guzzleExec($this->_restRanap, [
            'url' => "cppt/export-pdf-cppt-bgproses",
            'payload' => [
                'query' => $session
            ],
        ]);
    }

    public function actionDownloadPdf()
    {
        $request = Yii::$app->request;
        $filename = $request->get('fileName', null);
        $fileDownloads = 'Cetakan CPPT.pdf';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restRanap->get('cppt/download-file-pdf', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::previewPdf($path);
    }

    // bedah
    public function actionModalJadwalOperasi()
    {
        try {
            $title = Yii::t('fe', 'Input Jadwal Prosedur / Operasi');
            $modelJadwalOperasi = new JadwalOperasiForm;
            // Get dokter
            $request = $this->_restRanap->get('allow/get-all-dokter', ['form_params' => []]);
            $response = json_decode($request->getBody(), true);
            $list_dokter = $response ? $response['response'] : [];

            return $this->renderAjax('//cppt/penunjang/_modal_jadwal_operasi', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionGetDataJadwalOperasi()
    {
        $request = Yii::$app->request;
        try {
            $response = $this->_restRanap->get('cppt/get-data-jadwal-operasi', [
                'query' => $request->post()
            ]);

            $body = json_decode($response->getBody(), true);
            $response = $body['response'];
            $ruangan = $data_jadwal = [];
            if ($response['metadata']['status'] == 200) {
                $ruangan = isset($response['response']['data_ruangan']) ? $response['response']['data_ruangan'] : [];
                $data_jadwal = isset($response['response']['jadwal_data']) ? $response['response']['jadwal_data'] : [];
            }

            return DocoHelpers::response([
                'ruangan' => $ruangan,
                'data_jadwal' => $data_jadwal
            ]);
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'message' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            return DocoHelpers::response([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function actionViewJadwalOperasi($id)
    {
        $id_parent = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $result = [];
        $title = Yii::t('fe', 'Jadwal Operasi');
        $model = new JadwalOperasiForm;
        try {
            $response = $this->_restRanap->get('cppt/view-jadwal-operasi', [
                'query' => [
                    'id' => $id_parent
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            $data = isset($body['response']['data']) ? $body['response']['data'] : [];
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }

        return $this->render('view', get_defined_vars());
    }

    public function actionSetJadwalOperasi()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $model = new JadwalOperasiForm;
            $model->load($post);
            if ($model->validate()) {
                $session = Yii::$app->session;
                $session->set('jadwal_operasi', $post);
                $response = $model->attributes;
            } else {
                $formName = substr(strrchr(get_class($model), "\\"), 1);
                $response = $model->errors;
                return DocoHelpers::response($response, 422, $formName);
            }

            return DocoHelpers::response($response);
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    public function actionCreateInstruksiDpjp()
    {
        $params = Yii::$app->request;
        $post = $params->post('InstruksiDpjpForm');
        $data_pasien = $this->_data_pasien;
        $ruangan_id = $data_pasien['ruangan_id'];
        $ruangan_nama = $data_pasien['ruangan_nama'];

        $model = new InstruksiDpjpForm;
        $model->pendaftaran_id = $data_pasien['pendaftaran_id'];
        $model->kelaspelayanan_id = $data_pasien['kelaspelayanan_id'];
        $model->penjamin_id = $data_pasien['penjamin_id'];
        $model->ruangan_id = $data_pasien['ruangan_id'];
        $model->ruangan_nama = $data_pasien['ruangan_nama'];

        $formName = substr(strrchr(get_class($model), "\\"), 1);
        if ($model->load($post, '')) {
            $explodeRuangan_id = explode('@#', $post['ruangan_id_kamar']);
            if (count($explodeRuangan_id) > 1) {
                $post['ruangan_id_kamar']         = (int)$explodeRuangan_id[0];
                $post['kamarruangan_id']    = $explodeRuangan_id[1];
                $post['kamartempattidur_id'] = $explodeRuangan_id[2];
                $post['kamar_tempattidur']  = $explodeRuangan_id[3];
                $post['tgl_cppt']   = date('Y-m-d H:i:s');
            }
            $post['instruksi'] = nl2br($post['instruksi']);
            if ($model->validate()) {
                $request = $this->_restRanap->post('instruksi-dpjp/create', [
                    'form_params' => $post
                ]);
                $response = json_decode($request->getBody(), true);
                return json_encode($response);
            } else {
                $response = $model->errors;
                return DocoHelpers::response($response, 422, $formName);
            }
        } else {
            $cpptId = 0;
            $requestCpptAsesmenMedis = $this->_restRanap->get('cppt/get-cppt-asesmen-medis?pendaftaran_id=' . $model->pendaftaran_id . '&cppt_id=' . $cpptId);
            $responseCpptAsesmenMedis = json_decode($requestCpptAsesmenMedis->getBody(), true);
            $responseCpptAsesmenMedis = $responseCpptAsesmenMedis['response'];

            /** Cek Pasien titipan  dan Pindah Kamar*/
            if ($responseCpptAsesmenMedis['isPindahKamar'] && !empty($responseCpptAsesmenMedis['dataPindahKamar'])) {
                if ($responseCpptAsesmenMedis['dataPindahKamar'][0]['is_pasientitipan'] == true) {
                    if ($responseCpptAsesmenMedis['dataPindahKamar'][0]['is_stoptitipan'] != true) {
                        $model->kelaspelayanan_id = !empty($responseCpptAsesmenMedis['dataPindahKamar'][0]['kelas_ditagihkan_id']) ? $responseCpptAsesmenMedis['dataPindahKamar'][0]['kelas_ditagihkan_id'] : $model->kelaspelayanan_id;
                    } else {
                        $model->kelaspelayanan_id = $responseCpptAsesmenMedis['dataPindahKamar'][0]['kelaspelayanan_id'];
                    }
                } else {
                    $model->kelaspelayanan_id = $responseCpptAsesmenMedis['dataPindahKamar'][0]['kelaspelayanan_id'];
                }
            } else if ($responseCpptAsesmenMedis['asesmenMedis']['is_pasientitipan'] == true) {
                if ($responseCpptAsesmenMedis['asesmenMedis']['is_stoppasientitipan'] != true) {
                    $model->kelaspelayanan_id = isset($responseCpptAsesmenMedis['asesmenMedis']['kelas_ditagihkan_id']) && !empty($responseCpptAsesmenMedis['asesmenMedis']['kelas_ditagihkan_id']) ? $responseCpptAsesmenMedis['asesmenMedis']['kelas_ditagihkan_id'] : $model->kelaspelayanan_id;
                } else {
                    $model->kelaspelayanan_id = $responseCpptAsesmenMedis['asesmenMedis']['kelaspelayanan_id'];
                }
            }

            // $listData = $this->getListData($this->_pasien_id, $this->_pegawai_id, $this->_id_ruangan, $this->_data_pasien['pendaftaran_id']);

            $listRuangan[$data_pasien['ruangan_id'] . '@#' . @$data_pasien['kamarruangan_id'] . '@#' . @$data_pasien['kamartempattidur_id'] . '@#' . $data_pasien['kamarruangan_nokamar'] . ' | ' . $data_pasien['no_tempattidur']] = $data_pasien['ruangan_nama'] . ' | ' . $data_pasien['kamarruangan_nokamar'] . ' | ' . $data_pasien['no_tempattidur'];

            // if (!empty($listData['listRuangan'])) {
            //     // Looping
            //     foreach ($listData['listRuangan'] as $value) {
            //         // Set temp list ruangan
            //         $listRuangan[$value['ruangan_id'].'@#'.$value['kamarruangan_id'].'@#'.$value['kamartempattidur_id'].'@#'.$value['kamarruangan_nokamar'].' | '.$value['no_tempattidur']] = $value['ruangan_nama'].' | '.$value['kamarruangan_nokamar'].' | '.$value['no_tempattidur'];
            //     }
            // }

            return $this->renderAjax('cppt/form_instruksi_dpjp', get_defined_vars());
        }
    }

    /**
     * This function will send data SOAP on create / update
     *
     * @return Array/Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionEditSoap($cpptId = null)
    {
        try {
            return $this->soapCreateOrUpdate($this->helper->decrypt($cpptId));
        } catch (\Exception $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(500, Yii::t("fe", "Terdapat kesalahan"));
        } catch (RequestException $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(500, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    /**
     * This function will send data SOAP on create / update
     *
     * @return Array/Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function soapCreateOrUpdate($cpptId = null, $auto = null)
    {
        // Get params
        $params = Yii::$app->request;
        $post = $params->post('CpptForm');
        $post['subject'] = DocoHelpers::purifyText(ArrayHelper::getValue($post, 'subject'));
        $post['object'] = DocoHelpers::purifyText(ArrayHelper::getValue($post, 'object'));
        $post['a_diag_utama'] = DocoHelpers::purifyText(ArrayHelper::getValue($post, 'a_diag_utama'));
        $post['a_diag_utama_text'] = DocoHelpers::purifyText(ArrayHelper::getValue($post, 'a_diag_utama_text'));
        $post['a_diag_penyerta'] = DocoHelpers::purifyText(ArrayHelper::getValue($post, 'a_diag_penyerta'));
        $post['planning'] = DocoHelpers::purifyText(ArrayHelper::getValue($post, 'planning'));
        $post['instruksi'] = DocoHelpers::purifyText(ArrayHelper::getValue($post, 'instruksi'));
        $post['catatan_dokter'] = DocoHelpers::purifyText(ArrayHelper::getValue($post, 'catatan_dokter'));
        $post['catatan_perawat'] = DocoHelpers::purifyText(ArrayHelper::getValue($post, 'catatan_perawat'));
        $model = new CpptForm;
        $model->is_icd_x = $post['is_icd_x'];
        $model->scenario = ($auto) ? CpptForm::SUBMIT_AUTO : CpptForm::SUBMIT_SOAP;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $userIdentity = Yii::$app->session->get('user_identity');
        $loginpemakai_id = Yii::$app->docoVars->user("id");
        // Explode ruangan_id ke 4 field
        $explodeRuangan_id = explode('@#', $post['ruangan_id']);
        if (count($explodeRuangan_id) > 1) {
            $post['ruangan_id'] = (int) $explodeRuangan_id[0];
            $post['kamarruangan_id'] = $explodeRuangan_id[1];
            $post['kamartempattidur_id'] = $explodeRuangan_id[2];
            $post['kamar_tempattidur'] = $explodeRuangan_id[3];
        }
        if(!empty($post['ruangan_order'])){
            $post['ruangan_id'] = $post['ruangan_order'];
            unset($post['ruangan_order']);
        }

        // Date reformat
        $post['tgl_cppt'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $post['tgl_cppt'])));
        $post['auto'] = $auto;

        // Set post to model
        $model->attributes = $post;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        if (!$post['a_diag_penyerta']) {
            $model->a_diag_penyerta = [];
        }

        if ($model->a_diag_utama != null && $model->a_diag_penyerta != null) {
            if (in_array($model->a_diag_utama, $model->a_diag_penyerta)) {
                return DocoHelpers::responseTemplate(
                    422,
                    'Error',
                    [],
                    [
                        'title' => Yii::t('fe', 'Peringatan!'),
                        'text' => Yii::t('fe', 'Diagnosa utama dan diagnosa penyerta tidak boleh sama.'),
                        'message' => Yii::t('fe', 'Diagnosa utama dan diagnosa penyerta tidak boleh sama.'),
                    ]
                );
            }
        }
        $post['is_dokter'] = isset($userIdentity['kelompokpegawai_id']) ? ($userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS ? true : false) : false;
        // Validasi
        if ($model->validate()) {
            if (!empty($cpptId)) {
                $result = $this->guzzleExec($this->_restRanap, [
                    'method' => 'POST',
                    'url' => 'cppt/edit-soap',
                    'payload' => [
                        'form_params' => $post,
                        'query' => compact('cpptId', 'loginpemakai_id')
                    ]
                ]);

                $todayDate = date('Y-m-d');
                $formDate = date('Y-m-d', strtotime($post['tgl_cppt']));

                if ($formDate >= $todayDate) {
                    // UPDATE SESSION
                    $pendId = $this->helper->encrypt($post['pendaftaran_id']);

                    $cache = Yii::$app->cache;
                    $cacheData = $cache->get('pasien-pendaftaran-id-' . $pendId);
                    $cacheData['cppt']['a_diag_utama'] = $result['data']['a_diag_utama'];
                    $cache->set('pasien-pendaftaran-id-' . $pendId, $cacheData, 3600);
                } else {
                    $result['data']['a_diag_utama'] = '';
                }

                return $this->helper->response($result, 200);
            } else {
                // Send to backend
                $request = $this->_restRanap->post('cppt/create-soap', [
                    'form_params' => $post
                ]);
                $response = json_decode($request->getBody(), true);

                // UPDATE SESSION
                $pendId = $this->helper->encrypt($post['pendaftaran_id']);

                $cache = Yii::$app->cache;
                $cacheData = $cache->get('pasien-pendaftaran-id-' . $pendId);
                $cacheData['cppt']['a_diag_utama'] = $response['response']['data']['a_diag_utama'];
                $cache->set('pasien-pendaftaran-id-' . $pendId, $cacheData, 3600);

                // Return
                return $this->helper->response($response['response'], 200);
            }
        } else {
            $response = $model->errors;
            return DocoHelpers::response($response, 422, $formName);
        }
    }

    public function actionCpptFilters($term, $pendaftaran_id, $type)
    {
        $id = DocoHelpers::decrypt($pendaftaran_id);
        $response = $this->guzzleExec($this->_restRanap, [
            'url' => 'cppt/get-filter-cppt?',
            'method' => 'get',
            'payload' => [
                'query' => [
                    'pendaftaran_id' => $id, 'term' => $term, 'type' => $type
                ]
            ]
        ]);

        return $this->responseJson(200, 'Data berhasil diambil!', $response);
    }

    private function getListDataCppt($pasienId, $pegawaiId, $ruanganId, $pendaftaranId, $pasienadmisiId = null)
    {
        $request = $this->_restRanap->get('cppt/get-list-data-cppt', [
            'form_params' => [],
            'query' => [
                'pasien_id' => $pasienId,
                'pegawai_id' => $pegawaiId,
                'ruangan_id' => $ruanganId,
                'pendaftaran_id' => $pendaftaranId,
                'pasienadmisi_id' => $pasienadmisiId,
                'instalasi_id' => Yii::$app->docoVars->workspace('instalasi_id'),
                'type' => Yii::$app->request->get('type', 'hasil'),
                'is_cppt' => true,
            ]
        ]);
        $response = json_decode($request->getBody(), true);
        $bodyResponse = $response['response'];
        return $bodyResponse;
    }

    public function actionDeleteCppt() {
        $req = Yii::$app->request;
        $cppt_id = $req->get('cppt_id');
        $pendaftaran_id = $req->get('pendaftaran_id');
        $tipe = $req->get('tipe');
        $userIdentity = Yii::$app->session->get('user_identity');
        $pegawaiId = ArrayHelper::getValue($userIdentity, 'loginpemakai_id');
        $response = $this->guzzleExec($this->_restRanap, [
            'url' => 'cppt/delete-cppt',
            'method' => 'post',
            'payload' => [
                'form_params' => [
                    'pendaftaran_id' => DocoHelpers::setDecryptIdFromString($pendaftaran_id),
                    'cppt_id' => DocoHelpers::setDecryptIdFromString($cppt_id),
                    'user_id' => $pegawaiId,
                    'tipe' => $tipe
                ]
            ],
        ]);
        
        return json_encode($response);
    }
}
