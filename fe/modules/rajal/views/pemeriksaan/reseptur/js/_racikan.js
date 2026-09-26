window.iteration = 0;
window.list_obat = [];
var apotek =  { list_stok: {} };
var config = {
    placeholder : 'Pilih Obat ... ',      // custom placeholder (optional) default null
    _api : '/rajal/allow/list-obat-alkes-depo',   // get data
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

var _changeObat = function () {
    var id = $(this).val();
    var selected = apotek.list_stok[id];
    var _parent = $(this).closest('div.child');
    var _stok_sisa = 0;
    var _hargajual = 0;
    var _harganetto = 0;
    var _harga = 0;
    var _nilai_konversi = 0;
    var _stok = 0;
    var _harga_satuan = 0;
    var _konversi_stok = 0;
    var _konversi_harga = 0;
    var _satuankecil_nama = '';
    var _satuankecil_id = '';

    if(_parent.length !== 0) {
        var iterasi = _parent.attr('class');
        var regex = /child-[0-9 -()+]+$/;
        if(iter = regex.exec(iterasi)) {
            iter = iter[0].substring(6);
        }
        if (typeof selected !== "undefined") {
            _stok_sisa = parseFloat(selected.qty_tersedia).toFixed(2);
            if (typeof _tmpStok[id] !== "undefined") {
                _stok_sisa -= parseFloat(_tmpStok[id]);
            }

            _hargajual = parseFloat(selected.hargaygdipakai !== null ? selected.hargaygdipakai : 0).toFixed(2);
            _harganetto = parseFloat(selected.harganetto).toFixed(2);

            // _hargajual = selected.hargajual;
            // _harganetto = selected.harganetto;
            _satuankecil_nama = selected.satuankecil_nama;
            _satuankecil_id = selected.satuankecil_id;

            _parent.find('#qty_tersedia_'+iter).val(_stok_sisa);
            _parent.find('#stok_sisa_'+iter).val(_stok_sisa);
            _parent.find('#hargasatuan_reseptur_'+iter).val(docoHelper.convertToRupiah(_hargajual));
            _parent.find('#harga_konversi_'+iter).val(parseFloat(_hargajual).toFixed(2));
            _parent.find("#harganetto_reseptur_"+iter).val(parseFloat(_harganetto).toFixed(2));
            _parent.find('#harga_'+iter).val(parseFloat(_hargajual).toFixed(2));
            _parent.find('#harga_jual_'+iter).val(parseFloat(_hargajual).toFixed(2));
            _parent.find('#satuandefault_id_'+iter).val(_satuankecil_id);
            _parent.find('#satuandefault_nama_'+iter).val(_satuankecil_nama);
            _parent.find('#satuankecil_nama_'+iter).val(_satuankecil_nama);

            $(".satuandefault_nama_"+iter).html('(<strong><i> ' + _satuankecil_nama + ') </i></strong>');

            _parent.find('#satuankecil_id_'+iter).select2();
            _parent.find('#satuankecil_id_'+iter).depdrop({
                depends: ['obatalkes_id_'+iter],
                url: baseUrl +'rajal/allow/list-satuan-besar',

            }).on('depdrop:afterChange', function(event, id, value, textStatus) {
                _group = {};
                var _id = $(this).val();
                var _response = $('#satuankecil_id_'+iter).depdrop('getAjaxResults');
                
                $.each(_response.output, function (x,y) {
                  _group[y.id] = y.konversi;
                });

                _nilai_konversi = _group[_id];

                $("#nilai_konversi_"+iter).val(_nilai_konversi);
                $("#satuaninput_id_"+iter).val(_id);
                $("#satuankecil_id_"+iter).val(_id);
            });
        }
    } 
    else {
        if (typeof selected !== "undefined") {
            _stok_sisa = parseFloat(selected.qty_tersedia).toFixed(2);
            if (typeof _tmpStok[id] !== "undefined") {
                _stok_sisa -= parseFloat(_tmpStok[id]);
            }

            _hargajual = parseFloat(selected.hargaygdipakai !== null ? selected.hargaygdipakai : 0).toFixed(2);
            _harganetto = parseFloat(selected.harganetto).toFixed(2);
            _satuankecil_nama = selected.satuankecil_nama;
            _satuankecil_id = selected.satuankecil_id;

            $("#satuan_id_0").val(_satuankecil_id);
            $("#qty_tersedia_0").val(_stok_sisa);
            $("#stok_sisa_0").val(docoHelper.convertToRupiah(_stok_sisa));
            $("#satuandefault_id_0").val(_satuankecil_id);
            $("#satuankecil_id_0").val(_satuankecil_id);
            $("#satuaninput_id_0").val(_satuankecil_id);
            $("#satuandefault_nama_0").val(_satuankecil_nama);
            $("#satuankecil_nama_0").val(_satuankecil_nama);
            $(".satuandefault_nama_0").html('<strong><i> ' + _satuankecil_nama + '</i></strong>');

            $("#harga_0").val(_hargajual);
            $("#harga_jual_0").val(_hargajual);
            $("#harga_konversi_0").val(_hargajual);

            $("#hargasatuan_reseptur_0").val(docoHelper.convertToRupiah(_hargajual));
            $("#harganetto_reseptur_0").val(_harganetto);
            $('#satuandefault_id_0').val(_satuankecil_id);
            $("#qty_reseptur_0").val(0);

            // clearChange();
        }
        else {
            $("#satuankecil_id_0").val("").trigger("change");
            $("#satuankecil_id_0").prop("disabled", true);
            $("#hargasatuan_reseptur_0").val(0);
            $(".satuandefault_nama_0").html("")
            $("#signa_reseptur").val("").trigger("change");
            $("#stok_sisa_0").val(0);
            $(".konversi_0").html("");
        }
    }

    $("#satuankecil_id_0").select2();
    $("#satuankecil_id_0").on('change', _changeSatuan);
}

var _changeSatuan = function () {
    if(typeof _group == 'undefined') {
        _group = {};
    }

    var _val = $(this).val();
    var _parent = $(this).closest('div.child');

    if(_parent.length !== 0) {
        var iterasi = _parent.attr('class');
        var regex = /child-[0-9 -()+]+$/;
        if(iter = regex.exec(iterasi)) {
            iter = iter[0].substring(6);
        }

        var _nilai_konversi = _group[_val];
        var _stok_default = $("#qty_tersedia_"+iter).val();
        var _harga_satuan = $("#harga_"+iter).val();
        var _konversi_stok = _stok_default/_nilai_konversi;
        var _konversi_harga = _harga_satuan*_nilai_konversi;
        var _satuan_kecil = $('#satuankecil_id_'+iter).select2('data');
        var lastText = '';

        if(_satuan_kecil.length !== 0) {
            lastText = _satuan_kecil[0].text;
        }

        _parent.find('#satuankecil_id_'+iter).val(_val);
        _parent.find('#satuaninput_id_'+iter).val(_val);
        _parent.find('#harga_'+iter).val(_konversi_harga);
        _parent.find('#harga_jual_'+iter).val(_harga_satuan);
        _parent.find('#harga_konversi_'+iter).val(_harga_satuan);
        _parent.find('#nilai_konversi_'+iter).val(_nilai_konversi);
        _parent.find('#satuankecil_nama_'+iter).val(lastText);
        _parent.find('.satuandefault_nama_'+iter).html('<strong><i> ' + lastText + '</i></strong>');
        _parent.find('.konversi_'+iter).html('');
        _parent.find('#stok_sisa_'+iter).val(docoHelper.convertToRupiah(_konversi_stok));
        _parent.find('#hargasatuan_reseptur_'+iter).val(_konversi_harga);

        //clear
        _parent.find('#qty_reseptur_'+iter).val(0);
        _parent.find('#konversi_html_'+iter).html("");
        _parent.find('#konversi_input_'+iter).html("");
        
    }
    else {
        var _stok_default = $("#qty_tersedia_0").val();
        var _satuan_kecil = $("#satuankecil_id_0").select2("data");
        var _harga_satuan = $("#harga_0").val();
        var _nilai_konversi = _group[_val];
        var _konversi_stok = _stok_default/_nilai_konversi;
        var _konversi_harga = _harga_satuan*_nilai_konversi;
        var lastText = '';

        
        if(_satuan_kecil.length !== 0) {
            lastText = _satuan_kecil[0].text;
        }

        $("#satuankecil_id_0").val(_val);
        $("#satuaninput_id_0").val(_val);

        $("#harga_jual_0").val(_harga_satuan);
        $("#harga_konversi_0").val(_harga_satuan);
        $("#nilai_konversi_0").val(_nilai_konversi);
        $("#satuankecil_nama_0").val(lastText);
        $(".satuandefault_nama_0").html('<strong><i>' + lastText + '</i></strong>');
        $(".konversi_0").html('');
        $("#stok_sisa_0").val(docoHelper.convertToRupiah(_konversi_stok));
        $("#hargasatuan_reseptur_0").val(docoHelper.convertToRupiah(_konversi_harga));

        //clear
        $('#qty_reseptur_0').val(0);
        $("#konversi_html_0").html("");
        $("#konversi_input_0").val("");
    }
}

var _changeQty = function () {
    var _qty = parseFloat($(this).val()).toFixed(2);
    var _parent = $(this).closest('div.child');

    if(_parent.length !== 0) {
        var iterasi = _parent.attr('class');
        var regex = /child-[0-9 -()+]+$/;
        if(iter = regex.exec(iterasi)) {
            iter = iter[0].substring(6);
        }

        var _nilai_konversi = $("#nilai_konversi_"+iter).val();
        var _konversi = _qty * _nilai_konversi;
        var _satuandefault = $('#satuandefault_nama_'+iter).val();
        var _stokTersedia = $("#stok_sisa_"+iter).val();
        var _stokTersediaConvert = parseFloat(_stokTersedia).toFixed(2);
        var _satuanOrder = $("#satuankecil_nama_"+iter).val();

        if(!_satuanOrder) {
            var _keterangan = '';
        }
        else {
            if(_satuandefault !== _satuanOrder) {
               var _keterangan = '<strong><i> ( ' + _qty + ' ' + _satuanOrder + ' ) </i></strong>';
            }
            else {
                var _keterangan = '';
            }
        }

        if(parseInt(_qty) > parseInt(_stokTersediaConvert)) {
            docoNotification('warning', 'Peringatan', 'Maaf, Stok Tersedia tidak mencukupi.');
            _parent.find('#qty_reseptur_'+iter).val(0);
            _parent.find('.konversi_'+iter).html("");
            _parent.find('.save-racikan').prop('disabled', true);
        }
        else {
            _parent.find('#konversi_'+iter).val(_konversi + ' ' + _satuandefault);
            _parent.find('#konversi_'+iter).html(_konversi + ' ' + _satuandefault + _keterangan);
            _parent.find('.save-racikan').prop('disabled', false);
            $(this).val(_qty);
        }
    }
    else {
        var _nilai_konversi = $("#nilai_konversi_0").val();
        var _konversi = _qty * _nilai_konversi;
        var _satuandefault = $('#satuandefault_nama_0').val();
        var _stokTersedia = $("#stok_sisa_0").val();
        var _stokTersediaConvert = parseFloat(_stokTersedia).toFixed(2);
        var _satuanOrder = $("#satuankecil_nama_0").val();
        var _keterangan = '';

        if(_satuandefault !== _satuanOrder) {
            _keterangan = '<strong><i> ( ' + _qty + ' ' + _satuanOrder + ' ) </i></strong>';
        }

        if(parseInt(_qty) > parseInt(_stokTersediaConvert)) {
            docoNotification('warning', 'Peringatan', 'Maaf, Stok Tersedia tidak mencukupi.');
            $(this).val(0);
            $('.konversi_0').html("");
            $('.save-racikan').prop('disabled', true);
        }
        else if(_qty == "" || _qty == 0) {
            $(".konversi_0").html("");
        }
        else {
            $(".konversi_0").html(_konversi + ' ' + _satuandefault + _keterangan);
            $('.save-racikan').prop('disabled', false);
            $(this).val(_qty);
        }
    }
}

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

    $('[data-popup="tooltip"]').tooltip();
    return new PNotify({
        title: errTitle,
        text: errMsg,
        type: 'error',
    });

    // docoNotification('error', errTitle, errMsg);
}

