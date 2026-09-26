<?php

    // Author: Ardi Pratama

    use yii\web\View;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use app\components\DocoHelpers;
    $this->title = Yii::t('fe', 'Komponen');
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-toolbar clearfix">
                <div class="btn-group pull-left">
                    <?=DocoHelpers::generateToolbar([
                        'search'=>[
                            'attributes'=>[
                                'data-parent'=>'.filter-form',
                            ]
                        ],
                        'reset'=>[
                            'attributes'=>[
                                'data-parent'=>'.filter-form',
                            ]
                        ],
                        'create' => [
                            'title' => \Yii::t('fe', 'Tambah'),
                            'icon' => 'fa fa-plus',
                            'attributes' => [
                                'class'=>'spa',
                                'data-options'=>'click',
                                'data-content'=>'content-komponen',
                                'data-url' => '/master/tarif-tindakan/create-komponen',
                            ]
                        ],
                        'update' => [
                            'title' => \Yii::t('fe', 'Edit'),
                            'icon' => 'fa fa-edit',
                            'attributes' => [
                                'class'=>'spa',
                                'data-options'=>'click',
                                'data-type'=>'edit',
                                'data-content'=>'content-komponen',
                                'data-url' => '/master/tarif-tindakan/update-komponen?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'data-additional'=>'data-rm',
                                'class'=>'hidden',
                                'data-target' => '/master/tarif-tindakan/delete-komponen?id=',
                            ]
                        ],
                        'excel' => [
                            'attributes' => [
                                'data-target'=>'/master/tarif-tindakan/export-excel?type=komponen&'
                            ]
                        ],
                        'pdf' => [
                            'attributes' => [
                                'data-target' => '/master/tarif-tindakan/export-pdf?type=komponen&',
                            ]
                        ],
                    ], '#table-komponen');?>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form">
                    </div>
                </div>
                <table id="table-komponen" class="table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th><?=Yii::t('fe', 'No')?></th>
                            <th><?=Yii::t('fe', 'Kode Komponen')?></th>
                            <th><?=Yii::t('fe', 'Nama Komponen')?></th>
                            <th><?=Yii::t('fe', 'Nama Lainnya')?></th>
                            <th><?=Yii::t('fe', 'Persentase Delegasi').' (%) '?></th>
                            <th><?=Yii::t('fe', 'Catatan')?></th>
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
        table.draw();
    });
    var table;
    $(document).ready(function() {
        // Generate Table
        table = $("#table-komponen").docoTabel({
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
            // sorting: [[3, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"master/tarif-tindakan/get-data-komponen",
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
                    orderable: false,
                    width:"5%"
                },
                {
                    title: "'.(\Yii::t("fe", "Kode Komponen")).'", 
                    data: "komponentarif_kode", 
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Komponen")).'", 
                    data: "komponentarif_nama", 
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Lainnya")).'", 
                    data: "komponentarif_namalainnya", 
                    searchable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Persentasi Delegasi")).' (%) '.'", 
                    data: "persen_delegasi", 
                    searchable: false,
                    class : "text-right"
                },
                {
                    title: "'.(\Yii::t("fe", "Catatan")).'", 
                    data: "catatan", 
                },
                {
                    title: "'.(\Yii::t("fe", "Status")).'", 
                    data: "is_active", 
                }
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                2, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('komponentarif_kode', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'Kode Komponen')]))).'\'
            ],
            [
                3, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('komponentarif_nama', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'Nama Komponen')]))).'\'
            ],
            [
                4, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('komponentarif_namalainnya', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'Nama Lainnya')]))).'\'
            ],
            [
                6, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('catatan', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'Catatan')]))).'\'
            ],
            [
                7, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $status, ['class' => 'select2', 'prompt' => \Yii::t('fe', '— Pilih Status —')]))). '\'
            ],
        ], {
            2:0,
            3:1,
            6:2,
            7:3,
            // 7:4
        });

        $("#table-komponen tbody").on("click", "tr", function(){
            try {
                primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
            } catch (e) {
                primaryKey = false;
            }

            if (primaryKey) {
                $("#btn-edit").attr("action",$("#btn-edit").data("url")+primaryKey);
            } else {
                $("#btn-edit").removeAttr("action");
            }
        });

    });
', View::POS_END, 'b-index');
?>