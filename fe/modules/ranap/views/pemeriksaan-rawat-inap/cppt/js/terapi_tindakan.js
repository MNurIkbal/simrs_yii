var listObat = [];

function clock() {
    var d = new Date();
    var hour = checkTime(d.getHours());
    var min = checkTime(d.getMinutes());
    var sec = checkTime(d.getSeconds());
    var ampm = (hour >= 12) ? 'PM' : 'AM';
    var currentTime = hour +":"+ min +":"+ sec;
    var getmont = parseFloat(d.getMonth()) +1;
    var month = checkTime(getmont);

    // set time
    // document.getElementById ("tgl_tindakan").innerHTML = getCurrentDate() + '  ' + currentTime;
    $('#instruksitindakanform-tgl_tindakan').val(  d.getFullYear()+"-"+month+"-"+d.getDate()+" "+hour+":"+min+":"+sec);
}
function checkTime(i) {
    if (i < 10) {i = "0" + i;}  // add zero in front of numbers < 10
    return i;
}

setInterval(clock, 1000);

function update_daftar_tindakan()
{
    $("#tindakan_obatalkes option").remove();

    var data = {
        id: "0",
        text: "-- Pilih tindakan --"
    };

    // Option untuk di append ke daftar tindakan di bmhp
    options = new Option(data.text, data.id, false, false);

    $('#tindakan_obatalkes').append(options);
    $('.tabel-tindakan > tbody > tr').not("tr.first-class, tr.detailed-paket").not($('tr.strikeout')).each(function() {
        // Get id
        var tindakanId = $(this).find("input.daftartindakan_id").val();
        var dokterId = $(this).find("input.dokterdpjp_id").val();
        var perawat1Id =  $(this).find("input.perawat1_id").val();
        var perawat2Id =  $(this).find("input.perawat2_id").val();
        var isCytoId =  $(this).find("input.is_cyto").val();

        var id_instruksi_tindakan = 0;
        if($(this).data('id_instruksi_tindakan') != undefined){
            if($(this).data('id_instruksi_tindakan') != '0' || $(this).data('id_instruksi_tindakan') != ''){
                id_instruksi_tindakan = $(this).data('id_instruksi_tindakan');
            }else{
                id_instruksi_tindakan = tindakanId+'-'+dokterId+'-'+perawat1Id+'-'+perawat2Id+'-'+isCytoId;
            }
        }else{
            id_instruksi_tindakan = tindakanId+'-'+dokterId+'-'+perawat1Id+'-'+perawat2Id+'-'+isCytoId;
        }
        var tindakanNama = $(this).find("td.daftartindakan-nama").text();
        var dokterNama = $(this).find("td.dokterdpjp-nama").text();

        
        var data = {
                id: id_instruksi_tindakan,
                text: tindakanNama+" - "+dokterNama
            };
        options = new Option(data.text, data.id, false, false);

        $('#tindakan_obatalkes').append(options);
    });
}

var _listTindakanPaket;
var resObat = []
var resAlkes = []

