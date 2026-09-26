var tableSchedule = null;
var { maksFrekuensi, jadwalOperasi, dokterPerujukId, dokterPerujukNama, pendId, diagPenyerta } = phpVars

function fixBackdropMultipleModal() {
    $(`.datetimepicker-custom`).on('click', function () {
        setTimeout(() => {
            $('#modal-lab').css('z-index', '1041')
        }, 10);
    })
    setTimeout(() => {
        $('#modal-lab').css('z-index', '1041')
    }, 10);
}

$(() => {
    removeSessionCache()
    _listpemeriksaanpenunjang = []
    if ($('#instruksipenunjangform-ruangan_id').prop('tagName') == 'SELECT') {
        $('#instruksipenunjangform-ruangan_id').select2()
        $('#instruksipenunjangform-ruangan_id').val($('#instruksipenunjangform-ruangan_id>option:eq(1)').val()).change()
    }

    setTimeout(() => {
        $('#instruksipenunjangform-ruangan_id').trigger('change')
    }, 200);
    var yesterday = new Date((new Date()).valueOf() - 1000 * 60 * 60 * 24);
    $('.pickadate').pickadate({
        format: 'dd/mm/yyyy',
        disable: [
            { from: [0, 0, 0], to: yesterday }
        ]
    });
    $("#lab-form [type='checkbox']").uniform()

    $('#instruksipenunjangform-ruangan_id').on('change', () => {
        if ($('#instruksipenunjangform-ruangan_id').val() != '') {
            if ($('#tmp-ruangan-id').val() != '' && $('#instruksipenunjangform-ruangan_id').val() != $('#tmp-ruangan-id').val() && _listpemeriksaanpenunjang.length) {
                confirmationDialog('Tindakan yang sudah diinput akan dihapus jika merubah ruangan tujuan, yakin akan merubah ruangan tujuan?', (isConfirm) => {
                    if (isConfirm) {
                        $('#btn-reset-pemeriksaan').click()
                    } else {
                        $('#instruksipenunjangform-ruangan_id').val($('#tmp-ruangan-id').val()).trigger('change')
                    }
                })
                return true;
            }
            $('#tmp-ruangan-id').val($('#instruksipenunjangform-ruangan_id').val())
            // $('#btn-tambah-pemeriksaan').attr('disabled', false)
        } else {
            // $('#btn-tambah-pemeriksaan').attr('disabled', true)
            $('#btn-reset-pemeriksaan').click()
        }
    })
    $('#btn-tambah-pemeriksaan').unbind()
    $('#btn-tambah-pemeriksaan').bind('click', () => {
        const { href, width } = $('#btn-tambah-pemeriksaan').data()
        showLoader('Memuat Halaman...')
        $('#modal-order-pemeriksaan').find('.modal-dialog').css('width', width)
        $('#modal-order-pemeriksaan .modal-content').docoLoad({
            url: href.replace('#instalasi_id#', $('#instruksipenunjang-instalasi_id').val()).replace('#ruangan_id#', $('#instruksipenunjangform-ruangan_id').val()).replace('#kelaspelayanan_id#', $('#instruksipenunjang-kelaspelayanan_id').val()).replace('#penjamin_id#', $('#instruksipenunjang-penjamin_id').val()),
            dataType: 'html',
            success: function (data) {
                hideLoader()
                $('#modal-order-pemeriksaan .modal-content').parents('.modal').modal('show')
                // $(`#btn-tambah-pemeriksaan`).attr('disabled', true);
            },
            error: function () {
                hideLoader()
            }
        })
    })

    $('#btn-save-penunjang').bind('click', () => {
        removeSessionCache()
        $('#fretif').remove()
        $('#instruksipenunjangform-frekuensi_terapi').parent('.input-group').removeClass('has-error');
        $('#jalor').remove();
        $('.form-group').removeClass('has-error');
        let _catatanList = ''
        $.each(_listpemeriksaanpenunjang, (k, v) => {
            if (v.catatan !== null) {
                if (_catatanList != '') {
                    _catatanList += '\n'
                }
                _catatanList += v.catatan
            }
        })
        if (_listpemeriksaanpenunjang.length > 0) {
            var currentFrek = $('#instruksipenunjangform-frekuensi_terapi').val();
            if (!_listpemeriksaanpenunjang[0].is_paketfisio) {
                if (parseInt(currentFrek) > parseInt(maksFrekuensi)) {
                    $('#instruksipenunjangform-frekuensi_terapi').parent('.input-group').addClass('has-error');
                    $('#instruksipenunjangform-frekuensi_terapi').parent('.input-group').after(`<span id="fretif" class="help-block error"><i class="fa fa-exclamation-circle"></i>Frekuensi Terapi harus tidak boleh lebih besar dari ${maksFrekuensi}.</span>`);
                    return docoNotification('error', 'Silahkan Cek Inputan', `Frekuensi Terapi harus tidak boleh lebih besar dari ${maksFrekuensi}.`);
                }
            }
            for(let i = 0; i < currentFrek; i++){
                const currentHtml =  $(`#schedule-${i}`);
                const currentAwal =  $(`#jamMulai-${i}`);
                const currentAkhir =  $(`#jamSelesai-${i}`);
                const currentValue = currentHtml.val();
                const currentAwalValue = currentAwal.val();
                const currentAkhirValue = currentAkhir.val();
                if (!currentValue) {
                    currentHtml.parent('.form-control').addClass('has-error');
                    currentHtml.after('<span id="jalor" class="help-block error"><i class="fa fa-exclamation-circle"></i>Jadwal Terapi Tidak Boleh Kosong.</span>');
                    return docoNotification("error", 'Proses Gagal', 'Silahkan Cek Inputan. Jadwal Tidak Boleh Kosong');
                }if(!currentAwalValue){
                    currentAwal.parent('.form-control').addClass('has-error');
                    currentAwal.after('<span id="jalor" class="help-block error"><i class="fa fa-exclamation-circle"></i>Jadwal Terapi Tidak Boleh Kosong.</span>');
                    return docoNotification("error", 'Proses Gagal', 'Silahkan Cek Inputan. Jadwal Tidak Boleh Kosong');
                }if(!currentAkhirValue){
                    currentAkhir.parent('.form-control').addClass('has-error');
                    currentAkhir.after('<span id="jalor" class="help-block error"><i class="fa fa-exclamation-circle"></i>Jadwal Terapi Tidak Boleh Kosong.</span>');
                    return docoNotification("error", 'Proses Gagal', 'Silahkan Cek Inputan. Jadwal Tidak Boleh Kosong');
                }
            }
        }
        let _form = $('#order-penunjang-form').serializeArray()
        _form.push({
            name: 'periksafisio',
            value: JSON.stringify(_listpemeriksaanpenunjang)
        })
        if (_form[_form.findIndex(_obj => _obj.name == 'InstruksiPenunjangForm[catatan_dokterpengirim]')].value != '') {
            _form.push({
                name: 'InstruksiPenunjangForm[catatan]',
                value: _form[_form.findIndex(_obj => _obj.name == 'InstruksiPenunjangForm[catatan_dokterpengirim]')].value
            })
            // _form[_form.findIndex(_obj => _obj.name == 'InstruksiPenunjangForm[catatan_dokterpengirim]')].value += _catatanList != '' ? '\n\n' + _catatanList : '';
        }
        $().docoForm('click', {
            data: _form,
            skipScrollUp: false,
            skipConfirm: true,
            url: '/ranap/pemeriksaan-rawat-inap/simpan-terapi-penunjang-fisio?id='+pendId,
            success: function (data) {
                const { url } = data.response;
                const { cppt_id } = data.response;
                pendaftaran_id = data.response.pendaftaran_id;
                pasienadmisi_id = data.response.pasienadmisi_id;
                pasienkirimkeunitlain_id = data.response.pasienkirimkeunitlain_id;
                if ($('#order-penunjang-form').attr('action').includes('rajal')) {
                    tableCppt.draw()
                } else if ($('#order-penunjang-form').attr('action').includes('ranap')) {
                    tabel.draw()
                } else if ($('#order-penunjang-form').attr('action').includes('igd')) {
                    table_dpjp.draw()
                }
                if (typeof last_cppt != 'undefined' && cppt_id != 'undefined') {
                    last_cppt = cppt_id
                }
                $('#modal-lab').find('.close').click();
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
                        window.open('/ranap/pemeriksaan-rawat-inap/cetak-penunjang-fisio?id='+pendaftaran_id+'&pasienadmisi_id='+pasienadmisi_id+'&pasienkirimkeunitlain_id='+pasienkirimkeunitlain_id);
                    }).on('pnotify.cancel', function () {
                    });
                }
            }
        })
    })

    $('#btn-reset-pemeriksaan').bind('click', () => {
        _listpemeriksaanpenunjang = []
        $('#modal-lab').find('#tbl-order-penunjang tbody').html(`
            <tr>
                <td colspan="8" class="title-empty text-center no-data-row">Belum Ada Data yang terpilih</td>
            </tr>
        `);
        // $('#btn-tambah-pemeriksaan').removeAttr('disabled');
        $('#modal-lab').find(`#instruksipenunjangform-frekuensi_terapi`).val('').attr('readonly', false);
        resetContentSchedule()
    })
    runApplyFrekuensi();

    if (diagPenyerta != null) {
        var decodeDiagnose = null

        var selectedOption = []
        decodeDiagnose = diagPenyerta
        decodeDiagnose.map((diagnose) => {
            if (decodeDiagnose != null) {
                if(diagnose.id != undefined){
                  var newOption = new Option(diagnose.text, `${diagnose.id}_${diagnose.text}`, false, false);
                  $('#instruksipenunjangform-a_diag_penyerta').append(newOption)
                  selectedOption.push(`${diagnose.id}_${diagnose.text}`)
                }else{
                  var newOption = new Option(diagnose.text, `${diagnose.text}`, false, false);
                  $('#instruksipenunjangform-a_diag_penyerta').append(newOption)
                  selectedOption.push(`${diagnose.text}`)
                }
            }
        })
        skipTimeout = true
        $('#instruksipenunjangform-a_diag_penyerta').val(selectedOption).trigger('change')
    }
})

