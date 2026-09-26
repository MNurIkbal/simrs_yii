/*
 * @Author: rizfardi@docotel.com
 * @Date:   2018-03-14 10:15:19
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2018-11-22 10:37:40
 */

/*================================
=            tindakan            =
================================*/

function real_time_field(id) {
    date = new Date()
    year = date.getFullYear()
    month = date.getMonth()
    months = new Array(
        'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    )
    d = date.getDate()
    day = date.getDay()
    days = new Array(
        'Minggu',
        'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat',
        'Sabtu'
    )
    h = date.getHours()
    if (h < 10) {
        h = '0' + h
    }
    m = date.getMinutes()
    if (m < 10) {
        m = '0' + m
    }
    s = date.getSeconds()
    if (s < 10) {
        s = '0' + s
    }
    result =
        '' +
        days[day] +
        ', ' +
        d +
        ' ' +
        months[month] +
        ' ' +
        year +
        ' ' +
        h +
        ':' +
        m +
        ':' +
        s
    if (document.getElementById(id) !== null) {
        document.getElementById(id).innerHTML = `<b>${result}</b>`
    }
    setTimeout('real_time_field("' + id + '");', '1000')
    return true
}

$(function () {
    real_time_field('time_tgl_tindakan')
    $('.select2tindakan').select2()
    $('#paket').trigger('change')
    $('#tagih_pasien').prop('checked', true)
    $('.jenis_pemakaian[value=619]').prop('checked', true).trigger('change')
})
// $('#tindakanpelayananform-text_cyto_tindakan').hide();
if ($('#perawat1_id').val() != '') {
    $('#perawat1_id').prop('disabled', true)
}

// Deklarasi variabel
var listTindakan = []
var listDokter = []
var tempPembanding = []
var options = []
var index = 0
var increment = 0
var incrementObat = 0
var resObat = []
var resAlkes = []
var resData = [];


$('#btn-tindakan-back').bind('click' , () => {
	$('#tab-cppt').trigger('click')
})

// When tindakan changed
$('#tindakan').on('change', function (e) {
    $('#detail-paket').empty()
    // Get data from select2
    var data_tindakan = $(this).val()

    // Tindakan id
    var id = data_tindakan
    var penjaminId = $('#penjamin_id').val()
    var kelasPelayananId = $('#kelaspelayanan_id').val()

    if (id != null) {
        if ($('#paket').is(':checked')) {
            var tipe = 'paket'
        } else {
            var tipe = 'tindakan'
        }

        $('#tipe').val(tipe)

        $('#tindakanpelayananform-tarif_satuan').val(0)
        $('#tarifsatuan_hidden').val(0)
        $('#tindakanpelayananform-tarifcyto_tindakan').val(0)
        $('#tindakanpelayananform-text_cyto_tindakan').val(0)

        var hargaSatuan = 0
        var harga = 0
        var hargaCyto = 0
        var persencyto = 0
        if (tipe == 'paket') {
            var resource = listTindakanPaket.response.paket
            if (typeof resource[data_tindakan] != 'undefined') {
                hargaSatuan = parseFloat(
                    resource[data_tindakan].harga_tariftindakan
                )
                persencyto = parseFloat(resource[data_tindakan].persencyto_tindakan)
                $('#tipepaket_id').val(id)
                if (resource[data_tindakan].list_tindakan.length > 0) {
                    var html = ''
                    resource[data_tindakan].list_tindakan.forEach((val, key) => {
                        html += '<li>' + val.daftartindakan_nama + '</li>'
                    })

                    $('#detail-paket').append(html)
                }
            }
        } else {
            var resource = listTindakanPaket.response.tindakan
            if (typeof resource[data_tindakan] != 'undefined') {
                hargaSatuan = parseFloat(
                    resource[data_tindakan].harga_tariftindakan
                )
                persencyto = parseFloat(resource[data_tindakan].persencyto_tindakan)
            }
        }

        if (parseInt(persencyto) > 0 && parseInt(hargaSatuan) > 0) {
            hargaCyto = (hargaSatuan * persencyto) / 100
        }

        $('#tindakanpelayananform-tarif_satuan').val(
            docoHelper.convertToRupiah(hargaSatuan)
        )
        $('#tarifsatuan_hidden').val(hargaSatuan)
        $('#tindakanpelayananform-tarifcyto_tindakan').val(hargaCyto)

        if ($('#tindakanpelayananform-qty_tindakan').val() != '') {
            var data_hid_satuan = $('#tarifsatuan_hidden').val()
            if (
                $('#tindakanpelayananform-cyto_tindakan').is(':checked') &&
                parseFloat($('#tindakanpelayananform-tarifcyto_tindakan').val()) > 0
            ) {
                hargaCyto = parseFloat(
                    $('#tindakanpelayananform-tarifcyto_tindakan').val()
                )
            } else {
                hargaCyto = 0
            }

            qty = parseFloat($('#tindakanpelayananform-qty_tindakan').val())
            total =
                (parseFloat(hargaCyto) + parseFloat(data_hid_satuan)) *
                parseFloat(qty)

            $('#tindakanpelayananform-tarif_tindakan').val(
                docoHelper.convertToRupiah(total)
            )
            $('#tindakanpelayananform-text_cyto_tindakan').val(
                docoHelper.convertToRupiah(hargaCyto)
            )
            $('#tariftotal_hidden').val(total)
        }
        return false

        // Url
        var url =
            '/rajal/pemeriksaan/get-tindakan-detail?id=' +
            id +
            '&penjamin_id=' +
            penjaminId +
            '&kelaspelayanan_id=' +
            kelasPelayananId +
            '&tipe=' +
            tipe

        // Ajax
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if ((response.status = 200)) {
                    $('#tindakanpelayananform-tarif_satuan').val(0)
                    $('#tarifsatuan_hidden').val(0)
                    $('#tindakanpelayananform-tarifcyto_tindakan').val(0)
                    $('#tindakanpelayananform-text_cyto_tindakan').val(0)

                    var data = response.data
                    var hargaSatuan = 0
                    var harga = 0
                    var hargaCyto = 0
                    var persencyto = 0
                    if (tipe == 'paket') {
                        hargaSatuan = data.tindakan_data.harga_tariftindakan
                        persencyto = data.tindakan_data.persencyto_tindakan
                        $('#tipepaket_id').val(id)
                        if (data.paket_data.length > 0) {
                            var html = ''
                            for (var i = data.paket_data.length - 1; i >= 0; i--) {
                                html +=
                                    '<li>' +
                                    data.paket_data[i].daftartindakan_nama +
                                    '</li>'
                            }

                            $('#detail-paket').empty()

                            $('#detail-paket').append(html)
                        }
                    } else {
                        hargaSatuan = data.harga_tariftindakan
                        persencyto = data.persencyto_tindakan
                    }
                    if (parseInt(persencyto) > 0 && parseInt(hargaSatuan) > 0) {
                        hargaCyto = (hargaSatuan * persencyto) / 100
                    }

                    $('#tindakanpelayananform-tarif_satuan').val(
                        docoHelper.convertToRupiah(hargaSatuan)
                    )
                    $('#tarifsatuan_hidden').val(hargaSatuan)
                    $('#tindakanpelayananform-tarifcyto_tindakan').val(hargaCyto)

                    if ($('#tindakanpelayananform-qty_tindakan').val() != '') {
                        var data_hid_satuan = $('#tarifsatuan_hidden').val()
                        if (
                            $('#tindakanpelayananform-cyto_tindakan').is(':checked') &&
                            parseFloat(
                                $('#tindakanpelayananform-tarifcyto_tindakan').val()
                            ) > 0
                        ) {
                            hargaCyto = parseFloat(
                                $('#tindakanpelayananform-tarifcyto_tindakan').val()
                            )
                        } else {
                            hargaCyto = 0
                        }

                        qty = parseFloat(
                            $('#tindakanpelayananform-qty_tindakan').val()
                        )
                        total =
                            (parseFloat(hargaCyto) + parseFloat(data_hid_satuan)) *
                            parseFloat(qty)

                        $('#tindakanpelayananform-tarif_tindakan').val(
                            docoHelper.convertToRupiah(total)
                        )
                        $('#tindakanpelayananform-text_cyto_tindakan').val(
                            docoHelper.convertToRupiah(hargaCyto)
                        )
                        $('#tariftotal_hidden').val(total)
                    }
                }
            },
        })
    }
})

