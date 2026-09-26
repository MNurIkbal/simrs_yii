$(document).ready(function(){
    $('#content-konfig-billing').docoLoad({
        url: '/master/konfig-system/konfig-billing',
        dataType: 'html',
        success : function(data) {
        }
    });
    $('#tab-konfig-tarif-default').on("click", function(e){
        $('#content-konfig-tarif-default').docoLoad({
            url: '/master/konfig-system/tarif-default',
            dataType: 'html',
            success : function(data) {
            }
        });
    });
});