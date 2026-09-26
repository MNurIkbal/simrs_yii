<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = Yii::t('fe', 'Jenis Kasus Penyakit Diagnosa');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white" style="margin-top: 0px !important">
            <div class="panel-heading">
              <!-- breadcrumbs replace with this -->
              <div class="row">
                  <div class="column-1">
                      <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                  </div>
                  <div class="column-2">
                      <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
                  </div>
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
                                'data-target' => '#modal_kasuspenyakitdiagnosa',
                                'action' => '/master/kasus-penyakit-diagnosa/create',
                            ]
                        ],
                         'edit' => [
                            'attributes' => [
                                'id' => 'data-edit',
                                'data-options' => 'modal',
                                'data-target' => '#modal_kasuspenyakitdiagnosa',
                                'data-url' => '/master/kasus-penyakit-diagnosa/update?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'data-additional' => 'data-rm',
                                'id' => 'btn-delete',
                                // 'data-target' => Url::home().'master/jenis-kasus-diagnosa/delete?id=',
                            ]
                        ],
                        /*'pdf' => [
                            'attributes' => [
                                'data-target' => '/master/kasus-penyakit-diagnosa/export-pdf?',
                            ]
                        ],*/
                        'excel' => ['attributes' => ['data-target'=>'/master/kasus-penyakit-diagnosa/export-excel?']],
                    ],'#penyakitdiagnosa');?>
            </div>
            <div class="panel-body">
                <div class="row">
                        <div class="col-md-12 filter-form"></div>
                </div>
                <table class="table datatable-basic table-striped table-hover dataTable no-footer"
                        id="penyakitdiagnosa" style="width: 100%;">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1"><?= Yii::t('fe', 'No'); ?></th>
                            <th><?= Yii::t('fe', 'Jenis kasus penyakit'); ?></th>
                            <th><?= Yii::t('fe', 'Kode diagnosa'); ?></th>
                            <th><?= Yii::t('fe', 'Nama diagnosa'); ?></th>
                            <th><?= Yii::t('fe', 'Status'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="modal_kasuspenyakitdiagnosa" class="modal fade" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        </div>
    </div>
</div>

<?php

$this->registerJs("
    var table_penyakitdiagnosa;
    $(document).ready(function() {
        jQuery('#btn-delete-penyakit-diagnosa').removeClass('btn-toolbar');
        
        table_penyakitdiagnosa = $('#penyakitdiagnosa').docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets:   0
            }],
            select: {
                style:    'os',
                selector: 'tr'
            },
            sorting: [[3, 'asc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            // rowsGroup: [2],
            ajax: baseUrl+'master/kasus-penyakit-diagnosa/get-data',
            columns: [
                {
                    title: '',
                    data: null,
                    defaultContent: '',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {title: '".(Yii::t('fe', 'Jenis Kasus Penyakit'))."',  data: 'jeniskasuspenyakit_nama',name: 'jeniskasuspenyakit_id'},
                {title: '".(Yii::t('fe', 'Kode Diagnosa'))."',  data: 'diagnosa_kode'},
                {title: '".(Yii::t('fe', 'Nama Diagnosa'))."',  data: 'diagnosa_nama'},
                {title: 'Status',  data: 'is_active'},
            ]
        });
        $('.dataTables_filter').hide();
        $('.filter-form').datatableBootstrapFilter(table_penyakitdiagnosa,[
            [
                2,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('jeniskasuspenyakit_nama', '', $listKasusPenyakit,
                            [
                                'id' => 'filter_jeniskasuspenyakit_nama',
                                'class' => 'form-control select2 jeniskasuspenyakit_nama',
                                'prompt' => '--Pilih--',
                                'col-index'=>3,
                                'prompt' => \Yii::t('fe', '--Pilih--'),
                            ]
                        )
                        )
                )."<div>\"
            ],
            [
                3,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'', 
                    Html::textInput('diagnosa_kode', '', 
                            [
                                'class' => 'form-control diagnosa_kode', 
                                'prompt' => 'Kode Diagnosa', 
                                'col-index'=>3,
                                'placeholder'=> 'Kode Diagnosa'
                            ]
                        )
                    )
                )."<div>\"
            ],
            [
                4,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::textInput('diagnosa_nama', '',
                        [
                            // 'id' => 'filter_diagnosa_nama',
                            'class' => 'form-control diagnosa_nama',
                            'prompt' => \Yii::t('fe', '--Pilih--'),
                            'placeholder'=> 'Nama Diagnosa'
                        ]
                    )
                )). "<div>\"
            ],
           [
                5,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('status', '',
                        $status,
                        [
                            'id' => 'filter_status',
                            'class' => 'form-control select2',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Status--'),
                        ]
                    )
                ))."<div>\"
            ]
        ],
        // {
        //     // 2:0,
        //     // 6:1,
        //     // 5:2,
        // }
        );

        // $('#filter_jeniskasuspenyakit_nama').select2({
        //     ajax: {
        //         url: '".\yii\helpers\Url::to(['/master/end-point/get-all-jenis-penyakit'])."',
        //         dataType: 'json',
        //         delay: 250,
        //         data: function (params) {
        //             return {
        //                 q: params.term, // search term
        //                 page: params.page,
        //                 is_valueWithText: 2
        //             };
        //         },
        //         cache: true
        //     },
        //     placeholder: '-- Pilih Jenis Kasus Penyakit--',
        //     escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        //     minimumInputLength: 3,
        // });

        $('#filter_diagnosa_kode').select2({
            ajax: {
                url: '".\yii\helpers\Url::to(['/master/end-point/get-all-diagnosa'])."',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page,
                        is_valueWithText: 2
                    };
                },
                cache: true
            },
            placeholder: '-- Pilih Nama Diagnosa--',
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 3,
        });

        $('#table_penyakitdiagnosa tbody').on('click', 'tr', function(){
            try {
                primaryKey = table_penyakitdiagnosa.row('.selected').data().primary ? table_penyakitdiagnosa.row('.selected').data().primary : null;
            } catch (e) {
                primaryKey = false;
            }

            if (primaryKey) {
                $('#data-edit').attr('action',$('#data-edit').data('url')+primaryKey);
                $('#data-delete').attr('action',$('#data-delete').data('url')+primaryKey);
            } else {
                $('#data-edit').removeAttr('action');
                $('#data-delete').removeAttr('action');
            }
            
        });


    });
    $('#filter_diagnosa_kode').select2();

    // Event Delete
    // $(document).on('click', '#data-delete', function(e) {
    //     e.preventDefault();
    //     if($(this).attr('action') == 'null_id'){
    //         new PNotify({
    //             title: 'Terjadi Kesalahan',
    //             text: 'Tidak ada data yang dipilih',
    //             addclass: 'alert alert-warning alert-arrow-right alert-styled-right',
    //             type: 'error'
    //         });
    //     }else{
    //         $(this).docoForm('delete',{
    //             success : function (data) {
    //                 table_penyakitdiagnosa.draw()
    //             }
    //         });
    //     }
    //     return false;
    // });

    $(document).on('click', '.data-reset', function() {
        table_penyakitdiagnosa.draw();
    });


    

    ", View::POS_END, 'b-index');
?>
