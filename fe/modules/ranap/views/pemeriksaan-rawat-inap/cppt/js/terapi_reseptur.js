/*
* @Author: Sigit
* @Date:   2018-07-12 11:13:42
* @Last Modified by:   Sigit
* @Last Modified time: 2019-03-20 14:00:56
*/

// Assign tabel reseptur
window.iteration = 0;
window.list_obat = [];
var racikan_append = 'depdrop_reseptur_r';
var racikan_append_satuan = 'racikan_append_satuan_';
var racikan_append_satuan_id = 'satuan_id_reseptur_r';
// var racikan_append_harga = 'racikan_append_harga_';
var racikan_append_harga = 'harga_reseptur_r';
var racikan_append_stok_tersedia = 'stok_sisa_r_';
var racikan_append_depdrop_reseptur_r = 'depdrop_reseptur_r_';
var racikan_append_penyimpanan_r_ = 'penyimpanan_r_';
var racikan_append_nilai_konversi_r_ = 'nilai_konversi_r_';
var racikan_append_satuan_default_r_ = 'satuan_default_r_';
var racikan_append_jml_konversi_r_ = 'jml_konversi_r_';
var racikan_append_konversi_r_ = 'konversi_r_';
var racikan_append_tmp_hargasatuan_reseptur_ = 'tmp_hargasatuan_reseptur_';
var racikan_append_val_qty_r_ = 'val_qty_r_';
var racikan_append_lb_stok_r_ = 'lb_stok_r_';
var racikan_append_tmp_stok_sisa_r_ = 'tmp_stok_sisa_r_';
var racikan_append_qty = 'qty_reseptur_';
var racikan_append_satuankecil_r_ = 'satuankecil_r_';
var iteration_id = (typeof $(this).attr("data-iteration") != 'undefined') ? $(this).attr("data-iteration") : '0';
var _group = {};
var ruangan_id = $('#select_depo').val();
var apotek =  { list_stok: {} };
var dataobat = []

// Select2 initialize
$(".newselect2").select2();

if (isEditReseptur == "1") {
    if (initObatAlkes.length > 0) {
        $("#depdrop_reseptur_nr").prop("disabled", false);
        $("#resepturdetailform-obatalkes_id-0").prop("disabled", false);
        $("#depdrop_reseptur_nr option").remove();
        $("#resepturdetailform-obatalkes_id-0 option").remove();

        $("#depdrop_reseptur_nr").append($("<option></option>")
        .attr("value", "")
        .attr("data-hargajual", 0)
        .attr("data-satuankecil_nama", "")
        .attr("data-satuankecil_id", "")
        .attr("data-qty_tersedia", "")
        .prop("disabled", false)
        .text("-- Pilih Nama Obat --"));

        $("#resepturdetailform-obatalkes_id-0").append($("<option></option>")
        .attr("value", "")
        .attr("data-hargajual", 0)
        .attr("data-satuankecil_nama", "")
        .attr("data-satuankecil_id", "")
        .attr("data-qty_tersedia", "")
        .prop("disabled", false)
        .text("-- Pilih Nama Obat --"));

        $.each(initObatAlkes, function(key, value) {
            $("#depdrop_reseptur_nr").append($("<option></option>")
            .attr("value", value.obatalkes_id)
            .attr("data-hargajual", value.hargaygdipakai)
            .attr("data-satuankecil_nama", value.satuankecil_nama)
            .attr("data-satuankecil_id", value.satuankecil_id)
            .attr("data-qty_tersedia", value.qty_tersedia)
            // .text(value.obatalkes_namalain + " - " + value.qty_tersedia));
            .text(value.obatalkes_namalain));

            $("#resepturdetailform-obatalkes_id-0").append($("<option></option>")
            .attr("value", value.obatalkes_id)
            .attr("data-hargajual", value.hargaygdipakai)
            .attr("data-satuankecil_nama", value.satuankecil_nama)
            .attr("data-satuankecil_id", value.satuankecil_id)
            .attr("data-qty_tersedia", value.qty_tersedia)
            // .text(value.obatalkes_namalain + " - " + value.qty_tersedia));
            .text(value.obatalkes_namalain));
        });

        $("#depdrop_reseptur_nr").select2();
        $("#depdrop_reseptur_r").select2();
        $("#depdrop_satuan_detail").select2();
        $("#resepturdetailform-obatalkes_id-0").select2();
    }
}

// mencegah karakter lain selain angka desimal
$(document).on('input', '.doco-decimal', function() {
    match = (/(\d{0,9})[^.]*((?:\.\d{0,2})?)/g).exec(this.value.replace(/[^\d.]/g, ''));
    this.value = match[1] + match[2];
});

$(document).on("change", "#select_depo", function(){
    $(".val_qty").html("-");
    $("#qty_nonracikan_id").val("");
    $(".konversi").html("-");
});

// NON RACIKAN

// Depdrop reseptur
$(document).on("change.select2", "#depdrop_reseptur_nr", function (event) {
    let selected = $(this).val();
    if (typeof apotek.list_stok[selected] !== 'undefined' ){
        apotek.list_stok[selected]
        const objectselected = apotek.list_stok[selected].options
        let harga = objectselected.hargajual
        let satuankecil = objectselected.satuankecil_nama
        let satuankecil_id = objectselected.satuankecil_id
        let obatalkes_id = selected;
        // let qty_tersedia = parseInt(selected.text().split(" - ").pop());
        let qty_tersedia = objectselected.qty_tersedia
        let qty_dihapus = 0;
        let temp_qty_tersedia = 0;
        let flag = 0;

        if(isNaN(harga)) {
            harga = 0;
        }

        $("#tmp_hargasatuan").val(harga);
        $("#satuan_id_reseptur_nr").val(satuankecil_id);

        $(".stok_tersedia_"+obatalkes_id).each(function(index, object) {
            temp_qty_tersedia = parseFloat($(object).text());
            flag = flag + 1;
        });

        if (flag > 0) {
            $("#tmp_stok_sisa_nr").val(!isNaN(temp_qty_tersedia) ? temp_qty_tersedia : 0);
        } else {
            $("#tmp_stok_sisa_nr").val(!isNaN(qty_tersedia) ? qty_tersedia : 0);
        }
    }
    

    // harga = docoHelper.numberFormat(harga, 2, ",", ".");

    // $("#qty_nonracikan_id").val("");
    // $(".konversi").html("-");
    // $(".val_qty").html("-");
    // $(".lb_stok").html("");
    // $("#penyimpanan").html("");
    // $('#satuankecil_nama').val(null).trigger('change');

    // $("#satuan_reseptur_nr").val(satuankecil);
    // $("#satuan_default").val(satuankecil);
    // $("#harga_reseptur_nr").val(harga);
    
        
});

