/* 
    Author : Wahyu
*/

// Global Variable
// var tmpTableTindakan = [];
var tmpTableTindakan = data_detail;
var tmpObat = [];
var input = document.getElementById("qty_input");

$(document).ready(function() {

    generateTableTindakan()
    $("#obatalkes_id").prop('disabled', true)

    // dropdown for choose obat alkes
    // $("#obatalkes_id").select2({
    //     placeholder: "-- Cari Obat Alkes --",
    //     minimumInputLength: 0,
    //     ajax: {
    //         url: "/master/tindakan/search-obat-alkes",
    //         dataType: "json",
    //         quietMillis: 250,
    //         data: function (params) {
    //             var query = {
    //               search: params,
    //             }
    //             return params;
    //         },
    //         processResults: function (data) {
    //           return {
    //             results: data.result
    //           };
    //         },
    //         dropdownCssClass: "bigdrop",
    //         escapeMarkup: function (m) { return m; },
    //     },
    // }).on("select2:select", function(e){
    //     var data = e.params.data;
    // });
    // dengan infinity scroll
    
    $("#group").on('change', function (){
        $("#obatalkes_id").empty().trigger('change');
        $("#satuanunit_id").empty();
        
        if($("#group").val()){
            $("#obatalkes_id").prop('disabled', false);
            $("#satuanunit_id").prop('disabled', false);
        }else{
            $("#obatalkes_id").prop('disabled', true);
            $("#satuanunit_id").prop('disabled', true);
        }
    });
    $("#obatalkes_id").select2({
        placeholder: "-- Cari Obat Alkes --",
        minimumInputLength: 0,
        ajax: {
            url: "/master/tindakan/list-obat-alkes",
            dataType: "json",
            data: function(params) { 
                console.log(params);
                return {
                    q:params.term, 
                    group:$("#group").val(), 
                    page:params.page || 1
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                results: data.result,
                    pagination: {
                        more: data.pagination
                                }
                }
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (markup) { 
                return markup; 
            },
            templateResult: function(object) { 
                return object.text; 
            },
            templateSelection: function (subject) { 
                return subject.text; 
            },
        },
    });

    // dropdown form choose satuan unit append to obat alkes id
    $(document).on("change", "#satuanunit_id", function(){
        let _val = $(this).val();
        let obatalkes_id = $('#obatalkes_id').val();

        // reset field
        $("#qty_input").val("");
        $("#qty_konversi").val("");
        $("#nilai_konversi").val(0);
    
        $.ajax({
            url: '/master/tindakan/get-nilai-konversi?obatalkes_id=' + obatalkes_id + '&satuanbesar_id=' + _val,
            type: 'GET',
            success: function(data) {
                var satuaninput_id = data.satuanbesar_id;
                var nilai_konversi = data.nilai_konversi;
                $("#satuaninput_id").val(satuaninput_id);
                $("#nilai_konversi").val(nilai_konversi);
            }
        })
    });

    // input qty and change for field qty konversi value
    $(document).on("input", "#qty_input", function(){
        var _qty = $(this).val();
        var nilai_konversi = $("#nilai_konversi").val();
        var konversi = _qty * nilai_konversi;
    
        $("#qty_konversi").html(konversi.toFixed(2));
        $("#qty_konversi").val(konversi.toFixed(2));
    });

    // for decimal number only
    $(document).on('input', '.doco-decimal', function() {
        match = (/(\d{0,9})[^.]*((?:\.\d{0,2})?)/g).exec(this.value.replace(/[^\d.]/g, ''));
        this.value = match[1] + match[2];
    });

    // for save data table temporary
    $('#simpan-table-tindakan-bmhp').on('click', function(e){
        e.preventDefault()
        var input = {
            obatalkes_id : $('#obatalkes_id').val(), // data yg di input ke database
            label_obat : $('#obatalkes_id').children("option:selected").text(), // data yg di tampilkan di tabel temporary
            satuanunit_id : $('#satuanunit_id').val(), // data yg di input ke database
            label_satuan : $('#satuanunit_id').children("option:selected").text(), // data yg di tampilkan di tabel temporary
            qty_input : $('#qty_input').val(), // data yg di input ke database
            qty_konversi : $('#qty_konversi').val(), // data yg di input ke database
            satuaninput_id : $('#satuaninput_id').val(), // data yg di input ke database
            daftartindakan_id : $('#daftartindakan_id').val(), // data yg di input ke database
            nilai_konversi : $('#nilai_konversi').val(), // data yg di input ke database
            group : $('#group').val(), // data yg di input ke database
            label_group : $('#group').children("option:selected").text(), // data yg di input ke database
        }

        if ($('#obatalkes_id').val() == null || $('#obatalkes_id').val() == "") {
            docoNotification("error", "Data Obat/Alkes Belum Dipilih!", "pilih salah satu Obat/Alkes!");
            return false
        }

        if ($('#qty_input').val() == null || $('#qty_input').val() == "") {
            docoNotification("error", "QTY Belum di Input!", "harus input QTY terlebih dahulu!");
            return false
        }

        if ($('#group').val() == null || $('#group').val() == "") {
            docoNotification("error", "Grup belum di Input!", "harus input grup terlebih dahulu!");
            return false
        }

        if (tmpObat[input.obatalkes_id]) {
            docoNotification("error", "Data Obat/Alkes Sudah Ada!", "tidak boleh menginputkan data obat/alkes yang sama!");
            return false
        }
        tmpTableTindakan.push(input)
        generateTableTindakan()

        // reset field
        $("#qty_input").val("");
        $("#qty_konversi").val("");
        $('#obatalkes_id').val(null).trigger('change');
        $('#satuanunit_id').val(null).trigger('change');
        $('#group').val(null).trigger('change');
        
    })
    
    // for save data table temporary to database
    $("#btn-update-tindakan-bmhp").on("click",function (e) {
        e.preventDefault();
        var hash = window.location.hash;
        var id = $('#daftartindakan_id').val();
        $(this).docoForm("click",{
            // url : "/master/tindakan/update-tindakan-bmhp",
            url : "/master/tindakan/update-tindakan-bmhp?id=".id,
            method : "POST",
            type : "json",
            data: {
                id:id,
                data:JSON.stringify(tmpTableTindakan),
                tindakan:$('#daftartindakan_id').val()
            },
            success : function (data) {
                $('.btn-back-tindakan').trigger('click')
            }
        });
    });

    input.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            document.getElementById("simpan-table-tindakan-bmhp").click();
        }
    });
    
});

// for generate table from input form data
function generateTableTindakan(){
    $('.isi-table-bmhp').hide()
    $('#table-tindakan-bmhp > tbody > tr').not('tr.isi-table-bmhp').remove()
    var _html = ""
    var no = 1;
    tmpObat = []
    tmpTableTindakan.forEach(function (val, key) {
    tmpObat[val.obatalkes_id] = val
        _html += `
        <tr>
            <td>${no++}</td>
            <td>${val.label_group}</td>
            <td>${val.label_obat}</td>
            <td>${val.label_satuan}</td>
            <td>${val.qty_input}</td>
            <td>${val.qty_konversi}</td>
            <td><button type='button' data-id='${key}' class='delete-table-tindakan-bmhp btn btn-danger btn-labeled btn-xs delete btn-block'><b><i class="fa fa-trash"></i></b>Hapus </button></td>
        </tr>
        `
    })
    $('#table-tindakan-bmhp > tbody').append(_html)
    // for delete data one by one in table temporary
    $(`.delete-table-tindakan-bmhp`).on('click', function(e){
        e.preventDefault()
        var tmpID = $(this).attr('data-id')
        // $(this).closest('tr').remove()
        tmpTableTindakan.splice(tmpID, 1)
        generateTableTindakan()
    })
}