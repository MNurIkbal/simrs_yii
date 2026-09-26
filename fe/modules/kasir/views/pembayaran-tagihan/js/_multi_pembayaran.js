/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

// Global Variable

var tmpPembayaran = [];
var input = document.getElementById("no_kartu");

$(document).ready(function() {
    generateTablePembayaran();

    var _totalDibayar = docoHelper.convertToAngka($("#total_dibayar").val());
    var _tunai = _totalDibayar - total_pembayaran;
    var _nontunai = total_pembayaran;
    var _total = total_pembayaran > 0 ? total_pembayaran : tmp_dibayar;

    _tunai = _tunai > 0 ? (_tunai) : 0;

    $("#tunai").val(docoHelper.convertToRupiah(_tunai));
    $("#non-tunai").val(docoHelper.convertToRupiah(_nontunai));

    var tmp_dibayar = docoHelper.convertToAngka($("#tunai").val());

    if (_total > 0) {
        $("#total").val(docoHelper.convertToRupiah(_totalDibayar));
    } else {
        $("#total").val(docoHelper.convertToRupiah(_total));
    }

    $("#jenisnontunai_id").docoPaginationSelec2(
        // dapat disesuaikan dengan kebutuhan data / customize
        config = {
            placeholder: '-- Cari Pembayaran --', // custom placeholder (optional) default null
            _api: '/kasir/master-api/get-data-nontunai', // get data
        }
    )

    $("#edclist_id").docoPaginationSelec2(
        // dapat disesuaikan dengan kebutuhan data / customize
        config = {
            placeholder: '-- Pilih Mesin EDC --', // custom placeholder (optional) default null
            _api: '/kasir/master-api/get-data-edc', // get data
        }
    )

    // for save data table temporary
    $('#simpan-table-multi-pembayaran').on('click', function(e) {
        e.preventDefault()
        var input = {
                no_kartu: $('#no_kartu').val(), // data yg di input ke database
                jenisnontunai_id: $('#jenisnontunai_id').val(), // data yg di input ke database
                edclist_id: $('#edclist_id').val(), // data yg di input ke database
                label_jenisnontunai: $('#jenisnontunai_id').children("option:selected").text(), // data yg di tampilkan di tabel temporary
                label_edc: $('#edclist_id').children("option:selected").text(), // data yg di tampilkan di tabel temporary
                catatan: $('#catatan').val(), // data yg di input ke database
                nominal: $('#nominal').val(), // data yg di input ke database
                nominal_angka: docoHelper.convertToAngka($('#nominal').val()),
            }
            // merubah format jadi angka
        total_pembayaran = parseInt(total_pembayaran) + docoHelper.convertToAngka($('#nominal').val());

        if ($('#no_kartu').val() == null || $('#no_kartu').val() == "") {
            docoNotification("error", "Nomor Kartu Belum di Input!", "harus input Nomor Kartu terlebih dahulu!");
            return false
        }

        // if ($('#catatan').val() == null || $('#catatan').val() == "") {
        //     docoNotification("error", "Catatan Belum di Input!", "harus input Catatan terlebih dahulu!");
        //     return false
        // }

        if ($('#jenisnontunai_id').val() == null || $('#jenisnontunai_id').val() == "") {
            docoNotification("error", "Jenis Pembayaran Belum di Input!", "harus input Jenis Pembayaran terlebih dahulu!");
            return false
        }

        if ($('#edclist_id').val() == null || $('#edclist_id').val() == "") {
            docoNotification("error", "Mesin EDC Belum di Input!", "harus input Mesin EDC terlebih dahulu!");
            return false
        }

        if ($('#nominal').val() == null || $('#nominal').val() == "") {
            docoNotification("error", "Nominal Belum di Input!", "harus input Nominal terlebih dahulu!");
            return false
        }

        if (docoHelper.convertToAngka($('#nominal').val() < 0)) {
            docoNotification("error", "Nominal Tidak Boleh Minus!", "harus input nominal lebih dari 0 (nol)!");
            return false
        }

        if (tmpPembayaran[input.no_kartu]) {
            docoNotification("error", "Data Nomor Kartu Sudah Ada!", "tidak boleh menginputkan data Nomor Kartu yang sama!");
            return false
        }
        tmpTablePembayaran.push(input)
        generateTablePembayaran()

        var _tmp_sisa = (tmp_dibayar == 0) ? docoHelper.convertToAngka($("#tagihan_pasien").html()) : tmp_dibayar;
        var tmp_tunai = _tmp_sisa - total_pembayaran;
        var _tmp_tunai = tmp_tunai > 0 ? tmp_tunai : 0;
        var total_all = _tmp_tunai + total_pembayaran;
        var _total_nontunai = total_all - _tmp_tunai;

        $("#tunai").val(docoHelper.convertToRupiah(_tmp_tunai));
        $("#non-tunai").val(docoHelper.convertToRupiah(_total_nontunai));
        $("#total").val(docoHelper.convertToRupiah(total_all));
        // $("#total_dibayar").val(docoHelper.convertToRupiah(total_all));
        $("#total_nontunai").val(docoHelper.convertToRupiah(_total_nontunai));
        // $("#total_sisa_piutang").val(total_all - docoHelper.convertToAngka($("#tagihan_pasien").html())).trigger('change');
        // $("#total_dibayar").trigger('change');

        // $(".label-tunai").html("Total Tagihan Tunai").css("font-weight", "bold");
        // $(".label-nontunai").html("Total Tagihan Non Tunai").css("font-weight", "bold");
        // $("#tagihan-tunai").html(docoHelper.convertToRupiah(_tmp_tunai));
        // $("#tagihan-nontunai").html(docoHelper.convertToRupiah(_total_nontunai));

        // reset field
        $("#no_kartu").val("");
        $("#catatan").val("");
        $("#nominal").val("");
        $('#jenisnontunai_id').val(null).trigger('change');
        $('#edclist_id').val(null).trigger('change');

        _setFieldPembayaran()
        _hitungTagihan()

    })



    input.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            document.getElementById("simpan-table-multi-pembayaran").click();
        }
    });

    $('#tunai').on('input', function(e) {
        var cash = $(this).val()
        var total_all = docoHelper.convertToAngka(cash > 0 ? cash : 0) + total_pembayaran;
        var total_tagihan = docoHelper.convertToAngka($("#tagihan_pasien").html());
        var total_sisa = total_tagihan - docoHelper.convertToAngka(cash);

        $("#total").val(docoHelper.convertToRupiah(total_all));
        $("#total_dibayar").val(docoHelper.convertToRupiah(total_all));
        $("#total_sisa_piutang").val(docoHelper.convertToRupiah(total_sisa)).trigger("change");
        _setFieldPembayaran()
    })
    _setFieldPembayaran()
});

