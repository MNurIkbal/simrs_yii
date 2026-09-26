/**
 * @Author: Andri Amirul Sonjaya
 * @Date:   2022-05-29
 */
var { maksFrekuensi, dataPerjadwalan, fieldName, maxKeteranganDropOut } =
  phpVars;

$(function () {
  $(".pickadate-input").pickadate({
    applyClass: "bg-slate-600",
    cancelClass: "btn-default",
    format: "yyyy-mm-dd",
  });

  $(`.jam_mulai`).pickatime({
    format: "HH:i",
    interval: 5,
  });

  $(`.jam_selesai`).pickatime({
    format: "HH:i",
    interval: 5,
  });
  $(`.btn-jadwal-list`).on("click", function () {
    setTimeout(() => {
      $("#modalProgramTerapi").css("z-index", "1041");
    }, 10);
  });
  setTimeout(() => {
    $("#modalProgramTerapi").css("z-index", "1041");
  }, 10);
  $(`.picker__holder`).removeClass(`picker_modal-two`);
  $(`.picker__holder`).addClass(`picker_modal-one`);
  for (let index = 0; index < maksFrekuensi; index++) {
    const nextIndex = index + 1;
    const splitTglAwal = dataPerjadwalan[index].tgl_penjadwalan_awal.split(" ");
    const splitTglAkhir =
      dataPerjadwalan[index].tgl_penjadwalan_akhir.split(" ");
    $(`#schedule-${index}`).pickadate("set").set("select", splitTglAwal[0]);
    $(`#jamMulai-${index}`).pickatime("set").set("select", splitTglAwal[1]);
    $(`#jamSelesai-${index}`).pickatime("set").set("select", splitTglAkhir[1]);

    initiateData(index);

    if ($(`#schedule-${index}`).hasClass("disabled")) {
      $(`#schedule-${index}`).prop("disabled", true);
    }
    if ($(`#jamMulai-${index}`).hasClass("disabled")) {
      $(`#jamMulai-${index}`).prop("disabled", true);
    }
    if ($(`#jamSelesai-${index}`).hasClass("disabled")) {
      $(`#jamSelesai-${index}`).prop("disabled", true);
    }

    if (!$(`.status_kunjungan_${index}`).hasClass(`isFirst`)) {
      $(`.status_kunjungan_${index}`).attr("disabled", true);
    }
    $(`.status_kunjungan_${index}`).on("change", function () {
      for (
        let indexChild = nextIndex;
        indexChild < maksFrekuensi;
        indexChild++
      ) {
        if ($(this).val() == "") {
          $(`.status_kunjungan_${indexChild}`).val("").trigger(`change`);
          $(`.status_kunjungan_${indexChild}`).attr("disabled", true);
        } else {
          $(`.status_kunjungan_${nextIndex}`).attr("disabled", false);
        }
      }
    });

    // Validasi jumlah karakter pada keterangan DROP OUT
    $(`textarea#alasan-dropout-${index}`).keydown(() => {
      var onTyping = $(`textarea#alasan-dropout-${index}`).val();
      $(`#jumlah-keterangan-dropout`).remove();
      $(`.input-keterangan-drop-out-${index}`).removeClass("has-error");

      if (onTyping.length > maxKeteranganDropOut) {
        $(`textarea#alasan-dropout-${index}`)
          .parent(`.input-keterangan-drop-out-${index}`)
          .after(
            '<p class="text-error" id="jumlah-keterangan-dropout" style="color: red;">Jumlah keterangan drop out maksimal 100 karakter</p>'
          );
        $(`.input-keterangan-drop-out-${index}`).addClass("has-error");
      }
    });

    var statusKunjunganId = dataPerjadwalan[index]["status_kunjungan_id"];
    // Jika status kunjungan adalah DROP OUT
    if (statusKunjunganId == 1209) {
      // Show textarea ketika pilih DROP OUT
      $(`textarea#alasan-dropout-${index}`).removeClass("d-none");
      // Disabled semua kolom karena status program sudah DROP OUT
      $(`textarea#alasan-dropout-${index}`).prop("disabled", true);
    }

    $(`.status_kunjungan_${index}`).on(`change`, function () {
      var kunjunganValue = $(this).val();

      // DROP OUT
      if (kunjunganValue == 1209) {
        $(`.status_kunjungan_${nextIndex}`).val(1209).change();
        $(`.status_kunjungan_${nextIndex}`).addClass("drop-out-child");
        $(`.status_kunjungan_${nextIndex}`).prop("disabled", true);

        // Show textarea ketika pilih DROP OUT
        $(`textarea#alasan-dropout-${index}`).removeClass("d-none");
        $(`textarea#alasan-dropout-${nextIndex}`).prop("disabled", true);
        // Hapus value keterangan DROP OUT selain yang dipilih
        $(`textarea#alasan-dropout-${nextIndex}`).val("");
      } else {
        if (!$(this).hasClass("drop-out-child")) {
          var nextKunjunganValue = $(`.status_kunjungan_${nextIndex}`).val();
          if (
            nextKunjunganValue == 1209 &&
            $(`.status_kunjungan_${nextIndex}`).hasClass("drop-out-child")
          ) {
            $(`.status_kunjungan_${nextIndex}`).removeClass("drop-out-child");
            $(`.status_kunjungan_${nextIndex}`).prop("disabled", false);
            $(`.status_kunjungan_${nextIndex}`).val("").change();

            // Show textarea ketika pilih DROP OUT
            $(`textarea#alasan-dropout-${index}`).removeClass("d-none");
            $(`textarea#alasan-dropout-${nextIndex}`).prop("disabled", false);
          }
        }
        // Hidden textarea ketika pilih yang lain :D
        $(`textarea#alasan-dropout-${index}`).addClass("d-none");
        $(`textarea#alasan-dropout-${nextIndex}`).addClass("d-none");
        $(`textarea#alasan-dropout-${index}`).val("");
      }
    });
  }
  $(".pickadate-input").on("change", function () {
    var dataKe = $(this).attr("data-ke");
    var tanggalTerapi = $(this).val();
    if (tanggalTerapi) {
      setLoading();
      getData(tanggalTerapi)
        .then((value) => {
          var currentPickDate = $(`#schedule-${dataKe}`).pickadate("get");
          var inputMulai = $(`#jamMulai-${dataKe}`);
          var inputSelesai = $(`#jamSelesai-${dataKe}`);
          var newVal = JSON.parse(JSON.stringify(value));
          $(`#jamMulai-${dataKe}`).pickatime("set").set("disable", false);
          $(`#jamMulai-${dataKe}`).pickatime("set").set("enable", true);
          $(`#jamSelesai-${dataKe}`).pickatime("set").set("disable", false);
          $(`#jamSelesai-${dataKe}`).pickatime("set").set("enable", true);
          if (newVal.data && newVal.data.length > 0) {
            for (
              let scheduleKe = 0;
              scheduleKe < newVal.data.length;
              scheduleKe++
            ) {
              var valueDate = newVal.data[scheduleKe]["tgl_penjadwalan_awal"]
                .toString()
                .split(" ");
              var from = new Date(
                newVal.data[scheduleKe]["tgl_penjadwalan_awal"]
              );
              var to = new Date(
                newVal.data[scheduleKe]["tgl_penjadwalan_akhir"]
              );
              if (currentPickDate.toString() == valueDate[0].toString()) {
                inputMulai.pickatime("set").set("disable", [
                  {
                    from: [from.getHours(), from.getMinutes()],
                    to: [to.getHours(), to.getMinutes()],
                  },
                ]);
                inputSelesai.pickatime("set").set("disable", [
                  {
                    from: [from.getHours(), from.getMinutes()],
                    to: [to.getHours(), to.getMinutes()],
                  },
                ]);
              }
            }
          }
          setLoading(false);
        })
        .finally((value) => {
          for (let prevData = parseInt(dataKe) - 1; prevData >= 0; prevData--) {
            var prevElement = $(`#jamSelesai-${prevData}`);
            if (prevElement.length > 0) {
              var prevDate = $(`#schedule-${prevData}`).pickadate("get");
              var currentDate = $(`#schedule-${dataKe}`).pickadate("get");
              var prev = $(`#jamSelesai-${prevData}`).pickatime("get");
              if (prev && prevDate == currentDate) {
                var formatedTimes = prev.toString().split(":");
                $(`#jamMulai-${dataKe}`)
                  .pickatime("set")
                  .set("min", [formatedTimes[0], formatedTimes[1]]);
                var currentVal = $(`#jamMulai-${dataKe}`).pickatime("get");
                if (currentVal) {
                  if (prev > currentVal) {
                    $(`#jamMulai-${dataKe}`)
                      .pickatime("set")
                      .set("select", null);
                  }
                }
                break;
              } else {
                $(`#jamMulai-${dataKe}`).pickatime("set").set("min", false);
              }
            }
          }
        });
      $(`#jamMulai-${dataKe}`).pickatime("set").set("select", null);
      $(`#jamSelesai-${dataKe}`).pickatime("set").set("select", null);
      for (
        let nextData = parseInt(dataKe);
        nextData < maksFrekuensi;
        nextData++
      ) {
        var nextElement = $(`#schedule-${nextData + 1}`);
        if (nextElement.length > 0) {
          var prevVal = $(`#schedule-${dataKe}`).pickadate("get");
          var formatedDate = new Date(prevVal);
          nextElement
            .pickadate("set")
            .set("min", [
              formatedDate.getFullYear(),
              formatedDate.getMonth(),
              formatedDate.getDate(),
            ]);
          if (nextElement)
            var currentVal = $(`#jamSelesai-${dataKe}`).pickatime("get");
          var nextVal = $(`#jamMulai-${nextData + 1}`).pickatime("get");
          var nextDate = $(`#schedule-${nextData + 1}`).pickadate("get");
          var currentDate = $(`#schedule-${dataKe}`).pickadate("get");
          if (nextDate < currentDate) {
            nextElement.pickadate("set").set("select", null);
            $(`#jamMulai-${nextData + 1}`)
              .pickatime("set")
              .set("select", null);
            $(`#jamSelesai-${nextData + 1}`)
              .pickatime("set")
              .set("select", null);
          }
          if (currentVal && nextVal && nextDate == currentDate) {
            if (currentVal > nextVal) {
              $(`#jamMulai-${nextData + 1}`)
                .pickatime("set")
                .set("select", null);
            }
          }
        }
      }
    }
  });

  $(".jam_mulai").on("change", function () {
    var dataKe = $(this).attr("data-ke");
    var selectedTime = $(`#jamMulai-${dataKe}`).pickatime("get");
    var formatedTime = selectedTime.toString().split(":");
    $(`#jamSelesai-${dataKe}`)
      .pickatime("set")
      .set("min", [formatedTime[0], formatedTime[1]]);

    var currentJamMulai = $(`#jamMulai-${parseInt(dataKe)}`).pickatime("get");
    var currentJamSelesai = $(`#jamSelesai-${parseInt(dataKe)}`).pickatime(
      "get"
    );
    if (!currentJamMulai && currentJamSelesai) {
      $(`#jamSelesai-${parseInt(dataKe)}`)
        .pickatime("set")
        .set("select", null);
    }
    if (
      currentJamMulai &&
      currentJamSelesai &&
      currentJamSelesai < currentJamMulai
    ) {
      $(`#jamSelesai-${parseInt(dataKe)}`)
        .pickatime("set")
        .set("select", null);
      return docoNotification(
        "error",
        "Proses Gagal",
        "Silahkan Cek Inputan. Jam Selesai Harus Lebih Besar Dari Jam Mulai"
      );
    }
  });

  $(".jam_selesai").on("change", function () {
    var dataKe = $(this).attr("data-ke");
    for (let nextData = 1; nextData < maksFrekuensi; nextData++) {
      var nextElement = $(`#jamMulai-${parseInt(dataKe) + nextData}`);
      if (nextElement.length > 0) {
        var prevDate = $(`#schedule-${dataKe}`).pickadate("get");
        var nextDate = $(`#schedule-${parseInt(dataKe) + nextData}`).pickadate(
          "get"
        );
        if (prevDate == nextDate) {
          var prevVal = $(`#jamSelesai-${dataKe}`).pickatime("get");
          var formatedTimes = prevVal.toString().split(":");
          nextElement
            .pickatime("set")
            .set("min", [formatedTimes[0], formatedTimes[1]]);
          var currentVal = nextElement.pickatime("get");
          if (currentVal) {
            if (prevVal > currentVal) {
              nextElement.pickatime("set").set("select", null);
            }
          }
        }
      }
    }
    var currentJamMulai = $(`#jamMulai-${parseInt(dataKe)}`).pickatime("get");
    var currentJamSelesai = $(`#jamSelesai-${parseInt(dataKe)}`).pickatime(
      "get"
    );
    if (!currentJamMulai && currentJamSelesai) {
      $(`#jamSelesai-${parseInt(dataKe)}`)
        .pickatime("set")
        .set("select", null);
    }
    if (
      currentJamMulai &&
      currentJamSelesai &&
      currentJamSelesai < currentJamMulai
    ) {
      $(`#jamSelesai-${parseInt(dataKe)}`)
        .pickatime("set")
        .set("select", null);
      return docoNotification(
        "error",
        "Proses Gagal",
        "Silahkan Cek Inputan. Jam Selesai Harus Lebih Besar Dari Jam Mulai"
      );
    }
  });
});

