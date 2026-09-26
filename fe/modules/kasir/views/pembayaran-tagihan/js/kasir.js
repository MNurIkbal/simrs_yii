var table;
let tmpTablePenjamin = [];
let tmpTableDiskonDokter = [];
let total_diskon = 0;
let total_penjamin = 0;
let tmpTablePembayaran = [];
let listUnbalanceTagihan = [];
let total_pembayaran = 0;
let diskonCompare = 0;
var getcarabayar;
var getpenjamin;
var tagihan_kotor = 0;
var nilai_pembulatan = parseInt(_satuanpembulatan);
var dijaminKotor = parseInt(0)
var persenPure;
$(document).ready(function () {
    if(!_isDefaultTmp || typeof changeData !== 'undefined'){
        getKontrakPenjamin();
    }

    if(typeof statusBelumBayar != 'undefined' && statusBelumBayar == 1){
        $('#jumlah_uangmuka').val(docoHelper.convertToRupiah($('#jumlah_uangmuka').val()))
        $('#total_piutang').val(docoHelper.convertToRupiah($('#total_piutang').val()))
    }

    $(".info-tunai").hide();
    docoHelper.is_pembulatankeatas = _is_pembulatan;
    docoHelper.satuanpembulatan = _satuanpembulatan;
    //Set up nilai satuan pembulatan
    $('#satuan_pembulatan').val(_satuanpembulatan)
    //Set up nilai diskon Admin
    if( typeof is_diskonadm_dijamin !== 'undefined'){
        $('#is_diskon_adm_payer').val(is_diskonadm_dijamin)
    } else {
        $('#is_diskon_adm_payer').val(0)
    }
    if (typeof diskon_adm_val !== 'undefined'){
        $('#diskon_adm_data').val(diskon_adm_val)
    } else{
        $('#diskon_adm_data').val(0)
    }

    var _totalTagihan = parseFloat(docoHelper.convertToAngka($("#total_tagihan").val()));
    var _jumUM = docoHelper.convertToAngka(typeof $("#jumlah_uangmuka").val() == 'undefined' ? 0 : $("#jumlah_uangmuka").val());
    var _subsidiAsuransi = (typeof $("#subsidi-asuransi").val() == 'undefined' || $('#subsidi-asuransi').val() == '') ? 0 : $("#subsidi-asuransi").val();
    var _tagihanPasien = _totalTagihan - _subsidiAsuransi;
    var _biaya_adm = 0;
    var _totalTagihanLain = tagihan_belumbayar - _totalTagihan;
    let _total_admin_round = docoHelper.calculateRounding(parseFloat(_total_admin));
    // let _total_admin_round = Math.round(parseFloat(_total_admin));
    // 

    // _biaya_adm = parseFloat(_total_admin);
    // _biaya_adm = _total_admin_round;
    _biaya_adm = _total_admin_round.nominal_round;
    if (typeof tmpTableTransaksi[0] !== "undefined" && _biaya_adm && !_isRekap) {
        var _firstCol = {
            checkPenjamin: tmpTableTransaksi[0].checkPenjamin,
            cyto: 0,
            cyto_origin: 0,
            penyulit: 0,
            penyulit_origin: 0,
            kelompoktindakan_nama: '',
            defaultPenjamin: tmpTableTransaksi[0].defaultPenjamin,
            dijamin: 0,
            totalDibayar: 0,
            harga: _biaya_adm,
            harga_origin: _biaya_adm,
            instalasi: "",
            isPenjamin: tmpTableTransaksi[0].isPenjamin,
            penjamin: tmpTableTransaksi[0].penjamin,
            qty: 1,
            subtotal: _biaya_adm,
            subtotal_origin: _biaya_adm,
            tanggal: _dateNow,
            tindakan: "Biaya Administrasi",
            keterangan: "",
            tindakan_obat_id: null,
            nominal_diskon: 0,
            persen_diskon: 0,
            value: {},
            pelayanan_id: null
        };
        if (_firstCol.isPenjamin) {
            _firstCol.dijamin = _biaya_adm
        } else {
            _firstCol.totalDibayar = _biaya_adm;
        }
        tmpTableTransaksi.push(_firstCol)
    }

    if (_totalTagihanLain < 1) {
        $(".content-tagihan-lain").hide();
    } else {
        $(".content-tagihan-lain").css("display", "block");
        switch (_info.kelompok) {
            case 'pasien_alkes':
                _info.sisa_obat -= parseFloat(_totalTagihan);
                break;
            case 'pasien_karcis':
                _info.sisa_karcis -= parseFloat(_totalTagihan);
                break;
            case 'pasien_penunjang':
                _info.sisa_penunjang -= parseFloat(_totalTagihan);
                break;
            default:
                break;
        }
        var _htmlPov = `<ul>`;
        var _sisa_tagihanlain = (tagihan_belumbayar - _info.sisa_penunjang) - _info.sisa_karcis - _info.sisa_obat;
        if (_info.sisa_karcis) {
            _htmlPov += `
                <li>Tagihan Karcis :
                    <b><span class='pull-right'>Rp. ${docoHelper.convertToRupiah(_info.sisa_karcis)}</span></b>
                </li>
            `;
        }

        if (_info.sisa_obat) {
            _htmlPov += `
                <li>Tagihan Obat :
                    <b><span class='pull-right'>Rp. ${docoHelper.convertToRupiah(_info.sisa_obat)}</span></b>
                </li>
            `;
        }

        if (_info.sisa_penunjang) {
            _htmlPov += `
                <li>Tagihan Penunjang :
                    <b><span class='pull-right'>Rp. ${docoHelper.convertToRupiah(_info.sisa_penunjang)}</span></b>
                </li>
            `;
        }

        if (_sisa_tagihanlain) {
            _htmlPov += `
                <li>Tagihan Lain-Lain :
                    <b><span class='pull-right'>Rp. ${docoHelper.convertToRupiah(_sisa_tagihanlain)}</span></b>
                </li>
            `;
        }

        _htmlPov += `</ul>`;

        $(".content-tagihan-lain").attr('data-content', _htmlPov)
        $('[data-popup=tooltip-custom]').popover({
            trigger: 'hover',
            container: 'body',
            title: 'Detail Sisa Tagihan',
            html: true,
            template: '<div class="popover border-teal-400"><div class="arrow"></div><h3 class="popover-title bg-teal-400"></h3><div class="popover-content"></div></div>'
        });
    }

    var tmpTablePenjamin_id = [];
    var harus_bayar = harus_bayar_subPlafon = tmp_bayar = 0
    var is_perseorangan = true
    var total_tagihan = 0;
    tmpTableTransaksi.forEach(function (val, key) {
        //Pengecekan dari branch Item tidak dibayarkan
        if(typeof val.is_ditagihkan != "undefined"){
            is_jaminan_ditagihkan = val.is_ditagihkan
        } else {
            is_jaminan_ditagihkan = null
        }
        if(is_jaminan_ditagihkan == null || is_jaminan_ditagihkan == true){
            if(typeof val.dijamin_subpayer == 'undefined' || val.dijamin_subpayer == '' || isNaN(val.dijamin_subpayer)){
                val.dijamin_subpayer = 0
            }
            if((val.dijamin != 0 || val.dijamin_subpayer != 0) && val.checkPenjamin == true){
                is_perseorangan = false
                tmpTablePenjamin_id.push({
                    dijamin : val.dijamin + val.nominal_diskon,
                    dijamin_subpayer : val.dijamin_subpayer,
                    penjamin : val.penjamin
                })
            }

            // Kondisi langka apabila nominal diskon membuat nilai dijamin nya menjadi 0 (diskon 100%)
            if((val.dijamin == 0 || val.dijamin_subpayer == 0) && val.nominal_diskon > 0 && val.checkPenjamin == true && val.subtotal == 0){
                is_perseorangan = false
                tmpTablePenjamin_id.push({
                    dijamin : val.dijamin + val.nominal_diskon,
                    dijamin_subpayer : val.dijamin_subpayer,
                    penjamin : val.penjamin
                })
            }
            if(!val.checkPenjamin) {
                if(val.plafon_payer == null || val.plafon_payer == 0){
                    harus_bayar += parseFloat(val.totalDibayar) + val.nominal_diskon
                }
            }
            if(val.plafon_payer != null && val.plafon_payer != 0){
                is_perseorangan = false
                if(_listPenjamin.length >= 2){
                    harus_bayar += parseFloat(val.totalDibayar) //+ val.nominal_diskon
                } else {
                    harus_bayar += parseFloat(val.totalDibayar)
                }
            }
            tmp_bayar += parseFloat(val.totalDibayar) + val.nominal_diskon
            total_tagihan += val.subtotal + val.nominal_diskon;
        }
    })
    if(is_perseorangan){
        harus_bayar = tmp_bayar
    }
    var _nilaiTotalPembulatan = 0
    var _nilaiTotalDijamin = 0
    var biayaAdmin = "Biaya Administrasi"
    tmpTablePenjamin_id.forEach(function(val,key){
        if(typeof val.dijamin_subpayer == 'undefined' || val.dijamin_subpayer == '' || val.dijamin_subpayer == null || isNaN(val.dijamin_subpayer) ){
            val.dijamin_subpayer = 0
        }
        _nilaiTotalDijamin += (val.dijamin + parseFloat(val.dijamin_subpayer) )
        bulatan_jaminan = docoHelper.calculateRounding(val.dijamin)
        if(bulatan_jaminan.nominal_selisih > 0 ){
        _nilaiTotalPembulatan += bulatan_jaminan.nominal_selisih
        }
    })
    hasil_round = docoHelper.calculateRounding(harus_bayar)
    harus_bayar_round = hasil_round.nominal_round
    var _diTagihkan = harus_bayar + _nilaiTotalDijamin  // +  _nilaiTotalPembulatan

    var _diTagihkan_pembulatan = $('#total_tagihan_pembulatan').val();
    $("#biaya_administrasi").val(docoHelper.convertToRupiah(_biaya_adm));
    //State saat pertama kali di load masuk halaman pembayaran
    $(".info-tagihan-lain").html('Rp. ' + docoHelper.convertToRupiah(_totalTagihanLain));
    $("#total_tagihan").val(docoHelper.convertToRupiah(total_tagihan));
    $("#total_tagihan_pembulatan").val((_diTagihkan - (harus_bayar + _nilaiTotalDijamin )));

    $("#tagihan_pasien").html(docoHelper.convertToRupiah(_totalTagihan + parseFloat(_biaya_adm)));
    $(".styled, .multiselect-container input").uniform({
        radioClass: 'choice'
    });
    if(_listPenjamin.length >= 2){
        id = 0
        $('#dijamin').after('<th id="nama-subpayer">Penjamin Sub Payer</th><th id="total-dijamin-subpayer">Dijamin (Rp)</th>')
        $('#detail-tagihan').find('tr').each(function(){
            row_insert = '<td class="nama-subpayer" id="nama-subpayer-'+id+'"></td><td class="text-right total-dijamin-subpayer" id="total-dijamin-subpayer-'+ id +'">0</td>'
            $(this).find('td').eq(11).after(row_insert);
            id++;
       });
    }

    table = $("#detail-tagihan").DataTable({
        language: {
            search: "Pencarian&nbsp;:&nbsp;"
        },
        responsive: true,
        filter: false,
        order: [[1, "asc"]],
        scrollY: "300px",
        scrollCollapse: true,
        paging: false,
        columnDefs: [{ targets: 0, orderable: false }],
    });

    table.on('order.dt search.dt', function () {
        table.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    }).draw();

    setTimeout(function () {
        table.order([[1, "asc"]]).draw()
    }, 1000)

    $.each(_listCaraBayar, function (key, val) {
        _groupCaraBayar[val.carabayar_id] = val.groupcarabayar_id;
    });

    if (typeof _groupCaraBayar[_caraBayarPasien] != "undefined") {
        _caraBayarPasien = _groupCaraBayar[_caraBayarPasien];
    }

    if (!_status) {
        $(".doco-number").not('#persen').not('#total_diskon').trigger("change");
        $("select[name=carabayar_id]").trigger("change");
        _checked();
        _checkStatus();
    } else {

        $("#tagihanpasienform-total_tagihan, #total_dibayar, #subsidi-asuransi, #biaya_administrasi").trigger("change");
        $(".content-wrapper").find("input, select, #save-pasien-karcis, .reset-form").prop("disabled", true);
    }

    reloadTagihan()

    if ($("#persen_chk").is(':checked')) {
        $("#persen").removeAttr("disabled")
    } else {
        $("#persen").attr("disabled", true)
    }

    if(_display == 1) {
        $("#job-order").show();
        $("#job-order").removeClass("btn-info");
        $("#job-order").addClass("btn-danger");
        $("#job-order").css("font-weight", "bold");
        if(_blockBilling == 1) {
            $("#save-pasien-karcis").prop("disabled", true);
        }
    }
});
const _hitungDiskon = () => {
    reloadTagihan()
    let penjaminTotal = 0
    let diskonDokterTotal = 0

    if (tmpTablePenjamin.length > 0) {
        tmpTablePenjamin.forEach((val, key) => {
            penjaminTotal += parseFloat(val.dijamin_angka)
        })
    }

    if (tmpTableDiskonDokter.length > 0) {
        tmpTableDiskonDokter.forEach((val, key) => {
            diskonDokterTotal += parseFloat(val.diskon_angka)
        })
    }

    tagihanPasien = docoHelper.convertToAngka($('#tagihan_pasien').html())
    if (diskonDokterTotal > 0) {
        if(_totalDijamin > 0){
            _totalDijamin = _totalDijamin - diskonDokterTotal;
            $("#subsidi-asuransi").val(docoHelper.convertToRupiah(_totalDijamin))
            $('#total_sisa_piutang').val(docoHelper.convertToRupiah(tagihanPasien))
        } else {
            $('#total_sisa_piutang').val(docoHelper.convertToRupiah(tagihanPasien))
        }
    }
}

