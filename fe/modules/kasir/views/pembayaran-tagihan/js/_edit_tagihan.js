/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */
var dijamin_subtotal = []
var tmp_dijamin_admin = 0
var isMultiplePenjamin = false
var isMultiPayer = false
var isPerseorangan = false
var defaultPenjamin_total = 0
var reloadTr = []



var changeData = []
var isChangePrice = false;
var generateChangePrice = {}

//Set data pembulatan
var satuan_pembulatan = parseInt($('#satuan_pembulatan').val())

$(document).ready(function () {
    isChanges = false
    initDataTabels()
    $(".selectpenjamin").select2()
    setTotalSementara()
    if (isPlafon){
    $('#table-edit-tagihan').each((index,elements) => {
        $(elements).find('.edit-harusbayar-parent').each(function(){
            if($(this).hasClass('edit-harusbayar-parent')){
                if($(this).parent().parent().find('.checkPenjamin-parent').is(':checked')){
                    // $(this).val(docoHelper.convertToRupiah(0)).trigger('change')
                }
            }
        })
    })
    }
});

initDataTabels = function () {
    var _dataDijamin = $('#table-edit-tagihan').DataTable({
        language: {
            search: "Pencarian&nbsp;:&nbsp;"
        },
        scrollY: "300px",
        scrollCollapse: true,
        paging: false,
        columnDefs: [
            {
                targets: 0,
                orderable: false
            },
            {
                targets: 7,
                orderable: false,
                width: "20%"
            },
        ],
        order: [[1, "asc"]],
        drawCallback: function (settings) {
        }
    });

    _dataDijamin.on('order.dt search.dt', function () {
        _dataDijamin.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    }).draw();

    setTimeout(function () {
        _dataDijamin.order([[2, "asc"]]).draw()
    }, 1)
}

destroyDataTabels = function () {
    $('#table-edit-tagihan').DataTable().destroy()
}

function initData(id) {
    if (typeof changeData[id] == 'undefined') {
        changeData[id] = {
            isPenjamin: null,
            dijamin: 0,
            totalDibayar: 0,
            penjaminNama: '',
            penjaminId: null,
            subtotal: 0,
            nominal_diskon: 0,
            persen_diskon: 0,
            keterangan: '',
            harga: null,
            cyto: null,
            penyulit: null,
        }
    }
}

function resetExcess(){
    //Reset Excess Penjamin
    dijamin_subtotal = []
    $('#selisih-penjamin').val(0)
    $('#selisih-penjamin-sub').val(0)
    defaultPenjamin_total = 0
    tmp_dijamin_admin = 0
}

function resetPlafonPenjamin(){
    $('#selisih-penjamin').val(0)
    $('#selisih-penjamin-sub').val(0)
    $('#excess-pasien').val(0)
    isPlafon = false
    //Tidak digunakan karena reset edit tagihan untuk mengembalikan nilai 0 ke nilai semula tidak diperlukan
    $('#table-edit-tagihan').each((index,elements) => {
        $(elements).find('.edit-dijamin').each(function(){
            if($(this).hasClass('edit-dijamin')){
                $(this).trigger('change')
            }
        })
        // $(elements).find('.edit-dijamin-parent').each(function(){
        //     if($(this).hasClass('edit-dijamin-parent')){
        //         $(this).trigger('change')
        //     }
        // })
    })
    // var biaya_adm_row = $("td:contains(Biaya Administrasi)").closest('tr');
    // if($(biaya_adm_row).find('.subtotal-kategory').hasClass('subtotal-kategory')){
    //     $(biaya_adm_row).find('.edit-dijamin-parent').trigger('change');
    // }

}

function setTotalSementara() {
    var totalDijamin = totalDijaminSubPayer = subTotalBiaya = 0
    var totalHarusBayar = _totalBayarPlafon = subTotalSementara = 0
    var totalDiskon = totalDiskonNonPlafon = 0
    var selisih_penjamin = docoHelper.convertToAngka($('#selisih-penjamin').val() )
    var selisih_penjamin_sub = docoHelper.convertToAngka($('#selisih-penjamin-sub').val() ) ? parseFloat(docoHelper.convertToAngka($('#selisih-penjamin-sub').val() )) : 0
    var excess_pasien = parseFloat(docoHelper.convertToAngka($('#excess-pasien').val()))
    tmpTableTransaksi.forEach(function (val, key) {
        if (typeof changeData[key] != "undefined") {
        //  subTotalSementara += docoHelper.convertToAngka(val.harga) ini dicomment karena ada bugs, setelah isi plafon total tagihan pasiennya tidak sesuai
            totalDijamin += parseFloat(changeData[key].dijamin)
            if (typeof changeData[key].dijamin_subpayer == 'undefined' || changeData[key].dijamin_subpayer == ''){
                changeData[key].dijamin_subpayer = 0
            }
            totalDijaminSubPayer += parseFloat(changeData[key].dijamin_subpayer)
            totalHarusBayar += parseFloat(changeData[key].totalDibayar)
            subTotalSementara += parseFloat(changeData[key].subtotal)

            //Case apabila ada penggunaan plafon dan ada tindakan yang tidak ter cek dan masuk dalam akumulasi plafon & dikenakan diskon
            if(selisih_penjamin > 0){
                totalDiskonNonPlafon += parseFloat(changeData[key].nominal_diskon)
            }
            totalDiskon += parseFloat( changeData[key].nominal_diskon)
        }
        else {
            totalDijamin += val.dijamin
            if (typeof val.dijamin_subpayer == 'undefined' || val.dijamin_subpayer == '' || isNaN(val.dijamin_subpayer)){
                val.dijamin_subpayer = 0
            }
            totalDijaminSubPayer += parseFloat(val.dijamin_subpayer)
            totalHarusBayar += parseFloat(val.totalDibayar)
            subTotalSementara += parseFloat(val.subtotal)
            totalDiskon += parseFloat(val.nominal_diskon)
        }
        if (typeof changeData[key].harga != 'undefined' && changeData[key].harga != '' && changeData[key].harga != null ){
            tmpChangeHarga = parseFloat(changeData[key].harga)
            tmpChangeCyto = parseFloat(val.cyto)
            if(typeof changeData[key].cyto != 'undefined' && changeData[key].cyto != '' && changeData[key].cyto != null ){
                tmpChangeCyto = parseFloat(changeData[key].cyto)
            }
            tmpChangePenyulit = parseFloat(val.penyulit)
            if(typeof changeData[key].penyulit != 'undefined' && changeData[key].penyulit != '' && changeData[key].penyulit != null ){
                tmpChangePenyulit = changeData[key].penyulit
            }
            tmpHargaTotal = (tmpChangeHarga + tmpChangeCyto + tmpChangePenyulit)
        } else {
            tmpCytoTindakan = (isNaN(val.cyto) || val.cyto == ''|| val.cyto =='0' || val.cyto == null) ? 0 : parseFloat(val.cyto)
            tmpHargaTindakan = (isNaN(val.harga) || val.harga =='')? 0 : parseFloat(val.harga)
            tmpPenyulitTindakan = (isNaN(val.penyulit)|| val.penyulit =='' || val.penyulit == '0' || val.penyulit == null) ? 0 : parseFloat(val.penyulit)
            tmpHargaTotal = (tmpHargaTindakan + tmpCytoTindakan +tmpPenyulitTindakan)
        }
        subTotalBiaya += (parseFloat(tmpHargaTotal * val.qty))
    })

    if((selisih_penjamin > 0) ){
        // totalHarusBayar = subTotalSementara
    }

    excess_pasien = (totalDijamin + totalDijaminSubPayer) - selisih_penjamin - selisih_penjamin_sub
    if(selisih_penjamin == 0 && selisih_penjamin_sub == 0) {
        excess_pasien = 0
    }

    $('#total-diskon').val(docoHelper.convertToRupiah(totalDiskon))
    $('#totaldijamin_main').val(docoHelper.convertToRupiah(totalDijamin))
    $('#total-plafon-main-payer').val(docoHelper.convertToRupiah(totalDijamin))
    $('#total-plafon-sub-payer').val(docoHelper.convertToRupiah(totalDijaminSubPayer))

    if(selisih_penjamin > 0){
        //Set data plafon saat ini di set as-is sesuai perhitungan yang dibuat
        // totalDijamin = selisih_penjamin
        // totalDijaminSubPayer = selisih_penjamin_sub
        // totalHarusBayar -= totalDijamin + totalDijaminSubPayer + excess_pasien
    } else {
        // $('#total-plafon-main-payer').val(docoHelper.convertToRupiah(totalDijamin))
    }

    $('#totaldijamin').val(docoHelper.convertToRupiah(totalDijamin + totalDijaminSubPayer))
    $('#totaldijamin_sub').val(parseFloat(docoHelper.convertToRupiah(totalDijaminSubPayer)))
    if ($('#totaldijamin').val() <= 0 && isPerseorangan && selisih_penjamin <= 0){
        $('.excess_penjamin').hide()
    }
    if(_listPenjamin.length >= 2){
    //    $('#total-plafon-sub-payer').val((docoHelper.convertToRupiah(totalDijaminSubPayer)))
    }

    // Reset properti untuk excess penjamin
    if($('#total-biaya').val() != '' && $('#total-biaya').val() != docoHelper.convertToRupiah(totalHarusBayar + totalDijamin + totalDiskon) || $('#total-biaya').val() == 0){
        dijamin_subtotal = []
    }
    let totalHarusBayarRound =  Math.round(totalHarusBayar);
    $('#total-biaya').val(docoHelper.convertToRupiah(subTotalBiaya))
    $('#totalharusbayar').val(docoHelper.convertToRupiah(totalHarusBayarRound))
    $('#excess-pasien').val(docoHelper.convertToRupiah(excess_pasien))
    //Disable field Plafon Penjamin Apabila ternyata semua kelompok tindakan di uncheck list
    if(totalDijamin + totalDijaminSubPayer <= 0){
        $('#selisih-penjamin').prop('disabled',true)
        $('#selisih-penjamin').css('background-color','#f5f5f5')
        $('#selisih-penjamin-sub').prop('disabled',true)
        $('#selisih-penjamin-sub').css('background-color','#f5f5f5')
        $('#excess-pasien').prop('disabled',true)
        $('#excess-pasien').css('background-color','#f5f5f5')
    } else {
        $('#selisih-penjamin').prop('disabled',false)
        $('#selisih-penjamin').css('background-color','')
        $('#selisih-penjamin-sub').prop('disabled',false)
        $('#selisih-penjamin-sub').css('background-color','')
        $('#excess-pasien').prop('disabled',false)
        $('#excess-pasien').css('background-color','')
    }
}

function setPenjamin(options, defValue) {
    var _html = ''
    options.forEach((data, key) => {
        var _selectedVal = defValue != null || defValue != undefined ? (defValue == data.id ? 'selected' : '') : (data.selected ? 'selected' : '');
        _html += `<option value="${data.id}" ${_selectedVal}>${data.text}</option>`
    })

    return _html
}

