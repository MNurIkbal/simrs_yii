<?php
// Author : Budi

namespace Doco\kasir\controllers;

use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\components\DHtml;
use app\components\DocoConstants;
use app\modules\kasir\models\CetakKwitansiForm;
use app\modules\kasir\models\BatalPembayaranForm;
use yii\helpers\ArrayHelper;
class InfPasienSudahBayarController extends DocoController
{
    protected $_title = "Informasi Pasien Sudah Bayar";
    protected $_module = 'kasir/inf-pasien-sudah-bayar/';
    protected $_restKasir;
    protected $_restMaster;
    protected $allowAction = [
        'cetak-kwitansi',
        'pdf-cetak-invoice-detail'
    ];

    public function init()
    {
        parent::init();
        $this->_restKasir = Yii::$app->docoRest->kasir;
        $this->_restMaster = Yii::$app->docoRest->master;
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
        $restKasir = Yii::$app->docoRest->kasir;
        $roleBtnInvInaCbg = DHtml::cekHakAkses('detail-invoice-inacbg');
        $roleBtnInvInaCbg = ($roleBtnInvInaCbg) ? '' : 'display:none';
        $path = Yii::$app->docoPlugin->execute($this, 'index_sudah_bayar');
        $btnDetailInvoice = Yii::$app->docoPlugin->execute($this, 'detail_invoice');
        $btnInvoice = Yii::$app->docoPlugin->execute($this, 'invoice');
        return $this->render($path, get_defined_vars());
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
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restKasir->get('inf-pasien-sudah-bayar/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']["data"] as $key => $value) {
                $no++;
                $pembayaranPelayananId = ArrayHelper::getValue($value, 'pembayaranpelayanan_id');
                $pembayaranId = ArrayHelper::getValue($value, 'pembayaran_id');
                $pendaftaranId = ArrayHelper::getValue($value, 'pendaftaran_id');
                $penjualanResepId = ArrayHelper::getValue($value, 'penjualanresep_id');
                $instalasiNama = ArrayHelper::getValue($value, 'instalasi_nama');
                $ruanganNama = ArrayHelper::getValue($value, 'ruangan_nama');
                $namaPasien = ArrayHelper::getValue($value, 'nama_pasien');
                $noRekamMedik = ArrayHelper::getValue($value, 'no_rekam_medik');
                $caraBayarNama = ArrayHelper::getValue($value, 'carabayar_nama');
                $penjaminNama = ArrayHelper::getValue($value, 'penjamin_nama');
                $tglPembayaran = ArrayHelper::getValue($value, 'tgl_pembayaran');
                $tglPendaftaran = ArrayHelper::getValue($value, 'tgl_pendaftaran');
                $tglPulang = !empty($value['tgl_pulang']) ? $value['tgl_pulang'] : $tglPendaftaran;
                $biayaAdministrasi = ArrayHelper::getValue($value, 'biaya_administrasi', 0);
                $totalDitagihkan = ArrayHelper::getValue($value, 'total_ditagihkan', 0);
                $totalTagihan = ArrayHelper::getValue($value, 'total_tagihan', 0);
                $totalTagihan = $totalTagihan + $biayaAdministrasi;
                $totalDijamin = ArrayHelper::getValue($value, 'total_dijamin', 0);
                $totalDiskon = ArrayHelper::getValue($value, 'total_discountpembayaran', 0);
                $totalDibayar = ArrayHelper::getValue($value, 'total_dibayar', 0);
                $pegawai_kasir = ArrayHelper::getValue($value, 'pegawai_kasir','-');

                $primaryKey = DocoHelpers::encrypt($pembayaranPelayananId);
                $value['tipe_pasien'] = (is_null($pendaftaranId)) ? 'pasien_bebas' : 'pasien_rs';
                $value['primary'] = $primaryKey;
                $value['nama_pasien_serialize'] = rawurlencode(ArrayHelper::getValue($value, 'nama_pasien'));
                $value['pendaftaran_id'] = DocoHelpers::encrypt($pendaftaranId);
                $value['penjualanresep_id'] =!empty($penjualanResepId) ? DocoHelpers::encrypt($penjualanResepId) : null;
                $value['pembayaran_id'] = DocoHelpers::encrypt($pembayaranId);
                $value['instalasi_nama'] = $instalasiNama.' - '.$ruanganNama;
                $value['carabayar_nama'] = $caraBayarNama.' - '.$penjaminNama;
                $value['rowNum'] = $no;
                $value['tgl_pembayaran'] = date('d-M-Y H:i:s', strtotime($tglPembayaran));
                $value['tgl_masuk_keluar'] = date('d-M-Y', strtotime($tglPendaftaran)).' - '.date('d-M-Y', strtotime($tglPulang));
                $value['total_bayar'] = DocoHelpers::formatNumber($totalDitagihkan);
                $value['tagihan'] = DocoHelpers::formatNumber($totalTagihan);
                $value['total_dibayar'] = DocoHelpers::formatNumber($totalDibayar);
                $value['total_discountpembayaran'] = DocoHelpers::formatNumber($totalDiskon);
                $value['total_dijamin'] = DocoHelpers::formatNumber($totalDijamin);
                $value['pegawai_kasir'] = $pegawai_kasir;
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionGetDataTindakan($id="",$type,$ppId="", $tipe_pasien = null)
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
        $result['recordsTotal'] = 0;
        $decryptId = DocoHelpers::decrypt($id);
        $decryptPpId = DocoHelpers::decrypt($ppId);
        $yiiRestfulParams['id'] = $decryptId;
        $yiiRestfulParams['type'] = $type;
        $yiiRestfulParams['ppId'] = $decryptPpId;
        $yiiRestfulParams['tipe_pasien'] = $tipe_pasien;
        try {
            $response = $this->_restKasir->get('inf-pasien-sudah-bayar/data-detail-tindakan?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']["data"] as $key => $value) {
                $no++;
                $primaryKey = is_null($value['pendaftaran_id']) ? $value['penjualanresep_id'] : $value['pendaftaran_id'];
                $value['primary'] = DocoHelpers::encrypt($primaryKey);
                $value['tgl_pelayanan'] = date('d-M-Y H:i:s', strtotime($value['tgl_pelayanan']));
                $value['tarifsatuan_'] = $value['tarif_satuan'];
                $value['subtotal_'] = $value['sub_total'];
                $value['tarif_satuan'] = DocoHelpers::rupiahDisplay(@$value['tarif_satuan']);
                $value['tarifcyto_tindakan'] = DocoHelpers::rupiahDisplay(@$value['tarifcyto_tindakan']);
                $value['tarif_cyto'] = DocoHelpers::rupiahDisplay(@$value['tarif_cyto']);
                $value['sub_total'] = DocoHelpers::rupiahDisplay(@$value['sub_total']);
                $value['total_tagihan'] = DocoHelpers::rupiahDisplay(@$value['total_tagihan']);
                $value['total_uang_muka'] = DocoHelpers::rupiahDisplay(@$value['total_uang_muka']);
                $value['total_sudah_dibayarkan'] = DocoHelpers::rupiahDisplay(@$value['total_sudah_dibayarkan']);
                $value['total_sisatagihan'] = DocoHelpers::rupiahDisplay(@$value['total_sisatagihan']);
                $value['tarif_diskon'] = DocoHelpers::rupiahDisplay(@$value['tarif_diskon']);
                $value['tarif_dijamin'] = DocoHelpers::rupiahDisplay(@$value['tarif_dijamin']);
                $value['tarif_dibayarkan'] = DocoHelpers::rupiahDisplay(@$value['tarif_dibayarkan']);
                $value['rowNum'] = $no;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionGetDataObat($id="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
                $decryptId = DocoHelpers::decrypt($id);

        $data = [];
        $result = [];

        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

                try {
            $response = $this->_restKasir->get('lap-pasien-sudah-bayar/data-detail-obat?id='.$decryptId);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']["data"] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pendaftaran_id']);
                $value['primary'] = $primaryKey;

                $value['tarif_satuan'] = DocoHelpers::rupiahDisplay($value['tarif_satuan']);
                $value['tarifcyto_tindakan'] = DocoHelpers::rupiahDisplay($value['tarifcyto_tindakan']);
                $value['tarif_cyto'] = DocoHelpers::rupiahDisplay($value['tarif_cyto']);
                $value['sub_total'] = DocoHelpers::rupiahDisplay($value['sub_total']);
                $value['total_tagihan'] = DocoHelpers::rupiahDisplay($value['total_tagihan']);
                $value['total_uang_muka'] = DocoHelpers::rupiahDisplay($value['total_uang_muka']);
                $value['total_sudah_dibayarkan'] = DocoHelpers::rupiahDisplay($value['total_sudah_dibayarkan']);
                $value['total_sisatagihan'] = DocoHelpers::rupiahDisplay($value['total_sisatagihan']);

                $value['rowNum'] = $no;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionGetDataLab($id="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
                $decryptId = DocoHelpers::decrypt($id);

        $data = [];
        $result = [];

        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restKasir->get('lap-pasien-sudah-bayar/data-detail-lab?id='.$decryptId);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            // return DocoHelpers::response($body['response']['data']);
            foreach ($body['response']["data"] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pendaftaran_id']);
                $value['primary'] = $primaryKey;

                $value['tgl_pelayanan'] = date('d-M-Y H:i:s', strtotime($value['tgl_pelayanan'] ));
                $value['tarif_satuan'] = DocoHelpers::rupiahDisplay($value['tarif_satuan']);
                $value['tarifcyto_tindakan'] = DocoHelpers::rupiahDisplay($value['tarifcyto_tindakan']);
                $value['tarif_cyto'] = DocoHelpers::rupiahDisplay($value['tarif_cyto']);
                $value['sub_total'] = DocoHelpers::rupiahDisplay($value['sub_total']);
                // $value['total_tagihan'] = DocoHelpers::rupiahDisplay($value['total_tagihan']);
                // $value['total_uang_muka'] = DocoHelpers::rupiahDisplay($value['total_uang_muka']);
                // $value['total_sudah_dibayarkan'] = DocoHelpers::rupiahDisplay($value['total_sudah_dibayarkan']);
                // $value['total_sisatagihan'] = DocoHelpers::rupiahDisplay($value['total_sisatagihan']);

                $value['rowNum'] = $no;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionGetDataRadiologi($id="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
                $decryptId = DocoHelpers::decrypt($id);

        $data = [];
        $result = [];

        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

                try {
            $response = $this->_restKasir->get('lap-pasien-sudah-bayar/data-detail-radiologi?id='.$decryptId);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']["data"] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pendaftaran_id']);
                $value['primary'] = $primaryKey;

                                if ($value['tarif_cyto']) {
                                    $value['cyto'] = "Ya";
                                }else{
                                    $value['cyto'] = "Tidak";
                                }

                                $value['tarif_satuan'] = DocoHelpers::rupiahDisplay($value['tarif_satuan']);
                                $value['tarifcyto_tindakan'] = DocoHelpers::rupiahDisplay($value['tarif_cyto']);
                                $value['sub_total'] = DocoHelpers::rupiahDisplay($value['sub_total']);
                                // $value['total_tagihan'] = DocoHelpers::rupiahDisplay($value['total_tagihan']);
                                // $value['total_uang_muka'] = DocoHelpers::rupiahDisplay($value['total_uang_muka']);
                                // $value['total_sudah_dibayarkan'] = DocoHelpers::rupiahDisplay($value['total_sudah_dibayarkan']);
                                // $value['total_sisatagihan'] = DocoHelpers::rupiahDisplay($value['total_sisatagihan']);

                $value['rowNum'] = $no;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionGetDataPembayaran()
    {
        try{
            if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
                $tgl_pembayaran = '';
                if(isset($_GET['z'])){
                        $tgl_pembayaran = $_GET['z'];
                }
                $response = $this->_restKasir->request('POST', 'inf-pasien-sudah-bayar/get-data-pembayaran',[
                        'form_params'=>['term'=>$_GET['q']['term'], 'date'=>$tgl_pembayaran],
                ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                        $data[] = ['id' => $value['no_pembayaran'], 'text' => $value['no_pembayaran']];
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

    public function actionGetPasien($q="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];

        try {
            $response = $this->_restMaster->get('pasien/get-pasien?advanced-filter[nama_pasien]='.$q);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value)
                $result['results'][] = [
                    'id' => $value['nama_pasien'],
                    'text' => $value['nama_pasien']
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

    public function actionSearch($tipe = NULL)
    {
        if($tipe == 'pasien') {
            $path = 'search_pasien';
        } elseif($tipe == 'pendaftaran') {
            $path = 'search_pendaftaran';
        } else {
            $path = 'search_pembayaran';
        }

        return $this->renderPartial($path, get_defined_vars());
    }

    public function actionPreview($id, $tipe_pasien = null)
    {
        $request = Yii::$app->request;
        $title = 'Detail Informasi Pasien Sudah Bayar';
        $dataLab = [];
        $dataRadiologi = [];
        $dataObat = [];
        $dataTindakan = [];
        $cacheItem = Yii::$app->cache;
        $pendaftaran_id = $request->get('pendaftaran_id');
        $pdId = DocoHelpers::decrypt($pendaftaran_id);
        $tipe_pasien = $request->get('tipe_pasien');
        $ppId = null;
        if(!empty($id)) {
            $ppId = DocoHelpers::decrypt($id);
            $buktibayar_id = DocoHelpers::decrypt($id);
        }
        $arrRuangan = [];
        $response = $this->_restKasir->get('inf-pasien-sudah-bayar/get-detail-pembayaran', [
            'query' => [
                'id' => $pdId,
                'pembayaranpelayanan_id'=> $ppId,
                'tipe_pasien'=> $tipe_pasien
            ]
        ]);
        $body = json_decode($response->getBody(), true);
        $data = $body['response']['detail'];
        $header = $body['response']['header'];
        for ($i=0; $i < count($data) ; $i++) {
            $primaryID = is_null(@$data[$i]['pendaftaran_id']) ? @$data[$i]['penjualanresep_id'] : @$data[$i]['pendaftaran_id'];
            $data[$i]['tarif_satuan'] = DocoHelpers::rupiahDisplay(@$data[$i]['tarif_satuan']);
            $data[$i]['tarifcyto_tindakan'] = DocoHelpers::rupiahDisplay(@$data[$i]['tarifcyto_tindakan']);
            $data[$i]['tarif_cyto'] = DocoHelpers::rupiahDisplay(@$data[$i]['tarif_cyto']);
            $data[$i]['sub_total'] = DocoHelpers::rupiahDisplay(@$data[$i]['sub_total']);
            $data[$i]['total_tagihan'] = DocoHelpers::rupiahDisplay(@$data[$i]['total_tagihan']);
            $data[$i]['total_uang_muka'] = DocoHelpers::rupiahDisplay(@$data[$i]['total_uang_muka']);
            $data[$i]['total_sudah_dibayarkan'] = DocoHelpers::rupiahDisplay(@$data[$i]['total_sudah_dibayarkan']);
            $data[$i]['total_sisatagihan'] = DocoHelpers::rupiahDisplay(@$data[$i]['total_sisatagihan']);
            $data[$i]['tarif_diskon'] = DocoHelpers::rupiahDisplay(@$data[$i]['tarif_diskon']);
            $data[$i]['tarif_dijamin'] = DocoHelpers::rupiahDisplay(@$data[$i]['tarif_dijamin']);
            $data[$i]['tarif_dibayarkan'] = DocoHelpers::rupiahDisplay(@$data[$i]['tarif_dibayarkan']);
            if ($data[$i]['is_obat']==false) {
                if ($data[$i]['instalasi_id']==4) {
                  $dataLab[] = $data[$i];
                }elseif ($data[$i]['instalasi_id']==5) {
                  $dataRadiologi[] = $data[$i];
                }else {
                    if(!isset($dataTindakan['info'])){
                        $dataTindakan['info']['pendaftaran_id'] = $primaryID;
                    }
                    $dataTindakan[str_replace(' ', '-', $data[$i]['ruangan_pelayanan'])][] = $data[$i];
                }
            }else{
                $data[$i]["pendaftaran_id"] = $primaryID;
                $dataObat[] = $data[$i];
            }
        }
        if($dataTindakan){
            $arrRuangan = array_keys($dataTindakan);
            unset($arrRuangan[0]);
        }
        $cacheName = 'tindakan-pasien-sudah-bayar-'.$pendaftaran_id.'-'.$id;
        $cacheItem->set('tindakan-pasien-sudah-bayar-'.$pendaftaran_id.'-'.$id, $dataTindakan);
        return $this->render('detail',get_defined_vars());
    }

    public function actionCancel()
    {
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        $pembayaran_id = $request->get('pembayaran_id', null);
        $penjualanresep_id = $request->get('penjualanresep_id', null);
        $title     = Yii::t('fe', 'Batal Pembayaran');
        $batalForm = new BatalPembayaranForm;
        $formName = substr(strrchr(get_class($batalForm), "\\"), 1);
        if ($request->post()) {
            $batalForm->load($request->post());
            $batalForm->tanggal_batal = date('Y-m-d');

            if ($batalForm->validate()) {
                if(!empty($id)) {
                    $id = DocoHelpers::decrypt($id);
                }
                if(!empty($batalForm->pendaftaran_id)) {
                    $pendaftaran_id = DocoHelpers::decrypt($batalForm->pendaftaran_id);
                }
                if(!empty($batalForm->pembayaran_id)) {
                    $pembayaran_id = DocoHelpers::decrypt($batalForm->pembayaran_id);
                }
                if(!empty($batalForm->penjualanresep_id) && $batalForm->penjualanresep_id != 'null') {
                    $penjualanresep_id = DocoHelpers::decrypt($batalForm->penjualanresep_id);
                }
                try {
                    $response = $this->_restKasir->get('inf-pasien-sudah-bayar/cancel', [
                        'query' => [
                            'id' => $id,
                            'pendaftaran_id' => $pendaftaran_id,
                            'pembayaran_id' => $pembayaran_id,
                            'penjualanresep_id' => $penjualanresep_id,
                            'alasan_batal' => $batalForm->alasan_batal,
                            'password' => $batalForm->password,
                        ]
                    ]);
                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response);

                } catch (RequestException $e) {
                    $response = json_decode($e->getResponse()->getBody(),true);
                    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    \Yii::$app->response->statusCode =500;
                    return ['response'=>[
                        'title'=>'Terjadi Kesalahan',
                        'message'=>'gagal',
                        'text'=>$response['response']
                    ]];
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(422, 'Error', $e->getMessage());

                }
            }else{
                $errors = DocoHelpers::parseError($batalForm->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }else{
            return $this->renderAjax('_form_batal', get_defined_vars());
        }
    }

    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/detail-pasien-sudah-bayar.pdf";
        try {
            $id = $request->get('id',null);
            $pdf_id = $request->get('pdf_id',null);
            $tipe_pasien = $request->get('tipe_pasien',null);
            $decryptId = DocoHelpers::decrypt($id);
            $decryptIdPdfId = DocoHelpers::decrypt($pdf_id);
            $params = ['id'=>$decryptId, 'pdf_id'=>$decryptIdPdfId, "tipe_pasien" => $tipe_pasien];
            $response = $this->_restKasir
                ->get('inf-pasien-sudah-bayar/export-pdf',
                [
                    'query' => $params,
                    'save_to' => $path
                ]);
            $response = json_decode($response->getBody(),true);
            // dump($response);die;
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionPrintKwitansi()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/kwitansi-sudah-bayar.pdf";
        try {
            $id = $request->get('id',null);
            $pdf_id = $request->get('pdf_id',null);
            $decryptId = DocoHelpers::decrypt($id);
            $tipe_pasien = $request->get('tipe_pasien',null);
            $post = ['id'=>$decryptId,'pdf_id'=>DocoHelpers::decrypt($pdf_id), "tipe_pasien" => $tipe_pasien];
            $response = $this->_restKasir->post('inf-pasien-sudah-bayar/print-kwitansi', [
                'form_params' => $post,
                'save_to' => $path
            ]);
            $response = json_decode($response->getBody(),true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionPrintBkm()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/bkm-sudah-bayar.pdf";
        try {
            $id = $request->get('id',null);
            $pdf_id = $request->get('pdf_id',null);
            $decryptId = DocoHelpers::decrypt($id);
            $tipe_pasien = $request->get('tipe_pasien',null);
            $post = ['id'=>$decryptId,'pdf_id'=>DocoHelpers::decrypt($pdf_id), "tipe_pasien" => $tipe_pasien];
            $response = $this->_restKasir->post('inf-pasien-sudah-bayar/print-bkm',[
                'form_params' => $post,
                'save_to' => $path
            ]);
            $response = json_decode($response->getBody(),true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionGetPendaftaran()
    {
        try{
            if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
                $tgl_pendaftaran = '';
                if(isset($_GET['z'])){
                    $tgl_pendaftaran = $_GET['z'];
                }
                $response = $this->_restKasir->request('POST', 'inf-pasien-sudah-bayar/get-data-pendaftaran',[
                    'form_params'=>['term'=>$_GET['q']['term'], 'date'=>$tgl_pendaftaran],
                ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id' => $value['no_pendaftaran'], 'text' => $value['no_pendaftaran']];
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
    public function actionGetTindakan($id, $ppId, $key, $tipe_pasien = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $cacheItem = Yii::$app->cache;
        $request = Yii::$app->request;

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        if(!$cacheItem->get('tindakan-pasien-sudah-bayar-'.$id.'-'.$ppId)){
            return $result;
        }
        $no = 0;
        $body = $cacheItem->get('tindakan-pasien-sudah-bayar-'.$id.'-'.$ppId);
        foreach ($body[$key] as $k => $value) {
            $no++;
            $primaryKey = DocoHelpers::encrypt($value['pendaftaran_id']);
            $value['primary'] = $primaryKey;
            $value['tgl_pelayanan'] = date('d-M-Y H:i:s', strtotime($value['tgl_pelayanan']));
            $value['tarif_satuan'] = $value['tarif_satuan'];
            $value['tarifcyto_tindakan'] = $value['tarifcyto_tindakan'];
            $value['tarif_cyto'] = $value['tarif_cyto'];
            $value['sub_total'] = $value['sub_total'];
            $value['total_tagihan'] = $value['total_tagihan'];
            $value['total_uang_muka'] = $value['total_uang_muka'];
            $value['total_sudah_dibayarkan'] = $value['total_sudah_dibayarkan'];
            $value['total_sisatagihan'] = $value['total_sisatagihan'];
            $value['tarif_diskon'] = $value['tarif_diskon'];
            $value['tarif_dijamin'] = $value['tarif_dijamin'];
            $value['tarif_dibayarkan'] = $value['tarif_dibayarkan'];
            $value['rowNum'] = $no;
            $data[$k] = $value; 
        }

        $result['data'] = $data;
        $result['recordsTotal'] = count($data);
        $result['recordsFiltered'] = count($data);

        return $result;
    }

    public function actionCetakKwitansi($id, $pembayaran_id)
    {
        $title = 'Cetak Kwitansi';
        $request = Yii::$app->request;
        $namaPasien = $request->get('nama_pasien_serialize','');
        $model = new CetakKwitansiForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $jenis_kwitansi = [1 => 'Lengkap', 2 => 'Pasien', 3 => 'Penjamin'];

        if(!empty($pembayaran_id) && !is_numeric($pembayaran_id)) {
            $pembayaran_id = DocoHelpers::decrypt($pembayaran_id);
        }

        if($request->post()) {
            $model->load($request->post());
            $model->pembayaran_id = $pembayaran_id;
            if(!$model->validate()) {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
            return DocoHelpers::response($model->attributes);
        }
        else {
            $groupcarabayar_id = $request->get('groupcarabayar_id', null);
            $listPenjamin = $this->getPenjamin($pembayaran_id);
            if(!empty($listPenjamin)) {
                $listPenjamin = ArrayHelper::map($listPenjamin, 'penjamin_id', 'penjamin_nama');
            }
            $groupUmum = DocoConstants::GROUP_UMUM;
            return $this->renderAjax('_cetak_kwitansi', get_defined_vars());
        }
    }

    public function actionGenerateKwitansi()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $pembayaran_id = $request->get('pembayaran_id', null);
        $jenis_kwitansi = $request->get('jenis_kwitansi', null);
        $diterima_dari = $request->get('diterima_dari', null);
        $keterangan = $request->get('keterangan', null);
        $userIdentity = Yii::$app->session->get('user_identity');
        $penjamin_id = $request->get('penjamin_id', null);
        $params = [
            'pembayaran_id' => $pembayaran_id,
            'jenis_kwitansi' => $jenis_kwitansi,
            'diterima_dari' => $diterima_dari,
            'keterangan' => $keterangan,
            'nama_pegawai' => $userIdentity['nama_pegawai'],
            'penjamin_id' => $penjamin_id,
        ];

        $url = 'lap-pasien-sudah-bayar/print-kwitansi';
        if ( Yii::$app->report->enabled ) {
            $reportCode = 'kasir/'.$url;

            return Yii::$app->report->exec($reportCode.'?'.http_build_query($params),[
                'manualRender' => function() use ($url, $params){
                    $path = Yii::getAlias("@download") . "/kwitansi-sudah-bayar.pdf";
                    $this->guzzleExec($this->_restKasir, [
                        'url' => $url,
                        'payload' => [
                            'query' => $params,
                            'save_to' => $path
                        ]
                    ]);
                    return DocoHelpers::previewPdf($path);
                }
            ]);
        }
        $path = Yii::getAlias("@download") . "/kwitansi-sudah-bayar.pdf";
        $response = $this->_restKasir->get($url, [
            'query' => $params,
            'save_to' => $path
        ]);
        $body = json_decode($response->getBody(), true);
        return DocoHelpers::previewPdf($path);
    }

    public function actionExportExcel()
    {        
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $result = [];
        $url = "inf-pasien-sudah-bayar/export-excel?".http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/informasi-pasien-sudah-bayar.xlsx";
        try {
            $response = $this->_restKasir->get($url,[
                'save_to' => $path,
            ]);

            $body = json_decode($response->getBody(), True);
            // dump($body);die;
            return DocoHelpers::downloadFile($path,true);
        } catch (RequestException $e) {
            throw new \yii\web\NotFoundHttpException();
            return $e;
        } catch (\Exception $e) {
            throw new \yii\web\NotFoundHttpException();
            return $e;
        }
    }

    public function actionShowPopup()
    {
        return Yii::$app->docoPlugin->execute($this, 'cetak_detail_invoice');
    }

    public function actionGenerateInvoice()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $pembayaran_id = $request->get('pembayaran_id', null);
        $jenis_invoice = $request->get('jenis_invoice', null);
        $params = [
            'id' => $id,
            'invoice_id' => $pembayaran_id,
            'jenis_invoice' => $jenis_invoice,
        ];
        return Yii::$app->runAction('/kasir/pembayaran-tagihan/cetak-detail-invoice', $params);
    }

    public function actionShowPopupBelumBayar()
    {
        $title = 'Jenis Detail Invoice';
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $kelompok = $request->get('kelompok', null);
        $jenis_invoice = [1 => 'Invoice Lengkap', 2 => 'Invoice Pasien', 3 => 'Invoice Penjamin'];
        if($request->post()) {
            $post = $request->post();
            return DocoHelpers::response(['data' => [
                'id' => $id,
                'kelompok' => $kelompok,
                'jenis_invoice' => DocoHelpers::encrypt($post['jenis_invoice']),
            ]]);
        }
        else {
            return $this->renderAjax('partial/_modal_belum_bayar', get_defined_vars());
        }
    }

    private function getPenjamin($pembayaran_id)
    {
        return $this->guzzleExec($this->_restKasir, [
            'url' => 'inf-pasien-sudah-bayar/get-penjamin',
            'payload' => [
                'query' => [
                    'pembayaran_id' => $pembayaran_id
                ]
            ]
        ]);
    }

    public function actionFilters()
    {
        return $this->guzzleExec($this->_restKasir, [
            'url' => 'inf-pasien-sudah-bayar/filters',
            'payload' => [
                'query' => Yii::$app->request->get()
            ],
            'returnResponse' => true
        ]);
    }

    public function actionShowPopupExcel()
    {
        $title = 'Informasi Pasien Sudah Bayar';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['instalasi_nama_text'] = $request->get('instalasi_nama', null);
        $yiiRestfulParams['advanced-filter']['instalasi_id'] = $request->get('instalasi_id', null);
        $yiiRestfulParams['advanced-filter']['carabayar_nama_text'] = $request->get('carabayar_nama',null);
        $yiiRestfulParams['advanced-filter']['carabayar_id'] = $request->get('carabayar_id',null);
        $yiiRestfulParams['advanced-filter']['penjamin_text'] = urldecode($request->get('penjamin', null));
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('partial/_modal_excel', get_defined_vars());
    }

    public function actionProcessSyncExcel($randString)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);
        $session['randString'] = $randString;
        return $this->guzzleExec($this->_restKasir, [
            'url' => "inf-pasien-sudah-bayar/export-excel-bg-proses",
            'payload' => [
                'query' => $session
            ],
        ]);
    }

    public function actionDownloadExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('fileName', null);
        $fileDownloads = 'Informasi Pasien Sudah Bayar '. date("dmY").'.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restKasir->get('inf-pasien-sudah-bayar/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }

    public function actionPdfCetakInvoiceDetail()
    {
        $reportCode = 'new-invoice-detail';
        $params = Yii::$app->request->get();
        foreach($params as $key =>$value){
            $params[$key] = (int)$value;
        }
        return Yii::$app->report->exec($reportCode,[
            'queryParamater' => $params
        ]);
    }



}