const _hitungTagihan = () => {
    let penjaminTotal = 0
    let bayarTotal = 0
    var ditagihkan = 0
    var is_plafon_data = false
    var totalUangMuka = parseFloat(docoHelper.convertToAngka($("#jumlah_uangmuka").val()))
    var totalTagihan = docoHelper.convertToAngka($("#total_tagihan").val())
    var totalPiutang = parseFloat(docoHelper.convertToAngka($("#total_piutang").val()))
    var diskon_total = $('#total_diskon').val() != '' ? parseFloat(docoHelper.convertToAngka($('#total_diskon').val())) : 0

    let diskonDokterTotal = 0
    if (tmpTableDiskonDokter.length > 0) {
        tmpTableDiskonDokter.forEach((val, key) => {
            diskonDokterTotal += parseFloat(val.diskon_angka)
        })
    }

    if (tmpTablePenjamin.length > 0) {
        tmpTablePenjamin.forEach((val, key) => {
            penjaminTotal += parseFloat(val.dijamin_angka)
        })
    }

    if (tmpTablePembayaran.length > 0) {
        tmpTablePembayaran.forEach((val, key) => {
            bayarTotal += parseFloat(val.nominal_angka)
        })
    }

    if(tmpTableTransaksi.length > 0){
        tmpTableTransaksi.forEach((val,key)=>{
            if (val.plafon_payer > 0){
                is_plafon_data = true
            }
        })
    }

    var totalTagihan_pembulatan = parseFloat($('#total_tagihan_pembulatan').val())

    //Pengurangan nilai pembulatan dari nilai total tagihan
    var tmp_tagihan_kotor = (totalTagihan) - (diskonDokterTotal + diskon_total)

    tagihan_kotor = tmp_tagihan_kotor < 0 ? 0 : tmp_tagihan_kotor
    var balanceRs = _totalDijaminRounded = 0

    //Pengurangan nilai pembulatan dari nilai asuransi
    _totalDijaminRounded = $("#subsidi-asuransi_pembulatan").val()
    if (tagihan_kotor < _totalDijamin) {
        if (diskonDokterTotal > 0) {
            _totalDijamin = _totalDijamin - diskonDokterTotal;
            $("#subsidi-asuransi").val(docoHelper.convertToRupiah(_totalDijamin));
        }
        balanceRs = (_totalDijamin - _totalDijaminRounded) - tagihan_kotor
    }

    if (tagihan_kotor > _totalDijamin) {
        tagihan_kotor -= parseFloat(_totalDijamin - _totalDijaminRounded).toFixed(2)
    } else {
        tagihan_kotor = 0
    }

    if (tagihan_kotor > 0) {
        tagihan_kotor -= totalPiutang
    }

    ditagihkan = tagihan_kotor < 1 ? 0 : parseFloat(tagihan_kotor).toFixed(1);

    /** diubah jadi < 1 untuk handler ketika total dijamin - tagihan kotor bernilai 0.xxx dimana sebelumnya ttp ada nilai yang akan diagihkan ke pasien */
    $("#total_balance_rs").val(docoHelper.convertToRupiah(balanceRs));
    docoHelper.pembulatan(ditagihkan, $("#pembulatan"), $("#total-pembulatan"))
    var totalPembulatan = parseFloat(docoHelper.convertToAngka(typeof $("#total-pembulatan").val() == 'undefined' ? 0 : $("#total-pembulatan").val()));
    var totalDibayar = parseFloat(docoHelper.convertToAngka($("#total_dibayar").val()))
    var totalPiutang = parseFloat(docoHelper.convertToAngka($("#total_piutang").val()))

    var _harusBayar = parseFloat(totalPembulatan - totalUangMuka < 0 ? 0 : totalPembulatan - totalUangMuka);
    // _harusBayar = _harusBayar - totalPiutang;

    total_diskon = docoHelper.convertToAngka($('#total_diskon').val())
    // var _pembayaranpasien = totalDibayar + bayarTotal - diskonDokterTotal
    var _pembayaranpasien = totalDibayar + bayarTotal
    var _kembalian = _pembayaranpasien - _harusBayar
    //State pertama saat load halaman pembayaran
    $("#tagihan_pasien").html(docoHelper.convertToRupiah(_harusBayar));
    $('#total_sisa_piutang').val(docoHelper.convertToRupiah(_kembalian)).trigger('change')
}


