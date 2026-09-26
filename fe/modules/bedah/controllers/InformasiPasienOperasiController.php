<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-07 10:47:21
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-05 10:42:43
 */

namespace Doco\bedah\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DHtml;
use GuzzleHttp\Exception\RequestException;

use Doco\bedah\models\BatalOperasiForm;

use app\modules\bedah\components\traits\IntraOperasiTrait;
use app\modules\bedah\components\traits\PostOperasiTrait;
use app\modules\bedah\components\traits\ApiTrait;
use app\modules\bedah\components\traits\ViewInpostTrait;
use app\modules\bedah\components\traits\VerifikasiTagihanTrait;
use app\modules\bedah\components\traits\LaporanOperasiTrait;
use app\modules\bedah\components\traits\LaporanEndoskopiTrait;
use app\components\Traits\UniversalCpptTrait;

class InformasiPasienOperasiController extends DocoController
{
    use IntraOperasiTrait;
    use PostOperasiTrait;
    use ApiTrait;
    use ViewInpostTrait;
    use VerifikasiTagihanTrait;
    use LaporanOperasiTrait;
    use LaporanEndoskopiTrait;
    use UniversalCpptTrait;

    protected $_title = "Informasi pasien operasi";
    protected $_module = 'bedahsentral/inf-pasien-operasi/';
    protected $_restBedah;
    protected $_restDcms;
    public $_universalCpptUrl;
    protected $allowAction = [
        'cetak'
    ];

    public function init()
    {
        parent::init();
        $this->_restBedah = Yii::$app->docoRest->bedahsentral;
        $this->_restDcms = Yii::$app->docoRest->dcms;
        $this->_universalCpptUrl = 'bedah/informasi-pasien-operasi/';
    }

