
$('#instruksiform-jenis_instruksi').data('prev',$('#instruksiform-jenis_instruksi').val());
$(document).ready(function() {
	// Show hide form
	$("#btn-back-terapi").on("click", function(event) {
		// Prevent default
		event.preventDefault();

        var source_tab = $(this).data('source_tab');
        if(source_tab != undefined && source_tab != 'asesmen-dpjp'){
            $('.nav-tabs a[href="#view-'+source_tab+'"]').tab('show');
            $('#content-'+source_tab).docoLoad({
                url: '/igd/pemeriksaan-igd/'+source_tab+'?id='+pendaftaran_id,
                dataType: 'html',
                success : function(data) {
                }
            });
        }else{
            $('#content-asesmen-dpjp').docoLoad({
                url: '/igd/pemeriksaan-igd/asesmen-dpjp?id='+pendaftaran_id,
                dataType: 'html',
                success : function(data) {
                }
            });
        }
	});

    if($("#instruksiform-jenis_instruksi option:selected").val() != '' && $('#instruksiform-instruksi_id').val()!=''){
        $('#content-transaksi-terapi').empty();
        var cppt_id = $('#instruksiform-cppt_id').val();
        var instruksi_id = $('#instruksiform-instruksi_id').val();
        var jenisterapi_id = $("#instruksiform-jenis_instruksi").val();
        $("#instruksiform-jenis_instruksi").prop("disabled",true);
        $('#content-transaksi-terapi').docoLoad({
            url: '/igd/pemeriksaan-igd/content-terapi?id='+pendaftaran_id+
            '&jenisterapi_id='+jenisterapi_id+
            '&cppt_id='+cppt_id+
            '&instruksi_id='+instruksi_id+
            '&isUbah=1',
            dataType: 'html',
            success : function(data) {
            }
        });
    }

    $('#instruksiform-jenis_instruksi').on("change", function(event) {
        // Prevent default
        event.preventDefault();
        var val_jenis_instruksi = $(this).val();
        if($('#content-transaksi-terapi').children().length !== 0){

            var header = "Perhatian !";
            var message = "Apakah Anda yakin untuk pindah terapi? Jika yakin maka form terakhir akan ter-refresh dan tidak tersimpan.";
            var label = { 
                buttons: {
                    'No': 'button-no',
                    'Yes': 'button-yes'
                }
            };
            $.showQuestionDialog(header, message, label, function(reaction) {
                if (reaction == 'Yes') {
                    $('#content-transaksi-terapi').empty();
                    var jenisterapi_id = val_jenis_instruksi;
                    var cppt_id = $('#instruksiform-cppt_id').val();
                    $('#content-transaksi-terapi').docoLoad({
                        url: '/igd/pemeriksaan-igd/content-terapi?id='+pendaftaran_id+'&jenisterapi_id='+jenisterapi_id+'&cppt_id='+cppt_id,
                        dataType: 'html',
                        success : function(data) {
                        }
                    });
                    $('#instruksiform-jenis_instruksi').data('prev', val_jenis_instruksi);
                }else{
                    $('#instruksiform-jenis_instruksi').val($('#instruksiform-jenis_instruksi').data('prev')).trigger('change.select2');
                    return false; 
                }
                hideQuestionDialog();
            });

        }else{
            $('#content-transaksi-terapi').empty();
            var jenisterapi_id = $(this).val();
            var cppt_id = $('#instruksiform-cppt_id').val();
            $('#content-transaksi-terapi').docoLoad({
                url: '/igd/pemeriksaan-igd/content-terapi?id='+pendaftaran_id+'&jenisterapi_id='+jenisterapi_id+'&cppt_id='+cppt_id,
                dataType: 'html',
                success : function(data) {
                }
            });
            $('#instruksiform-jenis_instruksi').data('prev', $(this).val());
        }
    });

});

