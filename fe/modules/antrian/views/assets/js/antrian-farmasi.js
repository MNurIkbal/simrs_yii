// $.fn.stepy.defaults.legend = false;
// $.fn.stepy.defaults.transition = 'fade';
// $.fn.stepy.defaults.duration = 150;
// $.fn.stepy.defaults.backLabel = '<i class=\"icon-arrow-left13 position-left\"></i> Back';
// $.fn.stepy.defaults.nextLabel = 'Next <i class=\"icon-arrow-right14 position-right\"></i>';

// $('#ambil-antrian-form').addClass('stepy-basic');
// $('.stepy-basic').stepy({
//     validate: true,
//     block: true,
//     next: function(index) {
//         if (!$('.stepy-basic').validate(validateVar)) {
//             return false
//         }
//     },
//     finish: function(index) {
//      if($('input:radio[name=\"status_pilih\"]:checked').length == 0){
//          var error = '<label id=\"status_pilih-error\" class=\"label label-danger label-roundless status_pilih-error\" for=\"status_pilih\">'+$('#title_farmasi_notif').val()+' Belum Dipilih</label>';
//         if ($('.status_pilih-error').length < 1) {
//             $('#error-opt-jaminan').append(error);
//         }
//          return false;
//      }
//     }
// });
// var validateVar = {
//     ignore: 'input[type=hidden]', // ignore hidden fields
//     errorClass: 'label label-danger label-roundless',
//     errorPlacement: function(error, element) {
//      if(element.parents('div').hasClass('opt-poly')){
//          error.appendTo($('#error-opt-poly'));
//      }else if(element.parents('div').hasClass('opt-dokter')){
//          error.appendTo($('#error-opt-dokter'));
//      }
//     },
//     rules: {
//         poly_pilih:'required',
//         status_pilih:'required'
//     },
//     messages: {
//      poly_pilih:'Ruangan Belum Dipilih',
//      status_pilih:'Racikan / Cara Bayar Belum Dipilih'
//     }
// }
// $('input[type=radio][name=poly_pilih]').each(function(e){
//  if($(this).parents('div').hasClass('is_disabled')){
//      $(this).prop('disabled',true);
//  }
// });
// $('input[type=radio][name=poly_pilih]').change(function(){
//  if($('input:radio[name=\"poly_pilih\"]:checked').length != 0){
//      var element_dokter = $('.dokter-dependent');
//      element_dokter.empty();
//      // var poly_id = $(this).val();
//     }
// })
// $('input[type=radio][name=status_pilih]').change(function() {
//  if($('input:radio[name=\"status_pilih\"]:checked').length != 0){
//         $('#error-opt-jaminan').empty();
//     }
// });

// $('.stepy-basic').find('.button-next').addClass('btn btn-info btn-more btn-huge-next');
// $('.stepy-basic').find('.button-back').addClass('btn btn-info btn-huge-prev pull-left');
// $('.stepy-navigator').css('text-align','end');

// $('#ambil-antrian-form').on('beforeSubmit', function(e){
//     var form = $(this);
//  var formData = form.serialize();
//     $.ajax({
//         url: form.attr('action'),
//         type: form.attr('method'),
//         data: formData,
//         success: function (res) {
//          var succMessage = 'Proses Berhasil!';
//          var succText = 'Antrian Dicetak';