// When qty tindakan changed
$('#tindakanpelayananform-qty_tindakan').keyup(function (e) {
    var data_hid_satuan = $('#tarifsatuan_hidden').val()
    // Check harga satuan
    if (data_hid_satuan != '') {
        // Allow: backspace, delete, tab, escape, enter and .
        if (
            $.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
            // Allow: Ctrl+A, Command+A
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
            // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)
        ) {
            // return;
        }
        // Ensure that it is a number and stop the keypress
        if (
            (e.shiftKey || e.keyCode < 48 || e.keyCode > 57) &&
            (e.keyCode < 96 || e.keyCode > 105)
        ) {
            e.preventDefault()
        }

        // Define some variables
        let harga = 0
        let qty = 0
        let total = 0
        var hargaCyto = 0

        // Check cyto
        if (
            $('#tindakanpelayananform-cyto_tindakan').is(':checked') &&
            parseFloat($('#tindakanpelayananform-tarifcyto_tindakan').val()) > 0
        ) {
            // Assign harga
            hargaCyto = parseFloat(
                $('#tindakanpelayananform-tarifcyto_tindakan').val()
            )
        } else {
            // Assign harga
            harga = parseFloat(data_hid_satuan)
        }

        // Calculate total
        qty = $(this).val() ? parseInt($(this).val()) : 0
        total =
            (parseFloat(hargaCyto) + parseFloat(data_hid_satuan)) * parseFloat(qty)

        // Assign total
        $('#tindakanpelayananform-tarif_tindakan').val(
            docoHelper.convertToRupiah(total)
        )
        $('#tariftotal_hidden').val(total)
    }
})

// When cyto tindakan checked
$('#tindakanpelayananform-cyto_tindakan').on('change', function (e) {
    var data_hid_satuan = $('#tarifsatuan_hidden').val()
    // Check cypto
    if (
        $('#tindakanpelayananform-cyto_tindakan').is(':checked') &&
        parseFloat($('#tindakanpelayananform-tarifcyto_tindakan').val()) > 0
    ) {
        // Assign harga
        hargaCyto = parseFloat(
            $('#tindakanpelayananform-tarifcyto_tindakan').val()
        )
    } else {
        // Assign harga
        hargaCyto = 0
    }

    // Calculate total
    qty = parseFloat($('#tindakanpelayananform-qty_tindakan').val())
    total =
        (parseFloat(hargaCyto) + parseFloat(data_hid_satuan)) * parseFloat(qty)

    // Assign total
    $('#tindakanpelayananform-tarif_tindakan').val(
        docoHelper.convertToRupiah(total)
    )
    $('#tindakanpelayananform-text_cyto_tindakan').val(
        docoHelper.convertToRupiah(hargaCyto)
    )
    $('#tariftotal_hidden').val(total)
})

var listTindakanPaket = {}

var buildOptions = response => {
    $('#tindakanpelayananform-qty_tindakan').val(0)
    $('#tindakanpelayananform-tarif_satuan').val(0)
    $('#tindakanpelayananform-tarif_tindakan').val(0)
    $('#tindakanpelayananform-text_cyto_tindakan').val(0)

    // Declare options
    var options = []

    // Check paket
    if ($('#paket').is(':checked')) {
        // Initiate options
        options.push({
            id: '0',
            text: '-- Pilih paket --',
        })

        // Check paket
        if (Object.values(listTindakanPaket.response.paket).length > 0) {
            Object.values(listTindakanPaket.response.paket).forEach((val, key) => {
                options.push({
                    id: val.tipepaket_id,
                    text: val.tipepaket_nama,
                })
            })
        }
        $('#tindakan').empty().select2({ data: options })
    } else {
        options.push({
            id: '0',
            text: '-- Pilih tindakan --',
        })

        if (Object.values(listTindakanPaket.response.tindakan).length > 0) {
            Object.values(listTindakanPaket.response.tindakan).forEach(
                (val, key) => {
                    options.push({
                        id: val.daftartindakan_id,
                        text: val.daftartindakan_nama,
                    })
                }
            )
        }
        $('#tindakan').empty().select2({ data: options })
    }
}

// When paket checked
$('#paket').on('change', function (e) {
    if ($('#paket').is(':checked')) {
        var html = "<span>Detail Paket</span><ul id='detail-paket'></ul>"
        $('.detail-paket').append(html)
    } else {
        $('.detail-paket').find('span').remove()
        $('.detail-paket').find('ul').remove()
    }

    if (Object.keys(listTindakanPaket).length === 0) {
        $.ajax({
            url: '/rajal/pemeriksaan/get-tindakan-paket-session',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                listTindakanPaket = response
                buildOptions(listTindakanPaket)
            },
        })
    } else {
        buildOptions(listTindakanPaket)
    }
})

