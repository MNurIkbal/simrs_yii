$(document).ready(function() {

    var list_pr = {};
    var last_obat = {
        oid: null,
        doi: null,
        ss_min: null,
        stok_gudang: null,
        stok_farmasi: null,
        stok_lain: null,
        konversi: 1,
        qty_outstanding: null,
        last_7: 0,
        last_14: 0,
        last_30: 0
    }

    var url_list_pemakaian = "/pengadaan/purchase-requisition/list-pemakaian?id=";

    $(".data-reset").on("click", function() {

        $("#table-obat-pr tbody").html("");
        list_pr = {};
        last_obat = {
            oid: null,
            doi: null,
            ss_min: null,
            stok_gudang: null,
            stok_farmasi: null,
            stok_lain: null,
            konversi: 1,
            qty_outstanding: null,
            last_7: null,
            last_14: null,
            last_30: null
        }
        resetForm();

    });

    if(isLargeUnit) {
        $(`select[name="satuan_obat"]`).prop('disabled', false);
    }

    $(`select[name="satuan_obat"]`).select2();
    $(`select[name="nama_obat"]`).select2({
        language: {
            errorLoading: function() { return "Please Wait .." }
        },
        placeholder: "Kode / Nama Obat",
        minimumInputLength: 2,
        ajax: {
            url: '/pengadaan/purchase-requisition/search-obat',
            data: function(params) {
                var query = {
                    term: params.term,
                    is_consignment: $("#purchaserequisitionform-is_consignment").is(":checked")
                };

                return query;
            },
            dataType: 'json',
            quietMillis: 250,
            delay: 250,
        },
    });

    $(`select[name="nama_obat"]`).on('select2:select', function(e) {
        $("#stok_gudang").html("0");
        $("#stok_farmasi").html("0");
        $("#stok_lain").html("0");
        $("#ss_min").html("0");
        $("#konversi").html("-");
        $("#last_7").html("0");
        $("#last_14").html("0");
        $("#last_30").html("0");
        $("#qty_outstanding").html("0");
        $(`select[name="satuan_obat"]`).html('').select2({data: [{id: '', text: ''}]});
        $(`select[name="satuan_obat"]`).find('Option').remove();
        var data_select = e.params.data;
        var data_satuan = data_select.satuan;
        var selected;
        $.each(data_satuan, function(index, value) {
            var newOpt = new Option(value.sb, value.sbid, false, (value.sel > 0) ? true: false);
            if(value.sel > 0) {
                selected = value.sbid;
            }
            newOpt.setAttribute('data-konversi', value.konv);
            newOpt.setAttribute('label', value.lbl);
            newOpt.setAttribute('label-master', data_select.label_master);
            $(`select[name="satuan_obat"]`).append(newOpt);
        });
        $(`select[name="satuan_obat"]`).val(selected);
        $(`select[name="satuan_obat"]`).trigger('change');
        getStok();
        getPoOutstanding();
        getStockBoundary();
        getPemakaian();
        $(`select[name="satuan_obat"]`).trigger('change');
        $("#btn-tambah").attr("disabled", true);
    });

    $("#btn-tambah").on('click', function() {
        var select_obat = $(`select[name="nama_obat"]`).select2('data')[0];
        var select_satuan = $(`select[name="satuan_obat"]`).select2('data')[0];
        
        var satuan = $(`select[name="satuan_obat"]`).find(':selected');
        var nilai_konversi = satuan.attr('data-konversi');

        if (!isObatValid()) {
            return false;
        }

        var catatan      = $(`[name="catatan"]`).val();
        var obat_id      = select_obat.id;
        var obat_nama    = select_obat.nama;
        var obat_kode    = select_obat.kode;
        var qty_pr       = $(`[name="qty"]`).val();
        var satuan_id    = select_satuan.id;
        var satuan_nama  = select_satuan.text;
        var konversi_lbl = $("#konversi").html()
        var stok_rs      = $("#stok_rs").html() == "-"  ? 0 : $("#stok_rs").html();
        var stok_saatini = last_obat.stok_rs //stok dengan satuan kecil
        var stok_gudang  = $("#stok_gudang").html() == "-"  ? 0 : $("#stok_gudang").html();
        var stok_farmasi = $("#stok_farmasi").html() == "-"  ? 0 : $("#stok_farmasi").html();
        var stok_lain    = $("#stok_lain").html() == "-"  ? 0 : $("#stok_lain").html();
        var qty_outstanding = $("#qty_outstanding").html() == "-"  ? 0 : $("#qty_outstanding").html();
        var last_7       = $("#last_7").html() == "-"  ? 0 : $("#last_7").html();
        var last_14      = $("#last_14").html() == "-"  ? 0 : $("#last_14").html();
        var last_30      = $("#last_30").html() == "-"  ? 0 : $("#last_30").html();
        var newData = {
            catatan        : catatan,
            kode_obat      : obat_kode,
            nma_obat       : obat_nama,
            obatalkes_id   : obat_id,
            qty            : qty_pr,
            qty_suggestion : null,
            satuan         : satuan_nama,
            konversi_lbl   : konversi_lbl,
            satuaninput_id : satuan_id,
            stok_gudang    : stok_gudang,
            stok_farmasi   : stok_farmasi,
            stok_lain      : stok_lain,
            doi            : null,
            ss_min         : null,
            stok_saatini   : stok_saatini,
            last_7         : last_7,
            last_14        : last_14,
            last_30        : last_30,
            qty_outstanding: qty_outstanding,
            nilai_konversi : nilai_konversi
        };

        list_pr[obat_id] = newData;
        appendObat(list_pr);
        resetForm();
    });

    $(document).on('click',".btn-hapus", function() {
        var button = $(this);
        var index = button.attr('data-index');

        delete list_pr[index];

        appendObat(list_pr);
    });

    $("#btn-submit").on('click', function() {
        var url = "/pengadaan/purchase-requisition/save";
        var catatan = $("#purchaserequisitionform-reference").val();
        var is_cyto = ($("#purchaserequisitionform-is_cyto").prop('checked') == true) ? 1 : 0;
        var is_consignment = $("#purchaserequisitionform-is_consignment").is(":checked");
        var is_admin = $("#purchaserequisitionform-is_admin").is(":checked");

        if ($.isEmptyObject(list_pr)) {
            docoNotification('warning', "Data Tidak Lengkap", "List obat kosong");
            return false;
        }

        $.ajax({
            type:'POST',
            url: url,
            data: JSON.stringify({
                type: "obat",
                catatan: catatan,
                is_cyto: is_cyto,
                detail: list_pr,
                is_consignment: is_consignment,
                is_admin: is_admin
            }),
            contentType:'application/json;charset=utf-8',
            dataType: 'json',
            success: function(data) {   
                docoNotification('success', "Berhasil", "Data berhasil di simpan");
                setTimeout(function() {
                    window.location.replace("/pengadaan/info-purchase-requisition");
                }, 850);
            }, error: function (response) {
                var data = response.responseJSON
                
                if (data.message != undefined) {
                    docoNotification('error', "Proses Gagal", ""+data.message+".");
                }
            }
        });
    })

    $(document).on('keyup', `input[name="qty_pr"]`, function () {
        var row = $(this);
        var index = row.attr('data-index');
        var _qty = row.val();

        list_pr[index].qty = _qty;

        let is_consignment = $("#purchaserequisitionform-is_consignment").is(":checked");
        let qty_suggest = parseInt($("#qty_suggestion_"+index).text());
        if(is_consignment && parseInt(_qty) > qty_suggest) {
            $(this).val(qty_suggest)
        }
    })

    $(document).on('keyup', `input[name="catatan_pr"]`, function () {
        var row = $(this);
        var index = row.attr('data-index');
        var _catatan = row.val();

        list_pr[index].catatan = _catatan;
    })

    function appendObat(data){
        $("#table-obat-pr tbody").html("");
        var no = 1;
        $.each(data, function(index, row) {
            if (row == undefined) {
                return;
            }

            isReorder = '';
            if (row.reorder != undefined) {
                isReorder = row.reorder == true ? 1 : 0;
            }

            var str_tr = "";
            str_tr += "<tr class='small' data-isreorder='"+isReorder+"'>";
                str_tr += "<td>"+ no +"</td>";
                str_tr += "<td>"+ row.kode_obat +"</td>";
                str_tr += "<td>"+ row.nma_obat +"</td>";
                str_tr += "<td class='number-align'> <button class='btn btn-link link-info-pemakaian' data-toggle='modal' data-target='#modal_backdrop' data-width='50%' action='"+url_list_pemakaian+index+"&data_pemakaian=7' style='font-size: 10px'>"+ (row.last_7 == null ? '-' : row.last_7) +"</button> </td>";
                str_tr += "<td class='number-align'> <button class='btn btn-link link-info-pemakaian' data-toggle='modal' data-target='#modal_backdrop' data-width='50%' action='"+url_list_pemakaian+index+"&data_pemakaian=14' style='font-size: 10px'>"+ (row.last_14 == null ? '-' : row.last_14) +"</button> </td>";
                str_tr += "<td class='number-align'> <button class='btn btn-link link-info-pemakaian' data-toggle='modal' data-target='#modal_backdrop' data-width='50%' action='"+url_list_pemakaian+index+"&data_pemakaian=30' style='font-size: 10px'>"+ (row.last_30 == null ? '-' : row.last_30) +"</button> </td>";
                str_tr += "<td class='number-align'>"+ (row.doi == null ? '-' : row.doi) +"</td>";
                str_tr += "<td class='number-align'>"+ (row.ss_min == null ? '-' : row.ss_min) +"</td>";
                str_tr += "<td >"+ (row.move_category == null ? '-' : row.move_category) +"</td>";
                str_tr += "<td class='number-align'> <button class='btn btn-link number-align link-info-stok' data-ruangan='gudang' data-index='"+index+"' style='font-size: 10px'>"+ row.stok_gudang +"</button> </td>";
                str_tr += "<td class='number-align'> <button class='btn btn-link number-align link-info-stok' data-ruangan='farmasi' data-index='"+index+"' style='font-size: 10px'>"+ row.stok_farmasi +"</button> </td>";
                str_tr += "<td class='number-align'> <button class='btn btn-link number-align link-info-stok' data-ruangan='rungan_lain' data-index='"+index+"' style='font-size: 10px'>"+ row.stok_lain +"</button> </td>";
                str_tr += "<td class='number-align'>"+ (row.qty_outstanding == null ? '-' : row.qty_outstanding) +"</td>";
                str_tr += "<td class='number-align' id='qty_suggestion_"+index+"'>"+ (row.qty_suggestion == null ? '-' : row.qty_suggestion) +"</td>";
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
            $("#table-obat-pr tbody").append(str_tr);
        });
        $('.small[data-isreorder="0"]').attr('style', 'background-color:antiquewhite')
    }

    function resetForm(){
        $(`[name="nama_obat"], [name="satuan_obat"], [name="catatan"], [name="qty"],
            [name="nilai_konversi"]`)
            .val(null).trigger('change');
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

    $(document).on('change', `select[name="satuan_obat"]`, function () {
        var satuan = $(`select[name="satuan_obat"]`).find(':selected');

        $("#stok_gudang").html("-");
        $("#stok_farmasi").html("-");
        $("#stok_lain").html("-");
        $("#konversi").html(satuan.attr('label-master'));
        konversiStok();
    })

    function getPoOutstanding() {
        var oid = $(`select[name="nama_obat"]`).val();
        if (oid == null) {
            return false;
        }

        $.get('/pengadaan/purchase-requisition/get-po-outstanding?oid='+oid, function(data, status){
            var response = data.response;
            last_obat.oid = oid;
            last_obat.qty_outstanding = response.qty_outstanding;
        }).then(function() {
            //$("#qty_outstanding").html(last_obat.qty_outstanding);
            konversiStok();
        });
    }

    function getStok() {
        var oid = $(`select[name="nama_obat"]`).val();
        if (oid == null) {
            return false;
        }

        $.get('/pengadaan/purchase-requisition/get-stok?oid='+oid, function(data, status){
            var response = data.response;
            last_obat.oid = oid;
            last_obat.stok_gudang = response.stok_gudang;
            last_obat.stok_farmasi = response.stok_farmasi;
            last_obat.stok_lain = response.stok_lain;
        }).then(function () {
            konversiStok();
        });
    }

    function getPemakaian() {
        var oid = $(`select[name="nama_obat"]`).val();
        if (oid == null) {
            return false;
        }

        $.get('/pengadaan/purchase-requisition/get-pemakaian?oid='+oid, function(data, status){
            var response = data.response;
            last_obat.oid = oid;
            last_obat.last_7 = response.last_7;
            last_obat.last_14 = response.last_14;
            last_obat.last_30 = response.last_30;
        }).then(function() {
            $("#last_7").html(last_obat.last_7);
            $("#last_14").html(last_obat.last_14);
            $("#last_30").html(last_obat.last_30);
            $("#btn-tambah").attr("disabled", false);
        });
    }

    function konversiStok() {
        var satuan = $(`select[name="satuan_obat"]`).find(':selected');
        var konversi = satuan.attr('data-konversi');
        var stok_gudang = last_obat.stok_gudang;
        var stok_farmasi = last_obat.stok_farmasi;
        var stok_lain = last_obat.stok_lain;
        
        var konversi_gudang = stok_gudang / konversi;
        var konversi_farmasi = stok_farmasi / konversi;
        var konversi_lain = stok_lain / konversi;

        last_obat.konversi = konversi;

        if (isNaN(konversi_gudang) || konversi_gudang < 0) {
            konversi_gudang = "0";
        }

        if (isNaN(konversi_farmasi) || konversi_farmasi < 0) {
            konversi_farmasi = "0";
        }

        if (isNaN(konversi_lain) || konversi_lain < 0) {
            konversi_lain = "0";
        }

        $("#stok_gudang").html(konversi_gudang);
        $("#stok_farmasi").html(konversi_farmasi);
        $("#stok_lain").html(konversi_lain);
        $("#qty").val("0");

        var qty_outstanding = last_obat.qty_outstanding;
        var konversi_outstanding = qty_outstanding / konversi;
        if (isNaN(konversi_outstanding) || konversi_outstanding < 0) {
            konversi_outstanding = "0";
        }
        $("#qty_outstanding").html(konversi_outstanding);
    }

    function getStockBoundary() {
        var oid = $(`select[name="nama_obat"]`).val();
        if (oid == null) {
            return false;
        }

        $.get('/pengadaan/purchase-requisition/get-stock-boundary?oid='+oid, function(data, status){
            var response = data.response;
        }).then(function() {
            konversiStok();
        });
    }

    function isObatValid() {
        if ($(`select[name="nama_obat"]`).val() == null) {
            docoNotification('warning', "Data Tidak Lengkap", "Obat belum terpilih");
            return false;
        }

        if ($(`select[name="nama_obat"]`).val() in list_pr) {
             docoNotification('warning', "Data Tidak Lengkap", "Obat sudah ada pada list");
             return false;
        }

        if ($(`select[name="satuan_obat"]`).val() == null) {
            docoNotification('warning', "Data Tidak Lengkap", "Satuan belum terpilih");
            return false;
        }

        if ($(`[name="qty"]`).val() <= 0) {
            docoNotification('warning', "Data Tidak Lengkap", "Qty PR tidak boleh kurang kosong atau dari 0");
            return false;
        }

        return true;
    }

    $(document).on('click',"#btn-generate-ro",function(event) {
        event.preventDefault();
        let is_consignment = $("#recommendationorderform-is_consignment").is(":checked");
        var jenisobatalkes_id = []
        $.each(checked_jenis, function(key, val){
            jenisobatalkes_id.push(val.jenisobatalkes_id)
        })
        
        $(this).docoForm('click', {
            url: '/pengadaan/purchase-requisition/recommendation-order',
            data: {
                doi: $("#days_of_inventory").val(),
                jenisobatalkes_id: jenisobatalkes_id,
                is_consignment: is_consignment
            },
            skipConfirm: true,
            skipSuccessNotif: true,
            success : function(response) {
                $('#modal_ro').modal('toggle');
                $("#purchaserequisitionform-is_consignment").prop('checked', is_consignment)
                let res = response.data.data;
                if(res.length > 0) {
                    $.each(res, function(index, data) {
                        var newData = {
                            catatan        : "Rekomendasi Order",
                            kode_obat      : data.obatalkes_kode,
                            nma_obat       : data.obatalkes_nama,
                            obatalkes_id   : data.obatalkes_id,
                            qty_suggestion : data.rekomendasi_order,
                            qty            : data.rekomendasi_qty_po,
                            satuan         : data.satuan_rekomendasi_po,
                            //konversi_lbl   : "1 "+ data.satuan_rekomendasi_po + " = " + data.nilai_konversi + " " + data.satuankecil_nama,
                            konversi_lbl   : data.konversi_label,
                            satuaninput_id : data.satuankecil_id,
                            stok_rs        : data.stok_rs,
                            stok_gudang    : data.stok_gudang.toString().replace(",", "."),
                            stok_farmasi   : data.stok_farmasi.toString().replace(",", "."),
                            stok_lain      : data.stok_lain.toString().replace(",", "."),
                            doi            : data.doi,
                            ss_min         : data.ss_min.toString().replace(",", "."),
                            stok_saatini   : data.stok_saatini,
                            last_7         : data.last_7.toString().replace(",", "."),
                            last_14        : data.last_14.toString().replace(",", "."),
                            last_30        : data.last_30.toString().replace(",", "."),
                            qty_outstanding: data.qty_outstanding,
                            reorder: data.reorder,
                            move_category_id: data.move_category_id,
                            move_category: data.move_category,
                            nilai_konversi  : data.nilai_konversi
                        };

                        list_pr[data.obatalkes_id] = newData;
                        appendObat(list_pr);
                    })

                    docoNotification('success', "Berhasil", "Data rekomendasi selesai dibuat.");
                    return true;
                } else {
                    docoNotification('error', "Data Tidak Ditemukan", "Tidak terdapat data rekomendasi.");
                    return false;
                }
            }
        });
    });

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

    $(document).on('click',".link-info-stok", function() {
        var button = $(this);
        var index = button.attr('data-index');
        var ruangan = button.attr('data-ruangan');
        if (index == null) {
            return false;
        }
        getInfoStokObatRuangan(index, ruangan);
    });

    function getInfoStokObatRuangan(index, ruangan) {
        $.get('/pengadaan/purchase-requisition/ruangan-stok-obat?oid='+index, function(data, status){
            var data = data.data.data;
            obatalkes_id = data.obatalkes_id;
            obatalkes_nama = data.obatalkes_nama;
            gudang_farmasi = data.gudang_farmasi;
            farmasi_utama = data.farmasi_utama;
            ruangan_lain = data.ruangan_lain;
        }).then(function () {
            var url = '/apotek/informasi-stok-obatalkes?';
            var params_ruangan = '';
            switch(ruangan) {
                case 'gudang':
                    params_ruangan = $.param({ ruangan_ids: gudang_farmasi });
                    break;
                case 'farmasi':
                    params_ruangan = $.param({ ruangan_ids: farmasi_utama });
                    break;
                default:
                    params_ruangan = $.param({ ruangan_ids: ruangan_lain });
            }
            var params = params_ruangan+"&obatalkes_nama="+obatalkes_nama
            window.open(url+params.toString(), '_blank');
        });
    }
});
