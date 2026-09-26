/*
 * @Author: Sigit
 * @Date:   2018-08-13 10:58:30
 */
var timer;
var draftRecord = {};
var isDraftActive = true;
var list_pemeriksaanlab = [];
var local = sessionStorage.getItem(`suggestsoapigd#${pendaftaran_id}#${pegawai_id}#${id_ruangan}`);
var suggest_storage = JSON.parse(local);
var suggest_cppt_id = local !== null ? suggest_storage.find(element => element.name == 'cpptId') : '';
var activeEditedCppt = '';
var limitDefault = 5;

$(() => {
  $('#cpptform-is_icd_x').val(0)
  $('#cpptform-a_diag_utama').parent().hide()
  var local = sessionStorage.getItem(`suggestsoapigd#${pendaftaran_id}#${pegawai_id}#${id_ruangan}`);
  var suggest_storage = JSON.parse(local)
  var load_suggest = true

  if (local != null) {
    $.each(suggest_storage, function () {
      var name = this.name;
      var value = this.value;
      var date = new Date()
      var text = null
      // if(name == 'cpptForm[date]'){
      //     var waktu_terpakai = (date.getTime() - value)/60000
      //     if(waktu_terpakai > time_reset){
      //       isDraftActive = false;
      //       load_suggest = false;
      //       $('#cpptform-subject').val(null)
      //       $('#cpptform-object').val(null)
      //       $('#cpptform-planning').val(null)
      //       $('#cpptform-instruksi').val(null)
      //       $('#cpptform-a_diag_utama').val(null).trigger('change')
      //       $('#cpptform-a_diag_penyerta').val(null).trigger('change')
      //       $('#cpptform-catatan_dokter').val(null)
      //       sessionStorage.removeItem(`suggestsoapigd#${pendaftaran_id}#${pegawai_id}#${id_ruangan}`);
      //       isDraftActive = true;
      //     }

      // }
      if (load_suggest) {
        if (this.length > 0) {
          $.each(this, function () {
            var split_text_diag_penyerta = this.value.split("_")
            text_diag_penyerta = (split_text_diag_penyerta[1] === undefined) ? split_text_diag_penyerta : split_text_diag_penyerta[1]

            $('#cpptform-a_diag_penyerta').select2("trigger", "select", {
              data: { id: this.value, text: text_diag_penyerta }
            })
          })
        } else {
          if (name === undefined) {
            name = "cpptform[a_diag_penyerta]"
          }
          name = name.toLowerCase();
          if (name == 'cpptform[a_diag_utama]') {
            value = (this.value == 'undefined_') ? '' : this.value
            var split_text = value.split("_")
            text = (split_text[1] === undefined) ? split_text : split_text[1]
            $('#cpptform-a_diag_utama')[0].append(new Option(text, value, false, false))
          }

          if (name == 'cpptform-is_icd_x' && value == 1) {
            // $('#cpptform-a_diag_utama_text').parent().hide()
            $('#cpptform-is_icd_x').val(1)
            $('#cpptform-is_icd_x').prop('checked', true)
          }

          let res = name.replace('[', '-')
          res = res.replace(']', '')
          $(`#${res}`).val(this.value)
        }
      }
    });

    if ($('#cpptform-is_icd_x').val() == 1) {
      $('#cpptform-a_diag_utama_text').parent().hide()
      // $('#cpptform-a_diag_utama_text').val('')
      $('#cpptform-a_diag_utama').parent().show()
    } else {
      $('#cpptform-a_diag_utama_text').parent().show()
      $('#cpptform-a_diag_utama').parent().hide()
      // $('#cpptform-a_diag_utama').val(null).trigger('change')
    }
  }

  mapExistingSoapForm();
  $('#form-soap-index').on('change keyup', ({ delegateTarget }) => {
    if ($('#cpptform-is_icd_x').is(':checked')) {
      $('input[name="CpptForm[is_icd_x]').val(1)
      $('#cpptform-a_diag_utama_text').parent().hide()
      // $('#cpptform-a_diag_utama_text').val('')
      $('#cpptform-a_diag_utama').parent().show()
    } else {
      $('input[name="CpptForm[is_icd_x]').val(0)
      $('#cpptform-a_diag_utama_text').parent().show()
      $('#cpptform-a_diag_utama').parent().hide()
      // $('#cpptform-a_diag_utama').val(null).trigger('change')
    }
    saveDraftSoap();
  });

  $("#cpptform-a_diag_utama_text,#cpptform-a_diag_penyerta").on("change", function (event) {
    saveDraftSoap()
  })
  let _listpemeriksaanpenunjang = []
  let listOrderButton = pelayananConfigButton
  let _buttonHtml = '<div class="btn-group" role="group">';
  $.each(listOrderButton, (k, v) => {
    if (v.title.toLowerCase() == 'laboratorium' || v.title.toLowerCase() == 'radiologi' || v.title.toLowerCase() == 'penjadwalan') {

    } else {

      _buttonHtml += `<button
              type='button' ${typeof v.disabled != 'undefined' ? 'disabled' : ''}
              data-href="${v.url}"
              class='btn btn-labeled btn-info btn-xs btn-order-cppt'
              data-width='${typeof v.width != 'undefined' ? v.width : '90%'}'
              data-wrapper='${typeof v.wrapper != 'undefined' ? v.wrapper : '#content-cppt'}'
              onClick='${typeof v.onclick != 'undefined' ? v.onclick : ''}'
              ID='${typeof v.id != 'undefined' ? v.id : ''}'
              data-type='${v.name}'>
              ${v.title}
          </button>` 
    }
  })
  
  _buttonHtml += '</div>';
  $('#button-wrapper').html(_buttonHtml)

  let _buttonLaboratoriumHtml = '';
  $.each(listOrderButton, (k, v) => {
    if (v.title.toLowerCase() == 'laboratorium') {

      _buttonLaboratoriumHtml += `<button
              type='button' ${typeof v.disabled != 'undefined' ? 'disabled' : ''}
              style='margin-right: -7px;width:50px;' data-href="${v.url}"
              class='btn btn-xs btn-only btn-primary-color btn-order-cppt'
              data-width='${typeof v.width != 'undefined' ? v.width : '90%'}'
              data-wrapper='${typeof v.wrapper != 'undefined' ? v.wrapper : '#content-cppt'}'
              onClick='${typeof v.onclick != 'undefined' ? v.onclick : ''}'
              ID='${typeof v.id != 'undefined' ? v.id : ''}'
              data-type='${v.name}'>
                  <b><i class='fa ${v.icon}'></i></b>
          </button>`
    } else {

    }
  })
  $('#button-laboratorium').html(_buttonLaboratoriumHtml)

  let _buttonRadiologiHtml = '';
  $.each(listOrderButton, (k, v) => {
    if (v.title.toLowerCase() == 'radiologi') {

      _buttonRadiologiHtml += `<button
              type='button' ${typeof v.disabled != 'undefined' ? 'disabled' : ''}
              style='margin-right: -7px;width:50px;' data-href="${v.url}"
              class='btn btn-xs btn-only btn-primary-color btn-order-cppt'
              data-width='${typeof v.width != 'undefined' ? v.width : '90%'}'
              data-wrapper='${typeof v.wrapper != 'undefined' ? v.wrapper : '#content-cppt'}'
              onClick='${typeof v.onclick != 'undefined' ? v.onclick : ''}'
              ID='${typeof v.id != 'undefined' ? v.id : ''}'
              data-type='${v.name}'>
                  <b><i class='fa ${v.icon}'></i></b>
          </button>`
    } else {

    }
  })
  $('#button-radiologi').html(_buttonRadiologiHtml)

  let _buttonPenjadwalanHtml = '';
  $.each(listOrderButton, (k, v) => {
    if (v.title.toLowerCase() == 'penjadwalan') {

      _buttonPenjadwalanHtml += `<button
              type='button' ${typeof v.disabled != 'undefined' ? 'disabled' : ''}
              style='margin-right: -7px;width:50px;' data-href="${v.url}"
              class='btn btn-xs btn-only btn-primary-color btn-order-cppt'
              data-width='${typeof v.width != 'undefined' ? v.width : '90%'}'
              data-wrapper='${typeof v.wrapper != 'undefined' ? v.wrapper : '#content-cppt'}'
              onClick='${typeof v.onclick != 'undefined' ? v.onclick : ''}'
              ID='${typeof v.id != 'undefined' ? v.id : ''}'
              data-type='${v.name}'>
                  <b><i class='fa ${v.icon}'></i></b>
          </button>`
    } else {

    }
  })
  $('#button-penjadwalan').html(_buttonPenjadwalanHtml)

  let _buttonFisioterapiHtml = '';
  $.each(listOrderButton, (k, v) => {
    if (v.title.toLowerCase() == 'fisioterapi') {

      _buttonFisioterapiHtml += `<button
              type='button' ${typeof v.disabled != 'undefined' ? 'disabled' : ''}
              style='margin-right: -7px;width:50px;' data-href="${v.url}"
              class='btn btn-xs btn-only btn-primary-color btn-order-cppt'
              data-width='${typeof v.width != 'undefined' ? v.width : '90%'}'
              data-wrapper='${typeof v.wrapper != 'undefined' ? v.wrapper : '#content-cppt'}'
              onClick='${typeof v.onclick != 'undefined' ? v.onclick : ''}'
              ID='${typeof v.id != 'undefined' ? v.id : ''}'
              data-type='${v.name}'>
                  <b><i class='fa ${v.icon}'></i></b>
          </button>`
    } else {

    }
  })
  $('#button-fisioterapi').html(_buttonFisioterapiHtml)

  $('.btn-order-cppt').unbind('click')
  $('.btn-order-cppt').bind('click', function ({ currentTarget }) {
    const { type, href, wrapper, width } = $(currentTarget).data()
    let wrapperParents = wrapper.split(' ')[0]
    $(wrapperParents).find('.modal-dialog').css('width', width)
    showLoader('Memuat Halaman...')
    $(wrapper).docoLoad({
      url: href.replace('#cppt_id#', last_cppt).replace('#pendaftaran_id#', pendaftaran_id).replace('#ruangan_id#', ruanganperiksa_id),
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
  $('#btn-add-terapi').unbind('click')
  $("#btn-add-terapi").on("click", function (e) {
    $('#content-asesmen-dpjp').docoLoad({
      url: "/igd/pemeriksaan-igd/tambah-instruksi?id=" + pendaftaran_id + "&pasien_id_now=" + pasien_id_now + "&ruangan_now=" + ruangan_now + "&cppt_id=" + last_cppt,
      dataType: 'html',
      success: function (data) {
      }
    });
  })
})
function saveDraftSoap() {
  if (isDraftActive) {
    clearTimeout(timer);

    var IsIcdX = ''
    var IsIcdXValue = 0
    if ($('#cpptform-is_icd_x').is(':checked')) {
      IsIcdX = 'checked'
      IsIcdXValue = 1
    }

    let uniformCpptformIsIcdX = [{ name: 'uniform-cpptform-is_icd_x', value: IsIcdX }];
    let cpptformIsIcdX = [{ name: 'cpptform-is_icd_x', value: IsIcdXValue }];
    timer = setTimeout(function () {
      let cpptId = [{ name: "cpptId", value: activeEditedCppt }]
      var date = [{ name: "cpptForm[date]", value: new Date().getTime() }]
      var diag_penyerta = [$('#cpptform-a_diag_penyerta').serializeArray()]
      var suggest_soap = date.concat(
        cpptId,
        uniformCpptformIsIcdX,
        cpptformIsIcdX,
        $('#cpptform-subject').serializeArray(),
        $('#cpptform-object').serializeArray(),
        $('#cpptform-a_diag_utama').serializeArray(),
        $('#cpptform-a_diag_utama_text').serializeArray(),
        diag_penyerta,
        $('#cpptform-planning').serializeArray(),
        $('#cpptform-catatan_dokter').serializeArray(),
        $('#cpptform-instruksi').serializeArray(),
        {
          name: "edited-cppt-id",
          value: $('#edited-cppt-id').val()
        },
      )
      sessionStorage.setItem(`suggestsoapigd#${pendaftaran_id}#${pegawai_id}#${id_ruangan}`, JSON.stringify(suggest_soap));
    }, 1000);
  }
}

var table_dpjp;

function showSoap() {
  $("#div-soap").docoLoad({
    url: "/igd/pemeriksaan-igd/create-soap?id=" + pendaftaran_id,
    dataType: "html",
    success: function (data) {
      $("#div-soap").prop("hidden", false);
      $("#div-asesmen-dpjp").prop("hidden", true);
      $("#div-asesmen-dpjp-soap").prop("hidden", true);
      $("#div-verbal-order").prop("hidden", true);
    },
  });
}

function showVerbalOrder() {
  $("#div-verbal-order").docoLoad({
    url: "/igd/pemeriksaan-igd/create-verbal-order?id=" + pendaftaran_id,
    dataType: 'html',
    success: function (data) {
      $("#panel-form-soap").prop("hidden", true);
      $("#panel-cppt-igd").prop("hidden", true);
      $("#div-verbal-order").prop("hidden", false);
      $("#div-asesmen-dpjp").prop("hidden", true);
      $("#div-asesmen-dpjp-soap").prop("hidden", true);
      // $("#button-wrapper").prop("hidden", true);
      $("#div-soap").prop("hidden", true);
    }
  });
}

$("#btn-add-terapi").on("click", function (e) {
  const cpptId = $("#btn-add-terapi").data("cpptId");
  $("#content-asesmen-dpjp").docoLoad({
    url:
      "/igd/pemeriksaan-igd/tambah-instruksi?id=" +
      pendaftaran_id +
      "&pasien_id_now=" +
      pasien_id_now +
      "&ruangan_now=" +
      ruangan_now +
      "&cppt_id=" +
      cpptId,
    dataType: "html",
    success: function (data) { },
  });
});

$(document).ready(function () {

  $('#cpptform-is_icd_x').uniform()
  $("#btn-add-terapi").data('cpptId', last_cppt)
  // Generate Table
  var limitDefault = 6;
  var limit = 6;
  var currentPage = 0;
  table_dpjp = $("#tb-asesmen-dpjp").docoTabel({
    filter: false,
    info: false,
    sorting: [[0, 'desc']],
    paging: false,
    processing: true,
    serverSide: true,
    ajax: {
      url: baseUrl + 'igd/pemeriksaan-igd/get-data-asesmen-dpjp',
      data: function (data) {
        data.start = currentPage * limit;
        data.length = limit;
        data.id = pendaftaran_id;
        data.pasien_id = pasien_id;
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
      { title: 'Tanggal/Jam', data: "tgl_cppt", name: "tgl_soaprj", searchable: false, orderable: true },
      { title: 'Profesional Pemberi Asuhan', data: "ruangan", searchable: false, orderable: false },
      { title: 'Hasil Asesmen Penatalaksanaan Pasien', data: "penatalaksanaan", searchable: false, orderable: false },
      { title: 'Instruksi', data: "instruksi_soap", searchable: false, orderable: false },
      { title: 'Aksi', data: "verifikasi", searchable: false, orderable: false },
    ],
    createdRow: (rowElement, data) => {
      var waktu_pengisian = Date.parse(data.another_format_tgl_cppt)
      var is_dpjp = data.user_identity['gelarbelakang'] != null ? 1 : 0;
      var waktu_sekarang = Date.now()
      var expired = waktu_sekarang - waktu_pengisian
      $(rowElement).find('.btn-edit-soap').bind('click', () => {
        isDraftActive = true
        if (data.is_icd_x) {
          $('#cpptform-a_diag_utama_text').parent().hide()
          $('#cpptform-a_diag_utama').parent().show()
          $('#cpptform-is_icd_x').prop('checked', true).uniform('refresh');
        } else {
          $('#cpptform-a_diag_utama_text').parent().show()
          $('#cpptform-a_diag_utama').parent().hide()
          $('#cpptform-is_icd_x').prop('checked', false).uniform('refresh');
        }

        isDraftActive = false
        $("#tb-cppt .edited-row").removeClass('edited-row')
        $("#tb-cppt .btn-edit-soap").removeClass('hidden')
        $("#tb-cppt .btn-cancel-edit-soap").addClass('hidden')
        mapExistingSoapForm()
        if (suggest_cppt_id.value != data.primary) {
          setFormSoap(data)
        }
        activeEditedCppt = data.primary;
        $("#header-form-soap").text('Edit SOAP')
        $("#cpptDate-form-wrapper").removeClass('hidden')
        $("#cpptform-tgl_cppt").val(moment(data.origin_tgl_cppt).format('DD/MM/YYYY HH:mm:00'))
        $(rowElement).toggleClass('edited-row')
        $(rowElement).find('.btn-edit-soap').toggleClass('hidden')
        $(rowElement).find('.btn-cancel-edit-soap').toggleClass('hidden')
        clearSoapForm()
        $("#btn-add-terapi").data('cpptId', data.primary)
      })

      $(rowElement).find('.btn-copy-soap').bind('click', () => {
        isDraftActive = true
        if (data.is_icd_x) {
          $('#cpptform-a_diag_utama_text').parent().hide()
          $('#cpptform-a_diag_utama').parent().show()
          $('#cpptform-is_icd_x').prop('checked', true).uniform('refresh');
        } else {
          $('#cpptform-a_diag_utama_text').parent().show()
          $('#cpptform-a_diag_utama').parent().hide()
          $('#cpptform-is_icd_x').prop('checked', false).uniform('refresh');
        }

        isDraftActive = false
        mapExistingSoapForm()
        if (suggest_cppt_id.value != data.primary) {
          copyFormSoap(data)
        }
        activeEditedCppt = data.primary;
        $("#header-form-soap").text('Tambah SOAP')
        $("#cpptDate-form-wrapper").removeClass('hidden')
        $("#cpptform-tgl_cppt").val(moment().format('DD/MM/YYYY HH:mm:00'))
        $(rowElement).toggleClass('edited-row')
      })
      
      $(rowElement).find('.btn-cancel-edit-soap').bind('click', () => {
        sessionStorage.removeItem(`suggestsoapigd#${pendaftaran_id}#${pegawai_id}#${id_ruangan}`);
        resetSoapForm()
        setFormSoap(draftRecord)
        $(rowElement).toggleClass('edited-row')
        $(rowElement).find('.btn-edit-soap').toggleClass('hidden')
        $(rowElement).find('.btn-cancel-edit-soap').toggleClass('hidden')
        $("#btn-add-terapi").data('cpptId', last_cppt)
        $("#form-soap-index").prop(
          "action",
          `/igd/pemeriksaan-igd/create-soap?id=${pendaftaran_id}`
        );
      })
      if (data.is_dokter) {
        for (var i = 0; i < rowElement.childNodes.length; ++i) {
          if (i == 1) {
            $(rowElement.childNodes[i]).css('background-color', '#A7F8FA')
            $(rowElement.childNodes[i]).css('color', 'black')
          }
        }
      }
    },
    initComplete: function () {
      const elementBtn = $(`.btn-edit-soap[data-cpptid="${$('#edited-cppt-id').val()}"]`)
      if (elementBtn.length > 0) {
        $('#tb-asesmen-dpjp_wrapper').animate({
          scrollTop: elementBtn.offset().top - $('#tb-asesmen-dpjp_wrapper').offset().top - 100
        }, 2000);
        elementBtn.click()
        $('html, body').animate({
          scrollTop: 350
        }, 'slow');
      }
    },
    drawCallback: (settings) => {
    },
    footerCallback: function (row, data, start, end, display) {
      var api = this.api();
      var columnLength = api.columns().header().length;
      var tfoot = $('<tfoot id="docotabel-footer">\
          <tr id="menu-action-cppt">\
              <th colspan="'+columnLength+'">\
                  <div class="flex-menu-cppt">\
                      <div class="list-action-button-cppt-table" style="display: flex; justify-content: center; align-items: center;">\
                          <button type="button" class="btn btn-xs btn-only btn-primary-color btn-load">Load More Data</button>\
                          <button type="button" class="btn btn-xs btn-only btn-primary-color btn-hide">Hide Data</button>\
                      </div>\
                  </div>\
              </th>\
          </tr>\
      </tfoot>');
      $(api.table().node()).find('#docotabel-footer').remove();
      $(api.table().node()).append(tfoot);
      var json = api.ajax.json();
      if (json.load_more) {
          $('.btn-load').prop('disabled', false);
      } else {
          $('.btn-load').prop('disabled', true);
      }
      if (limit <= limitDefault) {
          $('.btn-hide').prop('disabled', true);
      } else {
          $('.btn-hide').prop('disabled', false);
      }
      $('.btn-load').off('click').on('click', function(event) {
          if (!event.detail || event.detail === 1) {
              limit += limitDefault;
              table_dpjp.ajax.reload(null, false);
          }
      });
      $('.btn-hide').off('click').on('click', function(event) {
          if (!event.detail || event.detail === 1) {
              limit -= limitDefault;
              table_dpjp.ajax.reload(null, false);
          }
      });
   },
  });

  // Hide filter
  $(".dataTables_filter").hide();

  $("#tb-asesmen-dpjp").on("click", ".btn-tambah-terapi", function (event) {
    var cpptid = $(this).data("cpptid");
    $("#content-asesmen-dpjp").docoLoad({
      url:
        "/igd/pemeriksaan-igd/tambah-instruksi?" +
        "id=" +
        pendaftaran_id +
        "&cppt_id=" +
        cpptid,
      dataType: "html",
      success: function (data) { },
    });
  });

  // Ubah terapi
  $("#tb-asesmen-dpjp").on("click", ".btn-ubah-terapi", function (event) {
    var cpptid = $(this).data("cpptid");
    var instruksiid = $(this).data("instruksiid");
    var jnsinstruksi = $(this).data("jnsinstruksi");
    if (jnsinstruksi == "PENUNJANG") {
      alert("under construction");
      return;
    }

    $("#content-asesmen-dpjp").docoLoad({
      url:
        "/igd/pemeriksaan-igd/ubah-instruksi?" +
        "id=" +
        pendaftaran_id +
        "&cppt_id=" +
        cpptid +
        "&instruksi_id=" +
        instruksiid +
        "&jns_instruksi=" +
        jnsinstruksi +
        "&from=asesmen-dpjp",
      dataType: "html",
      success: function (data) { },
    });
  });

  // Hapus terapi
  $("#tb-asesmen-dpjp").on("click", ".btn-hapus-terapi", function (event) {
    // Delete
    var jnsinstruksi = $(this).data('jnsinstruksi');
    if (jnsinstruksi == 'PENUNJANG') {
      alert('under construction');
      return;
    }
    $(this).docoForm("delete", {
      additional: 'data-rm',
      success: function (data) {
        // Draw tabel
        table_dpjp.draw();
      }
    });
  });
  pageFormId = $("#form-soap-index :not([readonly]):not('.disable-get-change')"); // get form id page | declare di pasien_pemeriksaan js
  pageFormDataValues = pageFormId.serializeArray(); // Get original value ketika pertama kali load page | declare di pasien_pemeriksaan js

  $('#btn-reset-filter-cppt').on('click', function (e) {
    e.preventDefault();
    $('.startDate1').val(null).trigger('change');
    $('.endDate1').val(null).trigger('change');
    $('.endDate1').prop('disabled', true)
    $('#filter-cppt-ruangan_id').val(null).trigger('change');
    $('#filter-cppt-pegawai_id').val(null).trigger('change');
    $('#filter-cppt-kelompokpegawai_id').val(null).trigger('change');
    limit = limitDefault;
    table_dpjp.draw();
  });
});

// verifikasi
$("#tb-asesmen-dpjp").on("click", ".btn-verifikasi-dpjp", function (event) {
  event.preventDefault();
  var cpptid = $(this).data('cpptid');
  $(this).docoForm('click', {
    url: '/igd/pemeriksaan-igd/verifikasi-dpjp?id=' + pendaftaran_id + '&cppt_id=' + cpptid,
    dataType: 'html',
    success: function (data) {
      table_dpjp.draw()
    }
  });
});

// verifikasi verbal order
$("#tb-asesmen-dpjp").on("click", ".btn-verifikasi-verbal", function (event) {
  event.preventDefault();
  var cpptid = $(this).data('cpptid');
  $(this).docoForm('click', {
    url: '/igd/pemeriksaan-igd/verifikasi-verbal-order?id=' + pendaftaran_id + '&cppt_id=' + cpptid,
    dataType: 'html',
    success: function (data) {
      table_dpjp.draw()
    }
  });
});

//delete verbal order
$("#tb-asesmen-dpjp").on("click", ".btn-delete-verbal", function (event) {
  event.preventDefault();
  var cpptid = $(this).data('cpptid');
  $(this).docoForm('click', {
    url: '/igd/pemeriksaan-igd/delete-verbal-order?id=' + pendaftaran_id + '&cppt_id=' + cpptid,
    dataType: 'json',
    success: function (data) {
      table_dpjp.draw()
    }
  });
});

var serializeRemove = function (thisArray, thisName) {
  "use strict";
  return thisArray.filter(function (item) {
    return item.name != thisName;
  });
}

$("#btn-save-soap-index").on("click", function (event) {
  event.preventDefault();
  var values = $("#form-soap-index").serializeArray();
  var diag_utama_length = $('#cpptform-a_diag_utama_text').val().length
  if (!$('#cpptform-is_icd_x').is(':checked')) {
    values = serializeRemove(values, 'CpptForm[a_diag_utama]')
    values.push({ name: 'CpptForm[a_diag_utama]', value: $('#cpptform-a_diag_utama_text').val() });

  }
  if ($('#cpptform-is_icd_x').is(':checked')) {
    values = serializeRemove(values, 'CpptForm[a_diag_utama_text]')
    values.push({ name: 'CpptForm[a_diag_utama_text]', value: $('#cpptform-a_diag_utama').val() });
    diag_utama_length = $('#cpptform-a_diag_utama').val().length
  }
  if ($('#cpptform-subject').val().length < 2 || $('#cpptform-object').val().length < 2 ||
    diag_utama_length < 2 || $('#cpptform-planning').val().length < 2) {
    new PNotify({
      title: "Peringatan",
      text: "bagian S / O / A / P hanya diisi satu karakter, minimal input pada field SOAP adalah 2 karakter",
      addclass: "alert alert-warning alert-arrow-right alert-styled-right",
      type: "warning",
      delay: 3000,
      hide: true
    });
    return false;
  }

  $(this).docoForm('click', {
    url: $("#form-soap-index").prop("action"),
    skipConfirm: true,
    data: values,
    method: "POST",
    success: function (response) {
      let { data } = response;
      sessionStorage.removeItem(`suggestsoapigd#${pendaftaran_id}#${pegawai_id}#${id_ruangan}`);
      if (typeof data.a_diag_utama.text != "undefined" && data.a_diag_utama.text != null) {
        $("#patient-history-tab")
          .find(".diagnosa-dokter-text")
          .text(data.a_diag_utama.text);
      }
      resetSoapForm()
      docoNotification(
        "success",
        "Proses berhasil",
        "Data berhasil disimpan"
      );
      pageFormDataValues = pageFormId.serializeArray(); // Get original value ketika pertama kali load page | declare di pasien_pemeriksaan js
      // $("#tab-asesmen-dpjp").trigger("click");
      table_dpjp.draw();
    },
    complete: () => {
      $("#edited-cppt-id").val("");
    },
  });

});

var clearSoapForm = () => {
  $("#form-soap-index .has-error").removeClass("has-error");
  $("#form-soap-index span.help-block.error").remove();
};
var resetSoapForm = () => {
  $("#cpptform-tgl_cppt").val(moment(new Date()).format('DD/MM/YYYY HH:mm:ss'))

  $("#form-soap-index textarea").val("");
  $("#form-soap-index select").val(null);
  $("#form-soap-index select").trigger("change");
  $("#header-form-soap").text("Tambah SOAP");
  $("#form-soap-index").prop("action", "/igd/pemeriksaan-igd/create-soap");
  clearSoapForm();
  isDraftActive = true;
};

var setFormSoap = (data) => {
  $("#cpptform-a_diag_utama").html("");
  $("#cpptform-a_diag_penyerta").html("");
  if (typeof data.a_diag_utama != "undefined" && data.a_diag_utama != null) {
    var decodeDiagnose = null;
    try {
      decodeDiagnose = JSON.parse(data.a_diag_utama);
    } catch (error) { }
    if (decodeDiagnose != null) {
      var newOption = new Option(
        decodeDiagnose.text,
        `${decodeDiagnose.id}_${decodeDiagnose.text}`,
        false,
        false
      );
      $("#cpptform-a_diag_utama")
        .append(newOption)
        .trigger("change")
        .val(`${decodeDiagnose.id}_${decodeDiagnose.text}`)
        .trigger("change");
      $('#cpptform-a_diag_utama_text').val(decodeDiagnose.text)
    }
  }
  if (
    typeof data.a_diag_penyerta != "undefined" &&
    data.a_diag_penyerta != null
  ) {
    var decodeDiagnose = null;
    try {
      decodeDiagnose = JSON.parse(data.a_diag_penyerta);
    } catch (error) { }
    var selectedOption = [];
    try {
      decodeDiagnose.map((diagnose) => {
        if (decodeDiagnose != null) {
          var newOption = new Option(
            diagnose.text,
            `${diagnose.id}_${diagnose.text}`,
            false,
            false
          );
          $("#cpptform-a_diag_penyerta").append(newOption);
          selectedOption.push(`${diagnose.id}_${diagnose.text}`);
        }
      });
      $("#cpptform-a_diag_penyerta").val(selectedOption).trigger("change");
    } catch (error) { }
  }
  $("#form-soap-index").prop(
    "action",
    `/igd/pemeriksaan-igd/edit-soap?id=${pendaftaran_id}&cpptId=${data.primary}`
  );
  $("#cpptform-subject").val(
    data.subject != null ? data.subject : null
  );
  $("#cpptform-object").val(
    data.object != null ? data.object : null
  );
  $("#cpptform-planning").val(
    data.planning != null ? data.planning : null
  );
  $("#cpptform-catatan_dokter").val(data.catatan_dokter);
  $("#cpptform-instruksi").val(data.instruksi != null
    ? data.instruksi
    : null);
  $("#cpptform-catatan_perawat").val(data.catatan_perawat);
  $('#edited-cppt-id').val(data.origin_cppt_id)
};
var copyFormSoap = (data) => {
  $("#cpptform-a_diag_utama").html("");
  $("#cpptform-a_diag_penyerta").html("");
  if (typeof data.a_diag_utama != "undefined" && data.a_diag_utama != null) {
    var decodeDiagnose = null;
    try {
      decodeDiagnose = JSON.parse(data.a_diag_utama);
    } catch (error) { }
    if (decodeDiagnose != null) {
      var newOption = new Option(
        decodeDiagnose.text,
        `${decodeDiagnose.id}_${decodeDiagnose.text}`,
        false,
        false
      );
      $("#cpptform-a_diag_utama")
        .append(newOption)
        .trigger("change")
        .val(`${decodeDiagnose.id}_${decodeDiagnose.text}`)
        .trigger("change");
      $('#cpptform-a_diag_utama_text').val(decodeDiagnose.text)
    }
  }
  if (
    typeof data.a_diag_penyerta != "undefined" &&
    data.a_diag_penyerta != null
  ) {
    var decodeDiagnose = null;
    try {
      decodeDiagnose = JSON.parse(data.a_diag_penyerta);
    } catch (error) { }
    var selectedOption = [];
    try {
      decodeDiagnose.map((diagnose) => {
        if (decodeDiagnose != null) {
          var newOption = new Option(
            diagnose.text,
            `${diagnose.id}_${diagnose.text}`,
            false,
            false
          );
          $("#cpptform-a_diag_penyerta").append(newOption);
          selectedOption.push(`${diagnose.id}_${diagnose.text}`);
        }
      });
      $("#cpptform-a_diag_penyerta").val(selectedOption).trigger("change");
    } catch (error) { }
  }
  $("#form-soap-index").prop(
    "action",
    `/igd/pemeriksaan-igd/create-soap?id=${pendaftaran_id}`
  );
  $("#cpptform-subject").val(
    data.subject != null ? data.subject : null
  );
  $("#cpptform-object").val(
    data.object != null ? data.object : null
  );
  $("#cpptform-planning").val(
    data.planning != null ? data.planning : null
  );
  $("#cpptform-catatan_dokter").val(data.catatan_dokter);
  $("#cpptform-instruksi").val(data.instruksi != null
    ? data.instruksi
    : null);
  $("#cpptform-catatan_perawat").val(data.catatan_perawat);
  $('#edited-cppt-id').val(data.origin_cppt_id)
};

var mapExistingSoapForm = () => {
  draftRecord = {
    subject: $("#cpptform-subject").val(),
    object: $("#cpptform-object").val(),
    planning: $("#cpptform-planning").val(),
    catatan_dokter: $("#cpptform-catatan_dokter").val(),
    instruksi: $("#cpptform-instruksi").val(),
    catatan_perawat: $("#cpptform-catatan_perawat").val(),
  };
  if (
    $("#cpptform-a_diag_utama").val() != "" &&
    $("#cpptform-a_diag_utama").val() != null
  ) {
    draftRecord.a_diag_utama = JSON.stringify({
      id: $("#cpptform-a_diag_utama").select2("data")[0].id,
      text: $("#cpptform-a_diag_utama").select2("data")[0].text,
    });
  }
  if (
    $("#cpptform-a_diag_penyerta").val() != "" &&
    $("#cpptform-a_diag_penyerta").val() != null
  ) {
    var diagnosePenyerta = [];
    $("#cpptform-a_diag_penyerta")
      .select2("data")
      .map(({ id, text }) => {
        diagnosePenyerta.push({
          id,
          text,
        });
      });
    draftRecord.a_diag_penyerta = JSON.stringify(diagnosePenyerta);
  }
};


$('#filter-cppt-ruangan_id,#filter-cppt-pegawai_id,#filter-cppt-kelompokpegawai_id').on('select2:select', function () {
  table_dpjp.draw();
})

$('.startDate1, .endDate1').pickadate({
  format: 'dd/mm/yyyy',
  formatSubmit: 'yyyy-mm-dd',
  onSet: function(context) {
    table_dpjp.draw();
  }
});

$('.startDate1').on('change', function () {
  $('.endDate1').prop('disabled', false)
})

$('.pickadate').pickadate({
  format: 'dd/mm/yyyy',
  formatSubmit: 'yyyy-mm-dd',
});

$('#btn-cetak-cppt',).on('click', function (e) {
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
  $('#btn-cetak-cppt').attr('data-url', '/igd/pemeriksaan-igd/show-popup-pdf?id=' + pendaftaran_id + '&ruangan_id=' + ruangan_id + '&pegawai_id' + pegawai_id + '&tgl_cppt' + tgl_cppt + `&kelompokpegawai_id=${kelompokpegawai_id}`);
});

$("#tb-asesmen-dpjp").on("click", ".btn-delete-cppt", function (event) {
  event.preventDefault();
    var cppt_id = $(this).attr('data-cpptid');
    var tipe = $(this).attr('data-tipe');
    $(this).docoForm("delete", {
      url: "/igd/pemeriksaan-igd/delete-cppt?pendaftaran_id=" + pendaftaran_id + "&cppt_id=" + cppt_id + '&tipe=' + tipe,
      success: function (params) {
        table_dpjp.draw();
      }
  });
});

