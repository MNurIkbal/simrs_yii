<?php

namespace Doco\penjaminasuransi\controllers;

use Yii;
use yii\web\Response;
use app\components\DHtml;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;
use Doco\penjaminasuransi\models\SuratKeteranganDokterForm;
use yii\helpers\ArrayHelper;

class InformasiPasienNonBpjsController extends DocoController
{
    protected $_title;
    protected $_restRajal;
    protected $_restMaster;
    protected $_restPenjaminAsuransi;
    protected $_module = '/penjaminasuransi/informasi-pasien-non-bpjs/';

    private $_id_diagnosa_utama    = DocoConstants::DIAGNOSA_UTAMA;
    private $_id_diagnosa_penyerta = DocoConstants::DIAGNOSA_PENYERTA;

    public function init()
    {
        parent::init();

        $this->_title                = 'Pasien Jaminan Asuransi';
        $this->_restRajal            = Yii::$app->docoRest->rajal;
        $this->_restMaster           = Yii::$app->docoRest->master;
        $this->_restPenjaminAsuransi = Yii::$app->docoRest->penjaminasuransi;
    }

    public function actionIndex()
    {
        $response = $this->_restPenjaminAsuransi->get('informasi-pasien-non-bpjs/generate-api');
        $response = json_decode($response->getBody(), true);
        $response = $response['response'];
        $response['penjamin'] = [];
        $response['ruangan'] = [];

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $request          = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

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
            $response = $this->_restPenjaminAsuransi->get('informasi-pasien-non-bpjs/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body     = json_decode($response->getBody(), true);
            $no       = $request->get('start', 1);
            
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey                  = DocoHelpers::encrypt($value['pendaftaran_id']);
                $value['pasienadmisi_id']    = $value['pasienadmisi_id'] ? $value['pasienadmisi_id'] : 0;
                $value['admisi']             = DocoHelpers::encrypt($value['pasienadmisi_id']);
                $pembayaranId                = ArrayHelper::getValue($value, 'pembayaran_id');
                $value['pembayaran_id']      = DocoHelpers::encrypt($pembayaranId);
                $value['primary']            = $primaryKey;
                $value['pendaftaran_id']     = $primaryKey;
                unset($value['pasienadmisi_id']);
                $value['rowNum']             = $no;
                $value['tgl_pendaftaran']    = date('d M Y', strtotime(ArrayHelper::getValue($value, 'tgl_pendaftaran')));
                $value['tglpasienpulang']    = !empty($value['tglpasienpulang']) ? date('d M Y', strtotime($value['tglpasienpulang'])) : '';
                $totalTagihan = isset($value['total_ditagihkan']) ? $value['total_ditagihkan'] : $value['total_tagihan'];
                $value['total_tagihan']      = 'Rp.'.DocoHelpers::formatNumber($totalTagihan, 0);
                $value['total_sdh_bayar']    = 'Rp.'.DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'total_sdh_bayar'), 0);
                $value['total_asuransi']     = 'Rp.'.DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'total_asuransi'), 0);
                $value['total_sisa_tagihan'] = 'Rp.'.DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'total_sisa_tagihan'), 0);
                $value['jumlah_pembayaran']  = 'Rp.'.DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'jumlah_pembayaran'), 0);
                $value['total_discountpembayaran']  = 'Rp.'.DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'total_discountpembayaran'), 0);
                $data[$key]                  = $value;
            }

            $result['data']            = $data;
            $result['recordsTotal']    = $body['response']['_meta']['totalCount'];
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

    public function actionGetPenjamin($assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $params = '';
        if ($request->post()) {
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
            $params = '?id='.$parent_label;
        }

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restMaster->get('allow/get-list-penjamin'. $params);
            $body = json_decode($response->getBody(), True);
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

    public function actionGetCarabayar($assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restPenjaminAsuransi->get('allow/get-list-carabayar?penjamin_id='.$parent_label);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value)
                $result['output'][] = [
                    'id' => $value['carabayar_id'],
                    'name' => $value['carabayar_nama']
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

    public function actionGetInstalasi($assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restPenjaminAsuransi->get('allow/get-list-instalasi?id='.$parent_label);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value)
                $result['output'][] = [
                    'id' => $value['instalasi_id'],
                    'name' => $value['instalasi_nama']
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

    public function actionGetRuangan($assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $params = '';
        if ($request->post()) {
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
            $params = '?instalasi_id='.$parent_label;
        }

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restPenjaminAsuransi->get('allow/get-list-ruangan'. $params);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value)
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

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $request          = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $url              = 'informasi-pasien-non-bpjs/export-excel?'.http_build_query($yiiRestfulParams);
        $path             = Yii::getAlias("@download") . "/Informasi Pasien Jaminan Asuransi.xlsx";

        try {
            $this->_restPenjaminAsuransi->get($url, [
                'save_to' => $path,
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
        $request = Yii::$app->request;

        try {
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path             = Yii::getAlias("@download") . "/Informasi Pasien Jaminan Asuransi.pdf";

            $this->_restPenjaminAsuransi->get('informasi-pasien-non-bpjs/export-pdf?'.http_build_query($yiiRestfulParams), [
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

    public function actionSkd($id, $admisi)
    {
        $title = 'Surat Keterangan Dokter';
        $id_dec = DocoHelpers::decrypt($id);
        $admisi_dec = DocoHelpers::decrypt($admisi);

        $model = new SuratKeteranganDokterForm;
        try {
            $response = $this->_restPenjaminAsuransi->get('informasi-pasien-non-bpjs/get-attributes',[
                'query' => [
                    'id' => $id_dec,
                    'admisi' => $admisi_dec
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $response = $response['response'];
            $data = $response['header'];
            $skd = $response['skd'];
            $model->attributes = $skd;
            $sebab = $response['sebab'];
            $dataDetail = $response['detail'];
            $defaultDokter = $response['default_dokter'];
            $detail = [];

            foreach ($dataDetail as $val) {
                if ($val["kelompokdiagnosa_id"] == $this->_id_diagnosa_utama) {
                    $detail["Utama"]["id"] = $val["diagnosa_id"];
                    $detail["Utama"]["text"] = $val["diagnosa_kode"]."-".$val["diagnosa_nama"];
                }

                if ($val["kelompokdiagnosa_id"] == $this->_id_diagnosa_penyerta) {
                    $detail["Peyerta"][] = [
                        "id" => $val["diagnosa_id"],
                        "text" => $val["diagnosa_kode"]."-".$val["diagnosa_nama"]
                    ];
                }
            }
            // if (count($dataDetail)) {
            //     $penyerta = json_decode($dataDetail['diagnosa_penyerta'],true);
            //     $detail = [
            //         'Utama' => json_decode($dataDetail['diagnosa_utama'],true),
            //         'Peyerta' => $penyerta,
            //     ];
            // }
        } catch (RequestException $e) {
            $data = $skd = $sebab = $detail = $defaultDokter = [];
        }
        return $this->render('form',get_defined_vars());
    }

    public function actionGetIcd()
    {
        $request = Yii::$app->request;
        $type_icd = $request->get('type');
        $term = $request->get('term');
        try {
            $response = $this->_restPenjaminAsuransi->get('informasi-pasien-non-bpjs/get-icd',[
                'query' => [
                    'type' => $type_icd,
                    'term' => $term,
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $response = $response['response'];
            $data = [];
            foreach ($response as $value) {
                $data[] = [
                    'id' => $value['diagnosa_id'],
                    'text' => $value['diagnosa_kode'] . ' - ' . $value['diagnosa_namalainnya'],
                    'kode' => $value['diagnosa_kode'],
                ];
            }
        } catch (RequestException $e) {
            $data = [];
        }

        return DocoHelpers::response([
            'result' => $data
        ]);
    }

    public function actionGetDokter()
    {
        $request = Yii::$app->request;
        $term = $request->get('term');
        try {
            $response = $this->_restPenjaminAsuransi->get('allow/pegawai-medis',[
                'query' => [
                    'term' => $term,
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $response = $response['response'];
            $data = [];
            foreach ($response as $value) {
                $data[] = [
                    'id' => $value['pegawai_id'],
                    'text' => $value['nama_pegawai'],
                ];
            }
        } catch (RequestException $e) {
            $data = [
                'messages' => $e->getMessage()
            ];
        }

        return DocoHelpers::response([
            'result' => $data
        ]);
    }

    public function actionSave($id, $admisi)
    {
        $request = Yii::$app->request;
        $model = new SuratKeteranganDokterForm;
        $model->load($request->post());
        $diagTambahan = json_decode($request->post('list_diagnosa'),true);
        $model->diag_tambahan = count($diagTambahan) ? json_encode($diagTambahan) : null;
        if ($model->validate()) {
            $id = DocoHelpers::decrypt($id);
            $admisi = DocoHelpers::decrypt($admisi);
            $sebabDiagnosa = [];
            if (is_array($model->sebebdiagnosa_id)) {
                foreach ($model->sebebdiagnosa_id as $value) {
                    if (!empty($value)) {
                        $sebabDiagnosa[] = $value;
                    }
                }
            }
            $model->sebebdiagnosa_id = json_encode($sebabDiagnosa);
            $model->diag_utama = $request->post('diagnosa_utama');
            $model->instalasi_id = $request->post('instalasi_id');
            $model->pasien_id = $request->post('pasien_id');
            $model->tgl_kecelakaan = !empty($model->tgl_kecelakaan) ? date('Y-m-d',strtotime($model->tgl_kecelakaan)) : null;
            $model->tgl_diagnosa = !empty($model->tgl_diagnosa) ? date('Y-m-d',strtotime($model->tgl_diagnosa)) : null;
            $model->tgl_konsul = !empty($model->tgl_konsul) ? date('Y-m-d',strtotime($model->tgl_konsul)) : null;
            $model->tgl_gejala = !empty($model->tgl_gejala) ? date('Y-m-d',strtotime($model->tgl_gejala)) : null;
            if (empty($model->is_kecelakaaan)) {
                $model->tgl_kecelakaan = null;
                $model->sebab_kecelakaan = null;
            }

            $model->tgl_diag_sama = !empty($model->tgl_diag_sama) ? date('Y-m-d',strtotime($model->tgl_diag_sama)) : null;
            if (empty($model->is_diag_sama)) {
                $model->tgl_diag_sama = null;
                $model->diag_sama = null;
                $model->nama_rs = null;
                $model->nama_dokter_rs = null;
            }

            $model->tgl_konsultasi = !empty($model->tgl_konsultasi) ? date('Y-m-d',strtotime($model->tgl_konsultasi)) : null;
            if (empty($model->is_konsultasi)) {
                $model->tgl_konsultasi = null;
                $model->diag_konsultasi = null;
                $model->nam_rs_konsul = null;
                $model->nama_dr_konsul = null;
            }

            if (empty($model->is_rujukan)) {
                $model->dokter_rujukan = null;
                $model->alamat = null;
            }

            try {
                $response = $this->_restPenjaminAsuransi->post('informasi-pasien-non-bpjs/save',[
                    'form_params' => $model->attributes,
                    'query' => [
                        'id' => $id,
                        'admisi' => $admisi,
                    ]
                ]);
                $response = json_decode($response->getBody(),true);
                return DocoHelpers::response($response, false, 'SuratKeteranganDokterForm');
            } catch (RequestException $e) {
                return DocoHelpers::response([
                    'messages' => $e->getMessage()
                ],500);
            }

        } else {
            return DocoHelpers::response([
                'response' => [
                    'data' => $model->errors
                ]
            ],422,'SuratKeteranganDokterForm');
        }
    }

    public function actionCetakSkd($id)
    {
        $id = DocoHelpers::decrypt($id);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        try {
            $path = Yii::getAlias("@download") . "/informasi-pasien-non-bpjs.pdf";
            $response = $this->_restPenjaminAsuransi->get('informasi-pasien-non-bpjs/cetak-skd',[
                'query' => [
                    'id' => $id
                ],
                'save_to' => $path
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

    public function actionPrintRincian($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download")."/detail-rincian.pdf";
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restPenjaminAsuransi->get('informasi-pasien-non-bpjs/print-rincian',
                [
                    'query' => [
                        'id' => $id
                    ],
                    'save_to' => $path
                ]
            );
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}