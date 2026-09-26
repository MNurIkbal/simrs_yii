var table;
$(() => {
   if (flashMessage != '') {
      docoNotification('warning', 'Perhatian', flashMessage);
   }
   $('.btn-proses').attr('disabled', true)
   $('.btn-batal').attr('disabled', true)
   $('.data-detail').attr('disabled', true)
   $('#cetak-laporan').attr('disabled', true)
   table = $("#example").docoTabel({
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
      sorting: [[2, "desc"]],
      displayLength: 10,
      processing: true,
      serverSide: true,
      scrollX: true,
      scrollY: false,
      ajax: {
         url: "/bedah/informasi-pasien-operasi/get-data",
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
               var tableInfo = table.page.info()
               return tableInfo.start + rowAdditionalData.row + 1
            }
         },
         {
            title: "Tanggal Rujukan",
            data: "tgl_rujukan",
            render: (data) => {
               return data == "" || data == null ? "-" : moment(data).format("DD-MMM-YYYY")
            }
         },
         {
            title: "Detail Diagnosa",
            data: "diagnosa",
            searchable: false,
            orderable: false,
         },
         {
            title: "Nomor operasi",
            data: "no_masukpenunjang",
            searchable: false,
         },
         {
            title: "Tanggal Operasi",
            data: "tgl_operasi",
            render: (data) => {
               return data == "" || data == null ? "-" : moment(data).format("DD-MMM-YYYY H:mm:ss")
            }
         },
         {
            title: "No Pendaftaran / No Rekam Medik",
            searchable: false,
            render: (data, rowElement, rowData) => {
               return `${rowData.no_pendaftaran != null ? rowData.no_pendaftaran : ''} / ${rowData.no_rekam_medik != null ? rowData.no_rekam_medik : ''}`
            }
         },
         {
            title: "Nama Pasien",
            data: "nama_pasien",
         },
         {
            title: "Instalasi - Ruangan Perujuk",
            searchable: false,
            render: (data, rowElement, rowData) => {
               return `${rowData.asalrujukan_nama != null ? rowData.asalrujukan_nama : ''} / ${rowData.ruangan_nama != null ? rowData.ruangan_nama : ''}`
            }
         },
         {
            title: "Cara Bayar / Penjamin",
            searchable: false,
            render: (data, rowElement, rowData) => {
               return `${rowData.carabayar_nama != null ? rowData.carabayar_nama : ''} / ${rowData.penjamin_nama != null ? rowData.penjamin_nama : ''}`
            }
         },
         {
            title: "Status",
            data: "status",
         },
      ],
      formFilters: [
         {
            fieldName: 'tgl_rujukan',
            label: 'Tanggal Rujukan',
            type: {
               name: 'rangeDate',
            }
         },
         {
            fieldName: 'tgl_operasi',
            label: 'Tanggal Operasi',
            type: {
               name: 'rangeDate',
               payload: {
                  allDate: true
               }
            }
         },
         'no_pendaftaran',
         'no_rekam_medik',
         'nama_pasien',
         {
            fieldName: 'instalasiasal_id',
            label: 'Instalasi Perujuk',
            type: {
               name: 'dropdownScroll',
               url: "/bedah/informasi-pasien-operasi/filters",
               additionalPayload: {
                  type: 'instalasi'
               }
            }
         },
         {
            fieldName: 'ruanganasal_id',
            label: 'Ruangan Perujuk',
            type: {
               name: 'dropdownScroll',
               url: "/bedah/informasi-pasien-operasi/filters",
               additionalPayload: {
                  type: 'ruangan'
               }
            }
         },
         {
            fieldName: 'status_periksa',
            label: 'Status',
            type: {
               name: 'select',
               payload: dropdownData.status_operasi
            }
         },
      ],
      filterRendered: (wrapper) => {
         $(wrapper).find('[name="instalasiasal_id"]').bind('change', ({ currentTarget }) => {
            if ($(currentTarget).val() == '' || $(currentTarget).val() == null) {
               var data = [
                  {
                     id: '',
                     text: '- Semua -'
                  }
               ]
               $(wrapper).find('[name="ruanganasal_id"]').html('')
               $(wrapper).find('[name="ruanganasal_id"]').select2({
                  data,
               })
            }
            else {
               $(wrapper).find('[name="ruanganasal_id"]').select2InfinityScroll({
                  url: '/bedah/informasi-pasien-operasi/filters',
                  callbackData: (params) => {
                     return {
                        term: params.term,
                        page: params.page || 1,
                        limit: params.limit,
                        type: 'ruangan',
                        additionalPayload: {
                           instalasi_id: $(currentTarget).val(),
                        }
                     }
                  }
               });
            }
         });
      },
      rowCallback: (rowElement, data) => {
         if (data.carabayar_kode_warna != null) {
            $($(rowElement).find('td')[8]).css('background-color', data.carabayar_kode_warna);
            $($(rowElement).find('td')[8]).css('color', invertColor(data.carabayar_kode_warna, true));
         }
      }
   });

   $(document).on("click", "#example tbody tr", function () {
      try {
         primaryKey = table.row('.selected').data().primary ? table.row('.selected').data().primary : null;
         status_periksa = table.row('.selected').data().status_periksa ? table.row('.selected').data().status_periksa : null;
      } catch (e) {
         primaryKey = false
         status_periksa = false
      }
      if (status_periksa == 488) {
         $('.btn-batal').attr('disabled', false)
         $('.btn-proses').attr('disabled', false)
         $('.data-detail').attr('disabled', true)
         $('#cetak-laporan').attr('disabled', true)
      } else if (status_periksa == 482) {
         $('.btn-batal').attr('disabled', true)
         $('.btn-proses').attr('disabled', false)
         $('.data-detail').attr('disabled', true)
         $('#cetak-laporan').attr('disabled', true)
      } else if (status_periksa == 483) {
         $('.btn-batal').attr('disabled', true)
         $('.btn-proses').attr('disabled', true)
         $('.data-detail').attr('disabled', false)
         $('#cetak-laporan').attr('disabled', false)
      } else if (status_periksa != false) {
         $('.btn-batal').attr('disabled', true)
         $('.btn-proses').attr('disabled', true)
         $('.data-detail').attr('disabled', true)
         $('#cetak-laporan').attr('disabled', true)
      } else {
         $('.btn-batal').attr('disabled', true)
         $('.btn-proses').attr('disabled', true)
         $('.data-detail').attr('disabled', true)
         $('#cetak-laporan').attr('disabled', true)
      }
   })
   $("#cetak-laporan").click(function (e) {
      e.preventDefault();
      var pasienmasukpenunjang_id = null;
      var tableData = table.row(".selected").data();
      if (typeof tableData !== "undefined") {
         pasienmasukpenunjang_id = tableData.pasienmasukpenunjang_id;
         var url = "/bedah/informasi-pasien-operasi/cetak?pasienmasukpenunjang_id=" + pasienmasukpenunjang_id;
         $(this).attr("data-target", url);
      }
   });
   $(document).on("click", ".btn-reset", function (e) {
      const tableId = "example";
      const element = $(`#filter-section__${tableId}`)
      const formWrapper = $(`#form-filter__${tableId}`)
      element.find('input').val('')
      element.find('select').val(null).trigger('change')
      element.find('#tgl_rujukan-startDate').val(moment().format("DD-MMM-YYYY")).trigger("change");
      element.find('#tgl_rujukan-endDate').val(moment().format("DD-MMM-YYYY")).trigger("change");
      const tableElement = $(`#${tableId}`).DataTable()
      showLoader()
      tableElement.context[0].ajax.data.advancedFilter = serializeArrayToJson(formWrapper)
      tableElement.ajax.reload()
   });
   $('.flex-1').css('display', 'none');
   if ($(this).find('row row__hidden')) {
      $("#filter-section__example .row__hidden").removeClass("row__hidden");
   }
});