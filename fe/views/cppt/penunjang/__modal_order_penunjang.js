$(document).ready(function () {
    if (_listpemeriksaanpenunjang.length) {
        $.each(_listpemeriksaanpenunjang, (k, v) => {
            $(`#${v.daftartindakan_id}`).closest('td').addClass('selected')
            $(`#${v.daftartindakan_id}`).prop('checked', true)
        })
    }
    $('.cb_penunjang').closest('td').css('margin-bottom', '8px !important')
    $('.cb_penunjang').uniform()
})

$('.cb_penunjang').on('click', function () {
    callCbPenunjang(this)
})

$('#txt-search').keyup(function (e) {
    if (e.keyCode === 13) {
        if(!$('#btn-search_radlab').is(":disabled")){
          $('#btn-search_radlab').click();
        }
    }
});

/* searching untuk modal */
$('#btn-search_radlab').on('click', function () {
    loadKonten()
})

function loadKonten() {
    var keyword = $('.search-radlab').val();
    var encode_url = encodeURIComponent(keyword);

    var params = 'ruangan_id=' + $('.search-radlab').attr('data-ruangan_id');
    params += '&penjamin_id=' + $('.search-radlab').attr('data-penjamin_id');
    params += '&kelaspelayanan_id=' + $('.search-radlab').attr('data-kelaspelayanan_id');
    params += '&instalasi_id=' + $('.search-radlab').attr('data-instalasi_id');
    params += '&searching=' + encode_url;

    var url = "/igd/pemeriksaan-igd/data-pemeriksaan-penunjang?" + params;
    var _checkKode = $(".check_kode");
    var withKode = (_checkKode.is(':checked')) ? true : false;

    $('.content-radlab').html('');
    var loading = $('#loading-content');
    loading.append('<h1 align="center"><i class="icon-spinner4 spinner position-center"></i>&nbsp;&nbsp;<b>Memuat ... </b></h1>');
    $('#btn-search_radlab').prop('disabled', true);

    $.get(url, function (datax) {
        let html = '';
        var data = JSON.parse(datax);

        if (data && data.length != 0) {
            html += '<div class="col-sm-12">';
            $.each(data, function (header, sub_header) {
                var _title = convertToSlug(header);
                countDetail = sub_header.length;
                html += '<div class="col-sm-4">';
                html += '<div class="panel panel-default">';
                html += '<a id="heading-' + _title + '" data-toggle="collapse" href="#tab-' + _title + '" role="button" aria-expanded="true" aria-controls="tab-' + _title + '" class="">';
                html += '<div class="panel-heading flex-container" style="background-color:#37474f;color:white;">';
                html += '<h6 class="panel-title text-bold" style="font-size:12px;">' + header.toUpperCase() + '</h6>';
                html += '<ul class="icons-list"><li><i id="chevron" class="fa fa-chevron-up"></i></li></ul>';
                html += '</div>';
                html += '</a>';
                html += '<div class="panel-body multi-collpase label-information collapse in" id="tab-' + _title + '" aria-expanded="true">';
                html += '<div class="row">';
                if (countDetail == 0) {
                    html += '<p style="text-align:center;font-weight:bold;">Tidak Ada Data.</p>';
                }
                else {
                  $.each(sub_header, function (key_sub_header, detail_header) {
                      let _sub_title = convertToSlug(key_sub_header);
                      countDetail = detail_header.length;
                      html += '<div class="col-sm-12">';
                      html += '<div class="panel panel-default">';
                      html += '<a id="heading-' + _sub_title + '" data-toggle="collapse" href="#tab-' + _sub_title + '" role="button" aria-expanded="true" aria-controls="tab-' + _sub_title + '" class="">';
                      html += '<div class="panel-heading flex-container" style="background-color:#316C74;color:white;">';
                      html += '<h6 class="panel-title text-bold" style="font-size:12px;">' + key_sub_header.toUpperCase() + '</h6>';
                      html += '<ul class="icons-list"><li><i id="chevron" class="fa fa-chevron-up"></i></li></ul>';
                      html += '</div>';
                      html += '</a>';
                      html += '<div class="panel-body multi-collpase label-information collapse in" id="tab-' + _sub_title + '" aria-expanded="true">';
                      html += '<div class="row">';
                      if (countDetail == 0) {
                          html += '<p style="text-align:center;font-weight:bold;">Tidak Ada Data.</p>';
                      }
                      else {
                        $.each(detail_header, function (key, $detail2) {
                            var _classKode = (withKode === true) ? 'kode-tindakan' : 'kode-tindakan hidden';
                            var _label = `<span class='${_classKode}'>${$detail2.kode} - </span> ${$detail2.daftartindakan_nama}`;

                            html += '<p style="margin-left:10px;margin-top:10px;">';
                            html += '<label>';
                            html += '<input type = "checkbox" id = "' + $detail2.daftartindakan_id + '" class="cb_penunjang"' +
                                ' data-catatan="" ' +
                                ' data-jenis                     ="' + $detail2.jenis + '"' +
                                ' data-tariftindakan_id          ="' + $detail2.tariftindakan_id + '"' +
                                ' data-ruangan_id                ="' + $detail2.ruangan_id + '"' +
                                ' data-ruangan_nama              ="' + $detail2.ruangan_nama + '"' +
                                ' data-instalasi_id              ="' + $detail2.instalasi_id + '"' +
                                ' data-instalasi_nama            ="' + $detail2.instalasi_nama + '"' +
                                ' data-ruanganpaket_id           ="' + $detail2.ruanganpaket_id + '"' +
                                ' data-ruanganpaket_nama         ="' + $detail2.ruanganpaket_nama + '"' +
                                ' data-perdatarif_id             ="' + $detail2.perdatarif_id + '"' +
                                ' data-perdanama_sk              ="' + $detail2.perdanama_sk + '"' +
                                ' data-kelaspelayanan_id         ="' + $detail2.kelaspelayanan_id + '"' +
                                ' data-kelaspelayanan_nama       ="' + $detail2.kelaspelayanan_nama + '"' +
                                ' data-penjamin_id               ="' + $detail2.penjamin_id + '"' +
                                ' data-penjamin_nama             ="' + $detail2.penjamin_nama + '"' +
                                ' data-kelompoktindakan_id       ="' + $detail2.kelompoktindakan_id + '"' +
                                ' data-kelompoktindakan_nama     ="' + $detail2.kelompoktindakan_nama + '"' +
                                ' data-kategoritindakan_id       ="' + $detail2.kategoritindakan_id + '"' +
                                ' data-kategoritindakan_nama     ="' + $detail2.kategoritindakan_nama + '"' +
                                ' data-daftartindakan_id         ="' + $detail2.daftartindakan_id + '"' +
                                ' data-daftartindakan_nama       ="' + $detail2.daftartindakan_nama + '"' +
                                ' data-tipepaket_id              ="' + $detail2.tipepaket_id + '"' +
                                ' data-tipepaket_nama            ="' + $detail2.tipepaket_nama + '"' +
                                ' data-komponentarif_id          ="' + $detail2.komponentarif_id + '"' +
                                ' data-komponentarif_nama        ="' + $detail2.komponentarif_nama + '"' +
                                ' data-harga_tariftindakan       ="' + $detail2.harga_tariftindakan + '"' +
                                ' data-persencyto_tindakan       ="' + $detail2.persencyto_tindakan + '"' +
                                ' data-persendiskon_tindakan     ="' + $detail2.persendiskon_tindakan + '"' +
                                ' data-is_default                ="' + $detail2.is_default + '"' +
                                ' data-is_akomodasi              ="' + $detail2.is_akomodasi + '"' +
                                ' data-carabayar_id              ="' + $detail2.carabayar_id + '"' +
                                ' data-is_konsultasi             ="' + $detail2.is_konsultasi + '"' +
                                ' data-kamarruangan_nokamar      ="' + $detail2.kamarruangan_nokamar + '"' +
                                ' data-kamarruangan_id           ="' + $detail2.kamarruangan_id + '"' +
                                ' data-ambulan_id                ="' + $detail2.ambulan_id + '"' +
                                ' data-no_polisi                 ="' + $detail2.no_polisi + '"' +
                                ' data-kelompokpemeriksaanlab_id ="' + $detail2.kelompokpemeriksaanlab_id + '"' +
                                ' data-nama_kelompok             ="' + $detail2.nama_kelompok + '"' +
                                ' data-jenispemeriksaanlab_id    ="' + $detail2.jenispemeriksaanlab_id + '"' +
                                ' data-jenispemeriksaanlab_nama  ="' + $detail2.jenispemeriksaanlab_nama + '"' +
                                ' data-pemeriksaanlab_id         ="' + $detail2.pemeriksaanlab_id + '"' +
                                ' data-pemeriksaanlab_nama       ="' + $detail2.pemeriksaanlab_nama + '"' +
                                ' data-persen_penyulit           ="' + $detail2.persen_penyulit + '"' +
                                ' data-is_cyto = "0"' +
                                '> &nbsp;&nbsp;' + _label + ' </label></p>'; //
                        });
                    }
                    html += '</div></div></div></div>';
                  });
                }
                html += '</div></div></div></div>';
            });
            html += '</div>';
            loading.empty();
            $('#btn-search_radlab').prop('disabled', false);
            $('.content-radlab').append(html);
            if (_listpemeriksaanpenunjang.length) {
                $.each(_listpemeriksaanpenunjang, (k, v) => {
                    $(`#${v.daftartindakan_id}`).closest('td').addClass('selected')
                    $(`#${v.daftartindakan_id}`).prop('checked', true)
                })
            }

            $('.cb_penunjang').closest('td').css('margin-bottom', '8px !important')
            $('.cb_penunjang').uniform()
            $('.cb_penunjang').on('click', function () {
                callCbPenunjang(this)
            })
        } else {
            loading.empty();
            $('#btn-search_radlab').prop('disabled', false);
            $('.content-radlab').html('<p style="text-align:center">Tidak Ada Data.</p>');
        }
    });
}