$(document).on("change", "#total_dibayar", function (event) {
    event.preventDefault();
    var _value = parseFloat(docoHelper.convertToAngka($(this).val()))
    var totalPembulatan = parseFloat(docoHelper.convertToAngka($("#total-pembulatan").val()))
    var totalUangMuka = parseFloat(docoHelper.convertToAngka($("#jumlah_uangmuka").val()))
    var _totalDiskonDokter = docoHelper.convertToAngka($("#diskon-dokter").val());
    var _pembayaranpasien = parseFloat(_value) + parseFloat(total_pembayaran) + parseFloat(_totalDiskonDokter) + parseFloat(totalUangMuka)
    // console.log(_pembayaranpasien, totalPembulatan)
    sisa = _pembayaranpasien - totalPembulatan
    // $("#total_sisa_piutang").val(docoHelper.convertToRupiah(sisa)).trigger("change");
    _hitungTagihan()
});

/**
 * Trigger function untuk diskon asuransi
 * 
 * @author Maulana Muhammad Rizky.
 */
setTimeout(() => {
    if (total_diskon == 0) {
        if(discountInsurance !== undefined && discountInsurance != '') {
            $("#persen_chk").prop("checked", true).trigger("change");
        }
    }
}, 500)


$("#persen_chk").on('change', function (e) {

    if ($("#persen_chk").is(':checked')) {
        $("#persen").removeAttr("disabled")
        $("#total_diskon").attr("readonly", true)
        $("#total_diskon").val(0).trigger('change')
        if(total_diskon == 0) {
            if(discountInsurance !== undefined && discountInsurance != '') {
                setTimeout(() => {
                    $("#persen").val(discountInsurance).trigger('change')
                }, 100)
            }
        } 
        
        var _tagihan = docoHelper.convertToAngka($('#total_tagihan').val())
        var _persen = docoHelper.convertToAngka($('#persen').val())
        var _diskon = _persen / 100 * _tagihan;
        $("#total_diskon").val(docoHelper.convertToRupiah(_diskon)).trigger('change')
    } else {
        $("#persen").attr("disabled", true)
        $("#total_diskon").removeAttr("readonly")
        $("#total_diskon").val(0).trigger('change')
        $("#persen").val(0).trigger('change')
    }
})

$("#persen").on('change', function (e) {
    var _persen = docoHelper.convertToAngka($(this).val() ? $(this).val() : 0)
    var _tagihan = docoHelper.convertToAngka($('#total_tagihan').val())
    if (_persen > 100) {
        docoNotification("error", "Data Tidak Valid!", "Persen tidak boleh lebih dari 100");
        $(this).val(0)
        $("#total_diskon").val(0).trigger('change')
    }
    var _diskon = _perhitunganDicPercen(_persen)
    reloadTagihan()
})

var _perhitunganDicPercen = function (percent) {
    var _percenTotal = percent > 100 ? 100 : percent;
    var _totalDiskon = 0;

    tmpTableTransaksi.map(function (v, k) {
        v.persen_diskon = parseFloat(_percenTotal)
        var _subTotal = v.subtotal_origin;
        let diskonItem = Math.round((_percenTotal / 100 * _subTotal));
        v.nominal_diskon = diskonItem;
        v.subtotal = _subTotal - v.nominal_diskon
        if (v.isPenjamin && v.checkPenjamin) {
            v.dijamin = v.subtotal;
            v.totalDibayar = 0;
        } else {
            if(v.isPenjamin == true  && v.checkPenjamin == false) {
                v.dijamin = 0;
            }else {
                v.totalDibayar = v.subtotal;
            }
        }
        _totalDiskon += v.nominal_diskon
        return v;
    })
    return _totalDiskon;
}

$(document).on("change", "#total_diskon", function (e) {
    e.preventDefault()
    var disdok = 0
    if (tmpTableDiskonDokter.length > 0) {
        tmpTableDiskonDokter.forEach((val, key) => {
            // disdok += parseFloat(val.diskon_angka).toFixed(2)
            disdok += parseFloat(val.diskon_angka)
        })
    }

    var _diskonTotal = docoHelper.convertToAngka($(this).val())
    var _diskonDokter = docoHelper.convertToAngka(disdok)
    var diskon = parseFloat(_diskonTotal) + parseFloat(_diskonDokter);
    var _tagihan = docoHelper.convertToAngka($('#total_tagihan').val())

    if (diskon > parseFloat(_tagihan)) {
        docoNotification("error", "Data Tidak Valid!", "Diskon Total tidak boleh lebih dari Total Tagihan");
        $(this).val(0)
        return false
    }
    _perhitunganDicTotal(docoHelper.convertToAngka($(this).val()));
    reloadTagihan()
    // _hitungDiskon()
})


var _perhitunganDicTotal = function (total) {
    var _totalDiskon = 0;
    var tagihan = docoHelper.convertToAngka($('#total_tagihan').val())
    var _avgDisc = total <= 0 ? 0 : (total / tagihan) * 100;
    tmpTableTransaksi.map(function (v, k) {
        v.persen_diskon = 0
        var _subTotal = v.subtotal_origin;
        v.nominal_diskon = _subTotal * (_avgDisc / 100)
        v.subtotal = _subTotal - v.nominal_diskon
        if (v.isPenjamin) {
            v.dijamin = v.subtotal;
            v.totalDibayar = v.subtotal - v.dijamin;
        } else {
            v.totalDibayar = v.subtotal;
        }
        _totalDiskon += v.nominal_diskon
        return v;
    })
    return parseFloat(_totalDiskon).toFixed(2);
}

var _checkStatus = function () {
    if (_status) {
        $(".content-wrapper").find("input, select, #save-pasien-karcis, .reset-form").prop("disabled", true);
        $("#edit-tagihan").prop("disabled", true);
        $(".print-tagihan").prop("disabled", false);
        $("#cetak-invoice").prop("disabled", false);

        if (_caraBayarPasien == docoHelper.groupUmum && _isKarcis) {
            $("#print-karcis").prop("disabled", false);
        }
        $("#cetak-detail-invoice").prop("disabled", false);
        if (_instalasi_id == 3) {
            $("#print-sip").prop("disabled", false);
        } else {
            $("#print-sip").prop("disabled", true);
        }
    }
}

var _checked = function () {
    if ($("#is_ecollect").is(":checked")) {
        $(".e-collection").attr("readonly", false);
    } else {
        $(".e-collection").attr("readonly", true);
        $(".e-collection").val(null);
    }
}

$(document).on("click", "#is_ecollect", _checked);

