<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-03-06 15:40:51
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2019-04-01 20:01:54
 */

namespace Doco\apotek\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use yii\helpers\Json;
use app\components\DocoConstants;

class InformasiPenjualanResepRumahsakitController extends DocoController
{
    protected $_title = "Informasi Penjualan Resep Rumah Sakit";
    protected $_module = '/apotek/informasi-penjualan-resep-rumahsakit';
    protected $_restApotek;

    public function init()
    {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek;
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        $title  = $this->_title;
        $carabayar = \Yii::$app->cache->get('carabayar');

        $loginpemakai_id = Yii::$app->docoVars->user("id");
        $cekLoket = empty(Yii::$app->docoVars->workspace("loket_farmasi_reseptur")[$loginpemakai_id])
                ? false
                : Yii::$app->docoVars->workspace("loket_farmasi_reseptur")[$loginpemakai_id];
        if ($cekLoket) {
            if (!$carabayar) {
                try {

                    $response = $this->_restMaster->get('cara-bayar/index?advanced-filter[is_active]=1');
                    $body = json_decode($response->getBody(), true);
                    $carabayar_data = ArrayHelper::map($body['response']['data'],'carabayar_nama','carabayar_nama');
                } catch (\Exception $e) {
                    $carabayar_data = [];
                } catch (\RequestException $e) {
                    $carabayar_data = [];
                }
                \Yii::$app->cache->set('carabayar', $carabayar_data, 60);
                $carabayar = $carabayar_data;
            }

            return $this->render('index', get_defined_vars());
        } else {
            return $this->actionPilihLoket(DocoConstants::JA_FAR);
        }

    }
    public function actionView($id)
    {
        $title = 'Detail Informasi Penjualan Resep Rumah Sakit';
        $penjualanresep_id = DocoHelpers::decrypt($id);
        $pendaftaranid = '';
        $noresep = '';
        try {
            $response = $this->_restApotek->get('inf-penjualan-resep-rumahsakit/data-detail', ['query' => ['id'=>$penjualanresep_id]]);
            $body = json_decode($response->getBody(), true);
            $data = $body['response'];
            $pendaftaranid = $data['pendaftaran_id'];
            $noresep_penjualan = $data['noresep'];
            // $noresep = $data['noresep'];
            // $noresep_penjualan = $data['noresep_penjualan'];
            $iter = $data['iter'];
            $disableButton = false;
            if ($iter < 1) {
                $disableButton = true;
            }

        } catch (\Exception $e) {
            $data = [];
        } catch (\RequestException $e) {
            $data = [];
        }
        return $this->render('detail',get_defined_vars());
    }
    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        try{
            $response = $this->_restApotek->post('inf-penjualan-resep-rumahsakit/delete-resep', ['form_params'=>['id'=>$id]]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response($body['response']);
        } catch(\Exception $e){
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch(RequestException $e){
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }

    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['ruangan_id'] = Yii::$app->docoVars->workspace("ruangan_id");

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restApotek->get('inf-penjualan-resep-rumahsakit/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['penjualanresep_id']);
                unset($value['penjualanresep_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;

                if (!empty($value['no_antrian'])) {
                    $terbilang = !empty($value['no_antrian']) ? DocoHelpers::convertAntrian($value['no_antrian']) : '';

                    $value['no_antrian'] = '<button type="button" class="btn btn-info btn-labeled btn-xs panggil" data-noantrian="'.$value['no_antrian'].'" data-nama="'.$value['nama_pasien'].'" data-parent="" data-antrian="'.$terbilang.'"><b><i class="fa fa-volume-up"></i></b>' .$value['no_antrian'].'</button>';

                } else {
                    $value['no_antrian'] = "-";
                }

                $value['tglpenjualan'] = date('d-M-Y', strtotime($value['tglpenjualan']));
                $value['totaltagihan'] = DocoHelpers::formatNumber($value['totalhargajual']);
                unset($value['totalhargajual']);
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
    public function actionGetPenjamin()
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
                    'id' => $value['penjamin_nama'],
                    'name' => $value['penjamin_nama']
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
    public function actionGetNoresep()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            try {
                $response = $this->_restApotek->request('POST', 'inf-penjualan-resep-rumahsakit/data-resep',[
                            'form_params'=>['term'=>$_GET['q']['term']],
                        ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {

                    $data[] = ['id'=>$value['noresep'],'text'=>$value['noresep']];

                }
                $total = count($body['response']);
            } catch (\RequestException $e) {
                $data = [];
                $total = 0;
            } catch (\Exception $e) {
                $data = [];
                $total = 0;
            }
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
            return DocoHelpers::response($return);
        }
    }
    public function actionGetNopendaftaran()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            try {
                $response = $this->_restApotek->request('POST', 'inf-penjualan-resep-rumahsakit/data-pendaftaran',[
                            'form_params'=>['term'=>$_GET['q']['term'],'ruangan_id'=>Yii::$app->docoVars->workspace("ruangan_id")],
                        ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {

                    $data[] = ['id'=>$value['pendaftaran_id'],'text'=>$value['no_pendaftaran']];

                }
                $total = count($body['response']);
            } catch (\RequestException $e) {
                $data = [];
                $total = 0;
            } catch (\Exception $e) {
                $data = [];
                $total = 0;
            }
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
            return DocoHelpers::response($return);
        }
    }
    public function actionGetDataObat()
    {
        $id = DocoHelpers::decrypt($_GET['id']);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['ruangan_id'] = Yii::$app->docoVars->workspace("ruangan_id");
        $yiiRestfulParams['advanced-filter']['penjualanresep_id'] = $id;
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restApotek->get('inf-penjualan-resep-rumahsakit/data-obat?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            // \Yii::$app->cache->set('dataobat_'.$id, $body['response'], 60);
            // \Yii::$app->cache->delete('dataobat_'.$id, $cache);
            $cache = $body['response']['data'];
            \Yii::$app->cache->set('dataobat_'.$id, $cache);

            $no = $request->get('start',1);
            $totalharga = 0;
            foreach ($cache as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['obatalkes_id']);
                unset($value['obatalkes_id']);
                $resValue['primary'] = $primaryKey;
                $resValue['rowNum'] = $no;
                $jenisRacikan = ($value['racikan_id'] == 1) ? Yii::t('fe', 'Racikan') : Yii::t('fe', 'Non Racikan');
                $resValue['jenis_racikan'] = $jenisRacikan;
                $resValue['signa_oa'] = !empty($value['signa_oa']) ? $value['signa_oa'] : '-';
                $resValue['rke'] = !empty($value['rke']) ? $value['rke'] : '-';
                $total = $value['qty_oa']*($value['hargasatuan_oa']);
                $resValue['totaltagihan'] = DocoHelpers::formatNumber($total);
                $resValue['hargajual_oa'] = DocoHelpers::formatNumber($value['hargasatuan_oa']);
                $resValue['qty_oa'] = DocoHelpers::formatNumber($value['qty_oa']);
                $resValue['obatalkes_nama'] = $value['obatalkes_nama'];
                $resValue['ppn_persen'] = $value['ppn_persen'];
                $resValue['ruangan_id'] = $value['ruangan_id'];
                $resValue['racikan_id'] = $value['racikan_id'];
                $totalharga += $total;
                $data[$key] = $resValue;
            }
            // echo "<pre>";var_dump($data);die();
            $result['totalobat'] = DocoHelpers::formatNumber($totalharga);
            $result['data'] = $data;
            $result['draw'] = $draw;
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
     * @Author: Rizqi Fitrianto
     * @Date:   2018-03-12
     * @Add by:   Rizqi Fitrianto
     * @todo: action for copy resep, params needed
     * penjualanresep_id, noresep, nopendaftaran
     */
    public function actionCopyResep($id,$noresep,$pendaftaranid)
    {
        try {
            $id = DocoHelpers::decrypt($id);
            $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
            $dataObat = \Yii::$app->cache->get('dataobat_'.$id);
            $postData = ['penjualanresep_id'=>$id,'pendaftaran_id'=>$pendaftaranid,'noresep'=>$noresep, 'dataObat'=>$dataObat];
            $response = $this->_restApotek->request('POST', 'inf-penjualan-resep-rumahsakit/copy-resep', [
                'form_params' => $postData
            ]);
            $body = json_decode($response->getBody(), true);
            /*if($body['response'] == false){
                $result = ['response'=>[
                            'return' => 'disableButton()',
                        ]
                    ];
            }else{
                $result = $body['response'];
            }*/
            return DocoHelpers::response($body);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }
    public function actionPrintDetail(){
        $request = Yii::$app->request;
        $id = $request->get('id');
        $id = DocoHelpers::decrypt($id);
        $noresep = $request->get('noresep');
        $path = Yii::getAlias("@download") . "/rincian-tagihan-penjualan-resep-".$noresep.".pdf";
        try {
            $response = $this->_restApotek->get('inf-penjualan-resep-rumahsakit/print-detail',[
                'save_to' => $path,
                'query'=>[
                    'id'=>$id
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    // clone dari pendaftaran untuk pemilihan loket apotek : ali.padilah@docotel.com
    public function actionPilihLoket($jenisantrian_id)
    {
        $title = Yii::t('fe', 'Pilih loket');
        try {
            $requests = $this->_restMaster->get('allow/get-loket?jenisantrian_id='.$jenisantrian_id);
            $response = json_decode($requests->getBody(), true);
            $listResponses = $response['response'];
        } catch (\RequestException $e) {
            $listResponses = [];
        } catch (\Exception $e) {
            $listResponses = [];
        }
        return $this->render('pilih-loket', get_defined_vars());
    }

    public function actionPanggilAntrian($no_antrian = null, $extend_text = null, $loket = null, $loket_id = null)
    {
        $loginpemakai_id = Yii::$app->docoVars->user("id");
        $cekLoket = empty(Yii::$app->docoVars->workspace("loket_farmasi_reseptur")[$loginpemakai_id])
                ? false
                : Yii::$app->docoVars->workspace("loket_farmasi_reseptur")[$loginpemakai_id];

        // set ke display antrian
        $data_display["panggil_antrian"] = [
            'no_antrian' =>  $no_antrian,
            'no_loket' => empty($cekLoket['loket_nourut']) ? 0 : $cekLoket['loket_nourut'],
            'loket_id' => empty($cekLoket['loket_id']) ? 0 : $cekLoket['loket_id'],
            'extend_text' => $extend_text,
        ];

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'display-antrian',
            'message' => Json::encode(['data' => $data_display])
        ]);

        // set ke display antrian
        $data_display["set_antrian"] = [
            'no_antrian' =>  $no_antrian,
            'loket_id' => empty($cekLoket['loket_id']) ? 0 : $cekLoket['loket_id'],
        ];

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'display-antrian',
            'message' => Json::encode(['data' => $data_display])
        ]);
        // end set display antrian

        return true;
        // end set display antrian
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
                'loginpemakai_id'=>$loginpemakai_id,
                'loket_id'=>$loket_id,
            ];
            $request = $this->_restApotek->get('inf-reseptur/set-loket', [
                'form_params' => $params
            ]);
            $body = json_decode($request->getBody(),TRUE);
            $response = $body['response'];

            // use for all antrian biar engga perlu lagi hit backend, expired mengikuti active_workspace
            $active_workspace['loket_farmasi_reseptur'] = [$loginpemakai_id=>$response];
            $session->set('active_workspace', $active_workspace);

            return DocoHelpers::response('Loket berhasil di set.', 200);
        } catch (Exception $e) {
             return DocoHelpers::response(['message' => $e->getMessage()],500);
        }

    }
    //end clone

}