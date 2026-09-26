var tmpObat = [];

function initializeSelect2() {
  $("#obatalkes_id").select2({
    placeholder: "-- Cari Obat --",
    minimumInputLength: 0,
    ajax: {
      url: "/master/kategori-obat/list-obat-alkes",
      dataType: "json",
      data: function (params) {
        return {
          q: params.term,
          page: params.page || 1,
        };
      },
      processResults: function (data, params) {
        params.page = params.page || 1;
        
        var filteredResults = data.result.filter(function(item) {
          return !tmpObat.some(function(existingItem) {
            return existingItem.obatalkes_id == item.id;
          });
        });
        
        return {
          results: filteredResults.map(function(item) {
            var kode = item.obatalkes_kode || (item.datavalue && item.datavalue.obatalkes_kode) || '';
            var nama = item.obatalkes_nama || item.text || '';
            return {
              id: item.id,
              text: (kode ? kode + ' - ' : '') + nama,
              datavalue: item.datavalue || item
            };
          }),
          pagination: {
            more: data.pagination && filteredResults.length > 0,
          },
        };
      },
      dropdownCssClass: "bigdrop",
      escapeMarkup: function (markup) {
        return markup;
      },
      templateResult: function (object) {
        return object.text;
      },
      templateSelection: function (subject) {
        return subject.text;
      },
    },
  });
}

function refreshSelect2() {
  $("#obatalkes_id").select2("destroy");
  initializeSelect2();
  $("#obatalkes_id").val(null).trigger("change");
}

// Function to auto-check "Umum" cara bayar
function autoCheckUmumCaraBayar() {
  // Only auto-check if this is a new form (not edit mode)
  if (!isEditMode) {
    // Find the "Umum" cara bayar group and check its children
    $('.group-wrapper').each(function() {
      var $groupWrapper = $(this);
      var $parentText = $groupWrapper.find('.parent-text');
      var groupText = $parentText.text().trim();
      
      // Check if this group contains "Umum" (case insensitive)
      if (groupText.toLowerCase().includes('umum')) {
        var $childList = $groupWrapper.find('.child-list');
        var $searchBox = $groupWrapper.find('.search-children');
        
        // Show the child list and search box
        $childList.show();
        $searchBox.show();
        
        // Update parent text to show expanded state
        if (groupText.includes('[ + ]')) {
          $parentText.text(groupText.replace('[ + ]', '[ - ]'));
        }
        
        // Check all children in this group
        $childList.find('input[type="checkbox"]').prop('checked', true);
        
        // Check the parent checkbox
        $groupWrapper.find('.parent-checkbox-input').prop('checked', true);
      }
    });
  }
}

