<?php

/**
 * @Author: Sigit
 * @Date:   2018-08-13 11:00:43
 */

namespace app\modules\igd\components\traits;

use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;

use function GuzzleHttp\json_encode;
use GuzzleHttp\Exception\RequestException;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use Doco\components\DocoMessages;
use app\components\Pelayanan\PelayananHelpers;

use app\modules\igd\models\CpptForm;
// use app\modules\igd\models\VerbalOrderRanapForm;
use app\modules\igd\models\InstruksiForm;
use app\modules\igd\models\InstruksiTindakanForm;
use app\modules\igd\models\InstruksiTindakanBmhpForm;
use app\modules\igd\models\InstruksiPenunjangForm;
use app\modules\igd\models\ResepturForm;
use app\modules\igd\models\ResepturDetailForm;
use app\modules\igd\models\ResepturNrDetailForm;
use app\modules\igd\models\JadwalOperasiForm;
use app\modules\igd\models\VerbalOrderForm;
use app\modules\igd\models\DietPasienForm;

trait AsesmenDpjpTrait
{
    /* Fungsi asesmen DPJP */
    public function actionAsesmenDpjp($id = null, $cppt_id = null, $draft_id = 1)
    {
        try {

            $pendaftaran_id = DocoHelpers::decrypt($id);
            $data_pasien = $this->_data_pasien;
            $userIdentity = $this->_user_identity;
            $classPerawat = 'hidden';
            $classDokter = 'hidden';
            $buttonVerbalOrder = 'hidden';
            $isPerawat = false;
            if(isset($userIdentity['kelompokpegawai_id']) && $userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN){
                $isPerawat = true;
                $classPerawat = '';
                $buttonVerbalOrder = '';
            }

            if(isset($userIdentity['kelompokpegawai_id']) && $userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS){
                $classDokter = '';
                $buttonVerbalOrder = '';
            }

            $params = Yii::$app->request;
            $post = $params->post('CpptForm');
            $data_pasien = $this->_data_pasien;
            $optDiagUtama = [];
            $valDiagPnyrt = $optDiagPnyrt = [];
            $model = new CpptForm;
            $model->pendaftaran_id = $data_pasien['pendaftaran_id'];
            $model->pasien_id = $data_pasien['pasien_id'];
            $model->pegawai_id = $this->_pegawai_id;
            $model->ruangan_id = $data_pasien['ruangan_id'];
            $model->cppt_id = $cppt_id;
            $model->is_active = true;
            $pegawai_id = $this->_pegawai_id;
            if (!empty($post['cppt_id'])) {
                $model->cppt_id = DocoHelpers::decrypt($post['cppt_id']);
            }

            if (isset($post['cppt_id'])) unset($post['cppt_id']);
            $model->tgl_cppt = date('d/m/Y H:i:s', strtotime('NOW'));
            $formName = substr(strrchr(get_class($model), "\\"), 1);

            if ($draft_id == 0) {
                $model->is_active = false;
            }

            //SOAP ke db untuk data lama atau baru
            if ($model->load($post, '')) {
                $post['subject'] = nl2br($post['subject']);
                $post['object'] = nl2br($post['object']);
                $post['planning'] = nl2br($post['planning']);
                $post['is_active'] = $model->is_active;

                if (!$post['a_diag_penyerta']) {
                    $model->a_diag_penyerta = [];
                }
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

                if($model->validate()) {
                    $request = $this->_restIgd->post('asesmen-dpjp/create-soap', [
                        'form_params' => $post,
                        'query' => [
                            'cppt_id' => $model->cppt_id
                        ]
                    ]);
                    $response = json_decode($request->getBody(), true);

                    return json_encode($response);
                } else {
                    $response = $model->errors;
                    return DocoHelpers::response($response, 422, $formName);
                }

            //soap get sugest data for edit
            } else {
                if (!empty($cppt_id)) {
                    $request = $this->_restIgd->get('asesmen-dpjp/get-soap', [
                        'query' => [
                            'cppt_id' => DocoHelpers::decrypt($cppt_id)
                        ]
                    ]);
                } else if ($cppt_id == null) {
                    $request = $this->_restIgd->get('asesmen-dpjp/get-soap-draft', [
                        'query' => [
                            'pendaftaran_id' => DocoHelpers::decrypt($id),
                            'pegawai_id'     => $this->_pegawai_id
                        ]
                    ]);
                }

                $response = json_decode($request->getBody(), true);
                $dataCppt = isset($response['response']['data']) ? $response['response']['data'] : [];
                $diagUtama = !empty($dataCppt['a_diag_utama']) ? json_decode($dataCppt['a_diag_utama'], true) : [];
                $diagPenyerta = !empty($dataCppt['a_diag_penyerta']) ? json_decode($dataCppt['a_diag_penyerta'], true) : [];

                $idDiagUtama = $textDiagUtama = null;
                if (isset($diagUtama['id']) && $diagUtama['text']) {
                    $idDiagUtama = $diagUtama['id'] . '_' . $diagUtama['text'];
                    $textDiagUtama = $diagUtama['text'];
                }

                foreach ($diagPenyerta as $value) {
                    if (isset($value['id']) && isset($value['text'])) {
                        $idDiag = $value['id'] . '_' . $value['text'];
                        $textDiag = $value['text'];
                        $valDiagPnyrt[] = $idDiag;
                        $optDiagPnyrt[] = [$idDiag => $textDiag];
                    } else if (isset($value['text'])) {
                        $valDiagPnyrt[] = $value['text'];
                        $optDiagPnyrt[] = [$value['text'] => $value['text']];
                    }
                }

                $model->a_diag_utama = $idDiagUtama;
                $model->a_diag_penyerta = $valDiagPnyrt;
                $optDiagUtama = [$idDiagUtama => $textDiagUtama];
                $model->subject = isset($dataCppt['subject']) ? strip_tags($dataCppt['subject']) : null;
                $model->object = isset($dataCppt['object']) ? strip_tags($dataCppt['object']) : null;
                $model->planning = isset($dataCppt['planning']) ? strip_tags($dataCppt['planning']) : null;
                $model->catatan_dokter = isset($dataCppt['catatan_dokter']) ? strip_tags($dataCppt['catatan_dokter']) : null;
                $model->instruksi = isset($dataCppt['instruksi']) ? strip_tags($dataCppt['instruksi']) : null;
                $model->tgl_cppt = isset($dataCppt['tgl_cppt']) ? date('d/m/Y H:i:s', strtotime($dataCppt['tgl_cppt'])) : null;
                $requestLastCppt = $this->_restIgd->get('asesmen-dpjp/get-last-cppt', [
                    'query' => [
                        'pendaftaran_id' => DocoHelpers::decrypt($id),
                        'pegawai_id'     => $this->_pegawai_id
                    ]
                ]);
                $lastCppt = json_decode($requestLastCppt->getBody(), true);
                $lastCppt = DocoHelpers::encrypt($lastCppt['response']);
            }

            $response = $this->_restIgd->get('allow/pelayanan-config-button');
            $pelayananConfigButton = json_decode($response->getBody(), true);
            $pelayananConfigButton = isset($pelayananConfigButton['response']['data']) ? $pelayananConfigButton['response']['data'] : [];
            if(!empty($pelayananConfigButton)) {
                foreach ($pelayananConfigButton as $key => $pelayanan) {
                    $dataPelayanan = [];
                    $dataPelayanan['name'] = $pelayanan['nama_fitur'];
                    $dataPelayanan['title'] = $pelayanan['title'];
                    $pelayanan['additional_data'] = json_decode($pelayanan['additional_data'], true);
                    $additional = empty($pelayanan['additional_data']) ? [] : $pelayanan['additional_data'];
                    $dataPelayanan = array_merge($dataPelayanan, $additional);
                    if($isPerawat && !$pelayanan['is_perawat']) {
                        $dataPelayanan['disabled'] = true;
                    } else if(!$isPerawat && !$pelayanan['is_dokter']) {
                        $dataPelayanan['disabled'] = true;
                    }

                    $pelayanan['additional_condition'] = json_decode($pelayanan['additional_condition'], true);
                    foreach ($pelayanan['additional_condition']['data_pasien'] as $attribute => $value) {
                        if(is_array($value['values'])) {
                            foreach ($value['values'] as $val) {
                                if($data_pasien[$attribute] == $val) {
                                    $dataPelayanan[$value['attr']] = $value['attr_value'];
                                }
                            }
                        } else {
                            if($data_pasien[$attribute] == $value['values']) {
                                $dataPelayanan[$value['attr']] = $value['attr_value'];
                            }
                        }
                    }
                    $pelayananConfigButton[$key] = $dataPelayanan;
                }
            }
            $rest_time_reset = $this->_restIgd->get('allow/time-reset-suggest-soap?kode_lookup='.DocoConstants::TIME_RESET_SUGGEST_SOAP);
            $rest_time_reset = json_decode($rest_time_reset->getBody(), true);
            $time_reset = isset($rest_time_reset['response']['data']) ? $rest_time_reset['response']['data']: 1440;
            $pasien_encrypt_id = DocoHelpers::encrypt($data_pasien['pasien_id']);
            $res_hasil_rad = Yii::$app->docoRest->radiologi->get('hasil-rad/get-total-hasil-radiologi', [
                'query' => [
                    'pendaftaran_id' => $pendaftaran_id,
                    'pasienadmisi_id' => null,
                    'is_read' => false,
                    'is_with_admisi' => true,
                ],
            ]);
            $body_hasil_rad = json_decode($res_hasil_rad->getBody(), true);
            $total_belum_baca_rad = isset($body_hasil_rad['response']['total_hasil_radiologi']) ? $body_hasil_rad['response']['total_hasil_radiologi'] : 0;
            $id_ruangan = $this->_ruangan_id;

            $config_soap = $this->_restIgd->get('allow/get-konfig-system');
            $config_soap = json_decode($config_soap->getBody(), true);
            $config_soap = isset($config_soap['response']['hide_instruksi_soap']) ? $config_soap['response']['hide_instruksi_soap']: true;
            
            return $this->renderAjax('asesmen-dpjp/index', get_defined_vars());
        } catch (RequestException $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        } catch (\Exception $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        }
    }

    /* Fungsi create SOAP */
    public function actionCreateSoap($id = null, $cppt_id = null, $draft_id = 1)
    {
        try {
            $params = Yii::$app->request;
            $post = $params->post('CpptForm');
            $post['subject'] = DocoHelpers::purifyText(ArrayHelper::getValue($post, 'subject'));
            $post['object'] = DocoHelpers::purifyText(ArrayHelper::getValue($post, 'object'));
            $post['a_diag_utama_text'] = DocoHelpers::purifyText(ArrayHelper::getValue($post, 'a_diag_utama_text'));
            $post['a_diag_penyerta'] = DocoHelpers::purifyText(ArrayHelper::getValue($post, 'a_diag_penyerta'));
            $post['planning'] = DocoHelpers::purifyText(ArrayHelper::getValue($post, 'planning'));
            $post['a_diag_utama'] = DocoHelpers::purifyText(ArrayHelper::getValue($post, 'a_diag_utama'));
            $post['instruksi'] = DocoHelpers::purifyText(ArrayHelper::getValue($post, 'instruksi'));
            $post['catatan_dokter'] = DocoHelpers::purifyText(ArrayHelper::getValue($post, 'catatan_dokter'));
            // $listDataAsesmenDpjp = $this->getListDataAsesmenDpjp($this->_pasien_id, $this->_pegawai_id, $this->_ruangan_id);
            // $pegawai = $listDataAsesmenDpjp['pegawai'];
            $data_pasien = $this->_data_pasien;
            $optDiagUtama = [];
            $valDiagPnyrt = $optDiagPnyrt = [];
            $model = new CpptForm;

            $model->tanggalPendaftaran = ArrayHelper::getValue($this->_data_pasien, 'tgl_pendaftaran', null);
            $model->pendaftaran_id = $data_pasien['pendaftaran_id'];
            $model->pasien_id = $data_pasien['pasien_id'];
            $model->pegawai_id = $this->_pegawai_id;
            $model->ruangan_id = $data_pasien['ruangan_id'];
            $model->cppt_id = $cppt_id;
            $model->is_active = true;
            if (!empty($post['cppt_id'])) {
                $model->cppt_id = DocoHelpers::decrypt($post['cppt_id']);
            }

            if (isset($post['cppt_id'])) unset($post['cppt_id']);
            $post['tgl_cppt'] = (date_create_from_format('d/m/Y H:i:s', $post['tgl_cppt'])) ? date_format(date_create_from_format('d/m/Y H:i:s', $post['tgl_cppt']), 'Y-m-d H:i:s') : $post['tgl_cppt'];
            $formName = substr(strrchr(get_class($model), "\\"), 1);

            if ($draft_id == 0) {
                $model->is_active = false;
            }
            $model->attributes = $post;
            //SOAP ke db untuk data lama atau baru
            if ($model->load($post, '')) {
                $post['subject'] = $post['subject'];
                $post['object'] = $post['object'];
                $post['planning'] = $post['planning'];
                $post['is_active'] = $model->is_active;
                if (!$post['a_diag_penyerta']) {
                    $model->a_diag_penyerta = [];
                }
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
                if ($model->validate()) {
                    $result = $this->guzzleExec($this->_restIgd, [
                        'method' => 'POST',
                        'url' => 'asesmen-dpjp/create-soap',
                        'payload' => [
                            'form_params' => $model->attributes,
                            'query' => [
                                'cppt_id' => $model->cppt_id
                            ]
                        ]
                    ]);

                    if (!empty($post['subject']) && !empty($post['object']) && !empty($post['a_diag_utama']) && !empty($post['planning'])) {
                        $cache = Yii::$app->cache;
                        $cacheData = $cache->get('data-pasien-igd-' . $model->pendaftaran_id);

                        $cacheData['cppt']['a_diag_utama'] = @$result['data']['a_diag_utama'];

                        $cache->set('data-pasien-igd-' . $model->pendaftaran_id, $cacheData, 3600);

                        return $this->helper->response($result, 200);
                    } else {
                        $result['data']['a_diag_utama'] = '';

                        return $this->helper->response($result, 200);
                    }
                } else {
                    $formName = substr(strrchr(get_class($model), "\\"), 1);
                    $response = $model->errors;
                    return DocoHelpers::response($response, 422, $formName);
                }
            //soap get sugest data for edit
            } else {
                if (!empty($cppt_id)) {
                    $request = $this->_restIgd->get('asesmen-dpjp/get-soap', [
                        'query' => [
                            'cppt_id' => DocoHelpers::decrypt($cppt_id)
                        ]
                    ]);
                }
                else if ($cppt_id == null) {
                    $request = $this->_restIgd->get('asesmen-dpjp/get-soap-draft', [
                        'query' => [
                            'pendaftaran_id' => DocoHelpers::decrypt($id),
                            'pegawai_id'     => $this->_pegawai_id
                        ]
                    ]);
                }

                $response = json_decode($request->getBody(), true);
                $dataCppt = isset($response['response']['data']) ? $response['response']['data'] : [];
                $diagUtama = !empty($dataCppt['a_diag_utama']) ? json_decode($dataCppt['a_diag_utama'], true) : [];
                $diagPenyerta = !empty($dataCppt['a_diag_penyerta']) ? json_decode($dataCppt['a_diag_penyerta'], true) : [];

                $idDiagUtama = $textDiagUtama = null;
                if (isset($diagUtama['id']) && $diagUtama['text']) {
                    $idDiagUtama = $diagUtama['id'] . '_' . $diagUtama['text'];
                    $textDiagUtama = $diagUtama['text'];
                }

                foreach ($diagPenyerta as $value) {
                    if (isset($value['id']) && isset($value['text'])) {
                        $idDiag = $value['id'] . '_' . $value['text'];
                        $textDiag = $value['text'];
                        $valDiagPnyrt[] = $idDiag;
                        $optDiagPnyrt[] = [$idDiag => $textDiag];
                    } else if (isset($value['text'])) {
                        $valDiagPnyrt[] = $value['text'];
                        $optDiagPnyrt[] = [$value['text'] => $value['text']];
                    }
                }

                $model->a_diag_utama = $idDiagUtama;
                $model->a_diag_penyerta = $valDiagPnyrt;
                $optDiagUtama = [$idDiagUtama => $textDiagUtama];
                $model->subject = isset($dataCppt['subject']) ? strip_tags($dataCppt['subject']) : null;
                $model->object = isset($dataCppt['object']) ? strip_tags($dataCppt['object']) : null;
                $model->planning = isset($dataCppt['planning']) ? $dataCppt['planning'] : null;
                $model->catatan_dokter = isset($dataCppt['catatan_dokter']) ? strip_tags($dataCppt['catatan_dokter']) : null;
                // }
                // var_dump($response);die();
                return $this->renderAjax('asesmen-dpjp/form_soap', get_defined_vars());
            }
        } catch (RequestException $e) {
            $this->logError($e);
            return $this->responseJson(500, 'Terjadi kesalahan pada server.');
        } catch (\Exception $e) {
            $this->logError($e);
            return $this->responseJson(500, 'Terjadi kesalahan pada server.');
        }
    }

