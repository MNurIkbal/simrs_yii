var timer;
var skipTimeout = false;
var limitDefault = 5;
var suggestSoapSessionName = `suggestsoaprj#${pendaftaran_id}#${id_pegawai}#${id_ruangan}`;

$("#btn-save-soap").on('click', function (event) {
    event.preventDefault();
    var values = $("#form-soap-cppt").serializeArray();
    var diag_utama_length = $('#soaprjform-a_diag_utama_text').val().length
    if (!$('#soaprjform-is_icd_x').is(':checked')) {
        values.push({ name: 'SoapRjForm[a_diag_utama]', value: $('#soaprjform-a_diag_utama_text').val() });
    }
    if ($('#soaprjform-is_icd_x').is(':checked')) {
        diag_utama_length = $('#soaprjform-a_diag_utama').val().length
        values.push({ name: 'SoapRjForm[a_diag_utama_text]', value: $('#soaprjform-a_diag_utama').val() });
    }
    clearTimeout(timer);

    if ($('#soaprjform-subject').val().length < 2 || $('#soaprjform-object').val().length < 2 ||
        diag_utama_length < 2 || $('#soaprjform-planning').val().length < 2) {
        new PNotify({
            title: "Peringatan",
            text: "bagian S / O / A / P hanya diisi satu karakter, minimal input pada field SOAP adalah 2 karakter",
            addclass: "alert alert-warning alert-arrow-right alert-styled-right",
            type: "warning",
            delay: 3000,
            hide: true
        });
        return false;
    }

    $(this).docoForm('click', {
        url: $("#form-soap-cppt").prop('action') + ($('.batal-edit-cppt:not(.hidden)').length ? '&edit=true' : ''),
        method: 'POST',
        skipConfirm: true,
        data: values,
        success: (response) => {
            const { data } = response
            tableCppt.draw()
            skipTimeout = true;
            $(document).ready(function () {
                $("#soaprjform-tgl_soaprj").val(moment(new Date()).format('DD/MM/YYYY HH:mm:00'))
                $('#soaprjform-cppt_id').val(null)
                $('#soaprjform-soaprj_id').val(null)
                $('#soaprjform-subject').val(null)
                $('#soaprjform-object').val(null)
                $('#soaprjform-a_diag_utama_text').val(null)
                $('#soaprjform-planning').val(null)
                $('#soaprjform-a_diag_utama_text').val(null)
                $('#soaprjform-instruksi').val(null)
                $('#soaprjform-a_diag_utama').val(null).trigger('change')
                $('#soaprjform-a_diag_penyerta').val(null).trigger('change')
                $('#soaprjform-catatan_dokter').val(null)
                sessionStorage.removeItem(suggestSoapSessionName)
                $('#patient-history-tab').find('.diagnosa-dokter-text').text(data.a_diag_utama.text)
                $('#soaprjform-is_icd_x').prop('checked', false)
                $('#soaprjform-is_icd_x').uniform()
                $('input[name="SoapRjForm[is_icd_x]"]').val(0)
                $('#soaprjform-a_diag_utama_text').parent().show()
                $('#soaprjform-a_diag_utama').parent().hide()
                docoNotification('success', 'Proses berhasil', 'SOAP berhasil disimpan')
            })
        }
    })
})


$('#soaprjform-is_icd_x').click(function (e) {
    if ($('#soaprjform-is_icd_x').prop('checked')) {
        $('input[name="SoapRjForm[is_icd_x]"]').val(1)
        $('#soaprjform-a_diag_utama_text').parent().hide()
        // $('#soaprjform-a_diag_utama_text').val('')
        $('#soaprjform-a_diag_utama').parent().show()
    } else {
        $('input[name="SoapRjForm[is_icd_x]"]').val(0)
        $('#soaprjform-a_diag_utama_text').parent().show()
        $('#soaprjform-a_diag_utama').parent().hide()
        // $('#soaprjform-a_diag_utama').val(null).trigger('change')
    }
});


