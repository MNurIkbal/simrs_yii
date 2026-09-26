/* 
    Author : Randy Vianda Putra (aweutist)
*/

$(document).ready(function() {
    appendObat(transObat);
    setTimeout(function(){ $('.doco-number').trigger('keyup'); }, 10)
    
    docoHelper.is_pembulatankeatas = ispembulatan;
    docoHelper.satuanpembulatan = satuanpembulatan;
    const dateNow = () => {
        const date = new Date()
        this.set('select', [date.getFullYear(), date.getMonth(), date.getDate()]);
    }

    if (isBackdate == '1') {
        $('.pickadate').pickadate({
            format: 'dd mmmm yyyy',
            max: dateNow,
            onStart: function () {
                var date = new Date()
                this.set('select', [date.getFullYear(), date.getMonth(), date.getDate()]);
            }
        });
    } else {
        $('.pickadate').pickadate({
            format: 'dd mmmm yyyy',
            min: dateNow,
            max: dateNow,
            onStart: function () {
                var date = new Date()
                this.set('select', [date.getFullYear(), date.getMonth(), date.getDate()]);
            }
        });
    }

    function alertApotek(title, message, type, element) {
        new PNotify({
            title: i18next.t(title),
            text: i18next.t(message),
            addclass: 'alert alert-'+ element +' alert-arrow-right alert-styled-right',
            type: type
        });
    }

    $(".nama_pasien").prop("disabled", true);
    $(".dokter_resep").prop("disabled", true);
    $(".r_ke").prop("disabled", true);
    $(".is_modal_search").hide();
    $(".required_racikan").hide();

    $(".racikan_id").change(function () {
        var racikan_id = $(this).is(":checked");
        if (racikan_id) {
            $(".r_ke").prop("disabled", false);
            $(".required_racikan").show();
            $(".r_ke").focus();
        } else {
            $(".r_ke").prop("disabled", true);
            $('.r_ke').val('');
            $(".required_racikan").hide();
        }
    });

    $(".qty").on("keyup", function () {
        var qty = $(this).val();
        qty = parseInt(docoHelper.convertToAngka(qty));
        if (isNaN(qty)) {
            qty = 0;
        }
        $(this).val(qty);
    });

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
    // end here
    $('#id_auto_obat').select2({
        placeholder: '— Pilih —',
        minimumInputLength: 3,
        ajax: {
            url: '/apotek/transaksi-resep/get-data-ajax',
            dataType: 'json',
            quietMillis: 250,
            data: function(term, page){
                return{
                    q: term,
                    page: page
                }
            },
            processResults: function (res) {
                var arr = []
                $.each(res.data_stok, function (index, value) {
                    arr.push({
                        id: value.obatalkes_id,
                        text: value.obatalkes_nama
                    })

                    let data = [];
                    let response = res.data_stok;
                    for (var i in response) {
                        data.push({ id: response[i].obatalkes_id, text: response[i].obatalkes_nama });
                        apotek.list_stok[response[i].obatalkes_id] = response[i];
                    }

                    autoObat.change(function (e) {
                        var id = $(this).val();
                        var selected = apotek.list_stok[id];
                        if (typeof selected !== "undefined") {
                            // console.log(selected.obatalkes_id);
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
                })
                return {
                    results: arr
                };
            },
            // cache: true,
        },
        dropdownCssClass: 'bigdrop',
        escapeMarkup: function (m) { return m; },
    })
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

    var form = 'true';
    
    $(document).on('click', '#tambah-obat', function(event) {
        event.preventDefault();
        /* Act on the event */
        var data = $("#form-obat").serialize();
        var form = $("#form-obat");
        var submit_btn = form.find(".add");
        var _r_ke = form.find('.r_ke').val();
        var _racikan_id = form.find(".racikan_id").is(":checked");
        var _id_obat = form.find('.id_obat').val();
        var _cekObatR = `${_r_ke}-${_id_obat}-${_racikan_id}`;
        var _stok = form.find('.stok').val();
        var _signa = form.find('.signa').val();
        var _qty = form.find('.qty-obat').val();
        var id_barang_list = form.find('.list_barang').get();

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

        if (parseInt(_qty) == 0) {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Jumlah tidak boleh kurang dari 1"));
            ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambah'));
            return false;
        }

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
                    var response = {};
                    response[value.posisi] = value;
                    transObat = $.extend({}, transObat, response);
                    appendObat(transObat)

                    $(".r_ke").val("");
                    $(".r_ke").prop("disabled", true);
                    $(".racikan_id").prop("checked", false);
                    $(".autoObat").val(null).trigger("change");
                    $(".signaid").val(null).trigger("change");
                    $(".qty-obat").val("");
                    $(".id_obat").val(null).trigger("change");

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
    $("#form-obat").submit(function (e) {
        e.preventDefault();
        console.log( form );
        if (form == 'false') {
            return false;
        } else {

            form = true;
            invalid = false;
            var submit_btn = $(this).find(".add");
            var _r_ke = $(".r_ke").val();
            var _racikan_id = $(this).find(".racikan_id").is(":checked");
            var _id_obat = $(".id_obat").val();
            var _cekObatR = `${_r_ke}-${_id_obat}-${_racikan_id}`;
            var _stok = $(".stok").val();
            var _signa = $(".signa").val();
            var _qty = $(".qty-obat").val();
            var id_barang_list = $('.list_barang').get();

            if (_racikan_id) {
                if (_r_ke == '') {
                    docoNotification("warning", i18next.t("Perhatian"), i18next.t("R ke tidak boleh kosong"));
                    ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambah'));
                    form = 'false';

                    return false;
                }
            }
            console.log(_id_obat);
            if (_id_obat == '') {
                docoNotification("warning", i18next.t("Perhatian"), i18next.t("Obat tidak boleh kosong"));
                ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambah'));
                form = 'false';

                return false;
            }
            
            if (_qty == '') {
                
                docoNotification("warning", i18next.t("Perhatian"), i18next.t("Jumlah tidak boleh kosong"));
                ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambah'));
                form = 'false';

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
                    url: "/apotek/transaksi-resep/save-cache",
                    type: "post",
                    data: $(this).serialize(),
                    beforeSend: function () {
                        ajaxLoading(submit_btn);
                    },
                    success: function (data) {
                        docoNotification("success", i18next.t("Berhasil"), i18next.t("Data berhasil di tambah"));
                        form = 'false';
                        var value = data.data;
                        var response = {};
                        response[value.posisi] = value;
                        transObat = $.extend({}, transObat, response);
                        appendObat(transObat)
    
                        $(".r_ke").val("");
                        $(".r_ke").prop("disabled", true);
                        $(".racikan_id").prop("checked", false);
                        $(".autoObat").val(null).trigger("change");
                        $(".signaid").val(null).trigger("change");
                        $(".qty-obat").val("");
                        $(".id_obat").val(null).trigger("change");

                        return false;
                    },
                    error: function (res) {
                        ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambah'));
                        var resMessage = res.responseJSON.message;
                        docoNotification("error", i18next.t("Perhatian"), i18next.t(resMessage));
                        form = 'false';
                    },
                    complete: function() {
                        ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambah'));
                        form = 'false';
                        return false;
                    }
                })
            } else if (invalid) {
                docoNotification("warning", i18next.t("Perhatian"), i18next.t("Nama Obat telah di input"));
                ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambah'));
                form = 'false';
            }
        }
    });
    */

    $('.signaid').on('change', function(){
        var _txt = $('.signaid option:selected').text();
        $('.signanama').val(_txt)
    })
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
                    _html += "<td class=\"qty\">" + y.qty + "</td>";
                    _html += "<td align=\"right\" class=\"total_harga\" data-sub=\"" + y.subtotal + "\">" + docoHelper.convertToRupiah(y.subtotal) + "</td>";
                    _html += "<td>";
                    // _html += "<a id=\"edit-list\" class=\"btn btn-xs btn-primary edit" + x +"\" data-id=\"" + x + "\"><i class=\"fa fa-pencil\"></i></a> ";
                    _html += "<a id=\"deleted\" class=\"btn btn-xs btn-danger\" data-jml=\"" + y.qty + "\" data-id=\"" + x + "\"><i class=\"fa fa-trash\"></i></a></td>";
                _html += "</tr>";
            }
            object[x] = y;

        });

        if (_html === "") {
            _html += "<tr>";
            _html += "<td colspan=\"8\" id=\"data-null\" class=\"text-center\">Data Tidak Ditemukan</td>";
            _html += "</tr>";
        }
        $("#list-obat").html("");
        $("#list-obat").prepend(_html);
        var sum_subtotalItem = 0;
        $.each($(".total_harga"), function () {
            var value = parseInt($(this).data("sub"));
            sum_subtotalItem += value;
        });
        $("#subtotalItem").val("" + docoHelper.convertToRupiah(sum_subtotalItem));
        setTimeout(function(){ $('.doco-number').trigger('keyup'); }, 10)
        // reSum()
    }

    $(document).on("click", "#deleted", function (event) {
        event.preventDefault();
        var id = $(this).data("id");
        var type = 1 // bebas
        var jml = $(this).data("jml");
        var button = this;
        var tes = parseInt($(".subtotal" + id).data("sub"));
        var valButton = $(button).html();
        var ResData = { "id" : id ,
                        "type" : type 
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
                // sumHarga()
                docoNotification("success", i18next.t("Berhasil"), i18next.t("Data berhasil di hapus"));
                
                if (typeof transObat != "undefined") {
                    if (typeof transObat != "undefined") {
                        delete transObat[id]
                    }
                    appendObat(transObat);
                }
            }
        });
    });

    /*$(document).on("click", "#deleted", function () {
        var id = $(this).data("id");
        var type = 1 // bebas
        var jml = $(this).data("jml");
        var button = this;
        var tes = parseInt($(".subtotal" + id).data("sub"));
        var valButton = $(button).html();
        $(button).parent().parent().remove();

        $.ajax({
            type: "GET",
            url: "/apotek/transaksi-resep/delete-cache/",
            data: "id=" + id + "&type=" + type,
            beforeSend: function () {
                $(button).html("<i class=\"fa fa-spin fa-spinner\"></i>");
                $(button).prop("disabled", true);
            },
            success: function () {
                docoNotification("success", i18next.t("Berhasil"), i18next.t("Data berhasil di hapus"));
                
                if (typeof transObat != "undefined") {
                    if (typeof transObat != "undefined") {
                        delete transObat[id]
                    }
                    appendObat(transObat);
                }
            }
        });
    });*/

    $("#jasa_racik").on("keyup", function () {
        var jasa_racik = $(this).val();
        jasa_racik = parseInt(docoHelper.convertToAngka(jasa_racik));

        var subtotalItem = $("#subtotalItem").val();
        subtotalItem = parseInt(docoHelper.convertToAngka(subtotalItem));

        var biaya_admin = $("#biaya_admin").val();
        biaya_admin = parseInt(docoHelper.convertToAngka(biaya_admin));

        var pembulatan = $("#pembulatan").val();
        pembulatan = parseInt(docoHelper.convertToAngka(pembulatan));

        if (isNaN(jasa_racik)) {
            jasa_racik = 0
        }
        if (isNaN(biaya_admin)) {
            biaya_admin = 0
        }
        if (isNaN(pembulatan)) {
            pembulatan = 0
        }
        var sum = 0;
        sum = (jasa_racik + subtotalItem + biaya_admin);
        // $(this).val(docoHelper.convertToRupiah(jasa_racik));
        $("#total_tagihan").val(docoHelper.convertToRupiah(sum))
        docoHelper.pembulatan(sum, $('#pembulatan'), $('#total_tagihan'))
    });

    $("#biaya_admin").on("keyup", function () {
        var biaya_admin = $(this).val();
        biaya_admin = parseInt(docoHelper.convertToAngka(biaya_admin));

        var subtotalItem = $("#subtotalItem").val();
        subtotalItem = parseInt(docoHelper.convertToAngka(subtotalItem));

        var jasa_racik = $("#jasa_racik").val();
        jasa_racik = parseInt(docoHelper.convertToAngka(jasa_racik));

        var pembulatan = $("#pembulatan").val();
        pembulatan = parseInt(docoHelper.convertToAngka(pembulatan));

        if (isNaN(jasa_racik)) {
            jasa_racik = 0
        }
        if (isNaN(biaya_admin)) {
            biaya_admin = 0
        }
        if (isNaN(pembulatan)) {
            pembulatan = 0
        }
        var sum = 0;
        sum = (jasa_racik + subtotalItem + biaya_admin);
        // $(this).val(docoHelper.convertToRupiah(biaya_admin));
        $("#total_tagihan").val(docoHelper.convertToRupiah(sum))
        docoHelper.pembulatan(sum, $('#pembulatan'), $('#total_tagihan'))
    });

    function reSum() {
        var subtotalItem = $("#subtotalItem").val();
        subtotalItem = parseInt(docoHelper.convertToAngka(subtotalItem));

        var biaya_admin = $("#biaya_admin").val();
        biaya_admin = parseInt(docoHelper.convertToAngka(biaya_admin));

        var jasa_racik = $("#jasa_racik").val();
        jasa_racik = parseInt(docoHelper.convertToAngka(jasa_racik));

        var pembulatan = $("#pembulatan").val();
        pembulatan = parseInt(docoHelper.convertToAngka(pembulatan));

        if (isNaN(jasa_racik)) {
            jasa_racik = 0
        }
        if (isNaN(biaya_admin)) {
            biaya_admin = 0
        }
        if (isNaN(pembulatan)) {
            pembulatan = 0
        }

        if (isNaN(subtotalItem)) {
            subtotalItem = 0
        }
        if (subtotalItem == 0) {
            $("#biaya_admin").val(0);
            $("#jasa_racik").val(0);
            $("#total_tagihan").val(0);
            $("#pembulatan").val(0);
        } else {
            var sum = 0;
            sum = (jasa_racik + subtotalItem + biaya_admin);
            // $(this).val(docoHelper.convertToRupiah(subtotalItem));
            docoHelper.pembulatan(sum, $('#pembulatan'), $('#total_tagihan'))
            $("#total_tagihan").val(docoHelper.convertToRupiah(sum))
        }
    }

    function ajaxLoading(element) {
        $(element).attr("disabled", true);
        $(element).html("<i class=\"fa fa-spinner fa-pulse fa-1x fa-fw\"></i>");
    }

    function ajaxAfterLoading(element, text) {
        $(element).attr('disabled', false);
        $(element).html(text)
    }

    $(document).on("click", "#edit-list", function (e) {
        e.preventDefault();
        $(".add").hide();
        $(".edited").remove()
        var html_button = '<button type="submit" class="btn bg-teal edited"><i class="fa fa-floppy-o"></i> '+i18next.t('Simpan')+'</button>';
        $(".button-list").append(html_button);
        var id = $(this).data('id');
        var data = transObat[id];
        var submit_btn = $(this).find(".add");
        $(".autoObat").val(data.obatalkes_id).trigger("change");
        $(".r_ke").val(data.r_ke);
        $(".racikan_id").prop('checked', data.racikan_id);
        $(".signa").val(data.signa);
        $(".qty").val(data.qty);
        $(".posisi").val(data.posisi);
    })

    $(document).on("click", ".edited", function () {
        var submit_btn = $(this);
        var jml = $(".stok").val();
        var type = 1 // bebas
        var id = $(".posisi").val();
        var dataPost = {
            posisi: $(".posisi").val(),
            obatalkes_id: $(".id_obat").val(),
            obatalkes_nama: $(".obat_nama").val(),
            signa: $(".signa").val(),
            qty: $(".qty").val(),
            ppn: $(".ppn").val(),
            r_ke: $(".r_ke").val(),
            racikan_id: $(".racikan_id").val(),
            harga: $(".harga").val(),
            ppn: $(".ppn").val(),
            hargamax: $(".hargamax").val(),
            hargamin: $(".hargamin").val(),
            hargarata: $(".hargarata").val(),
            satuankecil_id: $(".satuankecil_id").val()
        };
        $.ajax({
            url: "/apotek/transaksi-resep/update-cache?id=" + id + "&type=" + type,
            type: "post",
            dataType: 'json',
            data: dataPost,
            beforeSend: function () {
                ajaxLoading(submit_btn);
            },
            success: function (data) {
                docoNotification("success", i18next.t("Berhasil"), i18next.t("Data berhasil di update"));
                
                const value = data.data;
                var response = {};
                response[value.posisi] = value;
                transObat = $.extend({}, transObat, response);;
                appendObat(transObat)
                
                $(".r_ke").val("");
                $(".racikan_id").prop('checked', false);
                $(".signa").val("");
                $(".qty").val("");
                $(".posisi").val("");
                $(".autoObat").val(null).trigger("change");
                $(".edited").remove();
                $(".add").show();
                
            },
            error: function (res) {
                ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambah'));
                var resMessage = res.responseJSON.message;
                docoNotification("error", i18next.t("Perhatian"), i18next.t(resMessage));
                form = false;
            },
            done: function () {
            }   
        })
    })

    /*improvement save-bebas with shortcut alt+s*/
    $(document).on('keydown', null, 'alt+s', function (event) {
        $("#save-bebas").click();
    });

    $(document).on("click", "#save-bebas", function(e) {
        e.preventDefault();
        var submit_btn = $(this);
        var isPasienRs = $('.isPasienRs').is(':checked');
        var no_resep = $(".no_resep").val();
        var penjamin = $(".penjamin").val();
        var cara_bayar = $(".cara_bayar").val();
        var pasien_id = $(".nama_pasien").val();
        var nama_pembeli_pasien = $(".nama_pasien").find(":selected").text();
        var pegawai_id = $(".dokter_resep").val();
        var iter = $(".iter").val();
        var nama_pembeli = $(".nama_pembeli").val();
        var tanggal_penjualan = $(".tanggal_penjualan").val();
        var count_list = $('.list_barang').get().length;

        if (count_list == 0) {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("List Obat Kosong!"));
            ajaxAfterLoading(submit_btn, "<i class='fa fa-save'></i> " + i18next.t('Simpan'));

            return false;
        }

        if (isPasienRs) {
            if (pasien_id == '') {
                docoNotification("warning", i18next.t("Perhatian"), i18next.t("Nama pasien tidak boleh kosong"));
                ajaxAfterLoading(submit_btn, "<i class='fa fa-save'></i> " + i18next.t('Simpan'));

                return false;
            }

            if (pegawai_id == '') {
                docoNotification("warning", i18next.t("Perhatian"), i18next.t("Dokter resep tidak boleh kosong"));
                ajaxAfterLoading(submit_btn, "<i class='fa fa-save'></i> " + i18next.t('Simpan'));

                return false;
            }
        }

        if (cara_bayar == '') {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Cara bayar tidak boleh kosong"));
            ajaxAfterLoading(submit_btn, "<i class='fa fa-save'></i> " + i18next.t('Simpan'));

            return false;
        }

        if (penjamin == '') {
            alertApotek(i18next.t('Perhatian'), i18next.t('Penjamin tidak boleh kosong'), 'warning', 'warning');
            ajaxAfterLoading(submit_btn, "<i class='fa fa-save'></i> " + i18next.t('Simpan'));

            return false;
        }

        var subtotalItem = $("#subtotalItem").val();
        subtotalItem = parseInt(docoHelper.convertToAngka(subtotalItem));

        var biaya_admin = $("#biaya_admin").val();
        biaya_admin = parseInt(docoHelper.convertToAngka(biaya_admin));
        if (isNaN(biaya_admin)) {
            biaya_admin = 0;
        }

        var jasa_racik = $("#jasa_racik").val();
        jasa_racik = parseInt(docoHelper.convertToAngka(jasa_racik));
        if (isNaN(jasa_racik)) {
            jasa_racik = 0;
        }

        var pembulatan = $("#pembulatan").val();
        pembulatan = parseInt(docoHelper.convertToAngka(pembulatan));

        var total_tagihan = $("#total_tagihan").val();
        total_tagihan = parseInt(docoHelper.convertToAngka(total_tagihan));

        if (typeof penjamin == 'undefined') {
            penjamin = '';
        }

        var dataPost = {
            cara_bayar: cara_bayar,
            penjamin: penjamin,
            total_obat: subtotalItem,
            biaya_admin: biaya_admin,
            jasa_racik: jasa_racik,
            pembulatan: pembulatan,
            total_tagihan: total_tagihan,
            pasien_id: pasien_id,
            pegawai_id: pegawai_id,
            iter: iter,
            nama_pembeli : (nama_pembeli)
                ? nama_pembeli
                : nama_pembeli_pasien,
            tanggal_penjualan: tanggal_penjualan,
            no_resep: no_resep,
            catatan: $('#catatan').val()
        };

        $(this).docoForm("click", {
            url: "/apotek/transaksi-resep/save-bebas",
            method: "post",
            dataType: "json",
            data: dataPost,
            beforeSend: function () {
                ajaxLoading(submit_btn);
            },
            success: function (data) {
                // docoNotification("success", i18next.t("Berhasil"), i18next.t("Data berhasil di simpan"));
                const id = data.response.id;
                const type_bebas = 343;
                // $('#btn-print').prop('disabled', false);
                // $('#btn-print').attr('data-target', '/apotek/transaksi-resep/cetak-pdf?id=' + id + '&type=' + type_bebas);
                $(".penjamin").val("").trigger('change');
                $(".cara_bayar").val("").trigger('change');
                $(".nama_pasien").val("").trigger('change');
                $(".dokter_resep").val("").trigger('change');
                $(".iter").val("");
                $(".nama_pembeli").val("");
                $("#pembulatan").val(0);
                $("#subtotalItem").val(0);
                $("#biaya_admin").val(0);
                $("#jasa_racik").val(0);
                $("#total_tagihan").val(0);
                $("#catatan").val("");
                transObat = {};
                appendObat(transObat);
                (new PNotify({
                    title: "Berhasil",
                    text: "Penjualan Resep Bebas dengan Nomor " + "<strong>" + data.response.nomor + "</strong>" + " berhasil disimpan, apakah Anda ingin melakukan cetak?",
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
                    window.open("/apotek/transaksi-resep/cetak-pdf?id="+data.response.id+"&type=343");
                }).on('pnotify.cancel', function() {
    
                });
            },
            error: function() {
                ajaxAfterLoading(submit_btn, "<i class='fa fa-save'></i> " + i18next.t('Simpan'));
                // docoNotification("error", i18next.t("Terjadi Kesalahan"), i18next.t("Data gagal di simpan"));
                appendObat(transObat);
            },
            complete: function () {
                ajaxAfterLoading(submit_btn, "<i class='fa fa-save'></i> " + i18next.t('Simpan'));

                return false;
            }
        });
    })
    
    $(document).on("click", "#ulang", function(e) {
        e.preventDefault();
        $(this).docoForm('click', {
            url: "/apotek/transaksi-resep/reset-cache?type=1",
            data: "",
            skipConfirm: true,
            skipSuccessNotif: true,
            success: function(data){
                var form = $("#ajax-form2");
                    form[0].reset();

                var formHarga = $("#form-obat2");
                    formHarga[0].reset();
                var transObat = {};

                $("#cara_bayar").val(null).trigger("change");
                var penjamin = $("#penjamin");
                    penjamin.val(null).trigger("change");
                    penjamin.attr('disabled', 'disabled');
                appendObat(transObat);
            }
        })
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

    $('#id_nama_pasien').select2({
        placeholder: '— Pilih —',
        minimumInputLength: 3,
        ajax: {
            url: '/apotek/end-point/get-pasien',
            dataType: 'json',
            quietMillis: 250,
            data: function(term, page){
                return{
                    q: term,
                    page: page
                }
            },
            processResults: function (data) {
                return {
                    results: data.results
                };
            }
        },
        dropdownCssClass: 'bigdrop',
        escapeMarkup: function (m) { return m; },
    })

    $('#id_dokter_resep').select2({
        placeholder: '— Pilih —',
        minimumInputLength: 3,
        ajax: {
            url: '/apotek/end-point/get-pegawai',
            dataType: 'json',
            quietMillis: 250,
            data: function(term, page){
                return{
                    q: term,
                    page: page
                }
            },
            processResults: function (data) {
                return {
                    results: data.results
                };
            }
        },
        dropdownCssClass: 'bigdrop',
        escapeMarkup: function (m) { return m; },
    })
})