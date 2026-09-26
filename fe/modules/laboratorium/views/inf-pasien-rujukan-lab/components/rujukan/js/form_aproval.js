var table;
var _listApprove = {
   tindakan: [],
   paket: []
}

var _recapListApprove = () => {
   _listApprove = {
      tindakan: [],
      paket: []
   }
   table.rows(".selected").data().each(function(val) {
      if (typeof val.daftartindakan_id != "undefined" && typeof val.tipepaket_id != "undefined") {
         if (val.daftartindakan_id) {
               _listApprove.tindakan.push(val.daftartindakan_id)
         } else {
               _listApprove.paket.push(val.tipepaket_id)
         }
      }
   })
}

$(document).on("click", ".data-reset", function() {
   table.draw();
   $("#btn-edit").prop("disabled", true);
   $("#btn-delete").prop("disabled", true);
});

$(document).on("click", "#tb-rencana-pemeriksaan-lab tbody tr", function() {
   try {
      primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
   } catch (e) {
      primaryKey = false;
   }
   $("#btn-edit").attr("action", updateUrl + primaryKey);
   if ($('#tb-inf-pasien-rujukan-lab tr.selected').length == 0) {
      $("#btn-edit").prop("disabled", true);
      $("#btn-delete").prop("disabled", true);
   }
   else {
      $("#btn-edit").prop("disabled", false);
      $("#btn-delete").prop("disabled", false);
   }
   _recapListApprove()
});

$(document).ready(function() {
    table = $("#tb-rencana-pemeriksaan-lab").docoTabel({
      select: {
         style: "multi",
         selector: "tr"
      },
      columnDefs: [ {
         searchable: false,
         orderable: false,
         className: "select-checkbox",
         targets: 0,
      }],
      filter: false,
      sorting: [[2, "asc"]],
      displayLength: 10,
      processing: true,
      serverSide: true,
      scrollX: true,
      paging: false,
      length: 15,
      
      ajax: baseUrl + "laboratorium/inf-pasien-rujukan-lab/get-data-pemeriksaan?id="+id,
      columns: [
         {
               title: "",
               data: null,
               defaultContent: "",
               searchable: false,
               orderable: false
         },
         {
               data: "rowNum", 
               searchable: false, 
               orderable: false
         },
         {
               data: "jenispemeriksaanlab_nama", 
               searchable: false
         },
         {
               data: "daftartindakan_nama",
               searchable: false
         },
         {
               data: "qtypermintaan", 
               searchable: false
         },
         {
               data: "is_checkbox", 
               searchable: false
         },
      ],
      scrollCollapse: true,
      drawCallback: function (settings) {
         table.rows().select();
         var rowIndex = [];
         table.rows().every(function(rowIdx, tableLoop, rowLoop){
               var rowData = this.data();
               if (rowData.is_approve == true) { 
                  $(this.node()).find('td.select-checkbox').addClass("c-not-allowed")
                  $(this.node()).find('td').addClass("c-not-allowed-row")
                  rowIndex.push(rowIdx)
               }    
         });
         table.rows(rowIndex).select().remove();
         _recapListApprove()
      },
      fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
         $('td.select-checkbox', nRow).prop('checked', true);
         _recapListApprove()
      }
    });

    $(".dataTables_filter").hide();

    $(".filter-form").datatableBootstrapFilter(table , [
   
    ]);

    dateRangeHelper(".startDate", ".endDate", ".targetDate");

    $(".daterange-basic").daterangepicker({
      startDate: '<?=(date("01-M-Y"))?>', autoUpdateInput: true,
      endDate: '<?=(date("d-M-Y"))?>',
      applyClass: "bg-slate-600",
      cancelClass: "btn-default",
      locale: {
         format: "DD-MMMM-YYYY"
      }
    });
    
    $(document).on("keydown", null, "alt+s", function (event) {
      $(".btn-simpan").click();
    });
});