$('#form-soap-cppt').on('change keyup', ({ delegateTarget }) => {

    skipTimeout = false
    if (skipTimeout) {
        skipTimeout = false
        return true
    }
    clearTimeout(timer);

    timer = setTimeout(function () {
        var IsIcdX = ''
        var IsIcdXValue = 0
        if ($('#soaprjform-is_icd_x').is(':checked')) {
            IsIcdX = 'checked'
            IsIcdXValue = 1
        }
        let uniformSoaprjformIsIcdX = [{ name: 'uniform-soaprjform-is_icd_x', value: IsIcdX }];
        let soaprjformIsIcdX = [{ name: 'soaprjform-is_icd_x', value: IsIcdXValue }];


        var date = [{ name: "soapRjForm[date]", value: new Date().getTime() }]
        var diag_penyerta = [$('#soaprjform-a_diag_penyerta').serializeArray()]
        var suggest_soap = date.concat(
            uniformSoaprjformIsIcdX,
            soaprjformIsIcdX,
            $('#soaprjform-soaprj_id').serializeArray(),
            $('#soaprjform-subject').serializeArray(),
            $('#soaprjform-object').serializeArray(),
            $('#soaprjform-a_diag_utama_text').serializeArray(),
            $('#soaprjform-a_diag_utama').serializeArray(),
            diag_penyerta,
            $('#soaprjform-planning').serializeArray(),
            $('#soaprjform-catatan_dokter').serializeArray(),
            $('#soaprjform-instruksi').serializeArray(),
        )
        sessionStorage.setItem(suggestSoapSessionName, JSON.stringify(suggest_soap));
    }, 1000);
})
$(document).ready(function () {
    jsRenderDataTable()
    $('#soaprjform-is_icd_x').val(0)
    $('#soaprjform-a_diag_utama').parent().hide()
    //    $('#soaprjform-is_icd_x').uniform()
    //    $('#soaprjform-is_icd_x').click(function(e){
    //         if ($('#soaprjform-is_icd_x').prop('checked')){
    //             $('#soaprjform-a_diag_utama_text').parent().hide()
    //             // $('#soaprjform-a_diag_utama_text').val('')
    //             $('#soaprjform-a_diag_utama').parent().show()
    //         }else{
    //             $('#soaprjform-a_diag_utama_text').parent().show()
    //             $('#soaprjform-a_diag_utama').parent().hide()
    //         // $('#soaprjform-a_diag_utama').val(null).trigger('change')
    //         }
    //     });
    var local = sessionStorage.getItem(suggestSoapSessionName);
    var suggest_storage = JSON.parse(local)
    var load_suggest = true
    loadSuggestSOAP(suggestSoapSessionName, { loadSuggest: load_suggest });

    let _listpemeriksaanpenunjang = [] //variable ini digunakan di popup order penunjang
    let listOrderButton = pelayananConfigButton
    let _buttonHtml = '';
    $.each(listOrderButton, (k, v) => {
        if (v.title.toLowerCase() == 'laboratorium' || v.title.toLowerCase() == 'radiologi' || v.title.toLowerCase() == 'penjadwalan' || v.title.toLowerCase() == 'fisioterapi' || v.title.toLowerCase() == 'darah') {
        } else {

            _buttonHtml += `<button type='button' ${typeof v.disabled != 'undefined' && v.disabled ? 'disabled' : ''} style='margin-right: 5px' data-href="${v.url}" class='btn btn-labeled btn-info btn-xs btn-order-cppt' data-width='${typeof v.width != 'undefined' ? v.width : '90%'}' data-wrapper='${typeof v.wrapper != 'undefined' ? v.wrapper : '#content-cppt'}' data-type='${v.name}'><b><i class='fa ${v.icon}'></i></b> ${v.title}</button>`
        }
    })
    $('#button-wrapper').html(_buttonHtml)
    if (soapDiagnosa.primary != null) {
        var decodeDiagnose = null
        try {
            decodeDiagnose = JSON.parse(soapDiagnosa.primary)
        } catch (error) {
        }
        if (decodeDiagnose != null) {
            var newOption = new Option(decodeDiagnose.text, `${decodeDiagnose.id}_${decodeDiagnose.text}`, false, false);
            skipTimeout = true
            $('#soaprjform-a_diag_utama').append(newOption).trigger('change').val(`${decodeDiagnose.id}_${decodeDiagnose.text}`).trigger('change')
        }
    }

    let _buttonHasilLabHtml = '';
    $.each(listOrderButton, (k, v) => {
        if (v.title.toLowerCase() == 'laboratorium') {
            _buttonHasilLabHtml += `<button type='button' ${typeof v.disabled != 'undefined' && v.disabled ? 'disabled' : ''} style='margin-right: -7px; width:50px;' data-href="${v.url}" class='btn btn-xs btn-only btn-primary-color btn-order-cppt' data-width='${typeof v.width != 'undefined' ? v.width : '90%'}' data-wrapper='${typeof v.wrapper != 'undefined' ? v.wrapper : '#content-cppt'}' data-type='${v.name}'><b><i class='fa ${v.icon}'></i></b></button>`
        } else {

        }
    })
    $('#button-laboratorium').html(_buttonHasilLabHtml)
    if (soapDiagnosa.primary != null) {
        var decodeDiagnose = null
        try {
            decodeDiagnose = JSON.parse(soapDiagnosa.primary)
        } catch (error) {
        }
        if (decodeDiagnose != null) {
            var newOption = new Option(decodeDiagnose.text, `${decodeDiagnose.id}_${decodeDiagnose.text}`, false, false);
            skipTimeout = true
            $('#soaprjform-a_diag_utama').append(newOption).trigger('change').val(`${decodeDiagnose.id}_${decodeDiagnose.text}`).trigger('change')
        }
    }

    let _buttonHasilRadHtml = '';
    $.each(listOrderButton, (k, v) => {
        if (v.title.toLowerCase() == 'radiologi') {
            _buttonHasilRadHtml += `<button type='button' ${typeof v.disabled != 'undefined' && v.disabled ? 'disabled' : ''} style='margin-right: -7px;width:50px;' data-href="${v.url}" class='btn btn-xs btn-only btn-primary-color btn-order-cppt' data-width='${typeof v.width != 'undefined' ? v.width : '90%'}' data-wrapper='${typeof v.wrapper != 'undefined' ? v.wrapper : '#content-cppt'}' data-type='${v.name}'><b><i class='fa ${v.icon}'></i></b></button>`
        } else {

        }
    })
    $('#button-radiologi').html(_buttonHasilRadHtml)
    if (soapDiagnosa.primary != null) {
        var decodeDiagnose = null
        try {
            decodeDiagnose = JSON.parse(soapDiagnosa.primary)
        } catch (error) {
        }
        if (decodeDiagnose != null) {
            var newOption = new Option(decodeDiagnose.text, `${decodeDiagnose.id}_${decodeDiagnose.text}`, false, false);
            skipTimeout = true
            $('#soaprjform-a_diag_utama').append(newOption).trigger('change').val(`${decodeDiagnose.id}_${decodeDiagnose.text}`).trigger('change')
        }
    }



    let _buttonHasilLaporanTerapiHtml = '';
    $.each(listOrderButton, (k, v) => {
        if (v.title.toLowerCase() == 'penjadwalan') {
            _buttonHasilLaporanTerapiHtml += `<button type='button' ${typeof v.disabled != 'undefined' && v.disabled ? 'disabled' : ''} style='margin-left:10px; margin-right: -7px;width:50px;' data-href="${v.url}" class='btn btn-xs btn-only btn-primary-color btn-order-cppt' data-width='${typeof v.width != 'undefined' ? v.width : '90%'}' data-wrapper='${typeof v.wrapper != 'undefined' ? v.wrapper : '#content-cppt'}' data-type='${v.name}'><b><i class='fa ${v.icon}'></i></b></button>`
        } else {

        }
    })
    $('#button-laporan-terapi').html(_buttonHasilLaporanTerapiHtml)
    if (soapDiagnosa.primary != null) {
        var decodeDiagnose = null
        try {
            decodeDiagnose = JSON.parse(soapDiagnosa.primary)
        } catch (error) {
        }
        if (decodeDiagnose != null) {
            var newOption = new Option(decodeDiagnose.text, `${decodeDiagnose.id}_${decodeDiagnose.text}`, false, false);
            skipTimeout = true
            $('#soaprjform-a_diag_utama').append(newOption).trigger('change').val(`${decodeDiagnose.id}_${decodeDiagnose.text}`).trigger('change')
        }
    }

    let _buttonHasilFisioterapiHtml = '';
    $.each(listOrderButton, (k, v) => {
        if (v.title.toLowerCase() == 'fisioterapi') {
            _buttonHasilFisioterapiHtml += `<button type='button' ${typeof v.disabled != 'undefined' && v.disabled ? 'disabled' : ''} style=' margin-right: -7px;width:50px;' data-href="${v.url}" class='btn btn-xs btn-only btn-primary-color btn-order-cppt' data-width='${typeof v.width != 'undefined' ? v.width : '90%'}' data-wrapper='${typeof v.wrapper != 'undefined' ? v.wrapper : '#content-cppt'}' data-type='${v.name}'><b><i class='fa ${v.icon}'></i></b></button>`
        } else {

        }
    })
    $('#button-fisioterapi').html(_buttonHasilFisioterapiHtml)
    if (soapDiagnosa.primary != null) {
        var decodeDiagnose = null
        try {
            decodeDiagnose = JSON.parse(soapDiagnosa.primary)
        } catch (error) {
        }
        if (decodeDiagnose != null) {
            var newOption = new Option(decodeDiagnose.text, `${decodeDiagnose.id}_${decodeDiagnose.text}`, false, false);
            skipTimeout = true
            $('#soaprjform-a_diag_utama').append(newOption).trigger('change').val(`${decodeDiagnose.id}_${decodeDiagnose.text}`).trigger('change')
        }
    }
    if (soapDiagnosa.secondary != null) {
        var decodeDiagnose = null
        try {
            decodeDiagnose = JSON.parse(soapDiagnosa.secondary)
        } catch (error) {
        }
        var selectedOption = []
        decodeDiagnose.map((diagnose) => {
            if (decodeDiagnose != null) {
                var newOption = new Option(diagnose.text, `${diagnose.id}_${diagnose.text}`, false, false);
                $('#soaprjform-a_diag_penyerta').append(newOption)
                selectedOption.push(`${diagnose.id}_${diagnose.text}`)
            }
        })
        skipTimeout = true
        $('#soaprjform-a_diag_penyerta').val(selectedOption).trigger('change')
    }

    if (soapDate != null && soapDate != '') {
        $("#soaprjform-tgl_soaprj").val(moment(soapDate).format('DD/MM/YYYY HH:mm:00'))
    }
    $('.btn-order-cppt').unbind('click')
    $('.btn-order-cppt').bind('click', function ({ currentTarget }) {
        const { type, href, wrapper, width } = $(currentTarget).data()
        let wrapperParents = wrapper.split(' ')[0]
        $(wrapperParents).find('.modal-dialog').css('width', width)
        showLoader('Memuat Halaman...')
        $(wrapper).docoLoad({
            url: href.replace('#pendaftaran_id#', encrytedPendaftaranId).replace('#pasien_id#', pasien_id),
            dataType: 'html',
            success: function (data) {
                hideLoader()
                $(wrapper).parents('.modal').modal('show')
            },
            error: function () {
                hideLoader()
            }
        })
    })

})

