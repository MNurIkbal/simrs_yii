$(document).ready(function() {
    var defaultAntrian = location.search.split('default=')[1];

    if (defaultAntrian != undefined) {
        setTimeout(function() {
            document.getElementById("back-antrian").href = "/antrian?default=false";
        }, 100);
    }
    
    var xhideHeader = localStorage.getItem('hideHeader');
    if (xhideHeader == 1) {
        showFullHeader();
    } else {
        showFullHeader();
    };

    $('.hd-up').on('click', function() {
        hideFullHeader();
    });

    $('.hd-down').on('click', function() {
        showFullHeader();
    })

    //From Page
    function hideFullHeader() {
        fullscreen  = 1;
         $('html').addClass('overflow');
        $('html body').css('overflow', 'hidden !important'); 
        $("#navbar-second").hide(1000);
        $(".navbar-right").hide(1000);
        if (defaultAntrian == undefined) {
            $(".back-antrian").hide();
        }
        $('.navbar-title').hide(1000);
        $('.navbar-position').hide(1000);
        $('.back-form').show();
        setTimeout(function () {
            $(".page-container").css('margin-top','0px !important');
            $(".hd-up").css('display', 'none');
            $(".hd-down").css('display', 'block');
        }, 600);
        openFullscreen();
    }

    function showFullHeader() {
        fullscreen  = 0;
        $('html').removeClass('overflow');
        $('html body').css('overflowY', 'auto'); 
        $("#navbar-second").show(1000);
        $(".navbar-right").show(1000);
        $('.navbar-title').show(1000);
        $('.navbar-position').show(1000);
        $('.back-form').hide();
        $(".back-antrian").show(1000);
        setTimeout(function () {
            $(".page-container").css("cssText", "margin-top: 0px !important;");
            $(".hd-down").css('display', 'none');
            $(".hd-up").css('display', 'block');
        }, 600);
        localStorage.setItem('hideHeader', 0);
        closeFullscreen();
    }

    var elem = document.documentElement;

    function openFullscreen() {
      if (elem.requestFullscreen) {
        elem.requestFullscreen();
      } else if (elem.mozRequestFullScreen) { /* Firefox */
        elem.mozRequestFullScreen();
      } else if (elem.webkitRequestFullscreen) { /* Chrome, Safari & Opera */
        elem.webkitRequestFullscreen();
      } else if (elem.msRequestFullscreen) { /* IE/Edge */
        elem.msRequestFullscreen();
      }
    }

    function closeFullscreen() {
      if (document.exitFullscreen) {
        document.exitFullscreen().catch(err => Promise.resolve(err))
      } else if (document.mozCancelFullScreen) {
        document.mozCancelFullScreen().catch(err => Promise.resolve(err));
      } else if (document.webkitExitFullscreen) {
        document.webkitExitFullscreen().catch(err => Promise.resolve(err));
      } else if (document.msExitFullscreen) {
        document.msExitFullscreen().catch(err => Promise.resolve(err));
      }
    }
});

$(document).on('hide.bs.modal','.modal', function () {
   PNotify.removeAll();
})

async function listenStatusReservationUpdate(payload, randString, printData) {
  let config = await $.ajax({
                  url: "./../../json/setup.json",
                  global: false,
              });

  if (config.origin == "true") {
      var socket = io.connect(window.location.origin);
  } else {
      var socket = io.connect(config.ip+':'+config.port);
  }

  let communicationReceived = false;

  const timeoutDuration = 120000; // 120 seconds

  socket.on('connect', () => {
    const timeout = setTimeout(() => {
      if (!communicationReceived) {
        socket.close();
        hideLoaderCustom();
        docoNotification('warning', '<b>Proses Gagal</b>', 'Antrian berhasil dibuat tetapi proses pendaftaran kunjungan gagal (timeout). <b>Silakan hubungi petugas pendaftaran untuk melakukan pendaftaran secara manual.</b>', false);
      }
    }, timeoutDuration);

    socket.on(`${channelListenerName}:${randString}`, (message) => {
        communicationReceived = true; // ✅ mark as successful communication
        clearTimeout(timeout); // stop the timeout once message received

        const _data = $.parseJSON(message);

        if (_data.registration_status) {
            updateLoaderCustom('Memproses data', '(100%)', 'Kunjungan Anda sudah dibuat, mohon tunggu sebentar ya, sistem sedang mencetak nomor antrian Anda.');
            $('#modal_backdrop').modal('hide');
            redirectToPrintAntrian(printData);
        } else {
            let defaultMessage = 'Antrian berhasil dibuat tetapi proses pendaftaran kunjungan gagal.';
            let message = _data.message ? _data.message : defaultMessage;

            docoNotification('warning', '<b>Proses Gagal</b>', message+' <b>Silakan hubungi petugas pendaftaran untuk melakukan pendaftaran secara manual.</b>', false);
            hideLoaderCustom();
        }
    });
  })

  socket.on("connect_error", (err) => {
    docoNotification('warning', '<b>Proses Gagal</b>', 'Proses pendaftaran kunjungan gagal. <b>Silakan hubungi petugas pendaftaran untuk melakukan pendaftaran secara manual.</b>', false);
    hideLoaderCustom();
    socket.close();
  });
}

