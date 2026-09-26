/*
* @Author: Sigit
* @Date:   2018-12-26 14:18:02
*/

var table;

$(document).ready(function() {
    localStorage.clear();
    localStorage.setItem("ruangan", ruangan);
    localStorage.setItem("kamar", kamar);

    table = $("#tb-lap-pasien-malnutrisi").docoTabel({
        filter: true,
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        order: [[1, "desc"]],
        ajax: baseUrl + "gizi/laporan-pasien-malnutrisi/get-data-pasien-malnutrisi",
        columns: [
            {title: no, data: "no", searchable: false, orderable: false},
            {title: tgl_masuk, data: "tgl_pendaftaran"},
            {title: no_rm_pendaftaran, data: "no_rm_pendaftaran", searchable: false, orderable: false},
            {title: jenis_kelamin, data: "jenis_kelamin", searchable: false},
            {title: dokter_dpjp, data: "nama_pegawai", searchable: false},
            {title: kasus_penyakit, data: "jeniskasuspenyakit_nama", searchable: false},
            {title: ruangan_kamar, data: "ruangan_nama", searchable: false, orderable: false},
            {title: kategori, data: "kategori"},
            {title: no_rekam_medik, data: "no_rekam_medik", visible: false, orderable: false},
            {title: no_pendaftaran, data: "no_pendaftaran", visible: false, orderable: false},
            {title: ruangan_nama, data: "ruangan_id", visible: false, orderable: false},
            {title: kamarruangan_nokamar, data: "kamarruangan_id", visible: false, orderable: false},
            {title: dokter_dpjp, data: "pegawai_id", visible: false, orderable: false},
            {title: kasus_penyakit, data: "jeniskasuspenyakit_id", visible: false, orderable: false},
            {title: nama_pasien, data: "nama_pasien", visible: false, orderable: false},
        ],
        columnDefs: [{
            targets: 8,
            className: "text-right"
        }],
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
        fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            if (aData.kategori == "Rendah") {
                $('td', nRow).css('background-color', '#55efc4');
            } else if (aData.kategori == "Sedang") {
                $('td', nRow).css('background-color', '#ffeaa7');
            } else {
                $('td', nRow).css('background-color', '#fab1a0');
            }
        }
    });

    $(".dataTables_filter").hide();

    $(".filter-form").datatableBootstrapFilter(table , [
        [1, inputTanggal],
        [10, dropdownRuangan],
        [11, dropdownKamar],
        [7, dropdownKategori],
        [12, dropdownDokter],
        [13, dropdownJenisKasusPenyakit],
    ], {
        1: 0,
        8: 1,
        9: 2,
        14: 3,
        10: 4,
        11: 5,
        12: 6,
        7: 7,
        13: 8,
    });

    dateRangeHelper(".startDate", ".endDate", ".targetDate", true);
});