$(document).ready(function() {
    let depo = $('#select_depo_bmhp').select2('data')
    depo.push({ id: $('#ruangan_id').val(), text: ruanganNama})
    $('#select_depo_bmhp').select2({
        placeholder: '--- Pilih --- ',
        data: depo
    });
    update_daftar_tindakan();
    $('.select-2').select2();
    $('#tagih_pasien[value=1]').prop("checked",true);
    $('.jenis_pemakaian[value=619]').prop("checked",true).trigger('change')
    $('[data-toggle="popover"]').popover({
        container: 'body'
    });
    
    // $('.field-instruksitindakanform-tarif_cyto').hide();
    // Save soap
    $("#save-terapi-tindakan").on("click", function(event) {
        // Prevent default
        $('.form-group').removeClass('has-error')
        $('.help-block.error').remove()
        event.preventDefault();
        var catatan = $('#instruksiform-catatan_instruksi').val();
        if (catatan == null || catatan == "") {
            docoNotification("error", "Catatan Belum Diisi!", "mohon isi dulu catatan!");
            var logo =
                    '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> &nbsp'
            var _field_catatan = $('#instruksiform-catatan_instruksi')
                    _field_catatan.parent('div').addClass('has-error')
                    _field_catatan.parent('.required').addClass('has-error')
                    _field_catatan.after(
                        '<span class="help-block error">' +
                            logo +
                            'Catatan Harus Diisi' +
                            '</span>'
                    )
            return false
        }
        var jmltindakan = 0;
        var jmlbmhp = 0;
        $(".tabel-tindakan").find("tbody").find("tr").not("tr.first-class, tr.strikeout").each(function(){
            jmltindakan += 1;
        });
        $(".tabel-bmhp").find("tbody").find("tr").not("tr.first-class, tr.strikeout").each(function(){
            jmlbmhp += 1;
        });

        // var stokObat = [];
        // if (resObat.length > 0) {
        //     resObat.forEach((val, key) => {
        //         stokObat[val.obatalkes_id] = val
        //     })
        // }
        // if (resAlkes.length > 0) {
        //     resAlkes.forEach((val, key) => {
        //         stokObat[val.obatalkes_id] = val
        //     })
        // }

        console.log(listObat);

        var _validationStok = false
        var _tmpValidObat = []
        var _namaObat = null
        $('.tabel-bmhp > tbody > tr').each(function(){
            var _obatAlkes = $(this).find('input.obatalkes_id').val()
            var _qtyObat = $(this).find('input.qty').val()
            if (typeof _qtyObat !== 'undefined') {
                _qtyObat = parseFloat(_qtyObat)
                if (typeof _tmpValidObat[_obatAlkes] === 'undefined') {
                    _tmpValidObat[_obatAlkes] = 0
                }
                _tmpValidObat[_obatAlkes] += _qtyObat
                if (typeof listObat[_obatAlkes] !== 'undefined') {
                    var _currentStok = listObat[_obatAlkes].qty_tersedia
                    if (_currentStok <= _tmpValidObat[_obatAlkes]) {
                        _namaObat = listObat[_obatAlkes].obatalkes_namalain
                        _validationStok = true
                        return false
                    }
                } else {
                    _namaObat = $(this).find("td:eq(3)").text()
                    _validationStok = true
                    return false
                }
            }
        })


        if (_validationStok) {
            docoNotification('error', 'Proses Gagal!', `Stok Obat ${_namaObat} tidak mencukupi`)
            return false;
        }


        if(jmltindakan == 0 && jmlbmhp == 0){
            docoNotification('warning', 'Perhatian', 'Tidak Ada Data yang disimpan!');
        }else{
            let dataInstruksi = $("#form-instruksi").serializeArray();
            let dataTerapi = $("#form-tindakan").serializeArray();
            let formData = $.merge(dataInstruksi, dataTerapi);
            // add ruangan 
            let dataRuangan = {name: 'ruangan', value: ruangan}
            formData.push(dataRuangan)
            $(this).docoForm('click',{
                url: "/ranap/pemeriksaan-rawat-inap/cppt-create-terapi-tindakan",
                data: formData,
                success: function(){
                    $(".tabel-tindakan").find("tbody").find("tr").each(function(){
                        if($(this).hasClass('newly-added')){
                            $(this).removeClass('newly-added');
                        }
                    });
                    $(".tabel-bmhp").find("tbody").find("tr").each(function(){
                        if($(this).hasClass('newly-added')){
                            $(this).removeClass('newly-added');
                        }
                    });
                    $("#btn-back-terapi").trigger('click');
                }
            });
        }

    });

    // Reset button
    $("#reset-terapi-tindakan").on("click", function(event) {
        // Prevent default
        event.preventDefault();

        // Find form
        var form = $("#form-tindakan");

        // Reset form
        form[0].reset();


        $(".tabel-tindakan").find("tbody").find("tr").each(function(){
            if($(this).hasClass('newly-added')){
                $(this).remove();
            }
            if($(this).hasClass('strikeout')){
                $(this).closest('tr').toggleClass('strikeout',false);
            }
            if($(this).closest('tr').find('input.is_ubah_deleted').length > 0){
                $(this).closest('tr').find('input.is_ubah_deleted').remove();
            }
        });
        $(".tabel-bmhp").find("tbody").find("tr").each(function(){
            if($(this).hasClass('newly-added')){
                $(this).remove();
            }
            if($(this).hasClass('strikeout')){
                $(this).closest('tr').toggleClass('strikeout',false);
            }
            if($(this).closest('tr').find('input.is_ubah_deleted').length > 0){
                $(this).closest('tr').find('input.is_ubah_deleted').remove();
            }
        });

        // $('#instruksiform-catatan_instruksi').val('');
        $(".detail-paket").find("span").remove();
        $(".detail-paket").find("ul").remove();
        $('#paket').trigger('change');
        // $('.field-instruksitindakanform-tarif_cyto').hide();
    });

/*from rajal*/
/*================================
=            tindakan            =
================================*/
// button print

    const count_riwayat = $('#count_riwayat').attr('data-id');
    if (count_riwayat > 0) {
        $('#btn-print').show();
    } else {
        $('#btn-print').hide();
    }

    // Deklarasi variabel
    var listTindakan = [];
    var listDokter = [];
    var tempPembanding = [];
    var options = [];
    var index = 0;

var changeTindakan = (tipe, response) => {
    if (tipe == "tindakan") {
        var data = response.data;
        var hargaSatuan = parseFloat(data.harga_tariftindakan);
        var persenCyto = parseFloat(data.persencyto_tindakan);
        var komponenTarifId = data.komponentarif_id;
        var harga = 0;
        var hargaCyto = 0;
        if(persenCyto > 0 ){
            hargaCyto = hargaSatuan * persenCyto / 100;
        }

        $("#instruksitindakanform-tarif_satuan").val(hargaSatuan).trigger('change');
        $("#instruksitindakanform-tarif_cyto").val(0);
        $("#komponentarif_id").val(komponenTarifId);

        $("#detail-paket").empty();

        if ($("#instruksitindakanform-qty").val() != '') {
            if ($("#instruksitindakanform-cyto_tindakan").is(":checked") && persenCyto > 0) {

                harga = (hargaSatuan + hargaCyto);
                $('.field-instruksitindakanform-tarif_cyto').show();
            } else {
                harga = hargaSatuan;
            }
            $("#instruksitindakanform-tarif_tindakan").val(parseFloat($("#instruksitindakanform-qty").val()) * harga).trigger('change');
        }
    } else {
        var data = response.data;

        var htmlDetail = ''
        if(data.paketDetail){
            if(data.paketDetail.length > 0){
                $(data.paketDetail).each(function(index){
                    htmlDetail += '<li>';
                    htmlDetail += data.paketDetail[index].daftartindakan_nama;
                    htmlDetail += '</li>';
                });
            }
        }else{
            htmlDetail += '<li>Gagal Mengambil Detail Paket</li>';
        }

        $("#detail-paket").empty();

        $("#detail-paket").append(htmlDetail);
        if (data) {
            var hargaPaket = parseFloat(data.harga_tariftindakan);
            var persenCyto = parseFloat(data.persencyto_tindakan);
            var persenCyto = data.persencyto_tindakan;
            var komponenTarifId = data.komponentarif_id;
            var harga = 0;
            var hargaCyto = 0;
            if(persenCyto >0 ){
                hargaCyto = hargaPaket * persenCyto / 100;
            }

            $("#instruksitindakanform-tarif_satuan").val(hargaPaket).trigger('change');
            $("#instruksitindakanform-tarif_cyto").val(hargaCyto);

            if ($("#instruksitindakanform-qty").val() != '') {
                if ($("#instruksitindakanform-cyto_tindakan").is(":checked") && persenCyto > 0) {
                    harga = (hargaPaket + hargaCyto);
                    $('.field-instruksitindakanform-tarif_cyto').show();
                } else {
                    harga = hargaPaket;
                }
                $("#instruksitindakanform-tarif_tindakan").val(parseFloat($("#instruksitindakanform-qty").val()) * harga).trigger('change');
            }
        }
    }
    $('#instruksitindakanform-cyto_tindakan').trigger('change')
}

$("#tindakan").on("change", function(e) {
    var daftartindakan_id = $(this).val();
    var penjaminId = $("#penjamin_id").val();
    var kelasPelayananId = $("#kelaspelayanan_id").val();

    if (daftartindakan_id != null && daftartindakan_id != '') {
        var response = {
            data: {}
        }

        if ($("#paket").is(":checked")) {
            var tipe = "paket";
        } else {
            var tipe = "tindakan";
        }

        $("#tipe").val(tipe);

        if (typeof _listTindakanPaket[tipe][daftartindakan_id]) {
            response.data = _listTindakanPaket[tipe][daftartindakan_id]
            changeTindakan(tipe, response)
        } else {
            var url = "/ranap/pemeriksaan-rawat-inap/cppt-get-tindakan-detail?id="+pendaftaran_id+"&detail_id="+daftartindakan_id+"&penjamin_id="+penjaminId+"&kelaspelayanan_id="+kelasPelayananId+"&tipe="+tipe;

            $.ajax({
                url: url,
                type: "GET",
                success : function(response) {
                    if (response.status = 200) {
                        changeTindakan(tipe, response)
                    } 
                }
            });
        }
    }
});

// When qty tindakan changed
$("#instruksitindakanform-qty").keyup(function (e) {
    // Check harga satuan
    if ($("#instruksitindakanform-tarif_satuan").val() != '') {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A, Command+A
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
             // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
                 // return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }

        let harga = 0;
        let qty = 0;
        let total = 0;
        let harga_tarif_satuan = docoHelper.convertToAngka($("#instruksitindakanform-tarif_satuan").val());

        if ($("#instruksitindakanform-cyto_tindakan").is(":checked") && $("#instruksitindakanform-tarif_cyto").val() > 0) {
            var _hargaCyto = docoHelper.convertToAngka($("#instruksitindakanform-tarif_cyto").val())
            harga = parseFloat(harga_tarif_satuan) + parseFloat(_hargaCyto);
            $('.field-instruksitindakanform-tarif_cyto').show();
        }
        else {
            harga = parseFloat(harga_tarif_satuan);
        }

        // Calculate total
        qty = $(this).val() ? parseFloat($(this).val()) : 0;
        total = parseFloat(harga * qty);

        // Assign total
        $("#instruksitindakanform-tarif_tindakan").val(total).trigger('change');
    }
});

$("#instruksitindakanform-cyto_tindakan").on("change", function(e) {
    var _idTindakan = $('#tindakan').val()
    if (_idTindakan === '') return false
    var isPaket = typeof $('#paket:checked').val() !== 'undefined' ? true : false; 
    if (isPaket) {
        var _data = _listTindakanPaket.paket[_idTindakan]
    } else {
        var _data = _listTindakanPaket.tindakan[_idTindakan]
    }

    var _persenCyto = parseFloat(_data.persencyto_tindakan)
    var _hargaTindakan = parseFloat(_data.harga_tariftindakan)

    _persenCyto = _persenCyto > 0 ? _persenCyto / 100 : 0;
    var _hargaCyto = _hargaTindakan * _persenCyto;

    if ($("#instruksitindakanform-cyto_tindakan").is(":checked")) {
        harga = _hargaTindakan + _hargaCyto;
        $('#instruksitindakanform-tarif_cyto').val(_hargaCyto).trigger('change');
    } else {
        harga = _hargaTindakan;
        $('#instruksitindakanform-tarif_cyto').val(0);
    }

    qty = parseFloat($("#instruksitindakanform-qty").val());
    total = parseFloat(harga * qty);
    $("#instruksitindakanform-tarif_tindakan").val(total).trigger('change');
});


var _tipePaket = () => {
    // Make 0
    $("#instruksitindakanform-qty").val(0);
    $("#instruksitindakanform-tarif_satuan").val(0);
    $("#instruksitindakanform-tarif_tindakan").val(0);

    // Check paket
    if ($("#paket").is(":checked")) {
        var optionsList = [];
        var newList = {
            id: '',
            text: '--Pilih Paket --'
        };
        optionsList.push(newList);

        // Check paket
        if (Object.values(_listTindakanPaket.paket).length > 0) {
            // Assign paket
            var paket = Object.values(_listTindakanPaket.paket);

            $(paket).each(function(i){
                optionsList.push({
                    id: this.tipepaket_id,
                    text: this.tipepaket_nama
                });
            });
        }
        $(optionsList).each(function(i){
            var newOption = new Option(this.text, this.id, false, false);
            $('#tindakan').append(newOption).trigger('change');
        });
    } else {
        var optionsList = [];
        var newList = {
            id: '',
            text: '--Pilih Tindakan--'
        };
        optionsList.push(newList);
        // Check tindakan
        if (Object.values(_listTindakanPaket.tindakan).length > 0) {
            // Assign paket
            var tindakan = Object.values(_listTindakanPaket.tindakan);

            // Loop
            $(tindakan).each(function(i){
                optionsList.push({
                    id: this.daftartindakan_id,
                    text: this.daftartindakan_nama
                });
            });

        }
        $(optionsList).each(function(i){

            var newOption = new Option(this.text, this.id, false, false);
            $('#tindakan').append(newOption).trigger('change');
        });
    }
}

$("#paket").on("change", function(e) {
    if ($("#paket").is(":checked")) {
        var html = "<span>Detail Paket</span><ul id='detail-paket'></ul>";
        $(".detail-paket").append(html);
    } else {
        $(".detail-paket").find("span").remove();
        $(".detail-paket").find("ul").remove();
    }
    $('#tindakan').empty()
    if (typeof _listTindakanPaket !== 'undefined') {
        _tipePaket()
    } else {
        $.ajax({
            url: "/ranap/pemeriksaan-rawat-inap/cppt-get-tindakan-paket-session?id="+pendaftaran_id,
            type: "GET",
            dataType: "json",
            beforeSend : function(){
                $("#tindakan").val(null).trigger('change');
                $('#tindakan').children('option').remove();
            },
            success : function(response) {
                _listTindakanPaket = response.response
                _tipePaket()
            }
        });
    }
});
$('#paket').trigger('change');

var appendBmhp = (inputTindakan) => {
    var params  = inputTindakan || {}
    if (_listTindakanPaket) {
        var listOrder = _listTindakanPaket.tindakan
        var id = params.daftar_tindakan_id
        if (params.isPaket) {
            listOrder = _listTindakanPaket.paket
            id = params.tipe_paket_id
        }
        var qtyTindakan = parseFloat(params.jumlahTindakan) || 0
        if (typeof listOrder[id] !== 'undefined') {
            var html = "";

            var count = parseFloat($("#count-bmhp").val());

            if (listOrder[id].list_bmhp.length > 0) {
               var listTindakan = listOrder[id].list_bmhp;
                $('.tabel-bmhp  tr.first-class').hide()
                listTindakan.forEach((val, key) => {
                    var sisaStok = parseFloat(val.stok)
                    var totalQty = qtyTindakan * val.qty_konversi
                    var _button = "<button type='button' class='btn btn-danger btn-remove-bmhp'><i class='fa fa-remove'></i></button>"
                    var _style = 'background: #fdb7b7'
                    if (sisaStok >= totalQty && sisaStok !== 0) {
                        // _button = "<i class='fa fa-lock'></i>"
                        _style = ''
                    }
                    var _findObat = $(`.tabel-bmhp > tbody > tr[data-id_instruksi_tindakan=${params.id_instruksi_tindakan}][data-mapping=1] > input[class='obatalkes_id'][value=${val.obatalkes_id}]`);
                    if (_findObat.length > 0) {
                        _findObat = _findObat.closest('tr')
                        var _stok = parseFloat(_findObat.find('input.qty').val()) + totalQty
                        if (sisaStok >= _stok && sisaStok !== 0) {
                            // _button = "<i class='fa fa-lock'></i>"
                            _style = ''
                        }
                        _findObat.find('td.td-hapus').html(_button)
                        _findObat.attr('style',_style)
                        _findObat.find('input.qty').val(_stok);
                        _findObat.find('td.qty').text(_stok);
                    } else {
                        html += "<tr data-daftartindakan_id='" + id + "' data-id_instruksi_tindakan='" + params.id_instruksi_tindakan + "'  data-count='"+count+"' data-mapping='1' class='newly-added' style='"+ _style +"'>" +
                            "<td class='td-no-bmhp'></td>" +
                            "<td>" + params.tanggalTindakan + "</td>" +
                            "<td class='namatindakan'>" + params.tindakanNama + "</td>" +
                            "<td>" + val.obatalkes_nama + "</td>" +
                            "<td>" + params.perawat1text + "</td>" +
                            "<td>" + params.perawat2text + "</td>" +
                            "<td class='qty'>" + totalQty + "</td>" +
                            "<td>-</td>" +
                            "<td>Belum Implementasi</td>" +
                            "<td class='td-hapus'>"+ _button +"</td>" +
                            "<input type='hidden' name='InstruksiTindakanBmhpForm[" + count + "][tgl_pelayanan]' value='" + params.tanggalTindakan + "' readoly='readonly'>" +
                            "<input type='hidden' class='daftartindakan_id' name='InstruksiTindakanBmhpForm[" + count + "][daftartindakan_id]' value='" + id + "' readoly='readonly'>" +
                            "<input type='hidden' class='obatalkes_id' name='InstruksiTindakanBmhpForm[" + count + "][obatalkes_id]' class='obatalkes_id' value='" + val.obatalkes_id + "' readoly='readonly'>" +
                            "<input type='hidden' class='perawat1_id' name='InstruksiTindakanBmhpForm[" + count + "][perawat1_id]' value='" + params.perawat1 + "' readoly='readonly'>" +
                            "<input type='hidden' class='perawat2_id' name='InstruksiTindakanBmhpForm[" + count + "][perawat2_id]' value='" + params.perawat2 + "' readoly='readonly'>" +
                            "<input type='hidden' class='qty' name='InstruksiTindakanBmhpForm[" + count + "][qty]' class='qty' value='" + totalQty + "' readoly='readonly'>" +
                            "<input type='hidden' name='InstruksiTindakanBmhpForm[" + count + "][hargajual_oa]' class='tarif-bmhp' value='"+ val.harga +"' readoly='readonly'>" +
                            "<input type='hidden' name='InstruksiTindakanBmhpForm[" + count + "][pasien_id]' value='" + $("#pasien_id").val() + "' readoly='readonly'>"+
                            "<input type='hidden' name='InstruksiTindakanBmhpForm[" + count + "][pendaftaran_id]' value='" + $("#pendaftaran_id").val() + "' readoly='readonly'>"+
                            "<input type='hidden' name='InstruksiTindakanBmhpForm[" + count + "][penjamin_id]' value='" + $("#penjamin_id").val() + "' readoly='readonly'>"+
                            "<input type='hidden' name='InstruksiTindakanBmhpForm[" + count + "][carabayar_id]' value='" + $("#carabayar_id").val() + "' readoly='readonly'>"+
                            "<input type='hidden' name='InstruksiTindakanBmhpForm[" + count + "][ruangan_id]' value='" + $("#ruangan_id").val() + "' readoly='readonly'>"+
                            "<input type='hidden' name='InstruksiTindakanBmhpForm[" + count + "][pegawai_id]' value='" + $("#pegawai-id").val() + "' readoly='readonly'>"+
                            "<input type='hidden' name='InstruksiTindakanBmhpForm[" + count + "][harga_jumlah]' value='0' readoly='readonly'>"+
                            "<input type='hidden' class='is_ditagihkan' name='InstruksiTindakanBmhpForm[" + count + "][is_ditagihkan]' value='0' readoly='readonly'>"+
                            "<input type='hidden' class='id_instruksi_tindakan' name='InstruksiTindakanBmhpForm[" + count + "][id_instruksi_tindakan]' value='" + params.id_instruksi_tindakan + "' readoly='readonly'>"+
                            "</tr>";
                       count++
                    }
                })
                $(".tabel-bmhp").find('tbody').append(html);
            }
        }
        $("#count-bmhp").val(count);
        checkStokTabel()
        $('.td-no-bmhp').each(function (index) {
            $(this).text(index + 1);
        });
    }
 }

var checkStokTabel = () => {
    var stokObat = [];
    if (resObat.length > 0) {
        resObat.forEach((val, key) => {
            stokObat.push(val)
        })
    }
    if (resAlkes.length > 0) {
        resAlkes.forEach((val, key) => {
            stokObat.push(val)
        })
    }

    stokObat.forEach((val, key) => {
        var _trObat = $(`.tabel-bmhp > tbody > tr > input[class='obatalkes_id'][value=${val.obatalkes_id}]`).closest('tr')
        var _qtyTersedia = parseFloat(val.qty_tersedia)
        if (_trObat.length > 0) {
            var _totalQty = 0
            _trObat.each(function() {
                var _qtyInput = parseFloat($(this).find('input.qty').val())
                _totalQty += _qtyInput
            })
            // _button = "<i class='fa fa-lock'></i>"
            _button = "<button type='button' class='btn btn-danger btn-remove-bmhp'><i class='fa fa-remove'></i></button>"
            _style = ''
            // _style = 'background: #fdb7b7'
            if (_totalQty > _qtyTersedia) {
                var _button = "<button type='button' class='btn btn-danger btn-remove-bmhp'><i class='fa fa-remove'></i></button>"
                var _style = 'background: #fdb7b7'
            }
            _trObat.attr('style', _style)
            $(`.tabel-bmhp > tbody > tr[data-mapping=1] > input[class='obatalkes_id'][value=${val.obatalkes_id}]`).closest('tr').find('td.td-hapus').html(_button)
        }
    })
}


// On click tambah tindakan
$("#tambah-tindakan").on("click", function(e) {
    $('.form-group').removeClass('has-error');
    $('.help-block.error').remove();
    // Get data
    var selectTindakan = $("#tindakan").val();
    var selectDokter = $("#dokter_pemeriksa").val();
    var inputJumlah = $("#instruksitindakanform-qty").val();

    // Check tindakan
    if ((selectTindakan != '') && (selectDokter != '') && (inputJumlah != 0 && inputJumlah != '')) {
        $('.tabel-tindakan  tr.first-class').hide()
        // Assign some values
        var tanggalTindakan = $("#instruksitindakanform-tgl_tindakan").val();
        var tindakan = $("#tindakan").val();
        var tindakanId = $("#tindakan").val();
        var dokterPemeriksaText = dokterPemeriksa != '' ? $("#dokter_pemeriksa option:selected").text() : '-';
        var tindakantext = tindakanId != '' ? $("#tindakan option:selected").text()+' - '+dokterPemeriksaText : '-';
        var jumlahTindakan = $("#instruksitindakanform-qty").val();
        var tarifSatuan = docoHelper.convertToAngka($("#instruksitindakanform-tarif_satuan").val());
        var tarifCyto = docoHelper.convertToAngka($("#instruksitindakanform-tarif_cyto").val());
        var tarifTindakan = docoHelper.convertToAngka($("#instruksitindakanform-tarif_tindakan").val());
        var dokterPemeriksa = $("#dokter_pemeriksa").val();
        var perawat1 = $("#perawat1_id").val();
        var perawat1text = perawat1 != '' ? $("#perawat1_id option:selected").text() : '-';
        var perawat2 = $("#perawat2_id").val();
        var perawat2text = perawat2 != '' ? $("#perawat2_id option:selected").text() : '-';
        var status_implementasi = "Belum Implementasi";

        var count = $("#countTindakan").val();
        var tipe;
        var addedListTindakan='';
        var detailedPaket = '';
        var isPaket = false
        if ($("#paket").is(":checked")) {
            isPaket = true
            tipe = "paket";
            tipe_paket_tindakan = tindakanId;
            if($('#detail-paket').length != 0){
                $('#detail-paket').find('li').each(function(){
                    detailedPaket += "<tr data-id='"+tindakanId+"' data-count='"+count+"' class='detailed-paket newly-added'><td></td><td></td><td colspan='8'>";
                    detailedPaket += $(this).html();
                    detailedPaket += "</td></tr>";
                });
            }
        } else {
            tipe = "tindakan";
            tipe_paket_tindakan = '';
        }
        var is_ket_cyto = '-';
        var isCyto = 0;
        if ($("#instruksitindakanform-cyto_tindakan").is(":checked")) {
            isCyto = 1;
            is_ket_cyto = '✓';
        }
        var id_instruksi_tindakan = tindakanId+'-'+dokterPemeriksa+'-'+perawat1+'-'+perawat2+'-'+isCyto + '-' + tipe_paket_tindakan;
        // Declare html
        var html;

        var inputTindakan = {   
            tindakanNama: tindakantext,
            daftar_tindakan_id: tindakanId,
            tipe_paket_id: tipe_paket_tindakan,
            jumlahTindakan: jumlahTindakan,
            isPaket: isPaket,
            isCyto: isCyto,
            perawat1: perawat1,
            perawat1text: perawat1text,
            perawat2: perawat2,
            perawat2text: perawat2text,
            id_instruksi_tindakan: id_instruksi_tindakan,
            tanggalTindakan: tanggalTindakan,
        }


        // Assign html
        html += "<tr data-id='"+tindakanId+"' data-count='"+count+"' data-id_instruksi_tindakan='"+id_instruksi_tindakan+"' class='newly-added'>"+
            "<td class='td-no'></td>"+
            "<td>"+tanggalTindakan+"</td>"+
            "<td class='daftartindakan-nama'>"+tindakantext+"</br>"+addedListTindakan+"</td>"+
            "<td class='dokterdpjp-nama'>"+dokterPemeriksaText+"</td>"+
            "<td>"+perawat1text+"</td>"+
            "<td>"+perawat2text+"</td>"+
            "<td class='qty'>"+jumlahTindakan+"</td>"+
            "<td>"+status_implementasi+"</td>"+
            "<td>"+is_ket_cyto+"</td>"+
            "<td class='td-hapus'><button type='button' class='btn btn-danger btn-remove-tindakan'><i class='fa fa-remove'></i></button></td>"+
            "<input type='hidden' class='tgl_tindakan' name='InstruksiTindakanForm["+count+"][tgl_tindakan]' value='"+tanggalTindakan+"' readoly='readonly'>"+
            "<input type='hidden' class='daftartindakan_id' name='InstruksiTindakanForm["+count+"][daftartindakan_id]' class='daftartindakan-id' value='"+tindakanId+"' readoly='readonly'>"+
            "<input type='hidden' class='dokterdpjp_id' name='InstruksiTindakanForm["+count+"][dokterdpjp_id]' class='dokterdpjp-id' value='"+dokterPemeriksa+"' readoly='readonly'>"+
            "<input type='hidden' class='perawat1_id' name='InstruksiTindakanForm["+count+"][perawat1_id]' value='"+perawat1+"' readoly='readonly'>"+
            "<input type='hidden' class='perawat2_id' name='InstruksiTindakanForm["+count+"][perawat2_id]' value='"+perawat2+"' readoly='readonly'>"+
            "<input type='hidden' class='qty' name='InstruksiTindakanForm["+count+"][qty]' value='"+jumlahTindakan+"' readoly='readonly'>"+
            "<input type='hidden' name='InstruksiTindakanForm["+count+"][status_implementasi]' value='454' readoly='readonly'>"+
            "<input type='hidden' name='InstruksiTindakanForm["+count+"][pasien_id]' value='"+$("#pasien_id").val()+"' readoly='readonly'>"+
            "<input type='hidden' name='InstruksiTindakanForm["+count+"][carabayar_id]' value='"+$("#carabayar_id").val()+"' readoly='readonly'>"+
            "<input type='hidden' name='InstruksiTindakanForm["+count+"][pendaftaran_id]' value='"+$("#pendaftaran_id").val()+"' readoly='readonly'>"+
            "<input type='hidden' name='InstruksiTindakanForm["+count+"][jeniskasuspenyakit_id]' value='"+$("#jeniskasuspenyakit_id").val()+"' readoly='readonly'>"+
            "<input type='hidden' name='InstruksiTindakanForm["+count+"][penjamin_id]' value='"+$("#new_penjamin_id").val()+"' readoly='readonly'>"+
            "<input type='hidden' name='InstruksiTindakanForm["+count+"][ruangan_id]' value='"+$("#ruangan_id").val()+"' readoly='readonly'>"+
            "<input type='hidden' name='InstruksiTindakanForm["+count+"][instalasi_id]' value='"+$("#instalasi_id").val()+"' readoly='readonly'>"+
            "<input type='hidden' name='InstruksiTindakanForm["+count+"][kelaspelayanan_id]' value='"+$("#kelaspelayanan_id").val()+"' readoly='readonly'>"+
            "<input type='hidden' name='InstruksiTindakanForm["+count+"][komponentarif_id]' value='"+$("#komponentarif_id").val()+"' readoly='readonly'>"+
            "<input type='hidden' name='InstruksiTindakanForm["+count+"][tarif_satuan]' value='"+tarifSatuan+"' readoly='readonly'>"+
            "<input type='hidden' name='InstruksiTindakanForm["+count+"][tarif_cyto]' value='"+tarifCyto+"' readoly='readonly'>"+
            "<input type='hidden' class='is_cyto' name='InstruksiTindakanForm["+count+"][is_cyto]' value='"+isCyto+"' readoly='readonly'>"+
            "<input type='hidden' name='InstruksiTindakanForm["+count+"][jumlah_tarif]' value='"+tarifTindakan+"' readoly='readonly'>"+
            "<input type='hidden' name='InstruksiTindakanForm["+count+"][tipepaket_id]' value='"+tipe_paket_tindakan+"' readoly='readonly'>"+
            "<input type='hidden' name='InstruksiTindakanForm["+count+"][tipe]' value='"+tipe+"' readoly='readonly'>"+
            "<input type='hidden' class='id_instruksi_tindakan' name='InstruksiTindakanForm["+count+"][id_instruksi_tindakan]' value='"+id_instruksi_tindakan+"' readoly='readonly'>"+
        "</tr>";
        html += detailedPaket;
        var appended = true
        $(".tabel-tindakan").find("tbody").find("tr").not($('tr.detailed-paket')).each(function(){
            var elemtr = $(this);
            if( $(this).data('id') == tindakanId &&
                $(this).children('input.dokterdpjp_id').val() == dokterPemeriksa &&
                $(this).children('input.perawat1_id').val() == perawat1 &&
                $(this).children('input.perawat2_id').val() == perawat2 &&
                $(this).children('input.is_cyto').val() == isCyto
            ){
                appended = false;
                var qty_result = parseFloat(jumlahTindakan) + parseFloat($(this).children('input.qty').val());
                $(this).children('input.qty').val(qty_result);
                $(this).children('td.qty').text(qty_result);
            }
        })
        if(appended){
            $(".tabel-tindakan").find("tbody").append(html);
        }

        count = parseFloat(count) + 1;


        $("#countTindakan").val(count);

        /* NUMBERING */
        // Looping for tindakan
        $('.td-no').each(function(index) {
            // Assign number
            $(this).text(index+1);
        });

        update_daftar_tindakan();

        $(".detail-paket").find("span").remove();
        $(".detail-paket").find("ul").remove();

        $('#dokter_pemeriksa').val('').trigger('change');
        $('#perawat1_id').val('').trigger('change');
        $('#perawat2_id').val('').trigger('change');
        $('#perawat3_id').val('').trigger('change');
        $("#form-tindakan").trigger('reset');
        $('#dokter_pemeriksa').val($('#dokter_pemeriksa option:selected').val()).trigger('change');
        $('#perawat1_id').val($('#perawat1_id option:selected').val()).trigger('change');
        $('#perawat3_id').val($('#perawat3_id option:selected').val()).trigger('change');
        $('#paket').trigger('change');
        $('.jenis_pemakaian[value=619]').prop("checked",true).trigger('change');
        $('#tagih_pasien[value=1]').prop("checked",true);
        appendBmhp(inputTindakan)
    } else {
        var logo  = '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> &nbsp';
        if(inputJumlah == '' || inputJumlah == 0){
            var _field_qty = $('#instruksitindakanform-qty');
            _field_qty.parent('div').addClass('has-error');
            _field_qty.parent('.required').addClass('has-error');
            _field_qty.after('<span class="help-block error">'+ logo + 'Jumlah Tindakan Tidak Boleh Kosong' +'</span>');
        }
        if(selectDokter == ''){
            var _field_dokter = $('#dokter_pemeriksa');
            _field_dokter.parent('div').addClass('has-error');
            _field_dokter.parent('.required').addClass('has-error');
            _field_dokter.after('<span class="help-block error">'+ logo + 'Dokter Harus Dipilih' +'</span>');     
        }
        if(selectTindakan == ''){
            var _field_tindakan = $("#tindakan");
            _field_tindakan.closest('div.form-group').addClass('has-error');
            _field_tindakan.closest('div.form-group').find('span.error').remove();
            _field_tindakan.closest('div.form-group').append('<span class="help-block error">'+ logo + 'Tindakan Harus Dipilih' +'</span>'); 
        }
        // Notify
        new PNotify({
            title: "Proses Gagal!",
            text: "Data yang mandatori tidak boleh kosong",
            addclass: 'alert alert-danger alert-arrow-right alert-styled-right',
            type: 'danger'
        });
    }

});

// On click btn remove
$(document).on('click', '.btn-remove-tindakan', function() {
    /* HAPUS ROW */
    // Remove row
    var tr_id = $(this).closest('tr').data('id');
    var tr_count = $(this).closest('tr').data('count');
    var tr_instruksitindakanid = $(this).closest('tr').data('id_instruksi_tindakan');
    $(this).closest('tr').parent().find('tr.detailed-paket[data-id='+tr_id+'][data-count='+tr_count+']').each(function(){
        $(this).remove();
    });
    $(this).closest('tr').remove();

    /* NUMBERING */
    // Numbering
    $('.td-no').each(function(index) {
        $(this).text(index+1);
    });

    update_daftar_tindakan();

    $('.tabel-bmhp > tbody > tr').each(function() {
        if($(this).data('id_instruksi_tindakan') != "0"){
            if($(this).data('id_instruksi_tindakan') == tr_instruksitindakanid){
                $(this).remove()
            }
        }
    });

    if ($(".tabel-tindakan").find("tbody").find("tr").not("tr.detailed-paket, tr.first-class").length === 0) {
        $(".tabel-tindakan > tbody > tr.first-class").show()
    }

    if ($('.td-no-bmhp').length === 0) {
        $('.tabel-bmhp > tbody > tr.first-class').show();
    }
    checkStokTabel();
});


/*=====  End of tindakan  ======*/


/*==================================
=            bmhp alkes            =
==================================*/
$("#bmhpalkes_select2").on("change", function(e) { 
    var data_bmhpalkes = $(this).val();

    // console.log(data_bmhpalkes);
    var obatalkes_id = data_bmhpalkes ? data_bmhpalkes : 0;
    var submit = $('#form-bmhp').find(':submit');

    var url = "/ranap/pemeriksaan-rawat-inap/get-obatalkes-detail?obatalkes_id="+obatalkes_id;
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        beforeSend : function() {
            submit.prop("disabled", true);
        },
        success : function(response) {
            if (response.status = 200){
                console.log(response.data);
                let data_obatalkes = response.data;
                let harga_satuan = data_obatalkes.harga_satuan;
                let harga_netto = data_obatalkes.harganetto;
                let obatalkes_nama = data_obatalkes.obatalkes_nama;

                $("#instruksitindakanbmhpform-obatalkes_nama").val(obatalkes_nama);
                $("#instruksitindakanbmhpform-hargasatuan_oa").val(harga_satuan);
                $("#instruksitindakanbmhpform-harga_netto").val(harga_netto);
            }else{
                console.log(response.message);
            }
        }
    }).done(function() {
        submit.prop("disabled", false);
    });
});

