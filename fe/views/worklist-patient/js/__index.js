var table
var wardGrouped = {}
var dataType

const navData2 = $(".nav-tab-type.active").data()
    if ((type == 'igd' || type == 'ranap' || type == 'bedah') && typeof navData2 != 'undefined' && typeof navData2.instalasi != 'undefined') {
        wardPayload = wardGrouped[navData2.instalasi]
    }

function konfigFilter(kode){
    return $.ajax({
        url: `/${type}/worklist/get-konfig-worklist?kode=${kode}`,
        async: false,
        type: "get",
        success: function (res) {
            
        },
        error: function() {
            
        }
    });
}

function getWorkspaceRuangan(){
    return $.ajax({
        url: `/${type}/worklist/get-ruangan-worklist`,
        async: false,
        type: "get",
        success: function (res) {
            
        },
        error: function() {
            
        }
    });
}

var filterRuangan = getWorkspaceRuangan().responseJSON.results;
var konfigRuangan = konfigFilter('konfig_worklist_filter_ruangan').responseJSON;
function setFilterRuangan(){
    
    if(konfigRuangan == 'true'){
        localStorage.setItem('FilterTable/table-patient/ruangan_id[]', JSON.stringify(filterRuangan));
    } else if (konfigRuangan == 'false') {
        localStorage.removeItem('FilterTable/table-patient/ruangan_id[]');
    }
}



