<?php

use yii\web\View;
use yii\helpers\Html;
use app\components\DHtml;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;

$this->title = DHtml::getTitleMenu();

$this->params['breadcrumbs'][] = ['label' => 'Kasir', 'url' => ['index']];
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
                        <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?php
                    $defaultBtn = [
                    'search',
                    'reset',
                    'excel-bgprocess' => [
                        'type' => 'button',
                        'title' => 'Unduh excel',
                        'icon' => 'fa fa-file-excel-o',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id'=>'excel-bgprocess',
                            'data-options' => 'excel-serconn',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '50%',
                            'data-url' => '/kasir/lap-detail-tagihan-pasien/show-popup?id=',
                            'data-conditions' => 'pendaftaran_id'
                        ]
                    ],

                ];

                ?>
                <?=DocoHelpers::generateToolbar($defaultBtn,'#table-informasi');?>
            </div>

            <div class="panel-body">
                <div class="advanced-filter">
                </div>
                <table id="table-informasi" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="9"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
// Global Var
var table;
$(document).ready(function() {
    table = $("#table-informasi").docoTabel({
        filter: true,
        sorting: [[1, "desc"]], 
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl+"'.(Yii::$app->controller->module->id).'/lap-detail-tagihan-pasien/get-data",
        columns: [
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: "'.(\Yii::t("fe", "Tanggal Pembayaran")).'",
                data: "tgl_pembayaran",
                name:"tgl_pembayaran",
                searchable : true,
                orderable: true,
            },
            {
                title: "'.(\Yii::t("fe", "Tanggal Masuk - Keluar")).'",
                data: "tgl_masuk_keluar",
                name:"tgl_masuk_keluar",
                searchable:false,
                orderable: false,
            },
            {
                title: "'.(\Yii::t("fe", "No Pembayaran")).'",
                data: "no_pembayaran",
                name:"no_pembayaran",
                searchable:false,
                orderable: true,
            },
            {
                title: "'.(\Yii::t("fe", "Instalasi - Ruangan Akhir")).'",
                data: "instalasi_nama",
                name:"instalasi_nama",
                searchable:false,
                orderable : false,
            },
            {
                title: "'.(\Yii::t("fe", "No Pendaftaran")).'",
                data: "no_pendaftaran",
                name:"no_pendaftaran",
                orderable: true,
            },
            {
                title: "'.(\Yii::t("fe", "Nama Pasien")).'",
                data: "nama_pasien_medik",
                name:"nama_pasien_medik",
                searchable:false,
                orderable: false,
            },
            {
                title: "'.(\Yii::t("fe", "Cara Bayar - Penjamin")).'",
                data: "carabayar_nama",
                name:"carabayar_nama",
                searchable:false,
                orderable: false,
            },
            {
                title: "'.(\Yii::t("fe", "Jumlah Tagihan")).'",
                data: "total_tagihan",
                name:"total_tagihan",
                searchable:false,
                orderable: true,
            },
            {
                title: "'.(\Yii::t("fe", "Jumlah Dibayar Penjamin")).'",
                data: "total_dijamin",
                name:"total_dijamin",
                searchable:false,
                orderable: true,
            },
            {
                title: "'.(\Yii::t("fe", "Jumlah Dibayar Pasien")).'",
                data: "total_dibayar",
                name:"total_dibayar",
                searchable:false,
                orderable: true,
            },
            {
                title: "'.(\Yii::t("fe", "Instalasi")).'",
                data: "instalasi",
                name:"instalasi",
                visible:false,
                orderable: true,
            },
            {
                title: "'.(\Yii::t("fe", "Poliklinik")).'",
                data: "ruangan",
                name:"ruangan",
                visible:false,
                orderable: true,
            },
            {
                title: "'.(\Yii::t("fe", "Cara Bayar")).'",
                data: "cara_bayar",
                name:"cara_bayar",
                visible:false,
                orderable: true,
            },
            {
                title: "'.(\Yii::t("fe", "Penjamin")).'",
                data: "penjamin",
                name:"penjamin",
                visible:false,
                orderable: true,
            },
            {
                title: "'.(\Yii::t("fe", "Tanggal Pulang")).'",
                data: "tgl_keluar",
                name:"tgl_keluar",
                visible:false,
                orderable: true,
            },
        ],
    });

    $(".dataTables_filter").hide();

    $(".filter-form").datatableBootstrapFilter(table,
        [
            [
                1,
                \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'.date('d-M-Y').'" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="'.date('d-M-Y').'" readonly="true" class="form-control endDate"/><input type="text" style="display:none" class="targetDate" col-index=2></div>\'
            ],
            [
                11,
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('instalasi', '',
                        [],
                        [
                            'id' => 'filter_instalasi',
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '--Pilih Instalasi--'),
                        ]
                    )
                )).'</div>\'
            ],
            [
                12,
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('ruangan', '',
                        [],
                        [
                            'id' => 'filter_ruangan',
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '--Pilih Ruangan--'),
                        ]
                    )
                )).'</div>\'
            ],
            [
                13,
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('cara_bayar', '',
                        [],
                        [
                            'id' => 'filter_carabayar_nama',
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '--Pilih Cara bayar--'),
                        ]
                    )
                )).'</div>\'
            ],
            [
                14,
                \''.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('penjamin', '',
                        [],
                        [
                            'id' => 'filter_penjamin_nama',
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '--Pilih Penjamin--'),
                        ]
                    )
                )).'</div>\'
            ],
            [
                15,
                \'<div class="input-group"><input type="text" value="'.date('d-M-Y', strtotime('-1 months')).'" id="rangeDemoStartOut" class="form-control startDateOut"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" value="'.date('d-M-Y', strtotime('+1 months')).'"  id="rangeDemoFinishOut" class="form-control endDateOut" readonly="readonly"/><input type="text" style="display:none" class="targetDateOut"></div>\'
            ],
        ], {
             1:0,
             5:1,
             13:2,
             14:3,
             15:4,
        }, 
    true);
    
    dateRangeHelper(".startDate",".endDate",".targetDate");
    dateRangeHelper(".startDateOut",".endDateOut",".targetDateOut");
    $(".daterange-basic").daterangepicker({
        startDate: "'.(date("d-M-Y")).'", autoUpdateInput: true,
        endDate: "'.(date("d-M-Y")).'",
        applyClass: "bg-slate-600",
        cancelClass: "btn-default",
        locale: {
            format: "DD-MMMM-YYYY"
        }
    });
    $("#filter_ruangan").select2InfinityScroll({
        url: "/kasir/lap-detail-tagihan-pasien/filters?type=ruangan",
        callbackData: (param) => {
            return {
                payload: {
                    ...param,
                }
            }
        }
    })
    $("#filter_instalasi").select2InfinityScroll({
        url: "/kasir/lap-detail-tagihan-pasien/filters?type=instalasi",
        callbackData: (param) => {
            return {
                payload: {
                    ...param,
                }
            }
        }
    })
    $("#filter_carabayar_nama").select2InfinityScroll({
        url: "/kasir/lap-detail-tagihan-pasien/filters?type=carabayar",
        callbackData: (param) => {
            return {
                payload: {
                    ...param,
                }
            }
        }
    })
    $("#filter_penjamin_nama").select2InfinityScroll({
        url: "/kasir/lap-detail-tagihan-pasien/filters?type=penjamin",
        callbackData: (param) => {
            return {
                payload: {
                    ...param,
                }
            }
        }
    })

});

',View::POS_END,'b-index');
