
var timer


$(document).ready(function () {
    $('#resumemedisform-nama_alergi').prop('readonly', true);
    let counterLab = $('#lab-row tbody tr').length > 0 ? $('#lab-row tbody tr').length - 1 : 0;
    let urlPrint = '';
    var _isAlergi = $('input[name="ResumeMedisForm[is_alergi]"]:checked').val();
    if (_isAlergi == 1) {
        $('#resumemedisform-nama_alergi').prop('readonly', false);
    }
    else {
        // $('#resumemedisform-nama_alergi').val(null).trigger('change');
        $('#resumemedisform-nama_alergi').prop('readonly', true);
    }

    counterRad = 0
    counterTerapi = 0
    counterKonsultasi = 0
    counterObatrs = 0
    counterObatpulang = 0
    const _dateConfig = {
        format: 'yyyy-mm-dd hh:ii:00',
        endDate: new Date()
    }
    $(`.btn-delete-item`).unbind()
    $(`.btn-delete-item`).bind('click', ({ currentTarget }) => {
        const _target = $(currentTarget).closest('table').attr('id')
        $(currentTarget).closest('tr').remove()
        if ($(`#${_target} tbody tr`).length < 1) {
            let _colspan = $(`#${_target} thead tr th`).length
            $(`#${_target} tbody`).append(`
                <tr class="no-data-row">
                    <td colspan="${_colspan}" class="text-center">Belum ada data</td>
                </tr>
            `)
        }
    })

    if (diagnosaPenyerta != '') {
        let _objDiagnosa = diagnosaPenyerta
        var selectedOption = []
        $.each(_objDiagnosa, (k, v) => {
            var newOption = new Option(v.text, `${v.id}_${v.text}`, false, false);
            $('#resumemedisform-diag_penyerta').append(newOption)
            selectedOption.push(`${v.id}_${v.text}`)
        })
        $('#resumemedisform-diag_penyerta').val(selectedOption).trigger('change')
    }

    $('#btn-cetak-resume').on('click', function () {
        // let url = $(this).attr('data-target');
        // window.open(url, '_blank');
        urlPrint = $(this).attr('data-target');
        $("#choose-margin-resumemedis-modal").modal();
    })

    $('#btn-cetak-resume-nonsetmargin').on('click', function () {
        url = $(this).attr('data-target');
        window.open(url, '_blank');
    });

    $(".btn-print-resumemedis").on('click', function () {
        let marginTop = $('#margin-top-resumemedis').val();
        let url = urlPrint + '&margin_top=' + marginTop;

        $("#choose-margin-resumemedis-modal").modal('hide');
        window.open(url, '_blank');
    });

    $("#margin-top-resumemedis").keypress(function (e) {
        if ((e.which == 13 || e.keyCode == 13)) {
            e.preventDefault();
            $(".btn-print-resumemedis").trigger('click');
        }
    });

    // $('.input-select-date').datetimepicker(_dateConfig)
    $('.btn-add-item').bind('click', function ({ currentTarget }) {
        const _target = $(currentTarget).attr('data-target')
        _form = $(currentTarget).attr('data-form')
        let _data = $(`#${_target} tfoot :input`).serializeArray()
        let _name = _form.replace('-', '')
        let _tglName = []
        let _html = '<tr>'
        $.each(_data, (k, v) => {
            let counters = counterLab
            if (_form == 'rad-row') {
                counters = counterRad
            } else if (_form == 'terapi-row') {
                counters = counterTerapi
            } else if (_form == 'konsultasi-row') {
                counters = counterKonsultasi
            } else if (_form == 'obatrs-row') {
                counters = counterObatrs
            } else if (_form == 'obatpulang-row') {
                counters = counterObatpulang
            }

            if (v.name.indexOf('tgl_') >= 0) {
                _tglName.push({
                    name: `${_name}[${counters}][${v.name}]`,
                    value: v.value
                })
            }
            _html += `
                    <td>
                        <input type="text" name="${_name}[${counters}][${v.name}]" value="${v.value}" class="form-control" style="margin-bottom: 10px">
                    </td>
                `
        })
        _html += `
                <td>
                    <button type="button" data-target="${_target}" class="btn btn-danger btn-sm btn-delete-item"><i class="fa fa-trash"></i></button>
                </td>
            </tr>`
        $(`#${_target} tbody`).find('.no-data-row').remove()
        $(`#${_target} tbody`).append(_html)
        $(`#${_target} tfoot :input`).val(null)
        if (_tglName.length) {
            $.each(_tglName, (k, v) => {
                $(`input[name="${v.name}"]`).datetimepicker(_dateConfig)
            })
        }
        if (_form == 'lab-row') {
            counterLab++
        } else if (_form == 'rad-row') {
            counterRad++
        } else if (_form == 'terapi-row') {
            counterTerapi++
        } else if (_form == 'konsultasi-row') {
            counterKonsultasi++
        } else if (_form == 'obatrs-row') {
            counterObatrs++
        } else if (_form == 'obatpulang-row') {
            counterObatpulang++
        }
        $(`#${_target} .btn-delete-item`).unbind()
        $(`#${_target} .btn-delete-item`).bind('click', ({ currentTarget }) => {
            $(currentTarget).closest('tr').remove()
            if ($(`#${_target} tbody tr`).length < 1) {
                let _colspan = $(`#${_target} thead tr th`).length
                $(`#${_target} tbody`).append(`
                    <tr class="no-data-row">
                        <td colspan="${_colspan}" class="text-center">Belum ada data</td>
                    </tr>
                `)
            }
        })
    })
    $('#resume-medis-ri-form').unbind()
    $('#resume-medis-ri-form').bind('submit', function (e) {
        e.preventDefault()
        const _data = generateFormData()
        confirmationDialog('Apakah Anda ingin menyimpan data ini?', (isAccept) => {
            if (isAccept) {
                $.ajax({
                    url: $('#resume-medis-ri-form').prop('action'),
                    data: generateFormData(),
                    method: 'POST',
                    success: function (response) {
                        localStorage.removeItem("view-resumemedisri-" + pendaftaran_id)
                        docoNotification('success', 'Proses Berhasil!', 'Simpan Data Resume Medis Sukses!')
                        $('#tab-resumemedisri > a').click()
                    }
                });
            }
        })
    })
    // $('#btn-simpan-resume-medis-auto').unbind()
    // $('#btn-simpan-resume-medis-auto').bind('click', function (e) {
    //     e.preventDefault()
    //     const _data = generateFormData()
    //     $.ajax({
    //         url: $('#resume-medis-ri-form').prop('action'),
    //         data: generateFormData(),
    //         method: 'POST',
    //         withoutLoading: true,
    //     });
    // })
    // $('input[name="ResumeMedisForm[is_alergi]"]:checked')
    $('input[name="ResumeMedisForm[is_alergi]"]').change(function () {
        if (this.value == 1) {
            $('#resumemedisform-nama_alergi').prop('readonly', false);
            if($('#resumemedisform-nama_alergi').data('lastvalue')) {
                $('#resumemedisform-nama_alergi').val($('#resumemedisform-nama_alergi').data('lastvalue'));
            }
        }
        else {
            // $('#resumemedisform-nama_alergi').val(null).trigger('change');
            $('#resumemedisform-nama_alergi').prop('readonly', true);
            $('#resumemedisform-nama_alergi').data('lastvalue', $('#resumemedisform-nama_alergi').val());
            $('#resumemedisform-nama_alergi').val('');
        }
    });

    const generateFormData = function () {
        let data = $('#anamnesa-row :input').serializeArray()
        var _isAlergi = $("input[name='ResumeMedisForm[is_alergi]']:checked").val();
        var _caraKeluar = $("input[name='ResumeMedisForm[cara_keluar]']:checked").val();

        data.push({
            name: 'ResumeMedisForm[pendaftaran_id]',
            value: $('#resumemedisform-pendaftaran_id').val()
        })

        data.push({
            name: 'ResumeMedisForm[pasienadmisi_id]',
            value: $('#resumemedisform-pasienadmisi_id').val()
        })

        //define diag awal data
        data.push({
            name: 'ResumeMedisForm[diag_awal]',
            value: diagnosaParser($('#resumemedisform-diag_awal').val())
        })

        //define diag utama data
        data.push({
            name: 'ResumeMedisForm[diag_utama]',
            value: diagnosaParser($('#resumemedisform-diag_utama').val())
        })

        //define diag penyerta data
        data.push({
            name: 'ResumeMedisForm[diag_penyerta]',
            value: multiDiagnosaParser($('#resumemedisform-diag_penyerta').select2('data'))
        })

        data.push({
            name: 'ResumeMedisForm[riwayat_penyakit_dahulu]',
            value: $('#resumemedisform-riwayat_penyakit_dahulu').val()
        })

        data.push({
            name: 'ResumeMedisForm[indikasi_pasien_dirawat]',
            value: $('#resumemedisform-indikasi_pasien_dirawat').val()
        })

        data.push({
            name: 'ResumeMedisForm[reaksi_alergi_obat]',
            value: $('#resumemedisform-reaksi_alergi_obat').val()
        })

        data.push({
            name: 'ResumeMedisForm[kondisi_pulang]',
            value: $('#resumemedisform-kondisi_pulang').val()
        })

        data.push({
            name: 'ResumeMedisForm[rencana_tindaklanjut]',
            value: $('#resumemedisform-rencana_tindaklanjut').val()
        })

        data.push({
            name: 'ResumeMedisForm[instruksi]',
            value: $('#resumemedisform-instruksi').val()
        })

        data.push({
            name: 'ResumeMedisForm[order_laboratorium]',
            value: $('#resumemedisform-order_laboratorium').val()
        })

        data.push({
            name: 'ResumeMedisForm[order_radiologi]',
            value: $('#resumemedisform-order_radiologi').val()
        })

        data.push({
            name: 'ResumeMedisForm[prosedur]',
            value: $('#resumemedisform-prosedur').val()
        })

        data.push({
            name: 'ResumeMedisForm[konsultasi]',
            value: $('#resumemedisform-konsultasi').val()
        })

        data.push({
            name: 'ResumeMedisForm[obat]',
            // value: $('#resumemedisform-obat_rs').val()
            value: JSON.stringify(_orderData.obat)
        })

        data.push({
            name: 'ResumeMedisForm[obat_dibawa_pulang]',
            // value: $('#resumemedisform-obat_dibawa_pulang').val()
            value: JSON.stringify(_orderData.obat_pulang)
        })
        
        data.push({
            name: 'ResumeMedisForm[obat_dibawa_pulang_text]',
            value: $('#resumemedisform-obat_dibawa_pulang_text').val()
        })

        data.push({
            name: 'ResumeMedisForm[lain_lainnya]',
            value: $('#resumemedisform-lain_lainnya').val()
        })

        data.push({
            name: 'ResumeMedisForm[catatan_diet]',
            value: $('#resumemedisform-catatan_diet').val()
        })

        data.push({
            name: 'ResumeMedisForm[pemeriksaan_fisik]',
            value: $('#resumemedisform-pemeriksaan_fisik').val()
        })

        data.push({
            name: 'ResumeMedisForm[tgl_masuk]',
            value: $('#resumemedisform-tgl_masuk').val()
        })

        data.push({
            name: 'ResumeMedisForm[tgl_keluar]',
            value: $('#resumemedisform-tgl_keluar').val()
        })

        data.push({
            name: 'ResumeMedisForm[instruksi_tanggal]',
            value: $('#resumemedisform-instruksi_tanggal').val()
        })

        data.push({
            name: 'ResumeMedisForm[instruksi_kontrol]',
            value: $('#resumemedisform-instruksi_kontrol').val()
        })

        data.push({
            name: 'ResumeMedisForm[is_igd]',
            value: $('[name="ResumeMedisForm[is_igd]"]:checked').length > 0 ? 1 : 0
        })

        data.push({
            name: 'ResumeMedisForm[kontak_darurat]',
            value: $('#resumemedisform-kontak_darurat').val()
        })

        data.push({
            name: 'ResumeMedisForm[edukasi_rencana]',
            value: $('#resumemedisform-edukasi_rencana').val()
        })

        data.push({
            name: 'ResumeMedisForm[nama_alergi]',
            value: $('#resumemedisform-nama_alergi').val()
        })

        data.push({
            name: 'ResumeMedisForm[kesadaran]',
            value: $('#resumemedisform-kesadaran').val()
        })

        data.push({
            name: 'ResumeMedisForm[keadaan_umum]',
            value: $('#resumemedisform-keadaan_umum').val()
        })

        data.push({
            name: 'ResumeMedisForm[frekuensi_nafas]',
            value: $('#resumemedisform-frekuensi_nafas').val()
        })

        data.push({
            name: 'ResumeMedisForm[cara_keluar]',
            value: _caraKeluar
        })

        data.push({
            name: 'ResumeMedisForm[is_alergi]',
            value: _isAlergi
        })

        data.push({
            name: 'ResumeMedisForm[td]',
            value: $('#resumemedisform-td').val()
        })

        data.push({
            name: 'ResumeMedisForm[nadi]',
            value: $('#resumemedisform-nadi').val()
        })

        data.push({
            name: 'ResumeMedisForm[suhu]',
            value: $('#resumemedisform-suhu').val()
        })

        data.push({
            name: 'ResumeMedisForm[instruksi_tindakanbmhp]',
            value: $('#resumemedisform-instruksi_tindakanbmhp').val()
        })

        data.push({
            name: 'ResumeMedisForm[obat_rs]',
            value: $('#resumemedisform-obat_rs').val()
        })

        data.push({
            name: 'ResumeMedisForm[dokter_pengirim]',
            value: $('#resumemedisform-dokter_pengirim').val()
        })

        let _tableData = [
            // {
            //     name: 'ResumeMedisForm[order_laboratorium]',
            //     key: '#lab-row',
            // },
            // {
            //     name: 'ResumeMedisForm[order_radiologi]',
            //     key: '#rad-row'
            // },
            {
                name: 'ResumeMedisForm[konsul]',
                key: '#konsultasi-row'
            },
            {
                name: 'ResumeMedisForm[tindakan]',
                key: '#terapi-row'
            },
            // {
            //     name: 'ResumeMedisForm[obat]',
            //     key: '#obatrs-row'
            // },
            // {
            //     name: 'ResumeMedisForm[obat_dibawa_pulang]',
            //     key: '#obatpulang-row'
            // },
        ];
        $.each(_tableData, (index, field) => {
            data.push({
                name: field.name,
                value: JSON.stringify($(`${field.key} tbody :input`).serializeArray())
            })
        })
        return $.merge(data, $('#periksafisik-row :input').serializeArray())
    }

    var diagnosaParser = function (_string) {
        if (_string == '') {
            return null
        }
        let _diagnosaData = _string.split(' - ');
        _diagnosaId = ''
        _diagnosaKode = ''
        _diagnosaNama = ''
        if (_diagnosaData.length > 1) {
            _diagnosaKodeId = _diagnosaData[0].split('_')
            if (_diagnosaKodeId.length) {
                _diagnosaId = _diagnosaKodeId[1] != undefined ? _diagnosaKodeId[0] : null
                _diagnosaKode = _diagnosaKodeId[1] != undefined ? _diagnosaKodeId[1] : _diagnosaKodeId[0]
            }
            _diagnosaNama = _diagnosaData[1]
        } else {
            _diagnosaNama = _diagnosaData[0]
        }
        return JSON.stringify({
            id: _diagnosaId,
            kode: _diagnosaKode,
            nama: _diagnosaNama,
            text: (_diagnosaKode != '' ? _diagnosaKode + ' - ' : '') + _diagnosaNama
        })
    }

    var multiDiagnosaParser = function (_obj) {
        if (_obj.length == 0) {
            return null
        }
        let _diagnosaData = []
        $.each(_obj, (k, v) => {
            let _diagnosaSplit = v.text.split(' - ')
            _diagnosaIdSplit = v.id.split('_')
            _diagnosaKode = ''
            _diagnosaNama = ''
            if (_diagnosaSplit.length > 1) {
                _diagnosaKode = _diagnosaSplit[0]
                _diagnosaNama = _diagnosaSplit[1]
            } else {
                _diagnosaNama = v.text
            }
            _diagnosaData.push({
                id: _diagnosaIdSplit[0],
                kode: _diagnosaKode,
                nama: _diagnosaNama,
                text: (_diagnosaKode != '' ? _diagnosaKode + ' - ' : '') + _diagnosaNama
            })
        })
        return JSON.stringify(_diagnosaData)
    }

    /* Fungsi untuk cek 30 hari dari daftar pulang */
    let getTglPasienPulang = $('#tglpasienpulang').attr('data-tgl_pasien_pulang');
    let isstopakomodasi = $('#tglpasienpulang').attr('is_stopakomodasi');
    if (getTglPasienPulang) {
        /* tanggal sekarang */
        let current_fulldate = new Date();
        let current_month = current_fulldate.getMonth() - (-1);
        let current_date = current_fulldate.getDate();
        let current_year = current_fulldate.getFullYear();
        let curdate = new Date(current_year + '-' + current_month + '-' + current_date);

        /* hitung tanggal dari tanggal pulang pasien */
        let date = new Date(getTglPasienPulang);
        let next30days = date.setDate(date.getDate() + 30);
        let dateToString = new Date(date.toISOString().split('T')[0]);
        /* di comment dulu
        lepas validasi pasien sudah dipulangkan lebih dari 30 hari tidak bisa edit resume medis */
        // if (current_fulldate > dateToString) {
        //     $('.btn-simpan-resume-medis').prop('disabled', true);
        // }
    }

})