// On click tambah tindakan
$('#tambah-tindakan').on('click', function (e) {
    // Get data
    var selectTindakan = $('#tindakan').val()
    // var selectDokter = $("#iddokter_hidden").val();
    var inputJumlah = $('#tindakanpelayananform-qty_tindakan').val() || 0
    
    // Check tindakan
    if (selectTindakan != '' && selectTindakan != '0' && inputJumlah != 0) {
        $('#table-trx-tindakan  tr.first-class').hide()
        // Get count
        var countTindakan = 1
        $('.tabel-tindakan > tbody > tr')
            .not('tr.detailed-paket, tr.first-class')
            .each(function () {
                countTindakan++
            })

        // Assign some values
        var tanggalTindakan = $('#tindakanpelayananform-tgl_tindakan').val()
        var tindakan = $('#tindakan').val()
        var tindakanId = tindakan
        var tindakanNama = $('#tindakan option:selected').text()
        var daftar_tindakan_id = ''
        var tipe_paket_id = ''
        var isPaket = 0
        var isCyto = 0
        var detailedPaket = ''

        var jumlahTindakan = $('#tindakanpelayananform-qty_tindakan').val()
        var tarifSatuan = $('#tarifsatuan_hidden').val()
        var tarifTindakan = $('#tariftotal_hidden').val()
        var tarifSatuanView = $('#tindakanpelayananform-tarif_satuan').val()
        var tarifTindakanView = $('#tindakanpelayananform-tarif_tindakan').val()
        var tarifCyto = 0
        var hargaCyto = $('#tindakanpelayananform-tarifcyto_tindakan').val()
        if ($('#tindakanpelayananform-cyto_tindakan').is(':checked')) {
            isCyto = 1
            tindakanNama = tindakanNama.toString() + ' - Cyto'
        } else {
            tindakanNama = tindakanNama.toString() + ' - Non Cyto'
        }
        if (
            $('#tindakanpelayananform-cyto_tindakan').is(':checked') &&
            parseInt(hargaCyto) > 0
        ) {
            // Assign harga
            tarifCyto = $('#tindakanpelayananform-tarifcyto_tindakan').val()
        }

        if ($('#paket').is(':checked')) {
            isPaket = 1
            tipe_paket_id = tindakanId

            if ($('#detail-paket').length != 0) {
                $('#detail-paket')
                    .find('li')
                    .each(function () {
                        detailedPaket +=
                            "<tr data-id='" +
                            tindakanId +
                            "' data-count='" +
                            countTindakan +
                            "' data-is_paket='" +
                            isPaket +
                            "' data-is_cyto='" +
                            isCyto +
                            "' class='detailed-paket newly-added'><td></td><td colspan='9'>"
                        detailedPaket += '<li>' + $(this).html() + '</li>'
                        detailedPaket += '</td></tr>'
                    })
            }
        } else {
            daftar_tindakan_id = tindakanId
        }

        var inputTindakan = {
            tindakanNama: tindakanNama,
            daftar_tindakan_id: daftar_tindakan_id,
            tipe_paket_id: tipe_paket_id,
            jumlahTindakan: jumlahTindakan,
            isPaket: isPaket,
            isCyto: isCyto,
        }
        // Declare html
        var html

        // Assign html
        html +=
            "<tr data-id='" +
            tindakan +
            "' data-count='" +
            countTindakan +
            "' data-is_paket='" +
            isPaket +
            "' data-is_cyto='" +
            isCyto +
            "'>" +
            "<td class='td-no '>" +
            countTindakan +
            '</td>' +
            "<td class='daftartindakan-nama'>" +
            tindakanNama +
            '</td>' +
            "<td class='qty'>" +
            parseInt(jumlahTindakan) +
            '</td>' +
            "<td nowrap style='text-align:right;'>Rp. " +
            docoHelper.convertToRupiah(tarifSatuan) +
            '</td>' +
            "<td nowrap style='text-align:right;'>Rp. " +
            docoHelper.convertToRupiah(tarifCyto) +
            '</td>' +
            "<td nowrap style='text-align:right;' class='tarif-tindakan'>Rp. " +
            docoHelper.convertToRupiah(tarifTindakan) +
            '</td>' +
            "<td class='td-hapus'><button type='button' class='btn btn-danger btn-remove-tindakan'><i class='fa fa-remove'></i></button></td>" +
            "<input type='hidden' name='TindakanPelayananForm[" +
            increment +
            "][daftartindakan_id]' class='daftartindakan-id' value='" +
            daftar_tindakan_id +
            "' readoly='readonly'>" +
            "<input type='hidden' name='TindakanPelayananForm[" +
            increment +
            "][tipepaket_id]' class='tipepaket-id' value='" +
            tipe_paket_id +
            "' readoly='readonly'>" +
            "<input type='hidden' name='TindakanPelayananForm[" +
            increment +
            "][qty_tindakan]' class='qty' value='" +
            jumlahTindakan +
            "' readoly='readonly'>" +
            "<input type='hidden' name='TindakanPelayananForm[" +
            increment +
            "][tarif_satuan]' class='tarif_satuan' value='" +
            tarifSatuan +
            "' readoly='readonly'>" +
            "<input type='hidden' name='TindakanPelayananForm[" +
            increment +
            "][tarifcyto_tindakan]' value='" +
            tarifCyto +
            "' readoly='readonly'>" +
            "<input type='hidden' name='TindakanPelayananForm[" +
            increment +
            "][tarif_tindakan]' class='tarif-tindakan' value='" +
            tarifTindakan +
            "' readoly='readonly'>" +
            "<input type='hidden' name='TindakanPelayananForm[" +
            increment +
            "][is_paket]' class='is-paket' value='" +
            isPaket +
            "' readoly='readonly'>" +
            "<input type='hidden' name='TindakanPelayananForm[" +
            increment +
            "][cyto_tindakan]' class='is_cyto' value='" +
            isCyto +
            "' readoly='readonly'>" +
            '</tr>'
        html += detailedPaket

        var appended = true
        $('.tabel-tindakan')
            .find('tbody')
            .find('tr')
            .not($('tr.detailed-paket'))
            .each(function () {
                var elemtr = $(this)
                if (
                    $(this).data('id') == tindakan &&
                    $(this).children('input.is_cyto').val() == isCyto &&
                    $(this).data('is_paket') == isPaket
                ) {
                    appended = false
                    var qty_result =
                        parseInt(jumlahTindakan) +
                        parseInt($(this).children('input.qty').val())
                    var total_result =
                        parseInt($(this).children('input.tarif_satuan').val()) *
                        qty_result
                    $(this)
                        .children('td.tarif-tindakan')
                        .text('Rp. ' + docoHelper.convertToRupiah(total_result))
                    $(this).children('input.tarif-tindakan').val(total_result)
                    $(this).children('input.qty').val(qty_result)
                    $(this).children('td.qty').text(qty_result)
                }
            })
        if (appended) {
            increment++
            $('.tabel-tindakan').find('tbody').append(html)
        }

        /* MENAMBAHKAN TINDAKAN DI TABEL TINDAKAN KE LIST DAFTAR TINDAKAN DI PANEL BMHP */
        // Menghapus semua options pada daftar tindakan di bmhp
        $('#tindakan_obatalkes option').remove()

        // Assign prompt untuk dimasukan ke list tindakan
        var data = {
            id: '0',
            text: '-- Pilih tindakan --',
        }

        // Option untuk di append ke daftar tindakan di bmhp
        options = new Option(data.text, data.id, false, false)
        // Append ke tindakan di bmhp
        $('#tindakan_obatalkes').append(options)

        // Deklarasi variabel
        var listTindakan = []
        var listDokter = []
        var tempPembanding = []
        var options = []
        var index = 0

        // Looping untuk mendapatkan list tindakan dan dokter
        $('.tabel-tindakan > tbody > tr')
            .not('tr.detailed-paket, tr.first-class')
            .each(function () {
                var tindakanNama = $(this).find('td.daftartindakan-nama').text()
                var is_paket_tindakan = $(this).find('input.is-paket').val()
                var tindakanDokter = ''
                var is_cyto_tindakan = $(this).find('input.is_cyto').val()

                if (is_paket_tindakan == 1) {
                    tindakanDokter = $(this).find('input.tipepaket-id').val()
                } else {
                    tindakanDokter = $(this).find('input.daftartindakan-id').val()
                }
                var data = {
                    id: tindakanDokter,
                    text: tindakanNama,
                }
                var opt_tindakan = new Option(data.text, data.id, false, false)
                opt_tindakan.setAttribute('data-is_paket', is_paket_tindakan)
                opt_tindakan.setAttribute('data-is_cyto', is_cyto_tindakan)
                options = opt_tindakan
                $('#tindakan_obatalkes').append(options)
            })

        $('#paket').trigger('change')
        $('.detail-paket').find('span').remove()
        $('.detail-paket').find('ul').remove()

        $('#tindakanpelayananform-text_cyto_tindakan').val('')
        $('#tindakanpelayananform-tarifcyto_tindakan').val('')
        $('#form-tindakanrajal-tindakan').trigger('reset')
        $('#paket').trigger('change')
        appendBmhp(inputTindakan)
    } else {
        var logo =
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> &nbsp'
        if (inputJumlah == '' || inputJumlah == 0) {
            var _field_qty = $('#tindakanpelayananform-qty_tindakan')
            _field_qty.parent('div').addClass('has-error')
            _field_qty.parent('.required').addClass('has-error')
            _field_qty.parent().find('span.error').remove()
            _field_qty.after(
                '<span class="help-block error">' +
                    logo +
                    'Jumlah Tindakan Tidak Boleh Kosong' +
                    '</span>'
            )
        }
        if (selectTindakan == '' || selectTindakan == '0') {
            var _field_tindakan = $('#tindakan')
            _field_tindakan.closest('div.form-group').addClass('has-error')
            _field_tindakan.closest('div.form-group').find('span.error').remove()
            _field_tindakan
                .closest('div.form-group')
                .append(
                    '<span class="help-block error">' +
                        logo +
                        'Tindakan Harus Dipilih' +
                        '</span>'
                )
        }
        // Notify
        new PNotify({
            title: 'Proses Gagal!',
            text: 'Data yang mandatori tidak boleh kosong',
            addclass: 'alert alert-danger alert-arrow-right alert-styled-right',
            type: 'danger',
        })
    }
})

