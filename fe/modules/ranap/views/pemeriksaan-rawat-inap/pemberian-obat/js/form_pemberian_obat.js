/*
* @Author: Sigit
* @Date:   2018-07-27 13:40:47
*/
var tablePO = [];
var tableRiwayatPO = [];

$(document).ready(function() {
    localStorage.removeItem('stored-obat');

    $.each(listJenisObatRiwayat, function(key, value) {
        var tableRiwayatId = "#tb-riwayat-pemberian-"+value.toLowerCase().replace(/\s/g, '-');

        tableRiwayatPO.push($(tableRiwayatId).docoTabel({
            filter: false,
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"ranap/pemeriksaan-rawat-inap/get-data-riwayat-pemberian-obat?id="+pendaftaran_id+"&jenisobat_id="+key,
            columns: [
                {title: noResep, data: "no_res_rekon", searchable: false, orderable: false},
                {title: namaObat, data: "nama_obat", searchable: false, orderable: false},
                {title: signa, data: "signa_obat", searchable: false, orderable: false},
                {title: jumlah, data: "jumlah", searchable: false, orderable: false},
                {title: dokter, data: "dokter", searchable: false, orderable: false},
                {title: waktuPemberianObat, data: "wkt_pemberian", searchable: false, orderable: false},
                {title: pemberiObat1, data: "pemberi1", searchable: false, orderable: false},
                {title: pemberiObat2, data: "pemberi2", searchable: false, orderable: false},
                {title: efek, data: "efek", searchable: false, orderable: false},
                {title: keterangan, data: "keterangan", searchable: false, orderable: false},
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
        }));
    });

    $.each(jenisObat, function(key, value) {
        var tableId = "#tb-pemberian-"+value.toLowerCase().replace(/\s/g, '-');

        tablePO.push($(tableId).DataTable({
            filter: false,
            displayLength: 10,
            scrollX: true,
            scrollY: "300px",
            scrollCollapse: true,
            paging: false,
            info: false,
            ordering: false,
        }));
    });
});

$(document).on('change', '.bb_tb', function() {
    var bb = $("#berat_badan").val();
    var tb = $("#tinggi_badan").val();

    if (bb != '' && tb != '') {
        var mosteller = Math.sqrt(Number(bb) * Number(tb) / 3600);

        $("#luas_tubuh").val(mosteller.toFixed(2));
    }
});

$(document).on("change", ".no_res_rekon", function(event) {
    event.preventDefault();

    var tr = $(this).closest("tr");
    var stokobatpasien_id = tr.find(".stokobatpasien_id").val();
    var obatalkes_id = tr.find(".obatalkes_id").val();
    var no_res_rekon = $(this).val();
    var jenisobat_id = tr.find(".jenis_obat").val();

    if (stokobatpasien_id != '' && obatalkes_id != '') {
        var storedObat = JSON.parse(localStorage.getItem("stored-obat"));
        var cek = stokobatpasien_id+"&"+obatalkes_id;

        if (storedObat.length > 0) {
            storedObat = jQuery.grep(storedObat, function(value) {
                return value != cek;
            });
        }

        localStorage.setItem("stored-obat", JSON.stringify(storedObat));
    }

    $.ajax({
        url:"/ranap/pemeriksaan-rawat-inap/get-list-obat?id="+pendaftaran_id+"&no_res_rekon="+no_res_rekon+"&jenisobat_id="+jenisobat_id,
        type: "GET",
        dataType: "json",
        success: function(response) {
            if (response) {
                var obatalkes_id = tr.find("select.obatalkes_id").prop("disabled", false);
                var storedObat = JSON.parse(localStorage.getItem("stored-obat"));

                obatalkes_id.find('option').remove();
                obatalkes_id.append($("<option></option>").attr("value", "").text("-- Pilih --"));

                if (response.length > 0) {
                    $.each(response, function(key, value) {
                        if (jQuery.inArray(value.stokobatpasien_id+"&"+value.id, storedObat) === -1) {
                            obatalkes_id.append($("<option></option>").attr("value", value.id).text(value.name+" - "+value.sisa));
                        } 
                    });
                }

                obatalkes_id.val("").trigger("change");
            }
        }
    });
});

$(document).on("change", ".obatalkes_id", function(event) {
    event.preventDefault();

    var obatalkes_id = $(this).val();
    var nomor = $(this).closest("tr").find(".no_res_rekon").val();
    var parent = $(this);

    if (obatalkes_id != '') {
        $.ajax({
            url:"/ranap/pemeriksaan-rawat-inap/get-stok-obat-pasien?id="+pendaftaran_id+"&obatalkes_id="+obatalkes_id+"&nomor="+nomor,
            type: "GET",
            dataType: "json",
            success: function(response) {
                if (response) {
                    var tr = parent.closest("tr");
                    var storedObat = [];

                    var signa_obat = tr.find("input.signa_obat");
                    var signa = tr.find("span.signa");
                    var nama_dokter = tr.find("span.nama_dokter");
                    var dokter_id = tr.find("input.dokter_id");
                    var jenisobat_id = tr.find("input.jenisobat_id");
                    var stokobatpasien_id = tr.find("input.stokobatpasien_id");
                    var sisa = tr.find("input.sisa");
                    var nama_obat = tr.find("input.nama_obat");
                    var wkt_pemberian = tr.find(".wkt_pemberian");

                    tr.find(".no_res_rekon").addClass("cek-inputan");
                    tr.find(".obatalkes_id").addClass("cek-inputan");
                    tr.find(".jumlah").addClass("cek-inputan");
                    tr.find(".wkt_pemberian").addClass("cek-inputan");
                    tr.find(".pemberi1_id").addClass("cek-inputan");
                    tr.find(".efek").addClass("cek-inputan");
                    tr.find(".keterangan").addClass("cek-inputan");

                    signa_obat.val(response.signa);
                    dokter_id.val(response.dokter_id);
                    jenisobat_id.val(response.jenisobatalkes_id);
                    stokobatpasien_id.val(response.stokobatpasien_id);
                    sisa.val(response.stok_sisa);
                    signa.text(response.signa);
                    nama_dokter.text(response.dokter);
                    nama_obat.val(response.nama_obat);

                    if (response.tglpenjualan != null) {
                        wkt_pemberian.datetimepicker("setStartDate", response.tglpenjualan);
                    }

                    if (JSON.parse(localStorage.getItem("stored-obat")) != null) {
                        storedObat = JSON.parse(localStorage.getItem("stored-obat"));
                    } else {
                        storedObat.push(response.stokobatpasien_id+"&"+response.obatalkes_id);
                        localStorage.setItem("stored-obat", JSON.stringify(storedObat));
                    }

                    if (jQuery.inArray(response.stokobatpasien_id+"&"+response.obatalkes_id, storedObat) === -1) {
                        storedObat.push(response.stokobatpasien_id+"&"+response.obatalkes_id);
                        localStorage.setItem("stored-obat", JSON.stringify(storedObat));
                    }
                } else {
                    var signa_obat = tr.find("input.signa_obat");
                    var signa = tr.find("span.signa");
                    var nama_dokter = tr.find("span.nama_dokter");
                    var dokter_id = tr.find("input.dokter_id");
                    var jenisobat_id = tr.find("input.jenisobat_id");
                    var stokobatpasien_id = tr.find("input.stokobatpasien_id");
                    var sisa = tr.find("input.sisa");
                    var nama_obat = tr.find("input.nama_obat");
                    var wkt_pemberian = tr.find(".wkt_pemberian");

                    tr.find(".no_res_rekon").removeClass("cek-inputan");
                    tr.find(".obatalkes_id").removeClass("cek-inputan");
                    tr.find(".jumlah").removeClass("cek-inputan");
                    tr.find(".wkt_pemberian").removeClass("cek-inputan");
                    tr.find(".pemberi1_id").removeClass("cek-inputan");
                    tr.find(".efek").removeClass("cek-inputan");
                    tr.find(".keterangan").removeClass("cek-inputan");

                    signa_obat.val("");
                    dokter_id.val("");
                    jenisobat_id.val("");
                    stokobatpasien_id.val("");
                    sisa.val("");
                    signa.text("");
                    nama_dokter.text("");
                    nama_obat.val("");
                }
            }
        });
    } else {
        var tr = parent.closest("tr");
        var signa_obat = tr.find("input.signa_obat");
        var signa = tr.find("span.signa");
        var nama_dokter = tr.find("span.nama_dokter");
        var dokter_id = tr.find("input.dokter_id");
        var jenisobat_id = tr.find("input.jenisobat_id");
        var stokobatpasien_id = tr.find("input.stokobatpasien_id");
        var sisa = tr.find("input.sisa");
        var nama_obat = tr.find("input.nama_obat");

        tr.find(".no_res_rekon").removeClass("cek-inputan");
        tr.find(".obatalkes_id").removeClass("cek-inputan");
        tr.find(".jumlah").removeClass("cek-inputan");
        tr.find(".wkt_pemberian").removeClass("cek-inputan");
        tr.find(".pemberi1_id").removeClass("cek-inputan");
        tr.find(".efek").removeClass("cek-inputan");
        tr.find(".keterangan").removeClass("cek-inputan");

        signa_obat.val("");
        dokter_id.val("");
        jenisobat_id.val("");
        stokobatpasien_id.val("");
        sisa.val("");
        signa.text("");
        nama_dokter.text("");
        nama_obat.val("");
    }
});

$(document).on("change", ".jumlah", function(event) {
    event.preventDefault();

    var tr = $(this).closest("tr");
    var sisa = tr.find(".sisa").val();

    if (parseInt($(this).val()) > parseInt(sisa)) {
        docoNotification('error', "Kelebihan jumlah!", "Jumlah yang diinputkan lebih besar dari jumlah sisa obat.");

        $(this).val('');
    }
});

$(".btn-cetak-pemberian-obat").on("click", function(event) {
    event.preventDefault();
    if (tableRiwayatPO.length == 0) {
        docoNotification('error', "Proses Cetak Gagal!", "Tidak ada data yang bisa cetak");
        return false;
    }
    window.open($(this).data("url"));
});

$(document).on("click", ".btn-kembali-pemberian-obat", function(event) {
    event.preventDefault();

    var hashUrl = localStorage.getItem("hash-url");
    $('.tabbable ul li a[href="'+ hashUrl +'"]').click()
    localStorage.removeItem('hash-url');
});

$(".newselect").select2();

// Fungsi tambah row pemberian obat
function tambahPemberianObat(index) {
    var panelBody = $(index).parent().parent().parent().find(".panel-body");
    var cek = [];

    panelBody.find(".cek-inputan").each(function() {
        if ($(this).val() != null) {
            cek.push($(this).val());
        }
        else {
            cek.push("");
        }
    });

    if (jQuery.inArray("", cek) !== -1) {
        docoNotification('error', "Tidak bisa tambah pemberian obat!", "Ada field yang belum diisi.");

        $(".newselect").select2();
    } else {
        if ($(".newselect").hasClass("select2-hidden-accessible")) {
            $(".newselect").select2("destroy");
        }

        var cloningTr = panelBody.find(".dataTable .cloning-tr:first");
        var clone = cloningTr.clone();
        var selectJenisObat = '';
        var count = parseInt($(index).val()) + 1;
        var jenisobat_id = clone.find(".jenisobat_id").val();
        $(".btn-tambah-pemberian-obat").val(count);

        panelBody.find(".dataTable .cloning-tr").removeClass("cloning-tr");
        clone.find(":text").val("");
        clone.find("span").text("");
        cloningTr.after(clone);var jenisobat_id = clone.find(".jenisobat_id").val();

        $(".newselect").select2();

        if (listObatPasien[jenisobat_id]) {
            selectJenisObat = clone.find(".no_res_rekon");
            selectJenisObat.find('option').remove();
            selectJenisObat.append($("<option></option>").attr("value", "").text("-- Pilih --"));

            selectObat = clone.find(".obatalkes_id");
            selectObat.prop("disabled", true);
            selectObat.find('option').remove();
            selectObat.append($("<option></option>").attr("value", "").text("-- Pilih --"));

            $.each(listObatPasien[jenisobat_id], function(key, value) {
                selectJenisObat.append($("<option></option>").attr("value", key).text(value));
            });

            clone.find(".no_res_rekon").prop("name", "PemberianObatDetailForm["+count+"][no_res_rekon]");
            clone.find(".obatalkes_id").prop("name", "PemberianObatDetailForm["+count+"][obatalkes_id]");
            clone.find(".jumlah").prop("name", "PemberianObatDetailForm["+count+"][jumlah]");
            clone.find(".wkt_pemberian").prop("name", "PemberianObatDetailForm["+count+"][wkt_pemberian]");
            clone.find(".pemberi1_id").prop("name", "PemberianObatDetailForm["+count+"][pemberi1_id]");
            clone.find(".efek").prop("name", "PemberianObatDetailForm["+count+"][efek]");
            clone.find(".keterangan").prop("name", "PemberianObatDetailForm["+count+"][keterangan]");
            clone.find(".dokter_id").prop("name", "PemberianObatDetailForm["+count+"][dokter_id]");
            clone.find(".stokobatpasien_id").prop("name", "PemberianObatDetailForm["+count+"][stokobatpasien_id]");
            clone.find(".jenisobat_id").prop("name", "PemberianObatDetailForm["+count+"][jenisobat_id]");
            clone.find(".signa_obat").prop("name", "PemberianObatDetailForm["+count+"][signa_obat]");
            clone.find(".pemberi2_id").prop("name", "PemberianObatDetailForm["+count+"][pemberi2_id]");
            clone.find(".is_resep").prop("name", "PemberianObatDetailForm["+count+"][is_resep]");
            clone.find(".nama_obat").prop("name", "PemberianObatDetailForm["+count+"][nama_obat]");

            clone.find(".no_res_rekon").removeClass("cek-inputan");
            clone.find(".obatalkes_id").removeClass("cek-inputan");
            clone.find(".jumlah").removeClass("cek-inputan");
            clone.find(".wkt_pemberian").removeClass("cek-inputan");
            clone.find(".pemberi1_id").removeClass("cek-inputan");
            clone.find(".efek").removeClass("cek-inputan");
            clone.find(".keterangan").removeClass("cek-inputan");

            if (clone.find(".wkt_pemberian").data("krajee-datetimepicker")) {
                var d = new Date($.now());
                var date = d.getDay()+"-"+d.getMonth()+"-"+d.getFullYear()+" "+d.getHours()+":"+d.getMinutes()+":"+d.getSeconds();

                clone.find(".wkt_pemberian").datetimepicker("destroy");
                clone.find(".wkt_pemberian").datetimepicker("setFormat", "dd-mm-yyyy HH:mm:ss");
                clone.find(".wkt_pemberian").datetimepicker("setValue", date);
            }
        }
    }
}

function simpanPemberianObat() {
    var cek = [];

    $(".cek-inputan").each(function() {
        if ($(this).val() != null) {
            cek.push($(this).val());
        }
        else {
            cek.push("");
        }
    });

    if (jQuery.inArray("", cek) !== -1) {
        docoNotification('error', "Tidak bisa menyimpan pemberian obat!", "Ada field yang belum diisi.");
    } else {
        $("#form-pemberian-obat").docoForm("click", {
            data: $("#form-pemberian-obat").serializeArray(),
            success : function(data) {
                $.each(tablePO, function(key, value) {
                    value.draw();
                });

                $.each(tableRiwayatPO, function(key, value) {
                    value.draw();
                });

                localStorage.removeItem('stored-obat');
                $(".tabbable").find("#tab-pemberian-obat").trigger("click");
            }
        });
    }
}

// Fungsi untuk menampilkan halaman retur obat
function showHalamanRetur() {
    if ($("#div-retur-obat").is(":hidden")) {
        $("#div-retur-obat").prop("hidden", false);
        $("#div-pemberian-obat").prop("hidden", true);

        $('#div-retur-obat').docoLoad({
            url: '/ranap/pemeriksaan-rawat-inap/permintaan-retur?id='+pendaftaran_id,
            dataType: 'html',
            success : function(data) {}
        });
    } else {
        $("#div-retur-obat").prop("hidden", true);
        $("#div-pemberian-obat").prop("hidden", false);
    }
}