// When tindakan changed
$("#tindakan_obatalkes").on("change", function(e) {
    // Get data from select2
    var data_tindakan = $(this).val();

    // Tindakan id
    var id = data_tindakan ? data_tindakan : null;

    // Check id
    if (id != null) {
        // Split
        var tempData = id.split("-");
        var tindakan = tempData[0];
        var dokter = tempData[1];

        // Assign datas
        $("#daftartindakan-id").val(tindakan);
        $("#pegawai-id").val(dokter);
    }
});


var buildOptObat = (response) => {
    let obatalkes_select2 = $('#obatalkes_select2');
    $("#obatalkes_select2 option").remove();

    var data = {
        id: "0",
        text: "-- Pilih obat/alkes --"
    };

    options = new Option(data.text, data.id, false, false);

    obatalkes_select2.append(options);
    if (response.length > 0) {
        const data = response;
        const count = data.length;
        if (parseFloat(count) > 0) {
            data.forEach(val => {
                var dis_obat = '';
                if(val.qty_tersedia < 1){
                    dis_obat = 'disabled';
                }
                obatalkes_select2.append('<option '+dis_obat+' value=' + `${val.obatalkes_id}`+ ' data-stok=' + `${val.qty_tersedia}` + ' data-harga=' + `${val.hargaygdipakai}`+ '>' + `${val.obatalkes_nama}` + ' - (' + `${val.qty_tersedia}` + ')</option>');
            });
        } else {
            obatalkes_select2.empty();
        }
    }
}


