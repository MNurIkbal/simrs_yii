/*
* @Author: rizqi_fitrianto
* @Date:   2018-07-05 09:48:23
 * @Last Modified by: dedeherdiana
 * @Last Modified time: 2022-11-07 12:21:55
*/
/**
 * Last Modified by: Dede Herdiana
 * A product of PT. CRN
 * Powered by Sirs
 */

// Global Variable
var tmpPenjamin = [];
var list_komponen = [];
var tampung_komponen = [];
var komponenDetailOrigin = []; // untuk menampung komponen tarif default
var isEditKomponen = isSetHarga = false

var sts_tindakan = document.getElementById("status-tindakan");
var sts_paket = document.getElementById("status-paket");
var sts_paket_mcu = document.getElementById("status-paket-mcu");
var btn_detail_mcu = document.getElementById("btn-show-detail-mcu");

$('input[name="TarifTindakanForm[tindakanpaket]"]').on('change', function () {
    changeRadio($(this).val());
    var elementId = $(this).attr("id");
    if (elementId == "RADIO-PAKET") {
        $(".field-kamar_ruangan_id").hide();
    }
})

var contains = function (needle) {
    // Per spec, the way to identify NaN is that it is not equal to itself
    var findNaN = needle !== needle;
    var indexOf;

    if (!findNaN && typeof Array.prototype.indexOf === 'function') {
        indexOf = Array.prototype.indexOf;
    } else {
        indexOf = function (needle) {
            var i = -1, index = -1;

            for (i = 0; i < this.length; i++) {
                var item = this[i];

                if ((findNaN && item !== item) || item === needle) {
                    index = i;
                    break;
                }
            }
            return index;
        };
    }
    return indexOf.call(this, needle) > -1;
};

if(_id != '') {
    $('#log-tarif').prop('disabled', false)
}
else {
    $('#log-tarif').prop('disabled', true)
    $(document).on("change", "#total_harga_tindakan", function (e) {
        
        $('.harga-komponen').val(0)
        $('.persentase-komponen').val(0)
        $('.total-harga').text(0)

        e.preventDefault();
        var _harga = docoHelper.convertToAngka($(this).val());
        var _komponenDefault = {
            komponentarif_nama: komponenRs.komponentarif_nama,
            persentase_komponen: '',
            harga: _harga,
            tariftindakan_id: '',
            is_deleted: false,
            komponentarif_id: komponenRs.komponentarif_id,
            komponentarif_kode: komponenRs.komponentarif_kode,
        }
        if(komponen.length == 0) {
            komponen.push(_komponenDefault);
            resetTableKomponen()
            sumHarga()
        }
    });
    
}

$('#btn-submit-tarif').on('click', function (event) {
    if(is_mcu && !status_edit){
        $('.select-paket').prop('disabled', false)
        $('.selectKelas').prop('disabled', false)
        $('#penjamin_id').prop('disabled', false)
        $('#perdatarif_id').prop('disabled', false)
        $('#carabayar_id').prop('disabled', false)
    }
    var _form = $('#tarif-form').serializeArray();
    var _url = $('#tarif-form').attr('action');
    var _total_komponen = docoHelper.convertToAngka($(".total-harga").text().replace('Rp. ', ''));
    var _total_harga = docoHelper.convertToAngka($(".total_harga_tindakan").val());
    if($('input[name="TarifTindakanForm[tindakanpaket]"]:checked').val() != 'PAKET') {
        var is_persentase = $("input[name='TarifTindakanForm[is_persentase]']:checked").val()
        var _message = (is_persentase == 1) ? 'Total Nominal Harga tidak boleh kurang atau melebihi Total Harga yang ditetapkan' : 'Persentase tidak boleh kurang atau melebihi 100%';
        if(_total_harga != _total_komponen) {
            docoNotification('error', 'Proses Gagal!', _message);
            return false;
        }
    }

    var _list_komponen = JSON.stringify(list_komponen)
    _form.push({
        name: 'list_komponen',
        value: _list_komponen
    });
    _form.push({
        name: 'is_akomodasi',
        value: is_akomodasi
    });

    $(this).docoForm("click", {
        url: _url,
        data: _form,
        success: function (data) {
            setTimeout(function () {
                $("#btn-back-tarif").click();
            }, 1000);
        },
    });
});

$(document).on('keydown', null, 'alt+s', function (event) {
    $("##content-tarif, #btn-submit-tarif").click();
});

$(document).on("keyup", "#total_harga_tindakan", function (e) {
    $('.harga-komponen').val(0)
    $('.persentase-komponen').val(0)
    $('.total-harga').text(0)
    e.preventDefault();
    docoHelper.convertToRupiah($(this).val());
});

$(document).on("change", "#persencyto_tindakan", function (e) {
    e.preventDefault();
    docoHelper.convertToDecimal(this, '.', ',', true);
});

$(document).on("change", "#persen_penyulit", function (e) {
    e.preventDefault();
    docoHelper.convertToDecimal(this, '.', ',', true);
});

$(document).on("change", "#persendiskon_tindakan", function (e) {
    e.preventDefault();
    docoHelper.convertToDecimal(this, '.', ',', true);
});

$('.select2-selection__clear').remove();

$(document).on('change', "#tariftindakanform-kelaspelayanan_id", function () {
    kelaspelayanan_id = $(this).val();
    $("#kamar_ruangan_id").docoPaginationSelec2(
        config = {
            placeholder: "-- Cari Kamar --",
            _api: '/master/tarif-tindakan/get-data-kamar?kelaspelayanan_id=' + kelaspelayanan_id,
        }
    );
    showButtonDetailMcu()
});

$(document).on("change", "#carabayar_id", function () {
    var data = $("#carabayar_id").select2("data")
    var carabayar_id = data[0].id;
    $("#penjamin_id").docoPaginationSelec2(
        config = {
            placeholder: "-- Pilih Penjamin --",
            _api: "/master/master-api/get-list-penjamin?carabayar_id=" + carabayar_id,
        }
    );
});