_generateDetail = function (object, kelompok) {
    var reloadTr = []
    var _tr = $(object).closest('tr');
    var _span = $(object).find('span:not(.kelompok_harga)');
    var tr = $('<tr/>');
    var td = $('<td/>');
    var _trimKel = kelompok.replace(/[^\w]/g, '')

    var _overWriteTarif = function (val,key) {
        if (_isEditTagihan) {
            return `
                <input type='text'
                        class='form-control doco-number edit-tarif-tagihan text-right'
                        id='edit-tarif-tagihan-${key}'
                        data-id='${key}'
                        value='${docoHelper.convertToRupiah(val.harga)}'>
            `
        } else {
            return docoHelper.convertToRupiah(val.harga)
        }
    }

    if (_span.hasClass('minus')) {
        _span.text('[ + ]')
        _span.removeClass('minus');
        $(`.${_trimKel}`).remove()
        // initDataTabels()
        _span.addClass('plus');
    } else {
        if (typeof groupKelTindakan[kelompok] != "undefined") {
            var _html = ''
            showLoader()
            _span.text('[ - ]')
            _span.removeClass('plus');
            setTimeout(function () {
                $(`.${_trimKel}`).remove()
                Object.keys(groupKelTindakan[kelompok]).forEach(function (key) {
                    var val = groupKelTindakan[kelompok][key];
                    var isDisabled = 'disabled'
                    var isReadonly = 'readonly'
                    var totalHarusBayar = val.totalDibayar
                    var totalHarusDijamin = val.dijamin
                    var totalHarusDijaminSub = val.dijamin_subpayer
                    isDisabledListPenjamin = 'disabled'
                    isDisabledSelect = 'disabled';
                    isDisabledSelectList = 'disabled';
                    setDataSubPenjamin = ''
                    setDataPenjamin = setPenjamin(_listPenjamin, val.defaultPenjamin.id)
                    if (val.checkPenjamin) {
                        isDisabled = 'enabled'
                        isDisabledListPenjamin = 'enabled'
                        isReadonly = ''
                    } else if((docoHelper.convertToAngka($('#selisih-penjamin').val()) > 0) || (docoHelper.convertToAngka($('#totaldijamin').val()) > 0)){
                        isDisabled = 'enabled'
                    }

                    var _isChecked = val.checkPenjamin ? 'checked' : '';
                    var _percenDis = val.persen_diskon ? 'checked' : '';

                    if(typeof val.dijamin_subpayer == 'undefined' || val.dijamin_subpayer == '' || isNaN(val.dijamin_subpayer)){
                        val.dijamin_subpayer = 0
                    }

                    if(val.dijamin < 1 && (isMultiPayer && val.dijamin_subpayer < 1) && !($('.excess_penjamin').is(':visible')) ){
                        _isChecked = ''
                        isReadonly = 'readonly'
                    }

                    if(isMultiPayer){
                        isDisabled = ''
                        isDisabledListPenjamin = 'disabled'
                        setDataPenjamin = '<option selected>'+ $('.excess-label-main').html() +'</option>'
                        setDataSubPenjamin = encodeURI($('.excess-label-sub').html())
                        isDisabledSelectList = 'disabled'
                    }

                    if(isPlafon){
                        isDisabled = ''
                        if(val.checkPenjamin){
                            _isChecked = 'checked'
                            totalHarusBayar = 0
                            isReadonly = ''
                        } else {
                            totalHarusBayar = val.subtotal
                        }
                    }

                    unbalanceTagihan = false
                    tmpHarga = parseFloat(val.harga)
                    tmpCyto = (isNaN(val.cyto) || val.cyto == ''|| val.cyto =='0' || val.cyto == null) ? 0 : parseFloat(val.cyto)
                    tmpPenyulit = (isNaN(val.penyulit)|| val.penyulit =='' || val.penyulit == '0' || val.penyulit == null) ? 0 : parseFloat(val.penyulit)
                    tmpQty =  parseFloat(val.qty)
                    tmpDijamin = parseFloat(val.dijamin)
                    tmpDijaminSubpayer = (isNaN(val.dijamin_subpayer) || val.dijamin_subpayer =='' || val.dijamin_subpayer == '0' || val.dijamin_subpayer == null) ? 0 : parseFloat(val.dijamin_subpayer)
                    tmpTotalDibayar = parseFloat(val.totalDibayar)
                    tmpNominalDiskon = (isNaN(val.nominal_diskon)|| val.nominal_diskon =='' || val.nominal_diskon == '0' || val.nominal_diskon == null) ? 0 : parseFloat(val.nominal_diskon)
                    tmpValidasiHarga = parseFloat(tmpQty*(tmpHarga + tmpCyto + tmpPenyulit)).toFixed(2)
                    tmpValidasiPembayaran = parseFloat(tmpDijamin + tmpDijaminSubpayer + tmpTotalDibayar +tmpNominalDiskon).toFixed(2)
                    tmpValidasi = tmpValidasiHarga - tmpValidasiPembayaran
                    styleError = ''
                    if(tmpTotalDibayar < 0 || tmpNominalDiskon < 0 || tmpDijaminSubpayer < 0 || tmpDijamin < 0 || tmpQty < 0 || tmpHarga < 0){
                        unbalanceTagihan = true
                    }
                    if(tmpValidasi != 0 || tmpValidasiHarga < 0 || tmpValidasiPembayaran < 0 || unbalanceTagihan == true){
                    // styleError = 'style="background-color:#4fc290"' //Ini Style untuk versi warna hijau
                        styleError = 'style="background-color:#ff9ca2"'

                    //Gunakan ini untuk otomatisasi validasi #otomatisasiValidasi
                        // reloadTr.push(key)
                        // if(val.checkPenjamin){
                        //     _isChecked = 'checked'
                        //     totalHarusBayar = 0
                        //     totalHarusDijamin = parseFloat(tmpQty*(tmpHarga + tmpCyto + tmpPenyulit)).toFixed(2)
                        //     totalHarusDijaminSub = 0
                        //     isReadonly = ''
                        // } else {
                        //     _isChecked = ''
                        //     totalHarusBayar = parseFloat(tmpQty*(tmpHarga + tmpCyto + tmpPenyulit)).toFixed(2)
                        //     totalHarusDijamin = 0
                        //     totalHarusDijaminSub = 0
                        //     isReadonly = ''
                        // }
                    }

                    _html += `
                    <tr data-id='${key}' class='${_trimKel}' `+ styleError +`>
                        <td></td>
                        <td>
                            ${val.tanggal}
                        </td>
                        <td>
                            ${val.instalasi}
                        </td>
                        <td style="white-space:revert!important;">
                            ${val.tindakan}
                        </td>
                        <td class="text-right">
                            <p id='qty-${key}' class='qty'>${val.qty}</p>
                        </td>
                        <td class="text-right">
                            ${_overWriteTarif(val,key)}
                        </td>
                        <td class="text-right">
                            ${docoHelper.convertToRupiah(val.cyto)}
                        </td>
                        <td class="bg-yellow">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group highlight-addon has-size-sm field-persen">
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <label>
                                                    <input type="checkbox"
                                                            id="persen-discount-chk-${key}"
                                                            class="persen-discount-chk" value="1" ${_percenDis}> &nbsp;
                                                    <i class="fa fa-percent" aria-hidden="true"></i>
                                                </label>
                                            </span>
                                            <input type="text"
                                                    id="percent-discount-${key}"
                                                    class="form-control input-sm text-right doco-number percent-discount"
                                                    autocomplete="off"
                                                    value="${val.persen_diskon}"
                                                    ${val.persen_diskon ? '' : 'disabled'}>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group highlight-addon has-size-sm field-tot-diskon">
                                        <input type="text"
                                                name='nominal-discount'
                                                class="form-control input-sm text-right doco-number nominal-discount"
                                                value="${docoHelper.convertToRupiah(Math.round(parseFloat(val.nominal_diskon)))}"
                                                autocomplete="off"
                                                id="nominal-discount-${key}"
                                                ${val.persen_diskon ? 'disabled' : ''}>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="text-right">
                            <p id='subtotal-${key}' class='subtotal_detail'>${docoHelper.convertToRupiah(val.subtotal)}</p>
                        </td>
                        <td>
                            <div class="input-group">
                                <label class="input-group-addon">
                                    <input type="checkbox"
                                            class="checkPenjamin"
                                            data-id='${key}'
                                            data-kelompok="${kelompok}"
                                            id="isPenjamin-${key}" ${isDisabled} ${_isChecked}>
                                </label>
                                <select name='penjamin'
                                        id='penjamin-${key}'
                                        data-id='${key}'
                                        class="select2 form-control
                                        selectpenjamin" disabled>${setDataPenjamin}</select>
                            </div>
                        </td>
                        <td>
                            <input type='text'
                                    name='edit-dijamin'
                                    class='form-control edit-dijamin doco-number text-right'
                                    id='edit-dijamin-${key}'
                                    data-id='${key}' ${isReadonly}
                                    value='${docoHelper.convertToRupiah(totalHarusDijamin)}'>
                        </td>
                        <td class='row-nama-subpayer'>
                        <input type='text'
                            class='form-control doco-number nama-edit-dijamin-sub'
                            readonly='true' disabled
                            value='${setDataSubPenjamin}'
                        </td>
                        <td class='row-subpayer'>
                            <input type='text'
                                    name='edit-dijamin-sub'
                                    class='form-control edit-dijamin-sub doco-number text-right'
                                    id='edit-dijamin-sub-${key}'
                                    data-id='${key}' ${isReadonly}
                                    value='${docoHelper.convertToRupiah(totalHarusDijaminSub)}'>
                        </td>
                        <td>
                            <input type='text'
                                    class='form-control doco-number edit-harusbayar text-right'
                                    readonly='true'
                                    name='edit-harusbayar'
                                    id='harusbayar-${key}'
                                    value='${docoHelper.convertToRupiah(totalHarusBayar)}'
                        </td>
                        <td>
                            <input type="text"
                                    class="form-control input-sm keterangan"
                                    autocomplete="off"
                                    value='${val.keterangan}'>
                        </td>
                    </tr>
                    `
                });
                $(_html).insertAfter(_tr);
                if(_listPenjamin.length < 2){
                    $('#table-edit-tagihan .row-subpayer').remove()
                    $('#table-edit-tagihan .row-nama-subpayer').remove()
                } else {
                    $('#table-edit-tagihan').each((index,element) => {
                        $(element).find('.nama-edit-dijamin-sub').each(function(){
                            _decodedData = decodeURI($(this).val())
                            $(this).val(_decodedData)
                        })
                    })
                }
                $('.select2').select2()
                _span.addClass('minus');
                hideLoader()
                // initDataTabels()

                penjaminIdCheck = 0;
                var _message = 'Nilai Jaminan tidak boleh lebih besar daripada Sub Total'

                $('.selectpenjamin').on('change', function (e) {
                    e.preventDefault()
                    var defaultPenjaminId = 0;
                    var _dataId = $(this).data('id');
                    var _parentTr = $(this).closest('tr');
                    var _classparent = _parentTr.attr('class').split(' ')[0]

                    var _valDijamin = _parentTr.find('.edit-dijamin').val();
                    var _valBayar = _parentTr.find('.edit-harusbayar').val();

                    var _penjaminId = parseInt($(this).val());
                    var _penjaminText = $(this).find(':selected').text();

                    var _dijamin = docoHelper.convertToAngka(_valDijamin);
                    var _bayar = docoHelper.convertToAngka(_valBayar);
                    $('tr[data-id="' + _classparent + '"]').find('.selectpenjamin-parent').append(`<option value=" " selected>  </option>`);

                    initData(_dataId)

                    changeData[_dataId].isPenjamin = $(`#isPenjamin-${_dataId}`).is(':checked') ? 'checked' : 'uncheck';
                    changeData[_dataId].dijamin = _dijamin
                    changeData[_dataId].totalDibayar = _bayar
                    changeData[_dataId].penjaminNama = _penjaminText
                    changeData[_dataId].penjaminId = _penjaminId
                    if (typeof groupKelTindakan[_classparent][_dataId] != "undefined") {
                        groupKelTindakan[_classparent][_dataId].dijamin = _dijamin
                        groupKelTindakan[_classparent][_dataId].totalDibayar = _bayar
                        groupKelTindakan[_classparent][_dataId].isPenjamin = $(`#isPenjamin-${_dataId}`).is(':checked') ? 'checked' : 'uncheck';
                        _reCalKateg(_classparent);
                    }
                })

                $('.edit-dijamin').on('focusin', function (e) {
                    $(this).data('val', $(this).val());
                });

                $('.edit-dijamin').on('change', function (e) {
                    e.preventDefault()
                    isChanges = true
                    var _parentTr = $(this).closest('tr')
                        _parentTr.css('background-color','')
                    var _classparent = _parentTr.attr('class').split(' ')[0]
                    var id = $(this).attr('data-id')
                    var _params = {}
                    isMultiPayer = (_listPenjamin.length <2) ? false : true
                    var hargaOrigin = 0
                    if (typeof groupKelTindakan[_classparent][id] != "undefined") {
                        var _baseData = groupKelTindakan[_classparent][id]
                        hargaOrigin = parseFloat(docoHelper.convertToAngka(_baseData.subtotal_origin))
                    }

                    var nominalDiscount = docoHelper.convertToAngka($(`#nominal-discount-${id}`).val())
                    var nominalCurrent = parseFloat(docoHelper.convertToAngka($(this).val()))

                    var _nominalDijaminSubPayer = _nominalDibayar = 0
                    var _edit_dijamin_sub = $('#edit-dijamin-sub-' + id)
                    var _edit_harusbayar = $('#harusbayar-' + id)

                    if(_edit_dijamin_sub.length != 0) {
                        _nominalDijaminSubPayer = _edit_dijamin_sub.val()
                    }

                    _nominalDijaminSubPayer = isMultiPayer ? parseFloat(docoHelper.convertToAngka(_nominalDijaminSubPayer)) : 0
                    _nominalDibayar = docoHelper.convertToAngka(_edit_harusbayar.val())

                    if($(`#isPenjamin-${id}`).is(':checked')) {
                        initData(id)
                        if(nominalDiscount > 0) {
                            if(nominalCurrent > hargaOrigin) {
                                $(this).val(docoHelper.convertToRupiah(hargaOrigin - _nominalDijaminSubPayer - _nominalDibayar - nominalDiscount))
                                docoNotification("warning", "Kesalahan Inputan", 'Total tagihan tidak bisa lebih dari Sub Total');
                                return false
                            }
                            else {
                                _params = {
                                    diskon:nominalDiscount,
                                    nominalMainPayer:nominalCurrent,
                                    nominalSubPayer:_nominalDijaminSubPayer,
                                    nominalPasien:_nominalDibayar,
                                    hargaOrigin:hargaOrigin,
                                }
                                resetDiskonMainPayer(_params, _parentTr)
                            }
                        }
                        else {
                            if(nominalCurrent > _nominalDibayar && _nominalDibayar != 0 || nominalCurrent > hargaOrigin) {
                                docoNotification("warning", "Kesalahan Inputan", 'Total tagihan tidak bisa lebih dari Sub Total');
                                $(this).val(docoHelper.convertToRupiah(hargaOrigin))
                                _edit_dijamin_sub.val(0)
                                _edit_harusbayar.val(0)

                                changeData[id].dijamin = hargaOrigin
                                changeData[id].dijamin_subpayer = 0
                                changeData[id].totalDibayar = 0

                                groupKelTindakan[_classparent][id].dijamin = hargaOrigin
                                groupKelTindakan[_classparent][id].dijamin_subpayer = 0
                                groupKelTindakan[_classparent][id].totalDibayar = 0

                                $(`#subtotal-${id}`).text(docoHelper.convertToRupiah(hargaOrigin))
                                $(`#subtotal-kategory-${_classparent}`).text(hargaOrigin)
                                setTotalSementara();
                                _reCalKateg(_classparent);
                                return false
                            }

                            _params = {
                                nominalDijamin:nominalCurrent,
                                nominalDijaminSub:_nominalDijaminSubPayer,
                                nominalDibayar:_nominalDibayar,
                                hargaOrigin:hargaOrigin,
                            }
                            setNominal(_params, _parentTr)
                        }
                    }
                })

                $('.edit-dijamin-sub').on('change', function (e) {
                    e.preventDefault()
                    isChanges = true
                    isMultiPayer = (_listPenjamin.length <2) ? false : true
                    var nominalCurrent = parseFloat(docoHelper.convertToAngka($(this).val()))
                    var _parentTr = $(this).closest('tr')
                    var id = $(this).attr('data-id')
                    var _classparent = _parentTr.attr('class').split(' ')[0]
                    var hargaOrigin = 0
                    if (typeof groupKelTindakan[_classparent][id] != "undefined") {
                        var _baseData = groupKelTindakan[_classparent][id]
                        hargaOrigin = parseFloat(docoHelper.convertToAngka(_baseData.subtotal_origin))
                    }

                    // var hargaOrigin = parseFloat(docoHelper.convertToAngka($(`#edit-tarif-tagihan-${id}`).val()))
                    var subTotal = parseFloat(docoHelper.convertToAngka($(`#subtotal-${id}`).text()))
                    var nominalDijamin = parseFloat(docoHelper.convertToAngka($(`#edit-dijamin-${id}`).val()))
                    var nominalDibayar = parseFloat(docoHelper.convertToAngka($(`#harusbayar-${id}`).val()))
                    var nominalDiscount = parseFloat(docoHelper.convertToAngka($(`#nominal-discount-${id}`).val()))
                    var nominalPayer = nominalDijamin + nominalCurrent

                    if (subTotal < nominalPayer && nominalDiscount == 0) {
                        docoNotification("warning", "Kesalahan Inputan", 'Nilai Jaminan tidak boleh lebih besar daripada Sub Total')
                        $(this).val(docoHelper.convertToRupiah(subTotal - nominalDijamin - nominalDibayar))
                        return false
                    } else {
                        if(nominalDiscount > 0) {
                            if(nominalDibayar > 0 && nominalDijamin == 0 || nominalDibayar == 0 && nominalDijamin > 0 || nominalDibayar > 0 && nominalDijamin > 0) {
                                _message = 'Diskon tidak dapat di input di Sub Payer.'
                                docoNotification("warning", "Kesalahan Inputan", _message)
                                $(this).val(docoHelper.convertToRupiah(subTotal - nominalDijamin - nominalDibayar))
                                return false
                                // _params = {
                                //     diskon:nominalDiscount,
                                //     nominalMainPayer: nominalDijamin,
                                //     nominalSubPayer: nominalCurrent,
                                //     nominalPasien: _nominalDibayar,
                                //     hargaOrigin:hargaOrigin
                                // }
                                // resetDiskonSubPayer(_params, _parentTr, _message)
                            }
                        }
                        else {
                            if(nominalCurrent > (hargaOrigin)) {
                                docoNotification("warning", "Kesalahan Inputan", 'Total tagihan tidak bisa lebih dari Sub Total');
                                return false
                            }

                            _params = {
                                nominalDijamin:nominalDijamin,
                                nominalCurrent:nominalCurrent,
                                nominalDibayar: nominalDibayar,
                                hargaOrigin:subTotal,
                            }

                            initData(id)
                            setNominalSubPayer(_params, _parentTr)
                        }
                    }
                })

                $('.edit-tarif-tagihan').on('change', function (e) {
                    e.preventDefault();
                    var _parentTr = $(this).closest('tr')
                    var _classparent = _parentTr.attr('class').split(' ')[0]
                    var id = $(this).attr('data-id')
                    var nominalTarif = parseFloat(docoHelper.convertToAngka($(this).val()))
                    var _baseData = {}
                    // if(docoHelper.convertToAngka($('#selisih-penjamin').val())){
                    //     resetPlafonPenjamin()
                    // }

                    initData(id)
                    if (typeof groupKelTindakan[_classparent][id] != "undefined") {
                        _baseData = groupKelTindakan[_classparent][id];
                        _tarifSatuan = parseFloat(_baseData.harga_origin);
                        _tarifCyto = parseFloat(_baseData.cyto_origin);
                        _tarifPenyulit = parseFloat(_baseData.penyulit_origin);
                        _qtyTrans = parseFloat(_baseData.qty);
                        // if (nominalTarif) {
                            groupKelTindakan[_classparent][id].harga = nominalTarif
                            _percentTarif = nominalTarif/(_tarifSatuan == 0 ? 1 : _tarifSatuan);
                            groupKelTindakan[_classparent][id].cyto = _tarifCyto * _percentTarif
                            groupKelTindakan[_classparent][id].penyulit = _tarifPenyulit * _percentTarif
                            groupKelTindakan[_classparent][id].subtotal = (nominalTarif + _baseData.cyto + _baseData.penyulit) * _qtyTrans;
                            groupKelTindakan[_classparent][id].subtotal_origin = (nominalTarif + _baseData.cyto + _baseData.penyulit) * _qtyTrans;

                            changeData[id].harga = _baseData.harga
                            changeData[id].cyto = _baseData.cyto
                            changeData[id].penyulit = _baseData.penyulit
                            changeData[id].subtotal = _baseData.subtotal
                            _parentTr.find('.persen-discount-chk').prop("checked", false).trigger('change')
                            if (_baseData.isPenjamin && _baseData.checkPenjamin) {
                                changeData[id].dijamin = _baseData.subtotal
                                groupKelTindakan[_classparent][id].dijamin = _baseData.subtotal
                                groupKelTindakan[_classparent][id].dijamin_subpayer = 0
                                _parentTr.find('.edit-dijamin').val(docoHelper.convertToRupiah(_baseData.subtotal));
                                _parentTr.find('.edit-harusbayar').val(0)
                                _parentTr.find('.edit-dijamin-sub').val(0)
                                _parentTr.find('.checkPenjamin').prop('checked', true);
                                _parentTr.find('.selectpenjamin ').prop('disabled', false);
                            } else {
                                changeData[id].totalDibayar = _baseData.subtotal
                                groupKelTindakan[_classparent][id].totalDibayar = _baseData.subtotal
                                _parentTr.find('.edit-dijamin').val(0);
                                _parentTr.find('.edit-dijamin-sub').val(0);
                                _parentTr.find('.edit-harusbayar').val(docoHelper.convertToRupiah(_baseData.subtotal))
                            }

                            _recallBiayaAdm();
                            _reCalKateg(_classparent);
                            setTotalSementara();
                            $(`.${_classparent}`).remove()

                            //Rekalkulasi jumlah total pada Header
                            var subTotalHeader = 0
                            Object.values(groupKelTindakan[_classparent]).forEach(function(valTmp){
                            subTotalHeader += parseFloat(valTmp.harga)
                            })
                            rowHeader = $('tr[data-id="'+_classparent+'"]')
                            rowHeader.find('.kelompok_harga').html('<b> Rp('+ docoHelper.convertToRupiah(subTotalHeader) +')</b>')

                            _generateDetail($(`tr[data-id="${_classparent}"]`).find('.nominal-discount-parent'), _classparent)
                    }
                })

                var _recallBiayaAdm = function () {
                    var _biayaAdm = parseFloat(_instance_adm.biayaResep);
                    var _totalTagihan = _totalJpk = 0
                    subtotalInsurance = [];
                    subtotalPrivate = 0;
                    var _listJpk = _instance_adm.listJpk
                    Object.keys(groupKelTindakan).forEach(function (key) {
                        Object.values(groupKelTindakan[key]).forEach(function (value) {
                            if (key) {
                                var _tindakanId = value.tindakan_obat_id
                                _totalTagihan += parseFloat(value.subtotal_origin)
                                if (!value.is_obat && _listJpk.includes(_tindakanId)) {
                                    _totalJpk += parseFloat(value.subtotal_origin)
                                }

                                if(value.dijamin != 0) {
                                    if (typeof subtotalInsurance[value.penjamin] !== 'undefined'){
                                        subtotalInsurance[value.penjamin] = value.dijamin
                                    } else {
                                        if(subtotalInsurance[value.penjamin] != 0){
                                            _totalTagihan += satuan_pembulatan
                                        }
                                        subtotalInsurance[value.penjamin] = value.dijamin
                                    }
                                } else {
                                    subtotalPrivate += value.subtotal_origin
                                }
                            }
                        })
                    })

                    if (_info.pasienadmisi_id) {
                        if (_totalTagihan > 0) {
                            _totalTagihan += _biayaAdm
                            var _percentAdm = _instance_adm.admPersen
                            var _maxAdm = _instance_adm.maxAdm

                            _nominalAdm = (_totalTagihan - _totalJpk) * _percentAdm;
                            _nominalAdm += parseFloat(_instance_adm.biayaResep);
                            _biayaAdm = _nominalAdm;
                            if (_nominalAdm > _maxAdm && _maxAdm != 0) {
                                _biayaAdm = _maxAdm;
                            }
                        }

                        if (typeof groupKelTindakan[""] != "undefined") {
                            Object.keys(groupKelTindakan[""]).forEach(function (key) {
                                groupKelTindakan[""][key].harga = _biayaAdm
                                groupKelTindakan[""][key].subtotal = _biayaAdm
                                groupKelTindakan[""][key].subtotal_origin = _biayaAdm
                                initData(key)
                                changeData[key].dijamin = 0;
                                changeData[key].totalDibayar = 0;
                                changeData[key].subtotal = _biayaAdm;
                                changeData[key].harga = _biayaAdm;
                            })
                            $(`tr[data-id=""]`).find('td:eq(5)').text(docoHelper.convertToRupiah(_biayaAdm));
                            $(`tr[data-id=""]`).find('.nominal-discount-parent').val(0);
                            $(`tr[data-id=""]`).find('.persen-discount-chk-parent').prop("checked", false).trigger("change");
                        }

                    }
                    isChangePrice = true
                    generateChangePrice = {
                        _biayaAdm: _biayaAdm,
                        _totalTagihan: _biayaAdm + _totalTagihan,
                        _subtotalInsurance : subtotalInsurance,
                        _subtotalPrivate : subtotalPrivate
                    }

                    // $('#biaya_administrasi').val(_biayaAdm).trigger('change')
                    // $('#total_tagihan').val(_biayaAdm + _totalTagihan).trigger('change')
                }

                $('.percent-discount').on('change', function (e) {
                    e.preventDefault()

                    var _trParent = $(this).closest('tr');
                    _trParent.css('background-color','')
                    var _dataId = _trParent.data('id');
                    var _classparent = _trParent.attr('class').split(' ')[0]

                    if (typeof tmpTableTransaksi[_dataId] != "undefined" && typeof groupKelTindakan[_classparent][_dataId] != "undefined") {
                        var _percentDist = docoHelper.convertToAngka($(this).val());
                        var _resourceDat = groupKelTindakan[_classparent][_dataId];
                        var _dijaminPayer = docoHelper.convertToAngka($(`#edit-dijamin-${_dataId}`).val())
                        var _edit_dijamin_sub = _trParent.find('#edit-dijamin-sub-' + _dataId)
                        var _hargaOrigin = _resourceDat.subtotal_origin;
                        var _dijaminSubPayer = 0
                        if(_edit_dijamin_sub.length == 0) {
                            _dijaminSubPayer = 0
                        }
                        else {
                            _dijaminSubPayer = $(`#edit-dijamin-sub-${_dataId}`).val()
                        }

                        var _dibayar = docoHelper.convertToAngka($(`#harusbayar-${_dataId}`).val())
                        var _subTotal = _hargaOrigin;
                        // if(!_resourceDat.checkPenjamin) {
                        //     _dibayar = _hargaOrigin
                        //     _dijaminPayer = 0
                        // }

                        // if(_dijaminPayer == 0 && _dijaminSubPayer == 0 && _dibayar > 0) {
                        //     _subTotal = _dibayar
                        // }
                        // else if(_dibayar > 0 && _dijaminPayer > 0 ) {
                        //     _subTotal = _dijaminPayer
                        // }

                        let diskonItem = Math.round(parseFloat(_subTotal * (_percentDist / 100)));
                        var _valueDiskon = diskonItem;

                        if (_percentDist > 100) {
                            docoNotification("error", "Data Tidak Valid!", "Persen tidak boleh lebih dari 100");
                            $(this).val(0).trigger('change')
                            return false;
                        }
                        initData(_dataId)
                        changeData[_dataId].persen_diskon = _percentDist
                        groupKelTindakan[_classparent][_dataId].persen_diskon = _percentDist
                        _trParent.find('.nominal-discount').val(docoHelper.convertToRupiah(_valueDiskon)).trigger('change')
                    }
                })

                $('.nominal-discount').on('change', function (e) {
                    e.preventDefault()
                    var input_disc = Math.round(parseFloat(docoHelper.convertToAngka($(this).val())))
                    $(this).val(input_disc)
                    isMultiPayer = (_listPenjamin.length <2) ? false : true
                    isChanges = true
                    var _nominalDijaminSubPayer = _nominalDijaminMainPayer = _nominalDibayar = 0
                    var _trParent = $(this).closest('tr');
                        _trParent.css('background-color','')

                    var _dataId = _trParent.data('id');
                    var _classparent = _trParent.attr('class').split(' ')[0]
                    var _edit_dijamin = $('#edit-dijamin-' + _dataId)
                    var _edit_dijamin_sub = $('#edit-dijamin-sub-' + _dataId)
                    var _edit_harusbayar = $('#harusbayar-' + _dataId)
                    var _params = {}
                    var _isValid = true

                    if (typeof tmpTableTransaksi[_dataId] != "undefined" && typeof groupKelTindakan[_classparent][_dataId] != "undefined") {
                        var _valueDiskon = Math.round(parseFloat(docoHelper.convertToAngka($(this).val())))
                        var _resourceDat = groupKelTindakan[_classparent][_dataId];
                        var _hargaOrigin = parseFloat(_resourceDat.subtotal_origin);
                        var _subTotal = parseFloat( _resourceDat.subtotal_origin);
                        var isPenjamin = _resourceDat.isPenjamin
                        var checkPenjamin = _resourceDat.checkPenjamin

                        if(_edit_dijamin.length != 0) {
                            _nominalDijaminMainPayer = parseFloat(docoHelper.convertToAngka(_edit_dijamin.val()))
                        }

                        if(_edit_dijamin_sub.length != 0) {
                            _nominalDijaminSubPayer = parseFloat(docoHelper.convertToAngka(_edit_dijamin_sub.val()))
                        }

                        if(_edit_harusbayar.length != 0) {
                            _nominalDibayar = parseFloat(docoHelper.convertToAngka(_edit_harusbayar.val()))
                        }

                        _nominalDijaminMainPayer = _hargaOrigin - _nominalDijaminSubPayer - _nominalDibayar
                        _nominalDijaminSubPayer = _hargaOrigin - _nominalDijaminMainPayer - _nominalDibayar + _valueDiskon
                        _nominalDibayar = _hargaOrigin - _nominalDijaminMainPayer - _nominalDijaminSubPayer + _valueDiskon

                        if(!checkPenjamin) {
                            _nominalDijaminSubPayer = _hargaOrigin - _nominalDijaminMainPayer - _nominalDibayar
                            _nominalDibayar = _hargaOrigin - _nominalDijaminMainPayer - _nominalDijaminSubPayer
                        }

                        if(_valueDiskon > 0) {
                            if (_valueDiskon > _subTotal) {
                                _isValid = false
                                _message = 'Diskon item tidak bisa lebih dari Total Tagihan'
                            }

                            if(checkPenjamin) {
                                if(_valueDiskon > _nominalDijaminMainPayer) {
                                    _isValid = false
                                    _message = 'Diskon tidak dapat melebihi Main Payer.'

                                    if(_subTotal == _nominalDijaminMainPayer) {
                                        _message = 'Diskon item tidak bisa lebih dari Total Tagihan'
                                    }

                                    if(_nominalDijaminSubPayer > 0 && _nominalDijaminMainPayer == 0) {
                                        _message = 'Diskon tidak dapat di input di Sub Payer.';
                                    }
                                }
                            }
                            else {
                                if(_valueDiskon > _hargaOrigin || _valueDiskon > (_nominalDibayar + _valueDiskon)) {
                                    _params = {
                                        diskon: _valueDiskon,
                                        hargaOrigin:_hargaOrigin,
                                        nominalPasien:_nominalDibayar
                                    }
                                    resetDiskonPasien(_params, _trParent, 'Diskon item tidak bisa lebih dari Total Tagihan')
                                    return false
                                }

                                _params = {
                                    diskon: _valueDiskon,
                                    hargaOrigin:_hargaOrigin,
                                    hargaAfterDiskon:_subTotal
                                }
                                setDiskonPasien(_params, _trParent)
                                return false
                            }
                        }

                        if(!_isValid) {
                            _params = {
                                diskon: _valueDiskon,
                                nominalMainPayer: _nominalDijaminMainPayer,
                                nominalSubPayer: _nominalDijaminSubPayer,
                                nominalPasien: _nominalDibayar,
                                hargaOrigin:_hargaOrigin
                            }
                            resetDiskon(_params, _trParent, _message)
                            return false
                        }

                        _subTotal-= _valueDiskon

                        initData(_dataId)

                        if(_nominalDijaminMainPayer == 0 && _nominalDijaminSubPayer == 0 && _nominalDibayar > 0) {
                            _params = {
                                diskon: _valueDiskon,
                                hargaOrigin:_hargaOrigin,
                                hargaAfterDiskon:_subTotal
                            }
                            setDiskonPasien(_params, _trParent)
                        }
                        else {
                            _params = {
                                diskon: _valueDiskon,
                                nominalMainPayer: _nominalDijaminMainPayer,
                                nominalSubPayer: _nominalDijaminSubPayer,
                                nominalPasien: _nominalDibayar,
                                hargaOrigin:_hargaOrigin
                            }
                            setDiskonMainPayer(_params, _trParent)
                        }
                    }
                })

                $('.keterangan').on('change', function (e) {
                    e.preventDefault()
                    isChanges = true
                    var _trParent = $(this).closest('tr');
                    var _dataId = _trParent.data('id');
                    if (typeof tmpTableTransaksi[_dataId] != "undefined") {
                        initData(_dataId)
                        changeData[_dataId].keterangan = $(this).val();
                    }
                })

                $('.checkPenjamin').on('change', function (e) {
                    e.preventDefault()
                    console.log('ss')
                    var id = $(this).attr('data-id')
                    var _parentTr = $(this).closest('tr')
                        _parentTr.css('background-color','')

                    var _classparent = _parentTr.attr('class').split(' ')[0]

                    $(`#edit-dijamin-${id}`).data('val', $(`#edit-dijamin-${id}`).val());

                    var isCek = ''
                    var _checkPenjamin = false
                    var subTotalOrigin = 0
                    initData(id)

                    if ($(`#isPenjamin-${id}`).is(':checked')) {
                        isCek = 'checked'
                        _checkPenjamin = true
                        $(`#edit-dijamin-${id}`).removeAttr("disabled")
                        $(`#edit-dijamin-${id}`).removeAttr("readonly")
                        $(`#edit-dijamin-sub-${id}`).removeAttr("disabled")
                        $(`#edit-dijamin-sub-${id}`).removeAttr("readonly")
                        $(`#nominal-discount-${id}`).prop('disabled', false);
                        $(`#nominal-discount-${id}`).val(0)
                        $(`#harusbayar-${id}`).val(0)
                        $(`#persen-discount-chk-${id}`).prop('checked', false)
                        $(`#percent-discount-${id}`).val(0).trigger('change')
                        if (typeof groupKelTindakan[_classparent][id] != "undefined") {
                            var _baseData = groupKelTindakan[_classparent][id]
                            subTotalOrigin = docoHelper.convertToAngka(_baseData.subtotal_origin)

                            changeData[id].isPenjamin = isCek
                            changeData[id].dijamin = subTotalOrigin
                            changeData[id].dijamin_subpayer = 0
                            changeData[id].totalDibayar = 0
                            changeData[id].nominal_diskon = 0

                            groupKelTindakan[_classparent][id].dijamin = subTotalOrigin
                            groupKelTindakan[_classparent][id].dijamin_subpayer = 0
                            groupKelTindakan[_classparent][id].totalDibayar = 0
                            groupKelTindakan[_classparent][id].nominal_diskon = 0
                            groupKelTindakan[_classparent][id].isPenjamin = isCek
                            groupKelTindakan[_classparent][id].checkPenjamin = _checkPenjamin
                        }
                    } else {
                        isCek = 'uncheck'
                        _checkPenjamin = false
                        $(`#penjamin-${id}`).attr("disabled", true)
                        $(`#edit-dijamin-${id}`).val(0)
                        $(`#edit-dijamin-sub-${id}`).val(0)
                        $(`#edit-dijamin-${id}`).attr("disabled", true)
                        $(`#edit-dijamin-${id}`).attr("readonly", true)
                        $(`#edit-dijamin-sub-${id}`).attr("disabled", true)
                        $(`#edit-dijamin-sub-${id}`).attr("readonly", true)
                        $(`#nominal-discount-${id}`).prop('disabled', true);
                        $(`#nominal-discount-${id}`).val(0)
                        $(`#persen-discount-chk-${id}`).prop('checked', false)
                        $(`#percent-discount-${id}`).val(0)
                        $(`#nominal-discount-${id}`).prop('disabled', false)

                        if (typeof groupKelTindakan[_classparent][id] != "undefined") {
                            var _baseData = groupKelTindakan[_classparent][id]
                            subTotalOrigin = docoHelper.convertToAngka(_baseData.subtotal_origin)

                            changeData[id].isPenjamin = isCek
                            changeData[id].nominal_diskon = 0
                            changeData[id].dijamin = 0
                            changeData[id].dijamin_subpayer = 0
                            changeData[id].totalDibayar = subTotalOrigin

                            groupKelTindakan[_classparent][id].dijamin = 0
                            groupKelTindakan[_classparent][id].dijamin_subpayer = 0
                            groupKelTindakan[_classparent][id].totalDibayar = subTotalOrigin
                            groupKelTindakan[_classparent][id].nominal_diskon = 0
                            groupKelTindakan[_classparent][id].isPenjamin = isCek
                            groupKelTindakan[_classparent][id].checkPenjamin = _checkPenjamin
                        }
                    }

                    if (typeof groupKelTindakan[_classparent][id] != "undefined") {
                        var _baseData = groupKelTindakan[_classparent][id]
                        checkPenjamin = _baseData.checkPenjamin
                        subTotalOrigin = docoHelper.convertToAngka(_baseData.subtotal_origin)
                        if(isCek == 'checked') {
                            $(`#harusbayar-${id}`).val(0)
                            $(`#edit-dijamin-${id}`).val(docoHelper.convertToRupiah(subTotalOrigin))
                        }
                        else {
                            $(`#harusbayar-${id}`).val(docoHelper.convertToRupiah(subTotalOrigin))
                            $(`#edit-dijamin-${id}`).val(0)
                        }
                        $(`#subtotal-${id}`).text(docoHelper.convertToRupiah(subTotalOrigin))
                    }

                    _reCalKateg(_classparent);
                    setTotalSementara()

                    if(docoHelper.convertToAngka($(`#edit-dijamin-parent-${_classparent}`).val()) == 0) {
                        $(`#edit-dijamin-parent-${_classparent}`).prop('disabled', true)
                        $(`#edit-dijamin-parent-sub-${_classparent}`).prop('disabled', true)
                        $(`#checkPenjamin-parent-${_classparent}`).prop('checked', false)
                    }
                    else {
                        $(`#edit-dijamin-parent-${_classparent}`).prop('disabled', false)
                        $(`#edit-dijamin-parent-sub-${_classparent}`).prop('disabled', false)
                        $(`#checkPenjamin-parent-${_classparent}`).prop('checked', true)
                    }

                    if(docoHelper.convertToAngka($('#total-plafon-main-payer').val()) == 0) {
                        docoNotification("warning", "Kesalahan Inputan", "Total tagihan main payer tidak dapat 0 Rupiah");
                        $(`#isPenjamin-${id}`).prop("checked", true).trigger('change')
                    }
                })

                var _reCalKateg = function (group) {
                    if (typeof groupKelTindakan[group] != "undefined") {
                        var _subTotal = _subDiskon = _subJamin = _subBayar = _subJaminSubPayer = 0
                        var _parentTr = $(`tr[data-id="${group}"]`)
                        Object.values(groupKelTindakan[group]).forEach(function (item) {
                            _subTotal += parseFloat(item.subtotal)
                            _subDiskon += Math.round(parseFloat(item.nominal_diskon))
                            _subJamin += parseFloat(item.dijamin)
                            if(typeof item.dijamin_subpayer == 'undefined' || item.dijamin_subpayer == ''){
                                item.dijamin_subpayer = 0
                            }
                            _subJaminSubPayer += parseFloat(item.dijamin_subpayer)
                            _subBayar += parseFloat(item.totalDibayar)
                        })

                        _parentTr.find('#nominal-discount-parent-'+group).val(docoHelper.convertToRupiah(Math.round(parseFloat(_subDiskon))))
                        _parentTr.find('#subtotal-kategory-'+group).text(docoHelper.convertToRupiah(_subTotal))
                        _parentTr.find('#edit-dijamin-parent-'+group).val(docoHelper.convertToRupiah(_subJamin))
                        _parentTr.find('#edit-dijamin-parent-sub-'+group).val(docoHelper.convertToRupiah(_subJaminSubPayer))
                        _parentTr.find('#edit-harusbayar-parent-'+group).val(docoHelper.convertToRupiah(_subBayar))
                    }
                }

                var resetDiskon = function (data, _trParent, _message) {
                    var _dataId = _trParent.data('id');
                    var _classparent = _trParent.attr('class').split(' ')[0]
                    initData(_dataId)

                    if(_message) {
                        docoNotification("warning", "Kesalahan Inputan", _message);
                    }

                    $(`#percent-discount-${_dataId}`).val(0)
                    $(`#nominal-discount-${_dataId}`).val(0)
                    $(`#persen-discount-chk-${_dataId}`).prop('checked', false)
                    $(`#nominal-discount-${_dataId}`).prop('disabled', false);

                    diskon = data.diskon
                    nominalMainPayer = data.nominalMainPayer
                    nominalSubPayer = data.nominalSubPayer
                    nominalPasien = data.nominalPasien
                    hargaOrigin = data.hargaOrigin

                    var newPricePayer = hargaOrigin - nominalSubPayer - nominalPasien - diskon
                    var newPricePasien = hargaOrigin - nominalMainPayer

                    if(newPricePasien != nominalPasien || newPricePasien < 0) {
                        newPricePasien = nominalPasien
                    }

                    if(newPricePayer < 0) {
                        newPricePayer = nominalMainPayer
                    }

                    var newPriceSubPayer = hargaOrigin - newPricePayer - nominalPasien

                    changeData[_dataId].nominal_diskon = 0
                    changeData[_dataId].dijamin = newPricePayer
                    changeData[_dataId].dijamin_subpayer = newPriceSubPayer
                    changeData[_dataId].totalDibayar = newPricePasien
                    changeData[_dataId].subtotal = hargaOrigin
                    changeData[_dataId].persen_diskon = 0

                    if(typeof groupKelTindakan[_classparent][_dataId] != "undefined") {
                        groupKelTindakan[_classparent][_dataId].nominal_diskon = 0
                        groupKelTindakan[_classparent][_dataId].dijamin = newPricePayer
                        groupKelTindakan[_classparent][_dataId].dijamin_subpayer = newPriceSubPayer
                        groupKelTindakan[_classparent][_dataId].totalDibayar = newPricePasien
                        groupKelTindakan[_classparent][_dataId].subtotal = hargaOrigin
                        groupKelTindakan[_classparent][_dataId].persen_diskon = 0
                    }

                    $("#edit-dijamin-" + _dataId).val(docoHelper.convertToRupiah(newPricePayer))
                    $("#edit-dijamin-sub-" + _dataId).val(docoHelper.convertToRupiah(newPriceSubPayer))
                    $("#harusbayar-" + _dataId).val(docoHelper.convertToRupiah(newPricePasien))
                    $(`#subtotal-${_dataId}`).text(docoHelper.convertToRupiah(hargaOrigin))
                    $(`#subtotal-kategory-${_classparent}`).text(hargaOrigin)

                    setTotalSementara();
                    _reCalKateg(_classparent);
                }

                var resetDiskonSubPayer = function (data, _trParent, _message) {
                    var _dataId = _trParent.data('id');
                    var _classparent = _trParent.attr('class').split(' ')[0]
                    initData(_dataId)

                    if(_message) {
                        docoNotification("warning", "Kesalahan Inputan", _message);
                    }

                    $(`#percent-discount-${_dataId}`).val(0)
                    $(`#nominal-discount-${_dataId}`).val(0)
                    $(`#persen-discount-chk-${_dataId}`).prop('checked', false)
                    $(`#nominal-discount-${_dataId}`).prop('disabled', false);

                    diskon = data.diskon
                    nominalMainPayer = data.nominalMainPayer
                    nominalSubPayer = data.nominalSubPayer
                    nominalPasien = data.nominalPasien
                    hargaOrigin = data.hargaOrigin

                    var newPriceSubPayer = nominalSubPayer
                    var newPricePasien = hargaOrigin - nominalMainPayer - newPriceSubPayer

                    changeData[_dataId].nominal_diskon = 0
                    changeData[_dataId].dijamin = nominalMainPayer
                    changeData[_dataId].dijamin_subpayer = newPriceSubPayer
                    changeData[_dataId].totalDibayar = newPricePasien
                    changeData[_dataId].subtotal = hargaOrigin
                    changeData[_dataId].persen_diskon = 0

                    if(typeof groupKelTindakan[_classparent][_dataId] != "undefined") {
                        groupKelTindakan[_classparent][_dataId].nominal_diskon = 0
                        groupKelTindakan[_classparent][_dataId].dijamin = nominalMainPayer
                        groupKelTindakan[_classparent][_dataId].dijamin_subpayer = newPriceSubPayer
                        groupKelTindakan[_classparent][_dataId].totalDibayar = newPricePasien
                        groupKelTindakan[_classparent][_dataId].subtotal = hargaOrigin
                        groupKelTindakan[_classparent][_dataId].persen_diskon = 0
                    }

                    // $("#edit-dijamin-" + _dataId).val(docoHelper.convertToRupiah(newPricePayer))
                    $("#edit-dijamin-sub-" + _dataId).val(docoHelper.convertToRupiah(newPriceSubPayer))
                    $("#harusbayar-" + _dataId).val(docoHelper.convertToRupiah(newPricePasien))
                    $(`#subtotal-${_dataId}`).text(docoHelper.convertToRupiah(hargaOrigin))
                    $(`#subtotal-kategory-${_classparent}`).text(hargaOrigin)

                    setTotalSementara();
                    _reCalKateg(_classparent);
                    return false
                }

                var resetDiskonMainPayer = function (data, _trParent, _message) {
                    var _dataId = _trParent.data('id');
                    var _classparent = _trParent.attr('class').split(' ')[0]
                    initData(_dataId)

                    if(_message) {
                        docoNotification("warning", "Kesalahan Inputan", _message);
                    }

                    $(`#percent-discount-${_dataId}`).val(0)
                    $(`#nominal-discount-${_dataId}`).val(0)
                    $(`#persen-discount-chk-${_dataId}`).prop('checked', false)
                    $(`#nominal-discount-${_dataId}`).prop('disabled', false);

                    diskon = data.diskon
                    nominalMainPayer = data.nominalMainPayer
                    nominalSubPayer = data.nominalSubPayer
                    nominalPasien = data.nominalPasien
                    hargaOrigin = data.hargaOrigin

                    var newPricePayer = nominalMainPayer
                    var newPriceSubPayer = 0
                    var newPricePasien = hargaOrigin - nominalMainPayer

                    changeData[_dataId].nominal_diskon = 0
                    changeData[_dataId].dijamin = newPricePayer
                    changeData[_dataId].dijamin_subpayer = newPriceSubPayer
                    changeData[_dataId].totalDibayar = newPricePasien
                    changeData[_dataId].subtotal = hargaOrigin
                    changeData[_dataId].persen_diskon = 0

                    if(typeof groupKelTindakan[_classparent][_dataId] != "undefined") {
                        groupKelTindakan[_classparent][_dataId].nominal_diskon = 0
                        groupKelTindakan[_classparent][_dataId].dijamin = newPricePayer
                        groupKelTindakan[_classparent][_dataId].dijamin_subpayer = newPriceSubPayer
                        groupKelTindakan[_classparent][_dataId].totalDibayar = newPricePasien
                        groupKelTindakan[_classparent][_dataId].subtotal = hargaOrigin
                        groupKelTindakan[_classparent][_dataId].persen_diskon = 0
                    }

                    $("#edit-dijamin-" + _dataId).val(docoHelper.convertToRupiah(newPricePayer))
                    $("#edit-dijamin-sub-" + _dataId).val(docoHelper.convertToRupiah(newPriceSubPayer))
                    $("#harusbayar-" + _dataId).val(docoHelper.convertToRupiah(newPricePasien))
                    $(`#subtotal-${_dataId}`).text(docoHelper.convertToRupiah(hargaOrigin))
                    $(`#subtotal-kategory-${_classparent}`).text(hargaOrigin)

                    setTotalSementara();
                    _reCalKateg(_classparent);
                }

                var setDiskonPasien = function(data, _trParent) {
                    var _dataId = _trParent.data('id');
                    var _classparent = _trParent.attr('class').split(' ')[0]

                    initData(_dataId)

                    diskon = data.diskon
                    hargaOrigin = data.hargaOrigin

                    var _newPrice = hargaOrigin - diskon

                    $(`#harusbayar-${_dataId}`).val(docoHelper.convertToRupiah(_newPrice))
                    $(`#subtotal-${_dataId}`).text(docoHelper.convertToRupiah(_newPrice))
                    $(`#subtotal-kategory-${_classparent}`).text(_newPrice)

                    changeData[_dataId].nominal_diskon = diskon
                    changeData[_dataId].totalDibayar = _newPrice
                    changeData[_dataId].dijamin = 0
                    changeData[_dataId].dijamin_subpayer = 0
                    changeData[_dataId].subtotal = _newPrice

                    groupKelTindakan[_classparent][_dataId].nominal_diskon = diskon
                    groupKelTindakan[_classparent][_dataId].totalDibayar = _newPrice
                    groupKelTindakan[_classparent][_dataId].dijamin = 0
                    groupKelTindakan[_classparent][_dataId].dijamin_subpayer = 0
                    groupKelTindakan[_classparent][_dataId].subtotal = _newPrice

                    setTotalSementara();
                    _reCalKateg(_classparent);
                }

                var resetDiskonPasien = function(data, _trParent, _message) {
                    var _dataId = _trParent.data('id');
                    var _classparent = _trParent.attr('class').split(' ')[0]
                    initData(_dataId)

                    if(_message) {
                        docoNotification("warning", "Kesalahan Inputan", _message);
                    }

                    $(`#percent-discount-${_dataId}`).val(0)
                    $(`#nominal-discount-${_dataId}`).val(0)
                    $(`#persen-discount-chk-${_dataId}`).prop('checked', false)
                    $(`#nominal-discount-${_dataId}`).prop('disabled', false);

                    diskon = data.diskon
                    nominalPasien = data.nominalPasien
                    hargaOrigin = data.hargaOrigin

                    var newPricePasien = hargaOrigin

                    changeData[_dataId].nominal_diskon = 0
                    changeData[_dataId].dijamin = 0
                    changeData[_dataId].dijamin_subpayer = 0
                    changeData[_dataId].totalDibayar = newPricePasien
                    changeData[_dataId].subtotal = hargaOrigin
                    changeData[_dataId].persen_diskon = 0

                    if(typeof groupKelTindakan[_classparent][_dataId] != "undefined") {
                        groupKelTindakan[_classparent][_dataId].nominal_diskon = 0
                        groupKelTindakan[_classparent][_dataId].dijamin = 0
                        groupKelTindakan[_classparent][_dataId].dijamin_subpayer = 0
                        groupKelTindakan[_classparent][_dataId].totalDibayar = newPricePasien
                        groupKelTindakan[_classparent][_dataId].subtotal = hargaOrigin
                        groupKelTindakan[_classparent][_dataId].persen_diskon = 0
                    }

                    $("#edit-dijamin-" + _dataId).val(0)
                    $("#edit-dijamin-sub-" + _dataId).val(0)
                    $("#harusbayar-" + _dataId).val(docoHelper.convertToRupiah(newPricePasien))
                    $(`#subtotal-${_dataId}`).text(docoHelper.convertToRupiah(hargaOrigin))
                    $(`#subtotal-kategory-${_classparent}`).text(hargaOrigin)

                    setTotalSementara();
                    _reCalKateg(_classparent);
                }

                var setDiskonMainPayer = function(data, _trParent) {
                    var _dataId = _trParent.data('id');
                    var _classparent = _trParent.attr('class').split(' ')[0]

                    if(typeof groupKelTindakan[_classparent][_dataId] != 'undefined') {
                        checkPenjamin = groupKelTindakan[_classparent][_dataId].checkPenjamin
                        nominalMainPayer = data.nominalMainPayer
                        nominalSubPayer = data.nominalSubPayer
                        nominalPasien = data.nominalPasien
                        hargaOrigin = data.hargaOrigin
                        diskon = data.diskon

                        if(!checkPenjamin) {
                            nominalMainPayer = 0
                            nominalPasien = hargaOrigin
                            changeData[_dataId].totalDibayar = hargaOrigin
                            groupKelTindakan[_classparent][_dataId].totalDibayar = hargaOrigin
                            $(`#harusbayar-${_dataId}`).val(docoHelper.convertToRupiah(hargaOrigin))
                        }

                        var newPriceMainPayer = hargaOrigin - nominalSubPayer - nominalPasien/*  - diskon */
                        var subTotal = newPriceMainPayer + nominalSubPayer + nominalPasien - diskon

                        $(`#edit-dijamin-${_dataId}`).val(docoHelper.convertToRupiah(newPriceMainPayer))
                        $(`#subtotal-${_dataId}`).text(docoHelper.convertToRupiah(subTotal))
                        $(`#subtotal-kategory-${_classparent}`).text(subTotal)

                        changeData[_dataId].nominal_diskon = diskon
                        changeData[_dataId].dijamin = newPriceMainPayer
                        changeData[_dataId].subtotal = subTotal

                        groupKelTindakan[_classparent][_dataId].nominal_diskon = diskon
                        groupKelTindakan[_classparent][_dataId].dijamin = newPriceMainPayer
                        groupKelTindakan[_classparent][_dataId].subtotal = subTotal

                        setTotalSementara();
                        _reCalKateg(_classparent);
                    }
                }

                var setNominalSubPayer = function(data, _trParent) {
                    var _dataId = _trParent.data('id');
                    var _classparent = _trParent.attr('class').split(' ')[0]

                    nominalDijamin = data.nominalDijamin
                    nominalCurrent = data.nominalCurrent
                    nominalDibayar = data.nominalDibayar
                    hargaOrigin = data.hargaOrigin

                    var newPricePasien = hargaOrigin - nominalDijamin - nominalCurrent
                    var newPriceMainPayer = nominalDijamin
                    var subTotal = newPriceMainPayer + nominalCurrent + newPricePasien

                    $(`#edit-dijamin-${_dataId}`).val(docoHelper.convertToRupiah(newPriceMainPayer))
                    $(`#edit-dijamin-sub-${_dataId}`).val(docoHelper.convertToRupiah(nominalCurrent))
                    $(`#harusbayar-${_dataId}`).val(docoHelper.convertToRupiah(newPricePasien))
                    $(`#subtotal-${_dataId}`).text(docoHelper.convertToRupiah(subTotal))
                    $(`#subtotal-kategory-${_classparent}`).text(subTotal)

                    changeData[_dataId].dijamin = newPriceMainPayer
                    changeData[_dataId].totalDibayar = newPricePasien
                    changeData[_dataId].dijamin_subpayer = nominalCurrent
                    changeData[_dataId].subtotal = subTotal

                    if(typeof groupKelTindakan[_classparent][_dataId] != "undefined") {
                        groupKelTindakan[_classparent][_dataId].dijamin = newPriceMainPayer
                        groupKelTindakan[_classparent][_dataId].dijamin_subpayer = nominalCurrent
                        groupKelTindakan[_classparent][_dataId].totalDibayar = newPricePasien
                        groupKelTindakan[_classparent][_dataId].subtotal = subTotal
                    }

                    setTotalSementara();
                    _reCalKateg(_classparent);
                }

                var setNominal = function(data, _trParent) {
                    var _dataId = _trParent.data('id');
                    var _classparent = _trParent.attr('class').split(' ')[0]

                    nominalDijamin = data.nominalDijamin
                    nominalDijaminSub = data.nominalDijaminSub
                    nominalDibayar = data.nominalDibayar
                    hargaOrigin = data.hargaOrigin

                    var newPriceMainPayer = nominalDijamin
                    var newPricePasien = hargaOrigin - nominalDijamin - nominalDijaminSub
                    var newPriceSubPayer = hargaOrigin - nominalDijamin - newPricePasien
                    var subTotal = newPriceMainPayer + newPriceSubPayer + newPricePasien

                    $(`#edit-dijamin-${_dataId}`).val(docoHelper.convertToRupiah(newPriceMainPayer))
                    $(`#edit-dijamin-sub-${_dataId}`).val(docoHelper.convertToRupiah(newPriceSubPayer))
                    $(`#harusbayar-${_dataId}`).val(docoHelper.convertToRupiah(newPricePasien))
                    $(`#subtotal-${_dataId}`).text(docoHelper.convertToRupiah(subTotal))
                    $(`#subtotal-kategory-${_classparent}`).text(subTotal)

                    changeData[_dataId].dijamin = newPriceMainPayer
                    changeData[_dataId].totalDibayar = newPricePasien
                    changeData[_dataId].dijamin_subpayer = newPriceSubPayer
                    changeData[_dataId].subtotal = subTotal

                    if(typeof groupKelTindakan[_classparent][_dataId] != "undefined") {
                        groupKelTindakan[_classparent][_dataId].dijamin = newPriceMainPayer
                        groupKelTindakan[_classparent][_dataId].dijamin_subpayer = newPriceSubPayer
                        groupKelTindakan[_classparent][_dataId].totalDibayar = newPricePasien
                        groupKelTindakan[_classparent][_dataId].subtotal = subTotal
                    }

                    setTotalSementara();
                    _reCalKateg(_classparent);
                    return false
                }

            //  Gunakan ini untuk otomatisasi validasi #otomatisasiValidasi
            //  if(reloadTr.length > 0){
            //     reloadTr.forEach(function(val) {
            //         $('tr[data-id='+ val +']').find('.edit-dijamin').trigger('change')
            //     });
            //     reloadTr = []
            // }
            }, 500);
        }
    }
}

