var typeName;
var key;

let tableTitipan;
let isStillExistDataKamarTitipan = {
  semua: true,
  aps: true,
  titipan: true
}
let pageDataKamarTitipan = {
  semua: 1,
  aps: 1,
  titipan: 1
}
let columnGeneratedTitipan = [];
let kelas_titipan = false;

function createHeaderDatatableKamarTitipan(typeName, key) {
  var jenisKamar = typeName; 
  if ($(`#filterHeaderKamarTitipan${key}`).length === 0) {
    $("#filterHeaderTitipan").append(`
        <div id="filterHeaderKamarTitipan${key}">
            <div class="col-md-3" style="width: 20% !important;">
                <div class="form-group">
                    <label for="jenis_penyakit">Jenis Penyakit</label>
                    <select name="jenisPenyakit" id="jenisPenyakitFilter${key}" class="form-control" placeholder="Semuaxxcc"></select>
                    <option></option>
                </div>
            </div>
            <div class="col-md-3" style="width: 20% !important;">
                  <div class="form-group">
                      <label for="jenis_penyakit">Klasifikasi Kamar</label>
                      <select name="jenisPenyakit" id="klasifikasiKamarFilter${key}" class="form-control"></select>
                  </div>
            </div>
            <div class="col-md-2 filterKamarSection" style="width: 20% !important;">
                  <div class="form-group">
                      <label for="jenis_penyakit">Kelas</label>
                      <select name="jenisPenyakit" id="kelasFilter${key}" class="form-control"></select>
                  </div>
            </div>
            <div class="col-md-2" style="width: 20% !important;">
                <div class="form-group">
                    <label for="jenis_penyakit">Ruangan</label>
                    <select name="jenisPenyakit" id="ruanganFilter${key}" class="form-control"></select>
                </div>
            </div>
            <div class="col-md-2" style="width: 20% !important;">
                <div class="form-group">
                    <label for="jenis_penyakit">Kamar</label>
                    <select name="jenisPenyakit" id="kamarFilter${key}" class="form-control"></select>
                    
                </div>  
            </div>
            <div class="col-sm-12 button-search-section">
                <button type="button" id="searchBtn${key}" class="btn btn-info btn-sm btn-labeled pull-right"><b class="fa fa-lg fa-search"></b> Cari</button>
            </div>
        </div>`);
    
    initFilterTitipan(key);
    
    $(`#kelasFilter${key}`).val($("#kelas_ditagihkan_id").val()).trigger('change');
    $(`#jenisPenyakitFilter${key}`).val($("#jeniskasuspenyakit_id").val()).trigger('change');
    $(`#kamarFilter${key}`).prop('disabled', true)
    $(`#ruanganFilter${key}`).prop('disabled', true)
    $(`#ruanganFilter${key}`).bind('change', ({ delegateTarget }) => {
      if ($(delegateTarget).val() === '' || $(delegateTarget).val() === '-' || $(delegateTarget).val() === 'Semua' || $(delegateTarget).val() == null) {
        $(`#kamarFilter${key}`).prop('disabled', true)
        $(`#kamarFilter${key}`).val('Semua').trigger('change')
        return false
      }
      if ($(`#kamarFilter${key}`).hasClass("select2-hidden-accessible")) {
        $(`#kamarFilter${key}`).select2('destroy')
        $(`#kamarFilter${key}`).html('')
      }
      $.ajax({
        url: `daftar/kamar-ruangan`,
        data: {
          ruanganId: $(delegateTarget).val(),
          kelasPelayananId: $(`#kelasFilter${key}`).val(),
          klasifikasiKamarId: $(`#klasifikasiKamarFilter${key}`).val(),
        },
        success: (res) => {
          let dataKamar = []
          res.data.map((item) => {
            dataKamar.push({
              id: item.kamarruangan_id,
              text: item.kamarruangan_nokamar,
            })
          })
          $(`#kamarFilter${key}`).prop('disabled', false)
          $(`#kamarFilter${key}`).select2({
            data: dataKamar
          });
          var optionKamar = new Option('Semua', '', true, true);
          $(`#kamarFilter${key}`).prepend(optionKamar).trigger('change');
        }
      })
    })
    $(`#jenisPenyakitFilter${key}`).bind('change', ({ delegateTarget }) => {
      initRuanganSourceTitipan(key)
    })
    $(`#kelasFilter${key}`).bind('change', ({ delegateTarget }) => {
      initRuanganSourceTitipan(key)
    })
    $(`#klasifikasiKamarFilter${key}`).bind('change', ({ delegateTarget }) => {
        initKamarSourceTitipan(key)
    })
    if (key == 'Semua') {
      $(`#jenisPenyakitFilter${key}`).val($("#jeniskasuspenyakit_id").val()).trigger('change')
    }
    $(`#searchBtn${key}`).bind('click', ({ delegateTarget }) => {
      initDatatableTitipan(jenisKamar, true)
    });
  }
}