$('#instruksitindakanbmhpform-obat').change(function() {
    // const jenis_id = /$(".jenis_pemakaian").val();
    const jenis_id = $("input:radio[name='InstruksiTindakanBmhpForm[obat]']:checked").val();
    const harga_digunakan = $('.harga_digunakan').val();
    let penjaminId = $("#penjamin_id").val();
    let obatalkes_select2 = $('#obatalkes_select2');
    var dataObat = []
    $('#select_depo_bmhp').val("").trigger('change')
    // $('#select_depo_bmhp').trigger('change')
    // $("#temp_obat").val(jenis_id);

    // if (jenis_id === "619") {
    //     dataObat = resObat
    // } else {
    //     dataObat = resAlkes
    // }

    // if (dataObat.length > 0) {
    //     buildOptObat(dataObat);
    // } else {
    //     $.ajax({
    //         url: '/ranap/pemeriksaan-rawat-inap/cppt-get-obat-alkes-by-jenis?id=' + pendaftaran_id+'&group_jenis='+jenis_id+'&penjamin='+penjaminId,
    //         type: 'get',
    //         success: (res) => {
    //             var _response = res.response || {}
    //             resObat = _response.obat || []
    //             resAlkes = _response.alkes || []
    //             if (jenis_id === "619") {
    //                 buildOptObat(resObat)
    //             } else {
    //                 buildOptObat(resAlkes)
    //             }

    //         }
    //     })
    // }
});

