$("document").ready(function () {
  let result = [];
  let widget_pendaftaran_id = $('[name="widget_pendaftaran_id"]').val();
  if (_dokumen_eklaim != undefined || _dokumen_eklaim != null) {
    _dokumen_eklaim = JSON.parse(_dokumen_eklaim);
    result = Object.keys(_dokumen_eklaim).map((key) => [
      key,
      _dokumen_eklaim[key],
    ]);
  }

  var _formTimeout = 2000;
  $(`#${parent_id}`)
    .find(".dokumen-select2")
    .select2({
      placeholder: "Pilih",
      dropdownParent: $(`#${parent_id}`),
    })
    .on("select2:select", function (e) {
      let val = $(this).val();
      if (_dokumen_eklaim != undefined || _dokumen_eklaim != null) {
        let hasil = result.find((value, index) => value["0"] == val);
        if (hasil["1"] == true) {
          $(".is_eklaim").prop("checked", true);
        } else {
          $(".is_eklaim").prop("checked", false);
        }
      }
    });
  if (dataPasien == 0) {
    var tb_upload = $(`#${parent_id}`)
      .find("#table-dokumen")
      .docoTabel({
        filter: false,
        sorting: [[0, "desc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: {
          url:
            `/` +
            _modul +
            `/` +
            _controller +
            `/get-dokumen-list?id=${widget_pendaftaran_id}&isHide=${isHide}`,
        },
        columns: [
          {
            title: "No",
            data: "rowNum",
            searchable: false,
            orderable: false,
            width: "10%",
          },
          {
            data: "nama_dokumen",
            name: "nama_dokumen",
            title: "Nama Dokumen",
            width: "40%",
          },
          {
            data: "filename",
            title: "File",
            width: "30%",
          },
          {
            data: "aksi",
            title: "Aksi",
            width: "20%",
          },
        ],
      });
  } else {
    var tb_upload = $(`#${parent_id}`)
      .find("#table-dokumen")
      .docoTabel({
        filter: false,
        sorting: [[0, "desc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: {
          url:
            `/` +
            _modul +
            `/` +
            _controller +
            `/get-dokumen-list?pasien_id=${pasien_id}&isHide=${isHide}`,
        },
        columns: [
          {
            title: "No",
            data: "rowNum",
            searchable: false,
            orderable: false,
            // width: "10%"
          },
          {
            data: "doc_date",
            name: "doc_date",
            title: "Tanggal Kunjungan / No Pendaftaran",
            // width: "40%"
          },
          {
            data: "ruangan_nama",
            name: "ruangan_nama",
            title: "Nama Ruangan",
            // width: "40%"
          },
          {
            data: "nama_pegawai",
            name: "nama_pegawai",
            title: "Nama Dokter",
            // width: "40%"
          },
          {
            data: "nama_dokumen",
            name: "nama_dokumen",
            title: "Nama Dokumen",
            // width: "40%"
          },
          {
            data: "filename",
            name: "filename",
            title: "File",
            // width: "30%"
          },
          {
            data: "aksi",
            title: "Aksi",
            width: "15%",
          },
        ],
      });
  }

  $(`#${parent_id}`)
    .find("#btn-upload")
    .on("click", function () {
      _hasError = false;

      if (is_pasienid == 1) {
        if (
          $(`#${parent_id}`)
            .find("#dokumenpasienform-nama_dokumen_freetext")
            .val() == "" ||
          $(`#${parent_id}`)
            .find("#dokumenpasienform-nama_dokumen_freetext")
            .val() == null
        ) {
          $(`#${parent_id}`)
            .find("#dokumenpasienform-nama_dokumen_freetext")
            .closest(".col-md-4")
            .append(
              '<p class="error-tags" style="color: red; margin-top: 5px" class="error-tags">Nama Dokumen Tidak Boleh Kosong</p>'
            );
          _hasError = true;
        }
      } else {
        if (
          $(`#${parent_id}`).find("#dokumen_id").val() == "" ||
          $(`#${parent_id}`).find("#dokumen_id").val() == null
        ) {
          $(`#${parent_id}`)
            .find("#dokumen_id")
            .closest(".col-md-4")
            .append(
              '<p class="error-tags" style="color: red; margin-top: 5px" class="error-tags">Nama Dokumen Tidak Boleh Kosong</p>'
            );
          _hasError = true;
        }
      }

      if (
        $(`#${parent_id}`).find("#file").val() == "" ||
        $(`#${parent_id}`).find("#file").val() == null
      ) {
        $(`#${parent_id}`)
          .find("#file")
          .closest(".col-md-2")
          .append(
            '<p class="error-tags" style="color: red; margin-top: 15px" class="error-tags">File Tidak Boleh Kosong</p>'
          );
        _hasError = true;
      }

      if (_hasError) {
        setTimeout(() => {
          $(".error-tags").remove();
        }, _formTimeout);
        return false;
      }
      $(`#${parent_id}`).find("#upload-dokumen-pasien-form").submit();
    });
  $(`#${parent_id}`)
    .find("#upload-dokumen-pasien-form")
    .submit(function (event) {
      event.preventDefault();
      tanggalDok = $("#tanggal_dok").val();
      ruanganId = $("#ruangan_ids").val();
      pegawaiId = $("#pegawai_ids").val();

      $("#doc_date").val(tanggalDok);
      $("#ruangan_id").val(ruanganId);
      $("#dokter_id").val(pegawaiId);
      var formData = new FormData(this);
      // return false
      $(this).docoForm("submit", {
        dataType: false,
        cache: false,
        contentType: false,
        processData: false,
        data: formData,
        method: "post",
        isUpload: true,
        skipSuccessNotif: true,
        success: function (data) {
          var metadata = data.metadata;
          var response = data.response;
          if (metadata.status == 206) {
            docoNotification(
              "warning",
              i18next.t(response.message),
              i18next.t(response.text)
            );
            $(`#${parent_id}`).find("#upload-dokumen-pasien-form")[0].reset();
          } else if (metadata.status == 200) {
            docoNotification(
              "success",
              i18next.t(response.message),
              i18next.t(response.text)
            );
            $(`#${parent_id}`).find("#upload-dokumen-pasien-form")[0].reset();
            $(`#${parent_id}`).find("#view-upload-dokumen").trigger("click");
            $(`#${parent_id}`)
              .find(".dokumen-select2")
              .val("")
              .trigger("change");
            tb_upload.draw();
          } else {
            docoNotification(
              "error",
              i18next.t(response.message),
              i18next.t(response.text)
            );
          }
        },
      });
    });

  $(document).on("click", `#${parent_id} .delete`, function (event) {
    event.preventDefault();
    let button = this;
    let pkId = $(this).attr("data-id");
    let parent = $(this).attr("data-parent");
    if (pkId) {
      $(this).docoForm("delete", {
        url:
          "/" +
          _modul +
          "/" +
          _controller +
          "/delete-upload?pendaftaran_id=" +
          pkId +
          "&parent=" +
          parent,
        success: function (params) {
          $("#confirm-dialog").remove();
          // $(document).off("click", `#${parent_id} .delete`);
          $(button).parent().parent().remove();
          tb_upload.draw();
        },
      });
    } else {
      $(button).parent().parent().remove();
    }
  });

  $(document).on("click", `#${parent_id} .preview`, ({ currentTarget }) => {
    const dataBtn = $(currentTarget).data();
    if (
      typeof dataBtn.url != "undefined" &&
      dataBtn.url != null &&
      dataBtn != ""
    ) {
      // $("#modal_riwayat").css('z-index', '1040')
      $("#modal-preview").data("url", dataBtn.url);
      $("#modal-preview").modal({
        keyboard: false,
      });
    }
  });

  $("#modal-preview").on("shown.bs.modal", function () {
    $("#preview-content").attr("src", $("#modal-preview").data("url"));
  });

  $(`#${parent_id}`)
    .find("#file")
    .bind("change", function () {
      if (this.files[0].size / 1024 / 1024 > 20) {
        docoNotification(
          "error",
          i18next.t("Perhatian !"),
          i18next.t("File tidak boleh lebih dari 20MB !")
        );
        $(`#${parent_id}`).find("#upload-dokumen-pasien-form")[0].reset();
        $(`#${parent_id}`).find("#dokumen_id").val("").trigger("change");
      }
    });
});
