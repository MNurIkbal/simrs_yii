<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Kasir', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .dataTable thead .sorting {
        padding-left: 2.2rem !important;
        padding-right: 0.25rem !important;
    }
</style>

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
                        <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
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
                    <?= DocoHelpers::generateToolbar([
                        'search',
                        'reset',
                        'excel',
                        'kwitansi' => [
                            'title' => Yii::t('fe', 'Cetak Kwitansi'),
                            'icon' => 'fa fa-print',
                            'method' => '#',
                            'attributes' => [
                                'id' => 'cetak-kwitansi',
                                'data-options' => 'link',
                                'target' => '_blank',
                            ]
                        ]
                    ]); ?>
                </div>
            </div>

            <div class="panel-body">
                <div class="advanced-filter">
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th width="1">No</th>
                            <th><?= \Yii::t("fe", "Tanggal"); ?></th>
                            <th><?= \Yii::t("fe", "No Transaksi"); ?></th>
                            <th><?= \Yii::t("fe", "Jenis"); ?></th>
                            <th><?= \Yii::t("fe", "dari/kepada"); ?></th>
                            <th style="width: 150px !important;"><?= \Yii::t("fe", "deskripsi"); ?></th>
                            <th><?= \Yii::t("fe", "Tipe"); ?></th>
                            <th><?= \Yii::t("fe", "Metode Pembayaran"); ?></th>
                            <th><?= \Yii::t("fe", "Jumlah"); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="9"><?= \Yii::t("fe", "Data tidak ditemukan."); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    // Global Var
    var table;

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        $("#cetak-kwitansi").prop("disabled", true);
        table = $("#example").docoTabel({
            columnDefs: [ {
                sortable: false,
                className: "select-checkbox",
                targets:   0
            }],
            select: {
                style: "os",
                selector: "tr"
            },
            filter: true,
            sorting: [[2, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"' . (Yii::$app->controller->module->id) . '/inf-transaksi-in-out/get-data",
            columns: [
                {data: null, searchable: false, sortable: false, defaultContent:""},
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "' . (\Yii::t("fe", "Tanggal")) . '", 
                    data: "tgl_transaksi"
                },
                {
                    title: "' . (\Yii::t("fe", "No Transaksi")) . '", 
                    data: "no_transaksi"
                },
                {
                    title: "' . (\Yii::t("fe", "Jenis")) . '", 
                    data: "jenis",
                    // name: "jenis_transaksi",
                },
                {
                    title: "' . (\Yii::t("fe", "Dari / Kepada")) . '", 
                    data: "dari_kepada"
                },
                {
                    title: "' . (\Yii::t("fe", "Deskripsi")) . '", 
                    data: "deskripsi",
                    render: function ( data, type, row ) {
                        return (row.deskripsi.length > 50) ? `${row.deskripsi.substr(0, 50)}...` : row.deskripsi;
                    },
                    searchable: false
                },
                {
                    title: "' . (\Yii::t("fe", "Tipe")) . '", 
                    data: "tipe",
                    // name: "tipe_transaksi",
                },
                {
                    title: "' . (\Yii::t("fe", "Metode Pembayaran")) . '", 
                    data: "metode_pembayaran_nama",
                    // name: "metode_pembayaran",
                },
                {
                    title: "' . (\Yii::t("fe", "Jumlah (Rp.)")) . '", 
                    data: "jumlah", 
                    class: "text-right", 
                    searchable: false
                },
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, 
            [
                [
                    2, 
                    \'<div class="input-group"><input type="text" value="' . date('d-M-Y', strtotime('-1 months')) . '" id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" value="' . date('d-M-Y', strtotime('+1 months')) . '"  id="rangeDemoFinish" class="form-control endDate" readonly="readonly"/><input type="text" style="display:none" class="targetDate"></div>\'
                ], [
                    4, 
                    \'' . (preg_replace(
    "/[\n\t\r]/i",
    '',
    Html::dropDownList(
        'lookup_name',
        '',
        ArrayHelper::map($jenisTrans, 'lookup_name', 'lookup_name'),
        [
            'class' => 'form-control select2',
            'prompt' => \Yii::t('fe', '-- Pilih --')
        ]
    )
)) . '\'
                ],
                [
                    7, 
                    \'' . (preg_replace(
    "/[\n\t\r]/i",
    '',
    Html::dropDownList(
        'lookup_name',
        '',
        ArrayHelper::map($tipeTrans, 'lookup_name', 'lookup_name'),
        [
            'class' => 'form-control select2',
            'prompt' => \Yii::t('fe', '-- Pilih --')
        ]
    )
)) . '\'
                ], 
                [
                    8, 
                    \'' . (preg_replace(
    "/[\n\t\r]/i",
    '',
    Html::dropDownList(
        'lookup_name',
        '',
        ArrayHelper::map($metodeBayar, 'lookup_name', 'lookup_name'),
        [
            'class' => 'form-control select2',
            'prompt' => \Yii::t('fe', '-- Pilih --')
        ]
    )
)) . '\'
                ], 
            ], {
                2: 0,
                3: 1,
                4: 2,
                5: 3,
                6: 4,
                7: 5,
            }, true
        );

        dateRangeHelper(".startDate",".endDate",".targetDate");
        $(".daterange-basic").daterangepicker({
            startDate: "' . (date("01-M-Y")) . '", autoUpdateInput: true,
            endDate: "' . (date("d-M-Y")) . '",
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
                var url = "/kasir/inf-transaksi-in-out/cetak-kwitansi?id=" + primary;
                $(this).attr("data-target", url);
            }
        });
    });
', View::POS_END, 'b-index');
?>