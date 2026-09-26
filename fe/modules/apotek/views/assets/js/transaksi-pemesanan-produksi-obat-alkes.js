/**
 * author: Sulthan Zaidan Fauzi (sulthanzaidan1026@gmail.com)
 */

$(document).ready(function () {

    $("#instalasi_select").focus();

    $(document).on("select2:close", "#instalasi_select", function(){
        $('.ruangan').focus();
    });

    $(document).on("select2:close", ".ruangan", function(){
        $('#obatalkes_id').focus();
    });

    $(document).on("select2:close", "#obatalkes_id", function(){
        $("#list-satuan").focus();
    });

    $(document).on("select2:close", "#list-satuan", function(){
        $("#qty-pemesanan").focus();
    });

    setTimeout(function(){
        $('#instalasi_select').trigger('depdrop:change');
    }, 300);

    $('#obatalkes_id').docoPaginationSelec2(
        config = {
            placeholder : 'Pilih Obat ... ',
            _api : baseUrl + "apotek/transaksi-pemesanan-produksi/search-obat-alkes",
            ajax : {
                data: function(params) {
                    return {
                        q: params.term,
                        page: params.page || 1,
                    }
                },
                results: function (data, params) {
                    var more = (params.page * 30) < data.total_count;
                    return { results: data.items, more: more };
                },
                processResults: function(res, params) {
                    params.page = params.page || 1;
                    var arr = [];
                    $.each(res.result, function(index, value) {
                        if (index < 10) {
                            arr.push({
                                id: value.id,
                                text: value.text,
                            });

                            _detailObat.item[value.id] = value;
                            _detailObat.satuan[value.id] = {};
                            _detailObat.satuankecil[value.id] = value.satuankecil_id;
                            $.each(value.satuan, function (id, item) {
                                _detailObat.satuan[value.id][id] = item;
                            });
                        }
                    });
                    return {
                        results: arr,
                        pagination: {
                            more: res.result.length > 10
                        }
                    };
                }
            }
        }
    ).on('change', function(e) {
        var value = $(this).val();
        var list_html = "";
        list_html += " <option value=\"\"></option>";
        data = [];
        if (typeof _detailObat.satuan[value] !== "undefined") {
            data = _detailObat.satuan[value];
        }

        var defaultValue = null;

        if (typeof _detailObat.satuankecil[value] !== "undefined") {
            defaultValue = _detailObat.satuankecil[value];
        }

        if (typeof _detailObat.item[value] !== "undefined") {
            attributes = _detailObat.item[value];
        }

        $.each(data, function (i, item) {
            if (defaultValue == item.satuanbesar_id) {
                list_html += "<option value=\'" + item.satuanbesar_id + "\' selected>" + item.satuan_besar + "</option>";
            } else {
                list_html += "<option value=\'" + item.satuanbesar_id + "\'>" + item.satuan_besar + "</option>";
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

    $("#qty-pemesanan").on("keyup change scroll", function () {
        var qty = parseInt($(this).val());

        if (qty < 0) {
            qty = 0;
        }
        $(this).val(qty);
    });

    $("#ajax-form").submit(function (event) {
        event.preventDefault();
        var _value = $(this).serializeArray();

        if (Object.keys(attributes).length) {
            $.each(attributes, function (key, val) {
                _value.push({
                    name: key,
                    value: val
                });
            });
        }

        _value.push({
            name: 'satuan_pesan_id',
            value: $('#list-satuan').val()
        }, {
            name: 'satuan_pesan_nama',
            value: $('#list-satuan option:selected').text()
        });

        $(this).docoForm("submit", {
            data: _value,
            skipConfirm: true,
            success: function (data) {
                $("#obatalkes_id").val('').trigger('change');
                $("#list-satuan").val('').trigger('change');
                $("#qty-pemesanan").val("");
                $("#obatalkes_id").focus();
                table.draw();
            }
        });
    });
    
    table = $("#pemesanan-produksi-obat-alkes").docoTabel({
        filter: false,
        paging: false,
        bLengthChange: false,
        serverSide: true,
        stateSave: true,
        processing: true,
        ajax: baseUrl + "apotek/transaksi-pemesanan-produksi/get-list-item",
        columns: [
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: "Nama obat alkes",
                data: "nama_obat",
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

    $(document).on('click','.delete', function(event) {
        event.preventDefault();
        $(this).docoForm('delete',{
            success : function (data) {
                table.draw();
            }
        });
    });

    $('#catatan').on('input', function() {
        const value = $(this).val();    
        $(this).val(value.replace(/[^a-zA-Z0-9\s+-,.():;]/g, ''));
    });

    $("#btn-simpan").on("click", function (event) {
        event.preventDefault();
        var dataPost = {
            pemesanan_id: $("#pemesanan_id").val(),
            instalasi_tujuan: $("#instalasi_select").val(),
            ruangan_tujuan: $("#ruangan_select").val(),
            catatan: $("#catatan").val()
        };

        $(this).docoForm("click", {
            url: "/apotek/transaksi-pemesanan-produksi/save-request",
            method: "POST",
            type: "json",
            data: dataPost,
            success: function (data) {
                if ($("#pemesanan_id").val() == '') {
                    $("#catatan").val('')
                    table.draw();
                }
                (new PNotify({
                    title: "Berhasil",
                    text: "Pemesanan Obat Alkes dengan Nomor " + "<strong>" + data.response.nomor + "</strong>" + " berhasil disimpan.",
                    addclass: "alert alert-success alert-arrow-right alert-styled-right",
                    type: "success",
                    history: {
                        history: false
                    }
                }))

                setTimeout(() => {
                    window.location.href = '/apotek/inf-produksi-obat';
                }, 2000);
            }
        });
    });

    $('#btn-ulang').on('click', function() {
        window.location.href = window.location.href;
    })
})

$(document).on('keydown', null, function(e){
    if(e.altKey && e.key == 's'){
        $('textarea').blur();
        $('input').each(function(){
            $(this).blur();
        });
        $('#btn-simpan').click();
    }
});