$(document).ready(function () {
    $('.select2-selection__clear').remove();
    $('#kamar_ruangan_id').select2();
    $('#tariftindakanform-perdatarif_id').select2();
    $('.selectKelas').select2();
    $('#carabayar_id').select2();
    $('#penjamin_id').select2();
    $('#dokter_id').select2();
    $("#label_paket").attr("style", "width:96% !important; margin-left:10px;");
    $("#is_active").attr("style", "width:96% !important; margin-left:10px;");
    $(".input-group").attr("style", "padding: 0px 10px;");
    $("#persencyto_tindakan").attr("style", "padding: 0px 10px; !important");
    $("#persendiskon_tindakan").attr("style", "padding: 0px 10px; !important");
    $("#persen_penyulit").attr("style", "padding: 0px 10px; !important");
    $(".field-kamar_ruangan_id").hide();
    if(status_edit){
        $('#btn-reset-tarif').prop('disabled',true)
    }else{
        $('#btn-reset-tarif').prop('disabled',false)
    }

    if (is_akomodasi == 1) {
        $(".field-kamar_ruangan_id").show();
        $(".field-kamar_ruangan_id").addClass('required');
    }

    $(".selectKomponen").docoPaginationSelec2(
        config = {
            placeholder: "-- Pilih Komponen --",
            _api: "/master/tarif-tindakan/get-list-komponen",
        }
    );

    var opsiKamar = {
        id: kamarruangan_id,
        text: kamar,
    }

    if (Object.keys(opsiKamar).length > 0) {
        if ($('#kamar_ruangan_id').find("option[value='" + opsiKamar.id + "']").length) {
            $('#kamar_ruangan_id').val(opsiKamar.id).trigger('change');
        } else {
            var newOption = new Option(opsiKamar.text, opsiKamar.id, true, true);
            $('#kamar_ruangan_id').append(newOption).trigger('change');
        }
    }

    if (Object.keys(komponenjson).length > 0) {
        $('input:radio[name="TarifTindakanForm[tindakanpaket]"][value="' + tindakanpaket + '"]').prop('checked', true);
        if (tipepaket == 'TINDAKAN') {
            loadKomponen();
            populateData($('.select-tindakan'), opsitindakanpaket);
            $('.select-tindakan').attr('disabled', false)
            $('.select-paket').prop('disabled', true);

            $('#RADIO-PAKET').attr('disabled', true);
            $('.select-tindakan').attr('disabled', true);

            $('#edit-tindakan').prop('disabled', false);
            $('#edit-paket').prop('disabled', true);
            $('#val-tindakan').val($(".select-tindakan option:selected").val());

            sts_tindakan.style.display = "block";
            sts_paket.style.display = "none";
            if(sts_paket_mcu){
                sts_paket_mcu.style.display = "none";
            }
        } else {
            populateData($('.select-paket'), opsitindakanpaket);
            $('.select-paket').prop('disabled', false);
            $('.select-tindakan').prop('disabled', true);

            $('#RADIO-TINDAKAN').attr('disabled', true);
            $('.select-paket').attr('disabled', true);

            $('#edit-paket').prop('disabled', false);
            $('#edit-tindakan').prop('disabled', true);
            $('#val-paket').val($(".select-paket option:selected").val());
            $(".field-kamar_ruangan_id").hide();
            sts_tindakan.style.display = "none";
            if(is_mcu){
                sts_paket.style.display = "none";
                sts_paket_mcu.style.display = "block";
                generateDetailMcu();
            }else{
                sts_paket.style.display = "block";
            }
        }
        $('#carabayar').prop('disabled', true)
        $('.select-penjamin').prop('disabled', true)
    }

    if (Object.keys(opsiPerda).length > 0) {
        if ($('.selectPerda').find("option[value='" + opsiPerda.id + "']").length) {
            $('.selectPerda').val(opsiPerda.id).trigger('change');
        } else {
            var newOption = new Option(opsiPerda.text, opsiPerda.id, true, true);
            $('.selectPerda').append(newOption).trigger('change');
        }
    }

    if (Object.keys(opsiKelas).length > 0) {
        if ($('.selectKelas').find("option[value='" + opsiKelas.id + "']").length) {
            $('.selectKelas').val(opsiKelas.id).trigger('change');
        } else {
            var newOption = new Option(opsiKelas.text, opsiKelas.id, true, true);
            $('.selectKelas').append(newOption).trigger('change');
        }
    }

    if (Object.keys(opsiCaraBayar).length > 0) {
        if ($('#carabayar_id').find("option[value='" + opsiCaraBayar.id + "']").length) {
            $('#carabayar_id').val(opsiCaraBayar.id).trigger('change');

        } else {
            var newOption = new Option(opsiCaraBayar.text, opsiCaraBayar.id, true, true);
            $('#carabayar_id').append(newOption).trigger('change');
        }
    }

    if (Object.keys(opsiPenjamin).length > 0) {
        if ($('#penjamin_id').find("option[value='" + opsiPenjamin.id + "']").length) {
            $('#penjamin_id').val(opsiPenjamin.id).trigger('change');
        } else {
            var newOption = new Option(opsiPenjamin.text, opsiPenjamin.id, true, true);
            $('#penjamin_id').append(newOption).trigger('change');
        }
    }

    if (Object.keys(opsiDokter).length > 0) {
        if ($('#dokter_id').find("option[value='" + opsiDokter.id + "']").length) {
            $('#dokter_id').val(opsiDokter.id).trigger('change');
        } else {
            var newOption = new Option(opsiDokter.text, opsiDokter.id, true, true);
            $('#dokter_id').append(newOption).trigger('change');
        }
    }

    $(".selectKelas").docoPaginationSelec2(
        config = {
            placeholder: "-- Pilih Kelas Pelayanan --",
            _api: "/master/master-api/get-list-kelas",
        }
    );

    $("#carabayar_id").docoPaginationSelec2(
        config = {
            placeholder: "-- Pilih Cara Bayar --",
            _api: "/master/master-api/get-list-carabayar",
        }
    );

    $("#kamar_ruangan_id").docoPaginationSelec2(
        config = {
            placeholder: "-- Cari Kamar --",
            _api: '/master/tarif-tindakan/get-data-kamar?kelaspelayanan_id=' + opsiKelas.id,
        }
    );

    $("#dokter_id").docoPaginationSelec2(
        config = {
            placeholder: "-- Pilih Dokter --",
            _api: "/master/master-api/get-list-dokter",
        }
    );

    $('.selectPerda').select2({
        placeholder: '',
        minimumInputLength: 1,
        ajax: {
            url: '/master/tarif-tindakan/get-perda',
            dataType: 'json',
            quietMillis: 250,
            data: function (term, page) {
                return {
                    q: term,
                    page: page
                }
            },
            processResults: function (data) {
                return {
                    results: data.result
                };
            }
        },
        dropdownCssClass: 'bigdrop',
        escapeMarkup: function (m) { return m; },
    });

    /*select-paket start*/
    $('.select-paket').select2({
        placeholder: '- Pilih Paket -',
        minimumInputLength: 3,
        ajax: {
            url: '/master/tarif-tindakan/get-paket',
            delay: 500, // Number of milliseconds before triggering the request
            dataType: 'json',
            quietMillis: 250,
            data: function (term, page) {
                return {
                    q: term,
                    page: page
                }
            },
            processResults: function (data) {
                return {
                    results: data.result
                };
            }
        },
        dropdownCssClass: 'bigdrop',
        escapeMarkup: function (m) { return m; },
    });
    /*select-paket end*/

    /*select-tindakan start*/
    $('.select-tindakan').select2({
        placeholder: '- Pilih Tindakan -',
        minimumInputLength: 3,
        ajax: {
            url: '/master/tarif-tindakan/get-tindakan',
            delay: 500, // Number of milliseconds before triggering the request
            dataType: 'json',
            quietMillis: 250,
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1
                }
            },
            processResults: function (data) {
                return {
                    results: data.result
                };
            }
        },
        dropdownCssClass: 'bigdrop',
        escapeMarkup: function (m) { return m; },
    }).on('select2:select', function (e) {
        var kelompoktindakan_persencyto = e.params.data.kelompoktindakan_persencyto
        var kelompoktindakan_persendiskon = e.params.data.kelompoktindakan_persendiskon
        is_akomodasi = e.params.data.is_akomodasi;
        is_akomodasi = (is_akomodasi) ? 1 : 0;

        $('#persencyto_tindakan').val(docoHelper.valToDecimal(kelompoktindakan_persencyto));
        $('#persendiskon_tindakan').val(docoHelper.valToDecimal(kelompoktindakan_persendiskon));
        if (is_akomodasi) {
            $(".field-kamar_ruangan_id").show();
            $(".field-kamar_ruangan_id").addClass('required');
        }
        else {
            $(".field-kamar_ruangan_id").hide();
        }
    });
    /*select-tindakan end*/

    $('.select-tindakan').on("select2:selecting", function (e) {
        var data = e.params.args.data;
        $("#tariftindakanform-persencyto_tindakan").val(data.datavalue.kelompoktindakan_persencyto);
        $("#tariftindakanform-persendiskon_tindakan").val(data.datavalue.kelompoktindakan_persendiskon);
    });

    // $('input[name="TarifTindakanForm[is_persentase]"]').trigger('change')
})

