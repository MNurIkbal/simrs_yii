
window.list_obat = [];
var apotek =  { list_stok: {} };
var form = false;

// mencegah karakter lain selain angka desimal
$(document).on('input', '.doco-decimal', function() {
    match = (/(\d{0,9})[^.]*((?:\.\d{0,9})?)/g).exec(this.value.replace(/[^\d.]/g, ''));
    this.value = match[1] + match[2];
});

function errorTemplate(data, status, error) {
    form = false;
    docoHelper.listen = false;
    $('html, body').animate({
        scrollTop: 0
    }, 'slow');
    var errMsg = 'Terjadi kesalahan, silahkan cek inputan.';
    var errTitle = 'Proses Gagal !';
    // extend message from backend : Ali
    $('.help-block.error').remove();
    $('.has-error').removeClass('has-error');
    try {
        var error = data.responseJSON.response;
        var sttsErr = data.status;
        var extMessage = (error.message) ? ' , ' + error.message : '';
        if (sttsErr == '422') {
            var logo = '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> &nbsp';
            if (error.text) {
                errMsg = error.text;
            }
            // Setting Error title
            if (error.title) {
                errTitle = error.title;
            }
            // Parsing Error;
            $.each(error.data, function (key, val) {
                var _field = $('[name="' + key + '"]');
                var _div = $('.error_' + key);
                var _getId = _field.attr('id');
                var _group = _field.closest('div.input-group');
                var _selectize = _field.closest('.form-group').find('div.selectize-control');
                var _select2 = _field.closest('div').find('.select2-container');
                // Menambahkan class Error pada form-group
                _field.parent('div').addClass('has-error');
                _field.parent('.required').addClass('has-error');
                $('.field-' + _getId).addClass('has-error');
                // Cara kedua menempelkan manual error pada form
                var replaceKey = key.replace(/[\[\]\'\!]/g, "");
                var manualErr = $('#error_' + replaceKey);

                if (manualErr.length) {
                    manualErr.html('<span class="help-block error">' + logo + val[0] + '</span>');
                } else {
                    if (_group.length) {
                        _group.after('<span class="help-block error">' + logo + val[0] + '</span>');
                    } else if (_selectize.length) {
                        _selectize.after('<span class="help-block error">' + logo + val[0] + '</span>');
                    } else if (_select2.length) {
                        _select2.after('<span class="help-block error">' + logo + val[0] + '</span>');
                    } else if (_div.length) {
                        _div.after('<span class="help-block error">' + logo + val[0] + '</span>');
                    } else {
                        _field.after('<span class="help-block error">' + logo + val[0] + '</span>');
                    }
                }
            });
        } else {
            errMsg = 'Terjadi kesalahan pada sistem ' + extMessage;
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

function resetNonRacikan(){
    var form = $("#form-nonracikan");
    form[0].reset();
    $('#obatalkes_id').val('').trigger('change');
}

function addTemp() {
    var arrData = [];
    var hargasatuan_ = parseInt($('#harga_reseptur').val());

    no = 0
    $.each($('#form-nonracikan').serializeArray(), function(k,v){
        arrData[no] = v;
        let oa = $(`select[name="${v.name}"]`).hasClass('obatalkes');
        if (oa) {
            var textSelected = $(`select[name="${v.name}"]`).select2('data')
            if (typeof textSelected[0] !== "undefined") {
                no++
                var oaId = $(`select[name="${v.name}"]`).attr('id');
                var oaKey = oaId.match(/(\d+)/);
                arrData[no] = {
                    name: `ResepturNrDetailForm[obatalkes_nama]`,
                    value: textSelected[0].text
                }
            }
        }
        if(k == 6){
            arrData[no].value = docoHelper.convertToAngka(v.value);
        }
        no++
    });

    arrData.push({
        name: 'iter',
        value: $('#reseptur_iter').val()
    });

    // if(hargasatuan_ < 1){
    //     docoNotification("warning", i18next.t("Perhatian"), i18next.t("Harga Satuan Obat Tidak Boleh 0"));
    //     form = false;
    // }else {
        $.ajax({
            url: $('#form-nonracikan').attr('action'),
            type: 'post',
            data: arrData,
            success: function (data) {
            tabel_reseptur.clear();
            tabel_reseptur.ajax.url(baseUrl+"rajal/pemeriksaan/get-data-reseptur-session?pendaftaran_id="+pendaftaran_id+"").draw();
                resetNonRacikan();
            },
            error: function (data, status, error) {
                errorTemplate(data, status, error);
            },
        })
    // }
}

$(document).ready(function(){
    $('#obatalkes_id').on('depdrop:afterChange', function(event, id, value, jqXHR, textStatus) {
        let ajaxResults = $('#obatalkes_id').depdrop('getAjaxResults');
        list_obat = ajaxResults['output'];
    });

    $('#obatalkes_id').docoPaginationSelec2(
        config = {
            placeholder : 'Pilih Obat ... ',
            _api : '/rajal/allow/list-obat-alkes-depo',
            ajax : {
                data: function(params) {
                    return {
                        q: params.term, 
                        page: params.page || 1,
                        ruangan_id: $("#select_ruangan").val(),
                        penjamin_id: $("#penjamin_id").val(),
                        kelaspelayanan_id: kelaspelayanan_id,
                    }
                },
                results: function (data, params) {
                    var more = (params.page * 30) < data.total_count;
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
            _stok_sisa = parseFloat(selected.qty_tersedia).toFixed(2);
            if (typeof _tmpStok[id] !== "undefined") {
                _stok_sisa -= parseFloat(_tmpStok[id]);
            }

            _hargajual = parseFloat(selected.hargaygdipakai !== null ? selected.hargaygdipakai : 0).toFixed(2);
            _harganetto = parseFloat(selected.harganetto).toFixed(2);
            _satuankecil_nama = selected.satuankecil_nama;
            _satuankecil_id = selected.satuankecil_id;

            $("#stok_tersedia").val(_stok_sisa);
            $("#stok_sisa").val(_stok_sisa);
            $("#satuandefault_id").val(_satuankecil_id);
            $("#satuankecil_id").val(_satuankecil_id);
            $("#satuandefault_nama").val(_satuankecil_nama);
            $("#satuankecil_nama").val(_satuankecil_nama);
            $(".satuandefault_nama").html('<strong><i>' + _satuankecil_nama + '</i></strong>');
            
            $("#harga").val(_hargajual);
            $("#harga_konversi").val(_hargajual);
            $("#harga_reseptur").val(docoHelper.convertToRupiah(_hargajual));
            $("#harganetto_reseptur").val(_harganetto);
            
            clearChange();
        }
        else {
            $("#satuankecil_id").val("").trigger("change");
            $("#satuankecil_id").prop("disabled", true);
            $('.satuandefault_nama').html('');
            $('.konversi').html('');
            $("#stok_sisa").val(0);
            $("#signa_id").val("").trigger("change");
        }
    });

    $("#satuankecil_id").select2();
    $('#satuankecil_id').on('change', function(){
        if(typeof _group == 'undefined') {
            _group = {};
        }

        var _val = $(this).val();
        var _stok_default = $("#stok_tersedia").val();
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
        $('.satuandefault_nama').removeClass('hidden');
        $(".satuandefault_nama").html('<strong><i>' + lastText + '</i></strong>');
        $("#stok_sisa").val(_konversi_stok.toFixed(2));
        $("#harga_reseptur").val(docoHelper.convertToRupiah(_konversi_harga));
        clearChange();
    });

    $('#qty_reseptur').on('change', function(){
        var _qty = parseFloat($(this).val()).toFixed(2);
        var _nilai_konversi = $("#nilai_konversi").val();
        var _konversi = _qty * _nilai_konversi;
        var _satuankecil = $('#satuandefault_nama').val();
        var _stokTersedia = $("#stok_sisa").val();
        var _stokTersediaConvert = parseFloat(_stokTersedia).toFixed(2);
        var _satuanOrder = $("#satuankecil_nama").val();
        var _keterangan = '';
        if(_satuankecil !== _satuanOrder) {
            _keterangan = '<strong><i> (' + _qty + ' ' + _satuanOrder + ') </i></strong>';
        }
        
        if(parseInt(_qty) > parseInt(_stokTersediaConvert)) {
            docoNotification('warning', 'Peringatan', 'Maaf, Stok Tersedia tidak mencukupi.');
            $(this).val(0);
            clearChange();
        }
        else {
            $(".konversi").html(_konversi + ' ' + _satuankecil + _keterangan);
            $('.save-non-racikan').prop('disabled', false);
            $(this).val(_qty);
        }
    });

    function clearChange () {
        $('#qty_reseptur').val(0);
        $(".konversi").html("");
        $("#signa_id").val("").trigger("change");
        $('.save-non-racikan').prop('disabled', true);
    }
})