$(".date").pickadate({
    applyClass: "bg-slate-600",
    cancelClass: "btn-default",
    locale: {
        format: "DD-MMMM-YYYY"
    }
});

// $(document).on("change", "#jadwalbukapoli_id", function (event) {

// });
// $('.txt-timepicker').timepicker({
//     showMeridian: false,
//     minuteStep: 30,
//     defaultTime: false
// });

// var $inputMulai = $(".jam_mulai").pickatime({
//     format: "HH:i",

// });
// var pickerMulai = $inputMulai.pickatime('picker');


// var $inputSelesai = $(".jam_selesai").pickatime({
//     format: "HH:i",
//     onOpen: function () {
//         var selectedTime = pickerMulai.get('select');
//         if(selectedTime){
//             this.set('min',
//                 [selectedTime.hour,selectedTime.mins+30]
//             );
//             if(pickerMulai.get('disable').length>0){
//                 var firstDis = this.get('disable')[0];
//                 var lastDis = this.get('disable')[this.get('disable').length -1];
//                 this.set('max',false);
//                 if(selectedTime.hour <= firstDis[0]){
//                     this.set('max',firstDis);
//                 }else{
//                     var selesai = $('#jadwalbukapoli_id').find(":selected").data('jam_selesai');
//                     this.set('max',selesai);
//                 }
//             }
//         }
//     }
// });
// var pickerSelesai = $inputSelesai.pickatime('picker');

// $(document).on("change", "#jadwalbukapoli_id", function (event) {
//     let selected = $(this).find(":selected");

//     let hari = selected.data("hari");
//     let jam_mulai = roundTime(selected.data("jam_mulai"),15);
//     let jam_selesai = roundTime(selected.data("jam_selesai"),15);
//     let pegawai_id = $('#pegawai_id').val();
//     let ruangan_id = $('#ruangan_id').val();

//     getDisabledTime(hari, jam_mulai, jam_selesai, pegawai_id, ruangan_id);
// });

// function getDisabledTime(hari, jam_mulai, jam_selesai, pegawai_id, ruangan_id, setdisable=true) {
//     // console.log(hari, jam_mulai, jam_selesai, pegawai_id, ruangan_id, setdisable);
//     if (typeof hari == 'undefined') {return;}
    
//     $.ajax({
//         'type': "POST",
//         'dataType': 'JSON',
//         'url': baseUrl + "master/jadwal-dokter/check-jadwal",
//         'data': {
//             hari: hari,
//             pegawai_id: pegawai_id
//         },
//         'beforeSend': function () {

//         },
//         'success': function (res) {
//             if (res != '[]') {
//                 var listDisableHours = [];
//                 $.each(res, function (index, value) {
                    
//                     var times = getTimes(res[index]['jam_mulai'], res[index]['jam_tutup']);
//                     $.each(times, function (i, item){
//                         listDisableHours.push(item);
//                     });
                    
//                 });

//                 if (setdisable) {

//                     // console.log(listDisableHours); return;
//                     if(listDisableHours.length > 0){
//                         // console.log(listDisableHours);
//                         pickerMulai.set('disable',listDisableHours);
//                         pickerSelesai.set('disable',listDisableHours);
//                     }else{
//                         pickerMulai.set('enable',true);
//                         pickerSelesai.set('enable',true);
//                     }
//                 } 
//                 if(jam_mulai != ''){
//                     pickerMulai.set('min',jam_mulai);
//                 }
//                 if(jam_selesai != ''){
//                     pickerMulai.set('max',jam_selesai);
//                     pickerSelesai.set('max',jam_selesai);
//                 }
//             }
            
//         },
//         'error': function (res) {

//         }
//     });
// }


// function getTimes(from, until) {
//     var until = Date.parse("01/01/2001 " + until);
//     var from = Date.parse("01/01/2001 " + from);
//     var max = (Math.abs(until - from) / (60 * 60 * 1000)) * 2;
//     var time = new Date(from);
//     var hours = [];
//     for (var i = 0; i <= max; i++) {
//         var hour = time.getHours();
//         var minute = time.getMinutes();
//         hours.push([hour, minute]);
//         time.setMinutes(time.getMinutes() + 30);
//     }
//     return hours;
// }

