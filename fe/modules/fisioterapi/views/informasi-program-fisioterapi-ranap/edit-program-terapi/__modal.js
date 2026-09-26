var _listpemeriksaanpenunjang = [];
var isAktif = true;
var isDeleted = false;
var isPaket = false;
var isChildDeleted = false;
var DatakonfigApproveFisio = 'false';
$(() => {
    removeSessionCache()
    var { choosedDatas, diagPenyerta, konfigApproveFisio } = phpVars
    DatakonfigApproveFisio = konfigApproveFisio
    $('.select2-container--krajee').removeClass('select2-hidden-accessible');
    $('.select2-container--default').addClass('select2-hidden-accessible');
    _listpemeriksaanpenunjang = choosedDatas;
    if (_listpemeriksaanpenunjang.length) {
        $.each(_listpemeriksaanpenunjang, (k, v) => {
            if(v.is_paketfisio == true){
                $('#modal-lab').find(`#orderpenunjangform-frekuensi_terapi`).attr('readonly', true);
                isAktif = v.is_aktif;
                isDeleted = v.is_deleted;
                isPaket = v.is_paketfisio;
                if(v.is_deleted_detail == true){
                    isChildDeleted = v.is_deleted_detail;
                }
            }
        });
        if(isAktif == false || isDeleted == true || isChildDeleted == true){
            if(isPaket == true){
                tindakanIsDeleted(this);
            }
        }
    }

    function tindakanIsDeleted($this){
        $(`#btn-tambah-pemeriksaan`).attr('disabled', true);
        $('#wrap-pemeriksaan').after('<p class="text-error" id="tindakan-terlarang" style="color: red; padding-left:20px">Tindakan sudah tidak aktif / sudah di hapus, silahkan kosongkan terlebih dahulu sebelum mengganti tindakan</p>');
    }

    var yesterday = new Date((new Date()).valueOf() - 1000 * 60 * 60 * 24);
    $('.pickadate').pickadate({
        format: 'dd/mm/yyyy',
        disable: [
            { from: [0, 0, 0], to: yesterday }
        ]
    });
    $("#lab-form [type='checkbox']").uniform()
    $('#btn-tambah-pemeriksaan').unbind()
    $('#btn-tambah-pemeriksaan').bind('click', () => {
        if(isAktif == false || isDeleted == true || isChildDeleted == true){
            if(_listpemeriksaanpenunjang.length > 0 ){
                $(`#btn-tambah-pemeriksaan`).attr('disabled', true);
                return false;
            }
        }
        const { href, width } = $('#btn-tambah-pemeriksaan').data()
        showLoader('Memuat Halaman...')
        $('#modal-order-pemeriksaan').find('.modal-dialog').css('width', width)
        $('#modal-order-pemeriksaan .modal-content').docoLoad({
            url: href.replace('#instalasi_id#', $('#instruksipenunjang-instalasi_id').val()).replace('#ruangan_id#', $('#instruksipenunjangform-ruangan_id').val()).replace('#kelaspelayanan_id#', $('#instruksipenunjang-kelaspelayanan_id').val()).replace('#penjamin_id#', $('#instruksipenunjang-penjamin_id').val()),
            dataType: 'html',
            success: function (data) {
                hideLoader()
                $('#modal-order-pemeriksaan .modal-content').parents('.modal').modal('show')
                $(`#btn-tambah-pemeriksaan`).attr('disabled', true);
            },
            error: function () {
                hideLoader()
            }
        })
    })

    $('#btn-save-penunjang').bind('click', () => {
        removeSessionCache()
        let _catatanList = ''
        $.each(_listpemeriksaanpenunjang, (k, v) => {
            if (v.catatan !== null) {
                if (_catatanList != '') {
                    _catatanList += '\n'
                }
                _catatanList += v.catatan
            }
        })
        let _form = $('#order-penunjang-form').serializeArray()
        _form.push({
            name: 'periksalab',
            value: JSON.stringify(_listpemeriksaanpenunjang)
        })
        $().docoForm('click', {
            data: _form,
            skipScrollUp: false,
            url: $('#order-penunjang-form').attr('action'),
            success: function (data) {
                const { url } = data.response;
                const { cppt_id } = data.response;
                $('#modal-lab').find('.close').click();
                table.draw();
                if (_listpemeriksaanpenunjang.length && url != null && url.length > 0) {
                    // Pnotify
                    PNotify.prototype.options.styling = "bootstrap3";
                    (new PNotify({
                        title: "Berhasil",
                        text: "Data berhasil disimpan, apakah Anda ingin melakukan cetak?",
                        addclass: "alert alert-success alert-arrow-right alert-styled-right",
                        type: "success",
                        buttons: {
                            closer: false,
                            sticker: false
                        },
                        hide: false,
                        confirm: {
                            confirm: true
                        },
                        history: {
                            history: false
                        }
                    })).get().on('pnotify.confirm', function () {
                        // Print
                        window.open(url);
                    }).on('pnotify.cancel', function () {
                    });
                }
            }
        })
    })

    $('#btn-reset-pemeriksaan').bind('click', () => {
        $('#tindakan-terlarang').remove();
        _listpemeriksaanpenunjang = []
        $('#modal-lab').find('#tbl-order-penunjang tbody').html(`
            <tr>
                <td colspan="9" class="title-empty text-center no-data-row">Belum Ada Data yang terpilih</td>
            </tr>
        `);
        $('#modal-lab').find('#tbl-order-penunjang tfoot').html(`
            <tr>
                <td>&nbsp;</td>
                <td colspan="5" class="text-bold">TOTAL</td>
                <td class="text-right text-bold order-summary">Rp. 0</td>
                <td></td>
            </tr>
        `);
        $('#btn-tambah-pemeriksaan').removeAttr('disabled');
        if(isPaket == true){
            $('#modal-lab').find('#btn-save-penunjang').attr('disabled', true);
            $('#modal-lab').find(`#orderpenunjangform-frekuensi_terapi`).val('').attr('readonly', false);
        }else{
            $('#modal-lab').find('#btn-save-penunjang').attr('disabled', false);
        }
    })

    refreshTableData();

    if (diagPenyerta != null) {
        var decodeDiagnose = null
    
        var selectedOption = []
        decodeDiagnose = diagPenyerta
        decodeDiagnose.map((diagnose) => {
            if (decodeDiagnose != null) {
                if(diagnose.id != undefined){
                  var newOption = new Option(diagnose.text, `${diagnose.id}_${diagnose.text}`, false, false);
                  $('#orderpenunjangform-a_diag_penyerta').append(newOption)
                  selectedOption.push(`${diagnose.id}_${diagnose.text}`)
                }else{
                  var newOption = new Option(diagnose.text, `${diagnose.text}`, false, false);
                  $('#orderpenunjangform-a_diag_penyerta').append(newOption)
                  selectedOption.push(`${diagnose.text}`)
                }
            }
        })
        skipTimeout = true
        $('#orderpenunjangform-a_diag_penyerta').val(selectedOption).trigger('change')
    }
})

