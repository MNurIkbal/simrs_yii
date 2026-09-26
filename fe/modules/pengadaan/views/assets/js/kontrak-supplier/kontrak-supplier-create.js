/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 * @last modified by: Muhamad Lukman Hakim
 * @date : 2020-09-21
 */

 $(document).ready(function() {
	var list_obat = {};
    var pajak_id = "";
    var baseController = "/pengadaan/kontrak-supplier/";

    $("#supplier_id").select2({
        placeholder: "Pilih Supplier",
        minimumInputLength: 3,
        ajax: {
            url: baseController + "search-supplier",
            dataType: "json",
            quietMillis: 250,
            data: function(params) {
                var query = {
                    search: params,
                }
                return params;
            },
            processResults: function(data) {
                return {
                    results: data.result
                };
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function(m) { return m; },
        },
    });

    $("#pajak_id").select2();

    $("#supplier_id").on('select2:select', function(e) {
        pajak_id = e.params.data.pajak_id == null ? "" : e.params.data.pajak_id;
        $("#pajak_id").val(pajak_id).trigger("change");        
    });

    $(`select[name="uom"]`).select2();
    $(`select[name="nama_obat"]`).select2({
        language: {
            errorLoading: function() { return "Please Wait .." }
        },
        placeholder: "Kode / Nama Obat",
        minimumInputLength: 2,
        ajax: {
            url: baseController + 'search-obat',
            dataType: 'json',
            quietMillis: 250,
            delay: 250,
        },
    });

    $(`select[name="nama_obat"]`).on('select2:select', function(e) {
        $("#stok_fisik").html("0");
        $(`select[name="uom"]`).find('Option').remove();
        var data_select = e.params.data;
        var data_satuan = data_select.satuan;
        var satuan_results = [];
        $.each(data_satuan, function(index, value) {
            var newOpt = new Option(value.lbl, value.sbid, false, false);
            newOpt.setAttribute('data-konversi', value.konv);
            $(`select[name="uom"]`).append(newOpt).trigger('change');
        });
        $(`select[name="uom"]`).trigger('change');
        $(`select[name="uom"]`).prop('disabled', false);
    });

    $(document).on('click',".btn-hapus", function() {
        var button = $(this);
        var index = button.attr('data-index');
        delete list_obat[index];
        appendObat(list_obat);
    });

    $(`input[name="KontrakSupplierForm[kontraksupplier_no]"]`).on("blur", function() {
        var no_kontraksupplier = $(`input[name="KontrakSupplierForm[kontraksupplier_no]"]`).val();
        var is_exist_no_kontrak = list_no_kontraksupplier.indexOf(no_kontraksupplier);
        if(is_exist_no_kontrak != -1) {
            docoNotification('warning', "Data Sudah Ada", "No Kontrak Supplier sudah ada");
            return false;
        }
    });

    $("#btn-tambah").on('click', function() {
        var select_obat = $(`select[name="nama_obat"]`).select2('data')[0];
        var select_uom = $(`select[name="uom"]`).select2('data')[0];

        if (!isObatValid()) {
            return false;
        }

        var obat_id      = select_obat.id;
        var obat_nama    = select_obat.text;
        var uom_id	     = select_uom.id;
        var uom_text     = select_uom.text;
        var harga_order  = docoHelper.convertToAngka($("#harga_order").val());
        var pengurang	 = docoHelper.convertToAngka($("#pengurang").val());
        var total_harga  = docoHelper.convertToAngka($("#total_harga").text());

        var newData = {
            nama_obat      : obat_nama,
            obatalkes_id   : obat_id,
            qty_minimum    : 1,
            uom_id		   : uom_id,
            uom_text       : uom_text,
            harga_order    : harga_order,
            pengurang	   : pengurang,
            penambah       : 0,
            total_harga	   : total_harga
        };

        list_obat[obat_id] = newData;
        appendObat(list_obat);
        resetFormObat();
    });

    function appendObat(data){
        $("#table-obat-kontrak-supplier tbody").html("");
        $.each(data, function(index, row) {
            if (row == undefined) {
                return;
            }
            var str_tr = "";
            str_tr += "<tr>";
                str_tr += "<td>"+ row.nama_obat +"</td>";
                str_tr += "<td>"+ row.uom_text +"</td>";
                str_tr += "<td class='text-right'>Rp "+ docoHelper.convertToRupiah(row.harga_order) +"</td>";
                str_tr += "<td class='text-center'>"+ docoHelper.convertToRupiah(row.pengurang) +"%</td>";
                str_tr += "<td class='text-right'>Rp "+ docoHelper.convertToRupiah(row.total_harga) +"</td>";
                str_tr += `<td><button class="btn btn-sm btn-danger btn-hapus"
                    data-index="`+ index +`"><i class="fa fa-close"></i></button></td>`;
            str_tr += "</tr>";
            $("#table-obat-kontrak-supplier tbody").append(str_tr);
        });
    }

    if(!$.isEmptyObject(list_detail)) {
        $.each(list_detail, function(index, row) {
            if (row == undefined) {
                    return;
            }
            var obat_id      = row.obatalkes_id;
            var obat_nama    = row.nama_obat;
            var uom_id       = row.uom_id;
            var uom_text     = row.uom_text;
            var qty_minimum  = 1;
            var harga_order  = row.harga;
            var pengurang    = row.pengurang;
            var penambah     = 0;
            var total_harga  = row.total_harga;
            var kontraksupplierdetail_id = row.kontraksupplierdetail_id;

            var newData = {
                kontraksupplierdetail_id    : kontraksupplierdetail_id,
                nama_obat                   : obat_nama,
                obatalkes_id                : obat_id,
                qty_minimum                 : qty_minimum,
                uom_id                      : uom_id,
                uom_text                    : uom_text,
                harga_order                 : harga_order,
                pengurang                   : pengurang,
                penambah                    : penambah,
                total_harga                 : total_harga
            };

            list_obat[obat_id] = newData;
            appendObat(list_obat);
        });
    }

    $("#btn-simpan").on('click', function() {
        var no_kontraksupplier = $(`input[name="KontrakSupplierForm[kontraksupplier_no]"]`).val();
        var is_exist_no_kontrak = list_no_kontraksupplier.indexOf(no_kontraksupplier);
        if(is_exist_no_kontrak != -1) {
            docoNotification('warning', "Data Sudah Ada", "No Kontrak Supplier sudah ada");
            return false;
        }

        if ($.isEmptyObject(list_obat)) {
            docoNotification('warning', "Data Tidak Lengkap", "List obat kosong");
            return false;
        }
        var _data = $("#create-kontrak-supplier-form").serializeArray();
        _data.push({
            name : "list_obat",
            value : JSON.stringify(list_obat)
        });

        $(this).docoForm("click", {
            url: baseController + "save",
            data: _data,
            success: function(data) {
                (new PNotify({
                    title: "Berhasil",
                    text: "Kontrak Supplier dengan Nomor " + "<strong>" + data.response.data.no_kontrak_supplier + "</strong>" + " berhasil disimpan, apakah Anda ingin melakukan input data baru?",
                    addclass: "alert alert-success alert-arrow-right alert-styled-right",
                    type: "success",
                    buttons: {
                        closer: false,
                        sticker: false
                    },
                    hide: false,
                    confirm: {
                        confirm: true,
                        buttons: [
                            {
                                text: 'Ya',
                                addClass: 'btn btn-xs btn-success',
                            },
                            {
                                text: 'Tidak',
                                addClass: 'btn btn-xs btn-danger',
                            }
                        ]
                    },
                    history: {
                        history: false
                    }
                })).get().on('pnotify.confirm', function() {
                    location.reload();
                }).on('pnotify.cancel', function() {
                    window.location.replace(baseController);
                });
            }
        });
    })

    $("#btn-edit").on('click', function() {
        if ($.isEmptyObject(list_obat)) {
            docoNotification('warning', "Data Tidak Lengkap", "List obat kosong");
            return false;
        }
        var _data = $("#update-kontrak-supplier-form").serializeArray();
        _data.push(
            {
                name : "KontrakSupplierForm[kontraksupplier_id]",
                value : kontraksupplier_id
            },
            {
                name : "list_obat",
                value : JSON.stringify(list_obat)
            }
        );

        $(this).docoForm("click", {
            url: baseController + "update?id=" + kontraksupplier_id,
            data: _data,
            success: function() {
                docoNotification('success', "Proses berhasil!", "Data telah diperbaharui.");
                location.reload();
            }
        });
    })

    function isObatValid() {
        if ($(`select[name="nama_obat"]`).val() == null) {
            docoNotification('warning', "Data Tidak Lengkap", "Obat belum terpilih");
            return false;
        }

        if ($(`select[name="nama_obat"]`).val() in list_obat) {
             docoNotification('warning', "Data Sudah Ada", "Obat sudah ada pada list");
             return false;
        }

        if ($(`select[name="uom"]`).val() == null) {
            docoNotification('warning', "Data Tidak Lengkap", "Satuan belum terpilih");
            return false;
        }

        return true;
    }

    function resetFormObat(){
        $("#nama_obat").val(null).trigger("change");
        $("#nama_obat").focus();
        $("#uom").val(null).trigger("change");
        $("#uom").prop("disabled", true);
        $("#harga_order").val(0);
        $("#pengurang").val(0);
        $("#total_harga").text(0);
        $("#pengurang_rp").text(0);
    }

    function sumTotalHarga() {
        var harga_order = docoHelper.convertToAngka($("#harga_order").val());
        var persen_pengurang = docoHelper.convertToDecimal($("#pengurang").val());
        var pengurang = harga_order * persen_pengurang / 100;
        var total = harga_order - pengurang;
        total = docoHelper.convertToRupiah(total);
        pengurang = docoHelper.convertToRupiah(pengurang);
        $("#total_harga").text(total);
        $("#pengurang_rp").text(pengurang);
    }

    $("#harga_order").on("focus", function(){
        if(this.value == 0) {
            this.value = "";
        }
    });

    $("#harga_order").on("blur", function(){
        if(this.value == "") {
            this.value = 0;
        }
    });

    $("#harga_order").on("input", function(){
        match        = (/(\d{0,9})[^,]*((?:\,\d{0,2})?)/g).exec(this.value.replace(/[^\d,]/g, ""));
        this.value   = match[1] + match[2];

        var angka = this.value;
        var number_string = angka.toString().toString().replace(/\./g, ','),
            split = number_string.split(','),
            absvalue = split[0];
        var _split = split[0].replace(/\-/g, '');
        var sisa = _split.length % 3,
            rupiah = _split.substr(0, sisa),
            ribuan = _split.substr(sisa).match(/\d{1,3}/gi);
        var simbol = absvalue.match(/\-/gm);
        simbol = simbol == null ? '' : simbol;

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        var _value = simbol + (split[1] != undefined ? rupiah + ',' + split[1] : rupiah);
        if (_value == 'NaN') {
            _value = 0;
        }

        this.value = _value;
        sumTotalHarga();
    });

    $("#pengurang").on("focus", function(){
        if(this.value == 0) {
            this.value = "";
        }
    });

    $("#pengurang").on("blur", function(){
        if(this.value == "") {
            this.value = 0;
        }
    });

    $("#pengurang").on("input", function(){
        match        = (/(\d{0,9})[^,]*((?:\,\d{0,2})?)/g).exec(this.value.replace(/[^\d,]/g, ""));
        this.value   = match[1] + match[2];

        if(this.value > 100) {
            this.value = 100;
        }

        sumTotalHarga();
    });
});