/* 
    Author : Randy Vianda Putra (aweutist)
*/

$(document).ready(function () {
    $("#btn-save").on('click', function (event) {
        event.preventDefault();
        var dataPost = $("#kamar-form").serializeArray();
        $(this).docoForm("click", {
            data: dataPost,
            method: 'post',
            success: function (data) {
                setTimeout(function () {
                    window.location.href = "/master/kamar"
                }, 1000);
            }
        });
    });

    if (scenario == "update") {
        $("#ruangan_id").select2().trigger("change");
    }
});

$('#btn-ulang').on('click', function () {
    location.reload();
});

$('#btn-kembali').on('click', function () {
    window.location.href = "/master/kamar"
});

// $('#jeniskasuspenyakit_id').select2();
// const url = document.URL;
// const patternAction = url.match(/update/g);

// if (patternAction[0] == 'update') {
//     const ruangan_id = $('#ruangan').val();
//     const pelayanan_id = $('.pelayanan_id').val();
//     $('#ruangan').val(ruangan_id).trigger('change');
//     setTimeout(function () {
//         $('#kelas_pelayanan').val(pelayanan_id).trigger('change');
//     }, 1000);
// }

$('#ruangan_id').on('change', function(){
    var valuedata = $(this).val();
    //  console.log(valuedata);
    if (valuedata) {
        $.ajax({
            type: 'GET',
            dataType: 'JSON',
            url: '/master/kamar/get-data-pelayanan?ruangan_id='+valuedata,
            success: function(response){
                var select = $('#kelas_pelayanan');
                select.children().remove();
                $('#kelas_pelayanan').append($('<option>', { value : '' }).text('-- Pilih --'));
                $.each(response.kelas_pelayanan, function(index, item) {
                    $('#kelas_pelayanan').append($('<option>', { value : item.id }).text(item.text));
                });

                var select = $('#jeniskasuspenyakit_id');
                select.children().remove();
                $('#jeniskasuspenyakit_id').append($('<option>', { value : '' }).text('-- Pilih --'));
                $.each(response.jenis_penyakit, function(index, item) {
                    $('#jeniskasuspenyakit_id').append($('<option>', { value : item.id }).text(item.text));
                });
            }
        }).done(function () {
            if (flag == true) {
                $('#kelas_pelayanan').find('option').each(function(i, e) {
                    if($(e).val() == kelaspelayanan_id) {
                        $('#kelas_pelayanan').prop('selectedIndex', i);
                    }
                });

                $('#jeniskasuspenyakit_id').find('option').each(function(i, e) {
                    if($(e).val() == jeniskasuspenyakit_id) {
                        $('#jeniskasuspenyakit_id').prop('selectedIndex', i);
                    }
                });

                flag = false;
            }
        });
    }
});