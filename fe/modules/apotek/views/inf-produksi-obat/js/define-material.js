$(document).ready(function () {
  firstRender();
  $("#depofarmasi").select2({
    placeholder: "-- Pilih Depo/Gudang --",
    allowClear: true,
  });

  renderSelect();

  $("#btn-kembali").click(function () {    
    if(Object.keys(existObat).length > 0) {
      let valuePayload = []
      let keyPayload = []

      for (let key in cleanObat) {
        valuePayload.push({
          name: key,
          value: JSON.stringify(cleanObat[key]),
        });

        keyPayload.push(key)
      }

      valuePayload.push(
        {
          name: "produksi_obat",
          value: JSON.stringify(keyPayload),
        },
        {
          name: "pemesananproduksi_id",
          value: pemesananId,
        }
      );

      $().docoForm("click", {
          url: "/apotek/inf-produksi-obat/save-cache-material",
          type: "POST",
          data: valuePayload,
          skipConfirm: true,
          skipSuccessNotif: true,
          success: function (data) {
            if (isEdit) {
              window.location.href = `/apotek/inf-produksi-obat/detail-produksi?id=${produksiObatAlkesId}`
            } else {
              window.location.href = "/apotek/inf-produksi-obat/index-produksi";
            }
          }
      });
    } else {
      if (isEdit) {
        window.location.href = `/apotek/inf-produksi-obat/detail-produksi?id=${produksiObatAlkesId}`
      } else {
        window.location.href = "/apotek/inf-produksi-obat/index-produksi";
      }
    }
  })

  $("#btn-simpan-produksi-obat").click(function () {
    if(Object.keys(existObat).length > 0) {
      let valuePayload = []
      let keyPayload = []
      let validate = true

      for (let key in cleanObat) {
        valuePayload.push({
          name: key,
          value: JSON.stringify(cleanObat[key]),
        });

        cleanObat[key].forEach(item => {
          if(item.qty === 0 || isNaN(item.qty)) {
            validate = false
            return false
          }
        });

        keyPayload.push(key)
      }

      if(validate === false) {
        docoNotification('warning', 'Proses Gagal!', 'Qty obat tidak boleh 0');
        return false
      }

      valuePayload.push(
        {
          name: "produksi_obat",
          value: JSON.stringify(keyPayload),
        },
        {
          name: "pemesananproduksi_id",
          value: pemesananId,
        },
        {
          name: "depofarmasi",
          value: $("#depofarmasi").val()
        },
        {
          name: "defaultdepo",
          value: $("#default_ruangan").val()
        },
        {
          name: "is_edit",
          value: isEdit
        }
      );

      $().docoForm("click", {
          url: "/apotek/inf-produksi-obat/save-define-material",
          type: "POST",
          data: valuePayload,
          skipNotifyMessage: true,
          success: function (data) {
            console.log(data, "Success data");
            if(data?.meta?.code === 200) {
              docoNotification('success', 'Proses Berhasil!', data?.data?.message);
              setTimeout(() => {
                if(isEdit) {
                  window.location.href = `/apotek/inf-produksi-obat/detail-produksi?id=${produksiObatAlkesId}`
                } else {
                  window.location.href = `/apotek/inf-produksi-obat/index-produksi`
                }
              }, 1000);
            }
          }
      });
    } else {
      docoNotification('error', 'Proses Gagal!', 'Tidak ada obat yang ditambahkan');
    }
  });
});

