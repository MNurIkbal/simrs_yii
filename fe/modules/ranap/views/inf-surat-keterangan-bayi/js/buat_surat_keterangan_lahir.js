$(document).ready(function () {

	$('.pickadate').pickadate({
		format: 'dd mmmm yyyy',
	});

	$('.timepicker').pickatime({
		format: 'HH:i',
		interval: 1
	});

	$('#buat-surat_keterangan-lahir').click(function(e) {
		e.preventDefault();
		var data = $('#buat-surat_keterangan-lahir-form').serializeArray();
		$().docoForm('click',{
			data:data,
			url:'/ranap/inf-surat-keterangan-bayi/save',
			success:function(data){
			}
		})
	});

	$(document).on("click","#print-skl", function (event) {
		event.preventDefault();
		var pendaftaran_id = $(this).attr('data-id');
		window.open("/ranap/inf-surat-keterangan-bayi/export-pdf?pendaftaran_id="+pendaftaran_id);
	});
});