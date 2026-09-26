/*
* @Author: DOCOTEL
* @Date:   2018-01-24 15:39:00
* @Last Modified by:   Ragnar-Lothbroc
* @Last Modified time: 2019-03-18 14:57:05
*/

var selectPendaftaran = $('.selectPendaftaran');
var selectPasien = $('.selectPasien');
var selectNoRm = $('.selectNoRm');
var tgl_pendaftaran = $('.tgl_pendaftaran');
var carabayar_nama = $('.carabayar_nama');
var penjamin_nama = $('.penjamin_nama');
var kelas_pelayanan = $('.kelas_pelayanan');
var pasien_id = $('.pasien_id');
var pasienadmisi_id = $('.pasienadmisi_id');
var pendaftaran_id = $('.pendaftaran_id');
var tandabuktibayar_id = $('.tandabuktibayar_id');
var tanggal_pembayaran = $('.tanggal_pembayaran');
// var biaya_administrasi = $('.biayaadministrasi');
var total_tagihan = $('.total_tagihan');
var pembulatan = $('.pembulatan');
var nama_pemilik_rekening = $('.nama_pemilik_rekening');
var uang_diterima = $('.uangditerima');
var nomor_rekening = $('.nomor_rekening');
var uangmuka = $('.uangmuka');
var carabayar_id = $('.carabayar_id');
var penjamin_id = $('.penjamin_id');
var bayaruangmuka_id = $('.bayaruangmuka_id');
var pasien_nama = $('.pasien_nama');
var pasien_alamat = $('.pasien_alamat');
var no_rekam_medik = $('#no_rekam_medik');
var nama_pasien = $('#nama_pasien');
var carabayar_nama = $('#carabayar_nama');
var penjamin_nama = $('#penjamin_nama');
var kelas_pelayanan = $('#kelas_pelayanan');

