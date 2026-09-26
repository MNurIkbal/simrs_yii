/*
* @Author: Rizqi Fitrianto
* @Date:   2019-03-08 14:47:29
* @Last Modified by:   Rizqi Fitrianto
* @Last Modified time: 2019-03-08 16:59:45
*/

$(document).ready(function(){

})
$('#no-rekam-medik').on('change', function(){
    var pendaftaranid = $(this).val();
    $('#content-pasien').docoLoad({
        url: '/ranap/pendaftaran-bayi/data-pasien?pendaftaranid='+pendaftaranid,
        dataType: 'html',
        success : function(data) {
            // $(" .select2 ").select2();
        }
    });
})