$('.select-paket').on('change', function (e) {
    var getPaket = $(".select-paket option:selected").data()
    var dataTindakan = []
    if (getPaket != undefined) {
        var _dataValue = $(".select-paket option:selected").data();
        is_mcu = 1;
        tipepaket_id = $(this).val()
        if (Object.keys(_dataValue).length >= 1) {
            tipepaket_id = $(".select-paket option:selected").data().data.datavalue.tipepaket_id
            is_mcu = $(".select-paket option:selected").data().data.datavalue.is_mcu
            
        }

        list_komponen = []
        generateTableDetailTindakan()
        
        if(!is_mcu){
            $('#dokter_id').prop('disabled', false)
            $.ajax({
                type: 'GET',
                url: '/master/tarif-tindakan/get-data-paket-detail?id=' + tipepaket_id + '&is_mcu=' + is_mcu,
                dataType: 'JSON',
                success: function (res) {
                    res.forEach(function (val, key) {
                        dataTindakan = {
                            tipepaket_id: val.tipepaket_id,
                            daftartindakan_id: val.tindakan_paket_id,
                            tindakan: val.tindakan_paket_nama,
                            kelompok: val.kelompoktindakan_nama != null ? val.kelompoktindakan_nama : '-',
                            ruangan: val.instalasi_nama != null ? val.instalasi_nama + ' - ' + val.ruangan_nama : '-',
                            ruangan_id: val.ruangan_id,
                            harga: 0,
                            komponen: [],
                            ruangan_id: val.ruangan_id,
                        }
                        list_komponen.push(dataTindakan)
                    })
    
                    generateTableDetailTindakan()
                    list_komponen.forEach(function (val, key) {
                        komponenjson.forEach(function (v, k) {
                            if (val.daftartindakan_id === v.daftartindakan_id && val.ruangan_id === v.ruangan_id) {
                                var input_komponen = {
                                    ruangan_id: val.ruangan_id,
                                    komponen_id: v.komponentarif_id,
                                    label_komponen: v.komponentarif_nama,
                                    nominal: docoHelper.convertToRupiah(v.harga_tariftindakan),
                                    nominal_angka: parseFloat(v.harga_tariftindakan),
                                }
                                list_komponen[key].komponen.push(input_komponen)
                                generateTableKomponen(key)
                            }
                        })
                    })
                },
            });
            sts_tindakan.style.display = "none";
            sts_paket.style.display = "block";
        }else{
            $('#dokter_id').prop('disabled', true)
            showButtonDetailMcu();
            sts_paket.style.display = "none";
            sts_paket_mcu.style.display = "block";
        }

    }
})

$('.add-row').on('click', function (e) {
    e.preventDefault()
    if ($('.selectKomponen').val() != '') {
        var value = $('.selectKomponen').select2('data')[0].datavalue
        addData(value)
    }
})

function removeCompRow(com) {
    var tmpID = $(com).data('counter')
    $(com).closest('tr').remove()
    komponen.splice(tmpID, 1)
    resetTableKomponen()
    sumHarga()
}

var addData = function (_value) {
    $('.selectKomponen').val('').trigger('change');
    var cek_komponen = $.inArray(_value.komponentarif_kode, tampung_komponen);
    if (cek_komponen < 0) {
        _value.persentase_komponen = 0
        _value.harga = 0
        _value.tariftindakan_id = ''
        _value.is_deleted = false
        tampung_komponen.push(_value.komponentarif_kode);
        komponen.push(_value);
        resetTableKomponen()
        sumHarga()
    } else {
        docoNotification('error', 'Gagal Input', 'Komponen yang sama tidak bisa di input 2x');
    }
}

var addRow = function (komp) {
    var _table = $('.tbl-komponen')
    if (Object.keys(komponen).length > 0) {
        if (!$('.tr-default').hasClass('hidden')) {
            $('.tr-default').addClass('hidden')
        }
        var key = komp
        var v = komponen[key]
        var row = '';
        row += '<tr class="tr-load">';
        row += '<td>' + v.komponentarif_nama + '</td>';
        row += '<td> <div class="form-group"><input name="TarifTindakanForm[persentase][' + key + ']" value="' + parseInt(v.persentase_komponen) + '" class="text-right form-control persentase-komponen doco-number unsigned" data-key=' + key + '></div> </td>';
        row += '<td> <div class="form-group"><input name="TarifTindakanForm[harga_tariftindakan][' + key + ']" value="' + v.harga + '" class="text-right form-control harga-komponen doco-number unsigned" data-key=' + key + '></div> </td>';
        row += '<td> <input type="hidden" name="TarifTindakanForm[komponentarif_id][' + key + ']" value="' + v.komponentarif_id + '"> <input type="hidden" class="is-deleted-' + key + '" name="TarifTindakanForm[is_deleted][' + key + ']" value="0"> <input type="hidden" name="TarifTindakanForm[tariftindakan_id][' + key + ']" value="' + v.tariftindakan_id + '"> <a class="btn btn-danger btn-sm remove-row" onclick="removeCompRow(this)" data-counter="' + key + '"><i class="fa fa-trash"></i></a> </td>';
        if ($('.tr-total').length == 0) {
            rowtotal = '';
            rowtotal += '<tr class="tr-total">';
            rowtotal += '<td>Total</td>';
            rowtotal += '<td class="total-harga" align="right">Rp. ' + komponentotal.harga + '</td><td></td>';
            rowtotal += '</tr>';
            _table.find('tfoot').append(rowtotal)
        }
        _table.find('tbody').append(row)
    } else {
        if (!$('.tr-default').hasClass('hidden')) {
            $('.tr-default').addClass('hidden')
        }
    }
}

