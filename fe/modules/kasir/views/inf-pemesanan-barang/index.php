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

$this->title = \Yii::t('fe', 'Informasi Pemesanan Barang');
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
                        'lihat' => [
                            'type' => 'link',
                            'title' => \Yii::t('fe', 'Lihat'),
                            'icon' => 'fa fa-eye',
                            'method' => '#',
                            'attributes' => [
                                'class' => 'data-lihat',
                                'data-target' => Url::home().('kasir/inf-pemesanan-barang/detail?id='),
                            ]
                        ],
                        'mutasi' => [
                            'type' => 'link',
                            'title' => \Yii::t('fe', 'Mutasi Barang'),
                            'icon' => 'fa fa-check',
                            'method' => '#',
                            'attributes' => [
                                'class' => 'data-mutasi',
                                'data-target' => Url::home().('kasir/inf-pemesanan-barang/mutasi?id='),
                            ]
                        ],
                    ]);?>
                </div>
                <div class="btn-group pull-right">
                    <?=DocoHelpers::generateToolbar([
                        'reset',
                    ]);?>
                </div>
            </div>

            <div class="panel-body">
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?=Yii::t('fe', 'Pencarian');?></legend>
                    <div class="row">
                        <div class="col-md-12 filter-form"></div>
                    </div>
                </fieldset>
                <br />

                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?=$this->title;?></legend>
                    <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th width="1"></th>
                                <th width="70">No</th>
                                <th><?=\Yii::t("fe", "Tanggal Pemesanan");?></th>
                                <th><?=\Yii::t("fe", "No Pemesanan");?></th>
                                <th><?=\Yii::t("fe", "Instalasi Tujuan");?></th>
                                <th><?=\Yii::t("fe", "Ruangan Tujuan");?></th>
                                <th><?=\Yii::t("fe", "Status Pemesanan");?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" colspan="7"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
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

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        table = $("#example").docoTabel({
            columnDefs: [ {
                sortable: false,
                className: "select-checkbox",
                targets:   0
            }],
            select: {
                style:    "os",
                selector: "td:first-child"
            },
            filter: true,
            sorting: [[2, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"'.(Yii::$app->controller->module->id).'/inf-pemesanan-barang/get-data",
            columns: [
                {data: null, searchable: false, sortable: false, defaultContent:""},
                {title: "No", data: "rowNum", searchable: false, sortable: false},
                {title: "'.(\Yii::t("fe", "Tanggal pemesanan")).'", data: "tgl_pesanbarang"},
                {title: "'.(\Yii::t("fe", "No pemesanan")).'", data: "no_pemesanan"},
                {title: "'.(\Yii::t("fe", "Instalasi Tujuan Pemesanan")).'",  data: "instalasi_tujuan"},
                {title: "'.(\Yii::t("fe", "Ruangan Tujuan Pemesanan")).'", data: "ruangan_tujuan"},
                {title: "'.(\Yii::t("fe", "Status pemesanan")).'", data: "status_pesan", searchable: false, "class":"text-center"},
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table,
            [
                [
                    2,
                    \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate" placeholder="Periode Awal"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate" placeholder="Periode Akhir"/><input type="text" style="display:none" class="targetDate"></div>\'
                ],
                [
                    4,
                    \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('instalasi_tujuan', '',
                            ArrayHelper::map($api['response']['instalasi'], 'instalasi_id', 'instalasi_nama'),
                            [
                                'id' => 'filter_instalasi',
                                'class' => 'form-control select2',
                                'prompt' => \Yii::t('fe', 'Pilih Instalasi')
                            ]
                        )
                    )).'</div>\'
                ],
                [
                    5,
                    \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                        DepDrop::widget([
                            'name' => 'ruangan_tujuan',
                            'data' => ['' => \Yii::t('fe', 'Pilih Ruangan')],
                            'options' => [
                                'disabled' => false,
                                'class' => 'form-control select2'
                            ],
                            'pluginOptions' => [
                               'depends'  => ['filter_instalasi'],
                               'placeholder' => \Yii::t('fe', 'Pilih Ruangan'),
                               'url' =>'/kasir/inf-pemesanan-barang/get-ruangan',
                            ]
                        ])
                    )).'</div>\'
                ],
                [
                    3,
                    \'<div class="input-group">'.(preg_replace("/[\n\t\r]/i", '',
                        Select2::widget([
                            'name' => 'no_pemesanan',
                            'options' => ['placeholder' => \Yii::t('fe', 'No Pemesanan'), 'class' => 'no_pemesanan'],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'minimumInputLength' => 3,
                                'language' => [
                                    'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                                ],
                                'ajax' => [
                                    'url' => '/kasir/inf-pemesanan-barang/get-nopemesanan',
                                    'dataType' => 'json',
                                    'data' => new JsExpression('function(params) {
                                     return {q:params.term}; }')
                                ],
                                'processResults' => 'function (data, params) {
                                    console.log(data)
                                }',
                                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                'templateResult' => new JsExpression('function(city) { return city.text; }'),
                                'templateSelection' => new JsExpression('function (city) { return city.text; }'),
                            ],
                        ])
                    )).'<span class="input-group-addon"><span class="cursor-pointer" action="'.Url::home().(Yii::$app->controller->module->id).'/modal-search/modal-pesan-barang" data-toggle="modal" data-target="#modal_backdrop_search"><i class="fa fa-list"></i> <i class="fa fa-search"></i></span></span></div>\'
                ]

            ], {}, true
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
    });

    $(document).on("click", "#example tr", function(){
        var tbl = table.row(".selected").data();
        if(tbl.status_pesan == "Sudah Dikirim" || tbl.status_pesan == "Diterima"){
            $(".data-mutasi").hide();
        }else{
            $(".data-mutasi").show();
        }
    });

    $(document).on("click", ".data-cancel", function(event) {
        event.preventDefault();
        var tableData = table.row(".selected").data();
        if (typeof tableData !== "undefined") {
            if ("primary" in tableData) {
                var primary = tableData.primary;
                var target = $(this).data("target");
                $(this).attr("action", target+primary);
                // console.log($(this).attr("action"));
                $(this).docoForm("delete",{
                    additional: "data-rm",
                    success : function (data) {
                        table.draw();
                    }
                });
            }
        }
    });
', View::POS_END, 'b-index');
?>
