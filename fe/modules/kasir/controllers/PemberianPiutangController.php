<?php 

/**
 * @author Budi
 * @todo Transaksi Pemberian Piutang
 * @copyright 12 Desember 2019
 */

namespace Doco\kasir\controllers;

use Yii;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoSelect2Trait;
use Doco\kasir\models\PemberianPiutangForm;

class PemberianPiutangController extends DocoController
{
    use DocoSelect2Trait;
    
    protected $_title = "Transaksi Pemberian Piutang";
    protected $_module = '/kasir/pemberian-piutang';
    protected $_restKasir; 
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restKasir = Yii::$app->docoRest->kasir; 
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actions()
    {
        /**
         * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
         * 
         * DATA ATTRIBUTE YANG BISA DIGUKANAN
         * 
         * --------------------------------------------------
         * data_name : nama kolom yang akan dimunculkan pada value text select2 (MAX 4 DATA)
         * keyField : sebagai id pada dropdown select2
         */
        return [
            'get-data-karyawan' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restKasir,
                'serviceAction' => 'pemberian-piutang/get-data-karyawan',
                'data_name' => [
                    'nomorindukpegawai',
                    'nama_pegawai'
                ],
                'keyField' => 'pegawai_id'
            ],
            'get-data-pendaftaran-reseptur' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restKasir,
                'serviceAction' => 'pemberian-piutang/get-data-pendaftaran-reseptur',
                'data_name' => [
                    'no_pendaftaran',
                    'nama_pasien'
                ],
                'keyField' => 'pendaftaran_id'
            ]
        ];
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $title = $this->_title;
        $model = new PemberianPiutangForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $model->tgl_pemberianpiutang = date('d-m-Y');
        if($request->post()) {
            $isCheckKaryawan = $request->post()['PemberianPiutangForm']['is_checkpegawai'];
            $model->load($request->post());
            $model->pendaftaran_id = $request->post('pendaftaran_id_hidden');
            $model->total_sisapiutang = DocoHelpers::convertToAngka($model->total_piutang);
            $model->total_tagihan = $request->post('total_tagihan');
            $model->penjualanresep_id = $request->post('penjualanresep_id');
            if($isCheckKaryawan == 0){
                $model->pegawaimengetahui_id = 1;
            }
            if($model->validate()) {
                if($isCheckKaryawan == 0){
                    $model->pegawaimengetahui_id = null;
                }
                if (!empty($model->penjualanresep_id)) {
                    $model->pendaftaran_id = null;
                }
                try {
                    $response = $this->_restKasir->post('pemberian-piutang/save', [
                        'form_params' => $model->attributes
                    ]);
                    $body = json_decode($response->getBody(), true);
                    return DocoHelpers::response($body, false, 'PemberianPiutangForm');
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            }
            else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }
        return $this->render('index', get_defined_vars());
    }

    public function actionGetPendaftaran()
    {
        $request = Yii::$app->request;
        $page = $request->get('page');
        $listData = [];
        $limit = 10;
        $offset = ($page-1)*10;
        
        try {
            $requests = $this->_restKasir->get('allow/get-data-pendaftaran', [
                'query' => [
                    'term' => $request->get('q'),
                    'page' => $page,
                    'offset' => $offset,
                    'limit' => $limit
                ]
            ]);
            $response = json_decode($requests->getBody(), true);
            $data = isset($response['response']) ? $response['response'] : [];
            $listData = [];
            foreach ($data as $key => $value) {
                $listData[] = [
                    'id' => $value['pendaftaran_id'],
                    'text' => $value['no_pendaftaran'].' / '.$value['no_rekam_medik'].' / '.$value['nama_pasien'],
                    'no_pendaftaran' => $value['no_pendaftaran'],
                    'no_rekam_medik' => $value['no_rekam_medik'],
                    'nama_pasien' => $value['nama_pasien'],
                    'total_tagihan' => $value['total_tagihan'],
                    'piutang_sudahbayar' => $value['piutang_sudahbayar'],
                    'total_piutang' => $value['total_piutang'],
                    'tanggal_lahir' => !empty($value['tanggal_lahir']) ? date('d-m-Y', strtotime($value['tanggal_lahir'])).' / '.$value['umur'] : '-',
                    'umur' => $value['umur'],
                    'tgl_pendaftaran' => date('d-m-Y', strtotime($value['tgl_pendaftaran'])),
                    'tgl_pendaftaran_value' => $value['tgl_pendaftaran'],
                    'adm_persen' => isset($value['adm_persen']) ? $value['adm_persen'] : 0,
                    'tarif_max' => isset($value['tarif_max']) ? $value['tarif_max'] : 0,
                    'tagihan_ranap' => isset($value['tagihan_ranap']) ? $value['tagihan_ranap'] : 0,
                    'uang_muka' => $value['uang_muka'],
                    'is_pembulatankeatas' => isset($value['is_pembulatankeatas']) ? $value['is_pembulatankeatas'] : false,
                    'satuanpembulatan' => isset($value['satuanpembulatan']) ? $value['satuanpembulatan'] : 0,
                ];
            }

        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $listData['message'] = $e->getMessage();
        } catch (\Exception $e) {
            Yii::info($e->getMessage());
            $listData['message'] = $e->getMessage();
        }

        return DocoHelpers::response([
            'result' => $listData,
            'total_count' => count($listData),
            'incomplete_results' => false,
            'pagination' => [ 'more' => count($listData) === $limit ? true : false ]
        ]);
    }

    public function actionGetDataPegawai()
    {
        $listData = [];
        try {
            $payloadRequest = Yii::$app->request->get();
            $requests = $this->_restKasir->get('allow/get-data-pegawai', [
                'query' => [
                    'q' => $payloadRequest['term'],
                ]
            ]);
            $response = json_decode($requests->getBody(), true);
            $data = isset($response['response']) ? $response['response'] : [];
            $listData = [];
            foreach ($data as $key => $value) {
                $listData[] = [
                    'id' => $value['pegawai_id'],
                    'text' => $value['nomorindukpegawai'].' / '.$value['nama_pegawai'],
                ];
            }
            
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $listData['message'] = $e->getMessage();
        } catch (\Exception $e) {
            Yii::info($e->getMessage());
            $listData['message'] = $e->getMessage();
        }

        return DocoHelpers::response([
            'result' => $listData
        ]);
    }

    public function actionGetTagihanRanap() 
    {
        $results = [];
        try {
            $request = Yii::$app->request;
            $response = $this->_restKasir->get('pemberian-piutang/get-tagihan-ranap', [
                'query' => [
                    'pendaftaran_id' => $request->get('pendaftaran_id'),
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            return $body['response'];
        } catch (Exception $e) {
            Yii::info($e->getMessage());
            $results['message'] = $e->getMessage();
        }
    }
}