$.getJSON("/json/config.json", function (config) {
    var socket = io.connect('http://'+config.ip+':'+config.port);

    socket.on('display-antrian', function (response) {
        var data = JSON.parse(response);

        $.each(data.data, function(first_key, first_value) {
            if (first_key == 'pendaftaran_online') {
                $.each(first_value, function(key, value) {
                    if (value != "Data jadwal dokter tidak ditemukan.") {
                        $(".aksi"+value).html('<button type="button" class="btn btn-danger btn-xs-new">Ditolak</button>');
                    }
                });
            }
        });
    });
});