$(document).on("change", "#satuankecil_nama", function(){
    let _val = $(this).val();
    let obatalkes_id = $('#depdrop_reseptur_nr').val();
    
    $("#qty_nonracikan_id").val("");

    $.ajax({
        url: '/ranap/pemeriksaan-rawat-inap/get-nilai-konversi?obatalkes_id=' + obatalkes_id + '&satuanbesar_id=' + _val,
        type: 'GET',
        success: function(data) {
            var satuan_besar = data.satuan_besar;
            var satuan_kecil = data.satuan_kecil;
            var satuan_besar_id = data.satuanbesar_id;
            var nilai_konversi = data.nilai_konversi;
            var tmp_stok_nr = $("#tmp_stok_sisa_nr").val();
            var harga_awal = $("#tmp_hargasatuan").val();
            var harga_konversi = harga_awal * nilai_konversi;
            var stok_konversi = tmp_stok_nr / nilai_konversi;
            if(isNaN(harga_konversi)) {
                harga_konversi = 0;
            }
            if(isNaN(stok_konversi)) {
                stok_konversi = 0;
            }
            harga_konversi = docoHelper.numberFormat(harga_konversi, 2, ",", ".");
            $("#satuan_default").val(satuan_besar);
            $("#satuankecil").val(satuan_kecil);
            $("#harga_reseptur_nr").val(harga_konversi);
            $("#nilai_konversi").val(nilai_konversi);
            $("#satuanbesar_id_reseptur_nr").val(satuan_besar_id);
            $("#stok_sisa_nr").val(stok_konversi.toFixed(2));
            $(".val_qty").html(stok_konversi.toFixed(2));
            $(".lb_stok").html(satuan_besar);
            $("#penyimpanan").html(satuan_besar);
            $(".konversi").html("-");
        }
    })
});

$(document).on("input", "#qty_nonracikan_id", function(){
    var _qty = docoHelper.convertToAngka($(this).val());
    var nilai_konversi = $("#nilai_konversi").val();
    var konversi = _qty * nilai_konversi;
    var satuan = $('#satuan_default').val();
    var satuankecil = $('#satuankecil').val();

    $(".konversi").html(konversi.toFixed(2) + ' ' + satuankecil);
    $(".nama_satuan").html(satuan);
    $("#satuan_penyimpanan").val(satuankecil);
    $("#jml_konversi").val(konversi.toFixed(2));
    $("#qty_konversi").val(konversi.toFixed(2));
});


// RACIKAN

// Delete data append
$(document).on("click", ".btn-deletes", function(e) {
    $('#section-racikan').find('.child-'+$(this).attr('data-iteration')).remove();
});

// Depdrop reseptur
$(document).on("change", "#depdrop_reseptur_r", function (event) {
    let selected = $(this).val();
    if (typeof apotek.list_stok[selected] !== 'undefined' ){
        const objectselected = apotek.list_stok[selected].options
        let harga = objectselected.hargajual
        let satuankecil = objectselected.satuankecil_nama
        let satuankecil_id = objectselected.satuankecil_id
        let obatalkes_id = selected;
        let qty_tersedia = objectselected.qty_tersedia
        let qty_dihapus = 0;
        let temp_qty_tersedia = 0;
        let flag = 0;

        if(isNaN(harga)) {
            harga = 0;
        }
        $("#tmp_hargasatuan_r").val(harga);
        $("#satuan_id_reseptur_r").val(satuankecil_id);

        $(".stok_tersedia_"+obatalkes_id).each(function(index, object) {
            temp_qty_tersedia = parseFloat($(object).text());
            flag = flag + 1;
        });

        if (flag > 0) {
            $("#tmp_stok_sisa_r").val(!isNaN(temp_qty_tersedia) ? temp_qty_tersedia : 0);
        } else {
            $("#tmp_stok_sisa_r").val(!isNaN(qty_tersedia) ? qty_tersedia : 0);
        }
    }
    
    // let qty_tersedia = parseInt(selected.text().split(" - ").pop());
    

    // let selected = $(this).find(":selected");
    // let harga = selected.data("hargajual");
    // let satuankecil = selected.data("satuankecil_nama");
    // let satuankecil_id = selected.data("satuankecil_id");
    // let obatalkes_id = selected.val();
    // // let qty_tersedia = parseInt(selected.text().split(" - ").pop());
    // let qty_tersedia = selected.attr("qty_tersedia");
    

    // harga = docoHelper.numberFormat(harga, 2, ",", ".");

    // $("#qty_racikan_id").val("");
    // $(".konversi_r").html("-");

    // $("#satuan_reseptur_r").val(satuankecil);
    // $("#satuan_default").val(satuankecil);
    // $("#harga_reseptur_nr").val(harga);
    
        
});

function setObatSatuan(value, index){
    let selected = value
    const objectselected = apotek.list_stok[selected].options
    let harga = objectselected.hargajual
    let satuankecil = objectselected.satuankecil_nama
    let satuankecil_id = objectselected.satuankecil_id
    let obatalkes_id = selected;
    let qty_tersedia = objectselected.qty_tersedia
    let qty_dihapus = 0;
    let temp_qty_tersedia = 0;
    let flag = 0;

    if(isNaN(harga)) {
        harga = 0;
    }

    // harga = docoHelper.numberFormat(harga, 2, ",", ".");

    // $("#qty_racikan_id").val("");
    // $(".konversi_r").html("-");

    // $("#satuan_reseptur_r").val(satuankecil);
    // $("#satuan_default").val(satuankecil);
    // $("#harga_reseptur_nr").val(harga);
    $(`#tmp_hargasatuan_r${index}`).val(harga);
    // $(`#satuan_id_reseptur_r${index}`).val(satuankecil_id);

    $(".stok_tersedia_"+obatalkes_id).each(function(index, object) {
        temp_qty_tersedia = parseFloat($(object).text());
        flag = flag + 1;
    });

    if (flag > 0) {
        $(`#tmp_stok_sisa_r${index}`).val(!isNaN(temp_qty_tersedia) ? temp_qty_tersedia : 0);
    } else {
        $(`#tmp_stok_sisa_r${index}`).val(!isNaN(qty_tersedia) ? qty_tersedia : 0);
    }
}

