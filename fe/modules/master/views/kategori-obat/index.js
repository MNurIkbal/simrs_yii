var table;

// Fungsi untuk mengupdate warna switch berdasarkan status
function updateSwitchColor($switch, isActive) {
  var $slider = $switch.siblings('.switch-slider');
  if (isActive) {
    $slider.removeClass('switch-inactive').addClass('switch-active');
  } else {
    $slider.removeClass('switch-active').addClass('switch-inactive');
  }
}

$(() => {
  table = $("#example").docoTabel({
    filter: false,
    columnDefs: [
      {
        orderable: false,
        className: "select-checkbox",
        targets: 0,
      },
    ],
    select: {
      style: "os",
      selector: "tr",
    },
    sorting: [],
    displayLength: 10,
    processing: true,
    serverSide: true,
    stateSave: false,
    ajax: {
      url: "/master/kategori-obat/get-data",
    },
    columns: [
      {
        data: null,
        render: function (data, type, full, meta) {
          return null;
        },
        searchable: false,
        orderable: false,
      },
      {
        data: null,
        searchable: false,
        orderable: false,
        render: function (data, type, row, rowAdditionalData) {
          var tableInfo = table.page.info();
          return tableInfo.start + rowAdditionalData.row + 1;
        },
      },
      {
        data: "restriction_obat_nama",
      },
      {
        data: "instalasi_nama",
        orderable: false,
      },
      {
        data: "penjamin_nama",
        orderable: false,
      },
      {
        data: "jml",
        searchable: false,
        orderable: false,
      },
      {
        data: "is_active",
        render: function (data, type, row) {
          var isActive = data === "Aktif" || data === true || data === 1;
          var checked = isActive ? "checked" : "";
          var switchClass = isActive ? "switch-active" : "switch-inactive";

          return `<label class="switch-container">
            <input type="checkbox" class="status-switch" data-id="${row.primary}" ${checked}>
            <span class="switch-slider ${switchClass}"></span>
            </label>`;
        },
        searchable: false,
        orderable: false,
      },
    ],
    formFilters: [
      {
        fieldName: "restriction_obat_nama",
        label: "Nama Kategori",
      },
      {
        fieldName: "instalasi_id",
        label: "Instalasi",
        type: {
          name: "selectMultiple",
          payload: instalasiOption,
        },
      },
      {
        fieldName: "penjamin_id",
        label: "Penjamin",
        type: {
          name: "selectMultiple",
          payload: penjaminOption,
        },
      },
      {
        fieldName: "is_active",
        label: "Status",
        type: {
          name: "select",
          payload: [
            {
              id: 1,
              text: "Aktif",
            },
            {
              id: 0,
              text: "Tidak Aktif",
            },
          ],
        },
      },
    ],
  });
  $("#btn-search__example").css("display", "none");
  $("#btn-reset__example").css("display", "none");
  $(document).on("click", ".btn-reset", function (e) {
    const tableId = "example";
    const element = $(`#filter-section__${tableId}`);
    const formWrapper = $(`#form-filter__${tableId}`);
    element.find("input").val("");
    element.find("select").val(null).trigger("change");
    const tableElement = $(`#${tableId}`).DataTable();
    showLoader();
    tableElement.context[0].ajax.data.advancedFilter =
      serializeArrayToJson(formWrapper);
    tableElement.ajax.reload();
  });
  $(document).on(
    "keydown",
    "#example-restriction_obat_nama--form",
    function (e) {
      if (e.key === "Enter") {
        $("#btn-search__example").trigger("click");
      }
    }
  );


  // Event untuk mengupdate warna switch secara real-time
  $(document).on("change", ".status-switch", function (e) {
    // Update warna switch berdasarkan status checkbox
    updateSwitchColor($(this), $(this).prop("checked"));
    
    e.preventDefault();
        var dataStatus = "0";
        var state = $(this).prop("checked");
        var dataId = $(this).attr("data-id");
        var originalState = !state;
        var $switch = $(this);

        if (e.target.checked == true) {
            dataStatus = "1";
        }
        
        $(this).docoForm("delete",{
            url: baseUrl+"master/kategori-obat/change-status?id="+dataId+"&status="+dataStatus,
            confirmTitle : "Konfirmasi",
            confirmMessage : "Apa anda yakin ingin mengubah status data?",
            success : function (data) {
              updateSwitchColor($switch, dataStatus === "1");
              table.draw();
            },
            error : function (error) {
              $switch.prop("checked", originalState);
              updateSwitchColor($switch, originalState);
            }
        });
        
        // Override confirmRejectAction untuk checkbox biasa
        var originalConfirmRejectAction = docoHelper.confirmRejectAction;
        docoHelper.confirmRejectAction = function(options, _this, btn) {
            // Kembalikan switch ke posisi semula
            $switch.prop("checked", originalState);
            // Update warna switch kembali ke status awal
            updateSwitchColor($switch, originalState);
            // Panggil fungsi asli
            originalConfirmRejectAction(options, _this, btn);
            // Restore fungsi asli
            docoHelper.confirmRejectAction = originalConfirmRejectAction;
        };
  });

  function updateButtonStates() {
    const selectedRow = table.row('.selected').data();
    const editBtn = $('.data-edit');
    const deleteBtn = $('.data-delete');
    if (selectedRow && (selectedRow.is_active === 'Aktif' || selectedRow.is_active === true || selectedRow.is_active === 1)) {
        editBtn.prop('disabled', false).removeClass('disabled');
        deleteBtn.prop('disabled', false).removeClass('disabled');
    } else {
        editBtn.prop('disabled', true).addClass('disabled');
        deleteBtn.prop('disabled', true).addClass('disabled');
    }
  }

  updateButtonStates();

  $("#example tbody").on("click", "tr", function(){
    try {
      primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
    } catch (e) {
      primaryKey = false;
    }
    updateButtonStates();
  });

  table.on('draw.dt', function() {
      updateButtonStates();
  });
});
