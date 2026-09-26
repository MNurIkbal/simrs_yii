$(() => {
    if(_orderBedahTanpaTindakan && _instalasiId == _instalasiBedah){
        $('#btn-reset-pemeriksaan').prop('disabled', true);
    }
    sessionStorage.removeItem('tgl');
    sessionStorage.removeItem('mulai');
    sessionStorage.removeItem('selesai');
    sessionStorage.removeItem('operator');
    sessionStorage.removeItem('anestesi');
    _listpemeriksaanpenunjang = []
    $('#instruksipenunjangform-ruangan_id').select2()
    $('#instruksipenunjangform-ruangan_id').val($('#instruksipenunjangform-ruangan_id>option:eq(1)').val()).change()
    $('#instruksipenunjangform-pegawai_id').docoPaginationSelec2(
        config = {
            placeholder : 'Pilih Dokter ... ',
            _api : $('#instruksipenunjangform-pegawai_id').data('api'),
            ajax : {
                data: function(params) {
                    return {
                        q: params.term,
                        page: params.page || 1,
                    }
                },
                results: function (data, params) {
                    var more = (params.page * 30) < data.total_count;
                    return { results: data.items, more: more };
                },
                processResults: function(res, params) {
                    params.page = params.page || 1;
                    var arr = [];
                    $.each(res.data, function(index, value) {
                        arr.push({
                            id: value.pegawai_id,
                            text: value.nama_pegawai,
                        })
                    });
                    return {
                        results: arr,
                        pagination: {
                            more: res.data.length >= 10
                        }
                    };
                }
            }
        }
    )

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

    $("#diagnosa_penyerta").tagsinput({
        delimiter: '|',  // avoid comma value
    })

    // set - jika diagnosa penyerta tidak ada, diakalin pake placeholder input
    if(_diagnosaPenyerta.length <= 0){
        $('.field-diagnosa_penyerta .bootstrap-tagsinput input').attr('placeholder', '-');
    }

    $.each(_diagnosaPenyerta, function(index, value) {
        $('#diagnosa_penyerta').tagsinput('add', value.text);
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
            if(_orderBedahTanpaTindakan && _instalasiId == _instalasiBedah){
                $('#btn-tambah-pemeriksaan').prop('disabled', true);
            }else{
                $('#btn-tambah-pemeriksaan').attr('disabled', false)
            }
            if (!$('.panel-jadwal-operasi').hasClass('hidden')) {
                $('.btn-buka-jadwal').attr('disabled', false)
            }
        } else {
            if (!$('.panel-jadwal-operasi').hasClass('hidden')) {
                $('.btn-buka-jadwal').attr('disabled', true)
            }
            $('#btn-tambah-pemeriksaan').attr('disabled', true)
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
            },
            error: function () {
                hideLoader()
            }
        })
    })

    $('.btn-buka-jadwal').unbind()
    $('.btn-buka-jadwal').bind('click', () => {
        const { href, width } = $('.btn-buka-jadwal').data()
        showLoader('Memuat Halaman...')
        $('#modal-jadwal-dokter').find('.modal-dialog').css('width', width)
        $('#modal-jadwal-dokter .modal-content').docoLoad({
            url: href,
            dataType: 'html',
            success: function (data) {
                hideLoader()
                $('#modal-jadwal-dokter .modal-content').parents('.modal').modal('show')
            },
            error: function () {
                hideLoader()
            }
        })
    })

    $('#btn-save-penunjang').bind('click', () => {
        sessionStorage.removeItem('tgl');
        sessionStorage.removeItem('mulai');
        sessionStorage.removeItem('selesai');
        sessionStorage.removeItem('operator');
        sessionStorage.removeItem('anestesi');
        // if( !_listpemeriksaanpenunjang.length && $('#instruksipenunjangform-is_rujukan').is(':checked') == false){
        //     docoNotification('warning', 'Perhatian!', 'Belum ada tindakan yang diinputkan!');
        //     return false
        // }
        if($('.panel-jadwal-operasi').length > 0 && !$('.panel-jadwal-operasi').hasClass('hidden') && !Object.keys(_jadwalOperasi).length) {
            docoNotification('warning', 'Perhatian!', 'Jadwal Operasi Belum Di Tentukan!');
            return false
        }
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
        if (typeof ruanganperiksa_id != 'undefined') {
            _form.push({
                name: 'ruanganperiksa_id',
                value: ruanganperiksa_id
            })
        }

        if ($('#diagnosa_utama_text').length > 0) {
            _form.push({
                name: 'InstruksiPenunjangForm[diagnosa_utama]',
                value: JSON.stringify(_diagnosaUtama)
            })
        }
        if ($('#diagnosa_penyerta').length > 0) {
            _form.push({
                name: 'InstruksiPenunjangForm[diagnosa_penyerta]',
                value: JSON.stringify(_diagnosaPenyerta)
            })
        }
        if (!$('.panel-jadwal-operasi').hasClass('hidden')) {
            _form.push({
                name: 'jadwal_operasi',
                value: JSON.stringify(_jadwalOperasi)
            })
        }
        if( _form[ _form.findIndex(_obj => _obj.name == 'InstruksiPenunjangForm[catatan_dokterpengirim]') ].value != ''){
            _form.push({
                name: 'InstruksiPenunjangForm[catatan]',
                value: _form[ _form.findIndex(_obj => _obj.name == 'InstruksiPenunjangForm[catatan_dokterpengirim]') ].value
            })

            _form[ _form.findIndex(_obj => _obj.name == 'InstruksiPenunjangForm[catatan_dokterpengirim]') ].value += _catatanList != '' ? '\n\n' + _catatanList : '';
        }
        $().docoForm('click', {
            data: _form,
            skipScrollUp: false,
            skipConfirm: true,
            url: $('#order-penunjang-form').attr('action'),
            success: function (data) {
                const { url } = data.response;
                const { cppt_id } = data.response;
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
                if( _listpemeriksaanpenunjang.length && url != null && url.length > 0){
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
        _listpemeriksaanpenunjang = []
        $('#modal-lab').find('#tbl-order-penunjang tbody').html(`
            <tr class="nd-row">
                <td colspan="8" class="text-center no-data-row">Belum Ada Data yang Diinputkan</td>
            </tr>
        `)
        $('#modal-lab').find('#tbl-order-penunjang tfoot').html(`
            <tr>
                <td>&nbsp;</td>
                <td colspan="5" class="text-bold">TOTAL</td>
                <td class="text-right text-bold order-summary">Rp. 0</td>
                <td></td>
            </tr>
        `)

        validasiClosePopup();
    })
})

$(document).ready(function() {
    validasiClosePopup();
});

/* FUNGSI VALIDASI CLOSE POP UP */
function validasiClosePopup() {
    var tableOrderPenunjang = $("#tbl-order-penunjang tbody tr").not('.nd-row').length;

    if(tableOrderPenunjang > 0) {
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