$(document).on("change", "#satuankecil_nama_r", function(){
    let _val = $(this).val();
    let obatalkes_id = $('#depdrop_reseptur_r').val();
    
    $("#qty_racikan_id").val("");

    $.ajax({
        url: '/ranap/pemeriksaan-rawat-inap/get-nilai-konversi?obatalkes_id=' + obatalkes_id + '&satuanbesar_id=' + _val,
        type: 'GET',
        success: function(data) {
            var satuan_besar = data.satuan_besar;
            var satuan_kecil = data.satuan_kecil;
            var satuan_besar_id = data.satuanbesar_id;
            var nilai_konversi = data.nilai_konversi;
            var tmp_stok_nr = $("#tmp_stok_sisa_r").val();
            var harga_awal = $("#tmp_hargasatuan_r").val();
            var harga_konversi = harga_awal * nilai_konversi;
            var stok_konversi = tmp_stok_nr / nilai_konversi;
            if(isNaN(harga_konversi)) {
                harga_konversi = 0;
            }
            if(isNaN(stok_konversi)) {
                stok_konversi = 0;
            }
            harga_konversi = docoHelper.numberFormat(harga_konversi, 2, ",", ".");
            $("#satuan_default_r").val(satuan_besar);
            $("#satuankecil_r").val(satuan_kecil);
            $("#harga_reseptur_r").val(harga_konversi);
            $("#nilai_konversi_r").val(nilai_konversi);
            $("#satuanbesar_id_reseptur_r").val(satuan_besar_id);
            $("#stok_sisa_r").val(stok_konversi.toFixed(2));
            $(".val_qty_r").html(stok_konversi.toFixed(2));
            $(".lb_stok_r").html(satuan_besar);
            $("#penyimpanan_r").html(satuan_besar);
            $(".konversi_r").html("-");
        }
    })
});

$(document).on("input", "#qty_racikan_id", function(){
    var _qty = docoHelper.convertToAngka($(this).val());
    var nilai_konversi = $("#nilai_konversi_r").val();
    var konversi = _qty * nilai_konversi;
    var satuan = $('#satuan_default_r').val();
    var satuankecil = $('#satuankecil_r').val();

    $(".konversi_r").html(konversi.toFixed(2) + ' ' + satuankecil);
    $(".nama_satuan_r").html(satuan);
    $("#satuan_penyimpanan_r").val(satuankecil);
    $("#jml_konversi_r").val(konversi.toFixed(2));
    $("#qty_konversi_r").val(konversi.toFixed(2));
});

// Reset reseptur
$(document).on("click", "#btn-reset-reseptur", function() {
    if (isEditReseptur != "1") {
        resetAll(true);
    }

    $("#berat_badan").val(berat_badan);
    $("#tinggi_badan").val(tinggi_badan);
    $("#luas_tubuh").val(luas_tubuh);
    // $("#diagnosa_id").val(diagnosa_id).trigger("change.select2");

    $.ajax({
        url: $(this).attr('data-url'),
        success: function(){
            tabel_reseptur.draw();
        }
    });
});

// BB TB change event handler
$(document).on("change", ".bb_tb", function() {
    // Inisialisasi
    var bb = parseFloat(docoHelper.convertToAngka($("#berat_badan").val()))
    var tb = parseFloat(docoHelper.convertToAngka($("#tinggi_badan").val()))

    // Check bb dan tb
    if (bb != '' && tb != '') {
        // Kalkulasi mosteller
        var mosteller = Math.sqrt(Number(bb) * Number(tb) / 3600);

        // Set mosteller
        $("#luas_tubuh").val(docoHelper.convertToRupiah(mosteller.toFixed(2)));
    }
});

$(document).on("change", ".qty", function() {
    if ($(this).val() == '') {
        $(this).val(0);
    }
});

$(document).on("change", "#qty_nonracikan_id", function(event) {
    event.preventDefault();

    let stok_tersedia = parseFloat($("#stok_sisa_nr").val());
    let qty = docoHelper.convertToAngka($(this).val());

    if (qty > stok_tersedia) {
        docoNotification("error", "Tidak bisa ubah qty!", "Qty tidak boleh lebih dari stok tersedia!");

        $(this).val(0);
        $("#jml_konversi").val(0);
        $(".konversi").html("0");
    }
});

$(document).on("change", "#qty_racikan_id", function(event) {
    event.preventDefault();

    let stok_tersedia = parseFloat($("#stok_sisa_r").val());
    let qty = docoHelper.convertToAngka($(this).val());

    if (qty > stok_tersedia) {
        docoNotification("error", "Tidak bisa ubah qty!", "Qty tidak boleh lebih dari stok tersedia!");

        $(this).val(0);
        $("#jml_konversi_r").val(0);
        $(".konversi_r").html("0");
    }
});

$(document).on("change", ".qty-reseptur", function(event) {
    event.preventDefault();

    let stok_tersedia = 0;
    let qty = docoHelper.convertToAngka($(this).val());
    let attr = $(this).attr("id");
    let count = attr.split("-");
    count = count[1];
    stok_tersedia = parseFloat($("#"+racikan_append_stok_tersedia+count).val());

    if (qty > stok_tersedia) {
        docoNotification("error", "Tidak bisa ubah qty!", "Qty tidak boleh lebih dari stok tersedia!");

        $(this).val(0);
    }
});

// Cetak reseptur
$("#btn-cetak-reseptur").on("click", function(event) {
    event.preventDefault();

    window.open("/ranap/pemeriksaan-rawat-inap/cetak-reseptur?id="+pendaftaran_id+"&instruksi_id="+$("#instruksiform-instruksi_id").val());
});

// Get data from depdrop
$("#depdrop_reseptur_nr").on("depdrop:afterChange", function(event, id, value, jqXHR, textStatus) {
    let ajaxResults = $('#depdrop_reseptur_nr').depdrop('getAjaxResults');
    list_obat = ajaxResults['result'];
});

