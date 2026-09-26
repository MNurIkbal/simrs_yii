// Global Var
var table;

// Event click
$(document).on("click", ".data-reset", function() {
    // Reload table
    table.draw();

    // Disable edit and delete button
    $("#btn-approve").prop("disabled", true);
    $("#btn-batal").prop("disabled", true);
});

// Event click
$(document).on("click", "#tb-lap-pasien-rujukan-rad tbody tr", function() {
    // Try catch
    try {
        // Get primary
        primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
        statusPenunjang = table.row(".selected").data().status_penunjang ? table.row(".selected").data().status_penunjang : null;
        statusBayar = table.row(".selected").data().is_bayar ? table.row(".selected").data().is_bayar : null;
                        // docoNotification('error', errTitle, errMsg);
    } catch (e) {
        // Make it false
        primaryKey = false;
        statusPenunjang = false;
    }

    // Assign to ubah
    // $("#btn-edit").attr("action", updateUrl + primaryKey);
    $("#btn-approve").attr("data-target", approveUrl + primaryKey);
    $("#btn-batal").attr("data-target", batalUrl + primaryKey);

    // Check class selected
    if ($('#tb-inf-pasien-rujukan-rad tr.selected').length == 0) {
        // Disable edit button
        $("#btn-approve").prop("disabled", true);
        $("#btn-batal").prop("disabled", true);
    }
    else {
        // Disable edit button
        $("#btn-approve").prop("disabled", false);
        $("#btn-batal").prop("disabled", false);
        $("#btn-batal").attr("data-options", 'link');

        // pengecek bila status sudah di setujui maka tombol approve tidak akan aktif
        if (statusPenunjang == '471'){
            $("#btn-approve").prop("disabled", true);
        }
        // pengecek bila status sudah di setujui maka tombol approve tidak akan aktif
        // pengecek bila status sudah dibatalkan maka tombol approve dan batal tidak akan aktif
        if (statusPenunjang == '472') {
            $("#btn-approve").prop("disabled", true);
            $("#btn-batal").prop("disabled", true);
        }
        // pengecek bila status sudah dibatalkan maka tombol approve dan batal tidak akan aktif
        if (statusPenunjang == '474') {
            $("#btn-approve").prop("disabled", true);
            $("#btn-batal").attr("data-options", 'click');
            $("#btn-batal").attr("data-target", "#");
            $("#btn-batal").attr("data-pesan-error", "Tidak Bisa membatalkan karena sudah mengambil sampel");
        }
        if(statusBayar){
            $("#btn-approve").prop("disabled", true);
            $("#btn-batal").attr("data-options", 'click');
            $("#btn-batal").attr("data-target", "#");
            $("#btn-batal").attr("data-pesan-error", "Tidak Bisa membatalkan karena sudah melakukan Pembayaran");
        }
    }
});

$("#btn-batal").on('click',function(){
    if ($("#btn-batal").attr("data-options") == "click"){
        docoNotification('error', "Gagal Melakukan Batal", $("#btn-batal").attr("data-pesan-error"));
    }
});

