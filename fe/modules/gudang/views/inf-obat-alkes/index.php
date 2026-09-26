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
$this->params['breadcrumbs'][] = ['label' => 'Gudang', 'url' => ['index']];
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
                        'lihat' => [
                            'type' => 'link',
                            'title' => \Yii::t('fe', 'Lihat'),
                            'icon' => 'fa fa-eye',
                            'method' => '#',
                            'attributes' => [
                                'class' => 'data-lihat',                            
                                'data-target' => '/gudang/inf-obat-alkes/view?id=',
                            ] 
                        ],
                        'reset',
                        'delete' => [
                            'attributes' => [
                                'data-additional' => 'data-rm',
                            ]
                        ]
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
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?=$this->title;?></legend>
                    <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th width="1"></th>
                                <th width="70">No</th>
                                <th><?=\Yii::t("fe", "Nama Obat Alkes");?></th>
                                <th><?=\Yii::t("fe", "Jenis Obat Alkes");?></th>
                                <th><?=\Yii::t("fe", "Ven");?></th>
                                <th><?=\Yii::t("fe", "Supplier");?></th>
                                <th><?=\Yii::t("fe", "Harga Netto");?></th>
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
                selector: "tr"
            },
            filter: true,
            sorting: [[2, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"'.(Yii::$app->controller->module->id).'/inf-obat-alkes/get-data",
            columns: [
                {data: null, searchable: false, sortable: false, defaultContent:""},
                {title: "No", data: "rowNum", searchable: false, sortable: false},
                {title: "'.(\Yii::t("fe", "Nama Obat Alkes")).'", data: "obatalkes_nama"},
                {title: "'.(\Yii::t("fe", "Jenis Obat Alkes")).'", data: "jenisobatalkes_nama"},
                {title: "'.(\Yii::t("fe", "Ven")).'",  data: "ven_nama"},
                {title: "'.(\Yii::t("fe", "Supplier")).'", data: "supplier_nama",searchable: false},
                {title: "'.(\Yii::t("fe", "Harga Netto")).'", data: "harganetto", searchable: false, "class":"text-right"},
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table,
        [
            [
                2, 
                \'<div class="input-group">'.(preg_replace("/[\n\t\r]/i", '', 
                    Select2::widget([
                        'name' => 'obatalkes_nama',
                        'options' => ['placeholder' => \Yii::t('fe', 'Nama Obat Alkes'), 'class' => 'obatalkes_nama select2'],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'language' => [
                                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                            ],
                            'ajax' => [
                                'url' => Url::home().(Yii::$app->controller->module->id).'/end-point/get-data-obat-alkes',
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q:params.term}; }')
                            ],
                        ],
                        'pluginEvents' => [
                            'select2:select' => "function(params) { 
                                var jenisobatalkes_id = params.params.data.jenisobatalkes_id;
                                var ven = params.params.data.ven;
                                var supplier_id = params.params.data.supplier_id;
                                var supplier_nama = params.params.data.supplier_nama;

                                console.log(supplier_id);
                                $('.jenisobatalkes_nama').select2().val(jenisobatalkes_id).trigger('change.select2');
                                $('.ven_nama').select2().val(ven).trigger('change.select2');
                                if (params.params.data.supplier_id) {
                                    var opt = new Option(supplier_nama, supplier_id, true, true);

                                    $('.supplier_nama').append(opt).trigger('change');
                                }
                            }",
                        ]
                    ])
                )).'</div>\'
            ],
            [
                4, 
                \''.(preg_replace("/[\n\t\r]/i", '', 
                    Html::dropDownList('ven_nama', '', 
                        ArrayHelper::map($api['ven'], 'lookup_id', 'lookup_name'), 
                        [
                            'class' => 'form-control select2 ven_nama', 
                            'prompt' => \Yii::t('fe', 'Pilih')
                        ]
                    )
                )).'\'
            ],
            [
                3, 
                \''.(preg_replace("/[\n\t\r]/i", '', 
                    Html::dropDownList('jenisobatalkes_nama', '', 
                        ArrayHelper::map($api['jenis_obat'], 'jenisobatalkes_id', 'jenisobatalkes_nama'), 
                        [
                            'class' => 'form-control select2 jenisobatalkes_nama', 
                            'prompt' => \Yii::t('fe', 'Pilih')
                        ]
                    )
                )).'\'
            ],
            [
                5, 
                \'<div class="input-group">'.(preg_replace("/[\n\t\r]/i", '', 
                    Select2::widget([
                        'name' => 'supplier_nama',
                        'options' => ['placeholder' => \Yii::t('fe', 'Supplier'), 'class' => 'form-control supplier_nama select2'],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'language' => [
                                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                            ],
                            'ajax' => [
                                'url' => Url::home().(Yii::$app->controller->module->id).'/end-point/get-data-supplier?assign_id=',
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q:params.term}; }')
                            ],
                        ],
                    ])
                )).'</div>\'
            ]
        ], {
            2:0,
            4:2,
            3:1,
            5:3
        }, true);
    });
', View::POS_END, 'b-index');
?>
