<?php

/**
 * @Author: Sigit
 * @Date:   2019-02-18 11:44:38
 */

use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = Yii::t('fe', 'Rekap Tempat Tidur');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rekam Medik'), 'url' => ['/rm']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style type="text/css" media="screen">
    th { white-space: nowrap; }
</style>

<div class="row body">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title">
                            <b><?= $this->title ?></b>
                        </h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])) ?>
                    </div>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'search',
                    'pdf',
                    'excel',
                    'reset',
                ], '#tabel_tempat_tidur');?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <h1 class="text-center">Total Tempat Tidur Aktif <span id="label_tahun"></span></h1>
                <!-- <h2 class="text-center">Tahun : <span id="label-tahun"></span></h2> -->
                <table class="table table-striped table-hover dataTable" id="tabel_tempat_tidur" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th><?= Yii::t('fe', 'No') ?></th>
                            <th><?= Yii::t('fe', 'Tahun') ?></th>
                            <th><?= Yii::t('fe', 'Nama Ruangan') ?></th>
                            <?php for ($j=1; $j <= 12; $j++) {
                                echo "<th>".Yii::t('fe', $months[$j])."</th>";
                            } ?>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3">Total :</th>
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
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    var table;
    var total_ruangan;

    $(document).ready(function(){
        table = $("#tabel_tempat_tidur").docoTabel({
            filter: true,
            sorting: [[1, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"rm/rekap-tempat-tidur/get-data",
            columns:[
                {
                    title:"No.",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title:"'.(\Yii::t("fe", "Tahun")).'",
                    data: "tahun",
                    visible : false,
                },
                {
                    title:"'.(\Yii::t("fe", "Nama Ruangan")).'",
                    data: "ruangan_nama",
                },
                {
                    title:"'.(\Yii::t("fe", "Jan")).'",
                    data: "Jan",
                    searchable: false
                },
                {
                    title:"'.(\Yii::t("fe", "Feb")).'",
                    data: "Feb",
                    searchable: false
                },
                {
                    title:"'.(\Yii::t("fe", "Mar")).'",
                    data: "Mar",
                    searchable: false
                },
                {
                    title:"'.(\Yii::t("fe", "Apr")).'",
                    data: "Apr",
                    searchable: false
                },
                {
                    title:"'.(\Yii::t("fe", "May")).'",
                    data: "May",
                    searchable: false
                },
                {
                    title:"'.(\Yii::t("fe", "Jun")).'",
                    data: "Jun",
                    searchable: false
                },
                {
                    title:"'.(\Yii::t("fe", "Jul")).'",
                    data: "Jul",
                    searchable: false
                },
                {
                    title:"'.(\Yii::t("fe", "Aug")).'",
                    data: "Aug",
                    searchable: false
                },
                {
                    title:"'.(\Yii::t("fe", "Sep")).'",
                    data: "Sep",
                    searchable: false
                },
                {
                    title:"'.(\Yii::t("fe", "Oct")).'",
                    data: "Oct",
                    searchable: false
                },
                {
                    title:"'.(\Yii::t("fe", "Nov")).'",
                    data: "Nov",
                    searchable: false
                },
                {
                    title:"'.(\Yii::t("fe", "Dec")).'",
                    data: "Dec",
                    searchable: false
                }
            ],
            footerCallback: function( row, data, start, end, display){
                var start_column = 3;
                total_ruangan = {};
                var api = this.api();
                for(var i=0; i<12; i++){
                    var curent_column = start_column+i;
                    total_ruangan[start_column+i] = api
                    .column(start_column+i)
                    .data()
                    .reduce( function (a, b) {
                        return a + b;
                    }, 0);
                }
                $.each(total_ruangan, function(index, value){
                    $(api.column(index).footer()).html(
                        value
                    );
                });
            },
        });

        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                1,
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('Tahun', date('Y'),
                        $years,
                        [
                            'id' => 'filter_tahun',
                            'class' => 'form-control select2',
                        ]
                    )
                )).'</div>\'
            ],
        ], {
        1:0,
        2:1
            });

        $("#filter_tahun").trigger("select2:change");
    });

    $("#filter_tahun").change(function(){
        $(".data-filter").click();
    });
', View::POS_END, 'index');

// $this->registerJs($this->render('js/index.js'), View::POS_END);
?>