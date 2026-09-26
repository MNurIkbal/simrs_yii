<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
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


$this->title = \Yii::t('fe', 'Paket MCU');
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
                        // 'add' => [
                        //     'attributes' => [
                        //         'data-options' => 'link',
                        //         'data-target' => '/master/paket-mcu/create-mcu',
                        //     ]
                        // ],
                        // 'edit' => [
                        //     'attributes' => [
                        //         'id' => 'data-edit',
                        //         'data-target' => '/master/paket-mcu/update-mcu?id=',
                        //     ]
                        // ],
                        // 'delete' => [
                        //     'attributes' => [
                        //     'id' => 'data-delete',
                        //     'data-additional' => 'data-rm'
                        //     ]
                        // ],
                        // 'pdf',
                        // 'excel',
                    ],'#table-paket-mcu');
                ?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table 
                    class="table datatable-basic table-striped table-hover dataTable no-footer"
                    id="table-paket-mcu"
                    style="width:100%"
                    data-filter=".form-filter"
                    data-test="true"
                >
                    <thead>
                        <tr class="bg-inverse">
                            <th width="5%"></th>
                            <th width="5%">No</th>
                            <th><?=Yii::t('fe', 'Detail'); ?></th>
                            <th><?=Yii::t('fe', 'Kode Paket'); ?></th>
                            <th><?=Yii::t('fe', 'Nama Paket'); ?></th>
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
        table = $('#table-paket-mcu').docoTabel({
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
            sorting: [[3, 'asc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            // scrollX: true,
            // fixedColumns: {
            //     leftColumns: 1
            // },
            ajax: baseUrl+'master/paket-mcu/get-data',
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
                {title: '".(\Yii::t('fe', 'Detail'))."', data: 'detail', searchable:false},
                {title: '".(\Yii::t('fe', 'Kode Paket'))."', data: 'tipepaket_kode', searchable:false},
                {title: '".(\Yii::t('fe', 'Nama Paket'))."', data: 'tipepaket_nama', searchable:true},
                {title: '".(\Yii::t('fe', 'Status'))."', data: 'is_active', searchable:false},
            ],
        });
        $('.dataTables_filter').hide();
        $('.filter-form').datatableBootstrapFilter(table
        );
        $('.pickatime').pickatime({
            format: 'HH:i'
        });

    });


    ", VIEW::POS_END, 'js-kunings');
?>
