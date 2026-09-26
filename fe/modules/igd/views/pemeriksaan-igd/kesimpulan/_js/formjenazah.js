/*
* @Author: Rizqi Fitrianto
* @Date:   2019-01-23 14:15:33
* @Last Modified by:   Rizqi Fitrianto
* @Last Modified time: 2019-02-08 10:47:50
*/
var dataobat = {};
var datatindakan = {};
$(document).ready(function(){
    /* Linen */
    $('#linen-select').select2({
        placeholder: '',
        minimumInputLength: 3,
        ajax: {
            url: "/igd/pemeriksaan-igd/get-linen",
            dataType: 'json',
            quietMillis: 250,
            data: function (term, page) {
                return {
                    q: term,
                };
            },
            processResults: function (data) {
              return {
                results: data.result
              };
            }
        },
        dropdownCssClass: "bigdrop",
        escapeMarkup: function (m) { return m; },
    });
    $('#linen-select').on('change', function(){
        var txt = $('#linen-select :selected').text();
        if(txt != ''){
            $('.barang-nama').val(txt);
        }
    })
    $('#add-linen').on('click', function(e){
        e.preventDefault();
        var data = $('#row-linen :input').serializeArray();
        var state = true;
        $.each(data, function(k,v){
            if( v.name == 'barang_nama' && v.value == ''){
                docoNotification('error', 'Terjadi Kesalahan', 'Linen Tidak Boleh Kosong');
                state = false;
                return false;
            }
            if( v.name == 'qty' && (v.value == '' || v.value == '0') ){
                docoNotification('error', 'Terjadi Kesalahan', 'Qty Tidak Boleh Kosong');
                state = false;
                return false;
            }
        })
        if(state == false){
            return false;
        }
        $(this).docoForm("click", {
            url: '/igd/pemeriksaan-igd/save-cache?id='+pendaftaranId+'&type=linen',
            data: data,
            skipConfirm: true,
            success : function(data) {
                $('#linen-select').val('').trigger('change');
                $('.barang-nama').val('');
                $('.barang-qty').val('');
                table_linen.draw();
            }
        });
    });
    /* end Linen */
    /* alat */
    $('#alat-select').select2({
        placeholder: '',
        minimumInputLength: 3,
        ajax: {
            url: "/igd/pemeriksaan-igd/get-alat",
            dataType: 'json',
            quietMillis: 250,
            data: function (term, page) {
                return {
                    q: term,
                };
            },
            processResults: function (data) {
              return {
                results: data.result
              };
            }
        },
        dropdownCssClass: "bigdrop",
        escapeMarkup: function (m) { return m; },
    });
    $('#add-alat').on('click', function(e){
        e.preventDefault();
        var data = $('#row-alat :input').serializeArray();
        var state = true;
        $.each(data, function(k,v){
            if( v.name == 'obatalkes_nama' && v.value == ''){
                docoNotification('error', 'Terjadi Kesalahan', 'Alat Tidak Boleh Kosong');
                state = false;
                return false;
            }
            if( v.name == 'qty' && (v.value == '' || v.value == '0') ){
                docoNotification('error', 'Terjadi Kesalahan', 'Qty Tidak Boleh Kosong');
                state = false;
                return false;
            }
        })
        if(state == false){
            return false;
        }
        $(this).docoForm("click", {
            url: '/igd/pemeriksaan-igd/save-cache?id='+pendaftaranId+'&type=alat',
            data: data,
            skipConfirm: true,
            success : function(data) {
                $('#alat-select').val('').trigger('change');
                $('.alat-nama').val('');
                $('.alat-qty').val('');
                table_alat.draw();
            }
        });
    });
    $('#alat-select').on('change', function(){
        var txt = $('#alat-select :selected').text();
        if(txt != ''){
            $('.alat-nama').val(txt);
        }
    });
    /* end alat */
    /* obat alkes */
    $('#obatalkes-select').select2({
        placeholder: '',
        minimumInputLength: 3,
        ajax: {
            url: "/igd/pemeriksaan-igd/get-obat-alkes",
            dataType: 'json',
            quietMillis: 250,
            data: function (term, page) {
                return {
                    q: term,
                };
            },
            processResults: function (data) {
                dataobat = data.obatalkes_data;
                return {
                    results: data.result
                };
            }
        },
        dropdownCssClass: "bigdrop",
        escapeMarkup: function (m) { return m; },
    });
    $('#obatalkes-select').on('change', function(){
        let data = dataobat[$(this).val()];
        if(typeof data != 'undefined'){
            $('.satuankecil-nama').val(data.satuankecil_nama);
            $('.obatalkes-nama').val(data.obatalkes_nama);
        }
    });
    $('#add-obatalkes').on('click', function(e){
        e.preventDefault();
        var data = $('#row-obat :input').serializeArray();
        var state = true;
        var obatavailable = false;
        $.each(data, function(k,v){
            if(v.name == 'obatalkes_id'){
                obatavailable = true;
            }
            if( v.name == 'qty' && (v.value == '' || v.value == '0') ){
                docoNotification('error', 'Terjadi Kesalahan', 'Qty Tidak Boleh Kosong');
                state = false;
                return false;
            }
        })
        if(state == false){
            return false;
        }
        if( obatavailable == false){
            docoNotification('error', 'Terjadi Kesalahan', 'Obat Tidak Boleh Kosong');
            state = false;
            return false;
        }
        var valueobat = dataobat[data[0].value];
        var qty = data[1].value;
        if(qty > valueobat.qty_tersedia){
            docoNotification('error', 'Terjadi Kesalahan', 'Qty Tidak Boleh Melebihi Stok Tersedia');
            return false;
        }
        var result = [];
        var exception = ['obatalkes_id', 'satuankecil_nama'];
        var harga = 0;
        $.each(valueobat, function(k,v){
            if(jQuery.inArray(k, exception) < 0){
                result.push({name: k, value: v});
            }
            if(k == 'hargajual'){
                harga = qty * v;
            }
        })
        $.merge(data, result);
        data.push({name: 'harga', value: harga});
        $(this).docoForm("click", {
            url: '/igd/pemeriksaan-igd/save-cache?id='+pendaftaranId+'&type=obat',
            data: data,
            skipConfirm: true,
            success : function(data) {
                $('#obatalkes-select').val('').trigger('change');
                $('.satuankecil-nama').val('');
                $('.obatalkes-nama').val('');
                $('.qty-obat').val('');
                table_obat_kesimpulan.draw();
            }
        });
    });
    /* end obat alkes */
    /* tindakan */
    $('#tindakan-select').select2({
        placeholder: '',
        minimumInputLength: 3,
        ajax: {
            url: "/igd/pemeriksaan-igd/get-tarif-rs",
            dataType: 'json',
            quietMillis: 250,
            data: function (term, page) {
                return {
                    q: term,
                };
            },
            processResults: function (data) {
                datatindakan = data.tindakandata;
                return {
                    results: data.result
                };
            }
        },
        dropdownCssClass: "bigdrop",
        escapeMarkup: function (m) { return m; },
    });
    $("#add-tindakan").on("click", function(e){
        e.preventDefault();
        var data = $('#row-tindakan :input').serializeArray();
        var tindakanavailable = false;
        var qtynull = false;
        $.each(data, function(k,v){
            if(v.name == 'daftartindakan_id'){
                tindakanavailable = true;
            }
            if( v.name == 'qty' && (v.value == '' || v.value == '0') ){
                qtynull = true;
            }
        });
        if(tindakanavailable == false){
            docoNotification('error', 'Terjadi Kesalahan', 'Tindakan Tidak Boleh Kosong');
            return false;
        }
        if(qtynull == true){
            docoNotification('error', 'Terjadi Kesalahan', 'Qty Tidak Boleh Kosong');
            return false;
        }
        var qty = data[1].value;
        var tindakanvalue = datatindakan[data[0].value];
        var exception = ['daftartindakan_id'];
        var result = [];
        $.each(tindakanvalue, function(k,v){
            if(jQuery.inArray(k, exception) < 0){
                if(k == "additional_data"){
                    v = JSON.stringify(v);
                }
                result.push({name: k, value: v});
            }
            if(k == 'harga_tariftindakan'){
                harga = qty * v;
            }
        })
        $.merge(data, result);
        data.push({name: 'harga', value: harga});
        data.push({name: 'qty_tindakan', value: qty});
        $(this).docoForm("click", {
            url: '/igd/pemeriksaan-igd/save-cache?id='+pendaftaranId+'&type=tindakan',
            data: data,
            skipConfirm: true,
            success : function(data) {
                $('#tindakan-select').val('').trigger('change');
                $('.tindakan-qty ').val('');
                table_tindakan.draw();
            }
        });
    })
    /* end tindakan */
    $(document).on('click', '.delete-data', function(e){
        e.preventDefault();
        var _url = $(this).attr('action');
        var _type = $(this).attr('data-type');
        $(this).docoForm("click", {
            url: _url,
            skipConfirm: true,
            success : function(data) {
                if(_type == 'obat'){
                    table_obat_kesimpulan.draw();
                }else if(_type == 'alat'){
                    table_alat.draw();
                }else if(_type == 'linen'){
                    table_linen.draw();
                }else{
                    table_tindakan.draw();
                }
                var counters = $("div[id*='confirm-dialog']").length;
                if(counters > 0){
                    $.each($("div[id*='confirm-dialog']"), function(k,v){
                        $("#confirm-dialog").remove();
                    })
                }
            }
        });
    })

});

