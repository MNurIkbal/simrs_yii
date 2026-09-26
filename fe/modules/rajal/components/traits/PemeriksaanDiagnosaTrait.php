<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-03-29 13:28:41
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-03-22 10:28:02
 * @Description: 
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

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

use app\modules\rajal\models\PasienMorbiditasForm;
use app\modules\rajal\models\DiagnosaForm;
use app\components\DHtml;

trait PemeriksaanDiagnosaTrait
{

    /*================================
    =            Diagnosa            =
    ================================*/

    public function actionDiagnosa()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id', null);
        $pasien_id = $request->get('pasien_id', null);
        $session = Yii::$app->session;
        // $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);

        $list_data = $this->getListData();
        $data_diagnosa = $list_data['data_diagnosa'];
        $data_diagnosaruangan = $list_data['data_diagnosaruangan'];
        $data_kelompokdiagnosa = $list_data['data_kelompokdiagnosa'];
        $modelMorbiditas = new PasienMorbiditasForm;
        $modelMorbiditas->scenario = PasienMorbiditasForm::SCENARIO_SESSION;
        $form_name = substr(strrchr(get_class($modelMorbiditas), "\\"), 1);
        $id = DocoHelpers::decrypt($pendaftaran_id);
        $status_update = $this->_statusPeriksa;

