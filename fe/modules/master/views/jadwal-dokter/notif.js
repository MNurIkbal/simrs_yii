/*
    Author : Randy Vianda Putra
*/

$(document).ready(function() {
    $('.list-notif').hide();
    $(document).on('click', '#btn-save', function(e) {
        e.preventDefault();
        const notif = $('.notif-set').val();
        if (!notif) {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Notifikasi tidak boleh kosong"));

            return false;
        }
        const dataPost = $('#form-notif').serializeArray();
        $(this).docoForm('click', {
            url: '/master/jadwal-dokter/push-notification',
            data: dataPost,
            skipSuccessNotif: true,
            success: function (data) {
                docoNotification("success", i18next.t("Berhasil"), i18next.t("Notifikasi berhasil dikirimkan"));               
            }
        });
    })
});