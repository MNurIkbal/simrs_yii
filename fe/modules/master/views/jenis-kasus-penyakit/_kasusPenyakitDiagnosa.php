<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
// use app\components\DocoHelpers;
// use app\components\DocoController;

$this->title = Yii::t('fe', 'Kasus Penyakit Diagnosa');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white" style="margin-top: 0px !important">
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
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_kasuspenyakitdiagnosa',
                                'data-url' => '/master/kasus-penyakit-diagnosa/update?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'id' => 'data-delete',
                                'data-target' => '/master/kasus-penyakit-diagnosa/delete?id=',
                                'action' => 'null_id'
                            ]
                        ],
                        'pdf' => [
                            'attributes' => [
                                'data-target' => '/master/kasus-penyakit-diagnosa/export-pdf?',
                            ]
                        ],
                        'excel' => ['attributes' => ['data-target'=>'/master/kasus-penyakit-diagnosa/export-excel?']],
                    ], '#table-kasuspenyakitdiagnosa');?>
            </div>
            <div class="panel-body">
                <div class="row">
                        <div class="col-md-12 filter-form-kasuspenyakitdiagnosa"></div>
                </div>
                <table class="table datatable-basic table-striped table-hover dataTable no-footer"
                        id="table-kasuspenyakitdiagnosa" style="width: 100%;">
                    <thead>
                        <tr class="bg-inverse">
                            <th><?= Yii::t('fe', 'No'); ?></th>
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
    var table;
    $(document).ready(function() {
        table = $('#table-kasuspenyakitdiagnosa').docoTabel({
            filter: true,
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets:   0
            }],
            select: {
                style:    'os',
                selector: 'td:first-child'
            },
            sorting: [[1, 'asc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+'master/kasus-penyakit-diagnosa/get-data',
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {title: '".(Yii::t('fe', 'Jenis Kasus Penyakit'))."',  data: 'jeniskasuspenyakit_nama'},
                {title: '".(Yii::t('fe', 'Kode Diagnosa')."',  data: 'diagnosa_kode', searchable:false},
                {title: '".(Yii::t('fe', 'Nama Diagnosa')."',  data: 'diagnosa_nama', searchable:false},
                {title: 'Status',  data: 'is_active'},
                {title: '".(Yii::t('fe', 'Kode Diagnosa')."',  data: 'diagnosa_id'},
            ]
        });
        $('.dataTables_filter').hide();
        $('.filter-form-kasuspenyakitdiagnosa').datatableBootstrapFilter(table,
                [
                ]
        );

        var primaryKey;

        $('#table-kasuspenyakitdiagnosa tbody').on('click', 'tr', function(){
            try {
                primaryKey = table.row('.selected').data().primary ? table.row('.selected').data().primary : null;
            } catch (e) {
                primaryKey = false;
            }


            if (primaryKey) {
                $('.data-edit').attr('action',$('.data-edit').data('url')+primaryKey);
                $('.data-delete').attr('action',$('.data-delete').data('target')+primaryKey);
            } else {
                $('.data-edit').removeAttr('action');
                $('.data-delete').removeAttr('action');
            }
        });
    });

    // Event Delete
    $(document).on('click', '#data-delete', function(e) {
        e.preventDefault();
        if($(this).attr('action') == 'null_id'){
            new PNotify({
                title: 'Terjadi Kesalahan',
                text: 'Tidak ada data yang dipilih',
                addclass: 'alert alert-warning alert-arrow-right alert-styled-right',
                type: 'error'
            });
        }else{
            $(this).docoForm('delete',{
                success : function (data) {
                    table.draw()
                }
            });
        }
        return false;
    });

    // Event Reload
    $(document).on('click', '.data-reset', function() {
        table.draw();
    });
    ", View::POS_END, 'b-index');
?>