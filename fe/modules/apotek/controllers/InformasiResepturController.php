<?php

/**
 * @author : Randy Vianda Putra
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
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
use yii\helpers\ArrayHelper;
use app\components\DocoConstants;
use Doco\apotek\components\filler\ResepDetailFiller;
use yii\helpers\Json;

class InformasiResepturController extends DocoController {
    protected $allowAction = [
        'print-resep-detail',
    ];
    protected $_restApotek;
    const DATA_RESEP_RESEPTUR_CACHE_DURATION = 1 * 15 * 60;
    
    public function init() {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek;
        $this->getKonfigFarmasi();
    }

    public function actions() {
        return [
            'index'                 => 'Doco\apotek\actions\InformasiReseptur\IndexAction',
            'delete'                => 'Doco\apotek\actions\InformasiReseptur\DeleteAction',
            'view'                  => 'Doco\apotek\actions\InformasiReseptur\ViewAction',
            'view-approve'          => 'Doco\apotek\actions\InformasiReseptur\ViewApproveAction',
            'export-excel'          => 'Doco\apotek\actions\InformasiReseptur\ExportExcelAction',
            'batal-resep'           => 'Doco\apotek\actions\InformasiReseptur\BatalResepAction',
            'get-data'              => 'Doco\apotek\actions\InformasiReseptur\GetDataInformasiResepturAction',
            'get-data-obat'         => 'Doco\apotek\actions\InformasiReseptur\GetDataObatAction',
            'get-no-pendaftaran'    => 'Doco\apotek\actions\InformasiReseptur\GetNoPendaftaranAction',
            'get-no-reseptur'       => 'Doco\apotek\actions\InformasiReseptur\GetNoResepturAction',
            'get-penjamin'          => 'Doco\apotek\actions\InformasiReseptur\GetPenjaminAction',
            'panggil-antrian'       => 'Doco\apotek\actions\InformasiReseptur\PanggilAntrianAction',
            'pilih-loket'           => 'Doco\apotek\actions\InformasiReseptur\PilihLoketAction',
            'print-resep'           => 'Doco\apotek\actions\InformasiReseptur\PrintResepAction',
            'serahkan-obat'         => 'Doco\apotek\actions\InformasiReseptur\SerahkanObatAction',
            'set-loket'             => 'Doco\apotek\actions\InformasiReseptur\SetLoketAction',
            'print-resep-detail'    => 'Doco\apotek\actions\InformasiReseptur\PrintResepDetailAction',
            'expand'                => 'Doco\apotek\actions\InformasiReseptur\ExpandAction',
            'batal-approve'         => 'Doco\apotek\actions\InformasiReseptur\BatalApproveAction',
            'cetak-multiple-resep'  => 'Doco\apotek\actions\InformasiReseptur\CetakMultipleResep',
            'print-multiple-resep'  => 'Doco\apotek\actions\InformasiReseptur\PrintMultipleResep',
            'generate-resep-kronis' => 'Doco\apotek\actions\InformasiReseptur\GenerateKronisAction',
            'mark-deleted'          => 'Doco\apotek\actions\InformasiReseptur\MarkDeletedAction',
            'generate-kronis'       => 'Doco\apotek\actions\InformasiReseptur\GenerateResepKronisAction',
            'log-perubahan'         => 'Doco\apotek\actions\InformasiReseptur\LogPerubahanAction',
            'get-data-log'          => 'Doco\apotek\actions\InformasiReseptur\GetDataLogAction',
        ];
    }

    public function actionViewNotif($reseptur_id)
    {
        try{
            $_curl = Yii::$app->docoRest->apotek->get('allow/get-resep-by-reseptur-id',[
                'query' => [
                    'reseptur_id' => DocoHelpers::decrypt($reseptur_id)
                ]
            ]);
            $_decodeResponse = json_decode($_curl->getBody(),TRUE);
            $id = $reseptur_id;
            $nomor = ArrayHelper::getValue($_decodeResponse,'response.nomor');
            $url = 'view?id='.$reseptur_id.'&nomor='.$nomor;
        }catch(RequestException $e){
            $url = '';
        }catch(\Exception $e){
            $url = '';
        };
        $this->redirect($url);
    }

    public function groupingResep($cacheResep)
    {
        $kelompok_resep = [];
        foreach ($cacheResep as $item_obat) {
            if(!isset($item_obat['posisi'])) {
                continue;
            }

            $posisi = $item_obat['posisi'];

            if ($item_obat['is_racikan']) {
                $kelompok_resep[$item_obat['r_ke']][$posisi] = $item_obat;
            } else {
                $kelompok_resep['non'][$posisi] = $item_obat;
            }
        }

        $final_list = [];
        foreach ($kelompok_resep as $kelompok) {
            $final_list = array_merge($final_list, $kelompok);
        }
        return $final_list;
    }

    public function actionGetListInstalasiRuangan()
    {
        if (isset($_GET['q']['term']) && !empty($_GET['q']['term'])) {
            $response = $this->guzzleExec(Yii::$app->docoRest->master,[
                'url' => 'master-api/get-list-instalasi-ruangan',
                'method' => 'GET',
                'payload' => [
                    'query' => ['term' => $_GET['q']['term']]
                ],
                'returnResponse' => true
            ]);
            $body = ArrayHelper::getValue($response, 'data', []);
            $data = [];
            foreach ($body as $key => $value) {
                $data[] = [
                    'ruangan_id' => ArrayHelper::getValue($value, 'ruangan_id'),
                    'instalasi_ruangan' => ArrayHelper::getValue($value, 'instalasi_nama') . " - " . ArrayHelper::getValue($value, 'ruangan_nama')
                ];
            }
            return ['result' => $data, 'total_count' =>count($body), 'incomplete_results' => false];
        }
    }

    public function actionGetListTipeResep() {
        $getKategoriResep = $this->guzzleExec(Yii::$app->docoRest->rajal, [
            'url' => 'allow/get-lookup-type',
            'method' => 'GET',
            'payload' => [
                'query' => [
                    'type' => 'kategori_resep'
                ]
            ],
            'returnResponse' => true
        ]);

        $kategori_resep = $getKategoriResep;
        
        $data = [];
        foreach ($kategori_resep['data'] as $key => $value) {
            $data[] = [
                'lookup_id' => $value['lookup_id'],
                'lookup_name' => $value['lookup_name']
            ];
        }

        $filter_all[] = [
            'lookup_id' => 'all',
            'lookup_name' => 'Semua Tipe Resep'
        ];

        $data = array_merge($filter_all, $data);

        return ['result' => $data, 'total_count' => count($kategori_resep), 'incomplete_results' => false];
    }

    public function actionShowPopupSerahObat() {
        $title = 'Informasi Proses Serahkan Obat';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['randString'] = $randString;
        $yiiRestfulParams['nomor'] = DocoHelpers::setDecryptIdFromString($request->get('nomor_resep'));

        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modalDetail', get_defined_vars());
    }

    public function actionProsesSyncSerahObat() {
        $request = Yii::$app->request;
        $randString = $request->get('randString');
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $this->guzzleExec(Yii::$app->docoRest->apotek, [
            'url' => "inf-reseptur/sync-serahkan-obat",
            'method' => 'GET',
            'payload' => [
                'query' => [
                    Yii::$app->session->getFlash($randString)
                ],
            ]
        ]);
    }

    public function actionProsesSerahkanObatMultiple() {
        $request = Yii::$app->request;
        $no_resep = $request->get('no_resep');
        $session = Yii::$app->session;
        /** menghindari get data ulang */
        $getDataObat = $session->get('list-data-info-reseptur', []);
        $getDataObat = ArrayHelper::index($getDataObat, 'nomor', []);
        $getDataObat = isset($getDataObat[$no_resep]) ? $getDataObat[$no_resep] : [];
        if (empty($getDataObat)) {
            // Kebutuhan untuk hanya mengambil ruangan, instalasi reseptur, dan status reseptur, kebutuhan persiapan data serahkan obat
            $getDataObat = $this->guzzleExec(Yii::$app->docoRest->apotek, [
                'url' => "inf-reseptur/get-info-data-resep",
                'method' => 'GET',
                'payload' => [
                    'query' => [
                        'nomor_resep' => $no_resep
                    ],
                ]
            ]);
        }

        $nomor = $no_resep;
        $ruangan_id = $getDataObat['ruangan_id'];
        $instalasiasal_id = $getDataObat['instalasi_reseptur_id'];
        $status_reseptur = $getDataObat['status_reseptur_id'];

        
        if($status_reseptur == DocoConstants::STATUS_RESEPTUR_BATAL || $status_reseptur == DocoConstants::STATUS_RESEPTUR_DISERAHKAN) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $nama_status = ($status_reseptur == DocoConstants::STATUS_RESEPTUR_DISERAHKAN) ? 'diserahkan.' : 'dibatalkan.';

            $responseData = [
                'data' => [
                    'file' => NULL,
                    'line' => NULL,
                    'message' => 'Resep sudah ' . $nama_status
                ],
                'httpStatusCode' => 422,
                'message' => 'Resep sudah ' . $nama_status,
                'meta' => [
                    'code' => 422,
                    'result' => 'failed',
                    'title' => NULL
                ]
            ];
        } else {
            $response = $this->guzzleExec(Yii::$app->docoRest->apotek, [
                'url' => 'inf-reseptur/serahkan-obat',
                'method' => 'POST',
                'payload' => [
                    'form_params' => [
                        'nomor' => $nomor,
                        'ruangan_id' => $ruangan_id,
                        'instalasiasal_id' => $instalasiasal_id
                    ]
                ]
            ]);

            $responseData = DocoHelpers::response($response);            
        }

        return $responseData;
    }

    public function getKonfigFarmasi($get_new = false)
    {
        try {
            $konfigFarmasi = null;
            if ($get_new) {
                $konfigFarmasi = $this->guzzleExec(Yii::$app->docoRest->apotek,[
                    'url' => 'allow/konfig-farmasi',
                    'method' => 'GET'
                ]);
                Yii::$app->cache->set('konfig-farmasi', $konfigFarmasi);
            } else {
                $konfigFarmasi = Yii::$app->cache->getOrSet('konfig-farmasi', function() {
                    return $this->guzzleExec(Yii::$app->docoRest->apotek,[
                        'url' => 'allow/konfig-farmasi',
                        'method' => 'GET'
                    ]);
                });
            }
            return $konfigFarmasi;
        } catch (\Exception $e) {
            return [
                'messages' => $e->getMessage()
            ];
        }
    }

    public function getLookupTransaksi($kode)
    {
        try {
            $getLookup = $this->guzzleExec(Yii::$app->docoRest->apotek, [
                'url' => 'allow/lookup-transaksi',
                'method' => 'GET',
                'payload' => [
                    'query' => [
                        'kode' => $kode
                    ]
                ]
            ]);

            return $getLookup;
        } catch (\Exception $e) {
            return [
                'messages' => $e->getMessage()
            ];
        }
    }

    public function actionPrintAntrian($pendaftaran_id = null, $resep_id = null)
    {
        $request = Yii::$app->request;
        $jenis = $request->get('jenis');
        $path = Yii::getAlias("@download") . "/print-antrian-farmasi.pdf";
        $post = [
                    'resep_id'=>$resep_id,
                    'pendaftaran_id'=>$pendaftaran_id
                ];
        try {
            $response = $this->_restApotek->post('inf-reseptur/print-antrian',
                [
                    'query' => [
                        'resep_id' => $resep_id,
                        'pendaftaran_id' => $pendaftaran_id,
                        'jenis' => $jenis
                    ],
                    'save_to' => $path
                ]
            );

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function getInfoResep($nomor)
    {
        $cacheName = 'info-resep-' . $nomor;
        $_res = Yii::$app->docoRest->apotek->get('allow/info-resep-v2',[
            'query' => [
                'nomor' => $nomor,
            ]
        ]);
        $infoResep = json_decode($_res->getBody(), true);
        Yii::$app->cache->Set($cacheName, $infoResep, 3600);
        return $infoResep;
    }

    public function getInfoResepData($nomor, $jenis, $params)
    {
        $cacheName = 'info-resep-' . $nomor;
        $dataResep = ResepDetailFiller::getInfoResepData($this->_restApotek, $jenis, $params);
        Yii::$app->cache->Set($cacheName, $dataResep, self::DATA_RESEP_RESEPTUR_CACHE_DURATION);
        return $dataResep;
    }

    public function actionModalHistoryResep()
    {
        $pasien_id = Yii::$app->request->get('pasien_id', null);
        $no_resep = Yii::$app->request->get('no_resep', null);
        return $this->renderAjax('/assets/_history_resep', [
            'pasien_id' => $pasien_id,
            'no_resep' => $no_resep
        ]);
    }

    public function actionGetListHistoryResep()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $draw = $request->get('draw', 1);
        $pasienId = $request->get('pasien_id', null);
        $no_resep = $request->get('no_resep', null);

        $getData = $this->guzzleExec(Yii::$app->docoRest->apotek, [
            'url' => 'allow/list-history-resep',
            'method' => 'GET',
            'payload' => [
                'query' => [
                    'pasien_id' => $pasienId,
                    'no_resep' => $no_resep,
                    'start' => $request->get('start', 0),
                    'length' => $request->get('length', 10)
                ]
            ]
        ]);

        return [
            'data' => $getData['data'],
            'draw' => $draw,
            'recordsTotal' => $getData['_meta']['totalCount'],
            'recordsFiltered' => $getData['_meta']['totalCount']
        ];
    }
}