generateTableEditTagihan = function () {
    var _html = ""
    var no = 0
    Object.keys(groupKelTindakan).forEach(function (header) {
        var _trimKel = header.replace(/[^\w]/g, '')
        var _string = `<tr data-id='${_trimKel}'>`
        _string += `<td>${no + 1}</td>`;
        var val = {}
        var _totalSubKateg = subTotJamin = subTotBayar = totalDiskon = subTotJaminSubPayer = 0;
        var _percenDis = _isChecked = isDisabled = isReadonly = setDataSubPenjamin = '';
        var _subTotalKategDiskon = (typeof dataGroupTindakan[header].total_diskon  !==  'undefined' || dataGroupTindakan[header].total_diskon != null) ? dataGroupTindakan[header].total_diskon : 0
        var _subTotalKateg = docoHelper.convertToRupiah(dataGroupTindakan[header].total_subtotal + _subTotalKategDiskon)
        var _subTotalPayer = dataGroupTindakan[header].total_dijamin
        var _subTotalPayerSecondary = dataGroupTindakan[header].total_dijaminSubPayer

        Object.keys(groupKelTindakan[header]).forEach(function (key) {
            val = groupKelTindakan[header][key];
            isDisabled = 'disabled'
            isDisabledListPenjamin = 'disabled'
            isDisabledSelect = 'disabled';
            isDisabledSelectList = 'disabled';
            isReadonly = 'readonly'
            setDataPenjamin = setPenjamin(_listPenjamin, val.defaultPenjamin.id)
            if (val.isPenjamin) {
                isDisabled = 'enabled'
                isDisabledListPenjamin = 'enabled'
                isReadonly = ''
                isDisabledSelect = ''
            }

            if(val.checkPenjamin == false){
                isReadonly = 'readonly'
            }

            _isChecked = val.checkPenjamin ? 'checked' : '';
            _percenDis = val.persen_diskon ? 'checked' : '';
            _isChecked = (_subTotalPayer == 0) ? '' : 'checked'

            //Penyesuaian untuk diskon Admin dikarenakan subtotal selalu diambil dari function Biaya Admin
            if(header == ''){
                val.subtotal = val.subtotal_origin - val.nominal_diskon
            }
            if (_subTotalPayer <= 0 && (_listPenjamin.length < 2) && !isPlafon) {
                _isChecked = ''
                isDisabledSelect = 'disabled';
                isDisabledSelectList = 'disabled';
                isPerseorangan = true
                $('.kolom-subpayer').hide()
            }

            if(isMultiPayer){
                isDisabledListPenjamin = 'disabled'
                setDataPenjamin = '<option selected>'+ $('.excess-label-main').html() +'</option>'
                setDataSubPenjamin = encodeURI($('.excess-label-sub').html())
                isDisabledSelectList = 'disabled'
            }

        //  if(isPlafon){
        //     _isChecked = 'checked'
        //     val.totalDibayar = 0
        //  }


            initData(key);

            changeData[key].dijamin = val.dijamin
            changeData[key].dijamin_subpayer = val.dijamin_subpayer
            changeData[key].totalDibayar = val.totalDibayar
            changeData[key].subtotal = val.subtotal
            changeData[key].nominal_diskon = val.nominal_diskon

            subTotJamin += val.dijamin
            subTotJaminSubPayer += parseFloat(val.dijamin_subpayer)
            subTotBayar += parseFloat(val.totalDibayar)
            _totalSubKateg += val.subtotal
            totalDiskon += val.nominal_diskon
        })
        if (header) {
            var _nameOrigin = dataGroupTindakan[header].origin_name
            _string += `
                <td colspan="6" class="text-center">
                    <a class='table-tagihan-header' onclick="_generateDetail(this,'${header}')" style="font-size:17px;"><b>${_nameOrigin}</b> <span class="kelompok_harga"><b>(Rp. ${_subTotalKateg})</span><span class="plus">  [ + ]</span></a>
                </td>
                <td style="display: none;"></td>
                <td style="display: none;"></td>
                <td class="text-right" style="display: none;"></td>
                <td class="text-right" style="display: none;"></td>
                <td class="text-right" style="display: none;"></td>
            `;
        } else {
            _string += `
                <td>
                    ${val.tanggal}
                </td>
                <td>
                    ${val.instalasi}
                </td>
                <td>
                    ${val.tindakan}
                </td>
                <td class="text-right">
                    ${val.qty}
                </td>
                <td class="text-right">
                    ${docoHelper.convertToRupiah(val.harga)}
                </td>
                <td class="text-right">
                    ${docoHelper.convertToRupiah(val.cyto)}
                </td>
                `;
        }

        _string += `
            <td class="bg-yellow">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group highlight-addon has-size-sm field-persen">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <label>
                                        <input type="checkbox"
                                                id="persen-discount-chk-parent-${_trimKel}"
                                                class="persen-discount-chk-parent" value="1" ${_percenDis}> &nbsp;
                                        <i class="fa fa-percent" aria-hidden="true"></i>
                                    </label>
                                </span>
                                <input type="text"
                                        class="form-control input-sm text-right doco-number percent-discount-parent"
                                        autocomplete="off"
                                        value="${val.persen_diskon}"
                                        ${val.persen_diskon ? 'disabled' : ''}>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group highlight-addon has-size-sm field-tot-diskon">
                            <input type="text"
                                    id="nominal-discount-parent-${_trimKel}"
                                    class="form-control input-sm text-right doco-number nominal-discount-parent"
                                    value="${docoHelper.convertToRupiah(Math.round(parseFloat(totalDiskon)))}"
                                    autocomplete="off"
                                    ${val.persen_diskon ? 'disabled' : ''}>
                        </div>
                    </div>
                </div>
            </td>
            <td class="text-right">
                <p class="subtotal-kategory" id="subtotal-kategory-${_trimKel}">${docoHelper.convertToRupiah(_totalSubKateg)}</p>
            </td>
            <td>
                <div class="input-group">
                    <label class="input-group-addon">
                        <input type="checkbox"
                                class="checkPenjamin-parent"
                                id="checkPenjamin-parent-${_trimKel}"
                                data-parent="${_trimKel}"
                                ${isDisabled} ${_isChecked}>
                    </label>
                    <select name='penjamin'
                            class="select2 form-control selectpenjamin-parent" ${isDisabledListPenjamin} ${isDisabledSelectList}>${setDataPenjamin}</select>
                </div>
            </td>
            <td>
                <input type='text'
                        id='edit-dijamin-parent-${_trimKel}'
                        class='form-control doco-number edit-dijamin-parent text-right'
                        data-id='${no}' ${isReadonly}
                        value='${docoHelper.convertToRupiah(subTotJamin)}'>
            </td>
            <td class="header-nama-subpayer">
                <input type='text' name='penjamin-sub'
                    id="selectpenjamin-parent-sub-${_trimKel}"
                    class="form-control selectpenjamin-parent-sub" disabled readonly value=${setDataSubPenjamin}>
        </td>
            <td class='header-subpayer'>
                <input type='text'
                        id='edit-dijamin-parent-sub-${_trimKel}'
                        class='form-control doco-number edit-dijamin-parent-sub text-right'
                        data-id='${no}' ${isReadonly}
                        value='${docoHelper.convertToRupiah(subTotJaminSubPayer)}'>
            </td>
            <td>
                <input type='text'
                        id="edit-harusbayar-parent-${_trimKel}"
                        class='form-control doco-number edit-harusbayar-parent text-right'
                        readonly='true'
                        value='${docoHelper.convertToRupiah(subTotBayar)}'
            </td>
            <td>
                <input type="text"
                        class="form-control input-sm keterangan-parent"
                        autocomplete="off"
                        value='${val.keterangan}'>
            </td>`
        _string += `</tr>`
        _html += _string

    });

    $('#table-edit-tagihan > tbody').append(_html)

    if(_listPenjamin.length < 2){
        $('#table-edit-tagihan .kolom-subpayer').remove();
        $('#table-edit-tagihan .kolom-nama-subpayer').remove();
        $('#table-edit-tagihan .header-subpayer').remove();
        $('#table-edit-tagihan .header-nama-subpayer').remove();
    }
    else {
        $('#table-edit-tagihan').each((index,element) => {
            $(element).find('.selectpenjamin-parent-sub').each(function(){
                _decodedData = decodeURI($(this).val())
                $(this).val(_decodedData)
            })
        })
    }

    $('.select2').select2()

    $('.persen-discount-chk-parent').on('change', function (e) {
        e.preventDefault();
        // if( docoHelper.convertToAngka($('#selisih-penjamin').val()) > 0 ){
        //     resetPlafonPenjamin()
        // }
        var _parent = $(this)
        var _trParent = _parent.closest('tr');
        var _percenDis = _trParent.find('.percent-discount-parent');
        var _nominDis = _trParent.find('.nominal-discount-parent');
        _percenDis.prop("disabled", true);
        _nominDis.prop("disabled", true);
        if (_parent.is(':checked')) {
            _percenDis.prop("disabled", false);
        } else {
            _nominDis.prop("disabled", false);
        }
        _trParent.find('.percent-discount-parent').val(0).trigger('change')
    })

    $('.percent-discount-parent').on('change', function (e) {
        e.preventDefault()
        var _trParent = $(this).closest('tr');
        var _dataId = _trParent.data('id');
        isChanges = true
        if (typeof groupKelTindakan[_dataId] != "undefined") {
            var _percentDist = docoHelper.convertToAngka($(this).val());
            var _totalSubKategory = totalParentJamin = totalParentBayar = 0;
            var _totalDiskonKateg = 0
            if (_percentDist > 100) {
                docoNotification("error", "Data Tidak Valid!", "Persen tidak boleh lebih dari 100");
                $(this).val(0).trigger('change')
                return false;
            }

            Object.keys(groupKelTindakan[_dataId]).forEach(function (item) {
                var _resourceDat = groupKelTindakan[_dataId][item];
                var _dijaminSubPayer = _resourceDat.dijamin_subpayer;
                var _subTotal = _resourceDat.subtotal_origin;
                let diskonItem =  Math.round(parseFloat(_subTotal * (_percentDist / 100)));
                var _valueDiskon = diskonItem;
                _totalDiskonKateg += _valueDiskon
                _subTotal -= _valueDiskon;
                _totalSubKategory += _subTotal
                initData(item)
                changeData[item].dijamin = 0;
                changeData[item].totalDibayar = 0;
                changeData[item].subtotal = _subTotal;
                changeData[item].nominal_diskon = _valueDiskon;
                changeData[item].persen_diskon = parseFloat(_percentDist);
                /** Setting Group */
                groupKelTindakan[_dataId][item].nominal_diskon = _valueDiskon;
                groupKelTindakan[_dataId][item].persen_diskon = parseFloat(_percentDist);
                groupKelTindakan[_dataId][item].subtotal = _subTotal;
                groupKelTindakan[_dataId][item].dijamin = 0;
                groupKelTindakan[_dataId][item].totalDibayar = 0;
                groupKelTindakan[_dataId][item].dijamin_subpayer = 0;

                if (_resourceDat.checkPenjamin) {
                    totalParentJamin += _subTotal;
                    changeData[item].dijamin = _subTotal
                    groupKelTindakan[_dataId][item].dijamin = _subTotal;
                } else {
                    totalParentBayar += parseFloat(_subTotal)
                    changeData[item].totalDibayar = _subTotal
                    groupKelTindakan[_dataId][item].totalDibayar = _subTotal;
                }

            });

            _trParent.find('.nominal-discount-parent').val(docoHelper.convertToRupiah(Math.round(parseFloat(_totalDiskonKateg))));
            _trParent.find('.edit-harusbayar-parent').val(docoHelper.convertToRupiah(totalParentBayar));
            _trParent.find('.edit-dijamin-parent').val(docoHelper.convertToRupiah(totalParentJamin));

            //Reset Sub Payer saat discount muncul
            _trParent.find('.edit-dijamin-parent-sub').val(docoHelper.convertToRupiah(0));
            _trParent.find('.subtotal-kategory').text(docoHelper.convertToRupiah(_totalSubKategory));

            if (_trParent.find('span').hasClass('minus')) {
                // destroyDataTabels()
                $(`.${_dataId}`).remove()
                _generateDetail($(this), _dataId)
            }
            setTotalSementara();
        }
    });

    $('.nominal-discount-parent').on('change', function (e) {
        e.preventDefault()
        // if( docoHelper.convertToAngka($('#selisih-penjamin').val()) > 0 && docoHelper.convertToAngka($(this).val()) > 0){
        //     resetPlafonPenjamin()
        // }
        var _trParent = $(this).closest('tr');
        _trParent.css('background-color','')
        var _dataId = _trParent.data('id');
        isChanges = true
        if (typeof groupKelTindakan[_dataId] != "undefined" && typeof dataGroupTindakan[_dataId] != "undefined") {
            var _valtDist = docoHelper.convertToAngka($(this).val());
            var _totalSubKategory = totalParentJamin = totalParentBayar = 0;
            var _totalDiskonKateg = 0
            var _subTotalKateg = 0
            Object.values(groupKelTindakan[_dataId]).forEach(function(valTmp){
                _subTotalKateg += parseFloat(valTmp.subtotal_origin)
                // _subTotalKateg += parseFloat(valTmp.harga) * parseFloat(valTmp.qty)
            })

            if (_valtDist > _subTotalKateg) {
                docoNotification("error", "Data Tidak Valid!", "Diskon item tidak boleh lebih dari Total Tagihan");
                $(this).val(0)
                return false;
            }

            var _percentDist = _subTotalKateg == 0 ? 0 : (_valtDist / _subTotalKateg) * 100;
            Object.keys(groupKelTindakan[_dataId]).forEach(function (item) {
                var _resourceDat = groupKelTindakan[_dataId][item];
                var _subTotal = _resourceDat.subtotal_origin;
                var _valueDiskon = _subTotal * (_percentDist / 100)
                _valueDiskon = parseFloat(_valueDiskon)

                _totalDiskonKateg += _valueDiskon
                _subTotal -= _valueDiskon;
                _totalSubKategory += _subTotal

                initData(item)
                changeData[item].dijamin = 0;
                changeData[item].dijamin_subpayer = 0;
                changeData[item].totalDibayar = 0;
                changeData[item].subtotal = _subTotal;

                changeData[item].nominal_diskon = _valueDiskon;

                /** Setting Group */
                groupKelTindakan[_dataId][item].nominal_diskon = _valueDiskon;
                groupKelTindakan[_dataId][item].persen_diskon = 0;
                groupKelTindakan[_dataId][item].subtotal = _subTotal;
                groupKelTindakan[_dataId][item].dijamin = 0;
                groupKelTindakan[_dataId][item].dijamin_subpayer = 0;
                groupKelTindakan[_dataId][item].totalDibayar = 0;

                if (_resourceDat.checkPenjamin) {
                    totalParentJamin += _subTotal;
                    changeData[item].dijamin = _subTotal
                    groupKelTindakan[_dataId][item].dijamin = _subTotal;
                } else {
                    totalParentBayar += parseFloat(_subTotal)
                    changeData[item].totalDibayar = _subTotal
                    groupKelTindakan[_dataId][item].totalDibayar = _subTotal;
                }
            });
            _trParent.find('.nominal-discount-parent').val(docoHelper.convertToRupiah(Math.round(parseFloat(_totalDiskonKateg))));
            _trParent.find('.edit-harusbayar-parent').val(docoHelper.convertToRupiah(totalParentBayar));
            _trParent.find('.edit-dijamin-parent').val(docoHelper.convertToRupiah(totalParentJamin));
            //Reset Sub Payer saat discount muncul
            _trParent.find('.edit-dijamin-parent-sub').val(docoHelper.convertToRupiah(0));
            _trParent.find('.subtotal-kategory').text(docoHelper.convertToRupiah(_totalSubKategory));

            if (_trParent.find('span').hasClass('minus')) {
                // destroyDataTabels()
                $(`.${_dataId}`).remove()
                _generateDetail($(this), _dataId)
            }
            setTotalSementara();
        }
    })

    $('.checkPenjamin-parent').on('change', function (e) {
        e.preventDefault()
        var id = $(this).attr('data-id')
        var _parent = $(this).closest('tr');
            _parent.css('background-color','')
        var _dataId = _parent.data('id');

        isChanges = true
        _parent.find('.nominal-discount-parent').val(0).trigger('change')
        _parent.find('.percent-discount-parent').val(0).trigger('change')
        var isCek = ''
        var subtotal = _parent.find('p.subtotal-kategory').text()
            subtotal = docoHelper.convertToAngka(subtotal)

        //Reset Plafon Penjamin
        // if( docoHelper.convertToAngka($('#selisih-penjamin').val()) > 0 ){
        //     resetPlafonPenjamin()
        // }

        var _isChecked = $(this).is(':checked')

        if (_isChecked) {
            isCek = 'checked'
           $('#edit-dijamin-parent-'+_dataId).removeAttr("disabled")
           $('#edit-dijamin-parent-'+_dataId).removeAttr("readonly")
           $('#edit-dijamin-parent-'+_dataId).val(docoHelper.convertToRupiah(subtotal))

           $('#edit-dijamin-parent-sub-'+_dataId).removeAttr("disabled")
           $('#edit-dijamin-parent-sub-'+_dataId).removeAttr("readonly")
           $('#edit-dijamin-parent-sub-'+_dataId).val(0)

           $('#edit-harusbayar-parent-'+_dataId).attr("disabled", true)
           $('#edit-harusbayar-parent-'+_dataId).attr("readonly", true)
           $('#edit-harusbayar-parent-'+_dataId).val(0)

        } else {
            isCek = 'uncheck'
            _parent.find('select.selectpenjamin-parent').attr("disabled", true)

           $('#edit-dijamin-parent-'+_dataId).attr("disabled", true)
           $('#edit-dijamin-parent-'+_dataId).attr("readonly", true)
           $('#edit-dijamin-parent-'+_dataId).val(0)

           $('#edit-dijamin-parent-sub-'+_dataId).attr("disabled", true)
           $('#edit-dijamin-parent-sub-'+_dataId).attr("readonly", true)
           $('#edit-dijamin-parent-sub-'+_dataId).val(0).trigger('change')

           $('#edit-harusbayar-parent-'+_dataId).removeAttr("disabled")
           $('#edit-harusbayar-parent-'+_dataId).removeAttr("readonly")
           $('#edit-harusbayar-parent-'+_dataId).val(docoHelper.convertToRupiah(subtotal))

            if (_dataId) {
                $('.' + _dataId).find('.checkPenjamin').prop('checked', false).prop('disabled', true);
                $('.' + _dataId).find('.selectpenjamin ').prop('disabled', true);
            }
        }

        if (typeof groupKelTindakan[_dataId] != "undefined") {
            Object.keys(groupKelTindakan[_dataId]).forEach(function (item) {
                initData(item)
                var _baseData = groupKelTindakan[_dataId][item]
                changeData[item].isPenjamin = isCek
                groupKelTindakan[_dataId][item].checkPenjamin = _isChecked
                if(isCek == 'checked') {
                    changeData[item].dijamin = docoHelper.convertToAngka(_baseData.totalDibayar)
                    changeData[item].totalDibayar = 0
                    groupKelTindakan[_dataId][item].dijamin = docoHelper.convertToAngka(_baseData.totalDibayar)
                    groupKelTindakan[_dataId][item].totalDibayar = 0
                }
                else {
                    changeData[item].dijamin = 0
                    changeData[item].totalDibayar = docoHelper.convertToAngka(_baseData.subtotal_origin)
                    groupKelTindakan[_dataId][item].dijamin = 0
                    groupKelTindakan[_dataId][item].totalDibayar = docoHelper.convertToAngka(_baseData.subtotal_origin)
                }
            })
        }

        if(!_isChecked) {
            $('.persen-discount-chk-parent').prop('checked', false)
        }

        if (_parent.find('span').hasClass('minus')) {
            // destroyDataTabels()
            $(`.${_dataId}`).remove()
            _generateDetail($(this), _dataId)
        }

        setTotalSementara();
        if(docoHelper.convertToAngka($('#total-plafon-main-payer').val()) == 0) {
            docoNotification("warning", "Kesalahan Inputan", "Total tagihan main payer tidak dapat 0 Rupiah");
            $(this).prop("checked", true).trigger('change')
        }
    })

    $('.edit-dijamin-parent').on('change', function (e) {
        e.preventDefault()
        isChanges = true
        var id = $(this).attr('data-id')
        var _parent = $(this).closest('tr');
        _parent.css('background-color','')
        var _dataId = _parent.data('id');
        var subtotal = _parent.find('.subtotal-kategory').text()
        var data_dijamin_sub = _parent.find('.edit-dijamin-parent-sub').val()
        var data_dibayar = _parent.find('.edit-harusbayar-parent').val()
        var data_dijamin = $(this).val()

        if(typeof data_dijamin_sub == "undefined"){
            data_dijamin_sub = 0
        }
        if (parseFloat(docoHelper.convertToAngka(subtotal)) < ( parseFloat(docoHelper.convertToAngka(data_dijamin))+ parseFloat(docoHelper.convertToAngka(data_dijamin_sub)))) {
            var _subTotal = parseFloat(docoHelper.convertToAngka(subtotal))
            var _dijaminSub = parseFloat(docoHelper.convertToAngka(data_dijamin_sub))
            var _dibayarPasien = parseFloat(docoHelper.convertToAngka(data_dibayar))
            var _current = _subTotal - _dijaminSub - _dibayarPasien
            docoNotification("warning", "Kesalahan Inputan", "Nilai Jaminan tidak boleh lebih besar daripada Subtotal")
            $(this).val(docoHelper.convertToRupiah(_current))
            return false
        }
        if (typeof dataGroupTindakan[_dataId] != "undefined" && typeof groupKelTindakan[_dataId] != "undefined") {
            var _subTotalKateg = docoHelper.convertToAngka(_parent.find('p.subtotal-kategory').text())
            var _totalKategJamin = parseFloat(docoHelper.convertToAngka($(this).val()))
            var _totalKategJaminSub = parseFloat(docoHelper.convertToAngka(data_dijamin_sub))
            var _dibayar = 0

            if (_subTotalKateg > (_totalKategJamin + _totalKategJaminSub) ) {
                _dibayar = _subTotalKateg - (_totalKategJamin + _totalKategJaminSub)
            }

            var _percentJaminan = _subTotalKateg == 0 ? 0 : _totalKategJamin / _subTotalKateg;
            var _percentJaminanSub = _subTotalKateg == 0 ? 0 : _totalKategJaminSub / _subTotalKateg;
            var _percentBayar = _subTotalKateg == 0 ? 0 : _dibayar / _subTotalKateg;

            _percentBayar = parseFloat(_percentBayar)
            _percentJaminan = parseFloat(_percentJaminan)
            Object.keys(groupKelTindakan[_dataId]).forEach(function (item) {
                initData(item)

                var _base = groupKelTindakan[_dataId][item]
                var _subTotalItem = groupKelTindakan[_dataId][item].subtotal

                var _dijamin = _subTotalItem * _percentJaminan;
                var _dibayar = _subTotalItem * _percentBayar;
                var _dijaminSub = _subTotalItem * _percentJaminanSub;

                changeData[item].totalDibayar = _dibayar
                changeData[item].dijamin = _dijamin
                changeData[item].dijamin_subpayer = _dijaminSub
                changeData[item].nominal_diskon = _base.nominal_diskon;
                changeData[item].subtotal = _subTotalItem

                groupKelTindakan[_dataId][item].totalDibayar = _dibayar
                groupKelTindakan[_dataId][item].dijamin = _dijamin
                groupKelTindakan[_dataId][item].dijamin_subpayer = _dijaminSub
                groupKelTindakan[_dataId][item].nominal_diskon = _base.nominal_diskon;
                groupKelTindakan[_dataId][item].subtotal = _subTotalItem
            })

            _parent.find('.edit-harusbayar-parent').val(docoHelper.convertToRupiah(_dibayar))
            if (_parent.find('span').hasClass('minus')) {
                // destroyDataTabels()
                $(`.${_dataId}`).remove()
                _generateDetail($(this), _dataId)
            }
            setTotalSementara();
        }
    })

    $('.edit-dijamin-parent-sub').on('change', function (e) {
        e.preventDefault()
        isChanges = true
        var id = $(this).attr('data-id')
        var _parent = $(this).closest('tr');
        var _dataId = _parent.data('id');
        var discount_parent = _parent.find('.nominal-discount-parent').val()
        var subtotal = _parent.find('.subtotal-kategory').text()
        var data_dijamin = _parent.find('.edit-dijamin-parent').val()
        var data_dibayar = _parent.find('.edit-harusbayar-parent').val()
        var data_dijamin_sub = $(this).val()

        if(typeof data_dijamin == "undefined"){
            data_dijamin = 0
        }

        if (docoHelper.convertToAngka(subtotal) < ( parseFloat(docoHelper.convertToAngka(data_dijamin)) + parseFloat(docoHelper.convertToAngka(data_dijamin_sub))) ){
            var _subTotal = parseFloat(docoHelper.convertToAngka(subtotal))
            var _dijamin = parseFloat(docoHelper.convertToAngka(data_dijamin))
            var _dibayarPasien = parseFloat(docoHelper.convertToAngka(data_dibayar))
            var _current = _subTotal - _dijamin - _dibayarPasien
            docoNotification("warning", "Kesalahan Inputan", "Nilai Jaminan tidak boleh lebih besar daripada Subtotal")
            $(this).val(docoHelper.convertToRupiah(_current))
            return false
        }

        if (typeof dataGroupTindakan[_dataId] != "undefined" && typeof groupKelTindakan[_dataId] != "undefined") {
            var _subTotalKateg = parseFloat(docoHelper.convertToAngka(_parent.find('p.subtotal-kategory').text()))

            var _totalKategJaminSub = parseFloat(docoHelper.convertToAngka(data_dijamin_sub))
            var _totalKategJamin = parseFloat(docoHelper.convertToAngka(data_dijamin))
            var _dibayar = 0

            if (_subTotalKateg > (_totalKategJamin + _totalKategJaminSub)) {
                _dibayar = _subTotalKateg - (_totalKategJamin  + _totalKategJaminSub)
            }

            var _percentJaminan = _subTotalKateg == 0 ? 0 : parseFloat(_totalKategJamin / _subTotalKateg);
            var _percentJaminanSub = _subTotalKateg == 0 ? 0 : _totalKategJaminSub / _subTotalKateg;
            var _percentBayar = _subTotalKateg == 0 ? 0 : _dibayar / _subTotalKateg;

            Object.keys(groupKelTindakan[_dataId]).forEach(function (item) {
                initData(item)
                var _base = groupKelTindakan[_dataId][item]
                var _subTotalItem = _base.subtotal
                var _dijamin = _subTotalItem * _percentJaminan;
                var _dibayar = _subTotalItem * _percentBayar;
                var _dijaminSub = _subTotalItem * _percentJaminanSub;
                var _nominal_diskon = _base.nominal_diskon;

                changeData[item].dijamin = _dijamin
                changeData[item].dijamin_subpayer = _dijaminSub
                changeData[item].totalDibayar = _dibayar
                changeData[item].nominal_diskon = _nominal_diskon;
                changeData[item].subtotal = _subTotalItem

                groupKelTindakan[_dataId][item].dijamin = _dijamin
                groupKelTindakan[_dataId][item].dijamin_subpayer = _dijaminSub
                groupKelTindakan[_dataId][item].totalDibayar = _dibayar
                groupKelTindakan[_dataId][item].nominal_diskon = _nominal_diskon;
                groupKelTindakan[_dataId][item].subtotal = _subTotalItem
            })

            _parent.find('.edit-harusbayar-parent').val(docoHelper.convertToRupiah(_dibayar))
            if (_parent.find('span').hasClass('minus')) {
                // destroyDataTabels()
                $(`.${_dataId}`).remove()
                _generateDetail($(this), _dataId)
            }
            setTotalSementara();
        }
    })

    $('.selectpenjamin-parent').on('change', function (e) {
        e.preventDefault()
        isChanges = true
        checkPenjaminParent = false
        _penjaminIdCheck = 0;
        var _parent = $(this).closest('tr');
        var _dataId = _parent.data('id');
        let _penjaminId = $(this).find('option:selected').val();
        let _penjaminNama = $(this).find('option:selected').text();
        var newDefaultPenjamin = { 'id': $(this).find('option:selected').val(), 'text': $(this).find('option:selected').text(), 'selected': true }
        if (typeof dataGroupTindakan[_dataId] != "undefined" && typeof groupKelTindakan[_dataId] != "undefined") {
            Object.keys(groupKelTindakan[_dataId]).forEach(function (item) {
                initData(item)
                groupKelTindakan[_dataId][item].defaultPenjamin = newDefaultPenjamin;
                changeData[item].penjaminId = _penjaminId;
                changeData[item].penjaminNama = _penjaminNama;
            })
            if (_parent.find('span').hasClass('minus')) {
                // destroyDataTabels()
                $(`.${_dataId}`).remove()
                _generateDetail($(this), _dataId)
            }
        }
    })
}

