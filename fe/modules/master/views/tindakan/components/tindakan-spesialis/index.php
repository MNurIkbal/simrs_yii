<?php

use yii\web\View;
use yii\helpers\Html;
use app\components\DocoHelpers;

$this->title = $title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'search',
                    'reset' => [
                        'data-target' => '.filter-tindakan-spesialis-new'
                    ],
                    'mapping' => [
                        'title' => \Yii::t('fe', 'Mapping'),
                        'icon' => 'fa fa-map-pin',
                        'attributes' => [
                            'class' => 'spa',
                            'data-options' => 'click',
                            'data-render' => 'map-tindakan-spesialis?id=',
                            'data-tab' => 'tab-tindakan-spesialis',
                            'data-target' => '#view-tindakan-spesialis',
                            'data-type' => 'wp'
                        ]
                    ]
                ], '#table-tindakan-spesialis-new'); ?>

            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-tindakan-spesialis-new"></div>
                </div>
                <table id="table-tindakan-spesialis-new" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th width="1"><?= \Yii::t("fe", "No"); ?></th>
                            <th><?= \Yii::t("fe", "Detail"); ?></th>
                            <th><?= \Yii::t("fe", "Nama spesialis"); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="3"><?= \Yii::t("fe", "Data tidak ditemukan."); ?></td>
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
    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    $(document).ready(function(){
        table = $("#table-tindakan-spesialis-new").docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets:   0
            }],
            select: {
                style:    "os",
                selector: "tr"
            },
            sorting: [[3, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"master/tindakan/get-data-tindakan-spesialis",
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
                {title: "' . (\Yii::t("fe", "Detail")) . '", data: "detail", searchable: false},
                {title: "' . (\Yii::t("fe", "Nama Spesialis")) . '", data: "spesialis_nama", name: "spesialis_id"},
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-tindakan-spesialis-new").datatableBootstrapFilter(table, [
            [
                3, 
                \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $data_spesialis, ['class' => 'form-control select2', 'prompt' => ""]))) . '\'
            ]
        ]);
    });
    

    ', VIEW::POS_END, 'js-kunings');
?>