$("#btn-cetak-cppt").click(function (e) {
    e.preventDefault();
    pegawai_id = $('#filter-cppt-pegawai_id').val();
    kelompokpegawai_id = $('#filter-cppt-kelompokpegawai_id').val();
    ruangan_id = $('#filter-cppt-ruangan_id').val();
    var tgl_cppt = null;
    if ($('.endDate1').val() != '') {
        tgl_cppt = $('.startDate1').val() + '-' + $('.endDate1').val();
    }
    $("#btn-cetak-cppt").attr("data-url", "/rajal/pemeriksaan/show-popup-pdf?id=" + encrytedPendaftaranId +
        "&pegawai_id=" + pegawai_id +
        "&kelompokpegawai_id=" + kelompokpegawai_id +
        "&filterruangan_id=" + ruangan_id +
        "&tgl_cppt=" + tgl_cppt
    );
});

$('#filter-cppt-ruangan_id,#filter-cppt-pegawai_id,#filter-cppt-kelompokpegawai_id,.endDate1').on('change', function () {
    $('#tb-cppt #filter-cppt-limit').val(5);
    tableCppt.draw();
})

$('#tb-cppt .link-action-cppt-table').on('click', function (e) {
    e.preventDefault();
    if ($(this).data('event') != null && ['show', 'hide'].includes($(this).data('event'))) {
        let limitIncrease = $(this).data('event') == 'show' ? limitDefault : -(limitDefault);
        let limitValue = $(`#tb-cppt tbody > tr.even:visible, tr.odd:visible`).length + limitIncrease;

        if (limitValue > tableCppt.context[0]._iRecordsTotal) {
            limitValue = tableCppt.context[0]._iRecordsTotal
            //disabled button show
            $('.link-action-cppt-table[data-event="show"]').css("visibility", "hidden");
            $('.link-action-cppt-table[data-event="hide"]').css("visibility", "visible");
        } else if (limitValue < limitDefault) {
            limitValue = limitDefault;
            $('.link-action-cppt-table[data-event="hide"]').css("visibility", "hidden");
            $('.link-action-cppt-table[data-event="show"]').css("visibility", "visible");
        } else {
            $('.link-action-cppt-table[data-event="show"]').css("visibility", "visible");
            $('.link-action-cppt-table[data-event="hide"]').css("visibility", "visible");
        }

        showCpptDatatable('tb-cppt', limitValue);
    }
})

