
const baseUrlServiceApi = '/gudang/penerimaan-barang-manual'
let satuanList = {}
let selectedSatuan = {}
let dataBarang = []
let indexTable = 0
let is_donasi = false

$(() => {
    $('.info-harga').hide();

    $("#expiredDateClearBtn").bind('click', () => {
        $('#expiredDate').val('')
        $("#infoKadaluarsaWrapper").show()
    })
    const urlSourceData = `${baseUrlServiceApi}/source-data`
    $("#infoKonversiSection").hide()
    renderPickadate($('#expiredDate'), {
        dependElementPicker: $('#expiredDateBtn'),
        dependElementRemoveValue: $('#expiredDateClearBtn'),
        moreThanToday: true
    });
    $("#expiredDate").bind('change', () => {
        if ($("#expiredDate").val() !== '') {
            $("#infoKadaluarsaWrapper").hide()
        }
    })
    $("#satuankonversi_id").select2()
    infinityScrollSelect2($("#supplierName"), urlSourceData, (params) => {
        return {
            type: 'supplier',
            payload: {
                term: params.term,
                page: params.page || 1
            }
        }
    }, (res, params) => {
        params.page = params.page || 1;
        const { data } = res
        const results = []
        data.map((item, index) => {
            if (index < 10) {
                results.push({
                    id: item.supplier_id,
                    text: item.supplier_nama
                })
            }
        })

        return {
            results,
            pagination: {
                more: data.length > 10
            }
        };
    })
    infinityScrollSelect2($("#peg_menyetujui,#peg_mengetahui"), urlSourceData, (params) => {
        return {
            type: 'pegawai',
            payload: {
                term: params.term,
                page: params.page || 1
            }
        }
    })
    infinityScrollSelect2($("#barangInputElement"), urlSourceData, (params) => {
        return {
            type: 'barang',
            payload: {
                term: params.term,
                page: params.page || 1
            }
        }
    })
    $("#barangInputElement").on('change.select2', () => {
        if ($("#barangInputElement").val() === null || $("#barangInputElement").val() === '') {
            $("#penerimaansupplierdetailform-qty_besar").val(0).prop('disabled', true)
            $("#satuankonversi_id").prop('disabled', true)
            $("#satuankonversi_id").val('').trigger('change')
        } else {
            $("#infoKonversiSection").hide()
            $.ajax({
                url: urlSourceData,
                data: {
                    type: 'satuanKonversiBarang',
                    barang_id: $("#barangInputElement").val()
                },
                success: (res) => {
                    const dataSatuan = [
                        {
                            id: '',
                            text: '----Pilih Satuan----'
                        }
                    ]
                    satuanList = {}
                    res.data.map((itemSatuan) => {
                        Object.assign(satuanList, {
                            [itemSatuan.satuankonversibrg_id]: itemSatuan
                        })
                        dataSatuan.push({
                            id: itemSatuan.satuankonversibrg_id,
                            text: `1 ${itemSatuan.besar} = ${itemSatuan.nilai_konversi} ${itemSatuan.kecil}`
                        })
                    })
                    refreshOptionSelect2($("#satuankonversi_id"), dataSatuan)
                    $("#satuankonversi_id").prop('disabled', false)
                }
            })
        }
    })
    $("#btnAddDetail").bind('click', () => {
        // validation frontend
        const rulesAndElement = {
            barang_id: [
                'required'
            ],
            satuankonversi_id: [
                'required'
            ],
            qty_besar: [
                'required',
                'greaterThan:0',
                'integer'
            ],
            harga_netto: [
                'required',
                'greaterThan:0',
                'integer'
            ],
            diskon: [
                'required',
                'integer'
            ]
        }
        const resultValidation = validateForm($("#detailFormTemp"), 'PenerimaanSupplierDetailForm', rulesAndElement)
        if (resultValidation) {
            if (dataBarang.indexOf($("#barangInputElement").val()) >= 0) {
                docoNotification('error', 'Input Gagal', `${$("#barangInputElement").select2('data')[0].text} telah ada pada tabel.`)
                return false
            }
            dataBarang.push($("#barangInputElement").val())
            const bodyTable = $($("#tablePenerimaanTmp").find('tbody'))
            if (bodyTable.find('td[colspan="10"]').length > 0) {
                bodyTable.html('')
            }
            let dateFormatYmd = ''
            if ($("#expiredDate").val() !== '') {
                const splitedDate = $("#expiredDate").val().split('-')
                dateFormatYmd = `${splitedDate[2]}-${splitedDate[1]}-${splitedDate[0]}`
            }
            const dataPayload = {
                barang_id: $("#barangInputElement").val(),
                satuankonversi_id: $("#satuankonversi_id").val(),
                qty_besar: $("#penerimaansupplierdetailform-qty_besar").val().split('.').join(''),
                satuanbesar_id: selectedSatuan.satuanbesar_id,
                satuankecil_id: selectedSatuan.satuankecil_id,
                satuankonversi_id: selectedSatuan.satuankonversibrg_id,
                qty_kecil: parseInt($('#penerimaansupplierdetailform-qty_besar').val().split('.').join('')) * selectedSatuan.nilai_konversi,
                tgl_kadaluarsa: dateFormatYmd,
                harga_netto: $("#penerimaansupplierdetailform-harga_netto").val().split('.').join(''),
                diskon: $("#penerimaansupplierdetailform-diskon").val(),
                no_batch: $("#penerimaansupplierdetailform-no_batch").val(),
                keterangan: $("#penerimaansupplierdetailform-keterangan").val(),
            }
            let elementInput = ''
            Object.keys(dataPayload).map((itemKey) => {
                elementInput += `<input name="PenerimaanSupplierDetailForm[${indexTable}][${itemKey}]" id="${itemKey}" value="${dataPayload[itemKey]}" type="hidden">`
            })
            // $("#tablePenerimaanTmp > tbody > tr:first-child").remove();
            bodyTable.append(`
                <tr>
                    ${elementInput}
                    <td id="rowNumberSection">${bodyTable.find('tr').length + 1}</td>
                    <td>${$("#barangInputElement").select2('data')[0].text}</td>
                    <td>${$("#penerimaansupplierdetailform-qty_besar").val()}</td>
                    <td>${(dataPayload.qty_kecil).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")}</td>
                    <td>${dataPayload.tgl_kadaluarsa !== '' ? convertDateByFormat(dateFormatYmd, 'd-m-Y') : '-'}</td>
                    <td>${dataPayload.harga_netto.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")}</td>
                    <td>${dataPayload.diskon}</td>
                    <td>${dataPayload.no_batch}</td>
                    <td>${dataPayload.keterangan}</td>
                    <td><button type="button" class="btn btn-xs btn-danger btn-delete-row"><i class="fa fa-trash"></i></button></td>
                </tr>
            `)
            indexTable += 1
            $("#detailFormTemp")[0].reset()
            $("#barangInputElement").val(null).trigger('change')
            $('#penerimaansupplierdetailform-harga_netto').val(is_donasi ? harga_donasi : 0);
            $('#penerimaansupplierform-is_donasi').prop('disabled', true);
        }
    })
    $("#simpan-penerimaan").on('click', (event) => {
        event.preventDefault();
        let dataPayload = $("#penerimaan-form").serializeArray()
        if ($("#detailForm").serializeArray().length === 0) {
            docoNotification('error', 'Terjadi kesalahan', 'Mohon input detail penerimaan.')
            return false
        }
        dataPayload = dataPayload.concat($("#detailForm").serializeArray())

        confirmationDialog('Apakah anda yakin untuk menyimpan data ini ?', (isConfirm) => {
            if (isConfirm) {
                $.ajax({
                    url: `${baseUrlServiceApi}/store`,
                    method: "POST",
                    data: dataPayload,
                    success: function (res) {
                        const { data } = res
                        const no_penerimaan = data.no_penerimaan;
                        $("#penerimaan-form")[0].reset()
                        $("#supplierName").val(null).trigger('change')
                        $('#infoKonversiSection').hide();
                        $("#penerimaansupplierform-pajak_id").val(null).trigger('change')
                        $("#penerimaansupplierform-payterm_id").val(null).trigger('change')
                        $("#peg_menyetujui").val(null).trigger('change')
                        $("#peg_mengetahui").val(null).trigger('change')
                        $("#penerimaansupplierform-sumber_penerimaan").val(null).trigger('change')
                        $("[tab-index='0']").focus();
                        $('#penerimaansupplierdetailform-harga_netto').val(0);
                        $('.isDonasiHeader').val(0);
                        dataBarang = []
                        setTimeout(() => {
                            $($("#tablePenerimaanTmp tbody")).html(`
                                <tr>
                                    <td colspan="10" class="text-center">Mohon inputkan data.</td>
                                </tr>
                            `)
                            $('#penerimaansupplierform-is_donasi').prop('disabled', false);
                            $('#penerimaansupplierdetailform-harga_netto').removeAttr('readonly');
                        }, 100);
                        (new PNotify({
                            title: "Berhasil",
                            text: `Data Penerimaan Obat Alkes Supplier dengan Nomor <strong>${no_penerimaan}</strong> berhasil disimpan, apakah Anda ingin melakukan cetak?`,
                            addclass: "alert alert-success alert-arrow-right alert-styled-right",
                            type: "success",
                            buttons: {
                                closer: false,
                                sticker: false
                            },
                            hide: false,
                            confirm: {
                                confirm: true,
                                buttons: [
                                    {
                                        text: 'Ya',
                                        addClass: 'btn btn-xs btn-success',
                                    },
                                    {
                                        text: 'Tidak',
                                        addClass: 'btn btn-xs btn-danger',
                                    }
                                ]
                            },
                            history: {
                                history: false
                            }
                        })).get().on('pnotify.confirm', function () {
                            window.open(`${baseUrlServiceApi}/cetak?noPenerimaan=${no_penerimaan}`);
                        }).on('pnotify.cancel', function () {

                        });
                    },
                    error: (xhr) => {
                        if (xhr.status === 422) {
                            const responseJson = xhr.responseJSON.response.data
                            Object.keys(responseJson).map((itemElementError) => {
                                let elementError = $(`[name='${itemElementError}']`)
                                const parentSection = $(elementError.parents('.form-group')[0])
                                parentSection.addClass('has-error')
                                const errorMessage = `<i class="fa fa-exclamation-circle"></i>${responseJson[itemElementError][0]}`
                                if (parentSection.find('p.help-block.error').length == 0) {
                                    parentSection.append(`
                                        <p class="help-block error">${errorMessage}</p>
                                    `)
                                } else {
                                    $(parentSection.find('p.help-block.error')[0]).html(errorMessage)
                                }
                            })
                        } else if (xhr.status >= 400 && xhr.status <= 499) {
                            docoNotification('error', 'Terjadi kesalahan pada input.', xhr.responseJSON.meta.message)
                        } else {
                            docoNotification('error', 'Terjadi kesalahan pada server', '')
                        }

                    }
                })
            }
        })
    })

    $('#penerimaansupplierform-is_donasi').change(function(){
        onInputTotalHarga(true);
        if($(this).prop('checked')){
            is_donasi = true;
            $('#penerimaansupplierdetailform-harga_netto').val(harga_donasi).attr('readonly','readonly');
        }else{
            is_donasi = false;
            $('#penerimaansupplierdetailform-harga_netto').removeAttr('readonly');
        }
        $('.isDonasiHeader').val(is_donasi ? 1 : 0);
    });

    $('#penerimaansupplierdetailform-qty_besar').on('keyup', function(){
        var _val = $(this).val();
        var total_konversi = _val.toString().replace(/\.|,/g, '') * selectedSatuan.nilai_konversi;
        var text = total_konversi + " " + selectedSatuan.kecil;
        // $('.info-kon /versi').show();
        $("#totalKonversiSection").html(`Total Konversi ${text}`);
        onInputTotalHarga();
    });

    $('.total_harga').on('input', function(){
        onInputTotalHarga();
    });

    function onInputTotalHarga(donasi=false) {
        var total_harga = donasi ? harga_donasi : docoHelper.convertToAngka($('.total_harga').val());
        var qty = $('#penerimaansupplierdetailform-qty_besar').val().toString().replace(/\.|,/g, '') * selectedSatuan.nilai_konversi;
        var harga_satuan = total_harga/qty;
        console.log('total '+total_harga);
        console.log('qty '+qty);
        console.log('harga '+harga_satuan);
        harga_satuan = isNaN(harga_satuan) ? 0 : harga_satuan;
        harga_satuan = harga_satuan > 1 ? docoHelper.convertToRupiah(harga_satuan) : harga_satuan;
        $('.info-harga').show();
        $('.harga_netto').html("Harga Netto Satuan : Rp. "+ harga_satuan);
    }
})

