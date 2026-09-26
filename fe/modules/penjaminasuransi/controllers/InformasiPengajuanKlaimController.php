<?php 

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-09-12 16:24:44
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2019-02-19 14:37:29
 */

namespace Doco\penjaminasuransi\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DHtml;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use app\modules\penjaminasuransi\models\InformasiPengajuanForm;
use app\components\Services\Penjamin\GetInitFilterService;

class InformasiPengajuanKlaimController extends DocoController
{
    protected $_title;
    protected $_restRajal;
    protected $_restMaster;
    protected $_restPenjaminAsuransi;
    protected $_module = '/penjamin-asuransi/informasi-pengajuan-klaim/';

    public function init()
    {
        parent::init();
        $this->_title = Yii::t('fe', 'Informasi Pengajuan Klaim');
        $this->_restRajal = Yii::$app->docoRest->rajal;
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restPenjaminAsuransi = Yii::$app->docoRest->penjaminasuransi;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $resultInitFilter = (new GetInitFilterService)->execute();
        $response = $resultInitFilter;
        $response['penjamin'] = [];
        $penjamin = ArrayHelper::map($response['penjamin'],'penjamin_id','penjamin_nama');
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if (isset($yiiRestfulParams['advanced-filter']['tgl_pengajuanklaim'])) {
            $tgl_pengajuanklaim_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_pengajuanklaim']);
            $tgl_awal = $tgl_pengajuanklaim_range[0];
            $tgl_akhir = $tgl_pengajuanklaim_range[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $yiiRestfulParams['advanced-filter']['tgl_pengajuanklaim_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_pengajuanklaim_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_pengajuanklaim']);
        }

        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restPenjaminAsuransi->get('informasi-pengajuan-klaim/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            $bodyDatas = ArrayHelper::getValue($body,'response.data');
            foreach ($bodyDatas as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pengajuanklaim_id']);
                $value['primary'] = $primaryKey;
                unset($value['pengajuanklaim_id']);
                $value['rowNum'] = $no;
                $value['tgl_pengajuanklaim'] = date('d M Y', strtotime($value['tgl_pengajuanklaim']));
                $value['tgl_jatuhtempo'] = date('d M Y', strtotime($value['tgl_jatuhtempo']));
                $value['total_piutang'] = 'Rp. '.DocoHelpers::formatNumber($value['total_piutang'], 0);
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = ArrayHelper::getValue($body,'response._meta.totalCount');
            $result['recordsFiltered'] = ArrayHelper::getValue($body,'response._meta.totalCount');
            $result['summary'] = ArrayHelper::getValue($body,'response.additional_data.summary');
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetPenjamin($assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $params = '';
        if ($request->post()) {
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
            $params = '?id='.$parent_label;
        }

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restPenjaminAsuransi->get('allow/get-list-penjamin'. $params);
            $body = json_decode($response->getBody(), True);
            
            foreach ($body['response']['data'] as $value)
                $result['output'][] = [
                    'id' => $value['penjamin_id'],
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

    public function actionGetCarabayar($assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];
        
        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restPenjaminAsuransi->get('allow/get-list-carabayar?id='.$parent_label);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value)
                $result['output'][] = [
                    'id' => $value['carabayar_id'],
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

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request                    = Yii::$app->request;

        $yiiRestfulParams          = DocoDatatableHelper::convertToRestfulParams($request->get());
        // $yiiRestfulParams['title'] = Dhtml::getTitleMenu();
        $title                     = "Informasi Pengajuan Klaim";

        $url              = 'informasi-pengajuan-klaim/export-excel?'.http_build_query($yiiRestfulParams);
        $path             = Yii::getAlias("@download") . "/" . $title . ".xlsx";
        
        try {
            $this->_restPenjaminAsuransi->get($url,[
                'save_to' => $path,
            ]);

            return DocoHelpers::downloadFile($path, true);
        } catch (RequestException $e) {
            return ['error' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request                    = Yii::$app->request;
        
        try {
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path             = Yii::getAlias("@download") . "/Informasi Pengajuan Klaim.pdf";
            
            $this->_restPenjaminAsuransi->get('informasi-pengajuan-klaim/export-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restPenjaminAsuransi->delete('informasi-pengajuan-klaim/delete',[
                'query' => [
                    'id' => $id
                ]
            ]);

            $body = json_decode($response->getBody(), true);
            
            return DocoHelpers::response($body['response']);
        } catch (RequestException $e) {
            return ['error' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function actionDetail($id)
    {
        $title = $this->_title;
        $id_dec = DocoHelpers::decrypt($id);
        $model = new InformasiPengajuanForm;
        try {
            $response = $this->_restPenjaminAsuransi->get('informasi-pengajuan-klaim/detail',[
                'query' => [
                    'id' => $id_dec
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $response = $response['response'];
            $header = isset($response['header']) ? $response['header'] : [];
            $model->total_pengajuan = isset($header['total_piutang']) ? $header['total_piutang'] : 0; 
            $model->tgl_jatuhtempo = !empty($header['tgl_jatuhtempo']) 
                                ? date('d-M-Y',strtotime($header['tgl_jatuhtempo'])) : null; 
            $model->catatan = !empty($header['catatan']) ? $header['catatan'] : null; 

        } catch (RequestException $e) {
            $header = [];
        }
        return $this->render('detail', get_defined_vars());
    }

    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $model = new InformasiPengajuanForm;
        $model->load($request->post());
        try {
            if ($model->validate()) {
                $response = $this->_restPenjaminAsuransi->post('informasi-pengajuan-klaim/update-pengajuan',[
                    'query' => [
                        'id' => $id
                    ],
                    'form_params' => $model->attributes
                ]);
                $body = json_decode($response->getBody(), true);
                return DocoHelpers::response($body['response']);
            } else {
                return DocoHelpers::response($model->errors,422,'InformasiPengajuanForm');
            }
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    public function actionGetDataPengajuan($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filters = DocoDatatableHelper::convertToRestfulParams($request->get());
        $filters['id'] = DocoHelpers::decrypt($id);
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restPenjaminAsuransi->get('informasi-pengajuan-klaim/get-data-pengajuan', [
                'query' => $filters
            ]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pengajuanklaimdetail_id']);
                $pendaftaranId = DocoHelpers::encrypt($value['pendaftaran_id']);
                $pembayaranId = DocoHelpers::encrypt($value['pembayaran_id']);
                $value['primary'] = $primaryKey;
                $value['pendaftaran_id'] = $pendaftaranId;
                $value['pembayaran_id'] = $pembayaranId;
                unset($value['pengajuanklaimdetail_id']);
                $value['rowNum'] = $no;
                $value['tgl_pendaftaran'] = date('d-M-Y', strtotime($value['tgl_pendaftaran']));
                $value['tglpasienpulang'] = date('d-M-Y', strtotime($value['tglpasienpulang']));
                $value['jumlah_bayar_label'] = 'Rp. '.DocoHelpers::formatNumber($value['jumlah_bayar'], 0);
                $value['jumlah_piutang_label'] = 'Rp. '.DocoHelpers::formatNumber($value['jumlah_piutang'], 0);
                $value['jumlah_telahbayar_label'] = 'Rp. '.DocoHelpers::formatNumber($value['jumlah_telahbayar'], 0);
                $value['jumlah_sisapiutang_label'] = 'Rp. '.DocoHelpers::formatNumber($value['jumlah_piutang'], 0);
                $value['total_tarifrs_label'] = 'Rp. '.DocoHelpers::formatNumber($value['total_tarifrs'], 0);
                $value['total_discountpembayaran'] = 'Rp. '.DocoHelpers::formatNumber($value['total_discountpembayaran'], 0);
                $value['total_tagihan_label'] = 'Rp. '.DocoHelpers::formatNumber($value['total_tagihan'], 0);
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

    public function actionExportPengajuanPdf($id)
    {
        $id = DocoHelpers::decrypt($id);

        $path = Yii::getAlias("@download")."/Detail Rincian.pdf";

        try {
            $this->_restPenjaminAsuransi->get('informasi-pengajuan-klaim/list-pengajuan-pdf', 
                [
                    'query' => [
                        'id' => $id
                    ],
                    'save_to' => $path
                ]
            );

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    public function actionExportPengajuanExcel($id)
    {
        $id = DocoHelpers::decrypt($id);

        $url = 'informasi-pengajuan-klaim/list-pengajuan-excel';
        $path = Yii::getAlias("@download") . "/Informasi Detail Pengajuan Klaim.xlsx";

        try {
            $response = $this->_restPenjaminAsuransi->get($url, [
                'save_to' => $path,
                'query'   => ['id' => $id]
            ]);

            $body = json_decode($response->getBody(), true);

            return DocoHelpers::downloadFile($path, true);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    public function actionPrintRincian($pendaftaran_id)
    {
        $id = DocoHelpers::decrypt($pendaftaran_id);
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download")."/detail-rincian.pdf";
        try {
            $response = $this->_restPenjaminAsuransi->get('informasi-pengajuan-klaim/print-rincian', 
                [
                    'query' => [
                        'id' => $id
                    ],
                    'save_to' => $path
                ]
            );
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionBatalPengajuanPasien($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restPenjaminAsuransi->delete('informasi-pengajuan-klaim/batal-pengajuan-pasien',[
                'query' => [
                    'id' => $id
                ],
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response($body['response']);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionTambahPasien($id)
    {
        $title = $this->_title;
        return $this->renderAjax('form', get_defined_vars());
    }

    public function actionGetDataListPasien($id)
    {
        $id = DocoHelpers::decrypt($id);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filters = DocoDatatableHelper::convertToRestfulParams($request->get());
        $filters['id'] = $id;
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restPenjaminAsuransi->get('informasi-pengajuan-klaim/get-data-list-pasien', [
                'query' => $filters
            ]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['no_pembayaran']);
                $value['pasienadmisi_id'] = $value['pasienadmisi_id'] ? $value['pasienadmisi_id'] : 0;
                $value['admisi'] = DocoHelpers::encrypt($value['pasienadmisi_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['tgl_pendaftaran'] = date('d M Y', strtotime($value['tgl_pendaftaran']));
                $value['tglpasienpulang'] = !empty($value['tglpasienpulang']) ? date('d M Y', strtotime($value['tglpasienpulang'])) : '';
                $value['total_tagihan_label'] = DocoHelpers::rupiahDisplay($value['total_tagihan']);
                $value['total_sdh_bayar_label'] = DocoHelpers::rupiahDisplay($value['total_sdh_bayar']);
                $value['total_asuransi_label'] = DocoHelpers::rupiahDisplay($value['total_asuransi']);
                $value['total_sisa_tagihan_label'] = DocoHelpers::rupiahDisplay($value['total_sisa_tagihan']);
                $value['jumlah_inacbg_label'] = DocoHelpers::rupiahDisplay($value['jumlah_inacbg']);
                $value['total_pengajuan'] = $value['carabayar_id'] == 6 ? $value['jumlah_inacbg'] : $value['total_asuransi'];
                $value['total_pengajuan_label'] = $value['carabayar_id'] == 6 
                                ? $value['jumlah_inacbg'] : $value['total_asuransi'];
                $value['total_pengajuan_label'] = DocoHelpers::rupiahDisplay($value['total_pengajuan_label']);
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

    public function actionSimpanTambahPasien($id)
    {
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        try {
            $response = $this->_restPenjaminAsuransi->post('informasi-pengajuan-klaim/simpan-tambah-pasien',[
                'query' => [
                    'id' => $id
                ],
                'form_params' => [
                    'data_pengajuan' => $request->post('data_pengajuan')
                ]
            ]);
            $body = json_decode($response->getBody(),true);
            return DocoHelpers::response($body);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}
