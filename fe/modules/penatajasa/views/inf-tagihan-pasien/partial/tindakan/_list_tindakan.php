<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\ActiveForm;
use kartik\widgets\DateTimePicker;

?>
<style type="text/css">
     .my-legend .legend-title {
      text-align: left;
      margin-bottom: 8px;
      font-weight: bold;
      font-size: 14px;
    }
    .my-legend .legend-scale ul {
      margin: 0;
      padding: 0;
      float: left;
      list-style: none;
    }
    .my-legend .legend-scale ul li {
      display: grid;
      float: left;
      /*width: 50px;*/
      /*width: 80%;*/
      margin-bottom: 6px;
      margin-right: 5px;
      text-align: center;
      font-size: 13px;
      list-style: none;
      text-align: -webkit-auto;
    }
    .my-legend ul.legend-labels li span {
      /*display: block;*/
      float: left;
      /*height: 15px;*/
      /*width: 50px;*/
      /*border: solid 0.2px;*/
    }
    .my-legend .legend-source {
      font-size: 70%;
      color: #999;
      clear: both;
    }
    .my-legend a {
      color: #777;
    }    
    .background-not-accept {
      background: #59eacc;
      width: 60px;
      border: solid 0.2px;
      height: 15px;
      opacity: 0.65;
    }
    .row-background-not-accept {
      background: #59eacc;
      /*opacity: 0.65;*/
    }
    .background-remove {
      background: #fd9644;
      width: 60px;
      border: solid 0.2px;
      height: 15px;
      opacity: 0.65;
    }
    .row-background-remove {
      background: #fd9644;
      /*opacity: 0.65;*/
    }
    .btn-remove-tindakan {
        padding: 5px 10px;
        margin: 5px 5px;
    }
    /*.table-hover > tbody > tr:hover > th, .table-hover > tbody > tr:hover > td {
        background-color: auto ;
    }*/
    #scrolltable { 
         /*margin-top: 20px; */
         /*height: 355px; */
         /*overflow: auto;*/
         /*overflow-y: overlay;*/
    }
    /*#scrolltable th div {
         position: absolute; 
         margin-top: -20px;
    }*/
    .dataTables_scroll {
      max-height: 515px;
      /* overflow-y: overlay !important;
      position: relative;
      overflow-x: hidden; */
    }
</style>
<div class="detail-tindakan" id="detail-tindakan">
   <div class="col-md-12">
      <div class="panel panel-default">
         <div class="panel-heading">
               <h6 class="panel-title"><b><?= Yii::t('fe','Detail Transaksi') ?></b></h6>
         </div>
         <div class="panel-body">
            <div class="col-md-12">
            <br>
               <div class="row">
                  <button type="button"
                     class="addrow btn btn-info btn-labeled btn-xs"
                     data-toggle="modal"
                     data-original-title="Tambah"
                     data-popup="tooltip"
                     data-target="#modal_backdrop"
                     data-width="90%"
                     action=<?= "/penatajasa/inf-tagihan-pasien/add-tindakan?pendaftaran_id={$pendaftaran_id}" ?>>
                     <b><i class="fa fa-plus"></i></b>Tambah
                  </button>
               </div><br>
               <div class="row">
               <div class="panel-toolbar clearfix">
                  <?php
                     $defaultBtn = [
                     'search',
                     'reset',
                     ];
                  ?>
                  <?=DocoHelpers::generateToolbar($defaultBtn,'#table-tagihan-tindakan');?>
                  </div><br>
                  <div class="advanced-filter">
                  </div>
                  <table id="table-tagihan-tindakan" class="table table-striped table-condensed table-hover" style="width:100%">
                     <thead>
                        <tr class="bg-inverse">
                           <th><?=\Yii::t("fe", "No");?></th>
                           <th><?=\Yii::t("fe", "Tanggal Tindakan");?></th>
                           <th><?=\Yii::t("fe", "Instalasi - Ruangan");?></th>
                           <th><?=\Yii::t("fe", "Dokter");?></th>
                           <th><?=\Yii::t("fe", "Tindakan/Obat");?></th>
                           <th><?=\Yii::t("fe", "BMHP");?></th>
                           <th><?=\Yii::t("fe", "Qty");?></th>
                           <th><?=\Yii::t("fe", "Harga Satuan");?>(Rp)</th>
                           <th><?=\Yii::t("fe", "Tarif Cyto");?>(Rp)</th>
                           <th><?=\Yii::t("fe", "Tarif Penyulit");?>(Rp)</th>
                           <th><?=\Yii::t("fe", "Sub Total");?>(Rp)</th>
                           <th><?=\Yii::t("fe", "Aksi");?></th>
                        </tr>
                     </thead>
                     <tbody>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php
