
let jk = '';
const existingPageUrl = 'daftar-ranap'
let existingReqData = {}
let dataBayi = {}
var alamatBy = {}
const dataDropDownPegawai = []
const firstCbG = 'add_carabayargroup_1';
const secondCbG = 'add_carabayargroup_2';
const tujuanKunjTrue = 1;
const tujuanKunReset = 0;
let isPilihKamarTitipan = false;
let isIntegrasi = false;

function pilihKamar(identifier) {
  let link = document.URL;
  let patternAction = link.match(/pemesanan-kamar/g);
  const jenisTempatTidur = $(identifier).data('kettempattidur_id');
  const kamarruangan_jenis = $(identifier).data('kamarruangan_jenis');
  const kamarruangan_id = $(identifier).data('kamarruangan_id');
  const kamartempattidur_id = $(identifier).data('kamartempattidur_id');

  var kelas_rawat = $("#kelas_rawat").val();
  var is_pasientitipan = $("#pasienTitipanValue").val();
  var is_pasienaps = $("#pasienApsValue").val();
  var carabayar_id = $("#selectCarabayar").val();

  var jk_kamar;
  var allow_jk;
  var attr = $(identifier).data('allow_jk');

  if(carabayar_id == 6) {
    $('#isApsCheck').prop('checked', true).trigger('change')
      // if(typeof kelas_rawat != 'undefined') {
      //     if(kelas_rawat != $(identifier).data('kelaspelayanan_id') && (is_pasientitipan == 0 && is_pasienaps == 0)) {
      //         docoNotification('error', i18next.t('Perhatian'), i18next.t('Kamar Titipan atau APS harus dipilih!'));
      //         return false;
      //     }
      // }
  }

  if (typeof attr !== typeof undefined && attr !== false) {
    allow_jk = $(identifier).data('allow_jk');
  }

  if (parseInt(jenisTempatTidur) == 1) {
    jk_kamar = 16;
  } else if (parseInt(jenisTempatTidur) == 2) {
    jk_kamar = 15;
  } else {
    jk_kamar = jk;
  }

  if (allow_jk && allow_jk != parseInt(jk)) {
    docoNotification('error', i18next.t('Perhatian'), i18next.t('Kamar fleksibel tidak sesuai dengan jenis kelamin'));
    return false;
  }

  if (jk != jk_kamar) {
    docoNotification('error', i18next.t('Perhatian'), i18next.t('Kamar tidak sesuai dengan jenis kelamin'));
    return false;
  }
  $.ajax({
    url: '/ranap/end-point/cek-ruangan-default',
    data: {
      ruangan_id: $(identifier).data('ruangan_id'),
      kelaspelayanan_id: $(identifier).data('kelaspelayanan_id'),
      penjamin_id: $("#penjamin_id").val(),
      kamarruanganId: kamarruangan_id,
      kamartempattidurId: kamartempattidur_id
    },
    method: 'POST',
    success: () => {
      if ($(document).find('#ruanganIdHidden').val() !== $(identifier).data('ruangan_id')) {
       getListDokter($(identifier).data('ruangan_id'))
      }

      var _keteranganKamar;
      var _kelaspelayanan_id;

      _kelaspelayanan_id = $(identifier).data('kelaspelayanan_id');
      _kelaspelayanan_nama = $(identifier).data('kelaspelayanan_nama');

      $("#kelaspelayanan_id").val(_kelaspelayanan_id).trigger('change');
      $(document).find("#ruanganIdHidden").val($(identifier).data('ruangan_id'));
      // if($("#kamarTitipanCheck").val() == 1) {
      //     _keteranganKamar = ' - KAMAR TITIPAN';
      //     _kelaspelayanan_id = $("input[name='bpjsKelas']").val();
      // } else if($("#isApsCheck").val() == 1) {
      //     _keteranganKamar = ' - APS';
      // } else {
      //     _keteranganKamar = ' - ' + _kelaspelayanan_nama;
      // }

      if (typeof $("input[name='bpjsKelas']").val() !== 'undefined' && parseInt($("input[name='bpjsKelas']").val()) !== parseInt($(identifier).data('kelaspelayanan_id'))) {
        $(document).find('#ruanganLabelValue').html("");
        // $(document).find('#ruanganLabelValue').append(` ${$(identifier).data('kelaspelayanan_nama')} ` + _keteranganKamar + ` (${($("input[name='bpjsKelas']").val() < $(identifier).data('kelaspelayanan_id') && $(identifier).data('kelaspelayanan_id') <= 3) ? 'TURUN KELAS' : 'NAIK KELAS'})`)
        $(document).find('#ruanganLabelValue').append(` ${$(identifier).data('kelaspelayanan_nama')}`)
      } else if (typeof $("input[name='bpjsKelas']").val() === 'undefined' && parseInt($("#kelaspelayanan_id").val()) !== parseInt($(identifier).data('kelaspelayanan_id'))) {
        $(document).find('#ruanganLabelValue').html("");
        $(document).find('#ruanganLabelValue').append(` ${$(identifier).data('kelaspelayanan_nama')} `)
      } else {
        $(document).find('#ruanganLabelValue').html("");
        $(document).find('#ruanganLabelValue').html($(identifier).data('ruangan_nama'));
      }

      $(document).find('#kamartempattidur_id').val($(identifier).data('kamartempattidur_id'));
      $(document).find('#kamarruangan_id').val($(identifier).data('kamarruangan_id'));
      $(document).find('#nokamar').val($(identifier).data('kamarruangan_nokamar') + ' - ' + $(identifier).data('no_tempattidur'));

      _formPendaftaran.getKarcis(_kelaspelayanan_id);
      $("#kelasPelayananSelected").val($(identifier).data('kelaspelayanan_id'));

      $("#btnCariKamar").focus();
      $('#modalTempatTidur').modal('hide');
      $('.is_pasientitipan').removeClass("hidden");

      $("#pasienadmisiform-is_pasientitipan").prop("checked", false);
      $(".kelas_ditagihkan").addClass("hidden");
      $("#kelas_ditagihkan_id").val("").trigger('change');
      $("#ruangan_titipan_id").val("");
      $("#ruanganTitipanLabelValue").text("");
    },
    error: ({ responseJSON }) => {
      docoNotification('error', responseJSON.meta.message, '')
    },
    complete: () => {
      hideLoader()
      $.unblockUI()
    }
  })
}

function pilihKamarRujukan(data) {
      if ($(document).find('#ruanganIdHidden').val() !== data.ruangan_id) {
       getListDokter(data.ruangan_id)
      }

      var _keteranganKamar;
      var _kelaspelayanan_id;

      _kelaspelayanan_id = data.kelaspelayanan_id;
      _kelaspelayanan_nama = data.kelaspelayanan_nama;

      $("#kelaspelayanan_id").val(_kelaspelayanan_id).trigger('change');
      $(document).find("#ruanganIdHidden").val(data.ruangan_id);
      // if($("#kamarTitipanCheck").val() == 1) {
      //     _keteranganKamar = ' - KAMAR TAGIHAN';
      //     _kelaspelayanan_id = $("input[name='bpjsKelas']").val();
      // } else if($("#isApsCheck").val() == 1) {
      //     _keteranganKamar = ' - APS';
      // } else {
      //     _keteranganKamar = ' - ' + _kelaspelayanan_nama;
      // }

      if (typeof $("input[name='bpjsKelas']").val() !== 'undefined' && parseInt($("input[name='bpjsKelas']").val()) !== parseInt($(identifier).data('kelaspelayanan_id'))) {
        $(document).find('#ruanganLabelValue').html("");
        // $(document).find('#ruanganLabelValue').append(` ${data.kelaspelayanan_nama} ` + _keteranganKamar + ` (${($("input[name='bpjsKelas']").val() < data.kelaspelayanan_id && data.kelaspelayanan_id <= 3) ? 'TURUN KELAS' : 'NAIK KELAS'})`)
        $(document).find('#ruanganLabelValue').append(` ${data.kelaspelayanan_nama}`)
      } else if (typeof $("input[name='bpjsKelas']").val() === 'undefined' && parseInt($("#kelaspelayanan_id").val()) !== parseInt(data.kelaspelayanan_id)) {
        $(document).find('#ruanganLabelValue').html("");
        $(document).find('#ruanganLabelValue').append(` ${data.kelaspelayanan_nama} `)
      } else {
        $(document).find('#ruanganLabelValue').html("");
        $(document).find('#ruanganLabelValue').html(data.ruangan_nama);
      }

      $(document).find('#kamartempattidur_id').val(data.kamartempattidur_id);
      $(document).find('#kamarruangan_id').val(data.kamarruangan_id);
      $(document).find('#nokamar').val(data.kamarruangan_nokamar + ' - ' + data.no_tempattidur);

      _formPendaftaran.getKarcis(_kelaspelayanan_id);
      $("#kelasPelayananSelected").val(data.kelaspelayanan_id);

      $("#btnCariKamar").focus();
      $('#modalTempatTidur').modal('hide');
      $('.is_pasientitipan').removeClass("hidden");

      $("#pasienadmisiform-is_pasientitipan").prop("checked", false);
      $(".kelas_ditagihkan").addClass("hidden");
      $("#kelas_ditagihkan_id").val("").trigger('change');
      $("#ruangan_titipan_id").val("");
      $("#ruanganTitipanLabelValue").text("");
      hideLoader()
      $.unblockUI()
}

function pilihKamarTitipan(identifier) {
  isPilihKamarTitipan = true;
  let link = document.URL;
  let patternAction = link.match(/pemesanan-kamar/g);
  const jenisTempatTidur = $(identifier).data('kettempattidur_id');
  const kamarruangan_jenis = $(identifier).data('kamarruangan_jenis');
  const kamarruangan_id = $(identifier).data('kamarruangan_id');
  const kamartempattidur_id = $(identifier).data('kamartempattidur_id');

  var kelas_rawat = $("#kelas_rawat").val();
  var is_pasientitipan = $("#pasienTitipanValue").val();
  var is_pasienaps = $("#pasienApsValue").val();
  var carabayar_id = $("#selectCarabayar").val();

  var jk_kamar;
  var allow_jk;
  var attr = $(identifier).data('allow_jk');

  if(carabayar_id == 6) {
      if(typeof kelas_rawat != 'undefined') {
          if(kelas_rawat != $(identifier).data('kelaspelayanan_id') && (is_pasientitipan == 0 && is_pasienaps == 0)) {
              docoNotification('error', i18next.t('Perhatian'), i18next.t('Kamar Titipan atau APS harus dipilih!'));
              return false;
          }
      }
  }

  if (typeof attr !== typeof undefined && attr !== false) {
    allow_jk = $(identifier).data('allow_jk');
  }

  if (parseInt(jenisTempatTidur) == 1) {
    jk_kamar = 16;
  } else if (parseInt(jenisTempatTidur) == 2) {
    jk_kamar = 15;
  } else {
    jk_kamar = jk;
  }

  if (allow_jk && allow_jk != parseInt(jk)) {
    docoNotification('error', i18next.t('Perhatian'), i18next.t('Kamar fleksibel tidak sesuai dengan jenis kelamin'));
    return false;
  }

  if (jk != jk_kamar) {
    docoNotification('error', i18next.t('Perhatian'), i18next.t('Kamar tidak sesuai dengan jenis kelamin'));
    return false;
  }
  $.ajax({
    url: '/ranap/end-point/cek-ruangan-default',
    data: {
      ruangan_id: $(identifier).data('ruangan_id'),
      kelaspelayanan_id: $(identifier).data('kelaspelayanan_id'),
      penjamin_id: $("#penjamin_id").val(),
      kamarruanganId: kamarruangan_id,
      kamartempattidurId: kamartempattidur_id
    },
    method: 'POST',
    success: () => {
      if ($(document).find('#ruanganTitipanIdHidden').val() !== $(identifier).data('ruangan_id')) {
        if ($('#pegawai_id').hasClass("select2-hidden-accessible")) {
          $('#pegawai_id').select2('destroy')
        }
        // ajax to get data pegawai_id by ruangan
        $.ajax({
          url: '/ranap/end-point/get-dokter-by-ruangan',
          data: {
            ruangan_id: $(identifier).data('ruangan_id')
          },
          method: 'GET',
          success: ({ data }) => {
            const dataDropDownPegawai = []
            Object.keys(data).map((itemOutput) => {
              dataDropDownPegawai.push({
                id: itemOutput,
                text: data[itemOutput]
              })
            })
            refreshOptionSelect2($('#pegawai_id'), dataDropDownPegawai)
          },
          complete: () => {
            $('#pegawai_id').prop('disabled', false)
          }
        })
      }

      var _keteranganKamar;
      var _kelaspelayanan_id;

      _kelaspelayanan_id = $(identifier).data('kelaspelayanan_id');
      _kelaspelayanan_nama = $(identifier).data('kelaspelayanan_nama');

      // $("#kelaspelayanan_id").val(_kelaspelayanan_id).trigger('change');
      $(document).find("#ruanganTitipanIdHidden").val($(identifier).data('ruangan_id'));
      // if($("#kamarTitipanCheck").val() == 1) {
      //     _keteranganKamar = ' - KAMAR TAGIHAN';
      //     _kelaspelayanan_id = $("input[name='bpjsKelas']").val();
      // } else if($("#isApsCheck").val() == 1) {
      //     _keteranganKamar = ' - APS';
      // } else {
      //     _keteranganKamar = ' - ' + _kelaspelayanan_nama;
      // }

      $(document).find('#ruanganTitipanLabelValue').html($(identifier).data('ruangan_nama'));
      $(document).find('#tempattidur_titipan_id').val($(identifier).data('kamartempattidur_id'));
      $(document).find('#kamar_titipan_id').val($(identifier).data('kamarruangan_id'));

      _formPendaftaran.getKarcis(_kelaspelayanan_id, true);
      $("#kelasPelayananTagihanSelected").val($(identifier).data('kelaspelayanan_id'));
      $("#kelas_ditagihkan_id").val($(identifier).data('kelaspelayanan_id')).trigger("change");

      $("#btnCariKamarTitipan").focus();
      $('#modalTempatTidurTitipan').modal('hide');
    },
    error: ({ responseJSON }) => {
      docoNotification('error', responseJSON.meta.message, '')
    },
    complete: () => {
      hideLoader()
      $.unblockUI()
    }
  })
}

function implementAsuransiData() {
  return new Promise((resolve, reject) => {
    var _groupCaraBayar = $('.selectCarabayar').find(':selected').attr('data-id');
    if (_groupCaraBayar == docoHelper.groupJaminan && $("#tipepasienform-no_asuransi").val() !== '') {
      if (_formPendaftaran.tmpAsuransi.pasien_id === null) {
        $.ajax({
          url: '/pendaftaran/end-point/get-data-asuransi',
          data: {
            penjamin_id: $("#penjamin_id").val(),
            no_asuransi: _formPendaftaran.tmpAsuransi.no_asuransi,
            onlyOneRecord: true,
            groupedByNoAsuransi: true,
          },
          success: (data) => {
            _formPendaftaran.isForeignAsuransi = false
            if (data !== null) {
              Object.assign(_formPendaftaran.tmpAsuransi, data)
              if ($("#no_rekam_medik").val() !== _formPendaftaran.tmpAsuransi.pasien.no_rekam_medik) {
                _formPendaftaran.isForeignAsuransi = true
                _formPendaftaran.isConfirmedForeignAsuransi = false
              }
              $('#asuransiform-nokartuasuransi').val(_formPendaftaran.tmpAsuransi.no_asuransi);
              $('#asuransiform-namapemilikasuransi').val(_formPendaftaran.tmpAsuransi.namapemilikasuransi);
              $('#asuransiform-nomorpokokperusahaan').val(_formPendaftaran.tmpAsuransi.nomorpokokperusahaan);
              $('#asuransiform-kelastanggungan_id').val(_formPendaftaran.tmpAsuransi.kelastanggunganasuransi_id).trigger('change'); // dropdownlist
              $('#asuransiform-namaperusahaan').val(_formPendaftaran.tmpAsuransi.namaperusahaan);
              var picker = $('#tgl_konfirmasi').pickadate('picker');
              if (_formPendaftaran.tmpAsuransi.tgl_konfirmasi) {
                picker.set('select', new Date(_formPendaftaran.tmpAsuransi.tgl_konfirmasi));
              } else {
                $('#tgl_konfirmasi').val('').trigger('change')
              }
              if (_formPendaftaran.tmpAsuransi.status_konfirmasi == '1') {
                $('#asuransiform-status_konfirmasi').attr("checked", true); // checkbox
              } else {
                $('#asuransiform-status_konfirmasi').attr("checked", false); // checkbox
              }
            } else {
              _formPendaftaran.tmpAsuransi.nokartuasuransi = $("#tipepasienform-no_asuransi").val()
              $('#asuransiform-carabayar_id').val('');
              $('#asuransiform-penjamin_id').val('');
              $('#asuransiform-pasien_id').val('');
              $('#asuransiform-nokartuasuransi').val('');
              $('#asuransiform-namapemilikasuransi').val('');
              $('#asuransiform-nomorpokokperusahaan').val('');
              $('#asuransiform-kelastanggungan_id').val(null).trigger('change'); // dropdownlist
              $('#asuransiform-namaperusahaan').val('');
              $('#tgl_konfirmasi').val('').trigger('change')
              $('#asuransiform-status_konfirmasi').attr("checked", false)
            }
          },
          complete: () => {
            resolve(true)
          }
        })
      } else {
        if ($("#no_rekam_medik").val() !== _formPendaftaran.tmpAsuransi.pasien.no_rekam_medik) {
          _formPendaftaran.isForeignAsuransi = true
          _formPendaftaran.isConfirmedForeignAsuransi = false
        }
        _formPendaftaran.tmpAsuransi.no_asuransi = _formPendaftaran.tmpAsuransi.nokartuasuransi
        $('#asuransiform-nokartuasuransi').val(_formPendaftaran.tmpAsuransi.no_asuransi);
        $('#asuransiform-namapemilikasuransi').val(_formPendaftaran.tmpAsuransi.namapemilikasuransi);
        $('#asuransiform-nomorpokokperusahaan').val(_formPendaftaran.tmpAsuransi.nomorpokokperusahaan);
        $('#asuransiform-kelastanggungan_id').val(_formPendaftaran.tmpAsuransi.kelastanggunganasuransi_id).trigger('change'); // dropdownlist
        $('#asuransiform-namaperusahaan').val(_formPendaftaran.tmpAsuransi.namaperusahaan);
        var picker = $('#tgl_konfirmasi').pickadate('picker');
        if (_formPendaftaran.tmpAsuransi.tgl_konfirmasi) {
          picker.set('select', new Date(_formPendaftaran.tmpAsuransi.tgl_konfirmasi));
        } else {
          $('#tgl_konfirmasi').val('').trigger('change')
        }
        if (_formPendaftaran.tmpAsuransi.status_konfirmasi == '1') {
          $('#asuransiform-status_konfirmasi').attr("checked", true); // checkbox
        } else {
          $('#asuransiform-status_konfirmasi').attr("checked", false); // checkbox
        }

        resolve(true)
      }
    } else {
      resolve(true)
    }
  })
}

