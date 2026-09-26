<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;

$this->title = \Yii::t('fe', 'Laporan Closing Kasir');
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
                      <h3 class="panel-title"><b><?= Yii::t('fe', 'Laporan Closing Kasir'); ?></b></h3>
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
                    <?=DocoHelpers::generateToolbar(['search','reset','pdf','excel']);?>
                </div>
            </div>

            <div class="panel-body">
                <div class="advanced-filter">
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Tanggal closing");?></th>
                            <th><?=\Yii::t("fe", "No closing");?></th>
                            <th><?=\Yii::t("fe", "Pegawai closing");?></th>
                            <th><?=\Yii::t("fe", "Shift");?></th>
                            <th><?=\Yii::t("fe", "Instalasi akhir");?></th>
                            <th><?=\Yii::t("fe", "Ruangan akhir");?></th>
                            <th><?=\Yii::t("fe", "Total closing");?></th>
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
        // Generate Table
        table = $("#example").docoTabel({
            filter: true,
            sorting: [[1, "desc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"'.(Yii::$app->controller->module->id).'/lap-closing-kasir/get-data",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "Tanggal closing")).'", data: "tgl_closingkasir"},
                {title: "'.(\Yii::t("fe", "No closing")).'", data: "no_struksetor", searchable: false},
                {title: "'.(\Yii::t("fe", "Pegawai closing")).'", data: "nama_pegawai"},
                {title: "'.(\Yii::t("fe", "Shift")).'", data: "shift_nama"},
                {title: "'.(\Yii::t("fe", "Instalasi akhir")).'",  data: "instalasi_nama"},
                {title: "'.(\Yii::t("fe", "Ruangan akhir")).'", data: "ruangan_nama"},
                {title: "'.(\Yii::t("fe", "Total closing (Rp.)")).'", data: "nilai_closingtransaksi", searchable: false, class: "text-right"},
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, 
            [
                [
                    1, 
                    \'<div class="input-group"><input type="text" value="'.date('d-M-Y').'" id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" value="'.date('d-M-Y').'"  id="rangeDemoFinish" class="form-control endDate" readonly="readonly"/><input type="text" style="display:none" class="targetDate"></div>\'
                ], [
                    4, 
                    \''.(preg_replace("/[\n\t\r]/i", '', 
                        Html::dropDownList('instalasi_nama', '', 
                            ArrayHelper::map($shift, 'shift_nama', 'shift_nama'), 
                            [
                                'class' => 'form-control select2', 
                                'prompt' => \Yii::t('fe', 'Shift')
                            ]
                        )
                    )).'\'
                ], [
                    5, 
                    \''.(preg_replace("/[\n\t\r]/i", '', 
                        Html::dropDownList('instalasi_nama', '', 
                            ArrayHelper::map($instalasi, 'instalasi_nama', 'instalasi_nama'), 
                            [
                                'id' => 'filter_instalasi', 
                                'class' => 'form-control select2', 
                                'prompt' => \Yii::t('fe', 'Instalasi akhir')
                            ]
                        )
                    )).'\'
                ], [
                    6, 
                    \''.(preg_replace("/[\n\t\r]/i", '', 
                        DepDrop::widget([
                            'name' => 'ruangan_nama',
                            'options' => [
                                'disabled' => false,
                                'class' => 'form-control select2'
                            ],
                            'pluginOptions' => [
                               'depends'  => ['filter_instalasi'],
                               '-placeholder' => \Yii::t('fe', 'Ruangan akhir'),
                               'url' => Url::home().(Yii::$app->controller->module->id).'/end-point/get-ruangan'
                            ]
                        ])
                    )).'\'
                ],
            ],
            {
                2:2
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
    });
    
    $(document).on("click", ".data-excel", function(e){
        e.preventDefault();
        window.open(baseUrl+"'.(Yii::$app->controller->module->id).'/lap-closing-kasir/export-excel?"+$.param(table.ajax.params()));
        return false;
    });
', View::POS_END, 'b-index');
?>
