<?php

/**
 * @author Randy Vianda Putra
 * @todo View Master Shift
 * @copyright 6 September 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use yii\widgets\Breadcrumbs;
use app\components\DocoTableHelper;


$this->title = \Yii::t('fe', 'Shift');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['/master']];
$this->params['breadcrumbs'][] = $title;
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
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
                <?= DocoHelpers::generateToolbar([
                        'search',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-form'
                            ]
                        ],
                        'add' => [
                            'attributes' => [
                                'data-options' => 'link',
                                'data-target' => '/master/shift/create',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'id' => 'data-edit',
                                'data-target' => '/master/shift/update?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                            'id' => 'data-delete',
                            'data-additional' => 'data-rm'
                            ]
                        ],
                        // 'pdf',
                        'excel',
                    ],'#table-shift');
                ?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table 
                    class="table datatable-basic table-striped table-hover dataTable no-footer"
                    id="table-shift"
                    style="width:100%"
                    data-filter=".form-filter"
                    data-test="true"
                >
                    <thead>
                        <tr class="bg-inverse">
                            <th width="5%"></th>
                            <th width="5%">No</th>
                            <th><?=Yii::t('fe', 'Kode shift'); ?></th>
                            <th><?=Yii::t('fe', 'Nama shift'); ?></th>
                            <th><?=Yii::t('fe', 'Jam awal'); ?></th>
                            <th><?=Yii::t('fe', 'Jam akhir'); ?></th>
                            <th><?=Yii::t('fe', 'Status'); ?></th>
                    </thead>
                    <tbody>
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

    $(document).ready(function(){
        table = $('#table-shift').docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets: 0
            }],
            select: {
                style: 'os',
                selector: 'tr'
            },
            sorting: [[2, 'asc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            // scrollX: true,
            // fixedColumns: {
            //     leftColumns: 1
            // },
            ajax: baseUrl+'master/shift/get-data',
            columns: [
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    defaultContent: '',
                },
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {title: '".(\Yii::t('fe', 'Kode shift'))."', data: 'shift_kode', name: 'shift_kode'},
                {title: '".(\Yii::t('fe', 'Nama shift'))."', data: 'shift_nama'},
                {title: '".(\Yii::t('fe', 'Jam awal'))."', data: 'shift_jamawal'},
                {title: '".(\Yii::t('fe', 'Jam akhir'))."', data: 'shift_jamakhir'},
                {title: '".(\Yii::t('fe', 'Status'))."', data: 'is_active', searchable:true},
            ],
        });
        $('.dataTables_filter').hide();
        $('.filter-form').datatableBootstrapFilter(table,
            [
                [
                    4,
                    \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'', 
                        Html::textInput('jam_awal', '', 
                                [
                                    'class' => 'form-control pickatime', 
                                    'prompt' => '', 
                                    'col-index'=>3
                                ]
                            )
                        )
                    )."<div>\"
                ],
                [
                    5,
                    \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'', 
                        Html::textInput('jamakhir', '', 
                                [
                                    'class' => 'form-control pickatime', 
                                    'prompt' => '', 
                                    'col-index'=>3
                                ]
                            )
                        )
                    )."<div>\"
                ],
                [
                    6,
                    \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                        Html::dropDownList('status', '', $status,
                                [
                                    'class' => 'form-control select2',
                                    'prompt' => \Yii::t('fe', '--Pilih--'),
                                    'id' => 'status',
                                ]
                            )
                        )
                    )."<div>\"
                ],
            ]
        );
        $('.pickatime').pickatime({
            format: 'HH:i'
        });

    });


    ", VIEW::POS_END, 'js-kunings');
?>