        try {
            if ($post = $request->post()) {
                $data = $post['PasienMorbiditasForm'];
                $modelMorbiditas->attributes = $data;
                preg_match_all("/[^a-zA-Z]/", $data['diagnosa_id'], $return);
                $imp = implode("", $return[0]);
                $diagnosaId = str_replace(' ', '', $imp);
                $modelMorbiditas->diagnosa_id = $data['diagnosa_id'] = $diagnosaId;
                $modelMorbiditas->diagnosa_kode = $data['diagnosa_kode'];
                $modelMorbiditas->diagnosa_nama = $data['diagnosa_nama'];
                $modelMorbiditas->kelompokdiagnosa_id = $data['kelompokdiagnosa_id'];
                $modelMorbiditas->kelompokdiagnosa_nama = $data['kelompokdiagnosa_nama'];
                if ($modelMorbiditas->validate()) {
                    // cek if diagnosa utama
                    $is_diagnosautama = $is_diagnosamasuk = $is_diagnosakerja = false;
                    foreach ($data_kelompokdiagnosa as $key => $value) {
                        if (($data['kelompokdiagnosa_id'] == $value['kelompokdiagnosa_id']) && ($value['status_diagnosa'] == PasienMorbiditasForm::KD_UTAMA)) {
                            $is_diagnosautama = true;
                        }

                        if (($data['kelompokdiagnosa_id'] == $value['kelompokdiagnosa_id']) && ($value['status_diagnosa'] == PasienMorbiditasForm::KD_MASUK)) {
                            $is_diagnosamasuk = true;
                        }
                    }
                    if ($data['kelompokdiagnosa_id'] == 9) {
                        $is_diagnosakerja = true;
                    }
                    $response = $this->addSessionDiagnosa($data, $pendaftaran_id, $pasien_id, $is_diagnosautama, $is_diagnosamasuk, $is_diagnosakerja);
                    if ($response['status'] != 200) {
                        $message = $response['message'];

                        return DocoHelpers::responseTemplate(
                            422,
                            'Error',
                            [],
                            [
                                'title' => Yii::t('fe', 'Proses Gagal'),
                                'text' => $message,
                                'message' => $message,
                            ]
                        );
                    } else {
                        return DocoHelpers::responseTemplate(200, 'OK');
                    }
                } else {
                    $errors = DocoHelpers::parseError($modelMorbiditas->errors, $form_name);
                    return DocoHelpers::responseTemplate(422, 'Error', $errors);
                }
            } else {
                $this->resetSessionDiagnosa($pendaftaran_id);

                return $this->renderAjax('diagnosa/__diagnosa', [
                    'pendaftaran_id' => $pendaftaran_id,
                    'pasien_id' => $pasien_id,
                    'modelMorbiditas' => $modelMorbiditas,
                    'data_diagnosa' => $data_diagnosa,
                    'data_diagnosaruangan' => $data_diagnosaruangan,
                    'data_kelompokdiagnosa' => $data_kelompokdiagnosa,
                    'status_update' => $status_update,
                ]);
            }
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(
                500,
                'Error',
                [],
                [
                    'title' => Yii::t('fe', 'Peringatan'),
                    'message' => $e->getMessage(),
                ]
            );
        }
    }


    public function actionAutoDiagnosa($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $q = $request->get('search');
        $result = [];
        $result['results'] = [];
        try {
            $response = $this->_restMaster->get('tra-pemeriksaan/auto-diagnosa?advanced-filter[diagnosa_nama]=' . $q);
            $body = json_decode($response->getBody(), true);
            $temp_diagnosa = array(); // array dokter temp
            foreach ($body['response']['data'] as $value)
                if (!array_key_exists($value['diagnosa_id'], $temp_diagnosa)) {
                    $result['results'][] = [
                        'id' => $value['diagnosa_id'],
                        'text' => $value['diagnosa_nama']
                    ];
                    $temp_diagnosa[$value['diagnosa_id']] = $value['diagnosa_nama'];
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

    public function actionListDiagnosa()
    {
        try {
            $request = Yii::$app->request;
            $title = Yii::t("fe", "List diagnosa");

            return $this->renderAjax('diagnosa/___listdiagnosa', get_defined_vars());
        } catch (Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    private function addSessionDiagnosa($data = null, $pendaftaran_id, $pasien_id, $is_diagnosautama = false, $is_diagnosamasuk = false, $is_diagnosakerja = false)
    {
        $is_utamaavailable = false;
        $datapasien = $this->_data_pasien;
        $session = Yii::$app->session;
        $pendaftaran_id_decrypt = DocoHelpers::decrypt($pendaftaran_id);
        $pasien_id_decrypt = (int) DocoHelpers::decrypt($pasien_id);
        $session_diagnosa_db = $session['pemeriksaan_diagnosa_db'];
        try {
            $keychange = null;
            // get data from db
            if (!isset($session_diagnosa_db[$pendaftaran_id])) {
                $response = $this->_restRajal->get('tra-pemeriksaan/get-pasien-morbiditas?ruangan_id=' . $this->_id_ruangan . '&pendaftaran_id=' . $pendaftaran_id_decrypt . '&pasien_id=' . $pasien_id_decrypt);
                $body = json_decode($response->getBody(), true);
                $data_diagnosa_db = $body['response']['data'];

                $session_diagnosa_db[$pendaftaran_id] = $data_diagnosa_db;
                $session->set('pemeriksaan_diagnosa_db', $session_diagnosa_db);
            }
            $data_diagnosa_db = $session_diagnosa_db[$pendaftaran_id];
            // check data from db
            if (count($data_diagnosa_db) > 0) {
                foreach ($data_diagnosa_db as $key => $value) {
                    // prevent data sama
                    if ($value['diagnosa_id'] == $data['diagnosa_id'] && !empty($data['diagnosa_id']) || $value['diagnosa_kode'] == $data['diagnosa_kode'] && !empty($data['diagnosa_kode'])) {
                        if ($value['is_deleted'] == false) {
                            return [
                                'status' => 422,
                                'title' => 'Proses Gagal',
                                'message' => Yii::t('fe', 'Terdapat duplikasi diagnosa'),
                            ];
                        }
                    }
                    if ($value['is_diagnosautama'] == true && $value['is_deleted'] == false) {
                        $is_utamaavailable = true;
                    }
                    // prevent data diagnosa utama > 1 & data diagnosa masuk > 1
                    if ((($value['is_diagnosautama'] === true) && ($is_diagnosautama === true)) || (($value['is_diagnosamasuk'] === true) && ($is_diagnosamasuk === true))) {
                        if ($value['is_deleted'] == false) {
                            if (isset($value['pasienmorbiditas_id'])) {
                                $session_diagnosa_db[$pendaftaran_id][$key]['is_deleted'] = true;
                            } else {
                                $keychange = $key;
                            }
                        }
                    }
                    if ($value['is_diagnosakerja'] == true && $is_diagnosakerja) {
                        if (isset($value['pasienmorbiditas_id'])) {
                            $session_diagnosa_db[$pendaftaran_id][$key]['is_deleted'] = true;
                        } else {
                            if ($value['is_deleted'] == false) {
                                $keychange = $key;
                            }
                        }
                    }
                }
            }

            $datavalue = [
                'instalasi_id' => $this->_instalasi_id,
                'ruangan_id' => $this->_id_ruangan,
                'pegawai_id' => $this->_pegawai_id,
                'pasien_id' => (int) DocoHelpers::decrypt($pasien_id),
                'jeniskasuspenyakit_id' => $datapasien['jeniskasuspenyakit_id'],
                'kelompokdiagnosa_id' => $data['kelompokdiagnosa_id'],
                'golonganumur_id' => $datapasien['golonganumur_id'],
                'pendaftaran_id' => $pendaftaran_id_decrypt,
                'tglmorbiditas' => date('Y-m-d H:i:s'),
                'diagnosa_id' => $data['diagnosa_id'],
                'diagnosa_pasien' => ([
                    'id' => $data['diagnosa_id'],
                    'text' => !empty($data['diagnosa_kode']) ? $data['diagnosa_kode'] . ' - ' . $data['diagnosa_nama'] : $data['diagnosa_nama'],
                    'kode' => $data['diagnosa_kode']
                ]),
                'kelompokdiagnosa_nama' => $data['kelompokdiagnosa_nama'],
                'diagnosa_kode' => $data['diagnosa_kode'],
                'diagnosa_nama' => !empty($data['diagnosa_kode']) ? $data['diagnosa_kode'] . ' - ' . $data['diagnosa_nama'] : $data['diagnosa_nama'],
                'diagnosa_text' => !empty($data['diagnosa_kode']) ? $data['diagnosa_kode'] . ' - ' . $data['diagnosa_nama'] : $data['diagnosa_nama'],
                'is_diagnosautama' => $is_diagnosautama,
                'is_diagnosamasuk' => $is_diagnosamasuk,
                'is_diagnosakerja' => $is_diagnosakerja,
                'is_deleted' => false
            ];
            if ($keychange === null) {
                $session_diagnosa_db[$pendaftaran_id][] = $datavalue;
            } else {
                $session_diagnosa_db[$pendaftaran_id][$keychange] = $datavalue;
            }
            if ($is_diagnosakerja && !$is_utamaavailable) {
                $datavalue['kelompokdiagnosa_id'] = 2;
                $datavalue['kelompokdiagnosa_nama'] = 'Diagnosa Utama';
                $datavalue['is_diagnosautama'] = true;
                $datavalue['is_diagnosamasuk'] = false;
                $datavalue['is_diagnosakerja'] = false;
                $session_diagnosa_db[$pendaftaran_id][] = $datavalue;
            }
            $session->set('pemeriksaan_diagnosa_db', $session_diagnosa_db);

            return [
                'status' => 200,
                'message' => Yii::t('fe', 'OK'),
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function actionBatalSessionDiagnosa()
    {
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $errors = 'Terdapat kesalahan';

        try {
            $pendaftaran_id = $request->get('pendaftaran_id', '');
            $datakey = $request->get('key', '');
            if (isset($session['pemeriksaan_diagnosa_db'])) {
                $session_diagnosa = $session['pemeriksaan_diagnosa_db'];
                if (isset($session_diagnosa[$pendaftaran_id])) {
                    if ($datakey != '') {
                        if (isset($session_diagnosa[$pendaftaran_id][$datakey]['pasienmorbiditas_id'])) {
                            $session_diagnosa[$pendaftaran_id][$datakey]['is_deleted'] = true;
                        } else {
                            unset($session_diagnosa[$pendaftaran_id][$datakey]);
                        }
                    }
                    $session->set('pemeriksaan_diagnosa_db', $session_diagnosa);
                }
            } else {
                return DocoHelpers::response(['message' => "Data tidak ditemukan"], 500);
            }

            return DocoHelpers::responseTemplate(
                200,
                Yii::t('fe', 'message_batal'),
                [],
                ['title' => Yii::t('fe', 'message_berhasil'), 'text' => Yii::t('fe', 'message_batal')]
            );
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionSaveSessionDiagnosa()
    {
        $session = Yii::$app->session;
        $request = Yii::$app->request;
        $errors = 'Terdapat kesalahan';

        try {
            $pendaftaran_id = $request->get('pendaftaran_id', null);
            $modelMorbiditas = new PasienMorbiditasForm;

            // get/set session
            if (isset($session['pemeriksaan_diagnosa_db'][$pendaftaran_id]) && !empty($session['pemeriksaan_diagnosa_db'][$pendaftaran_id])) {
                $session_diagnosa = $session['pemeriksaan_diagnosa_db'][$pendaftaran_id];
            } else {
                $message = Yii::t('fe', 'Data belum ditambahkan!');
                return DocoHelpers::responseTemplate(
                    422,
                    'Error',
                    [],
                    [
                        'title' => Yii::t('fe', 'Peringatan'),
                        'text' => $message,
                        'message' => $message,
                    ]
                );
            }
            $data_diagnosa = $deleted_diagnosa = [];
            $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
            $utamakey = $utamaid = null;
            $penyerta = [];
            foreach ($session_diagnosa as $key => $value) {
                if (!isset($value['kelaspelayanan_id'])) {
                    $value['kelaspelayanan_id'] = $this->_data_pasien['kelaspelayanan_id'];
                }
                if (!isset($value['golonganumur_id'])) {
                    $value['golonganumur_id'] = $this->_data_pasien['golonganumur_id'];
                }
                if (!isset($value['jeniskasuspenyakit_id'])) {
                    $value['jeniskasuspenyakit_id'] = $this->_data_pasien['jeniskasuspenyakit_id'];
                }
                if (!isset($value['pegawai_id'])) {
                    $value['pegawai_id'] = $this->_pegawai_id;
                }
                if (!isset($value['instalasi_id'])) {
                    $value['instalasi_id'] = $this->_instalasi_id;
                }
                $value['additional_data'] = '';
                if (!isset($value['pasienmorbiditas_id'])) {
                    // data insert pasien morbiditas
                    $data_diagnosa[$key] = $value;
                    if ($value['kelompokdiagnosa_id'] == 2) {
                        $utamakey = $key;
                    }
                    if ($value['kelompokdiagnosa_id'] == 3) {
                        // $penyerta[] = json_decode($value['diagnosa_pasien'], true);
                        $penyerta[] = $value['diagnosa_pasien'];
                    }
                } else {
                    if ($value['is_deleted'] == true) {
                        $deleted_diagnosa[] = $value['pasienmorbiditas_id'];
                    } else {
                        if ($value['kelompokdiagnosa_id'] == 2) {
                            $utamaid = $value['pasienmorbiditas_id'];
                        }
                        if ($value['kelompokdiagnosa_id'] == 3) {
                            $diagnosapasien = ['id' => $value['diagnosa_id'], 'text' => $value['diagnosa_nama'], 'kode' => $value['diagnosa_kode']];
                            $penyerta[] = $diagnosapasien;
                        }
                    }
                }
            }
            if (!empty($utamakey)) {
                $data_diagnosa[$utamakey]['additional_data'] = json_encode(['penyerta' => $penyerta]);
            }
            if (count($data_diagnosa) < 1 && count($deleted_diagnosa) < 1) {
                return DocoHelpers::responseTemplate(200, 'OK');
            }
            $response = $this->_restRajal->post('tra-pemeriksaan/create-diagnosa', [
                'form_params' => [
                    'diagnosa_baru' => $data_diagnosa,
                    'diagnosa_hapus' => $deleted_diagnosa,
                    'utamaid' => $utamaid,
                    'penyerta' => $penyerta
                ]
            ]);
            $response = json_decode($response->getBody(), true);
            if ($response['metadata']['status'] == 200) {
                // unset session
                $this->resetSessionDiagnosa($pendaftaran_id);

                return DocoHelpers::responseTemplate(200, 'OK');
            } else {
                return DocoHelpers::responseTemplate(
                    500,
                    'Error',
                    [],
                    [
                        'title' => Yii::t('fe', 'Peringatan'),
                        'text' => 'Terdapat kesalahan',
                        'message' => $response['response']['message'],
                    ]
                );
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionGetDataDiagnosaSession()
    {
        $session = Yii::$app->session;
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id');
        $pendaftaran_id_decrypt = DocoHelpers::decrypt($pendaftaran_id);
        $pasien_id = $request->get('pasien_id');
        $pasien_id_decrypt = DocoHelpers::decrypt($pasien_id);
        $isdokter = DHtml::cekHakAkses('is-dokter') ? true : false;
        $allYellow = false;

        $data_tables = [];
        $no = $request->post('start', 1);
        try {
            $status_update = $this->_statusPeriksa;
            $disabled = ($status_update == true) ? 'disabled' : '';
            $hidden = (!$isdokter) ? 'hidden' : '';
            $temp_session = [];
            // get/set session
            if (isset($session['pemeriksaan_diagnosa_db'][$pendaftaran_id])) {
                $data_diagnosa_db = $session['pemeriksaan_diagnosa_db'][$pendaftaran_id];
            } else {
                $response = $this->_restRajal->get('tra-pemeriksaan/get-pasien-morbiditas?ruangan_id=' . $this->_id_ruangan . '&pendaftaran_id=' . $pendaftaran_id_decrypt . '&pasien_id=' . $pasien_id_decrypt);
                $body = json_decode($response->getBody(), true);
                $data_diagnosa_db = $body['response']['data'];

                $session_diagnosa_db[$pendaftaran_id] = $data_diagnosa_db;
                $session->set('pemeriksaan_diagnosa_db', $session_diagnosa_db);
            }

            // Looping untuk cek diagosa yang dihapus (semua diagnosa menjadi warna kuning)
            foreach ($data_diagnosa_db as $key => $value) {
                if ($value['is_deleted'] == true) {
                    $allYellow = true;
                }
            }

            // $data_tables = $this->generateTableDiagnosa($temp_session, $pendaftaran_id);
            foreach ($data_diagnosa_db as $key => $value) {
                if (!$value['is_deleted']) {
                    $value['aksi'] = Html::button(
                        '<i class="fa fa-times"></i>',
                        [
                            'class' => 'btn btn-danger btn-xs delete-diagnosa ' . $disabled . ' ' . $hidden,
                            'action' => '/rajal/pemeriksaan/batal-session-diagnosa?key=' . $key . '&pendaftaran_id=' . $pendaftaran_id,
                            'data-confirm-message' => Yii::t('fe', 'confirm_batal'),
                        ]
                    );
                    $value['color'] = '';
                    if ($allYellow == false) {
                        if (!isset($value['pasienmorbiditas_id'])) {
                            $value['color'] = '#ffec8b';
                        }
                    } else {
                        $value['color'] = '#ffec8b';
                    }

                    array_push($data_tables, $value);
                }
            }

            $return = [
                'data' => $data_tables,
                'draw' => $request->post('draw'),
                'recordsTotal' => count($data_tables),
                'recordsFiltered' => count($data_tables)
            ];
            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    private function generateTableDiagnosa($datas = [], $pendaftaran_id)
    {
        // get data
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $pendaftaran_id_decrypt = DocoHelpers::decrypt($pendaftaran_id);

        $data_tables = [];
        $no = $request->post('start', 1);
        try {
            // data get from db
            $status_update = $this->getStatusPeriksa($pendaftaran_id_decrypt);
            $disabled = ($status_update == true) ? 'disabled' : '';
            if (isset($session['pemeriksaan_diagnosa_db'][$pendaftaran_id])) {
                $data_diagnosa_db = $session['pemeriksaan_diagnosa_db'][$pendaftaran_id];
            } else {
                $response = $this->_restRajal->get('tra-pemeriksaan/get-pasien-morbiditas?ruangan_id=' . $this->_id_ruangan . '&pendaftaran_id=' . $pendaftaran_id_decrypt);
                $body = json_decode($response->getBody(), true);
                $data_diagnosa_db = $body['response']['data'];

                $session_diagnosa_db[$pendaftaran_id] = $data_diagnosa_db;
                $session->set('pemeriksaan_diagnosa_db', $session_diagnosa_db);
            }
            foreach ($data_diagnosa_db as $key => $value) {
                $value['aksi'] = Html::button(
                    '<i class="fa fa-times"></i>',
                    [
                        'class' => 'btn btn-danger btn-xs delete-diagnosa ' . $disabled . '',
                        // 'action' => '/rajal/pemeriksaan/batal-diagnosa?pasienmorbiditas_id='. $value['pasienmorbiditas_id'],
                        'data-confirm-message' => Yii::t('fe', 'confirm_batal'),
                    ]
                );

                array_push($data_tables, $value);
            }

            $return = [
                'data' => $data_tables,
                'draw' => $request->post('draw'),
                'recordsTotal' => count($data_tables),
                'recordsFiltered' => count($data_tables)
            ];
        } catch (\Exception $e) {
            $return = [
                'data' => [],
                'draw' => $request->post('draw'),
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ];
        } catch (\RequestException $e) {
            $return = [
                'data' => [],
                'draw' => $request->post('draw'),
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ];
        }
        return $return;
    }

    public function actionBatalDiagnosa()
    {
        $request = Yii::$app->request;
        $errors = 'Terdapat kesalahan';
        $pendaftaran_id = $request->get('pendaftaran_id');

        try {
            $pasienmorbiditas_id = $request->get('pasienmorbiditas_id', '');

            if ($pasienmorbiditas_id) {
                $response = $this->_restRajal->get('tra-pemeriksaan/delete-diagnosa?id=' . $pasienmorbiditas_id);
                $body = json_decode($response->getBody(), true);

                if ($body['response']['Status'] == 200) {
                    $this->resetSessionDiagnosa($pendaftaran_id, 2);

                    return DocoHelpers::responseTemplate(
                        200,
                        Yii::t('fe', 'message_batal'),
                        [],
                        ['title' => Yii::t('fe', 'message_berhasil'), 'text' => Yii::t('fe', 'Data dihapus')]
                    );
                } else {
                    return DocoHelpers::responseTemplate(500, 'Error', $body['response']['message']);
                }
            } else {
                return DocoHelpers::responseTemplate(500, 'Error', $errors);
            }

            return DocoHelpers::responseTemplate(
                200,
                Yii::t('fe', 'message_batal'),
                [],
                ['title' => Yii::t('fe', 'message_berhasil'), 'text' => Yii::t('fe', 'message_batal')]
            );
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }
    public function actionResetSession($pendaftaran_id)
    {
        try {
            $response = $this->resetSessionDiagnosa($pendaftaran_id);
            return true;
        } catch (Exception $e) {
            return DocoHelpers::response(['message' => 'Terjadi Kesalahan'], 500);
        }
    }
    private function resetSessionDiagnosa($pendaftaran_id, $case = 0)
    {
        /**
         * @param : $case integer, 0 default value
         * @param : $case integer, 1 = session add diagnosa session
         * @param : $case integer, 2 = session diagnosa data from db
         */

        $session = Yii::$app->session;

        if ($case == 0 || $case == 1) {
            if (isset($session['pemeriksaan_diagnosa'][$pendaftaran_id])) {
                $temp_session = $session['pemeriksaan_diagnosa'];
                if (isset($temp_session[$pendaftaran_id])) {
                    unset($temp_session[$pendaftaran_id]);
                }
                $session->set('pemeriksaan_diagnosa', $temp_session);
            }
        }

        if ($case == 0 || $case == 2) {
            if (isset($session['pemeriksaan_diagnosa_db'][$pendaftaran_id])) {
                $temp_session = $session['pemeriksaan_diagnosa_db'];
                if (isset($temp_session[$pendaftaran_id])) {
                    unset($temp_session[$pendaftaran_id]);
                }
                $session->set('pemeriksaan_diagnosa_db', $temp_session);
            }
        }

        return true;
    }

    public function actionExportPdfDiagnosa($pendaftaran_id = null, $ruangan_id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        // $no_pendaftaran = $this->_data_pasien['no_pendaftaran'];
        $pendaftaran_id_encrypt = DocoHelpers::encrypt($pendaftaran_id);
        $ruangan_id = !empty($ruangan_id) ? $ruangan_id : $this->_id_ruangan;
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);

        // $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/pemeriksaan-diagnosa-{$pendaftaran_id_encrypt}.pdf";

        // if (!isset($session['pemeriksaan_diagnosa_db'][$pendaftaran_id_encrypt])){
        //     return DocoHelpers::responseTemplate(
        //         500, 
        //         'Error', 
        //         [], 
        //         [
        //             'title' => Yii::t('fe', 'Peringatan'), 
        //             'text' => Yii::t('fe', 'Data kosong'),
        //             'message' => Yii::t('fe', 'Data kosong'),
        //         ]
        //     );
        // }

        // try {
        $response = $this->_restRajal->get('tra-pemeriksaan/export-pdf-diagnosa?pendaftaran_id=' . $pendaftaran_id . '&ruangan_id=' . $ruangan_id, [
            'save_to' => $path
        ]);
        $body = json_decode($response->getBody(), true);

        return DocoHelpers::previewPdf($path);
        // } catch (RequestException $e) {
        //     throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        // } catch (\Exception $e) {
        //     throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        // }
    }

    public function actionGetDataDiagnosa()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restRajal->get('tra-pemeriksaan/get-diagnosa?' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $data_body = $body['response']['data'];

            $no = 0;
            foreach ($data_body as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['diagnosa_id']);

                $value['rowNum'] = $no;
                $value['primary'] = $primaryKey;

                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = count($data);
            $result['recordsFiltered'] = count($data);
            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    /**
     * @todo Fungsi untuk mendapatkan icd ruangan by versi tabular list
     * @author Sigit Arif Munandar <sigit@docotel.com>
     **/
    public function actionGetListDiagnosaByVersiTabular()
    {
        try {
            $out = [];
            $temp_id = [];

            if (isset($_POST['depdrop_parents'])) {
                $parents = $_POST['depdrop_parents'];
                if ($parents != null) {
                    $kelompokdiagnosa_id = $parents[0];

                    if ($kelompokdiagnosa_id == DocoConstants::VAR_KELOMPOK_DIAGNOSA_TERAPI) {
                        $versiTabular = 'ICD IX';
                    } else {
                        $versiTabular = 'ICD X';
                    }

                    $restRajal = $this->_restRajal->get('tra-pemeriksaan/get-diagnosa-by-versi-tabular?ruangan_id=' . $this->_id_ruangan . '&versi_tabular=' . $versiTabular);
                    $body = json_decode($restRajal->getBody(), true);
                    $data = $body['response'];

                    if (!empty($data)) {
                        foreach ($data as $key => $value) {
                            if (!in_array($value['diagnosa_id'], $temp_id)) {
                                $temp_id[] = $value['diagnosa_id'];
                                $out[] = ['id' => $value['diagnosa_id'], 'name' => $value['diagnosa_kode'] . ' - ' . $value['diagnosa_nama']];
                            }
                        }
                    }

                    return json_encode(['output' => $out, 'selected' => '']);
                }
            }
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}
