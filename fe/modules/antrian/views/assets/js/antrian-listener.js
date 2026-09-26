var arrData = [];
var arrDataFarmasi = [];
var stat = 0;
var statFarmasi = 0;

$.ajaxSetup({
  cache:false
});

$(document).ready(function() {
    if (typeof is_slider !== 'undefined') {
        if(is_slider == 1){
            $('#idle_video')[0].load();
            $('#idle_video')[0].play();
        }
    } else {
        var newVideo = document.getElementById('idle_video');

        if (newVideo) {
            newVideo.addEventListener('ended', function() {
                this.currentTime = 0;
                this.play();
            }, false);

            newVideo.play();
        }
    }

    
    if (typeof videos !== 'undefined') {
        if (videos.length > 0) {
            player = document.getElementById('idle_video');
            player.setAttribute('src', videos[index]);
            player.play();
        }
    }

    $(".rslides").responsiveSlides();

    setInterval(clockUpdate, 1000);
});

$.getJSON("./../../json/setup.json", function (config) {
    if (config.origin == "true") {
        var socket = io.connect(window.location.origin);
    } else {
        var socket = io.connect(config.ip+':'+config.port);
    }

    socket.on('display-antrian-' + config.name, function (data) {
        console.log('test', data);
        var return_data = JSON.parse(data);
        $.each(return_data.data, function(key,value){
            if (key == 'panggil_antrian') {
                var loket_panggil = value.loket_id;

                var status = false;
                if (typeof _arrloket !== 'undefined') {
                    $.each(_arrloket, function( i, v ) {
                        if (parseInt(v) == parseInt(loket_panggil)) {
                            status = true;
                        };
                    });
                }

		console.log(loket_panggil);
		console.log(_arrloket);
		console.log(status);
                if (status) {
			console.log(konfig_jenisantriandetail);
                    if (typeof konfig_jenisantriandetail !== 'undefined' && konfig_jenisantriandetail == '0') {
                        if(konfig_jenisantriandetail == '0'){
				console.log("masukkkk")
                            arrData.push(value);
                        }
                    }else{
                        if (jenisantriandetail_id == value.jenisantriandetail_id) {
                            arrData.push(value);
                        }
                    }
                }
            } else if (key == 'set_antrian') {
                if (typeof newDesign !== 'undefined') {
                    var prefix = value.no_antrian.charAt(0);
                    var number = value.no_antrian.substring(1, 5);
                    var htmlNoAntrian = "<span class='prefix-color'>"+prefix+"</span><span class='light-navy'>"+number+"</span>";

                    $("#set-antrian-"+value.loket_id).html(htmlNoAntrian);
                } else {
                    $("#set-antrian-"+value.loket_id).html(value.no_antrian);
                }

                $("#set-antrian-"+value.loket_id).click();
            } else if (key == 'panggil_antrian_poli') {
                $("#set-antrian-"+value.ruangan_id+"-"+value.pegawai_id).html(value.nama_pasien + ' - ( ' + value.no_antrian + ' )' );
                $("#set-antrian-"+value.ruangan_id+"-"+value.pegawai_id).click();
            } else if (key == 'list_antrian_poli') {
                $(".list-data-antrian1").html('');
                $(".list-data-antrian2").html('');
                $(".pasien_sum").html('');
                var _html = '';
                var _html2 = '';
                var _no = 1;

                var _pembagi_dt_antrian = value.length / 2;
                var _sum_dt_antrian = 0;

                $.each(value, function( i, v ) {
                    if (i < _pembagi_dt_antrian) {
                        _html += '<tr>';
                            _html += '<td><span>'+v.ruangan_nama+'</span></td>';
                            _html += '<td width="10px" align="center"><span>'+v.count+'</span></td>';
                        _html += '</tr>';
                        _no++;
                        _sum_dt_antrian += v.count;
                    }
                });

                $.each(value, function( i, v ) {
                    if (i >= _pembagi_dt_antrian) {
                        _html2 += '<tr>';
                            _html2 += '<td><span>'+v.ruangan_nama+'</span></td>';
                            _html2 += '<td width="10px" align="center"><span>'+v.count+'</span></td>';
                        _html2 += '</tr>';
                        _no++;
                        _sum_dt_antrian += v.count;
                    }
                });

                $(".list-data-antrian1").html(_html);
                $(".list-data-antrian2").html(_html2);
                $(".pasien_sum").html(_sum_dt_antrian);

            } else if (key == 'proses_antrian_farmasi') {
                generateDataFarmasi(value.data,value.ruangan);
            } else if (key == 'panggil_antrian_farmasi') {
                var loket_panggil = value.loket_id;

                var status = false;
                if (typeof _arrloket !== 'undefined') {
                    $.each(_arrloket, function( i, v ) {
                        if (parseInt(v) == parseInt(loket_panggil)) {
                            status = true;
                        };
                    });
                }

                if (status) {
                    arrDataFarmasi.push(value);
                };

            } else if (key == 'autirefresh_layarantrian') {
                location.reload();
            } else if (key == 'panggil_antrian_poliklinik_with_dokter') {
                let jadwalId = value.jadwaldokter_id;

                let status = false;
                
                if (typeof _arrJadwal !== 'undefined') {
                    $.each(_arrJadwal, function( i, v ) {
                        if (parseInt(v) == parseInt(jadwalId)) {
                            status = true;
                        };
                    });
                }

                if (status) {
                    arrData.push(value);
                }
            }
        });
    });

    // on notification push
    socket.on('display-notif-' + config.name, function (data) {
        console.log('test2', data);
        const return_data = JSON.parse(data);
        $.each(return_data.data, function (key, value) {
            if (key == 'list_notification') {
                $('.dis-footer').html("");
                let list_notif = [];
                if (list_notif.length < 1) {
                    list_notif.push("Selamat Datang Di Rumah Sakit Sirs")
                } else {
                    list_notif = [];
                }
                $.each(value, function (i, val) {
                    const text = `${val.nama_pegawai} dengan jadwal ${val.jadwaldokter_mulai} - ${val.jadwaldokter_tutup} Poliklinik ${val.ruangan_nama} ${val.notifikasi}`;
                    list_notif.push(text);
                });
                $('.dis-footer').html(`<p class="text-center marq-footer typewrite" data-period="10000"><span class="wrap"></span>`);

                const elements = document.getElementsByClassName('typewrite');
                const toRotate = list_notif;
                for (let i=0; i < elements.length; i++) {
                    const period = elements[i].getAttribute('data-period');
                    if (toRotate) {
                        new TxtType(elements[i], toRotate, period);
                    }
                }
                // INJECT CSS
                const css = document.createElement("style");
                css.type = "text/css";
                css.innerHTML = ".typewrite > .wrap { border-right: 0.08em solid #fff}";
                document.body.appendChild(css);
            }
        })

    });
});

