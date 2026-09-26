
// console.log(jsonAntrian);
// generateTable('#table-antrian-lewati', jsonAntrian);
// Event Ready
// fetchSisaAntrian();
// function fetchSisaAntrian() {
    $.ajaxSetup({
      cache:false
    });
    $.getJSON("./../../json/setup.json", function (config) {
        const socket = io.connect('http://' + config.ip + ':' + config.port);
        // get sisa antrian
        socket.on('panggil-antrian-' + config.name, function (data) {
            const return_data = JSON.parse(data);
            $.each(return_data.data, function (key, value) {
                // console.log(value)
                if (key == 'list_sisa_antrian') {
                    let html = '';
                    $.each(value, function (kel, group) {
                        html += `
                            <div class="col-md-4">
                                <div><h6 class="text-bold text-center">${kel}</h6></div>`;
                                $.each(group, function (k, val) {
                                    if (val.kode == 'A') {
                                        // console.log(val.total_sisa)

                                    }
                                    const klasifikasi = (val.klasifikasipasien_nama) ? val.klasifikasipasien_nama : '';
                                    html += `
                                        <div class="row container-info">
                                            <div class="col-md-12 head-info">
                                                <h6 class="text-bold text-center">${val.kode} - ${klasifikasi} (${val.group_carabayar})</h6>
                                            </div>
                                            <div class="col-md-12 body-info">
                                                <h1 class="text-bold text-center">${val.total_sisa}</h1>
                                            </div>
                                        </div>
                                    `;
                                });
                        html += `
                            </div>
                        `;
                    });
                    $('#list-info').html(html);
                }
            })
    
        });
    });
// }

$(document).ready(function () {

    $('#btnNext').attr('disabled', false);
    $('#btnPilih').attr('disabled', true);
    $('#btnPanggilUlang').attr('disabled', true);
    $('#btnLewati').attr('disabled', true);
    $('#btnBatal').attr('disabled', true);

    // Generate Table
    refreshTable();

    const jenisantrian_id = $('#jenisantrian_id').val();
    const loading_text = 'loading';
    $.ajax({
        url: `/antrian/panggil-antrian/get-sisa-antrian?id=${jenisantrian_id}`,
        type: 'get',
        beforeSend: function () {
            var _html = '<div class="text-center">';
            _html += '<h3><i class="icon-spinner4 spinner position-center"></i>&nbsp;&nbsp;<b>' + loading_text + ' . . . </b></h3>';
            _html += '</div>';
            $('#list-info').html(_html);
        },
        success: function() {
        }
    });
});

$('#btnNext').click(function() {
    var loading_text = i18next.t("memuat");
    $.ajax({
        type: 'GET',
        url: '/antrian/panggil-antrian/next-antrian',
        // data: _data,
        dataType: 'JSON',
        beforeSend: function () {
            var _html = '<i class="icon-spinner4 spinner position-center"></i>&nbsp;&nbsp;<b>' + loading_text + ' . . . </b>';
            $(this).html(_html);
        },
        success: function (res) {
            // console.log(res.length);
            $('#btnNext').attr('disabled', false);
            $('#btnPilih').attr('disabled', true);
            $('#btnPanggilUlang').attr('disabled', true);
            $('#btnLewati').attr('disabled', true);
            $('#btnBatal').attr('disabled', true);
            if (res.data !== undefined){

                $('#btnNext').attr('disabled', true);
                $('#btnPilih').attr('disabled', false);
                $('#btnPanggilUlang').attr('disabled', false);
                $('#btnLewati').attr('disabled', false);
                $('#btnBatal').attr('disabled', false);

                $('#no_antrian').html(res.data.no_antrian);
                $('#jumlah_panggil').html(res.data.panggilan_ke);
                $('#hide_antrian_id').val(res.data.antrian_id);
                $('#btnPilih').attr('data-antrian_id', res.data.antrian_id);
                $('#btnLewati').attr('data-antrian_id', res.data.antrian_id);
                $('#btnBatal').attr('data-antrian_id', res.data.antrian_id);

                // var text = res.teks_panggil;
                // var player = $("#playerAudio");
                // var arrayText = text.split(" ");
                // arrayText.push("stop");
                // arrayText = arrayText.filter(Boolean);

                // var index = 0;

                // player[0].defaultPlaybackRate = 1;
                // player[0].src = window.location.origin + "/media/sounds/" + arrayText[index] + ".mp3";
                // player[0].play();

                // player[0].addEventListener("ended", function () {
                //     index = index + 1;

                //     if (index < arrayText.length) {
                //         player[0].defaultPlaybackRate = index == arrayText.length - 3 ? 1.5 : 1.2;
                //         if (arrayText[index] == "stop") {
                //             console.log("masuk");
                //             // hapusAntrianAudio();
                //         } else {
                //             console.log(arrayText[index]);
                //             player[0].src = window.location.origin + "/media/sounds/" + arrayText[index] + ".mp3";
                //             player[0].play();
                //         }
                //     }
                // });

            }
            if(panggilan_ke > 0){
             $('#btnNext').attr('disabled', false);
             $('#btnPilih').attr('disabled', false);
             $('#btnPanggilUlang').attr('disabled', false);
             $('#btnLewati').attr('disabled', false);
             $('#btnBatal').attr('disabled', false);
            }
        },
    });
});


