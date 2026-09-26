<?php

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
use yii\helpers\ArrayHelper;
use app\components\DocoConstants;
use yii\helpers\Json;

class InfProduksiObatController extends DocoController {
    protected $allowAction = ['*'];
    protected $_title = 'Informasi Produksi Obat';
    protected $_restApotek;
    
    public function init() {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek;
    }

    public function actions() {
        return [
            'index-produksi' => 'Doco\apotek\actions\ProduksiObat\IndexAction',
            'detail-produksi' => 'Doco\apotek\actions\ProduksiObat\DetailProduksiAction',
            'get-list-produksi' => 'Doco\apotek\actions\ProduksiObat\GetListProduksiAction',
            'get-list-detail-produksi' => 'Doco\apotek\actions\ProduksiObat\GetListDetailProduksiAction',
            'export-excel-produksi' => 'Doco\apotek\actions\ProduksiObat\ExportExcelProduksiAction',
            'show-popup-produksi' => 'Doco\apotek\actions\ProduksiObat\ShowPopUpProduksiAction',
            'define-material' => 'Doco\apotek\actions\ProduksiObat\DefineMaterialAction',
            'expand' => 'Doco\apotek\actions\ProduksiObat\ExpandAction',
            'get-alert-harga' => 'Doco\apotek\actions\ProduksiObat\GetAlertHargaAction',
            'alert-harga' => 'Doco\apotek\actions\ProduksiObat\AlertHargaAction',
            'save-produksi-obat' => 'Doco\apotek\actions\ProduksiObat\SaveProduksiAction',
        ];
    }

    public function actionIndex()
    {
        $title = \Yii::t('fe', "Informasi Pemesanan Produksi Obat");
        $user_login = DocoHelpers::encrypt(Yii::$app->user->identity->loginpemakai_id);

        $response = $this->guzzleExec($this->_restApotek, [
            'url' => 'inf-produksi-obat/init-index',
            'method' => 'GET',
        ]);

        $_status = ArrayHelper::getValue($response, 'statusData', []);

        return $this->render('index', compact('title', 'user_login', '_status'));
    }

    public function actionGetData()
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