var _setFieldPembayaran = () => {
    var _totalPembayaran = 0
    var totalPembulatan = parseFloat(docoHelper.convertToAngka($("#total-pembulatan").val()))
    var _bayarTunai = 0
    if (tmpTablePembayaran.length > 0) {
        tmpTablePembayaran.forEach((val, key) => {
            _totalPembayaran += parseFloat(val.nominal_angka)
        })
    }
    if (_totalPembayaran < totalPembulatan) {
        _bayarTunai = totalPembulatan - _totalPembayaran
    }
    $('#tunai').val(_bayarTunai).trigger('change')
    $('#non-tunai').val(_totalPembayaran).trigger('change')
    $('#total_nontunai').val(_totalPembayaran).trigger('change')
    $('#total').val(totalPembulatan).trigger('change')
    $('#total_dibayar').trigger('change')
}


// for generate table from input form data
function generateTablePembayaran() {
    $('.isi-table').hide()
    $('#table-multi-pembayaran > tbody > tr').not('tr.isi-table').remove()
    var _html = ""
    var no = 1;
    tmpPembayaran = []
    tmpTablePembayaran.forEach(function(val, key) {
        tmpPembayaran[val.pembayaran_id] = val
        _html += `
        <tr>
            <td>${no++}.</td>
            <td>${val.no_kartu}</td>
            <td>${val.label_jenisnontunai}</td>
            <td>${val.label_edc}</td>
            <td>${val.catatan}</td>
            <td>${val.nominal}</td>
            <td><button type='button' data-id='${key}' class='delete-table-multi-pembayaran btn btn-danger btn-labeled btn-xs delete btn-block'><i class="fa fa-trash"></i> </button></td>
        </tr>
        `
    })
    $('#table-multi-pembayaran > tbody').append(_html)
        // for delete data one by one in table temporary
    $(`.delete-table-multi-pembayaran`).on('click', function(e) {
        e.preventDefault()
        var tmpID = $(this).attr('data-id')
            // $(this).closest('tr').remove()
        total_pembayaran = parseInt(total_pembayaran) - docoHelper.convertToAngka(tmpTablePembayaran[tmpID]["nominal"]);
        tmpTablePembayaran.splice(tmpID, 1)
        generateTablePembayaran()
        _setFieldPembayaran()
        _hitungTagihan()
    })
}