function saveracikan() {
    var arrData = [];
    let no = 0
    $.each($('#form-racikan').serializeArray(), function(k,v){
        arrData[no] = v;
        let arr = v.name.split("hargasatuan_reseptur");
        let oa = $(`select[name="${v.name}"]`).hasClass('obatalkes');
        if (oa) {
            var textSelected = $(`select[name="${v.name}"]`).select2('data')
            if (typeof textSelected[0] !== "undefined") {
                no++
                var oaId = $(`select[name="${v.name}"]`).attr('id');
                var oaKey = oaId.match(/(\d+)/);
                arrData[no] = {
                    name: `ResepturDetailForm[obatalkes_nama][${oaKey[0]}]`,
                    value: textSelected[0].text
                }
            }
        }
        if(arr.length == 2){
            arrData[no].value = docoHelper.convertToAngka(v.value);
        }
        no++
    });
    arrData.push({
        name: 'iter',
        value: $('#reseptur_iter').val()
    });

    $.ajax({
        url: $('#form-racikan').attr('action'),
        type: 'post',
        data: arrData,
        success: function (data) {
            tabel_reseptur.clear();
            tabel_reseptur.ajax.url(baseUrl+"rajal/pemeriksaan/get-data-reseptur-session?pendaftaran_id="+pendaftaran_id+"").draw();
            resetRacikan();
            $('.child').remove();
        },
        error: function (data, status, error) {
            errorTemplate(data, status, error);
        },
    });
}

