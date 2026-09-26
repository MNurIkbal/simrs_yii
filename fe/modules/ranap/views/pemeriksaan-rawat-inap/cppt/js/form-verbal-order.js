$(document).ready(function() {
	// Remove class
	jQuery("#btn-back-verbal-order").removeClass("btn-toolbar");
	jQuery("#btn-save-verbal-order").removeClass("btn-toolbar");
	jQuery("#btn-reset-verbal-order").removeClass("btn-toolbar");

	// Ajax
	$("#btn-save-verbal-order").on("click", function(event) {
		event.preventDefault();
	    var data = $("#form-verbal-order").serializeArray();
		$(this).docoForm('click',{
	        url: '/ranap/pemeriksaan-rawat-inap/create-verbal-order',
	        data: data,
	        success : function(res) {
	        	var form = $("#form-verbal-order");
	            form[0].reset();
				var valDefaultInstruksi = $("#ruangan_id-verbal-order").find('option:selected').val();
				$("#pemberi_instruksi_id-verbal-order").val(valDefaultInstruksi);		

				var ruanganId = $("#ruangan_id-verbal-order").find('option:selected').length
				var valDefaultRuangan = $("#ruangan_id-verbal-order").find('option:selected').val();
				if(ruanganId != 1){
					$("#ruangan_id-verbal-order").val(0);			
				}else{
					$("#ruangan_id-verbal-order").val(valDefaultRuangan);			
				}
				
				// Show hide form
				$("div #div-verbal-order").prop("hidden", true);
				$("div #div-soap").prop("hidden", true);
				$("div #div-cppt").prop("hidden", false);
				tabel.draw();
	        }
	    });
    });
	
	// Show hide form
	$("#btn-back-verbal-order").on("click", function(event) {
		// Prevent default
		event.preventDefault();
		
		// Show hide form
		jQuery("div #div-verbal-order").prop("hidden", true);
		jQuery("div #div-soap").prop("hidden", true);
		jQuery("div #div-cppt").prop("hidden", false);
	});

	// Reset button
	$("#btn-reset-verbal-order").on("click", function(event) {
		// Prevent default
		event.preventDefault();
		
		// Find form
		var form = $("#form-verbal-order");
		
		// Reset form
		form[0].reset();
		var valDefaultInstruksi = $("#ruangan_id-verbal-order").find('option:selected').val();
		$("#pemberi_instruksi_id-verbal-order").val(valDefaultInstruksi);		

		var ruanganId = $("#ruangan_id-verbal-order").find('option:selected').length
		var valDefaultRuangan = $("#ruangan_id-verbal-order").find('option:selected').val();
		if(ruanganId != 1){
			$("#ruangan_id-verbal-order").val(0);		
		}else{
			$("#ruangan_id-verbal-order").val(valDefaultRuangan);			
		}

	});
});