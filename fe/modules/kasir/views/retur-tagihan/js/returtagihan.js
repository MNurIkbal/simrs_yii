/*
* @Author: Rizqi Fitrianto
* @Date:   2018-01-22 13:46:27
* @Last Modified by:   Tri Anggoro M
* @Last Modified time: 2019-02-06
* @Branch : feature/kasir-retur-tagihan-pasien
*/

var transaksi = {};
var transaksi_id = false;
$(document).ready(function(){
    $(".panel-informasi").hide();
    $(".detail-form").hide();
    $("#btn-save").prop("disabled", true);

    docoHelper.is_pembulatankeatas = pembulatan_keatas;
    docoHelper.satuanpembulatan = satuan_pembulatan;

    $(".tb-no-rek").attr("readonly", true);
    $(".tb-nama-rek").attr("readonly", true);

    $(".search-kwitansi").select2({
        placeholder: "Cari No. Kwitansi",
        minimumInputLength: 3,
        ajax : {
            url: baseUrl+"kasir/retur-tagihan/search-kwitansi",
            dataType: "json",
            quietMillis: 250,
            data: function (params) {
              var query = {
                search: params,
              }
              return params;
            },
            processResults: function (data) {
                transaksi = data.transaksi;
                return{
                    results: data.result
                }
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        },
    });

    // infinity scroll Select2 with helper docoHealth.js
    // config = {} : untuk melakukan custom config pada js untuk kebutuhan data di select2 / modifikasi response ajax
    // $(".search-kwitansi").docoPaginationSelec2(
    //     // dapat disesuaikan dengan kebutuhan data / customize
    //     config = {
    //         placeholder : '-- Cari No. Kwitansi --',      // custom placeholder (optional) default null
    //         minimumInputLength: 3,
    //         _api : '/kasir/retur-tagihan/search-kwitansi',   // get data
    //         ajax: {
    //             url: _api,
    //             dataType: "json",
    //             quietMillis: 250,
    //             data: function(params) {
    //                 var query = {
    //                     search: params,
    //                   }
    //                 return {
    //                     q:params.term,
    //                     page:params.page || 1
    //                 };
    //             },
    //             processResults: function (data, params) {
    //                 transaksi = data.transaksi;
    //                 params.page = params.page || 1;
    //                 return {
    //                 results: data.result,
    //                     pagination: {
    //                         more: data.pagination.more
    //                                 }
    //                 }
    //             },
    //             dropdownCssClass: "bigdrop",
    //             escapeMarkup: function (markup) {
    //                 return markup;
    //             },
    //             templateResult: function(object) {
    //                 return object.text;
    //             },
    //             templateSelection: function (subject) {
    //                 return subject.text;
    //             },
    //         },
    //     }
    // )
});

$(document).on('change', '.search-kwitansi', function(event) {
    event.preventDefault();
    var id = $(this).val();
    var current_data = transaksi[id];
    $("#total-retur").val("");
    $(".biaya-admin").val(0);
    $("#pembulatan").val(0);
    $("#uang-diserahkan").val(0);

    $.each(current_data, function(i, el){
        var prefix = "#transaksi_";
        var prefix_1 = "#head_transaksi_";
        var selector = prefix+i;
        var selector_1 = prefix_1+i;
        // console.log(i, el)
        var _val = el  || ' - ';
        // console.log(_val)
        if (selector == '#transaksi_jmlpembayaran') {
            $(selector).val(docoHelper.convertToRupiah(_val));
        }else if (selector == '#transaksi_tglbuktibayar') {
            $(selector).val(_val);
        }else if (selector == '#transaksi_tandabuktibayar_id') {
            $(selector).val(_val);
        }else{
            $(selector).text(_val)
        }
        $(selector_1).text(_val)
    });
    $('#no_pendaftaran').val($('#transaksi_no_pendaftaran').text())
    $('.panel-informasi').show();
    $('.detail-form').show();
    $("#btn-save").prop("disabled", false);
    $('#tunai').val(0);
    $('#nontunai').val(0);
    $('#keterangan').val('');
});

$(document).on("change", "#tunai", function(e){
    e.preventDefault();
    var tunai = docoHelper.convertToAngka($(this).val())
    var nontunai = docoHelper.convertToAngka($("#nontunai").val())
    var total_retur = docoHelper.convertToAngka(tunai+nontunai)
    var jml_pembayaran = docoHelper.convertToAngka($("#transaksi_jmlpembayaran").val())
    if (total_retur <= 0) {
        docoNotification("error", "Data Tidak Sesuai!", "Total Retur Harus Lebih Dari 0");
        $("#total-retur").val(0)
    }else{
        $("#total-retur").val(docoHelper.convertToRupiah(total_retur))
    }

    if (total_retur > jml_pembayaran) {
        docoNotification("error", "Data Tidak Sesuai!", "Total Retur Tidak Boleh Lebih dari Jumlah Pembayaran");
        $(this).val(0)
        $("#total-retur").val(docoHelper.convertToRupiah(nontunai))
    }
})

$(document).on("change", "#nontunai", function(e){
    e.preventDefault();
    var nontunai = docoHelper.convertToAngka($(this).val())
    var tunai = docoHelper.convertToAngka($("#tunai").val())
    var total_retur = docoHelper.convertToAngka(tunai+nontunai)
    var jml_pembayaran = docoHelper.convertToAngka($("#transaksi_jmlpembayaran").val())
    if (total_retur <= 0) {
        docoNotification("error", "Data Tidak Sesuai!", "Total Retur Harus Lebih Dari 0");
        $("#total-retur").val(0)
    }else{
        $("#total-retur").val(docoHelper.convertToRupiah(total_retur))
    }

    if (total_retur > jml_pembayaran) {
        docoNotification("error", "Data Tidak Sesuai!", "Total Retur Tidak Boleh Lebih dari Jumlah Pembayaran");
        $(this).val(0)
        $("#total-retur").val(docoHelper.convertToRupiah(tunai))
    }
})

$(document).on("click", "#btn-save", function(e)
{
    e.preventDefault();
    var urlPost = $("#returtagihan-form").attr("action");
    var dataPost = $("#returtagihan-form").serializeArray();

    $().docoForm("click",{
        url : urlPost,
        data : dataPost,
        success : function (data){
            transaksi_id = data.response.data.satuid;
            var no_transaksi = data.response.data.no_transaksi;
            enableBtnPrint(transaksi_id);
            (new PNotify({
                title: "Proses Berhasil !",
                text: "Transaksi Retur Tagihan <strong>"+no_transaksi+"</strong> berhasil disimpan",
                addclass: "alert alert-success alert-arrow-right alert-styled-right",
                type: "success",
                buttons: {
                    closer: true,
                    sticker: false
                },
                hide: false
            }));

            $('#btn-save').attr('disabled', true);
            $(".print-bkk-kw").attr('disabled', false);
            // $('#btn-new').trigger('click');
            // $('#keterangan').val('');
        }
    })
});

function enableBtnPrint(transaksi_id) {
    var btnKw = $("#print-kwitansi");
    var btnBkk = $("#print-bkk");
    var prev_kw = btnKw.attr('data-target');
    var prev_bkk = btnBkk.attr('data-target');

    btnKw.attr('data-target', "/kasir/retur-tagihan/print-kwitansi?id="+transaksi_id);
    btnBkk.attr('data-target', "/kasir/retur-tagihan/print-bkk?id="+transaksi_id);
}

$(document).on('click', '#btn-new', function(e){

    $(".search-kwitansi").val("").change();

    $.each($("#returtagihan-form input"), function(i, el){
        $(this).val("");
    });

    $.each($("#returtagihan-form span.clearable"), function(i, el){
        $(this).text("-");
    });

    $('#keterangan').val('');

    $(".panel-informasi").hide();
    $(".detail-form").hide();

});

$(document).on('change', "#transaksi_jmlpembayaran", function(){
    var val = $(this).val();
    $(this).val(docoHelper.convertToRupiah(val));
});

// $(document).on("keyup", "#total-retur", function(e){
//     var pembayaran = docoHelper.convertToAngka($("#transaksi_jmlpembayaran").val());
//     var val = docoHelper.convertToAngka($(this).val());

//     if (val > pembayaran) {
//         $(this).val(docoHelper.convertToRupiah(pembayaran));
//     }else{
//         $(this).val(docoHelper.convertToRupiah(val));
//     }
// });

// $(document).on("keyup", ".itung-pembulatan", function(){
//     var biaya_admin = docoHelper.convertToAngka($(".biaya-admin").val());
//     var biaya_retur =  docoHelper.convertToAngka($("#total-retur").val());
//     var s_pembulatan = "#pembulatan";
//     var s_diserahkan = "#uang-diserahkan";


//     var bulatkan = biaya_retur - biaya_admin;
//     if (bulatkan >= 0) {
//         docoHelper.pembulatan(bulatkan, $(s_pembulatan), $(s_diserahkan));
//     }
// });

// $(document).on('keyup', '.biaya-admin', function(){
//     var biaya_retur =  docoHelper.convertToAngka($("#total-retur").val());
//     var biaya_admin = docoHelper.convertToAngka($(this).val());

//     if (biaya_admin > biaya_retur) {
//         $(this).val(docoHelper.convertToRupiah(biaya_retur));
//     }else{
//         $(this).val(docoHelper.convertToRupiah(biaya_admin));
//     }
// });

$(document).on("click", ".print-bkk-kw", function(){
    var print_url = $(this).attr("data-target");
    window.open(print_url);
});

$(document).on("change", "#cb_ecollection", function(){
    if($(this).is(":checked") == true){
        console.log("true");
        $('.tb-ecollection').removeAttr('readonly')
    }
    if($(this).is(":checked") == false){
        console.log("false");
        $('.tb-ecollection').attr('readonly', true)
        $('.tb-ecollection').val("");
    }
});

$("#refresh-nobuktibayar").on("click", function(){
    $("#btn-save").prop("disabled", true);
    $("#print-kwitansi").prop("disabled", true);
    $("#print-bkk").prop("disabled", true);
    $(".panel-informasi").hide();
    $(".detail-form").hide();
    $('#btn-new').trigger('click');
});