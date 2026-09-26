var table;
var _dataStatus  = [];
var _dataRuangan = [];
var _dataPenjamin = [];
var _dataCaraBayar = [];
var _dataPegawai = [];
var _dataPelayanan = [];
var _dataKelasPelayanan = [];
var _dataJasa  = [];

$.each(status_bayar, function (index, value) {
    _dataStatus.push({
        id: index,
        text: value,
    });
});

$.each(status_jasa, function (index, value) {
    _dataJasa.push({
        id: index,
        text: value,
    });
});

$.each(ruangan, function (index, value) {
    _dataRuangan.push({
        id: index,
        text: value,
    });
});

$.each(penjamin, function (index, value) {
    _dataPenjamin.push({
        id: index,
        text: value,
    });
});

$.each(cara_bayar, function (index, value) {
    _dataCaraBayar.push({
        id: index,
        text: value,
    });
});

$.each(pegawai, function (index, value) {
    _dataPegawai.push({
        id: index,
        text: value,
    });
});

$.each(kelas_pelayanan, function (index, value) {
    _dataKelasPelayanan.push({
        id: index,
        text: value,
    });
});

$.each(pelayanan, function (index, value) {
    _dataPelayanan.push({
        id: index,
        text: value,
    });
});

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

$(() => {
    table = $("#example").docoTabel({
        filter: false,
        columnDefs: [{
            orderable: false,
            className: 'select-checkbox',
            targets: 0
        }],
        select: {
            style: 'multi',
            selector: 'tr'
        },
        sorting: [[2, "desc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        scrollY: false,
        ajax: {
            url: "/kasir/lap-rekap-jasa-dokter/get-data",
        },
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                defaultContent: '',
            },
            {
                data: null,
                searchable: false,
                orderable: false,
                render: (data, rowElement, rowData, rowAdditionalData) => {
                    var tableInfo = table.page.info()
                    return tableInfo.start + rowAdditionalData.row + 1
                }
            },
            {
                title: "Tanggal Transaksi",
                data: "tgl_tindakan",
                searchable: true,
                orderable: true,
                render: (data) => {
                    return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY")
                }
            },
            {
                title: "Tanggal Pulang",
                data: "tgl_pasienpulang",
                searchable: true,
                orderable: true,
                render: (data) => {
                    return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY")
                }
            },
            {
                title: "Tanggal Bayar Jasa Dokter",
                data: "tgl_flag",
                searchable: true,
                orderable: false,
                render: (data) => {
                    return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY")
                }
            },
            {
                title: "Nama Dokter",
                data: "nama_pegawai",
                searchable: true,
                orderable: true,
                render: (data) => {
                    return data == "" || data == null ? "-" : data
                }
            },
            {
                title: "Status Billing",
                data: "status_bayar",
                searchable: true,
                orderable: true,
                render: (data) => {
                    return data == "" || data == null ? "-" : data.toUpperCase()
                }
            },
            {
                title: "Status Bayar Jasa Dokter",
                data: "flag_jasdok",
                searchable: false,
                orderable: false,
                render: (data) => {
                    return data == "" || data == null ? "-" : data.toUpperCase()
                }
            },
            {
                title: "No Transaksi",
                data: "no_pendaftaran",
                searchable: false,
                render: (data) => {
                    return data == "" || data == null ? "-" : data
                }
            },
            {
                title: "No Rekam Medik",
                data: "no_rekam_medik",
                searchable: false,
                render: (data) => {
                    return data == "" || data == null ? "-" : data
                }
            },
            {
                title: "Nama Pasien",
                data: "nama_pasien",
                searchable: false,
                render: (data) => {
                    return data == "" || data == null ? "-" : data
                }
            },
            {
                title: "Ruangan",
                data: "ruangan_nama",
                searchable: true,
                orderable: true,
                render: (data) => {
                    return data == "" || data == null ? "-" : data
                }
            },
            {
                title: "Tindakan",
                data: "daftartindakan_nama",
                searchable: true,
                orderable: true,
                render: (data) => {
                    return data == "" || data == null ? "-" : data
                }
            },
            {
                title: "Nama Jasa",
                data: "komponentarif_nama",
                searchable: true,
                orderable: true,
                render: (data) => {
                    return data == "" || data == null ? "-" : data.toUpperCase()
                }
            },
            {
                title: "Tarif (Rp.)",
                data: "tarif_tindakan",
                orderable: false,
                searchable: false,
                className: "text-right",
                render: $.fn.dataTable.render.number(".", ",", 0, "")
            },
            {
                title: "Jasa (Rp.)",
                data: "tarif_tindakankomp",
                orderable: false,
                searchable: false,
                className: "text-right",
                render: $.fn.dataTable.render.number(".", ",", 0, "")
            },
            {
                title: "Bruto (Rp.)",
                data: "bruto",
                orderable: false,
                searchable: false,
                className: "text-right",
                render: $.fn.dataTable.render.number(".", ",", 0, "")
            },
            {
                title: "DPP (Rp.)",
                data: "dpp",
                orderable: false,
                searchable: false,
                className: "text-right",
                render: $.fn.dataTable.render.number(".", ",", 0, "")
            },
            {
                title: "Keterangan",
                data: "kondisi",
                searchable: false,
                orderable: true,
                render: (data) => {
                    return data == "" || data == null ? "-" : data
                }
            },
            {
                title: "Jenis Transaksi",
                data: "jenis_transaksi",
                render: (data) => {
                    return data == "" || data == null ? "-" : data
                }
            },
            {
                title: "Cara Bayar",
                data: "carabayar_nama",
                render: (data) => {
                    return data == "" || data == null ? "-" : data
                }
            },
            {
                title: "Penjamin",
                data: "penjamin_nama",
                render: (data) => {
                    return data == "" || data == null ? "-" : data
                }
            },
            {
                title: "Kelas Pelayanan",
                data: "kelaspelayanan_nama",
                render: (data) => {
                    return data == "" || data == null ? "-" : data
                }
            },
            {
                data: "pelayanan",
                render: (data) => {
                    return data == "" || data == null ? "-" : data
                }
            },
        ],
        "drawCallback": function (setting) {
            var response = setting.json;
            var summary = response.summary;
            $("#jasa").html(`<b>Rp. ${docoHelper.convertToRupiah(summary.jasa)}</b>`)
            $("#dpp").html(`<b>Rp. ${docoHelper.convertToRupiah(summary.dpp)}</b>`)
            $("#bruto").html(`<b>Rp. ${docoHelper.convertToRupiah(summary.bruto)}</b>`)
        },
        "preDrawCallback": function (setting) {
            $("#jasa").html(`Loading . . .`)
            $("#dpp").html(`Loading . . .`)
            $("#bruto").html(`Loading . . .`)
        },
        formFilters: [
            {
                fieldName: 'tgl_tindakan',
                label: 'Periode Tanggal Transaksi',
                type: {
                    name: 'rangeDate',
                }
            },
            {
                fieldName: 'tgl_pasienpulang',
                label: 'Periode Tanggal Pulang',
                type: {
                    name: 'rangeDate',
                    payload: {
                        allDate: true
                    }
                }
            },
            {
                fieldName: 'tgl_flag',
                label: 'Periode Tanggal Bayar Jasa Dokter',
                type: {
                    name: 'rangeDate',
                    payload: {
                        allDate: true
                    }
                }
            },
            {
                fieldName: 'dokterpenanggungjawab_id',
                label: 'Nama Dokter',
                type: {
                    name: 'selectMultiple',
                    payload: _dataPegawai
                }
            },
            {
                fieldName: 'status_bayar_id',
                label: 'Pilih Status Billing',
                type: {
                    name: 'selectMultiple',
                    payload: _dataStatus,
                }
            },
            {
                fieldName: 'flag_jasdok',
                label: 'Pilih Status Bayar Jasa Dokter',
                type: {
                    name: 'select',
                    payload: _dataJasa,
                }
            },
            {
                fieldName: "daftartindakan_nama",
                label: "Keterangan",
            },
            {
                fieldName: "komponentarif_nama",
                label: "Nama Jasa",
            },
            {
                fieldName: 'ruangan_id',
                label: 'Pilih Ruangan',
                type: {
                    name: 'selectMultiple',
                    payload: _dataRuangan,
                }
            },
            {
                fieldName: "jenis_transaksi",
                label: "Jenis Transaksi",
            },
            {
                fieldName: 'carabayar_id',
                label: 'Cara Bayar',
                type: {
                    name: 'selectMultiple',
                    payload: _dataCaraBayar
                }
            },
            {
                fieldName: 'penjamin_id',
                label: 'Penjamin',
                type: {
                    name: 'selectMultiple',
                    payload: _dataPenjamin
                }
            },
            {
                fieldName: 'kelaspelayanan_id',
                label: 'Kelas Pelayanan',
                type: {
                    name: 'selectMultiple',
                    payload: _dataKelasPelayanan
                }
            },
            {
                fieldName: 'pelayanan',
                type: {
                    name: 'selectMultiple',
                    payload: _dataPelayanan
                }
            },
        ],
    });
    $(document).on("click", ".btn-reset", function (e) {
        moment.locale("en");
        const tableId = "example";
        const element = $(`#filter-section__${tableId}`)
        const formWrapper = $(`#form-filter__${tableId}`)
        element.find('input').val('')
        element.find('select').val(null).trigger('change')
        // element.find('#tgl_tindakan-startDate').val(moment().format("DD-MMM-YYYY")).trigger("change");
        // element.find('#tgl_tindakan-endDate').val(moment().format("DD-MMM-YYYY")).trigger("change");
        const tableElement = $(`#${tableId}`).DataTable()
        showLoader()
        tableElement.context[0].ajax.data.advancedFilter = serializeArrayToJson(formWrapper)
        tableElement.ajax.reload()
    });
    $(document).on("click", "#example tbody tr", function () {
        var count = table.rows( { selected: true } ).count();
        try {
            primaryKey = table.row('.selected').data().primary ? table.row('.selected').data().primary : null;
        } catch (e) {
            primaryKey = false
        }
        if (primaryKey) {
            if(count == 1) {
                $("#btn-delete-jd").prop("disabled", false);
            } else {
                $("#btn-delete-jd").prop("disabled", true);
            }
        }
        else {
            $("#btn-delete-jd").prop("disabled", true);
        }
        if(count > 0) {
            $("#btn-flag-bayar-jasdok").prop("disabled", false);
        } else {
            $("#btn-flag-bayar-jasdok").prop("disabled", true);
        }
    });
    $("#cetak-rincian-jasdok").click(function (e) {
        e.preventDefault();
        var url = "/kasir/lap-rekap-jasa-dokter/cetak-rincian-jasdok?";
        $(this).attr("data-target", url, "data-table", table);
    });
    $('.flex-1').css('display', 'none');
    if ($(this).find('row row__hidden')) {
        $("#filter-section__example .row__hidden").removeClass("row__hidden");
    }

    $(".pickMe").on( "click", function(e) {
        if ($(this).is( ":checked" )) {
            $("#btn-flag-bayar-jasdok").prop("disabled", false);
            table.rows(  ).select();
        } else {
            $("#btn-flag-bayar-jasdok").prop("disabled", true);
            table.rows(  ).deselect();
        }
    });

    $('#btn-flag-bayar-jasdok').on('click', function (event) {
        var count = table.rows( { selected: true } ).count();
        if(count < 1) {
            return new PNotify({
                title: "Proses Gagal",
                text: "Pilih Transaksi terlebih dahulu",
                addclass: "alert alert-warning alert-arrow-right alert-styled-right",
                type: "warning"
            });
        }
        var header = 'Perhatian !';
        var message = 'Apakah anda yakin ?';
        var label = {
            buttons: {
                'Yes': 'button-yes',
                'No': 'button-no'
            }
        };
        event.preventDefault();
        $.showQuestionDialog(header, message, label, function(reaction) {
            var datas = []; var invalid = []; var completed = [];
            table.rows('.selected').data().map((row) => {
                if(row.status_bayar_id != 348) {
                    invalid.push(row.no_pendaftaran);
                } else if(row.tgl_flag != null) {
                    completed.push(row.no_pendaftaran);
                } else {
                    datas.push(row);
                }
            });

            if (reaction == 'Yes') {
                if(invalid.length > 0) {
                    return new PNotify({
                        title: "Proses Gagal",
                        text: "Transaksi tidak bisa diproses (Transaksi Belum Lunas/Invalid)",
                        addclass: "alert alert-warning alert-arrow-right alert-styled-right",
                        type: "warning"
                    });
                }

                if(completed.length > 0) {
                    return new PNotify({
                        title: "Proses Gagal",
                        text: "Transaksi tidak bisa diproses (Jasa Dokter Sudah Dibayarkan)",
                        addclass: "alert alert-warning alert-arrow-right alert-styled-right",
                        type: "warning"
                    });
                }

                $.ajax({
                    url: '/kasir/lap-rekap-jasa-dokter/flag-bayar',
                    data: { data_flag: datas },
                    method: "POST",
                    type: "json",
                    success: function (res) {
                        table.draw();
                        return new PNotify({
                            title: res.response.title,
                            text: res.response.text,
                            addclass: "alert alert-"+res.response.status+" alert-arrow-right alert-styled-right",
                            type: res.response.status
                        });
                    }
                });
            }
        });
    });
});