function initRuanganSourceTitipan(key) {
  if ($(`#jenisPenyakitFilter${key}`).val() === '' || $(`#jenisPenyakitFilter${key}`).val().toLowerCase() === 'semua') {
    $(`#ruanganFilter${key}`).prop('disabled', true)
    $(`#ruanganFilter${key}`).val('Semua').trigger('change')
    return false
  }

  if ($(`#ruanganFilter${key}`).hasClass("select2-hidden-accessible")) {
    $(`#ruanganFilter${key}`).select2('destroy')
    $(`#ruanganFilter${key}`).html('')
  }
  $.ajax({
    url: `daftar/get-list-ruangan-kelas?instalasi_id=${instalasiId}`,
    method: 'POST',
    data: {
      jeniskasuspenyakit_id: $(`#jenisPenyakitFilter${key}`).val(),
      kelaspelayanan_id: $(`#kelasFilter${key}`).val(),
    },
    success: (res) => {
      $(`#ruanganFilter${key}`).prop('disabled', false)
      const dataOutput = JSON.parse(res).output
      const dataRuangan = []
      dataOutput.map(({ id, name }) => {
        dataRuangan.push({
          id,
          text: name
        })
      })
      $(`#ruanganFilter${key}`).select2({
        data: dataRuangan,
      });
      var optionRuangan = new Option('Semua', '', true, true);
      $(`#ruanganFilter${key}`).prepend(optionRuangan).trigger('change');
    },
    error: () => {
      $(`#ruanganFilter${key}`).select2({
        data: []
      })
    },
    complete: () => {
      $("#kamarFilter").prop('disabled', true)
      hideLoader()
    }
  })
}

function initKamarSourceTitipan(key) {
    if ($(`#ruanganFilter${key}`).val() === '' || $(`#ruanganFilter${key}`).val() === '-' || $(`#ruanganFilter${key}`).val() === 'Semua' || $(`#ruanganFilter${key}`).val() == null) {
        $(`#kamarFilter${key}`).prop('disabled', true)
        $(`#kamarFilter${key}`).val('Semua').trigger('change')
        return false
    }
    if ($(`#kamarFilter${key}`).hasClass("select2-hidden-accessible")) {
        $(`#kamarFilter${key}`).select2('destroy')
        $(`#kamarFilter${key}`).html('')
    }
    $.ajax({
        url: `daftar/kamar-ruangan`,
        data: {
          ruanganId: $(`#ruanganFilter${key}`).val(),
          kelasPelayananId: $(`#kelasFilter${key}`).val(),
          klasifikasiKamarId: $(`#klasifikasiKamarFilter${key}`).val(),
        },
        success: (res) => {
          let dataKamar = []
          res.data.map((item) => {
            dataKamar.push({
              id: item.kamarruangan_id,
              text: item.kamarruangan_nokamar,
            })
          })
          $(`#kamarFilter${key}`).prop('disabled', false)
          $(`#kamarFilter${key}`).select2({
            data: dataKamar
          });
          var optionKamar = new Option('Semua', '', true, true);
          $(`#kamarFilter${key}`).prepend(optionKamar).trigger('change');
        }
    })
}

