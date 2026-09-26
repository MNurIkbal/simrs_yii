var table;
var id = "";
var state = "";
const progress = $(".progress");

$(document).ready(function () {
    $(".btn-proses").attr("disabled", true);
    table = $("#example").docoTabel({
        filter: false,
        columnDefs: [{
            orderable: false,
            className: "select-checkbox",
            targets: 0
        }],
        select: {
            style: "single",
            selector: "tr"
        },
        sorting: [[0, "asc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        scrollY: true,
        ajax: {
            url: "/penjamin-asuransi/informasi-pasien-rajal-bpjs/get-data",
        },
        stateSave: false,
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                defaultContent: "", // 0
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
                title: "Tgl Masuk / Tanggal Keluar",
                data: "tgl_pendaftaran",
                render: (data, rowElement, rowData) => {
                    return `
                        <p style="margin-bottom: 2px"> Tanggal Masuk : ${rowData.tgl_pendaftaran != null ? moment(rowData.tgl_pendaftaran).format("DD MMM YYYY") : '-'} </p>
                        <p style="margin-bottom: 2px"> Tanggal Keluar : ${rowData.tgl_pulang != null ? moment(rowData.tgl_pulang).format("DD MMM YYYY") : '-'} </p>
                    `
                }
            },
            {
                title: "Data Pasien",
                data: "nama_pasien",
                render: (data, rowElement, rowData) => {
                    let _gender = (rowData.jenis_kelamin == 16) ? 'P' : 'L';
                    return `
                        <p style="margin-bottom: 2px"> ${rowData.nama_pasien != null ? rowData.nama_pasien : ''} (${_gender != null ? _gender : '-'})</p>
                        <p style="margin-bottom: 2px"> No. Registrasi : ${rowData.no_pendaftaran != null ? rowData.no_pendaftaran : '-'} </p>
                        <p style="margin-bottom: 2px"> No RM : ${rowData.no_rekammedik != null ? rowData.no_rekammedik : '-'}</p>
                    `
                }
            },
            {
                title: "Instalasi / Ruangan",
                data: "instalasi_nama",
                render: (data, rowElement, rowData) => {
                    return `
                        <p style="margin-bottom: 2px"> ${rowData.instalasi_nama != null ? rowData.instalasi_nama : '-'}</p>
                        <p style="margin-bottom: 2px"> ${rowData.ruangan_nama != null ? rowData.ruangan_nama : '-'}</p>
                    `
                }
            },
            {
                title: "Cara Bayar / Penjamin",
                data: "carabayar_nama",
                render: (data, rowElement, rowData) => {
                    return `
                        <p style="margin-bottom: 2px"> ${rowData.carabayar_nama != null ? rowData.carabayar_nama : '-'}</p>
                        <p style="margin-bottom: 2px"> ${rowData.penjamin_nama != null ? rowData.penjamin_nama : '-'}</p>
                    `
                 }
            },
            {
                title: "Dokter Penanggung<br> Jawab",
                data: "dokter_nama",
                render: (data) => {
                    return data == "" || data == null ? "-" : data
                }
            },
            {
                title: "Status",
                data: "verifikasi",
                render: (data) => {
                    return data == "" || data == null ? "-" : data
                }
            },
        ],
        drawCallback: function (e) {
            var api = this.api();
            for (var i = 0; api.rows().count() > i; i++) {
                var rowData = api.row(i).data();
                var rowNode = api.row(i).node();
                if (rowData.status_kunjungan == 549) {
                    $(rowNode).removeClass("final-klaim");
                    $(rowNode).removeClass("proses-klaim");
                    $(rowNode).removeClass("sudah-koreksi");
                    $(rowNode).addClass("belum-koreksi");
                } else if (rowData.status_kunjungan == 550) {
                    $(rowNode).removeClass("final-klaim");
                    $(rowNode).removeClass("proses-klaim");
                    $(rowNode).removeClass("belum-koreksi");
                    $(rowNode).addClass("sudah-koreksi");
                } else if (rowData.status_kunjungan == 556) {
                    $(rowNode).removeClass("final-klaim");
                    $(rowNode).removeClass("sudah-koreksi");
                    $(rowNode).removeClass("belum-koreksi");
                    $(rowNode).addClass("proses-klaim");
                } else if (rowData.status_kunjungan == 551) {
                    $(rowNode).removeClass("proses-klaim");
                    $(rowNode).removeClass("sudah-koreksi");
                    $(rowNode).removeClass("belum-koreksi");
                    $(rowNode).addClass("final-klaim");
                }
            }
        },
        formFilters: [
            {
                fieldName: 'tgl_pulang',
                label: 'Tanggal Pulang',
                type: {
                   name: 'rangeDate',
                }
            },
            {
                fieldName: 'tgl_pendaftaran',
                label: 'Tanggal Masuk',
                type: {
                   name: 'rangeDate',
                   payload: {
                    allDate: true,
                   }
                }
            },
            'no_pendaftaran',
            {
                fieldName: 'nama_pasien',
                label: 'Nama Pasien'
            },
            {
                fieldName: 'no_rekamedik',
                label: 'No.Rekam Medik'
            },
            // {
            //     fieldName: 'ruangan_id',
            //     label: 'Ruangan',
            //     type: {
            //         name: 'dropdownScroll',
            //         url: "/penjamin-asuransi/informasi-pasien-rajal-bpjs/filters",
            //         additionalPayload: {
            //             type: 'ruangan',
            //         }
            //     }
            // },
            {
                fieldName: 'status_kunjungan',
                label: 'Status',
                type: {
                    name: 'dropdownScroll',
                    url: "/penjamin-asuransi/informasi-pasien-rajal-bpjs/filters",
                    additionalPayload: {
                        type: 'status',
                    }
                }
            },
        ],
    });
    $('#btn-search__example').css('display', 'none');
    $('#btn-reset__example').css('display', 'none');
});