$('.simpan-edit-tagihan').on('click', function (e) {
    var header = 'Perhatian !';
    if (isChangePrice) {
        add = $('#confirm-form').clone().removeClass('hidden');
        add.find('.input-pemakai').removeAttr('readonly');
        add.find('.input-pemakai').attr('value', '');
        add.find('.input-pemakai').attr('id', 'pemakai-validasi');
        add.find('.input-pemakai').attr('placeholder', 'Username');
        add.find('.input-sandi').attr('id', 'sandi-validasi');
        add = add.html();

        var message = 'Apakah anda yakin untuk mengedit tarif tagihan pada transaksi ini ?' + add;

        var label = {
            buttons: {
                'Yes': 'button-yes',
                'No': 'button-no'
            },
            hidden: true
        };
    } else {
        var message = 'Apakah anda yakin untuk menyimpan data ini ?'
        var label = {
            buttons: {
                'Yes': 'button-yes',
                'No': 'button-no'
            }
        };
    }

    //Validasi Pada Edit Tagihan
    var penjaminUtama = penjaminSub = _sendTmpData = [];
    var plafon_payer = plafon_subpayer = excess_pasien = total_dijamin = 0
    var isNotValid = false
    var total_biaya = parseFloat(docoHelper.convertToAngka($('#total-biaya').val()))
    var total_diskon = parseFloat(docoHelper.convertToAngka($('#total-diskon').val()))
    var total_bayar = parseFloat(docoHelper.convertToAngka($('#totalharusbayar').val()))

    if(typeof $('#selisih-penjamin').val() != 'undefined') {
        plafon_payer = parseFloat(docoHelper.convertToAngka($('#selisih-penjamin').val()))
    }

    if(typeof $('#selisih-penjamin-sub').val() != 'undefined') {
        plafon_sub_payer = parseFloat(docoHelper.convertToAngka($('#selisih-penjamin-sub').val()))
    }
    if(typeof $('#excess-pasien').val() != 'undefined') {
        excess_pasien = parseFloat(docoHelper.convertToAngka($('#excess-pasien').val()))
    }

    if(typeof $('#totaldijamin').val() != 'undefined') {
        total_dijamin = parseFloat( docoHelper.convertToAngka($('#totaldijamin').val()))
    }

    var validasi_tagihan = total_biaya - (total_dijamin + total_diskon + total_bayar)

    if(excess_pasien < 0) {
        docoNotification("warning", "Kesalahan Data", "Nominal Tindakan/Obat tidak sesuai. Silahkan cek kembali")
        return false
    }

    if(_listPenjamin.length > 1) {
        if($('#total-plafon-main-payer').val() != 'undefined') {
            var _totalPlafonMainPayer = docoHelper.convertToAngka($('#total-plafon-main-payer').val())

            if(_totalPlafonMainPayer == 0) {
                docoNotification("warning", "Kesalahan Data", "Total Tagihan Main Payer tidak dapat 0 Rupiah")
                return false
            }
            else {
                if(plafon_payer > _totalPlafonMainPayer) {
                    docoNotification("warning", "Kesalahan Data", "Plafon Main Payer tidak dapat lebih besar dari Total Main Payer.")
                    return false
                }
            }
        }

        if(plafon_payer == 0 && plafon_sub_payer > 0) {
            docoNotification("warning", "Kesalahan Data", "Total Plafon Main Payer tidak dapat 0 Rupiah")
            return false
        }

        if(plafon_payer > 0 && plafon_sub_payer == 0) {
            docoNotification("warning", "Kesalahan Data", "Total Plafon Sub Payer tidak dapat 0 Rupiah")
            return false
        }
    }

    if(validasi_tagihan != 0){
        Object.entries(groupKelTindakan).forEach((item,key) => {
            Object.values(item[1]).forEach((val,keyItem)=>{
                lockTagihan = false
                tmpHarga = parseFloat(val.harga)
                tmpCyto = (isNaN(val.cyto) || val.cyto == ''|| val.cyto =='0' || val.cyto == null) ? 0 : parseFloat(val.cyto)
                tmpPenyulit = (isNaN(val.penyulit)|| val.penyulit =='' || val.penyulit == '0' || val.penyulit == null) ? 0 : parseFloat(val.penyulit)
                tmpQty =  parseFloat(val.qty)
                tmpDijamin = parseFloat(val.dijamin)
                tmpDijaminSubpayer = (isNaN(val.dijamin_subpayer) || val.dijamin_subpayer =='' || val.dijamin_subpayer == '0' || val.dijamin_subpayer == null) ? 0 : parseFloat(val.dijamin_subpayer)
                tmpTotalDibayar = parseFloat(val.totalDibayar)
                tmpNominalDiskon = (isNaN(val.nominal_diskon)|| val.nominal_diskon =='' || val.nominal_diskon == '0' || val.nominal_diskon == null) ? 0 : parseFloat(val.nominal_diskon)
                tmpValidasiHarga = parseFloat(tmpQty*(tmpHarga + tmpCyto + tmpPenyulit)).toFixed(2)
                tmpValidasiPembayaran = parseFloat(tmpDijamin + tmpDijaminSubpayer + tmpTotalDibayar +tmpNominalDiskon).toFixed(2)
                tmpValidasi = tmpValidasiHarga - tmpValidasiPembayaran
                if(tmpTotalDibayar < 0 || tmpNominalDiskon < 0 || tmpDijaminSubpayer < 0 || tmpDijamin < 0 || tmpQty < 0 || tmpHarga < 0){
                    lockTagihan = true
                }
                if(tmpValidasi != 0 || tmpValidasiHarga < 0 || tmpValidasiPembayaran < 0 || lockTagihan == true){
                    isNotValid = true
                    if(item[0] === ""){
                        trError = $('tr[data-id=""]')
                        trError.css("background-color","#ff9ca2")
                        // trError.trigger('click')
                    } else
                    if(item[0] !== "" || item[0] !=  null || item[0] != ' '){
                        trError = $('tr[data-id='+ item[0] +']')
                        _isOpened = $(trError).find('span').hasClass('plus');
                        if (_isOpened){
                            detailError = $(trError).find('.plus')
                            $(detailError).trigger('click')
                        }
                    }
                }
            })
            if(isNotValid){
                docoNotification("warning", "Kesalahan Data", "Nominal Tindakan/Obat tidak sesuai. Silahkan cek kembali")
            }
        })
        if(isNotValid){
            return false
        }
    }

    var _simpanTmpData = function (uid) {
        var _mappSendData = function (keyIden) {
            if (typeof tmpTableTransaksi[keyIden] !== 'undefined') {
                var obj = $.extend({}, tmpTableTransaksi[keyIden]);
                _sendTmpData[keyIden] = obj;
            }
        }

        //Penyesuaian Penyimpanan Data Penjamin untuk MultiPayer Penjamin
        _listPenjamin.forEach((val,key) =>{
            if(_listPenjamin.length == 2){
                if (val.selected == true){
                    penjaminUtama = val
                } else {
                    penjaminSub = val
                    penjaminSub.id = parseInt(val.id)
                }
            }
        })
        if($('#selisih-penjamin').val() != 0){
            plafon_payer = docoHelper.convertToAngka($('#selisih-penjamin').val())
            plafon_subpayer = docoHelper.convertToAngka($('#selisih-penjamin-sub').val())
        }


        changeData.forEach((data, key) => {
            if (typeof tmpTableTransaksi[key] !== 'undefined') {
                _mappSendData(key);
                _sendTmpData[key].dijamin = data.dijamin
                _sendTmpData[key].totalDibayar = data.totalDibayar
                _sendTmpData[key].keterangan = data.keterangan
                _sendTmpData[key].nominal_diskon = data.nominal_diskon
                _sendTmpData[key].persen_diskon = data.persen_diskon

                if (data.harga != null) {
                    _sendTmpData[key].harga = data.harga
                    _sendTmpData[key].cyto = data.cyto
                    _sendTmpData[key].penyulit = data.penyulit
                    _sendTmpData[key].subtotal_origin = (data.harga + data.cyto + data.penyulit) * _sendTmpData[key].qty
                }

                if (data.subtotal || data.subtotal == 0) {
                    _sendTmpData[key].subtotal = data.subtotal
                }

                if (data.penjaminNama) {
                    _sendTmpData[key].penjamin = data.penjaminNama
                }

                if (data.penjaminId) {
                    _sendTmpData[key].defaultPenjamin.id = data.penjaminId
                    _sendTmpData[key].defaultPenjamin.text = data.penjaminNama
                }

                if (data.isPenjamin !== null) {
                    _sendTmpData[key].isPenjamin = true
                if(data.isPenjamin =='checked'){
                    _sendTmpData[key].checkPenjamin = true
                } else
                { _sendTmpData[key].checkPenjamin = false}
                }
                if(_listPenjamin.length >= 2){
                    _sendTmpData[key].defaultPenjamin = penjaminUtama
                    _sendTmpData[key].subPenjamin = penjaminSub
                    if(typeof data.dijamin_subpayer == 'undefined' || data.dijamin_subpayer == ''){
                        data.dijamin_subpayer = 0
                    }
                    _sendTmpData[key].dijamin_subpayer = data.dijamin_subpayer
                }
            }
        })

        let _dataTmp = {
            _tagihanPasien: _sendTmpData,
            _pendaftaran_id: _pendaftaran_id,
            _pasienmasukpenunjang_id: _pasienmasukpenunjang_id,
            verify_uid: uid || '',
            plafon_payer : plafon_payer,
            plafon_subpayer : plafon_subpayer,
            excess_pasien: excess_pasien
        };

        tmpTablePenjamin_id = [];
        totalbayar_kasir = subtotal_real_kasir = 0
        _sendTmpData.forEach(function (val, key) {
            subtotal_real_kasir += parseFloat(val.subtotal_origin)
            totalbayar_kasir += parseFloat(val.totalDibayar)
            tmpTablePenjamin_id.push({
                dijamin : val.dijamin,
                dijamin_subpayer : val.dijamin_subpayer,
                penjamin : val.penjamin
            })

            if(docoHelper.convertToAngka($('#selisih-penjamin').val()) > 0 ){
                if(_listPenjamin.length > 1){
                    _sendTmpData[key].plafon_subpayer = docoHelper.convertToAngka($('#selisih-penjamin-sub').val())
                }
                    _sendTmpData[key].plafon_payer = docoHelper.convertToAngka($('#selisih-penjamin').val())
            } else {
                if(_listPenjamin.length > 1){
                    _sendTmpData[key].plafon_subpayer = 0
                }
                _sendTmpData[key].plafon_payer = 0
            }

            if(docoHelper.convertToAngka($('#excess-pasien').val()) > 0 ) {
                _sendTmpData[key].excess_pasien = docoHelper.convertToAngka($('#excess-pasien').val())
            }
        })
        var _nilaiTotalPembulatan = 0
        var _nilaiTotalDijamin = 0

        tmpTablePenjamin_id.forEach(function(val,key){
            if(typeof val.dijamin_subpayer == 'undefined' || val.dijamin_subpayer == '' || isNaN(val.dijamin_subpayer)){
                val.dijamin_subpayer = 0
            }
            _nilaiTotalDijamin += parseFloat(val.dijamin) + parseFloat(val.dijamin_subpayer)
            bulatan_jaminan = docoHelper.calculateRounding(val.dijamin + val.dijamin_subpayer)
            if(bulatan_jaminan.nominal_selisih > 0){
                _nilaiTotalPembulatan += bulatan_jaminan.nominal_selisih
            }
        })

        // console.log(_dataTmp,_sendTmpData)
        // return false

        $.ajax({
            url: `/kasir/pembayaran-tagihan/save-tmp-tagihan?_uidProccess=${_uidProccess}`,
            type: "POST",
            data: JSON.stringify(_dataTmp),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function () {
                _isDefaultTmp = true;
                docoNotification("success", "Success!", "Data Berhasil Disimpan!")
                tmpTableTransaksi = _sendTmpData
                _totalInsurance = 0
                var nilai_rupiah = 0

                var pembulatan_dijamin = docoHelper.calculateRounding(_nilaiTotalDijamin)
                totalbayar_kasir = parseFloat(totalbayar_kasir)
                _nilaiTotalDijamin = parseFloat(_nilaiTotalDijamin)
                biaya_diskon = parseFloat(docoHelper.convertToAngka($('#total-diskon').val()))

                $('#total_tagihan').val(docoHelper.convertToRupiah(subtotal_real_kasir))
                $('#subsidi-asuransi_pembulatan').val(pembulatan_dijamin.nominal_selisih)
                $('#modal_backdrop').modal('hide')
                if (isChangePrice) {
                    $('#biaya_administrasi').val((generateChangePrice._biayaAdm).toString().replace(".",",")).trigger('change')
                    //$('#total_tagihan').val((_totalTagihanPembulatan).toString().replace(".",",")).trigger('change')
                }

                table.draw();
                reloadTagihan()
                enableButtonInvoice();
                var sisa_piutang = docoHelper.convertToAngka($('#total_sisa_piutang').val())
                var diskonDokter = docoHelper.convertToAngka($('#diskon-dokter').val())
                var diskonTotal = docoHelper.convertToAngka($('#total_diskon').val())
                $('#total_sisa_piutang').val(docoHelper.convertToRupiah(sisa_piutang + diskonDokter))
                $("#print-detail-edit-tagihan").show();
                $("#print-summary-edit-tagihan").show();

                //Reset Warna untuk unbalance tagihan
                if(listUnbalanceTagihan.length > 0 ){
                    listUnbalanceTagihan.forEach(function(val){
                        $('tr[data-id='+ val +']').css('background-color','')
                    })
                    listUnbalanceTagihan = []
                }

            },
            error:function (res) {
               var _response = []
               if(typeof (res.responseJSON) !== 'undefined') {
                   _response = res.responseJSON.response
                   _title = _response.title
                   _message = _response.text
                   if(_title && _message) {
                       docoNotification('error', _title, _message)
                   }
               }
            }
        })
        changeData = []
    }

    e.preventDefault();
    $.showQuestionDialog(header, message, label, function (reaction) {
        if (reaction == 'Yes') {
            if (isChangePrice) {
                var user = $('#pemakai-validasi').val();
                var pass = $('#sandi-validasi').val();
                $().docoForm('click', {
                    url: baseUrl + 'kasir/end-point/check-authorization',
                    skipConfirm: true,
                    skipSuccessNotif: true,
                    data: {
                        nama_pemakai: user,
                        katakunci_pemakai: pass,
                        akses: 'save-tmp-tagihan'
                    },
                    success: function (data) {
                        var response = data.response;
                        setTimeout(function () {
                            showLoader()
                        }, 100);
                        _simpanTmpData(response.verify_uid);
                    }
                })
            } else {
                _simpanTmpData()
            }
        }
        if (reaction == 'No') {
            hideQuestionDialog();
            $('[data-popup="tooltip"]').tooltip();
        }
    });
});