$('#select_depo_bmhp').bind('change.select2', ({ delegateTarget }) => {
    const jenis_id = $("input:radio[name='InstruksiTindakanBmhpForm[obat]']:checked").val();
    let penjaminId = $("#penjamin_id").val();
    let depoId = $(delegateTarget).val()
    $("#temp_obat").val(jenis_id);
    
    if(depoId == null ) {
        $('#obatalkes_select2').html('').select2({data: [{id: '', text: '-- Pilih Obat/Alkes--'}]});
        return console.log('Depo Belum Di pilih')
    }

    $("#obatalkes_select2").docoPaginationSelec2(
        config = {
            placeholder : '-- Pilih Obat/Alkes--',     
            _api : '/ranap/pemeriksaan-rawat-inap/cppt-get-obat-alkes-by-jenis?id=' + pendaftaran_id+'&group_jenis='+jenis_id+'&penjamin='+penjaminId+'&ruangan_id='+depoId+'&kelaspelayananId='+kelaspelayananId,
            ajax: {
                processResults: function (data, params) {
                    let _response = data.response;
                    let res = '';
                    params.page = params.page || 1;
                    if(typeof _response.obat !== 'undefined'){
                        res = _response.obat
                    } else {
                        res = _response.alkes
                    }
                    return {
                        results: res,
                        pagination: {
                            more: data.pagination.more
                        }
                    }
                },
                success: (res) => {
                    let _response = res.response || {}
                    resObat = _response.obat || []
                    resAlkes = _response.alkes || []

                    // Tambah ke variabel list obat
                    if (resObat.length > 0) {
                        resObat.forEach((val, key) => {
                            listObat[val.obatalkes_id] = val;
                        });
                    }
                    if (resAlkes.length > 0) {
                        resAlkes.forEach((val, key) => {
                            listObat[val.obatalkes_id] = val;
                        });
                    }
                }
            }
        }
    );
})

