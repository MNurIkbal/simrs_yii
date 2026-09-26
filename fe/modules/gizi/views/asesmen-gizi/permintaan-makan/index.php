<?php
//Author: Ardi Pratama

// Using
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use kartik\widgets\DatePicker;
?>
<style type="text/css">
    .select2-container--default.select2-container--focus{
        box-shadow: none;
        border:1px solid blue;
    }
    .form-control.jumlah_diet:focus{
        border:1px solid blue;
    }
    .form-control.keterangan:focus{
        border:1px solid blue;
    }
</style>
<div class="row body">
	<div class="col-md-12">
		<div>
			<div class="panel panel-white">
				<div class="panel-toolbar clearfix">
					<?=DocoHelpers::generateToolbar([
						
                        'custom-save' => [
                            'type' => 'button',
                            'title' => Yii::t('fe', 'Simpan'),
                            'icon' => 'fa fa-floppy-o',
                            'attributes' => [
                                'data-options' => 'click',
                                'id' => 'btn-simpan-minta-makan'
                            ],
                        ],
					]);?>
				</div>
				<div class="panel-header"></div>
				<div class="panel-body">
	                <div class="panel panel-default">
	                    <div class="panel-heading">
	                        <h6 class="panel-title">Daftar Permintaan Makan</h6>
	                    </div>
	                    <div class="panel-body">

	                        <?php 
	                            $form = ActiveForm::begin([
	                                'id' => 'minta-makan-form',
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
	                            <div class="col-sm-12">
	                                <table id="tbl-minta-makan" class="table table-condensed">
	                                    <thead>
	                                        <tr class="bg-inverse">
	                                            <th width="25%" class="text-center required"><?=\Yii::t("fe", "Jenis Diet");?></th>
	                                            <th width="20%" class="text-center required"><?=\Yii::t("fe", "Menu");?></th>
	                                            <th class="text-center required"><?=\Yii::t("fe", "Waktu Diet");?></th>
	                                            <th width="15%" class="text-center required"><?=\Yii::t("fe", "Jumlah");?></th>
	                                            <th class="text-center"><?=\Yii::t("fe", "Keterangan");?></th>
	                                            <th class="text-center"><?=\Yii::t("fe", "Aksi");?></th>
	                                        </tr>
	                                    </thead>
	                                    <tbody class="container">
	                                        <tr id="tr-default" data-row="0" data-last="0">
	                                           <td colspan="5" class="text-center">
	                                           </td>
	                                           <td class="text-right">
	                                                <button type="button" class="mintamakan-addrow btn btn-info btn-custom">
	                                                    <span class="fa fa-plus"></span>
	                                                </button>
	                                           </td> 
	                                        </tr>
	                                    </tbody>
	                                </table>
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


<?php 
$this->registerJs('

var _listIncremnt = {};
var is_valid = false;
var dataWaktuDiet = '.$dropdownWaktuDiet.';

$(document).ready(function() {
    
    //Menghapus icon x pada kolom pilih jenis diet view-permintaan-makan 
    
    //

	$(".mintamakan-addrow").click(function(){
        console.log("");
        
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
                $(this).parent().parent().closest("div").addClass("has-error");
                _field.after("<span class=\'help-block error\'>"+ logo_err + info_field + "</span>");
            }
            if($(this).data("fieldjenis") == "jumlah_diet" && $(this).val() == 0 && $(this).val() != ""){
                cek.push("");
                $(this).parent().parent().closest("div").addClass("has-error");
                _field.after("<span class=\'help-block error\'>"+ logo_err + "Jumlah Harus Lebih dari 0</span>");
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
        
        parent.attr("data-last", _inc);
        // parent.hide();
        var jenis_diet = "<div class=\'form-group\'><div class=\'col-sm-12\'><select name=\'PermintaanMakanForm["+_inc+"][jenis_diet]\' data-fieldjenis=\'jenis_diet\' class=\'form-control input-sm cek-minta-makan selectJenisDiet\' id=\'jenis_diet_"+_inc+"\'></select></div></div>";

        var menu_diet = "<div class=\'form-group\'><div class=\'col-sm-12\'><select name=\'PermintaanMakanForm["+_inc+"][menu_diet]\' data-fieldjenis=\'menu_diet\' class=\'form-control cek-minta-makan input-sm menu_diet selectMenuDiet\' id=\'menu_diet_"+_inc+"\'></select></div></div>";

        var waktu_diet = "<div class=\'form-group\'><div class=\'col-sm-12\'><select name=\'PermintaanMakanForm["+_inc+"][waktu_diet]\' data-fieldjenis=\'waktu_diet\' class=\'form-control cek-minta-makan input-sm selectWaktuDiet\' id=\'waktu_diet_"+_inc+"\'></select></div></div>";

        var jumlah_diet = "<div class=\'form-group\'><div class=\'col-sm-9\'><input type=\'text\' name=\'PermintaanMakanForm["+_inc+"][jumlah_diet]\' data-fieldjenis=\'jumlah_diet\' maxlength=5 class=\'form-control cek-minta-makan jumlah_diet input-sm doco-number text-right\' id=\'jumlah_diet_"+_inc+"\'></div></div>";

        var keterangan = "<div class=\'form-group\'><div class=\'col-sm-9\'><textarea name=\'PermintaanMakanForm["+_inc+"][keterangan]\' rows=2 cols=15 maxlength=80 style=\'resize:none;\' class=\'form-control keterangan input-sm \' id=\'keterangan_"+_inc+"\'></textarea></div></div>";

        var button_delete = "<button type=\'button\' class=\'mintamakan-deleteRow btn btn-danger btn-custom\'><span class=\'fa fa-trash\'></span></button>";

        var data = "<tr data-row=\'"+ _inc +"\' class=\'child\'><td class=\'jenis_diet\'>"+jenis_diet+"</td><td class=\'menu_diet\'>"+menu_diet+"</td>" + "<td class=\'waktu_diet\'>"+waktu_diet+"</td><td class=\'jumlah_diet\'>"+jumlah_diet+"</td><td>"+keterangan+"</td><td class=\'text-right\'>"+button_delete+"</td></tr>";
     
        $("#tr-default").before(data);
        $(".selectWaktuDiet").select2({
            data:dataWaktuDiet
        });
        $(".selectMenuDiet").select2();

        $(\'#jenis_diet_\'+_inc).select2({
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

$("#btn-simpan-minta-makan").on("click", function(){

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
    
    $(this).docoForm("click", {
        url : baseUrl+"gizi/asesmen-gizi/simpan-minta-makan?id="+pendaftaran_id,
        method : "POST",
        type : "json",
        data: tbl_data,
        success: function(data){
            var no_permintaanmakan = data.response.no_permintaanmakan;
            $(".selectJenisDiet").val("").trigger("change");
            $(".selectMenuDiet").val("").trigger("change");
            $(".selectWaktuDiet").val("").trigger("change");
	        $("#minta-makan-form").trigger("reset");
	        $("#tbl-minta-makan > tbody > tr").not("#tr-default").each(function(){
	        	$(this).remove();
	        });

            (new PNotify({
                title: "Berhasil",
                text: "Permintaan Makan dengan Nomor " + "<strong>" + no_permintaanmakan + "</strong>" + " berhasil disimpan, apakah Anda ingin melakukan cetak?",
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
                window.open("/gizi/asesmen-gizi/cetak-permintaan-makan?id="+pendaftaran_id+"&no_permintaanmakan="+data.response.no_permintaanmakan);
            }).on("pnotify.cancel", function() {

            });
        }
    });
});
', View::POS_END);