$(document).ready(function () {
  if (typeof existingObatIds !== 'undefined' && existingObatIds.length > 0) {
    loadExistingObatData(existingObatIds);
  }
  
  // Auto-check "Umum" cara bayar when modal opens
  autoCheckUmumCaraBayar();
  
  $(document).on('click', '.data-edit', function() {
    $('#modal_backdrop').one('shown.bs.modal', function() {
      setTimeout(function() {
        autoExpandSelectedParents();
      }, 500);
    });
  });
  
  $(document).on('click', '[data-options="modal"]', function() {
    var url = $(this).attr('data-url') || $(this).attr('action');
    if (url && url.includes('/master/kategori-obat/create')) {
      $('#modal_backdrop').one('shown.bs.modal', function() {
        setTimeout(function() {
          autoExpandSelectedParents();
          autoCheckUmumCaraBayar(); // Auto-check Umum for new forms
        }, 500);
      });
    }
  });
  
  $(document).on('click', '[data-toggle="modal"]', function() {
    var action = $(this).attr('action');
    if (action && action.includes('/master/kategori-obat/create')) {
      $('#modal_backdrop').one('shown.bs.modal', function() {
        setTimeout(function() {
          autoExpandSelectedParents();
          autoCheckUmumCaraBayar(); // Auto-check Umum for new forms
        }, 500);
      });
    }
  });
  
  $(document).on('DOMNodeInserted', '#modal_backdrop', function(e) {
    if ($(e.target).find('#kategori-obat-form').length > 0) {
      setTimeout(function() {
        autoExpandSelectedParents();
        autoCheckUmumCaraBayar(); // Auto-check Umum for new forms
      }, 300);
    }
  });
  
  $(document).on('DOMNodeInserted', '#kategori-obat-form', function() {
    setTimeout(function() {
      autoExpandSelectedParents();
      autoCheckUmumCaraBayar(); // Auto-check Umum for new forms
    }, 200);
  });
  
  $(document).on('DOMNodeInserted', '.group-wrapper', function() {
    if ($('#modal_backdrop').is(':visible')) {
      setTimeout(function() {
        autoExpandSelectedParents();
        autoCheckUmumCaraBayar(); // Auto-check Umum for new forms
      }, 100);
    }
  });
  
  $(document).on('DOMNodeInserted', 'input[type="checkbox"]', function() {
    if ($('#modal_backdrop').is(':visible') && $(this).closest('#kategori-obat-form').length > 0) {
      setTimeout(function() {
        autoExpandSelectedParents();
        autoCheckUmumCaraBayar(); // Auto-check Umum for new forms
      }, 50);
    }
  });
  
  $(document).on('ajaxComplete', function(event, xhr, settings) {
    if (settings.url && settings.url.includes('/master/kategori-obat/create')) {
      setTimeout(function() {
        autoExpandSelectedParents();
        autoCheckUmumCaraBayar(); // Auto-check Umum for new forms
      }, 300);
    }
  });
  
  $(document).on('load', '.modal-content', function() {
    setTimeout(function() {
      autoExpandSelectedParents();
      autoCheckUmumCaraBayar(); // Auto-check Umum for new forms
    }, 200);
  });
  
  $(document).on('docoModalLoaded', function() {
    setTimeout(function() {
      autoExpandSelectedParents();
      autoCheckUmumCaraBayar(); // Auto-check Umum for new forms
    }, 150);
  });
  
  $(document).on('shown.bs.modal', '#modal_backdrop', function() {
    if ($('#kategori-obat-form').length > 0) {
      setTimeout(function() {
        autoExpandSelectedParents();
        autoCheckUmumCaraBayar(); // Auto-check Umum for new forms
      }, 250);
    }
  });
  
  var checkInterval = setInterval(function() {
    if ($('#kategori-obat-form').length > 0 && $('#modal_backdrop').is(':visible')) {
      var hasExpandedGroups = $('.group-wrapper .child-list:visible').length > 0;
      var hasCheckedChildren = $('.group-wrapper .child-list input[type="checkbox"]:checked').length > 0;
      
      if (hasCheckedChildren && !hasExpandedGroups) {
        autoExpandSelectedParents();
        autoCheckUmumCaraBayar(); // Auto-check Umum for new forms
        clearInterval(checkInterval);
      }
    }
  }, 500);
  
  setTimeout(function() {
    clearInterval(checkInterval);
  }, 10000);
  
  initializeSelect2();
  $("#btn-add-obat").on("click", function (e) {
    e.preventDefault();
    var selected = $("#obatalkes_id").select2("data")[0];
    
    var drugName = selected.datavalue.obatalkes_nama || selected.text;
    
    if (drugName && drugName.includes(' - ')) {
      var parts = drugName.split(' - ');
      if (parts.length > 1) {
        drugName = parts.slice(1).join(' - ');
      }
    }
    
    var input = {
      obatalkes_id: selected.id,
      obatalkes_nama: drugName,
      obatalkes_kode: selected.datavalue.obatalkes_kode,
      is_active: selected.datavalue.is_active,
    };
    if ($("#obatalkes_id").val() == null || $("#obatalkes_id").val() == "") {
      docoNotification(
        "warning",
        "Data Obat Belum Dipilih!Pilih salah satu obat."
      );
      return false;
    }
    var isExist = tmpObat.some(function (item) {
      return item.obatalkes_id === input.obatalkes_id;
    });

    if (isExist) {
      docoNotification(
        "warning",
        "Proses Gagal",
        `Obat ${drugName} sudah ditambahkan!.Silakan pilih obat lain.`
      );
      return false;
    }
    tmpObat.push(input);
    generateTable();
    refreshSelect2();
  });
  $(document).on("click", ".btn-delete-obat", function () {
    $(this).closest("tr").remove();
  });
  $(document).on("click", ".btn-save", function (e) {
    e.preventDefault();
    if (tmpObat.length == 0) {
      docoNotification("warning", "Proses Gagal", "Data obat masih kosong.");
      return false;
    }
    
    var isEditMode = typeof window.isEditMode !== 'undefined' && window.isEditMode;
    var url = "/master/kategori-obat/create";
    
    var _form = $("#kategori-obat-form").serializeArray();
    var formObject = {};
    $.each(_form, function (i, field) {
      let name = field.name.replace("[]", "");
      if (formObject[name]) {
        formObject[name].push(field.value);
      } else {
        formObject[name] = [field.value];
      }
    });
    
    if (isEditMode && typeof window.editId !== 'undefined' && window.editId) {
      formObject.id = window.editId;
    }
    
    $(this).docoForm("click", {
      url: url,
      method: "POST",
      data: {
        ...formObject,
        detail: JSON.stringify(tmpObat),
      },
      success: function (data) {
        $("#modal_backdrop").hide();
        setTimeout(function () {
            window.location.href = "/master/kategori-obat"
        },1);
      },
      error: function(data) {
          var title = data.responseJSON.response.title
          var message = data.responseJSON.response.text
          docoNotification('error', title, message);
      }
    });
  });

  $('.parent-text').on('click', function(e) {
      e.preventDefault();
      var $parentText = $(this);
      var $groupWrapper = $parentText.closest('.group-wrapper');
      var $childList = $groupWrapper.find('.child-list');
      var $searchBox = $groupWrapper.find('.search-children');
      
      if ($childList.is(':visible')) {
          $childList.hide();
          $searchBox.hide();
          $parentText.text($parentText.text().replace('[ - ]', '[ + ]'));
      } else {
          $childList.show();
          $searchBox.show();
          $parentText.text($parentText.text().replace('[ + ]', '[ - ]'));
      }
  });

  $('.parent-checkbox-input').on('change', function() {
      var $parent = $(this);
      var $childList = $parent.closest('.group-wrapper').find('.child-list');
      $childList.find('input[type="checkbox"]').prop('checked', $parent.is(':checked'));
  });

  $('.child-list input[type="checkbox"]').on('change', function() {
      var $child = $(this);
      var $childList = $child.closest('.child-list');
      var $parentCheckbox = $childList.closest('.group-wrapper').find('.parent-checkbox-input');
      
      var allChecked = true;
      $childList.find('input[type="checkbox"]').each(function() {
          if (!$(this).is(':checked')) {
              allChecked = false;
          }
      });
      
      $parentCheckbox.prop('checked', allChecked);
  });

  function logCheckedSelection() {
      var selection = {};

      $('.group-wrapper').each(function() {
          var $group = $(this);
          var $parentInput = $group.find('.parent-checkbox-input');
          var parentText = $group.find('.parent-text').text().trim().replace(/\\[ [+-] \\] /g, '');

          var checkedChildren = [];
          $group.find('.child-list input[type="checkbox"]:checked').each(function() {
              checkedChildren.push({
                  value: $(this).val(),
                  text: $(this).closest('label').text().trim()
              });
          });

          if (checkedChildren.length > 0) {
              selection[parentText] = {
                  parent_checked: $parentInput.is(':checked'),
                  children: checkedChildren
              };
          }
      });
  }

  $('.parent-checkbox-input, .child-list input[type="checkbox"]').on('change', function() {
      logCheckedSelection();
  });

  $('.custom-dropdown-checkbox').on('input', '.search-children', function() {
      var keyword = $(this).val().toLowerCase();
      var $childList = $(this).siblings('.child-list');
      $childList.find('li').each(function() {
          var text = $(this).text().toLowerCase();
          if (text.indexOf(keyword) > -1) {
              $(this).show();
          } else {
              $(this).hide();
          }
      });
  });

  $('#kategori-obat-form').on('submit', function(e) {
      logCheckedSelection();
  });

  $('#search-table-obat').on('input', function() {
      var keyword = $(this).val().toLowerCase();
      $('#table-obat tbody tr').each(function() {
          var rowText = $(this).text().toLowerCase();
          if (rowText.indexOf(keyword) > -1 || $(this).hasClass('isi-table-obat')) {
              $(this).show();
          } else {
              $(this).hide();
          }
      });
  });
});

