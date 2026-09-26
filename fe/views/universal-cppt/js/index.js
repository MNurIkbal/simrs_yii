// Tabel
var tabel;
var tableCppt
var local = sessionStorage.getItem(`suggestsoapranap#${pendaftaran_id}#${pegawai_id}#${id_ruangan}`);
var suggest_storage = JSON.parse(local);
var suggest_cppt_id = local !== null ? suggest_storage.find(element => element.name == 'cpptId') : '';

// Initiate page
$(document).ready(function () {
  if (isMustCheckProgramFisio) {
    $("#btn-modal-fisioterapi-available-program").trigger("click");
  }
  $("#btn-add-terapi").data('cpptId', last_cppt)
  let _listpemeriksaanpenunjang = [] //variable ini digunakan di popup order penunjang
  let listOrderButton = pelayananConfigButton
  let _buttonHtml = '';
  $.each(listOrderButton, (k, v) => {
    if (v.title.toLowerCase() == 'laboratorium' || v.title.toLowerCase() == 'radiologi' || v.title.toLowerCase() == 'penjadwalan' || v.title.toLowerCase() == 'fisioterapi' || v.name.toLowerCase() == 'fisioterapi' || v.name.toLowerCase() == 'darah') {
    } else {
      _buttonHtml += `<button type='button' ${typeof v.disabled != 'undefined' && v.disabled ? 'disabled' : ''} style='margin-right: 5px' data-href="${v.url}" class='btn btn-labeled btn-info btn-xs btn-order-cppt' data-width='${typeof v.width != 'undefined' ? v.width : '90%'}' data-wrapper='${typeof v.wrapper != 'undefined' ? v.wrapper : '#content-cppt'}' data-type='${v.name}'><b><i class='fa ${v.icon}'></i></b> ${v.title}</button>`
    }
  })
  $('#button-wrapper').html(_buttonHtml)

  let _buttonLaboratoriumHtml = '';
  $.each(listOrderButton, (k, v) => {
    if (v.title.toLowerCase() == 'laboratorium') {
      _buttonLaboratoriumHtml += `<button type='button' ${typeof v.disabled != 'undefined' && v.disabled ? 'disabled' : ''} style='margin-right: -7px;width:50px;' data-href="${v.url}" class='btn btn-xs btn-only btn-primary-color btn-order-cppt' data-width='${typeof v.width != 'undefined' ? v.width : '90%'}' data-wrapper='${typeof v.wrapper != 'undefined' ? v.wrapper : '#content-cppt'}' data-type='${v.name}'><b><i class='fa ${v.icon}'></i></b></button>`
    } else {
    }
  })
  $('#button-laboratorium').html(_buttonLaboratoriumHtml)

  let _buttonRadiologiHtml = '';
  $.each(listOrderButton, (k, v) => {
    if (v.title.toLowerCase() == 'radiologi') {
      _buttonRadiologiHtml += `<button type='button' ${typeof v.disabled != 'undefined' && v.disabled ? 'disabled' : ''} style='margin-right: -7px;width:50px;' data-href="${v.url}" class='btn btn-xs btn-only btn-primary-color btn-order-cppt' data-width='${typeof v.width != 'undefined' ? v.width : '90%'}' data-wrapper='${typeof v.wrapper != 'undefined' ? v.wrapper : '#content-cppt'}' data-type='${v.name}'><b><i class='fa ${v.icon}'></i></b></button>`
    } else {
    }
  })
  $('#button-radiologi').html(_buttonRadiologiHtml)

  let _buttonPenjadwalanHtml = '';
  $.each(listOrderButton, (k, v) => {
    if (v.title.toLowerCase() == 'penjadwalan') {
      _buttonPenjadwalanHtml += `<button type='button' ${typeof v.disabled != 'undefined' && v.disabled ? 'disabled' : ''} style='margin-left:10px; margin-right: -7px;width:50px;' data-href="${v.url}" class='btn btn-xs btn-only btn-primary-color btn-order-cppt' data-width='${typeof v.width != 'undefined' ? v.width : '90%'}' data-wrapper='${typeof v.wrapper != 'undefined' ? v.wrapper : '#content-cppt'}' data-type='${v.name}'><b><i class='fa ${v.icon}'></i></b></button>`
    } else {
    }
  })
  $('#button-penjadwalan').html(_buttonPenjadwalanHtml)

  let _buttonFisioterapiHtml = '';
  $.each(listOrderButton, (k, v) => {
    if (v.title.toLowerCase() == 'fisioterapi') {
      _buttonFisioterapiHtml += `<button type='button' ${typeof v.disabled != 'undefined' && v.disabled ? 'disabled' : ''} style='margin-right: -7px;width:50px;' data-href="${v.url}" class='btn btn-xs btn-only btn-primary-color btn-order-cppt' data-width='${typeof v.width != 'undefined' ? v.width : '90%'}' data-wrapper='${typeof v.wrapper != 'undefined' ? v.wrapper : '#content-cppt'}' data-type='${v.name}'><b><i class='fa ${v.icon}'></i></b></button>`
    } else {
    }
  })
  $('#button-fisioterapi').html(_buttonFisioterapiHtml)
  $('.btn-order-cppt').unbind();
  $('.btn-order-cppt').bind('click', ({ currentTarget }) => {
    const { type, href, wrapper, width } = $(currentTarget).data()
    let wrapperParents = wrapper.split(' ')[0]
    if (wrapperParents != '#content-cppt') {
      $(wrapperParents).find('.modal-dialog').css('width', width)
      showLoader('Memuat Halaman...')
    }
    $(wrapper).docoLoad({
      url: href.replace('#cppt_id#', last_cppt).replace('#pendaftaran_id#', pendaftaran_id).replace('#pasien_id#', pasien_id),
      dataType: 'html',
      success: function (data) {
        hideLoader()
        $(wrapper).parents('.modal').modal('show')
      },
      error: function () {
        hideLoader()
      }
    })
  })
  // $("#btn-add-terapi").data('cpptId', last_cppt)
  // Find id an remove class
  $("#btn-soap").removeClass("btn-toolbar");
  $("#btn-verbal-order").removeClass("btn-toolbar");
  $("#btn-add-terapi").removeClass("btn-toolbar");
  $("#btn-add-instruksi-dpjp").removeClass("btn-toolbar");

  // Generate Table
  tabel = $("#tb-cppt").docoTabel({
    filter: false,
    displayLength: 10,
    processing: true,
    serverSide: true,
    paging: false,
    info: false,
    // aaSorting: [],
    sorting: [[1, "asc"]],
    // bDestroy: true,
    ajax: {
      url: baseUrl + _universalCpptUrl +'get-data-cppt?no_masukpenunjang='+ _no_masukpenunjang,
      data: function (data) {
        data.id = pendaftaran_id;
        data.ruangan_id = $('#filter-cppt-ruangan_id').val();
        data.pegawai_id = $('#filter-cppt-pegawai_id').val();
        data.kelompokpegawai_id = $('#filter-cppt-kelompokpegawai_id').val();
        var tgl_cppt = null;
        if ($('.endDate1').val() != '') {
          tgl_cppt = $('.startDate1').val() + '-' + $('.endDate1').val();
        }
        data.tanggal_cppt = tgl_cppt
      }
    },
    columns: [
      {
        title: "No",
        data: "no",
        searchable: false,
        orderable: false
      },
      { title: ruang, name: "tgl_soaprj", data: "ruangan", searchable: false, orderable: true },
      // { title: tanggalJam, data: "tgl_cppt", searchable: false, orderable: false },
      // { title: profesi, data: "profesi", searchable: false, orderable: false },
      { title: pelaksanaan, data: "penatalaksanaan", searchable: false, orderable: false },
      { title: instruksi, data: "instruksi_soap", searchable: false, orderable: false },
      // {
      //     title: dpjp,
      //     data: "instruksi_dpjp",
      //     searchable: false,
      //     orderable: false
      // },
      { title: verifikasi, data: "verifikasi", searchable: false, orderable: false },
      // {
      //     title: "Aksi",
      //     data: "aksi",
      //     searchable: false,
      //     orderable: false
      // },
      // { title: verifikasi, data: "is_verifikasi", visible: false, searchable: false, orderable: false },
    ],
    language: {
      emptyTable: emptyTable,
      info: info,
      infoEmpty: infoEmpty,
      infoFiltered: infoFiltered,
      lengthMenu: lengthMenu,
      loadingRecords: loadingRecords,
      processing: processing,
      search: search,
      zeroRecords: zeroRecords,
      aria: {
        sortAscending: sortAscending,
        sortDescending: sortDescending
      }
    },
    // fnRowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
    //     // Set verifikasi
    //     if (aData.is_verifikasi != true) {
    //         $("td", nRow).css("background-color", "#fdfd96");
    //     }
    // },
    createdRow: (rowElement, data) => {

      var waktu_pengisian = Date.parse(data.another_format_tgl_cppt)
      var waktu_sekarang = Date.now()
      // var is_dpjp = data.user_identity['gelarbelakang'] != null ? 1 : 0;
      var is_dpjp = data.user_identity.gelarbelakang != null ? 1 : 0;
      var expired = waktu_sekarang - waktu_pengisian
      var is_cppt = data.is_cppt;

      if (!is_cppt) {
        var waktu_pengisian_pagt = Date.parse(data.another_format_tgl_pagt)
        var expired_pagt = waktu_sekarang - waktu_pengisian_pagt
      }
      if (!data.is_verifikasi) {
        $(rowElement).addClass('not-verified-cppt')
        if (is_dpjp == 1) {
          var _expired = (is_cppt) ? expired : expired_pagt;
          if (_expired > 86400000) {
            $(rowElement).removeClass('not-verified-cppt')
            // $(rowElement).addClass('expired-cppt')
            $(rowElement).css('background-color', '#990011')
            $(rowElement).css('color', '#fff')
          }
        }
      }


      $(rowElement).find('.btn-edit-soap').bind('click', () => {
        $("#tb-cppt .edited-row").removeClass('edited-row')
        $("#tb-cppt .btn-edit-soap").removeClass('hidden')
        $("#tb-cppt .btn-cancel-edit-soap").addClass('hidden')
        // if(data.primary != activeEditedCppt){
        if (data.is_icd_x) {
          $('#cpptform-a_diag_utama_text').parent().hide()
          $('#cpptform-a_diag_utama').parent().show()
          $('#cpptform-is_icd_x').prop('checked', true).uniform('refresh');
        } else {
          $('#cpptform-a_diag_utama_text').parent().show()
          $('#cpptform-a_diag_utama').parent().hide()
          $('#cpptform-is_icd_x').prop('checked', false).uniform('refresh');
        }

        $('#cpptform-a_diag_utama').html('')
        $('#cpptform-a_diag_penyerta').html('')
        if (data.a_diag_utama != null) {
          var decodeDiagnose = null
          try {
            decodeDiagnose = JSON.parse(data.a_diag_utama)
          } catch (error) {
            decodeDiagnose = null
          }
          if (decodeDiagnose != null) {
            var newOption = new Option(decodeDiagnose.text, `${decodeDiagnose.id}_${decodeDiagnose.text}`, false, false);
            $('#cpptform-a_diag_utama').append(newOption).trigger('change').val(`${decodeDiagnose.id}_${decodeDiagnose.text}`).trigger('change')
            $('#cpptform-a_diag_utama_text').val(decodeDiagnose.text)
          }
        }
        if (data.a_diag_penyerta != null) {
          var decodeDiagnose = []
          try {
            decodeDiagnose = JSON.parse(data.a_diag_penyerta)
          } catch (error) {
          }
          var selectedOption = []
          if (Array.isArray(decodeDiagnose)) {
            decodeDiagnose.map((diagnose) => {
              if (decodeDiagnose != null) {
                var newOption = new Option(diagnose.text, `${diagnose.id}_${diagnose.text}`, false, false);
                $('#cpptform-a_diag_penyerta').append(newOption)
                selectedOption.push(`${diagnose.id}_${diagnose.text}`)
              }
            })
            $('#cpptform-a_diag_penyerta').val(selectedOption).trigger('change')
          }
        }
        $("#cpptform-subject").val(
          data.subject != null
            ? data.subject
            : null
        );
        $("#cpptform-object").val(
          data.object != null
            ? data.object
            : null
        );
        $("#cpptform-planning").val(
          data.planning != null
            ? data.planning
            : null
        );
        $("#cpptform-catatan_dokter").val(data.catatan_dokter != null ? data.catatan_dokter : null);
        $("#cpptform-catatan_perawat").val(data.catatan_perawat != null ? data.catatan_perawat : null);
        $("#cpptform-instruksi").val(
          data.instruksi != null
            ? data.instruksi
            : null);
        $("#cpptform-tgl_cppt").val(
          moment(data.origin_tgl_cppt).format("DD/MM/YYYY HH:mm:ss")
        );
        // }
        $("#form-soap").prop("action", `/ranap/pemeriksaan-rawat-inap/edit-soap?id=${pendaftaran_id}&cpptId=${data.primary}`);
        activeEditedCppt = data.primary;
        $("#header-form-soap").text("Edit SOAP");
        $("#cpptDate-form-wrapper").removeClass("hidden");
        $(rowElement).toggleClass("edited-row");
        $(rowElement).find(".btn-edit-soap").toggleClass("hidden");
        $(rowElement).find(".btn-cancel-edit-soap").toggleClass("hidden");
        clearSoapForm();
        $("#btn-add-terapi").data("cpptId", data.primary);
      });
      $(rowElement)
        .find(".btn-cancel-edit-soap")
        .bind("click", () => {
          resetSoapForm();
          $("#cpptDate-form-wrapper").removeClass('hidden');
          $(rowElement).toggleClass("edited-row");
          $(rowElement).find(".btn-edit-soap").toggleClass("hidden");
          $(rowElement).find(".btn-cancel-edit-soap").toggleClass("hidden");
          sessionStorage.removeItem(`suggestsoapranap#${pendaftaran_id}#${pegawai_id}#${id_ruangan}`);
          $("#cpptform-is_icd_x").prop('checked', false).uniform('refresh');
          activeEditedCppt = "";
        });
      if (data.is_deleted) {
        for (var i = 0; i < rowElement.childNodes.length; ++i) {
          if (i != rowElement.childNodes.length - 1) {
            $(rowElement.childNodes[i]).addClass('strike-text')

          }
        }
      }
      if (data.is_dokter) {
        for (var i = 0; i < rowElement.childNodes.length; ++i) {
          if (i == 1) {
            $(rowElement.childNodes[i]).css('background-color', '#1FA345')
            $(rowElement.childNodes[i]).css('color', 'white')
          }
        }
      }

      if (data.primary == suggest_cppt_id.value) {
        activeEditedCppt = suggest_cppt_id.value;
      }
    },
    initComplete: function () {
      focusCpptBtnEdit($("#edited-cppt-id").val());
    },
    drawCallback: () => {
      // lepas validasi focus after edit
      // if (
      //   typeof activeEditedCppt != "undefined" &&
      //   activeEditedCppt != null &&
      //   activeEditedCppt != ""
      // ) {
      //   focusCpptBtnEdit(activeEditedCppt);
      // }
    },
  });
  tableCppt = tabel;

  // Hide filter
  $(".dataTables_filter").hide();

  // Show hide form
  $("#btn-soap").on("click", function (event) {
    // Prevent default
    event.preventDefault();

    // Show hide form
    $("div #div-soap").prop("hidden", false);
    $("div #div-cppt").prop("hidden", true);
  });

  // Show hide form
  $("#btn-verbal-order").on("click", function (event) {

    // Prevent default
    event.preventDefault();

    // Show hide form
    $("div #div-verbal-order").prop("hidden", false);
    $("div #div-soap").prop("hidden", true);
    $("div #div-cppt").prop("hidden", true);
  });

  // Show hide form
  $("#tb-cppt").on("click", ".btn-tambah-terapi-notused", function (event) {
    // id
    var cppt_id = $(this).data("id");

    // Ajax
    $.ajax({
      url: baseUrl + "ranap/pemeriksaan-rawat-inap/get-cppt-asesmen-medis",
      type: "POST",
      dataType: "json",
      data: { pendaftaran_id: pendaftaran_id },
      success: function (response) {
        // Cek response reseptur
        if (response.reseptur["0"]) {
          // Set default
          $("#berat_badan").val(response.reseptur["0"].berat_badan).trigger("change");
          $("#tinggi_badan").val(response.reseptur["0"].tinggi_badan).trigger("change");
          $("#luas_tubuh").val(response.reseptur["0"].luas_tubuh).trigger("change");

          // Cek value
          if (response.reseptur["0"].is_hamil == true) {
            // Set hamil
            var hamil = 1;
          }
          else {
            // Set hamil
            var hamil = 0;
          }

          // Radio
          $("input[name='ResepturForm[is_hamil]'][value=" + hamil + "]").prop('checked', true);
        }
        else {
          // Set default
          $("#berat_badan").val(response.asesmenMedis.berat_badan).trigger("change");
          $("#tinggi_badan").val(response.asesmenMedis.tinggi_badan).trigger("change");
          $("#luas_tubuh").val(response.asesmenMedis.luas_permukaantubuh).trigger("change");

          // Cek value
          if (response.asesmenMedis.is_hamil == true) {
            // Set hamil
            var hamil = 1;
          }
          else {
            // Set hamil
            var hamil = 0;
          }

          // Radio
          $("input[name='ResepturForm[is_hamil]'][value=" + hamil + "]").prop('checked', true);
        }

        // Cek response sppt
        if (response.cppt["0"]) {
          // Set default
          $("#diagnosa_id").val(response.cppt["0"].a_diag_utama).trigger("change");
          $("#nama_dokter_cppt").val(response.cppt["0"].nama_pegawai);
          $("#resepturform-pegawai_id").val(response.cppt["0"].pegawai_id);
        }
        else {
          // Set default
          $("#diagnosa_id").val(response.asesmenMedis.diagnosa_id).trigger("change");
        }
      }
    }).done(function () {
      // Set id
      $("#instruksiform-cppt_id").val(cppt_id);
      $("#resepturnrdetailform-cppt_id").val(cppt_id);
      $("#resepturdetailform-cppt_id").val(cppt_id);
      $("#btn-save-reseptur").attr("action", "/ranap/pemeriksaan-rawat-inap/save-session-reseptur?pendaftaran_id=" + pendaftaran_id + "&cppt_id=" + cppt_id);
      $("#btn-reset-reseptur").attr("data-url", "/ranap/pemeriksaan-rawat-inap/reset-reseptur-session?pendaftaran_id=" + pendaftaran_id + "&cppt_id=" + cppt_id);

      // Show hide form
      $("div #div-cppt").prop("hidden", true);
      $("div #div-terapi").prop("hidden", false);

      // Cek cppt
      if (cppt_id != '') {
        // Draw
        tabel_reseptur.ajax.url(baseUrl + "ranap/pemeriksaan-rawat-inap/get-data-reseptur-session?pendaftaran_id=" + pendaftaran_id + "&cppt_id=" + cppt_id + "").draw();
      }
    });
  });
  // Ubah terapi
  $("#tb-cppt").on("click", ".btn-ubah-terapi", function (event) {
    var cpptid = $(this).data('cpptid');
    var instruksiid = $(this).data('instruksiid');
    var jnsinstruksi = $(this).data('jnsinstruksi');
    if (jnsinstruksi == 'PENUNJANG') {
      alert('under construction');
      return;
    }
    $('#content-cppt').docoLoad({
      url: '/ranap/pemeriksaan-rawat-inap/cppt-transaksi-instruksi?' +
        'id=' + pendaftaran_id +
        '&pasien_id=' + pasien_id +
        '&cppt_id=' + cpptid +
        '&instruksi_id=' + instruksiid +
        '&jns_instruksi=' + String(jnsinstruksi) +
        '&is_ubah=1' +
        '&from=cppt',
      dataType: 'html',
      success: function (data) {
      }
    });
  });

  // Hapus terapi
  $("#tb-cppt").on("click", ".btn-hapus-terapi", function (event) {
    // Delete
    var jnsinstruksi = $(this).data('tipeinstruksi');
    if (jnsinstruksi == 'PENUNJANG') {
      alert('under construction');
      return;
    }
    $(this).docoForm("delete", {
      additional: 'data-rm',
      success: function (data) {
        // Draw tabel
        tabel.draw();
      }
    });
  });

  $("#tb-cppt").on("click", ".btn-tambah-terapi", function (event) {
    var cpptid = $(this).data('cpptid');
    $('#content-cppt').docoLoad({
      url: '/ranap/pemeriksaan-rawat-inap/cppt-transaksi-instruksi?' +
        'id=' + pendaftaran_id +
        '&pasien_id=' + pasien_id +
        '&cppt_id=' + cpptid,
      dataType: 'html',
      success: function (data) {
      }
    });
  });

  $("#btn-add-terapi").on("click", function (e) {
    const cpptId = $("#btn-add-terapi").data('cpptId')
    if (typeof cpptId != 'undefined' && cpptId != '' && cpptId != null) {
      $('#content-cppt').docoLoad({
        url: `/ranap/pemeriksaan-rawat-inap/cppt-transaksi-instruksi?id=${pendaftaran_id}&pasien_id=${pasien_id}&cppt_id=${cpptId}`,
        dataType: 'html',
        success: function (data) {
        }
      });
    }
  })

  $("#btn-add-instruksi-dpjp").on("click", function (e) {
    $('#content-cppt').docoLoad({
      url: '/ranap/pemeriksaan-rawat-inap/create-instruksi-dpjp?id=' + pendaftaran_id,
      dataType: 'html',
      success: function (data) {
        $("div #div-instruksi-dpjp").prop("hidden", false);
        $("div #div-verbal-order").prop("hidden", true);
        $("div #div-soap").prop("hidden", true);
        $("div #div-cppt").prop("hidden", true);
      }
    });
  })

  // verifikasi
  $("#tb-cppt").on("click", ".btn-verifikasi-dpjp", function (event) {
    event.preventDefault();
    var cpptid = $(this).data('cpptid');
    var ispagt = $(this).data('ispagt');
    if (typeof ispagt == 'undefined') {
      ispagt = 0;
    }
    $(this).docoForm('click', {
      url: '/ranap/pemeriksaan-rawat-inap/cppt-verifikasi-dpjp?id=' + pendaftaran_id + '&cppt_id=' + cpptid + '&ispagt=' + ispagt,
      dataType: 'html',
      success: function (data) {
        tabel.draw()
      }
    });
  });

  if (status_disabled == 1) {
    $(".btn-verifikasi-dpjp").attr("disabled", true);
    $(".input-group-addon").hide();
  }

  // verifikasi verbal order
  $("#tb-cppt").on("click", ".btn-verifikasi-verbal", function (event) {
    event.preventDefault();
    var cpptid = $(this).data('cpptid');
    $(this).docoForm('click', {
      url: '/ranap/pemeriksaan-rawat-inap/cppt-verifikasi-verbal-order?id=' + cpptid,
      dataType: 'html',
      success: function (data) {
        tabel.draw()
      }
    });
  });

});
var clearSoapForm = () => {
  $("#form-soap .has-error").removeClass('has-error')
  $("#form-soap span.help-block.error").remove()
}
var resetSoapForm = () => {
  $("#cpptform-tgl_cppt").val(moment(new Date()).format('DD/MM/YYYY HH:mm:ss'))
  $("#form-soap textarea").val('')
  $("#form-soap select").val(null)
  $("#form-soap select").trigger('change')
  $("#header-form-soap").text('Tambah SOAP')
  $("#form-soap").prop('action', '/ranap/pemeriksaan-rawat-inap/create-soap')
  $("#cpptDate-form-wrapper").addClass('hidden')
  $("#btn-add-terapi").data('cpptId', last_cppt)
  clearSoapForm()
}

