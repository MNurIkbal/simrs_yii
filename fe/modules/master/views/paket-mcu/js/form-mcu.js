/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

var tmpTablePaketTindakan = [];
var tmpPaketTindakan = [];
var tmpRuangan = [];
var input = document.getElementById("ruangan_id");

$(document).ready(function () {

    generateTablePenjamin()

    var _endpoint = 'tindakan'
    $("#is_active").prop("checked", true)
    ruangan_id = docoHelper.convertToAngka($('#ruangan_id').children("option:selected").val()) > 0 ? $('#ruangan_id').children("option:selected").val() : 0

    // infinity scroll Select2 with helper docoHealth.js
    // config = {} : untuk melakukan custom config pada js untuk kebutuhan data di select2 / modifikasi response ajax
    $("#ruangan_id").docoPaginationSelec2(
        // dapat disesuaikan dengan kebutuhan data / customize
        config = {
            placeholder : '-- Cari Instalasi/Ruangan --',      // custom placeholder (optional) default null
            _api : '/master/paket-mcu/instalasi-ruangan',   // get data
        }
    )

    // infinity scroll Select2 with helper docoHealth.js
    // config = {} : untuk melakukan custom config pada js untuk kebutuhan data di select2 / modifikasi response ajax
    $("#tipepaket_id").docoPaginationSelec2(
        // dapat disesuaikan dengan kebutuhan data / customize
        config = {
            placeholder : '-- Cari Paket/Tindakan --',      // custom placeholder (optional) default null
            _api : '/master/paket-mcu/'+_endpoint+'-ruangan?ruangan_id='+ruangan_id,   // get data
        }
    )
    
    input.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            document.getElementById("simpan-table-paket-mcu").click();
        }
    });

});

$("#paket").on('change', function (e){
    
    if ($("#paket").is(':checked')){
        _endpoint = 'paket'
    }else{
        _endpoint = 'tindakan'
    }
    ruangan_id = $('#ruangan_id').children("option:selected").val()

    // reset field
    $('#tipepaket_id').val(null).trigger('change');

    // infinity scroll Select2 with helper docoHealth.js
    // config = {} : untuk melakukan custom config pada js untuk kebutuhan data di select2 / modifikasi response ajax
    $("#tipepaket_id").docoPaginationSelec2(
        // dapat disesuaikan dengan kebutuhan data / customize
        config = {
            placeholder : '-- Cari Paket/Tindakan --',      // custom placeholder (optional) default null
            _api : '/master/paket-mcu/'+_endpoint+'-ruangan?ruangan_id='+ruangan_id,    // get data
        }
    )
})

$('#ruangan_id').on('change', function(e){
    if ($("#paket").is(':checked')){
        _endpoint = 'paket'
    }else{
        _endpoint = 'tindakan'
    }
    ruangan_id = $('#ruangan_id').children("option:selected").val()

    // infinity scroll Select2 with helper docoHealth.js
    // config = {} : untuk melakukan custom config pada js untuk kebutuhan data di select2 / modifikasi response ajax
    $("#tipepaket_id").docoPaginationSelec2(
        // dapat disesuaikan dengan kebutuhan data / customize
        config = {
            placeholder : '-- Cari Paket/Tindakan --',      // custom placeholder (optional) default null
            _api : '/master/paket-mcu/'+_endpoint+'-ruangan?ruangan_id='+ruangan_id,    // get data
        }
    )
    // reset field
    $('#tipepaket_id').val(null).trigger('change');
})