        try {
            $response = $this->guzzleExec($this->_restApotek, [
                'url' => 'inf-produksi-obat/get-data',
                'payload' => [
                    'query' => $yiiRestfulParams
                ],
            ]);

            $no = $request->get('start',1);
            foreach ($response['data'] as $key => $value) {
                $text = substr($value['catatan_bahanbaku'],0,30)."<font color=#1160f2><b>[...]</b></font>";
                $tooltip = (strlen($value['catatan_bahanbaku']) > 30 ? "<span data-toggle='tooltip' class='tool' data-placement='top' data-original-title title='".$value['catatan_bahanbaku']."'>$text</span>" : $value['catatan_bahanbaku']);
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pemesananproduksiobat_id']);
                $value['tglpemesanan'] = date("j M Y H:i:s", strtotime($value['tglpemesanan']));
                $value['nopemesanan'] = is_null($value['nopemesanan']) ? "-" : $value['nopemesanan'];
                $value['status_pemesanan'] = is_null($value['status_pemesanan']) ? "-" : $value['status_pemesanan'];
                $value['tgl_aprove'] = is_null($value['tgl_aprove']) ? "-" : date("j M Y H:i:s", strtotime($value['tgl_aprove']));
                $value['noproduksi'] = isset($value['noproduksiobat']) ? $value['noproduksiobat'] : "-";
                $value['pegawai_pemesanan'] = is_null($value['pegawai_pemesanan']) ? "-" : $value['pegawai_pemesanan'];
                $value['obat'] = $this->formatObatProduksi($value['obatalkes_nama']);
                $value['catatan'] = is_null($value['catatan_bahanbaku']) ? "-" : $tooltip;
                $value['pegawaipemesanan_id'] = DocoHelpers::encrypt($value['pegawaipemesanan_id']);

                $value['rowNum'] = $no; $value['primary'] = $primaryKey;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $response['_meta']['totalCount'];
            $result['recordsFiltered'] = $response['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function formatObatProduksi($nama_obat = null) {
        $formatObat = '-';
        if(!empty($nama_obat)) {
            $raw = explode(',', $nama_obat);
            $twoFirst = [];
            $lastData = [];

            for ($i = 0; $i < count($raw); $i++) {
                if($i < 2) {
                    $twoFirst[] = '<li>'.$raw[$i];
                } else {
                    $lastData[] = '<li>'.$raw[$i];
                }
            }

            $lastDataParse = implode('<br>', $lastData);
            $lastDataLainnya = '<br><font color=\'#1160f2\'><b>+ ' . count($lastData) . ' Obat Lainnya</b></font>';

            $dataHtmlStatus = (count($raw) > 2) ? true : false;
            $dataTitle = (count($raw) > 2) ? $lastDataParse : '';

            $dataLainnya = (count($raw) > 2) ? $lastDataLainnya : '';

            $formatObat = '
            <span
                rel="tooltip"
                data-toggle="tooltip"
                data-trigger="hover"
                data-placement="bottom"
                data-html="'.$dataHtmlStatus.'"
                title="'.$dataTitle.'">
                    '.implode('<br>', $twoFirst).'
                    '.$dataLainnya.'
            </span>';
        }

        return $formatObat;
    }

    public function actionUpdateStatus($status){
        $request = Yii::$app->request;
        $response = $this->guzzleExec(Yii::$app->docoRest->apotek, [
            'url' => 'inf-produksi-obat/update-status',
            'method' => 'POST',
            'payload' => [
                'form_params' => [
                    'data' => $status,
                    'id' => DocoHelpers::decrypt($request->get('id'))
                ]
            ]
        ]);

        $response = DocoHelpers::response($response);
        return $response;
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $result = [];
        $url = "inf-produksi-obat/export-excel?" . http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/informasi-pemesanan-produksi-obat.xlsx";
        try {
            $this->_restApotek->get($url, [
                'save_to' => $path,
            ]);

            return DocoHelpers::downloadFile($path, true);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionListPegawai() {
        $request = Yii::$app->request;
        $response = $this->guzzleExec(Yii::$app->docoRest->apotek, [
            'url' => 'allow/get-list-pegawai',
            'method' => 'GET',
            'payload' => [
                'query' => [
                    'keyword' => $request->get('term'),
                ]
            ]
        ]);

        $list[] = ['id' => '', 'text' => '-- Approved by --'];
        foreach ($response as $value) {
            $list[] = [
                'id' => $value['pegawai_id'],
                'text' => $value['nama_pegawai']
            ];
        }

        return DocoHelpers::response($list);
    }
    public function actionSaveCacheMaterial()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $pegawaiId = Yii::$app->docoVars->user('id_pegawai');
        $pemesananId = $request->post("pemesananproduksi_id");

        try {
            $produksiObatKey = ArrayHelper::getValue($post, 'produksi_obat');
            $payloadCache = [];
            if(!empty($produksiObatKey)) {
                $produksiArray = json_decode($produksiObatKey, true);
                if(is_array($produksiArray)) {
                    foreach ($produksiArray as $value) {
                        $payloadCache[$value] = json_decode($post[$value], true);
                    }
                }

                if(! empty($payloadCache)) {
                    Yii::$app->cache->set('cache-produksi-obat-'.$pemesananId.'-'.$pegawaiId, $payloadCache, 3600);
                }

                return DocoHelpers::response([
                    'title' => 'Proses Berhasil !',
                    'message' => 'Data Obat Berhasil disimpan!',
                ]);
            }
        } catch (\Throwable $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionSearchObatMaterial()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $get = $request->get();

            $keyword = isset($get['q']) ? $get['q'] : '';
            $page = isset($get['page']) ? $get['page'] : 1;

            $result = $this->_restApotek->get('inf-produksi-obat/list-obat-alkes', [
                'query' => [
                    'term' => $keyword,
                    'page' => $page,
                    'is_produksi' => true
                ]
            ]);
            
            $result = json_decode($result->getBody(), true);
            $data = isset($result['response']['data']) ? $result['response']['data'] : [];
            $response = [];
            foreach ($data as $value) {
                $nama = empty($value['obatalkes_namalain']) ? $value['obatalkes_nama'] : $value['obatalkes_namalain'];
                $response[] = [
                    'id' => $value['obatalkes_id'],
                    'text' => $nama,
                    'kode' => !empty($value['obatalkes_kode']) ? $value['obatalkes_kode'] : "-",
                    'satuankecil_id' => $value['satuankecil_id'],
                    'satuankecil_nama' => $value['satuankecil_nama'],
                    'harga_netto' => $value['harganetto_ygdipakai']
                ];
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }

        return DocoHelpers::response([
            'result' => $response,
        ]);
    }

    public function actionSaveDefineMaterial()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $ruanganId = $request->post("depofarmasi");
            $pemesananId = $request->post("pemesananproduksi_id");
            $defaultDepo = $request->post("defaultdepo");
            $isEdit = $request->post("is_edit");
            unset($post['depofarmasi'], $post['pemesananproduksi_id'], $post['defaultdepo'], $post['is_edit']);
            
            return $this->guzzleExec(Yii::$app->docoRest->apotek, [
                'url' => 'inf-produksi-obat/save-define-material',
                'method' => 'POST',
                'payload' => [
                    'form_params' => [
                        'data' => $post,
                        'ruanganId' => (int) $ruanganId,
                        'pemesananId' => (int) $pemesananId,
                        'defaulDepo' => (int) $defaultDepo,
                        'isEdit' => (int) $isEdit
                    ]
                ],
                "returnResponse" => true
            ]);
        } catch (RequestException $th) {
            return DocoHelpers::response([
                'result' => [
                    'message' => $th->getMessage(),
                ],
            ]);
        }
    }

    public function actionBatalProduksi()
    {
        $request = Yii::$app->request;
        try {
            $pemesananId = $request->post("pemesananproduksi_id");
            $pemesananId = DocoHelpers::decrypt($pemesananId);

            return $this->guzzleExec($this->_restApotek, [
                'url' => 'inf-produksi-obat/batal-produksi',
                'method' => 'POST',
                'payload' => [
                    'form_params' => [
                        'pemesananproduksi_id' => (int) $pemesananId
                    ]
                ],
               
                "returnResponse" => true
            ]);
        } catch (RequestException $th) {
            return DocoHelpers::response([
                'result' => [
                    'message' => $th->getMessage(),
                ],
            ]);
        }
    }

    public function actionUpdateHarga()
    {
        $request = Yii::$app->request;
        try {
            $response = $this->guzzleExec($this->_restApotek, [
                'url' => 'inf-produksi-obat/save-update-harga',
                'method' => 'POST',
                'payload' => [
                    'form_params' => json_decode($request->post('toPost'))
                ],
               
                "returnResponse" => true
            ]);
        } catch (Exception $e) {
            return DocoHelpers::response($e->getMessage());
        }

        return DocoHelpers::response($response);
    }
}