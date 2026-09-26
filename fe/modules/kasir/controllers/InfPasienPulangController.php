<?php
// Author : Ramdhan Nurrachman

namespace Doco\kasir\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoConstants;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\components\DHtml;
use app\modules\kasir\models\PlafonBpjsForm;

class InfPasienPulangController extends DocoController
{
    protected $_title = "Informasi Pasien Pulang";
    protected $_module = 'kasir/inf-pasien-pulang/';
    protected $_restKasir; protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restKasir = Yii::$app->docoRest->kasir; $this->_restMaster = Yii::$app->docoRest->master;
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
        $title = $this->_title;
        $response = $this->_restKasir->get('inf-pasien-pulang/get-api');
        $body = json_decode($response->getBody(), TRUE);
        $resMaster = $body['response'];
        $newData = [];
        
        $btnEditTagihan = Yii::$app->docoPlugin->execute($this, 'invoice_edit_tagihan');
        $btnEditTagihanDetail = Yii::$app->docoPlugin->execute($this, 'invoice_edit_tagihan_detail');

        return $this->render('index', get_defined_vars());
    }

    // Detail
    public function actionDetail($id)
    {
        $cacheItem = Yii::$app->cache;
        // Try catch
        try {
            $dataLab = [];
            $dataRadiologi = [];
            $dataObat = [];
            $arrRuangan = [];
            $dataTindakan = [];
            $dataTindakanRj = [];
            $dataTindakanRi = [];
            $dataTindakanRd = [];
            $dataTindakanGudang = [];
            $dataTindakanRehab = [];
            $dataTindakanRm = [];
            $dataTindakanKasir = [];
            $dataTindakanInformasi = [];
            $dataTindakanPendaftaran = [];
            $dataTindakanBedah = [];
            $dataTindakanAmbulan = [];
            // Get data
            $response = $this->_restKasir->get('inf-pasien-pulang/view?id='.DocoHelpers::decrypt($id));
            $body = json_decode($response->getBody(), TRUE);

            $data_all = $body['response'];
            $data = $data_all['header'];
            // var_dump($data);die;
            $data_detail = $data_all['detail'];
            //list data detail
            for ($i=0; $i < count($data_detail) ; $i++) {
                // Cek obat
                if ($data_detail[$i]['is_obat']==false) {
                    if ($data_detail[$i]['instalasi_pelayanan'] == 'Rawat Jalan' || $data_detail[$i]['instalasi_pelayanan'] == 'Rawat Darurat' || $data_detail[$i]['instalasi_pelayanan'] == 'Rawat Inap') {
                        if(!isset($dataTindakan['info']['pendaftaran_id'])){
                            $dataTindakan['info']['pendaftaran_id'] = $data_detail[$i]['pendaftaran_id'];
                        }
                        $dataTindakan[str_replace(' ', '-', $data_detail[$i]['ruangan_pelayanan'])][] = $data_detail[$i];
                    }

                    // Cek instalasi
                    // if ($data_detail[$i]['instalasi_pelayanan'] == 'Rawat Jalan') {
                    //     $dataTindakanRj[] = $data_detail[$i];
                    // }
                    // elseif ($data_detail[$i]['instalasi_pelayanan'] == 'Rawat Darurat') {
                    //     $dataTindakanRd[] = $data_detail[$i];
                    // }
                    if ($data_detail[$i]['instalasi_pelayanan'] == 'Laboratorium') {
                        $dataLab[] = $data_detail[$i];
                    }
                    elseif ($data_detail[$i]['instalasi_pelayanan'] == 'Radiologi') {
                        $dataRadiologi[] = $data_detail[$i];
                    }
                    elseif ($data_detail[$i]['instalasi_pelayanan'] == 'Gudang Farmasi') {
                        $dataTindakanGudang[] = $data_detail[$i];
                    }
                    elseif ($data_detail[$i]['instalasi_pelayanan'] == 'Rehabilitasi Medik') {
                        $dataTindakanRehab[] = $data_detail[$i];
                    }
                    elseif ($data_detail[$i]['instalasi_pelayanan'] == 'Rekam Medik') {
                        $dataTindakanRm[] = $data_detail[$i];
                    }
                    elseif ($data_detail[$i]['instalasi_pelayanan'] == 'Kasir') {
                        $dataTindakanKasir[] = $data_detail[$i];
                    }
                    elseif ($data_detail[$i]['instalasi_pelayanan'] == 'Informasi') {
                        $dataTindakanInformasi[] = $data_detail[$i];
                    }
                    elseif ($data_detail[$i]['instalasi_pelayanan'] == 'Pendaftaran & Penjadwalan') {
                        $dataTindakanPendaftaran[] = $data_detail[$i];
                    }
                    elseif ($data_detail[$i]['instalasi_pelayanan'] == 'Bedah Sentral') {
                        $dataTindakanBedah[] = $data_detail[$i];
                    }
                    elseif ($data_detail[$i]['instalasi_pelayanan'] == 'Ambulan') {
                        $dataTindakanAmbulan[] = $data_detail[$i];
                    }
                    // else {
                    //     $dataTindakanRi[] = $data_detail[$i];
                    // }
                }
                else {
                  $dataObat[] = $data_detail[$i];
                }
            }

            if($dataTindakan){
                $arrRuangan = array_keys($dataTindakan);
                unset($arrRuangan[0]);
                $cacheItem->set('detail-pasien-pulang-'.DocoHelpers::decrypt($id), $dataTindakan);
            }

            // Return
            return $this->render('detail', get_defined_vars());
        } catch (RequestException $e) {
            // Return
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            // Return
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $payload = DocoDatatableHelper::advancedFilterParam();
        if (isset($payload['advanced-filter']['is_stopakomodasi']) && $payload['advanced-filter']['is_stopakomodasi'] == '') {
            unset($payload['advanced-filter']['is_stopakomodasi']);
        }

        $response = $this->guzzleExec($this->_restKasir, [
            'url' => "inf-pasien-pulang/index",
            'payload' => [
                'query' => $payload,
            ]
        ]);
        foreach ($response['data'] as $key => $value) {
            $response['data'][$key]['primary'] = DocoHelpers::encrypt($value['pendaftaran_id']);
        }
        $response['recordsTotal'] = $response['_meta']['totalCount'];
        $response['recordsFiltered'] = $response['_meta']['totalCount'];
        return $response;

    }

    // Get data tindakan
    public function actionGetDataTindakan()
    {
        // Try catch
        try {
            // Response
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

            // Get params
            $id = $request->get('id');
            $id = DocoHelpers::decrypt($id);
            $instalasi = $request->get('instalasi');

            // Declare some data
            $draw = $request->get('draw', 1);
            $data = [];

            // Declare result
            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;
            $result['recordsFiltered'] = 0;
            $result["total_biaya"] = 0;

            // Response
            $response = $this->_restKasir->get('inf-pasien-pulang/get-data-tindakan?id='.$id.'&instalasi='.$instalasi.'&'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), true);

            // Number
            $no = $request->get('start',1);

            // Check
            if (isset($body['response']['data']) && !empty($body['response']['data'])) {
                // Loop
                foreach ($body['response']['data'] as $key => $value) {
                    // Counter the number
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['pendaftaran_id']);
                    unset($value['pendaftaran_id']);

                    // Assign tanggal
                    $value['tgl_pelayanan'] = date("j F Y", strtotime($value['tgl_pelayanan']));
                    $value['tarifsatuan'] = $value['tarif_satuan'];
                    $value['tarif_satuan'] = DocoHelpers::formatNumber($value['tarif_satuan']);
                    if($value['cyto_tindakan'] == 1 && ($value['tarifsatuan'] == $value['sub_total'])){
                        $value['sub_total'] = ($value['tarifsatuan']+$value['tarifcyto_tindakan'])*$value['qty'];
                    }
                    $value['sub_total'] = DocoHelpers::formatNumber($value['sub_total']);
                    $value['tarif_cyto'] = ($value['cyto_tindakan']) ? DocoHelpers::formatNumber($value['tarifcyto_tindakan']) : DocoHelpers::formatNumber(0);
                    // Rownum
                    $value['row'] = $no;
                    $value['primary'] = $primaryKey;
                    $data[$key] = $value;
                }
            }

            // Assign result
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['totalCount'];
            $result['recordsFiltered'] = $body['response']['totalCount'];
            $result["total_biaya"] = $body['response']['total'];

            // Return result
            return $result;
        } catch (RequestException $e) {
            // Error
            $result['error'] = $e->getMessage();

            // Return result
            return $result;
        } catch (\Exception $e) {
            // Error
            $result['error'] = $e->getMessage();

            // Return result
            return $result;
        }
    }
    public function actionGetListTindakan($id, $key)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $cacheItem = Yii::$app->cache;
        $request = Yii::$app->request;
        $pageLength = $request->get("length", 100);
        $pageStart = $request->get("start", 0);

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        $result_total_biaya = 0;


        if(!$cacheItem->get('detail-pasien-pulang-'.DocoHelpers::decrypt($id))){
            return $result;
        }
        $no = 0;
        $body = $cacheItem->get('detail-pasien-pulang-'.DocoHelpers::decrypt($id));
        foreach ($body[$key] as $k => $value) {
            $no++;
            $primaryKey = DocoHelpers::encrypt($value['pendaftaran_id']);
            $value['tgl_pelayanan'] = date("j F Y", strtotime($value['tgl_pelayanan']));
            $value['tarifsatuan'] = $value['tarif_satuan'];
            $value['tarif_satuan'] = DocoHelpers::formatNumber($value['tarif_satuan']);
            if($value['cyto_tindakan'] == 1 && ($value['tarifsatuan'] == $value['sub_total'])){
                $value['sub_total'] = ($value['tarifsatuan']+$value['tarifcyto_tindakan'])*$value['qty'];
            }
            $result_total_biaya += $value['sub_total'];
            $value['sub_total'] = DocoHelpers::formatNumber($value['sub_total']);
            $value['tarif_cyto'] = ($value['cyto_tindakan']) ? DocoHelpers::formatNumber($value['tarifcyto_tindakan']) : DocoHelpers::formatNumber(0);
            // Rownum
            $value['row'] = $no;
            $value['primary'] = $primaryKey;
            $data[$k] = $value;
        }
        // return $data;
        $afterSlice = array_slice($data, $pageStart, 5, true);
        $result['data'] = array_values($afterSlice);
        $result['recordsTotal'] = count($data);
        $result['recordsFiltered'] = count($data);
        $result['total_biaya'] = $result_total_biaya;

        return $result;
    }
    // Get data obat
    public function actionGetDataObat()
    {
        // Try catch
        try {
            // Response
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            // Get params
            $id = $request->get('id');
            $id = DocoHelpers::decrypt($id);

            // Declare some data
            $draw = $request->get('draw', 1);
            $data = [];

            // Declare result
            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;
            $result['recordsFiltered'] = 0;
            $result["total_biaya"] = 0;

            // Response
            $response = $this->_restKasir->get('inf-pasien-pulang/get-data-obat?id='.$id.'&'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), true);

            // Number
            $no = $request->get('start',1);

            // Check
            if (isset($body['response']['data']) && !empty($body['response']['data'])) {
                // Loop
                foreach ($body['response']['data'] as $key => $value) {
                    // Counter the number
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['pendaftaran_id']);
                    unset($value['pendaftaran_id']);

                    // Assign tanggal
                    $value['tgl_pelayanan'] = date("j F Y", strtotime($value['tgl_pelayanan']));
                    $value['tarif_satuan'] = DocoHelpers::formatNumber($value['tarif_satuan']);

                    // Rownum
                    $value['row'] = $no;
                    $value['primary'] = $primaryKey;
                    $data[$key] = $value;
                }
            }

            // Assign result
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['totalCount'];
            $result['recordsFiltered'] = $body['response']['totalCount'];
            $result["total_biaya"] = $body['response']['total'];
            // Return result
            return $result;
        } catch (RequestException $e) {
            // Error
            $result['error'] = $e->getMessage();

            // Return result
            return $result;
        } catch (\Exception $e) {
            // Error
            $result['error'] = $e->getMessage();

            // Return result
            return $result;
        }
    }
    public function actionPrint()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $id = $request->get('id');
        $id = DocoHelpers::decrypt($id);
        try {
            $path = Yii::getAlias("@download") . "/cetak-detail-pasien.pdf";
            $response = $this->_restKasir->get('inf-pasien-pulang/export-pdf?id='.$id,[
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            // return DocoHelpers::response($body);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }
    public function actionView($id,$status = null)
    {
        $roleBtnCloseBill = DHtml::cekHakAkses('close-bill');
        $roleBtnCloseBill = ($roleBtnCloseBill) ? 1 : 0;
        return Yii::$app->runAction('/kasir/pembayaran-tagihan',[
            'id' => $id,
            'kelompok' => DocoConstants::PASIEN_PULANG,
            'status' => $status,
            'roleBtnCloseBill' => $roleBtnCloseBill,
        ]);
    }

    //action buat handle data no pendaftaran
    public function actionGetPendaftaran()
    {
        try{
            if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
                $tgl_pendaftaran = '';
                if(isset($_GET['z'])){
                    $tgl_pendaftaran = $_GET['z'];
                }
                $response = $this->_restKasir->request('POST', 'inf-pasien-pulang/get-data-pendaftaran',[
                                'form_params'=>['term'=>$_GET['q']['term'], 'date'=>$tgl_pendaftaran],
                            ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id'=>$value['pendaftaran_id'],'text'=>$value['no_pendaftaran']];
                }
                $total = count($body['response']);
                $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
                return DocoHelpers::response($return);
            }
        }catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }
    
    public function actionGenerateInvoiceBelumBayar($id,$status = 1, $kelompok = null, $jenis_invoice = 1, $tipe_pasien = 0 )
    {
        $id = DocoHelpers::decrypt($id);
        return Yii::$app->runAction('/kasir/pembayaran-tagihan/cetak-detail-invoice',[
            'id' => $id,
            'kelompok' => $kelompok,
            'status' => $status,
            'tipe_pasien' => $tipe_pasien,
            'jenis_invoice' => $jenis_invoice
        ]);
    }

    public function actionFilters() 
    {
        $request = Yii::$app->request;
        $term = $request->get('term', null);
        $type = $request->get('type', null);
        $page = $request->get('page', 1);
        $additionalPayload = $request->get('additionalPayload', []);
        $response = $this->guzzleExec($this->_restKasir, [
            'url' => 'inf-pasien-pulang/filters',
            'payload' => [
                'query' => [
                    'term' => $term,
                    'type' => $type,
                    'page' => $page,
                    'additionalPayload' => $additionalPayload
                ]
            ],
        ]);
        return $this->responseJson(200, 'Data berhasil diambil!', $response);
    }

    public function actionModalPlafon()
    {
        $title = 'Plafon BPJS Pasien';
        $model = new PlafonBpjsForm;
        $request = Yii::$app->request;
        $pendaftaranId = $request->get('pendaftaran_id');
        $detailPlafon = $this->guzzleExec($this->_restKasir, [
            'url' => 'inf-pasien-pulang/get-data-plafon',
            'payload' => [
                'query' => [
                    'pendaftaran_id' => $pendaftaranId,
                ]
            ],
        ]);
        $model->pendaftaran_id = $pendaftaranId;
        $model->instalasi_id = ArrayHelper::getValue($detailPlafon, 'instalasi_id');
        $model->kelaspelayanan_id = ArrayHelper::getValue($detailPlafon, 'kelaspelayanan_id');
        $model->plafon = ArrayHelper::getValue($detailPlafon, 'limit_tagihan', 0);
        return $this->renderAjax('_modal_plafon', compact('model', 'title', 'pendaftaranId', 'detailPlafon'));
    }

    public function actionSimpanPlafon()
    {
        $request = Yii::$app->request;
		$model = new PlafonBpjsForm;
		$formName = substr(strrchr(get_class($model), "\\"), 1);
        $result = null;
        if($request->post()) {
			$post = $request->post($formName);
			$model->attributes = $post;
			if($model->validate()) {
				try {
					$response = $this->_restKasir->post('inf-pasien-pulang/simpan-plafon',[
						'form_params' => $model->attributes
					]);
					$response = json_decode($response->getBody(),true);
                    $result = DocoHelpers::response($response);
				} catch (RequestException $e) {
                    $result = DocoHelpers::responseJsonString(
                        $e->getResponse()->getBody()->getContents(),
                        $formName
                    );
				} catch (\Exception $e) {
                    $result = DocoHelpers::responseTemplate(500, $e->getMessage());
				}
			}
			else {
				$errors = DocoHelpers::parseError($model->errors, $formName);
                $result = DocoHelpers::responseTemplate(422, 'Error', $errors);
			}
		}
        return $result;
    }

    public function actionModalHistoryPlafon()
    {
        $title = 'Riwayat Perubahan Plafon BPJS';
        $request = Yii::$app->request;
        $pendaftaranId = $request->get('pendaftaran_id');
        return $this->renderAjax('_modal_history_plafon', compact('title', 'pendaftaranId'));
    }

    public function actionGetDataHistoryPlafon()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $payload = DocoDatatableHelper::advancedFilterParam();
        $payload['pendaftaran_id'] = $request->get('pendaftaran_id');
        $response = $this->guzzleExec($this->_restKasir, [
            'url' => "inf-pasien-pulang/get-data-history-plafon",
            'payload' => [
                'query' => $payload,
            ]
        ]);
        
        foreach ($response['data'] as $key => $value) {
            $response['data'][$key]['primary'] = DocoHelpers::encrypt($value['histori_plafon_bpjs_pasien_id']);
        }
        $response['recordsTotal'] = count($response['data']);
        $response['recordsFiltered'] = count($response['data']);
        return $response;
    }
}
