<?php

use app\components\DocoHelpers;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use kartik\widgets\DepDrop;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Rekam Medik', 'url' => ['index']];
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
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
                   'search' => [
                        'attributes' => [
                            'id' => 'search'
                        ]
                    ],
                    'reset' => [
                        'attributes' => [
                            'id' => 'reset'
                        ]
                    ],
                    'excel'
                ], '#example'); ?>
            </div>
            <div class="panel-body">
                <div class="advanced-filter"></div>
                <table id="example" class="table table-condensed" style="width:100%">
                    <thead>
                    <tr class="bg-inverse">
                        <th><?= \Yii::t("fe", "No"); ?></th>
                        <th><?= \Yii::t("fe", "Kode Diagnosa"); ?></th>
                        <th><?= \Yii::t("fe", "Diagnosa Utama"); ?></th>
                        <th><?= \Yii::t("fe", "Jumlah"); ?></th>
                        <th><?= \Yii::t("fe", "Kode Diagnosa"); ?></th>
                        <th><?= \Yii::t("fe", "Diagnosa Penyerta"); ?></th>
                        <th><?= \Yii::t("fe", "Jumlah"); ?></th>
                        <th><?= \Yii::t("fe", "Total"); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="10"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
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
    var data;

    $(document).ready(function() {
        table = $("#example").docoTabel({
            filter: true,
            sorting: false,
            lengthMenu: [[10, 25, 50], [10, 25, 50]],
            processing: true,
            serverSide: true,
            paging: true,
            ajax: baseUrl+"rm/lap-morbiditas/get-data",
            columns: [
                {title: "'.(\Yii::t("fe", "No")).'",data: "rowNum", name : "rowNum", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Kode Diagnosa")).'", data: "kode_diagnosa_utama", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Diagnosa Utama")).'", data: "nama_diagnosa_utama", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Jumlah")).'", data: "jumlah_utama", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Kode Diagnosa")).'", data: "kode_diagnosa_penyerta", className: "text-center", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Diagnosa Penyerta")).'", data: "nama_diagnosa_penyerta", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Jumlah")).'", data: "jumlah_penyerta", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Total")).'", data: "total", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Tgl Pendaftaran")).'", data: "tgl_pendaftaran", visible: false}, //8
                {title: "'.(\Yii::t("fe", "Instalasi")).'", data: "instalasi_id", visible: false}, //9
                {title: "'.(\Yii::t("fe", "Ruangan")).'", data: "ruangan_id", visible: false}, //10
                {title: "'.(\Yii::t("fe", "Dokter")).'", data: "pegawai_id", visible: false}, //11
                {title: "'.(\Yii::t("fe", "Umur")).'", data: "golonganumur_id", visible: false}, //12
                {title: "'.(\Yii::t("fe", "Cara Bayar")).'", data: "carabayar_id", visible: false},//13
                {title: "'.(\Yii::t("fe", "Penjamin")).'", data: "penjamin_id", visible: false}, //14
            ],
            infoCallback: function( settings, start, end, max, total, pre ) {
                return ""
            },
        });

        table.on( \'xhr\', function () {
            data = table.ajax.params();
            // alert( \'Search term was: \'+data.search.value );
        });

        $(".dataTables_filter").hide();
        $("#example_paginate").hide();

        $(".filter-form").datatableBootstrapFilter(table,
            [
                [
                    8,
                    \'<div class="input-group"><input value='.date("d-M-Y").' type="text" id="rangeDemoStart" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input value='.date("d-M-Y").' type="text" id="rangeDemoFinish" class="form-control endDate" /><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\'
                ],
                [
                    11,
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList(
                        'pegawai_id',
                        null,
                        ArrayHelper::map($api['response']['dokter'], 'pegawai_id', 'nama_pegawai'),
                        [
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih --'),
                            'col-index' => '4'
                        ]
                    ))).'\'
                ],
                [
                    9,
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList(
                        'instalasi_id',
                        '',
                        ArrayHelper::map($api['response']['instalasi'], 'instalasi_id', 'instalasi_nama'),
                        [
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih --'),
                            'col-index' => '2'
                        ]
                    ))).'\'
                ],
                [
                    10,
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList(
                        'ruangan_id',
                        '',
                        ArrayHelper::map($api['response']['ruangan'], 'ruangan_id', 'ruangan_nama'),
                        [
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih --'),
                            'col-index' => '3'
                        ]
                    ))).'\'
                ],
                [
                    12,
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList(
                        'golonganumur_id',
                        '',
                        ArrayHelper::map($api['response']['golUmur'], 'golonganumur_id', 'golonganumur_namalainnya'),
                        [
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih --'),
                            'col-index' => '3'
                        ]
                    ))).'\'
                ],
                [
                    13,
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList(
                        'carabayar_id',
                        '',
                        ArrayHelper::map($api['response']['caraBayar'], 'carabayar_id', 'carabayar_nama'),
                        [
                            'id' => 'carabayar_id',
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih --'),
                            'col-index' => '3'
                        ]
                    ))).'\'
                ],
                [
                    14,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            DepDrop::widget(
                                [
                                    'name'=>'penjamin_id',
                                    'options'=>[
                                        'id'=>'penjamin_id',
                                        'class'=>'select2',
                                    ],
                                    'pluginOptions'=>[
                                        'depends'=>['carabayar_id'],
                                        'placeholder'=>\Yii::t('fe', '--pilih penjamin--'),
                                        'url'=>Url::to(['list-penjamin'])
                                    ]
                                ]
                            )

                        )
                    ).'\'
                ],
            ], {
                8:0,
                11:1,
                9:2,
                10:3,
            }, true
        );

        dateRangeHelper(".startDate", ".endDate", ".targetDate", true);
    });

', View::POS_END, 'b-index');
?>