$(document).ready(function () {
    $("#pemberi_instruksi_id").docoPaginationSelec2(
        // dapat disesuaikan dengan kebutuhan data / customize
        config = {
            placeholder: '-- Pilih Instruksi --',      // custom placeholder (optional) default null
            _api: '/rajal/master-api/list-all-new-dokter',   // get data
        }
    )

    $("#fee_konsul").docoPaginationSelec2(
        // dapat disesuaikan dengan kebutuhan data / customize
        config = {
            placeholder: '-- Pilih Fee Konsul --',      // custom placeholder (optional) default null
            _api: `/rajal/master-api/list-fee-konsul?ruangan_id=${ruangan_id}&penjamin_id=${penjamin_id}&kelaspelayanan_id=${kelaspelayanan_id}`,   // get data
        }
    )

    validasiClosePopup();
});

$(() => {
    $("#btn-save").bind("click", () => {
        let _form = $("#form-verbal-order").serializeArray();
        $().docoForm("click", {
            data: _form,
            skipScrollUp: false,
            skipConfirm: true,
            url: '/rajal/pemeriksaan/create-verbal-order?id=' + pendaftaran_id,
            success: function (data) {
                $("#modal-lab").find(".close").click();
                tableCppt.draw()
            },
        });
    });
});

$("#pemberi_instruksi_id").bind("change", () => {
    validasiClosePopup();
});

$("#fee_konsul").bind("change", () => {
    validasiClosePopup();
});

$('#verbalorderform-instruksi').on('keyup blur', function() {
    validasiClosePopup();
});


/* FUNGSI VALIDASI CLOSE POP UP */
function validasiClosePopup() {
    var pemberiInstruksi = $("#pemberi_instruksi_id").on("select2:selected").val();
    var feeKonsul = $("#fee_konsul").on("select2:selected").val();
    var instruksi = $("#verbalorderform-instruksi").val();

    if(pemberiInstruksi != "" || feeKonsul != "" || instruksi != "") {
        var isUpdate = true;
    } else {
        var isUpdate = false;
    }

    if(isUpdate == true) {
        $('.close-modal-jadwal').attr('data-dismiss-confirmation', 'modal');
        $('.close-modal-jadwal').removeAttr('data-dismiss');
    } else {
        $('.close-modal-jadwal').removeAttr('data-dismiss-confirmation');
        $('.close-modal-jadwal').attr('data-dismiss', 'modal');
    }
}