$(document).on('click','#btn-simpan', function(e){
    e.preventDefault();

    var bukapoli_mulai = $('#jadwalbukapoli_id option:selected').data('jam_mulai');
    var bukapoli_selesai = $('#jadwalbukapoli_id option:selected').data('jam_selesai');
    if(bukapoli_mulai == undefined || bukapoli_mulai == ''){
        docoNotification("error", "Simpan Gagal", "Jadwal Poli Belum Dipilih");
        return false;
    }
    if(bukapoli_selesai == undefined || bukapoli_selesai == ''){
        docoNotification("error", "Simpan Gagal", "Jadwal Poli Belum Dipilih");
        return false;
    }
    var regexhour = /^([01]\d|2[0-3]):?([0-5]\d)$/g;
    var val_jammulai = $('#jadwaldokterform-jadwaldokter_mulai').val();
    var val_jamtutup = $('#jadwaldokterform-jadwaldokter_tutup').val();
    if(val_jammulai.match(regexhour) && val_jamtutup.match(regexhour) && bukapoli_mulai.match(regexhour) && bukapoli_mulai.match(regexhour)){
        var parse_val_jammulai = Date.parse("01/01/2001 " + val_jammulai);
        var parse_val_jamtutup = Date.parse("01/01/2001 " + val_jamtutup);
        var time_val_jammulai = new Date(parse_val_jammulai);
        var time_val_jamtutup = new Date(parse_val_jamtutup);
        var val_jammulai_ms = time_val_jammulai.getTime();
        var val_jamtutup_ms = time_val_jamtutup.getTime();
        var diff_times = val_jamtutup_ms - val_jammulai_ms;
        var minutes_difference = diff_times / 1000 / 60;
        if(minutes_difference <=0){
            docoNotification("error", "Simpan Gagal", "Jam Tutup Tidak Boleh Kurang dari Sama Dengan Jam Mulai");
            return false;
        }
        // else if(minutes_difference < 30){
        //     docoNotification("error", "Simpan Gagal", "Jadwal Dokter Tidak Boleh Kurang dari 30 Menit");
        //     return false;
        // }

        split_val_jammulai = val_jammulai.split(':');
        split_val_jamselesai = val_jamtutup.split(':');
        split_bukapoli_mulai = bukapoli_mulai.split(':');
        split_bukapoli_selesai = bukapoli_selesai.split(':');

        jam_mulai_minuteformat = (split_val_jammulai[0] * 60) + split_val_jammulai[1];
        jam_selesai_minuteformat = (split_val_jamselesai[0] * 60) + split_val_jamselesai[1];
        bukapoli_mulai_minuteformat = (split_bukapoli_mulai[0] * 60) + split_bukapoli_mulai[1];
        bukapoli_selesai_minuteformat = (split_bukapoli_selesai[0] * 60) + split_bukapoli_selesai[1];
        if(
            parseInt(split_val_jammulai[0]) >= parseInt(split_bukapoli_mulai[0]) && 
            parseInt(jam_mulai_minuteformat) >= parseInt(bukapoli_mulai_minuteformat) && 
            parseInt(split_val_jamselesai[0]) <= parseInt(split_bukapoli_selesai[0]) && 
            parseInt(jam_selesai_minuteformat) <= parseInt(bukapoli_selesai_minuteformat)
        ){
            $('#jadwaldokter-form').submit();
        }else{
            docoNotification("error", "Simpan Gagal", "Jadwal Dokter Tidak Dapat Diluar Jam Buka Poliklinik");
            return false;
        }
    }else{
        docoNotification("error", "Simpan Gagal", "Format Jam Salah");
        return false;
    }
});

$('#jadwaldokter-form').docoForm('submit',{
    success : function(data) {
        this.formInput[0].reset();
        window.location.href = $('.data-back').attr('href')
    }
});

$(document).on('change', '#instalasi-id', function(){
    if($(this).val() != 1){
        if(!$('.content-maksantrian').hasClass('hidden')){
            $('.content-maksantrian').addClass('hidden')
        }
        if(!$('.jbp').hasClass('hidden')){
            $('.jbp').addClass('hidden')
        }
        if($('.jdh').hasClass('hidden')){
            $('.jdh').removeClass('hidden')
        }
    }else{
        if($('.content-maksantrian').hasClass('hidden')){
            $('.content-maksantrian').removeClass('hidden')
        }
        if($('.jbp').hasClass('hidden')){
            $('.jbp').removeClass('hidden')
        }
        if(!$('.jdh').hasClass('hidden')){
            $('.jdh').addClass('hidden')
        }
    }
});
// $(document).on('click', '.data-simpan', function () {
//     $('.btn-simpan').click();
// });
// $('#jadwaldokter-form').docoForm('submit', {
//     success: function (response) {
//         this.formInput[0].reset();
//         $('#modal_backdrop').modal('hide');
//         $('.data-filter').click();
//     }
// });

$(document).on('change', '#pegawai_id', function(){
    // $("#jadwalbukapoli_id").change();
});

// $(document).ready(function () {
//     $("#jadwalbukapoli_id").val($('#hide-jadwalbukapoli_id').val()).change();
// });
// $('#jadwalbukapoli_id').change(function() {
//     alert('tes');
// });

let roundTime = (time, minutesToRound) => {

    let [hours, minutes] = time.split(':');
    hours = parseInt(hours);
    minutes = parseInt(minutes);

    // Convert hours and minutes to time in minutes
    time = (hours * 60) + minutes; 

    let rounded = Math.round(time / minutesToRound) * minutesToRound;
    let rHr = ''+Math.floor(rounded / 60)
    let rMin = ''+ rounded % 60

    return rHr.padStart(2, '0')+':'+rMin.padStart(2, '0')
}