$(() => {

    let notifPulang = sessionStorage.getItem('notifPulang');
    if (notifPulang != null || notifPulang != undefined) {
        setTimeout(function() {
            let jsonNotif = JSON.parse(notifPulang);
            let title = jsonNotif.title;
            let message = jsonNotif.message;
            new PNotify({
                title: title,
                text: message,
                addclass: 'alert alert-success alert-arrow-right alert-styled-right',
                type: "success",
                delay: 10000
            });
            sessionStorage.removeItem('notifPulang');
        },  500);
    }
    
    $('[rel="tooltip"]').tooltip();
    setFilterRuangan()

    // variable 
    let wardPayload = dropdownData.ruangan
    let stat = dropdownData.status_periksa
    let data_type

    switch (type) {
        case 'ranap':
            data_type = 'ri';
            stat = dropdownData.status_periksa_ranap
            $("#kelaspelayanan_id--filter").show()
            $("#table-patient-kelaspelayanan_id--form").select2({
                data: [{ id: '', text: '- Semua -' }].concat(dropdownData.kelaspelayanan)
            })
            break;
        case 'rajal':
            data_type = 'rj';
            stat = dropdownData.status_periksa_rajal;
            break;
        case 'igd':
            data_type = 'rd';
            stat = dropdownData.status_periksa_igd;
            break;
        case 'bedah':
            data_type = 'ot';
            stat = dropdownData.status_periksa_ot;
            break;
        default:
            data_type = null;
            break;
    }

    dataType = data_type

    // grouping data
    dropdownData.ruangan.map((ward) => {
        if (typeof wardGrouped[ward.instalasi_id] == 'undefined') {
            wardGrouped[ward.instalasi_id] = []
        }
        wardGrouped[ward.instalasi_id].push(ward)
    })
    const navData = $(".nav-tab-type.active").data()
    if ((type == 'igd' || type == 'ranap' || type == 'bedah') && typeof navData != 'undefined' && typeof navData.instalasi != 'undefined') {
        wardPayload = wardGrouped[navData.instalasi]
    }
    table = $('#table-patient').docoTabel({
        cacheFilter: true,
        filter: false,
        info: false,
        sorting: defaultSorting,
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollY: false,
        scrollX: true,

        ajax: {
            url: `/${type}/worklist/datatable?tabType=${data_type}&is_nosep=null`,
        },
        columns: [
            {
                data: null,
                orderable: false,
                render: (data, rowElement, rowData, rowAdditionalData) => {
                    var tableInfo = table.page.info()
                    return tableInfo.start + rowAdditionalData.row + 1
                }
            },
            {
                data: null,
                orderable: false,
                className: 'btn-action-column',
                render: (data, rowElement, rowData) => {
                    return rowData.action
                }
            },
            {
                data: 'status_periksa_nama',
                orderable: false,
                render: (data, rowElement, rowData) => {
                    return rowData.status_periksa_nama === '' || rowData.status_periksa_nama === null ? '-' : rowData.status_periksa_nama.toLowerCase().replace(/\b(\w)/g, s => s.toUpperCase());
                }
            },
            {
                data: 'tgl_pendaftaran',
                orderable: true,
                render: (data, rowElement, rowData) => {
                    return `
                        <p style="margin-bottom: 2px"> ${moment(data).format('DD-MM-YYYY HH:mm:ss')} </p>
                        <p> Petugas: ${rowData.peg_create_nama != null ? rowData.peg_create_nama : '-'} </p>
                    `
                }
            },
            {
                data: 'nama_pegawai',
                orderable: false,
                render: (data, rowElement, rowData) => {
                    let _format = rowData.ruangan_nama + (rowData.kamarruangan_nokamar != null ? ' - ' + rowData.kamarruangan_nokamar : '')
                    if (rowData.jenis == 'RI') {
                        _format = (rowData.kelaspelayanan_nama != null ? rowData.kelaspelayanan_nama : '') + " / " + (rowData.kamarruangan_nokamar != null ? rowData.kamarruangan_nokamar : '')
                        if (rowData.hak_kelas !== null) {
                            _format = (rowData.hak_kelas != null ? rowData.hak_kelas : '') + " / " + (rowData.kamarruangan_nokamar != null ? rowData.kamarruangan_nokamar : '')
                        }
                    } else if (rowData.jenis == 'RD') {
                        _format = rowData.ruangan_nama + ' / ' + (rowData.no_tempattidur != null && !rowData.is_pulang ? rowData.no_tempattidur : '-')
                    }
                    return `
                        <p style="margin-bottom: 2px">${data}</p>
                        <p><b>${_format}</b></p>
                        `
                }
            },
            {
                data: 'konsulpoli_dokter_nama',
                orderable: false
            },
            {
                data: 'nama_pasien',
                orderable: false,
                visible: false
            },
            {
                data: 'tanggal_lahir',
                render: (data) => {
                    return data == null ? '-' : moment(data).format('DD-MM-YYYY')
                },
                orderable: false,
                visible: false
            },
            {
                data: 'jk',
                orderable: false,
                searchable: false,
                visible: false
            },
            {
                data: 'no_pendaftaran',
                orderable: true,
                render: (data, rowElement, rowData) => {
                    if(rowData.nama_depan == null){
                        rowData.nama_depan = '';
                    }
                    return `
                    <p style="margin-bottom: 2px"> 
                        <b>${rowData.nama_depan} ${rowData.nama_pasien} (${rowData.jk})</b>
                        ${(rowData.riwayat_alergi != null && rowData.riwayat_alergi != '' ) || (rowData.catatanpenting_pasien != null && rowData.catatanpenting_pasien != '') ? rowData.alergi_catatan : ''}
                    </p>
                    <p style="margin-bottom: 2px"> ${rowData.tanggal_lahir == null ? '-' : moment(rowData.tanggal_lahir).format('DD-MM-YYYY')} / ${rowData.no_telepon}</p>
                    <p> ${rowData.no_pendaftaran} / ${rowData.no_rekam_medik}</p>
                    `
                }
            },
            {
                data: 'carabayar_nama',
                orderable: false,
                render: (data, rowElement, rowData) => {
                    return `${data != null ? data : ''} / ${rowData.penjamin_nama != null ? rowData.penjamin_nama : ''}`
                }
            },
            {
                data: 'nosep',
                orderable: false,
                render: (data) => {
                    return data == null ? '-' : data
                },
            },
            {
                data: 'hak_kelas',
                orderable: false,
                render: (data, rowElement, rowData) => {
                    // let _wording = `${data != null ? data : '-'} / ${rowData.kelaspelayanan_nama != null ? rowData.kelaspelayanan_nama : ''} / ${rowData.kelas_tagihan}`
                    let _wording = `${rowData.kelaspelayanan_nama != null ? rowData.kelaspelayanan_nama : ''} / ${rowData.kelas_tagihan != null ? rowData.kelas_tagihan : '-'}`
                    if (rowData.jenis == 'RI') {
                        if (rowData.is_pasientitipan) {
                            _wording = `${data != null ? data : ''} / ${rowData.kelas_tagihan != null ? rowData.kelas_tagihan : '-'}`
                        } else if (data != null) {
                            _wording = `${rowData.kelaspelayanan_nama != null ? rowData.kelaspelayanan_nama : ''} / ${rowData.kelas_tagihan != null ? rowData.kelas_tagihan : '-'}`
                        }
                    }
                    return _wording
                }
            },
            {
                data: 'status_kamar',
                orderable: false,
            },
            {
                data: 'jeniskasuspenyakit_nama',
                orderable: false,
                visible: false,
            },
            {
                data: 'ruangan_nama',
                orderable: true,
                visible: false,
                render: (data, rowElement, rowData) => {
                    return `${rowData?.ruangan_nama} ${rowData?.kamarruangan_nokamar} - ${rowData?.no_tempattidur}`
                }
            },
            {
                data: 'tgl_pindahkamar',
                orderable: false,
                render: (data) => {
                    return data != null ? moment(data).format('DD-MM-YYYY') : '-'
                }
            },
            {
                data: 'rencana_pulang',
                orderable: false,
            },
            {
                data: 'nosep',
                orderable: false,
                searchable: false,
                visible: false,
            },
        ],
        formFilters: [
            {
                fieldName: 'status_periksa_nama',
                label: 'Status Periksa',
                type: {
                    name: 'select',
                    payload: stat
                }

            },
            {
                fieldName: 'ruangan_id',
                label: 'Nama Ruangan',
                type: {
                    name: 'selectMultiple',
                    payload: wardPayload
                }
            },
            'no_rekam_medik',
            {
                fieldName: 'tgl_pendaftaran',
                label: 'Tanggal Pendaftaran',
                type: {
                    name: 'rangeDate',
                    payload: {
                        startDate: moment().add('-30', 'days').locale('en').format('DD-MMM-YYYY'),
                        endDate: moment().locale('en').format('DD-MMM-YYYY'),
                    }
                }
            },
            'no_pendaftaran',
            {
                fieldName: 'pegawai_id',
                label: 'Dokter',
                type: {
                    name: 'dropdownScroll',
                    url: `/${type}/worklist/filters`,
                    additionalPayload: {
                        type: 'dokter'
                    }
                }
            },
            'nama_pasien',
            {
                fieldName: 'carabayar_id',
                label: 'Cara Bayar',
                type: {
                    name: 'select',
                    payload: dropdownData.carabayar
                }
            },
            {
                fieldName: 'penjamin_id',
                label: 'Penjamin',
                type: {
                    name: 'select',
                }
            },
            'hak_kelas',
            {
                fieldName: 'jeniskasuspenyakit_id',
                label: 'Kasus Penyakit',
                type: {
                    name: 'select',
                    payload: dropdownData.jeniskasuspenyakit
                }
            },
            {
                fieldName: 'kelaspelayanan_id',
                label: 'Kelas dirawat',
                type: {
                    name: 'select',
                    payload: dropdownData.kelaspelayanan
                }
            },
        ],
        filterRendered: (wrapper) => {
            $(wrapper).find('[name="carabayar_id"]').bind('change', ({ currentTarget }) => {
                if ($(currentTarget).val() == '' || $(currentTarget).val() == null) {
                    var data = [
                        {
                            id: '',
                            text: '- Semua -'
                        }
                    ]
                    $(wrapper).find('[name="penjamin_id"]').html('')
                    $(wrapper).find('[name="penjamin_id"]').select2({
                        data,
                    })
                } else {
                    $.ajax({
                        url: `/${type}/worklist/filters`,
                        data: {
                            type: 'penjamin',
                            additionalPayload: {
                                carabayar_id: $(currentTarget).val()
                            }
                        },
                        success: (res) => {
                            // let tableId = table.tables().nodes().to$().attr('id')
                            // let sessionPenjamin = `FilterTable/${tableId}/penjamin_id`
                            // var data = [
                            //     {
                            //         id: '',
                            //         text: '- Semua -'
                            //     }
                            // ]
                            // data = data.concat(res.data)
                            // $(wrapper).find('[name="penjamin_id"]').html('')
                            // $(wrapper).find('[name="penjamin_id"]').select2({
                            //     data,
                            // })
                            // $(wrapper).find('[name="penjamin_id"]').val(sessionStorage.getItem(sessionPenjamin)).trigger('change')

                            let filterPenjamin = localStorage.getItem(`FilterTable/${table.tables().nodes().to$().attr('id')}/penjamin_id`);
                            let data = [
                                {
                                    id: '',
                                    text: '- Semua -'
                                }
                            ]

                            data = data.concat(res.data)
                            $(wrapper).find('[name="penjamin_id"]').html('')
                            $(wrapper).find('[name="penjamin_id"]').select2({
                                data,
                            })
                            if (filterPenjamin) {
                                $(wrapper).find('[name="penjamin_id"]').val(JSON.parse(filterPenjamin).value).trigger('change')
                            }
                        }
                    })
                }
            })
        },

    })


    // call function clik default
    clickNavbarDefault(data_type);


    // call funtion form filter status periksa default 
    // formFilterStatusPeriksa(stat);
});


