<?php
/*
* @Author: Sunarko / Master Kondisi Keluar
* @Date:   2018-07-30 11:22:24
* @Last Modified by:  
* @Last Modified time: 
*/

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = \Yii::t('fe', 'Kondisi Keluar');
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
                                'action' => '/master/kondisi-keluar/create',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'data-options' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'data-url' => '/master/kondisi-keluar/update?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'data-additional' => 'data-rm',
                                'id' => 'data-delete',
                                'data-target' => '/master/kondisi-keluar/delete?id=',
                            ]
                        ],
                        'pdf' => [
                            'attributes' => [
                                'data-target' => '/master/kondisi-keluar/export-pdf?',
                            ]
                        ],
                        'excel' => [
                            'attributes' => [
                                'data-target'=>'/master/kondisi-keluar/export-excel?'
                            ]
                        ],
                    ],'#table-carakeluar');
                ?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table 
                    class="table datatable-basic table-striped table-hover dataTable no-footer"
                    id="table-carakeluar"
                    style="width:100%"
                    data-source="<?=Url::home();?>master/kondisi-keluar/get-data"
                    data-filter=".form-filter"
                    data-test="true"
                >
                    <thead>
                        <tr class="bg-inverse">
                            <th width="5%"></th>
                            <th width="5%">No</th>
                            <th><?=Yii::t('fe', 'Kode'); ?></th>
                            <th><?=Yii::t('fe', 'Cara Keluar'); ?></th>
                            <th><?=Yii::t('fe', 'Kondisi Keluar'); ?></th>
                            <th><?=Yii::t('fe', 'Nama Lainnya'); ?></th>
                            <th><?=Yii::t('fe', 'Status'); ?></th>
                            <th><?=Yii::t('fe', 'Catatan'); ?></th>
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
        table = $('#table-carakeluar').docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets: 0
            }],
            select: {
                style: 'os',
                selector: 'td:first-child'
            },
            sorting: [ [2,'asc'] ],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+'master/kondisi-keluar/get-data',
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
                {title: '".(\Yii::t('fe', 'Kode'))."', data: 'kondisikeluar_kode'},
                {title: '".(\Yii::t('fe', 'Cara Keluar'))."',  data: 'carakeluar_nama'},
                {title: '".(\Yii::t('fe', 'Kondisi Keluar'))."',  data: 'kondisikeluar_nama'},
                {title: '".(\Yii::t('fe', 'Nama Lainnya'))."', data: 'kondisikeluar_namalain'},
                {title: '".(\Yii::t('fe', 'Status'))."', data: 'is_active', searchable:false},
                {title: '".(\Yii::t('fe', 'Catatan'))."', data: 'catatan'},
            ],
            rowCallback: function(row, data, index){
                if(data['is_active'] == 'Aktif'){
                    $(row).find('td:eq(6)').css('color', 'white');
                    $(row).find('td:eq(6)').css('background-color', '#26A65B');
                }else{
                    $(row).find('td:eq(6)').css('color', 'white');
                    $(row).find('td:eq(6)').css('background-color', '#D24D57');
                }
            },
        });
        $('.dataTables_filter').hide();
        $('.filter-form').datatableBootstrapFilter(table,
            [
                [
                    2,
                    \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                        Html::dropDownList('kondisikeluar_kode', '', array(),
                                [
                                    'class' => 'form-control select2 kondisikeluar_kode',
                                    'prompt' => '',
                                    'col-index'=>3
                                ]
                            )
                        )
                    )."<div>\"
                ],
                [
                    3,
                    \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                        Html::dropDownList('carakeluar_nama', '', array(),
                                [
                                    'class' => 'form-control select2 carakeluar_nama',
                                    'prompt' => '',
                                    'col-index'=>3
                                ]
                            )
                        )
                    )."<div>\"
                ],
                [
                    4,
                    \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                        Html::dropDownList('kondisikeluar_nama', '', array(),
                                [
                                    'class' => 'form-control select2 kondisikeluar_nama',
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
                        Html::dropDownList('kondisikeluar_namalain', '', array(),
                                [
                                    'class' => 'form-control select2 kondisikeluar_namalain',
                                    'prompt' => '',
                                    'col-index'=>3
                                ]
                            )
                        )
                    )."<div>\"
                ],
            ],{
                2:0,
                3:1,
                4:2,
                5:3,
                7:4,
        });

        $('.kondisikeluar_kode').select2({
            placeholder: '',
            minimumInputLength: 1,
            ajax: {
                url: '/master/kondisi-keluar/get-kondisi-keluar-kode',
                dataType: 'json',
                quietMillis: 250,
                data: function(term, page){
                    return{
                        q: term,
                        page: page
                    }
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                }
            },
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function (m) { return m; },
        });

        $('.kondisikeluar_nama').select2({
            placeholder: '',
            minimumInputLength: 3,
            ajax: {
                url: '/master/kondisi-keluar/get-kondisi-keluar-nama',
                dataType: 'json',
                quietMillis: 250,
                data: function(term, page){
                    return{
                        q: term,
                        page: page
                    }
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                }
            },
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function (m) { return m; },
        });

        $('.carakeluar_nama').select2({
            placeholder: '',
            minimumInputLength: 3,
            ajax: {
                url: '/master/kondisi-keluar/get-cara-keluar-nama',
                dataType: 'json',
                quietMillis: 250,
                data: function(term, page){
                    return{
                        q: term,
                        page: page
                    }
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                }
            },
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function (m) { return m; },
        });

        $('.kondisikeluar_namalain').select2({
            placeholder: '',
            minimumInputLength: 3,
            ajax: {
                url: '/master/kondisi-keluar/get-kondisi-keluar-namalain',
                dataType: 'json',
                quietMillis: 250,
                data: function(term, page){
                    return{
                        q: term,
                        page: page
                    }
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                }
            },
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function (m) { return m; },
        });
        
    });

    ", VIEW::POS_END, 'b-index');
?>