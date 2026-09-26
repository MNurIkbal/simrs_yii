<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-03-08 17:39:47
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-12-11 11:05:05
 */


use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;

$this->title = \Yii::t('fe', 'Laporan Pemesanan Obat Alkes');
$this->params['breadcrumbs'][] = ['label' =>  Yii::t('fe', Yii::$app->docoVars->workspace('ruangan_name')), 'url' => ['']];
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
                <div class="btn-group pull-left">
                    <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset',
                        'print',
                        'excel'
                    ]);?>
                </div>
            </div>

            <div class="panel-body">
                <div class="filter-form">
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">      
                            <th></th>
                            <th>No</th>
                            <th>Tanggal Pemesanan</th>
                            <th>Nomor Pemesanan</th>
                            <th>Instalasi Tujuan</th>
                            <th>Ruangan Tujuan</th>
                            <!-- <th width="80"></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th> -->
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
<!-- Untuk Kebutuhan Modal Global -->
<div id="modal_backdrop_search" class="modal fade" style="z-index:1065;" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<!-- End -->

<?php 
$this->registerCss($this->render('../assets/css/apotek.css'));
$this->registerJs('
    // Global Var
    var table;

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {
        table = $("#example").docoTabel({
            filter: true,
            sorting: [[2, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"apotek/laporan-pemesanan-obatalkes/get-data",
            columns: [
                {
                    title: "",
                    data: "detail",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Tanggal Pemesanan")).'",
                    data: "tglpemesanan"
                },
                {
                    title: "'.(\Yii::t("fe", "No Pemesanan")).'", 
                    data: "nopemesanan"
                },
                {
                    title: "'.(\Yii::t("fe", "instalasi_tujuan")).'",  
                    data: "instalasi_tujuan"
                },
                {
                    title: "'.(\Yii::t("fe", "ruangan_tujuan")).'", 
                    data: "ruangan_tujuan"
                },
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, 
            [
                [
                    2, 
                    \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate"/><input type="text" style="display:none" class="targetDate" col-index=2></div>\'
                ],
                [
                    4, 
                    \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',  
                        Html::dropDownList('instalasi_tujuan', '', 
                            ArrayHelper::map($instalasi, 'instalasi_id', 'instalasi_nama'), 
                            [
                                'id' => 'filter_instalasi', 
                                'class' => 'form-control select2', 
                                'prompt' => \Yii::t('fe', 'Instalasi tujuan')
                            ]
                        )
                    )).'</div>\'
                ],
                [
                    5, 
                    \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '', 
                        DepDrop::widget([
                            'name' => 'ruangan_tujuan',
                            'options' => [
                                'disabled' => false,
                                'class' => 'form-control select2'
                            ],
                            'pluginOptions' => [
                               'depends'  => ['filter_instalasi'],
                               'placeholder' => \Yii::t('fe', 'Ruangan tujuan'),
                               'url' =>'inf-pemesanan-obat-alkes/get-ruangan'
                            ]
                        ])                        
                    )).'</div>\'
                ],
                
            ], {
                2:0,
                3:1,
                4:2,
                5:3
            }, true
        );

        dateRangeHelper(".startDate",".endDate",".targetDate");
        $(".daterange-basic").daterangepicker({
            startDate: "'.(date("01-M-Y")).'", autoUpdateInput: true,
            endDate: "'.(date("d-M-Y")).'",
            applyClass: "bg-slate-600",
            cancelClass: "btn-default",
            locale: {
                format: "DD-MMMM-YYYY"
            }
        });

         $(".instalasi").select2({
            placeholder: "",
        });

        
    });

    
', View::POS_END, 'b-index');
?>
