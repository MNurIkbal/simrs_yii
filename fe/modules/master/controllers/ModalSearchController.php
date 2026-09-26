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

class ModalSearchController extends DocoController
{
    protected $_title = "Modal Search";
    protected $_module = 'master/modal-search/';
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

    public function actionGetDataPendaftaran($sel_wrap = ".filter-form", $assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restMaster->get('pendaftaran/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                    'class' => 'btn btn-success btn-xs data-check',
                    'data-sel_wrap' => $sel_wrap,
                    'data-no_pendaftaran' => $value['no_pendaftaran'],
                    'data-nama_pasien' => $value['pasien_m']['nama_pasien'],
                    'data-no_rekam_medik' => $value['pasien_m']['no_rekam_medik'],
                    'title' => \Yii::t('fe', 'Klik'),
                ]);
                $value['tgl_pendaftaran'] = date("d M Y", strtotime($value['tgl_pendaftaran']));
                $value['rowNum'] = $no;
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

    public function actionModalPendaftaran($sel_wrap = ".filter-form", $assign_id = "")
    {
        return $this->renderPartial('modal-pendaftaran', get_defined_vars());
    }

    public function actionGetDataPembayaran($sel_wrap = ".filter-form", $assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restMaster->get('pembayaran-pelayanan/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                    'class' => 'btn btn-success btn-xs data-check',
                    'data-sel_wrap' => $sel_wrap,
                    'data-no_pembayaran' => $value['no_pembayaran'],
                    'data-tgl_pembayaran' => $value['tgl_pembayaran'],
                    'data-no_pendaftaran' => $value['pendaftaran_t']['no_pendaftaran'],
                    'data-nama_pasien' => $value['pasien_m']['nama_pasien'],
                    'data-no_rekam_medik' => $value['pasien_m']['no_rekam_medik'],
                    'title' => \Yii::t('fe', 'Klik'),
                ]);
                $value['tgl_pembayaran'] = date("d M Y", strtotime($value['tgl_pembayaran']));
                $value['pendaftaran_t']['tgl_pendaftaran'] = date("d M Y", strtotime($value['pendaftaran_t']['tgl_pendaftaran']));
                $value['rowNum'] = $no;
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

    public function actionModalPembayaran($sel_wrap = ".filter-form", $assign_id = "")
    {
        return $this->renderPartial('modal-pembayaran', get_defined_vars());
    }

    public function actionGetDataDokter($sel_wrap = ".filter-form", $assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restMaster->get('dokter/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                    'class' => 'btn btn-success btn-xs data-check',
                    'data-sel_wrap' => $sel_wrap,
                    'data-key' => $assign_id? $value['pegawai_id'] : $value['nama_pegawai'],
                    'data-label' => $value['nama_pegawai'],
                    'title' => \Yii::t('fe', 'Klik'),
                ]);
                $value['rowNum'] = $no; 
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

    public function actionModalDokter($sel_wrap = ".filter-form", $assign_id = "")
    {    
        return $this->renderPartial('modal-dokter', get_defined_vars());
    }

    public function actionGetDataPegawai($sel_wrap = ".filter-form", $assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restMaster->get('pegawai/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                    'class' => 'btn btn-success btn-xs data-check',
                    'data-sel_wrap' => $sel_wrap,
                    'data-key' => $assign_id? $value['pegawai_id'] : $value['nama_pegawai'],
                    'data-label' => $value['nama_pegawai'],
                    'title' => \Yii::t('fe', 'Klik'),
                ]);
                $value['rowNum'] = $no;
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

    public function actionModalPegawai($sel_wrap = ".filter-form", $assign_id = "")
    {    
        return $this->renderPartial('modal-pegawai', get_defined_vars());
    }

    public function actionGetDataShift($sel_wrap = ".filter-form", $assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restMaster->get('shift/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                    'class' => 'btn btn-success btn-xs data-check',
                    'data-sel_wrap' => $sel_wrap,
                    'data-key' => $assign_id? $value['shift_id'] : $value['shift_nama'],
                    'data-label' => $value['shift_nama'],
                    'title' => \Yii::t('fe', 'Klik'),
                ]);
                $value['rowNum'] = $no;
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

    public function actionModalShift($sel_wrap = ".filter-form", $assign_id = "")
    {    
        return $this->renderPartial('modal-shift', get_defined_vars());
    }

    public function actionGetDataStokObatAlkes($sel_wrap = ".filter-form", $assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restMaster->get('inf-stok-obat-alkes/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                    'class' => 'btn btn-success btn-xs data-check',
                    'data-sel_wrap' => $sel_wrap,
                    'data-key' => $assign_id? $value['obatalkes_id'] : $value['obatalkes_namalain'],
                    'data-label' => $value['obatalkes_namalain'],
                    'title' => \Yii::t('fe', 'Klik'),
                ]);
                $value['rowNum'] = $no;
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

    public function actionModalStokObatAlkes($sel_wrap = ".filter-form", $assign_id = "")
    {
        return $this->renderPartial('modal-stok-obat-alkes', get_defined_vars());
    }