// for save data table temporary
$('#simpan-table-paket-mcu').on('click', function(e){
    e.preventDefault()

    // set paket/tindakan
    if ($("#paket").is(':checked')){
        $('#paketdetail_id').val($('#tipepaket_id').children("option:selected").val())
        $('#daftartindakan_id').val("")
    }else{
        $('#daftartindakan_id').val($('#tipepaket_id').children("option:selected").val())
        $('#paketdetail_id').val("")
    }

    var input = {
        ruangan_id : $('#ruangan_id').children("option:selected").val(), // data yg di input ke database
        label_instalasi : $('#ruangan_id').children("option:selected").text(), // data yg di tampilkan di tabel temporary
        tipepaket_id : $('#tipepaket_id').children("option:selected").val(), // data yg di input ke database
        label_tipe : $('#tipepaket_id').children("option:selected").text(), // data yg di tampilkan di tabel temporary
        paketdetail_id : $('#paketdetail_id').val(),
        daftartindakan_id : $('#daftartindakan_id').val()
    }
    
    if ($('#ruangan_id').val() == null || $('#ruangan_id').val() == "") {
        docoNotification("error", "Data Instalasi - Ruangan Belum Dipilih!", "pilih salah satu Instalasi - Ruangan!");
        return false
    }
    
    if ($('#tipepaket_id').val() == null || $('#tipepaket_id').val() == "") {
        docoNotification("error", "Data Paket/Tindakan Belum Dipilih!", "pilih salah satu Paket/Tindakan!");
        return false
    }

    if ((tmpPaketTindakan[input.tipepaket_id]) && (tmpRuangan[input.ruangan_id])) {
        docoNotification("error", "Data Paket/Tindakan pada ruangan tersebut Sudah Ada!", "tidak boleh menginputkan data Paket/Tindakan di ruangan yang sama!");
        return false
    }
    tmpTablePaketTindakan.push(input)
    generateTablePenjamin()

    // reset field
    $('#ruangan_id').val(null).trigger('change');
    $('#tipepaket_id').val(null).trigger('change');
    $("#paket").prop("checked", false)
    // _endpoint = 'tindakan'
})

$('#btn-ulang').on('click', function () {
    location.reload();
});

$('#btn-kembali').on('click', function () {
    window.location.href = "/master/paket-mcu"
});

$('.tipepaket_nama').on('keyup', function() {
    $('.tipepaket_namalainnya').val($(this).val());
});

// for save data table temporary to database
$("#btn-save-paket-mcu").on("click",function (e) {
    e.preventDefault();
    var hash = window.location.hash;
    var status = 0
    if ($("#is_active").is(':checked')){
        status = 1
    }
    $(this).docoForm("click",{
        url : "/master/paket-mcu/create-mcu",
        method : "POST",
        type : "json",
        data: {
            detail:JSON.stringify(tmpTablePaketTindakan),
            tipepaket_nama:$('#tipepaket_nama').val(),
            tipepaket_kode:$('#tipepaket_kode').val(),
            tipepaket_namalainnya:$('#tipepaket_namalainnya').val(),
            keterangan_tipepaket:$('#keterangan_tipepaket').val(),
            is_mcu:true,
            is_active:status
        },
        success : function (data) {
            $('#btn-kembali').trigger('click')
        }
    });
});

// for generate table from input form data
function generateTablePenjamin(){
    $('.isi-table').hide()
    $('#table-paket-mcu > tbody > tr').not('tr.isi-table').remove()
    var _html = ""
    var no = 1;
    tmpPaketTindakan = [];
    tmpRuangan = [];
    tmpTablePaketTindakan.forEach(function (val, key) {
    tmpPaketTindakan[val.tipepaket_id] = val
    tmpRuangan[val.ruangan_id] = val
        _html += `
        <tr>
            <td>${no++}.</td>
            <td>${val.label_instalasi}</td>
            <td>${val.label_tipe}</td>
            <td><button type='button' data-id='${key}' class='delete-table-paket-mcu btn btn-danger btn-labeled btn-xs delete btn-block'><b><i class="fa fa-trash"></i></b>Hapus </button></td>
        </tr>
        `
    })
    $('#table-paket-mcu > tbody').append(_html);

    // for delete data one by one in table temporary
    $(`.delete-table-paket-mcu`).on('click', function(e){
        e.preventDefault()
        var tmpID = $(this).attr('data-id')
        tmpTablePaketTindakan.splice(tmpID, 1)
        generateTablePenjamin()
    })
}