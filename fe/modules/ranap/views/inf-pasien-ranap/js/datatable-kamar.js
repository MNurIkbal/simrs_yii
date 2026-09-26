
let table;
let isStillExistDataKamar = {
  general: true,
  titipan: true
}
let pageDataKamar = {
  general: 1,
  titipan: 1
}
let columnGenerated = []
function createHeaderDatatableKamar(isKamarTitipan) {
  const key = isKamarTitipan ? 'Titipan' : 'NonTitipan'
  if ($(`#filterHeaderKamar${key}`).length === 0) {
    $("#filterHeader").append(`
    <div id="filterHeaderKamar${key}">
        <div class="col-md-${isKamarTitipan ? '3' : '4'}">
            <div class="form-group">
                <label for="jenis_penyakit">Jenis Penyakit</label>
                <select name="jenisPenyakit" id="jenisPenyakitFilter${key}" class="form-control"></select>
            </div>
        </div>
        ${isKamarTitipan ? `
          <div class="col-md-3 filterKamarSection">
              <div class="form-group">
                  <label for="jenis_penyakit">Kelas</label>
                  <select name="jenisPenyakit" id="kelasFilter${key}" class="form-control"></select>
              </div>
          </div>
        ` : ''}
        <div class="col-md-${isKamarTitipan ? '3' : '4'}">
            <div class="form-group">
                <label for="jenis_penyakit">Ruangan</label>
                <select name="jenisPenyakit" id="ruanganFilter${key}" class="form-control"></select>
            </div>
        </div>
        <div class="col-md-${isKamarTitipan ? '3' : '4'}">
            <div class="form-group">
                <label for="jenis_penyakit">Kamar</label>
                <select name="jenisPenyakit" id="kamarFilter${key}" class="form-control"></select>
            </div>
        </div>
        <div class="col-sm-12 button-search-section">
            <button type="button" id="searchBtn${key}" class="btn btn-info btn-sm btn-labeled pull-right"><b class="fa fa-lg fa-search"></b> Cari</button>
        </div>
    </div>
    `)
    initFilter(key)
    const kelasPelayananId = !isKamarTitipan ? $("#kelaspelayanan_id").val() : $(`#kelasFilter${key}`).val()
    $(`#kamarFilter${key}`).prop('disabled', false)
    $(`#ruanganFilter${key}`).prop('disabled', false)
    $(`#ruanganFilter${key}`).bind('change', ({ delegateTarget }) => {
      if ($(delegateTarget).val() === '' || $(delegateTarget).val() === '-' || $(delegateTarget).val() === 'Semua' || $(delegateTarget).val() == null) {
        $(`#kamarFilter${key}`).prop('disabled', false)
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
          kelasPelayananId: !isKamarTitipan ? $("#kelaspelayanan_id").val() : $(`#kelasFilter${key}`).val(),
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
      initRuanganSource(key, isKamarTitipan)
    })
    $(`#kelasFilter${key}`).bind('change', ({ delegateTarget }) => {
      initRuanganSource(key, isKamarTitipan)
    })

    if (!isKamarTitipan) {
      $(`#jenisPenyakitFilter${key}`).val($("#jeniskasuspenyakit_id").val()).trigger('change')
    }
    $(`#searchBtn${key}`).bind('click', ({ delegateTarget }) => {
      initDatatable(isKamarTitipan, true)
    })
  }
}

function initRuanganSource(key, isKamarTitipan) {
  const kelasPelayananId = !isKamarTitipan ? $("#kelaspelayanan_id").val() : $(`#kelasFilter${key}`).val()
  if ($(`#jenisPenyakitFilter${key}`).val() === '' || $(`#jenisPenyakitFilter${key}`).val().toLowerCase() === 'semua' || kelasPelayananId.toLowerCase() === 'semua' || kelasPelayananId === '') {
    $(`#ruanganFilter${key}`).prop('disabled', false)
    $(`#ruanganFilter${key}`).val('Semua').trigger('change')
    return false
  }

  if ($(`#ruanganFilter${key}`).hasClass("select2-hidden-accessible")) {
    $(`#ruanganFilter${key}`).select2('destroy')
    $(`#ruanganFilter${key}`).html('')
  }
  $.ajax({
    url: `/ranap/end-point/get-list-ruangan`,
    method: 'GET',
    data: {
      jenisKasusPenyakit: $(`#jenisPenyakitFilter${key}`).val(),
      kelasPelayanan: kelasPelayananId
    },
    success: (res) => {
      $(`#ruanganFilter${key}`).prop('disabled', false)
      const dataOutput = res.response
      const dataRuangan = [
        {
          id: '-',
          text: 'Semua'
        }
      ]
      Object.keys(dataOutput).map((keyItem) => {
        dataRuangan.push({
          id: keyItem,
          text: dataOutput[keyItem]
        })
      })
      refreshOptionSelect2($(`#ruanganFilter${key}`), dataRuangan)
    },
    error: () => {
      refreshOptionSelect2($(`#ruanganFilter${key}`), [])
    },
    complete: () => {
      hideLoader()
      $("#kamarFilter").prop('disabled', true)
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
  dataKasus.map((item) => {
    dataKasusArray.push({
      id: item.kelaspelayanan_id,
      text: item.kelaspelayanan_nama
    })
  })
  let dataJenisPenyakit = [
    {
      id: '',
      text: 'Semua'
    }
  ]
  dataPenyakit.map((item) => {
    dataJenisPenyakit.push({
      id: item.jeniskasuspenyakit_id,
      text: item.jeniskasuspenyakit_nama
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
  'general': {},
}
function setParamDatatable(typeName) {
  if (Object.keys(paramDatatable[typeName]).length == 0) {
    const key = (typeName === 'titipan' ? 'Titipan' : 'NonTitipan')
    paramDatatable[typeName] = {
      penjamin_id: penjaminId,
      kamar_id: typeof $(`#kamarFilter${key}`) !== 'undefined' && $(`#kamarFilter${key}`).val() !== null ? $(`#kamarFilter${key}`).val() : '',
      status_kamar: typeof $(`#statusKamarFilter${key}`) !== 'undefined' && $(`#statusKamarFilter${key}`).val() !== null ? $(`#statusKamarFilter${key}`).val() : '',
      ruangan_id: typeof $(`#ruanganFilter${key}`) !== 'undefined' && $(`#ruanganFilter${key}`).val() !== null ? $(`#ruanganFilter${key}`).val() : '',
      jenis_id: typeof $(`#jenisPenyakitFilter${key}`) !== 'undefined' && $(`#jenisPenyakitFilter${key}`).val() !== null ? $(`#jenisPenyakitFilter${key}`).val() : '',
      kelas_id: typeof $(`#kelasFilter${key}`) !== 'undefined' && $(`#kelasFilter${key}`).val() !== null ? $(`#kelasFilter${key}`).val() : ''
    }
    if (typeName !== 'titipan') {
      paramDatatable[typeName].kelas_id = $("#kelaspelayanan_id").val()
    }
  }
}
function initDatatable(isKamarTitipan = false, reinit = false) {
  const typeName = isKamarTitipan ? 'titipan' : 'general'
  const key = isKamarTitipan ? 'Titipan' : 'NonTitipan'
  $(`#searchBtn${key}`).prop('disabled', true)

  if (reinit) {
    pageDataKamar[typeName] = 1
    paramDatatable[typeName] = {}
  }
  createHeaderDatatableKamar(isKamarTitipan)
  setParamDatatable(typeName)
  // const { kamar_id, status_kamar, ruangan_id, jenis_id, penjamin_id, kelas_id } = paramDatatable[typeName]
  if (isKamarTitipan) {
    columnGenerated = columnKamarTitipan
    table = $('#tableKamarTitipan')
    $('#tableKamarWrapper').hide()
  } else {
    columnGenerated = columns
    table = $('#tableKamar')
    $('#tableKamarTitipanWrapper').hide()
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
    url: `/ranap/end-point/get-data-kamar-default`,
    data: { ...paramDatatable[typeName], page: pageDataKamar[typeName], gender: jk },
    method: 'GET',
    beforeSend: () => {
      hideLoader()
    },
    success: (res) => {
      // Append if
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

const scrollWrapper = document.querySelector('#tableKamarWrapper')
scrollWrapper.addEventListener('scroll', function () {
  if (scrollWrapper.scrollTop + scrollWrapper.clientHeight >= scrollWrapper.scrollHeight && isStillExistDataKamar.general) {
    initDatatable()
  }
});
const scrollWrapperTitipan = document.querySelector('#tableKamarTitipanWrapper')
scrollWrapperTitipan.addEventListener('scroll', function () {
  if (Math.round(scrollWrapperTitipan.scrollTop + scrollWrapperTitipan.clientHeight) >= scrollWrapperTitipan.scrollHeight && isStillExistDataKamar.titipan) {
    initDatatable(true)
  }
});

let activeTable = 'kamar'