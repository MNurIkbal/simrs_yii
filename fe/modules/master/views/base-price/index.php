<?php

/**
 * @Author: Sigit
 * @Date:   2018-11-28 17:21:58
 */

use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;

use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\bootstrap\Modal;
use yii\web\JsExpression;

$this->title = Yii::t('fe', 'Base Price Obat');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                        <div class="column-1">
                            <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                        </div>
                        <div class="column-2">
                            <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title) ?></b></h3>
                            <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params["breadcrumbs"])) ?>
                        </div>
                </div>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    "search"=> [
                        'attributes'=>[
                            'id' => 'find-data',
                        ]
                    ],
                    'reset'=> [
                        'attributes'=>[
                            'id' => 'reset-data',
                            'data-parent' => '.filter-form'
                        ]
                    ],
                    'edit' => [
                        'attributes' => [
                            'style' => $roleUbahBtn,
                            'id' => 'edit-base-price',
                            'data-options' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'data-url' => '/master/base-price/edit?id=',
                            'data-width' => '50%',
                        ]
                    ],
                    'detail' => [
                        'title' => \Yii::t('fe', 'History'),
                        'attributes' => [
                            'style' => $roleDetailHistoryBtn,
                            'data-options' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'data-url' => Url::home().('master/base-price/detail?id=')
                        ]
                    ],
                    /*"pdf"=> [
                        'attributes'=>[
                            'id' => 'pdf-data'
                        ]
                    ],*/
                    "excel"=> [
                        'attributes'=>[
                            'id' => 'excel-data'
                        ]
                    ],
                    "export" => [
                        'title' => \Yii::t('fe', 'Export Template Data'),
                        'attributes'=>[
                            'style' => $roleExportTemplateBtn,
                            'id' => 'export-download-data',
                            'data-options' => 'click',
                        ]
                    ],
                    'import' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Import Data'),
                        'icon' => 'fa fa-upload"',
                        'attributes' => [
                            'style' => $roleImportBtn,
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'action' => Url::home().('master/base-price/import-data'),
                            'data-width' => '90%',
                        ]
                    ],
                ], "#table-base-price"); 
                ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="table-base-price" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th style="width: 10%;"><?= Yii::t("fe", "No") ?></th>
                            <th style="width: 20%;"><?= Yii::t("fe", "Jenis Obat") ?></th>
                            <th style="width: 20%;"><?= Yii::t("fe", "Kode Obat") ?></th>
                            <th style="width: 20%;"><?= Yii::t("fe", "Nama Obat") ?></th>
                            <th style="width: 20%;"><?= Yii::t("fe", "Harga Dasar Yang digunakan").' (Rp.) ' ?></th>
                            <!-- <th style="width: 35%;"><?= Yii::t("fe", "Harga Jual") ?></th> -->
                            <th style="width: 20%;"><?= Yii::t("fe", "Satuan Kecil") ?></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    $(document).on("keydown", null, "enter", function (event) {
        $("#find-data").click();
    });

    $(document).on("keydown", null, function (e) {
        if (e.key == "Enter") {
            $("#find-data").click();
        }

        if (e.key == "F7") {
            $("#reset-data").click();
        }

        if (e.key == "F8") {
            $("#pdf-data").click();
        }

        if (e.key == "F9") {
            $("#excel-data").click();
        }
    });


    var table;
    $(document).ready(function() {
        table = $("#table-base-price").docoTabel({
            filter: true,
            sorting: [[2, "asc"], [3, "asc"]],
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets: 0
            }, {
                targets: 5,
                render: function(data, type, row) {
                    if(data != null) {
                        return docoHelper.convertToRupiah(data)
                    } else {
                        return data;
                    }
                }
            }],
            select: {
                style:    "os",
                selector: "tr"
            },
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"master/base-price/get-data",
            columns: [
                {
                    title: "", 
                    data: null, 
                    defaultContent: "", 
                    searchable: false, 
                    orderable: false,
                    width: "5%"
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Jenis Obat")).'",  
                    data: "jenisobatalkes_nama",
                },
                {
                    title: "'.(\Yii::t("fe", "Kode Obat")).'",  
                    data: "obatalkes_kode",
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Obat")).'",  
                    data: "obatalkes_nama",
                },
                {
                    title: "'.(\Yii::t("fe", "Harga Dasar Yang Digunakan ")).' (Rp.) '.'",
                    data: "harganetto_ygdipakai",
                    searchable: false,
                    class : "text-right"
                },
                {
                    title: "'.(\Yii::t("fe", "Satuan Kecil")).'",  
                    data: "satuankecil_nama",
                }
            ],
        });

        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                2, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('jenisobatalkes_nama', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'Jenis Obat')]))).'\'
            ],
            [
                4, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('obatalkes_nama', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'Nama Obat')]))).'\'
            ],
            [
                6, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('satuankecil_nama', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'Satuan Kecil')]))).'\'
            ]
        ]);

        $("#table-base-price tbody").on("click", "tr", function(){
            try {
                primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
            } catch (e) {
                primaryKey = false;
            }

            if (primaryKey) {
                $("#edit-base-price").attr("action",$("#edit-base-price").data("url")+primaryKey);
            } else {
                $("#edit-base-price").removeAttr("action");
            }
        });

        $(document).on("click", ".data-export-download", function(e){
            e.preventDefault();
            window.open(baseUrl+"'.(Yii::$app->controller->module->id).'/base-price/export-download-excel?"+$.param(table.ajax.params()));
            return false;
        });

    });

', View::POS_END, 'index');

?>
