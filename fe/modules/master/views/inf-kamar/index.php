<?php

/**
 * @author Randy Vianda Putra
 * @todo View Master Kamar
 * @copyright 21 Mei 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\modules\master\models\DisplayAntrianForm;
use Doco\master\controllers\DisplayAntrianController;
use yii\widgets\Breadcrumbs;
use app\components\DocoTableHelper;


$this->title = \Yii::t('fe', 'Kamar');
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
                                'data-target' => '/master/kamar/create',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'id' => 'data-edit',
                                'data-target' => '/master/kamar/update?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                            'id' => 'data-delete',
                            'data-additional' => 'data-rm'
                            ]
                        ],
                        'pdf',
                        'excel',
                    ],'#table-kamar');
                ?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table 
                    class="table datatable-basic table-striped table-hover dataTable no-footer"
                    id="table-kamar"
                    style="width:100%"
                    data-source="<?=Url::home();?>master/kamar/get-data"
                    data-filter=".form-filter"
                    data-test="true"
                >
                    <thead>
                        <tr class="bg-inverse">
                            <th width="5%"></th>
                            <th width="5%">No</th>
                            <th><?=Yii::t('fe', 'Ruangan'); ?></th>
                            <th><?=Yii::t('fe', 'Kelas pelayanan'); ?></th>
                            <th><?=Yii::t('fe', 'Jenis kasus penyakit'); ?></th>
                            <th><?=Yii::t('fe', 'Nama kamar'); ?></th>
                            <th><?=Yii::t('fe', 'Jenis kamar'); ?></th>
                            <th><?=Yii::t('fe', 'Status'); ?></th>
                            <th></th>
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
        table = $('#table-kamar').docoTabel({
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
            sorting: [[2, 'asc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            // scrollX: true,
            // fixedColumns: {
            //     leftColumns: 1
            // },
            ajax: baseUrl+'master/kamar/get-data',
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
                {title: '".(\Yii::t('fe', 'Ruangan'))."', data: 'ruangan_nama', name: 'ruangan_id'},
                {title: '".(\Yii::t('fe', 'Kelas pelayanan'))."', data: 'kelaspelayanan_nama'},
                {title: '".(\Yii::t('fe', 'Jenis kasus penyakit'))."', data: 'jeniskasuspenyakit_nama', name: 'jeniskasuspenyakit_id'},
                {title: '".(\Yii::t('fe', 'Nama kamar'))."',  data: 'kamarruangan_nokamar'},
                {title: '".(\Yii::t('fe', 'Jenis kamar'))."',  data: 'jenis_kamar'},
                {title: '".(\Yii::t('fe', 'Status'))."',  data: 'is_active'},
                {title: '".(\Yii::t('fe', 'Status'))."',  data: 'status_isi', searchable:false, visible:false},
            ],
            rowCallback: function(row, data, index){
                // if(data['is_active'] == 'Aktif'){
                //     $(row).find('td:eq(7)').css('color', 'white');
                //     $(row).find('td:eq(7)').css('background-color', '#26A65B');
                // }else{
                //     $(row).find('td:eq(7)').css('color', 'white');
                //     $(row).find('td:eq(7)').css('background-color', '#D24D57');
                // }
            },
        });
        $('.dataTables_filter').hide();
        $('.filter-form').datatableBootstrapFilter(table,
            [
                [
                    2,
                    \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                        Html::dropDownList('ruangan_nama', '', $data_ruangan,
                                [
                                    'class' => 'select2 ruangan_nama',
                                    'prompt' => \Yii::t('fe', '--Pilih--'),
                                    'id' => 'ruangan',
                                    // 'col-index'=>3
                                ]
                            )
                        )
                    )."<div>\"
                ],
                [
                    3,
                    \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                        Html::dropDownList('kelaspelayanan_id', '', $data_pelayanan,
                                [
                                    'class' => 'form-control select2 kelaspelayanan_nama',
                                    'prompt' => \Yii::t('fe', '--Pilih--'),
                                    'id' => 'kelaspelayanan_id',
                                    'col-index'=>3
                                ]
                            )
                        )
                    )."<div>\"
                ],
                [
                    4,
                    \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                        Html::dropDownList('jeniskasus_id', '', $data_kasus_penyakit,
                                [
                                    'class' => 'form-control select2 jeniskasuspenyakit_nama',
                                    'prompt' => \Yii::t('fe', '--Pilih--'),
                                    'id' => 'jeniskasus_id',
                                    'col-index'=>3
                                ]
                            )
                        )
                    )."<div>\"
                ],
                [
                    5,
                    \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'', 
                        Html::textInput('kamarruangan_nokamar', '', 
                                [
                                    'class' => 'form-control jenis_kamar', 
                                    'prompt' => 'Nama Kamar', 
                                    'col-index'=>3,
                                    'placeholder'=> 'Nama Kamar'
                                ]
                            )
                        )
                    )."<div>\"
                ],

                [
                    6,
                        \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                            Html::dropDownList('jenis_kamar', '', $data_jenis_kamar,
                                    [
                                        'class' => 'form-control select2',
                                        'prompt' => Yii::t('fe', '-- Pilih --'),
                                    ]
                                )
                                )
                    )."<div>\"
                ],
                [
                    7,
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

        // $('.ruangan_nama').select2({
        //     placeholder: '',
        //     minimumInputLength: 3,
        //     ajax: {
        //         url: '/master/kamar/get-nama-ruangan',
        //         dataType: 'json',
        //         quietMillis: 250,
        //         data: function(term, page){
        //             return{
        //                 q: term,
        //                 page: page
        //             }
        //         },
        //         processResults: function (data) {
        //           return {
        //             results: data.result
        //           };
        //         }
        //     },
        //     dropdownCssClass: 'bigdrop',
        //     escapeMarkup: function (m) { return m; },
        // });

        // $('.nama_kamar').select2({
        //     placeholder: '',
        //     minimumInputLength: 3,
        //     ajax: {
        //         url: '/master/kamar/get-kamar',
        //         dataType: 'json',
        //         quietMillis: 250,
        //         data: function(term, page){
        //             return{
        //                 q: term,
        //                 page: page
        //             }
        //         },
        //         processResults: function (data) {
        //           return {
        //             results: data.result
        //           };
        //         }
        //     },
        //     dropdownCssClass: 'bigdrop',
        //     escapeMarkup: function (m) { return m; },
        // });
        
        // $('.data-reset').on('click', function () {
        //     $('#kelaspelayanan_id' ).prop( 'disabled', true );
        //     $('#jeniskasus_id' ).prop( 'disabled', true );
        //     // $('#ruangan').val('').trigger('change').trigger('depdrop:change');
        // });

        table.on( 'select', function ( e, dt, type, indexes ) {
            if ( type === 'row' ) {
                var data = table.rows( indexes ).data()[0].status_isi;
                var data_kamar = table.rows( indexes ).data()[0].kamarruangan_nokamar;
                
                if(data){
                    // console.log(data);
                    // console.log(data_kamar);
                    $('#data-delete').attr('disabled', true);
                    $('#data-edit').attr('disabled', true);
                }else{
                    $.ajax({
                        type: 'GET',
                        url: '/master/kamar/cek-data-kamar?kamarruangan_nokamar='+data_kamar,
                        success: function(response){
                            // console.log(response);
                            if (response=='kosong') {
                                $('#data-delete').attr('disabled', false);
                                $('#data-edit').attr('disabled', false);
                            }else{
                                $('#data-delete').attr('disabled', true);
                                $('#data-edit').attr('disabled', true);
                            }
                        }
                    });
                }
            }
        });

    });


    ", VIEW::POS_END, 'js-kunings');
?>
