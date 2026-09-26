<?php

namespace Doco\pendaftaran\controllers;

use Yii;
use yii\web\Response;
use app\components\DocoConstants;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use app\components\Services\Contracts\BpjsInterface;
use app\modules\pendaftaran\models\UpdateTanggalPulangForm;
use app\modules\v1\models\UpdateTanggalPulangView;

class UpdateTanggalPulangController extends DocoController
{
    protected $_title = 'Update Tanggal Pulang';
    protected $_restPendaftaran;
    protected $_module = 'update-rencana-pulang/';
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
            $result = $this->helper->guzzleExec($this->_restPendaftaran, [
                'url' => 'update-tanggal-pulang/index',
                'methode' => 'get',
            ]);
            return $this->render('index', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

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

            if (isset($yiiRestfulParams['advanced-filter']['tglpasienpulang'])) {
                $tgl_kunjungan_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tglpasienpulang']);
                $tgl_awal = $tgl_kunjungan_range[0];
                $tgl_akhir = $tgl_kunjungan_range[1];
                $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
                $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
                $yiiRestfulParams['advanced-filter']['tgl_pulang_awal'] = $tgl_awal_format;
                $yiiRestfulParams['advanced-filter']['tgl_pulang_akhir'] = $tgl_akhir_format;
                unset($yiiRestfulParams['advanced-filter']['tgl_pulang']);
            }

            $body = $this->helper->guzzleExec($this->_restPendaftaran, [
                'url' => 'update-tanggal-pulang/index',
                'payload' => [
                    'query' => $yiiRestfulParams
                ]
            ]);
            $data = $body['data'];
            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['bpjs_id']);
                    $value['primary'] = $primaryKey;
                    $value['no'] = $no;
                    $value['tglpasienpulang'] = DocoHelpers::convDateTime($value['tglpasienpulang'], false, false);
                    $data[$key] = $value;
                }

                $result['data'] = $data;
                $result['recordsTotal'] = $body['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['_meta']['totalCount'];

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

    public function actionGetInfoPeserta()
    {
        $request = Yii::$app->request;
        $bpjs_id = $request->get('bpjs_id', null);
        try 
        {
            $response = $this->helper->guzzleExec($this->_restPendaftaran, [
                'url' => 'update-tanggal-pulang/get-info-peserta',
                'payload' => [
                    'query' => [
                        'bpjs_id' => $bpjs_id,
                    ]
                ]
            ]);
            return json_encode($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionUpdate($id)
    {
        try {
            $request = Yii::$app->request;
            $update_tanggal_pulang = $tgl_pulang = $pasien = [];
            $title = 'Ubah '.$this->_title;
            $model = new UpdateTanggalPulangForm;
            $bpjs_id = DocoHelpers::decrypt($id);

            if ($request->post()){
                $model->load($request->post());
                $model['user'] = Yii::$app->docoVars->user("nama");
                if ($model->validate()) {
                    $response = $this->helper->guzzleExec($this->_restPendaftaran, [
                        'url' => 'update-tanggal-pulang/update-tanggal-pulang?bpjs_id='.$bpjs_id,
                        'method' => 'post',
                        'payload' => [
                            'form_params' => $model->attributes
                        ]
                    ]);
                    if ($response['result']['metaData']['code'] > 200 || $response['result']['metaData']['code'] != '200')
                    {
                        return DocoHelpers::responseTemplate(
                            500,
                            'Error',
                            [],
                            [
                                'title' => Yii::t('fe', 'Peringatan!'),
                                'text' => $response['result']['metaData']['message'],
                                'message' => $response['result']['metaData']['message'],
                            ]
                        );
                    }
                    return DocoHelpers::response($response);
                }

                return DocoHelpers::response($model->errors,422,'UpdateTanggalPulangForm');
            } else {
                $result = $this->helper->guzzleExec($this->_restPendaftaran, [
                    'url' => 'update-tanggal-pulang/get-data-update',
                    'payload' => [
                        'query' => [
                            'bpjs_id' => $bpjs_id
                        ]
                    ]
                ]);
                
                $model->pendaftaran_id = ArrayHelper::getValue($result['data_updat_tanggal_pulang'],'pendaftaran_id', null);
                $model->pasienadmisi_id = ArrayHelper::getValue($result['data_updat_tanggal_pulang'],'pasienadmisi_id', null);
                $model->pasienpulang_id = ArrayHelper::getValue($result['data_updat_tanggal_pulang'],'pasienpulang_id', null);
                $model->bpjs_id = ArrayHelper::getValue($result['data_updat_tanggal_pulang'],'bpjs_id', null);

                if ($result['data_bpjs']['nosep'] != null ) {
                    $model->nosep = ArrayHelper::getValue($result['data_bpjs'], 'nosep', null);
                }else {
                    $model->nosep = ArrayHelper::getValue($result['data_updat_tanggal_pulang'],'nosep', null);
                }

                if ($result['data_bpjs']['status_pulang'] != null ) {
                    $model->status_pulang_id = ArrayHelper::getValue($result['data_bpjs'], 'status_pulang', null);
                }else {
                    $model->status_pulang_id = ArrayHelper::getValue($result['data_pasien_pulang'],'carakeluar_id', null);
                }

                if ($result['data_bpjs']['no_surat_meninggal'] != null ) {
                    $model->no_surat_kematian = ArrayHelper::getValue($result['data_bpjs'], 'no_surat_meninggal', null);
                }else {
                    $model->no_surat_kematian = ArrayHelper::getValue($result['data_updat_tanggal_pulang'],'no_surat_kematian', null);
                }
                
                if ($result['data_bpjs']['tgl_meninggal_bpjs'] != null ) {
                    $date_kematian = ArrayHelper::getValue($result['data_bpjs'],'tgl_meninggal_bpjs', null);
                }else {
                    $date_kematian = ArrayHelper::getValue($result['data_updat_tanggal_pulang'],'tgl_meninggal', null);
                }

                if ($result['data_bpjs']['tglpulang'] != null ) {
                    $date_pulang = ArrayHelper::getValue($result['data_bpjs'],'tglpulang', null);
                }else {
                    $date_pulang = ArrayHelper::getValue($result['data_updat_tanggal_pulang'],'tglpasienpulang', null);
                }

                $model->tgl_meninggal = ($date_kematian != null) ? date('d-m-Y', strtotime($date_kematian)) : null;
                $model->tglpasienpulang = ($date_pulang != null) ? date('d-m-Y', strtotime($date_pulang)) : null;

                if ($result['data_bpjs']['no_lp_manual'] != null ) {
                    $model->no_up_manual = ArrayHelper::getValue($result['data_bpjs'],'no_lp_manual', null);
                }else {
                    $model->no_up_manual = ArrayHelper::getValue($result['data_updat_tanggal_pulang'],'no_up_manual', null);
                }


                $data_vclaim = $this->bpjsService->cariSep($result['data_updat_tanggal_pulang']['nosep']);
                $status_pulang = ArrayHelper::map($result['status_pulang_option'], 'lookup_value', 'lookup_name');
            }

            return $this->render('update', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }
}
