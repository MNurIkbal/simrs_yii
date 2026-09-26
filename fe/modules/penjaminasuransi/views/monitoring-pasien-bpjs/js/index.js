var table;
var _api = '/penjamin-asuransi/monitoring-pasien-bpjs/';
var listStatus = [
   {
      'id': 0,
      'text': 'Belum di Monitor'
   },
   {
      'id': 1,
      'text': 'Sudah di Monitor'
   }
];
$(document).ready(function () {
   table = $("#example").docoTabel({
      filter: false,
      columnDefs: [
         {
            orderable: false,
            className: "select-checkbox",
            targets: 0,
         }
      ],
      select: {
         style: "os",
         selector: "tr"
      },
      sorting: [[2, "desc"]],
      displayLength: 10,
      processing: true,
      serverSide: true,
      scrollX: true,
      scrollY: true,
      ajax: {
         url: _api + "get-data",
      },
      columns: [
         {
            data: null,
            searchable: false,
            orderable: false,
            defaultContent: "",
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
            data: "tgl_pendaftaran",
            render: (data) => {
               return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY")
            }
         },
         {
            data: "tglpasienpulang",
            render: (data) => {
               return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY")
            }
         },
         {
            data: "nama_pasien",
            orderable: false,
            render: (data, rowElement, rowData) => {
               return `
                  <p style="margin-bottom: 2px"> <b>${rowData.nama_pasien != null ? rowData.nama_pasien : ''} </b></p>
                  <p style="margin-bottom: 2px"> Tanggal Lahir : ${rowData.tanggal_lahir != null ? moment(rowData.tanggal_lahir).format("DD MMM YYYY") : '-'} </p>
                  <p style="margin-bottom: 2px"> No Pendaftaran : ${rowData.no_pendaftaran != null ? rowData.no_pendaftaran : '-'}</p>
                  <p style="margin-bottom: 2px"> No Rekam Medik : ${rowData.no_rekam_medik != null ? rowData.no_rekam_medik : '-'}</p>
               `
            }
         },
         {
            data: "nosep",
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            data: "penjamin_nama",
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            data: "ruangan_nama",
            orderable: false,
            render: (data, rowElement, rowData) => {
               return `
                  <p style="margin-bottom: 2px"> Ruangan : ${rowData.ruangan_nama != null ? rowData.ruangan_nama : ''} </p>
                  <p style="margin-bottom: 2px"> Kamar/Bed : ${rowData.kamarruangan_nokamar != null ?rowData.kamarruangan_nokamar : '-'} / ${rowData.no_tempattidur != null ?rowData.no_tempattidur : '-'}</p>
                  <p style="margin-bottom: 2px"> Hak Kelas : ${rowData.hak_kelas != null ?rowData.hak_kelas : '-'} </p>
               `
            }
         },
         {
            data: "dokter_dpjp",
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            data: "set_diagnosautama",
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            data: "set_diagnosapenyerta",
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            data: "set_diagnosatindakan",
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            data: "tagihan_rs",
            className : "text-right",
            render: $.fn.dataTable.render.number(".", ",", 0, "")
         },
         {
            data: "tarif_inacbg",
            className : "text-right",
            render: $.fn.dataTable.render.number(".", ",", 0, "")
         },
         {
            data: "persentase",
            className : "text-right",
            render: $.fn.dataTable.render.number(".", ",", 0, "")
         },
         {
            data: "status_monitor",
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            data: "status_periksa",
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
      ],
      createdRow: function(row, data, index) {
         var tagihan_rs = data["tagihan_rs"];
         var tarif_inacbg = data["tarif_inacbg"];
         var limit75 = (75/100) * tarif_inacbg;
         var status_monitor = data["status_monitor_id"];
         if(status_monitor == 0) {
            $( row ).css( "background-color", "#FDFEFE" );
         }
         else if(parseInt(tagihan_rs) > parseInt(tarif_inacbg)) {
            $( row ).css( "background-color", "#B94747" );
            $(row).find("td:eq(0)").css("color", "white");
            $(row).find("td:eq(1)").css("color", "white");
            $(row).find("td:eq(2)").css("color", "white");
            $(row).find("td:eq(3)").css("color", "white");
            $(row).find("td:eq(4)").css("color", "white");
            $(row).find("td:eq(5)").css("color", "white");
            $(row).find("td:eq(6)").css("color", "white");
            $(row).find("td:eq(7)").css("color", "white");
            $(row).find("td:eq(8)").css("color", "white");
            $(row).find("td:eq(9)").css("color", "white");
            $(row).find("td:eq(10)").css("color", "white");
            $(row).find("td:eq(11)").css("color", "white");
            $(row).find("td:eq(12)").css("color", "white");
            $(row).find("td:eq(13)").css("color", "white");
            $(row).find("td:eq(14)").css("color", "white");
            $(row).find("td:eq(15)").css("color", "white");
            $(row).find("td:eq(16)").css("color", "white");
         }
         else if(parseInt(tagihan_rs) <= parseInt(limit75)) {
            $( row ).css( "background-color", "#829232" );
            $(row).find("td:eq(0)").css("color", "white");
            $(row).find("td:eq(1)").css("color", "white");
            $(row).find("td:eq(2)").css("color", "white");
            $(row).find("td:eq(3)").css("color", "white");
            $(row).find("td:eq(4)").css("color", "white");
            $(row).find("td:eq(5)").css("color", "white");
            $(row).find("td:eq(6)").css("color", "white");
            $(row).find("td:eq(7)").css("color", "white");
            $(row).find("td:eq(8)").css("color", "white");
            $(row).find("td:eq(9)").css("color", "white");
            $(row).find("td:eq(10)").css("color", "white");
            $(row).find("td:eq(11)").css("color", "white");
            $(row).find("td:eq(12)").css("color", "white");
            $(row).find("td:eq(13)").css("color", "white");
            $(row).find("td:eq(14)").css("color", "white");
            $(row).find("td:eq(15)").css("color", "white");
            $(row).find("td:eq(16)").css("color", "white");
         }
         else if(parseInt(tagihan_rs) >= (parseInt(limit75) && parseInt(tagihan_rs) < parseInt(tarif_inacbg) )) {
            $( row ).css( "background-color", "#DD972C" );
            $(row).find("td:eq(0)").css("color", "white");
            $(row).find("td:eq(1)").css("color", "white");
            $(row).find("td:eq(2)").css("color", "white");
            $(row).find("td:eq(3)").css("color", "white");
            $(row).find("td:eq(4)").css("color", "white");
            $(row).find("td:eq(5)").css("color", "white");
            $(row).find("td:eq(6)").css("color", "white");
            $(row).find("td:eq(7)").css("color", "white");
            $(row).find("td:eq(8)").css("color", "white");
            $(row).find("td:eq(9)").css("color", "white");
            $(row).find("td:eq(10)").css("color", "white");
            $(row).find("td:eq(11)").css("color", "white");
            $(row).find("td:eq(12)").css("color", "white");
            $(row).find("td:eq(13)").css("color", "white");
            $(row).find("td:eq(14)").css("color", "white");
            $(row).find("td:eq(15)").css("color", "white");
            $(row).find("td:eq(16)").css("color", "white");
         }
         if(data["is_stopakomodasi"]){
           $(row).find("td:eq(4)").css("background-color", "#57bed6"); 
         }
      },
      formFilters: [
         {
            fieldName: 'tgl_pendaftaran',
            label: 'Tanggal Masuk',
            type: {
               name: 'rangeDate',
            }
         },
         'nama_pasien',
         'no_rekam_medik',
         'no_pendaftaran',
         {
            fieldName: 'nosep',
            label: 'No. SEP'
         },
         {
            fieldName: 'pegawai_id',
            label: 'Dokter Penanggung Jawab',
            type: {
               name: 'dropdownScroll',
               url: _api + "filters",
               additionalPayload: {
                  type: 'dokter_dpjp',
               }
            }
         },
         {
            fieldName: 'penjamin_id',
            label: 'Penjamin',
            type: {
               name: 'dropdownScroll',
               url: _api + "filters",
               additionalPayload: {
                  type: 'penjamin_id',
               }
            }
         },
         {
            fieldName: 'ruangan_id',
            label: 'Ruangan',
            type: {
               name: 'dropdownScroll',
               url: _api + "filters",
               additionalPayload: {
                  type: 'ruangan_id',
               }
            }
         },
         {
            fieldName: 'kamarruangan_id',
            label: 'Kamar/Bed',
            type: {
               name: 'dropdownScroll',
               url: _api + "filters",
               additionalPayload: {
                  type: 'kamar',
               }
            }
         },
         {
            fieldName: 'status_monitor_id',
            label: 'Status',
            type: {
               name: 'select',
               payload: listStatus
            }
         },
         {
            fieldName: 'status_periksa',
            label: 'Status Periksa',
            type: {
               name: 'select',
               payload: listStatusPeriksa
            }
         },
      ],
   });
   $('#btn-search__example').css('display', 'none');
   $('#btn-reset__example').css('display', 'none');
})

$(document).on("click", "#example tbody tr", function (event) {
   event.preventDefault();
   var datas = table.row(".selected").data();
   var diagnosa_utama = datas.diagnosa_utama;
   var status_monitor = datas.status_monitor_id;

   if (diagnosa_utama == "") {
       $(".monitor").prop("disabled",true);
   } else {
       $(".monitor").prop("disabled",false);
   }

   if (status_monitor == 0) {
       $(".monitor").prop("disabled",true);
   } else {
       $(".monitor").prop("disabled",false);
   }
});

$(document).on("click", ".btn-reset", function (e) {
   const tableId = "example";
   const element = $(`#filter-section__${tableId}`)
   const formWrapper = $(`#form-filter__${tableId}`)
   element.find('input').val('')
   element.find('select').val(null).trigger('change')
   element.find('#tgl_pendaftaran-startDate').val(moment().format("DD-MMM-YYYY")).trigger("change");
   element.find('#tgl_pendaftaran-endDate').val(moment().format("DD-MMM-YYYY")).trigger("change");
   const tableElement = $(`#${tableId}`).DataTable()
   showLoader()
   tableElement.context[0].ajax.data.advancedFilter = serializeArrayToJson(formWrapper)
   tableElement.ajax.url(_api + "get-data").load()
});