function initFilterTitipan(key) {
  let dataKasusArray = []
  let dataJenisPenyakit = []
  let dataKlasifikasiKamar = []

  Object.keys(dataKasus).map((item) => {
    dataKasusArray.push({
      id: item,
      text: dataKasus[item]
    })
  })

  Object.keys(dataPenyakit).map((item) => {
    dataJenisPenyakit.push({
        id: item,
        text: dataPenyakit[item]
    });
  });

  Object.keys(dataKlasifikasi).map((item) => {
    dataKlasifikasiKamar.push({
        id: item,
        text: dataKlasifikasi[item]
    });
  });

  $(`#jenisPenyakitFilter${key}`).select2({
    data: dataJenisPenyakit,
  })
  
  $(`#kelasFilter${key}`).select2({
    data: dataKasusArray,
  });

  $(`#klasifikasiKamarFilter${key}`).select2({
    data: dataKlasifikasiKamar,
  });

  var optionJenisPenyakit = new Option('Semua', '', true, true);
  $(`#jenisPenyakitFilter${key}`).prepend(optionJenisPenyakit).trigger('change');

  var optionKelas = new Option('Semua', '', true, true);
  $(`#kelasFilter${key}`).prepend(optionKelas).trigger('change');

  var optionKlasifikasiKamar = new Option('Semua', '', true, true);
  $(`#klasifikasiKamarFilter${key}`).prepend(optionKlasifikasiKamar).trigger('change');
}
let paramDatatableTitipan = {
  'titipan': {},
  'aps': {},
  'general': {},
}
function setParamDatatableTitipan(typeName, key) {
  if (Object.keys(paramDatatableTitipan[typeName]).length == 0) {
    paramDatatableTitipan[typeName] = {
      penjamin_id: $("#penjamin_id").val(),
      kamar_id: typeof $(`#kamarFilter${key}`) !== 'undefined' && $(`#kamarFilter${key}`).val() !== null ? $(`#kamarFilter${key}`).val() : '',
      status_kamar: typeof $(`#statusKamarFilter${key}`) !== 'undefined' && $(`#statusKamarFilter${key}`).val() !== null ? $(`#statusKamarFilter${key}`).val() : '',
      ruangan_id: typeof $(`#ruanganFilter${key}`) !== 'undefined' && $(`#ruanganFilter${key}`).val() !== null ? $(`#ruanganFilter${key}`).val() : '',
      jenis_id: typeof $(`#jenisPenyakitFilter${key}`) !== 'undefined' && $(`#jenisPenyakitFilter${key}`).val() !== null ? $(`#jenisPenyakitFilter${key}`).val() : '',
      kelas_id: typeof $(`#kelasFilter${key}`) !== 'undefined' && $(`#kelasFilter${key}`).val() !== null ? $(`#kelasFilter${key}`).val() : '',
      klasifikasikamar_id: typeof $(`#klasifikasiKamarFilter${key}`) !== 'undefined' && $(`#klasifikasiKamarFilter${key}`).val() !== null ? $(`#klasifikasiKamarFilter${key}`).val() : ''
    }
  }
}
function initDatatableTitipan(jenisKamar, reinit = false) {
  typeName = jenisKamar;
  if(jenisKamar == 'titipan') {
      key = 'Titipan';
  }
  else if(jenisKamar == 'aps') {
      key = 'Aps';
  }
  else {
      key = 'Semua';
  }

  $(`#searchBtn${key}`).prop('disabled', true);
  if (reinit) {
    pageDataKamarTitipan[typeName] = 1
    paramDatatableTitipan[typeName] = {}
  }
  createHeaderDatatableKamarTitipan(typeName, key);
  setParamDatatableTitipan(typeName, key);
  const { kamar_id, status_kamar, ruangan_id, jenis_id, penjamin_id, kelas_id, klasifikasikamar_id } = paramDatatableTitipan[typeName]
  columnGeneratedTitipan = columnKamarTitipan;
  tableTitipan = $('#tableTitipan');
  tableTitipan.parent().show();

  if (pageDataKamarTitipan[typeName] === 1 || reinit) {
    tableTitipan.find('tbody').html('')
  }

  tableTitipan.block({
    message: null
  });

  // Append table
  var _default = {
    page: pageDataKamarTitipan[typeName], 
    gender: jk,
    pasien_titipan : true
  }
  var content = paramDatatableTitipan[typeName];
  var _mergeObject = $.extend({}, _default, content);
  $.ajax({
    url: `${baseUrl}ranap/end-point/get-data-kamar-default`,
    data: _mergeObject,
    method: 'GET',
    beforeSend: () => {
      hideLoader()
    },
    success: (res) => {
      // Append if
      if(typeof res.data.length != 'undefined') {
        if (res.data.length === 0 && pageDataKamarTitipan[typeName] == 1) {
          tableTitipan.find('tbody').html(`
            <tr>
              <td class="text-center" colspan="${columnGeneratedTitipan.length}">Data tidak tersedia</td>
            </tr>
          `)
      } else {
        const records = res.data
        const tbodySection = tableTitipan.find('tbody')
        const length = records.length
        for (let indexRecord = 0; indexRecord < length; indexRecord++) {
          let trHtml = '<tr>'
          let valueOfColumn = ''
          for (let indexColumn = 0; indexColumn < columnGeneratedTitipan.length; indexColumn++) {
            if (columnGeneratedTitipan[indexColumn].data === 'rowNum') {
              valueOfColumn = (pageDataKamarTitipan[typeName] - 1) * 10 + (indexRecord + 1)
            } else {
              valueOfColumn = records[indexRecord][columnGeneratedTitipan[indexColumn].data]
              valueOfColumn = valueOfColumn != null ? valueOfColumn : '';
            }
            trHtml += `<td ${typeof columnGeneratedTitipan[indexColumn].className !== 'undefined' ? `class="${columnGeneratedTitipan[indexColumn].className}"` : ''}>${valueOfColumn}</td>`
          }
          tbodySection.append(`${trHtml}</tr>`)
          tbodySection.find('td').last().parent().attr('data-akomodasi', records[indexRecord]['is_akomodasi'] ? '1' : '0')
        }
        isStillExistDataKamarTitipan[typeName] = records.length > 10
        pageDataKamarTitipan[typeName] += 1
      }
      }
      
    },
    error: () => {
      tableTitipan.find('tbody').html(`
        <tr>
          <td colspan="${columnGeneratedTitipan.length}">Terjadi kesalahan</td>
        </tr>
      `)
    },
    complete: () => {
      tableTitipan.unblock()
      $(`#searchBtn${key}`).prop('disabled', false)
    }
  })
}