function showLoaderCustom(message = 'Memproses data', percent = '', description = '') {
  if (
      typeof $('.loader-section') === 'undefined' ||
      $('.loader-section').length === 0
  ) {
      $('body').append(`
              <div class="loader-section">
                  <div class="loader-overlay"></div>
                  <div class="indicator-custom">
                      <div class="header-items">
                        <svg width="24px" height="14px" style="transform: scale(1.3)">
                            <polyline id="back" points="1 6 4 6 6 11 10 1 12 6 15 6"></polyline>
                            <polyline id="front" points="1 6 4 6 6 11 10 1 12 6 15 6"></polyline>
                        </svg>
                        <div class="loading-text">
                          <span class="title">${message}</span>
                          <span class="dots"></span>
                          <span class="percent">${percent}</span>
                        </div>
                      </div>
                      <div class="header-items">
                        <div class="description loading-text">
                          ${description}
                        </div>
                      <div>
                  </div>
              </div>
          `);
  }
  $('html').css('overflow-y', 'hidden');
};

function updateLoaderCustom(message = 'Memproses data', percent='', description = '') {
  if (
      typeof $('.loader-section') !== 'undefined' ||
      $('.loader-section').length === 1
  ) {
      $('body').find('.loader-section .header-items span.title').html(message);
      if (percent.length>0) {
        $('body').find('.loader-section .header-items div span.percent').html(percent);
      }
      if (description) {
        $('body').find('.loader-section .header-items div.description').html(description);
      }
  }
}

function hideLoaderCustom() {
  $('.loader-section').remove();
  $('html').css('overflow-y', 'scroll');
}

function redirectToPrintAntrian(data) {
  $.ajax({
      type: 'POST',
      url: '/antrian/dashboard/cetak-antrian-dashboard',
      timeout: (60 * 1000),
      data: data,
      global: false,
      success: function (res){
          // location.reload();
          updateLoaderCustom('Memproses data ', '(100%)', 'Nomor antrian sedang dicetak. Silakan tunggu sebentar sampai cetakan keluar...');
          $.redirect('/antrian/dashboard/cetak-antrian-dashboard',
            {
              antrian_id: data.antrian_id,
              no_antrian: data.no_antrian,
              jenisantrian_id: 2121,
            }
          );
      },
      error: function (error) {
        docoNotification('warning', '<b>Proses Gagal</b>', 'Pendaftaran kunjungan Anda berhasil, tetapi cetakan belum keluar. <b>Mohon hubungi petugas pendaftaran agar dibantu mencetak ulang lewat pendaftaran.</b>', false);
        hideLoaderCustom();
      }
  })
}

function setPayloadAutoDaftar(data) {
  return [
      {
          "carabayar_id": data?.carabayar_id,
          "penjamin_id": data?.penjamin_id,
          "asalrujukan_id": data?.rujukan_id,
          "antrian_id": data?.antrian_id,
          "no_rekam_medik": data?.no_rekam_medik,
          "no_asuransi": data?.no_asuransi,
          "pendaftaranol_id": data?.pendaftaranol_id, // encrypted
          "pendaftaran_id": null,
          "dokter_perujuk": null,
          "is_kolektif": "false",
          "is_multi_payer": "",
          "tgl_pendaftaran": moment().format("YYYY-MM-DD HH:mm:ss"),
          "ruangan_id": data?.ruangan_id,
          "jeniskasuspenyakit_id": "23",
          "dokter_id": data?.pegawai_id,
          "keadaan_masuk": "",
          "transportasi": "",
          "keterangan": "",
          "referal": "",
          "jadwaldokter_id": data?.jadwaldokter_id,
          "no_pendaftaranol": data?.kodebooking,
          "nama_pasien": data?.nama_pasien,
          "jeniskelamin": data?.jeniskelamin,
          "pj_pengantar": "990", //pasien
          "no_telepon_pasien": data?.no_telepon_pasien,
          "no_kartu_bpjs": data?.no_bpjs,
          "no_rujukan_bpjs": data?.no_rujukan,
          "jenis_kunjungan_bpjs": data?.jenis_kunjungan_bpjs,
          "nama_pengguna": "superadmin",
          "no_surat_kontrol": data?.nomorreferensi
      }
  ];
}
