$(document).ready(function() {
	// Show hide form
	$("#btn-back-terapi").on("click", function(event) {
		// Prevent default
		event.preventDefault();

        if($(this).data('jns_instruksi') == 'RESEPTUR'){
            // Cek inputan obat
            ajaxHapusSessionReseptur("kembali");
        }

        // Set enabled
        $("#btn-cetak-reseptur").prop("disabled", true);

        var source_tab = $(this).data('source_tab');
        if(source_tab != undefined && source_tab != 'cppt'){
            $('.nav-tabs a[href="#view-'+source_tab+'"]').tab('show');
            $('#content-'+source_tab).docoLoad({
                url: '/ranap/pemeriksaan-rawat-inap/'+source_tab+'?id='+pendaftaran_id+'&pasien_id='+pasien_id,
                dataType: 'html',
                success : function(data) {
                }
            });
        }else{
            $('#content-cppt').docoLoad({
                url: '/ranap/pemeriksaan-rawat-inap/cppt?id='+pendaftaran_id+'&pasien_id='+pasien_id,
                dataType: 'html',
                success : function(data) {
                }
            });
        }
	});

    // Jenis terapi
    $("#jenis_terapi").on("change", function(event) {
        // Prevent default
        event.preventDefault();

        // Cek flag
        if ($("#jenis_instruksi_flag").val() != '') {
            // Cek instruksi flag
            if ($("#jenis_instruksi_flag").val() != $(this).val()) {
                // Cek inputan obat
                cekInputanObat("ganti_jenis_terapi");
            }
            else {
                // Change jenis terapi
                changeJenisTerapi();
            }
        }
        else {
            // Change jenis terapi
            changeJenisTerapi();
        }
    });
    
	if(isUbah == "1"){
        if($('#id_jenisinstruksi_addon').val() != 0){
            $("#jenis_terapi").val($('#id_jenisinstruksi_addon').val()).trigger("change");
            $("#jenis_terapi").prop("disabled",true);

        }else{
    		$("#jenis_terapi").val(457).trigger("change");
    		$("#jenis_terapi").prop("disabled",true);
        }
	}

    // Jenis terapi
    $("#catatan_terapi").on("keyup", function(event) {
        // Prevent default
        event.preventDefault();

        // Set val ke active record
        $("#instruksiform-catatan_instruksi").val($(this).val());
    });
});

// Cek inputan obat
function cekInputanObat(jenis_cek) {
    // Cek inputan reseptur obat
    var catatan = $("#catatan_terapi").val();
    var depo = $("#select_depo").val();
    var iter = $("#reseptur_iter").val();
    var reseptur = $("#depdrop_reseptur_nr").val();
    var nonracikan = $("#qty_nonracikan_id").val();
    var signa_nonracikan = $("#signa_nonracikan_id").val();
    var rke = $("#resepturdetailform-rke").val();
    var obatalkes1 = $("#resepturdetailform-obatalkes_id-0").val();
    var qty = $("#resepturdetailform-qty_reseptur-0").val();
    var signa_reseptur = $("#resepturdetailform-signa_reseptur").val();
    var total_data_reseptur = tabel_reseptur.page.info().recordsTotal;

    // Cek reseptur
    if (reseptur == null) {
        // Set reseptur
        reseptur = "";
    }

    // Cek obatalkes
    if (obatalkes1 == null) {
        // Set reseptur
        obatalkes1 = "";
    }

    // Set value
    var value = catatan + depo + iter + reseptur + nonracikan + signa_nonracikan + rke + obatalkes1 + qty + signa_reseptur;

    // Cek apakah ada form yang terisi
    if (value != "" || total_data_reseptur > 0) {
        // Question dialogue
        questionDialogue(jenis_cek);
    }
    else {
        // Cek jenis terapi
        if (jenis_cek == "kembali") {
            // Click kembali
            clickKembali();
        }
        else if (jenis_cek == "ganti_jenis_terapi") {
            // Change jenis terapi
            changeJenisTerapi();
        }
    }
}