$(document).on("click", "#save-pasien-karcis", function (event) {
    event.preventDefault();
    var header = 'Perhatian !';
    var label = {
        buttons: {
            'Yes': 'button-yes',
            'No': 'button-no'
        }
    };
    var nominalDiscEditTagihan = 0;
    _idPenjaminSubPayer = _idPenjaminPayer = 0
    _namaPenjaminSubPayer = ''
    _admPenjaminPayer = _admPenjaminSubPayer =  []
    isUnbalanceTagihan = isErrorAdm = false
    if (_status) return false;
    var totalMainPayer = 0
    var _form = $("#ajax-form").serializeArray();
    var _totalDisc = parseFloat($('#diskon-dokter').val()) + parseFloat($('#total_diskon').val())
    // for detail tagihan
    var _totalItem = totalTindakanObat = 0;
    var discountSum = 0;
    tmpTablePenjamin_id =[];
    _listPenjamin.forEach(function(value){
        if(value.selected == false){
            _idPenjaminSubPayer = value.id
            _namaPenjaminSubPayer = value.text
            _admPenjaminSubPayer = value
        } else {
            _admPenjaminPayer = value
            _idPenjaminPayer = value.id
        }
    })
    $.each(_cache, function (key, val) {
        $.each(val, function (id, item) {
            if (key != "obat") {
                if(typeof item.dijamin_subpayer == 'undefined'|| item.dijamin_subpayer == ''){
                    item.dijamin_subpayer = 0
                }
                var _condition = {
                    is_paket: 0,
                    penjamin_id: item.penjamin_pelayanan_id,
                    tindakan: item.tindakan_obat_id,
                    kelaspelayanan_id: item.kelaspelayanan_id,
                    id_parent: item.pelayanan_id,
                    is_cyto: item.tarif_cyto ? 1 : 0,
                    penjamin: item.penjamin,
                    dijamin: item.dijamin,
                    dijamin_subpayer: item.dijamin_subpayer,
                    harusbayar: item.harusbayar,
                    nominal_diskon: item.nominal_diskon,
                    keterangan: item.keterangan,
                    is_mainpayer : true,
                    is_obat: false
                };
                if (item.is_paket) {
                    _condition.is_paket = 1;
                }
                _form.push({
                    name: "condition[]",
                    value: JSON.stringify(_condition)
                });
                _totalItem++
                item.plafon_payer = (item.plafon_payer) ? item.plafon_payer : 0
                if(item.dijamin_subpayer > 0 /* || item.plafon_payer > 0 */){
                     var _condition = {
                        is_paket: 0,
                        penjamin_id: parseInt(_idPenjaminSubPayer),
                        tindakan: item.tindakan_obat_id,
                        kelaspelayanan_id: item.kelaspelayanan_id,
                        id_parent: item.pelayanan_id,
                        is_cyto: item.tarif_cyto ? 1 : 0,
                        penjamin: _namaPenjaminSubPayer,
                        dijamin: item.dijamin,
                        dijamin_subpayer: item.dijamin_subpayer,
                        harusbayar: item.harusbayar,
                        nominal_diskon: item.nominal_diskon,
                        keterangan: item.keterangan,
                        is_mainpayer : false
                    };
                    if (item.is_paket) {
                        _condition.is_paket = 1;
                    }
                    _form.push({
                        name: "condition[]",
                        value: JSON.stringify(_condition)
                    });

                    _totalItem++
                }
            }
            if (key == "obat") {
                dataSubPayer = []
                item.plafon_payer = (item.plafon_payer) ? item.plafon_payer : 0
                if(item.dijamin_subpayer > 0 || item.plafon_payer > 0){
                    dataSubPayer = {
                        penjamin_pelayanan_id : parseInt(_idPenjaminSubPayer),
                        penjamin : _namaPenjaminSubPayer,
                        harusbayar : item.harusbayar,
                        nominal_diskon : 0,
                        dijamin_subpayer : item.dijamin_subpayer,
                        dijamin_payer : item.dijamin_payer,
                        is_mainpayer : false
                    }
                    item.subpayer_data = JSON.stringify(dataSubPayer)
                }
                _form.push({
                    name: "detail_tagihan[" + key + "][" + id + "]" ,
                    value: JSON.stringify(item)
                });
                _totalItem++
            }
            if(item.harus_bayar != 0 && item.persen_diskon != 0) {
                nominalDiscEditTagihan += item.nominal_diskon
            }
            totalTindakanObat++
        })
    });

    if (typeof tmpTableTransaksi[totalTindakanObat] !== "undefined") {
        var _admAsuransi = tmpTableTransaksi[totalTindakanObat];
        if(typeof _admAsuransi.dijamin_subpayer =='' || _admAsuransi.dijamin_subpayer == ''){
            _admAsuransi.dijamin_subpayer = 0
        }
        _admDefaultPenjamin =  _admAsuransi.defaultPenjamin ?  _admAsuransi.defaultPenjamin : _admPenjaminPayer
        _admSubPenjamin =  _admAsuransi.dijamin_subpayer ?  _admPenjaminSubPayer : null
        _admAsuransi = {
            dijamin: _admAsuransi.dijamin,
            dijamin_subpayer: _admAsuransi.dijamin_subpayer,
            defaultPenjamin: _admDefaultPenjamin,
            nominal_diskon: _admAsuransi.nominal_diskon,
            harusbayar: _admAsuransi.totalDibayar,
            subPenjamin: _admSubPenjamin,
        }
        _form.push({
            name: "adm_asuransi",
            value: JSON.stringify(_admAsuransi),
        });
    }
    // for detail penjamin
    $.each(tmpTablePenjamin, function (key, val) {
        _form.push({
            name: "data_penjamin[]",
            value: JSON.stringify(val),
        });
    });

    // for detail jenis pembayaran
    $.each(tmpTablePembayaran, function (key, val) {
        _form.push({
            name: "data_metode_pembayaran[]",
            value: JSON.stringify(val),
        });
    });

    // for diskon dokter
    $.each(tmpTableDiskonDokter, function (key, val) {
        _form.push({
            name: "data_diskon[]",
            value: JSON.stringify(val),
        });
    });
    // Inisiasi pembagian pembulatan untuk setiap penjamin
    data_tagihan = plafonPayer = plafonSubPayer = excessPasien = 0
    var isBayarSetengah = false;

    tmpTableTransaksi.forEach(function (val, key) {
        flag_found = false;
        if(val.dijamin != 0){
            tmpTablePenjamin_id.forEach(function(valtab,keytab){
                if(_listPenjamin.length == 2){
                    flag_found = false
                }
                else if(valtab.penjamin == val.penjamin){
                    valtab.dijamin += val.dijamin;
                    flag_found = true;
                }
            })
            if(typeof val.dijamin_subpayer == '' || val.dijamin_subpayer == '' || isNaN(val.dijamin_subpayer)){
                val.dijamin_subpayer = 0
            }
            if(flag_found == false){
                tmpTablePenjamin_id.push({
                    dijamin : val.dijamin,
                    dijamin_subpayer : parseFloat(val.dijamin_subpayer),
                    penjamin : val.penjamin,
                    id : val.defaultPenjamin.id
                })
            }  
            
            discountSum += val.dijamin + val.nominal_diskon
        }
        else{
            data_tagihan += val.subtotal
            id = -1;
            discountSum += val.subtotal_origin
        }
        if(val.plafon_payer != 0 || val.plafon_payer != '' || typeof val.plafon_payer != 'undefined'){
            plafonPayer = val.plafon_payer
        }
        if(val.plafon_subpayer != 0 || val.plafon_subpayer != '' || typeof val.plafon_subpayer != 'undefined'){
            plafonSubPayer = val.plafon_subpayer
        }
        if(val.excess_pasien != '' || typeof val.excess_pasien != 'undefined') {
            excessPasien = val.excess_pasien
        }

        //Validasi Perhitungan Tabel apabila terjadi ketikasesuaian
        unbalanceTagihan = false
        tmpHarga = parseFloat(val.harga)
        tmpCyto = (isNaN(val.cyto)|| val.cyto =='' || val.cyto == '0' || val.cyto == null) ? 0 : parseFloat(val.cyto)
        tmpPenyulit = (isNaN(val.penyulit)|| val.penyulit =='' || val.penyulit == '0' || val.penyulit == null) ? 0 : parseFloat(val.penyulit)
        tmpQty =  parseFloat(val.qty)
        tmpDijamin = parseFloat(val.dijamin)
        tmpDijaminSubpayer = (isNaN(val.dijamin_subpayer) || val.dijamin_subpayer =='' || val.dijamin_subpayer == '0' || val.dijamin_subpayer == null) ? 0 : parseFloat(val.dijamin_subpayer)
        tmpTotalDibayar = parseFloat(val.totalDibayar)
        tmpNominalDiskon = (isNaN(val.nominal_diskon)|| val.nominal_diskon =='' || val.nominal_diskon == '0' || val.nominal_diskon == null) ? 0 : parseFloat(val.nominal_diskon)
        tmpValidasiHarga = parseFloat(tmpQty*(tmpHarga + tmpCyto + tmpPenyulit)).toFixed(2)
        tmpValidasiPembayaran = parseFloat(tmpDijamin + tmpDijaminSubpayer + tmpTotalDibayar +tmpNominalDiskon).toFixed(2)
        tmpValidasi = tmpValidasiHarga - tmpValidasiPembayaran
        totalMainPayer += parseFloat(val.dijamin)
        if(tmpTotalDibayar < 0 || tmpNominalDiskon < 0 || tmpDijaminSubpayer < 0 || tmpDijamin < 0 || tmpQty < 0 || tmpHarga < 0){
            unbalanceTagihan = true
        }
        if(tmpValidasi != 0 || tmpValidasiHarga < 0 || tmpValidasiPembayaran < 0 || unbalanceTagihan == true){
            if(val.kelompoktindakan_nama.length == 0){
                isErrorAdm = true
            }
            isUnbalanceTagihan = true
            $('tr[data-id='+ val.pelayanan_id +']').css('background-color','#ff9ca2')
            listUnbalanceTagihan.push(val.pelayanan_id)
        }
        
        if(val.isPenjamin == true && val.checkPenjamin == false) {
            isBayarSetengah = true;
        }
    })

    console.log(discountSum, "INI SUm")
    var perhitunganDiscount = discountInsurance / 100 * discountSum 
    var perhitunganDiscountUmum = discountUmum / 100 * discountSum
    
    if(_listPenjamin.length > 1) {
        if(totalMainPayer == 0) {
            docoNotification("warning", "Kesalahan Data", "Total Tagihan Main Payer tidak dapat 0 Rupiah")
            return false
        }
    }

    tmpTablePenjamin_id.push({
        tagihan_perseorangan : data_tagihan
    })

    $.each(tmpTablePenjamin_id, function(key,val){
        _form.push({
            name: "tagihan_dijamin[]",
            value: JSON.stringify(val),
        });
    })

    _form.push({ name: "ruangan_pelakhir_id", value: _info.ruangan_id });
    _form.push({ name: "instalasi_id", value: _info.instalasi_id });
    _form.push({ name: "pendaftaran_id", value: _info.pendaftaran_id });
    _form.push({ name: "pasienadmisi_id", value: _info.pasienadmisi_id });
    _form.push({ name: "pasien_id", value: _info.pasien_id });
    _form.push({ name: "nama_pasien", value: _info.nama_pasien });
    _form.push({ name: "type", value: _typePasien });
    _form.push({ name: "status_pasien", value: _info.status_pasien });
    _form.push({ name: "uang_muka", value: (isNaN(_jumUM) ? 0 : _jumUM) });
    _form.push({ name: "penjualanresep_id", value: _penjualan_resep });
    _form.push({ name: "plafon_payer", value: plafonPayer });
    _form.push({ name: "plafon_subpayer", value: plafonSubPayer });
    _form.push({ name: "penjamin_id_main", value: _idPenjaminPayer });
    _form.push({ name: "penjamin_id_sub", value: _idPenjaminSubPayer });
    _form.push({ name: "excess_pasien", value: excessPasien });
    _form.push({ name: "limit_penjamin", value: discountInsurance });
    _form.push({ name: "nominaldisc_edit_tagihan", value: nominalDiscEditTagihan });

    var _simpan = function (confirm = false, approval = false) {
        $().docoForm("click", {
            url: `/kasir/pembayaran-tagihan/create?approval=${approval}`,
            method: "POST",
            type: "json",
            data: _form,
            skipConfirm: confirm,
            success: function (data) {
                var pembayaranId = data.response.pembayaran_id;
                var approval = data.response?.approval

                $('#btn-cetak-detail-invoice-pembayaran').prop("disabled", false);
                $('#btn-cetak-detail-invoice-pembayaran').attr('action', `/kasir/inf-pasien-sudah-bayar/show-popup?pembayaran_id=${pembayaranId}`);
                $('#btn-cetak-invoice').prop("disabled", false);
                $('#btn-cetak-invoice').attr('action', `/kasir/inf-pasien-sudah-bayar/show-popup?multipayer=true&is_bgprocess=false&is_invoice=true&pembayaran_id=${pembayaranId}`);
                $("#cetak-invoice").on("click", function (event) {
                    event.preventDefault();
                    window.open(`/kasir/pembayaran-tagihan/cetak-invoice?id=${_id}&invoice_id=${pembayaranId}&kelompok=${kelompok}`);
                });
                $("#print-sip").on("click", function (event) {
                    event.preventDefault();
                    window.open(`/kasir/pembayaran-tagihan/cetak-sip?id=${_id}`);
                });
                $("#cetak-invoice-belum-bayar").hide();
                $("#cetak-detail-invoice").show();
                if(! approval) {
                    $("#cetak-detail-invoice").prop("disabled", false);
                }
                $("#cetak-detail-invoice-adhy").prop("disabled", false);

                /**penyesuaian tombol cetakan report designer */
                $("#cetak-detail-invoice").removeAttr("data-url");
                $("#cetak-detail-invoice").removeAttr("data-table");
                $("#cetak-detail-invoice").removeAttr("data-conditions");
                $("#cetak-detail-invoice").attr("data-options", "link");
                $("#cetak-detail-invoice").attr("data-toggle", "modal");
                $("#cetak-detail-invoice").attr("data-target", "#modal_backdrop");
                $('#cetak-detail-invoice').attr('action', `/kasir/inf-pasien-sudah-bayar/show-popup?multipayer=true&is_bgprocess=false&is_invoice=true&pembayaran_id=${pembayaranId}`);

                $("#cetak-detail-invoice-adhy").on("click", function (event) {
                    event.preventDefault();
                    window.open(`/kasir/pembayaran-tagihan/cetak-detail-invoice?id=${_id}&invoice_id=${pembayaranId}`);
                });
                // $("#cetak-detail-invoice").on("click", function (event) {
                //     event.preventDefault();
                //     window.open(`/kasir/pembayaran-tagihan/cetak-detail-invoice?id=${_id}&invoice_id=${pembayaranId}`);
                // });

                if(approval) {
                    _status = 0
                } else {
                    _status = 1;
                }
                _checkStatus();
                window.history.replaceState({}, "", _currentUrl + '&status=1');
                $("#add-tagihan").prop('disabled', true);
                $("#add-dibayar").prop('disabled', true);

                if (typeof pembayaranId !== 'undefined') {
                    var urlCetak = $("#print-kwitansi").attr("action");
                    $("#print-kwitansi").prop("disabled", false);
                    $("#print-kwitansi").attr('action', urlCetak + pembayaranId);
                }
            }
        });
    }

    var prosesSimpan = function() {
        if (_totalDisc) {
            $("#confirm-dialog-overlay").remove();
            adds = $('#confirm-form').clone().removeClass('hidden');
            adds.find('.input-pemakai').removeAttr('readonly');
            adds.find('.input-pemakai').attr('value', '');
            adds.find('.input-pemakai').attr('id', 'pemakai-validasi');
            adds.find('.input-pemakai').attr('placeholder', 'Username');
            adds.find('.input-sandi').attr('id', 'sandi-validasi');
            adds = adds.html();
            
            var validateInsurance = total_diskon > Math.round(perhitunganDiscount);
            var validateUmum = total_diskon > Math.round(perhitunganDiscountUmum);

            // Validasi limit melebihi disc
            var isLimitDisc = false;
            if(validateInsurance && configOtoritasPenjamin || isBayarSetengah && validateUmum && configOtoritasPenjamin) {
                var diskonSekarang = (total_diskon / discountSum) * 100;
                
                var limit = 0;
                var limitDisc = 0;
                isLimitDisc = true
                if(validateInsurance && isBayarSetengah) {
                    limitDisc = validateInsurance ? discountInsurance : discountUmum
                    limit = validateInsurance ? perhitunganDiscount : perhitunganDiscountUmum
                } else if (validateInsurance) {
                    limitDisc = discountInsurance
                    limit = perhitunganDiscount
                } else if (validateUmum) {
                    limitDisc = discountUmum
                    limit = perhitunganDiscountUmum
                }

                var message = `
                    <div>
                        Diskon yang diterapkan melebihi limit, 
                        Apakah anda yakin untuk memberikan diskon pada transaksi ini ?
                    </div>
                    <div class="mt-3 mb-3">
                        Limit diskon : ${limitDisc} % Rp.<span class="doco-number">${docoHelper.convertToRupiah(Math.round(limit))}</span>
                        <br>
                        Diskon yang diterapkan : ${parseFloat(diskonSekarang.toFixed(2))} % Rp.<span class="doco-number">${docoHelper.convertToRupiah(parseInt(total_diskon))}</span>
                    </div>
                `;  
            } else {
                var message = 'Apakah anda yakin untuk memberikan diskon pada transaksi ini ?' + adds;
            }
            var label = {
                buttons: {
                    'Yes': 'button-yes',
                    'No': 'button-no'
                },
                hidden: true
            }

            $.showQuestionDialog(header, message, label, function (reaction) {
                if (reaction == 'Yes') {
                    if(configOtoritasPenjamin && isLimitDisc) { 
                        _simpan(true, true);                                                                                                                                                                                                                        
                    } else {
                        var user = $("#pemakai-validasi").val();
                        var pass = $('#sandi-validasi').val();
                        $().docoForm('click', {
                            url: baseUrl + 'kasir/end-point/check-authorization',
                            skipConfirm: true,
                            skipSuccessNotif: true,
                            data: {
                                nama_pemakai: user,
                                katakunci_pemakai: pass,
                                akses: 'discount'
                            },
                            success: function (response) {
                                setTimeout(function () {
                                    showLoader()
                                }, 100);
                                _simpan(true);
                            }
                        })
                    }
                } else {
                    hideQuestionDialog();
                    $('[data-popup="tooltip"]').tooltip();
                }
            })
        }
        else {
            _simpan();
        }
    }
    
    if(!isUnbalanceTagihan) {
        if(_display == 1) {
            $("#confirm-dialog-overlay").remove();
            var messages = `Apakah Anda Yakin Akan Menyimpan Transaksi Ini ? <br/><br/> <span style='font-weight:bold;font-size:16px;'> Pasien Memiliki Orderan Tindakan/Obat yang Belum Selesai.</span>`;
            $.showQuestionDialog(header, messages, label, function (reaction) {
                if (reaction == 'No') {
                    hideQuestionDialog();
                    $('[data-popup="tooltip"]').tooltip();
                }
                else {
                    prosesSimpan()
                }
            });
        }
        else {
            prosesSimpan()
        }
    }
    else {
        errorAdmTxt = (isErrorAdm == true) ? "dan Biaya Administrasi " : ""
        notifText = "Nominal Tindakan/Obat "+ errorAdmTxt +"tidak sesuai. Silahkan cek kembali"
        docoNotification("warning", "Kesalahan Data", notifText)
    }

});


