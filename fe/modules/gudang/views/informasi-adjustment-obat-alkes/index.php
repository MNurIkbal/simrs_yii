<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\DepDrop;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Gudang', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style type="text/css" media="screen">
    .more-filter{
        display: none;
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
                        <h3 class="panel-title"><b><?= $title; ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <div class="col-md-12">
                    <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
                        'lihat' => [
                            'type' => 'link',
                            'title' => \Yii::t('fe', 'Lihat'),
                            'icon' => 'fa fa-eye',
                            'method' => '#',
                            'attributes' => [
                                'class' => 'data-lihat',
                                'data-target' => Url::home().('gudang/informasi-adjustment-obat-alkes/view?id='),
                                'data-conditions' => 'no_adjusmen'
                            ]
                        ],
                        
                    ]);?>
                    <?=DocoHelpers::generateToolbar([
                        'cetak-detail' => [
                            'title' => 'Cetak PDF',
                            'icon' => 'fa fa-file-pdf-o',
                            'attributes' => [
                                'data-pages' => '_blank',
                                'data-target' => Url::home().('gudang/informasi-adjustment-obat-alkes/cetak?id='),
                                'data-conditions' => 'no_adjusmen,jenis_adjusmen'
                            ]
                        ]
                    ],'#example');?>
                </div>
            </div>
            <div class="panel-body">
                <div class="advanced-filter"></div>
                <table
                    id="example"
                    class="table table-striped table-condensed table-hover table-informasi-adjustment-obat-alkes"
                    style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "No. Adjustment");?></th>
                            <th><?=\Yii::t("fe", "Tanggal");?></th>
                            <th><?=\Yii::t("fe", "Jenis Adjustment");?></th>
                            <th><?=\Yii::t("fe", "Pegawai Input");?></th>
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
    $this->registerJs("
    var table;
    // Event Reload
    $(document).on('click', '.data-reload', function() {
        table.draw();
    });

    //menghapus checkbox table yang di clone
    $(document).ready(function(){
        $('.DTFC_Cloned').remove();
    });

    $(document).ready(function(){
        table = $('#example').docoTabel({
            filter: true,
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets: 0
            },
            {
                targets: 4,
                render: function(data, type, row) {
                    if(data == docoHelper.adjustmentMasukNama) {
                        return 'Adjustment Masuk';
                    } else if(data == docoHelper.adjustmentKeluarNama) {
                        return 'Adjustment Keluar';
                    } else {
                        return data;
                    }
                }
            }
            ],
            select: {
                style:    'os',
                selector: 'tr'
            },
            sorting: [[2, 'desc'], [3, 'desc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+'gudang/informasi-adjustment-obat-alkes/get-data',
            columns: [
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    width: '50px',
                    defaultContent: ''
                },
                {
                    width: '50px',
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {title: '".(\Yii::t('fe', 'Tanggal Adjustment'))."', data: 'tgl_adjusmen'},
                {title: '".(\Yii::t('fe', 'No. Adjustment'))."', data: 'no_adjusmen'},
                {title: '".(\Yii::t('fe', 'Jenis Adjustment'))."', data: 'jenis_adjusmen_nama'},
                {title: '".(\Yii::t('fe', 'Pegawai Input'))."', data: 'pegawai_adjusmen'}
            ],
        });
        $('.dataTables_filter').hide();

        $('.filter-form').datatableBootstrapFilter(table, [
            [
                2,
                \"<div class='input-group'><input type='text' id='rangeDemoStart' value='".date('d-M-Y')."' class='form-control startDate'/><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' value='".date('d-M-Y')."' id='rangeDemoFinish' class='form-control endDate'/><input type='text' style='display:none' class='targetDate' col-index=2></div>\"
            ],
            [
                3,
                '<div class=\"form-group\">".(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('no_adjusmen', '',
                            [],
                            [
                                'class' => 'form-control select2',
                                'id' => 'select2_no_adjustment',
                                'prompt' => \Yii::t('fe', 'No. Adjustment'),
                            ]
                        )
                    ))."</div>'
            ],
            [
                4,
                '<div class=\"form-group\">".(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('jenis_adjusmen_nama', '',
                            $jenis_adjustment,
                            [
                                'class' => 'form-control select2',
                                'prompt' => \Yii::t('fe', 'All'),
                            ]
                        )
                    ))."</div>'

            ],
        ], {2:0, 3:1, 4:2}, true);

        $('#select2_no_adjustment').select2({
            ajax: {
                url: '/gudang/informasi-adjustment-obat-alkes/search-no-adjustment',
                delay: 300,
                processResults: function(result) {
                    return {
                        results: result.list
                    }
                },
            },
            placeholder: 'No. Adjustment',
            minimumInputLength: 2
        });

        dateRangeHelper('.startDate','.endDate','.targetDate');

        $(document).on('change', '.startDate, .endDate, select[name=\"no_adjusmen\"], select[name=\"jenis_adjusmen_nama\"]', function(){
            $('.data-filter').click();
        });
    });


    ", VIEW::POS_END, 'js-kunings');
?>