$(function() {
    $('.velocity-panggil').on('click', function (e) {
        // Get animation class and panel
        var animation = $(this).data("animation");

        // Add animation class to panel element
        $(this).velocity("callout." + animation, { stagger: 500 });
        $(this).parents(".dis-panel-right-isi").velocity("callout." + animation, { stagger: 500 });
        e.preventDefault();
    });

    $.ajax({
        url: '/antrian/dashboard/fetch-info-notif',
        type: 'get',
        success: function() {

        }
    })
});

function callAntrian(text) {
    stat = 1;
    var player = $("#playerAudio");
    var arrayText = text.split(" ");
    arrayText.push("stop");
    arrayText = arrayText.filter(Boolean);
    var index = 0;

    player[0].defaultPlaybackRate = 1;
    player[0].src = window.location.origin + "/media/sounds/" + arrayText[index] + ".wav";
    player[0].play();

    player[0].addEventListener("ended", function () {
        index = index + 1;

        if (index < arrayText.length) {
            player[0].defaultPlaybackRate = index == arrayText.length - 3 ? 1.5 : 1.2;
            if (arrayText[index] == "stop") {
                setTimeout(function() {
                    removeDataArr();
                    stat = 0;
                }, 1000);
            } else {
                player[0].src = window.location.origin + "/media/sounds/qms/" + arrayText[index] + ".mp3";
                player[0].play();
                // setTimeout(function() {
                //     removeDataArr();
                //     stat = 0;
                // }, 1000);
            }
        }
    });
}

