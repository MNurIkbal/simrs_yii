// tab load
var listExceptResume = ['riwayat_penyakit_dahulu', 'keluhan_utama', 'diag_utama', 'nama_alergi','obat_dibawa_pulang_text','instruksi_tindakanbmhp','instruksi_kontrol'
  ,'konsultasi','edukasi_rencana','is_igd','kontak_darurat','instruksi_kontrol','instruksi_kontrolinstruksi_kontrol','instruksi_tanggal','td'];

var pageFormDataValues = [];
var pageFormId = null;

$(document).ready(function () {
  localStorage.removeItem("hash-url");
  var lastHash = $(this).find("ul .active").find("a").attr("href");
  window.location.hash = $(this).find("ul .active").find("a").attr("href");

  var cekUpdateResume = function () {
    if (resumeUpdate == true) {
      resumeUpdate = false;
      $('#btn-simpan-resume-medis-auto').click()
    }
  }

  function runPeriksaMenuAction (menuId) {
      switch (menuId) {
          case 'tab-asesmenawal':
              cekUpdateResume();
              $("#content-asesmenawal").docoLoad({
                url:
                  "/ranap/pemeriksaan-rawat-inap/asesmen-keperawatan?id=" +
                  pendaftaran_id +
                  "&pasien_id=" +
                  pasien_id,
                dataType: "html",
                success: function (data) {
                  // $(" .select2 ").select2();
                },
              });
              break;
          case 'tab-asesmenmedis':
              // Konten asesmen awal medis
              cekUpdateResume();
              $("#content-asesmenmedis").docoLoad({
                  url:
                      "/ranap/pemeriksaan-rawat-inap/asesmen-medis?id=" +
                      pendaftaran_id +
                      "&pasien_id=" +
                      pasien_id,
                  dataType: "html",
                  success: function (data) {
                    // $(" .select2 ").select2();
                  },
              });
              break;

          case 'tab-pagt':
              cekUpdateResume();
              $("#content-pagt").docoLoad({
                  url:
                      "/ranap/pemeriksaan-rawat-inap/pagt?id=" +
                      pendaftaran_id +
                      "&pasien_id=" +
                      pasien_id,
                  dataType: "html",
                  success: function (data) {
                    // $(" .select2 ").select2();
                  },
              });
              break;

          case 'tab-nursingnote':
              $('#content-nursingnote').docoLoad({
                  url:
                      '/ranap/pemeriksaan-rawat-inap/nursing-note?id=' + pendaftaran_id + "&pasienadmisi_id=" + pasienadmisi_id,
                  dataType: 'html',
                  success: function (data) { },
              })
              break;

          case 'tab-cppt':
              cekUpdateResume();
              // Konten cppt
              $("#content-cppt").docoLoad({
                  url:
                      "/ranap/pemeriksaan-rawat-inap/cppt?id=" +
                      pendaftaran_id +
                      "&pasien_id=" +
                      pasien_id,
                  dataType: "html",
                  success: function (data) {
                    // $(" .select2 ").select2();
                  },
              });
              break;

          case 'tab-implementasi':
              cekUpdateResume();
              // Konten implementasi
              $("#content-implementasi").docoLoad({
                  url:
                      "/ranap/pemeriksaan-rawat-inap/implementasi?id=" +
                      pendaftaran_id +
                      "&pasien_id=" +
                      pasien_id,
                  dataType: "html",
                  success: function (data) {
                    // $(" .select2 ").select2();
                  },
              });
              break;

          case 'tab-pemberian-infus':
              cekUpdateResume();
              // Konten infus
              $("#content-pemberian-infus").docoLoad({
                  url:
                      "/ranap/pemeriksaan-rawat-inap/pemberian-infus?id=" +
                      pendaftaran_id +
                      "&pasien_id=" +
                      pasien_id,
                  dataType: "html",
                  success: function (data) {
                    // $(" .select2 ").select2();
                  },
              });
              break;

          case 'tab-permintaan-konsul':
              cekUpdateResume();
              // Konten permintaan konsul
              $("#content-permintaan-konsul").docoLoad({
                  url:
                      "/ranap/pemeriksaan-rawat-inap/permintaan-konsul?id=" +
                      pendaftaran_id +
                      "&pasien_id=" +
                      pasien_id,
                  dataType: "html",
                  success: function (data) {
                      $(" .select2 ").select2();
                  },
              });
              break;

          case 'tab-pemberian-obat':
              cekUpdateResume();
              // Konten implementasi
              $("#content-pemberian-obat").docoLoad({
                  url:
                      "/ranap/pemeriksaan-rawat-inap/pemberian-obat?id=" +
                      pendaftaran_id +
                      "&pasien_id=" +
                      pasien_id,
                  dataType: "html",
                  success: function (data) {
                    // $(" .select2 ").select2();
                  },
              });
              break;

          case 'tab-permintaan-makan':
              cekUpdateResume();
              // Konten implementasi
              $("#content-permintaan-makan").docoLoad({
                url:
                    "/ranap/pemeriksaan-rawat-inap/minta-makan?id=" +
                    pendaftaran_id +
                    "&pasien_id=" +
                    pasien_id,
                dataType: "html",
                success: function (data) {
                  // $(" .select2 ").select2();
                },
              });
              break;

          case 'tab-rekonsobat':
              cekUpdateResume();
              $("#content-rekonsobat").docoLoad({
                url:
                    "/ranap/pemeriksaan-rawat-inap/rekonsobat?id=" +
                    pendaftaran_id +
                    "&pasien_id=" +
                    pasien_id,
                dataType: "html",
                success: function (data) {
                  // $(" .select2 ").select2(); // deprecated because menimpah select 2 with ajax loading
                },
              });
              break;

          case 'tab-partograf':
              cekUpdateResume();
              $("#content-partograf").docoLoad({
                url:
                    "/ranap/pemeriksaan-rawat-inap/partograf?id=" +
                    pendaftaran_id +
                    "&pasien_id=" +
                    pasien_id,
                dataType: "html",
                success: function (data) { },
              });
              break;

          case 'tab-dischargeplan':
              cekUpdateResume();
              // Konten Discharge Planning
              $("#content-dischargeplan").docoLoad({
                  url:
                      "/ranap/pemeriksaan-rawat-inap/discharge-planning?id=" +
                      pendaftaran_id +
                      "&pasienadmisi_id=" +
                      pasienadmisi_id,
                  dataType: "html",
                  success: function (data) {
                      $(" .select2 ").select2();
                  },
              });
              break;

          case 'tab-resumemedis':
              cekUpdateResume();
              $("#content-resumemedis").docoLoad({
                  url:
                      "/ranap/pemeriksaan-rawat-inap/resumemedis?id=" +
                      pendaftaran_id +
                      "&pasien_id=" +
                      pasien_id,
                  dataType: "html",
                  success: function (data) { },
              });
              break;

          case 'tab-resumemedisri':
              cekUpdateResume();
              $("#content-resumemedisri").docoLoad({
                  url:
                    "/ranap/pemeriksaan-rawat-inap/resume-medis-ri?id=" +
                    pendaftaran_id +
                    "&pasien_id=" +
                    pasien_id +
                    "&pasienadmisi_id=" +
                    pasienadmisi_id,

                  dataType: "html",
                  success: function (data) {
                      onTabResumeMedisRI = 1;
                      if (localStorage.getItem("view-resumemedisri-" + pendaftaran_id) != null) {
                          localStorage.removeItem("view-resumemedisri-" + pendaftaran_id)
                          for (let [key, val] of Object.entries(JSON.parse(localStorage.getItem("view-resumemedisri-" + pendaftaran_id)))) {
                              if (val.type == 'text') {
                                  if ($.inArray(key, listExceptResume) === -1) {
                                      $('body').find('#resume-medis-ri-form #resumemedisform-' + key).val(val.value)
                                  }
                              } else if (val.type == 'select') {
                                  if (key != 'diag_penyerta' && key != 'diag_awal') {
                                      $('body').find(  '#resume-medis-ri-form #resumemedisform-' + key).append(new Option(val.value.text, val.value.id, true, true)).trigger('change');
                                  }
                              } else if (val.type == 'ckeditor') {
                                  if (key == 'order_laboratorium') {
                                      $('body').find('#resume-medis-ri-form #temporary_order_lab').html(val.value)
                                  }

                                  if (key == 'order_radiologi') {
                                      $('body').find('#resume-medis-ri-form #temporary_order_rad').html(val.value)
                                  }

                                  CKEDITOR.instances['resumemedisform-' + key].setData(val.value)
                              } else if (val.type == 'radio') {
                                  if (val.value.length > 0) {
                                      if(key != 'is_alergi'){
                                          $('body').find('#resume-medis-ri-form #resumemedisform-' + key + " input:radio[name='ResumeMedisForm[" + key + "]']").filter('[value=' + val.value + ']').attr('checked', true)
                                      }
                                  }
                              } else if (val.type == 'checkbox') {
                                  $('body').find('#resume-medis-ri-form #resumemedisform-' + key).attr('checked', $('body').find('#resume-medis-ri-form #resumemedisform-' + key).val() == val.value)
                              }
                          }
                      }
                  },
              });
              break;

          case 'tab-cathlab-koroangiografi':
              cekUpdateResume();
              $("#content-cathlab").docoLoad({
                  url:
                      "/ranap/pemeriksaan-rawat-inap/cathlab?id=" +
                      pendaftaran_id +
                      "&pasienadmisi_id=" +
                      pasienadmisi_id +
                      "&tipe=koroangiografi",
                  dataType: "html",
                  success: function (data) { },
              });
              break;

          case 'tab-cathlab-pci':
              cekUpdateResume();
              $("#content-cathlab").docoLoad({
                  url:
                      "/ranap/pemeriksaan-rawat-inap/cathlab?id=" +
                      pendaftaran_id +
                      "&pasienadmisi_id=" +
                      pasienadmisi_id +
                      "&tipe=pci",
                  dataType: "html",
                  success: function (data) { },
              });
              break;

        case 'tab-cathlab-dsa':
            cekUpdateResume();
            $("#content-cathlab").docoLoad({
                url:
                    "/ranap/pemeriksaan-rawat-inap/cathlab?id=" +
                    pendaftaran_id +
                    "&pasienadmisi_id=" +
                    pasienadmisi_id +
                    "&tipe=dsa",
                dataType: "html",
                success: function (data) { },
            });
            break;

        case 'tab-upload-dokumen':
            $("#content-upload-dokumen").docoLoad({
                url:"/api/upload-dokumen/tab-upload-dokumen?id=" +
                    pendaftaran_id +
                    "&pasienadmisi_id=" +
                    pasienadmisi_id,
                dataType:"html",
                success: function(data){},
            });
            break;

        case 'tab-surat-keterangan':
            cekUpdateResume();
            $("#content-surat-keterangan").docoLoad({
                url:"/ranap/pemeriksaan-rawat-inap/surat-keterangan?id=" + pendaftaran_id + "&pasienadmisi_id=" + pasienadmisi_id,
                dataType:"html",
                success: function(data){},
            });
            break;

        case 'tab-asmed-ranap':
          $("#content-asmed-ranap").docoLoad({
              url:"/ranap/pemeriksaan-rawat-inap/asmed-ranap?id=" + pendaftaran_id + "&pasienadmisi_id=" + pasienadmisi_id + "&pasien_id=" + pasien_id,
              dataType:"html",
              success: function(data){},
          });
          break;
        
        case 'tab-monitoring-ttv':
          tabUrlParams = new URLSearchParams({
            id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
          }).toString();

          $("#content-monitoring-ttv").docoLoad({
            url: "/ranap/pemeriksaan-rawat-inap/monitoring-ttv?" + tabUrlParams,
            dataType: 'html',
            success: function (data) {
            }
          });
          break;

        case 'tab-monitoring-ews':
            tabUrlParams = new URLSearchParams({
                id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
            }).toString();
            
            $("#content-monitoring-ews").docoLoad({
                url: "/ranap/pemeriksaan-rawat-inap/observasi-ews?"+tabUrlParams,
                dataType: 'html',
                success: function (data) {
                }
            });
            break;

        case 'tab-sbar':
            tabUrlParams = new URLSearchParams({
                id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
            }).toString();
            
            $("#content-sbar").docoLoad({
                url: "/ranap/pemeriksaan-rawat-inap/sbar?"+tabUrlParams,
                dataType: 'html',
                success: function (data) {
                }
            });
            break;

    

        default:
    }
  }
  function allowChangeMenu(event) {
      let _this = $(this);
      let defaultData = pageFormDataValues;
      let existingData = (pageFormId != null && pageFormId.length > 0) ? pageFormId.serializeArray() : [];
      let isEqualData = isArrayEqual(defaultData, existingData);
      let skipConfirmTabChange = event.data != undefined ? event.data.skipConfirmTabChange : false;
      if (pageFormDataValues.length == 0 || isEqualData || skipConfirmTabChange) {
          pageFormDataValues = [];
          pageFormId = null;
          runPeriksaMenuAction(_this.parents('li').attr('id')); //get parent untuk kasus igd, karena menu id nya ada di li
      } else {
          event.stopPropagation();
          confirmationDialog("Apakah anda yakin untuk meninggalkan halaman ini?", function (condition) {
              if (condition) {
                  pageFormDataValues = [];
                  pageFormId = null;
                  $(_this).trigger('click', {skipConfirmTabChange: true});
              } else {
                  return false;
              }
          });
      }
  }

  var onTabResumeMedisRI = 0;

  // var hash = document.location.hash;
  // var prefix = "tab_";
  // if (hash.match('#view-')) {
  //     var tab_link = hash.split('-').pop();
  // }
  // Change hash for page-reload
  // $('.nav-tabs a').on('shown', function (e) {
  //     window.location.hash = e.target.hash.replace("#", "#" + prefix);
  // });
  if ($("#tab-asesmenawal").hasClass("active")) {
    $("#content-asesmenawal").docoLoad({
      url:
        "/ranap/pemeriksaan-rawat-inap/asesmen-keperawatan?id=" +
        pendaftaran_id +
        "&pasien_id=" +
        pasien_id,
      dataType: "html",
      success: function (data) {
        // $(" .select2 ").select2();
      },
    });
  }

  if ($("#tab-partograf").hasClass("active")) {
    $("#content-partograf").docoLoad({
      url:
        "/ranap/pemeriksaan-rawat-inap/partograf?id=" +
        pendaftaran_id +
        "&pasien_id=" +
        pasien_id,
      dataType: "html",
      success: function (data) {
        /**
         * @todo Event untuk menambahkan input hipotermi
         * @author Sigit Arif Munandar <sigit@docotel.com>
         */
        $(document).on("click", ".btn-tambah-hipotermi", function (event) {
          var html = "";
          var div;
          event.preventDefault();

          html =
            "<div class='row hipotermi last-hipotermi'>" +
            "<br>" +
            "<div class='col-sm-10'>" +
            "<input type='text' class='form-control hipotermi_keterangan' name='KelahiranBayiForm[hipotermi_keterangan][]'>" +
            "</div>" +
            "<div class='col-sm-1'>" +
            "<button type='button' class='btn btn-sm btn-danger btn-hapus-hipotermi'><i class='fa fa-trash'></i></button>" +
            "</div>" +
            "</div>";

          div = $(".last-hipotermi").removeClass("last-hipotermi");
          div.after(html);
        });
      },
    });
  }
  if ($("#tab-cppt").hasClass("active")) {
    // $(".navbar-position").hide()
    $("#content-cppt").docoLoad({
      url:
        "/ranap/pemeriksaan-rawat-inap/cppt?id=" +
        pendaftaran_id +
        "&pasien_id=" +
        pasien_id,
      dataType: "html",
      success: function (data) {
        // $(" .select2 ").select2();
      },
    });
    $("#view-partograf").removeClass("active");
  }

  if ($("#tab-resumemedisri").hasClass("active")) {
    $("#content-resumemedisri").docoLoad({
      url:
        "/ranap/pemeriksaan-rawat-inap/resume-medis-ri?id=" +
        pendaftaran_id +
        "&pasien_id=" +
        pasien_id +
        "&pasienadmisi_id=" +
        pasienadmisi_id,
      dataType: "html",
      success: function (data) {

      },
    });
    $("#view-partograf").removeClass("active");
  }

  $("#tab-periksafisik").on("click", function (e) {
    cekUpdateResume();
    $("#content-periksafisik").docoLoad({
      url:
        "/ranap/pemeriksaan-rawat-inap/periksa-fisik?id=" +
        pendaftaran_id +
        "&pasien_id=" +
        pasien_id,
      dataType: "html",
      success: function (data) {
        $(" .select2 ").select2();
      },
    });
  });

  //fungsi untuk cetak halaman partograf
  $(document).on("click", "#btn-cetak-keadaan-umum", function (e) {
    let url = window.location.origin;
    let target = $("#btn-cetak-keadaan-umum").attr("data-target");
    window.open(url + target);
  });
  $(".nav-tabs a").on("click", function (event) {
    if ($(this).data("hash") == undefined) {
      localStorage.setItem("hash-url", lastHash);
      lastHash = $(this).attr("href");
      window.location.hash = $(this).attr("href");
    }

    if (event.target.getAttribute("href") != '#view-resumemedisri' && onTabResumeMedisRI == 1) {
      let dataResumeMedisRI = {};
      const dataSaveResumeMedisRi = {
        'tgl_masuk': 'text',
        'diag_utama': 'text',
        'tgl_keluar': 'text',
        'diag_awal': 'select',
        'diag_penyerta': 'select',
        'keluhan_utama': 'text',
        'riwayat_penyakit_dahulu': 'text',
        'pemeriksaan_fisik': 'text',
        'indikasi_pasien_dirawat': 'text',
        'order_laboratorium': 'ckeditor',
        'order_radiologi': 'ckeditor',
        'lain_lainnya': 'text',
        'prosedur': 'ckeditor',
        'instruksi_tindakanbmhp': 'text',
        'obat_rs': 'text',
        'is_alergi': 'radio',
        'nama_alergi': 'text',
        'keadaan_umum': 'text',
        'kesadaran': 'text',
        'td': 'text',
        'suhu': 'text',
        'nadi': 'text',
        'frekuensi_nafas': 'text',
        'cara_keluar': 'radio',
        'obat_dibawa_pulang_text': 'text',
        'instruksi_kontrol': 'text',
        'instruksi_tanggal': 'text',
        'is_igd': 'checkbox',
        'kontak_darurat': 'text',
        'edukasi_rencana': 'text',
        'konsultasi': 'text'
      };

      for (let [key, val] of Object.entries(dataSaveResumeMedisRi)) {
        if (val == 'text') {
          dataResumeMedisRI[key] = {
            type: 'text',
            value: $('body').find('#resume-medis-ri-form #resumemedisform-' + key).val()
          }
        } else if (val == 'select') {
          dataResumeMedisRI[key] = {
            type: 'select',
            value: {
              id: $('body').find('#resume-medis-ri-form #resumemedisform-' + key + ' :selected').val(),
              text: $('body').find('#resume-medis-ri-form #resumemedisform-' + key + ' :selected').text()
            }
          }
        } else if (val == 'ckeditor') {
          dataResumeMedisRI[key] = {
            type: 'ckeditor',
            value: CKEDITOR.instances['resumemedisform-' + key].getData()
          }
        } else if (val == 'radio') {
          dataResumeMedisRI[key] = {
            type: val,
            value: $('body').find('#resume-medis-ri-form #resumemedisform-' + key + " [name='ResumeMedisForm[" + key + "]']:checked").val() ?? ''
          }
        } else if (val == 'checkbox') {
          dataResumeMedisRI[key] = {
            type: val,
            value: $('body').find('#resume-medis-ri-form #resumemedisform-' + key).is(':checked') ? $('body').find('#resume-medis-ri-form #resumemedisform-' + key).val() : ''
          }
        }
      }

      localStorage.setItem("view-resumemedisri-" + pendaftaran_id, JSON.stringify(dataResumeMedisRI))

      onTabResumeMedisRI = 0;
    }
  });

  $(".nav-tabs.nav-tab-periksa a").bind("click", allowChangeMenu)

});

