<?php
// author : rizal@docotel.com

namespace Doco\rajal\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

class EndPointController extends DocoController
{


    protected $_restRajal;
    protected $_restKasir;
    /**
     * @inheritdoc
     */

    public function init()
    {
        parent::init();
        $this->_restRajal = Yii::$app->docoRest->rajal;
        $this->_restKasir = Yii::$app->docoRest->kasir;
    }

    public function beforeAction($action)
    {
        return true;
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

            if(isset($yiiRestfulParams['advanced-filter']['tipepaket_nama'])){
                $params['tipepaket_nama'] = strtolower($yiiRestfulParams['advanced-filter']['tipepaket_nama']);
            }

            if(isset($yiiRestfulParams['advanced-filter']['jenispemeriksaanlab_nama'])){
                $params['jenispemeriksaanlab_nama'] = strtolower($yiiRestfulParams['advanced-filter']['jenispemeriksaanlab_nama']);
            }

            $url = 'allow/get-tarif-paket';
            // $response = $this->_restRajal->get($url, ['query'=>$params]);
            $response = $this->_restKasir->get($url, ['query'=>$params]);
            $body = json_decode($response->getBody(), true);
            $body = isset($body['response']) ? $body['response'] : [];
            $no = 0;
            foreach ($body['data'] as $key => $value) :
                $no++;
                $value['rowNum'] = $no;

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

            if (isset($yiiRestfulParams['advanced-filter']['jenispemeriksaanlab_nama'])){
                $params['jenispemeriksaanlab_nama'] = strtolower($yiiRestfulParams['advanced-filter']['jenispemeriksaanlab_nama']);
            }

            if (isset($yiiRestfulParams['advanced-filter']['daftartindakan_nama'])){
                $params['daftartindakan_nama'] = strtolower($yiiRestfulParams['advanced-filter']['daftartindakan_nama']);
            }
            $url = 'allow/get-tarif-tindakan';
            // $response = $this->_restRajal->get($url, ['query'=>$params]);
            $response = $this->_restKasir->get($url, ['query'=>$params]);
            $body = json_decode($response->getBody(), true);
            $body = isset($body['response']) ? $body['response'] : [];
            $no = 0;
            foreach ($body['data'] as $key => $value) :
                $no++;
                $value['rowNum'] = $no;
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

    public function actionGetListRuangan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_instalasi = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restRajal->get('allow/get-list-ruangan?instalasi_id='.$parent_instalasi);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value)
                $result['output'][] = [
                    'id' => $value['ruangan_id'],
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

    /**
     * @todo Method untuk mendapatkan data diagnosa berdasarkan versi tabular list
     * @author ardi
     */
    public function actionGetNewDiagnosa($q = '',$page = null,$type = 'diagnosa_masuk', $all_text = 0, $id_with_text = 0)
    {
        try {
            $limit = 10;
            $offset = ($page-1)*10;
            Yii::$app->response->format = Response::FORMAT_JSON;
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

            $request = $this->_restRajal->get('allow/get-new-diagnosa',['query'=>['q'=>$q,'type'=>$type,'page'=>$page,'offset'=>$offset,'limit'=>$limit]]);
            $response = json_decode($request->getBody(), true);

            $list = $response['response'];
            if ($all_text == 1) {
                foreach ($response['response'] as $value) {
                    $result['results'][] = [
                        'id' => $value['diagnosa_kode'].' - '.$value['diagnosa_nama'],
                        'text' => $value['diagnosa_kode'].' - '.$value['diagnosa_nama']
                    ];
                }
            } else {
                if ($id_with_text == 1) {
                    foreach ($response['response'] as $value) {
                        $result['results'][] = [
                            'id' => $value['diagnosa_id'].'_'.$value['diagnosa_kode'].' - '.$value['diagnosa_nama'],
                            'text' => $value['diagnosa_kode'].' - '.$value['diagnosa_nama']
                        ];
                    }
                } else {
                    foreach ($response['response'] as $value) {
                        $result['results'][] = [
                            'id' => $value['diagnosa_id'],
                            'text' => $value['diagnosa_kode'].' - '.$value['diagnosa_nama']
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

    public function actionGetDataDiagnosa()
    {
        $request = Yii::$app->request;
        $q = $request->get('q');
        $type = $request->get('type');
        $data = [];
        try {
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

            $response = $this->_restRajal->get('allow/get-new-diagnosa',[
                'query' => [
                    'q' => $q,
                    'type' => $type,
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $response = $response['response'];
            foreach ($response as $value) {
                $data[] = [
                    'id' => $value['diagnosa_id'].'_'.$value['diagnosa_kode'].' - '.$value['diagnosa_nama'],
                    'text' => $value['diagnosa_kode'].' - '.$value['diagnosa_nama']
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

    public function actionDoctorList()
    {
        $request = $this->_restRajal->get('allow/doctor-list', [
            'query' => Yii::$app->request->get('payload', [])
        ]);
        $body = json_decode($request->getBody(),TRUE);
        return $this->responseJson(200, 'Data Berhasil didapat!', $body['response']);
    }

    public function actionGetKamarTempatTidur()
    {
        return $this->helper->guzzleExec($this->_restRajal, [
            'method' => 'get',
            'url' => 'allow/get-kamar-tempat-tidur',
            'payload' => [
                'query' =>  Yii::$app->request->get('payload', [])
            ],
            'returnResponse' => true
        ]);
    }

    public function actionGetJenisKamar()
    {
       $request = $this->_restRajal->get('allow/get-jenis-kamar', [
           'query' => Yii::$app->request->get('payload', [])
       ]);
       $body = json_decode($request->getBody(),TRUE);
       return $this->responseJson(200, 'Data Berhasil didapat!', $body['response']);
    }

}