$(document).on("click", ".reset-form", function (event) {
    $("#is_ecollect").prop("checked", true).trigger("click");
    $("#biaya_administrasi").val(null).trigger("change");
    $(".doco-number").trigger("change");
    $(".event-karcis").val(null).trigger("change");
    _checked();
});

// Kebutuhan Print

$(document).on("click", ".print-bkm", function () {
    window.open("/kasir/pembayaran-tagihan/export-bkm?id=" + _id + "&tipe_pasien=" + _tipe_pasien);
});


$(document).on("click", "#print-karcis", function (event) {
    event.preventDefault();
    var _type = $(this).data("type");
    window.open("/kasir/pembayaran-tagihan/export-karcis?id=" + _id + "&type=" + _type + "&tipe_pasien=" + _tipe_pasien);
});

$(document).on("click", "#print-tagihan", function (event) {
    event.preventDefault();
    window.open("/kasir/pembayaran-tagihan/export-rincian?id=" + _id + "&tipe_pasien=" + _tipe_pasien);
});

$(document).ready(function () {
    $("#info-heading").click(function () {
        $("#data-pasien").toggle();
    });
});

//delete filter search dan info table
$(document).ready(function () {
    $("#pemakaian-obat-alkes_filter.dataTables_filter").remove();
    $("#pemakaian-obat-alkes_info").remove();
});

