let $input_date = $('#pjpasienform-pj_tanggal_lahir').pickadate({
    editable: true,
    format: 'dd-mm-yyyy',
    formatSubmit: 'dd-mm-yyyy',
    selectMonths: true,
    selectYears: true,
    min: [1900, 01, 01],
    max: true,
    onClose: function () {
        $('.datepicker').focus();
    }
});

let picker_date = $input_date.pickadate('picker');
$('#pj-date').parent().on('click', function (event) {
    if (picker_date.get('open')) {
        picker_date.close();
    } else {
        picker_date.open();
    }
    event.stopPropagation();
});

$('#pjpasienform-pj_tanggal_lahir').on('change', function () {
    let umur = '';
    if ($(this).val() != '') {
        const splitDate = $(this).val().split('-')
        const date = `${splitDate[2]}-${splitDate[1]}-${splitDate[0]}`

        let myDate = new Date(date);
        let today = new Date();
        if (myDate > today) {
            $(this).pickadate('picker').set('select', new Date())
            return true
        }
        umur = generateUmur($(this).val());
    }
    $('.umurtext').val(umur);
    $("input[name='PjpasienForm[pj_tanggal_lahir]_submit']").val($('#pjpasienform-pj_tanggal_lahir').val());
})

if(statePj == true) {
    let data = JSON.parse(dataPj)
    $('#add-pj').prop('disabled', true)
    $('.data_pj_pasien').removeClass('hidden')
    if(Object.keys(data).length) {
        $("#pjpasienform-pj_jk").find(`input[value=${data.pj_jk}]`).trigger('click')
        if (data.pj_tanggal_lahir != null && data.pj_tanggal_lahir != "") {
            $('#pjpasienform-pj_tanggal_lahir').val(convertDateByFormat(data.pj_tanggal_lahir, 'd-M-Y')).trigger('change')
        }
    }
} else {
    $('#add-pj').prop('disabled', false)
    $('#remove-pj').prop('disabled', true )
}

//Cek PJ Tera
if(statePj == false && jQuery.isEmptyObject(JSON.parse(dataPjTera)) == false) {
    let data = JSON.parse(dataPjTera)
    statePj = true
    $('#pj_id').val('0').trigger('change')
    $('#remove-pj').prop('disabled', false)
    $('#add-pj').prop('disabled', true)
    $('.data_pj_pasien').removeClass('hidden')
    $("#pjpasienform-pj_nama").val(data.penanggungjawabtera_nama).trigger('change')
    $("#pjpasienform-pj_alamat").val(data.penanggungjawabtera_alamat).trigger('change')
}

$('#add-pj').click(function (e) { 
    e.preventDefault();
    if(statePj != true) {
        statePj = true
        $('#pj_id').val('0').trigger('change')
        $('#remove-pj').prop('disabled', false)
        $(this).prop('disabled', true)
        $('.data_pj_pasien').removeClass('hidden')
    }
});

$('#remove-pj').click(function (e) { 
    e.preventDefault();
    if(statePj == true) {
        statePj = false
        $('#pj_id').val('').trigger('change')
        $('#is_deleted_pj').val(1).trigger('change')
        $('#add-pj').prop('disabled', false)
        $(this).prop('disabled', true)
        $('.data_pj_pasien').addClass('hidden')
        clearForm()
    }
});

if(stateKp == true) {
    let data = JSON.parse(dataKp)
    $('#add-kp').prop('disabled', true)
    $('.data_keluarga_pasien').removeClass('hidden')
    if(Object.keys(data).length) {
        $("#keluargapasienform-keluarga_jk").find(`input[value=${data.keluarga_jk}]`).trigger('click')
    }
    setTimeout(function(){
        $('#frm-kp-propinsi_id').trigger('depdrop:change');
        }, 300);
} else {
    $('#add-kp').prop('disabled', false)
    $('#remove-kp').prop('disabled', true )
}

$('#add-kp').click(function (e) { 
    e.preventDefault();
    if(stateKp != true) {
        stateKp = true
        $('#keluargapasien_id').val('0').trigger('change')
        $('#is_deleted_kp').val(0).trigger('change')
        $('#remove-kp').prop('disabled', false)
        $(this).prop('disabled', true)
        $('.data_keluarga_pasien').removeClass('hidden')
    }
});

$('#remove-kp').click(function (e) { 
    e.preventDefault();
    if(stateKp == true) {
        stateKp = false
        $('#keluargapasien_id').val('').trigger('change')
        $('#is_deleted_kp').val(1).trigger('change')
        $('#add-kp').prop('disabled', false)
        $(this).prop('disabled', true)
        $('.data_keluarga_pasien').addClass('hidden')
        clearFormKp()
    }
});

function clearForm() {
    $('#pjpasienform-pj_pengantar').val('').trigger('change')
    $('#pjpasienform-pj_jenis_identitas').val('').trigger('change')
    $('#pjpasienform-pj_hubungan').val('').trigger('change')
    $('#pjpasienform-pj_nama').val('').trigger('change')
    $('#pjpasienform-pj_no_identitas').val('').trigger('change')
    $('#pjpasienform-pj_tempat_lahir').val('').trigger('change')
    $('#pjpasienform-pj_tanggal_lahir').val('').trigger('change')
    $('#pjpasienform-pj_no_telepon').val('').trigger('change')
    $('#pjpasienform-pj_alamat').val('').trigger('change')
    $('input[name="PjpasienForm[pj_jk]"]').prop('checked', false)
}

function clearFormKp() {
    $('#keluargapasienform-keluarga_hubungan').val('').trigger('change')
    $('#keluargapasienform-keluarga_nama').val('').trigger('change')
    $('#keluargapasienform-keluarga_namadepan').val('').trigger('change')
    $('#keluargapasienform-keluarga_no_telepon').val('').trigger('change')
    $('#keluargapasienform-keluarga_alamat').val('').trigger('change')
    $('input[name="KeluargaPasienForm[keluarga_jk]"]').prop('checked', false)
    $('#keluargapasienform-keluarga_rt').val('').trigger('change')
    $('#keluargapasienform-keluarga_rw').val('').trigger('change')
    $('#keluargapasienform-keluarga_pekerjaan_id').val('').trigger('change')
}

$("input[name='PasienForm[jeniskelamin]']").on('change', function() {
    if ($('#frm-pasien-namadepan').val().length < 1) {
        var jk = $("input[name='PasienForm[jeniskelamin]']:checked").val();
        if (jk == 15) {
            $('#frm-pasien-namadepan').val(201).trigger('change');
        } else if (jk == 16) {
            $('#frm-pasien-namadepan').val(202).trigger('change');
        } else {
            $('#frm-pasien-namadepan').val('').trigger('change');
        }
    }
});