$(document).on('keyup', '.persentase-komponen', function () {
    var _this = $(this).val();
    var key = $(this).attr('data-key')
    var _totalHarga = $('.total_harga_tindakan').val();

    if(!is_mcu){
        if(docoHelper.convertToAngka(_this) > 100) {
            $(this).val(parseInt(komponen[key].persentase_komponen))
            $('#harga-komponen-'+key).val(docoHelper.convertToRupiah(komponen[key].harga))
            docoNotification('error', 'Proses Gagal', 'Persentase tidak boleh melebihi 100%!');
            return false;
        }

        if(_totalHarga == '' || typeof _totalHarga == 'undefined') {
            $(this).val(0)
            $('#harga-komponen-'+key).val(0)
            docoNotification('error', 'Proses Gagal', 'Total Harga Belum di Isi!');
            return false;
        }
        else {
            var _harga = (_this/100) * docoHelper.convertToAngka(_totalHarga)
            $('#harga-komponen-'+key).val(docoHelper.convertToRupiah(_harga))
            komponen[key].persentase_komponen = _this
            komponen[key].harga = _harga
            sumHarga()
        }
    }
})

$(document).on('keyup', '.harga-komponen', function () {
    var key = $(this).attr('data-key')
    if(!is_mcu){
        komponen[key].harga = docoHelper.convertToAngka($(this).val())
    }
})

$(document).on('change', '.harga-komponen', function () {
    sumHarga()
});

var sumHarga = function () {
    var total = 0;
    tampung_komponen = []
    $.each(komponen, function (k, v) {
        total += Math.round(v.harga)
        tampung_komponen.push(v.komponentarif_kode)
    })
    $('.total-harga').empty().html('Rp. ' + docoHelper.convertToRupiah(total));
}

/*
var checkPaketTindakan = function(){
        console.log(opsitindakanpaket);
        var jenis = $('input:radio[name="TarifTindakanForm[tindakanpaket]"]:checked').val();
        console.log( jenis );
    if(jenis != '' && jenis == 'TINDAKAN'){
        $('input[name="TarifTindakanForm[tindakanpaket]"][value=' + $('.tindakan-paket').val() + ']').prop('checked',true)
        populateData($('.select-tindakan'), opsitindakanpaket)
        changeRadio($('.tindakan-paket').val())
    }else if( jenis != '' && jenis == 'PAKET'){
        $('input[name="TarifTindakanForm[tindakanpaket]"][value=' + $('.tindakan-paket').val() + ']').prop('checked',true)
        populateData($('.select-paket'), opsitindakanpaket)
        changeRadio($('.tindakan-paket').val())
    }
}
*/
var changeRadio = function (val) {
    // console.log(val)
    if (val == 'TINDAKAN') {
        $('.select-tindakan').attr('disabled', false)
        $('.select-paket').attr('disabled', true)
        $('.select-paket').val('').trigger('change')

        sts_tindakan.style.display = "block";
        sts_paket.style.display = "none";
        if(sts_paket_mcu){
            sts_paket_mcu.style.display = "none";
        }
        is_mcu = false;
        $('#is_persentase_tindakan').html($('#is_persentase').html())
        changeRadioPersentase()
    } else {
        $('#is_persentase').html($('#is_persentase_tindakan').html())
        $('#total-harga-mcu').text(0)
        changeRadioPersentase()
        $('#persencyto_tindakan').val('0,00');
        $('#persendiskon_tindakan').val('0,00');
        $('#persen_penyulit').val('0,00');

        $('.select-tindakan').attr('disabled', true)
        $('.select-paket').attr('disabled', false)
        $('.select-tindakan').val('').trigger('change')

        sts_tindakan.style.display = "none";
        sts_paket.style.display = "none";
    }
    $('.tindakan-paket').val(val)
}

var populateData = function (_class, _obj) {
    // console.log(_obj);
    if (_class.find("option[value='" + _obj.id + "']").length) {
        _class.val(_obj.id).trigger('change');
    } else {
        var newOption = new Option(_obj.text, _obj.id, true, true);
        _class.append(newOption).trigger('change');
    }
}

var loadKomponen = function () {
    var _table = $('.tbl-komponen');
    var total_tarif_tindakan = 0;
    if (Object.keys(komponenjson).length > 0) {
        if (!$('.tr-default').hasClass('hidden')) {
            $('.tr-default').addClass('hidden')
        }
        var row = '';
        $("#btn-reset").hide();
        var is_persentase = $("input[name='TarifTindakanForm[is_persentase]']:checked").val()
        var readonlyHarga = (is_persentase == 1) ? '' : 'readonly'
        var readonlyPersentase = (is_persentase == 1) ? 'readonly' : ''

        $.each(komponenjson, function (k, v) {
            counter++;
            total_tarif_tindakan = total_tarif_tindakan + parseInt(v.harga_tariftindakan);
            var key = counter - 1;
            komponen[key] = { persentase_komponen: v.persentase_komponen, harga: v.harga_tariftindakan, komponentarif_id: v.komponentarif_id, komponentarif_kode: v.komponentarif_kode, komponentarif_nama: v.komponentarif_nama, tariftindakan_id: v.tariftindakan_id, is_deleted: false }
            tampung_komponen.push(v.komponentarif_kode);
            var _persentase = v.persentase_komponen
            _persentase = (readonlyPersentase != '') ? '' : parseInt(_persentase)

            row += '<tr class="tr-load">';
            row += '<td>' + v.komponentarif_nama + '</td>';
            row += '<td> <div class="form-group"><input name="TarifTindakanForm[persentase][' + key + ']" value="' + _persentase + '" id="persentase-komponen-'+key+'" class="text-right form-control persentase-komponen doco-number unsigned" '+readonlyPersentase+' data-key=' + key + '></div> </td>';
            row += '<td> <div class="form-group"><input name="TarifTindakanForm[harga_tariftindakan][' + key + ']" value="' + docoHelper.convertToRupiah(v.harga_tariftindakan) + '" id="harga-komponen-'+key+'" class="text-right form-control harga-komponen doco-number unsigned" '+readonlyHarga+' data-key=' + key + '></div> </td>';
            row += '<td> <input type="hidden" name="TarifTindakanForm[komponentarif_id][' + key + ']" value="' + v.komponentarif_id + '"> <input type="hidden" class="is-deleted" name="TarifTindakanForm[is_deleted][' + key + ']" value="0"> <input type="hidden" name="TarifTindakanForm[tariftindakan_id][' + key + ']" value="' + v.tariftindakan_id + '"> <a class="btn btn-danger btn-sm remove-row" onclick="removeCompRow(this)" data-counter="' + key + '"><i class="fa fa-trash"></i></a> </td>';
        })
        _table.find('tbody').append(row)
        rowtotal = '';
        rowtotal += '<tr class="tr-total">';
        rowtotal += '<td>Total</td>';
        rowtotal += '<td>&nbsp;</td>';
        rowtotal += '<td class="total-harga" align="right">Rp. ' + docoHelper.convertToRupiah(total_tarif_tindakan) + '</td><td></td>';
        rowtotal += '</tr>';
        _table.find('tfoot').append(rowtotal)
    } else {
        if (!$('.tr-default').hasClass('hidden')) {
            $('.tr-default').addClass('hidden')
        }
    }
}

