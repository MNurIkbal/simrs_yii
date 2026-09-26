var table;
$('input[name="RekonsiliasiObatForm[is_alergi]"]').on('change', function(){
    if($(this).val() == '2'){
        $('.alergi_obat').attr('readonly', false);
    }else{
        $('.alergi_obat').val('').attr('readonly', true);
    }
})

$('input[name="RekonsiliasiObatForm[sumber_informasi]"]').on('change', function(){
    if($(this).val() != '0'){
        $('.sumber-hubungan').attr('readonly', false);
    }else{
        $('.sumber-hubungan').val('').attr('readonly', true);
    }
})

if (hideSimpan == 1) {
    $("#simpan_rekon_obat").addClass("hidden");
}

if (hideLayak == 1) {
    $("#simpan_kelayakan_obat").addClass("hidden");
}

if (hideVerif == 1) {
    $("#simpan_keputusan").addClass("hidden");
}

if (hidePrint == 1) {
    $(".print_rekon").addClass("hidden");
}

// compare qty 
$(document).on('keyup', '.layak', function(e) {
    e.preventDefault();
    var id = $(this).data('rekon_id');
    var qty = parseInt($('.qty-obat-'+id).html());
    var qtylayak = $(this).val();

    if (qtylayak > qty) {
        qtylayak = qty;
    }
    $(this).val(qtylayak);
    $('input[name="'+id+'-qty_tidaklayak"]').val(qty - qtylayak);
});
$(document).on('keyup', '.tidaklayak', function(e) {
    e.preventDefault();
    var id = $(this).data('rekon_id');
    var qty = parseInt($('.qty-obat-'+id).html());
    var qtytidaklayak = $(this).val();

    if (qtytidaklayak > qty) {
        qtytidaklayak = qty;
    }
    $(this).val(qtytidaklayak);
    $('input[name="'+id+'-qty_layak"]').val(qty - qtytidaklayak);
});

$('#ajax-form').on('submit', function(e){
    e.preventDefault();

    var _value = $(this).serializeArray();
    $(this).docoForm("submit", {
        data: _value,
        skipConfirm: true,
        success: function (response) {
            table.draw();
            $('#obatalkes_id').val('').trigger('change');
            $('#rekonsiliasiobatform-qty_obat').val('');
            $('#rekonsiliasiobatform-satuan_kecil').val('');
            $('#signa').val('').trigger('change');
            $('#rekonsiliasiobatform-rute_obat').val('');
            $('#rekonsiliasiobatform-waktu_pemberian').val('');

            $('#simpan_rekon_obat').removeClass('hidden');
        }
    });
})

$(document).on('ready', function(){
    table.draw();
    
})

$('#simpan_rekon_obat').on('click', function(e) {
    e.preventDefault();
    var header = 'Perhatian !';
    var message = 'Apakah anda yakin untuk menyimpan data ini ?';
    var label = { 
        buttons: {
            'No': 'btn btn-danger button-no',
            'Yes': 'btn btn-success button-yes'
        }
    };
    $.showQuestionDialog(header, message, label, function(reaction) {
        if (reaction == 'Yes') {
            hideQuestionDialog();

            $.ajax({
                url: '/ranap/pemeriksaan-rawat-inap/save-rekon',
                data: {
                    pend_id: $('#rekonsiliasiobatform-pendaftaran_id').val(),
                    admisi_id: $('#rekonsiliasiobatform-pasienadmisi_id').val(),
                },
                type: 'POST',
                success: function(data, status, xhr) {
                    new PNotify({
                        title: 'Berhasil',
                        text: 'Data berhasil disimpan',
                        addclass: 'alert alert-success alert-arrow-right alert-styled-right',
                        type: 'success'
                    });
                    table.draw();

                    $('.panel-inputobat').hide();
                    $("#simpan_rekon_obat").addClass("hidden");
                    $("#simpan_keputusan").removeClass("hidden");
                }
            });
        } else {
            // btn.button('reset');
            // docoHelper.listen = false;
            $('[data-popup="tooltip"]').tooltip();
        }
    });
});

$('#simpan_keputusan').click(function(e) {
    e.preventDefault();

    var data = $('#list-form').serializeArray();
    // console.log(data);
    // return;
    var header = 'Perhatian !';
    var message = 'Apakah anda yakin untuk menyimpan semua keputusan dokter ?';
    var label = { 
        buttons: {
            'No': 'btn btn-danger button-no',
            'Yes': 'btn btn-success button-yes'
        }
    };
    $.showQuestionDialog(header, message, label, function(reaction) {
        if (reaction == 'Yes') {
            hideQuestionDialog();

            var form_lanjut = $('.is_lanjut').serializeArray();
            var error = false;
            $.each(form_lanjut, function(i, val){
                if (!form_lanjut[i].value) {
                    error = true;
                }
            }); 
            if (error) {
                new PNotify({
                    title: 'Error',
                    text: 'Terjadi Kesalahan, Silahkan cek inputan',
                    addclass: 'alert alert-danger alert-arrow-right alert-styled-right',
                    type: 'error'
                });
                return;
            }


            $.ajax({
                url: '/ranap/pemeriksaan-rawat-inap/save-keputusan-rekon',
                data: {
                    data:data,
                    pend_id: $('#rekonsiliasiobatform-pendaftaran_id').val(),
                    admisi_id: $('#rekonsiliasiobatform-pasienadmisi_id').val(),
                },
                type: 'POST',
                success: function(data, status, xhr) {
                    new PNotify({
                        title: 'Berhasil',
                        text: 'Data berhasil disimpan',
                        addclass: 'alert alert-success alert-arrow-right alert-styled-right',
                        type: 'success'
                    });
                    table.draw();
                    $("#simpan_keputusan").addClass("hidden");
                    $("#simpan_kelayakan_obat").removeClass("hidden");

                }
            });
        } else {
            // btn.button('reset');
            // docoHelper.listen = false;
            $('[data-popup="tooltip"]').tooltip();
        }
    });
});

