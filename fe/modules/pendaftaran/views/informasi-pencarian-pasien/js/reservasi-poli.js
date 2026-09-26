/*
* @Author: Naufal Ziyad L
* @Date:   2018-02-08 14:39:00
*/

var listdata = {listpendaftaran: {},listpasien: {}, listnorm: {}};
var selectRekamMedik = $('.selectRekamMedik');
var selectPasien = $('.selectPasien');
var selectPanggilan = $('.selectPanggilan');
var selectTempatLahir = $('.selectTempatLahir');
var selectTanggalLahir = $('.selectTanggalLahir');
var selectUmur = $('.selectUmur');
var selectAlamatPasien = $('.selectAlamatPasien');
var selectNoTelp = $('.selectNoTelp');
var selectNoRm = $('.selectRekamMedik');
var selectJenisKelamin = $('.selectJenisKelamin');
var selectPasienId = $('.selectPasienId');
var selectStatusJanji = $('.selectStatusJanji');
var selectTanggalSekarang = $('.selectTanggalSekarang');
var selectHari = $('.selectHari');
var selectAntrian = $('.selectAntrian');
var selectByPhone = $('.selectByPhone');

var today = new Date();
var dd = today.getDate();
var mm = today.getMonth()+1; //January is 0!
var yyyy = today.getFullYear();

if(dd<10) {
    dd = '0'+dd
} 

if(mm<10) {
    mm = '0'+mm
} 

today = yyyy + '/' + mm + '/' + dd;

/*var byphone = $('.byphone');*/
$('.inputpemesan').hide();
$('.ByPhoneText').val(0);
$('#by_phonecheck').click(function(){
	if($(this).is(":checked")){ 
	  	$('.ByPhoneText').val(1);
	  }
	else{
	  	$('.ByPhoneText').val(0);
	  }
});

$(document).ready(function(){
	$(".pickadate").pickadate({
		format: "dd mmm yyyy",
	});

	$('#form-reservasi-poli').docoForm('submit',{	 
		before: function(){
			console.log('tester');
			return false;
		},	
		success : function(response) {            
		    this.formInput[0].reset();
		    console.log('suksas');
		    selectRekamMedik.val("").trigger("change");
		}
	});	 

	datapendaftaran = [];
	datanorm = [];
	datapasien = [];
 	selectRekamMedik.select2({			
			placeholder: 'Pilih No Rekam Medik',
			minimumInputLength: 2,				
			ajax: {
				url: "/pendaftaran/reservasi-poliklinik/get-rekam-medik",
		        dataType: 'json',
		        quietMillis: 250,		        
		        data: function (term, page) {
		            return {
		                q: term,
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
 	selectRekamMedik.change(function(e){
 		e.preventDefault();
 		var id = $(this).val(); 		
 		getData(id); 		
 	});
	$('#tombol-reset').click(function(){		
		selectRekamMedik.val("").trigger('change.select2');
	});
});

function getData(id){
	$.ajax({
		url: '/pendaftaran/reservasi-poliklinik/get-data?id='+id,
		type: 'GET',
		beforeSend: function(){
			let loadtext = 'Loading...';
			selectNoRm.val(loadtext)
			selectPasien.val(loadtext)
			selectPanggilan.val(loadtext)
			selectTempatLahir.val(loadtext)
			selectTanggalLahir.val(loadtext)
			selectUmur.val(loadtext)
			selectAlamatPasien.val(loadtext)
			selectNoTelp.val(loadtext)
			selectJenisKelamin.val(loadtext)
			selectStatusJanji.val(loadtext)

		},
		success: function(response){
			if(response.data_pasien != 'kosong'){
				let data = response.data_pasien;
				selectNoRm.val(data.no_rekam_medik);
				selectPasien.val(data.nama_pasien);
				selectPanggilan.val(data.nama_bin);
				selectTempatLahir.val(data.tempat_lahir);
				selectTanggalLahir.val(data.tanggal_lahir);
				selectAlamatPasien.val(data.alamat_pasien);
				selectNoTelp.val(data.no_telepon_pasien);
				selectJenisKelamin.val(data.jeniskelamin);
				selectUmur.val(data.umur);
				selectPasienId.val(data.pasien_id);
				selectStatusJanji.val(357);
				selectTanggalSekarang.val(today);
				selectHari.val(0);
				selectAntrian.val(1);
				$('.jk'+data.jeniskelamin).prop('checked', true);
			}
		}
	})
}