var callCbPenunjang = ($this) => {
    let dataPost = []
    let _checkboxData = $($this).data()
    $.each(_checkboxData, (k, v) => {
        if (v == '') {
            _checkboxData[k] = null
        }
        if (k == 'is_cyto') {
            _checkboxData[k] = false
        }
    })
    if ($($this).prop('checked') == true) {
        $($this).closest('td').addClass('selected')
        if (_listpemeriksaanpenunjang.length && _listpemeriksaanpenunjang.find(_obj => _obj.daftartindakan_id == _checkboxData.daftartindakan_id)) {
            return true
        }
        _listpemeriksaanpenunjang.push(_checkboxData)
        let _tarifCyto = parseFloat(_checkboxData.harga_tariftindakan) * (parseFloat(_checkboxData.persencyto_tindakan) / 100)
        let _catatanField = ``
        if (!$('#modal-lab').find('.modal-body').hasClass('form-modal-bedah')) {
            _catatanField = `
                <td>
                   <textarea rows="3" maxlength="100" class="form-control pemeriksaan-catatan-text" data-daftartindakan_id='${_checkboxData.daftartindakan_id}' />
                </td>
          `
        }
        let _html = `
       <tr class='order-row-${_checkboxData.daftartindakan_id}'>
                <td></td>
                <td>${_checkboxData.jenispemeriksaanlab_nama}</td>
                <td>${_checkboxData.daftartindakan_nama}</td>
                ${_catatanField}
                <td class="text-right" style="display:none">Rp. ${docoHelper.convertToRupiah(_checkboxData.harga_tariftindakan)}</td>
                <td class="text-center">
                   <input type='checkbox' class='check-uniform update-cyto' data-daftartindakan_id='${_checkboxData.daftartindakan_id}'>
                </td>
                <td class="text-right col-harga-cyto" style="display:none">Rp. 0</td>
                <td class="text-right col-total-harga" style="display:none">${'Rp. ' + docoHelper.convertToRupiah(_checkboxData.harga_tariftindakan)}</td>
                <td class="text-right">
                   <button style="margin-bottom: 5px; margin-top: 0" class='btn btn-danger btn-xs btn-delete-item' data-daftartindakan_id='${_checkboxData.daftartindakan_id}' type='button'><i class='fa fa-trash'></i></button>
                </td>
       </tr>`
        $('#modal-lab').find('#tbl-order-penunjang tbody').find('.no-data-row').remove()
        $('#modal-lab').find('#tbl-order-penunjang tbody').append(_html)
        updateNumbering()
        $(`.order-row-${_checkboxData.daftartindakan_id}`).find('.check-uniform').uniform()

        $(`.order-row-${_checkboxData.daftartindakan_id}`).find('.update-cyto').bind('click', ({ delegateTarget }) => {
            const { daftartindakan_id } = $(delegateTarget).data()
            let _index = _listpemeriksaanpenunjang.findIndex(_obj => _obj.daftartindakan_id == daftartindakan_id)
            _tarifCyto = 0
            _totalTarif = parseFloat(_listpemeriksaanpenunjang[_index].harga_tariftindakan)
            _listpemeriksaanpenunjang[_index].is_cyto = $(delegateTarget).prop('checked')
            if ($(delegateTarget).prop('checked')) {
                _tarifCyto = _totalTarif * (parseFloat(_listpemeriksaanpenunjang[_index].persencyto_tindakan) / 100)
                _totalTarif += _tarifCyto
            }
            $('#modal-lab').find('#tbl-order-penunjang tbody').find(`.order-row-${daftartindakan_id}`).find('.col-harga-cyto').html(`Rp. ${docoHelper.convertToRupiah(_tarifCyto)}`)
            $('#modal-lab').find('#tbl-order-penunjang tbody').find(`.order-row-${daftartindakan_id}`).find('.col-total-harga').html(`Rp. ${docoHelper.convertToRupiah(parseFloat(_totalTarif))}`)
            orderSummary()
        })
        $(`.order-row-${_checkboxData.daftartindakan_id}`).find('.pemeriksaan-catatan-text').bind('change', ({ delegateTarget }) => {
            const { daftartindakan_id } = $(delegateTarget).data()
            let _index = _listpemeriksaanpenunjang.findIndex(_obj => _obj.daftartindakan_id == daftartindakan_id)
            _listpemeriksaanpenunjang[_index].catatan = $(delegateTarget).val() != '' ? _listpemeriksaanpenunjang[_index].daftartindakan_nama + ': ' + $(delegateTarget).val() : null
        })

        $(`.order-row-${_checkboxData.daftartindakan_id}`).find('.btn-delete-item').bind('click', ({ delegateTarget }) => {
            const { daftartindakan_id } = $(delegateTarget).data()
            let _index = _listpemeriksaanpenunjang.findIndex(_obj => _obj.daftartindakan_id == daftartindakan_id)
            _listpemeriksaanpenunjang.splice(_index, 1)
            $('#modal-lab').find('#tbl-order-penunjang tbody').find(`.order-row-${daftartindakan_id}`).remove()
            if (_listpemeriksaanpenunjang.length == 0) {
                $('#modal-lab').find('#tbl-order-penunjang tbody').append(`
                   <tr class="nd-row">
                      <td colspan="5" class="text-center no-data-row">Belum Ada Data yang Diinputkan</td>
                   </tr>
                `)
            } else {
                updateNumbering()
            }
            orderSummary()
            validasiClosePopup()
        })
        validasiClosePopup()
    } else {
        let _index = _listpemeriksaanpenunjang.findIndex(obj => obj.daftartindakan_id == _checkboxData.daftartindakan_id)
        _listpemeriksaanpenunjang.splice(_index, 1)
        $('#modal-lab').find('#tbl-order-penunjang tbody').find(`.order-row-${_checkboxData.daftartindakan_id}`).remove()
        if (_listpemeriksaanpenunjang.length == 0) {
            $('#modal-lab').find('#tbl-order-penunjang tbody').append(`
                <tr class="nd-row">
                   <td colspan="5" class="text-center no-data-row">Belum Ada Data yang Diinputkan</td>
                </tr>
          `)
        } else {
            updateNumbering()
        }
        validasiClosePopup()
    }
    orderSummary()
    return true
}

