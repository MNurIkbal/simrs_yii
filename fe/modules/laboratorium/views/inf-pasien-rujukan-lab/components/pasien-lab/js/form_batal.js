var tableRencana;
var _listBatal = {
   tindakan: [],
   paket: []
}

var option = new Option(nama_pegawai, pegawai_id, true, true);
$("#batalorderpenunjangform-peg_menyetujui_id").append(option);
$('#batalorderpenunjangform-peg_menyetujui_id').val(pegawai_id).trigger('change');

$(document).on("click", ".data-reset", function() {
   tableRencana.draw();
   $("#btn-edit").prop("disabled", true);
   $("#btn-delete").prop("disabled", true);
});

$(document).on("click", "#tb-rencana-pemeriksaan-lab tbody tr", function() {
   try {
      primaryKey = tableRencana.row(".selected").data().primary ? tableRencana.row(".selected").data().primary : null;
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
   _recapListBatal()
});

var _recapListBatal = () => {
   _listBatal = {
      tindakan: [],
      paket: []
   }
   tableRencana.rows(".selected").data().each(function(val) {
      if (typeof val.daftartindakan_id != "undefined" && typeof val.tipepaket_id != "undefined") {
         if (val.daftartindakan_id) {
            _listBatal.tindakan.push(val.daftartindakan_id)
         } else {
            _listBatal.paket.push(val.tipepaket_id)
         }
      }
   })
}

$(document).ready(function() {
   tableRencana = $("#tb-rencana-pemeriksaan-lab").docoTabel({
      filter: false,
      displayLength: 10,
      columnDefs: [
         {
            orderable: false,
            targets: 0,
            class: 'checkboxes-all',
            checkboxes: {
               selectRow: true,
               stateSave: false,
            },
            defaultContent: ''
         },
      ],
      select: {
         style: 'multi',
         selector: 'tr'
      },
      sorting: [[2, 'asc']], 
      processing: true,
      serverSide: true,
      scrollCollapse: true,
      ajax: baseUrl + "laboratorium/inf-pasien-rujukan-lab/get-data-pemeriksaan?id="+id,
      columns: [
         {
            data: 'permintaankepenunjang_id',
            searchable: false,
            orderable: false,
            visible: true,
            defaultContent: ''
         },
         {
            title: "No.", 
            data: "rowNum", 
            searchable: false, 
            orderable: false
         },
         {   data: "jenispemeriksaanlab_nama", searchable: false },
         {
            data: "daftartindakan_nama",
            searchable: false
         },
         {
            data: "is_approve",
            render: (isApprove) => {
               if(isApprove == true){
                  return '<i class="fa fa-check-square-o fs-20"></i>';
               }else{
                  return '<i class="fa fa-square-o fs-20"></i>';
               }
            }, orderable: true,
            className: "text-center",
            searchable: false
         },
      ],
      drawCallback: function (settings) {
         var rowIndex = [];
         tableRencana.rows().every(function(rowIdx, tableLoop, rowLoop){
            var rowData = this.data();
            if (rowData.is_deleted == true) {
               $(this.node()).find('td.dt-checkboxes-cell').addClass("c-not-allowed") 
               $(this.node()).find('td.dt-checkboxes-cell').find(".dt-checkboxes").attr('checked',true) 
               $(this.node()).find('td.dt-checkboxes-cell').find(".dt-checkboxes").attr('disabled',true) 
               rowIndex.push(rowIdx)
            }    
         });
         tableRencana.rows(rowIndex).select().remove();
         _recapListBatal()
      },
   });
   $(".dataTables_filter").hide();
   $(".filter-form").datatableBootstrapFilter(tableRencana , []);
   dateRangeHelper(".startDate");   
   $('#tb-rencana-pemeriksaan-lab').on('click', 'thead .dt-checkboxes-select-all', function(e){
      setTimeout(function(){
         _recapListBatal()
      }, 150);
   });
});