$(document).on("keydown", null, "alt+s", function (event) {
    $(".data-save").click();
});


// for generate table from input form data
function generateTablePenjamin() {
    $('.isi-table').hide()
    $('#table-multi-penjamin > tbody > tr').not('tr.isi-table').remove()
    var _html = ""
    var no = 1;
    var totalDijamin = 0;
    tmpPenjamin = [];
    tmpTablePenjamin.forEach(function (val, key) {
        totalDijamin = totalDijamin + docoHelper.convertToAngka(val.dijamin);
        tmpPenjamin[val.penjamin_id] = val
        _html += `
        <tr>
            <td>${no++}.</td>
            <td>${val.label_penjamin}</td>
            <td>${val.no_kartu}</td>
            <td>Rp. ${val.dijamin}</td>
            <td><button type='button' data-id='${key}' class='delete-table-multi-penjamin btn btn-danger btn-labeled btn-xs delete btn-block'><b><i class="fa fa-trash"></i></b>Hapus </button></td>
        </tr>
        `
    })
    $('#table-multi-penjamin > tbody').append(_html);
    $("#subsidi-asuransi").val(docoHelper.convertToRupiah(totalDijamin));
    total_penjamin = totalDijamin;
    totalDijamin_rounded = 0
    totalDijamin_rounded = docoHelper.calculateRounding(totalDijamin)
    var _tagihanPasien = docoHelper.convertToAngka((typeof $("#total_tagihan").val() == 'undefined') ? 0 : $("#total_tagihan").val());
    var _total_balance_rs = totalDijamin_rounded.nominal_round - _tagihanPasien;

    $("#tagihan_pasien").html(docoHelper.convertToRupiah(Math.abs(_total_balance_rs)));

    if (_total_balance_rs < 0) {
        _total_balance_rs = 0;
    }


    $("#total_balance_rs").val(docoHelper.convertToRupiah(_total_balance_rs));
    if ($("#total_dibayar").val() != 0) {
        var _totalDibayar = docoHelper.convertToAngka($('#total_dibayar').val());
        var _totalTagihan = docoHelper.convertToAngka((typeof $("#tagihan_pasien").html() == 'undefined') ? 0 : $("#tagihan_pasien").html());
        var _sisa = _totalDibayar - _totalTagihan;

        $("#total_sisa_piutang").val(docoHelper.convertToRupiah(_sisa)).trigger("change");
    }

    // for delete data one by one in table temporary
    $(`.delete-table-multi-penjamin`).on('click', function (e) {
        e.preventDefault()
        var tmpID = $(this).attr('data-id')
        // $(this).closest('tr').remove()
        total_penjamin = parseInt(total_penjamin) - docoHelper.convertToAngka(tmpTablePenjamin[tmpID]["dijamin"]);
        tmpTablePenjamin.splice(tmpID, 1)
        generateTablePenjamin()
        _hitungTagihan()
    })
}

