$('.data-filter').click(function() {
        var jam_mulai          = $('#jam_mulai').val();
        var jam_selesai        = $('#jam_selesai').val();
        
        var parse_val_jammulai = Date.parse("01/01/2001 " + jam_mulai);
        var parse_val_jamtutup = Date.parse("01/01/2001 " + jam_selesai);
        var time_val_jammulai  = new Date(parse_val_jammulai);
        var time_val_jamtutup  = new Date(parse_val_jamtutup);
        var val_jammulai_ms    = time_val_jammulai.getTime();
        var val_jamtutup_ms    = time_val_jamtutup.getTime();
        var diff_times         = val_jamtutup_ms - val_jammulai_ms;
        var minutes_difference = diff_times / 1000 / 60;
        if(minutes_difference <=0 && jam_mulai != 0 && jam_selesai != 0){
            docoNotification("error", "Error", "Jam Selesai Tidak Boleh Kurang dari Sama Dengan Jam Mulai");
            return false;
        }
});

$('.data-excel2').click(function () {
    var ruangan_id  = $('#ruangan_id').val();
    var shift_id    = $('#shift_id').val();
    var hari        = $('#hari').val();
    var jam_mulai   = $('#jam_mulai').val();
    var jam_selesai = $('#jam_selesai').val();
    window.open('/master/jadwal-poliklinik/export-excel?ruangan_id=' + ruangan_id + '&shift_id=' +shift_id + '&hari='+ hari + '&jam_mulai='+jam_mulai+'&jam_selesai='+jam_selesai);
});