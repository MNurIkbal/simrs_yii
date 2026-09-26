var table;
var _base_table_url = "/kasir/inf-pasien-pulang/get-data";

$(() => {
  moment.locale("en");
  table = $("#example").docoTabel({
    info: false,
    filter: true,
    columnDefs: [
      {
        orderable: false,
        className: "select-checkbox",
        targets: 0,
      },
    ],
    select: {
      style: "os",
      selector: "tr",
    },
    sorting: [[2, "desc"]],
    displayLength: 10,
    processing: true,
    serverSide: true,
    scrollX: true,
    // scrollY: true,
    cache: false,
    cacheFilter: true,
    ajax: {
      url: _base_table_url,
    },
    columns: [
      {
        data: null,
        searchable: false,
        orderable: false,
        defaultContent: "",
      },
      {
        data: null,
        searchable: false,
        orderable: false,
        render: (data, rowElement, rowData, rowAdditionalData) => {
          var tableInfo = table.page.info();
          return tableInfo.start + rowAdditionalData.row + 1;
        },
      },
      {
        title: "Tanggal Pulang",
        data: "tglpasienpulang",
        render: (data) => {
          return data == "" || data == null
            ? "-"
            : moment(data).format("DD MMM YYYY");
        },
      },
      {
        title: "Tanggal Registrasi",
        data: "tgl_pendaftaran",
        render: (data) => {
          return data == "" || data == null
            ? "-"
            : moment(data).format("DD MMM YYYY");
        },
      },
      {
        title: "Pasien",
        data: "nama_pasien",
        searchable: false,
        orderable: false,
        render: (data, rowElement, rowData) => {
          let _gender = rowData.jeniskelamin == 16 ? "P" : "L";
          return `
                   <p style="margin-bottom: 2px"> ${
                     rowData.nama_pasien != null ? rowData.nama_pasien : ""
                   } (${_gender != null ? _gender : "-"})</p>
                   <p style="margin-bottom: 2px"> ${
                     rowData.tanggal_lahir != null
                       ? moment(rowData.tanggal_lahir).format("DD MMM YYYY")
                       : "-"
                   } </p>
                   <p style="margin-bottom: 2px"> ${
                     rowData.no_pendaftaran != null
                       ? rowData.no_pendaftaran
                       : "-"
                   } / ${
            rowData.no_rekam_medik != null ? rowData.no_rekam_medik : "-"
          }</p>
               `;
        },
      },
      {
        title: "Instalasi  / Ruangan",
        data: "instalasi_nama",
        searchable: false,
        orderable: false,
        render: (data, rowElement, rowData) => {
          return `
                   <p style="margin-bottom: 2px"> ${
                     rowData.instalasi_nama != null
                       ? rowData.instalasi_nama
                       : "-"
                   }</p>
                   <p style="margin-bottom: 2px"> ${
                     rowData.ruangan_nama != null ? rowData.ruangan_nama : "-"
                   }</p>
               `;
        },
      },
      {
        title: "Kamar  / Tempat Tidur",
        data: "kamar_tempat_tidur",
        searchable: false,
        orderable: false,
        render: (data, rowElement, rowData) => {
          return `${
            rowData.kamarruangan_nokamar != null
              ? rowData.kamarruangan_nokamar
              : "-"
          } / ${rowData.no_tempattidur != null ? rowData.no_tempattidur : "-"}`;
        },
      },
      {
        title: "Kelas Ditempati  / Kelas Ditagihkan",
        data: "kelas_ditempati_ditagihkan",
        searchable: false,
        orderable: false,
        render: (data, rowElement, rowData) => {
          let _wording = `${
            rowData.kelaspelayanan_nama != null
              ? rowData.kelaspelayanan_nama
              : ""
          } / ${rowData.kelas_tagihan != null ? rowData.kelas_tagihan : "-"}`;
          if (rowData.pasienadmisi_id != null) {
            if (rowData.is_pasientitipan) {
              _wording = `${
                rowData.hak_kelas != null ? rowData.hak_kelas : ""
              } / ${
                rowData.kelas_tagihan != null ? rowData.kelas_tagihan : "-"
              }`;
            } else if (data != null) {
              _wording = `${
                rowData.kelaspelayanan_nama != null
                  ? rowData.kelaspelayanan_nama
                  : ""
              } / ${
                rowData.kelas_tagihan != null ? rowData.kelas_tagihan : "-"
              }`;
            }
          }
          return _wording;
        },
      },
      {
        title: "Status Kamar",
        data: "status_kelas",
        render: (data) => {
          return data == "" || data == null ? "-" : data;
        },
      },
      {
        title: "Cara Bayar  / Penjamin",
        data: "carabayar_nama",
        searchable: false,
        orderable: false,
        render: (data, rowElement, rowData) => {
          return `
                   <p style="margin-bottom: 2px"> ${
                     rowData.carabayar_nama != null
                       ? rowData.carabayar_nama
                       : "-"
                   }</p>
                   <p style="margin-bottom: 2px"> ${
                     rowData.penjamin_nama != null ? rowData.penjamin_nama : "-"
                   }</p>
               `;
        },
      },
      {
        title: "No SEP",
        data: "no_sep",
        render: (data) => {
          return data == "" || data == null ? "-" : data;
        },
      },
      {
        title: "Status Pulang",
        data: "status_pulang",
        searchable: false,
        render: (data) => {
          return data == "" || data == null ? "-" : data;
        },
      },
      {
        title: "Total Tagihan (Rp.)",
        data: "total_tagihan",
        searchable: false,
        orderable: false,
        className: "text-right",
        render: $.fn.dataTable.render.number(".", ",", 0, ""),
      },
    ],
    formFilters: [
      {
        fieldName: "tglpasienpulang",
        label: "Tanggal Pulang",
        type: {
          name: "rangeDate",
        },
      },
      {
        fieldName: "tgl_pendaftaran",
        label: "Tanggal Registrasi",
        type: {
          name: "rangeDate",
          payload: {
            allDate: true,
          },
        },
      },
      "no_pendaftaran",
      {
        fieldName: "nama_pasien",
        label: "Nama Pasien / No.Rekam Medik",
      },
      {
        fieldName: "instalasi_id",
        label: "Instalasi Akhir",
        type: {
          name: "dropdownScroll",
          url: "/kasir/inf-pasien-pulang/filters",
          additionalPayload: {
            type: "instalasi",
          },
        },
      },
      {
        fieldName: "ruangan_id",
        label: "Ruangan Akhir",
        type: {
          name: "dropdownScroll",
          url: "/kasir/inf-pasien-pulang/filters",
          additionalPayload: {
            type: "ruangan",
          },
        },
      },
      {
        fieldName: "carabayar_id",
        label: "Cara Bayar",
        type: {
          name: "dropdownScroll",
          url: "/kasir/inf-pasien-pulang/filters",
          additionalPayload: {
            type: "carabayar",
          },
        },
      },
      {
        fieldName: "penjamin_id",
        label: "Penjamin",
        type: {
          name: "dropdownScroll",
          url: "/kasir/inf-pasien-pulang/filters",
          additionalPayload: {
            type: "penjamin",
          },
        },
      },
      {
        fieldName: "no_sep",
        label: "No. SEP",
      },
    ],
    filterRendered: (wrapper) => {
      var defaultPlaceHolder = [
        {
          id: "",
          text: "- Semua -",
        },
      ];
      $(wrapper)
        .find('[name="instalasi_id"]')
        .bind("change", ({ currentTarget }) => {
          if ($(currentTarget).val() == "" || $(currentTarget).val() == null) {
            $(wrapper).find('[name="ruangan_id"]').html("");
            $(wrapper).find('[name="ruangan_id"]').select2({
              defaultPlaceHolder,
            });
          } else {
            $(wrapper)
              .find('[name="ruangan_id"]')
              .select2InfinityScroll({
                url: "/kasir/inf-pasien-pulang/filters",
                callbackData: (params) => {
                  return {
                    term: params.term,
                    page: params.page || 1,
                    limit: params.limit,
                    type: "ruangan",
                    additionalPayload: {
                      instalasi_id: $(currentTarget).val(),
                    },
                  };
                },
              });
          }
        });
      $(wrapper)
        .find('[name="carabayar_id"]')
        .bind("change", ({ currentTarget }) => {
          if ($(currentTarget).val() == "" || $(currentTarget).val() == null) {
            $(wrapper).find('[name="penjamin_id"]').html("");
            $(wrapper).find('[name="penjamin_id"]').select2({
              defaultPlaceHolder,
            });
          } else {
            $(wrapper)
              .find('[name="penjamin_id"]')
              .select2InfinityScroll({
                url: "/kasir/inf-pasien-pulang/filters",
                callbackData: (params) => {
                  return {
                    term: params.term,
                    page: params.page || 1,
                    limit: params.limit,
                    type: "penjamin",
                    additionalPayload: {
                      carabayar_id: $(currentTarget).val(),
                    },
                  };
                },
              });
          }
        });
    },
    rowCallback: (rowElement, data) => {
      if (data.carabayar_kode_warna != null) {
        $($(rowElement).find("td")[9]).css(
          "background-color",
          data.carabayar_kode_warna
        );
        $($(rowElement).find("td")[9]).css(
          "color",
          invertColor(data.carabayar_kode_warna, true)
        );
      }
      if (data.is_stopakomodasi) {
        $(rowElement).css("background-color", "#7efff5");
      }
      if (data.status_approve_id == 1323) {
        $($(rowElement).find("td")[4]).css("background-color", "#cce9b5");
      }
      if (data.status_approve_id == 1325) {
        $($(rowElement).find("td")[4]).css("background-color", "#a8c6ff");
      }
    },
  });
  $(document).on("click", ".btn-reset", function (e) {
    const tableId = "example";
    const element = $(`#filter-section__${tableId}`);
    const formWrapper = $(`#form-filter__${tableId}`);
    $(".legend-information").css("border", "1px solid #dddddd");
    element.find("input").val("");
    element.find("select").val(null).trigger("change");
    element
      .find("#tglpasienpulang-startDate")
      .val(moment().format("DD-MMM-YYYY"))
      .trigger("change");
    element
      .find("#tglpasienpulang-endDate")
      .val(moment().format("DD-MMM-YYYY"))
      .trigger("change");
    const tableElement = $(`#${tableId}`).DataTable();
    showLoader();
    tableElement.context[0].ajax.data.advancedFilter =
      serializeArrayToJson(formWrapper);
    tableElement.ajax.url("/kasir/inf-pasien-pulang/get-data").load();
    $("#plafon-bpjs").prop("disabled", true);
  });
  $(".dataTables_filter").hide();
  $("#plafon-bpjs").prop("disabled", true);
  $(document).on("click", "#example tbody tr", function () {
    var pendaftaran_id = null;
    var instalasi_id = null;
    var groupcarabayar_id = null;
    try {
      var _data = table.row(".selected").data();
      pendaftaran_id = _data.pendaftaran_id;
      instalasi_id = _data.instalasi_id;
      groupcarabayar_id = _data.groupcarabayar_id;
    } catch (error) {
      pendaftaran_id = null;
      instalasi_id = null;
      groupcarabayar_id = null;
    }

    if (typeof _data !== "undefined") {
      if (pendaftaran_id) {
        $("#cetak-rincian-tagihan").attr(
          "data-target",
          cetakRincian + pendaftaran_id
        );
        if (groupcarabayar_id == groupBpjs && instalasi_id != instalasiRanap) {
          $("#plafon-bpjs").prop("disabled", false);
          $("#plafon-bpjs").attr(
            "action",
            "/kasir/inf-pasien-pulang/modal-plafon?pendaftaran_id=" +
              pendaftaran_id
          );
        } else {
          $("#plafon-bpjs").prop("disabled", true);
        }
      }
    } else {
      $("#plafon-bpjs").prop("disabled", true);
    }
  });
  $("#btn-search__example").css("display", "none");
  $("#btn-reset__example").css("display", "none");
  function convertColor(hex, bw) {
    if (hex.indexOf("#") === 0) {
      hex = hex.slice(1);
    }
    // convert 3-digit hex to 6-digits.
    if (hex.length === 3) {
      hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
    }
    if (hex.length !== 6) {
      throw new Error("Invalid HEX color.");
    }
    var r = parseInt(hex.slice(0, 2), 16),
      g = parseInt(hex.slice(2, 4), 16),
      b = parseInt(hex.slice(4, 6), 16);
    if (bw) {
      return r * 0.299 + g * 0.587 + b * 0.114 > 186 ? "#000000" : "#FFFFFF";
    }
    // invert color components
    r = (255 - r).toString(16);
    g = (255 - g).toString(16);
    b = (255 - b).toString(16);
    // pad each with zeros and return
    return "#" + padZero(r) + padZero(g) + padZero(b);
  }

  $("#print-detail-edit-tagihan").prop("disabled", true);
  $("#print-summary-edit-tagihan").prop("disabled", true);

  // $("#print-summary-edit-tagihan").click(function(e){
  //    const cetakRincian = "/kasir/inf-pasien-belum-bayar/cetak-rincian?id=";
  //    e.preventDefault();
  //    var tableData = table.row(".selected").data();

  //    if(typeof tableData !== "undefined" && tableData.is_invoice)
  //    {
  //       var _id = tableData.pendaftaran_id;
  //       var _instalasi_id = tableData.instalasi_id;
  //       var url = cetakRincian+_id+"&instalasi_id="+_instalasi_id;
  //       window.open(url);
  //    }
  // });

  $(document).on("click", table, function () {
    var tableData = table.row(".selected").data();
    if (typeof tableData !== "undefined") {
      $("#print-detail-edit-tagihan").prop("disabled", false);
      $("#print-summary-edit-tagihan").prop("disabled", false);
      var _id = tableData.pendaftaran_id;
      var _instalasi_id = tableData.instalasi_id;
      var url_summary =
        "/kasir/inf-pasien-belum-bayar/show-popup-detail-designer?id=" +
        _id +
        "&instalasi_id=" +
        _instalasi_id +
        "&isdetail=false";
      var url_detail =
        "/kasir/inf-pasien-belum-bayar/show-popup-detail-designer?id=" +
        _id +
        "&instalasi_id=" +
        _instalasi_id +
        "&isdetail=true";
      $("#print-detail-edit-tagihan").attr("action", url_detail);
      $("#print-summary-edit-tagihan").attr("action", url_summary);
    } else {
      $("#print-detail-edit-tagihan").prop("disabled", true);
      $("#print-summary-edit-tagihan").prop("disabled", true);
      $(".close-bill").prop("disabled", true);
      $(".close-bill").html('<b><i class="fa fa-key"></i></b>Close Bill');
    }
  });

  /*added filter legend*/
  var _legend_info = ".legend-information";

  $(_legend_info).css("cursor", "pointer");
  $(_legend_info).removeClass("active");
  $(_legend_info).on("click", function () {
    showLoader();
    _dt_group = $(this).data("group");
    _dt_type = $(this).data("type");
    _adv_filter = [];

    // filter keterangan
    if (_dt_group == "keterangan") {
      if ($(this).hasClass("active") === true) {
        $(this).removeClass("active");
        $(this).css("border", "1px solid #dddddd");
      } else {
        $(".legend-information[data-group='keterangan'].active")
          .removeClass("active")
          .css("border", "1px solid #dddddd");
        $(this).addClass("active");
        $(this).css("border", "2px solid #2ca38b");

        _dt_val = $(this).data("val");
        _filter_keterangan = `advancedFilter%5B${_dt_type}%5D=${_dt_val}`;
        _adv_filter.push(_filter_keterangan);
      }

      // get filter keterangan cara bayar
      _dt_carabayar_id = $(
        ".legend-information[data-group='keterangan-cara-bayar'].active"
      ).attr("data-type");
      if (_dt_carabayar_id) {
        _filter_keterangan_cara_bayar = `advancedFilter%5Bcarabayar_id%5D=${_dt_carabayar_id}`;
        _adv_filter.push(_filter_keterangan_cara_bayar);
      }
    }

    // filter keterangan cara bayar
    else if (_dt_group == "keterangan-cara-bayar") {
      if ($(this).hasClass("active") === true) {
        $(this).removeClass("active");
        $(this).css("border", "1px solid #dddddd");
        $("#example-carabayar_id--form").prop("disabled", false);
        $("#example-penjamin_id--form").prop("disabled", true);
        $("#example-carabayar_id--form").val("").trigger("change");
        $("#example-penjamin_id--form").val("").trigger("change");
      } else {
        $(".legend-information[data-group='keterangan-cara-bayar'].active")
          .removeClass("active")
          .css("border", "1px solid #dddddd");
        $(this).addClass("active");
        $(this).css("border", "2px solid #2ca38b");

        $("#example-carabayar_id--form").prop("disabled", true);
        $("#example-penjamin_id--form").prop("disabled", false);
        $("#example-carabayar_id--form").val(_dt_type).trigger("change");

        var _text = $(this).find("div.legend-information__text").text();
        var option = new Option(_text, _dt_type, true, true);
        $("#example-carabayar_id--form").append(option).trigger("change");

        _filter_keterangan_cara_bayar = `advancedFilter%5Bcarabayar_id%5D=${_dt_type}`;
        _adv_filter.push(_filter_keterangan_cara_bayar);
      }

      // get filter keterangan
      _keterangan_active = $(
        ".legend-information[data-group='keterangan'].active"
      );
      if (_keterangan_active) {
        _filter_keterangan = `advancedFilter%5B${_keterangan_active.attr(
          "data-type"
        )}%5D=${_keterangan_active.attr("data-val")}`;
        _adv_filter.push(_filter_keterangan);
      }
    }

    // reload datatable
    _table_ajax_url = _base_table_url + "?" + _adv_filter.join("&");
    table.ajax.url(_table_ajax_url).load();
  });
});
