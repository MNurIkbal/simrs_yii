<?php

/**
 * @author Yaya
 * @copyright 26 April 2018
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\modules\kasir\components\widget\CloseBillWidget;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Kasir', 'url' => ['/kasir']];
$this->params['breadcrumbs'][] = $breadcrumbs;
$this->params['breadcrumbs'][] = $this->title;
$statusBelumBayar =  !empty($statusBelumBayar) ?  $statusBelumBayar : 0;
if($disableSip == 1) {
    $disabled = 0;
}else{
    $disabled = ($status == 1 || $statusBelumBayar == 1) ? 1 : 0;
}

$isResetTagihan = ($isResetEditTagihan == true) ? 1 : 0;
$_tipe_pasien = null;

?>

<style type="text/css">
.doco-number {
    font-weight: bold;
}
</style>

<?php $form = ActiveForm::begin([
    'id' => 'ajax-form',
    'action' => '/kasir/pembayaran-tagihan/create',
    'enableAjaxValidation'=>false,
    'enableClientValidation'=>false,
    'type' => ActiveForm::TYPE_VERTICAL,
    'options' => [
        'skip-confirm' => "true"
    ]
]);
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <div class="col-md-9">
                    <?= DocoHelpers::generateToolbar([
                        'save' => [
                            'title' => $labelSimpan,
                            'attributes' => [
                                'id' => 'save-pasien-karcis',
                                'onClick' => '',
                                'disabled' =>  $disabled ? true : false
                            ]
                        ],
                        'invoice' => $btnInvoice,
                        'detailinvoice' => $btnDetailInvoice,
                        // 'detail-invoice-belum-bayar' => $btnDetailInvoiceBelumBayar,
                        // 'invoice' => [
                        //     'title' => Yii::t('fe', 'Cetak Invoice'),
                        //     'icon' => 'fa fa-print',
                        //     'method' => '#',
                        //     'attributes' => [
                        //         'id'=>'cetak-invoice',
                        //         'data-options' => 'link',
                        //         'disabled' =>  $disabled ? false : true
                        //     ]
                        // ],
                        // 'detail-invoice-belum-bayar' => [
                        //     'type' => 'button',
                        //     'title' => Yii::t('fe', 'Cetak Detail Invoice'),
                        //     'icon' => 'fa fa-print',
                        //     'attributes' => [
                        //         'id' => 'cetak-invoice-belum-bayar',
                        //         'data-toggle' => 'modal',
                        //         'data-target' => '#modal_backdrop',
                        //         'data-width' => '50%',
                        //         'action' => '/kasir/inf-pasien-sudah-bayar/show-popup-belum-bayar?id='.$id.'&kelompok='.$kelompok,
                        //     ]
                        // ],
                        // 'detailinvoice' => [
                        //     'title' => Yii::t('fe', 'Cetak Detail Invoice'),
                        //     'icon' => 'fa fa-print',
                        //     'method' => '#',
                        //     'attributes' => [
                        //         'id'=>'cetak-detail-invoice',
                        //         'data-options' => 'link',
                        //         'disabled' =>  $disabled ? $statusBtnDetailRi : true
                        //     ]
                        // ],
                        // 'print-rincian' => [
                        //     'type' => 'button',
                        //     'title' => 'Print Rincian',
                        //     'icon' => 'fa fa-print',
                        //     'attributes' => [
                        //         'class' => 'data-lihat print-tagihan',
                        //         'id' => 'print-tagihan',
                        //         'method' => 'json',
                        //         'data-options' => 'link',
                        //         'disabled' =>  $disabled ? false : true
                        //     ]
                        // ],
                        'custom-print-kwitansi' => [
                            'type'=>'button',
                            'title' => \Yii::t('fe', 'Print Kwitansi'),
                            'icon' => 'fa fa-print',
                            'attributes' => [
                                'id' => 'print-kwitansi',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'data-width' => '50%',
                                'action' => '/kasir/inf-pasien-sudah-bayar/cetak-kwitansi?id='.$id.'&pembayaran_id=',
                                'disabled' =>  ($disabled && $statusBelumBayar != 1) ? false : true
                            ]
                        ],
                        // 'custom-print' => [
                        //     'type'=>'button',
                        //     'title' => Yii::t('fe', 'Print BKM'),
                        //     'icon' => 'fa fa-print',
                        //     'attributes' => [
                        //         'class' => 'print-bkm print-tagihan',
                        //         'method' => 'json',
                        //         'data-options' => 'link',
                        //         'disabled' =>  $disabled ? false : true
                        //     ],
                        // ],
                        'print-karcis' => [
                            'type' => 'button',
                            'title' => 'Print Karcis',
                            'icon' => 'fa fa-print',
                            'method' => '#',
                            'attributes' => [
                                'class' => 'data-lihat',
                                'id' => 'print-karcis',
                                'data-type' => DocoHelpers::encrypt($jenisAntrian),
                                'method' => 'json',
                                'data-options' => 'link',
                                'disabled' => $disabled
                            ]
                        ],
                        'print-sip' => [
                            'type' => 'button',
                            'title' => 'Print Surat Izin Pulang',
                            'icon' => 'fa fa-print',
                            'method' => '#',
                            'attributes' => [
                                'id' => 'print-sip',
                                'data-options' => 'link',
                                'disabled' =>  $disabled ? false : true
                            ]
                        ],
                        'edit-tagihan' => [
                            'type' => 'button',
                            'title' => 'Edit Tagihan',
                            'icon' => 'fa fa-edit',
                            'attributes' => [
                                'id' => 'edit-tagihan',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action' => '/kasir/pembayaran-tagihan/edit-tagihan?id='.$id,
                                'data-width' => '100%',
                            ]
                        ],
                        'recalculate-payer' => [
                            'title' => 'Kalkulasi Payer',
                            'icon' => 'fa fa-refresh',
                            'attributes' => [
                                'data-options' => 'click',
                                'id' => 'recalculate-payer',
                            ]
                        ],
                        'reset-edit-tagihan' => [
                            'title' => 'Reset Edit Tagihan',
                            'icon' => 'fa fa-refresh',
                            'attributes' => [
                                'data-options' => 'click',
                                'id' => 'reset-edit-tagihan',
                            ]
                        ],
                        'print-edit-tagihan' => $btnEditTagihan,
                        'print-edit-tagihan-detail' => $btnEditTagihanDetail,
                        'log-activity' => [
                            'type' => 'button',
                            'title' => 'Log Activity',
                            'icon' => 'fa fa-list',
                            'attributes' => [
                                'id' => 'edit-tagihan',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action' => '/penatajasa/inf-tagihan-pasien/log-activity?pendaftaran_id='.$id.'&is_kasir=1',
                                'data-width' => '70%',
                                'class' => $is_logactivity ? "" : "hidden"
                            ]
                        ],
                        'job-order' => [
                            'type' => 'button',
                            'title' => 'Job Order',
                            'icon' => 'fa fa-eye',
                            'attributes' => [
                                'id' => 'job-order',
                                'data-options' => 'click',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action' => '/kasir/pembayaran-tagihan/detail-job-order?id='.$pendaftaranId,
                                'data-width' => '70%',
                            ]
                        ],
                        'close-bill' => CloseBillWidget::widget(),
                        'back' => [
                            'attributes' => [
                                'href' => $homeUrl
                            ]
                        ],
                    ]) ?>
                </div>
                <div class="col-md-3 pull-right content-tagihan-lain" 
                    data-popup="tooltip-custom" 
                    data-trigger="hover" 
                    data-content="<ul>
                        <li>Tagihan Karcis : 
                            <b><span class='pull-right'>Rp. 3.000.000</span></b>
                        </li>
                        <li>Tagihan Obat : 
                            <b><span class='pull-right'>Rp. 3.000.000</span></b>
                        </li>
                        <li>Tagihan Penunjang : 
                            <b><span class='pull-right'>Rp. 6.000.000</span></b>
                        </li>
                    </ul>" 
                    data-placement="bottom" 
                    tabindex="0"
                    style="display: none"
                >
                    <span style="font-size: 22px;font-weight: bold;">
                        <p style="color:white;background: #C0392B;text-align: center;">
                            Tagihan Lain : <span class="info-tagihan-lain" style="color:white;background: #C0392B;text-align: center;"></span>
                        </p>
                    </span>
                </div>
            </div>
            <br>
            <div class="panel-body">
                <!--Informasi Pasien-->
                <div class="row row-eq-height " style="margin-top:10px;">
                    <?= Yii::$app->controller->renderPartial('_informasi_pasien', [
                        'model' => $model,
                        'form' => $form,
                        'caraBayar' => $caraBayar,
                        'penjamin' => $penjamin,
                        'disabled' => $disabled,
                        'isKarcis' => $isKarcis,
                        'jenisAntrian' => $jenisAntrian,
                        'id' => $id,
                        'adm_persen' => $adm_persen,
                        'biaya_adm_maksimal' => $biaya_adm_maksimal,
                        'labelCaraBayar' => $labelCaraBayar,
                        'konfigSistem' => $konfigSistem,
                        'listPenjamin' => $listPenjamin,
                        'totalTagihan' => $totalTagihan,
                        'total_admin' => $total_admin,
                        'is_ranap' => $is_ranap,
                        'konfirmasi_match' => $konfirmasi_match,
                    ]) ?>
                    
                    <!--Detail Pembayaran-->
                    <?= Yii::$app->controller->renderPartial('_detail_pembayaran', [
                        'model' => $model,
                        'form' => $form,
                        'id' => $id,
                        'kelompok' => $_kelompok,
                    ]) ?>

                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
$this->registerJs('
tagihan_belumbayar = "'. (int) $tagihan_belumbayar.'";
kelompok = "'.$_kelompok.'";
var _instance_adm = '. json_encode($instance_adm) .';
var _uidProccess = "'. $randString .'";
var status = "'. $status .'";
var _id = "'.$id.'";
var _uid = "'.$uid.'";
var _instalasi_id = "'.$instalasi_id.'";
const cetakRincian = "/kasir/inf-pasien-belum-bayar/cetak-rincian?id=";
var isEditTagihan = "'.$isEditTagihan.'";
var isResetTagihan = "'.$isResetTagihan.'";
var statusBelumBayar = '.$statusBelumBayar.'
var _roleBtnCloseBill = "'.$roleBtnCloseBill.'"
var _is_close_bill = "'.$isCloseBill.'";
var disabledBtnCloseBill = "'.$disabledBtnCloseBill.'"
var discountInsurance = "'.$penjaminInsurance.'"
var configOtoritasPenjamin = "'.$configOtoritasPenjamin.'"
var discountUmum = "'.$penjaminDisUmum.'"

if($("#btn-cetak-invoice").length == 1) {
    $("#btn-cetak-invoice").attr("data-options", "link");
    $("#btn-cetak-invoice").attr("data-toggle", "modal");
    $("#btn-cetak-invoice").removeAttr("data-url");
    $("#btn-cetak-invoice").removeAttr("data-conditions");
}
if(status == 0) {
    $("#cetak-invoice").prop("disabled", true);
    $("#btn-cetak-invoice").prop("disabled", true);
    $("#cetak-detail-invoice").prop("disabled", true);
    $("#cetak-detail-invoice-adhy").prop("disabled", true);
    $("#btn-cetak-detail-invoice-pembayaran").prop("disabled", true);
}
else {
    $("#cetak-invoice").prop("disabled", false);
    $("#btn-cetak-invoice").prop("disabled", false);
    $("#cetak-detail-invoice").prop("disabled", false);
    $("#cetak-detail-invoice-adhy").prop("disabled", false);
    $("#btn-cetak-detail-invoice-pembayaran").prop("disabled", false);
}

if(isResetTagihan == 0){
    $("#reset-edit-tagihan").hide()
}
var url_detail = "/kasir/inf-pasien-belum-bayar/show-popup-detail-designer?id=" + _id+"&instalasi_id="+_instalasi_id+"&isdetail=true";
var url_summary = "/kasir/inf-pasien-belum-bayar/show-popup-detail-designer?id=" + _id+"&instalasi_id="+_instalasi_id+"&isdetail=false";
$("#print-detail-edit-tagihan").attr("action",url_detail);
$("#print-summary-edit-tagihan").attr("action",url_summary);

// $("#print-summary-edit-tagihan").click(function(e){
//     e.preventDefault();
//     var url = cetakRincian+_id+"&instalasi_id="+_instalasi_id;
//     window.open(url);
// });

// if(!isEditTagihan){
//     $("#print-detail-edit-tagihan").hide();
//     $("#print-summary-edit-tagihan").hide();
// }

$("#cetak-invoice-belum-bayar").attr("action", "/kasir/inf-pasien-sudah-bayar/show-popup-belum-bayar?id=" + _id + "&kelompok=" + kelompok)
if(kelompok == "'.DocoConstants::PASIEN_PENUNJANG.'") {
    $("#print-karcis").hide();
}

var _display = "'.$display.'";
var _blockBilling = "'.$blockBilling.'";
$("#job-order").hide();

if(_roleBtnCloseBill == 0) {
    $(".close-bill").removeClass("hidden")
    var url_close_bill = "/kasir/pembayaran-tagihan/close-bill?id=" + _id;
    if(_is_close_bill) {
       $(".close-bill").html("<b><i class=\"fa fa-key\"></i></b>Unlock Bill")
    }
    else {
       $(".close-bill").html("<b><i class=\"fa fa-key\"></i></b>Lock Bill")
    }
    $(".close-bill").attr("action",url_close_bill);
    $(".close-bill").attr("data-close-bill", (_is_close_bill) ? 1 : 0)
    $(".close-bill").attr("data-job-order", _display)
    if(disabledBtnCloseBill == 1) {
        $(".close-bill").prop("disabled", true)
    }
    else {
        $(".close-bill").prop("disabled", false)
    }
 }
', View::POS_END);
?>