//          var response = res.response.data
//          var no_antrian = response.no_antrian;
//          var jenisantrian_id = response.jenisantrian_id;
//          console.log(response)
//          var msg = res.response;
//          if(msg.text != undefined){
//              succText = msg.text;
//          }
//          if(msg.message != undefined){
//              succMessage = msg.message;
//          }
//             new PNotify({
//                 title: succMessage,
//                 text: succText,
//                 addclass: 'alert alert-success alert-arrow-right alert-styled-right',
//                 type: 'success'
//                });
//             if(msg.data.no_antrian != undefined){
//                 noantrian = msg.data.no_antrian;
//             }
//             if(msg.data.jenisantrian_id != undefined){
//                 jenis_id = msg.data.jenisantrian_id;
//             }
//             if(msg.data.antrian_id != undefined){
//                 antrian_id = msg.data.antrian_id;
//             }
//          // document.location = '/antrian/dashboard/cetak-antrian?no=' + no_antrian + '&jenisantrian_id=' + jenisantrian_id;
//         $.redirect("/antrian/dashboard/cetak-antrian-dashboard",{no_antrian:noantrian,jenisantrian_id:jenis_id,antrian_id:antrian_id});
//         },
//         error: function (res) {
//          var errMessage = 'Gagal Diproses';
//          var errText = 'Terjadi Kesalahan';
//             new PNotify({
//                 title: errMessage,
//                 text: errText,
//                 addclass: 'alert alert-danger alert-arrow-right alert-styled-right',
//                 type: 'danger'
//             });
//         }
//     });
// }).on('submit', function(e){
//     e.preventDefault();
// });

$('#clock1').clock({
    'dateFormat':'l, d F Y',
    'timeFormat':'H:i:s',
    'langSet':'id'
});

$('#type_norm').keyboard({
    display: {
        'bksp': "\u2190",
        'accept': 'Tutup',
        'normal': '.?123',
    },
    autoAccept: 'true',
    restrictInput: true,
    preventPaste: true,
    reposition: false,
    maxLength: 7,
    usePreview: false,
    layout: 'custom',
    appendLocally:true,
    css:{
        container:'container-keyboard-virtual',
        buttonDefault: 'btn btn-teal btn-lg'
    },
    customLayout: {
        'normal': [
            '7 8 9',
            '4 5 6 ',
            '1 2 3 ',
            '0 {bksp} {accept}'
        ],
    }
});

$('#btn_caripasien').click(function(e){
    $('#error-field-rm').empty();
    $('#pasien_info').empty();
    $('#field_pasien_id').val('');
    var no_rm = $('#type_norm').val();
    if(no_rm !== ''){
        $.ajax({
            type: 'GET',
            url: '/antrian/dashboard/get-pasien-apotek-by-norm',
            data: {no_rm:no_rm},
            success:function(response){
                var res='';
                console.log(response);
                if(response.status == 200){
                res='<div class="panel panel-flat">'+
                        '<div class="panel-heading">'+
                            '<h5 class="panel-title">Data Pasien</h5>'+
                        '</div>'+
                        '<div class="panel-body" style="text-align: left;">'+
                            '<div class="col-sm-6">'+
                                '<div class="col-sm-6">'+
                                    'No RM'+
                                '</div>'+
                                '<div class="col-sm-6">'+
                                    ': '+ response.data[0]["no_rekam_medik"] +
                                '</div>'+
                                '<div class="col-sm-6">'+
                                    'Nama'+
                                '</div>'+
                                '<div class="col-sm-6">'+
                                    ': '+ response.data[0]["nama_pasien"] +
                                '</div>'+
                                '<div class="col-sm-6">'+
                                    'Tanggal Lahir'+
                                '</div>'+
                                '<div class="col-sm-6">'+
                                    ': '+ moment(response.data[0]["tanggal_lahir"]).format('DD-MM-YYYY') +
                                '</div>'+
                            '</div>'+
                        '</div>'+
                        '<div class="panel-body warning" style="text-align: center;">'+
                        '</div>'+
                        '<div class="table-responsive">'+
                            '<table class="table" id="record_reseptur">'+
                                '<thead>'+
                                    '<tr class="bg-blue" style="background: #54be8b;">'+
                                        '<th>No</th>'+
                                        '<th>Tanggal Reseptur</th>'+
                                        '<th>No Pendaftaran</th>'+
                                        '<th>No Resep</th>'+
                                        '<th>Ruangan</th>'+
                                        '<th>Cetak Antrian</th>'+
                                    '</tr>'+
                                '</thead>'+
                                '<tbody>';

                                var nox = 0; 
                                $.each(response.data, function(i,v){
                                    nox++;
                                    res += '<tr onclick="setCheck(this)">'+
                                                '<th>'+nox+'</th>'+
                                                '<th>'+moment(v.tglreseptur).format('DD-MM-YYYY') +'</th>'+
                                                '<th>'+v.no_pendaftaran+'</th>'+
                                                '<th>'+v.noresep+'</th>'+
                                                '<th>'+v.ruangan_reseptur+'</th>'+
                                                '<th><input type="checkbox" name="cetak-antrian[]" value="'+v.reseptur_id+'"/></th>'+
                                            '</tr>';
                                })

                                res += '</tbody>'+
                            '</table>'+
                        '</div>'+
                        '<div class="panel-body">&nbsp;<div>'+
                        '<div class="panel-body">'+
                            '<div class="col-sm-12">'+
                                '<div class="col-sm-6">'+
                                    '<button type="button" class="btn btn-info" onclick="clearFind()"><b>Batal</b></button>'+
                                '</div>'+
                                '<div class="col-sm-6">'+
                                    '<button type="submit" class="btn btn-info"><b>Cetak</b></button>'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                    '</div>';

                    $('#pasien_info').html(res);

                    if (nox == 1) {
                        $(':checkbox').trigger('click');
                    };
                    
                }else{
                    new PNotify({
                        title: "Terjadi Kesalahan",
                        text: "Pasien tidak ditemukan",
                        addclass: "alert alert-warning alert-arrow-right alert-styled-right",
                        type: "warning",
                    });
                }
            }
        });
    }else{
        var error = '<label class=\"label label-danger label-roundless\">No RM belum diisi</label>';
        $('#error-field-rm').append(error);
    }
});