$('#checked_kesimpulan_jenazah').on('click', function(){
    var checked = $('#checked_kesimpulan_jenazah').is(':checked');
    if(checked){
        $.ajax({
            url: "/igd/pemeriksaan-igd/reset-cache?id="+pendaftaranId+"&type=all",
            success: function(data){
            }
        });
        table_linen = $("#tabel-linen").docoTabel({
            destroy: true,
            scrollX: false,
            filter: false,
            sorting: [[1, "asc"]], 
            processing: true,
            serverSide: true,
            paging: false,
            ajax: baseUrl+"igd/pemeriksaan-igd/get-cache?cachetype=linen&id="+pendaftaranId,
            columns: [
                {title: "No", data: "rownum"},
                {title: "Nama Linen", data: "barang_nama"},
                {title: "Qty", data: "qty"},
                {
                    title: "Aksi",
                    data: "aksi",
                    searchable: false,
                    orderable: false,
                },
            ],
        });
        table_alat = $("#tabel-alat").docoTabel({
            destroy: true,
            scrollX: false,
            filter: false,
            sorting: [[1, "asc"]], 
            processing: true,
            serverSide: true,
            paging: false,
            ajax: baseUrl+"igd/pemeriksaan-igd/get-cache?cachetype=alat&id="+pendaftaranId,
            columns: [
                {title: "No", data: "rownum"},
                {title: "Nama Alat", data: "obatalkes_nama"},
                {title: "Qty", data: "qty"},
                {
                    title: "Aksi",
                    data: "aksi",
                    searchable: false,
                    orderable: false,
                },
            ],
        });
        table_obat_kesimpulan = $("#tabel-obat").docoTabel({
            destroy: true,
            scrollX: false,
            filter: false,
            sorting: [[1, "asc"]], 
            processing: true,
            serverSide: true,
            paging: false,
            ajax: baseUrl+"igd/pemeriksaan-igd/get-cache?cachetype=obat&id="+pendaftaranId,
            columns: [
                {title: "No", data: "rownum"},
                {title: "Nama Obat Alkes", data: "obatalkes_nama"},
                {title: "Qty", data: "qty"},
                {title: "Satuan", data: "satuankecil_nama"},
                {title: "Harga (Rp.)", data: "harga"},
                {
                    title: "Aksi",
                    data: "aksi",
                    searchable: false,
                    orderable: false,
                },
            ],
        });
        table_tindakan = $("#tabel-tindakan").docoTabel({
            destroy: true,
            scrollX: false,
            filter: false,
            sorting: [[1, "asc"]], 
            processing: true,
            serverSide: true,
            paging: false,
            ajax: baseUrl+"igd/pemeriksaan-igd/get-cache?cachetype=tindakan&id="+pendaftaranId,
            columns: [
                {title: "No", data: "rownum"},
                {title: "Nama Tindakan", data: "daftartindakan_nama"},
                {title: "Qty", data: "qty"},
                {title: "Harga (Rp.)", data: "harga"},
                {
                    title: "Aksi",
                    data: "aksi",
                    searchable: false,
                    orderable: false,
                },
            ],
        });
    }else{
        $("#kondisipasien").find(":input").val('');
        $("#kondisipasien").find(".select2").val('').trigger('change');
    }
})