var appendBmhp = inputTindakan => {
    var paramas = inputTindakan || {}
    if (listTindakanPaket.response) {
        var listOrder = listTindakanPaket.response.tindakan
        var id = paramas.daftar_tindakan_id
        if (paramas.isPaket) {
            listOrder = listTindakanPaket.response.paket
            id = paramas.tipe_paket_id
        }
        var qtyTindakan = parseFloat(paramas.jumlahTindakan) || 0
        if (typeof listOrder[id] !== 'undefined') {
            var html = ''

            var countBmhp = 1
            $('.tabel-bmhp > tbody > tr')
                .not(' tr.first-class')
                .each(function () {
                    countBmhp++
                })

            if (listOrder[id].list_bmhp.length > 0) {
                var listTindakan = listOrder[id].list_bmhp
                $('#table-trx-bmhp  tr.first-class').hide()
                var medPackage = []
                listTindakan.forEach((val, key) => {
                    medPackage.push({
                        id: val.obatalkes_id,
                        text: val.obatalkes_nama,
                        datavalue: {
                            obatalkes_id: val.obatalkes_id,
                            obatalkes_nama: val.obatalkes_nama,
                            qty_tersedia: val.stok,
                        }
                    })
                    var sisaStok = parseFloat(val.stok)
                    var totalQty = qtyTindakan * val.qty_konversi
                    var _button =
                        "<button type='button' class='btn btn-danger btn-remove-bmhp'><i class='fa fa-remove'></i></button>"
                    var _style = 'background: #fdb7b7'
                    if (sisaStok >= totalQty && sisaStok !== 0) {
                        _button = "<i class='fa fa-lock'></i>"
                        _style = ''
                    }
                    var _findObat = $(
                        `#table-trx-bmhp > tbody > tr[data-id=${id}][data-is_paket=${paramas.isPaket}][data-mapping=1][data-obat=${val.obatalkes_id}]`
                    )
                    if (_findObat.length > 0) {
                        var _stok =
                            parseInt(_findObat.find('input.qty_oa').val()) + totalQty
                        if (sisaStok >= _stok && sisaStok !== 0) {
                            _button = "<i class='fa fa-lock'></i>"
                            _style = ''
                        }
                        _findObat.find('td.td-hapus').html(_button)
                        _findObat.attr('style', _style)
                        _findObat.find('input.qty_oa').val(_stok)
                        _findObat.find('td.qty_oa').text(_stok)
                    } else {
                        html =
                            "<tr data-id='" +
                            id +
                            "' data-is_paket='" +
                            paramas.isPaket +
                            "' data-is_cyto='" +
                            paramas.isCyto +
                            "' data-mapping='1' data-obat='" +
                            val.obatalkes_id +
                            "' style='" +
                            _style +
                            "'>" +
                            "<td class='td-no-bmhp'>" +
                            countBmhp +
                            '</td>' +
                            "<td class='nama_tindakan'>" +
                            inputTindakan.tindakanNama +
                            '</td>' +
                            '<td>' +
                            val.obatalkes_nama +
                            '</td>' +
                            "<td class='qty_oa'>" +
                            totalQty +
                            '</td>' +
                            '<td></td>' +
                            "<td nowrap style='text-align:right'>Rp. 0</td>" +
                            "<td nowrap style='text-align:right'>Rp. 0</td>" +
                            "<td class='td-hapus text-center'>" +
                            _button +
                            '</td>' +
                            "<input type='hidden' name='ObatAlkesPasienForm[" +
                            incrementObat +
                            "][daftartindakan_id]' class='tindakanid_nonpaket' value='" +
                            paramas.daftar_tindakan_id +
                            "' readoly='readonly'>" +
                            "<input type='hidden' name='ObatAlkesPasienForm[" +
                            incrementObat +
                            "][tipepaket_id]' class='tindakanid_paket' value='" +
                            paramas.tipe_paket_id +
                            "' readoly='readonly'>" +
                            "<input type='hidden' name='ObatAlkesPasienForm[" +
                            incrementObat +
                            "][obatalkes_id]' class='obatalkes_id' value='" +
                            val.obatalkes_id +
                            "' readoly='readonly'>" +
                            "<input type='hidden' name='ObatAlkesPasienForm[" +
                            incrementObat +
                            "][qty_oa]' class='qty_oa' value='" +
                            totalQty +
                            "' readoly='readonly'>" +
                            "<input type='hidden' name='ObatAlkesPasienForm[" +
                            incrementObat +
                            "][hargasatuan_oa]' value='" +
                            val.harga +
                            "' readoly='readonly'>" +
                            "<input type='hidden' name='ObatAlkesPasienForm[" +
                            incrementObat +
                            "][hargajual_oa]' class='tarif-bmhp' value='0' readoly='readonly'>" +
                            "<input type='hidden' name='ObatAlkesPasienForm[" +
                            incrementObat +
                            "][is_ditagihkan]' class='is_ditagihkan' value='0' readoly='readonly'>" +
                            "<input type='hidden' name='ObatAlkesPasienForm[" +
                            incrementObat +
                            "][tindakan_is_cyto]' class='tindakan_is_cyto' value='" +
                            paramas.isCyto +
                            "' readoly='readonly'>" +
                            "<input type='hidden' name='ObatAlkesPasienForm[" +
                            incrementObat +
                            "][tindakan_is_paket]' class='tindakan_is_paket' value='" +
                            paramas.isPaket +
                            "' readoly='readonly'>" +
                            '</tr>'
                        $('.tabel-bmhp').find('tbody').append(html)
                        countBmhp++
                    }

                    incrementObat++
                })
                resData = resData.concat(resData, medPackage)
            }
        }
        checkStokTabel()
    }
}