function reloadTagihan() {
    var noTableTra = 1
    var penjaminUtama = ''
    var penjaminUtama_id = null
    _totalDijamin = _totalDijamin_sub = 0
    var _totalDiscount = totalRound = 0;
    var tmpTablePenjamin_id = [];
    var flag_found = true
    var _dibayarPasien = plafonPayer = plafonSubPayer = 0
    var _uangMuka = docoHelper.convertToAngka($('#jumlah_uangmuka').val())
    tmpTableTransaksi.forEach(function (val, key) {
        //Pengecekan dari branch Item tidak dibayarkan
        if(typeof val.is_ditagihkan != "undefined"){
            is_jaminan_ditagihkan = val.is_ditagihkan
        } else {
            is_jaminan_ditagihkan = null
        }
        _listPenjamin.forEach(function(val,key){
            if (val.selected == true){
                penjaminUtama = val.text
                // Apabila ID Penjamin Utama pada defaultPenjamin = NULL
                penjaminUtama_id = parseInt(val.id)
                return;
            }
        })

        if( (is_jaminan_ditagihkan == null || is_jaminan_ditagihkan == true)){
             if(typeof val.dijamin_subpayer == 'undefined' || val.dijamin_subpayer == '' || isNaN(val.dijamin_subpayer)){
                 val.dijamin_subpayer = 0
             }
             if(_listPenjamin.length == 2){
                 val.penjamin = penjaminUtama;
             }
             tmpTablePenjamin_id.push({
                 dijamin : val.dijamin,
                 dijamin_subpayer : parseFloat(val.dijamin_subpayer),
                 penjamin : val.penjamin
             })
         }

        penjamin_main = penjamin_sub = ''
        var _totalDibayar = parseFloat(val.totalDibayar)
        _dibayarPasien += _totalDibayar
        _hargaSatuan = parseFloat(parseFloat(val.harga).toFixed(2))
        _hargaCyto = parseFloat(parseFloat(val.cyto).toFixed(2))
        _totalDiscount += val.nominal_diskon ? val.nominal_diskon : 0
        var _isObat = val.is_obat ? 'obat' : 'tindakan';

        _listPenjamin.forEach(function(value){
            if(value.selected == true){
                penjamin_main = value.text
            } else {
                penjamin_sub = value.text
            }
        })

        $('#tarif-diskon-' + noTableTra + '').html(docoHelper.convertToRupiah(Math.round(parseFloat(val.nominal_diskon))))
        $('#sub-total-' + noTableTra + '').html(docoHelper.convertToRupiah(val.subtotal))
        $('#nama-penjamin-' + noTableTra + '').html(val.penjamin)
        if(typeof val.dijamin_subpayer == 'undefined' || val.dijamin_subpayer == '' || val.dijamin_subpayer == null || isNaN(val.dijamin_subpayer)){
            val.dijamin_subpayer = 0
        }
        $('#total-dijamin-' + noTableTra + '').html(docoHelper.convertToRupiah(val.dijamin) )
        // if(typeof val.plafon_payer == 'undefined' || val.plafon_payer == null || !val.checkPenjamin || val.plafon_payer == 0){
            _totalDibayar =  val.totalDibayar = (!val.checkPenjamin) ? parseFloat(val.subtotal) : _totalDibayar
            $('#harus-dibayar-' + noTableTra + '').html(docoHelper.convertToRupiah(_totalDibayar))
        // } else {
        //     $('#harus-dibayar-' + noTableTra + '').html(0)
        // }
        $('#tarif-satuan-' + noTableTra + '').html(docoHelper.convertToRupiah(_hargaSatuan))
        $('#tarif-cyto-' + noTableTra + '').html(docoHelper.convertToRupiah(_hargaCyto))

        if(_listPenjamin.length >= 2){
            $('#total-dijamin-subpayer-' + noTableTra + '').html(docoHelper.convertToRupiah(parseFloat(val.dijamin_subpayer).toFixed(2)))
            $('#nama-subpayer-' + noTableTra + '').html(penjamin_sub)
            $('#nama-penjamin-' + noTableTra + '').html(penjamin_main)
        }

        if (typeof _cache[_isObat][val.pelayanan_id] != "undefined") {
            if(typeof val.dijamin_subpayer == 'undefined' || val.dijamin_subpayer == '' || isNaN(val.dijamin_subpayer)){
                val.dijamin_subpayer = 0
            }
            _cache[_isObat][val.pelayanan_id].penjamin = val.penjamin
            _cache[_isObat][val.pelayanan_id].dijamin = parseFloat(val.dijamin)
            _cache[_isObat][val.pelayanan_id].dijamin_subpayer = parseFloat(val.dijamin_subpayer)
            _cache[_isObat][val.pelayanan_id].harusbayar = parseFloat(val.totalDibayar)
            _cache[_isObat][val.pelayanan_id].nominal_diskon = parseFloat(val.nominal_diskon)
            _cache[_isObat][val.pelayanan_id].persen_diskon = parseFloat(val.persen_diskon)
            _cache[_isObat][val.pelayanan_id].penjamin_pelayanan_id = (val.defaultPenjamin.id) ? val.defaultPenjamin.id : penjaminUtama_id
            _cache[_isObat][val.pelayanan_id].keterangan = val.keterangan
            _cache[_isObat][val.pelayanan_id].plafon_payer = (val.plafon_payer) ? parseFloat(val.plafon_payer) : 0
            _cache[_isObat][val.pelayanan_id].plafon_subpayer = (val.plafon_subpayer) ? parseFloat(val.plafon_subpayer) : 0

        }

        if (val.plafon_payer != 0 && val.plafon_payer != null && typeof val.plafon_payer != 'undefined' ){
            plafonPayer = parseFloat(val.plafon_payer)
        }
        if (val.plafon_subpayer != 0 && val.plafon_subpayer != null && typeof val.plafon_subpayer != 'undefined'){
            plafonSubPayer = parseFloat(val.plafon_subpayer)
        }
        noTableTra++
    })
    var _nilaiTotalPembulatan = _nilaiTotalPembulatan_sub = 0
    var counter_penjamin = 0
    tmpTablePenjamin_id.forEach(function(val,key){
        if(typeof val.dijamin_subpayer == 'undefined' || val.dijamin_subpayer == '' || val.dijamin_subpayer == null || isNaN(val.dijamin_subpayer)){
            val.dijamin_subpayer = 0
        }
        bulatan_jaminan = docoHelper.calculateRounding(val.dijamin)
        bulatan_jaminan_sub = docoHelper.calculateRounding(val.dijamin_subpayer)
        _totalDijamin += ( val.dijamin )
        _totalDijamin_sub += ( val.dijamin_subpayer )
        counter_penjamin++
    })
    round_jaminan_main = docoHelper.calculateRounding(parseFloat(_totalDijamin).toFixed(2))
    round_jaminan_sub = docoHelper.calculateRounding(parseFloat(_totalDijamin_sub).toFixed(2))
    round_plafon_main = docoHelper.calculateRounding(parseFloat(plafonPayer).toFixed(2))
    round_plafon_sub = docoHelper.calculateRounding(parseFloat(plafonSubPayer).toFixed(2))

    if(plafonPayer == 0){
        _totalDijamin = parseFloat(round_jaminan_main.nominal_round) + parseFloat(round_jaminan_sub.nominal_round)
        totalRound = parseFloat(round_jaminan_main.nominal_selisih) + parseFloat(round_jaminan_sub.nominal_selisih)
    } else {
        _totalDijamin = parseFloat(round_plafon_main.nominal_round) + parseFloat(round_plafon_sub.nominal_round)
        totalRound = parseFloat(round_plafon_main.nominal_selisih) + parseFloat(round_plafon_sub.nominal_selisih)
    }
    _totalDiscount =  Math.round(_totalDiscount);
    $("#subsidi-asuransi").val(docoHelper.convertToRupiah(_totalDijamin));
    $("#subsidi-asuransi_pembulatan").val(totalRound);

    $("#total_diskon").val(docoHelper.convertToRupiah(_totalDiscount));
    _hitungTagihan()
    // _hitungDiskon()
}

$(document).on("click", "#recalculate-payer", function (event) {
    event.preventDefault();
    showLoader();
    getKontrakPenjamin();
    setTimeout(() => {
        hideLoader()
        }, 1000);
});

function getKontrakPenjamin(){
    let params = {
        penjamin: JSON.stringify(_listPenjamin),
        tgl_pendaftaran: _tglPendaftaran,
    };
    $.ajax({
        type: 'POST',
        url: '/kasir/pembayaran-tagihan/get-kontrak-penjamin',
        data: params,
        beforeSend: function () {
            showLoader();
        },
        success: function (res) {
            if(res.response){
                _kontrakPenjamin = res.response;
                if(_kontrakPenjamin.length != 0 ){
                    setKontrakPenjamin(function (){
                        hideLoader()
                    });
                }
            }else{
                hideLoader()
            }
        },
        error: function (res) {

        },
    });
}