function parseDataBayiToForm(idKelahiran) {
  getProfile()
  $("#form-pasien-content").children('.col-md-6').hide();
  $(".div-pasien").show();
  $("#tableBayi").find('.selected-row').removeClass('selected-row')
  $("#tableBayi").find(`tr[data-id="${idKelahiran}"]`).addClass('selected-row')
  $("#kelahiranIdInput").val(idKelahiran)
  const selectedBayi = dataBayi[idKelahiran]
  let goldrhby = (selectedBayi.goldarah_by !== null) ? lookGoldrh(selectedBayi.goldarah_by.toLowerCase()) : lookGoldrh(0);
  jk = selectedBayi.jeniskelamin_id_bayi
  $("#pendaftaranIbuInput").val(selectedBayi.pendaftaran_id)
  $("#frm-pasien-jeniskelamin").find(`input[value=${selectedBayi.jeniskelamin_id_bayi}]`).trigger('click')
  $("#frm-pasien-nama_ayah").val(selectedBayi.penanggungjawab_nama)
  $("#frm-pasien-no_telepon_pasien").val(selectedBayi.no_telepon_pasien)
  console.log($("#frm-pasien-jeniskelamin").find(`input[value=${selectedBayi.jeniskelamin_id_bayi}]`).trigger('click')) // set value by console log
  $("#frm-pasien-nama_pasien").val('By.Ny ' + selectedBayi.nama_ibu)
  $("#frm-pasien-nama_ibu").val(selectedBayi.nama_ibu)
  $("#frm-pasien-agama").val(selectedBayi.agama).trigger('change')
  $("#frm-pasien-namadepan").val(206).trigger('change')
  destroySelect2IfExist($("#frm-pasien-statusperkawinan"))
  $("#frm-pasien-statusperkawinan").css('pointer-events', 'none').addClass('form-control')
  $('#frm-pasien-tanggal_lahir').val(convertDateByFormat(selectedBayi.tgl_lahir_by, 'd-M-Y')).trigger('change')
  $("#frm-pasien-golongandarah").val(goldrhby).trigger('change')
  $("#frm-pasien-statusperkawinan").val(303).trigger('change')
  $("#frm-pasien-propinsi_id").val(selectedBayi.propinsi_id).trigger('change')
  $("#frm-pasien-alamat_pasien").val(selectedBayi.alamat_pasien !== null ? selectedBayi.alamat_pasien : '')
  $("#frm-pasien-rt").val(selectedBayi.rt !== null ? selectedBayi.rt : '')
  $("#frm-pasien-rw").val(selectedBayi.rw !== null ? selectedBayi.rw : '')
  setAlamat(selectedBayi.propinsi_id, selectedBayi.kabupaten_id, selectedBayi.kecamatan_id, selectedBayi.kelurahan_id)
  $("#form-pasien-content").children('.col-md-6').show()
  $("#frm-pasien-nama_pasien").focus()
  setKunjunganBbl(selectedBayi)
}

// init
$(() => {
  _formPendaftaran.init();
  $("#tipepasienform-is_bbl").bind('click', () => {
    if ($("#no_rekam_medik").val() !== '') {
      _formPendaftaran.resetForm()
    }
  })
  bindCheckboxRadio(0)
  $("#tipepasienform-is_multi_payer").focus()
});

const bindCheckboxRadio = (indexPage) => {
  $(`#steps-uid-0-p-${indexPage}`).find('input[type="checkbox"], input[type="radio"]').not('.notUniform').uniform({
    radioClass: 'choice'
  });
  bindRadioWithSpace($(`#steps-uid-0-p-${indexPage}`))
  bindCheckboxWithSpace($(`#steps-uid-0-p-${indexPage}`))
}

$(".steps-basic").steps({
  headerTag: "h6",
  bodyTag: "fieldset",
  transitionEffect: "slide",
  titleTemplate: '<span class="number">#index#</span> #title#',
  onStepChanged: function (event, currentIndex, priorIndex) {
    var _parent = $('fieldset[aria-hidden="false"]');
    _parent.find('[data-urutan="1"]').focus();
    _formPendaftaran.indexActive = currentIndex
  },
  onStepChanging: function (event, index, newIndex) {
    /**  get current li */
    var _currentLi = $('ul[role="tablist"] > li.current > a').attr("aria-controls");
    /** get content field */
    var _contentStep = $('#' + _currentLi);
    var _childContent = _contentStep.children('div');
    var _idContent = _childContent.attr("id");
    var _pasienId = $('#no_rekam_medik').val();

    /** inputan step awal */
    var _caraBayar = $('#selectCarabayar').val();
    var _asalRujukan = $('#asalrujukan_id').val();
    var penjaminId = $('#penjamin_id').val();
    var _isNew = $('input[name="chk-statuspasien"]:checked').val();

    /** Input BPJS */
    var _jenisPencarian = $('input[name="BpjsNewForm[jenis_rujukan]"]:checked').val();
    var _jenisPelayanan = $('select[name="BpjsNewForm[jenis_pelayanan]"]').val();
    var _jenisKartu = $('input[name="BpjsNewForm[jenis_kartu]"]:checked').val();
    var _noKartu = $('input[name="BpjsNewForm[no_kartu]"]').val();

    /** BPJS Rujukan */
    var _asalRujukanBpjs = $('select[name="BpjsNewForm[asal_rujukan]"]').val();
    var _noRujukan = $('input[name="BpjsNewForm[no_rujukan_f]"]').val();

    /** Group Cara Bayar */
    var _groupCaraBayar = $('.selectCarabayar').find(':selected').attr('data-id');

    let _isMultiPayer = false; //multipayer init

    if ($('input[name="TipePasienForm[is_multi_payer]"]:checked').val()) {
      _isMultiPayer = true
    }
    _isNew = (typeof _isNew != 'undefined' ? _isNew : null);
    /** Kondisi step awal jika berubah */
    /** WIP kondisi pasien asuransi lama -> umum lama -> asuransi lama */
    if (_formPendaftaran.tipePasien.carabayar_id != _caraBayar
      || _formPendaftaran.tipePasien.asalrujukan_id != _asalRujukan
      || _formPendaftaran.tipePasien.tipe_pasien != _isNew
      || _formPendaftaran.tipePasien.penjamin_id != penjaminId
      || _formPendaftaran.tipePasien.no_rekam_medik != _pasienId) {
      $.each($('ul[role="tablist"] > li:not(.first)'), function () {
        $('.steps-basic').steps("remove", 1);
      });
      $('a[href="#finish"]').trigger('click');
      return false;
    }

    /** Kondisi Untuk Cara bayar Asuransi Non BPJS */
    if (_groupCaraBayar == docoHelper.groupJaminan
      && (_formPendaftaran.tipePasien.no_asuransi != _formPendaftaran.tmpAsuransi.nokartuasuransi
        || _formPendaftaran.tmpAsuransi.prevPasien != _formPendaftaran.tmpAsuransi.pasien_id)) {
      $.each($('ul[role="tablist"] > li:not(.first)'), function () {
        $('.steps-basic').steps("remove", 1);
      });
      $('a[href="#finish"]').trigger('click');
      return false;
    }

    if (_groupCaraBayar == docoHelper.groupBPJS) {
      let condition = conditionResetStepBpjs()
      if (!condition) {
          return false;
      }
    }


    if (_isMultiPayer) {
      let _addCarabayar = $('#addSelectCarabayar2').val();
      let _addCarabayarGroup = $('#addSelectCarabayar2').find(':selected').attr('data-id');
      let _isAddPayer = false

      if (_formPendaftaran.listTmp.is_add_payer != 'undefined' && _formPendaftaran.listTmp.is_add_payer == true) {
          _isAddPayer = true
      }

      if (_formPendaftaran.listTmp.add_carabayar_id_1 != 'undefined' && _formPendaftaran.listTmp.add_carabayar_id_1 != _addCarabayar) {
          validationTipePasien()
          return false;
      }

      if (_addCarabayarGroup == docoHelper.groupJaminan
          && (_formPendaftaran.listTmp.add_no_asuransi_1 != _formPendaftaran.firstTmpAsuransi.no_asuransi)) {
          validationTipePasien()
          return false;
      }

      if (_addCarabayarGroup == docoHelper.groupBPJS) {
          let condition = conditionResetStepBpjs()
          if (!condition) {
              return false;
          }
      }

      if (_isAddPayer) {
          _addCarabayar = $('#addSelectCarabayar3').val();
          _addCarabayarGroup = $('#addSelectCarabayar3').find(':selected').attr('data-id');

          if (_formPendaftaran.listTmp.add_carabayar_id_2 != 'undefined' && _formPendaftaran.listTmp.add_carabayar_id_2 != _addCarabayar) {
              validationTipePasien()
              return false;
          }
          if (_addCarabayarGroup == docoHelper.groupJaminan
              && (_formPendaftaran.listTmp.add_no_asuransi_2 != _formPendaftaran.secondTmpAsuransi.no_asuransi)) {
              validationTipePasien()
              return false;
          }

          if (_addCarabayarGroup == docoHelper.groupBPJS) {
              let condition = conditionResetStepBpjs()
              if (!condition) {
                  return false;
              }
          }
      }
    }

    if (index < newIndex) {
      bindCheckboxRadio(newIndex)
      switch (_idContent) {
        case 'form-pasien-content':
          return _formPendaftaran.validatePasien(_contentStep);
          break;
        case 'form-rujukan-content':
          return _formPendaftaran.validateRujukan(_contentStep);
          break;
        case 'form-bpjs-error-content':
          /**  WIP */
          var _bpjsEr = $('input[name="chk-statuspasien-bpjs"]:checked').val();
          var _noRm = $('#no_rekam_medik_bpjs').val();
          var _content = $('#form-pasien-content');
          if (typeof _bpjsEr != 'undefined') {
            if ($('#no_rekam_medik_bpjs').val() != "") {
              if (_content.length) {
                $('.steps-basic').steps("remove", 2);
              }
            } else {
              docoNotification('error', 'Proses Gagal!', "No Rekam Medik Harus diisi");
              return false;
            }
          } else {
              return true;
          }
        case 'form-kunjungan-content':
          return _formPendaftaran.validKunjugan(_contentStep);
          break;
        case 'form-asuransi-content':
          return _formPendaftaran.validateAsuransi(_contentStep);
          break;
          // if (_formPendaftaran.isForeignAsuransi && !_formPendaftaran.isConfirmedForeignAsuransi) {
          //   confirmationDialog(`Data asuransi telah dipakai pada pasien ${_formPendaftaran.tmpAsuransi.pasien.nama_pasien} (NO. RM ${_formPendaftaran.tmpAsuransi.pasien.no_rekam_medik}), lanjutkan proses pendaftaran?`, (isConfirm) => {
          //     if (isConfirm) {
          //       _formPendaftaran.isConfirmedForeignAsuransi = true
          //       const asuransiResult = _formPendaftaran.validateAsuransi(_contentStep)
          //       if (asuransiResult) {
          //         $('.steps-basic').steps("next");
          //       }
          //     }
          //   })
          //   return false
          // } else if (!_formPendaftaran.isForeignAsuransi) {
          //   return _formPendaftaran.validateAsuransi(_contentStep);
          // }
          // break;
        case 'form-bpjs-content':
          return _formPendaftaran.validateBpjs(_contentStep);
          break;
        case 'form-first-asuransi-content':
            return _formPendaftaran.validateAsuransi(_contentStep, true);
            break;
        case 'form-second-asuransi-content':
            return _formPendaftaran.validateAsuransi(_contentStep, true);
            break;
        case 'form-multi-carabayar-content':
          return _formPendaftaran.validateMultiCarabayar(_contentStep);
          break;
        default:
          break;
      }
    }
    return true;
  },
  labels: {
    finish: 'Submit',
    previous: 'Kembali',
    next: 'Selanjutnya',
  },
  onFinished: function (event, currentIndex) {
    var no_rekam_medik_pasien = $("#no_rekam_medik").val();
    var no_rekam_medik_asuransi = _formPendaftaran.tmpAsuransi.no_rekam_medik;
    var _pasienId = _formPendaftaran.tmpAsuransi.pasien_id;
    var _carabayar = $("#selectCarabayar").val();
    let _isAddPayer = false

    if (_formPendaftaran.listTmp.is_add_payer != 'undefined' && _formPendaftaran.listTmp.is_add_payer == true) {
        _isAddPayer = true
    }

    if(_carabayar != 5 && _carabayar != 6) {
        if(_pasienId != null) {
            if (no_rekam_medik_pasien != no_rekam_medik_asuransi) {
                confirmationDialog(`Data asuransi telah dipakai pada pasien ${_formPendaftaran.tmpAsuransi.nama_pasien} (NO. RM ${_formPendaftaran.tmpAsuransi.no_rekam_medik}), lanjutkan proses pendaftaran?`, (isConfirm) => {
                    if (isConfirm) {
                        _formPendaftaran.tmpAsuransi.no_rekam_medik = no_rekam_medik_pasien;
                        $('.steps-basic').steps("next");
                        $('a[href="#finish"]').trigger('click');
                    }
                })
                return false;
            }
        }
    }
    var _groupCaraBayar = $('.selectCarabayar').find(':selected').attr('data-id');
    if (currentIndex == 0) {
      _formPendaftaran.generateForm();
    } else {
      var i = 0;
      var o = 0;
      var dataTarif = [];
      var _data = $('#tipe-pasien').serializeArray();
      $.each($('.check-aksi'), function () {
        if ($(this).is(':checked')) {
          var _ke = $(this).attr('data-key');
          if (typeof _formPendaftaran.listTarif[_ke] != 'undefined') {
            dataTarif.push(_formPendaftaran.listTarif[_ke].daftartindakan_id);
          }
        }
      });
      _data.push({
        name: 'list_tindakan',
        value: JSON.stringify(dataTarif)
      });
      /** Push No Asuransi */
      if (_formPendaftaran.tmpAsuransi.no_asuransi) {
        _data.push({
          name: 'no_asuransi',
          value: _formPendaftaran.tmpAsuransi.no_asuransi
        });

        _data.push({
          name: 'no_rekam_medik',
          value: _formPendaftaran.tmpAsuransi.no_rekam_medik
        });
      }

      if (_groupCaraBayar == docoHelper.groupBPJS) {
        if(!_formPendaftaran.dataBpjs.noKartu || _formPendaftaran.dataBpjs.noKartu == 'undefined') { //handle unauth
          _formPendaftaran.dataBpjs.noKartu = _formPendaftaran.tmpBpjs.no_kartu

          _data.push({
              name: 'allow_bpjs',
              value: 1,
          })
        }
        _data.push({
          name: 'is_bpjs',
          value: 1
        });
        _data.push({
            name: 'BpjsNewForm[no_kartu]',
            value: _formPendaftaran.dataBpjs.noKartu
        });
        _data.push({
          name: 'allow_notif_bpjs',
          value: _formPendaftaran.tmpBpjs.allow_notif_bpjs,
        });

        // if (typeof _formPendaftaran.dataReturnBpjs.rujukan !== 'undefined') {
        _data.push({
            name: 'BpjsNewForm[ppk_rujukan]',
            value: $('[name="BpjsNewForm[ppk_rujukan]"]').val()
        });
        // }
        

        _data.push({
            name: 'BpjsNewForm[nama_dpjp_melayani]',
            value: _formPendaftaran.tmpBpjs.dpjpServeText
        })

        _data.push({
            name: 'BpjsNewForm[kode_ppk_perujuk]',
            value: _formPendaftaran.tmpBpjs.ppkPerujukId
        })

        _data.push({
            name: 'BpjsNewForm[nama_ppk_perujuk]',
            value: _formPendaftaran.tmpBpjs.ppkPerujukNama
        })

        _data.push({
            name: 'BpjsNewForm[asal_rujukan]',
            value: $('[name="BpjsNewForm[asal_rujukan]"]').val()
        });

        _data.push({
          name: 'BpjsNewForm[jenis_peserta]',
          value: $('#jenis_peserta').val()
        });

        _data.push({
          name: 'BpjsNewForm[kode_dpjp_spri]',
          value: $('#kode_dpjp').val()
        });

        _data.push({
          name: 'BpjsNewForm[nama_dpjp_spri]',
          value:_formPendaftaran.tmpBpjs.dpjpText
        });

        _data.push({
          name: 'BpjsNewForm[info_response]',
          value: JSON.stringify(_formPendaftaran.dataReturnBpjs)
        })
      }

      _data = _data.concat({ name: 'is_ranap', value: true })
      if ($("#tipepasienform-is_bbl").is(':checked')) {
        _data = _data.concat({ name: 'is_bbl', value: true })
      }

      if ($('input[name="TipePasienForm[is_multi_payer]"]:checked').val()) {
        _data.push({
            name: 'MultiCarabayarForm[is_add_payer]',
            value: _isAddPayer
        });
      }

      _data.push({
          name: 'eligible_pasien',
          value: _formPendaftaran.eligibleAsuransi
      });

      _data.push({
          name: 'refresh_asuransi',
          value: _formPendaftaran.referensiAsuransi
      });
      const simpanPendaftaran = function (dataPost, extra = {}) {
        var attr = {
          url: `/pendaftaran/${existingPageUrl}/simpan-kunjungan?params=${_formPendaftaran.params}`,
          data: dataPost,
          success: function (data) {
            _formPendaftaran.resetForm();
            activeTable = 'kamar'
            if (_groupCaraBayar == docoHelper.groupBPJS) {
              _formPendaftaran.resetBpjs();
              var _responseData = typeof data.response != 'undefined' ? data.response : {};
              if (_responseData.is_bpjs) {
                window.open(`/pendaftaran/end-point/print-sep?pendaftaran_id=${_responseData.id}`);
              }
            }
            window.location = window.location.href.split("?")[0];
          },
          error: function (data) {
            var _res = data.responseJSON.response
            if(_formPendaftaran.tipePasien.groupcarabayar_id == docoHelper.groupBPJS) {
              if (_res.flag) {
                  setTimeout(function () {
                      var _tittle = `<b>${_res.title}</b><br>${_res.text}<br>Apakah anda yakin untuk meneruskan penyimpanan data ini ?`;
                      confirmationDialog(_tittle, function (reaction) {
                          if (reaction) {
                              var newData = dataPost
                              newData.push({
                                  name: 'allow_bpjs',
                                  value: 1,
                              })
                              simpanPendaftaran(newData, { skipConfirm: true })
                          }
                      })
                  },500)
                  return true;
              }
            }else if(_res.data){
              let errorMessages = _res.data;
              for (let field in errorMessages) {
                  if(field.includes('PjpasienForm')){
                      _formPendaftaran.indexActive= 1;
                      $('.steps-basic').steps('previous');
                      break;
                  }
              }
          
          }
          },
        };
        var mergeObj = $.extend({}, attr, extra);
        $().docoForm('click', mergeObj);
      }
      simpanPendaftaran(_data)
    }
  }
});

