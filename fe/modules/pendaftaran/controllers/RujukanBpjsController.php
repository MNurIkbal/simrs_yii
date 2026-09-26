<?php

namespace Doco\pendaftaran\controllers;

use Yii;
use app\components\DocoConstants;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\pendaftaran\models\RujukanBpjsForm;
use app\modules\pendaftaran\models\HapusRujukanForm;
use GuzzleHttp\Exception\RequestException;
use yii\base\Exception;
use yii\web\Response;
use app\components\Services\Contracts\BpjsInterface;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class RujukanBpjsController extends DocoController
{
    protected $_title = 'Rujukan Pasien Keluar (BPJS)';
    protected $_restPendaftaran;
    protected $_module = 'rujukan-bpjs/';
    protected $bpjsService;

    public function __construct($id, $module, $config = [], BpjsInterface $bpjsService)
    {
        $this->bpjsService = $bpjsService;
        parent::__construct($id, $module, $config);
    }

    public function init()
    {
        parent::init();
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);

        return $behaviors;
    }

    public function actionIndex()
    {
        try {
            $title = $this->_title;

            return $this->render('index', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    /**
     * @todo Action untuk mendapatkan list data rujukan bpjs
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetData()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $draw = $request->get('draw', 1);
            $no = $request->get('start', 1);
            $data = [];

            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;

            if (isset($yiiRestfulParams['advanced-filter']['tanggal_rujukan'])) {
                $tgl_kunjungan_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tanggal_rujukan']);
                $tgl_awal = $tgl_kunjungan_range[0];
                $tgl_akhir = $tgl_kunjungan_range[1];
                $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
                $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
                $yiiRestfulParams['advanced-filter']['tanggal_rujukan_awal'] = $tgl_awal_format;
                $yiiRestfulParams['advanced-filter']['tanggal_rujukan_akhir'] = $tgl_akhir_format;
                unset($yiiRestfulParams['advanced-filter']['tanggal_rujukan']);
            }

            $restPendaftaran = $this->_restPendaftaran->get('rujukan-bpjs/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($restPendaftaran->getBody(), true);
            $data = $body['response']['data'];

            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['rujukanbpjs_id']);
                    $value['primary'] = $primaryKey;
                    $value['no'] = $no;
                    $value['tanggal_rujukan'] = DocoHelpers::convDateTime($value['tanggal_rujukan'], false, false);
                    $data[$key] = $value;
                }

                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

                return $result;
            } else {
                $result['data'] = $data;
                $result['recordsTotal'] = 0;
                $result['recordsFiltered'] = 0;

                return $result;
            }
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    /**
     * @todo Action untuk menampilkan form rujukan bpjs
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionCreate()
    {
        try {
            $request = Yii::$app->request;
            $title = Yii::t('fe', 'Tambah Rujukan');
            $model = new RujukanBpjsForm;
            $model->rujukan = 0;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            
            if ($request->post()) {
                $model->load($request->post());
                if($model->rujukan == 0) {
                    $model->scenario = 'penuh';
                } else {
                    $model->scenario = 'default';
                }
                $model->dirujukke = $model->kode_ppkrujukan;
                $model->jenis_pelayanan_bpjs = $request->post('jenis_pelayanan');
                //$model->tanggal_rencana_kunjungan = date('Y-m-d', strtotime($request->post('tmp_tgl_kunjungan')));
                if ($model->validate()) {
                    $temp_response = $this->_restPendaftaran->post('rujukan-bpjs/create', [
                        'form_params' => $model->attributes
                    ]);
                    $response = json_decode($temp_response->getBody(), true);
                    $responseStatus = (int) ArrayHelper::getValue($response['metadata'],'status', 400);
                    // $responseMetadata = (int) ArrayHelper::getValue($response['response']['text']['metaData'],'code', 400);

                    if (($responseStatus != 200) && ($response['response']['text']['metaData']['code'] != 200 || $response['response']['text']['metaData']['code'] != '200')) {
                        return DocoHelpers::responseTemplate(
                            400,
                            'Error',
                            [],
                            [
                                'title' => Yii::t('fe', 'Peringatan!'),
                                'text' => $response['response']['text']['metaData']['message'],
                                'message' => $response['response']['text']['metaData']['message'],
                            ]
                        );
                    }
                    return DocoHelpers::responseJsonString($temp_response->getBody(), $formName);
                } else {
                    $errors = DocoHelpers::parseError($model->errors, $formName);

                    return DocoHelpers::responseTemplate(422, 'Error', $errors);
                }
            } else {
                $response = $this->_restPendaftaran->get('rujukan-bpjs/get-data-options');
                $body = json_decode($response->getBody(), true);
                $options = $body['response'];
                
                return $this->render('form', [
                    'title' => $title,
                    'model' => $model,
                    'options' => $options
                ]);
            }
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionGetPasienBySep($nosep)
    {
        try {
            $request = $this->_restPendaftaran->get('rujukan-bpjs/get-pasien-by-sep', [
                'query' => ['nosep' => $nosep]
            ]);
            $body = json_decode($request->getBody(), true);

            return json_encode($body);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionGetDiagnosa($q = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $bridgeRes = $this->bpjsService->referensiDiagnosa($q);
        $results = [];
        if ($bridgeRes && $bridgeRes['metaData']['code'] == 200) {
            $list = $bridgeRes['response']['diagnosa'];
            foreach ($list as $key => $each) {
                $results[] = ['id' => $each['kode'], 'text' => $each['nama']];
            }
        }
        return ['results' => $results];
    }

    public function actionPrintRujukan()
    {
        try {
            $request = Yii::$app->request;
            $path = Yii::getAlias("@download") . "/print-rujukan.pdf";
        
            $rujukanbpjs_id = $request->get('id', null);
            $bpjs_id = $request->get('bpjs', null);

            if (!is_numeric($rujukanbpjs_id)) {
                $rujukanbpjs_id = DocoHelpers::decrypt($rujukanbpjs_id);
            }

            $filterQuery = [
                'rujukanbpjs_id' => $rujukanbpjs_id,
                'bpjs_id' => $bpjs_id
            ];

            $response = $this->_restPendaftaran->get('rujukan-bpjs/print-rujukan', [
                'query' => $filterQuery,
                'save_to' => $path
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionModalPencarianRujukan()
    {
        try {
            $model = new RujukanBpjsForm;
            return $this->renderAjax('pencarian-rujukan', compact('model'));
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    public function actionRujukanSpesialistik()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $draw = $request->get('draw', 1);
        $ppk = $request->get('ppk');
        $tgl = date('Y-m-d', strtotime($request->get('tgl')));

        $results = [];
        $data = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        $bridgeRes = $this->bpjsService->rujukanSpesialistik($ppk, $tgl);
        if ($bridgeRes && $bridgeRes['metaData']['code'] == 200) {
            $list = $bridgeRes['response']['list'];
            $no = 0;
            foreach ($list as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['selectedSpesialis'] = Html::button("<i class='fa fa-check'></i>", [
                    'class' => 'btn btn-sm btn-success selected-spesialis']);
                // $value['detail'] = Html::button("<i class='fa fa-plus-square-o'></i>", [
                //     'class' => 'btn btn-sm btn-info', 'data-source'=> "link".$value['kodeSpesialis'],'onclick'=> 'docoHelper.detail(this)']);
                $data[$key] = $value;
            }
        }
        $result['data'] = $data;
        return DocoHelpers::response($result);
    }

    /**
     * Hapus rujukan BPJS
     *
     * @return void
     * @author
     */
    public function actionConfirmHapus() 
    {
        $request        = Yii::$app->request;
        $rujukanbpjs_id = $request->get('rujukanbpjs_id');

        $username = Yii::$app->docoVars->user('nama');
        $title     = Yii::t('fe', 'Hapus Rujukan');
        $model = new HapusRujukanForm;

        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                $response = $this->_restPendaftaran->post('rujukan-bpjs/hapus-rujukan', [
                    'query' => [
                        'rujukanbpjs_id' => $rujukanbpjs_id
                    ],
                    'form_params' => $model->attributes
                ]);
                $response = json_decode($response->getBody(), true);
                return DocoHelpers::response($response);
            }

            return DocoHelpers::response($model->errors,422,'HapusRujukanForm');
        } else {
            return $this->renderAjax('_modal_hapus', get_defined_vars());
        }
    }

    public function actionUpdate($id = null)
    {
        // try {
            $request = Yii::$app->request;
            $title = Yii::t('fe', 'Update Rujukan');
            $model = new RujukanBpjsForm;
            $model->rujukan = 0;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $status_update = false;
            $data_rujukan = [];
            $sep = [];

            $id = DocoHelpers::decrypt($id);
            
            
            if ($request->post()) {
                $model->load($request->post());
                if($model->rujukan == 0) {
                    $model->scenario = 'penuh';
                } else {
                    $model->scenario = 'default';
                }
                $model->dirujukke = $model->kode_ppkrujukan;
                $model->jenis_pelayanan_bpjs = $request->post('jenis_pelayanan');
                $model->tanggal_rencana_kunjungan = date('Y-m-d', strtotime($request->post('tmp_tgl_kunjungan')));
                if ($model->validate()) {
                    $temp_response = $this->_restPendaftaran->post('rujukan-bpjs/update-rujukan', [
                        'form_params' => $model->attributes
                    ]);
                    $response = json_decode($temp_response->getBody(), true);
                    $responseStatus = (int) ArrayHelper::getValue($response['metadata'],'status', 400);
                    // $responseMetadata = (int) ArrayHelper::getValue($response['response']['text']['metaData'],'code', 400);

                    if (($responseStatus != 200) && ($response['response']['text']['metaData']['code'] != 200 || $response['response']['text']['metaData']['code'] != '200')) {
                        return DocoHelpers::responseTemplate(
                            400,
                            'Error',
                            [],
                            [
                                'title' => Yii::t('fe', 'Peringatan!'),
                                'text' => $response['response']['text']['metaData']['message'],
                                'message' => $response['response']['text']['metaData']['message'],
                            ]
                        );
                    }
                    return DocoHelpers::responseJsonString($temp_response->getBody(), $formName);
                } else {
                    $errors = DocoHelpers::parseError($model->errors, $formName);

                    return DocoHelpers::responseTemplate(422, 'Error', $errors);
                }
            } else {
                $response = $this->_restPendaftaran->get('rujukan-bpjs/get-data-options');
                $body = json_decode($response->getBody(), true);
                $options = $body['response'];

                $data = $this->_restPendaftaran->get('rujukan-bpjs/get-data-rujukan', [
                    'query' => [
                        'rujukanbpjs_id' => $id
                    ]
                ]);
                $data = json_decode($data->getBody(), true);
                if (!empty($data['response'])){
                    $additional_data = json_decode($data['response']['rujukan']['additional_data']);
                    $additional_request = (array) json_decode($data['response']['rujukan']['additional_request']);
                    $additional_request['tglRencanaKunjungan'] = date('Y-m-d');
                    $peserta = $data['response']['peserta']['response']['peserta'];
                    $data['response']['rujukan']['tanggal_rencana_kunjungan'] = date('Y-m-d', strtotime($data['response']['rujukan']['tanggal_rencana_kunjungan']));
                    $data_rujukan = $data['response']['rujukan'];
                    $catatan_rujukan = $data_rujukan['catatan_rujukan'];
                    $baris = explode("\n", $catatan_rujukan);
                    $baris_bersih = array_map('trim', $baris);
                    $catatan_rujukan = implode("<br>", $baris_bersih);
                    $catatan_rujukan = trim($catatan_rujukan);
                    $catatan_rujukan = json_encode($catatan_rujukan);

                    $sep =  $data['response']['sep']['response'];
                }
                return $this->render('form-update', [
                    'title' => $title,
                    'model' => $model,
                    'options' => $options,
                    'data_rujukan' => $data_rujukan,
                    'peserta' => $peserta,
                    'additional_request' => $additional_request,
                    'catatan_rujukan'=>$catatan_rujukan,
                    'sep' => $sep
                ]);
            }
        // } catch (RequestException $e) {
        //     throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        // } catch (\Exception $e) {
        //     return DocoHelpers::responseTemplate(500, $e->getMessage());
        // }
    }
}