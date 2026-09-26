<?php

namespace Doco\antrian\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use Doco\antrian\models\DisplayAntrianForm;
use yii\web\UploadedFile;
use app\components\DocoDatatableHelper;

use app\components\DocoConstants;
use yii\helpers\Json;
use app\modules\master\models\LoketForm;

class PanggilAntrianController extends DocoController
{
    protected $_title = "Display Antrian";
    protected $_module = '/antrian/display-antrian/';
    protected $_restAntrian;
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restAntrian = Yii::$app->docoRest->antrian;
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionGetLoket()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $response = $this->_restAntrian->request('POST', 'display-antrian/get-loket',['form_params' => $post]);
        $body = json_decode($response->getBody(), true);

        return DocoHelpers::response($body, false, true);
    }


    public function actionPilihLoket($jenisantrian_id = null, $redirect = null, $instalasi_id = null)
    {
        $jenisantrian_id = DocoHelpers::decrypt($jenisantrian_id);
        $redirect = DocoHelpers::decrypt($redirect);
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");

        if ($jenisantrian_id == DocoConstants::JA_FAR) {
            $requests = $this->_restAntrian->get('dashboard/pilih-loket?jenisantrian_id=' . $jenisantrian_id . '&instalasi_id=' . $instalasi_id . '&ruangan_id=' . $ruangan_id);
        } else {
            $requests = $this->_restAntrian->get('dashboard/pilih-loket?jenisantrian_id=' . $jenisantrian_id . '&instalasi_id=' . $instalasi_id);
        }
        $response = json_decode($requests->getBody(), true);
        if($response['metadata']['status'] != 422) {
            $listResponses = $response['response'];
            $title = Yii::t('fe', 'Pilih loket');

            return $this->render('pilih-loket', get_defined_vars());
        }
        else {

            return DocoHelpers::response($response);
        }
    }

    public function actionSetLoket()
    {
        $session = Yii::$app->session;
        $active_workspace = $session->get('active_workspace');
        $post = Yii::$app->request->post();
        try {
            $loket_id = $post;
            $loginpemakai_id = Yii::$app->docoVars->user("id");
            $params = [
                'loginpemakai_id' => $loginpemakai_id,
                'loket_id' => $loket_id,
            ];
            $request = $this->_restAntrian->post('dashboard/set-loket', [
                'form_params' => $params
            ]);
            $body = json_decode($request->getBody(), true);
            $response = $body['response'];

            // use for all antrian biar engga perlu lagi hit backend, expired mengikuti active_workspace
            $active_workspace['loket'] = [$loginpemakai_id => $response];
            $session->set('active_workspace', $active_workspace);

            return DocoHelpers::response('Loket berhasil di set.', 200);
        } catch (Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }

    }


    public function actionIndex($jenisantrian_id = null)
    {
        $session = Yii::$app->session;
        $active_workspace = $session->get('active_workspace');
        $loket_nama = '';
        $ruanganId = $active_workspace['ruangan_id'];
        $ruanganNama = $active_workspace['ruangan_name'];
        $isPenunjang = false;

        $modulId = $active_workspace['modul_id'];
        $instalasiId = $active_workspace['instalasi_id'];

        try {
            $param = DocoConstants::MODUL[$modulId];
        } catch (\yii\base\ErrorException $e) {
            $param = 'rajal';
        }

        switch ($param) {
            case "rajal":
                $jenisantrian_id = DocoConstants::JA_PDN;
                break;
            case "farmasi":
                $jenisantrian_id = DocoConstants::JA_FAR;
                break;
            case "laboratorium":
            case "radiologi":
                $jenisantrian_id = DocoConstants::JA_PNG;
                break;
            default:
                $jenisantrian_id = DocoConstants::JA_PDN;
                break;
        }

        $loginpemakai_id = Yii::$app->docoVars->user("id");
        $requests = $this->_restAntrian->get('allow/get-current-loket?loginpemakai_id=' . $loginpemakai_id . '&jenisantrian_id=' .$jenisantrian_id . '&instalasi_id=' .$instalasiId);
        $response = json_decode($requests->getBody(), true);
        $response = $response['response'];
        if (!$response) {
            return $this->actionPilihLoket($jenisantrian_id,null,$instalasiId);
        } else {
            $session = Yii::$app->session;
            $active_workspace = $session->get('active_workspace');
            $active_workspace['loket'] = [$loginpemakai_id => $response];
            $session->set('active_workspace', $active_workspace);

            $loket_nama = $response['loket_nama'];
            $loket_id = $response['loket_id'];
        }

        $title = Yii::t('fe', 'Panggil Antrian');

        if ($jenisantrian_id == DocoConstants::JA_FAR) {

            // dump($ruanganId);exit;

            $request = $this->_restAntrian->get('allow-antrian/component-antrian-farmasi?ruangan_id=' . $ruanganId);
            $body = json_decode($request->getBody(), true);
            $dt_antrian = $body['response'];

            $status_ambil = DocoConstants::VAR_SF_3;


            return $this->render('panggil_antrian_farmasi', get_defined_vars());
        } else {
            return $this->render('panggil_antrian', get_defined_vars());
        }

    }

    public function actionComponentAntrian()
    {
        $datatables_antrian = [];
        $loginpemakai_id = Yii::$app->docoVars->user("id");

        $loket_id = Yii::$app->docoVars->workspace("loket")[$loginpemakai_id]['loket_id'];
        $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");

        try {
            $request = $this->_restAntrian->get('allow-antrian/component-antrian?loket_id=' . $loket_id . '&instalasi_id=' . $instalasi_id);
            $body = json_decode($request->getBody(), true);
            $data = $body['response'];

            $data_antrian = !empty($data['data_antrian_terlewat'])
                ? $data['data_antrian_terlewat']
                : [];
            $datatables_antrian['header'] = [
                Yii::t('fe', 'No antrian'),
                Yii::t('fe', 'Aksi'),
            ];
            $datatables_antrian['field'] = [
                'no_antrian',
                'aksi'
            ];
            if (!empty($data_antrian)) {
                foreach ($data_antrian as $key => $value) {
                    $primaryKey = $value['antrian_id'];
                    $value['aksi'] = '';
                    $value['aksi'] .= Html::button(
                        Yii::t('fe', 'Pilih'),
                        [
                            'class' => 'btn btn-success btn-xs btnPilihLewat',
                            'id' => 'btnPilihLewat-'. $value['antrian_id'],
                            'onclick' => 'pilihLewat(this)',
                            'data-antrian_id' => $primaryKey,
                            'style' => "margin-left:5px",
                        ]
                    );
                    $value['aksi'] .= Html::button(
                        Yii::t('fe', 'Batal'),
                        [
                            'class' => 'btn btn-danger btn-xs btnBatalLewat',
                            // 'id' => 'btnPanggilUlang',
                            'data-antrian_id' => $primaryKey,
                            'style' => "margin-left:5px",
                            'onclick' => 'batalLewat(this)',
                            'data-confirm-message' => \Yii::t('fe', 'Apakah anda yakin untuk membatalkan data ini?'),
                            // 'action' => Url::home().'pendaftaran/daftar/batal?antrian_id='.$primaryKey,
                        ]
                    );
                    $datatables_antrian['detail'][] = $value;
                }
            }
            $json_datatables = json_encode($datatables_antrian);

            $result = [
                'data_antrian_terlewat' => $datatables_antrian,
                'count_sisa_antrian' => $data['count_sisa_antrian'],
                'limit_antrian' => $data['limit_antrian']
            ];

            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionCountSisaAntrian()
    {
        try {
            $loginpemakai_id = Yii::$app->docoVars->user("id");
            $loket_id = Yii::$app->docoVars->workspace("loket")[$loginpemakai_id]['loket_id'];
            $request = $this->_restAntrian->get('allow-antrian/count-sisa-antrian?status=belum_panggil&loket_id=' . $loket_id);
            $body = json_decode($request->getBody(), true);
            $response = $body['response']['count'];
            return DocoHelpers::response($response, 200);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionNextAntrian()
    {
        try {
            $loginpemakai_id = Yii::$app->docoVars->user("id");
            $loket_id = Yii::$app->docoVars->workspace("loket")[$loginpemakai_id]['loket_id'];
            $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
            $request = $this->_restAntrian->get('allow-antrian/next?loket_id=' . $loket_id . '&instalasi_id=' . $instalasi_id);
            $body = json_decode($request->getBody(), true);
            $response = $body['response'];

            $session = Yii::$app->session;
            $active_workspace = $session->get('active_workspace');
            $loket = Yii::$app->docoVars->workspace("loket")[$loginpemakai_id]['loket_nourut'];
            if(array_key_exists('teks_panggil',$response)){

                $response['teks_panggil'] .= ' Loket';
                $response['teks_panggil'] .= DocoHelpers::Terbilang((int)$loket);
                $response['teks_panggil'] = trim($response['teks_panggil']);
            }


            // set ke display antrian
            $data_display["panggil_antrian"] = [
                'no_antrian' => empty($response['data']['no_antrian'])
                    ? '-'
                    : $response['data']['no_antrian'],
                'no_loket' => $loket . ' (' . DocoHelpers::Terbilang((int)$loket) . ')',
                'loket_id' => empty($response['data']['loket_id'])
                    ? '-'
                    : $response['data']['loket_id'],
                'text_panggil' => empty($response['teks_panggil'])
                                ? ''
                                : $response['teks_panggil']
            ];
            $mode = Yii::$app->params->mode;
            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'display-antrian-'.$mode,
                'message' => Json::encode(['data' => $data_display])
            ]);
            // end set display antrian

            return DocoHelpers::response($response, 200);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionPanggilUlang($antrian_id)
    {
        try {
            $request = $this->_restAntrian->get('allow-antrian/panggil-ulang?antrian_id=' . $antrian_id);
            $body = json_decode($request->getBody(), true);
            $response = $body['response'];

            $session = Yii::$app->session;
            $active_workspace = $session->get('active_workspace');
            $loginpemakai_id = Yii::$app->docoVars->user("id");
            $loket = Yii::$app->docoVars->workspace("loket")[$loginpemakai_id]['loket_nourut'];

            $response['teks_panggil'] .= ' Loket';
            $response['teks_panggil'] .= DocoHelpers::Terbilang((int)$loket);
            $response['teks_panggil'] = trim($response['teks_panggil']);

            // set ke display antrian
            $data_display["panggil_antrian"] = [
                'no_antrian' => empty($response['data']['no_antrian'])
                    ? '-'
                    : $response['data']['no_antrian'],
                'no_loket' => $loket . ' (' . DocoHelpers::Terbilang((int)$loket) . ')',
                'loket_id' => empty($response['data']['loket_id'])
                    ? '-'
                    : $response['data']['loket_id'],
                'text_panggil' => empty($response['teks_panggil'])
                                ? ''
                                : $response['teks_panggil']
            ];
            $mode = Yii::$app->params->mode;
            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'display-antrian-'.$mode,
                'message' => Json::encode(['data' => $data_display])
            ]);
            // end set display antrian

            return DocoHelpers::response($response, 200);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionLewati($antrian_id)
    {
        try {
            $request = $this->_restAntrian->get('allow-antrian/lewati?antrian_id=' . $antrian_id);
            $body = json_decode($request->getBody(), true);
            $response = [];
            $response['data'] = $body['response'];
            $response['response'] = [
                'return' => 'disableLewati()',
            ];
            return DocoHelpers::response($response, 200);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionBatal($antrian_id)
    {
        try {
            $request = $this->_restAntrian->get('allow-antrian/batal?antrian_id=' . $antrian_id);
            $body = json_decode($request->getBody(), true);
            $response = [];
            $response['data'] = $body['response'];
            $response['response'] = [
                'return' => 'disableBatal()',
                'title' => "Proses Berhasil",
                'text' => "Data berhasil dibatalkan"
            ];


            return DocoHelpers::response($response, 200);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionPilih($antrian_id)
    {
        try {
            $request = $this->_restAntrian->get('allow-antrian/pilih?antrian_id=' . $antrian_id);
            $body = json_decode($request->getBody(), true);
            $response = $body['response'];

            $loginpemakai_id = Yii::$app->docoVars->user("id");
            $loket = Yii::$app->docoVars->workspace("loket")[$loginpemakai_id]['loket_nourut'];

            $body['response']['teks_panggil'] .= ' Loket';
            $body['response']['teks_panggil'] .= DocoHelpers::Terbilang((int)$loket);
            $body['response']['teks_panggil'] = trim($body['response']['teks_panggil']);

            // set ke display antrian
            $data_display["set_antrian"] = [
                'no_antrian' => empty($response['no_antrian'])
                    ? '-'
                    : $response['no_antrian'],
                'loket_id' => empty($response['loket_id'])
                    ? '-'
                    : $response['loket_id'],
            ];
            $mode = Yii::$app->params->mode;
            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'display-antrian-'.$mode,
                'message' => Json::encode(['data' => $data_display])
            ]);
            // end set display antrian

            return DocoHelpers::response($body, 200);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionListAntrianDilewati()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $post = $request->post();
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $loginpemakai_id = Yii::$app->docoVars->user("id");
            $loket_id = Yii::$app->docoVars->workspace("loket")[$loginpemakai_id]['loket_id'];
            $response = $this->_restPendaftaran->request('GET', 'allow-antrian/list-antrian?status=lewati&loket_id=' . $loket_id);
            $body = json_decode($response->getBody(), true);

            $no = $request->get('start', 0);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = $value['antrian_id'];
                $value['primary'] = $primaryKey;

                $value['aksi'] = '';
                $value['aksi'] .= Html::button(
                    Yii::t('fe', 'Pilih'),
                    [
                        'class' => 'btn btn-success btn-xs btnPilihLewat',
                        'id' => 'btnPilihLewat',
                        'onclick' => 'pilihLewat(this)',
                        'data-antrian_id' => $primaryKey,
                        'style' => "margin-left:5px",
                    ]
                );
                $value['aksi'] .= Html::button(
                    Yii::t('fe', 'Batal'),
                    [
                        'class' => 'btn btn-danger btn-xs btnBatalLewat',
                        // 'id' => 'btnPanggilUlang',
                        'data-antrian_id' => $primaryKey,
                        'style' => "margin-left:5px",
                        'onclick' => 'batalLewat(this)',
                        'data-confirm-message' => \Yii::t('fe', 'Apakah anda yakin untuk membatalkan data ini?'),
                        // 'action' => Url::home().'pendaftaran/daftar/batal?antrian_id='.$primaryKey,
                    ]
                );
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['draw'] = $request->post('draw');
            $result['recordsTotal'] = $body['response']['count'];
            $result['recordsFiltered'] = $body['response']['count'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            echo DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            echo DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionUbahJenisAntrian($loket_id, $jenisantrian_id)
    {
        $loket_id = DocoHelpers::decrypt($loket_id);
        $jenisantrian_id = DocoHelpers::decrypt($jenisantrian_id);
        $model = new loketForm;
        $request = Yii::$app->request;
        if ($request->post()) {
            $model->load($request->post());
            if (!empty($model->konfigantrian_id)) {
                $response = $this->_restMaster->put('loket/ubah-pengambilan-antrian?id='.$loket_id, [
                    'form_params' => $model->attributes
                ]);
                $response = json_decode($response->getBody(),true);
                return DocoHelpers::response($response,false,'LoketForm');
            } else {
                return DocoHelpers::response($model->errors,422,'LoketForm');
            }
        } else {
            $response = $this->_restMaster->get('loket/view?id='.$loket_id);
            $body = json_decode($response->getBody(), TRUE);
            if($body['metadata']['status'] !== 422) {
                $attributes = $body['response'];
                $model->attributes = $attributes['loket'];
                $val_konfig = DocoHelpers::encrypt(json_encode($attributes['val_konfig']));
            }
            // return DocoHelpers::response($body);
        }

        return $this->renderAjax('ubah-jenis-antrian', get_defined_vars());
    }

    public function actionGetSisaAntrian($id)
    {
        $response = $this->_restMaster->get('allow/get-sisa-antrian?id='.$id);
        $body = json_decode($response->getBody(), true);

        return DocoHelpers::response($body);
    }

    public function actionProsesAntrianFarmasi($antrian_id = null)
    {
        $session = Yii::$app->session;
        $active_workspace = $session->get('active_workspace');
        $loginpemakai_id = Yii::$app->docoVars->user("id");
        $loket_id = Yii::$app->docoVars->workspace("loket")[$loginpemakai_id]['loket_id'];
        $ruanganId = $active_workspace['ruangan_id'];


        try {
            $request = $this->_restAntrian->get('allow-antrian/proses-antrian-farmasi?ruangan_id=' . $ruanganId . '&antrian_id=' . $antrian_id);
            $body = json_decode($request->getBody(), true);

            return DocoHelpers::response($body);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionPanggilAntrianFarmasi($antrian_id = null, $tipe = null)
    {
        try {
            $loginpemakai_id = Yii::$app->docoVars->user("id");
            $loket_id = Yii::$app->docoVars->workspace("loket")[$loginpemakai_id]['loket_id'];
            $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");

            $request = $this->_restAntrian->get('allow-antrian/panggil-antrian-farmasi?antrian_id=' . $antrian_id);

            $body = json_decode($request->getBody(), true);
            $response = $body['response'];

            $session = Yii::$app->session;
            $active_workspace = $session->get('active_workspace');
            $loket = Yii::$app->docoVars->workspace("loket")[$loginpemakai_id]['loket_nourut'];
            if(array_key_exists('teks_panggil',$response)){

                $response['teks_panggil'] .= ' Loket';
                $response['teks_panggil'] .= DocoHelpers::Terbilang((int)$loket);
                $response['teks_panggil'] = trim($response['teks_panggil']);
            }


            // set ke display antrian
            $data_display["panggil_antrian_farmasi"] = [
                'no_antrian' => empty($response['data']['no_antrian'])
                    ? '-'
                    : $response['data']['no_antrian'],
                'no_loket' => $loket . ' (' . DocoHelpers::Terbilang((int)$loket) . ')',
                'loket_id' => empty($response['data']['loket_id'])
                    ? $loket_id
                    : $response['data']['loket_id'],
                'text_panggil' => empty($response['teks_panggil'])
                                ? ''
                                : $response['teks_panggil'],
                'tipe' => $tipe
            ];
            $mode = Yii::$app->params->mode;
            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'display-antrian-'.$mode,
                'message' => Json::encode(['data' => $data_display])
            ]);
            // end set display antrian

            return DocoHelpers::response($response, 200);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }
}