jQuery(function ($) {
  $(document).on("click", ".btn-ganti-tindakan", function () {
    $(this).closest("tr").toggleClass("strikeout");
    var thisTr = $(this).closest("tr");
    var dataCount = $(this).closest("tr").data("count");
    var tr_instruksitindakanid = $(this)
      .closest("tr")
      .data("id_instruksi_tindakan");
    var newInput = "";
    newInput +=
      '<input class="is_ubah_deleted" type="hidden" name="InstruksiTindakanForm[' +
      dataCount +
      '][is_ubah_deleted]" value="1" readonly="readonly">';
    if ($(this).closest("tr").find("input.is_ubah_deleted").length === 0) {
      $(this).closest("tr").append(newInput);
    } else {
      $(this).closest("tr").find("input.is_ubah_deleted").remove();
    }

    update_daftar_tindakan();

    $(document)
      .find(".tabel-bmhp > tbody > tr")
      .each(function () {
        if ($(this).data("id_instruksi_tindakan") != "0") {
          if ($(this).data("id_instruksi_tindakan") == tr_instruksitindakanid) {
            $(this).data("id_instruksi_tindakan", 0);
            $(this).children("td.namatindakan").text("-");
            $(this).children("input.id_instruksi_tindakan").val(0);
            $(this).children("input.daftartindakan_id").val("");
          }
        }
      });
  });
  // On click btn remove
  $(document).on("click", ".btn-ganti-bmhp", function () {
    // Get obat id
    $(this).closest("tr").toggleClass("strikeout");
    var thisTr = $(this).closest("tr");
    var id = $(this).closest("tr").find(".obatalkes_id").val();
    var jumlah = $(this).closest("tr").find(".qty").val();
    var ruangan_id = $("#ruangan_id").val();
    var dataCount = $(this).closest("tr").data("count");
    var newInput = "";
    newInput +=
      '<input class="is_ubah_deleted" type="hidden" name="InstruksiTindakanBmhpForm[' +
      dataCount +
      '][is_ubah_deleted]" value="1" readonly="readonly">';
    if ($(this).closest("tr").find("input.is_ubah_deleted").length === 0) {
      $(this).closest("tr").append(newInput);
    } else {
      $(this).closest("tr").find("input.is_ubah_deleted").remove();
    }

    // Numbering
    $(document)
      .find(".td-no-bmhp")
      .each(function (index) {
        // Assign number
        $(this).text(index + 1);
      });
  });
});

