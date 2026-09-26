<?php

/**
 * @Author: doconb-121
 * @Date:   2018-09-25 17:21:16
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-28 13:53:11
 */

namespace Doco\penjaminasuransi\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DHtml;
use GuzzleHttp\Exception\RequestException;
use app\components\Services\Penjamin\GetInitFilterService;
use app\components\DocoConstants;
use Doco\penjaminasuransi\models\PenerimaanPembayaranForm;
use Doco\penjaminasuransi\models\AlokasiPengajuanForm;
use Doco\penjaminasuransi\models\TransaksiAlokasiImportForm;

class TransaksiPenerimaanPembayaranController extends DocoController
{
    protected $_title;
    protected $_restPenjamin;
    protected $_module = '/penjaminasuransi/informasi-pasien-non-bpjs/';

    public function init()
    {
        parent::init();
        $this->_title = Yii::t('fe', 'Penerimaan Pembayaran Klaim');
        $this->_restPenjamin = Yii::$app->docoRest->penjaminasuransi;
    }

    public function actionIndex()
    {
        $session = Yii::$app->session;
        $session['data-penerimaan'] = [];
        $title = $this->_title;
        $model = new PenerimaanPembayaranForm;
        $modelAjuan = new AlokasiPengajuanForm;
        $model->tgl_terimabayarklaim = date('Y-m-d H:i:s');
        $carabayar = [];
        $defaultPenjaminBpjs = null; 
        try {
            $response = (new GetInitFilterService)->execute();
            $response['penjamin'] = [];
            $responseCaraBayar = ArrayHelper::getValue($response, 'cara_bayar');
            $carabayar = [];
            foreach ($responseCaraBayar as $key => $value) {
                $carabayar[] = [
                    'carabayar_id' => $value['carabayar_id'],
                    'carabayar_nama' => $value['carabayar_nama']
                ];
            }
        } catch (RequestException $e) {
            $carabayar = [];
        } catch (\Exception $e) {
            $carabayar = [];
        }
        return $this->render('index', get_defined_vars());
    }

    public function actionGetSession($type = 1)
    {
        $session = Yii::$app->session;
        $request = Yii::$app->request;
        $result = [];
        $data = [];
        $draw = $request->get('draw', 1);
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        $sessionName = ($type == 1) ? 'data-penerimaan' : 'data-penerimaan-bpjs';
        if (!isset($session[$sessionName])) {
            return DocoHelpers::response($result);
        }

        $rawData = $session[$sessionName];
        $no = 1;
        foreach ($rawData as $key => $value) {
            $newData = $value;
            $totalPengajuan = ArrayHelper::getValue($newData, 'total_pengajuan', 0);
            $totalTerbayar = ArrayHelper::getValue($newData, 'total_terbayar', 0);
            $pembayaran = ArrayHelper::getValue($newData, 'pembayaran', 0);
            $totalSisaPiutang = ArrayHelper::getValue($newData, 'total_sisapiutang', 0);
            $newData['rowNum'] = $no;
            $newData['total_pengajuan'] = DocoHelpers::rupiahDisplay($totalPengajuan);
            $newData['total_terbayar'] = DocoHelpers::rupiahDisplay($totalTerbayar);
            $newData['pembayaran'] = DocoHelpers::rupiahDisplay($pembayaran);
            $newData['total_sisapiutang'] = DocoHelpers::rupiahDisplay($totalSisaPiutang);
            $newData['aksi'] = '<button type="button" data-key="' . $key . '" class="btn btn-danger btn-xs btn-hapus"><i class="fa  fa-trash"></i></button>';
            $data[] = $newData;
            $no++;
        }
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = count($data);
        $result['recordsFiltered'] = count($data);
        return DocoHelpers::response($result);
    }

