var loading_spinner = '<i class="icon-spinner4 spinner position-center form-control-feedback spinner-text" style="display: block;"></i>';
var _class_option = ['kulit', 'limfonodi', 'tato', 'tindik', 'kelopak_mata', 'kanjungtiva',
    'gerakan_bola_mata', 'kelainan_daun_telinga', 'serumen_prop', 'membran_timpani', 'hidung',
    'kelenjar_tiroid', 'inspeksi_paru', 'palpasi_paru', 'perkusi_paru', 'auskultasi_paru',
    'bunyi_jantung', 'inspeksi_abdomen', 'perkusi_abdomen', 'palpasi_abdomen', 'auskultasi_abdomen',
    'rectal_toucher', 'genital'];

$(document).ready(function () {
    console.log(deformitas_kanan_atas)
    $('.td_field').keyup();
    $('.imt_field').keyup();
    $.each(_class_option, function (i, v) {
        var _val = 1;
        var _el = $(".opt_" + v);
        var radioValue = $(".opt_" + v + ':checked').val();

        if (v == 'rectal_toucher') {
            _val = 4;
        }
        else if (v == 'perkusi_abdomen') {
            _val = 3;
        }

        if (v == 'auskultasi_abdomen') {
            $(".note_" + v).hide();
        }
        else {
            if (_el.is(':checked') && radioValue == _val) {
                $(".note_" + v).show();
            }
            else {
                $(".note_" + v).hide();
            }
        }

        $('.opt_' + v).on('change', function () {
            if (v != 'auskultasi_abdomen') {
                if ($(this).is(":checked") && $(this).val() == _val) {
                    $(".note_" + v).show();
                }
                else {
                    $(".note_" + v).val(null).trigger('change');
                    $(".note_" + v).hide();
                }
            }
        });
    });
});

/*----------  Tekanan Darah Start  ----------*/
$(".td_field").keyup(function (e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
        // Allow: Ctrl+A, Command+A
        (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
        // Allow: home, end, left, right, down, up
        (e.keyCode >= 35 && e.keyCode <= 40)) {
        // return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
    }

    let systol = $('#pemeriksaanfisikdefaultform-td_sistolik').val() ? parseInt($('#pemeriksaanfisikdefaultform-td_sistolik').val()) : 0;
    let diastol = $('#form-td_diastolik').val() ? parseInt($('#form-td_diastolik').val()) : 0;
    let td_kategori = $("#pemeriksaanfisikdefaultform-td_kategori");

    let keterangan_td = systol + "/" + diastol;

    let pendaftaran_id = $('#pemeriksaanfisikdefaultform-pendaftaran_id').val();
    let nilai = keterangan_td;

    var hasil;

    $.ajax({
        url: '/mcu/pemeriksaan/get-hasil-td?pendaftaran_id=' + pendaftaran_id + '&nilai=' + nilai,
        type: 'get',
        dataType: 'JSON',
        beforeSend: function () {
            $('.hasil-td').after(loading_spinner);
        },
        success: function (data, text, xhr) {
            if (xhr.status == 200) {
                hasil = data.hasil
            }
        }
    }).done(function () {
        var blank = '-';
        if ((systol == 0) && diastol == 0) {
            td_kategori.val(blank);
        } else {
            td_kategori.val(hasil);
        }
        $(".spinner-text").remove();
    });
});
/*----------  Tekanan Darah end  ----------*/


