/*
    Author : Randy Vianda Putra (aweutist)
*/

$(document).ready(function () {
    // appendObat(transObat);

    var yesterday = new Date((new Date()).valueOf() - 1000 * 60 * 60 * 24);

    function alertApotek(title, message, type, element) {
        new PNotify({
            title: i18next.t(title),
            text: i18next.t(message),
            addclass: 'alert alert-' + element + ' alert-arrow-right alert-styled-right',
            type: type
        });
    }

    function ajaxLoading(element) {
        $(element).attr("disabled", true);
        $(element).html("<i class=\"fa fa-spinner fa-pulse fa-1x fa-fw\"></i>");
    }

    function ajaxAfterLoading(element, text) {
        $(element).attr('disabled', false);
        $(element).html(text)
    }

    $('.ruangan').change(function() {
        var ruangan_id = $(this).val();
    });

        $("#barang_id").select2({
            placeholder: "-- Pilih --",
            minimumInputLength: 3,
            ajax: {
                url: baseUrl + "gudang/transaksi-pemesanan/search-barang" ,
                dataType: 'json',
                quietMillis: 250,
                data: function (params) {
                    var query = {
                        search: params,
                    }
                    // Query parameters will be ?search=[term]&type=public
                    return params;
                },
                processResults: function (data) {
                    $.each(data.result, function (key, val) {
                        _detailBarang.item[val.id] = val;
                        _detailBarang.satuan[val.id] = {};
                        _detailBarang.stok[val.id] = val.stok;
                        _detailBarang.satuankecil[val.id] = val.satuankecil_id;
                        $.each(val.satuan, function (id, item) {
                            _detailBarang.satuan[val.id][id] = item;
                        });
                    });
                    return {
                        results: data.result
                    };
                },
                dropdownCssClass: 'bigdrop',
                escapeMarkup: function(m) { return m; },
            },
            cache: true
        }).on("change", function (e) {
            var value = $(this).val();
            var list_html = "";
            list_html += " <option value=\"\"></option>";
            data = [];

            if (typeof _detailBarang.satuan[value] !== "undefined") {
                data = _detailBarang.satuan[value];
            }

            if (typeof _detailBarang.stok[value] !== "undefined") {
                $("#pemesanan-obat-stok").val(_detailBarang.stok[value]);
                _detailBarang.currentStok = _detailBarang.stok[value];
            }

            var defaultValue = null;

            if (typeof _detailBarang.satuankecil[value] !== "undefined") {
                _detailBarang.currentSatuan = _detailBarang.satuankecil[value];
                defaultValue = _detailBarang.satuankecil[value];
            }

            if (typeof _detailBarang.item[value] !== "undefined") {
                attributes = _detailBarang.item[value];
            }
            $.each(data, function (i, item) {
                if (defaultValue == i) {
                    list_html += "<option data-nilai=\'"+ item.nilai_konversi +"\' value=\'" + item.satuanbesar_id + "\' selected>" + item.satuanunit_nama + "</option>";
                } else {
                    list_html += "<option data-nilai=\'"+ item.nilai_konversi +"\' value=\'" + item.satuanbesar_id + "\'>" + item.satuanunit_nama + "</option>";
                }
            });

            $("#list-satuan").html(list_html);
            var count = Object.keys(data).length;
            if (count > 0) {
                $("#list-satuan").removeAttr("disabled");
                $("#list-satuan").select2({ placeholder: "--Pilih--" });
            } else {
                $("#list-satuan").select2("enable", false);
            }

            $("#list-satuan").select2({ placeholder: "--Pilih--" });
        });

    table = $("#pemesanan-obat-alkes").docoTabel({
        filter: false,
        pageLength: 20,
        // lengthMenu: [5, 10, 25, 100],
        bLengthChange: false,
        serverSide: true,
        stateSave: true,
        processing: true,
        ajax: baseUrl + "gudang/transaksi-pemesanan/get-list-item",
        columns: [
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: "Nama barang",
                data: "nama_barang",
                orderable: false
            },
            {
                title: "Qty Pemesanan",
                data: "qty_besar",
                name: "qty_besar",
                orderable: false
            },
            {
                title: "Qty Konversi",
                data: "qty_kecil",
                name: "qty_kecil",
                orderable: false
            },
            {
                title: "Aksi",
                data: "aksi",
                searchable: false,
                orderable: false,
                class: "text-center"
            }
        ],
    });

    $("#ajax-form").submit(function (event) {
        event.preventDefault();
        var _value = $(this).serializeArray();
        var stok_tersedia = $('#pemesanan-obat-stok').val();
        var qty = $('#pemesanan-obat-qty').val();
        if (Object.keys(attributes).length) {
            $.each(attributes, function (key, val) {
                _value.push({
                    name: key,
                    value: (key == 'satuan') ? JSON.stringify(val) : val
                });
            });
        }

        $(this).docoForm("submit", {
            data: _value,
            success: function (response) {
                $("#barang_id").val('').trigger('change');
                $("#list-satuan").val('').trigger('change');
                $("#pemesanan-obat-stok, #pemesanan-obat-qty").val("");
                table.draw();
            }
        });
    });

    $(document).on('click','.delete', function(event) {
        event.preventDefault();
        $(this).docoForm('delete',{
            url: $(this).attr('data-target'),
            success : function (data) {
                table.draw();
            }
        });
    });

    $("#btn-simpan").on("click", function (event) {
        event.preventDefault();
        var dataPost = {
            tanggal_kirim: $("#tanggal-kirim").val(),
            keterangan: $("#catatan").val(),
            instalasi_id: $("#instalasi_select").val(),
            ruangan_id: $("#ruangan_select").val(),
        };

        $(this).docoForm("click", {
            url: "/gudang/transaksi-pemesanan/save",
            method: "POST",
            type: "json",
            data: dataPost,
            success: function (data) {
                var id = data.response.id;
                $('#btn-print').prop('disabled', false);
                $('#btn-print').attr('data-target', '/gudang/transaksi-pemesanan/cetak-pdf?id=' + id);
                $("#barang_id").val('').trigger('change');
                $("#list-satuan").val('').trigger('change');
                $("#pemesanan-obat-stok, #pemesanan-obat-qty").val("");
                // $("#instalasi_select").val('').trigger('change');
                // $("#ruangan_select").val('').trigger('change');
                $("#catatan").val('')
                table.draw();
                var _noTrans = data.response.nomor;
                (new PNotify({
                    title: "Berhasil",
                    text: "Data pemesanan barang berhasil disimpan dengan No.<b>"+ _noTrans  +"</b>, apakah Anda ingin melakukan cetak?",
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
                    // Print
                    window.open('/gudang/transaksi-pemesanan/cetak-pdf?id=' + id);
                }).on('pnotify.cancel', function() {

                });
            }
        });
    });

    $("#pemesanan-obat-qty").on("keyup", function () {
        var qty = $(this).val();
        qty = parseInt(docoHelper.convertToAngka(qty));
        $(this).val(qty);
    });

    $('#btn-ulang').on('click', function () {
        location.reload();
    })

    $('#btn-print').prop('disabled', true);
    $('#btn-print').on('click', function () {
        var link = $(this).attr('data-target');

        if (typeof link !== 'undefined') {
            window.location.href = link;
        } else {
            $('#btn-print').prop('disabled', true);
        }
    })
})