function setKontrakPenjamin(){
    tmpTableTransaksi.forEach(function (val, key) {
        let tindakan_obat_id = val.tindakan_obat_id;
        let kelompoktindakan_id = val.kelompoktindakan_id;
        let lob_id = val.lob_id;
        let kelaspelayanan_id = val.kelaspelayanan_id;
        let subtotal = val.subtotal_origin;
        let penjamin_id = val.defaultPenjamin.id;
        let penjamingrade_id = val.penjamingrade_id;

        let max_dijamin = null;
        let dijamin = null;
        let discount = null;

        let _kontrakPenjaminNonAsuransi = {};

        /**Cek Lob ID, Tindakan, kelompok tindakan, dan kelas pelayanan */
        if(typeof _kontrakPenjamin[penjamin_id] !== 'undefined'){
            if(typeof _kontrakPenjamin[penjamin_id][penjamingrade_id] !== 'undefined'){
                if(typeof _kontrakPenjamin[penjamin_id][penjamingrade_id][lob_id] !== 'undefined'){
                    if( typeof _kontrakPenjamin[penjamin_id][penjamingrade_id][lob_id][_tdTindakan] !== 'undefined' && typeof _kontrakPenjamin[penjamin_id][penjamingrade_id][lob_id][_tdTindakan][tindakan_obat_id] !== 'undefined'){
                        max_dijamin = _kontrakPenjamin[penjamin_id][penjamingrade_id][lob_id][_tdTindakan][tindakan_obat_id].max_dijamin;
                        discount = _kontrakPenjamin[penjamin_id][penjamingrade_id][lob_id][_tdTindakan][tindakan_obat_id].disc_persen;
                    }else if(typeof _kontrakPenjamin[penjamin_id][penjamingrade_id][lob_id][_tdKelompok] !== 'undefined' && typeof _kontrakPenjamin[penjamin_id][penjamingrade_id][lob_id][_tdKelompok][kelompoktindakan_id] !== 'undefined'){
                        max_dijamin = _kontrakPenjamin[penjamin_id][penjamingrade_id][lob_id][_tdKelompok][kelompoktindakan_id].max_dijamin;
                        discount = _kontrakPenjamin[penjamin_id][penjamingrade_id][lob_id][_tdKelompok][kelompoktindakan_id].disc_persen;
                    }else if(typeof _kontrakPenjamin[penjamin_id][penjamingrade_id][lob_id][_tdKelas] !== 'undefined' && typeof _kontrakPenjamin[penjamin_id][penjamingrade_id][lob_id][_tdKelas][kelaspelayanan_id] !== 'undefined'){
                        max_dijamin = _kontrakPenjamin[penjamin_id][penjamingrade_id][lob_id][_tdKelas][kelaspelayanan_id].max_dijamin;
                        discount = _kontrakPenjamin[penjamin_id][penjamingrade_id][lob_id][_tdKelas][kelaspelayanan_id].disc_persen;
                    }
                }
            }else{
                objVal = Object.values(_kontrakPenjamin[penjamin_id])[0];
                if(!_pasienAsuransi && typeof objVal != "undefined"){
                    _kontrakPenjaminNonAsuransi = objVal;
                    if(typeof _kontrakPenjaminNonAsuransi[lob_id] !== 'undefined'){
                        if( typeof _kontrakPenjaminNonAsuransi[lob_id][_tdTindakan] !== 'undefined' && typeof _kontrakPenjaminNonAsuransi[lob_id][_tdTindakan][tindakan_obat_id] !== 'undefined'){
                            max_dijamin = _kontrakPenjaminNonAsuransi[lob_id][_tdTindakan][tindakan_obat_id].max_dijamin;
                            discount = _kontrakPenjaminNonAsuransi[lob_id][_tdTindakan][tindakan_obat_id].disc_persen;
                        }else if(typeof _kontrakPenjaminNonAsuransi[lob_id][_tdKelompok] !== 'undefined' && typeof _kontrakPenjaminNonAsuransi[lob_id][_tdKelompok][kelompoktindakan_id] !== 'undefined'){
                            max_dijamin = _kontrakPenjaminNonAsuransi[lob_id][_tdKelompok][kelompoktindakan_id].max_dijamin;
                            discount = _kontrakPenjaminNonAsuransi[lob_id][_tdKelompok][kelompoktindakan_id].disc_persen;
                        }else if(typeof _kontrakPenjaminNonAsuransi[lob_id][_tdKelas] !== 'undefined' && typeof _kontrakPenjaminNonAsuransi[lob_id][_tdKelas][kelaspelayanan_id] !== 'undefined'){
                            max_dijamin = _kontrakPenjaminNonAsuransi[lob_id][_tdKelas][kelaspelayanan_id].max_dijamin;
                            discount = _kontrakPenjaminNonAsuransi[lob_id][_tdKelas][kelaspelayanan_id].disc_persen;
                        }
                    }
                }
            }
        }

        if(max_dijamin != null || max_dijamin != 'undefined' || discount != null || discount != 'undefined'){
            max_dijamin = subtotal < max_dijamin ? subtotal : max_dijamin;
            // diskonItem = parseFloat((discount/100) * subtotal);
            diskonItem = parseFloat((discount/100) * max_dijamin);
            dijamin = max_dijamin-diskonItem;
            dijamin = (dijamin < 0) ? 0 : dijamin;
            tmpTableTransaksi[key].dijamin = dijamin;
            tmpTableTransaksi[key].nominal_diskon = diskonItem;
            tmpTableTransaksi[key].totalDibayar = subtotal - max_dijamin;
            tmpTableTransaksi[key].subtotal = subtotal - diskonItem;
            if(!_pasienAsuransi){
                tmpTableTransaksi[key].dijamin = 0;
                tmpTableTransaksi[key].totalDibayar = subtotal - diskonItem;
            }
        }
    })
    reloadTagihan();
}

$(document).on("click", "#reset-edit-tagihan", function (e) {
    e.preventDefault();
    resetEditTagihan();
    $("#print-detail-edit-tagihan").hide();
    $("#print-summary-edit-tagihan").hide();
});

function resetEditTagihan(skipConfirm=false){
    var isPlafon = false
    var header = 'Perhatian !';
    var message = 'Apakah anda yakin melakukan reset edit tagihan?'
    var label = {
        buttons: {
            'Yes': 'button-yes',
            'No': 'button-no'
        }
    };

    //Reset Tagihan apabila ada Plafon
    tmpTableTransaksi.forEach(function(val,key){
        if(typeof val.plafon_payer !== 'undefined' && val.plafon_payer >0){
            isPlafon = true
            return true
        }
    })

    var _resetTmpData = function (uid) {
        var _sendTmpData = []

        let _dataTmp = {
            _tagihanPasien: _sendTmpData,
            _pendaftaran_id: _pendaftaran_id,
            _pasienmasukpenunjang_id: _pasienmasukpenunjang_id,
            verify_uid: uid || ''
        };

        $.ajax({
            url: `/kasir/pembayaran-tagihan/save-tmp-tagihan?_uidProccess=${_uidProccess}`,
            type: "POST",
            data: JSON.stringify(_dataTmp),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function () {
                docoNotification("success", "Success!", "Data Berhasil Disimpan!")
                location.reload();
            }
        })
    }
    if(!skipConfirm) {
        $.showQuestionDialog(header, message, label, function (reaction) {
            if (reaction == 'Yes') {
                if(typeof _arrDefaultTmp != 'undefined') {
                    tmpTableTransaksi = JSON.parse(_arrDefaultTmp);
                    setBiayaAdminToRow()
                    reloadTagihan();
                    _tmpBiayaAdmin = $('#biaya_administrasi').val()
                    //Apabila ada biaya admin, untuk menghindari saat reset edit tagihan tidak terhitung, halaman perlu di reload
                    if(isPlafon|| docoHelper.convertToAngka(_tmpBiayaAdmin) != 0){
                        _resetTmpData()
                    } else {
                        if(!_isDefaultTmp){
                            docoNotification("success", "Success!", "Data Berhasil Direset!")
                        }else{
                            _resetTmpData()
                        }
                    }
                }
            }
            if (reaction == 'No') {
                hideQuestionDialog();
                $('[data-popup="tooltip"]').tooltip();
            }
        });
    }else{
        if(_isDefaultTmp || typeof changeData == 'undefined'){
            _resetTmpData()
        }
    }

}

function setBiayaAdminToRow(){
    /**
     * Gunakan ini untuk push row biaya admin ke tabel edit tagihan
     */
    _biaya_adm = parseFloat(_total_admin);
    if (typeof tmpTableTransaksi[0] !== "undefined" && _biaya_adm && !_isRekap) {
        var _firstCol = {
            checkPenjamin: tmpTableTransaksi[0].checkPenjamin,
            cyto: 0,
            cyto_origin: 0,
            penyulit: 0,
            penyulit_origin: 0,
            kelompoktindakan_nama: '',
            defaultPenjamin: tmpTableTransaksi[0].defaultPenjamin,
            dijamin: 0,
            totalDibayar: 0,
            harga: _biaya_adm,
            harga_origin: _biaya_adm,
            instalasi: "",
            isPenjamin: tmpTableTransaksi[0].isPenjamin,
            penjamin: tmpTableTransaksi[0].penjamin,
            qty: 1,
            subtotal: _biaya_adm,
            subtotal_origin: _biaya_adm,
            tanggal: _dateNow,
            tindakan: "Biaya Administrasi",
            keterangan: "",
            tindakan_obat_id: null,
            nominal_diskon: 0,
            persen_diskon: 0,
            value: {},
            pelayanan_id: null
        };
        if (_firstCol.isPenjamin) {
            _firstCol.dijamin = _biaya_adm
        } else {
            _firstCol.totalDibayar = _biaya_adm;
        }
        tmpTableTransaksi.push(_firstCol)
    }
}
