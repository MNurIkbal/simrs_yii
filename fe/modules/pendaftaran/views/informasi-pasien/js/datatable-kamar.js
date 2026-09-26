
var typeName;
var key;

let table;
let isStillExistDataKamar = {
  general: true,
  aps: true,
  titipan: true
}
let pageDataKamar = {
  general: 1,
  aps: 1,
  titipan: 1
}
let columnGenerated = [];

$("#btnCariKamar").bind('click', () => {
    var is_pasientitipan = $("#kamarTitipanCheck");
    var is_pasienaps = $("#kamarApsCheck");
    var jenis_id = $('#jeniskasuspenyakit_id').val();
    var kelas_id = $('#kelaspelayanan_id').val();
    var ruangan_id = $('#ruanganIdHidden').val();
    var carabayar_id = $("#selectCarabayar").val();

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
    if (carabayar_id != 6) {
          $("#kamarTitipanCheck").parents('.form-group').hide();
          $("#kamarApsCheck").parents('.form-group').hide();
          
    } else {
          $("#kamarTitipanCheck").parents('.form-group').show();
          $("#kamarApsCheck").parents('.form-group').show();
          $("#filterHeaderKamarTitipan").show();
          $("#filterHeaderKamarAps").show();
          activeTable = 'kamar'
    }
    initDatatable(activeTable !== 'kamar', false, true);
    $("#modalTempatTidur").modal({
      backdrop: 'static',
      keyboard: false
    })
});

$(() => {
    $("#kamarTitipanCheck").bind('change', ({ delegateTarget }) => {
      if ($(delegateTarget).is(':checked')) {
        $("#pasienTitipanValue").val(1);
        $("#pasienApsValue").val(0);
        $("#kamarApsCheck").prop("disabled", true);
        initDatatable(true, false, true);
        activeTable = 'kamarTitipan'
        $("#tableKamarTitipanWrapper").show();
        $("#tableKamarWrapper").hide();
        $("#tableKamarApsWrapper").hide();
        $("#filterHeaderKamarTitipan").show();
        $("#filterHeaderKamarAps").hide();
        $("#filterHeaderKamarNonAps").hide();
        $("#filterHeaderKamar").hide();
        $(".filterKamarSection").show();
      } else {
        $("#pasienTitipanValue").val(0);
        $("#pasienApsValue").val(0);
        $("#kamarApsCheck").prop("disabled", false);
        initDatatable(false, false, true);
        activeTable = 'kamar'
        $("#filterHeaderKamarAps").hide();
        $("#filterHeaderKamarTitipan").hide();
        $("#filterHeaderKamarNonAps").hide();
        $("#filterHeaderKamar").show();
        $("#tableKamarTitipanWrapper").hide();
        $("#tableKamarApsWrapper").hide();
        $("#tableKamarWrapper").show();
      }
    });
    $("#kamarApsCheck").bind('change', ({ delegateTarget }) => {
      if ($(delegateTarget).is(':checked')) {
        $("#pasienTitipanValue").val(0);
        $("#pasienApsValue").val(1);
        $("#kamarTitipanCheck").prop("disabled", true);
        initDatatable(false, true, true);
        activeTable = 'kamarAps';
        $("#tableKamarApsWrapper").show();
        $("#tableKamarTitipanWrapper").hide();
        $("#tableKamarWrapper").hide();
        $("#filterHeaderKamarAps").show();
        $("#filterHeaderKamarTitipan").hide();
        $("#filterHeaderKamar").hide();
        $(".filterKamarSection").show();
      } else {
        $("#pasienTitipanValue").val(0);
        $("#pasienApsValue").val(0);
        $("#kamarTitipanCheck").prop("disabled", false);
        initDatatable(false, false, true);
        activeTable = 'kamar';
        $("#filterHeaderKamarAps").hide();
        $("#filterHeaderKamarTitipan").hide();
        $("#filterHeaderKamar").show();
        $("#tableKamarTitipanWrapper").hide();
        $("#tableKamarApsWrapper").hide();
        $("#tableKamarWrapper").show();
      }
  });
  $(document).on('select2:close', '.select2-hidden-accessible', ({ currentTarget }) => {
    $(currentTarget).focus()
  })
});

$("#jeniskasuspenyakit_id").on('change', function(){
    $("#kelaspelayanan_id").val("").trigger("change");
    $("#nokamar").val("").trigger("change");
    $("#pegawai_id").val("").trigger("change");
});

