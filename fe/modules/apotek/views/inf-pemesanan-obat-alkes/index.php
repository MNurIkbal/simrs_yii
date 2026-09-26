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
use app\components\DocoConstants;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"), 'url' => []];
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
                        <h3 class="panel-title"><b><?= $this->title ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>

            <div class="panel-toolbar clearfix">
                <div class="btn-group pull-left">
                    <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset',
                        'kirim' => [
                            'title' => \Yii::t('fe', 'Kirim'),
                            'icon' => 'fa fa-paper-plane',
                            'method' => '#',
                            'attributes' => [
                                'class' => 'kirim-pemesanan',
                                'data-target' => Url::home().('apotek/transaksi-mutasi/obat-alkes?id='),
                                'data-conditions' => 'nopemesanan',
                            ]
                        ],
                        'edit-kirim' => [
                            'title' => \Yii::t('fe', 'Edit Kirim'),
                            'icon' => 'fa fa-eye',
                            'method' => '#',
                            'attributes' => [
                                'class' => 'edit-kirim-pemesanan',
                                'data-target' => Url::home().('apotek/transaksi-mutasi/obat-alkes?id='),
                                'data-conditions' => 'nopemesanan',
                            ]
                        ],
                        'detail' => [
                            'title' => 'Lihat',
                            'icon' => 'fa fa-eye',
                            'attributes' => [
                                'url' => "/apotek/inf-pemesanan-obat-alkes/detail?id=",
                            ],
                        ],
                        'cetak-detail' => [
                            'title' => 'Cetak Bukti Distribusi',
                            'icon' => 'fa fa-file-pdf-o',
                            'attributes' => [
                                // 'data-pages' => '_blank',
                                // 'data-target' => Url::home().('apotek/inf-pemesanan-obat-alkes/cetak-detail?id=')
                                'id' => 'cetak-distribusi',
                                'data-options' => 'link',
                                'class' => 'spa',
                            ]
                        ]
                    ],'#example');?>
                </div>
            </div>

            <div class="panel-body">
                <div  class="advanced-filter"></div>
                <!-- <div class="filter-form">

                </div> -->
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="35">No</th>
                            <th><?=\Yii::t("fe", "Tanggal pemesanan");?></th>
                            <th><?=\Yii::t("fe", "No Pemesanan");?></th>
                            <th><?=\Yii::t("fe", "Instalasi - Ruangan Pemesan");?></th>
                            <th><?=\Yii::t("fe", "Status");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Dikirim");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Terima");?></th>
                            <th><?=\Yii::t("fe", "Reference");?></th>
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
    // Global Var
    var table;
    var belum_dikirim = '.DocoConstants::BELUM_DIKIRIM.';
    var belum_diterima = '.DocoConstants::STATUS_BELUM_DITERIMA.';
    var sudah_diterima = '.DocoConstants::STATUS_TERIMA.';
    var batal_pesan = '.DocoConstants::BATAL_PESAN.';
    var pegawai_id = "'.$pegawaiId.'";

    // Event Ready
    $(document).ready(function() {

        $(".kirim-pemesanan").attr("disabled", true);
        $(".edit-kirim-pemesanan").attr("disabled", true);

        // Generate Table
        table = $("#example").docoTabel({
            columnDefs: [ {
                sortable: false,
                className: "select-checkbox",
                targets: 0
            }],
            select: {
                style:    "os",
                selector: "tr"
            },
            filter: true,
            sorting: [[2, "desc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"'.(Yii::$app->controller->module->id).'/inf-pemesanan-obat-alkes/get-data",
            columns: [
                {
                    title: "",
                    data: null,
                    render : function ( data, type, full, meta ) {
                        return null;
                    },
                    searchable: false,
                    sortable: false
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    sortable: false
                },
                {title: "'.(\Yii::t("fe", "Tanggal pemesanan")).'", data: "tglpemesanan"},
                {title: "'.(\Yii::t("fe", "No pemesanan")).'", data: "nopemesanan"},
                {title: "'.(\Yii::t("fe", "Instalasi - Ruangan Pemesan")).'",  data: "ruangan_pemesan", name: "ruangan_pemesan_id"},
                {title: "'.(\Yii::t("fe", "Status")).'",  data: "statusdistribusiobat", name:"status_distribusi"},
                {title: "'.(\Yii::t("fe", "Tanggal Dikirim")).'", data: "tglmutasioa", searchable: false},
                {title: "'.(\Yii::t("fe", "Tanggal Terima")).'", data: "tglterima", searchable: false},
                {title: "'.(\Yii::t("fe", "No. Referensi")).'", data: "reference"}
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table,
            [
                [
                    2,
                    \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'.date("d-M-Y",strtotime('-30 days')).'" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" readonly="true" value="'.date("d-M-Y").'" class="form-control endDate"/><input type="text" style="display:none"  class="targetDate" col-index=2></div>\'
                ],
                [
                    4,
                    \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('ruangan_nama', '',
                            $ruangan,
                            [
                                'class' => 'form-control select2',
                                'prompt' => \Yii::t('fe', 'All'),
                            ]
                        )
                    )).'</div>\'
                ],
                [
                    5,
                    \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('status_distribusi', '',
                            $lookup_status,
                            [
                                'class' => 'form-control select2',
                                'prompt' => \Yii::t('fe', 'All'),
                            ]
                        )
                    )).'</div>\'
                ]
            ], {
            2:0,
            3:1,
            4:2,
            5:3,
            8:4
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

        $(".instalasi").select2({
            placeholder: "",
        })

        $(document).on("click", "#example tr", function(){
            var tbl = table.row(".selected").data();

            if(typeof tbl !== "undefined") {
                if(tbl.status_id == belum_dikirim){
                    $(".kirim-pemesanan").removeAttr("disabled");
                    $(".edit-kirim-pemesanan").attr("disabled", true);
                }

                if(tbl.status_id == belum_diterima){
                    $(".kirim-pemesanan").attr("disabled", true);
                    $(".edit-kirim-pemesanan").removeAttr("disabled");
                }

                if(tbl.status_id == sudah_diterima){
                    $(".kirim-pemesanan").attr("disabled", true);
                    $(".edit-kirim-pemesanan").attr("disabled", true);
                }

                if(tbl.status_id == batal_pesan){
                    $(".kirim-pemesanan").attr("disabled", true);
                    $(".edit-kirim-pemesanan").attr("disabled", true);
                }
            } else{
                $(".kirim-pemesanan").attr("disabled", true);
                $(".edit-kirim-pemesanan").attr("disabled", true);
            }
        });

        $(".nopemesanan").select2({
            placeholder: "",
            minimumInputLength: 2,
            ajax: {
                url: "/apotek/inf-pemesanan-obat-alkes/get-pemesanan",
                dataType: "json",
                quietMillis: 250,
                data: function (term, page) {
                    return {
                        q: term,
                        z: $(".targetDate").val(),
                        page: page
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.result
                    };
                }
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
            templateSelection: function(container) {
                $(container.element).attr("data-id", container.data_ruangan);
                return container.text;
            }
        });

        $(".nopemesanan").change(function() {
            const ruangan_id = $(this).find(":selected").attr("data-id");
            if (typeof ruangan_id !== "undefined") {
                $("#filter_ruangan").val(ruangan_id).trigger("change");
            }
        })

        $("#cetak-distribusi").click(function(e){
            e.preventDefault();
            var tableData = table.row(".selected").data();
            if(typeof tableData !== "undefined") {
                id = tableData.pesanobatalkes_id;
                var url = "/reports/viewer/detail-distribusi?id=" + id + "&uid=" + pegawai_id;
                window.open(url);
            }
        });
    });

', View::POS_END, 'b-index');
?>