var resetTableKomponen = function () {
    var _table = $('.tbl-komponen');
    var total_tarif_tindakan = 0;
    tampung_komponen = []
    if (Object.keys(komponen).length > 0) {
        $('.tr-default').remove()
        $('.tr-load').remove()
        $('.tr-total').remove()
        var row = '';
        $.each(komponen, function (key, val) {
            tampung_komponen.push(val.komponentarif_kode);
            total_tarif_tindakan = total_tarif_tindakan + parseInt(val.harga_tariftindakan);
            var readonlyHarga = readonlyPersentase = '';
            var is_persentase = $("input[name='TarifTindakanForm[is_persentase]']:checked").val()
            var readonlyHarga = (is_persentase == 1) ? '' : 'readonly'
            var readonlyPersentase = (is_persentase == 1) ? 'readonly' : ''
            if(is_persentase == 1) {
                val.persentase_komponen = '';
            }
            else {
                val.persentase_komponen = typeof val.persentase_komponen == 'undefined' ? '' : parseInt(val.persentase_komponen);
            }
            row += '<tr class="tr-load">';
            row += '<td>' + val.komponentarif_nama + '</td>';
            row += '<td> <div class="form-group"><input name="TarifTindakanForm[persentase][' + key + ']" value="' + val.persentase_komponen + '" id="persentase-komponen-'+key+'" class="text-right form-control persentase-komponen doco-number unsigned" '+readonlyPersentase+' data-key=' + key + '></div> </td>';
            row += '<td> <div class="form-group"><input name="TarifTindakanForm[harga_tariftindakan][' + key + ']" value="' + docoHelper.convertToRupiah(val.harga) + '" id="harga-komponen-'+key+'" class="text-right form-control harga-komponen doco-number unsigned" '+readonlyHarga+' data-key=' + key + '></div> </td>';
            row += '<td> <input type="hidden" name="TarifTindakanForm[komponentarif_id][' + key + ']" value="' + val.komponentarif_id + '"> <input type="hidden" class="is-deleted-' + key + '" name="TarifTindakanForm[is_deleted][' + key + ']" value="0"> <input type="hidden" name="TarifTindakanForm[tariftindakan_id][' + key + ']" value="' + val.tariftindakan_id + '"> <a class="btn btn-danger btn-sm remove-row" onclick="removeCompRow(this)" data-counter="' + key + '"><i class="fa fa-trash"></i></a> </td>';
        })
        _table.find('tbody').append(row)
        rowtotal = '';
        rowtotal += '<tr class="tr-total">';
        rowtotal += '<td>Total</td>';
        rowtotal += '<td>&nbsp;</td>';
        rowtotal += '<td class="total-harga" align="right">Rp. ' + docoHelper.convertToRupiah(total_tarif_tindakan) + '</td><td></td>';
        rowtotal += '</tr>';
        _table.find('tfoot').append(rowtotal)
    } else {
        if (!$('.tr-default').hasClass('hidden')) {
            $('.tr-default').addClass('hidden')
        }
    }
}

// for generate table from input form data
function generateTableDetailTindakan() {
    $('.tr-detail-default').hide()
    $('.tbl-detail-tindakan > tbody > tr').not('tr.tr-detail-default').remove()
    var _html = ""
    var no = 1;
    var totalHarga = 0;
    tmpTindakan = [];
    list_komponen.forEach(function (val, key) {
        totalHarga += parseInt(val.harga);
        tmpTindakan[val.daftartindakan_id] = val
        _html += `
        <tr>
            <td>${no++}.</td>
            <td>${val.tindakan}</td>
            <td>${val.kelompok}</td>
            <td>${val.ruangan}</td>
            <td align="right">Rp. ${docoHelper.convertToRupiah(val.harga)}</td>
            <td><button type='button' data-id='${key}' action='/master/tarif-tindakan/add-component?id=${val.tipepaket_id}-${val.daftartindakan_id}-${key}' class='add-tbl-detail-tindakan btn btn-info btn-labeled btn-xs add btn-block' data-toggle='modal' data-target='#modal_backdrop' data-width='50%'><b><i class="fa fa-plus-circle"></i></b>Tambah</button></td>
        </tr>
        `
    })
    $('.tbl-detail-tindakan > tbody').append(_html);
    $('#total_harga').val(docoHelper.convertToRupiah(totalHarga))
}

// for generate table from input form data
function generateTableDetailTindakanMcu() {
    $('.tr-detail-default').hide()
    $('.tbl-detail-tindakan > tbody > tr').not('tr.tr-detail-default').remove()
    
    var is_persentase = $("input[name='TarifTindakanForm[is_persentase]']:checked").val()
    var readonlyHarga = (is_persentase == 1) ? '' : 'readonly'
    var readonlyPersentase = (is_persentase == 1) ? 'readonly' : ''
    var _html = ""
    var no = 1;
    var totalHarga = 0;
    var totalHargaOrigin = 0;
    tmpTindakan = [];
    list_komponen.forEach(function (val, key) {
        totalHarga += parseInt(val.harga);
        totalHargaOrigin += parseInt(val.harga_origin);
        tmpTindakan[val.daftartindakan_id] = val
        _html += `
        <tr>
            <td>${no++}.</td>
            <td>${val.tindakan}</td>
            <td>${val.kelompok}</td>
            <td>${val.ruangan}</td>
            <td align="right">${docoHelper.convertToRupiah(val.harga_origin)}</td>
            <td> <div class="form-group"><input name="TarifTindakanForm[persentase][${key}]" value="${(val.persentase_komponen!=0 ? val.persentase_komponen : '')}" class="text-right form-control persentase-komponen doco-number unsigned" ${readonlyPersentase} data-key='${key}'></div> </td>
            <td> <div class="form-group"><input name="TarifTindakanForm[harga_tariftindakan][${key}]" value="${docoHelper.convertToRupiah(val.harga_penyesuaian)}" class="text-right form-control harga-komponen doco-number unsigned" ${readonlyHarga} data-key='${key}'></div> </td>
            <td><button type='button' data-id='${key}' action='/master/tarif-tindakan/add-component?id=${val.tipepaket_id}-${val.daftartindakan_id}-${key}&is_mcu=${is_mcu}' class='add-tbl-detail-tindakan btn btn-info btn-labeled btn-xs add btn-block' data-toggle='modal' data-target='#modal_backdrop' data-width='50%'>Lihat</button></td>
        </tr>
        `
    })
    _html += `
        <tr>
            <td> &nbsp; </td>
            <td colspan= "3"><b>TOTAL HARGA </b></td>
            <td align="right">Rp. ${docoHelper.convertToRupiah(totalHargaOrigin)}</td>
            <td> </td>
            <td align="right" id="total-harga-mcu">Rp. ${docoHelper.convertToRupiah(totalHarga)}</td>
            <td></td>
        </tr>
        `
    $('.tbl-detail-tindakan > tbody').append(_html);
    $('#total_harga_mcu').val(docoHelper.convertToRupiah(totalHarga))
}