$(document).off('click', '.btn-reset--datatable').on('click', '#btn-reset__table-patient', ({ currentTarget }) => {

    const tableId = $(currentTarget).data('table-id');
    RemoveFilterSession(tableId)
    
    const element = $(`#filter-section__${tableId}`);
    const formWrapper = $(`#form-filter__${tableId}`);
    element.find('input').val('');
    element.find('select').val(null).trigger('change', {'elemfrom':currentTarget});
    element.find('#tgl_pendaftaran-startDate').val(moment().add('-30', 'days').locale('en').format("DD-MMM-YYYY")).trigger("change");
    element.find('#tgl_pendaftaran-endDate').val(moment().locale('en').format("DD-MMM-YYYY")).trigger("change");
    const tableElement = $(`#${tableId}`).DataTable();
    showLoader();
    tableElement.order(defaultSorting);
    if(konfigRuangan == 'true'){
        formWrapper.find('select#table-patient-ruangan_id--form').val(filterRuangan[0].value).trigger('change', {'elemfrom':currentTarget})
    }
    tableElement.context[0].ajax.data.advancedFilter =
    serializeArrayToJson(formWrapper);
    tableElement.ajax.reload();
})

function clickNavbarDefault(data_type) {
    $(`.nav-item[data-type=${data_type}]`).addClass('active')
}