$("#btn-update-jadwal").on("click", function () {
  var checkDisabled = $(this).hasClass("disabled");
  $("#jalor").remove();
  $(".form-group").removeClass("has-error");
  if (checkDisabled == false) {
    var jadwalArray = [];
    for (let i = 0; i < maksFrekuensi; i++) {
      var statusKunjungan = $(`.status_kunjungan_${i}`).val();
      var keteranganDropout = $(`textarea#alasan-dropout-${i}`).val();

      if (keteranganDropout.length > maxKeteranganDropOut) {
        docoNotification(
          "error",
          "Gagal",
          `Jumlah keterangan drop out maksimal ${maxKeteranganDropOut} karakter`
        );
        $(`.input-keterangan-drop-out-${i}`).addClass("has-error");
      }

      const currentHtml = $(`#schedule-${i}`);
      const currentAwal = $(`#jamMulai-${i}`);
      const currentAkhir = $(`#jamSelesai-${i}`);
      const currentValue = currentHtml.val();
      const currentAwalValue = currentAwal.val();
      const currentAkhirValue = currentAkhir.val();
      if (!currentValue) {
        currentHtml.parent(".form-control").addClass("has-error");
        currentHtml.after(
          '<span id="jalor" class="help-block error"><i class="fa fa-exclamation-circle"></i>Jadwal Terapi Tidak Boleh Kosong.</span>'
        );
        return docoNotification(
          "error",
          "Proses Gagal",
          "Silahkan Cek Inputan. Jadwal Tidak Boleh Kosong"
        );
      }
      if (!currentAwalValue) {
        currentAwal.parent(".form-control").addClass("has-error");
        currentAwal.after(
          '<span id="jalor" class="help-block error"><i class="fa fa-exclamation-circle"></i>Jadwal Terapi Tidak Boleh Kosong.</span>'
        );
        return docoNotification(
          "error",
          "Proses Gagal",
          "Silahkan Cek Inputan. Jadwal Tidak Boleh Kosong"
        );
      }
      if (!currentAkhirValue) {
        currentAkhir.parent(".form-control").addClass("has-error");
        currentAkhir.after(
          '<span id="jalor" class="help-block error"><i class="fa fa-exclamation-circle"></i>Jadwal Terapi Tidak Boleh Kosong.</span>'
        );
        return docoNotification(
          "error",
          "Proses Gagal",
          "Silahkan Cek Inputan. Jadwal Tidak Boleh Kosong"
        );
      }

      jadwalArray.push({
        tgl_penjadwalan_awal: currentValue + " " + currentAwalValue,
        tgl_penjadwalan_akhir: currentValue + " " + currentAkhirValue,
        jadwal_id: currentHtml.attr("data-jadwalId"),
        kunjungan_ke: currentHtml.attr("data-kunjunganKe"),
        pegawai_id: currentHtml.attr("data-pegawaiId"),
        name: `schedule_details-${i}`,
        status_kunjungan_id: statusKunjungan,
        keterangan_drop_out: keteranganDropout,
      });
    }
    $().docoForm("click", {
      url: "/fisioterapi/informasi-program-fisioterapi-ranap/save",
      data: { data: jadwalArray, id: $(`#programterapi_id`).val() },
      confirmMessage:
        "Apakah Anda yakin akan mengubah jadwal program fisioterapi ini?",
      success: () => {
        $("#modalProgramTerapi").modal("hide");
        $(".data-filter").click();
      },
      error: function (response) {
        response = response.responseJSON.response;
        docoNotification("error", response.title, response.message);
      },
    });
  }
});