$(document).on('click', '.pilih_antrian', function (e) {
    e.preventDefault();
    var ruangan_id = $(this).attr('data-ruangan');
    var jenisantrian_id = $(this).attr('data-id-decrypt');
    var instalasi_id = $(this).attr('data-instalasi');
    var fungsiantrian_id = $(this).attr('data-fungsiantrian');

    $.ajax({
        url: '/antrian/dashboard/simpan-antrian',
        type: 'POST',
        data: {
            poly_pilih: ruangan_id,
            jenisantrian_id: jenisantrian_id,
            fungsiantrian_id: fungsiantrian_id,
            instalasi_id: instalasi_id
        },
        success: function (res) {
            var succMessage = 'Proses Berhasil!';
            var succText = 'Antrian Dicetak';
            var msg = res.response;
            var noantrian = '';
            var jenis_id = 'Mq';
            var antrian_id = 0;
            if (msg.text != undefined) {
                succText = msg.text;
            }
            if (msg.message != undefined) {
                succMessage = msg.message;
            }
            new PNotify({
                title: succMessage,
                text: succText,
                addclass: 'alert alert-success alert-arrow-right alert-styled-right',
                type: 'success'
            });
            if (msg.data.no_antrian != 'undefined') {
                noantrian = msg.data.no_antrian;
            }
            if (msg.data.jenisantrian_id != 'undefined') {
                jenis_id = msg.data.jenisantrian_id;
            }
            if (msg.data.antrian_id != undefined) {
                antrian_id = msg.data.antrian_id;
            }
            console.log(res['response']['data']['cetak']);
            $.ajax({
                type: 'GET',
                url: 'http://localhost:8000/print/print-antrian',
                data: res['response']['data']['cetak'],
                success: function (res){
                    location.reload();
                },
                error: function () {
                    $.redirect("/antrian/dashboard/cetak-antrian-dashboard", {
                        no_antrian: noantrian,
                        jenisantrian_id: jenis_id,
                        antrian_id: antrian_id
                    });
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


})


function setCheck (index) {
    console.log("asdasd");
}