$(".nav-tab-type").bind('click', ({ currentTarget }) => {
    /**
     * * active inactive class navbar
     */
    $(".nav-item").removeClass('active')
    $(currentTarget).addClass('active')

    /**
     * * datatable
     * 
     */
    const data = $(currentTarget).data()
    $("#table-patient-ruangan_id--form").html('')
    $("#table-patient-ruangan_id--form").select2({
        data: [{ id: '', text: '- Semua -' }].concat(typeof data.instalasi != 'undefined' && typeof wardGrouped[data.instalasi] != 'undefined' ? wardGrouped[data.instalasi] : dropdownData.ruangan)
    })
    $("#kelaspelayanan_id--filter").hide()
    $("#table-patient-kelaspelayanan_id--form").html('')

    table.columns(11).visible(true); //show hak kelas selain appointment

    // function form filter 
    formSelectTwoStatusPeriksa(data.instalasi)

    showLoader()
    table.context[0].ajax.data.tabType = data.type != 'all' ? data.type : ''
    table.context[0].ajax.data.advancedFilter = ''
    table.ajax.reload();

    let hideColumns = [15, 16]
    let columnHakKelasHide = [12]
    let column = table.columns(hideColumns);
    let columnHakKelas = table.columns(columnHakKelasHide);
    column.visible(true);
    columnHakKelas.visible(true);
    $(table.column(12).header()).text('Kelas Ditempati / Penjamin')
    // jngn dihapus
    if ($.inArray(data.type, ['ri', 'all']) >= 0) {
        column.visible(true);
        $(table.column(12).header()).text('Kelas Ditempati / Penjamin')
    } else {
        column.visible(false);
        $(table.column(12).header()).text('Kelas Ditempati / Penjamin')
    }

    if (data.type == 'ol') {
        $('#tgl_pendaftaran--filter > div > label').html('Tanggal Kunjungan :')
    } else {
        $('#tgl_pendaftaran--filter > div > label').html('Tanggal Pendaftaran :')
    }
})