window.setInterval(function(){
    if (stat == 0 && arrData.length >= 1) {
        var value = arrData[0];
        if (value.tipe != undefined) {
            $("#no_antrian_panggil_"+value.tipe).html(value.no_antrian);
            $("#no_loket_panggil_"+value.tipe).html('Counter '+value.no_loket);

            if (value.extend_text != undefined) {
                $("#extend-text_"+value.tipe).html(value.extend_text);
            };

            $("#no_antrian_panggil_"+value.tipe).click();
            callAntrian(value.text_panggil);
        } else {
            if (newDesign != undefined) {
                var prefix = value.no_antrian.charAt(0);
                var number = value.no_antrian.substring(1, 6);
                var htmlNoAntrian = "<span class='prefix-color'>"+prefix+"</span><span class='dark-navy'>"+number+"</span>";

                if(isPoli !== 'undefined' && isPoli == true) {
                    let jadwalId = value.jadwaldokter_id
                    $("#no_antrian_panggil_"+jadwalId).html(htmlNoAntrian);
                } else {
                    $("#no_antrian_panggil").html(htmlNoAntrian);
                    $("#no_loket_panggil").html(value.no_loket);
                }

                if (value.extend_text != undefined) {
                    $("#extend-text").html(value.extend_text);
                };
            } else {
                $("#no_antrian_panggil").html(value.no_antrian);
                $("#no_loket_panggil").html('Counter '+value.no_loket);

                if (value.extend_text != undefined) {
                    $("#extend-text").html(value.extend_text);
                };
            }

            $("#no_antrian_panggil").click();
            callAntrian(value.text_panggil);
        }
    }
}, 1000);

function removeDataArr() {
    arrData.splice(0,1);
}

function generateDataFarmasi(data,ruangan) {
    $("#tb-r-blm-"+ruangan).html("");
    $("#tb-nr-blm-"+ruangan).html("");

    var _tb_r_blm = "";
    if (data.racikan.belum_proses !== undefined || data.racikan.belum_proses.length != 0) {
        var _no = 1;
        data.racikan.belum_proses.forEach(function(element){
           if (element.antrian_farmasi != null) {
                _tb_r_blm +="<tr>"+
                            "<td>"+_no+"</td>"+
                            "<td>"+element.no_antrian+"</td>"+
                            "<td>"+element.stat_antrian_farmasi+"</td>"+
                        "</tr>";
                _no++;
           }
        });
    }

    var _tb_nr_blm = "";
    if (data.non_racikan.belum_proses !== undefined || data.non_racikan.belum_proses.length != 0) {
        var _no = 1;
        data.non_racikan.belum_proses.forEach(function(element){
           if (element.antrian_farmasi != null) {
            _tb_nr_blm +="<tr>"+
                        "<td>"+_no+"</td>"+
                        "<td>"+element.no_antrian+"</td>"+
                        "<td>"+element.stat_antrian_farmasi+"</td>"+
                    "</tr>";
            _no++;
           }
        });
    }

    $("#tb-r-blm-"+ruangan).html(_tb_r_blm);
    $("#tb-nr-blm-"+ruangan).html(_tb_nr_blm);
}

var TxtType = function(el, toRotate, period) {
    this.toRotate = toRotate;
    this.el = el;
    this.loopNum = 0;
    this.period = parseInt(period, 10) || 10000;
    this.txt = '';
    this.tick();
    this.isDeleting = false;
};

TxtType.prototype.tick = function() {
    var i = this.loopNum % this.toRotate.length;
    var fullTxt = this.toRotate[i];

    if (this.isDeleting) {
        this.txt = fullTxt.substring(0, this.txt.length - 1);
    } else {
        this.txt = fullTxt.substring(0, this.txt.length + 1);
    }
    this.el.innerHTML = '<span class="wrap">'+this.txt+'</span>';

    var that = this;
    var delta = 100 - Math.random() * 50;

    if (this.isDeleting) { delta /= 2; }

    if (!this.isDeleting && this.txt === fullTxt) {
        delta = this.period;
        this.isDeleting = true;
    } else if (this.isDeleting && this.txt === '') {
        this.isDeleting = false;
        this.loopNum++;
        delta = 50;
    }

    setTimeout(function() {
        that.tick();
    }, delta);
};