function generateTable() {
  $(".isi-table-obat").hide();
  $("#table-obat > tbody").empty();

  var _html = "";
  var no = 1;
  tmpObat.forEach(function (val, key) {
    const rowStyle =
      val.is_active == 0 ? 'style="background-color: #ffc0cb;"' : "";
    _html += `
        <tr ${rowStyle}>
            <td>${no++}</td>
            <td>${val.obatalkes_kode}</td>
            <td>${val.obatalkes_nama}</td>
            <td><button type='button' data-id='${key}' class='delete-table-obat btn btn-danger btn-labeled btn-xs'><b><i class="fa fa-trash"></i></b>Hapus </button></td>
        </tr>
    `;
  });

  $("#table-obat > tbody").append(_html);

  $(".delete-table-obat").on("click", function (e) {
    e.preventDefault();
    var index = $(this).data("id");
    tmpObat.splice(index, 1);
    generateTable();
    refreshSelect2();
  });
  
  refreshSelect2();
}

function loadExistingObatData(obatIds) {
  if (!obatIds || obatIds.length === 0) {
    return;
  }
  
  $.ajax({
    url: '/master/kategori-obat/get-obat-details',
    type: 'POST',
    data: {
      obat_ids: obatIds
    },
    success: function(response) {
      if (response.success && response.data && response.data.length > 0) {
        tmpObat = [];
        
        response.data.forEach(function(obat) {
          tmpObat.push({
            obatalkes_id: obat.obatalkes_id,
            obatalkes_nama: obat.obatalkes_nama,
            obatalkes_kode: obat.obatalkes_kode,
            is_active: obat.is_active || 1
          });
        });
        
        generateTable();
        refreshSelect2();
      }
    },
    error: function(xhr, status, error) {
    }
  });
}

function autoExpandSelectedParents() {
  if (!$('#modal_backdrop').is(':visible')) {
    return;
  }
  
  if ($('#kategori-obat-form').length === 0) {
    return;
  }
  
  var groupWrappers = $('.group-wrapper');
  
  groupWrappers.each(function(index) {
    var $groupWrapper = $(this);
    var $childList = $groupWrapper.find('.child-list');
    var $searchBox = $groupWrapper.find('.search-children');
    var $parentText = $groupWrapper.find('.parent-text');
    var $parentCheckbox = $groupWrapper.find('.parent-checkbox-input');
    
    var checkedChildren = $childList.find('input[type="checkbox"]:checked');
    var totalChildren = $childList.find('input[type="checkbox"]');
    
    if (checkedChildren.length > 0) {
      $childList.show();
      $searchBox.show();
      
      var currentText = $parentText.text();
      if (currentText.includes('[ + ]')) {
        $parentText.text(currentText.replace('[ + ]', '[ - ]'));
      }
      
      if (checkedChildren.length === totalChildren.length) {
        $parentCheckbox.prop('checked', true);
      } else {
        $parentCheckbox.prop('checked', false);
      }
    }
  });
}
