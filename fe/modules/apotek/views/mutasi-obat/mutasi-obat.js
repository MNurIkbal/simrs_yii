var current_limit;

function select2Obat(){
    ruangan_id = $('.ruangan').val();
    $("#obatalkes_id").select2({
        language: {
            errorLoading: function () { return "Searching..." }
        },
        placeholder: "-- Pilih --",
        minimumInputLength: 3,
        ajax: {
            url: baseUrl + "apotek/transaksi-pemesanan/search-obat-alkes?ruangan_id=" + current_ruangan_id,
            dataType: 'json',
            quietMillis: 250,
            data: function (params) {
                var query = {
                    search: params,
                }
                return params;
            },
            processResults: function (data) {
                $.each(data.result, function (key, val) {
                    _detailObat.item[val.id] = val;
                    _detailObat.satuan[val.id] = {};
                    _detailObat.stok[val.id] = val.stok;
                    _detailObat.satuankecil[val.id] = val.satuankecil_id;
                    $.each(val.satuan, function (id, item) {
                        _detailObat.satuan[val.id][id] = item;
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
        if (typeof _detailObat.satuan[value] !== "undefined") {
            data = _detailObat.satuan[value];
        }

        if (typeof _detailObat.stok[value] !== "undefined") {
            $("#pemesanan-obat-stok").val(_detailObat.stok[value]);
            _detailObat.currentStok = _detailObat.stok[value];
            current_limit = _detailObat.stok[value];
        }

        var defaultValue = null;

        if (typeof _detailObat.satuankecil[value] !== "undefined") {
            _detailObat.currentSatuan = _detailObat.satuankecil[value];
            defaultValue = _detailObat.satuankecil[value];
        }

        if (typeof _detailObat.item[value] !== "undefined") {
            attributes = _detailObat.item[value];
        }

        $.each(data, function (i, item) {
            if (defaultValue == item.satuanbesar_id) {
                list_html += "<option data-nilai=\'"+ item.nilai_konversi +"\' value=\'" + item.satuanbesar_id + "\' selected>" + item.satuan_besar + "</option>";
            } else {
                list_html += "<option data-nilai=\'"+ item.nilai_konversi +"\' value=\'" + item.satuanbesar_id + "\'>" + item.satuan_besar + "</option>";
            }
        });

        $("#list-satuan").html(list_html);
        var count = Object.keys(data).length;
        if (count > 0) {
            $("#list-satuan").removeAttr("disabled");
            $("#list-satuan").select2({ placeholder: "--Pilih--" }).trigger('change');
        } else {
            $("#list-satuan").select2("enable", false);
        }

        $("#list-satuan").select2({ placeholder: "--Pilih--" });
    });
}

$(document).on('depdrop:afterChange', '.ruangan', function(){
   ruangan_id = $('.ruangan').val();
   getObatSelect2();
});

$(document).on('change', '.ruangan', function(){
    ruangan_id = $('.ruangan').val();
    getObatSelect2();
});


function getObatSelect2() {
    $('#obatalkes_id').docoPaginationSelec2(
      config = {
          placeholder : 'Pilih Obat ... ',
          _api : baseUrl + "apotek/transaksi-pemesanan/search-obat-alkes",
          ajax : {
              data: function(params) {
                  return {
                      q: params.term,
                      page: params.page || 1,
                      ruangan_id: current_ruangan_id
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
                          var _disabled = parseFloat(value.stok) <= 0 ? true : false;
                          arr.push({
                              id: value.id,
                              text: value.text,
                              disabled: _disabled
                          });

                          _detailObat.item[value.id] = value;
                          _detailObat.satuan[value.id] = {};
                          _detailObat.stok[value.id] = value.stok;
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

        if (typeof _detailObat.stok[value] !== "undefined") {
            $("#pemesanan-obat-stok").val(_detailObat.stok[value]);
            _detailObat.currentStok = _detailObat.stok[value];
        }

        var defaultValue = null;

        if (typeof _detailObat.satuankecil[value] !== "undefined") {
            _detailObat.currentSatuan = _detailObat.satuankecil[value];
            defaultValue = _detailObat.satuankecil[value];
        }

        if (typeof _detailObat.item[value] !== "undefined") {
            attributes = _detailObat.item[value];
        }

        $.each(data, function (i, item) {
            if (defaultValue == item.satuanbesar_id) {
                list_html += "<option data-nilai=\'"+ item.nilai_konversi +"\' value=\'" + item.satuanbesar_id + "\' selected>" + item.satuan_besar + "</option>";
            } else {
                list_html += "<option data-nilai=\'"+ item.nilai_konversi +"\' value=\'" + item.satuanbesar_id + "\'>" + item.satuan_besar + "</option>";
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
}

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
        $("#pemesanan-obat-qty").focus();
    });

    // setTimeout(function(){
    //     $('#instalasi_select').trigger('depdrop:change');
    // }, 300);

    // setTimeout(function(){
    //     $('.ruangan').trigger('change');
    // }, 2000);

    var yesterday = new Date((new Date()).valueOf() - 1000 * 60 * 60 * 24);
    $('.pickadate').pickadate({
        format: 'dd mmmm yyyy',
        disable: [{
            from: [0, 0, 0],
            to: yesterday
        }],
        onStart: function () {
            var date = new Date();
            this.set('select', [date.getFullYear(), date.getMonth(), date.getDate()]);
        }
    });

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

    function deleteAllCache(){
        $.ajax({
            url: baseUrl + "apotek/transaksi-pemesanan/delete-all-cache",
            type:"POST",
            success: function(result){
                table.draw();
            }
        });
    }

    $('#instalasi_select').change(function(){
        deleteAllCache();
    })

    $('.ruangan').change(function() {
        deleteAllCache();
        var ruangan_id = $(this).val();
        getObatSelect2();
    });

    $('#list-satuan').on('change', function(event){
        event.preventDefault();
        var oaId = $("#obatalkes_id").val();
        var _value = $(this).val();
        var _current = _detailObat.currentSatuan;
        var _stok = _detailObat.currentStok;
        var hasil = _stok;
        var _konv = $(this).find("option:selected").attr("data-nilai");
        hasil = _konv != 0 ? Math.floor(_stok/_konv) : 0;
        var _fixed = hasil%1 == 0 ? 0 : 2;
        $("#pemesanan-obat-stok").val(hasil.toFixed(_fixed));
        current_limit = hasil;
        $(".satuan-text").val( $(this).find("option:selected").text() )
        $("#pemesanan-obat-qty").val(0);
    })
    table = $("#pemesanan-obat-alkes").docoTabel({
        filter: false,
        paging: false,
        // lengthMenu: [5, 10, 25, 100],
        bLengthChange: false,
        serverSide: true,
        stateSave: true,
        processing: true,
        ajax: baseUrl + "apotek/transaksi-pemesanan/get-list-item",
        columns: [
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: "Kode obat alkes",
                data: "kode_obat",
                orderable: false
            },
            {
                title: "Nama obat alkes",
                data: "nama_obat",
                orderable: false
            },
            {
                title: "Qty Mutasi",
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
                    value: val
                });
            });
        }
        var selected_satuan = $('#list-satuan').val();
        var selected_satuan_text = $('#list-satuan option:selected').text();
        _value.push(
            {
                name: 'satuan_pesan_id',
                value: selected_satuan
            },
            {
                name: 'satuan_pesan_nama',
                value: selected_satuan_text
            },
            {
                name: 'stok_asli',
                value: _detailObat.stok[$("#obatalkes_id").val()]
            },
            {
                name: 'kode_obat',
                value: $('#obatalkes_id').val() != undefined ? _detailObat.item[$("#obatalkes_id").val()].kode : null
            }
        );
        $(this).docoForm("submit", {
            data: _value,
            skipConfirm: true,
            success: function (data) {
                $("#obatalkes_id").val('').trigger('change');
                $("#list-satuan").val('').trigger('change');
                $("#pemesanan-obat-stok, #pemesanan-obat-qty").val("");
                $("#obatalkes_id").focus();
                table.draw();
            }
        });
    });

    $(document).on('click','.delete', function(event) {
        event.preventDefault();
        $(this).docoForm('delete',{
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
            ruangantujuan_id: $('#ruangan_select').val()
        };

        $(this).docoForm("click", {
            url: "/apotek/mutasi-obat/save",
            method: "POST",
            type: "json",
            data: dataPost,
            success: function (data) {
                var id = data.response.id;
                // $("#instalasi_select").val('').trigger('change');
                // $("#ruangan_select").val('').trigger('change');
                $("#catatan").val('')
                table.draw();
                (new PNotify({
                    title: "Berhasil",
                    text: "Mutasi Obat Alkes dengan Nomor " + "<strong>" + data.response.nomor + "</strong>" + " berhasil disimpan, apakah Anda ingin melakukan cetak?",
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
                    window.open('/apotek/informasi-mutasi/print?id='+id);
                }).on('pnotify.cancel', function() {

                });

                setTimeout(function(){
                    window.location.href = "/apotek/informasi-mutasi/obat-alkes-keluar";
                }, 3000);
            }
        });
    });

    $("#pemesanan-obat-qty").on("keyup change scroll", function () {
        var qty = parseInt($(this).val());

        if (qty > parseInt(current_limit)) {
            qty = parseInt(current_limit);
        }

        if (qty < 0) {
            qty = 0;
        }
        $(this).val(qty);
    });

    $('#btn-ulang').on('click', function() {
        window.location.href = window.location.href + '?state=MA';
    })

    $('#btn-print').prop('disabled', true);

    $('#btn-print').on('click', function () {
        var link = $(this).attr('data-target');

        if (typeof link !== 'undefined') {
            window.open(link, '_blank');
        } else {
            $('#btn-print').prop('disabled', true);
        }
    });
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