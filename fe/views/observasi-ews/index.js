$(document).ready(function () {
  let limit = 20;
  localStorage.removeItem("max-limit-observasi");
  localStorage.removeItem("total-records-observasi");
  localStorage.removeItem("last-jenis-ews");
  localStorage.setItem("last-jenis-ews", latestJenisEws);
  $('#btn-search-ews').off('click').on('click', function(e) {
    e.preventDefault();
    // Reset limit and localStorage when manually searching to ensure fresh data
    limit = 20;
    localStorage.removeItem("max-limit-observasi");
    localStorage.removeItem("total-records-observasi");
    fetchDataEws();
  })

  $('#btn-refresh-ews').off('click').on('click', function(e) {
    e.preventDefault();
    limit = 20;
    localStorage.removeItem("max-limit-observasi");
    localStorage.removeItem("total-records-observasi");
    
    $('.startDateEws').val('');
    $('.endDateEws').val('');
    $('.targetDateEws').val('');
    
    fetchDataEws();
  })

  $('#observasiEwsEmpty').DataTable({
    ordering: false,
    searching: false,
    fixedHeader: true,
    paging: false,
    lengthChange: false,
    info: false,
    language: {
        emptyTable: "Belum Ada data EWS" // Ubah sesuai kebutuhan
    }
  })
  
  var options = new Option(latestJenisEwsNama, latestJenisEws, true, true);
  $('.selectJenisEws').append(options).trigger('change');

  $(".selectJenisEws").select2InfinityScroll({
		url: `/${modul}${url}/filters`,
		callbackData: (param) => {
			return {
				payload: {
					...param,
                    type: 'jenis_ews'
				}
			}
		},
  });

  // Hide mandatory indicator when EWS type is selected
  $('.selectJenisEws').on('change', function() {
    var selectedValue = $(this).val();
    if (selectedValue && selectedValue !== '') {
      $('#error-jenis-ews').addClass('hidden');
    }

    let lastJenisEws = localStorage.getItem("last-jenis-ews");
    if (lastJenisEws != "" && lastJenisEws !== selectedValue) {
        let header = 'Perhatian !';
        let message = 'Perubahan jenis EWS akan menghitung ulang skor dari data yang ada, Lanjutkan ubah jenis EWS ?';
        let label = {
            buttons: {
                'Yes': 'button-yes',
                'No': 'button-no'
            }
        };

      $.showQuestionDialog(header, message, label, function(reaction) {
        if (reaction == 'Yes') {
          $().docoForm("click", {
              url: `/${modul}${url}/recalculate-ews`,
              method: "GET",
              data: {
                pendaftaran_id: pendaftaranId,
                jenis_ews: lastJenisEws,
                new_jenis_ews: selectedValue,
              },
              skipConfirm: true,
              success: function (data) {
                setTimeout(() => {
                  fetchDataEws();
                }, 200);

                localStorage.setItem("last-jenis-ews", selectedValue);
              }
          });
        } else {
          $('.selectJenisEws').val(lastJenisEws).trigger('change');
        }
      });
    }
  });

  $('#input-ews').unbind('click')
  $('#input-ews').on('click', function(e) {
    e.preventDefault();
    var _jenisEwsId = $('.selectJenisEws').val()
    var _jenisEws = $('.selectJenisEws').find(":selected").text().toLowerCase();
    if(!_jenisEwsId) {
        docoNotification('warning', 'Proses Gagal!', 'Lakukan Pemilihan EWS Terlebih Dahulu.')
        $('#error-jenis-ews').removeClass('hidden')
        return false
    }

    var _action = $('#input-ews').attr('action')
    var url = new URL(_action, window.location.origin);
    url.searchParams.set('jenisews_id', _jenisEwsId);
    url.searchParams.set('jenisews', _jenisEws);
    $('#input-ews').attr('action', url.toString())
    return true
  })
  
  $(document).on('click', '#btn-panduan', ({ currentTarget }) => {
      const dataBtn = $(currentTarget).data()
      if (typeof dataBtn.url != 'undefined' && dataBtn.url != null && dataBtn != '') {
          $('#modal-preview').data('url', dataBtn.url)
          $('#modal-preview').modal({
              backdrop: 'static',
              keyboard: false
          })
      }
  })

  $("#modal-preview").on("shown.bs.modal", function () {
    $("#preview-content").attr("src", $("#modal-preview").data("url"));
  });

  $('#load-more').on('click', function() {
    fetchDataEws();
  })

  $('#scroll-load').on('click', function() {
    fetchDataEws(true);
  })

  $('.startDate').val('');
  $('.endDate').val('');
  $('.targetDate').val('');

  setTimeout(() => {
    fetchDataEws(true);
  }, 100);

  // Add debouncing to prevent multiple calls
  let fetchDataEwsTimeout;
  let isFetchingData = false;

  function fetchDataEws(isScroll = false) {
    // Prevent multiple simultaneous calls
    if (isFetchingData) {
      return false;
    }

    // Clear any existing timeout
    if (fetchDataEwsTimeout) {
      clearTimeout(fetchDataEwsTimeout);
    }
    
    // Debounce the function call
    fetchDataEwsTimeout = setTimeout(function() {
      fetchDataEwsInternal(isScroll);
    }, 100);
  }

  function fetchDataEwsInternal(isScroll = false) {
    if (isScroll) {
      let maximumLimit = parseInt(localStorage.getItem("max-limit-observasi"))
      let lastRecords = parseInt(localStorage.getItem("total-records-observasi"))

      if (maximumLimit != null && lastRecords >= maximumLimit) {
          return false;
      }
    }

    let date = $('.targetDateEws').val();
    let jenisEwsId = $('.selectJenisEws').val();
    if (!jenisEwsId) {
      return false;
    }

    $('#error-jenis-ews').addClass('hidden');
    // Set flag to prevent multiple calls
    isFetchingData = true;

    $.ajax({
      type: "POST",
      url: `/${modul}${url}/get-data-observasi-ews`,
      data: {
        limit: limit,
        date: date,
        pendaftaran_id: pendaftaranId,
        jenisEwsId: jenisEwsId,
      },
      success: function (response) {
        if (response.totalData > response?.totalRecords && isScroll) {
          limit += 20;
        }

        if (response?.totalRecords == 0 && (!response?.lastEws || !response?.lastEws?.tanggal_ews)) {
          localStorage.setItem("last-jenis-ews", "");
        }

        generateComponent(response?.lastEws)
        localStorage.setItem("max-limit-observasi", response?.totalData);
        localStorage.setItem("total-records-observasi", response?.totalRecords);
        $("#content-table-observasi-ews").html(response?.data);
        // Reset flag after successful completion
        isFetchingData = false;
      },
    });
  }

  function generateComponent(data) {
    // Clear banner first
    $('#banner-ews').html('');
    
    if (data?.tanggal_ews == null) {
      return '';
    }

    var tanggalIndo = Intl.DateTimeFormat('id-ID', {
      day: '2-digit',
      month: 'long',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    }).format(new Date(data?.tanggal_ews));

    var kategori = data?.kategori
    var kategoriName = '';
    var banner = '';
    if (kategori == 1) {
      kategoriName = 'Resiko Rendah';
      banner = 'resiko-rendah';
    } else if (kategori == 2) {
      kategoriName = 'Resiko Sedang';
      banner = 'resiko-sedang';
    } else if (kategori == 3) {
      kategoriName = 'Resiko Tinggi';
      banner = 'resiko-tinggi';
    } else if (kategori == 4) {
      kategoriName = 'Resiko Sangat Tinggi';
      banner = 'resiko-sangat-tinggi';
    }

    if (kategori != null) {
      var component = `                
          <div class="alert ${banner} text-left">
              <strong style="font-size: 14px;">EWS Terakhir: ${data?.total_skor} (${kategoriName})</strong><br>
              <span style="font-size: 12px; margin-right: 10px; margin-top: 10px;">
                  Tanggal/Waktu: ${tanggalIndo}
              </span>
              <span>
                  | Input : ${data?.pegawai_nama}
              </span>
          </div>`;

      $('#banner-ews').html(component);
    }
  }

  // Function to refresh data after delete operation
  function refreshAfterDelete() {
    // Reset all pagination and cache data
    limit = 20;
    localStorage.removeItem("max-limit-observasi");
    localStorage.removeItem("total-records-observasi");
    
    // Force refresh the data
    fetchDataEws();
  }
  
  // Expose refreshAfterDelete function globally so it can be called from form.js
  window.refreshAfterDelete = refreshAfterDelete;
  
  // Expose refreshEwsData function globally for form.js to use
  window.refreshEwsData = function() {
    // Reset all pagination and cache data
    limit = 20;
    localStorage.removeItem("max-limit-observasi");
    localStorage.removeItem("total-records-observasi");

    // Force refresh the data
    fetchDataEws();
  }; 
 
  dateRangeHelper(".startDateEws",".endDateEws", ".targetDateEws");      
});
