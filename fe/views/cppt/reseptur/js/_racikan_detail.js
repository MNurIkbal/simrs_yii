var _rSatuanData = [];
var _signaRacikan = [];
$(document).ready( () => {
    $('#is_kronis_racikan').uniform();
    $('#satuan_racikan').docoPaginationSelec2({
        placeholder: 'Pilih',
        _api: '/rajal/allow/get-master-unit',
        ajax: {
            data: function(params) {
                return {
                    q: params.term,
                    page: params.page || 1,
                    allowEmpty: false
                }
            },
            results: (data, params) => {
                var more = (params.page * 30) < data.total_count;
                return { results: data.items, more: more };
            },
            processResults: function(res, params) {
                params.page = params.page || 1;
                var arr = [];
                $.each(res.data.satuan, function(index, value) {
                    if ( index < res.data.limit) {
                        arr.push({
                            id: value.satuanunit_id,
                            text: value.satuanunit_nama,
                        })
                    }
                });
                return {
                    results: arr,
                    pagination: {
                        more: res.data.satuan.length > res.data.limit
                    }
                };
            },
        }
    })

    $('#is_kronis_racikan').on('change', function() {
        if($('#is_kronis_racikan').is(':checked') ) {
            if(enable_split_kronis == 1) {
                $('#r_hari').val(hari_resep_kronis).trigger('change')
            }
        }
    })

    $('#r_signa_id').docoPaginationSelec2(_configSignaReseptur).on('select2:select', function (){
        setQtyRacikanValue();
    });

    $('#r_hari').select2({
        placeholder: 'Hari',
        dropdownParent: $('#form-racikan-detail'),
    });

    $('#r_hari').on('select2:select', function(){
        setQtyRacikanValue();
    })

    $('#r_obatalkes_id').docoPaginationSelec2(_configObatAlkesSelectNew).on('change', () => {
        $('#r_obatalkes_id').parents('.form-group').next('.text-danger').remove();
        if ( $('#r_obatalkes_id').val() != '' ) {
            var id = $('#r_obatalkes_id').val();
            let selected;
            let non_racikan = 0
            let group_jenisobat = null
            if($(this).attr('id') == 'obatalkes_id'){
                non_racikan = 1
                group_jenisobat = $('#generalresepturnrdetailform-group_jenisobat input:checked').val()
            }
            $.ajax({
                data: {
                    obatalkes_id: id,
                    ruangan_id: $('#select_ruangan').val(),
                    penjamin_id: $('#penjamin_id').val(),
                    group_jenisobat: group_jenisobat,
                    kelaspelayanan_id: kelaspelayanan_id,
                    kelastagihan_id: kelastagihan_id,
                    is_others: is_others,
                    non_racikan: non_racikan,
                    groupJenisobat: groupJenisobat,
                },
                url: '/rajal/allow/list-obat-alkes-depo',
                dataType: 'json',
                success: function (results) {
                    selected = results?.data_stok[0];
                    obatalkesRacikanChange(id, selected);
                    
                },
                error: function (data) {
                    obatalkesRacikanChange(id, selected);
                    return false;
                }
            });
        } else {
            $('#r_satuan_id').val('');
            $('#r_satuan_text').val('');
            $("#r_stok_tersedia").val(0);
            $("#r_stok_sisa").val(0);
            $("#text_stok_tersedia_racikan").text('');

            $("#r_harga").val(0);
            $("#r_harganetto_reseptur").val(0);
        }
    })
    $('#btn-add-detail-racikan').unbind()
    $('#btn-add-detail-racikan').bind('click', (e) => {
        e.preventDefault()
        let _hasError = false
        if ( $('#r_obatalkes_id').val() == '' ) {
            $('#r_obatalkes_id').closest('.form-group').after('<p class="has-error" style="color: red">Obat Tidak Boleh Kosong!</p>')
            _hasError = true
        }

        if ( $('#r_hari').val() == '' || $('#r_hari').val() == null ) {
            $('#r_hari').closest('.form-group').after('<p class="has-error" style="color: red">Hari Tidak Boleh Kosong!</p>')
            _hasError = true
        }

        if ( $('#r_qty').val() == ''  ) {
            $('#r_qty').closest('.form-group').after('<p class="has-error" style="color: red">Qty Tidak Boleh Kosong!</p>')
            _hasError = true
        } else {
            if ( isNaN($('#r_qty').val()) || $('#r_qty').val() < 0) {
                $('#r_qty').closest('.form-group').after('<p class="has-error" style="color: red">Qty Tidak Valid!</p>')
                _hasError = true
            }
        }

        if ( $('#rke').val() == '') {
            $('#rke').closest('.form-group').after('<p class="has-error" style="color: red">R-ke Tidak Boleh Kosong!</p>')
            _hasError = true
        }
        if ( $('#qty_racikan').val() == '') {
            $('#qty_racikan').closest('.form-group').after('<p class="has-error" style="color: red">Qty Racikan Tidak Boleh Kosong!</p>')
            _hasError = true
        }
        if ( $('#satuan_racikan').val() == '' || !$('#satuan_racikan').val()) {
            $('#satuan_racikan').parent().append('<p class="has-error" style="color: red">Satuan Racikan Tidak Boleh Kosong!</p>')
            _hasError = true
        }

        if ( $('#r_signa_id').val() == '') {
            $('#r_signa_id').closest('.form-group').after('<p class="has-error" style="color: red">Signa Tidak Boleh Kosong!</p>')
            _hasError = true
        }
        if ( $('#r_satuan_id').val() == '' ) {
            $('#r_satuan_text').closest('.form-group').after('<p class="has-error" style="color: red">Satuan Tidak Boleh Kosong!</p>')
            _hasError = true
        }

        if ($('#r_catatan').val().length > 2000) {
            $('#r_catatan').closest('.form-group').after('<p class="has-error" style="color: red">Catatan tidak boleh lebih dari 2000 karakter</p>')
            _hasError = true;
        }

        if (konfigStokObatAlkes == 'true') {
            if (parseFloat($('#r_qty').val()) > parseFloat($("#r_stok_tersedia").val())) {
                docoNotification('warning', 'Perhatian', 'Qty tidak boleh melebihi stok tersedia')
                $('#r_qty').closest('.form-group').after('<p class="has-error" style="color: red">Qty Tidak Valid!</p>')
                _hasError = true
            }

            if (parseFloat($('#r_qty').val()) == 0 || parseFloat($('#r_qty').val()) == null || parseFloat($('#r_qty').val()) == undefined) {
                docoNotification('warning', 'Perhatian', 'Qty harus lebih dari 0')
                $('#r_qty').closest('.form-group').after('<p class="has-error" style="color: red">Qty Tidak Valid!</p>')
                _hasError = true
            }
        }

        if ( _hasError ) {
            setTimeout( () => {
                $('.has-error').remove()
            }, 2000);
            $('#rke').focus();
            return false
        }

        let  is_kronis = false;
        if($('#is_kronis_racikan:checked').val() == '1') {
            is_kronis = true;
        }

        let _racikanIndex = racikanArray.findIndex( itemracikan => itemracikan.obatalkes_id == parseInt( $('#r_obatalkes_id').val() ))
        if ( _racikanIndex < 0 ) {
            let thisSigna = dataSigna[$('#r_signa_id').val()];
            let _formData = {
                detail_type: 'racikan_detail',
                obatalkes_id: parseInt( $('#r_obatalkes_id').val() ),
                obatalkes_nama: $('#r_obatalkes_id :selected').text(),
                qty_reseptur: _konversiData().qty_konversi,
                satuankecil_id: _konversiData().satuankecil_id,
                satuankecil_text:_konversiData().satuan_kecil,
                satuaninput_id: _konversiData().satuanbesar_id,
                satuaninput_nama: _konversiData().satuan_besar,
                satuaninput_text: _konversiData().satuan_besar,
                satuan_nama: _konversiData().satuan_besar,
                rke: $('#rke').val(),
                r: 'r',
                hargasatuan_reseptur: $('#r_harga').val(),
                harganetto_reseptur: $('#r_harganetto_reseptur').val(),
                hargajual_reseptur: $("#r_harga").val(),
                signa_id: isNaN( $('#r_signa_id').val() ) ? null : $('#r_signa_id').val(),
                signa: $('#r_signa_id :selected').text(),
                qty_konversi: _konversiData().qty_konversi,
                satuanbesar_id: _konversiData().satuanbesar_id,
                satuan_besar: _konversiData().satuan_besar,
                additional_data: _konversiData().additional_data,
                etiket: $('#r_catatan').val(),
                racikan_id: 'OR',
                nama_racikan: $('#racikan_nama').val(),
                qty_racikan: $('#racikan_qty').val(),
                satuan_racikan_id: $('#satuan_racikan').val(),
                satuan_racikan_nama: $('#satuan_racikan :selected').text(),
                is_kronis: is_kronis,
                hari: parseInt($('#r_hari').val()),
                kebutuhan: _konversiData().kebutuhan,
                qty_obat : thisSigna.qty_obat != null ? thisSigna.qty_obat : 0,
                iterasi : thisSigna.iterasi != null ? thisSigna.iterasi : 0,
                stok_sisa : parseFloat($("#r_stok_tersedia").val())
            }
            racikanArray.push(_formData)
        } else {
            let _racikanAdditionalData = JSON.parse(racikanArray[_racikanIndex].additional_data)
            racikanArray[_racikanIndex].qty_reseptur = (parseFloat(racikanArray[_racikanIndex].qty_reseptur) + parseFloat( $('#r_qty').val().replace(',', '.') )).toFixed(2)
            racikanArray[_racikanIndex].qty_konversi = (parseFloat(racikanArray[_racikanIndex].qty_konversi) + parseFloat(_konversiData().qty_konversi)).toFixed(2)
            _racikanAdditionalData.harga_konversi = racikanArray[_racikanIndex].qty_reseptur * $("#r_harganetto_reseptur").val()
            racikanArray[_racikanIndex].additional_data = JSON.stringify(_racikanAdditionalData)
        }

        _resetRacikanObat()
        $('#rke').attr('disabled', true);
        $('#qty_racikan').attr('disabled', true);
        $('#r_hari').attr('disabled', true);
        $('#satuan_racikan').trigger('change').attr('disabled', true);
        $('#r_signa_id').trigger('change').attr('disabled', true);
        $('#racikan_nama').attr('disabled', true);
        $('#r_catatan').attr('disabled', true);
        drawRacikanTable()
        $('#r_obatalkes_id').focus();
    })

    var drawRacikanTable = () => {
        let _racikanTable = $('#racikan-tbl')
        let _html = ''
        if ( racikanArray.length ) {
            $.each(racikanArray, (k,v) => {
                _html += `
                    <tr>
                        <td>${v.obatalkes_nama}</td>
                        <td>${v.qty_reseptur} ${v.satuan_nama}</td>
                        <td>${v.stok_sisa} ${v.satuan_nama}</td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm delete-racikan-data" data-id="${k}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `
            })
        } else {
            _html = `
                <tr>
                    <td colspan="3" class="no-row-data text-center">Belum ada data obat racikan yang ditambahkan</td>
                </tr>
            `
            _undisabledRacikan()
        }
        _racikanTable.find('tbody').html(_html)
        $('.delete-racikan-data').unbind()
        $('.delete-racikan-data').bind('click', ({delegateTarget}) => {
            let _id = $(delegateTarget).data('id')
            racikanArray.splice(_id, 1)
            drawRacikanTable()
        })

        $('#btn-add-racikan').attr('disabled', racikanArray.length ? false : true )
    }

    $('#btn-add-racikan').unbind()
    $('#btn-add-racikan').bind('click', () => {
        let _hasError = false

        if ( $('#rke').val() == '') {
            $('#rke').closest('.col-md-2').append('<p class="has-error" style="color: red">R-ke Tidak Boleh Kosong!</p>')
            _hasError = true
        }

        if ( $('#r_signa_id').val() == '') {
            $('#r_signa_id').closest('.col-md-12').append('<p class="has-error" style="color: red">Signa Tidak Boleh Kosong!</p>')
            _hasError = true
        }

        if ( $('#rke').val() != '' && $('#r_signa_id').val() != '') {
            let _tmpObatIndex = list_temp_obat.findIndex( itemObat => itemObat.rke == $('#rke').val() )
            if ( _tmpObatIndex >= 0) {
                if ( list_temp_obat[_tmpObatIndex].signa_id != $('#r_signa_id').val() || list_temp_obat[_tmpObatIndex].satuan_racikan_id != $('#satuan_racikan').val() || list_temp_obat[_tmpObatIndex].qty_racikan != $('#qty_racikan').val() ) {
                    $('#r_signa_id').closest('.col-md-12').append('<p class="has-error" style="color: red">R-ke yg sama dengan signa / qty / satuan racikan yg berbeda tidak dapat diinputkan!</p>')
                    _hasError = true
                }
            }
        }

        if ( _hasError ) {
            setTimeout( () => {
                $('.has-error').remove()
            }, 2000);
            return false
        }
        let racikanObat = []
        let  is_kronis = false;
        if($('#is_kronis_racikan:checked').val() == '1') {
            is_kronis = true;
        }
        $.each(racikanArray, (key, item) => {
            let r_signa_id = isNaN( $('#r_signa_id').val() ) ? null : $('#r_signa_id').val()
            let r_signa = $('#r_signa_id :selected').text().trim()

            racikanArray[key].signa_id = r_signa_id == r_signa ? null : r_signa_id
            racikanArray[key].signa = r_signa
            racikanArray[key].etiket = $('#r_catatan').val()
            racikanArray[key].rke = $('#rke').val()
            racikanArray[key].nama_racikan = $('#racikan_nama').val()
            racikanArray[key].qty_racikan = $('#qty_racikan').val()
            racikanArray[key].satuan_racikan_id = $('#satuan_racikan').val()
            racikanArray[key].satuan_racikan_nama = $('#satuan_racikan :selected').text()
            racikanArray[key].is_kronis = is_kronis;

            let tempObatIndex = list_temp_obat.findIndex(itemObat => itemObat.signa_id == item.signa_id && itemObat.obatalkes_id == item.obatalkes_id && itemObat.rke == item.rke && itemObat.satuan_racikan_id == item.satuan_racikan_id && itemObat.qty_racikan == item.qty_racikan)
            if ( tempObatIndex >= 0 ) {
                let _tempObatAdditionalData = JSON.parse(list_temp_obat[tempObatIndex].additional_data)
                list_temp_obat[tempObatIndex].qty_reseptur = ( parseFloat(list_temp_obat[tempObatIndex].qty_reseptur) + parseFloat(item.qty_reseptur) ).toFixed(2)
                list_temp_obat[tempObatIndex].qty_konversi = ( parseFloat(list_temp_obat[tempObatIndex].qty_konversi) + parseFloat(item.qty_konversi) ).toFixed(2)
                _tempObatAdditionalData.harga_konversi = list_temp_obat[tempObatIndex].harganetto_reseptur * list_temp_obat[tempObatIndex].qty_reseptur
                list_temp_obat[tempObatIndex].additional_data = JSON.stringify(_tempObatAdditionalData)

            } else {
                list_temp_obat.push(item)
            }
        })

        _undisabledRacikan()
        // list_temp_obat = [...list_temp_obat, ...racikanArray]
        appendObat(list_temp_obat)
        _resetRacikan()
        drawRacikanTable()
        _resetRacikanObat()
        $('#rke').focus();
        $('#is_kronis_racikan').prop('disabled', false).uniform()
    })

    let _resetRacikan = () => {
        racikanArray = []
        $('#rke').val('')
        $('#r_signa_id').val('').trigger('change')
        $('#r_catatan').val('')
        $('#racikan_nama').val('')
        $('#qty_racikan').val('')
        $('#r_hari').val('').change();
        $('#satuan_racikan').val('').trigger('change')
        $('#is_kronis_racikan').prop('checked', false).uniform()
    }

    let _resetRacikanObat = () => {
        $('#r_obatalkes_id').val('').trigger('change')
        $('#r_qty').val('')
        $('#kebutuhan').val('')
        $('#is_kronis_racikan').prop('disabled', true).uniform()
    }

    let _undisabledRacikan = () => {
        $('#rke').attr('disabled', false);
        $('#qty_racikan').attr('disabled', false);
        $('#r_hari').attr('disabled', false);
        $('#satuan_racikan').trigger('change').attr('disabled', false);
        $('#r_signa_id').trigger('change').attr('disabled', false);
        $('#racikan_nama').attr('disabled', false);
        $('#r_catatan').attr('disabled', false);
    }

    let _konversiData = () => {
        let _qtyInput = parseFloat( $('#r_qty').val().replace(',', '.') ).toFixed(2)
            _satuanInput = parseInt($('#r_satuan_id').val())
            _nilaiKonversi = _rSatuanData.find( itemkonversi => itemkonversi.id == _satuanInput )
            _kebutuhan = parseFloat($('#kebutuhan').val().replace(',', '.') ).toFixed(2);
        return {
            qty_konversi: _qtyInput,
            satuankecil_id: _satuanInput,
            satuan_nama: $('#r_satuan_text').val(),
            satuan_kecil: $('#r_satuan_text').val(),
            satuanbesar_id: _satuanInput,
            satuan_besar: $('#r_satuan_text').val(),
            kebutuhan: _kebutuhan,
            additional_data: JSON.stringify({
                satuaninput_id: _satuanInput,
                satuan_input: $('#r_satuan_text').val(),
                satuankonversi_id: _nilaiKonversi.satuankonversi_id,
                satuan_konversi: $('#r_satuan_text').val(),
                harga_konversi: _qtyInput * $("#r_harganetto_reseptur").val(),
                nilai_konversi: parseInt(_nilaiKonversi.nilai_konversi)
            })
        }
    }


    $("#kebutuhan").keyup(function() {
        var hasil_r_qty =  $('#qty_racikan').val() * $( "#kebutuhan" ).val()
        if(hasil_r_qty == 0){
            hasil_r_qty = ''
        }
        $('#r_qty').val(parseFloat(hasil_r_qty).toFixed(2));
    });

    $("#r_qty").keyup(function() {
        var hasil_kebutuhan =   $( "#r_qty" ).val() / $('#qty_racikan').val()
        if($("#qty_racikan") == undefined|| hasil_kebutuhan == 0){
            hasil_kebutuhan = ''
        }
        $('#kebutuhan').val(hasil_kebutuhan)
    });

    $("#rke").keyup(function() {
        $('#r_qty').val('')
        $('#kebutuhan').val('')
    });

    $("#qty_racikan").on('change', function() {
        if($(this).val() != ''){
            $('#kebutuhan').parents('.form-group').removeClass('has-error');
            $('#r_qty').parents('.form-group').removeClass('has-error');
            $('#r_qty').parents('.form-group').next('.text-danger').remove();
            $('#r_qty').attr('disabled', false);
            $('#kebutuhan').attr('disabled', false);
        }else{
            $('#r_qty').attr('disabled', true);
            $('#kebutuhan').attr('disabled', true);
        }
        $('#r_qty').val('');
        $('#kebutuhan').val('');
    });

})

