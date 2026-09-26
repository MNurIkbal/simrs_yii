<?php

    // Author: Ardi Pratama

    use yii\web\View;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use app\components\DocoHelpers;
    $this->title = \Yii::t('fe', 'Perda');
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-toolbar clearfix">
                <div class="btn-group pull-left">
                    <?=DocoHelpers::generateToolbar([
                        'search'=>[
                            'attributes'=>[
                                'data-parent'=>'.filter-form-perda'
                            ]
                        ],
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-form-perda'
                            ]
                        ],
                        'create' => [
                            'title' => \Yii::t('fe', 'Tambah'),
                            'icon' => 'fa fa-plus',
                            'attributes' => [
                                'class'=>'spa',
                                'data-options'=>'click',
                                'data-content'=>'content-perda',
                                'data-url' => '/master/tarif-tindakan/create-perda',
                            ]
                        ],
                        'update' => [
                            'title' => \Yii::t('fe', 'Edit'),
                            'icon' => 'fa fa-edit',
                            'attributes' => [
                                'class'=>'spa',
                                'data-options'=>'click',
                                'data-type'=>'edit',
                                'data-content'=>'content-perda',
                                'data-url' => '/master/tarif-tindakan/update-perda?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'class'=>'hidden',
                                'data-additional' => 'data-rm',
                                'data-target' => '/master/tarif-tindakan/delete-perda?id=',
                            ]
                        ],
                        'excel' => [
                            'attributes' => [
                                'data-target'=>'/master/tarif-tindakan/export-excel?type=perda&'
                            ]
                        ],
                        'pdf' => [
                            'attributes' => [
                                'data-target' => '/master/tarif-tindakan/export-pdf?type=perda&'
                            ]
                        ],
                    ], '#table-perda');?>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form-perda">
                    </div>
                </div>
                <table id="table-perda" class="table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th><?=Yii::t('fe', 'No')?></th>
                            <th><?=Yii::t('fe', 'Nomer Perda')?></th>
                            <th><?=Yii::t('fe', 'Nama Perda')?></th>
                            <th><?=Yii::t('fe', 'Nama Lainnya')?></th>
                            <th><?=Yii::t('fe', 'Tanggal Berlaku')?></th>
                            <th><?=Yii::t('fe', 'Detail')?></th>
                            <th><?=Yii::t('fe', 'Ditetapkan Oleh')?></th>
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
        tablePerda.draw();
    });
    var tablePerda;
    $(document).ready(function() {
        // Generate Table
        tablePerda = $("#table-perda").docoTabel({
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
            ajax: baseUrl+"master/tarif-tindakan/get-data-perda",
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
                    title: "'.(\Yii::t("fe", "Nomor Perda")).'", 
                    data: "perda_no", 
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Perda")).'", 
                    data: "perdanama_sk", 
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Lainnya")).'", 
                    data: "nama_lainnya", 
                    searchable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Tanggal Berlaku")).'", 
                    data: "perda_tgl", 
                },
                {
                    title: "'.(\Yii::t("fe", "Detail")).'", 
                    data: "perda_tentang", 
                    searchable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Ditetapkan Oleh")).'", 
                    data: "ditetapkan_oleh", 
                },
                {
                    title: "'.(\Yii::t("fe", "Status")).'", 
                    data: "is_active", 
                },
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form-perda").datatableBootstrapFilter(tablePerda, [
            [
                2, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('perda_no', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'Nomor Perda')]))).'\'
            ],
            [
                3, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('perdanama_sk', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'Nama Perda')]))).'\'
            ],
            [
                4, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('nama_lainnya', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'Nama Lainnya')]))).'\'
            ],
            [
                5,
                \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate" /><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\'
            ],
            [
                7, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('ditetapkan_oleh', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'Ditetapkan Oleh')]))).'\'
            ],
            [
                8, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $status, ['class' => 'select2', 'prompt' => \Yii::t('fe', '— Pilih Status —')]))). '\'
            ],
        ], {
            2:0,
            3:1,
            5:2,
            7:3,
            8:4
        });
        dateRangeHelper(".startDate",".endDate",".targetDate");

        $("#table-perda tbody").on("click", "tr", function(){
            try {
                primaryKey = tablePerda.row(".selected").data().primary ? tablePerda.row(".selected").data().primary : null;
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