// $("#penerimaansupplierdetailform-qty_besar").bind('change', ({ delegateTarget }) => {
//     $("#totalKonversiSection").html(`Total Konversi ${parseInt($(delegateTarget).val().split('.').join('')) * selectedSatuan.nilai_konversi} ${selectedSatuan.kecil}`)
// })

$(document).on('change.select2', "#satuankonversi_id", () => {
    if ($("#satuankonversi_id").val() !== null && $("#satuankonversi_id").val() !== '') {
        selectedSatuan = satuanList[$("#satuankonversi_id").val()]
        $("#infoKonversiSection").show()
        const totalKonversi = $("#penerimaansupplierdetailform-qty_besar").val() !== '' ? parseInt($("#penerimaansupplierdetailform-qty_besar").val().split('.').join('')) * selectedSatuan.nilai_konversi : 0
        $("#totalKonversiSection").html(`Total Konversi ${totalKonversi} ${selectedSatuan.kecil}`)
        $("#penerimaansupplierdetailform-qty_besar").prop('disabled', false)
    } else {
        $("#infoKonversiSection").hide()
        $("#penerimaansupplierdetailform-qty_besar").prop('disabled', true)
    }
    $('#penerimaansupplierdetailform-qty_besar').trigger('keyup');
})
$(document).on('click', '.btn-delete-row', ({ currentTarget }) => {
    const trTarget = $($(currentTarget).parents('tr')[0])
    const idBarang = $($(currentTarget).parents('tr')[0]).find('#barang_id').val()
    dataBarang.splice(dataBarang.indexOf(idBarang) ,1)
    trTarget.remove()
    const bodyTable = $($("#tablePenerimaanTmp").find('tbody'))
    if (bodyTable.find('tr').length === 0) {
        bodyTable.append(`
            <tr>
                <td colspan="10" class="text-center">Mohon inputkan data.</td>
            </tr>
        `);
        $('#penerimaansupplierform-is_donasi').prop('disabled', false);
    } else {
        // Re init table row number
        bodyTable.find('tr').map((indexRow, itemRow) => {
            $(itemRow).find("#rowNumberSection").html(indexRow + 1)
        })
    }
})
