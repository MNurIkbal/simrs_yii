<?php
// author : rizal@docotel.com

namespace Doco\ranap\controllers;

use app\components\DocoConstants;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use phpDocumentor\Reflection\Types\String_;
use Symfony\Component\Console\Input\StringInput;
use Yii;
use yii\base\DynamicModel;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;

class EndPointController extends DocoController
{

    protected $_restRanap;
    protected $_restPendaftaran;
    protected $_restKasir;
    protected $_restDefault;
    /**
     * @inheritdoc
     */

    public function init()
    {
        parent::init();
        $this->_restRanap = Yii::$app->docoRest->ranap;
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;
        $this->_restKasir = Yii::$app->docoRest->kasir;
        $this->_restDefault = $this->_restRanap;
    }

    public function beforeAction($action)
    {
        return true;
    }

    public function actionGetListObatAlkes($q = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $response = $this->_restRanap->post('allow/get-list-obat-alkes', [
            'form_params' => ['keyword' => $q],
        ]);
        $response = json_decode($response->getBody(), true);
        $results = [];
        if ($response['metadata']['status'] == 200) {
            $list = $response['response'];
            foreach ($list as $key => $each) {
                $results[] = [
                    'id' => $each['obatalkes_id'],
                    'text' => $each['obatalkes_nama'],
                ];
            }
        }

        return ['results' => $results];
    }

