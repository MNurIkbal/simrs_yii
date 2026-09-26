/*
* @Author: Sunarko / Master Tempat Tidur
* @Date:   2018-07-23 17:16:31
* @Last Modified by:   Rizqi Fitrianto
* @Last Modified time: 2019-02-20 10:55:34
*/

$(document).ready(function () {

    $("#tempat-tidur-form").on('submit', function (event) {
        event.preventDefault();
        // var dataPost = $("#tempat-tidur-form").serializeArray();
        $(this).docoForm("submit", {
            method: 'post',
            success: function (data) {
                setTimeout(function () {
                    window.location.href = "/master/tempat-tidur"
                }, 1000);
            },
            error: function(){
                $('#ruangan_id').val('').trigger('change');
            }
        });
    });
});

$('#btn-ulang').on('click', function () {
    location.reload();
});

$('#btn-kembali').on('click', function () {
    window.location.href = "/master/tempat-tidur"
});

$('#ruangan_id').on('change', function(){
    var valuedata = $(this).val();
    //  console.log(valuedata);
    if (valuedata) {
        $.ajax({
            type: 'GET',
            url: '/master/tempat-tidur/get-data-kamar?ruangan_id='+valuedata,
            success: function(response){
                var select = $('#kamarruangan_id');
                select.children().remove();
                $('#kamarruangan_id').append($('<option>', { value : '' }).text('-- Pilih Kamar --'));
                $.each(response.result, function(index, item) {
                    $('#kamarruangan_id').append($('<option>', { value : item.id }).text(item.text));
                });
            }
        });
    }
});

$('.is_rekapkinerjaprofesi').change(function(){
    if (this.checked) {
        $('.is_terisi').prop('disabled', false);
    } else {
        $('.is_terisi').prop('checked', false);
        $('.is_terisi').prop('disabled', true);
    }
});