$(document).on("click", ".btn-reset", function (e) {
    const tableId = "example";
    const element = $(`#filter-section__${tableId}`)
    const formWrapper = $(`#form-filter__${tableId}`)
    element.find('input').val('')
    element.find('select').val(null).trigger('change')
    element.find('#tgl_pulang-startDate').val(moment().locale('en').format("DD-MMM-YYYY")).trigger("change");
    element.find('#tgl_pulang-endDate').val(moment().locale('en').format("DD-MMM-YYYY")).trigger("change");
    const tableElement = $(`#${tableId}`).DataTable()
    tableElement.context[0].ajax.data.advancedFilter = serializeArrayToJson(formWrapper)
    tableElement.ajax.reload()
 });

$(document).on("click", "#btn-proses", function (e) {
    event.preventDefault();
    var no_sep = null;
    if (table.row(".selected").length) {
        id = table.row(".selected").data().primary;
        no_sep = table.row(".selected").data().nosep;
        no_pendaftaran = table.row(".selected").data().no_pendaftaran;
        if(no_sep == '' || no_sep == null) {
            docoNotification('error', 'Proses Gagal.', 'Data ini belum memiliki No.SEP.')
            return false;
        }

        let status = table.row(".selected").data().verifikasi;

        if(status.toLowerCase() == 'sudah dikoreksi') {
            window.open(baseUrl + `penjamin-asuransi/informasi-pasien-rajal-bpjs/eklaim?id=${id}&updated=MQ`);
            return false
        }

        if(status.toLowerCase() == 'proses klaim') {
            window.open(baseUrl + `penjamin-asuransi/informasi-pasien-rajal-bpjs/eklaim?id=${id}&updated=MQ`);
            return false
        }
            
        if(status.toLowerCase() == 'sudah final klaim') {
            window.open(baseUrl + `penjamin-asuransi/informasi-pasien-rajal-bpjs/eklaim?id=${id}&updated=MQ`);
        }else{
            window.open(baseUrl + `penjamin-asuransi/informasi-pasien-rajal-bpjs/proses?id=${id}&no_pendaftaran=${no_pendaftaran}`);
        }
    }
});

