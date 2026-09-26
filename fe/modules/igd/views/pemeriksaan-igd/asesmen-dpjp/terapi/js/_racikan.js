
window.list_obat = [];
window.iteration = 0;
var _group = {};
var apotek =  { list_stok: {} };

function appendRacikan() {
    var cek = [];
    $(".cek-racikan").each(function() {
        if ($(this).val() != null) {
            cek.push($(this).val());

            if ($(this).val() == "") {
                $(this).parent().parent().closest("div").addClass("has-error");
            } else if (parseInt($(this).val()) == 0) {
                $(this).parent().parent().closest("div").addClass("has-error");
            }  else {
                $(this).parent().parent().closest("div").removeClass("has-error");
            }
        } else {
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
                '<div class="row">'+
                    '<div class="col-md-3">'+
                        '<div class="form-group field-resepturdetailform-obatalkes_id required">'+
                            '<label class="text-right has-star">Nama Obat</label>'+
                                '<select id="obatalkes_id_'+iteration+'" class="select2 form-control" name="ResepturDetailForm[obatalkes_id]['+iteration+']"></select>'+
                                '<div class="help-block"></div>'+
                        '</div>'+
                    '</div>'+
                    '<div class="col-md-3">'+
                        '<div class="form-group field-resepturdetailform-satuankecil_id required">'+
                            '<label class="text-right has-star" for="resepturdetailform-satuankecil_id">Satuan</label>'+
                            '<select id="satuankecil_id_'+ iteration +'" class="select2 form-control" name="ResepturDetailForm[satuankecil_id]['+iteration+']"></select>'+
                            '<div class="help-block"></div>'+
                        '</div>'+
                        '<input type="hidden" id="qty_tersedia_'+ iteration +'" name="ResepturDetailForm[stok_tersedia]['+iteration+']">'+
                        '<input type="hidden" id="satuan_id_'+ iteration +'" name="ResepturDetailForm[satuan_id]['+iteration+']">'+
                        '<input type="hidden" id="satuandefault_id_'+ iteration +'" name="ResepturDetailForm[satuandefault_id]['+iteration+']">'+
                        '<input type="hidden" id="satuandefault_nama_'+ iteration +'" name="ResepturDetailForm[satuandefault_nama]['+iteration+']">'+
                        '<input type="hidden" id="satuankecil_nama_'+ iteration +'" name="ResepturDetailForm[satuankecil_nama]['+iteration+']">'+
                        '<input type="hidden" id="nilai_konversi_'+ iteration +'" name="ResepturDetailForm[nilai_konversi]['+iteration+']">'+
                        '<input type="hidden" id="harga_'+ iteration +'" name="ResepturDetailForm[harga]['+iteration+']">'+
                        '<input type="hidden" id="harga_konversi_'+ iteration +'" name="ResepturDetailForm[harga_konversi]['+iteration+']">'+
                        '<input type="hidden" id="harganetto_reseptur_'+ iteration +'" name="ResepturDetailForm[harganetto]['+iteration+']">'+
                        '<input type="hidden" id="harga_jual_'+ iteration +'" name="ResepturDetailForm[harga_jual]['+iteration+']">'+
                        '<input type="hidden" id="harga_satuan_'+ iteration +'" name="ResepturDetailForm[harga_satuan]['+iteration+']">'+
                    '</div>'+
                    '<div class="col-md-3">'+
                        '<div class="form-group field-resepturdetailform-hargasatuan_reseptur">'+
                            '<label class="text-right has-star" for="resepturdetailform-hargasatuan_reseptur">Harga Satuan</label>'+
                            '<div class="input-group">'+
                            '<span class="input-group-addon"><i class="satuandefault_nama_'+ iteration +'"></i></span>'+
                                '<input type="text" id="hargasatuan_reseptur_'+ iteration +'" class="form-control input-sm" name="ResepturDetailForm[hargasatuan_reseptur]['+iteration+']" readonly="readonly">'+
                                '<div class="help-block"></div>'+
                             '</div>' +
                        '</div>'+
                    '</div>'+
                    '<div class="col-md-3">'+
                        '<div class="form-group">'+
                            '<label class="text-right has-star">Stok</label>'+
                            '<div class="input-group">'+
                                '<span class="input-group-addon"><i class="satuandefault_nama_'+ iteration +'"></i></span>'+
                                '<input type="text" id="stok_sisa_'+ iteration +'" class="form-control input-sm" name="ResepturDetailForm[stok_sisa]['+iteration+']" readonly="readonly">'+
                                '<div class="help-block"></div>'+
                             '</div>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
                '<div class="row">'+
                    '<div class="col-md-3">'+
                        '<div class="row">'+
                            '<div class="col-md-6">'+
                                '<div class="form-group field-resepturdetailform-qty_reseptur required">'+
                                    '<label class="text-right has-star" for="resepturdetailform-qty_reseptur">Qty</label>'+
                                        '<input type="text" id="qty_reseptur_'+ iteration +'" class="form-control input-sm doco-decimal-wcomma" name="ResepturDetailForm[qty_reseptur]['+iteration+']" placeholder="Jumlah">'+
                                        '<div class="help-block"></div>'+
                                '</div>'+
                            '</div>'+
                            '<div class="col-md-6">'+
                                '<div class="form-group highlight-addon has-size-sm field-resepturdetailform-qty_konversi">'+
                                '<label class="text-right has-star" for="resepturdetailform-qty_konversi">Qty Konversi</label>'+
                                    '<div class="input-group">'+
                                        '<input type="text" id="resepturdetailform-qty_konversi_'+ iteration +'" class="form-control input-sm" name="ResepturDetailForm[qty_konversi]" readonly="">'+
                                        '<span class="input-group-addon"><i class="satuankecil_nama_nr_'+ iteration +'"></i></span>'+
                                    '</div>'+
                                '<div class="help-block"></div>'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
                '<div class="row">'+
                    '<div class="col-md-4">'+
                        '<div class="form-group" style="margin-top: 16px;margin-right: 300px;">'+
                            '<label class="text-right has-star"></label>'+
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
}

function submitRacikan() {
    var arrData = [];
    $.each($('#form-racikan').serializeArray(), function(k,v){
        let arr = v.name.split("hargasatuan_reseptur");
        arrData[k] = v;
        if(arr.length == 2){
            arrData[k].value = docoHelper.convertToAngka(v.value);
        }
    });
    arrData.push({
        name: 'iter',
        value: $('#reseptur_iter').val()
    });

    $.ajax({
        url: $('#form-racikan').attr('action'),
        method: "POST",
        dataType: "json",
        data: arrData,
        success : function(data) {
            resetAll(false);
            resetTambah();
            tabel_reseptur.clear();
            tabel_reseptur.ajax.url(baseUrl+"igd/pemeriksaan-igd/get-data-reseptur-session?id="+pendaftaran_id+"&cppt_id="+cppt_id+"&ruangan_id="+$("#select_depo").val()+"&jenis_racikan="+$("#resepturdetailform-jenis_racikan").val()+"").draw();
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

var config = {
    placeholder : 'Pilih Obat ... ',      // custom placeholder (optional) default null
    _api : '/igd/pemeriksaan-igd/list-obat-alkes-depo',   // get data
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

var _changeObat = function () {
    var id = $(this).val();
    var selected = apotek.list_stok[id];
    var _parent = $(this).closest('div.child');
    var _stok = 0;
    var _nilai_konversi = 0;
    var _harga = 0;
    var _konversi_stok = 0;
    var _konversi_harga = 0;
    var _harga_satuan = 0;
    var _stok_sisa = 0;

    if(_parent.length !== 0) {
        var iterasi = _parent.attr('class');
        var regex = /child-[0-9 -()+]+$/;
        if(iter = regex.exec(iterasi)) {
            iter = iter[0].substring(6);
        }
        if (typeof selected !== "undefined") {
            var _qtySedia = parseFloat(selected.qty_tersedia);
            if (typeof _tmpStok[id] !== "undefined") {
                _qtySedia -= parseFloat(_tmpStok[id]);
            }
            _parent.find('#qty_tersedia_'+iter).val(_qtySedia);
            _parent.find('#stok_sisa_'+iter).val(_qtySedia);
            _parent.find('#hargasatuan_reseptur_'+iter).val(selected.hargaygdipakai !== null ? selected.hargaygdipakai : 0);

            _parent.find('#harga_konversi_'+iter).val(selected.hargajual !== null ? selected.hargajual : 0);
            _parent.find("#harganetto_reseptur_"+iter).val(selected.harganetto !== null ? selected.harganetto : 0);
            _parent.find('#harga_'+iter).val(selected.hargajual !== null ? selected.hargajual : 0);
            _parent.find('#harga_jual_'+iter).val(selected.hargajual !== null ? selected.hargajual : 0);

            _parent.find('#satuandefault_id_'+iter).val(selected.satuankecil_id);
            _parent.find('#satuandefault_nama_'+iter).val(selected.satuankecil_nama);
            _parent.find('#satuankecil_nama_'+iter).val(selected.satuankecil_nama);
            _parent.find('.jumlah_konversi_racikan_'+iter).addClass('hidden');

            if(selected.satuankecil_nama == 'undefined' || selected.satuankecil_nama == null || selected.satuankecil_nama == "") {
                $('.satuandefault_nama_'+iter).addClass('hidden');
            } else {
                $('.satuandefault_nama_'+iter).removeClass('hidden');
                // $(".satuandefault_nama_"+iter).html('(<strong><i> ' + selected.satuankecil_nama + ') </i></strong>');
                $(".satuankecil_nama_nr_"+iter).html('<strong><i> ' + selected.satuankecil_nama + ' </i></strong>');
                $(".satuandefault_nama_"+iter).html(selected.satuankecil_nama);
            }

            _parent.find('#satuankecil_id_'+iter).select2();
            _parent.find('#satuankecil_id_'+iter).depdrop({
                depends: ['obatalkes_id_'+iter],
                url: baseUrl +'igd/end-point/list-satuan-besar',

            }).on('depdrop:afterChange', function(event, id, value, textStatus) {
                _group = {};
                var _id = $(this).val();
                var _response = $('#satuankecil_id_'+iter).depdrop('getAjaxResults');
                
                $.each(_response.output, function (x,y) {
                  _group[y.id] = y.konversi;
                });

                _harga = $("#hargasatuan_reseptur_"+iter).val();
                _nilai_konversi = _group[_id];
                _stok = $("#stok_sisa_"+iter).val();
                _harga_satuan = $("#harga_"+iter).val();
                _konversi_stok = _stok/_nilai_konversi;
                _konversi_harga = _harga_satuan*_nilai_konversi;

                $("#harga_satuan_"+iter).val(_konversi_harga.toFixed(2));
                $("#nilai_konversi_"+iter).val(_nilai_konversi);
                $("#stok_sisa_"+iter).val(docoHelper.convertToRupiah(_konversi_stok));
                $("#hargasatuan_reseptur_"+iter).val(docoHelper.convertToRupiah(_konversi_harga));
                 _parent.find('#satuankecil_id_'+iter).trigger('change');
            });
        }
    } 
    else {
        if (typeof selected !== "undefined") {
            var _qtySedia = parseFloat(selected.qty_tersedia);
            if (typeof _tmpStok[id] !== "undefined") {
                _qtySedia -= parseFloat(_tmpStok[id]);
            }
            $("#satuan_id_0").val(selected.satuankecil_id);
            $("#qty_tersedia_0").val(_qtySedia);
            $("#stok_sisa_0").val(_qtySedia);
            $("#satuandefault_id_0").val(selected.satuankecil_id);
            $("#satuankecil_id_0").val(selected.satuankecil_id);
            $("#satuandefault_nama_0").val(selected.satuankecil_nama);
            $("#satuankecil_nama_0").val(selected.satuankecil_nama);
            $('.jumlah_konversi_racikan_0').addClass('hidden');
            if(selected.satuankecil_nama == 'undefined' || selected.satuankecil_nama == null || selected.satuankecil_nama == "") {
                $('.satuandefault_nama_0').addClass('hidden');
            } else {
                $('.satuandefault_nama_0').removeClass('hidden');
                $(".satuankecil_nama_nr_0").html('<strong><i> ' + selected.satuankecil_nama + '</i></strong>');
                $(".satuandefault_nama_0").html('<strong><i> ' + selected.satuankecil_nama + '</i></strong>');

            }

            $("#harga_0").val(selected.hargajual !== null ? selected.hargajual : 0);
            $("#harga_jual_0").val(selected.hargajual !== null ? selected.hargajual : 0);
            $("#harga_konversi_0").val(selected.hargajual !== null ? selected.hargajual : 0);
            $("#hargasatuan_reseptur_0").val(selected.hargaygdipakai !== null ? selected.hargaygdipakai : 0);
            $("#harganetto_reseptur_0").val(selected.harganetto !== null ? selected.harganetto : 0);
            $('#satuandefault_id_0').val(selected.satuankecil_id);
            $("#qty_reseptur-0").val(0);
            $('.jumlah_konversi_racikan_0').addClass('hidden');
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

        var _konversi = (typeof _group[_val] === 'undefined') ? 0 : _group[_val];
        var _stok = (typeof $("#qty_tersedia_"+iter).val() === 'undefined') ? 0 : $("#qty_tersedia_"+iter).val();
        var _harga = $("#harga_"+iter).val();
        var lastText = '';

        if(_konversi == 0) {
            var _konversi_stok = 0;
            var _konversi_harga = 0;
        }
        else {
            _konversi_stok = _stok/_konversi;
            _konversi_harga =  _harga*_konversi;
        }

        var _satuan_kecil = $('#satuankecil_id_'+iter).select2('data');
        if(_satuan_kecil.length !== 0) {
            if (_satuan_kecil[0].id) {
                lastText = _satuan_kecil[0].text;
            }
        }

        _parent.find('#satuan_id_'+iter).val(_val);
        _parent.find('#satuankecil_id_'+iter).val(_val);
        _parent.find('#satuankecil_nama_'+iter).val(lastText);
        _parent.find('.satuandefault_nama_'+iter).removeClass('hidden');
        _parent.find('.satuandefault_nama_'+iter).html('<strong><i> ' +lastText + ' </i></strong>');
        _parent.find('#nilai_konversi_'+iter).val(_konversi);
        _parent.find('#qty_reseptur-'+iter).val(0);
        _parent.find('#hargasatuan_reseptur_'+iter).val(docoHelper.convertToRupiah(_konversi_harga));
        _parent.find('#harga_'+iter).val(_konversi_harga);
        _parent.find('#stok_sisa_'+iter).val(docoHelper.convertToRupiah(_konversi_stok));

        //clear
        $('.jumlah_konversi_racikan_'+iter).addClass('hidden');
        $('#qty_reseptur_'+iter).val(0);
        $("#konversi_html_"+iter).html("");
        $("#konversi_input_"+iter).val("");
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
            if (_satuan_kecil[0].id) {
                lastText = _satuan_kecil[0].text;
            }
        }
        $("#satuan_id_0").val(_val);
        $("#hargasatuan_reseptur_0").val(docoHelper.convertToRupiah(_konversi_harga));
        $("#nilai_konversi_0").val(_nilai_konversi);
        $("#satuankecil_nama_0").val(lastText);
        $('.satuandefault_nama_0').removeClass('hidden');
        $(".satuandefault_nama_0").html('<strong><i>' + lastText + '</i></strong>');
        $("#stok_sisa_0").val(docoHelper.convertToRupiah(_konversi_stok));

        //clear
        $('.jumlah_konversi_racikan_0').addClass('hidden');
        $('#qty_reseptur_0').val(0);
        $("#konversi_html_0").html("");
        $("#konversi_input_0").val("");
    }
}

var _changeQty = function () {
    var _qty = parseFloat(docoHelper.convertToAngka($(this).val())).toFixed(2);
    var _parent = $(this).closest('div.child');
    // $(this).val(_qty);
    if(_parent.length !== 0) {
        var iterasi = _parent.attr('class');
        var regex = /child-[0-9 -()+]+$/;
        if(iter = regex.exec(iterasi)) {
            iter = iter[0].substring(6);
        }

        var _nilai_konversi = $("#nilai_konversi_"+iter).val();
        var _konversi = _qty * _nilai_konversi;
        var _satuandefault = $('#satuandefault_nama_'+iter).val();
        var _stokTersedia = docoHelper.convertToAngka($("#stok_sisa_"+iter).val());
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

        if(_konversi > (_stokTersedia * _nilai_konversi)) {
            docoNotification('warning', 'Peringatan', 'Maaf, Stok Tersedia tidak mencukupi.');
            _parent.find('#qty_reseptur_'+iter).val(0);
             _parent.find('#resepturdetailform-qty_konversi_'+iter).val(0);
            _parent.find('.jumlah_konversi_racikan_'+iter).addClass('hidden');
            _parent.find('.save-racikan').prop('disabled', true);
        }
        else {
            _parent.find('.jumlah_konversi_racikan_'+iter).removeClass('hidden');
            _parent.find('#konversi_input_'+iter).val(_konversi + ' ' + _satuandefault);
            _parent.find('#resepturdetailform-qty_konversi_'+iter).val(docoHelper.convertToRupiah(_konversi.toFixed(2)));
            _parent.find('#konversi_html_'+iter).html(_konversi + ' ' + _satuandefault + _keterangan);
            _parent.find('.save-racikan').prop('disabled', false);
        }
    }
    else {
        var _nilai_konversi = $("#nilai_konversi_0").val();
        var _konversi = _qty * _nilai_konversi;
        var _satuandefault = $('#satuandefault_nama_0').val();
        var _stokTersedia = docoHelper.convertToAngka($("#stok_sisa_0").val());
        var _satuanOrder = $("#satuankecil_nama_0").val();
        var _keterangan = '';

        if(_satuandefault !== _satuanOrder) {
            _keterangan = '<strong><i> ( ' + _qty + ' ' + _satuanOrder + ' ) </i></strong>';
        }

        if(_konversi > (_stokTersedia * _nilai_konversi)) {
            docoNotification('warning', 'Peringatan', 'Maaf, Stok Tersedia tidak mencukupi.');
            $(this).val(0);
            $("#resepturdetailform-qty_konversi").val(0);
            $('.jumlah_konversi_racikan_0').addClass('hidden');
            $("#konversi_html_0").html("");
            $("#konversi_input_0").val("");
            $('.save-racikan').prop('disabled', true);
        }
        else {
            $(".jumlah_konversi_racikan_0").removeClass('hidden');
            $("#konversi_html_0").html(_konversi + ' ' + _satuandefault + _keterangan);
            $("#konversi_input_0").val(_konversi + ' ' + _satuandefault);
            $("#resepturdetailform-qty_konversi").val(docoHelper.convertToRupiah(_konversi.toFixed(2)));
            $('.save-racikan').prop('disabled', false);
        }
    }
}

$(document).ready(function(){
    $('#obatalkes_id_0').docoPaginationSelec2(config).on('change',_changeObat);
    $("#qty_reseptur_0").on('change', _changeQty);
});

function resetTambah(){
    var $form = $("#form-racikan");
    $form.find('input:text, input:password, input:file, textarea').val('');
    $(document).ready(function() {
        $('.satuandefault_nama_0').addClass('hidden');
        $("#stok_sisa_0").val(0);
        $("#obatalkes_id_0").val(null).trigger("change");
        $("#satuankecil_id_0").val(null).trigger("change");
        $('#qty_reseptur_0').val(0);
        $('.jumlah_konversi_racikan_0').addClass('hidden');
        $("#konversi_html_0").html("");
        $("#konversi_input_0").val("");
        
    });
}