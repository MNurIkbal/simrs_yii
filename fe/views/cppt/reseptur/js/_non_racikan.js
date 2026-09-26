window.list_obat = [];
var apotek =  { list_stok: {} };
var pesan_validasi = "";

// mencegah karakter lain selain angka desimal
$(document).on('input', '.doco-decimal', function() {
    match = (/(\d{0,9})[^.]*((?:\.\d{0,9})?)/g).exec(this.value.replace(/[^\d.]/g, ''));
    this.value = match[1] + match[2];
});

function resetNonRacikan(){
    var form = $("#form-nonracikan");
    form[0].reset();
    $('#obatalkes_id').val('').trigger('change');
    $('#signa_reseptur').val('').trigger('change').attr('disabled', true);
    $('#jumlah_hari').val(1).trigger('change').attr('disabled', true);
    $('#qty_reseptur').attr('disabled', true);
    $("#text_konversi").text("");
    $("#satuan_terpilih").text("");
    $("#text_stok_tersedia").text("");
    $('.is-kronis-nr').prop('checked', false).uniform();
}

function validasiObat(obat) {
    var is_valid = true;

    if(list_temp_obat.length > 0) {
        $.each(list_temp_obat, function (x, y) {
            if(y.racikan_id == "NR") {
                if(obat.obatalkes_id == y.obatalkes_id) {
                    pesan_validasi = "Obat "+obat.obatalkes_nama+" sudah diinputkan";
                    is_valid = false;
                }
            }
        });
    }
    if(obat.obatalkes_id == 0) {

        if(obat.etiket === "") {
            pesan_validasi = "Untuk pilihan obat alkes others <br> catatan harus di isi";
            is_valid = false;
        }

    } else {

        if(obat.obatalkes_id === "") {
            pesan_validasi = "Obat harus dipilih";
            is_valid = false;
        }

        if(obat.signa == null || obat.signa === "" || $("#signa_reseptur option:selected").val() == "") {
            pesan_validasi = "Signa harus diisi";
            is_valid = false;
        }

        if(obat.qty_reseptur <= 0) {
            pesan_validasi = "Qty harus lebih dari 0";
            is_valid = false;
        }

        if (konfigStokObatAlkes == 'true') {
            if (parseFloat(obat.qty_reseptur) > parseFloat(obat.stok_sisa)) {
                pesan_validasi =  "Qty tidak boleh melebihi stok tersedia";
                is_valid = false;
            }
        }
    }

    if (obat.etiket.length > 2000) {
        pesan_validasi = "Catatan tidak boleh lebih dari 2000 karakter";
        is_valid = false;
    }
    return is_valid;
}

function suggestionQty() {
    let currentSigna = dataSigna[$('#signa_reseptur').val()];
        calculateJumlah = 0;
        _signaObat = typeof currentSigna !== 'undefined' ? currentSigna : null;
        _jumlahHari = $('#jumlah_hari').val() != '' ? parseInt($('#jumlah_hari').val()) : 0;

    if (_signaObat != null && !isNaN(_jumlahHari) && _signaObat.qty_obat != null && _signaObat.iterasi != null) {
        calculateJumlah = (_jumlahHari * (parseFloat(_signaObat.qty_obat) * parseFloat(_signaObat.iterasi)));
    }
    $('#qty_reseptur').val(calculateJumlah).trigger('change');
}

