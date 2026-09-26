/*
* @Author: rizfardi@docotel.com
* @Date:   2018-04-11 11:35:00
* @Last Modified by:   Sigit
* @Last Modified time: 2019-02-20 12:09:17
*/

// var tabel_reseptur = $("#tabel-reseptur");

// mencegah karakter lain selain angka desimal
$(document).on('input', '.doco-decimal', function() {
    match = (/(\d{0,9})[^.]*((?:\.\d{0,9})?)/g).exec(this.value.replace(/[^\d.]/g, ''));
    this.value = match[1] + match[2];
  });

$(".docoDate").pickadate({
    applyClass: "bg-slate-600",
    cancelClass: "btn-default",
    locale: {
        format: "DD-MMMM-YYYY"
    }
});

// delete data append
$(document).on("click", ".btn-deletes", function(e) {
    $('#section-racikan').find('.child-'+$(this).attr('data-iteration')).remove();
});

function clearForm(form) {
  $(':input', form).each(function() {
    var type = this.type;
    var tag = this.tagName.toLowerCase();
    if (type == 'text' || type == 'password' || tag == 'textarea' )
      this.value = "";
    else if (type == 'checkbox' || type == 'radio')
      this.checked = false;
    else if (tag == 'select')
      this.selectedIndex = -1;
  });
};

// delete added reseptur
$(document).on("click", ".delete-reseptur", function (e) {
    $(this).docoForm("delete", {
        success : function(data) {
            tabel_reseptur.clear();
            tabel_reseptur.draw();
        }
    });
});


$('#btn-reseptur-back').bind('click' , () => {
    $('#tab-cppt').trigger('click')
})

$(document).on('click', '#btn-resetAll', function(){
    resetAll()
    $.ajax({
        url: $(this).attr('data-url'),
        success: function(){
            tabel_reseptur.ajax.url(baseUrl+"rajal/pemeriksaan/get-data-reseptur-session?pendaftaran_id="+pendaftaran_id+"").draw();
            $(document).ready(function() {
                // $("#resepturdetailform-obatalkes_id-0").prop("disabled",true);
            });
        }
    })
});

function resetAll(){
    resetForm($('#form-racikan'));
    resetForm($('#form-nonracikan'));
    $('#select_ruangan').val(default_depo).trigger('change')
    $('#reseptur_iter').val('')
    $('#qty_nonracikan_id').val('')
    $('#form-racikan').find('.child').remove();
    $(".satuandefault_nama_0").html("");
    $(".satuandefault_nama").html("");
    $("#stok_sisa").val(0);
}

// reset form
function resetForm($form) {
    $form.find('input:text, input:password, input:file, select, textarea').val('');
    $form.find('input:radio, input:checkbox')
         .removeAttr('checked').removeAttr('selected');
    $('.select2').val(null).trigger('change');
    $(document).ready(function() {
        $("#stok_sisa_nr").val(0);
        $(".satuandefault_nama_nr").html("");
    });
}

$('.pilih-template').on('click', function() {
    let temp_id = $('#select_template').val();
    let pendaftaran_id = $('#pendaftaran_id').val();
    let depo_id = $('#select_ruangan').val();
    
    if (typeof depo_id == 'undefined' || depo_id == "" || depo_id == 'null' || depo_id == null) {
        docoNotification('warning', 'Terjadi Kesalahan', 'Depo Tujuan belum di pilih');
        return false;
    }
    if (temp_id == '') {
        docoNotification('warning', 'Terjadi Kesalahan', 'Template resep belum di pilih');
        return false;
    }
    showLoader()
    
    $.ajax({
        url: '/rajal/pemeriksaan/generate-template?idResep=' + temp_id + '&id=' + pendaftaran_id + '&depo_id=' + depo_id,
        type: 'GET',
        success: function() {
            tabel_reseptur.ajax.url(baseUrl+"rajal/pemeriksaan/get-data-reseptur-session?pendaftaran_id="+pendaftaran_id+"").draw();
        },
        error: function(res) {
            const response = res.responseJSON.response;
            const title = (typeof response == 'undefined') ? 'Terjadi Kesalahan Sistem' : response.title;
            const text = (typeof response == 'undefined') ? res.statusText : response.text;

            docoNotification('error', title, text);
        },
        complete: function() {
            hideQuestionDialog();
            $('body').find('.confirm-dialog-overlay').remove();
            docoHelper.listen = false;
            hideLoader()
        }
    });
});