var payloadPenunjang = {
    'lab-external': {
        table: null,
        data: {},
        resultNoFieldName: 'nohasilperiksalab',
        dateFieldName: 'tgl_hasilpemeriksaanlab',
        selected: []
    },
    lab: {
        table: null,
        data: {},
        resultNoFieldName: 'nohasilperiksalab',
        dateFieldName: 'tgl_hasilpemeriksaanlab',
        selected: []
    },
    rad: {
        table: null,
        data: {},
        resultNoFieldName: 'no_hasilrad',
        dateFieldName: 'tgl_hasilrad',
        fieldShowed: [
            { key: 'daftartindakan_nama', title: 'Nama Pemeriksaan' },
            { key: 'deskripsi', title: 'Deskripsi' },
            { key: 'kesan', title: 'Kesan' },
        ],
        selected: []
    }
}
var originInput = null
$(".search-penunjang").bind('click', ({ delegateTarget }) => {
    const { type } = $(delegateTarget).data()
    let columns = []
    let textEditorId = ''
    const dataModal = payloadPenunjang[type]
    if (typeof dataModal == 'undefined') {
        return false
    }
    switch (type) {
        case 'rad':
            textEditorId = 'resumemedisform-order_radiologi'
            columns = [
                {
                    data: null,
                    width: '10px',
                    searchable: false,
                    orderable: false,
                    render: function (data) {
                        return `<input type="checkbox" class="checkbox-result" value="${data.no_hasilrad}" ${dataModal.selected.indexOf(`${data.no_hasilrad}-${data.no_hasilrad}`) >= 0 ? 'checked' : ''} data-result-no="${data.no_hasilrad}">`
                    }
                },
                {
                    data: null,
                    width: '10px',
                    searchable: false,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'tgl_hasilrad',
                    searchable: false,
                    orderable: false,
                    render: (data) => {
                        return moment(data).format('DD-MMMM-YYYY HH:mm')
                    }
                },
                {
                    data: 'daftartindakan_nama',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'deskripsi',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'kesan',
                    searchable: false,
                    orderable: false
                },
            ]
            break;
        case 'lab':
            textEditorId = 'resumemedisform-order_laboratorium'
            columns = [
                {
                    data: null,
                    width: '10px',
                    searchable: false,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'tgl_hasilpemeriksaanlab',
                    searchable: false,
                    orderable: false,
                    render: (data) => {
                        return moment(data).format('DD-MMMM-YYYY HH:mm')
                    }
                },
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    render: (data) => {
                        console.log(data);
                        var eachName = '<table border="0" width="100%">';
                        var len = data.results.length;
                        var checkbox = false;
                        data.results.map((detailResult, index) => {

                            if (!checkbox) {
                                eachName += `<tr>
                                    <td><input type="checkbox" class="checkbox-result check-${type}" value="${data.results[index].daftartindakan_id}" ${dataModal.selected.indexOf(`${data.nohasilperiksalab}-${data.results[index].daftartindakan_id}`) >= 0 ? 'checked' : ''} data-result-no="${data.nohasilperiksalab}"><strong>&nbsp;${data.results[index].daftartindakan_nama}</strong></td>
                                </tr>`;
                                checkbox = true;
                            }
                        
                            eachName += `<tr">
                                <td ${detailResult.daftartindakan_nama != detailResult.nama_rujukan && detailResult.nama_rujukan != null ? 'style="padding-left: 40px;"' : ''}>
                                    ${detailResult.daftartindakan_nama == detailResult.nama_rujukan || detailResult.nama_rujukan == null ? `<input type="checkbox" class="checkbox-result check-${type}" value="${detailResult.daftartindakan_id}" ${dataModal.selected.indexOf(`${data.nohasilperiksalab}-${detailResult.daftartindakan_id}`) >= 0 ? 'checked' : ''} data-result-no="${data.nohasilperiksalab}">` : ''} ${detailResult.nama_rujukan != null ? detailResult.nama_rujukan : ''}
                                </td>
                            </tr>`
                        })
                        eachName += '</table>'
                        return eachName
                    }
                },
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    render: (data) => {
                        var eachResult = '<table border="0" width="100%">'
                        data.results.map((detailResult) => {
                            eachResult += `<tr><td>${detailResult.hasil}</td></tr>`
                        })
                        eachResult += '</table>'
                        return eachResult
                    }
                },
            ]
            break;
        case 'lab-external':
            textEditorId = 'resumemedisform-order_laboratorium'
            columns = [
                {
                    data: null,
                    width: '10px',
                    searchable: false,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'tgl_hasilpemeriksaanlab',
                    searchable: false,
                    orderable: false,
                    render: (data) => {
                        return moment(data).format('DD-MMMM-YYYY HH:mm')
                    }
                },
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    render: (data) => {
                        var eachName = '<table border="0" width="100%">'
                        data.results.map((detailResult) => {
                            eachName += `<tr">
                                <td ${detailResult.daftartindakan_nama != detailResult.nama_rujukan && detailResult.nama_rujukan != null ? 'style="padding-left: 40px;"' : ''}>
                                    ${detailResult.daftartindakan_nama == detailResult.nama_rujukan || detailResult.nama_rujukan == null ? `<input type="checkbox" class="checkbox-result check-${type}" value="${detailResult.daftartindakan_id}" ${payloadPenunjang['lab'].selected.indexOf(`${data.nohasilperiksalab}-${detailResult.daftartindakan_id}`) >= 0 ? 'checked' : ''} data-result-no="${data.nohasilperiksalab}">` : ''} ${detailResult.nama_rujukan != null ? detailResult.nama_rujukan : ''}
                                </td>
                            </tr>`
                        })
                        eachName += '</table>'
                        return eachName
                    }
                },
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    render: (data) => {
                        var eachResult = '<table border="0" width="100%">'
                        data.results.map((detailResult) => {
                            eachResult += `<tr><td>${detailResult.hasil}</td></tr>`
                        })
                        eachResult += '</table>'
                        return eachResult
                    }
                },
            ]
            break;
    }
    originInput = CKEDITOR.instances[textEditorId].getData()
    if (typeof payloadPenunjang[type].table == 'undefined' || payloadPenunjang[type].table == null) {
        showLoader()
        let paykey = type
        let qadditional = ''
        if (type == 'lab-external') {
            paykey = 'lab'
            qadditional = 'load=external'
        }
        payloadPenunjang[type].table = $(`#${type}-order-table`).docoTabel({
            filter: false,
            info: false,
            processing: true,
            serverSide: true,
            autoWidth: false,
            aaSorting: [],
            order: [],
            ajax: {
                url: `/ranap/pemeriksaan-rawat-inap/resume-${paykey}-result?${qadditional}`,
                data: {
                    pendaftaran_id
                }
            },
            columns,
            initComplete: () => {
                $(`#${type}-order-modal`).modal({
                    backdrop: 'static',
                    keyboard: false
                })
            },
            drawCallback: ({ json }) => {
                var resultGrouped = {}
                json.data.map((data) => {
                    if (typeof payloadPenunjang[type].data[data[dataModal['resultNoFieldName']]] == 'undefined') {
                        payloadPenunjang[type].data[data[dataModal['resultNoFieldName']]] = {}
                    }
                    if (type == 'lab' || type == 'lab-external') {
                        resultGrouped = {}
                        data.results.map((detailResult) => {
                            if (typeof resultGrouped[detailResult.daftartindakan_id] == 'undefined') {
                                resultGrouped[detailResult.daftartindakan_id] = []
                            }
                            resultGrouped[detailResult.daftartindakan_id].push(detailResult)

                        })
                        payloadPenunjang[type].data[data.nohasilperiksalab]['tgl_hasilpemeriksaanlab'] = data.tgl_hasilpemeriksaanlab
                        payloadPenunjang[type].data[data.nohasilperiksalab]['details'] = resultGrouped
                    } else {
                        payloadPenunjang[type].data[data[dataModal['resultNoFieldName']]] = data
                    }
                })
                $(`#${type}-order-table input[type='checkbox']`).uniform({
                    radioClass: 'choice'
                })
                $('.checkbox-result').bind('change', ({ currentTarget }) => {
                    const { resultNo } = $(currentTarget).data()
                    const detailId = $(currentTarget).val()
                    const keyField = `${resultNo}-${detailId}`
                    if ($(currentTarget).is(':checked')) {
                        // checked
                        payloadPenunjang[paykey].selected.push(keyField)
                    } else {
                        const indexExisting = payloadPenunjang[paykey].selected.indexOf(keyField)
                        if (indexExisting >= 0) {
                            // if is exist
                            payloadPenunjang[paykey].selected.splice(indexExisting, 1)
                        }
                    }
                    var newAdditionalSelected = ''
                    var totalSelected = payloadPenunjang[paykey].selected.length
                    var neednewlinebefore = false
                    if (originInput) {
                        var originInputElementLastHtml = $(originInput).last().prev().html()
                        if (originInputElementLastHtml !== undefined) {
                            neednewlinebefore = originInputElementLastHtml.match(/<strong>\d{1,2}-\w+-\d{4,}\s\d{1,2}\:\d{1,2}<\/strong>/)
                        }
                    }
                    // var 
                    if (paykey == 'lab') {
                        if (type == 'lab-external') {
                            const groupedSelected = {};
                            payloadPenunjang[paykey].selected.forEach(selected => {
                                const resultNo = selected.split('-')[0];
                                const detailIdSelected = selected.split(/-(.*)/s)[1];
                                if (!groupedSelected[resultNo]) {
                                    groupedSelected[resultNo] = [];
                                }
                                groupedSelected[resultNo].push(detailIdSelected);
                            });

                            const resultNos = Object.keys(groupedSelected);
                            resultNos.forEach((resultNo, groupIdx) => {
                                if (typeof payloadPenunjang[type].data[resultNo] != 'undefined') {
                                    newAdditionalSelected += `<p><strong class="input-hasil-pemeriksaan">${moment(payloadPenunjang[type].data[resultNo][dataModal.dateFieldName]).format('DD-MMMM-YYYY HH:mm')}</strong></p>`;
                                    groupedSelected[resultNo].forEach(detailIdSelected => {
                                        payloadPenunjang[type].data[resultNo]['details'][detailIdSelected].forEach(detailSelected => {
                                            newAdditionalSelected += `<p class="input-hasil-pemeriksaan" ${detailSelected.nama_rujukan != detailSelected.daftartindakan_nama && detailSelected.nama_rujukan != null ? 'style="margin-left: 24px;"' : ''}>${detailSelected.nama_rujukan != null ? detailSelected.nama_rujukan : ''} : ${detailSelected.hasil}&nbsp;${detailSelected.satuanlab_nama != null ? detailSelected.satuanlab_nama : ''}</p>`;
                                        });
                                    });
                                }
                                if ((groupIdx + 1) < resultNos.length) {
                                    newAdditionalSelected += '<br><hr>';
                                }
                            });
                        } else {
                            payloadPenunjang[paykey].selected.map((selected, indexSelected) => {
                                var resultNo = selected.split('-')[0]
                                var detailIdSelected = selected.split('-')[1]
                                if (typeof payloadPenunjang[type].data[resultNo] != 'undefined') {
                                    if (neednewlinebefore) {
                                        newAdditionalSelected += '<br><hr>'
                                    }
                                    // add to new additional selected as text
                                    newAdditionalSelected += `<p class="input-hasil-pemeriksaan"><strong>${moment(payloadPenunjang[type].data[resultNo][dataModal.dateFieldName]).format('DD-MMMM-YYYY HH:mm')}</strong></p>`
                                    newAdditionalSelected += `<p class="input-hasil-pemeriksaan"><strong>${payloadPenunjang[type].data[resultNo]['details'][detailIdSelected][0].daftartindakan_nama}</strong></p>`
                                    payloadPenunjang[type].data[resultNo]['details'][detailIdSelected].map((detailSelected, indexDetailSelected) => {
                                        newAdditionalSelected += `<p class="input-hasil-pemeriksaan" ${detailSelected.nama_rujukan != detailSelected.daftartindakan_nama && detailSelected.nama_rujukan != null ? 'style="margin-left: 24px;"' : ''}>${detailSelected.nama_rujukan != null ? detailSelected.nama_rujukan : ''} : ${detailSelected.hasil}&nbsp;${detailSelected.satuanlab_nama != null ? detailSelected.satuanlab_nama : ''}</p>`
                                    })
                                    if ((indexSelected + 1) < totalSelected) {
                                        newAdditionalSelected += '<br><hr>'
                                    }
                                }
                            })
                        }
                        var getCheck = localStorage.getItem('setCheck');
                        if (getCheck == 'true') {
                            localStorage.setItem('checkboxValue', originInput + newAdditionalSelected);
                        } else {
                            CKEDITOR.instances[textEditorId].setData(originInput + newAdditionalSelected)
                        }

                    } 
                    // else if (paykey == 'rad') {
                    //     payloadPenunjang[paykey].selected.map((selected, indexSelected) => {
                    //         var resultNo = selected.split('-')[0]
                    //         var detailIdSelected = selected.split('-')[1]
                    //         if (typeof payloadPenunjang[type].data[resultNo] != 'undefined') {
                    //             // add to new additional selected as text
                    //             newAdditionalSelected += `<p><strong>${moment(payloadPenunjang[type].data[resultNo][dataModal.dateFieldName]).format('DD-MMMM-YYYY HH:mm')}</strong></p>`
                    //             dataModal.fieldShowed.map((fieldObject) => {
                    //                 if (typeof payloadPenunjang[type].data[resultNo][fieldObject.key] != 'undefined') {
                    //                     newAdditionalSelected += `<p><strong>${fieldObject.title} :</strong></p>${payloadPenunjang[type].data[resultNo][fieldObject.key]}`
                    //                 }
                    //             })
                    //             if ((indexSelected + 1) < totalSelected) {
                    //                 newAdditionalSelected += '<br><hr>'
                    //             }
                    //         }
                    //     })
                    // }
                    // CKEDITOR.instances[textEditorId].setData(originInput + newAdditionalSelected)


                    if (['lab-external', 'rad'].some(from => from == paykey)) {
                        let addGroupSelected = '';
                        let groupingSelected = [];
                        
                        const wrapperTemporary = $('body').find('#temporary_order_'+paykey);
                        const selectedTemprary = wrapperTemporary.find('.data-selected');
    
                        payloadPenunjang[paykey].selected.map((valueSelected, keySelected) => {
                            if (valueSelected.split('-').length > 1) {
                                let selectedNo = valueSelected.split('-')[0];
                                let selectedDetail = valueSelected.split('-')[1];
                                let selectedDate = payloadPenunjang[type].data[selectedNo];

                                if (paykey == 'lab') {
                                    if (typeof selectedDate !== 'undefined') {
                                        let selectedPenunjangDetailLab = selectedDate.details[selectedDetail];
                                        let idLab = moment(selectedDate[dataModal.dateFieldName]).format('YYYYMMDD');
                                        let selectedDateDetailLab = [];

                                        if (selectedPenunjangDetailLab.length > 0) {
                                            if (groupingSelected.some((value) => value.id === idLab)) {
                                                if (!groupingSelected.filter((value) => value.id === idLab)[0].value_detail.some((cv) => cv.id === selectedPenunjangDetailLab[0].daftartindakan_id)) {
                                                    selectedDateDetailLab.push({
                                                        id: selectedPenunjangDetailLab[0].daftartindakan_id,
                                                        value: `<div class="data-selected-detail" data-id-detail="${selectedPenunjangDetailLab[0].daftartindakan_id}" ${selectedPenunjangDetailLab[0].nama_rujukan != selectedPenunjangDetailLab[0].daftartindakan_nama && selectedPenunjangDetailLab[0].nama_rujukan != null ? 'style="margin-left: 24px;"' : ''}>${selectedPenunjangDetailLab[0].nama_rujukan != null ? selectedPenunjangDetailLab[0].nama_rujukan : ''} : ${selectedPenunjangDetailLab[0].hasil}</div>`
                                                    })
                                                }
                                            } else {
                                                selectedDateDetailLab.push({
                                                    id: selectedPenunjangDetailLab[0].daftartindakan_id,
                                                    value: `<div class="data-selected-detail" data-id-detail="${selectedPenunjangDetailLab[0].daftartindakan_id}" ${selectedPenunjangDetailLab[0].nama_rujukan != selectedPenunjangDetailLab[0].daftartindakan_nama && selectedPenunjangDetailLab[0].nama_rujukan != null ? 'style="margin-left: 24px;"' : ''}>${selectedPenunjangDetailLab[0].nama_rujukan != null ? selectedPenunjangDetailLab[0].nama_rujukan : ''} : ${selectedPenunjangDetailLab[0].hasil}</div>`
                                                })
                                            } 
                                        }
    
                                        if (groupingSelected.some((value) => value.id === idLab)) {
                                            groupingSelected[groupingSelected.findIndex((obj => obj.id === idLab))].value_detail = [
                                                ...groupingSelected.filter((value) => value.id === idLab)[0].value_detail,
                                                ...selectedDateDetailLab
                                            ];
                                        } else {
                                            groupingSelected.push({
                                                id: idLab,
                                                value: `<div data-id-title="${idLab}">
                                                    <strong>
                                                        ${moment(selectedDate[dataModal.dateFieldName]).format('DD-MMMM-YYYY')}
                                                    </strong>
                                                </div>`,
                                                value_detail: selectedDateDetailLab
                                            });
                                        }
                                    }
                                } else if (paykey == 'rad') {
                                    if (typeof selectedDate !== 'undefined') {
                                        let selectedDateDetailRad = [];
                                        const idRad = moment(selectedDate[dataModal.dateFieldName]).format('YYYYMMDD');

                                        if (groupingSelected.some((value) => value.id === idRad)) {
                                            if (!groupingSelected.filter((value) => value.id === idRad)[0].value_detail.some((cv) => cv.id === selectedDate.no_hasilrad)) {
                                                selectedDateDetailRad.push({
                                                    id: selectedDate.no_hasilrad,
                                                    value: `
                                                        <div class="data-selected-detail" data-id-detail="${selectedDate.no_hasilrad}">
                                                            ${dataModal.fieldShowed.map((fieldObject) => {
                                                                if (typeof selectedDate[fieldObject.key] != 'undefined') {
                                                                    return `
                                                                        <div>
                                                                            <strong>${fieldObject.title} :</strong>
                                                                        </div>
                                                                        ${selectedDate[fieldObject.key]}
                                                                    `
                                                                }
                                                            }).join('')}
                                                        </div>
                                                    `
                                                })
                                            }
                                        } else {
                                            selectedDateDetailRad.push({
                                                id: selectedDate.no_hasilrad,
                                                value: `
                                                    <div class="data-selected-detail" data-id-detail="${selectedDate.no_hasilrad}">
                                                        ${dataModal.fieldShowed.map((fieldObject) => {
                                                            if (typeof selectedDate[fieldObject.key] != 'undefined') {
                                                                return `
                                                                    <div>
                                                                        <strong>${fieldObject.title} :</strong>
                                                                    </div>
                                                                    ${selectedDate[fieldObject.key]}
                                                                `
                                                            }
                                                        }).join('')}
                                                    </div>
                                                `
                                            })
                                        } 
    
                                        if (groupingSelected.some((value) => value.id === idRad)) {
                                            groupingSelected[groupingSelected.findIndex((obj => obj.id === idRad))].value_detail = [
                                                ...groupingSelected.filter((value) => value.id === idRad)[0].value_detail,
                                                ...selectedDateDetailRad
                                            ];
                                        } else {
                                            groupingSelected.push({
                                                id: idRad,
                                                value: `<div data-id-title="${idRad}">
                                                    <strong>
                                                        ${moment(selectedDate[dataModal.dateFieldName]).format('DD-MMMM-YYYY')}
                                                    </strong>
                                                </div>`,
                                                value_detail: selectedDateDetailRad
                                            });
                                        }
                                    }
                                }
                            }
                        })

                        if (originInput) {
                            selectedTemprary.each(function() {
                                // hapus tanggal kalau pemeriksaan ga ada
                                if (!groupingSelected.some(gss => parseInt(gss.id) === $(this).data('id'))) {
                                    wrapperTemporary.find('[data-id="'+$(this).data('id')+'"] [data-id-title="'+$(this).data('id')+'"]').remove();
                                    wrapperTemporary.find('[data-id="'+$(this).data('id')+'"] .data-selected-detail').remove();
                                    if (wrapperTemporary.find('[data-id="'+$(this).data('id')+'"]').children().length < 1) {
                                        wrapperTemporary.find('[data-id="' + $(this).data('id') + '"]').remove();
                                    }
                                }
                            });

                            groupingSelected.map((gsmVal) => {
                                const dataSelectedTemporary = wrapperTemporary.find(`[data-id="${gsmVal.id}"]`);

                                // data tidak ada
                                if (dataSelectedTemporary.length < 1) {
                                    wrapperTemporary.find('.data-selected:last').length < 1 ? wrapperTemporary.append(`
                                        <div class="data-selected" data-id="${gsmVal.id}" style="margin-bottom: 15px;">
                                            ${gsmVal.value}
                                            ${gsmVal.value_detail.map(vd => vd.value).join('')}
                                        </div>
                                    `) : wrapperTemporary.find(`.data-selected:last`).after(`
                                        <div class="data-selected" data-id="${gsmVal.id}" style="margin-bottom: 15px;">
                                            ${gsmVal.value}
                                            ${gsmVal.value_detail.map(vd => vd.value).join('')}
                                        </div>
                                    `)   
                                } else { // data ada
                                    // judul tidak ada
                                    if (dataSelectedTemporary.find(`[data-id-title="${gsmVal.id}"]`).length < 1) {
                                        dataSelectedTemporary.append(gsmVal.value)
                                        dataSelectedTemporary.find(`[data-id-title="${gsmVal.id}"]`).after(gsmVal.value_detail.map(vd => vd.value).join('')) 
                                    } else { // judul ada
                                        gsmVal.value_detail.map((gsmvdmVal) => {
                                            // tambah
                                            if (dataSelectedTemporary.find(`[data-id-detail="${gsmvdmVal.id}"]`).length < 1) {
                                                dataSelectedTemporary.find(`.data-selected-detail:last`).after(gsmvdmVal.value)
                                            } else { // hapus
                                                dataSelectedTemporary.find('.data-selected-detail').each(function() {
                                                    if (!gsmVal.value_detail.some(gvdv => gvdv.id === $(this).data('id-detail'))) {
                                                        dataSelectedTemporary.find(`[data-id-detail="${$(this).data('id-detail')}"]`).remove();
                                                    }
                                                })
                                            }
                                        })
                                    }
                                }
                            })
                        } else {
                            groupingSelected.map((gsmVal) => {
                                addGroupSelected += `
                                    <div class="data-selected" data-id="${gsmVal.id}" style="margin-bottom: 15px;">
                                        ${gsmVal.value}
                                        ${gsmVal.value_detail.map(vd => vd.value).join('')}
                                    </div>
                                `;
                            })

                            wrapperTemporary.html(addGroupSelected);
                        }
                        
                        CKEDITOR.instances[textEditorId].setData(wrapperTemporary.html())
                    }
                })
            }
        })
    } else {
        $(`#${type}-order-modal`).modal({
            backdrop: 'static',
            keyboard: false
        })
        payloadPenunjang[type].table.draw()
    }
})

