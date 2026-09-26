window.list_obat = [];
var _group = {};
var apotek =  { list_stok: {} };

$('.autoObat').docoPaginationSelec2(
    config = {
        placeholder : 'Pilih Obat ... ',
        _api : '/igd/pemeriksaan-igd/list-obat-alkes-depo',
        ajax : {
            data: function(params) {
                return {
                    q: params.term, 
                    page: params.page || 1,
                    ruangan_id: $("#select_depo").val(),
                    penjamin_id: $("#penjamin_id").val(),
                }
            },
            results: function (data, page) {
                var more = (page * 30) < data.total_count;
    
                return { results: data.items, more: more };
            },
            processResults: function(res, params) {
                params.page = params.page || 1;
                var arr = [];
                $.each(res.data_stok, function(index, value) {
                    if (index < 10) {
                        var _disabled = value.qty_tersedia <= 0 ? true : false;
                        arr.push({
                            id: value.obatalkes_id,
                            text: value.obatalkes_nama,
                            disabled: _disabled
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
    }
).on('change', function(e) {
    var id = $(this).val();
    var selected = apotek.list_stok[id];
    if (typeof selected !== "undefined") {
        var _qtySedia = parseFloat(selected.qty_tersedia);
        if (typeof _tmpStok[id] !== "undefined") {
            _qtySedia -= parseFloat(_tmpStok[id]);
        }
        $("#qty_tersedia").val(_qtySedia);
        $("#stok_sisa_nr").val(_qtySedia);
        $("#satuandefault_id").val(selected.satuankecil_id);
        $("#satuandefault_nama").val(selected.satuankecil_nama);
        $("#satuankecil_nama").val(selected.satuankecil_nama);

        if(selected.satuankecil_nama == 'undefined' || selected.satuankecil_nama == null || selected.satuankecil_nama == "") {
            $('.satuandefault_nama_nr').addClass('hidden');
        } else {
            $('.satuandefault_nama_nr').removeClass('hidden');
            $(".satuandefault_nama_nr").html('<strong><i>' + selected.satuankecil_nama + '</i></strong>');
            $(".satuankecil_nama_nr").html('<strong><i>' + $("#satuandefault_nama").val() + '</i></strong>');
        }

        $("#harga").val(selected.hargajual);
        $("#harga_jual").val(selected.hargajual);
        $("#harganetto").val(selected.harganetto);
        $("#harga_reseptur_nr").val(selected.hargaygdipakai !== null ? selected.hargaygdipakai : 0);
        $('.jumlah_konversi').addClass('hidden');
        $("#qty_nonracikan_id").val(0);
    } else {
        $("#satuankecil_id").val("").trigger("change");
        $("#satuankecil_id").prop("disabled", true);
        $('.satuandefault_nama_nr').addClass('hidden');
        $('.jumlah_konversi').addClass('hidden');
        $("#stok_sisa_nr").val(0);
    }
})

$("#satuankecil_id").select2();
$('#satuankecil_id').on('change', function(){
    if(typeof _group == 'undefined') {
        _group = {};
    }

    var _val = $(this).val();
    var _stok_default = $("#qty_tersedia").val();
    var _satuan_kecil = $("#satuankecil_id").select2("data");
    var _harga_satuan = $("#harga").val();
    var _nilai_konversi = _group[_val];
    var _konversi_stok = _stok_default/_nilai_konversi;
    var _konversi_harga = _harga_satuan*_nilai_konversi;
    var lastText = '';

    if(_satuan_kecil.length !== 0) {
        lastText = _satuan_kecil[0].text;
    }

    $("#harga_konversi").val(_konversi_harga);
    $("#nilai_konversi").val(_nilai_konversi);
    $("#satuankecil_nama").val(lastText);
    $('.satuandefault_nama_nr').removeClass('hidden');
    $(".satuandefault_nama_nr").html('<strong><i>' + lastText + '</i></strong>');
    $(".satuankecil_nama_nr").html('<strong><i>' + $("#satuandefault_nama").val() + '</i></strong>');
    $("#stok_sisa_nr").val(docoHelper.convertToRupiah(_konversi_stok.toFixed(2)));
    $("#harga_reseptur_nr").val(docoHelper.convertToRupiah(_konversi_harga));

    $('#qty_nonracikan_id').val(0);
    $(".konversi").val("");
    $('.jumlah_konversi').addClass('hidden');
});

$('#qty_nonracikan_id').on('change', function(){
    var _qty = docoHelper.convertToAngka($(this).val());
    var _nilai_konversi = $("#nilai_konversi").val();
    var _konversi = _qty * _nilai_konversi;
    var _satuankecil = $('#satuandefault_nama').val();
    var _stokTersedia = docoHelper.convertToAngka($("#stok_sisa_nr").val());
    var _satuanOrder = $("#satuankecil_nama").val();
    var _keterangan = '';
    if(_satuankecil !== _satuanOrder) {
        _keterangan = '<strong><i> ( ' + _qty + ' ' + _satuanOrder + ' ) </i></strong>';
    }

    if(_konversi > (_stokTersedia * _nilai_konversi)) {
        docoNotification('warning', 'Peringatan', 'Maaf, Stok Tersedia tidak mencukupi.');
        $(this).val(0);
        $('#qty_nonracikan_id').val(0);
        $(".konversi").val("");
        $('.jumlah_konversi').addClass('hidden');
        $('.save-non-racikan').prop('disabled', true);
    }
    else {
        $(".jumlah_konversi").removeClass('hidden');
        $(".konversi").val(docoHelper.convertToRupiah(_konversi.toFixed(2)));
        $('.save-non-racikan').prop('disabled', false);
    }
});

function submitNonracikan() {
    var arrData = [];
    $.each($('#form-nonracikan').serializeArray(), function(k,v){
        arrData[k] = v;
        if(k == 6){
            arrData[k].value = docoHelper.convertToAngka(v.value);
        }
    });
    arrData.push({
        name: 'iter',
        value: $('#reseptur_iter').val()
    });
    $.ajax({
        url: $('#form-nonracikan').attr('action'),
        method: "POST",
        dataType: "json",
        data: arrData,
        success : function(data) {
            resetAll();
            tabel_reseptur.clear();
            tabel_reseptur.ajax.url(baseUrl+"igd/pemeriksaan-igd/get-data-reseptur-session?id="+pendaftaran_id+"&cppt_id="+cppt_id+"&ruangan_id="+$("#select_depo").val()+"&isEditReseptur="+ isEditReseptur +"&instruksi_id=" + instruksi_id + "&is_submit=1" + "").draw();
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

function resetAll() {
    var formNonRacikan = $('#form-nonracikan');
    formNonRacikan[0].reset();
    $('#obatalkes_id').val('').trigger('change');
}
