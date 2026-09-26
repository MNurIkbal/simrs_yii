<?php


/**
* @author yaya
**/

namespace Doco\kasir\controllers;


use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;

use app\components\DocoHelpers;
use app\components\DocoController;
use app\components\DocoConstants;
use app\components\DocoSelect2Trait;

use Doco\kasir\models\TagihanPasienForm;
use Doco\kasir\models\TmpTagihanPasienForm;
use Doco\kasir\models\PenjaminView;
use Doco\kasir\models\MultiPenjamin;
use Doco\kasir\models\EditTagihan;
use Doco\kasir\models\MultiPembayaran;
use Doco\kasir\models\DiskonDokter;
use GuzzleHttp\Exception\RequestException;

use app\modules\v1\cache\Cache;
use app\modules\kasir\models\CetakInvoiceForm;
use app\components\DocoDatatableHelper;
use app\components\DHtml;

class PembayaranTagihanController extends DocoController
{
    use DocoSelect2Trait;

    protected $_title = "Pembayaran Tagihan Pasien";
    protected $_module = 'kasir/pembayaran-tagihan/';
    protected $_restKasir;
    protected $allowAction = [
        '*'
    ];
    public function init()
    {
        parent::init();
        $this->_restKasir = Yii::$app->docoRest->kasir;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actionIndex($id,$kelompok, $status = null, $tipe_pasien = null, $is_belumbayar = null, $roleBtnCloseBill = false)
    {
        $title = $this->_title;
        $request = Yii::$app->request;
        $is_belumbayar = $request->get('is_belumbayar', null);
        $id = DocoHelpers::decrypt($id);
        $model = new TagihanPasienForm;
        $breadcrumbs = [];
        $homeUrl = null;
        $isKarcis = $jenisAntrian = $statusBelumBayar = 0;
        $isEditTagihan = false;
        $is_logactivity = false;
        Yii::$app->cache->delete("default-penjamin-modal-".$id);
        switch ($kelompok) {
            case DocoConstants::PASIEN_KARCIS:
                $homeUrl = '/kasir/inf-pasien-karcis';
                $breadcrumbs = ['label' => 'Informasi Pasien Karcis', 'url' => [$homeUrl]];
                $isKarcis = 1;
                $jenisAntrian = DocoConstants::ANTRIAN_POLI;
                break;
            case DocoConstants::PASIEN_PULANG:
                $homeUrl = '/kasir/inf-pasien-pulang';
                $breadcrumbs = ['label' => 'Informasi Pasien Pulang', 'url' => [$homeUrl]];
                $is_logactivity = true;
                break;
            case DocoConstants::PASIEN_PENUNJANG:
                $homeUrl = '/kasir/inf-pasien-kepenunjangan';
                $breadcrumbs = ['label' => 'Informasi Pasien Penunjang', 'url' => [$homeUrl]];
                $isKarcis = 1;
                $jenisAntrian = DocoConstants::ANTRIAN_PENUNJANG;
                break;
            case DocoConstants::PASIEN_ALKES:
                $homeUrl = '/kasir/inf-penjualan-obat-alkes';
                $breadcrumbs = ['label' => 'Informasi Penjualan Obat Alkes', 'url' => [$homeUrl]];
                break;
            default:
                $homeUrl = '/kasir';
                $breadcrumbs = ['label' => 'Tagihan Pasien', 'url' => [$homeUrl]];
                break;
        }

        // Akses Menu Pembayaran dari Informasi Pasien Belum Bayar
        if($is_belumbayar == 1){
            $homeUrl = '/kasir/inf-pasien-belum-bayar';
            $breadcrumbs = ['label' => 'Pasien Belum Bayar', 'url' => [$homeUrl]];
            $statusBelumBayar = 1;
        }
        $penjamin = $caraBayar = $konfigSistem = [];

        $randString = DocoHelpers::generateRandomString();

        $queryParams = [
            'id' => $id,
            'kelompok' => $kelompok,
            'status' => $status,
            'tipe_pasien' => $tipe_pasien,
        ];

        Yii::$app->session->set($randString,$queryParams);

        $response = $this->_restKasir->get('tagihan-pasien/view', [
            'query' => $queryParams
        ]);
        $response = json_decode($response->getBody(),true);
        if ($response['response']['info'] == null) {
            throw new \Exception("Terjadi Kesalahan Data", 500);
        }
        $uid = Yii::$app->user->identity->loginpemakai_id;

        $penjamin_id = isset($response['response']['info']['penjamin_id']) ? $response['response']['info']['penjamin_id'] : null;
        $pasien_penjamin = isset($response['response']['info']['penjamin_nama']) ? $response['response']['info']['penjamin_nama'] : null;
        $pasien_carabayar = isset($response['response']['info']['carabayar_nama']) ? $response['response']['info']['carabayar_nama'] : null;
        $pasien_kartu = isset($response['response']['info']['no_kartu']) ? $response['response']['info']['no_kartu'] : null;
        $group_carabayar = isset($response['response']['info']['group_carabayar']) ? $response['response']['info']['group_carabayar'] : null;
        $gabung_penjamin = $pasien_carabayar .' - '. $pasien_penjamin .'#'. $pasien_kartu .'#'. $penjamin_id .'#'.$group_carabayar;
        $discountInsurance = isset($response['response']['discountInsurance']) ? $response['response']['discountInsurance'] : null;
        $configOtoritasPenjamin = isset($response['response']['configOtoritasPenjamin']) ? $response['response']['configOtoritasPenjamin'] : false;
        $getCache = Yii::$app->cache->get("default-penjamin-modal-".$id);
        if ($getCache == false) {
            Yii::$app->cache->set("default-penjamin-modal-".$id,$gabung_penjamin);
        }
        $getListPenjamin = isset($response['response']['listPenjamin']) ? $response['response']['listPenjamin'] : null;
        $model->attributes = isset($response['response']['info']) ? $response['response']['info'] : [];
        $group_carabayar = isset($response['response']['info']['group_carabayar']) ? $response['response']['info']['group_carabayar'] : null;
        $administrasi_ri = isset($response['response']['administrasi_ri']) ? $response['response']['administrasi_ri'] : [];
        $model->detail_tagihan = isset($response['response']['detail_tagihan']) ? $response['response']['detail_tagihan'] : [];

        $instalasi_nama = isset($response['response']['info']['instalasi_nama']) ? $response['response']['info']['instalasi_nama'] : null;
        $instalasi_map= isset($response['response']['instalasi_map']) ? $response['response']['instalasi_map'] : null;
        $penjamin = isset($response['response']['penjamin']) ? $response['response']['penjamin'] : [];
        $header = isset($response['response']['header']) ? $response['response']['header'] : [];
        $caraBayar = isset($response['response']['cara_bayar']) ? $response['response']['cara_bayar'] :[];
        $konfigSistem = isset($response['response']['konfig_sistem']) ? $response['response']['konfig_sistem'] : [];
        $adm_persen = isset($konfigSistem['adm_persen']) ? $konfigSistem['adm_persen'] : 0;
        $isResetEditTagihan = isset($konfigSistem['is_reset_edit_tagihan']) ? $konfigSistem['is_reset_edit_tagihan'] : false;
        $biaya_adm_maksimal = isset($administrasi_ri['tarif']) ? $administrasi_ri['tarif'] : "";
        $totalTagihan = isset($response['response']['total_tagihan']) ? $response['response']['total_tagihan'] : 0;
        $instance_adm = isset($response['response']['instance_adm']) ? $response['response']['instance_adm'] : [];
        $total_admin = isset($response['response']['total_admin']) ? $response['response']['total_admin'] : 0;
        $model->tindakan_visitdokter = isset($response['response']['tindakan_visitdokter']) ? $response['response']['tindakan_visitdokter'] : [];
        $disableSip = isset($response['response']['konfig_sip']) ? $response['response']['konfig_sip'] : 0;
        $labelCaraBayar = !empty($model->carabayar_nama) ? $model->carabayar_nama : '-';
        $labelNoKartu = !empty($model->no_kartu) ? ' / '.$model->no_kartu : '';
        $labelCaraBayar = ($group_carabayar != DocoConstants::GROUP_UMUM) ? 
        $labelCaraBayar.$labelNoKartu: $labelCaraBayar;

        $_kelompok = $kelompok;
        $model->kelompok = $_kelompok;
        $tagihan_belumbayar = isset($response['response']['info']) ? $response['response']['info']['tagihan_belumbayar'] : 0;

        $instalasi_id = isset($response['response']['info']['instalasi_id']) ? $response['response']['info']['instalasi_id'] : null;
        
        $is_ranap = FALSE;
        $konfirmasi_val =[];
        $konfirmasi_match =[];

        if($instalasi_id == DocoConstants::INSTALASI_ID_RI){
            $konfirmasi_view =isset($response['response']['konfirmasi_view']) ? $response['response']['konfirmasi_view'] : null;
            $is_ranap = TRUE;
            
                $urutan_konfirm = isset($response['response']['konfirm_instalasi']) ? $response['response']['konfirm_instalasi'] : null;
                for($i=0;$i < count($urutan_konfirm); $i++){
                 
                    foreach ($instalasi_map as $value){
                        if($urutan_konfirm[$i] == $value['instalasi_id']){
                            $konfirmasi_val['instalasi_nama'] = $value['instalasi_nama'];
                        }
                        if(count($konfirmasi_view) == 0){
                            $konfirmasi_val['is_konfirmasi'] = FALSE;
                        }
                    }
                    if(isset($konfirmasi_view)){ 
                        foreach ($konfirmasi_view as $value){
                            if($urutan_konfirm[$i] == $value['instalasi_id']){
                                $konfirmasi_val['is_konfirmasi'] = $value['is_konfirmasi'];break;
                            } else $konfirmasi_val['is_konfirmasi'] = FALSE;
                        }
                    }                       
                    $konfirmasi_match[$i] = $konfirmasi_val;                    
                }         
        };
        
        $statusBtnDetailRi = $instalasi_id == DocoConstants::INSTALASI_ID_RI ? false : true;

        $model->tmpTagihan = isset($response['response']['tmpTagihan']) ? $response['response']['tmpTagihan'] : [];
        $model->kontrakPenjamin = isset($response['response']['kontrakPenjamin']) ? $response['response']['kontrakPenjamin'] : [];

        // get list penjamin dari penata jasa
        $listPenjamin = [];
        foreach ($getListPenjamin as $key => $value) {
            $listPenjamin[] = [
                'id' => (string)$value['penjamin_id'],
                'text' => $value['penjamin_nama'],
                'selected' =>  false
            ];
        }
        $btnDetailInvoice = Yii::$app->docoPlugin->execute($this, 'detail_invoice_pembayaran');
        $btnInvoice = Yii::$app->docoPlugin->execute($this, 'invoice');
        $labelSimpan = Yii::$app->docoPlugin->execute($this, 'label_simpan');
        $btnEditTagihan = Yii::$app->docoPlugin->execute($this, 'invoice_edit_tagihan');
        $btnEditTagihanDetail = Yii::$app->docoPlugin->execute($this, 'invoice_edit_tagihan_detail');
        $roleBtnCloseBill = ($roleBtnCloseBill) ? 0 : 1;
        $isCloseBill = isset($response['response']['info']['is_close_bill']) ? $response['response']['info']['is_close_bill'] : false;
        $pasienAdmisiId = isset($response['response']['info']['pasienadmisi_id']) ? $response['response']['info']['pasienadmisi_id'] : null;
        $isStopAkomodasi = isset($response['response']['info']['is_stopakomodasi']) ? $response['response']['info']['is_stopakomodasi'] : false;
        $disabledBtnCloseBill = false;
        // if($pasienAdmisiId && !$isStopAkomodasi) {
        //     $disabledBtnCloseBill = true;
        // }

        if(empty($model->tmpTagihan['obat']) && empty($model->tmpTagihan['tindakan']) && empty($model->tmpTagihan['adm']) ){
            $isEditTagihan = false;
        } else {
            $isEditTagihan = true;
        }

        $pendaftaranId = isset($response['response']['info']['pendaftaran_id']) ? $response['response']['info']['pendaftaran_id'] : null;
        $jobOrder = $this->countJobOrder($pendaftaranId);
        $blockBilling = isset($konfigSistem['block_billing']) ? $konfigSistem['block_billing'] : false;
        $blockBilling = ($blockBilling) ? 1 : 0;
        $display = (!empty($jobOrder) && $jobOrder > 0) ? 1 : 0;
        $penjaminInsurance = null;
        $penjaminDisUmum = null;
        if($kelompok == DocoConstants::PASIEN_PULANG) {
            $configOtoritasPenjamin = filter_var($configOtoritasPenjamin, FILTER_VALIDATE_BOOLEAN);
            if(is_array($discountInsurance)) {
                foreach ($discountInsurance as $value) {
                    if($value['penjamin_id'] == $penjamin_id) {
                        $penjaminInsurance = (int) $value['diskon_otomatis'];
                    } else {
                        $penjaminDisUmum = (int) $value['diskon_otomatis'];
                    }
                }
            }
        } else {
            $configOtoritasPenjamin = false;
        }
        
        return $this->render('view', get_defined_vars());
    }

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new TagihanPasienForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        if ($request->post()) {
            $model->load($request->post());
            $model->detail_tagihan = $request->post('detail_tagihan');
            $model->ruangan_pelakhir_id = $request->post('ruangan_pelakhir_id');
            $model->pendaftaran_id = $request->post('pendaftaran_id');
            $model->pasienadmisi_id = $request->post('pasienadmisi_id');
            $model->pasien_id = $request->post('pasien_id');
            $model->nama_pasien = $request->post('nama_pasien');
            $model->instalasi_id = $request->post('instalasi_id');
            $model->status_pasien = $request->post('status_pasien');
            $model->condition = $request->post('condition');
            $model->penjualanresep_id = DocoHelpers::decrypt($request->post('penjualanresep_id'));
            $model->data_penjamin = $request->post('data_penjamin');
            $model->data_diskon = $request->post('data_diskon');
            $model->data_metode_pembayaran = $request->post('data_metode_pembayaran');
            $model->adm_asuransi = $request->post('adm_asuransi');
            $model->tagihan_dijamin = $request->post('tagihan_dijamin');
            $model->plafon_payer = $request->post('plafon_payer');
            $model->plafon_subpayer = $request->post('plafon_subpayer');
            $model->penjamin_id_main = $request->post('penjamin_id_main');
            $model->penjamin_id_sub = $request->post('penjamin_id_sub');
            $model->excess_pasien = $request->post('excess_pasien');
            $model->limit_penjamin = $request->post('limit_penjamin');
            $model->nominaldiskon_edit_tagihan = $request->post('nominaldisc_edit_tagihan');

            $type = DocoHelpers::decrypt($request->post('type'));
            if ($type == DocoConstants::ANTRIAN_POLI) {
                $model->type_antrian = $type;
            }
            
            if ($model->validate()) {
                // Disini validasi untuk kirim ke listing approval.
                $isApproval = $request->get("approval");
                $isApproval = $isApproval == "true" ? true : false;
                $response = $this->_restKasir->post('tagihan-pasien/save',[
                    'form_params' => $model->attributes,
                    'query' => [
                        'approval_penjamin' => $isApproval
                    ]
                ]);

                $response = json_decode($response->getBody(),true);
                if (isset($response['response']['pembayaran_id'])) {
                    $idPembayaran = DocoHelpers::encrypt($response['response']['pembayaran_id']);
                    $response['response']['pembayaran_id'] = $idPembayaran;
                }
                $response['response']['approval'] = $isApproval;
                           
                return DocoHelpers::response($response);
            } else {
                $response = $model->errors;
                $errors = DocoHelpers::parseError($response, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }
    }

    public function actionMultiPenjamin()
    {
        $title = Yii::t('fe', 'Detail Penjamin');
        $model = new MultiPenjamin;
        // get default penjamin dari pendaftaran
        $getDefaultPenjamin = explode('#', Yii::$app->cache->get("default-penjamin-modal"));
        $grupCaraBayar = isset($getDefaultPenjamin[3]) ? $getDefaultPenjamin[3] : null;
        if ($grupCaraBayar == DocoConstants::GROUP_UMUM) {
            $res_default = [];
            $penjamin_id = '';
        }else{
            $res_default = $getDefaultPenjamin[0];
            $model->no_kartu = $getDefaultPenjamin[1];
            $penjamin_id = $getDefaultPenjamin[2];
        }

        return $this->renderAjax('partial/_multi_penjamin', get_defined_vars());
    }

    public function actionEditTagihan($id)
    {
        $title = Yii::t('fe', 'Edit Tagihan');
        $request = Yii::$app->request;
        $model = new EditTagihan;
        if ($request->post()) {
            return DocoHelpers::response('Data berhasil disimpan sementara', 200);
        } else {
            return $this->renderAjax('partial/_edit_tagihan', get_defined_vars());
        }
    }

    public function actionMultiPembayaran()
    {
        $title = Yii::t('fe', 'Tambah Jenis Pembayaran');
        $model = new MultiPembayaran;

        return $this->renderAjax('partial/_multi_pembayaran', get_defined_vars());
    }

    public function actionDiskonDokter($id, $kelompok)
    {
        $title = Yii::t('fe', 'Diskon Dokter');
        $model = new DiskonDokter;
        $result =  $this->_restKasir->get("tagihan-pasien/get-jasa-dokter", [
            'query' => [
                'id' => $id,
                'kelompok' => $kelompok,
            ]
        ]);
        $body = json_decode($result->getBody(), 1);
        $list_data = $body["response"];
        // dump($list_data); die();
        
        $list_dokter = $list_data['list_dokter'];
        $list_jasa_dokter = $list_data['list_jasa_dokter'];

        return $this->renderAjax('partial/_diskon_dokter', get_defined_vars());
    }

    public function actionExportRincian($id, $tipe_pasien = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download")."/detail-rincian.pdf";
        $id = DocoHelpers::decrypt($id);
        $response = $this->_restKasir->get('tagihan-pasien/print-rincian',
            [
                'query' => [
                    'id' => $id,
                    'tipe_pasien' => $tipe_pasien,
                ],
                'save_to' => $path
            ]
        );
        return DocoHelpers::previewPdf($path);
    }

    public function actionExportKwitansi($id, $tipe_pasien = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download")."/kwitansi.pdf";
        $id = DocoHelpers::decrypt($id);
        $response = $this->_restKasir->get('tagihan-pasien/print-kwitansi',
            [
                'query' => [
                    'id' => $id,
                    'tipe_pasien' => $tipe_pasien,
                ],
                'save_to' => $path
            ]
        );

        return DocoHelpers::previewPdf($path);
    }

    public function actionExportBkm($id, $tipe_pasien = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $path = Yii::getAlias("@download")."/bukti_kas_masuk.pdf";
        $response = $this->_restKasir->get('tagihan-pasien/print-bkm',
            [
                'query' => [
                    'id' => $id,
                    'tipe_pasien' => $tipe_pasien,
                ],
                'save_to' => $path
            ]
        );

        $body = json_decode($response->getBody(), true);
        return DocoHelpers::previewPdf($path);
    }

    public function actionExportKarcis($id,$type = null)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $type = DocoHelpers::decrypt($type);
        $path = Yii::getAlias("@download")."/print_karcis.pdf";
        $response = $this->_restKasir->get('tagihan-pasien/print-karcis',
            [
                'query' => [
                    'id' => $id,
                    'type' => $type
                ],
                'save_to' => $path
            ]
        );

        return DocoHelpers::previewPdf($path, 'print_karcis');
    }
    
    public function actionCetakInvoice()
    {
        return Yii::$app->docoPlugin->execute($this, 'cetak_invoice');
        $request = Yii::$app->request;
        $id = $request->get('id');
        $invoice_id = $request->get('invoice_id');
        if(!is_numeric($id)) {
            $id = DocoHelpers::decrypt($id);
        }
        if(!is_numeric($invoice_id)) {
            $invoice_id = DocoHelpers::decrypt($invoice_id);
        }
        $kelompok = $request->get('kelompok', null);
        $jenis_invoice = $request->get('jenis_invoice', 1);
        $penjamin_id = $request->get('penjamin', null);
        $path = Yii::getAlias("@download")."/cetak-invoice.pdf";
        $userIdentity = Yii::$app->session->get('user_identity');
        $response = $this->_restKasir->get('tagihan-pasien/invoice', [
            'query' => [
                'id' => $id,
                'invoice_id' => $invoice_id,
                'nama_pegawai' => $userIdentity['nama_pegawai'],
                'kelompok' => $kelompok,
                'penjamin_id' => $penjamin_id,
                'jenis_invoice' => $jenis_invoice
            ],
            'save_to' => $path
        ]);
        $body = json_decode($response->getBody(), true);
        return DocoHelpers::previewPdf($path);
    }
    
    public function actionCetakDetailInvoice()
    {
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $invoice_id = $request->get('pembayaran_id', null);
        $kelompok = $request->get('kelompok', null);
        $status = $request->get('status', 1);
        $tipe_pasien = $request->get('tipe_pasien', null);
        $penjamin_id = $request->get('penjamin_id', null);
        if($request->get('invoice_id')) {
            $invoice_id = $request->get('invoice_id', null);
        }
        $jenis_invoice = $request->get('jenis_invoice', 1);
        if(!is_numeric($id)) {
            $id = DocoHelpers::decrypt($id);
        }
        if(!is_numeric($invoice_id)) {
            $invoice_id = DocoHelpers::decrypt($invoice_id);
        }
        if(!is_numeric($jenis_invoice)) {
            $jenis_invoice = DocoHelpers::decrypt($jenis_invoice);
        }
        if(!is_numeric($penjamin_id)) {
            $penjamin_id = DocoHelpers::decrypt($penjamin_id);
        }
        $path = Yii::getAlias("@download")."/cetak-detail-invoice.pdf";
        $userIdentity = Yii::$app->session->get('user_identity');
        $response = $this->_restKasir->get('tagihan-pasien/detail-invoice', [
            'query' => [
                'id' => $id,
                'invoice_id' => $invoice_id,
                'nama_pegawai' => $userIdentity['nama_pegawai'],
                'jenis_invoice' => $jenis_invoice,
                'kelompok' => $kelompok,
                'status' => $status,
                'tipe_pasien' => $tipe_pasien,
                'tipe' => $request->get('tipe'),
                'invoice_type' => $request->get('invoice_type'),
                'kelompok' => $request->get('kelompok'),
                'penjamin_id' => $penjamin_id,
            ],
            'save_to' => $path
        ]);
        $body = json_decode($response->getBody(), true);
        // dump($body);die;
        return DocoHelpers::previewPdf($path, $response);
    }

    public function actionGenerateInvoice()
    {
        $request = Yii::$app->request;
        $get = array_filter($request->get());
        $detailInvoice = isset($get['detailInvoice']) ? $get['detailInvoice'] : 0;
        $detailInvoice = ($detailInvoice == 1) ? true : false;
        $title = ($detailInvoice) ? 'Cetak Detail Invoice' : 'Cetak Invoice';
        $id = $get['id'];
        $pembayaran_id = $get['pembayaran_id'];
        $jenis_invoice = [1 => 'Invoice Total', 2 => 'Invoice Penjamin', 3 => 'Invoice Pasien'];
        $model = new CetakInvoiceForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $model->pembayaran_id = $pembayaran_id;
        $model->detailInvoice = $detailInvoice;
        $model->jenis_invoice = 1;
        if($request->post()) {
            $model->load($request->post());
            $model->pembayaranpelayanan_id = $id;
            if(!$model->validate()) {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }

            return DocoHelpers::response(['data' => $model->attributes]);
        }
        else {
            return $this->renderAjax('_cetak_invoice', get_defined_vars());
        }
    }

    public function actionCetakDetailInvoiceInacbg($id)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $path = Yii::getAlias("@download")."/cetak-detail-invoice-inacbg.pdf";
        $userIdentity = Yii::$app->session->get('user_identity');
        $response = $this->_restKasir->get('tagihan-pasien/detail-invoice-inacbg', [
            'query' => [
                'id' => $id,
                'nama_pegawai' => $userIdentity['nama_pegawai']
            ],
            'save_to' => $path
        ]);
        
        return DocoHelpers::previewPdf($path, $response);
    }