function formSelectTwoStatusPeriksa(data) {
    switch (data) {
        case 1:
            stat = dropdownData.status_periksa_rajal
            break;
        case 2:
            stat = dropdownData.status_periksa_igd
            break;
        case 3:
            stat = dropdownData.status_periksa_ranap
            $("#kelaspelayanan_id--filter").show()
            $("#table-patient-kelaspelayanan_id--form").select2({
                data: [{ id: '', text: '- Semua -' }].concat(dropdownData.kelaspelayanan)
            })
            break;
        case 12:
            stat = dropdownData.status_periksa_ot
            break;
        case 21:
            stat = dropdownData.status_periksa_mcu
            break;
        case '':
            stat = dropdownData.status_periksa_ol
            $("#table-patient-ruangan_id--form").html('')
            $("#table-patient-ruangan_id--form").select2({
                data: [{ id: '', text: '- Semua -' }].concat(wardGrouped[1])
            })
            table.columns(11).visible(false);
            break;
        case 'all':
            stat = dropdownData.status_periksa
            break;
        default:
            $("#kelaspelayanan_id--filter").show()
            $("#table-patient-kelaspelayanan_id--form").select2({
                data: [{ id: '', text: '- Semua -' }].concat(dropdownData.kelaspelayanan)
            })
            break;
    }
    formFilterStatusPeriksa(stat);
}

// form filter status periksa 
function formFilterStatusPeriksa(status) {
    $("#table-patient-status_periksa_nama--form").html('')
    $("#table-patient-status_periksa_nama--form").select2({
        data: [{ id: '', text: '' }, { id: '0', text: '- Semua -' }].concat(status),
        placeholder: "",

    })
}

$(document).on('change', '#form-filter__table-patient select', function (e, x) {
    if (typeof x == 'undefined' || typeof x.elemfrom == 'undefined' || x.elemfrom == null) {
        if ($.fn.DataTable.isDataTable('#table-patient')) {
            $('#btn-search__table-patient').trigger('click');
        }
    }
})

$(document).on('keydown', '#form-filter__table-patient input', function (e, x) {
    if (typeof x == 'undefined' || typeof x.elemfrom == 'undefined' || x.elemfrom == null) {
        if ($.fn.DataTable.isDataTable('#table-patient')) {
            if (e.keyCode === 13) {
                $('#btn-search__table-patient').trigger('click');
                e.stopPropagation();
            }
        }
    }
})

$(document).on('focusout', '#tgl_pendaftaran-endDate,#tgl_pendaftaran-startDate', function (e, x) {
    if (typeof x == 'undefined' || typeof x.elemfrom == 'undefined' || x.elemfrom == null) {
        setTimeout(function (eve) {
            if ($.fn.DataTable.isDataTable('#table-patient') && $('#AnyTime--tgl_pendaftaran-startDate').is(":hidden") && $('#AnyTime--tgl_pendaftaran-endDate').is(":hidden")) {
                $('#btn-search__table-patient').trigger('click');
            }
        }, 500);
    }
})