/*----------  Berat Badan dan IMT Start  ----------*/
// $(".imt_field").keyup(function(e){
$(".imt_field").on('keyup', function (e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
        // Allow: Ctrl+A, Command+A
        (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
        // Allow: home, end, left, right, down, up
        (e.keyCode >= 35 && e.keyCode <= 40)) {
        // return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
    }

    let tb = $('#pemeriksaanfisikdefaultform-tinggi_badan').val() ? parseFloat($('#pemeriksaanfisikdefaultform-tinggi_badan').val()) : null;
    let bb = $('#pemeriksaanfisikdefaultform-berat_badan').val() ? parseFloat($('#pemeriksaanfisikdefaultform-berat_badan').val()) : null;
    let field_imt = $("#pemeriksaanfisikdefaultform-imt");
    let field_bbideal = $("#pemeriksaanfisikdefaultform-kategori_bb");
    let bbideal = 0.0;
    let imt = 0.0;
    let imt_kategori = '';

    // hitung bmi / imt
    if (bb && tb) {
        imt = (bb / ((tb / 100) * (tb / 100))).toFixed(2);

        $.each(data_bmi, function (index, value) {
            if (parseFloat(imt) >= parseFloat(value.bmi_minimum) && parseFloat(imt) <= parseFloat(value.bmi_maksimum)) {
                imt_kategori = value.bmi_defenisi;
                return false;
            }
        });

        // hitung berat badan ideal
        if (jeniskelamin == lakilaki) {
            bbideal = parseFloat((tb - 100) - (0.1 * (tb - 100))).toFixed(2);
        } else {
            bbideal = parseFloat((tb - 100) - (0.15 * (tb - 100))).toFixed(2);
        }
    }

    field_imt.val(imt);
    field_bbideal.val(imt_kategori);
});
/*----------  Berat Badan dan IMT end  ----------*/

var deformitas_option = [
    'deformitas_kanan_atas', 'deformitas_kanan_bawah', 'deformitas_kiri_atas', 'deformitas_kiri_bawah',
];

$.each(deformitas_option, function (i, v) {
    if(v == 'deformitas_kanan_atas') {
        if(deformitas_kanan_atas != '' && deformitas_kanan_atas == 'on') {
            $('.opt_' + v).prop("checked", true);
            $(".note_" + v).prop("readonly", false);
        }
    }
    if(v == 'deformitas_kanan_bawah') {
        if(deformitas_kanan_bawah != '' && deformitas_kanan_bawah == 'on') {
            $('.opt_' + v).prop("checked", true);
            $(".note_" + v).prop("readonly", false);
        }
    }
    if(v == 'deformitas_kiri_atas') {
        if(deformitas_kiri_atas != '' && deformitas_kiri_atas == 'on') {
            $('.opt_' + v).prop("checked", true);
            $(".note_" + v).prop("readonly", false);
        }
    }
    if(v == 'deformitas_kiri_bawah') {
        if(deformitas_kiri_bawah != '' && deformitas_kiri_bawah == 'on') {
            $('.opt_' + v).prop("checked", true);
            $(".note_" + v).prop("readonly", false);
        }
    }

    $('.opt_' + v).on('change', function(){
        if ($(this).is(':checked')) {
            $(".note_" + v).prop("readonly", false);
        }
        else {
            $(".note_" + v).prop("readonly", true);
            if(isEdit == 0) {
                $(".note_" + v).val(null).trigger('change')
            }
        }
    });
})

var fungsi_option = [
    'fungsi_motorik_kanan_atas', 'fungsi_motorik_kanan_bawah', 'fungsi_motorik_kiri_atas', 'fungsi_motorik_kiri_bawah',
    'fungsi_sensorik_kanan_atas', 'fungsi_sensorik_kanan_bawah', 'fungsi_sensorik_kiri_atas', 'fungsi_sensorik_kiri_bawah',
];

