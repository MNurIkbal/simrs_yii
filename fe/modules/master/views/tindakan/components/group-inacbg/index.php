<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-04-27 09:49:54
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-03-26 16:42:02
 */
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = $title;

?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-group-inacbg'
                            ]
                        ],
                        'create' => [
                            'title' => \Yii::t('fe', 'Tambah'),
                            'icon' => 'fa fa-plus',
                            'attributes'=>[
                                'class' => 'spa',
                                'data-options' => 'click',
                                'data-render' => 'create-group-inacbg',
                                'data-tab' => 'tab-group-inacbg',
                                'data-target' => '#view-group-inacbg',
                                'id' => 'btn-create-group-inacbg'
                            ]
                        ],
                        'update' => [
                            'title' => \Yii::t('fe', 'Ubah'),
                            'icon' => 'fa fa-edit',
                            'attributes'=>[
                                'class' => 'spa',
                                'data-options'=>'click',
                                'data-render' => 'update-group-inacbg?id=',
                                'data-tab' => 'tab-group-inacbg',
                                'data-target' => '#view-group-inacbg',
                                'data-type' => 'wp'
                            ]
                        ],
                        // 'delete' => [
                        //     'attributes' => [
                        //         'data-additional' => 'data-rm',
                        //         'data-target' => '/master/tindakan/delete-group-inacbg?id='
                        //     ]
                        // ],
                        'pdf' => [
                            'attributes' => [
                                'data-target' => '/master/tindakan/export-pdf-group-inacbg?'
                            ]
                        ],
                        'excel' => [
                            'attributes' => [
                                'data-target' => '/master/tindakan/export-excel-group-inacbg?'
                            ]
                        ],
                    ],'#table-group-inacbg');?>    
            
            </div>
            <div class="panel-body">
                <div class="tab-group-inacbg"></div>
                <table id="table-group-inacbg" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1"><?=\Yii::t("fe", "No");?></th>
                            <th><?=\Yii::t("fe", "Detail");?></th>
                            <th><?=\Yii::t("fe", "Kode Group INA CBGS");?></th>
                            <th><?=\Yii::t("fe", "Nama Group INA CBGS");?></th>
                            <th><?=\Yii::t("fe", "Nama lainnya");?></th>
                            <th><?=\Yii::t("fe", "Status");?></th>
                            <th><?=\Yii::t("fe", "Catatan");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="6"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
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
    var tableGroupInacbg;

    // Event Ready
    $(document).ready(function() {
        $(document).on("keydown", null, "alt+t", function (event) {
            $("#btn-create-group-inacbg").click();
        });
        
        generateFilter("tab-group-inacbg", "filter-group-inacbg");
        // Generate Table
        tableGroupInacbg = $("#table-group-inacbg").docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets:   0
            }],
            select: {
                style:    "os",
                selector: "td:first-child"
            },
            sorting: [[4, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: "/master/tindakan/get-data-group-inacbg",
            columns: [
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    defaultContent: "",
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Detail")).'", 
                    data: "detail", 
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "Kode Group INA CBGS")).'", data: "groupinacbg_kode"},
                {title: "'.(\Yii::t("fe", "Nama Group INA CBGS")).'", data: "groupinacbg_nama"},
                {title: "'.(\Yii::t("fe", "Nama lainnya")).'",  data: "groupinacbg_namalainnya"},
                {title: "'.(\Yii::t("fe", "Status")).'", data: "status", name: "is_active"},
                {title: "'.(\Yii::t("fe", "Catatan")).'", data: "catatan"},
            ],
            // scrollCollapse: true,
            // fixedColumns: {
            //     leftColumns: 2,
            // }
        });
        $(".dataTables_filter").hide();
        $(".filter-group-inacbg").datatableBootstrapFilter(tableGroupInacbg, [
            [
                6, 
                \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $status, ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Pilih')]))).'\'
            ]
        ],
        {
            3:0,
            4:1,
            5:2,
            7:3,
            6:4,
        }, true);
    });
    ',VIEW::POS_END);
?>