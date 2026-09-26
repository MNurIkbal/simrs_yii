$(document).ready(function() {
    var list_pr = {};
    var last_barang = {
        oid: null,
        stok: null,
        last_7: 0,
        last_14: 0,
        last_30: 0,
        doi: null,
        ss_min: null,
        stok_gudang: null,
        stok_farmasi: null,
        stok_lain: null,
        konversi: 1,
        qty_outstanding: null,
    }

    $(`select[name="satuan_barang"]`).select2();
    $(`select[name="nama_barang"]`).select2({
        language: {
            errorLoading: function() { return "Please Wait .." }
        },
        placeholder: "Kode / Nama barang",
        minimumInputLength: 2,
        ajax: {
            url: "/pengadaan/purchase-requisition/search-item-barang",
            dataType: "json",
            quietMillis: 250,
            delay: 250,
        },
    });

    $(`select[name="nama_barang"]`).on("select2:select", function(e) {
        $("#stok_fisik").html("0");
        $(`select[name="satuan_barang"]`).html('').select2({data: [{id: '', text: ''}]});
        $(`select[name="satuan_barang"]`).find("Option").remove();
        var data_select = e.params.data;
        var data_satuan = data_select.satuan;
        var satuan_results = [];
        var selected;
        $.each(data_satuan, function(index, value) {
            var newOpt = new Option(value.sb, value.sbid, false, (value.sel > 0) ? true: false);
            if(value.sel > 0) {
                selected = value.sbid;
            }
            newOpt.setAttribute("data-konversi", value.konv);
            newOpt.setAttribute('label', value.lbl);
            newOpt.setAttribute('label-master', data_select.label_master);
            $(`select[name="satuan_barang"]`).append(newOpt);
        });
        $(`select[name="satuan_barang"]`).val(selected);
        getStok();
    });

    if(isLargeUnit) {
        $(`select[name="satuan_barang"]`).prop('disabled', false);
    }

    $("#btn-tambah").on("click", function() {
        var select_barang = $(`select[name="nama_barang"]`).select2("data")[0];
        var select_satuan = $(`select[name="satuan_barang"]`).select2("data")[0];

        var satuan = $(`select[name="satuan_barang"]`).find(":selected");
        var nilai_konversi = satuan.attr("data-konversi");

        if (!isbarangValid()) {
            return false;
        }

        var catatan         = $(`[name="catatan"]`).val();
        var barang_id       = select_barang.id;
        var barang_nama     = select_barang.nama;
        var kode_barang     = select_barang.kode;
        var qty_pr          = $(`[name="qty"]`).val();
        var satuan_id       = select_satuan.id;
        var satuan_nama     = select_satuan.text;
        var konversi_lbl    = $("#konversi").html()
        var stok_saatini    = last_barang.stok_saatini//stok dengan satuan kecil
        var stok_gudang     = $("#stok_gudang").html();
        var stok_farmasi    = last_barang.stok_farmasi;
        var stok_lain       = $("#stok_lain").html();
        var qty_outstanding = $("#qty_outstanding").html();
        var last_7          = $("#last_7").html();
        var last_14         = $("#last_14").html();
        var last_30         = $("#last_30").html();

        var newData = {
            barang_id      : barang_id,
            kode_barang    : kode_barang,
            barang_nama    : barang_nama,
            last_7         : last_7,
            last_14        : last_14,
            last_30        : last_30,
            doi            : null,
            ss_min         : null,
            stok_gudang    : stok_gudang,
            stok_farmasi   : stok_farmasi,
            stok_lain      : stok_lain,
            qty_outstanding: qty_outstanding,
            qty_suggestion : null,
            qty            : qty_pr,
            satuaninput_id : satuan_id,
            satuan         : satuan_nama,
            konversi_lbl   : konversi_lbl,
            catatan        : catatan,
            stok_saatini   : stok_saatini,
            nilai_konversi : nilai_konversi
        };

        list_pr[barang_id] = newData;
        appendbarang(list_pr);
        resetForm();
    });

    $(document).on("click",".btn-hapus", function() {
        var button = $(this);
        var index = button.attr("data-index");

        delete list_pr[index];

        appendbarang(list_pr);
    });

    $("#btn-submit").on("click", function() {
        var url = "/pengadaan/purchase-requisition/save";
        var catatan = $("#purchaserequisitionform-reference").val();
        var is_cyto = ($("#purchaserequisitionform-is_cyto").prop("checked") == true) ? 1 : 0;
        var is_admin = ($("#purchaserequisitionform-is_admin").prop("checked") == true) ? 1 : 0;

        if ($.isEmptyObject(list_pr)) {
            docoNotification("warning", "Data Tidak Lengkap", "List barang kosong");
            return false;
        }

        $(this).docoForm("click", {
            url: url,
            skipErrorNotif: true,
            data: {
                type: "barang",
                catatan: catatan,
                is_cyto: is_cyto,
                is_admin: is_admin,
                detail: list_pr,
            },
            success: function (data) {
                setTimeout(function() {
                    window.location.replace("/pengadaan/info-purchase-requisition/barang");
                }, 850);
            }
        });
    })

    function appendbarang(data){
        $("#table-barang-pr tbody").html("");
        var no = 1;
        $.each(data, function(index, row) {
            if (row == undefined) {
                return;
            }
            var str_tr = "";
            str_tr += "<tr>";
                str_tr += "<td>"+ no +"</td>";
                str_tr += "<td>"+ row.kode_barang +"</td>";
                str_tr += "<td>"+ row.barang_nama +"</td>";
                str_tr += "<td class='number-align'>"+ (row.last_7 == null ? '-' : row.last_7) +"</td>";
                str_tr += "<td class='number-align'>"+ (row.last_14 == null ? '-' : row.last_14) +"</td>";
                str_tr += "<td class='number-align'>"+ (row.last_30 == null ? '-' : row.last_30) +"</td>";
                str_tr += "<td class='number-align'>"+ (row.doi == null ? '-' : row.doi) +"</td>";
                str_tr += "<td class='number-align'>"+ (row.ss_min == null ? '-' : row.ss_min.toFixed(2)) +"</td>";
                str_tr += "<td class='number-align'>"+ row.stok_gudang +"</td>";
                str_tr += "<td class='number-align'>"+ row.stok_lain +"</td>";
                str_tr += "<td class='number-align'>"+ (row.qty_outstanding == null ? '-' : row.qty_outstanding) +"</td>";
                str_tr += "<td class='number-align'>"+ (row.qty_suggestion == null ? '-' : row.qty_suggestion) +"</td>";
                str_tr += `<td>
                        <input type="text"
                            id="qty_`+no+`"
                            name="qty_pr"
                            class="form-control doco-number small"
                            style="width: 70px; text-align: right"
                            value="`+ row.qty +`"
                            data-index="`+ index +`">
                        </td>`;
                str_tr += "<td>"+ row.satuan +"</td>";
                str_tr += "<td>"+ row.konversi_lbl +"</td>";
                str_tr += `<td>
                        <input type="text"
                            id="catatan_`+no+`"
                            name="catatan_pr"
                            class="form-control small"
                            style="width: 70px"
                            value="`+ row.catatan +`"
                            data-index="`+ index +`">
                        </td>`;
                str_tr += `<td><button class="btn btn-sm btn-danger btn-hapus"
                    data-index="`+ index +`"><i class="fa fa-minus"></i></button></td>`;
            str_tr += "</tr>";
            no++;
            $("#table-barang-pr tbody").append(str_tr);
        });
    }

    function resetForm(){
        $(`[name="nama_barang"], [name="satuan_barang"], [name="catatan"], [name="qty"],
            [name="nilai_konversi"]`)
            .val(null).trigger("change");
        $("#stok_gudang").html("0");
        $("#stok_farmasi").html("0");
        $("#stok_lain").html("0");
        $("#ss_min").html("0");
        $("#konversi").html("-");
        $("#last_7").html("0");
        $("#last_14").html("0");
        $("#last_30").html("0");
        $("#qty_outstanding").html("0");
    }

    $(document).on("change", `select[name="satuan_barang"]`, function () {
        var satuan = $(`select[name="satuan_barang"]`).find(":selected");
        konversiStok();
    })

    function getStok() {
        var oid = $(`select[name="nama_barang"]`).val();
        if (oid == null) {
            return false;
        }

        $.get("/pengadaan/purchase-requisition/get-stok-barang?barang_id=" + oid , function(data, status){
            var response = data.data.data;
            last_barang.oid = oid;
            last_barang.stok_saatini = response.qty_tersedia;
            last_barang.stok_gudang = response.stok_gudang;
            last_barang.stok_lain = response.stok_lain;
        }).then(function () {
            konversiStok();
        });
    }

    function konversiStok() {
        var satuan = $(`select[name="satuan_barang"]`).find(":selected");
        var konversi = satuan.attr("data-konversi");
        var stok_gudang = last_barang.stok_gudang;
        var stok_lain = last_barang.stok_lain;

        var konversi_gudang = stok_gudang / konversi;
        var konversi_lain = stok_lain / konversi;

        last_barang.konversi = konversi;

        if (isNaN(konversi_gudang) || konversi_gudang < 0) {
            konversi_gudang = "0";
        }

        if (isNaN(konversi_lain) || konversi_lain < 0) {
            konversi_lain = "0";
        }

        $("#stok_gudang").html(konversi_gudang);
        $("#stok_lain").html(konversi_lain);
        $("#qty").val("0");
        $("#konversi").html(satuan.attr('label'));
    }

    function isbarangValid() {
        if ($(`select[name="nama_barang"]`).val() == null) {
            docoNotification("warning", "Data Tidak Lengkap", "barang belum terpilih");
            return false;
        }

        if ($(`select[name="nama_barang"]`).val() in list_pr) {
             docoNotification("warning", "Data Tidak Lengkap", "barang sudah ada pada list");
             return false;
        }

        if ($(`select[name="satuan_barang"]`).val() == null) {
            docoNotification("warning", "Data Tidak Lengkap", "Satuan belum terpilih");
            return false;
        }

        if ($(`[name="qty"]`).val() <= 0) {
            docoNotification("warning", "Data Tidak Lengkap", "Qty PR tidak boleh kurang kosong atau dari 0");
            return false;
        }

        return true;
    }

    $(".selectPegawai").select2({
        minimumInputLength: 3,
        data: [dataUser],
        ajax : {
            url: baseUrl+"pengadaan/purchase-requisition/search-pegawai",
            dataType: "json",
            quietMillis: 250,
            data: function (params) {
                var query = {
                search: params,
                }
                return params;
            },
            processResults: function (data) {
                $.each(data.result, function (key,val) {
                });
                return {
                results: data.result
                };
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        },
    });

    $(".selectPegawai").val(1).trigger('change');    
});
