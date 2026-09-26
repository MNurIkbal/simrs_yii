/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

// Global Variable


var tmpKomponen = [];
var input = document.getElementById("add_komponentarif_id");
var total_komponen = 0

$(document).ready(function() {

    generateTableKomponen(list_id)
    var _total_awal = ($('#total_komponen').val() == '' || typeof($('#total_komponen').val()) == 'undefined') ? 0 : docoHelper.convertToAngka($('#total_komponen').val());
    $("#total_komponen").val(docoHelper.convertToRupiah(_total_awal));

    // infinity scroll Select2 with helper docoHealth.js
    // config = {} : untuk melakukan custom config pada js untuk kebutuhan data di select2 / modifikasi response ajax
    $("#add_komponentarif_id").docoPaginationSelec2(
        // dapat disesuaikan dengan kebutuhan data / customize
        config = {
            placeholder : '-- Cari Komponen --',      // custom placeholder (optional) default null
            _api : '/master/tarif-tindakan/list-komponen',   // get data
        }
    )

    // for save data table temporary
    $('#simpan-table-add-component').on('click', function(e){
        e.preventDefault()
        var getKomponen = $("#add_komponentarif_id option:selected").data()
        if (getKomponen != undefined) {
            var input = {
                komponen_id : $('#add_komponentarif_id').val(), // data yg di input ke database
                // label_komponen : $('#add_komponentarif_id').children("option:selected").text(), // data yg di tampilkan di tabel temporary
                label_komponen : getKomponen.data.datavalue.komponentarif_nama, // data yg di tampilkan di tabel temporary
                nominal : $('#harga_komponen').val(), // data yg di input ke database
                nominal_angka : docoHelper.convertToAngka($('#harga_komponen').val()),
            }
        }
        total_komponen = docoHelper.convertToAngka($("#total_komponen").val()) + docoHelper.convertToAngka($('#harga_komponen').val());

        if ($('#add_komponentarif_id').val() == null || $('#add_komponentarif_id').val() == "") {
            docoNotification("error", "Data Komponen Belum Dipilih!", "pilih salah satu Komponen!");
            return false
        }

        if ($('#harga_komponen').val() == null || $('#harga_komponen').val() == "") {
            docoNotification("error", "Harga Komponen Belum di Input!", "harus input nominal yang harga komponen terlebih dahulu!");
            return false
        }

        if (docoHelper.convertToAngka($('#harga_komponen').val() < 0 )) {
            docoNotification("error", "Harga Komponen Tidak Boleh Minus!", "harus input nominal lebih dari 0 (nol)!");
            return false
        }

        if (tmpKomponen[input.komponen_id]) {
            docoNotification("error", "Data Komponen Sudah Ada!", "tidak boleh menginputkan data komponen yang sama!");
            return false
        }
        list_komponen[list_id].komponen.push(input)
        generateTableKomponen(list_id)
        $("#total_komponen").val(docoHelper.convertToRupiah(total_komponen));

        // reset field
        $("#harga_komponen").val("");
        $('#add_komponentarif_id').val(null).trigger('change');
    })
    
    if(input){
        input.addEventListener("keyup", function(event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                document.getElementById("simpan-table-add-component").click();
            }
        });
    }
});