function runApplyFrekuensi() {
    $(`#btn-apply-schedule`).on(`click`, function () {
        handlerBtnApplySchedule()
    })
}

function handlerBtnApplySchedule() {
    resetContentSchedule()
    if (_listpemeriksaanpenunjang.length <= 0) {
        docoNotification('error', 'Silahkan Cek Inputan', `Pemeriksaan harus dipilih terlebih dahulu !`);
        return false;
    }
    const valueFrekuensi = $('#instruksipenunjangform-frekuensi_terapi').val();
    const isPaketFisio = _listpemeriksaanpenunjang[0].is_paketfisio
    const greaterThan = parseInt(valueFrekuensi) > parseInt(maksFrekuensi)
    if (!isPaketFisio & greaterThan) {
        $('#instruksipenunjangform-frekuensi_terapi').parent('.input-group').addClass('has-error');
        $('#instruksipenunjangform-frekuensi_terapi').parent('.input-group').after(`<span id="fretif" class="help-block error"><i class="fa fa-exclamation-circle"></i>Frekuensi Terapi harus tidak boleh lebih besar dari ${maksFrekuensi}.</span>`);
        return false;
    }
    $('#content-schedule').docoLoad({
        url: `/ranap/pemeriksaan-rawat-inap/form-modal-fisio-schedule?frekuensi=${valueFrekuensi}&maks_frekuensi=${maksFrekuensi}`,
        dataType: 'html',
        success: function (data) {
            hideLoader()
            fixBackdropMultipleModal()
        },
        error: function () {
            hideLoader()
            fixBackdropMultipleModal()
        }
    })
}

function resetContentSchedule() {
    $('#content-schedule').html(`
        <div id="content-schedule">
            <table class="table table-hover" id="tbl-schedule" style="width: 100%">
                <thead>
                    <tr class="bg-inverse">
                        <th style='width: 1px'>No</th>
                        <th>Frekuensi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>`)
}

function removeSessionCache() {
    sessionStorage.removeItem('tgl');
    sessionStorage.removeItem('mulai');
    sessionStorage.removeItem('selesai');
    sessionStorage.removeItem('operator');
    sessionStorage.removeItem('anestesi');
}