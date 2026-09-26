'use strict'; 
(function (document, window, index) {
   var inputs = document.querySelectorAll('.inputfile')
   Array.prototype.forEach.call(inputs, function (input) {
      var label = input.nextElementSibling,
            labelVal = label.innerHTML

      input.addEventListener('change', function (e) {
         var fileName = ''
         if (this.files && this.files.length > 1)
            fileName = (
               this.getAttribute('data-multiple-caption') || ''
            ).replace('{count}', this.files.length)
         else fileName = e.target.value.split('\\').pop()

         if (fileName) label.querySelector('span').innerHTML = fileName
         else label.innerHTML = labelVal
      })

      input.addEventListener('focus', function () {
         input.classList.add('has-focus')
      })
      input.addEventListener('blur', function () {
         input.classList.remove('has-focus')
      })
   })
})(document, window, 0)

var filename = $('.lihat_file').attr('data-file')

if (filename) {
   $('#lihat').html(filename)
} else {
   $('#lihat').html(i18next.t('Lihat File'))
}

$(document).on('click', '.lihat_file', function () {
   $('.lihat_file').removeAttr('href')
   var idP = $(this).attr('data-id')
   var path = '/media/input-hasil-lab/' + idP + '/'
   var filename = $('.lihat_file').attr('data-file')
   if (filename) {
      $('.lihat_file').removeAttr('href')
      $('.lihat_file').attr('href', path + filename)
   } else {
      docoNotification(
         'error',
         i18next.t('Perhatian'),
         i18next.t('Belum ada berkas yang di upload!')
      )
   }
})

$("#file-1").change(function (event) {
   event.preventDefault();
   var button = this;
   var data = new FormData();
   var dataPost = $("#form-upload").serializeArray();
   var pasien_id = $('.pasien_id').val();
   var pendaftaran_id = $('.pendaftaran_id').val();
   var pasienadmisi_id = $('.pasienadmisi_id').val();
   var pasienpenunjang_id = $('.pasienpenunjang_id').val();
   var sample_id = $('.sample_id').val();
   var hasilpemeriksaanlab_id = $('.hasilpemeriksaanlab_id').val();
   var tanggal = $('#inputhasilform-tanggal').val();
   var penanggungjawab = $('.penanggungjawab').val();
   var getLink = $('.lihat_file').attr('href');
   data.append("UploadForm[upload_file]", $("#file-1")[0].files[0]);

   if ($("#file-1")[0].files[0].size > MAX_UPLOAD) {
      console.log($("#file-1")[0].files[0].size);
      var message = i18next.t("File harus maksimal 100 mb");
      $('.error-upload').text(message);
      $('.error-upload').css('color', 'red');
      return false;
   }
   data.append("UploadForm[penanggungjawab]", penanggungjawab);
   data.append("UploadForm[tanggal]", tanggal);
   data.append("UploadForm[pasien_id]", pasien_id);
   data.append("UploadForm[pendaftaran_id]", pendaftaran_id);
   data.append("UploadForm[pasienadmisi_id]", pasienadmisi_id);
   data.append("UploadForm[pasienmasukpenunjang_id]", pasienpenunjang_id);
   data.append("UploadForm[samplelab_id]", sample_id);
   data.append("UploadForm[hasilpemeriksaanlab_id]", hasilpemeriksaanlab_id);

   $.each(dataPost, function (key, value) {
      data.append(value.name, value.value);
   });

   $.ajax({
      type: "post",
      dataType: false,
      cache: false,
      contentType: false,
      processData: false,
      url: "/laboratorium/inf-pasien-rujukan-lab/upload-hasil",
      data: data,
      beforeSend: function () {
         $(button).html("<i class=\"fa fa-spin fa-spinner\"></i>");
         $(button).prop("disabled", true);
      },
      success: function (res) {
         docoNotification("success", i18next.t("Berhasil"), i18next.t("Data berhasil di upload"));
         setTimeout(() => {
            $("#file-1").removeAttr("disabled");
         }, 100);
         $('.error-upload').empty();
         var nama_file = res.response.file;
         $("#label-file").html(i18next.t("Pilih Berkas"));
         $("#lihat").html(nama_file);
         $(".lihat_file").attr('data-file', nama_file);
      },
      error: function (res) {
         setTimeout(() => {
            $("#file-1").removeAttr("disabled");
         }, 100);
         var message = i18next.t("File harus berbentuk jpg, png, pdf, doc dengan maksimal 100 mb");
         $('.error-upload').text(message);
         $('.error-upload').css('color', 'red');
      }
   });
})