    public function actionGetListRuangan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $payloadRequest = $request->get();
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_instalasi = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            // ruangan ranap by jenis kasus penyakit and kelas pelayanan
            if (isset($payloadRequest['jenisKasusPenyakit']) && isset($payloadRequest['kelasPelayanan'])) {
                $apiService = $this->_restPendaftaran->get(
                    'allow/get-list-ruangan-kelas',
                    [
                        'query' => [
                            'jeniskasuspenyakit_id' => $payloadRequest['jenisKasusPenyakit'],
                            'instalasi_id' => DocoConstants::INSTALASI_ID_RI,
                            'kelaspelayanan_id' => $payloadRequest['kelasPelayanan'],
                            'dropdown' => true
                        ],
                    ]
                );
                $result = json_decode($apiService->getBody());
            } else {
                $response = $this->_restRanap->get('allow/get-list-ruangan?instalasi_id=' . $parent_instalasi);
                $body = json_decode($response->getBody(), true);
                foreach ($body['response'] as $value) {
                    $result['output'][] = [
                        'id' => $value['ruangan_id'],
                        'name' => $value['ruangan_nama'],
                    ];
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

    // penunjang CLONE FROM DAFTARCONTROLLER
    // Rizal Faidin
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
            ];
            $data = [];
            $draw = $request->get('draw', 1);
            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;
            $result['recordsTotal'] = 0;

            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($get);
            $params['page'] = $yiiRestfulParams['page'];
            $params['per-page'] = $yiiRestfulParams['per-page'];

            if (isset($yiiRestfulParams['advanced-filter']['tipepaket_nama'])) {
                $params['tipepaket_nama'] = strtolower($yiiRestfulParams['advanced-filter']['tipepaket_nama']);
            }

            if(isset($yiiRestfulParams['advanced-filter']['jenispemeriksaanlab_nama'])){
                $params['jenispemeriksaanlab_nama'] = strtolower($yiiRestfulParams['advanced-filter']['jenispemeriksaanlab_nama']);
            }

            $url = 'allow/get-tarif-paket';
            $response = $this->_restKasir->get($url, ['query'=>$params]);
            // $response = $this->_restRanap->get($url, ['query' => $params]);
            $body = json_decode($response->getBody(), true);
            $body = isset($body['response']) ? $body['response'] : [];
            $no = 0;
            foreach ($body['data'] as $key => $value):
                $no++;
                $value['rowNum'] = $no;

                if (isset($value['paketDetailView'])) {
                    if (count($value['paketDetailView']) > 0) {
                        foreach ($value['paketDetailView'] as $k => $v):
                            $value['nama_tindakan_paket'][] = $v['daftartindakan_nama'];
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
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
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
            ];
            $data = [];
            $draw = $request->get('draw', 1);
            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;
            $result['recordsTotal'] = 0;

            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($get);
            $params['page'] = $yiiRestfulParams['page'];
            $params['per-page'] = $yiiRestfulParams['per-page'];

            if (isset($yiiRestfulParams['advanced-filter']['jenispemeriksaanlab_nama'])) {
                $params['jenispemeriksaanlab_nama'] = strtolower($yiiRestfulParams['advanced-filter']['jenispemeriksaanlab_nama']);
            }

            if (isset($yiiRestfulParams['advanced-filter']['daftartindakan_nama'])){
                $params['daftartindakan_nama'] = strtolower($yiiRestfulParams['advanced-filter']['daftartindakan_nama']);
            }

            $url = 'allow/get-tarif-tindakan';
            $response = $this->_restKasir->get($url, ['query'=>$params]);
            // $response = $this->_restRanap->get($url, ['query' => $params]);
            $body = json_decode($response->getBody(), true);
            $body = isset($body['response']) ? $body['response'] : [];
            $no = 0;
            foreach ($body['data'] as $key => $value):
                $no++;
                $value['rowNum'] = $no;
                $data[$key] = $value;
            endforeach;
            $result['data'] = $data;
            $result['recordsTotal'] = $body['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['_meta']['totalCount'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionGetAllDiagnosa($q = null, $page = null, $is_valueWithText = 0, $id = null, $set_id_as_text = 0)
    {
        try {
            $limit = 10;
            $offset = ($page - 1) * 10;
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $out = ['results' => ['id' => '', 'text' => '']];
            $response = $this->_restRanap->get('allow/get-all-diagnosa', [
                'query' => ['keyword' => $q, 'page' => $page, 'offset' => $offset, 'limit' => $limit],
            ]);
            $response = json_decode($response->getBody(), true);
            $results = [];
            if ($response['metadata']['status'] == 200) {
                $list = $response['response'];
                foreach ($list as $key => $each) {
                    if ($set_id_as_text == 0) {
                        if ($is_valueWithText == 2) { // normal condition
                            $results[] = [
                                'id' => $each['diagnosa_id'],
                                'text' => $each['nama_diagnosa'],
                            ];
                        } else {
                            $results[] = [
                                'id' => $each['diagnosa_id'] . '_' . $each['nama_diagnosa'],
                                'text' => $each['nama_diagnosa'],
                            ];
                        }
                    } else {
                        $results[] = [
                            'id' => $each['diagnosa_nama'],
                            'text' => $each['diagnosa_nama'],
                        ];
                    }
                }
                $out['results'] = $results;
                $out['pagination'] = ['more' => !empty($list) ? true : false];
            }

            return $out;
        } catch (RequestException $e) {
            return ['results' => ['id' => '', 'text' => '']];
        } catch (\Exception $e) {
            return ['results' => ['id' => '', 'text' => '']];
        }
    }

    public function actionGetDataKondisiKeluar($carakeluar_id)
    {
        try {
            $data = [];
            if (isset($carakeluar_id)) {
                $response = $this->_restRanap->get('allow/data-kondisi-keluar?carakeluar_id=' . $carakeluar_id);
                $body = json_decode($response->getBody(), true);
                foreach ($body['response'] as $key => $value) {
                    $data[] = [
                        'id' => $value['kondisikeluar_id'],
                        'text' => $value['kondisikeluar_nama'],
                        'parent' => $value['carakeluar_id'],
                    ];
                }
                $total = count($body['response']);
                $return = ['result' => $data];
                return DocoHelpers::response($return);
            }
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionGetListDokter($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];

        try {
            $response = $this->_restRanap->get('allow/get-list-dokter?q=' . $q);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response'] as $value) {
                $result['results'][] = [
                    'id' => $value['pegawai_id'],
                    'text' => $value['nama_pegawai'],
                ];
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

    public function actionGetListKasusPenyakit($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];

        try {
            $response = $this->_restRanap->get('allow/get-list-kasus-penyakit?q=' . $q);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response'] as $value) {
                $result['results'][] = [
                    'id' => $value['jeniskasuspenyakit_id'],
                    'text' => $value['jeniskasuspenyakit_nama'],
                ];
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

    public function actionGetListRuanganByKp()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $jkp_id = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restRanap->get('allow/get-list-ruangan-by-kp', [
                'query' => ['jeniskasuspenyakit_id' => $jkp_id],
            ]);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response'] as $value) {
                $result['output'][] = [
                    'id' => $value['ruangan_id'],
                    'name' => $value['ruangan_nama'],
                ];
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

    public function actionGetListRuanganByJenis()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $jkp_nama = $request->post('jeniskasuspenyakit_nama');
        
        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restRanap->get('allow/get-list-ruangan-by-kp', [
                'query' => ['jeniskasuspenyakit_nama' => $jkp_nama],
            ]);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response'] as $value) {
                $result['output'][] = [
                    'id' => $value['ruangan_id'],
                    'name' => $value['ruangan_nama'],
                ];
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

    public function actionGetKamarRuanganByKp()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restRanap->get('allow/get-kamar-ruangan-by-kp', [
                'query' => [
                    // 'jeniskasuspenyakit_nama' => $jkp_nama
                ],
            ]);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response'] as $value) {
                $result['output'][] = [
                    'id' => $value['kamarruangan_id'],
                    'name' => $value['kamarruangan_nokamar'],
                ];
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

    public function actionPilihTempatTidur()
    {
        $request = Yii::$app->request;
        $title = Yii::t('fe', 'Pilih Tempat Tidur');
        $ruangan_id = $request->get('ruangan_id');
        $jenis_id = $request->get('jenis_id');
        $kelas_id = $request->get('kelas_id');
        $jk = $request->get('jk');

        $masterWarnaTempatTidur = $this->_restRanap->get(
            'allow/get-warna-tempat-tidur', [
                'query' => [],
            ]);
        $body = json_decode($masterWarnaTempatTidur->getBody(), true);
        $getWarnaTempatTidur = $body['response'];

        return $this->renderAjax('_pemilihan_tempat_tidur', get_defined_vars());
    }

    public function actionCekKamarFleksibel($kamarruangan_id)
    {
        try {
            $response = $this->_restRanap->get('allow/cek-kamar-fleksibel?kamarruangan_id=' . $kamarruangan_id);
            $body = json_decode($response->getBody(), true);

            return DocoHelpers::response($body);
        } catch (RequestException $e) {
            // dump($e);exit;
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * This function is checking if the ruangan with ruangan_id, kelaspelayanan_id and penjamin_id
     *
     * @param Integer $ruangan_id ruangan
     * @param Integer $kelaspelayanan_id kelas pelayanan
     * @param Integer $penjamin_id penjamin
     * @method POST
     * @return JSON
     * @author Tsani Nashrullah <tsani@docotel.com>
     **/
    public function actionCekRuangan()
    {
        $payload = Yii::$app->request->post();
        $email = ArrayHelper::getValue($payload, 'ruangan_id');
        $payloadValidation = [
            'ruangan_id' => ArrayHelper::getValue($payload, 'ruangan_id'),
            'kelaspelayanan_id' => ArrayHelper::getValue($payload, 'kelaspelayanan_id'),
            'penjamin_id' => ArrayHelper::getValue($payload, 'penjamin_id'),
            'kamarruanganId' => ArrayHelper::getValue($payload, 'kamarruanganId'),
        ];
        $validation = DynamicModel::validateData($payloadValidation, [
            ['ruangan_id', 'required', 'message' => 'Ruangan tidak boleh kosong.'],
            ['kelaspelayanan_id', 'required', 'message' => 'Kelas Pelayanan tidak boleh kosong.'],
            ['penjamin_id', 'required', 'message' => 'Penjamin tidak boleh kosong.'],
            ['kamarruanganId', 'required', 'message' => 'Kammar Ruangan tidak boleh kosong.'],
        ]);

        if ($validation->hasErrors()) {
            return $this->helper->macroResponseJson(422, $this->helper->mapMessageErrorValidation($validation->errors));
        } else {
            try {
                $response = $this->_restPendaftaran->post('allow/cek-akomodasi-ruangan', [
                    'form_params' => [
                        'kelaspelayanan_id' => $payloadValidation['kelaspelayanan_id'],
                        'ruangan_id' => $payloadValidation['ruangan_id'],
                        'penjamin_id' => $payloadValidation['penjamin_id'],
                        'kamarruanganId' => $payloadValidation['kamarruanganId'],
                    ],
                ]);
                $contentGuzzle = json_decode($response->getBody())->response;
                return $this->helper->macroResponseJson($response->getStatusCode(), $contentGuzzle->message, $contentGuzzle->data);
            } catch (RequestException $e) {
                $contentGuzzle = json_decode($e->getResponse()->getBody(true));
                if (isset($contentGuzzle->metadata) && $contentGuzzle->metadata->status < 500) {
                    return $this->helper->macroResponseJson($contentGuzzle->metadata->status, $contentGuzzle->response->message, []);
                } else {
                    return $this->helper->macroResponseJson(500, Yii::t('fe', 'error_500'), []);
                }
            } catch (Exception $e) {
                return $this->helper->macroResponseJson(500, Yii::t('fe', 'error_500'), []);
            }
        }
    }

    /**
     * This function is checking if the ruangan with ruangan_id, kelaspelayanan_id and penjamin_id
     *
     * @param Integer $ruangan_id ruangan
     * @param Integer $kelaspelayanan_id kelas pelayanan
     * @param Integer $penjamin_id penjamin
     * @method POST
     * @return JSON
     * @author Tsani Nashrullah <tsani@docotel.com>
     **/
    public function actionCekRuanganDefault()
    {
        $payload = Yii::$app->request->post();
        $email = ArrayHelper::getValue($payload, 'ruangan_id');
        $payloadValidation = [
            'ruangan_id' => ArrayHelper::getValue($payload, 'ruangan_id'),
            'kelaspelayanan_id' => ArrayHelper::getValue($payload, 'kelaspelayanan_id'),
            'penjamin_id' => ArrayHelper::getValue($payload, 'penjamin_id'),
            'kamarruanganId' => ArrayHelper::getValue($payload, 'kamarruanganId'),
            'kamartempattidurId' => ArrayHelper::getValue($payload, 'kamartempattidurId'),
        ];
        $validation = DynamicModel::validateData($payloadValidation, [
            ['ruangan_id', 'required', 'message' => 'Ruangan tidak boleh kosong.'],
            ['kelaspelayanan_id', 'required', 'message' => 'Kelas Pelayanan tidak boleh kosong.'],
            ['penjamin_id', 'required', 'message' => 'Penjamin tidak boleh kosong.'],
            ['kamarruanganId', 'required', 'message' => 'Kammar Ruangan tidak boleh kosong.'],
        ]);

        if ($validation->hasErrors()) {
            return $this->helper->macroResponseJson(422, $this->helper->mapMessageErrorValidation($validation->errors));
        } else {
            try {
                $response = $this->_restPendaftaran->post('allow/cek-akomodasi-ruangan-default', [
                    'form_params' => [
                        'kelaspelayanan_id' => $payloadValidation['kelaspelayanan_id'],
                        'ruangan_id' => $payloadValidation['ruangan_id'],
                        'penjamin_id' => $payloadValidation['penjamin_id'],
                        'kamarruanganId' => $payloadValidation['kamarruanganId'],
                        'kamartempattidurId' => ArrayHelper::getValue($payloadValidation, 'kamartempattidurId', ''),
                    ],
                ]);
                $contentGuzzle = json_decode($response->getBody())->response;
                return $this->helper->macroResponseJson($response->getStatusCode(), $contentGuzzle->message, isset($contentGuzzle->data) ? $contentGuzzle->data : []);
            } catch (RequestException $e) {
                $contentGuzzle = json_decode($e->getResponse()->getBody(true));
                if (isset($contentGuzzle->metadata) && $contentGuzzle->metadata->status < 500) {
                    return $this->helper->macroResponseJson($contentGuzzle->metadata->status, $contentGuzzle->response->message, []);
                } else {
                    return $this->helper->macroResponseJson(500, Yii::t('fe', 'error_500'), []);
                }
            } catch (Exception $e) {
                return $this->helper->macroResponseJson(500, Yii::t('fe', 'error_500'), []);
            }
        }
    }

    /**
     * Retrieve data dokter by ruangan
     *
     * @param Type $ruangan_id Ruangan
     * @return JSON
     **/
    public function actionGetDokterByRuangan()
    {
        $payload = Yii::$app->request->get();
        if (isset($payload['ruangan_id']) && !empty($payload['ruangan_id'])) {
            $response = $this->_restPendaftaran->get('allow/get-ruangan-dokter', [
                'query' => [
                    'id' => $payload['ruangan_id'],
                ],
            ]);
            $response = json_decode($response->getBody());
            return $this->helper->macroResponseJson(200, 'Berhasil mendapatkan data dokter berdasarkan ruangan', $response->response);
        } else {
            return $this->helper->macroResponseJson(400, 'Mohon isi ruangan');
        }
    }

    public function actionGetDataKamar()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $jenis_id = $request->get('jenis_id');
        $kelas_id = $request->get('kelas_id');
        $ruangan_id = $request->get('ruangan_id');
        $draw = $request->get('draw', 1);
        $data = [];
        try {
            $response = $this->_restPendaftaran->get('allow/list-bed-by-ruangan', ['form_params' => [],
                'query' => [
                    'jeniskasuspenyakit_id' => $jenis_id,
                    'kelaspelayanan_id' => $kelas_id,
                    'ruangan_id' => $ruangan_id === '-' ? '' : $ruangan_id,
                    'status_kamar' => 1,
                    'penjamin_id' => $request->get('penjamin_id'),
                    'gender' => $request->get('gender'),
                    'page' => $request->get('page'),
                    'kamarruangan_id' => $request->get('kamar_id'),
                ],
            ]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            $result['data'] = $this->listRuangan($body['response']['list-ruangan'], $body['response']['data']);
            $result['recordsTotal'] = '';
            $result['recordsFiltered'] = '';

            return $result;

        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDataKamarDefault()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $jenis_id = $request->get('jenis_id');
        $kelas_id = $request->get('kelas_id');
        $ruangan_id = $request->get('ruangan_id');
        $pasien_titipan = $request->get('pasien_titipan', null);
        $draw = $request->get('draw', 1);
        $data = [];
        try {
            Yii::error([
                'pindah-kamar-payload' => [
                    'jeniskasuspenyakit_id' => $jenis_id,
                    'kelaspelayanan_id' => $kelas_id,
                    'ruangan_id' => $ruangan_id === '-' ? '' : $ruangan_id,
                    'status_kamar' => 1,
                    'penjamin_id' => $request->get('penjamin_id'),
                    'gender' => $request->get('gender'),
                    'page' => $request->get('page'),
                    'kamarruangan_id' => $request->get('kamar_id'),
                    'klasifikasikamar_id' => $request->get('klasifikasikamar_id'),
                ]
            ]);
            $response = $this->_restPendaftaran->get('allow/list-bed-by-ruangan-default', ['form_params' => [],
                'query' => [
                    'jeniskasuspenyakit_id' => $jenis_id,
                    'kelaspelayanan_id' => $kelas_id,
                    'ruangan_id' => $ruangan_id === '-' ? '' : $ruangan_id,
                    'status_kamar' => 1,
                    'penjamin_id' => $request->get('penjamin_id'),
                    'gender' => $request->get('gender'),
                    'page' => $request->get('page'),
                    'kamarruangan_id' => $request->get('kamar_id'),
                    'klasifikasikamar_id' => $request->get('klasifikasikamar_id'),
                    'is_pasien_titipan' => $pasien_titipan,
                ],
            ]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);

            if ($pasien_titipan) {
                $result['data'] = $this->listRuanganTitipan($body['response']['list-ruangan'], $body['response']['data'], $pasien_titipan);
            } else {
                $result['data'] = $this->listRuangan($body['response']['list-ruangan'], $body['response']['data'], $pasien_titipan);
            }

            $result['recordsTotal'] = '';
            $result['recordsFiltered'] = '';

            return $result;

        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function listRuangan($index, $data, $pasien_titipan = null)
    {
        function listKamar($id, $no_kamar, $kelaspelayanan_id, $data, $pasien_titipan = null)
        {
            $buttonList = [];
            $i = 1;
            foreach ($data as $key => $value) {
                $kamarruangan_jenis = null;
                if(isset($value['kamarruangan_jenis_id'])) {
                    $kamarruangan_jenis = $value['kamarruangan_jenis_id'];
                }
                elseif(isset($value['kamarruangan_jenis'])) {
                    $kamarruangan_jenis = $value['kamarruangan_jenis'];
                }

                if ($value['ruangan_id'] == $id && $value['kamarruangan_nokamar'] == $no_kamar && $value['kelaspelayanan_id'] == $kelaspelayanan_id) {
                    $attributes = [
                        'style' => "margin-bottom:8px;background-color:{$value['kode_warna']}",
                        'data-kettempattidur_id' => $value['kettempattidur_id'],
                        'data-kamartempattidur_id' => $value['kamartempattidur_id'],
                        'data-kamarruangan_jenis' => $kamarruangan_jenis,
                        'data-kamarruangan_id' => $value['kamarruangan_id'],
                        'data-ruangan_nama' => $value['ruangan_nama'],
                        'data-ruangan_id' => $value['ruangan_id'],
                        'data-no_tempattidur' => $value['no_tempattidur'],
                        'data-kelaspelayanan_id' => $value['kelaspelayanan_id'],
                        'data-kelaspelayanan_nama' => $value['kelaspelayanan_nama'],
                        'data-kamarruangan_nokamar' => $value['kamarruangan_nokamar'],
                        'data-jeniskasuspenyakit_nama' => $value['jeniskasuspenyakit_nama'],
                        'data-klasifikasikamar_id' => isset($value['klasifikasikamar_id']) ?: null,
                        'data-klasifikasikamar_nama' => isset($value['klasifikasikamar_nama']) ?: null,
                        'data-status_isi' => isset($value['status_isi']) ?: null,
                        'class' => 'pilih-kamar btn btn-danger-custom btn-xs',
                        'onClick' => $pasien_titipan ? 'pilihKamarTitipan(this)' : 'pilihKamar(this)',
                    ];
                    if (($value['kettempattidur_id'] == DocoConstants::ISI_PRMPN) or ($value['kettempattidur_id'] == DocoConstants::ISI_LAKI) or ($value['kettempattidur_id'] == DocoConstants::DIPESAN)) {
                        $attributes['disabled'] = 'disabled';
                    }
                    if ($kamarruangan_jenis && $kamarruangan_jenis == DocoConstants::JENIS_KAMAR_FLEKSIBEL) {
                        $attributes['data-allow_jk'] = $value['isi_jk'];
                    }
                    // $buttonLabel = $value['kamarruangan_nokamar'] . ' - ' . $value['no_tempattidur'];
                    $buttonLabel = $value['no_tempattidur'];
                    $buttonList[$value['no_tempattidur'] . $value['kamartempattidur_id']] = Html::buttonInput($buttonLabel, $attributes);
                }
                $i++;
            }
            if (count($buttonList) == 0) {
                $buttonList[] = "<p>--Tempat Tidur Tidak Tersedia--</p>";
            } else {
                ksort($buttonList);
            }

            return implode(' ', $buttonList);
        }

        $data_kamar = [];
        $tempKamar = [];
        $no = 1;
        foreach ($index as $key => $value) {
            if (!in_array($value['kamarruangan_id'], $tempKamar) && !in_array($value['kelaspelayanan_id'], $tempKamar)) {
                $tempKamar[]['kamarraungan_id'] = $value['kamarruangan_id'];
                $tempKamar[]['kelaspelayanan_id'] = $value['kelaspelayanan_id'];
                $value['rowNum'] = $no;
                $value['datakamar'] = listKamar($value['ruangan_id'], $value['kamarruangan_nokamar'], $value['kelaspelayanan_id'], $data, $pasien_titipan);
                $data_kamar[$no-1] = $value;
                $no++;
            }
        }
        return $data_kamar;
    }

    public function listRuanganTitipan($index, $data, $pasien_titipan = null)
    {
        function generateTombol($id, $no_kamar, $data, $pasien_titipan = null, $kelaspelayanan_id)
        {
            $tempKamar = [];
            $buttonList = [];
            $i = 1;
            foreach ($data as $key => $value) {
                $kamarruangan_jenis = null;

                if (isset($value['kamarruangan_jenis_id'])) {
                    $kamarruangan_jenis = $value['kamarruangan_jenis_id'];
                }
                else if (isset($value['kamarruangan_jenis'])) {
                    $kamarruangan_jenis = $value['kamarruangan_jenis'];
                }

                if ($value['ruangan_id'] == $id && $value['kamarruangan_nokamar'] == $no_kamar && $value['kelaspelayanan_id'] == $kelaspelayanan_id) {

                    $attributes = [
                        'style' => "margin-bottom:8px;background-color:{$value['kode_warna']}",
                        'data-kettempattidur_id' => $value['kettempattidur_id'],
                        'data-kamartempattidur_id' => $value['kamartempattidur_id'],
                        'data-kamarruangan_jenis' => $kamarruangan_jenis,
                        'data-kamarruangan_id' => $value['kamarruangan_id'],
                        'data-ruangan_nama' => $value['ruangan_nama'],
                        'data-ruangan_id' => $value['ruangan_id'],
                        'data-no_tempattidur' => $value['no_tempattidur'],
                        'data-kelaspelayanan_id' => $value['kelaspelayanan_id'],
                        'data-kelaspelayanan_nama' => $value['kelaspelayanan_nama'],
                        'data-kamarruangan_nokamar' => $value['kamarruangan_nokamar'],
                        'data-jeniskasuspenyakit_nama' => $value['jeniskasuspenyakit_nama'],
                        'data-klasifikasikamar_id' => $value['klasifikasikamar_id'],
                        'data-klasifikasikamar_nama' => $value['klasifikasikamar_nama'],
                        'data-status_isi' => $value['status_isi'],
                        'class' => 'pilih-kamar btn btn-danger-custom btn-xs',
                        'onClick' => $pasien_titipan ? 'pilihKamarTitipan(this)' : 'pilihKamar(this)',
                    ];

                    if ($kamarruangan_jenis && $kamarruangan_jenis == DocoConstants::JENIS_KAMAR_FLEKSIBEL) {
                        $attributes['data-allow_jk'] = null;
                    }

                    // $buttonLabel = Yii::t('fe', 'Pilih');
                    $buttonLabel = $value['no_tempattidur'];
                    $buttonList[$value['no_tempattidur'] . $value['kamartempattidur_id']] = Html::buttonInput($buttonLabel, $attributes);
                }
                
                $i++;
            }

            if (count($buttonList) == 0) {
                $buttonList[] = "<p>--Tempat Tidur Tidak Tersedia--</p>";
            } else {
                ksort($buttonList);
            }

            return implode(' ', $buttonList);
        }

        $data_kamar = [];
        $tempKamar = [];
        $no = 1;
        foreach ($index as $key => $value) {
            if (!in_array($value['kamarruangan_id'], $tempKamar) && !in_array($value['kelaspelayanan_id'], $tempKamar)) {
                $tempKamar[]['kamarraungan_id'] = $value['kamarruangan_id'];
                $tempKamar[]['kelaspelayanan_id'] = $value['kelaspelayanan_id'];
                $value['rowNum'] = $no;
                $value['datakamar'] = generateTombol($value['ruangan_id'], $value['kamarruangan_nokamar'], $data, $pasien_titipan, $value['kelaspelayanan_id']);
                $value['harga_tariftindakan'] = DocoHelpers::rupiahDisplay($value['harga_tariftindakan']);
                $data_kamar[$no-1] = $value;
                $no++;
            }
        }
        
        return $data_kamar;
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

            $request = $this->_restRanap->get('allow/get-new-diagnosa',['query'=>['q'=>$q,'type'=>$type,'page'=>$page,'offset'=>$offset,'limit'=>$limit,'is_perawat'=>$is_perawat]]);
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

    /**
     * @author rizal
     * @since
     * @param
     * @return
     * @desc DUPLICATE FROM PENDAFTARAN
     */
    public function actionListPenjamin()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $carabayar_id = $post['depdrop_parents'][0];

        $penjaminRequest = $this->_restRanap->get('allow/list-penjamin?carabayar_id=' . $carabayar_id);
        $body = json_decode($penjaminRequest->getBody(), true);
        $responses = $body['response'];

        $out = [];
        foreach ($responses as $key => $response) {
            $out[] = [
                'id' => $key,
                'name' => $response,
            ];
        }

        echo json_encode(['output' => $out, 'selected' => '']);
        return;
    }

    /**
     * Function for handle get pegawai data
     * 
     * @return JSON
     * @author : Rizqi Fitrianto (rizqi@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */

     public function actionGetPegawaiData($idKelompok = null)
     {
         $options = [
            'method' => 'get',
            'url' => 'allow/list-pegawai',
            'returnResponse' => true
         ];
         if ($idKelompok) {
            $options['payload'] = [
                'query' => [
                    'kelompokpegawai_id' => json_decode($idKelompok)
                ]
            ];
         }
         return $this->helper->guzzleExec($this->_restRanap, $options);
     }

     public function actionGetKamarTempatTidur()
     {
         return $this->helper->guzzleExec($this->_restRanap, [
            'method' => 'get',
            'url' => 'allow/get-kamar-tempat-tidur',
            'payload' => [
                'query' =>  Yii::$app->request->get('payload', [])
            ],
            'returnResponse' => true
         ]);
     }

     public function actionDoctorList()
    {
        $request = $this->_restRanap->get('allow/doctor-list', [
            'query' => Yii::$app->request->get('payload', [])
        ]);
        $body = json_decode($request->getBody(),TRUE);
        return $this->responseJson(200, 'Data Berhasil didapat!', $body['response']);
    }

    public function actionJenisKonsulInfinity()
    {
        $request = $this->_restRanap->get('allow/jenis-konsul-infinity', [
            'query' => Yii::$app->request->get('payload', [])
        ]);
        $body = json_decode($request->getBody(),TRUE);
        return $this->responseJson(200, 'Data Berhasil didapat!', $body['response']);
    }

    public function actionDokterKonsulInfinity()
    {
        $query = Yii::$app->request->get('payload', []);
        $request = $this->_restRanap->get('allow/dokter-konsul-infinity', ['form_params' => [],
            'query' => [
                'pendaftaran_id' => 1,
                'term'           => isset($query['term']) ? $query['term'] : null,
                'page'           => isset($query['page']) ? $query['page'] : 1,
                'limit'          => isset($query['limit']) ? $query['limit'] : 10,
            ],
        ]);
        $body = json_decode($request->getBody(),TRUE);
        return $this->responseJson(200, 'Data Berhasil didapat!', $body['response']);
    }

    public function actionGetDefaultKamarPasienTitipan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $jenis_id = $request->get('jenis_id');
        $kelas_id = $request->get('kelas_id');
        $ruangan_id = $request->get('ruangan_id');
        $penjamin_id = $request->get('penjamin_id');
        $gender = $request->get('gender');
        $kamarruangan_id = $request->get('kamar_id');
        $klasifikasikamar_id = $request->get('klasifikasikamar_id');
        $is_pasien_titipan = $request->get('pasien_titipan', null);
        try {
            $response = $this->_restPendaftaran->get('allow/get-default-bed-by-ruangan', ['form_params' => [],
                'query' => [
                    // 'jeniskasuspenyakit_id' => $jenis_id,
                    'kelaspelayanan_id' => $kelas_id,
                    'ruangan_id' => $ruangan_id === '-' ? '' : $ruangan_id,
                    'status_kamar' => 0,
                    'penjamin_id' => $penjamin_id,
                    'gender' => $gender,
                    // 'kamarruangan_id' => $kamarruangan_id,
                    'klasifikasikamar_id' => $klasifikasikamar_id,
                    'is_pasien_titipan' => $is_pasien_titipan,
                    'all_data' => false,
                ],
            ]);
            $body = json_decode($response->getBody(), true);
            $data = $body['response']['data'];
            $data['harga_tariftindakan'] = DocoHelpers::rupiahDisplay($data['harga_tariftindakan']);
            return (object)$data;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetListPemberiInstruksi()
    {
        $request = Yii::$app->request;
        $ruangan_id = $request->get('id_ruangan');
        try {
            $response = $this->_restRanap->get('cppt/get-list-pemberi-instruksi',[
                'query' => [
                    'term' => $request->get('term'),
                    'ruangan_id' => $ruangan_id,
                    'instalasi_id' => DocoConstants::INSTALASI_ID_RI,
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $data = isset($response['response']['data']) ? $response['response']['data'] : [];
    
            $results = [];
            foreach($data as $value) {
                $results[] = [
                    'id' => isset($value['pegawai_id']) ? $value['pegawai_id'] : null,
                    'text' => isset($value['nama_pegawai']) ? $value['nama_pegawai'] : null,
                ];
            }
            return DocoHelpers::response([
                'data' => $results
            ]);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionListDataTindakan()
    {
        $title = 'Laporan Terapi';
        $type = Yii::$app->request->get('type', DocoConstants::TYPE_RJ);
        $pendaftaran_id = Yii::$app->request->get('id');
        $pasienadmisi_id = Yii::$app->request->get('pasienadmisi_id');
        $pasien_id = Yii::$app->request->get('pasien_id', null); // kegunaannya untuk ri dan rd
        switch ($type) {
            case DocoConstants::TYPE_RI:
            case 'ranapLaporanTerapi':
                $url = [
                    'datatable' => Url::to(
                        ['/ranap/end-point/get-list-tindakan', 'id' => $pendaftaran_id, 'pasien_id'=> $pasien_id, 'pasienadmisi_id' => $pasienadmisi_id,  'type' => $type,]
                    )
                ];
                break;
        }
        return $this->renderAjax('//cppt/laporan_tindakan', compact('title', 'url'));
    }

    public function actionGetListTindakan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $type = Yii::$app->request->get('type', DocoConstants::TYPE_RI);
        $payload = DocoDatatableHelper::advancedFilterParam();
        $pasien_id = '';
        $urlDelete = '';
        $instalasiPenunjangArr = [DocoConstants::INSTALASI_ID_RAD, DocoConstants::INSTALASI_ID_LAB, DocoConstants::INSTALASI_ID_BEDAH];
        switch (strtolower($type)) {
            case DocoConstants::TYPE_RI:
            case 'ranaplaporanterapi':
                $url = 'pemeriksaan-rawat-inap/fetch-list-tindakan';
                $urlDelete = '/ranap/pemeriksaan-rawat-inap/batal-instruksi-form';
                $pasien_id = Yii::$app->request->get('pasien_id', null) ? $this->helper->decrypt(Yii::$app->request->get('pasien_id', null)) : null;
                break;
            case DocoConstants::TYPE_RI:
                    $url = 'pemeriksaan-rawat-inap/fetch-list-tindakan';
                    $urlDelete = '/ranap/pemeriksaan-rawat-inap/batal-instruksi-form';
                break;
        }
        $queryParams = array_merge($payload, [
            'pendaftaran_id' => $this->helper->decrypt(Yii::$app->request->get('id')),
            'pasien_id' => $pasien_id,
            'pasienadmisi_id' => Yii::$app->request->get('pasienadmisi_id', null),
            'start' => Yii::$app->request->get('start', 0),
            'length' => Yii::$app->request->get('length', 10),
            'type' => $type,
            'instruksi' => Yii::$app->request->get('instruksi'),
            'jenis' => Yii::$app->request->get('jenis'),
            'startDate' => Yii::$app->request->get('startDate'),
            'endDate' => Yii::$app->request->get('endDate'),
        ]);
        $getData = $this->helper->guzzleExec($this->_restDefault, [
            'url' => $url,
            'payload' => [
                'query' => $queryParams
            ]
        ]);
        $statusImplementasiFarmasi = isset($getData['status_implementasi_farmasi']) ? $getData['status_implementasi_farmasi'] : DocoConstants::STATUS_RESEPTUR_BELUM_DIPROSES;
        foreach ($getData['data'] as $key => $value) {
            $getData['data'][$key]['instruksi'] = str_replace('Cyto', 'CITO', $value['instruksi']);
            $buttonAction = [];
            $is_resep_lunas = strtolower($type) == DocoConstants::RJ_LAP_TERAPI ? $value['status_bayar'] != DocoConstants::STAT_BAYAR_LUNAS ? false : true : $value['is_bayar'];

            /**
             * Penyesuaian show alasan, tanggal & pegawai ketika order bedah ditolak
             * 541 = Ditolak
             */

            if (!$value['deleted'] && (int) $value['status_implementasi'] != 541) {
                if (!$value['is_bayar']) {
                    if (strtolower($value['grouping_tipe']) == 'tindakanbmhp' && !$value['is_pulang']
                         && $value['status_bmhp_id'] == DocoConstants::BMHP_BELUM_VERIFIKASI) {
                        $buttonAction = [
                            'title' => '<i class="fa fa-trash"></i>',
                            'attr' => [
                                'class' => 'btn btn-danger btn-sm btn-delete-instruksi',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal-batal-instruksi',
                                'href' => Url::to([
                                    $urlDelete,
                                    'id' => $this->helper->encrypt($value['pendaftaran_id']),
                                    'pasienadmisi_id' => Yii::$app->request->get('pasienadmisi_id', null),
                                    'instruksitindakan_id' => $value['instruksitindakan_id'],
                                    'jenis' => strtolower($value['jenis']),
                                    'type' => $type,
                                    'group' => strtolower($value['grouping_tipe'])
                                ]),
                                'data-instruksitindakan_id' => $value['instruksitindakan_id']
                            ]
                        ];
                    } else if (Yii::$app->docoVars->workspace('instalasi_id') == DocoConstants::INSTALASI_ID_RI && strtolower($value['grouping_tipe']) == 'tindakanbmhp' && !$value['is_pulang'] && empty($value['status_bmhp_id'])) {
                        $buttonAction = [
                            'title' => '<i class="fa fa-trash"></i>',
                            'attr' => [
                                'class' => 'btn btn-danger btn-sm btn-delete-instruksi',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal-batal-instruksi',
                                'href' => Url::to([
                                    $urlDelete,
                                    'id' => $this->helper->encrypt($value['pendaftaran_id']),
                                    'instruksitindakan_id' => $value['instruksitindakan_id'],
                                    'instruksi_id' => $value['instruksi_id'],
                                    'jenis' => strtolower($value['jenis']),
                                    'type' => $type,
                                    'group' => strtolower($value['grouping_tipe']),
                                    'instalasi_id' => $value['instalasi_penunjang_id']
                                ])
                            ]
                        ];
                    }

                    // Batal Penunjang
                    // aksi hanya dimunculkan jika tindakan terkait belum dibayar dan status tindakan tersebut tidak termasuk dalam list status yg dijadikan kondisi
                    /**
                     * Reseptur
                     * 347 = Sudah Diproses
                     *
                     * Implementasi
                     * 455 = Sudah Implementasi
                     *
                     * Penunjang
                     * 477 = Sudah disetujui/belum periksa penunjang
                     * 471 = Sudah disetujui Bedah
                     * 472 = Batal
                     * 473 = Periksa
                     * 475 = Selesai
                     * 476 = Batal
                     * 482 = Sedang operasi
                     * 483 = Sudah operasi
                     * 488 = Belum operasi
                     * 692 = Reschedule
                     */

                    if (in_array($value['instalasi_penunjang_id'], $instalasiPenunjangArr) && !in_array((int) $value['status_implementasi'], [347, 455, 477, 471, 472, 473, 475, 476, 482, 483, 488, 692]) ||  in_array($value['instalasi_penunjang_id'], $instalasiPenunjangArr) && $value['is_telah_implementasi']) {
                        // pengecekan kondisi untuk menyesuaikan parameter yg dikirim disesuaikan dengan instalasi di pendaftaran

                        if ((Yii::$app->docoVars->workspace('instalasi_id') ==  DocoConstants::INSTALASI_ID_RI && !$value['is_pulang'])) {
                            if (count(array_intersect([ArrayHelper::getValue($value, 'instalasi_penunjang_id')], DocoConstants::INSTALASI_ID_PENUNJANG)) && !ArrayHelper::getValue($value, 'is_telah_implementasi', false) && ArrayHelper::getValue($value, 'status_implementasi') != DocoConstants::LAB_ST_PEN_BELUMPERIKSA) {
                                // Baru untuk bedah
                                $buttonAction = [
                                    'title' => '<i class="fa fa-trash"></i>',
                                    'attr' => [
                                        'class' => 'btn btn-danger btn-sm btn-delete-instruksi',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-batal-instruksi',
                                        'href' => Url::to([
                                            $urlDelete,
                                            'id' => $this->helper->encrypt($value['pendaftaran_id']),
                                            'instruksitindakan_id' => $value['instruksitindakan_id'],
                                            'jenis' => strtolower($value['jenis']),
                                            'type' => $type,
                                            'group' => strtolower($value['grouping_tipe']),
                                            'instalasi_id' => $value['instalasi_penunjang_id']
                                        ]),
                                    ]
                                ];
                            }
                        }
                    }

                    if (strtolower($value['grouping_tipe'])  == 'reseptur') {
                        if($value['status_implementasi'] != DocoConstants::STATUS_RESEPTUR_DISERAHKAN && $value['status_implementasi'] != DocoConstants::STATUS_RESEPTUR_BATAL &&   !$value['is_pulang'] && !$is_resep_lunas) {
                            $buttonAction = [
                                'title' => '<i class="fa fa-trash"></i>',
                                'attr' => [
                                    'class' => 'btn btn-danger btn-sm btn-delete-instruksi',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-batal-instruksi',
                                    'href' => Url::to([
                                        $urlDelete,
                                        'id' => $this->helper->encrypt($value['pendaftaran_id']),
                                        'noresep' => $value['noresep'],
                                        'type' => $type,
                                        'group' => strtolower($value['grouping_tipe']),
                                        'instalasi_id' => $value['instalasi_penunjang_id']
                                    ]),
                                ]
                            ];
                        } else if ($value['status_implementasi'] == DocoConstants::STATUS_RESEPTUR_BATAL) {
                            if (strtolower($type) == DocoConstants::TYPE_RI || strtolower($type) == DocoConstants::RI_LAP_TERAPI) {
                                $buttonAction = [
                                    'title' => '<i class="fa fa-eye"></i>',
                                    'attr' => [
                                        'class' => 'btn btn-info btn-sm btn-view-instruksi',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-batal-instruksi',
                                        'data-width' => '50%',
                                        'href' => Url::to([
                                            '/ranap/pemeriksaan-rawat-inap/view-pembatalan-instruksi',
                                            'id' => $this->helper->encrypt($value['pendaftaran_id']),
                                            'noresep' => $value['noresep'],
                                            'group' => $value['grouping_tipe'],
                                        ]),
                                    ]
                                ];
                            }
                        }
                    }
                }
            } else {
                if ((strtolower($type) == DocoConstants::TYPE_RI || strtolower($type) == DocoConstants::RI_LAP_TERAPI) && in_array(strtolower($value['grouping_tipe']), ['tindakanbmhp', 'penunjang', 'reseptur'])) {
                    $buttonAction = [
                        'title' => '<i class="fa fa-eye"></i>',
                        'attr' => [
                            'class' => 'btn btn-info btn-sm btn-view-instruksi',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal-batal-instruksi',
                            'data-width' => '50%',
                            'href' => Url::to([
                                '/ranap/pemeriksaan-rawat-inap/view-pembatalan-instruksi',
                                'id' => $this->helper->encrypt($value['pendaftaran_id']),
                                'instruksitindakan_id' => $value['instruksitindakan_id'],
                                'group' => $value['grouping_tipe'],
                                'noresep' => $value['noresep']
                            ]),
                        ]
                    ];
                }
            }
            $getData['data'][$key]['aksi'] = !empty($buttonAction) ? Html::button($buttonAction['title'], $buttonAction['attr']) : '';
        }
        return [
            'draw' => Yii::$app->request->get('draw'),
            'data' => $getData['data'],
            'load_more' => $getData['load_more']
        ];
    }
}