$this->registerJs('
    var table;
    $(document).ready(function() {
      table = $("#table-tagihan-tindakan").docoTabel({
         filter: true,
         select: {
            style:    "os",
            selector: "tr"
         },
         paging: false,
         sorting: [[1, "desc"]],
         displayLength: 10,
         stateSave: false,
         processing: true,
         serverSide: true,
         scrollCollapse: true,
         scrollY: true,
         scrollX: true,
         ajax: baseUrl+"penatajasa/inf-tagihan-pasien/get-data-tindakan?pendaftaran_id='.$pendaftaran_id.'",
         columns: [
            {
               title: "'.(\Yii::t("fe", "No")).'",
               data: null,
               searchable: false,
               orderable: false,
               render: (data, rowElement, rowData, rowAdditionalData) => {
                  var tableInfo = table.page.info()
                  return tableInfo.start + rowAdditionalData.row + 1
               }
            },
            {
               title: "'.(\Yii::t("fe", "Tanggal Tindakan")).'",  
               data: "tgl_tindakan",
               searchable: false,
            },
            {
               title: "'.(\Yii::t("fe", "Instalasi")).'<br>'.(\Yii::t("fe", "Ruangan")).'",  
               data: "instalasi_ruangan_nama",
               searchable: false,
            },
            {
               title: "'.(\Yii::t("fe", "Dokter")).'",  
               data: "nama_dokter",
            },
            {
               title: "'.(\Yii::t("fe", "Tindakan")).'",  
               data: "daftartindakan_nama",
            }, 
            {
               title: "'.(\Yii::t("fe", "BMHP")).'",  
               data: "obatalkes_nama",
               orderable: false,
            }, 
            {
               title: "'.(\Yii::t("fe", "Qty")).'",  
               data: "qty_tindakan",
               orderable: false,
               searchable: false,
            },
            {
               title: "'.(\Yii::t("fe", "Harga Satuan ")).'",  
               data: "tarif_satuan",
               searchable: false,
               class : "text-right",
               orderable: false,
            },
            {
               title: "'.(\Yii::t("fe", "Tarif Cyto")).'",
               data: "tarifcyto_tindakan",
               searchable: false,
               class : "text-right",
               orderable: false,
            },
            {
               title: "'.(\Yii::t("fe", "Tarif Penyulit")).'",
               data: "tarifpenyulit_tindakan",
               searchable: false,
               class : "text-right",
               orderable: false,
            },
            {
               title: "'.(\Yii::t("fe", "Sub Total")).'",
               data: "tarif_tindakan",
               searchable: false,
               class : "text-right",
               orderable: false,
            },
            {
               title: "'.(\Yii::t("fe", "Aksi")).'",  
               data: "aksi",
               searchable: false,
               orderable: false,
            }, 
         ],
         // drawCallback : function (settings) {
         //     var api = this.api();
         //     var dataRows = api.rows( {page:"current"} ).data();
         //     var totalTagihan = 0;
         //     $.each(dataRows, function (key, val) {
         //         if(val.is_deleted == false){
         //           totalTagihan += parseInt(docoHelper.convertToAngka(val.tarif_tindakan));
         //         }
         //     });
         //     $("#total_tagihan").val(docoHelper.convertToRupiah(totalTagihan));
         //     _sumTotal();
         // },
      });
      
      $(".dataTables_filter").hide();
      $(".filter-form").datatableBootstrapFilter(table, [
         
      ])
      dateRangeHelper(".startDate",".endDate",".targetDate");
      $(".daterange-basic").daterangepicker({
         startDate: "'.(date("d-M-Y")).'", autoUpdateInput: true,
         endDate: "'.(date("d-M-Y")).'",
         applyClass: "bg-slate-600",
         cancelClass: "btn-default",
         locale: {
               format: "DD-MMMM-YYYY"
         }
      });
    });

', View::POS_END, 'index-pen');

?>