$("#btn-upload").on('click', function (event) {
    event.preventDefault();
    var data = new FormData();
    var dataPost = $("#form-upload").serializeArray();
    var pasien_id = $('.pasien_id').val();
    var pendaftaran_id = $('.pendaftaran_id').val();
    var pasienadmisi_id = $('.pasienadmisi_id').val();
    var pasienpenunjang_id = $('.pasienpenunjang_id').val();
    var sample_id = $('.sample_id').val();
    var hasilpemeriksaanlab_id = $('.hasilpemeriksaanlab_id').val();
    var tanggal = $('#inputhasilform-tanggal').val();
    var penanggungjawab = $('.penanggungjawab').val();

    data.append("UploadForm[upload_file]", $("#file")[0].files[0]);
    data.append("UploadForm[penanggungjawab]", penanggungjawab);
    data.append("UploadForm[tanggal]", tanggal);
    data.append("UploadForm[pasien_id]", pasien_id);
    data.append("UploadForm[pendaftaran_id]", pendaftaran_id);
    data.append("UploadForm[pasienadmisi_id]", pasienadmisi_id);
    data.append("UploadForm[pasienmasukpenunjang_id]", pasienpenunjang_id);
    data.append("UploadForm[samplelab_id]", sample_id);
    data.append("UploadForm[hasilpemeriksaanlab_id]", hasilpemeriksaanlab_id);

    $.each(dataPost, function (key, value) {
        data.append(value.name, value.value);
    });
    $(this).docoForm("click", {
        url: '/laboratorium/inf-pasien-rujukan-lab/upload-hasil',
        dataType: false,
        cache: false,
        contentType: false,
        processData: false,
        data: data,
        method: 'post',
        isUpload: true,
        success: function (data) {
            
        }
    });
})