function clockUpdate() {
    var date = new Date();
    function addZero(x) {
        if (x < 10) {
            return x = '0' + x;
        } else {
            return x;
        }
    }

    function twelveHour(x) {
        if (x > 12) {
            return x = x - 12;
        } else if (x == 0) {
            return x = 12;
        } else {
            return x;
        }
    }

    var h = addZero(twelveHour(date.getHours()));
    var m = addZero(date.getMinutes());
    var s = addZero(date.getSeconds());

    $('#time').text(h + ':' + m)
}

window.setInterval(function(){
    if (stat == 0 && arrData.length >= 1) {
        var value = arrData[0];
        if (value.tipe != undefined) {
            $("#no_antrian_panggil_"+value.tipe).html(value.no_antrian);
            $("#no_loket_panggil_"+value.tipe).html('Counter '+value.no_loket);

            if (value.extend_text != undefined) {
                $("#extend-text_"+value.tipe).html(value.extend_text);
            };

            $("#no_antrian_panggil_"+value.tipe).click();
            callAntrian(value.text_panggil);
        } else {
            if (newDesign != undefined) {
                var prefix = value.no_antrian.charAt(0);
                var number = value.no_antrian.substring(1, 4);
                var htmlNoAntrian = "<span class='prefix-color'>"+prefix+"</span><span class='dark-navy'>"+number+"</span>";

                if(isPoli !== 'undefined' && isPoli == true) {
                    let jadwalId = value.jadwaldokter_id
                    $("#no_antrian_panggil_"+jadwalId).html(htmlNoAntrian);
                } else {
                    $("#no_antrian_panggil").html(htmlNoAntrian);
                    $("#no_loket_panggil").html(value.no_loket);
                }

                if (value.extend_text != undefined) {
                    $("#extend-text").html(value.extend_text);
                };
            } else {
                $("#no_antrian_panggil").html(value.no_antrian);
                $("#no_loket_panggil").html('Counter '+value.no_loket);

                if (value.extend_text != undefined) {
                    $("#extend-text").html(value.extend_text);
                };
            }

            $("#no_antrian_panggil").click();
            callAntrian(value.text_panggil);
        }
    }
}, 1000);


window.setInterval(function(){
    // console.log(statFarmasi, arrDataFarmasi.length);
    if (statFarmasi == 0 && arrDataFarmasi.length >= 1) {
        var value = arrDataFarmasi[0];

        if (value.jenis_resep == "Racikan") {
            $("#no_antrian_panggil_r").html(value.no_antrian);
            $("#counter_panggil_r").html(value.no_loket);
            callAntrianFarmasi(value.text_panggil);
        } else {
            $("#no_antrian_panggil_nr").html(value.no_antrian);
            $("#counter_panggil_nr").html(value.no_loket);
            callAntrianFarmasi(value.text_panggil);
        }
    }
}, 1000);

function callAntrianFarmasi(text) {
    statFarmasi = 1;
    var player = $("#playerAudio");
    var arrayText = text.split(" ");
    arrayText.push("stop");
    arrayText = arrayText.filter(Boolean);
    var index = 0;

    player[0].defaultPlaybackRate = 1;
    player[0].src = window.location.origin + "/media/sounds/" + arrayText[index] + ".wav";
    player[0].play();

    player[0].addEventListener("ended", function () {
        index = index + 1;

        if (index < arrayText.length) {
            player[0].defaultPlaybackRate = index == arrayText.length - 3 ? 1.5 : 1.2;
            if (arrayText[index] == "stop") {
                console.log("stop");
                setTimeout(function() {
                    removeDataArrFarmasi();
                    statFarmasi = 0;
                }, 1000);
            } else {
                player[0].src = window.location.origin + "/media/sounds/qms/" + arrayText[index] + ".mp3";
                player[0].play();
                // setTimeout(function() {
                //     removeDataArrFarmasi();
                //     statFarmasi = 0;
                // }, 1000);
            }
        }
    });
}

function removeDataArrFarmasi() {
    arrDataFarmasi.splice(0,1);
}
