$(document).ready(function () {
   $("#cetak-tagihan").prop("disabled", true);
   $("#btn-ubah").prop("disabled", true);
   $("#btn-hasil").prop("disabled", true);
   $("#btn-cetak").prop("disabled", true);

   var dataStatusBayar = [];
   var dataAsalRujukan = [];
   var dataStatusPeriksa = [];

   $.each(data.status_bayar, function( index, value ) {
      dataStatusBayar.push({
          id: index,
          text: value,
      });
   });

   $.each(data.asal_rujukan, function( index, value ) {
      dataAsalRujukan.push({
          id: value.asalrujukan_nama,
          text: value.asalrujukan_nama,
      });
   });

   $.each(data.status_periksa, function( index, value ) {
      dataStatusPeriksa.push({
          id: index,
          text: value,
      });
   });

   tableRiwayat = $('#table-riwayat').docoTabel({
      info: false,
      filter: false,
      columnDefs: [{
         orderable: false,
         className: 'select-checkbox',
         targets: 0
      }],
      select: {
         style: 'os',
         selector: 'tr'
      },
      displayLength: 10,
      processing: true,
      serverSide: true,
      scrollY: true,
      scrollX: false,
      ajax: {
         url: baseController + "get-data-riwayat",
      },
      columns: [
         {
            data: null,
            searchable: false,
            orderable: false,
            defaultContent: '',
         },
         {
            data: null,
            searchable: false,
            orderable: false,
            render: (data, rowElement, rowData, rowAdditionalData) => {
               var tableInfo = tableRiwayat.page.info()
               return tableInfo.start + rowAdditionalData.row + 1
            }
         },
         {
            data: 'status_periksa_nama',
            orderable: false,
            searchable: false,
         },
         {
            data: 'tglmasukpenunjang',
            render: (data) => {
               return data == null ? '-' : moment(data).format('DD MMM YYYY')
           },
         },
         {
            data: 'nama_pasien',
            orderable: false,
            searchable: false,
            render: (data, rowElement, rowData) => {
               return `
                   <p> ${data != null ? data : ''} ${rowData.jenis_kelamin_kode != null ? "(" + rowData.jenis_kelamin_kode + ")" : '-'}</p>
                   <p> ${rowData.tanggal_lahir == null ? '-' : moment(rowData.tanggal_lahir).format('DD MMM YYYY')}</p>
                   <p> ${rowData.no_pendaftaran != null ? rowData.no_pendaftaran : ''} / ${rowData.no_rekam_medik != null ? rowData.no_rekam_medik : ''}</p>
               `
           }
         },
         {
            data: 'pemeriksaan',
            orderable: false,
            searchable: false,
         },
         {
            data: 'dokter_penunjang',
            orderable: false,
         },
         {
            searchable: false,
            orderable: false,
            render: (data, rowElement, rowData) => {
               return `${rowData.carabayar_nama != null ? rowData.carabayar_nama : ''} / ${rowData.penjamin_nama != null ? rowData.penjamin_nama : ''}`
            }
         },
         {
            data: 'status_bayar',
            searchable: false,
            orderable: false,
         },
         {
            data: 'asalrujukan_nama',
            searchable: false,
            orderable: false,
         },
         {
            data: 'no_masukpenunjang',
            searchable: false,
            orderable: false,
         },
      ],
      formFilters: [
         {
            fieldName: 'tglmasukpenunjang_riwayat',
            label: 'Tanggal Pendaftaran',
            type: {
               name: 'rangeDate',
            }
         },
         'no_pendaftaran',
         'no_rekam_medik',
         'nama_pasien',
         {
            fieldName: 'no_masukpenunjang',
            label: 'No Lab'
         },
         {
            fieldName: 'pegawai_id',
            label: 'Dokter',
            type: {
               name: 'dropdownScroll',
               url: baseController + "filters",
               additionalPayload: {
                  type: 'dokter',
               }
            }
         },

         {
            fieldName: 'carabayar_id',
            label: 'Cara Bayar',
            type: {
               name: 'dropdownScroll',
               url: baseController + "filters",
               additionalPayload: {
                  type: 'carabayar',
               }
            }
         },
         {
            fieldName: 'penjamin_id',
            label: 'Penjamin',
            type: {
               name: 'dropdownScroll',
               url: baseController + "filters",
               additionalPayload: {
                  type: 'penjamin',
               }
            }
         },
         {
            fieldName: 'is_status_bayar',
            label: 'Status Bayar',
            type: {
               name: 'select',
               payload: dataStatusBayar
            }
         },
         {
            fieldName: 'asalrujukan_nama',
            label: 'Asal Rujukan',
            type: {
               name: 'select',
               payload: dataAsalRujukan
            }
         },
      ],
      rowCallback: (rowElement, data) => {
         if (data.carabayar_kode_warna != null) {
            $($(rowElement).find('td')[7]).css('background-color', data.carabayar_kode_warna);
            $($(rowElement).find('td')[7]).css('color', invertColor(data.carabayar_kode_warna, true));
         }
      }
   });

   $(document).on("click", ".antrian", function (event) {
      const id_pendaftaran = $(this).attr("data-id");
      const no_antrian = $(this).attr("data-antrian");
      const dataPost = {
         no_antrian: no_antrian
      };
      $.ajax({
         url: baseController + "panggil-antrian",
         data: dataPost,
         type: "post",
         success: function (res) {
            if (typeof res.teks_panggil !== "undefined") {
               let text = res.teks_panggil;
               let player = $("#playerAudio");
               let arrayText = text.split(" ");
               arrayText.push("stop");
               arrayText = arrayText.filter(Boolean);
               let index = 0;
               player[0].defaultPlaybackRate = 1;
               player[0].src = window.location.origin + "/media/sounds/" + arrayText[index] + ".mp3";
               player[0].play();
               player[0].addEventListener("ended", function () {
                  index = index + 1;
                  if (index < arrayText.length) {
                     player[0].defaultPlaybackRate = index == arrayText.length - 3 ? 1.5 : 1.2;
                     if (arrayText[index] == "stop") {
                     } else {
                        player[0].src = window.location.origin + "/media/sounds/" + arrayText[index] + ".mp3";
                        player[0].play();
                     }
                  }
               });
            }
         }
      });
   });

   $('#btn-search__table-riwayat').css('display', 'none');
   $('#btn-reset__table-riwayat').css('display', 'none');
});

