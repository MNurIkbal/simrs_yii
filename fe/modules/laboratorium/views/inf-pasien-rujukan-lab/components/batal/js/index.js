$(document).ready(function () {
   moment.locale("en");
   tableBatal = $("#table-batal").docoTabel({
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
      sorting: [[3, "desc"]],
      displayLength: 10,
      processing: true,
      serverSide: true,
      scrollX: false,
      scrollY: true,
      ajax: {
         url: baseController + "get-data-batal?type=5",
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
               var tableInfo = tableBatal.page.info()
               return tableInfo.start + rowAdditionalData.row + 1
            }
         },
         {
            title: "Status Pembayaran",
            data: "status_bayar",
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }

         },
         {
            title: "Tanggal Rujukan",
            data: "tgl_rujukan_batal",
            render: (data) => {
               return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY")
            }
         },
         {
            title: "Pasien",
            data: "nama_pasien",
            searchable: false,
            orderable: false,
            render: (data, rowElement, rowData) => {
               return `
                   <p style="margin-bottom: 2px"> <b> ${rowData.nama_pasien != null ? rowData.nama_pasien : ''} (${rowData.jenis_kelamin_kode != null ? rowData.jenis_kelamin_kode : '-'})</b></p>
                   <p style="margin-bottom: 2px"> <b> ${rowData.tanggal_lahir != null ? moment(rowData.tanggal_lahir).format("DD MMM YYYY") : '-'} </b></p>
                   <p style="margin-bottom: 2px"> <b> ${rowData.no_pendaftaran != null ? rowData.no_pendaftaran : '-'} - ${rowData.no_rekam_medik != null ? rowData.no_rekam_medik : '-'}</b></p>
               `
            }
         },
         {
            title: "Pemeriksaan",
            data: "pemeriksaan",
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }

         },
         {
            title: "Dokter Perujuk",
            data: "dokter_perujuk",
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            title: "Cara Bayar / Penjamin",
            data: "carabayar_nama",
            searchable: false,
            render: (data, rowElement, rowData) => {
               return `${rowData.carabayar_nama != null ? rowData.carabayar_nama : ''} / ${rowData.penjamin_nama != null ? rowData.penjamin_nama : ''}`
            }
         },
         {
            title: "Nomor Rujukan",
            data: "no_rujukan",
            render: (data, rowElement, rowData) => {
               return `
                   <p style="margin-bottom: 2px"> ${rowData.no_rujukan != null ? rowData.no_rujukan : ''} </p>
                   <p style="margin-bottom: 2px"> ${rowData.ruanganasal_nama != null ? rowData.ruanganasal_nama : '-'} </p>
               `
            }
         },
      ],
      formFilters: [
         {
            fieldName: 'tgl_rujukan_batal',
            label: 'Tanggal Rujukan',
            type: {
               name: 'rangeDate',
            }
         },
         'no_pendaftaran',
         'no_rekam_medik',
         'nama_pasien',
         'no_rujukan',
         {
            fieldName: 'tanggal_lahir',
            label: 'Tanggal Lahir',
            type: {
               name: 'rangeDate',
               payload: {
                  allDate: true,
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
            fieldName: 'ruanganasal_id',
            label: 'Asal Rujukan',
            type: {
               name: 'dropdownScroll',
               url: baseController + "filters",
               additionalPayload: {
                  type: 'ruangan',
               }
            }
         },
         {
            fieldName: 'pegawai_id',
            label: 'Dokter Perujuk',
            type: {
               name: 'dropdownScroll',
               url: baseController + "filters",
               additionalPayload: {
                  type: 'dokter',
               }
            }
         },
      ],
      filterRendered: (wrapper) => {
         var defaultPlaceHolder = [
            {
               id: '',
               text: '- Semua -'
            }
         ]
         $(wrapper).find('[name="carabayar_id"]').bind('change', ({ currentTarget }) => {
            if ($(currentTarget).val() == '' || $(currentTarget).val() == null) {
               $(wrapper).find('[name="penjamin_id"]').html('')
               $(wrapper).find('[name="penjamin_id"]').select2({
                  defaultPlaceHolder,
               })
            }
            else {
               $(wrapper).find('[name="penjamin_id"]').select2InfinityScroll({
                  url: baseController + 'filters',
                  callbackData: (params) => {
                     return {
                        term: params.term,
                        page: params.page || 1,
                        limit: params.limit,
                        type: 'penjamin',
                        additionalPayload: {
                           carabayar_id: $(currentTarget).val(),
                        }
                     }
                  }
               });
            }
         });
      },
      rowCallback: (rowElement, data) => {
         if (data.carabayar_kode_warna != null) {
            $($(rowElement).find('td')[7]).css('background-color', data.carabayar_kode_warna);
            $($(rowElement).find('td')[7]).css('color', invertColor(data.carabayar_kode_warna, true));
         }
      }
   });

   $(document).on("click", ".btn-reset-batal", function (e) {
      const tableId = "table-batal";
      const element = $(`#filter-section__${tableId}`)
      const formWrapper = $(`#form-filter__${tableId}`)
      element.find('input').val('')
      element.find('select').val(null).trigger('change')
      element.find('#tgl_rujukan_batal-startDate').val(moment().add(-1, 'months').format("DD-MMM-YYYY")).trigger("change");
      element.find('#tgl_rujukan_batal-endDate').val(moment().add(1, 'months').format("DD-MMM-YYYY")).trigger("change");
      const tableElement = $(`#${tableId}`).DataTable()
      showLoader()
      tableElement.context[0].ajax.data.advancedFilter = serializeArrayToJson(formWrapper)
      tableElement.ajax.url(baseController + "get-data-batal?type=5").load()
   });

   $('#btn-search__table-batal').css('display', 'none');
   $('#btn-reset__table-batal').css('display', 'none');
});
