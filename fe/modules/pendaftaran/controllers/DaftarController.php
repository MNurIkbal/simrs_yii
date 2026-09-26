<?php
/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pendaftaran\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\modules\pendaftaran\models\PendaftaranForm;
use app\modules\pendaftaran\models\PasienAdmisiForm;
use app\modules\pendaftaran\models\RujukanForm;
use app\modules\pendaftaran\models\KunjunganForm;
use app\modules\pendaftaran\models\BpjsNewForm;
use app\modules\pendaftaran\models\IgdForm;
use app\modules\pendaftaran\models\PjpasienForm;
use app\modules\pendaftaran\models\AsuransiForm;
use app\modules\pendaftaran\models\PasienForm;
use app\modules\master\models\PenanggungjawabForm;
use app\modules\master\models\CaraBayarForm;
use app\modules\pendaftaran\models\LoketForm;
use app\modules\pendaftaran\models\TipePasienForm;
use app\modules\pendaftaran\models\ProgramTerapiForm;
use GuzzleHttp\Exception\RequestException;
use yii\web\UploadedFile;
use yii\helpers\Json;
use app\modules\pendaftaran\models\PencarianIdentitasForm;

use app\modules\pendaftaran\components\traits\PendaftaranTrait;

class DaftarController extends DocoController
{
    use PendaftaranTrait;

    protected $_title = 'Pendaftaran';
    protected $_module = '/pendaftaran/pendaftaran';
    protected $_moduleRedirect = '/pendaftaran/pendaftaran';
    protected $allowAction = ['*'];
    protected $_restPendaftaran;
    protected $_restAntrian;
    protected $_chace_loket;
    const COMPLETE = 'complete';
    const INCOMPLETE = 'incomplete';
    const DUPLIKASI = 'duplikasi';
    const NORM = 'no_rekam_medik';

    public function init()
    {
        parent::init();
        // $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;
        $this->_restAntrian = Yii::$app->docoRest->antrian;
        $loginpemakai_id = Yii::$app->docoVars->user("id");
        $this->_chace_loket = 'loket-' . $loginpemakai_id;
    }

    public function actionPilihLoket($jenisantrian_id = null, $redirect = null)
    {
        $requests = $this->_restPendaftaran->get('pendaftaran/pilih-loket?jenisantrian_id='.$jenisantrian_id);
        $response = json_decode($requests->getBody(), true);
        $listResponses = $response['response'];

        $title = Yii::t('fe', 'Pilih loket');

        return $this->render('pilih-loket', get_defined_vars());
    }