$('#btn-print').on('click', function(e) {
    e.preventDefault();
    const pendaftaranId = $(this).attr('data-id');
    console.log(pendaftaranId)
    window.open(
        '/ranap/pemeriksaan-rawat-inap/cetak-tindakan?id=' + pendaftaranId,
        '_blank'
    );
})


$('#obatalkes_select2').change(function() {
    var harga = $(this).find(':selected').data().data.data_harga;
    var stok = $(this).find(':selected').data().data.data_stok;
    const id = $(this).val();
    var jml = $('.jml').val();
    var total = parseFloat(harga) * parseFloat(jml);

    var qty_pesan = 0;
    $(".tabel-bmhp").find("tbody").find("tr.newly-added").not($('tr.strikeout')).each(function(){
        if($(this).find('input.obatalkes_id').val() == id){
            qty_pesan = qty_pesan + parseFloat($(this).find('input.qty').val());
        }
    });
    var stok_tersedia = stok - qty_pesan;
    $('#obat_alkes_tersedia').val(stok_tersedia);

    if(isNaN(total)) {
        total = 0;
    }

    var convertedTotal = docoHelper.numberFormat(total, 2, ",", ".");
    $('#instruksitindakanbmhpform-harga_jumlah').val(convertedTotal);
});

$('.jml').on('keyup', function() {
    var jml = $(this).val();
    var harga = $('#obatalkes_select2').find(':selected').data().data.data_harga;
    const stok = $('#obatalkes_select2').find(':selected').data().data.data_stok;
    var total = parseFloat(harga) * parseFloat(jml);

    if(isNaN(total)) {
        total = 0;
    }

    var convertedTotal = docoHelper.numberFormat(total, 2, ",", ".");
    $('#instruksitindakanbmhpform-harga_jumlah').val(convertedTotal);
})