$('#btn-simpan-input-hasil').unbind();
$('#btn-simpan-input-hasil').bind('click', function (e) {
   e.preventDefault();
   var dataPost = $("#form-hasil").serializeArray();
   var pasien_id = $('.pasien_id').val();
   var pendaftaran_id = $('.pendaftaran_id').val();
   var pasienadmisi_id = $('.pasienadmisi_id').val();
   var pasienpenunjang_id = $('.pasienpenunjang_id').val();
   var expertise = $('.expertise').val();
   var is_kritis = $(".is_kritis").is(":checked");
   var tanggal = $('#inputhasilform-tanggal').val();
   if (tanggal == '') {
      docoNotification("error", i18next.t("Perhatian"), i18next.t("Tanggal Hasil Laboratorium tidak boleh kosong !"));
      return false;
   }
   var penanggungjawab = $('.penanggungjawab').val();
   if (penanggungjawab == '') {
      docoNotification("error", i18next.t("Perhatian"), i18next.t("Dokter Penanggung Jawab Laboratorium tidak boleh kosong !"));
      return false;
   }
   var sample_id = $('.sample_id').val();
   var hasilpemeriksaanlab_id = $('.hasilpemeriksaanlab_id').val();
   var error = false;
   $('.hasil').each(function (i) {
      var hasil = $(this).val();
      if (hasil) {
         if ($('.pegawai_id').eq(i).val() == '') {
            docoNotification("error", i18next.t("Perhatian"), i18next.t("Petugas tidak boleh kosong !"));
            error = true;
            return false;
         }
         dataPost.push({
            name: 'InputHasilForm[nilairujukan_id][]',
            value: $(this).attr('data-rujukan')
         });
         dataPost.push({
            name: 'InputHasilForm[hasil_pemeriksaan][]',
            value: $(this).val()
         });
         dataPost.push({
            name: 'InputHasilForm[daftartindakan_id][]',
            value: $(this).attr('data-id')
         });
         dataPost.push({
            name: 'InputHasilForm[tindakanpaket_id][]',
            value: $(this).attr('data-tindakan')
         });
         dataPost.push({
            name: 'InputHasilForm[petugaslab_id][]',
            value: $('.pegawai_id').eq(i).val()
         });
         dataPost.push({
            name: 'InputHasilForm[pemeriksaanlab_id][]',
            value: $(this).attr('data-pemeriksaan')
         });
         dataPost.push({
            name: 'InputHasilForm[nilai_rujukan][]',
            value: $(this).attr('data-nilai')
         });
         dataPost.push({
            name: 'InputHasilForm[satuan_hasil][]',
            value: $(this).attr('data-satuan')
         });
         dataPost.push({
            name: 'InputHasilForm[keterangan][]',
            value: $(this).attr('data-keterangan')
         });

         var checked = 0;
         if ($('#verif-' + $(this).attr('data-rujukan')).is(":checked")) {
            checked = 1;
         }
         dataPost.push({
            name: 'InputHasilForm[is_verifikasi_filtered][]',
            value: checked
         });
      }
   });

   if (error) {
      return false;
   }
   dataPost.push({
      name: 'InputHasilForm[penanggungjawab]',
      value: penanggungjawab
   });
   dataPost.push({
      name: 'InputHasilForm[tanggal]',
      value: tanggal
   });
   dataPost.push({
      name: 'InputHasilForm[pasien_id]',
      value: pasien_id
   });
   dataPost.push({
      name: 'InputHasilForm[pendaftaran_id]',
      value: pendaftaran_id
   });
   dataPost.push({
      name: 'InputHasilForm[pasienadmisi_id]',
      value: pasienadmisi_id
   });
   dataPost.push({
      name: 'InputHasilForm[pasienpenunjang_id]',
      value: pasienpenunjang_id
   });
   dataPost.push({
      name: 'InputHasilForm[expertise]',
      value: expertise
   });
   dataPost.push({
      name: 'InputHasilForm[is_kritis]',
      value: is_kritis
   });
   dataPost.push({
      name: 'InputHasilForm[samplelab_id]',
      value: sample_id
   });
   dataPost.push({
      name: 'InputHasilForm[hasilpemeriksaanlab_id]',
      value: hasilpemeriksaanlab_id
   });

   $(this).docoForm('click', {
      url: '/laboratorium/inf-pasien-rujukan-lab/save-input-hasil',
      data: dataPost,
      success: function (data) {
         // $('#modal_backdrop').modal('toggle');
         var id = data.response.id
         $('.hasilpemeriksaanlab_id').val(id)
         $("#btn-kembali-input-hasil").trigger('click');
      }
   });
});

$("#generatepetugas").change(function () {
    $(".pegawai_id").val($(this).val()).change();
})

$(document).ready(function () {
   var viewDomId = document.getElementsByClassName("tab-pane active");
   if (viewDomId != undefined) {
      var buttonBack = $("#btn-kembali-input-hasil").attr("data-target", `#${viewDomId[0]?.id}`)
   }
});

onHasilLegend = function (no, hasilinput, nilai_min, nilai_max) {
   Number.prototype.inRange = function (a, b) {
      return this >= a && this <= b;
   };
   var coloring = false;
   var setColor = true;
   if (nilai_min && nilai_max) {
      coloring = parseFloat(hasilinput).inRange(parseFloat(nilai_min), parseFloat(nilai_max))
   } 
   else {
   if (nilai_min && nilai_max != '') {
      coloring = hasilinput == nilai_min ? true : false;
   } else {
      setColor = false;
   }
   }

   var reg = /<(.|\n)*?>/g;
   if (reg.test(hasilinput) == true) {
      $("#hasil" + no).val('')
      $(".legend" + no).css({ 'background-color': '' })
   } 
   else {
      if (setColor) {
         if (hasilinput !== '') {
            if (coloring === true) {
               $(".legend" + no).css({ 'background-color': '#A1F582' })
            } else {
               $(".legend" + no).css({ 'background-color': '#FA8686' })
            }
         } else {
            $(".legend" + no).css({ 'background-color': '' });
         }
      }
   }

   if (hasilinput !== null && hasilinput !== '') {
      $("#verif-" + no).prop('checked', true);
   } else {
      $("#verif-" + no).prop('checked', false);
   }
}