$(document).on("click", "#example tbody tr", function () {
    let status_kunjungan_id = null;
    if (table.row(".selected").length) {
        state = table.row(".selected").data().state;
        id = table.row(".selected").data().primary;
        status_kunjungan_id = table.row(".selected").data().status_kunjungan;
    }
    if (!status_kunjungan_id && status_kunjungan_id !== STATUS_VERIFIKASI_BPJS_FNL) {
        $(".btn-proses").attr("disabled", true)
    }
    else {
        $(".btn-proses").attr("disabled", false);
    }
})

progress.css("display", "none")

const showInfo = () => {
    return new Promise((resolve) => {
        setTimeout(() => {
            resolve($(".populate-data").html(`mempersiapkan data ...`))
        }, 1000);
        setTimeout(() => {
            resolve($(".populate-data").css("display", "none"))
            resolve(progress.css("display", "block"))
            resolve($(".label-progress").html(`<p style="font-size:16px;font-weight:bold;"> menyiapkan data ... </p>`))
        }, 2000);
    })
}

const setPresentase = function(progress) {
    $(".progress .label-persentase").html(progress)
    $(".progress .progress-bar").css("width", progress +"%")
    .attr("aria-valuenow", progress)
    .attr("aria-volume", progress);
}

async function updateProgressBarSinkron(randString) {
    let config = await $.getJSON("./../../json/setup.json")
    if (config.origin == "true") {
        var socket = io.connect(window.location.origin);
    } else {
        var socket = io.connect(config.ip+':'+config.port);
    }

    const channel = `export-excel:`
    await showInfo()
    $.ajax({
        url : '/penjamin-asuransi/informasi-pasien-rajal-bpjs/sinkron?randString=' + randString,
        success : function (data) {
            let startNum = 5
            setPresentase(startNum)
            let totalProgres = parseInt(startNum) + parseInt(data.totalPerPage) + 20;
            $(".label-progress").html(`<p style="font-size:16px;font-weight:bold;">Sinkronisasi sedang berjalan </p>`);
            socket.on(channel + data.randString, (message) => {
                const _data = $.parseJSON(message);
                const { status , messageProcess , progress} = _data
                if(status == 'finish') {
                    $(".label-progress").html(`<p style="font-size:16px;font-weight:bold;">${messageProcess}</p>`)
                    setPresentase(progress)
                    if (progress == 100) {
                        table.draw();
                        docoNotification("success", "Proses Berhasil", "Data Berhasil Tersinkronisasi");
                        $('#modal_progress').modal('hide');
                    }
                } else if (status == 'finish') {
                    docoNotification('error','Proses Gagal!', messageProcess)
                } else {
                    startNum++
                    setPresentase(Math.ceil((startNum/totalProgres) * 100))
                    $(".label-progress").html(`<p style="font-size:16px;font-weight:bold;">Sinkronisasi sedang berjalan  </p>`)
                }
            });

        }
    });
}

$(document).on("click", "#btn-sync", function (event) {
    // TODOS:
    event.preventDefault();
    var header = "Perhatian!";
    var message = "Apakah anda yakin untuk melakukan sinkronisasi?";
    var label = {
        buttons: {
            "Yes": "button-yes",
            "No": "button-no"
        },
    };

    $.showQuestionDialog(header, message, label, function (reaction) {
        if (reaction == "Yes") {
            hideQuestionDialog();
            $.ajax({
                url : '/penjamin-asuransi/informasi-pasien-rajal-bpjs/get-random-string',
                success : function (data) {
                    var randString = data
                    if(randString !== null || randString != '') {
                        $('#modal_progress').modal('toggle');
                        updateProgressBarSinkron(randString)
                    }
                }
            });
        } else {
            hideQuestionDialog();
        }
    });
})

