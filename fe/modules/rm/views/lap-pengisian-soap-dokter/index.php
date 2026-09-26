<?php

use app\components\DocoHelpers;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;

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
                    'detail' => [
                        'title' => \Yii::t('fe', 'Detail'),
                        'icon' => 'fa fa-eye',
                        'attributes' => [
                            'data-options' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '75%',
                            'data-url' => '/rm/lap-pengisian-soap-dokter/detail?id=',
                            'data-conditions' => 'instalasi_id,ruangan_id,jenis_laporan,tgl_pendaftaran',
                            'id' => 'btn-detail'
                        ]
                    ],
                    'excel'
                ], '#example'); ?>
            </div>
            <div class="panel-body">
                <div class="advanced-filter"></div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                    <tr class="bg-inverse">
                        <th colspan="10" class="text-center"> Laporan Rekapitulasi <span id="jenis">SOAP</span> Sudah Terisi</th>
                    </tr>
                        <tr class="bg-inverse">
                            <th><?= \Yii::t("fe", "No"); ?></th>
                            <th><?= \Yii::t("fe", "Instalasi"); ?></th>
                            <th><?= \Yii::t("fe", "Ruangan"); ?></th>
                            <th><?= \Yii::t("fe", "Dokter"); ?></th>
                            <th><?= \Yii::t("fe", "Jumlah SOAP"); ?></th>
                            <th><?= \Yii::t("fe", "Jumlah Pasien"); ?></th>
                            <th><?= \Yii::t("fe", "Tanggal Pendaftaran"); ?></th>
                            <th><?= \Yii::t("fe", "Instalasi ID"); ?></th>
                            <th><?= \Yii::t("fe", "Ruangan ID"); ?></th>
                            <th><?= \Yii::t("fe", "Pegawai ID"); ?></th>
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
    const SOAP = "'.$title_soap.'";
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    $(document).ready(function() {
        table = $("#example").docoTabel({
            filter: true,
            sorting: false,
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"rm/lap-pengisian-soap-dokter/get-data",
            columnDefs: [{
                className: "select-checkbox",
                targets: 0
            }],
            select: {
                style: "os",
                selector: "tr"
            },
            columns: [
                {title: "", data: null, defaultContent: "", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "No")).'",data: "rowNum", name : "rowNum", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Instalasi")).'", data: "instalasi_nama", searchable: false},
                {title: "'.(\Yii::t("fe", "Ruangan")).'", data: "ruangan_nama", searchable: false},
                {title: "'.(\Yii::t("fe", "Dokter")).'", data: "nama_dokter", searchable: false},
                {title: "'.(\Yii::t("fe", "Jumlah SOAP")).'", data: "jumlah_soap", className: "text-center", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Jumlah Pasien")).'", data: "jumlah_pasien", className: "text-center", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Tanggal Pendaftaran")).'", data: "tgl_pendaftaran", visible: false},
                {title: "'.(\Yii::t("fe", "Instalasi")).'", data: "instalasi_id", visible: false},
                {title: "'.(\Yii::t("fe", "Ruangan")).'", data: "ruangan_id", visible: false},
                {title: "'.(\Yii::t("fe", "Dokter")).'", data: "pegawai_id", visible: false},
            ]
        });

        table.on( \'xhr\', function () {
            data = table.ajax.params();
            // alert( \'Search term was: \'+data.search.value );
        });

        $(".dataTables_filter").hide();

        $(".filter-form").datatableBootstrapFilter(table,
            [
                [
                    7,
                    \'<div class="input-group"><input value='.date("d-M-Y").' type="text" id="rangeDemoStart" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input value='.date("d-M-Y").' type="text" id="rangeDemoFinish" class="form-control endDate" /><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\'
                ],
                [
                    8,
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList(
                        'instalasi_id',
                        '',
                        ArrayHelper::map($api['instalasi'], 'instalasi_id', 'instalasi_nama'),
                        [
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih --'),
                            'col-index' => '2'
                        ]
                    ))).'\'
                ],
                [
                    9,
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList(
                        'ruangan_id',
                        '',
                        ArrayHelper::map($api['ruangan'], 'ruangan_id', 'ruangan_nama'),
                        [
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih --'),
                            'col-index' => '3'
                        ]
                    ))).'\'
                ],
                [
                    10,
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList(
                        'pegawai_id',
                        null,
                        ArrayHelper::map($api['dokter'], 'pegawai_id', 'nama_pegawai'),
                        [
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih --'),
                            'col-index' => '4'
                        ]
                    ))).'\'
                ],
            ], {
                7:0,
                8:1,
                9:2,
                10:3,
            }, true
        );

        dateRangeHelper(".startDate", ".endDate", ".targetDate", true);
    });

    $(document).on("click", "#reset", function(){
        $("#jenis").text(""+SOAP+"")
    })

    $(document).on("click", "#search", function(){
        let jenis = $("#jenis_laporan").val()
        $("#jenis").text(""+SOAP+"")
    })

', View::POS_END, 'b-index');
?>