// for generate table from input form data
function generateTableKomponen(param_id = null) {
    $('.isi-table').hide()
    $('#table-add-component > tbody > tr').not('tr.isi-table').remove()
    var _html = ""
    var no = 1;
    var totalHargaKomponen = totalHargaOrigin = 0;
    tmpKomponen = [];

    list_komponen[param_id].komponen.forEach(function (val, key) {
        let nominalOrigin = val.nominal_origin;
        if(komponenDetailOrigin[val.ruangan_id]){
            komponenDetailOrigin[val.ruangan_id].forEach(function (v, k) {
                if(val.komponen_id == v.komponentarif_id && val.daftartindakan_id == v.daftartindakan_id){ 
                    nominalOrigin = v.harga_tariftindakan;
                }
            })
        }
        totalHargaOrigin += parseInt(nominalOrigin);
        totalHargaKomponen += parseFloat(val.nominal_angka);
        tmpKomponen[val.komponen_id] = val
        if(is_mcu){
            _html += `
            <tr>
                <td>${no++}.</td>
                <td>${val.label_komponen}</td>
                <td align="right" style="padding-right: 40px;">${docoHelper.convertToRupiah(nominalOrigin)}</td>
                <td><div class="form-group"><input name="TarifTindakanForm[harga_tariftindakan_komponen][${key}]" value="${val.nominal}" class="text-right form-control harga-komponen-paket doco-number unsigned" style="width:140px" data-id='${param_id}' data-key='${key}'></div> </td>
            </tr>
            `
        }else{
            _html += `
            <tr>
                <td>${no++}.</td>
                <td>${val.label_komponen}</td>
                <td>Rp. ${val.nominal}</td>
                <td><button type='button' data-id='${key}' class='delete-table-add-component btn btn-danger btn-labeled btn-xs delete btn-block'><b><i class="fa fa-trash"></i></b>Hapus </button></td>
            </tr>
            `
        }
    })
    $('#table-add-component > tbody').append(_html);
    $('#total_komponen_origin').val(docoHelper.convertToRupiah(totalHargaOrigin))
    $('#total_komponen').val(docoHelper.convertToRupiah(totalHargaKomponen))
    list_komponen[param_id].harga = totalHargaKomponen
    if(!is_mcu){
        generateTableDetailTindakan()
    }else{
        generateTableDetailTindakanMcu()
    }

    // for delete data one by one in table temporary
    $(`.delete-table-add-component`).on('click', function (e) {
        e.preventDefault()
        var tmpID = $(this).attr('data-id')
        total_komponen = docoHelper.convertToAngka($('#total_komponen').val()) - docoHelper.convertToAngka(list_komponen[list_id].komponen[tmpID]["nominal_angka"]);
        list_komponen[list_id].komponen.splice(tmpID, 1)
        generateTableKomponen(list_id)
        $("#total_komponen").val(docoHelper.convertToRupiah(total_komponen));
    })
}

$(document).on('click', "#log-tarif", function () {
    $("#log-tarif").attr("data-url",  "/master/tarif-tindakan/view-log?id=" + _id);
});

$('input[name="TarifTindakanForm[is_persentase]"]').change(function(){
    var _is_persentase = $(this).val()
    if(_is_persentase == 0) {
        $('.harga-komponen').prop("readonly", true);
        $('.harga-komponen').val(0)
        $('.persentase-komponen').prop("readonly", false);
        $('.persentase-komponen').prop("disabled", false);
        $('.persentase-komponen').each(function(index, value) {
            $(".persentase-komponen[data-key='"+index+"']").val(0).trigger('change'); 
          });
         if(!is_mcu){
            resetHargaKomponen()
         }
         $('.total-harga').text(0)

    }
    else {
        $('.persentase-komponen').each(function(index, value) {
            $(".persentase-komponen[data-key='"+index+"']").val(0).trigger('change'); 
          });
        $('.harga-komponen').prop("readonly", false);
        $('.persentase-komponen').prop("readonly", true);
        $('.persentase-komponen').prop("disabled", true);
        
        /**
         * tambah atau edit tarif, pilih persentase, pilih komponen, input harga komponen, lalu pilih nominal. harga pada nominal tidak kereset. seharusnya harga ke reset semua.
         */
         $('.harga-komponen').val(0).trigger('change')
         $('.total-harga').text(0)
         if(!is_mcu){
            resetHargaKomponen()
         }
    }
});

function showButtonDetailMcu(){
    if(is_mcu){
        if(typeof status_edit !='undefined' && !status_edit ){
            let tmpTipePaket = $('.select-paket').val();
            let tmpKelasPelayanan = $('.selectKelas').val();
            let tmpPenjaminId = $('#penjamin_id').val();
            let tmpPerdaTarif = $('#perdatarif_id').val();
            if(tmpTipePaket && tmpKelasPelayanan && tmpPenjaminId && tmpPerdaTarif){
                btn_detail_mcu.style.display = "block";
                list_komponen = [];
                generateTableDetailTindakanMcu();
            }else{
                if(btn_detail_mcu){
                    btn_detail_mcu.style.display = "none";
                }
            }
        }
    }else{
        if(btn_detail_mcu){
            btn_detail_mcu.style.display = "none";
        }
    }

}

$('#penjamin_id').on('change', function(){
    showButtonDetailMcu();
})

$('#perdatarif_id').on('change', function(){
    showButtonDetailMcu();
})

$('#btn-show-detail-mcu').on('click', function(){
    list_komponen = [];
    $('.select-paket').prop('disabled', true)
    $('.selectKelas').prop('disabled', true)
    $('#penjamin_id').prop('disabled', true)
    $('#perdatarif_id').prop('disabled', true)
    $('#carabayar_id').prop('disabled', true)
    generateDetailMcu();
})

