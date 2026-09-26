<?php
// Author : Ramdhan Nurrachman

namespace Doco\kasir\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\models\LoginForm;
use GuzzleHttp\Exception\RequestException;

class EndPointController extends DocoController
{
    protected $_title = "End Point";
    protected $_module = 'master/end-point/';
    protected $_restKasir;

    public function beforeAction($action)
    {
        return true;
    }

    public function init()
    {
        parent::init();
        $this->_restKasir = Yii::$app->docoRest->kasir;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actionGetInstalasi($assign_id="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restKasir->get('instalasi?advanced-filter[ruangan_m.ruangan_nama]='.$parent_label);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value)
                $result['output'][] = [
                    'id' => $assign_id? $value['instalasi_id'] : $value['instalasi_nama'],
                    'name' => $value['instalasi_nama']
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

    public function actionGetRuangan($assign_id="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $params = '';
        if ($request->post()) {
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
            $params = '?advanced-filter[instalasi_m.instalasi_nama]='.$parent_label;
        }

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restKasir->get('ruangan'. $params);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value)
                $result['output'][] = [
                    'id' => $assign_id? $value['ruangan_id'] : $value['ruangan_nama'],
                    'name' => $value['ruangan_nama']
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

    public function actionGetCarabayar($assign_id="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $params = '';
        if ($request->post()) {
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
            $params = '?advanced-filter[penjamin_m.penjamin_nama]='.$parent_label;
        }

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restKasir->get('cara-bayar' . $params);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value)
                $result['output'][] = [
                    'id' => $assign_id? $value['carabayar_id'] : $value['carabayar_nama'],
                    'name' => $value['carabayar_nama']
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

    public function actionGetPenjamin($assign_id="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restKasir->get('penjamin?advanced-filter[carabayar_m.carabayar_nama]='.$parent_label);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value)
                $result['output'][] = [
                    'id' => $assign_id? $value['penjamin_id'] : $value['penjamin_nama'],
                    'name' => $value['penjamin_nama']
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

    public function actionGetDataPendaftaran($assign_id="", $q="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $tanggal = $request->get('tanggal');
        $result = [];
        $result['results'] = [];
        $result['incomplete_results'] = true;
        $result['total_count'] = 0;

        try {
            $response = $this->_restKasir->get('pendaftaran?advanced-filter[no_pendaftaran]='.$q,[
                'query' => [
                    'advanced-filter' => [
                        'tanggal' => $tanggal
                    ]
                ]
            ]);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value) {
                $result['results'][] = [
                    'id' => $value['no_pendaftaran'],
                    'text' => $value['no_pendaftaran']
                ];
                $result['total_count']++;
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

    public function actionGetDataNoPen($assign_id="", $q="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $tanggal = $request->get('tanggal');
        $result = [];
        $result['results'] = [];
        $result['incomplete_results'] = true;
        $result['total_count'] = 0;

        try {
            $response = $this->_restKasir->get('allow/get-data-resep?q='.$q,[
                'query' => [
                    'advanced-filter' => [
                        'tanggal' => $tanggal
                    ]
                ]
            ]);
            $temp_group_pendaftaran = array(); // digunakan untuk melakukan grouping no pendataran agar tidak muncul berkali-kali

            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value) {
                if (stripos($value['pendaftaran']['no_pendaftaran'],$q) !== false) {
                    if (!array_key_exists($value['pendaftaran']['pendaftaran_id'], $temp_group_pendaftaran)) {
                        $result['results'][] = [
                            'id' => $value['pendaftaran']['no_pendaftaran'],
                            'text' => $value['pendaftaran']['no_pendaftaran']
                        ];
                        $result['total_count']++;
                    }
                    $temp_group_pendaftaran[$value['pendaftaran']['pendaftaran_id']] = $value['pendaftaran']['no_pendaftaran'];
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

    public function actionGetDataResep($assign_id="", $q="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $tanggal = $request->get('tanggal');
        $result = [];
        $result['results'] = [];
        $result['incomplete_results'] = true;
        $result['total_count'] = 0;

        try {
            $response = $this->_restKasir->get('allow/get-data-resep',[
                'query' => [
                    'advanced-filter' => [
                        'tanggal' => $tanggal,
                    ],
                    'q' => $q
                ]
            ]);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value) {
                $result['results'][] = [
                    'id' => $value['noresep'],
                    'text' => $value['noresep']
                ];
                $result['total_count']++;
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

    public function actionGetDataBarang($assign_id="", $q="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];

        try {
            $response = $this->_restKasir->get('barang?advanced-filter[barang_nama]='.$q);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value)
                $result['results'][] = [
                    'id' => $assign_id? $value['barang_id'] : $value['barang_nama'],
                    'text' => $value['barang_nama']
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

    public function actionGetDataFormulirStokOpname($assign_id="", $q="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];

        try {
            $response = $this->_restKasir->get('inf-formulir-stok-opname?advanced-filter[noformulir]='.$q);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value)
                $result['results'][] = [
                    'id' => $assign_id? $value['formulirstokopname_id'] : $value['noformulir'],
                    'text' => $value['noformulir']
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

    public function actionGetDataObatAlkes($assign_id="", $q="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];

        try {
            $response = $this->_restKasir->get('inf-stok-obat-alkes?advanced-filter[obatalkes_namalain]='.$q);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value)
                $result['results'][] = [
                    'id' => $assign_id? $value['obatalkes_id'] : $value['obatalkes_namalain'],
                    'text' => $value['obatalkes_namalain']
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

    public function actionGetDataShift($assign_id="", $q="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];

        try {
            $response = $this->_restKasir->get('shift?advanced-filter[shift_nama]='.$q);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value)
                $result['results'][] = [
                    'id' => $assign_id? $value['shift_id'] : $value['shift_nama'],
                    'text' => $value['shift_nama']
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

    public function actionGetDataPegawai($assign_id="", $q="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];

        try {
            $response = $this->_restKasir->get('pegawai?advanced-filter[nama_pegawai]='.$q);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value)
                $result['results'][] = [
                    'id' => $assign_id? $value['pegawai_id'] : $value['nama_pegawai'],
                    'text' => $value['nama_pegawai']
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

    public function actionGetDataDokter($assign_id="", $q="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];

        try {
            $response = $this->_restKasir->get('dokter?advanced-filter[nama_pegawai]='.$q);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value)
                $result['results'][] = [
                    'id' => $assign_id? $value['pegawai_id'] : $value['nama_pegawai'],
                    'text' => $value['nama_pegawai']
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

    public function actionGetDataPesanBarang($assign_id="", $q="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];

        try {
            $response = $this->_restKasir->get('pesan-barang?advanced-filter[no_pemesanan]='.$q);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value)
                $result['results'][] = [
                    'id' => $assign_id ? $value['pesanbarang_id'] : $value['no_pemesanan'],
                    'text' => $value['no_pemesanan']
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

    public function actionGetDataMutasiBarang($assign_id="", $q="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];

        try {
            $response = $this->_restKasir->get('mutasi-barang?advanced-filter[nomutasi_barang]='.$q);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value)
                $result['results'][] = [
                    'id' => $assign_id? $value['mutasibarang_id'] : $value['nomutasi_barang'],
                    'text' => $value['nomutasi_barang']
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

    public function actionGetDataMutasiObatRuangan($assign_id="", $q="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];

        try {
            $response = $this->_restKasir->get('mutasi-barang?advanced-filter[nomutasioa]='.$q);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value)
                $result['results'][] = [
                    'id' => $assign_id? $value['mutasiobatruangan_id'] : $value['nomutasioa'],
                    'text' => $value['nomutasioa']
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

    public function actionGetDataDiagnosa($assign_id="", $q="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];

        try {
            $response = $this->_restKasir->get('diagnosa?advanced-filter[diagnosa_nama]='.$q);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value)
                $result['results'][] = [
                    'id' => $assign_id? $value['diagnosa_id'] : $value['diagnosa_nama'],
                    'text' => $value["diagnosa_kode"]." - ".$value['diagnosa_nama']
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

    public function actionCheckAuthorization()
    {
        $urlRef = Yii::$app->request->referrer;
        $pattern = preg_replace("/(http[s]?:\/\/)?([^\/\s]+)(.*)/",'$3',$urlRef);
        $pattern2 = preg_replace("/(\/(?:.(?!\/))+$)/",'',$pattern);

        $request = Yii::$app->request;
        $akses = $request->post('akses');
        $model = new LoginForm;
        $model->username = $request->post('nama_pemakai');
        $model->password = $request->post('katakunci_pemakai');
        if ($response = $model->getAuthorization()) {
            $menus = isset($response['response']['menus']) ? $response['response']['menus'] : [];
            $uid = isset($response['response']['uid']) ? $response['response']['uid'] : null;
            foreach ($menus as $key => $value) {
                if (!empty($value['akses'][$pattern2])) {
                    if (in_array($akses, $value['akses'][$pattern2])) {
                        return DocoHelpers::response([
                            'response' => [
                                'message' => true,
                                'verify_uid' => $uid
                            ]]);
                    }
                }else if(!empty($value['akses'][$pattern])) {
                    if (in_array($akses, $value['akses'][$pattern])) {
                        return DocoHelpers::response([
                            'response' => [
                                'message' => true,
                                'verify_uid' => $uid
                            ]]);
                    }
                }
            }
            return DocoHelpers::response([
                'response' => [
                    'text' => 'User tidak memiliki hak akses.'
                ]
            ], 422);
        } else {
            return DocoHelpers::response([
                'response' => [
                    'text' => 'user/password tidak valid.'
                ]
            ], 422);
        }
    }
}
