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

$this->title = \Yii::t('fe', 'Informasi Stok Obat Alkes');
$this->params['breadcrumbs'][] = ['label' => 'Kasir', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
                <?=Breadcrumbs::widget([
                    'homeLink' => [ 
                        'label' => Yii::t('yii', 'Home'),
                        'url' => Yii::$app->homeUrl,
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-toolbar clearfix">
                <div class="btn-group pull-left">
                    <?=DocoHelpers::generateToolbar();?>
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
                                <th><?=\Yii::t("fe", "Periode Stok");?></th>
                                <th><?=\Yii::t("fe", "Nama obat alkes");?></th>
                                <th><?=\Yii::t("fe", "Instalasi akhir");?></th>
                                <th><?=\Yii::t("fe", "Ruangan akhir");?></th>
                                <th><?=\Yii::t("fe", "Qty masuk");?></th>
                                <th><?=\Yii::t("fe", "Qty keluar");?></th>
                                <th><?=\Yii::t("fe", "Qty dipesan");?></th>
                                <th><?=\Yii::t("fe", "Qty tersedia");?></th>
                                <th><?=\Yii::t("fe", "Stok");?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" colspan="11"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
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
            ajax: baseUrl+"'.(Yii::$app->controller->module->id).'/inf-stok-obat-alkes/get-data",
            columns: [
                {data: null, searchable: false, sortable: false, defaultContent:""},
                {title: "No", data: "rowNum", searchable: false, sortable: false},
                {title: "'.(\Yii::t("fe", "Periode Stok")).'", data: "periodestok_nama"},
                {title: "'.(\Yii::t("fe", "Nama obat alkes")).'", data: "obatalkes_namalain"},
                {title: "'.(\Yii::t("fe", "Instalasi akhir")).'",  data: "instalasi_nama"},
                {title: "'.(\Yii::t("fe", "Ruangan akhir")).'", data: "ruangan_nama"},
                {title: "'.(\Yii::t("fe", "Qty masuk")).'", data: "qty_masuk", searchable: false, "class":"text-right"},
                {title: "'.(\Yii::t("fe", "Qty keluar")).'", data: "qty_keluar", searchable: false, "class":"text-right"},
                {title: "'.(\Yii::t("fe", "Qty dipesan")).'", data: "qty_dipesan", searchable: false, "class":"text-right"},
                {title: "'.(\Yii::t("fe", "Qty tersedia")).'", data: "qty_tersedia", searchable: false, "class":"text-right"},
                {title: "'.(\Yii::t("fe", "Stok")).'", data: "qty_stok", searchable: false, "class":"text-right"},
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, 
            [
                [
                    2, 
                    \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate"/><input type="text" style="display:none" class="targetDate"></div>\'
                ], [
                    4, 
                    \''.(preg_replace("/[\n\t\r]/i", '', 
                        Html::dropDownList('instalasi_nama', '', 
                            ArrayHelper::map($instalasi, 'instalasi_nama', 'instalasi_nama'), 
                            [
                                'id' => 'filter_instalasi', 
                                'class' => 'form-control no-select2', 
                                'prompt' => \Yii::t('fe', 'Instalasi akhir')
                            ]
                        )
                    )).'\'
                ], [
                    5, 
                    \''.(preg_replace("/[\n\t\r]/i", '', 
                        DepDrop::widget([
                            'name' => 'ruangan_nama',
                            'options' => [
                                'disabled' => false,
                                'class' => 'form-control no-select2'
                            ],
                            'pluginOptions' => [
                               'depends'  => ['filter_instalasi'],
                               '-placeholder' => \Yii::t('fe', 'Ruangan akhir'),
                               'url' => Url::home().(Yii::$app->controller->module->id).'/end-point/get-ruangan'
                            ]
                        ])
                    )).'\'
                ], [
                    3, 
                    \'<div class="input-group">'.(preg_replace("/[\n\t\r]/i", '', 
                        Select2::widget([
                            'name' => 'obatalkes_namalain',
                            'options' => ['-placeholder' => \Yii::t('fe', 'Nama obat alkes'), 'class' => 'obatalkes_namalain'],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'minimumInputLength' => 3,
                                'language' => [
                                    'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                                ],
                                'ajax' => [
                                    'url' => Url::home().(Yii::$app->controller->module->id).'/end-point/get-data-obat-alkes?assign_id=',
                                    'dataType' => 'json',
                                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                ],
                                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                'templateResult' => new JsExpression('function(city) { return city.text; }'),
                                'templateSelection' => new JsExpression('function (city) { return city.text; }'),
                            ],
                        ])
                    )).'<span class="input-group-addon"><span class="cursor-pointer" action="'.Url::home().(Yii::$app->controller->module->id).'/modal-search/modal-stok-obat-alkes" data-toggle="modal" data-target="#modal_backdrop_search"><i class="fa fa-list"></i> <i class="fa fa-search"></i></span></span></div>\'
                ]
            ], {
                2:1,
                3:5,
                4:2,
                5:4
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
        
        table.row(0).column(0).visible(false);
    });
', View::POS_END, 'b-index');
?>