function appendHistoryObat(object, type) {
    $("#list-history").html("");
    let _no = 0;
    let _html = "";
    $(".default-value").attr("style", "display:none");
    $.each(object, function (x, y) {
        if (typeof object[x] !== "undefined" && type != 1) {
            _no++;
            _html += "<tr class=\"resep\">";
            _html += "<td style='display:none;'><input type='hidden' class='list_barang' data-id='' value='" + y.obatalkes_id + "'></td>";
            _html += "<td>" + y.rowNum + "</td>";
            _html += "<td>" + y.nama_racikan + "</td>";
            _html += "<td>" + y.rke + "</td>";
            _html += "<td>" + y.obatalkes_nama + "</td>";
            _html += "<td>" + y.satuankecil_nama + "</td>";
            _html += "<td>" + y.signa_edit + "</td>";
            _html += "<td>" + y.qty_edit + "</td>";
            _html += "<td id=\"harga" + y.resepturdetail_id + "\">Rp. " + docoHelper.convertToRupiah(y.hargasatuan_reseptur) + "</td>";
            _html += "<td id=\"total-resep" + y.resepturdetail_id + "\">Rp. " + docoHelper.convertToRupiah(y.jumlah_harga) + "</td>";
            _html += "<td>" + y.etiket + "</td>";
            _html += "<td>" + y.aksi + "</td>";
            _html += "</tr>";
        }
        else {
            _no++;
            _html += "<tr class=\"resep\">";
            _html += "<td style='display:none;'><input type='hidden' class='list_barang' data-id='' value='" + y.obatalkes_id + "'></td>";
            _html += "<td>" + y.rowNum + "</td>";
            _html += "<td>" + y.nama_racikan + "</td>";
            _html += "<td>" + y.rke + "</td>";
            _html += "<td>" + y.obatalkes_nama + "</td>";
            _html += "<td>" + y.satuankecil_nama + "</td>";
            _html += "<td>" + y.signa_edit + "</td>";
            _html += "<td>" + y.qty_edit + "</td>";
            _html += "<td id=\"harga" + y.resepturdetail_id + "\">Rp. " + docoHelper.convertToRupiah(y.hargasatuan_reseptur) + "</td>";
            _html += "<td id=\"total-resep" + y.resepturdetail_id + "\">Rp. " + docoHelper.convertToRupiah(y.jumlah_harga) + "</td>";
            _html += "<td>" + y.etiket + "</td>";
            _html += "</tr>";
        }
        object[x] = y;

    });

    if (_html === "") {
        _html += "<tr>";
        _html += "<td colspan=\"10\" id=\"data-null\" class=\"text-center\">Data Tidak Ditemukan</td>";
        _html += "</tr>";
    }
    $("#list-history").prepend(_html);
}

$('#racikan').hide();
$('#non_racikan').hide();
$(".jenis_racikan").on('change', function(){
    let jenis = $(this).val();
    if (jenis == 0) {
        // racikan
        $('#racikan').show();
        $("#stok_sisa_0").val(0).trigger('change');
        $('#non_racikan').hide();
    }
    if (jenis == 1) {
        $('#non_racikan').show();
        $("#stok_sisa").val(0).trigger('change');
        $(".satuandefault_nama").html("");
        $('#racikan').hide();
        $('.child').remove();
    }
});

// $(document).on("keyup", '.qty_validate', function () {
//     let id = $(this).attr('data-id');
//     let qty = $(this).val();
//     if (parseInt(qty) == 0) {
//         qty = 1;
//         $(this).val(qty);
//     }
//     $(this).val(qty);
// })

$(document).on('change', '#select_ruangan', function () {
    let id_depo = $(this).val();
    $('#nr_depo_id').val(id_depo);
    $('#r_depo_id').val(id_depo);
});

function getDetailListObat(id, type, state = false) {
    var uri = "";
    if(state){
        uri = "&state=true";
    }
    $.ajax({
        type: "GET",
        url: "/rajal/pemeriksaan/get-data-detail-riwayat-reseptur?type="+type+"&reseptur_id="+id+uri,
        dataType: "JSON",
        success: function (res) {
            appendHistoryObat(res, type);
        },
    });
}


var tabel_riwayat_reseptur = $("#tabel-riwayatreseptur").docoTabel({
    scrollX: true,
    filter: false,
    sorting: [[1, "asc"]],
    processing: true,
    serverSide: true,
    paging: false,
    autoWidth: false,
    fnDrawCallback: function(oSettings){
        resetAll()
    },
    ajax: baseUrl+"rajal/pemeriksaan/get-data-riwayat-reseptur?pendaftaran_id=" + pendaftaran_id,
    columns: [
        {title: "Tanggal", data: "tglreseptur"},
        {title: "No reseptur", data: "noresep"},
        {title: "Status", data: "status_reseptur"},
        {
            title: "Aksi",
            data: "aksi",
            searchable: false,
            orderable: false,
        },
    ],
});

function simpanReseptur(thisSimpan) {
    var depo_id = $("#select_ruangan").val();
    
    if(depo_id == "") {
        docoNotification("warning", "Peringatan", "Depo Tujuan Belum di Pilih!");
        return false;
    } else {
        let data_depo = $("#select_ruangan");
        let data = new FormData();
        let signa = $('.signa_edit').get();
        let qty = $('.qty_add').get();
        for (let i = 0; i < signa.length; i++) {
            data.append(signa[i].name, signa[i].value);
        }
        for (let i = 0; i < qty.length; i++) {
            data.append(qty[i].name, qty[i].value);
        }
        
        let data_iter = $("#reseptur_iter");
        let data_general = $.merge( data_depo, data_iter );
        let general = data_general.serializeArray();
        general.push({
            name: 'diagnosa_id',
            value: $('#diagnosa_id').val()
        });
        $.each(general, function (key, value) {
            data.append(value.name, value.value);
        });

        $(thisSimpan).docoForm("click", {
            data: data,
            dataType: false,
            cache: false,
            contentType: false,
            processData: false,
            method: 'post',
            isUpload: true,
            success : function(data) {
                tabel_reseptur.clear();
                tabel_reseptur.ajax.url(baseUrl+"rajal/pemeriksaan/get-data-reseptur-session?pendaftaran_id="+pendaftaran_id+"").draw();
                tabel_riwayat_reseptur.draw();
                $('.jenis_racikan').removeAttr('checked');
                $('#racikan').hide();
                $('#non_racikan').hide();
            }
        });
    }
}