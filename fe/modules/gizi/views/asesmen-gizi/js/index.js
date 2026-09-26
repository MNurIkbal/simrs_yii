
// tab load
$(document).ready(function(){
    localStorage.removeItem('hash-url');
    var lastHash = $(this).find("ul .active").find("a").attr("href");
    window.location.hash = $(this).find("ul .active").find("a").attr("href");

    // Konten asesmen awal gizi
    $('#tab-sga').on("click", function(e){
        $('#content-sga').docoLoad({
            url: '/gizi/asesmen-gizi/sga?id='+pendaftaran_id,
            dataType: 'html',
            success : function(data) {
                // $(" .select2 ").select2();
            }
        });
    });

    // Konten asesmen awal gizi
    $('#tab-pagt').on("click", function(e){
        $('#content-pagt').docoLoad({
            url: '/gizi/asesmen-gizi/pagt?id='+pendaftaran_id,
            dataType: 'html',
            success : function(data) {
                // $(" .select2 ").select2();
            }
        });
    });

    // Konten asuhan gizi
    $('#tab-asuhan-gizi').on("click", function(e){
        $('#content-asuhan-gizi').docoLoad({
            url: '/gizi/asesmen-gizi/asuhan-gizi?id='+pendaftaran_id,
            dataType: 'html',
            success : function(data) {
                // $(" .select2 ").select2();
            }
        });
    });

    $('#tab-cppt-gizi').on("click", function(e){
        $('#content-cppt-gizi').docoLoad({
            url: '/gizi/asesmen-gizi/cppt-gizi?id='+pendaftaran_id,
            dataType: 'html',
            success : function(data) {
                // $(" .select2 ").select2();
            }
        });
    });

    $('#tab-nrs').on("click", function(e){
        $('#content-nrs').docoLoad({
            url: '/gizi/asesmen-gizi/nrs?id='+pendaftaran_id,
            dataType: 'html',
            success : function(data) {
                // $(" .select2 ").select2();
            }
        });
    });

    // Konten permintaan makan
    $('#tab-permintaan-makan').on("click", function(e){
        $('#content-permintaan-makan').docoLoad({
            url: '/gizi/asesmen-gizi/minta-makan?id='+pendaftaran_id,
            dataType: 'html',
            success : function(data) {
                // $(" .select2 ").select2();
            }
        });
    });


    $('a').on("click", function(event) {
        if($(this).data('hash') == undefined){

            localStorage.setItem("hash-url", lastHash);
            lastHash = $(this).attr("href");
            window.location.hash = $(this).attr("href");
        }
    });

    if($('#tab-pagt').css('display') !== 'none') {
        $('#tab-pagt a').trigger('click')
    }

    if(permintaan_makan){
        $('#tab-permintaan-makan a[href="#view-permintaan-makan"]').tab('show');
        $('#tab-permintaan-makan a[href="#view-permintaan-makan"]').trigger('click');
    }
});

