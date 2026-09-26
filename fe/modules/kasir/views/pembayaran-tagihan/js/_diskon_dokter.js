/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

// Global Variable

var tmpDiskonDokter = [];
var input = document.getElementById("jasa_dokter");
var diskonDeleted = 0;

function isNotEmpty(list_jasa_dokter) {
    for(var key in list_jasa_dokter) {
        if(list_jasa_dokter.hasOwnProperty(key))
            return true;
    }
    return false;
}

$(document).ready(function() {

    generateTableDiskonDokter()
    $("#dokter_id").select2("val", "");

    diskon_dokter = docoHelper.convertToAngka((typeof $("#diskon-dokter").val() == 'undefined') ? 0 : $("#diskon-dokter").val())
    var _diskon = diskon_dokter > 0 ? diskon_dokter : 0
    $("#diskon-dokter").val(docoHelper.convertToRupiah(_diskon > 0 ? _diskon : 0));
    
    
    $("#dokter_id").select2({
        placeholder: "-- Cari Dokter --",
        data: data
    })

    $('#dokter_id').on('change', function(e){
        // reset field
        $("#jasa_dokter").val("");
        var dok_id = $('#dokter_id').val()
        if(dok_id === "") {
            return true;
        }

        var nama_dokter = $('#dokter_id').children("option:selected").text();

        if (isNotEmpty(list_jasa_dokter)) {
            if(typeof list_jasa_dokter[dok_id] != 'undefined') {
                $("#jasa_dokter").val(docoHelper.convertToRupiah(list_jasa_dokter[dok_id]));
            }
            else {
                docoNotification("warning", "Data Tidak Ada!", "Dokter "+nama_dokter+" tidak memiliki jasa dokter!");
                return false
            }
            // $.each(list_jasa_dokter, function(k, v) {
            //     if(k === dok_id){
            //         $("#jasa_dokter").val(docoHelper.convertToRupiah(v));
            //     }
            //     else {
            //         docoNotification("warning", "Data Tidak Ada!", "Dokter "+nama_dokter+" tidak memiliki jasa dokter!");
            //         return false
            //     }
            // })
        }else{
            docoNotification("warning", "Data Tidak Ada!", "Dokter "+nama_dokter+" tidak memiliki jasa dokter!");
            return false
        }
        $("#diskon").val(0);
        $("#nominal").val(0);
    })

    $('#diskon').on('input', function(e){
        e.preventDefault()
        var _jasa = docoHelper.convertToAngka($('#jasa_dokter').val())
        var _diskon = docoHelper.convertToAngka($(this).val())
        if (_diskon > _jasa) {
            $(this).val(0)
            docoNotification("error", "Data Tidak Valid!", "Diskon Tidak Boleh Lebih dari Jasa Dokter!");
            return false
        }
    })

    $('#diskon').on('change', function(e){
        e.preventDefault()
        var _jasa = docoHelper.convertToAngka($('#jasa_dokter').val())
        var _diskon = docoHelper.convertToAngka($(this).val())
        if (_diskon > _jasa) {
            $(this).val(0)
            docoNotification("error", "Data Tidak Valid!", "Diskon Tidak Boleh Lebih dari Jasa Dokter!");
            return false
        }
    })

    $('#nominal').on('input', function(e){
        e.preventDefault()
        var _jasa = docoHelper.convertToAngka($('#jasa_dokter').val())
        var _nominal = docoHelper.convertToAngka($(this).val())
        if (_nominal > _jasa) {
            $(this).val(0)
            docoNotification("error", "Data Tidak Valid!", "Nominal Tidak Boleh Lebih dari Jasa Dokter!");
            return false
        }
    })

    $('#nominal').on('change', function(e){
        e.preventDefault()
        var _jasa = docoHelper.convertToAngka($('#jasa_dokter').val())
        var _nominal = docoHelper.convertToAngka($(this).val())
        if (_nominal > _jasa) {
            $(this).val(0)
            docoNotification("error", "Data Tidak Valid!", "Nominal Tidak Boleh Lebih dari Jasa Dokter!");
            return false
        }

        //Solusi untuk mencegah diskon dokter tidak bulat
         nominalRounded = docoHelper.calculateRounding(_nominal)
         $('#nominal').val(nominalRounded.nominal_round)
    })

    if($("#is_persentase").is(":checked")) {
        $("#is_persentase").val(1);
        $("#nominal").prop("readonly", true);
        $("#diskon").prop("readonly", false);
        $("#nominal").val(0)
        $("#diskon").val(0)
    }
    else {
        $("#is_persentase").val(0);
        $("#nominal").prop("readonly", false);
        $("#diskon").prop("readonly", true);
        $("#nominal").val(0)
        $("#diskon").val(0)
        $("#diskon").on('keyup', function(){
            var dokter_id = $("#dokter_id").val();
            if(typeof dokter_id == 'undefined' || dokter_id == "") {
                docoNotification("warning", "Peringatan", "Dokter Harus di Pilih!");
                $("#diskon").val(null);
                $("#nominal").val(null);
                return false;
            }

            var diskon = docoHelper.convertToAngka($("#diskon").val());
            $("#nominal").val(docoHelper.convertToRupiah(diskon));
        });
    }

    $("#is_persentase").on('change', function(){
        if($(this).is(":checked")) {
            $("#is_persentase").val(1);
            $("#nominal").prop("readonly", true);
            $("#diskon").prop("readonly", false);
            $("#nominal").val(0)
            $("#diskon").val(0)
            $("#diskon").on('keyup', function(){
                if($(this).val() > 100) {
                    docoNotification("warning", "Peringatan", "Persentase Maksimal 100% !");
                    $("#diskon").val(null);
                    $("#nominal").val(null);
                    return false;
                }

                var jasa_dokter = docoHelper.convertToAngka($("#jasa_dokter").val());
                var diskon = $(this).val()/100 * jasa_dokter; 
                $("#nominal").val(docoHelper.convertToRupiah(diskon));
            });
        }
        else {
            $("#is_persentase").val(0);
            $("#diskon").val(null).trigger('change');
            $("#nominal").val(null).trigger('change');
            $("#nominal").prop("readonly", false);
            $("#diskon").prop("readonly", true);
            $("#nominal").val(0)
            $("#diskon").val(0)
        }
    });

    // for save data table temporary
    $('#simpan-table-diskon-dokter').on('click', function(e){
        e.preventDefault()
        var input = {
            dokter_id : $('#dokter_id').val(), // data yg di input ke database
            label_dokter : $('#dokter_id').children("option:selected").text(), // data yg di tampilkan di tabel temporary
            jasa_dokter : $('#jasa_dokter').val(), // data yg di input ke database
            diskon : $('#diskon').val(), // data yg di input ke database
            is_persentase: $("#is_persentase").val(),
            diskon_angka : docoHelper.convertToAngka($('#nominal').val()),
            nominal : $('#nominal').val(),
            alasan : $('#alasan').val(),
        }

        diskon_dokter = parseInt(diskon_dokter) + docoHelper.convertToAngka($('#nominal').val());
        if($("#is_persentase").val() == 1) {
            if ($('#diskon').val() == null || $('#diskon').val() == "" || $('#diskon').val() == 0) {
                docoNotification("error", "Nominal Diskon Belum di Input!", "harus input nominal yang diskon terlebih dahulu!");
                return false
            }
        }
        else {
            if ($('#nominal').val() == null || $('#nominal').val() == "" || $('#nominal').val() == 0) {
                docoNotification("error", "Nominal Belum di Input!", "harus input nominal yang diskon terlebih dahulu!");
                return false
            }
        }
        
        if ($('#dokter_id').val() == null || $('#dokter_id').val() == "") {
            docoNotification("error", "Data Dokter Belum Dipilih!", "pilih salah satu dokter!");
            return false
        }
        
        if ($('#jasa_dokter').val() == null || $('#jasa_dokter').val() == "") {
            docoNotification("error", "Jasa Dokter Belum Ada!", "Mohon Cek Kembali!");
            return false
        }
        
        if (docoHelper.convertToAngka($('#diskon').val() < 0 )) {
            docoNotification("error", "Nominal Diskon Tidak Boleh Minus!", "harus input nominal lebih dari 0 (nol)!");
            return false
        }
        
        if (tmpDiskonDokter[input.dokter_id]) {
            docoNotification("error", "Data Dokter Sudah Ada!", "tidak boleh menginputkan data dokter yang sama!");
            return false
        }
        
        var _jasa = docoHelper.convertToAngka($('#jasa_dokter').val())
        // var _diskon = ($("#is_persentase").val() == 0) ? docoHelper.convertToAngka($('#nominal').val()) : docoHelper.convertToAngka($('#diskon').val())
        var _diskon = docoHelper.convertToAngka($('#nominal').val())
        
        if (_diskon > _jasa) {
            $("#diskon").val(0)
            diskon_dokter = 0
            docoNotification("error", "Data Tidak Valid!", "Diskon Tidak Boleh Lebih dari Jasa Dokter!");
            return false
        }
        
        var total_tagihan = docoHelper.convertToAngka($('#total_tagihan').val())
        var diskon_total = docoHelper.convertToAngka($('#total_diskon').val())
        var jum_total_diskon = parseFloat(docoHelper.convertToAngka(diskon_total) + docoHelper.convertToAngka(diskon_dokter))
        
        if (jum_total_diskon > total_tagihan) {
            if($("#is_persentase").val() == 1) {
                $('#nominal').val(0)
            }
            else {
                $('#diskon').val(0)
            }

            // total_diskon = 0
            docoNotification("error", "Data Tidak Valid!", "Diskon Total tidak boleh lebih dari Total Tagihan");
            return false
        }
        
        tmpTableDiskonDokter.push(input)
        generateTableDiskonDokter()
        $("#diskon-dokter").val(docoHelper.convertToRupiah(diskon_dokter));
        var _tagihan = docoHelper.convertToAngka($('#tagihan_pasien').html())
        //Diskon Persentasi 
        // var tmp_tagihan = _tagihan - docoHelper.convertToAngka($('#diskon').val())
        // $('#tagihan_pasien').html(docoHelper.convertToRupiah(tmp_tagihan))

        // reset field
        $("#jasa_dokter").val("");
        $("#diskon").val("");
        $("#alasan").val("");
        $('#dokter_id').val(null).trigger('change');
        // $('#total_dibayar').trigger('change');
        $("#is_persentase").prop("checked", false).change();
        
        let diskonDokterTotal = 0
        if (tmpTableDiskonDokter.length > 0) {
            tmpTableDiskonDokter.forEach((val, key) => {
                diskonDokterTotal += parseFloat(val.diskon_angka)
            })
        }
        tagihanPasien = docoHelper.convertToAngka($('#tagihan_pasien').html())
        if (diskonDokterTotal > 0) {
            // if(_totalDijamin > 0){
            //     $("#subsidi-asuransi").val(docoHelper.convertToRupiah(_totalDijamin - diskonDokterTotal)).trigger('change')
            // } 
            $('#tagihan_pasien').html(docoHelper.convertToRupiah(tagihanPasien - diskonDokterTotal)).trigger('change')
            $('#total_sisa_piutang').val(docoHelper.convertToRupiah(tagihanPasien - diskonDokterTotal)).trigger('change')
        }
    })
    
    input.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            document.getElementById("simpan-table-diskon-dokter").click();
        }
    });
});