$('.btnPanggilUlang').on('click', function () {
    var loading_text = i18next.t("memuat");
    var antrian_id = $('#hide_antrian_id').val();
    if (typeof $(this).data('antrian_id') !== 'undefined') {
        antrian_id = $(this).data('antrian_id');
    }

    $.ajax({
        type: 'GET',
        url: '/antrian/panggil-antrian/panggil-ulang?antrian_id=' + antrian_id,
        dataType: 'JSON',
        beforeSend: function () {
            $('#btnNext').attr('disabled', true);
            $('#btnPilih').attr('disabled', false);
            $('#btnPanggilUlang').attr('disabled', false);
            $('#btnLewati').attr('disabled', false);
            $('#btnBatal').attr('disabled', false);
            var _html = '<i class="icon-spinner4 spinner position-center"></i>&nbsp;&nbsp;<b>' + loading_text + ' . . . </b>';
            $(this).html(_html);
        },
        success: function (res) {
            $('#no_antrian').html(res.data.no_antrian);
            $('#jumlah_panggil').html(res.data.panggilan_ke);
            $('#btnPilih').attr('data-antrian_id', antrian_id);
            if (res.data.panggilan_ke >= $('#hide_limit_antrian').val()) {
                $('#btnPanggilUlang').attr('disabled', true);
            }

            // var text = res.teks_panggil;
            // var player = $("#playerAudio");
            // var arrayText = text.split(" ");
            // arrayText.push("stop");
            // arrayText = arrayText.filter(Boolean);

            // var index = 0;

            // player[0].defaultPlaybackRate = 1;
            // player[0].src = window.location.origin + "/media/sounds/" + arrayText[index] + ".mp3";
            // player[0].play();

            // player[0].addEventListener("ended", function () {
            //     index = index + 1;

            //     if (index < arrayText.length) {
            //         player[0].defaultPlaybackRate = index == arrayText.length - 3 ? 1.5 : 1.2;
            //         if (arrayText[index] == "stop") {
            //             console.log("masuk");
            //             // hapusAntrianAudio();
            //         } else {
            //             console.log(arrayText[index]);
            //             player[0].src = window.location.origin + "/media/sounds/" + arrayText[index] + ".mp3";
            //             player[0].play();
            //         }
            //     }
            // });
        },
    });
});

