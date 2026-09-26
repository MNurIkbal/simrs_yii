<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;
use app\components\DocoHelpers;

$this->title = \Yii::t('fe', 'Laporan Cara Bayar');
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
                      <h3 class="panel-title"><b><?= Yii::t('fe', 'Laporan Cara Bayar'); ?></b></h3>
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
                        'pdf',
                        // 'excel'
                        'excel-bgprocess' => [
                            'type' => 'button',
                            'title' => 'Unduh Excel',
                            'icon' => 'fa fa-file-excel-o',
                            'method' => 'not-exist',
                            'attributes' => [
                                'id'=>'excel-bgprocess',
                                'data-options' => 'excel-serconn',
                                'data-target' => '#modal_backdrop',
                                'data-width' => '50%',
                                'data-url' => '/kasir/lap-cara-bayar/show-popup?'
                            ]
                        ],
                    ]);?>
                </div>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>

                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Tanggal pembayaran");?></th>
                            <th><?=\Yii::t("fe", "No pembayaran");?></th>
                            <th><?=\Yii::t("fe", "No pendaftaran");?></th>
                            <th><?=\Yii::t("fe", "No rekam medik");?></th>
                            <th><?=\Yii::t("fe", "Nama pasien");?></th>
                            <th><?=\Yii::t("fe", "Cara bayar");?></th>
                            <th><?=\Yii::t("fe", "Penjamin");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="8"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
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
$this->registerJs('
    // Global Var
    var table;

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {
        $("#excel-bgprocess").unbind("click");
        $("#excel-bgprocess").on("click", function (event) {
            var tgl_pembayaran = $(".startDate").val() + " - " + $(".endDate").val();
            var carabayar = $("#filter_carabayar option:selected").val();
            var penjamin = $("#filter_penjamin option:selected").val();

            _url = encodeURI(baseUrl+"kasir/lap-cara-bayar/show-popup?tgl_pembayaran="+ tgl_pembayaran 
            + "&carabayar=" + carabayar
            + "&penjamin=" + penjamin
            )

            $(this).attr("data-url",_url);
        });

        // Generate Table
        table = $("#example").docoTabel({
            filter: true,
            sorting: [[2, "desc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"'.(Yii::$app->controller->module->id).'/lap-cara-bayar/get-data",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "Tanggal pembayaran")).'", data: "tgl_pembayaran"},
                {title: "'.(\Yii::t("fe", "No pembayaran")).'",  data: "no_pembayaran", searchable: false},
                {title: "'.(\Yii::t("fe", "No pendaftaran")).'", data: "no_pendaftaran", searchable: false},
                {title: "'.(\Yii::t("fe", "No rekam medik")).'", data: "no_rekam_medik", searchable: false},
                {title: "'.(\Yii::t("fe", "Nama pasien")).'", data: "nama_pasien", searchable: false},
                {title: "'.(\Yii::t("fe", "Cara bayar")).'", data: "carabayar_nama"},
                {title: "'.(\Yii::t("fe", "Penjamin")).'", data: "penjamin_nama"},
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, 
            [
                [
                    1, 
                    \'<div class="input-group"><input type="text" value="'.date('d-M-Y').'" id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" value="'.date('d-M-Y').'" id="rangeDemoFinish" readonly="readonly" class="form-control endDate"/><input type="text" style="display:none" class="targetDate"></div>\'
                ], [
                    6, 
                    \''.(preg_replace("/[\n\t\r]/i", '', 
                        Html::dropDownList('carabayar_nama', '', 
                            ArrayHelper::map($caraBayar, 'carabayar_nama', 'carabayar_nama'), 
                            [
                                'id' => 'filter_carabayar', 
                                'class' => 'form-control select2', 
                                'prompt' => \Yii::t('fe', 'Cara bayar')
                            ]
                        )
                    )).'\'
                ], [
                    7, 
                    \''.(preg_replace("/[\n\t\r]/i", '', 
                        DepDrop::widget([
                            'name' => 'penjamin_nama',
                            'options' => [
                                'id' => 'filter_penjamin',
                                'disabled' => false,
                                'class' => 'form-control select2'
                            ],
                            'pluginOptions' => [
                               'depends'  => ['filter_carabayar'],
                               '-placeholder' => \Yii::t('fe', 'Penjamin'),
                               'url' => Url::home().(Yii::$app->controller->module->id).'/end-point/get-penjamin'
                            ]
                        ])
                    )).'\'
                ]
            ]
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
    });
    
    $(document).on("click", ".data-excel", function(e){
        e.preventDefault();
        window.open(baseUrl+"'.(Yii::$app->controller->module->id).'/lap-cara-bayar/export-excel?"+$.param(table.ajax.params()));
        return false;
    });
', View::POS_END, 'b-index');
?>
