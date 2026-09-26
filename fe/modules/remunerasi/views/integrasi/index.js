var table;
var listData = [];
var _api = '/remunerasi/integrasi/';

$(document).ready(function () {
   moment.locale('id');
   var _currentMonth = String(moment().month() + 1).padStart(2, '0');
   var _currentYear = moment().year();

   // $("#excel-bgprocess").attr("disabled", true);
   $("#btn-delete").attr("disabled", true);
   $("#calc-bgprocess").attr("disabled", true);

   table = $("#example").docoTabel({
      filter: false,
      columnDefs: [
         {
            targets: 0,
            width: 20,
            orderable: false,
            className: 'select-checkbox',
            render: () => {
               $('#checkBox').prop("checked", false);
               // $("#excel-bgprocess").attr("disabled", true);
               $("#btn-delete").attr("disabled", true);
               $("#calc-bgprocess").attr("disabled", true);
            }
         },
      ],
      select: {
         style: "multi",
         selector: 'tr'
      },
      sorting: [[1, "asc"]],
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
            data: "nama_pegawai",
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            data: "nik",
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            data: "jabatan_nama",
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            data: "periode_bulan",
            render: (data, rowElement, rowData) => {
               return `
                  <p style="margin-bottom: 2px"> ${rowData.periode_bulan != null ? moment(rowData.periode_bulan).format("MMMM") : '-'} 
                  ${rowData.periode_tahun != null ? moment(rowData.periode_tahun).format("YYYY") : '-'}</p>
               `
            }
         },
         {
            data: "imbal_jasa",
            orderable: false,
            class: "text-right",
            render: $.fn.dataTable.render.number(".", ",", 0, "")
         },
      ],
      formFilters: [
         {
            fieldName: 'periode_remun',
            label: 'Periode Remunerasi'
         },
         'nama_pegawai',
         {
            fieldName: 'nik',
            label: 'NIK'
         },
         {
            fieldName: 'jabatan_nama',
            label: 'Jabatan'
         },
      ],
   });

   $('#btn-search__example').css('display', 'none');
   $('#btn-reset__example').css('display', 'none');

   $('#example-periode_remun--form').attr('lang', 'id');
   $('#example-periode_remun--form').attr('value', _currentYear+'-'+_currentMonth);
   $('#example-periode_remun--form').attr('type', 'month');
   $('#example-periode_remun--form').attr('min', '2015-01');
   $('#example-periode_remun--form').attr('max', _currentYear+'-'+_currentMonth);
});

$('#checkBox').on('click', function () {
   if ($('#checkBox').is(':checked')) {
      table.rows().select();
   } else {
      table.rows().deselect();
   }

   checkStatus();
});

function checkStatus() {
   var _listData = [];

   table.rows(".selected").data().each(function (val) {
      if (typeof val.remunpegawai_id != "undefined") {
         _listData.push(val.remunpegawai_id)
      }
   });

   var periode = $("#example-periode_remun--form").val();
   var nama_pegawai = $("#example-nama_pegawai--form").val();
   var nik = $("#example-nik--form").val();
   var jabatan_nama = $("#example-jabatan_nama--form").val();
   
   $("#excel-bgprocess").attr('data-url', _api + 'show-popup?ids=')
   $("#calc-bgprocess").attr('data-url', _api + 'show-popup-calc?ids=')
   $("#btn-delete").attr(
      'data-target', _api + 'delete?periode_remun=' + periode 
      + '&nama_pegawai=' + nama_pegawai
      + '&nik=' + nik
      + '&jabatan_nama=' + jabatan_nama + '&'
   )

   if(_listData.length) {
      // $("#excel-bgprocess").attr("disabled", false);
      $("#btn-delete").attr("disabled", false);
      $("#calc-bgprocess").attr("disabled", false);
   } else {
      // $("#excel-bgprocess").attr("disabled", true);
      $("#btn-delete").attr("disabled", true);
      $("#calc-bgprocess").attr("disabled", true);
   }
}

$(document).on("click", "#example tbody", function () {
   if (table.row(".selected").length) {
      var _rowData = table.rows('.selected').data();
      var _listData = [];

      for (var i = 0; i < _rowData.length; i++) {
         if(typeof _rowData[i].remunpegawai_id != "undefined") {
            _listData.push(_rowData[i].remunpegawai_id)
         }
      }

      _listData = JSON.stringify(_listData)

      $("#excel-bgprocess").attr('data-url', _api + 'show-popup?ids=' + _listData + '&')
      $("#calc-bgprocess").attr('data-url', _api + 'show-popup-calc?ids=' + _listData + '&')
      $("#btn-delete").attr('data-target', _api + 'delete?ids=' + _listData + '&')

      // $("#excel-bgprocess").attr("disabled", false);
      $("#btn-delete").attr("disabled", false);
      $("#calc-bgprocess").attr("disabled", false);
   }
   else {
      // $("#excel-bgprocess").attr("disabled", true);
      $("#btn-delete").attr("disabled", true);
      $("#calc-bgprocess").attr("disabled", true);
   }
});

$(document).on("click", ".btn-reset", function (e) {
   moment.locale('id')
   var _currentMonth = String(moment().month() + 1).padStart(2, '0')
   var _currentYear = moment().year()
   const tableId = "example";
   const element = $(`#filter-section__${tableId}`)
   const formWrapper = $(`#form-filter__${tableId}`)
   element.find('input').val('')
   element.find('select').val(null).trigger('change')
   $('#example-periode_remun--form').val(_currentYear+'-'+_currentMonth).trigger('change')
   const tableElement = $(`#${tableId}`).DataTable()
   showLoader()
   tableElement.context[0].ajax.data.advancedFilter = serializeArrayToJson(formWrapper)
   tableElement.ajax.url(_api + "get-data").load()
});

$(document).on("click", "#btn-sync", function (event) {
   // TODOS:
   event.preventDefault();

   const periode = $("#example-periode_remun--form").val()
   if (periode == "" || periode == undefined) {
      docoNotification('error', 'Proses Gagal!', "Tanggal tidak valid")
      return false;
   }
   
   var header = "Perhatian!";
   var message = "Apakah anda yakin untuk melakukan sinkronisasi?";
   var label = {
       buttons: {
           "Yes": "button-yes",
           "No": "button-no"
       },
   };

   $(".modal-content").empty()
   
   $.showQuestionDialog(header, message, label, function (reaction) {
       if (reaction == "Yes") {
           hideQuestionDialog();
           $.ajax({
               url : '/remunerasi/integrasi/show-popup-sync?id=',
               success : function (data) {
                   var randString = data
                   if(randString !== null || randString != '') {
                       $('#modal_sinkron').modal('toggle');
                       $('.modal-content').append(data)
                   }
               }
           });
       } else {
           hideQuestionDialog();
       }
   });
})