    public function actionGetPegawai()
    {
        if (isset($_GET['q']['term']) && !empty($_GET['q']['term'])) {
            $response = $this->_restPenjamin->request('POST', 'allow/get-pegawai', [
                'form_params' => ['term' => $_GET['q']['term'], 'ruangan_id' => Yii::$app->docoVars->workspace("ruangan_id")],
            ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = ['id' => $value['pegawai_id'], 'text' => $value['nama_pegawai']];
            }
            $total = count($body['response']);
            $return = ['result' => $data, 'total_count' => $total, 'incomplete_results' => false];
            return DocoHelpers::response($return);
        }
    }

    public function actionGetAjuan()
    {
        $request = Yii::$app->request;
        $term = $request->get('term', null);
        $type = $request->get('type', null);
        $page = $request->get('page', 1);
        $additionalPayload = $request->get('additionalPayload', []);
        $penjamin_id = $request->get('id', null);
        $rawData = [];

        $response = $this->guzzleExec($this->_restPenjamin, [
            'url' => 'transaksi-penerimaan-pembayaran/get-pengajuan',
            'payload' => [
                'query' => [
                    'term' => $term,
                    'type' => $type,
                    'page' => $page,
                    'additionalPayload' => $additionalPayload,
                    'penjamin_id' => $penjamin_id
                ]
            ],
        ]);

        foreach ($response as $key => $value) {
            $data[] = ['id' => $value['pengajuanklaim_id'], 'text' => $value['no_pengajuanklaim']];
            $rawData[$value['pengajuanklaim_id']] = $value;
        }

        $total = count($response);

        $resData = [
            'result' => $response,
            'pagination' => [
                'more' => count($response) == 10 ? true : false,
            ],
            'rawData' => $rawData,
            'total_count' => $total
        ];

        return $this->responseJson(200, 'Data berhasil diambil!', $resData);
    }

    public function actionGetPenjamin()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restPenjamin->get('allow/get-penjamin?id=' . $parent_label);
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

    public function actionAddAjuan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $model = new AlokasiPengajuanForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $model->load($post);
        if ($model->validate()) {
            $data = [$model->attributes];
            $session = Yii::$app->session;
            $sessionData = isset($session['data-penerimaan']) ? $session['data-penerimaan'] : [];
            if (count($sessionData) > 0) {
                $arr = $sessionData;
                $arr = array_merge($arr, $data);
                $session['data-penerimaan'] = $arr;
            } else {
                $session['data-penerimaan'] = $data;
            }
            return json_encode($session['data-penerimaan']) ;
        } else {
            $errors = DocoHelpers::parseError($model->errors, $formName);
            return DocoHelpers::responseTemplate(422, 'Error', $errors);
        }
    }

