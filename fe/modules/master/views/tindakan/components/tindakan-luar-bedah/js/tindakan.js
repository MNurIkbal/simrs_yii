/* 
    Author : Ripan
*/

// Global Variable data_detail
var tableTindakanLuarBedah = [];
$('#ditagihkan').parent().parent().css("margin-top", 0)

$(document).ready(function() {
    if(data_detail.length > 0) {
        tableTindakanLuarBedah = data_detail
        generateTableTindakan()
    } else {
        // dengan infinity scroll
        $("#daftartindakanuntukluarbedah_id").select2({
            placeholder: "-- Cari Tindakan --",
            minimumInputLength: 0,
            ajax: {
                url: "/master/tindakan/list-tindakan-luar-bedah",
                dataType: "json",
                data: function(params) { 
                    return {
                        q:params.term, 
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
    }

    // dengan infinity scroll
    $("#tindakanluarbedah_id").select2({
        placeholder: "-- Cari Tindakan Di Luar Bedah --",
        minimumInputLength: 0,
        ajax: {
            url: "/master/tindakan/list-tindakan-luar-bedah",
            dataType: "json",
            data: function(params) { 
                return {
                    q:params.term, 
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

    // for save data table temporary
    $('#simpan-table-tindakan-luar-bedah').on('click', function(e){
        e.preventDefault()

        var input = {
            tindakanluarbedah_id : $('#tindakanluarbedah_id').val(),
            tindakanluarbedah_nama : $('#tindakanluarbedah_id').children("option:selected").text(), 
            qty : $('#qty').val(),
            is_ditagihkan : $('#ditagihkan').is(':checked'),
        }

        if ($('#tindakanluarbedah_id').val() == null || $('#tindakanluarbedah_id').val() == "") {
            docoNotification("error", "Data Tindakan Di Luar Bedah Belum Dipilih!", "pilih salah satu tindakan di luar bedah!");
            return false
        }

        if ($('#qty').val() == null || $('#qty').val() == "") {
            docoNotification("error", "QTY Belum di Input!", "harus input QTY terlebih dahulu!");
            return false
        }

        if (tableTindakanLuarBedah.some((vs) => vs.tindakanluarbedah_id == input.tindakanluarbedah_id)) {
            docoNotification("error", "Tindakan Di Luar Bedah Sudah Ada!", "tidak boleh menginputkan data tindakan di luar bedah yang sama!");
            return false
        }

        if ($("#daftartindakanuntukluarbedah_id").val() == input.tindakanluarbedah_id) {
            docoNotification("error", "Tindakan Di Luar Bedah Sudah Ada!", "tidak boleh menginputkan data tindakan di luar bedah yang sama dengan Tindakan!");
            return false
        }

        tableTindakanLuarBedah.push(input)
        generateTableTindakan()

        // reset field
        $('#tindakanluarbedah_id').val(null).trigger('change'); 
        $("#qty").val("");    
        $('#ditagihkan').prop('checked', false);   
    })
    
    // for save data table temporary to database
    $("#btn-simpan-tindakan-luar-bedah").on("click",function (e) {
        e.preventDefault();

        $(this).docoForm("click",{
            url : url,
            method : "POST",
            type : "json",
            data: {
                data: JSON.stringify(tableTindakanLuarBedah),
                tindakan: $('#daftartindakanuntukluarbedah_id').val()
            },
            success : function () {
                $('.btn-back-tindakan').trigger('click')
            }
        });
    });

    $('#qty').keyup(function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            document.getElementById("simpan-table-tindakan-luar-bedah").click();
        }
    });
});

// for generate table from input form data
function generateTableTindakan() {
    $('#table-tindakan-luar-bedah > tbody > tr').not('tr.isi-table-luar-bedah').remove()
    $('.isi-table-luar-bedah').hide()

    var _html = ""
    tableTindakanLuarBedah.forEach(function (val, key) {
        _html += `
            <tr>
                <td>${key+1}</td>
                <td>${val.tindakanluarbedah_nama}</td>
                <td>${val.qty ?? 0}</td>
                <td>${val.is_ditagihkan == true ? '&#10003;' : '&#10005;'}</td>
                <td>
                    <div class="btn-group pull-right">
                        <button type='button' data-id='${key}' class='delete-table-tindakan-luar-bedah btn btn-danger btn-labeled btn-xs delete btn-block'><b><i class="fa fa-trash"></i></b>Hapus </button></td>
                    </div>
            </tr>
        `
    })
    $('#table-tindakan-luar-bedah > tbody').append(_html)

    // for delete data one by one in table temporary
    $(`.delete-table-tindakan-luar-bedah`).on('click', function(e){
        e.preventDefault()
        var tmpID = $(this).attr('data-id')

        tableTindakanLuarBedah.splice(tmpID, 1)
        generateTableTindakan()
    })
}