/** Class Form Pendaftaran */
var _formPendaftaran = {
  params: 'ranap',
  isForeignAsuransi: false,
  isConfirmedForeignAsuransi: false,
  infoPasien: null,
  historyKunjugan: null,
  formErrorBpjs: null,
  formInputKunjugan: null,
  formInputBpjs: null,
  formInputPj: null,
  formInputPasien: null,
  formInputAsuransi: null,
  formFirstInputAsuransi: null, //multi payer
  formSecondInputAsuransi: null,
  listTarif: [],
  totalTarif: 0,
  selectedPasienId: null,
  rujukRanap: {},
  tmpAsuransi: {
    pasien: {
      nama_pasien: null,
      no_rekam_medik: null,
    },
    pasien_id: null,
    no_asuransi: null,
    namapemilikasuransi: null,
    nomorpokokperusahaan: null,
    namaperusahaan: null,
    kelastanggunganasuransi_id: null,
    penjamingrade_id: null,
    prevPasien: null
  },
  firstTmpAsuransi: { // tmp asuransi multi payer
    no_rekam_medik: null,
    pasien_id: null,
    no_asuransi: null,
    namapemilikasuransi: null,
    nomorpokokperusahaan: null,
    namaperusahaan: null,
    kelastanggunganasuransi_id: null,
    penjamingrade_id: null,
    prevPasien: null,
  },
  secondTmpAsuransi: { // tmp asuransi multi payer
    no_rekam_medik: null,
    pasien_id: null,
    no_asuransi: null,
    namapemilikasuransi: null,
    nomorpokokperusahaan: null,
    namaperusahaan: null,
    kelastanggunganasuransi_id: null,
    penjamingrade_id: null,
    prevPasien: null,
  },
  eligibleAsuransi: null,
  referensiAsuransi: null,
  ignoreAsuransi: false,
  resetAsuransi: function () {
    _formPendaftaran.tmpAsuransi = {
      no_rekam_medik: null,
      pasien_id: null,
      no_asuransi: null,
      namapemilikasuransi: null,
      nomorpokokperusahaan: null,
      namaperusahaan: null,
      kelastanggunganasuransi_id: null,
      penjamingrade_id: null,
      prevPasien: null
    };
    $('.field-tipepasienform-no_asuransi span.input-group-addon').trigger('click');
  },
  tipePasien: {
    carabayar_id: null,
    penjamin_id: null,
    asalrujukan_id: null,
    groupcarabayar_id: null,
    no_rekam_medik: null,
    tipe_pasien: 1,
    no_asuransi: null,
    no_bpjs: null,
    is_rujuk: null
  },
  tmpBpjs: {
    no_kartu: null,
    asal_rujukan: null,
    jenis_pencarian: null,
    jenis_kartu: null,
    jenis_pelayanan: null,
    allow_notif_bpjs: false,
    dpjpServeText: null,
    ppkPerujukId: null,
    ppkPerujukNama: null
  },
  dataBpjs: {},
  dataReturnBpjs: {},
  dataRujukanBpjs: {},
  tmpDataPj: {},
  listTmp: {},
  resetBpjs: function () {
    _formPendaftaran.tmpBpjs = {
      no_kartu: null,
      jenis_pencarian: null,
      jenis_kartu: null,
      jenis_pelayanan: null,
      allow_notif_bpjs: false,
    };
    $("#no_kartu").val("");
    $("input[name='BpjsNewForm[jenis_rujukan]']").prop("checked", false);
    $("input[name='BpjsNewForm[jenis_kartu]']").prop("checked", false);
    /** Remove span checked */
    $("input[name='BpjsNewForm[jenis_rujukan]']").closest("span").removeClass("checked");
    $("input[name='BpjsNewForm[jenis_kartu]']").closest("span").removeClass("checked");
    $("#asal_rujukan_1").val('').trigger('change');
    var date = new Date();
    var picker = $('#tanggal_sep_1').pickadate('picker');
    picker.set('select', [[date.getFullYear(), date.getMonth() + 1, date.getDate()]]);
    $('.selectCarabayar').trigger("change");
  },
  init: function () {
    $('.nextRow').hide();
    $($(".field-tipepasienform-no_asuransi").children('.col-md-7').find('.help-block')).before('<label id="note-asuransi" style="display:none;"></label>')
    $($("#refreshAsuransiIcon").parent()).bind('click', () => {
      $('#tipepasienform-no_asuransi').attr('readonly', false)
      $('#tipepasienform-no_asuransi').val('')
      $('.field-tipepasienform-no_asuransi').find('#note-asuransi').hide
    })
    $("#tipepasienform-no_asuransi").on('change', function(obj, item) {
      var _data = obj.data;
      var _value = $(this).val();
      _formPendaftaran.ignoreAsuransi = false;
      _formPendaftaran.tipePasien.no_asuransi = "";
      _formPendaftaran.tipePasien.no_asuransi = _value;

      if(_formPendaftaran.tmpAsuransi.pasien && _formPendaftaran.tmpAsuransi.pasien.no_rekam_medik) {
        _formPendaftaran.tmpAsuransi.no_rekam_medik = _formPendaftaran.tmpAsuransi.pasien.no_rekam_medik;
      }
      if(_formPendaftaran.tmpAsuransi.pasien && _formPendaftaran.tmpAsuransi.pasien.nama_pasien) {
        _formPendaftaran.tmpAsuransi.nama_pasien = _formPendaftaran.tmpAsuransi.pasien.nama_pasien;
      }

      if (!_formPendaftaran.tmpAsuransi.pasien_id) {
          _formPendaftaran.tmpAsuransi.no_asuransi = null;
          $('.field-tipepasienform-no_asuransi').find("#note-asuransi").html("");
      } else {
          _formPendaftaran.tipePasien.no_asuransi = null;
          _formPendaftaran.tmpAsuransi.no_asuransi = _value
          $('#tipepasienform-no_asuransi').prop('readonly', false);
      }

      if(_formPendaftaran.tmpAsuransi.no_rekam_medik == null) {
          _formPendaftaran.tmpAsuransi.no_rekam_medik = $("#no_rekam_medik").val();
      }
    })
    $(document).on('change', '#multicarabayarform-add_no_asuransi_1', function (obj, item) { //multi payer
      var _data = obj.data;
      let _value = $(this).val();

      _formPendaftaran.listTmp.add_no_asuransi_1 = "";
      _formPendaftaran.listTmp.add_no_asuransi_1 = _value;

      if (!_formPendaftaran.firstTmpAsuransi.pasien_id && _formPendaftaran.firstTmpAsuransi.pasien_id != null) {
          _formPendaftaran.firstTmpAsuransi.no_asuransi = null;
          $('.field-multicarabayarform-add_no_asuransi_1').find("#first-note-asuransi").html("");
      } else {
          _formPendaftaran.listTmp.add_no_asuransi_1 = null;
          _formPendaftaran.firstTmpAsuransi.no_asuransi = _value
          $('#multicarabayarform-add_no_asuransi_1').prop('readonly', false);
      }

      if (_formPendaftaran.firstTmpAsuransi.no_rekam_medik == null) {
          _formPendaftaran.firstTmpAsuransi.no_rekam_medik = $("#no_rekam_medik").val();
      }
  });
  $(document).on('change', '#multicarabayarform-add_no_asuransi_2', function (obj, item) {
      var _data = obj.data;
      let _value = $(this).val();

      _formPendaftaran.listTmp.add_no_asuransi_2 = "";
      _formPendaftaran.listTmp.add_no_asuransi_2 = _value;

      if (!_formPendaftaran.secondTmpAsuransi.pasien_id && _formPendaftaran.secondTmpAsuransi.pasien_id != null) {
          _formPendaftaran.secondTmpAsuransi.no_asuransi = null;
          $('.field-multicarabayarform-add_no_asuransi_2').find("#second-note-asuransi").html("");
      } else {
          _formPendaftaran.listTmp.add_no_asuransi_2 = null;
          _formPendaftaran.secondTmpAsuransi.no_asuransi = _value
          $('#multicarabayarform-add_no_asuransi_2').prop('readonly', false);
      }

      if (_formPendaftaran.secondTmpAsuransi.no_rekam_medik == null) {
          _formPendaftaran.secondTmpAsuransi.no_rekam_medik = $("#no_rekam_medik").val();
      }
  });
    $(document).on('click', '.field-tipepasienform-no_asuransi span.input-group-addon', function (event) {
      event.preventDefault();
      _formPendaftaran.tipePasien.no_asuransi = null;
      _formPendaftaran.tmpAsuransi = {
        kelastanggunganasuransi_id: null,
        namapemilikasuransi: null,
        namaperusahaan: null,
        no_asuransi: null,
        nomorpokokperusahaan: null,
        pasien_id: null,
        no_rekam_medik: null,
        nama_pasien: null,
        no_rekam_medik: null
      };
      $('#tipepasienform-no_asuransi').val('');
      $('#tipepasienform-no_asuransi').prop('readonly', false);
      $('.field-tipepasienform-no_asuransi').find('#note-asuransi').html("");
    });
    $('#no_rekam_medik').select2({
      ajax: {
        url: '/pendaftaran/end-point/norm',
        dataType: 'json',
        data: function (params) {
          return {
            q: params.term,
            isRanap: 1,
            isBbl: $("#tipepasienform-is_bbl").is(':checked'),
            isMultiPayer: $("#tipepasienform-is_multi_payer").is(':checked')
          };
        }
      },
      placeholder: 'No Rm / Nama pasien / Tanggal lahir',
      minimumInputLength: 3,
      templateResult: function (noRm) {
        return noRm.text;
      },
      templateSelection: function (noRm) {
        Object.assign(existingReqData, {
          jeniskasuspenyakit_id: noRm.jeniskasuspenyakit_id,
          kelaspelayanan_id: noRm.kelaspelayanan_id,
        })
        $(".pendaftaran-id").val(noRm.pendaftaran_id);
        $("#pasien_id_hidden").val(noRm.pasien_id);
        return noRm.text;
      }
    });
    $('#no_rekam_medik').on("change", function ({ delegateTarget }) {
      if ($(delegateTarget).val() !== '') {
        _formPendaftaran.getInfoPasien('', $(delegateTarget).val())
        validasiKunjungan($(this).val())
        //Check Kunjungan
        $.ajax({
          url: '/pendaftaran/daftar/get-kunjungan?no_rekam_medik=' + $(this).val(),
          type: 'GET',
          dataType: 'JSON',
          success: function(res) {
              if(res.results != "") {
                  var response = res.results;
                  if(response !== null) {
                      if (response.status_periksa == 4 || response.status_periksa == 433 || response.status_ranap == 487){
                          docoNotification('warning', 'Perhatian!', 'Pasien Sudah Berkunjung dan Dipulangkan Dari Ruangan ' + response.ruangan_nama + '<br/>Pendaftaran Masih Bisa Tetap Dilanjutkan.')
                      } else {
                          docoNotification('warning', 'Perhatian!', 'Pasien Sudah Terdaftar di Ruangan ' + response.ruangan_nama + '<br/>Pendaftaran Masih Bisa Tetap Dilanjutkan.')
                      }
                  }
              }
          },
          error: function(err) {
              console.log("error cek kunjungan");
              console.log(err);
          }
        });
      }
    });
    _formPendaftaran.historyKunjugan = $('#kunjungan').clone(true);
    _formPendaftaran.formInputKunjugan = $('#form-kunjungan-content').clone(true);
    _formPendaftaran.formInputPj = $('#form-input-pj').clone(true);
    _formPendaftaran.formInputPasien = $('#form-input-pasien').clone(true);
    _formPendaftaran.formInputAsuransi = $('#form-input-asuransi').clone(true);
    _formPendaftaran.formInputBpjs = $('#form-input-bpjs').clone(true);
    _formPendaftaran.formErrorBpjs = $('#form-bpjs-error').clone(true);
    _formPendaftaran.formFirstInputAsuransi = $('#form-input-first-asuransi').clone(true);
    _formPendaftaran.formSecondInputAsuransi = $('#form-input-second-asuransi').clone(true);
    $('#form-kunjungan-content > .select2').select2("destroy");
    $('#form-input-pj > .select2').select2("destroy");
    $('#form-input-pasien > .select2').select2("destroy");
    $('#list-history').remove();
    $('#form-kunjungan-content').remove();
    $('#form-input-pj').remove();
    $('#form-input-pasien').remove();
    $('#form-input-asuransi').remove();
    $('#form-input-bpjs').remove();
    $('#form-bpjs-error').remove();
    $('#form-input-first-asuransi').remove();
    $('#form-input-second-asuransi').remove();
    /** Untuk BPJS */
    $('.styled, .multiselect-container input').uniform({
      radioClass: 'choice'
    });
    $('.field-tipepasienform-no_asuransi').hide();
    $('#jenis_rujukan').change(function () {
      var base = $("input:radio[name='BpjsNewForm[jenis_rujukan]']:checked").val();
      if (base == 1) {
        $('#base-rujukan').show();
        $('#base-rujukan-manual').hide();
      } else {
        $('#base-rujukan').hide();
        $('#base-rujukan-manual').show();
      }
    });
    $("#jenisKartu-1").prop('checked', true).trigger('click')
    var date = new Date();
    $('#tanggal_sep_1, #tanggal_sep, #tanggal_rujukan, #tanggal_kejadian').pickadate({
      format: 'dd mmm yyyy',
      formatSubmit: 'yyyy-mm-dd',
      max: [date.getFullYear(),date.getMonth(),date.getDate()],
      onStart: function () {
        var date = new Date();
        this.set('select', [[date.getFullYear(), date.getMonth() + 1, date.getDate()]]);
      }
    });
    $("#tanggal_sep_1").prop("readonly", false);
    $(document).on('change', '.selectCarabayar', function () {
      var carabayar = $('.selectCarabayar').val();
      var carabayar_group = $('.selectCarabayar').find(':selected').attr('data-id');
      _formPendaftaran.listTmp['carabayar_utama'] = carabayar
      _formPendaftaran.listTmp['carabayargroup_utama'] = carabayar_group
      if(typeof carabayar_group !== 'undefined') {
        stateCaraBayar(carabayar_group)
      }
      $('input[name="chk-statuspasien"]').prop("checked", true);
      $("#tipepasienform-is_bbl").focus()
      // Refresh penjamin_id
      $.ajax({
        url: '/pendaftaran/daftar/get-penjamin',
        data: {
          depdrop_parents: [
            $("#selectCarabayar").val()
          ]
        },
        method: 'POST',
        success: (res) => {
          $("#penjamin_id").select2('destroy')
          $("#penjamin_id").html('')
          res.output.map(({ id, name }) => {
            $("#penjamin_id").append(`<option value="${id}">${name}</option>`)
          })
          $("#penjamin_id").select2()
          if (existingReqData.newPasien) {
            $("#penjamin_id").val(existingReqData.penjamin_id).trigger('change')
            existingReqData.newPasien = false;
          }
          if(res.selected == penjamin_umum) {
            $("#asalrujukan_id").val(rujukan_datang_sendiri).trigger('change');
          }
        }
      })
    });
    $('#btn-edit-info-pasien').click(function(e){
      e.preventDefault();
      window.open($(this).attr('data-target'), '_blank');
      var getSession = setInterval(function() {
        if (localStorage.getItem("isPasienUpdated")) {
            localStorage.removeItem("isPasienUpdated");
            clearInterval(getSession);
            var _noRm = _formPendaftaran.tipePasien.no_rekam_medik;
            _formPendaftaran.getInfoPasien("", _noRm);
        }
      },2000);
    });
    $('#informasi-data-kunjungan').click(function (e) {
      e.preventDefault();
      if ($(this).attr('aria-expanded') == 'false') {
        _formPendaftaran.getInfoKunjungan();
      }
    });

    $(document).on('change', '#penjamin_id', function (e) {
      e.preventDefault()
      _formPendaftaran.ignoreAsuransi = false;
    })
  },
  checkEligiblePatient: function (data, tmpAsuransi) {

    if (data.no_asuransi == null || data.no_asuransi == "" || data.penjamin_id == null || data.penjamin_id == "") {
      return false
    }

    let numberAsuransi = data.no_asuransi
    let penjaminId = data.penjamin_id
    let noRekamMedik = data.no_rekam_medik

    if(tmpAsuransi.hasOwnProperty('nokartuasuransi')) { 
      if(tmpAsuransi.no_asuransi != null && tmpAsuransi.no_asuransi.length < 20) {
        numberAsuransi = tmpAsuransi.no_asuransi
      } else {
          numberAsuransi = tmpAsuransi.nokartuasuransi
      }
    }

    if (tmpAsuransi.hasOwnProperty('no_rekam_medik')) {
      noRekamMedik = tmpAsuransi.no_rekam_medik
    }

    $.ajax({
        type: 'GET',
        url: '/pendaftaran/daftar/cek-eligible-peserta',
        data: {
            no_asuransi: numberAsuransi,
            penjamin_id: penjaminId
        },
        async: true,
        beforeSend: function () {
            showLoader();
        },
        success: function (response) {
            /**
             * Handling apabila integrasi belum tersedia
             */

            if(typeof response.isIntegrasi != 'undefined') {
              isIntegrasi = response.isIntegrasi
            }

            if(isIntegrasi == false) {
                _formPendaftaran.ignoreAsuransi = true
                _formPendaftaran.generateForm()
                return false;
            }

            if(response?.response?.status !== 0) {
                docoNotification("warning", "Informasi", response?.response?.message);
                return false;
            }

            _formPendaftaran.ignoreAsuransi = false
            _formPendaftaran.eligibleAsuransi = JSON.stringify(response?.response?.data)

            $('.eligible-peserta').attr('data-width', '900px')
            $('.eligible-peserta').attr('action',`/pendaftaran/daftar/confirm-eligible-peserta?no_asuransi=${numberAsuransi}&penjamin_id=${penjaminId}&no_rm=${noRekamMedik}&is_pasienbaru=1`);
            $('.eligible-peserta').click();
        },
        error: function (response) {
          docoNotification("warning", "Proses Gagal !", response?.responseJSON.message);
        }
    });
  },
  indexActive: 0,
  generateForm: function () {
    var _caraBayar = $('#selectCarabayar').val();
    var _penjamin = $('#penjamin_id').val();
    var _asalRujukan = $('#asalrujukan_id').val();
    var _pasienId = $('#no_rekam_medik').val();
    var _groupCaraBayar = $('.selectCarabayar').find(':selected').attr('data-id');
    var _tipeId = $('input[name=chk-statuspasien]:checked').val();
    var _noAsuransi = _formPendaftaran.tmpAsuransi.no_asuransi ? _formPendaftaran.tmpAsuransi.no_asuransi : $('#tipepasienform-no_asuransi').val();
    /** Kebutuhan Untuk BPJS */
    var _bpjs = {};
    var _pencarianBpjs = $("input[name='BpjsNewForm[jenis_rujukan]']:checked").val();
    var _jenisKartu = $("input[name='BpjsNewForm[jenis_kartu]']:checked").val();
    var _tglSep = $("input[name='BpjsNewForm[tanggal_sep]']").val();
    var _jenisPelayanan = $("select[name='BpjsNewForm[jenis_pelayanan]']").val();
    var _noKartu = $("input[name='BpjsNewForm[no_kartu]']").val();
    var _asalRujukanBpjs = $("input[name='BpjsNewForm[asal_rujukan]']").val();
    /** end Bpjs */
    var isMultiPayer = false
    var _isBpjs = false

    if ($('input[name="TipePasienForm[is_multi_payer]"]:checked').val()) {
      isMultiPayer = true
    }

    _tipeId = (typeof _tipeId != 'undefined' ? _tipeId : null);
    $('#ket-bpjs').hide();
    var _data = {
      carabayar_id: _caraBayar,
      penjamin_id: _penjamin,
      asalrujukan_id: _asalRujukan,
      groupcarabayar_id: _groupCaraBayar,
      no_rekam_medik: _pasienId,
      tipe_pasien: _tipeId,
      no_asuransi: _noAsuransi,
      is_multi_payer: isMultiPayer
    };
    if (_groupCaraBayar == docoHelper.groupBPJS) {
      _bpjs = {
        is_bpjs: true,
        no_kartu: _noKartu,
        jenis_pencarian: _pencarianBpjs,
        jenis_kartu: _jenisKartu,
        jenis_pelayanan: _jenisPelayanan,
        tanggal_sep: _tglSep,
        asal_rujukan: _asalRujukanBpjs,
      }
      _isBpjs = true
      _data.asalrujukan_id = defaultAsalRujukan
      $('#asalrujukan_id').val(defaultAsalRujukan).trigger('change')
    }

    if (isMultiPayer) {
      let carabayar = $('#addSelectCarabayar2').val();
      let carabayar_group = $('#addSelectCarabayar2').find(':selected').attr('data-id');
      let add_noAsuransi_1 = _formPendaftaran.firstTmpAsuransi.no_asuransi
          ? _formPendaftaran.firstTmpAsuransi.no_asuransi : $('#multicarabayarform-add_no_asuransi_1').val();
      let add_noAsuransi_2 = _formPendaftaran.secondTmpAsuransi.no_asuransi
          ? _formPendaftaran.secondTmpAsuransi.no_asuransi : $('#multicarabayarform-add_no_asuransi_2').val();

      _formPendaftaran.listTmp.add_carabayar_id_1 = carabayar
      _formPendaftaran.listTmp[firstCbG] = carabayar_group

      if (_formPendaftaran.listTmp.is_add_payer != 'undefined' && _formPendaftaran.listTmp.is_add_payer == true) {
          carabayar = $('#addSelectCarabayar3').val();
          carabayar_group = $('#addSelectCarabayar3').find(':selected').attr('data-id');
          _formPendaftaran.listTmp.add_carabayar_id_2 = carabayar
          _formPendaftaran.listTmp[secondCbG] = carabayar_group
      }

      _formPendaftaran.listTmp.add_penjamin_id_1 = $('#add_penjamin_id_1').val()
      _formPendaftaran.listTmp.add_penjamin_id_2 = $('#add_penjamin_id_2').val()
      // _formPendaftaran.listTmp['firstAsalrujukan_id'] = $('#firstAsalrujukan_id').val()
      // _formPendaftaran.listTmp['secondAsalrujukan_id'] = $('#secondAsalrujukan_id').val()
      _formPendaftaran.listTmp.add_no_asuransi_1 = add_noAsuransi_1
      _formPendaftaran.listTmp.add_no_asuransi_2 = add_noAsuransi_2

      if (!_isBpjs) { // pengecekan carabayar bpjs
          for (const key in _formPendaftaran.listTmp) {
              if (key == firstCbG && typeof _formPendaftaran.listTmp[key] != 'undefined' && _formPendaftaran.listTmp[key] == docoHelper.groupBPJS) {
                  _isBpjs = true
              } else if (key == secondCbG && typeof _formPendaftaran.listTmp[key] != 'undefined' && _formPendaftaran.listTmp[key] == docoHelper.groupBPJS) {
                  _isBpjs = true
              }
              if (_isBpjs) {
                  _bpjs = {
                      is_bpjs: true,
                      no_kartu: _noKartu,
                      jenis_pencarian: _pencarianBpjs,
                      jenis_kartu: _jenisKartu,
                      jenis_pelayanan: _jenisPelayanan,
                      tanggal_sep: _tglSep,
                      asal_rujukan: _asalRujukanBpjs,
                      no_rujukan_f: _noRujukan,
                      asalrujukan_id: 1
                  };
              }
          }
      }
      _data.list_tmp = _formPendaftaran.listTmp
  }


    // if(!_formPendaftaran.ignoreAsuransi && $("#selectCarabayar").val() == 2) {
    //   _formPendaftaran.checkEligiblePatient(_data, _formPendaftaran.tmpAsuransi);
    //   return false;
    // }

    var _dataPost = $.extend({}, _data, _bpjs);
    $().docoForm('click', {
      url: `/pendaftaran/${existingPageUrl}/validation-tipe-pasien`,
      // url: '/pendaftaran/daftar-' + _formPendaftaran.params + '/validation-tipe-pasien',
      data: _dataPost,
      skipConfirm: true,
      skipSuccessNotif: true,
      success: function (data) {
        var _findPasien = false;
        var _noRmBpjs = null;
        _formPendaftaran.tipePasien = _data;
        _formPendaftaran.tmpAsuransi.no_asuransi = _data.no_asuransi;
        _formPendaftaran.tmpAsuransi.prevPasien = _formPendaftaran.tmpAsuransi.pasien_id;
        var _noRm = _formPendaftaran.tipePasien.no_rekam_medik;
        var _pasienId = $("#pasien_id_hidden").val();
        _pasienId = (_pasienId == "") ? null : _pasienId;
        // var _pasienId = $('#no_rekam_medik').val();

        if(isMultiPayer) {
          generateStepRi(_groupCaraBayar, _noRm, _asalRujukan, _pasienId, _findPasien, _bpjs, data, _pencarianBpjs)
          for (const property in _formPendaftaran.listTmp) {
            if (!_isBpjs) { //cek case bpjs
                if (property == firstCbG && typeof _formPendaftaran.listTmp[property] != 'undefined' && _formPendaftaran.listTmp[property] == docoHelper.groupBPJS) {
                    _isBpjs = true

                } else if (property == secondCbG && typeof _formPendaftaran.listTmp[property] != 'undefined' && _formPendaftaran.listTmp[property] == docoHelper.groupBPJS) {
                    _isBpjs = true
                }
            }

            if (_isBpjs) {
                if (property == firstCbG && typeof _formPendaftaran.listTmp[property] != 'undefined' && _formPendaftaran.listTmp[property] == docoHelper.groupBPJS) {
                    generateStepBpjsRi(_groupCaraBayar, _bpjs, _pencarianBpjs, data, _asalRujukan, _noRm)
                } else if (property == secondCbG && typeof _formPendaftaran.listTmp[property] != 'undefined' && _formPendaftaran.listTmp[property] == docoHelper.groupBPJS) {
                    generateStepBpjsRi(_groupCaraBayar, _bpjs, _pencarianBpjs, data, _asalRujukan, _noRm)
                }
            } else {
                if (property == firstCbG && typeof _formPendaftaran.listTmp[property] != 'undefined' && _formPendaftaran.listTmp[property] == docoHelper.groupJaminan) {
                    _formPendaftaran.formFirstAsuransi(_formPendaftaran.firstTmpAsuransi);

                } else if (property == secondCbG && typeof _formPendaftaran.listTmp[property] != 'undefined' && _formPendaftaran.listTmp[property] == docoHelper.groupJaminan) {
                    _formPendaftaran.formSecondAsuransi(_formPendaftaran.secondTmpAsuransi);
                }
            }
          }

          if (!_isBpjs) {
              generateFormKunjungan()
          }
        } else {
          if (_groupCaraBayar == docoHelper.groupJaminan) {
            _formPendaftaran.formAsuransi();
            if(_pasienId == null) {
              _formPendaftaran.formPasien();
            }
            // _pasienId = _formPendaftaran.tmpAsuransi.pasien_id;
            // _noRm = _pasienId;
            // _data.tipe_pasien = 1;
            $("#tipepasienform-no_asuransi").prop("readonly", false);
            // get data asuransi by no asuransi
            implementAsuransiData()
            // _formPendaftaran.formKunjungan()
          } else if (_groupCaraBayar == docoHelper.groupBPJS) {
            _formPendaftaran.tmpBpjs = _bpjs;
            let _statRujukan = false;
            _formPendaftaran.dataReturnBpjs = data.response.pasien_bpjs;
            if (typeof data.response.pasien_bpjs.peserta != 'undefined') {
                _formPendaftaran.dataBpjs = data.response.pasien_bpjs.peserta;
                if (_formPendaftaran.dataBpjs.statusPeserta.kode > 0) {
                    docoNotification('error', 'Proses BPJS Gagal !', _formPendaftaran.dataBpjs.statusPeserta.keterangan)
                    return false;
                }
            }
            if (_pencarianBpjs == 1) { // rujukan
                _formPendaftaran.dataRujukanBpjs = data.response.pasien_bpjs.rujukan;
            } else { // rujukan manual / IGD
            }
            if (typeof _formPendaftaran.dataReturnBpjs.messages != 'undefined') {
                _formPendaftaran.errorBpjs(_bpjs);
                _formPendaftaran.generateBpjs(_pencarianBpjs, _asalRujukan, _groupCaraBayar, _noRm);
                return true;
            }

            if (_formPendaftaran.infoPasien.nama_pasien.toLowerCase() !== _formPendaftaran.dataReturnBpjs.peserta.nama.toLowerCase()) {
                setTimeout(function () {
                    var _title = `<b>Peringatan !</b><br>
                    Nama Pasien yang diinputkan berbeda dengan Nama BPJS<br>
                    Nama Pasien BPJS : <strong>${_formPendaftaran.dataReturnBpjs.peserta.nama}</strong><br>
                    Pasien yang terdaftar: <strong>${_formPendaftaran.infoPasien.nama_pasien}</strong><br>
                    Apakah Anda yakin akan melanjutkan proses?`;
                    confirmationDialog(_title, function (cond) {
                        if (cond) {
                            _formPendaftaran.dataBpjs.mr.noMR = _formPendaftaran.tipePasien.no_rekam_medik;
                            _formPendaftaran.generateBpjs(_pencarianBpjs, _asalRujukan, _groupCaraBayar, _noRm);
                        }
                    })
                },100)
                return true
            }

            _formPendaftaran.formBpjs(_pencarianBpjs);
            _formPendaftaran.resetAsuransi();
            if (typeof _formPendaftaran.dataBpjs.mr != 'undefined' && _formPendaftaran.dataBpjs.mr != null) {
                _findPasien = true;
                _noRm = _formPendaftaran.dataBpjs.mr.noMR;
            } else {
                _formPendaftaran.formPasien();
            }
          } else if (_groupCaraBayar == docoHelper.groupUmum) {
            _formPendaftaran.resetAsuransi();
          }

          if (_asalRujukan != 1 && _groupCaraBayar != docoHelper.groupBPJS) {
            _formPendaftaran.formRujukan();
          }

          if ($("#tipepasienform-is_bbl").is(':checked')) {
            _formPendaftaran.formPasien();
          }

          generateFormPenanggungJawab(false)
          _formPendaftaran.formKunjungan();

          $('.steps-basic').steps("next");
        }
      }
    });
  },
  getInfoKunjungan: function () {
    let noRekamMedik = $('[name="TipePasienForm[no_rekam_medik]"]').val() != '' ? $('[name="TipePasienForm[no_rekam_medik]"]').val() : $('[name="BpjsNewForm[no_rekam_medik]"]').val();
    $.ajax({
      type: 'GET',
      url: '/pendaftaran/daftar-' + _formPendaftaran.params + '/get-kunjungan-by-no-rm?no_rekam_medik=' + noRekamMedik,
      dataType: 'JSON',
      beforeSend: function () {
        $("#loading-image").show();
        let loader = '<div class="text-center">';
        loader += '<h5><i class="icon-spinner6 spinner position-center"></i>&nbsp;&nbsp;<b>' + i18next.t("memuat") + ' . . . </b></h5>';
        loader += '</div>';
        $('#kunjungan').html(loader);
      },
      success: function (res) {
        var _response = res.response;
        var _html = "";
        var countKunjungan = _response.length;
        if (countKunjungan === 0) {
          $("#kunjungan").html('<p class="text-center">Pasien Belum Memiliki Riwayat</p>')
        } else {
          $.each(_response, function (key, data) {
            var _clone = _formPendaftaran.historyKunjugan;
            _clone.find('.history-pendaftaran-id').text(data.no_pendaftaran);
            _clone.find('.history-instalasi').text(': ' + (data.instalasi_nama ? data.instalasi_nama : '-'));
            _clone.find('.history-ruangan').text(': ' + (data.ruangan_nama ? data.ruangan_nama : '-'));
            _clone.find('.history-dokter').text(': ' + (data.nama_pegawai ? data.nama_pegawai : '-'));
            _clone.find('.history-tgl-pendaftaran').text(': ' + (data.tgl_pendaftaran ? convertDateByFormat(data.tgl_pendaftaran, 'd m Y - h:i') : '-'));
            _clone.find('.history-keluar').text(': ' + (data.tglpasienpulang ? convertDateByFormat(data.tglpasienpulang, 'd m Y - h:i') : '-'));
            _clone.find('.history-cara-keluar').text(': ' + (data.carakeluar_nama ? data.carakeluar_nama : '-'));
            _html += _clone.html();
            _clone.find('.history-penanggung-biaya').text(': ' + (data.penanggungbiaya_nama ? data.penanggungbiaya_nama : '-'));
          });
          if (countKunjungan >= 3) {
            var _action = $('.btn-detailss').attr('action');
            $('.btn-detailss').show();
            _html += '<hr><div align="center"><button type="button" class="btn btn-detailss btn-info btn-labeled btn-xs" action="/pendaftaran/daftar-igd/detail-kunjungan?no_rekam_medik=' + noRekamMedik + '" data-toggle="modal" data-target="#modal_backdrop" data-width="75%"><b><i class="fa fa-eye"></i></b>Detail Kunjungan</button></div>';
          }
          $('#kunjungan').html(_html);
        }
      },
      error: function (err) {
        console.log(err);
        $("#kunjungan").html('<p class="text-center text-danger">Terjadi kesalahan pada server</p>')
      }
    });
  },
  generateBpjs: function (_pencarianBpjs, _asalRujukan, _groupCaraBayar, _noRm) {
      _formPendaftaran.formBpjs(_pencarianBpjs);
      _formPendaftaran.resetAsuransi();
      _pasienId = ""
      _findPasien = false;

      if (typeof _formPendaftaran.dataBpjs.mr != 'undefined' && _formPendaftaran.dataBpjs.mr != null) {
          _findPasien = true;
          _noRm = _formPendaftaran.dataBpjs.mr.noMR;

      }

      if ($("#tipepasienform-is_bbl").is(':checked')) {
        _formPendaftaran.formPasien();
      }
      // if (_formPendaftaran.dataReturnBpjs.pasien_baru === true) {
      //     _formPendaftaran.formPasien();
      // }

      /** kondisi pasien baru atau pasien lama */
      if ((_formPendaftaran.tipePasien.tipe_pasien && _noRm) || _findPasien) {
          // if (Object.keys(_formPendaftaran.pendaftaranOl).length == 0) {
              // if (_formPendaftaran.dataReturnBpjs.pasien_baru === false) {
              //     _formPendaftaran.getInfoPasien(_pasienId, _noRm);
              // }
          // }
      } else {
          // if (Object.keys(_formPendaftaran.pendaftaranOl).length == 0) {
              $('#form-parent-info').hide();
              $('#form-parent').removeClass("col-md-9").addClass("col-md-12");
          // }
      }

      if (_asalRujukan != 1 && _groupCaraBayar != docoHelper.groupBPJS) {
          _formPendaftaran.formRujukan();
      }

      if ($('input[name="TipePasienForm[is_multi_payer]"]:checked').val()) {
        if (_formPendaftaran.listTmp[firstCbG] == docoHelper.groupJaminan) {
            _formPendaftaran.formFirstAsuransi(_formPendaftaran.firstTmpAsuransi);
        }

        if (_formPendaftaran.listTmp.is_add_payer != 'undefined' && _formPendaftaran.listTmp.is_add_payer == true) {
            if (_formPendaftaran.listTmp[secondCbG] == docoHelper.groupJaminan) {
                _formPendaftaran.formSecondAsuransi(_formPendaftaran.secondTmpAsuransi);
            }
        }
    }

      _formPendaftaran.formKunjungan();
      $('select').removeAttr('tabindex');
      $('.steps-basic').steps("next");

  },
  errorBpjs: function (_bpjs) {
        var _clone = _formPendaftaran.formErrorBpjs;
        /** dari BPJS */
        $('.steps-basic').steps("add", {
            title: "BPJS",
            content: _clone.html()
        });
        var _content = $('#form-bpjs-error-content');
        _content.find('span.select2').remove();
        _content.find('.select2').select2();
        _content.find('#error-bpjs-msg').html(_formPendaftaran.dataReturnBpjs.messages);
        _formPendaftaran.tmpBpjs.allow_notif_bpjs = true;
        // $('.steps-basic').steps("next");
        _formPendaftaran.tmpBpjs = _bpjs
    },
  formPasien: function (steps) {
    var _clone = _formPendaftaran.formInputPasien;
    $('.steps-basic').steps("add", {
      title: "Pasien",
      content: _clone.html()
    });
    $.ajax({
      url: '/pendaftaran/end-point/bayi-by-pasien',
      data: {
        noRekamMedik: $("#no_rekam_medik").val()
      },
      success: (res) => {
        // Append table bayi
        $("#form-pasien-content").prepend(`
          <div class="col-sm-12 table-responsive" style="margin-bottom:12px;">
            <input type="hidden" name="kelahiran_id" id="kelahiranIdInput">
            <input type="hidden" name="pendaftaran_ibu_id" id="pendaftaranIbuInput">
            <h4 class="text-center">Pilih bayi yang akan didaftarkan</h4>
            <table class="table table-hover table-row-clickable table-striped table-condensed" id="tableBayi">
              <thead>
                <tr>
                  <th>Bayi</th>
                  <th>Berat Badan (Gram)</th>
                  <th>Panjang Badan (Cm)</th>
                  <th>Jenis Kelamin</th>
                  <th>Kondisi Bayi</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        `)
        if (res.data.length > 0) {
          res.data.map((eachBayi) => {
            $("#tableBayi tbody").append(`
              <tr data-id="${eachBayi.kelahiranbayi_id}" data-registered="${eachBayi.is_registered ? '1' : '0'}" ${eachBayi.is_registered ? `class="row-registered" data-toggle="tooltip" data-placement="top" title="Bayi sudah didaftarkan dengan no pendaftaran ${eachBayi.no_pendaftaranbayi}"` : ''}>
                <td>${eachBayi.nomor_bayi}</td>
                <td>${eachBayi.berat_badan}</td>
                <td>${eachBayi.tinggi_badan}</td>
                <td>${eachBayi.jenis_kelamin === 15 ? 'Laki-Laki' : 'Perempuan'}</td>
                <td>${eachBayi.kondisi_bayi}</td>
              </tr>
            `)
            Object.assign(dataBayi, {
              [eachBayi.kelahiranbayi_id]: eachBayi
            })
          })
        } else {
          dataBayi = {}
        }
        $("#form-pasien-content").children('.col-md-6').hide();
        $(".div-pasien").hide();
        $("#tableBayi tbody tr").bind('click', ({ delegateTarget }) => {
          // parsing data bayi to form
          if ($(delegateTarget).data('registered') === 0) {
            parseDataBayiToForm($(delegateTarget).data('id'))
          }
        })
        /** Event after generate form */
        var _content = $('#form-pasien-content');
        _content.find('span.select2').remove();
        _content.find('.select2').select2();
        _content.find('input').attr("autocomplete", "off");
        renderPickadate($('#frm-pasien-tanggal_lahir'), {
          dependElementPicker: $('#btn_addon_tgllahir'),
          defaultValue: Date()
        });

        // -- pasien baru
        $(document).on('change', '#frm-pasien-tanggal_lahir', function () {
          var umur = '';
          if ($(this).val() != '') {
            const splitDate = $(this).val().split('-')

            var myDate = new Date(splitDate[2], splitDate[1] - 1, splitDate[0]);
            var today = new Date();
            if (myDate > today) {
              $(this).pickadate('picker').set('select', new Date())
              return true
            }
            umur = generateUmur($(this).val());
          }

          $('#frm-pasien-umur').val(umur);
          $("input[name='PasienForm[tanggal_lahir]_submit']").val($('#frm-pasien-tanggal_lahir').val());
        });
        $('#frm-pasien-no_identitas_pasien').on('blur change', function () {
          if ($(this).val().length < 16) {
            return false;
          }
          function padZero(num) {
            str = num;
            return str < 10 ? "0" + num : num;
          }
          var kodeProv = $(this).val().substring(0, 2);
          var kodeKab = $(this).val().substring(2, 4);
          var kodeKec = $(this).val().substring(4, 6);

          var jk = 15;
          /** Bulan */
          if (parseInt($(this).val().substring(8, 10)) > 12 || parseInt($(this).val().substring(6, 8)) > 31) {
            jk = 16;
          }
          var day_date_s = parseInt($(this).val().substring(6, 8)) > 31
            ? parseInt($(this).val().substring(6, 8)) - 40 : parseInt($(this).val().substring(6, 8));
          var day_date = padZero(day_date_s);
          var month_date = parseInt($(this).val().substring(8, 10)) > 12
            ? parseInt($(this).val().substring(8, 10)) - 40 : parseInt($(this).val().substring(8, 10));
          month_date = padZero(month_date);
          var year_date_s = $(this).val().substring(10, 12);
          var year_date = year_date_s > 24 ? '19' + year_date_s : '20' + year_date_s;
          var tglLahir = day_date + '-' + month_date + '-' + year_date;
          if ($('#frm-pasien-jenisidentitas').val() == '94') {
            // set propinsi
            $('#frm-pasien-propinsi_id option').each(function (id, el) {
              if (String($(el).data('kode')) == kodeProv) {
                // $(el).attr('selected', true);
                $('#frm-pasien-propinsi_id').val($(el).attr('value')).trigger('change').trigger('depdrop:change');
              }
            })
            // set jenis kelamin
            $("input[name='PasienForm[jeniskelamin]'][value=" + jk + "]").prop('checked', true).trigger('change');
            $('#frm-pasien-tanggal_lahir').val(tglLahir).trigger('change');
          }
        });
        $('#frm-pasien-namadepan').val(206).trigger('change')
        $('#frm-pasien-tanggal_lahir').trigger('change')

        // $("#frm-pasien-propinsi_id").val('').trigger('change')
        $("#frm-pasien-kabupaten_id").prop('disabled', true)
        $("#frm-pasien-kecamatan_id").prop('disabled', true)
        $("#frm-pasien-kelurahan_id").prop('disabled', true)
        $('.field-frm-pasien-statusperkawinan').hide()
        $('.field-frm-pasien-warga_negara').hide()
        $('.field-frm-pasien-suku_id').hide()
        $('.field-frm-pasien-pekerjaan_id').hide()
        $('.field-frm-pasien-pendidikan_id').hide()

        // bindingRegion('/pendaftaran/end-point/region-list', {
        //   province: $("#frm-pasien-propinsi_id"),
        //   city: $("#frm-pasien-kabupaten_id"),
        //   district: $("#frm-pasien-kecamatan_id"),
        //   village: $("#frm-pasien-kelurahan_id"),
        // })
      }
    })
    generateFormPenanggungJawab()
  },
  formRujukan: function () {
    var _clone = $("#form-rujukan").clone();
    _clone.find('input').attr("autocomplete", "off");
    $('.steps-basic').steps("add", {
      title: "Rujukan",
      content: _clone.html()
    });
    let dataRujukanDari = []
    if (typeof _rujukanDari[_formPendaftaran.tipePasien.asalrujukan_id] != "undefined") {
      _rujukanDari[_formPendaftaran.tipePasien.asalrujukan_id].map((item) => {
        dataRujukanDari.push({
          id: item.id,
          text: item.value
        })
      })
    }

    $('#rujukanform-rujukandari_id').select2({
      data: dataRujukanDari,
    });

    // if (dataRujukanDari.length <= 1 && dataRujukanDari.length >= 1) {
    //   $('#rujukanform-rujukandari_id').val(1).trigger('change');
    // }
    // refreshOptionSelect2($('#rujukanform-rujukandari_id'), dataRujukanDari)
    $("#rujukanform-diagnosa_id").select2({
      placeholder: "Pilih Diagnosa",
      minimumInputLength: 3,
      ajax: {
        url: "/pendaftaran/daftar-igd/get-diagnosa?type=10",
        dataType: "json",
        quietMillis: 250,
        data: function (params) {
          var query = {
            search: params.term
          }

          return query;
        },
      },
    });

    renderPickadate($('input[name="RujukanForm[tanggal_rujukan]"]'), {
      dependElementPicker: $('.btn_addon_tglrujuk').parent(),
      defaultValue: new Date()
    })
  },
  formAsuransi: function () {
    var _clone = _formPendaftaran.formInputAsuransi;
    $('.steps-basic').steps("add", {
      title: "Asuransi",
      content: _clone.html()
    });
    /** Re init */
    var _content = $('#form-asuransi-content');


    renderPickadate($(_content.find('#tgl_konfirmasi')), {
      dependElementPicker: _content.find('#btn_addon_tgllahir').parent(),
      defaultValue: typeof _formPendaftaran.tmpAsuransi.tgl_konfirmasi !== 'undefined' && _formPendaftaran.tmpAsuransi.tgl_konfirmasi !== null ? _formPendaftaran.tmpAsuransi.tgl_konfirmasi : new Date()
    });
    $('#asuransiform-kelastanggungan_id').select2();
    $('#asuransiform-kelastanggungan_id').on('select2:close', ({ delegateTarget }) => {
      $(delegateTarget).focus()
    })
    _content.find('input').attr("autocomplete", "off");
    _content.find('#asuransiform-namapemilikasuransi').val(_formPendaftaran.tmpAsuransi.namapemilikasuransi);
    _content.find('#asuransiform-nomorpokokperusahaan').val(_formPendaftaran.tmpAsuransi.nomorpokokperusahaan);
    _content.find('#asuransiform-kelastanggungan_id')
      .val(_formPendaftaran.tmpAsuransi.kelastanggunganasuransi_id)
      .trigger('change');
    _content.find('#asuransiform-namaperusahaan')
      .val(_formPendaftaran.tmpAsuransi.namaperusahaan);
    $('.field-asuransiform-kelastanggungan_id').attr("style", "display: none")
    $("#asuransiform-penjamingrade_id").select2({
        placeholder: "-",
        ajax: {
            url: "/pendaftaran/end-point/get-grade-penjamin",
            dataType: "json",
            quietMillis: 250,
            data: function (params) {
                let query = {
                    search: params.term,
                    penjamin_id: _formPendaftaran.tipePasien.penjamin_id,
                }

                return query;
            },
        },
    });
    $("#asuransiform-benefit_id").select2({
      placeholder: "-",
      ajax: {
          url: "/pendaftaran/daftar/get-referensi-benefit",
          dataType: "json",
          quietMillis: 250,
          data: function (params) {
              let query = {
                  search: params.term,
                  penjamin_id: _formPendaftaran.tipePasien.penjamin_id,
                  no_asuransi: _formPendaftaran.tmpAsuransi.no_asuransi
              }

              return query;
          },
      },
    }).on('select2:select', function (e) {
        _formPendaftaran.referensiAsuransi = e.params.data?.id            
    });

    if(isIntegrasi) {
      $('.field-asuransiform-benefit_id').attr("style", "display: block")
    }
    else {
        $('.field-asuransiform-benefit_id').attr("style", "display: none")
    }
  },
  formFirstAsuransi: function (data) { //multi payer
    var _clone = _formPendaftaran.formFirstInputAsuransi;
    $('.steps-basic').steps("add", {
        title: "Asuransi",
        content: _clone.html()
    });
    /** Re init */
    var _content = $('#form-first-asuransi-content');
    renderPickadate($(_content.find('#add_tgl_konfirmasi_1')), {
        dependElementPicker: _content.find('#btn_addon_add_tgl_konfirmasi_1').parent(),
        defaultValue: typeof data.tgl_konfirmasi !== 'undefined' && data.tgl_konfirmasi !== null ? data.tgl_konfirmasi : new Date()
    });

    $('#multicarabayarform-add_kelastanggungan_id_1').select2();
    $('#multicarabayarform-add_kelastanggungan_id_1').on('select2:close', ({ delegateTarget }) => {
        $(delegateTarget).focus()
    })
    $('.field-multicarabayarform-add_namapemilikasuransi_1').addClass('required');
    _content.find('input').attr("autocomplete", "off");
    _content.find('#multicarabayarform-add_namapemilikasuransi_1').val(data.namapemilikasuransi);
    _content.find('#multicarabayarform-add_nomorpokokperusahaan_1').val(data.nomorpokokperusahaan);
    _content.find('#multicarabayarform-add_kelastanggungan_id_1')
        .val(data.kelastanggunganasuransi_id)
        .trigger('change');
    _content.find('#multicarabayarform-add_namaperusahaan_1')
        .val(data.namaperusahaan);
    _content.find('#multicarabayarform-add_asuransipasien_id_1')
        .val(data.asuransipasien_id);
    $("#multicarabayarform-add_penjamingrade_id_1").select2({
        placeholder: "-",
        ajax: {
            url: "/pendaftaran/end-point/get-grade-penjamin",
            dataType: "json",
            quietMillis: 250,
            data: function (params) {
                let query = {
                    search: params.term,
                    penjamin_id: $('#add_penjamin_id_1').val()
                }

                return query;
            },
        },
    });
  },
  formSecondAsuransi: function (data) { //multi payer
      var _clone = _formPendaftaran.formSecondInputAsuransi;
      $('.steps-basic').steps("add", {
          title: "Asuransi",
          content: _clone.html()
      });
      /** Re init */
      var _content = $('#form-second-asuransi-content');
      renderPickadate($(_content.find('#add_tgl_konfirmasi_2')), {
          dependElementPicker: _content.find('#btn_addon_add_tgl_konfirmasi_2').parent(),
          defaultValue: typeof data.tgl_konfirmasi !== 'undefined' && data.tgl_konfirmasi !== null ? data.tgl_konfirmasi : new Date()
      });

      $('#multicarabayarform-add_kelastanggungan_id_2').select2();
      $('#multicarabayarform-add_kelastanggungan_id_2').on('select2:close', ({ delegateTarget }) => {
          $(delegateTarget).focus()
      })
      $('.field-multicarabayarform-add_namapemilikasuransi_2').addClass('required');
      _content.find('input').attr("autocomplete", "off");
      _content.find('#multicarabayarform-add_namapemilikasuransi_2').val(data.namapemilikasuransi);
      _content.find('#multicarabayarform-add_nomorpokokperusahaan_2').val(data.nomorpokokperusahaan);
      _content.find('#multicarabayarform-add_kelastanggungan_id_2')
          .val(data.kelastanggunganasuransi_id)
          .trigger('change');
      _content.find('#multicarabayarform-add_namaperusahaan_2')
          .val(data.namaperusahaan);
      _content.find('#multicarabayarform-add_asuransipasien_id_2')
          .val(data.asuransipasien_id);
      $("#multicarabayarform-add_penjamingrade_id_2").select2({
          placeholder: "-",
          ajax: {
              url: "/pendaftaran/end-point/get-grade-penjamin",
              dataType: "json",
              quietMillis: 250,
              data: function (params) {
                  let query = {
                      search: params.term,
                      penjamin_id: $('#add_penjamin_id_2').val()
                  }

                  return query;
              },
          },
      });
  },
  validateAsuransi: function (object, _isMultiPayer = false) {
    var _data = object.serializeArray();
    _data.push({
      name: 'isIntegrasi',
      value: isIntegrasi
    });
    
    var _result = false;
    $().docoForm('click', {
      url: `/pendaftaran/${existingPageUrl}/validation-asuransi?isMultiPayer=${_isMultiPayer}`,
      data: _data,
      skipConfirm: true,
      skipSuccessNotif: true,
      async: false,
      success: function (data) {
        _result = true;
      }
    });
    return _result;
  },
  validateBpjs: function (object) {
    // Skip tujuan kunjungan
    $('#is_tujuan_kunj').val(tujuanKunjTrue);

    var _data = object.serializeArray();
    _data.push({
      name: 'no_kartu',
      value: _formPendaftaran.dataBpjs.noKartu
    },{
      name: 'scenario',
      value: bpjsScenario
    },{
      name: 'BpjsNewForm[asal_rujukan]',
      value: $('[name="BpjsNewForm[asal_rujukan]"]').val()
    },{
      name: 'BpjsNewForm[ppk_rujukan]',
      value: $('[name="BpjsNewForm[ppk_rujukan]"]').val()
    });
    var _result = false;
    $().docoForm('click', {
      url: `/pendaftaran/${existingPageUrl}/validation-bpjs`,
      data: _data,
      skipConfirm: true,
      skipSuccessNotif: true,
      async: false,
      success: function (data) {
        _result = true;
      }
    });
    return _result;
  },
  formBpjs: function () {
    var _clone = _formPendaftaran.formInputBpjs;
    var peserta = _formPendaftaran.dataBpjs;
    var getRoom = _formPendaftaran.params;
    var post_ranap = _formPendaftaran.dataReturnBpjs.post_ranap;

    if (Object.keys(_formPendaftaran.dataBpjs).length) {
      $('#ket-bpjs').show();
      $('.steps-basic').steps("add", {
        title: "Bpjs",
        content: _clone.html()
      });
      var _content = $('#form-bpjs-content');
      _content.find('.select2Bpjs').select2();
      _content.find('input').attr("autocomplete", "off");
      _content.append(`<input type="hidden" name="bpjsKelas" value=${peserta.hakKelas.kode}>`)
      if (getRoom === 'ranap') {
        _content.find('.form-poli_tujuan').hide()
      }
      $(".select2AsalRujukan").select2();
      $(".select2KasusKecelakaan").select2();
      $("#asal_rujukan").on("change", function(){
          $("#ppk_rujukan").val(null).trigger("change");
      });
      $("#ppk_rujukan").select2({
        placeholder: "PPK Rujukan",
        minimumInputLength: 3,
        ajax: {
          url: "/api/bpjs/referensi-faskes-new",
          dataType: "json",
          quietMillis: 250,
          data: function (params) {
            var query = {
              search: params.term,
              asal_rujukan: $('#asal_rujukan').val(),
              type: 'public'
            }

            return query;
          },
        },
        templateSelection: function (res) {
          _formPendaftaran.tmpBpjs.ppkPerujukId = res.id
          _formPendaftaran.tmpBpjs.ppkPerujukNama = res.text
          return res.text;
        }
      });

      $("#diagnosa_awal").select2({
        placeholder: "Pilih Diagnosa Awal",
        minimumInputLength: 3,
        ajax: {
          url: "/api/bpjs/referensi-diagnosa-new",
          dataType: "json",
          quietMillis: 250,
          data: function (params) {
            var query = {
              search: params.term,
              type: 'public'
            }

            return query;
          },
        },
      });

      bindingRegionBpjs({
        province: $("#kode_provinsi"),
        city: $("#kode_kabupaten"),
        district: $("#kode_kecamatan")
      })

      $(".select2Dpjp").select2({
        placeholder: "Pilih Dokter DPJP",
        ajax: {
          url: "/api/bpjs/referensi-dpjp",
          dataType: "json",
          quietMillis: 250,
          data: function (params) {
            var query = {
              search: params.term,
              type: 'public',
              cacheId: 'dpjp-melayani'
            }

            return query;
          },
        },
        templateSelection: function (res) {
          _formPendaftaran.tmpBpjs.dpjpText = res.text
          return res.text;
        }
      });

      // $(".select2DpjpServe").select2({
      //   placeholder: "Pilih Dokter DPJP Melayani",
      //   ajax: {
      //       url: "/api/bpjs/referensi-dpjp",
      //       dataType: "json",
      //       quietMillis: 250,
      //       data: function(params) {
      //           var query = {
      //               search: params.term,
      //               type: 'public',
      //               cacheId: 'dpjp-melayani',
      //           }

      //           return query;
      //       },
      //   },
      // });

      $(".dpjp_form_melayani").hide()

      $(".select2KelasRawat").select2({
        placeholder: "Pilih Kelas Rawat",
        ajax: {
          url: "/api/bpjs/referensi-kelas-rawat",
          dataType: "json",
          quietMillis: 250,
          data: function (params) {
            var query = {
              search: params.term,
              type: 'public'
            }

            return query;
          },
        },
      });

      $(".select2Poli").select2({
        placeholder: "Pilih Poli Tujuan",
        minimumInputLength: 3,
        ajax: {
          url: "/api/bpjs/referensi-poli-new",
          dataType: "json",
          quietMillis: 250,
          data: function (params) {
            var query = {
              search: params.term,
              type: 'public'
            }

            return query;
          },
        },
      });

      renderPickadate($('#tanggal_sep_1'), {
        dependElementPicker: $('#tanggal_sep_1').parent().find('.input-group-addon'),
        lowerThanToday: true,
        defaultValue: new Date()
      })
      renderPickadate($('#tanggal_sep'), {
        dependElementPicker: $('#tanggal_sep').parent().find('.input-group-addon'),
        lowerThanToday: true,
        defaultValue: new Date(),
        defaultValue: $("#tanggal_sep_1").pickadate().val(),
        readOnly: true
      })
      renderPickadate($('#tanggal_rujukan'), {
        dependElementPicker: $('#tanggal_rujukan').parent().find('.input-group-addon'),
        lowerThanToday: true,
        defaultValue: new Date()
      })
      renderPickadate($('#tanggal_kejadian'), {
        dependElementPicker: $('#tanggal_kejadian').parent().find('.input-group-addon'),
        lowerThanToday: true,
        defaultValue: new Date()
      })
      $("#tanggal_sep").attr("readonly", true);
      $("select").on('select2:close', ({ delegateTarget }) => {
        $(delegateTarget).focus()
      })
      $('#asal_rujukan').on('change', function (e) {
          e.preventDefault();
          var _valAsalRujukan = $(this).val()
          if (_valAsalRujukan == 2) {
            setTimeout(function() {
              $("#no_surat_kontrol").val('');
              $(".dpjp_form").show();
            }, 10);

            return false;
          }
           $('.dpjp_form').hide();
           $('#no_surat_kontrol').val(null);
           $('#kode_dpjp').val(null).trigger('change');
      })

      $('#kasus_kecelakaan').change(function () {
        var base = $(this).val();
        if (base == 0) {
          $('.kasus_kecelakaan_form').hide();
          $('#status_suplesi').val('0');
        } else if (base == 3) {
          $('.kasus_kecelakaan_form').show();
          $('.suplesi_form').hide();
          $('#status_suplesi').val('0');
        } else {

          swal({
            title: "Perhatian!",
            text: "Apakah ini merupakan kasus kecelakaan lalu lintas baru?",
            type: "info",
            showCancelButton: true,
            cancelButtonText: "Tidak",
            confirmButtonText: "Ya",
          }, function (i) {
            if (i) {
              $('.kasus_kecelakaan_form').show();
              $('.suplesi_form').hide();
              $("#tanggal_kejadian").focus()
              $('#status_suplesi').val('0');
            } else {
              $('.suplesi_form').show();
              $('.kasus_kecelakaan_form').hide();
              $('#status_suplesi').val('1');

              $('#button-list-sep').click();
            }
          });
        }
      });

      setTimeout(function() {
            $("#no_surat_kontrol").val('');
            $(".dpjp_form").hide();
          }, 10);

      $('#no_surat_kontrol').on('change', function () {
          if($(this).val() == '') {
            return;
          }
          let url = window.location.origin + '/api/bpjs/cari-surat-kontrol?no_surat_kontrol=' + $(this).val();
          $.ajax({
              type: "GET",
              url: url,
              dataType: "JSON",
              success: function (res) {
                  console.log(res)

                  let responsebpjs = res.response;
                  $("#option_dpjp").remove();
                  $("#option_dpjp_pemberi").remove();

                  if (responsebpjs.kodeDokter != null) {
                      let option_dpjp = '<option id="option_dpjp" value="'+responsebpjs.kodeDokter+'" selected="">'+responsebpjs.namaDokter+'</option>'
                      $("#kode_dpjp").append(option_dpjp);
                      $('#kode_dpjp').val(responsebpjs.kodeDokter).trigger('change');
                  }
              },
              error: function(res) {
                  docoNotification("warning", "Peringatan!", res.responseJSON.response.text);
              }
          });
      })
      $('#bpjsnew_detail_nik').html('<strong>:</strong> ' + peserta.nik);
      $('#bpjsnew_detail_tgl_lahir').html('<strong>:</strong> ' + peserta.tglLahir);
      $('#bpjsnew_detail_jenis_peserta').html('<strong>:</strong> ' + peserta.jenisPeserta.keterangan);
      $('#bpjsnew_detail_hak_kelas').html('<strong>:</strong> ' + peserta.hakKelas.keterangan);
      $('.btn-detail-bpjs').attr('action', `/pendaftaran/daftar-igd/detail-history-bpjs?no_kartu=${peserta.noKartu}`)

      $("[name='BpjsNewForm[asal_rujukan]']").val(2).change();
      $("[name='BpjsNewForm[asal_rujukan]']").prop('disabled', true);

      let ppkRujukan = _formPendaftaran.dataReturnBpjs.ppkPelayanan;

      $("#ppk_rujukan").select2({
          data : [{
              id : ppkRujukan.kode,
              text : ppkRujukan.nama,
          }]
      });
      $("#ppk_rujukan").val(ppkRujukan.kode).trigger('change');
      $("#ppk_rujukan").prop('disabled', true);
      $("input[name='BpjsNewForm[no_rekam_medik]']").val(peserta.mr.noMR);
      $("#no_asuransi").val(peserta.noKartu);
      setKelasRawat(peserta.hakKelas.kode);
      var tmt = peserta.tglTMT;
      var tat = peserta.tglTAT;
      $('#bpjsnew_detail_tmt_tat').html('<strong>:</strong> ' + tmt + ' - ' + tat);
      var kdProv = peserta.provUmum.kdProvider;
      var nmProv = peserta.provUmum.nmProvider;
      $('#bpjsnew_detail_ppk_rujukan').html('<strong>:</strong> ' + kdProv + " - " + nmProv);
      var statusPeserta = peserta.statusPeserta.keterangan;
      $('#bpjsnew_detail_status_peserta').html('<strong>:</strong> ' + statusPeserta);

      var tglSep = $("input[name='BpjsNewForm[tanggal_sep]_submit']").val();
      $('#button-list-sep').attr('href', '/pendaftaran/daftar/list-sep?no_kartu=' + peserta.noKartu + '&tgl_sep=' + tglSep);
      $("#no_telp").val(peserta.mr.noTelepon);

      if(peserta.informasi.prolanisPRB != null) {
        $('#group_prb').show();
        $('#content_prb').html(peserta.informasi.prolanisPRB);
      }
    }
  },
  validatePasien: function (object) {
    var _data = object.serializeArray();
    var _result = false;
    $().docoForm('click', {
      url: `/pendaftaran/${existingPageUrl}/validation-pasien?is_bbl=true`,
      data: _data,
      skipConfirm: true,
      skipSuccessNotif: true,
      async: false,
      success: function (data) {
        _result = true;
      },
      error: ({ responseJSON }) => {
        if (typeof responseJSON !== 'undefined' && typeof responseJSON.meta !== 'undefined' && typeof responseJSON.meta.message !== 'undefined') {
          docoNotification('error', responseJSON.meta.message, '')
        }
      }
    });
    return _result;
  },
  validateRujukan: function (object) {
    var _data = object.serializeArray();
    _data.push({
      name: 'asalrujukan_id',
      value: _formPendaftaran.tipePasien.asalrujukan_id
    });
    var _result = false;
    $().docoForm('click', {
      url: `/pendaftaran/${existingPageUrl}/validation-rujukan`,
      data: _data,
      skipConfirm: true,
      skipSuccessNotif: true,
      async: false,
      success: function (data) {
        _result = true;
      }
    });
    return _result;
  },
  formKunjungan: function () {
    var _clone = _formPendaftaran.formInputKunjugan;
    var _clonePj = _formPendaftaran.formInputPj;
    $('.steps-basic').steps("add", {
      title: "Kunjungan",
      content: `<div class="row" id="form-kunjungan-content">${_clone.html()}</div>`
    });

    // $('#kunjunganform-is_pj').on("change", function () {
    //   if (this.checked) {
    //     $('.steps-basic').steps("add", {
    //       title: "Penanggung Jawab",
    //       content: _clonePj.html()
    //     });
    //     /** Re init */
    //     var _content = $('#form-pj-content');
    //     $("#pjpasienform-pj_jenis_identitas,#pjpasienform-pj_hubungan,#pjpasienform-pj_pengantar").select2()
    //     var $input_date = $('#pjpasienform-pj_tanggal_lahir').pickadate({
    //       editable: true,
    //       format: 'dd-mm-yyyy',
    //       formatSubmit: 'dd-mm-yyyy',
    //       selectMonths: true,
    //       selectYears: true,
    //       min: [1900, 01, 01],
    //       max: true,
    //       onClose: function () {
    //         $('.datepicker').focus();
    //       }
    //     });
    //     var picker_date = $input_date.pickadate('picker');
    //     $('#pj-date').parent().on('click', function (event) {
    //       if (picker_date.get('open')) {
    //         picker_date.close();
    //       } else {
    //         picker_date.open();
    //       }
    //       event.stopPropagation();
    //     });

    //     $('#pjpasienform-pj_tanggal_lahir').on('change', function () {
    //       var umur = '';
    //       if ($(this).val() != '') {
    //         const splitDate = $(this).val().split('-')
    //         const date = `${splitDate[2]}-${splitDate[1]}-${splitDate[0]}`

    //         var myDate = new Date(date);
    //         var today = new Date();
    //         if (myDate > today) {
    //           $(this).pickadate('picker').set('select', new Date())
    //           return true
    //         }
    //         umur = generateUmur($(this).val());
    //       }
    //       $('.umurtext').val(umur);

    //     })
    //   } else {
    //     var _totalStep = $('ul[role="tablist"] > li').length;
    //     $('.steps-basic').steps("remove", _totalStep - 1);
    //   }
    // });

    $("#pasienadmisiform-is_pasientitipan").on("change", function () {
      if (this.checked) {
        $(".kelas_ditagihkan").removeClass("hidden");
      } else {
        $(".kelas_ditagihkan").addClass("hidden");
        $("#kelas_ditagihkan_id").val("").trigger('change');
        $("#ruangan_titipan_id").val("");
        $("#ruanganTitipanLabelValue").text("");
        _formPendaftaran.getKarcis($("#kelaspelayanan_id").val());
      }
    });

    /** Event after generate form */
    $('.styled, .multiselect-container input').uniform({
      radioClass: 'choice'
    });
    /** Re init */
    var _content = $('#form-kunjungan-content');
    $("#jeniskasuspenyakit_id,#kelaspelayanan_id,#kelas_ditagihkan_id").select2()
    $("#referal").select2();
    $("#kelaspelayanan_id").bind('change', () => {
      refreshOptionSelect2($('#pegawai_id'), [])
      $('#pegawai_id').prop('disabled', true)
      // $(document).find('#kamartempattidur_id').val('');
      // $(document).find('#kamarruangan_id').val('');
      // $(document).find('#nokamar').val('');
      // $(document).find('#ruanganLabelValue').html('');
      // $(document).find('#ruanganIdHidden').val('');
      // $(document).find('#ruanganTitipanIdHidden').val('');
      // $("#tableKarcisWrapper tfoot").html('')
      $("#tbl-karcis_info").remove()
      $("#tableKarcisWrapper tbody").html(`
        <tr>
          <td colspan="5" class="text-center"><?=Yii::t('fe','Data tidak tersedia')?></td>
        </tr>
      `)
      // _formPendaftaran.getKarcis($(identifier).data('kelaspelayanan_id'))
      $("#kelasPelayananSelected").val('')
    })
    $("#jeniskasuspenyakit_id,#kelaspelayanan_id").bind('change', () => {
      $("#kamarTitipanCheck").prop('checked', false);
      $("#isApsCheck").prop('checked', false)
      $("#filterHeader").html('');
      activeTable = 'kamar';

      if ($("#jeniskasuspenyakit_id").val()) {
        existingReqData.jeniskasuspenyakit_id = $("#jeniskasuspenyakit_id").val();
      }

      if ($("#kelaspelayanan_id").val()) {
        existingReqData.kelaspelayanan_id = $("#kelaspelayanan_id").val();
      }
    })
    if ($("#tipepasienform-is_bbl").is(':checked')) {
      $("#jeniskasuspenyakit_id").val(null).trigger('change')
    } else {
      $("#jeniskasuspenyakit_id").val(existingReqData.jeniskasuspenyakit_id).trigger('change')
    }
    $("#kelaspelayanan_id").val(existingReqData.kelaspelayanan_id).trigger('change')

    $("#datetime").AnyTime_noPicker();
    $("#datetime").AnyTime_picker({
      format: "%d-%m-%Y %H:%i",
    });
    $("#datetime").val(function () {
      var d = new Date();
      return ("0" + d.getDate()).slice(-2) + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + d.getFullYear() + " " + ("0" + d.getHours()).slice(-2) + ":" + ("0" + d.getMinutes()).slice(-2);;
    });
    // Add event when render kunjungan form
    $("#tableKarcisWrapper").html(`
      <table id="tbl-karcis" class="table table-striped table-condensed table-hover table-karcis" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th width="10%">No</th>
                <th class="karcis-title"><?=\Yii::t("fe", "Karcis");?></th>
                <th class="harga-title"><?=\Yii::t("fe", "Harga");?></th>
                <th><?=\Yii::t("fe", "Aksi");?></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="5" class="text-center"><?=Yii::t('fe','Data tidak tersedia')?></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" style="text-align:left">Total:</th>
                <th colspan="2">Rp. 0</th>
            </tr>
        </tfoot>
      </table>
    `)
    $('.karcis-title').text(title_karcis.replace(/['"]+/g, ''))
    $("#is_booked").bind('change', function (e) {
      e.preventDefault();
      if (this.checked) {
        $('.btn-caribooking').prop('disabled', false);
      } else {
        $('.btn-caribooking').prop('disabled', true);
        $('#bookingkamar_no').val('');
        $('#bookingkamar_id').val('');
      }
    });

    $("#btnCariKamar").bind('click', () => {
      var is_pasientitipan = $("#kamarTitipanCheck");
      var is_pasienaps = $("#isApsCheck");
      var jenis_id = $('#jeniskasuspenyakit_id').val();
      var kelas_id = $('#kelaspelayanan_id').val();
      var ruangan_id = $('#ruangan_id').val();
      var carabayar_id = $("#selectCarabayar").val();
      var jenisKamar = 'semua';

      if(is_pasientitipan.val() == 1) {
          jenisKamar = 'titipan';
      }
      else if(is_pasienaps.val() == 1) {
          jenisKamar = 'aps';
      }

      is_pasientitipan.prop("disabled", false);
      is_pasienaps.prop("disabled", false);
      is_pasientitipan.prop("checked", false);
      is_pasienaps.prop("checked", false);
      is_pasientitipan.val(0);
      is_pasienaps.val(0);

      $("#pasienTitipanValue").val(0);
      $("#pasienApsValue").val(0);

      if (!jenis_id) {
        return new PNotify({
          title: "Terjadi Kesalahan",
          text: "Jenis Kasus belum dipilih!",
          addclass: "alert alert-warning alert-arrow-right alert-styled-right",
          type: "warning"
        });
      }
      if (!kelas_id) {
        return new PNotify({
          title: "Terjadi Kesalahan",
          text: "List Kelas Pelayanan belum dipilih!",
          addclass: "alert alert-warning alert-arrow-right alert-styled-right",
          type: "warning"
        });
      }
      // if cara bayar is umum it will hide kamar titipan
      if ($("#selectCarabayar").val() === '5' || $("#tipepasienform-is_bbl").is(':checked')) {
        $("#kamarTitipanCheck").parents('.form-group').hide();
        $("#isApsCheck").parents('.form-group').hide();
        activeTable = 'kamar';
      } else {
        if(typeof $("input[name='bpjsKelas']").val() == 'undefined' || $("input[name='bpjsKelas']").val() == '' || $("input[name='bpjsKelas']").val() == null) {
            $("#kamarTitipanCheck").parents('.form-group').hide();
            $("#isApsCheck").parents('.form-group').hide();
            activeTable = 'kamar';

        }
        else {
            // $("#kamarTitipanCheck").parents('.form-group').show();
            // $("#isApsCheck").parents('.form-group').show();
            $("#kamarTitipanCheck").parents('.form-group').hide();
            $("#isApsCheck").parents('.form-group').hide();
            activeTable !== 'kamar';
        }
      }

      initDatatable(jenisKamar, true);
      $("#modalTempatTidur").modal({
        backdrop: 'static',
        keyboard: false
      })
    })

    $("#btnCariKamarTitipan").bind('click', () => {
      var jenis_id = $('#jeniskasuspenyakit_id').val();
      var kelas_id = $('#kelas_ditagihkan_id').val();
      var ruangan_id = $('#ruangan_titipan_id').val();
      var carabayar_id = $("#selectCarabayar").val();
      var jenisKamar = 'semua';

      if (!jenis_id) {
        return new PNotify({
          title: "Terjadi Kesalahan",
          text: "Jenis Kasus belum dipilih!",
          addclass: "alert alert-warning alert-arrow-right alert-styled-right",
          type: "warning"
        });
      }
      if (!kelas_id) {
        return new PNotify({
          title: "Terjadi Kesalahan",
          text: "Kelas Tagihan belum dipilih!",
          addclass: "alert alert-warning alert-arrow-right alert-styled-right",
          type: "warning"
        });
      }

      initDatatableTitipan(jenisKamar, true);
      $("#modalTempatTidurTitipan").modal({
        backdrop: 'static',
        keyboard: false
      })
    })

    // Case when sudah memilih ketersediaan kamar
    if(!$.isEmptyObject(_formPendaftaran.rujukRanap)) {
      if (Object.keys(_formPendaftaran.rujukRanap.kamar).length) {
        pilihKamarRujukan(_formPendaftaran.rujukRanap.kamar[0]);
      }
    }

    $('#pegawai_id').change(function (e) {
      e.preventDefault();
      _formPendaftaran.getKarcis($("#kelaspelayanan_id").val());
    });
  },
  validKunjugan: function (object) {
    var _data = object.serializeArray();
    var _result = false;
    $().docoForm('click', {
      url: `/pendaftaran/${existingPageUrl}/validation-kunjungan`,
      data: _data.concat({ name: 'is_ranap', value: true }),
      skipConfirm: true,
      skipSuccessNotif: true,
      async: false,
      success: function (data) {
        _result = true;
      }
    });
    return _result;
  },
  getInfoPasien: function (id, noRm) {
    $.ajax({
      type: 'GET',
      url: `/pendaftaran/${existingPageUrl}/get-info-pasien?id=${id}&no_rm=${noRm}&is_ranap=true`,
      dataType: 'JSON',
      success: function (res) {
        var _response = res.response;
        $("#filterHeader").html('')
        var _infoPasien = _response.info_pasien;
        _formPendaftaran.infoPasien = _response.info_pasien
        jk = _infoPasien.jeniskelamin
        _formPendaftaran.selectedPasienId = _infoPasien.pasien_id
        $('#pasien_id_hidden').val(_formPendaftaran.selectedPasienId);
        $('.nama-pasien').text(_infoPasien.nama_pasien);
        $('.rm-pasien').text(_infoPasien.no_rekam_medik);
        $('.kelamin-pasien').text(': ' + (_infoPasien.jenis_kelamin ? _infoPasien.jenis_kelamin : '-'));
        $('.tempat-lahir-pasien').text(': ' + (_infoPasien.tempat_lahir ? _infoPasien.tempat_lahir : '-'));
        $('.tanggal-lahir-pasien').text(': ' + (_infoPasien.tanggal_lahir ? convertTanggalView(_infoPasien.tanggal_lahir) : '-'));
        $('.darah-pasien').text(': ' + (_infoPasien.golongan_darah ? _infoPasien.golongan_darah : '-'));
        $('.ibu-pasien').text(': ' + (_infoPasien.nama_ibu ? _infoPasien.nama_ibu : '-'));
        $('.tlp-pasien').text(': ' + (_infoPasien.no_telepon_pasien ? _infoPasien.no_telepon_pasien : '-'));
        $('.alamat-pasien').text(': ' + (_infoPasien.alamat_pasien ? _infoPasien.alamat_pasien : '-'));
        var _html = "";
        $.each(_response.kunjugan, function (key, data) {
          var _clone = _formPendaftaran.historyKunjugan;
          _clone.find('.history-pendaftaran-id').text(data.no_pendaftaran);
          _clone.find('.history-instalasi').text(': ' + (data.instalasi_nama ? data.instalasi_nama : '-'));
          _clone.find('.history-ruangan').text(': ' + (data.ruangan_nama ? data.ruangan_nama : '-'));
          _clone.find('.history-dokter').text(': ' + (data.nama_pegawai ? data.nama_pegawai : '-'));
          _clone.find('.history-tgl-pendaftaran').text(': ' + (data.tgl_pendaftaran ? convertDateByFormat(data.tgl_pendaftaran, 'd m Y - h:i') : '-'));
          _clone.find('.history-keluar').text(': ' + (data.tglpasienpulang ? convertDateByFormat(data.tglpasienpulang, 'd m Y - h:i') : '-'));
          _clone.find('.history-cara-keluar').text(': ' + (data.carakeluar_nama ? data.carakeluar_nama : '-'));
          _html += _clone.html();
          _clone.find('.history-penanggung-biaya').text(': ' + (data.penanggungbiaya_nama ? data.penanggungbiaya_nama : '-'));
        });
        if (_response.kunjugan.length >= 3) {
          var _action = $('.btn-detailss').attr('action');
          $('.btn-detailss').show();
          _html += '<hr><div align="center"><button type="button" class="btn btn-detailss btn-info btn-labeled btn-xs" action="/pendaftaran/daftar-igd/detail-kunjungan?no_rekam_medik=' + _infoPasien.no_rekam_medik + '" data-toggle="modal" data-target="#modal_backdrop" data-width="75%"><b><i class="fa fa-eye"></i></b>Detail Kunjungan</button></div>';
        }

        // info piutang pasien
        var info_piutang = document.getElementById("info-piutang");
        if (_infoPasien.total_sisapiutang > 0) {
            info_piutang.style.display = "block";
            $("#val-piutang").text(docoHelper.convertToRupiah(_infoPasien.total_sisapiutang));
        }

        // info catatan penting pasien
        var info_catatan = document.getElementById("info-catatan");
        if (_infoPasien.catatanpenting_pasien) {
            info_catatan.style.display = "block";
            $("#val-catatan").text(_infoPasien.catatanpenting_pasien);
        }

        $('#kunjungan').html(_html);
        $('#form-parent-info').show();
        $('#form-parent').removeClass("col-md-12").addClass("col-md-9");
        const latestKunjungan = _response.latest_kunjungan_rujuk_ranap;
        if(latestKunjungan) {
          if(latestKunjungan.penanggungjawab_id !== 'undefined' && latestKunjungan.penanggungjawab_id != null) {
              _formPendaftaran.tmpDataPj = {
                  idPj : latestKunjungan.penanggungjawab_id
              }
          } else {
              _formPendaftaran.tmpDataPj = {
                idPj : null
              }
          }
        }
        //PJ Pasien Tera
        if (_infoPasien.penanggungjawabtera_nama !== 'undefined' && _infoPasien.penanggungjawabtera_nama != null) {
            _formPendaftaran.tmpDataPj.penanggungjawabtera_nama = _infoPasien.penanggungjawabtera_nama
            _formPendaftaran.tmpDataPj.penanggungjawabtera_hubungan = _infoPasien.penanggungjawabtera_hubungan
            _formPendaftaran.tmpDataPj.penanggungjawabtera_alamat = _infoPasien.penanggungjawabtera_alamat
        }

        $(".nextRow").show()
        $("#selectCarabayar").val(latestKunjungan?.carabayar_id).trigger('change');
        if(latestKunjungan?.carabayar_id == 6) {
          $("input[name='BpjsNewForm[jenis_rujukan]']").prop("checked", true).trigger("click");
          var base = $("input:radio[name='BpjsNewForm[jenis_rujukan]']:checked").val();
          if (base == 1) {
            $('#base-rujukan').show();
            $('#base-rujukan-manual').hide();
          } else {
            $('#base-rujukan').hide();
            $('#base-rujukan-manual').show();
          }
        }
        if ($("#selectCarabayar").val() === 6) {
          var _action = $('.btn-detail-bpjs').attr('action');
          $('.btn-detail-bpjs').show();
        }
        existingReqData.penjamin_id = latestKunjungan?.penjamin_id
        existingReqData.newPasien = true
        if (latestKunjungan?.asalrujukan_id !== null) {
          $("#asalrujukan_id").val(latestKunjungan?.asalrujukan_id).trigger('change');

        }
        var url_edit_pasien = '/pendaftaran/informasi-pencarian-pasien/update?id='+_infoPasien.encrypted_pasien_id+'&is_close=true';
        $('#btn-edit-info-pasien').attr('data-target', url_edit_pasien);
      }
    });
  },
  getKarcis: function (kelasPelayananId = null, pasienTitipan = null) {
    if (kelasPelayananId === null) {
      return false
    } else {
      if (pasienTitipan) {
        var idRuangan = $('#ruanganTitipanIdHidden').val();
      } else {
        var idRuangan = $('#ruanganIdHidden').val();
      }
      var _params = {
        ruangan_id: idRuangan,
        kp_id: kelasPelayananId,
        status: _formPendaftaran.tipePasien.tipe_pasien ? 0 : 1,
        penjamin_id: _formPendaftaran.tipePasien.penjamin_id,
        type: "kamar",
        dokter_id: $('#pegawai_id').val()
      };
      _params = $.param(_params);
      var tbl;
      tbl = $('#tbl-karcis').docoTabel({
        filter: true,
        destroy: true,
        paging: false,
        sorting: [[0, 'asc']],
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: baseUrl + `pendaftaran/${existingPageUrl}/get-karcis?${_params}`,
        initComplete: function (row, data) {
          _formPendaftaran.listTarif = $();
          $('.styled, .check-aksi input').uniform({
            radioClass: 'choice'
          });
          var api = this.api();
          $.each(api.rows().data(), function (key, val) {
            _formPendaftaran.listTarif.push(val);
          });
          $('.check-aksi').on('click', function (event) {
            var key = $(this).attr('data-key');
            if ($(this).is(':checked')) {
              total_tarif = parseInt(_formPendaftaran.totalTarif) + parseInt(_formPendaftaran.listTarif[key].harga_tariftindakan);
            } else {
              total_tarif = _formPendaftaran.totalTarif - (_formPendaftaran.listTarif[key].harga_tariftindakan);
            }
            _formPendaftaran.totalTarif = total_tarif;
            $('.kolom-total-tarif').html('Rp. ' + docoHelper.convertToRupiah(total_tarif));
          });
        },
        drawCallback: () => {
          $('#tbl-karcis').find('.styled, .check-aksi input').uniform({
            radioClass: 'choice'
          });
          bindCheckboxWithSpace($('#tbl-karcis'))
        },
        fnFooterCallback: function (row, data, start, end, display) {
          var api = this.api();
          var intVal = function (i) {
            return typeof i === 'string' ?
              i.replace(/[\$,]/g, '') * 1 :
              typeof i === 'number' ?
                i : 0;
          };
          _formPendaftaran.totalTarif = api
            .column(4)
            .data()
            .reduce(function (a, b) {
              return intVal(a) + intVal(b);
            }, 0);
          $(api.column(2).footer()).addClass('kolom-total-tarif').html('Rp. ' + docoHelper.convertToRupiah(_formPendaftaran.totalTarif));
        },
        columns: [
          {
            title: 'No',
            data: 'number',
            searchable: false,
            orderable: false
          },
          {
            title: title_karcis.replace(/['"]+/g, ''),
            data: 'daftartindakan_nama',
            searchable: false,
            orderable: false
          },
          {
            title: 'Harga',
            data: 'tmp_view',
            searchable: false,
            orderable: false
          },
          {
            title: '',
            data: 'aksi',
            searchable: false,
            orderable: false
          },
          {
            data: 'tmp_total',
            visible: false,
            searchable: false,
            orderable: false
          }
        ],

      });
      $('.dataTables_filter').hide();
    }
  },
  resetForm: function () {
    var stepsLength = $('ul[role="tablist"] > li:not(.first)').length;
    for (x = stepsLength; x > 0; x--) {
      $('.steps-basic').steps('previous');
    }
    setTimeout(function () {
      for (x = stepsLength; x > 0; x--) {
        $('.steps-basic').steps("remove", x);
      }
    }, 300);

    $('#form-parent-info').hide();
    $('#form-parent').removeClass("col-md-9").addClass("col-md-12");
    var _currentLi = $('ul[role="tablist"] > li.current > a').attr("aria-controls");
    $('#' + _currentLi + ' select').val('').trigger('change');
    tableDaftarTerakhir.draw();
    _formPendaftaran.resetAsuransi();
    resetFormMultiAsuransi()
    $('.field-tipepasienform-no_asuransi').hide();
    $('.nextRow').hide();
    $('#form-bpjs').hide();
  },
  formPenanggungJawab: function() {
    /** Re init */
    var _content = $('#form-pj-content');
    $("#pjpasienform-pj_jenis_identitas,#pjpasienform-pj_hubungan,#pjpasienform-pj_pengantar").select2()
    var $input_date = $('#pjpasienform-pj_tanggal_lahir').pickadate({
      editable: true,
      format: 'dd-mm-yyyy',
      formatSubmit: 'dd-mm-yyyy',
      selectMonths: true,
      selectYears: true,
      min: [1900, 01, 01],
      max: true,
      onClose: function () {
        $('.datepicker').focus();
      }
    });
    var picker_date = $input_date.pickadate('picker');
    $('#pj-date').parent().on('click', function (event) {
      if (picker_date.get('open')) {
        picker_date.close();
      } else {
        picker_date.open();
      }
      event.stopPropagation();
    });

    /**
     * Penambahan Suggestion apabila memilihi diri sendiri.
     */
    $("#pjpasienform-pj_pengantar").on("change", function () {
      let val = $(this).val();
      let noRm = _formPendaftaran?.tipePasien?.no_rekam_medik;

      if (noRm && val == 245) {
        $.ajax({
          type: "GET",
          url:
            "/pendaftaran/daftar-" +
            _formPendaftaran.params +
            "/get-info-pasien?id=&no_rm=" +
            noRm,
          dataType: "JSON",
          success: function (res) {
            var _caraBayar = $(".selectCarabayar").val();
            var _response = res.response;
            var _infoPasien = _response.info_pasien;

            var additionalPasien = _infoPasien?.additional_pasien;
            if (additionalPasien != undefined) {
                additionalPasien = JSON.parse(additionalPasien);
                if (additionalPasien.length > 0) {
                    let additionalDataObj = additionalPasien[0];
                    if (additionalDataObj) {
                        let jenisidentitas = additionalDataObj?.jenisidentitas;
                        let noIdentitas = additionalDataObj?.no_identitas_pasien;
                        $('#pjpasienform-pj_jenis_identitas').val(jenisidentitas).trigger('change')
                        $('#pjpasienform-pj_no_identitas').val(noIdentitas).trigger('change')
                    }
                }
            } else {
                $('#pjpasienform-pj_jenis_identitas').val(_infoPasien.jenisidentitas).trigger('change')
                $('#pjpasienform-pj_no_identitas').val(_infoPasien.no_identitas_pasien).trigger('change')
            }

            var idBtnJk = $('#pj_jk_' + parseInt(_infoPasien.jeniskelamin))
            idBtnJk.prop('checked', true).trigger('click');

            $('#pjpasienform-pj_nama').val(_infoPasien.nama_pasien).trigger('change')
            $('#pjpasienform-pj_tempat_lahir').val(_infoPasien.tempat_lahir).trigger('change')
            $('#pjpasienform-pj_tanggal_lahir').val(convertDateByFormat(_infoPasien.tanggal_lahir, 'd-M-Y')).trigger('change')
            $('#pjpasienform-pj_no_telepon').val(_infoPasien.no_telepon_pasien).trigger('change')
            $('#pjpasienform-pj_alamat').val(_infoPasien.alamat_pasien).trigger('change')
            $('#pjpasienform-pj_hubungan').val('').trigger('change')
          },
          error: function (err) {
            console.log(err);
          },
        });
      } else {
        $('#pjpasienform-pj_jenis_identitas').val('').trigger('change')
        $('#pjpasienform-pj_no_identitas').val('').trigger('change')
        $('#pjpasienform-pj_nama').val('').trigger('change')
        $('#pjpasienform-pj_tempat_lahir').val('').trigger('change')
        $('#pjpasienform-pj_tanggal_lahir').val('').trigger('change')
        $('#pjpasienform-pj_no_telepon').val('').trigger('change')
        $('#pjpasienform-pj_alamat').val('').trigger('change')
        $('#pjpasienform-pj_hubungan').val('').trigger('change')
        
        let jenisKelamin = [15, 16, 728]
        jenisKelamin.map((jk) => {
            let jkCheck = $('#pj_jk_' + jk)
            if (jkCheck.parent().hasClass('checked')) {
                jkCheck.prop('checked', false).trigger('click').parent().removeClass('checked');
            }
        })
      }
    });

    $('#pjpasienform-pj_tanggal_lahir').on('change', function () {
      var umur = '';
      if ($(this).val() != '') {
        const splitDate = $(this).val().split('-')
        const date = `${splitDate[2]}-${splitDate[1]}-${splitDate[0]}`

        var myDate = new Date(date);
        var today = new Date();
        if (myDate > today) {
          $(this).pickadate('picker').set('select', new Date())
          return true
        }
        umur = generateUmur($(this).val());
      }

      setTimeout(() => {
        $('.usiapjtext').val(umur);
      }, 100);
    })
  }
};

$(() => {
  $("#kamarTitipanCheck").bind('change', ({ delegateTarget }) => {
      changeKamar('titipan');
  });

  $("#isApsCheck").bind('change', ({ delegateTarget }) => {
      changeKamar('aps');
  });

  $(document).on('select2:close', '.select2-hidden-accessible', ({ currentTarget }) => {
    $(currentTarget).focus()
  });

  changeKamar = function(jenisKamar) {
      var element = '';
      if(jenisKamar == 'semua') {
          activeTable = 'kamar';
          enableAll();
      }
      else {
          if(jenisKamar == 'titipan') {
            activeTable = 'kamarTitipan';
            element = $("#kamarTitipanCheck");
            if(element.is(':checked')) {
                disableAps();
            }
            else {
                jenisKamar = 'semua';
                activeTable = 'kamar';
                enableAll();
            }
        }
        else {
            activeTable = 'kamarAps';
            element = $("#isApsCheck");
            if(element.is(':checked')) {
                disableTitipan();
            }
            else {
                jenisKamar = 'semua';
                activeTable = 'kamar';
                enableAll();
            }
        }
      }

      initDatatable(jenisKamar, true);
  }

  disableAps = function() {
    $("#pasienTitipanValue").val(1);
    $("#kamarTitipanCheck").val(1);
    $("#pasienApsValue").val(0);
    $("#isApsCheck").val(0);
    $("#isApsCheck").prop("disabled", true);

    //aps
    $("#filterHeaderKamarAps").hide();
    $("#tableKamarApsWrapper").hide();

    //titipan
    $("#filterHeaderKamarTitipan").show();
    $("#tableKamarTitipanWrapper").show();

    //semua
    $("#filterHeaderKamarSemua").hide();
    $("#tableKamarWrapper").hide();
  }

  disableTitipan = function() {
    $("#pasienApsValue").val(1);
    $("#isApsCheck").val(1);
    $("#pasienTitipanValue").val(0);
    $("#kamarTitipanCheck").val(0);
    $("#kamarTitipanCheck").prop("disabled", true);

    //aps
    $("#filterHeaderKamarAps").show();
    $("#tableKamarApsWrapper").show();

    //titipan
    $("#filterHeaderKamarTitipan").hide();
    $("#tableKamarTitipanWrapper").hide();

    //semua
    $("#filterHeaderKamarSemua").hide();
    $("#tableKamarWrapper").hide();
  }

  enableAll = function() {
    //aps
    $("#pasienApsValue").val(0);
    $("#isApsCheck").val(0);
    $("#isApsCheck").prop("disabled", false);
    $("#filterHeaderKamarAps").hide();
    $("#tableKamarApsWrapper").hide();

    //titipan
    $("#pasienTitipanValue").val(0);
    $("#kamarTitipanCheck").val(0);
    $("#kamarTitipanCheck").prop("disabled", false);
    $("#filterHeaderKamarTitipan").hide();
    $("#tableKamarTitipanWrapper").hide();

    //semua
    $("#filterHeaderKamarSemua").show();
    $("#tableKamarWrapper").show();
  }
})

function validasiKunjungan(data) {
  let btnNxt = $(".wizard").find('a[href="#next"]');
  let btnFnsh = $(".wizard").find('a[href="#finish"]');
  let validate = true;

  if ($("#tipepasienform-is_bbl").is(':checked')) {
    validate = false;
  }

  if(validate) {
    $.ajax({
        type: 'POST',
        url: '/pendaftaran/end-point/validate',
        data: {
            noRm: data,
            params: _formPendaftaran.params
        },
        dataType: 'JSON',
        success: function (res) {
            if(typeof res.text != 'undefined') {
                docoNotification('warning', 'Perhatian!', res.text)
            }else {
                btnNxt.attr("href", '#next');
                btnFnsh.attr("href", '#finish');
                btnNxt.parent().removeClass("hidden")
                btnFnsh.parent().removeClass("hidden")
            }

            return true;
        },
        error: function (res) {
            btnNxt.attr("href", '#next');
            btnFnsh.attr("href", '#finish');
            btnNxt.parent().addClass("hidden")
            btnFnsh.parent().addClass("hidden")

            docoNotification('error', 'Perhatian!', res.responseJSON.text)
            return false;
        }
    });
  } else {
    return 'Pendaftaran Bayi'
  }
}

function getProfile() {
  $.ajax({
    type: 'GET',
    url: '/pendaftaran/end-point/get-profile-rs',
    dataType: 'JSON',
    success: function (res) {
      $('#frm-pasien-tempat_lahir').val(res.response.kota)
    },
  });
}

function lookGoldrh(val) {
  let lookId = '';
  switch (val) {
    case 'a':
      lookId = 23
      break;
    case 'ab':
      lookId = 24
      break;
    case 'o':
      lookId = 25
      break;
    case 'b':
      lookId = 26
      break;
    default:
      lookId = 600;
      break;
  }
  return lookId
}

function setKelasRawat(hakKelas) {
  let kelas = parseInt(hakKelas);
  let defaultOptions = [{
    'id': 1, 'text': 'Kelas I'
    },{
    'id': 2, 'text': 'Kelas II'
    },{
    'id': 3, 'text': 'Kelas III'
    },
  ]
  switch (kelas) {
    case 3:
      defaultOptions.shift();
      refreshOptionSelect2($("#kelas_rawat"), defaultOptions)
      break;
    default:
      refreshOptionSelect2($("#kelas_rawat"), defaultOptions)
      break;
  }
  $("#kelas_rawat").val(kelas).trigger('change');
}

function getPj(idPj = null, idPasien = null) {
  $.ajax({
      type: 'GET',
      url: '/pendaftaran/end-point/get-penanggung-jawab?idPj='+idPj+ '&pasienId=' + idPasien,
      dataType: 'JSON',
      success: function (data) {
          let idBtnJk = $('#pj_jk_' + parseInt(data.penanggungjawab_jeniskelamin))
          $('#pjpasienform-pj_pengantar').val(data.pengantar).trigger('change')
          $('#pjpasienform-pj_nama').val(data.penanggungjawab_nama).trigger('change')
          // $("#pjpasienform-pj_jk").find(`input[value=${data.penanggungjawab_jeniskelamin}]`).trigger('click')
          idBtnJk.prop('checked', true).trigger('click');
          $('#pjpasienform-pj_jenis_identitas').val(data.jenisidentitas).trigger('change')
          $('#pjpasienform-pj_no_identitas').val(data.no_identitas).trigger('change')
          $('#pjpasienform-pj_hubungan').val(data.hubungankeluarga).trigger('change')
          $('#pjpasienform-pj_tempat_lahir').val(data.penanggungjawab_tempatlahir).trigger('change')
          $('#pjpasienform-pj_tanggal_lahir').val(convertDateByFormat(data.penanggungjawab_tgllahir, 'd-M-Y')).trigger('change')
          $('#pjpasienform-pj_no_telepon').val(data.penanggungjawab_notelp).trigger('change')
          $('#pjpasienform-pj_alamat').val(data.penanggungjawab_alamat).trigger('change')
      },
      error: function (res) {
         console.log(res)
      }
  });
}

function getListDokter(ruanganId) {
  if ($('#pegawai_id').hasClass("select2-hidden-accessible")) {
    $('#pegawai_id').select2('destroy')
  }

  $.ajax({
    url: '/ranap/end-point/get-dokter-by-ruangan',
    data: {
      ruangan_id: ruanganId
    },
    method: 'GET',
    success: ({ data }) => {
      Object.keys(data).map((itemOutput) => {
        dataDropDownPegawai.push({
          id: itemOutput,
          text: data[itemOutput]
        })
      })
      refreshOptionSelect2($('#pegawai_id'), dataDropDownPegawai)
    },
    complete: () => {
      $('#pegawai_id').prop('disabled', false)
    }
  })
}

function setAlamat(propinsiId, kabupatenId, kecamatanId, kelurahanId) {
  alamatBy = {
    province: propinsiId,
    city: kabupatenId,
    district: kecamatanId,
    village: kelurahanId,
  }
  // $("#frm-pasien-propinsi_id").val(propinsiId).change()
  bindingRegionBbl('/pendaftaran/end-point/region-list', {
    province: $("#frm-pasien-propinsi_id"),
    city: $("#frm-pasien-kabupaten_id"),
    district: $("#frm-pasien-kecamatan_id"),
    village: $("#frm-pasien-kelurahan_id"),
  })
}

function setKunjunganBbl(selectedBayi) {
  $(document).find('#nokamar').val(selectedBayi.kamarruangan_nokamar + ' - ' + selectedBayi.no_tempattidur)
  $(document).find('#ruanganLabelValue').html("");
  $(document).find('#ruanganLabelValue').html(selectedBayi.ruangan_by + ' - ' + selectedBayi.kelas_nama_by)
  $("#kamartempattidur_id").val(selectedBayi.kamartempattidur_id)
  $("#kamarruangan_id").val(selectedBayi.kamarruangan_id)
  $("#jeniskasuspenyakit_id").val(selectedBayi.jeniskasuspenyakit_id).trigger('change')
  $("#kelaspelayanan_id").val(selectedBayi.kelas_id_by).trigger('change')
  $("#ruanganIdHidden").val(selectedBayi.ruangan_id_by).trigger('change')
  $("#kelasPelayananSelected").val(selectedBayi.kelas_id_by)
  setTimeout(() => {
    _formPendaftaran.getKarcis(selectedBayi.kelas_id_by);
    getListDokter(selectedBayi.ruangan_id_by);
  }, 3000);
}

function bindingRegionBbl(urlApi, objectElement) {
  // object element must be 4 element -> province,city,district,village
  const { province, city, district, village } = objectElement
  const defaultValue = [
      {
          id: '',
          text: '--Pilih--'
      }
  ]

  existVillage = typeof village !== 'undefined'
  // province.bind('change', () => {
      if (province.val() !== '' && province.val() !== null) {
          $.ajax({
              url: urlApi,
              method: 'GET',
              data: {
                  type: 'city',
                  foreignId: province.val()
              },
              success: (res) => {
                  const { data } = res
                  refreshOptionSelect2(city, data)
                  city.prop('disabled', false)
                  city.val(alamatBy.city).trigger('change')
                  resetDropdownRegion(district)
                  resetDropdownRegion(village)
              }
          })
      } else {
          city.val('').trigger('change')
          city.prop('disabled', true)
      }
  // })

  city.bind('change', () => {
      if (city.val() !== '' && city.val() !== null) {
          $.ajax({
              url: urlApi,
              method: 'GET',
              data: {
                  type: 'district',
                  foreignId: city.val()
              },
              success: (res) => {
                  const { data } = res
                  refreshOptionSelect2(district, data)
                  // district.val('').trigger('change')
                  district.prop('disabled', false)
                  district.val(alamatBy.district).trigger('change')
                  resetDropdownRegion(village)
              }
          })
      } else {
          district.val('').trigger('change')
          district.prop('disabled', true)
      }
  })
  if (existVillage) {
    district.bind('change', () => {
          if (district.val() !== '' && district.val() !== null) {
              $.ajax({
                  url: urlApi,
                  method: 'GET',
                  data: {
                      type: 'village',
                      foreignId: district.val()
                  },
                  success: (res) => {
                      const { data } = res
                      refreshOptionSelect2(village, data)
                      // village.val('').trigger('change')
                      village.prop('disabled', false)
                      village.val(alamatBy.village).trigger('change')
                  }
              })
          } else {
              village.val('').trigger('change')
              village.prop('disabled', true)
          }
      })
      village.bind('change', () => {
          if (village.val() !== '' && village.val() !== null) {
              village.prop('disabled', false)
          }
      })
  }
  if (province.val() == null || province.val() == '') {
      city.prop('disabled', true)
  }
  if (city.val() == null || city.val() == '') {
      district.prop('disabled', true)
  }
  if ((district.val() == null || district.val() == '') && existVillage) {
      village.prop('disabled', true)
  }
}

$(document).on('select2:close', '#frm-pasien-propinsi_id', function (e) {
  e.preventDefault()
  bindingRegionBbl('/pendaftaran/end-point/region-list', {
    province: $("#frm-pasien-propinsi_id"),
    city: $("#frm-pasien-kabupaten_id"),
    district: $("#frm-pasien-kecamatan_id"),
    village: $("#frm-pasien-kelurahan_id"),
  })
})

$(document).on('click', '.tambah-cara-bayar', function (e) {
  $(".form-multi-carabayar-second").show()
  $(this).prop('disabled', true)
  $(".field-addSelectCarabayar3").addClass("required")
  $(".field-add_penjamin_id_2").addClass("required")
  // $(".field-secondAsalrujukan_id").addClass("required")
  _formPendaftaran.listTmp.is_add_payer = true
})

$(document).on('click', '.hapus-cara-bayar', function (e) {
  resetMultiPayer()
})

$(document).on('change', '#tipepasienform-is_multi_payer', function (e) {
  e.preventDefault()
  _formPendaftaran.resetForm();
  $('.field-multicarabayarform-add_no_asuransi_1').hide();

  if ($(this).prop("checked")) {
      $('.form-multi-carabayar').show()
  } else {
      $('.form-multi-carabayar').hide()
      // resetMultiPayer()
  }
})

$(document).on('change', '#addSelectCarabayar2', function () {
  let carabayar_group = $('#addSelectCarabayar2').find(':selected').attr('data-id');
  stateCaraBayar(carabayar_group, true)
  $("#first-note-asuransi").html("");
  $("#multicarabayarform-add_no_asuransi_1").val("").trigger("change");
});

$(document).on('change', '#addSelectCarabayar3', function () {
  let carabayar_group = $('#addSelectCarabayar3').find(':selected').attr('data-id');
  stateCaraBayar(carabayar_group, true, true)
  $("#second-note-asuransi").html("");
  $("#multicarabayarform-add_no_asuransi_2").val("").trigger("change");
});

$(document).on('click', '.field-multicarabayarform-add_no_asuransi_1 span.input-group-addon', function (event) {
  event.preventDefault();
  _formPendaftaran.firstTmpAsuransi = {
      kelastanggunganasuransi_id: null,
      namapemilikasuransi: null,
      namaperusahaan: null,
      no_asuransi: null,
      nomorpokokperusahaan: null,
      pasien_id: null,
      no_rekam_medik: null,
      prevPasien: _formPendaftaran.firstTmpAsuransi.prevPasien
  };
  $('#multicarabayarform-add_no_asuransi_1').val('');
  $('#multicarabayarform-add_no_asuransi_1').prop('readonly', false);
  $('.field-multicarabayarform-add_no_asuransi_1').find("#first-note-asuransi").html("");
});

$(document).on('click', '.field-multicarabayarform-add_no_asuransi_2 span.input-group-addon', function (event) {
  event.preventDefault();
  _formPendaftaran.secondTmpAsuransi = {
      kelastanggunganasuransi_id: null,
      namapemilikasuransi: null,
      namaperusahaan: null,
      no_asuransi: null,
      nomorpokokperusahaan: null,
      pasien_id: null,
      no_rekam_medik: null,
      prevPasien: _formPendaftaran.secondTmpAsuransi.prevPasien
  };
  $('#multicarabayarform-add_no_asuransi_2').val('');
  $('#multicarabayarform-add_no_asuransi_2').prop('readonly', false);
  $('.field-multicarabayarform-add_no_asuransi_2').find("#second-note-asuransi").html("");
});

$(document).on('change', '#bpjsnewform-is_naikkelas_ranap', function (e) {
    e.preventDefault();

    if ($(this).prop("checked")) {
        $('.naik_kelas_rawat').show();
    } else {
        $('.naik_kelas_rawat').hide();
        setKelasRawat(peserta.hakKelas.kode);
        $(`[name^="BpjsNewForm[naik_kelas_rawat_inap]"]`).val('').change();
        $(`[name^="BpjsNewForm[pembiayaan]"]`).val('').change();
        $(`[name^="BpjsNewForm[nama_penganggung_jawab]"]`).val('').change();
    }
});

$(document).on('change', '[name="BpjsNewForm[pembiayaan]"]', function (e) {
    e.preventDefault();
    if ($(this).val() == 1) {
        $('[name="BpjsNewForm[nama_penganggung_jawab]"]').val('Pribadi').change();
    }
})

$(document).on('click', '#btn-confirm-peserta-asuransi', function () {
  $('#modal_backdrop').modal('hide');
  _formPendaftaran.ignoreAsuransi = true
  _formPendaftaran.generateForm()
});


const stateCaraBayar = function (carabayar_group, multi = false, scd = false) {
  let _isBpjs = false;
  let main_carabayar_group = $('.selectCarabayar').find(':selected').attr('data-id');
  let add_carabayar_group_1 = $('#addSelectCarabayar2').find(':selected').attr('data-id');
  let add_carabayar_group_2 = $('#addSelectCarabayar3').find(':selected').attr('data-id');

  if (main_carabayar_group == docoHelper.groupBPJS || add_carabayar_group_1 == docoHelper.groupBPJS || add_carabayar_group_2 == docoHelper.groupBPJS) {
      _isBpjs = true
  }

  if (multi) {
      let inputAsuCls = $('.field-multicarabayarform-add_no_asuransi_1')
      let hintNoteNoasuId = $("#first-note-asuransi")
      let hintNoasuId = $("#multicarabayarform-add_no_asuransi_1")

      if (scd != false) {
          inputAsuCls = $('.field-multicarabayarform-add_no_asuransi_2')
          hintNoteNoasuId = $("#second-note-asuransi")
          hintNoasuId = $("#multicarabayarform-add_no_asuransi_2")
      }

      if (_isBpjs) {
          if (carabayar_group == docoHelper.groupUmum) {
              inputAsuCls.hide()
          } else if (carabayar_group == docoHelper.groupJaminan) {
              inputAsuCls.show()
          } else if (carabayar_group == docoHelper.groupBPJS) {
              inputAsuCls.hide()
          }
          $('#form-bpjs').show();
          $('#jenis_pelayanan').val(2).trigger("change");
      } else {
          if (carabayar_group == docoHelper.groupUmum) {
              inputAsuCls.hide()
              $('#form-bpjs').hide();
          } else if (carabayar_group == docoHelper.groupJaminan) {
              inputAsuCls.show()
              $('#form-bpjs').hide();
          } else if (carabayar_group == docoHelper.groupBPJS) {
              $('#form-bpjs').show();
              $('#jenis_pelayanan').val(2).trigger("change");
              inputAsuCls.hide();
          }
          hintNoteNoasuId.html("");
          hintNoasuId.val("").trigger("change");
      }
  } else {
      if (_isBpjs) {
          if (carabayar_group == docoHelper.groupUmum) {
              $('.field-tipepasienform-no_asuransi').hide();
              $('.field-asalrujukan_id').show();
          } else if (carabayar_group == docoHelper.groupJaminan) {
              $('.field-tipepasienform-no_asuransi').show();
              $('.field-asalrujukan_id').show();
          } else if (carabayar_group == docoHelper.groupBPJS) {
              $('.field-tipepasienform-no_asuransi').hide();
              $('.field-asalrujukan_id').hide();
          }

          $('#form-bpjs').show();
          $('#jenis_pelayanan').val(2).trigger("change");
          // $('.field-asalrujukan_id').hide();
      } else {
          if (carabayar_group == docoHelper.groupUmum) {
              $('.field-tipepasienform-no_asuransi').hide();
              $('.field-asalrujukan_id').show();
              $('#form-bpjs').hide();
          } else if (carabayar_group == docoHelper.groupJaminan) {
              $('.field-tipepasienform-no_asuransi').show();
              $('.field-asalrujukan_id').show();
              $('#form-bpjs').hide();
          } else if (carabayar_group == docoHelper.groupBPJS) {
              $('#form-bpjs').show();
              $('#jenis_pelayanan').val(2).trigger("change");
              $('.field-asalrujukan_id').hide();
              $('.field-tipepasienform-no_asuransi').hide();
          } else {
              $('#form-bpjs').hide();
              $('.field-tipepasienform-no_asuransi').hide();
              $('.field-asalrujukan_id').show();
          }
          $("#note-asuransi").html("");
          $("#tipepasienform-no_asuransi").val("").trigger("change");
      }
  }
}

const resetFormMultiAsuransi = function() {
  $('.field-multicarabayarform-add_no_asuransi_1 span.input-group-addon').trigger('click');
  $('.field-multicarabayarform-add_no_asuransi_2 span.input-group-addon').trigger('click');
  $('.field-tipepasienform-no_asuransi').hide();
  $('.field-multicarabayarform-add_no_asuransi_1').hide();
  $('.field-multicarabayarform-add_no_asuransi_2').hide();
}

function resetMultiPayer() {
  if ($('#addSelectCarabayar3').find(':selected').attr('data-id') == docoHelper.groupBPJS) {
      $('#form-bpjs').hide()
  }
  $(".form-multi-carabayar-second").hide()
  $('.tambah-cara-bayar').prop('disabled', false)
  $("#second-note-asuransi").html("");
  $("#multicarabayarform-add_no_asuransi_2").trigger("change");
  $('#addSelectCarabayar3').val("").trigger('change')
  $('#add_penjamin_id_2').val("").trigger('change')
  $('.field-multicarabayarform-add_no_asuransi_2').hide()
  packDeleteGroup2()
  removeStepList()
}

const packDeleteGroup2 = function () {
  delete _formPendaftaran.listTmp.is_add_payer
  delete _formPendaftaran.listTmp[secondCbG]
  delete _formPendaftaran.listTmp.add_carabayar_id_2
}

const validationTipePasien = function () {
  removeStepList()
  $('a[href="#finish"]').trigger('click');
}

const removeStepList = function () {
  $.each($('ul[role="tablist"] > li:not(.first)'), function () {
      $('.steps-basic').steps("remove", 1);
  });
}

const generateStepRi = function (_groupCaraBayar, _noRm, _asalRujukan, _pasienId = null, _findPasien, _bpjs, data, _pencarianBpjs, _isGenerateKunjungan = false) {
  if (_groupCaraBayar == docoHelper.groupJaminan) {
      _formPendaftaran.formAsuransi();
      implementAsuransiData()
  } else if (_groupCaraBayar == docoHelper.groupBPJS) {
      generateStepBpjsRi(_groupCaraBayar, _bpjs, _pencarianBpjs, data, _asalRujukan, _noRm, true)
  } else if (_groupCaraBayar == docoHelper.groupUmum) {
      _formPendaftaran.resetAsuransi();
  }

  if ((_asalRujukan != 1 && _groupCaraBayar != docoHelper.groupBPJS)) {
      _formPendaftaran.formRujukan();
  }

  if (_isGenerateKunjungan) {
      generateFormKunjungan()
  }
}

const generateStepBpjsRi = function (_groupCaraBayar, _bpjs, _pencarianBpjs, data, _asalRujukan, _noRm, notMultipayer = false) {
  _formPendaftaran.tmpBpjs = _bpjs;
  let _statRujukan = false;
  console.log(data.response)
  _formPendaftaran.dataReturnBpjs = data.response.pasien_bpjs;

  if (typeof data.response.pasien_bpjs.peserta != 'undefined') {
      _formPendaftaran.dataBpjs = data.response.pasien_bpjs.peserta;
      if (_formPendaftaran.dataBpjs.statusPeserta.kode > 0) {
          docoNotification('error', 'Proses BPJS Gagal !', _formPendaftaran.dataBpjs.statusPeserta.keterangan)
          return false;
      }
  }

  if (_pencarianBpjs == 1) { // rujukan
      if (typeof data.response.pasien_bpjs.rujukan != 'undefined') {
          _formPendaftaran.dataRujukanBpjs = data.response.pasien_bpjs.rujukan;
          _formPendaftaran.tmpBpjs.no_kartu = _formPendaftaran.dataRujukanBpjs.noKunjungan;
      }
  } else { // rujukan manual / IGD

  }

  if (typeof _formPendaftaran.dataReturnBpjs.messages != 'undefined') {
      _formPendaftaran.errorBpjs(_bpjs);
      _formPendaftaran.generateBpjs(_pencarianBpjs, _asalRujukan, _groupCaraBayar, _noRm);
      return true;
  }

  if (_formPendaftaran.infoPasien.nama_pasien.toLowerCase() !== _formPendaftaran.dataReturnBpjs.peserta.nama.toLowerCase()) {
      setTimeout(function () {
          var _title = `<b>Peringatan !</b><br>
          Nama Pasien yang diinputkan berbeda dengan Nama BPJS<br>
          Nama Pasien BPJS : <strong>${_formPendaftaran.dataReturnBpjs.peserta.nama}</strong><br>
          Pasien yang terdaftar: <strong>${_formPendaftaran.infoPasien.nama_pasien}</strong><br>
          Apakah Anda yakin akan melanjutkan proses?`;
          confirmationDialog(_title, function (cond) {
              if (cond) {
                _formPendaftaran.dataBpjs.mr.noMR = _formPendaftaran.tipePasien.no_rekam_medik;
                _formPendaftaran.generateBpjs(_pencarianBpjs, _asalRujukan, _groupCaraBayar, _noRm);
              } else {
                removeStepList()
              }
          })
      },100)
      return true
  }

  _formPendaftaran.formBpjs(_pencarianBpjs);
  if (notMultipayer) {
      _formPendaftaran.resetAsuransi();
  }
  if (typeof _formPendaftaran.dataBpjs.mr != 'undefined' && _formPendaftaran.dataBpjs.mr != null) {
      _findPasien = true;
      _noRm = _formPendaftaran.dataBpjs.mr.noMR;
  } else {
      _formPendaftaran.formPasien();
  }

  if ($('input[name="TipePasienForm[is_multi_payer]"]:checked').val()) {
      if (_formPendaftaran.listTmp[firstCbG] == docoHelper.groupJaminan) {
          _formPendaftaran.formFirstAsuransi(_formPendaftaran.firstTmpAsuransi);
      }

      if (_formPendaftaran.listTmp.is_add_payer != 'undefined' && _formPendaftaran.listTmp.is_add_payer == true) {
          if (_formPendaftaran.listTmp[secondCbG] == docoHelper.groupJaminan) {
              _formPendaftaran.formSecondAsuransi(_formPendaftaran.secondTmpAsuransi);
          }
      }
  }

  generateFormKunjungan()
}

const generateFormKunjungan = function () {
  _formPendaftaran.formKunjungan();
  $('select').removeAttr('tabindex');
  $('.steps-basic').steps("next");
}

const conditionResetStepBpjs = function () {
  let _execute = false;
  /** Input BPJS */
  var _jenisPencarian = $('input[name="BpjsNewForm[jenis_rujukan]"]:checked').val();
  var _jenisPelayanan = $('select[name="BpjsNewForm[jenis_pelayanan]"]').val();
  var _jenisKartu = $('input[name="BpjsNewForm[jenis_kartu]"]:checked').val();
  var _noKartu = $('input[name="BpjsNewForm[no_kartu]"]').val();

  /** BPJS Rujukan */
  var _asalRujukanBpjs = $('select[name="BpjsNewForm[asal_rujukan]"]').val();
  var _noRujukan = $('input[name="BpjsNewForm[no_rujukan_f]"]').val();

  if (_formPendaftaran.tmpBpjs.jenis_pencarian == 2) {
      if (_formPendaftaran.tmpBpjs.jenis_pelayanan != _jenisPelayanan
          || _formPendaftaran.tmpBpjs.jenis_kartu != _jenisKartu
          || _formPendaftaran.tmpBpjs.no_kartu != _noKartu
          || _formPendaftaran.tmpBpjs.jenis_pencarian != _jenisPencarian) {
          _execute = true;
      }
  } else {
      if (_formPendaftaran.tmpBpjs.asal_rujukan != _asalRujukanBpjs
          || _formPendaftaran.tmpBpjs.no_rujukan_f != _noRujukan
          || _formPendaftaran.tmpBpjs.jenis_pencarian != _jenisPencarian) {
          _execute = true;
      }
  }
  if (_execute) {
      validationTipePasien()
      return false;
  } else {
    return true;
  }
}

function generatePj(_formPendaftaran) {
  // setTimeout(function() {
    if(Object.keys(_formPendaftaran.tmpDataPj).length) {
        // $("input[name='KunjunganForm[is_pj]']").closest('span').addClass('checked')
        // $("input[name='KunjunganForm[is_pj]']").prop("checked", true).trigger('change')
        //getPj(_formPendaftaran.tmpDataPj.idPj)
        let _clonePj = _formPendaftaran.formInputPj;
        $('.steps-basic').steps("add", {
          title: "Penanggung Jawab",
          content: _clonePj.html()
        });
        _formPendaftaran.formPenanggungJawab();
        if (_formPendaftaran.tmpDataPj.idPj != null) {
            getPj(_formPendaftaran.tmpDataPj.idPj)
        } else {
            $('#pjpasienform-pj_nama').val(_formPendaftaran.tmpDataPj.penanggungjawabtera_nama).trigger('change')
            $('#pjpasienform-pj_alamat').val(_formPendaftaran.tmpDataPj.penanggungjawabtera_alamat).trigger('change')
        }
    }
  // }, 1);
}

function generateFormPenanggungJawab(isNew = true) {
  let _clonePj = _formPendaftaran.formInputPj;
  if(isNew) {
      $('#pasienform-is_pj').on("change", function () {
          if (this.checked) {
              let _totalStep = $('ul[role="tablist"] > li').length;
              let formPjIdx = _totalStep - 1

              $('.steps-basic').steps("insert", formPjIdx, {
                  title: "Penanggung Jawab",
                  content: _clonePj.html()
              });
              _formPendaftaran.formPenanggungJawab();
          } else {
              let _totalStep = $('ul[role="tablist"] > li').length;
              $('.steps-basic').steps("remove", formPjIdx);
          }
      });
  } else {
      generatePj(_formPendaftaran)
  }
}

function autoPilihKamarTitipan(identifier) {
  if ($(identifier).val() != '' && !isPilihKamarTitipan) {
    var _jeniskasuspenyakit_id = $(identifier).val();
    var _kelas_id = $('#kelas_ditagihkan_id').val();
    var _ruangan_id = $('#ruangan_titipan_id').val();
    var _kamarruang_id = $('#kamar_titipan_id').val();
    var _penjamin_id = $("#penjamin_id").val();
    var _is_pasientitipan = $("#pasienTitipanValue").val();

    if (!_jeniskasuspenyakit_id) {
      return new PNotify({
        title: "Terjadi Kesalahan",
        text: "Jenis Kasus belum dipilih!",
        addclass: "alert alert-warning alert-arrow-right alert-styled-right",
        type: "warning"
      });
    }
    if (!_kelas_id) {
      return new PNotify({
        title: "Terjadi Kesalahan",
        text: "Kelas Tagihan belum dipilih!",
        addclass: "alert alert-warning alert-arrow-right alert-styled-right",
        type: "warning"
      });
    }

    showLoader();
    $.ajax({
      url: '/ranap/end-point/get-default-kamar-pasien-titipan',
      data: {
        jenis_id: _jeniskasuspenyakit_id,
        kelas_id: _kelas_id,
        ruangan_id: _ruangan_id,
        penjamin_id: _penjamin_id,
        gender: jk,
        kamar_id: _kamarruang_id,
        pasien_titipan: _is_pasientitipan
      },
      success: (res) => {
        let link = document.URL;
        let patternAction = link.match(/pemesanan-kamar/g);
        const jenisTempatTidur = res.kettempattidur_id;
        const kamarruangan_jenis = res.kamarruangan_jenis;
        const kamarruangan_id = res.kamarruangan_id;
        const kamartempattidur_id = res.kamartempattidur_id;

        var kelas_rawat = $("#kelas_rawat").val();
        var is_pasienaps = $("#pasienApsValue").val();
        var carabayar_id = $("#selectCarabayar").val();

        var jk_kamar;
        var allow_jk;
        var attr = res.allow_jk;

        if(carabayar_id == 6) {
            if(typeof kelas_rawat != 'undefined') {
                if(kelas_rawat != res.kelaspelayanan_id && (is_pasientitipan == 0 && is_pasienaps == 0)) {
                    docoNotification('error', i18next.t('Perhatian'), i18next.t('Kamar Titipan atau APS harus dipilih!'));
                    return false;
                }
            }
        }
        if (typeof attr !== typeof undefined && attr !== false) {
          allow_jk = res.allow_jk;
        }
        if (parseInt(jenisTempatTidur) == 1) {
          jk_kamar = 16;
        } else if (parseInt(jenisTempatTidur) == 2) {
          jk_kamar = 15;
        } else {
          jk_kamar = jk;
        }
        if (allow_jk && allow_jk != parseInt(jk)) {
          docoNotification('error', i18next.t('Perhatian'), i18next.t('Kamar fleksibel tidak sesuai dengan jenis kelamin'));
          return false;
        }
        if (jk != jk_kamar) {
          docoNotification('error', i18next.t('Perhatian'), i18next.t('Kamar tidak sesuai dengan jenis kelamin'));
          return false;
        }
        $.ajax({
          url: '/ranap/end-point/cek-ruangan-default',
          data: {
            ruangan_id: res.ruangan_id,
            kelaspelayanan_id: res.kelaspelayanan_id,
            penjamin_id: _penjamin_id,
            kamarruanganId: kamarruangan_id,
            kamartempattidurId: kamartempattidur_id
          },
          method: 'POST',
          success: () => {
            if ($(document).find('#ruanganTitipanIdHidden').val() !== res.ruangan_id) {
              if ($('#pegawai_id').hasClass("select2-hidden-accessible")) {
                $('#pegawai_id').select2('destroy')
              }
              $.ajax({
                url: '/ranap/end-point/get-dokter-by-ruangan',
                data: {
                  ruangan_id: res.ruangan_id
                },
                method: 'GET',
                success: ({ data }) => {
                  const dataDropDownPegawai = []
                  Object.keys(data).map((itemOutput) => {
                    dataDropDownPegawai.push({
                      id: itemOutput,
                      text: data[itemOutput]
                    })
                  })
                  refreshOptionSelect2($('#pegawai_id'), dataDropDownPegawai)
                },
                complete: () => {
                  $('#pegawai_id').prop('disabled', false)
                }
              })
            }

            var _kelaspelayanan_id;

            _kelaspelayanan_id = res.kelaspelayanan_id;
            _kelaspelayanan_nama = res.kelaspelayanan_nama;

            $(document).find("#ruanganTitipanIdHidden").val(res.ruangan_id);
            $(document).find('#ruanganTitipanLabelValue').html(res.ruangan_nama);
            $(document).find('#tempattidur_titipan_id').val(res.kamartempattidur_id);
            $(document).find('#kamar_titipan_id').val(res.kamarruangan_id);

            _formPendaftaran.getKarcis(_kelaspelayanan_id, true);
          },
          error: ({ responseJSON }) => {
            docoNotification('error', responseJSON.meta.message, '')
          }
        })
      },
      error: ({ responseJSON }) => {
        docoNotification('error', responseJSON.meta.message, '')
      },
      complete: () => {
        hideLoader()
        $.unblockUI()
      }
    });
  }
}