function generateDetailMcu(){
    list_daftartindakan_id = []
    let tmpPerdaTarifId = $('#perdatarif_id').val();

    // Pengkodisian jika edit ambil dari tmppenjamin yang sudah didefine, kalo tambah ambil dari element yang dipilih.
    var valKelasPelayanan = $('.selectKelas').val();
    var valPenjaminId = $('#penjamin_id').val()
    if(valPenjaminId && !status_edit){
        tmpPenjaminId = valPenjaminId;
    }else{
        tmpPenjaminId = tmpPenjaminId
    }

    if(valKelasPelayanan && !status_edit){
        tmpKelasPelayanan = valKelasPelayanan;
    }else{
        tmpKelasPelayanan = tmpKelasPelayanan
    }
    showLoader()

    $.ajax({
        type: 'GET',
        url: '/master/tarif-tindakan/get-data-paket-detail?id=' + tipepaket_id + '&is_mcu=' + is_mcu+ '&kelaspelayanan_id=' + tmpKelasPelayanan + '&penjamin_id=' + tmpPenjaminId+ '&perdatarif_id=' + tmpPerdaTarifId +'&status_edit=' + status_edit,
        dataType: 'JSON',
        success: function (res) {
            let listPaketDetail = typeof res.listPaketDetail != 'undefined' ? res.listPaketDetail : res;
            if(listPaketDetail && listPaketDetail.length){
                listPaketDetail.forEach(function (val, key) {
                    dataTindakan = {
                        tipepaket_id: val.tipepaket_id,
                        daftartindakan_id: val.tindakan_paket_id,
                        tindakan: val.tindakan_paket_nama,
                        kelompok: val.kelompoktindakan_nama != null ? val.kelompoktindakan_nama : '-',
                        ruangan: val.instalasi_nama != null ? val.instalasi_nama + ' - ' + val.ruangan_nama : '-',
                        ruangan_id: val.ruangan_id,
                        harga: 0,
                        komponen: [],
                        ruangan_id: val.ruangan_id,
                        persentase_komponen: '',
                        harga_penyesuaian: 0,
                        harga_origin : 0,
                    }
                    list_komponen.push(dataTindakan)
                    list_daftartindakan_id.push(val.tindakan_paket_id)
                    hideLoader()
    
                })
            }
            if(list_komponen && list_komponen.length){
                list_komponen.forEach(function (val, key) {
                    var listKomponenDetail = []; // list komponen untuk detail paket mcu
                    if(typeof res.komponenDetail != 'undefined'){
                        if(typeof res.komponenDetail[val.ruangan_id] != 'undefined'){
                            listKomponenDetail = res.komponenDetail[val.ruangan_id];
                            /** define tarif normal */
                            listKomponenDetail.forEach(function (v, k) {
                                if (val.daftartindakan_id === v.daftartindakan_id ) {
                                    list_komponen[key].harga_origin += docoHelper.convertToAngka(parseFloat(v.harga_tariftindakan));
                                    if(!komponenDetailOrigin[val.ruangan_id]){
                                        komponenDetailOrigin[val.ruangan_id] = [];
                                    }
                                    komponenDetailOrigin[val.ruangan_id].push(v);
                                }
                            })
                        }
                    }

                    /** Untuk kondisi edit maka list komponen detail diisi (dioverride) dari (komponenJson) yang sudah disimpan sebelumnya
                     * alur :
                     * komponenJson selalu diset ketika edit dan array kossong ketika tambah
                     */
                    if(komponenjson.length){
                        listKomponenDetail = komponenjson; 
                    }
                    if(listKomponenDetail && listKomponenDetail.length){
                        listKomponenDetail.forEach(function (v, k) {
                            if (val.daftartindakan_id === v.daftartindakan_id && val.ruangan_id === v.ruangan_id) {
                                let tmpPersentase = typeof v.persentase_komponen != 'undefined' ? v.persentase_komponen : '';
                                tmpPersentase = parseFloat(tmpPersentase);
                                let tmpNominalOrigin = docoHelper.convertToAngka(parseFloat(v.harga_tariftindakan));
                                if(status_edit){
                                    if(tmpPersentase){
                                        tmpNominalOrigin = tmpNominalOrigin/(tmpPersentase/100);
                                    }else{
                                        tmpNominalOrigin = 0;
                                    }
                                }
                                var input_komponen = {
                                    ruangan_id: val.ruangan_id,
                                    komponen_id: v.komponentarif_id,
                                    label_komponen: v.komponentarif_nama,
                                    nominal: docoHelper.convertToRupiah(v.harga_tariftindakan),
                                    nominal_origin: tmpNominalOrigin,
                                    nominal_angka: parseFloat(v.harga_tariftindakan),
                                    harga_penyesuaian: 0,
                                    persentase_komponen: tmpPersentase,
                                }
                                list_komponen[key].komponen.push(input_komponen);
                                list_komponen[key].harga += docoHelper.convertToAngka(parseFloat(v.harga_tariftindakan));
                                if(status_edit){
                                    list_komponen[key].harga_penyesuaian += parseFloat(v.harga_tariftindakan);
                                }
                                if(tmpPersentase){
                                    list_komponen[key].persentase_komponen = tmpPersentase;
                                }
                                generateTableKomponen(key)
                            }
                        })

                        /** case edit jika penyesuaian pakai nominal : karena harga_originnya tidak disimpan untuk menghitung nominal origin, harus sudah terbentuk harga total dlu untuk tiap-tiap tindakan */
                        if(status_edit){
                            if( list_komponen[key].komponen &&  list_komponen[key].komponen.length){
                                list_komponen[key].komponen.forEach(function (v, k) {
                                    let tmpPersentase = typeof v.persentase_komponen != 'undefined' ? v.persentase_komponen : '';
                                    tmpPersentase = parseFloat(tmpPersentase);
                                    let tmpNominalOrigin = parseFloat(v.nominal_angka);
                                    if(status_edit){
                                        if(tmpPersentase){
                                            tmpNominalOrigin = tmpNominalOrigin/(tmpPersentase/100);
                                        }else{
                                            tmpNominalOrigin = tmpNominalOrigin/(val.harga/val.harga_origin); //(val.harga/val.harga_origin) buat cari persentase
                                        }
                                    }
                                    list_komponen[key].komponen[k].nominal_origin = tmpNominalOrigin
                                })
                            }
                        }
                    }
                })
            }
            generateTableDetailTindakanMcu()

        },
    });
}