// On click btn remove
$(document).on('click', '.btn-remove-tindakan', function () {
    /* HAPUS ROW */
    // Remove row
    var tr_id = $(this).closest('tr').data('id')
    var tr_count = $(this).closest('tr').data('count')
    var tr_is_paket = $(this).closest('tr').data('is_paket')
    var tr_is_cyto = $(this).closest('tr').data('is_cyto')
    $(this)
        .closest('tr')
        .parent()
        .find(
            'tr.detailed-paket[data-id=' +
                tr_id +
                '][data-count=' +
                tr_count +
                '][data-is_paket=' +
                tr_is_paket +
                '][data-is_cyto=' +
                tr_is_cyto +
                ']'
        )
        .each(function () {
            $(this).remove()
        })
    $(this).closest('tr').remove()

    $('.tabel-tindakan > tbody > tr')
        .not('tr.first-class, tr.detailed-paket')
        .each(function (indx) {
            // Assign number
            $(this)
                .find('td.td-no')
                .text(indx + 1)
        })

    // Calculate total tindakan
    var totalHargaTindakan = 0

    // LLop to calculate total
    $('.tarif-tindakan').each(function () {
        // Get value
        var value = $(this).val()

        // add only if the value is number
        if (!isNaN(value) && value.length != 0) {
            // Calculate
            totalHargaTindakan += parseFloat(value)
        }
    })

    /* MENAMBAHKAN TINDAKAN DI TABEL TINDAKAN KE LIST DAFTAR TINDAKAN DI PANEL BMHP */
    // Menghapus semua options pada daftar tindakan di bmhp
    $('#tindakan_obatalkes option').remove()

    // Assign prompt untuk dimasukan ke list tindakan
    var data = {
        id: '0',
        text: '-- Pilih tindakan --',
    }

    // Option untuk di append ke daftar tindakan di bmhp
    options = new Option(data.text, data.id, false, false)

    // Append ke tindakan di bmhp
    $('#tindakan_obatalkes').append(options)

    // Deklarasi variabel
    var listTindakan = []
    var listDokter = []
    var tempPembanding = []
    var options = []
    var index = 0

    $('.tabel-tindakan > tbody > tr')
        .not('tr.first-class, tr.detailed-paket')
        .each(function () {
            var tindakanNama = $(this).find('td.daftartindakan-nama').text()
            var is_paket_tindakan = $(this).find('input.is-paket').val()
            var tindakanDokter = ''
            var is_cyto = $(this).find('input.is_cyto').val()

            if (is_paket_tindakan == 1) {
                tindakanDokter = $(this).find('input.tipepaket-id').val()
            } else {
                tindakanDokter = $(this).find('input.daftartindakan-id').val()
            }
            var data = {
                id: tindakanDokter,
                text: tindakanNama,
            }
            var opt_tindakan = new Option(data.text, data.id, false, false)
            opt_tindakan.setAttribute('data-is_paket', is_paket_tindakan)
            opt_tindakan.setAttribute('data-is_cyto', is_cyto)
            options = opt_tindakan
            $('#tindakan_obatalkes').append(options)
        })

    $('#table-trx-bmhp > tbody > tr').each(function () {
        var id = $(this).data('id')
        var is_paket = $(this).data('is_paket')
        var is_cyto = $(this).data('is_cyto')

        if (id == tr_id && is_paket == tr_is_paket && is_cyto == tr_is_cyto) {
            $(this).remove()
        }
    })

    $('.tabel-bmhp > tbody > tr')
        .not(' tr.first-class')
        .each(function (indx) {
            // Assign number
            $(this)
                .find('td.td-no-bmhp')
                .text(indx + 1)
        })

    if ($('.tabel-tindakan > tbody > tr').length === 1) {
        $('#table-trx-tindakan  tr.first-class').show()
    }

    if ($('#table-trx-bmhp > tbody > tr').length === 1) {
        $('#table-trx-bmhp  tr.first-class').show()
    }
    checkStokTabel()
})

var checkStokTabel = () => {
    var stokObat = []
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
        var _trObat = $(
            `#table-trx-bmhp > tbody > tr[data-obat=${val.obatalkes_id}]`
        )
        var _qtyTersedia = parseInt(val.qty_tersedia)
        if (_trObat.length > 0) {
            var _totalQty = 0
            _trObat.each(function () {
                var _qtyInput = parseInt($(this).find('input.qty_oa').val())
                _totalQty += _qtyInput
            })
            _button = "<i class='fa fa-lock'></i>"
            _style = ''
            if (_totalQty > _qtyTersedia) {
                var _button =
                    "<button type='button' class='btn btn-danger btn-remove-bmhp'><i class='fa fa-remove'></i></button>"
                var _style = 'background: #fdb7b7'
            }
            _trObat.attr('style', _style)
            $(
                `#table-trx-bmhp > tbody > tr[data-obat=${val.obatalkes_id}][data-mapping=1]`
            )
                .find('td.td-hapus')
                .html(_button)
        }
    })
}

