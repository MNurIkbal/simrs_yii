<?php

/**
 * @Author: Sigit
 * @Date:   2018-12-20 15:56:06
 */

namespace Doco\gizi\controllers;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

use GuzzleHttp\Exception\RequestException;

use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class LaporanPermintaanMakanController extends DocoController
{
    /**
     * @todo Protected vars
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    protected $_restGizi;
    protected $allowAction = ['*'];

    /**
     * @todo Init function
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function init()
    {
        parent::init();
        $this->_restGizi = Yii::$app->docoRest->gizi;
    }

    /**
     * @todo Behaviors function
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    /**
     * @todo Fungsi untuk menampilkan halaman awal laporan permintaan makan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionIndex()
    {
        try {

            return Yii::$app->docoPlugin->execute($this,'lap_permintaan_makan');
    
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    /**
     * @todo Action untuk melakukan proses export pdf
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionExportPdf()
    {
        try {
            $docoVars = Yii::$app->docoVars;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/informasi-permintaan-makan.pdf";
            $ruangan_id = $docoVars->workspace('ruangan_id');
            $user_identity = Yii::$app->session->get('user_identity');
            $nama_pegawai = $user_identity['nama_pegawai'];

            $restGizi = $this->_restGizi->get('laporan-permintaan-makan/export-pdf?ruangan_id='.$ruangan_id.'&nama_pegawai='.$nama_pegawai.'&'.http_build_query($yiiRestfulParams), [
                'save_to' => $path,
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        }
    }

    /**
     * @todo Action untuk melakukan proses export excel
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionExportExcel()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $url = 'laporan-permintaan-makan/export-excel?'.http_build_query($yiiRestfulParams);
            $path = Yii::getAlias("@download") . "/laporan-permintaan-makan.xlsx";
            $restGizi = $this->_restGizi->get($url,[
                'save_to' => $path
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (RequestException $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        }
    }

    /**
     * @todo Fungsi untuk mendapatkan data permintaan makan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDataPermintaanMakan()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $draw = $request->get('draw', 1);
            $no = $request->get('start', 1);
            $data = [];

            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;

            $restGizi = $this->_restGizi->get('laporan-permintaan-makan/get-data-permintaan-makan?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($restGizi->getBody(), true);
            $data = $body['response']['data'];

            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    $no++;
                    $value['no'] = $no;
                    $value['tgl_permintaanmakan'] = DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($value['tgl_permintaanmakan'])), false, false);
                    $value['jenisdiet_nama'] = !empty($value['jenisdiet_nama']) ? $value['jenisdiet_nama'] : '-';
                    $value['diagnosa'] = !empty($value['diagnosa']) ? $value['diagnosa'] : '-';
                    $value['riwayat_alergi'] = !empty($value['riwayat_alergi']) ? $value['riwayat_alergi'] : '-';
                    $value['tanggal_lahir'] = date('d M Y',strtotime($value['tanggal_lahir']));
                    $data[$key] = $value;
                }

                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
                $result['counters'] = $body['response']['counter'];
                return $result;
            }
            else {
                $result['data'] = $data;
                $result['recordsTotal'] = 0;
                $result['recordsFiltered'] = 0;
                $result['counters'] = [
                    'count_jenis' => 0,
                    'count_makanan' => 0,
                    'count_jumlah' => 0,
                ];
                return $result;
            }
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionGetDataPermintaanMakanMhg()
    {
        // try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $draw = $request->get('draw', 1);
            $no = $request->get('start', 1);
            $data = [];

            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;


            $restGizi = $this->_restGizi->get('laporan-permintaan-makan/get-data-permintaan-makan?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($restGizi->getBody(), true);
            $data = $body['response']['data'];
            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    $no++;
                    $value['LOS'] = ' - ';
                    $value['no'] = $no;
                    $value['tgl_permintaanmakan'] = isset($value['tgl_permintaanmakan'])? DocoHelpers::convDateTime(date('Y-m-d H:i', strtotime($value['tgl_permintaanmakan']))) : ' - ';
                    if(isset($value['tgl_admisi'])){
                        $firstDate = date_create(Date('Y-m-d', strtotime($value['tgl_admisi'])));
                        $lastDate = date_create(Date('Y-m-d'));
                        $diff = date_diff($firstDate, $lastDate);
                        $LOS = $diff->days > 0 ? $diff->days : 1;                        
                        $value['LOS'] = ($diff->days > 0 ? $diff->days : 1).' D';                 }
                    $value['tgl_admisi'] = isset($value['tgl_admisi'])? DocoHelpers::convDateTime(date('Y-m-d H:i', strtotime($value['tgl_admisi']))) : ' - ';
                    $value['umur'] = isset($value['umur']) ? DocoHelpers::getUmur($value['tanggal_lahir'], true, false).' Y' : ' - ';
                    $value['new_diet'] =  $value['remark'] =  $value['BF'] = $value['S1'] =  $value['LN'] = $value['LN'] = $value['S2'] = $value['DN'] = $value['S3'] = $value['SP'] = $value['EX'] ='';
                    $value['no_rekam_medik'] = isset($value['no_rekam_medik']) ? $value['no_rekam_medik'] : ' - ';
                    $value['nama_pasien'] = isset($value['nama_pasien']) ? $value['nama_pasien'] : ' - ';
                    $value['kelaspelayanan_nama'] = isset($value['kelaspelayanan_nama']) ? $value['kelaspelayanan_nama'] : ' - ';
                    $value['kamarruangan_nokamar'] = $value['kamarruangan_nokamar'] != null ? $value['kamarruangan_nokamar'] : ' - ';
                    $value['no_tempattidur'] = $value['no_tempattidur'] != null ? $value['no_tempattidur'] : ' - ';
                    $value['no_bed'] = $value['kamarruangan_nokamar'].' / '.$value['no_tempattidur'];
                    $value['dokter_dpjp'] = isset($value['dokter_dpjp']) ? $value['dokter_dpjp'] : ' - ';
                    $value['menu_makan'] = isset($value['menu_makan']) ? $value['menu_makan'] : ' - ';
                    $value['jenisdiet_nama'] = isset($value['jenisdiet_nama']) ? $value['jenisdiet_nama'] : ' - ';
                    $value['diagnosa'] = $value['diagnosa'] != null ? isset($value['diagnosa']['nama']) ? $value['diagnosa']['nama'] : '' : '-';

                    $data[$key] = $value;
                }
                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
                return $result;
            }
            else {
                $result['data'] = $data;
                $result['recordsTotal'] = 0;
                $result['recordsFiltered'] = 0;
                return $result;
            }

        // } catch (RequestException $e) {
        //     return DocoHelpers::dataTabelsException($e->getMessage());
        // } catch (\Exception $e) {
        //     return DocoHelpers::dataTabelsException($e->getMessage());
        // }
    }

    /**
     * @todo Fungsi untuk mendapatkan data count
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDataCount()
    {
        try {
            $restGizi = $this->_restGizi->get('laporan-permintaan-makan/get-data-count');
            $body = json_decode($restGizi->getBody(), true);
            $data = $body['response'];

            return json_encode($data);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }
}