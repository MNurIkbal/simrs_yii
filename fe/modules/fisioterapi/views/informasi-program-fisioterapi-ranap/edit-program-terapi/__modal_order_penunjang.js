var getAllOption = $('[id=parentOption]'); //dapetin semua parentOption untuk perbandingan nanti
var DatakonfigApproveFisio = 'false';

$(document).ready(function () {
    var {konfigApproveFisio } = phpVars
    DatakonfigApproveFisio =konfigApproveFisio
    if (_listpemeriksaanpenunjang.length) {
        $.each(_listpemeriksaanpenunjang, (k, v) => {
            if(v.is_paketfisio == false){
                $(`#${v.daftartindakan_id}`).closest('td').addClass('selected')
                $(`#${v.daftartindakan_id}`).prop('checked', true)
            }
            if(v.is_paketfisio == true){
                var index = $(`#${v.parentdaftartindakan_id+'-'+v.daftartindakan_id}`).data('index');
                $('.cb_penunjang')[index].click();
            }
        })
    }
    // Todo : Di-comment, setiap on shown modal, data yang sebelumnya kehapus 1
    // if (_listpemeriksaanpenunjang.length > 0) {
    //     $.each(_listpemeriksaanpenunjang, function (index, value) {
    //         $('.cb_penunjang')[value.index].click();
    //     });
    // }
    $('.cb_penunjang').closest('td').css('margin-bottom', '8px !important')
    $('.cb_penunjang').uniform()
})

$('#modal-order-pemeriksaan').on('shown.bs.modal', function (e) {
    $('#modal-lab').css('z-index', '1040');
})

$('#modal-order-pemeriksaan').on('hidden.bs.modal', function () {
    $('#modal-lab').find('#btn-tambah-pemeriksaan').removeAttr('disabled');
    // if (_listpemeriksaanpenunjang.length <= 0) {
    //     $('#modal-lab').find('#btn-tambah-pemeriksaan').removeAttr('disabled');
    //     return false;
    // }
    // $('#modal-lab').find('#btn-tambah-pemeriksaan').attr('disabled', true);
    let isPaketFisio = false;
    _listpemeriksaanpenunjang.map(function (item) {
        if (!isPaketFisio) isPaketFisio = item?.is_paketfisio
    })
    if (isPaketFisio) {
        const paketFisioJumlah = _listpemeriksaanpenunjang[0]?.paketfisio_jumlah
        if (_listpemeriksaanpenunjang.length == paketFisioJumlah) {
            $('#modal-lab').find('#btn-save-penunjang').attr('disabled', false);
        } else {
            $('#modal-lab').find('#btn-tambah-pemeriksaan').removeAttr('disabled');
            $('#modal-lab').find('#btn-save-penunjang').attr('disabled', true);
        }
    }
})

