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

$this->title = \Yii::t('fe', 'Laporan 10 Besar Penyakit');
$this->params['breadcrumbs'][] = ['label' => $workspace, 'url' => ['/']];
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
                    <?=DocoHelpers::generateToolbar(['search','reset','pdf','excel']);?>
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

                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?=$this->title;?></legend>
                    <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th width="1">No</th>
                                <th><?=\Yii::t("fe", "Tanggal pemeriksaan");?></th>
                                <th><?=\Yii::t("fe", "Kode diagnosa");?></th>
                                <th><?=\Yii::t("fe", "Nama diagnosa");?></th>
                                <th><?=\Yii::t("fe", "Jumlah");?></th>
                                <th><?=\Yii::t("fe", "Instalasi akhir");?></th>
                                <th><?=\Yii::t("fe", "Ruangan akhir");?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" colspan="7"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                            </tr>
                        </tbody>
                    </table>
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
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"rm/lap-sepuluh-besar-penyakit/get-data",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "Tanggal Pemeriksaan")).'", data: "tglmorbiditas", "visible": false},
                {title: "'.(\Yii::t("fe", "Kode Diagnosa")).'", data: "diagnosa_kode", "searchable": false},
                {title: "'.(\Yii::t("fe", "Nama Diagnosa")).'", data: "diagnosa_nama"},
                {title: "'.(\Yii::t("fe", "Jumlah")).'", data: "jumlah", "searchable": false},
                {title: "'.(\Yii::t("fe", "Instalasi")).'",  data: "instalasi_nama", "visible": false},
                {title: "'.(\Yii::t("fe", "Nama Ruangan")).'", data: "ruangan_nama", "visible": false},
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table,
            [
                [
                    1,
                    \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate" value='.date('d-M-Y').' /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate" value='.date('d-M-Y').' /><input type="text" style="display:none" class="targetDate" id="targetDate" col-index="2" readonly="true"></div>\'
                ], [
                    3,
                    \'<div class="input-group">'.(preg_replace("/[\n\t\r]/i", '',
                        Select2::widget([
                            'name' => 'diagnosa_nama',
                            'options' => ['placeholder' => \Yii::t('fe', 'Nama diagnosa'), 'class' => 'diagnosa_nama'],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'minimumInputLength' => 3,
                                'language' => [
                                    'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                                ],
                                'ajax' => [
                                    'url' => Url::home().'master/end-point/get-data-diagnosa?assign_id=',
                                    'dataType' => 'json',
                                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                ],
                                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                'templateResult' => new JsExpression('function(city) { return city.text; }'),
                                'templateSelection' => new JsExpression('function (city) { return city.text; }'),
                            ],
                        ])
                    )).'<span class="input-group-addon"><span class="cursor-pointer" action="'.Url::home().'master/modal-search/modal-diagnosa" data-toggle="modal" data-target="#modal_backdrop_search"><i class="fa fa-list"></i> <i class="fa fa-search"></i></span></span></div>\'
                ], [
                    5,
                    \''.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('instalasi_nama', '',
                            ArrayHelper::map($instalasi, 'instalasi_id', 'instalasi_nama'),
                            [
                                'id' => 'filter_instalasi',
                                'class' => 'form-control select2',
                                'prompt' => '-',
                                'style'=>'height: 90px'
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
                               'placeholder' => \Yii::t('fe', 'Ruangan akhir'),
                               'url' => Url::to(['lap-sepuluh-besar-penyakit/get-ruangan'])
                            ]
                        ])
                        // Html::dropDownList('ruangan_nama', '',
                            // ArrayHelper::map($ruangan, 'ruangan_nama', 'ruangan_nama'),
                            // [
                                // 'class' => 'form-control no-select2',
                                // 'prompt' => \Yii::t('fe', 'Ruangan akhir')
                            // ]
                        // )
                    )).'\'
                ],
            ]
        );

        dateRangeHelper(".startDate", ".endDate", ".targetDate", true);
    });
', View::POS_END, 'b-index');
?>
