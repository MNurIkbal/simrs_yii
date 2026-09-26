<?php

// Author : Budi

namespace Doco\rm\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class LaporanSirsController extends DocoController
{

    protected $_restRm;
    protected $_module = '/rm/laporan/';
    public $_title;

    public function init()
    {
        parent::init();
        $this->_restRm = Yii::$app->docoRest->rm;
        $this->_title = 'Laporan SIRS';
    }

    public function actionIndex()
    {
        $title = $this->_title;
        try {
            $response = $this->_restRm->get('allow/get-look-up',[
                'query' => [
                    'params' => 'jenis_rl'
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $jenisLaporan = $response['response'];
        } catch (RequestException $e) {
            $jenisLaporan = [];
        }
        return $this->render('index', get_defined_vars());
    }

    public function actionGetLaporan()
    {
        $request = Yii::$app->request;
        $jenis_laporan = $request->get('jenis_laporan');
        switch ($jenis_laporan  ) {
            case 372:
                /** RL 1.1  Data Dasar Rumah Sakit**/
                return $this->laporanDataDasarRs();
                break;
            case 373:
                /** RL 1.2  Indikator Pelayanan Rumah Sakit**/
                return $this->laporanIndikatorRs();
                break;
            case 374:
                /** RL 1.3  Fasilitas Tempat Tidur Ranap**/
                return $this->laporanFasilitasTempatTidurRanap();
                break;
            case 376:
                /** RL 3.1  Kegiatan Pelayanan Rawat Inap**/
                return $this->laporanRl_3_1();
                break;
            case 389:
                /** RL 3.14  Rujukan**/
                return $this->laporanRujukan();
                break;
            case 377:
                /** RL 3.2  Kegiatan Pelayanan Rawat Darurat**/
                return $this->laporanKegiatanPelayananIgd();
                break;
            case 381:
                /** RL 3.6  Pembedahan**/
                return $this->laporanPembedahan();
                break;
            case 382:
                /** RL 3.7  Radiologi**/
                return $this->laporanRadiologi();
                break;
            case 383:
                /** RL 3.7  Laboratorium**/
                return $this->laporanLaboratorium();
                break;
            case 391:
                /** RL 4.a  laporan Morbiditas Ranap**/
                return $this->laporanMorbiditasRanap();
                break;
            case 392:
                /** RL 4.b  laporan Morbiditas Rajal**/
                return $this->laporanMorbiditasRajal();
                break;
            case 393:
                /** RL 5.2 Laporan Pengunjung Rawat Jalan **/
                return $this->laporanPengunjungRajal();
                break;
            case 394:
                /** RL 5.2 Laporan Kunjungan Rawat Jalan **/
                return $this->laporanKunjunganRajal();
                break;
            case 395:
                /** RL 5.3 Daftar 10 Besar Penyakit Rawat Inap **/
                return $this->laporanSepuluhBesarPenyakit();
                break;
            case 396:
                /** RL 5.4 Daftar 10 Besar Penyakit Rawat Jalan **/
                return $this->laporanRl_5_4();
                break;
            default:
               return;
                break;
        }
    }

    protected function laporanDataDasarRs()
    {
        $request = Yii::$app->request;
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');
        $jenis_laporan = $request->get('jenis_laporan');
        try {
            $response = $this->_restRm->get('laporan-sirs/get-laporan',[
                'query' => [
                    'jenis_laporan' => $jenis_laporan,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $response = $response['response'];
        } catch (RequestException $e) {
            $response = [];
        }

        return $this->renderpartial('rl_1_1',get_defined_vars());
    }

    /**
     * @todo Fungsi untuk menampilkan laporan Indikator RS
     * @author Aris Munandar <aris.m@docotel.com>
    */
    protected function laporanIndikatorRs()
    {
        $request = Yii::$app->request;
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');
        $jenis_laporan = $request->get('jenis_laporan');
        try {
            $response = $this->_restRm->get('laporan-sirs/get-laporan',[
                'query' => [
                    'jenis_laporan' => $jenis_laporan,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $response = $response['response'];

        } catch (RequestException $e) {
            $response = [];
        }

        return $this->renderpartial('rl_1_2',get_defined_vars());
    }

    protected function laporanKegiatanPelayananIgd()
    {
        $request = Yii::$app->request;
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');
        $jenis_laporan = $request->get('jenis_laporan');
        try {
            $response = $this->_restRm->get('laporan-sirs/get-laporan',[
                'query' => [
                    'jenis_laporan' => $jenis_laporan,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $response = $response['response'];

            $tableHeader = $response['tabelHeader'];
            $contents = $response['contents'];
        } catch (RequestException $e) {
            $response = [];

            $tableHeader = [];
            $contents = [];
        }

        return $this->renderpartial('rl_3_2',get_defined_vars());
    }

    protected function laporanPembedahan()
    {
        $request = Yii::$app->request;
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');
        $jenis_laporan = $request->get('jenis_laporan');
        try {
            $response = $this->_restRm->get('laporan-sirs/get-laporan',[
                'query' => [
                    'jenis_laporan' => $jenis_laporan,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $response = $response['response'];
            $header = isset($response['header']) ? $response['header'] : [];
            $list = isset($response['list']) ? $response['list'] : [];
            $detail = isset($response['detail']) ? $response['detail'] : [];
        } catch (RequestException $e) {
            $header = $list = $detail = [];
        }

        return $this->renderpartial('rl_3_6',get_defined_vars());
    }

    protected function laporanRadiologi()
    {
        $request = Yii::$app->request;
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');
        $jenis_laporan = $request->get('jenis_laporan');
        try {
            $response = $this->_restRm->get('laporan-sirs/get-laporan',[
                'query' => [
                    'jenis_laporan' => $jenis_laporan,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $response = $response['response'];
            $detail = isset($response['detail']) ? $response['detail'] : [];

        } catch (RequestException $e) {
            $header = $list = $detail = [];
        }

        return $this->renderpartial('rl_3_7',get_defined_vars());
    }

    protected function laporanLaboratorium()
    {
        $request = Yii::$app->request;
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');
        $jenis_laporan = $request->get('jenis_laporan');
        try {
            $response = $this->_restRm->get('laporan-sirs/get-laporan',[
                'query' => [
                    'jenis_laporan' => $jenis_laporan,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $response = $response['response'];
            $detail = isset($response['detail']) ? $response['detail'] : [];

        } catch (RequestException $e) {
            $header = $list = $detail = [];
        }

        return $this->renderpartial('rl_3_8',get_defined_vars());
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');
        $jenis_laporan = $request->get('jenis_laporan');

        switch ($jenis_laporan  ) {
            case 372:
                /** RL 1.1  Data Dasar Rumah Sakit**/
                $namaLaporan = Yii::t('fe', 'RL 1.1  Data Dasar Rumah Sakit');
                break;
            case 373:
                /** RL 1.2  Indikator Pelayanan Rumah Sakit**/
                $namaLaporan = Yii::t('fe', 'RL 1.2  Indikator Pelayanan Rumah Sakit');
                break;
            case 374:
                /** RL 1.3  Fasilitas Tempat Tidur Ranap**/
                $namaLaporan = Yii::t('fe', 'RL 1.3  Fasilitas Tempat Tidur Ranap');
                break;
            case 376:
                /** RL 3.1  Kegiatan Pelayanan Rawat Inap**/
                $namaLaporan = Yii::t('fe', 'RL 3.1  Kegiatan Pelayanan Rawat Inap');
                break;
            case 377:
                /** RL 3.2  Kegiatan Pelayanan Rawat Darurat**/
                $namaLaporan = Yii::t('fe', 'RL 3.2  Kegiatan Pelayanan Rawat Darurat');
                break;
            case 381:
                /** RL 3.6  Pembedahan**/
                $namaLaporan = Yii::t('fe', 'RL 3.6  Pembedahan');
                break;
            case 382:
                /** RL 3.7  Radiologi**/
                $namaLaporan = Yii::t('fe', 'RL 3.7  Radiologi');
                break;
            case 383:
                /** RL 3.7  Laboratorium**/
                $namaLaporan = Yii::t('fe', 'RL 3.7  Laboratorium');
                break;
            case 391:
                /** RL 4.a  laporan Morbiditas Ranap**/
                $namaLaporan = Yii::t('fe', 'RL 4.a  laporan Morbiditas Ranap');
                break;
            case 392:
                /** RL 4.a  laporan Morbiditas Rajal**/
                $namaLaporan = Yii::t('fe', 'RL 4.b  laporan Morbiditas Rajal');
                break;
            case 393:
                /** RL 5.2 Laporan Pengunjung Rawat Jalan **/
                $namaLaporan = Yii::t('fe', 'RL 5.2 Laporan Pengunjung Rawat Jalan');
                break;
            case 394:
                /** RL 5.2 Laporan Kunjungan Rawat Jalan **/
                $namaLaporan = Yii::t('fe', 'RL 5.2 Laporan Kunjungan Rawat Jalan');
                break;
            case 395:
                /** RL 5.3 Daftar 10 Besar Penyakit Rawat Inap **/
                $namaLaporan = Yii::t('fe', 'RL 5.3 Daftar 10 Besar Penyakit Rawat Inap');
                break;
            case 396:
                /** RL 5.4 Daftar 10 Besar Penyakit Rawat Jalan **/
                $namaLaporan = Yii::t('fe', 'RL 5.4 Daftar 10 Besar Penyakit Rawat Jalan');
                break;
            case 389:
                /** RL 3.14 Daftar Rujukan **/
                $namaLaporan = Yii::t('fe', 'RL 3.14 Daftar Rujukan');
                break;
            default:
                $namaLaporan = Yii::t('fe', 'Laporan SIRS');
                break;
        }

        try {
            $path = Yii::getAlias("@download") . "/".$namaLaporan.".xlsx";
            $response = $this->_restRm->get('laporan-sirs/export-excel',[
                'query' => [
                    'jenis_laporan' => $jenis_laporan,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                ],
                'save_to' => $path,
            ]);
            // dump(json_decode($response->getBody(), true));die;
            return DocoHelpers::downloadFile($path, true);
        } catch (RequestException $e) {
            //var_dump($e->getMessage());die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            //var_dump($e->getMessage());die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }



    protected function laporanFasilitasTempatTidurRanap()
    {
        $request = Yii::$app->request;
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');
        $jenis_laporan = $request->get('jenis_laporan');
        try {
            $response = $this->_restRm->get('laporan-sirs/get-laporan',[
                'query' => [
                    'jenis_laporan' => $jenis_laporan,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $response = $response['response'];
            $header = isset($response['header']) ? $response['header'] : [];
            $list = isset($response['list']) ? $response['list'] : [];
            $detail = isset($response['detail']) ? $response['detail'] : [];
            $dataBed = isset($response['dataBed']) ? $response['dataBed'] : [];
            $profil = isset($response['profil']) ? $response['profil'] : [];
        } catch (RequestException $e) {
            $profil = $dataBed = $header = $list = $detail = [];
        }
        asort($header);
        asort($list);

        return $this->renderpartial('rl_1_3',get_defined_vars());
    }


    protected function laporanRl_3_1()
    {
        $request = Yii::$app->request;
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');
        $jenis_laporan = $request->get('jenis_laporan');
        try {
            $response = $this->_restRm->get('laporan-sirs/get-laporan',[
                'query' => [
                    'jenis_laporan' => $jenis_laporan,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $response = $response['response'];
            $header = isset($response['header']) ? $response['header'] : [];
            $list = isset($response['list']) ? $response['list'] : [];
            $detail = isset($response['detail']) ? $response['detail'] : [];
            $dataBed = isset($response['dataBed']) ? $response['dataBed'] : [];
            $profil = isset($response['profil']) ? $response['profil'] : [];
            $dataPelayanan = isset($response['data-pelayanan']) ? $response['data-pelayanan'] : [];
        } catch (RequestException $e) {
            $header = $dataBed = $list = $detail = [];
        }

        return $this->renderpartial('rl_3_1',get_defined_vars());
    }

    protected function laporanRujukan()
    {
        $request = Yii::$app->request;
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');
        $jenis_laporan = $request->get('jenis_laporan');
        try {
            $response = $this->_restRm->get('laporan-sirs/get-laporan',[
                'query' => [
                    'jenis_laporan' => $jenis_laporan,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $response = $response['response'];
            $profil = isset($response['profil']) ? $response['profil'] : [];
            $dataRujukan = isset($response['data-rujukan']) ? $response['data-rujukan'] : [];
            $asalRujukan = isset($response['asal-rujukan']) ? $response['asal-rujukan'] : [];
        } catch (RequestException $e) {
            $dataRujukan = $asalRujukan = $profil = [];
        }

         return $this->renderpartial('rl_3_14',get_defined_vars());
    }

    protected function laporanRl_5_4()
    {
        $request = Yii::$app->request;
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');
        $jenis_laporan = $request->get('jenis_laporan');
        $textBulan = '';
        try {
            $response = $this->_restRm->get('laporan-sirs/get-laporan',[
                'query' => [
                    'jenis_laporan' => $jenis_laporan,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $response = $response['response'];
            $datarl_5_4 = $response['datarl5_4'];
            $profil = $response['profil'];
            $textBulan = $bulan && isset(DocoHelpers::$namaBulan[$bulan-1]) ? DocoHelpers::$namaBulan[$bulan-1] : '';
        } catch (RequestException $e) {
            $header = [];
            $datarl_5_4 = [];
        } catch (\Exception $e) {
            $header = [];
            $datarl_5_4 = [];
        }
        return $this->renderpartial('rl_5_4',get_defined_vars());
    }

    protected function laporanMorbiditasRanap()
    {
        $request = Yii::$app->request;
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');
        $jenis_laporan = $request->get('jenis_laporan');
        try {
            $response = $this->_restRm->get('laporan-sirs/get-laporan',[
                'query' => [
                    'jenis_laporan' => $jenis_laporan,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'header' => true,
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $response = $response['response'];
            // dump($response);die;
            $listGolJk = isset($response['columns']) ? $response['columns'] : [];
            $listHeader = isset($response['header']) ? $response['header'] : [];
            $columns = isset($response['columns']) ? $response['columns'] : [];

            foreach($listGolJk as $k => $v) {
                if($v['title'] == 'L' || $v['title'] == 'P'){
                    $listJk[] = $v['title'];
                }
            }

            foreach($listHeader as $k => $v){
                $header[] = $v;
            }
            $countJk = count($listJk) ?: 0;
            $countHeader = count($header) ?: 0;
            $profil = $response['profil'];
            $textBulan = $bulan && isset(DocoHelpers::$namaBulan[$bulan-1]) ? DocoHelpers::$namaBulan[$bulan-1] : '';
        } catch (RequestException $e) {
            $profil = $dataBed = $header = $list = $detail = [];
        }
        return $this->renderpartial('rl_4_a',get_defined_vars());
    }

    protected function laporanMorbiditasRajal()
    {
        $request = Yii::$app->request;
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');
        $jenis_laporan = $request->get('jenis_laporan');
        $instalasi_id = $request->get('instalasi_id');
        try {
            $response = $this->_restRm->get('laporan-sirs/get-laporan',[
                'query' => [
                    'jenis_laporan' => $jenis_laporan,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'header' => true,
                    'instalasi_id' => $instalasi_id
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $response = $response['response'];
            $listGolJk = isset($response['columns']) ? $response['columns'] : [];
            $listHeader = isset($response['header']) ? $response['header'] : [];
            $columns = isset($response['columns']) ? $response['columns'] : [];

            foreach($listGolJk as $k => $v) {
                if($v['title'] == 'L' || $v['title'] == 'P'){
                    $listJk[] = $v['title'];
                }
            }

            foreach($listHeader as $k => $v){
                $header[] = $v;
            }
            $countJk = count($listJk) ?: 0;
            $countHeader = count($header) ?: 0;
            $profil = $response['profil'];
            $textBulan = $bulan && isset(DocoHelpers::$namaBulan[$bulan-1]) ? DocoHelpers::$namaBulan[$bulan-1] : '';
            
            $randString = DocoHelpers::generateRandomString();
            $yiiRestfulParams = [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'jenis_laporan' => $jenis_laporan,
                'instalasi_id' => $instalasi_id,
            ];

            $yiiRestfulParams['randString'] = $randString;
            Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        } catch (RequestException $e) {
            $profil = $dataBed = $header = $list = $detail = [];
        }
        return $this->renderpartial('rl_4_b',get_defined_vars());
    }

    /**
     * @todo Fungsi untuk menampilkan laporan kunjungan rawat jalan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    protected function laporanKunjunganRajal()
    {
        try {
            $request = Yii::$app->request;
            $bulan = $request->get('bulan');
            $tahun = $request->get('tahun');
            $jenis_laporan = $request->get('jenis_laporan');
            $textBulan = '';

            $restRm = $this->_restRm->get('laporan-sirs/get-laporan',[
                'query' => [
                    'jenis_laporan' => $jenis_laporan,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                ]
            ]);
            $body = json_decode($restRm->getBody(), true);
            $response = $body['response'];

            $data = $response['data'];
            $profil = $response['profil'];
            $textBulan = $bulan && isset(DocoHelpers::$namaBulan[$bulan-1]) ? DocoHelpers::$namaBulan[$bulan-1] : '';
        } catch (RequestException $e) {
            $header = [];
            $data = [];
        } catch (\Exception $e) {
            $header = [];
            $data = [];
        }

        return $this->renderpartial('rl_5_2', get_defined_vars());
    }/**
     * @todo Fungsi untuk menampilkan laporan sepuluh besar penyakit
     * @author Rizqi Fitrianto <rizqi@docotel.com>
     */
    protected function laporanSepuluhBesarPenyakit()
    {
        try {
            $request = Yii::$app->request;
            $bulan = $request->get('bulan');
            $tahun = $request->get('tahun');
            $jenis_laporan = $request->get('jenis_laporan');
            $textBulan = '';

            $restRm = $this->_restRm->get('laporan-sirs/get-laporan',[
                'query' => [
                    'jenis_laporan' => $jenis_laporan,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                ]
            ]);
            $body = json_decode($restRm->getBody(), true);
            $response = $body['response'];

            $data = $response['data'];
            $profil = $response['profil'];
            $textBulan = $bulan && isset(DocoHelpers::$namaBulan[$bulan-1]) ? DocoHelpers::$namaBulan[$bulan-1] : '';
        } catch (RequestException $e) {
            $header = [];
            $data = [];
        } catch (\Exception $e) {
            $header = [];
            $data = [];
        }

        return $this->renderpartial('rl_5_3', get_defined_vars());
    }

    /**
     * @todo Fungsi untuk menampilkan laporan pengunjung rawat jalan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    protected function laporanPengunjungRajal()
    {
        try {
            $request = Yii::$app->request;
            $bulan = $request->get('bulan');
            $tahun = $request->get('tahun');
            $jenis_laporan = $request->get('jenis_laporan');
            $textBulan = '';

            $restRm = $this->_restRm->get('laporan-sirs/get-laporan',[
                'query' => [
                    'jenis_laporan' => $jenis_laporan,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                ]
            ]);
            $body = json_decode($restRm->getBody(), true);
            $response = $body['response'];

            $data = $response['data'];
            $profil = $response['profil'];
            $textBulan = $bulan && isset(DocoHelpers::$namaBulan[$bulan-1]) ? DocoHelpers::$namaBulan[$bulan-1] : '';
        } catch (RequestException $e) {
            $data = [];
            $profil = [];
        } catch (\Exception $e) {
            $data = [];
            $profil = [];
        }

        return $this->renderpartial('rl_5_1', get_defined_vars());
    }

    /**
     * @todo get datatable rl4a
     * @return array
     * @author : Erlangga (erlangga@docotel.com)
     */
    public function actionGetDataMorbiditasRanap()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');
        $jenis_laporan = $request->get('jenis_laporan');
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        $draw = $request->get('draw', 1);
        $no = $request->get('start', 1);

        $data = [];
        $tmp = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        $response = $this->_restRm->get('laporan-sirs/get-data-morbiditas-ranap?jenis_laporan='.$jenis_laporan.'&bulan='.$bulan.'&tahun='.$tahun.'&header='.false.'&'.http_build_query($yiiRestfulParams), [
            'form_params' => []]);
        // $response = json_decode($response->getBody(),true);
        // $body = $response['response'];
        // $result['data'] = $body['data'];
        // $result['recordsTotal'] = $response['response']['_meta']['totalCount'];
        // $result['recordsFiltered'] = $response['response']['_meta']['totalCount'];
        return true;        
    }

    /**
     * @function get datatable rl4b
     * @return array
     * @author : Erlangga (erlangga@docotel.com)
     */
    public function actionGetDataMorbiditasRajal()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');
        $jenis_laporan = $request->get('jenis_laporan');
        $instalasi_id = $request->get('instalasi_id');
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $no = $request->get('start', 1);

        $data = [];
        $tmp = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restRm->get('laporan-sirs/get-laporan?jenis_laporan='.$jenis_laporan.'&bulan='.$bulan.'&tahun='.$tahun.'&header='.false.'&instalasi_id='.$instalasi_id.'&'.http_build_query($yiiRestfulParams), [
                'form_params' => []]);
            $response = json_decode($response->getBody(),true);
            $body = $response['response'];
            $result['data'] = $body['data'];
            $result['recordsTotal'] = $response['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $response['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            return $e->getMessage();
        }
    }

    public function actionShowPopupExcel()
    {
        $title = 'RL 4.a  laporan Morbiditas Ranap';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = [
            'bulan' => $request->get('bulan'),
            'tahun' => $request->get('tahun'),
            'jenis_laporan' => $request->get('jenis_laporan')
        ];
        // $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['randString'] = $randString;

        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modalExcel', get_defined_vars());
    }

    public function actionShowPopupExcelMorbiditas()
    {
        $title = 'RL 4.b laporan Morbiditas Rajal';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = [
            'bulan' => $request->get('bulan'),
            'tahun' => $request->get('tahun'),
            'jenis_laporan' => $request->get('jenis_laporan'),
            'instalasi_id' => $request->get('instalasi_id'),
        ];
        // $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['randString'] = $randString;

        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modalExcelMorbiditas', get_defined_vars());
    }

    public function actionProcessSyncExcel($randString)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($this->_restRm, [
            'url' => "laporan-sirs/sync-export-excel",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionProcessSyncExcelMorbiditas($randString)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($this->_restRm, [
            'url' => "laporan-sirs/sync-export-excel-morbiditas",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionProcessGetDataMorbiditasRajal($randString)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($this->_restRm, [
            'url' => "laporan-sirs/sync-data-laporan-morbiditas-rajal",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Laporan RL4.a Morbiditas Rawat Inap.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restRm->get('laporan-sirs/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }

    public function actionDownloadFileExcelMorbiditas()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Laporan RL4.b Morbiditas Rawat Jalan.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restRm->get('laporan-sirs/download-file-morbiditas', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }
}
