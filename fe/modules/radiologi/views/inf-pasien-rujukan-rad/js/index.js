// Global Var
var table;

// Event click
$(document).on("click", ".data-reset", function() {
    // Reload table
    table.draw();

    // Disable edit and delete button
    $("#btn-approve").prop("disabled", true);
    $("#btn-batal").prop("disabled", true);
    $("#btn-edit-tanggal").prop("disabled", true);
});

// Event click
$(document).on("click", "#tb-inf-pasien-rujukan-rad tbody tr", function() {
    // Try catch
    
    try {
        // Get primary
        primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
        statusPenunjang = table.row(".selected").data().status_penunjang ? table.row(".selected").data().status_penunjang : null;
        statusBayar = table.row(".selected").data().is_bayar ? table.row(".selected").data().is_bayar : null;
        statusPeriksa = table.row(".selected").data().status_periksa ? table.row(".selected").data().status_periksa : null;

        instalasi_id = table.row(".selected").data().instalasi_id ? table.row(".selected").data().instalasi_id : null;
        carabayar_id = table.row(".selected").data().carabayar_id ? table.row(".selected").data().carabayar_id : null;
        penjamin_id  = table.row(".selected").data().penjamin_id ? table.row(".selected").data().penjamin_id : null;
        pendaftaran_id_encrypt  = table.row(".selected").data().pendaftaran_id_encrypt ? table.row(".selected").data().pendaftaran_id_encrypt : null;
        is_referred = table.row(".selected").data().is_referred ? table.row(".selected").data().is_referred : false;
        is_aps = table.row(".selected").data().is_aps ? table.row(".selected").data().is_aps : false;
                        
    } catch (e) {
        // Make it false
        primaryKey = false;
        statusPenunjang = false;
        statusPeriksa = false;
        pendaftaran_id_encrypt = null;
        is_referred = false;
        is_aps = false;
    }
    // console.log(statusPeriksa);
    if (statusPeriksa != STATUS_BELUM_PERIKSA && statusPeriksa) {
        statusPeriksa = true;
    } else {
        statusPeriksa = false;
    }

    // Assign to ubah
    // $("#btn-edit").attr("action", updateUrl + primaryKey);
    $("#btn-approve").attr("data-target", approveUrl + primaryKey);
    $("#btn-batal").attr("data-target", batalUrl + primaryKey);
    $("#btn-rujuk").attr("data-target", rujukUrl + primaryKey);

    // Check class selected
    if ($('#tb-inf-pasien-rujukan-rad tr.selected').length == 0) {
        // Disable edit button
        $("#btn-approve").prop("disabled", true);
        $("#btn-batal").prop("disabled", true);
        $("#btn-edit-tanggal").prop("disabled", true);
    }
    else {
        // Disable edit button
        $("#btn-approve").prop("disabled", false);
        $("#btn-batal").prop("disabled", false);
        $("#btn-batal").attr("data-options", 'link');
        $("#btn-edit-tanggal").prop("disabled", true);

        if(is_aps) {
            $("#btn-rujuk").prop("disabled", true);
            $("#cetak-rujukan").prop("disabled", true);
        }
        else {
            $("#btn-rujuk").prop("disabled", false);
            $("#cetak-rujukan").prop("disabled", false);

            if(is_referred) {
                $("#cetak-rujukan").attr("data-target", cetakRujukUrl + primaryKey);
                $("#cetak-rujukan").prop("disabled", false);
            }
            else {
                $("#cetak-rujukan").prop("disabled", true);
            }

            if (statusPenunjang == STATUS_BELUM_DISETUJUI) {
                $("#btn-rujuk").prop("disabled", false);
            }

            if (statusPenunjang == STATUS_DISETUJUI){
                $("#btn-rujuk").prop("disabled", true);
            }
        }

        if (statusPenunjang == STATUS_BELUM_DISETUJUI) {
            $("#btn-edit-tanggal").prop("disabled", false);
        }

        if (statusPenunjang == STATUS_DISETUJUI){
            $("#btn-approve").prop("disabled", true);
        }

        if (statusPenunjang == STATUS_BATAL_APPROVE) {
            $("#btn-approve").prop("disabled", true);
            $("#btn-batal").prop("disabled", true);
        }
        if(statusBayar && instalasi_id == INSTALASI_RJ && carabayar_id == CARA_BAYAR_UMUM && penjamin_id == PENJAMIN_UMUM){
            $("#btn-approve").prop("disabled", true);
            $("#btn-batal").attr("data-options", 'click');
            $("#btn-batal").attr("data-target", "#");
            $("#btn-batal").attr("data-pesan-error", "Pasien dari instalasi Rawat Jalan dan Penjamin Perseorangan tidak bisa dibatalkan jika sudah melakukan pembayaran, harap melakukan pembatalan pembayaran.");
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

    $('.pickadate').pickadate({
        formatSubmit: 'yyyy-mm-dd',
    });

    $('.pickadate-today').pickadate({
        formatSubmit: 'yyyy-mm-dd',
        clear:'',
        onStart: function ()
        {
            var date = new Date();
            this.set('select', [date.getFullYear(), date.getMonth() + 1, date.getDate()]);
            this.set('disable',true);
        },
    });

    $('.pickadate-w-month').pickadate({
        format: 'dd mmm, yyyy',
        selectMonths: true,
          selectYears: 99,
        max: true,
        formatSubmit: 'yyyy-mm-dd',
        clear:''
    });

    $(".datetime").AnyTime_picker({
        format: "%d-%m-%Y %H:%i",
        earliest: new Date(),
        latest: new Date(new Date().getTime() + 24 * 60 * 60 * 1000)
    });

    $("#btn-approve").prop("disabled", true);
    $("#btn-batal").prop("disabled", true);
    $("#btn-edit-tanggal").prop("disabled", true);
    // Generate Table
    table = $("#tb-inf-pasien-rujukan-rad").docoTabel({
        info: false,
        columnDefs: [ {
            // searchable: false,
            orderable: false,
            className: "select-checkbox",
            targets: 0
        }],
        select: {
            style: "os",
            selector: "tr"
        },
        filter: true,
        sorting: [[4, "desc"]],
        displayLength: 50,
        processing: true,
        serverSide: true,
        // scrollX: true,
        ajax: baseUrl + "radiologi/inf-pasien-rujukan-rad/get-data",
        columns: [
            {
                title: "", 
                data: null, defaultContent: "", 
                searchable: false, 
                orderable: false},
            {
                title: no, data: "rowNum", 
                searchable: false, 
                orderable: false
            },
            {
                title: no_rekam_medik, 
                data: "no_rekam_medik", 
                searchable: true,
                visible: false
            },
            {
                title: status_penunjang,
                data: "stat_penunjang",
                name : "stat_penunjang",
                searchable: true
            },
            { 
                title: tgl_rujukan, 
                data: "tgl_rujukan", 
                searchable: true 
            },
            {
                title: "Detail Diagnosa",
                data: "detail_diagnosa",
                searchable: false,
                orderable: false
            }, 
            {
                title: nama_pasien,
                data: "nama_pasien",
                render: (data, rowElement, rowData) => {
                    return `<p style="margin-bottom: 2px"> 
                    <b>${rowData.nama_pasien} (${rowData.jk})</b>
                        ${rowData.tanggal_lahir != null ? ` <p style="margin-bottom: 2px"> ${rowData.tanggal_lahir}</p>` : ' - '}
                    </p>
                   
                    <p> ${rowData.no_pendaftaran} / ${rowData.no_rekam_medik}</p>

                    <p style="margin-bottom: 2px;"> ${rowData.no_sep != null ? 'No. SEP : ' + rowData.no_sep : '-'} </p>
                    `
                }
            },
            {
                title: pemeriksaan,
                data: "pemeriksaan",
                searchable: false
            },
            { 
                title: dokter_perujuk, 
                data: "dokter_perujuk", 
                searchable: true 
            },
            { 
                title: no_rujukan, 
                data: "no_rujukan", 
                searchable: true 
            },
            {
                title: carabayar_nama, 
                data: "carabayar_nama",  
                render: (data, rowElement, rowData) => {
                    return `
                    <p> ${rowData.carabayar_nama} / ${rowData.penjamin_nama}</p>
                    `
                }
            },
            {
                title: 'Asal Rujukan',
                name : "asalrujukan_nama",
                data: "asalrujukan_nama",
            },
            {
                title: 'Status Pembayaran',
                data: null,
                name : "status_bayar",
                data: "status_bayar"
            },
            { 
                title: tanggal_lahir, 
                data: "tanggal_lahir", 
                searchable: true,
                visible: false
            },
            {
                title: penjamin_nama,
                data: "penjamin_nama",
                searchable: true,
                visible: false
            },
        ],
        rowCallback: (row, data) => {
            let backgroundColor = '#fff'
            let textColor = '#000'
            if(typeof data.status_penunjang !== null) {
                if (data.status_penunjang == STATUS_BATAL_APPROVE) {
                    backgroundColor = '#d64541';
                    textColor = 'white';
                } else if (data.status_penunjang == STATUS_BELUM_DISETUJUI) {
                    backgroundColor = '#FFFfff';
                    textColor = 'black';
                } else if(data.status_penunjang == STATUS_DISETUJUI) {
                    backgroundColor = '#2bcc6e';
                    textColor = 'white';
                }
             }
            $(row).css('background-color', backgroundColor)
            $(row).css('color', textColor)
            if ( data.cyto_tindakan == true) {
                $('td:eq(4)', row).css({"background-color":"#ff8900","color":"#ffffff"});
              }
        }
    });

    // Hide datatables filter form
    $(".dataTables_filter").hide();

    // Custom filter
    $(".filter-form").datatableBootstrapFilter(table , [
        [4, filterTanggalRujukan],
        [13, filterTanggalLahir],
        [11, dropdownRujukan],
        [8, autocompleteDokter],
        [10, dropdownCaraBayar],
        [14, dropdownPenjamin],
        [3, dropdownStatus],
        [12, dropdownPembayaran],
    ],{
        4:0,
        6:1,
        2:2,
        3:3,
        13:4,
        11:5,
        8:6,
        9:7,
        10:8,
        14:9,
        12:10,
    });

    dateRangeHelper(".rangeLahirStart", ".rangeLahirFinish", ".targetDateLahir");
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
    $('.legend-information').css('cursor', 'pointer');
    $('.legend-information').each(function (params) {
        var _id = $(this).attr("id");
        $(document).on('click', "#" + _id, function () {
            var _type = $(this).attr("data-type");
            const tableElement = $(`#tb-inf-pasien-rujukan-rad`).DataTable()
            showLoader();
            $('.legend-information').css('border', '1px solid #dddddd');
            if($(this).attr("selected-filter") == "true"){
                $('#filter_kelompok_2').prop('disabled', false);
                _type = 0;
                $(this).attr("selected-filter", false);
            }else{
                $("#" + _id).css('border', '2px solid #2ca38b');
                $('.legend-information').attr("selected-filter", false);
                $(this).attr("selected-filter", true);
                $('#filter_kelompok_2').prop('disabled', true);
            }
            tableElement.ajax.url("/radiologi/inf-pasien-rujukan-rad/get-data?type=" + _type).load()
        })
    })

    $(document).on('click', ".data-filter", function () {
        const tableElement = $(`#tb-inf-pasien-rujukan-rad`).DataTable()
        showLoader();
        tableElement.ajax.url("/radiologi/inf-pasien-rujukan-rad/get-data").load()
        $('.legend-information').css('border', '1px solid #dddddd');
    })

    $(document).on('click', ".data-reset", function () {
        const tableElement = $(`#tb-inf-pasien-rujukan-rad`).DataTable()
        showLoader();
        // tableElement.ajax.url("/radiologi/inf-pasien-rujukan-rad/get-data").load()
        $('.legend-information').css('border', '1px solid #dddddd');
        $('#filter_kelompok_2').prop('disabled', false);
    })

});