dateRangeHelper('.startDate','.endDate','.targetDate');
$(document).ready(function(){
    $(".pickadate").pickadate({
        format: "dd mmm yyyy",
    });

    $("#jenisnontunai_id").docoPaginationSelec2(
        // dapat disesuaikan dengan kebutuhan data / customize
        config = {
            placeholder : '-- Pilih Jenis --',      // custom placeholder (optional) default null
            _api : '/kasir/master-api/get-data-nontunai',   // get data
        }
    )

    $('[name="BayarUangMukaForm[carapembayaran]"]').on('change', function (e) {
        e.preventDefault();
        var _value = parseInt($(this).val());
        if ($(this).is(':checked')) {
            $('.namapemilik_rek').prop("disabled", false);
            $('.no_rek').prop("disabled", false);
            $('#jenisnontunai_id').prop("disabled", false);
            $('#jenisnontunai_id').closest('div.form-group').addClass('required');
        } else {
            $('#jenisnontunai_id').closest('div.form-group').removeClass('required');
            $('#jenisnontunai_id').val(null).trigger('change');
            $('#bayaruangmukaform-namapemilik_rek').val(null);
            $('#bayaruangmukaform-no_rek').val(null);
            $('.namapemilik_rek').prop("disabled", true);
            $('.no_rek').prop("disabled", true);
            $('#jenisnontunai_id').prop("disabled", true)
        }
    })

    $('#btn-submit').on('click', function (e) {
        e.preventDefault();
        var _data = $('#uangmuka-form').serializeArray();
        _data.push({
            name: 'no_pendaftaran',
            value: listdata.no_pendaftaran
        });

        $().docoForm('click', {
            data: _data,
            url: $('#uangmuka-form').attr('action'),
            success : function (response) {
                $('#uangmuka-form')[0].reset();
                // $('[name="BayarUangMukaForm[carapembayaran]"]').trigger('change')
                $('#uniform-bayaruangmukaform-carapembayaran > span').removeClass('checked')
                $('.jenisnontunai_id').text("");
                $(".data-save").prop("disabled", false);

                //Disable untuk transaksi non-tunai
                $(".namapemilik_rek").prop("disabled", true);
                $(".no_rek").prop("disabled", true);
                $(".jenisnontunai_id").prop("disabled", true);

                selectPendaftaran.val("").trigger("change");
                no_rekam_medik.text("");
                nama_pasien.text("");
                carabayar_nama.text("");
                penjamin_nama.text("");
                kelas_pelayanan.text("");

                if($('.btn-print-kwitansi').hasClass('disabled')){
                    $('.btn-print-kwitansi').removeClass('disabled');
                    $('.btn-print-kwitansi').removeAttr('disabled');
                }
                if($('.btn-print-bkm').hasClass('disabled')){
                    $('.btn-print-bkm').removeClass('disabled');
                    $('.btn-print-bkm').removeAttr('disabled');
                }
                $('.btn-print-kwitansi').attr("data-id", response.response.id_transaksi)
                $('.btn-print-bkm').attr("data-id", response.response.id_transaksi)
            }
        })
    });

    datapendaftaran = [];
    datanorm = [];
    datapasien = [];
    selectPendaftaran.select2({
            placeholder: '',
            minimumInputLength: 2,
            ajax: {
                url: "/kasir/tra-pembayaran-uang-muka/get-pendaftaran",
                dataType: 'json',
                quietMillis: 250,
                data: function (term, page) {
                    return {
                        q: term,
                        z: $('.targetDate').val(),
                        page: page
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
    selectPendaftaran.change(function(e){
        e.preventDefault();
        var id = $(this).val();
        getData(id);
    });

    $('.tombol-reset').click(function(){
        selectPendaftaran.val("").trigger('change.select2');
    });
});

function getData(id){
    $.ajax({
        url: '/kasir/tra-pembayaran-uang-muka/get-data?id='+id,
        type: 'GET',
        beforeSend: function(){
            let loadtext = 'Loading...';
            selectNoRm.val(loadtext)
            selectPasien.val(loadtext)
            carabayar_nama.val(loadtext)
            penjamin_nama.val(loadtext)
            kelas_pelayanan.val(loadtext)
            uang_diterima.val(loadtext);
            // biaya_administrasi.val(loadtext);
            pembulatan.val(loadtext);
            total_tagihan.val(loadtext);
            $('.uangmuka').val(loadtext)
        },
        success: function(response){
            // biaya_administrasi.val(0)
            uang_diterima.val(0)
            if(response.data_pasien != 'kosong'){
                let data = response.data_pasien;
                listdata = data
                no_rekam_medik.text(data.no_rekam_medik);
                nama_pasien.text(data.nama);
                carabayar_nama.text(data.carabayar_nama);
                penjamin_nama.text(data.penjamin_nama);
                kelas_pelayanan.text(data.kelaspelayanan_nama);
                pasien_id.val(data.pasien_id);
                pendaftaran_id.val(data.pendaftaran_id);
                pasienadmisi_id.val(data.pasienadmisi_id);
                carabayar_id.val(data.carabayar_id);
                penjamin_id.val(data.penjamin_id);
                pasien_nama.val(data.nama);
                pasien_alamat.val(data.alamat_pasien);
            }else{
                listdata = {}
                no_rekam_medik.text('');
                nama_pasien.text('');
                carabayar_nama.text('');
                penjamin_nama.text('');
                kelas_pelayanan.text('');
                pasien_id.val('');
                pendaftaran_id.val('');
                carabayar_id.val('');
                penjamin_id.val('');
                pasien_nama.val('');
                pasien_alamat.val('');
            }
            if (response.data_pembayaran != 'kosong') {
                let payment = response.data_pembayaran;
                total_tagihan.val(docoHelper.convertToRupiah(payment.total_tagihan));
                $('.uangmuka').val(0)
            }else{
                total_tagihan.val(0)
                $('.uangmuka').val(0)
            }
        }
    })
}
$(document).on('click', '.data-reset', function(){
    if(!$('.btn-print-kwitansi').hasClass('disabled')){
        $('.btn-print-kwitansi').addClass('disabled')
        $('.btn-print-kwitansi').prop('disabled', true);
    }
    if(!$('.btn-print-bkm').hasClass('disabled')){
        $('.btn-print-bkm').addClass('disabled')
        $('.btn-print-bkm').prop('disabled', true);
    }
    // $('.biayaadministrasi').val(0)
    $('.uangmuka').val(0)
    // $('.jmlpembulatan').val(0)
    $('.uangditerima').val(0)
})
$(document).on('click', '.btn-print-kwitansi', function(){
    window.open($(this).attr('data-target')+'?id='+$(this).attr('data-id'), '_blank')
})
$(document).on('click', '.btn-print-bkm', function(){
    window.open($(this).attr('data-target')+'?id='+$(this).attr('data-id'), '_blank')
})
$(document).on('change', '#bayaruangmukaform-carapembayaran', function(){
    if($(this).is(":checked") == true){
        $('.namapemilik_rek').removeAttr('readonly')
        $('.no_rek').removeAttr('readonly')
    }
    if($(this).is(":checked") == false){
        $('.namapemilik_rek').attr('readonly', true)
        $('.no_rek').attr('readonly', true)
    }
})
$(document).on('keyup', '.uangmuka', function(){

    hitungTotal()
})
$(document).on('keyup', '.biayaadministrasi', function(){
    hitungTotal()
})

$(document).on('change', '.uangmuka', function(){
    hitungTotal()
})
$(document).on('ready')


function hitungTotal(){
    let uangmuka = ($('.uangmuka').val() != '') ? parseInt( docoHelper.convertToAngka($('.uangmuka').val()) )  : 0;
    // // Set up biaya admin
    // let biayaadministrasi = ($('.biayaadministrasi').val() != '') ? parseInt(docoHelper.convertToAngka($('.biayaadministrasi').val())) : 0;
    // let _total = uangmuka // + biayaadministrasi
    //docoHelper.pembulatan(_total, $('.jmlpembulatan'), $('.uangditerima'))
    $('.uangditerima').val(docoHelper.convertToRupiah(uangmuka))
}