$('#selisih-penjamin').on('keyup change', function(){
    var defaultPenjamin = 0
    var selisihPenjamin = docoHelper.convertToAngka($(this).val())
    var totaldijamin = docoHelper.convertToAngka($(`#totaldijamin`).val())
    selisihPenjaminSub = 0

    if($('#selisih-penjamin-sub').val() != 'undefined') {
        selisihPenjaminSub = docoHelper.convertToAngka($('#selisih-penjamin-sub').val())
    }

    // if(selisihPenjamin == 0) {
    //     docoNotification("warning", "Kesalahan Inputan", "Plafon Main Payer tidak dapat 0 Rupiah");
    //     $(this).val(docoHelper.convertToRupiah(totaldijamin - selisihPenjaminSub))
    //     return false
    // }
    // else {
        //Membuka setiap data detail tindakan/obat
        $('#table-edit-tagihan').each((i,e) => {
            $(e).find('.table-tagihan-header').each(function(){
                _isOpened = $(this).find('span').hasClass('plus');
                if (_isOpened){
                    $(this).trigger('click')
                }
            })
        })
    // }
})

$('#selisih-penjamin').on('change',function (e){
    if($('.excess_penjamin').is(':visible')){
        var harusBayarDiLuarPlafon = harusBayarSinglePayer = 0
        var perhitungan_prorate_admin = 0
        var selisihPenjamin = docoHelper.convertToAngka($('#selisih-penjamin').val())
        var selisihPenjaminSub = docoHelper.convertToAngka($('#selisih-penjamin-sub').val())
        var totalBiaya =  docoHelper.convertToAngka($('#total-biaya').val())
        var plafonMainPayer =  docoHelper.convertToAngka($('#total-plafon-main-payer').val())
        var totalJaminan =  docoHelper.convertToAngka($('#totaldijamin_main').val())
        var totalDiskon =  docoHelper.convertToAngka($('#total-diskon').val())
        var plafonPayer = docoHelper.convertToAngka($('#selisih-penjamin').val())
        var biayaAdmRow = $("td:contains(Biaya Administrasi)").closest('tr');
        var _totalDijamin = docoHelper.convertToAngka($('#totaldijamin').val())
        var _harusBayar = docoHelper.convertToAngka($('#totalharusbayar').val())
        var _excessPasien = _totalDijamin - selisihPenjamin - selisihPenjaminSub

        if( (selisihPenjamin ) > totalBiaya){
            docoNotification("warning", "Kesalahan Inputan", "Plafon Main Payer tidak dapat melebihi total tagihan");
            $(this).val(0)
            return false
            // resetPlafonPenjamin()
        }
        else if((selisihPenjamin ) > plafonMainPayer) {
            docoNotification("warning", "Kesalahan Inputan", "Plafon Main Payer tidak dapat melebihi total jaminan");
            $(this).val(0)
            return false
            // resetPlafonPenjamin()
        }
        else {
            if(selisihPenjamin > 0){
                if ( dijamin_subtotal.length == 0) {
                    defaultPenjamin_total =  totalJaminan
                }
                $('#table-edit-tagihan').each((index,elements) => {
                    $(elements).find('.edit-harusbayar').each(function(){
                        if( _listPenjamin.length == 1 && !($(this).parent().parent().find('.checkPenjamin').is(':checked'))){
                            harusBayarSinglePayer += parseFloat(docoHelper.convertToAngka($(this).val()))
                        }
                    })
                })
                if(!$(biayaAdmRow).find('.checkPenjamin-parent').is(':checked')){
                    if( _listPenjamin.length == 1 ){
                        harusBayarSinglePayer += parseFloat(docoHelper.convertToAngka($(biayaAdmRow).find('.subtotal-kategory').text()))
                    }
                }
                if( (selisihPenjamin ) > (totalBiaya - harusBayarSinglePayer)){
                    docoNotification("warning", "Kesalahan Inputan", "Plafon Main Payer tidak dapat melebihi total jaminan");
                    $(this).val(0).trigger('change')
                } else{

                    if($(biayaAdmRow).find('.subtotal-kategory').hasClass('subtotal-kategory')){
                        // Akumulasi dari diskon biaya admin disimpan) *Saat ini di comment karena mau dibuat seperti as-is
                        // $(biaya_adm_row).find('.nominal-discount-parent').val(0).trigger('change');
                        // $(biaya_adm_row).find('.edit-dijamin-parent').val(0).trigger('change');
                        // $(biaya_adm_row).find('.edit-dijamin-parent-sub').val(0).trigger('change');
                        Object.keys(groupKelTindakan['']).forEach(function (item) {
                            initData(item)
                            //changeData[item].totalDibayar = 0
                        })
                    }

                    $('#table-edit-tagihan').each((index,element) => {
                        $(element).find('.checkPenjamin, .subtotal_detail, .selectpenjamin, .edit-dijamin, .edit-dijamin-sub, .nominal-discount').each(function(){
                            //*Saat ini di comment karena mau dibuat seperti as-is ketika isi plafon
                            if($(this).hasClass('subtotal_detail')){
                                // tmpSubtotal_detail = docoHelper.convertToAngka($(this).text())
                            }
                            if($(this).attr('name') == 'edit-dijamin'){
                                // $(this).val(docoHelper.convertToRupiah(0)).trigger('change')
                            }
                            if($(this).attr('name') == 'edit-dijamin-sub'){
                                // $(this).val(docoHelper.convertToRupiah(0)).trigger('change')
                            }
                            if($(this).attr('class') == 'checkPenjamin'){
                                //$(this).val(docoHelper.convertToRupiah(0)).trigger('change')
                            }
                            if($(this).attr('name') == 'nominal-discount'){
                                penjaminChecked = $(this).parent().parent().parent().parent().parent().find('.checkPenjamin')
                                if($(penjaminChecked).prop('checked')){
                                    // $(this).val(docoHelper.convertToRupiah(0)).trigger('change')
                                }
                            }
                        })
                        $(element).find('.edit-harusbayar, .edit-harusbayar-parent').each(function(){
                            if($(this).attr('name') == 'edit-harusbayar'){
                                if($(this).parent().parent().find('.checkPenjamin').is(':checked')){
                                    // $(this).val(docoHelper.convertToRupiah(0)).trigger('change')
                                    parentTr = $(this).closest('tr')
                                    id_parent = parentTr.attr('data-id')
                                } else {
                                    //harusBayarDiLuarPlafon += docoHelper.convertToAngka($(this).val())
                                }
                            }
                            if($(this).hasClass('edit-harusbayar-parent')){
                                if($(this).parent().parent().find('.checkPenjamin-parent').is(':checked')){
                                    harusBayarDiLuarPlafon += docoHelper.convertToAngka($(this).val())
                                }
                            }
                        })
                    })

                    totalJaminanSubPayer = totalBiaya - plafonPayer - _harusBayar - totalDiskon
                    if(_listPenjamin.length >= 2){
                        if(totalJaminanSubPayer >= 0){
                            $('#selisih-penjamin-sub').val(docoHelper.convertToRupiah(totalJaminanSubPayer))
                            $('#excess-pasien').val(docoHelper.convertToRupiah(_totalDijamin - selisihPenjamin - totalJaminanSubPayer))
                        } else {
                            docoNotification("warning", "Kesalahan Inputan", "Plafon Main Payer tidak dapat melebihi total jaminan");
                            return false
                            // resetPlafonPenjamin()
                        }
                    }
                    else {
                        setTotalSementara()
                        _harusBayar = _totalDijamin - selisihPenjamin
                        $('#totalharusbayar').val(docoHelper.convertToRupiah(_harusBayar))
                    }
                }
            }
            else {
                if(selisihPenjamin == 0 && selisihPenjaminSub == 0) {
                    _excessPasien = 0;
                }
                $('#excess-pasien').val(docoHelper.convertToRupiah((_excessPasien)))
                // return false
                // docoNotification("warning", "Kesalahan Inputan", "Plafon Main Payer tidak dapat 0 Rupiah");
                // resetPlafonPenjamin()
            }
        }
    }
})