// Append racikan
function appendRacikan() {
    // Inisiasi cek
    var cek = [];

    // Cek inputan
    $(".cek-racikan").each(function() {
        // Cek value
        if ($(this).val() != null) {
            // Set cek
            cek.push($(this).val());

            // Cek value
            if ($(this).val() == "") {
                // Add class
                $(this).parent().parent().closest("div").addClass("has-error");
            }
            else {
                // Remove class
                $(this).parent().parent().closest("div").removeClass("has-error");
            }
        }
        else {
            // Set cek
            cek.push("");

            // Cek value
            if ($(this).val() == "" || $(this).val() == null) {
                // Add class
                $(this).parent().parent().closest("div").addClass("has-error");
            }
            else {
                // Remove class
                $(this).parent().parent().closest("div").removeClass("has-error");
            }
        }
    });

    // Cek
    if (jQuery.inArray("", cek) !== -1) {
        // Notification
        docoNotification('error', "Tidak bisa tambah racikan", "Ada field yang belum diisi");
    }
    else {
        // Append resepturdetailform-
        iteration++;
        let template = 
            '<div id="childParent-'+iteration+'" class="child child-'+iteration+'"><hr>'+
                '<div class="row">'+
                    '<div class="col-md-4">'+
                        '<div class="form-group field-resepturdetailform-obatalkes_id_ required">'+
                            '<label class="text-right control-label col-sm-5" for="resepturdetailform-obatalkes_id">Nama Obat</label>'+
                            '<div class="col-sm-7">'+
                                '<select id="obatalkes_id_'+ iteration +'" data-iteration="'+ iteration +'" class="select2 form-control racikan_append '+ racikan_append + iteration +' depdrop_resept_js'+ iteration +' cek-racikan obatreseptur" name="ResepturDetailForm[obatalkes_id]['+iteration+']"></select>'+
                                '<div class="help-block"></div>'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                    '<div class="col-md-4">'+
                        '<div class="form-group field-resepturdetailform-hargasatuan_reseptur">'+
                            '<label class="text-right control-label col-sm-5" for="resepturdetailform-hargasatuan_reseptur">Harga Per <p id="penyimpanan_js'+iteration+'"></p></label>'+
                            '<div class="col-sm-7">'+
                                '<input type="text" id="harga_reseptur_r'+iteration +'" class="form-control input-sm harga_reseptur_r'+iteration +'" name="ResepturDetailForm[hargasatuan_reseptur]['+iteration+']" readonly="readonly">'+
                                '<div class="help-block"></div>'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
                '<div class="row">'+
                    '<div class="col-md-4">'+
                        '<div class="form-group field-resepturdetailform-satuankecil_nama required">'+
                            '<label class="text-right control-label col-sm-5" for="resepturdetailform-satuankecil_nama">Satuan</label>'+
                            '<div class="col-sm-7">'+
                                '<select id="satuankecil_nama_r'+ iteration +'" data-iteration="'+ iteration +'" class="select2 form-control satuankecil_nama satuankecil_nama_r'+ iteration +' cek-racikan" name="ResepturDetailForm[satuanbesar_id]['+iteration+']"></select>'+
                                '<div class="help-block"></div>'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                    '<input type="hidden" id="satuan_id_reseptur_r'+ iteration +'" name="ResepturDetailForm[satuankecil_id]['+iteration+']">'+
                    '<input type="hidden" id="satuanbesar_id_reseptur_r'+ iteration +'" name="ResepturDetailForm[satuanbesar_id]['+iteration+']">'+
                    '<input type="hidden" id="tmp_hargasatuan_r'+ iteration +'" name="ResepturDetailForm[tmp_hargasatuan]['+iteration+']">'+
                    // '<input type="hidden" id="stok_sisa_r'+ iteration +'" name="ResepturDetailForm[stok_sisa]['+iteration+']">'+
                    '<input type="hidden" id="tmp_stok_sisa_r'+ iteration +'" name="ResepturDetailForm[tmp_stok_sisa]['+iteration+']">'+
                    '<input type="hidden" id="stok_konversi_r'+ iteration +'" name="ResepturDetailForm[stok_konversi]['+iteration+']">'+
                    '<input type="hidden" id="nilai_konversi_r'+ iteration +'" name="ResepturDetailForm[nilai_konversi]['+iteration+']">'+
                    '<input type="hidden" id="satuankecil_r'+ iteration +'" name="ResepturDetailForm[satuankecil_nama]['+iteration+']">'+
                    '<input type="hidden" id="satuan_default_r'+ iteration +'" name="ResepturDetailForm[satuankecil]['+iteration+']">'+
                    '<input type="hidden" id="satuan_penyimpanan_r'+ iteration +'" name="ResepturDetailForm[satuan_penyimpanan]['+iteration+']">'+
                    // '<input type="hidden" id="jml_konversi_r'+ iteration +'" name="ResepturDetailForm[qty_reseptur]['+iteration+']">'+
                    '<input type="hidden" id="qty_konversi_r'+ iteration +'" name="ResepturDetailForm[qty_konversi]['+iteration+']">'+
                    '<div class="col-md-4">'+
                        '<div class="form-group field-resepturdetailform-qty_reseptur required">'+
                            '<label class="text-right control-label col-sm-5" for="resepturdetailform-qty_reseptur">QTY</label>'+
                            '<div class="col-sm-7">'+
                                '<input type="text" id="qty_racikan_id'+ iteration +'" class="form-control input-sm qty-reseptur cek-racikan doco-decimal-wcomma" maxlength="8" name="ResepturDetailForm[jml_konversi]['+iteration+']">'+
                                '<div class="help-block"></div>'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
                '<div class="row">'+
                    '<div class="col-md-4">'+
                        '<div class="form-group">'+
                            '<label class="text-right control-label col-sm-5">Stok Tersedia Per <p class="lb_stok_r'+iteration+'"></p></label>'+
                            '<div class="col-sm-7">'+
                                '<input type="text" id="stok_sisa_r'+ iteration +'" class="form-control input-sm stok_sisa_r'+ iteration +'" name="ResepturDetailForm[stok_sisa]['+iteration+']" readonly="readonly">'+
                                '<div class="help-block"></div>'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                    '<div class="col-md-4">'+
                        '<div class="form-group">'+
                            '<label class="text-right control-label col-sm-5">Jumlah Konversi</label>'+
                            '<div class="col-sm-7">'+
                                '<input type="text" id="jml_konversi_r' + iteration +'" class="form-control input-sm" name="ResepturDetailForm[qty_reseptur]['+iteration+']" readonly="readonly">'+
                                '<div class="help-block"></div>'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                    '<div class="col-md-1">'+
                        '<button type="button" class="btn btn-danger btn-sm btn-deletes" data-iteration="'+iteration+'"><i class="fa fa-trash"></i></button>'+
                    '</div>'+
                    // '<input type="hidden" id="'+ racikan_append_satuan_id + iteration +'" class="'+ racikan_append_satuan_id + iteration +'" name="ResepturDetailForm[satuankecil_id]['+iteration+']" readonly="readonly">'+
                '</div>'+
            '</div>';

        $("#section-racikan").append(template);
        // $('.racikan_append').select2(_configSelect).on('change',_changeSelect);
        // $(`#obatalkes_id_${iteration}`).select2({
        //     data : [
        //     {
        //         id : '',
        //         text : '-- Pilih Nama Obat --'
        //     }].concat(dataobat)
        // })
        let depoId = $('#select_depo').val()
        $(`#obatalkes_id_${iteration}`).docoPaginationSelec2(
            config = {
                placeholder : '-- Pilih Nama Obat --',     
                _api : '/ranap/pemeriksaan-rawat-inap/list-obat-alkes-depo?ruangan_id='+depoId+'&kelaspelayananId='+kelaspelayananId+'&penjaminId='+penjaminId,
                ajax: {
                    processResults: function (data, params) {
                        var _results = data.result;
                        params.page = params.page || 1;
                        return {
                        results: data.result,
                            pagination: {
                                more: data.pagination.more
                                        }
                        }
                    },
                    success: (res) => {
                        apotek.list_stok = {}
                        res.result.map( function(items){
                            apotek.list_stok[items.id] = items
                        })
                    }
                }
            }
        );
        $(`#obatalkes_id_${iteration}`).on('change.select2',({ delegateTarget }) => {
            _changeSelect(delegateTarget)
           setObatSatuan($(delegateTarget).val(), iteration)
        });
        // $(".qty-reseptur").on('change', _qtyChange);
        $(document).on('input', `#qty_racikan_id${iteration}`,({ delegateTarget }) => {
           _qtyChange($(`#qty_racikan_id${iteration}`))
        });
        $(document).on('change.select2', `#satuankecil_nama_r${iteration}`,({ delegateTarget }) => {
           _satuanChange($(`#satuankecil_nama_r${iteration}`))
        });

        if (isEditReseptur == "1") {
            if (initObatAlkes.length > 0) {
                $('.'+racikan_append+iteration).append($("<option></option>")
                .attr("value", "")
                .attr("data-hargajual", 0)
                .attr("data-satuankecil_nama", "")
                .attr("data-satuankecil_id", "")
                .attr("data-qty_tersedia", "")
                .prop("disabled", false)
                .text("-- Pilih Nama Obat --"));

                $.each(initObatAlkes, function(key, value) {
                    $('.'+racikan_append+iteration).append($("<option></option>")
                    .attr("value", value.obatalkes_id)
                    .attr("data-hargajual", value.hargaygdipakai)
                    .attr("data-satuankecil_nama", value.satuankecil_nama)
                    .attr("data-satuankecil_id", value.satuankecil_id)
                    .attr("data-qty_tersedia", value.qty_tersedia)
                    // .text(value.obatalkes_namalain + " - " + value.qty_tersedia));
                    .text(value.obatalkes_namalain));
                });

                // $('.'+racikan_append+iteration).select2();
            }
        } else {
            if (list_obat.length !== 0){
                $('.'+racikan_append+iteration).append($("<option></option>")
                .attr("value", "")
                .attr("data-hargajual", 0)
                .attr("data-satuankecil_nama", "")
                .attr("data-satuankecil_id", "")
                .attr("data-qty_tersedia", "")
                .prop("disabled", false)
                .text("-- Pilih Nama Obat --"));

                $.each(list_obat, function(key, value) {
                     $('.'+racikan_append+iteration).append($("<option></option>")
                        .attr("value", value.id)
                        .attr("data-hargajual", value.options.hargajual)
                        .attr("data-satuankecil_nama", value.options.satuankecil_nama)
                        .attr("data-satuankecil_id", value.options.satuankecil_id)
                        .attr("data-qty_tersedia", value.options.qty_tersedia)
                        .prop("disabled", value.options.disabled)
                        .text(value.name));
                });

                // $('.'+racikan_append+iteration).select2();
            }
        }
    }
}