async function initiateData(dataKe = null) {
  var tanggalTerapi = $(`#schedule-${dataKe}`).val();
  setLoading();
  getData(tanggalTerapi).then((value) => {
    var currentPickDate = $(`#schedule-${dataKe}`).pickadate("get");
    var inputMulai = $(`#jamMulai-${dataKe}`);
    var inputSelesai = $(`#jamSelesai-${dataKe}`);
    var newVal = JSON.parse(JSON.stringify(value));
    if (newVal.data && newVal.data.length > 0) {
      for (let scheduleKe = 0; scheduleKe < newVal.data.length; scheduleKe++) {
        var valueDate = newVal.data[scheduleKe]["tgl_penjadwalan_awal"]
          .toString()
          .split(" ");
        var from = new Date(newVal.data[scheduleKe]["tgl_penjadwalan_awal"]);
        var to = new Date(newVal.data[scheduleKe]["tgl_penjadwalan_akhir"]);
        if (currentPickDate.toString() == valueDate[0].toString()) {
          inputMulai.pickatime("set").set("disable", [
            {
              from: [from.getHours(), from.getMinutes()],
              to: [to.getHours(), to.getMinutes()],
            },
          ]);
          inputSelesai.pickatime("set").set("disable", [
            {
              from: [from.getHours(), from.getMinutes()],
              to: [to.getHours(), to.getMinutes()],
            },
          ]);
        }
      }
    }
    setLoading(false);
  });
}

