<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title ='Informasi Pembayaran Piutang Pasien';
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("ruangan_name")];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
              <!-- breadcrumbs replace with this -->
              <div class="row">
                  <div class="column-1">
                      <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                  </div>
                  <div class="column-2">
                      <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                      <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
              </div>
              <!-- end -->
              <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-toolbar clearfix">
                <div class="btn-group pull-left">
                    <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset',
                        // 'print',
                        // 'pdf',
                        'excel',
                        'kwitansi' => [
                            'title' => Yii::t('fe', 'Cetak Kwitansi'),
                            'icon' => 'fa fa-print',
                            'method' => '#',
                            'attributes' => [
                            'id'=>'cetak-kwitansi',
                            'data-options' => 'link',
                            'target'=>'_blank',
                            ]
                        ]
                    ]);?>
                </div>
            </div>

            <div class="panel-body">
                <div class="advanced-filter">
                    
                    </div>
                <table class="table datatable-basic table-striped table-hover dataTable no-footer"
                    id="example" style="width:100%" data-filter=".form-filter">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th width="20">No</th>
                            <th><?=Yii::t('fe', 'Tanggal Pembayaran')?></th>
                            <th><?=Yii::t('fe', 'Nama Pasien')?></th>
                            <th><?=Yii::t('fe', 'No. RM')?></th>
                            <th><?=Yii::t('fe', 'No. Pendaftaran')?></th>
                            <th><?=Yii::t('fe', 'No. Pembayaran')?></th>
                            <th><?=Yii::t('fe', 'Metode Pembayaran')?></th>
                            <th><?=Yii::t('fe', 'Jumlah Bayar')?></th>
                            <th><?=Yii::t('fe', 'Sisa Piutang')?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Untuk Kebutuhan Modal Global -->
<div id="modal_backdrop_search" class="modal fade" style="z-index:1065;" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<!-- End -->

<?php
$this->registerJs('
var table;
$(document).on("click", ".data-reload", function() {
    table.draw();
});
$(document).ready(function() {
    $("#cetak-kwitansi").prop("disabled", true);
    table = $("#example").docoTabel({
        filter: true,
        columnDefs: [
            {
                orderable: false,
                className: "select-checkbox",
                targets:   0
            },
        ],
        select: {
            style:    "os",
            selector: "tr"
        },
        sorting: [[2, "asc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl+"kasir/inf-pembayaran-piutang-pasien/get-data",
        columns: [

            {
                title: "",
                data: null,
                defaultContent: "",
                searchable: false,
                orderable: false
            },
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {title: "'.(\Yii::t("fe", "Tanggal Pembayaran")).'", data: "tgl_pembayaranpiutang"},
            {title: "'.(\Yii::t("fe", "Nama Pasien")).'", data: "nama_pasien"},
            {title: "'.(\Yii::t("fe", "No. RM")).'", data: "no_rekam_medik"},
            {title: "'.(\Yii::t("fe", "No. Pendaftaran")).'", data: "no_pendaftaran"},
            {title: "'.(\Yii::t("fe", "No. Pembayaran")).'", data: "no_pembayaranpiutang"},
            {title: "'.(\Yii::t("fe", "Metode Pembayaran")).'", data: "metode_pembayaran"},
            {title: "'.(\Yii::t("fe", "Jumlah Bayar")).'", data: "total_bayarpiutang", searchable: false},
            {title: "'.(\Yii::t("fe", "Sisa Piutang")).'", data: "total_sisapiutang", searchable: false},
        ],
    });

    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table, [
        [
            2,
            \'<div class="input-group"><input type="text" value="'.date('d-M-Y').'" id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" readonly="true" class="form-control endDate" value="'.date('d-M-Y').'"/><input type="text" style="display:none" class="targetDate"></div>\'
        ],
        [
            7,
            \'<div class="form-group">'.preg_replace('/[\n\t\r]/i', "", preg_replace('/[\']/i', "\"", 
                Html::dropDownList('metode_bayar', '', $listMetode, 
                        [
                            'class' => 'form-control select2 metode_bayar', 
                            'col-index'=> 5,
                            'prompt'=> '-Pilih Metode Pembayaran-'
                        ]
                    )
                )
            ).'<div>\'
        ],
    ],
    {
        2:0,
        3:1,
        4:2,
        5:3,
        7:4,
    }, true
);

dateRangeHelper(".startDate",".endDate",".targetDate");

$(".daterange-basic").daterangepicker({
    startDate: "'.(date("01-M-Y")).'", autoUpdateInput: true,
    endDate: "'.(date("d-M-Y")).'",
    applyClass: "bg-slate-600",
    cancelClass: "btn-default",
    locale: {
        format: "DD-MMMM-YYYY"
    }
});
$(document).on("click", "#example tbody tr", function(){
    var primary = null;
    try {
        primary = table.row(".selected").data().primary;
    }
    catch(e) {
        primary = null;
    }

    if (primary) {
        $("#cetak-kwitansi").attr("disabled", false);
    }
    else {
        $("#cetak-kwitansi").attr("disabled", true);
    }
})
$("#cetak-kwitansi").click(function(e){
    e.preventDefault();
    var tableData = table.row(".selected").data();
    var primary = null;

    if(typeof tableData !== "undefined") {
        $("#cetak-kwitansi").prop("disabled", false);
        primary = tableData.primary;
        var url = "/kasir/inf-pembayaran-piutang-pasien/cetak-kwitansi?id=" + primary;
        $(this).attr("data-target", url);
    }
});
});
', View::POS_END, 'b-index');

?>