function submitNonracikan() {
    $.ajax({
        url: $('#form-nonracikan').attr('action'),
        method: "POST",
        dataType: "json",
        data: $('#form-nonracikan').serializeArray(),
        success : function(data) {
            resetAll(false);
            tabel_reseptur.clear();
            tabel_reseptur.ajax.url(baseUrl+"ranap/pemeriksaan-rawat-inap/get-data-reseptur-session?id="+pendaftaran_id+"&cppt_id="+cppt_id+"&ruangan_id="+$("#select_depo").val()+"").draw();
        },
        error : function(data, status, error) {
            docoHelper.listen = false;
            $('html, body').animate({scrollTop:0}, 'slow');
            var errMsg   = 'Terjadi kesalahan, silahkan cek inputan.';
            var errTitle = 'Proses Gagal !';                        
            // extend message from backend : Ali
            $('span.help-block.error').remove();
            try {
                var error = data.responseJSON.response;
                var sttsErr = data.status;
                var extMessage =  (error.message) ? ' , '+error.message : '';
                if (sttsErr == '422') {
                    var logo  = '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> &nbsp';
                    if(error.text) {
                        errMsg = error.text;
                    }
                    // Setting Error title
                    if(error.title) {
                        errTitle = error.title;
                    }
                    // Parsing Error;
                    $.each(error.data, function(key, val) {
                        var _field     = $('[name="'+ key +'"]');
                        var _div       = $('.error_' + key);
                        var _getId = _field.attr('id');
                        var _group     = _field.closest('div.input-group');
                        var _selectize = _field.closest('.form-group').find('div.selectize-control');
                        var _select2   = _field.closest('div').find('.select2-container');
                        // Menambahkan class Error pada form-group
                        _field.parent('div').addClass('has-error');
                        _field.parent('.required').addClass('has-error');
                        $('.field-'+_getId).addClass('has-error');
                        // Cara kedua menempelkan manual error pada form
                        var replaceKey = key.replace(/[\[\]\'\!]/g,"");
                        var manualErr = $('#error_' + replaceKey);
                        
                        if(manualErr.length) {
                            manualErr.html('<span class="help-block error">'+ logo + val[0] +'</span>');
                        } else {
                            if(_group.length) {
                                _group.after('<span class="help-block error">'+ logo + val[0] +'</span>');
                            } else if(_selectize.length) {
                                _selectize.after('<span class="help-block error">'+ logo + val[0] +'</span>');
                            } else if (_select2.length) {
                                _select2.after('<span class="help-block error">'+ logo + val[0] +'</span>');
                            } else if(_div.length) {
                                _div.after('<span class="help-block error">'+ logo + val[0] +'</span>');
                            } else {
                                _field.after('<span class="help-block error">'+ logo + val[0] +'</span>');
                            }
                        }
                    });
                } else {
                    errMsg   = 'Terjadi kesalahan pada sistem ' + extMessage;
                    errTitle = 'Error ' + sttsErr + ' !';
                }
            } catch ($e) {
                var errTitle = 'Proses error ' + data.status + ' !';
                var errMsg = error;
            }
            hideQuestionDialog();
            $('body').find('.confirm-dialog-overlay').remove();
            docoNotification('error', errTitle, errMsg);
            $('[data-popup="tooltip"]').tooltip();
        }
    });
}

function submitRacikan() {
    $.ajax({
        url: $('#form-racikan').attr('action'),
        method: "POST",
        dataType: "json",
        data: $('#form-racikan').serializeArray(),
        success : function(data) {
            resetAll(false);

            $(".val_qty_r").html("-");
            $(".lb_stok_r").html("");
            $("#penyimpanan_r").html("");
            $('#r_ke_r').val(null).trigger('change');
            $('#depdrop_reseptur_r').val(null).trigger('change');
            $('#satuankecil_nama_r').val(null).trigger('change');

            tabel_reseptur.clear();
            tabel_reseptur.ajax.url(baseUrl+"ranap/pemeriksaan-rawat-inap/get-data-reseptur-session?id="+pendaftaran_id+"&cppt_id="+cppt_id+"&ruangan_id="+$("#select_depo").val()+"").draw();
        },
        error : function(data, status, error) {
            docoHelper.listen = false;
            $('html, body').animate({scrollTop:0}, 'slow');
            var errMsg   = 'Terjadi kesalahan, silahkan cek inputan.';
            var errTitle = 'Proses Gagal !';                        
            // extend message from backend : Ali
            $('span.help-block.error').remove();
            try {
                var error = data.responseJSON.response;
                var sttsErr = data.status;
                var extMessage =  (error.message) ? ' , '+error.message : '';
                if (sttsErr == '422') {
                    var logo  = '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> &nbsp';
                    if(error.text) {
                        errMsg = error.text;
                    }
                    // Setting Error title
                    if(error.title) {
                        errTitle = error.title;
                    }
                    // Parsing Error;
                    $.each(error.data, function(key, val) {
                        var _field     = $('[name="'+ key +'"]');
                        var _div       = $('.error_' + key);
                        var _getId = _field.attr('id');
                        var _group     = _field.closest('div.input-group');
                        var _selectize = _field.closest('.form-group').find('div.selectize-control');
                        var _select2   = _field.closest('div').find('.select2-container');
                        // Menambahkan class Error pada form-group
                        _field.parent('div').addClass('has-error');
                        _field.parent('.required').addClass('has-error');
                        $('.field-'+_getId).addClass('has-error');
                        // Cara kedua menempelkan manual error pada form
                        var replaceKey = key.replace(/[\[\]\'\!]/g,"");
                        var manualErr = $('#error_' + replaceKey);
                        
                        if(manualErr.length) {
                            manualErr.html('<span class="help-block error">'+ logo + val[0] +'</span>');
                        } else {
                            if(_group.length) {
                                _group.after('<span class="help-block error">'+ logo + val[0] +'</span>');
                            } else if(_selectize.length) {
                                _selectize.after('<span class="help-block error">'+ logo + val[0] +'</span>');
                            } else if (_select2.length) {
                                _select2.after('<span class="help-block error">'+ logo + val[0] +'</span>');
                            } else if(_div.length) {
                                _div.after('<span class="help-block error">'+ logo + val[0] +'</span>');
                            } else {
                                _field.after('<span class="help-block error">'+ logo + val[0] +'</span>');
                            }
                        }
                    });
                } else {
                    errMsg   = 'Terjadi kesalahan pada sistem ' + extMessage;
                    errTitle = 'Error ' + sttsErr + ' !';
                }
            } catch ($e) {
                var errTitle = 'Proses error ' + data.status + ' !';
                var errMsg = error;
            }
            hideQuestionDialog();
            $('body').find('.confirm-dialog-overlay').remove();
            docoNotification('error', errTitle, errMsg);
            $('[data-popup="tooltip"]').tooltip();
        }
    });
}

function saveSessionReseptur() {
    //validasi
    $('.form-group').removeClass('has-error')
    $('.help-block.error').remove()

    var catatan = $('#instruksiform-catatan_instruksi').val();
    if (catatan == null || catatan == "") {
        docoNotification("error", "Catatan Belum Diisi!", "mohon isi dulu catatan!");
        var logo =
                '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> &nbsp'
        var _field_catatan = $('#instruksiform-catatan_instruksi')
                _field_catatan.parent('div').addClass('has-error')
                _field_catatan.parent('.required').addClass('has-error')
                _field_catatan.after(
                    '<span class="help-block error">' +
                        logo +
                        'Catatan Harus Diisi' +
                        '</span>'
                )
        return false
    }

    // Data
    let data = tabel_reseptur.$("input, select");
    let data_depo = $("#select_depo");
    let data_iter = $("#reseptur_iter");
    let data_general = $.merge(data_depo, data_iter);
    let all = $.merge(data, data_general);
    let all_serialize = all.serializeArray();

    // Additional form reseptur and instruksi
    let dataReseptur = $("#form-reseptur").serializeArray();
    let dataInstruksi = $("#form-instruksi").serializeArray();

    // create object for auto cppt
    let addData = [
        {name: 'ruangan', value: ruangan},
        {name: 'pegawai_id', value: pegawaId},
    ]

    // All data
    let allData = $.merge(all_serialize, dataReseptur);
    allData = $.merge(allData, dataInstruksi);
    allData = $.merge(allData, addData);

    // Docoform click
    $("#btn-save-reseptur").docoForm("click", {
        data: allData,
        skipSuccessNotif: true,
        success : function(data) {
            // Set enabled
            $("#temp_instruksi_id").val(data.response.data.instruksi_id);
            var instruksi_id = $("#temp_instruksi_id").val();

            // Reset all
            resetAll(true);

            // Draw tabel
            tabel.draw();

            // Ajax hapus session obat
            ajaxHapusSessionReseptur("");

            // Pnotify
            PNotify.prototype.options.styling = "bootstrap3";
            (new PNotify({
                title: "Berhasil",
                text: "Data berhasil disimpan, apakah Anda ingin melakukan cetak?",
                addclass: "alert alert-success alert-arrow-right alert-styled-right",
                type: "success",
                buttons: {
                    closer: false,
                    sticker: false
                },
                hide: false,
                confirm: {
                    confirm: true
                },
                history: {
                    history: false
                }
            })).get().on('pnotify.confirm', function() {
                // Print
                window.open("/ranap/pemeriksaan-rawat-inap/cetak-reseptur?id="+pendaftaran_id+"&instruksi_id="+instruksi_id);
            }).on('pnotify.cancel', function() {

            });

            $("#btn-back-terapi").trigger('click');
        }
    });
}

function batalSessionReseptur(index) {
    $(index).docoForm("delete", {
        success : function(response) {
            resetAll(false);
            tabel_reseptur.clear();
            tabel_reseptur.ajax.url(baseUrl+"ranap/pemeriksaan-rawat-inap/get-data-reseptur-session?id="+pendaftaran_id+"&cppt_id="+cppt_id+"&ruangan_id="+$("#select_depo").val()+"").draw();
        }
    });
}

// Reset all
function resetAll(reset_all = true) {
    // Reset
    var formRacikan = $('#form-racikan');
    var formNonRacikan = $('#form-nonracikan');
    formRacikan[0].reset();
    formNonRacikan[0].reset();
    $('#resepturdetailform-obatalkes_id-0').val('').trigger('change');
    $('#depdrop_reseptur_nr').val('').trigger('change');
    $('#section-racikan').find('.child').remove();
    $("#qty_nonracikan_id").val("");
    $(".konversi").html("-");
    $(".val_qty").html("-");
    $(".lb_stok").html("");
    $("#penyimpanan").html("");
    $('#satuankecil_nama').val(null).trigger('change');
    $('#satuankecil_nama_r').val(null).trigger('change');
    $("#qty_racikan_id").val("");
    $(".konversi_r").html("-");

    // Cek reset all
    if (reset_all == true) {
        // Reset all
        $('#select_depo').val('').trigger('change');
        $('#reseptur_iter').val('');
        $('#instruksiform-catatan_instruksi').val('');
    }
}

$('#select_depo').bind('change.select2', ({ delegateTarget }) => {

    let depoId = $(delegateTarget).val()
    $(".obatreseptur").docoPaginationSelec2(
        config = {
            placeholder : '-- Pilih Obat/Alkes--',     
            _api : '/ranap/pemeriksaan-rawat-inap/list-obat-alkes-depo?ruangan_id='+depoId+'&kelaspelayananId='+kelaspelayananId+'&penjaminId='+penjaminId,
            ajax: {
                processResults: function (data, params) {
                    var _results = data.result;
                    params.page = params.page || 1;
                    return {
                    results: data.result,
                        pagination: {
                            more: data.pagination.more
                                    }
                    }
                },
                success: (res) => {
                    apotek.list_stok = {}
                    res.result.map( function(items){
                        apotek.list_stok[items.id] = items
                    })
                }
            }
        }
    );

    // $.ajax({
    //     url : '/ranap/pemeriksaan-rawat-inap/list-obat-alkes-depo',
    //     method : 'POST',
    //     data : {
    //         depdrop_parents : [
    //             $(delegateTarget).val()
    //         ]
    //     },
    //     success : (res) =>{
    //         apotek.list_stok = {}
            
    //         res.result.map( function(items){
    //             apotek.list_stok[items.id] = items
    //             dataobat.push({
    //                 id : items.id,
    //                 text : items.name,
    //                 ...items.options
    //             })
    //             // let obj_obat = {}
    //             // Object.assign(obj_obat, items, {text: items.name})
    //             // dataobat.push(obj_obat)
    //         })
    //         $('.obatreseptur').select2({
    //             data : dataobat

    //         })


    //     }
    // })
})

var _configSelect = {
    placeholder: "-- Pilih Nama Obat --",
    width: '100%',
    language: 'id',
    ajax: {
        url: '/ranap/pemeriksaan-rawat-inap/list-obat-alkes-depo',
        dataType: 'json',
        quietMillis: 250,
        data: function(term, page) {
            return {
                q: term, 
                page: page || 1,
                ruangan_id: $("#select_depo").val(),
            }
        },
        results: function (data, page) {
            var more = (page * 30) < data.total_count;

            return { results: data.items, more: more };
        },
        dropdownCssClass: "bigdrop",
        escapeMarkup: function (m) { return m; },
        processResults: function(res, params) {
            params.page = params.page || 1;
            var arr = [];
            $.each(res.data_stok, function(index, value) {
                if (index < 10) {
                    arr.push({
                        id: value.obatalkes_id,
                        text: value.obatalkes_nama
                    })

                    let data = [];
                    let response = res.data_stok;
                    for (var i in response) {
                        data.push({ id: response[i].obatalkes_id, text: response[i].obatalkes_nama });
                        apotek.list_stok[response[i].obatalkes_id] = response[i];
                    }
                }
            });
            return {
                results: arr,
                pagination: {
                    more: res.data_stok.length > 10
                }
            };
        }
    } 
};

var _changeSelect = function (elment) {
    var id = $(elment).val();
    var selected = apotek.list_stok[id];
    var _parent = $(elment).closest('div.child');
    if(_parent.length !== 0) {
        var iterasi = _parent.attr('class');
        var regex = /child-[0-9 -()+]+$/;
        if(iter = regex.exec(iterasi)) {
            iter = iter[0].substring(6);
        }
        if (typeof selected !== "undefined") {
            _parent.find('#tmp_stok_sisa_r'+iter).val(!isNaN(selected.options.qty_tersedia) ? selected.options.qty_tersedia : 0);
            _parent.find('#stok_sisa_r'+iter).val(selected.options.qty_tersedia);
            _parent.find('#harga_reseptur_r'+iter).val(selected.options.harganetto);
            _parent.find('#tmp_hargasatuan_r'+iter).val(selected.options.hargajual);
            _parent.find('#satuan_id_reseptur_r'+iter).val(selected.options.satuankecil_id);
            _parent.find('#satuan_default_r'+iter).val(selected.options.satuankecil_nama);
            _parent.find('#satuankecil_nama_r'+iter).select2();
            _parent.find('#satuankecil_nama_r'+iter).depdrop({
                depends: ['obatalkes_id_'+iter],
                url: '/ranap/pemeriksaan-rawat-inap/list-satuan-besar',

            }).on('depdrop:afterChange', function(event, id, value, textStatus) {
                $("#satuankecil_nama_r"+iter).trigger("change");
                var _response = $('#satuankecil_nama_r'+iter).depdrop('getAjaxResults');
                var _nilai_konversi = $("#nilai_konversi_r"+iter).val();
                var _stok = $("#stok_sisa_r"+iter).val();
                var _harga = $("#tmp_hargasatuan_r"+iter).val();

                if(_nilai_konversi == 0) {
                    var _konversi_stok = 0;
                    var _konversi_harga = 0;
                }
                else {
                    _konversi_stok = _stok/_nilai_konversi;
                    _konversi_harga = _harga*_nilai_konversi;
                }
                
                $.each(_response.output, function (x,y) {
                  _group[y.id] = y.nilai_konversi;
                });

                $("#stok_sisa_r_"+iter).val(_konversi_stok);
                // $("#tmp_hargasatuan_r"+iter).val(_konversi_harga);

                _satuanChange(event.delegateTarget)
            });
            
        }
    } 
    else {
        if (typeof selected !== "undefined") {
            $("#tmp_stok_sisa_r").val(!isNaN(selected.qty_tersedia) ? selected.qty_tersedia : 0);
            $("#stok_sisa_r").val(!isNaN(selected.qty_tersedia) ? selected.qty_tersedia : 0);
            $("#satuan_default_r").val(selected.satuankecil_nama);
            $("#tmp_hargasatuan_r").val(selected.harganetto);
            $("#harga_reseptur_r").val(selected.harganetto);
            $('#satuan_id_reseptur_r').val(selected.satuankecil_id);
        }
    }
}

var _satuanChange = function (elment) {
    var _id = $(elment).val();
    var _parent = $(elment).closest('div.child');
    if(_parent.length !== 0) {
        var iterasi = _parent.attr('class');
        var regex = /child-[0-9 -()+]+$/;
        if(iter = regex.exec(iterasi)) {
            iter = iter[0].substring(6);
        }
        var _konversi = parseFloat((typeof _group[_id] === 'undefined') ? 0 : _group[_id]);
        var _stok = (typeof $("#tmp_stok_sisa_r"+iter).val() === 'undefined') ? 0 : $("#tmp_stok_sisa_r"+iter).val();
        var _harga = $("#tmp_hargasatuan_r"+iter).val();

        if(_konversi == 0) {
            var _konversi_stok = 0;
            var _konversi_harga = 0;
        }
        else {
            _konversi_stok = parseFloat(_stok/_konversi);
            _konversi_harga =  parseFloat(_harga*_konversi);
        }
        _konversi_harga = docoHelper.numberFormat(_konversi_harga, 2, ",", ".");

        var lastText = $('#satuankecil_nama_r'+iter).select2('data');
        if(lastText) {
            lastText = lastText[0].text;
        }
        else {
            lastText = '';
        }

        _parent.find('#satuanbesar_id_reseptur_r'+iter).val(_id);
        _parent.find('#satuankecil_nama_r'+iter).val(_id);
        _parent.find('#satuankecil_r'+iter).val(lastText);
        _parent.find('#penyimpanan_js'+iter).html(lastText);
        _parent.find('.lb_stok_r'+iter).html(lastText);
        _parent.find('#nilai_konversi_r'+iter).val(_konversi.toFixed(2));
        // _parent.find('#qty_konversi_r'+iter).val();
        _parent.find('#harga_reseptur_r'+iter).val(_konversi_harga);
        _parent.find('#qty_racikan_id'+iter).val('');
        _parent.find('#jml_konversi_r'+iter).val(0);
        // _parent.find('#tmp_hargasatuan_r'+iter).val(_konversi_harga);
        _parent.find('#stok_sisa_r'+iter).val(_konversi_stok.toFixed(2));
    }
    else {
        var _konversi = (typeof _group[_id] === 'undefined' || _group[_id] == null) ? 0 : _group[_id];
        var _stok = (typeof $("#tmp_stok_sisa_r").val() === 'undefined' || $("#tmp_stok_sisa_r").val() == null) ? 0 : $("#tmp_stok_sisa_r").val();
        var _konversi_stok = (typeof _konversi === 'undefined' || _konversi == null) ? 0 : _stok/_konversi;
        var _harga = $("#harga_reseptur_r").val();
        var _konversi_harga = _harga*_konversi;

        $("#satuanbesar_id_reseptur_r").val(_id);
        $("#nilai_konversi_r").val(_konversi);
        $("#stok_sisa_r").val(_konversi_stok);
        // $('#qty_konversi_r').val();
        $("#harga_reseptur_r").val(!isNaN(_konversi_harga) ? _konversi_harga : _harga);
    }
}

var _qtyChange = function ( elment) {
    var _qty = docoHelper.convertToAngka($(elment).val())
    var _parent = $(elment).closest('div.child');

    if(_parent.length !== 0) {
        var iterasi = _parent.attr('class');
        var regex = /child-[0-9 -()+]+$/;
        if(iter = regex.exec(iterasi)) {
            iter = iter[0].substring(6);
        }

        var nilai_konversi = $("#nilai_konversi_r"+iter).val();
        var konversi = parseFloat(_qty * nilai_konversi);
        var satuankecil = $('#satuan_default_r'+iter).val();
        var stokTersedia = $("#stok_sisa_r"+iter).val();

        if(konversi > (stokTersedia * nilai_konversi)) {
            docoNotification('warning', 'Peringatan', 'Maaf, Stok Tersedia tidak mencukupi.');
            _parent.find('#qty_racikan_id'+iter).val(0);
            _parent.find('#jml_konversi_r'+iter).val(0);
        }
        else {
            _parent.find('#jml_konversi_r'+iter).val(konversi.toFixed(2) + ' ' + satuankecil);
            _parent.find('#qty_konversi_r'+iter).val(konversi.toFixed(2));
        }
    }
    else {
        var nilai_konversi = $("#nilai_konversi_r").val();
        var konversi = _qty * nilai_konversi;
        var satuankecil = $('#satuan_default_r').val();
        var stokTersedia = $("#stok_sisa_r").val();

        if(konversi > (stokTersedia * nilai_konversi)) {
            docoNotification('warning', 'Peringatan', 'Maaf, Stok Tersedia tidak mencukupi.');
            $(elment).val(0);
        }
        else {
            $("#jml_konversi_r").val(konversi + ' ' + satuankecil);
        }
    }
}
// $('.racikan_append').select2(_configSelect).on('change',_changeSelect);
$(".qty-reseptur").on('change', _qtyChange);
$(".satuankecil_nama").select2();
$(".satuankecil_nama").on('change', _satuanChange);

$('#select_depo').trigger('change')