// When button save clicked
$('#btn-save').on('click', function (e) {
    // New window
    var form_data = []
    var disabledFormPegawai = $('div.form-tindakan-bmhp')
        .find(':input:disabled')
        .removeAttr('disabled')
    var data_tindakanbmhp = $('div.form-tindakan-bmhp')
        .find(':input')
        .serializeArray()
    disabledFormPegawai.attr('disabled', 'disabled')
    form_data_pegawai = form_data.concat(data_tindakanbmhp)
    var data_tindakan = $('#table-trx-tindakan').find(':input').serializeArray()
    form_data_with_tindakan = form_data_pegawai.concat(data_tindakan)
    var data_bmhp = $('#table-trx-bmhp').find(':input').serializeArray()
    form_data_all = form_data_with_tindakan.concat(data_bmhp)
    var stokObat = []
    if (resData.length > 0) {
        resData.forEach((val, key) => {
            stokObat[val.datavalue.obatalkes_id] = val
        })
    }
    
    var _validationStok = false
    var _tmpValidObat = []
    var _namaObat = null
    $('#table-trx-bmhp > tbody > tr').each(function () {
        var _obatAlkes = $(this).find('input.obatalkes_id').val()
        var _qtyObat = $(this).find('input.qty_oa').val()

        if (typeof _qtyObat !== 'undefined') {
            _qtyObat = parseInt(_qtyObat)
            if (typeof _tmpValidObat[_obatAlkes] === 'undefined') {
                _tmpValidObat[_obatAlkes] = 0
            }
            _tmpValidObat[_obatAlkes] += _qtyObat
            if (typeof stokObat[_obatAlkes] !== 'undefined') {
                var _currentStok = stokObat[_obatAlkes].qty_tersedia
                if (_currentStok <= _tmpValidObat[_obatAlkes]) {
                    _namaObat = stokObat[_obatAlkes].obatalkes_nama
                    _validationStok = true
                    return false
                }
            } else {
                _namaObat = $(this).find('td:eq(2)').text()
                _validationStok = true
                return false
            }
        }
    })

    if (_validationStok) {
        docoNotification(
            'error',
            'Proses Gagal!',
            `Stok Obat ${_namaObat} tidak mencukupi`
        )
        return false
    }

    if (data_tindakan.length == 0 && data_bmhp.length == 0) {
        new PNotify({
            title: 'Gagal',
            text: 'Tidak Ada Data Yang Disimpan',
            addclass: 'alert alert-danger alert-arrow-right alert-styled-right',
            type: 'danger',
        })

        return
    }
    $(this).docoForm('click', {
        url: '/rajal/pemeriksaan/tindakan?id=' + pendaftaran_id,
        method: 'POST',
        dataType: 'JSON',
        data: form_data_all,
        success: function (response) {
            $('#btn-print').show()
            if ((response.status = 200)) {
                new PNotify({
                    title: 'Berhasil',
                    text: 'Data berhasil disimpan',
                    addclass:
                        'alert alert-success alert-arrow-right alert-styled-right',
                    type: 'success',
                })

                $('#table-trx-tindakan').find('tbody').empty()
                $('#table-trx-bmhp').find('tbody').empty()
                resObat = []
                resAlkes = []
                $('#content-cppt').docoLoad({
                    url:
                        '/rajal/pemeriksaan/tindakan?id=' +
                        pendaftaran_id +
                        '&pasien_id=' +
                        pasien_id +
                        '&kelaspelayanan_id=' +
                        kelaspelayanan_id,
                    dataType: 'html',
                    success: function (data) {},
                })
            } else {
                new PNotify({
                    title: 'Gagal',
                    text: 'Data gagal disimpan',
                    addclass:
                        'alert alert-danger alert-arrow-right alert-styled-right',
                    type: 'danger',
                })
            }
        },
    })
})
/*=====  End of tindakan  ======*/

/*==================================
=            bmhp alkes            =
==================================*/
$('#bmhpalkes_select2').on('change', function (e) {
    // var data_bmhpalkes = $(this).select2("data");
    var data_bmhpalkes = $(this).val()

    // console.log(data_bmhpalkes);
    var obatalkes_id = data_bmhpalkes
    var submit = $('#form-bmhp').find(':submit')

    var url =
        '/rajal/pemeriksaan/get-obatalkes-detail?obatalkes_id=' + obatalkes_id
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        beforeSend: function () {
            submit.prop('disabled', true)
        },
        success: function (response) {
            if ((response.status = 200)) {
                let data_obatalkes = response.data
                let harga_satuan = data_obatalkes.hargaygdipakai
                let harga_netto = data_obatalkes.harganetto
                let obatalkes_nama = data_obatalkes.obatalkes_nama

                $('#obatalkespasienform-obatalkes_nama').val(obatalkes_nama)
                $('#obatalkespasienform-hargasatuan_oa').val(harga_satuan)
                $('#obatalkespasienform-harganetto_oa').val(harga_netto)
            } else {
                // console.log(response.message)
            }
        },
    }).done(function () {
        submit.prop('disabled', false)
    })
})

// When tindakan changed
$('#tindakan_obatalkes').on('change', function (e) {
    // Get data from select2
    // var data_tindakan = $(this).select2("data");
    var data_tindakan = $(this).val()

    // Tindakan id
    var id = data_tindakan

    // Check id
    if (id != null) {
        // Split
        var tempData = id.split('-')
        var tindakan = tempData[0]
        var dokter = tempData[1]

        // Assign datas
        $('#daftartindakan-id').val(tindakan)
        $('#pegawai-id').val(dokter)
    }
})

$('#btn-print').on('click', function (e) {
    e.preventDefault()
    const pendaftaranId = $(this).attr('data-id')
    // console.log(pendaftaranId)
    window.open(
        '/rajal/pemeriksaan/cetak-tindakan?id=' + pendaftaranId,
        '_blank'
    )
})

var buildOptObat = response => {
    let obatalkes_select2 = $('#obatalkes_select2')
    var data = {
        id: '0',
        text: '-- Pilih obat/alkes --',
    }

    options = new Option(data.text, data.id, false, false)

    obatalkes_select2.append(options)
    
    if (response.length > 0) {
        const data = response
        const count = data.length
        if (parseInt(count) > 0) {
            data.forEach(val => {
                var dis_obat = ''
                if (val.qty_tersedia < 1) {
                    dis_obat = 'disabled'
                }
                obatalkes_select2.append(
                    '<option ' +
                        dis_obat +
                        ' value=' +
                        `${val.obatalkes_id}` +
                        ' data-stok=' +
                        `${val.qty_tersedia}` +
                        ' data-harga=' +
                        `${val.hargaygdipakai}` +
                        '>' +
                        `${val.obatalkes_namalain}` +
                        ' - (' +
                        `${val.qty_tersedia}` +
                        ')</option>'
                )
            })
        } else {
            obatalkes_select2.empty()
        }
    }
}

$('#obatalkes_select2').on("select2:select", function (e) {
    var data = e.params.data;
    var datavalue = data.datavalue;
    const stok = datavalue.qty_tersedia;
    const id = data.id;
    
    var harga = datavalue.hargaygdipakai;
    var total = 0
    var jml = 0
    if ($('.jml').val() != '') {
        jml = parseInt($('.jml').val())
    }
    var harga_rupiah = 0
    var total_harga_rupiah = 0
    
    if (harga) {
        var regex_titik = /\./g
        var harga_display = 0
        var harga_koma = 0
        harga_display = harga.toString().replace(regex_titik, ',')
        harga_koma = harga_display.toString().split(',')
        if (harga_koma.length > 1) {
            harga_rupiah = docoHelper.convertToRupiah(harga_koma[0])
            harga_rupiah = harga_rupiah.toString() + ',' + harga_koma[1]
        } else {
            harga_rupiah = docoHelper.convertToRupiah(harga_koma[0])
        }

        if (jml != 0) {
            total = harga * jml
            var total_harga_display = 0
            var total_harga_koma = 0
            total_harga_display = total.toString().replace(regex_titik, ',')
            total_harga_koma = total_harga_display.toString().split(',')
            
            if (total_harga_koma.length > 1) {
                total_harga_rupiah = docoHelper.convertToRupiah(total_harga_koma[0])
                total_harga_rupiah =
                    total_harga_rupiah.toString() + ',' + total_harga_koma[1]
            } else {
                total_harga_rupiah = docoHelper.convertToRupiah(total_harga_koma[0])
            }
        } else {
            total = harga
        }

        $('#obatalkespasienform-hargasatuan_oa').val(docoHelper.convertToRupiah(harga))
        $('.jml_tarif').val(total_harga_rupiah)
        $('#jumlahtarifobat_hidden').val(total)
        $('#harga_obat_satuan_hidden').val(harga)
    } else {
        $('.jml_tarif').val(docoHelper.convertToRupiah(0))
        $('#jumlahtarifobat_hidden').val(0)
        $('#harga_obat_satuan_hidden').val(0)
    }

    if (stok) {
        currentStok = stok
        $('.tabel-bmhp')
            .find('tbody')
            .find('tr')
            .each(function () {
                if (id == $(this).children('input.obatalkes_id').val()) {
                    currentStok =
                        currentStok - $(this).children('input.qty_oa').val()
                }
            })
        $('#stok_obat_tersedia').val(currentStok)
    }
    $('#obatalkespasienform-qty_oa').trigger('keyup')
})

