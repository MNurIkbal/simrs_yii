$(document).ready(function () {
  $("#btn-pembatalan").prop("disabled", false).trigger("change");
  $("#btn-resend").prop("disabled", false).trigger("change");
  $("#btn-pengesahan").prop("disabled", false).trigger("change");

  /**
   * Delete removed item.
   */
  deleteLocalStorage();
  
  let table = $("#table-tindakan").docoTabel({
    filter: false,
    sorting: [[2, "asc"]],
    processing: true,
    serverSide: true,
    scrollY: "300px",
    scrollCollapse: true,
    ajax: {
      url: "/penjamin-asuransi/informasi-dashboard-integrasi/get-grouping-data",
      data: function (d) {
        (d.pendaftaran_id = pendaftaranId),
          (d.penjamin_id = penjaminId),
          (d.no_klaim = noKlaim);
      },
    },
    columns: [
      {
        data: "rowNum",
        name: "rowNum",
        searchable: false,
        orderable: false, // 0
      },
      {
        title: "Detail",
        data: "item_button",
        name: "item_button",
        orderable: false, // 1
        searchable: false,
      },
      {
        title: "Item",
        data: "kelompoktindakan_nama",
        name: "kelompoktindakan_nama",
        orderable: false, // 1
        searchable: false,
      },
      {
        title: "No. Pendaftaran",
        data: "no_pendaftaran",
        name: "no_pendaftaran",
        orderable: true, // 2
        searchable: false,
      },
      {
        title: "Qty",
        data: "total_qty",
        name: "total_qty",
        orderable: true, // 3
        className: "text-right",
      },
      {
        title: "Price",
        data: "price_mapping",
        name: "price_mapping",
        orderable: false,
        searchable: false, // 4
        className: "text-right",
      },
      {
        title: "Subtotal",
        data: "tarif_mapping",
        name: "tarif_mapping",
        orderable: false,
        searchable: false, // 5
        className: "text-right",
      },
      {
        title: "Status",
        data: "status",
        name: "status",
        orderable: false, // 6
        searchable: false,
      },
    ],
    drawCallback: function (settings) {
      const dataRes = settings.jqXHR.responseJSON;
      var sumTotal =
        (sumPenjamin =
        sumDijamin =
        sumDibayarPasien =
        selisih =
        sumCob =
        sumInacbgs =
          0);
      var sumSudahKirim = 0;
      var isValid = true;
      var isCob = false;
      var isPengesahan = (isBatal = isResend = false);

      dataRes.data.map((data) => {
        sumTotal = docoHelper.convertToRupiah(data.sumTotal);
        sumPenjamin = docoHelper.convertToRupiah(data.sumPenjamin);
        sumDijamin = docoHelper.convertToRupiah(data.sumDijamin);
        sumDibayarPasien = docoHelper.convertToRupiah(data.sumDibayarPasien);
        sumSudahKirim = docoHelper.convertToRupiah(data.sumSudahDikirim);
        sumCob = docoHelper.convertToRupiah(data.sumCob);
        sumInacbgs = docoHelper.convertToRupiah(data.sumInacbgs);
        selisih = docoHelper.convertToRupiah(data.selisih);
        isValid = data.isValid;
        isPengesahan = data.is_pengesahan;
        isBatal = data.is_batal;
        isResend = data.is_resend;
        isCob = data.is_cob;
      });

      $("#sumTotal").val(sumTotal);
      $("#sumPenjamin").val(sumPenjamin);
      $("#sumDijamin").val(sumDijamin);
      $("#sumDibayarPasien").val(sumDibayarPasien);
      $("#sumCob").val(sumCob);
      $("#sumInacbgs").val(sumInacbgs);
      $("#selisih").val(selisih);
      $("#sumSudahKirim").val(sumSudahKirim);

      if (!isValid) {
        $("#btn-pengesahan").prop("disabled", true).trigger("change");
      } else {
        $("#btn-resend").prop("disabled", true).trigger("change");
      }

      if (isPengesahan) {
        $("#btn-pembatalan").prop("disabled", true).trigger("change");
        $("#btn-resend").prop("disabled", true).trigger("change");
        $("#btn-pengesahan").prop("disabled", true).trigger("change");
      }

      if (isBatal) {
        $("#btn-pembatalan").prop("disabled", true).trigger("change");
        $("#btn-pengesahan").prop("disabled", true).trigger("change");
        $("#btn-resend").prop("disabled", true).trigger("change");
      }
    },
    fnRowCallback: function(nRow, aData) {
      $('td', nRow).addClass(`kelompoktindakanid-${aData.kelompoktindakanobat_id}`);
    },
  });

  $("#btn-pembatalan").click(function (e) {
    e.preventDefault();

    $().docoForm("click", {
      skipSuccessNotif: true,
      confirmMessage: "Apakah anda yakin ingin membatalakan transaksi ini ?",
      data: {
        pendaftaran_id: pendaftaranId,
        penjamin_id: penjaminId,
        no_klaim: noKlaim,
        asuransi_id: asuransiId,
      },
      url: "/penjamin-asuransi/informasi-dashboard-integrasi/pembatalan",
      success: function (data) {
        if (data.status == 200) {
          docoNotification("success", "Berhasil", data.message);
        }

        if (data.status == 201) {
          docoNotification("error", "Gagal", data.message);
        }

        setTimeout(() => {
          window.location.reload();
        }, 1000);
      },
      error: (response) => {
        console.log(response);
      },
    });
  });

  $("#btn-resend").click(function (e) {
    e.preventDefault();
    let selectedDiagnosa = $("#diagnosa").val();
    let selectedText = $('#diagnosa').find(":selected").text();

    $().docoForm("click", {
      confirmMessage:
        "Apakah anda yakin ingin melakukan Resend data transaksi ini ?",
      data: {
        pendaftaran_id: pendaftaranId,
        kode_icd: selectedDiagnosa,
        kode_icd_text: selectedText,
      },
      skipSuccessNotif: true,
      skipErrorNotif: true,
      url: "/penjamin-asuransi/informasi-dashboard-integrasi/resend-transaksi",
      success: function (data) {
        if (data?.data?.data?.status === 201 && data?.meta?.code === 200) {

          let kodeItem = data?.data?.item_not_found;
          if (kodeItem != undefined) {
            localStorage.setItem(
              "item_not_found",
              JSON.stringify(kodeItem)
            )

            setTimeout(() => {
              notFoundItem()
            }, 100);
          }

          docoNotification(
            "warning",
            "Proses Gagal",
            data?.data?.data?.message
          );
          return;
        }

        if (data?.meta?.code === 200) {
          docoNotification(
            "success",
            "Proses Berhasil",
            "Proses Resend Berhasil !"
          );

          setTimeout(() => {
            window.location.reload();
          }, 1000);
          return;
        }
      },
      error: (response) => {
        if (response?.responseJSON?.meta?.code === 404) {
          docoNotification(
            "error",
            "Proses Gagal",
            response?.responseJSON?.meta?.message
          );
          return false;
        }
      },
    });
  });

  $("#btn-pengesahan").click(function (e) {
    e.preventDefault();
    let selectedText = $('#diagnosa').find(":selected").text();

    $().docoForm("click", {
      confirmMessage: "Apakah anda yakin ingin mengesahkan transaksi ini ?",
      data: {
        pendaftaran_id: pendaftaranId,
        no_klaim: noKlaim,
        penjamin_id: penjaminId,
        kode_icd: $("#diagnosa").val(),
        kode_icd_text: selectedText
      },
      skipErrorNotif: true,
      url: "/penjamin-asuransi/informasi-dashboard-integrasi/pengesahan",
      success: function (data) {
        console.log(data);
        setTimeout(() => {
          window.location.reload();
        }, 1000);
      },
      error: (response) => {
        if (response?.responseJSON?.meta?.code !== 200) {
          docoNotification(
            "error",
            "Proses Gagal",
            response?.responseJSON?.meta?.message
          );
          return false;
        }
      },
    });
  });

  $("#btn-back").click(function (e) {
    e.preventDefault();
    window.location.replace(
      baseUrl + "penjamin-asuransi/informasi-dashboard-integrasi"
    );
  });

  $("#diagnosa").select2({
    placeholder: "Pilih Diagnosa",
    minimumInputLength: 2,
    ajax: {
      url: "/penjamin-asuransi/informasi-dashboard-integrasi/get-diagnosa",
      dataType: "json",
      delay: 250,
      data: function (params) {
        return {
          q: params.term, // search term
          page: params.page || 1,
        };
      },
      processResults: function (data, params) {
        return {
          results: data.results.map(function (item) {
            return {
              id: item.id,
              text: item.text,
            };
          }),
        };
      },
      cache: true,
    },
  });

  setTimeout(() => {
    getSelectedData();
  }, 200);
});

function getSelectedData() {
  if (diagnosaKode && diagnosaText) {
    let newOption = new Option(diagnosaText, diagnosaKode, true, true);
    $("#diagnosa").append(newOption).trigger("change");

    $("#diagnosa").on("change", function () {
      let selectedValue = $(this).val();
      let selectedText = $(this).find(":selected").text();
      $("#selected-tag").html(
        `<strong>Selected Tag:</strong> ${selectedText} (ID: ${selectedValue})`
      );
    });
  }
}

function notFoundItem() {
  let itemNotFound = JSON.parse(localStorage.getItem("item_not_found"));
  itemNotFound.map((item) => {
    $(`.kelompoktindakanid-${item.kelompoktindakan_id}`).css("background", "#FFC0CB");
  })

  itemNotFound.map((item) => {
    $(`.kodetindakan-${item.daftartindakan_kode}`).css("background", "#FFC0CB");
  })
}


function deleteLocalStorage() {
  localStorage.removeItem("item_not_found");
}