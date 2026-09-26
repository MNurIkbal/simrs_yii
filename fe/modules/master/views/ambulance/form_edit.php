<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-03-09 11:00:02
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2019-01-07 16:36:04
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\form\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\DatePicker;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .datepicker>div{
        display:block;
    }
    p.barang_merk {
        font-size: 15px;
        padding-left: 5px;
        font-weight: 500;
    }
    .panel-heading{
        margin-bottom: 10px;
    }
    legend {
        font-weight: bold !important;
        padding: 5px;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= Html::button('<b><i class="fa fa-floppy-o"></i></b>'.Yii::t('fe', ' Simpan'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'id' => 'btn-simpan',
                        // 'disabled' => ($data['status_periksa'] == 577) ? false : true,
                    ]);
                ?>
                <?=
                    DocoHelpers::generateToolbar([
                        'back',
                    ]);
                ?>
            </div>

            <div class="panel-body">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= Yii::t('fe','Form').' '.$this->title ?></b></h6>
                        </div>
                        <div class="panel-body">
							<?php $form = ActiveForm::begin([
                                'id' => 'form', 
                                'action' => "/master/ambulance/update?id=".DocoHelpers::encrypt($id),
                                'enableAjaxValidation'=>false, 
                                'enableClientValidation'=>false,
                                'type' => ActiveForm::TYPE_HORIZONTAL,
                                'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL] 
                            ]); 
                            echo Html::hiddenInput('satuan_kecil', '', ['class' => 'satuan_kecil']);
                            ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= $form->field($model, 'barang_id')->dropDownList($barang,[
                                        'class' => 'form-control select2',
                                        'prompt' => Yii::t('fe', '— Pilih Nama Barang —'),
                                        'id' => 'barang_id',
                                    ])->label(Yii::t('fe', 'Nama Barang')); ?>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group control-label col-sm-3 barang_merk_label">Merek</div>
                                    <div class="col-sm-9">
                                        <p style="margin: 10px;" class="barang_merk"><b></b></p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <?=$form->field($model, 'is_emergency')->radioList($jenisAmbulan, [
                                        'inline' => true,
                                    ])->label(Yii::t("fe", "Jenis Ambulan")); ?>
                                </div>
                                <div class="col-md-6">
                                    <?=$form->field($model, 'no_polisi')?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <?=$form->field($model, 'keterangan')->textArea(['rows' => 2])->label(Yii::t("fe", "Note"))?>
                                </div>
                            </div><br>
                            <div class="row">
                                <legend>
                                    <b><li>
                                            <?= Yii::t('fe','Tarif Tindakan') ?>
                                        </li>
                                    </b>
                                </legend>
                                <div class="col-md-6">
                                    <?= $form->field($model, 'daftartindakan_id')->dropDownList([],[
                                        'class' => '',
                                        'id' => 'daftartindakan_id',
                                    ])->label(Yii::t('fe', 'Tarif')); ?>
                                </div>
                                <div class="col-md-3">
                                    <?= Html::button('<b><i class="fa fa-plus"></i></b>'.Yii::t('fe', ' Tambah'), 
                                        [
                                            'class' => 'btn btn-info btn-labeled btn-xs',
                                            'id' => 'btn-tambah',
                                        ]);
                                    ?>
                                </div>
                            </div>
                            <br><br>
                            <table id="tindakan" class="table table-striped table-condensed table-hover" style="width:100%">
			                    <thead>
			                        <tr class="bg-inverse">
			                            <th>No</th>
			                            <th><?=\Yii::t("fe", "Tindakan");?></th>
			                            <th><?=\Yii::t("fe", "Biaya Tetap");?></th>
                                        <th><?=\Yii::t("fe", "Aksi");?></th>
			                        </tr>
			                    </thead>
			                    <tbody>
			                        <tr>
			                            <td class="text-center" colspan="9"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
			                        </tr>
			                    </tbody>
			                </table>
                            <hr>
                            <div class="row">
                                 <legend>
                                    <b><li>
                                            <?= Yii::t('fe','Obat Alkes') ?>
                                        </li>
                                    </b>
                                </legend>
                                <div class="col-md-6">
                                    <?= $form->field($model, 'obatalkes_id')->dropDownList([],[
                                        'class' => '',
                                        'id' => 'obatalkes_id',
                                    ])->label(Yii::t('fe', 'Default Obat Alkes Yang di Bawa')); ?>
                                </div>
                                <div class="col-md-3">
                                    <?=$form->field($model, 'qty')->textInput(['
                                        class' => 'doco-number',
                                        'placeholder' => Yii::t('fe', 'Qty')
                                    ])?>
                                </div>
                                <div class="col-md-3">
                                    <?= Html::button('<b><i class="fa fa-plus"></i></b>'.Yii::t('fe', ' Tambah'), 
                                        [
                                            'class' => 'btn btn-info btn-labeled btn-xs',
                                            'id' => 'btn-tambah-obat',
                                        ]);
                                    ?>
                                </div>
                            </div>
                            <br><br>
                            <table id="obat" class="table table-striped table-condensed table-hover" style="width:100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th>No</th>
                                        <th><?=\Yii::t("fe", "Nama Obat Alkes");?></th>
                                        <th><?=\Yii::t("fe", "Qty");?></th>
                                        <th><?=\Yii::t("fe", "Aksi");?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center" colspan="9"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                                    </tr>
                                </tbody>
                            </table>
			                <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 

$barangMerk = isset($data['header']['barang_merk']) ? $data['header']['barang_merk'] : '';
$barangNama = isset($model->barang_nama) ? $model->barang_nama : '';
$barangId = isset($model->barang_id) ? $model->barang_id : '';
$this->registerJs('
    var table;
    var tableObat;
    var ambulan_id = "'.$ambulan_id.'";
    var barang_id = "'.$barangId.'";
    var barang_nama = "'.$barangNama.'";
    var barang_merk = "'.$data['header']['barang_merk'].'";
    $(".barang_merk").text(": " + barang_merk);
    // $(".barang_merk_label").hide();
    $(document).ready(function(){
        $("#barang_id").on("change", function() {
            var barang_id = $(this).val();
            $.get("/master/ambulance/get-merek", {
                barang_id:barang_id,
            },function(data){
                var merek = ": " + data.barang_merk;
                $(".barang_merk_label").show();
                $(".barang_merk").text(merek);
            });
        })
        $("#daftartindakan_id").select2({
            placeholder: "— Pilih Tarif —",
            minimumInputLength: 3, 
            ajax : {
                url: "/master/ambulance/search-tindakan",
                dataType: "json",
                quietMillis: 250,
                data: function (params) {
                  var query = {
                    search: params,
                  }
                  return params; 
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                },
                dropdownCssClass: "bigdrop",
                escapeMarkup: function (m) { return m; },
            },
        });

        $("#obatalkes_id").select2({
            placeholder: "Pilih Obat Alkes",
            minimumInputLength: 3, 
            ajax : {
                url: "/master/ambulance/search-obat",
                dataType: "json",
                quietMillis: 250,
                data: function (params) {
                  var query = {
                    search: params,
                  }
                  return params; 
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                },
                dropdownCssClass: "bigdrop",
                escapeMarkup: function (m) { return m; },
            },
        }).on("select2:select", function(e){
            var data = e.params.data;
            $(".satuan_kecil").val(data.satuan_kecil);
        });

        table = $("#tindakan").docoTabel({
            filter: false,
            displayLength: 10,
            processing: true,
            serverSide: true,
            stateSave: true,
            lengthChange: false,
            paging: false,
            ajax: baseUrl+"master/ambulance/get-list-tindakan?ambulan_id=" + ambulan_id,
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "Tindakan", 
                    data: "daftartindakan_nama",
                    orderable: false
                },
                {
                    title: "Biaya Tetap", 
                    data: "biaya_tetap",
                    orderable: false
                },
                {
                    title: "Aksi",
                    data: "aksi",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                }
            ],
            drawCallback:function(){
                $(".chk_tindakan, .multiselect-container input").uniform({
                    radioClass: \'choice\'
                });
            }
        });

        tableObat = $("#obat").docoTabel({
            filter: false,
            displayLength: 10,
            processing: true,
            serverSide: true,
            stateSave: true,
            lengthChange: false,
            paging: false,
            ajax: baseUrl+"master/ambulance/get-list-obat?ambulan_id=" + ambulan_id,
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "Nama Obat Alkes", 
                    data: "obatalkes_nama",
                    orderable: false
                },
                {
                    title: "Qty", 
                    data: "qty",
                    orderable: false,
                    class: "text-right",
                },
                {
                    title: "Aksi",
                    data: "aksi",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                }
            ],
        });
    });
    
    // $(document).on("click",".delete-cache-tindakan", function(event) {
    //     event.preventDefault();
    //     $(this).docoForm("delete",{
    //         skipConfirm : true,
    //         skipSuccessNotif: true,
    //         success : function (data) {
    //             table.draw();
    //         }
    //     });
    // });

    $(document).on("click", ".delete-cache-tindakan", function (event) {
        event.preventDefault();
        var id = $(this).data("id");
        var ambulanceid = $(this).data("ambulanceid");
        var action = $(this).data("action");
        var button = this;
        var valButton = $(button).html();
        var ResData = {
                        ambulan_id : ambulanceid,
                        id : id
                    };
        console.log(action);
        $(this).docoForm("click",{
            url: action,
            confirmTitle: i18next.t("Konfirmasi"),
            confirmMessage: i18next.t("Apa anda yakin ingin membatalkan data ini?"),
            data: ResData,
            method: "GET",
            before: function () {
                $(button).html("<i class=\"fa fa-spin fa-spinner\"></i>");
                $(button).prop("disabled", true);
            },
            success: function () {
                tableObat.draw();
                $(button).parent().parent().remove();
                docoNotification("success", i18next.t("Berhasil"), i18next.t("Data berhasil di hapus"));
            }
        });
    });
    
    $(document).on("change", ".chk_tindakan", function(){
        var biaya_tetap = $(".chk_tindakan").val();
        var daftartindakan_id = $(this).attr("data-id");
        if(this.checked) {
            biaya_tetap = 1;
        }
        else {
            biaya_tetap = 0;
        }
        var resData = {
            ambulan_id: ambulan_id,
            biaya_tetap : biaya_tetap,
            daftartindakan_id : daftartindakan_id,
        };
        $.ajax({
            url: "/master/ambulance/update-cache-biaya-tetap",
            type: "get",
            data: resData,
            success: function (data) {
                // var $remote = $("#daftartindakan_id");
                // $remote.html("").select2("data",null);
                // table.draw();
                // docoNotification("success", data.response.title, data.response.text);
            },
            error: function (res) {
                // docoNotification("error", data.title, data.text);
            }
        });
    });
    
    $(document).on("click",".delete-tindakan-lama", function(event) {
        event.preventDefault();
        $(this).docoForm("delete",{
            skipConfirm : true,
            skipSuccessNotif: true,
            success : function (data) {
                table.draw();
            }
        });
    });

    $(document).on("click",".delete-cache-obat", function(event) {
        event.preventDefault();
        var id = $(this).data("id");
        var ambulanceid = $(this).data("ambulanceid");
        var action = $(this).data("action");
        var button = this;
        var valButton = $(button).html();
        var ResData = {
                        ambulan_id : ambulanceid,
                        id : id
                    };
        $(this).docoForm("click",{
            url: action,
            confirmTitle: i18next.t("Konfirmasi"),
            confirmMessage: i18next.t("Apa anda yakin ingin membatalkan data ini?"),
            data: ResData,
            method: "GET",
            before: function () {
                $(button).html("<i class=\"fa fa-spin fa-spinner\"></i>");
                $(button).prop("disabled", true);
            },
            success: function () {
                tableObat.draw();
                $(button).parent().parent().remove();
                docoNotification("success", i18next.t("Berhasil"), i18next.t("Data berhasil di hapus"));

            }
        });
    });

    // $(document).on("click",".delete-cache-obat", function(event) {
    //     event.preventDefault();
    //     $(this).docoForm("delete",{
    //         skipConfirm : true,
    //         skipSuccessNotif: true,
    //         success : function (data) {
    //             tableObat.draw();
    //         }
    //     });
    // });

    // $(document).on("click",".delete-obat-lama", function(event) {
    //    event.preventDefault();
    //     var id = $(this).data("id");
    //     var action = $(this).data("action");
    //     var button = this;
    //     var valButton = $(button).html();
    //     var ResData = {};
    //     console.log(action);
    //     $(this).docoForm("click",{
    //         url: action,
    //         confirmTitle: i18next.t("Konfirmasi"),
    //         confirmMessage: i18next.t("Apa anda yakin ingin membatalkan data ini?"),
    //         data: ResData,
    //         method: "GET",
    //         before: function () {
    //             $(button).html("<i class=\"fa fa-spin fa-spinner\"></i>");
    //             $(button).prop("disabled", true);
    //         },
    //         success: function () {
    //             tableObat.draw();
    //             $(button).parent().parent().remove();
    //             docoNotification("success", i18next.t("Berhasil"), i18next.t("Data berhasil di hapus"));

    //         }
    //     });
    // });

    // $("#btn-tambah").on("click", function (event) {
    //     var daftartindakan_id = $("#daftartindakan_id").val();
        
    //     $.post("/master/ambulance/set-cache", {
    //         daftartindakan_id:daftartindakan_id,

    //     },function(data){
    //         var $remote = $("#daftartindakan_id");
    //         $remote.html("").select2("data",null);
    //         table.draw();
    //         if(data.status == 200){
    //              docoNotification("success", data.title, data.text);
    //         }
    //         if(data.status == 500){
    //              docoNotification("error", data.title, data.text);
    //         }
    //     });
    // });

    $("#btn-tambah").on("click", function (event) {
        var daftartindakan_id = $("#daftartindakan_id").val();
        if ( daftartindakan_id == null ) {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Tarif tidak boleh kosong"));
            return false;
        }
        var resData = {
            ambulan_id: ambulan_id,
            daftartindakan_id:daftartindakan_id
        };

        $.ajax({
            url: "/master/ambulance/set-cache",
            type: "post",
            data: resData,
            success: function (data) {
                var $remote = $("#daftartindakan_id");
                $remote.html("").select2("data",null);
                table.draw();
                docoNotification("success", data.response.title, data.response.text);
            },
            error: function (res) {
                docoNotification("error", data.title, data.text);
            }
        });
    });
    
    $(document).on("click", "#btn-simpan", function (event) {
        var _data = $("#form").serializeArray();
        $("#form").docoForm("submit",{
            data : _data,
            success : function (data) {
                window.location.href = "/master/ambulance";
            }
        });
        $("#form").trigger("submit");
    });

    $("#btn-tambah-obat").on("click", function (event) {
        var obatalkes_id = $("#obatalkes_id").val();
        var satuan_kecil = $(".satuan_kecil").val();
        var _qty = $("#ambulanform-qty").val();

        if ( obatalkes_id == null ) {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Obat Alkes tidak boleh kosong"));
            return false;
        }
        if ( _qty == "" ) {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Qty tidak boleh kosong"));
            return false;
        }
        var resData = {
                    ambulan_id: ambulan_id,
                    obatalkes_id:obatalkes_id,
                    qty: _qty,
                    satuan_kecil:satuan_kecil,
                };

        $.ajax({
            url: "/master/ambulance/set-cache-obat",
            type: "post",
            data: resData,
            success: function (data) {
                var $remote = $("#obatalkes_id");
                $remote.html("").select2("data",null);
                $("#ambulanform-qty").val("");
                $(".satuan_kecil").val("");

                tableObat.draw();
                docoNotification("success", data.response.title, data.response.text);
            },
            error: function (res) {
                docoNotification("error", data.title, data.text);
            }
        });
    })
', View::POS_END, 'b-index');
?>