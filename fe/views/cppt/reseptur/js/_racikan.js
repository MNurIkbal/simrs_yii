$(document).ready(function() {
	$("#btn-tambah-racikan").on("click", function() {
		var rke = 0;
		$.each(list_temp_obat, function (x, y) {
			if(y.racikan_id == "OR") {
				rke = rke + 1;
			}
		});

		var racikan_text = $("#racikan").val();
		racikan = {
			detail_type: "racikan_freetext",
			racikan_id: "OR",
            rke: rke + 1,
            racikan_text: racikan_text
		};

		list_temp_obat.push(racikan);
		appendObat(list_temp_obat);
		resetRacikan();
	});

	$("#racikan").on("input", function() {
		var racikan_text = $("#racikan").val();
		if(racikan_text === "" || racikan_text == null) {
			$("#btn-tambah-racikan").attr("disabled", true);
		} else {
			$("#btn-tambah-racikan").attr("disabled", false);
		}
	});
});

function resetRacikan() {
	$("#racikan").val(null);
	$("#btn-tambah-racikan").attr("disabled", true);
}