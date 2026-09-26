/*
* @Author: Sigit
* @Date:   2019-03-06 17:48:47
*/

$(document).on("click", "#btn-sync", function () {
    $.ajax({
        type: 'GET',
        url: window.location.origin + '/pendaftaran/daftar/resync',
        dataType: 'JSON',
        success: function (response) {
            updateSync();
            docoNotification("success", "Proses Berhasil!", "Proses Sinkronisasi Berhasil!");
        },
        error: function (error) {
            docoNotification("error", "Proses Gagal!", "Proses Sinkronisasi Gagal!");
        }
    });
});
