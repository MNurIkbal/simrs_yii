<?php

/**
 * @author Randy Vianda Putra
 * @todo Transaksi Retur Resep
 * @copyright 15 January 2018 aweutist
 */

namespace Doco\apotek\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use Doco\apotek\models\TransaksiResepForm;

class TransaksiReturController extends DocoController
{

    protected $_title = "Retur Resep";
    protected $_module = '/apotek/transaksi-retur';
    protected $_restApotek;
    protected $allowAction = ['*'];

    public function init()
    {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek;
    }

    /**
     * @todo get all data ajax
     * @author Randy Vianda Putra <randy@docotel.com>
     * @param integer penjualan_id
     * @param string noresep
     */
    private function getData($no_resep = null)
    {
        try {
            $request = Yii::$app->request;
            $response = $this->_restApotek->request('GET', 'transaksi-retur/ajax', [
                'query' => [
                    'no_resep' => $no_resep
                ]
            ]);
            $row = [];
            $body = json_decode($response->getBody(), true);

            $return = [
                'data-penjualan' => $body['response']['data-penjualan'],
                'data-detail-penjualan' => $body['response']['data-detail-penjualan']
            ];
            return $return;
        } catch (RequestException $e) {
            return [
                'data-penjualan' => [],
                'data-detail-penjualan' => []
            ];
        }
    }

    public function actionIndex($id, $no_resep)
    {
        $title = $this->_title;
        $model = new TransaksiResepForm;
        $penjualan_id = DocoHelpers::decrypt($id);
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
        $jenis_penjualan = 'Pasien RS';
        $backUrl = '/apotek/informasi-reseptur';

        $data = $this->getData($no_resep);

        // detail pasien retur
        if (!empty($data['data-penjualan']['nama_pasien'])) {
            $nama_pasien = $data['data-penjualan']['nama_pasien'];
        } elseif (!empty($data['data-penjualan']['nama_karyawan'])) {
            $nama_pasien = $data['data-penjualan']['nama_karyawan'];
        } elseif (!empty($data['data-penjualan']['nama_pembeli'])) {
            $nama_pasien = $data['data-penjualan']['nama_pembeli'];
        } else {
            $nama_pasien = '-';
        }

        $no_rm = isset($data['data-penjualan']['no_rekam_medik']) ? $data['data-penjualan']['no_rekam_medik'] : "-";
        $tglpenjualan = !empty($data['data-penjualan']['tglpenjualan']) ? $data['data-penjualan']['tglpenjualan'] : '-';
        $totalhargajual = !empty($data['data-penjualan']['totalhargajual'])
            ? DocoHelpers::rupiahDisplay($data['data-penjualan']['totalhargajual'])
            : '-';
        $jenis_penjualan = !empty($data['data-penjualan']['jenis_penjualan']) ? $data['data-penjualan']['jenis_penjualan'] : '-';
        $transaksiRetur = json_encode($data['data-detail-penjualan']);

        return $this->render('index', get_defined_vars());
    }

    public function actionSave($no_resep)
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $data_retur = $request->post('data_retur',[]);
            $valid = false;
            $payload_retur = [];
            foreach ($data_retur as $key => $value) {
                $payload_retur['detail'][] = [
                    'identifier' => $value['obatalkespasien_id'],
                    'qty_retur' => $value['qty_retur']
                ];

                if($value['qty_retur'] > 0) {
                    $valid = true;
                }
            }

            if (!$valid) {
                return DocoHelpers::response([
                    'response' => [
                        'text' => 'Qty Retur tidak boleh kosong.',
                        'title' => 'Proses Gagal!',
                    ]
                ], 422);
            }
            
            $response = $this->_restApotek->request('POST', 'transaksi-retur/retur', [
                'json' => $payload_retur,
                'query' => [
                    'no_resep' => $no_resep
                ]
            ]);
            $response = json_decode($response->getBody(), true);

            if(isset($response['metadata']['status']) && $response['metadata']['status'] == 500) {
                return DocoHelpers::response([
                    'response' => [
                        'text' => $response['response']['text'],
                        'title' => 'Proses Gagal!',
                    ]
                ], 422);
            }

            return DocoHelpers::response($response, false, true);
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['response']['text'] = 'Terjadi kesalah pada sistem';
            $response['response']['message'] = $e->getMessage();
            return DocoHelpers::response($response, 500);
        }
    }

    public function actionCetakPdf($returresep_id)
    {
        $returresep_id = DocoHelpers::decrypt($returresep_id);
        $path = Yii::getAlias("@download") . "/transaksi-retur.pdf";
        try {
            $response = $this->_restApotek->get('transaksi-retur/cetak-pdf?id=' . $returresep_id,[
                'save_to' => $path
            ]);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi kesalah pada sistem');
        }
    }

}