function firstRender() {
  let childItem = [];
  let valueParse = JSON.parse(defaultValue);
  let indexRacikan = 'racikan'

  /**
   * Render header item.
   */
  valueParse.forEach((item, index) => {
    if (item?.detailObat.length > 0) {
      item.detailObat.forEach((value, indexvalue) => {
        if(existObat[`${indexRacikan}-${value.produksiObatId}`] === undefined) {
          existObat[`${indexRacikan}-${value.produksiObatId}`] = [];
        }

        if(cleanObat[`${indexRacikan}-${value.produksiObatId}`] === undefined) { 
          cleanObat[`${indexRacikan}-${value.produksiObatId}`] = [];
        }

        existObat[`${indexRacikan}-${value.produksiObatId}`].push(value.obatAlkesId);
        cleanObat[`${indexRacikan}-${value.produksiObatId}`].push({
          "id": value.obatAlkesId,
          "produksiObatId": value.produksiObatId,
          "obatAlkesId": value.obatAlkesId,
          "namaObat": value.namaObat,
          "hargaNetto": value.hargaSatuan ?? value.hargaNetto,
          "satuan": value.satuan,
          "satuanId": value.satuanId,
          "qty": value.qty
        })

        if(indexvalue == 0) {
          item.firstItem = value;
        } else {
          childItem.push(value);
        }
      });
    }
    
    produksiObatArray.push(item.produksiObatId)
    let html = componentRender(index, item);

    $(".data-produksi-obat-alkes").append(html);
  });

  /**
   * Render children item.
   */
  if (childItem.length > 0) {
    childItem.forEach((item) => {
      childComponentRender(item.produksiObatId,$(`.produksiobat-${item.produksiObatId}`), item);
    });
  }
}

/**
 * Component first render.
 *
 * @param {any} index
 * @param {any} data
 * @returns
 */
function componentRender(index, data) {
  let firstItem;
  let hargaSatuanItem = 0;

  if(data?.firstItem !== undefined) {
    firstItem = data?.firstItem
  }

  if(firstItem !== undefined) {
    hargaSatuanItem = firstItem?.hargaSatuan ?? firstItem.hargaNetto 
  }

  return `
    <tr class="racikan-${data.produksiObatId}">
        <td rowspan="1" class="produksiobat-${data.produksiObatId}">${index + 1}</td>
        <td rowspan="1" class="produksiobat-${data.produksiObatId}">${data.namaObat}</td>
        <td rowspan="1" class="produksiobat-${data.produksiObatId}">${data.qty}</td>
        <td rowspan="1" class="produksiobat-${data.produksiObatId}">${data.satuan}</td>
        <td style="width: 20%;">
            <select class="form-control select2 obatalkes_id" data-obatalkes="${firstItem !== undefined ? firstItem?.obatAlkesId : 0}" data-produksiobat="${data.produksiObatId}" data-index="1">
              ${firstItem !== undefined ? `<option value="${firstItem?.obatalkesId}" selected>${firstItem?.namaObat}</option>` : ''}
            </select>
        </td>
        <td style="width: 10%;">
            <div style="padding: 5px;">
                <input type="text m-2 pb-2" class="form-control doco-decimal child-qty inputan-obatalkes" name="qty[]" autocomplete="off" data-produksiobat="${data.produksiObatId}" data-obatalkes="${firstItem !== undefined ? firstItem?.obatAlkesId : 0}" data-index="1" value="${firstItem !== undefined ? firstItem?.qty : '0'}">
            </div>
        </td>
        <td>
            <select class="form-control select-satuan child-satuan-${data.produksiObatId}-1 inputan-obatalkes" data-produksiobat="${data.produksiObatId}" data-index="1">
              ${firstItem !== undefined ? `<option value="${firstItem?.satuan}" selected>${firstItem?.satuan}</option>` : ''}
            </select>
        </td>
        <td>
            <span class="child-harga-${data.produksiObatId}-1" data-produksiobat="${data.produksiObatId}" data-harga="${firstItem !== undefined ? hargaSatuanItem : "0"}" data-index="1">Rp.${firstItem !== undefined ? 
            docoHelper.convertToRupiah(hargaSatuanItem) : '0'}</span>
        </td>
        <td>
            <span class="child-total-${data.produksiObatId}-1" data-produksiobat="${data.produksiObatId}" data-total="0">Rp.${firstItem !== undefined ? docoHelper.convertToRupiah(hargaSatuanItem * firstItem?.qty) : '0'}</span>
        </td>
        <td>
            <button class="btn btn-success btn-sm btn-add-material" id="btn-add-material" data-produksiobat="${data.produksiObatId}"><i class="fa fa-plus"></i></button>
        </td>
    </tr>
  `;
}