$.each(fungsi_option, function (i, v) {
    // fungsi motorik
    if(v == 'fungsi_motorik_kanan_atas') {
        if(fungsi_motorik_kanan_atas != '' && fungsi_motorik_kanan_atas == 'on') { // normal
            $('.opt_' + v).prop("checked", true);
            $(".note_" + v).prop("readonly", true);
        }
        else {
            $(".note_" + v).prop("readonly", false);
        }
    }
    if(v == 'fungsi_motorik_kanan_bawah') {
        if(fungsi_motorik_kanan_bawah != '' && fungsi_motorik_kanan_bawah == 'on') {
            $('.opt_' + v).prop("checked", true);
            $(".note_" + v).prop("readonly", true);
        }
        else {
            $(".note_" + v).prop("readonly", false);
        }
    }
    if(v == 'fungsi_motorik_kiri_atas') {
        if(fungsi_motorik_kiri_atas != '' && fungsi_motorik_kiri_atas == 'on') {
            $('.opt_' + v).prop("checked", true);
            $(".note_" + v).prop("readonly", true);
        }
        else {
            $(".note_" + v).prop("readonly", false);
        }
    }
    if(v == 'fungsi_motorik_kiri_bawah') {
        if(fungsi_motorik_kiri_bawah != '' && fungsi_motorik_kiri_bawah == 'on') {
            $('.opt_' + v).prop("checked", true);
            $(".note_" + v).prop("readonly", true);
        }
        else {
            $(".note_" + v).prop("readonly", false);
        }
    }

    // fungsi sensorik
    if(v == 'fungsi_sensorik_kanan_atas') {
        if(fungsi_sensorik_kanan_atas != '' && fungsi_sensorik_kanan_atas == 'on') {
            $('.opt_' + v).prop("checked", true);
            $(".note_" + v).prop("readonly", true);
        }
        else {
            $(".note_" + v).prop("readonly", false);
        }
    }
    if(v == 'fungsi_sensorik_kanan_bawah') {
        if(fungsi_sensorik_kanan_bawah != '' && fungsi_sensorik_kanan_bawah == 'on') {
            $('.opt_' + v).prop("checked", true);
            $(".note_" + v).prop("readonly", true);
        }
        else {
            $(".note_" + v).prop("readonly", false);
        }
    }
    if(v == 'fungsi_sensorik_kiri_atas') {
        if(fungsi_sensorik_kiri_atas != '' && fungsi_sensorik_kiri_atas == 'on') {
            $('.opt_' + v).prop("checked", true);
            $(".note_" + v).prop("readonly", true);
        }
        else {
            $(".note_" + v).prop("readonly", false);
        }
    }
    if(v == 'fungsi_sensorik_kiri_bawah') {
        if(fungsi_sensorik_kiri_bawah != '' && fungsi_sensorik_kiri_bawah == 'on') {
            $('.opt_' + v).prop("checked", true);
            $(".note_" + v).prop("readonly", true);
        }
        else {
            $(".note_" + v).prop("readonly", false);
        }
    }

    $('.opt_' + v).on('change', function(){
        if ($(this).is(':checked')) {
            $(".note_" + v).prop("readonly", true);
        }
        else {
            $(".note_" + v).prop("readonly", false);
        }
    });
})

var refleks_option = [
    'refleks_fisiologis_kanan_atas', 'refleks_fisiologis_kanan_bawah', 'refleks_fisiologis_kiri_atas', 'refleks_fisiologis_kiri_bawah',
    'refleks_patologis_kanan_atas', 'refleks_patologis_kanan_bawah', 'refleks_patologis_kiri_atas', 'refleks_patologis_kiri_bawah',
];

$.each(refleks_option, function (i, v) {
    if(v == 'refleks_fisiologis_kanan_atas') {
        if(refleks_fisiologis_kanan_atas == 1) {
            $('#' + v + '_negatif').prop("checked", true);
        }
    }
    if(v == 'refleks_fisiologis_kanan_bawah') {
        if(refleks_fisiologis_kanan_bawah == 1) {
            $('#' + v + '_negatif').prop("checked", true);
        }
    }
    if(v == 'refleks_fisiologis_kiri_atas') {
        if(refleks_fisiologis_kiri_atas == 1) {
            $('#' + v + '_negatif').prop("checked", true);
        }
    }
    if(v == 'refleks_fisiologis_kiri_bawah') {
        if(refleks_fisiologis_kiri_bawah == 1) {
            $('#' + v + '_negatif').prop("checked", true);
        }
    }

    if(v == 'refleks_patologis_kanan_atas') {
        if(refleks_patologis_kanan_atas == 1) {
            $('#' + v + '_negatif').prop("checked", true);
        }
    }
    if(v == 'refleks_patologis_kanan_bawah') {
        if(refleks_patologis_kanan_bawah == 1) {
            $('#' + v + '_negatif').prop("checked", true);
        }
    }
    if(v == 'refleks_patologis_kiri_atas') {
        if(refleks_patologis_kiri_atas == 1) {
            $('#' + v + '_negatif').prop("checked", true);
        }
    }
    if(v == 'refleks_patologis_kiri_bawah') {
        if(refleks_patologis_kiri_bawah == 1) {
            $('#' + v + '_negatif').prop("checked", true);
        }
    }
})
