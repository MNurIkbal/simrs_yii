var table;
var groupColumn = ["kelompoktindakan_nama"];

$(document).ready(function(){
   table = $("#example").docoTabel({
      filter: true,
      paging: false,
      order: [[4, "asc"],[0, "asc"]],
      processing: true,
      serverSide: true,
      scrollX: false,
      scrollY: true,
      stateSave: false,
      ajax: baseUrl+"kasir/lap-pendapatan-kasir/get-data",
      rowGroup: {
         startRender: function (rows, group) {
            return $("<tr/>")
               .append( "<td style=\'background:#FEF9E7;font-weight:bold;\' class=\'text-right;\'> " + group + " </td>" )
               .append( "<td style=\'background:#FEF9E7;\'> </td>")
               .append( "<td style=\'background:#FEF9E7;\'> </td>")
               .append("<td style=\'background:#FEF9E7;\'></td>")
               .append( "</tr>" );
         },
         endRender: function (rows, group) {
            var intVal = function ( i ) {
               return typeof i === 'string' ?
                  i.replace(/[\$,]/g, '')*1 :
                  typeof i === 'number' ?
                     i : 0;
            };

            var total = rows
               .data()
               .pluck("total_harga")
               .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
            }, 0);

            var sum_unit = rows
               .data()
               .pluck("qty")
               .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
            }, 0);
   
            return $("<tr/>")
               .append( "<td style=\'background:#EBF5FB;font-weight:bold;\' class=\'text-right;\'> SUB TOTAL " + group + " </td>" )
               .append( "<td style=\'background:#EBF5FB;\'> </td>" )
               .append( "<td style=\'background:#EBF5FB;font-weight:bold;\'  class=\'text-right\'> " + docoHelper.convertToRupiah(sum_unit) + " </td>" )
               .append( "<td style=\'background:#EBF5FB;font-weight:bold;\'  class=\'text-right\'> " + docoHelper.convertToRupiah(total) + " </td>")
               .append( "</tr>" );
         },
         dataSrc: groupColumn
      },
      columnDefs: [
         {
            "visible" : false, 
            "targets": groupColumn
         }
      ],
      columns: [
         {
            data: "daftartindakan_kode",
            name:"daftartindakan_kode",
            searchable: false,
            orderable: false,
         },
         {
            data: "daftartindakan_nama",
            name:"daftartindakan_nama",
            searchable: false,
            orderable: false,
         },
         {
            data: "qty",
            name:"qty",
            searchable: false,
            className: "text-right",
            orderable: false,
         },
         {
            data: "total_harga",
            name:"total_harga",
            searchable: false,
            className: "text-right",
            orderable: false,
            render: $.fn.dataTable.render.number( ".", ",", 2, "" )
         },
         {
            data: "kelompoktindakan_nama",
            name:"kelompoktindakan_nama",
            visible: false,
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

         grandTotal = api
            .column( 3 )
            .data()
            .reduce( function (a, b) {
               return intVal(a) + intVal(b);
            }, 0 );

            grandTotal_number = api
            .column( 2 )
            .data()
            .reduce( function (a, b) {
               return intVal(a) + intVal(b);
            }, 0 );
         $( api.column( 3 ).footer() ).html(
            'Rp. ' + docoHelper.convertToRupiah(grandTotal)
         );
         $( api.column( 2 ).footer() ).html(grandTotal_number);
      },
      drawCallback:function(){
         width_total = $(".dataTables_scrollFootInner").css("width");
         $(".dataTables_scrollFoot").css("width", width_total);
      }
   });
   $(".dataTables_filter").hide();
   dateRangeHelper(".startDate",".endDate",".targetDate");
   dateRangeHelper(".startDatePulang",".endDatePulang",".targetDatePulang");
   $(document).on("click", ".cari-pendapatan", function(event){
      event.preventDefault();
      var _startDatePembayaran = $(".startDate").val();
      var _endDatePembayaran = $(".endDate").val();
      var _periodePembayaran = _startDatePembayaran + " - " + _endDatePembayaran;
      var _startDatePulang = $(".startDatePulang").val();
      var _endDatePulang = $(".endDatePulang").val();
      var _periodePulang = _startDatePulang + " - " + _endDatePulang;

      var _unit = $("#filter_unit").val();
      var _kelas = $("#filter_kelas").val();
      var _kelompok = $("#filter_kelompok").val();
      var _tindakan = $("#filter_tindakan").val();
      var _url = baseUrl+"kasir/lap-pendapatan-kasir/get-data?tgl_pembayaran="+ _periodePembayaran + "&tgl_pulang=" + _periodePulang + "&unit=" + _unit + "&kelas=" + _kelas + "&kelompok=" + _kelompok + "&tindakan=" + _tindakan
      var _urlExcel = baseUrl+"kasir/lap-pendapatan-kasir/show-popup?unit="+/*  _periodePembayaran */ + /* "&tgl_pulang=" + _periodePulang + */ /* "&unit=" + */ _unit + "&kelas=" + _kelas + "&kelompok=" + _kelompok + "&tindakan=" + _tindakan
      table.ajax.url(_url).draw(false);
   });
   $("#excel-bgprocess").unbind("click");
    $("#excel-bgprocess").on("click", function (event) {
      var _startDatePembayaran = $(".startDate").val();
      var _endDatePembayaran = $(".endDate").val();
      var _startDatePulang = $(".startDatePulang").val();
      var _endDatePulang = $(".endDatePulang").val();

      var _unit = $("#filter_unit").val();
      var _kelas = $("#filter_kelas").val();
      var _namaKelas = $("#filter_kelas option:selected").text();
      if(!_kelas || _kelas.length === 0){
         _namaKelas = ''
      }
      var _kelompok = $("#filter_kelompok").val();
      var _tindakan = $("#filter_tindakan").val();
      var query = "kasir/lap-pendapatan-kasir/show-popup?tgl_pembayaran="+ _startDatePembayaran +" - "+ _endDatePembayaran 
                  +"&tgl_pulang="+ _startDatePulang +" - "+ _endDatePulang 
                  +"&unit="+ _unit 
                  +"&kelas=" + _kelas 
                  +"&namaKelas=" + _namaKelas 
                  +"&kelompok=" + _kelompok 
                  +"&tindakan=" + _tindakan +"&"
        _url = encodeURI(baseUrl+query)
        $(this).attr("data-url",_url);
    })

   $(document).on("click", ".reset-pendapatan", function (event) {
      event.preventDefault()
      var parent = $(this).data("parent");
      $(".form-group").removeClass("has-error");
      $("span.help-block.error").remove();
      $("div.help-block.error").remove();
      if (typeof parent !== "undefined") {
         $(parent + " [type=reset]").click();
         $("#rangeDemoStart").val(null).trigger("change");
         $("#rangeDemoFinish").val(null).trigger("change");
         $("#rangeDemoStartPulang").val(null).trigger("change");
         $("#rangeDemoFinishPulang").val(null).trigger("change");
         $("#filter_unit").val(null).trigger("change");
         $("#filter_kelas").val(null).trigger("change");
         $("#filter_kelompok").val(null).trigger("change");
         $("#filter_tindakan").val(null).trigger("change");
         _url = baseUrl+"kasir/lap-pendapatan-kasir/get-data"
         $("#example").DataTable().ajax.url(_url).draw(false);
         $(parent + " .advancedFilterDo").click();
      } else {
         localStorage.clear();
         $(".advancedFilter [type=reset]").click();
         $(".advancedFilterDo").click();
      }
   });
   $(document).on("click", ".excel-pendapatan", function(event){
      event.preventDefault();
      var _startDatePembayaran = $("#rangeDemoStart").val();
      var _endDatePembayaran = $("#rangeDemoFinish").val();
      var _periodePembayaran = _startDatePembayaran + " - " + _endDatePembayaran;

      var _startDatePulang = $("#rangeDemoStartPulang").val();
      var _endDatePulang = $("#rangeDemoFinishPulang").val();
      var _periodePulang = _startDatePulang + " - " + _endDatePulang;

      var _unit = $("#filter_unit").val();
      var _kelas = $("#filter_kelas").val();
      var _kelompok = $("#filter_kelompok").val();
      var _tindakan = $("#filter_tindakan").val();
      
      var _url = baseUrl+"kasir/lap-pendapatan-kasir/export-excel?tgl_pembayaran="+ _periodePembayaran + "&tgl_pulang=" + _periodePulang + "&unit=" + _unit + "&kelas=" + _kelas + "&kelompok=" + _kelompok + "&tindakan=" + _tindakan
      window.open(_url);
   });
   
   $("#filter_kelas").select2InfinityScroll({
      url: "/kasir/lap-pendapatan-kasir/filters?type=kelas",
      callbackData: (param) => {
          return {
              payload: {
                  ...param,
              }
          }
      }
   })
   $("#filter_kelompok").select2InfinityScroll({
      url: "/kasir/lap-pendapatan-kasir/filters?type=kelompok",
      callbackData: (param) => {
          return {
              payload: {
                  ...param,
              }
          }
      }
   })
   $("#filter_tindakan").select2InfinityScroll({
      url: "/kasir/lap-pendapatan-kasir/filters?type=tindakan",
      callbackData: (param) => {
          return {
              payload: {
                  ...param,
              }
          }
      }
   })
});