function setLoading(loading = true) {
  if (loading == true) {
    $(`.lihat-jadwal-wrap`).hide();
    $("#tbl-schedule").hide();
    $("#loading-schedule").html(`<h3 style="text-align:center;">
          <i class="icon-spinner4 spinner position-center"></i>
          &nbsp;&nbsp;<b> Memuat... </b>
          </h3>`);
  } else {
    $(`.lihat-jadwal-wrap`).show();
    $("#tbl-schedule").show();
    $("#loading-schedule").html("");
  }
}

async function getData(tglTerapi = null) {
  var terapis = dataPerjadwalan[0].pegawai_id;
  var initialValue = null;
  var data =
    tglTerapi && terapis
      ? `?tgl_penjadwalan_awal=${tglTerapi}&pegawai_id=${terapis}`
      : "";
  var url = `/fisioterapi/informasi-jadwal-terapi/get-jam-terapi${data}`;
  await $.ajax({
    type: "GET",
    url: url,
    contentType: "application/json",
    success: function (res) {
      initialValue = res;
    },
  });
  return initialValue;
}

$(document).ready(function () {
  $("#detail").docoTabel({
    info: false,
    searching: false,
    scrollY: "250px",
    serverSide: false,
    scrollCollapse: true,
    paging: false,
    ordering: false,
  });
});
