$('#clock1').clock({
	'dateFormat':'l, d F Y',
	'timeFormat':'H:i:s',
	'langSet':'id'
});
// $.fn.stepy.defaults.legend = false;
// $.fn.stepy.defaults.transition = 'fade';
// $.fn.stepy.defaults.duration = 150;
// $.fn.stepy.defaults.backLabel = '<i class=\"icon-arrow-left13 position-left\"></i> Back';
// $.fn.stepy.defaults.nextLabel = 'Next <i class=\"icon-arrow-right14 position-right\"></i>';

// $('#ambil-antrian-form-penunjang').addClass('stepy-basic');
// $('.stepy-basic').stepy({
//     validate: true,
//     block: true,
//     next: function(index) {
//         if (!$('.stepy-basic').validate(validateVar)) {
//             return false
//         }
//     },
//     finish: function(index) {
//         if($('input:radio[name=\"status_pilih\"]:checked').length == 0){
//             var error = '<label id=\"status_pilih-error\" class=\"label label-danger label-roundless\" for=\"status_pilih\">Status Pasien Belum Dipilih</label>';
//             $('#error-opt-jaminan').append(error);
//             return false;
//         }else if($('input:radio[name=\"carabayar_pilih\"]:checked').length == 0){
//             var error = '<label id=\"carabayar_pilih-error\" class=\"label label-danger label-roundless\" for=\"carabayar_pilih\">Cara Bayar Belum Dipilih</label>';
//             // error.appendTo($('#error-opt-jaminan'));
//             $('#error-opt-jaminan').append(error);
//             return false;
//         }else{
//             return true;
//         }
//     }
// });
// var validateVar = {
//     ignore: 'input[type=hidden]', // ignore hidden fields
//     errorClass: 'label label-danger label-roundless',
//     errorPlacement: function(error, element) {
//         if(element.parents('div').hasClass('opt-poly')){
//             error.appendTo($('#error-opt-poly'));
//         }else if(element.parents('div').hasClass('opt-dokter')){
//             error.appendTo($('#error-opt-dokter'));
//         }
//     },
//     rules: {
//         poly_pilih:'required',
//         dokter_pilih:'required',
//         carabayar_pilih: 'required',
//         status_pilih:'required'
//     },
//     messages: {
//         poly_pilih:'Poliklinik Belum Dipilih',
//         dokter_pilih:'Dokter Belum Dipilih',
//         carabayar_pilih: 'Cara Bayar Belum Dipilih',
//         status_pilih:'Status Pasien Belum Dipilih'
//     }
// }
// $('input[type=radio][name=poly_pilih]').each(function(e){
//     if($(this).parents('div').hasClass('is_disabled')){
//         $(this).prop('disabled',true);
//     }
// });
// $('input[type=radio][name=poly_pilih]').change(function(){
//     if($('input:radio[name=\"poly_pilih\"]:checked').length != 0){
//         var element_dokter = $('.dokter-dependent');
//         element_dokter.empty();
//         var poly_id = $(this).val();
//         $.ajax({
//             type: 'GET',
//             url: '/antrian/dashboard/get-list-dokter-by-poly-id',
//             data: {poly_id:poly_id},
//             success: function(res) {
//                 if(res.message == 'success'){
//                     var data = res.data;
//                     $(data).each(function(index){
//                         var status_dokter_class;
//                         var status_dokter_text;
//                         var status_dokter_btn;
//                         var status_dokter_disabled = '';
//                         if(this.is_buka == true){
//                             if(this.sisa_kuota >= 1){
//                                 status_dokter_class = 'label-btn-buka';
//                                 status_dokter_text = 'BUKA';
//                                 status_dokter_btn = 'btn btn-primary btn-lg raised btn-huge';
//                             }else{
//                                 status_dokter_class = 'label-btn-penuh';
//                                 status_dokter_text = 'Kuota Penuh';
//                                 status_dokter_btn = 'btn btn-warning btn-lg raised btn-huge';
//                                 status_dokter_disabled = 'is_disabled';
//                             }
//                         }else{
//                             status_dokter_class = 'label-btn-tutup';
//                             status_dokter_text = 'TUTUP';
//                             status_dokter_btn = 'btn btn-danger btn-lg raised btn-huge';
//                             status_dokter_disabled = 'is_disabled';
//                         }
//                         var _html = '';
//                         _html += '<div class=\"col-md-4 plan opt-dokter '+status_dokter_disabled+'\" >';
//                         _html += '<input type=\"radio\" name=\"dokter_pilih\" id=\"btn-pilih-dokter-'+this.pegawai_id+'\" value=\"'+this.pegawai_id+'\">';
//                         _html += '<label for=\"btn-pilih-dokter-'+this.pegawai_id+'\" class=\"'+status_dokter_btn+'\"';
//                         _html += '<div class=\"container-label\">';
//                         _html += '<h4><b>'+this.nama_pegawai_lengkap+'</b></h4>';
//                         _html += '<p class=\"'+status_dokter_class+'\">'+status_dokter_text+'</p>';
//                         _html += '<span class=\"badge badge-primary position-right\">'+this.sisa_kuota+'</span>';
//                         _html += '</div>';
//                         _html += '</label>';
//                         _html += '</div>';
//                         element_dokter.append(_html);
//                         $('input[type=radio][name=dokter_pilih]').each(function(e){
//                             if($(this).parents('div').hasClass('is_disabled')){
//                                 $(this).prop('disabled',true);
//                             }
//                         });
//                     });
//                 }
//             }
//         });
//     }
// })
// $('input[type=radio][name=status_pilih]').change(function() {
//     if($('input:radio[name=\"status_pilih\"]:checked').length != 0){
//         $('#error-opt-jaminan').empty();
//     }
// });
// $('input[type=radio][name=carabayar_pilih]').change(function() {
//     if($('input:radio[name=\"carabayar_pilih\"]:checked').length != 0){
//         $('#error-opt-jaminan').empty();
//     }
// });