$(document).ready(function(){
    $('.is-kronis-nr').uniform();
    $('#signa_reseptur').attr('disabled', true);
    $('#jumlah_hari').val(1).attr('disabled', true);
    $('#qty_reseptur').attr('disabled', true);
    $("#btn-tambah-non-racikan").on("click", function(){
        var obatalkes_id = $("#obatalkes_id").val();
        var obatalkes_nama = $("#obatalkes_id option:selected").text();
        var qty_reseptur = $("#qty_reseptur").val();
        var harganetto_reseptur = $("#harganetto_reseptur").val();
        var satuankecil_id = $("#satuandefault_id").val();
        var satuaninput_id = $("#satuankecil_id").val();
        var satuankecil_text = $("#satuandefault_nama").val();
        var satuaninput_text = $("#satuankecil_nama").val();
        var signa = $("#signa_reseptur option:selected").text().trim();
        var signa_id = $("#signa_reseptur option:selected").val();
        signa_id = signa == signa_id ? null : signa_id;
        var etiket = $("#etiket").val();
        var racikan_id = "NR";
        var rke = null;
        var is_valid = false;
        var nilai_konversi = $("#nilai_konversi").val();
        var harga_reseptur = $("#harga").val() * nilai_konversi;
        var qty_konversi = qty_reseptur * nilai_konversi;
        var validateSigna = dataSigna[signa_id];
        var detail_type = "non_racikan";
        var is_kronis = false;

        if($('.is-kronis-nr:checked').val() == '1') {
            is_kronis = true;
        }
        let hari = $('#jumlah_hari').val();

        if(obatalkes_id == 0){
            signa = ''
            qty_konversi = 1
            harganetto_reseptur = 1
            satuankecil_id = 0
            detail_type = "racikan_freetext"
        }

        let thisSigna = dataSigna[signa_id];

        var additional_data = JSON.stringify({
            satuaninput_id: satuaninput_id,
            satuan_input: satuaninput_text,
            satuankonversi_id: satuankecil_id,
            satuan_konversi: satuankecil_text,
            harga_reseptur: harga_reseptur,
            nilai_konversi: nilai_konversi
        });

        var obat_nr = {
            detail_type: detail_type,
            racikan_id: racikan_id,
            obatalkes_id: obatalkes_id,
            obatalkes_nama: obatalkes_nama,
            rke: null,
            qty_reseptur: qty_reseptur,
            qty_konversi: qty_konversi,
            hargasatuan_reseptur: harga_reseptur,
            harganetto_reseptur: harganetto_reseptur,
            satuankecil_id: satuankecil_id,
            satuankecil_text: satuankecil_text,
            satuaninput_id: satuaninput_id,
            satuaninput_text: satuaninput_text,
            signa: signa,
            etiket: etiket,
            additional_data: additional_data,
            signa_id: typeof validateSigna !== 'undefined' && !isNaN(signa_id) ? signa_id : null,
            racikan_text: etiket,
            is_kronis: is_kronis,
            hari: hari,
            qty_obat : thisSigna != null ? thisSigna.qty_obat : 0,
            iterasi : thisSigna != null ? thisSigna.iterasi : 0,
            stok_sisa: _stok_sisa
        };

        is_valid = validasiObat(obat_nr);
        if(!is_valid) {
            docoNotification("warning", "Peringatan", pesan_validasi);
        } else {
            list_temp_obat.push(obat_nr);
            appendObat(list_temp_obat);
            resetNonRacikan();
        }
        $('#obatalkes_id').focus();
    });

    $("#jumlah_hari").select2({
        dropdownParent: $("#non_racikan"),
    })
    
    $('#obatalkes_id').on('depdrop:afterChange', function(event, id, value, jqXHR, textStatus) {
        let ajaxResults = $('#obatalkes_id').depdrop('getAjaxResults');
        list_obat = ajaxResults['output'];
    });

    $('#obatalkes_id').docoPaginationSelec2(_configObatAlkesSelectNew).on('change', function(e) {

        let id = $(this).val();
        let selected;

        if ($(this).attr('id') == 'obatalkes_id') {
            non_racikan = 1
            group_jenisobat = $('#generalresepturnrdetailform-group_jenisobat input:checked').val()
        }
        if (id && id != 0) {
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
                    obatalkesNonRacikanChange(id, is_others, selected);
                },
                error: function (data) {
                    obatalkesNonRacikanChange(id, is_others, selected);
                    return false;
                }
            });
        } else if (id == 0) {
            selected = {
                // 'instalasi_id': $instalasi_id,
                'obatalkes_id': '0',
                'obatalkes_namalain': 'OTHERS',
                'obatalkes_nama': 'OTHERS',
                'qty_tersedia': 1,
                // 'ruangan_id': $ruangan_id,
                'jenisobatalkes_id': '0',
                'qty_reseptur': 1,
                'qty_konversi': 1,
            };
            obatalkesNonRacikanChange(id, is_others, selected);
        } else {
            obatalkesNonRacikanChange(id, is_others, selected);
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
        var _satuan_terkecil = $('#satuandefault_nama').val();

        if(_satuan_kecil.length !== 0) {
            lastText = _satuan_kecil[0].text;
        }

        $("#satuan_terpilih").text(lastText);
        $("#text_stok_tersedia").text(_konversi_stok.toFixed(2));
        var _text_konversi = "1 "+ lastText + " = "+ _nilai_konversi +" " + _satuankecil_nama;
        $("#text_konversi").text(_text_konversi);

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

    $('#signa_reseptur').docoPaginationSelec2(_configSignaReseptur).on('change', function(e) {
        if ($('#signa_reseptur').val() != '') {
            suggestionQty();
        }
    })

    $('#jumlah_hari').on('change', function(e) {
        if ($('#jumlah_hari').val() != '') {
            suggestionQty();
        }
    })

    $('.is-kronis-nr').on('change', function(e) {
        if($('.is-kronis-nr').is(':checked')) {
            if(enable_split_kronis == 1) {
                $('#jumlah_hari').val(hari_resep_kronis).trigger('change');
                suggestionQty();
            }
        }
    })

    function clearChange () {
        $(".konversi").html("");
        $("#signa_reseptur").val("");
        $("#jumlah_hari").val(1);
        $("#etiket").val("");

        $('.save-non-racikan').prop('disabled', true);

        // $("#satuan_terpilih").html("");
        // $("#text_stok_tersedia").html("0");
    }

    function obatalkesNonRacikanChange (id, is_others, selected) {
        if (typeof selected !== "undefined") {

            // set data apotek
            apotek.list_stok[selected.obatalkes_id] = selected;

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

            $("#satuan_terpilih").text(_satuankecil_nama);
            $("#text_stok_tersedia").text(_stok_sisa);
            var _text_konversi = "1 " + _satuankecil_nama + " = 1 " + _satuankecil_nama;
            $("#text_konversi").text(_text_konversi);

            $("#harga").val(_hargajual);
            $("#harga_konversi").val(_hargajual);
            $("#harga_reseptur").val(docoHelper.convertToRupiah(_hargajual));
            $("#harganetto_reseptur").val(_harganetto);

            $('#signa_reseptur').attr('disabled', false);
            $('#jumlah_hari').attr('disabled', false);
            $('#qty_reseptur').attr('disabled', false);
            $('#label_qty').attr('class', 'text-right required-reseptur');
            $('#label_signa').attr('class', 'text-right required-reseptur');
            $('#label_catatan').attr('class', 'text-right');
            if ($("#signa_reseptur").val() == undefined) {
                clearChange();
            }
            $('.save-non-racikan').prop('disabled', false);
            $('.is-kronis-nr').prop('disabled', false).uniform();
        } else {
            $("#satuankecil_id").val("").trigger("change");
            $("#satuankecil_id").prop("disabled", true);
            $('.satuandefault_nama').html('');
            $('.konversi').html('');
            $("#stok_sisa").val(0);
            $("#text_stok_tersedia").text("0");
            $("#signa_id").val("").trigger("change");
        }

        if (id == 0) {
            $("#signa_reseptur").val("").trigger("change");
            $('#qty_reseptur').val("");
            $("#jumlah_hari").val("").trigger("change");

            $('#label_signa').attr('class', 'text-right');
            $('#signa_reseptur').attr('disabled', true);
            $('#jumlah_hari').attr('disabled', true);
            $('#qty_reseptur').attr('disabled', true);
            $('#label_qty').attr('class', 'text-right');
            $('#label_catatan').attr('class', 'text-right required-reseptur');
            $('.save-non-racikan').prop('disabled', false);
            if (is_others) {
                $('.is-kronis-nr').prop('disabled', true).uniform();
            }
        }
    }
})