    public function actionCetakSip()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        if(!is_numeric($id)) {
            $id = DocoHelpers::decrypt($id);
        }
        
        $path = Yii::getAlias("@download")."/cetak-sip.pdf";
        $userIdentity = Yii::$app->session->get('user_identity');
        $response = $this->_restKasir->get('tagihan-pasien/cetak-sip', [
            'query' => [
                'id' => $id,
                'nama_pegawai' => $userIdentity['nama_pegawai'],
            ],
            'save_to' => $path
        ]);
        
        $body = json_decode($response->getBody(), true);
        return DocoHelpers::previewPdf($path);
    }

    public function actionSaveTmpTagihan(){
        
        $request = Yii::$app->request;
        if ($request->post()) {
            $pendaftaran_id = $request->post('_pendaftaran_id');
            $pasienmasukpenunjang_id = $request->post('_pasienmasukpenunjang_id');
            $verify_uid = $request->post('verify_uid');
            $plafon_payer = $request->post('plafon_payer');
            $plafon_subpayer = $request->post('plafon_subpayer');
            $excess_pasien = $request->post('excess_pasien');
            $verify_uid = $request->post('verify_uid');
            $params = $request->post('_tagihanPasien');
            $_uidProccess = $request->get('_uidProccess');
            $queryParams = Yii::$app->session->get($_uidProccess);
            $payload = [];
            foreach ($params as $value) {
                $row = $value;
                if (isset($row['value'])) {
                    unset($row['value']);
                }
                $payload[] = $row;
            }
            $response = $this->_restKasir->post('tagihan-pasien/save-tmp-tagihan', [
                'query' => $queryParams,
                'form_params' => [
                    '_tagihanPasien' => json_encode($payload),
                    'pendaftaran_id' => $pendaftaran_id,
                    'pasienmasukpenunjang_id' => $pasienmasukpenunjang_id,
                    'verify_uid' => $verify_uid,
                    'plafon_payer' => $plafon_payer,
                    'plafon_subpayer' => $plafon_subpayer,
                    'excess_pasien' => $excess_pasien,
                ]
            ]);

            $response = json_decode($response->getBody(),true);
            yii::error($response);
            return DocoHelpers::response($response);
        }else{
            return DocoHelpers::responseTemplate(200, 'Error', 'Tidak ada tindakan yang diinputkan');
        }
    }

    public function actionDownloadInvoice()
    {
        $request = Yii::$app->request;
        $fileName = $request->get('fileName', null);
        $noPendaftaran = $request->get('noPendaftaran', null);
        if(!empty($noPendaftaran)) {
            $noPendaftaran = DocoHelpers::decrypt($noPendaftaran);
        }
        $path = Yii::getAlias("@download")."/Cetak Detail Rincian Tagihan - ".$noPendaftaran.".pdf";
        $response = $this->_restKasir->get('tagihan-pasien/download-invoice',
        [
            'query' => [
                'fileName' => $fileName,
            ],
            'save_to' => $path,
        ]);
        $response = json_decode($response->getBody(), true);
        return DocoHelpers::previewPdf($path);
    }

    public function actionGetKontrakPenjamin()
    {
        $request = Yii::$app->request;
        try {
            if ($request->post()) {
                $penjamin = $request->post('penjamin', []);
                $tgl_pendaftaran = $request->post('tgl_pendaftaran', null);
                $response = $this->_restKasir->post('tagihan-pasien/get-kontrak-penjamin', [
                    'form_params' => [
                        'penjamin' => $penjamin,
                        'tgl_pendaftaran' => $tgl_pendaftaran,
                    ]
                ]);
    
                $response = json_decode($response->getBody(),true);
                return DocoHelpers::response($response);
            }else{
                return DocoHelpers::responseTemplate(200, 'Error', 'Tidak ada tindakan yang diinputkan');
            }
        } catch (\Throwable $e) {
            Yii::error([$e]);
        }
    }

    public function actionLogActivity()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get("id");
        $title = "Log Edit Tagihan";

        try {
            $response = $this->_restKasir->post('tagihan-pasien/get-log-activity', [
                'query' => [
                    'pendaftaran_id' => $pendaftaran_id,
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $datalog = $response['response']['data'];
        } catch (\Throwable $th) {
            //throw $th;
        }

        return $this->renderAjax('partial/_log_activity', get_defined_vars());
    }

    private function countJobOrder($id)
    {
        return $this->guzzleExec($this->_restKasir, [
            'url' => "tagihan-pasien/count-job-order",
            'payload' => [
                'query' => [
                    'pendaftaran_id' => $id,
                ]
            ],
        ]);
    }

    public function actionDetailJobOrder()
    {
        $title = 'Detail Order';
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        // $data = $this->guzzleExec($this->_restKasir, [
		// 	'url' => 'tagihan-pasien/get-data-order',
		// 	'payload' => [
		// 		'query' => [
        //             'pendaftaran_id' => $id
        //         ],
		// 	]
		// ]);
        return $this->renderAjax('partial/_job_order', get_defined_vars());
    }

    public function actionGetDataOrder()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $pendaftaranId = $request->get('pendaftaran_id');
        $payload = DocoDatatableHelper::advancedFilterParam();
        $payload['pendaftaran_id'] = $pendaftaranId;
        $response = $this->guzzleExec($this->_restKasir, [
            'url' => "tagihan-pasien/get-data-order",
            'payload' => [
                'query' => $payload,
            ]
        ]);
        $response['recordsTotal'] = $response['_meta']['totalCount'];
        $response['recordsFiltered'] = $response['_meta']['totalCount'];
        return $response;
    }

    public function actionCloseBill()
    {
        $request = Yii::$app->request;
        try {
            if ($request->isPost) {
                $pendaftaranId = $request->get('id');
                $response = $this->_restKasir->post('tagihan-pasien/close-bill', [
                    'query' => [
                        'pendaftaran_id' => $pendaftaranId
                    ],
                    'form_params' => $request->post()
                ]);
                $response = json_decode($response->getBody(),true);
                return DocoHelpers::response($response);
            }
        } catch (\Throwable $e) {
            Yii::error([$e]);
        }
    }

    public function actionCetakInvoiceGabung()
    {
        return Yii::$app->docoPlugin->execute($this, 'cetak_invoice_gabung');
    }
}