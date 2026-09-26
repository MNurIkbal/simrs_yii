<?php

/**
 * @Author: sunarko
 * @Date:   2018-06-25 14:54:42
 * @Description:
 */

namespace Doco\ranap\controllers;

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

use app\modules\ranap\models\InfoPasienRanapForm;
use app\modules\ranap\models\PendaftaranForm;
use app\modules\ranap\models\PasienBatalPeriksaForm;


class InfPasienPindahController extends DocoController
{
    protected $_title = "Informasi pasien pindahan";
    protected $_controller = '/ranap/inf-pasien-pindah';
    protected $_restRanap;

    public function init()
    {
        parent::init();
        $this->_restRanap = Yii::$app->docoRest->ranap;
        $this->_restMaster = Yii::$app->docoRest->master;

    }

    public function behaviors()
    {
      $behaviors = parent::behaviors();
      unset($behaviors['access']);
      unset($behaviors['verbs']);
      return $behaviors;
    }

    /*=========================================
    =            list pasien pindahan         =
    =========================================*/

    public function actionIndex()
    {
        $response = $this->_restRanap->get('allow/get-api');
        $resResponseBody = json_decode($response->getBody(), True)['response']['master'];
        $body = json_decode($response->getBody(), TRUE);
        
        $getcarabayar = isset($resResponseBody['carabayar']) ? $resResponseBody['carabayar'] : [];
        $listcarabayar = ArrayHelper::map($getcarabayar, 'carabayar_id', 'carabayar_nama');

        $getpenjamin = isset($resResponseBody['penjamin']) ? $resResponseBody['penjamin'] : [];
        $listpenjamin = ArrayHelper::map($getpenjamin, 'penjamin_id', 'penjamin_nama');

        $getruangan = isset($resResponseBody['ruangan']) ? $resResponseBody['ruangan'] : [];
        $listruangan = ArrayHelper::map($getruangan, 'ruangan_id', 'ruangan_nama');

        $getkelaspelayanan = isset($resResponseBody['kelaspelayanan']) ? $resResponseBody['kelaspelayanan'] : [];
        $listkelaspelayanan = ArrayHelper::map($getkelaspelayanan, 'kelaspelayanan_id', 'kelaspelayanan_nama');

        $resResponselist_dokter = json_decode($response->getBody(), True)['response']['list_dokter'];
        $getlist_dokter = isset($resResponselist_dokter) ? $resResponselist_dokter : [];

        $resResponsejeniskasuspenyakit = json_decode($response->getBody(), True)['response']['jenis_kasus_penyakit'];
        $getjeniskasuspenyakit = isset($resResponsejeniskasuspenyakit) ? $resResponsejeniskasuspenyakit : [];
        $resMaster = $body['response']['master'];

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $idR = Yii::$app->docoVars->workspace("ruangan_id");
        
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        $counter=0;

        $response = $this->_restRanap->get('inf-pasien-pindah/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
        $body = json_decode($response->getBody(), True);
        $no = $request->get('start', 1);

        foreach ($body['response']["data"] as $key => $value) {
            $no++;
            $primary = json_encode($value['pindahkamar_id']);
            $value['primary'] = DocoHelpers::encrypt($primary);
            $ket_pindah = $value['is_pasientitipan'] == 1 ? "TITIPAN" : "APS";
            $data[$key] = $value;
            $data[$counter]['ruangan_sekarang'] = $value['ruangan_sekarang'].' - '.$value['kamar_sekarang'].' - '.$value['tempattidur_sekarang'];
            $data[$counter]['ruangan_pindah'] = $value['ruangan_pindah'].' - '.$value['kamar_pindah'].' - '.$value['tempattidur_pindah'];
            $data[$counter]['carBay'] = $value['carabayar_nama'].' / '.$value['penjamin_nama'];
            $data[$counter]['tgl_pindahkamar'] = date('d F Y H:i:s', strtotime($value['tgl_pindahkamar']));
            $data[$counter]['tgl_admisi'] = date('d F Y H:i:s', strtotime($value['tgl_admisi']));

            $data[$counter]['tgl_admisi'] = 'Tgl. Admisi : '.$data[$counter]['tgl_admisi'].'<br>Tgl. Pindah : '.$data[$counter]['tgl_pindahkamar'];
            $data[$counter]['no_rekam_medik'] = strtoupper($data[$counter]['nama_pasien']).' ('.substr($data[$counter]['jenis_kelamin'], 0, 1).')<br>No. Registrasi : '.$data[$counter]['no_pendaftaran'].'<br>No. RM : '.$data[$counter]['no_rekam_medik'];
            $data[$counter]['carBay'] = 'Cara Bayar : '.$data[$counter]['carabayar_nama'].'<br>Penjamin : '.$data[$counter]['penjamin_nama'];
            $data[$counter]['kelaspelayanan_nama'] = 'Hak  Kelas : -<br>Kelas Layanan : '.$data[$counter]['kelaspelayanan_nama'].'<br>Kelas Tagihan : '.$data[$counter]['kelas_ditagihkan_nama_pk'];
            $data[$counter]['ruangan_sekarang'] = 'Asal : '.$data[$counter]['ruangan_sekarang'].'<br>Tujuan : '.$data[$counter]['ruangan_pindah'];
            $data[$counter]['ket_pindah'] = $value['penjamin_id'] != 1 ? $ket_pindah : "-";
            $data[$counter]['rowNum'] = $no;
            $counter++;
        }
        $result['data'] = $data;
        $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
        $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

        return $result;
    }

    public function actionGetNoPendaftaran()
    {
        try{
            if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
                $tgl_pendaftaran = '';
                
                if(isset($_GET['z'])){
                    $tgl_pendaftaran = $_GET['z'];
                }
                $response = $this->_restRanap->request('POST', 'inf-pasien-pindah/data-pendaftaran',[
                    'form_params'=>['term'=>$_GET['q']['term'], 'date'=>$tgl_pendaftaran], 
                ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id' => $value['no_pendaftaran'], 'text' => $value['no_pendaftaran']];
                }
                $total = count($body['response']);
                $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
                return DocoHelpers::response($return);
            }
        }catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionGetNoRekamMedik()
    {
        try{
            if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
                $tgl_pendaftaran = '';
                if(isset($_GET['z'])){
                    $tgl_pendaftaran = $_GET['z'];
                }
                $response = $this->_restRanap->request('POST', 'inf-pasien-pindah/data-rekam-medik',[
                    'form_params'=>['term'=>$_GET['q']['term'], 'date'=>$tgl_pendaftaran],
                ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id' => $value['no_rekam_medik'], 'text' => $value['no_rekam_medik']];
                }
                $total = count($body['response']);
                $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
                return DocoHelpers::response($return);
            }
        }catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionGetPasien()
    {
        try{
            if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
                $tgl_pendaftaran = '';
                if(isset($_GET['z'])){
                    $tgl_pendaftaran = $_GET['z'];
                }
                $response = $this->_restRanap->request('POST', 'inf-pasien-pindah/data-nama-pasien',[
                    'form_params'=>['term'=>$_GET['q']['term'], 'date'=>$tgl_pendaftaran],
                ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id' => $value['nama_pasien'], 'text' => $value['nama_pasien']];
                }
                $total = count($body['response']);
                $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
                return DocoHelpers::response($return);
            }
        }catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionGetDokter()
    {
        try{
            if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
                $tgl_pendaftaran = '';
                if(isset($_GET['z'])){
                    $tgl_pendaftaran = $_GET['z'];
                }
                $response = $this->_restRanap->request('POST', 'inf-pasien-pindah/data-nama-dokter',[
                    'form_params'=>['term'=>$_GET['q']['term'], 'date'=>$tgl_pendaftaran],
                ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id' => $value['nama_pegawai'], 'text' => $value['nama_pegawai']];
                }
                $total = count($body['response']);
                $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
                return DocoHelpers::response($return);
            }
        }catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
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
            $response = $this->_restRanap->get('cara-bayar' . $params);
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
            $response = $this->_restRanap->get('penjamin?advanced-filter[carabayar_m.carabayar_nama]='.$parent_label);
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

    public function actionGetKasusPenyakit()
    {
        try{
            if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
                $tgl_pendaftaran = '';
                if(isset($_GET['z'])){
                    $tgl_pendaftaran = $_GET['z'];
                }
                $response = $this->_restRanap->request('POST', 'inf-pasien-pindah/data-kasus-penyakit',[
                    'form_params'=>['term'=>$_GET['q']['term'], 'date'=>$tgl_pendaftaran],
                ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id' => $value['jeniskasuspenyakit_nama'], 'text' => $value['jeniskasuspenyakit_nama']];
                }
                $total = count($body['response']);
                $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
                return DocoHelpers::response($return);
            }
        }catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionGetNamaRuangan()
    {
        try{
            if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
                $tgl_pendaftaran = '';
                if(isset($_GET['z'])){
                    $tgl_pendaftaran = $_GET['z'];
                }
                $response = $this->_restRanap->request('POST', 'inf-pasien-pindah/data-nama-ruangan',[
                    'form_params'=>['term'=>$_GET['q']['term'], 'date'=>$tgl_pendaftaran],
                ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id' => $value['ruangan_nama'], 'text' => $value['ruangan_nama']];
                }
                $total = count($body['response']);
                $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
                return DocoHelpers::response($return);
            }
        }catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if (isset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran'])) {
            $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_akhir = $tgl_pendaftaran_range[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
        }
        $path = Yii::getAlias("@download") . "/informasi-pasien-pindah-kamar.pdf";
        try {
            $response = $this->_restRanap->get('inf-pasien/export-pdf?&ruangan='.Yii::$app->docoVars->workspace("ruangan_id").'&'.http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    

}