function childComponentRender(produksiObatId, parent, data) {
  let lastTr = $(`.racikan-${produksiObatId}`).last();
  let rowSpan = parent.attr("rowspan");
  let indexRow = parseInt(rowSpan) + 1;
  let hargaSatuanItem = data?.hargaSatuan ?? data?.hargaNetto;

  let newRow = `
    <tr class="racikan-${data.produksiObatId}">
        <td style="width: 20%;">
            <select class="form-control select2 obatalkes_id" data-obatalkes="${data?.obatAlkesId}" data-produksiobat="${data.produksiObatId}" data-index="${indexRow}">
              ${data !== undefined ? `<option value="${data?.obatalkesId}" selected>${data?.namaObat}</option>` : ''}
            </select>
        </td>
        <td style="width: 10%;">
            <div style="padding: 5px;">
                <input type="text m-2 pb-2" class="form-control doco-decimal child-qty inputan-obatalkes" name="qty[]" autocomplete="off" data-produksiobat="${data.produksiObatId}" data-obatalkes="${data?.obatAlkesId}" data-index="${indexRow}" value="${data !== undefined ? data?.qty : '0'}">
            </div>
        </td>
        <td>
            <select class="form-control select-satuan child-satuan-${data.produksiObatId}-${indexRow} inputan-obatalkes" data-produksiobat="${data.produksiObatId}" data-index="${indexRow}">
              ${data !== undefined ? `<option value="${data?.satuan}" selected>${data?.satuan}</option>` : ''}
            </select>
        </td>
        <td>
            <span class="child-harga-${data.produksiObatId}-${indexRow}" data-produksiobat="${data.produksiObatId}" data-harga="${data !== undefined ? hargaSatuanItem : '0'}" data-index="${indexRow}">Rp.${data !== undefined ? docoHelper.convertToRupiah(hargaSatuanItem): '0'}</span>
        </td>
        <td>
            <span class="child-total-${data.produksiObatId}-${indexRow}" data-produksiobat="${data.produksiObatId}" data-total="0">Rp.${data !== undefined ? docoHelper.convertToRupiah(hargaSatuanItem * data?.qty) : '0'}</span>
        </td>
        <td>
            <button class="btn btn-danger btn-sm btn-remove-material inputan-obatalkes" data-produksiobat="${produksiObatId}" data-obatalkes="${data?.obatAlkesId}""><i class="fa fa-trash"></i></button>
        </td>
    </tr>
  `;

  parent.attr("rowspan", indexRow);
  lastTr.after(newRow);
}

/**
 * Renders a select2 dropdown with options to select an obat.
 *
 * @return {void}
 */