$('#selisih-penjamin-sub').on('change',function (e){
    if($('.excess_penjamin_sub').is(':visible')){
        var harusBayarDiLuarPlafon = harusBayarSinglePayer = 0
        var selisihPenjamin = docoHelper.convertToAngka($('#selisih-penjamin').val())
        var selisihPenjaminSub = docoHelper.convertToAngka($('#selisih-penjamin-sub').val())
        var totalBiaya =  docoHelper.convertToAngka($('#total-biaya').val())
        var totalJaminan =  docoHelper.convertToAngka($('#totaldijamin_main').val())
        var totalDiskon =  docoHelper.convertToAngka($('#total-diskon').val())
        var biayaAdmRow = $("td:contains(Biaya Administrasi)").closest('tr');
        var _totalDijamin = docoHelper.convertToAngka($('#totaldijamin').val())
        var _excessPasien = _totalDijamin - selisihPenjamin - selisihPenjaminSub

        if( (selisihPenjaminSub ) > (_totalDijamin - selisihPenjamin)){
            var _selisihSub = _totalDijamin - selisihPenjamin
            docoNotification("warning", "Kesalahan Inputan", "Plafon Sub Payer tidak dapat melebihi total tagihan");
            $(this).val(docoHelper.convertToRupiah(_selisihSub))
            $('#excess-pasien').val(0)
            return false
        }
        // else if(selisihPenjamin == 0) {
        //     docoNotification("warning", "Kesalahan Inputan", "Plafon Main Payer tidak dapat 0 rupiah");
        //     $(this).val(0)
        //     return false
        // }
        else {
            if(selisihPenjaminSub > 0){
                if ( dijamin_subtotal.length == 0) {
                    defaultPenjamin_total =  totalJaminan
                }
                $('#table-edit-tagihan').each((index,elements) => {
                    $(elements).find('.edit-harusbayar').each(function(){
                        if( _listPenjamin.length == 1 && !($(this).parent().parent().find('.checkPenjamin').is(':checked'))){
                            harusBayarSinglePayer += parseFloat(docoHelper.convertToAngka($(this).val()))
                        }
                    })
                })
                if(!$(biayaAdmRow).find('.checkPenjamin-parent').is(':checked')){
                    if( _listPenjamin.length == 1 ){
                        harusBayarSinglePayer += parseFloat(docoHelper.convertToAngka($(biayaAdmRow).find('.subtotal-kategory').text()))
                    }
                }

                if( (selisihPenjaminSub ) > (totalBiaya - harusBayarSinglePayer)){
                    docoNotification("warning", "Kesalahan Inputan", "Plafon Sub Payer tidak dapat melebihi total jaminan");
                    $(this).val(0).trigger('change')
                } else{
                    if($(biayaAdmRow).find('.subtotal-kategory').hasClass('subtotal-kategory')){
                        Object.keys(groupKelTindakan['']).forEach(function (item) {
                            initData(item)
                        })
                    }

                    $('#table-edit-tagihan').each((index,element) => {
                        $(element).find('.edit-harusbayar, .edit-harusbayar-parent').each(function(){
                            if($(this).attr('name') == 'edit-harusbayar'){
                                if($(this).parent().parent().find('.checkPenjamin').is(':checked')){
                                    parentTr = $(this).closest('tr')
                                    id_parent = parentTr.attr('data-id')
                                }
                            }
                            if($(this).hasClass('edit-harusbayar-parent')){
                                if($(this).parent().parent().find('.checkPenjamin-parent').is(':checked')){
                                    harusBayarDiLuarPlafon += docoHelper.convertToAngka($(this).val())
                                }
                            }
                        })
                    })

                    totalJaminanMainPayer = totalBiaya - selisihPenjaminSub - harusBayarDiLuarPlafon - totalDiskon
                    if(_listPenjamin.length >= 2) {
                        if(totalJaminanMainPayer >= 0) {
                            $('#excess-pasien').val(docoHelper.convertToRupiah(_excessPasien))
                        } else {
                            docoNotification("warning", "Kesalahan Inputan", "Plafon Sub Payer tidak dapat melebihi total jaminan");
                            return false
                        }
                    }
                    else {
                        setTotalSementara()
                    }
                }
            }
            else {
                // docoNotification("warning", "Kesalahan Inputan", "Plafon Sub Payer tidak dapat 0 rupiah");
                // $(this).val(docoHelper.convertToRupiah(_totalDijamin - selisihPenjamin))
                // return false
                if(selisihPenjamin == 0 && selisihPenjaminSub == 0) {
                    _excessPasien = 0;
                }
                $('#excess-pasien').val(docoHelper.convertToRupiah((_excessPasien)))
            }
        }
    }
})