var orderSummary = () => {
    let _total = 0
    $.each(_listpemeriksaanpenunjang, (k, v) => {
        let _tarif = parseFloat(v.harga_tariftindakan)
        _tarifCyto = v.is_cyto ? _tarif * (parseFloat(v.persencyto_tindakan) / 100) : 0
        _totalTarif = v.is_cyto ? _tarif + _tarifCyto : _tarif
        _total += _totalTarif
    })
    $('#modal-lab').find('#tbl-order-penunjang tfoot').find('.order-summary').html(`<b>Rp. ${docoHelper.convertToRupiah(_total)}</b>`)
}

var updateNumbering = () => {
    $('#tbl-order-penunjang > tbody > tr').each(function (i, val) {
        $('td:first', this).text(i);
    });
}

var convertToSlug = (text) => {
    return text.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
}

$(".check_kode").on('change', function () {
    if (!$(this).is(':checked')) {
        $('.kode-tindakan').addClass('hidden');
    }
    else {
        $('.kode-tindakan').removeClass('hidden');
    }
})

/* FUNGSI VALIDASI CLOSE POP UP */
function validasiClosePopup() {
    var tableOrderPenunjang2 = $("#tbl-order-penunjang tbody tr").not('.nd-row').length;

    if(tableOrderPenunjang2 > 0) {
        var isUpdate = true;
    } else {
        var isUpdate = false;
    }

    if(isUpdate == true) {
        $('.close-modal-jadwal').attr('data-dismiss-confirmation', 'modal');
        $('.close-modal-jadwal').removeAttr('data-dismiss');
    } else {
        $('.close-modal-jadwal').removeAttr('data-dismiss-confirmation');
        $('.close-modal-jadwal').attr('data-dismiss', 'modal');
    }
}