// const scrollWrapper = document.querySelector('#tableKamarWrapper');
// const scrollWrapperTitipan = document.querySelector('#tableKamarTitipanWrapper');
// const scrollWrapperAps = document.querySelector('#tableKamarApsWrapper');

// scrollWrapper.addEventListener('scroll', function () {
//   if (scrollWrapper.scrollTop + scrollWrapper.clientHeight >= scrollWrapper.scrollHeight && isStillExistDataKamarTitipan.general) {
//     var jenisKamar = 'semua';
//     initDatatableTitipan(jenisKamar);
//   }
// });

// scrollWrapperTitipan.addEventListener('scroll', function () {
//   if (Math.round(scrollWrapperTitipan.scrollTop + scrollWrapperTitipan.clientHeight) >= scrollWrapperTitipan.scrollHeight && isStillExistDataKamarTitipan.titipan) {
//     var jenisKamar = 'titipan';
//     initDatatableTitipan(jenisKamar, false);
//   }
// });

// scrollWrapperAps.addEventListener('scroll', function () {
//   if (Math.round(scrollWrapperAps.scrollTop + scrollWrapperAps.clientHeight) >= scrollWrapperAps.scrollHeight && isStillExistDataKamarTitipan.aps) {
//     var jenisKamar = 'aps';
//     initDatatableTitipan(jenisKamar, true)
//   }
// });

let activeTableTitipan = 'kamar'