$(document).on("click", ".btn-reset-riwayat", function () {
   const tableId = "table-riwayat";
   const element = $(`#filter-section__${tableId}`)
   const formWrapper = $(`#form-filter__${tableId}`)
   element.find('input').val('')
   element.find('select').val(null).trigger('change')
   element.find('#tglmasukpenunjang_riwayat-startDate').val(moment().add(-1, 'months').format("DD-MMM-YYYY")).trigger("change");
   element.find('#tglmasukpenunjang_riwayat-endDate').val(moment().add(1, 'months').format("DD-MMM-YYYY")).trigger("change");
   const tableElement = $(`#${tableId}`).DataTable()
   showLoader()
   tableElement.context[0].ajax.data.advancedFilter = serializeArrayToJson(formWrapper)
   tableElement.ajax.url(baseController + "get-data-riwayat").load()

   $("#cetak-tagihan").prop("disabled", true);
   $("#btn-ubah").prop("disabled", true);
   $("#btn-hasil").prop("disabled", true);
   $("#btn-cetak").prop("disabled", true);
});

$(document).on("click", "#table-riwayat tbody tr", function () {
   try {
      primaryKey = tableRiwayat.row(".selected").data().primary ? tableRiwayat.row(".selected").data().primary : null;
      statusPeriksa = tableRiwayat.row(".selected").data().status_periksa ? tableRiwayat.row(".selected").data().status_periksa : null;
      caraBayar = tableRiwayat.row(".selected").data().carabayar_nama ? tableRiwayat.row(".selected").data().carabayar_nama : null;
      isBayar = tableRiwayat.row(".selected").data().is_status_bayar ? tableRiwayat.row(".selected").data().is_status_bayar : null;
      statusBayar = tableRiwayat.row(".selected").data().status_bayar ? tableRiwayat.row(".selected").data().status_bayar : null;
      pegawai_id = tableRiwayat.row(".selected").data().pegawai_id ? tableRiwayat.row(".selected").data().pegawai_id : null;
      pasienmasukpenunjang_id = tableRiwayat.row(".selected").data().pasienmasukpenunjang_id ? tableRiwayat.row(".selected").data().pasienmasukpenunjang_id : null;
      pendaftaran_id = tableRiwayat.row(".selected").data().pendaftaran_id ? tableRiwayat.row(".selected").data().pendaftaran_id : null;
   } catch (e) {
      primaryKey = false;
   }

   if ($('#table-riwayat tr.selected').length == 0) {
      $("#cetak-tagihan").prop("disabled", true);
      $("#btn-ubah").prop("disabled", true);
      $("#btn-hasil").prop("disabled", true);
      $("#btn-cetak").prop("disabled", true);
   } 
   else {
      $("#cetak-tagihan").prop("disabled", false);
      $("#btn-ubah").prop("disabled", false);
      $("#btn-hasil").prop("disabled", false);
      $("#btn-cetak").prop("disabled", false);

      if (statusPeriksa == 475) {
         $("#btn-ubah").prop("disabled", true);
      }
   }
});

$(".btn-search--datatable").on('click', function () {
   $("#cetak-tagihan").prop("disabled", true);
   $("#btn-ubah").prop("disabled", true);
   $("#btn-hasil").prop("disabled", true);
   $("#btn-cetak").prop("disabled", true);
});