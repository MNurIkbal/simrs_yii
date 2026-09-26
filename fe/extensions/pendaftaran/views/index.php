<?php

/**
 * @author Naufal Ziyad L
 * @copyright 15 Februari 2018
 */

use yii\web\View;
use yii\helpers\Html;
use app\components\DHtml;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\DepDrop;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Pendaftaran', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$instalasi_id = DocoHelpers::encrypt(Yii::$app->docoVars->workspace('instalasi_id'));

?>
<style type="text/css">
    .row {
        padding-right: 5px;
    }

    #modal_backdrop {
        z-index: 1041 !important;
        max-height: calc(100vh);
        overflow-y: auto;
    }
</style>
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
                    <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                    <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
                </div>
                <!-- end -->
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-form'
                            ]
                        ],
                        'update' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Ubah'),
                            'icon' => 'fa fa-pencil',
                            'method' => '',
                            'attributes' => [
                                'data-target' => Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/update?id=',
                            ]
                        ],
                        'riwayat' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Riwayat Kunjungan'),
                            'icon' => 'fa fa-eye',
                            'method' => '',
                            'attributes' => [
                                'data-pages' => '_blank',
                                'data-target' => Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/riwayat?id=',
                            ]
                        ],
                        'riwayat-tera' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Riwayat Kunjungan Tera'),
                            'icon' => 'fa fa-eye',
                            'method' => '',
                            'attributes' => [
                                'data-pages' => '_blank',
                                'data-target' => Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/riwayat-tera?id=',
                            ]
                        ],
                        'pdf'=>[
                            'attributes' => [
                                'data-target' => Url::home() . Yii::$app->controller->module->id . '/' . Yii::$app->controller->id . '/export-pdf?'
                            ]
                        ],
                        'cetak-data-pasien' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Cetak Data Pasien'),
                            'icon' => 'fa fa-file-pdf-o',
                            'method' => '',
                            'attributes' => [
                                'id'=>'cetak-pdf',
                                'data-options'=>'click',
                                'data-target' => Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/cetak-data-pasien?id=',
                            ]
                        ],
                        'excel',
                        'cetak-kartu' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Cetak Kartu'),
                            'icon' => 'fa fa-print',
                            'method' => '',
                            'attributes' => [
                                'data-options'=>'click',
                                'data-target' => Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/cetak-kartu?',
                            ]
                        ],
                        'riwayat-pasien' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Riwayat Pasien'),
                            'icon' => 'fa fa-history',
                            'method' => 'not-exist',
                            'attributes' => [
                                'id' => 'btn-riwayat-pasien',
                                'data-options' => 'click',
                                'data-target'=> ''
                            ]
                        ],
                        'riwayat-pasien-tera' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Riwayat Pasien Tera'),
                            'icon' => 'fa fa-user',
                            'method' => 'not-exist',
                            'attributes' => [
                                'id' => 'btn-riwayat-pasien-tera',
                                'data-options' => 'click',
                                'data-target'=> ''
                            ]
                        ],
                    ],'#table-informasi-pasien');?>
                <?= Html::button("hidden riwayat", [
                    'id'          => 'btn-hidden-riwayat-pasien',
                    'data-width'  => '90%',
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_backdrop',
                    'action'      => '',
                    'style'       => 'display: none;',
                ]) ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 panel panel-flat">
                        <div class="panel-body">
                            <div class="advanced-filter">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 panel panel-flat">
                        <div class="panel-body">
                            <table id="table-informasi-pasien" class="table table-striped table-condensed table-hover" style="width:100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1"></th>
                                        <th width="1">No</th>
                                        <th><?=\Yii::t("fe", "Tgl. Rekam Medik");?></th>
                                        <th><?=\Yii::t("fe", "No Rekam Medik");?></th>
                                        <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                                        <th><?=\Yii::t("fe", "Jenis Kelamin");?></th>
                                        <th><?=\Yii::t("fe", "Alamat");?></th>
                                        <th><?=\Yii::t("fe", "Propinsi");?></th>
                                        <th><?=\Yii::t("fe", "Kabupaten");?></th>
                                        <th><?=\Yii::t("fe", "Kecamatan");?></th>
                                        <th><?=\Yii::t("fe", "Petugas");?></th>
                                        <th><?=\Yii::t("fe", "Alasan Perubahan");?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                    </tr>
                                    <!-- <tr>
                                        <td class="text-center" colspan="3"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                                    </tr> -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal_poli" class="modal fade" data-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
            </div>
        </div>
    </div>
    <div id="modal_riwayat" class="modal">
        <div class="modal-dialog modal-xl" style="width: 90%;">
            <div class="modal-content">
            </div>
        </div>
    </div>
    <div id="modal-preview" class="modal">
        <div class="modal-dialog modal-lg" style="width: 90%;">
            <div class="modal-header bg-inverse" style="z-index: 1050">
                <button type="button" id="dismiss-preview-btn" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Preview</h5>
            </div>
            <div class="modal-content">
                <div class="preview-wrapper" style="position: relative;" id="preview-wrapper">
                    <!-- <div class="overlay-preview"></div> -->
                    <iframe frameborder="0" id="preview-content" style="width:100%;height:85vh"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
