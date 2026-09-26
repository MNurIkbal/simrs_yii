$( document ).ready(function() {

    var socket = io.connect('http://localhost:8890');

    socket.on('display-antrian', function (data) {

        var return_data = JSON.parse(data);
        var loket_panggil = return_data.data.loket_id;

        if (_arrloket.indexOf(loket_panggil) != -1) {
            $("#no_antrian_panggil").html(return_data.data.no_antrian);
            $("#no_loket_panggil").html('Loket '+return_data.data.no_loket);
        };

    });

});