$('#search-prosedur-btn').click(function (e) { 
    e.preventDefault();
    $(`#tindakan-order-modal`).modal({
        backdrop: 'static',
        keyboard: false
    })
    var textEditorId = 'resumemedisform-prosedur';
    $('#tindakan-order-modal').on('shown.bs.modal', function() {
        $('#tindakan-order-modal').find('.modal-body').empty();
        $('#tindakan-order-modal').find('.modal-body').append(_orderData.tindakan);
        originInput = CKEDITOR.instances[textEditorId].getData();
        $('.checkbox-tindakan-all').bind("change", function () {
            if ($(this).is(':checked')) {
                $('.checkbox-tindakan').prop('checked', true);
            } else {
                $('.checkbox-tindakan').prop('checked', false);
            }
            $('.checkbox-tindakan').trigger("change");
        })
        $('.checkbox-tindakan').bind("change", function () {
            if($('.checkbox-tindakan:checked').length == $('.checkbox-tindakan').length){
                $('.checkbox-tindakan-all').prop('checked', true);
            } else {
                $('.checkbox-tindakan-all').prop('checked', false);
            }
            var string = $('.checkbox-tindakan').filter(":checked").map(function(i,v){
                return this.value;
            }).get().join('<br>');
            CKEDITOR.instances[textEditorId].setData(originInput + string);
            
        });
    });
});