    public function actionSetLoket()
    {
        $session = Yii::$app->session;
        $active_workspace = $session->get('active_workspace');
        $post = Yii::$app->request->post();
        try {
            $loket_id = $post['loket_id'];
            $loginpemakai_id = Yii::$app->docoVars->user("id");
            $params = [
                'loginpemakai_id'=>$loginpemakai_id,
                'loket_id'=>$loket_id,
            ];

            $request = $this->_restPendaftaran->post('pendaftaran/set-loket', [
                'form_params' => $params
            ]);
            $body = json_decode($request->getBody(),TRUE);


            $response = $body['response'];

            if ($body['metadata']['status'] == 500) {
                $message = $response['message'];
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

            // use for all antrian biar engga perlu lagi hit backend, expired mengikuti active_workspace
            $active_workspace['loket'] = [$loginpemakai_id=>$response];
            $session->set('active_workspace', $active_workspace);

            return DocoHelpers::response('Loket berhasil di set.', 200);
        } catch (Exception $e) {
             return DocoHelpers::response(['message' => $e->getMessage()],500);
        }

    }

    public function actionPanggilAntrian($jenisantrian_id = null)
    {
        // $requests = $this->_restPendaftaran->get('pendaftaran/pilih-loket?jenisantrian_id=' . $jenisantrian_id);
        // $response = json_decode($requests->getBody(), true);
        // $listResponses = $response['response'];

        $session = Yii::$app->session;
        $active_workspace = $session->get('active_workspace');
        $loket_nama = '';
        $ruanganId = $active_workspace['ruangan_id'];
        $ruanganNama = $active_workspace['ruangan_name'];
        $isPenunjang = false;
        try {
            $param = DocoConstants::PARAM_DFTR[$ruanganId];
        } catch (\yii\base\ErrorException $e) {
            $param = 'rajal';
        }

        if ($param == 'rajal' || $param == 'penunjang') {
            $loginpemakai_id = Yii::$app->docoVars->user("id");
            $requests = $this->_restPendaftaran->get('allow/get-current-loket?loginpemakai_id=' . $loginpemakai_id);
            $response = json_decode($requests->getBody(), true);
            $response = $response['response'];
            if (!$response) {
                // penambahan parameter untuk penentuan loket : ali.padilah@docotel.com
                if ($param == 'rajal') {
                    return $this->actionPilihLoket(DocoConstants::JA_PDN);
                } else {
                    return $this->actionPilihLoket(DocoConstants::JA_PNG);
                }

            } else {
                $session = Yii::$app->session;
                $active_workspace = $session->get('active_workspace');
                $active_workspace['loket'] = [$loginpemakai_id => $response];
                $session->set('active_workspace', $active_workspace);

                $loket_nama = $response['loket_nama'];
            }
        }


        $jenisantrian_id = DocoConstants::JA_PDN;

        $title = Yii::t('fe', 'Panggil Antrian');

        return $this->render('panggil_antrian', get_defined_vars());
    }

    public function actionPilihAntrian()
    {
        try {
            // $modelAntrian = new Antrian;

            return $this->renderAjax('pilih-antrian', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionNextAntrian()
    {
        try {
            $loginpemakai_id = Yii::$app->docoVars->user("id");
            $loket_id = Yii::$app->docoVars->workspace("loket")[$loginpemakai_id]['loket_id'];
            $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
            $request = $this->_restPendaftaran->get('allow-antrian/next?loket_id=' . $loket_id . '&instalasi_id=' . $instalasi_id);
            $body = json_decode($request->getBody(),TRUE);
            $response = $body['response'];
            $session = Yii::$app->session;
            $active_workspace = $session->get('active_workspace');
            $loket = Yii::$app->docoVars->workspace("loket")[$loginpemakai_id]['loket_nourut'];
            if(array_key_exists('teks_panggil',$response)){

                $response['teks_panggil'] .= ' Loket';
                $response['teks_panggil'] .= DocoHelpers::terbilang((int)$loket);
                $response['teks_panggil'] = trim($response['teks_panggil']);
            }

            // set ke display antrian
            $data_display["panggil_antrian"] = [
                'no_antrian' => empty($response['data']['no_antrian'])
                    ? '-'
                    : $response['data']['no_antrian'],
                'no_loket' => $loket,// . ' (' . DocoHelpers::terbilang((int)$loket) . ')',
                'loket_id' => empty($response['data']['loket_id'])
                    ? '-'
                    : $response['data']['loket_id'],
                'text_panggil' => empty($response['teks_panggil'])
                                ? ''
                                : $response['teks_panggil'],
                'jenisantriandetail_id' => $response['data']['jenisantriandetail_id']
            ];
            $mode = Yii::$app->params->mode;
            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'display-antrian-'.$mode,
                'message' => Json::encode(['data' => $data_display])
            ]);
            // end set display antrian

            return DocoHelpers::response($response, 200);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionLimitPanggil()
    {
        try {
            $request = $this->_restPendaftaran->get('allow-antrian/limit-panggil');
            $body = json_decode($request->getBody(),TRUE);
            $response = $body['response'];
            return DocoHelpers::response($response, 200);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionComponentAntrian()
    {
        $datatables_antrian = [];
        $loginpemakai_id = Yii::$app->docoVars->user("id");
        $loket_id = Yii::$app->docoVars->workspace("loket")[$loginpemakai_id]['loket_id'];
        $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
        try {
            $request = $this->_restPendaftaran->get('allow-antrian/component-antrian?loket_id=' . $loket_id . '&instalasi_id=' . $instalasi_id);
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
                        Yii::t('fe', 'Pilih'), [
                            'class' => 'btn btn-success btn-xs btnPilihLewat',
                            'id'=>'btnPilihLewat',
                            'onclick'=>'pilihLewat(this)',
                            'data-antrian_id' => $primaryKey,
                            'style' => "margin-left:5px",
                        ]
                    );
                    $value['aksi'] .= Html::button(
                        Yii::t('fe', 'Batal'), [
                            'class' => 'btn btn-danger btn-xs btnBatalLewat',
                            // 'id' => 'btnPanggilUlang',
                            'data-antrian_id' => $primaryKey,
                            'style' => "margin-left:5px",
                            'onclick'=>'batalLewat(this)',
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
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionCountSisaAntrian()
    {
        try {
            $loginpemakai_id = Yii::$app->docoVars->user("id");
            $loket_id = Yii::$app->docoVars->workspace("loket")[$loginpemakai_id]['loket_id'];
            $request = $this->_restPendaftaran->get('allow-antrian/count-sisa-antrian?status=belum_panggil&loket_id='.$loket_id);
            $body = json_decode($request->getBody(),TRUE);
            $response = $body['response']['count'];
            return DocoHelpers::response($response, 200);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionPanggilUlang($antrian_id)
    {
        try {
            $request = $this->_restPendaftaran->get('allow-antrian/panggil-ulang?antrian_id=' . $antrian_id);
            $body = json_decode($request->getBody(),TRUE);
            $response = $body['response'];

            $session = Yii::$app->session;
            $active_workspace = $session->get('active_workspace');
            $loginpemakai_id = Yii::$app->docoVars->user("id");
            $loket = Yii::$app->docoVars->workspace("loket")[$loginpemakai_id]['loket_nourut'];

            $response['teks_panggil'] .= ' Loket';
            $response['teks_panggil'] .= DocoHelpers::terbilang((int)$loket);
            $response['teks_panggil'] = trim($response['teks_panggil']);

            // set ke display antrian
            $data_display["panggil_antrian"] = [
                'no_antrian' => empty($response['data']['no_antrian'])
                    ? '-'
                    : $response['data']['no_antrian'],
                'no_loket' => $loket,// . ' (' . DocoHelpers::terbilang((int)$loket) . ')',
                'loket_id' => empty($response['data']['loket_id'])
                    ? '-'
                    : $response['data']['loket_id'],
                'text_panggil' => empty($response['teks_panggil'])
                                ? ''
                                : $response['teks_panggil'],
                'jenisantriandetail_id' => $response['data']['jenisantriandetail_id']
            ];
            $mode = Yii::$app->params->mode;
            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'display-antrian-'.$mode,
                'message' => Json::encode(['data' => $data_display])
            ]);
            // end set display antrian

            return DocoHelpers::response($response, 200);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionLewati($antrian_id)
    {
        try {
            $request = $this->_restPendaftaran->get('allow-antrian/lewati?antrian_id=' . $antrian_id);
            $body = json_decode($request->getBody(),TRUE);
            $response = [];
            $response['data'] = $body['response'];
            $response['response'] = [
                'return' => 'disableLewati()',
            ];
            return DocoHelpers::response($response, 200);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionBatal($antrian_id)
    {
        try {
            $request = $this->_restPendaftaran->get('allow-antrian/batal?antrian_id=' . $antrian_id);
            $body = json_decode($request->getBody(),TRUE);
            $response = [];
            $response['data'] = $body['response'];
            $response['response'] = [
                'return' => 'disableBatal()',
                'title' => "Proses Berhasil",
                'text' => "Data berhasil dibatalkan"
            ];
            return DocoHelpers::response($response, 200);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionPilih($antrian_id)
    {
        try {
            $request = $this->_restPendaftaran->get('allow-antrian/pilih?antrian_id=' . $antrian_id);
            $body = json_decode($request->getBody(),TRUE);
            $response = $body['response'];

            // set ke display antrian
            $data_display["set_antrian"] = [
                'no_antrian' =>  empty($response['no_antrian'])
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
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
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
            $response = $this->_restPendaftaran->request('GET', 'allow-antrian/list-antrian?status=lewati&loket_id='.$loket_id);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',0);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = $value['antrian_id'];
                $value['primary'] = $primaryKey;

                $value['aksi'] = '';
                $value['aksi'] .= Html::button(
                    Yii::t('fe', 'Pilih'), [
                        'class' => 'btn btn-success btn-xs btnPilihLewat',
                        'id'=>'btnPilihLewat',
                        'onclick'=>'pilihLewat(this)',
                        'data-antrian_id' => $primaryKey,
                        'style' => "margin-left:5px",
                    ]
                );
                $value['aksi'] .= Html::button(
                    Yii::t('fe', 'Batal'), [
                        'class' => 'btn btn-danger btn-xs btnBatalLewat',
                        // 'id' => 'btnPanggilUlang',
                        'data-antrian_id' => $primaryKey,
                        'style' => "margin-left:5px",
                        'onclick'=>'batalLewat(this)',
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

    public function actionIndex($id_booking = null, $pendaftaranol_id=null)
    {
        if ($pendaftaranol_id) {
            $pendaftaranol_id = DocoHelpers::decrypt($pendaftaranol_id);
        }
        $session = Yii::$app->session;
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        if (!empty($pendaftaran_id)) {
            $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        }

        $active_workspace = $session->get('active_workspace');
        $loket_id = null;
        $loket_nama = '';
        $isPenunjang = 0;
        $ruanganId = $active_workspace['ruangan_id'];

        if($ruanganId == 5 || $pendaftaranol_id) {
            if ($pendaftaranol_id && $ruanganId != 5) {
                throw new \yii\web\HttpException(401,Yii::t("fe","Tidak Ada Akses"));
            }
            $loginpemakai_id = Yii::$app->docoVars->user("id");
            $cache = Yii::$app->cache;
            $keyChace = $this->_chace_loket;
            $response = $this->cacheLoket($keyChace);
            if (empty($response)) {
                return $this->actionPilihLoket(DocoConstants::JA_PDN);
            }

            $loket_id = isset($response['loket_id']) ? $response['loket_id'] : null;
            $loket_nama = isset($response['loket_nama']) ? $response['loket_nama'] : null;

            $updateAntrian = [
                'pendaftaranol_id' => $pendaftaranol_id,
                'status_antrian_jkn' => DocoConstants::STATUS_TUNGGU_ADMISI
            ];

            $requests = $this->_restPendaftaran->post('api/update-admisi-jkn',
                    ['form_params' => $updateAntrian]
                );

            return Yii::$app->runAction('/pendaftaran/daftar-rajal', [
                'pendaftaranol_id' => $pendaftaranol_id,
                'loket_id' => $loket_id
            ]);
        } elseif ($ruanganId == 10) {
            return Yii::$app->runAction('/pendaftaran/daftar-igd');
        } elseif ($ruanganId == 28) {
            $param = 'penunjang';
            $isPenunjang = 1;
            $keyChace = $param .'-'. $this->_chace_loket;
            $response = $this->cacheLoket($keyChace);
            if (empty($response)) {
                return $this->actionPilihLoket(DocoConstants::JA_PNG);
            }
            $loket_id = isset($response['loket_id']) ? $response['loket_id'] : null;
            $loket_nama = isset($response['loket_nama']) ? $response['loket_nama'] : null;
            return Yii::$app->runAction('/pendaftaran/daftar-penunjang',[
                'loket_id' => $loket_id
            ]);
        } else if ($ruanganId == 74) {
           return Yii::$app->runAction('/pendaftaran/daftar-rajal',[
                'params' => 'mcu'
           ]);
        } else {
            return Yii::$app->runAction('/pendaftaran/daftar-ranap', [
                'pendaftaran_id' => $pendaftaran_id,
            ]);
        }
    }

    protected function cacheLoket($keyChace)
    {
        $loginpemakai_id = Yii::$app->docoVars->user("id");
        $cache = Yii::$app->cache;
        if (!empty($cache->get($keyChace))) {
            $response = $cache->get($keyChace);
        } else {
            $requests = $this->_restPendaftaran->get('allow/get-current-loket?loginpemakai_id='.$loginpemakai_id);
            $response = json_decode($requests->getBody(), true);
            $response = $response['response'];
            if (empty($response)) {
                return;
            }
            $cache->set($keyChace, $response);
        }

        $session = Yii::$app->session;
        $active_workspace = $session->get('active_workspace');
        $active_workspace['loket'] = [
            $loginpemakai_id => $response
        ];
        $session->set('active_workspace', $active_workspace);
        return $response;
    }

    public function actionGetInfoPasien($id)
    {
        try {
            $requests = $this->_restPendaftaran->get('allow/get-info-pasien?id='.$id);
            $response = json_decode($requests->getBody(), true);
            // if ($response['response']['photopasien']) {
            //     $path = Yii::getAlias('@webroot').'/media/img/pasien/';
            //     $photo = $path . $response['response']['photopasien'];
            //     $response['response']['photopasien'] = file_exists($photo) ? $response['response']['photopasien'] : null;
            // }
            $newData = [];
            foreach ($response['response'] as $key => $value) {
                $newData['PasienForm['.$key.']'] = $value;
            }
            $result = ['response'=>$response['response'], 'form'=>$newData];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
        } catch (\Exception $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
        }
    }

    public function actionGetInfoPasienBayi($kelahiranbayi_id)
    {
        try {
            $requests = $this->_restPendaftaran->get('allow/get-info-pasien-bayi?kelahiranbayi_id='.$kelahiranbayi_id);
            $response = json_decode($requests->getBody(), true);
            $newData = [];
            foreach ($response['response'] as $key => $value) {
                $newData['PasienForm['.$key.']'] = $value;
            }
            $result = ['response'=>$response['response'], 'form'=>$newData];

            // return $this->render('partial/_pasienBayi_new', get_defined_vars());
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
        } catch (\Exception $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
        }
    }

    public function actionLoadDataBayi($kelahiranbayi_id)
    {
        // try {
            $modelPasien = new PasienForm();
            $requests = $this->_restPendaftaran->get('allow/get-info-pasien-bayi?kelahiranbayi_id='.$kelahiranbayi_id);
            $response = json_decode($requests->getBody(), true);
            $newData = [];
            foreach ($response['response'] as $key => $value) {
                $newData['PasienForm['.$key.']'] = $value;
            }
            $result = ['response'=>$response['response'], 'form'=>$newData];
            $res = $response['response'];
            $modelPasien->agama = $res['agama'];
            $modelPasien->no_identitas_pasien = null;
            // $modelPasien->no_identitas_pasien = $res['no_identitas_pasien'];
            $modelPasien->jenisidentitas = $res['jenisidentitas'];
            $modelPasien->nama_pasien = $res['nama_pasien'];
            $modelPasien->nama_panggilan = "By";
            // $modelPasien->namadepan = $res['nama_panggilan'];
            $modelPasien->namadepan = '';
            $modelPasien->tempat_lahir = '';
            $modelPasien->tanggal_lahir = '';
            $modelPasien->umur = '';
            $modelPasien->jeniskelamin = $res['jeniskelamin_id_bayi'];
            $modelPasien->nama_ibu = $res['nama_pasien'];
            $modelPasien->alamat_pasien = $res['alamat_pasien'];
            $modelPasien->rt = $res['rt'];
            $modelPasien->rw = $res['rw'];
            $modelPasien->propinsi_id = $res['propinsi_id'];
            $modelPasien->kabupaten_id = $res['kabupaten_id'];
            $modelPasien->kecamatan_id = $res['kecamatan_id'];
            $modelPasien->kelurahan_id = $res['kelurahan_id'];
            $instalasi_id = json_encode(DocoConstants::INSTALASI_ID_RI);
            $data = $this->getDataApi($instalasi_id, 2);
            $listResponses = $data['lookup'];
            $listMasters = $data['master'];
            $optionsProv = [];
            foreach ($data['master']['propinsi'] as $key => $propinsi) {
                $optionsProv[$propinsi['propinsi_id']] = ['data-kode'=>$propinsi['kode_propinsi']];
            }
            return $this->renderAjax('partial/_pasienBayi_new', get_defined_vars());
            // return DocoHelpers::response($result);
        // } catch (RequestException $e) {
        //     return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
        // } catch (\Exception $e) {
        //     return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
        // }
    }

    public function actionTambahPasien()
    {
        try {
            $post = Yii::$app->request->post();
            $model = new PasienForm();
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $model->load($post);
            if ($model->validate()) {
                // $model->is_aps = ($model->is_aps == ) ? true : false;
                $image = UploadedFile::getInstance($model, "photopasien");
                if (isset($image->name)) {
                    $ext = end(explode(".", $image->name));
                    $rand = substr(Yii::$app->security->generateRandomString(), 0, 7);
                    $model->photopasien = $model->no_rekam_medik . '-' . $rand . ".{$ext}";
                    $path = \Yii::getAlias('@webroot') . '/media/img/pasien/';
                    if (!$image->saveAs($path . $model->photopasien)) {
                        return DocoHelpers::responseTemplate(500, 'Terjadi Kesalahan simpan gambar');
                    }
                }
                $requests = $this->_restPendaftaran->post(
                    'pasien/create',
                    ['form_params' => $model->attributes]
                );
                $response = json_decode($requests->getBody(), true);
                return DocoHelpers::response($response);
            } else {
                // $errors = DocoHelpers::parseError($model->errors, $formName);
                // return DocoHelpers::response([
                //     'response' => [
                //         'data' => $errors
                //     ]
                // ], 422);
                $response = $model->errors;
                return DocoHelpers::response($response, 422, 'PasienForm');

            }


        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
        } catch (Exception $e) {
            return DocoHelpers::responseJsonString($e->getMessage(),$formName);
        }
    }


    public function actionUbahPasien($id, $param)
    {
        $request = Yii::$app->request;
        $post = Yii::$app->request->post();
        $model = new PasienForm;
        if ($request->post()) {
            $model->load($post['PasienForm']);
            try {
                $image = UploadedFile::getInstance($model, "photopasien");
                if(isset($image->name)){
                    $path = \Yii::getAlias('@webroot').'/media/img/pasien/';
                    $oldFile = $path . $post['PasienForm']['photopasien'];
                    if ($post['PasienForm']['photopasien'] && file_exists($oldFile)) {
                        unlink($oldFile); // hapus file lama
                    }
                    $ext = end(explode(".", $image->name));
                    $rand = substr(Yii::$app->security->generateRandomString(), 0, 7);
                    $post['PasienForm']['photopasien'] = $rand . ".{$ext}";
                    if(!$image->saveAs($path . $post['PasienForm']['photopasien'])){
                        return DocoHelpers::responseTemplate(500,'Terjadi Kesalahan simpan gambar');
                    }
                }

                $requests = $this->_restPendaftaran->post('pasien/update?id='.$id,
                    ['form_params' => $post]
                );
                $response = json_decode($requests->getBody(), true);
                return DocoHelpers::response($response);
            } catch (RequestException $e) {
                echo DocoHelpers::dataTabelsException($e->getMessage());
            } catch (\Exception $e) {
                echo DocoHelpers::dataTabelsException($e->getMessage());
            }
        }
        return $this->renderAjax('partial/_pasien', get_defined_vars());
    }

    public function actionGetInfoPasienNew($pasien_id)
    {
        try {
            $requests = $this->_restPendaftaran->get('allow/get-info-pasien-new?pasien_id='.$pasien_id);
            $response = json_decode($requests->getBody(), true);
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
        } catch (\Exception $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
        }
    }

    /*
    * author: Rizqi Febian
    * date: 11-04-2018
    * form igd page
    */
    public function actionIgd()
    {
        $instalasi_id = 2;
        try {
            $modelIgd = new IgdForm();
            $modelAsuransi = new AsuransiForm();
            $modelPenanggungjawab = new PjpasienForm();

            $listRequestsPj = [
                'jenis_identitas',
                'nama_depan',
                'jenis_kelamin',
                'status_perkawinan',
                'warga_negara',
                'agama',
                'pengantar'
            ];
            $requestsPj = $this->_restPendaftaran->post('allow/list-lookup-types', [
                'form_params' => $listRequestsPj
            ]);
            $requestsPj = json_decode($requestsPj->getBody(), true);

            $listRequestsIgd = [
                'keadaan_masuk',
                'transportasi',
            ];
            $requestsIgd = $this->_restPendaftaran->post('allow/list-lookup-types', [
                'form_params' => $listRequestsIgd
            ]);
            $requestsIgd = json_decode($requestsIgd->getBody(), true);

            $ruangan = $this->_restPendaftaran->get('allow/get-ruangan?advanced-filter[instalasi_id]='.$instalasi_id,[]);
            $ruangan = json_decode($ruangan->getBody(), true);
            $ruangan = ArrayHelper::map($ruangan['response'], 'ruangan_id','ruangan_nama');

            $carabayar = $this->_restPendaftaran->get('allow/list-cara-bayar',[]);
            $carabayar = json_decode($carabayar->getBody(), true);
            $carabayar = $carabayar['response'];

            $packFormIgd = ['modelIgd'=>$modelIgd,'ruangan'=>$ruangan,'carabayar'=>$carabayar,'lib'=>$requestsIgd['response']];
            $packFormAsuransi = ['modelAsuransi'=>$modelAsuransi,'ruangan'=>$ruangan,'carabayar'=>$carabayar];
            $packFormPjPasien = ['modelPenanggungjawab'=>$modelPenanggungjawab,'lib'=>$requestsPj['response']];
            $packFormBpjs = [];

            if(isset($_POST['data'])){
                return DocoHelpers::response($_POST['data']);
            }

            return $this->render('form-igd',get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::response($e->getMessage());
        }
    }

    /*
    * author: Rizqi Febian
    * date: 11-04-2018
    * get data penjamin by carabayar id
    * params sent: carabayar_id
    */
    public function actionGetPenjamin($selected = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $cache = Yii::$app->cache;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = $selected;

        // if ($cache->get("get-penjamin-{$parent_label}")) {
        //     $result = $cache->get("get-penjamin-{$parent_label}");
        //     if ($selected) {
        //         $result['selected'] = $selected;
        //     }
        //     return $result;
        // }

        try {
            $response = $this->_restPendaftaran->get('allow/list-penjamin', [
                'query' => [
                    'carabayar_id' => $parent_label,
                    'withKode' => true
                ]
            ]);
            $body = json_decode($response->getBody(), True);
            $firstkey = null;
            foreach ($body['response'] as $key => $value) {
                $result['output'][] = [
                    'id' => $key,
                    'name' => $value
                ];
                if (empty($selected)) {
                    $result['selected'] = $key;
                    $selected = $key;
                }
            }
            $cache->set("get-penjamin-{$parent_label}",$result);
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return$result;
        }
    }
    /*
    * author: Rizqi Febian
    * date: 11-04-2018
    * get data jenis kasus penyakit by ruangan
    * params sent: ruangan_id
    */
    // to-do: improvement using cache for pasien rs
    public function actionGetJenisKasusPenyakit($selected = null, $asalrujukan_id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $cache = Yii::$app->cache;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = $selected;

        if($asalrujukan_id != null) {
            $url = 'allow/list-penyakit';
        } else {
            $url = 'allow/get-ruangan-penyakit';

            // if ($cache->get("get-kasus-penyakit-{$parent_label}") == true) {
            //     $return = $cache->get("get-kasus-penyakit-{$parent_label}");
            //     $return['selected'] = $selected;
            //     return $return;
            // }
        }

        try {
            $response = $this->_restPendaftaran->get($url, [
                'query' => [
                    'id' => $parent_label,
                    'withKode' => true
                ]
            ]);
            $body = json_decode($response->getBody(), True);
            $firstkey = null;
            foreach ($body['response'] as $key => $value) {
                $result['output'][] = [
                    'id' => $key,
                    'name' => $value,
                ];
                if(empty($selected)){
                    $result['selected'] = $key;
                    $selected = $key;
                    $firstkey = true;
                }
            }
            // $cache->set("get-kasus-penyakit-{$parent_label}",$result);
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return$result;
        }
    }
    /*
    * author: Rizqi Febian
    * date: 11-04-2018
    * get data kelas pelayanan by ruangan
    * params sent: ruangan_id
    */
    // to-do: improvement using cache for pasien rs
    public function actionGetKelasPelayanan($selected = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $cache = Yii::$app->cache;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = $temp_kp_2 = '';

        if($selected) {
            $url = "allow/get-mapping-list-kelas-pelayanan";
            $query = [
                'id' => $selected
            ];
        } else {
            $url = 'allow/get-config-kelas-pelayanan';
            $query = [
                'id' => $parent_label,
                'withKode' => true
            ];
        }
        try {
            $response = $this->_restPendaftaran->get($url, [
                'query' => $query
            ]);
            $body = json_decode($response->getBody(), True);
            $firstkey = null;
            foreach ($body['response'] as $key => $value) {
                $result['output'][] = [
                    'id' => $key,
                    'name' => $value
                ];
                if(is_null($firstkey)){
                    $result['selected'] =
                    $firstkey = $key;
                    $selected = $key;
                }
            }

            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return$result;
        }
    }
    /*
    * author: Rizqi Febian
    * date: 11-04-2018
    * get data dokter by ruangan
    * params sent: ruangan_id
    */
    public function actionGetDokter($asalrujukan_id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $isBpjs = $request->get('is_bpjs');
        $depdrop_parents = $request->post('depdrop_all_params');
        $parent_label = $depdrop_parents['ruangan_id'];
        $type = isset($_GET['param']) ? $_GET['param'] : 'igd';
        $with_kuota = false;
        $booked_jadwaldokter_id = $request->get('booked_jadwaldokter_id');
        $isSelected = isset($_GET['isSelected']) ? $_GET['isSelected'] : true;
        $isIntalasi = isset($_GET['isInstalasi']) ? true : false;
        $result = [];
        $result['output'] = [];
        $result['selected'] = '';
        $tgl_rujukan = $request->get('tgl_rujukan', null);
        
        try {
            $response = $this->_restPendaftaran->get('allow/get-ruangan-jam-dokter?id='.$parent_label.'&type='.$type.'&isInstalasi='.$isIntalasi.'&withCodeBpjs=true'.'&withKuota='.$with_kuota.'&is_bpjs='.$isBpjs.'&tgl_rujukan='.$tgl_rujukan);
            $body = json_decode($response->getBody(), True);
            $pegawaiKey = [];
            $data = ArrayHelper::getValue($body,'response.data',[]);
            $with_kuota = ArrayHelper::getValue($body,'response.with_kuota',[]);
            $konfig_is_unik = ArrayHelper::getValue($body,'response.konfig_is_unik',0);
            $selectedVal = '';
            foreach ($data as $key => $value) {
                $key = $value['pegawai_id'];
                if($konfig_is_unik == 0 || !in_array($key, $pegawaiKey)) {
                    $option_value = [
                        'id' => $key,
                        'name' => $value['nama_pegawai'],
                        'kode_bpjs' => $value['kode_dokter_bpjs'],
                        'kuota_tersedia' => ArrayHelper::getValue($value, 'kuota_tersedia'),
                        'jadwaldokter_id' => $type==='penunjang'?'': ArrayHelper::getValue($value, 'jadwaldokter_id')
                    ];
                    $jam_praktek = '('.ArrayHelper::getValue($value, 'jam_praktek').')';
                   
                    if ($with_kuota=='false' && $type=='rajal') {
                        $hariIni = DocoHelpers::$_hari_indo[date('D')];
                        $withJadwal = (isset($value['jadwaldokter_mulai']) && isset($value['jadwaldokter_tutup']));
                        if($withJadwal) {
                            $jamMulai = date('H:i', strtotime($value['jadwaldokter_mulai']));
                            $jamTutup = date('H:i', strtotime($value['jadwaldokter_tutup']));
                        }

                        if ($booked_jadwaldokter_id == $value['jadwaldokter_id']) {
                            $option_value['name'] = $value['nama_pegawai']. $jam_praktek . ' - ' .ArrayHelper::getValue($value, 'kuota_tersedia'). ' Kuota (BOOKED)';
                        } else if ((ArrayHelper::getValue($value, 'kuota_tersedia', 0) < 1 || ArrayHelper::getValue($value, 'kuota_tersedia', 0) == null)) {
                            if($withJadwal) {
                                $option_value['name'] = $value['nama_pegawai']. ' - 0 Kuota | '. $hariIni . ' '. $jamMulai . ' - '. $jamTutup;
                            } else {
                                $option_value['name'] = $value['nama_pegawai']. $jam_praktek . ' - 0 Kuota ';
                            }
                            $option_value['options'] = ['disabled' => true];
                        } else {
                            if($withJadwal) {
                                $option_value['name'] = $value['nama_pegawai']. ' - ' .ArrayHelper::getValue($value, 'kuota_tersedia'). ' Kuota | Jam Pelayanan '. $jamMulai . ' - '. $jamTutup;
                            } else {
                                $option_value['name'] = $value['nama_pegawai']. $jam_praktek . ' - ' .ArrayHelper::getValue($value, 'kuota_tersedia'). ' Kuota';
                            }
                            $selectedVal = $value['pegawai_id'];
                        }
                        $option_value['options']['data-jadwaldokter_id'] = ArrayHelper::getValue($value, 'jadwaldokter_id');
                    }


                    $result['output'][] = $option_value;
                    if(is_null($asalrujukan_id)) {
                        if ($isSelected == true) {
                            $result['selected'] = $result['selected'] ? : $selectedVal;
                        } else {
                            $result['selected'] = "";
                        }
                    } else {
                        // $result['selected'] = $result['selected'] ? : $key;
                        $result['selected'] = "";
                        // $result['selected'] =  $key;
                    }

                    $pegawaiKey[] = $key;
                }
            }
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return$result;
        }
    }
    /*
    * author: Rizqi Febian
    * date: 12-04-2018
    * get data kunjungan rajal by pasien id
    * params needed: pasien_id
    */
    public function actionGetKunjunganRajal($id)
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
            $response = $this->_restPendaftaran->get('pendaftaran/get-kunjungan-rajal', ['query' => ['id'=>$id]]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
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

    public function actionRanap()
    {
        $session = Yii::$app->session;
        $active_workspace = $session->get('active_workspace');
        $model = new PendaftaranForm();
        $modelPasien = new PasienForm();
        $modelPenanggungjawab = new PenanggungjawabForm();
        $modelRujukan = new RujukanForm();
        $modelRanap = new PasienAdmisiForm();

        // get data bundule api from backend
        $data = $this->getDataApi();
        // list karcis
        $listKarcis = $data['karcis'];
        $title = Yii::t('fe',$this->_title)." ".Yii::t('fe', 'Rawat Inap');
        $request = Yii::$app->request;
        $formName = substr(strrchr(get_class($modelPenanggungjawab), "\\"), 1);

        if($request->post()) {

            try {
                $response = $this->_restPendaftaran->post('pendaftaran/create-ranap', [
                    'form_params' => Yii::$app->request->post()
                ]);
                // return DocoHelpers::response(json_decode($response->getBody(), TRUE));
                return DocoHelpers::responseJsonString($response->getBody(), $formName);
            } catch (RequestException $e) {
                return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
            } catch (\Exception $e) {
                return DocoHelpers::responseTemplate(500, $e->getMessage());
            }
        }

        return $this->render('ranap', get_defined_vars());
    }

    public function actionGetListRuangan() {
        $request = Yii::$app->request;
        $post = $request->post();
        $jeniskasuspenyakit_id = $post['depdrop_parents'][0];
        $instalasi_id = isset($_GET['instalasi_id']) ? $_GET['instalasi_id'] : null;
        $ruanganRequest = $this->_restPendaftaran->get(
            'allow/get-list-ruangan',
            [
                'query'=>[
                    'jeniskasuspenyakit_id'=>$jeniskasuspenyakit_id,
                    'instalasi_id'=>$instalasi_id,
                ]
            ]
        );

        $body = json_decode($ruanganRequest->getBody(),TRUE);
        $response = $body['response'];
        $out = [];
        foreach($response as $kamar => $value) {
            $out[] = [
                'id' => $kamar,
                'name' => $value
            ];
        }

        echo json_encode(['output'=>$out, 'selected'=>'']);
        return;
    }

    public function actionGetListRuanganKelas() {
        $request = Yii::$app->request;
        $post = $request->post();
        $jeniskasuspenyakit_id = $post['jeniskasuspenyakit_id'];
        $kelaspelayanan_id = ((int) $post['kelaspelayanan_id']) ? (int) $post['kelaspelayanan_id'] : null ;

        $instalasi_id = isset($_GET['instalasi_id']) ? $_GET['instalasi_id'] : null;
        $ruanganRequest = $this->_restPendaftaran->get(
            'allow/get-list-ruangan-kelas',
            [
                'query'=>[
                    'jeniskasuspenyakit_id'=>$jeniskasuspenyakit_id,
                    'instalasi_id'=>$instalasi_id,
                    'kelaspelayanan_id'=>$kelaspelayanan_id,
                ]
            ]
        );

        $body = json_decode($ruanganRequest->getBody(),TRUE);
        $response = $body['response'];
        $out = [];
        foreach($response as $kamar => $value) {
            $out[] = [
                'id' => $kamar,
                'name' => $value
            ];
        }

        echo json_encode(['output'=>$out, 'selected'=>'']);
        return;
    }

    public function actionPilihTempatTidur()
    {
        $request = Yii::$app->request;
        $title = Yii::t('fe', 'Pilih Tempat Tidur');
        $ruangan_id = DocoHelpers::decrypt($request->get('id'));
        return $this->renderAjax('_pemilihan_tempat_tidur', get_defined_vars());
    }

    public function actionGetDataTempatTidur()
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
            $ruangan_id = $request->get('ruangan_id');
            $response = $this->_restPendaftaran->request('POST', 'allow/get-kamar-by-ruangan', [
                        'form_params'=>['ruangan_id' => $ruangan_id],
                 ]);

            $body = json_decode($response->getBody(),TRUE);
            $response = $body['response'];
            $no = $request->get('start',1);

            foreach ($response as $key => $value) {
                $no++;
                $primaryKey = $value['kamarruangan_id'];
                $value['rowNum'] = $no;
                $value['nama_kamar'] = $value['kamarruangan_nokamar'];
                $value['no_tempattidur'] = $this->getTempatTidur($primaryKey);
                $value['primary'] = $primaryKey;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = count($response);
            $result['recordsFiltered'] = count($response);
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    /*
    * author: Rizqi Febian
    * date: 13-04-2018
    * get data list tarif karcis by ruangan id and kelaspelayanan id
    * params needed: ruangan_id, kelaspelayanan_id
    */
    public function actionGetKarcis($ruangan_id,$kp_id,$status,$penjamin_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $ruanganId = Yii::$app->docoVars->workspace('ruangan_id');
        // $param = DocoConstants::PARAM_DFTR[$ruanganId];
        // $tarifgroup =($param == 'penunjang') ? DocoConstants::VAR_KEL_KRCS_PNJ : DocoConstants::VAR_KEL_KRCS;
        $tarifgroup = DocoConstants::VAR_KEL_KRCS;
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;

        try {
            if(empty($kp_id)){
                return $result;
            }
            $query = ['ruangan_id'=>$ruangan_id,'kp_id'=>$kp_id,'penjamin_id'=>$penjamin_id, 'tarifgroup'=>$tarifgroup];
            $response = $this->_restPendaftaran->get('pendaftaran/get-tarif-total', ['query' => $query]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            $checked = true;
            $i = 0;
            $y = 0;
            $komponenTarif = [];
            foreach ($body['response']['data'] as $key => $value) {
                $tmp_total = 0;
                if($status == 0){
                    if($value['daftartindakan_id'] != 1){
                        $checked = false;
                    }
                }
                if($checked || $value['is_default']==true){
                    $tmp_total = $value['harga_tariftindakan'];
                }

                if($value['komponentarif_id'] == '6'){
                    $value['checked'] = $checked;
                    $value['tmp_view'] = 'Rp. ' . number_format($value['harga_tariftindakan'], 0, ',', '.');
                    $value['tmp_total'] = $tmp_total;
                    $value['number'] = $i + 1;
                    $value['aksi'] = Html::checkbox('status[]', ($value['is_default']) ? true : $checked, [
                        'label' => '',
                        'data-key' => $i,
                        'disabled' => ($value['is_default']) ? true : false,
                        'class' => 'check-aksi',
                        'value' => $value['daftartindakan_id'],
                    ]);
                    $value['cek-default'] = ($value['is_default']) ? 1 : 0;
                    $data[$i] = $value;
                    $i++;
                }else{
                    $komponenTarif[$value['daftartindakan_id']][] = $value;
                    $y++;
                }
            }

            foreach ($data as $key => $value) {
                if(isset($komponenTarif[$value['daftartindakan_id']])){
                    $data[$key]['komponen'] = json_encode($komponenTarif[$value['daftartindakan_id']]);
                }
            }

            $result['data'] = $data;
            $result['recordsTotal'] = count($data);
            $result['recordsFiltered'] = count($data);
            return $result;
        } catch (RequestException $e) {
            $result['errorMessage'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['errorMessage'] = $e->getMessage();
            return $result;
        }
    }

    private function getTempatTidur($kamarruangan_id)
    {
        $tempat_tidur = $this->_restPendaftaran->request('POST', 'allow/get-tempat-tidur', [
                        'form_params'=>['kamarruangan_id' => $kamarruangan_id],
                ]);

        $body = json_decode($tempat_tidur->getBody(),TRUE);

        $response = $body['response'];
        return $response;
    }



    public function actionGetAntrian($antrian_id)
    {
        if (!$antrian_id) return DocoHelpers::response([], 200);
        try {
            $request = $this->_restPendaftaran->get('allow-antrian/get-antrian?antrian_id=' . $antrian_id);
            $body = json_decode($request->getBody(),TRUE);
            $response = $body['response'];
            return DocoHelpers::response($response, 200);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    /*
    * author: Rizqi Febian
    * date: 19-04-2018
    * desc: submit form rujukan
    * params needed: rujukanForm
    */
    public function actionSaveRujukan($params){
        $request = Yii::$app->request;
        if($request->post()){
            $model = new RujukanForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            try {
                $post = $request->post();
                $model->load($post);
                if($model->validate()){
                    $response = $this->_restPendaftaran->post('pendaftaran/save-rujukan', [
                        'query'=>[
                            'params'=>$params
                        ],
                        'form_params'=>$post
                    ]);
                    $body = json_decode($response->getBody(), true);
                    if(isset($body['response']['errors'])){
                        return DocoHelpers::response($body['response']['errors'], 422, $formName);
                    }
                    return DocoHelpers::response($body['response']);
                }else{
                    $response = $model->errors;
                    return DocoHelpers::response($response, 422, $formName);
                }
            } catch (Exception $e) {
                $response = json_decode($e->getResponse()->getBody(), true);
                $response['response']['title'] = 'Terjadi Kesalahan';
                $response['response']['text'] = isset($response['response']['message']) ? $response['response']['message'] : 'Terjadi Kesalahan';
                return DocoHelpers::response($response, 422);
            } catch(\RequestException $e){
                $response = json_decode($e->getResponse()->getBody(), true);
                $response['response']['title'] = 'Terjadi Kesalahan';
                $response['response']['text'] = isset($response['response']['message']) ? $response['response']['message'] : 'Terjadi Kesalahan';
                return DocoHelpers::response($response, 422);
            }
        }
    }
    /*
    * author: Rizqi Febian
    * date: 23-04-2018
    * desc: submit form asuransi
    * params needed: asuransiForm
    */
    public function actionSaveAsuransi(){
        $request = Yii::$app->request;
        if($request->post()){
            $model = new AsuransiForm();
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            try {
                $post = $request->post();
                $model->load($post);
                if($model->validate()){
                    $response = $this->_restPendaftaran->post('pendaftaran/save-asuransi', [
                        'form_params'=>$post
                    ]);
                    $body = json_decode($response->getBody(), true);
                    if(isset($body['response']['errors'])){
                        return DocoHelpers::response($body['response']['errors'], 422, $formName);
                    }
                    return DocoHelpers::response($body['response']);
                }else{
                    $response = $model->errors;
                    return DocoHelpers::response($response, 422, $formName);
                }
            } catch (Exception $e) {
                $result['error'] = $e->getMessage();
                return DocoHelpers::response($result);
            } catch(\RequestException $e){
                $result['error'] = $e->getMessage();
                return DocoHelpers::response($result);
            }
        }
    }
    /*
    * author: Rizqi Febian
    * date: 25-04-2018
    * desc: get rujukan dari by asalrujukan_id
    * params needed: asalrujukan_idm
    */
    public function actionGetRujukanDari(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restPendaftaran->get('allow/get-rujukan-dari?id='.$parent_label);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $key => $value)
                $result['output'][] = [
                    'id' => $key,
                    'name' => $value
                ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return$result;
        }
    }
    /*
    * author: Rizqi Febian
    * date: 25-04-2018
    * desc: get data diagnosa
    * params needed: diagnosa_name
    */
    public function actionGetDiagnosa(){
        if(isset($_GET['search']) && !empty($_GET['search'])){
            $type = isset($_GET['type']) ? $_GET['type'] : null;
            $response = $this->_restPendaftaran->request('POST', 'allow/get-diagnosa',[
                            'form_params'=>['term'=>$_GET['search'], 'type'=>$type],
                        ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $text = $value['diagnosa_kode'] . ' - ' . $value['diagnosa_namalainnya'];
                $data[] = ['id'=>$value['diagnosa_id'],'text'=>$text];
            }
            $total = count($body['response']);
            $return = ['results'=>$data,'total_count'=>$total,'incomplete_results'=>false];
            return DocoHelpers::response($return);
        }
    }

    /**
     * @todo get all data api from backend
     * @author Randy Vianda Putra <randy@docotel.com>
     * @param instalasi_id
     * @param default for cara bayar
     */
    private function getDataApi($instalasi_id = null, $default = null, $pendaftaranol_id=null, $janji_id = null)
    {
        try {
            $response = $this->_restPendaftaran->get('allow/get-api?instalasi_id=' . $instalasi_id . '&default=' . $default . '&pendaftaranol_id=' . $pendaftaranol_id . '&janji_id=' . $janji_id);
            $body = json_decode($response->getBody(), true);
            $result = [
                'response' => $body['response']['lookup'],
                'lookup' => $body['response']['lookup'],
                'master' => $body['response']['master'],
                'ruangan' => $body['response']['ruangan'],
                'cara_bayar' => $body['response']['cara_bayar'],
                'asal_rujukan' => $body['response']['asal_rujukan'],
                'kelas_pelayanan' => $body['response']['kelas_pelayanan'],
                'karcis' => $body['response']['karcis'],
                'jeniskasus' => $body['response']['jeniskasus'],
                'dokter' => $body['response']['dokter'],
                'penjamin' => $body['response']['penjamin'],
                'instalasi' => isset($body['response']['instalasi']['data']) ? $body['response']['instalasi']['data'] : [],
                // 'statusSync' => json_decode($this->actionGetTotalDataUnsyc()),
                'pendaftaranol' => $body['response']['pendaftaranol'],
                'janjiPoli' => !empty($body['response']['janjiPoli']) ? $body['response']['janjiPoli'] : []
            ];

            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return$result;
        }
    }

    /**
    * @author Rizal
    * @since 2018-05-04 10:26:29
    * @param no_antrian
    * @return
    * @desc pilih antrian manual
    */
    public function actionPilihAntrianManual($no_antrian)
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $loginpemakai_id = Yii::$app->docoVars->user("id");
            $loket_id = Yii::$app->docoVars->workspace("loket")[$loginpemakai_id]['loket_id'];
            $url = 'allow-antrian/pilih-manual?no_antrian=' . $no_antrian . '&loket_id=' . $loket_id;
            $response = $this->_restPendaftaran->get($url);
            $body = json_decode($response->getBody(), true);
            return $body;
        } catch (RequestException $e) {
            $result = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDataSepuluhTerakhir()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $draw = $request->get('draw', 1);
        $param = $request->get('param');
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $loginpemakai_id = Yii::$app->docoVars->user("id");
            $response = $this->_restPendaftaran->request('GET', 'inf-daftar-sepuluh-terakhir/index', [
                'form_params'=>[],
                'query'=>['user_id'=>$loginpemakai_id, 'jenis'=>$param]
            ]);

            $body = json_decode($response->getBody(),TRUE);
            $response = $body['response']['data'];
            $no = $request->get('start',1);

            foreach ($response as $key => $value) {
                $no++;
                $primaryPendaftaran = DocoHelpers::encrypt($value['pendaftaran_id']);
                $primaryPasien = DocoHelpers::encrypt($value['pasien_id']);
                $primarySep = empty($value['nosep']) ? null : DocoHelpers::encrypt($value['nosep']);
                $value['primary'] = $primaryPendaftaran.'-'.$value['no_rekam_medik'];
                $status_kamar = '-';
                if($param == 'ranap') {
                    if($value['carabayar_id'] == 6) {
                        $status_kamar = 'Sesuai Kelas';
                        if($value['klsrawat'] != null) {
                            if($value['is_aps'] == true) {
                                if($value['bpjs_kelas'] != null) {
                                    if($value['kelaspelayanan_id'] < $value['klsrawat']) {
                                        $status_kamar = 'APS / Naik kelas';
                                    }
                                    elseif($value['kelaspelayanan_id'] > $value['klsrawat']) {
                                        $status_kamar = 'APS / Turun kelas';
                                    }
                                }
                                else {
                                    $status_kamar = 'APS / Naik kelas';
                                }
                            }
                            elseif($value['is_pasientitipan'] == true) {
                                if($value['bpjs_kelas'] != null) {
                                    if($value['kelaspelayanan_id'] < $value['klsrawat']) {
                                        $status_kamar = 'Titipan / Naik kelas';
                                    }
                                    elseif($value['kelaspelayanan_id'] > $value['klsrawat']) {
                                        $status_kamar = 'Titipan / Turun kelas';
                                    }
                                }
                                else {
                                    $status_kamar = 'Titipan / Naik kelas';
                                }
                            }
                        }
                    }
                }
                $value['ruangan_nama'] = $param == 'ranap'
                    ? $value['ruangan_nama'] . ' - ' . $value['kamarruangan_nokamar'] . ' - ' . $value['no_tempattidur']
                    : $value['ruangan_nama'];
                $value['rowNum'] = $no;
                $value['primaryPendaftaran'] = $primaryPendaftaran;
                $value['primaryPasien'] = $primaryPasien;
                $value['pasien_id'] = $primaryPasien;
                $value['pendaftaran_id'] = $primaryPendaftaran;
                $value['primarySep'] = $primarySep;

                if ($param == 'ranap') {
                    $value['tgl_pendaftaran'] = date('d-m-Y H:i:s', strtotime($value['tgl_admisi']));
                } else if($param == 'penunjang') {
                    $value['tgl_pendaftaran'] = date('d-m-Y H:i:s', strtotime($value['tglmasukpenunjang']));
                    $value['nosep'] = array_key_exists('nosep', $value) ? $value['nosep'] : '';
                } else {
                    $value['tgl_pendaftaran'] = date('d-m-Y H:i:s', strtotime($value['tgl_pendaftaran']));
                }

                $value['status_kamar'] = $status_kamar;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = count($response);
            $result['recordsFiltered'] = count($response);
            return $result;
        } catch (RequestException $e) {
            $result['errorMessage'] = $e->getMessage();
            return $result;
        } catch (ErrorException $e) {
            $result['errorMessage'] = $e->getMessage();
            return $result;
        }
    }

    public function actionPrintKarcis()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-karcis.pdf";
        try {
            $pasien_id = $request->get('pasien_id',null);
            $decryptPasienId = DocoHelpers::decrypt($pasien_id);
            $pendaftaran_id = $request->get('pendaftaran_id',null);
            $decryptPendaftaranId = DocoHelpers::decrypt($pendaftaran_id);
            $post = ['pasien_id'=>$decryptPasienId,'pendaftaran_id'=>$decryptPendaftaranId];
            $response = $this->_restPendaftaran->post(
                'inf-daftar-sepuluh-terakhir/print-karcis',
                [
                    'form_params' => $post,
                    'save_to' => $path
                ]
            );
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionPrintStatusPasien()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/cetak-label.pdf";
        try {
            $pasien_id = $request->get('pasien_id',null);
            $decryptPasienId = DocoHelpers::decrypt($pasien_id);
            $pendaftaran_id = $request->get('pendaftaran_id',null);
            $decryptPendaftaranId = DocoHelpers::decrypt($pendaftaran_id);
            $post = ['pasien_id'=>$decryptPasienId,'pendaftaran_id'=>$decryptPendaftaranId];
            $response = $this->_restPendaftaran->post('inf-daftar-sepuluh-terakhir/print-status-pasien',
                [
                    'form_params' => $post,
                    'save_to' => $path
                ]
            );
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionPrintKartuPasien()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-kartu-pasien.pdf";
        try {
            $pasien_id = $request->get('pasien_id',null);
            $decryptPasienId = DocoHelpers::decrypt($pasien_id);
            $pendaftaran_id = $request->get('pendaftaran_id',null);
            $decryptPendaftaranId = DocoHelpers::decrypt($pendaftaran_id);
            $post = ['pasien_id'=>$decryptPasienId,'pendaftaran_id'=>$decryptPendaftaranId];
            $response = $this->_restPendaftaran->get('inf-daftar-sepuluh-terakhir/print-kartu-pasien',
                [
                    'query' => $post,
                    'save_to' => $path
                ]
            );
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (Exception $e) {
            // var_dump($e);exit;
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionPrintGelangPasien()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-gelang-pasien.pdf";
        try {
            $pasien_id = $request->get('pasien_id',null);
            $decryptPasienId = DocoHelpers::decrypt($pasien_id);
            $pendaftaran_id = $request->get('pendaftaran_id',null);
            $decryptPendaftaranId = DocoHelpers::decrypt($pendaftaran_id);
            $post = ['pasien_id'=>$decryptPasienId,'pendaftaran_id'=>$decryptPendaftaranId];
            $response = $this->_restPendaftaran->post('inf-daftar-sepuluh-terakhir/print-gelang-pasien',
                [
                    'form_params' => $post,
                    'save_to' => $path
                ]
            );
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionPrintLabelPasien()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-label-pasien.pdf";
        try {
            $pasien_id = $request->get('pasien_id',null);
            $decryptPasienId = DocoHelpers::decrypt($pasien_id);
            $pendaftaran_id = $request->get('pendaftaran_id',null);
            $decryptPendaftaranId = DocoHelpers::decrypt($pendaftaran_id);
            $post = ['pasien_id'=>$decryptPasienId,'pendaftaran_id'=>$decryptPendaftaranId];
            $response = $this->_restPendaftaran->post('inf-daftar-sepuluh-terakhir/print-label-pasien',
                [
                    'form_params' => $post,
                    'save_to' => $path
                ]
            );
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionPrintSep()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-sep.pdf";
        try {
            $pasien_id = $request->get('pasien_id',null);
            $decryptPasienId = DocoHelpers::decrypt($pasien_id);
            $pendaftaran_id = $request->get('pendaftaran_id',null);
            $decryptPendaftaranId = DocoHelpers::decrypt($pendaftaran_id);
            $jenis_pendaftaran = $request->get('jenis', null);
            $params = ['pasien_id'=>$decryptPasienId,'pendaftaran_id'=>$decryptPendaftaranId];

            if ($jenis_pendaftaran != null) {
                $params['jenis_pendaftaran'] = $jenis_pendaftaran;
            }

            $response = $this->_restPendaftaran
                ->get('allow-bpjs/print-sep',
                    [
                        'query' => $params,
                        'save_to' => $path
                    ]);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }


    /**
    * @author Rizal F. <rizal@docotel.com>
    * @since 2018-05-22
    * @param
    * @return
    */
    public function actionModalBookingkamar()
    {
        try {
            // $modelAntrian = new Antrian;
            $title = Yii::t('fe', 'Pemesanan kamar');


            return $this->renderAjax('_modal_bookingkamar', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionGetDataBookingkamar()
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
            $response = $this->_restPendaftaran->get('inf-pemesanan-kamar/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['bookingkamar_id']);
                unset($value['bookingkamar_id']);

                $value['kamarruangan_nokamar'] = $value['kamarruangan_nokamar'] . ' - ' . $value['no_tempattidur'];

                $value['aksi']  = '';
                if (in_array($value['statusbooking'], [DocoConstants::BOOK_BLM_KNFRM, DocoConstants::BOOK_SETUJU])) {
                    $value['aksi'] .= Html::button(
                        Yii::t('fe', 'Pilih'),
                        [
                            'class' => 'btn btn-success btn-xs pilih-bookingkamar',
                            'data-placement' => 'bottom',
                            'data-bookingkamar_no' => $value['no_pemesanan'],
                            'data-bookingkamar_id' => DocoHelpers::decrypt($primaryKey),
                            'data-jeniskasuspenyakit_id' => $value['jeniskasuspenyakit_id'],
                            'data-kelaspelayanan_id' => $value['kelaspelayanan_id'],
                            'data-ruangan_id' => $value['ruangan_id'],
                            'data-kamarruangan_id' => $value['kamarruangan_id'],
                            'data-kamartempattidur_id' => $value['kamartempattidur_id'],
                            'data-kamarruangan_nokamar' => $value['kamarruangan_nokamar'], // . ' - ' . $value['no_tempattidur'],
                            // 'action' => Url::home().$this->_module.'setujui?id='.$primaryKey,
                            'data-popup' => "tooltip",
                            'title' => \Yii::t('fe', 'Pilih'),
                        ]
                    );
                }
                $value['rowNum'] = $no;
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
    /*
    author: Rizqi Febian
    usage: modal tambah pemeriksaan laboratorium pendaftaran penunjang
    date: 11-07-2018
    */
    public function actionModalPemeriksaanLab()
    {
        try {
            $visible = true;
            $request = Yii::$app->request;
            $instalasi_id = $request->get('instalasi_id', null);
            $title = Yii::t('fe', 'Tambah pemeriksaan');
            if($instalasi_id) {
                $title = Yii::t('fe', 'Tambah Paket');
                $visible = false;
            }

            return $this->renderAjax('partial/_modal_viewplab', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }
    public function actionGetTarifPaket()
    {
        $result = [];
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            $params = [
                'ruangan_id' => isset($get['ruangan_id']) ? $get['ruangan_id'] : '',
                'penjamin_id' => isset($get['penjamin_id']) ? $get['penjamin_id'] : '',
                'kelaspelayanan_id' => isset($get['kelaspelayanan_id']) ? $get['kelaspelayanan_id'] : '',
                'instalasi_id' => isset($get['instalasi_id']) ? $get['instalasi_id'] : '',
                'list_paket' => isset($get['listPaket']) ? $get['listPaket'] : '',
            ];
            $data = [];
            $draw = $request->get('draw', 1);
            if (empty($params['ruangan_id']) || empty($params['penjamin_id'])
                    || empty($params['kelaspelayanan_id']) || empty($params['instalasi_id'])) {
                return DocoHelpers::response([
                    'data' => [],
                    'draw' => $draw,
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                ]);
            }
            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;
            $result['recordsTotal'] = 0;

            if(!isset($get['listPaket'])){
                $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($get);
                $params['page'] = $yiiRestfulParams['page'];
                $params['per-page'] = $yiiRestfulParams['per-page'];
            }

            if(isset($yiiRestfulParams['advanced-filter']['tipepaket_nama'])){
                $params['tipepaket_nama'] = strtolower($yiiRestfulParams['advanced-filter']['tipepaket_nama']);
            }

            if(isset($yiiRestfulParams['advanced-filter']['pemeriksaanlab_nama'])){
                $params['jenispemeriksaanlab_nama'] = strtolower($yiiRestfulParams['advanced-filter']['pemeriksaanlab_nama']);
            }

            if (isset($yiiRestfulParams['advanced-filter']['kode'])){
                $params['kode'] = $yiiRestfulParams['advanced-filter']['kode'];
            }

            $params['limit'] = !empty($get['length']) ? $get['length'] : 50;

            $url = 'allow/get-tarif-paket';
            $response = $this->_restPendaftaran->get($url, ['query' => $params]);
            $body = json_decode($response->getBody(), true);

            $body = isset($body['response']) ? $body['response'] : [];
            $no = !empty($get['start']) ? $get['start'] : 0;
            foreach ($body['data'] as $key => $value) :
                $no++;
                $value['rowNum'] = $no;
                $value['detail'] = '';
                if($params['instalasi_id'] == DocoConstants::INSTALASI_MCU) {
                    $value['nama_tindakan_paket'] = '';
                    $value['detail'] = Html::button("<i class='fa fa-plus-square-o'></i>", [
                    'class' => 'btn btn-sm btn-success', 'data-source'=> "/pendaftaran/daftar/detail-paket?id=".$value['tipepaket_id'].'&instalasi_id='.$params['instalasi_id'],'onclick'=> 'docoHelper.detail(this)']);
                }

                if(isset($value['paketDetailView'])){
                    if(count($value['paketDetailView']) > 0){
                        foreach ($value['paketDetailView'] as $k => $v) :
                            $value['nama_tindakan_paket'][] =$v['daftartindakan_nama'];
                        endforeach;
                        $value['nama_tindakan_paket'] = implode(',', $value['nama_tindakan_paket']);
                    }
                }

                $data[$key] = $value;
            endforeach;
            $result['data'] = $data;
            $result['recordsTotal'] = $body['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['_meta']['totalCount'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }
    public function actionGetTarifTindakan()
    {
        $result = [];
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            $params = [
                'ruangan_id' => isset($get['ruangan_id']) ? $get['ruangan_id'] : '',
                'penjamin_id' => isset($get['penjamin_id']) ? $get['penjamin_id'] : '',
                'kelaspelayanan_id' => isset($get['kelaspelayanan_id']) ? $get['kelaspelayanan_id'] : '',
                'instalasi_id' => isset($get['instalasi_id']) ? $get['instalasi_id'] : '',
                'list_tindakan' => isset($get['listTindakan']) ? $get['listTindakan'] : '',
            ];

            $draw = $request->get('draw', 1);
            if (empty($params['ruangan_id']) || empty($params['penjamin_id'])
                    || empty($params['kelaspelayanan_id']) || empty($params['instalasi_id'])) {
                return DocoHelpers::response([
                    'data' => [],
                    'draw' => $draw,
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                ]);
            }
            $data = [];
            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;
            $result['recordsTotal'] = 0;

            if(!isset($get['listTindakan'])){
                $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($get);
                $params['page'] = $yiiRestfulParams['page'];
                $params['per-page'] = $yiiRestfulParams['per-page'];
            }

            if(isset($yiiRestfulParams['advanced-filter']['jenispemeriksaanlab_nama'])){
                $params['jenispemeriksaanlab_nama'] = strtolower($yiiRestfulParams['advanced-filter']['jenispemeriksaanlab_nama']);
            }

            if (isset($yiiRestfulParams['advanced-filter']['daftartindakan_nama'])){
                $params['daftartindakan_nama'] = strtolower($yiiRestfulParams['advanced-filter']['daftartindakan_nama']);
            }

            if (isset($yiiRestfulParams['advanced-filter']['kode'])){
                $params['kode'] = $yiiRestfulParams['advanced-filter']['kode'];
            }

            $params['limit'] = !empty($get['length']) ? $get['length'] : 50;

            $url = 'allow/get-tarif-tindakan';
            $response = $this->_restPendaftaran->get($url, ['query' => $params]);
            $body = json_decode($response->getBody(), true);
            $body = isset($body['response']) ? $body['response'] : [];
            $no = !empty($get['start']) ? $get['start'] : 0;
            foreach ($body['data'] as $key => $value) :
                $no++;
                $value['rowNum'] = $no;
                // if(isset($value['jenispemeriksaanlab_nama'])){
                //     $value['jenispemeriksaanlab_nama'] = $value['jenispemeriksaanlab_nama'];
                // }
                $data[$key] = $value;
            endforeach;
            $result['data'] = $data;
            $result['recordsTotal'] = $body['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['_meta']['totalCount'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }
    public function actionGetRuangan()
    {
        $cache = Yii::$app->cache;
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        if ($cache->get("get-ruangan-{$parent_label}")) {
            return $cache->get("get-ruangan-{$parent_label}");
        }

        try {
            $response = $this->_restPendaftaran->get('allow/get-ruangan?advanced-filter[instalasi_id]='.$parent_label);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value) {
                $result['output'][] = [
                    'id' => $value['ruangan_id'],
                    'name' => $value['ruangan_nama']
                ];
                if (empty($result['selected'])) {
                    $result['selected'] = $value['ruangan_id'];
                }
            }
            $cache->set("get-ruangan-{$parent_label}",$result);
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    // DUPLICATE FROM ANTRIAN FEATURE
    public function actionUbahJenisAntrian($loket_id, $jenisantrian_id)
    {
        $model = new loketForm;
        $request = Yii::$app->request;
        if ($request->post()) {
            $model->load($request->post());
            if (!empty($model->konfigantrian_id)) {
                try {
                    $response = $this->_restPendaftaran->put('pendaftaran/ubah-pengambilan-antrian?id='.$loket_id, [
                        'form_params' => $model->attributes
                    ]);
                $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response,false,'LoketForm');
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), 'LoketForm');
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                return DocoHelpers::response($model->errors,422,'LoketForm');
            }
        } else {
            $response = $this->_restPendaftaran->get('pendaftaran/view-loket?id='.$loket_id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'];
            $model->attributes = $attributes['loket'];
            $val_konfig = DocoHelpers::encrypt(json_encode($attributes['val_konfig']));
        }

        return $this->renderAjax('ubah-jenis-antrian', get_defined_vars());
    }

    // DUPLICATE FROM ANTRIAN FEATURE
    public function actionAddItemKonfig($id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $compare = [];
        if ($id) {
            $id = DocoHelpers::decrypt($id);
            $compare = json_decode($id);
        }

        $post = $request->post();
        $dataKonfig = [];
        if ($post['jenisantrian_id']) {
            $KonfigantrianRequest = $this->_restPendaftaran->get('allow/get-group-konfig',[
                'query' => [
                    'jenisantrian_id' => $post['jenisantrian_id']
                ]
            ]);
            $body = json_decode($KonfigantrianRequest->getBody(),TRUE);
            $dataKonfig = $body['response'];
        }

        return DocoHelpers::response(['output'=>$dataKonfig]);
    }

    /**
     * @todo Fungsi untuk mendapatkan data pasien menggunakan pencarian lanjutan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionPencarianLanjutan($is_bpjs = '')
    {
        return Yii::$app->docoPlugin->execute($this,'modal_pencarian_lanjutan');
    }

    /**
     * @todo Fungsi untuk mendapatkan data pasien menggunakan pencarian lanjutan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDataPasien()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            // $yiiRestfulParams['is_aps'] = $request->get('is_aps');
            $is_bpjs = $request->get('is_bpjs', false);
            $draw = $request->get('draw', 1);
            $no = $request->get('start', 1);
            $data = [];

            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;
            $result['recordsFiltered'] = 0;

            if (isset($yiiRestfulParams['order']) && $yiiRestfulParams['order'] == ' ') {
                unset($yiiRestfulParams['order']);
            }

            if (isset($yiiRestfulParams['advanced-filter']['tanggal_lahir']) && $yiiRestfulParams['advanced-filter']['tanggal_lahir'] != '') {
                if(preg_match("/^[0-9]{1,2}\-[0-9]{1,2}\-[0-9]{4}$/", $yiiRestfulParams['advanced-filter']['tanggal_lahir']) === 0) {
                    unset($yiiRestfulParams['advanced-filter']['tanggal_lahir']);
                } else {
                    $yiiRestfulParams['advanced-filter']['tanggal_lahir'] = date('Y-m-d', strtotime($yiiRestfulParams['advanced-filter']['tanggal_lahir']));
                }
            }

            if (isset($yiiRestfulParams['advanced-filter']) && !empty($yiiRestfulParams['advanced-filter'])) {
                $restPendaftaran = $this->_restPendaftaran->get('pendaftaran/get-data-pasien?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
                $body = json_decode($restPendaftaran->getBody(), true);
                $data = $body['response']['data'];

                if (!empty($data)) {
                    foreach ($data as $key => $value) {
                        $no++;
                        $primary = DocoHelpers::encrypt($value['pasien_id']);
                        $value['no'] = $no;
                        $value['info_pasien'] = $value['no_rekam_medik'].'<br>'.$value['nama_pasien'].'<br>'.DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($value['tanggal_lahir'])), false, false);
                        $value['tgl_pendaftaran'] = $value['tgl_pendaftaran'] != '' ? DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($value['tgl_pendaftaran'])), false, true) : '';
                        $value['aksi'] = Html::button(Yii::t('fe', 'Pilih'), [
                            'class' => 'btn btn-info btn-sm',
                            'data-id' => $value['pasien_id'],
                            'data-norm' => $value['no_rekam_medik'],
                            'data-nama' => $value['nama_pasien'],
                            'data-bpjs' => $value['nopeserta_bpjs'],
                            'onClick' => $is_bpjs ? 'onClickBpjsPasien(this)' : 'onClickPilihPasien(this)',
                        ]);
                        unset($value['pasien_id']);

                        $data[$key] = $value;
                        $data[$key]['primary'] = $primary;
                    }

                    $result['data'] = $data;
                    $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                    $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
                }
                else {
                    $result['data'] = $data;
                    $result['recordsTotal'] = 0;
                    $result['recordsFiltered'] = 0;
                }
            }

            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionGetPasienAutofill($nopesertabpjs)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response_field = [
            'nik' => '',
            'namapasien' => '',
            'tanggallahir' => '',
            'jeniskelamin' => '',
            'alamatpasien' => '',
            'provinsi' => '',
            'kabupaten' => '',
            'kecamatan' => '',
            'kelurahan' => '',
            'norekammedik' => ''
        ];
        try{
            $getDataRequest = $this->_restPendaftaran->get('allow/get-pasien-autofill',[
                'query' => [
                            'nopesertabpjs' => $nopesertabpjs
                        ]
            ]);
            $parsingRequest = json_decode($getDataRequest->getBody(),TRUE);
            $response = $parsingRequest['response'];
            return $response;
        }catch (RequestException $e) {
            return $response_field;
        } catch (\Exception $e) {
            return $response_field;
        }
    }

    /**
     * @todo Fungsi untuk mendapatkan data kunjungan pasien
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDataKunjunganPasien()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $pasien_id = $request->get('pasien_id', null);
            $draw = $request->get('draw', 1);
            $no = $request->get('start', 1);
            $data = [];

            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;
            $result['recordsFiltered'] = 0;

            if ($pasien_id) {
                $restPendaftaran = $this->_restPendaftaran->get('pendaftaran/get-data-kunjungan-pasien?pasien_id='.$pasien_id.'&'.http_build_query($yiiRestfulParams), ['form_params' => []]);
                $body = json_decode($restPendaftaran->getBody(), true);
                $data = $body['response']['data'];
                if (!empty($data)) {
                    foreach ($data as $key => $value) {
                        $no++;
                        $value['tgl_pendaftaran'] = $value['tgl_pendaftaran'] != '' ? DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($value['tgl_pendaftaran'])), false, false) : '';
                        unset($value['pasien_id']);
                        $data[$key] = $value;
                    }

                    $result['data'] = $data;
                    $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                    $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
                }
                else {
                    $result['data'] = $data;
                    $result['recordsTotal'] = 0;
                    $result['recordsFiltered'] = 0;
                }
            }

            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    /**
     * @todo Fungsi untuk mendapatkan data kunjungan pasien bayi
     * @author Budi <budi@docotel.com>
     */
    public function actionGetDataKunjunganPasienBayi()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $pasien_id = $request->get('pasien_id', null);
            $draw = $request->get('draw', 1);
            $no = $request->get('start', 1);
            $data = [];

            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;
            $result['recordsFiltered'] = 0;

            if ($pasien_id) {
                $restPendaftaran = $this->_restPendaftaran->get('pendaftaran/get-data-kunjungan-pasien-bayi?pasien_id='.$pasien_id.'&'.http_build_query($yiiRestfulParams), ['form_params' => []]);
                $body = json_decode($restPendaftaran->getBody(), true);
                $data = $body['response']['data'];

                if (!empty($data)) {
                    foreach ($data as $key => $value) {
                        $no++;
                        $value['tgl_pendaftaran'] = $value['tgl_pendaftaran'] != '' ? DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($value['tgl_pendaftaran'])), false, false) : '';
                        unset($value['pasien_id']);
                        $data[$key] = $value;
                    }

                    $result['data'] = $data;
                    $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                    $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
                }
                else {
                    $result['data'] = $data;
                    $result['recordsTotal'] = 0;
                    $result['recordsFiltered'] = 0;
                }
            }

            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionBayiLahir($id_booking = null)
    {
        $session = Yii::$app->session;
        $active_workspace = $session->get('active_workspace');
        $loket_id = null;
        $loket_nama = '';
        $ruanganId = $active_workspace['ruangan_id'];
        $ruanganNama = $active_workspace['ruangan_name'];
        $isPenunjang = false;
        $param = 'ranap';
        // try{
        //     $param = DocoConstants::PARAM_DFTR[$ruanganId];
        // }catch(\yii\base\ErrorException $e) {

        // }

        $title = Yii::t('fe', 'Bayi Lahir');
        $modelPasien = new PasienForm();
        $modelKunjungan = new KunjunganForm();

        $modelBpjs = new BpjsNewForm();
        $modelRujukan = new RujukanForm();
        $modelAsuransi = new AsuransiForm();
        $modelPemeriksaan = [];

        $modelAdmisi = new PasienAdmisiForm();
        $instalasi_id = json_encode(DocoConstants::INSTALASI_ID_RI);
        $data = $this->getDataApi($instalasi_id, 2);

        // prevent data
        $ruangan = $carabayar = $asalrujukan = $jeniskasus = $instalasi = $jenisidentitas = [];

        if(!empty($data['jenis_identitas'])) {
            $jenisidentitas = $data['jenis_identitas'];
            $jenisidentitasOptions = [];
            foreach ($jenisidentitas as $key => $value) {
                $jenisidentitasOptions[$value['lookup_id']] = ['data-id' => $value['lookup_id']];
            }
            $jenisidentitas = ArrayHelper::map($carabayar, 'lookup_id', 'lookup_name');
        }

        if (!empty($data['ruangan'])) {
            $ruangan = ArrayHelper::map($data['ruangan'], 'ruangan_id','ruangan_nama');
        }

        if (!empty($data['cara_bayar'])) {
            $carabayar = $data['cara_bayar'];
            $carabayarOptions = [];
            foreach ($carabayar as $key => $value) {
                $carabayarOptions[$value['carabayar_id']] = ['data-id' => $value['groupcarabayar_id']];
            }
            $carabayar = ArrayHelper::map($carabayar, 'carabayar_id', 'carabayar_nama');
        }

        if (!empty($data['asal_rujukan'])) {
            $asalrujukan = $data['asal_rujukan'];
        }

        if (!empty($data['kelas_pelayanan'])) {
            $kelaspelayanan = ArrayHelper::map($data['kelas_pelayanan'], 'kelaspelayanan_id','kelaspelayanan_nama');
        }

        if (!empty($data['jeniskasus'])) {
            $jeniskasus = ArrayHelper::map($data['jeniskasus'], 'jeniskasuspenyakit_id','jeniskasuspenyakit_nama');
        }
        $instalasi = (!empty($data['instalasi']) ? ArrayHelper::map($data['instalasi'], 'instalasi_id', 'instalasi_nama') : []);

        $optionsProv = [];
        foreach ($data['master']['propinsi'] as $key => $propinsi) {
            $optionsProv[$propinsi['propinsi_id']] = ['data-kode'=>$propinsi['kode_propinsi']];
        }

        // need for render partial
        $packFormPasien = [
            'param' => $param,
            'modelPasien' => $modelPasien,
            'listResponses' => $data['lookup'],
            'listMasters' => $data['master'],
            'optionsProv' => $optionsProv,
            'isPenunjang' => $isPenunjang,
        ];
        $packFormKunjungan = [
            'modelKunjungan' => $modelKunjungan,
            'ruangan' => $ruangan,
            'carabayar' => $carabayar,
            'listResponses' => $data['lookup'],
            'listRujukan' => $asalrujukan,
            'carabayarOptions' => $carabayarOptions,
            'instalasi_id' => $instalasi_id,
            'listInstalasi' => $instalasi,
            'isPenunjang' => $isPenunjang,
            'modelPemeriksaan'=> $modelPemeriksaan
        ];
        $packFormKunjunganRanap = [
            'modelAdmisi' => $modelAdmisi,
            'jeniskasus' => $jeniskasus,
            'kelaspelayanan' => $kelaspelayanan,
            'carabayar' => $carabayar,
            'listResponses' => $data['lookup'],
            'listRujukan' => $asalrujukan,
            'carabayarOptions' => $carabayarOptions,
            'instalasi_id' => $instalasi_id,
            'id_booking' => $id_booking
        ];
        $packFormAsuransi = [
            'modelAsuransi' => $modelAsuransi,
            'ruangan' => $ruangan,
            'carabayar' => $carabayar,
            'kelaspelayanan' => $kelaspelayanan
        ];

        // var_dump($packFormPasien['listResponses']['jenis_identitas']);die;
        $packFormBpjs = ['modelBpjs' => $modelBpjs];
        $packFormRujukan = ['modelRujukan' => $modelRujukan];

        $pegawai_id = Yii::$app->docoVars->user('pegawai_id');
        $cache_pemesanan_kamar = Yii::$app->cache->get('data_booking' . $id_booking . $pegawai_id);
        if ($cache_pemesanan_kamar !== false) {
            $data_pemesanan_kamar = json_encode($cache_pemesanan_kamar);
        }

        if(Yii::$app->request->post()){
            // $loginpemakai_id = Yii::$app->docoVars->user("id");
            // $requests = $this->_restPendaftaran->get('allow/get-current-loket?loginpemakai_id='.$loginpemakai_id);
            // $response = json_decode($requests->getBody(), true);
            // $response = $response['response'];

            // if (!$response) {
            //     return $this->actionPilihLoket(DocoConstants::JA_PNG);
            // } else {
            //     $session = Yii::$app->session;
            //     $active_workspace = $session->get('active_workspace');
            //     $active_workspace['loket'] = [$loginpemakai_id=>$response];
            //     $session->set('active_workspace', $active_workspace);
            //     $loket_id = $response['loket_id'];
            //     $loket_nama = $response['loket_nama'];
            // }

            $cek_pjawab = Yii::$app->request->post("HiddenPenanggungJawab");
            // if ($cek_pjawab==0) {
            //     $modelKunjungan->scenario = "with_mandatory_pjawab";
            //     $modelAdmisi->scenario = "with_mandatory_pjawab";
            // }else{
            //     $modelKunjungan->scenario = "with_mandatory_all";
            //     $modelAdmisi->scenario = "with_mandatory_all";
            // }

            $response = '';
            try {
                $post = Yii::$app->request->post();

                // $modelPasien->load($post);

                $modelKunjungan->load($post);
                $modelAdmisi->load($post);
                $modelRujukan->load($post);
                $modelAsuransi->load($post);

                if($param != 'penunjang'){
                    $modelKunjungan->instalasi_id = $instalasi_id;
                }

                if($param != 'ranap' && !$modelKunjungan->validate()){
                    $formName = substr(strrchr(get_class($modelKunjungan), "\\"), 1);
                    $response = $modelKunjungan->getErrors();
                    return DocoHelpers::response($response, 422, $formName);
                }

                if($param == 'ranap' && !$modelAdmisi->validate()){
                    $formName = substr(strrchr(get_class($modelAdmisi), "\\"), 1);
                    $response = $modelAdmisi->getErrors();
                    return DocoHelpers::response($response, 422, $formName);
                }

                $modelRujukan->asalrujukan_id = (int) $post['PasienAdmisiForm']['asalrujukan_id'];
                if (!in_array($modelRujukan->asalrujukan_id, [DocoConstants::ASAL_RUJUKAN_DS, DocoConstants::ASAL_RUJUKAN_RI])
                    && !$modelRujukan->validate()) {
                    $formName = substr(strrchr(get_class($modelRujukan), "\\"), 1);
                    $response = $modelRujukan->getErrors();
                    return DocoHelpers::response($response, 422, $formName);
                }

                // $modelAdmisi->kelahiranbayi_id =
                $modelAsuransi->carabayar_id = $modelAdmisi->carabayar_id;
                if ($modelAsuransi->carabayar_id == DocoConstants::CARA_BAYAR_ASU) {
                    if  (!$modelAsuransi->validate()) {
                        $formName = substr(strrchr(get_class($modelAsuransi), "\\"), 1);
                        $response = $modelAsuransi->getErrors();
                        return DocoHelpers::response($response, 422, $formName);
                    }
                }

                $sent = $this->_restPendaftaran->post('pendaftaran/save-pendaftaran-bayi',['query'=>['params'=>$param],'form_params'=>$post]);
                $response = json_decode($sent->getBody(), true);

                // delete cache informasi pemesanan kamar
                if ($cache_pemesanan_kamar !== false) {
                    Yii::$app->cache->delete('data_booking' . $id_booking . $pegawai_id);
                }
                return DocoHelpers::response($response);
            } catch (RequestException $e) {
                $response = json_decode($e->getResponse()->getBody(), true);
                $response['response']['title'] = 'Terjadi Kesalahan';
                $response['response']['text'] = isset($response['response']['message']) ? $response['response']['message'] : 'Terjadi Kesalahan';
                return DocoHelpers::response($response, 422);
                return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
            } catch(Exception $e){
                $response = json_decode($e->getResponse()->getBody(), true);
                $response['response']['title'] = 'Terjadi Kesalahan';
                $response['response']['text'] = isset($response['response']['message']) ? $response['response']['message'] : 'Terjadi Kesalahan';
                return DocoHelpers::response($response, 422);
                return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
            }
        }
        $modelKunjungan->scenario = "with_mandatory_all";
        $modelAdmisi->scenario = "with_mandatory_all";
        $jenisantrian_id = DocoConstants::JA_PDN;
        return $this->render('bayi_lahir', get_defined_vars());
    }

    public function actionGetFormBpjs()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $pendaftaran_id = DocoHelpers::decrypt($get['pendaftaran_id']);
        $no_rekam_medik = $get['no_rekam_medik'];
        $param = $get['param'];
        $is_ranap = ($param == 'ranap') ? 1 : 0;

        $dataPendaftaran =  $this->getPendaftaran($pendaftaran_id);
        $pasienadmisi_id = isset($dataPendaftaran['pasienadmisi_id']) ? $dataPendaftaran['pasienadmisi_id'] : null;
        $nama_pasien = isset($dataPendaftaran['nama_pasien']) ?
        $dataPendaftaran['nama_pasien'] : '';

        $modelBpjs = new BpjsNewForm();
        $modelBpjs->pendaftaran_id = $pendaftaran_id;
        $modelBpjs->no_rekam_medik = $no_rekam_medik;
        $modelBpjs->pasienadmisi_id = $pasienadmisi_id;
        $modelBpjs->nama_pasien = $nama_pasien;
        $modelBpjs->jenis_pelayanan = ($is_ranap == 1) ? 1 : 2;

        $packFormBpjs = ['modelBpjs' => $modelBpjs];

        return $this->renderAjax('partial/_formbpjs', get_defined_vars());
    }

    private function getPendaftaran($pendaftaran_id)
    {
        try {
            $requests = $this->_restPendaftaran->get('allow/get-pendaftaran?pendaftaran_id='.$pendaftaran_id);
            $response = json_decode($requests->getBody(), true);
            $result = ['response'=>$response['response']];

            return $result['response'];
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
        } catch (\Exception $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
        }
    }

    public function actionGetFormAsuransi()
    {
        $request = Yii::$app->request;
        $get = $request->get();

        $pendaftaran_id = isset($get['pendaftaran_id']) ? $get['pendaftaran_id'] : null;
        $asalrujukan_id = isset($get['asalrujukan_id']) ? $get['asalrujukan_id'] : null;
        $penjamin_id = isset($get['penjamin_id']) ? $get['penjamin_id'] : null;
        $pasien_id = isset($get['pasien_id']) ? $get['pasien_id'] : null;
        $carabayar_id = isset($get['carabayar_id']) ? $get['carabayar_id'] : null;
        $params = null;

        if($params == 'bayi' || $params == 'ranap') {
            $instalasi_id = DocoConstants::INSTALASI_ID_RI;
        } elseif($params == 'igd') {
            $instalasi_id = DocoConstants::INSTALASI_ID_RD;
        } else {
            $instalasi_id = DocoConstants::INSTALASI_ID_RJ;
        }

        $modelAsuransi = new AsuransiForm();
        $modelRujukan = new RujukanForm();

        $modelAsuransi->pendaftaran_id = $pendaftaran_id;
        $formName = substr(strrchr(get_class($modelAsuransi), "\\"), 1);
        $packFormAsuransi = ['modelAsuransi' => $modelAsuransi];
        $packFormRujukan = ['modelRujukan' => $modelRujukan];
        $rujukandari = $this->getRujukanDari($asalrujukan_id);
        $data = $this->getDataApi($instalasi_id, 2);
        $kelaspelayanan = $data['kelas_pelayanan'];

        if(Yii::$app->request->post()) {
            $post = Yii::$app->request->post();
            $post['pendaftaran_id'] = $pendaftaran_id;
            $post['asalrujukan_id'] = $asalrujukan_id;
            $post['penjamin_id'] = $penjamin_id;
            $post['pasien_id'] = $pasien_id;
            $post['carabayar_id'] = $carabayar_id;
            $post['pasien_id'] = $pasien_id;

            $is_rujukan = isset($post['RujukanForm']) ? true : false;
            $modelAsuransi->attributes = $post['AsuransiForm'];
            $modelRujukan->attributes = $post['RujukanForm'];
            $modelRujukan->asalrujukan_id = $post['asalrujukan_id'];
            $modelRujukan->is_rujukan = $is_rujukan;

            $modelAsuransi->asalrujukan_id = $post['asalrujukan_id'];
            $modelAsuransi->carabayar_id = $post['carabayar_id'];
            $modelAsuransi->penjamin_id = $post['penjamin_id'];
            $modelAsuransi->pasien_id = $post['pasien_id'];

            if (!in_array($modelRujukan->asalrujukan_id, [DocoConstants::ASAL_RUJUKAN_DS, DocoConstants::ASAL_RUJUKAN_RI])
                && !$modelRujukan->validate()) {
                $formName = substr(strrchr(get_class($modelRujukan), "\\"), 1);
                $response = $modelRujukan->getErrors();
                return DocoHelpers::response($response, 422, $formName);
            }

            $data = array_merge($modelAsuransi->attributes, $modelRujukan->attributes);

            if($modelAsuransi->validate()) {
                try {
                    $response = $this->_restPendaftaran->post('pendaftaran-igd/save-data-asuransi', [
                        'query' => [
                            'is_rujukan' => $is_rujukan
                        ],
                        'form_params' => $data
                    ]);

                    return DocoHelpers::responseJsonString($response->getBody(), $formName);
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            }
            else {
                $errors = DocoHelpers::parseError($modelAsuransi->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }

        return $this->renderAjax('partial/_formAsuransi', get_defined_vars());
    }

    private function getRujukanDari($asalrujukan_id)
    {
        try {
            $requests = $this->_restPendaftaran->get('allow/get-rujukan-dari?id='.$asalrujukan_id);
            $response = json_decode($requests->getBody(), true);
            $result = ['response'=>$response['response']];

            return $result['response'];
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
        } catch (\Exception $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
        }
    }

    /**
     * Get data kamar by ruangan_id & kelaspelayanan_id
     *
     * @param String/Integer $ruanganId
     * @param String/Integer $kelasPelayananId
     * @author Tsani Nashrullah <tsani@docotel.com>
     * @return JSON
     **/
    public function actionKamarRuangan()
    {
        $request = Yii::$app->request;
        $payload = Yii::$app->request->get();
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $klasifikasikamar_id = $request->get('klasifikasiKamarId', null);

        try {
            if (!empty($payload['ruanganId'])) {
                $requests = $this->_restPendaftaran->get('allow/kamar-ruangan?ruanganId='.$payload['ruanganId'].'&kelasPelayananId='.$payload['kelasPelayananId'].'&klasifikasiKamarId='.$klasifikasikamar_id);
                $response = json_decode($requests->getBody(), true);
                return [
                    'data' => $response['response']['data'],
                ];
            } else {
                return [
                    'data' => [],
                    'message' => 'Mohon pastikan untuk mengirim data ruangan, kelas pelayanan dan jenis kasus penyakit.'
                ];
            }
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            if (YII_ENV == 'dev') {
                return [
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile()
                ];
            } else {
                return [
                    'message' => Yii::t('fe', 'error_500')
                ];
            }
        }
    }

    public function actionDetailPaket($id = null, $instalasi_id = null)
    {
        $title = Yii::t('fe', 'Detail Paket');
        $id_encrypt = $id;
        $id = DocoHelpers::decrypt($id);
        return $this->renderAjax('partial/_detailpaket', get_defined_vars());
    }

    public function actionGetDataPaketDetail($id = null)
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
            $response = $this->_restPendaftaran->get('allow/detail-paket-mcu?tipepaket_id='.$id, ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            if(isset($body['response']['data'][0]['detail'])) {
                $detail = json_decode($body['response']['data'][0]['detail'], true);
                $no = $request->get('start', 0);
                if(!empty($detail)) {
                    foreach ($detail as $key => $value) {
                        $no++;
                        $value['rowNum'] = $no;
                        $value['tipepaket_nama'] = $value['nama'];
                        $value['kelompoktindakan_nama'] = '-';
                        $data[$key] = $value;
                    }
                }
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

    /**
     * Get List tindakan or paket by tariftindakan_id
     *
     * @return array
     */
    public function actionListTarifTindakanPaket()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $tindakan = [];
        $paket = [];
        $return = [];
        $get = $request->get();

        if(isset($get['listTindakan']) && !empty($get['listTindakan'])){
            $tindakan = $this->actionGetTarifTindakan();
            $tindakan = $tindakan['data'];
        }

        if(isset($get['listPaket']) && !empty($get['listPaket'])){
            $paket = $this->actionGetTarifPaket();
            $paket = $paket['data'];
        }

        if(!empty($tindakan) || !empty($paket)){
            $return = ArrayHelper::merge($tindakan, $paket);
        }

        return $return;
    }

    public function actionDownloadTemplate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        try {
            $path = Yii::getAlias("@download") . "/template-mcu.xlsx";
            $response = $this->_restPendaftaran->get('pendaftaran-mcu/download-excel',[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            $url = $body['response'];

            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e){
           return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionUploadTemplate()
    {
        $request = Yii::$app->request;
        $model = new TipePasienForm();
        $model->upload_file = UploadedFile::getInstance($model, 'upload_file');
        $result = $info = $tmpRm = $countRm = [];
        $group_carabayar = $request->get('group_carabayar',null);

        if ($model->upload_file == NULL) {

            $response['response']['file'] = '';
            $response['response']['data'] = $result;
            $response['response']['status'] = 422;
            $response['response']['title'] = 'Proses Gagal';
            $response['response']['text'] = 'Format yang di Upload tidak sesuai, harus berupa xls, xlsx';

            return DocoHelpers::response($response, 422);
        }

        $fileName = $model->upload_file->name;
        $file = $model->upload_file->tempName;
        if (isset($model->upload_file)) {
            $getDataImport=  DocoHelpers::getUploadFileExcel($file);
            $nomor = 0;
            $groupOrder = 0;
            $countIncomplete = 0;
            $countComplete = 0;
            $countDouble = 0;
            for($row=4; $row <= $getDataImport['highestRow']; $row++){
                $rowData = $getDataImport['sheet']->rangeToArray('B'.$row.':'.$getDataImport['highestColumn'].$row,Null,true, false);

                foreach ($rowData as $key => $value) {
                    $nomor++;
                    $status = self::COMPLETE;

                    if (!$value[0] && !$value[1] && !$value[2] && !$value[3] && !$value[4] && !$value[5] && !$value[6] && !$value[7] && !$value[8] && !$value[9] && !$value[10] && !$value[11] && !$value[12] && !$value[13] && !$value[14] && !$value[15]) {
                        continue;
                    } else if (!empty($group_carabayar) && $group_carabayar != 419) {
                        if (!$value[2] || !$value[3] || !$value[5] || !$value[6] || !$value[7] || !$value[8] || !$value[9] || !$value[10] || !$value[11] || !$value[12] || !$value[13] || !$value[14] || !$value[15]) {
                            $status = self::INCOMPLETE;
                            $groupOrder = 1;
                            $countIncomplete++;
                        } else {
                            $countComplete++;
                        }
                    } else if (!$value[0] || !$value[1] || !$value[2] || !$value[3] || !$value[5] || !$value[6] || !$value[7] || !$value[8] || !$value[9] || !$value[10] || !$value[11] || !$value[12] || !$value[13] || !$value[14] || !$value[15]) {
                        $status = self::INCOMPLETE;
                        $groupOrder = 1;
                        $countIncomplete++;
                    } else {
                        $countComplete++;
                    }

                    if($rowData[0][4] != ''){
                        $tmpRm[] = strval($rowData[0][4]);
                    }

                    $tgl = null;
                    if ($rowData[0][7]) {
                        $tgl  = $rowData[0][7];
                        if(!strpos($tgl, '/')) {
                            $tgl = ($tgl - 25569) * 86400;
                            $tgl = gmdate("d-m-Y", $tgl);
                        } else {
                            $tgl = str_replace('/', '-', $rowData[0][7]);
                        }
                    }

                    $item = [
                        'nokartuasuransi'    => !empty($rowData[0][0])? $rowData[0][0]:'',
                        'namapemilikasuransi'   => !empty($rowData[0][1])? $rowData[0][1]:'',
                        'jenisidentitas'  => !empty($rowData[0][2])? $rowData[0][2]:'', // constant
                        'no_identitas_pasien' => !empty($rowData[0][3])? $rowData[0][3]:'',
                        self::NORM => $rowData[0][4],
                        'nama_pasien' => !empty($rowData[0][5])? $rowData[0][5]:'',
                        'tempat_lahir' => !empty($rowData[0][6])? $rowData[0][6]:'',
                        'tanggal_lahir' => $tgl,
                        'jenis_kelamin' => !empty($rowData[0][8])? $rowData[0][8]:'', // constant
                        'golongandarah' => !empty($rowData[0][9])? $rowData[0][9]:'', //constant
                        'statusperkawinan' => !empty($rowData[0][10])? $rowData[0][10]:'', //constant
                        'alamat_pasien' => !empty($rowData[0][11])? $rowData[0][11]:'',
                        'no_telepon_pasien' => !empty($rowData[0][12])? $rowData[0][12]:'',
                        'no_rujukan' => !empty($rowData[0][13])? $rowData[0][13]:'',
                        'asalrujukan' => !empty($rowData[0][14])? $rowData[0][14]:'', //constant
                        'nama_perujuk' => !empty($rowData[0][15])? $rowData[0][15]:'',
                        'status'   => $status,
                        'no'   => $nomor,
                        'group' => $groupOrder
                    ];

                    $result[] = $item;

                    $info = [
                        'dataComplete' => $countComplete,
                        'dataIncomplete' => $countIncomplete,
                        'dataDouble' => 0,
                    ];
                }
            }

            $countRm = array_count_values($tmpRm);

            if(!empty($tmpRm)){
                try {
                    $request = $this->_restPendaftaran->post('pendaftaran-mcu/validasi-rekam-medik', [
                        'form_params' => $tmpRm
                    ]);
                    $body = json_decode($request->getBody(),TRUE);
                    $tmpRm = $body['response'];
                } catch (\Exception $e) {
                    return DocoHelpers::response(['message' => $e->getMessage()],500);
                } catch (RequestException $e){
                    return DocoHelpers::response(['message' => $e->getMessage()],500);
                }
            }

            foreach($result as $k => $v) {
                $isValid = true;
                if($v[self::NORM] != ''){
                    if(!empty($tmpRm) && $tmpRm[$v[self::NORM]] != true && $v['status'] == self::COMPLETE){
                        $isValid = false;
                    }
                    if(!empty($countRm) && $countRm[$v[self::NORM]] > 1 && $v['status'] == self::COMPLETE){
                        $isValid = false;
                    }
                    if(!$isValid){
                        $countDouble++;
                        $countComplete--;
                        $result[$k]['status'] = self::DUPLIKASI;
                        $result[$k]['group'] = 1;
                        $info['dataDouble'] = $countDouble;
                        $info['dataComplete'] = $countComplete;
                    }
                }
            }

            if(!empty($result)){
                try {
                    $request = $this->_restPendaftaran->post('pendaftaran-mcu/validasi-identitas-pasien', [
                        'form_params' => $result
                    ]);
                    $body = json_decode($request->getBody(),TRUE);
                    $result = $body['response']['data'];
                    $info['dataMultipleRM'] = $body['response']['count'];
                } catch (\Exception $e) {
                    return DocoHelpers::response(['message' => $e->getMessage()],500);
                } catch (RequestException $e){
                    return DocoHelpers::response(['message' => $e->getMessage()],500);
                }
            }

            ArrayHelper::multisort($result, ['group', self::NORM], [SORT_DESC, SORT_DESC]);

            $response['response']['file'] = $fileName;
            $response['response']['data'] = $result;
            $response['response']['status'] = 200;
            $response['response']['title'] = 'Proses Berhasil';
            $response['response']['text'] = 'Data Berhasil di upload!';
            $response['response']['info'] = $info;
            $response['response']['tmp'] = $countRm;

            return DocoHelpers::response($response ,200);
        }
        else {
            $response['response']['title'] = 'Proses Gagal!';
            $response['response']['text'] = 'File gagal di upload.';
            return DocoHelpers::response($response, 422);
        }
    }

    public function actionGetNomorUrut($is_nomor_urut=null)
    {
        return Yii::$app->docoPlugin->execute($this,'nomor_urut');

        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $depdrop_params = $request->post('depdrop_params');
        $depdrop_all_params = $request->post('depdrop_all_params');//dari reservasi
        $is_update = $request->post('is_update');
        $date = null;
        $no_antrian_user = null;
        $monthsFull = [
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'Nopember',
            'Desember'
        ];
        $month = '';

        if(!empty($depdrop_all_params)) {

            if(!empty($depdrop_all_params['kunjunganform-dokter_id'])){
                $dokter_id = $depdrop_all_params['kunjunganform-dokter_id'];
            } else if(!empty($depdrop_all_params['pegawai_id'])){
                $dokter_id = $depdrop_all_params['pegawai_id'];
            } else{
                $dokter_id = ArrayHelper::getValue($depdrop_all_params, 'pegawai_id');
            }

             // convert to integer month, for reservasi poliklinik feature
             if(!empty($depdrop_all_params['tgl_pendaftaranol'])){
                if(strtotime($depdrop_all_params['tgl_pendaftaranol']) == false){
                    $temp_tgl_pendaftaranol = explode(' ',$depdrop_all_params['tgl_pendaftaranol']);
                    foreach ($monthsFull as $key => $value) {
                        # code...
                        if ($temp_tgl_pendaftaranol[1] == $value) {
                            # code...
                            $month = $key+1;
                        }
                    }
                    $tgl_pendaftaranol = $temp_tgl_pendaftaranol[0].'-'.$month.'-'.$temp_tgl_pendaftaranol[2];
                }else{
                    $tgl_pendaftaranol = $depdrop_all_params['tgl_pendaftaranol'];
                }
            }


            $ruangan_id = $depdrop_all_params['ruangan_id'];
            if (isset($depdrop_all_params[''])) {
                # code...
            }
            $tanggal_pendaftaran_online = isset($depdrop_all_params['tgl_pendaftaranol']) ? $tgl_pendaftaranol : date("Y-m-d");
            $date = date('Y-m-d',strtotime($tanggal_pendaftaran_online));

            if(!is_null($is_nomor_urut)){
                $no_antrian_user = $is_nomor_urut;
            }else {
                $no_antrian_user = null;
            }

        } else {
            $dokter_id = array_key_exists(0, $depdrop_parents) ? $depdrop_parents[0] : null;
            $ruangan_id = array_key_exists(0, $depdrop_params) ? $depdrop_params[0] : null;
        }

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        if(empty($dokter_id) || empty($ruangan_id)) return $result;

        try {
            $restPendaftaran = $this->_restPendaftaran->post('allow/get-nomor-urut?ruangan_id='.$ruangan_id.'&dokter_id='.$dokter_id.'&date='.$date.'&no_antrian_user='.$no_antrian_user);
            $response = json_decode($restPendaftaran->getBody(), true)['response'];
            if (!empty($response)) {
                if ($is_update) {
                    foreach ($response as $key => $value) {
                        $result['output'][] = [
                            'id' => $key,
                            'text' => $value
                        ];

                        if (empty($result['selected'])) {
                            $result['selected'] = $key;
                        }
                    }
                } else {
                    if (!empty($is_nomor_urut)){
                        foreach ($response as $key => $value) {
                            if ($key == $is_nomor_urut){
                                $result['output'][] = [
                                    'id' => $key,
                                    'name' => $value
                                ];
                            }
                            if (empty($result['selected'])) {
                                $result['selected'] = $is_nomor_urut;
                            }
                        }
                    } else {
                        foreach ($response as $key => $value) {
                            $result['output'][] = [
                                'id' => $key,
                                'name' => $value
                            ];

                            if (empty($result['selected'])) {
                                $result['selected'] = $is_nomor_urut;
                            }
                        }
                    }

                }
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

    public function actionGetKunjungan()
    {
        $request = Yii::$app->request;
        $no_rekam_medik = $request->get('no_rekam_medik',null);
        $session = Yii::$app->session;
        $active_workspace = $session->get('active_workspace');
        $ruanganId = $active_workspace['ruangan_id'];

        try {
            $response = $this->_restPendaftaran->get('pendaftaran/get-kunjungan-by-rekam-medik',[
                'query' => [
                    'no_rekam_medik' => $no_rekam_medik,
                    'jenis' => $ruanganId
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            $data = isset($body['response']['data']) ? $body['response']['data'] : [];
            $results = $data;

            return DocoHelpers::response(['results' => $results]);
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'messages' => $e->getMessage()
            ],422);
        } catch (\Exception $e) {
            return DocoHelpers::response([
                'messages' => $e->getMessage()
            ],422);
        }
    }

    public function actionPencarianIdentitas()
    {
        $model = new PencarianIdentitasForm;
        return $this->renderAjax('pencarian_identitas', get_defined_vars());
    }

    public function actionGetDataPasienBpjs()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $draw = $request->get('draw', 1);


        $no_kartu = $request->get('no_kartu');
        $nik = $request->get('nik');
        $jenis_pencarian = $request->get('jenis_pencarian');
        $asal_rujukan = $request->get('asal_rujukan');
        $date = date('Y-m-d');

        if ($jenis_pencarian == DocoConstants::PENCARIAN_NIK){
            $peserta = $this->bpjsService->cariPesertaNik($nik, $date);
            if ($peserta && $peserta['metaData']['code'] == 200) {
                $no_kartu = $peserta['response']['peserta']['noKartu'];
            } else {
                $peserta['metaData']['message'] = 'NIK tidak Ditemukan';
                return DocoHelpers::response($peserta, 422);
            }

        }


        $results = [];
        $data = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        if ($asal_rujukan == DocoConstants::ASAL_RUJUKAN_FAKSES_1){
            $bridgeRes = $this->bpjsService->listRujukanNoKartu($no_kartu);
        } else {
            $bridgeRes = $this->bpjsService->listRujukanNoKartuRS($no_kartu);
        }


        if ($bridgeRes && $bridgeRes['metaData']['code'] == 200) {
            $list = $bridgeRes['response']['rujukan'];
            $no = 0;
            foreach ($list as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['noKartu'] = $value['peserta']['noKartu'];
                $value['nama'] = $value['peserta']['nama'];
                $value['nama_perujuk'] = $value['provPerujuk']['nama'];
                $value['no_rekam_medik'] = $value['peserta']['mr']['noMR'];
                $value['spesialis'] = $value['poliRujukan']['nama'];
                $value['aksi'] = Html::button(Yii::t('fe', 'Pilih'), [
                    'class' => 'btn btn-info btn-sm',
                    'data-id' => $value['noKunjungan'],
                    'data-norm' => $value['peserta']['mr']['noMR'],
                    'data-nama' => $value['peserta']['nama'],
                    'data-bpjs' => $value['peserta']['noKartu'],
                    'onClick' => 'onClickPilih(this)',
                ]);
                $data[$key] = $value;
            }
            $result['recordsTotal'] = $no;
        }

        $result['data'] = $data;
        $result['recordsTotal'] = count($data);
        $result['recordsFiltered'] = count($data);
        return DocoHelpers::response($result);
    }

    public function actionCekRujukan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;


        $no_rujukan = $request->post('no_rujukan');
        $jenis_pencarian = $request->post('jenis_pencarian');

        $rujukan = $this->bpjsService->cariBerdasarkanNoRujukan($no_rujukan, $jenis_pencarian);

        if ($rujukan && $rujukan['metaData']['code'] == 200) {
            return DocoHelpers::response($rujukan);
        } else {
            return DocoHelpers::response($rujukan, 422);
        }


    }

    public function actionConfirmEligiblePeserta()
    {
        $request = Yii::$app->request;
        $post = Yii::$app->request->post();
        $model = new PasienForm;
        $request = Yii::$app->request;
        $pasienBaru = filter_var($request->get('is_pasienbaru'), FILTER_VALIDATE_BOOLEAN);

        $getDataRequest = $this->_restPendaftaran->get('allow/identitas-pasien-asuransi',[
            'query' => [
                'no_kartu' => $request->get('no_asuransi'),
                'penjamin_id' => $request->get('penjamin_id'),
                'norm' => isset($pasienBaru) && ! $pasienBaru ? null : $request->get('no_rm'),
            ]
        ]);
        $parsingRequest = json_decode($getDataRequest->getBody(),TRUE);
        $response = $parsingRequest['response'];
                
        $insuranceData = ArrayHelper::getValue($response, 'data.pasienAsuransi.data.dataPeserta', []);
        $pasienData = ArrayHelper::getValue($response, 'data.pasien', []);

        return $this->renderAjax('partial/_eligible_peserta', compact('insuranceData', 'pasienData'));
    }

    public function actionGetReferensiBenefit()
    {
        try {
            $request = Yii::$app->request;
            $post = Yii::$app->request->post();
            $model = new PasienForm;
            $request = Yii::$app->request;
            $getDataRequest = $this->_restPendaftaran->get('allow/referensi-benefit',[
                'query' => [
                    'no_kartu' => $request->get('no_asuransi'),
                    'penjamin_id' => $request->get('penjamin_id'),
                ]
            ]);
            $parsingRequest = json_decode($getDataRequest->getBody(),TRUE);
            $response = $parsingRequest['response'];
            
            $benefit = ArrayHelper::getValue($response, 'data.data', []);
            $newBenefit = [];

            if (!empty($benefit)) {
                foreach ($benefit as $key => $value) {
                    $newBenefit[] = [
                        'id' => $value['kodebenefit'],
                        'text' => $value['namabenefit'],
                    ];
                }
            }

            return DocoHelpers::response([
                'results' => $newBenefit
            ]);
        } catch (\Exception $th) {
            return DocoHelpers::response($th->getMessage(), 500);
        }
    }

    public function actionCekEligiblePeserta()
    {
        try {
            $request = Yii::$app->request;
            $getDataRequest = $this->_restPendaftaran->get('allow/cek-eligible-peserta',[
                'query' => [
                    'no_kartu' => $request->get('no_asuransi'),
                    'penjamin_id' => $request->get('penjamin_id')
                ]
            ]);
            $parsingRequest = json_decode($getDataRequest->getBody(),TRUE);
            $response = $parsingRequest['response'];

            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionPencarianAsuransi()
    {
        try {
            return $this->renderAjax('pencarian_asuransi', get_defined_vars());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    public function actionCekPasienAsuransiNik()
    {
        try {
            $request = Yii::$app->request;
            $penjamin_id = $request->post('penjamin_id');
            $nik = $request->post('nik');
            $tanggal_lahir = $request->post('tanggal_lahir');

            return $this->guzzleExec($this->_restPendaftaran, [
                'url' => 'allow/cek-pasien-asuransi-nik',
                'method' => 'GET',
                'payload' => [
                    'query' => [
                        'penjamin_id' => $penjamin_id,
                        'nik' => $nik,
                        'tanggal_lahir' => $tanggal_lahir
                    ]
                ],
                'returnResponse' => true  
            ]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }
}