var focusCpptBtnEdit = (cpptId) => {
  const elementBtn = $(`.btn-edit-soap[data-cpptid="${cpptId}"]`)
  if (elementBtn.length > 0) {
    $('#tb-cppt_wrapper').animate({
      scrollTop: 0,
    }, 'fast', () => {
      $('#tb-cppt_wrapper').animate({
        scrollTop: elementBtn.offset().top - $('#tb-cppt_wrapper').offset().top - 100
      }, 2000, () => {
        elementBtn.click()
      });
    })
    $('html, body').animate({
      scrollTop: 350
    }, 'fast');
  }
}

$('#filter-cppt-ruangan_id,#filter-cppt-pegawai_id,.endDate1,#filter-cppt-kelompokpegawai_id').on('change', function () {
  tabel.draw();
})

$('.startDate1').on('change', function () {
  $('.endDate1').prop('disabled', false)
})

$('.pickadate').pickadate({
  format: 'dd/mm/yyyy',
  formatSubmit: 'yyyy-mm-dd',
});


$('#btn-print-cppt',).on('click', function (e) {
  e.preventDefault();
  ruangan_id = $('#filter-cppt-ruangan_id').val();
  pegawai_id = $('#filter-cppt-pegawai_id').val();
  kelompokpegawai_id = $('#filter-cppt-kelompokpegawai_id').val();
  var tgl_cppt = null;
  if ($('.endDate1').val() != '') {
    tgl_cppt = $('.startDate1').val() + '-' + $('.endDate1').val();
  }

  // var url = "/igd/pemeriksaan-igd/cetak-asmed-rd?id=" + pendaftaran_id;
  // window.open(url, '_blank');
  $('#btn-print-cppt').attr('data-url', '/ranap/pemeriksaan-rawat-inap/show-popup-pdf?id=' + pendaftaran_id + '&ruangan_id=' + ruangan_id + '&pegawai_id' + pegawai_id + '&tgl_cppt' + tgl_cppt + `&kelompokpegawai_id=${kelompokpegawai_id}`);
});

$('#btn-reset-filter-cppt').on('click', function (e) {
  e.preventDefault();
  $('.startDate1').val(null).trigger('change');
  $('.endDate1').val(null).trigger('change');
  $('.endDate1').prop('disabled', true)
  $('#filter-cppt-ruangan_id').val(null).trigger('change');
  $('#filter-cppt-pegawai_id').val(null).trigger('change');
  $('#filter-cppt-kelompokpegawai_id').val(null).trigger('change');
});
