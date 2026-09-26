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

$this->title = Yii::t('fe', 'Informasi Ambulan');
$this->params['breadcrumbs'][] = ['label' => 'Ambulan', 'url' => ['index']];
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
                    'detail' => [
                        // 'title' => \Yii::t('fe', 'History'),
                        'attributes' => [
                            'data-width' => '96%',
                            'data-options' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'data-url' => Url::home().('ambulan/informasi-ambulan/detail?id=')
                        ]
                    ],
                    "excel"=> [
                        'attributes'=>[
                            'id' => 'excel-data'
                        ]
                    ],
                    /*"pdf"=> [
                        'attributes'=>[
                            'id' => 'pdf-data'
                        ]
                    ],*/
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
                            <th ><?= Yii::t("fe", "No") ?></th>
                            <th ><?= Yii::t("fe", "Nomor Polisi") ?></th>
                            <th ><?= Yii::t("fe", "Jenis Ambulan") ?></th>
                            <th ><?= Yii::t("fe", "Status Ambulan") ?></th>
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
    $(document).on("keydown", null, function (e) {
        if (e.key == "Enter") {
            $("#find-data").click();
        }

        if (e.key == "F7") {
            $("#reset-data").click();
        }

        if (e.key == "F8") {
            $("#excel-data").click();
        }

        if (e.key == "F9") {
            $("#pdf-data").click();
        }
    });

    var table;
    $(document).ready(function() {
        table = $("#table-base-price").docoTabel({
            filter: true,
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets:   0
            }],
            select: {
                style:    "os",
                selector: "tr"
            },
            sorting: [[2, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"ambulan/informasi-ambulan/get-data",
            columns: [
                {
                    title: "", 
                    data: null, 
                    defaultContent: "", 
                    searchable: false, 
                    orderable: false,
                    width: "10%"
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Nomor Polisi")).'",  
                    data: "no_polisi",
                },
                {
                     title: "'.(\Yii::t("fe", "Jenis Ambulan")).'",  
                    data: "jenis_ambulan",
                    name: "is_emergency",
                },
                {
                     title: "'.(\Yii::t("fe", "Status Ambulan")).'",
                    data: "status_ambulan",
                    name: "status_ambulan_id",
                }
            ],
        });

        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                2, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('no_polisi', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'No Polisi')]))).'\'
            ],
            [
                3, 
                \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('jenis_ambulan', '', $jenis_ambulan, ['class' => 'form-control select2', 
                'prompt' => \Yii::t('fe', '— Pilih Jenis Ambulan —')]))).'\'
            ],
            [
                4, 
                \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('status_ambulan', '', $status_ambulan, ['class' => 'form-control select2', 
                'prompt' => \Yii::t('fe', '— Pilih Status Ambulan —')]))).'\'
            ],
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
    });

', View::POS_END, 'index');

?>