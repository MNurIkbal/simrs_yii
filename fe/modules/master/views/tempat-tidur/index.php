<?php

/*
* @Author: Sunarko / Master Tempat Tidur
* @Date:   2018-07-23 17:16:31
* @Last Modified by:  
* @Last Modified time: 
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


$this->title = \Yii::t('fe', 'Tempat Tidur');
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
                                'data-target' => '/master/tempat-tidur/create',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                            'data-additional' => 'data-rm'
                            ]
                        ],
                        'pdf',
                        'excel',
                    ],'#table-tempat-tidur');
                ?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table 
                    class="table datatable-basic table-striped table-hover dataTable no-footer"
                    id="table-tempat-tidur"
                    style="width:100%"
                    data-source="<?=Url::home();?>master/tempat-tidur/get-data"
                    data-filter=".form-filter"
                    data-test="true"
                >
                    <thead>
                        <tr class="bg-inverse">
                            <th width="5%"></th>
                            <th width="5%">No</th>
                            <th><?=Yii::t('fe', 'Ruangan'); ?></th>
                            <th><?=Yii::t('fe', 'Nama kamar'); ?></th>
                            <th><?=Yii::t('fe', 'No Tempat Tidur'); ?></th>
                            <th width="10"><?=Yii::t('fe', 'Status'); ?></th>
                            <th></th>
                            <th width="10"><?=Yii::t('fe', 'Integrasi Aplikasi'); ?></th>
                            <th width="10"><?=Yii::t('fe', 'Setting Terisi'); ?></th>
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
        $(document).on('switchChange.bootstrapSwitch', '.change-status', function (e, state) {
            var dataStatus = '0';
            var dataId = $(this).attr('data-id');

            if (e.target.checked == true)
                dataStatus = '1';
            $(this).docoForm('delete',{
                url: baseUrl+'master/tempat-tidur/change-status?id='+dataId+'&status='+dataStatus,
                confirmTitle : '".(\Yii::t('fe', 'Konfirmasi'))."',
                confirmMessage : '".(\Yii::t('fe', 'Apa anda yakin ingin mengubah status data?'))."',
                success : function (data) {
                    table.draw();
                }
            });
            table.draw();
        });
        table = $('#table-tempat-tidur').docoTabel({
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
            sorting: [[2,'asc'], [3,'asc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+'master/tempat-tidur/get-data',
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
                {title: '".(\Yii::t('fe', 'Ruangan'))."', data: 'ruangan_nama'},
                {title: '".(\Yii::t('fe', 'Nama kamar'))."',  data: 'kamarruangan_nokamar'},
                {title: '".(\Yii::t('fe', 'No Tempat Tidur'))."', data: 'no_tempattidur', searchable:false},
                {title: '".(\Yii::t('fe', 'Status'))."',  data: 'is_active'},
                {title: '".(\Yii::t('fe', 'Kamar Tempat Tidur Id'))."', data: 'kamartempattidur_id', searchable:false, visible:false},
                {title: '".(\Yii::t('fe', 'Integrasi Aplikasi'))."',  data: 'integrasi_aplikasi',  searchable: false,},
                {title: '".(\Yii::t('fe', 'Setting Terisi'))."',  data: 'is_terisi',  searchable: false,},
            ],
            rowCallback: (rowElement, data) => {
                if(data.is_terisi == 'false') {
                    $($(rowElement).find('td')[6]).css('background-color', data.carabayar_kode_warna);
                }
            },
        });
        $('.dataTables_filter').hide();
        $('.filter-form').datatableBootstrapFilter(table,
            [
                [
                    2,
                    \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                        Html::dropDownList('ruangan_nama', '',  $data_ruangan,
                                [
                                    'class' => 'select2 ruangan_nama',
                                    'prompt' => Yii::t('fe', '-- Pilih --'),
                                    'col-index'=>3
                                ]
                            )
                        )
                    )."<div>\"
                ],
                [
                    3,
                    \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                        Html::dropDownList('kamarruangan_nokamar', '', $data_kamar,
                                [
                                    'class' => 'form-control select2 kamarruangan_nokamar',
                                    'prompt' => Yii::t('fe', '-- Pilih --'),
                                    'col-index'=>3
                                ]
                            )
                        )
                    )."<div>\"
                ],
                [
                    5,
                        \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                            Html::dropDownList('is_active', '', $status,
                                    [
                                        'class' => 'form-control select2',
                                        'prompt' => Yii::t('fe', '-- Pilih --'),
                                    ]
                                )
                                )
                    )."<div>\"
                ],
            ]
        );

        table.on( 'select', function ( e, dt, type, indexes ) {
            if ( type === 'row' ) {
                var data = table.rows( indexes ).data()[0].status_isi;
                if(data == 'Isi'){
                    $('.data-delete').attr('disabled', true);
                    $('.data-edit').attr('disabled', true);
                }else{
                    $('.data-delete').attr('disabled', false);
                    $('.data-edit').attr('disabled', false);
                }
            }
        });
        
        $(document).on('switchChange.bootstrapSwitch', '.change-status-integrasi', function (e, state) {
            var dataStatus = '0';
            $(this).attr('data-state', state);
            var dataId = $(this).attr('data-id');
            var that = $(this);
            var header = 'Konfirmasi'
            var message = 'Apa anda yakin ingin mengubah status data Integrasi ?'
            var label = {buttons: { Yes: 'button-yes', No: 'button-no'}, hidden: true};
            $.showQuestionDialog(header, message, label, function (reaction) {
                showReaction(reaction, that, function(str) {
                    hideIt();
                });
            });            
        });

        function showReaction(str, that, callback) {
            var dataId = that.attr('data-id');
            var dataState = that.attr('data-state');
            var dataStatus = '1';
            if (dataState == 'false') {
                dataStatus = '0';
            }
            //jika pilih No
            if(dataState == 'false') {
                dataState = true;
            } else {
                dataState = false;
            }
        
            if (str == 'Yes') {
                $.ajax({
                    url: baseUrl+'master/tempat-tidur/change-status-integrasi-bpjs?id='+dataId+'&status='+dataStatus,
                    type: 'POST',
                    dataType: 'json',
                    success : function(data) {
                        const message = data.response?.message == undefined ? data.response?.text : data.response?.message
                        docoNotification('success', 'Proses Berhasil !', message);
                        hideIt();
                    },
                    error : function(data) {
                        docoNotification('error', 'Proses Gagal !', data.responseJSON.meta.message);
                        that.bootstrapSwitch('state', dataState);
                        hideIt();
                    }
                });
            } else {
                that.bootstrapSwitch('state', dataState);
            }
            callback(str);
        }

        function hideIt() {
            $('#confirm-dialog-overlay').remove();
            $('#confirm-dialog').remove();
            $('#confirm-dialog-overlay').remove();
            $('#confirm-dialog').remove();            
        }

        $(document).on('switchChange.bootstrapSwitch', '.change-status-occupied', function (e, state) {
            var dataStatus = '0';
            var dataId = $(this).attr('data-id');

            if (e.target.checked == true)
                dataStatus = '1';
            $(this).docoForm('delete',{
                url: baseUrl+'master/tempat-tidur/change-status-occupied?id='+dataId+'&status='+dataStatus,
                confirmTitle : '".(\Yii::t('fe', 'Konfirmasi'))."',
                confirmMessage : '".(\Yii::t('fe', 'Apa anda yakin ingin mengubah status data occupied ?'))."',
                success : function (data) {
                    table.draw();
                },
            });
            table.draw();
        });

    });


    ", VIEW::POS_END, 'b-index');
?>
