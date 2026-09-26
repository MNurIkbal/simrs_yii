<?php 

/**
 * @author Randy Vianda Putra
 * @todo Laporan Waktu Tunggu Lab
 * @copyright 01 Agustus 2018 aweutist
 */

namespace Doco\laboratorium\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;

class LaporanWaktuTungguController extends DocoController
{
    protected $_title = "Laporan Waktu Tunggu Pemeriksaan Laboratorium";
    protected $_module = '/laboratorium/laporan-waktu-tunggu';
    protected $_restLab;
    protected $allowAction = [
        '*'
    ];

    public function init()
    {
        parent::init();
        $this->_restLab = Yii::$app->docoRest->laboratorium;
    }

    public function actionIndex()
    {
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $cache = Yii::$app->cache;
        $title = $this->_title;
        // $data = $this->getDataApi($id);
        // dump($data);exit;
        // $data_pasien = !empty($data['data_pasien']) ? $data['data_pasien'] : [];
        // $pasienmasukpenunjang_id = DocoHelpers::decrypt($id);
        // $count_tindakan = !empty($data['data_pasien_detail']) ? count($data['data_pasien_detail']) : 0;

        // // initial cache list sample
        // $transLab = $cache->get('addSampleLab' . $ruangan_id . '-' . $pegawai_id . $pasienmasukpenunjang_id);
        // if ($transLab === false) {
        //     $list_cache = [];
        //     $list_sample = json_encode($list_cache);
        //     $cache->set('addSampleLab'.  $ruangan_id . '-' . $pegawai_id . $pasienmasukpenunjang_id, $list_sample);
        // }
        // $transLab = $cache->get('addSampleLab' . $ruangan_id . '-' . $pegawai_id . $pasienmasukpenunjang_id);

        return $this->render('index', get_defined_vars());
    }

    private function getDataApi($id = null)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restLab->request('GET', 'speciment/generate-api?id=' . $id);
            $body = json_decode($response->getBody(),TRUE);

            $return = [
                'data_pasien' => $body['response']['data-pasien'],
                'data_pasien_detail' => $body['response']['data-pasien-detail'],
                'data_sample' => $body['response']['data-sample'],
                'data_satuan' => $body['response']['data-satuan'],
            ];

            return $return;
        } catch (RequestException $e) {
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionGetData()
    {
        try {
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $response = $this->_restLab->request('get', 'lap-waktu-tunggu/index?'. http_build_query($yiiRestfulParams), ['form_params' => []]);
            $row = $cache = [];
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['samplelab_id']);
                $pasienmasukpenunjang_id = DocoHelpers::encrypt($value['pasienmasukpenunjang_id']);
                $value['primary'] = $primaryKey;
                $value['penunjang_id'] = $pasienmasukpenunjang_id;
                unset($value['samplelab_id']);
                $value['rowNum'] = $no;
                $value['tgl_ambilsample'] = empty($value['tgl_ambilsample']) 
                    ? '' 
                    : date('d M Y', strtotime($value['tgl_ambilsample'])) .' '. date('H:i:s', strtotime($value['jam_ambilsample']));
                $value['tglmasukpenunjang'] = empty($value['tglmasukpenunjang']) 
                    ? '' 
                    : date('d M Y H:i:s', strtotime($value['tglmasukpenunjang']));
                $value['tgl_expertise'] = empty($value['tgl_expertise']) 
                    ? '' 
                    : date('d M Y H:i:s', strtotime($value['tgl_expertise']));
                $value['tgl_hasilpemeriksaanlab'] = empty($value['tgl_hasilpemeriksaanlab']) 
                    ? '' 
                    : date('d M Y H:i:s', strtotime($value['tgl_hasilpemeriksaanlab']));
                $value['is_expertise'] = empty($value['is_expertise']) ? '' : '&#10003;';
                $tgl_ambil_sample = $value['tgl_ambilsample'];
                $diffPendaftaran = DocoHelpers::getLamaTunggu($value['tglmasukpenunjang'], $value['tgl_expertise']);
                $diffSample = DocoHelpers::getLamaTunggu($value['tgl_ambilsample'], $value['tgl_expertise']);
                $value['waktu_tunggu_daftar'] = $diffPendaftaran;
                $value['waktu_tunggu_sample'] = $diffSample;
                $row[$key] = $value;
            }
            $return = [
                'data' => $row,
                'draw' => $request->get('draw'),
                'recordsTotal' => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount']
            ];

            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionGetDokter()
    {
        if (isset($_GET['term']) && !empty($_GET['term'])) {
            $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
            $response = $this->_restLab->request('get', 'allow/get-dokter-lab',[
                'query' => ['ruangan_id' => $ruangan_id, 'nama_pegawai' => $_GET['term']],
            ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = ['id'=>$value['nama_pegawai'],'text'=>$value['nama_pegawai']];
            }
            $total = count($body['response']);
            $return = ['result' => $data, 'total_count' => $total, 'incomplete_results' => false];
            return DocoHelpers::response($return);
        }
    }

    public function actionGetPemeriksaan()
    {
        if (isset($_GET['term']) && !empty($_GET['term'])) {
            $response = $this->_restLab->request('get', 'allow/get-pemeriksaan', [
                'query' => ['pemeriksaanlab_nama' => $_GET['term']],
            ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = ['id' => $value['pemeriksaanlab_nama'], 'text' => $value['pemeriksaanlab_nama']];
            }
            $total = count($body['response']);
            $return = ['result' => $data, 'total_count' => $total,'incomplete_results' => false];
            return DocoHelpers::response($return);
        }
    }

    public function actionGetSpecimen()
    {
        if (isset($_GET['term']) && !empty($_GET['term'])) {
            $response = $this->_restLab->request('get', 'allow/get-sample', [
                'query' => ['nama_sample' => $_GET['term']],
            ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = ['id' => $value['nama_sample'], 'text' => $value['nama_sample']];
            }
            $total = count($body['response']);
            $return = ['result' => $data, 'total_count' => $total, 'incomplete_results'=>false];
            return DocoHelpers::response($return);
        }
    }

    public function actionExportExcel()
    {
        // Convert to json format
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Get request
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['ruangan'] = Yii::$app->docoVars->workspace('ruangan_name');

        // Try catch
        try {
            // Response
            $path = Yii::getAlias("@download") . "/laporan-waktu-tunggu.xlsx";
            $response = $this->_restLab->get('lap-waktu-tunggu/export-excel?' . http_build_query($yiiRestfulParams),[
                'save_to' => $path
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionExportPdf(){
        // Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        try {
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $yiiRestfulParams['advanced-filter']['ruangan'] = Yii::$app->docoVars->workspace('ruangan_name');
            $path = Yii::getAlias("@download") . "/lap-waktu-tunggu-pasien-laboratorium.pdf";
            $response = $this->_restLab->get('lap-waktu-tunggu/export-pdf?' . http_build_query($yiiRestfulParams), [
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            // return $path;
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }
}