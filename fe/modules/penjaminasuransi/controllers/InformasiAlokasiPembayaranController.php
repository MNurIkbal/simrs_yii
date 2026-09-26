<?php 

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
use Doco\penjaminasuransi\models\TransaksiAlokasiForm;
use app\components\Services\Penjamin\GetInitFilterService;

class InformasiAlokasiPembayaranController extends DocoController
{
    protected $_title;
    protected $_restMaster;
    protected $_restPenjaminAsuransi;
    protected $_module = '/penjamin-asuransi/informasi-alokasi-pembayaran/';

    public function init()
    {
        parent::init();
        $this->_title = Yii::t('fe', 'Informasi Alokasi Pembayaran');
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restPenjaminAsuransi = Yii::$app->docoRest->penjaminasuransi;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $response = (new GetInitFilterService)->execute();
        $response['penjamin'] = [];
        $penjamin = ArrayHelper::map($response['penjamin'],'penjamin_id','penjamin_nama');
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if (isset($yiiRestfulParams['advanced-filter']['tgl_terimabayarklaim'])) {
            $tgl_pengajuanklaim_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_terimabayarklaim']);
            $tgl_awal = $tgl_pengajuanklaim_range[0];
            $tgl_akhir = $tgl_pengajuanklaim_range[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $yiiRestfulParams['advanced-filter']['tgl_terimabayarklaim_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_terimabayarklaim_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_terimabayarklaim']);
        }

        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restPenjaminAsuransi->get('informasi-alokasi-pembayaran/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pembayaranalokasi_id']);
                $value['primary'] = $primaryKey;
                unset($value['pembayaranalokasi_id']);
                $value['rowNum'] = $no;
                $value['tgl_pengajuanklaim'] = date('d M Y', strtotime($value['tgl_pengajuanklaim']));
                $value['tgl_pembayaranalokasi'] = date('d M Y', strtotime($value['tgl_pembayaranalokasi']));
                $value['tgl_terimabayarklaim'] = date('d M Y', strtotime($value['tgl_terimabayarklaim']));
                $value['sisa'] = DocoHelpers::formatNumber($value['sisa']);
                $value['total_pembayaran'] = DocoHelpers::formatNumber($value['total_pembayaran']);
                $value['total_pengajuan'] = DocoHelpers::formatNumber($value['total_pengajuan']);
                $value['jumlah_pembayaran'] = DocoHelpers::formatNumber($value['jumlah_pembayaran']);
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

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restPenjaminAsuransi->delete('informasi-alokasi-pembayaran/delete',[
                'query' => [
                    'id' => $id
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response($body);
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'messages' => $e->getMessage()
            ],500);
        } catch (\Exception $e) {
            return DocoHelpers::response([
                'messages' => $e->getMessage()
            ],500);
        }
    }

    public function actionDetail($id)
    {
        $title = $this->_title;
        $model = new TransaksiAlokasiForm;
        try {
            $response = $this->_restPenjaminAsuransi->get('informasi-alokasi-pembayaran/get-data-transaksi',[
                'query' => [
                    'id' => DocoHelpers::decrypt($id)
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $header = $response['response']['header'];
            $model->total_pengajuan = isset($header['total_pengajuan']) 
                ? $header['total_pengajuan'] : 0;
            $model->jumlah_pembayaran = isset($header['jumlah_pembayaran']) 
                ? $header['jumlah_pembayaran'] : 0;
            $model->total_terbayar = isset($header['total_pembayaran']) 
                ? $header['total_pembayaran'] - $model->jumlah_pembayaran : 0;
            $model->sisa_piutang = ($model->total_pengajuan - $model->total_terbayar) - $model->jumlah_pembayaran;
        } catch (RequestException $e) {
            $header = [];
        }

        return $this->render('detail',get_defined_vars());
    }

    public function actionGetDetailAlokasi($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $filter['id'] = DocoHelpers::decrypt($id);
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restPenjaminAsuransi->get('informasi-alokasi-pembayaran/get-data-alokasi', [
                'form_params' => [],
                'query' => $filter
            ]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pendaftaran_id']);
                $rawData = $value;
                $rawData['rowNum'] = $no;
                $bayarAlokasi = $rawData['bayar_alokasi'];
                $rawData['nosep'] = $value['nosep'] ? $value['nosep'] : '-';
                $html = '<div class="input-group">';
                    $html .= '<span class="input-group-addon" title="Select date &amp; time">';
                        $html .= '<span>Rp.</span>';
                    $html .= '</span>';
                    $html .= Html::textInput('jumlah_bayar[]',null,[
                                'class' => 'form-control doco-number text-right total-alokasi',
                                'disabled' => true
                            ]);
                $html .= '</div>';
                $rawData['label_bayar'] = $html;
                $rawData['nama_pasien'] = $value['no_rekam_medik'] .' - '. $value['nama_pasien'];
                $rawData['tgl_pendaftaran'] = date('d-M-Y',strtotime($value['tgl_pendaftaran']));
                $rawData['tglpasienpulang'] = date('d-M-Y',strtotime($value['tglpasienpulang']));
                $rawData['total_tagihan'] = DocoHelpers::formatNumber($value['total_tagihan']);

                $rawData['jumlah_telahbayar'] = DocoHelpers::formatNumber($value['jumlah_telahbayar'] - $bayarAlokasi);
                $rawData['jumlah_bayar'] = DocoHelpers::formatNumber($value['jumlah_bayar'] - $bayarAlokasi);
                $rawData['jumlah_sisapiutang'] = DocoHelpers::formatNumber(($value['jumlah_piutang'] - $value['jumlah_bayar']) + $bayarAlokasi);
                $rawData['sisa_piutang'] = ($value['jumlah_piutang'] - $value['jumlah_bayar']) + $bayarAlokasi;
                $rawData['jumlah_piutang'] = DocoHelpers::formatNumber($value['jumlah_piutang']);
                $rawData['piutang'] = $value['jumlah_piutang'];
                $rawData['bayar'] = $value['jumlah_bayar'] - $bayarAlokasi;
                $rawData['no_pembayaran'] = $value['no_pembayaran'];
                $data[$key] = $rawData;
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

    public function actionGetPembayaran($no_pembayaran)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [];
            try {
                $response = $this->_restPenjaminAsuransi->get('informasi-alokasi-pembayaran/get-pembayaran?no_pembayaran='.$no_pembayaran);
                $response = json_decode($response->getBody(),true);

                return $response['response']['data'];
            } catch (RequestException $e) {
                $result['error'] = $e->getMessage();
                return $result;
            } 
    }

    public function actionSave($id)
    {
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $model = new TransaksiAlokasiForm;
        $model->load($request->post());
        $model->total_alokasi = $request->post('total_alokasi',0);
        $model->detail_pembayaran = $request->post('data_alokasi',[]);
        $model->no_pengajuan = 'update';
        $model->no_pembayaran = 'update';
        if ($model->validate()) {
            try {
                $response = $this->_restPenjaminAsuransi->put('informasi-alokasi-pembayaran/update',[
                    'form_params' => $model->attributes,
                    'query' => [
                        'id' => $id
                    ]
                ]);
                $response = json_decode($response->getBody(),true);
                return DocoHelpers::response($response,false,'TransaksiAlokasiForm');
            } catch (RequestException $e) {
                return DocoHelpers::response([
                    'messages' => $e->getMessage()
                ], 500);
            }
        } else {
            return DocoHelpers::response($model->errors, 422, 'TransaksiAlokasiForm');
        }
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if (isset($yiiRestfulParams['advanced-filter']['tgl_terimabayarklaim'])) {
            $tgl_pengajuanklaim_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_terimabayarklaim']);
            $tgl_awal = $tgl_pengajuanklaim_range[0];
            $tgl_akhir = $tgl_pengajuanklaim_range[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $yiiRestfulParams['advanced-filter']['tgl_terimabayarklaim_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_terimabayarklaim_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_terimabayarklaim']);
        }
        $url = 'informasi-alokasi-pembayaran/export-excel?'.http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/informasi-alokasi-pembayaran.xlsx";
        try {
            $response = $this->_restPenjaminAsuransi->get($url,[
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionCetakTransaksi($id)
    {
        $request = Yii::$app->request;
        try {
            $path = Yii::getAlias("@download") . "/informasi-alokasi-pembayaran.pdf";
            $response = $this->_restPenjaminAsuransi->get('informasi-alokasi-pembayaran/cetak-transaksi',[
                'query' => [
                    'id' => DocoHelpers::decrypt($id)
                ],
                'save_to' => $path
            ]);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        try {
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            if (isset($yiiRestfulParams['advanced-filter']['tgl_terimabayarklaim'])) {
                $tgl_pengajuanklaim_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_terimabayarklaim']);
                $tgl_awal = $tgl_pengajuanklaim_range[0];
                $tgl_akhir = $tgl_pengajuanklaim_range[1];
                $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
                $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
                $yiiRestfulParams['advanced-filter']['tgl_terimabayarklaim_awal'] = $tgl_awal_format;
                $yiiRestfulParams['advanced-filter']['tgl_terimabayarklaim_akhir'] = $tgl_akhir_format;
                unset($yiiRestfulParams['advanced-filter']['tgl_terimabayarklaim']);
            }
            $path = Yii::getAlias("@download") . "/informasi-alokasi-pembayaran.pdf";
            $response = $this->_restPenjaminAsuransi->get('informasi-alokasi-pembayaran/export-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path
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
