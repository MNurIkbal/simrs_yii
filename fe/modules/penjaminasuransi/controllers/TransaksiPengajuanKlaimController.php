<?php

namespace Doco\penjaminasuransi\controllers;

use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class TransaksiPengajuanKlaimController extends DocoController
{
    protected $_title;
    protected $_restMaster;
    protected $_restPenjaminAsuransi;
    protected $_module = 'penjaminasuransi/transaksi-pengajuan-klaim/';

    public function init()
    {
        parent::init();
        $this->_title = 'Pengajuan Klaim';
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restPenjaminAsuransi = Yii::$app->docoRest->penjaminasuransi;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        $newActions = [
            'index' => 'Doco\penjaminasuransi\actions\TransaksiPengajuanKlaim\IndexAction',
            'list-pasien' => 'Doco\penjaminasuransi\actions\TransaksiPengajuanKlaim\ListPasienAction',
            'add-detail-klaim' => 'Doco\penjaminasuransi\actions\TransaksiPengajuanKlaim\AddDetailKlaimAction',
            'cetak-pengajuan' => 'Doco\penjaminasuransi\actions\TransaksiPengajuanKlaim\CetakPengajuanAction',
        ];
        return array_merge($actions, $newActions);
    }

    public function actionSearchCaraBayar()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restMaster->get('allow/get-list-cara-bayar', [
                'query' => [
                    'term' => $request->get('term'),
                ]
            ]);

            $result = json_decode($result->getBody(), true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['carabayar_id'],
                    'text' => $value['carabayar_nama'],
                ];
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }

        return DocoHelpers::response([
            'result' => $response
        ]);
    }

    public function actionGetPenjamin($selected = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request                    = Yii::$app->request;

        if ($request->post()) {
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label    = $depdrop_parents[0];
        }

        $result             = [];
        $result['output']   = [];
        $result['selected'] = $selected;

        try {
            $response = $this->_restMaster->get('allow/get-list-penjamin', [
                'query' => [
                    'parent_label' => $parent_label,
                ]
            ]);

            $body = json_decode($response->getBody(), true);

            foreach ($body['response'] as $value)
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

    public function actionGetDataListPasien()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['carabayar_id'] = $request->get('carabayar_id');
        $yiiRestfulParams['penjamin_id']  = $request->get('penjamin_id');
        $yiiRestfulParams['is_paging']  = $request->get('is_paging');
        $yiiRestfulParams['advanced-filter']['tglpasienpulang'] = ArrayHelper::getValue($yiiRestfulParams, 'advanced-filter.tglpasienpulang', $request->get('tglpasienpulang'));
        $yiiRestfulParams['advanced-filter']['instalasi_nama'] = ArrayHelper::getValue($yiiRestfulParams, 'advanced-filter.instalasi_nama', $request->get('instalasi_nama'));
        $yiiRestfulParams['advanced-filter']['ruangan_nama'] = ArrayHelper::getValue($yiiRestfulParams, 'advanced-filter.ruangan_nama', $request->get('ruangan_nama'));
        if ($yiiRestfulParams["per-page"] < 1) {
            unset($yiiRestfulParams["per-page"]);
        }
        if (isset($yiiRestfulParams['advanced-filter']['tglpasienpulang'])) {
            $helper                = new DocoHelpers;
            $tglpasienpulang_range = $helper->parsingRangeDate($yiiRestfulParams['advanced-filter']['tglpasienpulang']);

            $yiiRestfulParams['advanced-filter']['tglpasienpulang_awal']  = $tglpasienpulang_range['startDate'];
            $yiiRestfulParams['advanced-filter']['tglpasienpulang_akhir'] = $tglpasienpulang_range['endDate'];

            unset($yiiRestfulParams['advanced-filter']['tglpasienpulang']);
        }
        $draw                   = $request->get('draw', 1);
        $data                   = [];
        $result                 = [];
        $result['data']         = $data;
        $result['draw']         = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restPenjaminAsuransi->get('transaksi-pengajuan-klaim/index?' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body     = json_decode($response->getBody(), true);
            $no       = $request->get('start', 1);

            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey                        = DocoHelpers::encrypt($value['no_pembayaran']);
                $value['pasienadmisi_id']          = $value['pasienadmisi_id'] ? $value['pasienadmisi_id'] : null;
                $value['admisi']                   = DocoHelpers::encrypt($value['pasienadmisi_id']);
                $value['primary']                  = $primaryKey;
                $value['rowNum']                   = $no;
                $value['tgl_pendaftaran']          = date('d M Y', strtotime($value['tgl_pendaftaran']));
                $value['tglpasienpulang']          = !empty($value['tglpasienpulang']) ? date('d M Y', strtotime($value['tglpasienpulang'])) : '';
                $value['total_tagihan_label']      = DocoHelpers::rupiahDisplay($value['total_tagihan']);
                $value['total_sdh_bayar_label']    = DocoHelpers::rupiahDisplay($value['total_sdh_bayar']);
                $value['total_discount_label']     = DocoHelpers::rupiahDisplay($value['total_discountpembayaran']);
                $value['total_asuransi_label']     = DocoHelpers::rupiahDisplay($value['total_asuransi']);
                $value['total_sisa_tagihan_label'] = DocoHelpers::rupiahDisplay($value['total_sisa_tagihan']);
                $value['jumlah_inacbg_label']      = DocoHelpers::rupiahDisplay($value['jumlah_inacbg']);
                $value['total_pengajuan']          = $value['carabayar_id'] == 6 ? $value['jumlah_inacbg'] : $value['total_asuransi'];
                $value['total_pengajuan_label']    = $value['carabayar_id'] == 6 ? $value['jumlah_inacbg'] : $value['total_asuransi'];
                $value['total_pengajuan_label']    = DocoHelpers::rupiahDisplay($value['total_pengajuan_label']);
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

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request                    = Yii::$app->request;

        try {
            $yiiRestfulParams                 = DocoDatatableHelper::convertToRestfulParams($request->get());
            $yiiRestfulParams['carabayar_id'] = $request->get('carabayar_id');
            $yiiRestfulParams['penjamin_id']  = $request->get('penjamin_id');
            // $yiiRestfulParams['title']        = DHtml::getTitleMenu();
            $title                            = "Transaksi Pengajuan Klaim";

            $url  = 'transaksi-pengajuan-klaim/export-excel?' . http_build_query($yiiRestfulParams);
            $path = Yii::getAlias("@download") . "/" . $title . ".xlsx";

            $this->_restPenjaminAsuransi->get($url, [
                'save_to' => $path
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
            $yiiRestfulParams                 = DocoDatatableHelper::convertToRestfulParams($request->get());
            $yiiRestfulParams['carabayar_id'] = $request->get('carabayar_id');
            $yiiRestfulParams['penjamin_id']  = $request->get('penjamin_id');
            // $yiiRestfulParams['title']        = DHtml::getTitleMenu();
            $title                            = "Transaksi Pengajuan Klaim";

            $path     = Yii::getAlias("@download") . "/" . $title . ".pdf";
            $this->_restPenjaminAsuransi->get('transaksi-pengajuan-klaim/export-pdf?' . http_build_query($yiiRestfulParams), [
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
}
