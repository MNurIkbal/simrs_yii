$(() => {
    _listpemeriksaanpenunjang = []
    $('#instruksipenunjangform-ruangan_id').select2()
    $('#instruksipenunjangform-ruangan_id').val($('#instruksipenunjangform-ruangan_id>option:eq(1)').val()).change()

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
            $('#btn-tambah-pemeriksaan').attr('disabled', false)
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

    $('#btn-save-penunjang').bind('click', () => {
        if (!_listpemeriksaanpenunjang.length && $('#instruksipenunjangform-is_rujukan').is(':checked') == false) {
            docoNotification('warning', 'Perhatian!', 'Belum ada terapi yang diinputkan!');
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
        if (!$('.panel-jadwal-operasi').hasClass('hidden')) {
            _form.push({
                name: 'jadwal_operasi',
                value: JSON.stringify(_jadwalOperasi)
            })
        }
        if (_form[_form.findIndex(_obj => _obj.name == 'InstruksiPenunjangForm[catatan_dokterpengirim]')].value != '') {
            _form[_form.findIndex(_obj => _obj.name == 'InstruksiPenunjangForm[catatan_dokterpengirim]')].value += _catatanList != '' ? '\n\n' + _catatanList : '';
        }
        $().docoForm('click', {
            data: _form,
            skipScrollUp: false,
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
                if (_listpemeriksaanpenunjang.length) {
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
            <tr>
                <td colspan="8" class="text-center no-data-row">Belum Ada Data yang Diinputkan</td>
            </tr>
        `)
    })
})
