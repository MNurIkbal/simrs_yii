$(document).ready(() => {
   if ($('.btn-save-post').length) {
      $('.btn-save-post').remove()
   }
   var _obj = $('#tab-operasi-step-4').find('.content');
   var _source = _obj.attr('data-noreg')
   var isLaporanEndoskopi = $('#tab-operasi-step-4').attr('style') == "" ? true : false;
   $('#laporanendoskopiform-pendaftaran_id').val(_source);
   $('.submit-laporan-endoskopi').remove();
   $('.cetak-laporan-endoskopi').remove();
   $('.submit-laporan').remove();
   $('.cetak-laporan').remove();
   $('.stepy-navigator').append('<button type="button" class="btn btn-xs btn-labeled btn-info submit-laporan-endoskopi"><b><i class="fa fa-floppy-o"></i></b> Simpan</button>')
   $('.stepy-navigator').append('<button type="button" class="btn btn-xs btn-labeled btn-info cetak-laporan-endoskopi" data-target="/bedah/informasi-pasien-operasi/export-pdf?id=' + penunjangId + '"><b><i class="fa fa-print"></i></b> Cetak</button>')
   $('.submit-laporan-endoskopi').prop('disabled', false);
   $('.stepy-navigator .button-back').removeClass('hidden');

   $('.submit-laporan-endoskopi').on('click', () => {
      let data = new FormData();
      let dataPost = $("#form-laporan-endoskopi").serializeArray();
      let validate = true;
      if ($(".inputfile").length > 1) {
         let n = $(".inputfile").length;
         for (let i = 1; i < n + 1; i++) {
            if ($("#file-" + i)[0].files[0] == undefined) {
               data.append("LaporanEndoskopiForm[additional_photo][]", []);
            } else {
               data.append("LaporanEndoskopiForm[additional_photo][]", $("#file-" + i)[0].files[0]);
            }
         }
      } else if ($(".inputfile").length == 1) {
         if ($("#file-1")[0].files[0] == undefined) {
            data.append("LaporanEndoskopiForm[additional_photo]", []);
         } else {
            if ($("#file-1")[0].files[0].size > max_upload) {
               let message = i18next.t("File yang diunggah maksimal 10 MB");
               docoNotification('error', "Upload Gagal", message);
               $('#error-file-1').text(message);
               $('#error-file-1').css('color', 'red');
               return false;
            }
            data.append("LaporanEndoskopiForm[additional_photo]", $("#file-1")[0].files[0]);
         }
      } else {
         data.append("LaporanEndoskopiForm[additional_photo]", []);
      }

      $.each(dataPost, function (key, value) {
         data.append(value.name, value.value);
      });

      $().docoForm("click", {
         url: $('#form-laporan-endoskopi').attr('action'),
         dataType: false,  // what to expect back from the PHP script, if anything
         cache: false,
         contentType: false,
         processData: false,
         data: data,
         method: 'post',
         isUpload: true,
         success: function (data) {
            setTimeout(() => {
               $('#btn-kembali').trigger("click");
               loadContent(5);
            }, 1000);
         }
      });
   });

   $('#add-upload').on('click', () => {
      let increment = parseInt($(".inputfile").length) + 1;
      $('#formUpload').append(`
            <div class="row" id="row-${increment}">
                <div class="col-md-3">
                </div>
                <div class="col-md-6">
                    <button type="button" class="btn btn-sm btn-danger delete" data-idrow="${increment}"><i class="fa fa-trash"></i></button>
                    <label for="file-${increment}" class="customform">
                        <i class="fa fa-upload"></i>
                        <span id="label-file-${increment}">Pilih Berkas</span>
                    </label>
                    <input type="file" name="LaporanEndoskopiForm[additional_photo][]" id="file-${increment}" class="form-control inputfile inputfile-1" data-multiple-caption="{count} files selected" >
                    <div class="error-upload" id="error-file-${increment}"></div>

                  </div>
                <div class="col-md-3">
                </div>
            </div>
      `);
      $("#file-" + increment).css('display', 'none');
   })
   $(".inputfile").css('display', 'none');

   $(document).on('click', '.delete', function () {
      let _id = $(this).attr("data-idrow");
      $('#row-' + _id).css('display', 'none');
      $('#arr-' + _id).attr('name', 'LaporanEndoskopiForm[delete_photo_arr][]');
      $('#file-' + _id).val('');
      $('.submit-laporan-endoskopi').prop('disabled', false);
   });

   $(document).on('change', '.inputfile', function () {
      let _idFile = $(this).attr("id");
      $('#error-' + _idFile).text('');
      if ($("#" + _idFile)[0].files[0].size > max_upload) {
         let message = i18next.t("File yang diunggah maksimal 10 MB");
         docoNotification('error', "Upload Gagal", message);
         $('#error-' + _idFile).text(message);
         $('#error-' + _idFile).css('color', 'red');
         $("#" + _idFile)[0].files[0] = [];
         $("#label-" + _idFile).html(`<span> Pilih Berkas </span>`);
         $('.submit-laporan-endoskopi').prop('disabled', true);
         return false;
      } else if (/\.(jpe?g|png|jpg)$/i.test($('#' + _idFile)[0].files[0].name) == false) {
         let message = i18next.t("Jenis file tidak diizinkan.!");
         docoNotification('error', "Upload Gagal", message);
         $('#error-' + _idFile).text(message);
         $('#error-' + _idFile).css('color', 'red');
         $("#" + _idFile)[0].files[0] = [];
         $("#label-" + _idFile).html(`<span> Pilih Berkas </span>`);
         $('.submit-laporan-endoskopi').prop('disabled', true);
      } else {
         $('.submit-laporan-endoskopi').prop('disabled', false);
         $("#label-" + _idFile).html(`<span> ` + $("#" + _idFile)[0].files[0].name + ` </span>`);
      }
   });

   $('.cetak-laporan-endoskopi').unbind();
	$('.cetak-laporan-endoskopi').bind('click', function () {
		window.open($(this).attr('data-target'), '_blank')
	})
   // $("#file-1").change(function(event) {
   //    event.preventDefault();
   //    let button = this;
   //    let data = new FormData();
   //    let dataPost = $("#form-laporan-endoskopi").serializeArray();
   //    let getLink = $('.lihat_file').attr('href');

   //    data.append("LaporanEndoskopiForm[additional_photo]", $("#file-1")[0].files[0]);
   //    // if ($("#file-1")[0].files[0].size > 10) {
   //    //    console.log($("#file-1")[0].files[0].size);
   //    //    let message = i18next.t("File harus maksimal 100 mb");
   //    //    $('.error-upload').text(message);
   //    //    $('.error-upload').css('color', 'red');
   //    //    return false;
   //    // }

   //    $.each(dataPost, function (key, value) {
   //       data.append(value.name, value.value);
   //   });
   //   console.log(dataPost);
   //    $.ajax({
   //       type: "post",
   //       dataType: false, // what to expect back from the PHP script, if anything
   //       cache: false,
   //       contentType: false,
   //       processData: false,
   //       url: $('#form-laporan-endoskopi').attr('action'),
   //       data: data,
   //       beforeSend: function () {
   //          $(button).html("<i class=\"fa fa-spin fa-spinner\"></i>");
   //          $(button).prop("disabled", true);
   //       },
   //       success: function (res) {
   //          docoNotification("success", i18next.t("Berhasil"), i18next.t("Data berhasil di upload"));
   //          setTimeout(() => {
   //                $("#file-1").removeAttr("disabled");
   //                // location.reload();
   //          }, 100);
   //          // $('.error-upload').empty();
   //          // let nama_file = res.response.file;
   //          // $("#label-file").html(i18next.t("Pilih Berkas"));
   //          // $("#lihat").html(nama_file);
   //          // $(".lihat_file").attr('data-file', nama_file);
   //       },
   //       error: function(res) {
   //          setTimeout(() => {
   //                $("#file-1").removeAttr("disabled");
   //                // location.reload();
   //          }, 100);
   //          let message = i18next.t("File harus berbentuk jpg, png, pdf, doc dengan maksimal 100 mb");
   //          $('.error-upload').text(message);
   //          $('.error-upload').css('color', 'red');
   //       }
   //    });
   // })



});