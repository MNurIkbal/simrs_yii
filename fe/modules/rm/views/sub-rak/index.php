<?php
// Author : Ardi Pratama

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = \Yii::t('fe', 'Lokasi Sub Rak');
$this->params['breadcrumbs'][] = ['label' => 'Rm', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style type="text/css">
    .select2-selection {
        height: 36px !important;
    }
</style>
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
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-form'
                            ]
                        ],
                        'add' => [
                            'attributes' => [
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action' => '/rm/sub-rak/create',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'data-target' => '#modal_backdrop',
                                'data-url' => '/rm/sub-rak/update?id=',
                                'data-options' => 'modal'
                            ]
                        ],
                        'delete' => [
                            'attributes' => [                  
                                'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/delete?id=',
                                'data-additional' => 'data-rm'
                            ]
                        ],
                        // 'pdf',
                        // 'excel',
                    ],'#table_subrak');
                ?>
            </div>

            <div class="panel-body">
                <div class="filter-form">
                </div>
                <table id="table_subrak" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">  
                            <th width="1"></th>                                  
                            <th width="80">No</th>
                            <th><?=\Yii::t('fe', 'Rak')?></th>
                            <th><?=\Yii::t('fe', 'Sub Rak')?></th>
                            <th><?=\Yii::t('fe', 'Keterangan')?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="8"><?=\Yii::t('fe', 'Data Tidak Ditemukan')?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="modal_backdrop" class="modal fade" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        </div>
    </div>
</div>
<?php 
$this->registerJs("
    // Global Var
    var table;

    // Event Reload
    $(document).on('click', '.data-reload', function() {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        table = $('#table_subrak').docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets:   0
            }],
            select: {
                style:    'os',
                selector: 'td:first-child'
            },
            sorting: [[2, 'asc']], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+'rm/sub-rak/get-data',
            fixedColumns: {
                leftColumns: 1
            },
            oLanguage: {
                sProcessing: '".(\Yii::t('fe', 'Processing'))."',
                sLengthMenu: '".(\Yii::t('fe', 'dt_length_menu'))."',
                sZeroRecords: '".(\Yii::t('fe', 'dt_zero_records'))."',
                sEmptyTable: '".(\Yii::t('fe', 'dt_empty_table'))."',
                sInfoFiltered: '".(\Yii::t('fe', 'dt_info_filtered'))."',
                sInfoEmpty: '".(\Yii::t('fe', 'dt_info_empty'))."',
                sInfo: '".(\Yii::t('fe', 'dt_info'))."',
                oPaginate: {
                    sFirst: '".(\Yii::t('fe', 'dt_first_page'))."',
                    sPrevious: '".(\Yii::t('fe', 'dt_previous_page'))."',
                    sNext: '".(\Yii::t('fe', 'dt_next_page'))."',
                    sLast: '".(\Yii::t('fe', 'dt_last_page'))."'
                }
            },
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
                {title: '".(\Yii::t('fe', 'Rak'))."',  data: 'lokasirak_m.lokasirak_nama'},
                {title: '".(\Yii::t('fe', 'Sub Rak'))."',  data: 'subrak_nama'},
                {title: '".(\Yii::t('fe', 'Keterangan'))."',  data: 'subrak_namalainnya'},          
                {                    
                    data: 'primary',
                    searchable: false,
                    orderable: false,
                    visible: false,
                },
            ]
        });
        $('.dataTables_filter').hide();
        $('.filter-form').datatableBootstrapFilter(table, 
            [              
                [
                    2, 
                    \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'', 
                        Html::dropDownList('lokasirak_nama', '', 
                            ArrayHelper::map($lokasirak, 'lokasirak_id', 'lokasirak_nama'), 
                            [

                                'class' => 'form-control select2', 
                                'id'=>'filter_rak',
                                'prompt' => \Yii::t('fe', 'Rak')
                            ]
                        )                        
                    )))."\"
                ],                 
                [
                    3, 
                    \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'',                         
                        DepDrop::widget([
                            'name' => 'subrak_nama',
                            'options' => [
                                'disabled' => false,
                                'class' => 'form-control select2'
                            ],
                            'pluginOptions' => [
                               'depends'  => ['filter_rak'],
                               'placeholder' => '',
                               'url' => Url::to(['/rm/sub-rak/list-subrak-nama'])
                            ]
                        ])
                        )
                    )
                )."\"
                ], 
                
            ], {
                // 1:0,
                // 2:4,
                // 3:1,
                // 4:3
            }, true
        );

    });


", View::POS_END, 'b-index');
?>
