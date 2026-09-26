$(function() {
    $("a.sidebar-menu").click(function() {
        var _cid = $(this).data('target');
        $('#main-content-inside').docoLoad({
            url:'/pendaftaran/tools-bpjs/render-view?_cid='+_cid,
            dataType: 'html',
            success: function (data) {
            },
        });
    });
    
});