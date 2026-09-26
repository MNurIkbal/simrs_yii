/*
* @Author: Sigit
* @Date:   2018-04-16 11:50:37
* @Last Modified by:   Sigit
* @Last Modified time: 2018-04-18 10:42:03
*/

// On click btn add
$(document).on('click', '#btn-add', function() {
    // Declare an array
    var existingId = [];

    // Loop table
    $('#tb-detail-pemesanan-dok-rekam-medik tbody').find('tr').each(function() {
        existingId.push(parseFloat($(this).data('id')));
    });

    // Check in array
    if(jQuery.inArray(parseFloat($("#data-posisi-dok-rekam-medik-id").val()), existingId) == '-1') {
        // Insert to table
        insert();
        $("#btn-save").prop("disabled", false);
        $('#filter_instalasi').prop('disabled', true);
        $('#filter_ruangan').prop('disabled', true);
    }
    else {
        // Alert
        alert("Data Dengan No. Rekam Medik: "+$("#data-no-rekam-medik").val()+" sudah dimasukkan kedalam tabel, silakan cari data yang lain!");
    }
});

// On click btn remove
$(document).on('click', '.btn-remove-row', function() {
    // Remove row
    $(this).closest('tr').remove();

    // Numbering
    $('.td-no').each(function(index) {
        // Assign number
        $(this).text(index+1);
    });
});

// On click print
$(document).on("click", ".btn-custom-print", function() {
    window.open("/rm/tra-pemesanan-dok-rekam-medik/export-pdf?pesandokrm_id="+$("#data-pesan-dok-rekam-medik-id").val());
});

// Function insert
function insert() {
    // Declare html
    var html;

    // Assign html
    html += "<tr data-id='"+$("#data-posisi-dok-rekam-medik-id").val()+"'>"+
        "<td class='td-no'></td>"+
        "<td>"+$("#data-tanggal-rekam-medis").val()+"</td>"+
        "<td>"+$('#filter_instalasi option:selected').text()+"</td>"+
        "<td>"+$('#filter_ruangan option:selected').text()+"</td>"+
        "<td>"+$("#data-no-rekam-medik").val()+"</td>"+
        "<td>"+$("#data-nama-pasien").val()+"</td>"+
        "<td>"+$("#data-warna-dok-nama").val()+"</td>"+
        "<td class='td-hapus'><button type='button' class='btn btn-danger btn-remove-row'><i class='fa fa-remove'></i></button></td>"+
        "<input type='hidden' name='PemesananDokRekamMedikDetailForm["+$("#data-posisi-dok-rekam-medik-id").val()+"][dokrekammedis_id]' value='"+$("#data-dok-rekam-medis-id").val()+"' readoly='readonly'>"+
    "</tr>";

    // Append
    $("#tb-detail-pemesanan-dok-rekam-medik").find('tbody').append(html);

    // Numbering
    $('.td-no').each(function(index) {
        // Assign number
        $(this).text(index+1);
    });
}

// Datepicker
$(".date").pickadate({
    applyClass: "bg-slate-600",
    cancelClass: "btn-default",
    locale: {
        format: "DD-MMMM-YYYY"
    },
    disable: [
        {from: [0,0,0], to: new Date((new Date()).valueOf()-1000*60*60*24)}
    ]
});

// After submit
$("#pemesanan-dok-rekam-medik-form").docoForm("submit", {
    success : function(data) {
        // Set some data
        $("#data-pesan-dok-rekam-medik-id").val(data.response.pesandokrm_id);
        $("#pemesanandokrekammedikform-no_pesandokrm").val(data.response.no_pesandokrm);

        // Make readonly
        $("#select2-filter_instalasi-container").prop("disabled", true);
        // $("#dd-filter_ruangan").prop("disabled", true);
        $("#pemesanandokrekammedikform-tgl_mintakirim").prop("readonly", true);
        $("#select2-dd-no-rekam-medik-container").prop("disabled", true);

        // Delete column hapus
        $(".td-hapus").each(function() {
            // Remove
            $(this).remove();
        });

        // Delete column header hapus
        $(".th-hapus").remove();

        // Enable peint
        $(".btn-custom-print").prop("disabled", false);
        $("#btn-save").prop("disabled", true);
        
    }
});

$('#btn-ulang').on('click', function () {
    location.reload();
})

$('#filter_instalasi').change(function () {
    $('#data-instalasi_id').val($(this).val());
})

$('#filter_ruangan').change(function () {
    $('#data-ruangan_id').val($(this).val());
    $('#dd-no-rekam-medik').val('');
})