$('.jml').on('keyup', function () {
    var elem_err = $('#errorJumlah')
    elem_err.empty()
    var jml = $(this).val()
    const harga = $('#obatalkespasienform-hargasatuan_oa').val()
    var stok = parseInt($('#stok_obat_tersedia').val())
    var total = 0
    if (harga && jml != '' && jml != 0) {
        total = docoHelper.convertToAngka(harga) * jml
        var total_harga_rupiah = 0
        var total_harga_display = 0
        var total_harga_koma = 0
        var regex_titik = /\./g
        total_harga_display = total.toString().replace(regex_titik, ',')
        total_harga_koma = total_harga_display.toString().split(',')
        if (total_harga_koma.length > 1) {
            total_harga_rupiah = docoHelper.convertToRupiah(total_harga_koma[0])
            total_harga_rupiah =
                total_harga_rupiah.toString() + ',' + total_harga_koma[1]
        } else {
            total_harga_rupiah = docoHelper.convertToRupiah(total_harga_koma[0])
        }
        $('.jml_tarif').val(docoHelper.convertToRupiah(total))
        $('#jumlahtarifobat_hidden').val(total)
    } else {
        $('.jml_tarif').val(docoHelper.convertToRupiah(0))
        $('#jumlahtarifobat_hidden').val(0)
    }
    if (parseInt(jml) > stok) {
        $(this).val(0)
        $('.jml_tarif').val(docoHelper.convertToRupiah(0))
        $('#jumlahtarifobat_hidden').val(0)
        var logo =
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> &nbsp'
        elem_err.html(
            '<span class="help-block error">' +
                logo +
                'Jumlah Tidak Boleh Melebihi Stok' +
                '</span>'
        )
    }
})

// On click tambah obatalkes
$('#tambah-obatalkes').on('click', function (e) {
    // Get data
    var radioPemakaian = $('#temp_obat').val()
    var selectObat = $('#obatalkes_select2').val()
    var inputJumlah = $('#obatalkespasienform-qty_oa').val()

    var stok = parseInt($('#stok_obat_tersedia').val())
    if (parseInt(inputJumlah) > stok) {
        var logo =
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> &nbsp'
        $('#errorJumlah').html(
            '<span class="help-block error">' +
                logo +
                'Jumlah Tidak Boleh Melebihi Stok' +
                '</span>'
        )
        return
    }
    // Cek data diatas
    if (
        selectObat != '' &&
        inputJumlah != '' &&
        inputJumlah != 0
    ) {
        $('#table-trx-bmhp  tr.first-class').hide()
        // Assign some values
        const tanggalTindakan = $('#tindakanpelayananform-tgl_tindakan').val()
        const tindakan = $('#tindakan_obatalkes').val()
        const obat_alkes = $('#obatalkes_select2').val()
        const jumlah = $('#obatalkespasienform-qty_oa').val()

        var tindakanNama = '-'
        if (tindakan != '' && tindakan != '0') {
            tindakanNama = $('#tindakan_obatalkes option:selected').text()
        }
        
        var val_is_ditagihkan = 0
        var text_harga_satuan = '0'
        var text_total_tarif = '0'
        var hargaSatuanOA = 0
        // Check paket
        if ($('#tagih_pasien').is(':checked')) {
            // Assign checklist
            var checklist = '✓'
            val_is_ditagihkan = 1

            hargaSatuanOA = $('#harga_obat_satuan_hidden').val()
            text_harga_satuan = $('#obatalkespasienform-hargasatuan_oa').val()
            text_total_tarif = $('#obatalkespasienform-jumlah_tarif').val()
            var jmlTarif = $('#jumlahtarifobat_hidden').val()
        } else {
            // Assign checklist
            var checklist = '-'

            // Make 0
            var jmlTarif = 0
        }

        var countBmhp = 1
        $('.tabel-bmhp > tbody > tr')
            .not(' tr.first-class')
            .each(function () {
                countBmhp++
            })

        var id_daftartindakan = ''
        var id_paket = ''
        var val_tindakan = 0
        if (tindakan != '' && tindakan != '0') {
            val_tindakan = tindakan
        }
        var text_obat_alkes = ''
        var selected_obat_alkes = $('#obatalkes_select2 option:selected').text()
        if (
            obat_alkes != '' &&
            obat_alkes != '0' &&
            selected_obat_alkes.length != 0
        ) {
            split_text_obat = selected_obat_alkes.split('-')
            if (split_text_obat[0]) {
                text_obat_alkes = split_text_obat[0]
            }
        }

        var isPaket = 0
        if ($('#tindakan_obatalkes option:selected').data('is_paket') == 1) {
            isPaket = 1
            id_paket = val_tindakan
        } else {
            id_daftartindakan = val_tindakan
        }
        var isCyto = 0
        if ($('#tindakan_obatalkes option:selected').data('is_cyto') == 1) {
            isCyto = 1
        }

        // Declare html
        var html
        var isMapping = 0
        // Assign html
        html +=
            "<tr data-id='" +
            val_tindakan +
            "' data-is_paket='" +
            isPaket +
            "' data-is_cyto='" +
            isCyto +
            "' data-obat='" +
            obat_alkes +
            "'>" +
            "<td class='td-no-bmhp'>" +
            countBmhp +
            '</td>' +
            "<td class='nama_tindakan'>" +
            tindakanNama +
            '</td>' +
            '<td>' +
            text_obat_alkes +
            '</td>' +
            "<td class='qty_oa'>" +
            jumlah +
            '</td>' +
            '<td>' +
            checklist +
            '</td>' +
            "<td nowrap style='text-align:right'>Rp. " +
            text_harga_satuan +
            '</td>' +
            "<td nowrap style='text-align:right'>Rp. " +
            text_total_tarif +
            '</td>' +
            "<td class='td-hapus'><button type='button' class='btn btn-danger btn-remove-bmhp'><i class='fa fa-remove'></i></button></td>" +
            "<input type='hidden' name='ObatAlkesPasienForm[" +
            incrementObat +
            "][daftartindakan_id]' class='tindakanid_nonpaket' value='" +
            id_daftartindakan +
            "' readoly='readonly'>" +
            "<input type='hidden' name='ObatAlkesPasienForm[" +
            incrementObat +
            "][tipepaket_id]' class='tindakanid_paket' value='" +
            id_paket +
            "' readoly='readonly'>" +
            "<input type='hidden' name='ObatAlkesPasienForm[" +
            incrementObat +
            "][obatalkes_id]' class='obatalkes_id' value='" +
            obat_alkes +
            "' readoly='readonly'>" +
            "<input type='hidden' name='ObatAlkesPasienForm[" +
            incrementObat +
            "][qty_oa]' class='qty_oa' value='" +
            jumlah +
            "' readoly='readonly'>" +
            "<input type='hidden' name='ObatAlkesPasienForm[" +
            incrementObat +
            "][hargasatuan_oa]' value='" +
            hargaSatuanOA +
            "' readoly='readonly'>" +
            "<input type='hidden' name='ObatAlkesPasienForm[" +
            incrementObat +
            "][hargajual_oa]' class='tarif-bmhp' value='" +
            jmlTarif +
            "' readoly='readonly'>" +
            "<input type='hidden' name='ObatAlkesPasienForm[" +
            incrementObat +
            "][is_ditagihkan]' class='is_ditagihkan' value='" +
            val_is_ditagihkan +
            "' readoly='readonly'>" +
            "<input type='hidden' name='ObatAlkesPasienForm[" +
            incrementObat +
            "][tindakan_is_cyto]' class='tindakan_is_cyto' value='" +
            isCyto +
            "' readoly='readonly'>" +
            "<input type='hidden' name='ObatAlkesPasienForm[" +
            incrementObat +
            "][tindakan_is_paket]' class='tindakan_is_paket' value='" +
            isPaket +
            "' readoly='readonly'>" +
            '</tr>'
        var appended = true
        $('.tabel-bmhp')
            .find('tbody')
            .find('tr')
            .each(function () {
                var elemtr = $(this)
                // console.log($(this).children());
                if (
                    $(this).data('id') == val_tindakan &&
                    $(this).data('is_paket') == isPaket &&
                    $(this).data('is_cyto') == isCyto &&
                    $(this).children('input.obatalkes_id').val() == obat_alkes &&
                    $(this).children('input.is_ditagihkan').val() ==
                        val_is_ditagihkan &&
                    typeof $(this).data('mapping') === 'undefined'
                ) {
                    appended = false
                    var _hargaSatuan =
                        parseFloat($(this).find('input.tarif-bmhp').val()) /
                        parseFloat($(this).children('input.qty_oa').val())
                    var qty_result =
                        parseFloat(jumlah) +
                        parseFloat($(this).children('input.qty_oa').val())
                    var _totalHarga = docoHelper.convertToRupiah(
                        _hargaSatuan * qty_result
                    )
                    $(this).find('td:eq(6)').text(`Rp. ${_totalHarga}`)
                    $(this).children('input.qty_oa').val(qty_result)
                    $(this)
                        .children('input.tarif-bmhp')
                        .val(_hargaSatuan * qty_result)
                    $(this).children('td.qty_oa').text(qty_result)
                }
            })
        if (appended) {
            // Append
            incrementObat++
            $('.tabel-bmhp').find('tbody').append(html)
        }

        // Calculate total tindakan
        var totalHargaTindakan = 0

        // LLop to calculate total
        $('.tarif-bmhp').each(function () {
            // Get value
            var value = $(this).val()

            // add only if the value is number
            if (!isNaN(value) && value.length != 0) {
                // Calculate
                totalHargaTindakan += parseFloat(value)
            }
        })

        var _valPemakaian = $('.jenis_pemakaian:checked').val()
        $('#obatalkes_select2').val('').trigger('change')
        $('#tindakan_obatalkes').val('').trigger('change')
        $('#form-tindakanrajal-bmhp').trigger('reset')
        $('#tagih_pasien').prop('checked', true)
        checkStokTabel()
        $(`.jenis_pemakaian[value=${_valPemakaian}]`)
            .prop('checked', true)
            .trigger('change')
    } else {
        var logo =
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> &nbsp'
        if (!$("input:radio[name='ObatAlkesPasienForm[obat]']").is(':checked')) {
            var _field_radioobat = $('#obatalkespasienform-obat')
            _field_radioobat.parent().find('span.error').remove()
            _field_radioobat.after(
                '<span class="help-block error">' +
                    logo +
                    'Jenis Pemakaian Harus Dipilih' +
                    '</span>'
            )
        }
        if (selectObat == '') {
            var _field_obatalkes = $('#obatalkes_select2')
            _field_obatalkes.parent('div').addClass('has-error')
            _field_obatalkes.parent('.required').addClass('has-error')
            _field_obatalkes.parent().find('span.error').remove()
            _field_obatalkes.after(
                '<span class="help-block error">' +
                    logo +
                    'Obat/Alkes Harus Dipilih' +
                    '</span>'
            )
        }

        if (inputJumlah == '' || inputJumlah == 0) {
            var _field_jumlah = $('#obatalkespasienform-qty_oa')
            _field_jumlah.parent('div').addClass('has-error')
            _field_jumlah.parent('.required').addClass('has-error')
            _field_jumlah.parent().find('span.error').remove()
            _field_jumlah.after(
                '<span class="help-block error">' +
                    logo +
                    'Jumlah Tidak Boleh Kosong' +
                    '</span>'
            )
        }
        // Notify
        new PNotify({
            title: 'Proses Gagal!',
            text: 'Data yang mandatori tidak boleh kosong',
            addclass: 'alert alert-danger alert-arrow-right alert-styled-right',
            type: 'danger',
        })
    }
})