function createHeaderDatatableKamar(isKamarTitipan, isAps) {
  if(isKamarTitipan) {
    key = isKamarTitipan ? 'Titipan' : 'NonTitipan';
  }
  else if(isAps) {
    key = isAps ? 'Aps' : 'NonAps';
  }
  else {
    key = '';
  }

  if ($(`#filterHeaderKamar${key}`).length === 0) {
    if(isKamarTitipan || isAps) {
      $("#filterHeader").append(`
          <div id="filterHeaderKamar${key}">
              <div class="col-md-3">
                  <div class="form-group">
                      <label for="jenis_penyakit">Jenis Penyakit</label>
                      <select name="jenisPenyakit" id="jenisPenyakitFilter${key}" class="form-control"></select>
                  </div>
              </div>
              <div class="col-md-3 filterKamarSection">
                    <div class="form-group">
                        <label for="jenis_penyakit">Kelas</label>
                        <select name="jenisPenyakit" id="kelasFilter${key}" class="form-control"></select>
                    </div>
              </div>
              <div class="col-md-3">
                  <div class="form-group">
                      <label for="jenis_penyakit">Ruangan</label>
                      <select name="jenisPenyakit" id="ruanganFilter${key}" class="form-control"></select>
                  </div>
              </div>
              <div class="col-md-3">
                  <div class="form-group">
                      <label for="jenis_penyakit">Kamar</label>
                      <select name="jenisPenyakit" id="kamarFilter${key}" class="form-control"></select>
                  </div>  
              </div>
              <div class="col-sm-12 button-search-section">
                  <button type="button" id="searchBtn${key}" class="btn btn-info btn-sm btn-labeled pull-right"><b class="fa fa-lg fa-search"></b> Cari</button>
              </div>
          </div>`);
    } 
    else {
        $("#filterHeader").append(`
        <div id="filterHeaderKamar${key}">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="jenis_penyakit">Jenis Penyakit</label>
                    <select name="jenisPenyakit" id="jenisPenyakitFilter${key}" class="form-control"></select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="jenis_penyakit">Ruangan</label>
                    <select name="jenisPenyakit" id="ruanganFilter${key}" class="form-control"></select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="jenis_penyakit">Kamar</label>
                    <select name="jenisPenyakit" id="kamarFilter${key}" class="form-control"></select>
                </div>
            </div>
            <div class="col-sm-12 button-search-section">
                <button type="button" id="searchBtn${key}" class="btn btn-info btn-sm btn-labeled pull-right"><b class="fa fa-lg fa-search"></b> Cari</button>
            </div>
        </div>`);
    }
    
    initFilter(key)
    const kelasPelayananId = !isKamarTitipan || !isAps ? $("#kelaspelayanan_id").val() : $(`#kelasFilter${key}`).val()
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
          kelasPelayananId: !isKamarTitipan || !isAps ? $("#kelaspelayanan_id").val() : $(`#kelasFilter${key}`).val(),
        },
        success: (res) => {
          let dataKamar = [
            {
              id: '',
              text: 'Semua'
            }
          ]
          res.data.map((item) => {
            dataKamar.push({
              id: item.kamarruangan_id,
              text: item.kamarruangan_nokamar,
            })
          })
          $(`#kamarFilter${key}`).prop('disabled', false)
          $(`#kamarFilter${key}`).select2({
            data: dataKamar
          })
        }
      })
    })
    $(`#jenisPenyakitFilter${key}`).bind('change', ({ delegateTarget }) => {
      initRuanganSource(key, isKamarTitipan, isAps)
    })
    $(`#kelasFilter${key}`).bind('change', ({ delegateTarget }) => {
      initRuanganSource(key, isKamarTitipan, isAps)
    });
    if (!isKamarTitipan || !isAps) {
      $(`#jenisPenyakitFilter${key}`).val($("#jeniskasuspenyakit_id").val()).trigger('change')
    }
    $(`#searchBtn${key}`).bind('click', ({ delegateTarget }) => {
      initDatatable(isKamarTitipan, isAps, true)
    })
  }
}

