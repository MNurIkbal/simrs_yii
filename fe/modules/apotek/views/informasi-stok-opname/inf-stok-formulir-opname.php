<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-12-06 11:24:01
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2019-03-15 16:16:00
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("instalasi_name"), 'url' => ['index']];
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
                      <h3 class="panel-title"><b><?= $this->title ?></b></h3>
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
                <div class="btn-group pull-left">
                    <?=DocoHelpers::generateToolbar($btn_toolbar, '#example');?>
                </div>
            </div>

            <div class="panel-body">
                <div class="row">
                    <!-- <div class="col-md-12 filter-form"></div> -->
                    <div class="advanced-filter">
                </div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th width="70">No</th>
                            <th><?=\Yii::t("fe", "Tanggal Formulir");?></th>
                            <th><?=\Yii::t("fe", "Nomor Formulir");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Stok Opname");?></th>
                            <th><?=\Yii::t("fe", "Nomor Stok Opname");?></th>
                            <th><?=\Yii::t("fe", "Instalasi / Ruangan");?></th>
                            <th><?=\Yii::t("fe", "Petugas Verifikasi");?></th>
                            <th><?=\Yii::t("fe", "Status");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="7"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    $(document).on("keydown", null, "enter", function (event) {
        $("#find-data").click();
    });

    $(document).on("keydown", null, function (e) {
        if (e.key == "Enter") {
            $("#find-data").click();
        }

        if (e.key == "F7") {
            $("#reset-data").click();
        }
    });

    // Global Var
    var table;

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        table = $("#example").docoTabel({
            columnDefs: [ {
                sortable: false,
                className: "select-checkbox",
                targets:   0
            }],
            select: {
                style:    "os",
                selector: "tr"
            },
            filter: true,
            sorting: [[2, "asc"], [4, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            stateSave: true,
            scrollX: true,
            ajax: baseUrl+"apotek/informasi-stok-opname/get-data-inf-stok-formulir-opname",
            columns: [
                {data: null, searchable: false, sortable: false, defaultContent:""},
                {title: "No", data: "rowNum", searchable: false, sortable: false},
                {title: "'.(\Yii::t("fe", "Tanggal Formulir")).'", data: "tglformulir"},
                {title: "'.(\Yii::t("fe", "Nomor Formulir")).'", data: "noformulir"},
                {title: "'.(\Yii::t("fe", "Tanggal Stok Opname")).'", data: "tglstokopname", searchable: false},
                {title: "'.(\Yii::t("fe", "Nomor Stok Opname")).'", data: "nostokopname"},
                {title: "'.(\Yii::t("fe", "Instalasi / Ruangan")).'", data: "inst-ruang", searchable: false, sortable: false},
                {title: "'.(\Yii::t("fe", "Petugas Verifikasi")).'", data: "pegawaiverifikasi_nama", searchable: false},
                {title: "'.(\Yii::t("fe", "Status")).'", data: "status_verifikasi", sortable: false},
                {
                    title: "'.(\Yii::t('fe', 'Instalasi')).'", 
                    data: "instalasi_id",
                    visible : false
                },
                {
                    title: "'.(\Yii::t('fe', 'Ruangan')).'", 
                    data: "ruangan_id",
                    visible : false
                }
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table,
            [
                [
                    2,
                    \'<div class="input-group"><input type="text"  id="rangeDemoStart" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text"  id="rangeDemoFinish" class="form-control endDate" /><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\'
                ],
                [
                    8,
                    \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('is_verifikasi', '',
                            [2 => "Belum Input Hasil", 0 => "Belum Verifikasi", 1 => "Sudah Verifikasi"],
                            [
                                'id' => 'filter_status_verif',
                                'class' => 'form-control select2',
                                'prompt' => \Yii::t('fe', '-- Pilih Semua--')
                            ]
                        )
                    )).'</div>\'
                ],
                [
                    9, 
                    \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '', 
                        Html::dropDownList('instalasi_id', '', 
                            ArrayHelper::map($instalasi, 'instalasi_id', 'instalasi_nama'), 
                            [
                                'id' => 'filter_instalasi', 
                                'class' => 'form-control select2', 
                                'prompt' => \Yii::t('fe', '-- Pilih --')
                            ]
                        )
                    )).'</div>\'
                ],
                [
                    10, 
                    \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '', 
                        DepDrop::widget([
                            'name' => 'ruangan_nama',
                            'options' => [
                                'disabled' => false,
                                'class' => 'form-control select2',
                                'id'=>'filter_ruangan'
                            ],
                            'pluginOptions' => [
                               'depends'  => ['filter_instalasi'],
                               'placeholder' => \Yii::t('fe', '-- Pilih --'),
                               'url' =>'get-ruangan',
                            ]
                        ])
                    )).'</div>\'
                ],
            ],
            {
                2:0,
                3:1,
                5:2,
                8:3,
            }, true
            );

        dateRangeHelper(".startDate",".endDate",".targetDate");
        
        setTimeout(function () {
            $(\'#filter_instalasi\').trigger(\'depdrop:change\');
        },1);

        $(document).on("click", "#example tbody tr", function () {
            try {
                $("#cetak-rincian-tagihan").attr("disabled", true)
                $("#detail-formulir").attr("disabled", true)
                $("#so-formulir").attr("disabled", true)
                $("#delete-formulir").attr("disabled", true)
                $("#detail-so").attr("disabled", true)
                
                id_status_verifikasi = table.row(".selected").data().id_status_verifikasi ? table.row(".selected").data().id_status_verifikasi : null;
            } catch (e) {
                id_status_verifikasi = false;
            }
            
            if (id_status_verifikasi == false) {
                $("#cetak-rincian-tagihan").attr("disabled", true)
                $("#detail-formulir").attr("disabled", true)
                $("#so-formulir").attr("disabled", true)
                $("#delete-formulir").attr("disabled", true)
                $("#detail-so").attr("disabled", true)
            }
            else if (id_status_verifikasi == 2) {
                $("#cetak-rincian-tagihan").attr("disabled", false)
                $("#detail-formulir").attr("disabled", false)
                $("#so-formulir").attr("disabled", false)
                $("#delete-formulir").attr("disabled", false)
            }
            else if(id_status_verifikasi == 1){
                $("#cetak-rincian-tagihan").attr("disabled", false)
                $("#detail-formulir").attr("disabled", false)
                $("#detail-so").attr("disabled", false)
            }
            else if(id_status_verifikasi == null){
                $("#cetak-rincian-tagihan").attr("disabled", false)
                $("#detail-formulir").attr("disabled", false)
                $("#so-formulir").attr("disabled", false)
                $("#delete-formulir").attr("disabled", false)
                $("#detail-so").attr("disabled", false)
            }
            
        });

    });
', View::POS_END, 'b-index');
?>