$('.btnPilih').on('click', function (event) {
    event.preventDefault();
    var antrian_id = $(this).attr('data-antrian_id');
    $(this).docoForm("click", {
        url: '/antrian/panggil-antrian/pilih?antrian_id=' + antrian_id,
        method: "GET",
        type: "json",
        success: function (res) {
             var promise = refreshTable();
            promise.done(function(){

                // $('#modal_backdrop').hide();
                $('#btnNext').attr('disabled', false);
                $('#btnPilih').attr('disabled', true);
                $('#btnPanggilUlang').attr('disabled', true);
                $('#btnLewati').attr('disabled', true);
                $('#btnBatal').attr('disabled', true);

                $('#no_antrian').html('-');
                panggilan_ke = 0;
                $('#jumlah_panggil').html('0');
                $('#hide_antrian_id').val('');

                // send to form pendaftaran
                console.log(res);
                $('.antrian-id').val(res.response.antrian_id).trigger('change');
                $('.pasien-id').val(res.response.pasien_id).trigger('change');
                $('.no-antrian').val(res.response.no_antrian);
                $('.close').trigger('click');
                
            });
            
        }
    });
});


$('#btnLewati').on('click', function (event) {
    event.preventDefault();
    var antrian_id = $(this).attr('data-antrian_id')
    
    $(this).docoForm("click", {
        url: '/antrian/panggil-antrian/lewati?antrian_id=' + antrian_id,
        method: "GET",
        type: "json",
        success: function (data) {
            var promise = refreshTable();
            promise.done(function(){

                $.ajax({
                    type: 'GET',
                    url: '/antrian/panggil-antrian/count-sisa-antrian',
                    dataType: 'JSON',
                    beforeSend: function (res) {
                        $('#queue').html('...');
                    },
                    success: function (res) {
                        if (res) {
                            $('#queue').html(res);
                            if (res == '0') {
                                $('#btnNext').attr('disabled', false);
                                $('#btnPilih').attr('disabled', true);
                                $('#btnPanggilUlang').attr('disabled', true);
                                $('#btnLewati').attr('disabled', true);
                                $('#btnBatal').attr('disabled', true);
                            } else {
                                $('#btnNext').attr('disabled', false);
                                $('#btnPilih').attr('disabled', true);
                                $('#btnPanggilUlang').attr('disabled', true);
                                $('#btnLewati').attr('disabled', true);
                                $('#btnBatal').attr('disabled', true);
                            }
                        } else {
                            $('#queue').html('0');
                            $('#btnNext').attr('disabled', false);
                            $('#btnPilih').attr('disabled', true);
                            $('#btnPanggilUlang').attr('disabled', true);
                            $('#btnLewati').attr('disabled', true);
                            $('#btnBatal').attr('disabled', true);
                        }
                    },
                });

                $('#no_antrian').html('-');
                panggilan_ke = 0;
                $('#jumlah_panggil').html('0');
                $('#hide_antrian_id').val('');

            });
            
        }
    });
});

var btnBatal;
$('.btnBatal').on('click', function (event) {
    event.preventDefault();
    var antrian_id = $(this).attr('data-antrian_id');
    var recheck = false;

    if (typeof $(this).data('antrian_id') !== 'undefined') {
        antrian_id = $(this).data('antrian_id');
        recheck = true;
    }

    $(this).docoForm("click", {
        url: '/antrian/panggil-antrian/batal?antrian_id=' + antrian_id,
        method: "GET",
        type: "json",
        success: function (data) {
            var promise = refreshTable();
            promise.done(function(){
                $.ajax({
                    type: 'GET',
                    url: '/antrian/panggil-antrian/count-sisa-antrian',
                    dataType: 'JSON',
                    beforeSend: function (res) {
                        $('#queue').html('...');
                    },
                    success: function (res) {
                        if (res) {
                            $('#queue').html(res);
                        } else {
                            $('#queue').html('0');
                        }
                    },
                });
                $('#btnNext').attr('disabled', false);
                $('#btnPilih').attr('disabled', true);
                $('#btnPanggilUlang').attr('disabled', true);
                $('#btnLewati').attr('disabled', true);
                $('#btnBatal').attr('disabled', true);

                $('#no_antrian').html('-');
                panggilan_ke = 0;
                $('#jumlah_panggil').html('0');
                $('#hide_antrian_id').val('');
            });
            
        }
    });
});
