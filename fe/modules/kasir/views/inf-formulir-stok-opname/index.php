<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;

$this->title = \Yii::t('fe', 'Informasi Formulir Stok Opname');
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
                    <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset',
                        'detail'
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
                                <th><?=\Yii::t("fe", "Tanggal formulir");?></th>
                                <th><?=\Yii::t("fe", "No formulir");?></th>
                                <!--th width="1"><?=\Yii::t("fe", "Detail");?></th-->
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" colspan="4"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
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
<div id="modal_backdrop_full" class="modal fade" style="z-index:1065;" data-backdrop="static">
    <div class="modal-dialog modal-full">
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
            ajax: baseUrl+"'.(Yii::$app->controller->module->id).'/inf-formulir-stok-opname/get-data",
            columns: [
                {data: null, searchable: false, sortable: false, defaultContent:""},
                {title: "No", data: "rowNum", searchable: false, sortable: false},
                {title: "'.(\Yii::t("fe", "Tanggal formulir")).'", data: "tglformulir"},
                {title: "'.(\Yii::t("fe", "No formulir")).'", data: "noformulir"},
                // {
                    // title: "'.(\Yii::t("fe", "Detail")).'",
                    // data: "detail",
                    // searchable: false,
                    // sortable: false,
                    // class: "text-center"
                // }
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, 
            [
                [
                    2, 
                    \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate"/><input type="text" style="display:none" class="targetDate"></div>\'
                ], [
                    3, 
                    \'<div class="input-group">'.(preg_replace("/[\n\t\r]/i", '', 
                        Select2::widget([
                            'name' => 'noformulir',
                            'options' => ['-placeholder' => \Yii::t('fe', 'No formulir'), 'class' => 'noformulir'],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'minimumInputLength' => 3,
                                'language' => [
                                    'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                                ],
                                'ajax' => [
                                    'url' => Url::home().(Yii::$app->controller->module->id).'/end-point/get-data-formulir-stok-opname?assign_id=',
                                    'dataType' => 'json',
                                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                ],
                                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                'templateResult' => new JsExpression('function(city) { return city.text; }'),
                                'templateSelection' => new JsExpression('function (city) { return city.text; }'),
                            ],
                        ])
                    )).'<span class="input-group-addon"><span class="cursor-pointer" action="'.Url::home().(Yii::$app->controller->module->id).'/modal-search/modal-formulir-stok-opname" data-toggle="modal" data-target="#modal_backdrop_search"><i class="fa fa-list"></i> <i class="fa fa-search"></i></span></span></div>\'
                ]
            ], {
                2:1,
                3:5
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

        $(".input-daterange input").each(function() {
            $(this).datepicker("clearDates");
        });
    });
', View::POS_END, 'b-index');
?>
