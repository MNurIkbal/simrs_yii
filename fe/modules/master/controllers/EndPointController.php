<?php
// Author : Ramdhan Nurrachman

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class EndPointController extends DocoController
{
    protected $_title = "End Point";
    protected $_module = 'master/end-point/';
    protected $_restMaster;
    
    public function beforeAction($action)
    {
        return true;
    }

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actionGetRuangan($assign_id="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restMaster->get('ruangan?advanced-filter[instalasi_m.instalasi_nama]='.$parent_label);
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
            $response = $this->_restMaster->get('penjamin?advanced-filter[carabayar_m.carabayar_nama]='.$parent_label);
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

        $result = [];
        $result['results'] = [];

        try {
            $response = $this->_restMaster->get('pendaftaran?advanced-filter[no_pendaftaran]='.$q);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value) 
                $result['results'][] = [
                    'id' => $assign_id? $value['pendaftaran_id'] : $value['no_pendaftaran'], 
                    'text' => $value['no_pendaftaran']
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

    public function actionGetDataBarang($assign_id="", $q="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];

        try {
            $response = $this->_restMaster->get('barang?advanced-filter[barang_nama]='.$q);
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
            $response = $this->_restMaster->get('inf-formulir-stok-opname?advanced-filter[noformulir]='.$q);
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
            $response = $this->_restMaster->get('inf-stok-obat-alkes?advanced-filter[obatalkes_namalain]='.$q);
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
            $response = $this->_restMaster->get('shift?advanced-filter[shift_nama]='.$q);
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
            $response = $this->_restMaster->get('pegawai?advanced-filter[nama_pegawai]='.$q);
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
            $response = $this->_restMaster->get('dokter?advanced-filter[nama_pegawai]='.$q);
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
            $response = $this->_restMaster->get('pesan-barang?advanced-filter[no_pemesanan]='.$q);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value) 
                $result['results'][] = [
                    'id' => $assign_id? $value['pesanbarang_id'] : $value['no_pemesanan'], 
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
            $response = $this->_restMaster->get('mutasi-barang?advanced-filter[nomutasi_barang]='.$q);
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
            $response = $this->_restMaster->get('mutasi-barang?advanced-filter[nomutasioa]='.$q);
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
            $response = $this->_restMaster->get('diagnosa?advanced-filter[diagnosa_nama]='.$q);
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

    public function actionGetDataGolonganOperasi($assign_id="", $q="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $result = [];
        $result['results'] = [];

        try {
            $response = $this->_restMaster->get('golongan-operasi?advanced-filter[golonganoperasi_nama]='.$q);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value) 
                $result['results'][] = [
                    'id' => $assign_id? $value['golonganoperasi_id'] : $value['golonganoperasi_nama'], 
                    'text' => $value['golonganoperasi_nama']
                ];
            $total = count($body['response']);
            $return = ['result'=>$result['results'],'total_count'=>$total,'incomplete_results'=>false];
            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDataKegiatanOperasi($assign_id="", $q="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $result = [];
        $result['results'] = [];

        try {
            $response = $this->_restMaster->get('kegiatan-operasi?advanced-filter[kegiatanoperasi_nama]='.$q);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value) 
                $result['results'][] = [
                    'id' => $assign_id? $value['kegiatanoperasi_id'] : $value['kegiatanoperasi_nama'], 
                    'text' => $value['kegiatanoperasi_nama']
                ];
            $total = count($body['response']);
            $return = ['result'=>$result['results'],'total_count'=>$total,'incomplete_results'=>false];
            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDaftarTindakan($assign_id="", $kelompoktindakan_id="", $q="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $result = [];
        $result['results'] = [];
        $usingCode = $request->get('using_kode', false);
        Yii::error([$assign_id, $kelompoktindakan_id, $q, $usingCode]);

        try {
            $qParams = 'daftar-tindakan?advanced-filter[daftartindakan_nama]=' . $q;
            if ($usingCode) {
                $qParams = 'daftar-tindakan?filters=daftartindakan_nama,daftartindakan_kode&q=' . $q;
            }
            $response = $this->_restMaster->get($qParams);
            // if($kelompoktindakan_id) {
            //     $response = $this->_restMaster->get('daftar-tindakan?kelompoktindakan_id='.$kelompoktindakan_id.'&advanced-filter[daftartindakan_nama]='.$q);
            // }

            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value) 
                $result['results'][] = [
                    'id' => $assign_id? $value['daftartindakan_id'] : $value['daftartindakan_nama'], 
                    'text' => $value['daftartindakan_kode'].' - '.$value['daftartindakan_nama']
                ];
            $total = count($body['response']);
            $return = ['result'=>$result['results'],'total_count'=>$total,'incomplete_results'=>false];
            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetKodeOperasi($assign_id="", $q="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $result = [];
        $result['results'] = [];

        try {
            $response = $this->_restMaster->get('operasi/data?advanced-filter[operasi_kode]='.$q);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value) 
                $result['results'][] = [
                    'id' => $assign_id? $value['operasi_id'] : $value['operasi_kode'], 
                    'text' => $value['operasi_kode']
                ];
            $total = count($body['response']);
            $return = ['result'=>$result['results'],'total_count'=>$total,'incomplete_results'=>false];
            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }


    public function actionGetAllDiagnosa($q = null, $page = null,$is_valueWithText= 0, $id = null, $set_id_as_text=0) 
    {
        try{
            $limit = 10;
            $offset = ($page-1)*10;
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $out = ['results' => ['id'=>'','text'=>'']];
            $response = $this->_restMaster->get('allow/get-all-diagnosa',[
                'query' => ['keyword'=>$q,'page'=>$page,'offset'=>$offset,'limit'=>$limit]
            ]);
            $response = json_decode($response->getBody(), TRUE);
            $results = [];
            if ($response['metadata']['status'] == 200) {
                $list = $response['response'];
                foreach ($list as $key => $each) {
                    if ($set_id_as_text == 0) {
                        if ($is_valueWithText == 2) { // normal condition
                            $results[] = [
                                'id'=>$each['diagnosa_id'], 
                                'text'=>$each['nama_diagnosa'],
                            ];
                        } else {
                            $results[] = [
                                'id'=>$each['diagnosa_id'], 
                                'text'=>$each['nama_diagnosa'],
                            ];
                        }
                    } else {
                        $results[] = [
                            'id'=>$each['diagnosa_nama'],
                            'text'=>$each['diagnosa_nama'],
                        ];
                    }
                }
                $out['results'] = $results;
                $out['pagination'] = [ 'more' => !empty($list)?true:false ];
            }

            return $out;
        } catch (RequestException $e) {
            return ['results' => ['id'=>'','text'=>'']];
        } catch (\Exception $e) {
            return ['results' => ['id'=>'','text'=>'']];
        }
    }

    public function actionGetAllJenisPenyakit($q = null, $page = null,$is_valueWithText= 0, $id = null, $set_id_as_text=0) 
    {
        try{
            $limit = 10;
            $offset = ($page-1)*10;
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $out = ['results' => ['id'=>'','text'=>'']];
            $response = $this->_restMaster->get('allow/get-all-jenis-penyakit',[
                'query' => ['keyword'=>$q,'page'=>$page,'offset'=>$offset,'limit'=>$limit]
            ]);
            $response = json_decode($response->getBody(), TRUE);
            $results = [];
            if ($response['metadata']['status'] == 200) {
                $list = $response['response'];
                foreach ($list as $key => $each) {
                    if ($set_id_as_text == 0) {
                        if ($is_valueWithText == 2) { // normal condition
                            $results[] = [
                                'id'=>$each['jeniskasuspenyakit_nama'], 
                                'text'=>$each['jeniskasuspenyakit_nama'],
                            ];
                        } else {
                            $results[] = [
                                'id'=>$each['jeniskasuspenyakit_nama'], 
                                'text'=>$each['jeniskasuspenyakit_nama'],
                            ];
                        }
                    } else {
                        $results[] = [
                            'id'=>$each['nama_jenis_penyakit'],
                            'text'=>$each['nama_jenis_penyakit'],
                        ];
                    }
                }
                $out['results'] = $results;
                $out['pagination'] = [ 'more' => !empty($list)?true:false ];
            }

            return $out;
        } catch (RequestException $e) {
            return ['results' => ['id'=>'','text'=>'']];
        } catch (\Exception $e) {
            return ['results' => ['id'=>'','text'=>'']];
        }
    }

    public function actionGetAllRuangan($q = null, $page = null,$is_valueWithText= 0, $id = null, $set_id_as_text=0) 
    {
        try{
            $limit = 10;
            $offset = ($page-1)*10;
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $out = ['results' => ['id'=>'','text'=>'']];
            $response = $this->_restMaster->get('allow/get-all-ruangan',[
                'query' => ['keyword'=>$q,'page'=>$page,'offset'=>$offset,'limit'=>$limit]
            ]);
            $response = json_decode($response->getBody(), TRUE);
            $results = [];
            if ($response['metadata']['status'] == 200) {
                $list = $response['response'];
                foreach ($list as $key => $each) {
                    if ($set_id_as_text == 0) {
                        if ($is_valueWithText == 2) { // normal condition
                            $results[] = [
                                'id'=>$each['ruangan_nama'], 
                                'text'=>$each['ruangan_nama'],
                            ];
                        } else {
                            $results[] = [
                                'id'=>$each['ruangan_nama'], 
                                'text'=>$each['ruangan_nama'],
                            ];
                        }
                    } else {
                        $results[] = [
                            'id'=>$each['nama_ruangan'],
                            'text'=>$each['nama_ruangan'],
                        ];
                    }
                }
                $out['results'] = $results;
                $out['pagination'] = [ 'more' => !empty($list)?true:false ];
            }

            return $out;
        } catch (RequestException $e) {
            return ['results' => ['id'=>'','text'=>'']];
        } catch (\Exception $e) {
            return ['results' => ['id'=>'','text'=>'']];
        }
    }

    public function actionGetJenisPenyakitAllRuangan($q = null, $page = null,$is_valueWithText= 0, $id = null, $set_id_as_text=0) 
    {
        try{
            $limit = 10;
            $offset = ($page-1)*10;
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $out = ['results' => ['id'=>'','text'=>'']];
            $response = $this->_restMaster->get('allow/get-all-ruangan',[
                'query' => ['keyword'=>$q,'page'=>$page,'offset'=>$offset,'limit'=>$limit]
            ]);
            $response = json_decode($response->getBody(), TRUE);
            $results = [];
            if ($response['metadata']['status'] == 200) {
                $list = $response['response'];
                foreach ($list as $key => $each) {
                    if ($set_id_as_text == 0) {
                        if ($is_valueWithText == 2) { // normal condition
                            $results[] = [
                                'id'=>$each['ruangan_id'], 
                                'text'=>$each['ruangan_nama'],
                            ];
                        } else {
                            $results[] = [
                                'id'=>$each['ruangan_id'], 
                                'text'=>$each['ruangan_nama'],
                            ];
                        }
                    } else {
                        $results[] = [
                            'id'=>$each['ruangan_id'],
                            'text'=>$each['ruangan_nama'],
                        ];
                    }
                }
                $out['results'] = $results;
                $out['pagination'] = [ 'more' => !empty($list)?true:false ];
            }

            return $out;
        } catch (RequestException $e) {
            return ['results' => ['id'=>'','text'=>'']];
        } catch (\Exception $e) {
            return ['results' => ['id'=>'','text'=>'']];
        }
    }

    /**
     * @todo Method untuk mendapatkan data semua makanan
     * @author Iqbal Qurahman <iqbal@docotel.com>
     */
    public function actionGetAllMakananDiet($q = '', $type = 'null', $all_text = 0, $id_with_text = 0, $is_valueWithText =0)
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $result = [];
            // $result['results'] = [];
            $result = ['results' => ['id'=>'','text'=>'']];

            $request = $this->_restMaster->get('allow/get-all-makanan-diet?q='.$q);
            $response = json_decode($request->getBody(), true);
            if ($response['metadata']['status'] == 200) {
                foreach ($response['response'] as $value) {
                    if ($id_with_text == 0) {
                        if ($is_valueWithText == 2) { 
                            $result['results'][] = [
                                'id'=>$value['makanandiet_id'], 
                                'text'=>$value['makanandiet_kode'].' -'.$value['makanandiet_nama'],
                            ];
                        } else {
                            $result['results'][] = [
                                'id'=>$value['makanandiet_id'], 
                                'text'=>$value['makanandiet_nama'],
                            ];
                        }
                    } else {
                        $result['results'][] = [
                            'id'=>$value['makanandiet_id'],
                            'text'=>$value['makanandiet_nama'],
                        ];
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

    public function actionGetDataMakananDiet()
    {
        $request = Yii::$app->request;
        $term = $request->get('term');
        try {
            $response = $this->_restMaster->get('allow/get-data-makanan-diet',[
                'query' => [
                    'term' => $term,
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $response = $response['response'];
            $data = [];
            foreach ($response as $value) {
                $data[] = [
                    'id' => $value['makanandiet_id'],
                    'text' => $value['makanandiet_nama'],
                ];
            }
        } catch (RequestException $e) {
            $data = [
                'messages' => $e->getMessage()
            ];
        }

        return DocoHelpers::response([
            'result' => $data
        ]);
    }
}