function initRuanganSource(key, isKamarTitipan, isAps) {
  const kelasPelayananId = !isKamarTitipan || !isAps ? $("#kelaspelayanan_id").val() : $(`#kelasFilter${key}`).val();
  
  if ($(`#jenisPenyakitFilter${key}`).val() === '' || $(`#jenisPenyakitFilter${key}`).val().toLowerCase() === 'semua' || kelasPelayananId.toLowerCase() === 'semua' || kelasPelayananId === '') {
    $(`#ruanganFilter${key}`).prop('disabled', true)
    $(`#ruanganFilter${key}`).val('Semua').trigger('change')
    return false
  }

  if ($(`#ruanganFilter${key}`).hasClass("select2-hidden-accessible")) {
    $(`#ruanganFilter${key}`).select2('destroy')
    $(`#ruanganFilter${key}`).html('')
  }
  $.ajax({
    url: `/pendaftaran/daftar/get-list-ruangan?instalasi_id=3`,
    method: 'POST',
    data: {
      depdrop_parents: [
        $(`#jenisPenyakitFilter${key}`).val(),
        kelasPelayananId
      ]
    },
    success: (res) => {
      $(`#ruanganFilter${key}`).prop('disabled', false)
      const dataOutput = JSON.parse(res).output
      const dataRuangan = [
        {
          id: '-',
          text: 'Semua'
        }
      ]
      dataOutput.map(({ id, name }) => {
        dataRuangan.push({
          id,
          text: name
        })
      })
      $(`#ruanganFilter${key}`).select2({
        data: dataRuangan,
      })
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

function initFilter(key) {
  let dataKasusArray = [
    {
      id: '',
      text: 'Semua'
    }
  ]
  Object.keys(dataKasus).map((item) => {
    dataKasusArray.push({
      id: item,
      text: dataKasus[item]
    })
  })
  let dataJenisPenyakit = [
    {
      id: '',
      text: 'Semua'
    }
  ]
  Object.keys(dataPenyakit).map((item) => {
    dataJenisPenyakit.push({
      id: item,
      text: dataPenyakit[item]
    })
  })
  $(`#jenisPenyakitFilter${key}`).select2({
    data: dataJenisPenyakit,
  })
  $(`#kelasFilter${key}`).select2({
    data: dataKasusArray,
  })
}
let paramDatatable = {
  'titipan': {},
  'aps': {},
  'general': {},
}
function setParamDatatable(typeName) {
  if (Object.keys(paramDatatable[typeName]).length == 0) {
    if(typeName === 'titipan') {
      key = 'Titipan';
    } else if(typeName === 'aps') {
      key = 'Aps';
    } else  {
      key = '';
    }

    // const key = (typeName === 'titipan' ? 'Titipan' : 'NonTitipan')
    paramDatatable[typeName] = {
      penjamin_id: $("#penjamin_id").val(),
      kamar_id: typeof $(`#kamarFilter${key}`) !== 'undefined' && $(`#kamarFilter${key}`).val() !== null ? $(`#kamarFilter${key}`).val() : '',
      status_kamar: typeof $(`#statusKamarFilter${key}`) !== 'undefined' && $(`#statusKamarFilter${key}`).val() !== null ? $(`#statusKamarFilter${key}`).val() : '',
      ruangan_id: typeof $(`#ruanganFilter${key}`) !== 'undefined' && $(`#ruanganFilter${key}`).val() !== null ? $(`#ruanganFilter${key}`).val() : '',
      jenis_id: typeof $(`#jenisPenyakitFilter${key}`) !== 'undefined' && $(`#jenisPenyakitFilter${key}`).val() !== null ? $(`#jenisPenyakitFilter${key}`).val() : '',
      kelas_id: typeof $(`#kelasFilter${key}`) !== 'undefined' && $(`#kelasFilter${key}`).val() !== null ? $(`#kelasFilter${key}`).val() : ''
    }
    if (typeName !== 'titipan' && typeName !== 'aps') {
      paramDatatable[typeName].kelas_id = $("#kelaspelayanan_id").val()
    }
  }
}
function initDatatable(isKamarTitipan = false, isAps = false, reinit = false) {
  var jk = $("#jeniskelamin_id").val();
  if(isKamarTitipan) {
    typeName = isKamarTitipan ? 'titipan' : 'semua';
    key = isKamarTitipan ? 'Titipan' : 'NonTitipan';
  }
  else if (isAps){
    typeName = isAps ? 'aps' : 'semua';
    key = isAps ? 'Aps' : 'NonAps';
  }
  else {
    typeName = 'semua';
    key = 'semua';
  }

  $(`#searchBtn${key}`).prop('disabled', true);
  if (reinit) {
    pageDataKamar[typeName] = 1
    paramDatatable[typeName] = {}
  }
  createHeaderDatatableKamar(isKamarTitipan, isAps);
  setParamDatatable(typeName);
  const { kamar_id, status_kamar, ruangan_id, jenis_id, penjamin_id, kelas_id } = paramDatatable[typeName]
  if (isKamarTitipan) {
    columnGenerated = columnKamarTitipan
    table = $('#tableKamarTitipan')
    $('#tableKamarWrapper').hide();
    $('#tableKamarApsWrapper').hide();
    $('#filterHeaderKamarAps').hide();
    $('#filterHeaderKamar').hide();
  } else if(isAps) {
    columnGenerated = columnKamarAps
    table = $('#tableKamarAps')
    $('#tableKamarWrapper').hide();
    $('#tableKamarTitipanWrapper').hide();
    $('#filterHeaderKamarTitipan').hide();
    $('#filterHeaderKamar').hide();
  } else {
    columnGenerated = columns
    table = $('#tableKamar')
    $('#tableKamarTitipanWrapper').hide();
    $('#tableKamarApsWrapper').hide();
    $('#filterHeaderKamarTitipan').hide();
    $('#filterHeaderKamarAps').hide();
  }
  table.parent().show()
  if (pageDataKamar[typeName] === 1 || reinit) {
    table.find('tbody').html('')
  }
  table.block({
    message: null
  })
  // Append table
  $.ajax({
    url: `${baseUrl}ranap/end-point/get-data-kamar`,
    data: { ...paramDatatable[typeName], page: pageDataKamar[typeName], gender: jk },
    method: 'GET',
    beforeSend: () => {
      hideLoader()
    },
    success: (res) => {
      // Append if
      if(typeof res.data.length != 'undefined') {
        if (res.data.length === 0 && pageDataKamar[typeName] == 1) {
          table.find('tbody').html(`
            <tr>
              <td class="text-center" colspan="${columnGenerated.length}">Data tidak tersedia</td>
            </tr>
          `)
      } else {
        const records = res.data
        const tbodySection = table.find('tbody')
        const length = records.length > 10 ? 10 : records.length
        for (let indexRecord = 0; indexRecord < length; indexRecord++) {
          let trHtml = '<tr>'
          let valueOfColumn = ''
          for (let indexColumn = 0; indexColumn < columnGenerated.length; indexColumn++) {
            if (columnGenerated[indexColumn].data === 'rowNum') {
              valueOfColumn = (pageDataKamar[typeName] - 1) * 10 + (indexRecord + 1)
            } else {
              valueOfColumn = records[indexRecord][columnGenerated[indexColumn].data]
            }
            trHtml += `<td ${typeof columnGenerated[indexColumn].className !== 'undefined' ? `class="${columnGenerated[indexColumn].className}"` : ''}>${valueOfColumn}</td>`
          }
          tbodySection.append(`${trHtml}</tr>`)
          tbodySection.find('td').last().parent().attr('data-akomodasi', records[indexRecord]['is_akomodasi'] ? '1' : '0')
        }
        isStillExistDataKamar[typeName] = records.length > 10
        pageDataKamar[typeName] += 1
      }
      }
      
    },
    error: () => {
      table.find('tbody').html(`
        <tr>
          <td colspan="${columnGenerated.length}">Terjadi kesalahan</td>
        </tr>
      `)
    },
    complete: () => {
      table.unblock()
      $(`#searchBtn${key}`).prop('disabled', false)
    }
  })
}

const scrollWrapper = document.querySelector('#tableKamarWrapper');
const scrollWrapperTitipan = document.querySelector('#tableKamarTitipanWrapper');
const scrollWrapperAps = document.querySelector('#tableKamarApsWrapper');

scrollWrapper.addEventListener('scroll', function () {
  if (scrollWrapper.scrollTop + scrollWrapper.clientHeight >= scrollWrapper.scrollHeight && isStillExistDataKamar.general) {
    initDatatable();
  }
});

scrollWrapperTitipan.addEventListener('scroll', function () {
  if (Math.round(scrollWrapperTitipan.scrollTop + scrollWrapperTitipan.clientHeight) >= scrollWrapperTitipan.scrollHeight && isStillExistDataKamar.titipan) {
    initDatatable(true, false);
  }
});

scrollWrapperAps.addEventListener('scroll', function () {
  if (Math.round(scrollWrapperAps.scrollTop + scrollWrapperAps.clientHeight) >= scrollWrapperAps.scrollHeight && isStillExistDataKamar.aps) {
    initDatatable(false, true)
  }
});

let activeTable = 'kamar'