    public function actionDraftSoap($id = null, $cppt_id = null, $draft_id = 1)
    {
        $params = Yii::$app->request;
        $post = $params->post('CpptForm');
        // $listDataAsesmenDpjp = $this->getListDataAsesmenDpjp($this->_pasien_id, $this->_pegawai_id, $this->_ruangan_id);
        // $pegawai = $listDataAsesmenDpjp['pegawai'];
        $data_pasien = $this->_data_pasien;
        $optDiagUtama = [];
        $valDiagPnyrt = $optDiagPnyrt = [];
        $model = new CpptForm;
        $model->pendaftaran_id = $data_pasien['pendaftaran_id'];
        $model->pasien_id = $data_pasien['pasien_id'];
        $model->pegawai_id = $this->_pegawai_id;
        $model->ruangan_id = $data_pasien['ruangan_id'];
        $model->cppt_id = $cppt_id;
        $model->is_active = true;
        if (!empty($post['cppt_id'])) {
            $model->cppt_id = DocoHelpers::decrypt($post['cppt_id']);
        }

        if (isset($post['cppt_id'])) unset($post['cppt_id']);
        $model->tgl_cppt = date('Y-m-d H:i:s', strtotime('NOW'));
        $formName = substr(strrchr(get_class($model), "\\"), 1);

        if ($draft_id == 0) {
            $model->is_active = false;
        }

        //SOAP ke db untuk data lama atau baru
        if ($model->load($post, '')) {
            $post['subject'] = nl2br($post['subject']);
            $post['object'] = nl2br($post['object']);
            $post['planning'] = nl2br($post['planning']);
            $post['is_active'] = $model->is_active;

            if (!$post['a_diag_penyerta']) {
                $model->a_diag_penyerta = [];
            }
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

            if($model->validate()) {
                $request = $this->_restIgd->post('asesmen-dpjp/create-soap', [
                    'form_params' => $post,
                    'query' => [
                        'cppt_id'    => $model->cppt_id,
                        'pegawai_id' => $this->_pegawai_id
                    ]
                ]);
                $response = json_decode($request->getBody(), true);

                return json_encode($response);
            } else {
                $response = $model->errors;
                return DocoHelpers::response($response, 422, $formName);
            }
        }
    }

    /* Fungsi create VERBAL ORDER */
    public function actionCreateVerbalOrder()
    {
        $params = Yii::$app->request;
        $post = $params->post('VerbalOrderForm');
        $data_pasien = $this->_data_pasien;
        $ruangan_id = $data_pasien['ruangan_id'];
        $ruangan_nama = $data_pasien['ruangan_nama'];

        $model = new VerbalOrderForm;
        $model->pendaftaran_id = $data_pasien['pendaftaran_id'];
        $model->kelaspelayanan_id = $data_pasien['kelaspelayanan_id'];
        $model->penjamin_id = $data_pasien['penjamin_id'];
        $model->ruangan_id = $data_pasien['ruangan_id'];
        $model->ruangan_nama = $data_pasien['ruangan_nama'];

        $formName = substr(strrchr(get_class($model), "\\"), 1);

        if ($model->load($post, '')) {
            $post['instruksi'] = nl2br($post['instruksi']);
            if($model->validate()) {
                return $this->guzzleExec($this->_restIgd, [
                    'url' => 'verbal-order/create',
                    'method' => 'POST',
                    'payload' => [
                        'form_params' => $post
                    ],
                    'returnResponse' => true
                ]);
            } else {
                $response = $model->errors;
                return DocoHelpers::response($response, 422, $formName);
            }
        } else {
            return $this->renderAjax('asesmen-dpjp/form_verbal_order', get_defined_vars());
        }
    }

