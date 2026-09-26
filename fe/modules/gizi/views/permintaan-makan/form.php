<?php

/**
 * @Author: Sigit
 * @Date:   2019-01-03 16:13:53
 */

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use kartik\datetime\DateTimePicker;

$this->title = Yii::t('fe', 'Ubah Permintaan Makan');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Informasi Permintaan Makan'), 'url' => ['inf-permintaan-makan']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style type="text/css">
    .select2-container, .select2-selection {
        width: 100% !important;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
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
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar">
                <?= DocoHelpers::generateToolbar([
                    'back' => [
                        'attributes' => [
                            'href' => '/gizi/inf-permintaan-makan'
                        ]
                    ]
                ]) ?>
            </div>
            <div class="panel-body">
                <!-- identitas pasien start -->
                <?=Yii::$app->controller->renderPartial('_pasien_identitas', [
                    'data_pasien' => $data_pasien
                ]);?>
                <!-- identitas pasien end -->

                <div class="row">
                    <div class="col-md-12">
                        <hr>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h6 class="panel-title"><?= Yii::t('fe', 'Daftar Permintaan Makan') ?></h6>
                            </div>
                            <div class="panel-body">
                                <?php
                                    $form = ActiveForm::begin([
                                        'id' => 'permintaan-makan-form',
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
                                ?>

                                <div class="row">

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <?=
                                                Html::activeCheckbox($model, 'is_ditagihkan', [
                                                    'label' => 'Ditagihkan',
                                                    'class' => 'uniform'
                                                ])
                                            ?>
                                        </div>
                                        <div class="col-sm-12">
                                            <?= $form->field($model, 'keterangan',[
                                            'horizontalCssClasses' => [
                                                'label' => 'col-sm-1',
                                                'wrapper' => 'col-md-11'
                                            ]
                                            ])->textarea(array('rows'=>2,'cols'=>3));
                                            ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-3">
                                        <?= $form->field($model, 'jenisdiet_id', [
                                            'horizontalCssClasses' => [
                                                'label' => 'col-sm-4',
                                                'wrapper' => 'col-sm-8'
                                                ]
                                            ])->dropDownList([], ['id' => 'select_jenis_diet']) ?>
                                        </div>

                                        <div class="col-sm-3">
                                        <?= $form->field($model, 'jenisdiet_lainnya', ['horizontalCssClasses' => ['wrapper' => 'col-sm-10']])->textInput(['class' => 'form-control default-disabled input-tag', 'id' => 'jenis_diet_lainnya'])->label(false) ?>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row">
                                                <p> Keterangan </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                        <?= $form->field($model, 'makanandiet_id', [
                                            'horizontalCssClasses' => [
                                                'label' => 'col-sm-2',
                                                'wrapper' => 'col-sm-4'
                                                ]
                                            ])->dropDownList([], ['id' => 'select_menu_diet']) ?>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="row">
                                                <p>I. Puasa</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                        <?= $form->field($model, 'perubahan_diet', [
                                            'horizontalCssClasses' => [
                                                'label' => 'col-sm-2',
                                                'wrapper' => 'col-sm-4'
                                                ]
                                            ])->dropDownList([], ['id' => 'select_perubahan_diet']) ?>
                                        </div>
                                        <div class="col-sm-6">
                                        <?= $form->field($model, 'kondisi_puasa', [
                                            'horizontalCssClasses' => [
                                                'label' => 'col-sm-2',
                                                'wrapper' => 'col-sm-4'
                                                ]
                                                    ])->textInput() ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                        <?= $form->field($model, 'kesimpulan', [
                                            'horizontalCssClasses' => [
                                                'label' => 'col-sm-2',
                                                'wrapper' => 'col-sm-4'
                                                ]
                                                    ])->textInput() ?>
                                        </div>
                                        <div class="col-sm-3">
                                        <?= $form->field($model, 'puasa_tgl_awal', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->widget(DateTimePicker::classname(), [
                                            'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                                            'size' => 'md',
                                            'convertFormat' => true,
                                            'removeButton' => false,
                                            'pluginOptions' => [
                                                'format' => 'dd/MM/yyyy HH:mm:ss',
                                                'autoclose' => true,
                                                'todayBtn' => true
                                            ]
                                        ]); ?>
                                        </div>
                                        <div class="col-sm-3">
                                        <?= $form->field($model, 'puasa_tgl_akhir', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-2',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->widget(DateTimePicker::classname(), [
                                            'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                                            'size' => 'md',
                                            'convertFormat' => true,
                                            'removeButton' => false,
                                            'pluginOptions' => [
                                                'format' => 'dd/MM/yyyy HH:mm:ss',
                                                'autoclose' => true,
                                                'todayBtn' => true
                                            ]
                                        ]);
                                        ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                        </div>

                                        <div class="col-sm-3">
                                        <?= $form->field($model, 'puasa_operasi_awal', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->widget(DateTimePicker::classname(), [
                                            'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                                            'size' => 'md',
                                            'convertFormat' => true,
                                            'removeButton' => false,
                                            'pluginOptions' => [
                                                'format' => 'dd/MM/yyyy HH:mm:ss',
                                                'autoclose' => true,
                                                'todayBtn' => true
                                            ]
                                        ]); ?>
                                        </div>
                                        <div class="col-sm-3">
                                        <?= $form->field($model, 'puasa_operasi_akhir', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-2',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->widget(DateTimePicker::classname(), [
                                            'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                                            'convertFormat' => true,
                                            'removeButton' => false,
                                            'pluginOptions' => [
                                                'format' => 'dd/MM/yyyy HH:mm:ss',
                                                'autoclose' => true,
                                                'todayBtn' => true
                                            ]
                                        ]);
                                        ?>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">

                                        </div>
                                        <div class="col-sm-6">
                                            <div class="row">
                                                <p>II. Buka Puasa</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                        </div>

                                        <div class="col-sm-3">
                                        <?= $form->field($model, 'buka_puasa', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->widget(DateTimePicker::classname(), [
                                            'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                                            'removeButton' => false,
                                            'convertFormat' => true,
                                            'pluginOptions' => [
                                                'format' => 'dd/MM/yyyy HH:mm:ss',
                                                'autoclose' => true,
                                                'todayBtn' => true,
                                            ]
                                        ]); ?>
                                        </div>
                                    </div>

                                    <div class="row">
                                            <div class="col-sm-6">
                                        </div>
                                        <div class="col-sm-3">
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="text-right">
                                                <button type="button" style="margin-right: 5px" class="btn btn-info btn-labeled btn-right btn-xs data-back" id="btn-ubah-permintaan-makan"><b><i class="fa fa-save"></i></b>Ubah</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php ActiveForm::end(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('

var _listIncremnt = {};
var is_valid = false;
var dataWaktuDiet = '.$dropdownWaktuDiet.';
var jenis_diett = '.$jenisDiet.';
var menu_diet = '.$menu_diet.';
var j_diet = '.$j_diet.';
var m_diet = '.$m_diet.';
var p_diet = '.$p_diet.';
var d_tindakan = '.$d_tindakan.';
var perubahan_diet = '.$perubahan_diet.';
var pendaftaran_id = "'.$pendaftaran_id.'";
var id = "'.$id.'";


$(document).ready(function() {
    $(".uniform").uniform()
    $("#jenis_diet_lainnya").attr("disabled", true);

    $("#select_jenis_diet").select2({
        data: jenis_diett
    })
    $("#select_menu_diet").select2({
        data: menu_diet
    })
    $("#select_perubahan_diet").select2({
        data: perubahan_diet
    })

    if(j_diet !== 0){
        $("#select_jenis_diet").val(j_diet).trigger("change")
    }
    if(m_diet !== 0 && d_tindakan !== 0){
        $("#select_menu_diet").val(m_diet+"-"+d_tindakan).trigger("change")
    }
    if(p_diet !== 0){
        $("#select_perubahan_diet").val(p_diet).trigger("change")
    }


    $(".mintamakan-addrow").click(function(){
        var parent = $("#tr-default");
        var _row = parseInt(parent.attr("data-last"));

        var _inc = parseInt(_row) + 1;

        var cek = [];



        $("span.help-block.error").remove();
        $(".cek-minta-makan").each(function() {
            // Cek value
            var logo_err  = "<i class=\'fa fa-exclamation-circle\' aria-hidden=\'true\'></i> &nbsp";

            var _field = $(this).closest("div.form-group");
            var info_field = "";
            if($(this).data("fieldjenis")){
                switch($(this).data("fieldjenis")){
                    case "jenis_diet" :
                        info_field = "Jenis Diet Belum Dipilih";
                        break;
                    case "menu_diet" :
                        info_field = "Menu Diet Belum Dipilih";
                        break;
                    case "waktu_diet" :
                        info_field = "Waktu Diet Belum Dipilih";
                        break;
                    case "jumlah_diet" :
                        info_field = "Jumlah Belum Diisi";
                        break;
                    default:
                        info_field = "Field Belum Sesuai";
                }
            }

            if($(this).data("fieldjenis") == "waktu_diet" && $(this).val() == -1 && $(this).val() != ""){
                cek.push("");
                $(this).parent().closest("div").addClass("has-error");
                _field.after("<span class=\'help-block error\'>"+ logo_err + info_field + "</span>");
            }
            if($(this).data("fieldjenis") == "jumlah_diet" && $(this).val() == 0 && $(this).val() != ""){
                cek.push("");
                $(this).parent().closest("div").addClass("has-error");
                _field.after("<span class=\'help-block error\'>"+ logo_err + "Jumlah Harus Lebih dari 0</span>");
            }
            if ($(this).val() != null && $(this).val() != "") {
                cek.push($(this).val());
                if ($(this).val() == "") {
                    // Add class
                    $(this).parent().closest("div").addClass("has-error");
                    _field.after("<span class=\'help-block error\'>"+ logo_err + info_field + "</span>");
                }
                else {
                    // Remove class
                    $(this).parent().closest("div").removeClass("has-error");
                }
            }
            else {
                cek.push("");
                if ($(this).val() == "" || $(this).val() == null) {
                    $(this).parent().closest("div").addClass("has-error");
                    _field.after("<span class=\'help-block error\'>"+ logo_err + info_field + "</span>");
                }
                else {
                    $(this).parent().closest("div").removeClass("has-error");
                }
            }
        });

        if (jQuery.inArray("", cek) !== -1) {
            // Notification
            docoNotification("error", "Tidak bisa tambah", "Ada field yang belum diisi");
            return false;
        }

        parent.attr("data-last", _inc);
        // parent.hide();
        var jenis_diet = "<div class=\'form-group\'><div class=\'col-sm-12\'><select name=\'PermintaanMakanForm["+_inc+"][jenis_diet]\' data-fieldjenis=\'jenis_diet\' class=\'form-control input-sm cek-minta-makan selectJenisDiet\' id=\'jenis_diet_"+_inc+"\'></select></div></div>";

        var menu_diet = "<div class=\'form-group\'><div class=\'col-sm-12\'><select name=\'PermintaanMakanForm["+_inc+"][menu_diet]\' data-fieldjenis=\'menu_diet\' class=\'form-control cek-minta-makan input-sm menu_diet selectMenuDiet\' id=\'menu_diet_"+_inc+"\'></select></div></div>";

        var waktu_diet = "<div class=\'form-group\'><div class=\'col-sm-12\'><select name=\'PermintaanMakanForm["+_inc+"][waktu_diet]\' data-fieldjenis=\'waktu_diet\' class=\'form-control cek-minta-makan input-sm selectWaktuDiet\' id=\'waktu_diet_"+_inc+"\'></select></div></div>";

        var jumlah_diet = "<div class=\'form-group\'><div class=\'col-sm-12\'><input type=\'text\' name=\'PermintaanMakanForm["+_inc+"][jumlah_diet]\' data-fieldjenis=\'jumlah_diet\' maxlength=5 class=\'form-control cek-minta-makan jumlah_diet input-sm doco-number text-right\' id=\'jumlah_diet_"+_inc+"\'></div></div>";

        var keterangan = "<div class=\'form-group\'><div class=\'col-sm-12\'><textarea name=\'PermintaanMakanForm["+_inc+"][keterangan]\' rows=2 cols=15 maxlength=200 style=\'resize:none;\' class=\'form-control keterangan input-sm \' id=\'keterangan_"+_inc+"\'></textarea></div></div>";

        var button_delete = "<button type=\'button\' class=\'mintamakan-deleteRow btn btn-danger btn-custom\'><span class=\'fa fa-trash\'></span></button>";

        var data = "<tr data-row=\'"+ _inc +"\' class=\'child\'><td class=\'jenis_diet\'>"+jenis_diet+"</td><td class=\'menu_diet\'>"+menu_diet+"</td>" + "<td class=\'waktu_diet\'>"+waktu_diet+"</td><td class=\'jumlah_diet\'>"+jumlah_diet+"</td><td>"+keterangan+"</td><td class=\'text-right\'>"+button_delete+"</td></tr>";

        $("#tr-default").before(data);
        $(".selectWaktuDiet").select2({
            data:dataWaktuDiet
        });
        $(".selectMenuDiet").select2();

        $(".selectJenisDiet").select2({
            placeholder: "Pilih Jenis Diet",
            allowClear: false,
            prompt: "- pilih -",
            data: '.$jenisDiet.',
            // minimumInputLength: 3,
            // ajax : {
            //     url: baseUrl+"gizi/asesmen-gizi/cari-jenis-diet",
            //     dataType: "json",
            //     quietMillis: 250,
            //     data: function (params) {
            //       params.id = pendaftaran_id;
            //       var query = {
            //         search: params,
            //       }
            //       return params;
            //     },
            //     processResults: function (data) {
            //         $.each(data.result, function (key,val) {
            //         });
            //           return {
            //             results: data.result
            //           };
            //     },
            //     dropdownCssClass: "bigdrop",
            //     escapeMarkup: function (m) { return m; },
            // },
        });
        $("#menu_diet_"+_inc).depdrop({
            depends: ["jenis_diet_"+_inc],
            url: "/gizi/asesmen-gizi/cari-menu-diet-by-jenis?id="+pendaftaran_id,
            placeholder:"Pilih Menu",
            loadingText:"Mencari.."
        });
        $("#menu_diet_"+_inc).on("depdrop:afterChange", function(event, id, value, jqXHR, textStatus) {
            $(this).focus();
        });

        $("#jenis_diet_"+_inc).focus();

    });

    $("#tbl-minta-makan").on("click", ".mintamakan-deleteRow",function(){
        var parent = $(this).closest("tr");
        var _row = parseInt(parent.attr("data-row"));
        parent.remove();

    });
});



$(document).on("change", "#select_jenis_diet ", function (e) {
    var v_jenis = $("#select_jenis_diet").val()
    if(v_jenis == 3 || v_jenis == 7 || v_jenis == 8 ||v_jenis == 14){
        $("#jenis_diet_lainnya").attr("disabled", false);
    }else {
        $("#jenis_diet_lainnya").attr("disabled", true);
        $("#jenis_diet_lainnya").val("");
    }

})
$(document).on("change", ".selectJenisDiet ", function (e) {
    $("span.help-block.error").remove();
    var parent = $(this).closest("tr");
    var _value = $(this).val();
    var _row = parseInt(parent.attr("data-row"));
    var _val_menu = $("#menu_diet_"+_row).val();

    if(is_valid) {
        is_valid = false;
        return false;

    }

    var dataRow = $(this).closest("tr").data("row");
    $("#tbl-minta-makan > tbody > tr").not("#tr-default").not("[data-row="+dataRow+"]").each(function(){
        var f_jenisdiet = $(this).find("td.jenis_diet").find(".selectJenisDiet").val();
        var f_menudiet = $(this).find("td.menu_diet").find(".selectMenuDiet").val();

        if(f_jenisdiet == _value &&  f_menudiet == _val_menu && _val_menu != null) {
            docoNotification("error", "Peringatan", "Data Jenis dan Menu Diet sudah ada.");
            is_valid = true;
            return false;
        }
    });
    if(is_valid) {
        $(this).val(null).trigger("change");
        return false;
    }

})

$(document).on("change", ".selectMenuDiet ", function (e) {
    $("span.help-block.error").remove();
    var parent = $(this).closest("tr");
    var _value = $(this).val();
    var _row = parseInt(parent.attr("data-row"));
    var _val_jenis = $("#jenis_diet_"+_row).val();

    if(is_valid) {
        is_valid = false;
        return false;

    }
    var dataRow = $(this).closest("tr").data("row");
    $("#tbl-minta-makan > tbody > tr").not("#tr-default").not("[data-row="+dataRow+"]").each(function(){
        var f_jenisdiet = $(this).find("td.jenis_diet").find(".selectJenisDiet").val();
        var f_menudiet = $(this).find("td.menu_diet").find(".selectMenuDiet").val();

        if(f_menudiet == _value && f_jenisdiet == _val_jenis && _val_jenis != null) {
            docoNotification("error", "Peringatan", "Data Jenis dan Menu Diet sudah ada.");
            is_valid = true;
            return false;
        }
    });

    if(is_valid) {
        $(this).val(null).trigger("change");
        return false;
    }

})

$(document).on("change", ".selectWaktuDiet ", function (e) {
    $("span.help-block.error").remove();
    var parent = $(this).closest("tr");
    var _value = $(this).val();
    var _row = parseInt(parent.attr("data-row"));

});
$(document).on("select2:select", ".selectMenuDiet ", function (e) {
    var parent = $(this).closest("tr");
    var _row = parseInt(parent.attr("data-row"));
    $("#waktu_diet_"+_row).focus();
});
$(document).on("select2:select", ".selectWaktuDiet ", function (e) {
    var parent = $(this).closest("tr");
    var _row = parseInt(parent.attr("data-row"));
    $("#jumlah_diet_"+_row).focus();
});

$("#btn-ubah-permintaan-makan").on("click",function(){
    var permintaan_makan = $("#permintaan-makan-form").serializeArray();
    $(this).docoForm("click",{
        url : baseUrl+"gizi/permintaan-makan/update?id="+id,
        method : "POST",
        data: permintaan_makan,
        success: function(data){

            var no_permintaanmakan = data.response.no_permintaanmakan;
            (new PNotify({
                title: "Berhasil",
                text: "Permintaan Makan dengan Nomor " + "<strong>" + no_permintaanmakan + "</strong>" + " berhasil ubah",
                addclass: "alert alert-success alert-arrow-right alert-styled-right",
                type: "success",
                buttons: {
                    closer: false,
                    sticker: false
                },
                hide: true,
                // confirm: {
                //     confirm: true,
                //     buttons: [
                //         {
                //             text: "Ya",
                //             addClass: "btn btn-xs btn-success",
                //         },
                //         {
                //             text: "Tidak",
                //             addClass: "btn btn-xs btn-danger",
                //         }
                //     ]
                // },
                history: {
                    history: false
                }
            })).get().on("pnotify.confirm", function() {
                window.open("/gizi/asesmen-gizi/cetak-permintaan-makan?id="+pendaftaran_id+"&no_permintaanmakan="+data.response.no_permintaanmakan);
            }).on("pnotify.cancel", function() {
            });

            // location.reload();
        },
        error: function(data){
            if(typeof data.responseJSON.response != "undefined") {
                var _response = data.responseJSON.response
                docoNotification("error", _response.title, _response.text);
            }
            else {
                if (data.status == "422") {
                    docoNotification("error", "Simpan gagal", "Silahkan cek inputan");
                }
            }
        }
    });

});

$("#btn-ubah-minta-makan").on("click", function(){

    var tbl_cek = $("#tbl-minta-makan > tbody > tr").not("#tr-default");
    if(tbl_cek.length < 1){
        docoNotification("error", "Proses Gagal", "Tidak Ada Data yang Disimpan");
        return false;
    }
    var cek = [];

    $("span.help-block.error").remove();
    $(".cek-minta-makan").each(function() {
        // Cek value
        var logo_err  = "<i class=\'fa fa-exclamation-circle\' aria-hidden=\'true\'></i> &nbsp";

        var _field = $(this).closest("div.form-group");
        var info_field = "";
        if($(this).data("fieldjenis")){
            switch($(this).data("fieldjenis")){
                case "jenis_diet" :
                    info_field = "Jenis Diet Belum Dipilih";
                    break;
                case "menu_diet" :
                    info_field = "Menu Diet Belum Dipilih";
                    break;
                case "waktu_diet" :
                    info_field = "Waktu Diet Belum Dipilih";
                    break;
                case "jumlah_diet" :
                    info_field = "Jumlah Belum Diisi";
                    break;
                default:
                    info_field = "Field Belum Sesuai";
            }
        }
        if($(this).data("fieldjenis") == "waktu_diet" && $(this).val() == -1 && $(this).val() != ""){
            cek.push("");
            $(this).parent().parent().closest("div").addClass("has-error");
            _field.after("<span class=\'help-block error\'>"+ logo_err + info_field + "</span>");
        }else{
            $(this).parent().parent().closest("div").removeClass("has-error");
        }
        if($(this).data("fieldjenis") == "jumlah_diet" && $(this).val() == 0 && $(this).val() != ""){
            cek.push("");
            $(this).parent().parent().closest("div").addClass("has-error");
            _field.after("<span class=\'help-block error\'>"+ logo_err + " Jumlah Harus Lebih dari 0</span>");
        }else{
            $(this).parent().parent().closest("div").removeClass("has-error");
        }
        if ($(this).val() != null && $(this).val() != "") {
            cek.push($(this).val());
            if ($(this).val() == "") {
                // Add class
                $(this).parent().parent().closest("div").addClass("has-error");
                _field.after("<span class=\'help-block error\'>"+ logo_err + info_field + "</span>");
            }
            else {
                // Remove class
                $(this).parent().parent().closest("div").removeClass("has-error");
            }
        }
        else {
            cek.push("");
            if ($(this).val() == "" || $(this).val() == null) {
                $(this).parent().parent().closest("div").addClass("has-error");
                _field.after("<span class=\'help-block error\'>"+ logo_err + info_field + "</span>");
            }
            else {
                $(this).parent().parent().closest("div").removeClass("has-error");
            }
        }
    });

    if (jQuery.inArray("", cek) !== -1) {
        // Notification
        docoNotification("error", "Tidak bisa tambah", "Ada field yang belum diisi");
        return false;
    }
    var tbl_data = $("#tbl-minta-makan").find("tbody").find(":input").serializeArray();
    $.each(tbl_data, (k,v) => {
        if (v.name.indexOf("menu_diet") >= 0) {
            let _colName = v.name.replace("]", "").split("[")
            tbl_data.push({
                name: `${_colName[0]}[${_colName[1]}][daftartindakan_id]`,
                value: $(`select[name="${v.name}"] :selected`).data("daftartindakan_id")
            })
        }
    })
    $(this).docoForm("click", {
        url : baseUrl+"gizi/permintaan-makan/update?id="+id,
        method : "POST",
        type : "json",
        data: tbl_data,
        success: function(data){
            var no_permintaanmakan = data.response.no_permintaanmakan;

            (new PNotify({
                title: "Berhasil",
                text: "Permintaan Makan dengan Nomor " + "<strong>" + no_permintaanmakan + "</strong>" + " berhasil ubah",
                addclass: "alert alert-success alert-arrow-right alert-styled-right",
                type: "success",
                buttons: {
                    closer: false,
                    sticker: false
                },
                hide: true,
                // confirm: {
                //     confirm: true,
                //     buttons: [
                //         {
                //             text: "Ya",
                //             addClass: "btn btn-xs btn-success",
                //         },
                //         {
                //             text: "Tidak",
                //             addClass: "btn btn-xs btn-danger",
                //         }
                //     ]
                // },
                history: {
                    history: false
                }
            })).get().on("pnotify.confirm", function() {
                window.open("/gizi/asesmen-gizi/cetak-permintaan-makan?id="+pendaftaran_id+"&no_permintaanmakan="+data.response.no_permintaanmakan);
            }).on("pnotify.cancel", function() {
            });

            // location.reload();
        }

    });
});
', View::POS_END);