    public function actionIndex()
    {
        $response = $this->_restBedah->get('allow/cara-bayar');
        $resResponseBody = json_decode($response->getBody(), True);

        $getCaraBayar = ArrayHelper::getValue($resResponseBody, 'response', []);

        $legendCaraBayar = [];
        if (is_array($getCaraBayar)) {
            foreach ($getCaraBayar as $key => $value) {
                $legendCaraBayar[$key]['carabayar_nama'] = ArrayHelper::getValue($value, 'carabayar_nama', '');
                $legendCaraBayar[$key]['carabayar_kode_warna'] = ArrayHelper::getValue($value, 'carabayar_kode_warna', '');
            }
        }

        $title = $this->_title;
        $filters = $this->actionFilters('status_operasi');
        $roleBatalBtn = DHtml::cekHakAkses('batal-verifikasi');
        $roleBatalBtn = ($roleBatalBtn) ? '' : 'display:none';
        return $this->render('index', get_defined_vars());
    }
    public function actionBatalOperasi($id)
    {
        $model = new BatalOperasiForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $title = Yii::t('fe', 'Pembatalan operasi bedah sentral');
        $data = [];
        $model->tgl_batalperiksa = date('d-M-Y');
        if ($request->post()) {
            $post = $request->post();
            $model->load($post);
            if ($model->validate()) {
                try {
                    $response = $this->_restBedah->post('inf-pasien-operasi/batal-pasien', [
                        'form_params' => $model->attributes
                    ]);
                    $body = json_decode($response->getBody(), true);
                    return DocoHelpers::response($body, false);
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }
        try {
            $response = $this->_restBedah->get('inf-pasien-operasi/view', ['query' => ['id' => $id]]);
            $body = json_decode($response->getBody(), TRUE);
            $data = $body['response']['data'];
            $detail = $body['response']['detail'];
        } catch (Exception $e) {
            $data = [];
        }
        return $this->render('form_batal', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $payload = DocoDatatableHelper::advancedFilterParam();
        $response = $this->guzzleExec($this->_restBedah, [
            'url' => 'inf-pasien-operasi/index',
            'method' => 'get',
            'payload' => [
                'query' => $payload,
            ]
        ]);
        foreach ($response['data'] as $key => $value) {
            $kirimUnitLainId = ArrayHelper::getValue($value, 'pasienkirimkeunitlain_id');
            $kirimUnitLainId = DocoHelpers::encrypt($kirimUnitLainId);
            $response['data'][$key]['primary'] = DocoHelpers::encrypt($value['pasienmasukpenunjang_id']);
            $response['data'][$key]['periksa'] = DocoHelpers::encrypt($value['status_periksa']);
            $response['data'][$key]['diagnosa'] = Html::button("<i class='fa fa-plus-square-o'></i>", [
                'class' => 'btn btn-sm btn-success', 'data-source'=>"/bedah/informasi-pasien-operasi/detail-diagnosa?id=".$kirimUnitLainId,'onclick'=> 'docoHelper.detail(this)']);
        }
        $response['recordsTotal'] = $response['_meta']['totalCount'];
        $response['recordsFiltered'] = $response['_meta']['totalCount'];
        return $response;
    }
    public function actionGetRuangan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $params = '';
        if ($request->post()) {
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
        }

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restBedah->get('allow/get-ruangan', ['query' => ['id' => $parent_label]]);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['ruangan'] as $value)
                $result['output'][] = [
                    'id' => $value['ruangan_id'],
                    'name' => $value['ruangan_nama']
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

    public function actionDetail($id)
    {
        $request = Yii::$app->request;
        $penunjangId = $id;
        $id = DocoHelpers::decrypt($id);
        $title = Yii::t('fe', 'Detail Pasien Operasi');
        $data = [];
        $posisi = '';
        $roleBatalBtn = DHtml::cekHakAkses('batal-verifikasi');
        $roleBatalBtn = ($roleBatalBtn) ? '' : 'display:none';
        $pageUsedCacheName = 'bedah-used-page';
        $getUsedPageCache = Yii::$app->cache->get($pageUsedCacheName);
        $lastOperasi = 'post-operasi';
        // if (!empty($getUsedPageCache) && isset($getUsedPageCache[Yii::$app->request->url]) && $getUsedPageCache[Yii::$app->request->url]['loginpemakai_id'] != Yii::$app->user->identity->id) {
        //     $message = "Halaman Intra Operasi untuk Pasien Terkait sedang diakses oleh {$getUsedPageCache[Yii::$app->request->url]['nama_pegawai']}";
        //     Yii::$app->session->setFlash('bedah-used-page', $message);
        //     return $this->redirect('/bedah/informasi-pasien-operasi');
        // } else if ((!empty($getUsedPageCache) && !isset($getUsedPageCache[Yii::$app->request->url])) || empty($getUsedPageCache)) {
        //     $getUsedPageCache[Yii::$app->request->url] = [
        //         'loginpemakai_id' => Yii::$app->user->identity->id,
        //         'nama_pegawai' => Yii::$app->user->identity->nama_pegawai,
        //     ];
        //     Yii::$app->cache->set($pageUsedCacheName, $getUsedPageCache);
        // }
        $activeTab = [
            [
                'title' => Yii::t('fe', 'Intra Operasi'),
                'source' => 'intra-operasi'
            ],
            [
                'title' => Yii::t('fe', 'Post Operasi'),
                'source' => 'post-operasi'
            ],
            [
                'title' => Yii::t('fe', 'Verifikasi Tagihan'),
                'source' => 'verifikasi-tagihan'
            ],
            [
                'title' => Yii::t('fe', 'CPPT'),
                'source' => 'cppt'
            ],
            [
                'title' => Yii::t('fe', 'Laporan Operasi'),
                'source' => 'laporan-operasi'
            ],
            [
                'title' => Yii::t('fe', 'Laporan Endoskopi'),
                'source' => 'laporan-endoskopi'
            ],
        ];
        try {
            $responseStatusPeriksa = $this->actionFilters(null, $id);
            if ($responseStatusPeriksa['status_periksa'] == 488) {
                $response = $this->_restBedah->get('inf-pasien-operasi/mulai-operasi', ['form_params' => ['pasienmasukpenunjang_id' => $id]]);
            } else {
                $response = $this->_restBedah->get('inf-pasien-operasi/view', ['query' => ['id' => $id]]);
            }
            $body = json_decode($response->getBody(), TRUE);
            $data = isset($body['response']['data']) ? $body['response']['data'] : [];
            $status = ArrayHelper::getValue($data, 'status_periksa', '');
            $data_rencanaOperasi = isset($body['response']['data_rencanaOperasi']) ? $body['response']['data_rencanaOperasi'] : [];
            $posisi = isset($body['response']['posisi']) ? $body['response']['posisi'] : '';
            $inpostId = isset($data['intraPosisi']['inpostoperasi_id']) ? $data['intraPosisi']['inpostoperasi_id'] : '';
            $lastOperasi = isset($body['response']['last-operasi']) ? $body['response']['last-operasi'] : 'post-operasi';

            if($posisi != 1){
                $cache = Yii::$app->session;
                $unique = DocoHelpers::encrypt($inpostId) . '-' . DocoHelpers::encrypt($id);
                $cache->set('pegawaioperasi-' . $unique, []);
                $cache->set('itemoperasi-' . $unique, []);
                $cache->set('penggunaancairan-' . $unique, []);
                $cache->set('alatditubuh-' . $unique, []);
                $cache->set('pemeriksaanpelengkap-' . $unique, []);
                $cache->set('konsultindakan-' . $unique, []);
                $cache->set('penggunaanbmhp-' . $unique, []);
                $cache->set('instrumen-' . $unique, []);
            }

        } catch (\Exception $e) {
            $data = [];
        }
        $display = 'style="display:block;"';
        if ($request->get('pasien') !== NULL) {
            $display = 'style="display:none";';
        }

        $carabayar_kode_warna = ArrayHelper::getValue($data, 'carabayar_kode_warna', '');
        return $this->render('detail', get_defined_vars());
    }

    /****
     * TODO : Endpoint get list kebutuhan filter
     * 
     * * @return Json
     * ? @author: Budi (budi@sirs.co.id)
     */
    public function actionFilters($type = null, $id = null)
    {
        $response = $this->guzzleExec($this->_restBedah, [
            'url' => 'inf-pasien-operasi/filters',
            'payload' => [
                'query' => [
                    'types' => $type,
                    'term' => Yii::$app->request->get('term'),
                    'additionalPayload' => Yii::$app->request->get('additionalPayload', []),
                    'page' => Yii::$app->request->get('page', 1),
                    'id' => $id,
                ]
            ],
        ]);

        $res = $response;
        if (!empty($type)) {
            if ($type != "status_operasi") {
                $res = $this->responseJson(200, 'Data berhasil diambil!', $response[$type]);
            } else {
                $res = $response;
            }
        }
        return $res;
    }


    public function actionExportPdf($id = null)
    {  
        try { 
            $path = Yii::getAlias("@download") . "/laporan-endoskopi.pdf"; 
            $response = $this->_restBedah->get('laporan-endoskopi/cetak-endoskopi-pdf?id='. $id, [
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true); 
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } 
    }

    public function actionDetailDiagnosa(){
        $request = Yii::$app->request;
        $data = $this->guzzleExec($this->_restBedah, [
            'url' => 'inf-pasien-operasi/get-diagnosa',
            'payload' => [
                'query' => $request->get()
            ],
        ]);
        $diagnosaUtama = [];
        $diagnosaPenyerta = [];
        if(!empty($data)){
            $diagnosaUtama = !empty($data['diag_utama']) ? json_decode($data['diag_utama'], true) : [];
            $diagnosaPenyerta = !empty($data['diag_penyerta']) ? json_decode($data['diag_penyerta'], true) : [];

        }
        return $this->renderAjax('_detailDiagnosa', get_defined_vars());

    }

    public function actionShowPopup()
    {
        $title = 'Informasi Pasien Operasi';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::advancedFilterParam();
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modal', get_defined_vars());
    }
    
    public function actionProcessSync($randString)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);
        $session['randString'] = $randString;
        return $this->guzzleExec($this->_restBedah, [
            'url' => "inf-pasien-operasi/export-excel-bgprocess",
            'payload' => [
                'query' => $session
            ],
        ]);
    }

    public function actionDownloadExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('fileName', null);
        $fileDownloads = 'Informasi Pasien Operasi.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restBedah->get('inf-pasien-operasi/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }

    public function getShowKegiatanGolonganOperasi()
    {
        return $this->guzzleExec($this->_restBedah, [
            'url' => 'allow/get-konfig-kegiatan-golongan-operasi',
            'payload' => [
                'query' => []
            ],
            'returnResponse' => true,
            'success' => function($response){
                return gettype($response) == 'integer' ? $response : 1;
            }
        ]);
    }
}