$("#tambah-obatalkes").on("click", function (e) {
    var radioPemakaian = $("#temp_obat").val();
    var selectObat = $("#obatalkes_select2").val();
    var inputJumlah = parseFloat($("#instruksitindakanbmhpform-qty").val() || 0);
    // var selectPerawat = $("#perawat3_id").val();
    var stok_tersedia = parseFloat($("#obat_alkes_tersedia").val());
    if ((radioPemakaian != '') && (selectObat != '') && (inputJumlah != 0) && (inputJumlah <= stok_tersedia)) {
        $('.tabel-bmhp > tbody > tr.first-class').hide();
        var tanggalTindakan = $("#instruksitindakanform-tgl_tindakan").val();
        var tindakan = '0';
        var id_instruksi_tindakan = $("#tindakan_obatalkes").val();
        if(id_instruksi_tindakan != "0"){
            var list_id_tindakan = id_instruksi_tindakan.split('-');
            tindakan = list_id_tindakan[0];
        }
        var tindakantext = tindakan != '0' ? $("#tindakan_obatalkes option:selected").text() : '-';
        var obat_alkes = $("#obatalkes_select2").val();
        var obat_alkestext = obat_alkes != '' ? $("#obatalkes_select2 option:selected").text() : '-';
        if(obat_alkestext != '-'){
            var splittxt = obat_alkestext.split('-');
            obat_alkestext = splittxt[0];
        }
        var jumlah = $(".jml").val();
        var perawat1 = $("#perawat3_id").val();
        var perawat1text = perawat1 != '' ? $("#perawat3_id option:selected").text() : '-';
        var perawat2 = $("#perawat4_id").val();
        var perawat2text = perawat2 != '' ? $("#perawat4_id option:selected").text() : '-';
        var status_implementasi = "Belum Implementasi";
        var is_ditagihkan = 0;
        // Check paket
        if ($("#tagih_pasien").is(":checked")) {
            // Assign checklist
            var checklist = '✓';
            is_ditagihkan = 1;

            // Assign jml tarif
            var jmlTarif = docoHelper.convertToAngka($("#instruksitindakanbmhpform-harga_jumlah").val());
        } else {
            var checklist = '-';
            var jmlTarif = 0;
        }

        var count = $("#count-bmhp").val();

        var html;

        // Assign html
        html += "<tr data-daftartindakan_id='" + tindakan + "' data-id_instruksi_tindakan='" + id_instruksi_tindakan + "'  data-count='"+count+"' class='newly-added'>" +
            "<td class='td-no-bmhp'></td>" +
            "<td>" + tanggalTindakan + "</td>" +
            "<td class='namatindakan'>" + tindakantext + "</td>" +
            "<td>" + obat_alkestext + "</td>" +
            "<td>" + perawat1text + "</td>" +
            "<td>" + perawat2text + "</td>" +
            "<td class='qty'>" + jumlah + "</td>" +
            "<td>" + checklist + "</td>" +
            "<td>" + status_implementasi + "</td>" +
            "<td class='td-hapus'><button type='button' class='btn btn-danger btn-remove-bmhp'><i class='fa fa-remove'></i></button></td>" +
            "<input type='hidden' name='InstruksiTindakanBmhpForm[" + count + "][tgl_pelayanan]' value='" + tanggalTindakan + "' readoly='readonly'>" +
            "<input type='hidden' class='daftartindakan_id' name='InstruksiTindakanBmhpForm[" + count + "][daftartindakan_id]' value='" + tindakan + "' readoly='readonly'>" +
            "<input type='hidden' class='obatalkes_id' name='InstruksiTindakanBmhpForm[" + count + "][obatalkes_id]' class='obatalkes_id' value='" + obat_alkes + "' readoly='readonly'>" +
            "<input type='hidden' class='perawat1_id' name='InstruksiTindakanBmhpForm[" + count + "][perawat1_id]' value='" + perawat1 + "' readoly='readonly'>" +
            "<input type='hidden' class='perawat2_id' name='InstruksiTindakanBmhpForm[" + count + "][perawat2_id]' value='" + perawat2 + "' readoly='readonly'>" +
            "<input type='hidden' class='qty' name='InstruksiTindakanBmhpForm[" + count + "][qty]' class='qty' value='" + jumlah + "' readoly='readonly'>" +
            "<input type='hidden' name='InstruksiTindakanBmhpForm[" + count + "][hargajual_oa]' class='tarif-bmhp' value='" + jmlTarif + "' readoly='readonly'>" +
            "<input type='hidden' name='InstruksiTindakanBmhpForm[" + count + "][pasien_id]' value='" + $("#pasien_id").val() + "' readoly='readonly'>"+
            "<input type='hidden' name='InstruksiTindakanBmhpForm[" + count + "][pendaftaran_id]' value='" + $("#pendaftaran_id").val() + "' readoly='readonly'>"+
            "<input type='hidden' name='InstruksiTindakanBmhpForm[" + count + "][penjamin_id]' value='" + $("#penjamin_id").val() + "' readoly='readonly'>"+
            "<input type='hidden' name='InstruksiTindakanBmhpForm[" + count + "][carabayar_id]' value='" + $("#carabayar_id").val() + "' readoly='readonly'>"+
            "<input type='hidden' name='InstruksiTindakanBmhpForm[" + count + "][ruangan_id]' value='" + $("#select_depo_bmhp").val() + "' readoly='readonly'>"+
            "<input type='hidden' name='InstruksiTindakanBmhpForm[" + count + "][pegawai_id]' value='" + $("#pegawai-id").val() + "' readoly='readonly'>"+
            "<input type='hidden' name='InstruksiTindakanBmhpForm[" + count + "][harga_jumlah]' value='" + jmlTarif + "' readoly='readonly'>"+
            "<input type='hidden' class='is_ditagihkan' name='InstruksiTindakanBmhpForm[" + count + "][is_ditagihkan]' value='" + is_ditagihkan + "' readoly='readonly'>"+
            "<input type='hidden' class='id_instruksi_tindakan' name='InstruksiTindakanBmhpForm[" + count + "][id_instruksi_tindakan]' value='" + id_instruksi_tindakan + "' readoly='readonly'>"+
            "</tr>";

        var appended = true
        $(".tabel-bmhp").find("tbody").find("tr").each(function(){
            var elemtr = $(this);
            // console.log($(this).children());
            if( $(this).data('daftartindakan_id') == tindakan &&
                $(this).children('input.obatalkes_id').val() == obat_alkes &&
                $(this).children('input.perawat1_id').val() == perawat1 &&
                $(this).children('input.perawat2_id').val() == perawat2 &&
                $(this).children('input.is_ditagihkan').val() == is_ditagihkan &&
                $(this).data('mapping') != 1
            ){
                appended = false;
                var qty_result = parseFloat(jumlah) + parseFloat($(this).children('input.qty').val());
                $(this).children('input.qty').val(qty_result);
                $(this).children('td.qty').text(qty_result);
            }
        })
        if(appended){
            $(".tabel-bmhp").find('tbody').append(html);
        }

        // Plus the counter
        count = parseFloat(count) + 1;

        // Assign count
        $("#count-bmhp").val(count);

        // Numbering
        $('.td-no-bmhp').each(function (index) {
            $(this).text(index + 1);
        });

        $("#obatalkes_select2 option").remove();

        // Assign prompt untuk dimasukan ke list tindakan
        var data = {
            id: "",
            text: "-- Pilih obat/alkes --"
        };

        // Option untuk di append ke daftar tindakan di bmhp
        options = new Option(data.text, data.id, false, false);

        // Append ke tindakan di bmhp
        obatalkes_select2.append(options);

        $("#form-tindakan").trigger('reset');
        $('#tagih_pasien[value=1]').prop("checked",true);
        $('.jenis_pemakaian[value=619]').prop("checked",true).trigger('change')
        $('#perawat4_id').val('').trigger('change')
        $('#tindakan_obatalkes').val(0).trigger('change')
        checkStokTabel()
    } else if(inputJumlah > stok_tersedia){
        new PNotify({
            title: "Proses Gagal!",
            text: "Jumlah tidak dapat melebihi stok yang tersedia",
            addclass: 'alert alert-danger alert-arrow-right alert-styled-right',
            type: 'danger'
        });
    } else {
        var logo  = '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> &nbsp';
        if(!$("input:radio[name='InstruksiTindakanBmhpForm[obat]']").is(':checked')) {
            var _field_radioobat = $('#instruksitindakanbmhpform-obat');
            _field_radioobat.parent().find('span.error').remove();
            _field_radioobat.after('<span class="help-block error">'+ logo + 'Jenis Pemakaian Harus Dipilih' +'</span>');     
        }
        if(selectObat == ''){
            var _field_obatalkes = $("#obatalkes_select2");
            _field_obatalkes.closest('div.form-group').addClass('has-error');
            _field_obatalkes.closest('div.form-group').find('span.error').remove();
            _field_obatalkes.closest('div.form-group').append('<span class="help-block error">'+ logo + 'Tindakan Harus Dipilih' +'</span>'); 
        }

        if(inputJumlah === 0){
            var _field_jumlah = $("#instruksitindakanbmhpform-qty");
            _field_jumlah.parent('div').addClass('has-error');
            _field_jumlah.parent('.required').addClass('has-error');
            _field_jumlah.parent().find('span.error').remove();
            _field_jumlah.after('<span class="help-block error">'+ logo + 'Jumlah Tidak Boleh Kosong' +'</span>');     
        }
        // if(selectPerawat == ''){
        //     var _field_perawat = $("#perawat3_id");
        //     _field_perawat.closest('div.form-group').addClass('has-error');
        //     _field_perawat.closest('div.form-group').find('span.error').remove();
        //     _field_perawat.closest('div.form-group').append('<span class="help-block error">'+ logo + 'Tindakan Harus Dipilih' +'</span>'); 
        // }
        // Notify
        new PNotify({
            title: "Tidak bisa menambahkan data",
            text: "Mohon periksa kembali form",
            addclass: 'alert alert-danger alert-arrow-right alert-styled-right',
            type: 'danger'
        });
    }
});

// On click btn remove
$(document).on('click', '.btn-remove-bmhp', function() {
    // Get obat id
    var id = $(this).closest('tr').find(".obatalkes_id").val();
    var jumlah = $(this).closest('tr').find(".qty").val();
    var ruangan_id = $("#ruangan_id").val();

    // Remove row
    $(this).closest('tr').remove();

    // Numbering
    $('.td-no-bmhp').each(function(index) {
        // Assign number
        $(this).text(index+1);
    });

    // Calculate total tindakan
    var totalHargaTindakan = 0;

    // LLop to calculate total
    $(".tarif-bmhp").each(function() {
        // Get value
        var value = $(this).val();

        // add only if the value is number
        if(!isNaN(value) && value.length != 0) {
            // Calculate
            totalHargaTindakan += parseFloat(value);
        }
    });

    if ($('.td-no-bmhp').length === 0) {
        $('.tabel-bmhp > tbody > tr.first-class').show();
    }
    checkStokTabel();
});

$('#instruksitindakanbmhpform-obat').trigger('change')
$('#select_depo_bmhp').trigger('change')
});