function appendRacikan() {
    var cek = [];
    $(".cek-racikan").each(function() {
        if ($(this).val() != null) {
            cek.push($(this).val());

            if ($(this).val() == "") {
                $(this).parent().parent().closest("div").addClass("has-error");
            }
            else {
                $(this).parent().parent().closest("div").removeClass("has-error");
            }
        }
        else {
            cek.push("");

            if ($(this).val() == "" || $(this).val() == null) {
                $(this).parent().parent().closest("div").addClass("has-error");
            }
            else {
                $(this).parent().parent().closest("div").removeClass("has-error");
            }
        }
    });

    if (jQuery.inArray("", cek) !== -1) {
        docoNotification('error', "Tidak bisa tambah racikan", "Ada field yang belum diisi");
    } else {
        iteration++;
        let template =
            '<div class="child child-'+iteration+'"><hr>'+
                '<input type="hidden" id="qty_tersedia_'+ iteration +'" name="ResepturDetailForm[stok_tersedia]['+iteration+']">'+
                '<input type="hidden" id="satuaninput_id_'+ iteration +'" name="ResepturDetailForm[satuaninput_id]['+iteration+']">'+
                '<input type="hidden" id="satuandefault_id_'+ iteration +'" name="ResepturDetailForm[satuandefault_id]['+iteration+']">'+
                '<input type="hidden" id="satuandefault_nama_'+ iteration +'" name="ResepturDetailForm[satuandefault_nama]['+iteration+']">'+
                '<input type="hidden" id="satuankecil_nama_'+ iteration +'" name="ResepturDetailForm[satuankecil_nama]['+iteration+']">'+
                '<input type="hidden" id="nilai_konversi_'+ iteration +'" name="ResepturDetailForm[nilai_konversi]['+iteration+']">'+
                '<input type="hidden" id="harga_'+ iteration +'" name="ResepturDetailForm[harga]['+iteration+']">'+
                '<input type="hidden" id="harga_konversi_'+ iteration +'" name="ResepturDetailForm[harga_konversi]['+iteration+']">'+
                '<input type="hidden" id="harga_satuan_'+ iteration +'" name="ResepturDetailForm[harga_satuan]['+iteration+']">'+
                '<input type="hidden" id="harga_jual_'+ iteration +'" name="ResepturDetailForm[harga_jual]['+iteration+']">'+
                '<input type="hidden" id="harganetto_reseptur_'+ iteration +'" class="form-control input-sm" name="ResepturDetailForm[harganetto]['+iteration+']" readonly="readonly">'+
                '<div class="row">'+
                    '<div class="col-md-4">'+
                        '<div class="form-group field-resepturdetailform-obatalkes_id required">'+
                            '<label class="text-right control-label" for="resepturdetailform-obatalkes_id">Obat Alkes</label>'+
                            '<select id="obatalkes_id_'+ iteration +'" class="form-control racikan_append cek-racikan obatalkes" name="ResepturDetailForm[obatalkes_id]['+iteration+']"></select>'+
                            '<div class="help-block"></div>'+
                        '</div>'+
                    '</div>'+
                    '<div class="col-md-4">'+
                        '<div class="form-group">'+
                            '<label class="text-right control-label">Stok</label>'+
                                '<input type="text" id="stok_sisa_'+ iteration +'" class="form-control input-sm" name="ResepturDetailForm[stok]['+iteration+']" readonly="readonly">'+
                                '<div class="help-block"></div>'+
                        '</div>'+
                    '</div>'+
                    '<div class="col-md-4">'+
                        '<div class="form-group field-resepturdetailform-hargasatuan_reseptur">'+
                            '<label class="text-right control-label" for="resepturdetailform-hargasatuan_reseptur">Harga Satuan</label>'+
                                '<input type="text" id="hargasatuan_reseptur_'+ iteration +'" class="form-control input-sm" name="ResepturDetailForm[hargasatuan_reseptur]['+iteration+']" readonly="readonly">'+
                                '<div class="help-block"></div>'+
                        '</div>'+
                    '</div>'+
                '</div>'+

                '<div class="row">'+
                    '<div class="col-md-4">'+
                        '<div class="form-group field-resepturdetailform-satuankecil_id required">'+
                            '<label class="text-right control-label" for="resepturdetailform-satuankecil_id">Satuan</label>'+
                                '<select id="satuankecil_id_'+ iteration +'" class="select2 form-control cek-racikan" name="ResepturDetailForm[satuankecil_id]['+iteration+']"></select>'+
                                '<div class="help-block"></div>'+
                        '</div>'+
                    '</div>'+
                    '<div class="col-md-4">'+
                        '<div class="form-group field-resepturdetailform-qty_reseptur required">'+
                            '<label class="text-right control-label" for="resepturdetailform-qty_reseptur">Qty</label>'+
                                '<div class="input-group"><input type="text" id="qty_reseptur_'+ iteration +'" class="form-control input-sm cek-racikan" name="ResepturDetailForm[qty_reseptur][' + iteration + ']">' +
                                '<span class="input-group-addon"><span id="konversi_'+iteration+'"><input type="hidden" id="konversi_'+ iteration +'" name="ResepturDetailForm[konversi]['+iteration+']"></span></span>'+
                                '<div class="help-block"></div></div>'+
                        '</div>'+
                    '</div>'+
                    '<div class="col-md-1">'+
                        '<div class="form-group" style="margin-top:18px;">'+
                            '<label class="text-right control-label"></label>'+
                                '<button type="button" class="btn btn-danger btn-sm btn-deletes" data-iteration="'+iteration+'"><i class="fa fa-trash"></i></button>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
            '</div>';

        $("#section-racikan").append(template);
        $('#obatalkes_id_'+iteration).docoPaginationSelec2(config).on('change',_changeObat);
        $("#qty_reseptur_"+iteration).on('change', _changeQty);
        $("#satuankecil_id_"+iteration).select2();
        $("#satuankecil_id_"+iteration).on('change', _changeSatuan);
    }
};

function resetRacikan() {
    var formRacikan = $('#form-racikan');
    formRacikan[0].reset();
    $('#obatalkes_id_0').val('').trigger('change');
}

$(document).ready(function(){
    $('#obatalkes_id_0').docoPaginationSelec2(config).on('change',_changeObat);
    $("#qty_reseptur_0").on('change', _changeQty);
});

// function clearChange() {
    
// }

