<?php

/**
 * @author Yaya
 * @copyright 23 March 2018 
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\typeahead\Typeahead;
use app\components\DocoHelpers;
use kartik\widgets\TimePicker;
use kartik\widgets\DatePicker;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"), 'url' => []];
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', $title)];

?>
<style>
    .datepicker>div{
        display:block;
    }
</style>
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?= Html::button('<b><i class="fa fa-floppy-o"></i></b>'.Yii::t('fe', ' Simpan'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'id' => 'simpan'
                    ]);
                ?>
            </div>
            <div class="panel-body">
                <?php 
                    $form = ActiveForm::begin([
                        'id' => 'ajax-form', 
                        'action' => '/bankdarah/pemesanan-darah/set-list-item',
                        'enableAjaxValidation'=>false, 
                        'enableClientValidation'=>false,
                        'type' => ActiveForm::TYPE_HORIZONTAL,
                        'formConfig' => [
                            'labelSpan' => 3, 
                            'deviceSize' => ActiveForm::SIZE_SMALL
                        ],
                        'options' => [
                            'skip-confirm' => "true"
                        ]
                    ]); 

                    echo Html::hiddenInput('PesanDarahPmiForm[total_harga]', null, [
                        'class' => 'total_harga'
                    ]);

                    echo Html::hiddenInput('PesanDarahPmiForm[total_kantongdarah]', null, [
                        'class' => 'total_kantongdarah'
                    ]);
                ?>
                <br>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($model, 'supplier_id', [
                            'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-3 text-bold',
                                'wrapper' => 'col-md-8'
                            ]
                            ])->dropDownList(ArrayHelper::map($dataRequest['pmi'], 'supplier_id', 'supplier_nama'),[
                                'class' => 'form-control select2',
                                'id' => 'supplier_id',
                                'prompt' => Yii::t('fe', 'Pilih Nama PMI')
                            ])->label(Yii::t('fe', 'Nama PMI')); ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'alamat', [
                            'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-3 text-bold',
                                    'wrapper' => 'col-md-5'
                                ]
                            ])->staticInput(['class' => 'alamat'])
                            ->label(Yii::t('fe', 'Alamat')); ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'no_tlp', [
                            'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-3 text-bold',
                                    'wrapper' => 'col-md-5'
                                ]
                            ])->staticInput(['class' => 'no_tlp'])
                            ->label(Yii::t('fe', 'No Telepon')); ?>
                        </div>
                    </div>
                </div>
                <br><br><hr>
                <div class="col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b>Data Pemesanan Darah PMI</b></h6>
                        </div>
                        <div class="panel-body">
                            <?= $form->field($modelDetail, 'jenisdarah_id',[
                                    'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ]
                                ])->dropDownList(ArrayHelper::map($dataRequest['jenis_darah'], 'jenisdarah_id', 'jenisdarah_nama'),[
                                    'class' => 'select2',
                                    'id' => 'jenisdarah_id',
                                    'prompt' => Yii::t('fe', 'Pilih Jenis Darah')
                                ])->label(Yii::t('fe', 'Jenis Darah')); 
                            ?>
                            <?= $form->field($modelDetail, 'golongandarah_id',[
                                    'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ]
                                ])->dropDownList(ArrayHelper::map($dataRequest['gol_darah'], 'lookup_id', 'lookup_name'),[
                                    'class' => 'select2',
                                    'id' => 'golongandarah_id',
                                    'prompt' => Yii::t('fe', 'Pilih Golongan Darah')
                                ])->label(Yii::t('fe', 'Golongan Darah')); 
                            ?>
                            <?= $form->field($modelDetail, 'rhesus',[
                                    'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ]
                                ])->checkbox(['label' => Yii::t('fe', 'Resus Positif'), 'class' => 'styled'], false)->label(Yii::t('fe', 'Resus Positif')); 
                            ?>
                            <div class="form-group div-tgl_mintakirim">
                                <label class="text-left control-label col-sm-4">
                                <?= Yii::t('fe', 'Tanggal di Kirim') ?> 
                                <span class="text-danger">*</span></label>
                                <?php $modelDetail->tgl_mintakirim = date('d-M-Y'); ?>
                                <?= $form->field($modelDetail, 'tgl_mintakirim', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-8'
                                ]])->widget(DatePicker::classname(), [
                                    'name' => 'date_12',
                                    'value' => date('Y-m-d'),
                                    'type' => DatePicker::TYPE_INPUT,
                                    'readonly' => true,
                                    'language' => 'en',
                                    'pluginOptions' => [
                                        'autoclose' => true,
                                        'format' => 'dd-M-yyyy',
                                        'startDate' => "0d",
                                    ]
                                ])->label(false); ?>
                            </div>

                            <?= $form->field($modelDetail, 'wkt_mintakirim', [
                            'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-4'
                                ]
                            ])->textInput([
                                    'class' => 'form-control input-sm wkt_mintakirim',
                                    'data-mask' => '99:99'
                                ])->label(Yii::t('fe', 'Waktu di Kirim')); 
                            ?>

                            <?= $form->field($modelDetail, 'qty_pesan', [
                            'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-4'
                                ]
                            ])->textInput([
                                'placeholder' => Yii::t('fe', 'Jumlah'),
                                'class' => 'form-control input-sm typeahead doco-number text-right',
                                'maxlength' => 4,
                                'id' => "qty_pesan",
                            ])->label(Yii::t('fe', 'Jumlah')); ?>

                            <div class="btn-group pull-right">
                                <?= Html::submitButton(
                                    '<b><i class="fa fa-plus"></i></b>' . Yii::t('fe','Tambah'), 
                                        [
                                            'class' => 'btn btn-success btn-labeled btn-xs btn-tambah',
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b>Tabel Pemesanan Darah PMI</b></h6>
                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <table id="pemesanan-darah" 
                                class="table table-striped table-condensed table-hover" style="width:100%">
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th width="1">No</th>
                                            <th><?=\Yii::t("fe", "Jenis Darah");?></th>
                                            <th><?=\Yii::t("fe", "Golongan Darah");?></th>
                                            <th><?=\Yii::t("fe", "Rhesus");?></th>
                                            <th><?=\Yii::t("fe", "Tanggal di Kirim");?></th>
                                            <th><?=\Yii::t("fe", "Jumlah");?></th>
                                            <th><?=\Yii::t("fe", "Harga");?></th>
                                            <th><?=\Yii::t("fe", "Sub Total");?></th>
                                            <th><?=\Yii::t("fe", "Aksi");?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center" colspan="9">
                                                <?=\Yii::t("fe", "Data tidak ditemukan.");?>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <footer>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td style="text-align: right;width: 20%"><strong> Total Kantong: </strong></td>
                                            <td style="text-align: right;"><strong><span class="subTotalKantong"></span></strong></td>
                                            <td style="text-align: right;width: 20%"><strong> Total Harga: </strong></td>
                                            <td style="text-align: right;"><strong><span class="subTotal"></span></strong></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                    </footer>
                                </table>
                                
                            </div>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    var table;
    var attributes = {};
    $("input[type=checkbox]").uniform({
        radioClass: \'choice\'
    });

    $("#ajax-form").submit(function(event){
        event.preventDefault();
        var _value = $(this).serializeArray();
        if (Object.keys(attributes).length) {
            $.each(attributes, function (key, val) {
                _value.push({
                    name : key,
                    value : val
                });
            });
        } 
        $(this).docoForm("submit",{
            data : _value,
            success : function (data) {
                $("#jenisdarah_id").val("").trigger("change");
                $("#golongandarah_id").val("").trigger("change");
                $("#qty_pesan").val(1).trigger("change");
                $("#pesandarahpmidetailform-wkt_mintakirim").val("").trigger("change");
                $("#pesandarahpmidetailform-rhesus").prop("checked", true).trigger("change");
                $("#pesandarahpmidetailform-rhesus").closest("span").addClass("checked");
                table.draw();
            }
        });
    });

    $(document).on(\'click\',\'.delete\', function(event) {
        event.preventDefault();
        $(this).docoForm(\'delete\',{
            skipConfirm: true,
            success : function (data) {
                table.draw();
            }
        });
    });
    
    $("#supplier_id").on("change", function(){
        var _supplier_id = $(this).val();
        $.ajax({
            type: "GET",
            url: "/bankdarah/pemesanan-darah/get-data-pmi?supplier_id=" + _supplier_id,
            success: function(data) {
                $(".alamat").text(data.supplier_alamat);
                $(".no_tlp").text(data.no_tlp);
            }
        });
    });

    $(document).ready(function() {
        $("#datetime").val(function() {
            var d = new Date();
            return ("0" + d.getDate()).slice(-2) + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + d.getFullYear() + " " + ("0" + d.getHours()).slice(-2) + ":" + ("0" + d.getMinutes()).slice(-2);;
        });
        
        $("#datetime").AnyTime_picker({
            format: "%d-%m-%Y %H:%i",
        });
        $(".pickadate").pickadate({
            format: "dd-mm-yyyy",
            formatSubmit: "yyyy-mm-dd",
            startDate: "0d",
            onStart: function() {
                var date = new Date();
                this.set("select", [[date.getFullYear(), date.getMonth() + 1, date.getDate()]]);
            }
        });
        
        $("#pesandarahpmidetailform-wkt_mintakirim").on("change", function(){
            var wkt_mintakirim = $(this).val();
            var result = wkt_mintakirim.split(":");
            var jam = result[0];
            var menit = result[1];

            if(jam > 23 || menit > 59) {
                docoNotification("error", "Error", "Format Waktu Tidak Sesuai.");
                $("#pesandarahpmidetailform-wkt_mintakirim").val("");
                $(".btn-tambah").prop("disabled", true);
            }
            else {
                $(".btn-tambah").prop("disabled", false);
            }
        });
        
        table = $("#pemesanan-darah").docoTabel({
            filter: false,
            displayLength: 20,
            lengthChange : false,
            processing: true,
            serverSide: true,
            paging: false,
            info: false,
            scrollY: "260px",
            ajax: baseUrl+"bankdarah/pemesanan-darah/get-list-item",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false,
                    width: "1"
                },
                {
                    title: "'.(\Yii::t("fe", "Jenis Darah")).'", 
                    data: "jenisdarah_nama",
                    searchable: false,
                    orderable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Golongan Darah")).'", 
                    data: "golongandarah_nama",
                   searchable: false,
                    orderable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Rhesus")).'",
                    data: "rhesus",
                    searchable: false,
                    orderable: false,
                },
               {
                    title: "'.(\Yii::t("fe", "Tanggal di Kirim")).'", 
                    data: "tgl_mintakirim",
                    searchable: false,
                    orderable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Jumlah")).'",
                    data: "jumlah",
                    searchable: false,
                    orderable: false,
                    class: "text-right"
                },
                {
                    title: "'.(\Yii::t("fe", "Harga (Rp.)")).'",
                    data: "harga",
                    searchable: false,
                    orderable: false,
                    class: "text-right"
                },
                {
                    title: "'.(\Yii::t("fe", "Sub Total (Rp.)")).'",
                    data: "subTotal",
                    searchable: false,
                    orderable: false,
                    class: "text-right"
                },
                {
                    title: "'.(\Yii::t("fe", "Aksi")).'",
                    data: "aksi",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                }
            ],
            drawCallback : function (settings) {
                var api = this.api();
                var dataRows = api.rows( {page:"current"} ).data();
                
                var total_harga = 0;
                var total_kantongdarah = 0;
                $.each(dataRows, function (key, val) {
                    total_harga += parseInt(val.subTotal2);
                    total_kantongdarah += parseInt(val.jumlah);
                });
                $(".total_harga").val(docoHelper.convertToAngka(total_harga));
                $(".total_kantongdarah").val(docoHelper.convertToAngka(total_kantongdarah));
                $(".subTotal").text(docoHelper.convertToRupiah(total_harga));
                $(".subTotalKantong").text(docoHelper.convertToAngka(total_kantongdarah));
            }
        });
    });
    
    $("#simpan").on("click",function (event) {
        event.preventDefault();
        $(this).docoForm("click",{
            url : "/bankdarah/pemesanan-darah/save",
            method : "POST",
            type : "json",
            data : {
                supplier_id : $("#supplier_id").val(),
                total_harga : $(".total_harga").val(),
                total_kantongdarah : $(".total_kantongdarah").val(),
            },
            success : function (data) {
                console.log(data);
                var no_pesandarahpmi = data.response.no_pesandarahpmi;
                var urlCetak = "/bankdarah/pemesanan-darah/cetak?no_pesandarahpmi="+no_pesandarahpmi;
                $("#supplier_id").val("").trigger("change");
                $(".alamat").text("");
                $(".no_tlp").text("");
                table.draw();
                (new PNotify({
                    title: "Berhasil",
                    text: "Data Berhasil di Simpan dengan Nomor Pemesanan Darah PMI " + "<strong>" + no_pesandarahpmi + "</strong>" + " , Apakah Anda Ingin Mencetak Bukti Pemesanan Darah PMI?",
                    addclass: "alert alert-success alert-arrow-right alert-styled-right",
                    type: "success",
                    buttons: {
                        closer: false,
                        sticker: false
                    },
                    hide: false,
                    confirm: {
                        confirm: true,
                        buttons: [
                            {
                                text: "Ya",
                                addClass: "btn btn-xs btn-success",
                            },
                            {
                                text: "Tidak",
                                addClass: "btn btn-xs btn-danger",
                            }
                        ]
                    },
                    history: {
                        history: false
                    }
                })).get().on("pnotify.confirm", function() {
                    window.open(urlCetak);
                }).on("pnotify.cancel", function() {
                    
                });
            }
        });
    });
    
    

',View::POS_END,'pemesanan-darah');