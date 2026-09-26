/*
* @Author: Rizqi Fitrianto
* @Date:   2019-01-18 14:49:32
* @Last Modified by:   Rizqi Fitrianto
* @Last Modified time: 2019-01-22 12:01:23
*/

$(document).ready(function(){
    $('#btn-cetak-resume').on('click', function(){
        let url = $(this).attr('data-target');
        window.open(url, '_blank');
    })
    var tabel_obat = $("#tabel-obat-resume").docoTabel({
        scrollX: true,
        filter: false,
        sorting: [[1, "asc"]], 
        processing: true,
        serverSide: true,
        paging: true,
        ajax: "/rajal/pemeriksaan/get-data-resume-obat?pendaftaran_id="+pendaftaran_id+"&pasien_id="+pasien_id,
        columns: [
            {title: "No", data: "rowNum"},
            {title: "Racikan / Non Racikan", data: "racikan_nama"},
            {title: "R Ke-", data: "rke"},
            {title: "Nama Obat", data: "obatalkes_nama"},
            {title: "Satuan Kecil", data: "satuan_kecil"},
            {title: "Signa", data: "signa_nama"},
            {title: "Qty", data: "qty_reseptur"},
        ],
    });
    var tabel_diagnosa = $("#tabel-diagnosa-resume").docoTabel({
        scrollX: true,
        filter: false,
        sorting: [[1, "asc"]], 
        processing: true,
        serverSide: true,
        paging: true,
        ajax: "/rajal/pemeriksaan/get-data-resume-diagnosa?pendaftaran_id="+pendaftaran_id+"&pasien_id="+pasien_id,
        columns: [
            {title: "No", data: "rowNum"},
            {title: "Kelompok Diagnosa", data: "kelompokdiagnosa_nama"},
            {title: "Kode Diagnosa", data: "diagnosa_kode"},
            {title: "Diagnosa Nama", data: "diagnosa_nama"},
        ],
    });
    var tabel_tindakan_terapi = $("#tabel-tindakan-terapi-resume").docoTabel({
        scrollX: true,
        filter: false,
        sorting: [[1, "asc"]], 
        processing: true,
        serverSide: true,
        paging: true,
        ajax: "/rajal/pemeriksaan/get-data-resume-terapi?pendaftaran_id="+pendaftaran_id+"&pasien_id="+pasien_id+"&type=tindakan",
        columns: [
            {title: "No", data: "rowNum"},
            {title: "Nama Tindakan / Paket", data: "tindakan_obat"},
            {title: "Jumlah", data: "qty"},
        ],
    });
    var tabel_tindakan_obat = $("#tabel-obat-terapi-resume").docoTabel({
        scrollX: true,
        filter: false,
        sorting: [[2, "asc"]], 
        processing: true,
        serverSide: true,
        paging: true,
        ajax: "/rajal/pemeriksaan/get-data-resume-terapi?pendaftaran_id="+pendaftaran_id+"&pasien_id="+pasien_id,
        columns: [
            {title: "No", data: "rowNum"},
            {title: "Nama Tindakan", data: "tindakan"},
            {title: "Obat / Alkes", data: "tindakan_obat"},
            {title: "Jumlah", data: "qty"},
        ],
    });
    var tabel_resume_lab = $("#tabel-lab-resume").docoTabel({
        scrollX: true,
        filter: false,
        sorting: [[1, "asc"]], 
        processing: true,
        serverSide: true,
        paging: true,
        ajax: "/rajal/pemeriksaan/get-data-resume-lab?pendaftaran_id="+pendaftaran_id+"&pasien_id="+pasien_id,
        columns: [
            {title: "No", data: "rowNum"},
            {title: "Tanggal Pemeriksaan", data: "tgl_tindakan"},
            {title: "Jenis Pemeriksaan", data: "jenis"},
            {title: "Nama Pemeriksaan", data: "daftartindakan_nama"},
        ],
    });
    var tabel_resume_rad = $("#tabel-rad-resume").docoTabel({
        scrollX: true,
        filter: false,
        sorting: [[1, "asc"]], 
        processing: true,
        serverSide: true,
        paging: true,
        ajax: "/rajal/pemeriksaan/get-data-resume-rad?pendaftaran_id="+pendaftaran_id+"&pasien_id="+pasien_id,
        columns: [
            {title: "No", data: "rowNum"},
            {title: "Tanggal Pemeriksaan", data: "tgl_tindakan"},
            {title: "Jenis Pemeriksaan", data: "jenis"},
            {title: "Nama Pemeriksaan", data: "daftartindakan_nama"},
        ],
    });
    var tabel_resume_bedah = $("#tabel-bedah-resume").docoTabel({
        scrollX: true,
        filter: false,
        sorting: [[1, "asc"]], 
        processing: true,
        serverSide: true,
        paging: true,
        ajax: "/rajal/pemeriksaan/get-data-resume-bedah?pendaftaran_id="+pendaftaran_id+"&pasien_id="+pasien_id,
        columns: [
            {title: "No", data: "rowNum"},
            {title: "Tanggal Pemeriksaan", data: "tgl_tindakan"},
            {title: "Jenis Pemeriksaan", data: "jenis"},
            {title: "Nama Pemeriksaan", data: "daftartindakan_nama"},
        ],
    });
    var tabel_resume_rehab = $("#tabel-rehab-resume").docoTabel({
        scrollX: true,
        filter: false,
        sorting: [[1, "asc"]], 
        processing: true,
        serverSide: true,
        paging: true,
        ajax: "/rajal/pemeriksaan/get-data-resume-rehab?pendaftaran_id="+pendaftaran_id+"&pasien_id="+pasien_id,
        columns: [
            {title: "No", data: "rowNum"},
            {title: "Tanggal Pemeriksaan", data: "tgl_tindakan"},
            {title: "Jenis Pemeriksaan", data: "jenis"},
            {title: "Nama Pemeriksaan", data: "daftartindakan_nama"},
        ],
    });
})