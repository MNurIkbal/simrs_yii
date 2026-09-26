<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Kasir', 'url' => ['index']];
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>

            <div class="panel-toolbar clearfix">
                <div class="btn-group pull-left">
                    <?=DocoHelpers::generateToolbar([
                        'search',
                        'detail'=>[
                            'type'=>'button',
                            'title' => \Yii::t('fe', 'Detail'),
                            'icon' => 'fa fa-eye',
                            'method' => 'not-exist',
                            'attributes' => [
                                'id'=>'btn-detail',
                                'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/detail?id='
                            ]
                        ],
                        'pengembalian-uangmuka' =>[
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Pengembalian'),
                            'icon' => 'fa fa-refresh',
                            'attributes' => [
                                'data-target' => '/kasir/inf-pasien-uang-muka/pengembalian?id=',
                                'class'=>'btn-pengembalian-uangmuka',
                                'disabled'=>'disabled'
                            ]
                        ],
                        'excel-bgprocess' => [
                            'type' => 'button',
                            'title' => 'Unduh excel',
                            'icon' => 'fa fa-file-excel-o',
                            'method' => 'not-exist',
                            'attributes' => [
                               'id' => 'excel-bgprocess',
                               'data-options' => 'excel-serconn',
                               'data-target' => '#modal_backdrop',
                               'data-width' => '75%',
                               'data-url' => '/kasir/inf-pasien-uang-muka/show-popup?',
                            ]
                        ],
                        'reset'
                    ]);?>
                </div>
            </div>

            <div class="panel-body">
                    <div class="advanced-filter">
                    </div>
                    <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th width="1"></th>
                                <th width="70">No</th>
                                <th><?=\Yii::t("fe", "Tanggal Masuk - Keluar");?></th>
                                <th><?=\Yii::t("fe", "Tanggal Pembayaran");?></th>
                                <th></th>
                                <th><?=\Yii::t("fe", "No pendaftaran");?></th>
                                <th><?=\Yii::t("fe", "No rekam medik");?></th>
                                <th><?=\Yii::t("fe", "Nama pasien");?></th>
                                <th><?=\Yii::t("fe", "Keterangan");?></th>
                                <th><?=\Yii::t("fe", "Jumlah Uang Muka");?></th>
                                <th><?=\Yii::t("fe", "Jumlah Pemakaian");?></th>
                                <th><?=\Yii::t("fe", "Jumlah Pengembalian");?></th>
                                <th><?=\Yii::t("fe", "Sisa");?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" colspan="9"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                            </tr>
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

    $(".data-batal-uang-muka").click(function(e){
        e.preventDefault()
        var tableData = table.row(".selected").data();
        if(typeof tableData !== "undefined")
        {
            if(tableData.is_closing == true){
            docoNotification("warning", "Terjadi Kesalahan", "Pasien Sudah closing!");
            }else{
            var primary = tableData.primary;
            var url = window.location.origin;
            var target = $(this).attr("data-target");
            $(this).attr("action", target+primary);
                $(this).docoForm("delete",{
                    additional: "data-rm",
                    success : function (data) {
                        table.draw();
                    }
            }); 
            }
        }else{
            docoNotification("warning", "Terjadi Kesalahan", "Belum ada data yang dipilih!");
        }
    });

    $(function(){
        table = $("#example").docoTabel({
            columnDefs: [ {
                sortable: false,
                className: "select-checkbox",
                targets:   0
            }],
            select: {
                style:    "os",
                selector: "tr"
            },
            filter: true,
            sorting: [[4, "desc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            stateSave: false,
            ajax: baseUrl+"'.(Yii::$app->controller->module->id).'/inf-pasien-uang-muka/get-data",
            columns: [
                {data: null, searchable: false, sortable: false, defaultContent:""},
                {title: "No", data: "rowNum", searchable: false, sortable: false},
                {title: "'.(\Yii::t("fe", "Tanggal Masuk - Keluar")).'", data: "tgl_masuk_keluar", searchable: false, sortable: false},
                {title: "'.(\Yii::t("fe", "Tanggal Pembayaran")).'", data: "tgl_pembayaran"},
                {title: "'.(\Yii::t("fe", "Tanggal Pendaftaran")).'", data: "tgl_pendaftaran", visible: false},
                {title: "'.(\Yii::t("fe", "No pendaftaran")).'", data: "no_pendaftaran"},
                {title: "'.(\Yii::t("fe", "No rekam medik")).'", data: "no_rekam_medik"},
                {title: "'.(\Yii::t("fe", "Nama pasien")).'", data: "nama_pasien"},
                {title: "'.(\Yii::t("fe", "Keterangan")).'", data: "keterangan", searchable: false},
                {title: "'.(\Yii::t("fe", "Jumlah Uang Muka (Rp.)")).'", data: "jumlah_uangmuka", searchable: false, class: "text-right", sortable: false},
                {title: "'.(\Yii::t("fe", "Jumlah Pemakaian (Rp.)")).'", data: "pemakaian_uangmuka", searchable: false, class: "text-right", sortable: false},
                {title: "'.(\Yii::t("fe", "Jumlah Pengembalian (Rp.)")).'", data: "pengembalian", searchable: false, class: "text-right", sortable: false},
                {title: "'.(\Yii::t("fe", "Sisa (Rp.)")).'", data: "sisa_uangmuka", searchable: false, class: "text-right", sortable: false},
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table,
            [
                [
                    4,
                    \'<div class="input-group"><input type="text" id="rangeDemoStart" value="" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="" readonly="true" class="form-control endDate"/><input type="text" style="display:none" class="targetDate" col-index=2></div>\'
                ],
                [
                    3,
                    \'<div class="input-group"><input type="text" id="rangeDemoStartPembayaran" value="" class="form-control startDatePembayaran"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinishPembayaran" value="" readonly="true" class="form-control endDatePembayaran"/><input type="text" style="display:none" class="targetDatePembayaran" col-index=3></div>\'
                ],
            ], {
                4: 0,
                3: 1,
                7: 2,
                6: 3,
                5: 4,
            }, true
        );

        dateRangeHelper(".startDate",".endDate",".targetDate");
        dateRangeHelper(".startDatePembayaran",".endDatePembayaran",".targetDatePembayaran");
    });

    $(document).on("click", "#example tbody tr", function () {
        if(typeof table.row(".selected").data() !== "undefined"){
            pengembalian = (typeof table.row(".selected").data().pengembalian !== "undefined") ? docoHelper.convertToAngka(table.row(".selected").data().pengembalian) : 0;
            sisa_uangmuka = (typeof table.row(".selected").data().sisa_uangmuka !== "undefined") ? docoHelper.convertToAngka(table.row(".selected").data().sisa_uangmuka) : 0;
            isBisaDikembalikan = (table.row(".selected").data().sisa == null || table.row(".selected").data().sisa == 0) || (table.row(".selected").data().pemakaian_uangmuka == 0)
            isBisaDibatalkan =  (pengembalian > 0 && sisa_uangmuka > 0)

            if(isBisaDikembalikan){
                    if( !$(".btn-pengembalian-uangmuka").prop("disabled") ){
                        $(".btn-pengembalian-uangmuka").prop("disabled", true)
                    }
            } else {
                $(".btn-pengembalian-uangmuka").prop("disabled", false)
            }
            if(isBisaDibatalkan)
            {
                $(".btn-pengembalian-uangmuka").prop("disabled", false)
            } 
        }           
    })

    $(document).ready(function(){
        $(".startDatePembayaran").val("");
        $(".endDatePembayaran").val("");
        $(".startDatePulang").val("");
        $(".endDatePulang").val("");
    });

    $(document).on("click", ".data-reset", function() {
        $(".startDate").val("");
        $(".endDate").val("");
    });

    $(".daterange-basic").daterangepicker({
        startDate: "'.(date("01-M-Y")).'", autoUpdateInput: true,
        endDate: "'.(date("d-M-Y")).'",
        applyClass: "bg-slate-600",
        cancelClass: "btn-default",
        locale: {
            format: "DD-MM-YYYY"
        }
    });

    $(document).on("click",".btn-print-uang-muka",function(e){
        e.preventDefault();
        var tableData = table.row(".selected").data();
        if (typeof tableData !== "undefined") {
            if("primary" in tableData){
                var target = $(this).attr("data-target");
                var primary = tableData.primary;
                window.open(target+"?id="+primary);
            }else{
                docoNotification("warning", "Terjadi Kesalahan", "Primary Tidak Didefinisikan");
            }
        }else{
            docoNotification("warning", "Terjadi Kesalahan", "Belum ada data yang dipilih!");
        }
    });
', View::POS_END, 'b-index');
?>