$('#simpan_kelayakan_obat').click(function(e) {
    e.preventDefault();

    var data = $('#list-form').serializeArray();
    // console.log($('#rekonsiliasiobatform-pendaftaran_id').val());
    // return;
    var header = 'Perhatian !';
    var message = 'Apakah anda yakin untuk menyimpan semua kelayakan ?';
    var label = { 
        buttons: {
            'No': 'btn btn-danger button-no',
            'Yes': 'btn btn-success button-yes'
        }
    };
    $.showQuestionDialog(header, message, label, function(reaction) {
        if (reaction == 'Yes') {
            hideQuestionDialog();


            var kelayakan = $('.input-kelayakan').serializeArray();
            var error = false;
            $.each(kelayakan, function(i, val){
                if (!kelayakan[i].value) {
                    error = true;
                }
            }); 
            if (error) {
                new PNotify({
                    title: 'Proses Gagal!',
                    text: 'Terjadi Kesalahan, Silahkan cek inputan',
                    addclass: 'alert alert-danger alert-arrow-right alert-styled-right',
                    type: 'error'
                });
                return;
            }


            $.ajax({
                url: '/ranap/pemeriksaan-rawat-inap/save-kelayakan-rekon',
                data: {
                    data:data,
                    pend_id: $('#rekonsiliasiobatform-pendaftaran_id').val(),
                    admisi_id: $('#rekonsiliasiobatform-pasienadmisi_id').val(),
                },
                type: 'POST',
                success: function(data, status, xhr) {
                    new PNotify({
                        title: 'Berhasil',
                        text: 'Data berhasil disimpan',
                        addclass: 'alert alert-success alert-arrow-right alert-styled-right',
                        type: 'success'
                    });
                    table.draw();
                    $("#simpan_kelayakan_obat").addClass("hidden");
                }
            });
        } else {
            // btn.button('reset');
            // docoHelper.listen = false;
            $('[data-popup="tooltip"]').tooltip();
        }
    });
});


$('.ulang').click(function(e) {
    e.preventDefault();
    $('#obatalkes_id').val('').trigger('change');
    $('#rekonsiliasiobatform-qty_obat').val('');
    $('#rekonsiliasiobatform-satuan_kecil').val('');
    $('#signa').val('').trigger('change');
    $('#rekonsiliasiobatform-rute_obat').val('');
    $('#rekonsiliasiobatform-waktu_pemberian').val('');
});

table = $("#tb_rekonobat").docoTabel({
    filter: false,
    pageLength: 20,
    // lengthMenu: [5, 10, 25, 100],
    bLengthChange: false,
    serverSide: true,
    stateSave: true,
    processing: true,
    scrollX: true,
    ajax: baseUrl + "ranap/pemeriksaan-rawat-inap/get-list-rekonsiliasi",
    columns: [
        {
            // title: "No",
            data: "rowNum",
            searchable: false,
            orderable: false
        },
        {
            // // title: "Nama obat alkes",
            data: "nama_obat",
            searchable: false,
            orderable: false
        },
        {
            // // title: "Jumlah",
            data: "qty_obat",
            name: "qty_obat",
            searchable: false,
            orderable: false
        }, 
        {
            // title: "Satuan pemesanan",
            data: "satuan_kecil",
            searchable: false,
            orderable: false,
            class: "text-center"
        }, 
        {
            // title: "Satuan pemesanan",
            data: "signa",
            searchable: false,
            orderable: false,
            // class: "text-center"
        },
        {
            // title: "Qty",
            data: "rute_obat",
            name: "qty_besar",
            searchable: false,
            orderable: false
        },
        {
            // title: "Satuan besar",
            data: "waktu_pemberian",
            searchable: false,
            orderable: false,
            // class: "text-center"
        },
        {
            // title: "Qty",
            data: "is_lanjut",
            name: "is_lanjut",
            searchable: false,
            orderable: false,
            class: "text-center"
        },
        {
            // title: "Satuan kecil",
            data: "catatan",
            searchable: false,
            orderable: false,
            class: "text-center"
        },
        {
            // title: "Satuan kecil",
            data: "pemberi_keputusan",
            searchable: false,
            orderable: false,
            class: "text-center"
        },
        {
            // title: "Satuan kecil",
            data: "qty_layak",
            searchable: false,
            orderable: false,
            class: "text-center"
        },
        {
            // title: "Satuan kecil",
            data: "qty_tidaklayak",
            searchable: false,
            orderable: false,
            class: "text-center"
        },
        {
            // title: "Satuan kecil",
            data: "terapi",
            searchable: false,
            orderable: false,
            class: "text-center"
        },
        {
            // title: "Satuan kecil",
            data: "signaterapi",
            searchable: false,
            orderable: false,
            class: "text-center"
        },
        {
            // title: "Satuan kecil",
            data: "rute_kelayakan",
            searchable: false,
            orderable: false,
            class: "text-center"
        },
        {
            // title: "Satuan kecil",
            data: "review_kelayakan",
            searchable: false,
            orderable: false,
            class: "text-center"
        },
    ],
    fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
        // console.log(aData.rekonsiliasiobat_id);
        var rekonsiliasiobat_id = aData.rekonsiliasiobat_id;

        if (!rekonsiliasiobat_id) {
            // Set color
            $("td", nRow).css("background-color", "#fdfd96");
        }
    }
});