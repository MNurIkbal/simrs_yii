var form = $("#form-instruksi-dpjp");
$(document).ready(function() {
    var ruangan_id = $('#ruangan_id').val()
    var penjamin_id = $('#penjamin_id').val()
    var kelaspelayanan_id = $('#kelaspelayanan_id').val()
    $("#ruangan_id_dropdown").prop("disabled", true);

    $("#pemberi_instruksi_id").docoPaginationSelec2(
        // dapat disesuaikan dengan kebutuhan data / customize
        config = {
            placeholder : '-- Pilih Instruksi --',      // custom placeholder (optional) default null
            _api : '/ranap/master-api/list-all-new-dokter',   // get data
        }
    )
    
    $("#fee_konsul").docoPaginationSelec2(
        // dapat disesuaikan dengan kebutuhan data / customize
        config = {
            placeholder : '-- Pilih Fee Konsul --',      // custom placeholder (optional) default null
            _api : `/ranap/master-api/list-fee-konsul?ruangan_id=${ruangan_id}&penjamin_id=${penjamin_id}&kelaspelayanan_id=${kelaspelayanan_id}`,   // get data
        }
    )

    jQuery("#btn-back-instruksi-dpjp").removeClass("btn-toolbar");
	jQuery("#btn-save-instruksi-dpjp").removeClass("btn-toolbar");
	jQuery("#btn-reset-instruksi-dpjp").removeClass("btn-toolbar");
    
});

$('#btn-reset-instruksi-dpjp').on('click', function (event) {
	event.preventDefault();
    form[0].reset()
    $('#pemberi_instruksi_id').empty()
    $('#fee_konsul').empty()
})

function kembaliInstruksiDpjp() {
    jQuery("div #div-instruksi-dpjp").prop("hidden", true);
    jQuery("div #div-cppt").prop("hidden", false);
}

function simpanInstruksiDpjp(index) {
    let data = form.serializeArray()
    
    $(index).docoForm('click', {
        url: '/ranap/pemeriksaan-rawat-inap/create-instruksi-dpjp',
        data: data,
        success : function(response) {
            form[0].reset();
            $('#pemberi_instruksi_id').empty()
            $('#fee_konsul').empty()
            $("#btn-back-instruksi-dpjp").click();
        }
    });
}

$("#btn-back-instruksi-dpjp").on("click", function(event) {
    // Prevent default
    event.preventDefault();
    
    $('.tabbable ul li a[href="#view-cppt"]').click();
});