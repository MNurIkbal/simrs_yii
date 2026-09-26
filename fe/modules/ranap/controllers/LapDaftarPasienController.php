<?php

/**
 * @Author  : Sunarko
 * @Date    : 2018-08-14 10:49:36
 * @Last Modified by    :  
 * @Last Modified time  :  
 * @Description : membuat laporan pasien rawat inap
 */

namespace Doco\ranap\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;

use GuzzleHttp\Exception\RequestException;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;

use app\modules\ranap\models\LaporanPasienriView;


class LapDaftarPasienController extends DocoController
{
    protected $_title = "Laporan Pasien Rawat Inap";
    protected $_restRanap;

    public function init()
    {
        parent::init();
        $this->_restRanap = Yii::$app->docoRest->ranap;
        $this->_restMaster = Yii::$app->docoRest->master;

    }

    public function actionIndex()
    {
        $response = $this->_restRanap->get('allow/get-api');
        $body = json_decode($response->getBody(), TRUE);
        $resMaster = $body['response']['master'];
        $list_data = $this->getListDataPasien();
        $data_pegawai = $list_data["data_pegawai"];
        $data_statusperiksa = $list_data["data_statusperiksa"];
        $data_ruangan = $list_data["data_ruangan"];
        $data_jenis_kasus = $list_data["data_jenis_kasus"];
        
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $request = Yii::$app->request;
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        $counter=0;

        try {
            $response = $this->_restRanap->get('laporan-daftar-pasien/index?ruangan_id=' . $ruangan_id . '&' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            // print_r($body); die;
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primary = json_encode($value['pendaftaran_id']);
                $value['primary'] = DocoHelpers::encrypt($primary);
                $data[$key] = $value;
                $data[$counter]['carabayar_penjamin'] = $value['Cara Bayar'] . ' / ' . $value['Penjamin'];
                $data[$counter]['pendaftaran'] = $value['No. Pendaftaran'];
                $data[$counter]['r_medik'] = $value['No. Rekam Medik'];
                $data[$counter]['pasien'] = $value['Nama Pasien'];
                $data[$counter]['jk'] = $value['Jenis Kelamin'];
                $tempKelas = $value['Kelas Pelayanan'];
                $data[$counter]['kp'] = 'Kelas Layanan: '.$value['Kelas Pelayanan'].'<br>Kelas Tagihan: -';
                $data[$counter]['jkp'] = $value['Jenis Kasus Penyakit'];
                $data[$counter]['lr'] = ($value['Lama Rawat']) ? $value['Lama Rawat']." Hari" : "" ; 
                $data[$counter]['Tanggal Masuk'] = date('d F Y H:i:s', strtotime($value['Tanggal Masuk']));
                $data[$counter]['Tanggal Keluar'] = ($value['Tanggal Keluar']) ? date('d F Y H:i:s', strtotime($value['Tanggal Keluar'])) : "" ;

                if (!array_key_exists('kelas_ditagihkan_nama_pk', $value)) {
                    $value['kelas_ditagihkan_nama_pk'] = '-';
                }

                if (!array_key_exists('kelas_ditagihkan_nama', $value)) {
                    $value['kelas_ditagihkan_nama'] = '-';
                }

                if ($value['pindahkamar_id']) {
                    if ($value['is_stoptitipan_pk'] == false) {
                        if ($value['is_pasientitipan_pk']) {
                            $data[$counter]['kp'] = 'Kelas Layanan: '.$tempKelas.'<br>Kelas Tagihan: '.$value['kelas_ditagihkan_nama_pk'];
                        }
                    }
                } else {
                    if ($value['is_stoptitipan'] == false) {
                        if ($value['is_pasientitipan']) {
                            $data[$counter]['kp'] = 'Kelas Layanan: '.$tempKelas.'<br>Kelas Tagihan: '.$value['kelas_ditagihkan_nama'];
                        }
                    }
                }

                $data[$counter]['rowNum'] = $no;
                $counter++;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if (isset($yiiRestfulParams['advanced-filter']['Tanggal Masuk'])) {
            $tgl_masuk_range = explode(' - ', $yiiRestfulParams['advanced-filter']['Tanggal Masuk']);
            $tgl_awal = $tgl_masuk_range[0];
            $tgl_akhir = $tgl_masuk_range[1];

            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            
            $yiiRestfulParams['advanced-filter']['tgl_masuk_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_masuk_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['Tanggal Masuk']);
        }
        $path = Yii::getAlias("@download") . "/laporan-pasien-rawat-inap.pdf";
        try {
            $response = $this->_restRanap->get('laporan-daftar-pasien/export-pdf?ruangan_id='.Yii::$app->docoVars->workspace("ruangan_id").'&ruangan_nama='.Yii::$app->docoVars->workspace("ruangan_name").'&'.http_build_query($yiiRestfulParams),[
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

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if (isset($yiiRestfulParams['advanced-filter']['Tanggal Masuk'])) {
            $tgl_masuk_range = explode(' - ', $yiiRestfulParams['advanced-filter']['Tanggal Masuk']);
            $tgl_awal = $tgl_masuk_range[0];
            $tgl_akhir = $tgl_masuk_range[1];

            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));

            $yiiRestfulParams['advanced-filter']['tgl_masuk_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_masuk_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['Tanggal Masuk']);
        }
        try {
            $path = Yii::getAlias("@download") . "/laporan-daftar-pasien.xlsx";
            $query = [
                'ruangan_id' => Yii::$app->docoVars->workspace("ruangan_id"),
            ];
            $query = array_merge($query,$yiiRestfulParams);

            $response = $this->_restRanap->get('laporan-daftar-pasien/export-excel',[
                'query' => $query,
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    /**
     *
     * private function
     *
     */
    private function getListDataPasien()
    {
        try {
            $request = Yii::$app->request;
            $response = $this->_restRanap->get('laporan-daftar-pasien/get-list-data?id_ruangan='.Yii::$app->docoVars->workspace('ruangan_id'));
            $body = json_decode($response->getBody(),TRUE);
            
            $data_statusperiksa = empty($body['response']['data-statusperiksa']) ? [] : $body['response']['data-statusperiksa'];
            $data_pegawai = empty($body['response']['data-pegawai']) ? [] : $body['response']['data-pegawai'];
            $data_ruangan = empty($body['response']['data-ruangan']) ? [] : $body['response']['data-ruangan'];
            $data_jenis_kasus = empty($body['response']['data-jenis-kasus']) ? [] : $body['response']['data-jenis-kasus'];
           
            $result = [
                'data_statusperiksa' => $data_statusperiksa,
                'data_pegawai' => $data_pegawai,
                'data_ruangan' => $data_ruangan,
                'data_jenis_kasus' => $data_jenis_kasus,
            ];

            return $result;
        } catch (RequestException $e) {
            return [
                'data_statusperiksa' => [],
                'data_pegawai' => [],
                'data_ruangan' => [],
                'data_jenis_kasus' => [],
            ];
        } catch (\Exception $e) {
            return [
                'data_statusperiksa' => [],
                'data_pegawai' => [],
                'data_ruangan' => [],
                'data_jenis_kasus' => [],
            ];
        }
    }

}
