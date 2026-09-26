<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = \Yii::t('fe', 'Unit Pelaksana Teknis');
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias", $this->title); ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
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
                    'search',
                    'reset' => [
                        'attributes' => [
                            'data-parent' => '.filter-form'
                        ]
                    ],
                ], '#table-unit-pelaksana-teknis'); ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="table-unit-pelaksana-teknis" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?= \Yii::t("fe", "Sync ID"); ?></th>
                            <th><?= \Yii::t("fe", "Nama UPT"); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="5"><?= \Yii::t("fe", "Data tidak ditemukan."); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
var table;
var tableUnitPelaksanaTeknis;

$(document).on("click", ".data-reload", function() {
    table.draw();
});

$(document).ready(function() {
    table = tableUnitPelaksanaTeknis = $("#table-unit-pelaksana-teknis").docoTabel({
        filter: true,
        columnDefs: [],
        select: {
            style: "os",
            selector: "td:first-child"
        },
        sorting: [[1, "asc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        stateSave: true,
        scrollX: true,
        ajax: baseUrl+"master/unit-pelaksana-teknis/get-data",
        columns: [
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {title: "'.(\Yii::t("fe", "Sync ID")).'", data: "upt_sync_id"},
            {title: "'.(\Yii::t("fe", "Nama UPT")).'", data: "upt_nama"},
        ],
        scrollCollapse: true,
    });

    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table, [
        [2, \''.(preg_replace("/[\n\t\r]/i", "", Html::textInput('upt_sync_id', '', ['class' => 'form-control', 'placeholder' => 'Sync ID']))).'\'],
        [3, \''.(preg_replace("/[\n\t\r]/i", "", Html::textInput('upt_nama', '', ['class' => 'form-control', 'placeholder' => 'Nama UPT']))).'\']
    ]);
});
', View::POS_END, 'upt-index');