<script src=""></script>
<?php
    $this->registerJs('
    // Event Ready
    $(document).ready(function() {
        $(function(){
            $(".daterange").daterangepicker({
                applyClass: "bg-slate-600",
                cancelClass: "btn-default",
                locale: {
                    format: "DD MMM YYYY"
                }
            });
        })

        // Generate Table
        table = $("#table-informasi-pasien").docoTabel({
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
            order: [[ 2, "asc" ]],
            sorting: [[2, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"pendaftaran/informasi-pencarian-pasien/get-data",
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
                {
                    title: "'.(\Yii::t("fe", "Tgl. Rekam Medik")).'",
                    data: "tgl_rekam_medik", searchable: false
                },
                {
                    title: "'.(\Yii::t("fe", "No Rekam Medik")).'",
                    data: "no_rekam_medik"
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Pasien")).'",
                    data: "nama_pasien"
                },
                {
                    title: "'.(\Yii::t("fe", "Jenis Kelamin")).'",
                    data: "jenis_kelamin"
                },
                {
                    title: "'.(\Yii::t("fe", "Alamat")).'",
                    data: "alamat_pasien"
                },
                {
                    title: "'.(\Yii::t("fe", "Propinsi")).'",
                    data: "propinsi_nama"
                },
                {
                    title: "'.(\Yii::t("fe", "Kabupaten")).'",
                    data: "kabupaten_nama"
                },
                {
                    title: "'.(\Yii::t("fe", "Kecamatan")).'",
                    data: "kecamatan_nama"
                },
                {
                    title: "'.(\Yii::t("fe", "Petugas")).'",
                    data: "petugas"
                },
                {
                    title: "'.(\Yii::t("fe", "Alasan Perubahan")).'",
                    data: "alasan_ubahdata",
                    searchable: false
              },
            ],
            infoCallback: function( settings, start, end, max, total, pre ) {
                return pre + " (Filtered) from total " + settings.json.totalData;
            },
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [2, \''.(preg_replace("/[\n\t\r]/i", '',
                Html::textInput('tgl_rekam_medik', '', ['class' => 'form-control daterange','placeholder'=>\Yii::t('fe', 'Tanggal Rekam Medik')])
                )).'\'],
            [5, \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('jeniskelamin', '', $ddlJenisKelamin, ['class' => 'form-control select2', 'id' => 'jeniskelamin', 'prompt' => Yii::t('fe', '--Pilih Jenis Kelamin--') ]))).'\' ],
            [7, \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('propinsi_id', '', $ddlPropinsi, ['class' => 'form-control select2', 'id' => 'propinsi_id', 'prompt' => Yii::t('fe', '--Pilih Propinsi--') ]))).'\' ],

            [
                8,
                \''.(
                    preg_replace(
                        "/[\n\t\r]/i",
                        '',
                        DepDrop::widget(
                            [
                                'name'=>'kabupaten_id',
                                'options'=>[
                                    'id'=>'kabupaten_id',
                                    'class'=>'select2',
                                ],
                                'pluginOptions'=>[
                                    'depends'=>['propinsi_id'],
                                    'placeholder'=>\Yii::t('fe', '--pilih kabupaten--'),
                                    'url'=>Url::to(['list-kabupaten'])
                                ]
                            ]
                        )

                    )
                ).'\'
            ],
            [
                9,
                \''.(
                    preg_replace(
                        "/[\n\t\r]/i",
                        '',
                        DepDrop::widget(
                            [
                                'name'=>'kecamatan_id',
                                'options'=>[
                                    'id'=>'kecamatan_id',
                                    'class'=>'select2',
                                ],
                                'pluginOptions'=>[
                                    'depends'=>['kabupaten_id'],
                                    'placeholder'=>\Yii::t('fe', '--pilih kecamatan--'),
                                    'url'=>Url::to(['list-kecamatan'])
                                ]
                            ]
                        )

                    )
                ). '\'
            ],
        ],
        {
            3:0,
            4:1,
            5:2,
            6:3,
            7:4,
            8:5,
            9:6,
            10:7
        });
    });
    $(".btn-cetak-kartu").click(function(e){
        e.preventDefault()
        var tableData = table.row(".selected").data();
        if(typeof tableData !== "undefined"){
            var primary = tableData.primary;
            var url = window.location.origin;
            var target = $(this).attr("data-target")+"id=";
            window.open(url+target+primary);
        }else{
            docoNotification("warning", "Terjadi Kesalahan", "Belum ada data yang dipilih!");
        }

    })
    $(".btn-cetak-data-pasien").click(function(e){
        e.preventDefault()
        var tableData = table.row(".selected").data();
        if(typeof tableData !== "undefined"){
            var primary = tableData.primary;
            var url = window.location.origin;
            var target = $(this).attr("data-target");
            // console.log(url+target+primary);
            window.open(url+target+primary);
        }else{
            docoNotification("warning", "Terjadi Kesalahan", "Belum ada data yang dipilih!");
        }

    })
    // Options
    var oneDay = 24*60*60*1000;
    var rangeDemoFormat = "%e-%b-%Y";
    var rangeDemoConv = new AnyTime.Converter({format:rangeDemoFormat});

    $(document).on("click", "#btn-riwayat-pasien", function() {
        if (typeof table.row(".selected").data() !== "undefined") {
            let norm = table.row(".selected").data().no_rekam_medik;
            if (norm) {
                    $("#btn-hidden-riwayat-pasien").attr("action", "/igd/riwayat-pasien/history-patient?norm="+norm+"&instalasi='.$instalasi_id.'&modal=is_modal");
                    $("#btn-hidden-riwayat-pasien").trigger("click");
                } else {
                    $("#btn-hidden-riwayat-pasien").attr("action", "");
                }
        } else {
            docoNotification("warning", "Terjadi Kesalahan", "Belum ada data yang dipilih!");

            return true;
        }
    });

    $(document).on("click", "#btn-riwayat-pasien-tera", function() {
        if (typeof table.row(".selected").data() !== "undefined") {
            let pasien_id = table.row(".selected").data().primary;
            if (pasien_id) {
                    $("#btn-riwayat-pasien-tera").attr("data-target", "'.Url::home().'"+"pendaftaran/informasi-pencarian-pasien/view-riwayat-tera?pasien_id="+pasien_id);
                } else {
                    $("#btn-riwayat-pasien-tera").attr("data-target", null);
                }
                window.open($(this).attr("data-target"), "_blank");
        } else {
            docoNotification("warning", "Terjadi Kesalahan", "Belum ada data yang dipilih!");

            return true;
        }
    });

', View::POS_END, 'b-index');

?>