function renderSelect() {

  $(".select-satuan").select2();
  $("#depofarmasi").select2();

  $(".obatalkes_id")
    .docoPaginationSelec2(
      (config = {
        placeholder: "Pilih Obat ... ",
        _api: baseUrl + "apotek/inf-produksi-obat/search-obat-material",
        ajax: {
          data: function (params) {
            return {
              q: params.term,
              page: params.page || 1,
            };
          },
          results: function (data, params) {
            let more = params.page * 30 < data.total_count;
            return { results: data.items, more: more };
          },
          processResults: function (res, params) {
            params.page = params.page || 1;
            let arr = [];
            $.each(res.result, function (index, value) {
              if (index < 10) {
                arr.push({
                  id: value.id,
                  text: value.text,
                });

                detailObat.push(value);
              }
            });
            return {
              results: arr,
              pagination: {
                more: res.result.length > 10,
              },
            };
          },
        },
      })
    )
    .on("select2:selecting", function (event) {
      let thisValue = event.params.args.data?.id
      let produksiObatId = $(this).attr("data-produksiobat");

      /**
       * Validasi apabila obat sudah di inputkan
       */
      if(Object.keys(existObat).length > 0) {
        if(existObat[`racikan-${produksiObatId}`] !== undefined) {
          if (existObat[`racikan-${produksiObatId}`].includes(thisValue)) {
            docoNotification('warning','Oops!', "Data Obat Sudah Di Input", );
            return false
          }
        }
      }

    })
    .on("select2:select", function (e) {
      let thisValue = $(this).val();
      let obatalkes_id = $(this).attr("data-obatalkes");
      let produksiObatId = $(this).attr("data-produksiobat");
      let indexRow = $(this).attr("data-index");
      let indexRacikan = `racikan-${produksiObatId}`

      if(cleanObat[indexRacikan] === undefined) {
        cleanObat[indexRacikan] = [];
      } 

      if(existObat[indexRacikan] === undefined) {
        existObat[indexRacikan] = [];
      }

      $.each(detailObat, function (index, value) {
        if (value.id == thisValue) {
          if (!existObat[indexRacikan].includes(value.id)) {
            cleanObat[indexRacikan].push({
              "id": value.id,
              "produksiObatId": produksiObatId,
              "obatAlkesId": value.id,
              "namaObat": value.text,
              "hargaNetto": value.harga_netto,
              "satuan": value.satuankecil_nama,
              "satuanId": value.satuankecil_id,
              "qty": 0
            })
            existObat[indexRacikan].push(value.id);
          }
        }
      });

      if (obatalkes_id != 0) {
        let indexObat = cleanObat[indexRacikan].findIndex(function (item) {
          return item.id == obatalkes_id;
        });

        if (indexObat != -1) {
          cleanObat[indexRacikan].splice(indexObat, 1);
          existObat[indexRacikan].splice(indexObat, 1);
        }
      }

      setTimeout(() => {
        $(this).attr("data-obatalkes", thisValue);
        
        $(this)
        .closest("tr")
        .find(".child-qty")
        .val(0);
        
        $(this)
        .closest("tr")
        .find(".btn-remove-material")
        .attr("data-obatalkes", thisValue);

        $(this)
        .closest("tr")
        .find(".inputan-obatalkes")
        .attr("data-obatalkes", thisValue);
        renderObatRow(produksiObatId, thisValue, indexRow);
      }, 100)

    });
}

/**
 * Event trigger on change qty
 */
$(document).on("change", ".child-qty", function (event) {
  let qty = $(this).val();
  let produksiObatId = $(this).attr("data-produksiobat");
  let indexRow = $(this).attr("data-index");
  let indexRacikan = `racikan-${produksiObatId}`
  let obatalkesId = $(this).attr("data-obatalkes");
  let hargaNetto = $(`.child-harga-${produksiObatId}-${indexRow}`).attr(
    "data-harga"
  );

  if(qty == '') {
    qty = 0
  }

  let roundUp = qty
  $(`.child-total-${produksiObatId}-${indexRow}`).html(
    "Rp. " + docoHelper.convertToRupiah(hargaNetto * roundUp)
  );

  let indexObat = cleanObat[indexRacikan].find(function (item) {
    return item.id == obatalkesId;
  });

  if(indexObat !== undefined) {
    indexObat.qty = parseFloat(roundUp)
    $(this).val(roundUp);
  }
});

/**
 * Event trigger on click add material
 */
$(document).on("click", "#btn-add-material", function (event) {
  event.preventDefault();
  let produksiObatId = $(this).data("produksiobat");
  let parent = $(".produksiobat-" + produksiObatId);
  addMaterial(produksiObatId, parent);
});

/**
 * Event trigger on click remove material
 */
$(document).on("click", ".btn-remove-material", function (event) {
  event.preventDefault();
  let produksiObatId = $(this).data("produksiobat");
  let obatalkesId = $(this).data("obatalkes");
  let parent = $(this).closest("tr");
  let indexRacikan = `racikan-${produksiObatId}`
  deleteMaterial(produksiObatId, parent);

  if (obatalkesId !== undefined) {
    let indexObat = cleanObat[indexRacikan].findIndex(function (item) {
      return item.id == obatalkesId;
    });

    let indexObatExist = existObat[indexRacikan].findIndex(function (item) {
      return item == obatalkesId;
    });

    if (indexObat != -1) {
      cleanObat[indexRacikan].splice(indexObat, 1);
    }

    if (indexObatExist != -1) {
      existObat[indexRacikan].splice(indexObatExist, 1);
    }
  }
});

