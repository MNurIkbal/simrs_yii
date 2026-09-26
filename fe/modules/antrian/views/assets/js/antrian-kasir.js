

$('#clock1').clock({
    'dateFormat': 'l, d F Y',
    'timeFormat': 'H:i:s',
    'langSet': 'id'
});

$(document).on('click', '.pilih_antrian', function(e) {
    e.preventDefault();
    var id = $(this).attr('data-id');
    var id_decrypt = $(this).attr('data-id-decrypt');

    $.ajax({
        url: '/antrian/dashboard/jenis-antrian?jenis_id=' + id,
        type: 'post',
        data: {id_jenis_antrian: id_decrypt},
        success: function(data) {
          console.log(data);
            var response = data.response.data
            var no_antrian = response.no_antrian;
            var jenisantrian_id = id_decrypt;
            var antrian_id = response.antrian_id;
            //document.location = '/antrian/dashboard/cetak-antrian?no=' + no_antrian + '&jenisantrian_id=' + jenisantrian_id;
            
            $.ajax({
                type: 'GET',
                url: 'http://localhost:8000/print/print-antrian',
                data: res['response']['data']['cetak'],
                success: function (res){
                    location.reload();
                },
                error: function () {
                    $.redirect("/antrian/dashboard/cetak-antrian-dashboard",{no_antrian:no_antrian,jenisantrian_id:jenisantrian_id,antrian_id:antrian_id});
                }
            });
        }
    })
})
