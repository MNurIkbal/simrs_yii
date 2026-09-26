<?php

/**
 * @author: [Maulana Muhammad Rizky]
 * A product of PT. Sirs
 * Powered by Sirs
 */

namespace Doco\penjaminasuransi\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class LaporanPengirimanKlaimController extends DocoController
{
    protected $_restPenjamin;

    public function init()
    {
        parent::init();
        $this->_restPenjamin = Yii::$app->docoRest->penjaminasuransi;
    }

    public function actionIndex()
    {
        $title = 'Laporan Pengiriman Klaim';
        return $this->render('index', compact('title'));
    }

    public function actionGetData()
    {
        $request = Yii::$app->request;

        try {
            $filter =  DocoDatatableHelper::advancedFilterParam();
            $response = $this->_restPenjamin->get('laporan-pengiriman-klaim/get-data?' . http_build_query($filter), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            $row = [];
            foreach ($body['response']['data'] as $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['tgl_masukpulang'] = '
                    <div>
                        <p>' . $value['tgl_pendaftaran'] . '</p>
                        <p>' . $value['tgl_pulang'] . '</p>
                    </div>
                ';
                $value['nama_pasien_lengkap'] = '
                    <div>
                        <p>' . $value['nama_pasien'] . '</p>
                        <p>' . $value['no_rekammedik'] . '</p>
                    </div>
                ';
                $value['spesial_prosesedur'] = '';
                if (isset($value['additional_data'])) {
                    $additional_data = json_decode($value['additional_data'], true);
                    $prosedur = ArrayHelper::getValue($additional_data, 'pros_code');
                    $proc = ArrayHelper::getValue($additional_data, 'proc_code');
                    $investigasi = ArrayHelper::getValue($additional_data, 'inv_code');
                    $drug = ArrayHelper::getValue($additional_data, 'drug_code');

                    $value['spesial_prosesedur'] .= '<p>'. $prosedur .'</p>';
                    $value['spesial_prosesedur'] .= '<p>'. $proc .'</p>';
                    $value['spesial_prosesedur'] .= '<p>'. $investigasi .'</p>';
                    $value['spesial_prosesedur'] .= '<p>'. $drug .'</p>';
                }
                $value['group_tarif_rp'] = 'Rp. ' . number_format($value['group_tarif'], 0);
                $value['total_tarifrs_rp'] = 'Rp. ' . number_format($value['total_tarifrs'], 0);
                $row[] = $value;
            }

            $return = [
                'data' => $row,
                'draw' => $request->get('draw'),
                'recordsTotal' => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount'],
            ];

            return DocoHelpers::response($return);
        } catch (\Exception $th) {
            return DocoHelpers::response($th->getMessage(), 500);
        }
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $filters = DocoDatatableHelper::advancedFilterParam($request->get());

        try {
            $path = Yii::getAlias("@download") . "/laporan-pengiriman-klaim.xlsx";
            $this->_restPenjamin->get('laporan-pengiriman-klaim/export-excel', [
                'query' => $filters,
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path, true);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionSummary()
    {
        try {
            $request = Yii::$app->request;
            $filters = DocoDatatableHelper::advancedFilterParam($request->get());
            $response = $this->_restPenjamin->get('laporan-pengiriman-klaim/summary?' . http_build_query($filters), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $tagihanRs = ArrayHelper::getValue($body, 'response.tagihanRs', 0);
            $tarifKlaim = ArrayHelper::getValue($body, 'response.tarifKlaim', 0);

            return DocoHelpers::response([
                'tagihanRs' => number_format($tagihanRs, 0),
                'tarifKlaim' => number_format($tarifKlaim, 0),
            ]);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
}