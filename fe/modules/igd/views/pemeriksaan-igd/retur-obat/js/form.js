/*
* @Author: Sigit
* @Date:   2018-08-21 10:34:23
*/

var tabelRetur;
var tabelRiwayatRetur;
var tabelReturDetail;

$(document).ready(function() {
    tabelRiwayatRetur = $("#tb-riwayat-retur-obat").docoTabel({
        sorting: false,
        filter: false,
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl+"igd/pemeriksaan-igd/get-data-riwayat-permintaan-retur?id="+pendaftaran_id+"",
        columnDefs: [ {
            orderable: false,
            className: 'select-checkbox',
            targets: 0
        }],
        select: {
            style: 'os',
            selector: 'td:first-child'
        },
        columns: [
            {title: "", data: null, defaultContent: "", searchable: false, orderable: false},
            {title: no, data: "no", searchable: false, orderable: false},
            {title: noRetur, data: "no_permintaanretur", searchable: false, orderable: false},
            {title: tanggalRetur, data: "tgl_permintaanretur", searchable: false, orderable: false},
            {title: ruang, data: "ruangan_nama", searchable: false, orderable: false},
            {title: status, data: "status", searchable: false, orderable: false},
            {title: userRetur, data: "nama_pegawai", searchable: false, orderable: false},
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
                sortDescending: sortDescending
            }
        },
    });

    tabelRetur = $("#tb-form-retur-obat").docoTabel({
        sorting: false,
        filter: false,
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl+"igd/pemeriksaan-igd/get-data-retur-obat?id="+pendaftaran_id+"",
        columns: [
            {title: noForm, data: "no", searchable: false, orderable: false},
            {title: noResep, data: "noresep", searchable: false, orderable: false},
            {title: namaObat, data: "nama_obat", searchable: false, orderable: false},
            {title: signa, data: "signa", searchable: false, orderable: false},
            {title: sisaObat, data: "stok_retur", searchable: false, orderable: false},
            {title: jumlahRetur, data: "jumlah_retur", searchable: false, orderable: false},
            {title: hargaSatuan, data: "harga_satuan", searchable: false, orderable: false},
            {title: total, data: "harga_jumlah", searchable: false, orderable: false},
            {title: alasan, data: "alasan", searchable: false, orderable: false},
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
                sortDescending: sortDescending
            }
        },
    });

    tabelReturDetail = $("#tb-detail-retur-obat").docoTabel({
        sorting: false,
        filter: false,
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl+"igd/pemeriksaan-igd/get-data-detail-permintaan-retur?id="+pendaftaran_id+"&permintaanretur_id=",
        columns: [
            {title: no, data: "no", searchable: false, orderable: false},
            {title: namaObatAlkes, data: "obatalkes_nama", searchable: false, orderable: false},
            {title: satuan, data: "signa_nama", searchable: false, orderable: false},
            {title: jumlahRetur, data: "qty_retur", searchable: false, orderable: false},
            {title: alasan, data: "alasan", searchable: false, orderable: false},
            {title: qtyApprove, data: "qty_approve", searchable: false, orderable: false},
            {title: alasanUF, data: "alasan_retur", searchable: false, orderable: false},
            {title: hargaSatuan, data: "harga_satuan", searchable: false, orderable: false},
            {title: total, data: "total", searchable: false, orderable: false},
            {title: tglApprove, data: "tgl_approve", searchable: false, orderable: false},
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
                sortDescending: sortDescending
            }
        },
    });
});

$(document).on('click', '#tb-riwayat-retur-obat tbody tr', function () {
    try {
        $("#btn-ubah-retur").prop("disabled", true);
        $("#btn-hapus-retur").prop("disabled", true);

        var status = tabelRiwayatRetur.row('.selected').data().status ? tabelRiwayatRetur.row('.selected').data().status : null;
    } catch (e) {
        var status = "";
    }

    if (status != "") {
        if (status == "Sudah di Proses Farmasi") {
            $("#btn-detail-retur").prop("disabled", false);
            $("#btn-ubah-retur").prop("disabled", true);
            $("#btn-hapus-retur").prop("disabled", true);
        } else if(status == "Belum di Proses Farmasi") {
            $("#btn-detail-retur").prop("disabled", false);
            $("#btn-ubah-retur").prop("disabled", false);
            $("#btn-hapus-retur").prop("disabled", false);
        }
    } else {
        $("#btn-detail-retur").prop("disabled", true);
        $("#btn-ubah-retur").prop("disabled", true);
        $("#btn-hapus-retur").prop("disabled", true);
    }
});

function showFormPermintaanRetur() {
    $("#div-form-retur-obat").prop("hidden", false);
    $("#div-riwayat-retur-obat").prop("hidden", true);
    $("#div-detail-retur-obat").prop("hidden", true);

    tabelRetur.clear();
    tabelRetur.ajax.url(baseUrl+"igd/pemeriksaan-igd/get-data-retur-obat?id="+pendaftaran_id+"").draw();
}

