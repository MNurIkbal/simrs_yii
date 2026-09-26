<?php

/**
 * @Author: Johndoe
 * @Date:   2018-03-22 13:49:47
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

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Rawat Jalan', 'url' => ['index']];
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
                      <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
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
                    <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset',
                        'print',
                        'excel',
                    ]);?>
                </div>
            </div>

            <div class="panel-body">
                <div class="panel panel-white no-border">
                    <div class="panel-heading">
                        <h4 class="panel-title"><?=Yii::t('fe', 'Pencarian');?></h4>
                        <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                    </div>
                    <div class="panel-body filter-form">

                    </div>
                </div>
                <div class="panel panel-white no-border">
                    <div class="panel-heading">
                        <h4 class="panel-title"><?=$this->title;?></h4>
                    </div>
                    <div class="panel-body">
                        <div id="no_header">
                        <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="80"></th>
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
$this->registerCss($this->render('../assets/css/rajal.css'));
$this->registerJs('
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
            filter: true,
            sorting: [[7, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"rajal/lap-pemesanan-barang/get-data",
            drawCallback: function ( settings ) {
                var api = this.api();
                var rows = api.rows( {page:"current"} ).nodes();
                var number = api.column(0, {page:"current"} ).nodes();
                var last=null;
                var rownum = 1;
                api.column(7, {page:"current"} ).data().each( function ( group, i ) {
                    rownum++;
                    if ( last !== group ) {
                        $(rows).eq( i ).before(
                            "<tr class=\'group\'>"+
                                "<th>'.(\Yii::t("fe", "Tanggal Pemesanan")).':</th>"+
                                "<th>"+api.column(6, {page:"current"} ).data()[i]+"</th>"+
                                "<th></th>"+
                                "<th></th>"+
                                "<th>'.(\Yii::t("fe", "Instalasi Tujuan")).':</th>"+
                                "<th>"+api.column(8, {page:"current"} ).data()[i]+"</th>"+
                            "</tr>"+
                            "<tr class=\'group\'>"+
                                "<th>'.(\Yii::t("fe", "No Pemesanan")).':</th>"+
                                "<th>"+api.column(7, {page:"current"} ).data()[i]+"</th>"+
                                "<th></th>"+
                                "<th></th>"+
                                "<th>'.(\Yii::t("fe", "Ruangan Tujuan")).':</th>"+
                                "<th>"+api.column(9, {page:"current"} ).data()[i]+"</th>"+
                            "</tr>"+
                            "<tr class=\'group\'>"+
                                "<th>'.(\Yii::t("fe", "No")).'</th>"+
                                "<th>'.(\Yii::t("fe", "Nama Barang")).'</th>"+
                                "<th>'.(\Yii::t("fe", "Qty Pemesanan")).'</th>"+
                                "<th>'.(\Yii::t("fe", "Satuan Besar")).'</th>"+
                                "<th>'.(\Yii::t("fe", "Qty Pemesanan")).'</th>"+
                                "<th>'.(\Yii::t("fe", "Satuan Kecil")).'</th>"+
                            "</tr>"
                        );

                        last = group;
                        rownum = 1;
                    }
                    $(number).eq(i).html(rownum);
                } );
            },
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "Nama Barang")).'", data: "barang_nama", searchable: false},
                {title: "'.(\Yii::t("fe", "Qty Pemesanan")).'", data: "qty_pesan", searchable: false},
                {title: "'.(\Yii::t("fe", "Satuan Besar")).'", data: "satuan_besar", searchable: false},
                {title: "'.(\Yii::t("fe", "Qty Pemesanan")).'", data: "qty_konversi", searchable: false},
                {title: "'.(\Yii::t("fe", "Satuan Kecil")).'", data: "satuan_kecil", searchable: false},
                {
                    title: "'.(\Yii::t("fe", "Tanggal Pemesanan")).'",
                    visible: false,
                    data: "tgl_pesanbarang"
                },
                {
                    title: "'.(\Yii::t("fe", "No Pemesanan")).'",
                    visible: false,
                    data: "no_pemesanan"
                },
                {
                    title: "'.(\Yii::t("fe", "Instalasi Tujuan")).'",
                    visible: false,
                    data: "instalasi_tujuan"
                },
                {
                    title: "'.(\Yii::t("fe", "Ruangan Tujuan")).'",
                    visible: false,
                    data: "ruangan_tujuan"
                },
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table,
            [
                [
                    6,
                    \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate"/><input type="text" style="display:none" class="targetDate" col-index=2></div>\'
                ],
                [
                    8,
                    \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('instalasi_tujuan', '',
                            ArrayHelper::map($api['response']['instalasi'], 'instalasi_id', 'instalasi_nama'),
                            [
                                'id' => 'filter_instalasi',
                                'class' => 'form-control select2',
                                'prompt' => \Yii::t('fe', 'Instalasi tujuan')
                            ]
                        )
                    )).'</div>\'
                ],
                [
                    9,
                    \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                        DepDrop::widget([
                            'name' => 'ruangan_tujuan',
                            'data' => ['' => 'Pilih'],
                            'options' => [
                                'disabled' => false,
                                'class' => 'form-control select2'
                            ],
                            'pluginOptions' => [
                               'depends'  => ['filter_instalasi'],
                               'placeholder' => \Yii::t('fe', 'Ruangan tujuan'),
                               'url' =>'/rajal/lap-pemesanan-barang/get-ruangan',
                            ]
                        ])
                    )).'</div>\'
                ],
                [
                    7,
                    \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('no_pemesanan', '',
                            ArrayHelper::map($api['response']['pemesanan'], 'no_pemesanan', 'no_pemesanan'),
                            [
                                'class' => 'form-control select2',
                                'prompt' => \Yii::t('fe', 'No Pemesanan')
                            ]
                        )
                    )).'</div>\'
                ],
            ], {
                1:0,
                2:4,
                3:1,
                4:3
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

        // Order by the grouping
        $("#example tbody").on( "click", "tr.group", function () {
            var currentOrder = table.order()[0];
            if ( currentOrder[0] === 7 && currentOrder[1] === "asc" ) {
                table.order( [ 7, "desc" ] ).draw();
            }
            else {
                table.order( [ 7, "asc" ] ).draw();
            }
        } );
    });


', View::POS_END, 'b-index');
?>
