<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;
use yii\web\View;

$this->title = Yii::t('fe', 'Daily Cutoff');
?>

<div class="row">
    <div class="col-md-12">
        
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title?></b></h3>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">Transaksi</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <?php echo Html::dropDownList('filter_trx_daily', '',
                                [
                                    'extract-master-patient' => 'Patient', 
                                    'extract-saleorder' => 'Sale Order', 
                                    'extract-saleorderline-tindakan' => 'Sale Order Line Tindakan',
                                    'extract-saleorderline-obat' => 'Sale Order Line Obat',
                                    'extract-saleorderbill' => 'Sale Order Bill',
                                    'extract-scrollcashier' => 'Scroll Cashier',
                                    'extract-inpatientdeposit' => 'Inpatient Deposit',
                                    'extract-patientdebt' => 'Patient Debt',
                                    'extract-stockscrap' => 'Stock Scrap',
                                    'extract-grnreceipt' => 'GRN Receipt',
                                    'extract-grnreceiptdetail' => 'GRN Receipt Detail',
                                    'extract-grnissue' => 'GRN Issue',
                                    'extract-grnissuedetail' => 'GRN Issue Detail'
                                ],
                                [
                                    'id' => 'filter_trx_daily',
                                    'class' => 'form-control select2 select2_master_data',
                                    'prompt' => \Yii::t('fe', '-- Pilih --'),
                                ]
                            ) ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <?php
                                echo Html::textInput('tanggal_trx_daily','',[
                                    'id' => 'tanggal_trx_daily',
                                    'class'=>'form-control'
                                ]);
                            ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button id="resend_transaksi" class="btn btn-info btn-labeled btn-xs"><b><i class="fa fa-paper-plane"></i></b>Generate</button>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">Master</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <?php echo Html::dropDownList('filter_master_data', '',
                                [
                                    'extract-master-productcategory' => 'Product Category', 
                                    'extract-master-uom' => 'UOM', 
                                    'extract-master-ruangan' => 'Ruangan',
                                    'extract-master-partner' => 'Partner',
                                    'extract-master-tindakanpaket' => 'Tindakan/Paket',
                                    'extract-master-obat' => 'Obat',
                                    'extract-master-barang' => 'Barang'
                                ],
                                [
                                    'id' => 'filter_master_data',
                                    'class' => 'form-control select2 select2_master_data',
                                    'prompt' => \Yii::t('fe', '-- Pilih --'),
                                ]
                            ) ?>
                        </div>
                    </div>
                    <div class="col-md-3 col-md-offset-3">
                        <button id="resend_master" class="btn btn-info btn-labeled btn-xs"><b><i class="fa fa-paper-plane"></i></b>Generate</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php
$this->registerJs('
    $(".select2_master_data").select2();
    var newDate = new Date();
    var yesterdayDate = newDate.setDate(newDate.getDate() - 1);
    var $input_tanggal_trx_daily = $("#tanggal_trx_daily").pickadate({
        applyClass: "bg-slate-600",
        cancelClass: "btn-default",
        locale: {
            format: "DD-MMMM-YYYY"
        },
        today:false,
        max: yesterdayDate
    });
    $("#resend_transaksi").on("click",function(){
        var picker = $input_tanggal_trx_daily.pickadate("picker");
        var _date = picker.get("select","yyyy-mm-dd");
        var _key = $("#filter_trx_daily").val();
        $.ajax({
            method: "POST",
            data: {
                key: _key,
                date: _date
            },
            url: baseUrl+"master/dashboard-odoo/resend-daily-trx",
            success: function(res) {
                docoNotification("success", i18next.t("Proses Berhasil"), i18next.t(res.message));
            },
            error: function(data) {
                docoNotification("error", i18next.t("Proses Gagal"), i18next.t("Terjadi Kesalahan"));
            }
        });
    });
    $("#resend_master").on("click",function(){
        var _key = $("#filter_master_data").val();
        $.ajax({
            method: "POST",
            data: {
                key: _key
            },
            url: baseUrl+"master/dashboard-odoo/resend-daily-master",
            success: function(res) {
                docoNotification("success", i18next.t("Proses Berhasil"), i18next.t(res.message));
            },
            error: function(data) {
                docoNotification("error", i18next.t("Proses Gagal"), i18next.t("Terjadi Kesalahan"));
            }
        });
    });
', View::POS_END, 'e-index');
?>