$(document).on("click", ".add-tindakan", function (e) {
  e.preventDefault();
  var div_group = $(this).closest(".group-tindakan");
  var name = $(this).attr("data-name");
  var index = $(this).attr("data-index");

  div_group.append(
    '<div class="input-group"><input type="text" class="form-control" placeholder="Tindakan" name="KalaTigaForm[' +
    name +
    '][]" id="' +
    name +
    "-" +
    index +
    '"><span class="input-group-btn"><button class="btn btn-default btn-danger rm-tindakan" type="button"><i class="fa fa-minus"></i></button></span></div>'
  );
  $(this).attr("data-index", parseInt(index) + 1);
});

$(document).on("click", ".rm-tindakan", function () {
  var parent = $(this).closest(".input-group").remove();
});

var offsetTopNavTabs = $("#nav-sticky").offset().top;
var heightNav = $($(".navbar-position")[0]).height();
var patientHeight = $($('.patient-informations')[0]).height()
var patientOffset = $($('.patient-informations')[0]).offset().top
$(document).scroll(function () {
  var scrollTop = $(document).scrollTop();

  if ( (scrollTop + patientHeight) > patientOffset) {
    $('.patient-informations').addClass('floating-sticky')
  } else {
      $('.patient-informations').removeClass('floating-sticky')
  }

  if (scrollTop + heightNav >= offsetTopNavTabs) {
    $("#nav-sticky").addClass("nav-tabs__sticky");
  } else {
    $("#nav-sticky").removeClass("nav-tabs__sticky");
  }
});

$(document).ready(function () {
  $("#terra-medik-soap").click(function () {
    $("#terra-medik-soap").attr(
      "action",
      "/ranap/riwayat-pasien/modal-history-terra-medik?pasien_id=" +
      pasien_id
    );
  });

  // initial collapsed
  $('.can-expanded').each(function(){
      toggleExpandRiwayat($(this));
  })

  $('.expand-data').on('click', function(e){
      e.preventDefault();
      if(!$(this).attr('expanded')){
          $(this).html('Ringkaskan..');
          $(this).attr('expanded', true);
      }else{
          $(this).html($(this).attr('data-text'));
          $(this).removeAttr('expanded');
      }
      let elementExpand = $(this).siblings('.can-expanded');
      toggleExpandRiwayat(elementExpand);
  });

  function toggleExpandRiwayat(elementTarget){
      let expandElement = elementTarget.children();
      if(expandElement.length > 3){
          expandElement.each(function(index, element){
              if(index > 1){
                  if($(this).css('display') == 'none'){
                      $(this).show();
                  }else{
                      $(this).hide();
                  }
              }
          })
      }
  }
});