$('.startDate1').on('change', function () {
    $('.endDate1').prop('disabled', false)
})

$('.pickadate').pickadate({
    format: 'dd/mm/yyyy',
    formatSubmit: 'yyyy-mm-dd',
});

$('#btn-reset-filter-cppt').on('click', function (e) {
    e.preventDefault();
    $('.startDate1').val(null).trigger('change');
    $('.endDate1').val(null).trigger('change');
    $('.endDate1').prop('disabled', true)
    $('#filter-cppt-ruangan_id').val(null).trigger('change');
    $('#filter-cppt-pegawai_id').val(null).trigger('change');
    $('#filter-cppt-kelompokpegawai_id').val(null).trigger('change');
});

$(document).on('click', '.btn-delete-cppt', function (e) {
    e.preventDefault();

    var cppt_id = $(this).attr('data-soaprj_id');
    $(this).docoForm("delete", {
        url: "/rajal/pemeriksaan/delete-cppt?pendaftaran_id=" + pendaftaran_id + "&cppt_id=" + cppt_id,
        success: function (params) {
            tableCppt.draw();
        }
    });
});

function loadSuggestSOAP(suggestStorageName, options = []) {
    let data = JSON.parse(sessionStorage.getItem(suggestStorageName));
    if (data != null) {
        $.each(data, function () {
            var name = this.name;
            var value = this.value;
            var date = new Date()
            if (name == 'soapRjForm[date]') {
                var waktu_terpakai = (date.getTime() - value) / 60000
                if (waktu_terpakai > time_reset) {
                    sessionStorage.removeItem(suggestSoapSessionName);
                    skipTimeout = true;
                    load_suggest = false;
                    $('#soaprjform-cppt_id').val(null)
                    $('#soaprjform-subject').val(null)
                    $('#soaprjform-object').val(null)
                    $('#soaprjform-planning').val(null)
                    $('#soaprjform-instruksi').val(null)
                    $('#soaprjform-a_diag_utama').val(null).trigger('change')
                    $('#soaprjform-a_diag_penyerta').val(null).trigger('change')
                    $('#soaprjform-catatan_dokter').val(null)
                }

            }
            if (options.loadSuggest != 'undefined' && options.loadSuggest) {
                if (this.length > 0) {
                    $.each(this, function () {
                        var split_text_diag_penyerta = this.value.split("_")
                        text_diag_penyerta = (split_text_diag_penyerta[1] === undefined) ? split_text_diag_penyerta : split_text_diag_penyerta[1]
                        $('#soaprjform-a_diag_penyerta').select2("trigger", "select", {
                            data: { id: this.value, text: text_diag_penyerta }
                        })
                    })
                } else {
                    if (name === undefined) {
                        name = "soaprjform[a_diag_penyerta]"
                    }
                    name = name.toLowerCase();
                    if (name == 'soaprjform[a_diag_utama]') {
                        var split_text = this.value.split("_")
                        text = (split_text[1] === undefined) ? split_text : split_text[1]
                        $('#soaprjform-a_diag_utama')[0].append(new Option(text, this.value, false, false))
                    }
                    if (name == 'soaprjform-is_icd_x' && value == 1) {
                        $('#soaprjform-is_icd_x').val(1)
                        $('#soaprjform-is_icd_x').prop('checked', true)
                    }

                    let res = name.replace('[', '-')
                    res = res.replace(']', '')
                    $(`#${res}`).val(this.value)
                }
            }
        });
        if ($('#soaprjform-is_icd_x').val() == 1) {
            $('#soaprjform-a_diag_utama_text').parent().hide()
            $('#soaprjform-a_diag_utama').parent().show()
        } else {
            $('#soaprjform-a_diag_utama_text').parent().show()
            $('#soaprjform-a_diag_utama').parent().hide()
        }

    }
    if ($('#soaprjform-is_icd_x').val() == 1) {
        $('#soaprjform-a_diag_utama_text').parent().hide()
        $('#soaprjform-a_diag_utama').parent().show()
    } else {
        $('#soaprjform-a_diag_utama_text').parent().show()
        $('#soaprjform-a_diag_utama').parent().hide()
    }

    $('#soaprjform-is_icd_x').uniform();
}

