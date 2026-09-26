<?php

// Author: Ardi Pratama

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-toolbar clearfix">
                <div class="btn-group pull-left">
                    <?=DocoHelpers::generateToolbar([
                        'search'=>[
                            'attributes'=>[
                                'data-parent'=>'.filter-tarif'
                            ]
                        ],
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-tarif'
                            ]
                        ],
                        'create' => [
                            'title' => \Yii::t('fe', 'Tambah'),
                            'icon' => 'fa fa-plus',
                            'attributes' => [
                                'class'=>'spa',
                                'data-options'=>'click',
                                'data-content'=>'content-tarif',
                                'data-url' => '/master/tarif-akomodasi-bedah/form-tarif',
                            ]
                        ],
                        'update' => [
                            'title' => \Yii::t('fe', 'Edit'),
                            'icon' => 'fa fa-edit',
                            'attributes' => [
                                'class'=>'spa',
                                'data-options'=>'click',
                                'data-type'=>'edit',
                                'data-content'=>'content-tarif',
                                'data-url' => '/master/tarif-akomodasi-bedah/form-tarif-edit?id=',
                            ]
                        ],
                        'delete'=>[
                            'attributes'=>[
                                'data-additional'=>'data-rm',
                            ]
                        ],
                        // 'excel' => [
                        //     'attributes' => [
                        //         'data-target'=>'/master/tarif-tindakan/export-excel?type=tarif&',
                        //     ],
                        // ],
                        // 'pdf' => [
                        //     'attributes' => [
                        //         'data-target' => '/master/tarif-tindakan/export-pdf?type=tarif&',
                        //     ]
                        // ],
                    ], '#table-tarif');?>
                </div>
            </div>
            <div class="panel-body">
                <div class="tab-tarif">
                </div>
                <table id="table-tarif" class="table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th><?=Yii::t('fe', 'No')?></th>
                            <th><?=Yii::t('fe', 'Kegiatan Operasi')?></th>
                            <th><?=Yii::t('fe', 'Kelas Pelayanan')?></th>
                            <th><?=Yii::t('fe', 'Perda/SK')?></th> 
                            <th><?=Yii::t('fe', 'Harga')?></th>
                            <th><?=Yii::t('fe', 'Status')?></th>
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
    $(document).on("click", ".data-reload", function() {
        tableTarif.draw();
    });
    var tableTarif;
    $(document).ready(function() {
        generateFilter("tab-tarif", "filter-tarif");
        // Generate Table
        tableTarif = $("#table-tarif").docoTabel({
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
            ajax: baseUrl+"master/tarif-akomodasi-bedah/get-data-tarif",
            columns: [
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    defaultContent: "",
                    width:"5%"
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false,
                    width:"5%"
                },
                {
                    title: "'.(\Yii::t("fe", "Kegiatan Operasi")).'", 
                    data: "kegiatanoperasi_nama", 
                    searchable: true,
                    visible: true,
                },
                {
                    title: "'.(\Yii::t("fe", "Kelas Pelayanan")).'", 
                    data: "kelaspelayanan_nama", 
                    searchable: true,
                    visible: true,
                },
                {
                    title: "'.(\Yii::t("fe", "Perda/SK")).'", 
                    data: "perdanama_sk", 
                    searchable: true,
                },
                {
                    title: "'.(\Yii::t("fe", "Harga")).' (Rp.) '.'", 
                    data: "tarif", 
                    searchable: false,
                    class: "text-right"
                },
                {
                    title: "'.(\Yii::t("fe", "Status")).'", 
                    data: "is_active", 
                    searchable: true,
                },
            ],
        });

        $(".dataTables_filter").hide();
        $(".filter-tarif").datatableBootstrapFilter(tableTarif, [
            [
                6, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $status, ['class' => 'select2', 'prompt' => \Yii::t('fe', '— Pilih Status —')]))). '\'
            ],
        ], {
            2:0,
             3:1,
            4:2,
             6:3,
        }, true);
        
        
        //dateRangeHelper(".startDate",".endDate",".targetDate");
    });
', View::POS_END, 'e-index');
?>