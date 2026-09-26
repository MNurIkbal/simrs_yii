<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
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
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'search',
                    'reset'=> [
                        'attributes'=>[
                            'data-parent' => '.filter-form'
                        ]
                    ],
                    'add' => [
                        'attributes' => [
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'action' => '/master/service-category/create',
                        ]
                    ],
                    'edit' => [
                        'attributes' => [
                            'id' => 'btn-edit',
                            'disabled' => 'disabled',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop'
                        ]
                    ],
                    'delete'=>[
                        'attributes' => [
                            'data-additional' => 'data-sg',
                        ]
                    ],
                ], '#service-category') ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="service-category" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th width="1"><?= Yii::t('fe', 'No') ?></th>
                            <th><?= Yii::t("fe", "Nama") ?></th>
                            <th><?= Yii::t("fe", "Flag Obat") ?></th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    var table;
    var updateUrl = "/master/service-category/update?id=";

    $(document).ready(function() {
        table = $("#service-category").docoTabel({
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
            ajax: baseUrl+"master/service-category/get-data",
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
                    title: "'.(\Yii::t("fe", "Nama")).'",
                    data: "servicecategory_nama",
                },
                {
                    title: "'.(\Yii::t("fe", "Flag Obat")).'",
                    data: "is_obat",
                    searchable: false,
                    class: "text-center",
                    width: "20%"
                }
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table,[
            [
                2, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('servicecategory_nama', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'Nama')]))).'\'
            ]
        ]);

        $(document).on("click", "#service-category tbody tr", function() {
            try {
                primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
            } catch (e) {
                primaryKey = false;
            }

            $("#btn-edit").attr("action", updateUrl + primaryKey);

            // Check class selected
            if ($("#service-category tr.selected").length == 0) {
                $("#btn-edit").prop("disabled", true);
            }
            else {
                $("#btn-edit").prop("disabled", false);
            }
        });

    });

', View::POS_END, 'b-index');