$('.cb_penunjang').on('click', function () {
    // get current checked length dari this parents
    var checked = $(this).parents('#parentOption').find('input:checkbox').filter(':checked').length;
    // kalau lengthnya > 0 akan dicari checkbox mana yg harus di disabled
    const jumlahBatas = $(this).attr('data-paketfisio_jumlah');
    const jumlahFrekuensi = $(this).attr('data-paketfisio_frekuensi');
    const batas = jumlahBatas !== undefined ? jumlahBatas : 0;
    if (checked > 0) {
        if ($(this).attr('data-parentdaftartindakan_id') !== undefined) {
            $('#modal-lab').find('#btn-save-penunjang').attr('disabled', true);
            for (var i = 0; i < getAllOption.length; i++) {
                var child = $(getAllOption[i]).find('.' + this.className);
                for (var y = 0; y < child.length; y++) {
                    //ketika data parentnya tidak sesuai maka checkbox akan disabled
                    if ($(this).attr('data-parentdaftartindakan_id') != $(child[y]).attr('data-parentdaftartindakan_id')) {
                        if ($(child[y]).prop('checked') == true) {
                            $(child[y]).prop('checked', false);
                            $('#btn-reset-pemeriksaan').click();
                        }
                        $(child[y]).attr('disabled', true);
                    } else {
                        if (checked >= batas) {
                            if ($(child[y]).prop('checked') == false) {
                                $(child[y]).attr('disabled', true);
                            }
                            $('#modal-lab').find('#btn-save-penunjang').attr('disabled', false);
                            $('#modal-lab').find(`#orderpenunjangform-frekuensi_terapi`).val(jumlahFrekuensi).attr('readonly', true);
                        } else {
                            $(child[y]).attr('disabled', false);
                            $('#modal-lab').find('#btn-save-penunjang').attr('disabled', true);
                        }

                    }
                }
            }
        }
        // kalau lengthnya < 0 maka lepas disabled checkbox
    } else {
        $('#modal-lab').find('#btn-save-penunjang').attr('disabled', false);
        $('#modal-lab').find(`#orderpenunjangform-frekuensi_terapi`).val('').attr('readonly', false);
        for (var i = 0; i < getAllOption.length; i++) {
            var child = $(getAllOption[i]).find('.' + this.className);
            for (var y = 0; y < child.length; y++) {
                if ($(this).attr('data-parentdaftartindakan_id') != $(child[y]).attr('data-parentdaftartindakan_id')) {
                    $(child[y]).attr('disabled', false);
                } else {
                    $(child[y]).attr('disabled', false);
                }
            }
        }
    }
    callCbPenunjang(this);
    $('.cb_penunjang').closest('td').css('margin-bottom', '8px !important')
    $('.cb_penunjang').uniform();
})

$('#txt-search').keyup(function (e) {
    if (e.keyCode === 13) {
        // $('#btn-search_radlab').click()
    }
});