$(document).on("click", "#table-patient tr button.antrian", function (event) {
    var isTrusted = event?.originalEvent?.isTrusted;
    if(typeof isTrusted !== 'undefined'){
        const pendaftaran_id = $(this).attr("data-id");
        const antrian_id = $(this).attr("data-antrianId");
        const no_antrian = $(this).attr("data-antrian");
        const nama_pasien = $(this).attr("data-namapasien");
        const ruangan_id = $(this).attr("data-ruanganid");
        const pegawai_id = $(this).attr("data-pegawaiid");
        const dataPost = {
            pendaftaran_id: pendaftaran_id,
            antrian_id: antrian_id,
            no_antrian: no_antrian,
            nama_pasien: nama_pasien,
            ruangan_id: ruangan_id,
            pegawai_id: pegawai_id
        };
        $.ajax({
            url: "/rajal/pemeriksaan/panggil-antrian",
            data: dataPost,
            type: "post",
            success: function (res) {
                if (typeof res.teks_panggil !== "undefined") {
                    let text = res.teks_panggil;
                    let player = $("#playerAudio");
                    let arrayText = text.split(" ");
                    arrayText.push("stop");
                    arrayText = arrayText.filter(Boolean);

                    let index = 0;

                    player[0].defaultPlaybackRate = 1;
                    player[0].src = `${window.location.origin}/media/sounds/${arrayText[index]}.wav`;
                    player[0].play();

                    player[0].addEventListener("ended", function () {
                        index = index + 1;

                        if (index < arrayText.length) {
                            player[0].defaultPlaybackRate = index == arrayText.length - 3 ? 1.5 : 1.2;
                            if (arrayText[index] == "stop") {
                                // hapusAntrianAudio();
                            } else {
                                player[0].src = window.location.origin + "/media/sounds/" + arrayText[index] + ".wav";
                                player[0].play();
                            }
                        }
                    });
                }
            }
        });
    }
});

var _legend_info = ".legend-information"
var _table_ajax_url = ''

$(_legend_info).css('cursor', 'pointer');
$(_legend_info).removeClass('active');
$(_legend_info).on('click', function () {
    if ($(this).hasClass('active') === true) {
        $(this).removeClass('active');
        $(this).css('border-color', '#dddddd');
        _dt_carabayar_id = $(this).data("carabayar_id");
        if(typeof _dt_carabayar_id !== 'undefined'){
            $("#advanced-filter-table-patient").find('[name="carabayar_id"]').val("").trigger('change');
            showLoader()
            return;
        }
        _table_ajax_url = `/${type}/worklist/datatable?tabType=${dataType}`
        showLoader()
        table.ajax.url(_table_ajax_url).load()
    } else {
        $(_legend_info).removeClass('active');
        $(_legend_info).css('border-color', '#dddddd');
        $(this).addClass('active');
        $(this).css('border-color', '#04aa6d');
        _dt_type = $(this).data("type")
        _dt_carabayar_id = $(this).data("carabayar_id");
        switch (_dt_type) {
            case 'pasien_konsul':
                _table_ajax_url = `/${type}/worklist/datatable?tabType=${dataType}&is_konsul=true`
                break;
            case 'pasien_titipan':
                _table_ajax_url = `/${type}/worklist/datatable?tabType=${dataType}&is_pasientitipan=true`
                break;
            case 'stop_akomodasi':
                _table_ajax_url = `/${type}/worklist/datatable?tabType=${dataType}&is_stopakomodasi=true`
                break;
            case 'belum_soap':
                _table_ajax_url = `/${type}/worklist/datatable?tabType=${dataType}&is_isisoap=false`
                break;
            case 'stop_akomodasi_lunas':
                _table_ajax_url = `/${type}/worklist/datatable?tabType=${dataType}&is_lunas=true&is_stopakomodasi=true`
                break;
            case 'belum_terbit_sep':
                _table_ajax_url = `/${type}/worklist/datatable?tabType=${dataType}&is_nosep=false`
                break;
            case 'sudah_terbit_sep':
                _table_ajax_url = `/${type}/worklist/datatable?tabType=${dataType}&is_nosep=true`
                break;
            default:
                if(typeof _dt_carabayar_id !== 'undefined'){
                    $("#advanced-filter-table-patient").find('[name="carabayar_id"]').val(_dt_carabayar_id).trigger('change');
                    showLoader()
                    return;
                }
                _table_ajax_url = `/${type}/worklist/datatable?tabType=${dataType}`;
                break;
        }
        showLoader()
        table.ajax.url(_table_ajax_url).load()
    }
});