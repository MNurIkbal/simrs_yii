<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;
use app\components\DocoHelpers;

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
                        'reset' => [
                            'attributes' => [
                                'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-reset',
                                'data-table-id' => 'example',
                                'data-options' => 'click',
                            ]
                        ],
                        'payment' => [
                            'type' => 'link',
                            'title' => \Yii::t('fe', 'Pembayaran'),
                            'icon' => 'fa fa-shopping-cart',
                            'method' => 'not exist',
                            'attributes' => [
                                'class' => 'data-payment',
                                'data-target' => Url::home().(Yii::$app->controller->module->id).'/inf-penjualan-obat-alkes/view?id=',
                                'data-conditions' => 'tipe'
                            ]
                        ],
                    ],'#example');?>
                </div>
            </div>

            <div class="panel-body">
                <div class="advanced-filter">
                </div>

                 <div class="col-md-6">
                    <div class='legend-index'>
                        <div class='legend-header'>Keterangan</div>
                        <div class="legend-wrapper">
                            <div class="legend-information btn-is_kronis" data-group="keterangan" data-type="is_kronis" data-val="1">
                                <div class="legend-information__color" style="background-color: #E0B8FF"></div>
                                <div class="legend-information__text">Kronis</div>
                                <input type="hidden" class="filter-is_kronis" id="is_kronis" value="0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class='legend-index'>
                        <div class='legend-header'>Keterangan Cara Bayar</div>
                        <div class="legend-wrapper">
                            <?php foreach($getCaraBayar as $key=>$value): ?>
                                <div class="legend-information">
                                    <div class="legend-information__color" style="background-color: <?= $value['carabayar_kode_warna']; ?>"></div>
                                    <div class="legend-information__text"><?= $value['carabayar_nama']; ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th width="70">No</th>
                            <th><?=\Yii::t("fe", "Tanggal penjualan");?></th>
                            <th><?=\Yii::t("fe", "No resep");?></th>
                            <th><?=\Yii::t("fe", "Jenis Penjualan");?></th>
                            <th><?=\Yii::t("fe", "No Pendaftaran");?></th>
                            <th><?=\Yii::t("fe", "No Rekam Medik");?></th>
                            <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                            <th><?=\Yii::t("fe", "Cara Bayar / Penjamin");?></th>
                            <th><?=\Yii::t("fe", "Penjamin");?></th>
                            <th><?=\Yii::t("fe", "Jumlah Retur");?></th>
                            <th><?=\Yii::t("fe", "Cara Bayar");?></th>
                            <th></th>
                            <!--th><?=\Yii::t("fe", "Pembayaran");?></th-->
                            <!--th><?=\Yii::t("fe", "Batal");?></th-->
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="11"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
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
    var tableUrl = baseUrl+"'.(Yii::$app->controller->module->id).'/inf-penjualan-obat-alkes/get-data";

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        table = $("#example").docoTabel({
            info: false,
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
            ajax: tableUrl,
            columns: [
                {
                    data: null,
                    searchable: false,
                    sortable: false,
                    defaultContent:""
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    sortable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Tanggal penjualan")).'",
                    data: "tglpenjualan"
                },
                {
                    title: "'.(\Yii::t("fe", "Jenis Penjualan")).'",
                    data: "jenis_penjualan",
                    name: "jenispenjualan"
                },
                {
                    title: "'.(\Yii::t("fe", "No resep")).'",
                    data: "noresep"
                },
                {
                    title: "'.(\Yii::t("fe", "No Pendaftaran")).'",
                    data: "no_pendaftaran"
                },
                {
                    title: "'.(\Yii::t("fe", "No Rekam Medik")).'",
                    data: "no_rekam_medik",
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Pasien")).'",
                    data: "nama_pembeli",
                },
                {
                    title: "'.(\Yii::t("fe", "Cara Bayar / Penjamin")).'",
                    data: "carabayar_nama",
                    render: (col, type, data) => {
                        return data.carabayar_nama+" / "+data.penjamin_nama
                    },
                    searchable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Penjamin")).'",
                    data: "penjamin_nama",
                    className: "hidden"
                },
                {
                    title: "'.(\Yii::t("fe", "Jumlah pembayaran")).'",
                    data: "totalharga_jual",
                    searchable: false,
                    class :"text-right"
                },
                {
                    title: "'.(\Yii::t("fe", "Cara Bayar")).'",
                    data: "carabayar_nama",
                    className: "hidden"
                },
                {
                    data: "is_kronis",
                    className: "hidden",
                    searchable: false
                },
            ],
            rowCallback: function(row, data, index) {
                if (data.carabayar_kode_warna != null) {
                    $($(row).find("td")[8]).css("background-color", data.carabayar_kode_warna);
                    $($(row).find("td")[8]).css("color", invertColor(data.carabayar_kode_warna, true));
                }
                if (data.is_kronis == true) {
                    $($(row).find("td")[4]).css("background-color", "#E0B8FF");
                }
            }
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table,
            [
                [
                    2,
                    \'<div class="input-group"><input type="text" value="'.date('d-M-Y').'" id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" value="'.date('d-M-Y').'" id="rangeDemoFinish" readonly="readonly" class="form-control endDate"/><input type="text" style="display:none" class="targetDate"></div>\'
                ],[
                    3,
                    \''.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('jenis_pejualan', '', $jenis_penjualan,
                            [
                                'class' => 'form-control select2',
                                'prompt' => \Yii::t('fe', 'Jenis Penjualan'),
                            ]
                        )
                    )).'\'
                ],[
                    11,
                    \''.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('carabayar_nama', '',
                            ArrayHelper::map($cara_bayar, 'carabayar_nama', 'carabayar_nama'),
                            [
                                'id' => 'filter_carabayar',
                                'class' => 'form-control select2 dep-to-child',
                                'prompt' => \Yii::t('fe', '--Cara bayar--'),
                                'data-url' =>  Url::home().(Yii::$app->controller->module->id).'/end-point/get-penjamin',
                                'data-depend_id' => 'filter_penjamin',
                                'data-depend_prompt' => \Yii::t('fe', '--Penjamin--'),
                                'data-storage' => 'penjamin',
                                'data-key' => 'penjamin_nama',
                            ]
                        )
                    )).'\'
                ], [
                    9,
                    \''.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('penjamin_nama', '',
                            ArrayHelper::map($penjamin, 'penjamin_nama', 'penjamin_nama'),
                            [
                                'id' => 'filter_penjamin',
                                'class' => 'form-control select2 dep-to-parent',
                                'prompt' => \Yii::t('fe', '--Penjamin--'),
                                'data-url' =>  Url::home().(Yii::$app->controller->module->id).'/end-point/get-carabayar',
                                'data-depend_id' => 'filter_carabayar',
                            ]
                        )
                    )).'\'
                ]
            ], {
                2:0,
                6:1,
                7:2,
                5:3, 
                11:8
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

        $(".no_pendaftaran").select2({
            placeholder: null,
            minimumInputLength: 2,
            ajax: {
                url: "/kasir/end-point/get-data-no-pen",
                dataType: "json",
                quietMillis: 250,
                data: function (term, page) {
                    var _tanggal  = $(".targetDate").val();
                    return {
                        q : term.term,
                        tanggal : _tanggal
                    };
                },
                processResults: function (data) {
                  return {
                    results: data.results
                  };
                }
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        });

        $(".no_resep").select2({
            placeholder: null,
            minimumInputLength: 2,
            ajax: {
                url: "/kasir/end-point/get-data-resep",
                dataType: "json",
                quietMillis: 250,
                data: function (term, page) {
                    var _tanggal  = $(".targetDate").val();
                    return {
                        q : term.term,
                        tanggal : _tanggal
                    };
                },
                processResults: function (data) {
                  return {
                    results: data.results
                  };
                }
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        });

        $(document).on("click", "#example tbody tr", function(){
            var pendaftaran_id = null;
            try {
                pendaftaran_id = table.row(".selected").data().pendaftaran_id;
            }
            catch(e) {
                pendaftaran_id = null;
            }
        });

        $(document).on("click", ".btn-reset", function (e) {
            const tableId = "example";
            const tableElement = $(`#${tableId}`).DataTable();

            $(".btn-is_kronis").css("border", "1px solid #dddddd");
            $(".btn-is_kronis").removeClass("active");
            $("#is_kronis").val("0").trigger("change");
            $(".filter-form").find("input").val("");
            $(".filter-form").find("select").val(null).trigger("change");
            $("#rangeDemoStart").val(moment().format("DD-MMM-YYYY")).trigger("change");
            $("#rangeDemoFinish").val(moment().format("DD-MMM-YYYY")).trigger("change");
            tableElement.search("").columns().search("");

            tableElement.ajax.url(tableUrl);
            tableElement.ajax.reload();
            showLoader();
        });

        var _legend_info = ".btn-is_kronis";

        $(_legend_info).css("cursor", "pointer");
        $(_legend_info).removeClass("active");
        $(_legend_info).on("click", function () {
            showLoader();
            var _dt_group = $(this).data("group");
            var _dt_type = $(this).data("type");
            var _adv_filter = [];

            if (_dt_group == "keterangan") {
                if ($(this).hasClass("active") === true) {
                    $(this).removeClass("active");
                    $(this).css("border", "1px solid #dddddd");
                } else {
                    $(".legend-information[data-group=\'keterangan\'].active")
                        .removeClass("active")
                        .css("border", "1px solid #dddddd");

                    $(this).addClass("active");
                    $(this).css("border", "2px solid #2ca38b");

                    var _dt_val = $(this).data("val");
                    var _filter_keterangan = `advancedFilter%5B${_dt_type}%5D=${_dt_val}`;

                    _adv_filter.push(_filter_keterangan);
                }
            }

            _table_ajax_url = tableUrl + "?" + _adv_filter.join("&");
            table.ajax.url(_table_ajax_url).load();
        });

    });

', View::POS_END, 'b-index');
?>