var callCbPenunjang = ($this) => {
    // Old Code
    let dataPost = []
    let _checkboxData = $($this).data();
    var deleted_button = null
    $.each(_checkboxData, (k, v) => {
        if (v == '') {
            _checkboxData[k] = null
        }
        if (k == 'is_cyto') {
            _checkboxData[k] = false
        }
        if (k == 'is_approve_edit') {
            _checkboxData[k] = false
        }
    })
    if ($($this).prop('checked') == true) {
        $($this).closest('td').addClass('selected');
        if (_listpemeriksaanpenunjang.length && _listpemeriksaanpenunjang.find(_obj => _obj.daftartindakan_id == _checkboxData.daftartindakan_id)) {
            return true
        }
        _listpemeriksaanpenunjang.push(_checkboxData);
        let _catatanField = ``
        if (!$('#modal-lab').find('.modal-body').hasClass('form-modal-bedah')) {
            _catatanField = `
                <td>
                   <textarea rows="3" maxlength="100" class="form-control pemeriksaan-catatan-text" data-daftartindakan_id='${_checkboxData.daftartindakan_id}' />
                </td>
          `
        }

        if (DatakonfigApproveFisio == 'false') {
            deleted_button = 
            `
                <td class="text-right">
                   <button style="margin-bottom: 5px; margin-top: 0" class='btn btn-danger btn-xs btn-delete-item' data-daftartindakan_id='${_checkboxData.daftartindakan_id}' type='button'><i class='fa fa-trash'></i></button>
                </td>
            `
        } else {
            deleted_button = 
            `
                <td class="text-right">
                   <button style="margin-bottom: 5px; margin-top: 0" class='btn btn-danger btn-xs btn-delete-item' data-daftartindakan_id='${_checkboxData.daftartindakan_id}' type='button'><i class='fa fa-trash'></i></button>
                   <br><span> Menunggu Approval </span>
                </td>
            `
        }
        let _html = `
            <tr class='order-row-${_checkboxData.daftartindakan_id}'>
                <td></td>
                <td>${_checkboxData.jenispemeriksaanlab_nama}</td>
                <td>${_checkboxData.daftartindakan_nama}</td>
                ${_catatanField}
                <td class="text-right" style="display:none"></td>
                <td class="text-center" style="display:none">
                   <input type='checkbox' class='check-uniform update-cyto' data-daftartindakan_id='${_checkboxData.daftartindakan_id}'>
                </td>
                <td class="text-right col-harga-cyto" style="display:none"></td>
                <td class="text-right col-total-harga" style="display:none"></td>
                ${deleted_button}
            </tr>`
        $('#modal-lab').find('#tbl-order-penunjang tbody').find('.no-data-row').parent().remove()
        $('#modal-lab').find('#tbl-order-penunjang tbody').append(_html)
        updateNumbering()

        $(`.order-row-${_checkboxData.daftartindakan_id}`).find('.check-uniform').uniform()

        $(`.order-row-${_checkboxData.daftartindakan_id}`).find('.pemeriksaan-catatan-text').bind('change', ({ delegateTarget }) => {
            const { daftartindakan_id } = $(delegateTarget).data()
            let _index = _listpemeriksaanpenunjang.findIndex(_obj => _obj.daftartindakan_id == daftartindakan_id)
            _listpemeriksaanpenunjang[_index].catatan = $(delegateTarget).val() != '' ? _listpemeriksaanpenunjang[_index].daftartindakan_nama + ': ' + $(delegateTarget).val() : null
        })

        $(`.order-row-${_checkboxData.daftartindakan_id}`).find('.btn-delete-item').bind('click', ({ delegateTarget }) => {
            const { daftartindakan_id } = $(delegateTarget).data()
            let _index = _listpemeriksaanpenunjang.findIndex(_obj => _obj.daftartindakan_id == daftartindakan_id)
            _listpemeriksaanpenunjang.splice(_index, 1);
            $('#modal-lab').find('#tbl-order-penunjang tbody').find(`.order-row-${daftartindakan_id}`).remove()
            if (_listpemeriksaanpenunjang.length <= 0) {
                $('#modal-lab').find(`#orderpenunjangform-frekuensi_terapi`).val('').attr('readonly', false);
                $('#modal-lab').find('#btn-save-penunjang').attr('disabled', false);
                $('#modal-lab').find('#btn-tambah-pemeriksaan').attr('disabled', false);
                $('#modal-lab').find('#tbl-order-penunjang tbody').append(`
                    <tr>
                        <td colspan="9" class="title-empty text-center no-data-row">Belum Ada Data yang terpilih</td>
                    </tr>
                `)
            } else {
                let isPaketFisio = false;
                _listpemeriksaanpenunjang.map(function (item) {
                    if (!isPaketFisio) isPaketFisio = item?.is_paketfisio
                })
                if (isPaketFisio) {
                    const paketFisioJumlah = _listpemeriksaanpenunjang[0]?.paketfisio_jumlah
                    if (_listpemeriksaanpenunjang.length == paketFisioJumlah) {
                        $('#modal-lab').find('#btn-save-penunjang').attr('disabled', false);
                    } else {
                        $('#modal-lab').find('#btn-save-penunjang').attr('disabled', true);
                        // $('#modal-lab').find('#btn-tambah-pemeriksaan').attr('disabled', false);
                        $('#modal-lab').find('#btn-tambah-pemeriksaan').removeAttr('disabled');
                    }
                }
                updateNumbering()
            }
        })
    } else {
        let _index = _listpemeriksaanpenunjang.findIndex(obj => obj.daftartindakan_id == _checkboxData.daftartindakan_id)
        _listpemeriksaanpenunjang.splice(_index, 1)
        $('#modal-lab').find('#tbl-order-penunjang tbody').find(`.order-row-${_checkboxData.daftartindakan_id}`).remove()
        if (_listpemeriksaanpenunjang.length <= 0) {
            $('#modal-lab').find('#tbl-order-penunjang tbody').html(`
                <tr>
                    <td colspan="9" class="title-empty text-center no-data-row">Belum Ada Data yang terpilih</td>
                </tr>
          `)
        } else {
            updateNumbering()
        }
    }
    return true
}

var updateNumbering = () => {
    $('#tbl-order-penunjang > tbody > tr').each(function (i, val) {
        $('td:first', this).text(i + 1);
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