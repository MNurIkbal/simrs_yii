$(document).ready(function () {

    var tabSkriningRajal = $("#tab-skrining-rajal");
    var tabSkriningCovid = $("#tab-skrining-covid");
    var tabAsesmentInformasi = $("#tab-asesment-informasi");
    var is_riwayat = $("#tab-skrining-rajal").attr('is-riwayat')

    if (is_riwayat == 'true'){
        $("div").remove(".modal-header")
        $("div").remove(".modal-footer")
    }

    tabSkriningRajal.click(function (e) { 
        e.preventDefault();
        $('#content-skrining-rajal').docoLoad({
            url: `/rajal/informasi/form-skrining-rajal?pendaftaran_id=${pendaftaran_id}&is_riwayat=${is_riwayat}`,
            dataType: 'html',
            success: function (data) {  

            },
        })
    });
    tabSkriningRajal.trigger('click')

    tabSkriningCovid.click(function (e) { 
        e.preventDefault();
        $('#content-skrining-covid').docoLoad({
            url: `/rajal/informasi/form-skrining-covid?pendaftaran_id=${pendaftaran_id}&is_riwayat=${is_riwayat}`,
            dataType: 'html',
            success: function (data) {  
                
            },
        })
    });

    tabAsesmentInformasi.click(function (e) { 
        e.preventDefault();
        $('#content-asesment-informasi').docoLoad({
            url: '/rajal/informasi/form-assesment-pasien?id='+pendaftaran_id+'&is_riwayat='+is_riwayat,
            dataType: 'html',
            success: function (data) {  
                
            },
        })
    });

    tabSkriningRajal.trigger('click')
});
