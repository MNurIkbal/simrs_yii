/*
* @Author: Sigit
* @Date:   2018-12-13 14:43:02
*/

var table;

$(document).ready(function () {
    localStorage.clear();
    localStorage.setItem("ruangan", ruangan);
    localStorage.setItem("kamar", kamar);

    table = $("#tb-inf-permintaan-makan").docoTabel({
        columnDefs: [{
            searchable: false,
            orderable: false,
            targets: 0,
            className: "select-checkbox",
        }],
        select: {
            style: 'multi',
        },
        filter: true,
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        order: [[2, "desc"]],
        ajax: baseUrl + "gizi/inf-permintaan-makan/get-data-permintaan-makan",
        columns: [
            { title: "", data: null, defaultContent: "", searchable: false, orderable: false },
            { title: no, data: "no", searchable: false, orderable: false },
            { title: tgl_permintaanmakan, data: "tgl_permintaanmakan" },
            { title: no_permintaanmakan, data: "no_permintaanmakan" },
            {
                title: pembayaran,
                data: "is_ditagihkan",
                render : function(data, type, row){
                    if(data != null && data != ''){
                        return 'Ditagihkan';
                    }
                    return 'Tidak Ditagihkan'
                }
            },
            { title: catatan_diet, data: "catatan_diet" },
            { title: ruangan_kamar, data: "ruangan_kamar", searchable: false, orderable: false },
            { title: no_pendaftaran, data: "no_pendaftaran" },
            { title: no_rekam_medik, data: "no_rekam_medik" },
            { title: nama_pasien, data: "nama_pasien" },
            { title: jenis_kelamin, data: "jenis_kelamin", searchable: false, },
            { title: tanggal_lahir, data: "tanggal_lahir", searchable: false, },
            { title: jenisdiet_nama, data: "jenisdiet_nama", searchable: false, },
            { title: diagnosa, data: "diagnosa", searchable: false, },
            { title: riwayat_alergi, data: "riwayat_alergi", searchable: false, },
            { title: penjamin_nama, data: "penjamin_nama", searchable: false, },
            {
                title: 'Cara Bayar',
                data: "carabayar_id",
                orderable: false,
                sortable: false,
                render: (value, type, data) => {
                    return data.carabayar_nama
                }
            },
            { title: status_permintaan, data: "status_permintaan" },
            { title: ruangan_nama, data: "ruangan_id", visible: false, orderable: false },
            { title: kamarruangan_nokamar, data: "kamarruangan_id", visible: false, orderable: false },
        ],
        scrollCollapse: true,
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
                sortDescending: sortDescending
            }
        },
        rowCallback: (rowElement, data) => {
            if (data.is_pulang) {
                $(rowElement).css('background-color', '#d64541')
                $(rowElement).css('color', 'white')
            } else if (data.is_stopakomodasi) {
                $(rowElement).css('background-color', '#7efff5')
            }
        }
    });

    $(".dataTables_filter").hide();

    $(".filter-form").datatableBootstrapFilter(table, [
        [2, inputTanggal],
        [18, dropdownRuangan],
        [19, dropdownKamar],
        [17, dropdownStatus],
        [16, dropdownCaraBayar],
        [4, dropdownPembayaran],
    ], {
        2: 0, //tgl permintaan makan
        7: 1, //no pendaftaran
        3: 2, //no permintaan makan
        8: 3, //no rekam medik
        5: 4, //catatan diet
        9: 5, //nama pasien
        18: 6, //ruangan nama
        19: 7, //kamar
        17: 8, //status permintaan
        16: 9, //cara bayar
        4: 10, // pembayaran
    });

    dateRangeHelper(".startDate", ".endDate", ".targetDate", true);
});

$(document).on("click", "#tb-inf-permintaan-makan tbody tr", function () {
        var selected = table.rows(".selected").data().length;

        if(typeof selected !== 'undefined' && selected > 0){
            if(selected == 1){
                var data = table.row(".selected").data();
                primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
                // console.log(data);
                $('#btn-label-makanan').attr('disabled', false);
                $('#btn-print-label-makanan').attr('disabled', false);
                $('#btn-edit-permintaan-makan').attr('disabled', false);
                $('#btn-detail-permintaan-makan').attr('disabled',false);

                if (data.status_permintaanmakan != 1) {
                    $('#btn-edit-permintaan-makan').attr('disabled', true);
                }
                if(data.is_pulang || data.is_stopakomodasi){
                    $('#btn-edit-permintaan-makan').attr('disabled', true);
                    $('#btn-label-makanan').attr('disabled', true);
                    $('#btn-print-label-makanan').attr('disabled', true);
                }
            }else{
                $('#btn-label-makanan').attr('disabled', false);
                $('#btn-print-label-makanan').attr('disabled', false);

                $('#btn-edit-permintaan-makan').attr('disabled', true);
                $('#btn-detail-permintaan-makan').attr('disabled',true);
            }
        }else{
            $('#btn-edit-permintaan-makan').attr('disabled', true);
            $('#btn-detail-permintaan-makan').attr('disabled',true);
            $('#btn-label-makanan').attr('disabled', true);
            $('#btn-print-label-makanan').attr('disabled', true);
            primaryKey = false;
        }
});
