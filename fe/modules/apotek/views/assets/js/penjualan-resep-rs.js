/* 
    Author : Randy Vianda Putra (aweutist)
*/


var _group = {};

$(document).ready(function () {

    $(window).bind('beforeunload', function(){
      return 'Are you sure you want to leave?';
    });
    
    appendObat(transObat);
    $('.disabled').prop('disabled', true);
    $(".r_ke").prop("disabled", true);

    $(".racikan_id").change(function () {
        var racikan_id = $(this).is(":checked");
        if (racikan_id) {
            $(".r_ke").prop("disabled", false);
            $(".required_racikan").show();
        } else {
            $(".r_ke").prop("disabled", true);
            $('.r_ke').val('');
            $(".required_racikan").hide();
        }
    });

    // mencegah karakter lain selain angka desimal
    $(document).on('input', '.qty', function() {
        match        = (/(\d{0,9})[^.]*((?:\.\d{0,2})?)/g).exec(this.value.replace(/[^\d.]/g, ''));
        this.value   = match[1] + match[2];
        var _qty = $(this).val();
        var nilai_konversi = $("#nilai_konversi").val();
        var konversi = _qty * nilai_konversi;
        konversi = konversi.toFixed(2);
        $(".qty_konversi").val(konversi);
    });

    $('#id_auto_obat').change(function(){
        $(".qty_konversi").val("");
        $(".qty").val("");
        $("#nilai_konversi").val("");
    });

    $('#ampuls_id').on('change', function(){

        var _stoktable = 0;
        var _id        = $('#id_auto_obat').val();
        $.each(transObat, function (x, y) {
            if (typeof transObat[x] !== "undefined") {
                if(y.obatalkes_id == _id && y.resepturdetail_id == null){
                    _stoktable = parseFloat(_stoktable) + parseFloat(y.qty_konversi);
                }
            }
        });

        $(".qty_konversi").val("");
        $(".qty").val("");

        var _val     = $(this).val();
        var konversi = _group[_val];
        if (konversi != null){
            konversi = konversi.toFixed(2);
        }
        $("#nilai_konversi").val(konversi);

        var _stok          = $(".stok").val() -_stoktable;
        var _konversi_stok = parseFloat(_stok)/parseFloat(konversi) ;
        _konversi_stok     = (isNaN(_konversi_stok)) ? 0 : _konversi_stok;
        // _konversi_stok     = docoHelper.numberFormat(_konversi_stok, 2, ",", ".");
        if (_konversi_stok != null){
            _konversi_stok = _konversi_stok.toFixed(2);
        }
        $(".qty_tersedia").html(_konversi_stok);

        var _hargajual      = $(".hargajual").val();
        var _konversi_harga = _hargajual*konversi;
        // _konversi_harga     = docoHelper.numberFormat(_konversi_harga, 2, ",", ".");
        if (_konversi_harga != null){
        _konversi_harga = _konversi_harga.toFixed(2);
        }
        $(".harga_konversi").val(_konversi_harga);

        var _val_nama  = $(this).find('option:selected').text();
        var _val_value = $(this).val();
        if ( _val_nama == '--Pilih--'){
            _val_nama = '';
            $(".harga_konversi").val('');
        }
        $(".stok_text").html(_val_nama);
        $(".harga_text").html(_val_nama);

        $("#satuaninput_id").val(_val_value);
        $("#satuan_input").val(_val_nama);
    });

    // $(".qty").on("keyup", function () {
    //     var qty = $(this).val();
    //     qty = parseInt(docoHelper.convertToAngka(qty));
    //     if (isNaN(qty)) {
    //         qty = 0;
    //     }
    //     $(this).val(qty);
    // });

    let autoObat = $(".autoObat");
    let id_obat = $(".id_obat");
    let stok = $(".stok");
    let harga = $(".harga");
    let ppn = $(".ppn");
    let obat_nama = $(".obat_nama");
    let tgl_kadaluarsa = $(".tgl_kadaluarsa");
    let satuankecil_id = $(".satuankecil_id");
    let apotek = { list_stok: {} };
    // change here
    let harganetto = $(".harganetto");
    let hn_margin = $(".hn_margin");
    let hn_diskon = $(".hn_diskon");
    let hn_ppn = $(".hn_ppn");
    let hargajual = $(".hargajual");
    let persenppn = $(".persenppn");
    let persenmargin = $(".persenmargin");
    let persendiscount = $(".persendiscount");
    let posisi = $(".posisi");
    // end here

    $("#id_auto_obat").select2({
        width: '100%',
        language: 'id',
        //data: existingData, // Jika ada existing data
        ajax: {
            url: '/apotek/transaksi-resep/get-data-ajax',
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page || 1
                }
            },
            delay: 1000,
            processResults: function (res, params) {
                params.page = params.page || 1;
                var arr = []
                let objectAssigned = {}
                res.data_stok.map((itemObat, index) => {
                    if (index < 10) {
                        objectAssigned = {
                            id: itemObat.obatalkes_id,
                            text: itemObat.obatalkes_nama
                        }
                        apotek.list_stok[itemObat.obatalkes_id] = itemObat;
                        if (itemObat.qty_tersedia <= 0) {
                            objectAssigned.disabled = true
                        }
                        arr.push(objectAssigned)
                    }
                })
                console.log(arr)
                autoObat.change(function (e) {
                    var id = $(this).val();
                    var selected = apotek.list_stok[id];
                    if (typeof selected !== "undefined") {
                        id_obat.val(selected.obatalkes_id);
                        stok.val(selected.qty_tersedia);
                        $(".qty_tersedia").html(!isNaN(selected.qty_tersedia) ? selected.qty_tersedia : 0);
                        harga.val(selected.harganetto);
                        ppn.val(selected.ppn);
                        obat_nama.val(selected.obatalkes_nama);
                        satuankecil_id.val(selected.satuankecil_id);
                        //change me
                        harganetto.val(selected.harganetto);
                        hn_margin.val(selected.hn_margin);
                        hn_diskon.val(selected.hn_diskon);
                        hn_ppn.val(selected.hn_ppn);
                        persendiscount.val(selected.disc);
                        persenmargin.val(selected.margin);
                        persenppn.val(selected.ppn);
                        hargajual.val(selected.hargajual);
                        var urutan = parseInt(urutObatRs);
                        urutan = urutan + 1;
                        posisi.val(urutan);
                        // end here
                    }
                });
                console.log(res, res.data_stok.length)
                return {
                    results: arr,
                    pagination: {
                        more: res.data_stok.length > 10
                    }
                };
            }
        }
    })

    // $('#id_auto_obat').select2({
    //     placeholder: '— Pilih —',
    //     //minimumInputLength: 3,
    //     ajax: {
    //         url: '/apotek/transaksi-resep/get-data-ajax',
    //         dataType: 'json',
    //         quietMillis: 250,
    //         data: function(term, page){
    //             return{
    //                 q: term,
    //                 page: page
    //             }
    //         },
    //         processResults: function (res) {
    //             var arr = []
    //             $.each(res.data_stok, function (index, value) {
    //                 arr.push({
    //                     id: value.obatalkes_id,
    //                     text: value.obatalkes_nama
    //                 })

    //                 let data = [];
    //                 let response = res.data_stok;
    //                 for (var i in response) {
    //                     data.push({ id: response[i].obatalkes_id, text: response[i].obatalkes_nama });
    //                     apotek.list_stok[response[i].obatalkes_id] = response[i];
    //                 }

    //                 autoObat.change(function (e) {
    //                     var id = $(this).val();
    //                     var selected = apotek.list_stok[id];
    //                     if (typeof selected !== "undefined") {
    //                         id_obat.val(selected.obatalkes_id);
    //                         stok.val(selected.qty_tersedia);
    //                         $(".qty_tersedia").html(!isNaN(selected.qty_tersedia) ? selected.qty_tersedia : 0);
    //                         harga.val(selected.harganetto);
    //                         ppn.val(selected.ppn);
    //                         obat_nama.val(selected.obatalkes_nama);
    //                         satuankecil_id.val(selected.satuankecil_id);
    //                         //change me
    //                         harganetto.val(selected.harganetto);
    //                         hn_margin.val(selected.hn_margin);
    //                         hn_diskon.val(selected.hn_diskon);
    //                         hn_ppn.val(selected.hn_ppn);
    //                         persendiscount.val(selected.disc);
    //                         persenmargin.val(selected.margin);
    //                         persenppn.val(selected.ppn);
    //                         hargajual.val(selected.hargajual);
    //                         var urutan = parseInt(urutObatRs);
    //                         urutan = urutan + 1;
    //                         posisi.val(urutan);
    //                         // end here
    //                     }
    //                 });
    //             })
    //             return {
    //                 results: arr
    //             };
    //         },
    //         // cache: true,
    //     },
    //     dropdownCssClass: 'bigdrop',
    //     escapeMarkup: function (m) { return m; },
    // })

    /*$.ajax({
        url: "/apotek/transaksi-resep/get-data-ajax",
        type: "json",
        success: function (res) {
            let data = [];
            let response = res.data_stok;
            for (var i in response) {
                data.push({ id: response[i].obatalkes_id, text: response[i].obatalkes_nama });
                apotek.list_stok[response[i].obatalkes_id] = response[i];
            }
            autoObat.select2({
                data: data,
                type: "GET",
                quietMillis: 50,
                minimumInputLength: 2,
            })

            autoObat.change(function (e) {
                var id = $(this).val();
                var selected = apotek.list_stok[id];
                if (typeof selected !== "undefined") {
                    id_obat.val(selected.obatalkes_id);
                    stok.val(selected.qty_tersedia);
                    harga.val(selected.harganetto);
                    ppn.val(selected.ppn);
                    obat_nama.val(selected.obatalkes_nama);
                    satuankecil_id.val(selected.satuankecil_id);
                    //change me
                    harganetto.val(selected.harganetto);
                    hn_margin.val(selected.hn_margin);
                    hn_diskon.val(selected.hn_diskon);
                    hn_ppn.val(selected.hn_ppn);
                    persendiscount.val(selected.disc)
                    persenmargin.val(selected.margin)
                    persenppn.val(selected.ppn)
                    hargajual.val(selected.hargajual)
                    // end here
                }
            });

            var _id_obat = id_obat.val();
            $(".autoObat").val(_id_obat).trigger("change");
        }
    })*/

    $(document).on('click', '#tambah-obat', function(event) {
        event.preventDefault();
        /* Act on the event */
        var data            = $("#form-obat").serialize();
        var form            = $("#form-obat");
        var submit_btn      = form.find(".add");
        var _r_ke           = form.find('.r_ke').val();
        var _racikan_id     = form.find(".racikan_id").is(":checked");
        var _id_obat        = form.find('.id_obat').val();
        var _cekObatR       = `${_r_ke}-${_id_obat}-${_racikan_id}`;
        var _stok           = form.find('.stok').val();
        var _signa          = form.find('.signa').val();
        
        var _qty            = form.find('.qty').val();
        var _qty_konversi   = form.find('.qty_konversi').val();
        var _nilai_konversi = form.find('.nilai_konversi').val();
        var id_barang_list  = form.find('.list_barang').get();

        var _stoksisa       = form.find('.qty_tersedia').html();
        
        if (parseFloat(_qty) > parseFloat(_stoksisa)) {
                docoNotification("warning", i18next.t("Perhatian"), i18next.t("Stok tidak mencukupi"));
                ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambah'));
                return false;
        }

        if (_racikan_id) {
            if (_r_ke == '') {
                docoNotification("warning", i18next.t("Perhatian"), i18next.t("R ke tidak boleh kosong"));
                ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambah'));
                return false;
            }
        }
        
        if (_id_obat == '') {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Obat tidak boleh kosong"));
            ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambah'));
            return false;
        }
        
        if (_qty == '') {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Jumlah tidak boleh kosong"));
            ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambah'));
            return false;
        }

        if (_qty_konversi == '') {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Jumlah tidak boleh kosong"));
            ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambah'));
            return false;
        }

        if (_nilai_konversi == '') {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Konversi Belum Dipilih"));
            ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambah'));
            return false;
        }

        // if (parseInt(_qty) == 0) {
        //     docoNotification("warning", i18next.t("Perhatian"), i18next.t("Jumlah tidak boleh kurang dari 1"));
        //     ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambah'));
        //     return false;
        // }

        invalid = false;
        for (var i = 0; i < id_barang_list.length; i++) {
            var cek_obat = $(id_barang_list[i]).data('id');

            if (_cekObatR == cek_obat) {
                invalid = true;
            }
        }
        if(!invalid){
            $.ajax({
                url: "/apotek/transaksi-resep/save-cache",
                type: "post",
                data: form.serialize(),
                beforeSend: function () {
                    ajaxLoading(submit_btn);
                },
                success: function (data) {
                    docoNotification("success", i18next.t("Berhasil"), i18next.t("Data berhasil di tambah"));
                    form = 'false';
                    var value = data.data;
                    console.log(value);
                    var response = {};
                    response[value.posisi] = value;
                    transObat = $.extend({}, transObat, response);
                    appendObat(transObat);
                    console.log(transObat);
                    urutObatRs = urutObatRs + 1;
                    $(".r_ke").val("");
                    $(".r_ke").prop("disabled", true);
                    $(".racikan_id").prop("checked", false);
                    $(".autoObat").val(null).trigger("change");
                    $(".signaid").val(null).trigger("change");
                    $(".qty-obat").val("");
                    $(".qty").val("");
                    $(".id_obat").val(null).trigger("change");

                    $("#ampuls_id").val(null).trigger("change");
                    $(".qty_tersedia").html("");
                    $(".harga_text").html("");
                    $(".stok_text").html("");
                    $("#harga_konversi").val("");
                    $("#etiket").val("");

                    return false;
                },
                error: function (res) {
                    ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambah'));
                    var resMessage = res.responseJSON.message;
                    docoNotification("error", i18next.t("Perhatian"), i18next.t(resMessage));
                    return false;
                },
                complete: function() {
                    ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambah'));
                }
            });

        }else{
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Nama Obat telah di input"));
            ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambah'));
            return false;
        }
    });

    /*
    var form = false;
    $("#form-obat").submit(function (e) {
        
        if (form) {
            return false;
        } else {
            form = true;
            invalid = false;
            e.preventDefault();
            var submit_btn = $(this).find(".add");
            var _r_ke = $(".r_ke").val();
            var _racikan_id = $(this).find(".racikan_id").is(":checked");
            var _id_obat = $(".id_obat").val();
            var _cekObatR = `${_r_ke}-${_id_obat}-${_racikan_id}`;
            var _stok = $(".stok").val();
            var _signa = $(".signa").val();
            var _qty = $(".qty").val();
            var id_barang_list = $('.list_barang').get();

            if (_racikan_id) {
                if (_r_ke == '') {
                    docoNotification("warning", i18next.t("Perhatian"), i18next.t("R ke tidak boleh kosong"));
                    ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambah'));
                    form = false;

                    return false;
                }
            }

            if (_id_obat == '') {
                docoNotification("warning", i18next.t("Perhatian"), i18next.t("Obat tidak boleh kosong"));
                ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambah'));
                form = false;

                return false;
            }

            if (_qty == '') {
                docoNotification("warning", i18next.t("Perhatian"), i18next.t("Jumlah tidak boleh kosong"));
                ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambah'));
                form = false;

                return false;
            }

            for (var i = 0; i < id_barang_list.length; i++) {
                var cek_obat = $(id_barang_list[i]).data('id');

                if (_cekObatR == cek_obat) {
                    invalid = true;
                }
            }
            if (!invalid) {
                $.ajax({
                    url: "/apotek/transaksi-resep/save-cache?id="+id,
                    type: "post",
                    data: $(this).serialize(),
                    beforeSend: function () {
                        ajaxLoading(submit_btn);
                    },
                    success: function (data) {
                        docoNotification("success", i18next.t("Berhasil"), i18next.t("Data berhasil di tambah"));
                        form = false;
                        var value = data.data;
                        var response = {};
                        response[value.posisi] = value;
                        transObat = $.extend({}, transObat, response);;
                        appendObat(transObat)
    
                        $(".r_ke").val("");
                        $(".r_ke").prop("disabled", true);
                        $(".racikan_id").prop("checked", false);
                        $(".autoObat").val(null).trigger("change");
                        $(".signaid").val(null).trigger("change");
                        $(".qty").val("");
                    },
                    error: function (res) {
                        ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambah'));
                        var resMessage = res.responseJSON.message;
                        docoNotification("error", i18next.t("Perhatian"), i18next.t(resMessage));
                        form = false;
                    },
                    complete: function() {
                        ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambah'));
                    }
                })
            } else if (invalid) {
                docoNotification("warning", i18next.t("Perhatian"), i18next.t("Nama Obat telah di input"));
                ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambah'));
                form = false;
            }
        }
    })
    */
    function appendObat(object) {
        var _no = 0;
        var _html = "";
        $(".default-value").attr("style", "display:none");
        $.each(object, function (x, y) {
            if (typeof object[x] !== "undefined") {
                _no++;
                _html += "<tr class=\"resep\">";
                    _html += "<td style='display:none;'><input type='hidden' class='list_barang' data-id='" + y.r_ke + "-" + y.obatalkes_id + "-" + y.racikan_id + "' value='" + y.obatalkes_id + "'></td>";
                    _html += "<td class=\"numbering\">" + _no + "</td>";
                    _html += "<td>" + y.jenis_racikan + "</td>";
                    _html += "<td>" + ((y.r_ke == null) ? '-' : y.r_ke)  + "</td>";
                    _html += "<td>" + y.obatalkes_nama + "</td>";
                    _html += "<td align=\"right\">" + docoHelper.convertToRupiah(y.harga) + "</td>";
                    _html += "<td class=\"signa\">" + y.signa + "</td>";
                    _html += "<td class=\"qty\" align=\"right\">" + y.qty + "</td>";
                    _html += "<td class=\"satuan\" align=\"right\">" + y.satuan_input + "</td>";
                    _html += "<td class=\"satuan\" align=\"right\">" + y.etiket + "</td>";
                    _html += "<td align=\"right\" class=\"total_harga\" data-netto=\""+y.harganetto+"\" data-sub=\"" + y.subtotal + "\">" + docoHelper.convertToRupiah(y.subtotal) + "</td>";
                    _html += "<td><a style='display:"+display+"' id=\"deleted\" class=\"btn btn-xs btn-danger deleted\" data-rdId=\"" + y.resepturdetail_id + "\" data-jml=\"" + y.qty + "\" data-id=\"" + x + "\"><i class=\"fa fa-trash\"></i></a></td>";
                _html += "</tr>";
            }
            object[x] = y;

        });

        if (_html === "") {
            _html += "<tr>";
            _html += "<td colspan=\"9\" id=\"data-null\" class=\"text-center\">Data Tidak Ditemukan</td>";
            _html += "</tr>";
        }
        $("#list-obat").html("");
        $("#list-obat").prepend(_html);
        sumHarga()
        // setTimeout(function(){ $('.doco-number').trigger('keyup'); }, 10)
        // reSum()
    }
    function ajaxLoading(element) {
        $(element).attr("disabled", true);
        $(element).html("<i class=\"fa fa-spinner fa-pulse fa-1x fa-fw\"></i>");
    }

    function ajaxAfterLoading(element, text) {
        $(element).attr('disabled', false);
        $(element).html(text)
    }

    function sumHarga(){
        var sum_subtotalItem = 0;
        $.each($(".total_harga"), function () {
            var value = ($(this).data("sub"));
            var netto = ($(this).data("netto"));
            sum_subtotalItem += value;
            sum_subtotalNetto += netto;
        });
        $(".subTotalItem").val(sum_subtotalItem);
        $(".total").html(docoHelper.convertToRupiah(sum_subtotalItem));
    }
    $('.signaid').on('change', function(){
        var _txt = $('.signaid option:selected').text();
        $('.signanama').val(_txt)
    })

    /*improvement save-bebas with shortcut alt+s*/
    $(document).on('keydown', null, 'alt+s', function (event) {
        $("#save-rs").click();
    });
    
    $('#save-rs').on('click', function (e) {
        e.preventDefault();
        var _id = $(this).data('id');
        var _url = '/apotek/transaksi-resep/save-rs?id='+id;
        $(this).docoForm('click', {
            url: _url,
            data: {
                totalharga_jual: $('.subTotalItem').val(), 
                totalharga_netto: sum_subtotalNetto, 
                keterangan: $('#keterangan').val()
            },
            success: function(data) {
                setTimeout(function(){ 
                    $('#save-rs, #ulang, #tambah-obat').attr('disabled', 'disabled'); 
                    $('#deleted').css('display','none');
                    $('.deleted').css('display','none');
                    $('#form-obat :input').prop('disabled', 'disabled');
                },500);
                // console.log(data)
                (new PNotify({
                    title: "Berhasil",
                    text: "Penjualan Resep Rumah Sakit dengan Nomor " + "<strong>" + data.response.nomor + "</strong>" + " berhasil disimpan, apakah Anda ingin melakukan cetak?",
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
                    window.open("/apotek/transaksi-resep/cetak-pdf?id="+data.response.id+"&type=344");
                }).on('pnotify.cancel', function() {
    
                });
            }
        })
    });
    
    $(document).on("click", "#deleted", function (event) {
        event.preventDefault();
        var id_ = $(this).data("id");
        var type = 3 // rs
        var jml = $(this).data("jml");
        var resepturdetail_id = $(this).data("rdid");
        var button = this;
        var tes = parseInt($(".subtotal" + id).data("sub"));
        var valButton = $(button).html();
        var ResData = { "id" : id_ ,
                        "type" : type ,
                        "parentid" : id, 
                        "resepturdetail_id" : resepturdetail_id
                    };
        $(this).docoForm('click',{
            url: "/apotek/transaksi-resep/delete-cache/",
            // data: "id=" + id_ + "&type=" + type + "&parentid="+id+ "&resepturdetail_id="+resepturdetail_id,
            confirmTitle: i18next.t("Konfirmasi"),
            confirmMessage: i18next.t("Apa anda yakin ingin membatalkan data ini?"),
            data: ResData,
            method: "GET",
            before: function () {
                $(button).html("<i class=\"fa fa-spin fa-spinner\"></i>");
                $(button).prop("disabled", true);
            },
            success: function () {
                $(button).parent().parent().remove();
                sumHarga()
                docoNotification("success", i18next.t("Berhasil"), i18next.t("Data berhasil di hapus"));
                if (typeof transObat != "undefined") {
                    if (typeof transObat != "undefined") {
                        delete transObat[id_]
                    }
                    appendObat(transObat);
                }
            }
        });
    });
    $(document).on('click', '.ulang', function (e) {
        e.preventDefault();
        location.reload()
    });

    $(document).on('click', '.print', function (e) {
        e.preventDefault();

        var link = $(this).attr('data-target');

        if (typeof link !== 'undefined') {
            window.location.href = link;
        } else {
            $('#btn-print').prop('disabled', true);
        }
    });

});