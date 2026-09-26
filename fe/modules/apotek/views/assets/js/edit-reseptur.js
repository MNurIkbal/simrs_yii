/**
 * @author : Randy Vianda Putra (aweutist)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 * @edited by : Anggoro (tri.anggoro@docotel.com)
 */

var _group = {};
var isEditReseptur = isEditResep = false;
var type;
var pathEditReseptur = '/apotek/transaksi-resep/edit-reseptur';
var pathEditResep = '/apotek/transaksi-resep/edit-resep';
var pathApproveResep = '/apotek/transaksi-resep/approve-reseptur';
var sum_subtotalNetto = 0;
var mapping_det = [];
var det_arr = [];
var det_kronis = [];
var apotek = { list_stok: {} };
var apotek_nr = { list_stok: {} };
var transaksi_obat_minus = false;
var transaksi_obat_minus_racikan = false;
if(window.location.pathname == pathEditReseptur){
    isEditReseptur = true;
    type = 5;
}

if(window.location.pathname == pathEditResep){
    isEditResep = true;
    type = 5;
}

$(document).ready(function () {
    var _form_racikan = $("#form-racikan");
    var _form_nonracikan = $("#form-obat");
    var racikan_id = $(this).is(":checked");

    if(isEditReseptur || isEditResep || pathApproveResep){
        appendObat(transObat);
    }

    $('.biayaAdmin').val(docoHelper.convertToRupiah(biayaadministrasi));
    sumHarga();
    $('.disabled').prop('disabled', true);
    $(".r_ke").prop("disabled", true);
    $(".required_racikan").hide();
    $(".racikan_id").change(function () {
        // tampilkan label required jika user memilih obat racikan
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
    $(document).on('input', '.iter', function() {
        match        = (/(\d{0,9})[^.]*((?:\.\d{0,2})?)/g).exec(this.value.replace(/[^\d.]/g, ''));
        this.value   = match[1] + match[2];
    });

    $(document).on('input', '.r_ke', function() {
        match        = (/(\d{0,9})[^.]*((?:\.\d{0,2})?)/g).exec(this.value.replace(/[^\d.]/g, ''));
        this.value   = match[1] + match[2];
        $("#rke_display").html($(this).val());
    });

    $(document).on('change', '.r_ke', function() {
        var rke = parseInt(this.value);
        var existing_racikan = transObat.find(val => val.r_ke == rke)
        if(typeof existing_racikan !== 'undefined') {
            $(".nama_racikan").val(existing_racikan.nama_racikan)
            $(".qty_racikan").val(existing_racikan.qty_racikan)
            $("#satuan_racikan_id").val(existing_racikan.satuan_racikan_id).trigger('change')
            $("#signa_racikan").val(existing_racikan.signa_id).trigger('change')
            $("#racikan_kronis").val(existing_racikan.racikan_kronis)

            var catatan;
            if(existing_racikan.etiket == "-") {
                catatan = null
            } else {
                catatan = existing_racikan.etiket
            }
            $(".catatan").val(catatan)
        } else {
            $(".catatan").val(null)
            $(".nama_racikan").val(null)
            $(".qty_racikan").val(null)
            $(".racikan_kronis").val(null)
            $("#signa_racikan").val(null).trigger('change')
            $("#satuan_racikan_id").val(null).trigger('change')
        }
    });

    $(document).on("input", ".qty_racikan ", function () {
        match = /(\d{0,9})[^.]*((?:\.\d{0,2})?)/g.exec(
          this.value.replace(/[^\d.]/g, "")
        );
        this.value = match[1] + match[2];
    });

    $(document).on('input', '.qty', function() {
        match        = (/(\d{0,9})[^.]*((?:\.\d{0,2})?)/g).exec(this.value.replace(/[^\d.]/g, ''));
        this.value   = match[1] + match[2];

        // set value konversi ketika user menginput qty
        var _qty = $(this).val();
        var nilai_konversi = $("#nilai_konversi").val();
        var konversi = _qty * nilai_konversi;
        konversi = parseFloat(konversi).toFixed(docoHelper.decimal_places); // set harga konversi dibulatkan 2 angka di belakang koma
        $(".qty_konversi").val(konversi);
    });

    $('#id_auto_obat').change(function(){
        $(".qty_konversi").val("");
        $(".qty").val("");
        $("#nilai_konversi").val("");
    });

    $('#select_obat_non_racikan').change(function(){
        $(".qty_konversi").val("");
        $(".qty").val("");
        $("#nilai_konversi").val("");
    });

    // ketika value dropdown satuan berubah, set ulang label satuan, value harga, stok, dan qty konversi
    $('#ampuls_id').on('change', function(){
        // var _id = $('#id_auto_obat').val();
        var _id = $('#select_obat_non_racikan').val();

        $(".qty_konversi").val("");
        $(".qty").val("");

        var _val     = $(this).val();
        var konversi = _group[_val];
        if (konversi != null){
            konversi = parseFloat(konversi).toFixed(docoHelper.decimal_places);
        }
        $("#nilai_konversi").val(konversi);

        var _stok          = $(".stok").val();
        var _konversi_stok = parseFloat(_stok)/parseFloat(konversi);
        _konversi_stok     = (isNaN(_konversi_stok)) ? 0 : _konversi_stok;
        if (_konversi_stok != null){
            _konversi_stok = _konversi_stok.toFixed(docoHelper.decimal_places);
        }
        $(".qty_tersedia").html(_konversi_stok);

        var _hargajual      = $(".hargajual").val();
        var _konversi_harga = _hargajual*konversi;
        if (_konversi_harga != null){
            _konversi_harga = _konversi_harga.toFixed(docoHelper.decimal_places);
        }
        $(".harga_konversi").val(docoHelper.convertToRupiah(_konversi_harga));

        var _val_nama  = $(this).find('option:selected').text();
        var _val_value = $(this).val();
        if ( _val_nama == '--Pilih--'){
            _val_nama = '';
            $(".harga_konversi").val('');
        }
        $(".stok_text").html(_val_nama);
        $(".harga_text").html(_val_nama);
        $(".satuan_kecil_text").html(_val_nama);

        $(".satuaninput_id").val(_val_value);
        $(".satuan_input").val(_val_nama);
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
    let posisi = $(".posisi");
    let kronis = $("#racikan_kronis");
    // end here

    $("#id_auto_obat").select2({
        width: '100%',
        language: 'id',
        ajax: {
            url: '/apotek/transaksi-resep/get-data-ajax',
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page || 1,
                    penjamin_id: $('#penjamin_id').val() || 1
                }
            },
            delay: 500,
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
                return {
                    results: arr,
                    pagination: {
                        more: res.data_stok.length > 10
                    }
                };
            }
        }
    });

    $('#select_obat_non_racikan').on('depdrop:afterChange', function(event, id, value, jqXHR, textStatus) {
        let ajaxResults = $('#select_obat_non_racikan').depdrop('getAjaxResults');
        list_obat = ajaxResults['output'];
      });

      $('#select_obat_non_racikan').docoPaginationSelec2(
        config = {
            placeholder : 'Pilih Obat ... ',
            _api : '/apotek/transaksi-resep/list-obat-alkes-depo',
            ajax : {
                data: function(params) {
                    return {
                        q: params.term,
                        page: params.page || 1,
                        ruangan_id: ruangan_id,
                        penjamin_id: $("#penjamin_id").val() || 1,
                        kelaspelayanan_id: $("#kelaspelayanan_id").val() || 0,
                        noresep: noresep,
                        instalasi_id: instalasiId,
                    }
                },
                results: function (data, params) {
                    var more = (params.page * 30) < data.total_count;
                    return { results: data.items, more: more };
                },
                processResults: function(res, params) {
                    params.page = params.page || 1;
                    transaksi_obat_minus = res.transaksi_obat_minus;
                    var arr = [];
                    $.each(res.data_stok, function(index, value) {
                        if (index < 10) {
                            arr.push({
                                id: value.obatalkes_id,
                                text: value.obatalkes_nama
                            })

                            let data = [];
                            let response = res.data_stok;
                            for (var i in response) {
                                data.push({ id: response[i].obatalkes_id, text: response[i].obatalkes_nama });
                                apotek_nr.list_stok[response[i].obatalkes_id] = response[i];
                            }
                        }
                    });
                    return {
                        results: arr,
                        pagination: {
                            more: res.data_stok.length > 10
                        }
                    };
                }
            }
        }
      ).on('change', function(e) {
        var id = $(this).val();
        var selected = apotek_nr.list_stok[id];
        if (typeof selected !== "undefined") {
          id_obat.val(selected.obatalkes_id);
          stok.val(selected.qty_tersedia);
          $(".qty_tersedia").html(
            !isNaN(selected.qty_tersedia) ? selected.qty_tersedia : 0
          );
          harga.val(selected.harganetto);
          ppn.val(selected.ppn);
          obat_nama.val(selected.obatalkes_nama);
          satuankecil_id.val(selected.satuankecil_id);
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
          var _nilai_konversi = $("#nilai_konversi").val();
          var _konversi_harga = selected.hargajual * _nilai_konversi;
          _konversi_harga = _konversi_harga.toFixed(2);
          $(".harga_konversi").val(
            docoHelper.convertToRupiah(_konversi_harga)
          );

            $("#tambah-obat").attr("disabled", false);
            $("#disabled_button_info").attr("style", "display:none");

            if (selected.qty_tersedia <= 0 && !transaksi_obat_minus) {
                $("#tambah-obat").attr("disabled", "true");
                $("#disabled_button_info").attr("style", "display:block");
            }

          $("#ampuls_id").val(selected.satuankecil_id).trigger("change");
          satuankecilid = selected.satuankecil_id;
        }
      });

    $('#select_obat_racikan').on('depdrop:afterChange', function(event, id, value, jqXHR, textStatus) {
        let ajaxResults = $('#select_obat_racikan').depdrop('getAjaxResults');
        list_obat = ajaxResults['output'];
      });

    $('#select_obat_racikan').docoPaginationSelec2(
    config = {
        placeholder : 'Pilih Obat ... ',
        _api : '/apotek/transaksi-resep/list-obat-alkes-depo',
        ajax : {
            data: function(params) {
                return {
                    q: params.term,
                    page: params.page || 1,
                    ruangan_id: ruangan_id,
                    penjamin_id: $("#penjamin_id").val() || 1,
                    kelaspelayanan_id: $("#kelaspelayanan_id").val() || 0,
                    instalasi_id: instalasiId,
                }
            },
            results: function (data, params) {
                var more = (params.page * 30) < data.total_count;
                return { results: data.items, more: more };
            },
            processResults: function(res, params) {
                params.page = params.page || 1;
                transaksi_obat_minus_racikan = res.transaksi_obat_minus;
                var arr = [];
                $.each(res.data_stok, function(index, value) {
                    if (index < 10) {
                        var _disabled = false;
                        if (value.qty_tersedia <= 0 && !transaksi_obat_minus_racikan) {
                            _disabled = true;
                        }
                        arr.push({
                            id: value.obatalkes_id,
                            text: value.obatalkes_nama,
                            disabled: _disabled
                        })

                        let data = [];
                        let response = res.data_stok;
                        for (var i in response) {
                            data.push({ id: response[i].obatalkes_id, text: response[i].obatalkes_nama });
                            apotek.list_stok[response[i].obatalkes_id] = response[i];
                        }
                    }
                });
                return {
                    results: arr,
                    pagination: {
                        more: res.data_stok.length > 10
                    }
                };
            }
        }
    }
  ).on('change', function(e) {
      var id = $(this).val();
      var selected = apotek.list_stok[id];
      if (typeof selected !== "undefined") {
        _stok_sisa = parseFloat(selected.qty_tersedia).toFixed(2);
        _hargajual = parseFloat(selected.hargaygdipakai !== null ? selected.hargaygdipakai : 0).toFixed(2);
        _harganetto = parseFloat(selected.harganetto).toFixed(2);
        _satuankecil_nama = selected.satuankecil_nama;
        _satuankecil_id = selected.satuankecil_id;

        _form_racikan.find(`input[name=obatalkes_id]`).val(id);
        _form_racikan.find(`input[name=obat_nama]`).val(selected.obatalkes_nama);
        _form_racikan.find(`input[name=stok]`).val(_stok_sisa);
        _form_racikan.find(`.qty_tersedia`).html(isNaN(_stok_sisa) ? "-1" : _stok_sisa);
        _form_racikan.find(`input[name=harga]`).val(_harganetto);
        _form_racikan.find(`input[name=ppn]`).val(selected.ppn);
        _form_racikan.find(`input[name=satuankecil_id]`).val(_satuankecil_id);
        _form_racikan.find(`input[name=harganetto]`).val(_harganetto);
        _form_racikan.find(`input[name=jmlmargin]`).val(selected.hn_margin);
        _form_racikan.find(`input[name=jmldiscount]`).val(selected.hn_diskon);
        _form_racikan.find(`input[name=jmlppn]`).val(selected.hn_ppn);
        _form_racikan.find(`input[name=persendiscount]`).val(selected.disc);
        _form_racikan.find(`input[name=persenmargin]`).val(selected.margin);
        _form_racikan.find(`input[name=persenppn]`).val(selected.ppn);
        _form_racikan.find(`input[name=hargajual]`).val(selected.hargajual);

        var urutan = parseInt(urutObatRs);
        urutan = urutan + 1;
        _form_racikan.find(`input[name=posisiNo]`).val(urutan);

        var _nilai_konversi = _form_racikan.find(`input[name='nilai_konversi']`).val();
        var _konversi_harga = parseFloat(selected.hargajual) * parseFloat(_nilai_konversi);
        _konversi_harga = _konversi_harga.toFixed(2);
        _form_racikan.find(`input[name='harga_konversi']`).val(docoHelper.convertToRupiah(_konversi_harga));

        
        $("#satuan_racikan").val(_satuankecil_id).trigger("change");
        satuankecilid = _satuankecil_id;
    }
  });

    $("#satuan_racikan").on('change', function(ev){
        $(".qty_konversi").val("");
        $(".qty").val("");

        var _satuan_id   = $(this).val();
        var konversi     = _group[_satuan_id];
        if (konversi != null){
            konversi = parseFloat(konversi).toFixed(docoHelper.decimal_places);
        }

        _form_racikan.find(`input[name='nilai_konversi']`).val(konversi);
        var __nilai_konversi = _form_racikan.find(`input[name='nilai_konversi']`).val();

        // Stok
        var __stok_obat          = _form_racikan.find(`input[name='stok']`).val();
        var __konversi_stok_obat = parseFloat(__stok_obat) / parseFloat(__nilai_konversi);
        __konversi_stok_obat = isNaN(__konversi_stok_obat) ? 0 : __konversi_stok_obat;
        if(__konversi_stok_obat != null){
          __konversi_stok_obat = __konversi_stok_obat.toFixed(docoHelper.decimal_places);
        }
        _form_racikan.find('.qty_tersedia').html(__konversi_stok_obat);

        var _id = $('#select_obat_racikan').val();
        $.each(transObat, function (index, value) {
            if (value.obatalkes_id == _id && !value.is_deleted && value.obatalkespasien_id == undefined && value.resepturdetail_id == undefined) {
                let qty_hitung
                if (value.det == undefined) {
                    qty_hitung = __konversi_stok_obat - value.qty;
                    _form_racikan.find('.qty_tersedia').html(qty_hitung);
                } else {
                    qty_hitung = __konversi_stok_obat - value.det;
                    _form_racikan.find('.qty_tersedia').html(qty_hitung);
                }
            }
        });

        // Harga
        var __hargajual       = _form_racikan.find(`input[name='hargajual']`).val();
        var __konversi_harga  = parseFloat(__hargajual) * parseFloat(__nilai_konversi);
        _form_racikan.find(`input[name='harga_konversi']`).val(docoHelper.convertToRupiah(__konversi_harga));

        // Label Satuan
        var __satuan_nama   = $("#satuan_racikan").find('option:selected').text();
        var __satuan_id     = $("#satuan_racikan").val();
        if ( __satuan_nama == "--Pilih--"){
            __satuan_nama = "";
            __satuan_id = "";
            _form_racikan.find('.qty_tersedia').html(__konversi_stok_obat);
        };

        _form_racikan.find('.satuan').html(__satuan_nama);
        _form_racikan.find(`input[name='satuaninput_id']`).val(__satuan_id);
        _form_racikan.find(`input[name='satuan_input']`).val(__satuan_nama);
        _form_racikan.find(`input[name='harga_kecil']`).val(__hargajual);

        _form_racikan.find('.satuan_kecil_racikan_text').html(__satuan_nama);
        _form_racikan.find('.stok_racikan_text').html(__satuan_nama);
    });

    $(document).on('input', "#form-racikan input[name='qty']", function(e){
        match        = (/(\d{0,9})[^.]*((?:\.\d{0,2})?)/g).exec(this.value.replace(/[^\d.]/g, ''));
        this.value   = match[1] + match[2];
        var _qty = $(this).val();
        var nilai_konversi = _form_racikan.find(`input[name='nilai_konversi']`).val();
        var konversi = _qty * nilai_konversi;
        $("#qty_konversi_racikan").val(docoHelper.convertToRupiah(konversi));
    });

    function validateFormAddRacikan() {
        var _form_data        = _form_racikan.serializeArray();
        var _r_ke             = _form_racikan.find(`input[name="r_ke"]`).val();
        var _signa_val        = _form_racikan.find(`#signa_racikan`).val();
        var _kronis           = _form_racikan.find(`#racikan_kronis`).val();
        var _signa            = _form_racikan.find(`input[name="signa_hidden"]`).val(_signa_val);
        var _catatan          = _form_racikan.find(`input[name="catatan"]`).val();
        var submit_btn        = _form_racikan.find('#btn-racikan');
        var _satuan_racikan_obat_val    = _form_racikan.find(`#satuan_racikan_id`).val();
        var _satuan_racikan_obat        = _form_racikan.find(`input[name="satuan_racikan_hidden"]`).val(_satuan_racikan_obat_val);
        var _qty_racikan                = _form_racikan.find(`input[name="qty_racikan"]`).val();

        var _obat             = _form_racikan.find(`input[name="obatalkes_id"]`).val();
        var _satuan_racikan   = _form_racikan.find(`#satuan_racikan`).val();
        var _stok             = _form_racikan.find(`input[name="stok"]`).val();
        var _qty              = _form_racikan.find(`input[name="qty"]`).val();
        var _qty_konversi     = _form_racikan.find(`input[name="qty_konversi"]`).val();
        var _nilai_konversi   = _form_racikan.find(`input[name="nilai_konversi"]`).val();

        var _stok_tersedia    = _form_racikan.find(".qty_tersedia").html();
        var button_text       = "<i class='fa fa-plus'></i> " + i18next.t('Tambah ke Racikan');

        // if(_form_racikan.find(`#select_obat_racikan`).val() == "") {
        //     docoNotification("warning", i18next.t("Perhatian"), i18next.t("Nama Obat Alkes tidak boleh kosong"));
        //     ajaxAfterLoading(submit_btn, button_text);
        //     return false;
        // }

        if(_form_racikan.find(`#select_obat_racikan`).val() == "") {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Nama Obat Alkes tidak boleh kosong"));
            ajaxAfterLoading(submit_btn, button_text);
            return false;
        }

        if (_satuan_racikan == "") {
          docoNotification(
            "warning",
            i18next.t("Perhatian"),
            i18next.t("Satuan tidak boleh kosong")
          );
          ajaxAfterLoading(submit_btn, button_text);
          return false;
        }

        if ((parseFloat(_qty) > parseFloat(_stok_tersedia)) && !transaksi_obat_minus_racikan) {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Stok tidak mencukupi"));
            ajaxAfterLoading(submit_btn, button_text);
            return false;
        }

        if (_r_ke == '') {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("R ke tidak boleh kosong"));
            ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambah'));
            return false;
        }

        if (_obat == '') {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Obat tidak boleh kosong"));
            ajaxAfterLoading(submit_btn, button_text);
            return false;
        }

        if (_qty == '' || _qty < 0) {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Jumlah tidak boleh kurang dari 0"));
            ajaxAfterLoading(submit_btn, button_text);
            return false;
        }

        if (_signa_val == '' || _signa_val == undefined) {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Signa Belum Dipilih"));
            ajaxAfterLoading(submit_btn, button_text);
            return false;
        }

        if (_nilai_konversi == "") {
          docoNotification(
            "warning",
            i18next.t("Perhatian"),
            i18next.t("Konversi Belum Dipilih")
          );
          ajaxAfterLoading(submit_btn, button_text);
          return false;
        }

        if (_satuan_racikan_obat_val == "" || _satuan_racikan_obat_val == undefined) {
            docoNotification(
                "warning",
                i18next.t("Perhatian"),
                i18next.t("Satuan Racikan Obat Belum Dipilih")
            );
            
            ajaxAfterLoading(submit_btn, button_text);
            
            return false;
        }
        if (_qty_racikan == "" || _qty_racikan < 0) {
            docoNotification(
                "warning",
                i18next.t("Perhatian"),
                i18next.t("Jumlah Racikan tidak boleh kurang dari 0")
            );
            
            ajaxAfterLoading(submit_btn, button_text);
            
            return false;
        }

        return true;
    }

    function validateExistingObat(tempData, _nama_obat) {
        var status            = true;
        var _r_ke             = _form_racikan.find(`input[name="r_ke"]`).val();
        var submit_btn        = _form_racikan.find('#btn-racikan');
        var button_text       = "<i class='fa fa-plus'></i> " + i18next.t('Tambah ke Racikan');

        // cek validasi jika obat sudah pernah diinputkan di tabel cache racikan
        $.each(tempObatRacikan, function(index, value){
            if(tempData['identifier'] == value.identifier && !value.is_deleted) {
                status = false;
                docoNotification("warning", i18next.t("Perhatian"), i18next.t(`Obat ${_nama_obat} sudah ada di racikan ke-${_r_ke}`));
                ajaxAfterLoading(submit_btn, button_text);
                return false;
            }
        });

        // cek validasi jika obat sudah pernah diinputkan di tabel cache obat
        $.each(transObat, function(index, value) {
            if(value.racikan_id == 1) {
                if(value.r_ke == tempData['r_ke'] && value.obatalkes_id == tempData['obatalkes_id'] && !value.is_deleted) {
                    status = false;
                    docoNotification("warning", i18next.t("Perhatian"), i18next.t(`Obat ${_nama_obat} sudah ada di racikan ke-${_r_ke}`));
                    ajaxAfterLoading(submit_btn, button_text);
                    return false;
                }

                if(value.r_ke == tempData['r_ke'] && !value.is_deleted) {
                    var signa = parseInt(tempData['signa'])
                    if(value.signa_id != null && value.signa_id != signa){
                        status = false;
                        docoNotification("warning", i18next.t("Perhatian"), i18next.t(`Signa racikan ke-${_r_ke} harus sama`));
                        ajaxAfterLoading(submit_btn, button_text);
                        return false;
                    } else if(value.signa_id == null && value.signa.trim() != tempData['signa_nama'].trim()) {
                        status = false;
                        docoNotification("warning", i18next.t("Perhatian"), i18next.t(`Signa racikan ke-${_r_ke} harus sama`));
                        ajaxAfterLoading(submit_btn, button_text);
                        return false;
                    }
                }

                if(value.r_ke == tempData['r_ke'] && value.nama_racikan != tempData['nama_racikan'] && !value.is_deleted) {
                    status = false;
                    docoNotification("warning", i18next.t("Perhatian"), i18next.t(`Nama Racikan racikan ke-${_r_ke} harus sama`));
                    ajaxAfterLoading(submit_btn, button_text);
                    return false;
                }
                if(value.r_ke == tempData['r_ke'] && value.qty_racikan != tempData['qty_racikan'] && !value.is_deleted) {
                    status = false;
                    docoNotification("warning", i18next.t("Perhatian"), i18next.t(`Jumlah Racikan racikan ke-${_r_ke} harus sama`));
                    ajaxAfterLoading(submit_btn, button_text);
                    return false;
                }
                if(value.r_ke == tempData['r_ke'] && value.satuan_racikan_id != tempData['satuan_racikan_id'] && !value.is_deleted) {
                    status = false;
                    docoNotification("warning", i18next.t("Perhatian"), i18next.t(`Satuan Racikan racikan ke-${_r_ke} harus sama`));
                    ajaxAfterLoading(submit_btn, button_text);
                    return false;
                }
            }
        });

        if(!status) {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t(`Obat ${_nama_obat} sudah ada di racikan ke-${_r_ke}`));
            ajaxAfterLoading(submit_btn, button_text);
            return false;
        }

        return true;
    }

    // Tambah obat ke racikan
    $(document).on('click', '#btn-racikan', function(e) {
        e.preventDefault();
        var _form_data        = _form_racikan.serializeArray();
        var _signaSelected    = _form_racikan.find("#signa_racikan option:selected");
        var _r_ke             = _form_racikan.find(`input[name="r_ke"]`).val();
        var _obat             = _form_racikan.find(`input[name="obatalkes_id"]`).val();
        var _kronis             = _form_racikan.find(".racikan_kronis").is(":checked");
        var _identifier       = `${_r_ke}-${_obat}`;
        var _nama_obat        = '';
        var button_text       = $(this).html();
        var is_signafreetext  = false;

        if(_signaSelected.data('select2Tag')){
            is_signafreetext = true;
        }

        if(!validateFormAddRacikan()) return false;

        let tempData = {};
        $.each(_form_data, function(key, val){
            if(val.name == 'signa_hidden') {
                val.name = 'signa';
            }

            if(val.name == 'obat_nama') {
                _nama_obat = val.value;
            }

            if (val.name == 'satuan_racikan_hidden') {
                val.name = 'satuan_racikan_id';
            }

            tempData[val.name] = val.value;
        });

        tempData['identifier'] = _identifier;
        tempData['kronis'] = _kronis;
        tempData['is_signafreetext'] = is_signafreetext;

        if(!validateExistingObat(tempData, _nama_obat)) return false;

        // readonly R ke & signa
        _form_racikan.find(`input[name="r_ke"]`).attr('readonly', true);
        _form_racikan.find(`#signa_racikan`).attr('disabled', 'true');

        _form_racikan.find(`input[name="nama_racikan"]`).attr("readonly", true);
        _form_racikan.find(`input[name="qty_racikan"]`).attr("readonly", true);
        _form_racikan.find(`#satuan_racikan_id`).attr("disabled", "true");
        _form_racikan.find(`#racikan_kronis`).attr("disabled", "true");

        // reset form obat
        resetFormObat();
        
        if(is_signafreetext) {
            var selected_signa = list_signa.find(item => item.signa_id == tempData.signa)
            if(selected_signa != undefined) {
                tempData.signa_nama = selected_signa.signa_nama
            }
        }

        tempObatRacikan.push(tempData);
        drawTempRacikan();
    });

    $(document).on('click', '#list-racikan-temp tr .btn-danger', function(e){
        e.preventDefault();
        tempObatRacikan.splice($(this).data('id'), 1);
        drawTempRacikan();
    });

    $(document).on('click', '#form-racikan .btn-ulang', function(e){
        e.preventDefault();
        tempObatRacikan = [];
        drawTempRacikan();
        resetFormRacikanKe();
    });

    function resetFormObat() {
        // _form_racikan.find(`#select_obat_racikan`).val(null).trigger('change');
        _form_racikan.find(`#select_obat_racikan`).val(null).trigger('change');
        document.getElementById("satuan_racikan").options.length = 0;
        _form_racikan.find(`#satuan_racikan`).attr('disabled', 'true');
        _form_racikan.find(`input[name="harga_konversi"]`).val(null);
        _form_racikan.find(`input[name="qty"]`).val(null);
        _form_racikan.find(`.qty_tersedia`).html("-");
        _form_racikan.find(`.satuan`).html("");
        _form_racikan.find(`input[name="qty_konversi"]`).val(null);
    }

    function resetFormRacikanKe() {
        _form_racikan.find(`input[name="r_ke"]`).val(null);
        _form_racikan.find(`input[name="r_ke"]`).attr('readonly', false);
        _form_racikan.find(`#signa_racikan`).attr('disabled', false);
        _form_racikan.find(`#racikan_kronis`).attr('disabled', false);
        _form_racikan.find(`#racikan_kronis`).prop("checked", false);
        _form_racikan.find(`#signa_racikan`).val(null).trigger('change');
        _form_racikan.find(`input[name="catatan"]`).val(null);
        _form_racikan.find(`input[name="nama_racikan"]`).val(null);
        _form_racikan.find(`input[name="nama_racikan"]`).val(null);
        _form_racikan.find(`input[name="nama_racikan"]`).attr("readonly", false);
        _form_racikan.find(`#satuan_racikan_id`).attr("disabled", false);
        _form_racikan.find(`#satuan_racikan_id`).val(null).trigger("change");
        _form_racikan.find(`input[name="qty_racikan"]`).val(null);
        _form_racikan.find(`input[name="qty_racikan"]`).attr("readonly", false);
    }

    function drawTempRacikan() {
        var _list_racik = $("#list-racikan-temp");
        _list_racik.html("");

        var _no = 0;
        var _html = "";
        $.each(tempObatRacikan, function(index, value){
            _no++;
            _html += `
            <tr data-index="` + index + `">
                <td>` + _no + `</td>
                <td>` + value.obat_nama + `</td>
                <td align='right'>` + value.harga_konversi + `</td>
                <td>` + value.qty + `</td>
                <td>` + value.satuan_input + `</td>
                <td><button class="btn btn-xs btn-danger" data-id=` + index + ` ><i class="fa fa-trash"></i></button></td>
            </tr>`
        });

        if (_no <= 0) {
            _html += `
            <tr>
                <td colspan="5" class="text-center">Tidak ada Data</td>
            </tr>`
        }

       _list_racik.prepend(_html);
    }

    $("#signa_racikan").on('change', function(e) {
        var signanama = $("#signa_racikan option:selected").text();
        var signa_val = _form_racikan.find(`#signa_racikan`).val();
        _form_racikan.find(`input[name="signa_nama"]`).val(signanama);
        _form_racikan.find(`input[name="signa_hidden"]`).val(signa_val);
    });

    $("#satuan_racikan_id").on("change", function (e) {
        var satuanracikanobat = $("#satuan_racikan_id option:selected").text();
        var satuanracikanobat_val = _form_racikan.find(`#satuan_racikan_id`).val();
        _form_racikan.find(`input[name="satuan_racikan_nama"]`).val(satuanracikanobat);
        _form_racikan.find(`input[name="satuan_racikan_hidden"]`).val(satuanracikanobat_val);
    });

    $(document).on('click', "#btn-tambah-racikan", function(e) {
        e.preventDefault();
        var submit_btn = $(this);
        if (tempObatRacikan.length <= 0) {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Belum ada obat di racikan"));
            ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambahkan Racikan'));
            return false;
        }

        $.ajax({
            url: "/apotek/transaksi-resep/save-multiple-cache-edit?cacheKey=" + cache_key,
            type: "post",
            data:  {
                data: tempObatRacikan
            },
            beforeSend: function() {
                ajaxLoading(submit_btn);
            },
            success: function(response) {
                docoNotification("success", i18next.t("Berhasil"), i18next.t("Data berhasil di tambah"));
                var value = response.data;
                transObat = value;
                appendObat(value);

                urutObatRs = urutObatRs + 1;
                tempObatRacikan = [];
                drawTempRacikan();
                resetFormRacikanKe();
                resetFormObat();

                return true;
            },
            error: function (response) {
                ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambah'));
                var resMessage = response.responseJSON.message;
                docoNotification("error", i18next.t("Perhatian"), i18next.t(resMessage));
                return false;
            },
            complete: function (response) {
                ajaxAfterLoading(submit_btn, "<i class='fa fa-plus'></i> " + i18next.t('Tambahkan Racikan'));
            }
        });
    });

    // Tambah Obat Non Racikan
    $(document).on('click', '#tambah-obat', function(event) {
        event.preventDefault();
        var data            = $("#form-obat").serialize();
        var form            = $("#form-obat");
        var button_text     = $(this).html();
        var submit_btn      = form.find(".add");
        var _r_ke           = form.find('.r_ke').val();
        var _racikan_id     = form.find(".racikan_id").is(":checked");
        var _id_obat        = form.find('.id_obat').val();
        var _cekObatR       = `${_r_ke}-${_id_obat}-${_racikan_id}`;
        var _stok           = form.find('.stok').val();
        var _signa          = form.find('.signaid').val();

        var _qty            = form.find('.qty').val();
        var _qty_konversi   = form.find('.qty_konversi').val();
        var _nilai_konversi = form.find('.nilai_konversi').val();
        var id_barang_list  = form.find('.list_barang').get();
        var kronis          = form.find(".kronis").is(":checked");

        var _stoksisa       = form.find('.qty_tersedia').html();
        var invalid         = false;

        if (parseFloat(_qty) > parseFloat(_stoksisa) && !transaksi_obat_minus) {
                docoNotification("warning", i18next.t("Perhatian"), i18next.t("Stok tidak mencukupi"));
                ajaxAfterLoading(submit_btn, button_text);
                return false;
        }

        if (_racikan_id) {
            if (_r_ke == '') {
                docoNotification("warning", i18next.t("Perhatian"), i18next.t("R ke tidak boleh kosong"));
                ajaxAfterLoading(submit_btn, button_text);
                return false;
            }
        }

        if (_id_obat == '') {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Obat tidak boleh kosong"));
            ajaxAfterLoading(submit_btn, button_text);
            return false;
        }

        if (_qty == '' || _qty < 0) {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Jumlah tidak boleh kurang dari 0"));
            ajaxAfterLoading(submit_btn, button_text);
            return false;
        }

        if (_qty_konversi == '' || _qty < 0) {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Jumlah tidak boleh kurang dari 0"));
            ajaxAfterLoading(submit_btn, button_text);
            return false;
        }

        if (_nilai_konversi == '') {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Konversi Belum Dipilih"));
            ajaxAfterLoading(submit_btn, button_text);
            return false;
        }

        if (_signa == '' || _signa == undefined) {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Signa Belum Dipilih"));
            ajaxAfterLoading(submit_btn, button_text);
            return false;
        }

        $.each(transObat, function(index, value) {
            if(value.obatalkes_id == _id_obat && !value.is_racikan && !value.is_deleted) {
                invalid = true;
                return false;
            } else {
                invalid = false;
            }
        });

        if(invalid) {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t(`Obat sudah diinputkan!`));
            ajaxAfterLoading(submit_btn, button_text);
            return false;
        }

        for (var i = 0; i < id_barang_list.length; i++) {
            var cek_obat = $(id_barang_list[i]).data('id');

            if (_cekObatR == cek_obat) {
                invalid = true;
            }
        }

        if(!invalid){
            $.ajax({
                url: "/apotek/transaksi-resep/save-cache-edit?cacheKey="+cache_key,
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
                    transObat = value;
                    appendObat(value);

                    urutObatRs = urutObatRs + 1;
                    $(".r_ke").val("");
                    $(".r_ke").prop("disabled", true);
                    $(".racikan_id").prop("checked", false);
                    $(".kronis").prop("checked", false);
                    $(".autoObat").val(null).trigger("change");
                    $("#select_obat_non_racikan").val(null).trigger("change");
                    $(".signaid").val(null).trigger("change");
                    $(".qty-obat").val("");
                    $(".qty").val("");
                    $(".id_obat").val(null).trigger("change");

                    $("#ampuls_id, .satuan_select").val(null).trigger("change");
                    $(".qty_tersedia").html("");
                    $(".harga_text").html("");
                    $(".stok_text").html("");
                    $(".harga_konversi").val("");
                    $(".catatan").val("");

                    return false;
                },
                error: function (res) {
                    ajaxAfterLoading(submit_btn, button_text);
                    var resMessage = res.responseJSON.message;
                    docoNotification("error", i18next.t("Perhatian"), i18next.t(resMessage));
                    return false;
                },
                complete: function() {
                    ajaxAfterLoading(submit_btn, button_text);
                }
            });
        }else{
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Nama Obat telah di input"));
            ajaxAfterLoading(submit_btn, button_text);
            return false;
        }
    });

    function appendObat(object, list_error = []) {
        var _no = 0;
        var _html = "";
        $(".default-value").attr("style", "display:none");
        $.each(object, function (x, y) {
            if (typeof object[x] !== "undefined" && object !== '') {
                var detMoreThanStock = parseInt(y.det) > parseInt(y.qty_tersedia) ? true : false;
                var additionalClass = detMoreThanStock ? ' det-more-than' : '';
                var out_of_stock = list_error.indexOf(parseInt(y.obatalkes_id)) > -1 ? "out-stock" : "";
                var is_deleted_row = y.is_deleted ? " strikeout" : "";
                var is_disabled = y.is_deleted ? " disabled" : "";
                det_arr[y.posisi] = (typeof det_arr[y.posisi] == 'undefined') ? (typeof y.det != 'undefined' ? y.det : y.qty) : det_arr[y.posisi];
                det_kronis[y.posisi] = (typeof det_kronis[y.posisi] == 'undefined') ? y.kronis : det_kronis[y.posisi];
                var qty_hitung = Math.ceil(det_arr[y.posisi]);
                var totalharga_netto = docoHelper.convertToAngka(y.harganetto) * qty_hitung;
                var subtotal_obat = y.harga * qty_hitung;
                var _det = det_arr[y.posisi] !== 'undefined' ? det_arr[y.posisi] : y.det;
                var nama_racikan = (y.nama_racikan == null || y.nama_racikan == "" || typeof y.nama_racikan == "undefined" ? "" : " - " + y.nama_racikan);
                var satuan_racikan_nama = (y.satuan_racikan_nama == null || y.satuan_racikan_nama == "" || typeof y.satuan_racikan_nama == "undefined" ? "" : " - " + y.satuan_racikan_nama);
                var qty_racikan = (y.qty_racikan == null || y.qty_racikan == "" || typeof y.qty_racikan == "undefined" ? "" : " - " + y.qty_racikan + " " + satuan_racikan_nama);
                if(det_kronis[y.posisi] == true || det_kronis[y.posisi] == "true"){
                    var checked = "checked";
                    var kronis = true;
                }else{
                    var checked = "";
                    var kronis = false;
                }
                det_kronis[y.posisi] = kronis

                if(y.r_ke == "-" || y.r_ke == null){
                    var jenis = "nonracikan";
                }else{
                    var jenis = "racikan"+y.r_ke;
                }
                
                _no++;
                _html += "<tr class='resep "+ out_of_stock + is_deleted_row +additionalClass+"'>";
                    _html += "<td style='display:none;'><input type='hidden' class='list_barang' data-id='" + y.r_ke + "-" + y.obatalkes_id + "-" + y.racikan_id + "' value='" + y.obatalkes_id + "'></td>";
                    _html += "<td class=\"numbering\">" + _no + "</td>";
                    _html += '<td colspan="2">' + ((y.r_ke == null) ? '-' : y.r_ke + nama_racikan + qty_racikan) + "</td>";
                    _html += "<td>" + y.obatalkes_nama + "</td>";
                    _html += "<td align=\"right\">" + docoHelper.convertToRupiah(y.harga) + "</td>";
                    if(y.is_deleted) {
                        _html += "<td class=\"signa\"><select disabled data-signa-"+y.posisi+"=\""+y.signa+"\" data-position=\"" + y.posisi + "\" class=\"selectSigna-" + y.posisi + " changeSigna doco form-control select2 docoSelect2SignaFormatOnly\" name=\"signa\" ></select></td>";
                    }else{
                        _html += "<td class=\"signa\"><select data-signa-"+y.posisi+"=\""+y.signa+"\" data-position=\"" + y.posisi + "\" class=\"selectSigna-" + y.posisi + " changeSigna form-control select2 docoSelect2SignaFormatOnly\" name=\"signa\" ></select></td>";
                    }
                    _html += "<td class=\"qty\" align=\"right\">" + y.qty + "</td>";

                    if(y.is_deleted) {
                        _html += `<td class="det" align="right">
                            <input
                                type="text"
                                class="form-control"
                                size="1"
                                value="`+ _det +`"
                                data-det="`+ y.posisi +`"
                                style="text-align: right;"
                                readonly
                            />
                            </td>`;
                    }
                    else {
                        _html += `<td class="det" align="right">
                            <input
                                type="text"
                                class="form-control"
                                size="1"
                                value="`+ _det +`"
                                data-det="`+ y.posisi +`"
                                style="text-align: right; margin-bottom:5%"
                            />
                            </td>`;
                    }
                    _html += "<td class=\"satuan\" align=\"left\">" + y.satuan_input + "</td>";
                    if(isEditReseptur || isEditResep){
                        _html += "<td class=\"satuan\" align=\"left\">" + y.etiket + "</td>";
                    } else {
                        _html += "<td class=\"satuan\" align=\"left\">" + y.catatan + "</td>";
                    }

                    if(y.is_deleted) {
                        _html += "<td align=\"right\" class=\"total_harga removed_row\" data-subindex=\""+y.posisi+"\" data-netto=\""+totalharga_netto+"\" data-sub=\"" + subtotal_obat + "\">" + docoHelper.convertToRupiah(subtotal_obat) + "</td>";
                        _html += "<td></td>";
                        _html += `<td class="kronis" align="right">
                            <input `+is_disabled+`
                                value = "true"
                                class ="hasil_kronis`+y.posisi+` `+jenis+`"
                                data-kronis="`+ y.posisi +`"
                                type="checkbox" `+ checked +`
                            />
                            </td>`;
                    } else {
                        _html += "<td align=\"right\" class=\"total_harga\" data-subindex=\""+y.posisi+"\" data-netto=\""+totalharga_netto+"\" data-sub=\"" + subtotal_obat + "\">" + docoHelper.convertToRupiah(subtotal_obat) + "</td>";
                        _html += "<td align=\"center\" class=\"stok_tersedia\">" + y.qty_tersedia + "</td>";
                        _html += `<td class="kronis" align="center">
                            <input `+is_disabled+`
                                class ="hasil_kronis`+y.posisi+` `+jenis+`"
                                data-kronis="`+ y.posisi +`"
                                type="checkbox" `+ checked +`
                            />
                            </td>`;
                        if(isEditReseptur){
                            _html += "<td><a class=\"btn btn-xs btn-danger deleted\" data-rdId=\"" + y.resepturdetail_id + "\" data-id=\"" + y.posisi + "\"><i class=\"fa fa-trash\"></i></a></td>";
                        } else if(isEditResep) {
                            _html += "<td><a class=\"btn btn-xs btn-danger deleted\" data-rdId=\"" + y.obatalkespasien_id + "\" data-id=\"" + y.posisi + "\"><i class=\"fa fa-trash\"></i></a></td>";
                        }
                    }
                _html += "</tr>";
            }

            $(document).ready(function () {
                 $(".hasil_kronis" + y.posisi).on('click', function (e) {
                    if(y.r_ke == "-" || y.r_ke == null){
                        det_kronis[y.posisi] = $(".hasil_kronis" + y.posisi).is(':checked');
                    }else{
                        $(".racikan" + y.r_ke).each(function(index, val){
                            det_kronis[$(this).attr("data-kronis")] = $(".hasil_kronis" + y.posisi).is(':checked');
                        })
                    }

                    e.preventDefault();
                    $.showQuestionDialog(header, message, label, function(reaction) {
                        if (reaction == 'Yes') {
                            if(y.r_ke == "-" || y.r_ke == null){
                                if($(".hasil_kronis" + y.posisi).is(':checked') == false){
                                    $(".hasil_kronis" + y.posisi).prop("checked", true);
                                }else{
                                    $(".hasil_kronis" + y.posisi).prop("checked", false);
                                }
                            }else{
                                if($(".racikan" + y.r_ke).is(':checked') == false){
                                    $(".racikan" + y.r_ke).prop("checked", true);
                                }else{
                                    $(".racikan" + y.r_ke).prop("checked", false);
                                }
                            }
                        }
                    });
                })

                var signaList = [];
                $(".selectSigna-" + y.posisi).select2InfinityScroll({
                    url: "/apotek/transaksi-resep/source-data",
                    callbackData: (params) => {
                        // auto-select selected value
                        if(typeof(params.term) == "undefined" && params.term == null) {
                            let term = $(".selectSigna-" + y.posisi).attr("data-signa-"+y.posisi);
                            $(".select2-search__field").val(null)
                            $(".select2-search__field").val(term)
                            params.term = term;
                        }
                        return {
                            payload: {
                               ...params,
                            }
                        }
                    },
                    callbackProccess: (data) => {
                        let resultProccess = {
                            pagination: data.pagination,
                            results: []
                        }
                        data.results.map((itemData) => {
                            resultProccess.results.push({
                                id: itemData.signa_id,
                                text: itemData.signa_kode +" "+ itemData.signa_nama,
                                ...itemData,
                                // disable: itemData.status == 1
                            })
                        })
                        signaList = resultProccess;
                        return resultProccess
                    }
                })

                var _signaName = (y.signa_id != null) ? y.signa_id : y.signa;
                var _options = new Option(y.signa, _signaName, false, false);
                $('.selectSigna-' + y.posisi).append(_options).trigger({
                    type: 'select2:select',
                    params: {
                        data: _signaName
                    }
                });

                $(".selectSigna-" + y.posisi).on("change", function(){
                    $.each(signaList.results, function(index, value) {
                        if(value.id == $(".selectSigna-" + y.posisi).val()) {
                            $(".selectSigna-" + y.posisi).attr("data-signa-"+y.posisi, value.signa_nama)
                        }
                    });
                });
            })

            object[x] = y;
            $(document).on("input", "[data-det=" + y.posisi + "]", function (e) {
                match = (/(\d{0,9})[^.]*((?:\.\d{0,2})?)/g).exec(this.value.replace(/[^\d.]/g, ''));
                this.value = match[1] + match[2];
            })


            
            $(document).on("change", "[data-det="+ y.posisi +"]", function(e){
                // validasi jumlah objek dengan jumlah item det pada array
                // if(object.length == det_arr.length) {
                    match        = (/(\d{0,9})[^.]*((?:\.\d{0,2})?)/g).exec(this.value.replace(/[^\d.]/g, ''));
                    this.value   = match[1] + match[2];

                    var det_value = parseFloat($(this).val());
                    var qty_value = parseFloat(y.qty);

                    // validasi field DET
                    if(det_value == ""){
                        this.value = parseFloat(0);
                    }

                    if(det_value > qty_value) {
                        this.value = qty_value;
                        det_value = qty_value;
                    }

                    // update det value
                    det_arr[y.posisi] = det_value.toString();

                    // kalkulasi ulang subtotal
                    var subtotal_hargajual = parseFloat(y.harga) * Math.ceil(det_value);
                    var subtotal_harganetto = parseFloat(y.harganetto) * Math.ceil(det_value);
                    subtotal_hargajual = parseFloat(subtotal_hargajual).toFixed(2);
                    subtotal_harganetto = parseFloat(subtotal_harganetto).toFixed(2);
                    if(subtotal_hargajual == NaN || Number.isNaN(subtotal_hargajual)){
                        subtotal_hargajual = 0;
                    }
                    if(subtotal_harganetto == NaN || Number.isNaN(subtotal_harganetto)){
                        subtotal_harganetto = 0;
                    }
                    $("[data-subindex="+ y.posisi +"]").text(docoHelper.convertToRupiah(subtotal_hargajual));
                    $("[data-subindex="+ y.posisi +"]").attr("data-sub", subtotal_hargajual);
                    $("[data-subindex="+ y.posisi +"]").attr("data-netto", subtotal_harganetto);
                    y.subtotal = subtotal_hargajual;
                    totalharga_netto = subtotal_harganetto;
                    sumHarga();
                // }
            });
        });

        if (_html === "") {
            _html += "<tr>";
            _html += "<td colspan=\"9\" id=\"data-null\" class=\"text-center\">Data Tidak Ditemukan</td>";
            _html += "</tr>";
        }
        $("#list-obat").html("");
        $("#list-obat").prepend(_html);
        sumHarga();
    }

    function ajaxLoading(element) {
        $(element).attr("disabled", true);
        $(element).html("<i class=\"fa fa-spinner fa-pulse fa-1x fa-fw\"></i>");
    }

    function ajaxAfterLoading(element, text) {
        $(element).attr('disabled', false);
        $(element).html(text);
    }

    function sumHarga(){
        var sum_subtotalItem = 0;
        var biaya_admin = docoHelper.convertToAngka($(".biayaAdmin").val());
        if(biaya_admin == 0 || isNaN(biaya_admin)) {
            biaya_admin = 0;
        }

        $.each($(".total_harga").not(".removed_row"), function () {
            var value = parseFloat($(this).attr("data-sub"));
            var netto = parseFloat($(this).attr("data-netto"));
            sum_subtotalItem += value;
            sum_subtotalNetto += netto;
        });
        $(".subTotalItem").val(sum_subtotalItem);
        $(".subtotal").html(docoHelper.convertToRupiah(sum_subtotalItem));
        $(".total").html(docoHelper.convertToRupiah(sum_subtotalItem + biaya_admin));
    }

    $('.biayaAdmin').on('input', function() {
        var sub_total = $(".subTotalItem").val();
        var biaya_admin = $(this).val();
        if(biaya_admin == 0 || isNaN(biaya_admin)) {
            biaya_admin = 0;
        }
        var total = docoHelper.convertToAngka(biaya_admin) + parseFloat(sub_total);
        $(".total").html(docoHelper.convertToRupiah(total));
    });

    $('.signaid').on('change', function(){
        var _txt = $('.signaid option:selected').text();
        $('.signanama').val(_txt);
    })

    /*improvement save with shortcut alt+s*/
    $(document).on('keydown', null, 'alt+s', function (event) {
        if(isEditReseptur) {
            $("#update-reseptur").click();
        } else if(isEditResep){
            $("#update-resep").click();
        } else {
            $("#save-pasien").click();
        }
    });

    // Simpan
    $("#update-reseptur").on("click", function (e) {
        e.preventDefault();

        var _url = "/apotek/transaksi-resep/save-edit-reseptur?id="+id;
        $(this).docoForm("click", {
            url: _url,
            data: {
                totalharga_jual: $(".subTotalItem").val(),
                totalharga_netto: sum_subtotalNetto,
                keterangan: $("#keterangan").val(),
                kronis: det_kronis,
                biayaadministrasi: docoHelper.convertToAngka($(".biayaAdmin").val()),
                det: det_arr,
                cacheKey: cache_key
            },
            success: function(data) {
                onUpdateSuccess();
            },
            error: function(response) {
                var respon = response.responseJSON.message;
                if(typeof respon != undefined) {
                    docoNotification('error', 'Proses Gagal', respon.response.message);
                    if (typeof respon.response.data != "undefined") {
                      var _responseData = respon.response.data;
                      if (
                        _responseData.status &&
                        _responseData.status == "sudah_diserahkan"
                      ) {
                        setTimeout(function () {
                          window.location.href = "/apotek/informasi-reseptur/#";
                        }, 3000);
                      }
                    }
                } else {
                    docoNotification('error', 'Proses Gagal', 'Terjadi Kesalahan');
                }
            }
        })
    });

    $("#update-resep").on("click", function (e) {
        e.preventDefault();

        var _url = "/apotek/transaksi-resep/save-edit-resep?id="+id;
        $(this).docoForm("click", {
            url: _url,
            data: {
                totalharga_jual: $(".subTotalItem").val(),
                totalharga_netto: sum_subtotalNetto,
                keterangan: $("#keterangan").val(),
                kronis: det_kronis,
                biayaadministrasi: docoHelper.convertToAngka($(".biayaAdmin").val()),
                det: det_arr,
                cacheKey: cache_key
            },
            success: function(data) {
                onUpdateSuccess();
            },
            error: function(response) {
                var respon = response.responseJSON;
                var res_message;

                if (respon.message.response.message) {
                    res_message = respon.message.response.message;
                } else if (respon.message) {
                    res_message = respon.message;
                } else if (respon.response.message) {
                    res_message = respon.response.message;
                }

                if (res_message) {
                    docoNotification('error', 'Proses Gagal', res_message);
                    if (typeof respon.message.response.data != "undefined") {
                     var _responseData = respon.message.response.data;
                      if (
                        _responseData.status &&
                        _responseData.status == "sudah_diserahkan"
                      ) {
                        setTimeout(function () {
                          window.location.href = "/apotek/informasi-reseptur/#";
                        }, 3000);
                      }
                    }
                } else {
                    docoNotification('error', 'Proses Gagal', 'Terjadi Kesalahan');
                }
            }
        })
    });

    function onUpdateSuccess() {
        setTimeout(function(){
            $("#update-reseptur, #update-resep, #ulang, #tambah-obat").attr("disabled", "disabled");
            $("#deleted").css("display","none");
            $(".deleted").css("display","none");
            $("#form-obat :input").prop("disabled", "disabled");
        },500);

        (new PNotify({
            title: "Berhasil",
            text: "Reseptur berhasil di update!",
            addclass: "alert alert-success alert-arrow-right alert-styled-right",
            type: "success",
            buttons: {
                closer: false,
                sticker: false
            },
            hide: false,
            confirm: {
                confirm: false
            },
            history: {
                history: false
            }
        }));

        setTimeout(function(){
            window.location.reload();
        }, 3000);
    }
    $(document).on("change", ".changeSigna", function (event) {
        event.preventDefault();
        var _signaTNama = $(this).find('option:selected').text();
        var _signaId = $(this).val();
        var _position = $(this).data('position');
        $.ajax({
            url: "/apotek/transaksi-resep/update-cache-edit",
            type: "post",
            data:  {
                signa_id: _signaId,
                signa_nama: _signaTNama,
                position: _position,
                cacheKey: cache_key
            },

            success: function(response) {
                return true;
            },
            error: function (response) {
                return false;
            },
        });

    });

    // Hapus Obat
    $(document).on("click", ".deleted", function (event) {
        event.preventDefault();
        var id_ = $(this).data("id");
        var button = this;
        var resepturdetail_id = $(this).data("rdid");
        if(isEditReseptur) {
            var ResData = {
                "id" : id_,
                "type" : type,
                "resepturdetail_id" : resepturdetail_id,
                "cacheKey" : cache_key
            };
        } else if(isEditResep) {
            var ResData = {
                "id" : id_,
                "type" : type,
                "obatalkespasien_id" : resepturdetail_id,
                "cacheKey" : cache_key
            };
        }

        $(this).docoForm('click',{
            url: "/apotek/transaksi-resep/mark-deleted",
            confirmTitle: i18next.t("Konfirmasi"),
            confirmMessage: i18next.t("Apa anda yakin ingin membatalkan data ini?"),
            data: ResData,
            method: "GET",
            before: function () {
                $(button).html("<i class=\"fa fa-spin fa-spinner\"></i>");
                $(button).prop("disabled", true);
            },
            success: function (data) {
                transObat = data.detail;
                // $(button).closest("tr").toggleClass("deleted-row");
                $(button).closest("tr").toggleClass("strikeout");
                $(button).closest("input").prop("disabled", true);
                $(button).closest("tr").find("input[type='checkbox']").attr("disabled",true)
                $(button).css("display", "none");
                appendObat(transObat);
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

    var header = 'Perhatian !';
    var message = 'Apakah anda yakin akan mengubah data obat Kronis ?';
    var label = {
        buttons: {
            'Yes': 'button-yes',
            'No': 'button-no'
        }
    };
    $('#racikan_kronis').on('click', function (event) {
        event.preventDefault();
        $.showQuestionDialog(header, message, label, function(reaction) {
            if (reaction == 'Yes') {
                if($("#racikan_kronis").is(':checked') == false){
                    $("#racikan_kronis").prop("checked", true);
                }else{
                    $("#racikan_kronis").prop("checked", false);

                }
            }
        });
    });

    $('#kronis').on('click', function (event) {
        event.preventDefault();
        $.showQuestionDialog(header, message, label, function(reaction) {
            if (reaction == 'Yes') {
                if($("#kronis").is(':checked') == false){
                    $("#kronis").prop("checked", true);
                }else{
                    $("#kronis").prop("checked", false);

                }
            }
        });
    });
});