function showCpptDatatable(tableId, limit)
{
    let indexShowed = $(`#${tableId} tbody > tr.even, tr.odd`).length - limit;

    $(`#${tableId} tbody > tr.even, tr.odd`).each(function(index, val){
        if(indexShowed > index){
            $(this).hide();
        }else{
            $(this).show();
        }
    })
}

function jsGetDataTableHeightPx() {
    // set default return height
    var retHeightPx = 1000

    // no nada if there is no dataTable (container) element
    var dataTable = document.getElementById('tb-cppt')
    if (!dataTable) {
        return retHeightPx
    }

    // do nada if we can't determine the browser height
    var pageHeight = $(window).height()
    if (pageHeight < 0) {
        return retHeightPx
    }

    // determine the data table height based upon the browser page height
    var dataTableHeight = pageHeight - 320 //default height
    var dataTablePos = $('#tb-cppt').offset()
    if (dataTablePos != null && dataTablePos.top > 0) {
        // the data table height is the page height minus the top of the data table,
        // minus space for any buttons at the bottom of the page
        dataTableHeight = pageHeight - dataTablePos.top - 120

        // clip height to min. value
        retHeightPx = Math.max(300, dataTableHeight)
    }
    return retHeightPx
}

function jsRenderDataTable() {
    // $("#tb-cppt").clear();

    tableCppt = $('#tb-cppt').DataTable({
        filter: false,
        displayLength: 10,
        processing: true,
        serverSide: true,
        info: false,
        paging: false,
        aaSorting: [],
        ajax: {
            url : baseUrl + _universalCpptUrl + 'get-data-cppt?id=' + encrytedPendaftaranId + '&no_masukpenunjang=' + _no_masukpenunjang,
            data : function(data) {
                data.id = encrytedPendaftaranId;
                data.source = 'cppt';
                data.ruangan_id = $('#filter-cppt-ruangan_id').val();
                data.pegawai_id = $('#filter-cppt-pegawai_id').val();
                data.kelompokpegawai_id = $('#filter-cppt-kelompokpegawai_id').val();
                data.limit = $('#filter-cppt-limit').val();
                var tgl_cppt = null;
                if($('.endDate1').val() != ''){
                    tgl_cppt = $('.startDate1').val()+'-'+$('.endDate1').val();
                }
                data.tanggal_cppt = tgl_cppt
            }
        },
        columns: [
            {
                title: 'No',
                name: 'no',
                data: 'no',
                searchable: false,
                orderable: false,
            },
            {
                title: 'Ruang',
                name: 'tgl_soaprj',
                data: 'ruangan',
                searchable: false,
                orderable: true,
                className: 'vtop',
            },
            {
                title: 'Hasil Asesmen Penatalaksanaan Pasien',
                name: 'penatalaksanaan',
                data: 'penatalaksanaan',
                className: 'wraptext',
                width: '50px',
                searchable: false,
                orderable: false,
            },
            {
                title: 'Instruksi',
                name: 'instruksi',
                data: 'instruksi',
                className: 'wraptext',
                width: '50px',
                searchable: false,
                orderable: false,
            },
            {
                title: 'Aksi',
                data: 'aksi',
                searchable: false,
                orderable: false,
                className: 'vtop',
            },
        ],
        language: {
            emptyTable: emptyTable,
            info: info,
            infoEmpty: infoEmpty,
            infoFiltered: infoFiltered,
            lengthMenu: lengthMenu,
            loadingRecords: loadingRecords,
            processing: processing,
            search: search,
            zeroRecords: zeroRecords,
            aria: {
                sortAscending: sortAscending,
                sortDescending: sortDescending,
            },
        },
        createdRow: (row, data, dataIndex, cells) => {
            if ( $('#soaprjform-soaprj_id').val() == data.rawData.soaprj_id.value) {
                $(row).find('.edit-cppt').addClass('hidden')
            } else {
                $(row).find('.batal-edit-cppt').addClass('hidden')
            }
            if (data.rawData.is_deleted_soap.value) {
                for (var i = 0; i < row.childNodes.length; ++i) {
                    if (i != row.childNodes.length - 1) {
                      $(row.childNodes[i]).addClass('strike-text')
                    }
                  }
            }
            if (data.rawData.is_dokter.value) {
                for (var i = 0; i < row.childNodes.length; ++i) {
                    if (i == 1) {
                        $(row.childNodes[i]).css('background-color', '#A7F8FA')
                        $(row.childNodes[i]).css('color', 'white')
                      }
                  }
            }
        },
        drawCallback: (settings) => {
            if(settings.aoData.length > 5){
                $('.link-action-cppt-table[data-event="show"]').css("visibility", "visible");
                showCpptDatatable(settings.sTableId, 5);
            }else{
                $('.link-action-cppt-table[data-event="show"]').css("visibility", "hidden");
            }
            $('.link-action-cppt-table[data-event="hide"]').css("visibility", "hidden");
            $('#total-cppt-data').html(settings._iRecordsTotal);
            // if(settings._aiDisplay)
            $('.edit-cppt').unbind()
            $('.batal-edit-cppt').unbind()

            $('.edit-cppt').bind('click', ({delegateTarget}) => {
                skipTimeout = true;
                let _cpptIndex = tableCppt.data().toArray().findIndex(cpptData => cpptData.rawData.soaprj_id.value == $(delegateTarget).data('soaprj_id') )
                if ( _cpptIndex < 0) {
                    return null;
                }

                if($(delegateTarget).data('is_icd_x')){
                    $('#soaprjform-a_diag_utama_text').parent().hide()
                    $('#soaprjform-a_diag_utama').parent().show()
                    $('#soaprjform-is_icd_x').prop('checked',true).uniform('refresh');
                }else{
                    $('#soaprjform-a_diag_utama_text').parent().show()
                    $('#soaprjform-a_diag_utama').parent().hide()
                    $('#soaprjform-is_icd_x').prop('checked',false).uniform('refresh');
                }

                if ( $('.edit-cppt').hasClass('hidden') ) {
                    $('.edit-cppt').removeClass('hidden')
                }
                $('.batal-edit-cppt:not(.hidden)').addClass('hidden')

                if($('#soaprjform-soaprj_id').val() != $(delegateTarget).data('soaprj_id')){
                    $("#soaprjform-tgl_soaprj").val(moment(new Date()).format('DD/MM/YYYY HH:mm:00'))
                    $('#soaprjform-soaprj_id').val(null)
                    $('#soaprjform-subject').val(null)
                    $('#soaprjform-object').val(null)
                    $('#soaprjform-planning').val(null)
                    $('#soaprjform-a_diag_utama').val(null).trigger('change')
                    $('#soaprjform-a_diag_penyerta').empty().val(null).trigger('change')
                    $('#soaprjform-catatan_dokter').val(null)

                    const {rawData} = tableCppt.data().toArray()[_cpptIndex]
                        $.each(rawData, (index, cpptItem) => {
                            if ( cpptItem.formId != undefined) {
                                if ( index == 'diagnosa_utama' ) {
                                    if ( Object.keys(cpptItem.value).length ) {
                                        var selectedDiagnosa = `${cpptItem.value.id}_${cpptItem.value.text}`
                                        var newOption = new Option(cpptItem.value.text, selectedDiagnosa, false, false);
                                        $(`#${cpptItem.formId}`).append(newOption).val(selectedDiagnosa).trigger('change')
                                        $('#soaprjform-a_diag_utama_text').val(cpptItem.value.text)
                                    }
                                } else if( index == 'diagnosa_penyerta' ) {
                                    if ( cpptItem.value.length ) {
                                        var selectedOption = []
                                        $.each(cpptItem.value, (index, diagnose) => {
                                            var newOption = new Option(diagnose.text, `${diagnose.id != undefined ? diagnose.id + '_' : '' }${diagnose.text}`, false, false);
                                            $(`#${cpptItem.formId}`).append(newOption)
                                            selectedOption.push(`${diagnose.id != undefined ? diagnose.id + '_' : '' }${diagnose.text}`)
                                        })

                                        $(`#${cpptItem.formId}`).val(selectedOption).trigger('change')
                                    }
                                } else if(index == 'tgl_soaprj') {
                                    $(`#${cpptItem.formId}`).val(moment(cpptItem.value).format('DD/MM/YYYY H:m:00'))
                                }else {
                                    $(`#${cpptItem.formId}`).val(cpptItem.value)
                                }
                            }
                        })
                }
                $(`.edit-cppt[data-soaprj_id=${$(delegateTarget).data('soaprj_id')}]`).addClass('hidden')
                $(`.batal-edit-cppt[data-soaprj_id=${$(delegateTarget).data('soaprj_id')}]`).removeClass('hidden')
            })

            $('.batal-edit-cppt').bind('click', ({delegateTarget}) => {
                skipTimeout = true;
                $("#soaprjform-tgl_soaprj").val(moment(new Date()).format('DD/MM/YYYY HH:mm:00'))
                $('#soaprjform-soaprj_id').val(null)
                $('#soaprjform-subject').val(null)
                $('#soaprjform-object').val(null)
                $('#soaprjform-planning').val(null)
                $('#soaprjform-a_diag_utama').val(null).trigger('change')
                $('#soaprjform-a_diag_penyerta').val(null).trigger('change')
                $('#soaprjform-a_diag_utama_text').val(null)
                $('#soaprjform-catatan_dokter').val(null)
                $('#soaprjform-instruksi').val(null)
                $(`.edit-cppt[data-soaprj_id=${$(delegateTarget).data('soaprj_id')}]`).removeClass('hidden')
                $(`.batal-edit-cppt[data-soaprj_id=${$(delegateTarget).data('soaprj_id')}]`).addClass('hidden')
            })
        }
    })

    if (tableCppt) {
        var height = jsGetDataTableHeightPx() + 'px'
        $('.dataTables_scrollBody:has(#tb-cppt)').css('min-height', height)
    }
}