function prorateKomponenDetailPaket(key){
    if(typeof list_komponen[key] != 'undefined'){
        let tmpKomponen = list_komponen[key];
        let persentase = 0;
        
        if(typeof tmpKomponen.persentase_komponen != 'undefined' && tmpKomponen.persentase_komponen > 0){
            persentase = (parseFloat(tmpKomponen.persentase_komponen)/100);
        }else if(typeof tmpKomponen.harga_penyesuaian != 'undefined' && tmpKomponen.harga_penyesuaian > 0){
            persentase = (parseFloat(tmpKomponen.harga_penyesuaian)/parseFloat(tmpKomponen.harga_origin));
            tmpKomponen.persentase_komponen = 0;
        }

        if (!is_mcu) {
            if((persentase*100) > 100){
                docoNotification('error', 'Proses Gagal', 'Nilai Tarif paket tidak bisa lebih besar dari harga normal!');
                persentase = 0;
                return false;
            }
        }
        list_komponen[key].harga_penyesuaian = persentase*parseFloat(tmpKomponen.harga_origin);
        tmpKomponen.komponen.forEach(function (v, k) {
            /** Blok prorate */
            let nominal= v.nominal_angka;
            if(persentase>0 && !isEditKomponen){
                nominal = (parseFloat(v.nominal_origin)*persentase).toFixed(2);
                if(typeof tmpKomponen.persentase_komponen != 'undefined' && tmpKomponen.persentase_komponen > 0){
                    list_komponen[key].komponen[k].persentase_komponen = tmpKomponen.persentase_komponen;
                }else{
                    list_komponen[key].komponen[k].persentase_komponen = 0;
                }
            }
            
            list_komponen[key].komponen[k].nominal = docoHelper.convertToRupiah(nominal);
            list_komponen[key].komponen[k].nominal_angka = nominal;
            hargaKomponen = $('input[name="TarifTindakanForm[harga_tariftindakan]['+key+']"]').val()
            // Reset Harga Apabila Harga Komponen di Set 0
            if(!isSetHarga){
                list_komponen[key].komponen[k].nominal = docoHelper.convertToRupiah(list_komponen[key].komponen[k].nominal_origin);
                list_komponen[key].komponen[k].nominal_angka = list_komponen[key].komponen[k].nominal_origin;
            }
            generateTableKomponen(key)
        })
    }
}

$(document).on('change', '.persentase-komponen', function () {
    var _this = $(this).val();
    var key = $(this).attr('data-key')
    if(is_mcu){
        if(docoHelper.convertToAngka(_this) > 100){
            let persentase_komponen = typeof list_komponen[key].persentase_komponen != 'undefined' ? list_komponen[key].persentase_komponen : 0;
            $(this).val(persentase_komponen)
            docoNotification('error', 'Proses Gagal', 'Persentase tidak boleh melebihi 100%!');
            return false;
        }
        list_komponen[key].persentase_komponen =  _this;
        list_komponen[key].harga_penyesuaian = 0;
        prorateKomponenDetailPaket(key)
    }
})

$(document).on('keyup', '.harga-komponen', function () {
    isEditKomponen = false
    isSetHarga = true
})

$(document).on('change', '.harga-komponen', function () {
    var key = $(this).attr('data-key')
    var _this = $(this).val();
    if(_this == 0){
        isSetHarga = false
    }
    if(is_mcu){
        if( parseFloat($(this).val()) > list_komponen[key].harga_origin){
            docoNotification('error', 'Proses Gagal', 'Nilai Tarif paket tidak bisa lebih besar dari harga normal!');
            $(this).val(0);
        }else{
            list_komponen[key].harga_penyesuaian = docoHelper.convertToAngka($(this).val());
            prorateKomponenDetailPaket(key)
        }
    }
})

$('#btn-reset-tarif').on('click', function(){
    
    $('.select-paket').prop('disabled', false)
    $('.selectKelas').prop('disabled', false)
    $('#penjamin_id').prop('disabled', false)
    $('#perdatarif_id').prop('disabled', false)
    $('#carabayar_id').prop('disabled', false)

    list_komponen = [];
    generateTableDetailTindakanMcu()
})

function resetHargaKomponen(){
    komponen.forEach(function (val, key) {
        komponen[key].harga = 0
        komponen[key].persentase_komponen = 0
    })
}

function changeRadioPersentase(){
    $('input[name="TarifTindakanForm[is_persentase]"]').change(function(){
        var _is_persentase = $(this).val()
        if(_is_persentase == 0) {
            $('.harga-komponen').prop("readonly", true);
            $('.harga-komponen').val(0)
            $('.persentase-komponen').prop("readonly", false);
            $('.persentase-komponen').prop("disabled", false);
            $('.persentase-komponen').each(function(index, value) {
                $(".persentase-komponen[data-key='"+index+"']").val(0).trigger('change'); 
              });
             if(!is_mcu){
                resetHargaKomponen()
             }
             $('.total-harga').text(0)
    
        }
        else {
            $('.persentase-komponen').each(function(index, value) {
                $(".persentase-komponen[data-key='"+index+"']").val(0).trigger('change'); 
              });
            $('.harga-komponen').prop("readonly", false);
            $('.persentase-komponen').prop("readonly", true);
            $('.persentase-komponen').prop("disabled", true);
            
            /**
             * tambah atau edit tarif, pilih persentase, pilih komponen, input harga komponen, lalu pilih nominal. harga pada nominal tidak kereset. seharusnya harga ke reset semua.
             */
             $('.harga-komponen').val(0).trigger('change')
             $('.total-harga').text(0)
             if(!is_mcu){
                resetHargaKomponen()
             }
        }
    });
}

$(document).on('change', '.harga-komponen-paket', function () {
    var value = $( this ).val();
    var listId = $( this ).data('id');
    var key = $( this ).data('key');
    isEditKomponen = true
    var tmpTotalHargaKomponen = totalHargaKomponen = 0;
    if(list_komponen[listId].komponen[key]){
        list_komponen[listId].komponen.forEach(function (val, k) {
            if(key == k){
                tmpTotalHargaKomponen += parseFloat(docoHelper.convertToAngka(value));
            }else{
                tmpTotalHargaKomponen += parseFloat(val.nominal_angka);
            }
        })
        let harga_penyesuaian =  list_komponen[listId].harga_origin;
        let harga_previous =  list_komponen[listId].komponen[key].nominal_angka;
        tmpTotalHargaKomponen = (isSetHarga) ? $('#total_komponen').val() : tmpTotalHargaKomponen
        if(tmpTotalHargaKomponen > harga_penyesuaian){
            docoNotification('error', 'Proses Gagal!', "Total harga komponen tidak boleh lebih besar dari " +docoHelper.convertToRupiah(harga_penyesuaian));
            if(!isSetHarga) {
                $( this ).val(docoHelper.convertToRupiah(harga_previous))
            }
        }else{
            list_komponen[listId].komponen[key].nominal = value;
            list_komponen[listId].komponen[key].nominal_angka = docoHelper.convertToAngka(value);
            generateTableKomponen(listId)
            total_komponen = docoHelper.convertToAngka($('#total_komponen').val()).toFixed(0)
            $('input[name="TarifTindakanForm[harga_tariftindakan]['+listId+']"]').val(total_komponen).trigger('change')
        }
    }
})

$(document).on('click', '.btn-close-modal', function(){
    var totalHargaKomponen = docoHelper.convertToAngka($('#total_komponen').val());
    let harga_penyesuaian = (list_komponen[list_id].harga_penyesuaian) ? list_komponen[list_id].harga_penyesuaian : 0;
    if(totalHargaKomponen != harga_penyesuaian && harga_penyesuaian != 0){
        docoNotification('error', 'Perhatian!', "Total harga komponen harus sama dengan harga penyesuaian Rp." +docoHelper.convertToRupiah(harga_penyesuaian));
        return false;
    }else{
        $("#modal_backdrop").modal("toggle")
    }
})