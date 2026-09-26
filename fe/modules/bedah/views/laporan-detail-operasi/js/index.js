var table;
var groupColumn = ['golongan_operasi', 'tindakan_operasi'];
$(() => {
   table = $("#example").docoTabel({
      filter: false,
      paging: false,
      order: [[0, "asc"],[1, "asc"]],
      processing: true,
      serverSide: true,
      scrollX: true,
      stateSave: false,
      ajax: {
         url: "/bedah/laporan-detail-operasi/get-data",
      },
      rowGroup: {
         startRender: null,
         endRender: function (rows, group) {
            var intVal = function ( i ) {
               return typeof i === 'string' ?
                  i.replace(/[\$,]/g, '')*1 :
                  typeof i === 'number' ?
                     i : 0;
            };

            var total = rows
               .data()
               .pluck("qty")
               .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
            }, 0);

            return $("<tr/>")
               .append( "<td colspan=\'4\' style=\'font-weight:bold;\' class=\'text-right;\'> Sub Total " + group + " </td>" )
               .append( "<td style=\'font-weight:bold;\' class=\'text-right\'> " + total + " </td>")
               .append( "</tr>" );
         },
         dataSrc: groupColumn
      },
      columns: [
         {
            title: "Golongan Operasi",
            data: "golongan_operasi",
         },
         {
            title: "Tindakan Operasi",
            data: "tindakan_operasi",
         },
         {
            title: "Kegiatan Operasi",
            data: "kegiatan_operasi",
         },
         {
            title: "Dokter Operator",
            data: "dokter_operator",
         },
         {
            title: "Qty Tindakan",
            data: "qty",
            className: "text-right",
            orderable: false,
         },
      ],
      footerCallback: function(row, data, start, end, display) {
         var api = this.api();
         var intVal = function ( i ) {
            return typeof i === 'string' ?
               i.replace(/[\$,]/g, '')*1 :
               typeof i === 'number' ?
                  i : 0;
         };

         var grandTotal = api
                .column( 4 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
         
         $( api.column( 4 ).footer() ).html(grandTotal);
      },
      formFilters: [
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
         {
            fieldName: 'golonganoperasi_id',
            label: 'Golongan Operasi',
            type: {
               name: 'selectMultiple',
            }
         },
         {
            fieldName: 'tindakan_operasi_id',
            label: 'Tindakan Operasi',
            type: {
               name: 'selectMultiple',
            }
         },
         {
            fieldName: 'kegiatanoperasi_id',
            label: 'Kegiatan Operasi',
            type: {
               name: 'selectMultiple',
            }
         },
         {
            fieldName: 'dokter_operator_id',
            label: 'Dokter Operator',
            type: {
               name: 'selectMultiple',
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
      const tableElement = $(`#${tableId}`).DataTable()
      showLoader()
      tableElement.context[0].ajax.data.advancedFilter = serializeArrayToJson(formWrapper)
      tableElement.ajax.reload()
   });
   $('#btn-search__example').css('display', 'none');
   $('#btn-reset__example').css('display', 'none');
   $("#example-golonganoperasi_id--form").select2InfinityScroll({
      url: "/bedah/laporan-detail-operasi/filters?type=golongan_operasi",
      callbackData: (param) => {
          return {
              payload: {
                  ...param,
              }
          }
      }
   })
   $("#example-tindakan_operasi_id--form").select2InfinityScroll({
      url: "/bedah/laporan-detail-operasi/filters?type=tindakan_operasi",
      callbackData: (param) => {
          return {
              payload: {
                  ...param,
              }
          }
      }
   })
   $("#example-kegiatanoperasi_id--form").select2InfinityScroll({
      url: "/bedah/laporan-detail-operasi/filters?type=kegiatan_operasi",
      callbackData: (param) => {
          return {
              payload: {
                  ...param,
              }
          }
      }
   })
   $("#example-dokter_operator_id--form").select2InfinityScroll({
      url: "/bedah/laporan-detail-operasi/filters?type=dokter_operator",
      callbackData: (param) => {
          return {
              payload: {
                  ...param,
              }
          }
      }
   })
});