    /* Fungsi get diagnosa */
    public function actionGetDiagnosa($q = null)
    {
        try {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $out = ['results' => ['id' => '', 'text' => '']];

            if (!is_null($q)) {
                $request = $this->_restIgd->get('asesmen-dpjp/get-list-diagnosa?q='.$q);
                $response = json_decode($request->getBody(), true);
                $data = $response['response']['data'];

                $out['results'] = array_values($data);
            }

            return $out;
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    /**
     *
     * get data obatalkes
     *
     */
    public function actionListObatAlkesDepo()
    {
        $instalasi_id = Yii::$app->docoVars->workspace('instalasi_id');
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
        $request = Yii::$app->request;
        $get = $request->get();
        $ruangan_id = isset($get['ruangan_id']) ? $get['ruangan_id'] : null;
        $keyword = isset($get['q']) ? $get['q'] : '';
        $page = isset($get['page']) ? $get['page'] : 1;
        $penjamin_id = isset($get['penjamin_id']) ? $get['penjamin_id'] : 1;
        $kelaspelayanan_id = $this->_kelaspelayanan_id;
        $return = [];
        $data = [];

        $group_jenis = null;
        $userIdentity = $this->_user_identity;
        if($userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN)
        {
            $group_jenis = DocoConstants::GOUP_ALKES;
        }

        try {
            // $request = $this->_restIgd->get('asesmen-dpjp/list-obat-alkes?ruangan_id=' .$ruangan_id.'&keyword='. $keyword.'&page='. $page);
            $request = $this->_restApotek->get('allow/get-list-stok-apotek',[
                'query' => [
                    'instalasi_id' => $instalasi_id,
                    'penjamin_id'  => $penjamin_id,
                    'group_jenisobat' => $group_jenis,
                    'ruangan_id'   => $ruangan_id,
                    'kelaspelayanan_id' => $kelaspelayanan_id,
                    'keyword'      => $keyword,
                    'page'         => $page,
                ]
            ]);
            $body = json_decode($request->getBody(),TRUE);
            if(!empty($body['response']['data'])) {
                $data = $body['response']['data'];

                $cacheLabelTrackStock = 'trackObatPasien' . $ruangan_id . '-' . $pegawai_id;
                $cacheTrackStok = Yii::$app->cache->get($cacheLabelTrackStock);
                $trackStock = json_decode($cacheTrackStok, true);

                foreach ($data as $index => $stok_obat) {
                    $obatalkes_id = $stok_obat['obatalkes_id'];
                    $stok_terpakai = isset($trackStock[$obatalkes_id]) ? $trackStock[$obatalkes_id] : 0;
                    $data[$index]['qty_tersedia'] -= $stok_terpakai;
                }
            }

            $return = [
                'data_stok' => $data,
                'payload' => $get
            ];

            return DocoHelpers::response($return);
        } catch (\Exception $e) {
            echo json_encode(['data_stok'=>[], 'payload'=>$get]);
            return;
        }

    }

    /* Get list data asesmen dpjp */
    private function getListDataAsesmenDpjp($pasien_id, $pegawai_id, $ruangan_id)
    {
        try {
            $request = $this->_restIgd->get('asesmen-dpjp/get-list-data-asesmen-dpjp?pasien_id='.$pasien_id.'&pegawai_id='.$pegawai_id.'&ruangan_id='.$ruangan_id);
            $response = json_decode($request->getBody(), true);

            return $response['response'];
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    public function actionGetDataAsesmenDpjp()
    {
        // Try catch
        try {
            // Inisiasi
            Yii::$app->response->format = Response::FORMAT_JSON;
            $params = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($params->get());
            $draw = $params->get('draw', 1);
            $pendaftaran_id = DocoHelpers::decrypt($params->get('id'));
            $pasien_id = DocoHelpers::decrypt($params->get('pasien_id', null));
            $ruangan_id = $params->get('ruangan_id', null);
            $pegawai_id = $params->get('pegawai_id', null);
            $kelompokpegawai_id = $params->get('kelompokpegawai_id', null);
            $tgl_cppt = $params->get('tanggal_cppt', null);
            $length = $params->get('length', null);
            $start = $params->get('start', null);

            $data = [];
            $pendaftaran_id = DocoHelpers::decrypt($params->get('id'));

            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;
            $result['recordsFiltered'] = 0;

            $request = $this->_restIgd->get('asesmen-dpjp/get-data-asesmen-dpjp?pendaftaran_id='.$pendaftaran_id. '&pasien_id='.$pasien_id.'&filterruangan_id=' . $ruangan_id . '&filterpegawai_id=' . $pegawai_id . '&' . http_build_query($yiiRestfulParams) .'&tgl_cppt='.$tgl_cppt."&filter_kelompokpegawai_id={$kelompokpegawai_id}" ."&length={$length}" . "&start={$start}", ['form_params' => []]);
            $response = json_decode($request->getBody(), true);

            $no = $params->get('start', 1);
            foreach ($response['response']["data"] as $key => $value) {
                $spesialis_nama = strtolower($value['spesialis_nama']);
                $no++;
                $ruangan = '<p>'.$value['ruangan_nama'].'</p>';
                $profesi = '<p' . ($value['kelompokpegawai_id'] == 1 ? 'dokter' : 'perawat') . '">' . $value['kelompokpegawai_nama'] ." - ". ucwords(str_replace('_', ' ', $spesialis_nama)). '</p>' . $value['nama_pegawai'];
                $tgl_cppt = date('d/m/Y', strtotime($value['tgl_cppt'])).'<br>'.date('H:i:00', strtotime($value['tgl_cppt']));
                $ruangan_profesi = $ruangan . $profesi;
                $data[$key]['primary'] = DocoHelpers::encrypt($value['origin_cppt_id']);
                $data[$key]['no'] = $no;
                $data[$key]['user_identity'] = $this->_user_identity;
                $data[$key]['ruangan'] = $ruangan_profesi;
                $data[$key]['tgl_cppt'] = $tgl_cppt;
                $data[$key]['tgl_soaprj'] = strtotime($value['tgl_cppt']);
                $data[$key]['another_format_tgl_cppt'] = date('Y-m-d', strtotime($value['tgl_cppt'])) . ' ' . date('H:i', strtotime($value['tgl_cppt']));;
                $data[$key]['penatalaksanaan'] = $this->getPenatalaksanaan($value);
                // $data[$key]['instruksi_dpjp'] = $this->getInstruksiDpjp($value['detailInstruksi']);
                $data[$key]['verifikasi'] = $this->getVerifikasi($value, []);
                $data[$key]['subject'] = str_replace('<br />'," ", $value['subject']);
                $data[$key]['object'] = str_replace('<br />'," ", $value['object']);
                $data[$key]['a_diag_utama'] = $value['a_diag_utama'];
                $data[$key]['a_diag_penyerta'] = $value['a_diag_penyerta'];
                $data[$key]['planning'] = str_replace('<br />'," ", $value['planning']);
                $data[$key]['catatan_dokter'] = str_replace('<br />'," ", $value['catatan_dokter']);
                $data[$key]['instruksi'] = str_replace('<br />'," ", $value['instruksi']);
                $data[$key]['instruksi_soap'] = '<div style="white-space: pre-line">'.str_replace('<br />'," ", $value['instruksi']).'</div>';
                $data[$key]['is_deleted'] = $value['is_deleted'];
                $data[$key]['origin_tgl_cppt'] = $value['tgl_cppt'];
                $data[$key]['origin_cppt_id'] = $this->helper->encrypt($value['origin_cppt_id']);
                $data[$key]['is_verifikasi'] = $value['is_verifikasi'];
                $data[$key]['is_dokter'] = $value['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS ? true : false;
                $data[$key]['is_icd_x'] = isset($value['is_icd_x']) ? $value['is_icd_x'] : true;
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

    // Get penatalaksanaan
    private function getPenatalaksanaan($data)
    {
        // Check if related cppt also ordered lab/rad/reseptur
        $hasLab = !empty($data['is_lab']) && $data['is_lab'] ? '* Pasien dilakukan pemeriksaan laboratorium <br/>' : '';
        $hasRad = !empty($data['is_rad']) && $data['is_rad'] ? '* Pasien dilakukan pemeriksaan radiologi <br/>' : '';
        $hasResep = !empty($data['is_reseptur']) && $data['is_reseptur'] ? '* Pasien diberikan resep <br/>' : '';

        // Deklarasi html
        $html = '<table border="0" cellpadding="0" cellspacing="0">';

        // Cek subjek
        if (isset($data['subject']) && $data['subject'] != '') {
            // normalize string
            $data['subject'] = str_replace('<br />'," ", $data['subject']);
            // Set html
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td><b>Subjektif:</b><br/><div style="white-space: pre-line">'. $data['subject'] .'</div></td>';
            $html .= '</tr>';
        }

        // Cek objek
        if (isset($data['object']) && $data['object'] != '') {
            // normalize string
            $data['object'] = str_replace('<br />'," ", $data['object']);
            // Set html
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td><b>Objektif:</b><br/><div style="white-space: pre-line">'.$data['object'].'</div></td>';
            $html .= '</tr>';
        }

        // Cek asesmen
        if (isset($data['a_diag_utama']) && $data['a_diag_utama'] != '') {
            $diag_utama = json_decode($data['a_diag_utama'],TRUE);
            // Set html
            $html .= '<tr style="line-height:130%;">';
            if($data['is_icd_x']){
                $html .= '<td><b>Asesmen Diagnosa Utama:</b><br/>'.wordwrap(@$diag_utama['text'],70,"\n") .'</td>';
            }
            else{
                $html .= '<td><b>Asesmen Diagnosa Utama:</b><br/><div style="white-space: pre-line">'.$diag_utama['text'].'</div></td>';
            }
            $html .= '</tr>';
        }

        // Cek asesmen
        if (isset($data['a_diag_penyerta']) && $data['a_diag_penyerta'] != '') {
            // Encode
            $diagnosaPenyerta = json_decode($data['a_diag_penyerta'],TRUE);

            // Cek diagnosa
            if ($diagnosaPenyerta != '') {
                // Inisialisasi counter
                $counter = 0;

                // Loop
                foreach ($diagnosaPenyerta as $valueDiagnosaPenyerta) {
                    // Cek counter
                    if ($counter == 0) {
                        // Set html
                        $html .= '<tr style="line-height:130%;">';
                        $html .= '<td><b>Asesmen Diagnosa Penyerta:</b><br/>- '.wordwrap(@$valueDiagnosaPenyerta['text'],70,"\n").'</td>';
                        $html .= '</tr>';
                    }
                    else {
                        // Set html
                        $html .= '<tr style="line-height:130%;">';
                        $html .= '<td>- '.wordwrap(@$valueDiagnosaPenyerta['text'],70,"\n").'</td>';
                        $html .= '</tr>';
                    }

                    // Plus the counter
                    $counter++;
                }
            }
            else {
                // Set strip
                $html .= '<td>-</td>';
            }

            // Close tag
            $html .= '</tr>';
        }
        if( (isset($data['instruksi']) && !empty($data['instruksi'])) && (isset($data['pegawai_instruksi']) && !empty($data['pegawai_instruksi'])) ){
            // normalize string
            $data['instruksi'] = str_replace('<br />'," ", $data['instruksi']);
            // Set html
            $html .= '<tr style="line-height:130%;">';
            //$html .= '<td>'.wordwrap(@$data['instruksi'],70,"\n").'<br> Verbal Order dari : '.wordwrap(@$data['pegawai_instruksi'],70,"\n").'</td>';
            $html .= '<td>Verbal Order dari : <br> '.wordwrap(@$data['pegawai_instruksi'],70,"\n").'</td>';
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

            $html .= '<br/>' . $hasLab . $hasRad . $hasResep;
            $html .= '</td></tr>';
        }

        if (isset($data['catatan_dokter']) && $data['catatan_dokter'] != '') {
            // normalize string
            $data['catatan_dokter'] = str_replace('<br />'," ", $data['catatan_dokter']);
            // Set html
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td><b>Catatan:</b><br/><div style="white-space: pre-line">'.$data['catatan_dokter'].'</div></td>';
            $html .= '</tr>';
        }

        // Set end tag html
        $html .= '</table>';

        // Return
        return $html;
    }

    private function getInstruksiDpjp($data_instruksi)
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
                'tgl_tindakan' => $d_instruksi['tipe_instruksi'] == 'BED_TINDAKAN' ? $d_instruksi['tgl_instruksi'] : $d_instruksi['tanggal_input'],
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
                if($nama_tipe == 'TINDAKANBMHP'){
                    $label_nama_tipe = 'Tindakan';
                }else if($nama_tipe == 'RESEPTUR'){
                    $label_nama_tipe = 'Obat';
                }else if($nama_tipe == 'PENUNJANG'){
                    $label_nama_tipe = 'Penunjang';
                }else{
                    $label_nama_tipe = 'Tindakan';
                }
                if($data_ins['instruksi_deleted'] == true){
                    $html .= '<tr class="strikeout"><td><table>';
                }else{
                    $html .= '<tr><td><table>';
                }
                    $html .= '<tr><td><b>'.@$label_nama_tipe.'</b></td>';

                $array_status_implemented = [];
                $array_status_penunjang_batal = [];
                foreach ($data_ins['list_tindakan'] as $ins_tindakan) {
                    if($ins_tindakan['tindakan_deleted'] != true){
                        $array_status_implemented[] = $ins_tindakan['is_telah_implementasi'];
                        if($nama_tipe == 'PENUNJANG'){
                            $array_status_penunjang_batal[] = $ins_tindakan['status_implementasi'];
                        }
                    }
                }
                $instruksi_implemented = false;
                if(count($array_status_implemented)>0){
                    if(count(array_unique($array_status_implemented)) === 1){
                        if(current($array_status_implemented) == true){
                            $instruksi_implemented = true;
                        }
                    }
                }
                $is_penunjang_batal = false;
                $is_penunjang_ditolak = false;
                if(count($array_status_penunjang_batal)>0){
                    if(in_array('472', $array_status_penunjang_batal)){
                        $is_penunjang_batal = true;
                    }
                    if(in_array('541', $array_status_penunjang_batal)){
                        $is_penunjang_ditolak = true;
                    }
                }
                if($data_ins['instruksi_deleted'] != true && $this->_pegawai_id == $data_ins['cpptpegawai_id'] && $data_ins['is_verifikasi_dpjp'] != true && $instruksi_implemented != true){
                    if($nama_tipe == 'PENUNJANG' && $is_penunjang_batal == true){
                        $html .= '<td><span class="label label-danger">Dibatalkan</span></td>';
                    }elseif($nama_tipe == 'PENUNJANG' && $is_penunjang_ditolak == true){
                        $html .= '<td><span class="label label-danger">Ditolak</span></td>';
                    }else{
                        $html .= '<td>'.Html::button('<b><i class="fa fa-pencil"></i></b>', [
                            'class' => 'btn btn-info btn-ubah-terapi btn-link',
                            'data-cpptid' => DocoHelpers::encrypt($data_ins['cppt_id']),
                            'data-instruksiid' => DocoHelpers::encrypt($data_ins['instruksi_id']),
                            'data-jnsinstruksi' => @$nama_tipe
                        ]).'</td>';
                        $html .= '<td>'.Html::button('<b><i class="fa fa-trash"></i></b>', [
                            'class' => 'btn btn-info btn-hapus-terapi btn-link',
                            'data-cpptid' => DocoHelpers::encrypt($data_ins['cppt_id']),
                            'data-instruksiid' => DocoHelpers::encrypt($data_ins['instruksi_id']),
                            'data-tipeinstruksi' => @$nama_tipe,
                            'action' => '/igd/pemeriksaan-igd/hapus-terapi?id='.DocoHelpers::encrypt($this->_pendaftaran_id).'&cppt_id='.DocoHelpers::encrypt($data_ins['cppt_id']).'&instruksi_id='.DocoHelpers::encrypt($data_ins['instruksi_id']).'&tipeinstruksi='.@$nama_tipe
                        ]).'</td>';
                    }
                }else{
                    if($nama_tipe == 'PENUNJANG' && $is_penunjang_batal == true){
                        $html .= '<td><span class="label label-danger">Dibatalkan</span></td>';
                    }elseif($nama_tipe == 'PENUNJANG' && $is_penunjang_ditolak == true){
                        $html .= '<td><span class="label label-danger">Ditolak</span></td>';
                    }
                }
                    $html .= '</tr>';
                    if($nama_tipe == 'PENUNJANG'){
                        if(substr($data_ins['tipe_instruksi'],0,3) == 'LAB'){
                            $label_instalasi = 'Laboratorium';
                        }else if(substr($data_ins['tipe_instruksi'],0,3) == 'RAD'){
                            $label_instalasi = 'Radiologi';
                        }else if(substr($data_ins['tipe_instruksi'],0,3) == 'BED'){
                            $label_instalasi = 'Bedah Sentral';
                        }else{
                            $label_instalasi = $data_ins['tipe_instruksi'];
                        }
                        $html .= '<tr><td>';
                        $html .= @$label_instalasi.' - ';
                        $html .= @$data_ins['ruangan_pertindakan'];
                        $html .= '<td></tr>';
                    }
                    $html .= '<tr><td>'.@$data_ins['catatan_instruksi'].'</td></tr>';
                        $html .= '<tr><td><table>';
                    $groupTglTindakan = [];
                    foreach ($data_ins['list_tindakan'] as $ins_tindakan) {
                        $groupTglTindakan[$ins_tindakan['tipe_instruksi']][$ins_tindakan['tgl_tindakan']][] = $ins_tindakan;
                    }
                    foreach ($groupTglTindakan as $tipe_instruksi => $group_tgl) {
                        $html .= '<tr>';
                        if($tipe_instruksi == 'LAB_TINDAKAN'){
                            $label_tipe_instruksi = 'Tindakan Laboratorium';
                        }else if($tipe_instruksi == 'RAD_TINDAKAN'){
                            $label_tipe_instruksi = 'Tindakan Radiologi';
                        }else if($tipe_instruksi == 'LAB_PAKET'){
                            $label_tipe_instruksi = 'Paket Laboratorium';
                        }else if($tipe_instruksi == 'RAD_PAKET'){
                            $label_tipe_instruksi = 'Paket Radiologi';
                        }else if($tipe_instruksi == 'BED_TINDAKAN'){
                            $label_tipe_instruksi = 'Tindakan Bedah Sentral';
                        }else{
                            $label_tipe_instruksi = $tipe_instruksi;
                        }
                        $html .= '<td><b>'.@$label_tipe_instruksi.'</b></td></tr>';
                        foreach ($group_tgl as $tgl_ins => $ins_tgltindakan) {
                            $hitungTgl = 0;
                            $hitungInsTindakan = count($ins_tgltindakan);
                            foreach ($ins_tgltindakan as $row_tgltindakan) {
                                $html .= '<tr>';
                                if($hitungTgl == 0){
                                    // if($hitungInsTindakan == 1 && $row_tgltindakan['tindakan_deleted'] == true){$html .= '<td rowspan="'.@$hitungInsTindakan.'"><strike>'.@$row_tgltindakan['tgl_tindakan'].'</strike></td>';
                                    // }else{
                                    //     $html .= '<td rowspan="'.@$hitungInsTindakan.'">'.@$row_tgltindakan['tgl_tindakan'].'</td>';
                                    // }
                                    if($hitungInsTindakan == 1 && $row_tgltindakan['tindakan_deleted'] == true){$html .= '<tr><td><b><strike>'.@date('d-m-Y H:i:s', strtotime($row_tgltindakan['tgl_tindakan'])).'</strike></b></td></tr>';
                                    }else{
                                        $html .= '<tr><td><b>'.@date('d-m-Y H:i:s', strtotime($row_tgltindakan['tgl_tindakan'])).'</b></td></tr>';
                                    }
                                }
                                // $html .= '<td>&nbsp;</td>';
                                if($row_tgltindakan['tindakan_deleted'] == true || $row_tgltindakan['instruksi_deleted'] == true){
                                    $html .= '<td><strike>';
                                    $html .= @$row_tgltindakan['instruksi'];
                                    if(substr($tipe_instruksi, -3) == 'KET'){
                                        $dftr_paket = json_decode($row_tgltindakan['daftar_paket'],true);
                                        if(count($dftr_paket)>1){
                                            $html .= '<ul>';
                                            foreach ($dftr_paket as $paket) {
                                                $html .= '<li>'.$paket.'</li>';
                                            }
                                            $html .= '</ul>';
                                        }
                                    }
                                    $html .= '</strike></td>';
                                }else{
                                    $html .= '<td>';
                                    $html .= @$row_tgltindakan['instruksi'];
                                    if(substr($tipe_instruksi, -3) == 'KET'){
                                        $dftr_paket = json_decode($row_tgltindakan['daftar_paket'],true);
                                        if(count($dftr_paket)>1){
                                            $html .= '<ul>';
                                            foreach ($dftr_paket as $paket) {
                                                $html .= '<li>'.$paket.'</li>';
                                            }
                                            $html .= '</ul>';
                                        }
                                    }
                                    $html .= '</td>';
                                }
                                $html .= '</tr>';
                                $hitungTgl ++;
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

    private function getVerifikasi($data,$data_instruksi)
    {
        $pegawai_id = $this->_pegawai_id;
        $dpjp = $this->_data_pasien['dokter_jaga_id'];
        $pemberi_instruksi = isset($data['pemberi_instruksi_id']) ? $data['pemberi_instruksi_id'] : null;
        $pegawaiInput = isset($data['pegawai_id']) ? $data['pegawai_id'] : null;
        $pulangId = isset($data['pasienpulang_id']) ? $data['pasienpulang_id'] : null;
        $html = '';

        $instruksi_implemented= false;
        $array_status = [];
        foreach ($data_instruksi as $instruksi) {
            $array_status[] = $instruksi['is_telah_implementasi'];
        }
        if(count($array_status)>0){
            if(count(array_unique($array_status)) === 1){
                if(current($array_status) == true){
                    $instruksi_implemented = true;
                }
            }
        }
        // cek verifikasi verbal order
        if (!$data['is_deleted']) {
             if ($data['instruksi']) {
              
                if ($data['is_verifikasi_verbal']) {
                    $html .= '<span>'.$data['pegawai_verifikasi_verbal'].' '.date('d/m/Y H:i:s', strtotime($data['tgl_verif_verbal'])).'</span>';
                } else {

                    if ($pegawai_id == $pemberi_instruksi && $this->_user_identity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS) {
                        
                        $html .= Html::button('<b><i class="fa fa-check-square"></i></b>'.Yii::t('fe', 'Verifikasi verbal order'), [
                            'class' => 'btn btn-info btn-labeled btn-xs btn-verifikasi-verbal',
                            'style' => 'margin-top:10px',
                            'data-id' => $data['cppt_id'],
                            'data-cpptid' => DocoHelpers::encrypt($data['cppt_id']),
                            'data-confirm-message' => Yii::t('fe', 'Apakah anda yakin akan memverifikasi verbal order tersebut?'),
                        ]);
                    }else if($data['is_verbal_order'] && !$data['is_verifikasi_verbal'] && ($pegawai_id == $pegawaiInput || $pegawai_id == $dpjp)){
                        $html .= Html::button('<b><i class="fa fa-trash"></i></b>'.Yii::t('fe', 'Delete SOAP'), [
                                    'class' => 'btn btn-danger btn-labeled btn-xs btn-delete-verbal',
                                    'style' => 'margin-top:10px',
                                    'data-cpptid' => DocoHelpers::encrypt($data['origin_cppt_id']),
                                    'data-confirm-message' => Yii::t('fe', 'Apakah anda yakin akan menghapus verbal order tersebut?'),
                                ]);   
                    }
                }
                $html .= '<br>';
            }

            // cek verifikasi dpjp
            if ($data['is_verifikasi']) {
                $html .= '<span>'.$data['pegawai_verifikasi'].' '.date('d/m/Y H:i:s', strtotime($data['tgl_verifikasi'])).'</span>';
                $html .= '<br>';
                $html .= Html::button('<b><i class="fa fa-pencil"></i></b>'.Yii::t('fe', 'Edit SOAP'), [
                    'class' => 'btn btn-info btn-labeled btn-xs',
                    'style' => 'margin-top:10px',
                    'disabled' => true
                ]);
                $html .= '<br>';
            } else {
                if ($pegawai_id == $dpjp && $this->_user_identity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS && empty($pemberi_instruksi)) {
                    $html .= Html::button('<b><i class="fa fa-check-square"></i></b>'.Yii::t('fe', 'Verifikasi DPJP'), [
                        'class' => 'btn btn-info btn-labeled btn-xs btn-verifikasi-dpjp',
                        'style' => 'margin-top:10px',
                        'data-id' => $data['cppt_id'],
                        'data-cpptid' => DocoHelpers::encrypt($data['origin_cppt_id']),
                        'data-confirm-message' => Yii::t('fe', 'Apakah anda yakin akan memverifikasi CPPT tersebut?'),
                    ]);
                    $html .= '<br>';
                }

                if (empty($pulangId) && $pegawai_id == $pegawaiInput && empty($pemberi_instruksi)) {
                    $html .= Html::button('<b><i class="fa fa-pencil"></i></b>'.Yii::t('fe', 'Edit SOAP'), [
                        'class' => 'btn btn-info btn-labeled btn-xs btn-edit-soap',
                        'style' => 'margin-top:10px',
                        'data-cpptid' => DocoHelpers::encrypt($data['origin_cppt_id']),
                        'data-is_icd_x' => isset($data['is_icd_x']) ? $data['is_icd_x'] : true,
                    ]);
                    $html .= Html::button('<b><i class="fa fa-copy"></i></b>'.Yii::t('fe', 'Copy'), [
                        'class' => 'btn btn-info btn-labeled btn-xs btn-copy-soap',
                        'style' => 'margin-top:10px',
                        'data-cpptid' => DocoHelpers::encrypt($data['origin_cppt_id']),
                        'data-is_icd_x' => isset($data['is_icd_x']) ? $data['is_icd_x'] : true,
                    ]);
                    $html .= Html::button('<b><i class="fa fa-times"></i></b>'. Yii::t('fe', 'Batal Edit SOAP'), [
                        'class' => 'btn btn-danger btn-labeled btn-xs btn-cancel-edit-soap hidden',
                        'style' => 'margin-top:10px',
                        'data-is_icd_x' => isset($data['is_icd_x']) ? $data['is_icd_x'] : true,
                    ]);
                    $html .= '<br>';
                }
            }
            
            if($pegawai_id == $pegawaiInput && !$data['is_verifikasi']) {
                $html .= Html::button('<b><i class="fa fa-trash"></i></b>' . Yii::t('fe', 'Hapus SOAP'), [
                    'class' => $data['tipe'] != 'RD' ? 'hidden' : 'btn btn-info btn-labeled btn-xs btn-delete-cppt',
                    'data-cpptid' => DocoHelpers::encrypt($data['origin_cppt_id']),
                    'data-tipe' => $data['tipe']
                ]);
            }

            // cek verifikasi untuk button tambah
            // jika telah verifikasi dpjp button tambah terapi tidak muncul
            // Hide Button Tambah Terapi (Untuk menampilan kembali hapus komentar ini)
            // if(($data['is_verifikasi'] != true && $data['pegawai_verifikasi'] == null && $this->_user_identity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS) || $this->_user_identity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN){
            //     $html .= Html::button('<b><i class="fa fa-plus"></i></b>'.Yii::t('fe', 'Tambah Terapi'), [
            //         'class' => 'btn btn-info btn-labeled btn-xs btn-tambah-terapi',
            //         'style' => 'margin-top:10px',
            //         'data-id' => $data['cppt_id'],
            //         'data-cpptid' => DocoHelpers::encrypt($data['cppt_id']),
            //         'disabled' => $this->_pegawai_id == $data['pegawai_id'] || $this->_user_identity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN ? false : true,
            //     ]);
            // }
            // $html .= '<br>';

            // $html .= Html::button('<b><i class="fa fa-flask"></i></b>'.Yii::t('fe', 'Laboratorium'), [
            //     'class' => 'btn btn-info btn-labeled btn-xs',
            //     'style' => 'margin-top:10px'
            // ]);
            // $html .= '<br>';
            // $html .= Html::button('<b><i class="fa fa-plus"></i></b>'.Yii::t('fe', 'Radiologi'), [
            //     'class' => 'btn btn-info btn-labeled btn-xs',
            //     'style' => 'margin-top:10px'
            // ]);
            // $html .= '<br>';
            // Hide Button Tambah Terapi (Untuk menampilan kembali hapus komentar ini)
        }else{
            $tgl_edit = isset($data['created_date']) ? date('d/m/Y / H:i:s', strtotime($data['created_date'])) : '';
            $html .= '<p> Data sudah di ubah oleh <br>'.$data['pegawai_update_nama'].' - <br>'.$tgl_edit.'</p>';
        }
        return $html;
    }

    public function actionTambahInstruksi()
    {
        try{
            $params = Yii::$app->request;
            $encryptedPendaftaranId = $params->get('id','MA');
            $pendaftaran_id = DocoHelpers::decrypt($encryptedPendaftaranId);
            $cppt_id = DocoHelpers::decrypt($params->get('cppt_id','MA'));
            $jns_instruksi = $params->get('jns_instruksi','');
            $sourceTab =  $params->get('from','asesmen-dpjp');
            $pasien_id_now = $params->get('pasien_id_now','MA');
            $ruangan_now = $params->get('ruangan_now','MA');

            $request = $this->_restIgd->get('asesmen-dpjp/ambil-data-instruksi');
            $response = json_decode($request->getBody(), true);
            $list_jenis_instruksi = $response['response']['list_jenis_instruksi'];
            $modelInstruksi = new InstruksiForm;
            $modelInstruksi->cppt_id = $cppt_id;

            $userIdentity = Yii::$app->session->get('user_identity');
            $listJenisInstruksi = [];
            if (!empty($list_jenis_instruksi)) {
                foreach ($list_jenis_instruksi as $value) {
                    // if(isset($userIdentity['kelompokpegawai_id']) && $userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN) {
                    //     if ($value['lookup_name'] == 'TINDAKAN') {
                    //         $listJenisInstruksi[] = $value;
                    //     }
                    // }
                    // else {
                    // }
                    $listJenisInstruksi[] = $value;
                }
            }

            return $this->renderAjax('asesmen-dpjp/transaksi_terapi',[
                'cppt_id'=>$cppt_id,
                'modelInstruksi' => $modelInstruksi,
                'list_jenis_instruksi' => $listJenisInstruksi,
                'jns_instruksi' => $jns_instruksi,
                'sourceTab' => $sourceTab,
                'pasien_id_now' => $pasien_id_now,
                'ruangan_now' => $ruangan_now
            ]);
        } catch(RequestException $e){
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch(\Exception $e){
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    public function actionUbahInstruksi()
    {
        try{
            $session = Yii::$app->session;
            $params = Yii::$app->request;
            $encryptedPendaftaranId = $params->get('id','MA');
            $pendaftaran_id = DocoHelpers::decrypt($encryptedPendaftaranId);
            $cppt_id = DocoHelpers::decrypt($params->get('cppt_id','MA'));
            $instruksi_id = DocoHelpers::decrypt($params->get('instruksi_id','MA'));
            $jns_instruksi = $params->get('jns_instruksi','');
            $sourceTab =  $params->get('from', 'asesmen-dpjp');
            $userIdentity = Yii::$app->session->get('user_identity');
            $modelInstruksi = new InstruksiForm;
            $request = $this->_restIgd->get('asesmen-dpjp/ambil-data-instruksi',[
                'query' => [
                    'pendaftaran_id' => $pendaftaran_id,
                    'instruksi_id' => $instruksi_id,
                    'cppt_id' => $cppt_id
                ]
            ]);
            $cpptId = $params->get('cppt_id','MA');

            $sess[$encryptedPendaftaranId.$cpptId] = [];

            if(isset($session['pemeriksaan_reseptur'])) {
                $session->set('pemeriksaan_reseptur', $sess);
            }

            if(isset($session['pemeriksaan_reseptur_db'])) {
                $session->set('pemeriksaan_reseptur_db', $sess);
            }


            $response = json_decode($request->getBody(), true);
            $list_jenis_instruksi = $response['response']['list_jenis_instruksi'];
            $listJenisInstruksi = [];
            if (!empty($list_jenis_instruksi)) {
                foreach ($list_jenis_instruksi as $value) {
                    if(isset($userIdentity['kelompokpegawai_id']) && $userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN) {
                        if ($value['lookup_name'] == 'TINDAKAN') {
                            $listJenisInstruksi[] = $value;
                        }
                    }
                    else {
                        $listJenisInstruksi[] = $value;
                    }
                }
            }
            if(isset($response['response']['data_instruksi'])){
                $data_instruksi = $response['response']['data_instruksi'];
                $modelInstruksi->instruksi_id = isset($data_instruksi['instruksi_id'])?$data_instruksi['instruksi_id']:null;
                $modelInstruksi->catatan_instruksi = isset($data_instruksi['catatan_instruksi'])?$data_instruksi['catatan_instruksi']:null;
                $modelInstruksi->jenis_instruksi = isset($data_instruksi['jenis_instruksi'])?$data_instruksi['jenis_instruksi']:null;
            }
            $modelInstruksi->cppt_id = $cppt_id;
            return $this->renderAjax('asesmen-dpjp/transaksi_terapi',[
                'cppt_id'=>$cppt_id,
                'instruksi_id' => $instruksi_id,
                'modelInstruksi' => $modelInstruksi,
                'list_jenis_instruksi' => $listJenisInstruksi,
                'jns_instruksi' => $jns_instruksi,
                'sourceTab' => $sourceTab
            ]);
        } catch(RequestException $e){
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch(\Exception $e){
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    public function actionContentTerapi()
    {
        $params = Yii::$app->request;
        $session = Yii::$app->session;
        $encryptedPendaftaranId = $params->get('id',0);
        $pendaftaran_id = DocoHelpers::decrypt($encryptedPendaftaranId);
        $jenisterapi_id = $params->get('jenisterapi_id',0);
        $cppt_id = $params->get('cppt_id',0);
        $encryptedCpptId = DocoHelpers::encrypt($cppt_id);
        $instruksi_id = $params->get('instruksi_id',0);
        $isUbah = $params->get('isUbah',0);

        $ruangan_id = $this->_ruangan_id;
        $kelaspelayanan_id = $this->_data_pasien['kelaspelayanan_id'];
        $penjamin_id = $this->_data_pasien['penjamin_id'];

        $kelompokpegawai_id = $this->_user_identity['kelompokpegawai_id'];

        $contentView = 'error';
        $paramData = [];
        switch ($jenisterapi_id) {
            case 457:
                $paramData = $this->getModelDataTindakanbmhp(
                    $pendaftaran_id,
                    $ruangan_id,
                    $kelaspelayanan_id,
                    $penjamin_id,
                    $instruksi_id,
                    $cppt_id,
                    $isUbah
                );
                $paramData['encryptedPendaftaranId']=$encryptedPendaftaranId;
                $paramData['cppt_id']=$cppt_id;
                $paramData['isUbah']=$isUbah;
                $paramData['kelompokpegawai_id']=$kelompokpegawai_id;
                $contentView = 'terapi/terapi_tindakan';
                break;
            case 458:
                $session->remove('obat_dihapus');
                $session->remove('pemeriksaan_reseptur_db');
                $session->remove('pemeriksaan_reseptur');
                $paramData = $this->resetResepturSession($encryptedPendaftaranId,$encryptedCpptId,$instruksi_id);
                $paramData = $this->getModelDataReseptur($pendaftaran_id,$ruangan_id,$kelaspelayanan_id,$penjamin_id,$instruksi_id,$cppt_id,$isUbah);
                $paramData['encryptedPendaftaranId']=$encryptedPendaftaranId;
                $paramData['cppt_id']=$cppt_id;
                $paramData['isUbah']=$isUbah;
                $paramData['isEditReseptur']=$isUbah;
                $paramData['instruksi_id']=$instruksi_id;
                $contentView = 'terapi/terapi_reseptur';
                break;
            case 459:
                $paramData = $this->getModelDataPenunjang($pendaftaran_id,$ruangan_id,$kelaspelayanan_id,$penjamin_id,$instruksi_id,$cppt_id,$isUbah);
                $paramData['encryptedPendaftaranId']=$encryptedPendaftaranId;
                $paramData['cppt_id']=$cppt_id;
                $paramData['isUbah']=$isUbah;
                $contentView = 'terapi/terapi_penunjang';
                break;
        }

        return $this->renderAjax('asesmen-dpjp/'.$contentView,$paramData);
    }

    public function getModelDataTindakanbmhp($pendaftaran_id,$ruangan_id,$kelaspelayanan_id,$penjamin_id,$instruksi_id,$cppt_id,$isUbah)
    {
        $modelInstruksiTindakan = new InstruksiTindakanForm;
        $modelInstruksiBmhp = new InstruksiTindakanBmhpForm;

        $request = $this->_restIgd->get('asesmen-dpjp/bundle-data-tindakanbmhp',[
            'query' => [
                'pendaftaran_id' => $pendaftaran_id,
                'ruangan_id' => $ruangan_id,
                'kelaspelayanan_id' => $kelaspelayanan_id,
                'penjamin_id' => $penjamin_id,
                'instruksi_id' => $instruksi_id,
                'cppt_id' => $cppt_id
            ]
        ]);
        $response = json_decode($request->getBody(), true);
        $data_tindakanruangan = isset($response['response']['data_tindakanruangan']) ? $response['response']['data_tindakanruangan'] : [];
        $data_paketruangan = isset($response['response']['data_paketruangan']) ? $response['response']['data_paketruangan'] : [];
        $data_dokter = isset($response['response']['data_dokter']) ? $response['response']['data_dokter'] : [];
        $data_perawat = isset($response['response']['data_perawat']) ? $response['response']['data_perawat'] : [];
        $data_jenispemakaian = isset($response['response']['data_jenispemakaian']) ? $response['response']['data_jenispemakaian'] : [];
        $list_farmasi = isset($response['response']['list_farmasi']) ? $response['response']['list_farmasi'] : [];
        $default_farmasi = isset($response['response']['default_farmasi']) ? $response['response']['default_farmasi'] : [];
        $workSpace = Yii::$app->session->get('active_workspace');
        $list_farmasi[$workSpace['ruangan_id']] = $workSpace['ruangan_name'];
        $session = Yii::$app->session;

        $session->set('tindakan_paket', [
            'tindakan' => $data_tindakanruangan,
            'paket' => $data_paketruangan,
        ]);


        $data_tindakanbmhp = [];
        if(isset($response['response']['data_tindakanbmhp'])){
            $data_tindakanbmhp = $response['response']['data_tindakanbmhp'];
        }

        if(!empty($instruksi_id) && $instruksi_id != 0){
            $modelInstruksiTindakan->instruksi_id = $instruksi_id;
        }
        $userIdentity = Yii::$app->session->get('user_identity');
        if(isset($userIdentity['kelompokpegawai_id'])) {
            if ($userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS) {
                $modelInstruksiTindakan->dokterdpjp_id = $userIdentity['id_pegawai'];
            } else if ($userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN) {
                $modelInstruksiTindakan->perawat1_id = $userIdentity['id_pegawai'];
                $modelInstruksiBmhp->perawat1_id = $userIdentity['id_pegawai'];
            }
        }
        return [
            'modelInstruksiTindakan' => $modelInstruksiTindakan,
            'modelInstruksiBmhp' => $modelInstruksiBmhp,
            'data_tindakanruangan' => $data_tindakanruangan,
            'data_dokter' => $data_dokter,
            'data_perawat' => $data_perawat,
            'id_ruangan' => $this->_ruangan_id,
            'id_instalasi' => $this->_data_pasien['instalasi_id'],
            'data_pasien' => $this->_data_pasien,
            'data_tindakanbmhp' => $data_tindakanbmhp,
            'data_jenispemakaian' => $data_jenispemakaian,
            'data_paketruangan' => $data_paketruangan,
            'list_farmasi' => $list_farmasi,
            'default_farmasi' => $default_farmasi,
        ];
    }

    public function getModelDataReseptur($pendaftaran_id,$ruangan_id,$kelaspelayanan_id,$penjamin_id,$instruksi_id,$cppt_id,$isUbah)
    {
        $modelReseptur = new ResepturForm;
        $modelResepturDetailRacikan = new ResepturDetailForm;
        $modelResepturDetailNonRacikan = new ResepturNrDetailForm;
        $modelResepturDetailRacikan->scenario = ResepturDetailForm::SCENARIO_SESSION;
        $modelResepturDetailNonRacikan->scenario = ResepturNrDetailForm::SCENARIO_SESSION;

        $diagnosa_nama = '';
        if ($isUbah == '1') {
            $request = $this->_restIgd->get('asesmen-dpjp/bundle-data-reseptur',[
                'query' => [
                    'pendaftaran_id'=>$pendaftaran_id,
                    'ruangan_id' => $ruangan_id,
                    'kelaspelayanan_id' => $kelaspelayanan_id,
                    'penjamin_id' => $penjamin_id,
                    'cppt_id' => $cppt_id,
                    'pegawai_id' => $this->_pegawai_id,
                    'instruksi_id' => $instruksi_id
                ]
            ]);
            $response = json_decode($request->getBody(), true);
            $listDataApotek = ArrayHelper::map($response['response']['listDataApotek'], 'ruangan_id', 'ruangan_nama');
            $listDataSigna = ArrayHelper::map($response['response']['listDataSigna'], 'signa_id', 'signa_nama');
            $dataCppt = $response['response']['dataCppt'];
            $isEditReseptur = true;
            $initObatAlkes = [];

            if (isset($response['response']['pegawai']['pegawai_id']) && $response['response']['pegawai']['pegawai_id'] != '') {
                $pegawai = $response['response']['pegawai'];
            } else {
                $pegawai = [];
            }

            $modelResepturDetailRacikan->cppt_id = $cppt_id;
            $modelResepturDetailNonRacikan->cppt_id = $cppt_id;

            $session = Yii::$app->session;
            $session_reseptur = [];
            $session_key = 1;
            $count_data_insert = 0;

            $pendaftaran_id_encrypted = DocoHelpers::encrypt($this->_data_pasien['pendaftaran_id']);
            $cppt_id_encrypted = DocoHelpers::encrypt($cppt_id);
            $pendaftaran_id = $this->_data_pasien['pendaftaran_id'];
            $data_instruksi = isset($response['response']['instruksi']) ? $response['response']['instruksi'] : [];
            $data_reseptur = isset($response['response']['reseptur']) ? $response['response']['reseptur'] : [];
            $data_resepturdetail = isset($response['response']['resepturDetail']) ? $response['response']['resepturDetail'] : [];
            $data_obatalkes = isset($response['response']['obatalkes']) ? $response['response']['obatalkes'] : [];

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
                $modelReseptur->berat_badan = str_replace(".", ",", $data_reseptur['berat_badan']);
                $modelReseptur->tinggi_badan = str_replace(".", ",", $data_reseptur['tinggi_badan']);
                $modelReseptur->luas_tubuh = str_replace(".", ",", $data_reseptur['luas_tubuh']);
                $modelReseptur->diagnosa_id = $data_reseptur['diagnosa_id'];
                $modelReseptur->is_hamil = $data_reseptur['is_hamil'] == true ? 1 : 0;
                $modelReseptur->instruksi_id = $data_reseptur['instruksi_id'];
                // $diagnosa_nama = $data_reseptur['diagnosa_nama'];
                if (isset($dataCppt['a_diag_utama']) && $dataCppt['a_diag_utama'] != '') {
                    $diagnosa_nama = $dataCppt['a_diag_utama']['text'];
                }
            } else {
                $modelReseptur->is_hamil = 0;
                $diagnosa_nama = '';
            }

            // if (!empty($data_resepturdetail)) {
            //     foreach ($data_resepturdetail as $value) {
            //         $session_reseptur[$pendaftaran_id_encrypted.$cppt_id_encrypted][] = [
            //             'session_key' => $session_key,
            //             'resepturdetail_id' => $value['resepturdetail_id'],
            //             'jenis_racikan' => !empty($value['rke']) ? ResepturDetailForm::VC_RC : ResepturDetailForm::VC_NRC,
            //             'nama_racikan' => $value['racikan_nama'],
            //             'rke' => $value['rke'],
            //             'signa_reseptur' => $value['signa_id'],
            //             'obatalkes_id' => $value['obatalkes_id'],
            //             'qty_reseptur' => $value['qty_reseptur'],
            //             'satuankecil_id' => $value['satuankecil_id'],
            //             'satuankecil_nama' => $value['satuan_kecil'],
            //             'hargasatuan_reseptur' => $value['hargajual_satuan'],
            //         ];

            //         $modelReseptur->iter = $value['iter'];
            //         $session_key++;
            //     }
            // }

            // $session->set('pemeriksaan_reseptur', $session_reseptur);
        } else {
            $request = $this->_restIgd->get('asesmen-dpjp/bundle-data-reseptur',[
                'query' => [
                    'pendaftaran_id'=>$pendaftaran_id,
                    'ruangan_id' => $ruangan_id,
                    'kelaspelayanan_id' => $kelaspelayanan_id,
                    'penjamin_id' => $penjamin_id,
                    'cppt_id' => $cppt_id,
                    'pegawai_id' => $this->_pegawai_id
                ]
            ]);
            $response = json_decode($request->getBody(), true);
            $listDataApotek = ArrayHelper::map($response['response']['listDataApotek'], 'ruangan_id', 'ruangan_nama');
            $listDataSigna = ArrayHelper::map($response['response']['listDataSigna'], 'signa_id', 'signa_nama');
            $dataCppt = $response['response']['dataCppt'];
            $isEditReseptur = false;
            $initObatAlkes = [];

            if (isset($dataCppt['a_diag_utama']) && $dataCppt['a_diag_utama'] != '') {
                // $diagnosa = json_decode($dataCppt['a_diag_utama']);
                $diagnosa = $dataCppt['a_diag_utama'];
                // $modelReseptur->diagnosa_id = $diagnosa->id;
                if (isset($diagnosa['id'])) {
                    $modelReseptur->diagnosa_id = $diagnosa['id'];
                }else{
                    $modelReseptur->diagnosa_id = '';
                }
                // $diagnosa_nama = $diagnosa->text;
                $diagnosa_nama = $diagnosa['text'];
            }

            if (isset($response['response']['pegawai']['pegawai_id']) && $response['response']['pegawai']['pegawai_id'] != '') {
                $pegawai = $response['response']['pegawai'];
                $modelReseptur->pegawai_id = $pegawai['pegawai_id'];
            } else {
                $pegawai = [];
            }

            if (isset($response['response']['lastReseptur']['reseptur_id']) && $response['response']['lastReseptur']['reseptur_id'] != '') {
                $lastReseptur = $response['response']['lastReseptur'];
                $modelReseptur->is_hamil = $lastReseptur['is_hamil'] == true ? 1 : 0;
                $modelReseptur->berat_badan = str_replace(".", ",", $lastReseptur['berat_badan']);
                $modelReseptur->tinggi_badan = str_replace(".", ",", $lastReseptur['tinggi_badan']);
                $modelReseptur->luas_tubuh = str_replace(".", ",", $lastReseptur['luas_tubuh']);
            } else {
                $modelReseptur->is_hamil = 0;
            }

            if ($this->_instalasi_id == DocoConstants::INSTALASI_ID_RD) {
                $modelReseptur->ruangan_id = DocoConstants::DEPO_APOTEK_RD;
            }

            $modelReseptur->pasien_id = $this->_data_pasien['pasien_id'];
            $modelReseptur->pendaftaran_id = $this->_data_pasien['pendaftaran_id'];
            $modelReseptur->tglreseptur = date('d F, Y', strtotime("NOW"));

            $modelResepturDetailRacikan->cppt_id = $cppt_id;
            $modelResepturDetailNonRacikan->cppt_id = $cppt_id;
        }

        return [
            'modelReseptur' => $modelReseptur,
            'modelResepturDetailRacikan' => $modelResepturDetailRacikan,
            'modelResepturDetailNonRacikan' => $modelResepturDetailNonRacikan,
            'listDataSigna' => $listDataSigna,
            'listDataApotek' => $listDataApotek,
            'isEditReseptur' => $isEditReseptur,
            'data_pasien' => $this->_data_pasien,
            'initObatAlkes' => $initObatAlkes,
            'pegawai' => $pegawai,
            'diagnosa_nama' => $diagnosa_nama,
        ];
    }

    public function getModelDataPenunjang($cppt_id, $isUbah)
    {
        $model = new InstruksiPenunjangForm;
        $model->tgl_kirimpasien = date('d F Y');
        $data_penunjang = [];

        $request = $this->_restIgd->get('asesmen-dpjp/bundle-data-penunjang',[
            // 'query' => [
            //     'pendaftaran_id'=>$pendaftaran_id,
            //     'ruangan_id' => $ruangan_id,
            //     'kelaspelayanan_id' => $kelaspelayanan_id,
            //     'penjamin_id' => $penjamin_id,
            //     'instruksi_id' => $instruksi_id,
            //     'cppt_id' => $cppt_id
            // ]
        ]);
        $response = json_decode($request->getBody(), true);
        $response = $response['response'];
        $listInstalasiPenunjang = ArrayHelper::map($response['listInstalasiPenunjang'], 'instalasi_id', 'instalasi_nama');

        return [
            'modelPenunjang' => $model,
            'data_pasien' => $this->_data_pasien,
            'pegawai' => $this->_data_pegawai,
            'listInstalasiPenunjang' => $listInstalasiPenunjang,
            'cppt_id' => $cppt_id,
            // 'modelInstruksi' => $modelInstruksi,
            'data_penunjang' => $data_penunjang,
            'isUbah' => $isUbah
        ];
    }

    public function actionDpjpCreateTerapiTindakan()
    {
        $request = Yii::$app->request;
        // Assign
        $post = $request->post();
        $instruksi = $request->post('InstruksiForm');
        $tindakan = $request->post('InstruksiTindakanForm');
        $bmhp = $request->post('InstruksiTindakanBmhpForm');
        $listTindakan = $listBhp = [];

        $userIdentity = $this->_user_identity;
        $perawat_cppt = null;
        if(isset($userIdentity['kelompokpegawai_id']) && $userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN){
            $perawat_cppt = $userIdentity['id_pegawai'];
        }

        foreach ($tindakan as $value) {
            if (is_array($value)) $listTindakan[] = $value;
        }

        foreach ($bmhp as $value) {
            if (is_array($value)) $listBhp[] = $value;
        }

        $form_params = [
            'pendaftaran_id' => $request->post('pendaftaran_id'),
            'depo_id' => $request->post('depo_id'),
            'instruksi' => $request->post('InstruksiForm'),
            'bmhp' => $listBhp,
            'tindakan' => $listTindakan,
            'pasien_id' => $request->post('pasien_id_now'),
            'ruangan' => $request->post('ruangan_now'),
            'perawat_cppt' => $perawat_cppt
        ];

        $response = $this->_restIgd->post('asesmen-dpjp/dpjp-create-terapi-tindakan', [
            'form_params' => $form_params
        ]);

        $response = json_decode($response->getBody(), true);
        return DocoHelpers::response($response);
    }

    public function actionDpjpGetTindakanDetail(
        $id = null,
        $detail_id = null,
        $penjamin_id = null,
        $kelaspelayanan_id = null,
        $tipe = null
    )
    {

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
                $response_tindakan = $this->_restIgd->get('asesmen-dpjp/dpjp-get-tindakan-ruangan?ruanganId='.$this->_ruangan_id.'&kelasPelayananId='.$kelaspelayanan_id.'&id='.$detail_id.'&penjaminId='.$penjamin_id);
            }
            else {
                // Get response
                $response_tindakan = $this->_restIgd->get('asesmen-dpjp/dpjp-get-paket-ruangan?ruanganId='.$this->_ruangan_id.'&kelasPelayananId='.$kelaspelayanan_id.'&id='.$detail_id.'&penjaminId='.$penjamin_id);
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

    // Get tindakan dan paket session
    public function actionDpjpGetTindakanPaket()
    {
        $session = Yii::$app->session;

        $dataTindakanPaket = $session->get('tindakan_paket');

        return DocoHelpers::response([
            'response' => $dataTindakanPaket
        ]);
    }

    public function actionDpjpGetObatAlkesByJenis()
    {
        $request = Yii::$app->request;
        $instalasi_id = Yii::$app->docoVars->workspace('instalasi_id');
        $ruangan_id = $request->get('ruangan_id', null);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $penjamin_id = $request->get('penjamin_id', null);
        $group_jenis = $request->get('group_jenis');
        $keyword = $request->get('q');
        $page = $request->get('page');
        $kelaspelayanan_id = $this->_kelaspelayanan_id;
        $restApotek = Yii::$app->docoRest->apotek;

        $userIdentity = $this->_user_identity;
        if($userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN)
        {
            $group_jenis = DocoConstants::GOUP_ALKES;
        }

        $params = [
            'instalasi_id' => $instalasi_id,
            'ruangan_id' => $ruangan_id,
            'penjamin_id' => $penjamin_id,
            'group_jenisobat' => $group_jenis,
            'kelaspelayanan_id' => $kelaspelayanan_id,
            'keyword' => $keyword,
            'page' => $page,
        ];

        $endPoint = 'allow/get-list-stok-apotek';
        $rest = $restApotek;

        $response = $restApotek->get('allow/get-list-stok-apotek', [
            'query' => $params,
        ]);

        $body = json_decode($response->getBody(), true);
        $data = isset($body['response']['data']) ? $body['response']['data'] : [];

        $response = [];
        foreach ($data as $key => $value) {
            $response[] = [
                        'id'=> $value['obatalkes_id'],
                        'text'=> $value['obatalkes_nama'] . ' (' . $value['qty_tersedia'] . ')',
                        'disabled' => $value['qty_tersedia'] <= 0 ? true : false,
                        'datavalue'=> $value
                    ];
        }

        return DocoHelpers::response([
            'result' => $response,
            'total_count' => count($response),
            'incomplete_results' => false,
            'pagination' => [ 'more' => count($response) === 11 ? true : false ]
        ]);
    }

    // Cetak pdf list cppt
    public function actionCetakListDpjp($id)
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/list-dpjp-{$id}.pdf";
        $pendaftaran_id = DocoHelpers::decrypt($id);
        $kelompokpegawai_id = $request->get('kelompokpegawai_id', null);
        $nama_usercetak = Yii::$app->session->get('user_identity')['nama'];
        $id_usercetak = Yii::$app->session->get('user_identity')['id_pegawai'];

        $filterruangan_id = $request->get('filterruangan_id', null);
        $filterpegawai_id = $request->get('filterpegawai_id', null);
        $tgl_cppt = $request->get('tanggal_cppt', null);
        $kelompokpegawai_id = $request->get('kelompokpegawai_id');

        $request = $this->_restIgd->get('asesmen-dpjp/cetak-pdf-list-dpjp', [
            'query' => [
                'pendaftaran_id' => $pendaftaran_id,
                'ruangan_id' => $this->_ruangan_id,
                'pegawai_id' => $this->_pegawai_id,
                'kelompokpegawai_id' => $this->_user_identity['kelompokpegawai_id'],
                'filter_kelompokpegawai_id' => $kelompokpegawai_id,
                'nama_usercetak' => $nama_usercetak,
                'id_usercetak' => $id_usercetak,
                'filterruangan_id' => $filterruangan_id,
                'filterpegawai_id' => $filterpegawai_id,
                'tgl_cppt' => $tgl_cppt,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::previewPdf($path);
    }


    public function actionVerifikasiDpjp()
    {
        $request = Yii::$app->request;
        $cppt_id = DocoHelpers::decrypt($request->get('cppt_id'));

        try{
            $response = $this->_restIgd->post('asesmen-dpjp/verifikasi', [
                'form_params' => ['id'=>$cppt_id, 'jenis'=>'dpjp']
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

    public function actionVerifikasiVerbalOrder()
    {
        $request = Yii::$app->request;
        $cppt_id = DocoHelpers::decrypt($request->get('cppt_id'));

        try{
            $response = $this->_restIgd->post('asesmen-dpjp/verifikasi', [
                'form_params' => ['id'=>$cppt_id, 'jenis'=>'verbal']
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

    //hapus verbal
    public function actionDeleteVerbalOrder()
    {
        $request = Yii::$app->request;
        $cppt_id = DocoHelpers::decrypt($request->get('cppt_id'));
        $codeHttp = 422;

        try{
            $response = $this->_restIgd->post('asesmen-dpjp/delete-verbal', [
                'form_params' => ['id'=>$cppt_id, 'jenis'=>'verbal']
            ]);
            $response = json_decode($response->getBody(), true);
            if($response['metadata']['status'] == 200){
                return DocoHelpers::response(['response' => ['text' => 'Data berhasil dihapus']]);
            }

         } catch (RequestException $e) {
             // Exception
             throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
         } catch (\Exception $e) {
             // Exception
             throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
         }
    }

    // Hapus terapi
    public function actionHapusTerapi()
    {
        $request = Yii::$app->request;
        $cppt_id = $request->get('cppt_id',0);
        $instruksi_id = $request->get('instruksi_id',0);
        $tipeinstruksi = $request->get('tipeinstruksi',0);
        $codeHttp = 422;

        try {
            // Request
            $request = $this->_restIgd->delete('asesmen-dpjp/hapus-terapi',[
                'query' => [
                    'cppt_id' => $cppt_id,
                    'instruksi_id' => $instruksi_id,
                    'tipeinstruksi' => $tipeinstruksi
            ]]);
            $response = json_decode($request->getBody(), true);
            if($response['metadata']['status'] == 200){
                $response['response']['text'] = 'Berhasil Dihapus';
                return DocoHelpers::response($response,$codeHttp);
            }else{
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
        return DocoHelpers::response($response,$codeHttp);
    }

    /* Reseptur */
    public function actionReseptur()
    {
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('id', null);
            $modelResepturDetailRacikan = new ResepturDetailForm;
            $modelResepturDetailNonRacikan = new ResepturNrDetailForm;
            $modelResepturDetailRacikan->scenario = ResepturDetailForm::SCENARIO_SESSION;
            $modelResepturDetailNonRacikan->scenario = ResepturDetailForm::SCENARIO_SESSION;
            $modelResepturDetailRacikan->jenis_racikan = ResepturDetailForm::VC_RC;
            $modelResepturDetailNonRacikan->jenis_racikan = ResepturDetailForm::VC_NRC;

            if ($post = $request->post()) {
                $data = [];
                if(isset($post['ResepturNrDetailForm'])) {
                    $jenis_racikan = ResepturDetailForm::VC_NRC;
                    $postResepturNr = $post['ResepturNrDetailForm'];
                    $modelResepturDetailNonRacikan->attributes = $postResepturNr;
                    $postResepturNr['jenis_racikan'] = $jenis_racikan;
                    if(!$modelResepturDetailNonRacikan->validate()){
                        $formName = substr(strrchr(get_class($modelResepturDetailNonRacikan), "\\"), 1);
                        $response = $modelResepturDetailNonRacikan->errors;

                        return DocoHelpers::response($response, 422, $formName);
                    }
                    $data = $postResepturNr;
                }

                if(isset($post['ResepturDetailForm'])) {
                    $jenis_racikan = ResepturDetailForm::VC_RC;
                    $postReseptur = $post['ResepturDetailForm'];
                    $modelResepturDetailRacikan->attributes = $postReseptur;
                    $postReseptur['jenis_racikan'] = $jenis_racikan;
                    if(!$modelResepturDetailRacikan->validate()) {
                        $formName = substr(strrchr(get_class($modelResepturDetailRacikan), "\\"), 1);
                        $response = $modelResepturDetailRacikan->errors;

                        return DocoHelpers::response($response, 422, $formName);
                    }

                    $data = $postReseptur;
                }

                $response = $this->addSessionReseptur($pendaftaran_id, $data);

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
                    return DocoHelpers::response(['response' => ['message' => 'Data berhasil disimpan']]);
                }
            }

        } catch(\Exception $e) {
            return DocoHelpers::response([
                'response' => [
                    'title' => 'Proses Gagal !',
                    'text' => $e->getMessage()
                ]
            ],422);

        }
    }

    /* Get data respetur session */
    public function actionGetDataResepturSession()
    {
        $request = Yii::$app->request;
        $session = Yii::$app->session;

        $pendaftaran_id_encrypt = $request->get('id');
        $cppt_id = $request->get('cppt_id');
        $ruangan_id = $request->get('ruangan_id');
        $isEditReseptur = $request->get('isEditReseptur');
        $instruksi_id = $request->get('instruksi_id');
        $is_submit = $request->get('is_submit');
        $draw = $request->get('draw');
        $user_login = Yii::$app->user->identity->loginpemakai_id;
        try {
            /*check if create and update method */
            if($isEditReseptur == 0) {
                if(isset($session['pemeriksaan_reseptur'][$pendaftaran_id_encrypt.$cppt_id])) {
                    $temp_session = $session['pemeriksaan_reseptur'][$pendaftaran_id_encrypt . $cppt_id];
                    $data_tables = $this->generateTableReseptur($pendaftaran_id_encrypt,
                            $temp_session, $cppt_id, $ruangan_id, $isEditReseptur);
                    return DocoHelpers::response($data_tables);
                } else {
                    return DocoHelpers::dataTabelsException('Data kosong');
                }
            } else {
                $dataResepturDb = $session->get('pemeriksaan_reseptur_db');
                $dataReseptur = $session->get('pemeriksaan_reseptur');
                $encryptedId = $pendaftaran_id_encrypt.$cppt_id;
                if(empty($dataResepturDb[$encryptedId]) && $draw == 1) {
                    if($is_submit == 0) {
                        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id_encrypt);
                        $dataExist = $this->getReseptur($pendaftaran_id, $instruksi_id);
                        if(!empty($dataExist)) {
                           $this->listDataReseptur($dataExist, $pendaftaran_id_encrypt, $cppt_id);
                        }
                    }
                }
                $dataResepturDb = $session->get('pemeriksaan_reseptur_db');
                if(!empty($dataResepturDb[$encryptedId])) {
                    $temp_session = $dataResepturDb[$encryptedId];
                    $data_tables = $this->generateTableReseptur($pendaftaran_id_encrypt, $temp_session, $cppt_id, $ruangan_id, $isEditReseptur);
                    return DocoHelpers::response($data_tables);
                }

                if(!empty($dataReseptur[$encryptedId])) {
                    $temp_session = $dataReseptur[$encryptedId];
                    $data_tables = $this->generateTableReseptur($pendaftaran_id_encrypt, $temp_session, $cppt_id, $ruangan_id, $isEditReseptur);

                    return DocoHelpers::response($data_tables);
                }
                else {
                    return DocoHelpers::dataTabelsException('Data kosong');
                }
            }
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    /* Batal reseptur */
    public function actionBatalSessionReseptur()
    {
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $errors = 'Terdapat kesalahan';
        $reseptur = [];
        try {
            $sess_id = $request->get('sess_key');
            $pendaftaran_id_encrypt = $request->get('pendaftaran_id');
            $isEditReseptur = $request->get('isEditReseptur');
            $resepturdetail_id = $request->get('resepturdetail_id');
            $cppt_id_encrypt = $request->get('cppt_id');
            $encryptedId = $pendaftaran_id_encrypt . $cppt_id_encrypt;

            if(!empty($resepturdetail_id)) {
                if (isset($session['pemeriksaan_reseptur_db'])) {
                    $session_reseptur = $session['pemeriksaan_reseptur_db'];
                    $temp_session = $session['pemeriksaan_reseptur'];
                    $default = !empty($session_reseptur[$encryptedId]) ? $session_reseptur[$encryptedId] : [];
                    $tmp = !empty($temp_session[$encryptedId]) ? $temp_session[$encryptedId] : $default;
                    if (!empty($tmp)){
                        foreach ($tmp as $key => $value) {
                            if ($value['resepturdetail_id'] == $resepturdetail_id) {
                                unset($tmp[$key]);
                                continue;
                            }
                        }
                        $temp_session[$encryptedId] = $tmp;
                        $session->set('pemeriksaan_reseptur_db', []);
                        $session->set('pemeriksaan_reseptur', $temp_session);
                    }
                }
            } else {
                if (isset($session['pemeriksaan_reseptur'])) {
                    $session_reseptur = $session['pemeriksaan_reseptur'];
                    if (isset($session_reseptur[$encryptedId])){
                        foreach ($session_reseptur[$encryptedId] as $key => $value) {
                            if ($value['session_key'] == $sess_id && $value['resepturdetail_id'] == ""){
                                $reseptur = $session_reseptur[$encryptedId][$key];
                                unset($session_reseptur[$encryptedId][$key]);
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

                $session_obat_dihapus[$reseptur['jenis_racikan'].$reseptur['obatalkes_id']] = $reseptur;
                $session->set('obat_dihapus', $session_obat_dihapus);
            }

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

    /* Reset reseptur session */
    public function actionResetResepturSession($id, $cppt_id, $instruksi_id)
    {
        try {
            $this->resetResepturSession($id, $cppt_id, $instruksi_id);

            return DocoHelpers::response(['message'=>'Sukses']);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    /* Save session reseptur */
    public function actionSaveSessionReseptur()
    {
        $session = Yii::$app->session;
        $request = Yii::$app->request;
        $errors = 'Terdapat kesalahan';
        // $session->set('pemeriksaan_reseptur', []);

        try {
            $post = $request->post();
            $instruksi_id = $post['InstruksiForm']['instruksi_id'];
            $pendaftaran_id_encrypt = $request->get('id');
            $cppt_id_encrypt = $request->get('cppt_id');
            $update = $request->get('update');
            $encryptedId = $pendaftaran_id_encrypt.$cppt_id_encrypt;

            $data_pasien = $this->_data_pasien;
            $resepturForm = $this->validateResepturForm($post, $data_pasien, $pendaftaran_id_encrypt);
            $instruksiForm = $post['InstruksiForm'] ? $post['InstruksiForm'] : [];
            $instruksiForm['tgl_instruksi'] = date('Y-m-d H:i:s', strtotime('NOW'));
            $data_reseptur = [];

            if (isset($session['pemeriksaan_reseptur_db'][$encryptedId])) {
                $data_reseptur = $session['pemeriksaan_reseptur_db'][$encryptedId];
            }

            if (isset($session['pemeriksaan_reseptur'][$encryptedId])) {
                $data_reseptur = array_merge($session['pemeriksaan_reseptur'][$encryptedId], $data_reseptur);
            }else{
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

            $data_insert_reseptur = $resepturForm;
            $data_insert_reseptur['ruangan_id'] = $resepturForm['ruangan_id'];
            $data_insert_reseptur['pasien_id'] = $data_pasien['pasien_id'];
            $data_insert_reseptur['berat_badan'] = str_replace(",", ".", $data_insert_reseptur['berat_badan']);
            $data_insert_reseptur['tinggi_badan'] = str_replace(",", ".", $data_insert_reseptur['tinggi_badan']);
            $data_insert_reseptur['luas_tubuh'] = str_replace(",", ".", $data_insert_reseptur['luas_tubuh']);
            $data_insert_reseptur['pendaftaran_id'] = DocoHelpers::decrypt($pendaftaran_id_encrypt);

            $data_insert_reseptur['tglreseptur'] = date('Y-m-d H:i:s', strtotime('NOW'));
            $data_insert_reseptur['ruanganreseptur_id'] = $this->_ruangan_id;

            $check_isracikan = false;
            $data_insert_resepturdetails = [];

            $no = 0;
            foreach ($data_reseptur as $key => $value) {
                $signaId = null;
                if (isset($post['signa_edit_'. $no])){
                    $signaId = $post['signa_edit_'. $no];
                }

                $additional = [
                    'satuaninput_id' => $value['satuankecil_id'],
                    'satuan_input' => $value['satuankecil_nama'],
                    'satuankonversi_id' => $value['satuandefault_id'],
                    'satuan_konversi' => $value['satuandefault_nama'],
                    'harga_konversi' => $value['hargasatuan_reseptur'],
                    'nilai_konversi' => isset($value['nilai_konversi']) ? $value['nilai_konversi'] : 0,
                ];


                $data_insert_resepturdetail = [];
                $data_insert_resepturdetail['resepturdetail_id'] = $value['resepturdetail_id'];
                $data_insert_resepturdetail['obatalkes_id'] = $value['obatalkes_id'];
                $data_insert_resepturdetail['racikan_id'] = $value['jenis_racikan'];
                $data_insert_resepturdetail['satuankecil_id'] = $value['satuankecil_id'];
                $data_insert_resepturdetail['reseptur_id'] = 'null';
                $data_insert_resepturdetail['r'] = !empty($value['rke']) ? 'r' : 'null';
                $data_insert_resepturdetail['rke'] = !empty($value['rke']) ? $value['rke'] : 'null';
                $data_insert_resepturdetail['qty_reseptur'] = str_replace(",", ".", $value['qty_reseptur']);
                $data_insert_resepturdetail['hargasatuan_reseptur'] = isset($value['harga_konversi']) ? $value['harga_konversi'] : 0;
                $data_insert_resepturdetail['harganetto_reseptur'] = $value['harganetto_reseptur'];
                $data_insert_resepturdetail['hargajual_reseptur'] = $value['harga_jual'];
                $data_insert_resepturdetail['iter'] = $value['iter'];
                $data_insert_resepturdetail['signa'] = !empty($signaId) ? $signaId : @$value['signa_reseptur'];
                // $data_insert_resepturdetail['signa_id'] = !empty($signaId) ? $signaId : @$value['signa_reseptur'];
                $data_insert_resepturdetail['qty_konversi'] = str_replace(",", ".", $value['total_konversi']);
                $data_insert_resepturdetail['etiket'] = isset($value['etiket']) ? $value['etiket'] : null;
                $data_insert_resepturdetail['additional_data'] = json_encode($additional);

                if ($check_isracikan === false && $value['jenis_racikan'] == ResepturDetailForm::VC_RC){
                    $check_isracikan = true;
                }

                array_push($data_insert_resepturdetails, $data_insert_resepturdetail);
                $no++;
            }

            $data_insert_reseptur['racikan_id'] = ($check_isracikan === true) ? ResepturDetailForm::VC_RC : ResepturDetailForm::VC_NRC;

            $data_send = [
                'data_instruksi' => $instruksiForm,
                'data_reseptur' => $data_insert_reseptur,
                'data_resepturdetail' => $data_insert_resepturdetails,
                'pasien_id' => $request->post('pasien_id_now'),
                'ruangan' => $request->post('ruangan_now')
            ];
            Yii::error([
                'data-send-igd' => $data_send
            ]);

            if ($update == 'false') {
                $response = $this->_restIgd->post('asesmen-dpjp/create-reseptur', [
                    'form_params' => $data_send
                ]);
                $response = json_decode($response->getBody(), true);

            } else {
                $response = $this->_restIgd->post('asesmen-dpjp/update-reseptur', [
                    'form_params' => $data_send
                ]);
                $response = json_decode($response->getBody(), true);
            }

            if ($response['metadata']['status'] == 200) {
                $this->resetResepturSession($pendaftaran_id_encrypt, $cppt_id_encrypt, $instruksi_id);
            }
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            $response = json_decode($e->getResponse()->getBody(),true);
            $this->logError($e);
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
        } catch (\Exception $e) {
            $response = json_decode($e->getMessage(), true);
            $this->logError($e);
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

    /* Cetak pdf */
    public function actionCetakReseptur($id, $instruksi_id)
    {
        $path = Yii::getAlias("@download") . "/terapi-reseptur.pdf";

        try {
            $request = $this->_restIgd->get('asesmen-dpjp/cetak-reseptur?pendaftaran_id='.$id.'&pegawai_id='.$this->_user_identity['id_pegawai'].'&ruangan_id='.$this->_ruangan_id.'&instruksi_id='.$instruksi_id, [
                'save_to' => $path,
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
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

                    if (isset($session_reseptur[$encrypted_pendaftaran_id.$cppt_id])) {
                        foreach ($session_reseptur[$encrypted_pendaftaran_id.$cppt_id] as $key => $value) {
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

                    if (isset($session_reseptur[$encrypted_pendaftaran_id.$cppt_id])) {
                        foreach ($session_reseptur[$encrypted_pendaftaran_id.$cppt_id] as $key => $value) {
                            if ($value['obatalkes_id'] == $obatalkes_id && $value['jenis_racikan'] == $jenis_racikan) {
                                $session_reseptur[$encrypted_pendaftaran_id.$cppt_id][$key]['last_qty_reseptur'] = $session_reseptur[$encrypted_pendaftaran_id.$cppt_id][$key]['qty_reseptur'];
                                $session_reseptur[$encrypted_pendaftaran_id.$cppt_id][$key]['qty_reseptur'] = floatval($qty);

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

    /* Generate tabel reseptur */
    private function generateTableReseptur($pendaftaran_id_encrypt, $datas = [], $cppt_id, $ruangan_id, $isEditReseptur)
    {
        if(is_numeric($pendaftaran_id_encrypt)) {
            $pendaftaran_id_encrypt = DocoHelpers::encrypt($pendaftaran_id_encrypt);
        }
        if(is_numeric($cppt_id)) {
            $cppt_id = DocoHelpers::encrypt($cppt_id);
        }
        foreach ($datas as $eachData) {
            $medIds[] = $eachData['obatalkes_id'];
        }
        $request = Yii::$app->request;
        $session = Yii::$app->session;
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
            foreach ($datas as $key => $data) {
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
                    $stok_tersedia[$data['obatalkes_id']] = isset($data['stok_tersedia']) ? $data['stok_tersedia'] : 0;
                }

                $resepturdetail_id = $data['resepturdetail_id'];
                $data_table['no'] = $no;
                $data_table['session_key'] = $primaryKey;
                $data_table['jenis_racikan'] = $data['jenis_racikan'];
                $data_table['nama_racikan'] = $data['nama_racikan'];
                $data_table['rke'] = !empty($data['rke']) ? $data['rke'] : '-';
                $data_table['signa_reseptur'] = $data['signa_reseptur'];
                $data_table['obatalkes_id'] = $data['obatalkes_id'];
                $data_table['obatalkes_nama'] = isset($list_data_obatalkes[$data['obatalkes_id']]) ? $list_data_obatalkes[$data['obatalkes_id']] : '-';
                $data_table['qty_reseptur'] = $data['qty_reseptur'];
                $data_table['total_konversi'] = $data['total_konversi'];
                $data_table['satuankecil_id'] = $data['satuankecil_id'];
                $data_table['etiket'] = isset($data['etiket']) ? $data['etiket'] : null;
                $data_table['satuankecil_nama'] = $data['satuankecil_nama'];
                $data_table['iter'] = $data['iter'];
                $data_table['hargasatuan_reseptur'] = DocoHelpers::rupiahDisplay($data['harga_konversi']);
                $data_table['jumlah_harga'] = DocoHelpers::rupiahDisplay($data['qty_reseptur'] * $data['harga_konversi']);
                $data_table['resepturdetail_id'] = $resepturdetail_id;
                $data_table['signa_edit'] = Html::textInput('signa_edit_'. ($no - 1), $data['signa_reseptur'], ['class' => 'form-control input-xs']);

                $data_table['qty_edit'] = Html::textInput('qty_edit_'. $primaryKey, $data['qty_reseptur'], ['class' => 'form-control input-xs docoNumberOnly qty', 'disabled' => true]);

                $data_table['aksi'] = Html::button(
                        '<i class="fa fa-times"></i>',
                        [
                            'class' => 'btn btn-indian-red delete-reseptur',
                            'action' => '/igd/pemeriksaan-igd/batal-session-reseptur?id='.$pendaftaran_id_encrypt.'&sess_key='.$primaryKey.'&pendaftaran_id='.$pendaftaran_id_encrypt.'&cppt_id='.$cppt_id.'&isEditReseptur='.$isEditReseptur.'&resepturdetail_id='.$resepturdetail_id,
                            'data-confirm-message' => Yii::t('fe', 'confirm_batal'),
                            'onclick' => 'batalSessionReseptur(this)',
                        ]
                    );


                array_push($data_tables, $data_table);
                $no++;
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

    /* Add session reseptur */
    private function addSessionReseptur($pendaftaran_id, $data = null)
    {
        $session = Yii::$app->session;
        $pendaftaran_id_encrypt = $pendaftaran_id;
        $cppt_id = DocoHelpers::encrypt($data['cppt_id']);

        try {
            $session_key = 1;
            $session_reseptur = [];

            if (isset($session['pemeriksaan_reseptur'])) {
                $session_reseptur = $session['pemeriksaan_reseptur'];
            }
            if (isset($session_reseptur[$pendaftaran_id . $cppt_id])){
                foreach ($session_reseptur[$pendaftaran_id . $cppt_id] as $key => $value) {
                    $session_key = $value['session_key'];
                }
                $session_key++;
            }

            $data_reseptur = $this->setAttrReseptur($data);
            $count_data_insert = $data_reseptur['count_data_insert'];
            $data_reseptur = $data_reseptur['data'];
            $isEditReseptur = $data_reseptur['isEditReseptur'];
            $instruksi_id = $data_reseptur['instruksi_id'];

            $this->setSessionReseptur($count_data_insert, $isEditReseptur, $session_reseptur, $pendaftaran_id, $cppt_id, $instruksi_id, $session_key, $data_reseptur);

            return [
                'status' => 200,
                'message' => Yii::t('fe', 'OK'),
                'data' => $session['pemeriksaan_reseptur'],
            ];
        } catch (Exception $e) {
            return [
                'status' => 100,
                'message' => $e->getMessage(),
            ];
        }
    }

    /* Reset session reseptur */
    private function resetResepturSession($id, $cppt_id, $instruksi_id) {
        try {
            $session = Yii::$app->session;
            if ($id){
                if($instruksi_id == "") {
                    if (isset($session['pemeriksaan_reseptur'])) {
                        $temp_session = $session['pemeriksaan_reseptur'];
                        if (isset($temp_session[$id . $cppt_id])){
                            unset($temp_session[$id . $cppt_id]);
                        }
                        $session->set('pemeriksaan_reseptur', $temp_session);
                    }
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

    /* Get list data signa dan obatalkes */
    private function getDataSignaDanObatalkes($medIds = [])
    {
        try {
            $request = $this->_restIgd->get('asesmen-dpjp/get-data-signa-dan-obatalkes', ['form_params' => compact('medIds')]);
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


    // =============== penunjang
    public function actionSimpanTerapiPenunjang()
    {
        try {
            $request = Yii::$app->request;
            $orders = $request->post('periksalab');
            $orders = json_decode($orders, true);
            $ruanganasal_id = $request->post('ruanganperiksa_id') != null ? PelayananHelpers::decryptId($request->post('ruanganperiksa_id')) : null;

            $model = new InstruksiPenunjangForm;
            $model->attributes = $request->post('InstruksiPenunjangForm');
            $jadwal_operasi = [];
            if ($model->instalasi_id == DocoConstants::INSTALASI_ID_BEDAH) {
                $session = Yii::$app->session;
                $jadwal_operasi = json_decode($request->post('jadwal_operasi'), true);
                $instruksiPenunjang = $request->post('InstruksiPenunjangForm');
            }

            if ($model->validate()) {
                $temps = [];
                foreach ($orders as $key=>$order) {
                    $temps[$key]['tariftindakan_id'] = $order['tariftindakan_id'];
                    $temps[$key]['is_cyto'] = $order['is_cyto'] == 'true' ? true : false;
                    $temps[$key]['is_paket'] = isset($order['tipepaket_id']) ? true : false;
                    $temps[$key]['golongan_id'] = isset($order['kelompokpemeriksaanlab_id'])
                        ? $order['kelompokpemeriksaanlab_id']
                        : null;
                    $temps[$key]['kegiatan_id'] = isset($order['jenispemeriksaanlab_id'])
                        ? $order['jenispemeriksaanlab_id']
                        : null;
                }

                $instruksiPenunjang = $request->post('InstruksiPenunjangForm');
                $cpptId = $request->post('cppt_id');
                $model->tgl_kirimpasien = (date_create_from_format('d/m/Y', $model->tgl_kirimpasien)) ?date_format(date_create_from_format('d/m/Y', $model->tgl_kirimpasien), 'Y-m-d H:i:s'):$model->tgl_kirimpasien;
                if(array_key_exists('tgl_kirimpasien',$jadwal_operasi)){
                    $jadwal_operasi['tgl_kirimpasien'] = (date_create_from_format('d/m/Y', $jadwal_operasi['tgl_kirimpasien'])) ?date_format(date_create_from_format('d/m/Y', $jadwal_operasi['tgl_kirimpasien']), 'Y-m-d H:i:s'):$jadwal_operasi['tgl_kirimpasien'];
                }
                $post = [
                    'pendaftaran_id' => $model->pendaftaran_id,
                    'pasienadmisi_id' => $model->pasienadmisi_id,
                    'instalasi_id' => $model->instalasi_id,
                    'ruangan_id' => $model->ruangan_id,
                    'pegawai_id' => $model->pegawai_id,
                    'cppt_id' => $model->cppt_id ? DocoHelpers::decrypt($model->cppt_id) : null,
                    'catatan_dokterpengirim' => $model->catatan_dokterpengirim,
                    'tgl_kirimpasien' => $model->instalasi_id == DocoConstants::INSTALASI_ID_BEDAH ? date('Y-m-d', strtotime($jadwal_operasi['tgl_kirimpasien'])) . ' ' . $jadwal_operasi['jam_mulai'] : date('Y-m-d', strtotime($model->tgl_kirimpasien)) . ' ' . date('H:i:s'),
                    'list_order' => $temps,
                    'jadwal_operasi' => $jadwal_operasi,
                    'is_puasa' => $model->is_puasa,
                    'ruangan' => $model->ruangan,
                    'pasien_id' => $this->_pasien_id,
                    'pemakaian_implant' => $model->pemakaian_implant,
                    'sewa_vendor' => $model->sewa_vendor,
                    'sewa_alat_rs' => $model->sewa_alat_rs,
                    'jenis_operasi_cito' => $model->jenis_operasi_cito,
                    'jenis_operasi_elektif' => $model->jenis_operasi_elektif,
                    'jenis_operasi_odc' => $model->jenis_operasi_odc,
                    'is_rujukan' => $model->is_rujukan,
                    'diagnosa_utama' => isset($model->diagnosa_utama) ? json_decode($model->diagnosa_utama, true) : [],
                    'diagnosa_penyerta' => isset($model->diagnosa_penyerta) ? json_decode($model->diagnosa_penyerta, true) : [],
                    'ruangan_asal' => $ruanganasal_id != null ? $ruanganasal_id : ArrayHelper::getValue(Yii::$app->session->get('active_workspace'), 'ruangan_id')
                ];
                $response = $this->_restIgd->post('asesmen-dpjp/create-terapi-penunjang', [
                    'form_params' => $post
                ]);
                $response = json_decode($response->getBody(), true);
            } else {
                $formName = substr(strrchr(get_class($model), "\\"), 1);
                $response = $model->errors;
                return DocoHelpers::response($response, 422, $formName);
            }

            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
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
            if($get['instalasi_id'] == DocoConstants::INSTALASI_ID_LAB || $get['instalasi_id'] == DocoConstants::INSTALASI_ID_RAD || $get['instalasi_id'] == DocoConstants::INSTALASI_ID_BEDAH){
                $params = [
                    'ruangan_id'        => isset($get['ruangan_id']) ? $get['ruangan_id'] : '',
                    'penjamin_id'       => isset($get['penjamin_id']) ? $get['penjamin_id'] : '',
                    'kelaspelayanan_id' => isset($get['kelaspelayanan_id']) ? $get['kelaspelayanan_id'] : '',
                    'instalasi_id'      => isset($get['instalasi_id']) ? $get['instalasi_id'] : '',
                ];
                /*
                $search_header = $get['searching'];

                if($search_konten){
                    $params['daftartindakan_nama'] = $search_konten;
                }
                */

                $url = 'allow/get-tarif-tindakan';
                $response = $this->_restIgd->get($url, ['query'=>$params]);
                $body = json_decode($response->getBody(), true);
                $body = isset($body['response']) ? $body['response'] : [];
                $groupingtindakan_penunjang = isset($body['groupingtindakan_penunjang']) ? $body['groupingtindakan_penunjang'] : false;
                foreach ($body['data'] as $key => $value) {
                    if($get['instalasi_id'] == DocoConstants::INSTALASI_ID_BEDAH){
                        if ($groupingtindakan_penunjang) {
                            $result[$value['daftartindakan_nama']][$value['nama_kelompok']][] = $value;
                            continue;
                        }
                    }
                    $result[$value['jenispemeriksaanlab_nama']][$value['nama_kelompok']][] = $value;
                }
            }

            $title = Yii::t('fe', 'Tambah Pemeriksaan');
            return $this->renderAjax('//cppt/penunjang/__modal_order_penunjang', get_defined_vars());

        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionDataPemeriksaanPenunjang()
    {
        $result = [];
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            if($get['instalasi_id'] == DocoConstants::INSTALASI_ID_LAB || $get['instalasi_id'] == DocoConstants::INSTALASI_ID_RAD || $get['instalasi_id'] == DocoConstants::INSTALASI_ID_BEDAH || $get['instalasi_id'] == DocoConstants::INSTALASI_FISIOTERAPI){
                $params = [
                    'ruangan_id'        => isset($get['ruangan_id']) ? $get['ruangan_id'] : '',
                    'penjamin_id'       => isset($get['penjamin_id']) ? $get['penjamin_id'] : '',
                    'kelaspelayanan_id' => isset($get['kelaspelayanan_id']) ? $get['kelaspelayanan_id'] : '',
                    'instalasi_id'      => isset($get['instalasi_id']) ? $get['instalasi_id'] : '',
                ];
                $search_konten = $get['searching'];

                if($search_konten){
                    $params['daftartindakan_nama'] = $search_konten;
                }

                $url = 'allow/get-tarif-tindakan';
                $response = $this->_restIgd->get($url, ['query'=>$params]);
                $body = json_decode($response->getBody(), true);
                $body = isset($body['response']) ? $body['response'] : [];

                foreach ($body['data'] as $key => $value) {
                    $result[$value['jenispemeriksaanlab_nama']][$value['nama_kelompok']][] = $value;
                }
            }

            return json_encode($result);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    /* Cetak pdf penunjang */
    public function actionCetakPenunjang($id, $instruksi_id)
    {
        $path = Yii::getAlias("@download") . "/terapi-penunjang.pdf";
        $request = $this->_restIgd->get('asesmen-dpjp/cetak-penunjang', [
            'query' => [
                'pendaftaran_id' => $id,
                'pegawai_id' => $this->_user_identity['id_pegawai'],
                'instruksi_id' => $instruksi_id
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
            // $request = $this->_restIgd->get('allow/get-all-dokter', ['form_params' => []]);
            // $response = json_decode($request->getBody(), true);
            // $list_dokter = $response ? $response['response'] : [];

            return $this->renderAjax('//cppt/penunjang/_modal_jadwal_operasi', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionGetDataJadwalOperasi()
    {
        $request = Yii::$app->request;
        try {
            $response = $this->_restIgd->get('asesmen-dpjp/get-data-jadwal-operasi', [
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
            $response = $this->_restIgd->get('asesmen-dpjp/view-jadwal-operasi',[
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
                $session->set('jadwal_operasi_igd', $post);
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

    public function actionCetakTindakanBmhp($id)
    {
        // Path
        $path = Yii::getAlias("@download") . "/tindakan-bmhp.pdf";
        $pendaftaran_id = DocoHelpers::decrypt($id);
        $nama_usercetak = Yii::$app->session->get('user_identity')['nama'];
        $id_usercetak = Yii::$app->session->get('user_identity')['id_pegawai'];
        // Try catch
        try {
            // Request
            $request = $this->_restIgd->get('asesmen-dpjp/cetak-tindakan-bmhp', [
                'query' => [
                    'pendaftaran_id' => $pendaftaran_id,
                    'ruangan_id' => $this->_ruangan_id,
                    'pegawai_id' => $this->_pegawai_id,
                    'kelompokpegawai_id' => $this->_user_identity['kelompokpegawai_id'],
                    'nama_usercetak' => $nama_usercetak,
                    'id_usercetak' => $id_usercetak
                ],
                'save_to' => $path,
            ]);

            // Download pdf
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    private function getSatuan($satuan_id)
    {
        try {
            $response = $this->_restIgd->get('allow/get-satuan',[
                'query' => [
                    'id' => $satuan_id
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            return $body['response'];
        } catch (\Exception $e) {
            // Exception
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (RequestException $e) {
            // Exception
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    private function listDataReseptur($data, $pendaftaran_id_encrypt, $cppt_id)
    {
        $session = Yii::$app->session;
        $session_db = $session->get('pemeriksaan_reseptur_db', []);
        $session_reseptur = !empty($data) ? $data : $session_db;

        $session_key = 1;
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $additional = json_decode($value['additional_reseptur'], true);
                $session_reseptur[$pendaftaran_id_encrypt.$cppt_id][$value['resepturdetail_id']] = [
                    'session_key' => 0,
                    'resepturdetail_id' => $value['resepturdetail_id'],
                    'jenis_racikan' => ($value['racikan_id'] == 2) ? "NR" : "OR",
                    'nama_racikan' => ($value['racikan_id'] == 2) ? "Non Racikan" : "Obat Racikan",
                    'rke' => $value['rke'],
                    'signa_reseptur' => $value['signa_id'],
                    'obatalkes_id' => $value['obatalkes_id'],
                    'obatalkes_nama' => $value['obatalkes_nama'],
                    'qty_reseptur' => $value['qty_reseptur'],
                    'satuankecil_id' => $additional['satuaninput_id'],
                    'satuankecil_nama' => $additional['satuan_input'],
                    'satuandefault_id' => $additional['satuankonversi_id'],
                    'satuandefault_nama' => $additional['satuan_konversi'],
                    'hargasatuan_reseptur' => $value['harga_jual'],
                    'nilai_konversi' => $value['qty_konversi'],
                    'total_konversi' => $value['qty_reseptur'] * $value['qty_konversi'],
                    'harga_konversi' => $value['hargajual_satuan'],
                    'harganetto_reseptur' => $value['harga_netto'],
                    'harga_jual' => $value['harga_jual'],
                    'etiket' => $value['etiket'],
                    'iter' => $value['iter'],
                ];
                $session_key++;
            }


            if(isset($session_reseptur[$pendaftaran_id_encrypt.$cppt_id])) {
                $sess[$pendaftaran_id_encrypt.$cppt_id] = $session_reseptur[$pendaftaran_id_encrypt.$cppt_id];
                $session->set('pemeriksaan_reseptur_db',$sess);
            }
        }

        return $session->get('pemeriksaan_reseptur_db');
    }

    private function getReseptur($pendaftaran_id_encrypt, $instruksi_id)
    {
        $request = $this->_restIgd->get('asesmen-dpjp/get-data-reseptur-detail?pendaftaran_id='.$pendaftaran_id_encrypt.'&instruksi_id='.$instruksi_id);

        $response = json_decode($request->getBody(), true);
        $attributes = $response['response'];

        return $attributes;
    }

    private function setAttrReseptur($data)
    {
        $count_data_insert = 0;
        $data_reseptur = [];
        $data_reseptur['jenis_racikan'] = $data['jenis_racikan'];
        $data_reseptur['nama_racikan'] = !empty($data['rke']) ? 'Obat Racikan' : 'Non racikan';
        $data_reseptur['rke'] = @$data['rke'];
        $data_reseptur['signa_reseptur'] = $data['signa_reseptur'];
        $data_reseptur['etiket'] = $data['etiket'];

        if (is_array($data['obatalkes_id'])){
            foreach ($data['obatalkes_id'] as $key => $value) {
                $data_reseptur['obatalkes_id'][] = $value;
                $count_data_insert++;
            }
        } else {
            $data_reseptur['obatalkes_id'] = $data['obatalkes_id'];
        }

        if (is_array($data['qty_reseptur'])){
            foreach ($data['qty_reseptur'] as $key => $value) {
                $data_reseptur['qty_reseptur'][] = $value;
            }
        } else {
            $data_reseptur['qty_reseptur'] = $data['qty_reseptur'];
        }

        if (is_array($data['satuankecil_id'])){
            foreach ($data['satuankecil_id'] as $key => $value) {
                $data_reseptur['satuankecil_id'][] = $value;
            }
        } else {
            $data_reseptur['satuankecil_id'] = $data['satuankecil_id'];
        }
        if (is_array($data['satuankecil_nama'])){
            foreach ($data['satuankecil_nama'] as $key => $value) {
                $data_reseptur['satuankecil_nama'][] = $value;
            }
        } else {
            $data_reseptur['satuankecil_nama'] = $data['satuankecil_nama'];
        }

        if (is_array($data['hargasatuan_reseptur'])){
            foreach ($data['hargasatuan_reseptur'] as $key => $value) {
                $data_reseptur['hargasatuan_reseptur'][] = $value;
            }
        } else {
            $data_reseptur['hargasatuan_reseptur'] = $data['hargasatuan_reseptur'];
        }

        if(is_array($data['nilai_konversi'])) {
            foreach ($data['nilai_konversi'] as $key => $value) {
                $data_reseptur['nilai_konversi'][] = $value;
            }
        } else {
            $data_reseptur['nilai_konversi'] = $data['nilai_konversi'];
        }

        if (is_array($data['satuandefault_id'])){
            foreach ($data['satuandefault_id'] as $key => $value) {
                $data_reseptur['satuandefault_id'][] = $value;
            }
        } else {
            $data_reseptur['satuandefault_id'] = $data['satuandefault_id'];
        }

        if (is_array($data['satuandefault_nama'])){
            foreach ($data['satuandefault_nama'] as $key => $value) {
                $data_reseptur['satuandefault_nama'][] = $value;
            }
        } else {
            $data_reseptur['satuandefault_nama'] = $data['satuandefault_nama'];
        }

        if(is_array($data['harga_konversi'])) {
            foreach ($data['harga_konversi'] as $key => $value) {
                $data_reseptur['harga_konversi'][] = $value;
            }
        } else {
            $data_reseptur['harga_konversi'] = $data['harga_konversi'];
        }

        if(is_array($data['harga_satuan'])) {
            foreach ($data['harga_satuan'] as $key => $value) {
                $data_reseptur['harga_satuan'][] = $value;
            }
        } else {
            $data_reseptur['harga_satuan'] = $data['harga_satuan'];
        }

        if(is_array($data['harga_jual'])) {
            foreach ($data['harga_jual'] as $key => $value) {
                $data_reseptur['harga_jual'][] = $value;
            }
        } else {
            $data_reseptur['harga_jual'] = $data['harga_jual'];
        }

        if(is_array($data['harganetto'])) {
            foreach ($data['harganetto'] as $key => $value) {
                $data_reseptur['harganetto_reseptur'][] = $value;
            }
        } else {
            $data_reseptur['harganetto_reseptur'] = $data['harganetto'];
        }

        if(is_array($data['stok_sisa'])) {
            foreach ($data['stok_sisa'] as $key => $value) {
                $data_reseptur['stok_sisa'][] = $value;
            }
        } else {
            $data_reseptur['stok_sisa'] = $data['stok_sisa'];
        }

        $data_reseptur['isEditReseptur'] = isset($data['isEditReseptur']) ? $data['isEditReseptur'] : 0;
        $data_reseptur['instruksi_id'] = isset($data['instruksi_id']) ? $data['instruksi_id'] : 0;

        return [
            'data' => $data_reseptur,
            'count_data_insert' => $count_data_insert
        ];
    }

    private function setSessionReseptur($count_data_insert, $isEditReseptur, $session_reseptur, $pendaftaran_id, $cppt_id, $instruksi_id, $session_key, $data_reseptur)
    {
        $postIter = Yii::$app->request->post('iter');
        $session = Yii::$app->session;
        $datas = [];
        $encryptedId = $pendaftaran_id.$cppt_id;
        $sesDb = $session->get('pemeriksaan_reseptur_db');
        if(empty($session_reseptur[$encryptedId])) {
            if(isset($sesDb[$encryptedId])) {
                foreach ($sesDb[$encryptedId] as $key => $value) {
                    $session_reseptur[$encryptedId][] = $value;
                }
            }
        }

        if ($count_data_insert !== 0) {
            for ($i = 0; $i < $count_data_insert; $i++) {
                if (!empty($session_reseptur[$pendaftaran_id.$cppt_id])) {
                    foreach ($session_reseptur[$pendaftaran_id.$cppt_id] as $key => $value) {
                        if (($value['obatalkes_id'] == $data_reseptur['obatalkes_id'][$i])
                             && ($value['jenis_racikan'] == $data_reseptur['jenis_racikan'])
                             && ($value['rke'] == $data_reseptur['rke'])) {
                            $message = Yii::t('fe', 'Tidak bisa menambahkan obat yang sama!');
                            throw new Exception($message, 1);
                        }
                    }
                }

                $session_reseptur[$pendaftaran_id.$cppt_id][] = [
                    'session_key' => $session_key,
                    'resepturdetail_id' => '',
                    'jenis_racikan' => $data_reseptur['jenis_racikan'],
                    'nama_racikan' => $data_reseptur['nama_racikan'],
                    'etiket' => isset($data_reseptur['etiket']) ? $data_reseptur['etiket'] : null,
                    'rke' => $data_reseptur['rke'],
                    'signa_reseptur' => $data_reseptur['signa_reseptur'],
                    'obatalkes_id' => $data_reseptur['obatalkes_id'][$i],
                    'qty_reseptur' => $data_reseptur['qty_reseptur'][$i],
                    'satuankecil_id' => $data_reseptur['satuankecil_id'][$i],
                    'satuankecil_nama' => $data_reseptur['satuankecil_nama'][$i],
                    'satuandefault_id' => $data_reseptur['satuandefault_id'][$i],
                    'satuandefault_nama' => $data_reseptur['satuandefault_nama'][$i],
                    'hargasatuan_reseptur' => $data_reseptur['harga_satuan'][$i],
                    'nilai_konversi' => $data_reseptur['nilai_konversi'][$i],
                    'total_konversi' => ($data_reseptur['qty_reseptur'][$i]) * ($data_reseptur['nilai_konversi'][$i]),
                    'harga_konversi' => $data_reseptur['hargasatuan_reseptur'][$i],
                    'harganetto_reseptur' => $data_reseptur['harganetto_reseptur'][$i],
                    'stok_sisa' => $data_reseptur['stok_sisa'][$i],
                    'harga_jual' => $data_reseptur['harga_jual'][$i],
                    'iter' => $postIter,
                ];
                $session_key++;
            }
            $session->set('pemeriksaan_reseptur', $session_reseptur);
        } else {
            if (isset($session_reseptur[$encryptedId])) {
                foreach ($session_reseptur[$encryptedId] as $key => $value) {
                    if (($value['obatalkes_id'] == $data_reseptur['obatalkes_id'])
                            && ($value['jenis_racikan'] == $data_reseptur['jenis_racikan'])) {
                            $message = Yii::t('fe', 'Tidak bisa menambahkan obat yang sama!');
                            throw new Exception($message, 1);
                    }
                }
            }

            if(!isset($session_reseptur[$encryptedId])) {
                $session_reseptur[$encryptedId] = $session_reseptur;
            }

            $session_reseptur[$encryptedId][] = [
                'session_key' => $session_key,
                'resepturdetail_id' => '',
                'jenis_racikan' => $data_reseptur['jenis_racikan'],
                'nama_racikan' => $data_reseptur['nama_racikan'],
                'etiket' => $data_reseptur['etiket'],
                'rke' => $data_reseptur['rke'],
                'signa_reseptur' => $data_reseptur['signa_reseptur'],
                'obatalkes_id' => $data_reseptur['obatalkes_id'],
                'qty_reseptur' => $data_reseptur['qty_reseptur'],
                'satuankecil_id' => $data_reseptur['satuankecil_id'],
                'satuankecil_nama' => $data_reseptur['satuankecil_nama'],
                'satuandefault_id' => $data_reseptur['satuandefault_id'],
                'satuandefault_nama' => $data_reseptur['satuandefault_nama'],
                'stok_sisa' => $data_reseptur['stok_sisa'],
                'hargasatuan_reseptur' => $data_reseptur['harga_satuan'],
                'nilai_konversi' => $data_reseptur['nilai_konversi'],
                'total_konversi' => $data_reseptur['qty_reseptur'] * $data_reseptur['nilai_konversi'],
                'harga_konversi' => $data_reseptur['harga_konversi'],
                'harganetto_reseptur' => $data_reseptur['harganetto_reseptur'],
                'harga_jual' => $data_reseptur['harga_jual'],
                'iter' => $postIter,
            ];

            $session->set('pemeriksaan_reseptur', $session_reseptur);
            $session->set('pemeriksaan_reseptur_db', []);
        }
    }

    private function deleteExistReseptur($resepturdetail_id)
    {
        try {
            $response = $this->_restIgd->request('DELETE', 'asesmen-dpjp/delete-reseptur',[
                'query' => ['id' => $resepturdetail_id ]
            ]);
            $response = json_decode($response->getBody(),true);
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionEditSoap($cpptId) {
        try {
            $params = Yii::$app->request;
            $post = $params->post('CpptForm');
            $post['tgl_cppt'] = (date_create_from_format('d/m/Y H:i:s', $post['tgl_cppt'])) ? date_format(date_create_from_format('d/m/Y H:i:s', $post['tgl_cppt']), 'Y-m-d H:i:s') : $post['tgl_cppt'];
            $post['subject'] = DocoHelpers::purifyText(ArrayHelper::getValue($post, 'subject'));
            $post['object'] = DocoHelpers::purifyText(ArrayHelper::getValue($post, 'object'));
            $post['a_diag_utama_text'] = DocoHelpers::purifyText(ArrayHelper::getValue($post, 'a_diag_utama_text'));
            $post['a_diag_penyerta'] = DocoHelpers::purifyText(ArrayHelper::getValue($post, 'a_diag_penyerta'));
            $post['planning'] = DocoHelpers::purifyText(ArrayHelper::getValue($post, 'planning'));
            $post['a_diag_utama'] = DocoHelpers::purifyText(ArrayHelper::getValue($post, 'a_diag_utama'));
            $post['instruksi'] = DocoHelpers::purifyText(ArrayHelper::getValue($post, 'instruksi'));
            $post['catatan_dokter'] = DocoHelpers::purifyText(ArrayHelper::getValue($post, 'catatan_dokter'));
            $data_pasien = $this->_data_pasien;
            $optDiagUtama = [];
            $valDiagPnyrt = $optDiagPnyrt = [];
            $model = new CpptForm;
            $model->tanggalPendaftaran = ArrayHelper::getValue($this->_data_pasien, 'tgl_pendaftaran', null);
            $model->attributes = $post;
            $model->pendaftaran_id = $data_pasien['pendaftaran_id'];
            $model->pasien_id = $data_pasien['pasien_id'];
            $model->pegawai_id = $this->_pegawai_id;
            $model->ruangan_id = $data_pasien['ruangan_id'];
            $cpptId = DocoHelpers::decrypt($cpptId);
            $model->tgl_cppt = date('Y-m-d H:i:s', strtotime($model->tgl_cppt));
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $todayDate = date('Y-m-d');
            $formDate = date('Y-m-d H:i:s', strtotime($post['tgl_cppt']));
            $loginpemakai_id = Yii::$app->docoVars->user("id");

            $post['subject'] = $post['subject'];
            $post['object'] = $post['object'];
            $post['planning'] = $post['planning'];

            if (!$post['a_diag_penyerta']) {
                $model->a_diag_penyerta = [];
            }
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
            if ($model->validate()) {
                $result = $this->guzzleExec($this->_restIgd, [
                    'method' => 'POST',
                    'url' => 'asesmen-dpjp/edit-soap',
                    'payload' => [
                        'form_params' => $post,
                        'query' => compact('cpptId','loginpemakai_id')
                    ]
                ]);

                if (!empty($post['subject']) && !empty($post['object']) && !empty($post['a_diag_utama']) && !empty($post['planning']) && $formDate >= $todayDate) {
                    $cache = Yii::$app->cache;
                    $cacheData = $cache->get('data-pasien-igd-' . $model->pendaftaran_id);

                    $cacheData['cppt']['a_diag_utama'] = $result['data']['a_diag_utama'];

                    $cache->set('data-pasien-igd-' . $model->pendaftaran_id, $cacheData, 3600);

                    return $this->helper->response($result, 200);
                } else {
                    $result['data']['a_diag_utama'] = '';

                    return $this->helper->response($result, 200);
                }
            } else {
                return $this->responseJson(422, 'Silakan cek kembali data yang disubmit.', $this->mapErrorForm($model->errors, $formName));
            }
        } catch (RequestException $e) {
            $this->logError($e);
            return $this->responseJson(500, 'Terjadi kesalahan pada server');
        } catch (\Exception $e) {
            $this->logError($e);
            return $this->responseJson(500, 'Terjadi kesalahan pada server');
        }
    }
    private function validateResepturForm($post, $data_pasien, $pendaftaran_id_encrypt)
    {
        $resepturForm = new ResepturForm;
        $resepturForm->attributes = $post['ResepturForm'] ? $post['ResepturForm'] : [];
        $resepturForm['pasien_id'] = $data_pasien['pasien_id'];
        $resepturForm['pendaftaran_id'] = DocoHelpers::decrypt($pendaftaran_id_encrypt);
        $resepturForm['tglreseptur'] = ($resepturForm['tglreseptur']) ? date('Y-m-d', strtotime($resepturForm['tglreseptur'])) : date('Y-m-d');

        $resepturForm['ruanganreseptur_id'] = $this->_ruangan_id;

        if ($resepturForm['ruangan_id'] == '') {
            $resepturForm['ruangan_id'] = $post['ruangan_id'];
        }

        if(!$resepturForm->validate()) {
            $formName = substr(strrchr(get_class($resepturForm), "\\"), 1);
            $response = $resepturForm->errors;
            return DocoHelpers::response($response, 422, $formName);
        }
        else {
            return $resepturForm->attributes;
        }
    }

    public function actionFormModal($type)
    {
        $konfigSystem = $this->actionGetKonfigSystem();
        $orderBedahTanpaTindakan = $konfigSystem['order_bedah_tanpa_tindakan'];
        $inst_id = DocoConstants::INSTALASI_ID_RD;
        $modelPenunjang = new InstruksiPenunjangForm;
        // is_puasa
        // get list ruangan by type
        $id = Yii::$app->request->get('id');
        $user = Yii::$app->session->get('user_identity');
        if($user['kelompokpegawai_id'] != DocoConstants::KELOMPOK_MEDIS){
            $user['nama_pegawai'] = $this->_data_pasien['nama_pegawai'];
        // } else {
        //     if( $this->_data_pasien['dokter_jaga_id'] != $user['id_pegawai'] ) {
        //         $user['nama_pegawai'] = $this->_data_pasien['dokter_jaga'];
        //         $user['id_pegawai'] =  $this->_data_pasien['dokter_jaga_id'];
        //     }
        }
        $url = [
            'form-action' => '/igd/pemeriksaan-igd/simpan-terapi-penunjang?id='.$id,
            'modal-pemeriksaan' => '/igd/pemeriksaan-igd/modal-pemeriksaan-penunjang?id='.$id.'&instalasi_id=#instalasi_id#&ruangan_id=#ruangan_id#&kelaspelayanan_id=#kelaspelayanan_id#&penjamin_id=#penjamin_id#',
            'jadwal-operasi' => '/igd/pemeriksaan-igd/modal-jadwal-operasi?id='.$id
        ];
        $instalasiId = null;
        $penjaminId = $this->_data_pasien['penjamin_id']; //get from PemeriksaanController@init
        $kelaspelayananId = $this->_data_pasien['kelaspelayanan_id']; //get from PemeriksaanController@init
        switch ($type) {
            case 'laboratorium':
                $instalasiId = DocoConstants::INSTALASI_ID_LAB;
                break;
            case 'radiologi':
                $instalasiId = DocoConstants::INSTALASI_ID_RAD;
                break;
            case 'bedah':
                $instalasiId = DocoConstants::INSTALASI_ID_BEDAH;
                break;
            default:
                break;
        }
        $wardDropdown = [];
        if (!empty($instalasiId)) {
            $wardData = $this->guzzleExec($this->_restIgd, [
                'url' => 'allow/get-list-ruangan',
                'payload' => [
                    'query' => [
                        'instalasi_id' => $instalasiId
                    ]
                ]
            ]);
            foreach ($wardData as $value)
                $wardDropdown[$value['ruangan_id']] = $value['ruangan_nama'];
        }

        $latest_cppt = $this->_restIgd->get('asesmen-dpjp/get-latest-cppt', [
                'query' => [
                        'pendaftaran_id' => PelayananHelpers::decryptId($id),
                        'is_dokter' => true, // get user login is dokter or not
                    ]
            ]);

        $latest_cppt = json_decode($latest_cppt->getBody(), true);
        $latest_cppt = isset($latest_cppt['response']['data']) ? $latest_cppt['response']['data'] : [];

        $opt_diagnosa_utama = !empty($latest_cppt['a_diag_utama']) ? json_decode($latest_cppt['a_diag_utama'], true) : [];
        $modelPenunjang->diagnosa_utama_text = isset($opt_diagnosa_utama['text']) ? $opt_diagnosa_utama['text'] : ' - ';
        $opt_diagnosa_penyerta = !empty($latest_cppt['a_diag_penyerta']) ? json_decode($latest_cppt['a_diag_penyerta'], true) : [];

        $modelPenunjang->pegawai_id = Yii::$app->docoVars->user("kelompokpegawai_id") != DocoConstants::KELOMPOK_MEDIS ? $this->_data_pasien['dokter_jaga_id'] : $user['id_pegawai'];
        $modelPenunjang->instalasi_id = $instalasiId;
        $modelPenunjang->pendaftaran_id = $this->helper->decrypt($id);
        $modelPenunjang->cppt_id = Yii::$app->request->get('cppt_id', null);
        $modelPenunjang->has_jadwal = 0;
        $modelPenunjang->ruangan = $this->_data_pasien['ruangan_id'];

        $kelasTagihan = ArrayHelper::getValue($this->_data_pasien, 'kelaspelayanan_nama', '-');
        $isTitipan = ArrayHelper::getValue($this->_data_pasien, 'is_pasientitipan', false);
        if ($isTitipan) {
            $kelasTagihan = ArrayHelper::getValue($this->_data_pasien, 'kelas_ditagihkan_nama', '-');
        }

        return $this->renderAjax('//cppt/penunjang/__modal', [
            'drperujukId' => $user['id_pegawai'],
            'type' => $type,
            'user' => $user,
            'model' => $modelPenunjang,
            'wards' => $wardDropdown,
            'penjaminId' => $penjaminId,
            'kelaspelayananId' => $kelaspelayananId,
            'id' => $id,
            'url' => $url,
            'inst_id' => $inst_id,
            'dokter_url' => '/igd/pemeriksaan-igd/list-dokter-perujuk',
            'dokterList' => [
                $modelPenunjang->pegawai_id => $user['nama_pegawai'],
            ],
            'opt_diagnosa_utama' => $opt_diagnosa_utama,
            'opt_diagnosa_penyerta' => $opt_diagnosa_penyerta,
            'orderBedahTanpaTindakan' => $orderBedahTanpaTindakan,
            'infoPasien' => [
                'nama_pasien' => ArrayHelper::getValue($this->_data_pasien, 'nama_pasien', '-'),
                'penjamin_nama' => ArrayHelper::getValue($this->_data_pasien, 'penjamin_nama', '-'),
                'kelaspelayanan_nama' => $kelasTagihan,
            ],
        ]);
    }

    // public function actionFormModalDietPasien($id)
    // {
    //     try{
    //         $pendaftaran_id = DocoHelpers::decrypt($id);
    //         $model = new DietPasienForm;
    //         $model->pendaftaran_id = $pendaftaran_id;
    //         $pegawai_id = $this->_pegawai_id;

    //         if (Yii::$app->request->post()) {
    //             $request = Yii::$app->request->post('DietPasienForm');
    //             $model->attributes = $request;

    //             if ($model->validate()) {
    //                 $post = [
    //                     'pendaftaran_id' => $model->pendaftaran_id,
    //                     'catatan_diet' => $model->catatan_diet,
    //                     'peg_pemesan_id' => $pegawai_id
    //                 ];

    //                 return $this->guzzleExec($this->_restIgd, [
    //                     'method' => 'POST',
    //                     'url' => 'pemeriksaan-igd/save-diet-pasien',
    //                     'payload' => [
    //                         'form_params' => $post
    //                     ],
    //                     'returnResponse' => true
    //                 ]);
    //             } else {
    //                 $formName = substr(strrchr(get_class($model), "\\"), 1);
    //                 $response = $model->errors;
    //                 return DocoHelpers::response($response, 422, $formName);
    //             }
    //         }

    //         return $this->renderAjax('/pemeriksaan-igd/__modal_diet_pasien', get_defined_vars());
    //     } catch (RequestException $e) {
    //         $this->logError($e);
    //         throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
    //     } catch (\Exception $e) {
    //         $this->logError($e);
    //         throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
    //     }
    // }

    public function actionListDokterPerujuk() {
        $dokterList = $this->guzzleExec($this->_restIgd, [
            'url' => 'allow/list-dokter-perujuk',
            'method' => 'get',
            'payload' => [
                'query' => Yii::$app->request->get()
            ],
        ]);
        return DocoHelpers::response($dokterList);
    }

    public function actionCpptFilters($term, $pasien_id, $type)
    {
        $response = $this->guzzleExec($this->_restIgd, [
            'url' => 'asesmen-dpjp/get-filter-cppt?',
            'method' => 'get',
            'payload' => [
                'query' => [
                    'pasien_id' => $pasien_id, 'term' => $term, 'type' => $type
                ]
            ]
        ]);

        return $this->responseJson(200, 'Data berhasil diambil!', $response);
    }

    public function actionGetKonfigSystem()
    {
        $response = $this->guzzleExec($this->_restIgd, [
            'url' => 'allow/get-konfig-system',
            'method' => 'get',
        ]);
        return $response;
    }

    public function actionDeleteCppt() {
        $req = Yii::$app->request;
        $cppt_id = $req->get('cppt_id');
        $pendaftaran_id = $req->get('pendaftaran_id');
        $tipe = $req->get('tipe');
        $userIdentity = Yii::$app->session->get('user_identity');
        $pegawaiId = ArrayHelper::getValue($userIdentity, 'loginpemakai_id');
        $response = $this->guzzleExec($this->_restIgd, [
            'url' => 'asesmen-dpjp/delete-cppt',
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

    public function actionShowPopupPdf()
    {
        $title = 'Cetak PDF CPPT IGD';
        $randString = DocoHelpers::generateRandomString();
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        $payload = [
            'pendaftaran_id' => PelayananHelpers::decryptId($request->get('id', '')),
            'filterruangan_id' => $request->get('ruangan_id', null),
            'filterpegawai_id' => $request->get('pegawai_id', null),
            'filter_kelompokpegawai_id' => $request->get('kelompokpegawai_id', null),
            'tgl_cppt' => $request->get('tanggal_cppt', null),
            'nama_usercetak' => $this->_user_identity['nama'],
            'id_usercetak' => $this->_user_identity['id_pegawai'],
            'order' => $yiiRestfulParams['order'],
            'only_attributes' => true,
        ];

        Yii::$app->session->setFlash($randString, $payload);
        $process_path = '/igd/pemeriksaan-igd/process-sync-pdf';
        $download_pdf_path = '/igd/pemeriksaan-igd/download-pdf';

        return $this->renderAjax('//cppt/_modal_progress', compact('randString', 'title', 'process_path', 'download_pdf_path'));
    }

    public function actionProcessSyncPdf($randString)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);
        $session['randString'] = $randString;
        return $this->guzzleExec($this->_restIgd, [
            'url' => "asesmen-dpjp/export-pdf-cppt-bgproses",
            'payload' => [
                'query' => $session
            ],
        ]);
    }

    public function actionDownloadPdf()
    {
        $request = Yii::$app->request;
        $filename = $request->get('fileName', null);
        $fileDownloads = 'Cetakan CPPT IGD.pdf';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restIgd->get('asesmen-dpjp/download-file-pdf', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::previewPdf($path);
    }
}
