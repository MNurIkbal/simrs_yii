let jk = '';
const existingPageUrl = 'pendaftaran-ranap'
let existingReqData = {}
let dataBayi = {}
var alamatBy = {}
const dataDropDownPegawai = []
const dataDropDownDokter = []
const JK_UMUM = 23
const tujuanKunjTrue = 1;
const tujuanKunReset = 0;
const rujukDatangSendiri = 1;
const extension = 'sty';
var defaultPenBiaya = [];
var pasienBaruBpjs = false;
var isNew = $('input[name="chk-statuspasien"]:checked').val();

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

    _isNew = (typeof _isNew != 'undefined' ? _isNew : null);
    /** Kondisi step awal jika berubah */
    /** WIP kondisi pasien asuransi lama -> umum lama -> asuransi lama */
    if (_formPendaftaran.tipePasien.carabayar_id != _caraBayar ||
      _formPendaftaran.tipePasien.asalrujukan_id != _asalRujukan ||
      _formPendaftaran.tipePasien.tipe_pasien != _isNew ||
      _formPendaftaran.tipePasien.penjamin_id != penjaminId ||
      _formPendaftaran.tipePasien.no_rekam_medik != _pasienId) {
      $.each($('ul[role="tablist"] > li:not(.first)'), function () {
        $('.steps-basic').steps("remove", 1);
      });
      $('a[href="#finish"]').trigger('click');
      return false;
    }

    /** Kondisi Untuk Cara bayar Asuransi Non BPJS */
    if (_groupCaraBayar == docoHelper.groupJaminan &&
      (_formPendaftaran.tipePasien.no_asuransi != _formPendaftaran.tmpAsuransi.nokartuasuransi ||
        _formPendaftaran.tmpAsuransi.prevPasien != _formPendaftaran.tmpAsuransi.pasien_id)) {
      $.each($('ul[role="tablist"] > li:not(.first)'), function () {
        $('.steps-basic').steps("remove", 1);
      });
      $('a[href="#finish"]').trigger('click');
      return false;
    }

    if (_groupCaraBayar == docoHelper.groupBPJS) {
      var _execute = false;
      if (_formPendaftaran.tmpBpjs.jenis_pencarian == 2) {
        if (_formPendaftaran.tmpBpjs.jenis_pelayanan != _jenisPelayanan ||
          _formPendaftaran.tmpBpjs.jenis_kartu != _jenisKartu ||
          _formPendaftaran.tmpBpjs.no_kartu != _noKartu ||
          _formPendaftaran.tmpBpjs.jenis_pencarian != _jenisPencarian) {
          _execute = true;
        }
      } else {
        if (_formPendaftaran.tmpBpjs.asal_rujukan != _asalRujukanBpjs ||
          _formPendaftaran.tmpBpjs.no_rujukan_f != _noRujukan ||
          _formPendaftaran.tmpBpjs.jenis_pencarian != _jenisPencarian) {
          _execute = true;
        }
      }

      if (_execute) {
        $.each($('ul[role="tablist"] > li:not(.first)'), function () {
          $('.steps-basic').steps("remove", 1);
        });
        $('a[href="#finish"]').trigger('click');
        return false;
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

    if (_carabayar != 5 && _carabayar != 6) {
      if (_pasienId != null) {
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

        _data.push({
          name: 'BpjsNewForm[jenis_peserta]',
          value: $('#jenis_peserta').val()
        });

        _data.push({
          name: 'BpjsNewForm[kode_dpjp_spri]',
          value: $('#kode_dpjp').val()
        });

        _data.push({
          name: 'BpjsNewForm[info_response]',
          value: JSON.stringify(_formPendaftaran.dataReturnBpjs)
        })

        _data.push({
          name: 'BpjsNewForm[nama_dpjp_spri]',
          value: _formPendaftaran.tmpBpjs.dpjpServeText
        });
      }

      _data = _data.concat({
        name: 'is_ranap',
        value: true
      })
      if ($("#tipepasienform-is_bbl").is(':checked')) {
        _data = _data.concat({
          name: 'is_bbl',
          value: true
        })
      }
      const simpanPendaftaran = function (dataPost, extra = {}) {
        var attr = {
          url: `/pendaftaran/${existingPageUrl}/simpan-kunjungan-v2?params=${_formPendaftaran.params}`,
          data: dataPost,
          success: function (data) {
            _formPendaftaran.resetForm();
            activeTable = 'kamar'
            if (_groupCaraBayar == docoHelper.groupBPJS) {
              _formPendaftaran.resetBpjs();
              var _responseData = typeof data.response != 'undefined' ? data.response : {};
              if (_responseData.is_bpjs) {
                $("#btn-print-sep").attr("disabled", false);
                window.open(`/pendaftaran/end-point/print-sep?pendaftaran_id=${_responseData.id}`);
              }
            }
            var cetakanAcion = $("#btn-modal-cetakan").attr("action");
            $("#btn-modal-cetakan").attr("action", cetakanAcion + '&pendaftaran_id=' + data.response.id + '&is_bpjs=' + data.response.is_bpjs).click();
            // location.reload();
          },
          error: function (data) {
            var _res = data.responseJSON.response
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
                    simpanPendaftaran(newData, {
                      skipConfirm: true
                    })
                  }
                })
              }, 500)
              return true;
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

const bindCheckboxRadio = (indexPage) => {
  $(`#steps-uid-0-p-${indexPage}`).find('input[type="checkbox"], input[type="radio"]')
    .not('.notUniform').uniform({
      radioClass: 'choice'
    });
  bindRadioWithSpace($(`#steps-uid-0-p-${indexPage}`))
  bindCheckboxWithSpace($(`#steps-uid-0-p-${indexPage}`))
}

/** Class Form Pendaftaran */
var _formPendaftaran = {
  params: 'ranap',
  isForeignAsuransi: false,
  isConfirmedForeignAsuransi: false,
  pendaftaranOl: {},
  dataAntrian: null,
  dataPendaftaran: null,
  historyKunjugan: null,
  formErrorBpjs: null,
  formInputKunjugan: null,
  formInputBpjs: null,
  formInputPj: null,
  formInputPasien: null,
  formInputAsuransi: null,
  formInputRujukan: null,
  formPasienMcu: null,
  listTarif: [],
  listPenunjang: {},
  totalTarif: 0,
  tmpAsuransi: {
    pasien: {
      nama_pasien: null,
      no_rekam_medik: null,
    },
    pasien_id: null,
    no_asuransi: null,
    masaberlakukartu: null,
    namapemilikasuransi: null,
    nomorpokokperusahaan: null,
    namaperusahaan: null,
    kelastanggunganasuransi_id: null,
    prevPasien: null
  },
  resetAsuransi: function () {
    _formPendaftaran.tmpAsuransi = {
      no_rekam_medik: null,
      pasien_id: null,
      no_asuransi: null,
      namapemilikasuransi: null,
      nomorpokokperusahaan: null,
      namaperusahaan: null,
      kelastanggunganasuransi_id: null,
      masaberlakukartu: null,
      nama_asuransi: null,
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
  tmpPasien: {
    namadepan: null,
    nama_pasien: null,
    propinsi_id: null,
    kabupaten_id: null,
    kecamatan_id: null,
    kelurahan_id: null,
    rt: null,
    rw: null,
    alamat_pasien: null,
    no_telepon_pasien: null,
    pekerjaan_id: null,
  },
  tmpKunjungan: {
    ruangan_id: null,
    jeniskasuspenyakit_id: null,
    kelaspelayanan_id: null,
    pegawai_id: null,
    keadaan_masuk: null,
    tgl_pendaftaran: null,
    keterangan_pendaftaran: null,
  },
  resetPasien: function () {
    _formPendaftaran.tmpPasien = {
      namadepan: null,
      nama_pasien: null,
      propinsi_id: null,
      kabupaten_id: null,
      kecamatan_id: null,
      kelurahan_id: null,
      rt: null,
      rw: null,
      alamat_pasien: null,
      no_telepon_pasien: null,
      pekerjaan_id: null,
      pt: null
    };
  },
  tmpBpjs: {
    no_kartu: null,
    asal_rujukan: null,
    jenis_pencarian: null,
    jenis_kartu: null,
    jenis_pelayanan: null,
    no_rujukan_f: null
  },
  dataBpjs: {},
  dataPostRanap: {},
  dataReturnBpjs: {},
  dataRujukanBpjs: {},
  dataPasienMcu: [],
  tmpDataMcu: {
    dataComplete: null,
    dataIncomplete: null,
    dataFile: null,
    dataDoubleRm: null,
  },
  resetBpjs: function () {
    _formPendaftaran.tmpBpjs = {
      no_kartu: null,
      jenis_pencarian: null,
      jenis_kartu: null,
      jenis_pelayanan: null
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
    picker.set('select', [
      [date.getFullYear(), date.getMonth() + 1, date.getDate()]
    ]);
    $('.selectCarabayar').trigger("change");
  },
  init: function () {
    $('.nextRow').show();
    $(".field-no_rekam_medik").addClass('required');
    $('input[type=text]').keyup(function () {
        $(this).val($(this).val().toUpperCase());
    });

    $('textarea').keyup(function () {
        $(this).val($(this).val().toUpperCase());
    });
    $("input[name=chk-statuspasien]").change(function () {
      isNew = $('input[name=chk-statuspasien]:checked').val();
      stateNextStep('next')
      if (this.checked) {
        $('#no_rekam_medik').prop("disabled", false);
        $(".field-no_rekam_medik").addClass('required');
      } else {
        $('#no_rekam_medik').val('').trigger('change');
        $('#no_rekam_medik').prop("disabled", true);
        $(".field-no_rekam_medik").removeClass('required');
      }
    });
    bindCheckboxRadio(_formPendaftaran.indexActive)
    //$('.field-no_rekam_medik').hide();
    $('.field-tipepasienform-no_asuransi').hide();
    $('#selectCarabayar').focus();
    // $('input[name="chk-statuspasien"]').prop("checked", false);
    $(document).on('click', '.field-tipepasienform-no_asuransi span.input-group-addon', function (event) {
      event.preventDefault();
      _formPendaftaran.tipePasien.no_asuransi = null;
      _formPendaftaran.tmpAsuransi = {
        kelastanggunganasuransi_id: null,
        namapemilikasuransi: null,
        namaperusahaan: null,
        no_asuransi: null,
        nomorpokokperusahaan: null,
        masaberlakukartu: null,
        nama_asuransi: null,
        pasien_id: null,
        no_rekam_medik: null,
        prevPasien: _formPendaftaran.tmpAsuransi.prevPasien
      };
      $('#tipepasienform-no_asuransi').val('');
      $('#tipepasienform-no_asuransi').prop('readonly', false);
      $('.field-tipepasienform-no_asuransi').find("#note-asuransi").html("");
    });
    $(document).on('change', '#tipepasienform-no_asuransi', function (obj, item) {
      var _data = obj.data;
      var _value = $(this).val();

      _formPendaftaran.tipePasien.no_asuransi = "";
      _formPendaftaran.tipePasien.no_asuransi = _value;

      if (!_formPendaftaran.tmpAsuransi.pasien_id) {
        _formPendaftaran.tmpAsuransi.no_asuransi = null;
        $('.field-tipepasienform-no_asuransi').find("#note-asuransi").html("");
      } else {
        _formPendaftaran.tipePasien.no_asuransi = null;
        _formPendaftaran.tmpAsuransi.no_asuransi = _value
        $('#tipepasienform-no_asuransi').prop('readonly', false);
      }

      if (_formPendaftaran.tmpAsuransi.no_rekam_medik == null) {
        _formPendaftaran.tmpAsuransi.no_rekam_medik = $("#no_rekam_medik").val();
      }

      var _content = $('#form-asuransi');

      renderPickadate($(_content.find('#masaberlakukartu')), {
        dependElementPicker: _content.find('#btn_addon_tgllahir').parent(),
        defaultValue: typeof _formPendaftaran.tmpAsuransi.masaberlakukartu !== 'undefined' && _formPendaftaran.tmpAsuransi.masaberlakukartu !== null ? _formPendaftaran.tmpAsuransi.masaberlakukartu : new Date()
      });

      $('#asuransiform-kelastanggungan_id').select2();
      $('#asuransiform-kelastanggungan_id').on('select2:close', ({
        delegateTarget
      }) => {
        $(delegateTarget).focus()
      })
      _content.find('input').attr("autocomplete", "off");

      if (_formPendaftaran.tipePasien.no_asuransi != "") {
        if (_formPendaftaran.tmpAsuransi.namapemilikasuransi != null)
          _content.find('#asuransiform-namapemilikasuransi').val(_formPendaftaran.tmpAsuransi.namapemilikasuransi);
        if (_formPendaftaran.tmpAsuransi.nama_asuransi != null)
          _content.find('#asuransiform-nama_asuransi').val(_formPendaftaran.tmpAsuransi.nama_asuransi);
      } else {
        _content.find('#asuransiform-namapemilikasuransi').val(_formPendaftaran.tmpAsuransi.namapemilikasuransi);
        _content.find('#asuransiform-nama_asuransi').val(_formPendaftaran.tmpAsuransi.nama_asuransi);
      }

      _content.find('#asuransiform-nomorpokokperusahaan').val(_formPendaftaran.tmpAsuransi.nomorpokokperusahaan);
      _content.find('#asuransiform-kelastanggungan_id')
        .val(_formPendaftaran.tmpAsuransi.kelastanggunganasuransi_id)
        .trigger('change');
      _content.find('#asuransiform-namaperusahaan')
        .val(_formPendaftaran.tmpAsuransi.namaperusahaan);
      _content.find('#asuransiform-masaberlakukartu')
        .val(_formPendaftaran.tmpAsuransi.masaberlakukartu)
        .trigger('change');
    });
    $('#no_rekam_medik').select2({
      allowClear: false,
      ajax: {
        url: '/pendaftaran/end-point/norm',
        dataType: 'json',
        data: function (params) {
          return {
            q: params.term,
            isRanap: 0,
            isBbl: $("#tipepasienform-is_bbl").is(':checked')
          };
        },
      },
      placeholder: 'No Rm / Nama pasien / Tanggal lahir',
      minimumInputLength: 3,
      templateResult: function (noRm) {
        return noRm.text;
      },
      templateSelection: function (noRm) {
        Object.assign(existingReqData, {
          jeniskasuspenyakit_id: JK_UMUM,
          kelaspelayanan_id: noRm.kelaspelayanan_id,
        })
        $(".pendaftaran-id").val(noRm.pendaftaran_id);
        $("#pasien_id_hidden").val(noRm.pasien_id);
        return noRm.text;
      }
    });
    $('#no_rekam_medik').on("change", function ({
      delegateTarget
    }) {
      var no_rekam_medik = $(this).val();
      var pasien_id = $("#pasien_id_hidden").val();
      var carabayar_id = null;
      if ($(delegateTarget).val() !== '') {
        $("#selectCarabayar").val('').trigger('change').trigger('depdrop:change');
        _formPendaftaran.getInfoPasien('', $(delegateTarget).val(), true, true)
        validasiKunjungan($(this).val())
      }
      carabayar_id = $(".selectCarabayar").val();
      if ((carabayar_id == 41 || carabayar_id == 44 || carabayar_id == 45) && no_rekam_medik != "") {
        $.ajax({
          url: '/pendaftaran/pendaftaran-ranap/get-penanggung-biaya?q=' + pasien_id + "&carabayar_id=" + carabayar_id,
          type: 'GET',
          dataType: 'JSON',
          success: function (res) {
            if (res.response != "") {
              var response = res.results;
              if (response !== null) {
                var _content = $('#form-penanggung');
                _content.find('#form-tipepasien-penanggungbiaya_nama')
                  .val(response.penanggungbiaya_nama);
                _content.find('#form-tipepasien-instansi')
                  .val(response.instansi);
                _content.find('#form-tipepasien-namabagian')
                  .val(response.namabagian);
                _content.find('#form-tipepasien-noindukkaryawan')
                  .val(response.noindukkaryawan);
                _content.find('#form-tipepasien-jpkm')
                  .val(response.jpkm);
              }
            }
          },
          error: function (err) {
            console.log("error get pasien id");
            console.log(err);
          }
        });
      }
      if (carabayar_id == 39 && no_rekam_medik != "") {
        if (typeof penjamin_id != 'undefined' || penjamin_id != "") {
          $.ajax({
            url: '/pendaftaran/end-point/get-no-asuransi?no_rekam_medik=' + no_rekam_medik + "&carabayar_id=" + carabayar_id + "&penjamin_id=" + penjamin_id,
            type: 'GET',
            dataType: 'JSON',
            success: function (res) {
              if (res.response != "") {
                var response = res.response;
                if (response !== null) {
                  var _data_asuransi = response.data_asuransi;
                  var _nama_pasien = response.nama_pasien;
                  if (_data_asuransi != null) {
                    $("#tipepasienform-no_asuransi").val(_data_asuransi.nokartuasuransi).trigger("change");
                    $("#tipepasienform-no_asuransi").prop("readonly", false);
                    _formPendaftaran.tmpAsuransi.no_rekam_medik = no_rekam_medik;
                    _formPendaftaran.tmpAsuransi.pasien_id = pasien_id;
                    _formPendaftaran.tmpAsuransi.no_asuransi = _data_asuransi.nokartuasuransi;
                    _formPendaftaran.tmpAsuransi.namapemilikasuransi = _data_asuransi.namapemilikasuransi;
                    _formPendaftaran.tmpAsuransi.nomorpokokperusahaan = _data_asuransi.nomorpokokperusahaan;
                    _formPendaftaran.tmpAsuransi.namaperusahaan = _data_asuransi.namaperusahaan;
                    _formPendaftaran.tmpAsuransi.kelastanggunganasuransi_id = _data_asuransi.kelastanggunganasuransi_id;
                    _formPendaftaran.tmpAsuransi.masaberlakukartu = _data_asuransi.masaberlakukartu;
                    _formPendaftaran.tmpAsuransi.nama_asuransi = _data_asuransi.nama_asuransi;

                    $('.field-tipepasienform-no_asuransi').find('#note-asuransi').html('<i>Atas Nama : ' + _formPendaftaran.tmpAsuransi.namapemilikasuransi + '<br>Pasien Pengguna Asuransi : ' + _nama_pasien + '<br> Nomor Rekam Medik Pasien : ' + _formPendaftaran.tmpAsuransi.no_rekam_medik);

                    var _content = $('#form-asuransi');

                    _content.find('#asuransiform-namapemilikasuransi').val(_formPendaftaran.tmpAsuransi.namapemilikasuransi);
                    _content.find('#asuransiform-nomorpokokperusahaan').val(_formPendaftaran.tmpAsuransi.nomorpokokperusahaan);
                    _content.find('#asuransiform-kelastanggungan_id')
                      .val(_formPendaftaran.tmpAsuransi.kelastanggunganasuransi_id)
                      .trigger('change');
                    _content.find('#asuransiform-namaperusahaan')
                      .val(_formPendaftaran.tmpAsuransi.namaperusahaan);
                    _content.find('#asuransiform-masaberlakukartu')
                      .val(_formPendaftaran.tmpAsuransi.masaberlakukartu);
                    _content.find('#asuransiform-nama_asuransi')
                      .val(_formPendaftaran.tmpAsuransi.nama_asuransi);
                  }
                }
              }
            },
            error: function (err) {
              console.log("error get no asuransi");
              console.log(err);
            }
          });
        }
      }
      if (no_rekam_medik != "") {
        $.ajax({
          url: '/pendaftaran/pendaftaran-rajal/cek-retensi?no_rekam_medik=' + no_rekam_medik,
          type: 'GET',
          dataType: 'JSON',
          success: function (res) {
            if (res.results != "") {
              var response = res.results;
              if (response !== null) {
                if (response.NORM != null) {
                  docoNotification('error', 'Pasien sudah diretensi dan tidak bisa melakukan pendaftaran', '')
                  _formPendaftaran.is_retensi = 1;
                } else {
                  _formPendaftaran.is_retensi = 0;
                }
              } else {
                _formPendaftaran.is_retensi = 0;
              }
            } else {
              _formPendaftaran.is_retensi = 0;
            }
          },
          error: function (err) {
            console.log("error cek retensi pasien");
            console.log(err);
          }
        });
      }
      //Check Kunjungan
      $.ajax({
        url: '/pendaftaran/daftar/get-kunjungan?no_rekam_medik=' + $(this).val(),
        type: 'GET',
        dataType: 'JSON',
        success: function (res) {
          $(".nextRow").show();
          if (res.results != "") {
            var response = res.results;
            if (response !== null) {
              if (response.tglpasienpulang != null) {
                docoNotification('warning', 'Perhatian!', 'Pasien Sudah Berkunjung dan Dipulangkan Dari Ruangan ' + response.ruangan_nama + '<br/>Pendaftaran Masih Bisa Tetap Dilanjutkan.')
              } else {
                docoNotification('warning', 'Perhatian!', 'Pasien Sudah Terdaftar di Ruangan ' + response.ruangan_nama + '<br/>Pendaftaran Masih Bisa Tetap Dilanjutkan.')
              }
            }
          }
        },
        error: function (err) {
          console.log("error cek kunjungan");
          console.log(err);
        }
      });
    });

    _formPendaftaran.historyKunjugan = $('#kunjungan').clone(true);
    _formPendaftaran.formInputRujukan = $('#form-rujukan').clone(true);
    _formPendaftaran.formInputKunjugan = $('#form-input-kunjugan').clone(true);
    _formPendaftaran.formInputPj = $('#form-input-pj').clone(true);
    _formPendaftaran.formInputPasien = $('#form-input-pasien').clone(true);
    _formPendaftaran.formInputAsuransi = $('#form-input-asuransi').clone(true);
    _formPendaftaran.formInputBpjs = $('#form-input-bpjs').clone(true);
    _formPendaftaran.formErrorBpjs = $('#form-bpjs-error').clone(true);
    _formPendaftaran.formPasienMcu = $('#form-pasien-mcu').clone(true);
    $('#form-input-kunjugan > .select2').select2("destroy");
    $('#form-input-pj > .select2').select2("destroy");
    $('#form-input-pasien > .select2').select2("destroy");
    $('#list-history').remove();
    $('#form-input-kunjugan').remove();
    $('#form-rujukan').remove();
    $('#form-input-pj').remove();
    $('#form-input-pasien').remove();
    $('#form-input-asuransi').remove();
    $('#form-input-bpjs').remove();
    $('#form-bpjs-error').remove();
    $('#form-pasien-mcu').remove();
    /** Untuk BPJS */
    $('.styled, .multiselect-container input').uniform({
      radioClass: 'choice'
    });
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
    var date = new Date();
    $('#tanggal_sep_1, #tanggal_sep, #tanggal_rujukan, #tanggal_kejadian').pickadate({
        format: 'dd mmm yyyy',
        formatSubmit: 'yyyy-mm-dd',
        max: [date.getFullYear(),date.getMonth(),date.getDate()],
        onStart: function () {
            this.set('select', [[date.getFullYear(), date.getMonth() + 1, date.getDate()]]);
        }
    });
    $("#tanggal_sep_1").prop("readonly", false); 
    $(document).on('change', '.selectCarabayar', function () {
      var carabayar = $('.selectCarabayar').val();
      var carabayar_group = $('.selectCarabayar').find(':selected').attr('data-id');
      $("#kunjunganform-group_carabayar").val(carabayar_group);
      $("#carabayar_id_hidden").val(carabayar);

      if (instalasi == instalasiMcu) {
        if ($("input[name='TipePasienForm[is_kolektif]']")[1]['checked']) {
          stateCaraBayar(carabayar_group)
        } else {
          $('.field-no_rekam_medik').hide();
        }
      } else {
        stateCaraBayar(carabayar_group)
      }

      var _pasienIdOl = $('#pasien_ol_status').val();
      if (_pasienIdOl != "" && _pasienIdOl == "310") { //status pasien baru
        $('input[name="chk-statuspasien"]').prop("checked", false);
        $('#no_rekam_medik').val('').trigger('change');
        $('#no_rekam_medik').prop("disabled", true);
        $(".field-no_rekam_medik").removeClass('required');
      } else {
        $('#no_rekam_medik').prop("disabled", false);
        $('input[name="chk-statuspasien"]').prop("checked", true);
      }

      _formPendaftaran.resetAsuransi();
      // $(".field-no_rekam_medik").addClass('required');
      // $('#no_rekam_medik').val('').trigger('change');
      $("#note-asuransi").html("");
      $("#tipepasienform-no_asuransi").val("").trigger("change");

      $('#form-tipepasien-namadepan').val("").trigger("change");
      $('#form-tipepasien-nama_pasien').val("");
      $('#form-tipepasien-kecamatan_id').val("").trigger("change");
      $('#form-tipepasien-kelurahan_id').val("").trigger("change");
      $('#form-tipepasien-rt').val("");
      $('#form-tipepasien-rw').val("");
      $('#form-tipepasien-alamat_pasien').val("");
      $('#form-tipepasien-no_telepon_pasien').val("");
      $('#form-tipepasien-pekerjaan_id').val("").trigger("change");
      $('#form-tipepasien-penanggungbiaya_nama').val("");
      $('#form-tipepasien-instansi').val("");
      $('#form-tipepasien-namabagian').val("");
      $('#form-tipepasien-noindukkaryawan').val("");
      $('#form-tipepasien-jpkm').val("");
      $("input[name='PjpasienForm[pj_pengantar]']").closest('span').removeClass('checked')
      $("input[name='PjpasienForm[pj_pengantar]']").closest('div').removeClass('checked')
      $("input[name='PjpasienForm[pj_pengantar]'][value=990]").prop('checked', true).trigger('change');
      $("input[name='PjpasienForm[pj_pengantar]'][value=990]").closest('span').addClass('checked')
      $("input[name='PjpasienForm[pj_pengantar]'][value=990]").closest('div').addClass('checked')

      _formPendaftaran.resetPasien();
      // Refresh penjamin_id
      if (isNew != 1) { //status pasien baru
        $('input[name="chk-statuspasien"]').prop("checked", false);
        $('#no_rekam_medik').val('').trigger('change');
        $('#no_rekam_medik').prop("disabled", true);
        $(".field-no_rekam_medik").removeClass('required');
      } else {
        $('#no_rekam_medik').prop("disabled", false);
        $('input[name="chk-statuspasien"]').prop("checked", true);
      }
      $.ajax({
        url: '/pendaftaran/daftar/get-penjamin',
        data: {
          depdrop_parents: [
            $("#selectCarabayar").val()
          ]
        },
        method: 'POST',
        success: (res) => {
          $('#penjamin_id').prop('disabled', false);
          $("#penjamin_id").select2('destroy')
          $("#penjamin_id").html('')
          res.output.map(({
            id,
            name
          }) => {
            $("#penjamin_id").append(`<option value="${id}">${name}</option>`)
          })
          $("#penjamin_id").select2()
          $("#penjamin_id").val(null).trigger('change');
          if (existingReqData.newPasien) {
            $("#penjamin_id").val(existingReqData.penjamin_id).trigger('change').trigger('depdrop:change');
            existingReqData.newPasien = false;
          }
          if (res.selected == penjamin_umum) {
            $("#asalrujukan_id").val(rujukan_datang_sendiri).trigger('change');
          }
        }
      });

      if (defaultPenBiaya[carabayar] !== undefined) {
        $('#penjamin_id').val(defaultPenBiaya[carabayar].penjamin_id)
            .trigger('change')
            .trigger('depdrop:change');

        var defAsalRujukan = defaultPenBiaya[carabayar].asal_rujukan_id;
        if (defaultPenBiaya[carabayar].asal_rujukan_id == null) {
            defAsalRujukan = rujukDatangSendiri;
        }
        $('#asalrujukan_id').val(defAsalRujukan)
            .trigger('change')
            .trigger('depdrop:change');

        if(defaultPenBiaya[carabayar].namapemilik_asuransi != null){
            $('#asuransiform-namapemilikasuransi')
                .val(defaultPenBiaya[carabayar].namapemilik_asuransi)
                .trigger('change');
        }

        if(defaultPenBiaya[carabayar].no_asuransi != null){
            $('#tipepasienform-no_asuransi')
                .val(defaultPenBiaya[carabayar].no_asuransi)
                .trigger('change');
        }

        if(defaultPenBiaya[carabayar].nopokokperusahaan != null){
            $('#asuransiform-nomorpokokperusahaan')
                .val(defaultPenBiaya[carabayar].nopokokperusahaan)
                .trigger('change');
        }

        if(defaultPenBiaya[carabayar].namaperusahaan != null){
            $('#asuransiform-namaperusahaan')
                .val(defaultPenBiaya[carabayar].namaperusahaan)
                .trigger('change');
        }

        if(defaultPenBiaya[carabayar].penanggungbiaya_nama != null){
            $('#form-tipepasien-penanggungbiaya_nama')
                .val(defaultPenBiaya[carabayar].penanggungbiaya_nama)
                .trigger('change');
        }

        $('#ruangcarabayar_id').on('depdrop:afterChange', function (event, id, value) {
            $('#ruangcarabayar_id')
                .val(defaultPenBiaya[carabayar].ruangcarabayar_id)
                .trigger('change');
            $('#ruangcarabayar_id').trigger('depdrop:change');
        });

        if(defaultPenBiaya[carabayar].noindukkaryawan != null){
            $('#form-tipepasien-noindukkaryawan')
                .val(defaultPenBiaya[carabayar].noindukkaryawan)
                .trigger('change');
        }

        if(defaultPenBiaya[carabayar].instansi != null){
            $('#form-tipepasien-instansi')
                .val(defaultPenBiaya[carabayar].instansi).trigger('change');
        }

        if(defaultPenBiaya[carabayar].namabagian != null){
            $('#form-tipepasien-namabagian')
                .val(defaultPenBiaya[carabayar].namabagian);
        }
    }
      //getPj(_formPendaftaran.tmpDataPj.idPj);
    });

    $(document).on("change", "#form-pj-pengantar", function () {
      var pj_pengantar = $("input[name='PjpasienForm[pj_pengantar]']:checked").val();
      if (pj_pengantar == 992) {
        $('#form-pasien').show();
      } else {
        $('#form-pasien').hide();
      }
    });
    $(document).on('select2:close', '.select2-hidden-accessible', ({
      currentTarget
    }) => {
      $(currentTarget).focus()
    })
    $(document).on('change', '#tipepasienform-is_kolektif', function () {
      _formPendaftaran.resetForm()
      _formPendaftaran.resetMcu()
      if ($("input[name='TipePasienForm[is_kolektif]']")[2]['checked']) {
        $('#row-template').prop('hidden', false)
      } else {
        $('#row-template').prop('hidden', true)
      }
    })
    _formPendaftaran.additional();

    $("input[name='BpjsNewForm[jenis_kartu]']").on('change', function () {
      var jenisKartu = $("input[name='BpjsNewForm[jenis_kartu]']:checked").val();
      if (jenisKartu == '1') {
        $("#no_kartu").attr('maxlength', '13');
        $("#no_kartu").val('');
      } else {
        $("#no_kartu").attr('maxlength', '16');
        $("#no_kartu").val('');
      }
    });
  },
  additional: function () {

  },
  indexActive: 0,
  generateForm: function () {
    var _jenisPendaftaran;
    var isKolektif = false //mcu
    if (_formPendaftaran.params == 'penunjang' && $("input[name='TipePasienForm[is_aps]']")[2]['checked'] === true) {
      _jenisPendaftaran = 0;
    } else {
      _jenisPendaftaran = 1;
    }

    if (_jenisPendaftaran == 1) {
      var _pasienId = $('#no_rekam_medik').val();
      var _caraBayar = $('#selectCarabayar').val();
      var _penjamin = $('#penjamin_id').val();
      var _asalRujukan = $('#asalrujukan_id').val();
      var _groupCaraBayar = $('.selectCarabayar').find(':selected').attr('data-id');
      var _tipeId = $('input[name=chk-statuspasien]:checked').val();
    } else {
      var _pasienId = $('#pasienrs_pasien_id_hidden').val();
      var _caraBayar = $('#pasienrs_carabayar_id_hidden').val();
      var _penjamin = $('#pasienrs_penjamin_id_hidden').val();
      var _asalRujukan = "0";
      var _groupCaraBayar = $('#pasienrs_group_carabayar_hidden').val();
      var _noPendaftaran = $('#pasienrs_no_pendaftaran_hidden').val();
      var _tipeId = 1;
    }

    var _noAsuransi = _formPendaftaran.tmpAsuransi.no_asuransi ?
      _formPendaftaran.tmpAsuransi.no_asuransi : $('#tipepasienform-no_asuransi').val();
    var _namapemilikasuransi = $('#asuransiform-namapemilikasuransi').val();
    var _nomorpokokperusahaan = $('#asuransiform-nomorpokokperusahaan').val();
    var _kelastanggungan_id = $('#asuransiform-kelastanggungan_id').val();
    var _namaperusahaan = $('#asuransiform-namaperusahaan').val();
    var _masaberlakukartu = $('#masaberlakukartu').val();
    var _status_konfirmasi = $('#asuransiform-status_konfirmasi').val();
    var _nama_asuransi = $('#asuransiform-nama_asuransi').val();
    /** Kebutuhan Untuk BPJS */
    var _bpjs = {};
    var _pencarianBpjs = $("input[name='BpjsNewForm[jenis_rujukan]']:checked").val();
    var _jenisKartu = $("input[name='BpjsNewForm[jenis_kartu]']:checked").val();
    var _tglSep = $("input[name='BpjsNewForm[tanggal_sep]']").val();
    var _jenisPelayanan = $("select[name='BpjsNewForm[jenis_pelayanan]']").val();
    var _noKartu = $("input[name='BpjsNewForm[no_kartu]']").val();
    var _noRujukan = $("input[name='BpjsNewForm[no_rujukan_f]']").val();
    var _asalRujukanBpjs = $("select[name='BpjsNewForm[asal_rujukan]']").val();
    /** end Bpjs */
    /** Kebutuhan untuk data pasien */
    /*var _namadepan = $('#form-tipepasien-namadepan').val();
    var _nama_pasien = $('#form-tipepasien-nama_pasien').val();
    var _propinsi_id = $('#form-tipepasien-propinsi_id').val();
    var _kabupaten_id = $('#form-tipepasien-kabupaten_id').val();
    var _kecamatan_id = $('#form-tipepasien-kecamatan_id').val();
    var _kelurahan_id = $('#form-tipepasien-kelurahan_id').val();
    var _rt = $('#form-tipepasien-rt').val();
    var _rw = $('#form-tipepasien-rw').val();
    var _alamat_pasien = $('#form-tipepasien-alamat_pasien').val();
    var _no_telepon_pasien = $('#form-tipepasien-no_telepon_pasien').val();
    var _pekerjaan_id = $('#form-tipepasien-pekerjaan_id').val();*/
    /** end data pasien */

    _tipeId = (typeof _tipeId != 'undefined' ? _tipeId : null);
    $('#ket-bpjs').hide();

    var _data = {};
    let isKonsul = false
    var skipBpjs = 0
    /** Case Daftar Konsul BPJS Hari sama */
    if (typeof _formPendaftaran.pendaftaranOl.buatjanjipoli_id !== 'undefined' && _formPendaftaran.pendaftaranOl.status_janji == true) {
      isKonsul = true
    }
    var _noindukkaryawan = $("input[name='PenanggungBiayaForm[noindukkaryawan]']").val();
    var _penanggungbiaya_nama = $("input[name='PenanggungBiayaForm[penanggungbiaya_nama]']").val();
    var _namabagian = $('#form-tipepasien-namabagian').val();
    var _instansi = $("input[name='PenanggungBiayaForm[instansi]']").val();
    var _ruangcarabayar_id = $("#ruangcarabayar_id").val();
    if (_jenisPendaftaran == 1) {
      _data = {
        carabayar_id: _caraBayar,
        penjamin_id: _penjamin,
        asalrujukan_id: _asalRujukan,
        groupcarabayar_id: _groupCaraBayar,
        no_rekam_medik: _pasienId,
        tipe_pasien: _tipeId,
        no_asuransi: _noAsuransi,
        noindukkaryawan: _noindukkaryawan,
        namapemilikasuransi: _namapemilikasuransi,
        nomorpokokperusahaan: _nomorpokokperusahaan,
        kelastanggungan_id: _kelastanggungan_id,
        namaperusahaan: _namaperusahaan,
        masaberlakukartu: _masaberlakukartu,
        status_konfirmasi: _status_konfirmasi,
        nama_asuransi: _nama_asuransi,
        penanggungbiaya_nama: _penanggungbiaya_nama,
        namabagian: _namabagian,
        instansi: _instansi,
        ruangcarabayar_id: _ruangcarabayar_id,
      };
    } else {
      _data = {
        carabayar_id: _caraBayar,
        penjamin_id: _penjamin,
        no_rekam_medik: _pasienId,
        asalrujukan_id: _asalRujukan,
        groupcarabayar_id: _groupCaraBayar,
        tipe_pasien: _tipeId,
        jenis_pendaftaran: 'pasien-rs',
        no_pendaftaran: _noPendaftaran
        // no_asuransi: _noAsuransi,
      };
    }

    if (_groupCaraBayar == docoHelper.groupBPJS) {
      _bpjs = {
        is_bpjs: true,
        no_kartu: _noKartu,
        jenis_pencarian: _pencarianBpjs,
        jenis_kartu: _jenisKartu,
        jenis_pelayanan: _jenisPelayanan,
        tanggal_sep: _tglSep,
        asal_rujukan: _asalRujukanBpjs,
        no_rujukan_f: _noRujukan,
        asalrujukan_id: 1,
        skipBpjs: ""
      };
    }

    if (isKonsul) {
      _data.penjamin_id = _formPendaftaran.pendaftaranOl.penjamin_id
      _bpjs.jenis_pendaftaran = 'pasien-rs'
    }

    if (instalasi == instalasiMcu) {
      if ($("input[name='TipePasienForm[is_kolektif]']")[2]['checked']) {
        isKolektif = true
        _data.is_kolektif = isKolektif
      }
    }

    _data.is_retensi = _formPendaftaran.is_retensi
    var _dataPost = $.extend({}, _data, _bpjs);
    $().docoForm('click', {
      url: `/pendaftaran/${existingPageUrl}/validation-tipe-pasien-v2`,
      // url: '/pendaftaran/daftar-' + _formPendaftaran.params + '/validation-tipe-pasien',
      data: _dataPost,
      skipConfirm: true,
      skipSuccessNotif: true,
      success: function (data) {
        var _findPasien = false;
        var _noRmBpjs = null;
        $('#btn-edit-info-pasien').prop('disabled', false)
        _formPendaftaran.tipePasien = _data;
        _formPendaftaran.tmpAsuransi.no_asuransi = _data.no_asuransi;
        _formPendaftaran.tmpAsuransi.prevPasien = _formPendaftaran.tmpAsuransi.pasien_id;
        var _noRm = _formPendaftaran.tipePasien.no_rekam_medik;
        var _pasienId = $("#pasien_id_hidden").val();
        _pasienId = (_pasienId == "") ? null : _pasienId;
        // var _pasienId = $('#no_rekam_medik').val();

        if (_groupCaraBayar == docoHelper.groupJaminan) {
          // _formPendaftaran.formAsuransi();
          // if(_pasienId == null) {
          //   _formPendaftaran.formPasien();
          // }
          // // _pasienId = _formPendaftaran.tmpAsuransi.pasien_id;
          // // _noRm = _pasienId;
          // // _data.tipe_pasien = 1;
          // $("#tipepasienform-no_asuransi").prop("readonly", false);
          // // get data asuransi by no asuransi
          implementAsuransiData();
          // _formPendaftaran.formKunjungan()
        } else if (_groupCaraBayar == docoHelper.groupBPJS) {
          _formPendaftaran.tmpBpjs = _bpjs;
          let _statRujukan = false;
          pasienBaruBpjs = false;
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
            _formPendaftaran.errorBpjs();
            _formPendaftaran.generateBpjs(_pencarianBpjs, _asalRujukan, _groupCaraBayar, _noRm);
            return true;
          }

          if (_formPendaftaran.dataReturnBpjs.pasien_baru === false) {
            if (_formPendaftaran.dataReturnBpjs.pasien_sesuai === false) {
              setTimeout(function () {
                var _title = `<b>Peringatan !</b><br>
                    Nama Pasien yang diinputkan berbeda dengan Nama BPJS<br>
                    Nama Pasien BPJS : <strong>${_formPendaftaran.dataReturnBpjs.peserta.nama}</strong><br>
                    Pasien yang terdaftar: <strong>${_formPendaftaran.dataReturnBpjs.nama_pasien}</strong><br>
                    Apakah Anda yakin akan melanjutkan proses?`;
                confirmationDialog(_title, function (cond) {
                  if (cond) {
                    _formPendaftaran.generateBpjs(_pencarianBpjs, _asalRujukan, _groupCaraBayar, _noRm)
                  }
                  // else {
                  //     _formPendaftaran.findRmBpjs()
                  // }
                })
              }, 100)
              return true
            }
          } else {
            setTimeout(function () {
              confirmationDialog("Data pasien tidak ditemukan, Apakah anda ingin melanjutkan dengan pasien baru ?", function (cond) {
                if (cond) {
                  pasienBaruBpjs = true
                  _formPendaftaran.generateBpjs(_pencarianBpjs, _asalRujukan, _groupCaraBayar, _noRm)
                } else {
                  _formPendaftaran.findRmBpjs()
                }
              })
            }, 100)
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

        if ($("#tipepasienform-is_bbl").is(':checked') && _groupCaraBayar != docoHelper.groupBPJS) {
          _formPendaftaran.formPasien();
        }

        var _tempNew = $('input[name="chk-statuspasien"]:checked').val();
        if (typeof _tempNew == 'undefined' && _groupCaraBayar != docoHelper.groupBPJS) {
          _formPendaftaran.formPasien();
        }

        /** kondisi pasien baru atau pasien lama */
        if ((_formPendaftaran.tipePasien.tipe_pasien && _noRm) || _findPasien) {
          if (Object.keys(_formPendaftaran.pendaftaranOl).length == 0) {
            _formPendaftaran.getInfoPasien(_pasienId, _noRm);
          }
        }

        _formPendaftaran.formKunjungan();

        $('.steps-basic').steps("next");
      }
    });
  },
  findRmBpjs: function () {
    var _target = $('#btn-pencarian-lanjutan');
    var _originAction = _target.attr('action');
    _target.attr('action', `${_originAction}?is_bpjs=MQ`)
    $('#btn-pencarian-lanjutan').trigger('click')
    _target.attr('action', `${_originAction}`)
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
    if (_formPendaftaran.dataReturnBpjs.pasien_baru === true) {
      _formPendaftaran.formPasien();
    }

    /** kondisi pasien baru atau pasien lama */
    if ((_formPendaftaran.tipePasien.tipe_pasien && _noRm) || _findPasien) {
      $('#form-parent-info').show();
      $('#form-parent').removeClass("col-md-12").addClass("col-md-9");
      $('#btn-edit-info-pasien').prop('disabled', true)
      if (_formPendaftaran.dataReturnBpjs.pasien_baru === false) {
        _formPendaftaran.getInfoPasien(_pasienId, _noRm, false);
      }
    } else {
      // if (Object.keys(_formPendaftaran.pendaftaranOl).length == 0) {
      $('#form-parent-info').hide();
      $('#form-parent').removeClass("col-md-9").addClass("col-md-12");
      // }
    }

    if (_asalRujukan != 1 && _groupCaraBayar != docoHelper.groupBPJS) {
      _formPendaftaran.formRujukan();
    }

    generateFormKunjungan()

  },
  errorBpjs: function () {
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
  },
  formPasien: function (steps) {
    var _clone = _formPendaftaran.formInputPasien;
    var _tempNew = $('input[name="chk-statuspasien"]:checked').val();
    if (typeof _tempNew == 'undefined') {
      if (typeof steps != 'undefined') {
        /** dari BPJS */
        $('.steps-basic').steps("insert", steps, {
          title: "Data Pasien",
          content: _clone.html()
        });

        if (typeof _formPendaftaran.dataBpjs.mr != 'undefined') {
          setTimeout(function () {
            if (_formPendaftaran.dataBpjs.nik) {
              $('#frm-pasien-jenisidentitas').val(94).trigger('change');
            }
            $('#frm-pasien-no_identitas_pasien').val(_formPendaftaran.dataBpjs.nik).trigger('blur');
            $('#frm-pasien-nama_pasien').val(_formPendaftaran.dataBpjs.nama);
            $('#frm-pasien-no_mobile_pasien').val(_formPendaftaran.dataBpjs.mr.noTelepon);
            $('#frm-pasien-no_telepon_pasien').val(_formPendaftaran.dataBpjs.mr.noTelepon);
          }, 1000);
        }
      } else {
        $('.steps-basic').steps("add", {
          title: "Data Pasien",
          content: _clone.html()
        });
      }
      /** Event after generate form */
      var _content = $('#form-pasien-content');
      _content.find('span.select2').remove();
      _content.find('.select2').select2();
      _content.find('input').attr("autocomplete", "off");

      //Add required to NIK
      $('.field-frm-pasien-no_identitas_pasien').addClass('required');
      /** binding region */
      setRegionData('/pendaftaran/end-point/region-list', {
        province: {
          element: $("#frm-pasien-propinsi_id"),
          value: propinsi_id
        },
        city: {
          element: $("#kabupatenForm"),
          value: kabupaten_id
        },
        district: {
          element: $("#frm-pasien-kecamatan_id"),
          value: ''
        },
        village: {
          element: $("#frm-pasien-kelurahan_id"),
          value: ''
        },
      })
      var kodeKab = '73';
      var _kabupaten = $('#kabupatenForm');
      _kabupaten.attr('data-code', kodeKab);
      $("#frm-pasien-propinsi_id").trigger('change');

      $('input[type=text]').keyup(function () {
        $(this).val($(this).val().toUpperCase());
      });

      $('textarea').keyup(function () {
        $(this).val($(this).val().toUpperCase());
      });

      var ctrlDown = false;
      var ctrlKey;
      var iKey;

      // When focus in keluarga pasien nama
      $('#keluargapasienform-keluarga_nama').focus(function () {
        ctrlKey = 17;
        iKey = 73;

        // Document Ctrl + i
        $(document).keydown(function (e) {
          if (e.keyCode == ctrlKey) {
            ctrlDown = true;
          }

          if (ctrlDown && (e.keyCode == iKey)) {
            var nama_pasien = $('#frm-pasien-nama_pasien').val();
            var namadepan = $('#frm-pasien-namadepan').val();
            var alamat = $('#frm-pasien-alamat_pasien').val();
            var no_telepon = $('#frm-pasien-no_telepon_pasien').val();
            var rt = $('#frm-pasien-rt').val();
            var rw = $('#frm-pasien-rw').val();
            var pekerjaan = $('#frm-pasien-pekerjaan_id').val();
            var propinsi = $('#frm-pasien-propinsi_id').val();
            var kota = $('#kabupatenForm').val();
            var kecamatan = $('#frm-pasien-kecamatan_id').val();
            var keluarahan = $('#frm-pasien-kelurahan_id').val();

            $('#keluargapasienform-keluarga_nama').val(nama_pasien);
            $('#keluargapasienform-keluarga_namadepan').val(namadepan).trigger('change');
            $('#keluargapasienform-keluarga_alamat').val(alamat);
            $('#keluargapasienform-keluarga_no_telepon').val(no_telepon);
            $('#form-keluarga-rt').val(rt);
            $('#form-keluarga-rw').val(rw);
            $('#keluargapasienform-keluarga_pekerjaan_id').val(pekerjaan).trigger('change');
            $('#form-keluarga-propinsi_id').val(propinsi).trigger('change');
            $('#form-keluarga-kabupaten_id').val(kota).trigger('change').trigger('depdrop:change');
            $('#form-keluarga-kecamatan_id').val(kecamatan).trigger('change').trigger('depdrop:change');
            $('#form-keluarga-kecamatan_id').on('depdrop:afterChange', function (event, id, value) {
              $('#form-keluarga-kecamatan_id').val(kecamatan).trigger('change')
              $('#form-keluarga-kecamatan_id').trigger('depdrop:change');
            });
            $('#form-keluarga-kelurahan_id').val(keluarahan).trigger('change').trigger('depdrop:change');
            $('#form-keluarga-kelurahan_id').on('depdrop:afterChange', function (event, id, value) {
              $('#form-keluarga-kelurahan_id').val(keluarahan).trigger('change')
              $('#form-keluarga-kelurahan_id').trigger('depdrop:change');
            });

            ctrlDown = false;
          }
        })
      })

      $('#keluargapasienform-keluarga_nama').blur(function () {
        ctrlDown = false;
        ctrlKey = 0;
        iKey = 0;
      });

      var $input_date = $('#frm-pasien-tanggal_lahir').pickadate({
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
      $('#frm-pasien-tanggal_lahir').parent().children('.input-group-addon').on('click', function (event) {
        if (picker_date.get('open')) {
          picker_date.close();
        } else {
          picker_date.open();
        }
        event.stopPropagation();
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
        var day_date_s = parseInt($(this).val().substring(6, 8)) > 31 ?
          parseInt($(this).val().substring(6, 8)) - 40 : parseInt($(this).val().substring(6, 8));
        var day_date = padZero(day_date_s);
        var month_date = parseInt($(this).val().substring(8, 10)) > 12 ?
          parseInt($(this).val().substring(8, 10)) - 40 : parseInt($(this).val().substring(8, 10));
        month_date = padZero(month_date);
        // var jk = parseInt($(this).val().substring(6, 8)) - 40 >= 0 ? 16 : 15;
        // var day_date_s = (jk == 16 ? $(this).val().substring(6, 8) - 40 : parseInt($(this).val().substring(6, 8)));
        var year_date_s = $(this).val().substring(10, 12);
        var year_date = year_date_s > 24 ? '19' + year_date_s : '20' + year_date_s;
        var tglLahir = day_date + '-' + month_date + '-' + year_date;
        var _kabupaten = $('#kabupatenForm')
        var _kecamatan = $('#frm-pasien-kecamatan_id')
        _kabupaten.attr('data-code', '')
        _kecamatan.attr('data-code', '')
        if ($('#frm-pasien-jenisidentitas').val() == '94') {
          // set propinsi
          _kabupaten.attr('data-code', kodeKab)
          _kecamatan.attr('data-code', kodeKec)
          $('#frm-pasien-propinsi_id option').each(function (id, el) {
            if (String($(el).data('kode')) == kodeProv) {
              // $(el).attr('selected', true);
              $('#frm-pasien-propinsi_id').val($(el).attr('value')).trigger('change').trigger('depdrop:change');
            }
          })
          // set jenis kelamin
          $("input[name='PasienForm[jeniskelamin]']").closest('span').removeClass('checked')
          $("input[name='PasienForm[jeniskelamin]']").closest('div').removeClass('checked')
          $("input[name='PasienForm[jeniskelamin]'][value=" + jk + "]").prop('checked', true).trigger('change');
          $("input[name='PasienForm[jeniskelamin]'][value=" + jk + "]").closest('span').addClass('checked')
          $("input[name='PasienForm[jeniskelamin]'][value=" + jk + "]").closest('div').addClass('checked')
          $('#frm-pasien-tanggal_lahir').val(tglLahir).trigger('change');

        }

      });

      $("#frm-pasien-namadepan").on('change', function () {
        var namadepan = $("#frm-pasien-namadepan").val();
        if (namadepan == 201 || namadepan == 993 || namadepan == 996 || namadepan == 999 || namadepan == 201 || namadepan == 995 || namadepan == 998 || namadepan == 1033) {
          //$('#frm-pasien-jeniskelamin').val(15).prop('checked', true).trigger('change');
          $("input[name='PasienForm[jeniskelamin]']").closest('span').removeClass('checked')
          $("input[name='PasienForm[jeniskelamin]']").closest('div').removeClass('checked')
          $("input[name='PasienForm[jeniskelamin]'][value=15]").prop('checked', true).trigger('change');
          $("input[name='PasienForm[jeniskelamin]'][value=15]").closest('span').addClass('checked')
          $("input[name='PasienForm[jeniskelamin]'][value=15]").closest('div').addClass('checked')
        } else if (namadepan == 202 || namadepan == 204 || namadepan == 1001 || namadepan == 994 || namadepan == 997 || namadepan == 1000 || namadepan == 1034) {
          //$('#frm-pasien-jeniskelamin').val(16).trigger('change');
          $("input[name='PasienForm[jeniskelamin]']").closest('span').removeClass('checked')
          $("input[name='PasienForm[jeniskelamin]']").closest('div').removeClass('checked')
          $("input[name='PasienForm[jeniskelamin]'][value=16]").prop('checked', true).trigger('change');
          $("input[name='PasienForm[jeniskelamin]'][value=16]").closest('span').addClass('checked')
          $("input[name='PasienForm[jeniskelamin]'][value=16]").closest('div').addClass('checked')
        } else {
          //$('#frm-pasien-jeniskelamin').val('').trigger('change');
          $("input[name='PasienForm[jeniskelamin]']").closest('span').removeClass('checked')
          $("input[name='PasienForm[jeniskelamin]']").closest('div').removeClass('checked')
          $("input[name='PasienForm[jeniskelamin]'][value='']").prop('checked', true).trigger('change');
          $("input[name='PasienForm[jeniskelamin]'][value='']").closest('span').addClass('checked')
          $("input[name='PasienForm[jeniskelamin]'][value='']").closest('div').addClass('checked')
        }
      });

      $(document).on('change', '#frm-pasien-alamatdepan', function () {
        var alamatDepan = $(this).val();
        $('#keluargapasienform-alamatdepan').val(alamatDepan).trigger('change').trigger('depdrop:change');
      });

      $("#keluargapasienform-keluarga_namadepan").on('change', function () {
        var namadepan = $("#keluargapasienform-keluarga_namadepan").val();
        if (namadepan != 202 && namadepan != 204 && namadepan != 1001 && namadepan != 994 && namadepan != 997 && namadepan != 1000 && namadepan != 1034) {
          //$('#frm-pasien-jeniskelamin').val(15).prop('checked', true).trigger('change');
          $("input[name='KeluargaPasienForm[keluarga_jk]']").closest('span').removeClass('checked')
          $("input[name='KeluargaPasienForm[keluarga_jk]']").closest('div').removeClass('checked')
          $("input[name='KeluargaPasienForm[keluarga_jk]'][value=15]").prop('checked', true).trigger('change');
          $("input[name='KeluargaPasienForm[keluarga_jk]'][value=15]").closest('span').addClass('checked')
          $("input[name='KeluargaPasienForm[keluarga_jk]'][value=15]").closest('div').addClass('checked')
        } else {
          //$('#frm-pasien-jeniskelamin').val(16).trigger('change');
          $("input[name='KeluargaPasienForm[keluarga_jk]']").closest('span').removeClass('checked')
          $("input[name='KeluargaPasienForm[keluarga_jk]']").closest('div').removeClass('checked')
          $("input[name='KeluargaPasienForm[keluarga_jk]'][value=16]").prop('checked', true).trigger('change');
          $("input[name='KeluargaPasienForm[keluarga_jk]'][value=16]").closest('span').addClass('checked')
          $("input[name='KeluargaPasienForm[keluarga_jk]'][value=16]").closest('div').addClass('checked')
        }
      });

      $("#frm-pasien-jenisidentitas").val(94).trigger('change');
      if (pasienBaruBpjs == true) {
        $('#form-parent-info').show();
        $('#form-parent').removeClass("col-md-12").addClass("col-md-9");
        $('#btn-edit-info-pasien').prop('disabled', true)
      } else {
        $('#form-parent-info').hide();
        $('#form-parent').removeClass("col-md-9").addClass("col-md-12");
      }

      /*if (_formPendaftaran.tmpPasien.nama_pasien != null) {
          $('#frm-pasien-namadepan').val(_formPendaftaran.tmpPasien.namadepan).trigger('change');                            
          $('#frm-pasien-nama_pasien').val(_formPendaftaran.tmpPasien.nama_pasien);
          $('#frm-pasien-propinsi_id').val(_formPendaftaran.tmpPasien.propinsi_id).trigger('change');
          $('#kabupatenForm').val(_formPendaftaran.tmpPasien.kabupaten_id).trigger('change');
          $('#frm-pasien-kecamatan_id').val(_formPendaftaran.tmpPasien.kecamatan_id).trigger('change');
          $('#frm-pasien-kelurahan_id').val(_formPendaftaran.tmpPasien.kelurahan_id).trigger('change');
          $('#frm-pasien-rt').val(_formPendaftaran.tmpPasien.rt);
          $('#frm-pasien-rw').val(_formPendaftaran.tmpPasien.rw);
          $('#frm-pasien-alamat_pasien').val(_formPendaftaran.tmpPasien.alamat_pasien);
          $('#frm-pasien-no_telepon_pasien').val(_formPendaftaran.tmpPasien.no_telepon_pasien);
          $('#frm-pasien-pekerjaan_id').val(_formPendaftaran.tmpPasien.pekerjaan_id);
      }*/
      $('#form-keluarga-kabupaten_id').depdrop({
        depends: ["form-keluarga-propinsi_id"],
        placeholder: "-- PILIH --",
        url: "/master/kabupaten/list-kabupaten"
      });
      $('#form-keluarga-kecamatan_id').depdrop({
        depends: ["form-keluarga-kabupaten_id"],
        placeholder: "-- PILIH --",
        url: "/master/kecamatan/list-kecamatan"
      });
      $('#form-keluarga-kelurahan_id').depdrop({
        depends: ["form-keluarga-kecamatan_id"],
        placeholder: "-- PILIH --",
        url: "/master/kelurahan/list-kelurahan"
      });

      $('#form-keluarga-kecamatan_id').trigger('change').trigger('depdrop:change');
      $('#form-keluarga-kelurahan_id').trigger('change').trigger('depdrop:change');
      $('#form-keluarga-kelurahan_id').on('depdrop:afterChange', function (event, id, value) {
        $('#form-keluarga-kelurahan_id').trigger('change')
        $('#form-keluarga-kelurahan_id').trigger('depdrop:change');
      });

      $('#btn-data-keluarga').click(function (e) {
        e.preventDefault();
        $('#modal_data_keluarga').modal('show');
        return false;
      });

      // Return input no rm to original size
      $("span[aria-labelledby='select2-no_rekam_medik-container']").css("max-width", "");
    } else {
      $('.steps-basic').steps("add", {
        title: "Pasien",
        content: _clone.html()
      });

      // $.ajax({
      //   url: '/pendaftaran/end-point/bayi-by-pasien',
      //   data: {
      //     noRekamMedik: $("#no_rekam_medik").val()
      //   },
      //   success: (res) => {
      //     // Append table bayi
      //     $("#form-pasien-content").prepend(`
      //           <div class="col-sm-12 table-responsive" style="margin-bottom:12px;">
      //             <input type="hidden" name="kelahiran_id" id="kelahiranIdInput">
      //             <input type="hidden" name="pendaftaran_ibu_id" id="pendaftaranIbuInput">
      //             <h4 class="text-center">Pilih bayi yang akan didaftarkan</h4>
      //             <table class="table table-hover table-row-clickable table-striped table-condensed" id="tableBayi">
      //               <thead>
      //                 <tr>
      //                   <th>Bayi</th>
      //                   <th>Berat Badan (Gram)</th>
      //                   <th>Panjang Badan (Cm)</th>
      //                   <th>Jenis Kelamin</th>
      //                   <th>Kondisi Bayi</th>
      //                 </tr>
      //               </thead>
      //               <tbody>
      //               </tbody>
      //             </table>
      //           </div>
      //         `)
      //     if (res.data.length > 0) {
      //       res.data.map((eachBayi) => {
      //         $("#tableBayi tbody").append(`
      //               <tr data-id="${eachBayi.kelahiranbayi_id}" data-registered="${eachBayi.is_registered ? '1' : '0'}" ${eachBayi.is_registered ? `class="row-registered" data-toggle="tooltip" data-placement="top" title="Bayi sudah didaftarkan dengan no pendaftaran ${eachBayi.no_pendaftaranbayi}"` : ''}>
      //                 <td>${eachBayi.nomor_bayi}</td>
      //                 <td>${eachBayi.berat_badan}</td>
      //                 <td>${eachBayi.tinggi_badan}</td>
      //                 <td>${eachBayi.jenis_kelamin === 15 ? 'Laki-Laki' : 'Perempuan'}</td>
      //                 <td>${eachBayi.kondisi_bayi}</td>
      //               </tr>
      //             `)
      //         Object.assign(dataBayi, {
      //           [eachBayi.kelahiranbayi_id]: eachBayi
      //         })
      //       })
      //     } else {
      //       dataBayi = {}
      //     }
      //     $("#form-pasien-content").children('.col-md-6').hide();
      //     $(".div-pasien").hide();
      //     $("#tableBayi tbody tr").bind('click', ({
      //       delegateTarget
      //     }) => {
      //       // parsing data bayi to form
      //       if ($(delegateTarget).data('registered') === 0) {
      //         parseDataBayiToForm($(delegateTarget).data('id'))
      //       }
      //     })
      //   }
      // })

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
        var day_date_s = parseInt($(this).val().substring(6, 8)) > 31 ?
          parseInt($(this).val().substring(6, 8)) - 40 : parseInt($(this).val().substring(6, 8));
        var day_date = padZero(day_date_s);
        var month_date = parseInt($(this).val().substring(8, 10)) > 12 ?
          parseInt($(this).val().substring(8, 10)) - 40 : parseInt($(this).val().substring(8, 10));
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
      $('#form-keluarga-kabupaten_id').depdrop({
        depends: ["form-keluarga-propinsi_id"],
        placeholder: "-- PILIH --",
        url: "/master/kabupaten/list-kabupaten"
      });
      $('#form-keluarga-kecamatan_id').depdrop({
        depends: ["form-keluarga-kabupaten_id"],
        placeholder: "-- PILIH --",
        url: "/master/kecamatan/list-kecamatan"
      });
      $('#form-keluarga-kelurahan_id').depdrop({
        depends: ["form-keluarga-kecamatan_id"],
        placeholder: "-- PILIH --",
        url: "/master/kelurahan/list-kelurahan"
      });

      $('#btn-data-keluarga').click(function (e) {
        e.preventDefault();
        $('#modal_data_keluarga').modal('show');
        return false;
      });

      // Return input no rm to original size
      $("span[aria-labelledby='select2-no_rekam_medik-container']").css("max-width", "");
    }
  },
  formRujukan: function () {
    var _clone = _formPendaftaran.formInputRujukan;
    _clone.find('input').attr("autocomplete", "off");
    $('.steps-basic').steps("add", {
      title: "Data Rujukan",
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

    $('#rujukanform-rujukandari_id').select2();
    refreshOptionSelect2($('#rujukanform-rujukandari_id'), dataRujukanDari)
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
    $('#asuransiform-kelastanggungan_id').on('select2:close', ({
      delegateTarget
    }) => {
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
  },
  validateAsuransi: function (object) {
    var _data = object.serializeArray();
    var _result = false;
    $().docoForm('click', {
      url: '/pendaftaran/daftar-' + _formPendaftaran.params + '/validation-asuransi',
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
    $('#is_tujuan_kunj').val(1);

    var _data = object.serializeArray();
    _data.push({
      name: 'no_kartu',
      value: _formPendaftaran.dataBpjs.noKartu
    });
    _data.push({
      name: 'ext',
      value: extension
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
      $("#asal_rujukan").on("change", function () {
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
        placeholder: "PILIH DOKTER DPJP",
        ajax: {
          url: "/api/bpjs/referensi-dpjp",
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
        templateSelection: function (res) {
          _formPendaftaran.tmpBpjs.dpjpServeText = res.text
          return res.text;
        }
      });

      $(".select2JenisPeserta").select2({
        placeholder: "PILIH JENIS KEPESERTAAN",
        ajax: {
          url: "/api/bpjs/referensi-kepesertaan",
          dataType: "json",
          quietMillis: 250,
          data: function (params) {
            var query = {
              search: params.term,
              type: 'public',
            }

            return query;
          },
        },
      });

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
          setTimeout(function () {
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

      setTimeout(function () {
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
    
      $('#bpjsnew_detail_nokartu').html('<strong>:</strong> ' + peserta.noKartu);
      $('#bpjsnew_detail_nik').html('<strong>:</strong> ' + peserta.nik);
      $('#bpjsnew_detail_tgl_lahir').html('<strong>:</strong> ' + convertDateByFormat(peserta.tglLahir,'d m Y'));
      $('#bpjsnew_detail_jenis_peserta').html('<strong>:</strong> ' + peserta.jenisPeserta.keterangan);
      $('#bpjsnew_detail_hak_kelas').html('<strong>:</strong> ' + peserta.hakKelas.keterangan);
      $('.btn-detail-bpjs').attr('action', `/pendaftaran/daftar-igd/detail-history-bpjs?no_kartu=${peserta.noKartu}`)
      $('.btn-detail-bpjs').show();

      $("#asal_rujukan").val(2).trigger('change');
      $("input[name='BpjsNewForm[no_rekam_medik]']").val(peserta.mr.noMR);
      $("#no_asuransi").val(peserta.noKartu);
      setKelasRawat(peserta.hakKelas.kode)
      var tmt = convertDateByFormat(peserta.tglTMT,'d m Y');
      var tat = convertDateByFormat(peserta.tglTAT,'d m Y');
      $('#bpjsnew_detail_tmt_tat').html('<strong>:</strong> ' + tmt + ' - ' + tat);
      var kdProv = peserta.provUmum.kdProvider;
      var nmProv = peserta.provUmum.nmProvider;
      $('#bpjsnew_detail_ppk_rujukan').html('<strong>:</strong> ' + kdProv + " - " + nmProv);
      var statusPeserta = peserta.statusPeserta.keterangan;
      $('#bpjsnew_detail_status_peserta').html('<strong>:</strong> ' + statusPeserta);

      var tglSep = $("input[name='BpjsNewForm[tanggal_sep]_submit']").val();
      $('#button-list-sep').attr('href', '/pendaftaran/daftar/list-sep?no_kartu=' + peserta.noKartu + '&tgl_sep=' + tglSep);
      $("#no_telp").val(peserta.mr.noTelepon);

      if (peserta.informasi.prolanisPRB != null) {
        $('#group_prb').show();
        $('#content_prb').html(peserta.informasi.prolanisPRB);
      }

      $('input[type=text]').keyup(function () {
        $(this).val($(this).val().toUpperCase());
      });

      $('textarea').keyup(function () {
          $(this).val($(this).val().toUpperCase());
      });
      }
  },
  setdpjp: function (jnsPelayanan, tglSep, poliTujuan) {
    let url = window.location.origin + '/api/bpjs/referensi-dokter?jnsPelayanan=' + jnsPelayanan + '&tglSep=' + tglSep + '&poliTujuan=' + poliTujuan;
    $.ajax({
      type: 'GET',
      url: url,
      dataType: 'JSON',
      beforeSend: function () {
        $('#kode_dpjp').empty();
      },
      success: function (res) {
        $("#kode_dpjp").attr("data-placeholder", "--Pilih Dokter DPJP--");
        // foreach
        if (res.results) {
          $.each(res.results, function (index, value) {
            var newOption = new Option(value.text, value.id, false, false);
            $('#kode_dpjp').append(newOption);
          });
          $('#kode_dpjp').trigger('change');
        }
      }
    });
  },
  validatePasien: function (object) {
    var _data = object.serializeArray();
    var _result = false;
    var _tempNew = $('input[name="chk-statuspasien"]:checked').val();
    if (typeof _tempNew == 'undefined') {
      $().docoForm('click', {
        url: '/pendaftaran/daftar-' + _formPendaftaran.params + '/validation-pasien',
        data: _data,
        skipConfirm: true,
        skipSuccessNotif: true,
        async: false,
        success: function (data) {
          _result = true;
        },
        error: function (error) {
          _result = false;
          var no_identitas_pasien = $('#frm-pasien-no_identitas_pasien').val();
          if (no_identitas_pasien == null || no_identitas_pasien == '') {

            $('.field-frm-pasien-no_identitas_pasien').parent().addClass('has-error');
            $('.field-frm-pasien-no_identitas_pasien').parent().find('.help-block').html('<i class="fa fa-exclamation-circle" aria-hidden=true></i> &nbsp;NIK Tidak Boleh Kosong.');
          } else if (!no_identitas_pasien.match(/^[0-9]$/)) {
            $('.field-frm-pasien-no_identitas_pasien').parent().addClass('has-error');
            $('.field-frm-pasien-no_identitas_pasien').parent().find('.help-block').html('<i class="fa fa-exclamation-circle" aria-hidden=true></i> &nbsp;NIK hanya boleh angka.');
          } else if (no_identitas_pasien.length > 16) {
            $('.field-frm-pasien-no_identitas_pasien').parent().addClass('has-error');
            $('.field-frm-pasien-no_identitas_pasien').parent().find('.help-block').html('<i class="fa fa-exclamation-circle" aria-hidden=true></i> &nbsp;NIK tidak boleh lebih dari 16 char.');
          } else {
            $('.field-frm-pasien-no_identitas_pasien').parent().removeClass('has-error');
            $('.field-frm-pasien-no_identitas_pasien').parent().find('.help-block').html('');
          }

          $('.jenis_identitas').each(function (key, obj) {
            if (!$(this).val()) {
              next = false;

              $(this).parent().addClass('has-error');
              $(this).parent().find('.help-block').html('<i class=fa aria-hidden=true></i> &nbsp;Jenis Identitas cannot be blank.');
              $(this).parent().find('.fa').addClass('fa-exclamation-circle');

              docoNotification('error', 'Proses Gagal !', 'Terjadi kesalahan, silahkan cek inputan.');
            }

            arrayJenisIdentitas.push($(this).val());
          });

          //Penanda Button Data Keluarga
          $('.div-data-keluarga').parent().find('.help-block-data-keluarga').html('<span style="color: black;">Data Keluarga Harus Diisi</span> <span style="color: red;">*</span>');
        }
      });
      if (_result == true) {
        $().docoForm('click', {
          url: '/pendaftaran/daftar-' + _formPendaftaran.params + '/validation-keluarga-pasien',
          data: _data,
          skipConfirm: true,
          skipSuccessNotif: true,
          async: false,
          success: function (data) {
            _result = true;

            //Penanda Button Data Keluarga
            $('.div-data-keluarga').parent().find('.help-block-data-keluarga').html('<span style="color: black;">Data Keluarga Harus Diisi</span> <span style="color: red;">*</span>');

            var no_identitas_pasien = $('#frm-pasien-no_identitas_pasien').val();
            if (no_identitas_pasien == null || no_identitas_pasien == '') {

              $('.field-frm-pasien-no_identitas_pasien').parent().addClass('has-error');
              $('.field-frm-pasien-no_identitas_pasien').parent().find('.help-block').html('<i class="fa fa-exclamation-circle" aria-hidden=true></i> &nbsp;NIK Tidak Boleh Kosong.');
              _result = false;
            } else {
              $('.field-frm-pasien-no_identitas_pasien').parent().removeClass('has-error');
              $('.field-frm-pasien-no_identitas_pasien').parent().find('.help-block').html('');
            }
          },
          error: function (error) {
            _result = false;

            //Penanda Button Data Keluarga
            $('.div-data-keluarga').parent().find('.help-block-data-keluarga').html('<span style="color: red;"><i class="fa fa-exclamation-circle" aria-hidden=true></i> &nbsp;Cek Inputan Data Keluarga.</span>');

            var no_identitas_pasien = $('#frm-pasien-no_identitas_pasien').val();
            if (no_identitas_pasien == null || no_identitas_pasien == '') {
              $('.field-frm-pasien-no_identitas_pasien').parent().addClass('has-error');
              $('.field-frm-pasien-no_identitas_pasien').parent().find('.help-block').html('<i class="fa fa-exclamation-circle" aria-hidden=true></i> &nbsp;NIK Tidak Boleh Kosong.');
            } else {
              $('.field-frm-pasien-no_identitas_pasien').parent().removeClass('has-error');
              $('.field-frm-pasien-no_identitas_pasien').parent().find('.help-block').html('');
            }
          }
        });
      }
    } else {
      _result = true;
      // $().docoForm('click', {
      //   url: `/pendaftaran/daftar-${_formPendaftaran.params}/validation-pasien?is_bbl=true`,
      //   data: _data,
      //   skipConfirm: true,
      //   skipSuccessNotif: true,
      //   async: false,
      //   success: function (data) {
      //     _result = true;
      //   },
      //   error: ({
      //     responseJSON
      //   }) => {
      //     if (typeof responseJSON !== 'undefined' && typeof responseJSON.meta !== 'undefined' && typeof responseJSON.meta.message !== 'undefined') {
      //       docoNotification('error', responseJSON.meta.message, '')
      //     }
      //   }
      // });
    }
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

    // setTimeout(function() {
    //   if(Object.keys(_formPendaftaran.tmpDataPj).length) {
    //       $("input[name='KunjunganForm[is_pj]']").closest('span').addClass('checked')
    //       $("input[name='KunjunganForm[is_pj]']").prop("checked", true).trigger('change')
    //       getPj(_formPendaftaran.tmpDataPj.idPj)
    //   }
    // }, 2000);

    $('#kunjunganform-is_pj').on("change", function () {
      if (this.checked) {
        $('.steps-basic').steps("add", {
          title: "Penanggung Jawab",
          content: _clonePj.html()
        });
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
          $('.umurtext').val(umur);

        })
      } else {
        var _totalStep = $('ul[role="tablist"] > li').length;
        $('.steps-basic').steps("remove", _totalStep - 1);
      }
    });

    $("#pasienadmisiform-is_pasientitipan").on("change", function () {
      if (this.checked) {
        $(".kelas_ditagihkan").removeClass("hidden");
      } else {
        $(".kelas_ditagihkan").addClass("hidden");
        $("#kelas_ditagihkan_id").val("").trigger('change');
        $("#ruangan_titipan_id").val("");
        $("#ruanganTitipanLabelValue").text("");
      }
    });

    /** Event after generate form */
    $('.styled, .multiselect-container input').uniform({
      radioClass: 'choice'
    });
    /** Re init */
    var _content = $('#form-kunjungan-content');
    $("#kelaspelayanan_id,#kelas_ditagihkan_id,#hakkelas_id,#kelaspermintaan_id, #pasienadmisiform-dokterpengirim_id, #pasienadmisiform-dokterkonsul_id, #prosedurmasuk_id").select2();
    getListDokterAll();
    $("#kelaspelayanan_id").bind('change', () => {
      $("#tbl-karcis_info").remove()
      $("#kelasPelayananSelected").val('')
    })
    $("#kunjunganform-jeniskasuspenyakit_id,#kelaspelayanan_id").bind('change', () => {
      $("#kamarTitipanCheck").prop('checked', false);
      $("#isApsCheck").prop('checked', false)
      $("#filterHeader").html('');
      activeTable = 'kamar';

      if ($("#kunjunganform-jeniskasuspenyakit_id").val()) {
        existingReqData.jeniskasuspenyakit_id = JK_UMUM;
      }

      if ($("#kelaspelayanan_id").val()) {
        existingReqData.kelaspelayanan_id = $("#kelaspelayanan_id").val();
      }
    })
    if ($("#tipepasienform-is_bbl").is(':checked')) {
      $("#kunjunganform-jeniskasuspenyakit_id").val(null).trigger('change')
    } else {
      $("#kunjunganform-jeniskasuspenyakit_id").val(JK_UMUM).trigger('change')
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
      var jenis_id = $('#kunjunganform-jeniskasuspenyakit_id').val();
      var kelas_id = $('#kelaspelayanan_id').val();
      var ruangan_id = $('#ruangan_id').val();
      var carabayar_id = $("#selectCarabayar").val();
      var jenisKamar = 'semua';

      if (is_pasientitipan.val() == 1) {
        jenisKamar = 'titipan';
      } else if (is_pasienaps.val() == 1) {
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
          text: "Kelas Perawatan belum dipilih!",
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
        if (typeof $("input[name='bpjsKelas']").val() == 'undefined' || $("input[name='bpjsKelas']").val() == '' || $("input[name='bpjsKelas']").val() == null) {
          $("#kamarTitipanCheck").parents('.form-group').hide();
          $("#isApsCheck").parents('.form-group').hide();
          activeTable = 'kamar';

        } else {
          $("#kamarTitipanCheck").parents('.form-group').show();
          $("#isApsCheck").parents('.form-group').show();
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
      var jenis_id = $('#kunjunganform-jeniskasuspenyakit_id').val();
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

    $('input[type=text]').keyup(function () {
      $(this).val($(this).val().toUpperCase());
    });

    $('textarea').keyup(function () {
        $(this).val($(this).val().toUpperCase());
    });
  },
  generateKunjungan: function (data) {
    var _penunjang = {
      paket: [],
      tindakan: []
    };
    $.each(data, function (x, y) {
      var _tmp = {
        is_cyto: y.is_cyto == 'false' ? 0 : 1,
        tariftindakan_id: y.tariftindakan_id
      };
      if (y.tipepaket_id != null) {
        _tmp.id = y.tipepaket_id;
        _penunjang.paket.push(_tmp);
      } else {
        _tmp.id = y.daftartindakan_id;
        _penunjang.tindakan.push(_tmp);
      }
    });
    return _penunjang;
  },
  validKunjugan: function (object) {
    var _data = object.serializeArray();
    var _result = false;
    $().docoForm('click', {
      url: `/pendaftaran/daftar-${_formPendaftaran.params}/validation-kunjungan`,
      data: _data.concat({
        name: 'is_ranap',
        value: true
      }),
      skipConfirm: true,
      skipSuccessNotif: true,
      async: false,
      success: function (data) {
        _result = true;
      }
    });
    return _result;
  },
  getInfoPasien: function (id, noRm, setPrevData = true, getPenanggung = false) {
    $.ajax({
      type: 'GET',
      url: `/pendaftaran/daftar-${_formPendaftaran.params}/get-info-pasien?id=${id}&no_rm=${noRm}&param=sty`,
      dataType: 'JSON',
      success: function (res) {
        var _response = res.response;
        $("#filterHeader").html('')
        var _infoPasien = _response.info_pasien;
        var _infoKeluarga = _response.keluarga;
        _formPendaftaran.infoPasien = _response.info_pasien
        $("#pasien_id_hidden").val(_infoPasien.pasien_id);
        jk = _infoPasien.jeniskelamin
        _formPendaftaran.selectedPasienId = _infoPasien.pasien_id
        $('.nama-pasien').text(_infoPasien.nama_pasien);
        $('.rm-pasien').text(_infoPasien.no_rekam_medik);
        $('.nik-pasien').text(': ' + (_infoPasien.nik_pasien != null ? _infoPasien.nik_pasien : '-'));
        $('.kelamin-pasien').text(': ' + (_infoPasien.jenis_kelamin ? _infoPasien.jenis_kelamin : '-'));
        $('.darah-pasien').text(': ' + (_infoPasien.golongan_darah ? _infoPasien.golongan_darah : '-'));
        $('.tlp-pasien').text(': ' + (_infoPasien.no_telepon_pasien ? _infoPasien.no_telepon_pasien : '-'));
        $('.alamat-pasien').text(': ' + (_infoPasien.alamat_pasien ? _infoPasien.alamat_pasien : '-'));
        $('.tempat-lahir-pasien').text(': ' + (_infoPasien.tempat_lahir ? _infoPasien.tempat_lahir : '-'));
        $('.tanggal-lahir-pasien').text(': ' + (convertDateByFormat(_infoPasien.tanggal_lahir, 'd m Y') ? convertDateByFormat(_infoPasien.tanggal_lahir, 'd m Y') : '-'));
        $('.umur-pasien').text(': ' + (_infoPasien.tanggal_lahir ? generateUmur(convertDateByFormat(_infoPasien.tanggal_lahir, 'd m Y')) : '-'));
        $('.status_perkawinan-pasien').text(': ' + (_infoPasien.status_perkawinan ? _infoPasien.status_perkawinan : '-'));
        $('.pekerjaan-pasien').text(': ' + (_infoPasien.pekerjaan_nama ? _infoPasien.pekerjaan_nama : '-'));
        $('.agama-pasien').text(': ' + (_infoPasien.agama ? _infoPasien.agama : '-'));
        if (_infoKeluarga) {
          $('.ortu-pasien').text(': ' + (_infoKeluarga.keluarga_nama ? _infoKeluarga.keluarga_nama + (_infoKeluarga.keluarga_hubungan ? ' - ' + _infoKeluarga.keluarga_hubungan : '') : '-'));
        } else {
          $('.ortu-pasien').text(': -')
        }
        $('.hub_keluarga-pasien').text(': ' + (_infoPasien.hubungankeluarga ? _infoPasien.hubungankeluarga : '-'));
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
          _clone.find('.history-penanggung-biaya').text(': ' + (data.carabayar_nama ? data.carabayar_nama : '-'));
          _html += _clone.html();
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

        if (getPenanggung) {
          var kunjungan = res.response.kunjugan[parseInt(res.response.kunjugan.length) - 1];
          var asal_rujukan_id;

          if (res.response.kunjugan !== undefined) {
            var allKunjungan = res.response.kunjugan;
            for (var x = 0; x < allKunjungan.length; x++) {
              if (defaultPenBiaya[allKunjungan[x].carabayar_id] === undefined) {
                defaultPenBiaya[allKunjungan[x].carabayar_id] = {
                  penjamin_id: allKunjungan[x].penjamin_id,
                  asal_rujukan_id: allKunjungan[x].asal_rujukan_id,
                  namapemilik_asuransi: allKunjungan[x].namapemilik_asuransi,
                  no_asuransi: allKunjungan[x].no_asuransi,
                  nopokokperusahaan: allKunjungan[x].nopokokperusahaan,
                  namaperusahaan: allKunjungan[x].namaperusahaan,
                  penanggungbiaya_nama: allKunjungan[x].penanggungbiaya_nama,
                  ruangcarabayar_id: allKunjungan[x].ruangcarabayar_id,
                  noindukkaryawan: allKunjungan[x].noindukkaryawan,
                  instansi: allKunjungan[x].instansi,
                  namabagian: allKunjungan[x].namabagian,
                };
              }
            }
          }
              
          if (kunjungan.asalrujukan_id != null) {
            asal_rujukan_id = kunjungan.asalrujukan_id;
          }else {
            asal_rujukan_id =1;
          }
              
          if(kunjungan.carabayar_id != null) {
            $('#selectCarabayar').val(kunjungan.carabayar_id).trigger('change').trigger('depdrop:change');
          }
              
          $.ajax({
            url: '/pendaftaran/daftar/get-penjamin',
            data: {
              depdrop_parents: [
                $("#selectCarabayar").val()
              ]
            },
            method: 'POST',
            success: (res) => {
              $('#penjamin_id').prop('disabled', false);
              $("#penjamin_id").select2('destroy')
              $("#penjamin_id").html('')
              res.output.map(({ id, name }) => {
                $("#penjamin_id").append(`<option value="${id}">${name}</option>`)
              })
              $("#penjamin_id").select2()
              if (kunjungan.penjamin_id) {
                $('#penjamin_id').val(kunjungan.penjamin_id).trigger('change').trigger('depdrop:change');
                kunjungan.penjamin_id = false;
              }
            }
          });
          $('#asalrujukan_id').val(asal_rujukan_id).trigger('change').trigger('depdrop:change');
        }

        $('#kunjungan').html(_html);
        $('#form-parent-info').show();
        $('#form-parent').removeClass("col-md-12").addClass("col-md-9");
        const latestKunjungan = (_response.kunjugan.length) ? _response.kunjugan[_response.kunjugan.length - 1] : '';
        if (latestKunjungan != '' && setPrevData == true) {
          setPrevData(latestKunjungan); //moved to separate method
        }
        if ($("#selectCarabayar").val() === 6) {
          var _action = $('.btn-detail-bpjs').attr('action');
          $('.btn-detail-bpjs').show();
        }
      }
    });
  },
  resetForm: function () {
    resetStep()
    var _currentLi = $('ul[role="tablist"] > li.current > a').attr("aria-controls");
    $('#' + _currentLi + ' select').val('').trigger('change');
    // tableDaftarTerakhir.draw();
    // if (_formPendaftaran.params === 'igd') {
    //     tableDaftarTerakhirIgd.draw();
    // } else {
    //     tableDaftarTerakhir.draw();
    // }
    _formPendaftaran.resetAsuransi();
    $('.field-tipepasienform-no_asuransi').hide();
    $('#selectCarabayar').focus();
    $('input[name="no_antrian"]').val("");
    $("#pendaftaran_id_hidden").val("");
    if (Object.keys(_formPendaftaran.pendaftaranOl).length) {
      var uri = window.location.toString();
      if (uri.indexOf("?") > 0) {
        var clean_uri = uri.substring(0, uri.indexOf("?"));
        window.history.replaceState({}, document.title, clean_uri);
      }
      _formPendaftaran.pendaftaranOl = {};
    }
    // _formPendaftaran.resetPasien();
    // Refresh penjamin_id
    $('.nextRow').show();
  }
};

function getPj(idPj) {
  $.ajax({
    type: 'GET',
    url: '/pendaftaran/end-point/get-penanggung-jawab?idPj=' + idPj,
    dataType: 'JSON',
    success: function (res) {
      let data = res.response
      $('#pjpasienform-pj_pengantar').val(data.pengantar).trigger('change')
      $('#pjpasienform-pj_nama').val(data.penanggungjawab_nama).trigger('change')
      $("#pjpasienform-pj_jk").find(`input[value=${data.penanggungjawab_jeniskelamin}]`).trigger('click')
      $('#pjpasienform-pj_jenis_identitas').val(data.jenisidentitas).trigger('change')
      $('#pjpasienform-pj_no_identitas').val(data.no_identitas).trigger('change')
      $('#pjpasienform-pj_hubungan').val(data.hubungankeluarga).trigger('change')
      $('#pjpasienform-pj_tempat_lahir').val(data.penanggungjawab_tempatlahir).trigger('change')
      $('#pjpasienform-pj_tanggal_lahir').val(convertDateByFormat(data.penanggungjawab_tgllahir, 'd M Y')).trigger('change')
      $('#pjpasienform-pj_no_telepon').val(data.penanggungjawab_notelp).trigger('change')
      $('#pjpasienform-pj_alamat').val(data.penanggungjawab_alamat).trigger('change')
    },
    error: function (res) {
      console.log(res)
    }
  });
}

$(() => {
  $("#kamarTitipanCheck").bind('change', ({
    delegateTarget
  }) => {
    changeKamar('titipan');
  });

  $("#isApsCheck").bind('change', ({
    delegateTarget
  }) => {
    changeKamar('aps');
  });

  $(document).on('select2:close', '.select2-hidden-accessible', ({
    currentTarget
  }) => {
    $(currentTarget).focus()
  });

  changeKamar = function (jenisKamar) {
    var element = '';
    if (jenisKamar == 'semua') {
      activeTable = 'kamar';
      enableAll();
    } else {
      if (jenisKamar == 'titipan') {
        activeTable = 'kamarTitipan';
        element = $("#kamarTitipanCheck");
        if (element.is(':checked')) {
          disableAps();
        } else {
          jenisKamar = 'semua';
          activeTable = 'kamar';
          enableAll();
        }
      } else {
        activeTable = 'kamarAps';
        element = $("#isApsCheck");
        if (element.is(':checked')) {
          disableTitipan();
        } else {
          jenisKamar = 'semua';
          activeTable = 'kamar';
          enableAll();
        }
      }
    }

    initDatatable(jenisKamar, true);
  }

  disableAps = function () {
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

  disableTitipan = function () {
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

  enableAll = function () {
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
});

function validasiKunjungan(data) {
  let validate = true

  if ($("#tipepasienform-is_bbl").is(':checked')) {
    validate = false;
  }

  if (validate) {
    $.ajax({
      type: 'POST',
      url: '/pendaftaran/end-point/validate',
      data: {
        noRm: data,
        params: _formPendaftaran.params
      },
      dataType: 'JSON',
      success: function (res) {
        if (typeof res.text != 'undefined') {
          docoNotification('warning', 'Perhatian!', res.text)
        } else {
          stateNextStep('next')
        }

        return true;
      },
      error: function (res) {
        stateNextStep('stop')
        docoNotification('error', 'Perhatian!', res.responseJSON.text)
        return false;
      }
    });
  } else {
    return 'Pendaftaran Bayi'
  }
}

const stateNextStep = function (state) {
  let btnNxt = $(".wizard").find('a[href="#next"]');
  let btnFnsh = $(".wizard").find('a[href="#finish"]');
  btnNxt.attr("href", '#next');
  btnFnsh.attr("href", '#finish');

  if (state == 'next') {
    btnNxt.parent().removeClass("hidden")
    btnFnsh.parent().removeClass("hidden")
  } else if (state == 'stop') {
    btnNxt.parent().addClass("hidden")
    btnFnsh.parent().addClass("hidden")
  }
}

const stateCaraBayar = function (carabayar_group) {
  var caraBayar = $('#selectCarabayar').val();
  if (carabayar_group == docoHelper.groupUmum) {
    $('.field-no_rekam_medik').show();
    $('.field-tipepasienform-no_asuransi').hide();
    $('#form-asuransi').hide();
    $('.field-asalrujukan_id').show();
    $('#form-bpjs').hide();
    $('#form-bpjs-1').hide();
    $('#form-penanggung').hide();
    $('.field-penjamin_id').show();

    if (caraBayar == 44) {
      $('#radio-penanggung_jawab').hide();
      $('#form-penanggung').show();
      $('#form-penanggung-instansi').show();
      $('#form-penanggung-noindukkaryawan').show();
      $('#form-penanggung-jpkm').hide();
      $('#div-namabagian').show();
      $('#div-ruangcarabayar_id').hide();
      $("label[for='penanggungbiayaform-penanggungbiaya_nama']").text("Pegawai");
    } else if (caraBayar == 45) {
      $('#radio-penanggung_jawab').hide();
      $('#form-penanggung').show();
      $('#form-penanggung-instansi').hide();
      $('#form-penanggung-noindukkaryawan').hide();
      $('#form-penanggung-jpkm').show();
      $('#div-namabagian').hide();
      $('#div-ruangcarabayar_id').show();
      $("label[for='penanggungbiayaform-penanggungbiaya_nama']").text("Nama");
    } else if (caraBayar == 41) {
      //$('.field-no_rekam_medik').show();
      $('.field-asalrujukan_id').show();
      $('#form-penanggung').show();
      $('#form-penanggung-instansi').hide();
      $('#form-penanggung-noindukkaryawan').show();
      $('#form-penanggung-jpkm').hide();
      $('#div-namabagian').hide();
      $('#div-ruangcarabayar_id').show();
      $("label[for='penanggungbiayaform-penanggungbiaya_nama']").text("Nama");
    } else {
      $('#radio-penanggung_jawab').show();
      getLastPenanggung();
    }
  } else if (carabayar_group == docoHelper.groupJaminan) {
    $('.field-tipepasienform-no_asuransi').show();
    $('#form-asuransi').show();
    /*$("#form-asuransi :input").attr("disabled", true);*/
    $("#form-asuransi-namaperusahaan").show();
    $("#form-asuransi-namapemilikasuransi").show();
    $("#form-asuransi-personil").hide();
    $('.field-no_rekam_medik').show();
    $('.field-asalrujukan_id').show();
    $('#form-bpjs').hide();
    $('#form-bpjs-1').hide();
    $('#radio-penanggung_jawab').hide();
    $('#form-penanggung').hide();
    $('.field-penjamin_id').show();
  } else if (carabayar_group == docoHelper.groupBPJS) {
    $('#jenis_rujukan').removeClass('bpjs-penunjang')
    $('#form-bpjs').show();
    $('#form-bpjs-1').show();
    $('#jenis_pelayanan').val(2).trigger("change");
    $('.field-asalrujukan_id').hide();
    $('.field-tipepasienform-no_asuransi').hide();
    $('#form-asuransi').hide();
    $('#radio-penanggung_jawab').hide();
    $('#form-penanggung').hide();
    $('.field-no_rekam_medik').hide();
    $('.field-penjamin_id').hide();
  } else {
    $('#form-bpjs').hide();
    $('#form-bpjs-1').hide();
    $('.field-tipepasienform-no_asuransi').hide();
    $('#form-asuransi').hide();
    $('#radio-penanggung_jawab').hide();
    $('#form-penanggung').hide();
    $('.field-no_rekam_medik').show();
    $('.field-asalrujukan_id').show();
    $('.field-penjamin_id').show();

    if (caraBayar == 44) {
      $('#radio-penanggung_jawab').hide();
      $('#form-penanggung').show();
      $('#form-penanggung-instansi').show();
      $('#form-penanggung-noindukkaryawan').show();
      $('#form-penanggung-jpkm').hide();
      $('#div-namabagian').show();
      $('#div-ruangcarabayar_id').hide();
      $("label[for='penanggungbiayaform-penanggungbiaya_nama']").text("Pegawai");
    } else if (caraBayar == 45) {
      $('#radio-penanggung_jawab').hide();
      $('#form-penanggung').show();
      $('#form-penanggung-instansi').hide();
      $('#form-penanggung-noindukkaryawan').hide();
      $('#form-penanggung-jpkm').show();
      $('#div-namabagian').hide();
      $('#div-ruangcarabayar_id').show();
      $("label[for='penanggungbiayaform-penanggungbiaya_nama']").text("Nama");
    } else if (caraBayar == 41) {
      //$('.field-no_rekam_medik').show();
      $('.field-asalrujukan_id').show();
      $('#form-penanggung').show();
      $('#form-penanggung-instansi').hide();
      $('#form-penanggung-noindukkaryawan').show();
      $('#form-penanggung-jpkm').hide();
      $('#div-namabagian').hide();
      $('#div-ruangcarabayar_id').show();
      $("label[for='penanggungbiayaform-penanggungbiaya_nama']").text("Nama");
    }
  }
  // $('input[name="chk-statuspasien"]').prop("checked", false);
  // $(".field-no_rekam_medik").addClass('required');
  // $('#no_rekam_medik').val('').trigger('change');
  // $("#note-asuransi").html("");
  // $("#tipepasienform-no_asuransi").val("").trigger("change");
}

const resetStep = function () {
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
}

const generateFormKunjungan = function () {
  _formPendaftaran.formKunjungan();
  $('select').removeAttr('tabindex');
  $('.steps-basic').steps("next");
}

function setKelasRawat(hakKelas) {
  let kelas = parseInt(hakKelas);
  let defaultOptions = [{
    'id': 1,
    'text': 'Kelas I'
  }, {
    'id': 2,
    'text': 'Kelas II'
  }, {
    'id': 3,
    'text': 'Kelas III'
  }, ]
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

function pilihKamar(identifier) {
  let link = document.URL;
  let patternAction = link.match(/pemesanan-kamar/g);
  const jenisTempatTidur = $(identifier).data('kettempattidur_id');
  const kamarruangan_jenis = $(identifier).data('kamarruangan_jenis');
  const kamarruangan_id = $(identifier).data('kamarruangan_id');

  var kelas_rawat = $("#kelas_rawat").val();
  var is_pasientitipan = $("#pasienTitipanValue").val();
  var is_pasienaps = $("#pasienApsValue").val();
  var carabayar_id = $("#selectCarabayar").val();

  var jk_kamar;
  var allow_jk;
  var attr = $(identifier).data('allow_jk');

  if (carabayar_id == 6) {
    if (typeof kelas_rawat != 'undefined') {
      if (kelas_rawat != $(identifier).data('kelaspelayanan_id') && (is_pasientitipan == 0 && is_pasienaps == 0)) {
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
      kamarruanganId: kamarruangan_id
    },
    method: 'POST',
    success: () => {
      var _keteranganKamar;
      var _kelaspelayanan_id;

      _kelaspelayanan_id = $(identifier).data('kelaspelayanan_id');
      _kelaspelayanan_nama = $(identifier).data('kelaspelayanan_nama');

      $("#kelaspelayanan_id").val(_kelaspelayanan_id).trigger('change');
      $(document).find("#ruanganIdHidden").val($(identifier).data('ruangan_id'));
      if ($("#kamarTitipanCheck").val() == 1) {
        _keteranganKamar = ' - KAMAR TITIPAN';
        _kelaspelayanan_id = $("input[name='bpjsKelas']").val();
      } else if ($("#isApsCheck").val() == 1) {
        _keteranganKamar = ' - APS';
      } else {
        _keteranganKamar = ' - ' + _kelaspelayanan_nama;
      }

      if (typeof $("input[name='bpjsKelas']").val() !== 'undefined' && parseInt($("input[name='bpjsKelas']").val()) !== parseInt($(identifier).data('kelaspelayanan_id'))) {
        $(document).find('#ruanganLabelValue').html("");
        $(document).find('#ruanganLabelValue').append(` ${$(identifier).data('kelaspelayanan_nama')} ` + _keteranganKamar + ` (${($("input[name='bpjsKelas']").val() < $(identifier).data('kelaspelayanan_id') && $(identifier).data('kelaspelayanan_id') <= 3) ? 'TURUN KELAS' : 'NAIK KELAS'})`)
      } else if (typeof $("input[name='bpjsKelas']").val() === 'undefined' && parseInt($("#kelaspelayanan_id").val()) !== parseInt($(identifier).data('kelaspelayanan_id'))) {
        $(document).find('#ruanganLabelValue').html("");
        $(document).find('#ruanganLabelValue').append(` ${$(identifier).data('kelaspelayanan_nama')} ` + _keteranganKamar)
      } else {
        $(document).find('#ruanganLabelValue').html("");
        $(document).find('#ruanganLabelValue').html($(identifier).data('ruangan_nama') + _keteranganKamar);
      }

      $(document).find('#kamartempattidur_id').val($(identifier).data('kamartempattidur_id'));
      $(document).find('#kamarruangan_id').val($(identifier).data('kamarruangan_id'));
      $(document).find('#nokamar').val($(identifier).data('kamarruangan_nokamar') + ' - ' + $(identifier).data('no_tempattidur'));

      $("#kelasPelayananSelected").val($(identifier).data('kelaspelayanan_id'));

      $("#btnCariKamar").focus();
      $('#modalTempatTidur').modal('hide');
      // $('.is_pasientitipan').removeClass("hidden");

      $("#pasienadmisiform-is_pasientitipan").prop("checked", false);
      $(".kelas_ditagihkan").addClass("hidden");
      $("#kelas_ditagihkan_id").val("").trigger('change');
      $("#ruangan_titipan_id").val("");
      $("#ruanganTitipanLabelValue").text("");
    },
    error: ({
      responseJSON
    }) => {
      docoNotification('error', responseJSON.meta.message, '')
    },
    complete: () => {
      hideLoader()
      $.unblockUI()
    }
  })
}

function pilihKamarTitipan(identifier) {
  let link = document.URL;
  let patternAction = link.match(/pemesanan-kamar/g);
  const jenisTempatTidur = $(identifier).data('kettempattidur_id');
  const kamarruangan_jenis = $(identifier).data('kamarruangan_jenis');
  const kamarruangan_id = $(identifier).data('kamarruangan_id');

  var kelas_rawat = $("#kelas_rawat").val();
  var is_pasientitipan = $("#pasienTitipanValue").val();
  var is_pasienaps = $("#pasienApsValue").val();
  var carabayar_id = $("#selectCarabayar").val();

  var jk_kamar;
  var allow_jk;
  var attr = $(identifier).data('allow_jk');

  if (carabayar_id == 6) {
    if (typeof kelas_rawat != 'undefined') {
      if (kelas_rawat != $(identifier).data('kelaspelayanan_id') && (is_pasientitipan == 0 && is_pasienaps == 0)) {
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
      kamarruanganId: kamarruangan_id
    },
    method: 'POST',
    success: () => {
      var _keteranganKamar;
      var _kelaspelayanan_id;

      _kelaspelayanan_id = $(identifier).data('kelaspelayanan_id');
      _kelaspelayanan_nama = $(identifier).data('kelaspelayanan_nama');

      // $("#kelaspelayanan_id").val(_kelaspelayanan_id).trigger('change');
      $(document).find("#ruanganTitipanIdHidden").val($(identifier).data('ruangan_id'));
      if ($("#kamarTitipanCheck").val() == 1) {
        _keteranganKamar = ' - KAMAR TITIPAN';
        _kelaspelayanan_id = $("input[name='bpjsKelas']").val();
      } else if ($("#isApsCheck").val() == 1) {
        _keteranganKamar = ' - APS';
      } else {
        _keteranganKamar = ' - ' + _kelaspelayanan_nama;
      }

      $(document).find('#ruanganTitipanLabelValue').html($(identifier).data('ruangan_nama') + _keteranganKamar);
      $(document).find('#tempattidur_titipan_id').val($(identifier).data('kamartempattidur_id'));
      $(document).find('#kamar_titipan_id').val($(identifier).data('kamarruangan_id'));

      $("#kelasPelayananTagihanSelected").val($(identifier).data('kelaspelayanan_id'));
      $("#kelas_ditagihkan_id").val($(identifier).data('kelaspelayanan_id')).trigger("change");

      $("#btnCariKamarTitipan").focus();
      $('#modalTempatTidurTitipan').modal('hide');
    },
    error: ({
      responseJSON
    }) => {
      docoNotification('error', responseJSON.meta.message, '')
    },
    complete: () => {
      hideLoader()
      $.unblockUI()
    }
  })
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
    success: ({
      data
    }) => {
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
              // clearInputanAsuransi()
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

$(document).on('click', '.tambah-dokter-konsul', function (event) {
  var next = true;
  var html = $('.dokter-konsul:last').clone();
  arrayDokterKonsul = [];
  html.find('span').remove();
  html.find('select').select2();
  html.find('.dokterkonsul_id').val(null);
  html.find('.tambah-dokter-konsul').html('X');
  html.find('.tambah-dokter-konsul').removeClass('btn-success');
  html.find('.tambah-dokter-konsul').addClass('btn-danger');
  html.find('.tambah-dokter-konsul').addClass('hapus-dokter-konsul');
  html.find('.tambah-dokter-konsul').removeClass('tambah-dokter-konsul');
  html.find('.tambah-dokter-konsul').prop('id', null);

  $('.dokterkonsul_id').each(function (key, obj) {
    if (!$(this).val()) {
      next = false;

      $(this).addClass('has-error');
      $(this).parent().find('.help-block').html('<i class=fa aria-hidden=true></i> &nbsp;Dokter Konsul cannot be blank.');
      $(this).parent().find('.fa').addClass('fa-exclamation-circle');

      docoNotification('error', 'Proses Gagal !', 'Terjadi kesalahan, silahkan cek inputan.');
    }
    arrayDokterKonsul.push($(this).val());
  });

  if (next) {
    // Append in the last
    $('.dokter-konsul:last').after(html);
  }
});

$(document).on('click', '.hapus-dokter-konsul', function (event) {
  $(this).parent().parent().parent().remove();
});

$("#dokterkonsul_id").prepend("<option selected=></option>").select2({
  placeholder: "Pilih"
});


function getListDokterAll() {
  if ($('#pasienadmisiform-dokterkonsul_id').hasClass("select2-hidden-accessible")) {
    $('#pasienadmisiform-dokterkonsul_id').select2('destroy');
  }
  if ($('#pasienadmisiform-dokterpengirim_id').hasClass("select2-hidden-accessible")) {
    $('#pasienadmisiform-dokterpengirim_id').select2('destroy');
  }
  if ($('#pegawai_id').hasClass("select2-hidden-accessible")) {
    $('#pegawai_id').select2('destroy');
  }
  $.ajax({
    url: '/pendaftaran/end-point/search-dokter',
    method: 'GET',
    dataType: 'json',
    success: (data) => {
      const dataDropDownDokter = []
      var placeholder = {
        id: "",
        text: "-- Pilih --"
      }
      dataDropDownDokter.push(placeholder);

      for (var i = 0; i < data.results.length; i++) {
        dataDropDownDokter.push(data.results[i]);
      }
      refreshOptionSelect2($('#pasienadmisiform-dokterkonsul_id'), dataDropDownDokter)
      refreshOptionSelect2($('#pasienadmisiform-dokterpengirim_id'), dataDropDownDokter)
      refreshOptionSelect2($('#pegawai_id'), dataDropDownDokter)
    },
    complete: () => {
      $('#pasienadmisiform-dokterkonsul_id').prop('disabled', false)
      $('#pasienadmisiform-dokterpengirim_id').prop('disabled', false)
      $('#pegawai_id').prop('disabled', false)
    }
  })
}

$('#modal_backdrop').on('hidden.bs.modal', function () {
  // location.reload();
})

const clearInputanAsuransi = function () {
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

function getLastPenanggung() {
  var pasien_id = $("#pasien_id_hidden").val();
  var no_rekam_medik = $('#no_rekam_medik').val();
  if (pasien_id != '' && pasien_id != 'undefined' && no_rekam_medik != '' && no_rekam_medik != 'undefined') {
    var pengantar_lainnya = '992';
    var pengantar_ortu = '991';
    $.ajax({
      url: '/pendaftaran/end-point/get-last-penanggung?pasien_id=' + pasien_id,
      method: 'GET',
      dataType: 'json',
      success: (data) => {
        var result = data.results;
        if (result) {
          if (result.pengantar == pengantar_ortu) {
            $("input[name='PjpasienForm[pj_pengantar]']").closest('span').removeClass('checked')
            $("input[name='PjpasienForm[pj_pengantar]']").closest('div').removeClass('checked')
            $("input[name='PjpasienForm[pj_pengantar]'][value=991]").prop('checked', true).trigger('change');
            $("input[name='PjpasienForm[pj_pengantar]'][value=991]").closest('span').addClass('checked')
            $("input[name='PjpasienForm[pj_pengantar]'][value=991]").closest('div').addClass('checked')
          } else if (result.pengantar == pengantar_lainnya) {
            $("input[name='PjpasienForm[pj_pengantar]']").closest('span').removeClass('checked')
            $("input[name='PjpasienForm[pj_pengantar]']").closest('div').removeClass('checked')
            $("input[name='PjpasienForm[pj_pengantar]'][value=992]").prop('checked', true).trigger('change');
            $("input[name='PjpasienForm[pj_pengantar]'][value=992]").closest('span').addClass('checked')
            $("input[name='PjpasienForm[pj_pengantar]'][value=992]").closest('div').addClass('checked')

            // Update form penanggung jawab
            var _content = $('#form-pasien');
            _content.find('#form-tipepasien-nama_pasien').val(result.penanggungjawab_nama);
            _content.find('#form-tipepasien-namadepan').val(result.pj_namadepan).trigger('change');
            _content.find('#form-tipepasien-propinsi_id').val(result.pj_propinsi_id).trigger('change');
            _content.find('#form-tipepasien-kabupaten_id').val(result.pj_kabupaten_id).trigger('change');
            _content.find('#form-tipepasien-kecamatan_id').val(result.pj_kecamatan_id).trigger('change').trigger('depdrop:change');
            _content.find('#form-tipepasien-kelurahan_id').val(result.pj_kelurahan_id).trigger('change').trigger('depdrop:change');
            _content.find('#form-tipepasien-rt').val(result.pj_rt);
            _content.find('#form-tipepasien-rw').val(result.pj_rw);
            _content.find('#form-tipepasien-alamat_pasien').val(result.penanggungjawab_alamat);
            _content.find('#form-tipepasien-no_telepon_pasien').val(result.penanggungjawab_notelp);
            _content.find('#form-tipepasien-pekerjaan_id').val(result.pj_pekerjaan_id).trigger('change');
            $("input[name='PjpasienForm[pj_jk]']").closest('span').removeClass('checked')
            $("input[name='PjpasienForm[pj_jk]']").closest('div').removeClass('checked')
            $("input[name='PjpasienForm[pj_jk]'][value=" + result.penanggungjawab_jeniskelamin + "]").prop('checked', true).trigger('change');
            $("input[name='PjpasienForm[pj_jk]'][value=" + result.penanggungjawab_jeniskelamin + "]").closest('span').addClass('checked')
            $("input[name='PjpasienForm[pj_jk]'][value=" + result.penanggungjawab_jeniskelamin + "]").closest('div').addClass('checked')
          }
        }
      }
    })
  }
}

function setPrevData(latestKunjungan) {
  existingReqData.penjamin_id = null
  if (latestKunjungan !== 'undefined' && latestKunjungan != '') {
    if (latestKunjungan.penanggungjawab_id !== 'undefined' && latestKunjungan.penanggungjawab_id != null) {
      _formPendaftaran.tmpDataPj = {
        idPj: latestKunjungan.penanggungjawab_id
      }
      // getPj(_formPendaftaran.tmpDataPj.idPj);
    } else {
      _formPendaftaran.tmpDataPj = {}
    }
    // setTimeout(() => {
    //   $("#selectCarabayar").val(latestKunjungan.carabayar_id).trigger('change').trigger('depdrop:change');
    // }, 1000);
    if (latestKunjungan.carabayar_id == 6) {
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
    existingReqData.penjamin_id = latestKunjungan.penjamin_id
    if (latestKunjungan.asalrujukan_id !== null) {
      $("#asalrujukan_id").val(latestKunjungan.asalrujukan_id).trigger('change').trigger('depdrop:change');
    }
    if (latestKunjungan.namapemilik_asuransi != null) {
      $('#asuransiform-namapemilikasuransi').val(latestKunjungan.namapemilik_asuransi).trigger('change');
    }
    if (latestKunjungan.no_asuransi != null) {
      $('#tipepasienform-no_asuransi').val(latestKunjungan.no_asuransi).trigger('change');
    }

    if (latestKunjungan.nopokokperusahaan != null) {
      $('#asuransiform-nomorpokokperusahaan').val(latestKunjungan.nopokokperusahaan).trigger('change');
    }

    if (latestKunjungan.namaperusahaan != null) {
      $('#asuransiform-namaperusahaan').val(latestKunjungan.namaperusahaan).trigger('change');
    }

    if (latestKunjungan.penanggungbiaya_nama != null) {
      $('#form-tipepasien-penanggungbiaya_nama').val(latestKunjungan.penanggungbiaya_nama).trigger('change');
    }

    // if(latestKunjungan.ruangcarabayar_id != null){
    //     $('#ruangcarabayar_id').val(latestKunjungan.ruangcarabayar_id).trigger('change').trigger('depdrop:change');
    // }

    $('#ruangcarabayar_id').on('depdrop:afterChange', function (event, id, value) {
      $('#ruangcarabayar_id').val(latestKunjungan.ruangcarabayar_id).trigger('change');
      $('#ruangcarabayar_id').trigger('depdrop:change');
    });

    if (latestKunjungan.noindukkaryawan != null) {
      $('#form-tipepasien-noindukkaryawan').val(latestKunjungan.noindukkaryawan).trigger('change');
    }

    if (latestKunjungan.instansi != null) {
      $('#form-tipepasien-instansi').val(latestKunjungan.instansi).trigger('change');
    }

    if (latestKunjungan.namabagian != null) {
      $('#form-tipepasien-namabagian').val(latestKunjungan.namabagian).trigger('change');
    }

    if (latestKunjungan.penjamin_nama != null) {
      $('#asuransiform-nama_asuransi').val(latestKunjungan.penjamin_nama).trigger('change');
    }
  }

  //PJ Pasien Tera
  if (_infoPasien.penanggungjawabtera_nama !== 'undefined' && _infoPasien.penanggungjawabtera_nama != null) {
    _formPendaftaran.tmpDataPj.penanggungjawabtera_nama = _infoPasien.penanggungjawabtera_nama
    _formPendaftaran.tmpDataPj.penanggungjawabtera_hubungan = _infoPasien.penanggungjawabtera_hubungan
    _formPendaftaran.tmpDataPj.penanggungjawabtera_alamat = _infoPasien.penanggungjawabtera_alamat
  }

  $(".nextRow").show();
  existingReqData.newPasien = true

  $( "span[aria-labelledby='select2-no_rekam_medik-container']" ).css("max-width","215px");
}

function changeFormatDate(date){
  var dates = new Date(date);
  var newDate = dates.getDate();
  var newMonth = dates.getMonth();
  var newYear = dates.getFullYear();
  
  var newDates = (newDate < 10 ? '0' : '')+newDate+'-'+ (newMonth < 10 ? '0' : '')+newMonth+'-'+newYear;
  return newDates;
}