// Event Ready
$(document).ready(function() {
    $("#btn-approve").prop("disabled", true);
    $("#btn-batal").prop("disabled", true);
    // Generate Table
    table = $("#tb-lap-pasien-rujukan-rad").docoTabel({
        select: {
            style: "os",
            selector: "tr"
        },
        filter: true,
        sorting: [[1, "asc"]],
        fnRowCallback: function (nRow, data, iDisplayIndex, iDisplayIndexFull) {
            // if (data['stat_penunjang'] == "SUDAH DISETUJUI") {
            //     $('td', nRow).css({
            //         'background-color': '#26A65B',
            //         'color': '#ffffff'
            //     });
            // } else if (data['stat_penunjang'] == "BATAL") {
            //     $('td', nRow).css({
            //         'background-color': '#D24D57',
            //         'color': '#ffffff'
            //     });
            // }
            // console.log(data['stat_penunjang']);
        },
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl + "radiologi/lap-pasien-rujukan-rad/get-data",
        columns: [
            { title: no, data: "rowNum", searchable: false, orderable: false},
            { title: tgl_rujukan, data: "tgl_rujukan", searchable: true },
            { title: tgl_persetujuan, data: "tgl_persetujuan", searchable: true},
            { title: no_pendaftaran, data: "no_pendaftaran", searchable: true },
            { title: no_rujukan, data: "no_rujukan", searchable: true },
            { title: 'No RM / Nama Pasien', data: "no_rekam_medik", visible:false, searchable : true },
            {
                title: pasien,
                data: "nama_pasien",
                searchable: false,
                orderable: false,
                render: (data, rowElement, rowData) => {
                   let _gender = (rowData.jeniskelamin == 16) ? 'Male' : 'Female';
                   return `
                       <p style="margin-bottom: 2px"> ${rowData.nama_pasien != null ? rowData.nama_pasien : ''} (${_gender != null ? _gender : '-'})</p>
                       <p style="margin-bottom: 2px"> ${rowData.no_rekam_medik != null ? rowData.no_rekam_medik : '-'}</p>
                       <p style="margin-bottom: 2px"> ${rowData.tanggal_lahir != null ? moment(rowData.tanggal_lahir).format("DD MMM YYYY") : '-'} </p>
                   `
                }
             },
            {
                title: pemeriksaan,
                data: "daftartindakan_nama",
                searchable: false
            },
            { title: jenis_rujukan, data: "jenis_rujukan", searchable: false},
            {
                title: "Asal Rujukan",
                data: "asalrujukan_nama",
                searchable: false,
                orderable: false,
                render: (data, rowElement, rowData) => {
                    let _asalRujukan = (rowData.asalrujukan_nama != null) ? rowData.asalrujukan_nama : '-';
                   return `
                       ${_asalRujukan}
                   `
                }
             },
             {
                title: "Nama RS",
                data: "rujukandari_nama",
                searchable: false,
                orderable: false,
                render: (data, rowElement, rowData) => {
                    let dataRs = (rowData.rujukandari_nama != null) ?  rowData.rujukandari_nama : '-';
                   return `
                       ${dataRs}
                   `
                }
             },
            { title: asal_rujukan,
                data: "asalrujukan_nama",
                render: () => "",
                visible: false
            },
            { title: dokter_perujuk, data: "dokter_perujuk", searchable: false },
            {
                title: "Cara Bayar / Penjamin",
                searchable: false,
                orderable: false,
                render: (data, rowElement, rowData) => {
                   return `
                       <p style="margin-bottom: 2px"> ${rowData.carabayar_nama != null ? rowData.carabayar_nama : ''} </p>
                       <p style="margin-bottom: 2px"> ${rowData.penjamin_nama != null ? rowData.penjamin_nama : '-'}</p>
                   `
                }
            },
            { title: 'Status', data: "status_periksa_nama", searchable: false },
            { title: 'Nama RS Rujukan', data: "rujukandari_nama",
                render: () => "",
                visible:false,
                searchable: true
            },
            { title: 'Nama Pemeriksaan', data: "daftartindakan_nama", visible:false, searchable: true },
            { title: 'Status', data: "status_periksa_nama", visible:false, searchable: true },
            { title: 'Dokter Perujuk', data: "dokter_perujuk", visible:false, searchable: true },
            { title: 'Jenis Rujukan', data: "jenis_rujukan_id", visible:false, searchable: true },
        ],
        // formFilters: [
        //     {
        //         fieldName: 'instalasi_id',
        //         label: 'Instalasi Akhir',
        //         type: {
        //            name: 'dropdownScroll',
        //            url: "/radiologi/lap-pasien-rujukan-rad/filters",
        //            additionalPayload: {
        //               type: 'pemeriksaan',
        //            }
        //         }
        //      },
        // ],
        // scrollCollapse: true,
        // language: {
        //     emptyTable: emptyTable,
        //     info: info,
        //     infoEmpty: infoEmpty,
        //     infoFiltered: infoFiltered,
        //     lengthMenu: lengthMenu,
        //     loadingRecords: loadingRecords,
        //     processing: processing,
        //     search: search,
        //     zeroRecords: zeroRecords,
        //     paginate: {
        //         first: first,
        //         last: last,
        //         next: next,
        //         previous: previous
        //     },
        //     aria: {
        //         sortAscending: sortAscending,
        //         sortDescending: sortDescending
        //     }
        // }
    });

    // Hide datatables filter form
    $(".dataTables_filter").hide();

    // Custom filter
    $(".filter-form").datatableBootstrapFilter(table , [
        [1, filterTanggalRujukan],
        [2, filterTanggalPersetujuan],
        [19, dropdownJenisRujukan],
        [11, dropdownAsalRujukan],
        [15, dropdownNamaRsRujukan],
        [16, dropdownJenisPemeriksaan],
        [17, dropdownStatusPeriksa],
        [18, dropdownDokterPerujuk],
    ],{
        1:0,
        3:1,
        5:3,
        2:4,
        19:5,
        11:6,
        15:7,
        16:8,
        17:9,
        18:10,
    },true);

    dateRangeHelper(".rangePersetujuanStart", ".rangePersetujuanFinish", ".targetDatePersetujuan");
    dateRangeHelper(".startDate", ".endDate", ".targetDate");

    $(".daterange-basic").daterangepicker({
        startDate: '<?=(date("01-M-Y"))?>', autoUpdateInput: true,
        endDate: '<?=(date("d-M-Y"))?>',
        applyClass: "bg-slate-600",
        cancelClass: "btn-default",
        locale: {
            format: "DD-MMMM-YYYY"
        }
    });

    $("#excel-bgprocess").unbind("click");
    $("#excel-bgprocess").on("click", function (event) {
        var _jenis_rujukan_nama = $("#filter_jenis_rujukan option:selected").text();
        var _jenis_rujukan_id = $("#filter_jenis_rujukan option:selected").val();

        _url = encodeURI(baseUrl+"radiologi/lap-pasien-rujukan-rad/show-popup-excel?jenis_rujukan_nama="+ _jenis_rujukan_nama
        +"&jenis_rujukan_id="+ _jenis_rujukan_id+"&")
        $(this).attr("data-url",_url);
    })

    $(".selectAsalRujukan").select2({
        language: { errorLoading:function() { return "Searching..." } },
        placeholder: "-Pilih-",
        minimumInputLength: 2,
        ajax: {
            url: urlAsalRujukan,
            dataType: "json",
            processResults: function (data) {
                return {
                    results: data.result.sort((a, b) => a.text.toLowerCase().localeCompare(b.text.toLowerCase()))
                };
            }
        },
        dropdownCssClass: "bigdrop",
        escapeMarkup: function (m) { return m; },
    });

    // Penyesuaian Filter
    $(".selectNamaRsRujukan").select2({
        language: { errorLoading:function() { return "Searching..." } },
        placeholder: "-Pilih",
        minimumInputLength: 2,
        ajax: {
            url:urlGetRsRujukan,
            dataType: "json",
            processResults: function (data) {
                return {
                    results: data.result.sort((a, b) => a.text.toLowerCase().localeCompare(b.text.toLowerCase()))
                };
            }
        },
        dropdownCssClass: "bigdrop",
        escapeMarkup: function (m) { return m; },
    });


    $("#pdf-bgprocess").unbind("click");
    $("#pdf-bgprocess").on("click", function (event) {
        var _jenis_rujukan_nama = $("#filter_jenis_rujukan option:selected").text();
        var _jenis_rujukan_id = $("#filter_jenis_rujukan option:selected").val();

        _url = encodeURI(baseUrl+"radiologi/lap-pasien-rujukan-rad/show-popup-pdf?jenis_rujukan_nama="+ _jenis_rujukan_nama
        +"&jenis_rujukan_id="+ _jenis_rujukan_id+"&")
        $(this).attr("data-url",_url);
    })

});