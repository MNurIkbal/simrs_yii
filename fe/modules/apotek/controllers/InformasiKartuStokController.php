<?php

/**
 * @author    : Randy Vianda Putra
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 * @edited by : Anggoro (tri.anggoro@docotel.com)
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
use Doco\apotek\models\InformasiForm;

class InformasiKartuStokController extends DocoController
{

    protected $_title = "Informasi kartu stok";
    protected $_module = '/apotek/informasi-pemakaian';
    protected $_restApotek;

    public function init()
    {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $model = new InformasiForm;

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['ruangan_id'] = $ruangan_id;
        if(!isset($yiiRestfulParams['advanced-filter']['tanggal_transaksi'])){
            $date = date('d-M-Y');
            $yiiRestfulParams['advanced-filter']['tanggal_transaksi'] = $date.' - '.$date;
        }
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restApotek->get('inf-kartu-stok/', [
                'form_params' => [],
                'query' => $yiiRestfulParams
            ]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['tanggal_transaksi'] = date('d-M-Y', strtotime($value['tanggal_transaksi']));
                $value['tglkadaluarsa'] = date('d-M-Y', strtotime($value['tglkadaluarsa']));
                $value['qtystok_in'] = DocoHelpers::formatNumber($value['qtystok_in'], true, false, 3);
                $value['qtystok_out'] = DocoHelpers::formatNumber($value['qtystok_out'], true, false, 3);
                $value['total'] = DocoHelpers::formatNumber($value['total'], true, false, 3);
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

    public function actionGetObat()
    {
        if (isset($_GET['q']['term']) && !empty($_GET['q']['term'])) {
            $response = $this->_restApotek->request('POST', 'inf-kartu-stok/data-obat', [
                'form_params' => ['term' => $_GET['q']['term'], 'ruangan_id'=>Yii::$app->docoVars->workspace('ruangan_id')],
            ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            $check = [];
            foreach ($body['response'] as $key => $value) {
                if(!isset($check[$value['obatalkes_nama']])){
                    $check[$value['obatalkes_nama']] = $value;
                    $text = $value['obatalkes_kode'] . " - " . $value['obatalkes_nama'];
                    $is_disabled = false;

                    if(isset($value['is_active']) && $value['is_active'] == false) {
                        $text .= ' - [TIDAK AKTIF]';
                        $is_disabled = true;
                    }

                    $data[] = [
                        'id' => $value['obatalkes_id'], 
                        'text' => $text,
                        'disabled' => $is_disabled
                    ];
                }
            }
            $total = count($body['response']);
            $return = ['result' => $data, 'total_count' => $total, 'incomplete_results' => false];
            return DocoHelpers::response($return);
        }
    }

    public function actionGetTglkadaluarsa()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restApotek->get('inf-kartu-stok/get-tglkadaluarsa',
                [
                    'query' => [
                        'id' => $parent_label,
                        'ruangan_id' => Yii::$app->docoVars->workspace('ruangan_id')
                    ]
                ]);
            $body = json_decode($response->getBody(), True);
            $res = [];
            foreach ($body['response'] as $value) {
                if(!isset($res[$value['tglkadaluarsa']])) {
                    $res[$value['tglkadaluarsa']] = $value;
                    $result['output'][] = [
                        'id' => $value['tglkadaluarsa'],
                        'name' => date('d-M-Y',strtotime($value['tglkadaluarsa']))
                    ];
                }
            }

            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return$result;
        }
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $filters = DocoDatatableHelper::convertToRestfulParams($request->get());
        $filters['advanced-filter']['ruangan_id'] = $ruangan_id;
        $path = Yii::getAlias("@download") . "/laporan-kartu-stok.xlsx";
        try {
            $response = $this->_restApotek->get('inf-kartu-stok/excel',[
                'query' => $filters,
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e){
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionGetStokObat($obatalkes_id = null)
    {
        $request = Yii::$app->request;
        $tgl_transaksi = $request->get('tgl_transaksi', null);

        try {
            if (!is_null($obatalkes_id)) {
                $response = $this->_restApotek->request('GET', 'inf-kartu-stok/get-stok-obat', [
                    'query' => [
                        'obatalkes_id'  => $obatalkes_id,
                        'ruangan_id'    => Yii::$app->docoVars->workspace('ruangan_id'),
                        'tgl_transaksi' => $tgl_transaksi
                    ],
                ]);

                $body = json_decode($response->getBody(), true);
                return DocoHelpers::response($body['response']);
            }
        } catch (\Exception $e) {
            return DocoHelpers::response("");
        }

    }

    public function actionShowPopupExcel()
    {
        $title = 'Informasi Kartu Stok';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['ruangan_id'] = $ruangan_id;
        if(!isset($yiiRestfulParams['advanced-filter']['tanggal_transaksi'])){
            $date = date('d-M-Y');
            $yiiRestfulParams['advanced-filter']['tanggal_transaksi'] = $date.' - '.$date;
        }
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modal_excel', get_defined_vars());
    }

    public function actionProcessSyncExcel($randString)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);
        $session['randString'] = $randString;
        return $this->guzzleExec($this->_restApotek, [
            'url' => "inf-kartu-stok/export-excel-bgprocess",
            'payload' => [
                'query' => $session
            ],
        ]);
    }

    public function actionDownloadExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('fileName', null);
        $fileDownloads = 'informasi-kartu-stok.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restApotek->get('inf-kartu-stok/download-excel', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }
}