    public function actionHapusAjuan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $session = Yii::$app->session;
        if (isset($post['key'])) {
            $sessionData = $session['data-penerimaan'];
            unset($sessionData[$post['key']]);
            $session['data-penerimaan'] = array_values($sessionData);;
            return json_encode($session['data-penerimaan']);
        }
    }

    public function actionSave()
    {
        $request = Yii::$app->request;
        $model = new PenerimaanPembayaranForm;
        $model->load($request->post());
        $session = Yii::$app->session;
        $randString = $request->get('randString');
        $dataPengajuan = [];
        if(!empty($model->carabayar_id)) {
            $dataPengajuan = isset($session['data-penerimaan']) ? $session['data-penerimaan'] : [];
        }
        $model->data_pengajuan = json_encode($dataPengajuan);
        if ($model->validate()) {
            try {
                $model->no_terimabayarklaim = trim($model->no_terimabayarklaim);
                $model->total_terimabayar = str_replace('.', '', $model->total_terimabayar);
                $response = $this->_restPenjamin->post('transaksi-penerimaan-pembayaran/save', [
                    'form_params' => $model->attributes,
                    'query' => [
                        'randString' => $randString
                    ]
                ]);
                $response = json_decode($response->getBody(), true);
                if ($response['metadata']['status'] == 200) {
                    Yii::$app->session['data-penerimaan'] = [];
                    Yii::$app->session['data-penerimaan-bpjs'] = [];
                }
                return DocoHelpers::response($response, false, 'PenerimaanPembayaranForm');
            } catch (RequestException $e) {
                return DocoHelpers::response([
                    'text' => $e->getMessage()
                ], 422);
            }
        } else {
            return DocoHelpers::response($model->errors, 422, 'PenerimaanPembayaranForm');
        }
    }

    public function actionResetSession()
    {
        Yii::$app->session['data-penerimaan'] = [];
        return;
    }

    public function actionCetakPenerimaan()
    {
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $noPembayaran = $request->get('no_pembayaran', null);
        if(!empty($id)) {
            $id = DocoHelpers::decrypt($id);
        }
        if(!empty($noPembayaran)) {
            $noPembayaran = DocoHelpers::decrypt($noPembayaran);
        }
        try {
            $path = Yii::getAlias("@download") . "/penerimaan-pembayaran.pdf";
            $response = $this->_restPenjamin->get('transaksi-penerimaan-pembayaran/cetak-penerimaan', [
                'save_to' => $path,
                'query' => [
                    'id' => $id,
                    'no_pembayaran' => $noPembayaran
                ]
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

    public function actionUploadTemplateProcess()
    {
        $request = Yii::$app->request;
        $model = new TransaksiAlokasiImportForm();
        $model->upload_file = UploadedFile::getInstance($model, 'upload_file');
        if ($model->upload_file == NULL) {
            return $this->sendErrorResponse();
        }
        if($model->validate()) {
            return true;
        }
        else {
            return $this->sendErrorResponse();
        }
    }

    private function convertExcelImportToDatas($dirtyExcelData)
    {
        $datas = [];
        $counterNumber = 1;
        $totalDisetujui = $totalPengajuan = 0;
        $valid = false;
        for ($row = 1; $row <= $dirtyExcelData['highestRow']; $row++) {
            $rowData = $dirtyExcelData['sheet']->rangeToArray('B' . $row . ':' . $dirtyExcelData['highestColumn'] . $row, Null, true, false);
            $data = ArrayHelper::getValue($rowData, '0');
            $columnNoSep = ArrayHelper::getValue($data, '0');
            $columnNoSep = strtolower($columnNoSep);
            $columnTglVerifikasi = ArrayHelper::getValue($data, '1');
            $columnTglVerifikasi = strtolower($columnTglVerifikasi);
            $columnRiilRs = ArrayHelper::getValue($data, '2');
            $columnRiilRs = strtolower($columnRiilRs);
            $columnDiajukan = ArrayHelper::getValue($data, '3');
            $columnDiajukan = strtolower($columnDiajukan);
            $columnDisetujui = ArrayHelper::getValue($data, '4');
            $columnDisetujui = strtolower($columnDisetujui);
            if($columnNoSep == 'no.sep' && $columnTglVerifikasi == 'tgl. verifikasi' && $columnRiilRs == 'riil rs' && $columnDiajukan == 'diajukan' && $columnDisetujui == 'disetujui') {
                $valid = true;
            }
        }

        if(!$valid) {
            return [];
        }

        for ($row = 3; $row <= $dirtyExcelData['highestRow']; $row++) {
            $rowData = $dirtyExcelData['sheet']->rangeToArray('B' . $row . ':' . $dirtyExcelData['highestColumn'] . $row, Null, true, false);
            foreach ($rowData as $key => $value) {
                $noSep = ArrayHelper::getValue($value, '0');
                if(!empty($noSep)) {
                    $diajukan = ArrayHelper::getValue($value, '3', 0);
                    $disetujui = ArrayHelper::getValue($value, '4', 0);
                    $tglVerifikasi = ArrayHelper::getValue($value, '1');
                    if(!empty($tglVerifikasi)) {
                        $tglVerifikasi = ($value['1'] - 25569) * 86400;
                        $tglVerifikasi = gmdate("Y-m-d", $tglVerifikasi);
                    }
                    $item = [
                        'no' => $counterNumber,
                        'no_pengajuan' => '-',
                        'no_sep' => $noSep,
                        'tgl_verifikasi' => $tglVerifikasi,
                        'riil_rs' => ArrayHelper::getValue($value, '2'),
                        'diajukan' => $diajukan,
                        'disetujui' => $disetujui,
                    ];
                    $datas[] = $item;
                    $totalPengajuan += $diajukan;
                    $totalDisetujui += $disetujui;
                    $counterNumber++;
                }
            }
        }
        return [
            'data' => $datas,
            'total_pengajuan' => $totalPengajuan,
            'total_disetujui' => $totalDisetujui,
        ];
    }

    private function sendSuccessResponse($filename, $datas, $totalDisetujui)
    {
        $response['response']['file'] = $filename;
        $response['response']['data'] = $datas;
        $response['response']['total_disetujui'] = $totalDisetujui;
        $response['response']['status'] = 200;
        $response['response']['title'] = 'Proses Berhasil';
        $response['response']['text'] = 'Data Berhasil di upload!';
        return DocoHelpers::response($response, 200);
    }

    private function sendErrorResponse($msg = 'Format yang di Upload tidak sesuai, harus berupa xls, xlsx')
    {
        $response['response']['file'] = '';
        $response['response']['data'] = [];
        $response['response']['status'] = 422;
        $response['response']['title'] = 'Proses Gagal';
        $response['response']['text'] = $msg;
        return DocoHelpers::response($response, 422);
    }

    private function sendErrorFormatResponse($msg = 'Penamaan Kolom di Excel Tidak Sesuai Format.')
    {
        $response['response']['file'] = '';
        $response['response']['data'] = [];
        $response['response']['status'] = 422;
        $response['response']['title'] = 'Proses Gagal';
        $response['response']['text'] = $msg;
        return DocoHelpers::response($response, 422);
    }

    public function actionProcessSync()
    {
        $request = Yii::$app->request;
        $model = new TransaksiAlokasiImportForm();
        $model->upload_file = UploadedFile::getInstance($model, 'upload_file');
        if ($model->upload_file == NULL) {
            return $this->sendErrorResponse();
        }
        if($model->validate()) {
            $response = $this->_restPenjamin->post('transaksi-penerimaan-pembayaran/convert-excel', [
                'query' => [
                    'filePath' => $model->upload_file->name,
                    'randString' => $request->get('randString'),
                ],
                'multipart' => [
                    [
                        'name' => 'upload_file',
                        'contents' => file_get_contents($model->upload_file->tempName),
                        'filename' => $model->upload_file->name
                    ],
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            return DocoHelpers::response($response);
        }
        else {
            return $this->sendErrorResponse();
        }
    }

    public function actionUpload()
    {
        return $this->renderAjax('modal-upload', get_defined_vars());
    }
}
