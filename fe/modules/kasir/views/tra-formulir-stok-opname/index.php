<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;

$this->title = \Yii::t('fe', 'Transaksi Formulir Stok Opname');
$this->params['breadcrumbs'][] = ['label' => 'Kasir', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
                <?=Breadcrumbs::widget([
                    'homeLink' => [ 
                        'label' => Yii::t('yii', 'Home'),
                        'url' => Yii::$app->homeUrl,
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-body">
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?=Yii::t('fe', 'Pencarian');?></legend>
                    <div class="row">
                        <div class="col-md-12 filter-form"></div>
                    </div>
                </fieldset>
                <br />

                <fieldset id="formLieur" class="scheduler-border" -style="display:none;">
                    <?php $form = ActiveForm::begin([
                        'id' => 'ajax-form',
                        'action' => 'tra-formulir-stok-opname/ajax-form',
                        'options' => ['class' => 'form-horizontal'],
                    ]) ?>
                    <legend class="scheduler-border"><?=$this->title;?></legend>
                    <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th width="1">No</th>
                                <th><?=\Yii::t("fe", "Periode Stok");?></th>
                                <th><?=\Yii::t("fe", "Nama obat alkes");?></th>
                                <th><?=\Yii::t("fe", "Instalasi akhir");?></th>
                                <th><?=\Yii::t("fe", "Ruangan akhir");?></th>
                                <th><?=\Yii::t("fe", "Stok Sistem");?></th>
                                <th><?=\Yii::t("fe", "Stok Fisik");?></th>
                                <th><?=\Yii::t("fe", "Kondisi");?></th>
                                <th><?=\Yii::t("fe", "Tanggal Kadaluarsa");?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" colspan="7"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                            </tr>
                        </tbody>
                    </table>

                    <?=Html::submitButton('<i class="fa fa-floppy-o"></i> '.\Yii::t('fe', 'Simpan').'', [
                        'class' => 'btn btn-teal btn-sm',
                    ]);?>
                    <?=Html::button('<i class="fa fa-print"></i> '.\Yii::t('fe', 'Cetak').'', [
                        'class' => 'btn btn-crimson btn-sm',
                    ]);?>
                    <?=Html::button('<i class="fa fa-list"></i> '.\Yii::t('fe', 'Petunjuk').'', [
                        'class' => 'btn btn-default btn-sm',
                    ]);?>
                    <?php ActiveForm::end(); ?>
                </fieldset>
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
            sorting: [[1, "asc"]], 
            displayLength: 100,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"'.(Yii::$app->controller->module->id).'/tra-formulir-stok-opname/get-data",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "Periode Stok")).'", data: "periodestok_nama", "visible":false},
                {title: "'.(\Yii::t("fe", "Nama obat alkes")).'", data: "obatalkes_namalain", searchable: false},
                {title: "'.(\Yii::t("fe", "Instalasi akhir")).'",  data: "instalasi_nama", "visible":false},
                {title: "'.(\Yii::t("fe", "Ruangan akhir")).'", data: "ruangan_nama", "visible":false},
                {title: "'.(\Yii::t("fe", "Stok Sistem")).'", data: "qty_tersedia", searchable: false, "class":"text-right"},
                {title: "'.(\Yii::t("fe", "Stok fisik")).'", data: "form_stok_fisik", searchable: false},
                {title: "'.(\Yii::t("fe", "Kondisi")).'", data: "form_kondisi", searchable: false},
                {title: "'.(\Yii::t("fe", "Tanggal kadaluarsa")).'", data: "form_kadaluarsa", searchable: false},
            ],
            "initComplete": function(settings, json) {
                $(".daterange-single").daterangepicker({ 
                    singleDatePicker: true,
                    applyClass: "bg-slate-600",
                    cancelClass: "btn-default",
                    locale: {
                        format: "DD-MMMM-YYYY"
                    }
                });
            }
        });

        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, 
            [
                [
                    1, 
                    \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate"/><input type="text" style="display:none" class="targetDate"></div>\'
                ], [
                    3, 
                    \''.(preg_replace("/[\n\t\r]/i", '', 
                        Html::dropDownList('instalasi_nama', '', 
                            ArrayHelper::map($instalasi, 'instalasi_nama', 'instalasi_nama'), 
                            [
                                'id' => 'filter_instalasi', 
                                'class' => 'form-control no-select2', 
                                'prompt' => \Yii::t('fe', 'Instalasi akhir')
                            ]
                        )
                    )).'\'
                ], [
                    4, 
                    \''.(preg_replace("/[\n\t\r]/i", '', 
                        DepDrop::widget([
                            'name' => 'ruangan_nama',
                            'options' => [
                                'disabled' => false,
                                'class' => 'form-control no-select2'
                            ],
                            'pluginOptions' => [
                               'depends'  => ['filter_instalasi'],
                               '-placeholder' => \Yii::t('fe', 'Ruangan akhir'),
                               'url' => Url::home().(Yii::$app->controller->module->id).'/end-point/get-ruangan'
                            ]
                        ])
                    )).'\'
                ]
            ]
        );

        $(".daterange-basic").daterangepicker({
            startDate: "'.(date("01-M-Y")).'", autoUpdateInput: true,
            endDate: "'.(date("d-M-Y")).'",
            applyClass: "bg-slate-600",
            cancelClass: "btn-default",
            locale: {
                format: "DD-MMMM-YYYY"
            }
        });
        
        // $(document).on("click", ".filter-form .advancedFilterDo", function(e){
            // $("#formLieur").show();
        // });
        
        // $(document).on("click", ".filter-form .-advancedFilterHide", function(e){
            // $("#formLieur").hide();
        // });
    });
', View::POS_END, 'b-index');
?>
