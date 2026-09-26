/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

// Global Variable

var tmpPenjamin = [];
var input = document.getElementById("no_kartu");

$(document).ready(function() {

    generateTablePenjamin()

    var total_tagihan = docoHelper.convertToAngka((typeof $("#total_tagihan").val() == 'undefined') ? 0 : $("#total_tagihan").val())
    var _dijamin = total_penjamin > 0 ? total_tagihan - total_penjamin : total_tagihan
    $("#dijamin").val(docoHelper.convertToRupiah(_dijamin > 0 ? _dijamin : 0));

    // infinity scroll Select2 with helper docoHealth.js
    // config = {} : untuk melakukan custom config pada js untuk kebutuhan data di select2 / modifikasi response ajax
    $("#penjamin_id").docoPaginationSelec2(
        // dapat disesuaikan dengan kebutuhan data / customize
        config = {
            placeholder: '-- Cari Penjamin --', // custom placeholder (optional) default null
            _api: '/kasir/pembayaran-tagihan/list-penjamin', // get data
        }
    )

    $('#penjamin_id').on('change', function(e) {
        // reset field
        $("#no_kartu").val("");
    })

    // for save data table temporary
    $('#simpan-table-multi-penjamin').on('click', function(e) {
        e.preventDefault()
        var input = {
            penjamin_id: $('#penjamin_id').val(), // data yg di input ke database
            label_penjamin: $('#penjamin_id').children("option:selected").text(), // data yg di tampilkan di tabel temporary
            no_kartu: $('#no_kartu').val(), // data yg di input ke database
            dijamin: $('#dijamin').val(), // data yg di input ke database
            dijamin_angka: docoHelper.convertToAngka($('#dijamin').val()),

        }
        total_penjamin = parseInt(total_penjamin) + docoHelper.convertToAngka($('#dijamin').val());

        if ($('#penjamin_id').val() == null || $('#penjamin_id').val() == "") {
            docoNotification("error", "Data Penjamin Belum Dipilih!", "pilih salah satu penjamin!");
            return false
        }

        if ($('#no_kartu').val() == null || $('#no_kartu').val() == "") {
            docoNotification("error", "Nomor Kartu Belum di Input!", "harus input Nomor Kartu terlebih dahulu!");
            return false
        }

        if ($('#dijamin').val() == null || $('#dijamin').val() == "") {
            docoNotification("error", "Nominal Dijamin Belum di Input!", "harus input nominal yang dijamin terlebih dahulu!");
            return false
        }

        if (docoHelper.convertToAngka($('#dijamin').val() < 0)) {
            docoNotification("error", "Nominal Dijamin Tidak Boleh Minus!", "harus input nominal lebih dari 0 (nol)!");
            return false
        }

        if (tmpPenjamin[input.penjamin_id]) {
            docoNotification("error", "Data Penjamin Sudah Ada!", "tidak boleh menginputkan data penjamin yang sama!");
            return false
        }
        tmpTablePenjamin.push(input)
        generateTablePenjamin()

        // reset field
        $("#no_kartu").val("");
        $("#dijamin").val("");
        $('#penjamin_id').val(null).trigger('change');
        // $('#total_dibayar').trigger('change');
        _hitungTagihan()
    })

    input.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            document.getElementById("simpan-table-multi-penjamin").click();
        }
    });
    _hitungTagihan()
});

// for generate table from input form data
function generateTablePenjamin() {
    $('.isi-table').hide()
    $('#table-multi-penjamin > tbody > tr').not('tr.isi-table').remove()
    var _html = ""
    var no = 1;
    var totalDijamin = 0;
    tmpPenjamin = [];
    tmpTablePenjamin.forEach(function(val, key) {
        totalDijamin = parseInt(totalDijamin) + docoHelper.convertToAngka(val.dijamin);
        tmpPenjamin[val.penjamin_id] = val
        _html += `
        <tr>
            <td>${no++}.</td>
            <td>${val.label_penjamin}</td>
            <td>${val.no_kartu}</td>
            <td>Rp. ${val.dijamin}</td>
            <td><button type='button' data-id='${key}' class='delete-table-multi-penjamin btn btn-danger btn-labeled btn-xs delete btn-block'><b><i class="fa fa-trash"></i></b>Hapus </button></td>
        </tr>
        `
    })
    $('#table-multi-penjamin > tbody').append(_html);
    $("#subsidi-asuransi").val(docoHelper.convertToRupiah(totalDijamin));
    total_penjamin = totalDijamin;

    var _tagihanPasien = docoHelper.convertToAngka((typeof $("#total_tagihan").val() == 'undefined') ? 0 : $("#total_tagihan").val());
    var _total_balance_rs = totalDijamin - _tagihanPasien;
    $("#tagihan_pasien").html(docoHelper.convertToRupiah(Math.abs(_total_balance_rs)));

    if (_total_balance_rs < 0) {
        _total_balance_rs = 0;
    }

    // if(docoHelper.convertToAngka($("#tagihan_pasien").html()) == 0) {
    //     $("#total_dibayar").prop("readonly", true);
    // }
    // else {
    //     $("#total_dibayar").prop("readonly", false);
    // }

    $("#total_balance_rs").val(docoHelper.convertToRupiah(_total_balance_rs));
    if ($("#total_dibayar").val() != 0) {
        var _totalDibayar = docoHelper.convertToAngka($('#total_dibayar').val());
        var _totalTagihan = docoHelper.convertToAngka((typeof $("#tagihan_pasien").html() == 'undefined') ? 0 : $("#tagihan_pasien").html());
        var _sisa = _totalDibayar - _totalTagihan;

        $("#total_sisa_piutang").val(_sisa).trigger("change");
    }

    // for delete data one by one in table temporary
    $(`.delete-table-multi-penjamin`).on('click', function(e) {
        e.preventDefault()
        var tmpID = $(this).attr('data-id')
            // $(this).closest('tr').remove()
        total_penjamin = parseInt(total_penjamin) - docoHelper.convertToAngka(tmpTablePenjamin[tmpID]["dijamin"]);
        tmpTablePenjamin.splice(tmpID, 1)
        generateTablePenjamin()
        _hitungTagihan()
            // $('#total_dibayar').trigger('change');
    })
    _hitungTagihan()
}