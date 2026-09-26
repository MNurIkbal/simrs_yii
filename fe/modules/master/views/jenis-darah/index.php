<?php

/**
 * @Author: Budi
 * @Date:   2018-04-24 11:36:33
 */

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\web\View;
use kartik\widgets\DepDrop;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;

// Some variables
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
                            'action' => '/master/jenis-darah/create',
                        ]
                    ],
                    'detail' => [
                        'title' => Yii::t('fe', 'Ubah'),
                        'icon' => 'fa fa-pencil',
                        'attributes' => [
                            'data-options'=>'modal',
                            'data-target'=>'#modal_backdrop',
                            'data-url' => '/master/jenis-darah/update?id=',
                        ]
                    ],
                    // 'edit' => [
                    //     'attributes' => [
                    //         'data-toggle' => 'modal',
                    //         'data-target' => '#modal_backdrop',
                    //         'data-url' => '/master/jenis-darah/update?id=',
                    //     ]
                    // ],
                    'delete'=>[
                        'attributes'=>[
                            'data-additional'=>'data-rm',
                        ]
                    ],
                    'excel' => [
                        'attributes' => [
                            'data-target' => '/master/jenis-darah/export-excel?'
                        ]
                    ],
                ]) ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th><?= Yii::t('fe', 'No') ?></th>
                            <th><?= Yii::t("fe", "Jenis Darah") ?></th>
                            <th><?= Yii::t("fe", "Masa Penyimpanan") ?></th>
                            <th><?= Yii::t("fe", "Suhu Penyimpanan (c)") ?></th>
                            <th><?= Yii::t("fe", "Harga") ?></th>
                            <th><?= Yii::t("fe", "Status") ?></th>
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
$(document).ready(function() {
    table = $("#example").docoTabel({
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
        ajax: baseUrl+"master/jenis-darah/get-data",
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
                title: "'.(\Yii::t("fe", "Jenis Darah")).'",
                data: "jenisdarah_nama",
            },
            {
                title: "'.(\Yii::t("fe", "Lama Penyimpanan (Hari)")).'",
                data: "lama_penyimpanan",
                searchable: false,
                class: "text-right",
            },
            {
                title: "'.(\Yii::t("fe", "Suhu Penyimpanan (&#8451;)")).'",
                data: "suhu_penyimpanan",
                searchable: false,
                class: "text-right",
            },
            {
                title: "'.(\Yii::t("fe", "Harga")).'",
                data: "harga",
                searchable: false,
                class: "text-right",
            },
            {title: "Status", data: "is_active", class: "text-center"},
        ],
        initComplete: () => {
          $(".change-status").docoToggleSwitch({
            url: baseUrl+"master/jenis-darah/change-status",
            confirmTitle : "'.(\Yii::t("fe", "Konfirmasi")).'",
            confirmMessage : "'.(\Yii::t("fe", "Apa anda yakin ingin mengubah status data?")).'",
            method: "GET",
          });
        }
    });

    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table, [
        [
            6,
            \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $is_active, ['class' => 'form-control select2']))).'\'
        ],
    ]);

});


', View::POS_END, 'b-index');