/**
 * Add new material to table
 * 
 * @param {int} produksiObatId 
 * @param {node} parent 
 */
function addMaterial(produksiObatId, parent) {
  let lastTr = $(`.racikan-${produksiObatId}`).last();
  let rowSpan = parent.attr("rowspan");
  let indexRow = parseInt(rowSpan) + 1;
  let newRow = `
       <tr class="racikan-${produksiObatId}">
            <td style="width: 20%;" class="childobat-${produksiObatId}">
                <select class="form-control select2 obatalkes_id" data-produksiobat="${produksiObatId}" data-obatalkes="0" data-index="${indexRow}"></select>
            </td>
            <td style="width: 10%;">
                <div style="padding: 5px;">
                    <input type="text m-2 pb-2" class="form-control doco-decimal child-qty inputan-obatalkes" name="qty[]" autocomplete="off" data-produksiobat="${produksiObatId}" data-index="${indexRow}" value="0">
                </div>
            </td>
            <td>
                <select class="form-control select-satuan child-satuan-${produksiObatId}-${indexRow} inputan-obatalkes" data-produksiobat="${produksiObatId}" data-index="${indexRow}"></select>
            </td>
            <td>
                <span class="child-harga-${produksiObatId}-${indexRow}" data-produksiobat="${produksiObatId}" data-harga="0" data-index="${indexRow}"></span>
            </td>
            <td>
                <span class="child-total-${produksiObatId}-${indexRow}" data-produksiobat="${produksiObatId}" data-total="0" data-index="${indexRow}"></span>
            </td>
            <td>
                <button class="btn btn-danger btn-sm btn-remove-material inputan-obatalkes" data-produksiobat="${produksiObatId}" data-obatalkes="0"><i class="fa fa-trash"></i></button>
            </td>
       </tr>
    `;
  parent.attr("rowspan", indexRow);
  lastTr.after(newRow);
  renderSelect();
}

/**
 * Deletes a material from the DOM.
 *
 * @param {string} produksiObatId - The ID of the production.
 * @param {jQuery} parent - The parent element of the material.
 * @return {void}
 */
function deleteMaterial(produksiObatId, parent) {
  let rowspanTd = $(`.produksiobat-${produksiObatId}`);
  let rowSpan = parseInt(rowspanTd.attr("rowspan")) - 1;
  rowspanTd.attr("rowspan", rowSpan);
  parent.remove();
}

/**
 * Renders the obat row in the DOM based on the provided data.
 *
 * @param {string} produksiObatId - The ID of the production.
 * @param {number} obatalkesId - The ID of the obat.
 * @param {number} indexRow - The index of the row.
 * @return {void}
 */
function renderObatRow(produksiObatId, obatalkesId, indexRow) {
  let indexRacikan = `racikan-${produksiObatId}`;
  
  if(cleanObat[indexRacikan] !== undefined) {
    let dataObat = cleanObat[indexRacikan].find(function (item) {
      return item.id == obatalkesId;
    });
    
    if (dataObat !== undefined) {
      $(`.child-harga-${produksiObatId}-${indexRow}`).html(
        "Rp. " + docoHelper.convertToRupiah(dataObat.hargaNetto)
      );
      $(`.child-total-${produksiObatId}-${indexRow}`).html(
        "Rp. 0"
      );
      $(`.child-harga-${produksiObatId}-${indexRow}`).attr(
        "data-harga",
        dataObat.hargaNetto
      );
  
      let listSatuan = `
        <option>${dataObat.satuan}</option>
      `;
      $(`.child-satuan-${produksiObatId}-${indexRow}`).html(listSatuan);
    }
  }
}