$('#resumemedisform-order_laboratorium').change(function () { 
    $('body').find('#temporary_order_lab').html($(this).val())
});

$('#resumemedisform-order_radiologi').change(function () { 
    $('body').find('#temporary_order_rad').html($(this).val())
});

CKEDITOR.addCss('.cke_editable p { margin: 0; }' );

$('.checkbox-lab').bind("change", function () {
    var thisDom = $(this);
    var textEditorId = 'resumemedisform-order_laboratorium';
    setTimeout(() => {
        localStorage.setItem('setCheck', true);
        $('.checkbox-result.check-lab').each(function () {
            var parent = $(this).parent();
            var dom = $(this);
            if (thisDom.is(':checked')) {
                if (! parent.hasClass('checked')) {
                    parent.addClass('checked');
                    dom.prop('checked', true).trigger("change");
                }
            } else {
                parent.removeClass('checked');
                dom.prop('checked', false).trigger("change");
            }
        })

        localStorage.setItem('setCheck', false);
        setTimeout(() => {
            var valueCheckbox = localStorage.getItem('checkboxValue');
            CKEDITOR.instances[textEditorId].setData(valueCheckbox);
        }, 200);
    }, 100);
})

$('.checkbox-lab-external').bind("change", function () {
    var thisDom = $(this);
    var textEditorId = 'resumemedisform-order_laboratorium';

    setTimeout(() => {
        localStorage.setItem('setCheck', true);
        $('.checkbox-result.check-lab-external').each(function () {
            var parent = $(this).parent();
            var dom = $(this);
            if (thisDom.is(':checked')) {
                if (! parent.hasClass('checked')) {
                    parent.addClass('checked');
                    dom.prop('checked', true).trigger("change");
                }
            } else {
                parent.removeClass('checked');
                dom.prop('checked', false).trigger("change");
            }
        })

        localStorage.setItem('setCheck', false);
        setTimeout(() => {
            var valueCheckbox = localStorage.getItem('checkboxValue');
            CKEDITOR.instances[textEditorId].setData(valueCheckbox);
            localStorage.removeItem('checkboxValue');
        }, 200);
    }, 100);
})