$('.btn-popup-kembali').on('click', function (e) {
    var header = 'Perhatian !'
    var message = 'Apakah anda yakin TIDAK akan menyimpan data pada halaman ini ?'
    var label = {
        buttons: {
            'Yes': 'button-yes',
            'No': 'button-no'
        }
    };

    e.preventDefault();
    if (isChanges) {
        $.showQuestionDialog(header, message, label, function (reaction) {
            if (reaction == 'Yes') {
                $('#modal_backdrop').modal('hide')
                changeData = []
                groupKelTindakan = {}
                dataGroupTindakan = {}
                table.draw()
                reloadTagihan()
            }
            if (reaction == 'No') {

            }
        });
    } else {

        $('#modal_backdrop').modal('hide')
    }

});

$(document).on('change','#table-edit-tagihan_length',function(){
    $('#table-edit-tagihan').each((index,elements) => {
        $(elements).find('.minus').each(function(){
            $(this).text('[ + ]')
            $(this).removeClass('minus');
            //$(`.${_trimKel}`).remove()
            // initDataTabels()
            $(this).addClass('plus');
        })
    })
})

$(document).on('change', '.persen-discount-chk', function (e) {
    e.preventDefault();
    // if( docoHelper.convertToAngka($('#selisih-penjamin').val()) > 0 ){
    //     resetPlafonPenjamin()
    // }

    var _parent = $(this)
    var _trParent = _parent.closest('tr');
    var _percenDis = _trParent.find('.percent-discount');
    var _nominDis = _trParent.find('.nominal-discount');
    _percenDis.prop("disabled", true);
    _nominDis.prop("disabled", true);
    if (_parent.is(':checked')) {
        _percenDis.prop("disabled", false);
    } else {
        _nominDis.prop("disabled", false);
    }
    _trParent.find('.percent-discount').val(0).trigger('change')
})

$(document).keypress(
    function(event){
    if (event.which == '13') {
        event.preventDefault();
    }
});

function enableButtonInvoice() {
    $('#cetak-invoice-belum-bayar').prop("disabled", false);
}
