$(document).on('click','.panggil', function(){
    var _antrian_farmasi = $(this).attr('data-antrian');
    var _antrian_nama = $(this).attr('data-nama');
    var _antrian_nomer = $(this).attr('data-noantrian');
    var _antrian_id = $(this).attr('data-antrianid');
    var _jenis_resep = $(this).attr('data-jenisresep');

    var _data = {
        'no_antrian' : _antrian_nomer,
        'antrian_id' : _antrian_id,
        'jenis_resep' : _jenis_resep,
        'extend_text' : _antrian_nama,
    };

    $.ajax({
        type: 'GET',
        url: '/apotek/informasi-reseptur/panggil-antrian',
        data: _data,
        dataType: 'JSON',
        beforeSend: function () {
        },
        success: function (res) {
            console.log(res);
            // let antrian = _antrian_farmasi;
            // let player = $('#playerAudio');
            // let arrayText = antrian.split(' ');
            // arrayText.push('stop');
            // arrayText = arrayText.filter(Boolean);
            // let index = 0;

            // player[0].defaultPlaybackRate = 1;
            // player[0].src = window.location.origin + '/media/sounds/' + arrayText[index] + '.mp3';
            // player[0].play();
            // player[0].addEventListener('ended', function () {
            //     index = index + 1;

            //     if (index < arrayText.length) {
            //         player[0].defaultPlaybackRate = index == arrayText.length - 3 ? 1.5 : 1.2;
            //         if (arrayText[index] != 'stop') {
            //             let txt = arrayText[index]
            //             player[0].src = window.location.origin + '/media/sounds/' + txt + '.mp3';
            //             player[0].play();
            //         }
            //     }
            // });
        },
    });
});