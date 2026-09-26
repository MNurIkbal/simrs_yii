$(document).ready(function() {
    $("#tab-jeniskasuspenyakit").on("click", function(event) {
        $("#content-jeniskasuspenyakit").docoLoad({
            url: "/master/jenis-kasus-penyakit/page-jenis-kasus-penyakit",
            dataType: 'html',
            success : function(data) {
                // $('.select2').select2();
            }
        });
    });
    $("#tab-kasuspenyakitdiagnosa").on("click", function(event) {
        $("#content-kasuspenyakitdiagnosa").docoLoad({
            url: '/master/kasus-penyakit-diagnosa/index',
            dataType: 'html',
            success : function(data) {
                // $('.select2').select2();
            }
        });
    });

    $("#tab-kasuspenyakitruangan").on("click", function(event) {
        $("#content-kasuspenyakitruangan").docoLoad({
            url: "/master/kasus-penyakit-ruangan/index",
            dataType: 'html',
            success : function(data) {
                // $('.select2').select2();
            }
        });
    });

    $("#tab-jeniskasuspenyakit").click();
});