function refreshTableData() {
    _listpemeriksaanpenunjang.map(function (item) {
        appendRow(item);
    })
}

function appendRow(currentData) {
    const catatanChild = currentData.catatan ? currentData.catatan : '';
    var color = 'black';
    var strikeout = ''
    var button_hapus = ''

    if(currentData.is_aktif == false || currentData.is_deleted == true || currentData.is_deleted_detail == true){
        color = 'red';
    }
    let _catatanField = `
        <td>
            <textarea rows="3" maxlength="100" class="form-control pemeriksaan-catatan-text" data-daftartindakan_id='${currentData.daftartindakan_id}'>${catatanChild}</textarea>
        </td>
        `
    
    if (DatakonfigApproveFisio == 'false') {
        button_hapus = `
        <td class="text-right">
            <button style="margin-bottom: 5px; margin-top: 0" class='btn btn-danger btn-xs btn-delete-item' data-daftartindakan_id='${currentData.daftartindakan_id}' type='button'><i class='fa fa-trash'></i></button>
        </td>`
    }  else if (currentData.programterapi_deleted == true) {
        strikeout = 'strikeout';
        button_hapus = `
            <td class="text-right">
                <span> Menunggu Approval </span>
            </td>`
    } else if (currentData.is_approve_edit == false) {
        button_hapus = `
        <td class="text-right">
            <button style="margin-bottom: 5px; margin-top: 0" class='btn btn-danger btn-xs btn-delete-item' data-daftartindakan_id='${currentData.daftartindakan_id}' type='button'><i class='fa fa-trash'></i></button><br>
            <span> Menunggu Approval </span>
        </td>`
    } else {
        button_hapus = `
        <td class="text-right">
            <button style="margin-bottom: 5px; margin-top: 0" class='btn btn-danger btn-xs btn-delete-item' data-daftartindakan_id='${currentData.daftartindakan_id}' type='button'><i class='fa fa-trash'></i></button>
        </td>`
    }
    
    let _html = `
    <tr style='color: ${color}' class='order-row-${currentData.daftartindakan_id} ${strikeout}'>
        <td></td>
        <td>${currentData.jenispemeriksaanlab_nama}</td>
        <td>${currentData.daftartindakan_nama}</td>
        ${_catatanField}
        <td class="text-right" style="display:none"></td>
        <td class="text-center" style="display:none">
            <input type='checkbox' class='check-uniform update-cyto' data-daftartindakan_id='${currentData.daftartindakan_id}'>
        </td>
        <td class="text-right col-harga-cyto" style="display:none"></td>
        <td class="text-right col-total-harga" style="display:none"></td>
        ${button_hapus}
    </tr>`
    $('#modal-lab').find('#tbl-order-penunjang tbody').find('.no-data-row').parent().remove()
    $('#modal-lab').find('#tbl-order-penunjang tbody').append(_html);
    $(`.order-row-${currentData.daftartindakan_id}`).find('.check-uniform').uniform()
    $(`.order-row-${currentData.daftartindakan_id}`).find('.check-uniform').uniform()
    $(`.order-row-${currentData.daftartindakan_id}`).find('.pemeriksaan-catatan-text').bind('change', ({ delegateTarget }) => {
        const { daftartindakan_id } = $(delegateTarget).data()
        let _index = _listpemeriksaanpenunjang.findIndex(_obj => _obj.daftartindakan_id == daftartindakan_id)
        _listpemeriksaanpenunjang[_index].catatan = $(delegateTarget).val() != '' ? _listpemeriksaanpenunjang[_index].daftartindakan_nama + ': ' + $(delegateTarget).val() : null
    })
    $(`.order-row-${currentData.daftartindakan_id}`).find('.btn-delete-item').bind('click', ({ delegateTarget }) => {
        const { daftartindakan_id } = $(delegateTarget).data()
        let _index = _listpemeriksaanpenunjang.findIndex(_obj => _obj.daftartindakan_id == daftartindakan_id)
        if (DatakonfigApproveFisio == 'false') {
            _listpemeriksaanpenunjang.splice(_index, 1);

        }
        $('#modal-lab').find('#tbl-order-penunjang tbody').find(`.order-row-${daftartindakan_id}`).remove()

        if (DatakonfigApproveFisio == 'true') {
            let _html = `
                <tr style='color: ${color}' class='order-row-${currentData.daftartindakan_id} strikeout'>
                    <td></td>
                    <td>${currentData.jenispemeriksaanlab_nama}</td>
                    <td>${currentData.daftartindakan_nama}</td>
                    ${_catatanField}
                    <td class="text-right" style="display:none"></td>
                    <td class="text-center" style="display:none">
                        <input type='checkbox' class='check-uniform update-cyto' data-daftartindakan_id='${currentData.daftartindakan_id}'>
                    </td>
                    <td class="text-right col-harga-cyto" style="display:none"></td>
                    <td class="text-right col-total-harga" style="display:none"></td>
                    <td class="text-right">
                        <span> Menunggu Approval </span>
                    </td>
                </tr>`
                $('#modal-lab').find('#tbl-order-penunjang tbody').find('.no-data-row').parent().remove()
                $('#modal-lab').find('#tbl-order-penunjang tbody').append(_html);
                _listpemeriksaanpenunjang[_index].programterapi_deleted = true
        }
            
        if (_listpemeriksaanpenunjang.length <= 0) {
            $('#tindakan-terlarang').remove();
            $('#modal-lab').find(`#orderpenunjangform-frekuensi_terapi`).val('').attr('readonly', false);
            if(isPaket == true){
                $('#modal-lab').find('#btn-save-penunjang').attr('disabled', true);
            }else{
                $('#modal-lab').find('#btn-save-penunjang').attr('disabled', false);
            }
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
                    if(isAktif == false || isDeleted == true || isChildDeleted == true){
                        $('#modal-lab').find('#btn-save-penunjang').attr('disabled', true);
                    }else{
                        $('#modal-lab').find('#btn-save-penunjang').attr('disabled', false);
                    }
                } else {
                    $('#modal-lab').find('#btn-save-penunjang').attr('disabled', true);
                    if(isAktif == false || isDeleted == true || isChildDeleted == true){
                        $(`#btn-tambah-pemeriksaan`).attr('disabled', true);
                    }else{
                        $('#modal-lab').find('#btn-tambah-pemeriksaan').removeAttr('disabled');
                    }
                }
            }
            updateNumbering()
        }
    })
    updateNumbering()
}

function updateNumbering() {
    $('#tbl-order-penunjang > tbody > tr').each(function (i, val) {
        $('td:first', this).text(i + 1);
    });
}

function removeSessionCache() {
    sessionStorage.removeItem('tgl');
    sessionStorage.removeItem('mulai');
    sessionStorage.removeItem('selesai');
    sessionStorage.removeItem('operator');
    sessionStorage.removeItem('anestesi');
}