function hideFormPermintaanRetur() {
    $("#div-form-retur-obat").prop("hidden", true);
    $("#div-riwayat-retur-obat").prop("hidden", false);
    $("#div-detail-retur-obat").prop("hidden", true);
    $("#btn-detail-retur").prop("disabled", true);
    $("#btn-ubah-retur").prop("disabled", true);
    $("#btn-hapus-retur").prop("disabled", true);

    tabelRiwayatRetur.clear();
    tabelRiwayatRetur.draw();
    tabelRetur.clear();
    tabelRetur.ajax.url(baseUrl+"igd/pemeriksaan-igd/get-data-retur-obat?id="+pendaftaran_id+"").draw();
}

function showDetailPermintaanRetur() {
    $("#div-form-retur-obat").prop("hidden", true);
    $("#div-riwayat-retur-obat").prop("hidden", true);
    $("#div-detail-retur-obat").prop("hidden", false);
    $("#btn-ubah-retur").prop("disabled", true);
    $("#btn-detail-retur").prop("disabled", true);
    $("#btn-hapus-retur").prop("disabled", true);
}

function hideDetailPermintaanRetur() {
    $("#div-form-retur-obat").prop("hidden", true);
    $("#div-riwayat-retur-obat").prop("hidden", false);
    $("#div-detail-retur-obat").prop("hidden", true);
    $("#btn-ubah-retur").prop("disabled", true);
    $("#btn-detail-retur").prop("disabled", true);
    $("#btn-hapus-retur").prop("disabled", true);

    tabelRiwayatRetur.clear();
    tabelRiwayatRetur.draw();
}

function simpanPermintaanRetur() {
    var data = $("#form-permintaan-retur").serializeArray();
    var dataDetail = tabelRetur.$("input").serializeArray();

    $("#form-permintaan-retur-detail").docoForm("click", {
        data: {"PermintaanReturForm": data, "PermintaanReturDetailForm": dataDetail},
        success : function(response) {
            $('.tabbable ul li a[href="#view-retur"]').click();
        },
        error : function(response) {
            $('.tabbable ul li a[href="#view-retur"]').click();
        }
    });
}

function ubahPermintaanRetur() {
    var data = tabelRiwayatRetur.rows('.selected').data().toArray();

    if (data.length > 0) {
        var id = data[0].primary;

        tabelRetur.clear();
        tabelRetur.ajax.url(baseUrl+"igd/pemeriksaan-igd/get-data-retur-obat?id="+pendaftaran_id+"&permintaanretur_id="+id).draw();
        $(".permintaanretur_id").val(id);
        $("#div-form-retur-obat").prop("hidden", false);
        $("#div-riwayat-retur-obat").prop("hidden", true);
        $("#div-detail-retur-obat").prop("hidden", true);
    }
}

function hapusPermintaanRetur() {
    var data = tabelRiwayatRetur.rows('.selected').data().toArray();

    if (data.length > 0) {
        var id = data[0].primary;

        $("#form-permintaan-retur").docoForm("delete", {
            additional: true,
            url: baseUrl+"igd/pemeriksaan-igd/hapus-permintaan-retur?id="+pendaftaran_id+"&permintaanretur_id="+id,
            success : function(response) {
                $('.tabbable ul li a[href="#view-retur"]').click();
            }
        });
    }
}

function cetakRiwayatRetur() {
    window.open("/igd/pemeriksaan-igd/cetak-riwayat-retur?id="+pendaftaran_id);
}

function cetakDetailRetur() {
    var id = $(".permintaanretur_id").val();

    window.open("/igd/pemeriksaan-igd/cetak-detail-retur?id="+pendaftaran_id+"&permintaanretur_id="+id);
}

function detailPermintaanRetur() {
    var data = tabelRiwayatRetur.rows('.selected').data().toArray();

    if (data.length > 0) {
        var id = data[0].primary;

        tabelReturDetail.clear();
        tabelReturDetail.ajax.url(baseUrl+"igd/pemeriksaan-igd/get-data-detail-permintaan-retur?id="+pendaftaran_id+"&permintaanretur_id="+id).draw();
        $(".permintaanretur_id").val(id);
        showDetailPermintaanRetur();
    }
}

function onQtyResepturChange(index) {
    var tr = $(index).closest("tr");
    var jumlahRetur = parseInt($(index).val());
    var hargaSatuan = parseInt(tr.find(".harga_satuan").val());
    var stokRetur = parseInt(tr.find(".stok_retur").text());
    var totalHarga = jumlahRetur * hargaSatuan;

    if (stokRetur >= jumlahRetur) {
        tr.find(".harga_jumlah").val(totalHarga);
        tr.find(".harga_jual").text(totalHarga);
    } else {
        $(index).val(0).trigger("change");
    }
}