function setQtyRacikanValue(){
    let currentSigna = dataSigna[$('#r_signa_id').val()];
    calculateJumlah = '';
    _signaObat = typeof currentSigna !== 'undefined' ? currentSigna : null;
    _jumlahHari = $('#r_hari').val() != '' ? parseInt($('#r_hari').val()) : 0;

    if (_signaObat != null && !isNaN(_jumlahHari) && _signaObat.qty_obat != null && _signaObat.iterasi != null) {
        calculateJumlah = parseFloat((_jumlahHari * _signaObat.qty_obat * parseFloat(_signaObat.iterasi))).toFixed(2);
    }


    let isSignaFreetext = currentSigna.signa_id == currentSigna.signa_nama
                          && currentSigna.qty_obat == null
                          ? true
                          : false;

    if (isSignaFreetext) {
        $('#qty_racikan').prop('readonly', false);
    } else {
        $('#qty_racikan').prop('readonly', true);
        $('#qty_racikan').val(calculateJumlah).trigger('change');
    }
}

function obatalkesRacikanChange(id, selected) {
    if (typeof selected != 'undefined') {

        // set data apotek
        apotek.list_stok[selected.obatalkes_id] = selected;

        _stok_sisa = parseFloat(selected.qty_tersedia).toFixed(2);
        if (typeof _tmpStok[id] !== "undefined") {
            _stok_sisa -= parseFloat(_tmpStok[id]);
        }

        _hargajual = parseFloat(selected.hargaygdipakai !== null ? selected.hargaygdipakai : 0).toFixed(2);
        _harganetto = parseFloat(selected.harganetto).toFixed(2);

        $("#r_stok_tersedia").val(_stok_sisa);
        $("#r_stok_sisa").val(_stok_sisa);
        $("#text_stok_tersedia_racikan").text(_stok_sisa);

        $("#r_harga").val(_hargajual);
        $("#r_harganetto_reseptur").val(_harganetto);
        $('#r_satuan_id').val(selected.satuankecil_id);
        $('#r_satuan_text').val(selected.satuankecil_nama);

        $.ajax({
            url: `/rajal/allow/get-satuan?obatalkes_id=${$('#r_obatalkes_id').val()}`,
            method: 'GET',
            beforeSend: () => {
                $('#btn-add-detail-racikan').attr('disabled', true)
            },
            success: (response) => {
                const { data } = response.data
                let _satuanData = []
                $('#btn-add-detail-racikan').attr('disabled', false)
                if (!data.length) {
                    docoNotification('warning', 'Perhatian', 'Obat Belum Memiliki Satuan Konversi')
                    return false
                }
                $.each(data, (key, item) => {
                    _satuanData.push({
                        id: item.satuanbesar_id,
                        text: item.satuan_besar,
                        ...item,
                    })
                })
                _rSatuanData = _satuanData
                let konversiSatuanTerkecil = _rSatuanData.find(itemkonversi => itemkonversi.id == $('#r_satuan_id').val());
                if (!konversiSatuanTerkecil) {
                    $('#btn-add-detail-racikan').attr('disabled', true);
                    $('#r_obatalkes_id').parents('.form-group').after('<p class="text-danger has-error">Obat ini tidak memiliki satuan konversi untuk unit terkecil</p>');
                }

                $("#text_stok_tersedia_racikan").text(_stok_sisa);
            }
        })
    } else {
        $("#r_stok_tersedia").val(0);
        $("#r_stok_sisa").val(0);
        $("#text_stok_tersedia_racikan").text('');

        $("#r_harga").val(0);
        $("#r_harganetto_reseptur").val(0);
    }
}