// On click btn remove
$(document).on('click', '.btn-remove-bmhp', function () {
    // Get obat id
    var id = $(this).closest('tr').find('.obatalkes_id').val()
    var jumlah = $(this).closest('tr').find('.qty_oa').val()
    var ruangan_id = $('#ruangan_id').val()

    // Remove row
    $(this).closest('tr').remove()
    $('.tabel-bmhp > tbody > tr')
        .not(' tr.first-class')
        .each(function (indx) {
            // Assign number
            $(this)
                .find('td.td-no-bmhp')
                .text(indx + 1)
        })

    // Calculate total tindakan
    var totalHargaTindakan = 0

    // LLop to calculate total
    $('.tarif-bmhp').each(function () {
        // Get value
        var value = $(this).val()

        // add only if the value is number
        if (!isNaN(value) && value.length != 0) {
            // Calculate
            totalHargaTindakan += parseFloat(value)
        }
    })
    if ($('#table-trx-bmhp > tbody > tr').length === 1) {
        $('#table-trx-bmhp  tr.first-class').show()
    }
    checkStokTabel()
})

/*=====  End of bmhp alkes  ======*/

$(document).ready(function(){
    generatetObat();
    $('.jenis_pemakaian').change(function () {
        $('#obatalkes_select2 option').remove()
        generatetObat();
    });
    $('#depo_id').change(function () {
        $('#obatalkes_select2 option').remove()
        generatetObat();
    })
});

function generatetObat() {
    var jenis_id = $('.jenis_pemakaian:checked').val();
    var penjaminId = $('#penjamin_id').val()
    var depo_id = $("#depo_id").val();
    var urlAjax = `/rajal/pemeriksaan/get-obat-alkes-by-jenis?jenis=${jenis_id}&penjamin_id=${penjaminId}&ruangan_id=${depo_id}`
    $("#obatalkes_select2").docoPaginationSelec2(
        config = {
            placeholder : '-- Pilih Obat/Alkes--',     
            _api : urlAjax,
            ajax: {
                processResults: function (data, params) {
                    var _results = data.result;
                    // console.log(_results);
                    _results.forEach((val, key) => {
                        resData.push(val)
                    })
                    params.page = params.page || 1;
                    return {
                    results: data.result,
                        pagination: {
                            more: data.pagination.more
                                    }
                    }
                },
            }
        }
    );
}