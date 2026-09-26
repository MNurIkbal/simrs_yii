$("#total_tagihan").css('font-weight', 'bold');
$("#total_sudahbayar").css('font-weight', 'bold');
$("#total_sisapiutang").css('font-weight', 'bold');

$(".no_rekam_medik").html(no_rekam_medik).css('font-weight', 'bold');
$(".no_pendaftaran").html(no_pendaftaran).css('font-weight', 'bold');
$(".nama_pasien").html(nama_pasien).css('font-weight', 'bold');
$(".tanggal_lahir").html(tanggal_lahir).css('font-weight', 'bold');
$(".tgl_pendaftaran").html(tgl_pendaftaran).css('font-weight', 'bold');
$(".tgl_pemberianpiutang").html(tgl_pemberianpiutang).css('font-weight', 'bold');
$(".nama_pegawai").html(nama_pegawai).css('font-weight', 'bold');
$(".total_tagihan").html(docoHelper.convertToRupiah(total_tagihan));
$(".total_sudahbayar").html(docoHelper.convertToRupiah(total_sudahbayar));
$(".total_sisapiutang").html(docoHelper.convertToRupiah(total_sisapiutang));
$("#total_sisapiutang").val(total_sisapiutang);
$(".total_piutang").html(docoHelper.convertToRupiah(total_piutang));

$("#ajax-form").docoForm("submit",{
    success : function(data) {
        window.location.href = "/kasir/informasi-pemberian-piutang";
    }
});

var d = new Date(tgl_pemberianpiutang.split('-').reverse().join('-'));
var dd = d.getDate();
var mm = d.getMonth()+1;
var yy = d.getFullYear();
            
$('#pembayaranpiutangform-tgl_pembayaranpiutang').pickadate({
    format: 'dd-mm-yyyy',
    min: [yy,mm-1,dd],
    max: new Date(),
});

$(document).ready(function(){
	$(".selectJenis").docoPaginationSelec2(
	    config = {
	        placeholder : "-- Cari Jenis Pembayaran --",  
	        _api : "/kasir/master-api/get-data-nontunai",
	    }
	);
	$(".selectMetode").docoPaginationSelec2(
	    config = {
	        placeholder : "-- Cari Metode Pembayaran --",  
	        _api : "/kasir/informasi-pemberian-piutang/list-metode-bayar",
	    }
	);
	$('.selectMetode').on('change', function(){
		var _val = $(this).val();
		if(_val == metode_non_tunai) {
			$('.field-pembayaranpiutangform-jenisnontunai_id').addClass('required');
			$(".selectJenis").prop("disabled", false);
		}
		else {
			$('.field-pembayaranpiutangform-jenisnontunai_id').removeClass('required');
			$('.field-pembayaranpiutangform-jenisnontunai_id').removeClass('has-error');
			$('.help-block').html('');
			$(".selectJenis").prop("disabled", true);
		}
	});
})