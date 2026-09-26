var table;
$(() => {
   moment.locale("en");
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
         url: "/kasir/lap-rekap-jasa-dokter/get-data",
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
            title: "Nama Pasien",
            data: "nama_pasien",
            searchable: false,
            render: (data, rowElement, rowData) => {
               return `${rowData.no_rekam_medik != null ? rowData.no_rekam_medik : ''} / ${rowData.nama_pasien != null ? rowData.nama_pasien : ''}`
            },
         },
         {
            title: "NO REG",
            data: "no_pendaftaran",
            searchable: false,
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            title: "Total Biaya Dokter",
            data: "total_jasadokter",
            searchable: false,
            orderable: false,
            className: "text-right",
            render: $.fn.dataTable.render.number(".", ",", 0, "")
         },
         {
            title: "Tanggal (Masuk - Keluar)",
            data: "tgl_masuk",
            searchable: false,
            render: (data, rowElement, rowData) => {
               return `${rowData.tgl_masuk != null ? moment(rowData.tgl_masuk).format("DD MMM YYYY") : '-'} / ${rowData.tgl_keluar != null ? moment(rowData.tgl_keluar).format("DD MMM YYYY") : '-'}`
            },
         },
         {
            title: "Nama Dokter",
            data: "nama_pegawai",
            searchable: false,
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            title: "Instalasi",
            data: "instalasi_nama",
            searchable: false,
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            title: "Cara Bayar",
            data: "group_carabayar",
            searchable: false,
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
      ],
      formFilters: [
         {
            fieldName: 'tgl_masuk',
            label: 'Periode Tanggal',
            type: {
               name: 'rangeDate',
            }
         },
         {
            fieldName: 'dokterpenanggungjawab_id',
            label: 'Nama Dokter',
            type: {
               name: 'dropdownScroll',
               url: "/kasir/lap-rekap-jasa-dokter/filters",
            }
         },
      ],
   });
   $(document).on("click", ".btn-reset", function (e) {
      const tableId = "example";
      const element = $(`#filter-section__${tableId}`)
      const formWrapper = $(`#form-filter__${tableId}`)
      element.find('input').val('')
      element.find('select').val(null).trigger('change')
      element.find('.startDate').val(moment().format("DD-MMM-YYYY")).trigger("change");
      element.find('.endDate').val(moment().format("DD-MMM-YYYY")).trigger("change");
      const tableElement = $(`#${tableId}`).DataTable()
      showLoader()
      tableElement.context[0].ajax.data.advancedFilter = serializeArrayToJson(formWrapper)
      tableElement.ajax.url("/kasir/lap-rekap-jasa-dokter/get-data?type=0").load()
      $(".keterangan_dokter").css("display", "none");
      $(".keterangan_payer").css("display", "none");
      $('#info_periode').html(`<strong>${moment().format("DD MMMM YYYY")} s/d ${moment().format("DD MMMM YYYY")}</strong>`);
   });
   const arrFilterPayer = [
      {
         class: "btn-rajal-umum",
         type: 1,
      },
      {
         class: "btn-rajal-penjamin",
         type: 2,
      },
      {
         class: "btn-igd-umum",
         type: 3,
      },
      {
         class: "btn-igd-penjamin",
         type: 4,
      },
      {
         class: "btn-ranap-umum",
         type: 5,
      },
      {
         class: "btn-ranap-penjamin",
         type: 6,
      },
      {
         class: "btn-all-umum",
         type: 7,
      },
      {
         class: "btn-all-penjamin",
         type: 8,
      },
   ];
   $.each(arrFilterPayer, function (index, value) {
      $(document).on("click", "." + value.class, function (e) {
         e.preventDefault();
         filterByPayer(value.type)
      });
   });
   function filterByPayer(type) {
      var _info = '';
      const tableId = "example";
      const tableElement = $(`#${tableId}`).DataTable()
      showLoader();
      tableElement.ajax.url("/kasir/lap-rekap-jasa-dokter/get-data?type=" + type).load()
      switch (type) {
         case 1:
            _info = `<strong>Pasien Rajal Pribadi</strong>`;
            break;

         case 2:
            _info = `<strong>Pasien Rajal Penjamin</strong>`;
            break;

         case 3:
            _info = `<strong>Pasien IGD Pribadi</strong>`;
            break;

         case 4:
            _info = `<strong>Pasien IGD Penjamin</strong>`;
            break;

         case 5:
            _info = `<strong>Pasien Ranap Pribadi</strong>`;
            break;

         case 6:
            _info = `<strong>Pasien Ranap Penjamin</strong>`;
            break;
         case 7:
            _info = `<strong>Semua Pasien Pribadi</strong>`;
            break;

         case 8:
            _info = `<strong>Semua Pasien Penjamin</strong>`;
            break;
      }
      $('#info_label_payer').html(_info);
   }
   $(document).on("click", "#example tbody tr", function () {
      try {
         primaryKey = table.row('.selected').data().primary ? table.row('.selected').data().primary : null;
      } catch (e) {
         primaryKey = false
      }
      if (primaryKey) {
         $("#btn-detail").prop("disabled", false);
      }
   });
   $('.flex-1').css('display', 'none');
   if ($(this).find('row row__hidden')) {
      $("#filter-section__example .row__hidden").removeClass("row__hidden");
   }
   $('#info_title').html(`<strong>DATA BIAYA PEMERIKSAAN DOKTER</strong>`)
   $('#info_periode').html(`<strong>${moment().format('DD MMMM YYYY')} s/d ${moment().format('DD MMMM YYYY')}</strong>`);
   $('.btn-search--datatable').on('click', function () {
      var startDate = moment($('.startDate').val()).format('DD MMMM YYYY');
      var endDate = moment($('.endDate').val()).format('DD MMMM YYYY');
      var _elementDokter = $('#example-dokterpenanggungjawab_id--form');
      if (_elementDokter.val() != '') {
         var filter_dokter = _elementDokter.select2('data');
         if (filter_dokter != '') {
            var _dokter = filter_dokter[0].text;
            $('#info_label_dokter').html(`<strong>Nama Dokter : ${_dokter} <strong>`);
         }
      }
      $(".keterangan_dokter").css("display", "block");
      $('#info_periode').html(`<strong>${startDate} s/d ${endDate}</strong>`);
   });
});