// for generate table from input form data
function generateTableDiskonDokter(){
    $('.isi-table').hide()
    $('#table-diskon-dokter > tbody > tr').not('tr.isi-table').remove()
    var _html = ""
    var no = 1;
    var totaldiskon = 0;
    tmpDiskonDokter = [];
    tmpTableDiskonDokter.forEach(function (val, key) {
    
    var keterangan_diskon_persen = (val.is_persentase == 1) ? ' (%)' : '';
    var keterangan_diskon_rupiah = (val.is_persentase == 1) ? '' : 'Rp. ';
    // var _nominal = (val.is_persentase == 1) ? val.diskon : val.nominal;
    var _nominal = val.nominal;

    var _alasan = val.alasan;
    var clean_alasan = _alasan.replace(/(<([^>]+)>)/ig,"");

    totaldiskon = parseInt(totaldiskon) + docoHelper.convertToAngka(val.nominal);
        
    tmpDiskonDokter[val.dokter_id] = val;

        _html += `
        <tr>
            <td>${no++}.</td>
            <td>${val.label_dokter}</td>
            <td>Rp. ${val.jasa_dokter}</td>`+
            // <td>${keterangan_diskon_rupiah + val.diskon + keterangan_diskon_persen}</td>
            `<td>Rp. ${_nominal}</td>
            <td>${clean_alasan}</td>
            <td><button type='button' data-id='${key}' class='delete-table-diskon-dokter btn btn-danger btn-labeled btn-xs delete btn-block'><b><i class="fa fa-trash"></i></b>Hapus </button></td>
        </tr>
        `
    });
    $('#table-diskon-dokter > tbody').append(_html);
    diskon_dokter = totaldiskon;
    // for delete data one by one in table temporary
    $(`.delete-table-diskon-dokter`).on('click', function(e){
        e.preventDefault()
        diskonDokterTotal = 0
        var tmpID = $(this).attr('data-id')
        var _tagihan = docoHelper.convertToAngka($('#tagihan_pasien').html())
        if (tmpTableDiskonDokter.length > 0) {
            tmpTableDiskonDokter.forEach((val, key) => {
                diskonDokterTotal += parseFloat(val.diskon_angka)
            })
        }

        // Penyesuaian menggunakan diskon persentase di comment
        // var tmp_tagihan = _tagihan + docoHelper.convertToAngka(tmpTableDiskonDokter[tmpID]["diskon"])
        // $('#tagihan_pasien').html(docoHelper.convertToRupiah(tmp_tagihan))
        
        //Pengurangan Diskon berpengaruh ke jumlah biaya asuransi
        reloadTagihan()
        var _diskonAngka = docoHelper.convertToAngka(tmpTableDiskonDokter[tmpID]["diskon_angka"]);
        _tagihan = _tagihan + _diskonAngka
        $('#tagihan_pasien').html(docoHelper.convertToRupiah(_tagihan))

        if(_totalDijamin > 0){
            $("#subsidi-asuransi").val(docoHelper.convertToRupiah(_totalDijamin + _diskonAngka))
            $('#total_sisa_piutang').val(docoHelper.convertToRupiah(_totalDijamin + _diskonAngka))
        } 

        diskon_dokter = parseInt(diskon_dokter) - _diskonAngka;
        tmpTableDiskonDokter.splice(tmpID, 1)
        generateTableDiskonDokter()
        $("#diskon-dokter").val(docoHelper.convertToRupiah(diskon_dokter));
        $('#total_dibayar').trigger('change');
        _hitungTagihan()
        
        if(_totalDijamin > 0) {
            $("#subsidi-asuransi").val(docoHelper.convertToRupiah(_totalDijamin))
            $('#total_sisa_piutang').val(docoHelper.convertToRupiah(_totalDijamin))
            $('#tagihan_pasien').html(docoHelper.convertToRupiah(_tagihan))
            $('#total_sisa_piutang').val(docoHelper.convertToRupiah(_tagihan))
        }
    })

    // $('#total_dibayar').trigger('change');
    // _hitungTagihan()
}