// Question dialogue
function questionDialogue(jenis_cek) {
    // Cek jenis cek
    if (jenis_cek == "kembali") {
        // Data untuk confirm message
        var header = "Perhatian !";
        var message = "Apakah Anda yakin untuk kembali? Jika yakin maka form terakhir akan ter-refresh dan tidak tersimpan.";
        var label = { 
            buttons: {
                'No': 'button-no',
                'Yes': 'button-yes'
            }
        };
    }
    else if (jenis_cek == "ganti_jenis_terapi") {
        // Data untuk confirm message
        var header = "Perhatian !";
        var message = "Apakah Anda yakin untuk pindah terapi? Jika yakin maka form terakhir akan ter-refresh dan tidak tersimpan.";
        var label = { 
            buttons: {
                'No': 'button-no',
                'Yes': 'button-yes'
            }
        };
    }

    // Confirm box
    $.showQuestionDialog(header, message, label, function(reaction) {
        // Cek reaksi
        if (reaction == 'Yes') {
            // Ajax hapus session obat
            ajaxHapusSessionReseptur(jenis_cek);
        }
        else {
            // Hide
            hideQuestionDialog();
            $('[data-popup="tooltip"]').tooltip();
            $('#jenis_terapi').val($("#jenis_instruksi_flag").val()).trigger('change.select2');
        }
    });
}

// hapus session reseptur
function ajaxHapusSessionReseptur(jenis_cek) {
    // Ajax
    $.ajax({
        url: "/ranap/pemeriksaan-rawat-inap/reset-reseptur-session?id="+pendaftaran_id+"&cppt_id="+$("#instruksiform-cppt_id").val(),
        success: function() {
            // Nothing to do
            tabel_reseptur.draw();
        }
    }).done(function() {
        // Cek jenis terapi
        if (jenis_cek == "kembali") {
            // Click kembali
            clickKembali();
        }
        else if (jenis_cek == "ganti_jenis_terapi") {
            // Change jenis terapi
            changeJenisTerapi();
        }

        // Hide
        hideQuestionDialog();
    });
}

// Klik kembali
function clickKembali() {
    // Show hide form
    jQuery("div #div-soap").prop("hidden", true);
    jQuery("div #div-terapi").prop("hidden", true);
    jQuery("div #div-cppt").prop("hidden", false);

    // Empty jenis terapi
    $('#jenis_terapi').val('').trigger('change.select2');
    $('#cppt-terapi-tindakan').prop("hidden", true);
    $('#cppt-terapi-reseptur').prop("hidden", true);

    // Reset all
    // resetAll();

    // Draw tabel
    // tabel.draw();
}

// Change jenis terapi
function changeJenisTerapi() {
    // Cek value
    if ($("#jenis_terapi").val() == 457) {
        // Show tindakan
        $("#cppt-terapi-tindakan").prop("hidden", false);
        $("#cppt-terapi-reseptur").prop("hidden", true);
        $("#cppt-terapi-penunjang").prop("hidden", true);
    }
    else if ($("#jenis_terapi").val() == 458) {
        // Show reseptur
        $("#cppt-terapi-tindakan").prop("hidden", true);
        $("#cppt-terapi-reseptur").prop("hidden", false);
        $("#cppt-terapi-penunjang").prop("hidden", true);

        // Trigger
        $("#resepturdetailform-cppt_id").trigger("change");
    }
    else {
        // Show penunjang
        $("#cppt-terapi-tindakan").prop("hidden", true);
        $("#cppt-terapi-reseptur").prop("hidden", true);
        $("#cppt-terapi-penunjang").prop("hidden", false);
    }

    // Set flag
    $("#jenis_instruksi_flag").val($("#jenis_terapi").val());

    // Set val ke active record
    $("#instruksiform-jenis_instruksi").val($("#jenis_terapi").val());

    // // Cek cppt
    // if ($("#instruksiform-cppt_id").val() != '') {
    //     // Draw
    //     tabel_reseptur.ajax.url(baseUrl+"ranap/pemeriksaan-rawat-inap/get-data-reseptur-session?pendaftaran_id="+pendaftaran_id+"&cppt_id="+$("#instruksiform-cppt_id").val()+"").draw();
    // }

    // Reset all
    // resetAll();

    // Draw tabel
    // tabel.draw();
}
