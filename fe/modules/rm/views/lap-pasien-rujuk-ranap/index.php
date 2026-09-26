<?php

use yii\bootstrap\Modal;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = isset($title) ? $title : Yii::t('fe', 'Rekam Medis');
$this->params['breadcrumbs'][] = ['label' => 'Rekam Medis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row body">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                  <div class="column-1">
                    <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                </div>
                <div class="column-2">
                    <h3 class="panel-title">
                        <b>
                            <?php
                            // echo Yii::$app->docoVars->workspace("modul_alias");
                            echo $this->title;
                            ?>
                        </b>
                    </h3>
                    <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                </div>
            </div>
            <!-- end -->
        </div>

        <div class="panel-toolbar clearfix">
            <?=DocoHelpers::generateToolbar([
                'search',
                'reset' => ['attributes' => ['data-parent' => '.filter-form']],
                'excel' => [
                    'title' => Yii::t('fe', 'Unduh Excel'),
                    'attributes'=>[
                        'class' => 'btn btn-info btn-labeled btn-xs export-excel',
                  ]
                ],
                'pdf'=>[
                    'type'=>'button',
                    'title' => \Yii::t('fe', 'Cetak PDF'),
                    'icon' => 'fa fa-file-pdf-o',
                    'attributes' => [
                        'data-target'=>Url::home().'rm/lap-pasien-rujuk-ranap/export-pdf?',
                    ]
                ],
                'detail-rajal' => [
                    'title' => \Yii::t('fe', 'Detail Rawat Jalan'),
                    'icon' => 'fa fa-eye',
                    'attributes' => [
                        'data-options' => 'modal',
                        'data-target' => '#modal_backdrop',
                        'data-width' => '75%',
                        'data-url' => '/rm/lap-pasien-rujuk-ranap/detail-rajal?',
                        'id' => 'btn-detail'
                    ]
                ],
                'detail-igd' => [
                    'title' => \Yii::t('fe', 'Detail IGD'),
                    'icon' => 'fa fa-eye',
                    'attributes' => [
                        'data-options' => 'modal',
                        'data-target' => '#modal_backdrop',
                        'data-width' => '75%',
                        'data-url' => '/rm/lap-pasien-rujuk-ranap/detail-igd?',
                        'id' => 'btn-detail'
                    ]
                ],
        ], '#lap-pasien-rujuk-ranap');?>
        </div>

        <div class="panel-body">
            <div class="row">
                <!--<div class="col-md-12 filter-form"></div>-->
            </div>
            <div class="advanced-filter">
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="lap-pasien-rujuk-ranap" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th><?=\Yii::t("fe", "Tanggal Pulang");?></th>
                                <th><?=\Yii::t("fe", "Rawat Jalan");?></th>
                                <th><?=\Yii::t("fe", "Rawat Darurat");?></th>
                                <th><?=\Yii::t("fe", "Jumlah");?></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<?php

$this->registerJs("
    var table;

    $(document).ready(function() {
        $('.export-excel').on('click', function (e) { 
            e.preventDefault();

            var rowData = table.row(0).data();
            if (rowData.jumlah === 0) {
                 docoNotification(
                    'warning',
                    'Terjadi Kesalahan',
                    'Data Tidak Tersedia!'
                );
            } else {
                var url = window.location.origin;
                var target = '/rm/lap-pasien-rujuk-ranap/export-excel?';
                var datas = table.ajax.params();
                let wrapper = $('.filter-form');
                wrapper.find('.advancedFilter input').each(function () {
                    var input = $(this);
                    var index = input.attr('col-index');
                    if (datas != null) {
                    if (typeof datas.columns[index] !== 'undefined') {
                        datas.columns[index].search.search = input.val();
                    }
                    }
                });
                window.open(url + target + $.param(datas));
            }
        });

        $('.flex-1').addClass('hidden')
        // Generate Table
        table = $('#lap-pasien-rujuk-ranap').docoTabel({
            drawCallback: function(e) {
                table.row(':eq(0)').select();
            },
            filter: true,
            columnDefs: [
                { targets: '_all', className: 'text-center', orderable: false }
            ],
            displayLength: 10,
            lengthChange: false,
            paging: false,
            info: false,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+'rm/lap-pasien-rujuk-ranap/get-data',
            columns: [
                {title: '".(\Yii::t('fe', "Tanggal Pulang"))."', data: 'tglpasienpulang', visible: false, searchable: true}, //0
                {title: '".(\Yii::t('fe', "Rawat Jalan"))."', data: 'pasienrjkeri', searchable: false}, //1
                {title: '".(\Yii::t('fe', "Rawat Darurat"))."', data: 'pasienrdkeri', searchable: false}, //2
                {title: '".(\Yii::t('fe', "Jumlah"))."', data: 'jumlah', searchable: false}, //3
            ],
        });
        
        $('.dataTables_filter').hide();

        //Filter berdasarkan Tanggal Pulang
        $('.filter-form').datatableBootstrapFilter(table, 
        [
            [
                0,
                \"<div class='input-group'><input value=".date('d-M-Y')." type='text' id='rangeDemoStart' class='form-control startDate' /><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input value=".date('d-M-Y')." type='text' id='rangeDemoFinish' class='form-control endDate' /><input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'></div>\"
            ],
        ],
        {
            //posisi kolom dan grid
            0:0,
        });
        
        dateRangeHelper('.startDate','.endDate','.targetDate');

    });
",View::POS_END) ?>