    public function actionGetDataStokOpname($sel_wrap = ".filter-form", $assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restMaster->get('inf-stok-opname/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                    'class' => 'btn btn-success btn-xs data-check',
                    'data-sel_wrap' => $sel_wrap,
                    'data-key' => $assign_id? $value['stokopname_id'] : $value['nostokopname'],
                    'data-label' => $value['nostokopname'],
                    'title' => \Yii::t('fe', 'Klik'),
                ]);
                $value['rowNum'] = $no;
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

    public function actionModalStokOpname($sel_wrap = ".filter-form", $assign_id = "")
    {
        return $this->renderPartial('modal-stok-opname', get_defined_vars());
    }

    public function actionGetDataFormulirStokOpname($sel_wrap = ".filter-form", $assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restMaster->get('inf-formulir-stok-opname/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                    'class' => 'btn btn-success btn-xs data-check',
                    'data-sel_wrap' => $sel_wrap,
                    'data-key' => $assign_id? $value['formulirstokopname_id'] : $value['noformulir'],
                    'data-label' => $value['noformulir'],
                    'title' => \Yii::t('fe', 'Klik'),
                ]);
                $value['rowNum'] = $no;
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

    public function actionModalFormulirStokOpname($sel_wrap = ".filter-form", $assign_id = "")
    {
        return $this->renderPartial('modal-formulir-stok-opname', get_defined_vars());
    }

    public function actionGetDataBarang($sel_wrap = ".filter-form", $assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restMaster->get('barang/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                    'class' => 'btn btn-success btn-xs data-check',
                    'data-sel_wrap' => $sel_wrap,
                    'data-key' => $assign_id? $value['barang_id'] : $value['barang_nama'],
                    'data-label' => $value['barang_nama'],
                    'title' => \Yii::t('fe', 'Klik'),
                ]);
                $value['rowNum'] = $no;
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

    public function actionModalBarang($sel_wrap = ".filter-form", $assign_id = "")
    {
        return $this->renderPartial('modal-barang', get_defined_vars());
    }

    public function actionGetDataStokBarang($sel_wrap = ".filter-form", $assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restMaster->get('inf-stok-barang/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                    'class' => 'btn btn-success btn-xs data-check',
                    'data-sel_wrap' => $sel_wrap,
                    'data-key' => $assign_id? $value['barang_id'] : $value['barang_nama'],
                    'data-label' => $value['barang_nama'],
                    'title' => \Yii::t('fe', 'Klik'),
                ]);
                $value['rowNum'] = $no;
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

    public function actionModalStokBarang($sel_wrap = ".filter-form", $assign_id = "")
    {
        return $this->renderPartial('modal-stok-barang', get_defined_vars());
    }

    public function actionGetDataPesanBarang($sel_wrap = ".filter-form", $assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restMaster->get('pesan-barang/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                    'class' => 'btn btn-success btn-xs data-check',
                    'data-sel_wrap' => $sel_wrap,
                    'data-key' => $assign_id? $value['pesanbarang_id'] : $value['no_pemesanan'],
                    'data-label' => $value['no_pemesanan'],
                    'title' => \Yii::t('fe', 'Klik'),
                ]);

                $value['tgl_pesanbarang'] = date("j M Y", strtotime($value['tgl_pesanbarang']));
                $value['rowNum'] = $no;
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

    public function actionModalPesanBarang($sel_wrap = ".filter-form", $assign_id = "")
    {
        return $this->renderPartial('modal-pesan-barang', get_defined_vars());
    }

    public function actionGetDataMutasiBarang($sel_wrap = ".filter-form", $assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restMaster->get('mutasi-barang/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                    'class' => 'btn btn-success btn-xs data-check',
                    'data-sel_wrap' => $sel_wrap,
                    'data-key' => $assign_id? $value['mutasibarang_id'] : $value['nomutasi_barang'],
                    'data-label' => $value['nomutasi_barang'],
                    'title' => \Yii::t('fe', 'Klik'),
                ]);

                $value['tgl_mutasibarang'] = date("j M Y", strtotime($value['tgl_mutasibarang']));
                $value['rowNum'] = $no;
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

    public function actionModalMutasiBarang($sel_wrap = ".filter-form", $assign_id = "")
    {
        return $this->renderPartial('modal-mutasi-barang', get_defined_vars());
    }

    public function actionGetDataDiagnosa($sel_wrap = ".filter-form", $assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restMaster->get('diagnosa/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                    'class' => 'btn btn-success btn-xs data-check',
                    'data-sel_wrap' => $sel_wrap,
                    'data-key' => $assign_id? $value['diagnosa_id'] : $value['diagnosa_nama'],
                    'data-label' => $value['diagnosa_kode']." - ".$value['diagnosa_nama'],
                    'title' => \Yii::t('fe', 'Klik'),
                ]);

                $value['rowNum'] = $no;
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

    public function actionModalDiagnosa($sel_wrap = ".filter-form", $assign_id = "")
    {
        return $this->renderPartial('modal-diagnosa', get_defined_vars());
    }
}