// $('.stepy-basic').find('.button-next').addClass('btn bg-teal-700 btn-huge-next');
// $('.stepy-basic').find('.button-back').addClass('btn bg-slate btn-huge-prev pull-left');
$('.btn-poly-penunjang').on('click',function(e){
    var ruangan_id = $(this).data('ruangan');
    var jenisantrian_id = $(this).data('jenisantrian');
    var instalasi_id = $(this).data('instalasi');
    var fungsiantrian_id = $(this).data('fungsiantrian');
     $.ajax({
        url: '/antrian/dashboard/simpan-antrian',
        type: 'POST',
        data: {poly_pilih:ruangan_id,jenisantrian_id:jenisantrian_id,fungsiantrian_id:fungsiantrian_id,instalasi_id:instalasi_id},
        success: function (res) {
            var succMessage = 'Proses Berhasil!';
            var succText = 'Antrian Dicetak';
            var msg = res.response;
            var noantrian = '';
            var jenis_id = 'Mq';
            var antrian_id = 0;
            if(msg.text != undefined){
                succText = msg.text;
            }
            if(msg.message != undefined){
                succMessage = msg.message;
            }
            new PNotify({
                title: succMessage,
                text: succText,
                addclass: 'alert alert-success alert-arrow-right alert-styled-right',
                type: 'success'
            });
            if(msg.data.no_antrian != 'undefined'){
                noantrian = msg.data.no_antrian;
            }
            if(msg.data.jenisantrian_id != 'undefined'){
                jenis_id = msg.data.jenisantrian_id;
            }
            if(msg.data.antrian_id != undefined){
                antrian_id = msg.data.antrian_id;
            }

            $.ajax({
                type: 'GET',
                url: 'http://localhost:8000/print/print-antrian',
                data: res['response']['data']['cetak'],
                success: function (res){
                    location.reload();
                },
                error: function () {
                    $.redirect("/antrian/dashboard/cetak-antrian-dashboard",{no_antrian:noantrian,jenisantrian_id:jenis_id,antrian_id:antrian_id});
                }
            });
        },
        error: function (res) {
            var errMessage = 'Gagal Diproses';
            var errText = 'Terjadi Kesalahan';
            new PNotify({
                title: errMessage,
                text: errText,
                addclass: 'alert alert-danger alert-arrow-right alert-styled-right',
                type: 'danger'
            });
        }
    });
});
